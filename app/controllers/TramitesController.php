<?php

	use controllers\UsuarioController;
	use controllers\ProvinciaController;
	use controllers\LocalidadController;
	use Presentacion\Premios\Formatos;
	use controllers\AgenciaController;
/**********************************************************/
	use \Dominio\Servicios\Habilitacion\Usuarios;
	use \Dominio\Servicios\Habilitacion\Tramites;
	use \Dominio\Servicios\Habilitacion\HistoricoTramitesEstados;
	use \Dominio\Servicios\Habilitacion\TipoTramites;
	use \Dominio\Servicios\Habilitacion\TipoDocumento;
/*************************************************************/
	class TramitesController extends BaseController{

		function __construct() {
			$this->controladorUsuario = new UsuarioController();
			$this->formatos=new Formatos();
			$this->servicioTramites = new Tramites();
			$this->servicioHistorico = new HistoricoTramitesEstados();
			$this->servicioTipoTramites =  new TipoTramites();
			$this->controladorLocalidad = new LocalidadController();
			$this->controladorAgencias = new AgenciaController();
			$this->servicioTipoDocumento = new TipoDocumento();
		}


		/************************************************/
		/*	Guarda el usuario logueado en el sistema    */
		/*	o lo redirecciona a la página de la loteria */
		/*  si no es un usuario válido.					*/
		/************************************************/
		public function cargaUsuarioEnApp($datos=null){

			if(!is_null($datos)){
				$datos=urldecode(base64_decode($datos));
				$parametros = (explode("&",$datos));

				$nomUsuario = explode("usuario=",$parametros[0])[1];
				if($parametros[1])
				$id_portal="'".explode("id_portal=",$parametros[1])[1]."'";
				$letra_portal=explode("letra_portal=",$parametros[2])[1];
				if(count($parametros)==4){
					$accion=explode("tramite=",$parametros[3]);

					if(count($accion)>1){
						$tramite = $accion[0];
						$funcion= array(0);
						$tramite=$this->servicioTipoTramites->buscarSimilaresPorNombre($tramite, $funcion);
						//borra todos los datos anteriores
						Session::flush();
						Session::put('tramiteGuia', $tramite);
					}
				}
				$nomUsuario=$this->formatos->decrypt($nomUsuario,"Pir@mide01");
				$usuario=$this->controladorUsuario->buscar($nomUsuario);

				if (is_null($usuario)|| ($usuario['nombreTipoUsuario']!="Agente" && $usuario['nombreTipoUsuario']!="SubAgente"
					&& $usuario['nombreTipoUsuario']!="CAS" && $usuario['nombreTipoUsuario']!="CAS_ROS")){
					$mensaje='Su tipo de usuario no posee acceso.';
					if(is_null($usuario)){
						\Log::info('usuario nulo 2 - Tramites Controller - Carga usuario en app');
						$mensaje='Su usuario no tiene asignada la provincia.';
					}
					Session::put('mensaje_acceso',$mensaje);
					return Redirect::to('/acceso-no-autorizado');
				}

				$usuario['id_portal']=$id_portal;
				$usuario['letra_portal']=$letra_portal;
				Session::put('usuarioLogueado',$usuario);

				//si viene del link de consultar trámite
				if(strpos(URL::current(),'carga-ingreso-consulta')!==false){
					return Redirect::to('/consulta-nrotramite');
				}else if(strpos(URL::current(),'carga-ingreso')!==false){
					$accion = explode("accion=",$parametros[3]);
					if(count($accion)==2){
						if(strcasecmp($accion[1],"tablero")==0){
							return Redirect::to('/carga-tramites');
						}else if(strcasecmp($accion[1],"consulta")==0){
							return Redirect::to('/tramites');
						}
					}
				}

			}else{
				if(Session::has('usuarioLogueado'))
					$usuario=Session::get('usuarioLogueado');
				else
					return Redirect::to('/portal');
			}

			return Redirect::to('/carga-tramites');

		}

		/*****************************************************
		* Ingreso a la sección de administración de permisos *
		******************************************************/
		public function cargaUsuarioEnAppSeccionPermisos($datos){
			$datos=urldecode(base64_decode($datos));

			$parametros = (explode("&",$datos));
			$nomUsuario = explode("usuario=",$parametros[0])[1];
			$id_portal="'".explode("id_portal=",$parametros[1])[1]."'";
			$letra_portal=explode("letra_portal=",$parametros[2])[1];
			$nomUsuario=$this->formatos->decrypt($nomUsuario,"Pir@mide01");

			$usuario=$this->controladorUsuario->buscar($nomUsuario);

			if (is_null($usuario)|| ($usuario['nombreTipoUsuario']!="CAS" && $usuario['nombreTipoUsuario']!="CAS_ROS") || count($usuario['listaFunciones'])==0){
				$mensaje='Su tipo de usuario no posee acceso.';
				if(is_null($usuario)){
					\Log::info('usuario nulo');
					$mensaje='Su usuario no tiene asignada la provincia.';
				}
				Session::put('mensaje_acceso',$mensaje);
		       	return Redirect::to('/acceso-no-autorizado');
    		}else if(in_array('Consulta_Permisos',$usuario['listaFunciones'])){
				$usuario['id_portal']=$id_portal;
				$usuario['letra_portal']=$letra_portal;
    			Session::put('usuarioLogueado',$usuario);
    			//Session::put('seccion-permisos',$tramite);
    			return Redirect::to('/administracion-permisos-inicio');
    		}else{

    			$mensaje='Su tipo de usuario no posee acceso.';
    			Session::put('mensaje_acceso',$mensaje);
		       	return Redirect::to('/acceso-no-autorizado');
    		}
		}

		/**********************************************/
		/* Funcion que redirecciona a la vista 		  */
		/* correspondiente al ingresar en función del */
		/* usuario que se encuentra en el sistema.    */
		/**********************************************/
		public function redireccion(){

			if(Session::has('usuarioLogueado')){
				$usuario=Session::get('usuarioLogueado');
				}else{
				return Redirect::to('/portal');
				}

			if($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS'){
				if(Session::has('seccion-permisos')){
					if(in_array("Consulta_permisos", $usuario['listaFunciones'])){
						return Redirect::to('/administracion-permisos');
					}else{
						$mensaje='Su tipo de usuario no posee acceso.';
						Session::put('mensaje_acceso',$mensaje);
				       	return Redirect::to('/acceso-no-autorizado');
					}
				}else{

					if(in_array("Tablero_Control", $usuario['listaFunciones'])){
						return Redirect::to('/tablero-tramites');
					}else{
						$mensaje='Su tipo de usuario no posee acceso.';
						Session::put('mensaje_acceso',$mensaje);
				       	return Redirect::to('/acceso-no-autorizado');
					}
				}
			}else{
					return View::make('tramites.ingreso');
			}
		}

		/***
			Redirecciona desde el botón de la casita al inicio
			correspondiente a cada usuario (tablero para cas - consulta para usuario)
		***/
		public function redireccion_inicio(){
			if(Session::has('usuarioLogueado')){
				$usuario=Session::get('usuarioLogueado');
			}else{
				return Redirect::to('/portal');
			}

			if($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS'){
				return Redirect::to('/tablero-tramites');
			}else{
				return Redirect::to('/guia');
			}
		}

		/**
		* Función para redireccionar desde la casita cdo está administrando permisos
		**/
		public function redireccion_inicio_admin_permisos(){
			if(Session::has('usuarioLogueado')){
				$usuario=Session::get('usuarioLogueado');
				}else{
				return Redirect::to('/portal');
				}

			if($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS'){
				return Redirect::to('/administracion-permisos');
			}
		}

		/**
		* Función para redireccionar a la consulta de administración de permisos
		**/
		public function consultaAdministracionPermisos(){
			return View::make('tramites.consultaAdministracionPermisos');
		}

		/**
		* Función que crea la vista para los usuarios CAS que
		* quieren ver los trámites de agentes/subagentes (trámites externos)
		**/
		public function tableroTramites (){

// 			Buscar el detalle para c/u de los recuadros del tablero
			$recepcionRos = $this->servicioTramites->buscarDetalleRecepRosario();
			$recepcionStaFe = $this->servicioTramites->buscarDetalleRecepStaFe();
			$firmaVice = $this->servicioTramites->buscarDetalleFirmaVice();
			$hab30 = $this->servicioTramites->buscarDetalleHab30();
			$hab3060 = $this->servicioTramites->buscarDetalleHab3060();
			$hab60 = $this->servicioTramites->buscarDetalleHab60();
			$iniPer = $this->servicioTramites->buscarDetalleIniPer();
			$devueltosDoc = $this->servicioTramites->buscarDetalleDevueltosDoc();
			$aprobPend = $this->servicioTramites->buscarDetalleAprobPendientes();

			$cantIniPer = 0;
			$cantDevDoc = 0;
			$cantRos = 0;
			$cantStaFe = 0;
			$cant30 = 0;
			$cant3060 = 0;
			$cant60 = 0;
			$cantFirma = 0;
			$cantAprobPend = 0;

			$i = 1;
			foreach ($iniPer as $iniP){
					$cantIniPer = $iniP->cantidad + $cantIniPer;
			}
			foreach ($devueltosDoc as $devueltosD){
					$cantDevDoc = $devueltosD->cantidad + $cantDevDoc;
			}
			foreach ($recepcionRos as $recepcionR){
					$cantRos = $recepcionR->cantidad + $cantRos;
			}
			foreach ($recepcionStaFe as $recepcionS){
					$cantStaFe = $recepcionS->cantidad + $cantStaFe;
			}
			foreach ($hab30 as $h30){
					$cant30 = $h30->cantidad + $cant30;
			}
			foreach ($hab3060 as $h3060){
					$cant3060 = $h3060->cantidad + $cant3060;
			}
			foreach ($hab60 as $h60){
					$cant60 = $h60->cantidad + $cant60;
			}
			foreach ($firmaVice as $firmaV){
					$cantFirma = $firmaV->cantidad + $cantFirma;
			}

			foreach ($aprobPend as $aP){
					$cantAprobPend = $aP->cantidad + $cantAprobPend;
			}

			$cantTramites = array(
				"iniPer" => $cantIniPer,
				"devueltosDoc" => $cantDevDoc,
				"recepcionRos" => $cantRos,
				"recepcionStaFe" => $cantStaFe,
				"hab30" => $cant30,
				"hab3060" => $cant3060,
				"hab60" => $cant60,
				"firmaVice" => $cantFirma,
				"aprobPend"=>$cantAprobPend
			);

			return View::make('tramites.tableroTramites', array('recepcionRos'=>$recepcionRos,
																'recepcionStaFe'=>$recepcionStaFe,
																'firmaVice'=>$firmaVice,
																'hab30'=>$hab30,
																'hab3060'=>$hab3060,
																'hab60'=>$hab60,
																'iniPer'=>$iniPer,
																'devueltosDoc'=>$devueltosDoc,
																'aprobPend'=>$aprobPend,
																'cantTramites'=>$cantTramites,
																));
		}

		//Usuario ingreso permiso

		/**Función similar a consulta permiso, solo que debe controlar
		si existe el permiso y si es igual al del usuario logueado, para
		luego redirigirlo a la página de consulta.**/
		public function cargaPermiso_add(){
			Session::forget('usuarioTramite');

// cambiado por ADEN - 2024-04-08
			$usr = Session::get('usuarioLogueado');
//			$permiso=Input::get('permiso');
			$permiso=$usr['permiso'];

			$email=Input::get('email');
			// Ya no tengo mas el permiso ingresado, tomo el de la session
			$usuario=$this->controladorUsuario->buscarPorPermiso($usr['permiso']);

			\Log::info("cargaPermiso_add ->usr: ", array($usr));
			\Log::info("cargaPermiso_add ->permiso: ", array($usr['permiso']));
			// --> $usr['email'] tiene el correo actual!
			\Log::info("cargaPermiso_add ->mail: ", array($email));
			\Log::info("cargaPermiso_add ->usuario: ", array($usuario));

			// verifico email valido
			$datos = Input::all();
			$reglas=array(
				'email'=>'required|email'
			);
			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"email"=>"Ingrese una cuenta de correo válida"
			);
			// validacion
			$validar=Validator::make($datos,$reglas,$mensaje);
			// si falló, devuelvo un mensaje
			if($validar->fails()){
				return Response::json(array('mensaje'=>"Ingrese una cuenta de correo válida"));
			}
			//	fin de verificación del e-mail
\Log::info("cargaPermiso_add -> 1-email: ", array($email));
\Log::info("cargaPermiso_add -> 2-usr'email': ", array($usr['email']));

			if($email==$usr['email']){//el mail del permiso logueado es igual al ingresado???
				if(Session::has('tramiteGuia')){
					$tramite=Session::get('tramiteGuia');
				}else{
					$tramite=null;
				}

				$respuesta = Response::json(array('usuario'=>$usuario,'tramite'=>$tramite));
				Session::put('usuarioTramite',$usuario);
				return $respuesta;
			}else{

// MIENTRAS DESARROLLO, Si es mi correo hago la rutina del envio del codigo y la validacion

// MERY

				// if ($email === "adrian.enrico@i2t.com.ar") {

					Session::forget('nuevoCorreo');
					Session::forget('codigo_validacion');
					// mando correo con ID -----------------------------------------------------------------
					// genero un id
					$codigo_validacion = rand(111111, 999999);
					\Log::info("cargaPermiso_add -> codigo_validacion: ", array($codigo_validacion));
					
					$datos_email=Session::get('datos_email');
					\Log::info("cargaPermiso_add -> datos_email: ", array($datos_email));
	
					// Guardo el nuevo correo en session
					Session::put('nuevoCorreo',$email);
					// Guardo el codigo de validacon en session
					Session::put('codigo_validacion', $codigo_validacion);
					// recupero datos del usuarioLogueado

					$usuarioLogueado = Session::get('usuarioLogueado');
					\Log::info("cargaPermiso_add -> usuarioLogueado: ", array($usuarioLogueado));
					
					// mando correo con ID -----------------------------------------------------------------
					if(Session::has('cancelado')){
						$titulo= Session::get('cancelado');
						Session::forget('cancelado');
					}else{
						Session::put('email_validacion_correo', $datos_email);
						$this->enviar_email_validacion_correo($email, $codigo_validacion);
					}

					// muestro la vista para ingreso de ID
					// este es el ejemplo -> tramites.cambioDomicilio
					\Log::info("cargaPermiso_add -> muestro la vista: ", array($email, $codigo_validacion));

					// return Response::json(array('mensaje'=>"Hay que pintar la vista que pide el codigo!"));

					return View::make('tramites.ingreso_codigo_validacion');
					// return View::make('tramites.prueba');
					
				//} else {
				//	return Response::json(array('mensaje'=>"El correo no coincide. Reintente!"));
				//}
			}
			
		}

		/****************************************************/
		/*	MANDO ID x correo y lo solicito       			*/
		/*	AE-12/04/2024				*/
		/****************************************************/
		// MERY
		public function validarCorreo(){
			$todos = Input::all();
			\Log::info("AE - validarCorreo : Codigo todos:", array($todos));
			// recupero el codigo ingresado
			$codigoIngresado=Input::get('codigo');
			// recupero datos del codigo generado
			$codigo = Session::get('codigo_validacion');
			\Log::info("AE - validarCorreo : Codigo Generado:" . $codigo . " Codigo Ingresado:" . $codigoIngresado);
			/*
			if ($codigo === $codigoIngresado) {
				return Response::json(array('tokenOK'=>"OK"));
			} else {
				return Response::json(array('mensaje'=>"Pasó por validar codigo"));
			}
			*/
			return View::make('tramites.ingreso_codigo_validacion'); // ADEN
		}
		
		
		/****************************************************/
		/*	Recepcion y validación de codigo       			*/
		/*	MM-03/05/2024									*/
		/****************************************************/	

		public function validarCorreo_add(){
			// Session::forget('usuarioTramite');
			\Log::info("AE - validarCorreo_add : ");

			// cambiado por MMERY - 2024-05-07
			$usr = Session::get('usuarioLogueado');
			$permiso=$usr['permiso'];

			$cod=Input::get('codigo');
			$codVal = Session::get('codigo_validacion');
			$nuevoCorreo = Session::get('nuevoCorreo');
			// Ya no tengo mas el permiso ingresado, tomo el de la session
			$usuario=$this->controladorUsuario->buscarPorPermiso($usr['permiso']);

			\Log::info("validarCorreo_add ->usr: ", array($usr));
			\Log::info("validarCorreo_add ->permiso: ", array($usr['permiso']));
			// --> $usr['email'] tiene el correo actual!
			\Log::info("validarCorreo_add ->cod: ", array($cod));
			\Log::info("validarCorreo_add ->codVal: ", array($codVal));
			\Log::info("validarCorreo_add ->usuario: ", array($usuario));

			// verifico email valido
			$datos = Input::all();
			$reglas=array(
				'codigo'=>'required|integer'
			);
			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"number"=>"Ingrese codigo valido"
			);
			// validacion
			$validar=Validator::make($datos,$reglas,$mensaje);
			// si falló, devuelvo un mensaje
			if($validar->fails()){
				return Response::json(array('mensaje'=>"Ingrese código válido"));
			}
			//	fin de verificación del e-mail

			if($cod===$codVal){//el mail del permiso logueado es igual al ingresado???
				$mensaje=$this->servicioTramites->actualizaCorreo($nuevoCorreo, $permiso,1);

				if(Session::has('tramiteGuia')){
					$tramite=Session::get('tramiteGuia');
				}else{
					$tramite=null;
				}

				$respuesta = Response::json(array('usuario'=>$usuario,'tramite'=>$tramite));
				Session::put('usuarioTramite',$usuario);
				return $respuesta;
			}else{
				$datos_email=Session::get('datos_email');
				\Log::info("cargaPermiso_add -> datos_email: ", array($datos_email));

				// Guardo el nuevo correo en session
				Session::put('nuevoCorreo',$email);
				// Guardo el codigo de validacon en session
				Session::put('codigo_validacion', $codigo_validacion);
				// recupero datos del usuarioLogueado

				$usuarioLogueado = Session::get('usuarioLogueado');
				\Log::info("cargaPermiso_add -> usuarioLogueado: ", array($usuarioLogueado));

				// muestro la vista para ingreso de ID
				// este es el ejemplo -> tramites.cambioDomicilio
				\Log::info("cargaPermiso_add -> muestro la vista: ", array($email, $codigo_validacion));

				// return Response::json(array('mensaje'=>"Hay que pintar la vista que pide el codigo!"));

				// return View::make('tramites.ingreso_codigo_validacion');
				return View::make('tramites.prueba');					
			}
			
		}


		/********************************/
		/*	listar tramites           	*/
		/*	EM-15/04/2014				*/
		/********************************/

		public function listarTramites(){
			$funciones = array(0); // 0: habilitado para todos, 1: únicamente usuario CAS (interno)
			$tiposTramites = $this->servicioTipoTramites->buscarTodos($funciones);

			$usuario = Session::get('usuarioLogueado');
			$usuario=$this->controladorUsuario->buscarPorId($usuario['id']);
			//lista de funciones de trámites que tiene permitidas
			$listaTramitesPermitidos=$usuario['listaFunciones'];
			\Log::info("MM - listarTramites - listaTramitesPermitidos: ", array($listaTramitesPermitidos));
			\Log::info("MM - listarTramites - tiposTramites: ", array($tiposTramites));

			//armo arreglo con los trámites disponibles
			$tipo_usuario= \Session::get('usuarioLogueado.nombreTipoUsuario');

			foreach($tiposTramites as $tipoTramite){
				foreach ($listaTramitesPermitidos as $funcion){
					
				if(strcmp($tipo_usuario,'CAS')==0
					&& ($tipoTramite['id_tipo_tramite']==6 || $tipoTramite['id_tipo_tramite']==7))
						continue;

					$abreviatura = explode("-",$funcion);
					if(strcmp(trim($abreviatura[0]),trim($tipoTramite['abreviatura']))==0 && $tipoTramite['id_tipo_tramite']!=5)
						$combobox[$tipoTramite['id_tipo_tramite']] = $tipoTramite['nombre_tramite'];
				}
			}
			\Log::info("MM - listarTramites - combobox1: ", array($combobox));
			//si el arreglo está vacío=> no tiene permiso para trámites
			if(!isset($combobox)){
				$mensaje='Su tipo de usuario no posee acceso para realizar éste trámite.';
				Session::put('mensaje_acceso',$mensaje);
				return Redirect::to('/acceso-no-autorizado');
			}

			if(Session::has('tramiteGuia')){
				$clave = Session::get('tramiteGuia.id_tipo_tramite');
				$valor = $combobox[$clave];
				unset($combobox[$clave]);
				$combobox=array($clave => $valor)+$combobox;
			}

			\Log::info("MM - listarTramites - combobox2: ", array($combobox));

			$lista=$this->servicioTramites->listaMotivosBaja();
			$listaMotivos="";
			foreach($lista as $clave=>$valor){
				$listaMotivos.=$clave.":".$valor.",";
			}
			$listaMotivos=rtrim($listaMotivos, ",");

			return View::make('tramites.tramites', array("combobox" => $combobox,"listaMotivos"=>$listaMotivos));

		}


		/*********************************/
		/*	ir a tramite       			*/
		/*	EM-15/04/2014				*/
		/********************************/
		public function irTramite(){

			\Log::info("MM - llego a irTramite");
			if(Session::has('tramiteNuevoPermiso'))
				Session::forget('tramiteNuevoPermiso');
			$datos = Input::all();
			$opc = $datos['tramites'];
			if(Session::has('tramiteGuia')){
				Session::forget('tramiteGuia');
			}
		//selecciona el trámite
		\Log::info("MM - tramite - datos: ", array($datos));
		\Log::info("MM - tramite - opc: ", array($opc));
			switch ($opc)
			{
			//*** opción cambio domicilio
			case 1:
				return Redirect::to('/cambio-domicilio');
				break;
			//*** opción cambio de categoría
			case 2:
				return Redirect::to('/cambio-categoria');
				break;
			//*** opción cambio Titular
			case 3:
				return Redirect::to('/cambio-titular');
				break;
			//*** opción cambio dependencia
			case 4:
				return Redirect::to('/cambio-dependencia');
				break;
			//*** opción baja permiso
			case 5:
				return Redirect::to('/baja-permiso');
				break;
			//*** opción incorporación máquina
			case 6:
				return Redirect::to('/incorporar-maquina');
				break;
			//*** opción retiro máquina
			case 7:
				return Redirect::to('/retirar-maquina');
				break;
			//*** opción cotitular
			case 8:
				return Redirect::to('/incorporar-cotitular');
				break;
			/*** opción habilitación permiso
						case 9:
							return Redirect::to('/habilitar-permiso');
							break;
			//*** opción suspensión permiso
						case 10:
							return Redirect::to('/suspender-permiso');
				break;*/
			// opción baja de cotitular
			case 11:
				return Redirect::to('/baja-cotitular');
				break;
			case 12:
				return Redirect::to('/fallecimiento');
				break;
			case 13:
				return Redirect::to('/renuncia');
				break;
			}


		}

/***************************** CAMBIO DOMICILIO **************************/
		/********************************************/
		/*	inicio cambio de domicilio				*/
		/*	EM-16-04-2014							*/
		/********************************************/
		public function cambioDomicilio(){
			Session::forget('ultimoTramite');
			$alta=0;
				$usuario=Session::get('usuarioLogueado');

				\Log::info("cambioDomicilio ->usuario: ", array($usuario));


				if(($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS') && !Session::has('permisoNuevoPermiso')){
					$permiso = Session::get('usuarioTramite')['permiso'];
				}elseif(Session::has('permisoNuevoPermiso')){
					$permiso = Session::get('permisoNuevoPermiso');
				}else{
					$permiso=$usuario['permiso'];
			}

				$datosExtra = null;
				if(Session::has('tramiteNuevoPermiso')){
					$alta=1;
					//buscar si ya se cargó en la red la localidad, cp, etc.
					$redCargada = $this->servicioTramites->obtenerCategoriaPermiso($permiso);
					if($redCargada != "undefined" && !is_null($redCargada)){
						$datosExtra['id_localidad'] = $redCargada['id_localidad'];
						$datosExtra['nombre_localidad'] = $redCargada['nombre_localidad'];
						$datosExtra['nombre_cpscp_localidad'] = trim($redCargada['nombre_localidad'])."(".$redCargada['cp_scp_localidad'].")";
					}
				}
			$titulo=$this->servicioTipoTramites->tituloTramite(1,$alta);
			$titulo=str_replace('Comercial', '', $titulo);
			if(count(Input::old())==0){//para cuando vuelve porque el validador encontró algo erróneo count!=0
				$datos['tipo_tramite']=1;
				self::datosComunesVistas($datos);
				Session::flashInput($datos);
			}

				$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 1);

			$meses=$margen['meses'];
			$dias=$margen['dias'];
			$paso_fecha=$margen['paso_fecha'];
			$cant = $margen['cant'];
			Session::put('ultimoTramite',$margen);

			$bloqIngreso = 0;
			// \log::info("MM - nombreTipoUsuario: ",array($usuario['nombreTipoUsuario']));
			if($usuario['nombreTipoUsuario']=='Agente')
				$bloqIngreso = 1;
			
			//  tipos de documento
			$tiposDocumentos = $this->servicioTipoDocumento->buscarTodos(); 
			\Log::info('MM - cambioDomicilio - 1 tiposDocumentos: ',array($tiposDocumentos));
			// $lista=$this->servicioTramites->listaMotivosBaja();
			$listaDocumentos=array();
			$listaDocumentos[0]='Ingrese Tipo Documento'; // Valor 0 - por defecto y validad en js para que si o si seleccione el tipo de documento
			foreach($tiposDocumentos as $valor){
				$listaDocumentos[$valor['id']]=$valor['nombre'];
			}
			
			return View::make('tramites.cambioDomicilio',
				array(
					'titulo'=>$titulo,
					'meses'=>$meses,
					'dias'=>$dias,
					'paso_fecha'=>$paso_fecha,
					'cant'=>$cant,
					'datosExtra'=>$datosExtra,
					'bloqIngreso'=>$bloqIngreso,
					'tipo_documento'=>$listaDocumentos
					));
		}

		/****************************/
		/* ingreso de domicilio  	*/
		/*	EM-16/04/2014			*/
		/****************************/
		public function cambioDomicilio_add(){
			
			\Log::info("MM - llego a - cambioDomicilio_add - 544");

			$datos = Input::all();

			$reglas=array(
				'agente'=>'required|integer',
				'subagente'=>'required|integer',
				'permiso'=>'required|integer',
				'departamento_id'=>'required|integer',
				'domicilio_comercial'=>'required',
				'referente'=>'required',
				'datos_contacto'=>'required',
				'nueva_localidad'=>'required|integer',
				'motivo_cd'=>'required'
			);
			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"integer"=>"Solo nº"
			);
			$validar=Validator::make($datos,$reglas,$mensaje);

			if($validar->fails()){
				return Redirect::to('cambio-domicilio')->withErrors($validar)->withInput();
			} else{
				$localidad = $this->controladorLocalidad->buscarPorIdLocalidad($datos['nueva_localidad']);
				if(is_null($localidad)){
					return Redirect::to('cambio-domicilio')->withErrors($validar)->withInput();
				}
				$departamento = $this->controladorLocalidad->buscarDepartamentoLocalidad($datos['nueva_localidad']);
				if(is_null($departamento)){
					\Log::error('departamento inexistente.');
					return Redirect::to('cambio-domicilio')->withErrors($validar)->withInput();
				}
			}
				$datos['id_nueva_localidad']=$localidad['id'];
				$datos['cp_scp']=$localidad['codigo_postal'].'-'.$localidad['subcodigo_postal'];
				$datos['nombre_nueva_localidad']=$localidad['nombre'];
				$datos['departamento_id'] = $departamento['id'];
				$datos['nuevo_departamento'] = $departamento['nombre'];
				$datos['tipo_tramite'] = 1;
				//$datos['maxi'] = "maxi";

			if($datos['continuar']){
				\Log::info("cambioDomicilio_add ->datos 1: ", array($datos));
				Session::put('domicilio_cargado',$datos);
				Session::put('domicilio_cargado_para_mail',$datos);
				return Redirect::to('plan-estrategico');
			}else{
					unset($datos_email);
					$datos_email=array();
					$mensaje = $this->servicioTramites->cargarDomicilio($datos, $datos_email);
					Session::flash('mensaje', $mensaje);
					if(Session::has('usuarioTramite'))
						$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
					else
						$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];

					// INI ADEN - 2024-03-26
					$dom_cargado = Session::get('domicilio_cargado');
					\Log::info("cambioDomicilio_add ->datos 2: ", array($dom_cargado));
					$datos_email['nombre_nueva_localidad'] = $datos[$nombre_nueva_localidad];
					// FIN ADEN - 2024-03-26
					Session::put('datos_email',$datos_email);
					Session::forget('permisoPlan');
					Session::forget('domicilio_cargado');
					Session::forget('usuarioTramite');
					Session::forget('tramiteGuia');
					return Redirect::to('exito-tramite');
		}
			}


		/****************************/
		/* ingreso de cambio titular  	*/
		/*	EM-27/07/2021			*/
		/****************************/
