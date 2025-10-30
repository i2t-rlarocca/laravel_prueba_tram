//Función utilizada para consultar el nº de permiso en el ingreso de trámites
jQuery.noConflict(); 
jQuery(document).ready(function(){

    jQuery('#btn_consultar_permiso').on('click',function(e){
        var code = e.keyCode || e.which; //code == 13 ||
         if( e.type=='click') { //Enter keycode
            jQuery('#formulario_permiso').submit();
         }
    });


    jQuery("#formulario_permiso").validate({

         rules: {
                permiso: {required:true,number:true}
                },
          messages: {
                permiso: {required:function(){
                    jQuery('#id_permiso_nt').focus();
                    return " Ingrese un Nº de permiso "} ,number:"Sólo números"}
                },
          errorElement: "error",
    submitHandler:function(form) {
           
            jQuery.ajax({
    	           
                    type: jQuery(form).attr('method'),
                    url: jQuery(form).attr('action'),
                    dataType:'json',
                    data: jQuery(form).serialize(),
     
                    success: function (data) {
                        console.log(data);
                            if(data['usuario']==null){
                                var nro= jQuery('#id_permiso_nt').val();
                                jQuery('#mensaje').text("No existe el permiso: "+nro);
                                jQuery('#errores').show();
                                jQuery('#errores').delay(2000).fadeOut();
                                jQuery('#divagentesubage').css("display","none");
                                jQuery('#id_permiso_nt').focus();                                
                            }else{
                                jQuery('#nro_permiso').val(data['usuario']['permiso']);
                                jQuery('#agente').val(data['usuario']['agente']);
                                jQuery('#subagente').val(data['usuario']['subAgente']);
                                jQuery('#titular').val(data['usuario']['titular']);
                                jQuery('#id_agente').text("Agente:    "+data['usuario']['razonSocial']);
                                jQuery('#id_tramites').focus();

                                if(data['usuario']['estado_comercializacion']=='suspendido'){
                                    jQuery("#id_tramites option[value='10']").hide();   
                                    jQuery("#id_tramites option[value='9']").show();
                                }else{
                                    jQuery("#id_tramites option[value='9']").hide();
                                    jQuery("#id_tramites option[value='10']").show();
                                }

                                jQuery('#divagentesubage').css("display","initial");
                                jQuery('#errores').hide();
                                jQuery('#btn_volver').hide();
                            }
                        },
                        error: function(data){
    						  var nro= jQuery('#id_permiso_nt').val();
                              jQuery('#mensaje').text("No existe el permiso: "+nro);
                              jQuery('#errores').show();
                              jQuery('#errores').delay(2000).fadeOut();
                              jQuery('#divagentesubage').css("display","none");
                              jQuery('#id_permiso_nt').focus();
                        }
                });
		return false;
		}
       
    });
	
		
});//fin document ready	
	
   
function refrescar_cas()
{
     //reset de todos los elementos del formulario 
    document.getElementById("formulario_permiso").reset();
    document.getElementById('formulario_tramite').reset();
    document.getElementById("btn_consultar_permiso").style.display="initial";
    document.getElementById("btn_volver").style.display="initial";
    document.getElementById("divagentesubage").style.display="none";
    document.getElementById('formulario_permiso').id_permiso_nt.focus();
}   

