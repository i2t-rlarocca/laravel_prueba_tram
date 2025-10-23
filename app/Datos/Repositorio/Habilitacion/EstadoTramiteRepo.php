<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\EstadoTramite;
	use models\Habilitacion\EstadoTramiteModelo;

	class EstadoTramiteRepo{

		/**
		 * Devuelve todos los tipo de trámite
		 * @return \Dominio\Entidades\Habilitacion\EstadoTramite
		 */
		public static function buscarTodos(){
			$estados = EstadoTramiteModelo::orderBy('id_estado_tramite')->get();

			if (is_null($estados)||count($estados)==0) {
				return null;
			}else{
				foreach ($estados as $estado) {
					$result[]=self::map($estado);
				}
				return $result;
			}
		}


		/**
		 * Devuelve todos los posibles estados
		 * posteriores para un trámite.
		 * @return \Dominio\Entidades\Habilitacion\EstadoTramite
		 */
		public static function estadosPosibles($estadoInicial, $usuarioLogueado, $tipoTramite){
			
			
			$tipoUsuario = $usuarioLogueado['nombreTipoUsuario'];

			if(($tipoTramite == 6 || $tipoTramite == 13)  //incorporar maquina
			//if($tipoTramite == 6 //incorporar maquina
				&& $estadoInicial == 0 //solicitud ingresada
				&& ($tipoUsuario == 'CAS' || $tipoUsuario == 'CAS_ROS') //usuario interno
			){
					$query=EstadoTramiteModelo::query();
					$query = $query->whereIn('id_estado_tramite',array($estadoInicial, 9, 10));	 //rechazado, finalizado
					$estados = $query->get();
			}
			else{
				$actual = EstadoTramiteModelo::find($estadoInicial);
				$result[] = self::map($actual);
				$estados = $actual
					->estadosPosibles()
					->where('usuario_habilitado', 'like', "%$tipoUsuario%")
					->get();
			}
			
			/*
			switch ($tipo_tramite) {
					case 6: // Incorporar Máquina
						if($estadoInicial == 0 //solicitud ingresada
							&& ($tipoUsuario == 'CAS' || $tipoUsuario == 'CAS_ROS') //usuario interno
						){
								$query=EstadoTramiteModelo::query();
								$query = $query->whereIn('id_estado_tramite',array($estadoInicial, 9, 10));	 //rechazado, finalizado
								$estados = $query->get();
						}
						break;
					case 13: // Renuncia
						if($estadoInicial == 0 //solicitud ingresada
							&& ($tipoUsuario == 'CAS' || $tipoUsuario == 'CAS_ROS') //usuario interno
						){
								$query=EstadoTramiteModelo::query();
								$query = $query->whereIn('id_estado_tramite',array($estadoInicial, 9, 10));	 //rechazado, finalizado
								$estados = $query->get();
						}
						break;
					default:
						$actual = EstadoTramiteModelo::find($estadoInicial);
						$result[] = self::map($actual);
						$estados = $actual
							->estadosPosibles()
							->where('usuario_habilitado', 'like', "%$tipoUsuario%")
							->get();
						break;
				}
				*/	
			
			

			foreach ($estados as $estado)
				$result[] = self::map($estado);
			return $result;
		}


		/**
		 * Devuelve un tipo de trámite
		 * @return \Dominio\Entidades\Habilitacion\EstadoTramite
		 */
		public static function buscarPorId($id){
            $estado = EstadoTramiteModelo::find($id);

			if (is_null($estado)) {
				return null;
			}else{
				return self::map($estado);
			}
		}

		/**
		 * Devuelve un tipo de trámite
		 * @return \Dominio\Entidades\Habilitacion\EstadoTramite
		 */
		public static function buscarPorId_arreglo($id){
            $estado = EstadoTramiteModelo::find($id);

			if (is_null($estado)) {
				return null;
			}else{
				$estado = self::map($estado);
				$estado = self::arregloEstadoTramite($estado);
				return $estado;
			}
		}



		/**
		 * Devuelve un EstadoTramite a partir de un modelo de EstadoTramite
		 * @param type EstadoTramiteModelo
		 * @return \Dominio\Entidades\Habilitacion\EstadoTramite
		 */
		private static function map(EstadoTramiteModelo $modelo) {
			$estado = new EstadoTramite();
			$estado->idEstadoTramite = $modelo->id_estado_tramite;
			$estado->descripcionTramite = $modelo->descripcion_tramite;



			return $estado;
		}


		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $EstadoTramite
		 * @return \Dominio\Entidades\Habilitacion\EstadoTramite
		 */
		private static function unmap(EstadoTramite $estado) {
			$modelo = new EstadoTramiteModelo;
			$modelo->id_estado_tramite = $estado->idEstadoTramite;
			$modelo->descripcion_tramite = $estado->descripcionTramite;

			return $modelo;
		}


		/**
		* Función que retorna el modelo de EstadoTramite
		* como un arreglo
		*/
		public static function arregloEstadoTramite(EstadoTramite $estado){
			$modeloEstadoTramite = self::unmap($estado);
			return $modeloEstadoTramite->toArray();
		}
	}
?>
