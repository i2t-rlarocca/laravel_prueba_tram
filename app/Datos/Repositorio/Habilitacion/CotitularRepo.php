<?php

namespace Datos\Repositorio\Habilitacion;
class CotitularRepo{
	/**
	 * Devuelve todos los cotitulares asociados a un permiso
	 */
	public static function obtenerCotitulares($permiso){
		\Log::info("Ejecutando query obtenerCotitulares");
		$db=\DB::connection('suitecrm_cas');
		$result = $db->select(\DB::raw('call tramite_obtener_cotitular('. $permiso . ');'));
		return $result;
	}
}

?>
