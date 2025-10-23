<?php
	
	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Departamento;
	use models\Habilitacion\DepartamentoModelo;
	
	class DepartamentoRepo{
		
		public static function buscarTodos(){
			$departamentos = DepartamentoModelo::all();
			
			if (is_null($departamentos)) {
				return null;
			}else{
				foreach ($departamentos as $departamento) {
					$result[]=self::map($departamento);
				}
				return $result;
			} 
			
		}
		
		/**
			Busca el departamento cuyo id es pasado por parámetro
		**/

		public static function buscarDepartamento($id){
			$departamento = DepartamentoModelo::where('id','=',$id)->first();
			if(is_null($departamento) || count($departamento)==0){
				return null;
			}

			$departamento=self::map($departamento);
			
			return $departamento;
		}
				
		/**
		 * Devuelve un Departamento a partir de un modelo de departamento
		 * @param type ModeloDepartamento
		 * @return \Dominio\Entidades\Habilitacion\Departamento
		 */
		private static function map(DepartamentoModelo $modelo) { 
			$departamento = new Departamento();
			$departamento->id = $modelo->id;
			$departamento->idProvincia = $modelo->id_provincia;
			$departamento->descripcion = $modelo->descripcion;
		
			return $departamento;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $departamento
		 * @return \Dominio\Entidades\Habilitacion\Departamento
		 */
		private static function unmap(Departamento $departamento) {
			$modelo = new DepartamentoModelo();
			$modelo->id = $departamento->id;
			$modelo->id_provincia = $departamento->idProvincia;
			$modelo->descripcion = $departamento->descripcion;
		
			return $modelo;
		}


		

		/**
		* Función que retorna el modelo de Departamento
		* como un arreglo
		*/
		public static function arregloDepartamento(Departamento $departamento){
			$modeloDepartamento = self::unmap($departamento);
			return $modeloDepartamento->toArray();
		}
		
	}
?>