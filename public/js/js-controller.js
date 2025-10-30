// función ubica el link donde debe agregar la clase "active"
jQuery.noConflict(); 
jQuery(document).ready(function () {
	var newURL = window.location.protocol + "//" + window.location.host + "" + window.location.pathname;
       // alert(this.location.pathname);	 
	 // jQuery('a[href="' + this.location.pathname + '"]').parent().addClass('active');
	  jQuery('a[href="' + newURL + '"]').parent().addClass('active');
	});
   
   
//jQuery('#menu_top').hidden {display:none;}   
//h1.hidden {display:none;}