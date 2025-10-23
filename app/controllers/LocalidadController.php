<?php
namespace controllers;	
	use \Dominio\Servicios\Habilitacion\Localidades;

	class LocalidadController extends \BaseController{
	
		function __construct() {
			$this->servicio = new Localidades();
		}
		
				
		/**
		* Busca la localidad cuyo id coincide con el pasado por parámetro
		* @param type $id
		* @return mensaje
		*/
		public function buscarLocalidad(){

			$id=trim(\Input::get('id'));

			$localidad = $this->servicio->buscar($id);

			if($localidad!=false){//existe la localidad
				return \Response::json(array('mensaje'=>"true", 'nombre_localidad'=>$localidad['nombre'], 'cpscp'=>$localidad['cpscp']));
			}else{
				return \Response::json(array('mensaje'=>"La localidad ingresada no existe."));
			}
		}
		
		public function buscarPorCodigoPostal($cp){
			$localidad = $this->servicio->buscarPorCodigoPostal($cp);
			return $localidad;
		}

		/**
		* Busca las localidades de la provincia cuyo
		* id es pasado por parámetro
		* @param type $id
		* @return $localidades
		*/
		public function buscarPorIdLocalidad($id){
			$localidad = $this->servicio->buscarPorIdLocalidad($id);
			return $localidad;
		}
		
		/**
		 * Devuelve un conjunto de localidades que contengan
		 * en su nombre lo que llega como parámetro. 
		 * @param type $nombre
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public function buscarSimilaresPorNombre() {

			$nombre=trim(\Input::get('letras'));

			$localidades = $this->servicio->buscarSimilaresPorNombre_santaFe($nombre);
		
			$lista = array();		
			foreach($localidades as $localidad){
			  $combobox[0]= $localidad['id'];
			  $combobox[1]=rtrim($localidad['nombre']).'('.$localidad['codigo_postal'].'-'.$localidad['subcodigo_postal'].')';
			  $lista[]=$combobox;
			}
						
			$respuesta = \Response::json(array('lista_localidades'=>$lista));

	        return $respuesta;
			
			
		}


		/**
		 * Devuelve un conjunto de localidades que contengan
		 * en su nombre lo que llega como parámetro. 
		 * @param type $nombre
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public function buscarSimilaresPorNombre_stafe_dpto() {

			$nombre=trim(\Input::get('letras'));
			$dpto=\Input::get('dpto');

			$localidades = $this->servicio->buscarSimilaresPorNombre_santaFe_dpto($nombre,$dpto);
		
			$lista = array();		
			foreach($localidades as $localidad){
			  $combobox[0]= $localidad['id'];
			  $combobox[1]=rtrim($localidad['nombre']).'('.$localidad['codigo_postal'].'-'.$localidad['subcodigo_postal'].')';
			  $lista[]=$combobox;
			}
						
			$respuesta = \Response::json(array('lista_localidades'=>$lista));

	        return $respuesta;
			
		}


		public function buscarDepartamentoLocalidad($idLocalidad){
			$departamento = $this->servicio->buscarDepartamentoLocalidad($idLocalidad);
			return $departamento;
		}

		public function buscarDepartamentoLocalidadPorCP_SCP($cp, $scp){
			$departamento = $this->servicio->buscarDepartamentoLocalidadPorCP_SCP($cp,$scp);			
			return $departamento;
		}

		public function arregloLocalidad($localidad){
			$localidadArray = $this->servicio->arregloLocalidad($localidad);
			return $localidadArray;
		}
				
	}
?>

