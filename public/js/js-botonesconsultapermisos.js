//TRATAMIENTO DE NUEVOS PERMISOS
jQuery.noConflict(); 
jQuery(document).ready(function(){
    
    jQuery(window).load(function(){
        jQuery().generarDatosBase(); 
        jQuery('#nro_pagina_fp').val(1);
        jQuery().grillaPermisos();       
    });

    jQuery.fn.grillaPermisos = function(){
        jQuery.ajax({
            type:'POST',
            url:'grilla-permisos',
            data:{'nro_pagina':jQuery('#nro_pagina_fp').val()},
            dataType:'json',
            success:function(data){
                    jQuery("#cuerpo tr td").remove();
                    jQuery("#pie_tabla tr td").remove();
                    if(data.permisos.permisos.length!=0){ 
                        var permisos=data.permisos.permisos;
                        var grilla='';
                        for(var i=0; i<permisos.length; i++) {
                            grilla+='<tr  id='+permisos[i]["numero"]+'>';
                            grilla+='<td class=" col-lg-2" id="numero">'+permisos[i]["numero"]+'</td>';
                            grilla+='<td class=" col-lg-2" id="fecha_inicio">'+permisos[i]["fecha_inicio"]+'</td>';
                            grilla+='<td class=" col-lg-2" id="estado">'+permisos[i]["estado"]+'</td>';
                            grilla+='<td class=" col-lg-2" id="usuario_generador">'+permisos[i]["usuario_generador"]+'</td>';
                            grilla+='</tr>';
                        }

                        var pie='<td COLSPAN=9>';//IE:7 CH=8
                        pie+= '<input type="button" id="btn-atras" name="btn-atras" value="<-" class="btn-primary" ">'
                        pie+= '<label for = "nro_pagina"> Pagina: </label>';
                        if(data.cantidadPaginas==0){
                          pie+='<input type="text" id = "nro_pagina_fp" value="0">';
                        }else{                              
                          pie+='<input type="text" id = "nro_pagina_fp" value="'+data.pagina+'">';
                        }
                        pie+='<label for = "total_paginas">de: '+data.cantidadPaginas+' </label>';
                        pie+='<input type="hidden" id = "total_paginas" value="'+data.cantidadPaginas+'">';
                        pie+='<input type="button" id="btn-adelante" name="btn-adelante" value="->" class="btn-primary" ></td></tr>'
                        jQuery('#pie_tabla').html('<tr>'+pie);
                        
                        //evento del input creado dinámicamente
                        //cuando el campo de nº de página se presiona una tecla o pierde el foco
                        jQuery('input#nro_pagina_fp').on('keydown focusout',function(e) {
                            if(e.keyCode == 13) {
                                var nro_pagina_fp = jQuery('#nro_pagina_fp').val();
                                var cantidad_paginas = jQuery('#total_paginas').val();
                                
                                if(Number(nro_pagina_fp) <= Number(cantidad_paginas) && nro_pagina_fp>0){//antes !=0
                                    jQuery('#nro_pagina').val(Number(nro_pagina_fp));
                                    jQuery().grillaPermisos();                                   
                                }else{
                                    alertify.error("No existe la página ingresada",2);
                                    jQuery('#nro_pagina_fp').val(jQuery('#nro_pagina').val());
                                    return false;
                                 }
                            }
                        });

                                             

                        //evento de las flechas creadas dinámicamente
                        jQuery('input#btn-atras').click(function(e) {
                            var nro_pagina_fp = jQuery('#nro_pagina_fp').val();
                            var nro_pagina = jQuery('#nro_pagina').val();
                            if(Number(nro_pagina_fp)<=1 || nro_pagina=='') {
                                jQuery("input#btn-atras").prop('disabled','disabled');
                            }else{
                                jQuery('#nro_pagina_fp').val(parseInt(jQuery('#nro_pagina_fp').val())-1);
                                jQuery('#nro_pagina').val(parseInt(jQuery('#nro_pagina_fp').val()));
                                jQuery().grillaPermisos();
                            }
                        });

                        jQuery('input#btn-adelante').click(function(e) {
                            var nro_pagina_fp = jQuery('#nro_pagina_fp').val();
                            var cantidad_paginas = jQuery('#total_paginas').val();
                            var nro_pagina = jQuery('#nro_pagina').val();
                            if(Number(nro_pagina_fp)>=Number(cantidad_paginas) || nro_pagina=='') {
                                jQuery("input#btn-adelante").prop('disabled','disabled');
                            }else{
                                jQuery('#nro_pagina_fp').val(parseInt(jQuery('#nro_pagina_fp').val())+1);
                                jQuery('#nro_pagina').val(parseInt(jQuery('#nro_pagina_fp').val()));
                                jQuery('#press-btn').val(1);
                                jQuery().grillaPermisos();
                            }
                        });
                        jQuery('#cuerpo').html('<tr>'+grilla);
                    }else{//no hay permisos
                        var grilla='<div><h1>NO HAY PERMISOS SIN ADJUDICAR</h1></div>';
                        jQuery('#cuerpo').html('<tr>'+grilla);
                    }
            },
            error:function(){
                alertify.error('Ocurrió un error al generar la grilla de permisos.',2);
            }
        });
    }
    

    jQuery.fn.generarDatosBase=function(){
        jQuery.ajax({
            type:'POST',
            url:'datos-base-alta-permiso',
            async:true,
            dataType:'json',
            success:function(data){
                ;
            },
            error:function(){
                alertify.error('Ocurrió un error al generar los datos Base.',2);
            }
        });
    };

    jQuery.fn.submitAltaPermisos = function(cant, resol, fecha){
        jQuery.ajax({
                type:'POST',
                url:'alta-permisos',
                dataType:'json',
                data: {'cant':cant, 'resol': resol, 'fecha': fecha},
                beforeSend: function(){
                    jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
                    jQuery('#cargandoModal').modal('show');
                },
                complete:function(){
                    jQuery('#cargandoModal').modal('hide');
                },
                success: function(data){
                    alertify.success("Se dieron de alta "+cant+" permisos.",2);
                    jQuery('#permisoModal').modal('hide');
                    jQuery().grillaPermisos();    
                    return false;
                },
                error: function(data){
                    alertify.error('Ocurrió un error al solicitar el alta de permisos.');
                }

            });
    };

    jQuery(document).on("keydown click", ".btn_alta", function(e) {
        jQuery('#permisoModal').modal({keyboard: false, backdrop:'static' });
        jQuery('#permisoModal').modal('show');
    });
    jQuery('#permisoModal').on('show.bs.modal', function(){
        jQuery('.modal-body #cant_permisos_modal').val('');
        jQuery('.modal-body #resolucion_modal').val('');
         jQuery('.modal-body #fecha_resol').val('');
    });

    jQuery('#permisoModal').on('shown.bs.modal', function(){
        jQuery('.modal-body #cant_permisos_modal').focus();
    });

    jQuery("#formulario_alta_permisos").validate({
      rules: {
            cant_permisos_modal: {required:true, number:true},
            },
      messages: {
            cant_permisos_modal: {required:"Ingrese la cantidad de permisos.", number:" Ingrese solo números"}, 
            },
      errorElement: "error",
      
    });
       
    
    jQuery('#permisoModal').find('#cargar').on('keydown click', function(e){
        var cant = jQuery('#cant_permisos_modal').val();
        var res = jQuery('#resolucion_modal').val();
        var fecha = jQuery('#fecha_resol').val();
        var valido =jQuery('#formulario_alta_permisos').validate().form();
            if(valido && cant.length>0 && res.length>0 && fecha.length>0){
                if(!alertify.confirmAltaPermiso){                          
                alertify.dialog('confirmAltaPermiso',function factory(){
                    return{
                            build:function(){
                                var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                                + '</span> Confirmación de alta de permiso';
                                this.setHeader(modifyHeader);
                            }
                        };
                    },false,'confirm');
                }

              alertify.confirmAltaPermiso('¿Está seguro que quiere solicitar el alta de ese numero de permisos?')
              .set({
                'labels':{ok:'Aceptar', cancel:'Cancelar'},
                'onok': function(){
                        var can = jQuery('#cant_permisos_modal').val();
                        var resol = jQuery('#resolucion_modal').val();
                        var fecha = jQuery('#fecha_resol').val();

                        jQuery(this).submitAltaPermisos(can, resol, fecha);},
              }).set('defaultFocus','cancel').show();
            }else{
                alertify.error("Debe completar todos los campos",2);
            }
        });   
    


    /***********************ADJUDICACIÓN DE PERMISOS***********************/
    /**Función para iluminar la fila de la tabla seleccionada**/
    
    jQuery('#cuerpo').on('click', 'tr', function () {        
        if(!jQuery(this).hasClass('highlighted')){//si no está iluminado
            jQuery(this).siblings().removeClass('highlighted');
            jQuery(this).toggleClass('highlighted');
            document.getElementById('id_seleccionado').value=this.id;
            jQuery('#btn_adjudicar_agencia').show();
            jQuery('#btn_adjudicar_subagencia').show();
        }else{
            jQuery(this).toggleClass('highlighted');
            document.getElementById('id_seleccionado').value="";                    
            jQuery('#btn_adjudicar_agencia').hide();
            jQuery('#btn_adjudicar_subagencia').hide();
        }
    });
    
    /*********************************************
    * Botón para adjudicar permisos como agencia *
    **********************************************/
    jQuery('#btn_adjudicar_agencia').on('keydown click', function(e){
        var seleccionado = jQuery('#id_seleccionado').val();

        if(seleccionado.length>0){
             if(!alertify.confirmAdjudicacionPermiso){                          
                alertify.dialog('confirmAdjudicacionPermiso',function factory(){
                    return{
                            build:function(){
                                var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                                + '</span> Confirmación de adjudicacion de permiso';
                                this.setHeader(modifyHeader);
                            }
                        };
                    },false,'confirm');
                }

              alertify.confirmAdjudicacionPermiso('¿Está seguro que quiere adjudicar el permiso?')
              .set({
                'labels':{ok:'Aceptar', cancel:'Cancelar'},
                'onok': function(){
                            jQuery().adjudicarPermiso(seleccionado, 'agencia');
                       },
              }).set('defaultFocus','cancel').show();
        }else{
            alertify.error('Seleccione un permiso para adjudicar.');
        }
    });

    /*********************************************
    * Botón para adjudicar permisos como agencia *
    **********************************************/
    jQuery('#btn_adjudicar_subagencia').on('keydown click', function(e){
        var seleccionado = jQuery('#id_seleccionado').val();

        if(seleccionado.length>0){
             if(!alertify.confirmAdjudicacionPermiso){                          
                alertify.dialog('confirmAdjudicacionPermiso',function factory(){
                    return{
                            build:function(){
                                var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                                + '</span> Confirmación de adjudicacion de permiso';
                                this.setHeader(modifyHeader);
                            }
                        };
                    },false,'confirm');
                }

              alertify.confirmAdjudicacionPermiso('¿Está seguro que quiere adjudicar el permiso?')
              .set({
                'labels':{ok:'Aceptar', cancel:'Cancelar'},
                'onok': function(){
                            jQuery().adjudicarPermiso(seleccionado, 'subagencia');
                       },
              }).set('defaultFocus','cancel').show();
        }else{
            alertify.error('Seleccione un permiso para adjudicar.');
        }
    });

    /**********************************
    * Función para adjudicar permisos *
    ***********************************/
    jQuery.fn.adjudicarPermiso = function(seleccionado, tipo_adjudicacion){
        jQuery.ajax({
            type: 'POST',
            url:'adjudicar-permiso',
            dataType:'json',
            data:{'id_seleccionado':seleccionado, 'tipo_adjudicacion':tipo_adjudicacion},
            success:function(data){
                if(data.exito){
                   alertify.success('Se han iniciado los trámites para completar la adjudicación.',3);
                   jQuery('#btn_adjudicar_agencia').hide();
                   jQuery('#btn_adjudicar_subagencia').hide();
                   jQuery().grillaPermisos();
                }else{
                    alertify.error('No se han podido inciar los trámites para completar la adjudicación.',3);
                }
            },
            error:function(){
                alertify.error('Error al intentar adjudicar un permiso.',2);
            }

        });
    }

    /**************************************
    * Botón para imprimir consulta en csv *
    ***************************************/
    jQuery('#btn_csv').on('keydown click', function(e){
        var cantidad=jQuery('#cuerpo tr').length;

        if(cantidad>1)
            permisos_csv();
        else
            alertify.error("No se puede generar un .csv de una consulta vacía.",3);
         
    });

    /**************************************
    * Botón para imprimir consulta en pdf *
    ***************************************/
    jQuery('#btn_imprimir').on('keydown click', function(e){
        var cantidad=jQuery('#cuerpo tr').length;        
         
        if(cantidad>1){
            jQuery.ajax({
                        type: 'POST',
                        url: 'informe-permisos-sin-adjudicar',
                        cache: false,
                        async:false,
                        dataType:'json',
                        success: function (data) {
                                informe_permisos_sin_adjudicar();
                            },
                        error: function(data){
                            alert("No se pudo encontrar la ruta al informe.");
                        }
                });
            

            //alertify.success("PROXIMAMENTE CONSULTA EN PDF :) .",3);
        }else{
            alertify.error("No se puede generar un .csv de una consulta vacía.",3);            
        }
         
    });



    /**************************BAJA DE PERMISO********************************/ 
    jQuery(document).on("keydown click", ".btn_baja", function(e) {
        jQuery.ajax({
            type: 'POST',
            url:'baja-permisos-ok',
            dataType:'json',
            success:function(data){
                if(data.exito){
                    vistaBajaPermiso(data.url);
                }else{
                    alertify.error('No tiene permiso de acceso a la baja de permisos',2);
                }
            },
            error:function(){
                alertify.error('Error al intentar acceder a la baja de permisos.',2);
            }

        });
    });


   
}); //fin document ready

function vistaBajaPermiso(url){
    window.location=url;
}

function permisos_csv(){
    url='grilla-permisos-csv';
    window.location = url;
}
function informe_permisos_sin_adjudicar(){
    url='informe-permisos-sin-adjudicar-pdf';
     window.open(url,'_blank');
}