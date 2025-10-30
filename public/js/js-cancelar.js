jQuery.noConflict(); 
jQuery(document).ready(function(){

    jQuery('#btn-cancelar-tramite').on('keydown click',function(e){
        var code = e.keyCode || e.which;
        if(code == 13 || e.type == "click") { //Enter keycode
            var seleccionado = jQuery('#id_seleccionado_c').val();
            var tabla_datos = jQuery('#tabla-tramites >tbody >tr').length;
            
            if(tabla_datos==0){
                alert('Debe seleccionar un trámite');
                return false;
            }else if(seleccionado==''){
                irCancelar();
            
            }else{
                if (confirm("Está seguro de que desea cancelar el trámite") == true) {
                   jQuery.ajax({
                        type: jQuery('#formulario-seleccionado-c').attr('method'),
                        url: jQuery('#formulario-seleccionado-c').attr('action'),
                        dataType:'json',
                        data: jQuery('#formulario-seleccionado-c').serialize(),
                        
         
                        success: function (data) {
                            if(data['mensaje']=='exito'){                            
                                jQuery('#formulario-tramite').submit();
                                jQuery('#mensaje_ex').text('Trámite cancelado exitosamente.');
                                jQuery('#exito').show();  
                                jQuery('#exito').delay(2000).fadeOut();  
                            }else{
                                jQuery('#mensaje').text(data['mensaje']);
                                jQuery('#errores').show();
                                jQuery('#errores').delay(2000).fadeOut();

                            }
                            
                            },
                        error: function(data){
                            jQuery('#mensaje').text("Problema al cancelar el trámite");
                            jQuery('#errores').show();
                            jQuery('#errores').delay(2000).fadeOut();
                        }
                    });
               
                } else {
                    return false;
                }
                
            }
        }
    }); 
    
});//fin document ready 

function irCancelar(){
    url='cancelar';
    window.location = url;
}