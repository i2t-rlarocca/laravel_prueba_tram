<?php

	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Maquina;
	use models\Habilitacion\MaquinaModelo;
/******************ADMINISTRACIÓN MÁQUINAS*****************/
	
	class MaquinaRepo extends BaseRepo{

		/**
		* Consulta de máquinas a incorporar
		**/
		public static function maquinasAIncorporar(){
			try{
				//$modeloMaquinas=(new MaquinaModelo())->setConnection('suitecrm_cas');	
				/*$maquinas = \DB::connection('suitecrm_cas')
							->select(\DB::raw('select id, NAME AS numero, description AS descripcion, estado, fecha_inicio,tipo_terminal
							FROM  maq_maquinas m
							WHERE m.`id` NOT IN (SELECT ma.`maq_maquinas_accountsmaq_maquinas_idb` FROM maq_maquinas_accounts_c ma)
							AND m.`estado`="operativa" AND deleted=0'));*/
				$maquinas = \DB::connection('suitecrm_cas')
							->select(\DB::raw('select id, NAME AS numero, description AS descripcion, estado, fecha_inicio,tipo_terminal
							FROM  maq_maquinas m
							WHERE m.`id` NOT IN (SELECT ma.`maq_maquinas_accountsmaq_maquinas_idb` FROM maq_maquinas_accounts_c ma)
							AND m.`estado`="asignada" AND deleted=0'));
				
				if(count($maquinas)==0){
					return null;
				}else{
					foreach ($maquinas as $ma) {
						$maquina = new Maquina();
						$maquina->id = $ma->id ;
						$maquina->nroMaquina = $ma->numero;
						$maquina->descripcion = $ma->descripcion;
						$maquina->tipoTerminal = $ma->tipo_terminal;
						$maquina->estado = $ma->estado;
						$maquina->fechaInicio = $ma->fecha_inicio;
						$result[]=$maquina;
					}
					
					return $result;
						
				}
			}catch(\Exception $e){
				\Log::info('Excepción listaMaquinasIncorporar: '+$e->getMessage());
			}		

		}
		
		public static function incorporarMaquina($maquina,$permiso, $usuarioAsigna){
			try{
				$idaccount=\DB::connection('suitecrm_cas')
						->select(\DB::raw('select account_id_c as id FROM age_permiso p
  							WHERE p.`id_permiso`='.$permiso));
				if(count($idaccount)>0){
					$ok = \DB::connection('suitecrm_cas')
						->insert('insert into maq_maquinas_accounts_c(id,date_modified,maq_maquinas_accountsaccounts_ida,maq_maquinas_accountsmaq_maquinas_idb)  values (UUID(), NOW(), ?, ?)', [$idaccount[0]->id,$maquina]);
					if($ok){
						$ok2 =\DB::connection('suitecrm_cas')
						->update('update maq_maquinas set modified_user_id=?,assigned_user_id=? where id=?',[$usuarioAsigna,$usuarioAsigna,$maquina]);
					}else{
						$ok2 =\DB::connection('suitecrm_cas')->rollback();
					}					
				}else{
					$ok="Error al encontrar el identificador de la máquina seleccionada.";
				}
				return $ok;
			}catch(\Exception $e){
				\DB::connection('suitecrm_cas')->rollback();
				\Log::error('Excepción al incorporar máquina.');
				return $ok="Error al incorporar la máquina.";
			}
		}

		/**
		* Consulta de máquinas a retirar del permiso
		**/
		public static function maquinasARetirar($permiso){
			try{
				
				$idaccount=\DB::connection('suitecrm_cas')
						->select(\DB::raw('select account_id_c as id FROM age_permiso p
  							WHERE p.`id_permiso`='.$permiso));

				$maquinas = \DB::connection('suitecrm_cas')
							->select(\DB::raw('select id, NAME AS numero, description AS descripcion, estado, fecha_inicio,tipo_terminal
							FROM  maq_maquinas m
							WHERE m.`id` IN (SELECT ma.`maq_maquinas_accountsmaq_maquinas_idb` FROM maq_maquinas_accounts_c ma where ma.maq_maquinas_accountsaccounts_ida="'.$idaccount[0]->id.'") 
							AND m.`estado` IN ("asignada","operativa","suspendida") AND deleted=0'));
				
				if(count($maquinas)==0){
					return null;
				}else{
					foreach ($maquinas as $ma) {
						$maquina = new Maquina();
						$maquina->id = $ma->id ;
						$maquina->nroMaquina = $ma->numero;
						$maquina->descripcion = $ma->descripcion;
						$maquina->tipoTerminal = $ma->tipo_terminal;
						$maquina->estado = $ma->estado;
						$maquina->fechaInicio = $ma->fecha_inicio;
						$result[]=$maquina;
					}
					
					return $result;
						
				}
			}catch(\Exception $e){
				\Log::info('Excepción listaMaquinasRetirar: '+$e->getMessage());
			}	
		}

		/**
		* Función que realiza elimina la relación máquina-permiso
		**/
		public static function retirarMaquina($maquina,$permiso,$usuarioModifica){
			try{
				$idaccount=\DB::connection('suitecrm_cas')
						->select(\DB::raw('select account_id_c as id FROM age_permiso p
  							WHERE p.`id_permiso`='.$permiso));
				if(count($idaccount)>0){
					$ok = \DB::connection('suitecrm_cas')
						->delete('delete from maq_maquinas_accounts_c where maq_maquinas_accountsmaq_maquinas_idb=? and maq_maquinas_accountsaccounts_ida=?',[$maquina,$idaccount[0]->id]);
					if($ok){
						$ok2 =\DB::connection('suitecrm_cas')
						->update('update maq_maquinas set modified_user_id=?,assigned_user_id=? where id=?',[$usuarioModifica,'NULL()',$maquina]);
					}else{
						$ok2 =\DB::connection('suitecrm_cas')->rollback();
					}					
				}else{
					$ok="Error al encontrar el identificador de la máquina seleccionada.";
				}
				return $ok;
			}catch(\Exception $e){
				\DB::connection('suitecrm_cas')->rollback();
				\Log::error('Excepción al retirar máquina.');
				return $ok="Error al retirar la máquina.";
			}
		}

	}
?>