jQuery.noConflict();
jQuery(document).ready(function(){

var totalFiles = 0;
var fileExtension_ff ="";

Dropzone.options.imagenes = {
  acceptedFiles:".odt,.png,.gif,.jpeg,.jpg,.txt,.pdf,.doc,.rtf,.docx,.ods,.bmp,.rar,.zip,.xls",
  dictRemoveFileConfirmation:'¿Desea eliminar el archivo seleccionado?',
  clickable: "#btn_adjuntar", //si quiero un botón en lugar de que apriete o desplace sobre la zona
  url:'adjuntar',
  
  //previewTemplate: '<div id="dz-preview-template" class="dz-preview dz-file-preview"><div class="dz-details">       <div class="dz-filename"><span data-dz-name></span></div><div class="dz-size" data-dz-size></div><img data-dz-thumbnail /></div><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div></div>',
  sending: function(file, xhr, formData) {
	  
			var tt=jQuery('#nombre_tipo_tramite').val();
			var nrot = jQuery('#nro_tramite').val();
			var permiso = jQuery('#id_permiso').val(); 
			var tipoDoc = jQuery('#tipo_documento').val(); 
			var sel = document.getElementById("tipo_documento");
			if(nrot != undefined){
			  formData.append('nro_tramite', nrot);   
			}
			formData.append('permiso', permiso);
			formData.append('tipo_tramite', tt);
			
			formData.append('tipo_documento', tipoDoc);
			
			var current_location = jQuery(location).attr('pathname');

			if((current_location.indexOf("detalle")>=0) && (jQuery('#estadoI').val() == 2 ||  jQuery('#estadoI').val() == 3 || jQuery('#estadoI').val()==5)){
				jQuery('#modal_adjunto_ok').modal({ keyboard: false, backdrop:'static' });
				jQuery('#modal_adjunto_ok').modal('show');
			}

			// luego de adjuntar el archivo vuelve el selector para que lo seleccione el usuario 
			// y se deshabilita el boton adjunto hasta que el selecto cambie.
			sel.remove(sel.selectedIndex);
			jQuery('#tipo_documento').val('0'); 
			jQuery('#btn_adjuntar').attr('disabled',true);
			// removerItemListaDesplegable(tipoDoc);
			
  },
  init: function() {
    
    var _this = this; //Capturar la instancia de Dropzone como una función
    var tt = jQuery('#nombre_tipo_tramite').val();
    var nrot = jQuery('#nro_tramite').val(); 
    var permiso = jQuery('#id_permiso').val(); 
	
	
	
    this.on("addedfile", function(file) {
		
		// alert('MM'+tipoDocu);
		
		
        totalFiles += 1;
        jQuery('#adjuntos').show();
        // Crear el botón para quitar los archivos
        var removeButton = Dropzone.createElement('<button id="'+totalFiles+'"><span class="glyphicon glyphicon-trash" data-dz-remove></span></button>');

        //Se agrega escuchador para el botón de quitar archivo
        removeButton.addEventListener("click", function(e) {
           //Asegurarse de que el botón no haga un submit del form
          e.preventDefault();
          e.stopPropagation();
		  
          Dropzone.confirm(_this.options.dictRemoveFileConfirmation, function() {
			  var tipoDocumentoC = jQuery('#tipoDocumentoC'+totalFiles).val(); 

				// console.log("tipoDocumentoC: "+tipoDocumentoC);
				
            //llamada para remover el archivo del servidor
            if(nrot != undefined){
              jQuery.ajax({
                  url: "eliminar_adjunto",
                  type: "POST",
                  dataType:'json',
                  data: { "file" : file.name, 'tipo_tramite':tt, 'nro_tramite':nrot, 'permiso':permiso,'tipoDocumento':tipoDocumentoC }
              });
            }else{
              jQuery.ajax({
                  url: "eliminar_adjunto",
                  type: "POST",
                  dataType:'json',
                  data: { "file" : file.name, 'tipo_tramite':tt, 'permiso':permiso,'tipoDocumento':tipoDocumentoC }
              });
            }

            // remover la preview del archivo.
            _this.removeFile(file);
            totalFiles -= 1;
              if (totalFiles === 0) {
                 jQuery('#adjuntos').hide();
				 // 

              }
          });

        });
        //Se agrega a la preview del archivo el botón para quitarlo
		// alert('agrega boton - 2');
        file.previewElement.appendChild(removeButton);
		// var tipoDocu = jQuery('#tipo_documento').val(); 
		var tipoDocu = jQuery("#tipo_documento option:selected" ).attr('value');
		// removerItemListaDesplegable(tipoDocu);
		// console.log('tipoDocu '+tipoDocu);
		// Se agrega tipo documento al
		var tipoDocument = Dropzone.createElement('<input class="tipoDocumentoC" type="hidden" value="'+tipoDocu+'"  id="tipoDocumentoC'+totalFiles+'"  name="tipoDocumentoC"></input>');
		file.previewElement.appendChild(tipoDocument);
		
		
        if(nrot != undefined){
          var downloadButton = Dropzone.createElement('<button><span class="glyphicon glyphicon-floppy-save"></span></button>');
          //Se agrega escuchador para el botón de descargar archivo
          downloadButton.addEventListener("click", function(e) {
            //Asegurarse de que el botón no haga un submit del form
            e.preventDefault();
            e.stopPropagation();

            //llamada para descargar el archivo del servidor
            jQuery.ajax({
                url: "descargar_adjunto",
                type: "POST",
                dataType:'json',
                data: { "nombre_archivo" : file.name, 'tipo_tramite':tt, 'num_tramite':nrot, 'permiso':permiso },
                success: function(data){
                  if(data=='OK'){
					// alert('es aqui');
                    descarga();   
                  }else{
                    alert("Error al intentar descargar el adjunto.");
                  }                 
                },
                error: function(data){
                  alert("Error al intentar descargar el adjunto.");
                }
            });         
          });
          //Se agrega a la preview del archivo el botón para descargarlo
          file.previewElement.appendChild(downloadButton);
		 
        } 
		
		
      });
    //carga adjuntos actuales
    jQuery.ajax({
        url: "adjuntos_actuales",
        type: "POST",
        dataType:'json',
        data: { 'tipo_tramite':tt, 'nro_tramite':nrot, 'permiso':permiso },
        success: function(data){          
            jQuery.each(data, function(key,value){
                  jQuery.each(value, function(key,value){
                    var mockFile = { name: value.name, size: value.size };
                    _this.emit("addedfile", mockFile); 
                    _this.emit("thumbnail", mockFile, value.ruta);
                  });
              });
               jQuery('#adjuntos').show();
			 
          
        }
    });
// removerItemListaDesplegable();
        },//fin init
	error:function(file){
		
	jQuery('#mensaje').text("No es un archivo válido.");
    jQuery('#errores').show();
    jQuery('#errores').delay(2000).fadeOut();
		var _this = this;
	 // remover la preview del archivo.
       _this.removeFile(file);
		totalFiles -= 1;
		if (totalFiles === 0) {
		   jQuery('#adjuntos').hide();
		}
	}

    };    

     jQuery("#tipo_documento").change(function(){
		var tipdoc = jQuery("#tipo_documento").val();
		// alert(tipdoc);
      if(tipdoc != 0) {
        jQuery('#btn_adjuntar').attr('disabled',false);
		// removerItemListaDesplegable();
      }else{
		  jQuery('#btn_adjuntar').attr('disabled',true);
	  }
    });
});//fin document ready

function descarga(){
  url='descarga';
  window.location = url;
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
            // jQuery('#mensaje').text("No es una imagen valida.");
            // jQuery('#mensaje').show();
            // jQuery('#errores').show();
            // jQuery('#errores').delay(1000).fadeOut();
            return false;
        break;
    }
}

// function xx(pTipoDocumento)
function removerItemListaDesplegable(tipoDocu) {
            let aTipoDocumento = document.getElementById('tipo_documento');
            // let aTipoDocumento = tipoDocu;
			// console.log('aTipoDocumento:'+aTipoDocumento);
            aTipoDocumento.remove(tipoDocu.selectedIndex);
			
				// var list = [aTipoDocumento];
			  // jQuery('#tipo_documento select option').filter(function() { 
				// return !(jQuery.inArray(this.value, list) == -1);
			  // }).remove();
        }




