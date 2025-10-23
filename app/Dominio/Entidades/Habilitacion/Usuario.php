<?php
namespace Dominio\Entidades\Habilitacion;
	class Usuario{
		//de la tabla users
		public $id;  

		public $nombreUsuario; //user_name      
		
		public $nombre; //first_name
		
		public $apellido; //last_name
		
		//de la tabla i2t01_tipos_usuarios
		
		public $tipoUsuario; //tipo de usuario de la tabla i2t01_tipos_usuarios, a traves de la relación users_cstm (campo:  i2t01_tipo_usuarios_id_c)
		
		public $nombreTipoUsuario;
		
		public $descripcionTipoUsuario;
		
		public $provincia;

		public $localidadAgencia;

		public $idLocalidad;/*Solo para uso en el objeto, no se guarda en la tabla de la base*/

		public $codigoPostalAgencia;

		public $subcodigoPostalAgencia;

		public $departamentoAgencia;

		public $email;

		public $agente;

		public $subAgente;

		public $permiso;

		public $razonSocial;
		
		public $titular;

		public $domicilioAgencia;

		public $listaFunciones; /*Sólo para uso en el objeto. No se guarda en la tabla de usuarios*/

		public $estado_comercializacion;

		public $cbu;
		
	}

?>