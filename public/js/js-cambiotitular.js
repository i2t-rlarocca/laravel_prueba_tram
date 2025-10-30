jQuery.noConflict();
jQuery(document).ready(function(){

    jQuery(window).load(function(){
        var val = jQuery('#tipo_persona').val();
        if(val=='J'){
            jQuery('#sexo_persona_fisica').hide();
            jQuery('#datos_persona_fisica').hide();
            jQuery('#div_pers_jur').show();
            jQuery('#tipo_persona_juridica').show();
            jQuery('#tipo_persona').removeAttr("onfocus");
            jQuery('#tipo_persona').attr('onfocus','siguienteCampo = this.form.tipo_sociedad.id;nombreForm=this.form.id');
        }else{
          jQuery('#div_pers_jur').hide();
          jQuery('#tipo_persona_juridica').hide();
          jQuery('#tipo_persona').removeAttr("onfocus");
          jQuery('#tipo_persona').attr('onfocus','siguienteCampo = this.form.mujer.id;nombreForm=this.form.id');
        }
    });

    /****************************************************/
    /*              Definición de funciones             */
    /****************************************************/

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

    jQuery.validator.addMethod("localidad", function(value, element) {
       return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'.()\-0-9," "]+$/i.test(value);
      }, "Debe seleccionar una localidad.");


   /* jQuery.validator.addMethod("telefono", function(value, element) {
       return this.optional(element) || /^(\d+-\d+\s{0,1})+$/gm.test(value);
      }, "Debe ingresar un teléfono. Ej: 0342-45896589");*/

    jQuery.validator.addMethod("documento", function(value, element) {
		var persona = jQuery('#tipo_persona').val();
		if(persona=="F"){
		  var valor_sin_ptos=value.replace(/\./g, "");
		  var tipo_doc = jQuery('#tipo_doc option:selected').val();
		  var docu=valor_sin_ptos;

		  if(docu.charAt(0)>"0"){
			  if(docu.length==7  && (tipo_doc==1 || tipo_doc==2 || tipo_doc==3)){
				  docu=valor_sin_ptos.slice(0,1)+'.'+valor_sin_ptos.slice(1,4)+'.'+valor_sin_ptos.slice(4,7);
				  jQuery('#nro_doc').val(docu);
				  return true;
			  }else if(docu.length==8 && (tipo_doc==3 ||tipo_doc==4)){
				  docu=valor_sin_ptos.slice(0,2)+'.'+valor_sin_ptos.slice(2,5)+'.'+valor_sin_ptos.slice(5,8);
				  jQuery('#nro_doc').val(docu);
				  return true;
			  }else{
				  return false;
			  }

		  }else{
			    return false;
		  }

			return this.optional(element) ;
		}else{
			return true;
		}
      }, "Debe ingresar un nº documento.");

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

/* ANULADO ADEN 2024-04-19
    jQuery.validator.addMethod('ingresos', function(value, element){
        return this.optional(element) || /[0-9]+\-[0-9]+\-[0-9]+$/gi.test(jQuery.trim(value));
      }, "Campo incorrecto.");
*/

/*************************************************************************/
/*                CAMBIOS EN LOS COMBOS DEL FORMULARIO                   */
/*************************************************************************/
      /**Combo de persona física y jurídica**/
     jQuery('#tipo_persona').on('change', function(){

          var id =jQuery(this).val();
          jQuery('#tipo_persona option').each(function(){
            jQuery(this).attr('selected',false);
            if(jQuery(this).val() == id){
               jQuery(this).attr('selected',true);
               this.selected = this.defaultSelected;//firefox
             }
          });
          var val = jQuery(this).val();
          var cuit = jQuery('#cuit').val();
          if(val=='F'){//pf
            jQuery('#sexo_persona_fisica').show();
            //seteo el ancho porque estaba oculto
            jQuery('#mujer').attr('width','100%')
            jQuery('#mujer').focus();

            jQuery('#datos_persona_fisica').show();
            jQuery('#div_pers_jur').hide();
            jQuery("#hombre").prop("checked", true);
            jQuery('#tipo_persona_juridica').hide();
          }else{//pj
            jQuery('#sexo_persona_fisica').hide();
            jQuery("input:radio").removeAttr("checked");
            jQuery('#datos_persona_fisica').hide();
            jQuery('#div_pers_jur').show();
            jQuery('#tipo_persona_juridica').show();
            //seteo el ancho porque estaba oculto
            jQuery('#tipo_sociedad').attr('width','100%')
            jQuery('#tipo_sociedad').focus();
          }

          if(jQuery('#tipo_persona').val()=='F'){
            var doc =jQuery('#nro_doc').val();
            var tipo = jQuery('#tipo_doc').val();
            if(jQuery('#cuit').val().length>0){
              jQuery(this).buscarPersona(jQuery('#cuit').val(),4);
            }else{
              jQuery(this).buscarPersona(doc,tipo);
            }

          }else{
              jQuery(this).buscarPersona(jQuery('#cuit').val(),4);
          }
     });


     jQuery("#hombre, #mujer").change(function() {

          if(jQuery('#tipo_persona').val()=='F'){
            var doc =jQuery('#nro_doc').val();
            var tipo = jQuery('#tipo_doc').val();
            if(jQuery('#cuit').val().length>0){
              jQuery(this).buscarPersona(jQuery('#cuit').val(),4);
            }else{
              jQuery(this).buscarPersona(doc,tipo);
            }

          }else{
              jQuery(this).buscarPersona(jQuery('#cuit').val(),4);
          }
     });


     /****************************************************/
     /* Validación del formulario de cambio de titular   */
     /****************************************************/

    jQuery("#formulario-cambio-titular").validate({
      ignore: "",
      focusInvalid: false,
      invalidHandler: function() {
            jQuery(this).find(":input.error:first").focus();
          },
      rules: {
            cuit: {required:function(){
                            var tp= jQuery('#tipo_persona').val();
                            var tipoTramite = jQuery('#tipo_tramite').val();
                            if(tipoTramite==3)
                            if(tp=="F"){
                              return false;
                            }else{
                              return true;
                              }
                            else
                              return false;
                          },cuit:function(){
                            var tp= jQuery('#tipo_persona').val();
                            if(tp=="F"){
                              return false;
                            }else{
                              return true;
                            }}},
            razon_social: {
				required:function(){
                            var tp= jQuery('#tipo_persona').val();
                            if(tp=="F"){
                              return false;
                            }else{
                              return true;
                            }},
							minlength: 2},
            apellido_nombre: {required:function(){
                            var tp= jQuery('#tipo_persona').val();
                            if(tp=="J"){
                              return false;
                            }else{
                              return true;
                            }}, minlength: 2}, //lettersonly: true,
            apellido_mat: {lettersonly: true, minlength: 2},
            nro_doc:{required:function(){
                            var tp= jQuery('#tipo_persona').val();
                            if(tp=="J"){
                              return false;
                            }else{
                              return true;
                            }},
					           documento: function(){
							         var tp= jQuery('#tipo_persona').val();
                            if(tp=="J"){
                              return false;
                            }else{
                              return true;
                            }}},
            /*cbu:{required:function(){
                    var agencia= jQuery('#id_subagente').val();
                    if(Number(agencia)==0){
                      return true;
                    }else{
                      return false;
                    }}},*/
            domicilio: {required: true, domicilio: true, minlength: 2},
/*	ANULADO - ADEN - 2024-04-19			
            ingresos: {maxlength: 13, ingresos:true},
*/			
           /* fecha_nac:{required:function(){
                            var tp= jQuery('#tipo_persona').val();
                            if(tp=="J"){
                              return false;
                            }else{
                              return true;
                            }}, fecha_nacimiento: function(){var tp= jQuery('#tipo_persona').val();
                            if(tp=="J"){
                              return false;
                            }else{
                              return true;
                            }}},  */
            email: {email:true},
            referente: {required:true, minlength: 4,maxlength:255},
            datos_contacto: {required:true, minlength: 4,maxlength:255},
            buscarLocalidad: {required:true,localidad:true, maxlength: 255 },
            //nueva_localidad: {required:true, number:true},
            motivo_ct: {required:true,motivo:true},
            },
      messages: {
            cuit: {required:"Debe ingresar cuit."},
            razon_social: {required:"Debe ingresar una razón social.", minlength: "Mín. 2 caracteres"},
            apellido_nombre: {required:"Debe ingresar apellido y nombre.", lettersonly: "Solo letras", minlength: "Mín. 2 caracteres"},
            apellido_mat: {lettersonly: "Solo letras", minlength: "Mín. 2 caracteres"},
            nro_doc: {required:"Debe ingresar nº de documento."},
            domicilio: {required:"Debe ingresar un domicilio.", domicilio: "Solo letras, números y algunos caracteres especiales.", minlength: "Mín. 2 caracteres"},
           // fecha_nac: {required: "Debe ingresar fecha de nacimiento."},
            buscarLocalidad: {required:"Debe ingresar una localidad.", localidad:" Debe seleccionar una localidad.", maxlength:"Ingrese hasta 255 caracteres."},
            //nueva_localidad: {required:" Debe ingresar una localidad válida. ", number:" Ingrese solo números"},
            motivo_ct: {required:"Debe ingresar un motivo para el cambio.",motivo:"Números y letras"},
            //nombre_localidad: {required:" Debe ingresar una localidad válida. ", igualA:" Debe ingresar una localidad válida. "}
            email:{email:"Ingrese un e-mail válido."},
            referente: {required: 'Debe ingresar un referente.'},
            datos_contacto: {required: 'Debe ingresar datos de contacto.'},

            },
      errorElement: "error_dt"
  });



/**************************************************/
/*   SUBMIT DEL FORMULARIO DE CAMBIO DE TITULAR   */
/**************************************************/
jQuery('#formulario-cambio-titular').on('submit', function() {
    jQuery(this).find(':input').removeProp('disabled');
});

jQuery.fn.submitFormTitular = function(){
        var id = jQuery('#nueva_localidad').val();
        if(id!=""){
          jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
          jQuery('#cargandoModal').modal('show');
          jQuery(this).buscarLocalidadPorId(id);
          var valido = jQuery('#formulario-cambio-titular').validate().form();
          var nl = jQuery('input[name="nueva_localidad"]').valid();

          if(valido && nl){
            jQuery("#formulario-cambio-titular :disabled").removeAttr('disabled');
            jQuery('#btn_ingresar').prop( "disabled", true );
            jQuery('#btn_volver').prop( "disabled", true );
            jQuery('#btn-limpiar').prop( "disabled", true );
            jQuery('#btn_adjuntar').prop( "disabled", true );

            jQuery('#formulario-cambio-titular').submit();
          }else{
            jQuery('#cargandoModal').modal('hide');
          }
        }else{
          var valido = jQuery('#formulario-cambio-titular').validate().form();
        }
}
/*Eventos del botón de ingresar trámite*/
jQuery('#formulario-cambio-titular').on('keydown click','#btn_ingresar',function(e){
        var code = e.keyCode || e.which;

        if(e.type == "keydown"){
           if(code == 13) { //Enter keycode
            jQuery(this).submitFormTitular();
          }
        }else if(e.type == "click"){
            jQuery(this).submitFormTitular();
        }

      return false;
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
                          var tipo_personaSeleccionada = jQuery('#tipo_persona').val();

                          jQuery("#tipo_persona option[value=" + data['persona']['tipo_persona'] + "]").prop('selected', 'selected');
                          var tipo_persona = jQuery('#tipo_persona').val();

                          if(tipo_personaSeleccionada!==tipo_persona){

                            if(tipo_persona=="F"){
                              jQuery('#div_pers_jur').hide();
                              jQuery('#tipo_persona_juridica').hide();
                              jQuery('#datos_persona_fisica').show();
                              jQuery('#sexo_persona_fisica').show();
                            }else{
                              jQuery('#datos_persona_fisica').hide();
                              jQuery('#sexo_persona_fisica').hide();
                              jQuery('#div_pers_jur').show();
                              jQuery('#tipo_persona_juridica').show();
                            }
                          }

                          //datos de contacto
                          if(data['persona']['datos_contacto']!=undefined && data['persona']['datos_contacto']!="" && data['persona']['datos_contacto']!==null && jQuery('#datos_contacto :enabled')){
                            jQuery('#datos_contacto').val(data['persona']['datos_contacto']);
                            jQuery('#datos_contacto').prop('disabled', true);
                          }else{
                            jQuery('#datos_contacto').prop('disabled',false);
                          }
/* ANULADO - ADEN - 2024-04-19
                          //ingresos
                          if(data['persona']['ingresos']!=undefined && data['persona']['ingresos']!="" && data['persona']['ingresos']!==null && jQuery('#ingresos :enabled')){
                            jQuery('#ingresos').val(data['persona']['ingresos']);
                            jQuery('#ingresos').prop('disabled', true);
                          }else{
                            jQuery('#ingresos').prop('disabled',false);
                          }
*/  
/* ANULADO - ADEN - 2024-04-19
						//cbu
                          if(data['persona']['cbu']!=undefined &&  data['persona']['cbu']!="" && data['persona']['cbu']!== null && jQuery('#cbu :enabled')){
                              jQuery('#cbu').val(data['persona']['cbu']);
                              jQuery('#cbu').prop('disabled', true);
                          }else{
                              jQuery('#cbu').prop('disabled',false);
                          }
*/  
                          //referente
                          if(data['persona']['referente']!=undefined &&  data['persona']['referente']!="" && data['persona']['referente']!==null && jQuery('#referente :enabled')){
                            jQuery('#referente').val(data['persona']['referente']);
                            jQuery('#referente').prop('disabled', true);
                          }else{
                            jQuery('#referente').prop('disabled',false);
                          }
                          //tipo de sociedad
                          if(data['persona']['tipo_sociedad']!=undefined && data['persona']['tipo_sociedad']!="" && data['persona']['tipo_sociedad']!==null && jQuery('#tipo_sociedad :enabled')){
                              jQuery('#tipo_sociedad').val(data['persona']['tipo_sociedad']);
                              jQuery('#tipo_sociedad').prop('disabled', true);
                          }else{
                            jQuery('#tipo_sociedad').prop('disabled',false);
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

                            //sexo
                            if(data['persona']['sexo_c']!=undefined &&  data['persona']['sexo_c']!="" && data['persona']['sexo_c']!==null && jQuery('input[name=sexo_persona] :enabled')){
                              jQuery("input[name=sexo_persona][value=" + data['persona']['sexo_c'] + "]").prop('checked', true);
                              jQuery('input[name=sexo_persona]').prop('disabled', true)
                            }else{
                              jQuery('input[name=sexo_persona]').prop('disabled',false);
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
                            //tipo de documento
                          /*  if(data['persona']['tipo_doc']!=undefined && data['persona']['tipo_doc']!="" && data['persona']['tipo_doc']!==null && jQuery('#tipo_doc :enabled')){
                              jQuery('#tipo_doc').val(data['persona']['tipo_doc']);
                            }*/
                            //nro de documento
                            if(data['persona']['nro_doc']!=undefined && data['persona']['nro_doc']!="" && data['persona']['nro_doc']!==null){
                              var docu=data['persona']['nro_doc'];
                              var value = data['persona']['nro_doc'];
                              if(docu.length==7){
                                docu=value.slice(0,1)+'.'+value.slice(1,4)+'.'+value.slice(4,7);
                              }else{
                                docu=value.slice(0,2)+'.'+value.slice(2,5)+'.'+value.slice(5,8);
                              }
                              jQuery('#nro_doc').val(docu);
                            }

                            //jQuery('#apellido_nombre, #id_apellido_mat, #domicilio, #fecha_nac, #referente, #datos_contacto, #tipo_ocup, #tipo_situacion, #ingresos, #buscarLocalidad' ).attr('disabled', 'disabled');
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
                        jQuery('#cuit').valid();
                       }//fin data['persona']
                      }else{
                            jQuery(this).habiReset();
                      }
                    }else{
                            jQuery(this).habiReset();
                    }
                  },
                  error: function(data){
                    alert("error buscando persona");
                  }
              });
  }

