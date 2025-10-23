<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
/*************************************************************/
/* Ingreso desde el módulo de trámites del portal			 */
/*************************************************************/
Route::get('/ingresoTramites', function()
{
\Log::info('ingresoTramites');
	try{
		$urlFull=$_SERVER["QUERY_STRING"];
		return Redirect::to('/carga-ingreso/'.$urlFull);
	}catch(\Exception $e){
		$mensaje='Lo sentimos pero no tiene acceso a esta sección.';
		Session::put('mensaje_acceso',$mensaje);
		return Redirect::to('/acceso-no-autorizado');
	}
	
});

/*************************************************************/
/* Ingreso desde la guía de trámites del portal.			 */
/*************************************************************/
Route::get('/ingresoTramitesGuia', function()
{
	try{
		$urlFull=$_SERVER["QUERY_STRING"];
		Session::put('parametros_guia',$urlFull);
		return Redirect::to('/carga-ingreso-guia/'.$urlFull);
	}catch(\Exception $e){
		$mensaje='Lo sentimos pero no tiene acceso a esta sección.';
		Session::put('mensaje_acceso',$mensaje);
		return Redirect::to('/acceso-no-autorizado');
	}
});

Route::get('/consultarTramites', function()
{
	try{
		$urlFull=$_SERVER["QUERY_STRING"];
		
		return Redirect::to('/carga-ingreso-consulta/'.$urlFull);
	}catch(\Exception $e){
		$mensaje='Lo sentimos pero no tiene acceso a esta sección.';
		Session::put('mensaje_acceso',$mensaje);
		return Redirect::to('/acceso-no-autorizado');
	}
});

/************************************************************************/
/* Ingreso desde el portal para dar de acceso al tratamiento de permisos.*/
/************************************************************************/
Route::get('/ingresoTramitesPermisos', function()
{
    try{
		$urlFull=$_SERVER["QUERY_STRING"];
		return Redirect::to('/carga-ingreso-permisos/'.$urlFull);
	}catch(\Exception $e){
		$mensaje='Lo sentimos pero no tiene acceso a esta sección.';
		Session::put('mensaje_acceso',$mensaje);
		return Redirect::to('/acceso-no-autorizado');
	}
});

/** Para controlar la sesión */
Route::get('/control_sesion', array('uses' => 'controllers\\UsuarioController@controlSesion'));
Route::get('/control_sesion_pv',array('uses' =>'controllers\\UsuarioController@controlSesionPrimeraVez'));

/**Para cuando por alguna razón el acceso no es permitido**/
Route::get('/acceso-no-autorizado',function(){
	$mensaje=Session::get('mensaje_acceso');
	Session::forget('mensaje_acceso');
	return View::make('tramites.accesoNegado', array('mensaje'=>$mensaje));
});

/**Para redireccionar al portal**/
Route::get('/portal', function(){
	$letra_portal = Session::get('usuarioLogueado.letra_portal');
	if($letra_portal=="'o'")
		return Redirect::to(Config::get('habilitacion_config/config.urlpac'));
	else if($letra_portal=="'i'")
		return Redirect::to(Config::get('habilitacion_config/config.url_i'));
	else if($letra_portal=="'a'")
		return Redirect::to(Config::get('habilitacion_config/config.url_a'));	
});

Route::get('/carga-ingreso/{datos?}', array('uses' => 'TramitesController@cargaUsuarioEnApp'));
Route::get('/carga-ingreso-guia/{datos?}', array('uses' => 'TramitesController@cargaUsuarioEnApp'));
Route::get('/carga-ingreso-consulta/{datos?}', array('uses' => 'TramitesController@cargaUsuarioEnApp'));
Route::get('/carga-ingreso-permisos/{datos?}', array('uses' => 'TramitesController@cargaUsuarioEnAppSeccionPermisos'));
Route::get('/carga-tramites', array('before'=>'existe_sesion','uses' => 'TramitesController@redireccion'));

/**redirección a la vista incial según usuario cdo presiona en la casita**/
Route::get('/carga-tramites-inicio', array('before'=>'existe_sesion','uses' => 'TramitesController@redireccion_inicio'));
Route::get('/administracion-permisos-inicio', array('before'=>'existe_sesion','uses' => 'TramitesController@redireccion_inicio_admin_permisos'));

/*redirección a la guia desde el inicio cdo es agente*/
Route::get('/guia', array('before'=>'existe_sesion',function(){
	$url=Config::get('habilitacion_config/config.urlpac');
	$url.=Config::get('habilitacion_config/config.url_guia');

	return Redirect::to($url);
}));

