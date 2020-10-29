$(document).ready(function() {
    $('#waivercheck').change(function(){        
        if(this.checked){            
            $('#joinevent').prop("disabled",false);   
            $('#joinevent').val('');
        } else {        
            $('#joinevent').prop("disabled",true);
            $('#joinevent').val('');
        }
    });
});