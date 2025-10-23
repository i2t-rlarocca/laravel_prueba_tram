<?php

	namespace Datos\Repositorio\Habilitacion;


	class SuiteCRMRepo extends BaseRepo{
		public static function listaSuperficie(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="superficie_local_1" and deleted=0'));
				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de superficie.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaSuperficie[$tipo->codigo]=$tipo->valor;
					}

					return $listaSuperficie;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaSuperficie: '+$e->getMessage());
			}
		}

		public static function listaUbicacion(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="ubicacion_local_c_list" and deleted=0'));

				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de ubicación.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaUbicacion[$tipo->codigo]=$tipo->valor;
					}

					return $listaUbicacion;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaUbicacion: '+$e->getMessage());
			}
		}


		public static function listaVidriera(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="vidriera_list" and deleted=0'));
				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de vidriera.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaVidriera[$tipo->codigo]=$tipo->valor;
					}

					return $listaVidriera;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaVidriera: '+$e->getMessage());
			}


		}

		public static function listaCaracteristicasZona(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="emplazamiento" and deleted=0 AND codigo<>"ni"'));
				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de caracteristcas de la zona.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaCaracteristicasZona[$tipo->codigo]=$tipo->valor;
					}

					return $listaCaracteristicasZona;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaCaracteristicasZona: '+$e->getMessage());
			}
		}


		public static function listaSocioEconomico(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="nivel_socioeconomico" and deleted=0'));
				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de nivel socioeconomico.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaSocioEconomico[$tipo->codigo]=$tipo->valor;
					}

					return $listaSocioEconomico;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaSocioEconomico: '+$e->getMessage());
				}
		}

		/**
		* Lista de ocupaciones del local (% ocupado para la agencia)
		**/
		public static function listaOcupacion(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="ocupacion_local_list" and deleted=0'));
				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de ocupación.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaOcupacion[$tipo->codigo]=$tipo->valor;
					}

					return $listaOcupacion;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaOcupacion: '+$e->getMessage());
			}
		}

		public static function listaCirculacion(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor FROM tbl_listas_desplegables WHERE NAME="circulacion_list" AND deleted=0'));
				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de circulación.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaCirculacion[$tipo->codigo]=$tipo->valor;
					}

					return $listaCirculacion;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaCirculacion: '+$e->getMessage());
			}
		}

		public static function listaHorario(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor FROM tbl_listas_desplegables WHERE NAME="horario_atencion_c" AND deleted=0'));
				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de ocupación.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaHorario[$tipo->codigo]=$tipo->valor;
					}

					return $listaHorario;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaHorario: '+$e->getMessage());
			}

		}


		public static function listaOcupacionPersonas(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('SELECT id, NAME AS valor FROM tbl_ocupaciones WHERE NAME NOT LIKE "A DETALLAR%" AND NAME NOT LIKE "%SIN DATOS%" AND deleted=0 ORDER BY NAME'));

				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de ocupaciones para las personas.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaOcupacionPersonas[$tipo->id]=$tipo->valor;
					}

					return $listaOcupacionPersonas;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaOcupacionPersonas: '+$e->getMessage());
			}
		}

		/**
		 * Función que devuelve todos los tipos de personas
		 * activos para ser seleccionados
		 */
		public static function tiposDePersonas(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="tipo_persona_list" and deleted=0'));
				if(count($tipos)==0){
					return $null;
				}else{

					foreach ($tipos as $tipo) {
						$lista_tipos[$tipo->codigo]=$tipo->valor;
					}

					return $lista_tipos;
				}
			}catch(\Exception $e){
				\Log::info('Excepción tiposDePersonas: '+$e->getMessage());
				return null;
			}

		}

		/**
		 * Función que devuelve todos los tipos de documentos
		 * activos para ser seleccionados
		 */
		public static function tiposDeDocumentos(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="tipo_documento_list" and deleted=0 AND codigo<>0'));
				if(count($tipos)==0){
					return null;
				}else{

					foreach ($tipos as $tipo) {
						$lista_tipos[$tipo->codigo]=$tipo->valor;
					}

					return $lista_tipos;
				}
			}catch(\Exception $e){
				\Log::info('Excepción tiposDePersonas: '+$e->getMessage());
				return null;
			}

		}
		
		/**
		 * Función que devuelve todos los tipos de relaciones
		 * activos para ser seleccionados
		 */
		public static function TiposRelacion(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="tipo_relacion_list" and deleted=0'));
				if(count($tipos)==0){
					return null;
				}else{

					foreach ($tipos as $tipo) {
						$lista_tipos[$tipo->codigo]=$tipo->valor;
					}

					return $lista_tipos;
				}
			}catch(\Exception $e){
				\Log::info('Excepción tiposDePersonas: '+$e->getMessage());
				return null;
			}

		}
		
		/**
		 * Función que devuelve todos los tipos de vinculos si es familiar
		 * activos para ser seleccionados
		 */
		public static function TiposVinculo(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="tipo_vinculo_list" and deleted=0'));
				if(count($tipos)==0){
					return null;
				}else{

					foreach ($tipos as $tipo) {
						$lista_tipos[$tipo->codigo]=$tipo->valor;
					}

					return $lista_tipos;
				}
			}catch(\Exception $e){
				\Log::info('Excepción tiposDePersonas: '+$e->getMessage());
				return null;
			}

		}

		public static function listaMotivosBaja(){
			try{
				$tipos = \DB::connection('mysql')->select(\DB::raw('select id, codigo, valor from lista_motivos_baja'));
				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de motivos de baja.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaMotivos[$tipo->codigo]=$tipo->valor;
					}
					return $listaMotivos;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaMotivos: '+$e->getMessage());
			}


		}

		/**GETS POR ID**/

		public static function obtenerSuperficiePorId($codigo){
			try{
				$superficie = \DB::connection('suitecrm_cas')->select(\DB::raw('select valor from tbl_listas_desplegables where name="superficie_local_1" and deleted=0 and codigo="'.$codigo.'"'));

				if(count($superficie)==0){
					throw new \Exception("Error Buscando la superficie por id", 1);
				}else{
					return $superficie[0]->valor;

				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerSuperficiePorId: '+$e->getMessage());
			}
		}

		public static function obtenerUbicacionPorId($codigo){
			try{
				$ubicacion = \DB::connection('suitecrm_cas')->select(\DB::raw('select valor from tbl_listas_desplegables where name="ubicacion_local_c_list" and deleted=0 and codigo="'.$codigo.'"'));
				if(count($ubicacion)==0){
					throw new \Exception("Error Buscando la ubicación por id.", 1);
				}else{
					return $ubicacion[0]->valor;
				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerUbicacionPorId: '+$e->getMessage());
			}
		}

		public static function obtenerVidrieraPorId($codigo){
			try{
				$vidriera = \DB::connection('suitecrm_cas')->select(\DB::raw('select valor from tbl_listas_desplegables where name="vidriera_list" and deleted=0 and codigo="'.$codigo.'"'));
				if(count($vidriera)==0){
					throw new \Exception("Error Buscando la vidriera por id.", 1);
				}else{
					return $vidriera[0]->valor;
				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerVidrieraPorId: '+$e->getMessage());
			}
		}

		public static function obtenerCaracteristicasZonaPorId($codigo){
			try{
				$caracteristicasZona = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="emplazamiento" and deleted=0 and codigo="'.$codigo.'"'));
				if(count($caracteristicasZona)==0){
					throw new \Exception("Error Buscando la característica de la zona por id.", 1);
				}else{
					return $caracteristicasZona[0]->valor;

				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerCaracteristicasZonaPorId: '+$e->getMessage());
			}
		}

		public static function obtenerSocioEconomicoPorId($codigo){
			try{
				$socioEconomico = \DB::connection('suitecrm_cas')->select(\DB::raw('select valor from tbl_listas_desplegables where name="nivel_socioeconomico" and deleted=0 and codigo="'.$codigo.'"'));

				if(count($socioEconomico)==0){
					throw new \Exception("Error Buscando el nivel socioeconomico por id.", 1);
				}else{
					return $socioEconomico[0]->valor;
				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerSocioEconomicoPorId: '+$e->getMessage());
			}
		}

		public static function obtenerCirculacionPorId($codigo){
			try{
				$horario = \DB::connection('suitecrm_cas')->select(\DB::raw('select valor from tbl_listas_desplegables where name="circulacion_list" and deleted=0 and codigo="'.$codigo.'"'));

				if(count($horario)==0){
					throw new \Exception("Error Buscando circulación por código.", 1);
				}else{
					return $horario[0]->valor;
				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerCirculacionPorId: '+$e->getMessage());
			}
		}

		public static function obtenerHorarioPorId($codigo){
			try{
				$horario = \DB::connection('suitecrm_cas')->select(\DB::raw('select valor from tbl_listas_desplegables where name="horario_atencion_c" and deleted=0 and codigo="'.$codigo.'"'));

				if(count($horario)==0){
					throw new \Exception("Error Buscando el horario por codigo.", 1);
				}else{
					return $horario[0]->valor;
				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerHorarioPorId: '+$e->getMessage());
			}
		}


		public static function obtenerOcupacionPersonaPorId($id){
			try{
				$ocupacion = \DB::connection('suitecrm_cas')->select(\DB::raw('select name as valor from tbl_ocupaciones where deleted=0 and id="'.$id.'"'));

				if(count($ocupacion)==0){
					throw new \Exception("Error Buscando la ocupacion id.", 1);
				}else{
					return $ocupacion[0]->valor;
				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerOcupacionPersonaPorId: '+$e->getMessage());
			}
		}



		/**
		 * Función que devuelve todos el tipo de documento
		 * según el código que llega como parámetro
		 */
		public static function obtenerTipoDeDocumento($código){
			try{
				$tipo = \DB::connection('suitecrm_cas')->select(\DB::raw('select valor from tbl_listas_desplegables where name="tipo_documento_list" and deleted=0 and codigo="'.$codigo.'"'));
				if(count($tipo)==0){
					throw new \Exception("Error Buscando el tipo de documento por codigo.", 1);
				}else{
					return $tipo[0]->valor;
				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerTipoDeDocumento: '+$e->getMessage());
				return null;
			}

		}

		/**
		* Función que devuelve todos los tipos de situación de
		* ganancias para ser seleccionadas
		**/
		public static function listaSituacionGanancias(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('SELECT id, codigo, valor, orden  FROM tbl_listas_desplegables l WHERE NAME="sit_ganancias" AND deleted=0 ORDER BY orden'));
				if(count($tipos)==0){
					return null;
				}else{

					foreach ($tipos as $tipo) {
						$lista_tipos[$tipo->codigo]=$tipo->valor;
					}

					return $lista_tipos;
				}
			}catch(\Exception $e){
				\Log::info('Excepción listaSituacionGanancias: '+$e->getMessage());
				return null;
			}
		}

		/**
		 * Función que devuelve todos el tipo de documento
		 * según el código que llega como parámetro
		 */
		public static function obtenerSituacionGanancias($código){
			try{
				$tipo = \DB::connection('suitecrm_cas')->select(\DB::raw('select valor from tbl_listas_desplegables where name="sit_ganancias" and deleted=0 and codigo="'.$codigo.'"'));
				if(count($tipo)==0){
					throw new \Exception("Error Buscando la situación de ganancias por codigo.", 1);
				}else{
					return $tipo[0]->valor;
				}
			}catch(\Exception $e){
				\Log::info('Excepción obtenerSituacionGanancias: '+$e->getMessage());
				return null;
			}

		}

		/**
		 * Función que devuelve todos los tipos de sociedad
		 * activos para ser seleccionados
		 */
		public static function tiposDeSociedad(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select codigo, valor from tbl_listas_desplegables where name="tipo_persona_juridica_list" and deleted=0 ORDER BY codigo'));

				if(count($tipos)==0){
					return null;
				}else{
					foreach ($tipos as $tipo) {
						$tipo_sociedad[$tipo->codigo]=$tipo->valor;
					}

					return $tipo_sociedad;
				}
				return $tipo_sociedad;
			}catch(\Exception $e){
				\Log::info('Excepción tiposDeSociedad: '+$e->getMessage());
				return null;
			}

		}

		/**
	    * Busca los datos de la red
		**/
		public static function nombreRed($nroRed){
			try{
				$nombreRed = \DB::connection('suitecrm_cas')->select(\DB::raw('SELECT r.`nombre_red` FROM  suitecrm_cas_dev.`age_red` r
				WHERE r.`id_red`='.$nroRed));

				if(count($nombreRed)==0){
					return null;
				}else{
					return $nombreRed[0]->nombre_red;
				}
			}catch(\Exception $e){
				\Log::info('Excepción datosRed: '+$e->getMessage());
				return null;
			}
		}
/****************************MAQUINAS*************************************/
	/**
	* Función que verifica si el permiso está dado de baja o si es la última
	* máquina de dicho permiso.
	* WB - Mal definido: siempre verdadero
	**/
	public static function puedeRetirar($permiso){
		return true;
	}

	/*******************************************************
	* Función que busca los tipos de terminales (máquinas) *
	********************************************************/
	public static function buscarTiposTerminal(){
		try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="maq_tipo_terminal_list" and deleted=0'));
				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de superficie.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaTipos[$tipo->codigo]=$tipo->valor;
					}

					return $listaTipos;

				}
				/*$listaTipos['offline']='Offline';
				$listaTipos['online']='Online';*/
				return $listaTipos;
			}catch(\Exception $e){
				\Log::info('Excepción listaSuperficie: '+$e->getMessage());
			}
	}

	/*********************************************************************
	* Función que busca las terminales del permiso que se pueden retirar *
	**********************************************************************/

	public static function buscarTerminal($permiso){
		try{
			$idpermiso=\DB::connection('suitecrm_cas')
				->select(\DB::raw('select id FROM age_permiso p
				WHERE p.`id_permiso`='.$permiso));

			$maquinas_actuales = \DB::connection('suitecrm_cas')
				->select(\DB::raw('SELECT
					m.tipo_terminal AS tipo_terminal,
					lista.valor AS nombre_fantasia,
					COUNT(*) AS cantidad
				FROM maq_maquinas m
				INNER JOIN `maq_maquinas_age_permiso_c` mp
					ON mp.`maq_maquinas_age_permisomaq_maquinas_ida` = m.id
					AND m.`estado` IN ("asignada","suspendida")
					AND mp.deleted = 0
					AND mp.`maq_maquinas_age_permisoage_permiso_idb` ="'.$idpermiso[0]->id.'"
				INNER JOIN `tbl_listas_desplegables` lista
					ON lista.codigo = m.tipo_terminal
				GROUP BY m.tipo_terminal;'));

			//cantidad que ya pidio retirar por cada tipo y no han sido informadas
			//(trámites no cancelados)
			$maquinas_a_retirar = \DB::connection('suitecrm_cas')
				->select(\DB::raw('SELECT
					t.`tipo_terminal` as tipo_terminal,
					lista.valor as nombre_fantasia,
					COUNT(*) AS cantidad
				FROM hab_tramites t
				INNER JOIN `tbl_listas_desplegables` lista
					ON lista.codigo = t.tipo_terminal
				WHERE  t.`id_tipo_tramite` = 7
					AND t.id_estado_tramite NOT IN (8, 9, 11)
					AND t.`nro_permiso` ='.$permiso. '
					AND t.informado_crm=0
				GROUP BY t.tipo_terminal'));

			//ver cuántas más se pueden retirar de cada tipo
			$cant_disponible = [];
			$cant_a_retirar = [];
			foreach($maquinas_a_retirar as $m) //transformo a array asociativo
				$cant_a_retirar[$m->tipo_terminal] = $m->cantidad;
			foreach($maquinas_actuales as $m){
				$retiro = isset($cant_a_retirar[$m->tipo_terminal]) ? $cant_a_retirar[$m->tipo_terminal]: 0;
				$cant_disponible[$m->tipo_terminal] = [$m->cantidad - $retiro, $m->nombre_fantasia];
			}
			$cant_disponible = array_filter(
				$cant_disponible,
				function($v, $k){//elimina las que tienen 0
					return $v[0] != 0;
				},
				ARRAY_FILTER_USE_BOTH
			);

			if(empty($cant_disponible))
				return null;
			return $cant_disponible;
		}catch(\Exception $e){
			\Log::info('Excepción listaMaquinasRetirar: '+$e->getMessage());
		}
	}
/******************************PERMISOS************************************/
	/**
	* Función que devuelve la lista de permisos sin adjudicar
	**/
	public static function listadoPermisos($filtros, &$count){
		try{
				$query = 'SELECT p.`name` AS numero, DATE_FORMAT(p.date_entered,"%Y-%m%-%d") AS fecha_inicio, p.`estado`, u.`last_name` as usuario_generador
						  FROM  age_permiso AS p
						  INNER JOIN users u ON u.`id` = p.`modified_user_id`
						  WHERE p.`estado` LIKE "%Nuevo%" AND p.`id_permiso` NOT IN(SELECT nro_permiso FROM hab_tramites h where h.id_estado_tramite<>8)';
				$query2= 'SELECT count(*) as cantidad FROM  age_permiso as p WHERE p.`estado` LIKE "%Nuevo%" AND p.`id_permiso` NOT IN(SELECT nro_permiso FROM hab_tramites h where h.id_estado_tramite<>8)';

				/*if($filtros->fechaInicio!=""){
					$query=$query.' AND DATE_FORMAT(p.date_entered,"%Y-%m%-%d") >="'.$filtros->fechaInicio.'"';
					$query2=$query2.' AND DATE_FORMAT(p.date_entered,"%Y-%m%-%d") >="'.$filtros->fechaInicio.'"';
				}*/

				//cantidad de páginas
				$count = \DB::connection('suitecrm_cas')->select(\DB::raw($query2));
				$count=$count[0]->cantidad;

				$query = $query. ' ORDER BY p.date_entered ASC';
				$query = $query. ' LIMIT '.$filtros->getPorPagina();
				$query = $query. ' OFFSET '.(($filtros->getNumeroPagina()-1) * $filtros->getPorPagina());


				$permisos=\DB::connection('suitecrm_cas')->select(\DB::raw($query));

				return $permisos;


			}catch(\Exception $e){
				\Log::info('Excepción listadoPermisos: '+$e->getMessage());
				return null;
			}
	}

	/**
	* Función que devuelve la lista de permisos sin adjudicar para armar el csv
	**/
	public static function listadoPermisosCSV($filtros){
		try{
				$query = 'SELECT p.`name` AS numero, DATE_FORMAT(p.date_entered,"%Y-%m%-%d") AS fecha_inicio, p.`estado`, u.`last_name` as usuario_generador
						  FROM  age_permiso AS p
						  INNER JOIN users u ON u.`id` = p.`modified_user_id`
						  WHERE p.`estado` LIKE "%Nuevo%"';
				if($filtros->fechaInicio!=""){
					$query=$query.' AND DATE_FORMAT(p.date_entered,"%Y-%m%-%d") >="'.$filtros->fechaInicio.'"';
					$query2=$query2.' AND DATE_FORMAT(p.date_entered,"%Y-%m%-%d") >="'.$filtros->fechaInicio.'"';
				}
				$permisos=\DB::connection('suitecrm_cas')->select(\DB::raw($query));

				return $permisos;


			}catch(\Exception $e){
				\Log::info('Excepción listadoPermisos: '+$e->getMessage());
				return null;
			}
	}

	/*Retorna "1" si el correo existe en la base, caso contrario "0"*/
	public static function verificarCorreo($correo, $permiso){
		try{
				$query = "SELECT hab_control_correo('".$correo."',".$permiso.") as existe;";

				$valido=\DB::connection('suitecrm_cas')->select(\DB::raw($query));

				return $valido[0]->existe;

			}catch(\Exception $e){
				\Log::info('Excepción verificación de correo: '+$e->getMessage());
				return null;
			}
	}


	public static function listaEstadosEvaluacion(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="estado_evaluacion_list" and deleted=0'));

				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de evaluacion.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaEstadosEvaluacion[$tipo->codigo]=$tipo->valor;
	}

					return $listaEstadosEvaluacion;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaEstadosEvaluacion: '+$e->getMessage());
			}
		}

	public static function listaEstadosEvaluacionGeneral(){
			try{
				$tipos = \DB::connection('suitecrm_cas')->select(\DB::raw('select id, codigo, valor from tbl_listas_desplegables where name="estado_gral_evaluacion_list" and deleted=0'));

				if(count($tipos)==0){
					throw new \Exception("Error Buscando la lista de evaluacion estados generales.", 1);
				}else{

					foreach ($tipos as $tipo) {
						$listaEstadosEvaluacion[$tipo->codigo]=$tipo->valor;
					}

					return $listaEstadosEvaluacion;

				}
			}catch(\Exception $e){
				\Log::info('Excepción listaEstadosEvaluacionGeneral: '+$e->getMessage());
			}
		}


	}
?>
