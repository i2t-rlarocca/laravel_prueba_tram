jQuery.noConflict();
jQuery(document).ready(function(){

  /****************************************/
  /*  Función que busca las localidades   */
  /* según las letras que se van ingresan-*/
  /* do en el input.                      */
  /****************************************/
  jQuery.fn.buscarLocalidades = function(inputString, idDatalist) {
    jQuery.ajax({

      type: 'POST',
      url: "localidad",
      dataType: 'json',
      data: {
        'letras': inputString
      },

      success: function(data) {
        var dataList = jQuery(idDatalist);
        dataList.empty();
        var grilla = ''; //'<select>';
        for (var i = 0; i < data['lista_localidades'].length; i++) {
          grilla += '<option value="' + data.lista_localidades[i][1] + '" id=' + data.lista_localidades[i][0] + '>';
        }
        jQuery(idDatalist).html(grilla);

      },
      error: function(data) {
        jQuery('#mensaje').text("Localidad no encontrada.");
        jQuery('#errores').show();
        jQuery('#errores').delay(5000).fadeOut();
      }
    });
  };



  /*****************************************/
  /*  Función que si se ingresaron 3       */
  /* caracteres (letras/numeros) llama a   */
  /* llama a la función que busca las      */
  /* localidades para completar el datalist*/
  /*****************************************/
  jQuery('#buscarLocalidad').on('keydown',function(e){
    var inputString = jQuery('#buscarLocalidad').val();
    var code = e.keyCode || e.which;

    if(inputString.length>2 && code != 13 && code!=9 && code!=40 && code!=38) { //si ingresó más de tres letras
      jQuery(this).buscarLocalidades(inputString,'#localidades');
    }
    if(code == 8){//delete
      jQuery('#nueva_localidad').val('');
    }
   /* if(code==13 || code==9){
      var val = jQuery('#localidades').val('id');
      var x = jQuery('#buscarLocalidad').val();
      var z = jQuery('#localidades');
      var val = jQuery(z).find('option[value="' + x + '"]');
      var endval = val.attr('id');

      if(endval != undefined){
        jQuery('#nueva_localidad').val(endval);
      }else{
        jQuery('#nueva_localidad').val('');
        jQuery('#nombre_localidad').val('');
      }
    }*/
  });


  /*****************************************/
  /*  Función que si se seleccionó una     */
  /* localidad del datalist, coloca el     */
  /* valor en un campo                     */
  /*****************************************/
  jQuery("input[name=buscarLocalidad]").focusout(function(){
    var val = jQuery('#localidades').val('id');
    var x = jQuery('#buscarLocalidad').val();
    var z = jQuery('#localidades');
    var val = jQuery(z).find('option[value="' + x + '"]');
    var endval = val.attr('id');
    if(endval != undefined){
      jQuery('#nueva_localidad').val(endval);
    }else{
      jQuery('#nueva_localidad').val('');
      jQuery('#nombre_localidad').val('');
    }

  });

  jQuery("input[name=buscarLocalidad]").change(function(){
    var val = jQuery('#localidades').val('id');
    var x = jQuery('#buscarLocalidad').val();
    var z = jQuery('#localidades');
    var val = jQuery(z).find('option[value="' + x + '"]');
    var endval = val.attr('id');

    if(endval != undefined){
      jQuery('#nueva_localidad').val(endval);
    }else{
      jQuery('#nueva_localidad').val('');
      jQuery('#nombre_localidad').val('');
    }
  });
 /* jQuery("#buscarLocalidad").bind('input', function(){
      var val = jQuery('#localidades').val('id');
      var x = jQuery('#buscarLocalidad').val();
      var z = jQuery('#localidades');
      var val = jQuery(z).find('option[value="' + x + '"]');
      var endval = val.attr('id');

      if(endval != undefined){
        jQuery('#nueva_localidad').val(endval);
      }else{
        jQuery('#nueva_localidad').val('');
        jQuery('#nombre_localidad').val('');
      }
  });*/
});





