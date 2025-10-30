jQuery.noConflict(); 
jQuery(document).ready(function(){
       
        jQuery('#a_ventas').click(function(){
			var ancho = jQuery("#tabla_ventas th:last").outerWidth(true);

			jQuery('#tabla_ventas tbody>tr>td').each(function() {
    			//jQuery(this).css('width',ancho);
    			//jQuery('table tr td').eq(this).css('width',ancho+"px");
    			jQuery(this).width(ancho+"px");
			});  
    

		});
        
});