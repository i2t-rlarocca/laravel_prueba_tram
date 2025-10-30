jQuery.noConflict(); 
jQuery(document).ready(function(){
var errorStates = [];
  /**Acciones ^ v **/
  jQuery('#collapseOne').on('shown.bs.collapse.in', function() {
    jQuery("#uno").addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');
  });
  jQuery('#collapseOne').on('hidden.bs.collapse', function() {
    jQuery("#uno").addClass('glyphicon-chevron-down').removeClass('glyphicon-chevron-up');
  });

  jQuery('#collapseTwo').on('shown.bs.collapse', function() {
    jQuery("#dos").addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');
  });
  jQuery('#collapseTwo').on('hidden.bs.collapse', function() {
    jQuery("#dos").addClass('glyphicon-chevron-down').removeClass('glyphicon-chevron-up');
  });

  jQuery('#collapseThree').on('shown.bs.collapse', function() {
    jQuery("#tres").addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');
  });
  jQuery('#collapseThree').on('hidden.bs.collapse', function() {
    jQuery("#tres").addClass('glyphicon-chevron-down').removeClass('glyphicon-chevron-up');
  });

  jQuery('#collapseFour').on('shown.bs.collapse', function() {
    jQuery("#cuatro").addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');
  });
  jQuery('#collapseFour').on('hidden.bs.collapse', function() {
    jQuery("#cuatro").addClass('glyphicon-chevron-down').removeClass('glyphicon-chevron-up');
  });

  /**fin ^ v**/

    /**Combo estado evaluación**/
      jQuery('#estado_eval').change(function(){
        /**seteo de elemento seleccionado**/
        var id =jQuery(this).val(); 
            jQuery('#estado_eval option').each(function(){
              jQuery(this).attr('selected',false);
            if(jQuery(this).val() == id){
               jQuery(this).attr('selected',true);
               this.selected = this.defaultSelected;//firefox
             }
        });     
      });

  /* Local */

   /**Combo ubicación**/
  jQuery('#ubicacion').change(function(){
    /**seteo de elemento seleccionado**/
    var id =jQuery(this).val(); 
        jQuery('#ubicacion option').each(function(){
          jQuery(this).attr('selected',false);
        if(jQuery(this).val() == id){
           jQuery(this).attr('selected',true);
           this.selected = this.defaultSelected;//firefox
         }
    });   
  });

  /**Combo superficie**/
  jQuery('#superficie').change(function(){
    /**seteo de elemento seleccionado**/
    var id =jQuery(this).val(); 
        jQuery('#superficie option').each(function(){
          jQuery(this).attr('selected',false);
        if(jQuery(this).val() == id){
           jQuery(this).attr('selected',true);
           this.selected = this.defaultSelected;//firefox
         }
    });     
  });


  /**Combo vidriera**/
  jQuery('#vidriera').change(function(){
    /**seteo de elemento seleccionado**/
    var id =jQuery(this).val(); 
        jQuery('#vidriera option').each(function(){
          jQuery(this).attr('selected',false);
        if(jQuery(this).val() == id){
           jQuery(this).attr('selected',true);
           this.selected = this.defaultSelected;//firefox
         }
    });     
  });

  /***************Porcentaje agencia******************/

  jQuery('#rubros_amigos').focusout(function() {
    var valido= jQuery(this).valid();
    var valor = jQuery(this).val();
    var suma = Number(jQuery(this).val())+Number(jQuery('#rubros_otros').val());
    if(suma<=100){
      if(valido && valor!='' && valor >=0){
        jQuery('#rubros_agencia').val(100-Number(jQuery(this).val())-Number(jQuery('#rubros_otros').val()));  
      }else {
        jQuery('#rubros_agencia').val(100);  
      }
    }else{
        jQuery(this).val(0); 
    }
  });

  jQuery('#rubros_otros').focusout(function() {
    var valido= jQuery(this).valid();
    var valor = jQuery(this).val();
    var suma = Number(jQuery(this).val())+Number(jQuery('#rubros_amigos').val());
    if(suma<=100){
      if(valido && valor!='' && valor >=0){
        jQuery('#rubros_agencia').val(100-Number(jQuery(this).val())-Number(jQuery('#rubros_amigos').val()));  
      }else{
        jQuery('#rubros_agencia').val(100);  
      }
    }else{
        jQuery(this).val(0); 
    }
    
  });


  /**Combo estado local**/
  jQuery('#estado_local').change(function(){
    /**seteo de elemento seleccionado**/
    var id =jQuery(this).val(); 
        jQuery('#estado_local option').each(function(){
          jQuery(this).attr('selected',false);
        if(jQuery(this).val() == id){
           jQuery(this).attr('selected',true);
           this.selected = this.defaultSelected;//firefox
         }
    });     
  });
  /* fin local */


  /*Entorno*/
        /* combo socioeconómico */
        /************************/
        jQuery('#soc_eco_codigo').on('keydown',function(){
          jQuery("#collapseThree").collapse('hide');
          jQuery("#collapseFour:not(.collapse.in)").collapse('show');
           jQuery('.ui-datepicker-trigger').focus();
         
        });

        /**** Centros de afinidad *****/
       jQuery('#divCentrosAfinidad').on('click', '.list-group .list-group-item', function () {
            jQuery(this).toggleClass('active');
        });

        //Accionar de flechas
        //jQuery('.list-arrows .move-left, .list-arrows .move-right').click(function () {
        jQuery('#flechaIzq, #flechaDer').on('click',function (e) {
          
            var $button = jQuery(this), actives = '';
            if ($button.hasClass('move-left')) {
                actives = jQuery('.list-right ul li.active');
                actives.clone().appendTo('.list-left ul');
                actives.remove();
            } else if ($button.hasClass('move-right')) {
                actives = jQuery('.list-left ul li.active');
                actives.clone().appendTo('.list-right ul');
                actives.remove();
            }
        });

        //Seleccionar todos
        jQuery('.dual-list .selector').on('click', function () {
            var $checkBox = jQuery(this);
            if (!$checkBox.hasClass('selected')) {
                $checkBox.addClass('selected').closest('.well').find('ul li:not(.active)').addClass('active');
                $checkBox.children('i').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
            } else {
                $checkBox.removeClass('selected').closest('.well').find('ul li.active').removeClass('active');
                $checkBox.children('i').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
            }
        });

        /***** fin centros de afinidad *****/
        
      //Función para cargar la imagen del plano
      jQuery('#plano').on('change', function(){ 
          var val = jQuery(this).val();
          fileExtension_plano = val.substring(val.lastIndexOf('.') + 1);
          var data= new FormData(jQuery('#formulario_evaluacion_domicilio')[0]);
          if(isImage(fileExtension_plano)){
              jQuery.ajax({
                  type: 'post',
                  url: 'plano',
                  dataType:'json',
                  data: data,
                  //necesario para subir archivos via ajax
                  cache: false,
                  contentType: false,
                  processData: false,

                  //mientras enviamos el archivo
                  beforeSend: function(){
                      message = jQuery("<span class='before'>Subiendo la imagen, por favor espere...</span>");
                      showMessage(message)        
                  },
                  //una vez finalizado correctamente
                  success: function(data){
                      message = jQuery("<span class='success'>La imagen ha subido correctamente.</span>");
                      showMessage(message);
                      jQuery("#vista_previa_plano").attr('src',data['path_plano']);
                     
                  },
                  //si ha ocurrido un error
                  error: function(){
                      jQuery('#mensaje').text("Ha ocurrido un error al cargar el plano.");
                      jQuery('#errores').show();
                      jQuery('#errores').delay(2000).fadeOut();
                  }
              });//fin ajax
          }else{
              jQuery('input#plano').val("");
              jQuery("#vista_previa_plano").attr('src','images/no-imagen.jpg');
          }
     });

      /**Combo estado local**/
      jQuery('#estado_entorno').change(function(){
        /**seteo de elemento seleccionado**/
        var id =jQuery(this).val(); 
            jQuery('#estado_entorno option').each(function(){
              jQuery(this).attr('selected',false);
            if(jQuery(this).val() == id){
               jQuery(this).attr('selected',true);
               this.selected = this.defaultSelected;//firefox
             }
        });     
      });

  /*fin entorno*/

  /* Cuantitativo */

    /**Combo estado local**/
  /**seteo de elemento seleccionado**/
  /*
      jQuery('#estado_cuantitativo').change(function(){
      
        var id =jQuery(this).val(); 
            jQuery('#estado_cuantitativo option').each(function(){
              jQuery(this).attr('selected',false);
            if(jQuery(this).val() == id){
               jQuery(this).attr('selected',true);
               this.selected = this.defaultSelected;//firefox
             }
        });     
      });
*/
  /* fin cuantitativo */

  /*Competidores*/

    jQuery("#chbxCompetidores").change(function(){
      if(this.checked) {
        jQuery('#divDistanciaCompetidores').find('input, a').attr('disabled',false);
      }else{
        jQuery('#divDistanciaCompetidores').find('input, a').attr('disabled','disabled');        
      }
    });
  // spinner(+-btn to change value) & total to parent input 
    jQuery(document).on('click', '.number-spinner a', function () {
          var btn = jQuery(this),
          input = btn.closest('.number-spinner').find('input'),
          total = jQuery(this).siblings("input");
          oldValue = input.val().trim();

      if (btn.attr('data-dir') == 'up') {
        if(oldValue < input.attr('max')){
          oldValue++;
          total++;
        }
      } else {
        if (oldValue > input.attr('min')) {
          oldValue--;
          total--;
        }
      }
      jQuery(this).siblings("input").val(total);
      input.val(oldValue);
    });

     /**Combo estado local**/
      jQuery('#estado_competencia').change(function(){
        /**seteo de elemento seleccionado**/
        var id =jQuery(this).val(); 
            jQuery('#estado_competencia option').each(function(){
              jQuery(this).attr('selected',false);
            if(jQuery(this).val() == id){
               jQuery(this).attr('selected',true);
               this.selected = this.defaultSelected;//firefox
             }
        });     
      });

    /*Fin competidores*/

    jQuery.fn.submitFormularioEvaluacion = function(){
        //armo arreglo con los centros de afinidad
        var listasCentros = [];
        //recorro la lista para obtener cada elemento y agregarlo al arreglo
        jQuery.each(jQuery('#centros_afinidad').find("li"), function(clave, valor){
            centroafin={};
            centroafin['clave']=clave;
            centroafin['valor']=valor.innerHTML;
            listasCentros.push(centroafin);
        });

        //Adición al formulario de ciertos campos
        //var arreglo = JSON.stringify(listasCentros);
        var arreglo = JSON.stringify(listasCentros);
        
        //elimino si existe el hidden
        jQuery('#listaCentrosAfinidad').remove();
        //creo el hidden
        var input = jQuery("<input>").attr({"type":"hidden","name":"listaCentrosAfinidad", "id":"listaCentrosAfinidad"}).val(arreglo);
        //agrego campo hidden al formulario
        jQuery('#formulario_evaluacion_domicilio').append(input);

        /*busco todos los hidden que quiero agregar
        var hiddenElements = jQuery( "#formulario_evaluacion_domicilio" ).find( ":hidden" ).not( "script,td" );
        //agrego hiddens al formulario
        jQuery('#formulario_evaluacion_domicilio').append(hiddenElements);
        
        var form = new FormData(jQuery('#formulario_evaluacion_domicilio')[0]);*/

        jQuery("#formulario_evaluacion_domicilio :disabled").removeAttr('disabled');
        jQuery('#formulario_evaluacion_domicilio').submit();
        
    }

    jQuery("#formulario_evaluacion_domicilio").on('keydown click', "#btn_aceptar", function(evt){
      evt.preventDefault();  
      var code = evt.keyCode || evt.which;

      if(evt.type == "keydown"){
         if(code == 13) { //Enter keycode
          //jQuery('#formulario_evaluacion_domicilio').submit();
          jQuery(this).submitFormularioEvaluacion();
        }
      }else if(evt.type == "click"){
        //jQuery('#formulario_evaluacion_domicilio').submit();
         jQuery(this).submitFormularioEvaluacion();
      }
   });



  //btn volver

  jQuery('#btn_volver').on('keydown click', function(evt){
      var code = evt.keyCode || evt.which;

      if(evt.type == "keydown"){
         if(code == 13) { //Enter keycode
          jQuery(this).submitVolver();
        }
      }else if(evt.type == "click"){
          jQuery(this).submitVolver();
      }
      
  }); 

    jQuery.fn.submitVolver = function() {
            window.location = jQuery('input[name=urlRetorno]').val();
    };


    jQuery.fn.submitEvaluacionPDF = function(){
        var nro_tramite = jQuery('#nroTramite').val();
       
        jQuery.ajax({
                        type: "POST",
                        url:'evaluacion-pdf',
                        dataType:'json',
                        cache: false,
                        async:false,
                        data: {'nro_tramite':nro_tramite},

                        success: function (data) {
                                evaluacion_pdf();
                            },
                        error: function(data){
                            alertify.error("No se pudo encontrar la ruta al plan.",2);
                        }
                });
    }

    jQuery('#btn_eval_pdf').on('keydown click', function(e){
        var code = e.keyCode || e.which;
        if(e.type == "keydown"){
           if(code == 13) { //Enter keycode
            jQuery(this).submitEvaluacionPDF();
          }
        }else if(e.type == "click"){
            jQuery(this).submitEvaluacionPDF();
        }
            return false;
    });

});

function showMessage(message){
        jQuery(".messages").html("").show();
        jQuery(".messages").html(message);                
    }

//comprobamos si el archivo a subir es una imagen
//para visualizarla una vez haya subido
function isImage(extension)
{   
    switch(extension.toLowerCase()) 
    {
        case 'jpg': case 'bmp': case 'png': case 'jpeg': case 'gif':
            return true;
        break;
        default:
            jQuery(this).val('');
            // error message here
            jQuery('#mensaje').text("No es una imagen");
            jQuery('#errores').show();
            jQuery('#errores').delay(1000).fadeOut();
            return false;
        break;
    }
}

function evaluacion_pdf(){
    url='evaluacion-pdf';
    window.open(url,'_blank');
}