/*
		public function cambioDomicilio_add_adap(){
			$datos = Input::all();

			$reglas=array(
				'agente'=>'required|integer',
				'subagente'=>'required|integer',
				'permiso'=>'required|integer',
				'departamento_id'=>'required|integer',
				'domicilio_comercial'=>'required',
				'referente'=>'required',
				'datos_contacto'=>'required',
				'nueva_localidad'=>'required|integer',
				'motivo_cd'=>'required'
			);
			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"integer"=>"Solo nº"
			);
			$validar=Validator::make($datos,$reglas,$mensaje);

			if($validar->fails()){
				return Redirect::to('cambio-domicilio')->withErrors($validar)->withInput();
			} else{
				$localidad = $this->controladorLocalidad->buscarPorIdLocalidad($datos['nueva_localidad']);
				if(is_null($localidad)){
					return Redirect::to('cambio-domicilio')->withErrors($validar)->withInput();
				}
				$departamento = $this->controladorLocalidad->buscarDepartamentoLocalidad($datos['nueva_localidad']);
				if(is_null($departamento)){
					\Log::error('departamento inexistente.');
					return Redirect::to('cambio-domicilio')->withErrors($validar)->withInput();
				}
			}
				$datos['id_nueva_localidad']=$localidad['id'];
				$datos['cp_scp']=$localidad['codigo_postal'].'-'.$localidad['subcodigo_postal'];
				$datos['nombre_nueva_localidad']=$localidad['nombre'];
				$datos['departamento_id'] = $departamento['id'];
				$datos['nuevo_departamento'] = $departamento['nombre'];

			\Log::info('MM - cambioDomicilio_add_adap');

			if($datos['continuar']){
				Session::put('domicilio_cargado',$datos);
				return Redirect::to('plan-estrategico');
			}else{
					unset($datos_email);
					$datos_email=array();
					$mensaje = $this->servicioTramites->cargarDomicilio($datos, $datos_email);
					Session::flash('mensaje', $mensaje);
					if(Session::has('usuarioTramite'))
						$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
					else
						$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];

					Session::put('datos_email',$datos_email);
					Session::forget('permisoPlan');
					Session::forget('domicilio_cargado');
					Session::forget('usuarioTramite');
					Session::forget('tramiteGuia');
					return Redirect::to('exito-tramite');
		}
			}
*/
		/************************************************************/
		/* Plan Estratégico y de inversión para cambio de domicilio */
		/************************************************************/
		/****Arma vista de plan estratégico****/
		/****Para cuando es la primera vez****/
		public function planEstrategico(){
			$datos_domicilio=Session::get('domicilio_cargado');
			$titular_cargado=Session::get('titular_cargado');

			//\Log::info('MM - planEstrategico - titular datos:',array($titular_cargado));
			\Log::info('MM - planEstrategico - datos_domicilio :',array($datos_domicilio));
			\Log::info('MM - planEstrategico - datos_domicilio-tipo_tramite:',array($datos_domicilio['tipo_tramite']));
			Session::put('permisoPlan',$datos_domicilio['permiso']);

			Session::forget('nroTramitePlan');
			// tramite cambio domicilio
			 if($datos_domicilio['tipo_tramite'] == 1){
					try{
						$datosAgencia = $this->servicioTramites->obtenerDatosAgencia($datos_domicilio['permiso']);

						if(!is_null($datosAgencia) || count($datosAgencia)!=0){
							//datos del sugar
							$datos['domicilio_actual']=trim($datosAgencia['domicilio']);
							$datos['localidad_actual']=trim($datosAgencia['localidad']);
							$datos['departamento']=trim($datosAgencia['nombre_departamento']);
							$datos['superficie_actual']=$datosAgencia['superficie'];
							$datos['ventas_actual']=round($datosAgencia['recaudacion']);
							$datos['horario_actual']=$datosAgencia['horario_atencion'];

							$datos['domicilio_nuevo']=trim($datos_domicilio['domicilio_comercial']); // trim($titular_cargado['tipo_tramite']==3?$datos_domicilio['domicilio']:$datos_domicilio['domicilio_comercial']);
							$datos['localidad_nuevo']=trim($datos_domicilio['nombre_nueva_localidad']);
							$datos['persona_contacto_a']=trim($datosAgencia['persona_contacto']);
							$datos['persona_contacto_a_datos']=trim($datosAgencia['persona_contacto_datos']);

							$datos['persona_contacto']=$datos_domicilio['referente'];
							$datos['telefono_contacto']=$datos_domicilio['datos_contacto'];
							if(Session::has('tramiteNuevoPermiso'))
								$agencia = $datos_domicilio['permiso'];
							else
								$agencia=trim($datosAgencia['nombre_agencia']);//$datosAgencia['agente'].'/'.$datosAgencia['subagente'];

							Session::flashInput($datos);
							$anio[strftime('%Y')]=strftime('%Y');
							$anio[strftime('%Y')+1]=strftime('%Y')+1;
							$listaHorario = $this->servicioTramites->listaHorario();
							$listaSuperficie = $this->servicioTramites->listaSuperficie();
							$listaUbicacion = $this->servicioTramites->listaUbicacion();
							$listaVidriera = $this->servicioTramites->listaVidriera();
							$listaCaracteristicas = $this->servicioTramites->listaCaracteristicasZona();
							$listaCirculacion =$this->servicioTramites->listaCirculacion();
							$listaSocioEconomico = $this->servicioTramites->listaSocioEconomico();

							\Log::info('MM - planEstrategico - 698:');

							return View::make('tramites.planEstrategico',array('agencia'=>$agencia,'anio'=>$anio,'lista_superficie'=>$listaSuperficie, 'lista_ubicacion'=>$listaUbicacion,
								'lista_vidriera'=>$listaVidriera, 'horario'=>$listaHorario, 'lista_caracteristicas'=>$listaCaracteristicas,
								'lista_circulacion'=>$listaCirculacion, 'lista_socioeconomico'=>$listaSocioEconomico, 'llamada_de'=>'planEstrategico'));
						}else{

							\Log::error('PlanEstrategico - Los datos de agencia llegaron nulos');
							//if($titular_cargado['tipo_tramite']==3){
								//return Redirect::to('/cambio-titular')->withInput();
							//}else{
								return Redirect::to('/cambio-domicilio')->withInput();
							// }
						}
					}catch(Exception $e){
						\Log::error('Ocurrió algún error al cargar el plan');
						\Log::info($e);

							//if($titular_cargado['tipo_tramite']==3){
								//return Redirect::to('/cambio-titular')->withInput();
							//}else{
								return Redirect::to('/cambio-domicilio')->withInput();
							//}
					}
			 } // fin if 1-cambio domicilio
			 // tramite cambio tirular
			 if($datos_domicilio['tipo_tramite'] == 3){
					try{
						$datosAgencia = $this->servicioTramites->obtenerDatosAgencia($datos_domicilio['permiso']);

						if(!is_null($datosAgencia) || count($datosAgencia)!=0){
							//datos del sugar
							$datos['domicilio_actual']=trim($datosAgencia['domicilio']);
							$datos['localidad_actual']=trim($datosAgencia['localidad']);
							$datos['departamento']=trim($datosAgencia['nombre_departamento']);
							$datos['superficie_actual']=$datosAgencia['superficie'];
							$datos['ventas_actual']=round($datosAgencia['recaudacion']);
							$datos['horario_actual']=$datosAgencia['horario_atencion'];

							$datos['domicilio_nuevo']=trim($datos_domicilio['domicilio']); // trim($titular_cargado['tipo_tramite']==3?$datos_domicilio['domicilio']:$datos_domicilio['domicilio_comercial']);
							$datos['localidad_nuevo']=trim($datos_domicilio['nombre_nueva_localidad']);

							$datos['persona_contacto_a']=trim($datosAgencia['persona_contacto']);
							$datos['persona_contacto_a_datos']=trim($datosAgencia['persona_contacto_datos']);

							$datos['persona_contacto']=$datos_domicilio['referente'];
							$datos['telefono_contacto']=$datos_domicilio['datos_contacto'];
							if(Session::has('tramiteNuevoPermiso'))
								$agencia = $datos_domicilio['permiso'];
							else
								$agencia=trim($datosAgencia['nombre_agencia']);//$datosAgencia['agente'].'/'.$datosAgencia['subagente'];

							Session::flashInput($datos);
							$anio[strftime('%Y')]=strftime('%Y');
							$anio[strftime('%Y')+1]=strftime('%Y')+1;
							$listaHorario = $this->servicioTramites->listaHorario();
							$listaSuperficie = $this->servicioTramites->listaSuperficie();
							$listaUbicacion = $this->servicioTramites->listaUbicacion();
							$listaVidriera = $this->servicioTramites->listaVidriera();
							$listaCaracteristicas = $this->servicioTramites->listaCaracteristicasZona();
							$listaCirculacion =$this->servicioTramites->listaCirculacion();
							$listaSocioEconomico = $this->servicioTramites->listaSocioEconomico();

							\Log::info('MM - planEstrategico - 761:');

							return View::make('tramites.planEstrategico',array('agencia'=>$agencia,'anio'=>$anio,'lista_superficie'=>$listaSuperficie, 'lista_ubicacion'=>$listaUbicacion,
								'lista_vidriera'=>$listaVidriera, 'horario'=>$listaHorario, 'lista_caracteristicas'=>$listaCaracteristicas,
								'lista_circulacion'=>$listaCirculacion, 'lista_socioeconomico'=>$listaSocioEconomico, 'llamada_de'=>'planEstrategico'));
						}else{

							\Log::error('PlanEstrategico - Los datos de agencia llegaron nulos');
							//if($titular_cargado['tipo_tramite']==3){
								return Redirect::to('/cambio-titular')->withInput();
						}
					}catch(Exception $e){
						\Log::error('Ocurrió algún error al cargar el plan');
						\Log::info($e);
						return Redirect::to('/cambio-titular')->withInput();
					}
			 } // fin if 3-cambio titular

			 // tramite 4-cambio dependencia
			  if($datos_domicilio['tipo_tramite'] == 4){
					try{
						$datosAgencia = $this->servicioTramites->obtenerDatosAgencia($datos_domicilio['permiso']);

						if(!is_null($datosAgencia) || count($datosAgencia)!=0){
							//datos del sugar
							$datos['domicilio_actual']=trim($datosAgencia['domicilio']);
							$datos['localidad_actual']=trim($datosAgencia['localidad']);
							$datos['departamento']=trim($datosAgencia['nombre_departamento']);
							$datos['superficie_actual']=$datosAgencia['superficie'];
							$datos['ventas_actual']=round($datosAgencia['recaudacion']);
							$datos['horario_actual']=$datosAgencia['horario_atencion'];
							$datos['domicilio_nuevo']=trim($datos_domicilio['domicilio']); // trim($titular_cargado['tipo_tramite']==3?$datos_domicilio['domicilio']:$datos_domicilio['domicilio_comercial']);
							$datos['localidad_nuevo']=trim($datos_domicilio['nombre_nueva_localidad']);
							$datos['persona_contacto_a']=trim($datosAgencia['persona_contacto']);
							$datos['persona_contacto_a_datos']=trim($datosAgencia['persona_contacto_datos']);

							$datos['persona_contacto']=$datos_domicilio['referente'];
							$datos['telefono_contacto']=$datos_domicilio['datos_contacto'];
							if(Session::has('tramiteNuevoPermiso'))
								$agencia = $datos_domicilio['permiso'];
							else
								$agencia=trim($datosAgencia['nombre_agencia']);//$datosAgencia['agente'].'/'.$datosAgencia['subagente'];

							Session::flashInput($datos);
							$anio[strftime('%Y')]=strftime('%Y');
							$anio[strftime('%Y')+1]=strftime('%Y')+1;
							$listaHorario = $this->servicioTramites->listaHorario();
							$listaSuperficie = $this->servicioTramites->listaSuperficie();
							$listaUbicacion = $this->servicioTramites->listaUbicacion();
							$listaVidriera = $this->servicioTramites->listaVidriera();
							$listaCaracteristicas = $this->servicioTramites->listaCaracteristicasZona();
							$listaCirculacion =$this->servicioTramites->listaCirculacion();
							$listaSocioEconomico = $this->servicioTramites->listaSocioEconomico();

							\Log::info('MM - planEstrategico - 823:');

							return View::make('tramites.planEstrategico',array('agencia'=>$agencia,'anio'=>$anio,'lista_superficie'=>$listaSuperficie, 'lista_ubicacion'=>$listaUbicacion,
								'lista_vidriera'=>$listaVidriera, 'horario'=>$listaHorario, 'lista_caracteristicas'=>$listaCaracteristicas,
								'lista_circulacion'=>$listaCirculacion, 'lista_socioeconomico'=>$listaSocioEconomico, 'llamada_de'=>'planEstrategico'));
						}else{
							\Log::error('PlanEstrategico - Los datos de agencia llegaron nulos');
							return Redirect::to('/cambio-dependencia')->withInput();
						}
					}catch(Exception $e){
						\Log::error('Ocurrió algún error al cargar el plan');
						\Log::info($e);
						return Redirect::to('/cambio-dependencia')->withInput();
					}
			 } // fin if 4-cambio dependencia

		}

		/*Guarda el plan*/
		public function planEstrategico_add(){
			$datos = Input::all();
			 \Log::info('MM - planEstrategico_add - datos: ',array($datos));

			$datos_tabla=explode(',',$datos['tabla_val']);
			$datos_tabla=array_filter($datos_tabla);
			$datos['tabla_val']=$datos_tabla;

			$reglas=array(
				'domicilio_nuevo'=>'required',
				'localidad_nuevo'=>'required',
				'persona_contacto'=>'required',
				'telefono_contacto'=>'required',
				'cant_empleados'=>'required',
				'venta_1'=>'required',
				'venta_2'=>'required',
				'venta_3'=>'required',
				'venta_4'=>'required',
				// 'foto_f'=>'required|image|max:3000',
				'foto_f'=>'image|max:3000',
				// 'foto_i'=>'required|image|max:3000',
				'foto_i'=>'image|max:3000',
				'rubros_amigos'=>'required',
				'rubros_otros'=>'required',  // MM - ojo
				'superficie'=>'not_in:ni',
				'ubicacion'=>'not_in:ni',
				'vidriera'=>'not_in:ni',
				'nivel_socioeconomico_codigo'=>'not_in:ni',

			);
			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"integer"=>"Solo nº"
			);
			$validar=Validator::make($datos,$reglas,$mensaje);

			 \Log::info('MM - planEstrategico_add - pasa');

			if($validar->fails()){
				\Log::info($validar->messages());
				return Redirect::back()->withErrors($validar)->withInput();
			} else{
				try{

					\Log::info('MM - planEstrategico_add - Entra try');

					$datosDomicilio = Session::get('domicilio_cargado');

					\Log::info('MM - planEstrategico_add - datosDomicilio:',array($datosDomicilio));

					\DB::beginTransaction();
					unset($datos_email);
					$datos_email=array();

					$datos['foto_f']=strtolower($datos['foto_f']->getClientOriginalExtension());//extensión
					$datos['foto_i']=strtolower($datos['foto_i']->getClientOriginalExtension());//extensión

					$datos['permisoPlan'] = Session::get('permisoPlan');
					\Log::info('MM - datos->permisoPlan: ',array($datos['permisoPlan']));



					if($datosDomicilio['tipo_tramite'] == 1){
						// agregar el servicioTramites->cargarDomicilio 
						\Log::info('MM - planEstrategico_add - llega');

						$mensaje = $this->servicioTramites->cargarDomicilio($datosDomicilio, $datos_email);
					}elseif($datosDomicilio['tipo_tramite'] == 3){
						//$mensaje = $this->servicioTramites->cargarDomicilio($datosDomicilio, $datos_email);
						$tipoTramite = $datosDomicilio['tipo_tramite'];
						$mensaje = $this->servicioTramites->cargarTitular($datosDomicilio, $datos_email, $tipoTramite);
					}elseif($datosDomicilio['tipo_tramite'] == 4){
						$mensaje = $this->servicioTramites->cargarDependencia($datosDomicilio, $datos_email);
					}
					// if(is_null($mensaje)){
						// return Response::json(array('mensaje'=>"Falló el incio del trámite Cambio de Titular."));
					// }

					$mensajePlan = $this->servicioTramites->cargarPlan($datos,$datos_email['nroTramite']);

					\Log::info('MM - planEstrategico_add - mensajePlan:',array($mensajePlan));

					\DB::commit();
					Session::flash('mensaje', $mensaje);
					if(Session::has('usuarioTramite'))
						$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
					else
						$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];

					Session::put('datos_email',$datos_email);

					Session::forget('permisoPlan');
					Session::forget('domicilio_cargado');
					Session::forget('usuarioTramite');
					Session::forget('tramiteGuia');

					return Redirect::to('exito-tramite');
				}catch(Exception $e){
					\Log::error("Ocurrió un problema al generar el tramite: ".$e->getMessage());
					\DB::rollback();
					$mensaje = "Ocurri&oacute un problema al generar el tramite";
					return Redirect::back();
				}
			}
		}

		/**Arma vista plan cuando es llamado para consulta/modificación**/
		public function llamaPlanEstrategico(){
			\Log::info("MM - entra a llamaPlanEstrategico");
			Session::forget('nroTramitePlan');
			Session::forget('permisoPlan');

			$tramite = Input::get('nroTramite');
			$permiso = Input::get('permiso_pe');

			\Log::info("MM - entra a llamaPlanEstrategico - nroTramite: ",array(Input::get('nroTramite')));
			\Log::info("MM - entra a llamaPlanEstrategico - permiso_pe: ",array(Input::get('permiso_pe')));

			$tipoTramite = $this->servicioTramites->tipoDelTramite($tramite);
				\Log::info("MM - entra a llamaPlanEstrategico - tipoTramite: ",array($tipoTramite));
			if($tipoTramite == 1){
				$domicilio = $this->servicioTramites->obtenerNuevoDomicilio($tramite);
				\Log::info("MM - entra a llamaPlanEstrategico - domicilio: ",array($domicilio));
			}

			$plan = $this->servicioTramites->obtenerPlan($tramite);

			\Log::info("MM - entra a llamaPlanEstrategico - plan: ",array($plan));

			$idPlan = $plan['id'];
			Session::put('idPlanModificado',$idPlan);
			Session::put('nroTramitePlan',$tramite);
			Session::put('permisoPlan',$permiso);

			$datosAgencia = $this->servicioTramites->obtenerDatosAgencia($permiso);

			\Log::info("MM - entra a llamaPlanEstrategico - datosAgencia: ",array($datosAgencia));

			//datos del sugar
			$plan['domicilio_actual']=$datosAgencia['domicilio'];
			$plan['localidad_actual']=$datosAgencia['localidad'];
			$plan['departamento']=$datosAgencia['nombre_departamento'];
			$plan['superficie_actual']=$datosAgencia['superficie'];
			$plan['ventas_actual']=$datosAgencia['recaudacion'];
			$plan['horario_actual']=$datosAgencia['horario_atencion'];
			if($tipoTramite == 1){
				$plan['domicilio_nuevo']=$domicilio['direccion_nueva'];//'San Juan 2344';
				$plan['localidad_nuevo']=$domicilio['nombre_localidad_nueva'];//'Santa Fe';
			}else{
				$plan['domicilio_nuevo']=$datosAgencia['domicilio'];//'San Juan 2344';
				$plan['localidad_nuevo']=$datosAgencia['localidad'];//'Santa Fe';
				//$plan['domicilio_nuevo']=$plan['domicilio_nuevo'];//'San Juan 2344';
				//$plan['localidad_nuevo']=$plan['localidad_nuevo'];//'Santa Fe';
			}
			$plan['persona_contacto_a']=$datosAgencia['persona_contacto'];//'Marcelo Carraspino';
			$plan['persona_contacto_a_datos']=$datosAgencia['persona_contacto_datos'];//'Teléfono: 5534567 Horario disponible: corrido';

			if(Session::has('tramiteNuevoPermiso'))
				$agencia = $permiso;
			else
				$agencia=trim($datosAgencia['nombre_agencia']);

			$anio[strftime('%Y')]=strftime('%Y');
			$anio[strftime('%Y')+1]=strftime('%Y')+1;
			$listaHorario = $this->servicioTramites->listaHorario();
			$listaSuperficie = $this->servicioTramites->listaSuperficie();
			$listaUbicacion = $this->servicioTramites->listaUbicacion();
			$listaVidriera = $this->servicioTramites->listaVidriera();
			$listaCaracteristicas = $this->servicioTramites->listaCaracteristicasZona();
			$listaCirculacion =$this->servicioTramites->listaCirculacion();
			$listaSocioEconomico = $this->servicioTramites->listaSocioEconomico();
			Session::flashInput($plan);
			return View::make('tramites.planEstrategico',array('agencia'=>$agencia,'anio'=>$anio,'lista_superficie'=>$listaSuperficie, 'lista_ubicacion'=>$listaUbicacion,
				'lista_vidriera'=>$listaVidriera, 'horario'=>$listaHorario, 'lista_caracteristicas'=>$listaCaracteristicas,
				'lista_circulacion'=>$listaCirculacion, 'lista_socioeconomico'=>$listaSocioEconomico, 'llamada_de'=>'llamaPlanEstrategico'));
		}

		/*Guarda el plan modificado*/
		public function planEstrategicoModificado_add(){
			$datos = Input::all();
			$datos_tabla=explode(',',$datos['tabla_val']);
			$datos_tabla=array_filter($datos_tabla);
			$datos['tabla_val']=$datos_tabla;
			$reglas=array(
				'domicilio_nuevo'=>'required',
				'localidad_nuevo'=>'required',
				'persona_contacto'=>'required',
				'telefono_contacto'=>'required',
				'cant_empleados'=>'required',
				'venta_1'=>'required',
				'venta_2'=>'required',
				'venta_3'=>'required',
				'venta_4'=>'required',
				'rubros_amigos'=>'required',
				'rubros_otros'=>'required',
				'superficie'=>'not_in:ni',
				'ubicacion'=>'not_in:ni',
				'vidriera'=>'not_in:ni',
				'nivel_socioeconomico_codigo'=>'not_in:ni',
			);

			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"integer"=>"Solo nº"
			);
			$validar=Validator::make($datos,$reglas,$mensaje);

			if($validar->fails()){
				\Log::info("Fallo al modificar el plan");
				\Log::info($validar->messages());
				\Log::info($datos);
				return Redirect::back()->withErrors($validar)->withInput();
			} else{
				try{
					\DB::beginTransaction();

					$id_plan = Session::get('idPlanModificado');

					$nro_tramite = Session::get('nroTramitePlan');
					//$extension=$datos['foto_f']->getClientOriginalExtension();
					$fotoF=Input::file('foto_f');
					$fotoI=Input::file('foto_i');
					if(is_null($fotoF)){
						$datos['foto_f']='';
					}else{
						$datos['foto_f']=$fotoF->getClientOriginalExtension();
					}
					if(is_null($fotoI)){
						$datos['foto_i']='';
					}else{
						$datos['foto_i']=$fotoI->getClientOriginalExtension();
					}

					$datos['permisoPlan'] = Session::get('permisoPlan');
					$mensajePlan = $this->servicioTramites->cargarPlanModificado($datos,$id_plan, $nro_tramite);
					\DB::commit();
					Session::flash('mensaje', $mensaje);
					Session::forget('usuarioTramite');
					Session::forget('tramiteGuia');
					Session::forget('idPlanModificado');
					Session::forget('nroTramitePlan');

					$accion = 'TramitesController@detallePorNroSeguimiento';
					$metodo = 'post';

					return View::make('tramites.exitoModificacionPlan', array('nroTramite'=>$nro_tramite, 'accion'=>$accion, 'metodo'=>$metodo));
				}catch(Exception $e){
					Log::error("Ocurrió un problema al modificar el plan: ".$e->getMessage());
					\DB::rollback();
					$mensaje = "Ocurrió un problema al modificar el plan";
					return Redirect::back();
				}
			}
		}

		/**
		* Función que se encarga de generar la ruta al
		* reporte del plan en formato pdf
		*/
		public function plan_pdf(){
			Session::forget('url_pdf');
			$nro_tramite = Input::get('nro_tramite');

			$plan = $this->servicioTramites->obtenerPlan($nro_tramite);
			if(is_null($plan)){
				return Response::json(array('mensaje'=>"El plan no existe."));
			}else{
				//armado de la url para el reporte
				$url_repositorio = Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
				$url_repositorio .= Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.plan_estrategico')."&";
				$url_repositorio .="formato=".Config::get('habilitacion_config/jasper_config.formatos_reportes.2')."&";
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_plan_estrategico.nombres.0')."=".$nro_tramite;

				Session::put('url_pdf',$url_repositorio);
				return Response::json(array('mensaje'=>'mensaje'));
			}
		}


		public function show_plan_pdf(){
			$url_pdf = Session::get('url_pdf');

			return View::make('reportes.reportes_tramites', array('url_repositorio'=>$url_pdf));
		}


		/**********************Carga fotos plan**************************/
		public function cargaFotoInterior(){
			try{
					$ds = DIRECTORY_SEPARATOR;
					if(Session::has('nroTramitePlan')){
						$nroTramite = Session::get('nroTramitePlan');
						$destinationPath = public_path(). $ds . 'upload'.$ds.'CD_'.$nroTramite.'_';
					}else{
						$destinationPath = public_path(). $ds . 'upload'.$ds.'CD_';
					}


		            if(Input::hasFile('foto_i')){
			                $file = Input::file('foto_i'); // your file upload input field in the form should be named 'file'
			       			$nombre = $file->getClientOriginalName();
			       			$extension=strtolower ($file->getClientOriginalExtension());

			       			$rules = array(
						       'file' => 'required|mimes:jpg,png,gif,jpeg,jpg|max:20000'
						    );
						    $validator = \Validator::make(array('file'=> $file), $rules);

						    if($validator->passes()){

				       			if(Session::has('permisoPlan')){
				       				$permiso =Session::get('permisoPlan');
				       				$nombre = $permiso."_I.".$extension;
								 	$destinationPath .= $permiso;
				       			}else{
				       				if(Session::has('usuarioTramite')){
								 		$permiso = Session::get('usuarioTramite.permiso');
								 		$nombre = $permiso."_I.".$extension;
								 		$destinationPath .= $permiso;
								 	}else{
								 		$permiso = Session::get('usuarioLogueado.permiso')['permiso'];
								 		$nombre = $permiso."_I.".$extension;
								 		$destinationPath .= $permiso;
								 	}
				       			}

				                if(!is_dir($destinationPath)){
								  File::makeDirectory($destinationPath, 0777, true);
								}
								//almaceno los archivos anteriores para luego eliminarlos
								$existeFoto=glob($destinationPath.$ds.$permiso.'_I.*');

				                $uploadSuccess = Input::file('foto_i')->move($destinationPath, $nombre);

				                if($uploadSuccess) {
									if(count($existeFoto)>0){
										foreach($existeFoto as $foto){
											if(strcasecmp($foto, $destinationPath.$ds.$nombre )!== 0)
												unlink($foto);
										}
									}
				                	if(Session::has('nroTramitePlan')){
				                		$nroTramite = Session::get('nroTramitePlan');
				                    	return Response::json(array('success'=>200,'path_fi'=>'upload/CD_'.$nroTramite.'_'.$permiso.'/'.$nombre));
				                    }else{
				                    	return Response::json(array('success'=>200,'path_fi'=>'upload/CD_'.$permiso.'/'.$nombre));
				                    }
				                } else {
				                    return Response::json('error', 400);
				                }
		     				}else{
						 		\Log::error('No es un tipo de imagen válido');
						 		return Response::json('error', 400);
						 	}//fin validator
		     		}else{
			     		return Response::json('error', 400);
		     			}
		 		}catch(Exception $e){
		 			\Log::error('Problema al cargar la foto del interior');
		 			return Response::json('error', 400);
		 		}

		}

		public function cargaFotoFrente(){
			try{
					$ds = DIRECTORY_SEPARATOR;
					\Log::info('MM - tramiteController - cargaFotoFrente: ',array(Session::get('nroTramitePlan')));
					if(Session::has('nroTramitePlan')){
						$nroTramite = Session::get('nroTramitePlan');
						$destinationPath = public_path(). $ds . 'upload'.$ds.'CD_'.$nroTramite.'_';
					}else{
						$destinationPath = public_path(). $ds . 'upload'.$ds.'CD_';
					}


		            if(Input::hasFile('foto_f')){
			                $file = Input::file('foto_f'); // your file upload input field in the form should be named 'file'
			       			$nombre = $file->getClientOriginalName();
		       			    $extension= strtolower($file->getClientOriginalExtension());

			       			$rules = array(
						       'file' => 'required|mimes:jpg,png,gif,jpeg,jpg|max:20000'
						    );
						    $validator = \Validator::make(array('file'=> $file), $rules);

						    if($validator->passes()){
				       			if(Session::has('permisoPlan')){
				       				$permiso =Session::get('permisoPlan');
				       				$nombre = $permiso."_F.".$extension;
								 	$destinationPath .= $permiso;
				       			}else{
				       				if(Session::has('usuarioTramite')){
								 		$permiso = Session::get('usuarioTramite.permiso');
								 		$nombre = $permiso."_F.".$extension;
								 		$destinationPath .= $permiso;
								 	}else{
								 		$permiso = Session::get('usuarioLogueado.permiso')['permiso'];
								 		$nombre = $permiso."_F.".$extension;
								 		$destinationPath .= $permiso;
								 	}
				       			}


				                $filename = $file->getClientOriginalName();
				                if(!is_dir($destinationPath)){
								  File::makeDirectory($destinationPath, 0777, true);
								}

								//almaceno los archivos anteriores para luego eliminarlos
								$existeFoto=glob($destinationPath.$ds.$permiso.'_F.*');

				                $uploadSuccess = Input::file('foto_f')->move($destinationPath, $nombre);

				                if($uploadSuccess) {
									if(count($existeFoto)>0){
										foreach($existeFoto as $foto){
											if(strcasecmp($foto, $destinationPath.$ds.$nombre )!== 0)
												unlink($foto);
										}
									}
				                	if(Session::has('nroTramitePlan')){
				                		$nroTramite = Session::get('nroTramitePlan');
				                    	return Response::json(array('success'=>200,'path_ff'=>'upload/CD_'.$nroTramite.'_'.$permiso.'/'.$nombre));
				                    }else{
				                    	return Response::json(array('success'=>200,'path_ff'=>'upload/CD_'.$permiso.'/'.$nombre));
				                    }
				                } else {
				                    return Response::json('error', 400);
				                }
			     			}else{
						 		\Log::error('No es un tipo de imagen válido');
						 		return Response::json('error', 400);
						 	}//fin validator
		     		}else{
			     		return Response::json('error', 400);
		     			}
		 		}catch(Exception $e){
		 			\Log::error();
		 			return Response::json('error', 400);
		 		}
		}


		/******************************
		* Actualización del domicilio
		*******************************/
		public function actualizarDomicilio(){
			$nuevoDomicilio = Input::get('nueva_direccion');
			$viejoDomicilio=Input::get('domicilio_viejo');
			if(strcmp(trim($nuevoDomicilio), $viejoDomicilio)==0){
				return Response::json(array('exito'=>1,'mensaje' => 'El domicilio a actualizar no tiene modificaciones.'));
			}else{
				$datos = Input::all();
				$resultado=$this->servicioTramites->actualizarDomicilio($datos);
				return Response::json(array('exito'=>1,'mensaje' => 'Domicilio actualizado.'));
			}

		}

/****************************************CAMBIO CATEGORIA************************************************/
		/********************************************/
		/*	inicio cambio de categoria  	    	*/
		/********************************************/
		public function cambioCategoria(){
			Session::forget('ultimoTramite');
			$alta=0;
				$datosExtra=array();
			if(Session::has('tramiteNuevoPermiso')){
				$alta=1;
			}

				$usuario=Session::get('usuarioLogueado');
				if(($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS') && !Session::has('permisoNuevoPermiso')){
					$permiso = Session::get('usuarioTramite')['permiso'];
				}elseif(Session::has('permisoNuevoPermiso')){
					$permiso = Session::get('permisoNuevoPermiso');
				}else{
					$permiso=$usuario['permiso'];
				}
				
			if($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS'){
				$datosExtra['nombreTipoUsuario'] = $usuario['nombreTipoUsuario'];
			}else{
				$datosExtra['nombreTipoUsuario'] = Session::get('usuarioTramite.nombreTipoUsuario');
			}
			$titulo=$this->servicioTipoTramites->tituloTramite(2,$alta);
			if(Session::has('usuarioTramite')){//es cas y entra a hacerle el trámite a un agente/subagente
				if(Session::get('usuarioTramite.nombreTipoUsuario')=='SubAgente'){
					$categorias['0'] = 'agente';
				}else if(Session::get('usuarioTramite.nombreTipoUsuario')=='Agente'){
					$categorias['1'] = 'subagente';
				}

			}else if(Session::has('tramiteNuevoPermiso')){
				$tramite = Session::get('tramiteNuevoPermiso');
				if($tramite['subagente']=='1'){
					$categorias['0'] = 'agente';
				}

					//buscar si ya se cargó en el domicilio la localidad, cp, etc.
					$domCargado = $this->servicioTramites->completoCambioDomicilioNP($permiso);
					if($domCargado != "undefined" && !is_null($domCargado)){
						$datosExtra['id_localidad'] = $domCargado['id_localidad_nueva'];
						$datosExtra['nombre_localidad'] = $domCargado['nombre_localidad_nueva'];
						$datosExtra['nombre_cpscp_localidad'] = trim($domCargado['nombre_localidad_nueva'])."(".$domCargado['cp_scp_localidad_nueva'].")";
						$datosExtra['cpscp_localidad'] = $domCargado['cp_scp_localidad_nueva'];

					}

			}else{ //es un agente/subagente
				if(Session::get('usuarioLogueado.nombreTipoUsuario')=='SubAgente'){
					$categorias['0'] = 'agente';
				}else if(Session::get('usuarioLogueado.nombreTipoUsuario')=='Agente'){
					$categorias['1'] = 'subagente';
				}
			}

			$datos['tipo_tramite']=2;
			self::datosComunesVistas($datos);

			Session::flashInput($datos);

			$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 2);
			$meses=$margen['meses'];
			$dias=$margen['dias'];
			$paso_fecha=$margen['paso_fecha'];
			$cant = $margen['cant'];
			$on=0; // se crea variable on para mostrar/ocultar elementos por tipo de usuario
			Session::put('ultimoTramite',$margen);
			
			//  tipos de documento
			$tiposDocumentos = $this->servicioTipoDocumento->buscarTodos(); 
			// \Log::info('MM - cambioDomicilio - tiposDocumentos: ',array($tiposDocumentos));
			// $lista=$this->servicioTramites->listaMotivosBaja();
			$listaDocumentos=array();
			$listaDocumentos[0]='Ingrese Tipo Documento'; // Valor 0 - por defecto y validad en js para que si o si seleccione el tipo de documento
			foreach($tiposDocumentos as $valor){
				$listaDocumentos[$valor['id']]=$valor['nombre'];
			}

				if(!Session::has('tramiteNuevoPermiso')){ // no es un trámite de nuevo permiso
				
					if(Session::has('usuarioTramite')){// es cas y entra a crear el trámite a un agente/subagente
						\Log::info("Cambio categoria: ", array(Session::get('usuarioTramite')));
						$codigoPostal=Session::get('usuarioTramite')['codigoPostalAgencia'];
						$on=1;
					}else{ //es un agente/subagente
						$codigoPostal=Session::get('usuarioLogueado.codigoPostalAgencia');
						$on=0;
					}

					if(array_key_exists(0, $categorias))
						$nro_red = $this->servicioTramites->numeroNuevaRed($codigoPostal, 0, 1);
					else
						$nro_red = '';

				}else{
					$nro_red = '';
				}

				return View::make('tramites.cambioCategoria', array('titulo'=>$titulo,'categorias'=>$categorias, 'meses'=>$meses,'dias'=>$dias, 'paso_fecha'=>$paso_fecha,'cant'=>$cant, 'cbu'=>$datos['cbu'], 'nro_red'=>$nro_red, 'datosExtra'=>$datosExtra, 'on'=>$on, 'tipo_documento'=>$listaDocumentos));
			}

		/****************************/
		/* ingreso de categoria  	*/
		/****************************/

		public function cambioCategoria_add(){
			$datos = Input::all();
			if(array_key_exists("cbu", $datos) && $datos['cbu']!=""){
				$reglas['cbu']='min:4';
				}

				if(Session::has('tramiteNuevoPermiso')){
					$reglas['nueva_localidad']='required|integer';

					$localidad = $this->controladorLocalidad->buscarPorIdLocalidad($datos['nueva_localidad']);
					$datos['id_localidad']=$localidad['id'];
					$datos['cp_scp']=$localidad['codigo_postal'].'-'.$localidad['subcodigo_postal'];
					$datos['nombre_localidad']=$localidad['nombre'];

				}else{
					$datos['id_localidad']=0;
					$datos['cp_scp']='';
					$datos['nombre_localidad']='';
			}

				//mensajes de validación
				$mensaje=array(
					"required"=>"Campo requerido",
					"integer"=>"Solo nº"
				);

				if(isset($reglas)){
					$validar=Validator::make($datos,$reglas,$mensaje);

					if($validar->fails()){
						\Log::info("Fallo al cargar cambio de categoria");
						\Log::info($validar->messages());
						\Log::info($datos);
						return Redirect::back()->withErrors($validar)->withInput();
					}
				}

			//Pasó la validación
			unset($datos_email);
			$datos_email=array();
			\DB::beginTransaction();
			try{
				$mensaje = $this->servicioTramites->cargarCategoria($datos, $datos_email);
				Session::flash('mensaje', $mensaje);
				if(Session::has('usuarioTramite'))
					$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
				else if(Session::has('tramiteNuevoPermiso'))
					$datos_email['email_duenio'] = 'emailvacio@gemeil.com';
				else
					$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];

				Session::put('datos_email',$datos_email);
				Session::forget('usuarioTramite');
				Session::forget('tramiteGuia');
				\DB::commit();
				return Redirect::to('exito-tramite');
			}catch(Exception $e){
				\DB::rollback();
				\Log::error($e);
				\Log::error('Problema al generar el trámite de cambio de categoria');
				return Redirect::back()->withInput($datos);
			}
		}

		/**
		 * Función que devuelve el número de red para un cambio
		 * de subagente a agente
		 */
		public function numeroNuevaRed(){
			$codigoPostal=Input::get('codigoPostal');
				$nro_red=Input::get('nro_red');
				$modalidad=Input::get('modalidad');
				\Log::info("numeroNuevaRed (controller): ", array($nro_red));
				$nroRed = $this->servicioTramites->numeroNuevaRed($codigoPostal, $nro_red, $modalidad);
			return Response::json(array('nro_nueva_red'=>$nroRed));
		}

		public function nuevaRedPropuesta(){
			$nroRed = Input::get('nro_red');
			$esRedValida = $this->servicioTramites->nuevaRedPropuesta($nroRed);
			return Response::json(array('mensaje'=>$esRedValida));
		}

		/**
		* Función que comprueba para el caso de agencias red (subagente=0)
		* si tienen agencias activas o no para permitir la aprobación del
		* trámite cambio de categoria.
		**/
		public function okAprobar(){
			$permiso = Input::get('permiso');
			$ok = $this->servicioTramites->okAprobar($permiso);
			return Response::json(array('exito'=>$ok));
		}

