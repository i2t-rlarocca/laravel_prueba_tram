jQuery.noConflict(); 
var j = jQuery.noConflict(); 
j(document).ready(function () {
        j('ul.nav > li').click(function (e) {
            e.preventDefault();
            j('ul.nav > li').removeClass('active');
            j(this).addClass('active');                
        });            
    });