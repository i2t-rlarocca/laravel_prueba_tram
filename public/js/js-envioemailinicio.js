jQuery.noConflict(); 
jQuery(document).ready(function(){
        jQuery.fn.enviarEmailInicioExitoso = function() {
              jQuery.ajax({
                    type:'get',
                    url:'envio_mail_inicio_exitoso',
                    dataType:'json',
                    success: function(data){
                        ;
                    },
                    error: function(data){
                        jQuery('#mensaje').text("Problema al enviar email.");
                        jQuery('#errores').show();
                        jQuery('#errores').delay(2000).fadeOut();
                    }

                });

        };
        
        /**Para la primera vez**/
        jQuery(window).load(function(){
            jQuery(this).enviarEmailInicioExitoso();

        });

    });




 