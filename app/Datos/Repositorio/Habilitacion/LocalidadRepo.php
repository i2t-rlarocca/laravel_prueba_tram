<?php
	
	namespace Datos\Repositorio\Habilitacion;
	use Dominio\Entidades\Habilitacion\Localidad;
	use models\Habilitacion\LocalidadModelo;
	
	class LocalidadRepo{
		
		/**
		 * Devuelve true si la localidad cuyo id llega como parámetro
		 * existe
		 * @param type $id
		 * @return boolean
		 */
		public static function buscar($id){
			$localidad = LocalidadModelo::find($id);
			
			if (is_null($localidad)) {
				return false;
			}else{
				$localidad = self::map($localidad);
				$datos['nombre']=rtrim($localidad->nombre).'('.$localidad->codigoPostal.'-'.$localidad->subCodigoPostal.')';
				$datos['cpscp']=$localidad->codigoPostal.'-'.$localidad->subCodigoPostal;
				return $datos;
			} 
		}

		public static function buscarPorCPSCPLocalidad($cp, $scp){
			$localidad = LocalidadModelo::where('codigo_postal','=',$cp)->where('subcodigo_postal','=',$scp)->first();
			
			if (is_null($localidad)) {
				return false;
			}else{
				$localidad = self::map($localidad);
				return $localidad;
			} 
		}

		/**
		 * Devuelve todas las localidades
		 * @return [Dominio\Entidades\Premios\Localidad]
		 */
		public static function buscarTodas(){
			$localidades=LocalidadModelo::all();
			
			$result = array();        
			foreach ($localidades  as $localidad) {
				$result[] = self::map($localidad);
			}        
        
			return $result;       
		}
		
		
		/**
		 * Devuelve un conjunto de localidades que contengan
		 * en su nombre lo que llega como parámetro. 
		 * @param type $nombre
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public static function buscarSimilaresPorNombre($nombre) {        
        
			$builder = LocalidadModelo::query();
			$builder->where('nombre_cp','like','%'.$nombre.'%'); 			
			$localidades = $builder->orderBy('nombre','ASC')->get();
			
			$result = array();        
			foreach ($localidades  as $localidad) {
				$result[] = self::map($localidad);
			}        
        
			return $result;          
		}

		/**
		 * Devuelve un conjunto de localidades que contengan
		 * en su nombre lo que llega como parámetro. 
		 * @param type $nombre
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public static function buscarSimilaresPorNombre_santaFe($nombre) {        
        
			$builder = LocalidadModelo::query();
			$builder->where('id_provincia','=',22)
			        ->where('nombre_cp', 'like', '%'.$nombre.'%');
			$localidades = $builder->orderBy('nombre','ASC')->get();
			
/*

DB::table('users')
            ->where('name', '=', 'John')
            ->orWhere(function($query)
            {
                $query->where('votes', '>', 100)
                      ->where('title', '<>', 'Admin');
            })
            ->get();

PARA HACER SELECCIONES CON VARIOS CLAUSULAS
SELECT * FROM foo WHERE a = 'a' 
AND (
    WHERE b = 'b'
    OR WHERE c = 'c'
)
AND WHERE d = 'd'


Foo::where( 'a', '=', 'a' )
    ->where( function ( $query )
    {
        $query->where( 'b', '=', 'b' )
            ->or_where( 'c', '=', 'c' );
    })
    ->where( 'd', '=', 'd' )
    ->get();
*/			
			
			
			$result = array();        
			foreach ($localidades  as $localidad) {
				$result[] = self::map($localidad);
			}        
        
			return $result;          
		}

		/**
		 * Devuelve un conjunto de localidades que contengan
		 * en su nombre lo que llega como parámetro. 
		 * @param type $nombre
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public static function buscarSimilaresPorNombre_santaFe_dpto($nombre, $dpto) {        
        
			$builder = LocalidadModelo::query();
			$builder->where('nombre','like','%'.$nombre.'%')
					->where('id_provincia','=',22)
					->where('id_departamento','=',$dpto);
			$localidades = $builder->orderBy('nombre','ASC')->get();
			
			$result = array();        
			foreach ($localidades  as $localidad) {
				$result[] = self::map($localidad);
			}        
        
			return $result;          
		}
		
		
		/**
		 * Devuelve la localidad cuyo id llega como parámetro
		 * @param type $id
		 * @return [\Dominio\Entidades\Premios\Localidad]
		 */
		public static function buscarPorIdLocalidad($id){
			$localidad = LocalidadModelo::find($id);
			
			if (is_null($localidad)) {
				return null;
			}else{
				return self::map($localidad);
			} 
		}
		
				
		/**
		 * Devuelve una Localidad a partir de un modelo de localidad
		 * @param type ModeloLocalidad
		 * @return \Dominio\Entidades\Premios\Localidad
		 */
		private static function map(LocalidadModelo $modelo) { 
			$localidad = new Localidad();
			$localidad->id = $modelo->id;
			$localidad->nombre = $modelo->nombre;
			$localidad->codigoPostal = $modelo->codigo_postal;        
			$localidad->subCodigoPostal = $modelo->subcodigo_postal;    
			$localidad->idProvincia = $modelo->id_provincia;  
			$localidad->cantidadHabitantes = $modelo->cantidad_habitantes;
			$localidad->comuna = $modelo->comuna;
			$localidad->codigoDepartamento = $modelo->id_departamento;
			
			return $localidad;
		}
		
		
		/**
		 * Devuelve un modelo a partir de una entidad
		 *(camino inverso a map)
		 * @param type $localidad
		 * @return \Dominio\Entidades\Premios\Localidad
		 */
		private static function unmap(Localidad $localidad) {
			$modelo = new LocalidadModelo();
			$modelo->id = $localidad->id;
			$modelo->nombre = $localidad->nombre;
			$modelo->codigo_postal = $localidad->codigoPostal;
			$modelo->subcodigo_postal = $localidad->subCodigoPostal;
			$modelo->id_provincia = $localidad->idProvincia;  
			$modelo->cantidad_habitantes = $localidad->cantidadHabitantes;
			$modelo->comuna = $localidad->comuna;
			$modelo->id_departamento = $localidad->codigoDepartamento;
			
			return $modelo;
		}


		public static function buscarPorCodigoPostal($cp){
			$localidad = LocalidadModelo::where('codigo_postal','=',$cp)->first();
			
			if (is_null($localidad)) {
				return null;
			}else{
				return self::map($localidad);
			} 
		}

		/**
			Busca el departamento de la localidad cuyo id es pasado por parámetro.
		**/

		public static function buscarDepartamentoLocalidad($idLocalidad){
			$localidad = LocalidadModelo::where('id','=',$idLocalidad)
						->where('id_provincia','=',22)->first();
			if(is_null($localidad) || count($localidad)==0){
				return null;
			}

			$departamento['id']=$localidad->departamento['id'];
			$departamento['nombre']=$localidad->departamento['descripcion'];
			
			return $departamento;
		}

		/**
			Busca el departamento de la localidad cuyo id es pasado por parámetro.
		**/

		public static function buscarDepartamentoPorCP_SCP($cp,$scp){

			$localidad = LocalidadModelo::where('codigo_postal','=',$cp)
						->where('subcodigo_postal','=',$scp)->first();

			if(is_null($localidad) || count($localidad)==0){
				return null;
			}

			$departamento['id']=$localidad->departamento['id'];
			$departamento['nombre']=strtoupper($localidad->departamento['descripcion']);
			
			return $departamento;
		}
		

		/**
		 Función que retorna el modelo de Localidad
		 como un arreglo
		*/
		public static function arregloLocalidad(Localidad $localidad){
			$modeloLocalidad = self::unmap($localidad);
			return $modeloLocalidad->toArray();
		}
		
	}
?>