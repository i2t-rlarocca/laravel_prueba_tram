<?php

namespace Datos\Filtros\Base;

use \Datos\Filtros\Interfaces\ICriterioOrden;

/**
 * Permite almacenar un ordenamiento indicando el nombre 
 * del campo por el que se va a ordenar y el tipo (ascendente o descendente)
 */
class CriterioOrden implements ICriterioOrden {
    
    /**
     * Nombre del campo de ordenamiento
     * @var string
     */
    var $nombreOrden;
    
    /**
     * Indica como se ordena este campo: Ascendente o Descendente
     * @var integer 
     */
    var $tipoOrden;    
    
    function getTipoOrden() {
        if ($this->tipoOrden == "ASC" || $this->tipoOrden == 1 ) {
            return ICriterioOrden::ORDEN_ASC;
        }elseif ($this->tipoOrden == "DESC" || $this->tipoOrden == 0 ) {
            return ICriterioOrden::ORDEN_DESC;
        }else {   
            return ICriterioOrden::ORDEN_ASC;            
        }
    }
    
    function getNombreOrden() {
        return $this->nombreOrden;
    }
    
}