/* ANULADO - ADEN - 2024-04-19
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
	
	jQuery.fn.validarCuitPermiso = function() {
      var id_permiso = jQuery('#id_permiso').val();
		
		jQuery.ajax({
                type:'POST',
                url:'validarCuitPermiso',
                dataType:'json',
                data: {'id_permiso':id_permiso},

                success: function(data){

                    if(!data.exito){
                      var error = '<error_dt for="cuit" class="error">'+data.mensaje+'</error_dt>'
                      jQuery(error).insertAfter(jQuery('#cuit'));
                    }else{
                      jQuery('error_dt [value="cuit"]').remove();
                    }
                }
            });
	   if(cuit == cuitPermiso){
			var error = '<error_dt for="cuit" class="error">Cuit no valido</error_dt>'
			jQuery(error).insertAfter(jQuery('#cuit')); 
			jQuery(this).habiReset();
			jQuery('#cuit').val('');
			return false;
	 }else{
		 jQuery("error_dt").remove();
	 }
	};
	  
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
    * Campo cuit pierde el foco
    **/
    jQuery('#cuit').focusout(function(){
		
		
      if(jQuery('#cuit').val().length>0){
		  

		if(jQuery('#cuitPermiso').val() ==	jQuery('#cuit').val()){
			jQuery("error_dt").remove();
			 jQuery(this).validarCuitPermiso();
			
			// return false;
		}else{
			jQuery("error_dt").remove();
		}
		  
        // jQuery(this).f_cuitPermiso(jQuery('#cuit').val(),jQuery('#cuitPermiso').val());
		
      }else{
        var tipo_persona=jQuery('#tipo_persona').val()
        if(tipo_persona=='F' && !jQuery('#nro_doc').is(':disabled')){
          var doc =jQuery('#nro_doc').val();
          var tipo = jQuery('#tipo_doc').val();
          jQuery(this).buscarPersona(doc,tipo);
        }else{
          jQuery(this).habiReset();
        }//nro_doc disabled
      }//cuit length >0
      });

    /**
    * Campo nº documento pierde el foco
    **/
    jQuery('#nro_doc').focusout(function(){
      if(jQuery('#nro_doc').valid()){
        if(jQuery('#tipo_persona').val()=='F'){
          var doc =jQuery('#nro_doc').val();
          var tipo = jQuery('#tipo_doc').val();
          if(jQuery('#cuit').val().length>0){
            if(!jQuery('#cuit').is(':disabled')){
              jQuery(this).buscarPersona(jQuery('#cuit').val(),4);
            }else{
              jQuery(this).habiReset();
              //jQuery('#formulario-cambio-titular').trigger("reset");
            }//cuit disabled
          }else{
            jQuery(this).buscarPersona(doc,tipo);
          }

        }
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

   //combo tipo de documento
  jQuery('#tipo_doc').on('change', function(){
        var id =jQuery(this).val();
        jQuery('#tipo_doc option').each(function(){
          jQuery(this).attr('selected',false);
          if(jQuery(this).val() == id){
             jQuery(this).attr('selected',true);
             this.selected = this.defaultSelected;//firefox
           }
        });
        var doc =jQuery('#nro_doc').val();
        var tipo = jQuery(this).val();
        jQuery(this).buscarPersona(doc,tipo);
        if(doc.length>0){
          jQuery('#nro_doc').valid();
        }

   });

   //combo tipo de documento
  jQuery('#tipo_ocup').on('change', function(){
          var id =jQuery(this).val();
          jQuery('#tipo_ocup option').each(function(){
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
/* ANULADO - ADEN - 2024-04-19
  jQuery('#ingresos').focusout(function(){
      if(jQuery(this).val().length>10){
        jQuery().validarIngresos();
      }
    });
*/	
	
 /**
  * CUIT
  **/
  jQuery('#cuit').focusout(function(){
      if(jQuery(this).val().length>13){
        // jQuery().validarCuit();
		alert(jQuery(this).val());
      }
    });

  /****
  * Habilita/Resetea
  **/
  jQuery.fn.habiReset=function(){
          var tipo_persona=jQuery('#tipo_persona').val(),
              arr,arrSelect,objetosDisabled;
          if(tipo_persona=='F'){
// INI MODIFICADO - ADEN - 2024-04-19
//             arr = ['#ingresos','#cbu','#fecha_nac','#apellido_nombre', '#id_apellido_mat', '#domicilio', '#id_email', '#referente', '#datos_contacto', '#buscarLocalidad', '#nombre_localidad','#nueva_localidad'];
             arr = ['#fecha_nac','#apellido_nombre', '#id_apellido_mat', '#domicilio', '#id_email', '#referente', '#datos_contacto', '#buscarLocalidad', '#nombre_localidad','#nueva_localidad'];
// FIN MODIFICADO - ADEN - 2024-04-19
             arrSelect=['#tipo_ocup','#tipo_situacion'];
          }else{
// INI MODIFICADO - ADEN - 2024-04-19
//             arr = ['#razon_social','#cbu','#domicilio','#referente','#datos_contacto', '#ingresos', '#buscarLocalidad'];
             arr = ['#razon_social','#domicilio','#referente','#datos_contacto', '#buscarLocalidad'];
// FIN MODIFICADO - ADEN - 2024-04-19
             arrSelect=['#tipo_sociedad','#tipo_situacion'];
          }

          objetosDisabled =jQuery(':disabled');

          for(var i=0; i<objetosDisabled.length;i++){
            jQuery(objetosDisabled[i]).prop('disabled',false);
          }

          //retorno al valor por defecto
          jQuery.each(arr, function( key, value ) {
            jQuery(value).val(jQuery(value).prop( 'defaultValue' ));
          });

          jQuery.each(arrSelect, function( key, value ) {
            if(value!='#tipo_ocup')
              jQuery(value).removeAttr('selected').find('option:first').attr('selected', 'selected');
          });
  }

  /**
  * Valida campos pre-cargados
  **/
  jQuery.fn.validarCampos=function(){

      var objetosDisabled =jQuery(':disabled');

      for(var i=0; i<objetosDisabled.length;i++){
        jQuery(objetosDisabled[i]).prop('disabled',false);
      }

      for(var i=0; i<objetosDisabled.length;i++){
        jQuery(objetosDisabled[i]).valid();
      }

      for(var i=0; i<objetosDisabled.length;i++){
        jQuery(objetosDisabled[i]).prop('disabled',true);
      }

      jQuery('#nro_doc').valid();
      jQuery('#cuit').valid();
  }

  /**
  * Función de limpieza del formulario
  **/
  jQuery('#btn-limpiar').on('keydown click', function(e){
      var objetosDisabled =jQuery(':disabled');
      for(var i=0; i<objetosDisabled.length;i++){
        jQuery(objetosDisabled[i]).prop('disabled',false);
      }
      jQuery('#formulario-cambio-titular').trigger('reset');

  });


  /*cambio en el campo email*/
  jQuery('#id_email').on('change', function(){
          jQuery(this).buscarCorreoElectronico(jQuery('#id_email').val(), jQuery('#id_permiso').val());
   });


	jQuery('#formulario-cambio-titular').on('keydown click','#btn_cargarPlan',function(e){
	//alert('llega1');
        var code = e.keyCode || e.which;
        if(e.type == "keydown"){
           if(code == 13) { //Enter keycode
            jQuery("#continuar").val(1);

            jQuery(this).submitFormCambioTitular();
          }
        }else if(e.type == "click"){
            jQuery("#continuar").val(1);
            jQuery(this).submitFormCambioTitular();
        }

        return false;

     });

jQuery.fn.submitFormCambioTitular = function(){
	//alert('entra2');
          var id = jQuery('#nueva_localidad').val();
          if(id!=""){
            jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
            jQuery('#cargandoModal').modal('show');
            jQuery(this).buscarLocalidadPorId(id);
            var valido = jQuery('#formulario-cambio-titular').validate().form();
            var nl = jQuery('input[name="nueva_localidad"]').valid();

              if(valido && nl){
                jQuery('#btn_ingresar').prop( "disabled", true );
                jQuery('#btn_cargarPlan').prop( "disabled", true );
                jQuery('#btn_volver').prop( "disabled", true );
                jQuery('#btn-limpiar').prop( "disabled", true );
              //  jQuery('#btn_adjuntar').prop( "disabled", true );

                jQuery('#formulario-cambio-titular').submit();

              }else{
                jQuery('#cargandoModal').modal('hide');
              }
          }else{
            var valido = jQuery('#formulario-cambio-titular').validate().form();
          }
    }
});//fin document ready



 function refrescar(formulario)
{
     //reset de todos los elementos del formulario
   document.getElementById(formulario).reset();
   //document.getElementById('id_nombre').focus();
   jQuery('#formulario-cambio-titular')[0].reset();
}

function irExito(){
    url='exito-tramite';
    window.location = url;
}

function volver(){
  url='tramites';
  window.location = url;
}
