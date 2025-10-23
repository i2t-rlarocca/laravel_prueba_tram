<?php

namespace Datos\Filtros;
   

use \Datos\Filtros\Base\Filtro;

/**
 * Filtros para buscar tramites
 * 
 */
class FiltroTramite extends Filtro {    
    
    public $tipoTramite;

    public $estadoTramite;

    public $fechaDesde;

    public $fechaHasta;

    public $permiso;

    public $agente;

    public $subAgente;

    public $ddv_nro;

    public $mesaEntrada;
	
    public $nuevoPermiso;
}

?>