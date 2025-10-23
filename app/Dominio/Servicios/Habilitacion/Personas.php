<?php

	/**
	* Clase de servicio que se comunica con el repositorio
	* para obtener los datos del modelo.
	*/
	namespace Dominio\Servicios\Habilitacion;
	use \Datos\Repositorio\Habilitacion\PersonaRepo;
	use \Dominio\Servicios\Habilitacion\Localidades;
	use \Dominio\Entidades\Habilitacion\Persona;
	use Presentacion\Premios\Formatos;

	class Personas{

		function __construct(){
			$this->formatos=new Formatos();
		}
				
		/**
		* Devuelve todas las personas
		*/
		public static function buscarTodas(){
			$persona=PersonaRepo::buscarTodos();
			return $persona;
		}
		
		/**
		* Busca la persona que tiene el id pasado por parámetro
		*/
		public static function buscarPorId($id){
			$persona=PersonaRepo::buscarPorId($id);
			$persona = PersonaRepo::arregloPersona($persona);
			return $persona;
		}

		/**
		* Busca la persona que tiene el cuit pasado por parámetro
		*/
		public static function buscarPorCuit($cuit){
			$persona=PersonaRepo::buscarPorCuit($cuit);
			return $persona;
		}

		/**
		* Busca la persona según el cuit y el documento
		**/
		public static function buscarPersona($dni,$cuit,$tipo_doc){
			$persona=PersonaRepo::buscarPersona($dni,$cuit,$tipo_doc);

			if(is_null($persona)){
				return $persona;
			}
			$servicioLocalidad = new Localidades();
			$localidad = $servicioLocalidad->buscarPorCPSCPLocalidad($persona['codigo_postal'], $persona['subcodigo_postal']);

			$persona['nombre_localidad']=$localidad['nombre'];
			$persona['id_localidad'] = $localidad['id'];
			return $persona;
		}

		/**
		 * Guarda una persona con los datos pasados
		 * por parámetro.
		 * @param Entidad $persona
		 */
		public function crear($datos) {
			$persona = self::armaPersona($datos);
			$persona = PersonaRepo::crear($persona);
			$persona = PersonaRepo::arregloPersona($persona);
			return $persona;
		}


		/**
		* Función que arma la entidad persona con los datos
		* que le llegan por parámetro
		*/
		private function armaPersona($datos){
			$persona = new Persona();
			
			$persona->id_persona =PersonaRepo::ultimoId();//esto vendría del crm 
			if($datos['tipo_persona']=='F'){//persona física
				$persona->sexo = $datos['sexo_persona'];	
				$persona->tipo_persona = $datos['tipo_persona'];
				$persona->tipo_sociedad = '';
				$persona->apellido_nombre_razon = $datos['apellido_nombre'];
				if($datos['apellido_mat']!='')
					$persona->apellido_materno = $datos['apellido_mat'];
				else
					$persona->apellido_materno = '';
				$persona->tipo_documento = $datos['tipo_doc'];
				$persona->nro_documento = str_replace(".", "", $datos['nro_doc']);
				if($datos['cuit']!=''){
					$persona->cuit = str_replace("-", "", $datos['cuit']);	
				}else{
					$persona->cuit = "";
				}
				$persona->ocupacion = $datos['tipo_ocup'];
				if($datos['fecha_nac']!='')
					$persona->fecha_nacimiento = $this->formatos->fecha($datos['fecha_nac']);
				else
					$persona->fecha_nacimiento = '0000-00-00';
			}else{//persona jurídica
				$persona->sexo ="S";
				$persona->tipo_persona = $datos['tipo_persona'];
				$persona->tipo_sociedad = $datos['tipo_sociedad'];
				$persona->apellido_nombre_razon = $datos['razon_social'];
				$persona->apellido_materno = '';
				$persona->cuit = str_replace("-", "", $datos['cuit']);
				$persona->tipo_documento = 4;//otro
				$persona->nro_documento = "";
				$persona->ocupacion ='';
				$persona->fecha_nacimiento = '0000-00-00';
			}
			$persona->situacion_ganancia = $datos['tipo_situacion'];		
			// aden 2024-04-22
			// $persona->nro_ingresos = $datos['ingresos'];
			$persona->nro_ingresos = '';
			
			$persona->domicilio_particular = $datos['domicilio'];
			if($datos['email']!='')
				$persona->email = $datos['email'];
			else
				$persona->email = '';
			$persona->id_localidad =  $datos['nueva_localidad'];
			$persona->id_departamento =  $datos['departamento_id'];
			$servicioLocalidad = new Localidades();
			$localidad = $servicioLocalidad->buscarPorIdLocalidad($datos['nueva_localidad']);
			$persona->codigo_postal = $localidad['codigo_postal'];
			$persona->subcodigo_postal = $localidad['subcodigo_postal'];
			$persona->referente = $datos['referente'];
			$persona->datos_contacto = $datos['datos_contacto'];
			// aden 2024-04-22
			// if($datos['cbu']==''){
				$persona->cbu='';
			// }else{
			//	$persona->cbu = $datos['cbu'];
			//}
			return $persona;
			
		}
		
	}
?>
