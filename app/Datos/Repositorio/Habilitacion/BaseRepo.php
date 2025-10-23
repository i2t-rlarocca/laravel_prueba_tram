<?php

namespace Datos\Repositorio\Habilitacion;

use Datos\Filtros\Interfaces\ICriterioOrden;
use Datos\Filtros\Interfaces\IFiltro;

class BaseRepo {    
    
    public static function OrdenarYPaginar($builder, IFiltro $filtro, array $map = null) {
        
        self::ordenar($builder, $filtro->getCriteriosOrden(), $map);
        
        self::paginar($builder, $filtro);
    
        return $builder;        
    }
    
    public static function OrdenarSinPaginar($builder, IFiltro $filtro, array $map = null) {
        
        self::ordenar($builder, $filtro->getCriteriosOrden(), $map);
           
        return $builder;        
    }
    
    private static function ordenar($builder, $criterios, $map) {
        
        foreach ($criterios as $criterio) {            
            
            if ((! $criterio->getNombreOrden() == '') && (!is_null($criterio->getNombreOrden())) ) {
               
                if ($criterio->getTipoOrden() == ICriterioOrden::ORDEN_ASC) {
                     $tipoOrden = 'ASC' ; 
                } else {
                     $tipoOrden = 'DESC' ; 
                }                  
                
                $orden = $criterio->getNombreOrden();
                 
                if (!is_null($map)) {                                        
                    if (key_exists($orden, $map)) {
                      $orden = $map[$orden];
                    }
                }
                
                $builder->orderBy($orden, $tipoOrden);

            }
        }
    }
    
    //skip() specifies the OFFSET, take() specifies the LIMIT.
    private static function paginar($builder, IFiltro $paginador) { 
        $builder->skip(($paginador->getNumeroPagina()-1) * $paginador->getPorPagina()) //en función de la página es desde cuál registro debe traer
                ->take($paginador->getPorPagina());        //obtiene hasta tantos registros
    }        
    
    
    
}

?>