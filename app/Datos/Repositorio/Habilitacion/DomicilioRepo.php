<?php
	
	namespace Datos\Repositorio\Habilitacion;
	use models\Habilitacion\DomicilioModelo;
	use models\Habilitacion\AgenciaModelo;
	use models\Habilitacion\PlanModelo;
	use models\Habilitacion\EvaluacionModelo;
	use Dominio\Entidades\Habilitacion\Domicilio;
	use Dominio\Entidades\Habilitacion\Plan;
	use Dominio\Entidades\Habilitacion\Evaluacion;
	
	class DomicilioRepo{


		/**
			Función que guarda el domicilio que llega por parámetro
		**/
		public static function cargarDomicilio($domicilio){
				try{
					$modelo = self::unmap($domicilio);
					$modelo->save(); 
					return self::map($modelo);	
					
				}catch(Exception $e){
					Log::error('Problema al crear el domicilio. '.$e);
					return null;
				}		
			
		}
		
		/**
			Función que busca el domicilio según el nº de trámite
		**/
		public static function obtenerNuevoDomicilio($nro_tramite){
			$domicilio = DomicilioModelo::where('nro_tramite','=',$nro_tramite)->first();
			if (isset($domicilio)){
				$domicilio = $domicilio->toArray();
				return $domicilio;
			}else{
				throw new \Exception('Domicilio nuevo vacío');
			}
		}
		
		/**
		* Función que devuelve el código postal del nuevo domicilio
		**/
		public static function cpNuevoPermiso($nroTramite){
			$cp = DomicilioModelo::where('nro_tramite','=',$nroTramite)->get(array('cp_scp_localidad_nueva'));
			$cp_scp =explode("-", $cp[0]['cp_scp_localidad_nueva']);
			return $cp_scp[0];
		}
		
				
				
		/*Modificar el domicilio - Actualización*/
		public static function actualizarDomicilio($nroTramite,$domicilioNuevo,$domicilioViejo){
			try{ 
				$domicilio = DomicilioModelo::where('nro_tramite','=',$nroTramite)->update(array('direccion_nueva' => $domicilioNuevo));
				return true;
			}catch(\Exception $e){
				\Log::info("Problema al actualizar el domicilio.", array($e));
				return false;
			}
		}
		
		/**
		 * Devuelve un Domicilio a partir de un modelo de domicilio
		 * @param type ModeloDomicilio
		 * @return \Dominio\Entidades\Habilitacion\Domicilio
		 */
		private static function map(DomicilioModelo $modelo) { 
			$domicilio = new Domicilio();
			$domicilio->id = $modelo->id;
			$domicilio->nroTramite = $modelo->nro_tramite;
			$domicilio->direccionAnterior = $modelo->direccion_anterior;
			$domicilio->localidadAnterior = $modelo->id_localidad_anterior;        
			$domicilio->direccionNueva = $modelo->direccion_nueva;                
			$domicilio->idLocalidadNueva = $modelo->id_localidad_nueva;
			$domicilio->localidadNueva = $modelo->nombre_localidad_nueva;
			$domicilio->cpScpLocalidadNueva = $modelo->cp_scp_localidad_nueva;
			$domicilio->idDepartamentoNuevo = $modelo->id_departamento_nuevo;
			$domicilio->departamentoNuevo = $modelo->nombre_departamento_nuevo;       
			$domicilio->referente = $modelo->referente;
			$domicilio->datos_contacto = $modelo->datos_contacto;
			
			
			return $domicilio;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $domicilio
		 * @return \Dominio\Entidades\Habilitacion\Domicilio
		 */
		private static function unmap(Domicilio $domicilio) {
			$modelo = new DomicilioModelo();
			$modelo->id = $domicilio->id;
			$modelo->nro_tramite = $domicilio->nroTramite;
			$modelo->direccion_anterior = $domicilio->direccionAnterior;
			$modelo->id_localidad_anterior = $domicilio->localidadAnterior;        
			$modelo->direccion_nueva = $domicilio->direccionNueva;                
			$modelo->id_localidad_nueva = $domicilio->idLocalidadNueva; 
			$modelo->nombre_localidad_nueva = $domicilio->localidadNueva;
			$modelo->cp_scp_localidad_nueva = $domicilio->cpScpLocalidadNueva;
			$modelo->id_departamento_nuevo = $domicilio->idDepartamentoNuevo;
			$modelo->nombre_departamento_nuevo = $domicilio->departamentoNuevo;
			$modelo->referente = $domicilio->referente;
			$modelo->datos_contacto = $domicilio->datos_contacto;
			return $modelo;
		}
		

		/**
		* Función que retorna el modelo de Domicilio
		* como un arreglo
		*/
		public static function arregloDomicilio(Domicilio $domicilio){
			$modeloDomicilio = self::unmap($domicilio);
			return $modeloDomicilio->toArray();
		}
		

/*****************************Métodos para el plan***********************************/
		/**Busca los datos actuales de la agencia para cargar en el plan**/
		public static function obtenerDatosAgencia($permiso){ 
			$datosAgencia = AgenciaModelo::where('permiso','=',$permiso)->first();
			
			if (is_null($datosAgencia)||count($datosAgencia)==0) {
				return null;
			}else{		
				$datosAgencia= $datosAgencia->toArray();
				return $datosAgencia;	
			}			
		}



		/**Busca el plan según el nro de trámite al que pertenece**/
		public static function obtenerPlan($nro_tramite){
			$plan = PlanModelo::where('nro_tramite','=',$nro_tramite)->first();
			
			if (is_null($plan)) {
				return null;
			}else{

				$ds = DIRECTORY_SEPARATOR;
				$plan= $plan->toArray();
				$nombreFotoF = substr($plan['foto_f'],strripos($plan['foto_f'],$ds)+1);
				$nombreFotoI = substr($plan['foto_i'],strripos($plan['foto_i'],$ds)+1);
				$permiso = explode('_',explode('.', $nombreFotoF)[0])[0];

				$plan['foto_vf']="upload".$ds."CD_".$nro_tramite.'_'.$permiso.$ds.$nombreFotoF;
				$plan['foto_vi']="upload".$ds."CD_".$nro_tramite.'_'.$permiso.$ds.$nombreFotoI;
				$plan['preg_1']=$plan['zona_1'];
				$plan['preg_2']=$plan['zona_2'];
				
				/*tabla*/
				$plan['mes_i']=$plan['mes_inicio'];
				$plan['anioi_t1']=$plan['trimestre_1_anioi'];
				$plan['aniof_t1']=$plan['trimestre_1_aniof'];
				$plan['meses_t1']=$plan['trimestre_1'];
				$plan['venta_1']=$plan['trimestre_1_ventas'];
				$plan['anioi_t2']=$plan['trimestre_2_anioi'];
				$plan['aniof_t2']=$plan['trimestre_2_aniof'];
				$plan['meses_t2']=$plan['trimestre_2'];
				$plan['venta_2']=$plan['trimestre_2_ventas'];
				$plan['anioi_t3']=$plan['trimestre_3_anioi'];
				$plan['aniof_t3']=$plan['trimestre_3_aniof'];
				$plan['meses_t3']=$plan['trimestre_3'];
				$plan['venta_3']=$plan['trimestre_3_ventas'];
				$plan['anioi_t4']=$plan['trimestre_4_anioi'];
				$plan['aniof_t4']=$plan['trimestre_4_aniof'];
				$plan['meses_t4']=$plan['trimestre_4'];
				$plan['venta_4']=$plan['trimestre_4_ventas'];

				return $plan;
			} 
		}

		/**Guardar plan modificado**/
		public static function modificaPlan($plan,$id_plan){
			if(is_null($id_plan)){//para los trámites que no lo tienen (antes de actualizar la app)
				//INSERT INTO plan_estrategico (id) VALUES (NULL);
				$modelo= self::unmapPlan($plan);
				$modelo->save();
				
				return true;
			}

			$modelo = PlanModelo::find($id_plan);

			if (is_null($plan)) {
				return false;
			}else{
				self::unmapPlanModificado($modelo,$plan);
				
				$modelo->save();
				
				return true;
			}

		}

		/*****************************************/
		/*	Guarda el plan en la base de datos   */
		/*****************************************/
		public static function cargarPlan($plan){
				try{

					$modelo = self::unmapPlan($plan);
					$modelo->save(); 
					return self::mapPlan($modelo);	
					
				}catch(Exception $e){
					Log::error('Problema al crear el plan. '.$e);
					return null;
				}		
			
		}
		
				
		/**
		 * Devuelve un Domicilio a partir de un modelo de plan
		 * @param type ModeloPlan
		 * @return \Dominio\Entidades\Habilitacion\Plan
		 */
		private static function mapPlan(PlanModelo $modelo) { 
			$plan = new Plan();
			$plan->id = $modelo->id;
			$plan->nro_tramite = $modelo->nro_tramite;
			$plan->superficie_codigo = $modelo->superficie_codigo;
			$plan->superficie_valor = $modelo->superficie_valor;        
			$plan->ubicacion_codigo = $modelo->ubicacion_codigo;                
			$plan->ubicacion_valor = $modelo->ubicacion_valor;
			$plan->vidriera_codigo = $modelo->vidriera_codigo;
			$plan->vidriera_valor = $modelo->vidriera_valor;
			$plan->persona_contacto = $modelo->persona_contacto;
			$plan->telefono_contacto = $modelo->telefono_contacto;
			$plan->cant_empleados = $modelo->cant_empleados;       
			$plan->horario_codigo = $modelo->horario_codigo;
			$plan->horario_valor = $modelo->horario_valor;
			$plan->rubros_agencia = $modelo->rubros_agencia;
			$plan->rubros_amigos = $modelo->rubros_amigos;
			$plan->rubros_otros = $modelo->rubros_otros;
			$plan->foto_f = $modelo->foto_f;
			$plan->foto_i = $modelo->foto_i;
			$plan->zona_1 = $modelo->zona_1;
			$plan->zona_2 = $modelo->zona_2;
			$plan->car_zona = $modelo->car_zona;
			$plan->nivel_circulacion_codigo = $modelo->nivel_circulacion_codigo;
			$plan->nivel_circulacion_valor = $modelo->nivel_circulacion_valor;
			$plan->nivel_socioeconomico_codigo = $modelo->nivel_socioeconomico_codigo;
			$plan->nivel_socioeconomico_valor = $modelo->nivel_socioeconomico_valor;
			$plan->mes_inicio = $modelo->mes_inicio;
			$plan->trimestre_1 = $modelo->trimestre_1;
			$plan->trimestre_1_anioi = $modelo->trimestre_1_anioi;
			$plan->trimestre_1_aniof = $modelo->trimestre_1_aniof;
			$plan->trimestre_1_ventas = $modelo->trimestre_1_ventas;
			$plan->trimestre_2 = $modelo->trimestre_2;
			$plan->trimestre_2_anioi = $modelo->trimestre_2_anioi;
			$plan->trimestre_2_aniof = $modelo->trimestre_2_aniof;
			$plan->trimestre_2_ventas = $modelo->trimestre_2_ventas;
			$plan->trimestre_3 = $modelo->trimestre_3;
			$plan->trimestre_3_anioi = $modelo->trimestre_3_anioi;
			$plan->trimestre_3_aniof = $modelo->trimestre_3_aniof;
			$plan->trimestre_3_ventas = $modelo->trimestre_3_ventas;
			$plan->trimestre_4 = $modelo->trimestre_4;
			$plan->trimestre_4_anioi = $modelo->trimestre_4_anioi;
			$plan->trimestre_4_aniof = $modelo->trimestre_4_aniof;
			$plan->trimestre_4_ventas = $modelo->trimestre_4_ventas;
		
			
			return $plan;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $domicilio
		 * @return \Dominio\Entidades\Habilitacion\Plan
		 */
		private static function unmapPlan(Plan $plan) {
			$modelo = new PlanModelo();
			$modelo->id;
			$modelo->nro_tramite = $plan->nro_tramite;
			$modelo->superficie_codigo = $plan->superficie_codigo;
			$modelo->superficie_valor = $plan->superficie_valor;        
			$modelo->ubicacion_codigo = $plan->ubicacion_codigo;                
			$modelo->ubicacion_valor = $plan->ubicacion_valor;
			$modelo->vidriera_codigo = $plan->vidriera_codigo;
			$modelo->vidriera_valor = $plan->vidriera_valor;
			$modelo->persona_contacto = $plan->persona_contacto;
			$modelo->telefono_contacto = $plan->telefono_contacto;
			$modelo->cant_empleados = $plan->cant_empleados;       
			$modelo->horario_codigo = $plan->horario_codigo;
			$modelo->horario_valor = $plan->horario_valor;
			$modelo->rubros_agencia = $plan->rubros_agencia;
			$modelo->rubros_amigos = $plan->rubros_amigos;
			$modelo->rubros_otros = $plan->rubros_otros;
			$modelo->foto_f = $plan->foto_f;
			$modelo->foto_i = $plan->foto_i;
			$modelo->zona_1 = $plan->zona_1;
			$modelo->zona_2 = $plan->zona_2;
			$modelo->car_zona = $plan->car_zona;
			$modelo->nivel_circulacion_codigo = $plan->nivel_circulacion_codigo;
			$modelo->nivel_circulacion_valor = $plan->nivel_circulacion_valor;
			$modelo->nivel_socioeconomico_codigo = $plan->nivel_socioeconomico_codigo;
			$modelo->nivel_socioeconomico_valor = $plan->nivel_socioeconomico_valor;
			$modelo->mes_inicio=$plan->mes_inicio;
			$modelo->trimestre_1 = $plan->trimestre_1;
			$modelo->trimestre_1_anioi = $plan->trimestre_1_anioi;
			$modelo->trimestre_1_aniof = $plan->trimestre_1_aniof;
			$modelo->trimestre_1_ventas = $plan->trimestre_1_ventas;
			$modelo->trimestre_2 = $plan->trimestre_2;
			$modelo->trimestre_2_anioi = $plan->trimestre_2_anioi;
			$modelo->trimestre_2_aniof = $plan->trimestre_2_aniof;
			$modelo->trimestre_2_ventas = $plan->trimestre_2_ventas;
			$modelo->trimestre_3 = $plan->trimestre_3;
			$modelo->trimestre_3_anioi = $plan->trimestre_3_anioi;
			$modelo->trimestre_3_aniof = $plan->trimestre_3_aniof;
			$modelo->trimestre_3_ventas = $plan->trimestre_3_ventas;
			$modelo->trimestre_4 = $plan->trimestre_4;
			$modelo->trimestre_4_anioi = $plan->trimestre_4_anioi;
			$modelo->trimestre_4_aniof = $plan->trimestre_4_aniof;
			$modelo->trimestre_4_ventas = $plan->trimestre_4_ventas;
			return $modelo;
		}

		/**Guarda las modificaciones del objeto plan en el modelo plan**/
		public static function unmapPlanModificado(PlanModelo $modelo, Plan $plan){
			$modelo->superficie_codigo = $plan->superficie_codigo;
			$modelo->superficie_valor = $plan->superficie_valor;        
			$modelo->ubicacion_codigo = $plan->ubicacion_codigo;                
			$modelo->ubicacion_valor = $plan->ubicacion_valor;
			$modelo->vidriera_codigo = $plan->vidriera_codigo;
			$modelo->vidriera_valor = $plan->vidriera_valor;
			$modelo->persona_contacto = $plan->persona_contacto;
			$modelo->telefono_contacto = $plan->telefono_contacto;
			$modelo->cant_empleados = $plan->cant_empleados;       
			$modelo->horario_codigo = $plan->horario_codigo;
			$modelo->horario_valor = $plan->horario_valor;
			$modelo->rubros_agencia = $plan->rubros_agencia;
			$modelo->rubros_amigos = $plan->rubros_amigos;
			$modelo->rubros_otros = $plan->rubros_otros;
			
			if(!is_null($plan->foto_f)){
				$modelo->foto_f = $plan->foto_f;
			}		

			if(!is_null($plan->foto_i)){
				$modelo->foto_i = $plan->foto_i;
			}					

			$modelo->zona_1 = $plan->zona_1;
			$modelo->zona_2 = $plan->zona_2;
			$modelo->car_zona = $plan->car_zona;
			$modelo->nivel_circulacion_codigo = $plan->nivel_circulacion_codigo;
			$modelo->nivel_circulacion_valor = $plan->nivel_circulacion_valor;
			$modelo->nivel_socioeconomico_codigo = $plan->nivel_socioeconomico_codigo;
			$modelo->nivel_socioeconomico_valor = $plan->nivel_socioeconomico_valor;
			$modelo->mes_inicio=$plan->mes_inicio;
			$modelo->trimestre_1 = $plan->trimestre_1;
			$modelo->trimestre_1_anioi = $plan->trimestre_1_anioi;
			$modelo->trimestre_1_aniof = $plan->trimestre_1_aniof;
			$modelo->trimestre_1_ventas = $plan->trimestre_1_ventas;
			$modelo->trimestre_2 = $plan->trimestre_2;
			$modelo->trimestre_2_anioi = $plan->trimestre_2_anioi;
			$modelo->trimestre_2_aniof = $plan->trimestre_2_aniof;
			$modelo->trimestre_2_ventas = $plan->trimestre_2_ventas;
			$modelo->trimestre_3 = $plan->trimestre_3;
			$modelo->trimestre_3_anioi = $plan->trimestre_3_anioi;
			$modelo->trimestre_3_aniof = $plan->trimestre_3_aniof;
			$modelo->trimestre_3_ventas = $plan->trimestre_3_ventas;
			$modelo->trimestre_4 = $plan->trimestre_4;
			$modelo->trimestre_4_anioi = $plan->trimestre_4_anioi;
			$modelo->trimestre_4_aniof = $plan->trimestre_4_aniof;
			$modelo->trimestre_4_ventas = $plan->trimestre_4_ventas;
		}


		/**
		* Modificar campos del trámite de cambio de domicilio
		*/
		public static function modificarCampos($nroTramite, $campos){
			$dom = DomicilioModelo::where('nro_tramite','=',$nroTramite)->first();

			foreach ($campos as $key => $value) {
				$dom->$key=$value;
	}

			
			return $dom->save();
		}


		/**
		* Verifico si se completaron todos los campos del trámite
		**/
		public static function CompletoTodosLosCamposDom($nroTramite){
			$dom = DomicilioModelo::where('nro_tramite','=',$nroTramite)->first();

			if(empty($dom->direccion_nueva)){
				\Log::info("control dom: direccion_nueva");
				return false;
			}                
			if(empty($dom->referente)){
				\Log::info("control dom: referente");
				return false;
			}                
			if(empty($dom->datos_contacto)){
				\Log::info("control dom: datos_contacto");
				return false;
			}   

			return true;             
		}

		public static function CompletoTodosLosCamposPlan($nroTramite){
			$plan = PlanModelo::where('nro_tramite','=',$nroTramite)->first();

			if(is_null($plan)){
				\Log::info("control plan: no cargó plan");
				return false;
			}

			      
			if(empty($plan->persona_contacto)){
				\Log::info("control plan: persona_contacto");
				return false;
			}                 
			if(empty($plan->telefono_contacto)){
				\Log::info("control plan: telefono_contacto");
				return false;
			}                
			if(empty($plan->cant_empleados)){
				\Log::info("control plan: cant_empleados");
				return false;
			}                 
			           
			if(empty($plan->rubros_agencia)){
				\Log::info("control plan: rubros_agencia");
				return false;
			}                 
			            
			if(empty($plan->foto_f)){
				\Log::info("control plan: foto_f");
				return false;
			}                 
			if(empty($plan->foto_i)){
				\Log::info("control plan: foto_i");
				return false;
			}               
			if(empty($plan->car_zona)){
				\Log::info("control plan: car_zona");
				return false;
			}                 
			              
			if(empty($plan->mes_inicio)){
				\Log::info("control plan: mes_inicio");
				return false;
			}                
			
			if(empty($plan->trimestre_1_ventas)){
				\Log::info("control plan: trimestre_1_ventas");
				return false;
			}                
			
			if(empty($plan->trimestre_2_ventas)){
				\Log::info("control plan: trimestre_2_ventas");
				return false;
			}                 
			 
			if(empty($plan->trimestre_3_ventas)){
				\Log::info("control plan: trimestre_3_ventas");
				return false;
			}                 

			if(empty($plan->trimestre_4_ventas)){
				\Log::info("control plan: trimestre_4_ventas");
				return false;
			}

			return true;
		}

		/**
		* Función que guarda la evaluación del domicilio
		**/
		public static function guardarEvaluacionDomicilio($eval){

			$evalModelo = self::unmapEval($eval);
						
			return $evalModelo->save();
		}

		/**
		* Función que obtiene la evaluación del domicilio
		**/
		public static function obtenerEvaluacionDomicilio($nroTramite){
						\Log::info($nroTramite);
			$eval = EvaluacionModelo::firstOrCreate(array('nro_tramite'=>$nroTramite));
			
			return self::mapEvaluacion($eval);
			
		}


		/**
		 * Devuelve un modelo a partir de una entidad para guardar en la base de datos
		 *( del objeto a la base)
		 * @param type $eval
		 * @return \Dominio\Entidades\Habilitacion\Evaluacion
		 */
		private static function unmapEval(Evaluacion $eval) {
			//$modelo 			= new EvaluacionModelo();
			$modelo 			= EvaluacionModelo::firstOrCreate(array('nro_tramite'=>$eval->nro_tramite));

			/*if($modelo->estado_eval == 0):
				$modelo->estado_eval = 2; //En tratamiento
			endif;*/

			//datosExtra_local                                         
			$datosLocal 		= array("ubicacion"=>array("ubicacion_codigo"=>$eval->ubicacion_codigo, "ubicacion_valor"=>$eval->ubicacion_valor),
										"superficie"=>array("superficie_codigo"=>$eval->superficie_codigo, "superficie_valor"=>$eval->superficie_valor),
										"vidriera"=>array("vidriera_codigo"=>$eval->vidriera_codigo, "vidriera_valor"=>$eval->vidriera_valor),
										"porcentaje_agencia"=>array("rubro_agencia"=>$eval->rubros_agencia, "rubro_amigos"=>$eval->rubros_amigos, "rubro_otros"=>$eval->rubros_otros));

			$modelo->datosExtra_local = json_encode($datosLocal);

			//datosExtra_entorno
			$datosEntorno 		= array("plano"=>array("url_plano"=>$eval->plano), 
										"localizacion"=>array("caracteristicas"=>$eval->caracteristicas), 
										"nivel"=>array("socioeconomico_codigo"=>$eval->socioeconomico_codigo, "socioeconomico_valor"=>$eval->socioeconomico_valor),
										"centros_afinidad"=>json_decode($eval->centroafinidad));

			$modelo->datosExtra_entorno = json_encode($datosEntorno);		

			//datosExtra_competencia
			$datosCompetencia 	= array("competidores"=>array("cuadra"=>$eval->competidor_cuadra, "antessiguiente"=>$eval->competidor_antsig, "transversal"=>$eval->competidor_transv));
			$modelo->datosExtra_competencia = json_encode($datosCompetencia); 			 

			//datos generales
			$modelo->observ_local        =  ($eval->observacion_local);
			// $modelo->observ_cuantitativo =  ($eval->observacion_cuantitativo);
			$modelo->observ_cuantitativo =  ("");
			$modelo->observ_entorno      =  ($eval->observacion_entorno);
			$modelo->observ_competencia  =  ($eval->observacion_competencia);

			$modelo->estado_eval         =  $eval->estado;
			$modelo->estado_local        =  $eval->estado_local;
			// $modelo->estado_cuantitativo =  $eval->estado_cuantitativo;
			$modelo->estado_cuantitativo =  "";
			$modelo->estado_entorno      =  $eval->estado_entorno;
			$modelo->estado_competencia  =  $eval->estado_competencia;

			return $modelo;
		}

		/**
		 * Devuelve un objeto Evaluación a partir de un modelo de evaluación (de la base de datos)
		 * @param type ModeloEvaluacion
		 * @return \Dominio\Entidades\Habilitacion\Evaluacion
		 */
		private static function mapEvaluacion(EvaluacionModelo $modelo) { 
			$eval = new Evaluacion();
			$eval->nro_tramite = $modelo->nro_tramite;

			if($modelo->datosExtra_local != null):

				$datosLocal = json_decode($modelo->datosExtra_local);
				$eval->ubicacion_codigo 	= $datosLocal->ubicacion->ubicacion_codigo;
				$eval->ubicacion_valor 		= $datosLocal->ubicacion->ubicacion_valor;
				$eval->superficie_codigo 	= $datosLocal->superficie->superficie_codigo;
				$eval->superficie_valor 	= $datosLocal->superficie->superficie_valor;
				$eval->vidriera_codigo 		= $datosLocal->vidriera->vidriera_codigo;
				$eval->vidriera_valor 		= $datosLocal->vidriera->vidriera_valor;
				$eval->rubros_agencia		= $datosLocal->porcentaje_agencia->rubro_agencia;
				$eval->rubros_amigos 		= $datosLocal->porcentaje_agencia->rubro_amigos;
				$eval->rubros_otros 		= $datosLocal->porcentaje_agencia->rubro_otros;
			else:
				$eval->ubicacion_codigo 	= "ni";
				$eval->ubicacion_valor 		= "";
				$eval->superficie_codigo 	= "ni";
				$eval->superficie_valor 	= "";
				$eval->vidriera_codigo 		= "ni";
				$eval->vidriera_valor 		= "";
				$eval->rubros_agencia		= 100;
				$eval->rubros_amigos 		= 0;
				$eval->rubros_otros 		= 0;
			endif;

			if($modelo->datosExtra_entorno != null):
				$datosEntorno = json_decode($modelo->datosExtra_entorno);	//si lo quiero como erreglo le pongo ,true	

				if($datosEntorno->plano!=""):
					$ds = DIRECTORY_SEPARATOR;
					$nombrePlano = str_replace('JPG','jpg',substr($datosEntorno->plano->url_plano,strripos($datosEntorno->plano->url_plano,$ds)+1));
					$nombrePlano = str_replace('PNG','png',$nombrePlano);
					$permiso = explode('_',explode('.', $nombrePlano)[0])[0];
					$eval->plano="upload".$ds."CD_".$eval->nro_tramite.'_'.$permiso.$ds.$nombrePlano;
				endif;
				$eval->caracteristicas 			= $datosEntorno->localizacion->caracteristicas;
				$eval->socioeconomico_codigo 	= $datosEntorno->nivel->socioeconomico_codigo;
				$eval->socioeconomico_valor 	= $datosEntorno->nivel->socioeconomico_valor;
				$eval->centroafinidad 			= json_decode(json_encode($datosEntorno->centros_afinidad, true), true);
			else:
				$datosEntorno = json_decode($modelo->datosExtra_entorno, true);		
				$eval->plano 					= "images/no-imagen.jpg";
				$eval->caracteristicas 			= "";
				$eval->socioeconomico_codigo 	= "ni";
				$eval->socioeconomico_valor 	= "";
				$eval->centroafinidad 			= "";
			endif;

			if($modelo->datosExtra_competencia != null):
				//datosExtra_competencia
				$datosCompetencia= json_decode($modelo->datosExtra_competencia); 	
				$eval->competidor_cuadra = $datosCompetencia->competidores->cuadra;
				$eval->competidor_antsig = $datosCompetencia->competidores->antessiguiente;
				$eval->competidor_transv = $datosCompetencia->competidores->transversal;
			else:
				$eval->competidor_cuadra = 0;
				$eval->competidor_antsig = 0;
				$eval->competidor_transv = 0;

			endif;

			//datos generales
			$eval->observacion_local 		= $modelo->observ_local;
			$eval->observacion_cuantitativo = $modelo->observ_cuantitativo;
			$eval->observacion_entorno		= $modelo->observ_entorno;
			$eval->observacion_competencia 	= $modelo->observ_competencia;
			
			if($modelo->estado_eval == null)
				$modelo->estado_eval = 2;
			$eval->estado 				= $modelo->estado_eval;
			$eval->estado_local 		= $modelo->estado_local;
			$eval->estado_cuantitativo 	= $modelo->estado_cuantitativo;
			$eval->estado_entorno 		= $modelo->estado_entorno;
			$eval->estado_competencia 	= $modelo->estado_competencia;
		
			
			return $eval;
		}


		/**
		* Verifico si se completaron todos los campos del trámite
		**/
		public static function TerminoEvaluacion($nroTramite){
			$eval = EvaluacionModelo::firstOrCreate(array('nro_tramite'=>$nroTramite));
//\Log::info("TerminoEvaluacion (domicilioRepo): ", array($eval->estado_eval));
			if(in_array($eval->estado_eval, array(2))): //si está en tratamiento (2) debe colocar satisfactorio o no satisfactorio
				return false;
			else:
				if($eval->datosExtra_local == null):
					return false;
				endif;
				if($eval->datosExtra_entorno == null):
					return false;
				endif;
				if($eval->datosExtra_competencia == null):
					return false;
				endif;

				return true; 
				            
			endif;   
		}

	}
?>
