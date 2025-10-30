//validar cuit
jQuery.noConflict(); 
jQuery(document).ready(function(){
   jQuery.validator.addMethod(
        'cuit', 
		function validaCuit(value) {

			/* Valido los primeros 2 digitos del cuit segun el tipo de persona */
			var	comienzoCUIT = value.substring(0,2);
			var tipo_per = jQuery('#tipo_persona').val();
			var sexo_per = jQuery("input[name='sexo_persona']:checked").val();

			if (tipo_per == "J") {//pj
				if (comienzoCUIT != 30 && comienzoCUIT != 33 && comienzoCUIT != 34) {

				return false; 						
				}
			} else {
				if(value.length>0){
					if (sexo_per == "F") {
						if (comienzoCUIT != 27 && comienzoCUIT != 23 && comienzoCUIT != 24) {
							return false; 				
						}				
					} else {
						if (comienzoCUIT != 20 && comienzoCUIT != 23 && comienzoCUIT != 24) {
							return false;				
						}
					}
				}else{
					return true;
				}
			}

			if (value.indexOf("-") >= 1){
			value=value.replace('-', '');
			value=value.replace('-', '');
			}
			var aMult = '5432765432'; 
			var aMult = aMult.split(''); 
			var sCUIT = value; 
			var fcuit = value; 
			if (sCUIT=="00000000000" || sCUIT=="23000000000" || sCUIT.indexOf(' ') >= 0) {return false;}
			if (sCUIT && sCUIT.length == 11) 
			{ 
				aCUIT = sCUIT.split(''); 
				var iResult = 0; 
				for(i = 0; i <= 9; i++) 
				{ 
					iResult += aCUIT[i] * aMult[i]; 
				} 
				iResult = (iResult % 11); 
				iResult = 11 - iResult; 
				 
				if (iResult == 11) iResult = 0; 
				if (iResult == 10) return false; 
				 
				if (iResult == aCUIT[10]) 
				{ 
					
					jQuery("#cuit").val(sCUIT.substring(0,2)+"-"+sCUIT.substring(2,10)+"-"+sCUIT.substring(10,13));
					
					return sCUIT;
							
				
				} 
			}     
						
			return false; 
		},
        "Cuit inválido"
    );
	
	
	
});

