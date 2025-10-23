<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Usuario;
	use models\Habilitacion\UsuarioModelo;
	use models\Habilitacion\UsuarioSesionModelo;
	use Datos\Repositorio\Habilitacion\ProvinciaRepo;

	class UsuarioRepo{

		/**
		 * Devuelve un usuario a partir del nombre de usuario
		 * que le llega como parámetro
		 * @param type $nombreUsuario
		 * @return \Dominio\Entidades\Premios\Usuario
		 */
		public static function buscar($userName){
			$usuario = UsuarioModelo::where('nombreUsuario','=',$userName)
				->where('deleted','=',0)->first();

			if (is_null($usuario)) {
				return null;
			}else{
				return self::map($usuario);
			}
		}


		/**
		 * Devuelve un usuario a partir del id que le llega
		 * como parámetro
		 * @param type $id
		 * @return \Dominio\Entidades\Premios\Usuario
		 */
		public static function buscarPorId($id){
			$usuario = UsuarioModelo::find($id);

			if (is_null($usuario)) {
				return null;
			}else{
				return self::map($usuario);
			}
		}


		/**
		 * Devuelve todos los usuarios
		 * @return [Dominio\Entidades\Premios\Usuario]
		 */
		public static function buscarTodos(){
			$usuarios=UsuarioModelo::all();

			$result = array();
			foreach ($usuarios  as $usuario) {
				$result[] = self::map($usuario);
			}

			return $result;
		}

		/*********************
		Buscar por permiso
		**********/
		public static function buscarPorPermiso($permiso){
		$usuario = UsuarioModelo::where('permiso','=',$permiso)
            						->where('deleted','=',0)->first();

			if (is_null($usuario)) {
				return null;
			}else{
				return self::map($usuario);
			}

		}

		/**
		 * Función que se encarga de verificar si el usuario
		 * aún está logueado en el portal
		 */
		public static function controlSesion($id_portal, $letra_portal){
			$sesionPortal=[];

			if(!isset($id_portal)){
				$sesionPortal['sesionActiva']=false;
				return $sesionPortal;
			}
			$query='select * from session_usuario_v where userid='.$id_portal.' and portal='.$letra_portal;

			$sesionActiva=\DB::connection('suitecrm_cas')->select(\DB::raw($query));

			if (count($sesionActiva)==0 || is_null($sesionActiva)) {
				$sesionPortal['sesionActiva']=false;
				return $sesionPortal;
			}else{
				$sesionPortal['sesionActiva']=true;
				return $sesionPortal;
		}
		}


		/**
		 * Devuelve un Usuario a partir de un modelo de usuario
		 * @param type ProvinciaModelo
		 * @return \Dominio\Entidades\Premios\Usuario
		 */
		private static function map(UsuarioModelo $modelo) {
			$usuario = new Usuario();
			$usuario->id = $modelo->id;
			$usuario->nombreUsuario = $modelo->nombreUsuario;
			$usuario->nombre = $modelo->nombre;
			$usuario->apellido = $modelo->apellido;
			$usuario->tipoUsuario = $modelo->tipoUsuario;
			$usuario->nombreTipoUsuario = $modelo->nombreTipoUsuario;
			$usuario->descripcionTipoUsuario = $modelo->descripcionTipoUsuario;
			$usuario->provincia = $modelo->provincia;
			$usuario->localidadAgencia = $modelo->localidadAgencia;
			$usuario->codigoPostalAgencia = $modelo->codigoPostalAgencia;
			$usuario->subcodigoPostalAgencia = $modelo->subcodigoPostalAgencia;
			$usuario->agente = $modelo->agente;
			$usuario->subAgente = $modelo->subAgente;
			$usuario->permiso = $modelo->permiso;
			$usuario->razonSocial = $modelo->razonSocial;
			$usuario->titular = $modelo->titular;
			$usuario->domicilioAgencia = $modelo->domicilioAgencia;
			$usuario->email = $modelo->email;
			$usuario->idLocalidad = $modelo->localidad->id; //$modelo->localidad() me devuelve un objeto de tipo localidad
			$usuario->listaFunciones = self::funcionesUsuario($modelo->id);
			if($modelo->nombreTipoUsuario!='CAS' && $modelo->nombreTipoUsuario !='CAS_ROS'){
				$usuario->departamentoNombre =$modelo->departamentoAgencia;//$modelo->localidad->departamento->descripcion;//de la entidad localidad saco el objeto departamento y de éste obtengo la descripción.
			}else{
				$usuario->departamentoNombre ="LA CAPITAL";
			}
			$usuario->estado_comercializacion=$modelo->estado_comercializacion;
			$usuario->cbu=$modelo->cbu;

			return $usuario;
		}


		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $usuario
		 * @return \Dominio\Entidades\Premios\Usuario
		 */
		private static function unmap(Usuario $usuario) {
			$modelo = new UsuarioModelo;
			$modelo->id = $usuario->id;
			$modelo->nombreUsuario = $usuario->nombreUsuario;
			$modelo->nombre = $usuario->nombre;
			$modelo->apellido = $usuario->apellido;
			$modelo->tipoUsuario = $usuario->tipoUsuario;
			$modelo->nombreTipoUsuario = $usuario->nombreTipoUsuario;
			$modelo->descripcionTipoUsuario = $usuario->descripcionTipoUsuario;
			$modelo->provincia = $usuario->provincia;
			$modelo->localidadAgencia = $usuario->localidadAgencia;
			$modelo->codigoPostalAgencia = $usuario->codigoPostalAgencia;
			$modelo->subcodigoPostalAgencia = $usuario->subcodigoPostalAgencia;
			$modelo->agente = $usuario->agente;
			$modelo->subAgente = $usuario->subAgente;
			$modelo->permiso = $usuario->permiso;
			$modelo->razonSocial = $usuario->razonSocial;
			$modelo->titular = $usuario->titular;
			$modelo->domicilioAgencia = $usuario->domicilioAgencia;
			$modelo->email = $usuario->email;
			$modelo->estado_comercializacion=$usuario->estado_comercializacion;
			$modelo->cbu = $usuario->cbu;
			return $modelo;
		}


		/**
		* Función para verificar qué funciones tiene habilitadas el usuario.
		* Devuelve una lista de las funciones
		**/
		public static function funcionesUsuario($id){
			try{

				$query="SELECT DISTINCT f.`name` AS nombre_funcion
						FROM i2t01_sistemas_funciones f
							JOIN i2t01_sistemas_funciones_i2t01_tipos_usuarios_1_c rftu ON rftu.`i2t01_sist0347nciones_ida` = f.id AND rftu.deleted = 0
							JOIN i2t01_tipos_usuarios tu ON tu.`id` = rftu.`i2t01_sist3f3dsuarios_idb` AND tu.`deleted` = 0
							JOIN `i2t01_sistemas_funciones_i2t01_sistemas_c` sfs ON sfs.`i2t01_sist2310nciones_idb` = f.`id`
							JOIN `i2t01_sistemas` s ON s.`id` = sfs.`i2t01_sistemas_funciones_i2t01_sistemasi2t01_sistemas_ida` AND s.`deleted` = 0
							JOIN users_cstm uc ON uc.`i2t01_tipos_usuarios_id_c` = tu.`id`
							JOIN users u ON u.id = uc.id_c AND u.`deleted` = 0
						WHERE f.`deleted` = 0
							AND s.`name` IN ( 'Tramites','Administracion_Permisos')
							AND u.id ='".$id."'

						UNION

						SELECT sf.`name` AS nombre_funcion
						FROM `i2t01_sistemas_funciones_users_c` sfu
							JOIN `i2t01_sistemas_funciones` sf ON sf.`id` = sfu.`i2t01_sistemas_funciones_usersi2t01_sistemas_funciones_ida` AND sf.deleted=0
							JOIN `i2t01_sistemas_funciones_i2t01_sistemas_c` sfs ON sfs.`i2t01_sist2310nciones_idb` = sf.`id` AND sfs.deleted=0
							JOIN `i2t01_sistemas` s ON s.`id` = sfs.`i2t01_sistemas_funciones_i2t01_sistemasi2t01_sistemas_ida` AND s.deleted=0
						WHERE sfu.deleted=0 AND s.`name` IN( 'Tramites','Administracion_Permisos')
						AND sfu.`i2t01_sistemas_funciones_usersusers_idb`='".$id."'
						ORDER BY 1";

				$listaFunciones = \DB::connection('suitecrm_cas')->select(\DB::raw($query));

				/*\DB::connection('suitecrm_cas')->select(\DB::raw('select sf.`name` AS nombre_funcion -- sfs.`i2t01_sist2310nciones_idb` AS id_funcion, sf.`description` AS descripcion
															FROM `i2t01_sistemas_funciones_users_c` sfu
															INNER JOIN `i2t01_sistemas_funciones` sf ON sf.`id` = sfu.`i2t01_sistemas_funciones_usersi2t01_sistemas_funciones_ida`
															INNER JOIN `i2t01_sistemas_funciones_i2t01_sistemas_c` sfs ON sfs.`i2t01_sist2310nciones_idb` = sf.`id`
															INNER JOIN `i2t01_sistemas` s ON s.`id` = sfs.`i2t01_sistemas_funciones_i2t01_sistemasi2t01_sistemas_ida` AND (s.`name` = "Tramites" || s.`name`="Administracion_Permisos")
															WHERE sfu.`i2t01_sistemas_funciones_usersusers_idb`="'.$id.'"'));*/

				if(count($listaFunciones)==0){
					//throw new \Exception("Error Buscando la lista de funciones de usuario.", 1);
					return $listaFunciones;
				}else{
					$funciones = array();
					foreach ($listaFunciones as $funcion) {
						$arregloFunciones=$funcion->nombre_funcion;
						$funciones[]=$arregloFunciones;
					}

					return $funciones;

				}
			}catch(\Exception $e){
				\Log::info('Excepción lista funciones usuario: '+$e->getMessage());
			}
		}

		/**
		* Función que retorna el modelo de usuario
		* como un arreglo
		*/
		public static function arregloUsuario(Usuario $usuario){
			$modeloUsuario = self::unmap($usuario);
			 $modeloUsuario = $modeloUsuario->toArray();
			 $modeloUsuario['listaFunciones'] = self::funcionesUsuario($modeloUsuario['id']);
			 return $modeloUsuario;
		}


	}
?>
