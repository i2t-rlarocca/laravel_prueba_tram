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
/***********Validaciones***********/

jQuery.validator.addMethod('numerico', function(value,element) {
        numeros = /^([0-9])*$/;
        return this.optional(element) || value.match(numeros) || element=='[]';
    });

jQuery.validator.addMethod("domicilio", function(value, element) {
     return this.optional(element) || /^[()A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'\-,º'.:0-9" "]+$/i.test(value);
    }, "Solo letras");

jQuery.validator.addMethod("lettersonly", function(value, element) {
     return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´,'." "]+$/i.test(value);
    }, "Solo letras");

jQuery.validator.addMethod("telefono", function(value, element) {
     return this.optional(element) || /^([0-9 \- " "])*$/i.test(value);
    });

jQuery.validator.addMethod("varios", function(value, element) {
     return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'-.,:\/0-9º" "]+$/i.test(value); 
    });

jQuery.validator.addMethod("masDUno", function(value, element) {
      var count = jQuery(element).find('option:selected').length;
                return count > 0;
    });

jQuery.validator.addMethod("superficie", function(value, element) {
      return ("ni" != value); 
    });

jQuery.validator.addMethod("ubicacion", function(value, element) {
      return ("ni" != value); 
    });

jQuery.validator.addMethod("vidriera", function(value, element) {
      return ("ni" != value); 
    });

jQuery.validator.addMethod("nivel_socioeconomico", function(value, element) {
      return ("ni" != value); 
    });

jQuery("#formulario_plan_estrategico").validate({
        
        rules: {
                domicilio_nuevo: {required:true,domicilio:true},
                localidad_nuevo: {required:true,varios:true},
                persona_contacto: {required:true,lettersonly:true},
                telefono_contacto: {required:true},
                cant_empleados: {required:true, min:1, maxlength:3,numerico:true},
                "caracteristicas[]": {required:true,masDUno:true},
                foto_f:{required:function(){
                  var vp_f = jQuery('#vista_previa_ff').attr('src');
                  if(vp_f && vp_f!="images/no-imagen.jpg"){
                    return false;
                  }else{
                    return true;
                  }}, extension:"jpg|jpeg|png|bmp|gif"},
                foto_i:{required:function(){
                  var vp_i = jQuery('#vista_previa_fi').attr('src');
                  if(vp_i && vp_i!="images/no-imagen.jpg"){
                    return false;
                  }else{
                    return true;
                  }}, extension:"jpg|jpeg|png|bmp|gif"},
               // foto_f:{required:true,extension:"jpg|jpeg|png|bmp"},
               // foto_i:{required:true,extension:"jpg|jpeg|png|bmp"},
                preg_1: {required:'opt'},
                preg_2: {required:'opt'},
                venta_1: {required:true,numerico:true},
                venta_2: {required:true,numerico:true},
                venta_3: {required:true,numerico:true},
                venta_4: {required:true,numerico:true},
                rubros_amigos: {required:true,numerico:true},
                rubros_otros:{required:true, numerico:true},
                mes_i:{required:true},
                superficie:{required:true,superficie:true},
                ubicacion:{required:true,ubicacion:true},
                vidriera:{required:true,vidriera:true},
                nivel_socioeconomico_codigo:{required:true, nivel_socioeconomico:true}
                },
        messages: {
                persona_contacto: {required:"Ingrese persona de contacto",lettersonly:"Sólo letras y espacios"},
                telefono_contacto: {required:"Ingrese un teléfono de contacto"},
                "caracteristicas[]": {masDUno:"Seleccione al menos una opción"},
                venta_1: {numerico: "Sólo números"},
                venta_2: {numerico: "Sólo números"},
                venta_3: {numerico: "Sólo números"},
                venta_4: {numerico: "Sólo números"},
                rubros_amigos: {numerico: "Sólo números"},
                rubros_otros: {numerico:"Sólo números"},
                mes_i:{required:"Debe ingresar un mes inicial."},
                foto_i:{required:"Seleccione una imagen."},
                foto_f:{required:"Seleccione una imagen."},
                superficie:{required:"Debe seleccionar un valor",superficie:"Opción no válida."},
                ubicacion:{required:"Debe seleccionar un valor",ubicacion:"Opción no válida."},
                vidriera:{required:"Debe seleccionar un valor",vidriera:"Opción no válida."},
                nivel_socioeconomico_codigo:{required:"Debe seleccionar un valor", nivel_socioeconomico:"Opción no válida."},
                cant_empleados:{min:"Al menos 1 empleado."}
                },
        //ignore: [],
        highlight: function (element, errorClass) {
           //jQuery(element).closest('.form-group').addClass('has-error');
            if(jQuery.inArray(element, errorStates) == -1){
                errorStates[errorStates.length] = element;
                jQuery(element).popover('show');
            }
        }, 
        unhighlight: function (element, errorClass, validClass) {
            if(jQuery.inArray(element, errorStates) != -1){
                this.errorStates = jQuery.grep(errorStates, function(value) {
                  return value != errorStates;
                });
                jQuery(element).popover('hide');
            }
            //jQuery(element).closest('.form-group').removeClass('has-error');
        },
        errorPlacement: function(err, element) {
            //err.hide();
            if(jQuery.inArray(element, errorStates) == -1){
                errorStates[errorStates.length] = element;
                jQuery(element).popover('show');
            }
        }
        /*errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }*/
});

