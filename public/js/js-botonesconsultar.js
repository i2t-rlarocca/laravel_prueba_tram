jQuery.noConflict(); 
jQuery(document).ready(function(){

    jQuery(window).load(function(){

        if(jQuery('#abrirmodal').val()!=""){
            var url = "detalle-tramite-modal/"+jQuery('#abrirmodal').val();
            jQuery('#myModal').find('.modal-content').load(url);
            jQuery('#myModal').modal({ keyboard: false, backdrop:'static' });
            jQuery('#myModal').modal('show');
        }
    });

    jQuery('#btn-nuevo-tramite').click(function(){
        
        jQuery('#formulario-nuevo-tramite').submit();

    }); 

    /***********************************MODAL*************************************/
    jQuery('#btn-cargar-detalle').click(function(){
            var seleccionado = jQuery('#id_seleccionado').val();
            var tabla_datos = jQuery('#tabla-tramites >tbody >tr').length;
            
            if(seleccionado==''){
                alert('Debe seleccionar un trámite');
                return false;
            }else if(tabla_datos==0){
                alert('Debe seleccionar un trámite');
                return false;
            }else{
                    var url = "detalle-tramite-modal/"+seleccionado;
                    jQuery('#myModal').find('.modal-content').load(url);
                    jQuery('#myModal').modal({ keyboard: false, backdrop:'static' });
                    jQuery('#myModal').modal('show');

               }
    });
   
        
    /*Cuando el modal se cierra*/
    jQuery('#myModal').on('hidden.bs.modal', function(){
        jQuery(this).find('#hmodal').remove();
        jQuery(this).find('#bmodal').remove();
        jQuery(this).find('#fmodal').remove();
        jQuery(this).removeData('bs.modal').find('#contenedor_modal').html('');
        
        jQuery('#btn-imprimir-caratula').hide();
        jQuery('#btn-nota-solicitud').hide();
        jQuery('#btn-imprimir-resolucion').hide();
        jQuery('#formulario-tramite').submit();
    });
    
    /***********Historial del trámite**************/
    jQuery('#btn-historial-tramite').click(function(){
        
        var seleccionado = jQuery('#id_seleccionado_h').val();
        var tabla_datos = jQuery('#tabla-tramites >tbody >tr').length;
        
        if(seleccionado==''){
            alert('Debe seleccionar un trámite');
            return false;
        }else if(tabla_datos==0){
            alert('Debe seleccionar un trámite');
            return false;
        }else{
            var url="historial-estados-tramite/"+seleccionado;
             jQuery('#myModal').find('.modal-content').load(url);
            jQuery('#myModal').modal({ keyboard: false, backdrop:'static' });
            jQuery('#myModal').modal('show');
            
        }

    }); 
	

   /* jQuery('#myModal').on('hidden.bs.modal', function(){
        jQuery(this).find('#hmodal').remove();
        jQuery(this).find('#bmodal').remove();
        jQuery(this).find('#fmodal').remove();
        jQuery('#formulario-tramite').submit();
    });*/
    /***********Fin métodos historial*********/

    //btn de la pantalla de consulta
    jQuery('#btn-imprimir').click(function(){
        var seleccionado = jQuery('#id_seleccionado').val();
        var tabla_datos = jQuery('#tabla-tramites tbody tr').size();
        
         if(tabla_datos==0){
            alert('No hay datos para imprimir');
            return false;
        }else{
            jQuery.ajax({
                        type: jQuery('.formulario-impresion').attr('method'),
                        url: jQuery('.formulario-impresion').attr('action'),
                        cache: false,
                        async:false,
                        dataType:'json',
                        data: jQuery('.formulario-impresion').serialize(),

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

    jQuery('#btn-imprimir-caratula').click(function(){
        var seleccionado = jQuery('#id_seleccionado').val();
        var tabla_datos = jQuery('#tabla-tramites tbody tr').size();
        
         if(seleccionado==''){
            alert('Debe seleccionar un trámite');
            return false;
        }else if(tabla_datos==0){
            alert('Debe seleccionar un trámite');
            return false;
        }else{
            jQuery.ajax({
                        type: "POST",
                        url:'caratula-tramites',
                        cache: false,
                        async:false,
                        dataType:'json',
                        data: {'nro_tramite':seleccionado},

                        success: function (data) {
                                caratula_tramite();
                            },
                        error: function(data){
                            alert("No se pudo encontrar la ruta al informe.");
                        }
                });
            return false;
            
        }

    });

    jQuery('#btn-imprimir-resolucion').click(function(){
        var seleccionado = jQuery('#id_seleccionado').val();
        var tabla_datos = jQuery('#tabla-tramites tbody tr').size();
		
		var indiceFila = document.getElementById(seleccionado).rowIndex;
        var id_tipo_tramite = document.getElementById("tabla-tramites").rows[indiceFila].cells.item(11).innerHTML;

         if(seleccionado==''){
            alert('Debe seleccionar un trámite');
            return false;
        }else if(tabla_datos==0){
            alert('Debe seleccionar un trámite');
            return false;
        }else{
            jQuery.ajax({
                        type: "POST",
                        url:'resolucion-tramites',
                        cache: false,
                        async:false,
                        dataType:'json',
                        data: {'nro_tramite':seleccionado, 'id_tipo_tramite':id_tipo_tramite},

                        success: function (data) {
                                resolucion_tramite();
                            },
                        error: function(data){
                            alert("No se pudo encontrar la ruta al informe.");
                        }
                });
            return false;
            
        }

    });
    
    /*********************************************************
    * Botón para descarga de la nota de solicitud del trámite*
    **********************************************************/
    jQuery('#btn-nota-solicitud').click(function(){
        var seleccionado = jQuery('#id_seleccionado').val();
        var tabla_datos = jQuery('#tabla-tramites tbody tr').size();
        
         if(seleccionado==''){
            alert('Debe seleccionar un trámite');
            return false;
        }else if(tabla_datos==0){
            alert('Debe seleccionar un trámite');
            return false;
        }else{
            jQuery.ajax({
                        type: "POST",
                        url:'nota-solicitud-tramite',
                        dataType:'json',
                        data: {'nro_tramite':seleccionado},
                        async:false,
                        success: function (data) {
                                nota_solicitud_tramite();
                            },
                        error: function(data){
                            alert("No se pudo encontrar la ruta al informe.");
                        }
                });
            return false;
            
        }

    });

    /*********************************************************
    * Botón para descarga de la consulta en formato en .csv  *
    **********************************************************/
    jQuery('#btn-csv').click(function(){
        documento_csv();
    });
       
    /*****************************************************************************
    * Botón para redireccionar a la carga de datos de trámites de nuevo permiso  *
    ******************************************************************************/
    jQuery('#btn_completar_tramite').on('keydown click',function(){
        var seleccionado = jQuery('#id_seleccionado').val();
        var np=0;
        jQuery('#tabla-tramites tbody tr').each(function(clave,valor){
            var fila =jQuery(valor).find('#nro_seguimiento').html()
            if(Number(seleccionado)==Number(fila)){
                np=jQuery(valor).find('#nuevo_permiso').html();
                
            }
        });

        if(Number(np)==1){
            jQuery().submitCompletarTramite();
        }else{
            alertify.error("No es un trámite para un permiso nuevo.",3);
        }     

    });
    

    jQuery.fn.submitCompletarTramite = function(){
         var seleccionado = jQuery('#id_seleccionado').val();
         var input = jQuery("<input>").attr({"type":"hidden","name":"seleccionado", "id":"seleccionado"}).val(seleccionado);
         //agrego campo hidden al formulario
        jQuery('#formulario_completar_tramite').append(input);
         var hiddenElements = jQuery( "#formulario_completar_tramite" ).find( ":hidden" ).not( "script,td" );
        //agrego hiddens al formulario
        jQuery('#formulario_completar_tramite').append(hiddenElements);
        jQuery('#formulario_completar_tramite').submit();            
    }

});//fin document ready	
	


function documento_csv(){
    url='csv';
    window.location = url;
}
  
function informe_tramite()
{
    url='informe-tramite-pdf';
    window.open(url,'_blank');
}
        
function caratula_tramite(){
    url='caratula-tramite-pdf';
    window.open(url,'_blank');
}            
       
function nota_solicitud_tramite(){
    url='nota-solicitud-tramite-pdf';
    window.open(url,'_blank');
}             
        
function resolucion_tramite(){
    url='resolucion-tramite-pdf';
    window.open(url,'_blank');
}      
