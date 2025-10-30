jQuery.noConflict(); 
jQuery(document).ready(function(){
	
	let var_categoria = jQuery('#id_categoria').val();
	let nombreTipoUsuario = jQuery('#nombreTipoUsuario').val();
	
	if(nombreTipoUsuario.toUpperCase() == 'AGENTE' || nombreTipoUsuario.toUpperCase() == 'SUBAGENTE'){
		jQuery('#nro_red').hide();
		jQuery('#lnro_red').hide();
		jQuery('#nro_subagente').hide();
		jQuery('#cbu').hide();
		jQuery('#lcbu').hide();
	}
	
var errorStates = [];
    jQuery(window).bind("load", function() {
        var es_nuevo_permiso = jQuery('#es_nuevo_permiso').val();
        var categoriaAgente = jQuery('#categorias').children(":selected").attr("value");

        if(!es_nuevo_permiso && categoriaAgente == 0) {
            jQuery(this).numeroNuevaRed(jQuery('input#codigopostal').val(), 0, 1);            
        }

    });

    jQuery('#btn_adjuntar').on('keydown click', function(){
        jQuery('#div_adjuntos').show();
    });

    jQuery.validator.addMethod("motivo", function(value, element) {
     return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'-.,;:\/0-9º" "^!¡?¿@#$%&*()}{@#~]+$/i.test(value); 
    });
    jQuery.validator.addMethod("red", function(value, element) {
        var red_actual = jQuery('#id_agente').val();
        if(Number(red_actual)==value)
          return false;
        else 
          return true;
    });

    jQuery.validator.addMethod("nroSubagente", function(value, element) {
     		var cambiaAagente = jQuery('#id_categoria').children(":selected").attr("value");
            if(cambiaAagente == 1){ //1 - agente pasa a subagente 0 -subagente pasa a agente
               if(value == 0)
               		return	false;
               else
               		return true;
            }else{
               if(value == 0)
               		return	true;
               else
               		return false;
            }
    });

    jQuery.validator.addMethod("localidad", function(value, element) {
      var nroRed = jQuery("#nro_red").val();
        var val=jQuery('#nueva_localidad').val();
        if(nroRed == ""){
          jQuery(this).buscarLocalidadPorId(val, 1);
        }else{
          jQuery(this).buscarLocalidadPorId(val, 2);
        }
        var idloc = jQuery('#localidades option').filter(function() { 
                     return this.id == val;
                  });
        var val2=jQuery('#buscarLocalidad').val();
        var nombreloc = jQuery('#localidades option').filter(function() {
               return this.value == val2;
          });
        var forma = /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'.()\-0-9," "]+$/i.test(value);
         return this.optional(element) || (idloc && forma && nombreloc) ;
    });

    jQuery("#buscarLocalidad").change(function(e){
        var val=jQuery('#nueva_localidad').val();
        var nroRed = jQuery("#nro_red").val();
        if(nroRed == ""){
          jQuery(this).buscarLocalidadPorId(val, 1);
        }else{
          jQuery(this).buscarLocalidadPorId(val, 2);
        }
    });

    jQuery.fn.buscarLocalidadPorId = function(id, modalidad) {
          jQuery.ajax({
                type: 'POST',
                url:  "localidad_id",
                dataType:'json',
                data: {'id': id},
                async: false,
                success: function (data) {
                    
                      if(data['mensaje']=='true'){
                        jQuery('#nombre_localidad').val(data['nombre_localidad']);
                        jQuery('#cpscp_localidad').val(data['cpscp']);
                        var cpscp = data['cpscp'].split("-");
                        if(modalidad == 1)
                          jQuery(this).numeroNuevaRed(cpscp[0], 0, 1);

                        jQuery('#errores').hide();
                      }else{
                        jQuery('#mensaje').text(data['mensaje']);
                        jQuery('#errores').show();
                        jQuery('#errores').delay(5000).fadeOut();
                      }
                },
                error: function(data){
                  jQuery('#mensaje').text("Localidad no encontrada.");
                        jQuery('#errores').show();
                    }
            });
        return false;
     };

     /****************************************************/
     /* Validación del formulario de cambio de categoria */
     /****************************************************/
	 if(nombreTipoUsuario.toUpperCase != 'AGENTE' && nombreTipoUsuario.toUpperCase != 'SUBAGENTE'){
		 // alert('entra debería validar cbu');
			jQuery("#formulario-cambio-categoria").validate({		
			  rules: {
					nro_red: {required:true, number:true, red:true},
					// cbu:{required:true,minlength:4},
					cbu:{minlength:4},
					buscarLocalidad: {required:function(){
												  var esnuevo = jQuery('input[name="es_nuevo_permiso"]').attr("value");
												  if(esnuevo)
													  return true;
												  else
													return false;
											  }, localidad:true, maxlength:255},
					nro_subagente:{required:function(){
									  var cambiaAagente = jQuery('#id_categoria').children(":selected").attr("value");
									  if(cambiaAagente == 1){
										return true;
							}else{
										return false;
							}
								  }, number:true, nroSubagente: true},
					motivo_cc: {required:true,motivo:true},
					},
			  messages: {
					nro_red: {required:"Ingrese Nº de red.", number:"Ingrese solo números", red:"Error igual red origen."}, 
					cbu:{minlength:"Min. 4 caracteres."},
					buscarLocalidad: {required:"Debe seleccionar una localidad", localidad:"Debe seleccionar una localidad válida.", maxlength:"Ingrese hasta 255 caracteres."},
					nro_subagente: {required:"Debe indicar el nº de subagente.", number:"Ingrese solo números", nroSubagente :"Debe colocar un nº válido"}, 
					motivo_cc: {required:"Debe ingresar un motivo para el cambio.", motivo:"Números y letras"},
					},
			  errorPlacement: function(err, element) {
					
					if(jQuery.inArray(element, errorStates) == -1){
						errorStates[errorStates.length] = element;
						jQuery('#'+jQuery(element[0]).attr('id')).focus();
						 }                
					}
			 });
	 }
     
      /**
      * Función para buscar la red de la que pasará a ser subagente
      **/
     jQuery.fn.submitRed = function(nro_red, nro_subagente, modalidad, quieroSer) {
      
          jQuery.ajax({
                type:'POST',
                url:'control_red', 
                dataType:'json',
                data: {'nro_red':nro_red, 'nro_subagente':nro_subagente, 'modalidad':modalidad, 'quieroSer':quieroSer},
                async:false,
                beforeSend: function(){
                    jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
                    jQuery('#cargandoModal').modal('show');
                },
                complete:function(){
                    jQuery('#cargandoModal').modal('hide');
                    jQuery('body').removeClass('modal-open');
                    jQuery('.modal-backdrop').remove(); 
                },
                success: function(data){
                    
                    var categoria = jQuery('#id_categoria').children(":selected").attr("value");
                    if(data['mensaje']){
						
                        jQuery('#mensaje').text(data['mensaje']);
                        jQuery('#errores').show();
                        jQuery('#errores').delay(2000).fadeOut();                        
                        //  jQuery('#nro_red').val('');
              jQuery('#razon_social_nr').val('');
                        if(categoria==1)
                          jQuery('#nro_subagente').val('');
                        
                        jQuery('#nro_red').focus();

              jQuery('#div_razon_social').hide();

                    }else{
                        jQuery('#motivo_cc').focus();
                        jQuery('#razon_social_nr').val(data['razon_social']['nombre_agencia']);
                        if(categoria == 1)
                          jQuery('#nro_subagente').val(data['nro_subagente']);

                       	jQuery('#nro_subagente').focus();
                        jQuery('#div_razon_social').show();

          }          
                },
                error: function(data){
                    jQuery('#mensaje').text("No existe la red.");
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();
                    jQuery('#nro_red').focus();
                    jQuery('#cargandoModal').modal('hide');
        }

     });
          return false;
     };

     /**
      * Función para ver que el nº de subagente sea válido
      **/
     jQuery.fn.submitSubagente = function(nro_red, nro_subagente) {
      jQuery.ajax({
                type:'POST',
                url:'control_subagente', 
                dataType:'json',
                data: {'nro_red':nro_red, 'nro_subagente':nro_subagente},
                async:false,
                beforeSend: function(){
                    jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
                    jQuery('#cargandoModal').modal('show');
                },
                complete:function(){
                    jQuery('#cargandoModal').modal('hide');
                    jQuery('body').removeClass('modal-open');
                    jQuery('.modal-backdrop').remove(); 
                },
                success: function(data){
                    if(data['mensaje']){
                        jQuery('#mensaje').text(data['mensaje']);
                        jQuery('#errores').show();
                        jQuery('#errores').delay(2000).fadeOut();
                        jQuery('#nro_subagente').focus();
                        jQuery('#nro_subagente').val('');
                    }else{
                        jQuery('#motivo_cc').focus();
                }
                },
                error: function(data){
                    jQuery('#mensaje').text("Problema con el nº de subagente.");
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();
                    jQuery('#id_tramite').focus();
                    jQuery('#cargandoModal').modal('hide');
    }
    
    });
        return false;
     };

     /**
    * Para cuando es un nuevo permiso, según la localidad busco la nueva red posible
    **/

    jQuery.fn.numeroNuevaRed = function(codigopostal, nro_red, modalidad){

        jQuery.ajax({
                        type: "POST",
                        url:'numeroNuevaRed',
                        dataType:'json',
                        data: {'codigoPostal':codigopostal, 'nro_red':nro_red, 'modalidad':modalidad},
                       /* cache: false,*/
                        async:false,
                        beforeSend: function(){
        jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
        jQuery('#cargandoModal').modal('show');
                        },
                        complete:function(){
                    jQuery('#cargandoModal').modal('hide');
                    jQuery('body').removeClass('modal-open');
                    jQuery('.modal-backdrop').remove(); 
                        },
                        success: function (data) { 

                            if(data['nro_nueva_red'] != 0){
                                jQuery('#nro_red').val(data['nro_nueva_red']);                              
               }else{
                                jQuery('#nro_red').val('');
                                jQuery('#mensaje').text("Nº de red no disponible");
                                jQuery('#errores').show();
                                jQuery('#errores').delay(2000).fadeOut();
                                jQuery('#nro_red').focus();                      
               }                
                            
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown){
                         // alert(XMLHttpRequest.responseText);
                            alertify.error("No se pudo determinar el nº de la nueva red.",2);
            }
                });
        return false;
        }
  

     jQuery.fn.submitFormCategoria = function(){
        jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
        jQuery('#cargandoModal').modal('show');
        var motivo_valido = jQuery('#id_motivo_cambio_c').valid();
		let var_categoria = jQuery('#id_categoria').val();
		if(nombreTipoUsuario.toUpperCase() != 'AGENTE' && nombreTipoUsuario.toUpperCase() != 'SUBAGENTE'){
			var valido = jQuery('#formulario-cambio-categoria').validate().form();
		}
        if(motivo_valido){
			if(nombreTipoUsuario.toUpperCase() == 'AGENTE' || nombreTipoUsuario.toUpperCase() == 'SUBAGENTE'){
					   jQuery('#formulario-cambio-categoria').submit();
			}else{
					
						if(jQuery('#nro_red').length > 0){
							  var nro_red_valido = jQuery('#nro_red').valid();
							  var rz = jQuery('#razon_social_nr').val();
							  var categoria = jQuery('#id_categoria').children(":selected").attr("value");
							  var subagente = jQuery('#nro_subagente').val();

							  if((categoria ==1 && typeof rz !== 'undefined') || (categoria == 0 && subagente != '' )){
										if(nro_red_valido && valido){
										  jQuery('#btn_ingresar').prop( "disabled", true );
										  jQuery('#btn_volver').prop( "disabled", true );
										  jQuery('#btn-limpiar').prop( "disabled", true );
										  jQuery('#btn_adjuntar').prop( "disabled", true );
										  jQuery('#formulario-cambio-categoria').submit();
										}else{
											jQuery('#cargandoModal').modal('hide');
										}
								}else{
								  jQuery('#cargandoModal').modal('hide');
								}
						}else{
							jQuery('#btn_ingresar').prop( "disabled", true );
							jQuery('#btn_volver').prop( "disabled", true );
							jQuery('#btn-limpiar').prop( "disabled", true );
							jQuery('#btn_adjuntar').prop( "disabled", true );
							jQuery('#formulario-cambio-categoria').submit();
						}
			}
        }else{
          jQuery('#cargandoModal').modal('hide');
          jQuery("#id_motivo_cambio_c").focus();
        }
     } // fin   jQuery.fn.submitFormCategoria = function(){



    jQuery('#formulario-cambio-categoria').on('keydown  click','#btn_ingresar',function(e){
       var code = e.keyCode || e.which;
        if(e.type == "keydown"){
           if(code == 13) { //Enter keycode
            //chequear que el subagente tenga valor
            jQuery(this).submitFormCategoria();
          }
        }else if(e.type == "click"){
            jQuery(this).submitFormCategoria();
        }
        
        return false;
     });

   		jQuery('#nro_red').on('focusout', function(e){
        if(jQuery('#nro_red').val()!='')
   			  jQuery('#nro_red').trigger({type: 'keydown', which: 13, keyCode: 13});

   		});
     
      jQuery('#nro_red').on('keydown',function(e){

        var code = e.keyCode || e.which;
        var nro_red = jQuery('#nro_red').val();
        var subagente = jQuery('#nro_subagente').val();
        var red_actual = jQuery('#id_agente').val();
        var es_nuevo_permiso = jQuery('input[name="es_nuevo_permiso"]').attr("value");
        var categoria = jQuery('#id_categoria').children(":selected").attr("value");

        var modalidad = 0;

        if(categoria == 1){//subagente
            modalidad = 1;
          if(subagente==''){//todavía no cargó el nº de subagente
            modalidad = 0;
            subagente = 0;
          }
        }else{
          subagente = 0;
        }

        if(nro_red.length>=3 && Number(red_actual)!=Number(nro_red)){
          if((code == 13  || code==9 || e.type == "click")  && nro_red.length>=3 ) { //Enter keycode            
			// categoria define qué quiere pasar a ser 1->subagencia ; 0->agencia red
			 if(categoria == 1){ //Subagente
                jQuery(this).submitRed(nro_red, subagente, modalidad, 2);
              }else //agente
			    // alert('soy agente!'+nro_red+'-'+subagente+'-'+modalidad);
                if(es_nuevo_permiso){   
                  var cpscp = jQuery('input[name="cpscp_localidad"]').val().split("-");
                  jQuery(this).numeroNuevaRed(cpscp[0], nro_red, 2);
                }
                else{
                  jQuery(this).submitRed(nro_red, subagente, modalidad, 1);
                }
          
          }else if(nro_red.length<3 || code == 8){//code==8->borrado
              jQuery('#razon_social_nr').val('');
              jQuery('#div_razon_social').hide();
          }          
        }
     });
    
  /* jQuery('#nro_red').on("focusout",function(e){
        var nro_red = jQuery('#nro_red').val();
        var subagente = jQuery('#nro_subagente').val();
        var red_actual = jQuery('#id_agente').val();
        var es_nuevo_permiso = jQuery('input[name="es_nuevo_permiso"]').attr("value");
        var categoria = jQuery('#id_categoria').children(":selected").attr("value");

        var modalidad = 0;
        if(categoria == 1){//subagente
            modalidad = 1;
          if(subagente==''){//todavía no cargó el nº de subagente
            modalidad = 0;
            subagente = 0;
          }
        }else{
          subagente = 0;
        }

       if(nro_red.length>=3 && Number(red_actual)!=Number(nro_red)){
          if(categoria == 1) //Subagente
            jQuery(this).submitRed(nro_red, subagente, modalidad);
          else //agente
            if(es_nuevo_permiso){
              
              var cpscp = jQuery('input[name="cpscp_localidad"]').val().split("-");

              jQuery(this).numeroNuevaRed(cpscp[0], nro_red, 2);
            }
            else{
              jQuery(this).submitRed(nro_red, subagente, modalidad);
            }
          
       }else{
          jQuery('#razon_social_nr').val('');
          jQuery('#div_razon_social').hide();
       }
       
    });*/

   /* jQuery('#nro_subagente').on("focusout",function(e){
        var nro_red = jQuery('#nro_red').val();
        var subagente = jQuery('#nro_subagente').val();
        if(subagente!=''  && nro_red != ''){//todavía no cargó el nº de subagente
            jQuery(this).submitSubagente(nro_red, subagente);
        }
    });*/

    jQuery('#nro_subagente').on('focusout', function(e){
        var nroSubAg = jQuery('#nro_subagente').val();
        if(nroSubAg!='' && nroSubAg!=0)
   			  jQuery('#nro_subagente').trigger({type: 'keydown', which: 13, keyCode: 13});
   		});
    
    jQuery('#nro_subagente').on("keydown",function(e){ //click
        var code = e.keyCode || e.which;
        var nro_red = jQuery('#nro_red').val();
        var subagente = jQuery('#nro_subagente').val();
        if((code == 13 || code == 9 || e.type =="click") && nro_red != '')
              if(subagente!=''){//todavía no cargó el nº de subagente
                  jQuery(this).submitSubagente(nro_red, subagente);
              }
    });

    jQuery.fn.submitVolver = function() {
      jQuery.ajax({
                type:'POST',
                url:'borrar_carpeta_adjuntos',
                dataType:'json',
                data: {'tipo_tramite':jQuery('#nombre_tipo_tramite').val(), 'permiso':jQuery('#id_permiso').val()},

                success: function(data){
                    volver();   
                }
            });
    }
    
    jQuery('#btn_volver').on('keydown click', function(e){
        var code = e.keyCode || e.which;
        if(e.type == "keydown"){
           if(code == 13) { //Enter keycode
            jQuery(this).submitVolver();
          }
        }else if(e.type == "click"){
            jQuery(this).submitVolver();
        }
        
    });


});

 function refrescar(formulario)
{
     //reset de todos los elementos del formulario 
   document.getElementById(formulario).reset();
   document.getElementById('div_razon_social').style.display="none";
   document.getElementById('id_categoria').focus();
}

