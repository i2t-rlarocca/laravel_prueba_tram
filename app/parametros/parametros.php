<?php
//die(basename($_SERVER['REQUEST_URI']));//REQUEST_URI: ésta me devuelve tableroTramites por ej.
//die($_SERVER["SCRIPT_NAME"]);
//strstr: encuentra la primera aparición de un string
//$urlActual=substr(basename($_SERVER['PHP_SELF']), 0,-4); //para sacarle el .php
$urlActual=basename($_SERVER['REQUEST_URI']);
$url_repositorio = 'http://192.168.127.70/cas/jasperserver-SOAP/Ejecutar_Reportes2.php?';
//Parametros de reporte tablero_control

\Log::error('parametros.php - url', array($urlActual));


switch ($urlActual) {
    case "tablero-tramites"://llama al tablero de control
    	$formato = 'HTML';
        $ruta_reporte='/reports/PAC/Tramites/nuevo_tablero';
        $url_repositorio .='ruta_reporte='.$ruta_reporte.'&formato='.strtoupper($formato);
        break;
    case "reporte-tramite":
    	$formato = 'PDF';
        $ruta_reporte='/reports/PAC/Tramites/nuevo_tablero';
        $url_repositorio .='ruta_reporte='.$ruta_reporte.'&formato='.strtoupper($formato);
        break;
    case "informe-licencia":
    	$formato = 'PDF';
        $ruta_reporte='/reports/PAC/reporte_consulta_licencias';
        //{{--La cantidad debe coicidir con los parametros definidos--}}
		$cantidad_parametros='4';
		$parametros = [
		'1' => [
			'1' => 'Fecha Desde:',
			'2' => 'param_Fecha_desde',
			'3' => 'datetime'
			],
		'2' => [
			'1' => 'Fecha Hasta:',
			'2' => 'param_Fecha_hasta',
			'3' => 'datetime'
			],
		'3' => [
			'1' => 'Motivo',
			'2' => 'param_Tipo_licencia',
			'3' => 'select'
			],
		'4' => [
			'1' => 'Nro Tramite',
			'2' => 'param_Nro_tramite',
			'3' => 'number'
			]
		];
        
        $url_repositorio .="ruta_reporte=".$ruta_reporte."&formato=".strtoupper($formato);
        break;
    
     }

$width = '900 px';
$height = '842 px';





//Parametros de reporte consulta_tramites



//$parametros='&param_idcuenta=d29e1ec4-f0be-830f-eacd-4d358e5b4944';

//$url_repositorio .="ruta_reporte=".$ruta_reporte."&formato=".strtoupper($formato).$parametros;

/*
case 50:// reporte consulta tramite			
$currentUri="/reports/PAC/Reporte_consulta_tramites";
			break;						

case 51:// reporte consulta licencia			
$currentUri="/reports/PAC/reporte_consulta_licencias";			
break;						
			
//*** fin reporte ejecutivo	
case 53:// reporte comprobante tramite			
$currentUri="/reports/PAC/comprobante_tramite";			
break;		
*/





?>