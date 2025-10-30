jQuery.noConflict();
jQuery(document).ready(function() {
  jQuery.ajaxSetup({
    cache: false
  });

  jQuery('#id_est_t').load(comparar());

  /******************************************reglas de validacion**************************/
  jQuery.validator.addMethod("tipo_sociedad", function(value, element) {
    return ("ni" != value);
  });
  jQuery.validator.addMethod("tipo_situacion", function(value, element) {
    var tipoTramite = jQuery('#id_tipo_tramite').val();
    if (tipoTramite == 3) {
      if (("rni" == value) || ("exento" == value))
        return true;

      return ("ni" != value);
    } else {
      return true;
    }

  });

  /*******************FUNCIONES PROPIAS******************/


  jQuery.fn.enviarEmailModificacionEstado = function() {
    jQuery.ajax({
      type: 'get',
      url: 'envio_mail_cambio_estado',
      dataType: 'json',
      async: true,
      success: function(data) {;
      },
      error: function(data) {
        var valor = jQuery('#estados_t').children(":selected").attr("value");
        if (Number(valor) != 7) { //en aprobado no mostrar
          jQuery('#mensaje').text("Problema al enviar email.");
          jQuery('#errores').show();
          jQuery('#errores').delay(2000).fadeOut();
        }

      }

    });

  };

  /************************ CAMBIO TITULAR ******************/
  /**
   * Validación de ingresos brutos
   **/
/* aden - 2024-04-24   
  jQuery.fn.validarIngresos = function() {
    var ingreso = jQuery('#ingresos').val();
    var situGan = jQuery('#tipo_situacion option:selected').val();
    if (situGan == "rni" || situGan == "exento")
      return true;
    else
      jQuery.ajax({
        type: 'POST',
        url: 'validarIngresos',
        dataType: 'json',
        data: {
          'ingresos': ingreso
        },

        success: function(data) {

          if (!data.exito) {
            var error = '<error_dt for="ingresos" class="error">' + data.mensaje + '</error_dt>'
            jQuery(error).insertAfter(jQuery('#ingresos'));
            return false;
          } else {
            jQuery('error_dt [value="ingresos"]').remove();
            return true;
          }
        }
      });
  };
*/
  jQuery.fn.buscarCorreoElectronico = function(correo, permiso) {
    jQuery.ajax({
      type: 'POST',
      url: "correo_valido",
      dataType: 'json',
      data: {
        'email': correo,
        'permiso': permiso
      },
      async: false,
      success: function(data) {

        if (data['mensaje'] == "1") {
          /*jQuery('#mensaje').text("El correo ya existe. Ingrese otro.");
						jQuery('#email').val("");
                        jQuery('#errores').show();
                        jQuery('#errores').delay(3000).fadeOut();*/
          jQuery('#email').val("");
          alertify.error("El correo ya existe. Ingrese otro.", 2);
        }
      },
      error: function(data) {
        /*jQuery('#mensaje').text("Correo no válido.");
        jQuery('#errores').show();
        jQuery('#errores').delay(3000).fadeOut();*/

        alertify.error("Correo no válido.", 2);
      }
    });
  };

  /*cambio en el campo email*/
  jQuery('#email').on('change', function() {
    jQuery(this).buscarCorreoElectronico(jQuery('#email').val(), jQuery('#nro_permiso_').val());
  });




  //Para cambio de categoría a agente
  jQuery.fn.numeroNuevaRed = function(codigopostal, nro_red, modalidad) {

    jQuery.ajax({
      type: "POST",
      url: 'numeroNuevaRed',
      dataType: 'json',
      data: {
        'codigoPostal': codigopostal,
        'nro_red': nro_red,
        'modalidad': modalidad
      },
      /* cache: false,*/
      async: false,
      beforeSend: function() {
        jQuery('#icono_error').hide();
        jQuery('#cargandoModal').removeData("modal").modal({
          backdrop: 'static',
          keyboard: false
        });
        jQuery('#cargandoModal').modal('show');
      },
      complete: function() {
        jQuery('#cargandoModal').modal('hide');
      },
      success: function(data) {
        if (data['nro_nueva_red'] != 0) {
          jQuery('#icono_ok').show();
          jQuery('#icono_error').hide();
          jQuery('#nro_nueva_red').val(data['nro_nueva_red']);
        } else {
          jQuery('#nro_nueva_red').val('');
          jQuery('#mensaje').text("Nº de red no disponible");
          jQuery('#errores').show();
          jQuery('#errores').delay(2000).fadeOut();
          jQuery('#nro_nueva_red').focus();
          jQuery('#icono_ok').hide();
          jQuery('#icono_error').show();
        }

      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        // alert(XMLHttpRequest.responseText);
        alertify.error("No se pudo determinar el nº de la nueva red.", 2);
      }
    });
  }

  jQuery('#nro_nueva_red').on('keydown, focusout', function(e) {
    var code = e.keyCode || e.which;
    var nro_red = jQuery('#nro_nueva_red').val(); //nueva red
    var nro_red_orig = jQuery("#nro_nueva_red_original").attr('value');
    if (nro_red == nro_red_orig) {
      jQuery('#icono_ok').show();
      jQuery('#icono_error').hide();
      return true;
    }
    var subagente = jQuery('#nro_nuevo_sbag').attr('value');
    var red_actual = jQuery('#id_agente').val(); //sólo si no es nuevo permiso
    var es_nuevo_permiso = jQuery('#nuevo_permiso').attr('value');
    var categoria = jQuery('#suba_a_ag').val();

    if (nro_red.length >= 3 && Number(red_actual) != Number(nro_red)) {
      if ((code == 13 || code == 9 || code == 0) && nro_red.length >= 3) { //Enter keycode 

        //éste control es para cambio de categoría
        if (es_nuevo_permiso == 1 || categoria == 1) {
          var cpscp = jQuery('#cp_localidad_m_').attr('value');
          var modalidad = 2;

          jQuery(this).numeroNuevaRed(cpscp, nro_red, modalidad);
        } else {
          var modalidad = 0;
          jQuery(this).submitRed(nro_red, subagente, modalidad);
        }

      } else if (nro_red.length < 3 || code == 8) { //code==8->borrado
        jQuery('#razon_social_nr').val('');
        jQuery('#div_razon_social').hide();
      }
    }
  });



  /**
   * Función para buscar la red de la que pasará a ser subagente
   **/
  jQuery.fn.submitRed = function(nro_red, nro_subagente, modalidad) {
    jQuery.ajax({
      type: 'POST',
      url: 'control_red',
      dataType: 'json',
      data: {
        'nro_red': nro_red,
        'nro_subagente': nro_subagente,
        'modalidad': modalidad
      },
      async: false,
      beforeSend: function() {
        jQuery('#cargandoModal').removeData("modal").modal({
          backdrop: 'static',
          keyboard: false
        });
        jQuery('#cargandoModal').modal('show');
      },
      complete: function() {
        jQuery('#cargandoModal').modal('hide');
      },
      success: function(data) {
        if (data['mensaje']) {
          jQuery('#icono_ok').hide();
          jQuery('#icono_error').show();
        } else {
          jQuery('#icono_ok').show();
          jQuery('#icono_error').hide();
          jQuery('#nro_nuevo_sbag').attr('value', data['nro_subagente']);

        }
      },
      error: function(data) {
        alert('error');
      }

    });

  };

  /**
   * Función para ver que el nº de subagente sea válido
   **/
  jQuery.fn.submitSubagente = function(nro_red, nro_subagente) {
    jQuery.ajax({
      type: 'POST',
      url: 'control_subagente',
      dataType: 'json',
      data: {
        'nro_red': nro_red,
        'nro_subagente': nro_subagente
      },
      async: false,
      beforeSend: function() {
        jQuery('#cargandoModal').removeData("modal").modal({
          backdrop: 'static',
          keyboard: false
        });
        jQuery('#cargandoModal').modal('show');
      },
      complete: function() {
        jQuery('#cargandoModal').modal('hide');
      },
      success: function(data) {

        if (data['mensaje']) {
          jQuery('#mensaje').text(data['mensaje']);
          jQuery('#errores').show();
          jQuery('#errores').delay(2000).fadeOut();
          jQuery('#nro_nuevo_sbag').focus();
          jQuery('#nro_nuevo_sbag').val('');
        } else {
          jQuery('#resolucion').focus();
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        //alert(XMLHttpRequest.responseText);
        jQuery('#mensaje').text("Problema con el nº de subagente.");
        jQuery('#errores').show();
        jQuery('#errores').delay(2000).fadeOut();
        jQuery('#cargandoModal').modal('hide');
      }

    });

  };

  jQuery('#nro_nuevo_sbag').on("keydown, focusout", function(e) {
    var code = e.keyCode || e.which;
    var nro_red = jQuery('#nro_red').val();
    var nuevoSubagente = jQuery('#nro_nuevo_sbag').val();
    if (nuevoSubagente == 0) {
      jQuery('#nro_nuevo_sbag').val('');
      jQuery('#nro_nuevo_sbag').focus();
      jQuery('#mensaje').text("Problema con el nº de subagente.");
      jQuery('#errores').show();
      jQuery('#errores').delay(2000).fadeOut();
      return false;
    }
    if ((code == 13 || code == 9 || code == 0) && nro_red != '')
      if (nuevoSubagente != '') { //todavía no cargó el nº de subagente
        jQuery(this).submitSubagente(nro_red, nuevoSubagente);
      }
  });

  /**
   * Cambio de categoria - Agente=>subAgente - agente sin agencias activas
   **/
  jQuery.fn.okAprobar = function() {
    var permiso = jQuery('#nro_permiso_').val();
    var respuesta = jQuery.ajax({
      type: 'POST',
      url: 'ok-aprobar',
      dataType: 'json',
      data: {
        'permiso': permiso
      },
      async: true,
      success: function(data) {
        return data.exito;
      },
      error: function() {
        return false;
      }
    });
    return respuesta;

  };
  /************************ FIN CAMBIO CATEGORIA A SUBAGENTE **************************/


  jQuery.fn.cambioObservacionesEstado = function(observaciones, nro_tramite, fecha, estaI, estaF) {

    jQuery.ajax({
      type: 'POST',
      url: 'nueva_observacion_estado',
      dataType: 'json',
      data: {
        'observaciones': observaciones,
        'nro_tramite': nro_tramite,
        'fecha': fecha,
        'estaI': estaI,
        'estaF': estaF
      },
      async: false,
      beforeSend: function() {
        jQuery('#cargandoModal').removeData("modal").modal({
          backdrop: 'static',
          keyboard: false
        });
        jQuery('#cargandoModal').modal('show');
      },
      complete: function() {
        jQuery('#cargandoModal').modal('hide');
      },
      success: function(data) {
        jQuery('#formulario-historial').submit();
      }
    });
    return false;

  };

  /************************FIN FUNCIONES PROPIAS**********************/
  /**********************FUNCIONES DE VALIDACIÓN********************/
/*  
  jQuery.validator.addMethod('ingresos', function(value, element) {
    var situGan = jQuery("#tipo_situacion option:selected").val();
    if (("rni" != situGan) && ("exento" != situGan))
      return this.optional(element) || /[0-9]+\-[0-9]+\-[0-9]+$/gi.test(jQuery.trim(value));
    return true;
  }, "Campo incorrecto.");
*/
  //valida que haya completado los comentarios/resolución y expediente
  jQuery("#formulario-estado").validate({

    errorElement: 'error_dt',
    errorPlacement: function(error, element) {
      var type = jQuery(element).attr("type");
      if (type === "radio") {
        // custom placement
        error.insertAfter(element).wrap('<li/>');
      } else if (type === "checkbox") {
        // custom placement
        error.insertAfter(element).wrap('<li/>');
      } else {
        error.insertAfter(element).wrap('<div/>');
      }
    },

    rules: {
      textoComentarios_m: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) != 7) {
            return true;
          } else {
            return false;
          }
        },
        minlength: 5,
        maxlength: 255
      },
      nro_nueva_red: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            var tipoTramite = jQuery('#id_tipo_tramite').val();
            var sah = jQuery('#subagente_h').val();
            if (tipoTramite == 2 && sah == 0) {
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        },
        number: true,
        minlength: 4,
        maxlength: 5
      },
      nro_red: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            var tipoTramite = jQuery('#id_tipo_tramite').val();
            var sah = jQuery('#subagente_h').val();
            if (tipoTramite == 2 && sah != 0) {
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        },
        number: true
      },
