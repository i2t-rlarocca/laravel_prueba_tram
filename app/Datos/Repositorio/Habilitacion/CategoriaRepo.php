<?php
	
	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Categoria;
	use models\Habilitacion\CategoriaModelo;
	
	class CategoriaRepo{
		
		public static function cargarCategoria($categoria){
				try{
					$modelo = self::unmap($categoria);
					$modelo->save(); 
					return self::map($modelo);	
					
				}catch(Exception $e){
					\Log::error('Problema al crear el cambio de categoria. '.$e);
					return null;
				}		
			
		}

		/*** Para trámites de tipo cambio de categoria: de subagente a agente***/
		/**
		 * Nuevo nº de red
		 **/
		public static function numeroNuevaRed($codigoPostal, $nro_red, $modalidad){
			//llamada a una función definida en la bd 
			$query = 'nro_nueva_red('.trim($codigoPostal).','. $nro_red.','. $modalidad.') as nro_red';
			\Log::info("numeroNuevaRed categoriaRepo: ", array($query));
			$nro = CategoriaModelo::select(\DB::raw($query))->first();
			return $nro['nro_red'];
		}
		
		/**
		 * Devuelve un cambio de categoria a partir del nro de trámite que le llega 
		 * como parámetro
		 * @param type $nroTramite
		 * @return \Dominio\Entidades\Habilitacion\categoria
		 */
		public static function buscarPorNroTramite($nroTramite){
            $categoria = CategoriaModelo::where('nro_tramite','=',$nroTramite)->get()->first();
			
			if (is_null($categoria) || count($categoria)==0) {
				return null;
			}else{
				$categoria = self::map($categoria);
				return CategoriaRepo::arregloCategoria($categoria);
			} 
		}
		
		/**
		* Buscar el cbu ingresado al inicio del trámite
		**/
		public static function buscarCategoriaCBU($nroTramite){
			$categoria=CategoriaModelo::where('nro_tramite','=',$nroTramite)->first();
			$cbu=$categoria['cbu'];
			return $cbu;
		}




		/**
		 * Verifica si la red propuesta es una red válida para
		 * ser ocupada
		 */
		public static function nuevaRedPropuesta($nroRed, $cp){
			$esRedValida = CategoriaModelo::select(\DB::raw('es_red_valida('.$nroRed.')'))->first();
			\Log::info('la red:', array($nroRed));
			\Log::info('es red valida:', array($esRedValida['es_red_valida('.$nroRed.')']));
			return $esRedValida['es_red_valida('.$nroRed.')'];
		}

		/**
		 * Modifica la nueva red 
		 **/
		public static function modificarRedNuevoAgente($nroSeguimiento,$nroNuevaRed, $cp){
			try{
				/*Verifico si la red puede ser usada*/

				if(!self::nuevaRedPropuesta($nroNuevaRed, $cp)){//si no puede ser usada busco una que sí pueda serlo
					$nroNuevaRed=self::numeroNuevaRed($cp, $nroNuevaRed, 1);
				}

				$categoria=CategoriaModelo::where("nro_tramite","=",$nroSeguimiento)->first();	
				$categoria->nro_red_nueva=$nroNuevaRed;
				//\Log::info('voy a guardar la categoria con la red que tengo: ', array($nroNuevaRed));
				return $categoria->save();
			}catch(Exception $e){
				return false;
			}
		}
		
		
		/**
		 * Modifica la nueva red del subagente 
		 */
		public static function modificarRedNuevoSubagente($nroSeguimiento,$nroNuevaRed, $nroSubAgente){
			try{
				\Log::info("modificarRedNuevoSubagente. cambio cat (cat repo 1): ", array($nroSeguimiento));
				\Log::info("modificarRedNuevoSubagente. cambio cat (cat repo 2): ", array($nroNuevaRed));
				\Log::info("modificarRedNuevoSubagente. cambio cat (cat repo 3): ", array($nroSubAgente));
				$categoria=CategoriaModelo::where("nro_tramite","=",$nroSeguimiento)->first();	
				$categoria->nro_red_nueva=$nroNuevaRed;
				$categoria->nro_pto_vta_nuevo = $nroSubAgente; 
				$categoria->save();
				return true;
			}catch(Exception $e){
				return false;
			}
		}

		/**
		 * Verifica si es un subagente que pasa a ser nuevo agente
		 */
		public static function esNuevoAgente($nro_seguimiento){
			$nuevaCategoria = CategoriaModelo::query()->where('nro_tramite','=',$nro_seguimiento)->get(array('categoria_nueva'));
			if($nuevaCategoria[0]['categoria_nueva']==0){//0-agente
				return 1;
			}else{
				return 0;
			}
				}

		/**
		* Verifica si el permiso puede, según tenga subagencias o no 
		* aprobarse el trámite de cambio de categoría
		**/
		public static function okAprobar($agente){
			try{

				/*$dbe=\DB::connection('suitecrm_cas');
				
				$ok = $dbe->select(\DB::raw('SELECT COUNT(a.id_c) as cant_subagencias
											 FROM accounts_cstm a 
											 WHERE a.estado_c="Activo"  
											       AND a.numero_subagente_c <> 0
											       AND a.numero_agente_c = (
																			SELECT numero_agente
																			FROM age_permiso p
																			WHERE p.id_permiso = '.$agente.')'
											)	
								);
				if($ok[0]->cant_subagencias==0){
					return true;
				}else{
					return false;
				}*/

				$cant_subagencias = CategoriaModelo::select(\DB::raw('subagencias_activas('.$agente.') as cantSub'))->first();
		
			
				if($cant_subagencias['cantSub']==0){
					return true;
				}else{
					return false;
				}
			}catch(\Exception $e){
				\Log::info("Exepción verificando si tiene subagencias o no.");
				return false;
			}
		}
		
				
		/**
		 * Devuelve una Categoria a partir de un modelo de Categoria
		 * @param type ModeloCategoria
		 * @return \Dominio\Entidades\Habilitacion\Categoria
		 */
		private static function map(CategoriaModelo $modelo) { 
			$categoria = new Categoria();
			$categoria->id = $modelo->id;
			$categoria->nro_tramite=$modelo->nro_tramite;
			$categoria->categoria_anterior = $modelo->categoria_anterior;
			$categoria->categoria_nueva = $modelo->categoria_nueva;
			$categoria->motivo_cambio = $modelo->motivo_cambio;        
      		$categoria->nro_red_anterior = $modelo->nro_red_anterior;
      		$categoria->nro_pto_vta_anterior = $modelo->nro_pto_vta_anterior;
			$categoria->nro_red_nueva = $modelo->nro_red_nueva;
			$categoria->nro_pto_vta_nuevo = $modelo->nro_pto_vta_nuevo;
			$categoria->cbu = $modelo->cbu;
			
			$categoria->idLocalidad = $modelo->id_localidad;
			$categoria->localidad = $modelo->nombre_localidad;
			$categoria->cpScpLocalidad = $modelo->cp_scp_localidad;

			
			return $categoria;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $categoria
		 * @return \Dominio\Entidades\Habilitacion\Categoria
		 */
		private static function unmap(Categoria $categoria) {
			$modelo = new CategoriaModelo();
			$modelo->id = $categoria->id;
			$modelo->nro_tramite = $categoria->nro_tramite;
			$modelo->categoria_anterior = $categoria->categoria_anterior;
			$modelo->categoria_nueva = $categoria->categoria_nueva;
			$modelo->motivo_cambio = $categoria->motivo_cambio;        
			$modelo->nro_red_anterior = $categoria->nro_red_anterior;
			$modelo->nro_pto_vta_anterior = $categoria->nro_pto_vta_anterior;
			$modelo->nro_red_nueva = $categoria->nro_red_nueva;
			$modelo->nro_pto_vta_nuevo = $categoria->nro_pto_vta_nuevo;
			$modelo->cbu = $categoria->cbu;
			
			$modelo->id_localidad = $categoria->idLocalidad;
			$modelo->nombre_localidad = $categoria->localidad;
			$modelo->cp_scp_localidad = $categoria->cpScpLocalidad;
			
			
			return $modelo;
		}


		/**
		* Función que retorna el modelo de Categoria
		* como un arreglo
		*/
		public static function arregloCategoria(Categoria $categoria){
			$modeloCategoria = self::unmap($categoria);
			return $modeloCategoria->toArray();
		}
		
		/**
		* Retorna el número de subagente asignado 
		*/
		public static function obtenerNroNuevoSubAgente($nroTramite){
			$categoria=CategoriaModelo::where("nro_tramite","=",$nroTramite)->first();
			return $categoria->nro_pto_vta_nuevo;
		}	
		
		/**
		* Función para modificar los campos de categoría
		**/
		public static function modificarCampos($nroTramite, $campos){
			$cat = CategoriaModelo::where('nro_tramite','=',$nroTramite)->first();

			foreach ($campos as $key => $value) {
				$cat->$key=$value;
	}
			
			return $cat->save();
		}

		/**
		* Verifico si se completaron todos los campos del trámite
		**/
		public static function CompletoTodosLosCampos($nroTramite){
			$cat = CategoriaModelo::where('nro_tramite','=',$nroTramite)->first();

			if(empty($cat->motivo_cambio)){
				return false;
			}         

			if($cat->nro_pto_vta_anterior <> 0){//Pasa a agente, entonces debe tener el cbu
				if(empty($cat->cbu)){
					\Log::info('no completo cbu y quiere ser agente: ');
					return false;
				} 
			}

			return true;
		}

		/**
		* Código postal de la localidad donde se generará el nuevo trámite
		**/
		public static function CpCategoria($nroTramite){
			$categoria=CategoriaModelo::where("nro_tramite","=",$nroTramite)->first();
			return $categoria->cp_scp_localidad;
		}
		
		/**
		* Verifico si se completaron los campos agencia y subagencia del trámite
		**/
		public static function ValidarAgenciaySubagencia($nroTramite){
			$categoria = CategoriaModelo::where('nro_tramite','=',$nroTramite)->first();
			\Log::info("MM - ValidarAgenciaySubagencia - categoria: ", array($categoria));
			// dependiendo de la categoría nueva
			// $categoria->categoria_nueva == 0 -> de subagencia a agencia
			// $categoria->categoria_nueva == 1 -> de agencia a subagencia
			
			
			if($categoria->categoria_nueva == 1){ // paso de agencia a subagencia
				if($categoria->nro_pto_vta_nuevo == 0){
					\Log::info("1-control ValidarAgenciaySubagencia nro_pto_vta_nuevo");
					return false;
				}   
				if($categoria->nro_red_nueva == 0){
					\Log::info("2-control ValidarAgenciaySubagencia nro_red_nueva");
					return false;
				}   
			}else{ // paso de subagencia a agencia
				if(empty($categoria->nro_red_nueva) || $categoria->nro_red_nueva == 0){
					\Log::info("3-control nro_red_nueva no puede ser 0");
					return false;
				}               
				if($categoria->nro_pto_vta_nuevo != 0){
					\Log::info("4-control ValidarAgenciaySubagencia nro_pto_vta_nuevo");
					return false;
				}   
			}
			return true;             
		}



	}
?>