jQuery.fn.submitFormPlanEstrategico = function(){
    jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
    jQuery('#cargandoModal').modal('show');
	  jQuery("#collapseOne:not(.collapse.in)").collapse('show');
    jQuery("#collapseTwo:not(.collapse.in)").collapse('show');
    jQuery("#collapseThree:not(.collapse.in)").collapse('show');
    jQuery("#collapseFour:not(.collapse.in)").collapse('show');
    jQuery("#mes_i").prop('disabled', false);
    var valido = jQuery('#formulario_plan_estrategico').validate().form();
  
    if(valido){  
      var dataArr = [];
      jQuery('#tablita td').map(function() {
            dataArr.push(jQuery(this).text());
      });
      var tabla =  jQuery("<input>").attr({"type":"hidden","name":"tabla_val"}).val(dataArr);
      jQuery('#formulario_plan_estrategico').append(tabla);
      if(jQuery('#btn_aceptar') != undefined){
        jQuery('#btn_aceptar').attr('disabled',true);
      }else{
        jQuery('#btn_modificar').attr('disabled',true);  
      }
      
      
      jQuery('#formulario_plan_estrategico').submit();
    }else{
       jQuery("#mes_i").prop('disabled', true);
       jQuery('#cargandoModal').modal('hide');
    }
}

jQuery('#formulario_plan_estrategico').on('keydown  click','#btn_aceptar,#btn_modificar',function(){
   
    /* if(jQuery("#collapseOne:not(.collapse.in)")){
      jQuery("#collapseOne:not(.collapse.in)").collapse('show');  
    }*/
    jQuery(this).submitFormPlanEstrategico();
    return false;
});

/*********************/
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
     var valor=jQuery('#ubicacion').children(":selected").attr("value");
     if(valor==3){//otra
        jQuery('#div_otra_ubicacion').show();
        var largo = jQuery('#div_ubicacion_superior').outerHeight();
      jQuery('#div_superficie_superior').css('height',largo);
      jQuery('#otra_ubicacion').focus();
     }else{
        jQuery('#otra_ubicacion').val('');
        jQuery('#div_otra_ubicacion').hide();
         var largo = jQuery('#div_ubicacion_superior').outerHeight();
      jQuery('#div_superficie_superior').css('height',largo);
      jQuery('#vidriera').focus();
     }
  });

  /*******************/
  /* Combo horario   */
  /*******************/