/***************************CAMBIO TITULAR**********************************************/

		/********************************************/
		/*	inicio cambio de titular				*/
		/*	EM-29-04-2014							*/
		/********************************************/
		public function cambioTitular(){
			\Log::info("MM - llego a - cambioTitular - Input ",array(Input::all()));
			\Log::info("MM - llego a - cambioTitular - Session ",array(Session::all()));
			// \Log::info("MM - llego a - cambioTitular - Session2 ",array(session()->all()));

			Session::forget('ultimoTramite');
			Session::forget('cuitPermiso');
			// Session::flush();
			$cuitPermiso = '';
			$datos['tipo_tramite']=3;
			$tipo = 3;
			self::datosComunesVistas($datos);

			//antes 1-física 2-juridica
			$tipo_persona = $this->servicioTramites->listaTiposPersonas();
			$tipo_documento = $this->servicioTramites->listaTiposDocumentos();

			$tipo_sociedad = $this->servicioTramites->listaTiposSociedad();
			$tipo_situacion = $this->servicioTramites->listaSituacionGanancias();
			$tipo_ocup_titu = $this->servicioTramites->listaOcupacionPersonas();
			$alta=0;
			
			$tiposDocumentos = $this->servicioTipoDocumento->buscarTodos(); 
			\Log::info('MM - cambioDomicilio - 2 tiposDocumentos: ',array($tiposDocumentos));
			// $lista=$this->servicioTramites->listaMotivosBaja();
			$listaDocumentos=array();
			$listaDocumentos[0]='Ingrese Tipo Documento'; // Valor 0 - por defecto y validad en js para que si o si seleccione el tipo de documento
			foreach($tiposDocumentos as $valor){
				$listaDocumentos[$valor['id']]=$valor['nombre'];
			}
			// usuarioTramite
			if(Session::has('tramiteNuevoPermiso')){
				$alta=1;
			}
			$titulo=$this->servicioTipoTramites->tituloTramite(3,$alta);
			\Log::info("MM - llego a - titulo: ",array($titulo));
			//para cuando vuelve porque el validador encontró algo erróneo
			if(count(Input::old())==0){

				Session::flashInput($datos);
				$usuario=Session::get('usuarioLogueado');

				if(($usuario['nombreTipoUsuario']!='CAS' && $usuario['nombreTipoUsuario']!='CAS_ROS') && !Session::has('tramiteNuevoPermiso')){
					$permiso = Session::get('usuarioTramite')['permiso'];
					$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 3);
					
					\Log::info("AE - cambioTitular - permiso per1: ",array($permiso));
					
				}else if(\Session::has('tramiteNuevoPermiso')){
					$permiso=Session::get('tramiteNuevoPermiso')['nro_permiso'];
					\Log::info("AE - cambioTitular - permiso per2: ",array($permiso));
					$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 3);
				}else{
					$margen=$this->servicioHistorico->fechaUltimoTramite($usuario['permiso'], 3);
					
					 \Log::info("MM - cambioTitular - permiso usu: ",array($usuario)); 
					
				}

				$cuitPermiso = '';
/* ADEN - 2024-04-26 - TODO ESTO DEL CUITPERMISO PARA CONTROLAR QUE NO SEA IGUAL AL NUEVO CUIT ESTA SIN TERMINAR, SE ANULA HASTA COMPLETAR!!!!
				 \Log::info("MM - cambioTitular - permiso usuarioTramite: ",array(Session::get('usuarioTramite')['permiso']));
				 
				 
				// con el permiso traer el cuit de
				// 1- invocar servicio
				if(null !== Session::get('cuitPermiso')){
					$cuitPermiso=Session::get('cuitPermiso');
					\Log::info("AE - cambioTitular - cuitPermiso: ",array($cuitPermiso)); 
				}else{
					\Log::info("AE - cambioTitular - cuitPermiso: ",array($cuitPermiso)); 
					$cuitPermiso=$this->servicioTipoTramites->cuitPermiso(Session::get('usuarioTramite')['permiso']);
					$cuitPermiso= $cuitPermiso[0]->cuit;
				}
*/				
				/*
					SELECT 	UPPER(TRIM(p.dni_cuit_titular_c)) AS cuit
					FROM suitecrm_cas_dev.age_permiso ap
					INNER JOIN suitecrm_cas_dev.age_personas p ON ap.age_personas_id_c = p.id  AND ap.estado="activo"
					WHERE ap.id_permiso = 6001;
				*/
				 
				 // \Log::info("MM - cambioTitular - cuitPermiso: ",array($cuitPermiso['cuit']));  $tituloTramite[0]->nombre_alta_tramite
				 \Log::info("MM - cambioTitular - cuitPermiso2: ",array($cuitPermiso));
				 // \Log::info("MM - cambioTitular - cuitPermiso2: ",array($cuitPermiso[0]->cuit));
					 
				$bloqIngreso = 0;

				if($usuario['nombreTipoUsuario']=='Agente')
					$bloqIngreso = 1;

				$meses=$margen['meses'];
				$dias=$margen['dias'];
				$paso_fecha=$margen['paso_fecha'];
				$cant = $margen['cant'];
				Session::put('ultimoTramite',$margen);
				Session::put('cuitPermiso',$cuitPermiso);

				return View::make('tramites.cambioTitular', array('titulo'=>$titulo,'tipo'=>$tipo,'tipo_persona'=>$tipo_persona,'meses'=>$meses, 'dias'=>$dias, 'paso_fecha'=>$paso_fecha, 'cant'=>$cant,'tipo_sociedad'=>$tipo_sociedad, 'tipo_situacion'=>$tipo_situacion, 'tipo_doc'=>$tipo_documento, 'tipo_ocup_tit'=>$tipo_ocup_titu,'bloqIngreso'=>$bloqIngreso,'listaDocumentos'=>$listaDocumentos, 'cuitPermiso'=>$cuitPermiso));
			}
			$usuario=Session::get('usuarioLogueado');
			if($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS'){
				$permiso = Session::get('usuarioTramite')['permiso'];
				$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 1);
			}else{
				$margen=$this->servicioHistorico->fechaUltimoTramite($usuario['permiso'], 1);
			}
			
			if(null !== Session::get('cuitPermiso')){
						$cuitPermiso=Session::get('cuitPermiso');
					}else{
						$cuitPermiso=$this->servicioTipoTramites->cuitPermiso(Session::get('usuarioTramite')['permiso']);
						$cuitPermiso= $cuitPermiso[0]->cuit;
					}
			$meses=$margen['meses'];
			$dias=$margen['dias'];
			$paso_fecha=$margen['paso_fecha'];
			$cant = $margen['cant'];

			Session::put('ultimoTramite',$margen);
			Session::put('cuitPermiso',$cuitPermiso);


				return View::make('tramites.cambioTitular',array('titulo'=>$titulo,'tipo'=>$tipo,'tipo_persona'=>$tipo_persona,'meses'=>$meses, 'dias'=>$dias, 'paso_fecha'=>$paso_fecha, 'cant'=>$cant,'tipo_sociedad'=>$tipo_sociedad, 'tipo_situacion'=>$tipo_situacion, 'tipo_doc'=>$tipo_documento, 'tipo_ocup_tit'=>$tipo_ocup_titu,'listaDocumentos'=>$listaDocumentos,'cuitPermiso'=>$cuitPermiso));

		}

		/****************************/
		/* ingreso de titular  		*/
		/*	EM-29/04/2014			*/
		/****************************/
		public function cambioTitular_add(){

				\Log::info("AE - cambioTitular_add - inicio de proceso");
				

				$datos = Input::all();
				\Log::info("AE - cambioTitular_add - datos: ",array($datos));

				$reglas=array(
					'agente'=>'required|integer',
					'subagente'=>'required|integer',
					'permiso'=>'required|integer',
					'departamento_id'=>'required|integer',
					'localidad_actual_id'=>'required|integer',
					'domicilio'=>'required',
					// 'domicilio_comercial'=>'required',
					//'email'=>'required|email',

					'referente'=>'required',
					'datos_contacto'=>'required',
					'nueva_localidad'=>'required|integer',
					'motivo_ct'=>'required'
				);

				if($datos['tipo_persona']=="J"){
					$reglas['tipo_persona']='required|in:F,J|TipoPersona:'.$datos['cuit'].',J';
					$reglas['cuit']='required|Cuit';
					$reglas['tipo_sociedad']='required';
					$reglas['razon_social']='required';
				}else{//persona física
					$reglas['sexo_persona']='required|in:F,M,S';//S=Sin especificar
					if($datos['cuit']!=''){
						$reglas['tipo_persona']='required|in:F,J|TipoPersona:'.$datos['cuit'].','.$datos['sexo_persona'];
					}else{
						$reglas['tipo_persona']='required|in:F,J';
					}

					$reglas['apellido_nombre']='required';
					$reglas['tipo_doc']='required';
					$reglas['nro_doc']='required|Documento:'.$datos['nro_doc'];
					//$reglas['fecha_nac']='required|FechaNacimiento:'.$datos['fecha_nac'];
				}
				//CBU
				/*
					if($datos['subagente']==0){//pasa a ser titular de una red
					$reglas['cbu']='max:4';
				}*/

				//IIBB
				// $reglas['ingresos']='IngresosBrutos:'.$datos['ingresos'];

				// Datos del Exámen
				// fecha_examen
				$reglas['fecha_examen']='required:'.$datos['fecha_examen'];
				// intentos
				$reglas['intentos']='required|integer:'.$datos['intentos'];
				// calificacion
				$reglas['calificacion']='required|integer:'.$datos['calificacion'];


				\Log::info("AE - cambioTitular_add - reglas: ",array($reglas));
				
				//mensajes de validación
				
				$mensaje=array(
					"required"=>"Campo requerido",
					"integer"=>"Solo nº"
				);
				$validar=Validator::make($datos,$reglas,$mensaje);

				if($validar->fails()){
					\Log::info($validar->messages());
					return Redirect::to('cambio-titular')->withErrors($validar)->withInput();
				} else{
					$localidad = $this->controladorLocalidad->buscarPorIdLocalidad($datos['nueva_localidad']);
					if(is_null($localidad)){
						return Redirect::to('cambio-titular')->withErrors($validar)->withInput();
					}
					$departamento = $this->controladorLocalidad->buscarDepartamentoLocalidad($datos['nueva_localidad']);
					if(is_null($departamento)){
						\Log::error('departamento inexistente.');
						return Redirect::to('cambio-titular')->withErrors($validar)->withInput();
					}
				}

				$datos['id_nueva_localidad']=$localidad['id'];
				$datos['cp_scp']=$localidad['codigo_postal'].'-'.$localidad['subcodigo_postal'];
				$datos['nombre_nueva_localidad']=$localidad['nombre'];

				$datos['departamento_id'] = $departamento['id'];
				$datos['nuevo_departamento'] = $departamento['nombre'];

				$datosT = $datos;
				$tipoTramite = 3;
				$datos['tipo_tramite'] = $tipoTramite;
				$datosT['tipo_tramite'] = $tipoTramite;
				\Log::info('AE - cambioTitular_add - datos: ', array($datos));
				\Log::info('AE - cambioTitular_add - datosT: ', array($datosT));

				if($datos['continuar']){
					Session::put('domicilio_cargado',$datos);
					Session::put('titular_cargado',$datosT);
					\Log::info('MM - TramiteController - llego a enviar plan: ');
					return Redirect::to('plan-estrategico');
				}else{
					unset($datos_email);
					$datos_email=array();
					\Log::info('AE - cambioTitular_add - datos 2: ', array($datos));
					\Log::info('AE - cambioTitular_add - datosT 2: ', array($datosT));
					$mensaje = $this->servicioTramites->cargarTitular($datos, $datos_email, $tipoTramite);

					if(is_null($mensaje)){
						return Response::json(array('mensaje'=>"Falló el incio del trámite Cambio de Titular."));
					}

					Session::flash('mensaje', $mensaje);

					if(Session::has('usuarioTramite'))
						$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
					else
						$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];
						Session::put('datos_email',$datos_email);

						Session::forget('permisoPlan');
						Session::forget('domicilio_cargado');
						Session::forget('titular_cargado');
						Session::forget('usuarioTramite');
						Session::forget('tramiteGuia');
					return Redirect::to('exito-tramite');
				}
			}

		/********************************************/
		/*	inicio fallecimiento					*/
		/*	MM-09-08-2021							*/
		/********************************************/
		public function fallecimientoTitular(){

			Session::forget('ultimoTramite');
			$datos['tipo_tramite']=12;
			$tipo = 12;
			self::datosComunesVistas($datos);

			//antes 1-física 2-juridica
			$tipo_persona = $this->servicioTramites->listaTiposPersonas();
			$tipo_documento = $this->servicioTramites->listaTiposDocumentos();

			$tipo_sociedad = $this->servicioTramites->listaTiposSociedad();
			$tipo_situacion = $this->servicioTramites->listaSituacionGanancias();
			$tipo_ocup_titu = $this->servicioTramites->listaOcupacionPersonas();
			$tipo_tipo_relacion = $this->servicioTramites->listaTipoRelacion();
			$tipo_tipo_vinculo = $this->servicioTramites->listaTipoVinculo();
			$alta=0;

			if(Session::has('tramiteNuevoPermiso')){
				$alta=1;
			}
			$titulo=$this->servicioTipoTramites->tituloTramite(12,$alta);

			/*							*/
			\Log::info('MM - fallecimientoTitular - usuarioLogueado ',array(Session::get('usuarioLogueado')));
			\Log::info('MM - fallecimientoTitular - usuarioTramite ',array(Session::get('usuarioTramite')));

				$departamento_f =$this->controladorLocalidad->buscarDepartamentoLocalidadPorCP_SCP(Session::get('usuarioLogueado.codigoPostalAgencia'), Session::get('usuarioLogueado.subcodigoPostalAgencia'));
				$departamento_nom_f = $departamento_f['nombre'];
				$departamento_id = $departamento_f['id'];

				if(Session::has('usuarioTramite')){
					$domicilio_agencia_f = Session::get('usuarioTramite.domicilioAgencia');
					$localidad_actual_id_f = Session::get('usuarioTramite.idLocalidad');
					$codigo_postal_f = Session::get('usuarioTramite.codigoPostalAgencia');
					$subcodigo_postal_f = Session::get('usuarioTramite.subcodigoPostalAgencia');
					$localidad_actual_f = Session::get('usuarioTramite.localidadAgencia').' ('.$codigo_postal_f.'-'.$subcodigo_postal_f.')';
				}else{
					$domicilio_agencia_f = Session::get('usuarioLogueado.domicilioAgencia');
					$codigo_postal_f = Session::get('usuarioLogueado.codigoPostalAgencia');
					$subcodigo_postal_f = Session::get('usuarioLogueado.subcodigoPostalAgencia');
					$localidad_actual_f = Session::get('usuarioLogueado.localidadAgencia').' ('.$codigo_postal_f.'-'.$subcodigo_postal_f.')';
					$localidad_actual_id_f =Session::get('usuarioLogueado.idLocalidad');
				}

			$tiposDocumentos = $this->servicioTipoDocumento->buscarTodos(); 
			\Log::info('MM - fallecimientoTitular - tiposDocumentos: ',array($tiposDocumentos));

			$listaDocumentos=array();
			$listaDocumentos[0]='Ingrese Tipo Documento'; // Valor 0 - por defecto y validad en js para que si o si seleccione el tipo de documento
			foreach($tiposDocumentos as $valor){
				$listaDocumentos[$valor['id']]=$valor['nombre'];
			}

			/*							*/

			//para cuando vuelve porque el validador encontró algo erróneo
			if(count(Input::old())==0){

				\Log::info('AE - fallecimientoTitular - count = 0: ');

				Session::flashInput($datos);
				$usuario=Session::get('usuarioLogueado');

				if(($usuario['nombreTipoUsuario']!='CAS' && $usuario['nombreTipoUsuario']!='CAS_ROS') && !Session::has('tramiteNuevoPermiso')){
					$permiso = Session::get('usuarioTramite')['permiso'];
					$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 12);
				}else if(\Session::has('tramiteNuevoPermiso')){
					$permiso=Session::get('tramiteNuevoPermiso')['nro_permiso'];
					$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 12);
				}else{
					$margen=$this->servicioHistorico->fechaUltimoTramite($usuario['permiso'],12);
				}

				$bloqIngreso = 0;

				if($usuario['nombreTipoUsuario']=='Agente')
					$bloqIngreso = 1;

				$meses=$margen['meses'];
				$dias=$margen['dias'];
				$paso_fecha=$margen['paso_fecha'];
				$cant = $margen['cant'];
				Session::put('ultimoTramite',$margen);

				return View::make('tramites.fallecimientoTitular', array('titulo'=>$titulo,'tipo'=>$tipo,'tipo_persona'=>$tipo_persona,'meses'=>$meses, 'dias'=>$dias, 'paso_fecha'=>$paso_fecha, 'cant'=>$cant,'tipo_sociedad'=>$tipo_sociedad, 'tipo_situacion'=>$tipo_situacion, 'tipo_doc'=>$tipo_documento, 'tipo_ocup_tit'=>$tipo_ocup_titu,'tipo_tipo_rel'=>$tipo_tipo_relacion,'tipo_tipo_vin'=>$tipo_tipo_vinculo,'bloqIngreso'=>$bloqIngreso,'localidad_actual_f'=>$localidad_actual_f,'localidad_actual_id_f'=>$localidad_actual_id_f,'codigo_postal_f'=>$codigo_postal_f,'subcodigo_postal_f'=>$subcodigo_postal_f,'domicilio_agencia_f'=>$domicilio_agencia_f,'listaDocumentos'=>$listaDocumentos));

			}

			\Log::info('AE - fallecimientoTitular - count != 0: ');
			$usuario=Session::get('usuarioLogueado');

			if($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS'){
				$permiso = Session::get('usuarioTramite')['permiso'];
				$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 12);
			}else{
				$margen=$this->servicioHistorico->fechaUltimoTramite($usuario['permiso'], 12);
			}

			$meses=$margen['meses'];
			$dias=$margen['dias'];
			$paso_fecha=$margen['paso_fecha'];
			$cant = $margen['cant'];

			Session::put('ultimoTramite',$margen);
			return View::make('tramites.fallecimientoTitular',array('titulo'=>$titulo,'tipo'=>$tipo,'tipo_persona'=>$tipo_persona,'meses'=>$meses, 'dias'=>$dias, 'paso_fecha'=>$paso_fecha, 'cant'=>$cant,'tipo_sociedad'=>$tipo_sociedad, 'tipo_situacion'=>$tipo_situacion, 'tipo_doc'=>$tipo_documento, 'tipo_ocup_tit'=>$tipo_ocup_titu,'tipo_tipo_rel'=>$tipo_tipo_relacion,'tipo_tipo_vin'=>$tipo_tipo_vinculo,'bloqIngreso'=>$bloqIngreso,'localidad_actual_f'=>$localidad_actual_f,'localidad_actual_id_f'=>$localidad_actual_id_f,'codigo_postal_f'=>$codigo_postal_f,'subcodigo_postal_f'=>$subcodigo_postal_f,'domicilio_agencia_f'=>$domicilio_agencia_f,'listaDocumentos'=>$listaDocumentos));

		}


		/********************************************/
		/* ingreso de fallecimiento titular  		*/
		/*	MM-09/08/2021			                */
		/********************************************/
		public function fallecimientoTitular_add(){
			$datos = Input::all();

			$reglas=array(
				'agente'=>'required|integer',
				'subagente'=>'required|integer',
				'permiso'=>'required|integer',
				'departamento_id'=>'required|integer',
				'localidad_actual_id'=>'required|integer',
				'domicilio'=>'required',
				// 'domicilio_comercial'=>'required',
				//'email'=>'required|email',

				'referente'=>'required',
				'datos_contacto'=>'required',
				'nueva_localidad'=>'required|integer',
				'motivo_ct'=>'required'
			);

			\Log::info('AE - fallecimientoTitular_add - reglas: ',array($reglas));

			if($datos['tipo_persona']=="J"){
				$reglas['tipo_persona']='required|in:F,J|TipoPersona:'.$datos['cuit'].',J';
				$reglas['cuit']='required|Cuit';
				$reglas['tipo_sociedad']='required';
				$reglas['razon_social']='required';
			}else{//persona física
				$reglas['sexo_persona']='required|in:F,M,S';//S=Sin especificar
				if($datos['cuit']!=''){
					$reglas['tipo_persona']='required|in:F,J|TipoPersona:'.$datos['cuit'].','.$datos['sexo_persona'];
				}else{
					$reglas['tipo_persona']='required|in:F,J';
				}

				$reglas['apellido_nombre']='required';
				$reglas['tipo_doc']='required';
				$reglas['nro_doc']='required|Documento:'.$datos['nro_doc'];
				//$reglas['fecha_nac']='required|FechaNacimiento:'.$datos['fecha_nac'];
			}
			//CBU
			/*
				if($datos['subagente']==0){//pasa a ser titular de una red
				$reglas['cbu']='max:4';
			}*/

			//IIBB
			//$reglas['ingresos']='IngresosBrutos:'.$datos['ingresos'];

			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"integer"=>"Solo nº"
			);
			$validar=Validator::make($datos,$reglas,$mensaje);

			if($validar->fails()){
				\Log::info($validar->messages());
				return Redirect::to('cambio-titular')->withErrors($validar)->withInput();
			} else{
				$localidad = $this->controladorLocalidad->buscarPorIdLocalidad($datos['nueva_localidad']);
				if(is_null($localidad)){
					return Redirect::to('cambio-titular')->withErrors($validar)->withInput();
				}
				$departamento = $this->controladorLocalidad->buscarDepartamentoLocalidad($datos['nueva_localidad']);
				if(is_null($departamento)){
					\Log::error('departamento inexistente.');
					return Redirect::to('cambio-titular')->withErrors($validar)->withInput();
				}
			}

				$datos['id_nueva_localidad']=$localidad['id'];
				$datos['cp_scp']=$localidad['codigo_postal'].'-'.$localidad['subcodigo_postal'];
				$datos['nombre_nueva_localidad']=$localidad['nombre'];

			$datos['departamento_id'] = $departamento['id'];
			$datos['nuevo_departamento'] = $departamento['nombre'];

			$datosT = $datos;
			$tipoTramite = 12;
			$datos['tipo_tramite'] = $tipoTramite;
			$datosT['tipo_tramite'] = $tipoTramite;
			\Log::info('MM - TramiteController - datosT: ', array($datosT));

			// if($datos['continuar']){
				// Session::put('domicilio_cargado',$datos);
				// Session::put('titular_cargado',$datosT);
				// \Log::info('MM - TramiteController - llego a enviar plan: ');
				// return Redirect::to('plan-estrategico');
			// }else{
				unset($datos_email);
				$datos_email=array();
				$mensaje = $this->servicioTramites->cargarTitular($datos, $datos_email, $tipoTramite);

				if(is_null($mensaje)){
					return Response::json(array('mensaje'=>"Falló el incio del trámite Cambio de Titular."));
				}

				Session::flash('mensaje', $mensaje);

				if(Session::has('usuarioTramite'))
					$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
				else
					$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];
					Session::put('datos_email',$datos_email);

					Session::forget('permisoPlan');
					Session::forget('domicilio_cargado');
					Session::forget('titular_cargado');
					Session::forget('usuarioTramite');
					Session::forget('tramiteGuia');
				return Redirect::to('exito-tramite');
		//	}
		}

		public function buscarPersona(){
			$dni_cuit=Input::get('dni_cuit');
			$tipo_doc = Input::get('tipo_doc');

			if($tipo_doc == 4){
				$cuit=str_replace('-', '', $dni_cuit);
				$dni = 0;
			}else{
				$cuit = 0;
				$dni = str_replace('.', '', $dni_cuit);
			}

			$persona = $this->servicioTramites->buscarPersona($dni,$cuit,$tipo_doc);

			if(is_null($persona)){
				return Response::json(array('exito'=>0,'persona'=>$persona));
			}else{
				return Response::json(array('exito'=>1,'persona'=>$persona));
			}
		}

		/**
		* Función para validar desde la vista el campo ingresos brutos
		**/
/*
		public function validarIngresos(){
			$datos['ingresos']=Input::get('ingresos');
			//IIBB
			$reglas['ingresos']='required|IngresosBrutos:'.$datos['ingresos'];

			$mensaje=array("required"=>"Campo requerido");

			$validar=Validator::make($datos,$reglas,$mensaje);

			if($validar->fails()){
				return Response::json(array('exito'=>false, 'mensaje'=>'Nº incorrecto'));
			}else{
				return Response::json(array('exito'=>true));
			}
		}
*/
/********************CAMBIO DE DEPENDENCIA*********************/

		/********************************************/
		/*	inicio cambio de dependencia	    	*/
		/********************************************/
		public function cambioDependencia(){

			\Log::info('MM - TramiteController - llego a - cambioDependencia');
			Session::forget('ultimoTramite');

			$datos['tipo_tramite']=4;
			self::datosComunesVistas($datos);
			Session::flashInput($datos);

			$alta=0;
			if(Session::has('tramiteNuevoPermiso')){
				$alta=1;
			}
			$titulo=$this->servicioTipoTramites->tituloTramite(4,$alta);

			$usuario=Session::get('usuarioLogueado');
			if($usuario['nombreTipoUsuario']!='CAS' && $usuario['nombreTipoUsuario']!='CAS_ROS' && Session::has('tramiteNuevoPermiso')){
				$permiso = Session::get('usuarioTramite')['permiso'];
				$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 4);
			}else if(Session::has('tramiteNuevoPermiso')){
				$permiso=Session::get('tramiteNuevoPermiso')['nro_permiso'];
				$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 4);
			}else{
				$margen=$this->servicioHistorico->fechaUltimoTramite($usuario['permiso'], 4);
			}

			$bloqIngreso = 0;

			if($usuario['nombreTipoUsuario']=='Agente')
				$bloqIngreso = 1;

			$meses=$margen['meses'];
			$dias=$margen['dias'];
			$paso_fecha=$margen['paso_fecha'];
			$cant = $margen['cant'];
			
			//  tipos de documento
			$tiposDocumentos = $this->servicioTipoDocumento->buscarTodos(); 
			// \Log::info('MM - cambioDomicilio - tiposDocumentos: ',array($tiposDocumentos));
			// $lista=$this->servicioTramites->listaMotivosBaja();
			$listaDocumentos=array();
			$listaDocumentos[0]='Ingrese Tipo Documento'; // Valor 0 - por defecto y validad en js para que si o si seleccione el tipo de documento
			foreach($tiposDocumentos as $valor){
				$listaDocumentos[$valor['id']]=$valor['nombre'];
			}

			Session::put('ultimoTramite',$margen);

			return View::make('tramites.cambioDependencia',array('titulo'=>$titulo,'meses'=>$meses, 'dias'=>$dias, 'paso_fecha'=>$paso_fecha, 'cant'=>$cant, 'bloqIngreso'=>$bloqIngreso,'tipo_documento'=>$listaDocumentos));
		}


		/****************************/
		/* ingreso de dependencia  	*/
		/****************************/
		public function cambioDependencia_add(){
			$datos = Input::all();

			$reglas=array(
				'nro_red'=>'required|integer',
				'nro_subagente'=>'required|integer',
				'nueva_localidad'=>'required|integer',
				'motivo_cr'=>'required'
			);

			$mensaje=array(
				"required"=>"Campo requerido",
				"integer"=>"Solo nº"
			);

			$validar=Validator::make($datos,$reglas,$mensaje);

			if($validar->fails()){
				\Log::info($validar->messages());
				return Redirect::to('cambio-dependencia')->withErrors($validar)->withInput();
			}else{
				$localidad = $this->controladorLocalidad->buscarPorIdLocalidad($datos['nueva_localidad']);
				if(is_null($localidad)){
					return Redirect::to('cambio-dependencia')->withErrors($validar)->withInput();
				}
				$departamento = $this->controladorLocalidad->buscarDepartamentoLocalidad($datos['nueva_localidad']);
				if(is_null($departamento)){
					\Log::error('departamento inexistente.');
					return Redirect::to('cambio-dependencia')->withErrors($validar)->withInput();
				}
			}

				$datos['id_nueva_localidad']=$localidad['id'];
				$datos['cp_scp']=$localidad['codigo_postal'].'-'.$localidad['subcodigo_postal'];
				$datos['nombre_nueva_localidad']=$localidad['nombre'];

			$datos['departamento_id'] = $departamento['id'];
			$datos['nuevo_departamento'] = $departamento['nombre'];
			$tipoTramite = 4;
			$datos['tipo_tramite'] = $tipoTramite;

			\Log::info('MM - cambioDependencia_add - datos',array($datos));

			if($datos['continuar']){
				Session::put('domicilio_cargado',$datos);
			//	Session::put('titular_cargado',$datosT);
				\Log::info('MM - TramiteController - cambioDependencia_add - llego a enviar plan: ');
				return Redirect::to('plan-estrategico');
			}else{
				unset($datos_email);
				$datos_email=array();
				$mensaje = $this->servicioTramites->cargarDependencia($datos, $datos_email);
				if(is_null($mensaje)){
					return Response::json(array('mensaje'=>"Falló el incio del trámite Cambio de Titular."));
				}

				Session::flash('mensaje', $mensaje);

				if(Session::has('usuarioTramite') && !Session::has('tramiteNuevoPermiso'))
					$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
				else if(Session::has('tramiteNuevoPermiso'))
					$datos_email['email_duenio'] = 'emailvacio@gimeil.com';
				else
					$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];

				Session::put('datos_email',$datos_email);

				Session::forget('usuarioTramite');
				Session::forget('tramiteGuia');
				return Redirect::to('exito-tramite');
			}
	}
	/*
		public function cambioDependencia_add(){

			$datos = Input::all();

			unset($datos_email);
			$datos_email=array();
			$mensaje = $this->servicioTramites->cargarDependencia($datos, $datos_email);
			Session::flash('mensaje', $mensaje);
			if(Session::has('usuarioTramite') && !Session::has('tramiteNuevoPermiso'))
				$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
			else if(Session::has('tramiteNuevoPermiso'))
				$datos_email['email_duenio'] = 'emailvacio@gimeil.com';
			else
				$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];

			Session::put('datos_email',$datos_email);

			Session::forget('usuarioTramite');
			Session::forget('tramiteGuia');
			return Redirect::to('exito-tramite');
		}
	*/
		/***********************************/
		/*	inicio rechazar trámite        */
		/***********************************/
		public function rechazarTramite(){
			$opcion['nopcion']=2;
			Session::flashInput($opcion);
			$accion="rechazar";
			$tituloTramite="Rechazar Trámite";
			return View::make('tramites.rechazarTramite');
		}

		/*****************************************************/
		/* Función que verifica que el permiso ingresado     */
		/* existe - Usada en: Nuevo Trámite - Cancelar       */
		/*****************************************************/
		public function consultaPermiso(){
			Session::forget('usuarioTramite');
			Session::forget('tramiteGuia');
			$usr = Session::get('usuarioLogueado');
			$permiso=Input::get('permiso');
			\Log::info('MM - TramiteController - consultaPermiso - permiso: ', array($permiso));
			$usuario=$this->controladorUsuario->buscarPorPermiso($permiso);
			\Log::info('MM - TramiteController - consultaPermiso - buscarPorPermiso-usuario: ', array($usuario));
			$respuesta = Response::json(array('usuario'=>$usuario));
			Session::put('usuarioTramite',$usuario);
			// puede que falte cargar
            return $respuesta;
		}

