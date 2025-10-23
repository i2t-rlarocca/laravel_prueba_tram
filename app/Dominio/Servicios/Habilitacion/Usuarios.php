<?php

	/**
	* Clase de servicio que se comunica con el repositorio
	* para obtener los datos del modelo.
	*/
	namespace Dominio\Servicios\Habilitacion;
	use Datos\Repositorio\Habilitacion\UsuarioRepo;
	use Datos\Repositorio\Habilitacion\ProvinciaRepo;
	use Dominio\Servicios\Habilitacion\Provincias;

	class Usuarios{
				
		/**
		* Devuelve todos los usuarios
		*/
		public static function buscarTodos(){
			$usuarios=UsuarioRepo::buscarTodos();
			return $usuarios;
		}
		
		/**
		* Busca el usuario por el id
		*/
		public static function buscarPorId($id){
			$usuario=UsuarioRepo::buscarPorId($id);
			$usuario=UsuarioRepo::arregloUsuario($usuario);
			return $usuario;
		}


		public static function controlSesion($nombreUsuario, $tipoUsuario){
			return UsuarioRepo::controlSesion($nombreUsuario, $tipoUsuario);
		}

		
		/**
		* Busca el usuario por el nombre de usuario
		*/
		public static function buscar($nombreUsuario){
			$usuario=UsuarioRepo::buscar($nombreUsuario);
			if(!is_null($usuario)){
				$idLocalidad = $usuario->idLocalidad;
				$nombreDepartamento = $usuario->departamentoNombre;
				$usuario=UsuarioRepo::arregloUsuario($usuario);
				$servicioProvincias = new Provincias();
				$usuario['provincia'] = $servicioProvincias->buscarPorId($usuario['provincia']->id);
				$usuario['idLocalidad'] = $idLocalidad;
				$usuario['departamentoNombre'] = $nombreDepartamento;
			}
			return $usuario;
		}
		
		/**
		* Devuelve todos los usuarios
		*/
		public static function buscarPorPermiso($permiso){
			$usuario=UsuarioRepo::buscarPorPermiso($permiso);
			if(!is_null($usuario)){
				$idLocalidad = $usuario->idLocalidad;
				$nombreDepartamento = $usuario->departamentoNombre;
				$usuario=UsuarioRepo::arregloUsuario($usuario);
				$servicioProvincias = new Provincias();
				$usuario['provincia'] = $servicioProvincias->buscarPorId($usuario['provincia']->id);
				$usuario['idLocalidad'] = $idLocalidad;
				$usuario['departamentoNombre'] = $nombreDepartamento;
			}
			return $usuario;
		}


		
		
	}
?>