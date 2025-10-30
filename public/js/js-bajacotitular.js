jQuery.noConflict();
function dar_de_baja_cotitular(){
	let valido = jQuery('#formulario-baja-cotitular').validate().form();
	let id = jQuery('input[name="cotitular_to_kill"]:checked').val();
	if(id == undefined || id == null){
		return;
	}
	if(valido)
		jQuery("#formulario-baja-cotitular").submit();
}

function refrescar(formulario){
   document.getElementById(formulario).reset();
}

jQuery(document).ready(function() {
  jQuery("#formulario-baja-cotitular").validate({
    ignore: "",
    focusInvalid: false,
    invalidHandler: function() {
      jQuery(this).find(":input.error:first").focus();
    },
    rules: {
			cotitular_to_kill: {
        required: true
			},
      motivo_ct: {
        required: true,
      }
    },
    messages: {
      cotitular_to_kill: {
        required: "*",
      },
      motivo_ct: {
        required: " Debe ingresar un motivo."
      },
    },
    errorElement: "error"
  });

});
