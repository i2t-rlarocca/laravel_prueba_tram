<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Agencia;
	use models\Habilitacion\AgenciaModelo;
	
	class AgenciaRepo{
				
		/**
		 * Devuelve todos los tipo de trámite 
		 * @return \Dominio\Entidades\Habilitacion\Agencia
		 */
		public static function buscarTodas(){
            $agencia = AgenciaModelo::all();
			
			if (is_null($agencia)) {
				return null;
			}else{
				return self::map($agencia);
			} 
		}
		

		
		/**
		 * Devuelve un Agencia a partir de un modelo de Agencia
		 * @param type AgenciaModelo
		 * @return \Dominio\Entidades\Habilitacion\Agencia
		 */
		private static function map(AgenciaModelo $modelo) {        
			$agencia = new Agencia();
			$agencia->id_agencia = $modelo->id_agencia;
			$agencia->nombre_agencia = $modelo->nombre_agencia;
			$agencia->permiso = $modelo->permiso; 
			$agencia->agente = $modelo->agente; 
			$agencia->subagente = $modelo->subagente;
			$agencia->domicilio = $modelo->domicilio;
			$agencia->provincia = $modelo->provincia;
			$agencia->localidad = $modelo->localidad;
			$agencia->codigo_postal = $modelo->codigo_postal;
			$agencia->email = $modelo->email;        
			$agencia->estado_red = $modelo->estado_red;        

			return $agencia;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $Agencia
		 * @return \Dominio\Entidades\Habilitacion\Agencia
		 */
		private static function unmap(Agencia $agencia) {
			$modelo = new AgenciaModelo;

			$modelo->id_agencia = $agencia->id_agencia;
			$modelo->nombre_agencia =$agencia->nombre_agencia;
			$modelo->permiso = $agencia->permiso; 
			$modelo->agente = $agencia->agente; 
			$modelo->subagente = $agencia->subagente;
			$modelo->domicilio = $agencia->domicilio;
			$modelo->provincia = $agencia->provincia;
			$modelo->localidad = $agencia->localidad;
			$modelo->codigo_postal = $agencia->codigo_postal;
			$modelo->email = $agencia->email;    
			$modelo->estado_red = $agencia->estado_red;    
			return $modelo;
		}		
			

		/**
		* Función que retorna el modelo de Agencia
		* como un arreglo
		*/
		public static function arregloAgencia(Agencia $agencia){
			$modeloAgencia = self::unmap($agencia);
			return $modeloAgencia->toArray();
		}	


		public static function buscarPorNumeroRed($nro_red){
			$agencia = AgenciaModelo::where('agente', '=', $nro_red)
						->where('subagente', '=', 0)
						->orderBy('estado_red', 'ASC')->first();//obtiene la red nºagente/0	
					
			if (is_null($agencia)) {
				return null;
			}else{
				return self::map($agencia);
			} 
		}



		/***************************************/
		/* Busca el mayor subagente de una red */
		/***************************************/
		public static function mayorSubagenteRed($nro_red, $nro_subagente, $modalidad){
			$query = 'CALL `verifica_subagente`('.$nro_red.','.$nro_subagente.','. $modalidad.')';
			\Log::info('AgenciaRepo mayorSubagenteRed: ', array($query));
			$resultado = \DB::connection('mysql')->select(\DB::raw($query));
			if($modalidad == 0)
				return $resultado[0]->nro_subagente;
			else
				return $resultado[0]->valido;
	}

	}
?>