/* aden - 2024-04-24
      cbu: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          var tienePI = jQuery('#tienePI').val();
          var subAg = jQuery('#subagente').val();

          if (Number(activo) == 7) {
            var tipoTramite = jQuery('#id_tipo_tramite').val();
            if ((tipoTramite == 3 || tipoTramite == 2 ) && subAg == 0) { //agregar a la primera condición si es agente 00 - agregar a la últim condición lo de si tiene algo pendiente
              return true;
            } else if (tipoTramite == 12){
              return true;
            }else {
              return false;
            }
          } else {
            return false;
          }
        },
        minlength: 4
      },
*/
/* aden - 2024-04-24
      ingresos: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            var tipoTramite = jQuery('#id_tipo_tramite').val();
            if (tipoTramite == 3) {
              var situGan = jQuery("#tipo_situacion option:selected").val();
              if (("rni" == situGan) || ("exento" == situGan))
                return false;
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        },
        ingresos: true
      },
*/	  
      email: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            var tipoTramite = jQuery('#id_tipo_tramite').val();
            if (tipoTramite == 3) {
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        }
      },
      referente: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            var tipoTramite = jQuery('#id_tipo_tramite').val();
            if (tipoTramite == 3) {
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        },
        minlength: 4,
        maxlength: 255
      },
      datos_contacto: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            var tipoTramite = jQuery('#id_tipo_tramite').val();
            if (tipoTramite == 3) {
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        },
        minlength: 4,
        maxlength: 255
      },
      cuit: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            var tipoTramite = jQuery('#id_tipo_tramite').val();
            var cuitActual = jQuery('#cuit_actual').val();
            if (tipoTramite == 3 && (cuitActual == "" || cuitActual == null)) {
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        },
        cuit: true
      },
      tipo_sociedad: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            var tipoTramite = jQuery('#id_tipo_tramite').val();
            var tipo_persona = jQuery('#tipo_persona').val()
            if (tipoTramite == 3 && tipo_persona == 'J') {
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        },
        tipo_sociedad: true
      },
      tipo_situacion: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          var tipoTramite = jQuery('#id_tipo_tramite').val();
          if (Number(activo) == 7) {
            if (tipoTramite == 3) {
              return true;
            } else {
              return false;
            }
          } else {
            return false;
          }
        },
        tipo_situacion: true
      },
      fecha_nac: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            return true;
          } else {
            return false;
          }
        }
      },
      resolucion: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            return true;
          } else {
            return false;
          }
        }
      },
      expediente: {
        required: function() {
          var activo = jQuery('#estados_t').children(":selected").attr("value");
          if (Number(activo) == 7) {
            return true;
          } else {
            return false;
          }
        }
      }


    },
    messages: {
      textoComentarios_m: {
        required: "*",
        minlength: " Por favor ingrese al menos 5 caracteres."
      },
      nro_nueva_red: {
        required: "*",
        number: "Sólo Nº",
        minlength: "Mín. 4 Nº",
        maxlength: "Máx. 5 Nº"
      },
      nro_red: {
        required: "Ingrese Nº de red.",
        number: "Sólo Nº"
      },
      cuit: {
        required: "*",
        cuit: "Cuit incorrecto."
      },
      email: {
        required: "*",
        email: "Email incorrecto."
      },
      cbu: {
        required: "*"
      },
      fecha_nac: {
        required: "*"
      },
      tipo_sociedad: {
        required: "*",
        tipo_sociedad: "No válido."
      },
      tipo_situacion: {
        required: "*",
        tipo_situacion: "No válido."
      },
      referente: {
        required: "*",
        minlength: "Mín. 4 caracteres"
      },
      datos_contacto: {
        required: "*",
        minlength: "Mín. 4 caracteres"
      },
/* aden - 2024-04-24
      ingresos: {
        required: "*",
        maxlength: "Máx. 11"
      },
*/	  
      resolucion: {
        required: "*"
      },
      expediente: {
        required: "*"
      }
    }

  });

  /********************FIN FUNCIONES DE VALIDACIÓN*********************/



  /*Select estados*/
  jQuery('#estados_t').change(function() {
    var valor = jQuery('#estados_t').children(":selected").attr("value");

    var tipoTramite = jQuery('#id_tipo_tramite').val();
    var estadoInicial = jQuery('#estadoI').val();
    var subagente = jQuery('#subagente').val();
    var nuevoPermiso = jQuery('#nuevo_permiso').val();

    if (Number(estadoInicial) != Number(valor)) {
      var okAprobar = "1";
      if (tipoTramite == 2 && subagente == 0 && Number(nuevoPermiso) == 0 && (Number(valor) == 6 || Number(valor) == 7)) {
        okAprobar = jQuery().okAprobar();
      } else if (tipoTramite == 2 && subagente == 0 && Number(nuevoPermiso) == 1 && (Number(valor) == 6 || Number(valor) == 7)) {
        okAprobar = 1;
      }

      if (okAprobar) {
        if (Number(valor) == 7) { //aprobar
          jQuery('#textoComentarios_m').val('');
          jQuery('#textoComentarios_m').attr('placeholder', "Ingrese un comentario...");
          jQuery('#divCambioEstado').hide();
          jQuery('#divEstadosResolExp').show();
          if (tipoTramite == 2) {
            if (subagente != "0") { //pasa a ser subagente                                                     
              jQuery('#divSubAAg').show();
              jQuery('input#nro_nueva_red').val(jQuery('input#nro_nueva_red').attr('value'));
            } else { //agente a subagente     
              var nuevoSubagente = jQuery('#nro_nuevo_sbag').val();
              if (nuevoSubagente == 0) { //para los cambios de cat. generados antes de las modificaciones
                var modalidad = 0;
                var nro_red = jQuery('#nro_red').val();
                jQuery(this).submitRed(nro_red, 0, modalidad);
              }
              jQuery('#divAgASub').show();
            }
            /*}else if(tipoTramite==4){
                    if(nuevoPermiso!="0"){//pasa a ser subagente                                                     
                        jQuery('#divAgASub').show();
                    }*/
          } else if (tipoTramite == 3 || tipoTramite == 8) { //titular || cotitular
            var tipoSocActual = jQuery('#tipo_soc_actual').val();
            var sexoActual = jQuery('#sexo_actual').val();
            var cuitActual = jQuery('#cuit_actual').val();
            var emailActual = jQuery('#email_actual').val();
            var sitGanActual = jQuery('#sit_gan_actual').val();
            var fechaNacActual = jQuery('#fecha_nacimiento').val();

            if (cuitActual != "" && cuitActual != null) {
              jQuery('#cuit').validate();
              jQuery('#cuit').prop('disabled', true);
            }
            if (sitGanActual != "" && sitGanActual != null) {
              jQuery('#tipo_situacion option[value="' + sitGanActual + '"]').prop('selected', true);
            }
            if (fechaNacActual != "00/00/00" && fechaNacActual != null) {
              jQuery('#fecha_nac').attr('value', fechaNacActual);
              jQuery('#fechaNac').validate();
              jQuery('#fechaNac').prop('disabled', true);
            }
            if (sexoActual !== "" && sexoActual !== undefined && sexoActual !== "S") {
              jQuery('#tipo_persona_juridica').hide();
              jQuery("input[name=sexo_persona][value='" + sexoActual + "']").prop("checked", true);
              jQuery('#sexo_persona_fisica').show();
              if (cuitActual != "" && cuitActual != null) {
                jQuery('#tipo_persona').prop('disabled', true);
                jQuery('input[name=sexo_persona]').prop('disabled', true);
              }
            } else {
              jQuery('#sexo_persona_fisica').hide();
              jQuery('#tipo_sociedad').val(tipoSocActual);
              jQuery('#tipo_persona_juridica').show();
            }

            jQuery('#divCuit').show();
          }
        } else {
          jQuery('#resolucion').val('');
          jQuery('#expediente').val('');
          jQuery('#divEstadosResolExp').hide();
          jQuery('#divSubAAg').hide();
          jQuery('#divAgASub').hide();
          jQuery('#divCuit').hide();
          jQuery('#textoComentarios_m').attr('placeholder', "Ingrese un comentario...");
          jQuery('#textoComentarios_m').focus();
          jQuery('#divCambioEstado').show();

        }
      } else {
        if (Number(valor) == 6 || Number(valor) == 7) {
          jQuery('#textoComentarios_m').val('');
          jQuery('#textoComentarios_m').attr('placeholder', "Ingrese un comentario...");
          jQuery('#divCambioEstado').hide();
          jQuery('#divEstadosResolExp').hide();
        }
        alertify.error('El permiso tiene subagencias activas', 2);
      }

    } else {
      jQuery('#textoComentarios_m').val('');
      jQuery('#textoComentarios_m').attr('placeholder', "Ingrese un comentario...");
      jQuery('#divCambioEstado').hide();
      jQuery('#resolucion').val('');
      jQuery('#expediente').val('');
      jQuery('#divSubAAg').hide();
      jQuery('#divAgASub').hide();
      jQuery('#divCuit').hide();
      jQuery('#divEstadosResolExp').hide();
    }
  });



  //click botón aplicar cambio de estado.
  jQuery('#btn_aplicar').on('click', function() {
    var valido = jQuery("#formulario-estado").validate().form();

    if (valido) {
      jQuery('.formulario-estado').submit();
    }

  });

  //click botón aplicar cambio de estado. - resolución expediente
  jQuery('#btn_aplicar_re').on('click', function() {
    var tt = jQuery('#tipo_tramite').val();
	/*
    if (Number(tt) == 3 || Number(tt) == 8) {
      if (Number(tt) == 3)
        jQuery().validarIngresos();

      jQuery(this).buscarCorreoElectronico(jQuery('#email').val(), jQuery('#nro_permiso_').val());
    }
	*/
    var valido = jQuery("#formulario-estado").validate().form();
    if (valido) {
      if (Number(tt) == 2)
        jQuery('#nro_nueva_red').prop('disabled', false);
      if (Number(tt) == 3 || Number(tt) == 8) {
        jQuery('#cuit').prop('disabled', false);
        jQuery('#tipo_persona').prop('disabled', false);
        jQuery('#fecha_nacimiento').attr('value', jQuery('#fecha_nac').val());
        if (jQuery('#tipo_persona').val() == "F")
          jQuery('input[name=sexo_persona]').prop('disabled', false);
        else
          jQuery('#tipo_sociedad').prop('disabled', false);
      }
      var suba = jQuery('#subagente').val();

      if (Number(tt) == 2 && Number(suba) == 0) {
        var nro_red_valido = jQuery('#nro_red').valid();
        var rz = jQuery('#razon_social').val();

        if (typeof rz !== 'undefined') {
          if (nro_red_valido && valido) {
            jQuery('.formulario-estado').submit();
          } else {
            alertify.error('Red no válida.', 3);
          }
        } else {
          alertify.error('Red no válida. Razón Social.', 3);
        }
      } else {
        jQuery('.formulario-estado').submit();
      }
    }

  });


  /*********** Actualización de domicilio ****************/
  jQuery('#btn_actualizar').on('click', function() {
    jQuery('.formulario-actualiza-dom').submit();
  });

  //submit formulario de actualización de domicilio
  jQuery('.formulario-actualiza-dom').bind('submit', function() {
    jQuery.ajax({

      type: jQuery('.formulario-actualiza-dom').attr('method'),
      url: jQuery('.formulario-actualiza-dom').attr('action'),
      dataType: 'json',
      data: jQuery('.formulario-actualiza-dom').serialize(),
      async: true,
      beforeSend: function() {
        jQuery('#domicilio_viejo').val(jQuery('#nueva_direccion').val());
        jQuery('#cargandoModal').removeData("modal").modal({
          backdrop: 'static',
          keyboard: false
        });
        jQuery('#cargandoModal').modal('show');
        jQuery('#btn_actualizar').prop("disabled", true);
      },
      complete: function() {
        jQuery('#cargandoModal').modal('hide');
      },
      success: function(data) {
        var valor = jQuery('#nueva_direccion').val();
        if (data.exito) {
          jQuery('#btn_actualizar').prop("disabled", false);
          jQuery('#nueva_direccion').val(valor);
        } else {
          jQuery('#btn_actualizar').prop("disabled", false);
          alertify.error(data.mensaje, 3);
        }
      },
      error: function(data) {
        jQuery('#cargandoModal').modal('hide');
        alertify.error("No se pudo cambiar los datos del domicilio.", 3);
      }
    });
    return false;
  });




  /**
   * Ingresos 
   **/
  jQuery('#ingresos').focusout(function() {
    if (jQuery(this).val().length > 10) {
      jQuery().validarIngresos();
    }
  });


  //mensaje del select de estados
  //jQuery('#estados').popover({content: 'Estados trámite'},'focus');

  //submit formulario de cambio de estado
  jQuery('.formulario-estado').bind('submit', function() {
    var tipotramite = jQuery('#id_tipo_tramite').val();
    if (tipotramite == 4) {
      var san = jQuery("<input>").attr("type", "hidden").attr("name", 'subagente_nuevo').val(jQuery('#nro_nuevo_sbag').val());
      jQuery('.formulario-estado').append(san);
    }

    //anexo al formulario el dato del subagente
    var sa = jQuery("<input>").attr("type", "hidden").attr("name", 'subagente_h').val(jQuery('#subagente').val());
    jQuery('.formulario-estado').append(sa);
    jQuery.ajax({

      type: jQuery('.formulario-estado').attr('method'),
      url: jQuery('.formulario-estado').attr('action'),
      dataType: 'json',
      data: jQuery('.formulario-estado').serialize(),
      async: true,
      beforeSend: function() {
        jQuery('#cargandoModal').removeData("modal").modal({
          backdrop: 'static',
          keyboard: false
        });
        jQuery('#cargandoModal').modal('show');
        jQuery("#divCambioEstado").hide();
        jQuery('#divEstadosResolExp').hide();
        jQuery('#btn-aplicar').prop("disabled", true);
        jQuery('#btn-aplicar-re').prop("disabled", true);

      },
      complete: function() {
        jQuery('#cargandoModal').modal('hide');
      },
      success: function(data) {

        var valor = jQuery('#estados_t').children(":selected").attr("value");

        if (data.exito) {
          jQuery('#estadoI').val('');
          jQuery('#estadoI').val(valor);
          var np = jQuery('#nuevo_permiso').val();
          //coloco en el combobox los nuevos estados posibles
          var est = jQuery('#estados_t').html(' ');
          jQuery.each(data['estadosPosibles'], function(key, value) {
            est.append(jQuery("<option></option>")
              .attr("value", key).text(value));
          });
          /*var arr = [ "8", "9", "10" ];
                                if(jQuery.inArray( jQuery('#estadoI').val(), arr )){*/
          if (jQuery('#estados_t option').length == 1) {
            jQuery('#estados_t').attr("disabled", "disabled");
          }
          //seteo el valor seleccionado 
          jQuery("#estados_t option[value=" + valor + "]").attr("selected", true);

          jQuery('#textoComentarios_m').val('');
          jQuery('#divCambioEstado').hide();
          //jQuery('#cuit').val('');
          jQuery('#resolucion').val('');
          jQuery('#expediente').val('');
          jQuery('#nro_nueva_red').val('');
          jQuery('#divEstadosResolExp').hide();
          jQuery('#btn-aplicar').prop("disabled", false);
          jQuery('#btn-aplicar-re').prop("disabled", false);
          jQuery('#formulario-historial').submit();
          if (Number(valor) >= 2 && Number(valor) < 6 && Number(np) == 0) {
            jQuery('#btnes_car_sol').show();
          } else {
            jQuery('#btnes_car_sol').hide();
          }

          var tt = jQuery('#id_tipo_tramite').val();

          if (Number(valor) > 6 && tt == 1) {
            jQuery('#nueva_direccion').prop("disabled", true);
            jQuery('#btn_actualizar').prop("disabled", true);
          }

          if (Number(valor) > 1 && Number(valor) < 7 && tt == 1 && data.extra['planCompleto']) {
            jQuery('#div_btn_plan_pdf').show();
            jQuery('#div_btn_eval_dom').show();
          } else if (tt == 2 && Number(valor) == 7) {
            var text = data.extra['nroNuevaRed'] + '/' + data.extra['nroNuevoSubAgente']; // jQuery('#nueva_red').val();
            jQuery('#nueva_categoria').val(text);
          } else {
            jQuery('#div_btn_plan_pdf').hide();
            jQuery('#div_btn_eval_dom').hide();
          }
          if (Number(valor) >= 6) {
            jQuery('#div_adjuntos').hide();
          }
          //sólo muestro el botón para modificar los trámites si no está aprobado.
          if (Number(valor) < 7) {
            jQuery('#div_btn_modificar_tramite').show();
          } else {
            jQuery('#div_btn_modificar_tramite').hide();
          }

          if (Number(valor) != 6 && Number(valor) != 4) {
            jQuery(this).enviarEmailModificacionEstado();
          }
        } else {

          if (jQuery('#subagente_h').length > 0) {
            jQuery('#subagente_h').remove();
          }
          if (Number(valor) == 7) {
            jQuery('#divEstadosResolExp').show();
            jQuery('#btn-aplicar-re').prop("disabled", false);
          } else {
            jQuery("#divCambioEstado").show();
            jQuery('#btn-aplicar').prop("disabled", false);
          }
          alertify.error(data.mensaje, 3);
        }
      },
      error: function(data) {
        if (jQuery('#subagente_h').length > 0) {
          jQuery('#subagente_h').remove();
        }

        jQuery('#estados_t').val(jQuery('#estadoI').val());
        jQuery('#textoComentarios_m').val('');
        jQuery('#textoComentarios_m').attr('placeholder', "Ingrese un comentario...");
        jQuery('#divCambioEstado').hide();
        jQuery('#resolucion').val('');
        jQuery('#expediente').val('');
        jQuery('#divSubAAg').hide();
        jQuery('#divAgASub').hide();
        jQuery('#divCuit').hide();
        jQuery('#cargandoModal').modal('hide');
        alertify.error("No se pudo cambiar los datos del trámite.", 2);
      }
    });
    return false;
  });

  jQuery.fn.submitPlan = function() {
    var nro_tramite = jQuery('#nro_tramite').val();

    jQuery.ajax({
      type: "POST",
      url: 'plan-pdf',
      dataType: 'json',
      cache: false,
      async: false,
      data: {
        'nro_tramite': nro_tramite
      },

      success: function(data) {
        plan_pdf();
      },
      error: function(data) {
        alertify.error("No se pudo encontrar la ruta al plan.", 2);
      }
    });
  }

  jQuery('#btn_plan_pdf').on('keydown click', function(e) {
    var code = e.keyCode || e.which;
    if (e.type == "keydown") {
      if (code == 13) { //Enter keycode
        jQuery(this).submitPlan();
      }
    } else if (e.type == "click") {
      jQuery(this).submitPlan();
    }
    return false;
  });

  jQuery.fn.submitHistorialPDF = function() {
    var nro_tramite = jQuery('#nro_tramite').val();

    jQuery.ajax({
      type: "POST",
      url: 'historial-pdf',
      dataType: 'json',
      cache: false,
      async: false,
      data: {
        'nro_tramite': nro_tramite
      },

      success: function(data) {
        historial_pdf();
      },
      error: function(data) {
        alertify.error("No se pudo encontrar la ruta al historial.", 2);
      }
    });
  }

  jQuery('#btn_imprimir_historial').on('keydown click', function(e) {
    var code = e.keyCode || e.which;
    if (e.type == "keydown") {
      if (code == 13) { //Enter keycode
        jQuery(this).submitHistorialPDF();
      }
    } else if (e.type == "click") {
      jQuery(this).submitHistorialPDF();
    }
    return false;
  });

  /**Combo de persona física y jurídica**/
  jQuery('#tipo_persona').on('change', function() {

    var id = jQuery(this).val();
    jQuery('#tipo_persona option').each(function() {
      jQuery(this).attr('selected', false);
      if (jQuery(this).val() == id) {
        jQuery(this).attr('selected', true);
        this.selected = this.defaultSelected; //firefox
      }
    });
    var val = jQuery(this).val();
    var cuit = jQuery('#cuit').val();
    if (val == 'F') { //pf
      var sexoActual = jQuery('#sexo_actual').val();
      jQuery('#sexo_persona_fisica').show();
      //seteo el ancho porque estaba oculto
      jQuery('#mujer').attr('width', '100%')
      jQuery('#mujer').focus();
      if (sexoActual !== "" && sexoActual !== undefined && sexoActual !== "S") {
        jQuery("input[name=sexo_persona][value='" + sexoActual + "']").prop("checked", true);
      }

      jQuery('#datos_persona_fisica').show();
      jQuery('#div_pers_jur').hide();
      jQuery("#hombre").prop("checked", true);
      jQuery('#tipo_persona_juridica').hide();
    } else { //pj
      jQuery('#sexo_persona_fisica').hide();
      jQuery("input:radio").removeAttr("checked");
      var tipoSocActual = jQuery('#tipo_soc_actual').val();
      if (tipoSocActual !== undefined) {
        jQuery('#tipo_sociedad').val(tipoSocActual);
      } else {
        jQuery("#tipo_sociedad option [value='SA']").prop("checked", true);
        jQuery('#tipo_sociedad').val("SA");
      }

      jQuery('#datos_persona_fisica').hide();
      jQuery('#div_pers_jur').show();
      jQuery('#tipo_persona_juridica').show();
      //seteo el ancho porque estaba oculto
      jQuery('#tipo_sociedad').attr('width', '100%');
      jQuery('#tipo_sociedad').val('ni');
      jQuery('#tipo_sociedad').focus();
    }
    jQuery('#cuit').valid();
  });

  //radio button sexo
  jQuery("#hombre, #mujer").change(function() {
    jQuery('#cuit').valid();
  });

  //combo tipo de sociedad
  jQuery('#tipo_sociedad').on('change', function() {
    var id = jQuery(this).val();
    jQuery('#tipo_sociedad option').each(function() {
      jQuery(this).attr('selected', false);
      if (jQuery(this).val() == id) {
        jQuery(this).attr('selected', true);
        this.selected = this.defaultSelected; //firefox
      }
    });
  });

  jQuery('#tipo_situacion').on('change', function() {
    var id = jQuery(this).val();
    jQuery('#tipo_situacion option').each(function() {
      jQuery(this).attr('selected', false);
      if (jQuery(this).val() == id) {
        jQuery(this).attr('selected', true);
        this.selected = this.defaultSelected; //firefox
      }
    });
  });

}); //fin document.Ready


function comparar() {
  var val = jQuery('#id_est_t').val();
  var tt = jQuery('#id_tipo_tramite').val();
  var np = jQuery('#nuevo_permiso').val();
  var pecomplet = jQuery('#planCompleto').val();
  var current_location = jQuery(location).attr('pathname');

  if ((val >= 2 && val < 6 || val == 10) && np == 0) {
    jQuery('#btnes_car_sol').show();
  }
  if (val >= 0 && val < 7 && tt == 1 && pecomplet == 1) {
    jQuery('#div_btn_plan_pdf').show();
    jQuery('#div_btn_eval_dom').show();
  }

  if (val >= 0 && val < 7) {
    jQuery('#div_btn_modificar_tramite').show();
  }

  if (current_location.indexOf('detalle-tramite-nro-seguimiento') >= 0) {
    if (val == 4 || val >= 6) {
      jQuery('#div_adjuntos').hide();
    }
  } else {
    if (val >= 6) {
      jQuery('#div_adjuntos').hide();
    }
  }


}

function plan_pdf() {
  url = 'plan-pdf';
  window.open(url, '_blank');
}

function historial_pdf() {
  url = 'historial-pdf';
  window.open(url, '_blank');
}