Route::get('/tablero-tramites', array('uses' => 'TramitesController@tableroTramites'));

// Recive el correo electronico del permiso, para validar
Route::post('/ingreso2', array('uses' => 'TramitesController@cargaPermiso_add'));

// Envía una ID y despliega la pagina que pide confirmación de correo
Route::get('/ingreso3', array('uses' => 'TramitesController@validadCorreo'));
// Confirma el ID, y si todo OK, guarda el correo en la DB
Route::post('/ingreso3', array('uses' => 'TramitesController@validadCorreo_add'));

// Solicito el tipo de tramite a iniciar (es get o post?)
Route::get('/tramites', array('before'=>'existe_sesion','uses' => 'TramitesController@listarTramites'));
Route::post('/tramites', array('before'=>'existe_sesion','uses' => 'TramitesController@listarTramites'));
Route::post('/tramites2', array('before'=>'existe_sesion','uses' => 'TramitesController@irTramite'));

Route::get('/cambio-domicilio', array('before'=>'existe_sesion','uses' => 'TramitesController@cambioDomicilio'));
Route::post('/cambio-domicilio2', array('before'=>'existe_sesion','uses' => 'TramitesController@cambioDomicilio_add'));
Route::get('/plan-estrategico', array('before'=>'existe_sesion','uses' => 'TramitesController@planEstrategico'));
Route::post('/plan-estrategico', array('before'=>'existe_sesion','uses' => 'TramitesController@planEstrategico_add'));
Route::post('/plan-domicilio', array('uses'=>'TramitesController@llamaPlanEstrategico'));
Route::get('/plan', array('uses'=>'TramitesController@llamaPlanEstrategico'));

Route::post('/plan', array('uses'=>'TramitesController@planEstrategicoModificado_add'));
Route::post('/plan-pdf',array('uses'=>'TramitesController@plan_pdf'));
Route::get('/plan-pdf',array('uses'=>'TramitesController@show_plan_pdf'));
Route::post('/foto-frente', array('before'=>'existe_sesion','uses' => 'TramitesController@cargaFotoFrente'));
Route::post('/foto-interior', array('before'=>'existe_sesion','uses' => 'TramitesController@cargaFotoInterior'));
/*Modificación del domicilio*/
Route::post('/actualizar-domicilio', array('uses'=>'TramitesController@actualizarDomicilio'));

// Solitito datos para nuevo titular
Route::get('/cambio-titular', array('before'=>'existe_sesion','uses' => 'TramitesController@cambioTitular'));
// Proceso datos del nuevo titular
Route::post('/cambio-titular2', array('before'=>'existe_sesion','uses' => 'TramitesController@cambioTitular_add'));

Route::get('/fallecimiento', array('before'=>'existe_sesion','uses' => 'TramitesController@fallecimientoTitular'));
Route::post('/fallecimiento2', array('before'=>'existe_sesion','uses' => 'TramitesController@fallecimientoTitular_add'));

Route::post('/buscar-persona', array('before'=>'existe_sesion', 'uses'=>'TramitesController@buscarPersona'));
Route::post('/validarIngresos', array('before'=>'existe_sesion', 'uses'=>'TramitesController@validarIngresos'));
Route::post('/validarCuitPermiso', array('before'=>'existe_sesion', 'uses'=>'TramitesController@validarCuitPermiso'));

Route::get('/solicitud-permiso', array('before'=>'existe_sesion','uses' => 'TramitesController@solicitudPermiso'));
Route::post('/solicitud-permiso2', array('before'=>'existe_sesion','uses' => 'TramitesController@solicitudPermiso_add'));

Route::get('/cambio-dependencia', array('before'=>'existe_sesion','uses' => 'TramitesController@cambioDependencia'));
Route::post('/cambio-dependencia2', array('before'=>'existe_sesion','uses' => 'TramitesController@cambioDependencia_add'));

Route::get('/cambio-categoria', array('before'=>'existe_sesion','uses' => 'TramitesController@cambioCategoria'));
Route::post('/cambio-categoria2', array('before'=>'existe_sesion','uses' => 'TramitesController@cambioCategoria_add'));
Route::post('/nueva_red_propuesta', array('before'=>'existe_sesion','uses'=>'TramitesController@nuevaRedPropuesta'));
Route::post('/ok-aprobar', array('before'=>'existe_sesion','uses'=>'TramitesController@okAprobar'));

