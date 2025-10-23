<?php

namespace Datos\Filtros\Interfaces;

interface IFiltro {
    
    function getCriteriosOrden() ;
    function addCriterioOrden($nombre, $tipoOrden) ;
    function getPorPagina() ;   
    function setPorPagina($porPagina) ;   
    function setNumeroPagina($numeroPagina) ;
    
}

?>
