<?php
	
	/**
	* Clase de servicio que se comunica con el repositorio
	* para obtener los datos del modelo.
	*/
	
	namespace Dominio\Servicios\Habilitacion;
	use controllers\UsuarioController;
	use \Datos\Repositorio\Habilitacion\TramiteRepo;
	use \Datos\Repositorio\Habilitacion\TitularRepo;
	use \Datos\Repositorio\Habilitacion\PersonaRepo;
	use \Datos\Repositorio\Habilitacion\DomicilioRepo;
	use \Datos\Repositorio\Habilitacion\LocalidadRepo;
	use \Datos\Repositorio\Habilitacion\DependenciaRepo;
	use \Datos\Repositorio\Habilitacion\CategoriaRepo;
	use \Datos\Repositorio\Habilitacion\EstadoTramiteRepo;
	use \Datos\Repositorio\Habilitacion\SuiteCRMRepo;
	use \Datos\Repositorio\Habilitacion\AdjuntoTramiteRepo;
	use \Dominio\Servicios\Habilitacion\TipoTramites;
	use \Dominio\Servicios\Habilitacion\HistoricoTramitesEstados;
	use \Dominio\Servicios\Habilitacion\Personas;
	use \Dominio\Entidades\Habilitacion\Tramite;
	use \Dominio\Entidades\Habilitacion\Domicilio;
	use \Dominio\Entidades\Habilitacion\Dependencia;
	use \Dominio\Entidades\Habilitacion\Categoria;
	use \Dominio\Entidades\Habilitacion\Titular;
	use \Dominio\Entidades\Habilitacion\Plan;
	use \Dominio\Entidades\Habilitacion\Evaluacion;
	use \Dominio\Entidades\Habilitacion\AdjuntoTramite;
	use Datos\Filtros\FiltroTramite;
	use Datos\Filtros\FiltroPermiso;
	use Presentacion\Premios\Formatos;
	use Carbon\Carbon;

	class Tramites{
		
		function __construct(){
			$this->servicioTipoTramite = new TipoTramites();
			$this->servicioHistorial = new HistoricoTramitesEstados();
			$this->controladorUsuario = new UsuarioController();
			$this->servicioPersona = new Personas();
			$this->formatos=new Formatos();
		}

		/**
		* Devuelve una lista de todos los tramites
		* según los criterios especificados por los parámetros
		*/
		public static function buscarTodos(){
			$tramites=TramiteRepo::buscarTodos();
			foreach ($tramites as $tramite) {
				$result[]=TramiteRepo::arregloTramite($tramite);
			}
			return $result;
		}


		/**
		* Devuelve el tramite de tipo cambio de categoría
		* según los criterios especificados por los parámetros
		*/
		public static function obtenerNuevaCategoria($nroTramite){
			$categoria=CategoriaRepo::buscarPorNroTramite($nroTramite);			
			return $categoria;
		}
		
		/**
		* Devuelve el tramite de tipo cambio de categoría
		* según los criterios especificados por los parámetros
		*/
		public static function obtenerCategoriaPermiso($nroPermiso){
			$nroTramite = TramiteRepo::nroTramiteCatNP($nroPermiso);
			if(!is_null($nroTramite)){
				$categoria=CategoriaRepo::buscarPorNroTramite($nroTramite);	
				return $categoria;
			}else{
				return null;
		}
		}
	
		/**
		* Devuelve el tramite de tipo cambio de dependencia
		* según los criterios especificados por los parámetros
		*/
		public static function obtenerDependencia($nroTramite){
			$dependencia=DependenciaRepo::buscarPorNroTramite($nroTramite);	
			$dependencia['razonSocial']=SuiteCRMRepo::nombreRed($dependencia['nro_red_actual']);		
			return $dependencia;
			}

	
		/**
		* Devuelve un tramite (como arreglo) cuyo id coincide
		* con el pasado por parámetro. Pasa el estado y el tipo de trámite
		* con los nombres(no solo los ids).
		*/
		public function buscarPorId($id){
			$tramite = TramiteRepo::buscarPorId($id);
			
			if(is_null($tramite)){
				return $tramite;
			}
        	$tipoTramite=$tramite->tipoTramite->toArray();
        	$estadoTramite=$tramite->estadoTramite->toArray();
        	$tramiteArray=TramiteRepo::arregloTramite($tramite);
        	
        	$tramiteArray['fecha']=Carbon::parse($tramiteArray['fecha'])->format('d/m/Y');
        	$tramiteArray['nombre_tramite']=$tipoTramite['nombre_tramite'];
        	$tramiteArray['abreviatura']=$tipoTramite['abreviatura'];
        	$tramiteArray['descripcion_estado']=$estadoTramite['descripcion_tramite'];
    		
    		if($tramiteArray['id_tipo_tramite']==2){//cambio de categoria
    			$tramiteArray['nro_pto_vta_anterior']=CategoriaRepo::buscarPorNroTramite($tramiteArray['nro_tramite'])['nro_pto_vta_anterior'];
    		}
			return $tramiteArray;
		}


		/**
		* Devuelve un tramite (como arreglo) cuyo id coincide
		* con el pasado por parámetro. Pasa el estado y el tipo de trámite
		* con los nombres(no solo los ids).
		*/
		public function buscarPorIdSinCancelar($id){
			$tramite = TramiteRepo::buscarPorIdSinCancelar($id);
			
			if(is_null($tramite)){
				return $tramite;
			}
        	  
			return TramiteRepo::arregloTramite($tramite);
		}

		/********************************************************************
		* Devuelve un arreglo con los ids de los trámites del permiso nuevo *
		*********************************************************************/		
		public function buscarTramitesNuevoPermiso($nroPermiso){
			return $idsTramites=TramiteRepo::buscarTramitesNuevoPermiso($nroPermiso);
		}

		/**
		* Busca todos los posibles estados para un tramite
		*/
		public function buscarTodosLosEstados(){
			$estadosTramites=EstadoTramiteRepo::buscarTodos();
			foreach ($estadosTramites as $estadoTramite) {
				$result[]=estadoTramiteRepo::arregloEstadoTramite($estadoTramite);
			}
			return $result;
		}

		/**
		* Busca todos los tipos posibles de tramite
		*/
		public function buscarTodosLosTipos($funcion){
			$tiposTramites=$this->servicioTipoTramite->buscarTodos($funcion);
			
			return $tiposTramites;
		}
 
		/**
		* Devuelve un estado de tramite (entidad en arreglo) cuyo id coincide
		* con el pasado por parámetro
		*/
		public function buscarElEstado($id){
			$estadoTramite = estadoTramiteRepo::buscarPorId($id);
			$estadoTramite = estadoTramiteRepo::arregloEstadoTramite($estadoTramite);
			return $estadoTramite;
		}

		/***************PARA CAMBIO CATEGORIA**************/
		//nº nueva red
		public function numeroNuevaRed($codigoPostal, $nro_red, $modalidad){
			$nro = CategoriaRepo::numeroNuevaRed($codigoPostal, $nro_red, $modalidad);
			return $nro;
		}
		//verifica si la red propuesta puede ser usada.
		public function nuevaRedPropuesta($nroRed,$cp){
			$nro = CategoriaRepo::nuevaRedPropuesta($nroRed,$cp);
			return $nro;
		}

		//verifica si es un cambio de subagente a agente
		public function esNuevoAgente($nro_seguimiento){
			$resultado = CategoriaRepo::esNuevoAgente($nro_seguimiento);
			return $resultado;
		}
		
		//verifica si completó el cambio de domicilio (para nuevos permisos)
		public function completoCambioDomicilioNP($nro_permiso){
			$nroTramite = TramiteRepo::nroTramiteDomNP($nro_permiso);
			if(isset($nroTramite))
			     return DomicilioRepo::obtenerNuevoDomicilio($nroTramite);
                        else 
			     return null;
			}

		/*devolver el código postal del nuevo permiso (si ya está completo el dom)
		public function cpNuevoPermiso($nro_permiso){
			$nroTramite = TramiteRepo::nroTramiteDomNP($nro_permiso);
			if(!is_null($nroTramite)){
				$cp= DomicilioRepo::cpNuevoPermiso($nroTramite);
				return $cp;
		}
			return 0;
		}*/

		/*********************PARA CAMBIO DE TITULAR************************/
		//obtener el nuevo titular según el nº de trámite (aprobación)
		public function obtenerNuevoTitular($nroTramite){
			$persona = TitularRepo::buscarPorNroTramite($nroTramite);
			if(!is_null($persona)){
				$id_persona = $persona['id_titular_nuevo'];
				$persona = $this->servicioPersona->buscarPorId($id_persona);

				if($persona['cuit']!=""){
					$persona['cuit']= $this->formatos->armaCuit($persona['cuit']);
				}
				$persona['fecha_nacimiento']=$this->formatos->fecha($persona['fecha_nacimiento']);
			}
			
			return $persona;
		}

		/********************Listas plan estratégico************************/
		public function listaHorario(){
			$listaHorario = SuiteCRMRepo::listaHorario();
			return $listaHorario;
		}

		public function listaUbicacion(){
			$listaUbicacion = SuiteCRMRepo::listaUbicacion();
			return $listaUbicacion;
		}

		public function listaSuperficie(){
			$listaSuperficie = SuiteCRMRepo::listaSuperficie();
			return $listaSuperficie;
		}

		public function listaVidriera(){
			$listaVidriera = SuiteCRMRepo::listaVidriera();
			return $listaVidriera;
		}

		public function listaCaracteristicasZona(){
			$listaCaracteristicas = SuiteCRMRepo::listaCaracteristicasZona();
			return $listaCaracteristicas;
		}

		public function listaCirculacion(){
			$listaCirculacion = SuiteCRMRepo::listaCirculacion();
			return $listaCirculacion;
		}

		public function listaSocioEconomico(){
			$listaSocioEconomico=SuiteCRMRepo::listaSocioEconomico();
			return $listaSocioEconomico;
		}

		public function listaOcupacionPersonas(){
			$listaOcupacionPersonas=SuiteCRMRepo::listaOcupacionPersonas();
			return $listaOcupacionPersonas;
		}

		/***************************************/
		/*	Posibles estados a los que puede   */
		/* pasar un trámite desde el estado en */
		/* el que está y según el usuario que  */
		/* quiere hacer el cambio. En caso de  */
		/* que no tenga estado posterior se    */
		/* devuelve el mismo estado.           */  
		/***************************************/
		public function estadosPosibles($estadoInicial, $usuarioLogueado){
			$estadosPosibles = EstadoTramiteRepo::estadosPosibles($estadoInicial, $usuarioLogueado);
			foreach ($estadosPosibles as $estados) {
				$result[]=estadoTramiteRepo::arregloEstadoTramite($estados);
			}
			return $result;
		}

	
		/**
		* Devuelve el historial de estado del trámite
		* cuyo id coincide con el pasado por parámetro.
		**/
		public function historialEstadoTramite($id){
			$historial = $this->servicioHistorial->buscarPorId($id);
			return $historial;
		}

		/**
		* Devuelve una lista de los ids de tipos de tramite
		**/
		public function listaTiposTramiteIds($funcion){
			$lista = $this->servicioTipoTramite->buscarTodosLosIds($funcion);
			return $lista;
		}

		/**
		 * Devuelve una lista de los tipos de persona
		 */
		public function listaTiposPersonas(){
			$lista = SuiteCRMRepo::tiposDePersonas();
			return $lista;
		}
		/**
		 * Devuelve una lista de los tipos de persona
		 */
		public function listaTiposSociedad(){
			$lista = SuiteCRMRepo::tiposDeSociedad();
			return $lista;
		}

		/**
		 * Devuelve una lista de los tipos de persona
		 */
		public function listaTiposDocumentos(){
			$lista = SuiteCRMRepo::tiposDeDocumentos();
			return $lista;
		}


		/**
		 * Devuelve una lista de los tipos de situaciones de ganancia
		 */
		public function listaSituacionGanancias(){
			$lista = SuiteCRMRepo::listaSituacionGanancias();
			return $lista;
		}

		/**
		* Devuelve los datos del permiso en suitecrm
		*/
		public function datosPermisoSuitecrm($nroPermiso, $tipoTramite){
			$datos = TramiteRepo::datosPermisoSuitecrm($nroPermiso, $tipoTramite);
			$departamento = LocalidadRepo::buscarDepartamentoPorCP_SCP($datos['cp'], $datos['scp']);
			\Log::info("ANALIZE_EMAIL - datosPermisoSuitecrm: ", array($datos));
			$localidad = LocalidadRepo::buscarPorCPSCPLocalidad($datos['cp'], $datos['scp']);//
			$datos['departamento']=$departamento;
			$datos['localidad']=trim($localidad->nombre);
			$datos['cp_localidad']=$localidad->codigoPostal;
			return $datos;
		}

		/**
		* Devuelve el tipo del trámite cuyo nro_tramite llega por parámetro
		*/
		public function tipoDelTramite($nro_tramite){
			$tipo_tramite = TramiteRepo::tipoDelTramite($nro_tramite);
			return $tipo_tramite;
		}
		
/**************************************Métodos específicos para carga de trámites***************************************/
		

		/**
		* Modifica el estado de un tramite
		**/
		public function modificarEstadoTramite($idTramite, $idEstadoI, $idEstadoF, $observaciones, &$datos_mail, $esNuevo){
			if($esNuevo && $idEstadoF == 10):
				$mensaje = TramiteRepo::modificarEstadoTramitesNuevo($idTramite, $idEstadoF, $datos_mail, $listaTramitesPermiso);
			else:
			$mensaje = TramiteRepo::modificarEstadoTramite($idTramite, $idEstadoF, $datos_mail);
			endif;


			if($esNuevo && $idEstadoF == 10):
				foreach ($listaTramitesPermiso as $ltp) {
					
				$datos['estadoIni'] = $idEstadoI;
				$datos['id_estado_tramite']=$idEstadoF;
				$datos['observaciones']=$observaciones; 
					$this->servicioHistorial->crear($ltp->nro_tramite, $datos);				
				if($idEstadoF==10)
						$mensaje=TramiteRepo::modificarRutaHistorial($ltp->nro_tramite, $idEstadoF, $datos_mail);
				$datos_mail['estadoI']=EstadoTramiteRepo::buscarPorId($idEstadoI)->descripcionTramite;
				$datos_mail['idEstadoI']=$idEstadoI;
				$datos_mail['estadoF']=EstadoTramiteRepo::buscarPorId($idEstadoF)->descripcionTramite;
				$datos_mail['idEstadoF']=$idEstadoF;
				$datos_mail['observaciones'] = $datos['observaciones'];
			}
			elseif(!is_null($mensaje)):
					$datos['estadoIni'] = $idEstadoI;
					$datos['id_estado_tramite']=$idEstadoF;
					$datos['observaciones']=$observaciones; 
					$this->servicioHistorial->crear($idTramite, $datos);				
					if($idEstadoF==10)
						$mensaje=TramiteRepo::modificarRutaHistorial($idTramite, $idEstadoF, $datos_mail);
					$datos_mail['estadoI']=EstadoTramiteRepo::buscarPorId($idEstadoI)->descripcionTramite;
					$datos_mail['idEstadoI']=$idEstadoI;
					$datos_mail['estadoF']=EstadoTramiteRepo::buscarPorId($idEstadoF)->descripcionTramite;
					$datos_mail['idEstadoF']=$idEstadoF;
					$datos_mail['observaciones'] = $datos['observaciones'];				
			endif;
			\Log::info("ANALIZE_EMAIL - modificarEstadoTramite - datos_mail: ", array($datos_mail));
			return $mensaje;
		}

		/**
		* Modifica el estado de un tramite a aprobado/rechazado
		* datoExtra=nroNuevaRed si es cambio de categoria
		* datoExtra=cuit si es cambio de titular
		**/
		public function modificarEstadoTramiteAR($idTramite, $idEstadoI, $idEstadoF, $datoExtra=NULL , $resolucion, $expediente, &$datos_mail, $tipoTramite, $subagente=NULL, $esNuevo){

			if(!is_null($datoExtra)){
				if($tipoTramite==2 && $subagente==0){//cambio de categoria a agente - cuando es un nuevo permiso y quiere ser agente
					$tramite=TramiteRepo::buscarPorId($idTramite);
					if($tramite->nuevo_permiso){
						$cp=TramiteRepo::nroTramiteDomNP($tramite->nroPermiso);
					}else{
						$cp=$tramite->codigoPostalAgencia;
					}
					$campos=array('cbu' => $datoExtra['cbu']);
					CategoriaRepo::modificarCampos($idTramite, $campos);
					CategoriaRepo::modificarRedNuevoAgente($idTramite, $datoExtra['nro_nueva_red']	, $cp);
                }else if($tipoTramite==2 && $subagente!=0){//cambio de categoria a subagente
					$valido = TramiteRepo::verificaNroSubagente($datoExtra, $subagente, 1);
					if($valido==0){
							//\Log::info("modificarEstadoTramiteAR. cambio cat (tramite serv): ", array($valido));
						CategoriaRepo::modificarRedNuevoSubagente($idTramite, $datoExtra, $subagente);
					}
					else
						return null;	
				}else if($tipoTramite==3 || $tipoTramite == 8){//cambio de titular
					$persona= TitularRepo::buscarPorNroTramite($idTramite);
					if(!is_null($persona)){
						$id_persona=$persona['id_titular_nuevo'];
						$datoExtra['cuit']=str_replace("-", "", $datoExtra['cuit']);
						PersonaRepo::modificarCamposNuevaPersonaTitular($id_persona, $datoExtra);	
					}else{
						\Log::info("servicio trámites - modificar estado trámite : la persona vino nula");
						return null;
					}					
				}else if($tipoTramite==4){//cambio de dependencia - para cdo es un nuevo permiso y quiere ser subagente
						$valido = TramiteRepo::verificaNroSubagente($datoExtra, $subagente, 1);
						if($valido==0)
							DependenciaRepo:: modificaNuevoSubagente($idTramite,$subagente);
						else
							return null;
				}						
			}					
			
			//controlo que haya completado todos los datos según el tipo de trámite
			$mensaje = "";
			$todoCompleto = false;
			$terminoEval = true;

			switch ($tipoTramite) {
				case '1':
					$completoDomicilio = DomicilioRepo::CompletoTodosLosCamposDom($idTramite);
					$completoPlan = DomicilioRepo::CompletoTodosLosCamposPlan($idTramite);
					\Log::info("completo Plan (modificarEstadoTramiteAR tramites):", array($completoPlan));
					if(!$completoPlan):
						$mensaje = "Debe completar el plan del domicilio";
					endif;

					$todoCompleto = ($completoDomicilio && $completoPlan) ? true:false;

					if($todoCompleto && $idEstadoF==7):
						$terminoEval = DomicilioRepo::TerminoEvaluacion($idTramite);

					\Log::info("terminoEval (modificarEstadoTramiteAR tramites):", array($terminoEval));
						if(!$terminoEval):

							$todoCompleto = false;
							$mensaje = "Debe completar si es satisfactoria/no satisfactoria la evaluación.";
						endif;
					endif;

					break;
				case '2':
					$todoCompleto = CategoriaRepo::CompletoTodosLosCampos($idTramite);
					break;
				case '4':
					$todoCompleto = DependenciaRepo::CompletoTodosLosCampos($idTramite);
					break;
				
				default:
					$todoCompleto = TitularRepo::CompletoTodosLosCampos($idTramite);					
					break;
				}
			
			if($todoCompleto):
				
					$modificar = true;
					if($esNuevo && $idEstadoF ==7):						
						if($this->todosLosOtrosTramitesCompletos($idTramite, $mensaje)):
							$mensaje = TramiteRepo::modificarEstadoTramitesNuevo($idTramite, $idEstadoF, $datos_mail, $listaTramitesPermiso);
						else:
							if(empty($mensaje))
								$mensaje = "Debe completar los otros trámites asociados al permiso";
							$modificar = false;
						endif;
					else:
						$mensaje = TramiteRepo::modificarEstadoTramite($idTramite, $idEstadoF, $datos_mail, $esNuevo);
					endif;

					if($esNuevo && $idEstadoF ==7 && $modificar && !is_null($mensaje)):

						foreach ($listaTramitesPermiso as $ltp) {

						$datos['estadoIni'] = $idEstadoI;
						$datos['id_estado_tramite']=$idEstadoF;
						$datos['observaciones']="Nº resolución: ".$resolucion." Nº expediente: ".$expediente; 
							$this->servicioHistorial->crear($ltp->nro_tramite, $datos);
							$resultado=TramiteRepo::resolucionExpedienteTramite($ltp->nro_tramite, $idEstadoF,$resolucion, $expediente);
						$datos_mail['estadoI']=EstadoTramiteRepo::buscarPorId($idEstadoI)->descripcionTramite;
						$datos_mail['idEstadoI']=$idEstadoI;
						$datos_mail['estadoF']=EstadoTramiteRepo::buscarPorId($idEstadoF)->descripcionTramite;
						$datos_mail['idEstadoF']=$idEstadoF;
						$datos_mail['observaciones'] = $datos['observaciones'];

						if(is_null($resultado))
							$mensaje += " Pero no cargados resolución y expediente.";
			}
					elseif(!is_null($mensaje) && $modificar):
						$datos['estadoIni'] = $idEstadoI;
						$datos['id_estado_tramite']=$idEstadoF;
						$datos['observaciones']="Nº resolución: ".$resolucion." Nº expediente: ".$expediente; 
						$this->servicioHistorial->crear($idTramite, $datos);
						$resultado=TramiteRepo::resolucionExpedienteTramite($idTramite, $idEstadoF,$resolucion, $expediente);
						$datos_mail['estadoI']=EstadoTramiteRepo::buscarPorId($idEstadoI)->descripcionTramite;
						$datos_mail['idEstadoI']=$idEstadoI;
						$datos_mail['estadoF']=EstadoTramiteRepo::buscarPorId($idEstadoF)->descripcionTramite;
						$datos_mail['idEstadoF']=$idEstadoF;
						$datos_mail['observaciones'] = $datos['observaciones'];

						if(is_null($resultado)){
							$mensaje += " Pero no cargados resolución y expediente.";
		}
					endif;
					
			else:	
				if($mensaje == "")			
					$mensaje = "Debe completar todos los datos solicitados en el trámite.";
			endif;

			return $mensaje;			
			
		}
		
		/*Modificar domicilio - Actualización*/
		public function actualizarDomicilio($datos){
			$tramite=$datos['nroTramite'];
			$domicilioNuevo=$datos['nueva_direccion'];
			$domicilioViejo=$datos['domicilio_viejo'];
			
			$actualizado=DomicilioRepo::actualizarDomicilio($tramite,$domicilioNuevo,$domicilioViejo);
			if($actualizado){
				try{
					$datos['observaciones']="Domicilio anterior ingresado: ".$domicilioViejo." nuevo domicilio ingresado: ".$domicilioNuevo;
					$datos['cambio_dom']=1;
					$this->servicioHistorial->crear($tramite, $datos);
					return true;
				}catch(\Exception $e){
					\Log::info("Problema al crear el historial para actualización de domicilio.", array($e));
					return false;
				}
			}else{
				return false;
			}
		}

		/**
		 * Guarda un tramite de cambio de domicilio con los datos pasados
		 * por parámetro.
		 * @param Entidad $tramite
		 */
		public function cargarDomicilio($datos, &$datos_mail) {
		 	$datos['id_estado_tramite']= 0;
		 	$datos['id_tipo_tramite']= 1;//ver el nº
		 	$datos['observaciones']=$datos['motivo_cd'];

		 	$tramite = null;
		 	if(\Session::has('tramiteNuevoPermiso')){
		 		$tramiteNuevoPermiso=\Session::get('tramiteNuevoPermiso');
		 		$tramite = TramiteRepo::buscarPorId($tramiteNuevoPermiso['nro_tramite']);
		 		TramiteRepo::modificarCampoDetalleCompleto($tramite->nroTramite, 1);
		 		TramiteRepo::modificarObservaciones($tramite->nroTramite, $datos['observaciones']);
		 	}else{
				$tramite=self::armaTramite($datos);
				$tramite = TramiteRepo::crear($tramite);		 		
		 	}
			$domicilio=self::armaDomicilio($tramite,$datos);

			$domicilio=DomicilioRepo::cargarDomicilio($domicilio);

			$ds = DIRECTORY_SEPARATOR;

			//$directorioAdjuntos = public_path().$ds."upload".$ds."CD_".$tramite->nroPermiso;
			$directorioAdjuntos = public_path().$ds."upload".$ds."CD_".$datos['permiso'];
			if (\File::isDirectory($directorioAdjuntos)){//si existe el directorio
				/**Renombramos el directorio para que sea específico del trámite**/
				//$nuevoNombre= public_path().$ds."upload".$ds."CD_".$tramite->nroTramite.'_'.$tramite->nroPermiso;
				$nuevoNombre= public_path().$ds."upload".$ds."CD_".$tramite->nroTramite.'_'.$datos['permiso'];
				rename($directorioAdjuntos, $nuevoNombre);
				$directorioAdjuntos = $nuevoNombre.$ds.'ADJUNTOS';
				if (is_dir($directorioAdjuntos)){
					$archivos = \File::allFiles($directorioAdjuntos);
					foreach ($archivos as $archivo)
					{	
						$ruta = $directorioAdjuntos.$ds.$archivo->getFilename();
						$adjuntos[] = self::armaAdjunto($tramite->nroTramite,$tramite->nroPermiso, $ruta);
					}
					
					AdjuntoTramiteRepo::cargarAdjuntos($adjuntos);
				}
				
			}
			/**datos necesarios para el email**/
			$datos_mail['nroTramite']=$tramite->nroTramite;
			$datos_mail['tipoTramite']=$tramite->tipoTramite['nombre_tramite'];
			$datos_mail['agente']=$tramite->agente;
			$datos_mail['subagente']=$tramite->subAgente;
			$datos_mail['nro_permiso']=$tramite->nroPermiso;

			//coloco en el historial el trámite
			$this->servicioHistorial->crear($tramite->nroTramite, $datos);
			$result ="Nº de seguimiento: ";
			return $result;
		}
		

		/**
		 * Guarda un plan de cambio de domicilio con los datos pasados
		 * por parámetro.
		 * @param Entidad $tramite
		 */
		public function cargarPlan($datos, $nroTramite) {
			$datos['nroTramite']=$nroTramite;
			$plan=self::armaPlan($datos, false);

			$plan=DomicilioRepo::cargarPlan($plan);

			if($plan){
				$result="Plan cargado";
			}else{
				$result="No se pudo cargar el plan";
			}
			
			return $result;
		}

		/**
		 * Guarda un plan modificado con los datos pasados
		 * por parámetro.
		 * @param Entidad $tramite
		 */
		public function cargarPlanModificado($datos, $id_plan,$nroTramite) {
			$datos['nroTramite']=$nroTramite;
			$datos['id_plan']=$id_plan;
			
			$plan=self::armaPlan($datos, true);

			$plan=DomicilioRepo::modificaPlan($plan,$id_plan);

			if($plan){
				$result="Plan cargado";
			}else{
				$result="No se pudo cargar el plan";
			}
			
			return $result;
		}

		/**
		 * Guarda un tramite de cambio de dependencia con los datos pasados
		 * por parámetro.
		 * @param Entidad $tramite
		 */
		public function cargarDependencia($datos, &$datos_mail) {
		 	$datos['id_estado_tramite']= 0;
		 	$datos['id_tipo_tramite']= 4;//ver el nº
		 	$datos['observaciones']=$datos['motivo_cr'];

		 	$tramite = null;
		 	if(\Session::has('tramiteNuevoPermiso')){
		 		$tramiteNuevoPermiso=\Session::get('tramiteNuevoPermiso');
		 		$tramite = TramiteRepo::buscarPorId($tramiteNuevoPermiso['nro_tramite']);
		 		TramiteRepo::modificarCampoDetalleCompleto($tramite->nroTramite, 1);
		 		TramiteRepo::modificarObservaciones($tramite->nroTramite, $datos['observaciones']);
		 	}else{
				$tramite=self::armaTramite($datos);
				$tramite = TramiteRepo::crear($tramite);		 		
		 	}
			
			//$nro_red=$datos['nro_red'];			

			$dependencia=self::armaDependencia($tramite,$datos);
			$dependencia=DependenciaRepo::cargarDependencia($dependencia);
			$ds = DIRECTORY_SEPARATOR;

			$directorioAdjuntos = public_path().$ds."upload".$ds."CR_".$tramite->nroPermiso;
			if (\File::isDirectory($directorioAdjuntos)){//si existe el directorio
				/**Renombramos el directorio para que sea específico del trámite**/
				$nuevoNombre= public_path().$ds."upload".$ds."CR_".$tramite->nroTramite.'_'.$tramite->nroPermiso;
				rename($directorioAdjuntos, $nuevoNombre);
				$directorioAdjuntos = $nuevoNombre.$ds.'ADJUNTOS';
				if (is_dir($directorioAdjuntos)){
					$archivos = \File::allFiles($directorioAdjuntos);
					foreach ($archivos as $archivo)
					{	
						$ruta = $directorioAdjuntos.$ds.$archivo->getFilename();
						$adjuntos[] = self::armaAdjunto($tramite->nroTramite,$tramite->nroPermiso, $ruta);
					}
					
					AdjuntoTramiteRepo::cargarAdjuntos($adjuntos);
				}
			}
			/**datos necesarios para el email**/
			$datos_mail['nroTramite']=$tramite->nroTramite;
			$datos_mail['tipoTramite']=$tramite->tipoTramite['nombre_tramite'];
			$datos_mail['agente']=$tramite->agente;
			$datos_mail['subagente']=$tramite->subAgente;
			$datos_mail['nro_permiso']=$tramite->nroPermiso;

			//coloco en el historial el trámite
			$this->servicioHistorial->crear($tramite->nroTramite, $datos);
			$result ="Nº de seguimiento: ";
			return $result;
		}

		/**
		 * Guarda un tramite de cambio de categoria con los datos pasados
		 * por parámetro.
		 * @param Entidad $tramite
		 */
		public function cargarCategoria($datos, &$datos_mail) {
		 	$datos['id_estado_tramite']= 0;
		 	$datos['id_tipo_tramite']= 2;//ver el nº
		 	$datos['observaciones']=$datos['motivo_cc'];

		 	$tramite = null;
			if(\Session::has('tramiteNuevoPermiso')){
		 		$tramiteNuevoPermiso=\Session::get('tramiteNuevoPermiso');
		 		$tramite = TramiteRepo::buscarPorId($tramiteNuevoPermiso['nro_tramite']);
		 		TramiteRepo::modificarCampoDetalleCompleto($tramite->nroTramite, 1);
		 		TramiteRepo::modificarObservaciones($tramite->nroTramite, $datos['observaciones']);
		 	}else{
				$tramite=self::armaTramite($datos);
				$tramite = TramiteRepo::crear($tramite);		 		
		 	}
			
			
			$categoria=self::armaCategoria($tramite,$datos);
			$categoria=CategoriaRepo::cargarCategoria($categoria);
			$ds = DIRECTORY_SEPARATOR;

			$directorioAdjuntos = public_path().$ds."upload".$ds."CC_".$tramite->nroPermiso;
			if (\File::isDirectory($directorioAdjuntos)){//si existe el directorio
				/**Renombramos el directorio para que sea específico del trámite**/
				$nuevoNombre= public_path().$ds."upload".$ds."CC_".$tramite->nroTramite.'_'.$tramite->nroPermiso;
				rename($directorioAdjuntos, $nuevoNombre);
				$directorioAdjuntos = $nuevoNombre.$ds.'ADJUNTOS';
				$archivos = \File::allFiles($directorioAdjuntos);
				foreach ($archivos as $archivo)
				{	
					$ruta = $directorioAdjuntos.$ds.$archivo->getFilename();
					$adjuntos[] = self::armaAdjunto($tramite->nroTramite,$tramite->nroPermiso, $ruta);
				}
				
				AdjuntoTramiteRepo::cargarAdjuntos($adjuntos);
			}

			/**datos necesarios para el email**/
			$datos_mail['nroTramite']=$tramite->nroTramite;
			$datos_mail['tipoTramite']=$tramite->tipoTramite['nombre_tramite'];
			$datos_mail['agente']=$tramite->agente;
			$datos_mail['subagente']=$tramite->subAgente;
			$datos_mail['nro_permiso']=$tramite->nroPermiso;
			
			\Log::info("ANALIZE_EMAIL - cargarCategoria datos_email: ", array($datos_mail));
			\Log::info("ANALIZE_EMAIL - cargarCategoria datos: ", array($datos));

			//coloco en el historial el trámite
			$this->servicioHistorial->crear($tramite->nroTramite, $datos);
			//$result ="Nº de seguimiento: ";
			$result ="Nº de seguimiento: ".$tramite->nroTramite;
			return $result;
		}

		/**
		* Función que llama a la que verifica si tiene permiso de hacer
		* el cambio de categoría
		**/
		public function okAprobar($permiso){
			$ok=CategoriaRepo::okAprobar($permiso);
			return $ok;
		}

		public function buscarCategoriaCBU($nroTramite){
			$cbu=CategoriaRepo::buscarCategoriaCBU($nroTramite);
			return $cbu;
		}

		public function buscarPersona($dni,$cuit,$tipo_doc){
			$persona = $this->servicioPersona->buscarPersona($dni,$cuit,$tipo_doc);
			return $persona;
		}

		/**
		 * Guarda un tramite de cambio de titular / incorporación de cotitular con los datos pasados
		 * por parámetro.
		 * @param Entidad $tramite
		 */
		public function cargarTitular($datos, &$datos_mail, $tipoTramite) {
		 	$datos['id_estado_tramite']= 0;
		 	$datos['id_tipo_tramite']= $tipoTramite;
		 	$datos['observaciones']=$datos['motivo_ct'];
		 	$ds = DIRECTORY_SEPARATOR;
		 	//creo la persona ->si ya existe se modifican los campos
		 	$persona = $this->servicioPersona->crear($datos);
		 	$tramite = null;
		 	if(\Session::has('tramiteNuevoPermiso')){
		 		$tramiteNuevoPermiso=\Session::get('tramiteNuevoPermiso');
		 		$tramite = TramiteRepo::buscarPorId($tramiteNuevoPermiso['nro_tramite']);
		 		TramiteRepo::modificarCampoDetalleCompleto($tramite->nroTramite, 1);
		 		TramiteRepo::modificarObservaciones($tramite->nroTramite, $datos['observaciones']);
		 	}else{
				$tramite=self::armaTramite($datos);
				$tramite = TramiteRepo::crear($tramite);		 		
		 	}

	 		/**datos necesarios para el email**/
			$datos_mail['nroTramite']=$tramite->nroTramite;
			$datos_mail['tipoTramite']=$tramite->tipoTramite['nombre_tramite'];
			$datos_mail['agente']=$tramite->agente;
			$datos_mail['subagente']=$tramite->subAgente;
			$datos_mail['nro_permiso']=$tramite->nroPermiso;

			$titular=self::armatitular($tramite,$datos,$persona);
			$separador = "CT_";
			if($tipoTramite == 8){
				$separador = "IT_";
			}

			if(isset($titular)){

				$titular=TitularRepo::cargarTitular($titular);
				
				$directorioAdjuntos = public_path().$ds."upload".$ds.$separador.$tramite->nroPermiso;
				if (\File::isDirectory($directorioAdjuntos)){//si existe el directorio
					/**Renombramos el directorio para que sea específico del trámite**/
					$nuevoNombre= public_path().$ds."upload".$ds.$separador.$tramite->nroTramite.'_'.$tramite->nroPermiso;
					//self::delTree($nuevoNombre);
					rename($directorioAdjuntos, $nuevoNombre);
					$directorioAdjuntos = $nuevoNombre.$ds.'ADJUNTOS';
					$archivos = \File::allFiles($directorioAdjuntos);
					foreach ($archivos as $archivo)
					{	
						$ruta = $directorioAdjuntos.$ds.$archivo->getFilename();
						$adjuntos[] = self::armaAdjunto($tramite->nroTramite,$tramite->nroPermiso, $ruta);
					}
					
					AdjuntoTramiteRepo::cargarAdjuntos($adjuntos);
				}

				/**datos necesarios para el email**
				$datos_mail['nroTramite']=$tramite->nroTramite;
				$datos_mail['tipoTramite']=$tramite->tipoTramite['nombre_tramite'];
				$datos_mail['agente']=$tramite->agente;
				$datos_mail['subagente']=$tramite->subAgente;
				$datos_mail['nro_permiso']=$tramite->nroPermiso;*/

				//coloco en el historial el trámite
				$this->servicioHistorial->crear($tramite->nroTramite, $datos);
				$result ="Nº de seguimiento: ";
				return $result;
			}else{
				return null;
			}
		}



		/**
		* Cancela el trámite cuyo id llega como parámetro
		**/
		public function cancelarTramite($datos, &$datos_mail){

			$tramiteOriginal = $this->buscarPorId($datos['nro_tramite']);

			$modelo = TramiteRepo::cancelarTramite($datos['nro_tramite']);

			if(!is_null($modelo)){//se canceló correctamente
				$datos['estadoIni'] = $tramiteOriginal['id_estado_tramite'];
				$datos['id_estado_tramite']=8;
				$datos['observaciones']="Cancelación del trámite."; 
				$this->servicioHistorial->crear($datos['nro_tramite'], $datos);

				/**datos necesarios para el email**/
				$datos_mail['nroTramite']=$tramiteOriginal['nro_tramite'];
				$datos_mail['tipoTramite']=$tramiteOriginal['nombre_tramite'];
				$datos_mail['agente']=$tramiteOriginal['agente'];
				$datos_mail['subagente']=$tramiteOriginal['subagente'];
				$datos_mail['nro_permiso']=$tramiteOriginal['nro_permiso'];

				$modelo="El trámite con el nº de seguimiento: ".$datos['nro_tramite'].", se ha cancelado correctamente.";
			}
				return $modelo;	
			
		}

/**********************************************Para consulta****************************************************************/
		/**
	     * Realiza una búsqueda, los items del resultado son stdClass
	     * 
	     * @return datos Resultado de la búsqueda
	     */
	    public function listar($filtros) {
	    	$filtro=$this->armarFiltroTramite($filtros);
	        $count = 0;        
	      
	        $tramites = TramiteRepo::listadoTramites($filtro, $count);

	        if(count($tramites)!=0){
		        foreach ($tramites as $tramite) {
		        	$tipoTramite=$tramite->tipoTramite->toArray();
		        	$estadoTramite=$tramite->estadoTramite->toArray();
		        	$ingreso = $tramite->ingreso;
		        	$tramiteArray=TramiteRepo::arregloTramite($tramite);
					$tramiteArray['nuevo_permiso']=$tramite->nuevo_permiso;
		        	$tramiteArray['fecha']=Carbon::parse($tramiteArray['fecha'])->format('d/m/Y');	
					$tramiteArray['fecha_ultima_modificacion']=$this->servicioHistorial->fechaUltimaModificacion($tramiteArray['nro_tramite']);	
		        	$tramiteArray['nombre_tramite']=$tipoTramite['nombre_tramite'];
		        	if($tramite->nuevo_permiso)
		        		$tramiteArray['titulo_tramite']=$tipoTramite['nombre_alta_tramite'];
		        	else
		        		$tramiteArray['titulo_tramite']=$tipoTramite['nombre_tramite'];
		        	$tramiteArray['descripcion_estado']=$estadoTramite['descripcion_tramite'];
					$tramiteArray['ingreso']=$ingreso;		        		    		
		        	$resultado[] = $tramiteArray;
		        }

		        $datos['tramites'] = $resultado;
	     	}else{
	     		$datos['tramites'] = array();
	     	}

	        
	        $datos['pagina'] = $filtros['pagina'];

	        if($datos['pagina']!="0"){
	        	$datos['cantidadPaginas']=ceil($count/$filtros['porPagina']);	
	        }else{
	        	$datos['pagina']=1;
	        	$datos['cantidadPaginas']=1;
	        }
	        

	        return $datos;
	    }	

	    public function armarFiltroTramite($filtros){

	    	// Paginación de la tabla
	        $filtro = new FiltroTramite();
	        $filtro->setNumeroPagina($filtros['pagina']);
	        $filtro->setPorPagina($filtros['porPagina']);
	        //ver por qué los va a ordenar
	        if($filtros['campoOrden']==0){//estado-fecha
	        	$filtro->addCriterioOrden('tramites.id_estado_tramite', $filtros['tipoOrden']);
	        	$filtro->addCriterioOrden('tramites.fecha', 0);//0=desc 1=asc
	        }else{//fecha-estado
	        	$filtro->addCriterioOrden('tramites.fecha', 0);
	        	$filtro->addCriterioOrden('tramites.id_estado_tramite', $filtros['tipoOrden']);
	        }
	        
	        $filtro->tipoTramite = $filtros['tipoTramite'];
	        $filtro->estadoTramite = $filtros['estados'] ;
	        $filtro->permiso = $filtros['permiso'];       
	        $filtro->fechaDesde = $filtros['fechaDesde'];
	        $filtro->fechaHasta = $filtros['fechaHasta'];
	        $filtro->agente = $filtros['agente'];
	     	$filtro->subAgente = $filtros['subAgente'];
	     	$filtro->ddv_nro = $filtros['ddv_nro'];
	     	$filtro->mesaEntrada = $filtros['mesa_entrada'];
	     	$filtro->nuevoPermiso = $filtros['nuevoPermiso'];
			
	        return $filtro;
	    }

/***************************** Armar entidades *************************************/
		/**
		* Función que arma la entidad tramite con los datos
		* que le llegan por parámetro
		*/
		private function armaTramite($datos){
			$usr = \Session::get('usuarioLogueado');

			$tramite = new Tramite();

			$tramite->tipoTramite = $this->servicioTipoTramite->buscarPorId($datos['id_tipo_tramite']);//$datos['id_tipo_tramite'];//se saca del id del combobox
			$tramite->estadoTramite = EstadoTramiteRepo::buscarPorId_arreglo($datos['id_estado_tramite']);

			if(strtoupper($usr['nombreTipoUsuario'])!="CAS" && strtoupper($usr['nombreTipoUsuario'])!="CAS_ROS"){//si registra el mismo agente
				$tramite->usuario = $usr['id'];
				$tramite->nroPermiso = $usr['permiso'];
				$tramite->agente = $usr['agente'];
				$tramite->subAgente = $usr['subAgente'];
				$tramite->razonSocial = $usr['razonSocial'];
				$tramite->localidadAgencia = $usr['localidadAgencia'];
				$tramite->departamentoNombre =$usr['departamentoNombre'];
				$tramite->codigoPostalAgencia = $usr['codigoPostalAgencia'];
				$tramite->domicilioAgencia =  $usr['domicilioAgencia'];
				$tramite->nuevo_permiso=0;
			}else if(\Session::has('tramiteNuevoPermiso')){
				$agenteBolsa=self::obtenerAgenteBolsa();
				$redAgenteBolsa=self::obtenerRedAgenteBolsa();
				$domicilioAgenteBolsa=self::obtenerDireccionAgenteBolsa();
				$tramiteNuevoPermiso=\Session::get('tramiteNuevoPermiso');

				$tramite->usuario =$usr['id'];
				$tramite->nroPermiso = $tramiteNuevoPermiso['nro_permiso'];
				$tramite->agente = $redAgenteBolsa->id_red;
				$tramite->subAgente = 0;
				$tramite->razonSocial = $agenteBolsa->agente;
				
				$departamento =LocalidadRepo::buscarDepartamentoPorCP_SCP($domicilioAgenteBolsa->cp, $domicilioAgenteBolsa->scp);

				$localidad = LocalidadRepo::buscarPorCodigoPostal($domicilioAgenteBolsa->cp);
				
				$tramite->localidadAgencia = rtrim($localidad->nombre);
				$tramite->departamentoNombre =$departamento['nombre'];
				$tramite->codigoPostalAgencia = $domicilioAgenteBolsa->cp;
				$tramite->domicilioAgencia =  $domicilioAgenteBolsa->direccion;
				$tramite->rutaHistorialPDF = '';
				$tramite->nuevo_permiso=1;
				
			}else{//registra el cas para un agente/subagente en particular
				$usuarioTramite=\Session::get('usuarioTramite');
				$tramite->usuario = $usr['id'];
				$tramite->nroPermiso = $usuarioTramite['permiso'];
				$tramite->agente = $usuarioTramite['agente'];
				$tramite->subAgente = $usuarioTramite['subAgente'];
				$tramite->razonSocial = $usuarioTramite['razonSocial'];
				$tramite->localidadAgencia = $usuarioTramite['localidadAgencia'];
				$tramite->departamentoNombre =$usuarioTramite['departamentoNombre'];
				$tramite->codigoPostalAgencia = $usuarioTramite['codigoPostalAgencia'];
				$tramite->domicilioAgencia =  $usuarioTramite['domicilioAgencia'];
				$tramite->rutaHistorialPDF = '';
				$tramite->nuevo_permiso=0;
			}
			
			$tramite->detalle_completo=1;
			$tramite->fecha = \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
			$tramite->observaciones = $datos['observaciones'];
			$tramite->informado_crm=0;
			$tramite->pendienteInformar=0;
			if(array_key_exists('id_motivo_baja',$datos)){
				$tramite->id_motivo_baja=$datos['id_motivo_baja'];			
			}else{
				$tramite->id_motivo_baja=-1;
			}

			if($datos['id_tipo_tramite'] == 10){
				$tramite->fechaSuspHab = $this->formatos->fecha($datos['fechahasta']);				
			}else if($datos['id_tipo_tramite'] == 9){
				$tramite->fechaSuspHab = $this->formatos->fecha($datos['fechadesde']);	
		}
		
			if($datos['id_tipo_tramite'] == 6 || $datos['id_tipo_tramite'] == 7){
				$tramite->tipo_terminal = $datos['tipo_terminal'];				
			}
			\Log::info("ANALIZE_EMAIL - armaTramite: ", $tramite);
			return $tramite;
		}
		
		/**
		* Función que arma la entidad domicilio con los datos
		* que le llegan por parámetro
		*/
		private function armaDomicilio($tramite,$datos){
			$domicilio = new Domicilio();
			
			$domicilio->nroTramite = $tramite->nroTramite;
			$domicilio->direccionAnterior = $tramite->domicilioAgencia;
			$domicilio->localidadAnterior = $datos['localidad_actual_id']; //$tramite->localidadAgencia;
			$domicilio->direccionNueva = $datos['domicilio_comercial'];
			$domicilio->idLocalidadNueva = $datos['id_nueva_localidad'];//id
			$domicilio->localidadNueva =$datos['nombre_nueva_localidad'];//nombre
			$domicilio->cpScpLocalidadNueva = $datos['cp_scp'];
			$domicilio->idDepartamentoNuevo = $datos['departamento_id'];//id
			$domicilio->departamentoNuevo = $datos['nuevo_departamento'];//nombre
			$domicilio->referente = $datos['referente'];
			$domicilio->datos_contacto = $datos['datos_contacto'];
			return $domicilio;
		}


		/**
		* Función que arma la entidad plan con los datos
		* que le llegan por parámetro
		*/
		private function armaPlan($datos, $modifica){
			$plan = new Plan();
			
			$plan->nro_tramite = $datos['nroTramite'];
			$plan->superficie_codigo = $datos['superficie'];
			$plan->superficie_valor = SuiteCRMRepo::obtenerSuperficiePorId($datos['superficie']);        
			$plan->ubicacion_codigo = $datos['ubicacion'];   
			$plan->ubicacion_valor = SuiteCRMRepo::obtenerUbicacionPorId($datos['ubicacion']);              
			$plan->vidriera_codigo = $datos['vidriera'];
			$plan->vidriera_valor = SuiteCRMRepo::obtenerVidrieraPorId($datos['vidriera']);
			$plan->persona_contacto = $datos['persona_contacto'];
			$plan->telefono_contacto = $datos['telefono_contacto'];
			$plan->cant_empleados = $datos['cant_empleados'];       
			$plan->horario_codigo = $datos['horario'];
			$plan->horario_valor = SuiteCRMRepo::obtenerHorarioPorId($datos['horario']);
			$plan->rubros_agencia = $datos['rubros_agencia'];
			$plan->rubros_amigos = $datos['rubros_amigos'];
			$plan->rubros_otros = $datos['rubros_otros'];
			
			$ds = DIRECTORY_SEPARATOR;
			$permiso = $datos['permisoPlan'];

			if($modifica){
				//$directorioFotos = \public_path().$ds."upload".$ds."CD_".$datos['nroTramite'].'_'.$permiso.$ds;
				$directorioFotos = \Config::get('habilitacion_config/config.urlDirectorioFotosPE')."upload".$ds."CD_".$datos['nroTramite'].'_'.$permiso.$ds;
				if($datos['foto_f']!=""){					
					$plan->foto_f=$directorioFotos.$permiso."_F.".$datos['foto_f'];
				}

				if($datos['foto_i']!=""){
					$plan->foto_i=$directorioFotos.$permiso."_I.".$datos['foto_i'];
				}

			}else{
				if(\Session::has('usuarioTramite')){
					
					//$directorioFotos = \public_path().$ds.'upload'.$ds.'CD_'.$permiso.$ds;
					$permiso = \Session::get('usuarioTramite')['permiso'];

					$directorioFotos =public_path().$ds.'upload'.$ds.'CD_'.$permiso.$ds;
					if (\File::isDirectory($directorioFotos)){//si existe el directorio
						/**Renombramos el directorio para que sea específico del trámite**/
						$nuevoNombre= public_path().$ds."upload".$ds."CD_".$datos['nroTramite'].'_'.$permiso.$ds;
						rename($directorioFotos, $nuevoNombre);
						$directorioFotos = $nuevoNombre;
					}else{
						$directorioFotos =\Config::get('habilitacion_config/config.urlDirectorioFotosPE')."upload".$ds."CD_".$datos['nroTramite'].'_'.$permiso.$ds;
						if (!\File::isDirectory($directorioFotos)){
							\File::makeDirectory($directorioFotos, 0777, true);
						}
						
					}
					
				}else{					
					$permiso = $datos['permisoPlan'];
					
					$directorioFotos = public_path().$ds.'upload'.$ds.'CD_'.$permiso.$ds;
					if (\File::isDirectory($directorioFotos)){//si existe el directorio
						/**Renombramos el directorio para que sea específico del trámite**/
						$nuevoNombre=public_path().$ds."upload".$ds."CD_".$datos['nroTramite'].'_'.$permiso.$ds;
						rename($directorioFotos, $nuevoNombre);
						$directorioFotos = $nuevoNombre;
					}else{
						$directorioFotos =public_path().$ds."upload".$ds."CD_".$datos['nroTramite'].'_'.$permiso.$ds;
						if (!\File::isDirectory($directorioFotos)){
							\File::makeDirectory($directorioFotos, 0777, true);
						}
					}
				}
				$directorioFotos = \Config::get('habilitacion_config/config.urlDirectorioFotosPE')."upload".$ds."CD_".$datos['nroTramite'].'_'.$permiso.$ds;
				$plan->foto_f=$directorioFotos.$permiso."_F.".$datos['foto_f'];
				$plan->foto_i=$directorioFotos.$permiso."_I.".$datos['foto_i'];
			}
			
			

			if(array_key_exists('preg_1', $datos)){
				$plan->zona_1 = $datos['preg_1'];//es opcional
			}else{
				$plan->zona_1 = "";//es opcional
			}
			
			if(array_key_exists('preg_2', $datos)){
				$plan->zona_2 = $datos['preg_2'];//es opcional
			}else{
				$plan->zona_2 = "";//es opcional
			}
			
			$plan->car_zona = implode(",",$datos['caracteristicas']);

			$plan->nivel_circulacion_codigo = $datos['nivel_circulacion_codigo'];
			$plan->nivel_circulacion_valor = SuiteCRMRepo::obtenerCirculacionPorId($datos['nivel_circulacion_codigo']);
			$plan->nivel_socioeconomico_codigo = $datos['nivel_socioeconomico_codigo'];
			$plan->nivel_socioeconomico_valor = SuiteCRMRepo::obtenerSocioEconomicoPorId($datos['nivel_socioeconomico_codigo']);
			$plan->mes_inicio = $datos['mes_i'];
			$plan->trimestre_1 = $datos['tabla_val'][3];
			$plan->trimestre_1_anioi = $datos['tabla_val'][1];
			$plan->trimestre_1_aniof = $datos['tabla_val'][2];
			$plan->trimestre_1_ventas = $datos['venta_1'];

			$plan->trimestre_2 = $datos['tabla_val'][8];
			$plan->trimestre_2_anioi = $datos['tabla_val'][6];
			$plan->trimestre_2_aniof = $datos['tabla_val'][7];
			$plan->trimestre_2_ventas = $datos['venta_2'];

			$plan->trimestre_3 = $datos['tabla_val'][13];
			$plan->trimestre_3_anioi = $datos['tabla_val'][11];
			$plan->trimestre_3_aniof = $datos['tabla_val'][12];
			$plan->trimestre_3_ventas = $datos['venta_3'];

			$plan->trimestre_4 = $datos['tabla_val'][18];
			$plan->trimestre_4_anioi = $datos['tabla_val'][16];
			$plan->trimestre_4_aniof = $datos['tabla_val'][17];
			$plan->trimestre_4_ventas = $datos['venta_4'];
			return $plan;
		}

		/**
		* Busca el plan correspondiente para ser modificado
		**/
		public function obtenerPlan($nro_tramite){
			$plan = DomicilioRepo::obtenerPlan($nro_tramite);
			return $plan;
		}

		/**
		* Busca los datos de la agencia en la vista de agencias
		**/
		public function obtenerDatosAgencia($permiso){
			$datosAgencia = DomicilioRepo::obtenerDatosAgencia($permiso);
			if(!is_null($datosAgencia) && count($datosAgencia)!=0){
				$departamento=LocalidadRepo::buscarDepartamentoPorCP_SCP($datosAgencia['codigo_postal'], $datosAgencia['subcodigo_postal']);
				$datosAgencia['id_departamento']=$departamento['id'];
				$datosAgencia['nombre_departamento']=$departamento['nombre'];
			}else{
				return null;
			}
			return $datosAgencia;
		}

		/**
		* Buscar domicilio nuevo según nro_tramite
		**/
		public function obtenerNuevoDomicilio($nro_tramite){
			$domicilio = DomicilioRepo::obtenerNuevoDomicilio($nro_tramite);
			return $domicilio;
		}

		/**
		* Función que arma la entidad dependencia con los datos
		* que le llegan por parámetro
		*/
		private function armaDependencia($tramite,$datos){
			$dependencia = new Dependencia();
			$dependencia->nro_tramite = $tramite->nroTramite;
			$dependencia->nro_red_anterior = $tramite->agente;
			$dependencia->nro_pto_vta_anterior = $tramite->subAgente; 
			$dependencia->nro_red_actual = $datos['nro_red']; 
			$dependencia->nro_pto_vta_nuevo =$datos['nro_subagente'];
			$dependencia->motivo_cambio = $datos['motivo_cr'];
						
			return $dependencia;
		}

		/**
		* Función que arma la entidad categoria con los datos
		* que le llegan por parámetro
		*/
		private function armaCategoria($tramite,$datos){
			$categoria = new Categoria();
			
			$categoria->nro_tramite = $tramite->nroTramite;

			if($datos['categorias']==0){//agente
				$categoria->categoria_anterior = 1;//era subagente
				$categoria->nro_red_anterior = $tramite->agente;
				$categoria->nro_pto_vta_anterior = $tramite->subAgente;
				$categoria->nro_red_nueva = $tramite->agente;
				$categoria->nro_pto_vta_nuevo = 0;
				if(isset($datos['cbu']))
					$categoria->cbu = $datos['cbu']; 
				else
					$categoria->cbu = ''; 

				$categoria->nro_pto_vta_nuevo = 0;

			}else{//subagente
				$categoria->categoria_anterior = 0;//era agente
				$categoria->nro_red_anterior = $tramite->agente;
				$categoria->nro_pto_vta_anterior = $tramite->subAgente;

				if(isset($datos['cbu']))
					$categoria->cbu = $datos['cbu']; 
				else
					$categoria->cbu = '';
				
				$categoria->nro_pto_vta_nuevo = $datos['nro_subagente'];			
			}
			
			$categoria->nro_red_nueva = $datos['nro_red'];
			$categoria->categoria_nueva = $datos['categorias']; 
			$categoria->motivo_cambio = $datos['motivo_cc'];

			$categoria->idLocalidad = $datos['id_localidad'];//id
			$categoria->localidad =$datos['nombre_localidad'];//nombre
			$categoria->cpScpLocalidad = $datos['cp_scp'];
			
			\Log::info("ANALIZE_EMAIL - armaCategoria: ", $categoria);
			
			return $categoria;
		}



		/**
		* Función que arma la entidad titular con los datos
		* que le llegan por parámetro
		*/
		private function armaTitular($tramite,$datos, $persona){
			$titular = new Titular();
			$datosAgencia = $this->obtenerDatosAgencia($tramite->nroPermiso);
			if(isset($datosAgencia)){
				$titular->nro_tramite = $tramite->nroTramite;
				$titular->id_titular_viejo = $datosAgencia['id_titular'];//id del suitecrm
				$titular->id_titular_nuevo = $persona['id'];//nuevo titular
				$titular->nro_permiso = $datos['permiso'];
				$titular->motivo_cambio = $datos['motivo_ct'];				
				return $titular;
			}else{
				return true;
			}

		}

		/**
		* Función que arma la entidad titular con los datos
		* que le llegan por parámetro
		*/
		private function armaAdjunto($nroTramite,$permiso, $ruta){
			$adjuntoTramite = new AdjuntoTramite();
			$adjuntoTramite->nroTramite = $nroTramite;
			$adjuntoTramite->rutaAdjunto = $ruta; 
			$adjuntoTramite->permiso = $permiso;  
			return $adjuntoTramite;
		}
		
		/***
		*	Función que se encarga de verificar si en la lista
		*	de trámites, existe uno del tipo seleccionado, para
		*	el usuario que llega como parámetro y está en un 
		*	estado diferente a rechazado/aprobado/cancelado
		**/
		public function verificar_existencia_tramite($agente, $subagente, $permiso, $tipoTramite){
			return TramiteRepo::verificar_existencia_tramite($agente, $subagente, $permiso, $tipoTramite);
		}

		// Busca los detalles de c/u de los recuadros del tablero		
		public function buscarDetalleRecepRosario(){
			$recepcionRos = TramiteRepo::buscarDetalleRecepRosario();
			return $recepcionRos;
		}
		public function buscarDetalleFirmaVice(){
			$firmaVice = TramiteRepo::buscarDetalleFirmaVice();
			return $firmaVice;
		}		
		public function buscarDetalleRecepStaFe(){
			$recepcionStaFe = TramiteRepo::buscarDetalleRecepStaFe();
			return $recepcionStaFe;
		}
		public function buscarDetalleHab30(){
			$hab30 = TramiteRepo::buscarDetalleHab30();
			return $hab30;
		}	
		public function buscarDetalleHab3060(){
			$hab3060 = TramiteRepo::buscarDetalleHab3060();
			return $hab3060;
		}		
		public function buscarDetalleHab60(){
			$hab60 = TramiteRepo::buscarDetalleHab60();
			return $hab60;
		}
		public function buscarDetalleIniPer(){
			$iniPer = TramiteRepo::buscarDetalleIniPer();
			return $iniPer;
		}	
		public function buscarDetalleDevueltosDoc(){
			$devueltosDoc = TramiteRepo::buscarDetalleDevueltosDoc();
			return $devueltosDoc;
		}

		public function buscarDetalleAprobPendientes(){
			$aprobPend = TramiteRepo::buscarDetalleAprobPendientes();
			return $aprobPend;
		}

		/**
		* Función que verifica si un trámite de cambio de titular
		* como subagencia, tiene un trámite pendiente de informar
		* antes de poder ser aprobado.
		**/
		public function tieneTramitePI($nroTramite,$permiso){
			$existeTramitePI=TramiteRepo::tieneTramitePI($nroTramite,$permiso);
			return $existeTramitePI;
		}

		/******************ADMINISTRACIÓN MÁQUINAS****************/

		/***********************************************
		* Busca los tipos de terminal para incorporar. *
		************************************************/
		public function buscarTiposTerminal(){
			$tipos=SuiteCRMRepo::buscarTiposTerminal();
			return $tipos;
		}
		/************************************************
		* Busca las terminales del permiso para retirar *
		*************************************************/
		public function buscarTerminal($permiso){
			$lista=SuiteCRMRepo::buscarTerminal($permiso);
			return $lista;
		}

		/************************************************************
		* Función que genera el trámite de incorporación de máquina *
		*************************************************************/
		public function incorporarMaquina($tipo_terminal, $observaciones){
			$datos['id_tipo_tramite']=6;
			$datos['id_estado_tramite']=10;
			$datos['tipo_terminal']=$tipo_terminal;
			$datos['observaciones']=$observaciones;
			$tramite = self::armaTramite($datos);
			$tramite = TramiteRepo::crear($tramite);

			/**datos necesarios para el email**/
			$datos_mail['nroTramite']=$tramite->nroTramite;
			$datos_mail['tipoTramite']=$tramite->tipoTramite['nombre_tramite'];
			$datos_mail['agente']=$tramite->agente;
			$datos_mail['subagente']=$tramite->subAgente;
			$datos_mail['nro_permiso']=$tramite->nroPermiso;
			$datos_mail['tipo_terminal']=$tipo_terminal;
			$datos_mail['observaciones']=$observaciones;
			$datos_mail['id_tipo_tramite']=$datos['id_tipo_tramite'];
			//coloco en el historial el trámite
			$datos['estadoIni']=0;
			$datos['estadoIni']=10;
			
			$this->servicioHistorial->crear($tramite->nroTramite, $datos);
			return $datos_mail; 
		}

		/***********************************************************************
		* Función que verifica si el permiso puede retirar máquinas            *   
		* Si es la última no se permite retiro. Debe darse de baja el permiso. *
		************************************************************************/
		public function puedeRetirar($permiso){
			$puedeRetirar = SuiteCRMRepo::puedeRetirar($permiso);
			return $puedeRetirar;
		}

		/**************************************************
		* Función que genera trámite de retiro de máquina *
		***************************************************/
		public function retirarMaquina($id_terminal, $observaciones){
			$datos['id_tipo_tramite']=7;
			$datos['id_estado_tramite']=10;
			$datos['tipo_terminal']=$id_terminal;
			$datos['observaciones']=$observaciones;
			$tramite = self::armaTramite($datos);
			$tramite = TramiteRepo::crear($tramite);

			/**datos necesarios para el email**/
			$datos_mail['nroTramite']=$tramite->nroTramite;
			$datos_mail['tipoTramite']=$tramite->tipoTramite['nombre_tramite'];
			$datos_mail['agente']=$tramite->agente;
			$datos_mail['subagente']=$tramite->subAgente;
			$datos_mail['nro_permiso']=$tramite->nroPermiso;
			$datos_mail['tipo_terminal']=$id_terminal;
			$datos_mail['observaciones']=$observaciones;
			$datos_mail['id_tipo_tramite']=$datos['id_tipo_tramite'];
			

			//coloco en el historial el trámite
			$datos['estadoIni']=0;
			$datos['estadoIni']=10;
			$this->servicioHistorial->crear($tramite->nroTramite, $datos);
			return $datos_mail; 
		}

/*******************************PERMISOS**************************/
	/*****************Baja de permisos*************/
		public function solicitudBaja($permiso,$motivo,$observaciones, $usuarioTramite){
			$datos['id_tipo_tramite']=5;
			$datos['id_estado_tramite']=10;
			$datos['id_motivo_baja']=$motivo;
			$datos['observaciones']=$observaciones;
			$datos['permiso']=$permiso;
			$tramite = self::armaTramite($datos);
			$tramite = TramiteRepo::crear($tramite);

			//coloco en el historial el trámite
			$datos['estadoIni']=0;
			$datos['estadoIni']=10;
			$this->servicioHistorial->crear($tramite->nroTramite, $datos);
			return true; 
		}

		/**
		* Función que llama a la que verifica si existe
		* una solicitud de baja para el permiso.
		**/
		public function existeSolicitudBaja($permiso){
			$ok=TramiteRepo::existeSolicitudBaja($permiso);
			return $ok;
		}
		
		/**
		* Función que devuelve los motivos por los que se puede
		* dar de baja un permiso
		*/
		public function listaMotivosBaja(){
			$lista=SuiteCRMRepo::listaMotivosBaja();
			return $lista;
		}

		/**Funciones para obtener los datos base para alta permiso**/
		public function obtenerAgenteBolsa(){
			$agenteBolsa = TramiteRepo::obtenerAgenteBolsa();
			return $agenteBolsa;
		}
		
		public function obtenerDireccionAgenteBolsa(){
			$agenteBolsa = TramiteRepo::obtenerDireccionAgenteBolsa();
			return $agenteBolsa;	
		}

		public function obtenerRedAgenteBolsa(){
			$agenteBolsa = TramiteRepo::obtenerRedAgenteBolsa();
			return $agenteBolsa;
		}

		/**** Alta de permisos ****/
		public function altaPermisos($cant,$resol, $fecha, $usuario){
			try{
				
				for($i=0; $i<$cant; $i++){
					//genero el nro de permiso
					$nroPermiso=TramiteRepo::generarNroPermiso();
					$nroPermiso = $nroPermiso->nro_permiso;

					//genero accounts y accounts_cstm
					$idAccounts=TramiteRepo::generarAccounts($resol, $fecha, $nroPermiso, $usuario['nombreUsuario']);

					//genero el permiso - age_permiso
					$ok=TramiteRepo::generarPermiso($resol, $fecha, $nroPermiso, $usuario, $idAccounts);
					
				}
					return $ok;
			}catch(\Exception $e){
				\Log::info('Error alta permisos - Tramites.');
				\Log::error($e->getMessage());
				return false;
			}
		}

	/***************************
	* Adjudicación de permisos *
	****************************/
	public function adjudicacionPermiso($nroPermiso, $tipoAdjudicacion){
		//genero los trámites cambioTitular, cambioDomicilio y cambioDependencia

					$agenteBolsa=TramiteRepo::obtenerAgenteBolsa();;
					$direccionAgenteBolsa=TramiteRepo::obtenerDireccionAgenteBolsa();
					$redAgenteBolsa=TramiteRepo::obtenerRedAgenteBolsa();

					$datosAgenteBolsa['permiso']=$nroPermiso;
					$datosAgenteBolsa['agente']=$redAgenteBolsa->id_red;

					if(strcasecmp($tipoAdjudicacion,'agencia')==0)
						$datosAgenteBolsa['subAgente'] = 1;
					else
						$datosAgenteBolsa['subAgente'] = 0;
					$datosAgenteBolsa['razonSocial']= $agenteBolsa->agente;
					$datosAgenteBolsa['localidadAgencia']= $direccionAgenteBolsa->localidad;
					$datosAgenteBolsa['departamentoNombre'] = $direccionAgenteBolsa->departamento;
					$datosAgenteBolsa['codigoPostalAgencia'] = $direccionAgenteBolsa->cp;
					$datosAgenteBolsa['domicilioAgencia'] =$direccionAgenteBolsa->direccion;

					//Trámite cambio domicilio
					$datosDomicilio['id_estado_tramite']= 0;
				 	$datosDomicilio['id_tipo_tramite']= 1;
				 	$datosDomicilio['observaciones']='Domicilio Permiso Nuevo';
				 	
					$tramiteDomicilio=self::armaTramitePermiso($datosDomicilio, $datosAgenteBolsa);

					$tramiteDomicilio = TramiteRepo::crear($tramiteDomicilio);
				 	TramiteRepo::modificarCampoNuevo($tramiteDomicilio->nroTramite,1);

					//Trámite cambioTitular
					$datosCambioTitular['id_estado_tramite']= 0;
				 	$datosCambioTitular['id_tipo_tramite']= 3;
				 	$datosCambioTitular['observaciones']='Titular Permiso Nuevo';
				 	$tramiteTitular=self::armaTramitePermiso($datosCambioTitular, $datosAgenteBolsa);
					$tramiteTitular = TramiteRepo::crear($tramiteTitular);
		 			TramiteRepo::modificarCampoNuevo($tramiteTitular->nroTramite,1);

		 			if(strcasecmp($tipoAdjudicacion,'agencia')==0){
		 				//Trámite cambio de categoría
		 				$datosCambioCategoria['id_estado_tramite']=0;
					 	$datosCambioCategoria['id_tipo_tramite']= 2;
					 	$datosCambioCategoria['observaciones']='Red Permiso Nuevo';					 	
					 	$tramiteCategoria=self::armaTramitePermiso($datosCambioCategoria, $datosAgenteBolsa);
						$tramiteCategoria = TramiteRepo::crear($tramiteCategoria);
						TramiteRepo::modificarCampoNuevo($tramiteCategoria->nroTramite,1);
		 			}else{
						//Trámite cambioDependencia
						$datosCambioDependencia['id_estado_tramite']= 0;
					 	$datosCambioDependencia['id_tipo_tramite']= 4;
					 	$datosCambioDependencia['observaciones']='Red Permiso Nuevo';
					 	$datosCambioDependencia['nro_red']='';
					 	$datosCambioDependencia['motivo_cr']='';
					 	$tramiteDependencia=self::armaTramitePermiso($datosCambioDependencia, $datosAgenteBolsa);
						$tramiteDependencia = TramiteRepo::crear($tramiteDependencia);
						TramiteRepo::modificarCampoNuevo($tramiteDependencia->nroTramite,1);
		 			}
					$ok=1;
					return $ok;
	}

	/**********************************
	* Lista de permisos sin adjudicar *
	***********************************/
	public function permisosSinAdjudicar($filtros){
		$filtro=$this->armarFiltroPermiso($filtros);
	    $count = 0;        
	      
	    $permisos = SuiteCRMRepo::listadoPermisos($filtro, $count);

        if(count($permisos)!=0){
	        $datos['permisos'] = $permisos;
     	}else{
     		$datos['permisos'] = array();
     	}

        $datos['pagina'] = $filtros['pagina'];

        if($datos['pagina']!="0"){
        	$datos['cantidadPaginas']=ceil($count/$filtros['porPagina']);	
        }else{
        	$datos['pagina']=1;
        	$datos['cantidadPaginas']=1;
        }
        

        return $datos;

	}

	/*************************************************
	* Lista de permisos sin adjudicar para armar CSV *
	**************************************************/
	public function permisosSinAdjudicarCSV($filtros){
		$filtro=$this->armarFiltroPermiso($filtros);
      
	    $permisos = SuiteCRMRepo::listadoPermisosCSV($filtro);

        if(count($permisos)!=0){
	        $datos['permisos'] = $permisos;
     	}else{
     		$datos['permisos'] = array();
     	}

        return $datos;
	}

	/********************************************************
	* Función que arma el modelo de trámite para un permiso *
	*********************************************************/
	public function armaTramitePermiso($datosTramite, $datosAgenteBolsa){
		$tramite = new Tramite();
		$usuarioTramite=\Session::get('usuarioLogueado');
		$tramite->usuario = $usuarioTramite['id'];
		$tramite->tipoTramite = $this->servicioTipoTramite->buscarPorId($datosTramite['id_tipo_tramite']);//$datos['id_tipo_tramite'];//se saca del id del combobox
		$tramite->estadoTramite = EstadoTramiteRepo::buscarPorId_arreglo($datosTramite['id_estado_tramite']);
		$tramite->nroPermiso = $datosAgenteBolsa['permiso'];
		$tramite->agente = $datosAgenteBolsa['agente'];
		$tramite->subAgente = $datosAgenteBolsa['subAgente'];
		$tramite->razonSocial = $datosAgenteBolsa['razonSocial'];
		$tramite->localidadAgencia = $datosAgenteBolsa['localidadAgencia'];
		$tramite->departamentoNombre =$datosAgenteBolsa['departamentoNombre'];
		$tramite->codigoPostalAgencia = $datosAgenteBolsa['codigoPostalAgencia'];
		$tramite->domicilioAgencia =  $datosAgenteBolsa['domicilioAgencia'];
		$tramite->rutaHistorialPDF = '';
		$tramite->fecha = \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		$tramite->observaciones = $datosTramite['observaciones'];
		$tramite->informado_crm=0;
		$tramite->pendienteInformar=0;
		$tramite->nuevo_permiso=1;
		$tramite->detalle_completo=0;
		$tramite->id_motivo_baja=-1;

		return $tramite;
	}

	/**************************************************
	* Función que arma el filtro para listar permisos *
	***************************************************/
	public function armarFiltroPermiso($filtros){

	    	// Paginación de la tabla
	        $filtro = new FiltroPermiso();
	        $filtro->setNumeroPagina($filtros['pagina']);
	        $filtro->setPorPagina($filtros['porPagina']);
	        //criterio de orden
	        $filtro->addCriterioOrden('permiso.fecha_inicio', $filtros['tipoOrden']);
	            
	        $filtro->fechaInicio = $filtros['fechaInicio'];
	     	
	        return $filtro;
	    }
	
	/*Retorna el nº del nuevo subagente*/
	public function obtenerNroNuevoSubAgente($nroTramite, $tipoTramite){
		if($tipoTramite==2){
			return CategoriaRepo::obtenerNroNuevoSubAgente($nroTramite);						
		}else if($tipoTramite==4){
			return DependenciaRepo::obtenerNroNuevoSubAgente($nroTramite);			
		}
	}
	
	/*Retorna "1" si el correo no es válido, caso contrario "0"*/
	public function verificarCorreo($correo, $permiso){
		return SuiteCRMRepo::verificarCorreo($correo, $permiso);
	}
	


	/**
	*
	*/
	public function modificarTramite($datos){
		$actualizado = false;
		switch ($datos['tipo_tramite']) {
			case '1':
				TramiteRepo::modificarObservaciones($datos['nro'], $datos['motivo_cd']);
				$datos_domicilio['direccion_nueva'] = $datos['domicilio_comercial'];
				$datos_domicilio['referente'] = $datos['referente'];
				$datos_domicilio['datos_contacto'] = $datos['datos_contacto'];
				$actualizado = DomicilioRepo::modificarCampos($datos['nro'], $datos_domicilio);

				break;
			case '2':
				TramiteRepo::modificarObservaciones($datos['nro'], $datos['motivo_cc']);
				$datos_categoria['motivo_cambio'] = $datos['motivo_cc'];
				if($datos['subagente_tramite']>0){
					$datos_categoria['cbu'] = $datos['cbu'];
} 
				$actualizado = CategoriaRepo::modificarCampos($datos['nro'], $datos_categoria);
				break;
			case '4':
				TramiteRepo::modificarObservaciones($datos['nro'], $datos['motivo_cr']);
				$datos_dependencia['motivo_cambio'] = $datos['motivo_cr'];
				$actualizado = DependenciaRepo::modificarCampos($datos['nro'], $datos_dependencia);
				break;
			default:
				TramiteRepo::modificarObservaciones($datos['nro'], $datos['motivo_ct']);
				$datos_titular['motivo_cambio']=$datos['motivo_ct'];
				TitularRepo::modificarCampos($datos['nro'], $datos_titular);

				$idPersona = TitularRepo::obtenerPersona($datos['nro']);
				if(!is_null($idPersona)){
					if($datos['tipo_persona']=='F'){
						if($datos['fecha_nac']!=''){
							$datos_persona['fecha_nacimiento'] =  $this->formatos->fecha($datos['fecha_nac']);
						}else{
							$datos_persona['fecha_nacimiento'] = $datos['fecha_nac'];
						}
						
						$datos_persona['ocupacion'] = $datos['tipo_ocup'];
						$datos_persona['apellido_nombre_razon'] = $datos['apellido_nombre'];
						$datos_persona['apellido_materno'] = $datos['apellido_mat'];

					}else{
						$datos_persona['tipo_sociedad'] = $datos['tipo_sociedad'];
						$datos_persona['apellido_nombre_razon'] = $datos['razon_social'];
						
					}

					if($datos['cuit']!="" ){
						$datos_persona['cuit'] = $this->formatos->desarmaCuit($datos['cuit']);
					}else{
						$datos_persona['cuit'] = $datos['cuit'];
					}

					$datos_persona['situacion_ganancia'] = $datos['tipo_situacion'];
					$datos_persona['nro_ingresos'] = $datos['ingresos'];
					$datos_persona['cbu'] = $datos['cbu'];
					$datos_persona['domicilio_particular'] = $datos['domicilio'];
					$datos_persona['email'] = $datos['email'];
					$datos_persona['referente'] = $datos['referente'];
					$datos_persona['datos_contacto'] = $datos['datos_contacto'];

					if($datos['nueva_localidad']!=""){
						$datos_persona['id_localidad'] = $datos['nueva_localidad'];
						$datos_persona['id_departamento'] = $datos['departamento_id'];

						//obtengo los datos de la localidad
						$servicioLocalidad = new Localidades();
						$localidad = $servicioLocalidad->buscarPorIdLocalidad($datos['nueva_localidad']);

						$datos_persona['codigo_postal'] = $localidad['codigo_postal'];
						$datos_persona['subcodigo_postal'] = $localidad['subcodigo_postal'];					
					}else{
						$datos_persona['id_localidad'] = "";
						$datos_persona['id_departamento'] =  "";
						$datos_persona['codigo_postal'] =  "";
						$datos_persona['subcodigo_postal'] =  "";
					}

					$actualizado = PersonaRepo::modificarCampos($idPersona, $datos_persona);					
				}

				break;
		}

		//Si se modificó correctamente => creo el historial para auditoría
		if($actualizado){
			$datos_hist['estadoIni'] = $datos['estado_tramite'];
			$datos_hist['id_estado_tramite']=$datos['estado_tramite'];
			$datos_hist['observaciones']="Se Modificaron los datos del trámite"; 
			$this->servicioHistorial->crear($datos['nro'], $datos_hist);
		}

		return true;
	}
	

	/**
	* Función que llama a la que verifica si existe
	* una solicitud de suspensión para el permiso.
	**/
	public function existeSolicitudSuspension($permiso){
		$ok=TramiteRepo::existeSolicitudSuspension($permiso);
		return $ok;
	}

	/**
	* Función para generar el trámite de suspensión de permiso
	**/
	public function solicitudSuspension($permiso,$fechahasta,$observaciones, $usuarioTramite){
		$datos['id_tipo_tramite']=10;
		$datos['id_estado_tramite']=10;
		$datos['fechahasta']=$fechahasta;
		$datos['observaciones']=$observaciones;
		$tramite = self::armaTramite($datos);
		$tramite = TramiteRepo::crear($tramite);

		//coloco en el historial el trámite
		$datos['estadoIni']=0;
		$this->servicioHistorial->crear($tramite->nroTramite, $datos);
		return $tramite->nroTramite;  
	}

	/**
	* Función que llama a la que verifica si existe
	* una solicitud de suspensión para el permiso.
	**/
	public function existeSolicitudHabilitacion($permiso){
		$ok=TramiteRepo::existeSolicitudHabilitacion($permiso);
		return $ok;
	}

	/**
	* Función para generar el trámite de habilitación de permiso
	**/
	public function solicitudHabilitacion($permiso,$fechadesde,$observaciones, $usuarioTramite){
		$datos['id_tipo_tramite']=9;
		$datos['id_estado_tramite']=10;
		$datos['fechadesde']=$fechadesde;
		$datos['observaciones']=$observaciones;
		$tramite = self::armaTramite($datos);
		$tramite = TramiteRepo::crear($tramite);

		//coloco en el historial el trámite
		$datos['estadoIni']=0;
		$this->servicioHistorial->crear($tramite->nroTramite, $datos);
		return $tramite->nroTramite; 
	}

	/**
	* Función que verifica si se creó el plan estratégico para el domicilio
	**/
	public function planCompleto($nroTramite){
		$pe= DomicilioRepo::obtenerPlan($nroTramite);
		if(is_null($pe)){
			return false;
		}
		return true;
	}

//Borra recursivamente un directorio y sus archivos
	private function delTree($dir)
    { 
        $files = array_diff(scandir($dir), array('.', '..')); 

        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file"); 
        }

        return rmdir($dir); 






    } 
	/**
	* Guarda la evaluación del domicilio
	**/
	public function guardarEvaluacionDomicilio($datos){
		$evaluacion = self::armaEvaluacion($datos);
		$mensaje = DomicilioRepo::guardarEvaluacionDomicilio($evaluacion);
		return $mensaje;
	}

	/**
	* Busca una lista de estados posibles para las diferentes
	* partes de la evaluación
	**/
	public function listaEstadosEvaluacion(){
		$listaEstados=SuiteCRMRepo::listaEstadosEvaluacion();
		return $listaEstados;
	}


	/**
	* Busca una lista de estados posibles para la evaluación
	**/
	public function listaEstadosEvaluacionGeneral(){
		$listaEstados=SuiteCRMRepo::listaEstadosEvaluacionGeneral();
		return $listaEstados;
	}

	/**
	* Busca la evaluación correspondiente para ser modificado
	**/
	public function obtenerEvaluacion($nro_tramite){
		$eval = DomicilioRepo::obtenerEvaluacionDomicilio($nro_tramite);
		return $eval;
	}

	/**
	* Función que arma la entidad plan con los datos
	* que le llegan por parámetro
	*/
	private function armaEvaluacion($datos){
		$evaluacion = new Evaluacion();
		
		$evaluacion->nro_tramite = $datos['nroTramite'];
		$evaluacion->superficie_codigo = $datos['superficie'];
		$evaluacion->superficie_valor = SuiteCRMRepo::obtenerSuperficiePorId($datos['superficie']);        
		$evaluacion->ubicacion_codigo = $datos['ubicacion'];   
		$evaluacion->ubicacion_valor = SuiteCRMRepo::obtenerUbicacionPorId($datos['ubicacion']);              
		$evaluacion->vidriera_codigo = $datos['vidriera'];
		$evaluacion->vidriera_valor = SuiteCRMRepo::obtenerVidrieraPorId($datos['vidriera']);
		
		$evaluacion->rubros_agencia = $datos['rubros_agencia'];
		$evaluacion->rubros_amigos = $datos['rubros_amigos'];
		$evaluacion->rubros_otros = $datos['rubros_otros'];
		
		$ds = DIRECTORY_SEPARATOR;
		$permiso = $datos['permiso'];

	
		if($datos['plano']!=""):
			
			//$directorioPlano =public_path().$ds.'upload'.$ds.'CD_'.$datos['nroTramite'].'_'.$permiso.$ds;
			$directorioPlano =\Config::get('habilitacion_config/config.urlDirectorioFotosPE').'upload'.$ds.'CD_'.$datos['nroTramite'].'_'.$permiso.$ds;
			if (!\File::isDirectory($directorioPlano)){//si no existe el directorio
					\File::makeDirectory($directorioPlano, 0777, true); //creamos el directorio				
			}
			//\Log::info($datos['plano']);
			$evaluacion->plano=$directorioPlano.$permiso."_P.".$datos['plano'];
			
		else:
			$evaluacion->plano="";
		endif;

		if(array_key_exists('localizacion', $datos)):
			$evaluacion->caracteristicas = implode(",",$datos['localizacion']);
		else:
			$evaluacion->caracteristicas = "";
		endif;
		$evaluacion->socioeconomico_codigo = $datos['soc_eco_codigo'];
		$evaluacion->socioeconomico_valor = SuiteCRMRepo::obtenerSocioEconomicoPorId($datos['soc_eco_codigo']);

		if(!empty($datos['listaCentrosAfinidad'])):
			$evaluacion->centroafinidad = $datos['listaCentrosAfinidad'];
			//$evaluacion->centroafinidad = $datos['listaCentrosAfinidad'];
		else:
			$evaluacion->centroafinidad = "";
		endif;

		$evaluacion->competidor_cuadra = $datos['cuadra']; 
		$evaluacion->competidor_antsig = $datos['antesig'];
		$evaluacion->competidor_transv = $datos['transver'];

		$evaluacion->estado = $datos['estado_eval'];
		$evaluacion->estado_local = $datos['estado_local'];
		$evaluacion->estado_cuantitativo = $datos['estado_cuantitativo'];
		$evaluacion->estado_entorno = $datos['estado_entorno'];
		$evaluacion->estado_competencia = $datos['estado_competencia'];

		$evaluacion->observacion_local = $datos['observaciones_local'];
		$evaluacion->observacion_cuantitativo = $datos['observaciones_cuantitativo'];
		$evaluacion->observacion_entorno = $datos['observaciones_entorno'];
		$evaluacion->observacion_competencia = $datos['observaciones_competencia'];


		return $evaluacion;
	}

	public function tablaAntecedentes($nro_tramite){
		$eval = TramiteRepo::tablaAntecedentes($nro_tramite);
		return $eval;
	}

	public function todosLosOtrosTramitesCompletos($nroTramite, &$mensaje){

			$tramite = TramiteRepo::buscarPorId($nroTramite);
			$nroPermiso = $tramite->nroPermiso;
			$nuevoTodoCompleto = false;
			$compDom = false;
			$compCat = false;
			$compDep = false;
			$compTit = false;
			$dom = TramiteRepo::nroTramiteDomNP($nroPermiso);
			if($tramite->subAgente == 0)://adjudica como subagencia
				$dep = TramiteRepo::nroTramiteDepNP($nroPermiso);
			else: //adjudica como agencia
				$cat = TramiteRepo::nroTramiteCatNP($nroPermiso);
			endif;

			$tit = TramiteRepo::nroTramiteTitNP($nroPermiso);
			if(!is_null($dom)):
					$completoDomicilio = DomicilioRepo::CompletoTodosLosCamposDom($dom);
					$completoPlan = DomicilioRepo::CompletoTodosLosCamposPlan($dom);
//\Log::info("completoPlan: ", array($completoPlan));
					if(!$completoPlan):
						$mensaje = "Debe completar el plan del domicilio";
					endif;

					$todoCompleto = ($completoDomicilio && $completoPlan) ? true:false;
//\Log::info("todoCompleto: ", array($todoCompleto));

					if($todoCompleto):
						$terminoEval = DomicilioRepo::TerminoEvaluacion($dom);	
//\Log::info("terminoEval: ", array($terminoEval));
						if(!$terminoEval):
							$mensaje = "Debe completar si es satisfactoria/no satisfactoria la evaluación.";
						endif;

					endif;
					
					$compDom = ($todoCompleto && $terminoEval) ? true : false ;						
			endif;

			if(isset($cat)):
//\Log::info("cat: ", array($cat));
					\Log::info("ANALIZE_EMAIL - todosLosOtrosTramitesCompletos: ", array($cat));
					$todoCompleto = CategoriaRepo::CompletoTodosLosCampos($cat);
					$compCat = $todoCompleto;
			
			endif;

			if(isset($dep)):
					$todoCompleto = DependenciaRepo::CompletoTodosLosCampos($dep);
					$compDep = $todoCompleto;
			
			endif;
				
			if(!is_null($tit)):
					$todoCompleto = TitularRepo::CompletoTodosLosCampos($tit);
					$compTit = $todoCompleto;
			
			endif;
			
//\Log::info("compDep: ", array($compDep));
//\Log::info("compTit: ", array($compTit));
			$nuevoTodoCompleto = ($compDom && ($compCat || $compDep) && $compTit) ? true : false;
//\Log::info("nuevoTodoCompleto: ", array($nuevoTodoCompleto));

			return $nuevoTodoCompleto;

} 

} 
?>