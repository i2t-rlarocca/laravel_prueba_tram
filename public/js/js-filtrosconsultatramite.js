jQuery.noConflict(); 
jQuery(document).ready(function(){
  /*Para cuando carga la página*/
  jQuery(window).load(function(){
    var bottom = jQuery('#btn_consultar').offset().top + 85;
    jQuery('#sidebar-wrapper-vertical').css('height',bottom);
  });

  jQuery("#solapa-toggle-vertical").click(function(e) {
    e.preventDefault();
    jQuery("#sidebar-wrapper-vertical").toggleClass("active");
    jQuery("#solapa-toggle-vertical").toggleClass("active");
    if(jQuery("#solapa-toggle-vertical").hasClass("active"))  {
      jQuery(this).find('.glyphicon-chevron-down').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
      jQuery('#id_tramites_c').focus();
    }else{
      jQuery(this).find('.glyphicon-chevron-up').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
    }
    
  });

});//fin document ready

