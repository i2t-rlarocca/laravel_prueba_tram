jQuery.noConflict(); 
jQuery(document).ready(function(){

    jQuery.fn.cambioObservacionesEstado = function(observaciones, nro_tramite, fecha, estaI, estaF) {
      
      jQuery.ajax({
            type:'POST',
            url:'nueva_observacion_estado',
            dataType:'json',
            data: {'observaciones':observaciones, 'nro_tramite':nro_tramite, 'fecha':fecha, 'estaI':estaI, 'estaF':estaF},
            async:false,

            success: function(data){
                jQuery(this).submitFormularioHistorial();
            }
        });
      return false;
 };
 
    jQuery.fn.submitFormularioHistorial = function() {
    /***Funciones para cargar y mostrar el historial del trámite**/
        jQuery.ajax({
                    type: jQuery('.formulario-historial').attr('method'),
                    url: jQuery('.formulario-historial').attr('action'),
                    dataType:'json',
                    data: jQuery('.formulario-historial').serialize(),
     
                    success: function (data) {
                        var grilla='';
                        for(var i=0; i<data['historial_e'].length; i++) {
                                grilla+='<tr  id='+data.historial_e[i]["nrotramite"]+'>';
                                grilla+='<td class=" col-lg-2" id="fecha">'+data.historial_e[i]["fecha"]+'</td>';
                                grilla+='<td class=" col-lg-2" id="estadoI">'+data.historial_e[i]["estadoI"]+'</td>';
                                grilla+='<td class=" col-lg-2" id="estadoF">'+data.historial_e[i]["estadoF"]+'</td>';
                                // grilla+='<td class=" col-lg-1" id="usuario">'+data.historial_e[i]["tipo_usuario"]+'</td>';
                                grilla+='<td class=" col-lg-1" id="usuario">'+data.historial_e[i]["usuario"]+'</td>';
                                grilla+='<td class=" col-lg-3" id="observaciones">'+data.historial_e[i]["observaciones"]+'</td>';
                                grilla+='<td class="hidden" id="estaI">'+data.historial_e[i]["idestado_ini"]+'</td>';
                                grilla+='<td class="hidden" id="estaF">'+data.historial_e[i]["idestado_fin"]+'</td>';
                                grilla+='<td class="hidden" id="nombre_usuario">'+data.historial_e[i]["usuario"]+'</td>';
                                grilla+='</tr>';
                            }
                          jQuery('#cuerpo').html(grilla);
                        },
                    error: function(data){
                        alertify.error("No existe historial para éste trámite.");
                    }
                });
    };

    jQuery(this).submitFormularioHistorial();

    var fecha='';
    var nrotramite=0;
    var estaI=0;
    var estaF=0;
    var usu ='';
       /**Función para iluminar el campo de la tabla seleccionada**/
      jQuery('#cuerpo').on('click', 'td', function () {
            if(!jQuery(this).hasClass('highlighted')){//si no está iluminado
                jQuery('#cuerpo tr td').removeClass('highlighted');
                jQuery(this).toggleClass('highlighted');
                var id_td=jQuery(this).attr('id');
                nrotramite = jQuery('#nro_tramite').val();
                //var fecha = jQuery(this).closest('tr').attr('id'); 
                fecha = jQuery(this).siblings().eq(0).html();
                estaI = jQuery(this).siblings().eq(4).html();
                estaF = jQuery(this).siblings().eq(5).html();
                usu = jQuery(this).siblings().eq(6).html();
                usulog = jQuery('#usu_log').val();

                if(jQuery.inArray( Number(estaI), [3, 4, 5, 6, 7, 8 ]) > -1){//solamente puede modificar los de CAS
                    if(id_td=='observaciones'){
                      if(usu==usulog){
                       
                        if(!alertify.alertaModificar){
                          
                          alertify.dialog('alertaModificar',function factory(){
                            return{
                                    build:function(){
                                        var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                                        + '</span> Modificacion Observaciones';
                                        this.setHeader(modifyHeader);
                                    }
                                };
                            },true,'confirm');
                        }
                        
                        if(!alertify.modificarO){
                          
                          alertify.dialog('modificarO',function factory(){
                            return{
                                    build:function(){
                                        var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                                        + '</span> Modificacion Observaciones';
                                        this.setHeader(modifyHeader);
                                        var body = '<div align="center">'
                                        +'<label for="motivo" style="display:table-cell">Motivo:</label>'
                                        +'<textarea class="ajs-input" maxlength=255 rows=3 cols=50 id="motivo" ></textarea>'
                                        +'</div>';
                                        this.setContent(body);
                                    }
                                };
                            },true,'prompt');
                        }

                        alertify
                            .alertaModificar('¿Desea modificar las observaciones de éste estado?')
                            .set({
                            'labels':{ok:'Modificar', cancel:'Cancelar'},
                            'onok': function(){
                                alertify.modificarO('Motivo').set({ 
                                    'labels':{ok:'Aceptar', cancel:'Cancelar'},
                                    'onok': function(){ jQuery().cambioObservacionesEstado(jQuery('#motivo').val(),nrotramite,fecha, estaI, estaF);},
                                    'oncancel': function(){ alertify.error('Observaciones no modificadas',1);}
                                    
                                  }).set('defaultFocus','cancel').show();
                              },
                            'oncancel': function(){ alertify.error('Observaciones no modificadas',1); },
                          }).set('defaultFocus','cancel').show();                        
                      }
                    }//fin observaciones 
                }
                
                  
            }else{
                jQuery(this).toggleClass('highlighted');                
            }
    }); 
          

});//fin document.Ready