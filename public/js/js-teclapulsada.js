//funcion que gestiona el evento  
function TeclaPulsada( e ) { 
var evt = e || window.event; 
  /* if ( window.event !== null)               //IE4+  
      tecla = window.event.keyCode;  
   else if ( e !== null )                //N4+ o W3C compatibles  
      tecla = e.which;  
   else  
      return;  */
   if ( evt !== null )                //N4+ o W3C compatibles  
      tecla = evt.which;  
   else  
      return;
      
   if ((tecla === 13 || tecla === 9) && !e.shiftKey) {                  //se pulso enter/tab  --> y no shift+enter 
  
      if ( siguienteCampo === 'fin' ) {          //fin de la secuencia, hace el submit  
         return true;                   //sustituir por return true para hacer el submit  
		  } else if ( siguienteCampo === 'nada' ) { //da el foco al siguiente campo  
			 return true;  
			  } else {       //da el foco al siguiente campo  
            //alert('document.getElementById("'+nombreForm+'").'+siguienteCampo+'.focus()');
				 eval('document.getElementById("'+nombreForm+'").'+siguienteCampo+'.focus()');
				 return false;
            }  
   }  
}  
  
document.onkeydown = TeclaPulsada;          //asigna el evento pulsacion tecla a la funcion  
/*if (document.addEventListener) {            //netscape es especial: requiere activar la captura del evento  
    document.addEventListener(Event.KEYDOWN)  
	}*/
if (document.addEventListener) { //netscape es especial: requiere activar la captura del evento  
        document.addEventListener(Event.KEYDOWN, TeclaPulsada, false);
    } else if (document.attachEvent) { //firefox
        document.attachEvent("on" + Event.KEYDOWN, TeclaPulsada);
    }

