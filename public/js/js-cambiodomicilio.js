jQuery.noConflict();
jQuery(document).ready(function(){

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
    };

    /*Funciones para validación de ciertos campos del formulario*/
    jQuery.validator.addMethod("letrasynum", function(value, element) {
     return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'\-.,:0-9 " "]+$/i.test(value);
    });

    jQuery.validator.addMethod("motivo", function(value, element) {
     return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'\-.,;:\/0-9º" "^!¡?¿@#$%&*()}{@#~]+$/i.test(value);
    });

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




     /****************************************************/
     /* Validación del formulario de cambio de domicilio */
     /****************************************************/

    jQuery("#formulario_domicilio").validate({
          ignore: "",
          focusInvalid: false,
          invalidHandler: function() {
            jQuery(this).find(":input.error:first").focus();
          },
          rules: {
                agente: {required:true, number:true},
                subagente: {required:true, number:true},
                permiso: {required:true, number:true},
                localidad_actual_id: {required:true, number:true},
                domicilio_comercial: {required:true,letrasynum:true},
                referente: {required:true, maxlength:255},
                datos_contacto: {required:true, maxlength:255},
                buscarLocalidad: {required:true,localidad:true, maxlength:255},
                //nueva_localidad: {required:true, number:true, igualID:true},
                motivo_cd: {required: true, motivo:true}
                //nombre_localidad: {required:true, igualA:true}
                },
          messages: {
                agente: {required:" * ", number:" Ingrese solo números"},
                subagente: {required:" * ", number:" Ingrese solo números"},
                permiso: {required:" * ", number:" Ingrese solo números"},
                localidad_actual_id: {required:" * ", number:" Debe seleccionar una localidad."},
                domicilio_comercial: {required:function(){
                    jQuery('#id_domicilio_comercial').focus();
                    return " Debe ingresar un nuevo domicilio comercial. ";
                },letrasynum:"Números y letras"},
                buscarLocalidad: {required:" Debe seleccionar una localidad ", localidad:" Debe seleccionar una localidad válida.", maxlength:"Ingrese hasta 255 caracteres."},
                //nueva_localidad: {required:" Debe ingresar una localidad válida. ", number:" Ingrese solo números"},
                motivo_cd: {required: " Debe ingresar un motivo.", motivo:" Ingrese solo letras y números"},
                referente: {required: 'Debe ingresar un referente.'},
                datos_contacto: {required: 'Debe ingresar datos de contacto.'}
                //nombre_localidad: {required:" Debe ingresar una localidad válida. ", igualA:" Debe ingresar una localidad válida. "}
                },
          errorElement: "error"

    });

    jQuery.fn.submitFormDomicilio = function(){
          var id = jQuery('#nueva_localidad').val();
          if(id!=""){
            jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
            jQuery('#cargandoModal').modal('show');
            jQuery(this).buscarLocalidadPorId(id);
            var valido = jQuery('#formulario_domicilio').validate().form();
            var nl = jQuery('input[name="nueva_localidad"]').valid();

              if(valido && nl){
                jQuery('#btn_ingresar').prop( "disabled", true );
                jQuery('#btn_cargarPlan').prop( "disabled", true );
                jQuery('#btn_volver').prop( "disabled", true );
                jQuery('#btn-limpiar').prop( "disabled", true );
                jQuery('#btn_adjuntar').prop( "disabled", true );

                jQuery('#formulario_domicilio').submit();

              }else{
                jQuery('#cargandoModal').modal('hide');
              }
          }else{
            var valido = jQuery('#formulario_domicilio').validate().form();
          }
    }

    jQuery('#formulario_domicilio').on('keydown click','#btn_cargarPlan',function(e){
        var code = e.keyCode || e.which;
        if(e.type == "keydown"){
           if(code == 13) { //Enter keycode
            jQuery("#continuar").val(1);
            jQuery(this).submitFormDomicilio();
          }
        }else if(e.type == "click"){
            jQuery("#continuar").val(1);
            jQuery(this).submitFormDomicilio();
        }

        return false;

     });

    jQuery('#formulario_domicilio').on('keydown click','#btn_ingresar',function(e){
        var code = e.keyCode || e.which;
        if(e.type == "keydown"){
        if(code == 13) { //Enter keycode
            jQuery("#continuar").val(0);
            jQuery(this).submitFormDomicilio();
              }
        }else if(e.type == "click"){
            jQuery("#continuar").val(0);
            jQuery(this).submitFormDomicilio();
          }

        return false;

            });


    /* jQuery('#btn_ingresar').keydown(function(e){
        var code = e.keyCode || e.which;

           if(code == 13) { //Enter keycode
          var id = jQuery('#nueva_localidad').val();

          if(id!=""){
            jQuery(this).buscarLocalidadPorId(id);
            var valido = jQuery('#formulario_domicilio').validate().form();
            var nl = jQuery('input[name="nueva_localidad"]').valid();

              if(valido && nl){
                jQuery('#btn_ingresar').prop( "disabled", true );
                  jQuery('#formulario_domicilio').submit();
          }
          }else{
            var valido = jQuery('#formulario_domicilio').validate().form();
        }
}
     });*/
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
   document.getElementById('nueva_localidad').value='';
   document.getElementById('buscarLocalidad').value='';
   document.getElementById('buscarLocalidad').attr("readonly","true");
}

function volver(){
  url='tramites';
  window.location = url;
}
