var url_portal='portal';
function controlSession(){
   jQuery.ajax({
            type: 'GET',
            url: "control_sesion",     
            success: function (data) {
                if(data['mensaje']=='out'){ 
					url_portal=data['url'];
					clearInterval(intervalo);
					var current_location = jQuery(location).attr('pathname');
					if(jQuery("#myModal").length){
						if(jQuery("#myModal").is(':visible')){
							//jQuery('#myModal').modal('hide')
							jQuery('#sesionModal_m').modal({ keyboard: false, backdrop:'static' });
							jQuery('#sesionModal_m').modal('show');
						}else{
							jQuery('#sesionModal').modal({ keyboard: false, backdrop:'static' });
							jQuery('#sesionModal').modal('show');
						}
					}else{
						jQuery('#sesionModal').modal({ keyboard: false, backdrop:'static' });
						jQuery('#sesionModal').modal('show');
					}
                    
                        
                    
                }
                
            }
        });
	//para el modal de fin de sesión de las vistas comunes
    jQuery('#sesionModal').on('hidden.bs.modal', function(){
       cerrarSesion();
    }); 
	
	//para el modal de fin de sesión de las vistas modales
	jQuery('#sesionModal_m').on('hidden.bs.modal', function(){
       cerrarSesion();
    });
	
	//los modales
   /* jQuery('#myModal').on('hidden.bs.modal','.modal', function(){
        //jQuery(this).removeData('bs.modal');
        jQuery('#sesionModal').modal({ keyboard: false, backdrop:'static' });
        jQuery('#sesionModal').modal('show');
    });*/
}
        
//llamada a la función cada 2 segundos         
var intervalo = setInterval('controlSession();', 5000);


function cerrarSesion(){
    url=url_portal;
    window.location = url;
}