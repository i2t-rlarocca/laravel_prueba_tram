<?php
namespace Dominio\Entidades\Habilitacion;
	class Persona{
		
		public $id;     

		public $id_persona;   //viene del crm

		public $sexo;

		public $tipo_persona; //1-física 2-jurídica

		public $tipo_sociedad; //SA, SRL, etc.

		public $situacion_ganancia;

		public $nro_ingresos;
		
		public $tipo_documento; 
		
		public $nro_documento;

		public $cuit;

		public $apellido_nombre_razon; 

		public $apellido_materno;
		
		public $domicilio_particular;

		public $fecha_nacimiento;

		public $ocupacion;

		public $email;

		public $id_localidad;

		public $id_departamento;

		public $codigo_postal;

		public $subcodigo_postal;

		public $referente;

		public $datos_contacto;
		
		public $cbu;
		
	}

?>