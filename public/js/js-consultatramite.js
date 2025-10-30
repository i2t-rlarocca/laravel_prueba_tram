jQuery.noConflict();
jQuery(document).ready(function() {
	
  var errorStates = [];

  jQuery.validator.addMethod('trioDate', function(value, element) {
    dateDDMMYYYRegex = /^(0[1-9]|[12][0-9]|3[01])[- \/.](0[1-9]|1[012])[- \/.](19|20)\d\d$/;
    return this.optional(element) || value.match(dateDDMMYYYRegex);
  });

  jQuery.validator.addMethod('numerico', function(value, element) {
    numeros = /^([0-9])*$/;
    return this.optional(element) || value.match(numeros) || element == '[]';
  });

  jQuery.validator.addMethod("alfanumerico", function(value, element) {
    //letrasnumeros = /^\w+$/i;
    letrasnumeros = /^[a-z0-9\-\s]+$/i;
    return this.optional(element) || value.match(letrasnumeros);
  });


  jQuery.fn.setEstados = function() {
    var cant2 = jQuery("#id_estados option").length;
    var str2 = "";
    var str2_env_rep = "";
    var cant_seleccionada2 = jQuery('#id_estados :selected').length;
    var todos2 = jQuery("#id_estados :nth(11)");

    jQuery("#id_estados option:selected").each(function() {
      str2 += jQuery(this).val() + ",";
      str2_env_rep += jQuery(this).text() + "','";
      var n2 = str2.indexOf(cant2 - 1);
      var deshabilitado = ["0", "1", "2"];

      /*
       En función de qué estado esta seleccionado activa/desactiva
       el combo de mesa_entrada
      */
      if (jQuery.inArray(jQuery(this).val(), deshabilitado) != -1) {
        jQuery('#mesa').css("display", "none");
        jQuery(this).setMesaEntrada(0);
      } else {
        //jQuery('#mesa').show();
        jQuery('#mesa').css("display", "inline");
      }

      if (n2 != -1 && n2 != 0) {
        jQuery("#id_estados option").removeAttr("selected");
        jQuery('#id_estados').prop('selectedIndex', cant2 - 1);
      }
    });

    if (cant_seleccionada2 == (cant2 - 1) && !todos2.is(':selected')) {
      jQuery("#id_estados option").removeAttr("selected");
      jQuery('#id_estados').prop('selectedIndex', cant2 - 1);
    }

    var n = str2_env_rep.length;
    var str2_env_rep = str2_env_rep.substring(0, n - 3);
    document.getElementById('id_hestadost').value = str2_env_rep;
    var n2 = str2.length;
    var str_env_val2 = str2.substring(0, n2 - 1);
    document.getElementById('id_hestadosv').value = str_env_val2;
  };

  jQuery.fn.setTramites = function() {
    var cant = jQuery("#id_tramites_c option").length;
    var str = "";
    var str_env_rep = "";
    var cant_seleccionada = jQuery('#id_tramites_c :selected').length;
    var todos = jQuery("#id_tramites_c :nth-child(14)"); //empieza en 0 - como son 10 trámites, para posicionarlo en "Todos" coloco 11

    jQuery("#id_tramites_c option:selected").each(function() {
      str += jQuery(this).val() + ",";
      str_env_rep += jQuery(this).text() + "','";
      var n1 = str.indexOf(cant - 1);

      /**Selecciona la opción todos si están todos los otros 
      seleccionados e incluyen o no a la opción "todos"
      **/
      //if (n1 != -1 && n1 != 0 && cant_seleccionada >= 10) { //7
      if (n1 != -1 && n1 != 0 && cant_seleccionada >= 13) { //7
        jQuery("#id_tramites_c option").removeAttr("selected");
        jQuery('#id_tramites_c').prop('selectedIndex', cant - 1);
      }
    });
    /**Selecciona la opción todos si están todos los otros 
        seleccionados e incluyen o no a la opción "todos"
        **/
    if (todos.is(':selected')) {
      jQuery("#id_tramites_c option").removeAttr("selected");
      jQuery('#id_tramites_c').prop('selectedIndex', cant - 1);
    }
    var n3 = str_env_rep.length;
    var str_env_rep = str_env_rep.substring(0, n3 - 3);
    document.getElementById('id_htramitest').value = str_env_rep;
    var n1 = str.length;
    var str_env_val = str.substring(0, n1 - 1);
    document.getElementById('id_htramitesv').value = str_env_val;
  };

  /**
  Para setear los estados seleccionados
  **/
  jQuery.fn.setEstadosSeleccionados = function(seleccionar) {
    for (var i = 0; i < seleccionar.length; i++) {
      var id = '#id_estados option[value=' + seleccionar[i] + ']';
      jQuery(id).prop('selected', 'selected');
      jQuery(id).selected = jQuery(id).defaultSelected; //firefox   
    };

  }
  /*
  Para setear manualmente dónde se ingresó (stafe-ros)
  */
  jQuery.fn.setMesaEntrada = function(seleccionar) {
    for (var i = 0; i < seleccionar.length; i++) {
      var id = '#combo_mesa_entrada option[value=' + seleccionar[i] + ']';
      jQuery(id).prop('selected', 'selected');
      jQuery(id).selected = jQuery(id).defaultSelected; //firefox    
    };

  }

  /*
  Cada vez que cambia el combo de mesa_entrada
  */
  jQuery("#combo_mesa_entrada").on("change", function() {

    var id = jQuery(this).val();

    jQuery('#combo_mesa_entrada option').each(function(event) {
      jQuery(this).attr('selected', false);
      if (jQuery(this).val() == id) {
        jQuery(this).attr('selected', true);
        this.selected = this.defaultSelected; //firefox
      }
    });
  });

  /*********Para la primera vez*****************************/
  jQuery(window).load(function() {

    var ddv = jQuery('#ddv_nro').val();

    switch (ddv) {

      //viene del botón de consulta-tramite
      case "0": //default
        var estados = ["0", "1", "2", "3", "4", "5", "6"];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(0);
        break;
      case "1": //iniciados-permisionarios
        var estados = ["0", "1", "2"];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(0);
        break;
      case "2": //falta docu
        var estados = ["5"];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(0);
        break;
      case "3": //recepcion-rosario
        var estados = ["3"];
        var mesa = ['2'];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(mesa);
        jQuery(this).setMesaEntrada(2);
        break;
      case "4": //recepcion-stafe
        var estados = ["3"];
        var mesa = ['1'];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(mesa);
        jQuery(this).setMesaEntrada(1);
        break;
      case "5": //habilitación <30
        var estados = ["4"];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(0);
        break;
      case "6": //habilitación 30-60
        var estados = ["4"];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(0);
        break;
      case "7": //habilitación >60
        var estados = ["4"];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(0);
        break;
      case "8": //firma vicepresidente
        var estados = ["6"];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(0);
        break;
      case "9": //aprobados pendientes informar
        var estados = ["7"];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(0);
        break;
      case "10":
        var estados = ["3", "4", "6"];
        jQuery(this).setEstadosSeleccionados(estados);
        jQuery(this).setMesaEntrada(0);
        break;

    }
    if (jQuery("#formulario-tramite").validate()) {
      jQuery(this).setTramites();
      jQuery(this).setEstados();
      jQuery('#formulario-tramite').submit();
    }

  });

  jQuery(document).mouseup(function() {
    jQuery(this).setTramites();
  });

  jQuery(document).mouseup(function() {
    jQuery(this).setEstados();
  });

  jQuery(document).on("keydown click", ".btn_admin_maq", function(e) {
    irAdministracionMaquina();
  });

  /**Función para iluminar la fila de la tabla seleccionada**/
  jQuery('#cuerpo').on('click', 'tr', function() {
    var estado = jQuery(this).find("td").eq(10).html();
    var activo1 = jQuery('#btn-imprimir-resolucion').is(":visible");
    var activo2 = jQuery('#btn-imprimir-caratula').is(":visible");
    var activo3 = jQuery('#btn-nota-solicitud').is(":visible");
    var nuevo_permiso = jQuery(this).children('td#nuevo_permiso').text();
    var detalle_completo = jQuery(this).children('td#detalle_completo').text();

    if (!jQuery(this).hasClass('highlighted')) { //si no está iluminado
      jQuery(this).siblings().removeClass('highlighted');
      jQuery(this).toggleClass('highlighted');
      document.getElementById('id_seleccionado').value = this.id;

      var tipo_tramite = jQuery(this).children('td#id_tipo_tramite').text();
      if (estado == 7) { //aprobado
        if (!activo1) { //no está el botón
           jQuery('#btn-imprimir-resolucion').show();
        }
        if (activo2) { //está el botón
          jQuery('#btn-imprimir-caratula').hide();
        }
        if (activo3) { //está el botón
          jQuery('#btn-nota-solicitud').hide();
        }
      } else if (estado != 0 && estado != 1 && estado != 6 && estado != 7 && estado != 8 && estado != 9 && estado != 101) { //en prep-en eval-iniciado-doc inc
        if (!activo2) { //no está el botón
          jQuery('#btn-imprimir-caratula').show();
        }
        if (activo1) { //está el botón
          jQuery('#btn-imprimir-resolucion').hide();
        }
        if (!activo3) { //está el botón
          jQuery('#btn-nota-solicitud').show();
        }
      } else { //0, 1, 6, 8, 9, 10

        if (activo1) { //está el botón
          jQuery('#btn-imprimir-resolucion').hide();
        }

        if (activo2) { //está el botón
          jQuery('#btn-imprimir-caratula').hide();
        }

        if (activo3) { //está el botón
          jQuery('#btn-nota-solicitud').hide();
        }
      }

      if (Number(nuevo_permiso) == 1) {
        jQuery('#btn-imprimir-caratula').hide();
        jQuery('#btn-nota-solicitud').hide();
        if (Number(detalle_completo) == 0) {
          jQuery('#btn_completar_tramite').show();
          jQuery('#btn-cargar-detalle').hide();
          jQuery('#btn-historial-tramite').hide();
        } else {
          jQuery('#btn_completar_tramite').hide();
          jQuery('#btn-cargar-detalle').show();
          jQuery('#btn-historial-tramite').show();
        }

        if (estado == 0) {
          jQuery('#btn-cancelar-tramite').show();
        } else {
          jQuery('#btn-cancelar-tramite').hide();
        }
      } else {
        jQuery('#btn_completar_tramite').hide();
        jQuery('#btn-cancelar-tramite').hide();
        jQuery('#btn-cargar-detalle').show();
        jQuery('#btn-historial-tramite').show();
      }

      if (document.getElementById('id_seleccionado_c') != null) { //seleccionado_c = cancelar
        document.getElementById('id_seleccionado_c').value = this.id;
      }
      document.getElementById('id_seleccionado_h').value = this.id; //seleccionado_h = historial

    } else {
      jQuery(this).toggleClass('highlighted');

      if (activo1 && activo2 && activo3) { //está el botón
        jQuery('#btn-imprimir-resolucion').hide();
        jQuery('#btn-imprimir-caratula').hide();
        jQuery('#btn-nota-solicitud').hide();
      }
      if (activo1) { //está el botón
        jQuery('#btn-imprimir-resolucion').hide();
      }
      if (activo2) { //está el botón
        jQuery('#btn-imprimir-caratula').hide();
      }
      if (activo3) { //está el botón
        jQuery('#btn-nota-solicitud').hide();
      }

      if (Number(nuevo_permiso) == 1) {
        if (Number(detalle_completo) == 0) {
          jQuery('#btn_completar_tramite').show();
          jQuery('#btn-cargar-detalle').hide();
          jQuery('#btn-historial-tramite').hide();
        } else {
          jQuery('#btn_completar_tramite').hide();
          jQuery('#btn-cargar-detalle').show();
          jQuery('#btn-historial-tramite').show();
        }

        if (estado == 0) {
          jQuery('#btn-cancelar-tramite').show();
        } else {
          jQuery('#btn-cancelar-tramite').hide();
        }

      } else {
        jQuery('#btn_completar_tramite').hide();
        jQuery('#btn-cancelar-tramite').hide();
        jQuery('#btn-cargar-detalle').show();
        jQuery('#btn-historial-tramite').show();
      }
      document.getElementById('id_seleccionado').value = "";
      if (document.getElementById('id_seleccionado_c') != null) {
        document.getElementById('id_seleccionado_c').value = "";
      }
      document.getElementById('id_seleccionado_h').value = "";
    }
  });



  jQuery('#btn_consultar').click(function() {
    //verifica si fecha desde es menor a fecha hasta
    var fd = jQuery('#fecha_desde').val();
    var fh = jQuery('#fecha_hasta').val();
    var parts_fd = fd.split("/");
    var parts_fh = fh.split("/");
    var fd2 = new Date(parts_fd[2], parts_fd[1] - 1, parts_fd[0]);
    var fh2 = new Date(parts_fh[2], parts_fh[1] - 1, parts_fh[0]);
    var fechaHoy = new Date();
    fechaHoy = fechaHoy.getDate() + '/' + fechaHoy.getMonth() + '/' + fechaHoy.getFullYear();
    if (fechaHoy < fd) {
      alert("Fecha desde no puede ser mayor a la fecha actual");
      return false;
    } else if (fd2 > fh2) {
      alert('Fecha hasta no puede ser menor a fecha desde');
      return false;
    } else {
      jQuery('#ddv_nro').val('0');
      jQuery('#press-btn').val('0');
      if (jQuery("#formulario-tramite").validate()) {
        jQuery('#formulario-tramite').submit();
      }

    }
  });




  jQuery("#formulario-tramite").validate({

    rules: {
      fecha_desde: {
        required: 'opt',
        trioDate: true
      },
      fecha_hasta: {
        required: 'opt',
        trioDate: true
      },
      permiso: {
        required: 'opt',
        numerico: true
      },
      agente: {
        required: 'opt',
        numerico: true
      },
      subagente: {
        required: 'opt',
        numerico: true
      },
      localidad: {
        required: 'opt',
        alfanumerico: true
      }
    },
    messages: {
      fecha_desde: {
        trioDate: "MM/DD/YYYY"
      },
      fecha_hasta: {
        trioDate: "MM/DD/YYYY"
      },
      permiso: {
        numerico: "Sólo números"
      },
      agente: {
        numerico: "Sólo números"
      },
      subagente: {
        numerico: "Sólo números"
      },
      localidad: {
        alfanumerico: "Sólo letras y números"
      }
    },
    highlight: function(element, errorClass) {
      if (jQuery.inArray(element, errorStates) == -1) {
        errorStates[errorStates.length] = element;
        jQuery(element).popover('show');
      }
    },
    unhighlight: function(element, errorClass, validClass) {
      if (jQuery.inArray(element, errorStates) != -1) {
        this.errorStates = jQuery.grep(errorStates, function(value) {
          return value != errorStates;
        });
        jQuery(element).popover('hide');
      }
    },
    errorPlacement: function(err, element) {
      err.hide();
    },

    submitHandler: function(form) {

      jQuery.ajax({

        type: jQuery('#formulario-tramite').attr('method'),
        url: jQuery('#formulario-tramite').attr('action'),
        dataType: 'json',
        data: jQuery('#formulario-tramite').serialize(),

        success: function(data) {

          if (jQuery("#solapa-toggle-vertical").hasClass("active")) {
            jQuery("#sidebar-wrapper-vertical").toggleClass("active");
            jQuery("#solapa-toggle-vertical").toggleClass("active");
          }
          jQuery('#errores').hide();
          jQuery('#id_seleccionado').val('');
          jQuery('#id_seleccionado_c').val('');
          jQuery('#id_seleccionado_h').val('');
          jQuery('#nro_pagina').val(data.pagina);
          jQuery('#nro_pagina_fp').val(0);
          jQuery("#cuerpo tr td").remove();
          jQuery("#pie_tabla tr td").remove();

          var grilla = '';
          for (var i = 0; i < data['tramites'].length; i++) {

            if (Number(data.tramites[i]["nuevo_permiso"]) == 1) {
              grilla += '<tr  class="nuevoPermiso" id=' + data.tramites[i]["nro_tramite"] + '>';
            } else if (Number(data.tramites[i]["id_tipo_tramite"]) == 6 || Number(data.tramites[i]["id_tipo_tramite"]) == 7) {
              grilla += '<tr  class="maquinas" id=' + data.tramites[i]["nro_tramite"] + '>';
            } else {
              grilla += '<tr  id=' + data.tramites[i]["nro_tramite"] + '>';
            }
            grilla += '<td class=" col-lg-2" id="nro_seguimiento">' + data.tramites[i]["nro_tramite"] + '</td>';
            grilla += '<td class=" col-lg-2" id="tipo_tramite">' + data.tramites[i]["titulo_tramite"] + '</td>';
            grilla += '<td class=" col-lg-2" id="fecha_inicio">' + data.tramites[i]["fecha"] + '</td>';
            grilla += '<td class=" col-lg-2" id="fecha_ultima_modif">' + data.tramites[i]["fecha_ultima_modificacion"] + '</td>';
            grilla += '<td class=" col-lg-2" id="estado">' + data.tramites[i]["descripcion_estado"] + '</td>';
            grilla += '<td class=" col-lg-2" id="ingreso">' + data.tramites[i]["ingreso"] + '</td>';
            if (Number(data.tramites[i]["nuevo_permiso"]) == 1) {
              grilla += '<td class=" col-lg-2" id="razon_social"></td>';
            } else {
              grilla += '<td class=" col-lg-2" id="razon_social">' + data.tramites[i]["razon_social"] + '</td>';
            }
            grilla += '<td class=" col-lg-2" id="permiso">' + data.tramites[i]["nro_permiso"] + '</td>';
            if (Number(data.tramites[i]["nuevo_permiso"]) == 1) {
              grilla += '<td class=" col-lg-2" id="agente"></td>';
              grilla += '<td class=" col-lg-2" id="subagente"></td>';
            } else {

              grilla += '<td class=" col-lg-2" id="agente">' + data.tramites[i]["agente"] + '</td>';
              grilla += '<td class=" col-lg-2" id="subagente">' + data.tramites[i]["subagente"] + '</td>';
            }
            grilla += '<td class="hidden" id = "id_estado">' + data.tramites[i]["id_estado_tramite"] + '</td>';
            grilla += '<td class="hidden" id = "id_tipo_tramite">' + data.tramites[i]["id_tipo_tramite"] + '</td>';
            grilla += '<td class="hidden" id = "nuevo_permiso">' + data.tramites[i]["nuevo_permiso"] + '</td>';
            grilla += '<td class="hidden" id = "detalle_completo">' + data.tramites[i]["detalle_completo"] + '</td>';
            grilla += '</tr>';
          }

          if (data['tramites'].length != 0) {
            var pie = '<td COLSPAN=9>'; //IE:7 CH=8
            pie += '<input type="button" id="btn-atras" name="btn-atras" value="<-" class="btn-primary" ">'
            pie += '<label for = "nro_pagina"> Pagina: </label>';
            if (data.cantidadPaginas == 0) {
              pie += '<input type="text" id = "nro_pagina_fp" value="0">';
            } else {
              pie += '<input type="text" id = "nro_pagina_fp" value="' + data.pagina + '">';
            }
            pie += '<label for = "total_paginas">de: ' + data.cantidadPaginas + ' </label>';
            pie += '<input type="hidden" id = "total_paginas" value="' + data.cantidadPaginas + '">';
            pie += '<input type="button" id="btn-adelante" name="btn-adelante" value="->" class="btn-primary" ></td></tr>'
            jQuery('#pie_tabla').html('<tr>' + pie);

            //evento del input creado dinámicamente
            //cuando el campo de nº de página se presiona una tecla o pierde el foco
            jQuery('input#nro_pagina_fp').on('keydown focusout', function(e) {
              if (e.keyCode == 13) {
                var nro_pagina_fp = jQuery('#nro_pagina_fp').val();
                var cantidad_paginas = jQuery('#total_paginas').val();

                if (Number(nro_pagina_fp) <= Number(cantidad_paginas) && nro_pagina_fp > 0) { //antes !=0
                  jQuery('#nro_pagina').val(Number(nro_pagina_fp));
                  jQuery('#press-btn').val(1);
                  jQuery('#formulario-tramite').submit();
                } else {
                  alert("No existe la página ingresada");
                  jQuery('#nro_pagina_fp').val(jQuery('#nro_pagina').val());
                  return false;
                }
              }
            });



            //evento de las flechas creadas dinámicamente
            jQuery('input#btn-atras').click(function(e) {
              var nro_pagina_fp = jQuery('#nro_pagina_fp').val();
              var nro_pagina = jQuery('#nro_pagina').val();
              if (Number(nro_pagina_fp) <= 1 || nro_pagina == '') {
                jQuery("input#btn-atras").prop('disabled', 'disabled');
              } else {
                jQuery('#nro_pagina_fp').val(parseInt(jQuery('#nro_pagina_fp').val()) - 1);
                jQuery('#nro_pagina').val(parseInt(jQuery('#nro_pagina_fp').val()));
                jQuery('#press-btn').val(1);
                jQuery('#formulario-tramite').submit();
              }
            });

            jQuery('input#btn-adelante').click(function(e) {
              var nro_pagina_fp = jQuery('#nro_pagina_fp').val();
              var cantidad_paginas = jQuery('#total_paginas').val();
              var nro_pagina = jQuery('#nro_pagina').val();
              if (Number(nro_pagina_fp) >= Number(cantidad_paginas) || nro_pagina == '') {
                jQuery("input#btn-adelante").prop('disabled', 'disabled');
              } else {
                jQuery('#nro_pagina_fp').val(parseInt(jQuery('#nro_pagina_fp').val()) + 1);
                jQuery('#nro_pagina').val(parseInt(jQuery('#nro_pagina_fp').val()));
                jQuery('#press-btn').val(1);
                jQuery('#formulario-tramite').submit();
              }
            });

            //agrego botón para csv
            jQuery('#btn-csv').css('visibility', 'visible');
            //agrego botón para imprimir
            jQuery('#btn-imprimir').css('visibility', 'visible');

          } else {
            jQuery('#mensaje').text(data.mensaje);
            var csv = jQuery('#btn-csv').css('visibility');
            if (csv == "visible") {
              jQuery('#btn-csv').css('visibility', 'hidden');
            }
            var imprimir = jQuery('#btn-imprimir').css('visibility');
            if (imprimir == "visible") {
              jQuery('#btn-imprimir').css('visibility', 'hidden');
            }
            var imprimir_resolucion = jQuery('#btn-imprimir-resolucion').css('visibility');
            if (imprimir_resolucion == "visible") {
              jQuery('#btn-imprimir-resolucion').css('visibility', 'hidden');
            }
            jQuery('#errores').show();
            //jQuery("#tabla-tramites th").remove();

          } //fin if lenght mayor

          jQuery('#cuerpo').html('<tr>' + grilla);

        },
        error: function(data) {
          alert('error');
          refrescar();

          jQuery('#btn-csv').hide();
          jQuery('#btn-imprimir').hide();
          jQuery('#btn-imprimir-resolucion').hide();
          jQuery('#mensaje').text("Error al realizar la consulta.");
          jQuery('#errores').show();
        }
      });
      return false;
    }

  }); //fin validate


  /**Popover con los mensajes de error**/
  jQuery('#id_permiso').popover({
    trigger: "manual",
    placement: "top",
    content: "Solo Nº",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
  });
  jQuery('#id_agente').popover({
    trigger: "manual",
    placement: "top",
    content: "Solo Nº",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
  });
  jQuery('#id_subagente').popover({
    trigger: "manual",
    placement: "top",
    content: "Solo Nº",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
  });




  /*****Funciones para página de consulta por nº de trámite***************************************/
  //verifica si colocó nº de seguimiento
  jQuery("#formulario-nroTramite").validate({
    errorElement: "error",
    rules: {
      ntramite: {
        required: true,
        number: true,
        maxlength: 20
      }
    },
    messages: {
      ntramite: {
        required: " Campo requerido: Nº seguimiento"
      }
    }

  });

  jQuery('#btn-iniciar').click(function() {
    var valido = jQuery("#formulario-nroTramite").validate().form();
    if (valido) {
      jQuery('#formulario-nroTramite').submit();
    }
  });



}); //fin document ready	


function irAdministracionMaquina() {
  window.location = 'administracion_maquinas';
}
