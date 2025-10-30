jQuery.noConflict(); 
jQuery(document).ready(function(){

	jQuery.validator.addMethod('trioDate', function(value,element) {
        dateDDMMYYYRegex = /^(0[1-9]|[12][0-9]|3[01])[- \/.](0[1-9]|1[012])[- \/.](19|20)\d\d$/;
        return this.optional(element) || value.match(dateDDMMYYYRegex);
    });
		
	jQuery.validator.addMethod("letrasynum", function(value, element) {
	 return this.optional(element) || /^[0-9A-ZÁÉÍÓÚÜñÑa-záéíóúü`´-.,: ]+$/i.test(value); 
	}, "Solo letras y numeros");
	
	jQuery("#formulario-motivo").validate({
	  rules: {
			motivo: {required:true,letrasynum:true}
			},
      messages: {
			motivo: {required:" * ",letrasynum:"Números y letras"}
			},
      errorElement: "error"
	});
	
			
		
	jQuery('#btn-aceptar').click(function(event){
		//verifica si fecha desde es menor a fecha hasta
		var fd = jQuery('#fecha_desde').val();
		var fh = jQuery('#fecha_hasta').val();
		var parts_fd = fd.split("/");
		var parts_fh = fh.split("/");
		var fd2= new Date(parts_fd[2], parts_fd[1] - 1, parts_fd[0]);
		var fh2= new Date(parts_fh[2], parts_fh[1] - 1, parts_fh[0]);
		var fechaHoy = new Date();
        fechaHoy = fechaHoy.getDate()+'/'+fechaHoy.getMonth()+'/'+fechaHoy.getFullYear();  
        if(fechaHoy<fd){
            alert("Fecha desde no puede ser mayor a la fecha actual");
            return false;
        }else if(fd2>fh2){
			alert('La fecha hasta no puede ser menor a fecha desde');
			return false;  
		}else{
		   // jQuery('#press-btn').val(1);
			jQuery('#formulario-motivo').submit();
			
		}
		});	
	});
  
function refrescar()
{
     //reset de todos los elementos del formulario 
   document.getElementById("formulario-motivo").reset();
   
}	  


  

	
		
		
	