<?php namespace app\Extension\Validation;
 
class CustomValidator extends \Illuminate\Validation\Validator {
 
 /**
	El nombre del método debe incluir validate como prefijo.
	=>al método lo vamos a llamar Cuit cuando lo usemos en las reglas
 **/
  public function validateCuit($attribute, $value, $parameters)
  {
	  if (stripos($value,"-") !== false){
	     $value=str_replace('-', '',$value);
	  }
	  
	  $aMult = '5432765432'; 
	  $aMult = str_split($aMult); 
	  $sCUIT = $value; 
	  
	  if ($sCUIT=="00000000000" || $sCUIT=="23000000000" || stripos($sCUIT,' ') !== false) {return false;}
      if ($sCUIT && strlen($sCUIT) == 11) 
      { 
        $aCUIT = str_split($sCUIT); 
       
        $iResult = 0; 
        for($i = 0; $i <= 9; $i++) 
        { 
          $iResult += $aCUIT[$i] * $aMult[$i]; 
        } 
        $iResult = ($iResult % 11); 
        $iResult = 11 - $iResult; 
         
        if ($iResult == 11) $iResult = 0; 
        if ($iResult == 10) return false;          
        if ($iResult == $aCUIT[10]) { return true;} 
      }     
            
      return false; 
  }

 /**
  * Valida el tipo de persona F/J y el cuit en función de ello.
  */
  public function validateTipoPersona ($attribute, $value, $parameters){
    $comienzoCUIT = substr($parameters[0], 0,2 );
    
    $sexo_per = $parameters[1];
   
    if ($value == "J") {//juridica
      if ($comienzoCUIT != 30 && $comienzoCUIT != 33 && $comienzoCUIT != 34) {
        return false;             
      }else{
          return true;
        }  
    } else { 
      if ($sexo_per == "F") {//femenino
        if ($comienzoCUIT != 27 && $comienzoCUIT != 23 && $comienzoCUIT != 24) {
          return false;         
        }else{
          return true;
        }       
      } else {//masculino
        if ($comienzoCUIT != 20 && $comienzoCUIT != 23 && $comienzoCUIT != 24) {
          return false;       
        }else{
          return true;
        }  
      }
    }  
  }


  /**
  * Valida el tipo mime del archivo adjunto
  **/  
  public function validateTipoMimeReal($attribute, $file, $parameters)
  {
      return Validator::make(
          [
              'file' => $file,
              'extension'  => \Str::lower($file->getClientOriginalExtension()),
          ],
          [
              'file' => 'required|max:20000',
              'extension'  => 'required|in:odt,png,gif,jpeg,jpg,txt,pdf,doc,rtf,docx,ods,bmp,rar,zip',
          ],
          $this->validationMessages()
      );          
  } 


  /**
  * Valida el formato del documento
  **/  
  public function validateDocumento($attribute, $documento, $parameters)
  {

      $doc_ptos='/^[1-9]{1}\d{0,1}[.]\d{3}[.]\d{3}$/';

      if($documento=='0.000.000' || $documento=='00.000.000' || $documento=='1.111.111' || $doc_ptos=='11.111.111'){
        return false;
      }
      if(strlen($documento)==9 || strlen($documento)==10){
        if(preg_match($doc_ptos, $documento)){
          return true;
        }
      }
      
      return false;        
  } 

