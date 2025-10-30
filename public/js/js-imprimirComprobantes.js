 jQuery.noConflict(); 
jQuery(document).ready(function(){

	jQuery('#btn-imprimir-caratula').on('keydown click',function(e){
      var code = e.keyCode || e.which;

      if(e.type == "keydown" || e.type == "click"){
    	  caratula();
      }    	
  });
    jQuery('#btn-imprimir-nota').on('keydown click',function(e){
      var code = e.keyCode || e.which;

      if(e.type == "keydown" || e.type == "click"){
    	   nota();
      }
    });
 });//fin document ready

function caratula(){
	url='caratula-tramite-pdf';
	 window.open(url,'_blank');
}
 function nota(){
  url2='nota-solicitud-tramite-pdf';
  window.open(url2,'_blank2');
 }