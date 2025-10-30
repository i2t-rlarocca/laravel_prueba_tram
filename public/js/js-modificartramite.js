jQuery.noConflict();
jQuery(document).ready(function(){
		
		jQuery('#tipo_vin').on('change', function(){
			
			var tipo_rel = jQuery('#tipo_rel').val();
			if(tipo_rel==0 || tipo_rel!=1){
				jQuery('#tipo_vin').val(0);
				jQuery('#tipo_vinh').val(0);
			}
			if(tipo_rel==1){
				var tipo_vinh = jQuery('#tipo_vin').val();
				jQuery('#tipo_vinh').val(tipo_vinh);
				// jQuery('#tipo_vin').val(0);
			}
		});

    jQuery(window).load(function(){
        var tipoTramite = jQuery('input[name=tipo_tramite]').val();
        if(tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
            var val = jQuery('input[name=nueva_localidad]').val();
            jQuery(this).buscarLocalidadPorId(val);
        }
		
		if(tipoTramite == 2){
			var categoria_nueva = jQuery('#categoria_nueva').val();
			var subagente = jQuery('#nro_subagente').val();
			// 1 -> quiere ser subagencia
			if(categoria_nueva == 1){
				if(subagente == 0){
					jQuery('#mensaje').text("Modifique la subagencia");
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();
					jQuery('#nro_subagente').val('');
					jQuery('#nro_subagente').focus();
				}
			}
		}

    });

    jQuery.fn.buscarLocalidadPorId = function(id) {
        jQuery.ajax({
                type: 'POST',
                url:  "localidad_id",
                dataType:'json',
                data: {'id': id},
                async: false,
                success: function (data) {
                      if(data['mensaje']=='true'){
                        jQuery('#nombre_localidad').val(data['nombre_localidad']);
                        jQuery('#buscarLocalidad').val(data['nombre_localidad']);
                      }else{
                        jQuery('#mensaje').text(data['mensaje']);
                        jQuery('#errores').show();
                        jQuery('#errores').delay(5000).fadeOut();
                      }
                    },
                error: function(data){
                  jQuery('#mensaje').text("Localidad no encontrada.");
                  jQuery('#errores').show();
                  jQuery('#errores').delay(5000).fadeOut();
                    }
            });
    };

  jQuery.fn.buscarCorreoElectronico = function(correo, permiso) {
        jQuery.ajax({
                type: 'POST',
                url:  "correo_valido",
                dataType:'json',
                data: {'email': correo, 'permiso':permiso},
                async: false,
                success: function (data) {
                      if(data['mensaje']=="1"){
                        jQuery('#mensaje').text("El correo ya existe. Ingrese otro.");
                        jQuery('#id_email').val("");
                        jQuery('#errores').show();
                        jQuery('#errores').delay(3000).fadeOut();
                      }
                    },
                error: function(data){
                  jQuery('#mensaje').text("Correo no válido.");
                  jQuery('#errores').show();
                  jQuery('#errores').delay(3000).fadeOut();
                    }
            });
    };

  jQuery.fn.submitVolver = function() {
    window.location = jQuery('input[name=urlRetorno]').val() + '?nroTramite=' + jQuery('#nro').val();
  };

  jQuery.fn.buscarPersona=function(dni_cuit, tipo_doc){
      var sit_gan = jQuery('#situacion_ganancia');
      var sociedad= jQuery('#tipo_sociedad option');

          jQuery.ajax({
                  type:'POST',
                  url:'buscar-persona',
                  dataType:'json',
                  data: {'dni_cuit':dni_cuit, 'tipo_doc':tipo_doc},

                  success: function(data){
                    if(data.exito){

                      if(typeof data['persona'] !== "undefined"){
                       if(data['persona']!== null){

                          //datos de contacto
                          if(data['persona']['datos_contacto']!=undefined && data['persona']['datos_contacto']!="" && data['persona']['datos_contacto']!==null && jQuery('#datos_contacto :enabled')){
                            jQuery('#datos_contacto').val(data['persona']['datos_contacto']);
                            jQuery('#datos_contacto').prop('disabled', true);
                          }else{
                            jQuery('#datos_contacto').prop('disabled',false);
                          }
                          //ingresos
                          if(data['persona']['ingresos']!=undefined && data['persona']['ingresos']!="" && data['persona']['ingresos']!==null && jQuery('#ingresos :enabled')){
                            jQuery('#ingresos').val(data['persona']['ingresos']);
                            jQuery('#ingresos').prop('disabled', true);
                          }else{
                            jQuery('#ingresos').prop('disabled',false);
                          }
                          //cbu
                          if(data['persona']['cbu']!=undefined &&  data['persona']['cbu']!="" && data['persona']['cbu']!== null && jQuery('#cbu :enabled')){
                              jQuery('#cbu').val(data['persona']['cbu']);
                              jQuery('#cbu').prop('disabled', true);
                          }else{
                              jQuery('#cbu').prop('disabled',false);
                          }
                          //referente
                          if(data['persona']['referente']!=undefined &&  data['persona']['referente']!="" && data['persona']['referente']!==null && jQuery('#referente :enabled')){
                            jQuery('#referente').val(data['persona']['referente']);
                            jQuery('#referente').prop('disabled', true);
                          }else{
                            jQuery('#referente').prop('disabled',false);
                          }

                          //tipo de situación
                          if(data['persona']['tipo_situacion']!=undefined &&  data['persona']['tipo_situacion']!="" && data['persona']['tipo_situacion']!== null && jQuery('#tipo_situacion :enabled')){
                              jQuery('#tipo_situacion').val(data['persona']['tipo_situacion']);
                              jQuery('#tipo_situacion').prop('disabled', true);
                          }else{
                            jQuery('#tipo_situacion').prop('disabled',false);
                          }
                          //localidad
                          var dataList=jQuery('#localidades');
                          dataList.empty();
                          if(data['persona']['nombre_localidad']!=undefined &&  data['persona']['nombre_localidad']!="" && data['persona']['nombre_localidad']!== null && jQuery('#buscarLocalidad :enabled')){
                            jQuery('#nombre_localidad').val(data['persona']['nombre_localidad']);
                            jQuery('#buscarLocalidad').val(data['persona']['nombre_localidad']);
                            jQuery('#nueva_localidad').val(data['persona']['id_localidad']);
                            var grilla='<option value="'+data['persona']['nombre_localidad']+'" id='+data['persona']['id_localidad']+'>';
                            jQuery('#localidades').html(grilla);
                            jQuery('#buscarLocalidad').prop('disabled',true);
                          }else{
                            jQuery('#buscarLocalidad').prop('disabled',false);
                          }
                          //domicilio
                          if(data['persona']['domicilio_titular']!=undefined &&  data['persona']['domicilio_titular']!="" && data['persona']['domicilio_titular']!==null && jQuery('#domicilio :enabled')){
                            jQuery('#domicilio').val(data['persona']['domicilio_titular']);
                            jQuery('#domicilio').prop('disabled', true);
                          }else{
                            jQuery('#domicilio').prop('disabled',false);
                          }
                          //cuit
                          if(data['persona']['cuit']!=undefined &&  data['persona']['cuit']!="" && data['persona']['cuit']!==null && jQuery('#cuit :enabled')){
                            var cuit = data['persona']['cuit'];
                            cuit=cuit.slice(0,2)+'-'+cuit.slice(2,10)+'-'+cuit.slice(10,11);
                             jQuery('#cuit').val(cuit);
                          }

                          //CAMPOS ESPECÍFICOS PERSONA FÍSICA
                          if(tipo_persona=='F'){

                            //tipo de ocupación
                            if(data['persona']['tipo_ocupacion']!=undefined &&  data['persona']['tipo_ocupacion']!="" && data['persona']['tipo_ocupacion']!==null && jQuery('#tipo_ocup :enabled')){
                              jQuery('#tipo_ocup option[value='+data['persona']['tipo_ocupacion']+']').attr('selected', 'selected');
                              jQuery('#tipo_ocup').prop('disabled', true)
                            }else{
                              jQuery('#tipo_ocup').prop('disabled',false);
                              jQuery('#tipo_ocup option[value="ni"]').attr('selected', 'selected');
                            }

                            //apellido nombre
                            if(data['persona']['nombre_titular']!=undefined &&  data['persona']['nombre_titular']!="" && data['persona']['nombre_titular']!==null && jQuery('#apellido_nombre :enabled')){
                              jQuery('#apellido_nombre').val(data['persona']['nombre_titular']);
                              jQuery('#apellido_nombre').prop('disabled', true)
                            }else{
                              jQuery('#apellido_nombre').prop('disabled',false);
                            }
                            //apellido materno
                            if(data['persona']['apellido_materno']!=undefined && data['persona']['apellido_materno']!="" && data['persona']['apellido_materno']!==null && jQuery('#id_apellido_mat :enabled')){
                              jQuery('#id_apellido_mat').val(data['persona']['apellido_materno']);
                              jQuery('#id_apellido_mat').prop('disabled', true)
                            }else{
                              jQuery('#id_apellido_mat').prop('disabled',false);
                            }
                            //fecha_nacimiento
                            if(data['persona']['fecha_nacimiento']!=undefined && data['persona']['fecha_nacimiento']!="" && data['persona']['fecha_nacimiento']!==null && jQuery('#fecha_nac :enabled')){
                              jQuery('#fecha_nac').val(data['persona']['fecha_nacimiento']);
                              jQuery('#fecha_nac').prop('disabled', true)
                            }else{
                              jQuery('#fecha_nac').prop('disabled',false);
                            }

                          }else{  //CAMPOS ESPECÍFICOS PERSONA JURÍDICA
                            //tipo de sociedad
                            if(data['persona']['tipo_sociedad']!=undefined && data['persona']['tipo_sociedad']!="" && data['persona']['tipo_sociedad']!==null && jQuery('#tipo_sociedad :enabled')){
                              jQuery('#tipo_sociedad').val(data['persona']['tipo_sociedad']);
                              jQuery('#tipo_sociedad').prop('disabled', true)
                            }else{
                              jQuery('#tipo_sociedad').prop('disabled', false);
                              jQuery('#tipo_sociedad').html(sociedad);
                            }

                            //tipo de situación
                            if(data['persona']['tipo_situacion']!=undefined && data['persona']['tipo_situacion']!="" && data['persona']['tipo_situacion']!==null && jQuery('#tipo_situacion :enabled')){
                              jQuery('#tipo_situacion').val(data['persona']['tipo_situacion']);
                              jQuery('#tipo_situacion').prop('disabled', true)
                            }else{
                              jQuery('#tipo_situacion').prop('disabled', false);
                              jQuery('#tipo_situacion').html(sit_gan);
                            }

                            //nombre del titular
                            if(data['persona']['nombre_titular']!=undefined && data['persona']['nombre_titular']!=="" && data['persona']['nombre_titular']!==null && jQuery('#razon_social :enabled')){
                              jQuery('#razon_social').val(data['persona']['nombre_titular']);
                              jQuery('#razon_social').prop('disabled', true)
                            }else{
                              jQuery('#razon_social').prop('disabled', false);
                            }
                          }
                          jQuery().validarCampos();
                       }else{
                        var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                        if(tipoTramite == 3)
                          jQuery('#cuit').valid();
                       }//fin data['persona']
                      }else{
                            jQuery(this).habiReset();
                      }
                    }
                  },
                  error: function(data){
                    alert("error buscando persona");
                  }
              });
  };

/* ADEN 2024-04-24
  jQuery.fn.validarIngresos = function() {
      var ingreso = jQuery('#ingresos').val();
      jQuery.ajax({
                type:'POST',
                url:'validarIngresos',
                dataType:'json',
                data: {'ingresos':ingreso},

                success: function(data){

                    if(!data.exito){
                      var error = '<error_dt for="ingresos" class="error">'+data.mensaje+'</error_dt>'
                      jQuery(error).insertAfter(jQuery('#ingresos'));
                    }else{
                      jQuery('error_dt [value="ingresos"]').remove();
                    }
                }
            });
    };
*/
    /****************************************************************/
    /*        Métodos para validación de campos del formulario      */
    /****************************************************************/

    jQuery.validator.addMethod("motivo", function(value, element) {
     return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'-.,;:\/0-9º" "^!¡?¿@#$%&*()}{@#~]+$/i.test(value);
    });

    jQuery.validator.addMethod("domicilio", function(value, element) {
     return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´º'.:0-9" "\-]+$/i.test(value);
    }, "Solo letras");

    jQuery.validator.addMethod("lettersonly", function(value, element) {
     return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'." "]+$/i.test(value);
    }, "Solo letras");


    jQuery.validator.addMethod('fecha_nacimiento', function(value, element){
        return this.optional(element) || /\d{1,2}\/[0,1]{1}[1-9]{1}\/[1-9]{1}\d{3}/.test(value);
      }, "Debe seleccionar una fecha.");

    jQuery.validator.addMethod("localidad", function(value, element) {
        var val=jQuery('#nueva_localidad').val();
        jQuery(this).buscarLocalidadPorId(val);
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

    jQuery.validator.addMethod('ingresos', function(value, element){
        return this.optional(element) || /[0-9]+\-[0-9]+\-[0-9]+$/gi.test(jQuery.trim(value));
      }, "Campo incorrecto.");



     /***********************************************************/
     /* Validación del formulario de modificación de trámites   */
     /***********************************************************/

    jQuery("#formulario_modificar_tramite").validate({
      ignore: "",
      focusInvalid: false,
      showErrors: function(errorMap, errorList) {
       this.defaultShowErrors();
      },
      submitHandler: function(form) {
        jQuery('#btn_volver').prop( "disabled", true );
        jQuery('#btn_limpiar').prop( "disabled", true );
        jQuery('#btn_guardar').prop( "disabled", true );
        form.submit();
      },
      invalidHandler: function() {
            jQuery('#cargandoModal').modal('hide');
            jQuery(this).find(":input.error:first").focus();
          },
      rules: {
            /*CAMBIO DE DOMICILIO */
            domicilio_comercial: {domicilio:function(){
                                    var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                    if(tipoTramite == 1){
                                      return true;
                                    }else{
                                      return false;
                                    }}
                                 },
            motivo_cd: {motivo:function(){
                            var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                            if(tipoTramite == 1){
                              return true;
                            }else{
                              return false;
                            }}
                        },
            /*CAMBIO DE CATEGORÍA */
            cbu:{minlength:function(){
                            var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                            if(tipoTramite == 2 || tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                              return 4;
                            }else{
                              return 0;
                            }}
                },
            motivo_cc: {motivo:function(){
                            var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                            if(tipoTramite == 2){
                              return true;
                            }else{
                              return false;
                            }}
                        },
            /*CAMBIO DE TITULAR / INCORPORACIÓN COTITULAR */
            cuit: {cuit:function(){
                                var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                if(tipoTramite == 3){
                                  var tp= jQuery('input[name=tipo_persona]').val();

                                  if(tp=="F"){
                                    return false;
                                  }else{
                                    return true;
                                  }
                                }else{
                                  return false;
                                }
                              }
                  },
            razon_social: {minlength:function(){
                              var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                              if(tipoTramite == 3){
                                return 2;
                              }else{
                                return 0;
                              }}
                          },
            apellido_nombre: {minlength:function(){
                                    var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                    if(tipoTramite == 3 || tipoTramite == 8){
                                      return 2;
                                    }else{
                                      return 0;
                                    }}
                             },
             apellido_mat: { lettersonly: function(){
                                var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                if(tipoTramite == 3 )
                                  return true;
                                else
                                  return false;
                                },
                            minlength:function(){
                                var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                if(tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                                  return 2;
                                }else{
                                  return 0;
                                }}
                          },
             domicilio: {domicilio: function(){
                                      var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                      if(tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                                        return true;
                                      }else{
                                        return false;
                                      }
                                    },
                        minlength:function(){
                                      var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                      if(tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                                        return 2;
                                      }else{
                                        return 0;
                                      }}
                       },
            ingresos: {maxlength:function(){
                              var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                              if(tipoTramite == 3){
                                return 13;
                              }else{
                                return 0;
                              }},
                       ingresos:function(){
                            var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                            if(tipoTramite == 3){
                              return true;
                            }else{
                              return false;
                            }}
                      },
            email: {email:function(){
                            var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                            if(tipoTramite == 3){
                              return true;
                            }else{
                              return false;
                            }}
                   },
            referente: { minlength:function(){
                              var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                              if(tipoTramite == 1 || tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                                return 4;
                              }else{
                                return 0;
                              }},
                        maxlength:function(){
                              var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                              if(tipoTramite == 1 || tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                                return 255;
                              }else{
                                return 0;
                              }}
                      },
            datos_contacto: { minlength:function(){
                                var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                if(tipoTramite == 1 || tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                                  return 4;
                                }else{
                                  return 0;
                                }},
                              maxlength:function(){
                                var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                if(tipoTramite == 1 || tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                                  return 255;
                                }else{
                                  return 0;
                                }}
                            },
            nueva_localidad: {/*required:function(){
                                  var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                  if(tipoTramite == 3 || tipoTramite == 8){
                                    return true;
                                  }else{
                                    return false;
                                  }},*/
                              localidad:function(){
                                  var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                  if(tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                                    return true;
                                  }else{
                                    return false;
                                  }},
                              maxlength:function(){
                                  var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                                  if(tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                                    return 255;
                                  }else{
                                    return 0;
                                  }}
                              },
            motivo_ct: {motivo:function(){
                            var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                            if(tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                              return true;
                            }else{
                              return false;
                            }}
                       },
            /*CAMBIO DE DEPENDENCIA */
            motivo_cr: {motivo:function(){
                            var tipoTramite = jQuery('input[name=tipo_tramite]').val();
                            if(tipoTramite == 3 || tipoTramite == 8 || tipoTramite == 12){
                              return true;
                            }else{
                              return false;
                            }}
                        },

            },
      messages: {
                domicilio_comercial:{domicilio: "Solo letras, números y algunos caracteres especiales."},
                cbu: {minlength:"Mín. 4 caracteres."},
                motivo_cc: {motivo:"Números y letras"},
                cuit: {cuit:"No válido"},
                razon_social: {minlength: "Mín. 2 caracteres"},
                apellido_nombre: {lettersonly: "Solo letras", minlength: "Mín. 2 caracteres"},
                apellido_mat: {lettersonly: "Solo letras", minlength: "Mín. 2 caracteres"},
                domicilio: {domicilio: "Solo letras, números y algunos caracteres especiales.", minlength: "Mín. 2 caracteres"},
                nueva_localidad: {localidad:" Debe seleccionar una localidad.", maxlength:"Ingrese hasta 255 caracteres."},
                email:{email:"Ingrese un e-mail válido."},
                motivo_ct: {motivo:"Números y letras"},
                motivo_cr:{motivo:"Números y letras"},
            },
      errorElement: "error_dt"
  });



    /**
    * Campo cuit pierde el foco
    **/
    jQuery('#cuit').focusout(function(){
      if(jQuery('#cuit').val().length>0){
       var tipoTramite = jQuery('input[name=tipo_tramite]').val();
       if(tipoTramite == 3)
        jQuery(this).buscarPersona(jQuery('#cuit').val(),4);
      }
    });

    /**
    * Campo nueva localidad pierde el foco
    **/
    jQuery('#buscarLocalidad').focusout(function(){
      if(jQuery('#buscarLocalidad').val().length<=0){
        jQuery('#nueva_localidad').val("");
        jQuery('#nombre_localidad').val("");
      }
    });


  //combo tipo de situación
  jQuery('#tipo_situacion').on('change', function(){
          var id =jQuery(this).val();
          jQuery('#tipo_situacion option').each(function(){
            jQuery(this).attr('selected',false);
            if(jQuery(this).val() == id){
               jQuery(this).attr('selected',true);
               this.selected = this.defaultSelected;//firefox
             }
          });

   });


  /**
  * Ingresos
  **/
/* ADEN - 2024-04-24
  jQuery('#ingresos').focusout(function(){
      if(jQuery(this).val().length>10){
        jQuery().validarIngresos();
      }
    });
*/
   /*cambio en el campo email*/
  jQuery('#id_email').on('change', function(){
          jQuery(this).buscarCorreoElectronico(jQuery('#id_email').val(), jQuery('#id_permiso').val());
   });


  //botón volver
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

  /**
  * Función de limpieza del formulario
  **/
  jQuery('#btn_limpiar').on('keydown click', function(e){
      jQuery(':input','#formulario_modificar_tramite')
        .removeAttr('checked')
        .removeAttr('selected')
        .not(':button, :submit, :reset, :hidden, :disabled, :radio, :checkbox')
        .val('');
      //jQuery('#formulario_modificar_tramite').trigger('reset'); -- No limpia los que traen datos
  });


  //botón guardar
  jQuery('#btn_guardar').on('keydown click', function(e){
	console.log('MM - entra');
	  
	var tipo_tramite = jQuery('#tipo_tramite').val();
	if(tipo_tramite == 2){
	  
	    var subage = jQuery('#nro_subagente').val();
		var categ = jQuery('#categoria_nueva').val();
		var red = jQuery('#nro_red').val();
		if(categ == '1'){ // quiere ser subagente
			// alert('camino casi correcto');
			if(subage != '' && parseInt(subage) > 0 ){
				  var code = e.keyCode || e.which;
					if(e.type == "keydown"){
					   if(code == 13) { //Enter keycode
						jQuery(this).submitFormModificarTramite();
					  }
					}else if(e.type == "click"){
						jQuery(this).submitFormModificarTramite();
					}

					// return false;
			}else{
				jQuery('#nro_subagente').focus();
				return false;
			}
		}else{ // quiere ser agente
			if(subage !='' && parseInt(subage) == 0){
				  var code = e.keyCode || e.which;
					if(e.type == "keydown"){
					   if(code == 13) { //Enter keycode
						jQuery(this).submitFormModificarTramite();
					  }
					}else if(e.type == "click"){
						jQuery(this).submitFormModificarTramite();
					}

					// return false;
			}else{
				jQuery('#nro_subagente').focus();
				return false;
			}
		}
		}else{
			console.log('MM - entra a no es cambCat');
			var code = e.keyCode || e.which;
			if(e.type == "keydown"){
			   if(code == 13) { //Enter keycode
				jQuery(this).submitFormModificarTramite();
			  }
			}else if(e.type == "click"){
				jQuery(this).submitFormModificarTramite();
			}
		}
  });

  jQuery.fn.submitFormModificarTramite = function(){
      //muestro la ruedita de cargando
     jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
     jQuery('#cargandoModal').modal('show');

     //valido el formulario
     var valido = jQuery('#formulario_modificar_tramite').validate().form();
	// alert(valido);
     if(valido){
        jQuery('#btn_volver').prop( "disabled", true );
        jQuery('#btn_limpiar').prop( "disabled", true );
        jQuery('#btn_guardar').prop( "disabled", true );
        jQuery('#formulario_modificar_tramite').submit();
     }else{
        jQuery('#cargandoModal').modal('hide');
     }

  }
	console.log('pasa a carga de tipo rel');

	jQuery('#tipo_rel').on('change', function(){
			
			var trval2 = jQuery('#tipo_rel').val();
		
			if(trval2 != 1){
				jQuery('#tipo_vin').val(0);
				var tipo_vin = jQuery('#tipo_vin').val();
				jQuery('#tipo_vinh').val(tipo_vin);
			}else if(trval2 == 0){
				jQuery('#tipo_vin').val(0);
				jQuery('#tipo_vinh').val(0);
			}else{
				var tipo_vin = jQuery('#tipo_vin').val();
				jQuery('#tipo_vinh').val(tipo_vin);
			}
		});
		
		

		
	/* validaciones para formulario modificacion de cambio de categoria */	
		
	jQuery('#nro_red').on('change', function(e){
        if(jQuery('#nro_red').val()!='')
   			  jQuery('#nro_red').trigger({type: 'keydown', which: 13, keyCode: 13});

   		});
     
      jQuery('#nro_red').on('keydown',function(e){

        var code = e.keyCode || e.which;
		
		
		var nro_red = jQuery('#nro_red').val();
		var subagente = jQuery('#nro_subagente').val();
        var red_actual = jQuery('#id_agente').val();
        var es_nuevo_permiso = jQuery('#es_nuevo_permiso').val(); //jQuery('#es_nuevo_permiso').val(); // jQuery('input[name="es_nuevo_permiso"]').attr("value");
       var categoria =jQuery('#categoria_nueva').val(); // 0-> quiere ser agente   1-> quiere ser subagente
	   // var categoria = jQuery('#id_categoria').children(":selected").attr("value");

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
              if(categoria == 1){ //Subagente
					console.log('MM - categoria:'+categoria);
					console.log('MM - nro_red:'+nro_red);
					console.log('MM - subagente:'+subagente);
					console.log('MM - modalidad:'+modalidad);
                jQuery(this).submitRed(nro_red, subagente, modalidad, 2);
              }else //agente
			    // alert('soy agente!'+nro_red+'-'+subagente+'-'+modalidad);
                if(es_nuevo_permiso){ 
					console.log('es_nuevo_permiso');
					// alert('es_nuevo_permiso');
					var cpscp = jQuery('input[name="cpscp_localidad"]').val().split("-");
					jQuery(this).numeroNuevaRed(cpscp[0], nro_red, 2);
                }
                else{
					// alert('no es_nuevo_permiso');
					console.log('MM - categoria:'+categoria);
					console.log('MM - nro_red:'+nro_red);
					console.log('MM - subagente:'+subagente);
					console.log('MM - modalidad:'+modalidad);
					jQuery(this).submitRed(nro_red, subagente, modalidad, 1);
                }
          
          }else if(nro_red.length<3 || code == 8){//code==8->borrado
              jQuery('#razon_social_nr').val('');
              jQuery('#div_razon_social').hide();
          }          
        }
     });
		
	jQuery('#nro_subagente').on('focusout', function(e){
		// alert('va');
        var nroSubAg = jQuery('#nro_subagente').val();
		var categoria_nueva = jQuery('#categoria_nueva').val();
		var nro_red = jQuery('#nro_red').val();
        var subagente = jQuery('#nro_subagente').val();

		if(categoria_nueva == '1'){ // quiere ser subagente
			// alert('camino casi correcto');
			if(nroSubAg != '' && parseInt(nroSubAg) > 0 ){
				// alert('camino correcto');
				 jQuery(this).submitSubagente(nro_red, subagente);
				  // jQuery('#nro_subagente').trigger({type: 'keydown', which: 13, keyCode: 13});
			}else{
				jQuery('#nro_subagente').val('');
				jQuery('#mensaje').text('La subagencia debe ser mayor a 0');
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();  
					
					// return false;
			}
		}else{ // quiere ser agente
			if(nroSubAg !=''){
				 jQuery(this).submitSubagente(nro_red, subagente);
				  // jQuery('#nro_subagente').trigger({type: 'keydown', which: 13, keyCode: 13});
			}
		}
		});
		/*
			jQuery('#nro_subagente').on("keydown",function(e){ //click
			alert('ve');
				var code = e.keyCode || e.which;
				var nro_red = jQuery('#nro_red').val();
				var subagente = jQuery('#nro_subagente').val();
				if((code == 13 || code == 9 || e.type =="click") && nro_red != '')
					  if(subagente!=''){//todavía no cargó el nº de subagente
						  jQuery(this).submitSubagente(nro_red, subagente);
					  }
			});
		*/
		
		
	/**
      * Función para buscar la red de la que pasará a ser subagente
      **/
     jQuery.fn.submitRed = function(nro_red, nro_subagente, modalidad, quieroSer) {
      // alert('nro_red:'+nro_red+' nro_subagente:'+nro_subagente+' modalidad:'+modalidad+' quieroSer:'+quieroSer);
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
                    console.log('MM - data: '+data);
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
		
		
});//fin document ready

 function refrescar(formulario)
{
     //reset de todos los elementos del formulario
   document.getElementById(formulario).reset();
   //document.getElementById('id_nombre').focus();
   jQuery('#formulario_modificar_tramite')[0].reset();
}









