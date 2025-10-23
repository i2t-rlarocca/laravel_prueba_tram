<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\TipoDocumento;
	use models\Habilitacion\TipoDocumentoModelo;

	class TipoDocumentoRepo{

				 
		 /**
		 * Devuelve todas los documentos
		 * @return [Dominio\Entidades\Habilitacion\TipoDocumento
		 */
		public static function buscarTodos(){
			$tipoDocumentos=TipoDocumentoModelo::all();
			\Log::info('MM - R-TipoDocumento - buscarTodos - tipoDocumentos: ',array($tipoDocumentos));
			$result = array();        
			foreach ($tipoDocumentos  as $tipodoc) {
				\Log::info('MM - R-TipoDocumento - tipodoc: ',array($tipodoc));
				$result[] = self::map($tipodoc);
			}        
        
			return $result;       
		}
		
		/**
		 * Devuelve tipo documento
		 * @param type $id
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public static function buscarPorId($id){
			$tipoDocu = TipoDocumentoModelo::find($id);
			
			if (is_null($tipoDocu)) {
				return null;
			}else{
				return self::map($tipoDocu);
			} 
		}
		
		/**
		 * Devuelve tipo documento
		 * @param type $id
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		
		public static function buscarPorPrefijo($prefijo){
			
            // $tipoDocumentos = TipoDocumentoModelo::where('pref_nom_archivo_serv','=',$prefijo)->get();
			$tipoDocumentos = TipoDocumentoModelo::query()
				->where('pref_nom_archivo_serv','=', $prefijo)
				->get();
			// $tipoDocumentos=TipoDocumentoModelo::all();
			\Log::info('MM - tipodocumentorepo - buscarPorPrefijo - tipoDocumentos: ',array($tipoDocumentos));
			$result = array();        
			foreach ($tipoDocumentos  as $tipodoc) {
				\Log::info('MM - tipodocumentorapo - buscarPorPrefijo - entra tipodoc: ',array($tipodoc));
				$result[] = self::map($tipodoc);
			}        
        
			return $result;  
			
			// $tiposTramites = TipoDocumentoModelo::query()
				// ->where('pref_nom_archivo_serv','=', $prefijo)
				// ->get();
			
			
			// if (is_null($tipoDocu)) {
				// return null;
			// }else{
				// foreach ($tipoDocu as $tipo) {
					// $result[]=self::map($tipo);
				// }
				// return $result;
			// } 
		}
		
		
		/**
		 * Devuelve tipo documento
		 * @param type $id
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		
		public static function buscarPorPrefijoLike($prefijo){
			$tiposTramites = TipoDocumentoModelo::query()
				->where('pref_nom_archivo_serv','LIKE', '%{$prefijo}%')
				->get();

			if (is_null($tiposTramites)) {
				return null;
			}else{
				$result = [];
				foreach ($tiposTramites as $tipoTramite) {
					$result[] = self::map($tipoTramite);
				}
 				return $result;
			}
		}
		 
		// public static function buscarTodos($listaFuncion){
			// $usuario= \Session::get('usuarioLogueado.nombreTipoUsuario');

			// $tiposDocumentos = TipoDocumentoModelo::query();
							// ->where('habilitado','=',1);
				// if(strcmp($usuario,'CAS')!=0)
					// $tiposDocumentos=$tiposDocumentos->whereIn('interno',$listaFuncion);
				// $tiposDocumentos=$tiposDocumentos->get();
			// if (is_null($tiposDocumentos)) {
				// return null;
			// }else{
				// foreach ($tiposDocumentos as $tipoDocumento) {
					// $result[] = self::map($tipoDocumento);
				// }

				// return $result;
			// }
		// }

		// /**
		 // * Devuelve todos los tipo de trámite
		 // */
		// public static function buscarTodosLosIds($funcion){
			// $tiposTramites = TipoTramiteModelo::where('habilitado','=',1)
				// ->where('interno', '=',$funcion)->get(array('id_tipo_tramite'));
			// $tiposTramites->toArray();

			// $i=1;
			// foreach ($tiposTramites as $tpTramite) {
				// $arregloTiposTramites[$i]=$tpTramite['id_tipo_tramite'];
				// $i++;
			// }

			// return $arregloTiposTramites;
		// }


		/**
		 * Devuelve un TipoTramite a partir de un modelo de TipoDocumentacion
		 * @param type TipoDocumentoModelo
		 * @return \Dominio\Entidades\Habilitacion\TipoDocumento
		 */
		private static function map(TipoDocumentoModelo $modelo) {
			$tipoDocumento = new TipoDocumento();
			$tipoDocumento->idTipoDoc = $modelo->id;
			$tipoDocumento->nombreDoc = $modelo->nombre;
			$tipoDocumento->accion = $modelo->accion;
			$tipoDocumento->prefNomArchivoServ = $modelo->pref_nom_archivo_serv;
			
			return $tipoDocumento;
		}


		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $tipoDocumento
		 * @return \Dominio\Entidades\Habilitacion\TipoDocumento
		 */
		private static function unmap(TipoDocumento $tipoDocumento) {
			$modelo = new TipoDocumentoModelo;
			$modelo->id = $tipoDocumento->idTipoDoc;
			$modelo->nombre = $tipoDocumento->nombreDoc;
			$modelo->accion = $tipoDocumento->accion;
			$modelo->pref_nom_archivo_serv = $tipoDocumento->prefNomArchivoServ;
			
			return $modelo;
		}

		/**
		* Función que retorna el modelo de TipoDocumento
		* como un arreglo
		*/
		public static function arregloTipoDocumento(TipoDocumento $tipoDocumento){
			$modeloTipoDocumento = self::unmap($tipoDocumento);
			return $modeloTipoDocumento->toArray();
		}
	}
?>
