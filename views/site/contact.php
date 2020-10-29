<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

// $this->registerJsFile('@web/index.js');
$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            Thank you for using our service!
        </div>
               
    <?php else: ?>

        <p>            
            Fill in the fields to receive your envelope template
        </p>

        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                    <?= $form->field($model, 'fromPerson')->textInput(['autofocus' => true]) ?>
                    <?= $form->field($model, 'fromPlace')?>
                    <?= $form->field($model, 'toPerson')?>
                    <?= $form->field($model, 'toPlace')?>
                    <?= $form->field($model, 'flag')->checkBox(['label'=>'Do you want to send an email template?','checked'=>false, 'id'=>'waivercheck'])?>

                    <?= $form->field($model, 'email')->textInput(['id'=>'joinevent','disabled'=>true]) ?>                   

                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                    ]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

    <?php endif; ?>
</div>
<script src='./../../web/index.js' type="module"></script>
