<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\TipoTramite;
	use models\Habilitacion\TipoTramiteModelo;

	class TipoTramiteRepo{

		/**
		 * Devuelve todos los tipo de trámite
		 * listaFuncion: 1-interna 0-agente/subagente
		 * es posible que lleguen los dos si se quiere mostrar todo
		 * @return \Dominio\Entidades\Habilitacion\TipoTramite
		 */
		public static function buscarTodos($listaFuncion){
			$usuario= \Session::get('usuarioLogueado.nombreTipoUsuario');

			$tiposTramites = TipoTramiteModelo::query()
							->where('habilitado','=',1);
				if(strcmp($usuario,'CAS')!=0)
					$tiposTramites=$tiposTramites->whereIn('interno',$listaFuncion);
				$tiposTramites=$tiposTramites->get();
			if (is_null($tiposTramites)) {
				return null;
			}else{
				foreach ($tiposTramites as $tipoTramite) {
					$result[] = self::map($tipoTramite);
				}

				return $result;
			}
		}

		/**
		 * Devuelve todos los tipo de trámite
		 */
		public static function buscarTodosLosIds($funcion){
			$tiposTramites = TipoTramiteModelo::where('habilitado','=',1)
				->where('interno', '=',$funcion)->get(array('id_tipo_tramite'));
			$tiposTramites->toArray();

			$i=1;
			foreach ($tiposTramites as $tpTramite) {
				$arregloTiposTramites[$i]=$tpTramite['id_tipo_tramite'];
				$i++;
			}

			return $arregloTiposTramites;
		}


		/**
		 * Devuelve un tipoTramite a partir del id que le llega
		 * como parámetro
		 * @param type $id
		 * @return \Dominio\Entidades\Habilitacion\TipoTramite
		 */
		public static function buscarPorId($id){
			$tipoTramite = TipoTramiteModelo::find($id);

			if (is_null($tipoTramite) || count($tipoTramite)==0) {
				return null;
			}else{
				return self::map($tipoTramite);
			}
		}

		/** Busca los tipos de trámites asociados a máquinas
		*/
		public static function buscarTiposTramitesMaquinas(){
			$tiposTramites = TipoTramiteModelo::query()
				->where('habilitado','=',1)
				->where('nombre_tramite','LIKE', '%maquina%')
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

		/**
		 * Devuelve un tipoTramite a partir del nombre que le llega
		 * como parámetro
		 * @param type $nombre
		 * @return \Dominio\Entidades\Habilitacion\TipoTramite
		 */
		public static function buscarSimilaresPorNombre($nombre, $listaFuncion){
            $tipoTramite = TipoTramiteModelo::where('nombre_tramite','LIKE', '%'.$nombre.'%')
            					->whereIn('interno',$listaFuncion)->first();

			if (is_null($tipoTramite)) {
				return null;
			}else{
				return self::map($tipoTramite);
			}
		}

		/**
		* Devuelve el nombre del tipo de tramite según sea para nuevo permiso o no
		*/
		public static function tituloTramite($id,$alta){
			$tituloTramite = TipoTramiteModelo::where('id_tipo_tramite','=', $id)
								->get(array('nombre_tramite', 'nombre_alta_tramite'));

            if($alta){
            	$tituloTramite=$tituloTramite[0]->nombre_alta_tramite;
            }else{
            	$tituloTramite=$tituloTramite[0]->nombre_tramite;
            }
			return $tituloTramite;
		}

		/**
		* Función que devuelve el nombre de la acción en función del nombre
		* que llega por parámetro
		*/
		public function nombreFuncion($funcion){
			try{
				$nombre = \DB::connection('suitecrm_cas')->select(\DB::raw('select sf.name FROM i2t01_sistemas_funciones sf
																		WHERE sf.`name` LIKE "%'.$funcion.'"%'));
				if(count($nombre)==0){
					throw new \Exception("Error Buscando el nombre de la función.", 1);
				}else{
					return $nombre;
				}
			}catch(\Exception $e){
				\Log::info('Excepción nombreFuncion: '+$e->getMessage());
			}
		}


		/**
		 * Devuelve un TipoTramite a partir de un modelo de TipoTramite
		 * @param type TipoTramiteModelo
		 * @return \Dominio\Entidades\Habilitacion\TipoTramite
		 */
		private static function map(TipoTramiteModelo $modelo) {
			$tipoTramite = new TipoTramite();
			$tipoTramite->idTipoTramite = $modelo->id_tipo_tramite;
			$tipoTramite->nombreTramite = $modelo->nombre_tramite;
			$tipoTramite->nombreAltaTramite = $modelo->nombre_alta_tramite;
			$tipoTramite->habilitacion = $modelo->habilitacion;
			$tipoTramite->abreviatura = $modelo->abreviatura;
			$tipoTramite->interno = $modelo->interno;

			return $tipoTramite;
		}


		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $tipoTramite
		 * @return \Dominio\Entidades\Habilitacion\TipoTramite
		 */
		private static function unmap(TipoTramite $tipoTramite) {
			$modelo = new TipoTramiteModelo;
			$modelo->id_tipo_tramite = $tipoTramite->idTipoTramite;
			$modelo->nombre_tramite = $tipoTramite->nombreTramite;
			$modelo->nombre_alta_tramite = $tipoTramite->nombreAltaTramite;
			$modelo->habilitado = $tipoTramite->habilitado;
			$modelo->abreviatura = $tipoTramite->abreviatura;
			$modelo->interno = $tipoTramite->interno;
			return $modelo;
		}


		/**
		* Función que retorna el modelo de TipoTramite
		* como un arreglo
		*/
		public static function arregloTipoTramite(TipoTramite $tipoTramite){
			$modeloTipoTramite = self::unmap($tipoTramite);
			return $modeloTipoTramite->toArray();
		}
		
		// 
		/**
		* Devuelve el cuit del permiso de usuario seteado para el tramite.(parametro : Permiso; retorna el cuit)
		*/
		public static function cuitPermiso($permiso){
			try{
				$cuit = \DB::connection('suitecrm_cas')->select(\DB::raw('SELECT UPPER(TRIM(p.dni_cuit_titular_c)) AS cuit
																			FROM age_permiso ap
																			INNER JOIN suitecrm_cas_dev.age_personas p ON ap.age_personas_id_c = p.id  AND ap.estado="activo"
																			WHERE ap.id_permiso ='.$permiso));
				if(count($cuit)==0){
					throw new \Exception("Error Buscando el cuit del permiso.", 1);
				}else{
					return $cuit;
				}
			}catch(\Exception $e){
				\Log::info('Excepción nombreFuncion: '+$e->getMessage());
			}
		}
		
	}
?>
