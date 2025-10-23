<?php
	
	/**
	* Clase de servicio que se comunica con el repositorio
	* para obtener los datos del modelo.
	*/
	
	namespace Dominio\Servicios\Habilitacion;
	use \Datos\Repositorio\Habilitacion\HistoricoTramiteEstadosRepo;
	use \Datos\Repositorio\Habilitacion\EstadoTramiteRepo;
	use \Dominio\Entidades\Habilitacion\HistoricoTramiteEstados;
	use Datos\Filtros\FiltroHistoricoTramiteEstados;

	class HistoricoTramitesEstados{
		
		
		/**
		* Devuelve una lista de todos los HistoricoTramiteEstados
		* según los criterios especificados por los parámetros
		*/
		public function buscarPorId($id){
			$historial = HistoricoTramiteEstadosRepo::buscarPorId($id);
			if(is_null($historial)){
				return $historial;
			}else{
				\Log::info("historicoTramiteEstados - buscarporid - ", $historial);
				foreach ($historial as $historia) {
					$estadoI = EstadoTramiteRepo::buscarPorId($historia->idEstadoIni)->descripcionTramite;
					$estadoF = EstadoTramiteRepo::buscarPorId($historia->idEstadoFin)->descripcionTramite;
					$arreglo=HistoricoTramiteEstadosRepo::arregloHistoricoTramiteEstados($historia);
					$arreglo['estadoI']=$estadoI;
					$arreglo['estadoF']=$estadoF;
					$resultado[]=$arreglo;
				}
				
				return $resultado;
			}
		}

		/***********************************************/
		/* Verifica si existe una fila en el historial */
		/* con exactamente el mismo nº de trámite,     */
		/* estado inicial y final.					   */
		/***********************************************/
		public function existeUnoIgual($idTramite, $idEstadoI, $idEstadoF){
			return HistoricoTramiteEstadosRepo::existeUnoIgual($idTramite, $idEstadoI, $idEstadoF);
		}



		/************************************************/
		/* Función que devuelve la última fecha en que  */
		/* se modificó el estado de un trámite.         */
		/************************************************/
		public function fechaUltimaModificacion($id){
			return HistoricoTramiteEstadosRepo::fechaUltimaModificacion($id);
		}

		public function fechaUltimoTramite($permiso, $tipoTramite){
			return HistoricoTramiteEstadosRepo::fechaUltimoTramite($permiso,$tipoTramite);
		}
		

		/*******************************************************************/
		/* Función que modifica la observación de algún estado del trámite */
		/*******************************************************************/
		public function modificaObservacionEstado($nroTramite, $fecha, $estaI, $estaF, $observaciones){
			HistoricoTramiteEstadosRepo::modificaObservacionEstado($nroTramite, $fecha, $estaI, $estaF, $observaciones);
		}

		/**
		 * Guarda un registro en el historial con los datos pasados
		 * por parámetro.
		 * @param Entidad $historicoTramiteEstados
		 */
		public function crear($nroTramite, $datos) {
			$datos['nroTramite']=$nroTramite;
			$historial=self::armaHistorialEstado($datos);
			$historial = HistoricoTramiteEstadosRepo::crear($historial);
			return $historial;
		}

		/**
	     * Realiza una búsqueda, los items del resultado son stdClass
	     * 
	     * @return ResultadoPaginacion Resultado de la búsqueda
	     */
	    public function listar($filtros) {
	    	$filtro=$this->armarFiltroHistorial($filtros);
	        $count = 0;        
	       
	        $historial = HistoricoTramiteEstadosRepo::listadoHistoricos($filtro, $count);

	        $datos['historial'] = $historial;
	        $datos['pagina'] = $filtros['pagina'];
	        $datos['cantidadPaginas']=ceil($count/$filtros['porPagina']);

	        return $datos;
	    }	

	    public function armarFiltroHistorial($filtros){
 
	    	// Paginación de la tabla
	        $filtro = new FiltroHistoricoTramiteEstados();
	        $filtro->setNumeroPagina($filtros['pagina']);
	        $filtro->setPorPagina($filtros['porPagina']);
	        //ver por qué los va a ordenar
	        if($filtros['campoOrden']==1){//fecha
	        	$filtro->addCriterioOrden('fecha', 0);//0=desc 1=asc $filtros['tipoOrden']
	        }elseif($filtros['campoOrden']==2){//estado inicial
	        	$filtro->addCriterioOrden('idestado_ini', $filtros['tipoOrden']);
	        }else{//estado final
	        	$filtro->addCriterioOrden('idestado_fin', $filtros['tipoOrden']);
	        }
	        
	        //array con ids de los tipos de tramite seleccionados

	        $filtro->nroTramite = $filtros['nroTramite'];
	        $filtro->estadoTramite = $filtros['estados'] ;
	        $filtro->fechaDesde = $filtros['fechaDesde'];
	        $filtro->fechaHasta = $filtros['fechaHasta'];
	        $filtro->permiso = $filtros['permiso'];     
	        $filtro->agente = $filtros['agente'];
	     	$filtro->subAgente = $filtros['subAgente'];

	        return $filtro;
	    }


		/**
		* Función que arma la entidad historial con los datos
		* que le llegan por parámetro
		*/
		private function armaHistorialEstado($datos){
			$usr = \Session::get('usuarioLogueado');

			$historial = new HistoricoTramiteEstados();

			$historial->nroTramite = $datos['nroTramite'];
			$historial->fecha = \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
			if(array_key_exists("cambio_dom", $datos)){
				$historialAnterior=HistoricoTramiteEstadosRepo::ultimoHistorial($datos['nroTramite']);
				$historial->idEstadoIni=$historialAnterior->idEstadoIni;
				$historial->idEstadoFin=$historialAnterior->idEstadoFin;
			}else{
				if (array_key_exists("estadoIni", $datos)) {
					$historial->idEstadoIni = $datos['estadoIni'];
				}else{
					$historial->idEstadoIni = 0;//pendiente
				}
				$historial->idEstadoFin = $datos['id_estado_tramite'];
			}
			$historial->tipo_usuario = $usr['nombreTipoUsuario'];
			$historial->usuario= $usr['nombreUsuario'];
			$historial->observaciones = $datos['observaciones']; 
			
			return $historial;
		}



	
	
	}
?>