Route::get('/consulta-links', array(function(){
	Session::forget('ddv_nro');
	//de dónde viene
	$ddv_nro=array('ddv_nro'=>Input::get('ddv_nro'));//viene del botón de consulta-tramite
	Session::put('ddv_nro', $ddv_nro);

	return Redirect::to('/consulta-tramite');
}));

	/*********************************/
	/* Consultar Tramite Pendiente	*/
	/*	JG-18/11/2020				*/
	/********************************/


Route::get('/consulta-nrotramite-pendientes', array('uses' => 'TramitesController@consultarPorNroTramitePendientes'));

/************************************FIN Tramites Pendientes************************************/

Route::get('/consulta-tramite', array('uses' => 'TramitesController@consultarTramite'));
Route::post('/consulta-tramite2', array('uses' => 'TramitesController@consultarTramite_add'));
Route::post('/consulta-tramiteP', array('uses' => 'TramitesController@consultarTramite_add')); //Consulta tabla tramites pendientes
/** Consulta por nº de seguimiento**/
Route::get('/consulta-nrotramite', array('before'=>'existe_sesion','uses' => 'TramitesController@consultarPorNroTramite'));
Route::post('/consulta-nrotramite2', array('before'=>'existe_sesion','uses' => 'TramitesController@consultarPorNroTramite_add'));
Route::post('/consulta-nrotramiteP', array('before'=>'existe_sesion','uses' => 'TramitesController@consultarPorNroTramite_add'));//Tramites pendientes JG
Route::get('/consulta-nrotramiteid/{id}', array('before'=>'existe_sesion','uses' => 'TramitesController@consultarPorNroTramite_addP'));//Tramites pendientesJG
Route::get('/consulta-nrotramite3/{datos}', array('before'=>'existe_sesion','uses' => 'TramitesController@consultarPorNroTramite_show'));

/****/
Route::get('/exito-tramite/', array('before'=>'existe_sesion','uses' => 'TramitesController@exitoTramite'));

/**Cancelar desde el menú**/
Route::get('/cancelar', array('before'=>'existe_sesion','uses' => 'TramitesController@cancelarTramite'));
Route::post('/cancelar2', array('before'=>'existe_sesion','uses' => 'TramitesController@cancelarTramite_add'));
Route::get('/cancelar-tramite', array('before'=>'existe_sesion','uses' => 'TramitesController@show_cancelarTramite'));

/**Rutas desde la página de consulta**/
Route::post('/cancelar', array('before'=>'existe_sesion','uses' => 'TramitesController@cancelarTramiteConsulta'));
Route::get('/historial-estados-tramite/{nroSeguimiento}', array('uses' => 'TramitesController@historialTramiteConsulta'));

Route::post('/informe-tramites', array('uses'=>'TramitesController@informeTramite'));
Route::get('/informe-tramite-pdf', array('uses'=>'TramitesController@show_informeTramite'));
Route::post('/caratula-tramites', array('uses'=>'TramitesController@caratulaTramite'));
Route::get('/caratula-tramite-pdf', array('uses'=>'TramitesController@show_caratulaTramite'));
Route::post('/nota-solicitud-tramite', array('uses'=>'TramitesController@notaSolicitudTramite'));
Route::get('/nota-solicitud-tramite-pdf', array('uses'=>'TramitesController@show_notaSolicitudTramite'));
Route::post('/caratula-nota', array('uses'=>'TramitesController@caratula_nota'));
Route::get('/csv', array('uses'=>'TramitesController@csv'));

/*Detalle trámite*/
//Route::get('/detalle-tramite', array('uses' => 'TramitesController@detalleTramite'));
Route::post('/detalle-tramite-nro-seguimiento', array('before'=>'existe_sesion','uses' => 'TramitesController@detallePorNroSeguimiento'));
Route::get('/detalle-tramite-nro-seguimiento', array('before'=>'existe_sesion','uses' => 'TramitesController@detallePorNroSeguimiento'));
Route::get('/detalle-tramite-nro-seguimientoP/{id}', array('before'=>'existe_sesion','uses' => 'TramitesController@detallePorNroSeguimientoP'));//Tramites pendientes JG
Route::get('/detalle-tramite-modal/{datos}', array('before'=>'existe_sesion','uses' => 'TramitesController@detalleModal'));

/**Rutas desde el tablero**/
Route::get('/reportes/{id}',array('before'=>'existe_sesion','uses'=>'TramitesController@reportes_consulta'));

/**Rutas desde el modal**/
Route::post('/actualizar-estado', array('before'=>'existe_sesion','uses' => 'TramitesController@actualizarEstadoTramite'));
Route::post('/historial-estado', array('before'=>'existe_sesion','uses' => 'TramitesController@historialEstadoTramite'));
Route::get('/historial-estado/{id}', array('before'=>'existe_sesion','uses' => 'TramitesController@historialEstadoTramite_get'));
Route::post('/nueva_observacion_estado', array('before'=>'existe_sesion','uses' => 'TramitesController@nueva_observacion_estado'));

