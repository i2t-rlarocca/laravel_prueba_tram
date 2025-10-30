jQuery.noConflict(); 
jQuery(document).ready(function(){
    jQuery(window).load(function(e){       
        jQuery(this).cargarGrilla();
    });
    
    //btn de la pantalla de consulta
    jQuery.fn.cargarGrilla=function(){
        var accion_inicial = jQuery('#accion').val();
        if(accion_inicial.indexOf('incorporar')!= -1){
            jQuery(this).incorporar();
        }else if(accion_inicial.indexOf('retirar')!= -1){
            jQuery(this).retirar();
        }else{
            alertify.error('No es una acción válida.');
        }
    }
    /**
    * Consulta para armar la grilla con las máquinas sin permiso asociado
    */
    jQuery.fn.incorporar = function(){
        jQuery.ajax({
                        type: jQuery('#formulario-maquina-seleccionada').attr('method'),
                        url: 'consulta-maquinas',
                        cache: false,
                        async:false,
                        data: {'accion':'incorporar'},//jQuery('#accion').val()},

                        success: function (data) {
                               if(data.error!=undefined){
                                    jQuery("#cuerpo tr td").remove();
                                    alertify.error('No hay datos');
                                    return false;
                               }else if(data.maquinas!=undefined){

                                jQuery("#cuerpo tr").remove();
                                    var grilla='';
                                    for(var i=0; i<data.maquinas.length; i++) {
                                            grilla+='<tr  id='+data.maquinas[i]["id"]+'>';
                                            grilla+='<td class=" col-lg-2" id="nro_maquina">'+data.maquinas[i]["nroMaquina"]+'</td>';
                                            grilla+='<td class=" col-lg-2" id="descripcion">'+data.maquinas[i]["descripcion"]+'</td>';
                                            grilla+='<td class=" col-lg-2" id="tipo_terminal">'+data.maquinas[i]["tipoTerminal"]+'</td>';
                                            grilla+='<td class=" col-lg-2" id="estado">'+data.maquinas[i]["estado"]+'</td>';
                                            grilla+='<td class=" col-lg-2" id="btn_incorporar"><button class="col-lg-2 btn-default incorporar_btn" id="incorporar"></button></td>';                             
                                            grilla+='</tr>';
                                    }
                                    jQuery('#cuerpo').html('<tr>'+grilla);
                                }


                            },
                        error: function(data){
                            alertify.error("Problema al consultar las máquinas.");
                        }
                });
    }

    /**
    * Función del botón de incorporar máquina al permiso
    **/
    jQuery('#tabla_maquinas').on('keydown click','.incorporar_btn', function(e){
        var maquina = jQuery(this).closest('tr').attr('id'); //.find('#id').html();

        jQuery.ajax({
            type:jQuery('#formulario-maquina-seleccionada').attr('method'),
            url:jQuery('#formulario-maquina-seleccionada').attr('action'),
            cache:false,
            async:true,
            data:{'maquina':maquina},
            beforeSend: function(){
                jQuery('#cargandoModal').modal('show');
            },
            complete:function(){
                jQuery('#cargandoModal').modal('hide');
            },
            success: function(data){
                if(data.mensaje){
                    jQuery(this).cargarGrilla();
                }else{
                    alertify.error(data.mensaje);
                }
            },
            error:function(){

            }

        });
        return false;
    });
    
     /**
    * Consulta para armar la grilla con las máquinas del permiso ingresado
    */
    jQuery.fn.retirar = function(){
            jQuery.ajax({
                        type: jQuery('#formulario-maquina-seleccionada').attr('method'),
                        url: 'consulta-maquinas',
                        cache: false,
                        async:false,
                        data: {'accion':'retirar'},//jQuery('#accion').val()},

                        success: function (data) {
                               if(data.error!=undefined){
                                    jQuery("#cuerpo tr").remove();
                                    alertify.error('No hay datos');
                                    return false;
                               }else if(data.maquinas!=undefined){

                                jQuery("#cuerpo tr").remove();
                                    var grilla='';
                                    for(var i=0; i<data.maquinas.length; i++) {
                                            grilla+='<tr  id='+data.maquinas[i]["id"]+'>';
                                            grilla+='<td class=" col-lg-2" id="nro_maquina">'+data.maquinas[i]["nroMaquina"]+'</td>';
                                            grilla+='<td class=" col-lg-2" id="descripcion">'+data.maquinas[i]["descripcion"]+'</td>';
                                            grilla+='<td class=" col-lg-2" id="tipo_terminal">'+data.maquinas[i]["tipoTerminal"]+'</td>';
                                            grilla+='<td class=" col-lg-2" id="estado">'+data.maquinas[i]["estado"]+'</td>';
                                            grilla+='<td class=" col-lg-2" id="btn_retirar"><button class="col-lg-2 btn-default retirar_btn" id="retirar"></button></td>';                             
                                            grilla+='</tr>';
                                    }
                                    jQuery('#cuerpo').html('<tr>'+grilla);
                                }


                            },
                        error: function(data){
                            alertify.error("Problema al consultar las máquinas.");
                        }
                });
        }

    /**
    * Función del botón de retirar máquina
    */
    jQuery('#tabla_maquinas').on('keydown click','.retirar_btn', function(e){
        var maquina = jQuery(this).closest('tr').attr('id'); 

        jQuery.ajax({
            type:jQuery('#formulario-maquina-seleccionada').attr('method'),
            url:jQuery('#formulario-maquina-seleccionada').attr('action'),
            cache:false,
            async:true,
            data:{'maquina':maquina},
            beforeSend: function(){
                jQuery('#cargandoModal').modal('show');
            },
            complete:function(){
                jQuery('#cargandoModal').modal('hide');
            },
            success: function(data){
                if(data.mensaje){
                    jQuery(this).cargarGrilla();
                }else{
                    alertify.error(data.mensaje);
                }
            },
            error:function(){

            }

        });
        return false;
    });
 

});//fin document ready	
	


