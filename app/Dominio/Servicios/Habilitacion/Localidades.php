<?php
	/**
	* Clase de servicio que se comunica con el repositorio
	* para obtener los datos del modelo.
	*/
	
	namespace Dominio\Servicios\Habilitacion;
	use Datos\Repositorio\Habilitacion\LocalidadRepo;
	
	class Localidades{
	

		/**
		 * Devuelve la localidad cuyo id es pasado por parámetro
		 * @param integer $id
		 */
		public function buscar($id){
			$localidad = LocalidadRepo::buscar($id);
			return $localidad;
		 }

		/**
		* Devuelve una lista de todos las localidades
		*/
		public static function buscarTodas(){
			$localidades=LocalidadRepo::buscarTodas();
			foreach ($localidades as $localidad) {
				$result[]=LocalidadRepo::arregloLocalidad($localidad);
			}
			return $result;
		}
		
		
		/**
		 * Devuelve las localidades que pertencen
		 * a la provincia cuyo id es pasado como parámetro
		 * @param integer $id
		 */
		public function buscarPorIdProvincia($id){
			$localidad = LocalidadRepo::buscarPorIdProvincia($id);
			$localidad = LocalidadRepo::arregloLocalidad($localidad);
			return $localidad;
		 }
		 

		 /**
		 * Devuelve la localidades cuyo id es pasado por parámetro
		 * @param integer $id
		 */
		public function buscarPorIdLocalidad($id){
			$localidad = LocalidadRepo::buscarPorIdLocalidad($id);
			if(is_null($localidad)){
				return null;
			}
			$localidad = LocalidadRepo::arregloLocalidad($localidad);
			return $localidad;
		 }

		 /**
		 * Devuelve la localidades cuyo cp y scp es pasado por parámetro
		 * @param integer $cp, $scp
		 */
		public function buscarPorCPSCPLocalidad($cp, $scp){
			$localidad = LocalidadRepo::buscarPorCPSCPLocalidad($cp, $scp);
			if(is_null($localidad)){
				return null;
			}
			$localidad = LocalidadRepo::arregloLocalidad($localidad);
			$localidad['nombre']=rtrim($localidad['nombre']).'('.$localidad['codigo_postal'].'-'.$localidad['subcodigo_postal'].')';
			return $localidad;
		 }

		/**
		 * Devuelve un conjunto de localidades que contengan
		 * en su nombre lo que llega como parámetro. 
		 * @param type $nombre
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public function buscarSimilaresPorNombre($nombre) {        
			$localidades = LocalidadRepo::buscarSimilaresPorNombre($nombre);
			$result=array();
			foreach ($localidades as $localidad) {
				$result[]=LocalidadRepo::arregloLocalidad($localidad);
			}
			return $result;
		} 

		/**
		 * Devuelve un conjunto de localidades que contengan
		 * en su nombre lo que llega como parámetro. 
		 * @param type $nombre
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public function buscarSimilaresPorNombre_santaFe($nombre) {        
			$localidades = LocalidadRepo::buscarSimilaresPorNombre_santaFe($nombre);
			$result=array();
			foreach ($localidades as $localidad) {
				$result[]=LocalidadRepo::arregloLocalidad($localidad);
			}
			return $result;
		} 

		/**
		 * Devuelve un conjunto de localidades que contengan
		 * en su nombre lo que llega como parámetro. 
		 * @param type $nombre
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public function buscarSimilaresPorNombre_santaFe_dpto($nombre, $dpto) {        
			$localidades = LocalidadRepo::buscarSimilaresPorNombre_santaFe_dpto($nombre, $dpto);
			$result=array();
			foreach ($localidades as $localidad) {
				$result[]=LocalidadRepo::arregloLocalidad($localidad);
			}
			return $result;
		} 


		public function buscarPorCodigoPostal($cp){
			$localidad = LocalidadRepo::buscarPorCodigoPostal($cp);
			$localidad = LocalidadRepo::arregloLocalidad($localidad);
			return $localidad;
		}


		public function buscarDepartamentoLocalidad($idLocalidad){
			$departamento = LocalidadRepo::buscarDepartamentoLocalidad($idLocalidad);
			return $departamento;
		}

		public function buscarDepartamentoLocalidadPorCP_SCP($cp, $scp){
			$departamento = LocalidadRepo::buscarDepartamentoPorCP_SCP($cp, $scp);
			return $departamento;
		}

		public function arregloLocalidad($localidad){
			$localidadArray = LocalidadRepo::arregloLocalidad($localidad);
			return $localidadArray;
		}

	}

?>