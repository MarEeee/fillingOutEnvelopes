$(document).ready(function() {   // работ с полем чекбокс. Если флаг нажат, отключаем поле email и наоборот. 
    $('#waivercheck').change(function(){        
        if(this.checked){            
            $('#joinevent').prop("disabled",false);   
            $('#joinevent').val(''); // так же здесь происходит отчистка поля email
        } else {        
            $('#joinevent').prop("disabled",true);
            $('#joinevent').val('');
        }
    });
});