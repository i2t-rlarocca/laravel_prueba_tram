<?php

namespace Presentacion\Premios;

class Formatos {
    
    /**
    * Función que formatea la fecha para
    * ser colocada en la forma d/m/y
    * o de d/m/y a aaaammdd/aaaa-mm-dd
    */
    public function fecha($fecha) {       

        if ($fecha == '') {    
            return $fecha;
        }        
        
        if ($fecha instanceof \Carbon\Carbon) {            
            return $fecha->format('d/m/Y');        
        } 
        
        if ($fecha instanceof \DateTime) {        
            return date_format($fecha, 'd/m/Y');
        }
        
        //si la fecha tiene la forma dd/mm/yyyy
        if(preg_match("/^\d{1,2}\/\d{1,2}\/\d{4}$/", $fecha)==1){
          list($dia, $mes, $anio) = explode("/", $fecha);

          return $anio."-".$mes."-".$dia;
        }

        //si la fecha tiene la forma yyyy-mm-dd
        if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $fecha)==1){
          
          list($anio, $mes, $dia) = explode("-", $fecha);

          return $dia."/".$mes."/".$anio;
        }

        //fecha aaaammdd
        if(strlen($fecha)==8){
          $dia = substr($fecha, 6,2);
          $mes = substr($fecha,4,2);
          $anio = substr($fecha,0,4);
          return $dia."/".$mes."/".$anio;
        }

        return $fecha;
        
    }

    function fechaAS($fecha){
      //si la fecha tiene la forma dd/mm/yyyy
        if(preg_match("/^\d{1,2}\/\d{1,2}\/\d{4}$/", $fecha)==1){
          list($dia, $mes, $anio) = explode("/", $fecha);

          return $anio.$mes.$dia;
        }
    }
    
    function decrypt($string, $key) {

       $result = '';
       $string = base64_decode($string);
       for($i=0; $i<strlen($string); $i++) {
          $char = substr($string, $i, 1);
          $keychar = substr($key, ($i % strlen($key))-1, 1);
          $char = chr(ord($char)-ord($keychar));
          $result.=$char;
       }
       return $result;
    }
	
	function encrypt($string, $key) {
	   $result = '';
	   for($i=0; $i<strlen($string); $i++) {
		  $char = substr($string, $i, 1);
		  $keychar = substr($key, ($i % strlen($key))-1, 1);
		  $char = chr(ord($char)+ord($keychar));
		  $result.=$char;
	   }
	   return base64_encode($result);
	}

    public function armaNumeroComprobante($letra, $ptoVta, $numero){
        $pto_vta=str_pad($ptoVta, 4, "0", STR_PAD_LEFT); 
        $nc = str_pad($numero, 8, "0", STR_PAD_LEFT); 
        return $numeroComprobante = $letra[0].$pto_vta.'-'.$nc;
    }


    public function armaCuit($cuit){
        $cuit_1 = substr($cuit,0,2);
        $cuit_2 = substr($cuit,2,8);
        $cuit_3 =substr($cuit,10,10);
        return $cuit_1."-".$cuit_2."-".$cuit_3;
    }

    public function desarmaCuit($cuit){
        $cuitPartes = explode("-", $cuit);
        return $cuitPartes[0].$cuitPartes[1].$cuitPartes[2];
    }

    public function colocaValor($valor){
        if($valor==0 || is_null($valor)){
              return "0,00";
        }else{
              return number_format ($valor , 2 , "," , ".");  
        }
    }
}

?>