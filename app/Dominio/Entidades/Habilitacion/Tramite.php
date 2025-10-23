<?php
namespace Dominio\Entidades\Habilitacion;
	class Tramite{
		public $nroTramite;
		
		public $tipoTramite;//objeto tipo tramite
		
		public $nroPermiso;
		
		public $estadoTramite;//objeto estado tramite

		public $usuario;

		public $agente;

		public $subAgente;

		public $fecha;

		public $observaciones;

		public $razonSocial;
		 
		public $titular;

		public $localidadAgencia;

		public $codigoPostalAgencia;
		
		public $domicilioAgencia;

		public $resolucion;

		public $expediente;

		public $rutaHistorialPDF;

		public $informado_crm;
		
		public $pendienteInformar;

		public $retiro_definitivo;

		/*Campos de otros modelos*/

		public $ingreso; //del historial de estados del trámite-->según sea CAS o CAS_ROSARIO
		
		public $nuevo_permiso;

		public $detalle_completo;
		
		public $id_motivo_baja;
		
		public $fechaSuspHab; //fecha hasta para trámites de suspensión - fecha desde para trámites de habilitación
		
		public $tipo_terminal; //tipo de terminal para los trámites de retiro/incorporación de máquinas
	}

?>
