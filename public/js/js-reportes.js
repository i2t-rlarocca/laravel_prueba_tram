/*function sizeFrame() {
	var F = document.getElementById("frame");
	if(F.contentDocument) {
		F.height = F.contentDocument.documentElement.scrollHeight+30; //FF 3.0.11, Opera 9.63, and Chrome
	}else {
		F.height = F.contentWindow.document.body.scrollHeight+30; //IE6, IE7 and Chrome
	}
}

window.onload=sizeFrame;*/



jQuery.noConflict(); 
jQuery(document).ready(function(){
  /*Para cuando carga la página*/
  jQuery(window).load(function(){
    var bottom = jQuery('#frame').offset().top ;
    //alert(bottom);
    //jQuery('#frame').css('height',bottom);
  });



});//fin document ready
