<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Tramite;
	use models\Habilitacion\TramiteModelo;
	use models\Habilitacion\ArchivoModelo;

	class TramiteRepo extends BaseRepo{

		/**
		 * Devuelve todos los tramites
		 * @return \Dominio\Entidades\Habilitacion\Tramite
		 */
		public static function buscarTodos(){
            $tramites = TramiteModelo::all();

			if (is_null($tramites)) {
				return null;
			}else{
				foreach ($tramites as $tramite) {
					$result[] = self::map($tramite);
				}
				return $result;
			}
		}

		/**
		* Función que verifica si tiene trámites pendientes de informar
		* (cualquier estado)
		**/
		public static function tieneTramitePI($nroTramite,$permiso){
			$existeTramitePI=TramiteModelo::where('nro_tramite','<>',$nroTramite)
								->where('nro_permiso','=',$permiso)
								->whereIn('id_estado_tramite',array(1,2,3,4,5,6,7,10))
								->where('pendiente_informar','=',0)->get();
			if (is_null($existeTramitePI) || count($existeTramitePI)==0) {
				return false;
			}else{
				return true;
			}
		}

		/**
		* Función que busca los datos actuales del permiso
		*/
		public static function datosPermisoSuitecrm($nroPermiso, $tipoTramite){
			try{
				$datos = \DB::connection('suitecrm_cas')->select(\DB::raw('select UPPER(apermi.punto_venta_tipo) AS tipo_punto_venta_permiso, apermi.punto_venta_numero AS nro_punto_venta_permiso, UPPER(RTRIM(apermi.razon_social)) AS razon_social_permiso,
				RTRIM(aperso.name) AS nombre_titular, RTRIM(aperso.tbl_localidades_id_c) AS cp_scp_titular, RTRIM(aperso.sexo_c) AS sexo_titular, RTRIM(aperso.dni_cuit_titular_c) as dni_cuit_titular_c, RTRIM(aperso.domicilio_titular_c) as domicilio_titular_c,
				RTRIM(aperso.billing_address_city) AS ciudad_titular, RTRIM(aperso.billing_address_state) AS provincia_titular, RTRIM(alocal.domicilio_c) AS domicilio_local, RTRIM(alocal.billing_address_city) AS ciudad_local, RTRIM(alocal.billing_address_state) AS provincia_local,
				RTRIM(alocal.tbl_localidades_id_c) AS cp_scp_local, concat(r.`id_red`, "-", upper(trim(r.`nombre_red`))) as red, upper(trim(r.`nombre_red`)) as nombre_red, UPPER(RTRIM(alocal.`persona_contacto_c`)) AS referente, upper(rtrim(alocal.`persona_contacto_datos`)) AS datos_contacto, COALESCE(aperso.`cbu`,"") AS cbu,
					CONCAT(apermi.numero_agente, " / ", apermi.punto_venta_numero, " - ", UPPER(RTRIM(apermi.razon_social))) AS nombre_agencia
				FROM age_permiso apermi
				INNER JOIN age_personas aperso ON aperso.id=apermi.age_personas_id_c AND aperso.deleted=0
				INNER JOIN age_local alocal ON alocal.id=apermi.age_local_id_c AND alocal.deleted=0
				INNER JOIN age_red r ON r.`id`=apermi.`age_red_id_c`
				WHERE apermi.id_permiso='.$nroPermiso));
				if(count($datos)==0 || is_null($datos)){
					throw new \Exception("Error Buscando la ubicación por id.", 1);
				}else{
					$datosPermiso['nombre_agencia'] = $datos[0]->nombre_agencia;
					$datosPermiso['razon_social_permiso']= $datos[0]->razon_social_permiso;
					$datosPermiso['red']= $datos[0]->red;
					$datosPermiso['nombre_red']= $datos[0]->nombre_red;
					if($tipoTramite==3){//cambio titular
						$cp = explode('-',$datos[0]->cp_scp_titular)[0];
						$scp = explode('-',$datos[0]->cp_scp_titular)[1];
						$datosPermiso['domicilio']= $datos[0]->domicilio_titular_c;
						$datosPermiso['cp']= $cp;
						$datosPermiso['scp']= $scp;
						$datosPermiso['ciudad']= $datos[0]->ciudad_titular;
						$datosPermiso['sexo']= $datos[0]->sexo_titular;
						return $datosPermiso;
					}else{
						if($tipoTramite==1){//cambio domicilio
							$datosPermiso['referente']=$datos[0]->referente;
							$datosPermiso['datos_contacto']=$datos[0]->datos_contacto;
						}
						if($tipoTramite==2){
							$datosPermiso['cbu']=$datos[0]->cbu;
						}
						$cp = explode('-',$datos[0]->cp_scp_local)[0];
						$scp = explode('-',$datos[0]->cp_scp_local)[1];
						$datosPermiso['domicilio']= $datos[0]->domicilio_local;
						$datosPermiso['cp']= $cp;
						$datosPermiso['scp']= $scp;
						$datosPermiso['ciudad']= $datos[0]->ciudad_local;
						$datosPermiso['sexo']= $datos[0]->sexo_titular;

						return $datosPermiso;
					}

				}
			}catch(\Exception $e){
				\Log::info('Excepción datos del permiso en suitecrm: '+$e->getMessage());
			}


		}

		/**
		* Devuelve el tipo del trámite cuyo nro_tramite llega por parámetro
		*/
		public static function tipoDelTramite($nro_tramite){
			$tipo_tramite = TramiteModelo::where('nro_tramite','=',$nro_tramite)->get(array('id_tipo_tramite'));

			if (is_null($tipo_tramite) || count($tipo_tramite)==0) {
				return null;
			}else{
				return $tipo_tramite[0]['id_tipo_tramite'];
			}
		}



		/**
		 * Devuelve un tramites a partir del id que le llega
		 * como parámetro
		 * @param type $id
		 * @return \Dominio\Entidades\Habilitacion\Tramite
		 */
		public static function buscarPorId($id){
			$tramite = TramiteModelo::find($id);

			if (is_null($tramite)) {
				return null;
			}else{
				return self::map($tramite);
			}
		}

		/**
		 * Devuelve un tramites a partir del id que le llega
		 * como parámetro
		 * @param type $id
		 * @return \Dominio\Entidades\Habilitacion\Tramite
		 */
		public static function buscarPorIdSinCancelar($id){
			$tramite = TramiteModelo::query()
				->where('nro_tramite','=',$id)
				->where('id_estado_tramite','<>',8)
				->whereBetween('id_estado_tramite', array(0, 2))->first();

			if (count($tramite)==0) {
				return null;
			}else{
				return self::map($tramite);
			}
		}

		public static function buscarTramitesNuevoPermiso($nroPermiso){
			try{
				$ids = TramiteModelo::query()
					->where('nro_permiso','=',$nroPermiso)
					->where('nuevo_permiso','=',1)->get(array('nro_tramite'));

				return $ids;
			}catch(\Exception $e){
				\Log::error('Problema al buscar los ids de los trámites del permiso nuevo: '.$nroPermiso.' '.$e);
				return null;
			}
		}


		/**
		* Devuelve el código postal si ya se completó el cambio de domicilio para un nuevo permiso
		**/
		public static function nroTramiteDomNP($nroPermiso){
			try{
				$nroTramite = TramiteModelo::query()
					->where('nro_permiso','=',$nroPermiso)
					->where('nuevo_permiso','=',1)
					->where('id_tipo_tramite','=',1)
					->where('detalle_completo','=',1)->first();//firstOrFail();
				if(isset($nroTramite))
					return $nroTramite->nro_tramite;
				else
					return null;
			}catch(\Exception $e){
				\Log::error('Problema al buscar el nº de trámite del dom del nuevo permiso: '.$nroPermiso.' '.$e);
				return null;
			}
		}

		/**
		 * Guarda la entidad
		 * @param Entidad $tramite
		 */
		public static function crear(Tramite $tramite) {
			try{
					$modelo = self::unmap($tramite);
					$modelo->save();
					return self::map($modelo);
			}catch(\Exception $e){
					\Log::error('Problema al crear el trámite. '.$e);
				return null;
			}
		}


		/**
		* Modifica el estado del trámite cuyo id llega como parámetro
		**/
		public static function modificarEstadoTramite($idTramite, $idEstadoF, &$datos_mail){
			try{
				$tramite = TramiteModelo::find($idTramite);
				if (is_null($tramite)) {
					return null;
				}else{
					/**datos necesarios para el email**/
					$datos_mail['esNuevoTramite'] = 0;
					$datos_mail['nroTramite']=$idTramite;
					$datos_mail['tipoTramite']=$tramite->tipoTramite['nombre_tramite'];
					$datos_mail['permiso_duenio'] =$tramite['nro_permiso'];
					$tramite->id_estado_tramite = $idEstadoF;

					if($idEstadoF==9){//rechazados o finalizados
						$ruta_historial_pdf = self::pdf_interno($idTramite, $tramite->tipoTramite['abreviatura'], $tramite['nro_permiso']);
						$tramite['ruta_historial_pdf'] = $ruta_historial_pdf;
						$tramite['pendiente_informar']=1;
						$tramite->save();
						if(!$tramite['nuevo_permiso']){
							$usuario=\Session::get('usuarioLogueado')['id'];
							TramiteModelo::storedProcedureCall($usuario);
						}
					}else{
						$tramite->save();
					}

					return "estado cambiado correctamente";
				}
			}catch(\Exception $e){
				\Log::error('Problema al modificar el estado del trámite'.$e);
				return null;
			}

		}

		public static function modificarRutaHistorial($idTramite, $idEstadoF, &$datos_mail){
			try{

				$tramite = TramiteModelo::find($idTramite);
				if (is_null($tramite)) {
					return null;
				}else{
					$ruta_historial_pdf = self::pdf_interno($idTramite, $tramite->tipoTramite['abreviatura'], $tramite['nro_permiso']);
					$tramite['ruta_historial_pdf'] = $ruta_historial_pdf;

					$tramite->save();

					return "ruta cambiada correctamente";
				}
			}catch(\Exception $e){
				\Log::error('Problema al modificar la ruta del historial del trámite'.$e);
				return null;
			}

		}

		/***********************************************************************/
		/* Carga el nº de resolución y de expediente del trámite cuyo id llega */
		/* como parámetro.                                                     */
		/***********************************************************************/
		public static function resolucionExpedienteTramite($idTramite,$idEstadoF,$resolucion, $expediente){
			try{
				\DB::beginTransaction();
				$tramite = TramiteModelo::find($idTramite);
				if (is_null($tramite)) {
					return null;
				}else{
					//$ruta_historial_pdf = public_path().$
					$tramite->id_estado_tramite = $idEstadoF;
					$tramite->resolucion = $resolucion;
					$tramite->expediente = $expediente;
					/*$ruta_historial_pdf = self::pdf_interno($idTramite, $tramite->tipoTramite['abreviatura'], $tramite['nro_permiso']);
					$tramite->ruta_historial_pdf = $ruta_historial_pdf;*/
					$tramite->save();

					\DB::commit();
					//\DB::rollback();
					return "Resolución y expediente cargados correctamente.";
				}
			}catch(\Exception $e){
				\DB::rollback();
				\Log::error('Problema al cargar resolución y expediente del trámite'.$e);
				return null;
			}

		}


		/**
		* Cancela el trámite cuyo id llega como parámetro
		**/
		public static function cancelarTramite($id){
			try{
				$modelo = TramiteModelo::query()->where('nro_tramite','=',$id)
											//->where('id_estado_tramite','=',0)->first();
											->whereBetween('id_estado_tramite', array(0, 2))->first();

				if (count($modelo)==0) {
					return null;
				}else{
					$modelo->id_estado_tramite = 8;
					$modelo->save();
					return $modelo;
				}
			}catch(\Exception $e){
				\Log::error('Problema al cancelar el trámite. ' .$e);
				return null;
			}

		}

		/**
			Verifica si existe un tipo de trámite como el que llega
			por parámetro, para el agente/subagente/permiso, y está
			en estado diferente a rechazado/cancelado/finalizado.
		**/
		public static function verificar_existencia_tramite($agente, $subagente, $permiso, $tipoTramite){
			$estados = array('8', '9', '10', '11');//cancelado (con rve)-rechazado-finalizado-cancelado (sin rve)

			$noExiste = TramiteModelo::where('agente','=', $agente)
				->where('subagente','=', $subagente)
				->where('nro_permiso','=', $permiso)
				->where('id_tipo_tramite','=', $tipoTramite)
				->whereNotIn('id_estado_tramite', $estados)->get();

			if (count($noExiste)==0) { //no existe-->da el ok para realizar el trámite
				return true;
			}else{
				return false;
			}

		}




		/**
		* Función que busca un listado de tramites en función de
		* los filtros recibidos por parámetro.
		**/
		public static function listadoTramites($filtros, &$count) {

	                $query=TramiteModelo::query();

	                //en dónde ingresó
                    $query=$query->leftJoin('hist_tramites_estados AS htee', function($join){
                                        $join->on('htee.nrotramite', '=', 'tramites.nro_tramite');
                                        $join->where('htee.idestado_fin','=',3);
                                    });

	                //vienen de consulta a través de los links y el último estado debe ser = 3
	                if($filtros->ddv_nro==3){//cas_rosario
	                        $query=$query->join('hist_tramites_estados AS ht', function($join)
	                                        {
	                                          $join->on('ht.nrotramite', '=', 'tramites.nro_tramite')
	                                                        ->where('ht.idestado_fin','=',3)
	                                                        ->where('ht.tipo_usuario','=',"CAS_ROS");
	                                        });
	                        $query = $query->where('id_estado_tramite','=','3');
	                }else if($filtros->ddv_nro==4){//cas
	                        $query=$query->join('hist_tramites_estados AS ht', function($join)
	                                        {
	                                          $join->on('ht.nrotramite', '=', 'tramites.nro_tramite')
	                                                        ->where('ht.idestado_fin','=',3)
	                                                        ->where('ht.tipo_usuario','=',"CAS");
	                                        });
	                        $query = $query->where('id_estado_tramite','=','3');
	                }

	                //según seleccionó el combo de "ingreso en"
	                if($filtros->mesaEntrada==1 && $filtros->ddv_nro!=4){
	                        $query=$query->join('hist_tramites_estados AS ht', function($join)
	                                {
	                                    $join->on('ht.nrotramite', '=', 'tramites.nro_tramite')
	                                    ->where('ht.tipo_usuario', '=', 'CAS')
	                                    ->where('ht.idestado_fin', '=', '3');
	                                });
	                }else if($filtros->mesaEntrada==2 && $filtros->ddv_nro!=3){
	                        $query=$query->join('hist_tramites_estados AS ht', function($join)
	                                {
	                                    $join->on('ht.nrotramite', '=', 'tramites.nro_tramite')
	                                    ->where('ht.tipo_usuario', '=', 'CAS_ROS')
	                                    ->where('ht.idestado_fin', '=', '3');
	                                });
	                }


	                if($filtros->ddv_nro!=5 && $filtros->ddv_nro!=6 && $filtros->ddv_nro!=7 && $filtros->ddv_nro!=9){
	                        if($filtros->fechaDesde!=""){
	                                if($filtros->fechaHasta!="" && $filtros->fechaDesde==$filtros->fechaHasta){
	                                        $query = $query->where('tramites.fecha','=' ,$filtros->fechaDesde);
	                                }else if($filtros->fechaHasta!="" && $filtros->fechaDesde<$filtros->fechaHasta){
	                                        $query = $query->whereBetween('tramites.fecha',array($filtros->fechaDesde,$filtros->fechaHasta));
	                                }else if($filtros->fechaHasta==""){
	                                        $query = $query->where('tramites.fecha','>=' ,$filtros->fechaDesde);
	                                }
	                        }else if ($filtros->fechaHasta != "") {//si no define la fechaHasta=> todos hasta el final
	                                $query = $query->where('tramites.fecha','<=' ,$filtros->fechaHasta);
	                        }

	                        if($filtros->ddv_nro==1){ //inciados permisionarios - devueltos docum
	                        	$query=$query->join('basica.usuario_v AS uv', function($join)
	                                        {
	                                          $join->on('uv.id', '=', 'tramites.usuario');
	                                        });
	                        }
	                }else{
	                        if($filtros->ddv_nro==5){//en hab<30
	                                $query=$query->join('hist_tramites_estados AS hte', function($join)
	                                        {
	                                          $join->on('hte.nrotramite', '=', 'tramites.nro_tramite')
	                                                        ->where('hte.idestado_fin','=',4)
	                                                    ->where(\DB::raw('DATEDIFF(NOW(), hte.fecha)'),'<',30);
	                                        });
                            }else if($filtros->ddv_nro==6){//en 30>hab<60
                                    $query=$query->join('hist_tramites_estados AS hte', function($join)
	                                        {
	                                          $join->on('hte.nrotramite', '=', 'tramites.nro_tramite')
	                                                        ->where('hte.idestado_fin','=',4)
	                                                    ->where(\DB::raw('DATEDIFF(NOW(), hte.fecha)'),'>',29)
	                                                    ->where(\DB::raw('DATEDIFF(NOW(), hte.fecha)'),'<',61);
	                                        });

                            }else if($filtros->ddv_nro==7){//en hab>60
                                    $query=$query->join('hist_tramites_estados AS hte', function($join)
                                    {
                                      $join->on('hte.nrotramite', '=', 'tramites.nro_tramite')
                                                    ->where('hte.idestado_fin','=',4)
                                                ->where(\DB::raw('DATEDIFF(NOW(), hte.fecha)'),'>',60);
                                    });

                            }else if($filtros->ddv_nro==9){//aprobados pendientes de envío al suitecrm
                                    $query=$query->where('tramites.pendiente_informar','=',0);
                            }


	                    }

	                if ($filtros->permiso != "") {//si no define el permiso=> todos
	                        $query = $query->where('tramites.nro_permiso','=' ,$filtros->permiso);
	                }
	                if ($filtros->agente != "") {//si no define el agente=> todos
	                        $query = $query->where('tramites.agente','=' ,$filtros->agente);
	                }
	                if ($filtros->subAgente != "") {//si no define el subAgente=> todos
	                        $query = $query->where('tramites.subagente','=' ,$filtros->subAgente);
	                }

					 if ($filtros->localidad != "") {//si no define el agente=> todos
	                        $query = $query->where('tramites.localidad','like' ,'%'.$filtros->localidad.'%');
	                }

	                //estados elegidos
	                if($filtros->estadoTramite!="" && $filtros->ddv_nro!=3 && $filtros->ddv_nro!=4){
	                        $estados=explode(",",$filtros->estadoTramite);
	                        $query=$query->whereIn('tramites.id_estado_tramite', $estados);
	                }

					 //nuevo permiso
                if($filtros->nuevoPermiso==1 ){
	                        $query=$query->where('tramites.nuevo_permiso', 1);
	                }

	                //tipos de tramite
	                if($filtros->tipoTramite!=""){
	                        $tipos=explode(",",$filtros->tipoTramite);
                    	$soloNuevos=sizeof($tipos);

                        	//tramites nuevo permiso
                        	if(in_array("5",$tipos)){
                        		foreach ($tipos as $key => $value) {
                        			if($value==5)
                        				unset($tipos[$key]);
                        		}

                    		if($filtros->nuevoPermiso):
                        			$query =$query->where('tramites.nuevo_permiso','=',1);
                    		else:
                        			$query =$query->whereIn('tramites.nuevo_permiso',array(0,1));
                        			$query = $query->whereIn('tramites.id_tipo_tramite',$tipos);
                    		endif;
                        	}else{
                    		if($filtros->ddv_nro!=9){
                        		$tiposNo=[5];
								if($filtros->nuevoPermiso):
                    				$query =$query->where('tramites.nuevo_permiso','=',1);
                    			else:
                        		$query = $query->whereNotIn('tramites.id_tipo_tramite',$tiposNo);
                        		$query = $query->whereIn('tramites.id_tipo_tramite',$tipos);
								endif;

								if($filtros->estadoTramite != ""):
									$estadosT=explode(",",$filtros->estadoTramite);
									if(count($estadosT)<10):
										//\Log::info(count($filtros->estadoTramite));
										$query=$query->where('tramites.pendiente_informar','=',0);
									endif;
								endif;
	                }else{
								$tiposNo=[5,6,7];
								if($filtros->nuevoPermiso):
                        			$query =$query->where('tramites.nuevo_permiso','=',1);
                        		else:
	                    $query = $query->whereNotIn('tramites.id_tipo_tramite',$tiposNo);
									$query = $query->whereIn('tramites.id_tipo_tramite',$tipos);
								endif;
								$query=$query->where('tramites.pendiente_informar','=',1);
							}
                    	}

                }else{
                    if($filtros->ddv_nro!=9){
						$tiposNo=[5];
						$query = $query->whereNotIn('tramites.id_tipo_tramite',$tiposNo);
						if($filtros->estadoTramite != ""):
							$estadosT=explode(",",$filtros->estadoTramite);
							if(count($estadosT)<10):
								$query=$query->where('tramites.pendiente_informar','=',0);
							endif;
						endif;
                    }else{
						$tiposNo=[5,6,7];
						$query = $query->whereNotIn('tramites.id_tipo_tramite',$tiposNo);
						$query=$query->where('tramites.pendiente_informar','=',1);
                        		}
								}


                $count = $query->count();

                if($filtros->numeroPagina=="-1"){//es el csv
                    $tramites = self::OrdenarSinPaginar($query, $filtros)->groupBy('tramites.nro_tramite')
                                ->get(array(\DB::raw('DISTINCT(tramites.nro_tramite)'),'tramites.id_tipo_tramite',
                                'tramites.nro_permiso','tramites.id_estado_tramite','tramites.usuario',
                                'tramites.agente','tramites.subagente','tramites.fecha','tramites.observaciones',
                                'tramites.razon_social', 'tramites.departamento_nombre', 'tramites.localidad',
                                'tramites.cp_localidad', 'tramites.domicilio', 'tramites.resolucion',
                                'tramites.expediente','htee.idestado_fin','htee.tipo_usuario',
                                \DB::raw('CASE WHEN COALESCE(htee.tipo_usuario, "") = "CAS_ROS" THEN "Rosario" ELSE CASE WHEN COALESCE(htee.tipo_usuario, "") = "" THEN "" ELSE "Santa Fe" END END AS Ingreso_en'),'tramites.nuevo_permiso', 'tramites.detalle_completo'));
                }else{
                    $tramites = self::ordenarYPaginar($query, $filtros)->groupBy('tramites.nro_tramite')
                                ->get(array(\DB::raw('DISTINCT(tramites.nro_tramite)'),'tramites.id_tipo_tramite',
                                'tramites.nro_permiso','tramites.id_estado_tramite','tramites.usuario',
                                'tramites.agente','tramites.subagente','tramites.fecha','tramites.observaciones',
                                'tramites.razon_social', 'tramites.departamento_nombre', 'tramites.localidad',
                                'tramites.cp_localidad', 'tramites.domicilio', 'tramites.resolucion',
                                'tramites.expediente','htee.idestado_fin','htee.tipo_usuario',
                                \DB::raw('CASE WHEN COALESCE(htee.tipo_usuario, "") = "CAS_ROS" THEN "Rosario" ELSE CASE WHEN COALESCE(htee.tipo_usuario, "") = "" THEN "" ELSE "Santa Fe" END END AS Ingreso_en'),'tramites.nuevo_permiso', 'tramites.detalle_completo'));
						}

				if(count($tramites)!=0){
					foreach ($tramites as $tramite) {
							$result[] = self::map($tramite);
	                }

					 return $result;
	                }else{
					return $tramites;
	                }
	                         }





	    /**
		 * Devuelve un Tramite(objeto) a partir de un modelo de Tramite
		 * @param type TipoTramiteModelo
		 * @return \Dominio\Entidades\Habilitacion\Tramite
		 */
		private static function map(TramiteModelo $modelo) {
			$tramite = new Tramite();
			$tramite->nroTramite = $modelo->nro_tramite;
			$tramite->tipoTramite = $modelo->tipoTramite;//$modelo->tipoTramite es la llamada a la funcion q devuelve el objeto tipoTramite
			$tramite->nroPermiso = $modelo->nro_permiso;
			$tramite->estadoTramite = $modelo->estadoTramite;//$modelo->id_estado_tramite;
			$tramite->usuario = $modelo->usuario;
			$tramite->agente = $modelo->agente;
			$tramite->subAgente = $modelo->subagente;
			$tramite->fecha = $modelo->fecha;
			$tramite->observaciones = strtoupper($modelo->observaciones);
			$tramite->razonSocial = strtoupper($modelo->razon_social);
			$tramite->titular = strtoupper($modelo->titular);
			$tramite->localidadAgencia = $modelo->localidad;
			$tramite->departamentoNombre = $modelo->departamento_nombre;
			$tramite->codigoPostalAgencia = $modelo->cp_localidad;
			$tramite->domicilioAgencia = strtoupper($modelo->domicilio);
			$tramite->resolucion = strtoupper($modelo->resolucion);
			$tramite->expediente = strtoupper($modelo->expediente);
			$tramite->rutaHistorialPDF = $modelo->ruta_historial_pdf;
			$tramite->ingreso = $modelo->Ingreso_en;
			$tramite->informado_crm=$modelo->informado_crm;
			$tramite->pendienteInformar=$modelo->pendiente_informar;
			$tramite->nuevo_permiso=$modelo->nuevo_permiso;
			$tramite->detalle_completo=$modelo->detalle_completo;
			$tramite->id_motivo_baja = $modelo->id_motivo_baja;
			$tramite->fechaSuspHab = $modelo->fecha_susp_hab;
			$tramite->tipo_terminal = $modelo->tipo_terminal;
			$tramite->retiro_definitivo = $modelo->retiro_definitivo;

			return $tramite;
		}


		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $tramite
		 * @return \Dominio\Entidades\Habilitacion\Tramite
		 */
		private static function unmap(Tramite $tramite) {
			$modelo = new TramiteModelo();
			$modelo->nro_tramite = $tramite->nroTramite;
			$modelo->id_tipo_tramite = $tramite->tipoTramite['id_tipo_tramite'];//saco del objeto tipo tramite el id
			if(is_null($tramite->nroPermiso)){
				$modelo->nro_permiso = 0;
			}else{
				$modelo->nro_permiso = $tramite->nroPermiso;
			}

			$modelo->id_estado_tramite = $tramite->estadoTramite['id_estado_tramite'];//$tramite->idEstadoTramite;
			$modelo->usuario = $tramite->usuario;
			$modelo->agente = $tramite->agente;
			$modelo->subagente = $tramite->subAgente;
			$modelo->fecha = $tramite->fecha;
			$modelo->observaciones = $tramite->observaciones;
			$modelo->razon_social = $tramite->razonSocial;
			$modelo->titular = $tramite->titular;
			$modelo->localidad = $tramite->localidadAgencia;
			$modelo->departamento_nombre = $tramite->departamentoNombre;
			$modelo->cp_localidad = $tramite->codigoPostalAgencia;
			$modelo->domicilio = $tramite->domicilioAgencia;
			$modelo->resolucion = $tramite->resolucion;
			$modelo->expediente = $tramite->expediente;
			$modelo->ruta_historial_pdf = $tramite->rutaHistorialPDF;
			$modelo->informado_crm = $tramite->informado_crm;
			$modelo->pendiente_informar = $tramite->pendienteInformar;
			$modelo->nuevo_permiso=$tramite->nuevo_permiso;
			$modelo->detalle_completo=$tramite->detalle_completo;
			$modelo->id_motivo_baja=$tramite->id_motivo_baja;

			if(is_null($tramite->fechaSuspHab)){
				$modelo->fecha_susp_hab=0;
			}else{
				$modelo->fecha_susp_hab=$tramite->fechaSuspHab;
			}

			if(is_null($tramite->tipo_terminal)){
				$modelo->tipo_terminal='';
			}else{
				$modelo->tipo_terminal=$tramite->tipo_terminal;
			}
			if(!isset($tramite->retiro_definitivo) || is_null($tramite->retiro_definitivo)){
				$modelo->retiro_definitivo='';
			}else{
				$modelo->retiro_definitivo=$tramite->retiro_definitivo;
			}
			return $modelo;
		}

		/**
		* Función que retorna el modelo de Tramite
		* como un arreglo
		*/
		public static function arregloTramite(Tramite $tramite){
			$modeloTramite = self::unmap($tramite);
			return $modeloTramite->toArray();
		}

		// Busca los detalles de c/u de los recuadros del tablero
		public static function buscarDetalleRecepRosario(){
			$recepcionRos = TramiteModelo::select('tt.nombre_tramite',\DB::raw('CASE tramites.id_estado_tramite WHEN 3 THEN COUNT(DISTINCT tramites.nro_tramite) ELSE 0 END AS cantidad'))
			->join('tipo_tramite as tt', 'tramites.id_tipo_tramite', '=', 'tt.id_tipo_tramite')
			->join('hist_tramites_estados as hte', 'tramites.nro_tramite', '=', 'hte.nrotramite')
			->where('tramites.id_estado_tramite','=',3)
			->whereNotIn('tramites.id_tipo_tramite',[6,7])
			->where('hte.idestado_fin','=',3)
			->where('hte.tipo_usuario','=',"CAS_ROS")
			->groupBy('tt.nombre_tramite')->get();

			return $recepcionRos;
		}
		public static function buscarDetalleRecepStaFe(){
			$recepcionStaFe = TramiteModelo::select('tt.nombre_tramite',\DB::raw('CASE tramites.id_estado_tramite WHEN 3 THEN COUNT(DISTINCT tramites.nro_tramite) ELSE 0 END AS cantidad'))
			->join('tipo_tramite as tt', 'tramites.id_tipo_tramite', '=', 'tt.id_tipo_tramite')
			->join('hist_tramites_estados as hte', 'tramites.nro_tramite', '=', 'hte.nrotramite')
			->where('tramites.id_estado_tramite','=',3)
			->whereNotIn('tramites.id_tipo_tramite',[6,7])
			->where('hte.idestado_fin','=',3)
			->where('hte.tipo_usuario','=',"CAS")
			->groupBy('tt.nombre_tramite')->get();

			return $recepcionStaFe;
		}
		public static function buscarDetalleHab30(){
			$hab30 = TramiteModelo::select('tt.nombre_tramite',\DB::raw('CASE tramites.id_estado_tramite WHEN 4 THEN COUNT(DISTINCT tramites.nro_tramite) ELSE 0 END AS cantidad'))
			->join('tipo_tramite as tt', 'tramites.id_tipo_tramite', '=', 'tt.id_tipo_tramite')
			->join('hist_tramites_estados as hte', 'tramites.nro_tramite', '=', 'hte.nrotramite')
			->where('tramites.id_estado_tramite','=',4)
			->whereNotIn('tramites.id_tipo_tramite',[6,7])
			->where('hte.idestado_fin','=',4)
			->where(\DB::raw('DATEDIFF(NOW(), hte.fecha)'),'<',30)
			->groupBy('tt.nombre_tramite')->get('nombre_tramite','cantidad');

			return $hab30;
		}
		public static function buscarDetalleHab3060(){
			$hab3060 = TramiteModelo::select('tt.nombre_tramite',\DB::raw('CASE tramites.id_estado_tramite WHEN 4 THEN COUNT(DISTINCT tramites.nro_tramite) ELSE 0 END AS cantidad'))
			->join('tipo_tramite as tt', 'tramites.id_tipo_tramite', '=', 'tt.id_tipo_tramite')
			->join('hist_tramites_estados as hte', 'tramites.nro_tramite', '=', 'hte.nrotramite')
			->where('tramites.id_estado_tramite','=',4)
			->whereNotIn('tramites.id_tipo_tramite',[6,7])
			->where('hte.idestado_fin','=',4)
	//		->where(\DB::raw('DATEDIFF(NOW(), hte.fecha)'),'>',30)
	//		->where(\DB::raw('DATEDIFF(NOW(), hte.fecha)'),'<',60)
			->whereBetween(\DB::raw('DATEDIFF(NOW(), hte.fecha)'), array(30, 60))
			->groupBy('tt.nombre_tramite')->get('nombre_tramite','cantidad');
			return $hab3060;
		}
		public static function buscarDetalleHab60(){
			$hab60 = TramiteModelo::select('tt.nombre_tramite',\DB::raw('CASE tramites.id_estado_tramite WHEN 4 THEN COUNT(DISTINCT tramites.nro_tramite) ELSE 0 END AS cantidad'))
			->join('tipo_tramite as tt', 'tramites.id_tipo_tramite', '=', 'tt.id_tipo_tramite')
			->join('hist_tramites_estados as hte', 'tramites.nro_tramite', '=', 'hte.nrotramite')
			->where('tramites.id_estado_tramite','=',4)
			->whereNotIn('tramites.id_tipo_tramite',[6,7])
			->where('hte.idestado_fin','=',4)
			->where(\DB::raw('DATEDIFF(NOW(), hte.fecha)'),'>',60)
			->groupBy('tt.nombre_tramite')->get('nombre_tramite','cantidad');

			return $hab60;
		}
		// Busca los detalles de c/u de los recuadros del tablero
		public static function buscarDetalleFirmaVice(){
			$firmaVice = TramiteModelo::select('tt.nombre_tramite',\DB::raw('CASE tramites.id_estado_tramite WHEN 6 THEN COUNT(DISTINCT tramites.nro_tramite) ELSE 0 END AS cantidad'))
			->join('tipo_tramite as tt', 'tramites.id_tipo_tramite', '=', 'tt.id_tipo_tramite')
			->join('hist_tramites_estados as hte', 'tramites.nro_tramite', '=', 'hte.nrotramite')
			->where('tramites.id_estado_tramite','=',6)
			->where('hte.idestado_fin','=',3)
			->groupBy('tt.nombre_tramite')->get();

			return $firmaVice;
		}
		public static function buscarDetalleDevueltosDoc(){
			$devueltosDoc = TramiteModelo::select('tt.nombre_tramite',\DB::raw('count(DISTINCT tramites.nro_tramite) as cantidad'))
			->join('tipo_tramite as tt', 'tramites.id_tipo_tramite', '=', 'tt.id_tipo_tramite')
			//->join('basica.usuario_v as uv', 'tramites.usuario', '=', 'uv.id')
			->where('tramites.id_estado_tramite','=',5)
			->whereNotIn('tramites.id_tipo_tramite',[6,7])
			->groupBy('tt.nombre_tramite')->get();

			return $devueltosDoc;
		}
		public static function buscarDetalleIniPer(){
			$iniPer = TramiteModelo::select('tt.nombre_tramite',\DB::raw('count(DISTINCT tramites.nro_tramite) as cantidad'))
			->join('tipo_tramite as tt', 'tramites.id_tipo_tramite', '=', 'tt.id_tipo_tramite')
			//->join('basica.usuario_v as uv', 'tramites.usuario', '=', 'uv.id')
			->whereIn('tramites.id_estado_tramite', array(0, 1, 2))
			->whereNotIn('tramites.id_tipo_tramite',[6,7])
			->groupBy('tt.nombre_tramite')->get();

			return $iniPer;
		}

		/**
		* Función que busca los trámites con estado aprobado que todavía no se informaron al suitecrm
		**/
		public static function buscarDetalleAprobPendientes(){
			$aprobPend = TramiteModelo::select('tt.nombre_tramite',\DB::raw('CASE tramites.id_estado_tramite WHEN 7 THEN COUNT(DISTINCT tramites.nro_tramite) ELSE 0 END AS cantidad'))
			->join('tipo_tramite as tt', 'tramites.id_tipo_tramite', '=', 'tt.id_tipo_tramite')
			//->join('hist_tramites_estados as hte', 'tramites.nro_tramite', '=', 'hte.nrotramite')
			->where('tramites.id_estado_tramite','=',7)
			->where('tramites.pendiente_informar','=',0)
			->whereNotIn('tramites.id_tipo_tramite',[5,6,7])
			->groupBy('tt.nombre_tramite')->get();

			return $aprobPend;
		}

		/**
		* Función que guarda el historial del trámite en pdf cuando el trámite
		* es autorizado o rechazado
		**/
		private static function pdf_interno($nro_tramite, $tipo_tramite, $permiso){
			//armado de la url para el reporte

			$historial_pdf = \Config::get('habilitacion_config/jasper_config.servidor_crm')."/";
			$historial_pdf .= \Config::get('habilitacion_config/jasper_config.archivo_reportes')."?";
			$historial_pdf .=\Config::get('habilitacion_config/jasper_config.historial_pdf')."&";
			$historial_pdf .="formato=".\Config::get('habilitacion_config/jasper_config.formatos_reportes.2')."&";
			$historial_pdf .=\Config::get('habilitacion_config/jasper_config.parametros_historial.nombres.0')."=".$nro_tramite;

			$contenido=\file_get_contents($historial_pdf);
			/* Forma 1 con directorio de descarga*/
			//create a file object for the contents to be written to
			$ds = DIRECTORY_SEPARATOR;
			//$directorio_descarga = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$nro_tramite.'_'.$permiso;
			$directorio_descarga = public_path().$ds.'upload'.$ds.$tipo_tramite.'_'.$nro_tramite.'_'.$permiso;

		 	if(!is_dir($directorio_descarga)){
			  \File::makeDirectory($directorio_descarga, 0777, true);
			}

			$fileObject = new \SPLFileObject($directorio_descarga.$ds.'historial_'.$nro_tramite.'.pdf', 'a+'  );

			//write the contents to the file
			$fileObject->fwrite( $contenido );

			//clean up by removing the contents
			unset( $contenido );
			$directorio_descarga = \Config::get('habilitacion_config/config.urlHistorialPDF').$ds.'upload'.$ds.$tipo_tramite.'_'.$nro_tramite.'_'.$permiso;
			return $directorio_descarga.$ds.'historial_'.$nro_tramite.'.pdf';
	}


	/**
	* Función para modificar un campo en particular del trámite
	**/
	public static function modificarCampoNuevo($nroTramite, $campo){
		$tramite = TramiteModelo::where('nro_tramite','=',$nroTramite)->first();

		$tramite->nuevo_permiso=$campo;

		$tramite->save();
	}

	/**
	* Función para modificar un campo en particular del trámite
	**/
	public static function modificarCampoDetalleCompleto($nroTramite, $campo){
		$tramite = TramiteModelo::where('nro_tramite','=',$nroTramite)->first();

		$tramite->detalle_completo=$campo;

		$tramite->save();
	}

	/******************* PERMISO************************/


	/**
	* Función que obtiene el agente bolsa
	**/
	public static function obtenerAgenteBolsa(){
		try{
				$ab = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, NAME as agente, sit_ganancias,tipo_persona, apellido_materno, tbl_localidades_id_c, tbl_ocupaciones_id_c, dni_cuit_titular_c, sexo_c, email_en_portal_c, domicilio_titular_c, nro_ing_brutos_c, titular_doc_tipo_c, titular_doc_nro_c,
									fecha_nacimiento_c, billing_address_city as ciudad, billing_address_state as localidad, billing_address_postalcode as cp_scp, billing_address_country as pais, tbl_nacionalidades_id_c as nacionalidad, cbu
									 FROM age_personas
									WHERE id_persona_as=2890'));
				if(count($ab)==0){
					throw new \Exception("Error Buscando al agente bolsa.", 1);
				}else{
					return $ab[0];
				}

			}catch(\Exception $e){
				\Log::info('Excepción Buscando al agente bolsa: '+$e->getMessage());
				return null;
			}
	}

	/**
	* Función que obtiene la dirección del agente bolsa
	**/
	public static function obtenerDireccionAgenteBolsa(){
		try{
				$dab = \DB::connection('suitecrm_cas')->select(\DB::raw('SELECT l.id, l.`name` AS direccion, l.superficie, l.circulacion, l.tbl_localidades_id_c, l.personas_atencion_c,
					l.vidriera_c, l.ubicacion_local_c,l.ocupacion_local,l.ocupacion_local_no_afines,l.horario_atencion_c,
					l.emplazamiento, l.mayor_transito, l.nivel_socioeconomico, l.billing_address_city AS ciudad,
					l.billing_address_state AS localidad,d.`name` AS departamento, l.billing_address_postalcode AS cp,
					l.billing_address_country AS pais, l.persona_contacto_c,
					l.persona_contacto_datos, l.ocupacion_local_afines, l.jjwg_maps_address_c, SUBSTRING(l.`tbl_localidades_id_c`,LOCATE(l.`tbl_localidades_id_c`,"-")-1,LENGTH(l.`tbl_localidades_id_c`)) AS scp
					FROM age_local l
					INNER JOIN tbl_localidades localidad ON localidad.id=l.tbl_localidades_id_c
					INNER JOIN tbl_departamentos d ON d.`id`=localidad.`tbl_departamentos_id_c`
					WHERE l.`id_local_as`=1819;'));
				if(count($dab)==0){
					throw new \Exception("Error Buscando al agente bolsa.", 1);
				}else{
					return $dab[0];
				}

			}catch(\Exception $e){
				\Log::info('Excepción Buscando domicilio del agente bolsa: '+$e->getMessage());
				return null;
			}
	}

	/**
	* Función que obtiene la red del agente bolsa
	**/
	public static function obtenerRedAgenteBolsa(){
		try{
				$rab = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, NAME as id_red, nombre_red FROM age_red
						WHERE id_red=99999;'));
				if(count($rab)==0){
					throw new \Exception("Problema Buscando la red del agente bolsa.", 1);
				}else{
					return $rab[0];
				}

			}catch(\Exception $e){
				\Log::info('Excepción Buscando al agente bolsa: '+$e->getMessage());
				return null;
			}
	}


	/**
	* Función que controla si existe un trámite de solicitud de baja para un permiso
	**/
	public static function existeSolicitudBaja($permiso){
		$ok=TramiteModelo::where('id_tipo_tramite','=','5')
						->where('nro_permiso','=',$permiso)->get();

		if(count($ok)==0 || is_null($ok)){
			return true;
		}else{
			return false;
		}
	}
	
	public static function existeSolicitudRenuncia($permiso){
		$ok=TramiteModelo::where('id_tipo_tramite','=','13')
						->where('nro_permiso','=',$permiso)->get();

		if(count($ok)==0 || is_null($ok)){
			return true;
		}else{
			return false;
		}
	}


	/**
	* Función que controla si existe un trámite de solicitud de baja para un permiso
	**/
	public static function generarNroPermiso(){
		try{
			$dbe=\DB::connection('suitecrm_cas');
			$nroPermiso=$dbe->table('age_permiso')->select(\DB::raw('MAX(id_permiso)+1 AS nro_permiso'))->where('id_permiso','<>','99999')->get();

			if(count($nroPermiso)==0 || is_null($nroPermiso)){
				throw new \Exception("Problema al generar el nº de permiso - tramite repo.", 1);
			}else{
				return $nroPermiso[0];
			}
		}catch(\Exception $e){
			\Log::info('Excepción al generar el nº de permiso.');
			\Log::error($e->getMessage());
		}
	}


	/**
	* Función donde se generan los accounts
	**/
	public static function generarAccounts($resol, $fecha, $nroPermiso, $nombreUsuario){
		try{
			$dbe=\DB::connection('suitecrm_cas');
			$usuario=\Session::get('usuarioLogueado');
			$datos=$dbe->select(\DB::raw('select UUID() as id_accounts, NOW() as fecha;'));
			$agenteBolsa=self::obtenerAgenteBolsa();
			$direccionAgenteBolsa=self::obtenerDireccionAgenteBolsa();

			$dbe->table('accounts')->insert(
			    ['id'=>$datos[0]->id_accounts,
			    'name' =>'9999/  0 - '.$agenteBolsa->agente,
			    'description'=>'9999/  0 - '.$agenteBolsa->agente,
				'date_entered'=>$datos[0]->fecha,
				'date_modified'=>$datos[0]->fecha,
				'modified_user_id'=>$usuario['id'],
				'created_by'=>$usuario['id'],
				'account_type'=>'Agencia',
				'billing_address_street'=> $direccionAgenteBolsa->direccion,
				'billing_address_city'=>$agenteBolsa->ciudad,
				'billing_address_state'=>$agenteBolsa->localidad,
				'billing_address_postalcode'=>$agenteBolsa->cp_scp,
				'billing_address_country'=>$agenteBolsa->pais]
			);

			$dbe->table('accounts_cstm')->insert(
			    ['id_c'=>$datos[0]->id_accounts,
			    'categoria_c'=>'agencia',
				'dni_cuit_titular_c'=> $agenteBolsa->dni_cuit_titular_c,
				'domicilio_c'=> $direccionAgenteBolsa->direccion,
				'estado_c'=>'Activo',
				'fecha_desde_c'=>$datos[0]->fecha,
				'fecha_habilitacion_c'=>$datos[0]->fecha,
				'id_permiso_c'=>$nroPermiso,
				'jjwg_maps_address_c'=>$direccionAgenteBolsa->jjwg_maps_address_c,
				'numero_agente_c'=>$nroPermiso,
				'numero_subagente_c'=>0,
				'numero_vendedor_c'=>0,
				'titular_c'=>$agenteBolsa->agente,
				'motivo_baja_c'=>0,
				'resolucion_tramite_c'=>$resol,
				'usuario_c'=>$nombreUsuario ]
			);

			return $datos[0]->id_accounts;
		}catch(\Exception $e){
			$dbe->rollback();
			\Log::info($e->getMessage());
			return false;
		}
	}

	/**
	* Función donde se generan los accounts
	**/
	public static function generarPermiso($resol, $fecha, $nroPermiso, $usuario, $idAccounts){
		try{
			$dbe=\DB::connection('suitecrm_cas');
			$usuario=\Session::get('usuarioLogueado');
			$datos=$dbe->select(\DB::raw('select UUID() as id_permiso, NOW() as fecha;'));
			$agenteBolsa=self::obtenerAgenteBolsa();
			$direccionAgenteBolsa=self::obtenerDireccionAgenteBolsa();
			$redAgenteBolsa=self::obtenerRedAgenteBolsa();

			$ok=$dbe->table('age_permiso')->insert(
			    ['id'=>$datos[0]->id_permiso,
				'name' => $nroPermiso,
				'date_entered'=>$datos[0]->fecha,
				'date_modified'=>$datos[0]->fecha,
				'modified_user_id'=>$usuario['id'],
				'created_by'=>$usuario['id'],
				'assigned_user_id'=>$usuario['id'],
				'description'=>'Permiso: permiso Titular: AGENTE BOLSA',
				'resolucion'=>$resol,
				'estado'=>'Nuevo',
				'account_id_c'=>$idAccounts,
				'age_local_id_c'=>$direccionAgenteBolsa->id,
				'age_personas_id_c'=>$agenteBolsa->id,
				'age_red_id_c'=>$redAgenteBolsa->id,
				'fecha_habilitacion'=>$fecha,
				'fecha_alta_titular'=>$datos[0]->fecha,
				'fecha_alta_local'=>$datos[0]->fecha,
				'fecha_alta_red'=>$datos[0]->fecha,
				'estado_comercializacion'=>'nuevo',
				'id_permiso'=>$nroPermiso,
				'punto_venta_tipo'=>'agente',
				'punto_venta_numero'=>0,
				'numero_agente'=>$nroPermiso,
				'razon_social'=>'AGENTE BOLSA']
			);

			return $ok;
		}catch(\Exception $e){
			\Log::info($e->getMessage());
			return false;
		}
	}

	/********* Cálculo del nuevo subagente ***********/
	public static function verificaNroSubagente($nro_red, $nro_subagente, $modalidad){
			$ok = \DB::connection('mysql')->select(\DB::raw('CALL `verifica_subagente`('.$nro_red.','.$nro_subagente.','. $modalidad.')'));
			if($modalidad == 0)
			return $ok[0]->nro_subagente;
                        else
			    return $ok[0]->valido;
	}

	/*********************************/
	/* Consultar Tramite Pendiente	*/
	/*	JG-18/11/2020				*/
	/********************************/
	public static function listadoTramitesPendientes($nroPermiso){
		$query = 'CALL `consulta_tramites_pendientes`('.$nroPermiso.')';
		\Log::info('Tramites Pendientes - datos_session', array($query));
			$ok = \DB::connection('mysql')->select(\DB::raw($query));
			return $ok;
	}
/************************************FIN Consultar Tramites Pendientes************************************/

	/**
	* Función para modificar el campo de observaciones del trámite
	**/
	public static function modificarObservaciones($nroTramite, $campo){
		$tramite = TramiteModelo::where('nro_tramite','=',$nroTramite)->first();

		$tramite->observaciones=$campo;

		return $tramite->save();
}

	/**
	* Función que controla si existe un trámite de solicitud de suspensión para un permiso
	**/
	public static function existeSolicitudSuspension($permiso){
		$ok=TramiteModelo::where('id_tipo_tramite','=','10')
						->where('nro_permiso','=',$permiso)
						->where('id_estado_tramite', '=', '10')
						->where('pendiente_informar', '=', '0')
						->where('informado_crm', '=', '0')
						->get();

		if(count($ok)==0 || is_null($ok)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Función que controla si existe un trámite de solicitud de habilitación para un permiso
	**/
	public static function existeSolicitudHabilitacion($permiso){
		$ok=TramiteModelo::where('id_tipo_tramite','=','9')
						->where('nro_permiso','=',$permiso)
						->where('id_estado_tramite', '=', '10')
						->where('pendiente_informar', '=', '0')
						->where('informado_crm', '=', '0')
						->get();

		if(count($ok)==0 || is_null($ok)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Devuelve el nº de trámite del cambio de categoria para un nuevo permiso
	**/
		public static function nroTramiteCatNP($nroPermiso){
			try{
				$nroTramite = TramiteModelo::query()->where('nro_permiso','=',$nroPermiso)
		            							   ->where('nuevo_permiso','=',1)
		            							   ->where('id_tipo_tramite','=',2)
		            							   ->where('detalle_completo','=',1)->first();//firstOrFail();

		        if(isset($nroTramite))
		                  return $nroTramite->nro_tramite;
		       	else
		       		return null;

			}catch(\Exception $e){
				\Log::error('Problema al buscar el nº de trámite del cambio de cat. del nuevo permiso: '.$nroPermiso.' '.$e);
				return null;
			}
		}


	public static function tablaAntecedentes($nroTramite){
		$ok = \DB::connection('mysql')->select(\DB::raw('CALL `tabla_antecedentes`('.$nroTramite.')'));
		return $ok;
	}


	/**
	* Modifica el estado de los trámites de nuevo permiso
	**/
	public static function modificarEstadoTramitesNuevo($idTramite, $idEstadoF, &$datos_mail, &$listaTramitesDelPermiso){
		try{

			$tramite = TramiteModelo::find($idTramite);

			$listaTramitesDelPermiso = TramiteModelo::where('nro_permiso','=',$tramite['nro_permiso'])
				->where('nuevo_permiso','=',1)
				->get(array('nro_tramite'));

			if (is_null($listaTramitesDelPermiso)) {
				return null;
			}else{
				$nrosTramites = '';
				foreach ($listaTramitesDelPermiso as $ltp) {
					$nrosTramites .= $ltp->nro_tramite;
					$ltp->id_estado_tramite = $idEstadoF;
					$ltp->save();
				}
				/**datos necesarios para el email**/
				$datos_mail['esNuevoTramite'] = 1;
				$datos_mail['nroTramite']=$nrosTramites;
				$datos_mail['tipoTramite']='';

				$datos_mail['permiso_duenio'] =$tramite['nro_permiso'];

				return "estado cambiado correctamente";
			}

		}catch(\Exception $e){
			\Log::error('Problema al modificar el estado del trámite'.$e);
			return null;
		}

	}


	/**
	* Devuelve el nº de trámite de titular para un nuevo permiso
	**/
	public static function nroTramiteTitNP($nroPermiso){
		try{
			$nroTramite = TramiteModelo::query()
				->where('nro_permiso','=',$nroPermiso)
				->where('nuevo_permiso','=',1)
				->where('id_tipo_tramite','=',3)
				->where('detalle_completo','=',1)->first();//firstOrFail();

			return $nroTramite->nro_tramite;
		}catch(\Exception $e){
			\Log::error('Problema al buscar el nº de trámite del titular del nuevo permiso: '.$nroPermiso.' '.$e);
			return null;
		}
	}

	/**
	* Devuelve el nº de trámite de cambio de dependencia para un nuevo permiso
	**/
	public static function nroTramiteDepNP($nroPermiso){
		try{
			$nroTramite = TramiteModelo::query()
				->where('nro_permiso','=',$nroPermiso)
				->where('nuevo_permiso','=',1)
				->where('id_tipo_tramite','=',4)
				->where('detalle_completo','=',1)->first();//firstOrFail();

			return $nroTramite->nro_tramite;
		}catch(\Exception $e){
			\Log::error('Problema al buscar el nº de trámite de cambio dependencia del nuevo permiso: '.$nroPermiso.' '.$e);
			return null;
		}
	}

}

?>
