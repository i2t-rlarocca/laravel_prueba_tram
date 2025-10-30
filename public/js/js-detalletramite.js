jQuery.noConflict(); 
jQuery(document).ready(function(){

  jQuery('#cuerpo').on('click', 'tr', function () {
    //remove any selected siblings 
    jQuery(this).siblings().removeClass('highlighted');
    //toggle current row
    if(!jQuery(this).hasClass('highlighted')){
        jQuery(this).toggleClass('highlighted');
        document.getElementById('id_seleccionado').value=this.id;
    }
  });
  
    jQuery('.formulario-tramite').bind('submit',function () {
       
        jQuery.ajax({
              
                    type: jQuery('.formulario-tramite').attr('method'),
                    url: jQuery('.formulario-tramite').attr('action'),
                    data: jQuery('.formulario-tramite').serialize(),
     
                    success: function (data) {
                            jQuery('#estadoI').val('');
                            var valor=jQuery('#estados').children(":selected").attr("value");
                            jQuery('#estadoI').val(valor);
                            jQuery('#formulario-historial').submit();
                        },
                    error: function(data){
                        alert("Hubo un problema al buscar los datos del trámite.");
                    }
                });
           return false;
    });

    jQuery('.formulario-historial').bind('submit',function () {
       
        jQuery.ajax({
              
                    type: jQuery('.formulario-historial').attr('method'),
                    url: jQuery('.formulario-historial').attr('action'),
                    data: jQuery('.formulario-historial').serialize(),
     
                    success: function (data) {

                         var grilla='';
                        for(var i=0; i<data['historial_e'].length; i++) {

                                grilla+='<tr  id='+data.historial_e[i]["nrotramite"]+'>';
                                grilla+='<td class=" col-lg-2" id="nrotramite">'+data.historial_e[i]["nrotramite"]+'</td>';
                                grilla+='<td class=" col-lg-2" id="fecha">'+data.historial_e[i]["fecha"]+'</td>';
                                grilla+='<td class=" col-lg-2" id="estadoI">'+data.historial_e[i]["estadoI"]+'</td>';
                                grilla+='<td class=" col-lg-2" id="estadoF">'+data.historial_e[i]["estadoF"]+'</td>';
                                grilla+='<td class=" col-lg-2" id="usuario">'+data.historial_e[i]["usuario"]+'</td>';
                                grilla+='<td class=" col-lg-2" id="observaciones">'+data.historial_e[i]["observaciones"]+'</td>';
                                grilla+='</tr>';
                            }
                          jQuery('#cuerpo').html('<tr>'+grilla);
                        }
                });
           return false;
    });

     jQuery('#estados').change(function () {
        jQuery('.formulario-tramite').submit();
    });

     jQuery('#btn-cargar-historial').on('click',function(){
          jQuery('#historial').toggle();
     });

     jQuery(function(){jQuery('#btn-cargar-historial').click(function() {
       jQuery(this).val() == "Historial Tramite" ? cambiarMensajeBtnHistorial_on() : cambiarMensajeBtnHistorial_off();
        });
    });

});//fin document.Ready


/**
* Pasa el mensaje del botón historial de show a off
**/
function cambiarMensajeBtnHistorial_on() {
    jQuery('#btn-cargar-historial').val("Ocultar Historial");
}

function cambiarMensajeBtnHistorial_off() {
    jQuery('#btn-cargar-historial').val("Historial Tramite");
}