<?php

namespace Datos\Filtros\Base;

use \Datos\Filtros\Interfaces\IFiltro;
use \Datos\Filtros\Base\CriterioOrden;

/**
 * Clase que permite crear criterios de búsqueda y paginación 
 * para luego utilizarlos en las clases de lógica (servicios) para filtrar y paginar.
 */
class Filtro implements IFiltro {

    /**
     * Cantidad de registros que se desean obtener en la búsqueda. 
     * Típicamente es el LIMIT de la consulta.
     */
    public $porPagina = 10;
    
    /**
     * Número de página que se desea obtener en la búsqueda
     * Típicamente es el OFFSET de la consulta.     
     */
    public $numeroPagina = 1;
    
    /**
     * Arreglo que contiene ordenamientos para la consulta.
     * Contiene objetos de tipo CriterioOrden
     * 
     * @var array
     */    
    public $criteriosOrden = array();
            
    public function getCriteriosOrden() {
        return $this->criteriosOrden;
    }    
    
    public function getPorPagina() {
        return $this->porPagina;
    }
    
    public function getNumeroPagina() {
        return $this->numeroPagina;
    }    
    
    public function setPorPagina( $porPagina ) {
        
        if ($porPagina == 0) 
            $porPagina = 10;
        
        $this->porPagina = $porPagina ;
    }
    
    public function setNumeroPagina( $pagina ) {
        
        if ($pagina == 0) 
            $pagina = 1;
        
        $this->numeroPagina = $pagina ;
    }
    
    
    public function addCriterioOrden($nombre, $tipoOrden) {        
        $criterio = new CriterioOrden();
        $criterio->nombreOrden = $nombre;
        $criterio->tipoOrden = $tipoOrden;        
        $this->criteriosOrden[] = $criterio;        
    }    
    
}