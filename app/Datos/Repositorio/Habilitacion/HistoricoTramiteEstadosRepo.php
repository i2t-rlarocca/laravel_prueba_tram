<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\HistoricoTramiteEstados;
	use models\Habilitacion\HistoricoTramiteEstadosModelo;
	use \Carbon\Carbon;

	class HistoricoTramiteEstadosRepo{
				
		/**
		 * Devuelve todos los estados por los que pasó un trámite 
		 * @param type $id
		 * @return \Dominio\Entidades\Habilitacion\HistoricoTramiteEstados
		 */
		public static function buscarPorId($id){
            $historial = HistoricoTramiteEstadosModelo::where("nrotramite","=",$id)->get();

			if (is_null($historial)) {
				return null;
			}else{
				foreach ($historial as $key => $hestado) {
					$result[] = self::map($hestado);
				}
				return $result;
			} 
		}

		public static function existeUnoIgual($idTramite, $idEstadoI, $idEstadoF){//,$comentarios){
			$historial = HistoricoTramiteEstadosModelo::where("nrotramite","=",$idTramite)
						->where('idestado_ini','=',$idEstadoI)
						->where('idestado_fin','=',$idEstadoF)
						->orderBy('fecha', 'DESC')->take(1)->get();
						//->where('observaciones','=',$comentarios)->get();
			if (count($historial)==0) {
				return false;
			}else{
				return true;
			} 
		}
		
		/*Último historial*/
		public static function ultimoHistorial($nroTramite){
			$hist = HistoricoTramiteEstadosModelo::where('nrotramite','=',$nroTramite)
				    ->orderBy('fecha', 'DESC')
					->first();
						
			return self::map($hist);
			
		}
		
		
		/************************************************/
		/* Función que devuelve la última fecha en que  */
		/* se modificó el estado de un trámite.         */
		/************************************************/
		public static function fechaUltimaModificacion($id){
			$query = HistoricoTramiteEstadosModelo::where('nrotramite','=',$id)
						->max('fecha');
			$fechaUltimaModif = Carbon::parse($query)->format('d/m/Y');		
			return $fechaUltimaModif;
		}

		/*************************************************/
		/* Fecha último trámite del tipo que llega por   */ 
		/* parámetro para el permiso ingresado			 */
		/*************************************************/
		public static function fechaUltimoTramite($permiso, $tipoTramite){

			$fechas = HistoricoTramiteEstadosModelo::select(\DB::raw('COALESCE(TIMESTAMPDIFF(MONTH,MAX(hist_tramites_estados.fecha),CURDATE()),0) as meses,
					COALESCE(TIMESTAMPDIFF(DAY,MAX(hist_tramites_estados.fecha),CURDATE()),0) AS dias,
					CASE WHEN COALESCE(TIMESTAMPDIFF(MONTH,MAX(hist_tramites_estados.fecha),CURDATE()),0) >= tt.margen_entre_tramites THEN 1 ELSE 0 END AS paso_fecha'))
			->join('tramites as t', 't.nro_tramite', '=', 'nrotramite')
			->join('tipo_tramite as tt', 'tt.id_tipo_tramite','=','t.id_tipo_tramite')
			->where('t.nro_permiso','=',$permiso)
			->where('t.id_tipo_tramite','=',$tipoTramite)
			->where('t.id_estado_tramite','=',10)->first();
		
			return $fechas;
		}
		
		/********************************************************/
		/* Modifica la observación de un estado de un trámite   */
		/********************************************************/
		public static function modificaObservacionEstado($nroTramite, $fecha, $estaI, $estaF, $observaciones){
			try{
				HistoricoTramiteEstadosModelo::where("nrotramite","=",$nroTramite)
						->where('idestado_ini','=',$estaI)
						->where('idestado_fin','=',$estaF)
						->where('fecha','=',$fecha)->update(array('observaciones' => $observaciones));
				
			}catch(Exception $e){
					\Log::error('Problema al cambiar las observaciones del estado. '.$e);
					return null;
			}
		}

		/**
		* crea un registro en el historial
		*/
		public static function crear($historico){
			$modelo = self::unmap($historico);
			$modelo->save(); 

			return self::map($modelo);
		}

		
		/**
		 * Devuelve un HistoricoTramiteEstados a partir de un modelo de HistoricoTramiteEstados
		 * @param type HistoricoTramiteEstadosModelo
		 * @return \Dominio\Entidades\Habilitacion\HistoricoTramiteEstados
		 */
		private static function map(HistoricoTramiteEstadosModelo $modelo) {        
			$his_tramite_esta = new HistoricoTramiteEstados();
			$his_tramite_esta->nroTramite = $modelo->nrotramite;
			$his_tramite_esta->fecha = $modelo->fecha;
			$his_tramite_esta->idEstadoIni = $modelo->idestado_ini;
			$his_tramite_esta->idEstadoFin = $modelo->idestado_fin;
			$his_tramite_esta->tipo_usuario = $modelo->tipo_usuario;
			$his_tramite_esta->usuario = $modelo->usuario; 
			$his_tramite_esta->observaciones = $modelo->observaciones;        
			
			return $his_tramite_esta;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $HistoricoTramiteEstados
		 * @return \Dominio\Entidades\Habilitacion\HistoricoTramiteEstados
		 */
		private static function unmap(HistoricoTramiteEstados $his_tramite_esta) {
			$modelo = new HistoricoTramiteEstadosModelo;
			$modelo->nrotramite = $his_tramite_esta->nroTramite;
			$modelo->fecha = $his_tramite_esta->fecha;
			$modelo->idestado_ini = $his_tramite_esta->idEstadoIni;
			$modelo->idestado_fin = $his_tramite_esta->idEstadoFin;
			$modelo->tipo_usuario = $his_tramite_esta->tipo_usuario;
			$modelo->usuario = $his_tramite_esta->usuario; 
			$modelo->observaciones = $his_tramite_esta->observaciones;    
			
			return $modelo;
		}		
			

		/**
		* Función que retorna el modelo de HistoricoTramiteEstados
		* como un arreglo
		*/
		public static function arregloHistoricoTramiteEstados(HistoricoTramiteEstados $his_tramite_esta){
			$modeloHistoricoTramiteEstados = self::unmap($his_tramite_esta);
			return $modeloHistoricoTramiteEstados->toArray();
		}

		/**
		* Función que buscar un listado de tramites en función de
		* los filtros recibidos por parámetro.
		**/
		public static function listadoHistoricos($filtros, &$count) {
    			        
			$query=TramiteModelo::query();	
    
     
	        if ($filtros->permiso != "") {//si no define el permiso=> todos
	        	$query = $query->where('nro_permiso','=' ,$filtros->permiso);
	        }
	        if ($filtros->agente != "") {//si no define el agente=> todos
	        	$query = $query->where('agente','=' ,$filtros->agente);
	        }
	        if ($filtros->subAgente != "") {//si no define el subAgente=> todos
	        	$query = $query->where('subagente','=' ,$filtros->subAgente);
	        }

	        if($filtros->fechaDesde!=""){
	        	if($filtros->fechaHasta!="" && $filtros->fechaDesde==$filtros->fechaHasta){
	        		$query = $query->where('fecha','=' ,$filtros->fechaDesde);
	        	}else if($filtros->fechaHasta!="" && $filtros->fechaDesde<$filtros->fechaHasta){
	        		$query = $query->whereBetween('fecha',array($filtros->fechaDesde,$filtros->fechaHasta));
	        	}else if($filtros->fechaHasta==""){
					$query = $query->where('fecha','>=' ,$filtros->fechaDesde);
	        	}
	        }else if ($filtros->fechaHasta != "") {//si no define la fechaHasta=> todos hasta el final
	        	$query = $query->where('fecha','<=' ,$filtros->fechaHasta);
	        }

	        //estados elegidos
	        $estados=str_split($filtros->estadoTramite);
	        $query=$query->whereIn('id_estado_tramite', $estados);
	        
	        //tipos de tramite
	        $tipos=str_split($filtros->tipoTramite);
	        $query = $query->whereIn('id_tipo_tramite',$tipos);


	        $count = $query->count();
	        
	        $tramites = self::ordenarYPaginar($query, $filtros);

	        foreach ($tramites as $tramite) {
	         	$result[]=self::map($tramite);
	         } 

	         return $result;
 		    //return $tramites->toArray();

	    }  


	}
?>