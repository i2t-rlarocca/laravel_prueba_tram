jQuery.noConflict(); 
jQuery(document).ready(function(){
	jQuery('#btn_aceptar_cancelar').on('keydown click',function(e){
        var code = e.keyCode || e.which;
        if(code == 13 || e.type == "click") { //Enter keycode
    		if(jQuery("#formulario_cancelarTramite").validate()){
    			jQuery('#formulario_cancelarTramite').submit();
    		}
         } 
    });

    /*jQuery('#btn_aceptar_cancelar').keydown(function(e){
        if(jQuery("#formulario_cancelarTramite").validate()){
            jQuery('#formulario_cancelarTramite').submit();
        }
     });*/

    //jQuery('.formulario-cancelarTramite').bind('submit',function(){
    jQuery("#formulario_cancelarTramite").validate({
	  rules: {
			ntramite: {required:true, number:true}
			},
      messages: {
			ntramite: {required:" * ", number:" Ingrese solo números"}
			},
	  errorElement: "error",
	  submitHandler: function(){
    	jQuery.ajax({
    		type:jQuery("#formulario_cancelarTramite").attr('method'),
    		url:jQuery("#formulario_cancelarTramite").attr('action'),
            dataType:'json',
    		data: jQuery("#formulario_cancelarTramite").serialize(),

    		success: function(data){
    		
    			if(data['mensaje']!=null){
                    jQuery('#mensaje').text(data['mensaje']);
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();
                    jQuery('#id_tramite').focus();
                }else{
                	jQuery('#errores').hide();
                	irCancelar();
                }
    		},
    		error: function(data){
    			var nro= jQuery('#id_tramite').val();
                jQuery('#mensaje').text("No existe el Nº de seguimiento: "+nro);
                jQuery('#errores').show();
                jQuery('#errores').delay(2000).fadeOut();
                jQuery('#id_tramite').focus();
    		}

    	});

    	return false;
    	}
    });        

});//document ready

function irCancelar(){
	url='cancelar-tramite';
	window.location = url;
}