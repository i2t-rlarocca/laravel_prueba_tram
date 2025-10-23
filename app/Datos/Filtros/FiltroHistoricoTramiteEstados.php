<?php

namespace Datos\Filtros;
   

use \Datos\Filtros\Base\Filtro;

/**
 * Filtros para buscar tramites
 * 
 */
class FiltroHistoricoTramiteEstados extends Filtro {    
    
    public $nroTramite;

    public $estadoTramite;

    public $fechaDesde;

    public $fechaHasta;

    public $permiso;

    public $agente;

    public $subAgente;
}

?>