  /**
  * Valida el formato de la fecha de nacimiento
  **/  
  public function validateFechaNacimiento($attribute, $fecha, $parameters)
  {
    if(preg_match("/^\d{1,2}\/\d{1,2}\/\d{4}$/", $fecha)){
      return true;
    }else{
      return false;
    }

  }
/*******************IIBB***************************************/
  /**
  * Valida el formato del nº de ingresos brutos
  **/
  public function validateIngresosBrutos($attribute, $nroIngresos, $parameters){

    list($primera,$segunda,$tercera) = explode("-",$nroIngresos);

    $codigo= explode("-",$nroIngresos);
    $codigosinguiones="";
    foreach($codigo as $partes){
      $codigosinguiones=$codigosinguiones.$partes;
    } 

	\Log::info("MM - inicia validaciones");

    //IIBB tiene 10 posiciones numericas
    if (! is_numeric($codigosinguiones)){
      \Log::info("no tiene 10 posiciones");
      return false;
    }
    //Verifica que no sean ceros (0) de la posición 4 a la 9   
    if ($segunda<=0){
      \Log::info("0 de la 4 a la 9");
      return false;
    }
    //Verifica existencia Delegación en la Tabla
    if (!($this->existe_delegacion($primera))){
      \Log::info("no existe la delegación");
      return false;
    }

    if (($primera>=901) && ($primera<=954)){
        if($this->verifica_digito_901($codigosinguiones))
        {
          return true;
        }
    }else{
      if($this->verifica_digito_400($codigosinguiones))
      {
        return true;
      }
    }
	 \Log::info("MM - customValidator - validateIngresosBrutos");
    return false;
  }



private function verifica_digito_400($codigosinguiones){
  // toma el ultimo nro del codigo, que es el digito verificador
  $digitoverificador=substr($codigosinguiones,9,1);
  
  $b=0;
  $dc=0;
  $resto=0;
  $pond="";
  //obtener el resto,siemre esta hardcodead como  9/4 (viene desde as400)
  $resto=9%4;
  switch ($resto) {
  case 0:
    $pond="1973";
    break;
  case 1:
    $pond="3197";
    break;
  case 2:
    $pond="7319";
    break;
  case 3:
    $pond="9731";
    break;
    
  } 
  
  $suma=0;
  for ($a=1;$a<=9;$a++){
    $c=substr($codigosinguiones,$a-1,1);
    $resto=$a%4;
    //$dividendo=$a/4;
    $resto=$resto+1;
    $resultado=substr($pond,$resto-1,1);
    $b=$resultado*$c;
    // toma el ultimo numero si es maroy a 10
    if (strlen($b)>1){
      $b=substr($b,1,1);
    }
    $suma=$suma+$b;
    $respuesta = array("C"=>$c, "RESTO"=>$resto,"POND"=>$resultado,"B"=>$b,"SUMA"=>$suma);
  }
  
  $restoGral=$suma%10;
  if ($restoGral<>0){
    $digito=10-$restoGral;
  }else
  {
    $digito=$restoGral;
  }
  
  if($digito!=$digitoverificador){
    return false;
  }else{
    return true;
  }
}
private function verifica_digito_901($codigosinguiones){
  
  $digitoverificador=substr($codigosinguiones,9,1);
  $suma=0;
  $dc=0;
  $pond="3971397139713";
  for($a=4;$a<=9;$a++){
    $c=substr($codigosinguiones,$a-1,1);
    $b=substr($pond,$a-1,1);
    $res=($c*$b);
    $suma=$suma+$res;
    $respuesta= array("C"=>$c,"B"=>$b,"SUMA"=>$suma,"multiplica"=>$res);
  }
  $resto=$suma%11;
  $digito=11-$resto;
  If ($digito==11){
    $digito=11;
    
  }else{
    If ($digito==10){
      $digito=0;    
    }
  }
  
  if($digito!=$digitoverificador){
    return false;
  }else{
    return true;
  }
}

private function existe_delegacion($delegacion){
  $row =  \DB::connection('basica')->select(\DB::raw('SELECT  count(*) as cantidad  FROM delegaciones_IIBB  WHERE  codigo_delegacion='.$delegacion));
  /*$q = "SELECT  count(*) as cantidad  FROM delegaciones_IIBB  WHERE  codigo_delegacion=$delegacion";
  $results = $db->query($q, true);
  $row = $db->fetchByAssoc($results);*/

  if ($row[0]->cantidad>0){
    return true;
  }else{
   return false;
  }
}
/********************FIN IIBB***********************************/

}

?>