jQuery('#horario').on('keydown',function(){
  jQuery("#collapseOne").collapse('hide');
    jQuery("#collapseTwo:not(.collapse.in)").collapse('show');
    jQuery('#foto_f').focus();
});

/***********/
/* foto i  */
/***********/
jQuery('#foto_i').on('keydown',function(){
  var src=jQuery("#vista_previa_fi").attr('src');
  if(src!="" && src!="images/no-imagen.jpg"){
    jQuery("#collapseTwo").collapse('hide');
    jQuery("#collapseThree:not(.collapse.in)").collapse('show');
    jQuery('#preg_1').focus();
  }
    
});

/************************/
/* nivel socioeconómico */
/************************/
jQuery('#nivel_socioeconomico_codigo').on('keydown',function(){
  jQuery("#collapseThree").collapse('hide');
  jQuery("#collapseFour:not(.collapse.in)").collapse('show');
   jQuery('.ui-datepicker-trigger').focus();
 
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

/*********Período inicial***********/
jQuery.datepicker.regional['es'] =
  {
  currentText:'Hoy',
  closeText: 'Seleccionar',
  prevText: 'Próximo',
  nextText: 'Previo',
  monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
  monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
  'Jul','Ago','Sep','Oct','Nov','Dic'],
  monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año',
  yearRange: "-0:+1",
  initStatus: 'Selecciona la fecha', isRTL: false};

jQuery.datepicker.setDefaults(jQuery.datepicker.regional['es']);

jQuery('#mes_i').datepicker({
        dateFormat: 'MM yy',
        minDate: 0,//"dateToday",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        showOn: "button",
        buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
        beforeShow: function( input ) {
            setTimeout(function () {
                //jQuery(input).datepicker("widget").find(".ui-datepicker-current").hide();
                jQuery(input).datepicker("widget").find(".ui-datepicker-calendar").hide();
               /* var clearButton = $(input ).datepicker( "widget" ).find( ".ui-datepicker-close" );
                clearButton.unbind("click").bind("click",function(){$.datepicker._clearDate( input );});*/
            }, 1 );
        },
        onClose: function(dateText, inst) {

            var month = jQuery("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = jQuery("#ui-datepicker-div .ui-datepicker-year :selected").val();
            
            //seteo el año de inicio y de fin según el mes-año de inicio           
            seteoAnioInicioFin(Number(month), Number(year));

            var nombreCuarto ='';
            var mes_ini_trim_1=month;    
            var mes_fin_trim_1=Number(month)+2;
            
            if(Number(mes_ini_trim_1) == 11){
              mes_fin_trim_1=1;
            }
            
            if(Number(mes_fin_trim_1) == 12){
              mes_fin_trim_1=0;
            }else if(Number(mes_fin_trim_1) == 13){
              mes_fin_trim_1=1;
            }
            nombreCuarto = jQuery.datepicker.regional['es'].monthNames[mes_ini_trim_1] + "-" +jQuery.datepicker.regional['es'].monthNames[mes_fin_trim_1];
            jQuery('#tablita tr:nth-child(1) td:nth-child(4)').html(nombreCuarto);  
            var mes_ini_trim_2=Number(mes_fin_trim_1)+1;
            
            if(Number(mes_ini_trim_2) == 11){
              mes_fin_trim_2=1;
            }else if(Number(mes_ini_trim_2) == 12){
              mes_ini_trim_2=0;
            }
            var mes_fin_trim_2=Number(mes_ini_trim_2)+2;
            
            if(Number(mes_fin_trim_2) == 12){
              mes_fin_trim_2=0;
            }else if(Number(mes_fin_trim_2) == 13){
              mes_fin_trim_2=1;
            }
            nombreCuarto = jQuery.datepicker.regional['es'].monthNames[mes_ini_trim_2] + "-" +jQuery.datepicker.regional['es'].monthNames[mes_fin_trim_2];
            jQuery('#tablita tr:nth-child(2) td:nth-child(4)').html(nombreCuarto);  
            var mes_ini_trim_3=Number(mes_fin_trim_2)+1;
            
            if(Number(mes_ini_trim_3) == 11){
              mes_fin_trim_3=1;
            }else if(Number(mes_ini_trim_3) == 12){
              mes_ini_trim_3=0;
            }
            var mes_fin_trim_3=Number(mes_ini_trim_3)+2;
            
            if(Number(mes_fin_trim_3) == 12){
              mes_fin_trim_3=0;
            }else if(Number(mes_fin_trim_3) == 13){
              mes_fin_trim_3=1;
            }
            nombreCuarto = jQuery.datepicker.regional['es'].monthNames[mes_ini_trim_3] + "-" +jQuery.datepicker.regional['es'].monthNames[mes_fin_trim_3];
            jQuery('#tablita tr:nth-child(3) td:nth-child(4)').html(nombreCuarto);  
      
      
            var mes_ini_trim_4=Number(mes_fin_trim_3)+1;
            
            if(Number(mes_ini_trim_4) == 11){
              mes_fin_trim_4=1;
            }else if(Number(mes_ini_trim_4) == 12){
              mes_ini_trim_4=0;
            }
            var mes_fin_trim_4=Number(mes_ini_trim_4)+2;
            
            if(Number(mes_fin_trim_4) == 12){
              mes_fin_trim_4=0;
            }else if(Number(mes_fin_trim_4) == 13){
              mes_fin_trim_4=1;
            }
            nombreCuarto = jQuery.datepicker.regional['es'].monthNames[mes_ini_trim_4] + "-" +jQuery.datepicker.regional['es'].monthNames[mes_fin_trim_4];
            
            jQuery('#tablita tr:nth-child(4) td:nth-child(4)').html(nombreCuarto);  
          
            year = jQuery("#ui-datepicker-div .ui-datepicker-year :selected").val();
            jQuery(this).val(jQuery.datepicker.formatDate('MM yy', new Date(year, month, 1)));
            jQuery('#venta_1').focus();

            // si tiene el mensaje de ingrese un mes inicial lo sacamos
            jQuery('#div_mes_i').find('#po_mes_i').remove();
        }

    });
    
    /**Para que solo aparezca el mes y el año**/
    jQuery('#mes_i').focus(function () {
        jQuery(".ui-datepicker-calendar").hide();
        jQuery("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: jQuery(this)
        });
    });

 /**Popover con los mensajes de error**/ 
jQuery('#rubros_amigos').popover({
    trigger: "manual",
    placement: "right",
    content: "Solo Nº",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class='popover-inner' id=\"cartel_error\"><div class='popover-content'><p></p></div></div></div>",
}); 
jQuery('#rubros_otros').popover({
    trigger: "manual",
    placement: "right",
    content: "Solo Nº",
    template: "<div class='popover'><div class='arrow'></div><div class='popover-inner' id='cartel_error'><div class='popover-content'><p></p></div></div></div>",
});
jQuery('#persona_contacto_n').popover({
    trigger: "manual",
    placement: "right",
    content: "Campo obligatorio",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#cant_empleados').popover({
    trigger: "manual",
    placement: "right",
    content: "Sólo Nº",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#telefono_contacto').popover({
    trigger: "manual",
    placement: "right",
    content: "Campo obligatorio",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});

jQuery('#superficie').popover({
    trigger: "manual",
    placement: "top",
    content: "Opción no válida.",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#nivel_socioeconomico_codigo').popover({
    trigger: "manual",
    placement: "top",
    content: "Opción no válida.",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('.vidriera').popover({
    trigger: "manual",
    placement: "bottom",
    content: "Opción no válida.",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#ubicacion').popover({
    trigger: "manual",
    placement: "top",
    content: "Opción no válida.",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#foto_i').popover({
    trigger: "manual",
    placement: "top",
    content: "Seleccione una imagen.",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#foto_f').popover({
    trigger: "manual",
    placement: "top",
    content: "Seleccione una imagen.",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#caracteristicas').popover({
    trigger: "manual",
    placement: "right",
    content: "Seleccione al menos una opción.",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class='popover-inner car_zona' id='cartel_error'><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#venta_1').popover({
    trigger: "manual",
    placement: "left",
    content: "Solo Nº",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#venta_2').popover({
    trigger: "manual",
    placement: "left",
    content: "Solo Nº",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#venta_3').popover({
    trigger: "manual",
    placement: "left",
    content: "Solo Nº",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});
jQuery('#venta_4').popover({
    trigger: "manual",
    placement: "left",
    content: "Solo Nº",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});

jQuery('#mes_i').popover({
    trigger: "manual",
    placement: "top",
    content: "Ingrese un mes inicial",
    template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\" id=\"cartel_error\"><div class=\"popover-content\"><p></p></div></div></div>",
});

jQuery('#btn_volver').on('keydown click', function(e){
        var code = e.keyCode || e.which;

        if(e.type == "keydown"){
           if(code == 13) { //Enter keycode
            volver();
          }
        }else if(e.type == "click"){
            volver();
        }
        
    });





});//fin document ready

function seteoAnioInicioFin(mes, anio){
  //calculo en función del mes-anio inicial
  switch(true) {
    case mes==0:
        for (var i = 0; i <4; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio);
        }
        break;
    case (mes >= 1 && mes < 3):
        for (var i = 0; i <3; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio);
        }
        jQuery('#tablita tr:nth-child(4) td:nth-child(2)').html(anio);
        jQuery('#tablita tr:nth-child(4) td:nth-child(3)').html(anio+1);
        break;
    case (mes==3):
        for (var i = 0; i <3; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio);
        }
        jQuery('#tablita tr:nth-child(4) td:nth-child(2)').html(anio+1);
        jQuery('#tablita tr:nth-child(4) td:nth-child(3)').html(anio+1);
        break;
    case (mes >= 4 && mes <= 5):
        for (var i = 0; i <2; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio);
        }
        jQuery('#tablita tr:nth-child(3) td:nth-child(2)').html(anio);
        jQuery('#tablita tr:nth-child(3) td:nth-child(3)').html(anio+1);
        jQuery('#tablita tr:nth-child(4) td:nth-child(2)').html(anio+1);
        jQuery('#tablita tr:nth-child(4) td:nth-child(3)').html(anio+1);
        break;
    case (mes == 6):
        for (var i = 0; i <2; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio);
        }
        for (var i = 2; i <4; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio+1);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio+1);
        }
        break;
    case (mes >= 7 && mes <9):
        for (var i = 0; i <1; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio);
        }
        jQuery('#tablita tr:nth-child(2) td:nth-child(2)').html(anio);
        jQuery('#tablita tr:nth-child(2) td:nth-child(3)').html(anio+1);
        for (var i = 2; i <4; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio+1);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio+1);
        }
        break;
    case (mes==9):
        jQuery('#tablita tr:nth-child(1) td:nth-child(2)').html(anio);
        jQuery('#tablita tr:nth-child(1) td:nth-child(3)').html(anio);
        for (var i = 1; i <4; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio+1);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio+1);
        }
        break;
    case (mes>=10 && mes <=11):
        jQuery('#tablita tr:nth-child(1) td:nth-child(2)').html(anio);
        jQuery('#tablita tr:nth-child(1) td:nth-child(3)').html(anio+1);
        for (var i = 1; i <4; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio+1);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio+1);
        }
        break;
    default:
        for (var i = 0; i <4; i++) {
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(2)').html(anio+1);
          jQuery('#tablita tr:nth-child('+(i+1)+') td:nth-child(3)').html(anio+1);
        }
        
        break;
  }

}


function volver(){
  window.history.back();
}
