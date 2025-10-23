<?php

namespace Datos\Filtros\Interfaces;

interface ICriterioOrden {
    
    const ORDEN_DESC = 0;    
    const ORDEN_ASC = 1;
   
    function getNombreOrden();
    
    function getTipoOrden();
    
}

?>
