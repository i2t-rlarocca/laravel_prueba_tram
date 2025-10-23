<?php
namespace controllers;	
	use Dominio\Servicios\Premios\Comprobantes;
	use Dominio\Servicios\Premios\Talonarios;
	use Dominio\Servicios\Premios\TipoComprobantes;
	use Dominio\Servicios\Premios\SicoreComprobantes;
	use Dominio\Servicios\Premios\SicorePersonas;
	use controllers\ProvinciaController;
	use Dominio\Entidades\Premios\Premio;
	use Dominio\Entidades\Premios\SicoreComprobante;
	use Presentacion\Premios\Formatos;

	class ComprobanteController extends \BaseController{
	
		function __construct(){
			$this->servicio = new Comprobantes();
			$this->talonarioServicio = new Talonarios();
			$this->tipoComprobanteServicio = new TipoComprobantes();
			$this->sicorePersonaServicio = new SicorePersonas();
			$this->sicoreComprobanteServicio = new SicoreComprobantes();
			$provinciaControlador = new ProvinciaController();
			$this->provinciaServicio = $provinciaControlador->servicio;
			$this->formatos = new Formatos();
		}
		
		/**
		* Genera un comprobante de pago para el premio
		* que es pasado por parámetro
		*/
		public function generarComprobante($datosPremio){
			//genero los elementos necesarios para armar el comprobante cabecera
			
			$tipoComprobante=$this->tipoComprobanteServicio->buscar($datosPremio['id_juego'],$datosPremio['id_provincia']);
			$talonario=$this->talonarioServicio->buscarPorId($tipoComprobante['id_talonario']);
			
			$numeroComprobante=$this->formatos->armaNumeroComprobante($talonario['letra'],$talonario['punto_venta'],$talonario['proximo_numero']);


			//armo el comprobante cabecera más los comprobantes detalle
			$datosPremio['tipo_comprobante'] = $tipoComprobante['id'];
			$datosPremio['numero_comprobante'] = $numeroComprobante;
			$datosPremio['id_talonario'] = $talonario['id'];

			$datosComprobante=$this->servicio->generarComprobante($datosPremio);

			//Consulto si existe el beneficiario (en sicorePersona)
			$idBeneficiario = $this->sicorePersonaServicio->buscarPorCuit($datosPremio['cuit']);
			
			if(is_null($idBeneficiario)){//no existe el beneficiario
				$this->sicorePersonaServicio->crear($datosPremio);
			}else{
				$this->sicorePersonaServicio->modificar($datosPremio,$idBeneficiario);
			}

			//armo el sicoreComprobante
			$sicoreComprobante = $this->sicoreComprobanteServicio->crear($datosPremio,$datosComprobante);


			//comprobar si se generó el comprobanteCabecera. En caso afirmativo, sumar 1 al próximo nº
			if (is_null($datosComprobante)) {
				return null; //algún mensaje
			}else{
				$this->talonarioServicio->generarProximoNumero($talonario['id']);
			}
			
			$datos['idCompCabecera'] = $datosComprobante['id'];
			$datos['idSicoreComp'] = $sicoreComprobante['id'];
			return $datos;
		}


		/**
		* Retorna un arreglo con todos los comprobantes con sus
		* detalles.
		**/

		public function buscarTodos(){
			return $this->servicio->buscarTodos();
		}

		/**
		* Retorna un arreglo con todos los comprobantes y sus
		* detalles, que corresponden al comprobante cabecera 
		* cuyo id es pasado por parámetro
		**/

		public function buscarPorIdCabecera($idCabecera){
			return $this->servicio->buscarPorIdCabecera();
		}
		
	}
	
?>