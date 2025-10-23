<?php
	
	/**
	* Clase de servicio que se comunica con el repositorio
	* para obtener los datos del modelo.
	*/
	
	namespace Dominio\Servicios\Habilitacion;
	use Datos\Repositorio\Habilitacion\ProvinciaRepo;
	use Datos\Repositorio\Habilitacion\DepartamentoRepo;
	
	class Provincias{
		
		/**
		* Devuelve una lista de todas las provincias
		* según los criterios especificados por los parámetros
		*/
		public static function buscar($id,$on=false){
			$provincias=ProvinciaRepo::buscar($id,$on);
			foreach ($provincias as $provincia) {
				$result[]=ProvinciaRepo::arregloProvincia($provincia);
			}
			return $result;
		}

		/**
		* Devuelve una provincia cuyo id coincide
		* con el pasado por parámetro
		*/
		public function buscarPorId($id){
			$provincia = ProvinciaRepo::buscarPorId($id);
			$provincia = ProvinciaRepo::arregloProvincia($provincia);
			return $provincia;
		}

		public function departamentos(){
			$departamentos = DepartamentoRepo::buscarTodos();
			foreach($departamentos as $departamento){
				$result[]=DepartamentoRepo::arregloDepartamento($departamento);
			}
			
			return $result;
		}
	
	}
?>