/*************************************Métodos para consulta de tramites*****************************************/

		/*********************************/
		/*	Consultar tramite          	 */
		/*	Arma los datos para la vista */
		/*********************************/
		public function consultarTramite(){

			$url = $_SERVER['HTTP_REFERER'];

			if((strpos($url, "evaluacion-domicilio")==false && strpos($url, "modificar-tramite")==false) ){
				Session::forget("abrirmodal");
				Session::forget("abrir");
			}

			$usuario=Session::get('usuarioLogueado');
			//por ahora busco todos
			$estados=$this->servicioTramites->buscarTodosLosEstados();
			foreach ($estados as $estado) {
				//seteo los que necesito
				$combobox[$estado['id_estado_tramite']]=$estado['descripcion_tramite'];
			}
			//array_push($combobox, "Aprobados Pendientes Informar");
			array_push($combobox, 'Todos');

			$listaTiposTramites = $this->servicioTramites->buscarTodosLosTipos(array(0));

			foreach ($listaTiposTramites as $tipoTramite) {
				if($tipoTramite['id_tipo_tramite']!=5)
					$tiposTramites[$tipoTramite['id_tipo_tramite']]=$tipoTramite['nombre_tramite'];
			}
			array_push($tiposTramites, "Nuevos Permisos");
			array_push($tiposTramites, "Todos");

			//la 1º vez va a llamar a la 1º página
			$nro_pagina = 1;
			//nro de tramites por página
			$paginacion['5']="5";
			$paginacion['15']="15";
			$paginacion['25']="25";
			$paginacion['50']="50";

			$ordenamiento['0']="Estado – Fecha";
			$ordenamiento['1']="Fecha – Estado";

			$mesa_entrada['0']="";
			$mesa_entrada['1']="Santa Fe";
			$mesa_entrada['2']="Rosario";

			if(Session::has('ddv_nro')){
				$ddv_nro = Session::get('ddv_nro');
			}else{
				$ddv_nro = array('ddv_nro'=>0);
			}

			Session::flashInput($ddv_nro);

			$permiso_mq=in_array('Administracion_Maquinas',$usuario['listaFunciones']);
			
			//  tipos de documento
				$tiposDocumentos = $this->servicioTipoDocumento->buscarTodos(); 
				$listaDocumentos=array();
				$listaDocumentos[0]='Ingrese Tipo Documento'; // Valor 0 - por defecto y validad en js para que si o si seleccione el tipo de documento
				foreach($tiposDocumentos as $valor){
					$listaDocumentos[$valor['id']]=$valor['nombre'];
				}

			return View::make('tramites.consultarTramite', 
				array(	"combobox" => $combobox, 
						"tramitesbox"=>$tiposTramites,
						'nro_pagina'=>$nro_pagina, 
						'paginacion'=>$paginacion, 
						'ordenamiento'=>$ordenamiento, 
						'mesa_entrada'=>$mesa_entrada,
						'permiso_mq'=>$permiso_mq,
						'tipo_documento'=>$listaDocumentos)
			);
		}

		/**********************************/
		/*	Devuelve la lista de tramites */
		/*	encontrada según los filtros  */
		/* de búsqueda seleccionados.     */
		/**********************************/
		public function consultarTramite_add(){
			$datos_vista = Input::all();
				\Log::info("consultarTramite_add - datos_vista: ", array($datos_vista));
			//reglas de validación
			$reglas=array(
				'permiso'=> 'numeric|Min:4',
                'agente'=> 'numeric|Min:4',
                'subagente'=> 'numeric|Min:0'
			);
			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"digits_between"=>"Máximo de dígitos superado",
				"Integer"=>"Solo números"
			);

			$validar=Validator::make($datos_vista,$reglas,$mensaje);

			if($validar->fails()){

				$pagina = 0;
			    $cantidadPaginas=0;
			    $mensaje="Filtros de búsqueda incorrectos.";
			    $listaTramites = array();
	            $respuesta = Response::json(array('tramites'=>$listaTramites, 'mensaje'=>$mensaje,'pagina'=>$pagina, 'cantidadPaginas'=>$cantidadPaginas));
	            Session::forget('ddv_nro');
	            return $respuesta;
				//return Redirect::back()->withErrors($validar);
		 	}else {
		 		//borra los datos de la consulta anterior
		 		Session::forget('datosConsulta');
		 		Session::forget("datosVistaConsulta");

		 		//si presionó algún btn de las flechas o si cargó la página con nº
				if($datos_vista['press-btn']==1){
		        	$datos['pagina'] = Input::get('nro_pagina', '1');
		        }else{
		        	$datos['pagina'] = '1';
		        }
		        $datos['porPagina'] = Input::get('paginar', '5');

		        $datos['campoOrden']= Input::get('ordenar'); //actual: 0: Estado-Fecha 1: Fecha-Estado

		        $datos['tipoOrden'] = 'ASC';

		        if((strcasecmp($datos_vista['htramitest'],"todos")!=0) && (strcasecmp($datos_vista['htramitest'],"")!=0)){
			        $datos['tipoTramite'] = Input::get('htramitesv','1,2,3,4,6,7,8,9,10,11,12,13,14');
		        }else{
			        $datos['tipoTramite'] ='1,2,3,4,6,7,8,9,10,11,12,13,14';
		        }

				if(strcasecmp($datos_vista['htramitest'],"Nuevos Permisos")!=0){
					$datos['nuevoPermiso']=0;
				}else{
					$datos['nuevoPermiso']=1;
				}

				$datos["permiso"]=$datos_vista["permiso"];

		        if((strcasecmp($datos_vista['hestadost'],"todos")!=0) && (strcasecmp($datos_vista['hestadost'],"Aprobados Pendientes Informar")!=0) && (strcasecmp($datos_vista['hestadost'],"")!=0)){
		        	$datos['estados'] = Input::get('hestadosv','0,1,2,3,4,5,6,7,8,9,10,11');
		        }else if((strcasecmp($datos_vista['hestadost'],"Aprobados Pendientes Informar")==0)){
		        	$datos['estados'] = '7';
		        }else{
		        	$datos['estados'] ='0,1,2,3,4,5,6,7,8,9,10,11';
		        }

		        $datos['fechaDesde'] =$this->formatos->fecha(Input::get('fecha_desde'));
		        $datos['fechaHasta'] =$this->formatos->fecha(Input::get('fecha_hasta'));
		        $usuario=Session::get('usuarioLogueado');
				\Log::info("consultarTramite_add - usuario: ", array($usuario));
		        if($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS'){
		        	$datos['permiso'] = Input::get('permiso','');
		        	$datos['agente'] = Input::get('agente','');
		        	$datos['localidad'] = Input::get('localidad','');

		        	$sub=Input::get('subagente','');

		        	if($sub!=''){
			        	switch (strlen($sub)){
	        				case 1:
	        					$datos['subAgente'] =sprintf("%02s", $sub);
	        					break;
							case 2:
							 	$datos['subAgente'] =sprintf("%01s", $sub);
							 	break;
							default:
								$datos['subAgente']=$sub;
	        			}
		        	}else{
		        		$datos['subAgente'] = $sub;
		        	}
		        }

		        //de qué link viene (si es !=0)
		        $datos['ddv_nro']=$datos_vista['ddv_nro'];

		        if(array_key_exists ('combo_mesa_entrada', $datos_vista)){
		        	$datos['mesa_entrada']=$datos_vista['combo_mesa_entrada'];//0-nada 1-stafe 2-rosario
		        }else{
		        	$datos['mesa_entrada']=0;
		        }

		        //guardo los datos en ésta variable para luego usarla con el csv
				Session::put("datosConsulta",$datos);//son los datos de la vista + otros que se completaron
				Session::put("datosVistaConsulta",$datos_vista);

		        // Obtiene los tramites con los filtros
		        $resultado = $this->servicioTramites->listar($datos);

		       	//coloco los datos para que se vuelvan a mostrar en la vista
				Session::flashInput($datos_vista);

		        $listaTramites = $resultado['tramites'];

				if(sizeof($listaTramites)==0){
					$pagina = $resultado['pagina'];
			        $cantidadPaginas=$resultado['cantidadPaginas'];
			      	$mensaje="No existen trámites que cumplan las condiciones de búsqueda.";

		            $respuesta = Response::json(array('tramites'=>$listaTramites, 'mensaje'=>$mensaje,'pagina'=>$pagina, 'cantidadPaginas'=>$cantidadPaginas));
		            Session::forget('ddv_nro');
		            return $respuesta;
				}
		        $pagina = $resultado['pagina'];
		        $cantidadPaginas=$resultado['cantidadPaginas'];
		       	$mensaje ="ok" ;


	            $respuesta = Response::json(array('tramites'=>$listaTramites, 'mensaje'=>$mensaje, 'pagina'=>$pagina, 'cantidadPaginas'=>$cantidadPaginas));
				Session::forget('ddv_nro');
	            return $respuesta;
			}
		}

		/*********************************/
		/*	Consultar tramite  			*/
		/*	EM-09/05/2014				*/
		/********************************/
		public function consultarPorNroTramite(){
			$datos_session = Session::get('usuarioLogueado');
			Session::forget("abrir");
			$files = glob($_SERVER['DOCUMENT_ROOT'].'/app/storage/views/*'); // get all file names
			//	\Log::info('MM - consultarPorNroTramite files: ', array($files));

				foreach($files as $file){ // iterate files
					  if(is_file($file)) {
						unlink($file); // delete file
					  }
					}

			\Log::info('MM - TRAMITES - usuario logeado - datos_session:',array($datos_session));
				return View::make('tramites.consultaNroTramite');
		}

		/*********************************/
		/*	tramite consultado 			*/
		/*	EM-09/05/2014				*/
		/********************************/
		public function consultarPorNroTramite_add(){


			$usr = Session::get('usuarioLogueado');
			Session::forget('usuarioTramitePermiso');

			$files = glob($_SERVER['DOCUMENT_ROOT'].'/app/storage/views/*'); // get all file names
			//	\Log::info('MM - consultarPorNroTramite_add ruta: ', array($_SERVER['DOCUMENT_ROOT'].'/app/storage/views/*'));
				// \Log::info('MM - consultarPorNroTramite_add files: ', array($files));

				foreach($files as $file){ // iterate files
					  if(is_file($file)) {
						unlink($file); // delete file
					  }
					}

			$id=Input::get('ntramite');

			\Log::info('MM - consultarPorNroTramite_add id: ', array($id));
			$tramite=$this->servicioTramites->buscarPorId($id);
			\Log::info('MM - consultarPorNroTramite_add tramite: ', array($tramite));

			// Agregado ADEN - 2024-04-08
			Session::forget('usuarioTramiteSinAdjunto'); // Desactivo la señal de apagar adjuntos!

			if(is_null($tramite)){
				$mensaje = "No tiene un trámite registrado con ese nº de seguimiento.";
				$respuesta = Response::json(array('mensaje'=>$mensaje));
	            return $respuesta;
	            //Session::flash('mensaje', $mensaje);
				//return Redirect::back();
			}else if($tramite['nro_permiso']!=$usr['permiso'] && $usr['nombreTipoUsuario']!="CAS" && $usr['nombreTipoUsuario']!='CAS_ROS'){
				$mensaje = "No tiene un trámite registrado con ese nº de seguimiento.";
				$respuesta = Response::json(array('mensaje'=>$mensaje));
	            return $respuesta;
				/*Session::flash('mensaje', $mensaje);
				return Redirect::back();*/
			}else{

				// Agregado ADEN - 2024-04-08 - si tipo de usuario es AGENTE o SUBAGENTE, y el estado no es "aprobado", apago los adjuntos!
				if (($usr['nombreTipoUsuario']=="AGENTE" 
					|| $usr['nombreTipoUsuario']!='SUBAGENTE')
					&& ($tramite['id_estado_tramite'] != 2
						&& $tramite['id_estado_tramite'] != 5
						&& $tramite['id_estado_tramite'] != 6)) {
					Session::put('usuarioTramiteSinAdjunto',$tramite['id_estado_tramite']);
					}

				Session::put('usuarioTramitePermiso',$tramite['nro_permiso']);
				$respuesta = Response::json(array('mensaje'=>null));
	            return $respuesta;
			}
		}

		/**
		* Función que muestra el trámite consultado
		**/
		public function consultarPorNroTramite_show($id){
			$tramite=$this->servicioTramites->buscarPorId($id);
			$datos=$tramite['nro_tramite']."&".$tramite['id_tipo_tramite']."&1";
			return Redirect::to('detalle-tramite-nro-seguimiento/'.$datos);
		}

		/****************************/
		/*      detalleTramite      */
		/****************************/
		private function detalleTramite($id){
			\Log::info("MM - MM - detalleTramite");
			$usuarioLogueado = Session::get('usuarioLogueado');
			$tramite = $this->servicioTramites->buscarPorId($id);
			\Log::info("MM - MM - detalleTramite - tramite: ",array($tramite));

			$datosPermiso=$this->servicioTramites->datosPermisoSuitecrm($tramite['nro_permiso'], $tramite['id_tipo_tramite']);
			$tramite['razon_social_permiso']=$datosPermiso['razon_social_permiso'];
			$tramite['red']=$datosPermiso['red'];
			$tramite['nombre_red']=$datosPermiso['nombre_red'];
			$tramite['planCompleto']=0;
			//trámite tipo categoria - subagente->agente
			if($tramite['id_tipo_tramite']==2){
				$tramite['suba_a_ag']=$this->servicioTramites->esNuevoAgente($tramite['nro_tramite']);
				$categoria =$this->servicioTramites->obtenerNuevaCategoria($tramite['nro_tramite']);
				$tramite['cbu']=$categoria['cbu'];// $this->servicioTramites->buscarCategoriaCBU($tramite['nro_tramite']);
				$tramite['nro_red']=$categoria['nro_red_nueva'];
				$tramite['nro_red_nueva']=$categoria['nro_red_nueva'];
				$tramite['nro_nuevo_sbag'] = $categoria['nro_pto_vta_nuevo'];
				$tramite['categoria_nueva'] = $categoria['categoria_nueva'];
				$datosPermiso['cp_localidad'] =explode("-", $categoria['cp_scp_localidad'])[0];

			}
			if($tramite['id_tipo_tramite']==3 || $tramite['id_tipo_tramite']==8 || $tramite['id_tipo_tramite']==12 ){//cambio de titular - cotitular
			// puede que para tramite otorga por fallecimiento debemos obtener los datos de otra forma...

				$tramite['persona_nt']=$this->servicioTramites->obtenerNuevoTitular($tramite['nro_tramite']);
				$tramite['tieneTramitePI']=$this->servicioTramites->tieneTramitePI($tramite['nro_tramite'], $tramite['nro_permiso']);
				// se trae relación y vínculo
				$tramite['tipo_relyvin']=$this->servicioTramites->obtenerRelyVin($tramite['nro_tramite']);
				\Log::info('MM - tramite - persona_nt:',array($tramite['persona_nt']));
				\Log::info('MM - tramite - persona_nt tipo_relacion:',array($tramite['tipo_relyvin']['tipo_relacion']));
				\Log::info('MM - tramite - persona_nt tipo_vinculo:',array($tramite['tipo_relyvin']['tipo_vinculo']));
				
				$tramite['persona_nt']['tipo_relacion']=$tramite['tipo_relyvin']['tipo_relacion'];
				$tramite['persona_nt']['tipo_vinculo']=$tramite['tipo_relyvin']['tipo_vinculo'];
			}
			if($tramite['id_tipo_tramite'] == 11){ //baja de cotitular
				$tramite['cotitular'] = $this->servicioTramites->obtenerCotitularTramite($tramite['nro_tramite']);
				\Log::info('AE - tramite - cotitular:',array($tramite['cotitular']));
			}
			if($tramite['id_tipo_tramite']==1){//cambio de domicilio
				$domicilio =$this->servicioTramites->obtenerNuevoDomicilio($tramite['nro_tramite']);
				$localidad =$this->controladorLocalidad->buscarPorIdLocalidad($domicilio['id_localidad_nueva']);
				$departamento=$this->controladorLocalidad->buscarDepartamentoLocalidad($localidad['id']);
				$tramite['nuevo_domicilio']=trim($domicilio['direccion_nueva']).', Localidad: '.trim($localidad['nombre']).', Departamento: '.trim($departamento['nombre']);
				$tramite['nueva_direccion']=trim($domicilio['direccion_nueva']);
				$tramite['referente'] = $domicilio['referente'];
				$tramite['datos_contacto'] = $domicilio['datos_contacto'];
				$tramite['planCompleto'] = $this->servicioTramites->planCompleto($tramite['nro_tramite']);
			}
			if($tramite['id_tipo_tramite']==4){//cambio de dependencia
				$dependencia = $this->servicioTramites->obtenerDependencia($tramite['nro_tramite']);
					$tramite['nueva_red_dependencia']=$dependencia['nro_red_actual'].'/'.$dependencia['nro_pto_vta_nuevo']." - ".trim($dependencia['razonSocial']);
				$tramite['nro_red']=$dependencia['nro_red_actual'];
					$tramite['nro_nuevo_sbag']= $dependencia['nro_pto_vta_nuevo'];
				}

			$margen=$this->servicioHistorico->fechaUltimoTramite($tramite['nro_permiso'], $tramite['id_tipo_tramite']);
			$meses=$margen['meses'];
			$dias=$margen['dias'];
			$paso_fecha=$margen['paso_fecha'];
			$cant = $margen['cant'];
			$combobox=[];

			$estadosPosibles = $this->servicioTramites->estadosPosibles($tramite['id_estado_tramite'], $usuarioLogueado, $tramite['id_tipo_tramite']);
			\Log::info('WB - estadosPosibles', $estadosPosibles);

			foreach ($estadosPosibles as $estado)
				$combobox[$estado['id_estado_tramite']]=$estado['descripcion_tramite'];

			$alta=0;
			if($tramite['nuevo_permiso']){
				$alta=1;
			}

			$titulo=$this->servicioTipoTramites->tituloTramite($tramite['id_tipo_tramite'],$alta);

			$titulo=str_replace('Comercial', '', $titulo);
			$tramite['titulo']=$titulo;
			$this->url_caratula($tramite['nro_tramite']);
			$this->url_nota($tramite['nro_tramite']);

			$puedeModificar = $this->puedeModificar($usuarioLogueado['nombreTipoUsuario'], $tramite);

			$resultado=array(
				'tramite'=>$tramite,
				'estados'=>$combobox,
				'meses'=>$meses,
				'dias'=>$dias,
				'paso_fecha'=>$paso_fecha,
				'cant'=>$cant,
				'datosPermiso'=>$datosPermiso,
				'puedeModificar' => $puedeModificar);
			return $resultado;
		}

		/************************************************/
		/* Funciones que llaman a la que trae los datos */
		/* para mostrar el detalle del trámite.         */
		/************************************************/
		public function detallePorNroSeguimiento(){
			try{
				\Log::info('MM - detallePorNroSeguimiento sesion abrir', array(Session::get("abrir")));
				if(Session::has("abrir")){

					$idTramite = Input::get('nroTramite');
					if(is_null($idTramite)){
								$idTramite = Session::get("abrir");
					}
					\Log::info('MM - detallePorNroSeguimiento 2501 - idTramite: ', array($idTramite));
				}else{

				    $idTramite = Input::get('nroTramite');
					\Log::info('MM - detallePorNroSeguimiento 2505 - idTramite: ', array($idTramite));
				}

				$usr = Session::get('usuarioLogueado');
				\Log::info('AE - detallePorNroSeguimiento 2771 - usr: ', array($usr));


				$resultado = $this->detalleTramite($idTramite);
				\Log::info('AE - detallePorNroSeguimiento 2771 - resultado: ', array($resultado));
				$tramite = $resultado['tramite'];
				\Log::info('AE - detallePorNroSeguimiento 2771 - tramite: ', array($tramite));

				$usu=Session::get('usuarioLogueado.nombreUsuario');
				$tipo_sociedad = $this->servicioTramites->listaTiposSociedad();
				$tipo_persona = $this->servicioTramites->listaTiposPersonas();
				$tipo_situacion = $this->servicioTramites->listaSituacionGanancias();
				
				// ADEN - ESTO HAY QUE CAMBIAR!!!

				// Agregado ADEN - 2024-04-24
				Session::forget('usuarioTramiteSinAdjunto'); // Desactivo la señal de apagar adjuntos!

				if(is_null($tramite)){
					$mensaje = "No tiene un trámite registrado con ese nº de seguimiento.";
					$respuesta = Response::json(array('mensaje'=>$mensaje));
					return $respuesta;
					//Session::flash('mensaje', $mensaje);
					//return Redirect::back();
				}else if($tramite['nro_permiso']!=$usr['permiso'] && $usr['nombreTipoUsuario']!="CAS" && $usr['nombreTipoUsuario']!='CAS_ROS'){
					$mensaje = "No tiene un trámite registrado con ese nº de seguimiento.";
					$respuesta = Response::json(array('mensaje'=>$mensaje));
					return $respuesta;
					}else{

						// Agregado ADEN - 2024-04-08 - si tipo de usuario es AGENTE o SUBAGENTE, y el estado no es "aprobado", apago los adjuntos!
						if (($usr['nombreTipoUsuario']=="AGENTE" 
							|| $usr['nombreTipoUsuario']!='SUBAGENTE')
							&& ($tramite['id_estado_tramite'] != 2
								&& $tramite['id_estado_tramite'] != 5
								&& $tramite['id_estado_tramite'] != 6)) {
							Session::put('usuarioTramiteSinAdjunto',$tramite['id_estado_tramite']);
							}

						Session::put('usuarioTramitePermiso',$tramite['nro_permiso']);
						$respuesta = Response::json(array('mensaje'=>null));
						// return $respuesta;
					}
				
				//  tipos de documento
				$tiposDocumentos = $this->servicioTipoDocumento->buscarTodos(); 
				$listaDocumentos=array();
				$listaDocumentos[0]='Ingrese Tipo Documento'; // Valor 0 - por defecto y validad en js para que si o si seleccione el tipo de documento
				foreach($tiposDocumentos as $valor){
					$listaDocumentos[$valor['id']]=$valor['nombre'];
				}

//				$tipoUsuario = Session::get('usuarioLogueado.nombreTipoUsuario');
				$tipoUsuario = $usr['nombreTipoUsuario'];

				return View::make(
					'tramites.detalleTramite',
					array(
						'tramite' => $resultado['tramite'],
						'estados'=>$resultado['estados'],
						'meses'=>$resultado['meses'],
						'dias'=>$resultado['dias'],
						'paso_fecha'=>$resultado['paso_fecha'],
						'cant'=>$resultado['cant'],
						'datosPermiso'=>$resultado['datosPermiso'],
						'usu'=>$usu,
						'tipo_sociedad'=>$tipo_sociedad,
						'tipo_persona'=>$tipo_persona,
						'tipo_situacion'=>$tipo_situacion,
						'puedeModificar' => $resultado['puedeModificar'],
						'tipo_documento'=>$listaDocumentos
					));
			}catch(Exception $e){
				\Log::error($e);
				\Log::error('Problema buscar el detalle del trámite por nro de seguimiento');
				return Redirect::back();
			}

		}


		public function detalleModal($id){
			try{
				Session::forget('usuarioTramitePermiso');
				$resultado = $this->detalleTramite($id);
				\Log::info("AE - tramitesController - detalleModal - resultado: ",array($resultado));
				Session::put('usuarioTramitePermiso',$resultado['tramite']['nro_permiso']);
				$usu=Session::get('usuarioLogueado.nombreUsuario');
				$tipo_sociedad = $this->servicioTramites->listaTiposSociedad();
				$tipo_persona = $this->servicioTramites->listaTiposPersonas();
				$tipo_situacion = $this->servicioTramites->listaSituacionGanancias();

				//  tipos de documento
				$tiposDocumentos = $this->servicioTipoDocumento->buscarTodos(); 
				$listaDocumentos=array();
				$listaDocumentos[0]='Ingrese Tipo Documento'; // Valor 0 - por defecto y validad en js para que si o si seleccione el tipo de documento
				foreach($tiposDocumentos as $valor){
					$listaDocumentos[$valor['id']]=$valor['nombre'];
				}

				$tipoUsuario = Session::get('usuarioLogueado.nombreTipoUsuario');
//				$tipoUsuario = $usr['nombreTipoUsuario'];

				return View::make(
					'tramites.detalleTramiteModal',
					array(
						'tramite' => $resultado['tramite'],
						'estados'=>$resultado['estados'],
						'meses'=>$resultado['meses'],
						'dias'=>$resultado['dias'],
						'paso_fecha'=>$resultado['paso_fecha'],
						'cant'=>$resultado['cant'],
						'datosPermiso'=>$resultado['datosPermiso'],
						'usu'=>$usu,
						'tipo_sociedad'=>$tipo_sociedad,
						'tipo_persona'=>$tipo_persona,
						'tipo_situacion'=>$tipo_situacion,
						'tipo_documento'=>$listaDocumentos
					));
			}catch(Exception $e){
				\Log::error($e);
				\Log::error('Problema buscar el detalle del trámite modal');
				return Redirect::back();
			}
		}

		/****************************/
		/* actualizarEstadoTramite 	*/
		/*	08/05/2014				*/
		/****************************/
		public function actualizarEstadoTramite(){
			
			\Log::info('MM - entro');
			$usuarioLogueado = Session::get('usuarioLogueado');
			$datos=Input::all();

			//reglas de validación
			$reglas=array(
				'estadoI'=>'required|max:1|in:0,1,2,3,4,5,6,7,8'
			);

			if($datos['estados_t']==7 && $datos['tipo_tramite']==2 && $datos['subagente_h']!=0){//solo para cdo pasa a agente
				$reglas['nro_nueva_red']='required|min:4|max:5';
				// $reglas['cbu']='min:4';
			}else if($datos['estados_t']==7 && $datos['tipo_tramite']==2 && $datos['subagente_h']==0){//para cuando pasa a subag
					$reglas['nro_red']='required|min:4|max:5';
			}else if($datos['estados_t']==7 && $datos['tipo_tramite']==4){ //cambio de dependencia
					$reglas['nro_red']='required|min:4|max:5';
			}else if($datos['estados_t']==7 && ($datos['tipo_tramite']==3 || $datos['tipo_tramite']==8)){
				if(array_key_exists('tipo_persona', $datos)){
					if($datos['tipo_persona']=="J"){
						$datos['sexo_persona']='S';
					}
				}


				$reglas['sexo_persona']='required|in:F,M,S';
/*				
				if($datos['tipo_tramite']!=2 && $datos['tipo_tramite']!=8 && $datos['subagente_h']==0){// si es agente 00 => es obligatorio el cbu
					$reglas['cbu']='min:4';
					$reglas['cbu']=$reglas['cbu'].'|required';
				}
*/
				if($datos['email_actual']!=""){
					$datos['email']=$datos['email_actual'];
				}

				if($datos['tipo_tramite']==3){
					$reglas['cuit']='required|Cuit';
					$reglas['email']='required|email';
					$reglas['tipo_persona']='required|in:F,J|TipoPersona:'.$datos['cuit'].",".$datos['sexo_persona'];
//					if($datos['tipo_situacion'] != 'rni' &&  $datos['tipo_situacion'] != 'exento')
//						$reglas['ingresos']='required|IngresosBrutos:'.$datos['ingresos'];
				}else{
					$reglas['tipo_persona']='required|in:F,J';
				}

				$reglas['referente']='required|min:4';
				$reglas['datos_contacto']='required|min:4';
				$reglas['fecha_nacimiento']='required|FechaNacimiento:'.$datos['fecha_nac'];
				//IIBB
				/*if($datos['tipo_tramite']==3)
					$reglas['ingresos']='required|IngresosBrutos:'.$datos['ingresos'];*/
			}
			
			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"in"=>"no es un número de estado válido."
			);
			$validar=Validator::make($datos, $reglas, $mensaje);

			if($validar->fails()):   // MM abre validar fails
				\Log::info($validar->messages());
				\Log::info("error validación actualizar estado");
				return Response::json(array('exito'=>0,'mensaje' => 'Ocurrió un error al actualizar el estado.'));
			else:
				$idEstadoI = Input::get('estadoI');
				$idEstadoF = Input::get('estados_t');
				$idTramite = Input::get('nroTramite');
				\Log::info('MM - idEstadoF',array($idEstadoF));
				$extra = array();
				$existeIgualEstado = 0;//$this->servicioHistorico->existeUnoIgual($idTramite,$idEstadoI,$idEstadoF);
				if($existeIgualEstado):   // MM abre if existeIgualEstado
					return Response::json(array('exito'=>0,'mensaje' => 'El trámite ya se encuentra en ese estado. Refresque su pantalla'));
				else:
					$tramite = $this->servicioTramites->buscarPorId($idTramite);
					if($datos['tipo_tramite']==1):
						$extra['planCompleto'] = $this->servicioTramites->planCompleto($idTramite);
					endif;
					$exito = 1;
					$mensaje = 'Éxito';
					$datos_mail = [];
					// \Log::info("validar ae", array($datos));
					try{
						if($idEstadoF == 7)://aprobado
							$resolucion = $datos['resolucion'];
							$expediente = $datos['expediente'];
							if(array_key_exists('nro_nueva_red', $datos) && $datos['tipo_tramite']==2 && ($datos['subagente_h']=="" || $datos['subagente_h']!=0))://cambio de categoria - subagente=> agente
								\Log::info("MM - actualizarEstadoTramite - datos:", array($datos));
								\Log::info("MM - actualizarEstadoTramite - 2691");
								$nroNuevaRed = $datos['nro_nueva_red'];
								$subagente=0;//$datos['subagente_h'];
								$extra['nroNuevaRed'] = $nroNuevaRed;
								$extra['nroNuevoSubAgente'] = str_pad("0", 3, "0", STR_PAD_LEFT);
								$mensaje=$this->servicioTramites->modificarEstadoTramiteAR(
										$idTramite,
										$idEstadoI,
										$idEstadoF,
										$datos,
										$resolucion,
										$expediente,
										$datos_mail,
										$datos['tipo_tramite'],
										$subagente,
										$tramite['nuevo_permiso']);
							elseif(array_key_exists('nro_red', $datos) && $datos['tipo_tramite']==2 && $datos['subagente_h']==0)://cambio de categoria - agente=>subagente
								\Log::info("MM - actualizarEstadoTramite - datos:", array($datos));
								\Log::info("MM - actualizarEstadoTramite - 2709");
								$nroNuevaRed = $datos['nro_red'];
								$subagente=$datos['nro_nuevo_sbag'];
								$extra['nroNuevaRed'] = $nroNuevaRed;
								$extra['nroNuevoSubAgente'] = str_pad($datos['nro_nuevo_sbag'], 3, "0", STR_PAD_LEFT);
									\Log::info("MM - actualiza est. cambio cat (controlador): ", array($extra));
								$mensaje=$this->servicioTramites->modificarEstadoTramiteAR(
										$idTramite,
										$idEstadoI,
										$idEstadoF,
										$nroNuevaRed,
										$resolucion,
										$expediente,
										$datos_mail,
										$datos['tipo_tramite'],
										$subagente,
										$tramite['nuevo_permiso']);
								\Log::info("MM - actualizarEstadoTramite - mensaje: ", array($mensaje));
							elseif(array_key_exists('cuit', $datos) && ($datos['tipo_tramite']==3 || $datos['tipo_tramite']==8))://cambio de titular
								$datos['fecha_nacimiento']=$this->formatos->fecha($datos['fecha_nacimiento']);
								$mensaje=$this->servicioTramites->modificarEstadoTramiteAR(
										$idTramite,
										$idEstadoI,
										$idEstadoF,
										$datos,
										$resolucion,
										$expediente,
										$datos_mail,
										$datos['tipo_tramite'],
										null,
										$tramite['nuevo_permiso']);
							elseif(array_key_exists('nro_red', $datos) && $datos['tipo_tramite']==4)://cambio de dependencia
								$nroNuevaRed = $datos['nro_red'];
								//$subagente=$datos['subagente_nuevo'];
								$subagente=$datos['nro_nuevo_sbag'];
								$mensaje=$this->servicioTramites->modificarEstadoTramiteAR(
										$idTramite,
										$idEstadoI,
										$idEstadoF,
										$nroNuevaRed,
										$resolucion,
										$expediente,
										$datos_mail,
										$datos['tipo_tramite'],
										$subagente,
										$tramite['nuevo_permiso']);
							elseif($datos['tipo_tramite']!= 2 && $datos['tipo_tramite']!=3 && $datos['tipo_tramite']!=8)://otros trámites
								$mensaje=$this->servicioTramites->modificarEstadoTramiteAR(
										$idTramite,
										$idEstadoI,
										$idEstadoF,
										null,
										$resolucion,
										$expediente,
										$datos_mail,
										$datos['tipo_tramite'],
										null,
										$tramite['nuevo_permiso']);
							endif;
						else:
						\Log::info("MM - actualizarEstadoTramite - flujo sigue por else");
							$observaciones = Input::get('textoComentarios_m');//son los comentarios
							\log::info("MM - actualizarEstadoTramite - idTramite",array($idTramite));
							\log::info("MM - actualizarEstadoTramite - idEstadoI",array($idEstadoI));
							\log::info("MM - actualizarEstadoTramite - idEstadoF",array($idEstadoF));
							\log::info("MM - actualizarEstadoTramite - observaciones",array($observaciones));
							\log::info("MM - actualizarEstadoTramite - datos_mail",array($datos_mail));
							\log::info("MM - actualizarEstadoTramite - tramite['nuevo_permiso']",array($tramite['nuevo_permiso']));
							$mensaje=$this->servicioTramites->modificarEstadoTramite(
								$idTramite,
								$idEstadoI,
								$idEstadoF,
								$observaciones,
								$datos_mail,
								$tramite['nuevo_permiso']);
						endif;

								if(!is_null($mensaje))://se modificó correctamente
									//\Log::info("mensaje: ", array($mensaje));
									if(strpos($mensaje,"Debe completar")===false)://no hay campos sin completar
										if($usuarioLogueado['nombreTipoUsuario']!='CAS' && $tramite['nuevo_permiso']!=1 && $idEstadoF!=9)://es el dueño y no es un permiso nuevo ni se rechaza
											$datos_mail['email_duenio'] = $usuarioLogueado['email'];
										elseif($tramite['nuevo_permiso']==1):
											$datos_mail['email_duenio'] = 'emailvacio@gemeil.com';
										else: //es el cas
											$datos_mail['email_duenio'] = $this->controladorUsuario->buscarPorPermiso($datos_mail['permiso_duenio'])['email'];
										endif;
									$datos_mail['nro_permiso'] = $tramite['nro_permiso'] ;
									$datos_mail['codigo_tipo_tramite'] = $datos['tipo_tramite'];
									$datos_mail['subagente_h'] = $datos['subagente_h'];

									// \Log::info("MM put email_cambio_estado 2 y 3: ", array($datos_mail));
									Session::put('email_cambio_estado',$datos_mail);
								else:
									$exito = 0;
								endif;


							$estadosPosibles = $this->servicioTramites->estadosPosibles($idEstadoF, $usuarioLogueado, $tramite['id_tipo_tramite']);

						else:
							$estadosPosibles = $this->servicioTramites->estadosPosibles($idEstadoI, $usuarioLogueado, $tramite['id_tipo_tramite']);
							$exito = 0;
							$mensaje = "Ocurrió un inconveniente al actualizar el estado del trámite.";
						endif;

						$combobox=[];
						foreach ($estadosPosibles as $estado) {
							if($estado['id_estado_tramite']==8):
								if($tramite['id_estado_tramite']==10):
									if(!$tramite['pendiente_informar'] && !$tramite['informado_crm']):
										$combobox[$estado['id_estado_tramite']]=$estado['descripcion_tramite'];
									endif;
								elseif($tramite['id_estado_tramite']==7 || $tramite['id_estado_tramite']==8)://autorizado
									$combobox[$estado['id_estado_tramite']]=$estado['descripcion_tramite'];
								endif;
							else:
								$combobox[$estado['id_estado_tramite']]=$estado['descripcion_tramite'];
							endif;
						}

						$respuesta = Response::json(array('exito'=>$exito,'mensaje'=>$mensaje,'tramite'=>$mensaje, 'estadosPosibles'=>$combobox, 'extra'=>$extra));

						return $respuesta;
				}catch(\Exception $e){
						\Log::error('Problema al modificar el estado del trámite - TramiteController. ' .$e);
						return Response::json(array('exito'=>0,'mensaje' => 'Ocurrió un error al actualizar el estado.'));
					}

					endif; // MM cierra if existeIgualEstado
				endif;  // MM abre validar fails
			}

		/***********************************************/
		/*	Modificar observación de estado de trámite */
		/***********************************************/
		public function nueva_observacion_estado(){
			$nroTramite = Input::get('nro_tramite');
			$observaciones = Input::get('observaciones');
			$fecha = Input::get('fecha');
			$estaI = Input::get('estaI');
			$estaF = Input::get('estaF');
			$this->servicioHistorico->modificaObservacionEstado($nroTramite, $fecha, $estaI, $estaF, $observaciones);
		}

	/****************************************Historial de estados del trámite**************************************/

		/*************************************************/
		/* Función llamada desde la consulta de trámites */
		/*************************************************/
		public function historialTramiteConsulta($nroSeguimiento){
			$tramite = $this->servicioTramites->buscarPorId($nroSeguimiento);
			$usu=Session::get('usuarioLogueado.nombreUsuario');
			return View::make('tramites.historialEstadosTramiteModal', array("tramite" => $tramite, 'usu'=>$usu));
		}

		/**************************************************/
		/* Devuelve el historial de estados de un trámite */
		/**************************************************/
		public function historialEstadoTramite(){

			$idTramite = Input::get('nroTramite');
			$historial=$this->servicioTramites->historialEstadoTramite($idTramite);

			if(is_null($historial)){
				$mensaje = "No existe historial de tramite";
				Session::flash('error', $mensaje);
				return Redirect::back();
						}
			$respuesta = Response::json(array('historial_e'=>$historial));

			return $respuesta;
		}

		/*
			Historial desde el plan estratégico
		*/
		public function historialEstadoTramite_get($idTramite){

			$historial=$this->servicioTramites->historialEstadoTramite($idTramite);

			if(is_null($historial)){
				$mensaje = "No existe historial de tramite";
				Session::flash('error', $mensaje);
				return Redirect::back();
			}
			$respuesta = Response::json(array('historial_e'=>$historial));

					return $respuesta;
	   }

	/************************************** Cancelar Trámite ************************************************************/

		/****************************/
		/* cancelar trámite 		*/
		/*	EM-16/04/2014			*/
		/****************************/
		public function cancelarTramite(){
			$usuarioLogueado=Session::get('usuarioLogueado');
			return View::make('tramites.cancelarTramite');
			}

		/*****************************/
		/* Verifica si existe el nº  */
		/* de seguimiento a cancelar */
		/*****************************/
		public function cancelarTramite_add(){
			$usuarioLogueado=Session::get('usuarioLogueado');
			//obtengo los datos del formulario
			$nroSeguimiento=Input::get('ntramite');

			//verifico que exista
			$existe = $this->servicioTramites->buscarPorIdSinCancelar($nroSeguimiento);

			if(is_null($existe)){
				return Response::json(array('mensaje'=>'No existe el nº de seguimiento o ya está cancelado.'));
			}else{
				if($usuarioLogueado['nombreTipoUsuario']=="CAS" || $usuarioLogueado['nombreTipoUsuario']=='CAS_ROS'){//es de la cas
					Session::put('tramiteACancelar',$existe['nro_tramite']);
					return Response::json(array('mensaje'=>null));
				}else if($usuarioLogueado['permiso']==$existe['nro_permiso']){//es el dueño
					Session::put('tramiteACancelar',$existe['nro_tramite']);
					return Response::json(array('mensaje'=>null));
				}else{
					return Response::json(array('mensaje'=>"No tiene permiso para cancelar el trámite."));
		}
	}
		}

		/***********************************************/
		/* Cancela el tramite y muestra si fue exitosa */
		/* la cancelación o no                         */
		/***********************************************/
		public function show_cancelarTramite(){
			if(Session::has('tramiteACancelar')){
				$datos['nro_tramite']=Session::get('tramiteACancelar');

				$datos_email = array();
				$mensaje=$this->servicioTramites->cancelarTramite($datos, $datos_email);
				
				if(is_null($mensaje)){
					$mensaje = "Problema al cancelar el tramite";
					Session::flash('error', $mensaje);
					return Redirect::back();
				}else{
					$titulo="Cancelación Exitosa";
					Session::flash('mensaje', $mensaje);
					if(Session::has('usuarioTramite'))
						$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
					else
						$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];
					Session::put('datos_email',$datos_email);
					Session::put('cancelado', $titulo);
					Session::forget('tramiteACancelar');
					return Redirect::to('exito-tramite');
				}
			}else{
					$mensaje = "Problema al cancelar el tramite";
					Session::flash('error', $mensaje);
					return Redirect::back();
	}
		}

		/*******************************/
		/*	Cancelar desde consulta    */
		/*******************************/
		public function cancelarTramiteConsulta(){
			$nroSeguimiento = Input::get('id_seleccionado_c');

			$usuarioLogueado = Session::get('usuarioLogueado');
			//verifico que exista
			$existe = $this->servicioTramites->buscarPorIdSinCancelar($nroSeguimiento);
			if(is_null($existe)){
				return Response::json(array('mensaje'=>"No existe el nº de seguimiento, trámite ya cancelado o en estado incompatible para cancelar."));
			}else{
				$tramite = $this->servicioTramites->buscarPorId($nroSeguimiento);

				if($usuarioLogueado['permiso']==$existe['nro_permiso']){//es de la cas o es el dueño
					$datos['nro_tramite']=$existe['nro_tramite'];
						$datos_email = array();
						$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
						$mensaje=$this->servicioTramites->cancelarTramite($datos, $datos_email);

					if(is_null($mensaje)){
						$mensaje = "Problema al cancelar el tramite";
						return Response::json(array('mensaje'=>$mensaje));
					}else{
						return Response::json(array('mensaje'=>"exito"));
	}

				}else if($tramite['nuevo_permiso']==1){
					$idsTramitesPermiso = $this->servicioTramites->buscarTramitesNuevoPermiso($tramite['nro_permiso']);
					foreach ($idsTramitesPermiso as $tramitePermiso=>$tramite) {
						$datos['nro_tramite']=$tramite->nro_tramite;
						$datos_email = array();
						$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
						$mensaje=$this->servicioTramites->cancelarTramite($datos, $datos_email);
				}

					if(is_null($mensaje)){
						$mensaje = "Problema al cancelar el tramite";
						return Response::json(array('mensaje'=>$mensaje));
					}else{
						return Response::json(array('mensaje'=>"exito"));
			}

				}else{
					return Response::json(array('mensaje'=>"No tiene permiso para cancelar el trámite."));
		}


			}
		}

	/***************************************************** Éxito **************************************************/

		/****************************/
		/* exitoTramite			  	*/
		/*	EM-16/04/2014			*/
		/****************************/
		public function exitoTramite(){
			$datos_email=Session::get('datos_email');
			$usuarioLogueado = Session::get('usuarioLogueado');
			if(Session::has('cancelado')){
				$titulo= Session::get('cancelado');
				Session::forget('cancelado');
			}else{
				Session::put('email_inicio_exitoso', $datos_email);
				$this->enviar_email_gral();
				$titulo="Trámite iniciado";
			}
			\Log::info("nro de seguimiento exito: ", array($datos_email['nroTramite']));
			if($usuarioLogueado['nombreTipoUsuario']=='CAS' || $usuarioLogueado['nombreTipoUsuario']=='CAS_ROS'){
				return View::make(
					'tramites.exitoTramite',
					array('usuario'=>$usuarioLogueado['nombreTipoUsuario'],
					'titulo'=>$titulo,
					'nro_seguimiento'=>$datos_email['nroTramite']));
			}else{
				return View::make(
					'tramites.exitoTramite',
					array('usuario'=>$usuarioLogueado['nombreTipoUsuario'],
					'titulo'=>$titulo,
					'nro_seguimiento'=>$datos_email['nroTramite']));
			}
		}

	/***********************************************Informes*******************************************************/
	
		//informe de trámite/s ->botón imprimir consulta en la vista de consulta
		public function informeTramite(){

			Session::forget('url_repositorio');

			$usuarioLogueado=Session::get('usuarioLogueado');
			$datos_consulta = Session::get('datosConsulta');

			$datos_vista = Session::get('datosVistaConsulta');

			//armado de la url para el reporte
			$url_repositorio = Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
			$url_repositorio .= Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.reporte_consulta_tramites')."&";
			$url_repositorio .="formato=".Config::get('habilitacion_config/jasper_config.formatos_reportes.2')."&";
			if($datos_consulta["fechaDesde"]!=''){
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.0')."=".$datos_consulta["fechaDesde"]."&";
			}else{
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.0')."=1900-01-01&";
		}
			if($datos_consulta["fechaHasta"]!=''){
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.1')."=".$datos_consulta["fechaHasta"]."&";
				}else{
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.1')."=2100-12-31&";
				}

			$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.2')."=".$datos_consulta["permiso"]."&";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.3')."=".$datos_consulta["agente"]."&";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.4')."=".$datos_consulta["subAgente"]."&";

			if((strcasecmp($datos_vista['hestadost'],"todos")!=0)){
			    $url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.5')."=".$datos_vista["hestadost"]."&";
			}else{
			    $url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.5')."=&";
				}

			if((strcasecmp($datos_vista["htramitest"],"todos")!=0)){
			    $url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.6')."=".$datos_vista["htramitest"]."&";
			}else{
			    $url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.6')."=&";
		}


			$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_consulta_tramites.nombres.7')."=";

			Session::put('url_repositorio',$url_repositorio);
			\Log::info($url_repositorio);
			return Response::json(array('mensaje'=>'mensaje'));
					}

		public function show_informeTramite(){
			$url_repositorio = Session::get('url_repositorio');

			return View::make('reportes.reportes_tramites', array('url_repositorio'=>$url_repositorio));
		}

		/**************************CARATULA***********************************/
		//caratula de trámite/s ->botón imprimir carátula en la vista de consulta
		public function caratulaTramite(){
			Session::forget('url_caratula');
			$nro_tramite=Input::get('nro_tramite');
			$this->url_caratula($nro_tramite);
			return Response::json(array('mensaje'=>'mensaje'));
					}

		public function show_caratulaTramite(){
			$url_repositorio = Session::get('url_caratula');
			return View::make('reportes.reportes_tramites', array('url_repositorio'=>$url_repositorio));
				}

		/**************************RESOLUCION***********************************/
		//resolucion de trámite/s ->botón imprimir resolucion en la vista de consulta
		public function resolucionTramite(){
			Session::forget('url_resolucion');
			$nro_tramite=Input::get('nro_tramite');
			$tipo_tramite=Input::get('id_tipo_tramite');
			$this->url_resolucion($nro_tramite,$tipo_tramite);
			return Response::json(array('mensaje'=>'mensaje'));
			}

		public function show_resolucionTramite(){
			$url_repositorio = Session::get('url_resolucion');
			return View::make('reportes.reportes_tramites', array('url_repositorio'=>$url_repositorio));
		}

		/**************************NOTAS SOLICITUD***********************************/
		//notas de trámite/s ->botón nota solicitud en la vista de consulta
		public function notaSolicitudTramite(){
			Session::forget('url_nota');
			$nro_tramite=Input::get('nro_tramite');
			$this->url_nota($nro_tramite);
			return Response::json(array('mensaje'=>'mensaje'));
			}

		public function show_notaSolicitudTramite(){
			$url_repositorio = Session::get('url_nota');
			return View::make('reportes.reportes_tramites', array('url_repositorio'=>$url_repositorio));
			}

		public function caratula_nota(){
			Session::forget('url_caratula');
			Session::forget('url_nota');
			$nro_tramite=Input::get('nro_tramite');
			$this->url_caratula($nro_tramite);
			$this->url_nota($nro_tramite);
			return Response::json(array('mensaje'=>'mensaje'));
		}
		/***********************************************************************/
		/* Arma las url para la carátula y la nota de solicitud.			   */
		/***********************************************************************/
		private function url_caratula($nro_tramite){
			$usuarioLogueado=Session::get('usuarioLogueado');
			//armado de la url para el reporte
			$url_repositorio = Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
			$url_repositorio .= Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.caratula_tramites')."&";
			$url_repositorio .="formato=".Config::get('habilitacion_config/jasper_config.formatos_reportes.2')."&";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_caratula.nombres.0')."=".$nro_tramite."&";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_caratula.nombres.1')."=".$usuarioLogueado['nombreTipoUsuario'];
			Session::put('url_caratula',$url_repositorio);
		}

		//nota de solicitud
		private function url_nota($nro_tramite){
			$usuarioLogueado=Session::get('usuarioLogueado');
			$tipo_tramite = $this->servicioTramites->tipoDelTramite($nro_tramite);

			//armado de la url para el reporte
			$url_repositorio = Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
			$url_repositorio .= Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";
/* INICIO modificado ADEN - 2024-09-11
			if($tipo_tramite==3){
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.nota_cesion_titularidad')."&";
			}else{
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.nota_solicitud_tramite')."&";
			}
*/			
			switch ($tipo_tramite) {
				case 3: // cesion de titularidad
					$url_repositorio .=Config::get('habilitacion_config/jasper_config.nota_cesion_titularidad')."&";
					\Log::info("aa - tipo tramite 3: ", array($tipo_tramite));
					break;
				case 8: // Incorporacion de Cotitular
					$url_repositorio .=Config::get('habilitacion_config/jasper_config.nota_alta_cotitular')."&";
					\Log::info("aa - tipo tramite 8: ", array($tipo_tramite));
					break;
				case 11: // Baja de Cotitular
					$url_repositorio .=Config::get('habilitacion_config/jasper_config.nota_baja_cotitular')."&";
					\Log::info("aa - tipo tramite 11: ", array($tipo_tramite));
					break;
				case 12: // cesion de titularidad por fallecimiento
					$url_repositorio .=Config::get('habilitacion_config/jasper_config.nota_cesion_por_fallecimiento')."&";
					\Log::info("aa - tipo tramite 12: ", array($tipo_tramite));
					break;
				case 13: // renuncia
					$url_repositorio .=Config::get('habilitacion_config/jasper_config.nota_renuncia')."&";
					\Log::info("aa - tipo tramite 13: ", array($tipo_tramite));
					break;
				default:
					$url_repositorio .=Config::get('habilitacion_config/jasper_config.nota_solicitud_tramite')."&";
			}
			\Log::info("aa - reporte a imprimir A: ", array($url_repositorio));

// FINAL modificado ADEN - 2024-09-19
			$url_repositorio .="formato=".Config::get('habilitacion_config/jasper_config.formatos_reportes.2')."&";
			if($tipo_tramite==3){
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_nota_cesion_titularidad.nombres.0')."=".$nro_tramite;
			}else{
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_nota_solicitud.nombres.0')."=".$nro_tramite;
			}

			\Log::info("aa - reporte a imprimir B: ", array($url_repositorio));

			Session::put('url_nota',$url_repositorio);
		}

		private function url_resolucion($nro_tramite,$tipo_tramite){
			$usuarioLogueado=Session::get('usuarioLogueado');

			//armado de la url para el reporte
			$url_repositorio = Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
			$url_repositorio .= Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";

			if ($tipo_tramite == 1) { // domicilio
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.resolucion_tramite.url_resolucion.1')."&";
			} else if ($tipo_tramite == 3) { // titular
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.resolucion_tramite.url_resolucion.2')."&";
			} else if ($tipo_tramite == 4) { //dependencia
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.resolucion_tramite.url_resolucion.0')."&";
			} else if ($tipo_tramite == 2) { //categoria
				$url_repositorio .=Config::get('habilitacion_config/jasper_config.resolucion_tramite.url_resolucion.3')."&";
			};

			$url_repositorio .="formato=".Config::get('habilitacion_config/jasper_config.formatos_reportes.2')."&";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_nota_solicitud.nombres.0')."=".$nro_tramite;
			Session::put('url_resolucion',$url_repositorio);
	}

	/********************************Envío de emails****************************************************/

		/***********************************************************************/
		/* Función que se encarga del envío de email						   */
		/* cdo la carga del trámite fue exitosa.							   */
		/***********************************************************************/
		public function enviar_email_gral(){
			$datosTramite = Session::get('email_inicio_exitoso');
			\Log::info("enviar_email_gral - antes de mandar los mails de email_gral: ");
			\Log::info($datosTramite);
			try{
				$usuarioLogueado=Session::get('usuarioLogueado');
				// \Log::info("enviar_email_gral - correo->usuarioLogueado: ", array($usuarioLogueado));
				$localidadActual=Session::get('usuarioLogueado.localidadAgencia'); // ADEN 2024-03-26
				// \Log::info("enviar_email_gral - correo->localidad: ", array($localidadActual));
				$usuarioTramite = Session::get('usuarioTramite');
				$linkguia = Config::get('habilitacion_config/config.urlpac').Config::get('habilitacion_config/config.url_guia');

				// INI ADEN 2024-03-26
				\Log::info("enviar_email_gral - correo->datosTramite: ", array($datosTramite));
				
				
				$dom_cargado = Session::get('domicilio_cargado_para_mail');
				\Log::info("cambioDomicilio_add ->datos 3: ", array($dom_cargado));
				// FIN ADEN 2024-03-26
				
				$margen=Session::get('ultimoTramite');

				$meses = $margen['meses'];
				$dias = $margen['dias'];
				$paso_fecha = $margen['paso_fecha'];
				$cant = $margen['cant'];

				Session::forget('ultimoTramite');
				$data = array(
					'meses'=>$meses,
					'dias'=>$dias,
					'paso_fecha'=>$paso_fecha,
					'cant'=>$cant,
					'nroAgente' => $datosTramite['agente'],
					'nroSubAgente' => $datosTramite['subagente'],
					'nroPermiso' => $datosTramite['nro_permiso'],
					'tipoTramite' => $datosTramite['tipoTramite'],
					'nroTramite' => $datosTramite['nroTramite'],
				// INI ADEN 2024-03-26
					'domicilio_nuevo' => $dom_cargado['domicilio_comercial'],
					'localidad_nuevo' => $dom_cargado['nombre_nueva_localidad'],
					'motivo' => $dom_cargado['motivo_cd'],
				// FIN ADEN 2024-03-26						
					'usuarioGenerador'=>$usuarioLogueado['nombreUsuario'],
					'linkguia' => $linkguia
				);

				\Log::info("enviar_email_gral - data: ", array($data));

				$mloteria=Config::get('mail.from.address'); //en el archivo app/config/mail.php
				$lista_cas=Config::get('mail.lista_cas');
				$listaJuegos_bancados = Config::get('mail.juegos_bancados');
				// $mBoldt=Config::get('mail.boldt'); //en el archivo app/config/mail.php*/

				/*$tipoUsuarioLogueado=$usuarioLogueado['nombreTipoUsuario'];
				$tiposASA=array('Agente','SubAgente');

				/*if(in_array($tipoUsuarioLogueado,$tiposASA)){
					$destinatarios = array(
						   'natalia.mignola@i2t.com.ar','andres.muller@i2t.com.ar'
						);
				}else{
					$destinatarios = array(
						   'natalia.mignola@i2t.com.ar','andres.muller@i2t.com.ar'
						);
				}*/
				if($datosTramite['tipoTramite'] == 'Cambio de Categoria' || ($datosTramite['tipoTramite'] == 'Cambio de Titular' && $datosTramite['subagente'] == "0") ){
					$destinatarios = array(
						   $datosTramite['email_duenio'],$lista_cas,$listaJuegos_bancados //,$mBoldt
						);
				}else{
					$destinatarios = array(
						   $datosTramite['email_duenio'],$lista_cas //,$mBoldt
						);
				}

				/* ADEN 2024-03-26 - Se pidió cambio para que los cambios de domicilio se informe en el SUBJECT
						CAMBIO DE DOMICILIO - Agencia 6001/000 - Santa Fe
				*/
				\Log::info("enviar_email_gral - correo->tipo de tramite: ", array($datosTramite));
				$subject = 'Acuse Registro Tramite Registrado';
				\Log::info("enviar_email_gral - correo->subject 1: ", array($subject));
				if($datosTramite['tipoTramite'] == 'Cambio de Domicilio Comercial') {
					$subject = $subject 
									. ' - ' . $datosTramite['tipoTramite']
									. ' - Agencia: ' . $data['nroAgente'] . '/' . $data['nroSubAgente'] . ' - Localidad: ' .$localidadActual;
				}
				\Log::info("enviar_email_gral - correo->subject 2: ", array($subject));

				$correos = array(
					'destinatarios'=>$destinatarios,
					'subject'=>$subject,
					'remitente'=>$mloteria);
				/*
					ADEN 2024-03-26 fin de ajuste
				*/
				if(empty($destinatarios))
					return true;
				
				\Log::info("enviar_email_gral - correos: ", array($correos));

				Mail::send('emails.mail_tramite_exito', $data, function ($message) use ($correos){
					/*$message->from($correos['remitente'], 'Sistema de tramites');
					$message->subject('Acuse Registro Tramite Registrado.');
					foreach ((array)$correos['destinatarios'] as $email) {
						if(isset($email))
							$message->to(explode(';', $email));
					}*/


					$concatenoEmails = "";
					$message->from($correos['remitente'], 'Sistema de tramites');
					// INI ADEN 2024-03-26 
					$message->subject($correos['subject']);
					// $message->subject('Acuse Registro Tramite Registrado.');
					// FIN ADEN 2024-03-26

					foreach ((array)$correos['destinatarios'] as $email) {
						// $email
						$arregloEmail = explode(";", $email);
						foreach ($arregloEmail as $mail) {
							if (isset($mail) && $mail != ""){
									$concatenoEmails = $concatenoEmails . $mail .  ";";
							}
						}
					}
					$concatenoEmails = substr($concatenoEmails, 0, -1);
					$message->to(explode(";",$concatenoEmails));

				});
				Session::forget('email_inicio_exitoso');

				if(count(Mail::failures()) > 0){
					\Log::error('Problema al enviar el correo. '.$e);
					return false;
				}else{
					return true;
}

			}catch(Exception $e){
				Log::error('Problema al enviar el correo. '.$e);
				return false;
}
}

		/***********************************************************************/
		/* Función que se encarga del envío de email						   */
		/* cdo se modifica el estado del trámite.							   */
		/***********************************************************************/
		public function enviar_email_modificacion_estado(){
			$datosTramite=Session::get('email_cambio_estado');
			\Log::info("enviar_email_modificacion_estado - datosTramite: ", array($datosTramite));
			try{
				$usuarioLogueado=Session::get('usuarioLogueado');

				$servidor_crm= Config::get('habilitacion_config/jasper_config.servidor_crm');

				if($usuarioLogueado['nombreTipoUsuario']=='Agente' || $usuarioLogueado['nombreTipoUsuario']=='SubAgente'){
					if(stripos($usuarioLogueado['apellido'],'-')!==false){
						$nombreUsuarioLogueado = explode('-',$usuarioLogueado['apellido'])[1];
					}else{
						$nombreUsuarioLogueado = $usuarioLogueado['apellido'];
					}
				}else{
					$nombreUsuarioLogueado = $usuarioLogueado['apellido'].", ".$usuarioLogueado['nombre'];
				}
				$linkpdf = Session::get('url_caratula');
				$linkpdf2 = Session::get('url_nota');
				$linkguia = Config::get('habilitacion_config/config.urlpac').Config::get('habilitacion_config/config.url_guia');
				$data = array(
					'nro_permiso'=>$datosTramite['nro_permiso'],
					// 'agente'=>"6001", //, $datosTramite['agente'], 
					'esNuevoTramite'=>$datosTramite['esNuevoTramite'],
					'tipoTramite' => $datosTramite['tipoTramite'],
					'nroTramite' => $datosTramite['nroTramite'],
					'estadoI' => $datosTramite['estadoI'],
					'estadoF' => $datosTramite['estadoF'],
					'observaciones' => $datosTramite['observaciones'],
					'usuarioGenerador'=>$nombreUsuarioLogueado,
					'linkguia'=>$linkguia
				);

				if($datosTramite['idEstadoF']>=2 && $datosTramite['idEstadoF']<6){
					$data['linkPDF'] = $linkpdf;
					$data['linkPDF2'] = $linkpdf2;
				}
				
				// recuperar agente y subagente con permiso
				// buscarPorPermiso
				$datosPermiso=$this->controladorUsuario->buscarPorPermiso($datosTramite['nro_permiso']);
				\Log::info('MM - enviar_email_modificacion_estado - datosPermiso : ', array($datosPermiso));
				\Log::info('MM - enviar_email_modificacion_estado - datosPermiso : ', array($datosPermiso));
				\Log::info('MM - enviar_email_modificacion_estado - datosPermiso agente : ', array($datosPermiso['agente']));
				\Log::info('MM - enviar_email_modificacion_estado - datosPermiso subAgente : ', array($datosPermiso['subAgente']));
				$agente = $datosPermiso['agente'];
				$data['agente'] = $agente;
				
				$subagente = $datosPermiso['subAgente'];
				$data['subagente'] = $subagente;
				// "agente":6001,"subAgente":"0"
				
				$mloteria=Config::get('mail.from.address'); //en el archivo app/config/mail.php
				$mBoldt=Config::get('mail.boldt'); //en el archivo app/config/mail.php
				$lista_cas=Config::get('mail.lista_cas');

				$listaJuegos_bancados = Config::get('mail.juegos_bancados');
				$listaDistrib_premios = Config::get('mail.distrib_premios');
				$listaSede_rosario = Config::get('mail.sede_rosario');
				$encuesta = Config::get('habilitacion_config/config.encuesta_goo');
				$envio_mailAgencias = Config::get('mail.envio_mailAgencias');
				// $encuesta = Config::get('habilitacion_config/config.envio_mailAgencias');
				// enviar_email_gral
				//destinatarios: usuario del trámite (si no es cas, es el logueado)
				$estados_envio_cas=array(10);// (1,3,10);
				$estados_envio_duenio=array(2,9,8,10,11);// (0,2,5,7,9,10,); //-->antes de cambio con incidencia 3062
				$estados_envio_finalizacion=array(10);

				$id_tipo_tramtes=array(2,3,4,5,8,9); // tipos de tramites(cambio categoria, cesion titularidad, cambio dependencia, baja permiso|revocacion,

				// \Log::info("valor in_array: ", array(in_array($datosTramite['idEstadoF'],$estados_envio_cas)));

				$destinatarios="";

				if(in_array($datosTramite['idEstadoF'],$estados_envio_cas)){//en validación - ingresado
					$destinatarios=$destinatarios.(($destinatarios != "") ? ';' : '').$lista_cas;//acá lista correo de la cas
				}
				
				if($envio_mailAgencias == 'S'){ //  S-> si enviar correoal agenciero en todos los estados
						$destinatarios=$destinatarios.(($destinatarios != "") ? ';' : '').$datosTramite['email_duenio'];
				}else{
				
					if(in_array($datosTramite['idEstadoF'],$estados_envio_duenio)){
						$regla=array('email_duenio'=>'email');
						$validar=Validator::make($datosTramite, $regla);
						if($validar->fails()){
							\Log::info('Problema con el envío de correo por email vacío.');
							throw new Exception('Email vacío');
						}
						/*
						agregado de control de envios mediante switch envio_mailAgencias en archivo de configuracion mail
						*/
						
						// \Log::info('MM - enviar_email_modificacion_estado - envio_mailAgencias estado: ', array($envio_mailAgencias));
						// \Log::info('MM - enviar_email_modificacion_estado - envio_mailAgencias email_duenio', array($datosTramite['email_duenio']));
						// \Log::info('MM - enviar_email_modificacion_estado - envio_mailAgencias idEstadoF', array($datosTramite['idEstadoF']));
						
						// envio de correo a agenciero cuando es finalizado
						
						// envio de correo en estados 2(iniciado)  y 10(finalizado) 9  8  11   -- agregados 09-10-2022
						if( $envio_mailAgencias == 'N' && ($datosTramite['idEstadoF'] == 2 || $datosTramite['idEstadoF'] == 10  || $datosTramite['idEstadoF'] == 9 ||  $datosTramite['idEstadoF'] == 8 || $datosTramite['idEstadoF'] == 11)){
							\Log::info('MM - enviar_email_modificacion_estado - N y 2||10');
							$destinatarios=$destinatarios.(($destinatarios != "") ? ';' : '').$datosTramite['email_duenio'];
						}else{
							\Log::info('MM - enviar_email_modificacion_estado - N y no es 10');
							$destinatarios=$destinatarios.(($destinatarios != "") ? ';' : '');
						}
					}
					// $destinatarios=$destinatarios.(($destinatarios != "") ? ';' : '').$datosTramite['email_duenio'];
				}
				/*
				if($datosTramite['idEstadoF']!=10){
					//en evaluación - firma - cancelado
					return true;
				}
				*/
				// Por fin le mando a boldt
				\Log::info("enviar_email_modificacion_estado - datosTramite-idEstadoF: ", array($datosTramite['idEstadoF']));
				
				// \Log::info("enviar_email_modificacion_estado - estados_envio_finalizacion: ", array($estados_envio_finalizacion));
				
				if(in_array($datosTramite['idEstadoF'], $estados_envio_finalizacion)) {
					//if($datosTramite['codigo_tipo_tramite'] == 6){ // tipo de tramite Incorporar Máquina -solo cuando  pase a estado 10 - finalizado
						$destinatarios=$destinatarios.(($destinatarios != "") ? ';' : '').$mBoldt;
					// }
					$data['linkEncuesta'] = $encuesta;
				}
				
				// Por fin de los tramites indicados, agrego juegos_bancados, listaDistrib_premios, listaSede_rosario
				if(in_array($datosTramite['idEstadoF'],$estados_envio_finalizacion ) && in_array($datosTramite['codigo_tipo_tramite'],$id_tipo_tramtes)){
						// se envia a bancados para /0 tramites 2 (cambio de categoria) -siempre- y 3 (cambio de titular) -solo agente-
							\Log::info('MM - enviar_email_modificacion_estado -verificar', array($datosTramite));

						if($datosTramite['codigo_tipo_tramite'] == 2 || ($datosTramite['codigo_tipo_tramite'] == 3 && $datosTramite['subagente_h'] == 0 )){

							$destinatarios=$destinatarios.(($destinatarios != "") ? ';' : '').$listaJuegos_bancados;
						}

						$destinatarios=$destinatarios.(($destinatarios != "") ? ';' : '').$listaDistrib_premios;
						$destinatarios=$destinatarios.(($destinatarios != "") ? ';' : '').$listaSede_rosario;
				}
				
				\Log::info("enviar_email_modificacion_estado - destinatarios: ", array($destinatarios));
				
				$correos = array(
					'destinatarios'=>$destinatarios,
					'remitente'=>$mloteria
				);
					
				\Log::info("enviar_email_modificacion_estado - correos: ", array($correos));
				
			if(isset($destinatarios) && $destinatarios != ""){
				Mail::send('emails.mail_tramite_modificacion', $data, function ($message) use ($correos){
					$concatenoEmails = "";
					$message->from($correos['remitente'], 'Sistema de tramites');
				   $message->subject('Acuse Modificación Estado del Tramite.');
					foreach ((array)$correos['destinatarios'] as $email) {
						$arregloEmail = explode(";", $email);
						foreach ($arregloEmail as $mail) {
							if (isset($mail) && $mail != ""){
									$concatenoEmails = $concatenoEmails . $mail .  ";";
							}
						}
					}
					$concatenoEmails = substr($concatenoEmails, 0, -1);
					$message->to(explode(";",$concatenoEmails));
					$message->bcc('software@i2t-sa.com.ar', 'My Name');
					});

				Session::forget('email_cambio_estado');
				if(count(Mail::failures()) > 0){
					\Log::error('Problema al enviar el correo. ');
					return 0;
				}else{
						\Log::error('No existen destinatarios de correo');
						Session::forget('email_cambio_estado');
						return 1;
					}
			}else{
					\Log::error('No existen destinatarios de correo');
					Session::forget('email_cambio_estado');
					return 1;
			}
			}catch(Exception $e){
				\Log::error('Problema al enviar el correo. '.$e);
				return 0;
			}
		}

	/********************************Envío de emails****************************************************/

		/**
			* Envío email incorporacion/retiro máquina
		**/
		public function enviar_email_incoretmaq($datos_email){
			try{
				\Log::info("Incorp/Retiro maq ingreso");
				$mloteria=Config::get('mail.from.address'); //en el archivo app/config/mail.php
				$mBoldt=Config::get('mail.boldt'); //en el archivo app/config/mail.php
				//$usuarioCas = Session::get('usuarioLogueado.email');
				$listaCas = Config::get('mail.lista_cas');
				$listaJuegos_bancados = Config::get('mail.juegos_bancados');
				$listaDistrib_premios = Config::get('mail.distrib_premios');
				$listaSede_rosario = Config::get('mail.sede_rosario');
				\Log::info("antes de mandar los mails de incoretmaq: ");
				\Log::info($datos_email);
				$nombreUsuarioLogueado=Session::get('usuarioLogueado.apellido');
				// tipo_tramite 6 -> Incorporacion de maquina
				// tipo_tramite 7 -> Retiro de maquina
				if($datos_email['id_tipo_tramite'] == 7){
					if($datos_email['subagente'] == "0"){
						$destinatarios = array(
							$mBoldt, $listaCas, $listaJuegos_bancados, $listaDistrib_premios, $listaSede_rosario //, $usuarioCas
						);
					}else{
						$destinatarios = array(
							$mBoldt, $listaCas,$listaDistrib_premios, $listaSede_rosario //, $usuarioCas
						);
					}
				}else{ // entre tipo de tramite 6 - inc maq
					if($datos_email['estado_tramite'] == 10){ // estado finalizado viene cuando lo crea usuario CAS y debe enviar correo a boldt
							if($datos_email['subagente'] == "0"){
								$destinatarios = array(
									$mBoldt, $listaCas, $listaJuegos_bancados
								);
							}else{
								$destinatarios = array(
									$mBoldt, $listaCas
								);
							}
					}else{
						if($datos_email['subagente'] == "0"){
								$destinatarios = array(
									$listaCas, $listaJuegos_bancados
								);
							}else{
								$destinatarios = array(
									$listaCas
								);
							}
					}
				}

				$usuario=Session::get('usuarioLogueado');
				if($usuario['nombreTipoUsuario']!='CAS' && $usuario['nombreTipoUsuario']!='CAS_ROS')
					$destinatarios[] = $this->mailAgente();

				$correos = array(
					'destinatarios'=>$destinatarios,
					'remitente'=>$mloteria);

				$data = array(
					'tipoTramite' => $datos_email['tipoTramite'],
					'agente' => $datos_email['agente'],
					'subagente' => $datos_email['subagente'],
					// 'razonSocial' => $datos_email['razonSocial'],
					'titular' => $datos_email['titular'],
					'nroTramite' => $datos_email['nroTramite'],
					'tipo_terminal' => $datos_email['tipo_terminal'],
					'observaciones' => $datos_email['observaciones'],
					'usuarioGenerador'=>$nombreUsuarioLogueado
				);
				$data['observaciones'] = str_replace("\n", '<br>', $data['observaciones']);
				Mail::send('emails.mail_tramite_maquina', $data, function ($message) use ($correos){
					$concatenoEmails = "";
					$message->from($correos['remitente'], 'Sistema de tramites');
					$message->subject('Acuse Registro Tramite Registrado.');
					foreach ((array)$correos['destinatarios'] as $email) {
						$arregloEmail = explode(";", $email);
						foreach ($arregloEmail as $mail) {
							if (isset($mail) && $mail != ""){
								$concatenoEmails = $concatenoEmails . $mail .  ";";
							}
						}
					}
					$concatenoEmails = substr($concatenoEmails, 0, -1);
					$message->to(explode(";",$concatenoEmails));
					$message->bcc('software@i2t-sa.com.ar', 'My Name');
				});
				Session::forget('email_inicio_exitoso');
				if(count(Mail::failures()) > 0){
					\Log::error('Problema al enviar el correo. '.$e);
					return 0;
				}else{
					return 1;
				}
			}catch(Exception $e){
				\Log::error('Problema al enviar el correo maquina exce. '.$e);
				return 0;
			}
		}

		/**
			* Envío email alta / baja de permiso
			* Carga (estado 0)
		**/
		public function enviar_email_alta_baja_permiso($datos_mail){
		try{
					$mloteria=Config::get('mail.from.address'); //en el archivo app/config/mail.php
					$mBoldt=Config::get('mail.boldt'); //en el archivo app/config/mail.php
					$listaJuegos_bancados = Config::get('mail.juegos_bancados');
					$nombreUsuarioLogueado=Session::get('usuarioLogueado.apellido');
					if($datos_mail['subagente'] == "0"){
						$destinatarios = array(
						  $mBoldt,$listaJuegos_bancados
						);
					}else{
						$destinatarios = array(
						  $mBoldt
						);
					}
					$correos = array(
						'destinatarios'=>$destinatarios,
						'remitente'=>$mloteria);

					$data = array(
						'tipoTramite'=>$datos_mail['tipoTramite'],
						'permiso' => $datos_mail['nroPermiso'],
						'nroTramite' => $datos_mail['nroTramite'],
						'usuarioGenerador'=>$nombreUsuarioLogueado,
						'agente' => $datos_mail['agente'],
						'subagente' => $datos_mail['subagente']
					);

					\Log::info("enviar_email_alta_baja_permiso -> correos-destinatarios: ", array($correos['destinatarios']));

					Mail::send('emails.mail_tramite_alta_baja_permiso', $data, function ($message) use ($correos){

						$concatenoEmails = "";
						$message->from($correos['remitente'], 'Sistema de tramites');
						$message->subject('Acuse Registro Tramite Registrado.');
						foreach ((array)$correos['destinatarios'] as $email) {
							$arregloEmail = explode(";", $email);
							foreach ($arregloEmail as $mail) {
								if (isset($mail) && $mail != ""){
									$concatenoEmails = $concatenoEmails . $mail .  ";";
								}
							}
						}
						$concatenoEmails = substr($concatenoEmails, 0, -1);
						$message->to(explode(";",$concatenoEmails));
						$message->bcc('software@i2t-sa.com.ar', 'My Name');

					});

					if(count(Mail::failures()) > 0){
						\Log::error('Problema al enviar el correo. '.$e);
						return 0;
					}else{
						return 1;
					}

				}catch(Exception $e){
					\Log::error('Problema al enviar el correo. '.$e);
					return 0;
			}
				}

		/**
		* Envío email suspe/hab permiso
					// Carga de tramites
					//  9->habilitacion permiso
					// 10-> suspencion
		**/
		public function enviar_email_suspen_habil_permiso($datos_email,$modo){
			try{
					$mloteria=Config::get('mail.from.address'); //en el archivo app/config/mail.php
					$mBoldt=Config::get('mail.boldt'); //en el archivo app/config/mail.php

					$listaCas = Config::get('mail.lista_cas');
					$listaJuegos_bancados = Config::get('mail.juegos_bancados');
					$listaDistrib_premios = Config::get('mail.distrib_premios');
					$listaSede_rosario = Config::get('mail.sede_rosario');

					$nombreUsuarioLogueado=Session::get('usuarioLogueado.apellido');

					\Log::info("enviar_email_suspen_habil_permiso -> datos_email: ", array($datos_email));
					$destinatarios = array(
							  $mBoldt, $listaCas, $listaJuegos_bancados, $listaDistrib_premios, $listaSede_rosario
							);

					$correos = array(
						'destinatarios'=>$destinatarios,
						'remitente'=>$mloteria);
					if($modo=='S'){
						$data = array(
						'tipoTramite' => $datos_email['nombreTipoTramite'],
						'nroTramite' => $datos_email['nroTramite'],
						'nroPermiso' => $datos_email['nroPermiso'],
						'agente' => $datos_email['agente'],
						'subagente' => $datos_email['subAgente'],
						'observaciones' => $datos_email['observaciones'],
						'titular' => $datos_email['titular'],
						'usuarioGenerador'=>$nombreUsuarioLogueado
					);
					}else{
						$data = array(
							'tipoTramite' => $datos_email['nombreTipoTramite'],
							'nroTramite' => $datos_email['nroTramite'],
							'nroPermiso' => $datos_email['nroPermiso'],
							'titular' => $datos_email['titular'],
							'agente' => $datos_email['agente'],
							'subagente' => $datos_email['subagente'],
							'observaciones' => $datos_email['observaciones'],
							'usuarioGenerador'=>$nombreUsuarioLogueado
						);
					}
					\Log::info("mail_tramite_susp_hab_permiso o mail_tramite_hab_permiso -> correos: ", array($correos));
					if($modo == 'S'){
						Mail::send('emails.mail_tramite_susp_hab_permiso', $data, function ($message) use ($correos){
						$concatenoEmails = "";
						$message->from($correos['remitente'], 'Sistema de tramites');
						$message->subject('Acuse Registro Tramite Registrado.');
						foreach ((array)$correos['destinatarios'] as $email) {
							$arregloEmail = explode(";", $email);
							foreach ($arregloEmail as $mail) {
								if (isset($mail) && $mail != ""){
										$concatenoEmails = $concatenoEmails . $mail .  ";";
								}
							}
						}
						$concatenoEmails = substr($concatenoEmails, 0, -1);
						$message->to(explode(";",$concatenoEmails));
						$message->bcc('software@i2t-sa.com.ar', 'My Name');
						});
					}else{
						Mail::send('emails.mail_tramite_hab_permiso', $data, function ($message) use ($correos){
						$concatenoEmails = "";
						$message->from($correos['remitente'], 'Sistema de tramites');
						$message->subject('Acuse Registro Tramite Registrado.');
						foreach ((array)$correos['destinatarios'] as $email) {
							$arregloEmail = explode(";", $email);
							foreach ($arregloEmail as $mail) {
								if (isset($mail) && $mail != ""){
										$concatenoEmails = $concatenoEmails . $mail .  ";";
								}
							}
						}
						$concatenoEmails = substr($concatenoEmails, 0, -1);
						$message->to(explode(";",$concatenoEmails));
						$message->bcc('software@i2t-sa.com.ar', 'My Name');
						});
					}
					if(count(Mail::failures()) > 0){
						\Log::error('Problema al enviar el correo. '.$e);
						return 0;
					}else{
						return 1;
					}
				}catch(Exception $e){
					\Log::error('Problema al enviar el correo. '.$e);
					return 0;
				}
		}

		/*
		public function enviar_email_suspen_habil_permiso($datos_email){
			try{
					$mloteria=Config::get('mail.from.address'); //en el archivo app/config/mail.php
					$mBoldt=Config::get('mail.boldt'); //en el archivo app/config/mail.php

					$listaCas = Config::get('mail.lista_cas');
					$listaJuegos_bancados = Config::get('mail.juegos_bancados');
					$listaDistrib_premios = Config::get('mail.distrib_premios');
					$listaSede_rosario = Config::get('mail.sede_rosario');

					$nombreUsuarioLogueado=Session::get('usuarioLogueado.apellido');

					\Log::info("enviar_email_suspen_habil_permiso -> datos_email: ", array($datos_email));

					$destinatarios = array(
							  $mBoldt, $listaCas, $listaJuegos_bancados, $listaDistrib_premios, $listaSede_rosario
							);

							$correos = array(
						'destinatarios'=>$destinatarios,
						'remitente'=>$mloteria);

					$data = array(
						'tipoTramite' => $datos_email['nombreTipoTramite'],
						'nroTramite' => $datos_email['nroTramite'],
						'nroPermiso' => $datos_email['nroPermiso'],
						'usuarioGenerador'=>$nombreUsuarioLogueado
					);

					Mail::send('emails.mail_tramite_susp_hab_permiso', $data, function ($message) use ($correos){
					$concatenoEmails = "";
					$message->from($correos['remitente'], 'Sistema de tramites');
					$message->subject('Acuse Registro Tramite Registrado.');
					foreach ((array)$correos['destinatarios'] as $email) {
						$arregloEmail = explode(";", $email);
						foreach ($arregloEmail as $mail) {
							if (isset($mail) && $mail != ""){
									$concatenoEmails = $concatenoEmails . $mail .  ";";
							}
						}
					}
					$concatenoEmails = substr($concatenoEmails, 0, -1);
					$message->to(explode(";",$concatenoEmails));
					$message->bcc('software@i2t-sa.com.ar', 'My Name');
					});

					if(count(Mail::failures()) > 0){
						\Log::error('Problema al enviar el correo. '.$e);
						return 0;
					}else{
						return 1;
					}
				}catch(Exception $e){
					\Log::error('Problema al enviar el correo. '.$e);
					return 0;
				}
		}
*/
		/*******************************************************************************************
		* Envío email SOLICITUD ID VALIDACION (verificación de casilla de correo) 
		********************************************************************************************/
		public function enviar_email_validacion_correo($nuevo_correo, $codigo_validacion){
			try{
					$mloteria=Config::get('mail.from.address'); //en el archivo app/config/mail.php
//					$mBoldt=Config::get('mail.boldt'); //en el archivo app/config/mail.php

					$nombreUsuarioLogueado=Session::get('usuarioLogueado.apellido');
					\Log::info("enviar_email_validacion_correo -> nombreUsuarioLogueado: ", array($nombreUsuarioLogueado));

					\Log::info("enviar_email_validacion_correo -> destinatario: ", array($nuevo_correo));

					$correos = array(
						'destinatarios'=>$nuevo_correo,
						'remitente'=>$mloteria);
					$data = array(
						'usuarioGenerador'=>$nombreUsuarioLogueado,
						'codigoValidacion'=>$codigo_validacion
					);
					\Log::info("enviar_email_validacion_correo -> correos: ", array($correos));

					Mail::send('emails.mail_validacion_correo', $data, function ($message) use ($correos){
						$concatenoEmails = "";
						$message->from($correos['remitente'], 'Sistema de tramites');
						$message->subject('Módulo de trámites - Codigo de Validacion de Casilla de Correo.');
						foreach ((array)$correos['destinatarios'] as $email) {
							$arregloEmail = explode(";", $email);
							foreach ($arregloEmail as $mail) {
								if (isset($mail) && $mail != ""){
										$concatenoEmails = $concatenoEmails . $mail .  ";";
								}
							}
						}
						$concatenoEmails = substr($concatenoEmails, 0, -1);
						$message->to(explode(";",$concatenoEmails));
						$message->bcc('software@i2t-sa.com.ar', 'Dpto Software - i2T SA');
						});
					if(count(Mail::failures()) > 0){
						\Log::error('enviar_email_validacion_correo - Problema al enviar el correo. '.$e);
						return 0;
					}else{
						return 1;
					}
				}catch(Exception $e){
					\Log::error('enviar_email_validacion_correo - Problema al enviar el correo. '.$e);
					return 0;
				}
		}


	/**************************** csv *************************************************/
		public function csv(){
			$datos = Session::get('datosConsulta');
			$datos['pagina'] = "-1";

			//$table = $this->servicioTramites->buscarTodosCSV($datos);
			$table = $this->servicioTramites->listar($datos);

			foreach ($table['tramites'] as $key => $tramite) {
				$tablita[]=array('nro_tramite'=>$tramite['nro_tramite'],
								 'tipo_tramite'=>$tramite['nombre_tramite'],
								 'fecha'=>$tramite['fecha'],
								 'fecha_ultima_modificacion'=>$tramite['fecha_ultima_modificacion'],
								 'estado_tramite'=>$tramite['descripcion_estado'],
								 'ingreso'=>$tramite['ingreso'],
								 'razon_social'=>$tramite['razon_social'],
								 'permiso'=>$tramite['nro_permiso'],
								 'agente'=>$tramite['agente'],
								 'subagente'=>$tramite['subagente']);
				}

			$output="Nº Seguimiento\tTipo Trámite\tFecha Inicio\tFecha Última Modificación\tEstado\tIngresó en\tRazon Social\tPermiso\tAgente\tSubagente\t";
			$output.="\n";
			foreach ($tablita as $row) {
			  $output.=  implode("\t",$row)."\n";
		}

			$headers = array(
			  'Content-Type' => 'text/csv',
			  'Charset'=>'UTF-8',
			  'Content-Disposition' => 'attachment; filename="GrillaConsultaTramites.csv"',
			);

			return Response::make($output, 200, $headers);

	}

		/***************** Datos generales para todas las vistas ****************************/
		private function datosComunesVistas(&$datos){
			if(Session::has('usuarioTramite') && !Session::has('tramiteNuevoPermiso')){//es cas y entra a hacerle el trámite a un agente/subagente
					$datos['agente']=Session::get('usuarioTramite.agente');
					$datos['subagente']=Session::get('usuarioTramite.subAgente');
					$datos['permiso']=Session::get('usuarioTramite.permiso');
					$datos['razon_social']= trim(substr(strrchr(Session::get('usuarioTramite.razonSocial'), '-'),2));
					$departamento = $this->controladorLocalidad->buscarDepartamentoLocalidadPorCP_SCP(Session::get('usuarioTramite.codigoPostalAgencia'),Session::get('usuarioTramite.subcodigoPostalAgencia'));
					$datos['departamento']=$departamento['nombre'];
					$datos['departamento_id']=$departamento['id'];
					$datos['localidad_actual']=Session::get('usuarioTramite.localidadAgencia');
					$datos['localidad_actual_id']=Session::get('usuarioTramite.idLocalidad');
					$datosPermisoSuitecrm=$this->servicioTramites->datosPermisoSuitecrm(Session::get('usuarioTramite.permiso'), $datos['tipo_tramite']);
					if($datos['tipo_tramite']==1){
						$datos['referente']=$datosPermisoSuitecrm['referente'];
						$datos['datos_contacto']=$datosPermisoSuitecrm['datos_contacto'];
					}
					if($datos['tipo_tramite']==2){
						$datos['cbu']=$datosPermisoSuitecrm['cbu'];
					}
					$datos['red']=$datosPermisoSuitecrm['red'];
					$datos['nombre_red']=$datosPermisoSuitecrm['nombre_red'];
			}else if(Session::has('tramiteNuevoPermiso')){//trámite para un nuevo permiso
					$agenteBolsa=$this->servicioTramites->obtenerAgenteBolsa();
					$redAgenteBolsa=$this->servicioTramites->obtenerRedAgenteBolsa();
					$domicilioAgenteBolsa=$this->servicioTramites->obtenerDireccionAgenteBolsa();
					$tramiteNuevoPermiso=Session::get('tramiteNuevoPermiso');
					$datos['agente']=$redAgenteBolsa->id_red;
					$datos['subagente']=$tramiteNuevoPermiso['subagente'];
					$datos['permiso']=$tramiteNuevoPermiso['nro_permiso'];
					$datos['razon_social']=$agenteBolsa->agente;

					$departamento =$this->controladorLocalidad->buscarDepartamentoLocalidadPorCP_SCP($domicilioAgenteBolsa->cp, $domicilioAgenteBolsa->scp);
					$datos['departamento']=$departamento['nombre'];
					$datos['departamento_id']=$departamento['id'];

					$localidad = $this->controladorLocalidad->buscarPorCodigoPostal($domicilioAgenteBolsa->cp);
					$datos['localidad_actual']=rtrim($localidad['nombre']);
					$datos['localidad_actual_id']=$localidad['id'];

					if($datos['tipo_tramite']==1){
						$datos['referente']='';
						$datos['datos_contacto']='';
					}
					if($datos['tipo_tramite']==2){
						$datos['cbu']=$agenteBolsa->cbu;
					}
					$datos['red']=$redAgenteBolsa->id_red;
					$datos['nombre_red']=$redAgenteBolsa->nombre_red;
			}else{ //es un agente/subagente
					$datos['agente']=Session::get('usuarioLogueado.agente');
					$datos['subagente']=Session::get('usuarioLogueado.subAgente');
					$datos['permiso']=Session::get('usuarioLogueado.permiso');
					$datos['razon_social']=trim(substr(strrchr(Session::get('usuarioLogueado.razonSocial'), '-'),2));
					$departamento =$this->controladorLocalidad->buscarDepartamentoLocalidadPorCP_SCP(Session::get('usuarioLogueado.codigoPostalAgencia'), Session::get('usuarioLogueado.subcodigoPostalAgencia'));
					$datos['departamento']=$departamento['nombre'];
					$datos['departamento_id']=$departamento['id'];
					$datos['localidad_actual']=Session::get('usuarioLogueado.localidadAgencia');
					$datos['localidad_actual_id']=Session::get('usuarioLogueado.idLocalidad');
					$datosPermisoSuitecrm=$this->servicioTramites->datosPermisoSuitecrm(Session::get('usuarioLogueado.permiso'), $datos['tipo_tramite']);
					if($datos['tipo_tramite']==1){
						$datos['referente']=$datosPermisoSuitecrm['referente'];
						$datos['datos_contacto']=$datosPermisoSuitecrm['datos_contacto'];
					}
					if($datos['tipo_tramite']==2){
						$datos['cbu']=$datosPermisoSuitecrm['cbu'];
					}
					$datos['red']=$datosPermisoSuitecrm['red'];
					$datos['nombre_red']=$datosPermisoSuitecrm['nombre_red'];
			}
		}

		/******************** Departamentos de la provincia de Santa Fe *********************/
		public function departamentos(){
			$controladorProvincia = new ProvinciaController();
			$departamentos = $controladorProvincia->departamentos();

			foreach ($departamentos as $departamento) {
				$combobox[$departamento['id']]=$departamento['descripcion'];
			}
			array_unshift($combobox, " ");
			return $combobox;
		}

		/************************* Verificación existencia nueva red  *****************************************/
		public function control_red(){
			$red = Input::get('nro_red');
			$subagente = Input::get('nro_subagente');
			$modalidad = Input::get('modalidad'); //modalidad 0
			$quieroSer = Input::get('quieroSer'); //1-agente 2-subagente			$razon_social = '';
			
			\Log::info("MM - control_red - red:", array($red));
			\Log::info("MM - control_red - subagente:", array($subagente));
			\Log::info("MM - control_red - modalidad:", array($modalidad));
			\Log::info("MM - control_red - quieroSer:", array($quieroSer));
			
			if(Session::has('usuarioTramite')){ //quien realiza el trámite es la CAS			
					if($quieroSer==1){//pasa de subagente a agente
						$redActual = Session::get('usuarioTramite.agente');
					}else{//es agente
						$redActual = 0;
					}
			}else if(Session::has('tramiteNuevoPermiso')){
				$redActual = 0;
			}else{

				if(Session::get('usuarioLogueado.subAgente')==0){
					$redActual = Session::get('usuarioLogueado.agente');
				}else{//es agente
					$redActual = 0;
				}
			}
			if($red==0){
				$respuesta= Response::json(array('mensaje'=>"No puede dejar en 0 la red."));
			}else if($red==$redActual){
				$respuesta= Response::json(array('mensaje'=>"No puede cambiar a la misma red."));
			}else{
				
				$agencia = $this->controladorAgencias->buscarPorNumeroRed($red);
				
				\Log::info("MM - control_red - quiero ser:", array($quieroSer));
				\Log::info("MM - control_red - agencia:", array($agencia));
				\Log::info("MM - control_red - estado_red:", array($agencia['estado_red']));
				\Log::info("MM - control_red - redActual:", array($redActual));
				
				// if(is_null($agencia)){//quiero ser subagente y no existe la red
					// \Log::info("MM - 0");
					// $respuesta= Response::json(array('mensaje'=>"Red inexistente."));
				// }
				
				if(is_null($agencia) && $redActual != 0){//quiero ser subagente y no existe la red
					\Log::info("MM - 1");
					$respuesta= Response::json(array('mensaje'=>"Red inexistente."));
				}else if(!is_null($agencia) && $agencia['estado_red'] === 'BAJA' && $redActual != 0){
					\Log::info("MM - 2");
					$respuesta= Response::json(array('mensaje'=>"Red no disponible."));
				}else if($redActual == 0 && !is_null($agencia) && $quieroSer == 1){//pasa a ser agente => la red no debe existir
					\Log::info("MM - 3");
					$respuesta= Response::json(array('mensaje'=>"Red no disponible."));
				}else if(!is_null($agencia) && $redActual == 0 && $quieroSer == 2){ //quiero ser subagente
				
					if($agencia['estado_red'] === 'BAJA')
						\Log::info("MM - 4");
						$respuesta= Response::json(array('mensaje'=>"Red no disponible."));

					$nro_subagente = $this->controladorAgencias->mayorSubagenteRed($red, $subagente, $modalidad);
					$respuesta= Response::json(array('razon_social'=>$agencia, 'nro_subagente' => $nro_subagente));
				}else{//quiere ser agente
				\Log::info("MM - 5 - quiere ser agente");
						if(Session::has('usuarioTramite')){
							$partes = explode("- ", Session::get('usuarioTramite.razonSocial'));
							\Log::info("MM - 5 - partes:",array($partes));
							\Log::info("MM - 5 - red:",array($red));
							$agencia['nombre_agencia'] = $red.'/  0 - '.trim($partes[1]);
				}else{
							$agencia['nombre_agencia'] = $red.'/  0 - ';
				}
						$respuesta= Response::json(array('razon_social'=>$agencia, 'nro_subagente' => 0));
				}
			}

			return $respuesta;
		}
			
		/************************* Verificación existencia nueva red  *****************************************/
		public function control_red_m(){
			$red = Input::get('nro_red');
			$subagente = Input::get('nro_subagente');
			$modalidad = Input::get('modalidad'); //modalidad 0
			$quieroSer = Input::get('quieroSer'); //1-agente 2-subagente			$razon_social = '';
			if(Session::has('usuarioTramite')){ //quien realiza el trámite es la CAS
				\Log::info("subagente:", array($subagente));
					if($quieroSer==1){//pasa de subagente a agente
						$redActual = Session::get('usuarioTramite.agente');
					}else{//es agente
						$redActual = 0;
					}
			}else if(Session::has('tramiteNuevoPermiso')){
				$redActual = 0;
			}else{

				if(Session::get('usuarioLogueado.subAgente')==0){
					$redActual = Session::get('usuarioLogueado.agente');
				}else{//es agente
					$redActual = 0;
				}
			}

			if($red==$redActual){
				$respuesta= Response::json(array('mensaje'=>"No puede cambiar a la misma red."));
			}else{
				$agencia = $this->controladorAgencias->buscarPorNumeroRed($red);
				\Log::info("MM - control_red_m - quiero ser:", array($quieroSer));
				\Log::info("MM - control_red_m - agencia:", array($agencia));
				\Log::info("MM - control_red_m - estado_red:", array($agencia['estado_red']));
				\Log::info("MM - control_red_m - redActual:", array($redActual));
				
				if(is_null($agencia)){//quiero ser subagente y no existe la red
					\Log::info("MM - 0");
					$respuesta= Response::json(array('mensaje'=>"Red inexistente."));
				}
				
				if(is_null($agencia) && $redActual != 0){//quiero ser subagente y no existe la red
					\Log::info("MM - 1");
					$respuesta= Response::json(array('mensaje'=>"Red inexistente."));
				}else if(!is_null($agencia) && $agencia['estado_red'] === 'BAJA' && $redActual != 0){
					\Log::info("MM - 2");
					$respuesta= Response::json(array('mensaje'=>"Red no disponible."));
				}else if($redActual == 0 && !is_null($agencia) && $quieroSer == 1){//pasa a ser agente => la red no debe existir
					\Log::info("MM - 3");
					$respuesta= Response::json(array('mensaje'=>"Red no disponible."));
				}else if(!is_null($agencia) && $redActual == 0 && $quieroSer == 2){ //quiero ser subagente
				
					if($agencia['estado_red'] === 'BAJA')
						\Log::info("MM - 4");
						$respuesta= Response::json(array('mensaje'=>"Red no disponible."));

					$nro_subagente = $this->controladorAgencias->mayorSubagenteRed($red, $subagente, $modalidad);
					$respuesta= Response::json(array('razon_social'=>$agencia, 'nro_subagente' => $nro_subagente));
				}else{//quiere ser agente
						if(Session::has('usuarioTramite')):
							$partes = explode("- ", Session::get('usuarioTramite.razonSocial'));
							$agencia['nombre_agencia'] = $red.'/  0 - '.trim($partes[1]);
						endif;
						$respuesta= Response::json(array('razon_social'=>$agencia, 'nro_subagente' => 0));
				}
			}

			return $respuesta;
		}

		/********************************************************************/
		/* Verificación de si dicho agente/subagente/permiso tiene          */
		/* un trámite de ese tipo que no esté aprobado/rechazado/cancelado. */
		/********************************************************************/

		public function verificar_existencia_tramite(){
			$datos = Input::all();
			$noExiste = $this->servicioTramites->verificar_existencia_tramite($datos['agente'],$datos['subagente'], $datos['permiso'],$datos['tipo_tramite']);
			if($noExiste){
				return $respuesta= Response::json(array('mensaje'=>"OK"));
			}else{
				return $respuesta= Response::json(array('mensaje'=>"Fallo"));
			}
		}

		// Resumen tramites - desde tablero
		public function resumenTramites (){

			$url_repositorio = Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
			$url_repositorio .= Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";

			$url_formato = "formato=".Config::get('habilitacion_config/jasper_config.formatos_reportes.1');

			$url_repositorio  = $url_repositorio . Config::get('habilitacion_config/jasper_config.tablero_tramites.url_reportes.8')."&" . $url_formato;

			return View::make('tramites.resumenTramites', array('url_repositorio'=>$url_repositorio));
		}

		/****************************************************************************
		 * Adjuntar archivos
		 ****************************************************************************/
		public function adjuntarArchivos(){
			\Log::info("MM - adjuntarArchivos - entra:");
			$tipo_tramite = strtoupper(Input::get('tipo_tramite'));
			$permiso = Input::get('permiso');
			$tipo_documento = Input::get('tipo_documento');
			
			\Log::info("MM - adjuntarArchivos - tipo_tramite:", array($tipo_tramite));
			\Log::info("MM - adjuntarArchivos - permiso:", array($permiso));
			\Log::info("MM - adjuntarArchivos - tipo_documento:", array($tipo_documento));
			
			// $tipo_documento = 2;
			if(is_null($tipo_documento)){
				  $tipo_documento = '11';
			}
			
			if($tipo_documento == '0'){
				   return Response::json('error', 400);
			}
			
			\Log::info("MM - adjuntarArchivos - tipo_documento:", array($tipo_documento));
			
			$file = Input::file('file');

			$rules = array(
			   'file' => 'required|max:20000',
			   'extension' => 'required|in:odt,png,gif,jpeg,jpg,txt,pdf,doc,rtf,docx,ods,bmp,rar,zip,xls',
			);

			$validator = \Validator::make(array('file'=> $file, 'extension'=>\Str::lower($file->getClientOriginalExtension())), $rules);

			if($validator->passes()){
				$ds = DIRECTORY_SEPARATOR;
				if(Input::has('nro_tramite')){
					$nro_t=Input::get('nro_tramite');
					$destinationPath = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$nro_t.'_'.$permiso.$ds.'ADJUNTOS';
				}else{
					$destinationPath = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$permiso.$ds.'ADJUNTOS';
				}

				if(!is_dir($destinationPath) && !file_exists($destinationPath)){
				  File::makeDirectory($destinationPath, 0777, true);
				}

				$filename = strtoupper(str_replace(array("á","é","í","ó","ú","ñ","Á","É","Í","Ó","Ú","Ñ"),
										 array("a","e","i","o","u","n","A","E","I","O","U","N"), $file->getClientOriginalName()));//strtoupper($file->getClientOriginalName());
				
				// crear servicio que trae el prefijo a partir del valor
				$tipoDocusPref = $this->servicioTipoDocumento->buscarPorId($tipo_documento); 
				
				\Log::info("MM - adjuntarArchivos - tipoDocusPref:", array($tipoDocusPref));
				// reemplazar el valor por el prefijo 
				$pref_tipDoc_archivo = $tipoDocusPref['pref_nom_archivo_serv'].'_'.$filename;
				
				\Log::info("MM - adjuntarArchivos - nuevo nombreArchivo - pref_tipDoc_archivo", array($pref_tipDoc_archivo));
				// $uploadSuccess_new = $file->move($destinationPath, $pref_tipDoc.$filename);	

				
				$iddelprefijo = $this->servicioTipoDocumento->buscarPorPrefijo($tipoDocusPref['pref_nom_archivo_serv']); 				 
				\Log::info("MM - adjuntarArchivos - iddelprefijo:", array($iddelprefijo));
				\Log::info("MM - adjuntarArchivos - iddelprefijo2:", array($iddelprefijo[0]->idTipoDoc));
				
				// $uploadSuccess = $file->move($destinationPath, $filename);
				$uploadSuccess = $file->move($destinationPath, $pref_tipDoc_archivo);
				\Log::info("MM - adjuntarArchivos - el archivo queda en:", array($destinationPath.'/'.$pref_tipDoc_archivo));
				if( $uploadSuccess ) {
					return Response::json('success', 200);
				} else {
					return Response::json('error', 400);
				}
			 } else {
					\Log::info('No pasó el validador:'.$file->getMimeType() );
					\Log::info($validator->messages());
					return Response::json('error','No se puede adjuntar el archivo.');
				}
		}
		
		/****************************************************************************
		 * Adjuntar archivos
		 ****************************************************************************/
		public function adjuntarArchivos_ant(){
			$tipo_tramite = strtoupper(Input::get('tipo_tramite'));
			$permiso = Input::get('permiso');
			$file = Input::file('file');

			$rules = array(
			   'file' => 'required|max:20000',
			   'extension' => 'required|in:odt,png,gif,jpeg,jpg,txt,pdf,doc,rtf,docx,ods,bmp,rar,zip,xls',
			);

			$validator = \Validator::make(array('file'=> $file, 'extension'=>\Str::lower($file->getClientOriginalExtension())), $rules);

			if($validator->passes()){
				$ds = DIRECTORY_SEPARATOR;
				if(Input::has('nro_tramite')){
					$nro_t=Input::get('nro_tramite');
					$destinationPath = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$nro_t.'_'.$permiso.$ds.$ds.'ADJUNTOS';
				}else{
					$destinationPath = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$permiso.$ds.$ds.'ADJUNTOS';
				}

				if(!is_dir($destinationPath) && !file_exists($destinationPath)){
				  File::makeDirectory($destinationPath, 0777, true);
				}

				$filename = strtoupper(str_replace(array("á","é","í","ó","ú","ñ","Á","É","Í","Ó","Ú","Ñ"),
										 array("a","e","i","o","u","n","A","E","I","O","U","N"), $file->getClientOriginalName()));//strtoupper($file->getClientOriginalName());
				$uploadSuccess = $file->move($destinationPath, $filename);

				if( $uploadSuccess ) {
					return Response::json('success', 200);
				} else {
					return Response::json('error', 400);
				}
			 } else {
					\Log::info('No pasó el validador:'.$file->getMimeType() );
					\Log::info($validator->messages());
					return Response::json('error','No se puede adjuntar el archivo.');
				}
		}

		public function eliminarAdjunto(){
			$tipo_tramite = strtoupper(Input::get('tipo_tramite'));
			$file = Input::get('file');
			$permiso = Input::get('permiso');
			$tipoDocumento = Input::get('tipoDocumento');
			\log::info("MM - eliminarAdjunto - tipoDocumento",array($tipoDocumento));
			
			if(Input::has('nro_tramite')){
				if($tipoDocumento == 0 || is_null($tipoDocumento)) {
					$prefijo_armado= '';
				}else{
					$tipoDocusPref = $this->servicioTipoDocumento->buscarPorId($tipoDocumento);  
					$prefijo_armado= $tipoDocusPref['pref_nom_archivo_serv'].'_';
				}	
			}else{
				if($tipoDocumento == 0 || is_null($tipoDocumento)) {
					$prefijo_armado= '';
				}else{
					$tipoDocusPref = $this->servicioTipoDocumento->buscarPorId($tipoDocumento);  
					$prefijo_armado= $tipoDocusPref['pref_nom_archivo_serv'].'_';
					
				}	
			}
			// \log::info("MM - eliminarAdjunto - tipoDocusPref",array($tipoDocusPref));
			// \log::info("MM - eliminarAdjunto - tipoDocusPref pref_nom_archivo_serv",array($tipoDocusPref['pref_nom_archivo_serv']));
			// \log::info("MM - eliminarAdjunto - tipoDocusPref 3",array($tipoDocusPref[3]));
			
			$ds = DIRECTORY_SEPARATOR;
			if(Input::has('nro_tramite')){
				$nro_tramite=Input::get('nro_tramite');
				$dir_base = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$nro_tramite.'_'.$permiso;
				$directorio = $dir_base.$ds.'ADJUNTOS'.$ds;
				// if($tipoDocumento != 0) {
					$destinationPath = $directorio.strtoupper($prefijo_armado.$file);
				// }{
					// $destinationPath = $directorio.strtoupper($file);
				// }	
			}else{
				$dir_base =public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$permiso;
				$directorio = $dir_base.$ds.'ADJUNTOS'.$ds;
				$destinationPath = $directorio.strtoupper($prefijo_armado.$file);
			}
			
			// enviar eliminacion de registro.
			
			
			// destinationPath ["/var/www/cas/CAS_Habilitaciones/public/upload/CD_56045/ADJUNTOS//Slide_contador.jpg"] []
			// $tipoDocusPref = $this->servicioTipoDocumento->buscarPorId($tipoDocumento); 
			 // $tipoDocusPref = $this->servicioTipoDocumento->buscarPorId($tipoDocumento); 
			\log::info("MM - eliminarAdjunto - destinationPath: ",array($destinationPath));
			if(\File::exists($destinationPath)){
				$deleteSuccess = File::delete($destinationPath);

				if( $deleteSuccess ) {

					if(count(File::files($directorio))==0){
						rmdir($directorio);
					}
					if(count(glob($dir_base.$ds."*")) === 0 ) { // vacio
						rmdir($dir_base);
					}
					return Response::json('success', 200);
				} else {
					return Response::json('error', 400);
				}
			 } else {
				\Log::error("Problema al eliminar adjunto.");
				return Response::json('error',400);
			 }
		}

		public function adjuntosActuales(){
			$tipo_tramite =strtoupper(Input::get('tipo_tramite'));
			$nroTramite = Input::get('nro_tramite');
			$permiso = Input::get('permiso');
			$ds = DIRECTORY_SEPARATOR;
			$directorio = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$nroTramite.'_'.$permiso.$ds.$ds.'ADJUNTOS';

			if (\File::isDirectory($directorio)){//si existe el directorio
				$files = File::allFiles($directorio);
				foreach ($files as $file)
				{

					$archivo['name']=$file->getFilename();
					$archivo['size']= $file->getSize();
					$archivo['ruta']='upload'.$ds.$tipo_tramite.'_'.$nroTramite.'_'.$permiso.$ds.'ADJUNTOS'.$ds.$file->getFilename();
					$result[]=$archivo;

				}

				return Response::json(array($result));
			}else {
				return Response::json(array('success' => false));
				}
		}
		/**
		 * Función para borrar la carpeta de adjuntos cdo no guarda el trámite
		 */
		public function borrar_carpeta_adjuntos(){
			$tipo_tramite =str_replace("", "_", strtoupper(Input::get('tipo_tramite')));
			$permiso = Input::get('permiso');
			$ds=DIRECTORY_SEPARATOR;
			$dir_base = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$permiso;
			$directorio = $dir_base.$ds.'ADJUNTOS';

			if (\File::isDirectory($directorio)){//si existe el directorio
				foreach (glob($directorio.$ds."*.*") as $file){
					unlink($file);
				}
				rmdir($directorio);
				rmdir($dir_base);
				return Response::json(array('ok'));
			}else {
				return Response::json(array('error'));
			}
		}

		public function descargar_adjunto(){
			Session::forget('archivo_descarga');
			$datos_archivo['tipo_tramite'] =strtoupper(Input::get('tipo_tramite'));
			$datos_archivo['nombre_archivo'] = strtoupper(str_replace(array("á","é","í","ó","ú","ñ","Á","É","Í","Ó","Ú","Ñ"),
										 array("a","e","i","o","u","n","A","E","I","O","U","N"), Input::get('nombre_archivo')));
			$datos_archivo['num_tramite'] = Input::get('num_tramite');
			$datos_archivo['permiso'] = Input::get('permiso');
			Session::put('archivo_descarga',$datos_archivo);
			return Response::json(array('OK'));
		}

		public function descarga(){

			if(Session::has('archivo_descarga')){
				$datos_archivo = Session::get('archivo_descarga');
				$tipo_tramite =$datos_archivo['tipo_tramite'];
				$nombre_archivo = $datos_archivo['nombre_archivo'];
				$num_tramite = $datos_archivo['num_tramite'];
				$permiso = $datos_archivo['permiso'];
				$ds =DIRECTORY_SEPARATOR;

				$directorio = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$num_tramite.'_'.$permiso.$ds.'ADJUNTOS';

				if (\File::isDirectory($directorio)){//si existe el directorio
					$path_archivo = $directorio.$ds.$nombre_archivo;

					$archivos = File::allFiles($directorio);
					foreach ($archivos as $archivo)
					{
						if(strcasecmp($archivo->getFilename(),$nombre_archivo)==0){

							//return Response::download($archivo, $archivo->getFilename());
							$file = new Symfony\Component\HttpFoundation\File\File($path_archivo);
							$mime = $file->getMimeType();
							$headers = array('Content-Type' => $mime);
							 header("Content-Disposition: attachment; filename=".$nombre_archivo);
							 header("Content-Transfer-Encoding: binary");
							 //header("Content-Length: " . $size);
							$response = Response::download($archivo, NULL, $headers);
							ob_end_clean();
							flush();
							return $response;
						}
					}
				}
			}//no existe archivo descarga
		}//fin descarga

