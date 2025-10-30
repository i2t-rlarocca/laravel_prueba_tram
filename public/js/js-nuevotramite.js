jQuery.noConflict();
jQuery(document).ready(function(){

  /**
   * Desactivar doble click en la página
   **/
  jQuery("*").dblclick(function(e){
    e.preventDefault();
  });

  /**
   * Formulario nuevo trámite - Externo
   **/
  jQuery.fn.submitFormulario = function(permiso, agente, subagente, tipoTramite) {

    jQuery.ajax({
      type: 'post',
      url: 'verificar_existencia_tramite',
      dataType:'json',
      data: {'permiso':permiso, 'agente':agente, 'subagente':subagente, 'tipo_tramite':tipoTramite},

      success: function (data) {
        if(data['mensaje']=="OK"){
          jQuery('#formulario_tramite').submit();
        }else{
          jQuery('#mensaje').text("El permiso ingresado ya tiene éste tipo de trámite en curso.");
          jQuery('#errores').show();
          jQuery('#errores').delay(2000).fadeOut();
          jQuery('#divagentesubage').css("display","none");
        }
      },

    });
  };

  /**
      No cualquier permiso puede realizar éste tipo de trámite
      */
  jQuery.fn.controlDependencia = function(seleccionado){
    var permiso =jQuery('#nro_permiso').val();
    var agente =jQuery('#agente').val();
    var subagente =jQuery('#subagente').val();


    if(seleccionado=="4" ){//dependencia - categoria
      if(subagente==="0"){
        jQuery('#mensaje').text("El permiso ingresado no puede realizar éste tipo de trámite.");
        jQuery('#errores').show();
        jQuery('#errores').delay(2000).fadeOut();
        jQuery('#divagentesubage').css("display","none");
        jQuery('#id_permiso_nt').focus();
      }else{
        jQuery(this).submitFormulario(permiso, agente, subagente, seleccionado);
      }
    }else{
      jQuery(this).submitFormulario(permiso, agente, subagente, seleccionado);
    }

  };

  /**
      No es posible solicitar más de una vez la baja de un permiso
      */
  jQuery.fn.controlBajaPermiso = function(permiso){
    jQuery.ajax({
      type:'POST',
      url: 'control-baja-permiso',
      dataType:'json',
      data:{'permiso':permiso},
      success: function(data){
        if(data.exito){
          if(!alertify.confirmBajaPermiso){
            alertify.dialog('confirmBajaPermiso',function factory(){
              return{
                build:function(){
                  var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                    + '</span> Confirmación de baja de permiso';
                  this.setHeader(modifyHeader);
                }
              };
            },false,'confirm');
          }
          alertify.confirmBajaPermiso('¿Está seguro de que quiere dar de baja el permiso: '+permiso+'?')
            .set({
              'labels':{ok:'Si', cancel:'No'},
              'onok': function(){
                jQuery('#bajaModal').removeData("modal").modal({keyboard: false, backdrop:'static' });
                jQuery('#bajaModal').modal('show');
              },
              'oncancel': function(){ alertify.error('No se realizó ninguna acción.',1); }
            }).set('defaultFocus','cancel').show();
        }else{
          alertify.error('El permiso ya tiene una solicitud de baja.', 2);
        }
      }
    });

  };

  /**
      No es posible solicitar más de una vez la baja de un permiso
      */
  jQuery.fn.controlRenunciaPermiso = function(permiso){
    jQuery.ajax({
      type:'POST',
      url: 'control-renuncia-permiso',
      dataType:'json',
      data:{'permiso':permiso},
      success: function(data){
        if(data.exito){
          if(!alertify.confirmBajaPermiso){
            alertify.dialog('confirmBajaPermiso',function factory(){
              return{
                build:function(){
                  var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                    + '</span> Confirmación de renuncia permiso';
                  this.setHeader(modifyHeader);
                }
              };
            },false,'confirm');
          }
          alertify.confirmBajaPermiso('¿Está seguro de que quiere renunciar al permiso: '+permiso+'?')
            .set({
              'labels':{ok:'Si', cancel:'No'},
              'onok': function(){
                jQuery('#renunciaModal').removeData("modal").modal({keyboard: false, backdrop:'static' });
                jQuery('#renunciaModal').modal('show');
              },
              'oncancel': function(){ alertify.error('No se realizó ninguna acción.',1); }
            }).set('defaultFocus','cancel').show();
        }else{
          alertify.error('El permiso ya tiene una solicitud de renuncia.', 2);
        }
      }
    });

  };


  jQuery('#btn_nuevo_agente').click(function(){
    var seleccionado = jQuery('#id_tramites_agente option:selected').val();
    var permiso = jQuery('#nro_permiso').val();

    resolve_selection(seleccionado, permiso);
    return false;
  });


  /*Evento para cdo el select cambia de valor*/
  jQuery( "select" ).change(function () {
    var id =jQuery(this).val();
    jQuery(this).find('option').each(function(){
      jQuery(this).attr('selected',false);
      if(jQuery(this).val() == id){
        jQuery(this).attr('selected',true);
        this.selected = this.defaultSelected;//firefox
      }
    });
  }).change();


  jQuery('#btn_nuevo').on('keydown click', function(){
    var seleccionado = jQuery('#id_tramites option:selected').val();
    var permiso = jQuery('#nro_permiso').val();

    resolve_selection(seleccionado, permiso);
    return false;
  });

  function resolve_selection(seleccionado, permiso){
    if(seleccionado==5){//baja permiso
      jQuery(this).controlSubAgenciasActivas(permiso, 5);
    }else if(seleccionado=="6"){//incor maq
      jQuery().buscarTiposTerminal();
    }else if(seleccionado=="7"){// ret maq
      jQuery().buscarMaquinasPermiso(permiso);
    }else if(seleccionado=="9"){// hab permiso
      jQuery().controlHabilitacionPermiso(permiso);
    }else if(seleccionado=="10"){// susp permiso
      jQuery().controlSubAgenciasActivas(permiso, 10);
    }else if(seleccionado=="13"){// susp permiso
      jQuery().controlSubAgenciasActivas(permiso, 13);
    }else{
      jQuery(this).controlDependencia(seleccionado);
    }
  }

  /**
   * llama para inciar solicitud baja
   **/
  jQuery.fn.submitBajaPermiso = function(permiso, motivo, observaciones, agente, subagente) {

    jQuery.ajax({
      type: 'post',
      url: 'baja-permiso',
      async:true,
      dataType:'json',
      data: {'permiso':permiso, 'motivo':motivo, 'observaciones':observaciones, 'agente':agente, 'subagente':subagente},
      beforeSend: function(){
        jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
        jQuery('#cargandoModal').modal('show');
        jQuery("#btn_sol_baja").prop("disabled",true);
      },
      complete:function(){
        jQuery('#cargandoModal').modal('hide');
        jQuery("#btn_sol_baja").prop("disabled",false);
      },
      success: function (data) {
        if(data.exito){
          alertify.success('Se inició la solicitud de baja para el permiso: '+ permiso,2);
        }else{
          alertify.error('No se pudo iniciar la solicitud de baja.',2);
        }
        return false;
      },
      error:function(){
        alertify.error('Ocurrió un problema al inciar la solicitud de baja.',2);
      }

    });
    return false;
  };


  /**
   * llama para inciar solicitud renuncia
   **/
  jQuery.fn.submitRenunciaPermiso = function(permiso, motivo, observaciones, agente, subagente) {

    jQuery.ajax({
                type: 'post',
                url: 'renuncia',
                async:true,
                dataType:'json',
                data: {'permiso':permiso, 'motivo':motivo, 'observaciones':observaciones, 'agente':agente, 'subagente':subagente},
                beforeSend: function(){
                    jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
                    jQuery('#cargandoModal').modal('show');
                    jQuery("#btn_sol_renuncia").prop("disabled",true);
                },
                complete:function(){
                    jQuery('#cargandoModal').modal('hide');
                    jQuery("#btn_sol_renuncia").prop("disabled",false);
                },
                success: function (data) {
                    if(data.exito){
						alertify.success('Se inició la solicitud de renuncia para el permiso: '+ permiso,4);
						setTimeout(function(){
							// window.location.href = 'consulta-tramite'
							window.location.href = 'tramites'
						}, 2000);
					  
                    }else{
                      alertify.error('No se pudo iniciar la solicitud de renuncia.',3);
                    }
                    return false;
                },
                error:function(){
                  alertify.error('Ocurrió un problema al inciar la solicitud de renuncia.',3);
                }

    });
    return false;
  };
  /**********Admin Máquinas *************/

  /**
   * Formulario admin máquinas
   **/
  jQuery.fn.formularioMaquina = function(
    permiso,
    agente,
    subagente,
    tipoTramite,
    url_accion,
    dato,
    observaciones,
	telefono,
    definitivo
  ) {
    jQuery.ajax({
      type: 'post',
      url: url_accion,
      dataType: 'json',
      data: {
        'permiso': permiso,
        'agente': agente,
        'subagente': subagente,
        'tipo_tramite': tipoTramite,
        'dato': dato,
        'observaciones': observaciones,
        'telefono': telefono,
        'definitivo': definitivo
      },
      async: false,
      beforeSend: function() {
        jQuery('#btn_incorporar').prop("disabled", true);
      },
      complete: function() {
        jQuery('#btn_incorporar').prop("disabled", false);
        jQuery('#cargandoModal').modal('hide');
      },
      success: function(data) {
        if (data.exito) {
          alertify.success('Se generó el pedido.', 5);
        } else {
          alertify.error(data.mensaje, 3);
        }
      },

    });
  };

  /**
   * busca los tipos de terminal a incorporar
   **/
  jQuery.fn.buscarTiposTerminal = function() {

    jQuery.ajax({
      type: 'post',
      url: 'buscar_tipos_terminal',
      dataType:'json',
      async:false,
      //data: {'permiso':permiso, 'agente':agente, 'subagente':subagente, 'tipo_tramite':tipoTramite},
      success: function (data) {
        if(jQuery('#tipo_terminal').length==0){
          var body = '<h3>Tipo de terminal:</h3>'
            +'<select id="tipo_terminal" class="form-control col-xs-3 col-lg-3">';
          jQuery.each( data.tipos, function( key, val ) {
            body+='<option value='+key+'>'+val+'</option>';
          });

          body+='</select> </br>';

          body+=' <div class="form-group">';
          body+='    <div id="label2">';
          body+='      <h3>Motivo:</h3>';
          body+='    </div>';
          body+='    <div id="div_obs_maq">';
          body+=' <textarea id="observaciones" class="form-control input-medium" rows="5" maxlength="255"> </textarea>';
          body+='    </div>';
          body+='</div>';

          jQuery('#terminalModal').find('.modal-body').html(body);
        }

        jQuery('#terminalModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
        jQuery('#terminalModal').modal('show');
        return false;
      },

    });
    return false;
  };

  /**
   * busca los tipos de terminal incorporados al permiso
   * para retirar
   **/
  jQuery.fn.buscarMaquinasPermiso = function(permiso) {
    jQuery.ajax({
      type: 'post',
      url: 'buscar_terminal',
      async: false,
      dataType: 'json',
      data: {
        'permiso': permiso
      },
      success: function(data) {
        if (data.lista != null) {
          let body = '<h3>Terminal:</h3>';

          //selector tipo terminal
          body += '<select id="terminales" class="form-control col-xs-3 col-lg-3">';
          jQuery.each(data.lista, function(key, val) {
            body += '<option value="' + key + '">' + val[1] + '</option>';
          });
          body += '</select></br>';

          //mostrar cantidad disponible
          body += '<label for="maq_cant">Cantidad disponible:</label>';
          body += '<input id="maq_cant" readonly></input><br>';
          let onchange_fun = '<script type="text/javascript">\n';
          onchange_fun += 'let datos = ' + JSON.stringify(data.lista) + ';\n';
          onchange_fun += 'jQuery("#terminales").change(function(){\n';
          onchange_fun += '  jQuery("#maq_cant").val(datos[this.value][0]);\n';
          onchange_fun += '});\n';
          onchange_fun += '</script>\n';

          console.log("script injertado:\n", onchange_fun);
          body += onchange_fun;

          //definitivo/provisorio
          body += '<input type="radio" name="definitivo_provisorio" value="Provisorio" checked>';
          body += '<label>Provisorio</label><br>';
          body += '<input type="radio" name="definitivo_provisorio" value="Definitivo">';
          body += '<label>Definitivo</label><br>';

		   //mostrar telefono
		  let body2="";
          body2 += '<label for="maq_cant">Teléfono:</label>';
          body2 += '<input  type="number" id="maq_telefono"  name="maq_telefono"></input><br>';
        
          body += body2;
		  
          //observaciones
          body += ' <div class="form-group">';
          body += '    <div id="label2">';
          body += '      <h3>Motivo:</h3>';
          body += '    </div>';
          body += '    <div id="div_obs_maq">';
          body += ' <textarea id="observaciones_ret" class="form-control input-medium" rows="5" maxlength="255"></textarea>';
          body += '    </div>';
          body += '</div>';

          jQuery('#terminalRetiroModal').find('.modal-body').html(body);
          jQuery('#terminalRetiroModal').removeData("modal").modal({
            backdrop: 'static',
            keyboard: false
          });
          jQuery('#terminalRetiroModal').modal('show');
          return false;
        } else {
          alertify.error('No hay terminales para retirar', 2);
        }
      },
      error: function(xhr, status, error) {
        alert(xhr.responseText);
      }
    });
    return false;
  };

  /**Para cuando el modal se hace visible al usuario**/
  jQuery('#terminalModal').on('shown.bs.modal', function(){
    jQuery('#tipo_maquina').val('');
    jQuery('#observaciones_maq').val('');
    jQuery('#observaciones').val('');
    /*Evento para cdo el select cambia de valor*/
    jQuery( "select" ).change(function () {
      var id =jQuery(this).val();
      jQuery(this).find('option').each(function(){
        jQuery(this).attr('selected',false);
        if(jQuery(this).val() == id){
          jQuery(this).attr('selected',true);
          this.selected = this.defaultSelected;//firefox
        }
      });
    }).change();

    jQuery('#btn_incorporar').on('keydown click', function(e){
      let observaciones = jQuery('#observaciones').val();
      if(observaciones == undefined || observaciones == null || observaciones == ""){
        alertify.error('Debe completar las observaciones', 2);
        return;
      }

      if(!alertify.confirmIncorpMaquina){
        alertify.dialog('confirmIncorpMaquina',function factory(){
          return{
            build:function(){
              var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                + '</span> Confirmación de incorporación de máquina';
              this.setHeader(modifyHeader);
            }
          };
        },false,'confirm');
      }
      alertify.confirmIncorpMaquina('¿Está seguro que quiere incorporar éste tipo de terminal?')
        .set({
          'labels':{ok:'Si', cancel:'No'},
          'onok': function(closeEvent){
            var tt=jQuery('#tipo_terminal').val();
            var obmaq=jQuery('#observaciones').val();

            jQuery('#tipo_maquina').val(tt);
            jQuery('#observaciones_maq').val(obmaq);

            var tipo_maquina = jQuery('#tipo_terminal').val();
            var permiso = jQuery('#nro_permiso').val();
            var agente = jQuery('#agente').val();
            var subagente = jQuery('#subagente').val();
            var observaciones = jQuery('#observaciones').val();
            if(tipo_maquina!=''){
              jQuery(this).formularioMaquina(
                permiso,
                agente,
                subagente,
                'incorporar',
                'incorporar-maquina',
                tipo_maquina,
                observaciones,
				'-',
				'-'
              );
            }
            jQuery('#terminalModal').modal('hide');
            jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
            jQuery('#cargandoModal').modal('show');
            jQuery('#cargandoModal').modal('hide');
          },
          'oncancel': function(closeEvent){
            jQuery('#tipo_maquina').val('');
            jQuery('#observaciones_maq').val('');
          }
        }).set('defaultFocus','cancel').show();
    });
  });

  /* 09-04-2021 MM: Se comenta este cñodigo ya que dispara el submit por aceptar y por cancelar provocando duplicados de tramites*/
  /*
    jQuery('#terminalModal').on('hidden.bs.modal', function(){
      var tipo_maquina = jQuery('#tipo_maquina').val();
      var permiso =jQuery('#nro_permiso').val();
      var agente =jQuery('#agente').val();
      var subagente =jQuery('#subagente').val();
      var observaciones =jQuery('#observaciones_maq').val();
      jQuery('#observaciones').val('');
      if(tipo_maquina!=''){
        jQuery(this).formularioMaquina(permiso, agente, subagente,'incorporar','incorporar-maquina',tipo_maquina, observaciones);
      }
   });
   */

  /***************************FIN INCORPORACIÓN MÁQUINAS***********************/
  /***************************RETIRO MÁQUINAS**************************/
  /**Para cuando el modal se hace visible al usuario**/

  jQuery('#terminalRetiroModal').on('shown.bs.modal', function(){
    jQuery('#tipo_maquina').val('');
    jQuery('#observaciones_ret').val('');

    /*Evento para cdo el select cambia de valor*/
    jQuery( "select" ).change(function () {
      var id =jQuery(this).val();
      jQuery(this).find('option').each(function(){
        jQuery(this).attr('selected',false);
        if(jQuery(this).val() == id){
          jQuery(this).attr('selected',true);
          this.selected = this.defaultSelected;//firefox
        }
      });
    }).change();

    jQuery('#btn_retirar').on('keydown click', function(e){
      let observaciones = jQuery('#observaciones_ret').val();
      if(observaciones == undefined || observaciones == null || observaciones == ""){
        alertify.error('Debe completar las observaciones', 2);
        return;
      }
	  
	  let mtelefono = jQuery('#maq_telefono').val();
      if(mtelefono == undefined || mtelefono == null || mtelefono == ""){
        alertify.error('Debe completar el teléfono', 2);
        return;
      }
	  if(mtelefono.length < 5){
        alertify.error('El teléfono debe tener mínimo 5 caracteres', 2);
        return;
      }
	  
	  

      if(!alertify.confirmRetiroMaquina){
        alertify.dialog('confirmRetiroMaquina',function factory(){
          return{
            build:function(){
              var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                + '</span> Confirmación de retiro de máquina';
              this.setHeader(modifyHeader);
            }
          };
        },false,'confirm');
      }
      let tipo_maquina_text = jQuery( "#terminales option:selected" ).text();
      let definitivo_provisorio = jQuery('input[name=definitivo_provisorio]:checked').val();
      alertify.confirmRetiroMaquina(
        "¿Está seguro que quiere retirar <i>UNA</i> máquina de tipo <i>" + tipo_maquina_text + "</i>"
        + " en carácter <i>" + definitivo_provisorio + "</i>?"
        + '</br>Recuerde que debe realizar un trámite por cada máquina que desee retirar.'
      )
        .set({
          'labels':{ok:'Si', cancel:'No'},
          'onok': function(){
            let tt = jQuery('#terminales').val();
            let obmaq = jQuery('#observaciones_ret').val();

            jQuery('#tipo_maquina').val(tt);
            jQuery('#observaciones_maq').val(obmaq);

            let tipo_maquina = jQuery('#terminales').val();
            let permiso = jQuery('#nro_permiso').val();
            let agente = jQuery('#agente').val();
            let subagente = jQuery('#subagente').val();
            let telefono = jQuery('#maq_telefono').val();
            let observaciones = jQuery('#observaciones_ret').val();

            if(tipo_maquina!='' && telefono!=''){
              jQuery(this).formularioMaquina(
                permiso,
                agente,
                subagente,
                'retirar',
                'retirar-maquina',
                tipo_maquina,
                observaciones,
				telefono,
                definitivo_provisorio == 'Definitivo'
              );
            }
            jQuery('#observaciones_ret').val('');
            jQuery('#terminalRetiroModal').modal('hide');
            jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
            jQuery('#cargandoModal').modal('show');
            jQuery('#cargandoModal').modal('hide');
          },
          'oncancel': function(){ jQuery('#maq_telefono').val(''); jQuery('#tipo_maquina').val(''); jQuery('observaciones_maq').val('');}
        }).set('defaultFocus','cancel').show();
    });
  });

  /*09-04-2021 MM: se cometa ya que al presionar "cancelar tambien se envía"*/
  /*Cuando el modal retiro se cierra*/
  /*
    jQuery('#terminalRetiroModal').on('hidden.bs.modal', function(){
        var tipo_maquina = jQuery('#tipo_maquina').val();
        var permiso =jQuery('#nro_permiso').val();
        var agente =jQuery('#agente').val();
        var subagente =jQuery('#subagente').val();
        var observaciones =jQuery('#observaciones_maq').val();
        jQuery('#observaciones_ret').val('');
        if(tipo_maquina!=''){
          jQuery(this).formularioMaquina(permiso, agente, subagente,'retirar','retirar-maquina',tipo_maquina, observaciones);
        }
    });
    */
  /************************FIN RETIRO MÁQUINAS***********************/

  /***************************BAJA PERMISO**********************************/
  jQuery('#bajaModal').on('show.bs.modal', function(){
    var listaMotivos = jQuery('#listaMotivos').val();
    var arregloMotivos = {};
    jQuery('.modal-body #id_motivos').empty();

    jQuery.each(listaMotivos.split(",").slice(0,-1), function(index, item) {
      var motivo=item.split(":");
      var clave= motivo[0];
      var valor= motivo[1];
      jQuery('.modal-body #id_motivos').append(jQuery('<option>', {value:clave, text:valor}));
    });

    jQuery('.modal-body #observaciones_baja').val('');
  });

  jQuery('#bajaModal').find('#btn_sol_baja').on('keydown click', function(e){
    var observaciones=jQuery('#observaciones_baja').val();
    var motivo=jQuery('#id_motivos').val();
    if(observaciones!=''){
      jQuery('#observaciones_baja').val(observaciones);
      jQuery('#motivo_baja').val(motivo);

      var motivo_i = jQuery('#id_motivos option:selected').val();
      var observaciones_i = jQuery('#observaciones_baja').val();
      var permiso =jQuery('#nro_permiso').val();
      var agente_i =jQuery('#agente').val();
      var subagente_i=jQuery('#subagente').val();
      jQuery().submitBajaPermiso(permiso, motivo_i, observaciones_i,agente_i,subagente_i);

      jQuery('#bajaModal').modal('hide');
    }else{
      alertify.error('Debe completar las observaciones.',1);
    }
  });

  jQuery('#renunciaModal').find('#btn_sol_renuncia').on('keydown click', function(e){
    var observaciones=jQuery('#observaciones_renuncia').val();
    var motivo=jQuery('#id_motivos_renuncia').val();
    // alert(observaciones+" "+motivo);
    if(observaciones!=''){
      jQuery('#observaciones_renuncia').val(observaciones);
      jQuery('#id_motivos_renuncia').val(motivo);

      var motivo_i = jQuery('#motivo_ren').val();
      var observaciones_i = jQuery('#observaciones_renuncia').val();
      var permiso =jQuery('#nro_permiso').val();
      var agente_i =jQuery('#agente').val();
      var subagente_i=jQuery('#subagente').val();
      jQuery().submitRenunciaPermiso(permiso, motivo_i, observaciones_i,agente_i,subagente_i);

      jQuery('#renunciaModal').modal('hide');
    }else{
      alertify.error('Debe completar las observaciones..',1);
    }
  });

  /* 09-04-2021 MM: se comenta el siguiente codigo ya que se debe disparar el submit solo cuando da en el boton aceptar y no por cancelar. */

  /*Cuando el modal incorporación se cierra*/
  /*
    jQuery('#bajaModal').on('hidden.bs.modal', function(){
        var motivo = jQuery('#id_motivos option:selected').val();
        var observaciones = jQuery('#observaciones_baja').val();
        var permiso =jQuery('#nro_permiso').val();

        if(observaciones!=''){
          jQuery().submitBajaPermiso(permiso, motivo, observaciones);
        }
    });
    */
  /***************************SUSPENSIÓN PERMISO**********************************/
  /**
      No es posible solicitar más de una vez la suspensión de un permiso
      */
  jQuery.fn.controlSuspensionPermiso = function(permiso){
    jQuery.ajax({
      type:'POST',
      url: 'control-suspender-permiso',
      dataType:'json',
      data:{'permiso':permiso},
      success: function(data){
        if(data.exito){
          if(!alertify.confirmSuspenderPermiso){
            alertify.dialog('confirmSuspenderPermiso',function factory(){
              return{
                build:function(){
                  var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                    + '</span> Confirmación de suspensión de permiso';
                  this.setHeader(modifyHeader);
                }
              };
            },false,'confirm');
          }
          alertify.confirmSuspenderPermiso('¿Está seguro de que quiere suspender el permiso: '+permiso+'?')
            .set({
              'labels':{ok:'Si', cancel:'No'},
              'onok': function(){
                jQuery('#suspensionModal').removeData("modal").modal({keyboard: false, backdrop:'static' });
                jQuery('#suspensionModal').modal('show');
              },
              'oncancel': function(){ alertify.error('No se realizó ninguna acción.',1); }
            }).set('defaultFocus','cancel').show();
        }else{
          alertify.error('El permiso ya tiene una solicitud de suspensión.', 3);
        }
      }
    });

  };

  jQuery('#suspensionModal').on('show.bs.modal', function(){
    var hoy = new Date();
	var d = JSON.stringify(hoy.getDate());
	var m = JSON.stringify(hoy.getMonth());
	// alert(JSON.stringify(hoy.getDate()).length);
	// alert(JSON.stringify(hoy.getMonth()).length+1);
    //convertir día y mes a 2 digitos
    var dia = (JSON.stringify(hoy.getDate()).length == 1)? ('0'+JSON.stringify(hoy.getDate())) : (JSON.stringify(hoy.getDate()));
    var mes = (JSON.stringify(hoy.getMonth()+1).length == 1)? ('0'+JSON.stringify(hoy.getMonth()+1)) :(JSON.stringify(hoy.getMonth()+1));

    jQuery('#fecha_hasta').val(dia+'/'+mes+'/'+JSON.stringify(hoy.getFullYear()));
    jQuery('.modal-body #observaciones_susp').val('');
  });

  jQuery('#suspensionModal').find('#btn_sol_susp').on('keydown click', function(e){
    var observaciones=jQuery('#observaciones_susp').val();
    var valido = jQuery("#formulario_suspension_permiso").validate().form();
    if (valido == true){
      jQuery('#observaciones_susp').val(observaciones);


      var fechahasta = jQuery('#fecha_hasta').val();
      var observaciones = jQuery('#observaciones_susp').val();
      var permiso =jQuery('#nro_permiso').val();
      var agente =jQuery('#agente').val();
      var subagente =jQuery('#subagente').val();
      var titular =jQuery('#titular').val();
      if(observaciones!=''){
        jQuery().submitSuspensionPermiso(permiso,agente,subagente,titular, fechahasta, observaciones);
      }

      jQuery('#suspensionModal').modal('hide');
      jQuery('#id_permiso_nt').val('');
    }else{
      alertify.error('Campos con formato incorrecto o sin completar.',2);
    }
  });


  /*Cuando el modal suspensión se cierra*/

  /*
  MM - 09-04-2021 : se comenta este codigo ya que al cerrar el modal no debe hacer nada. esto produce que al cancelar el trámite creara de todas maneras!
  */


  /*
    jQuery('#suspensionModal').on('hidden.bs.modal', function(){
        var fechahasta = jQuery('#fecha_hasta').val();
        var observaciones = jQuery('#observaciones_susp').val();
        var permiso =jQuery('#nro_permiso').val();
        var agente =jQuery('#agente').val();
        var subagente =jQuery('#subagente').val();

        if(observaciones!=''){
          jQuery().submitSuspensionPermiso(permiso,agente,subagente, fechahasta, observaciones);
        }
    });
    */
  /**
   * llama para inciar solicitud suspensión
   **/
  jQuery.fn.submitSuspensionPermiso = function(permiso,agente,subagente,titular, fechahasta, observaciones) {

    jQuery.ajax({
      type: 'post',
      url: 'suspender-permiso',
      async:true,
      dataType:'json',
      data: {'permiso':permiso, 'agente':agente, 'subagente':subagente, 'titular':titular ,'fechahasta':fechahasta, 'observaciones':observaciones},
      beforeSend: function(){
        jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
        jQuery('#cargandoModal').modal('show');
        jQuery("#btn_sol_susp").prop("disabled",true);
      },
      complete:function(){
        jQuery('#cargandoModal').modal('hide');
        jQuery("#btn_sol_susp").prop("disabled",false);
      },
      success: function (data) {
        if(data.exito){
          //alertify.success('Se inició la solicitud de suspensión para el permiso: '+ permiso,2);
          alertify.alert('Suspensión Generada', '<h4><center>Se inició la solicitud de suspensión para el permiso: '+ permiso + '<br/>' + ' Nº Trámite: <font color="red">' + data.nroTramite + '</font></center></h4>');
        }else{
          alertify.error('No se pudo iniciar la solicitud de suspensión del permiso.',2);
        }
        return false;
      },
      error:function(){
        alertify.error('Ocurrió un problema al inciar la solicitud de suspensión.',2);
      }

    });

    return false;
  };

  jQuery.validator.addMethod("observaciones", function(value, element) {
    return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'-.,;:\/0-9º" "^!¡?¿@#$%&*()}{@#~]+$/i.test(value);
  });

 jQuery.validator.addMethod('fechavalidate', function(value, element){
      // return this.optional(element) || /\d{1,2}\/[0,1]{1}[1-9]{1}\/[1-9]{1}\d{3}/.test(value);
      // return this.optional(element) || /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(0[1-9]|1[1-9]|4[1-9])$/.test(value);
      return this.optional(element) || /^([0][1-9]|[12][0-9]|3[01])(\/|-)([0][1-9]|[1][0-2])\2(\d{4})$/.test(value);
    }, "Debe seleccionar una fecha.");

  jQuery("#formulario_suspension_permiso").validate({
      rules: {
            fecha_hasta: {required:true, fechavalidate:true},
            observaciones_susp: {required:true, observaciones:true}
            },
      messages: {
            fecha_hasta: {required:"Ingrese la fecha desde", fechavalidate:"El formato de la fecha es dd/mm/aaaa"},
            observaciones_susp: {required:"Debe ingresar observaciones para la suspensión."}
            },
      errorElement: "error",

    });
/*************************** FIN SUSPENSIÓN PERMISO**********************************/

/***************************HABILITACIÓN PERMISO**********************************/
    /**
      No es posible solicitar más de una vez la habilitación de un permiso
    */
   jQuery.fn.controlHabilitacionPermiso = function(permiso){
      jQuery.ajax({
          type:'POST',
          url: 'control-habilitar-permiso',
          dataType:'json',
          data:{'permiso':permiso},
          success: function(data){
            if(data.exito){
              if(!alertify.confirmHabilitarPermiso){
                alertify.dialog('confirmHabilitarPermiso',function factory(){
                    return{
                            build:function(){
                                var modifyHeader = '<span style="vertical-align:middle;color:#e10000;">'
                                + '</span> Confirmación de habilitación de permiso';
                                this.setHeader(modifyHeader);
                            }
                        };
                    },false,'confirm');
                }
              alertify.confirmHabilitarPermiso('¿Está seguro de que quiere habilitar el permiso: '+permiso+'?')
              .set({
                      'labels':{ok:'Si', cancel:'No'},
                      'onok': function(){
                          jQuery('#habilitacionModal').removeData("modal").modal({keyboard: false, backdrop:'static' });
                          jQuery('#habilitacionModal').modal('show');
                      },
                      'oncancel': function(){ alertify.error('No se realizó ninguna acción.',1); }
                    }).set('defaultFocus','cancel').show();
            }else{
              alertify.error('El permiso ya tiene una solicitud de habilitación.', 3);
            }
          }
      });

   };

  jQuery('#habilitacionModal').on('show.bs.modal', function(){
    var hoy = new Date();

    //convertir día y mes a 2 digitos

    var dia = (JSON.stringify(hoy.getDate()).length == 1)? ('0'+JSON.stringify(hoy.getDate())) : (JSON.stringify(hoy.getDate()));
    var mes = (JSON.stringify(hoy.getMonth()+1).length == 1)? ('0'+JSON.stringify(hoy.getMonth()+1)) :(JSON.stringify(hoy.getMonth()+1));


    jQuery('#fecha_desde').val(dia+'/'+mes+'/'+JSON.stringify(hoy.getFullYear()));
    jQuery('.modal-body #observaciones_hab').val('');
  });

  jQuery('#habilitacionModal').find('#btn_sol_hab').on('keydown click', function(e){
    var observaciones=jQuery('#observaciones_hab').val();
    var valido = jQuery("#formulario_habilitacion_permiso").validate().form();
    if (valido == true){
      jQuery('#observaciones_hab').val(observaciones);

    var fechadesde = jQuery('#fecha_desde').val();
    var titular = jQuery('#titular').val();
        var observaciones_h = jQuery('#observaciones_hab').val();
        var permiso = jQuery('#nro_permiso').val();
		var agente =jQuery('#agente').val();
		var subagente =jQuery('#subagente').val();

        if(observaciones_h!=''){
          // jQuery().submitHabilitacionPermiso(permiso, fechadesde, observaciones_h, titular);
          jQuery().submitHabilitacionPermiso(permiso,agente, subagente, fechadesde, observaciones_h, titular);
        }

      jQuery('#habilitacionModal').modal('hide');
    }else{
      alertify.error('Campos con formato incorrecto o sin completar.',2);
    }
  });

  /* 09-04-2021 MM: se comenta ya que solo debe enviarse cuando le dan aceptar y no por cancelar */
  /*Cuando el modal suspensión se cierra*/
  /*  jQuery('#habilitacionModal').on('hidden.bs.modal', function(){
        var fechadesde = jQuery('#fecha_desde').val();
        var observaciones = jQuery('#observaciones_hab').val();
        var permiso =jQuery('#nro_permiso').val();

        if(observaciones!=''){
          jQuery().submitHabilitacionPermiso(permiso, fechadesde, observaciones);
        }
    });
  */
/**
   * llama para inciar solicitud habilitación
   **/
  jQuery.fn.submitHabilitacionPermiso = function(permiso, agente, subagente, fechadesde, observaciones, titular) {
      jQuery.ajax({
              type: 'post',
              url: 'habilitar-permiso',
              async:true,
              dataType:'json',
              // data: {'permiso':permiso, 'fechadesde':fechadesde, 'observaciones':observaciones, 'titular':titular},
			  data: {'permiso':permiso,'agente':agente,'subagente':subagente, 'fechadesde':fechadesde, 'observaciones':observaciones, 'titular':titular},
              beforeSend: function(){
                  jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
                  jQuery('#cargandoModal').modal('show');
                  jQuery("#btn_sol_hab").prop("disabled",true);
              },
              complete:function(){
                  jQuery('#cargandoModal').modal('hide');
                  jQuery("#btn_sol_hab").prop("disabled",false);
              },
              success: function (data) {
                  if(data.exito){
                    //alertify.success('Se inició la solicitud de habilitación para el permiso: '+ permiso,2);
                    alertify.alert('Habilitación Generada', '<h4><center>Se inició la solicitud de habilitación para el permiso: '+ permiso + '<br/>' + ' Nº Trámite: <font color="red">' + data.nroTramite + '</font></center></h4>');
                  }else{
                    alertify.error('No se pudo iniciar la solicitud de habilitación del permiso.',2);
                  }
                  return false;
              },
              error:function(){
                alertify.error('Ocurrió un problema al inciar la solicitud de habilitación.',2);
              }

      });

      return false;
  };


  jQuery("#formulario_habilitacion_permiso").validate({
      rules: {
            fecha_desde: {required:true, fechavalidate:true},
            observaciones_hab: {required:true, observaciones:true}
            },
      messages: {
            fecha_desde: {required:"Ingrese la fecha desde", fechavalidate:"El formato de la fecha es dd/mm/aaaa"},
            observaciones_hab: {required:"Debe ingresar observaciones para la habilitación."}
            },
      errorElement: "error",

    });
/*************************** FIN HABILITACIÓN PERMISO**********************************/


/*El permiso tiene subagencias activas y no se puede dar de baja/suspender*/
jQuery.fn.controlSubAgenciasActivas = function(permiso, tipoTramite) {
      jQuery.ajax({
            type:'POST',
            url:'ok-aprobar',
            dataType:'json',
            data: {'permiso':permiso},
            //async:true,
            success: function(data){
                if(data.exito){
                  if(tipoTramite == 5){
                     jQuery(this).controlBajaPermiso(permiso);
                  }else if(tipoTramite == 13){
                     jQuery(this).controlRenunciaPermiso(permiso);
                  }
          else{
                     jQuery(this).controlSuspensionPermiso(permiso);
        }
                }else{
                  alertify.error('El permiso tiene subagencias activas.', 4);
                }
            },
            error:function(){
                return false;
            }
        });

    };




});//fin document ready


function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function volver(){
  //url='consulta-tramite';
  //window.location = url;
  history.go(-1);
}
