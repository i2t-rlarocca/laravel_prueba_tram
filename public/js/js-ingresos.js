jQuery.noConflict(); 
jQuery(document).ready(function(){
	jQuery('#btn_ingresar').click(function(){
		if(jQuery("#formulario-ingreso").validate()){
			jQuery('#formulario-ingreso').submit();
		}
          
    });

    jQuery('#btn_ingresar').keydown(function(e){
        var code = e.keyCode || e.which;
       
        if(code == 13) { //Enter keycode
            if(jQuery("#formulario-ingreso").validate()){
                jQuery('#formulario-ingreso').submit();
            }
        }
     });

	jQuery("#formulario-ingreso").validate({

      rules: {
        permiso: {required:true ,number: true}
        },
      messages: {
        permiso: {required:" * ", number:"Solo números"}
        },
      errorElement: "error",
     submitHandler: function(){
    	jQuery.ajax({
    		type:jQuery("#formulario-ingreso").attr('method'),
    		url:jQuery("#formulario-ingreso").attr('action'),
            dataType:'json',
    		data: jQuery("#formulario-ingreso").serialize(),

    		success: function(data){
               
               	if(data['usuario']){
                    jQuery('#errores').hide();
                    irListaTramite();                  
                }else{
                    var nro= jQuery('#id_permiso').val();
                    jQuery('#mensaje').text(data['mensaje']);
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();
                    jQuery('#id_permiso').focus();
                }	
                    
                
    		},
    		error: function(data){
    			var nro= jQuery('#id_permiso').val();
                jQuery('#mensaje').text("No existe el Nº de permiso: "+nro);
                jQuery('#errores').show();
    		}

    	});

    	return false;
    	}
    });        

});//document ready

/*function irConsultaTramite(){
    url='consulta-tramite';
    window.location = url;
}*/

function irListaTramite(){
	url='tramites';
	window.location = url;
}
