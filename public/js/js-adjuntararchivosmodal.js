jQuery.noConflict();
jQuery(document).ready(function(){
var totalFiles = 0;
var myDropzone = new Dropzone("div#imagenes", {
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
    if(nrot != undefined){
      formData.append('nro_tramite', nrot);  
    }
    formData.append('permiso', permiso);
    formData.append('tipo_tramite', tt);
	formData.append('tipo_documento', tipoDoc);
	
	
	jQuery('#tipo_documento').val('0'); 
	jQuery('#btn_adjuntar').attr('disabled',true);
  },
  init: function() {
    var _this = this; //Capturar la instancia de Dropzone como una función
    var tt = jQuery('#nombre_tipo_tramite').val();
    var nrot = jQuery('#nro_tramite').val(); 
    var permiso = jQuery('#id_permiso').val();
	var tipoDocu = jQuery('#tipo_documento').val(); 
    this.on("addedfile", function(file) {
	
        totalFiles += 1;
        jQuery('#adjuntos').show();
        // Crear el botón para quitar los archivos
        var removeButton = Dropzone.createElement('<button><span class="glyphicon glyphicon-trash"></span></button>');
        //Se agrega escuchador para el botón de quitar archivo
        removeButton.addEventListener("click", function(e) {
          //Asegurarse de que el botón no haga un submit del form
          e.preventDefault();
          e.stopPropagation();
          //sobreescribimos la función confirm de dropzone
          Dropzone.confirm(_this.options.dictRemoveFileConfirmation, function() {
             
              //llamada para remover el archivo del servidor
              if(nrot != undefined){
                jQuery.ajax({
                    url: "eliminar_adjunto",
                    type: "POST",
                    dataType:'json',
                    data: { "file" : file.name, 'tipo_tramite':tt, 'nro_tramite':nrot, 'permiso':permiso }
                });
              }else{
                jQuery.ajax({
                    url: "eliminar_adjunto",
                    type: "POST",
                    dataType:'json',
                    data: { "file" : file.name, 'tipo_tramite':tt, 'permiso':permiso }
                });
              }

              // remover la preview del archivo.
              _this.removeFile(file);
              totalFiles -= 1;
    		  
                if (totalFiles === 0) {
                   jQuery('#adjuntos').hide();
                }
              });
                       
        });
        //Se agrega a la preview del archivo el botón para quitarlo
        file.previewElement.appendChild(removeButton);

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
          
        }, 
		error: function(){
			alert("error adjuntar");
		}
    });
        },// fin init
	error: function(){
		alert("error");
	}
    }); 
	
	jQuery("#tipo_documento").change(function(){
		var tipdoc = jQuery("#tipo_documento").val();
		// alert(tipdoc);
      if(tipdoc != 0) {
        jQuery('#btn_adjuntar').attr('disabled',false);
      }else{
		  jQuery('#btn_adjuntar').attr('disabled',true);
	  }
    });
	
});//fin document ready

function descarga(){
  url='descarga';
  window.location = url;
}




