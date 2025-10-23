<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\AdjuntoTramite;
	use models\Habilitacion\AdjuntoTramiteModelo;
	
	class AdjuntoTramiteRepo{
				
		/**
		 * Devuelve todos los adjuntos
		 * @return \Dominio\Entidades\Habilitacion\AdjuntoTramite
		 */
		public static function buscarTodos(){
            $adjuntosTramite = AdjuntoTramiteModelo::all();
			
			if (is_null($adjuntosTramite)) {
				return null;
			}else{
				foreach ($adjuntosTramite as $adjuntoTramite) {
					$result[]=self::map($adjuntoTramite);
				}
				return $result;
			} 
		}


		/**
		 * Devuelve un adjuntoTramite a partir del id que le llega 
		 * como parámetro
		 * @param type $id
		 * @return \Dominio\Entidades\Habilitacion\AdjuntoTramite
		 */
		public static function buscarPorId($id){
            $adjuntoTramite = AdjuntoTramiteModelo::find($id);
			
			if (is_null($adjuntoTramite)) {
				return null;
			}else{
				return self::map($adjuntoTramite);
			} 
		}
		
		/**
		 * Devuelve un adjuntoTramite a partir del id que le llega 
		 * como parámetro
		 * @param type $nroTramite
		 * @return \Dominio\Entidades\Habilitacion\AdjuntoTramite
		 */
		public static function buscarPorNroTramite($nroTramite){
            $adjuntosTramite = AdjuntoTramiteModelo::where('nro_tramite','=',$nroTramite)->get();
			
			if (is_null($adjuntosTramite)) {
				return null;
			}else{
				foreach ($adjuntosTramite as $adjuntoTramite) {
					$result[]=self::map($adjuntoTramite);
				}
				return $result;
			} 
		}
		
		public static function buscarParaActualizar($nroTramite, $ruta){
			$adjunto = AdjuntoTramiteModelo::where('nro_tramite','=',$nroTramite)
					->where('ruta_adjunto','=',$ruta)->first();
			if (is_null($adjunto)) {
				return null;
			}else{
				return self::map($adjunto);
			} 
		}
		
		/*************************************************
		* 	Elimina proceso sin registros para exportar	 *
		*************************************************/
		
		public static function eliminarAdjunto($nroTramite, $ruta){
			// Conseguimos el objeto
			$adjunto=AdjuntoTramiteModelo::where('nro_tramite','=',$nroTramite);
							->where('ruta_adjunto','=',$ruta)->first();
			// Lo eliminamos de la base de datos
			$adjunto->delete();
			// DB::table('users')->where('votes', '<', 100)->delete();
			if (is_null($adjunto)) {
				return null;
			}else{
				return true;
			} 
		
		}
		
		
		public static function DeleteAdjunto($nroTramite,$ruta){
			try{
				$db = \DB::connection('suitecrm');
				$query = "DELETE FROM adjuntos_tramites WHERE nro_tramite = '".$nroTramite."' and ruta_adjunto ='".$ruta."'";
				//$query.='VALUES ("'.$id_ejec_proc.'","'.$nombre_proceso.'", NOW(),NOW(), 0, "'.$id_exp_tipo.'","'.$id_auditoria.'","'.$envio.'","Nuevo", CURDATE(), DATE_FORMAT(NOW(), "%T"),"'.$nombre_proceso.'","'.$paq_path.'", "'.$mail_destino_paq.'");';

				$resultadoDELETE = $db->getpdo()->exec($query);
				\DB::disconnect('suitecrm');
				if(count($resultadoDELETE)>0){
					return true;
				}else{
					return null;
				}
			}catch(\Exception $e){
				\Log::info("Error realizando delete adjuntos tramite");
				\Log::info($e);
				return false;
			}
		}

		public static function cargarAdjuntos($adjuntos){
				try{
					foreach ($adjuntos as $adjunto) {
						$existe = self::buscarParaActualizar($adjunto->nroTramite,$adjunto->rutaAdjunto);
						if(count($existe)!==0){//hay que actualizar
							$existe = self::unmap($existe);
							$existe->save(); 
							
						}else{
							$modelo = self::unmap($adjunto);
							$modelo->save(); 
						}
					}
					
				}catch(Exception $e){
					Log::error('Problema al insertar el archivo adjunto. '.$e);
					return null;
				}		
			
		}


		/**
		 * Devuelve un AdjuntoTramite a partir de un modelo de AdjuntoTramite
		 * @param type AdjuntoTramiteModelo
		 * @return \Dominio\Entidades\Habilitacion\AdjuntoTramite
		 */
		private static function map(AdjuntoTramiteModelo $modelo) {        
			$adjuntoTramite = new AdjuntoTramite();
			$adjuntoTramite->id = $modelo->id;
			$adjuntoTramite->nroTramite = $modelo->nro_tramite;
			$adjuntoTramite->rutaAdjunto = $modelo->ruta_adjunto; 
			$adjuntoTramite->permiso = $modelo->permiso;        
			$adjuntoTramite->idTipoDoc = $modelo->id_tipo_doc;        
			
			
			return $adjuntoTramite;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $AdjuntoTramite
		 * @return \Dominio\Entidades\Habilitacion\AdjuntoTramite
		 */
		private static function unmap(AdjuntoTramite $adjuntoTramite) {
			$modelo = new AdjuntoTramiteModelo;
			$modelo->id = $adjuntoTramite->id;
			$modelo->nro_tramite = $adjuntoTramite->nroTramite;
			$modelo->ruta_adjunto = $adjuntoTramite->rutaAdjunto;
			$modelo->permiso = $adjuntoTramite->permiso;
			$modelo->id_tipo_doc = $adjuntoTramite->idTipoDoc;
			
			return $modelo;
		}		
			

		/**
		* Función que retorna el modelo de AdjuntoTramite
		* como un arreglo
		*/
		public static function arregloAdjuntoTramite(AdjuntoTramite $adjuntoTramite){
			$modeloAdjuntoTramite = self::unmap($adjuntoTramite);
			return $modeloAdjuntoTramite->toArray();
		}	
	}
?>