/**Para nuevo trámite cdo es CAS y quiere ingresar un permiso**/
Route::post('/consulta-permiso', array('before'=>'existe_sesion','uses'=>'TramitesController@consultaPermiso'));
Route::post('/consulta-permiso_add', array('before'=>'existe_sesion','uses'=>'TramitesController@consultaPermiso'));

/**Localidad**/
Route::post('/localidad', array('before'=>'existe_sesion','uses' => 'controllers\LocalidadController@buscarSimilaresPorNombre'));
Route::get('/localidad', array('before'=>'existe_sesion','uses' => 'controllers\LocalidadController@buscarSimilaresPorNombre'));

/**Localidad departamento**/
Route::post('/localidad_departamento', array('before'=>'existe_sesion','uses' => 'controllers\LocalidadController@buscarSimilaresPorNombre_stafe_dpto'));
Route::get('/localidad_departamento', array('before'=>'existe_sesion','uses' => 'controllers\LocalidadController@buscarSimilaresPorNombre_stafe_dpto'));

/**Localidad por id**/
Route::post('/localidad_id', array('before'=>'existe_sesion','uses' => 'controllers\LocalidadController@buscarLocalidad'));
Route::get('/localidad_id', array('before'=>'existe_sesion','uses' => 'controllers\LocalidadController@buscarLocalidad'));

/** cambio dependencia - nueva red**/
Route::post('/control_red', array('before'=>'existe_sesion','uses'=>'TramitesController@control_red'));

/** antes de inciar un trámite **/
Route::post('/verificar_existencia_tramite', array('before'=>'existe_sesion','uses'=>'TramitesController@verificar_existencia_tramite'));

/** envios de correos **/
Route::get('/envio_mail_inicio_exitoso',array('before'=>'existe_sesion','uses'=>'TramitesController@enviar_email_gral'));
Route::get('/envio_mail_cambio_estado', array('before'=>'existe_sesion','uses'=>'TramitesController@enviar_email_modificacion_estado'));
//para pruebas de envío de correo
Route::get('/envio_mail_pruebas', array('uses'=>'TramitesController@enviar_email_prueba'));

/***** historial en pdf ******/
Route::post('/historial-pdf',array('uses'=>'TramitesController@historial_pdf'));
Route::get('/historial-pdf',array('uses'=>'TramitesController@show_historial_pdf'));

// Resumen Tramites
Route::get('/resumen-tramites', array('before'=>'existe_sesion','uses' => 'TramitesController@resumenTramites'));

// Imprimir Resolucion
Route::post('/resolucion-tramites', array('before'=>'existe_sesion','uses'=>'TramitesController@resolucionTramite'));
Route::get('/resolucion-tramite-pdf', array('before'=>'existe_sesion','uses'=>'TramitesController@show_resolucionTramite'));

/**Adjuntar archivos**/
Route::post('/adjuntar', array('before'=>'existe_sesion','uses'=>'TramitesController@adjuntarArchivos'));
Route::post('/descargar_adjunto',array('uses'=>'TramitesController@descargar_adjunto'));
Route::post('/eliminar_adjunto',array('uses'=>'TramitesController@eliminarAdjunto'));
Route::post('/adjuntos_actuales',array('uses'=>'TramitesController@adjuntosActuales'));
Route::post('/borrar_carpeta_adjuntos',array('uses'=>'TramitesController@borrar_carpeta_adjuntos'));
Route::get('/descarga',array('uses'=>'TramitesController@descarga'));

/**Administración máquinas**/
Route::get('/administracion_maquinas',array('uses'=>'TramitesController@adminMaquinas'));
Route::post('/buscar_tipos_terminal',array('uses'=>'TramitesController@buscarTiposTerminal'));
Route::post('/buscar_terminal',array('uses'=>'TramitesController@buscarTerminal'));
Route::post('/incorporar-maquina',array('uses'=>'TramitesController@incorporarMaquina_add'));
Route::post('/retirar-maquina',array('uses'=>'TramitesController@retirarMaquina_add'));

