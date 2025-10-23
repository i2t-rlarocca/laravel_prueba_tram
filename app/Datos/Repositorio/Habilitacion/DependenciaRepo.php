<?php
	
	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Dependencia;
	use models\Habilitacion\DependenciaModelo;
	
	class DependenciaRepo{
		
		public static function cargarDependencia($dependencia){
				try{
					$modelo = self::unmap($dependencia);
					$modelo->save(); 
					return self::map($modelo);	
					
				}catch(Exception $e){
					Log::error('Problema al crear el cambio de dependencia. '.$e);
					return null;
				}		
			
		}
		
		/**
		 * Devuelve un cambio de dependencia a partir del nro de trámite que le llega 
		 * como parámetro
		 * @param type $nroTramite
		 * @return \Dominio\Entidades\Habilitacion\dependencia
		 */
		public static function buscarPorNroTramite($nroTramite){
            $dependencia = DependenciaModelo::where('nro_tramite','=',$nroTramite)->first();
			
			if (is_null($dependencia) || count($dependencia)==0) {
				return null;
			}else{
				$dependencia = self::map($dependencia);
				return self::arregloDependencia($dependencia);
			} 
		}

				
		/**
		 * Devuelve una Dependencia a partir de un modelo de Dependencia
		 * @param type ModeloDependencia
		 * @return \Dominio\Entidades\Habilitacion\Dependencia
		 */
		private static function map(DependenciaModelo $modelo) { 
			$dependencia = new Dependencia();
			$dependencia->id = $modelo->id;
			$dependencia->nro_tramite=$modelo->nro_tramite;
			$dependencia->nro_red_anterior = $modelo->nro_red_anterior;
			$dependencia->nro_pto_vta_anterior = $modelo->nro_pto_vta_anterior;
			$dependencia->nro_red_actual = $modelo->nro_red_actual;
			$dependencia->nro_pto_vta_nuevo = $modelo->nro_pto_vta_nuevo;
			$dependencia->motivo_cambio = $modelo->motivo_cambio;        
      
			
			return $dependencia;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $dependencia
		 * @return \Dominio\Entidades\Habilitacion\Dependencia
		 */
		private static function unmap(Dependencia $dependencia) {
			$modelo = new DependenciaModelo();
			$modelo->id = $dependencia->id;
			$modelo->nro_tramite = $dependencia->nro_tramite;
			$modelo->nro_red_anterior = $dependencia->nro_red_anterior;
			$modelo->nro_pto_vta_anterior = $dependencia->nro_pto_vta_anterior;
			$modelo->nro_red_actual = $dependencia->nro_red_actual;
			$modelo->nro_pto_vta_nuevo = $dependencia->nro_pto_vta_nuevo;
			$modelo->motivo_cambio = $dependencia->motivo_cambio;        

			
			return $modelo;
		}


		

		/**
		* Función que retorna el modelo de Dependencia
		* como un arreglo
		*/
		public static function arregloDependencia(Dependencia $dependencia){
			$modeloDependencia = self::unmap($dependencia);
			return $modeloDependencia->toArray();
		}
		
		/**
		* Función que modifica el campo del nuevo subagente
		*/
		public static function modificaNuevoSubagente($idTramite,$nroSubAgente){
		
			try{
				$dependencia=DependenciaModelo::where("nro_tramite","=",$idTramite)->first();	
				$dependencia->nro_pto_vta_nuevo = $nroSubAgente; 
				$dependencia->save();
				return true;
			}catch(Exception $e){
				return false;
			}
		}
		
		/**
		* Retorna el número de subagente asignado 
		*/
		public static function obtenerNroNuevoSubAgente($nroTramite){
			$dependencia=DependenciaModelo::where("nro_tramite","=",$nroTramite)->first();
			return $dependencia->nro_pto_vta_nuevo;
		}	


		/**
		* Función para modificar los campos del cambio de dependencia
		**/
		public static function modificarCampos($nroTramite, $campos){
			$dep = DependenciaModelo::where('nro_tramite','=',$nroTramite)->first();

			foreach ($campos as $key => $value) {
				$dep->$key=$value;
	}
			
			return $dep->save();
		}

		/**
		* Verifico si se completaron todos los campos del trámite
		**/
		public static function CompletoTodosLosCampos($nroTramite){
			$depend = DependenciaModelo::where('nro_tramite','=',$nroTramite)->first();

			if(empty($depend->motivo_cambio)){
				return false;
			}   

			return true;
		}

	}
?>