function volver(){
  url='tramites';
  window.location = url;
}

	
/*

  jQuery(window).bind("load", function() {
        var es_nuevo_permiso = jQuery('#es_nuevo_permiso').val();
        if(!es_nuevo_permiso) {
            jQuery(this).numeroNuevaRed(jQuery('input#codigopostal').val());
        }
   });


    /*jQuery.fn.nuevaRedOK = function(nro_red) {
      jQuery.ajax({
            type:'POST',
            url:'nueva_red_propuesta',
            dataType:'json',
            data: {'nro_red':nro_red},
            beforeSend: function(){
                jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
                jQuery('#cargandoModal').modal('show');
            },
            complete:function(){
                jQuery('#cargandoModal').modal('hide');
            },
            success: function(data){
                if(Number(data['mensaje'])==1){
                    jQuery('#icono_error').hide();
                    jQuery('#icono_ok').show();
                    jQuery('#error_red').remove();
                }else{
                    jQuery('#error_red').remove();
                    jQuery('#icono_ok').hide();
                    jQuery('#icono_error').show();
                    jQuery('#divSubAAg').append("<label for='error_red' id='error_red'>'El Nº de Red se encuentra ocupado, ingrese otro Nº de Red'</label>");
                }
            },
            error: function(data){
                 jQuery('#icono_error').show();
                 jQuery('#icono_ok').hide();
                 jQuery('#divSubAAg').append("<label for='error_red' id='error_red'>'Problema al verificar el nº de red.'</label>");
            }
        });

    };*/

	
		
	