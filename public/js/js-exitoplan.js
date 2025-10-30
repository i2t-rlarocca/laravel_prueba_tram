jQuery.noConflict(); 
jQuery(document).ready(function(){
    jQuery('#btn_exit').click(function(){        
        jQuery('#formulario_nroTramite').submit(); 
    }); 
});

function irDetalle(){
	jQuery('#formulario_nroTramite').submit();   
};

var intervalo = setInterval('irDetalle();', 2000);