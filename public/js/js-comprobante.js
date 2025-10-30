jQuery.noConflict(); 
jQuery(document).ready(function(){
       
        jQuery('#btn-imprimir-caratula').on('click',function(){
        	caratula();
        	
        });
        jQuery('#btn-imprimir-nota').on('click',function(){
        	nota();
        });
    });

 function caratula_nota(){
 	url='caratula-tramite-pdf';
    url2='nota-solicitud-tramite-pdf';
    window.open(url2,'_blank');
    window.open(url,'_blank');
 }

function caratula(){
	url='caratula-tramite-pdf';
	 window.open(url,'_blank');
}
 function nota(){
  url2='nota-solicitud-tramite-pdf';
  window.open(url2,'_blank2');
 }