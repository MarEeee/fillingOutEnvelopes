<?php
namespace app\models;

use Yii;
use yii\base\Model;
use function morphos\Russian\inflectName; //падежи 
require('.\..\vendor\setasign\fpdf\fpdf.php');


/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{    
    public $email;
    public $fromPerson;
    public $fromPlace;
    public $toPerson;
    public $toPlace;
    public $flag; 
    public $verifyCode;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['fromPerson', 'fromPlace', 'toPerson', 'toPlace', 'flag'], 'required'],
            // email has to be a valid email address
            ['fromPerson', 'string', 'min'=>4, 'max'=>20],
            ['toPerson', 'string', 'min'=>4, 'max'=>20],
            ['email', 'email', 'when' =>function($model) {
                return $model->flag == 1;
            }],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }
    
    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
   

    public function contact($email)
    {        
        if ($this->validate()) {
            $token = "87b700e12ac7694540c921880c87501fc2b9ed13";
            $secret = "500760fa71bf9be61a2bfc2feb42045968993f8b";
            $dadata = new \Dadata\DadataClient($token, $secret);
            $apiObj1 = $dadata->clean("address", $this->fromPlace);
            $apiObj2 = $dadata->clean("address", $this->toPlace);

            $departurePlace = explode(",", $apiObj1['result']);  
            $destination =  explode(",", $apiObj2['result']);
            
            // $stringLng = 0;           
            // $currentString = "";
            // $xForAdress = 1340;
            // $yForAdress = 1590;

            $envelope = __DIR__ .'\images\konvert-6.jpg';
            $img = ImageCreateFromJPEG($envelope);
            $black = imagecolorallocate($img, 0x00, 0x00, 0x00);           
            $font_file = __DIR__ .'\fonts\Roboto-Black.ttf';

            function writeAdress($x,$y, $place, $img, $font_file){
                
                $stringLng = 0;           
                $currentString = "";
                foreach($place as $item){
                    $stringLng = $stringLng + strlen($item);
                    if($stringLng<70){
                        $currentString = $currentString . $item;        
                    }else{
                        imagefttext($img, 30, 0, $x, $y, $black, $font_file, $currentString);
                        $y = $y + 70;
                        $currentString = $item;
                        $stringLng = 0;
                    }    
                }
                imagefttext($img, 30, 0, $x, $y, $black, $font_file, $currentString);
            }

            // Рисуем текст 'PHP Manual' шрифтом 30го размера
            //перенос слишком длинного текста на следущую
            
            imagefttext($img, 30, 0, 520, 840, $black, $font_file, inflectName($this->fromPerson, 'родительный')); // от кого 
            writeAdress(520, 920, $departurePlace, $img, $font_file); // от куда
            // imagefttext($img, 30, 0, 520, 920, $black, $font_file, $departurePlace["result"]); 
            imagefttext($img, 30, 0, 1340, 1430, $black, $font_file,inflectName($this->toPerson, 'дательный')); // кому
            writeAdress(1340, 1590, $destination, $img, $font_file);// куда
            // imagefttext($img, 30, 0, 1340, 1590, $black, $font_file, $destination["result"]); 

            $index = (int)$apiObj2["postal_code"];
            $chars = preg_split('//', $index, -1, PREG_SPLIT_NO_EMPTY);  
            $x = 1210;           
            foreach ($chars as &$value) {    
                imagefttext($img, 30, 0, $x, 1900, $black, $font_file, $value);// Добавляем индекс в каждую ячейку - своя цифра 
                $x = $x+75; 
            }

            imagejpeg($img, 'currentEnvelope.jpg'); 
            imagedestroy($img);

            $currentEnvelope = "currentEnvelope.jpg";
            $pdf=new \FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',16);
            $pdf->Image($currentEnvelope ,60,30,90,0); 
             
            
            
            if($this->flag == 1){               
                Yii::$app->mailer->compose()
                    ->setTo($this->email)
                    ->setFrom(['pro100efim@gmail.com' => Yii::$app->params['senderName']])
                    ->setReplyTo([$this->email => $this->fromPerson])
                    ->setSubject("Ваш конверт")                                  
                    ->attachContent($pdf->Output('', 'S'), ['fileName' => 'pdfName.pdf',   'contentType' => 'application/pdf'])
                    ->send();                                
            }            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . 'enelope.pdf');
            exit($pdf->Output('', 'S'));  // работает не коорректно, не переводит на нужную страницу
            
            return true;
        }
        
        return false;
    }
   
}

