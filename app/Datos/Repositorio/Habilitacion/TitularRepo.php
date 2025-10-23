<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Titular;
	use models\Habilitacion\TitularModelo;
	use models\Habilitacion\PersonaModelo;

	class TitularRepo{

		/**
		 * Devuelve todos los titulares
		 * @return \Dominio\Entidades\Habilitacion\Titular
		 */
		public static function buscarTodos(){
			$titulares = TitularModelo::all();

			if (is_null($titulares)) {
				return null;
			}else{
				foreach ($titulares as $titular) {
					$result[] = self::map($titular);
				}
 				return $result;
			}
		}

		public static function cargarTitular($titular){
				try{

					\Log::info('AE - repositorio->cargarTitular - titular: ', array($titular));
					
					$modelo = self::unmap($titular);
					$modelo->save();
					return self::map($modelo);

				}catch(Exception $e){
					Log::error('Problema al crear el titular. '.$e);
					return null;
				}

		}

		/**
		 * Devuelve un titular a partir del id que le llega
		 * como parámetro
		 * @param type $id
		 * @return \Dominio\Entidades\Habilitacion\Titular
		 */
		public static function buscarPorId($id){
            $titular = TitularModelo::find($id);

			if (is_null($titular)) {
				return null;
			}else{
				return self::map($titular);
			}
		}

		/**
		 * Devuelve un titular a partir del nro de trámite que le llega
		 * como parámetro
		 * @param type $nroTramite
		 * @return \Dominio\Entidades\Habilitacion\Titular
		 */
		public static function buscarPorNroTramite($nroTramite){
            $titular = TitularModelo::where('nro_tramite','=',$nroTramite)->first();

			if (is_null($titular) || count($titular)==0) {
				return null;
			}else{
				$titular = self::map($titular);
				return TitularRepo::arregloTitular($titular);
			}
		}

		public function existe_delegacion($delegacion){
		  $db=\DB::connection('suitecrm_cas');
		  $q = $db->select(\DB::raw("SELECT  count(*) as cantidad  FROM delegaciones_IIBB  WHERE  codigo_delegacion=".$delegacion));
		  $results = $db->query($q, true);
		  $row = $db->fetchByAssoc($results);
		  if ($row['cantidad']>0){
		    return true;
		  }else{
		   return false;
		  }
		}


		/**
		 * Devuelve un Titular a partir de un modelo de Titular
		 * @param type TitularModelo
		 * @return \Dominio\Entidades\Habilitacion\Titular
		 */
		private static function map(TitularModelo $modelo) {
			$titular = new Titular();
			$titular->id = $modelo->id;
			$titular->nro_tramite = $modelo->nro_tramite;
			$titular->id_titular_viejo = $modelo->id_titular_viejo;
			$titular->id_titular_nuevo = $modelo->id_titular_nuevo;
			$titular->nro_permiso = $modelo->nro_permiso;
			$titular->motivo_cambio = $modelo->motivo_cambio;
			$titular->tipo_relacion = $modelo->tipo_relacion;
			$titular->tipo_vinculo = $modelo->tipo_vinculo;
			$titular->fecha_examen = $modelo->fecha_examen;
			$titular->intentos = $modelo->intentos;
			$titular->calificacion = $modelo->calificacion;
	
			return $titular;
		}


		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $titular
		 * @return \Dominio\Entidades\Habilitacion\Titular
		 */
		private static function unmap(Titular $titular) {
			$modelo = new TitularModelo;
			$modelo->id = $titular->id;
			$modelo->nro_tramite = $titular->nro_tramite;
			$modelo->id_titular_viejo = $titular->id_titular_viejo;
			$modelo->id_titular_nuevo = $titular->id_titular_nuevo;
			$modelo->nro_permiso = $titular->nro_permiso;
			$modelo->motivo_cambio = $titular->motivo_cambio;
			$modelo->tipo_relacion = $titular->tipo_relacion;
			$modelo->tipo_vinculo = $titular->tipo_vinculo;
			$modelo->fecha_examen = $titular->fecha_examen;
			$modelo->intentos = $titular->intentos;
			$modelo->calificacion = $titular->calificacion;

			return $modelo;
		}


		/**
		* Función que retorna el modelo de Titular
		* como un arreglo
		*/
		public static function arregloTitular(Titular $titular){
			$modeloTitular = self::unmap($titular);
			return $modeloTitular->toArray();
		}


		/**
		 * Devuelve el id de la nueva persona
		 * como parámetro
		 * @param type $nroTramite
		 * @return \Dominio\Entidades\Habilitacion\Titular
		 */
		public static function obtenerPersona($nroTramite){
            $titular = TitularModelo::where('nro_tramite','=',$nroTramite)->first();

			if (is_null($titular) || count($titular)==0) {
				return null;
			}else{
				$titular = self::map($titular);
				return $titular->id_titular_nuevo;
	}
		}


		/**
		* Función para modificar los campos del titular / cotitular
		**/
		public static function modificarCampos($nroTramite, $campos){

			\Log::info("AE - TitularRepo - modificarCampos - campos: ",array($campos));

			$tit = TitularModelo::where('nro_tramite','=',$nroTramite)->first();
			foreach ($campos as $key => $value) {
				$tit->$key=$value;
			}

			return $tit->save();
		}


		/**
		* Verifico si se completaron todos los campos del trámite
		**/
		public static function CompletoTodosLosCampos($nroTramite){
			$titu = TitularModelo::where('nro_tramite','=',$nroTramite)->first();
			$pers = PersonaModelo::where('id','=',$titu->id_titular_nuevo)->first();

			if(empty($titu->motivo_cambio)){
				\Log::info("motivo 1");
				return false;
			}

			if($pers->tipo_persona == "F"){
	            if(empty($pers->fecha_nacimiento)){
	            	\Log::info("fecha nac 1");
					return false;
				}
	            if(empty($pers->ocupacion)){
	            	\Log::info("ocup 1");
					return false;
				}
			}

			if(empty($pers->situacion_ganancia)){
				\Log::info("sit 1");
				return false;
			}
/* ADEN - 2024-04-25
			if(empty($pers->nro_ingresos) && !in_array($pers->situacion_ganancia, array('rni', 'exento', 'ni'))){
				\Log::info("nro ing 1");
				\Log::info("sit gan 1", array($pers->situacion_ganancia));
				return false;
			}
*/			
			if(empty($pers->cuit)){
				\Log::info("cuit  1");
				return false;
			}
			if(empty($pers->apellido_nombre_razon)){
				\Log::info("ape  1");
				return false;
			}
            if(empty($pers->domicilio_particular)){
				\Log::info("dom part  1");

				return false;
			}
            if(empty($pers->email)){
            	\Log::info("email  1");
				return false;
			}
            if(empty($pers->id_localidad)){
            	\Log::info("local 1");
				return false;
			}

			if(empty($pers->referente)){
				\Log::info("ref 1");
				return false;
			}
			if(empty($pers->datos_contacto)){
				\Log::info("datos cont 1");
				return false;
			}
/* ADEN - 2024-04-25
			if(empty($pers->cbu)){
				\Log::info("cbu 1");
				return false;
			}
*/
			return true;
		}

	}
?>
