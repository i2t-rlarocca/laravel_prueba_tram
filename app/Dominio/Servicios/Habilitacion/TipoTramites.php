<?php
	
	/**
	* Clase de servicio que se comunica con el repositorio
	* para obtener los datos del modelo.
	*/
	
	namespace Dominio\Servicios\Habilitacion;
	use Datos\Repositorio\Habilitacion\TipoTramiteRepo;
	
	class TipoTramites{
		
		/**
		* Devuelve una lista de todos los TipoTramites habilitados
		* $funcion: 1-interna 0-agente/subagente(CAS)
		*/
		public static function buscarTodos($funcion){
			$tiposTramites=TipoTramiteRepo::buscarTodos($funcion);
			foreach ($tiposTramites as $tipoTramite) {
				$result[]=TipoTramiteRepo::arregloTipoTramite($tipoTramite);
			}
			
			return $result;
		}

		/**
		* Devuelve una lista de todos los ids TipoTramites habilitados
		* $funcion:1-interna 0-agente/subagente(CAS)
		*/
		public static function buscarTodosLosIds($funcion){
			$tiposTramites=TipoTramiteRepo::buscarTodosLosIds($funcion);
			return $tiposTramites;
		}

		/**
		* Devuelve un tipo de tramite cuyo id coincide
		* con el pasado por parámetro
		*/
		public function buscarPorId($id){
			$tipoTramite = TipoTramiteRepo::buscarPorId($id);
			$tipoTramite = TipoTramiteRepo::arregloTipoTramite($tipoTramite);
			return $tipoTramite;
		}

		/**
		* Devuelve una lista con las funciones para máquinas
		**/
		public function buscarTiposTramitesMaquinas(){
			$tiposTramites = TipoTramiteRepo::buscarTiposTramitesMaquinas();
			foreach ($tiposTramites as $tipoTramite) {
				$result[]=TipoTramiteRepo::arregloTipoTramite($tipoTramite);
			}
			
			return $result;
		}
		/**
		* Devuelve un tipo de tramite cuyo nombre coincide
		* con el pasado por parámetro
		*/
		public function buscarSimilaresPorNombre($nombre, $listaFunciones){
			$tipoTramite = TipoTramiteRepo::buscarSimilaresPorNombre($nombre, $listaFunciones);
			$tipoTramite = TipoTramiteRepo::arregloTipoTramite($tipoTramite);
			return $tipoTramite;
		}

		/**
		* Devuelve el nombre del tipo de tramite según sea para nuevo permiso o no
		*/
		public function tituloTramite($id,$alta){
			$tituloTramite = TipoTramiteRepo::tituloTramite($id,$alta);
			return $tituloTramite;
		}
		
		/**
		* Devuelve el cuit del permiso de usuario seteado para el tramite.
		*/
		public function cuitPermiso($permiso){
			$cuit = TipoTramiteRepo::cuitPermiso($permiso);
			return $cuit;
		}

	
	}
?>