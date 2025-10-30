jQuery.noConflict();
jQuery(document).ready(function(){



jQuery(".messages").hide();
    //queremos que esta variable sea global
    var fileExtension = "";
    //función que observa los cambios del campo file y obtiene información
/*jQuery(':file').change(function()
    {
        var file = jQuery("file#imagen")[0].files[0];
        //obtenemos el nombre del archivo
        var fileName = file.name;
        //obtenemos la extensión del archivo
        fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        //información del formulario
        //var formData = new FormData(jQuery(".formulario")[0]);
        var message = ""; 
        //hacemos la petición ajax  
        jQuery.ajax({
            url: 'foto-frente',  
            type: 'POST',
            // Form data
            //datos del formulario
            data: jQuery('#foto_f').serialize(),//formData,
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
                if(isImage(fileExtension))
                {alert(data);
                    jQuery(".showImage").html("<img src='upload/"+data+"' />");
                }
            },
            //si ha ocurrido un error
            error: function(){
                message = jQuery("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });//fin ajax
    });

 
    //como la utilizamos demasiadas veces, creamos una función para 
    //evitar repetición de código
    function showMessage(message){
        jQuery(".messages").html("").show();
        jQuery(".messages").html(message);
    }*/
  var fileExtension_ff ="";
jQuery('#foto_f').on('change', function(){ 
        var val = jQuery(this).val();
         fileExtension_ff = val.substring(val.lastIndexOf('.') + 1);
        var data= new FormData(jQuery('#formulario_plan_estrategico')[0]);
        if(isImage(fileExtension_ff)){
            jQuery.ajax({
                type: 'post',
                url: 'foto-frente',
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
                  /*  if(isImage(fileExtension_ff))
                    {*/
                       jQuery("#vista_previa_ff").attr('src',data['path_ff']);
                   // }
                },
                //si ha ocurrido un error
                error: function(){
                    jQuery('#mensaje').text("Ha ocurrido un error al cargar la foto.");
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();
                }
            });//fin ajax
        }else{
            jQuery('input#foto_f').val("");
            jQuery("#vista_previa_ff").attr('src','images/no-imagen.jpg');
        }
     });


 var fileExtension_fi="";
jQuery('#foto_i').on('change', function(){ 
        var val = jQuery(this).val();
         fileExtension_fi = val.substring(val.lastIndexOf('.') + 1);
        var data= new FormData(jQuery('#formulario_plan_estrategico')[0]);
        if(isImage(fileExtension_fi)){
            jQuery.ajax({
                type: 'post',
                url: 'foto-interior',
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
                   /* if(isImage(fileExtension_fi))
                    {*/
                       jQuery("#vista_previa_fi").attr('src',data['path_fi']);
                   // }
                },
                //si ha ocurrido un error
                error: function(){
                    jQuery('#mensaje').text("Ha ocurrido un error al cargar la foto.");
                    jQuery('#errores').show();
                    jQuery('#errores').delay(2000).fadeOut();
                }
            });//fin ajax
        }else{
            jQuery('input#foto_i').val("");
            jQuery("#vista_previa_fi").attr('src','images/no-imagen.jpg');
        }
     });


     

});//fin document ready

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
