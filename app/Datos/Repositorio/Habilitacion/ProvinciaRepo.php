<?php
	
	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Provincia;
	use models\Habilitacion\ProvinciaModelo;

	class ProvinciaRepo{
		
		/**
		* Devuelve la provincia del usuario e incluye aquellas que
		* tienen online=0 si el parámetro on=true(usuario cas)
		* @param type $id, $on
		* @return $provincias
		*/
		public static function buscar($id,$on=false){ //el id es de la provincia del usuario
			$result = array();
			if($on==true){
				$builder = ProvinciaModelo::query();
                
				$builder->where('online','=',0)
						->orWhere('id','=',$id);
                         
				$provincias = $builder->orderBy('nombre','ASC')->get();
			}else{
				$provincias=ProvinciaModelo::all();
			}
			
			foreach ($provincias  as $provincia) {
			       	$result[] = self::map($provincia);
				} 
				
			return $result;
		}
		
		/**
		 * Devuelve una provincia con el id pasado como parámetro
		 * @param integer $id
		 */
		public static function buscarPorId($id){
			$provincia = ProvinciaModelo::find($id);
			return self::map($provincia);
		 }
	
		
		/**
		 * Devuelve una provincia a partir de un modelo de Provincia
		 * @param type ProvinciaModelo
		 * @return \Dominio\Entidades\Premios\Provincia
		 */
		private static function map(ProvinciaModelo $modelo) {        
			$provincia = new Provincia();
			$provincia->id = $modelo->id;
			$provincia->nombre = $modelo->nombre; 
			$provincia->descripcion = $modelo->descripcion;        
			$provincia->online = $modelo->online;
            $provincia->letra = $modelo->letra;    
			
			return $provincia;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $provincia
		 * @return \Dominio\Entidades\Premios\Provincia
		 */
		private static function unmap(Provincia $provincia) {
			$modelo = new ProvinciaModelo();
			$modelo->id = $provincia->id;
			$modelo->nombre = $provincia->nombre;
			$modelo->descripcion = $provincia->descripcion;
			$modelo->online = $provincia->online;
            $modelo->letra = $provincia->letra;
						
			return $modelo;
		}
		

		/**
		* Función que retorna el modelo de Provincia
		* como un arreglo
		*/
		public static function arregloProvincia(Provincia $provincia){
			$modeloProvincia = self::unmap($provincia);
			return $modeloProvincia->toArray();
		}
	}
?>