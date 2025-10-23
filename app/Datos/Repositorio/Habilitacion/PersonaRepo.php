<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Persona;
	use models\Habilitacion\PersonaModelo;

	class PersonaRepo{


		/**
		 * Devuelve una persona con el id pasado como parámetro
		 * @param integer $id
		 */
		public static function buscarPorId($id){
			$persona = PersonaModelo::find($id);

			if (is_null($persona)) {
				return null;
			}else{
				return self::map($persona);
			}
		 }
		 public static function buscarPorIdModelo($id){
			\Log::info('AE - PersonaRepo - buscarPorIdModelo - id: ', array($id));
			 
			$persona = PersonaModelo::find($id);

			\Log::info('AE - PersonaRepo - buscarPorIdModelo - persona: ', array($persona));

			if (is_null($persona)) {
				return null;
			}else{
				return $persona;
			}
		 }


		/**
		 * Devuelve una persona con el cuit pasado como parámetro
		 * @param integer $cuit
		 */
		public static function buscarPorCuit($cuit){
			$persona = PersonaModelo::where('nro_documento','=',$cuit)->first();

			if (is_null($persona)) {
				return null;
			}else{
				return $persona;//self::map($persona);
			}
		 }


		/**
		 * Devuelve una persona con el cuit y el tipo pasado como parámetro
		 * @param integer $cuit
		 */
		public static function buscarPorTipoCuit($tipo, $cuit){
			if(strcmp(strtoupper($tipo), "J")==0){
				$persona = PersonaModelo::where('cuit','=',$cuit)
						  ->where('tipo_persona', $tipo)->first();
			}else{
				$persona = PersonaModelo::where('nro_documento','=',$cuit)
				          ->where('tipo_persona', $tipo)->first();
			}

			if (is_null($persona)) {
				return null;
			}else{
				return $persona;//self::map($persona);
			}
		 }


		 /**
		 * Devuelve un arreglo con los datos de la persona en el suite
		 **/
		 public static function buscarPersona($dni, $cuit, $tipo_doc){
		 	try{

		 		if($dni==0){
		 			$campo='p.dni_cuit_titular_c="'.$cuit.'"';
		 		}else{
		 			$campo='p.titular_doc_nro_c='.$dni; //.'" AND p.titular_doc_tipo_c="'.$tipo_doc.'"';
		 		}
		 		$query = 'select 	upper(trim(p.name)) AS nombre_titular,
									upper(trim(p.apellido_materno)) as apellido_materno,
									upper(trim(p.domicilio_titular_c)) as domicilio_titular,
									DATE_FORMAT(p.fecha_nacimiento_c, "%d/%m%/%Y") as fecha_nacimiento,
									p.sexo_c, 
									upper(trim(p.titular_doc_nro_c)) as nro_doc, 
									upper(trim(p.titular_doc_tipo_c)) as tipo_doc, 
									o.id AS tipo_ocupacion, 
									o.name AS ocupacion,
									l.name AS nombre_localidad, 
									l.loc_cpos as codigo_postal, 
									upper(trim(l.loc_scpo)) as subcodigo_postal, 
									upper(trim(lo.persona_contacto_c)) as referente, 
									UPPER(trim(lo.persona_contacto_datos)) as datos_contacto, 
									upper(trim(p.dni_cuit_titular_c)) as cuit,
									p.sit_ganancias AS tipo_situacion, 
									upper(trim(p.nro_ing_brutos_c)) AS ingresos, 
									ld.codigo AS tipo_sociedad, 
									p.`tipo_persona`
						FROM age_personas p
						INNER JOIN tbl_ocupaciones o ON o.id=p.tbl_ocupaciones_id_c AND o.deleted=0
						INNER JOIN tbl_localidades l ON l.id=p.tbl_localidades_id_c AND o.deleted=0
						INNER JOIN tbl_provincias pro ON pro.id=l.tbl_provincias_id_c AND pro.prv_id_boldt=22
						INNER JOIN age_permiso ap ON ap.age_personas_id_c = p.id AND o.deleted=0 AND ap.estado="activo"
						INNER JOIN age_local lo ON lo.id=ap.age_local_id_c AND o.deleted=0
						LEFT JOIN tbl_listas_desplegables ld ON ld.name="tipo_persona_juridica" AND ld.codigo=UPPER(p.tipo_persona_juridica)
						WHERE p.deleted=0 AND p.estado_c="activo" AND '.$campo;

				$persona = \DB::connection('suitecrm_cas')->select(\DB::raw($query));

				if(count($persona)==0){
					return null;
				}else{
					return (array)$persona[0];
				}
			}catch(\Exception $e){
				\Log::info('Excepción buscarPersona PersonaRepo: '+$e->getMessage());
				return null;
			}
		 }

// modificado ADEN - 2024-09-30 - se agrega id y cuit
		 public static function buscarPersonaSuite($id){
			try{
				$query = 'SELECT
					p.id, 
					upper(trim(p.name)) AS nombre_titular,
					upper(trim(p.apellido_materno)) as apellido_materno,
					upper(trim(p.domicilio_titular_c)) as domicilio_titular,
					DATE_FORMAT(p.fecha_nacimiento_c, "%d/%m%/%Y") as fecha_nacimiento,
					p.sexo_c,
					upper(trim(p.titular_doc_nro_c)) as nro_doc,
					upper(trim(p.titular_doc_tipo_c)) as tipo_doc,
					p.dni_cuit_titular_c,
					p.sit_ganancias AS tipo_situacion,
					upper(trim(p.nro_ing_brutos_c)) AS ingresos,
					p.`tipo_persona`
				FROM age_personas p
				WHERE p.deleted=0
					AND p.id="'.$id.'"';

				$persona = \DB::connection('suitecrm_cas')->select(\DB::raw($query));

				if(count($persona)==0){
					return null;
				}else{
					return (array)$persona[0];
				}
			}catch(\Exception $e){
				\Log::info('Excepción buscarPersona PersonaRepo: '+$e->getMessage());
				return null;
			}
		}

		/**
		 * Búsca el máximo id de persona y le suma 1
		 */
		public static function ultimoId(){
			$id = PersonaModelo::max('id_persona');
			$id += 1;
			return $id;
		}
		/**************************************************/
		/* crea una persona con los datos que le llegaron */
		/* por parámetros. Primero se crea en el crm y    */
		/* luego se crea en la base de trámites           */
		/**************************************************/
		public static function crear(Persona $persona){
			try{

					//$modeloExistente=self::buscarPorCuit($persona->nro_documento);
					if(empty($persona->nro_documento)){
						$modeloExistente = self::buscarPorTipoCuit($persona->tipo_persona, $persona->cuit);
					}else{
						$modeloExistente = self::buscarPorTipoCuit($persona->tipo_persona, $persona->nro_documento);
					}

					if(count($modeloExistente)!==0){//si existe-->la reemplaza
						static::unmap_modificado($modeloExistente,$persona);
						$modeloExistente->save();
						return self::map($modeloExistente);
					}else{//si no existe la creo
						$modelo = self::unmap($persona);
						$modelo->save();
						return self::map($modelo);
					}


				}catch(Exception $e){
					Log::error('Problema al crear la persona. '.$e);
					return null;
				}
		}

		/**
		* Modifica campos obligatorios de la persona que será el nuevo titular
		*/
		public static function modificarCamposNuevaPersonaTitular($id_persona,$datosNuevos){

			\Log::info("La persona es - modificarCamposNuevaPersonaTitular - id_persona", array($id_persona));
			\Log::info("La persona es - modificarCamposNuevaPersonaTitular - datosNuevos", array($datosNuevos));

			try{
				/*Verifico si la persona está cargada*/
				$persona=self::buscarPorIdModelo($id_persona);
				if(is_null($persona)){//si no existe devuelvo un error
					\Log::info("No encuentro la persona a cambiar campos - cambio titular");
					throw new \Exception("Error buscando la persona para cambiar los campos", 1);
				}
				\Log::info("La persona es - modificarCamposNuevaPersonaTitular - persona", array($persona));

				self::unmap_actualizar($persona, $datosNuevos);
				$persona->save();
				return true;
			}catch(\Exception $e){
				\Log::info("Ocurrió un problema al modificar los campos del titular - PersonaRepo");
				throw new \Exception("Error buscando la persona para cambiar los campos", 1);
			}
		}

		/**
		 * Devuelve una persona a partir de un modelo de persona
		 * @param type personaModelo
		 * @return \Dominio\Entidades\Habilitacion\persona
		 */
		private static function map(PersonaModelo $modelo) {

			\Log::info('MM - PersonaRepo - map - Modelo', array($modelo));

 			$persona = new Persona();
			$persona->id = $modelo->id;
			$persona->id_persona = $modelo->id_persona;
			$persona->sexo = $modelo->sexo;
			$persona->tipo_persona = $modelo->tipo_persona;
			$persona->tipo_sociedad = $modelo->tipo_sociedad;
			$persona->situacion_ganancia = $modelo->situacion_ganancia;
			$persona->nro_ingresos = $modelo->nro_ingresos;
			$persona->tipo_documento = $modelo->tipo_documento;
			$persona->nro_documento = $modelo->nro_documento;
			$persona->cuit = $modelo->cuit;
			$persona->apellido_nombre_razon = $modelo->apellido_nombre_razon;
			$persona->apellido_materno = $modelo->apellido_materno;
            $persona->domicilio_particular = $modelo->domicilio_particular;
            $persona->fecha_nacimiento = $modelo->fecha_nacimiento;
            $persona->ocupacion = $modelo->ocupacion;
            $persona->email = $modelo->email;
			$persona->id_localidad = $modelo->localidad;//es el objeto localidad

			if(is_null($modelo->localidad)){
				$persona->id_departamento = 0;//objeto departamento
			}else{
			$persona->id_departamento = $modelo->localidad->departamento;//objeto departamento
			}
			$persona->codigo_postal = $modelo->codigo_postal;
			$persona->subcodigo_postal = $modelo->subcodigo_postal;
			$persona->referente = $modelo->referente;
			$persona->datos_contacto = $modelo->datos_contacto;
			$persona->cbu = $modelo->cbu;
			return $persona;
		}



		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $persona
		 * @return \Dominio\Entidades\Habilitacion\persona
		 */
		private static function unmap(Persona $persona) {

			\Log::info('MM - PersonaRepo - unmap - Persona', array($persona));

			$modelo = new PersonaModelo();
			$modelo->id = $persona->id;
			$modelo->id_persona = $persona->id_persona;
			$modelo->sexo = $persona->sexo;
			$modelo->tipo_persona = $persona->tipo_persona;
			$modelo->tipo_sociedad = $persona->tipo_sociedad;
			$modelo->situacion_ganancia = $persona->situacion_ganancia;
			$modelo->nro_ingresos = $persona->nro_ingresos;
			$modelo->tipo_documento = $persona->tipo_documento;
			$modelo->nro_documento = $persona->nro_documento;
			$modelo->cuit = $persona->cuit;
			$modelo->apellido_nombre_razon = $persona->apellido_nombre_razon;
			$modelo->apellido_materno = $persona->apellido_materno;
            $modelo->domicilio_particular = $persona->domicilio_particular;
            $modelo->fecha_nacimiento = $persona->fecha_nacimiento;
            $modelo->ocupacion = $persona->ocupacion;
            $modelo->email = $persona->email;
            $modelo->id_localidad = $persona->id_localidad;
            $modelo->id_departamento = $persona->id_departamento;
            $modelo->codigo_postal = $persona->codigo_postal;
			$modelo->subcodigo_postal = $persona->subcodigo_postal;
			$modelo->referente = $persona->referente;
			$modelo->datos_contacto = $persona->datos_contacto;
			$modelo->cbu = $persona->cbu;

			return $modelo;
		}

		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $persona
		 * @return \Dominio\Entidades\Habilitacion\persona
		 */
		private static function unmap_modificado(PersonaModelo &$modelo, Persona $persona) {
			$modelo->id_persona = $persona->id_persona;
			$modelo->sexo = $persona->sexo;
			$modelo->tipo_persona = $persona->tipo_persona;
			$modelo->tipo_sociedad = $persona->tipo_sociedad;
			$modelo->situacion_ganancia = $persona->situacion_ganancia;
			$modelo->nro_ingresos = $persona->nro_ingresos;
			$modelo->tipo_documento = $persona->tipo_documento;
			$modelo->nro_documento = $persona->nro_documento;
			$modelo->cuit = $persona->cuit;
			$modelo->apellido_nombre_razon = $persona->apellido_nombre_razon;
			$modelo->apellido_materno = $persona->apellido_materno;
            $modelo->domicilio_particular = $persona->domicilio_particular;
            $modelo->fecha_nacimiento = $persona->fecha_nacimiento;
            $modelo->ocupacion = $persona->ocupacion;
            $modelo->email = $persona->email;
            $modelo->id_localidad = $persona->id_localidad;
            $modelo->id_departamento = $persona->id_departamento;
            $modelo->codigo_postal = $persona->codigo_postal;
			$modelo->subcodigo_postal = $persona->subcodigo_postal;
			$modelo->referente = $persona->referente;
			$modelo->datos_contacto = $persona->datos_contacto;
			$modelo->cbu = $persona->cbu;
		}

		private static function unmap_actualizar(PersonaModelo &$persona, $datosNuevos){

			\Log::info("PersonaRepo - unmap_actualizar - persona", array($persona));
			\Log::info("PersonaRepo - unmap_actualizar - datosNuevos", array($datosNuevos));

			$persona['sexo']=$datosNuevos['sexo_persona'];
			$persona['tipo_persona']=$datosNuevos['tipo_persona'];
			if($datosNuevos['tipo_persona']=="F"){
				//$persona['tipo_documento']=4;
				$persona['nro_documento']=substr($datosNuevos['cuit'],2,8);
				$persona['tipo_sociedad']='';
			}else{
				$persona['nro_documento']='';
				$persona['tipo_sociedad']=$datosNuevos['tipo_sociedad'];
			}
			$persona['situacion_ganancia']=$datosNuevos['tipo_situacion'];
			$persona['cuit']=$datosNuevos['cuit'];
			$persona['referente']=$datosNuevos['referente'];
			$persona['datos_contacto']=$datosNuevos['datos_contacto'];

// ini modificado - ADEN - 2024-04-25
//			$persona['nro_ingresos']=$datosNuevos['ingresos'];
			if(isset($datosNuevos['ingresos']))
				$persona['nro_ingresos'] = $datosNuevos['ingresos'];
			else
				$persona['nro_ingresos'] = '';
			
//			$persona['cbu']=$datosNuevos['cbu'];
			if(isset($datosNuevos['cbu']))
				$persona['cbu'] = $datosNuevos['cbu'];
			else
				$persona['cbu'] = '';
// fin modificado - ADEN - 2024-04-25
			$persona['email']=$datosNuevos['email'];
			$persona['fecha_nacimiento'] =$datosNuevos['fecha_nacimiento'];
		}

		/**
		* Función que retorna el modelo de persona
		* como un arreglo
		*/
		public static function arregloPersona(Persona $persona){
			$modeloPersona = self::unmap($persona);
			return $modeloPersona->toArray();
		}


		/**
		* Función para modificar los campos de la persona del cambio de titular /cotitular
		**/
		public static function modificarCampos($nroPersona, $campos){
			$pers = PersonaModelo::where('id','=',$nroPersona)->first();

			foreach ($campos as $key => $value) {
				$pers->$key=$value;
	}

			return $pers->save();
		}
	}
?>