/**Administración de permisos**/
Route::get('/administracion-permisos', array('uses' => 'TramitesController@consultaAdministracionPermisos'));
Route::post('/datos-base-alta-permiso', array('uses'=>'TramitesController@datosBaseAltaPermiso'));
Route::post('/alta-permisos',array('uses'=>'TramitesController@altaPermisos_add'));
Route::post('/baja-permisos-ok',array('uses'=>'TramitesController@bajaPermisosOk'));
Route::post('/control-baja-permiso',array('uses'=>'TramitesController@controlBajaPermiso'));
Route::post('/control-renuncia-permiso',array('uses'=>'TramitesController@controlRenunciaPermiso'));
Route::post('/baja-permiso',array('uses'=>'TramitesController@bajaPermiso'));
Route::post('/completar-tramite',array('uses'=>'TramitesController@completarTramite'));
Route::post('/grilla-permisos',array('uses'=>'TramitesController@grillaPermisosSinAdjudicar'));
Route::get('/grilla-permisos-csv',array('uses'=>'TramitesController@permisosSinAdjudicarCSV'));
Route::post('/adjudicar-permiso',array('uses'=>'TramitesController@adjudicarPermiso'));
Route::post('/completar_tramite',array('uses'=>'TramitesController@adjudicarPermiso'));
Route::post('/informe-permisos-sin-adjudicar',array('uses'=>'TramitesController@informePermisosSinAdjudicar'));
Route::get('/informe-permisos-sin-adjudicar-pdf',array('uses'=>'TramitesController@informePermisosSinAdjudicarPDF'));
/**/
//Route::post('/renuncia',array('uses'=>'TramitesController@renunciaPermiso'));
//Route::get('/renuncia', array('uses'=>'TramitesController@vistaIngresoTramitesInternosRenuncia'));
Route::post('/renuncia',array('uses'=>'TramitesController@renunciaPermiso'));
/*Redirecciona a la vista de ingreso de trámites*/
Route::get('/ingreso', array('uses'=>'TramitesController@vistaIngresoTramitesInternos'));


/* Verificación de correo electrónico */
Route::post('/correo_valido', array('uses'=>'TramitesController@verificarCorreo'));

/* Incorporación de cotitulares de un permiso */
Route::get('/incorporar-cotitular', array('before'=>'existe_sesion','uses' => 'TramitesController@incorporarCoTitular'));
Route::post('/incorporar-cotitular2', array('before'=>'existe_sesion','uses' => 'TramitesController@incorporarCoTitular_add'));

/* Baja cotitular */
Route::get('/baja-cotitular', array('before'=>'existe_sesion','uses' => 'TramitesController@bajaCotitular'));
Route::post('/baja-cotitular', array('before'=>'existe_sesion','uses' => 'TramitesController@bajaCotitular_add'));

/* Modificar trámites */
Route::post('/modificar-tramite', array('before'=>'existe_sesion','uses' => 'TramitesController@modificarTramite'));
Route::post('/modificar-tramite2', array('before'=>'existe_sesion','uses' => 'TramitesController@modificarTramite_add'));

/* Habilitación de un permiso */
Route::post('/control-habilitar-permiso',array('before'=>'existe_sesion','uses'=>'TramitesController@controlHabilitarPermiso'));
Route::post('/habilitar-permiso', array('before'=>'existe_sesion','uses' => 'TramitesController@habilitarPermiso'));

/* Suspensión de un permiso */
Route::post('/control-suspender-permiso',array('before'=>'existe_sesion','uses'=>'TramitesController@controlSuspenderPermiso'));
Route::post('/suspender-permiso', array('before'=>'existe_sesion','uses' => 'TramitesController@suspenderPermiso'));

/** control nº de subagente red**/
Route::post('/control_subagente', array('before'=>'existe_sesion','uses'=>'TramitesController@control_subagente'));
/** control nueva red **/
Route::post('/numeroNuevaRed', array('before'=>'existe_sesion','uses'=>'TramitesController@numeroNuevaRed'));

/** Evaluación Domicilio **/
Route::get('/evaluacion-domicilio', array('uses'=>'TramitesController@cargarEvaluacionDomicilio'));//para que pueda accederla sin datos
Route::post('/evaluacion-domicilio', array('before'=>'existe_sesion','uses'=>'TramitesController@cargarEvaluacionDomicilio'));
Route::post('/evaluacion-domicilio-modificar', array('before'=>'existe_sesion','uses'=>'TramitesController@evaluacionDomicilio'));
Route::post('/plano', array('before'=>'existe_sesion','uses' => 'TramitesController@cargaPlanoEvaluacion'));
Route::post('/evaluacion-pdf',array('uses'=>'TramitesController@evaluacion_pdf'));
Route::get('/evaluacion-pdf',array('uses'=>'TramitesController@show_evaluacion_pdf'));