/***********************************************Para todos los trámites*************************/
		/**
		* Función que se encarga de generar la ruta al
		* reporte del historial en formato pdf
		*/
		public function historial_pdf(){
			Session::forget('url_historial_pdf');
			$nro_tramite = Input::get('nro_tramite');
			//armado de la url para el reporte
			$url_repositorio = Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
			$url_repositorio .= Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.historial_pdf')."&";
			$url_repositorio .="formato=".Config::get('habilitacion_config/jasper_config.formatos_reportes.2')."&";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_historial.nombres.0')."=".$nro_tramite;

			Session::put('url_historial_pdf',$url_repositorio);
			return Response::json(array('mensaje'=>'mensaje'));

		}

		public function show_historial_pdf(){
			$url_pdf = Session::get('url_historial_pdf');

			return View::make('reportes.reportes_tramites', array('url_repositorio'=>$url_pdf));
		}

/**************************ADMINISTRACIÓN MÁQUINAS**************************/

		/**
		* Arma el combo para las acciones sobre máquinas
		**/
		public function adminMaquinas(){
			$usuarioLogueado=Session::get('usuarioLogueado');
			//si tiene permiso para incorporar/retirar máq.
			$tiposTramites = $this->servicioTipoTramites->buscarTiposTramitesMaquinas();

			foreach($tiposTramites as $tipoTramite){
				$combobox[$tipoTramite['id_tipo_tramite']] = $tipoTramite['nombre_tramite'];
			}

			//permiso suspendido - elimino incorporar máquina
			if($usuarioLogueado['nombreTipoUsuario']=='CAS' || $usuarioLogueado['nombreTipoUsuario']=='CAS_ROS'){
				$usuarioTramite = Session::get('usuarioTramite');
				if($usuarioTramite['estado_comercializacion']=='suspendido'){
					unset($combobox['6']);//6-Incorporación de máquinas
				}
			}else{
				if($usuarioLogueado['estado_comercializacion']=='suspendido'){
					unset($combobox['6']);//6-Incorporación de máquinas
				}
			}


			$lista=$this->servicioTramites->listaMotivosBaja();
			$listaMotivos="";
			foreach($lista as $clave=>$valor){
				$listaMotivos.=$clave.":".$valor.",";
			}
			$listaMotivos=rtrim($listaMotivos, ",");

			return View::make('tramites.tramites', array("combobox" => $combobox, "listaMotivos"=>$listaMotivos, 'titulo_maquinas'=>"Administración de máquinas"));
		}

		/**
		* Busca los tipos de terminal que se puden incorporar
		**/
		public function buscarTiposTerminal(){
			$tipos=$this->servicioTramites->buscarTiposTerminal();
			return Response::json(array('tipos'=>$tipos));
		}

		/**
		* Busca las terminales del permiso
		**/
		public function buscarTerminal(){
			$permiso=Input::get('permiso');
			$lista=$this->servicioTramites->buscarTerminal($permiso);
			return Response::json(array('lista'=>$lista));
		}

		/**
		* Método que obtiene de la vista el tipo de la máquina a incorporar
		* al permiso.
		**/
		public function incorporarMaquina_add(){
			$usuario = Session::get('usuarioLogueado');
			$permiso=Input::get('permiso');
			$agente=Input::get('agente');
			$subagente=Input::get('subagente');
			$tipo_terminal=Input::get('dato');
			$observaciones=trim(Input::get('observaciones'));
			$usuarioAsigna=Session::get('usuarioLogueado.id');
			try{
				$datos_email=$this->servicioTramites->incorporarMaquina($usuario['nombreTipoUsuario'], $tipo_terminal, $observaciones);
				\Log::info("MM - incorporarMaquina_add - antes de enviar_email_incoretmaq ");

				$this->enviar_email_incoretmaq($datos_email);
				\Log::info("MM - incorporarMaquina_add - termine de mandar los mails");
				$ok='true';
				return Response::json(array('exito'=>$ok));
			}catch(\Exception $e){
				return Response::json(array('exito'=>'false'));
			}

		}

		public function retirarMaquina_add(){
			$permiso = Session::get('usuarioTramite.permiso');
			$agente = Input::get('agente');
			$subagente = Input::get('subagente');
			$maquina = Input::get('dato');
			$maq_telefono=Input::get('telefono');
			$salto="\n";
			$observaciones =$salto."Teléfono: ". $maq_telefono .$salto. (trim(Input::get('observaciones')));
			$definitivo = trim(Input::get('definitivo')) == "true";
			$usuarioModifica = Session::get('usuarioLogueado.id');
			//FIXME: agregar definitivo como campo independiente
			$observaciones = "Retiro " . ($definitivo? "Definitivo - ": "Provisorio  ") . $observaciones;
			try{
				$puedeRetirar = $this->servicioTramites->puedeRetirar($permiso);
				if($puedeRetirar){
					$datos_email = $this->servicioTramites->retirarMaquina($maquina, $observaciones, $definitivo);
					if(isset($datos_email['nroTramite'])){
						$this->enviar_email_incoretmaq($datos_email);
						$ok = 'true';
						return Response::json(array('exito' => $ok));
					}else{
						$ok = 'false';
						return Response::json(array(
							'exito' => 'false',
							'mensaje' => 'Se registro un inconveniente al crear trámite. Intente nuevamente en unos minutos, y si el mismo persiste, contacte a soporte.'
						));
					}
				}else{
					$ok = 'false';
					return Response::json(array('exito' => $ok, 'mensaje' => 'Antes debe dar de baja el permiso.'));
				}


			}catch(\Exception $e){
				return Response::json(array('exito'=>'false'));
			}
		}

