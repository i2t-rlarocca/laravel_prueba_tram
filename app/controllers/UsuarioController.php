<?php
namespace controllers;	
	use Dominio\Servicios\Habilitacion\Usuarios;
	

	class UsuarioController extends \BaseController{
		
		function __construct() {
			$this->servicio = new Usuarios();
		}
		
		/**
		* Función que busca un usuario según el nombre de usuario
		* que llega por parámetro.
		*/
		public function buscar($nombreUsuario){
			return $this->servicio->buscar($nombreUsuario);
		}
		
		public function buscarPorPermiso($permiso){
			return $this->servicio->buscarPorPermiso($permiso);
		
		}

		public function buscarPorId($id){
			return $this->servicio->buscarPorId($id);
		
		}

		

		/**
		* Función que verifica si el usuario tiene permiso
		* para ejecutar la función. Se busca en la lista de funciones
		* del usuario
		**/
		public function tienePermisoFuncion($funcion_pedida, $listaFunciones){
			$tienePermiso = false;
			foreach ($listaFunciones as $funcion) {
					if(in_array($funcion_pedida, $funcion)){
						$tienePermiso=true;
						break;
					}
				}
			return $tienePermiso;
		}
		

		/**
		* Función para ver si el usuario tiene sesión activa en el portal
		**/
                public function controlSesion(){
					
			return \Response::json(array('mensaje'=>'in'));
	/*			
			$id_portal = \Session::get('usuarioLogueado')['id_portal'];
			$letra_portal = str_replace("\'","",\Session::get('usuarioLogueado')['letra_portal']);

			if(is_null($id_portal)||is_null($letra_portal)){
				$url=\Config::get('habilitacion_config/config.url_i');		
				return \Response::json(array('mensaje'=>'out', 'url'=>$url));
			}
			$sesionPortal = $this->servicio->controlSesion($id_portal, $letra_portal);
			if($sesionPortal['sesionActiva']){
				return \Response::json(array('mensaje'=>'in'));
			}else{
				\Session::flush();
				\Log::info("letra: ",array($letra_portal));
				$letra_portal=substr($letra_portal, 1, -1);
				$url=\Config::get('habilitacion_config/config.url_'.$letra_portal);		
				return \Response::json(array('mensaje'=>'out', 'url'=>$url));
			}
	*/		
		}

		
		public function controlSesionPrimeraVez(){
			$id_portal = \Session::get('usuarioLogueado')['id_portal'];
			$letra_portal = \Session::get('usuarioLogueado')['letra_portal'];
			$sesionPortal = $this->servicio->controlSesion($id_portal, $letra_portal);
			
			if($sesionPortal['sesionActiva']){
				return ;
			}else{
				\Session::flush();
				$url=\Config::get('habilitacion_config/config.url_'.$letra_portal);	
			
				return \Redirect::to('/portal/'.$url);
			}
			
		}

	}

?>