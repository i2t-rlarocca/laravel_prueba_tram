jQuery.noConflict();
jQuery(document).ready(function() {

  jQuery('#btn-iniciar').on('keydown click', function(e) {
    var code = e.keyCode || e.which;
    if (code == 13 || e.type == "click") { //Enter keycode
      jQuery('#formulario-nroTramite').submit();
    }
  });


  jQuery("#formulario-nroTramite").validate({
    rules: {
      ntramite: {
        required: true,
        number: true
      }
    },
    messages: {
      ntramite: {
        required: " * ",
        number: " Ingrese solo números"
      }
    },
    errorElement: "error_dt",
    submitHandler: function(form) {

      jQuery.ajax({

        type: jQuery(form).attr('method'),
        url: jQuery(form).attr('action'),
        dataType: 'json',
        data: jQuery(form).serialize(),

        success: function(data) {
          if (data['mensaje'] != null) {
            jQuery('#mensaje').text(data.mensaje);
            jQuery('#errores').show();
            refrescar();
          } else {
            var nro = jQuery('#id_tramite').val();
            //detalleTramite(nro);
            jQuery('#nroTramite').val(nro);
            jQuery('#formulario-tramite').submit();
          }
        },
        error: function(data) {
          jQuery('#mensaje').text("No existe ese nº de seguimiento.");
          jQuery('#errores').show();
          jQuery('#errores').delay(5000).fadeOut();
        }

      });
      return false;
    }
  });



});

function refrescar() {
  document.getElementById("formulario-nroTramite").reset();
}