/*******************Administración de Permisos*******************/

		/**
		* Función que obtiene del suite los datos ficticios
		**/
		public function datosBaseAltaPermiso(){
			if(Session::has('agenteBolsa') && Session::has('direccionAgenteBolsa') && Session::has('redAgenteBolsa')){
				return Response::json(array('exito'=>'true'));
			}
			$agenteBolsa=$this->servicioTramites->obtenerAgenteBolsa();
			$direccionAgenteBolsa=$this->servicioTramites->obtenerDireccionAgenteBolsa();
			$redAgenteBolsa=$this->servicioTramites->obtenerRedAgenteBolsa();

			Session::put('agenteBolsa',$agenteBolsa);
			Session::put('direccionAgenteBolsa',$direccionAgenteBolsa);
			Session::put('redAgenteBolsa',$redAgenteBolsa);
			return Response::json(array('exito'=>'true'));
		}

		/**
		* Función que da de alta los permisos
		**/
		public function altaPermisos_add(){
			$cant = Input::get('cant');
			$resol = Input::get('resol');
			$fecha = Input::get('fecha');
			$usuario=Session::get('usuarioLogueado');
			$ok=$this->servicioTramites->altaPermisos($cant, $resol, $fecha, $usuario);
			return Response::json(array('mensaje'=>$ok));
		}

		/**
		* Función que verifica si tiene permiso para acceder a la vista
		* de baja de permisos.
		**/
		public function bajaPermisosOk(){
			$usuario=Session::get('usuarioLogueado');
			if(in_array("Inicio_tramites", $usuario['listaFunciones'])){
				return Response::json(array('url'=>'ingreso', 'exito'=>'true'));
			}else{
				return Response::json(array('url'=>'', 'exito'=>'false'));
				//return Redirect::back()->withMessage('No tiene permiso');
			}
		}

		/**
		* Función que redirecciona a la vista de ingreso de trámites CAS
		* para
		**/
		public function vistaIngresoTramitesInternos(){
			$tipoTramite = $this->servicioTipoTramites->buscarPorId(5);

			$combobox[$tipoTramite['id_tipo_tramite']] = $tipoTramite['nombre_tramite'];
			$lista=$this->servicioTramites->listaMotivosBaja();
			$listaMotivos="";
			foreach($lista as $clave=>$valor){
				$listaMotivos.=$clave.":".$valor.",";
			}
			$listaMotivos=rtrim($listaMotivos, ",");
			return View::make('tramites.tramites', array('combobox'=>$combobox, 'listaMotivos'=>$listaMotivos));
		}

		/**
		* Función que redirecciona a la vista de ingreso de trámites CAS
		* para
		**/
		public function vistaIngresoTramitesInternosRenuncia(){
			$tipoTramite = $this->servicioTipoTramites->buscarPorId(13);

			$combobox[$tipoTramite['id_tipo_tramite']] = $tipoTramite['nombre_tramite'];
			$lista=$this->servicioTramites->listaMotivosBaja();
			$listaMotivos="";
			foreach($lista as $clave=>$valor){
				$listaMotivos.=$clave.":".$valor.",";
			}
			$listaMotivos=rtrim($listaMotivos, ",");
			return View::make('tramites.tramites', array('combobox'=>$combobox, 'listaMotivos'=>$listaMotivos));
		}

		/*************************************************************************
		* Función para controlar que no se inicie dos veces la solicitud de baja *
		**************************************************************************/
		public function controlBajaPermiso(){
			$permiso=Input::get('permiso');
			$ok=$this->servicioTramites->existeSolicitudBaja($permiso);
			return Response::json(array('exito'=>$ok));
		}

		/*************************************************************************
		* Función para controlar que no se inicie dos veces la solicitud de baja *
		**************************************************************************/
		public function controlRenunciaPermiso(){
			$permiso=Input::get('permiso');
			$ok=$this->servicioTramites->existeSolicitudRenuncia($permiso);
			return Response::json(array('exito'=>$ok));
		}
		/******************************************************
		* Función que inicia la solicitud de baja del permiso *
		*******************************************************/
		public function bajaPermiso(){
			$datos = Input::all();
			$permiso=Input::get('permiso');
			$motivo=Input::get('motivo');
			$observaciones=Input::get('observaciones');
			$agente=Input::get('agente');
			$subagente=Input::get('subagente');
			$usuarioTramite=Session::get('usuarioTramite');
			$ok=$this->servicioTramites->solicitudBaja($permiso,$motivo,$observaciones,$usuarioTramite);
			$datos_mail['tipoTramite']=5;
			$datos_mail['nroPermiso']=$permiso;
			$datos_mail['nroTramite']=$ok->nroTramite;
			$datos_mail['agente']=$agente;
			$datos_mail['subagente']=$subagente;

			$this->enviar_email_alta_baja_permiso($datos_mail);
			if($ok){
				return Response::json(array('exito'=>'true'));
			}else{
				return Response::json(array('exito'=>'false'));
				//return Redirect::back()->withMessage('No tiene permiso');
			}
		}

		/******************************************************
		* Función que inicia la solicitud de baja del permiso *
		*******************************************************/
		public function renunciaPermiso(){
			$datos = Input::all();

			$permiso=Input::get('permiso');
			$motivo=Input::get('motivo');
			$observaciones=$datos['observaciones'];// Input::get('observaciones_renuncia');
			$agente=Input::get('agente');
			$subagente=Input::get('subagente');
			\log::info("MM - renunciaPermiso - agente",array($agente));
			\log::info("MM - renunciaPermiso - subagente",array($subagente));
			$usuarioTramite=Session::get('usuarioTramite');
			$ok=$this->servicioTramites->solicitudRenuncia($permiso,$motivo,$observaciones,$usuarioTramite);
			$datos_mail['tipoTramite']=13;
			$datos_mail['nroPermiso']=$permiso;
			$datos_mail['nroTramite']=$ok->nroTramite;
			$datos_mail['agente']=$agente;
			$datos_mail['subagente']=$subagente;

			$this->enviar_email_alta_baja_permiso($datos_mail);
			if($ok){
				return Response::json(array('exito'=>'true'));
			}else{
				return Response::json(array('exito'=>'false'));
				//return Redirect::back()->withMessage('No tiene permiso');
			}
		}

		/**********************************************************************
		* Función que arma la grilla de permisos que aún no se han adjudicado *
		***********************************************************************/
		public function grillaPermisosSinAdjudicar(){
			Session::forget('permisosSinAdjudicar');
			//filtros
			$filtros['tipoOrden']='ASC';
			$filtros['pagina']=Input::get('nro_pagina',1);
			$filtros['porPagina']='6';
			$filtros['fechaInicio']='';
			// Obtiene los tramites con los filtros
			$resultado = $this->servicioTramites->permisosSinAdjudicar($filtros);

			$listaPermisos = $resultado;
			$exito=1;
			if(sizeof($listaPermisos)==0){
				$mensaje="No existen permisos que cumplan las condiciones de búsqueda.";
				$exito=0;
			}
			$pagina = $resultado['pagina'];
			$cantidadPaginas=$resultado['cantidadPaginas'];
			$mensaje="Lista de permisos generada correctamente.";
			Session::put('permisosSinAdjudicar', $listaPermisos);
			$respuesta = Response::json(array('exito'=>$exito,'permisos'=>$listaPermisos, 'mensaje'=>$mensaje, 'pagina'=>$pagina, 'cantidadPaginas'=>$cantidadPaginas));

			return $respuesta;

		}

		/***************************************
		* Grilla CSV de permisos sin adjudicar *
		****************************************/
		public function permisosSinAdjudicarCSV(){
			$datos = Session::get('permisosSinAdjudicar');

			foreach ($datos['permisos'] as $permiso) {

				$tablita[]=array('nro_permiso'=>$permiso->numero,
								 'fecha_inicio'=>$permiso->fecha_inicio,
								 'estado'=>$permiso->estado,
								 'usuario_generador'=>$permiso->usuario_generador,
								);
			}

			$output="Nº Permiso\tFecha Inicio\tEstado\tUsuario Generador\t";
			$output.="\n";
			foreach ($tablita as $row) {
			  $output.=  implode("\t",$row)."\n";
			}

			$headers = array(
			  'Content-Type' => 'text/csv',
			  'Charset'=>'UTF-8',
			  'Content-Disposition' => 'attachment; filename="GrillaConsultaPermisosSinAdjudicar.csv"',
			);

			return Response::make($output, 200, $headers);

		}

		/***************************************
		*           Adjudicar permisos         *
		****************************************/
		public function adjudicarPermiso(){
			$nroPermiso = Input::get('id_seleccionado');
			$tipoAdjudicacion = Input::get('tipo_adjudicacion');
			$ok=$this->servicioTramites->adjudicacionPermiso($nroPermiso, $tipoAdjudicacion);
			$datos_mail['tipoTramite']=6;
			$datos_mail['nroPermiso']=$nroPermiso;
			//$this->enviar_email_alta_baja_permiso($datos_mail);-->no se usa porque se necesita el envío sólo cdo los trámites se finalizaron
			return Response::json(array('exito'=>$ok));

		}

		/**********************************************************************
		* Función que busca el trámite para el nuevo permiso y redirecciona   *
		* a la vista de trámite correspondiente para que se puedan completar  *
		* los datos.                                                          *
		***********************************************************************/
		public function completarTramite(){
			\Log::info('paso por completarTramite');
			$seleccionado = Input::get('seleccionado');
			$tramite =$this->servicioTramites->buscarPorId($seleccionado);
			$tipo_tramite = $tramite['id_tipo_tramite'];

			//if(Session::has('tramiteNuevoPermiso')){
				Session::forget('tramiteNuevoPermiso');
				Session::forget('permisoNuevoPermiso');
				//}
			//guardo nº trámite para el nuevo permiso
			Session::put('tramiteNuevoPermiso',$tramite);
			Session::put('permisoNuevoPermiso',$tramite['nro_permiso']);


			switch ($tipo_tramite) {
						case 1://cambio domicilio
							return Redirect::to('/cambio-domicilio');
							break;
						case 2://cambio categoria
							return Redirect::to('/cambio-categoria');
							break;
						case 3://cambio Titular
							return Redirect::to('/cambio-titular');
							break;
						case 4://cambio dependencia
							return Redirect::to('/cambio-dependencia');
							break;

				default:
					return Redirect::back();
					break;
			}
		}

		public function informePermisosSinAdjudicar(){
			Session::forget('url_repositorio');
			$usuarioLogueado=Session::get('usuarioLogueado');

			//armado de la url para el reporte
			$url_repositorio = Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
			$url_repositorio .= Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.reporte_consulta_permisos_sin_adjudicar')."&";
			$url_repositorio .="formato=".Config::get('habilitacion_config/jasper_config.formatos_reportes.2');

			Session::put('url_repositorio',$url_repositorio);
			return Response::json(array('mensaje'=>'mensaje'));
		}

		public function informePermisosSinAdjudicarPDF(){
			$url_repositorio = Session::get('url_repositorio');

			return View::make('reportes.reportes_tramites', array('url_repositorio'=>$url_repositorio));
		}

		public function verificarCorreo(){
			$correo = Input::get('email');
			$permiso = Input::get('permiso');
			$mensaje = $this->servicioTramites->verificarCorreo($correo, $permiso);

			return Response::json(array('mensaje'=>$mensaje));
		}

	/***************************Incorporación  Cotitular **********************************************/

		/********************************************/
		/*	inicio incoporación de Cotitular		*/
		/********************************************/
		public function incorporarCoTitular(){
			Session::forget('ultimoTramite');
			$datos['tipo_tramite']=8;
			$tipo = 8 ;
			self::datosComunesVistas($datos);

			//antes 1-física 2-juridica
			$tipo_persona = $this->servicioTramites->listaTiposPersonas();
			$tipo_documento = $this->servicioTramites->listaTiposDocumentos();

			$tipo_sociedad = $this->servicioTramites->listaTiposSociedad();
			$tipo_situacion = $this->servicioTramites->listaSituacionGanancias();
			$tipo_ocup_titu = $this->servicioTramites->listaOcupacionPersonas();
			$alta = 0;
			$titulo=$this->servicioTipoTramites->tituloTramite(8,$alta);

			$tiposDocumentos = $this->servicioTipoDocumento->buscarTodos(); 
			\Log::info('AE - incorporarCoTitular - tiposDocumentos: ',array($tiposDocumentos));
			// $lista=$this->servicioTramites->listaMotivosBaja();
			$listaDocumentos=array();
			$listaDocumentos[0]='Ingrese Tipo Documento'; // Valor 0 - por defecto y validad en js para que si o si seleccione el tipo de documento
			foreach($tiposDocumentos as $valor){
				$listaDocumentos[$valor['id']]=$valor['nombre'];
			}

			$usuario=Session::get('usuarioLogueado');
			$bloqIngreso = 0;
			if($usuario['nombreTipoUsuario']=='Agente')
				$bloqIngreso = 1;

			if(count(Input::old())==0){//para cuando vuelve porque el validador encontró algo erróneo
				Session::flashInput($datos);
				$usuario=Session::get('usuarioLogueado');

				if(($usuario['nombreTipoUsuario']!='CAS' && $usuario['nombreTipoUsuario']!='CAS_ROS') && !Session::has('tramiteNuevoPermiso')){
					$permiso = Session::get('usuarioTramite')['permiso'];
					$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 8);
				}else{
					$margen=$this->servicioHistorico->fechaUltimoTramite($usuario['permiso'], 8);
			}

				$meses=$margen['meses'];
				$dias=$margen['dias'];
				$paso_fecha=$margen['paso_fecha'];
				$cant = $margen['cant'];
				Session::put('ultimoTramite',$margen);

				return View::make(
					'tramites.cambioTitular',
					array('titulo'=>$titulo,
					'tipo'=>$tipo,
					'tipo_persona'=>$tipo_persona,
					'meses'=>$meses,
					'dias'=>$dias,
					'paso_fecha'=>$paso_fecha,
					'cant'=>$cant,
					'tipo_sociedad'=>$tipo_sociedad,
					'tipo_situacion'=>$tipo_situacion,
					'tipo_doc'=>$tipo_documento,
					'tipo_ocup_tit'=>$tipo_ocup_titu,
					'bloqIngreso'=>$bloqIngreso,
					'listaDocumentos'=>$listaDocumentos));
			}
			$usuario=Session::get('usuarioLogueado');
			if($usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS'){
				$permiso = Session::get('usuarioTramite')['permiso'];
				$margen=$this->servicioHistorico->fechaUltimoTramite($permiso, 8);
			}else{
				$margen=$this->servicioHistorico->fechaUltimoTramite($usuario['permiso'], 8);
			}

			$meses=$margen['meses'];
			$dias=$margen['dias'];
			$paso_fecha=$margen['paso_fecha'];
			$cant = $margen['cant'];

			Session::put('ultimoTramite',$margen);


			return View::make(
				'tramites.cambioTitular',
				array('titulo'=>$titulo,
				'tipo'=>$tipo,
				'tipo_persona'=>$tipo_persona,
				'meses'=>$meses,
				'dias'=>$dias,
				'paso_fecha'=>$paso_fecha,
				'cant'=>$cant,
				'tipo_sociedad'=>$tipo_sociedad,
				'tipo_situacion'=>$tipo_situacion,
				'tipo_doc'=>$tipo_documento,
				'tipo_ocup_tit'=>$tipo_ocup_titu,
				'bloqIngreso'=>$bloqIngreso,
				'listaDocumentos'=>$listaDocumentos));
		}

		/****************************/
		/* ingreso de titular  		*/
		/*	EM-29/04/2014			*/
		/****************************/

		public function incorporarCoTitular_add(){
			$datos = Input::all();

			$reglas=array(
				'agente'=>'required|integer',
				'subagente'=>'required|integer',
				'permiso'=>'required|integer',
				'departamento_id'=>'required|integer',
				'localidad_actual_id'=>'required|integer',
				'domicilio'=>'required',
				'nueva_localidad'=>'required|integer',
				'referente'=>'required',
				'datos_contacto'=>'required',
				'motivo_ct'=>'required',
			);

			if($datos['tipo_persona']=="J"){
				$reglas['tipo_persona']='required|in:F,J|TipoPersona:'.$datos['cuit'].',J';
				$reglas['cuit']='required|Cuit';
				$reglas['tipo_sociedad']='required';
				$reglas['razon_social']='required';
			}else{//persona física
				$reglas['sexo_persona']='required|in:F,M,S';//S=Sin especificar
				if($datos['cuit']!=''){
					$reglas['tipo_persona']='required|in:F,J|TipoPersona:'.$datos['cuit'].','.$datos['sexo_persona'];
				}else{
					$reglas['tipo_persona']='required|in:F,J';
				}

				$reglas['apellido_nombre']='required';
				$reglas['tipo_doc']='required';
				$reglas['nro_doc']='required|Documento:'.$datos['nro_doc'];
			}

/* Comentado ADEN - 2024-09-03
			//IIBB
			if($datos['ingresos']!='')
				$reglas['ingresos']='IngresosBrutos:'.$datos['ingresos'];
*/
			//mensajes de validación
			$mensaje=array(
				"required"=>"Campo requerido",
				"integer"=>"Solo nº"
			);
			$validar=Validator::make($datos,$reglas,$mensaje);

			if($validar->fails()){
				\Log::info($validar->messages());
				return Redirect::to('cambio-titular')->withErrors($validar)->withInput();
			}else{
				$localidad = $this->controladorLocalidad->buscarPorIdLocalidad($datos['nueva_localidad']);
				if(is_null($localidad)){
					return Redirect::to('cambio-titular')->withErrors($validar)->withInput();
				}
				$departamento = $this->controladorLocalidad->buscarDepartamentoLocalidad($datos['nueva_localidad']);
				if(is_null($departamento)){
					return Redirect::to('cambio-titular')->withErrors($validar)->withInput();
				}
			}

			unset($datos_email);
			$datos_email=array();

			$datos['departamento_id'] = $departamento['id'];
			$datos['nuevo_departamento'] = $departamento['nombre'];
			$tipoTramite = 8;
			$mensaje = $this->servicioTramites->cargarTitular($datos, $datos_email, $tipoTramite);

			if(is_null($mensaje)){
				return Response::json(array('mensaje'=>"Falló el incio del trámite Incorporación de cotitular."));
			}

			Session::flash('mensaje', $mensaje);

			if(Session::has('usuarioTramite'))
				$datos_email['email_duenio'] = Session::get('usuarioTramite')['email'];
			else
				$datos_email['email_duenio'] = Session::get('usuarioLogueado')['email'];

			Session::put('datos_email',$datos_email);
			Session::forget('usuarioTramite');
			Session::forget('tramiteGuia');

			return Redirect::to('exito-tramite');

		}
				
		/********************************************/
		/* Fin incorporación de cotitular */
		/********************************************/

		/********************************************/
		/* Baja cotitular */
		/********************************************/
		public function bajaCotitular(){
			/* preambulo */
			Session::forget('ultimoTramite');
			$tipoTramite = 11;
			$datos['tipo_tramite'] = $tipoTramite;
			self::datosComunesVistas($datos);
			
			$titulo = $this->servicioTipoTramites->tituloTramite($tipoTramite, 0);
			\Log::info("MM - tramitesController - titulo: ",array($titulo)); 
			
			$permiso = $this->obtenerPermiso();
			\Log::info("MM - tramitesController - titulo: ",array($titulo)); 
			/* fin preambulo */

			/* obtener listado de cotitulares */
			\Log::info("AE - tramitesController - bajaCotitular - permiso: ",array($permiso)); 
			$cotitulares = $this->servicioTramites->obtenerCotitulares($permiso);
			\Log::info("MM - tramitesController - bajaCotitular - cotitulares: ",array($cotitulares)); 
			
			return View::make(
				'tramites.bajaCotitular',
				array('titulo' => $titulo,
				'cotitulares' => $cotitulares,
				'datos' => $datos));
		}

		public function bajaCotitular_add(){
			\Log::info("WB - baja cotitular add");
			$datos = Input::all();
			if(!isset($datos['cotitular_to_kill']))
				return false;
			$id_cotitular = $datos['cotitular_to_kill'];
			$tipoTramite = 11;
			
			$datos['tipo_tramite'] = $tipoTramite;
			$datos['permiso'] = $this->obtenerPermiso();
			
			\Log::info("MM - tramitesController - datos motivo_ct: ",array($datos['motivo_ct']));
			
			$tramite = $this->servicioTramites->actualizarTramite($datos['motivo_ct'], $tipoTramite);
			\Log::info("MM - tramitesController - param1 - tramite: ",array($tramite));
			\Log::info("MM - tramitesController - param2 - datos: ",array($datos));
			\Log::info("MM - tramitesController - param3 - id_cotitular: ",array($id_cotitular));
			// tabla hab_cambio_titular: guarda los datos para realizar el cambio
			
			
			$titular = $this->servicioTramites->actualizarTitular($tramite, $datos, $id_cotitular);
			\Log::info("MM - tramitesController - titular: ",array($titular));
			// enviar mail
			$datosEmail = $this->datosEmail($tramite);

			Session::forget('usuarioTramite');
			Session::forget('tramiteGuia');

			return Redirect::to('exito-tramite');
		}

		/********************************* MODIFICACIONES A TRÁMITES **************************************/
		public function modificarTramite(){
			$nroTramite = Input::get('nroTramite');
			$resultado = $this->detalleTramite($nroTramite);
			$permiso = $resultado['tramite']['nro_permiso']; // recupero el # de permiso ADEN - 2024-09-30
			//Session::forget('tramiteNuevoPermiso');
			// Session::put('tramiteNuevoPermiso',$resultado);
			\Log::info('MM - modificarTramite - session usuarioTramite: ',array(Session::has('usuarioTramite')));
			\Log::info('MM - modificarTramite - session usuarioLogueado: ',array(Session::has('usuarioLogueado')));
			\Log::info('MM - modificarTramite - resultado: ',array($resultado));
			
			
			// cargamos tipo de usuario CAS/CAS_ROS/SUBAGENTE/AGENTE
			$datos_session = Session::get('usuarioLogueado');
			$tipo_usuario = strtoupper($datos_session['nombreTipoUsuario']);
			
			//listas
			$tipo_sociedad = $this->servicioTramites->listaTiposSociedad();
			$tipo_persona = $this->servicioTramites->listaTiposPersonas();
			$tipo_situacion = $this->servicioTramites->listaSituacionGanancias();
			$tipo_ocup_titu = $this->servicioTramites->listaOcupacionPersonas();
			$lista_tipo_relacion = $this->servicioTramites->listaTipoRelacion();
			$lista_tipo_vinculo = $this->servicioTramites->listaTipoVinculo();
			$urlRetorno = $_SERVER['HTTP_REFERER'];
			//ini listas agregadas el 2024-10-18 - ADEN
			$tipo_documento = $this->servicioTramites->listaTiposDocumentos();
			$tipo_sociedad = $this->servicioTramites->listaTiposSociedad();
			//fin listas agregadas el 2024-10-18 - ADEN

// ADEN-2024-09-30 - Si es modificación de baja de co-titular

			if($resultado['tramite']['id_tipo_tramite'] == 11){
				\Log::info("AE - tramitesController - modificarTramite - permiso: ",array($permiso)); 
				$baja_cotitular_id = $resultado['tramite']['cotitular']['id']; 
				/* obtener listado de cotitulares */
				$cotitulares = $this->servicioTramites->obtenerCotitulares($permiso);
				\Log::info("AE - tramitesController - modificarTramite - cotitulares: ",array($cotitulares)); 
			}

			if($resultado['tramite']['id_tipo_tramite'] ==12){
				$localidad_actual_f = $resultado['tramite']['localidad'].' ('.$resultado['tramite']['nro']['codigo_postal'].'-'.$resultado['tramite']['persona_nt']['subcodigo_postal'].')';
				//$localidad_actual_id_f = '999';
				$codigo_postal_f = $resultado['tramite']['persona_nt']['codigo_postal'];
				$subcodigo_postal_f = $resultado['tramite']['persona_nt']['subcodigo_postal'];
				
				 if($resultado['tramite']['id_tipo_tramite']==12){
					 $tipo_relacion=$resultado['tramite']['tipo_relyvin']['tipo_relacion'];
					 $tipo_vinculo=$resultado['tramite']['tipo_relyvin']['tipo_vinculo'];
					 
						\Log::info('MM - MM - modificarTramite - tipo_vinculo: ',array($tipo_vinculo));
						\Log::info('MM - MM - modificarTramite - tipo_relacion: ',array($tipo_relacion));
				 }
			}//elseif($resultado['tramite']['id_tipo_tramite'] ==2){
			//} 
			else{
					 $tipo_relacion=0;
					 $tipo_vinculo=0;
				 }

			\Log::info('MM - modificarTramite - urlRetorno: ',array($urlRetorno));
			\Log::info('MM - modificarTramite - resultado: ',array($resultado));
			
			Session::forget("abrirmodal");
			Session::forget("abrir");
			if(strpos($urlRetorno, "detalle-tramite-nro-seguimiento")!==false){
				Session::put("abrir", $nroTramite);
			}else{
				Session::put("abrirmodal", $nroTramite);
			}

			switch ($resultado['tramite']['id_tipo_tramite']) {
				case 11: //  baja de co-titular
					\Log::info("aa - tipo tramite 11: ", array($resultado['tramite']['id_tipo_tramite']));
					return View::make('tramites.modificarTramite',array('nroTramite'=>$nroTramite, 'tramite' => $resultado['tramite'], 'tipo_sociedad'=>$tipo_sociedad, 'tipo_persona'=>$tipo_persona, 'tipo_doc'=>$tipo_documento, 'tipo_situacion'=>$tipo_situacion, 'tipo_ocup_tit'=>$tipo_ocup_titu, 'urlRetorno' => $urlRetorno,'tipo_relacion'=>$tipo_relacion,'tipo_vinculo'=>$tipo_vinculo,'lista_tipo_relacion'=>$lista_tipo_relacion,'lista_tipo_vinculo'=>$lista_tipo_vinculo,'tipo_usuario'=>$tipo_usuario,'cotitulares' => $cotitulares, 'baja_cotitular_id' => $baja_cotitular_id));
					break;
				case 12: // cesion de titularidad por fallecimiento
					\Log::info("aa - tipo tramite 12: ", array($resultado['tramite']['id_tipo_tramite']));
					return View::make('tramites.modificarTramite',array('nroTramite'=>$nroTramite, 'tramite' => $resultado['tramite'], 'tipo_sociedad'=>$tipo_sociedad, 'tipo_persona'=>$tipo_persona, 'tipo_doc'=>$tipo_documento, 'tipo_situacion'=>$tipo_situacion, 'tipo_ocup_tit'=>$tipo_ocup_titu, 'urlRetorno' => $urlRetorno,'localidad_actual_f'=>$localidad_actual_f,'codigo_postal_f'=>$codigo_postal_f,'subcodigo_postal_f'=>$subcodigo_postal_f,'tipo_relacion'=>$tipo_relacion,'tipo_vinculo'=>$tipo_vinculo,'lista_tipo_relacion'=>$lista_tipo_relacion,'lista_tipo_vinculo'=>$lista_tipo_vinculo,'tipo_usuario'=>$tipo_usuario));
					break;
				default:
					\Log::info("aa - tipo tramite default: ", array($resultado['tramite']['id_tipo_tramite']));
					return View::make('tramites.modificarTramite',array('nroTramite'=>$nroTramite, 'tramite' => $resultado['tramite'], 'tipo_sociedad'=>$tipo_sociedad, 'tipo_persona'=>$tipo_persona, 'tipo_doc'=>$tipo_documento, 'tipo_situacion'=>$tipo_situacion, 'tipo_ocup_tit'=>$tipo_ocup_titu, 'urlRetorno' => $urlRetorno,'tipo_relacion'=>$tipo_relacion,'tipo_vinculo'=>$tipo_vinculo,'lista_tipo_relacion'=>$lista_tipo_relacion,'lista_tipo_vinculo'=>$lista_tipo_vinculo,'tipo_usuario'=>$tipo_usuario));
			}

/*
			if($resultado['tramite']['id_tipo_tramite'] ==12){
				return View::make('tramites.modificarTramite',array('nroTramite'=>$nroTramite, 'tramite' => $resultado['tramite'], 'tipo_sociedad'=>$tipo_sociedad, 'tipo_persona'=>$tipo_persona, 'tipo_situacion'=>$tipo_situacion, 'tipo_ocup_tit'=>$tipo_ocup_titu, 'urlRetorno' => $urlRetorno,'localidad_actual_f'=>$localidad_actual_f,'codigo_postal_f'=>$codigo_postal_f,'subcodigo_postal_f'=>$subcodigo_postal_f,'tipo_relacion'=>$tipo_relacion,'tipo_vinculo'=>$tipo_vinculo,'lista_tipo_relacion'=>$lista_tipo_relacion,'lista_tipo_vinculo'=>$lista_tipo_vinculo,'tipo_usuario'=>$tipo_usuario));
			}else{
				return View::make('tramites.modificarTramite',array('nroTramite'=>$nroTramite, 'tramite' => $resultado['tramite'], 'tipo_sociedad'=>$tipo_sociedad, 'tipo_persona'=>$tipo_persona, 'tipo_situacion'=>$tipo_situacion, 'tipo_ocup_tit'=>$tipo_ocup_titu, 'urlRetorno' => $urlRetorno,'tipo_relacion'=>$tipo_relacion,'tipo_vinculo'=>$tipo_vinculo,'lista_tipo_relacion'=>$lista_tipo_relacion,'lista_tipo_vinculo'=>$lista_tipo_vinculo,'tipo_usuario'=>$tipo_usuario));
			}
*/			
		}

		/**
		* Función que se encarga de guardar los datos modificados
		* para cada tipo de trámite
		**/
		public function modificarTramite_add(){
			$datos = Input::all();
			\Log::info("AE - TramitesController - modificarTramite_add - datos:", array($datos));

			if(($datos['tipo_tramite'] == 3 || $datos['tipo_tramite'] == 8 || $datos['tipo_tramite'] == 12) && $datos['nueva_localidad']!=""){
				$departamento = $this->controladorLocalidad->buscarDepartamentoLocalidad($datos['nueva_localidad']);
				$datos['departamento_id'] = $departamento['id'];
				$datos['nuevo_departamento'] = $departamento['nombre'];
			}

			$mensaje =$this->servicioTramites->modificarTramite($datos);

			if(is_null($mensaje)){
				return Response::json(array('mensaje'=>"Falló la actualización de los datos del trámite."));
			}
			return Redirect::to($datos['urlRetorno']);
		}

	/***************************** FIN MODIFICACIONES A TRÁMTIES **************************************/

	/***************************** SUSPENSIÓN DE PERMISO *********************************************/

	   /********************************************************************************
		* Función para controlar que no se inicie dos veces la solicitud de suspensión *
		********************************************************************************/
		public function controlSuspenderPermiso(){
			$permiso=Input::get('permiso');
			$ok=$this->servicioTramites->existeSolicitudSuspension($permiso);
			return Response::json(array('exito'=>$ok));
		}
		/******************************************************
		* Función que inicia la solicitud de baja del permiso *
		*******************************************************/
		public function suspenderPermiso(){

			$titular=Input::get('titular');
			$agente=Input::get('agente');
			$subagente=Input::get('subagente');
			$permiso=Input::get('permiso');
			$fechahasta=Input::get('fechahasta');
			$observaciones=Input::get('observaciones');
			$usuarioTramite=Session::get('usuarioTramite');
			$nroTramite=$this->servicioTramites->solicitudSuspension($permiso,$fechahasta,$observaciones,$usuarioTramite);
			$datos_mail['tipoTramite']=10;
			$tipoTramite = $this->servicioTipoTramites->buscarPorId(10);
			$datos_mail['nombreTipoTramite']= $tipoTramite['nombre_tramite'];
			$datos_mail['nroPermiso']=$permiso;
			$datos_mail['nroTramite']=$nroTramite;
			$datos_mail['agente']=$agente;
			$datos_mail['subAgente']=$subagente;
			$datos_mail['observaciones']=$observaciones;
			$datos_mail['titular']=$titular;

			\Log::info('MM suspenderPermiso datos_mail: ',array($datos_mail));

			$this->enviar_email_suspen_habil_permiso($datos_mail,'S');
			if(isset($nroTramite)){
				return Response::json(array('exito'=>'true', 'nroTramite'=>$nroTramite));
			}else{
				return Response::json(array('exito'=>'false'));
				//return Redirect::back()->withMessage('No tiene permiso');
			}
		}

	/******************************FIN SUSPENSIÓN DE PERMISO *****************************************/

	/****************************** HABILITACIÓN DE PERMISO ******************************************/
		
		/*********************************************************************************
		* Función para controlar que no se inicie dos veces la solicitud de habilitación *
		**********************************************************************************/
		public function controlHabilitarPermiso(){
			$permiso=Input::get('permiso');
			$ok=$this->servicioTramites->existeSolicitudHabilitacion($permiso);
			return Response::json(array('exito'=>$ok));
		}
		/**
		* Método para generar el trámite de habilitación de permiso
		**/
		public function habilitarPermiso(){
			$titular=Input::get('titular');
			$permiso=Input::get('permiso');
			$agente=Input::get('agente');
			$subagente=Input::get('subagente');
			$fechadesde=Input::get('fechadesde');
			$observaciones=Input::get('observaciones');
			$usuarioTramite=Session::get('usuarioTramite');
			try{
				$nroTramite=$this->servicioTramites->solicitudHabilitacion($permiso,$fechadesde,$observaciones,$usuarioTramite);
				$datos_mail['tipoTramite']=9;
				$tipoTramite = $this->servicioTipoTramites->buscarPorId(9);
				$datos_mail['nombreTipoTramite']= $tipoTramite['nombre_tramite'];
				$datos_mail['nroPermiso']=$permiso;
				$datos_mail['agente']=$agente;
				$datos_mail['subagente']=$subagente;
				$datos_mail['nroTramite']=$nroTramite;
				$datos_mail['titular']=$titular;
				$datos_mail['observaciones']=$observaciones;

				$this->enviar_email_suspen_habil_permiso($datos_mail,'H');

				if(isset($nroTramite)){
					return Response::json(array('exito'=>'true', 'nroTramite'=>$nroTramite));
				}else{
					return Response::json(array('exito'=>'false'));
				}
			}catch(\Exception $e){
				return Response::json(array('exito'=>'false'));
			}

		}

	/****************************** FIN HABILITACIÓN DE PERMISO **************************************/

		/**
		* Función de control del nº de subagente ingresado para una red
		**/
		public function control_subagente(){
			$red = Input::get('nro_red');
			$subagente = Input::get('nro_subagente');
			$modalidad = 1;
			$resultado = $this->controladorAgencias->mayorSubagenteRed($red, $subagente, $modalidad);
			if($resultado == 0)
				$respuesta= Response::json(array('mensaje'=>"El nº de subagente no es válido."));
			else
				$respuesta= Response::json(array('nro_subagente' => "válido"));


			return $respuesta;
		}

	/*************************** Evaluación Domicilio *********************************************/

		/**
		* Función para llamar a la vista de evaluación de domicilio
		**/
		public function cargarEvaluacionDomicilio(){

			$usuarioLogueado = Session::get('usuarioLogueado');
			$nroTramite = Input::get('nroTramite');
			$tramite = $this->servicioTramites->buscarPorId($nroTramite);
			$datosPermiso=$this->servicioTramites->datosPermisoSuitecrm($tramite['nro_permiso'], $tramite['id_tipo_tramite']);

			/* Datos de los domicilios */
			$domicilio =$this->servicioTramites->obtenerNuevoDomicilio($nroTramite);
			$localidad_vieja =$this->controladorLocalidad->buscarPorIdLocalidad($domicilio['id_localidad_anterior']);
			$departamento_viejo=$this->controladorLocalidad->buscarDepartamentoLocalidad($localidad_vieja['id']);

			$localidad_nueva =$this->controladorLocalidad->buscarPorIdLocalidad($domicilio['id_localidad_nueva']);
			$departamento_nuevo=$this->controladorLocalidad->buscarDepartamentoLocalidad($localidad_nueva['id']);

			$datos['localidad_vieja']=trim($localidad_vieja['nombre']);
			$datos['departamento_viejo']=trim($departamento_viejo['nombre']);
			$datos['direccion_vieja']=trim($datosPermiso['domicilio']);

			$datos['localidad_nueva']=trim($localidad_nueva['nombre']);
			$datos['departamento_nuevo']=trim($departamento_nuevo['nombre']);
			$datos['direccion_nueva']=trim($domicilio['direccion_nueva']);

			/* Plan estratégico */
			$plan = $this->servicioTramites->obtenerPlan($nroTramite);
			$evaluacion = $this->servicioTramites->obtenerEvaluacion($nroTramite);

			$urlRetorno = $_SERVER['HTTP_REFERER'];

			Session::forget("abrirmodal");
			Session::forget("abrir");
			if(strpos($urlRetorno, "detalle-tramite-nro-seguimiento")!==false){
				Session::put("abrir", $nroTramite);
			}else{
				Session::put("abrirmodal", $nroTramite);
			}

			//Datos para la vista
			$agencia=$datosPermiso['nombre_agencia'];

			$lista_centro_afinidad=array(	'1'=>'Organismos - Serv. Públicos',
											'2'=>'Hospital - Sanatorio',
											'3'=>'Hipermercado',
											'4'=>'Supermercado - Almacén',
											'5'=>'Correo - Locutorio',
											'6'=>'Estacionamiento - Estación Serv.',
											'7'=>'Parada de colectivos',
											'8'=>'Terminal de colectivos',
											'9'=>'Bar - Restaurantes',
											'10'=>'Bancos',
											'11'=>'Farmacias',
											'12'=>'Fábricas',
											'13'=>'Otros comercios',
											'14'=>'Otros',
										);

			$lista_estados=$this->servicioTramites->listaEstadosEvaluacion();
			$lista_estados_general=$this->servicioTramites->listaEstadosEvaluacionGeneral();

			$listaCaracteristicas = $this->servicioTramites->listaCaracteristicasZona();
			$listaSocioEconomico = $this->servicioTramites->listaSocioEconomico();
			$listaSuperficie = $this->servicioTramites->listaSuperficie();
			$listaUbicacion = $this->servicioTramites->listaUbicacion();
			$listaVidriera = $this->servicioTramites->listaVidriera();
			$listaVisibilidad =array(	'1'=>'Elija uno',
										'2'=>'Más de 1 cuadra',
										'3'=>'Misma cuadra',
										'4'=>'Pasa desapercibido',
									);
			$lista_tipolocal = array(	'1'=>'Elija uno',
										'2'=>'Local comercial',
										'3'=>'Garage',
										'4'=>'Vivienda',
									);

			$tablaAntecedentes = $this->servicioTramites->tablaAntecedentes($nroTramite);

			$habilitarGuardar = in_array($evaluacion->estado, array(1, 2, 3));
			$arregloEvaluacion = (array)$evaluacion;
			$arregloEvaluacion ['existen_competidores']= ($arregloEvaluacion['competidor_cuadra']>0 || $arregloEvaluacion['competidor_antsig']>0 || $arregloEvaluacion['competidor_transv']>0 ? "1": "0");

			Session::flashInput($arregloEvaluacion);
			return View::make('tramites.evaluacionDomicilio',array('nroTramite'=>$nroTramite, 'agencia'=>$agencia,'plan'=>$plan, 'evaluacion'=>$evaluacion, 'lista_centro_afinidad'=>$lista_centro_afinidad, 'lista_estados_general'=>$lista_estados_general,'lista_estados'=>$lista_estados, 'lista_caracteristicas'=>$listaCaracteristicas, 'lista_socioeconomico'=>$listaSocioEconomico,'lista_superficie'=>$listaSuperficie, 'lista_ubicacion'=>$listaUbicacion, 'lista_vidriera'=>$listaVidriera, 'lista_visibilidad' =>$listaVisibilidad, 'lista_tipolocal'=>$lista_tipolocal,'datos'=>$datos, 'tablaAntecedentes'=>$tablaAntecedentes,'urlRetorno' => $urlRetorno, 'permiso'=>$tramite['nro_permiso'], 'habilitarGuardar'=>$habilitarGuardar));
		}

		/**
		* Función de Carga plano
		*/
		public function cargaPlanoEvaluacion(){
			try{
					$ds = DIRECTORY_SEPARATOR;

					$nroTramite = Input::get('nroTramite');
					$destinationPath = public_path(). $ds . 'upload'.$ds.'CD_'.$nroTramite.'_';

					if(Input::hasFile('plano')){
							$file = Input::file('plano'); // your file upload input field in the form should be named 'file'
							$nombre = $file->getClientOriginalName();
							$extension= strtolower($file->getClientOriginalExtension());

							$rules = array(
							   'file' => 'required|mimes:jpg,png,gif,jpeg|max:20000'
							);
							$validator = \Validator::make(array('file'=> $file), $rules);

							if($validator->passes()){

								$permiso =Input::get('permiso');
								$nombre = $permiso."_P.".$extension;
								$destinationPath .= $permiso;

								if(!is_dir($destinationPath)){
								  File::makeDirectory($destinationPath, 0777, true);
								}
								//almaceno los archivos anteriores para luego eliminarlos
								$existePlano=glob($destinationPath.$ds.$permiso.'_P.*');

								$uploadSuccess = Input::file('plano')->move($destinationPath, $nombre);

								if($uploadSuccess) {
									if(count($existePlano)>0){
										foreach($existePlano as $plano){
											if(strcasecmp($plano, $destinationPath.$ds.$nombre )!== 0)
												unlink($plano);
										}
									}

									return Response::json(array('success'=>200,'path_plano'=>'upload/CD_'.$nroTramite.'_'.$permiso.'/'.$nombre));
								} else {
									return Response::json('error', 400);
								}
							}else{
								\Log::error('No es un tipo de imagen válido');
								return Response::json('error', 400);
							}//fin validator
					}else{
						return Response::json('error', 400);
						}
				}catch(Exception $e){
					\Log::error('Problema al cargar el plano');
					return Response::json('error', 400);
				}

		}

		/**
		* Función para guardar los datos de la vista de evaluación de domicilio
		* en la base de datos
		**/
		public function evaluacionDomicilio(){
			$datos = Input::all();
			if($datos['plano']!="")
				$datos['plano']=strtolower($datos['plano']->getClientOriginalExtension());//extensión

			//ver cómo obtener el estado de la evaluación general
			$mensaje = $this->servicioTramites->guardarEvaluacionDomicilio($datos);
			return Redirect::to($datos['urlRetorno']);
		}

		/**
		* Función que se encarga de generar la ruta al
		* reporte del plan en formato pdf
		*/
		public function evaluacion_pdf(){
			Session::forget('url_evaluacion_pdf');
			$nro_tramite = Input::get('nro_tramite');
			//armado de la url para el reporte
			$url_repositorio = Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
			$url_repositorio .= Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.evaluacion_pdf')."&";
			$url_repositorio .="formato=".Config::get('habilitacion_config/jasper_config.formatos_reportes.2')."&";
			$url_repositorio .=Config::get('habilitacion_config/jasper_config.parametros_evaluacion.nombres.0')."=".$nro_tramite;

			Session::put('url_evaluacion_pdf',$url_repositorio);
			return Response::json(array('mensaje'=>'mensaje'));

		}


		public function show_evaluacion_pdf(){
			$url_pdf = Session::get('url_evaluacion_pdf');

			return View::make('reportes.reportes_tramites', array('url_repositorio'=>$url_pdf));
		}

	/*************************** Fin Evaluación Domicilio *****************************************/

		/* CORREO DE PRUEBA*/
		public function enviar_email_prueba(){
				try{
						\Log::info("envio mail prueba");
						$mloteria=Config::get('mail.from.address');
						$i2t=Config::get('mail.i2t_prueba'); //en el archivo app/config/mail.php
						$prueba=Config::get('mail.prueba'); //en el archivo app/config/mail.php
						$nombreUsuarioLogueado=Session::get('usuarioLogueado.apellido');
						$destinatarios = array(
							  $i2t,$prueba
							);

						$correos = array(
							'destinatarios'=>$destinatarios,
							'remitente'=>$mloteria);

						$data = array(
							'from'=>$mloteria,
							'to' => $i2t
						);

						Mail::send('emails.mail_prueba', $data, function ($message) use ($correos){
							$concatenoEmails = "";
							$message->from($correos['remitente'], 'Sistema de tramites');
							$message->subject('Acuse Registro Tramite Registrado.');
							foreach ((array)$correos['destinatarios'] as $email) {
								$arregloEmail = explode(";", $email);
								foreach ($arregloEmail as $mail) {
									if (isset($mail) && $mail != "") {
											$concatenoEmails = $concatenoEmails . $mail .  ";";
									}
								}


							}
							$concatenoEmails = substr($concatenoEmails, 0, -1);
							$message->to(explode(";",$concatenoEmails));
						});

					}catch(Exception $e){
						\Log::error('Problema al enviar el correo. '.$e);
						return true;
					}
			}

		/*********************************/
		/* Consultar Tramite Pendiente	*/
		/*	JG-18/11/2020				*/
		/********************************/

		public function consultarPorNroTramitePendientes(){
			$datos_session = Session::get('usuarioLogueado');
			$permiso= $datos_session['permiso'];
			$tramitesPendientes = [];
			if($datos_session['nombreTipoUsuario'] != 'CAS')
				$tramitesPendientes = $this->servicioTramites->listarTramitesPendientes($permiso);

			$respuesta = Response::json(array('tramites'=>$tramitesPendientes));
			return $respuesta;
		}


		public function consultarPorNroTramite_addP($id){
			$idt = $id;
			$usr = Session::get('usuarioLogueado');
			Session::forget('usuarioTramitePermiso');

			$tramite=$this->servicioTramites->buscarPorId($idt);

			\Log::info('TRAMITES - consultarPorNroTramite_add2', array($tramite));

			if(is_null($tramite)){
				$mensaje = "No tiene un trámite registrado con ese nº de seguimiento.";
				$respuesta = Response::json(array('mensaje'=>$mensaje));
				return $respuesta;
			}else if($tramite['nro_permiso']!=$usr['permiso'] && $usr['nombreTipoUsuario']!="CAS" && $usr['nombreTipoUsuario']!='CAS_ROS'){
				$mensaje = "No tiene un trámite registrado con ese nº de seguimiento.";
				$respuesta = Response::json(array('mensaje'=>$mensaje));
				return $respuesta;
			}else{
				Session::put('usuarioTramitePermiso',$tramite['nro_permiso']);
				$datos=$tramite['nro_tramite']."&".$tramite['id_tipo_tramite']."&1";
				return Redirect::to('detalle-tramite-nro-seguimientoP/'.$datos);
			}
		}


		public function detallePorNroSeguimientoP($id){
			//TODO: unificar con detallePorNroSeguimiento()
			try{
				if(Session::has("abrir")){
					$idTramite = Session::get("abrir");
				}else{
					$idTramite = $id;
				}
				$resultado = $this->detalleTramite($idTramite);
				$usu=Session::get('usuarioLogueado.nombreUsuario');
				$tipo_sociedad = $this->servicioTramites->listaTiposSociedad();
				$tipo_persona = $this->servicioTramites->listaTiposPersonas();
				$tipo_situacion = $this->servicioTramites->listaSituacionGanancias();

//				$tipoUsuario = Session::get('usuarioLogueado.nombreTipoUsuario');
				$tipoUsuario = $usr['nombreTipoUsuario'];

				return View::make(
					'tramites.detalleTramite',
					array('tramite' => $resultado['tramite'],
					'estados'=>$resultado['estados'],
					'meses'=>$resultado['meses'],
					'dias'=>$resultado['dias'],
					'paso_fecha'=>$resultado['paso_fecha'],
					'cant'=>$resultado['cant'],
					'datosPermiso'=>$resultado['datosPermiso'],
					'usu'=>$usu,
					'tipo_sociedad'=>$tipo_sociedad,
					'tipo_persona'=>$tipo_persona,
					'tipo_situacion'=>$tipo_situacion,
					'puedeModificar' => $resultado['puedeModificar']
				));
			}catch(Exception $e){
				\Log::error($e);
				\Log::error('Problema buscar el detalle del trámite por nro de seguimiento');
				return Redirect::back();
			}

		}

