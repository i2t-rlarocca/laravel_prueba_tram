jQuery.noConflict(); 
jQuery(document).ready(function(){

	
	//btn de la pantalla de consulta
	jQuery('#btn-imprimir').click(function(event){
		var seleccionado = jQuery('#id_seleccionado').val();
		var tabla_datos = jQuery('#tabla-tramites tbody tr').size();
		
	     if(tabla_datos==0){
	        alert('No hay datos para imprimir');
	        return false;
	    }else{
	        jQuery.ajax({
	                    type: jQuery('.formulario-seleccionado').attr('method'),
                    	url: jQuery('.formulario-seleccionado').attr('action'),
                    	dataType:'json',
                    	data: jQuery('.formulario-seleccionado').serialize(),

	                    success: function (data) {
	                    	    informe_tramite();
	                        },
	                    error: function(data){
	                        alert("No se pudo encontrar la ruta al informe.");
	                    }
	            });
	        return false;
	        
    	}
    });



	});//fin document ready
  
	  
function informe_tramite()
{
url='informe-tramite-pdf';
window.open(url,"","width=1023,height=800");
}

 
/* function imprimir_reporte(){
    var seleccionado = document.getElementById('id_seleccionado').value;
    var tabla_datos = document.getElementById('tabla-tramites').getElementsByTagName("tbody")[0].getElementsByTagName("tr").length;
    if(seleccionado==''){
        alert('Debe seleccionar un premio');
        return false;
    }else if(tabla_datos==0){
        alert('Debe seleccionar un premio');
        return false;
    }else{
      imprimir(50);
    }

}*/

	
		
		
	