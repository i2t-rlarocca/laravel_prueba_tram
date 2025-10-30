jQuery.noConflict(); 
jQuery(document).ready(function(){

    /**********************************************/
    /* Metodos para validación del formulario     */
    /**********************************************/
    jQuery.validator.addMethod("motivo", function(value, element) {
     return this.optional(element) || /^[A-ZÁÉÍÓÚÜñÑa-záéíóúü`´'-.,;:\/0-9º" "^!¡?¿@#$%&*()}{@#~]+$/i.test(value); 
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

    /***********************************************/
    /* Funciones para el formulario_dependencia    */
    /***********************************************/
    jQuery.fn.submitFormulario = function(nro_red, nro_subagente, modalidad) {
            jQuery.ajax({
                type:'POST',
                url:'control_red',
                dataType:'json',
                data: {'nro_red':nro_red, 'nro_subagente':nro_subagente, 'modalidad':modalidad, 'quieroSer':2},
                async:false,
                beforeSend: function(){
                  jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
                    jQuery('#cargandoModal').modal('show');
                },
                complete:function(){
                    jQuery('#cargandoModal').modal('hide');
                    jQuery('body').removeClass('modal-open');
                    jQuery('.modal-backdrop').remove(); 
                },
                success: function(data){
                
                    if(data['mensaje']){
                        jQuery('#mensaje').text(data['mensaje']);
                        jQuery('#errores').show();
                        jQuery('#errores').delay(2000).fadeOut();
                        jQuery('#nro_red').focus();
                        jQuery('#nro_red').val('');
                        jQuery('#nro_subagente').val('');
                        jQuery('#razon_social_nueva_red').val('');
                        jQuery('#div_razon_social').hide();
                    }else{
                        jQuery('#nro_subagente').focus();
						console.log('MM data: '+data);
                        jQuery('#razon_social_nueva_red').val(data['razon_social']['nombre_agencia']);
                        if(data['nro_subagente'] != 0)
                          jQuery('#nro_subagente').val(data['nro_subagente']);
                        jQuery('#div_razon_social').show();
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    jQuery('#mensaje').text("No existe la red.");
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();
                    jQuery('#nro_red').focus();
                    jQuery('#cargandoModal').modal('hide');
                }

            });
    };
    
     /******************************************************/
     /* Validación del formulario de cambio de dependencia */
     /******************************************************/

    jQuery("#formulario_dependencia").validate({
      rules: {
            nro_red: {required:true, number:true},
            buscarLocalidad: {required:function(){
                                          var esnuevo = jQuery('#es_nuevo_permiso').attr("value");
                                          return esnuevo;
                                      },localidad:true, maxlength:255},
            nro_subagente:{required:true, number:true},
            motivo_cr: {required: true, motivo:true}
            },
      messages: {
            nro_red: {required:" Debe ingresar un nº de red destino.", number:" Ingrese solo números"}, 
            buscarLocalidad: {required:"Debe seleccionar una localidad", localidad:"Debe seleccionar una localidad válida.", maxlength:"Ingrese hasta 255 caracteres."},
            nro_subagente: {required:"Debe indicar el nº de subagente.", number:"Ingrese solo números"}, 
            motivo_cr: {required: " Debe ingresar un motivo.", motivo:" Ingrese caracteres válidos."}           
            },
      errorElement: "error"
    });

    jQuery('#nro_red').keydown(function(e){
        var code = e.keyCode || e.which;
        var nro_red = jQuery('#nro_red').val();
        var red_actual = jQuery('#id_agente').val();
        var nroSubagente = jQuery('#nro_subagente').val();
        var modalidad = 0;

        if(nroSubagente==''){//todavía no cargó el nº de subagente
          nroSubagente = 0;
        }
        
        if((code == 13  || code==9)  && nro_red.length==4 && nro_red==red_actual) { //Enter keycode
            jQuery('#mensaje').text("Debe ingresar un nº de red diferente a la actual.");
            jQuery('#errores').show();
            jQuery('#errores').delay(2000).fadeOut();
        }else if((code == 13  || code==9)  && nro_red.length==4 && nro_red!=red_actual){
          e.preventDefault;
           jQuery(this).submitFormulario(nro_red, nroSubagente, modalidad); 
        }else if(nro_red.length<4 || code == 8){//code==8->borrado
            jQuery('#razon_social_nueva_red').val('');
            jQuery('#div_razon_social').hide();
        }
     });

    jQuery('#nro_red').on("focusout",function(e){
      var nro_red = jQuery('#nro_red').val();
      var red_actual = jQuery('#id_agente').val();
      var nroSubagente = jQuery('#nro_subagente').val();
      var modalidad = 0;

      if(nroSubagente==''){//todavía no cargó el nº de subagente
        nroSubagente = 0;
      }

      if(nro_red!='' && nro_red.length==4 && nro_red!=red_actual){
        jQuery(this).submitFormulario(nro_red, nroSubagente, modalidad);    
      }else if(nro_red.length==4 && nro_red==red_actual){
        jQuery('#mensaje').text("Debe ingresar un nº de red diferente a la actual.");
        jQuery('#errores').show();
        jQuery('#errores').delay(2000).fadeOut();
      }else{
         jQuery('#mensaje').text("Debe ingresar un nº de red.");
         jQuery('#errores').show();
         jQuery('#errores').delay(2000).fadeOut();
      }
            
    });

   

	jQuery('#formulario_dependencia').on('keydown click','#btn_cargarPlan',function(e){
	// alert('llega1');
        var code = e.keyCode || e.which;
        if(e.type == "keydown"){
           if(code == 13) { //Enter keycode
            jQuery("#continuar").val(1);
			
            jQuery(this).submitFormDependencia();
          }
        }else if(e.type == "click"){
            jQuery("#continuar").val(1);
            jQuery(this).submitFormDependencia();
        }
          
        return false;

	});

     jQuery('#formulario_dependencia').on('keydown click','#btn_iniciar',function(e){
        var code = e.keyCode || e.which;
        if(e.type == "keydown"){
           if(code == 13) { //Enter keycode
            jQuery(this).submitFormDependencia();
          }
        }else if(e.type == "click"){
            jQuery(this).submitFormDependencia();
        }
        
        return false;
     });

	  jQuery.fn.submitFormDependencia = function(){
      var valido = jQuery('#formulario_dependencia').validate().form();
      jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
        jQuery('#cargandoModal').modal('show');
        
		var rz = jQuery('#razon_social_nueva_red').val();
		// alert(rz);
        if(valido && rz.length!=0){
            jQuery('#btn_iniciar').prop( "disabled", true );
			jQuery('#btn_cargarPlan').prop( "disabled", true );
            jQuery('#btn_volver').prop( "disabled", true );
            jQuery('#btn-limpiar').prop( "disabled", true );
            jQuery('#btn_adjuntar').prop( "disabled", true );
            jQuery('#formulario_dependencia').submit();
        }else{
          jQuery('#cargandoModal').modal('hide');
        }
    }
	 
    /**
      * Función para buscar la red de la que pasará a ser subagente
      **/
     jQuery.fn.submitSubagente = function(nro_red, nro_subagente) {
      jQuery.ajax({
                type:'POST',
                url:'control_subagente', 
                dataType:'json',
                data: {'nro_red':nro_red, 'nro_subagente':nro_subagente},
                async:false,
                beforeSend: function(){
                    jQuery('#cargandoModal').removeData("modal").modal({backdrop: 'static', keyboard: false});
                    jQuery('#cargandoModal').modal('show');
                },
                complete:function(){
                    jQuery('#cargandoModal').modal('hide');
                    jQuery('body').removeClass('modal-open');
                    jQuery('.modal-backdrop').remove(); 
                },
                success: function(data){
                    
                    if(data['mensaje']){
                        jQuery('#mensaje').text(data['mensaje']);
                        jQuery('#errores').show();
                        jQuery('#errores').delay(2000).fadeOut();
                        jQuery('#nro_subagente').get(0).focus();
                        jQuery('#nro_subagente').val('');
                    }else{
                        jQuery('#motivo_cr').get(0).focus();
                }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    jQuery('#mensaje').text("Problema con el nº de subagente.");
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();
                    jQuery('#nro_subagente').get(0).focus();
                    jQuery('#cargandoModal').modal('hide');
    }
    
    });  
    
     };

    jQuery('#nro_subagente').on("focusout",function(e){
        var nro_red = jQuery('#nro_red').val();
        var subagente = jQuery('#nro_subagente').val();
        if(subagente!=''  && nro_red != ''){//todavía no cargó el nº de subagente
            jQuery(this).submitSubagente(nro_red, subagente);
        }else if(nro_red == ''){
          jQuery('#mensaje').text("Ingrese un nº de red.");
          jQuery('#errores').show();
          jQuery('#errores').delay(2000).fadeOut();
        }
});

    jQuery('#nro_subagente').on("keydown",function(e){
        var code = e.keyCode || e.which;
        var nro_red = jQuery('#nro_red').val();
        var subagente = jQuery('#nro_subagente').val();
        if((code == 13 || code == 9) && nro_red != ''){
              if(subagente!='' && nro_red != ''){//todavía no cargó el nº de subagente
                  jQuery(this).submitSubagente(nro_red, subagente);
}  
        }else if(nro_red == ''){
          jQuery('#mensaje').text("Ingrese un nº de red.");
          jQuery('#errores').show();
          jQuery('#errores').delay(2000).fadeOut();
}
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
   document.getElementById('div_razon_social').style.display="none";
   document.getElementById('nro_red').focus();
}  

function volver(){
  url='tramites';
  window.location = url;
}

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
  


	
		
		
	