/************************************FIN Consultar Tramites Pendientes************************************/

		/** FIXME: funciones auxiliares que podrían estar en otra clase **/
		public function esUsuarioInterno($usuario){
			return $usuario['nombreTipoUsuario']=='CAS' || $usuario['nombreTipoUsuario']=='CAS_ROS';
		}

		public function obtenerPermiso(){
			$usuario = Session::get('usuarioLogueado');
			$permiso = self::esUsuarioInterno($usuario) ? Session::get('usuarioTramite')['permiso'] : $usuario['permiso'];
			return $permiso;
		}

		public function mailAgente(){
			if(Session::has('usuarioTramite'))
				return Session::get('usuarioTramite')['email'];
			else
				return Session::get('usuarioLogueado')['email'];
		}

		public function datosEmail($tramite){
			$datos_mail['nroTramite'] = $tramite->nroTramite;
			$datos_mail['tipoTramite'] = $tramite->tipoTramite['nombre_tramite'];
			$datos_mail['agente'] = $tramite->agente;
			$datos_mail['subagente'] = $tramite->subAgente;
			$datos_mail['nro_permiso'] = $tramite->nroPermiso;
			$datos_mail['email_duenio'] = $this->mailAgente();
			Session::put('datos_email', $datos_mail);

			return $datos_mail;
		}

		public function puedeModificar($usuario, $tramite){
			if(in_array($tramite['id_tipo_tramite'], array(6, 7, 11)))
				//baja cotitular, incorporar maquina, baja de maquina
				return false;
			if($usuario == "CAS" || $usuario == "CAS_ROS")
				return in_array($tramite['id_estado_tramite'], array(0, 1, 2, 5, 6, 7));
			else
				return $tramite['id_estado_tramite'] == 0;
			return true;
		}
		
}
