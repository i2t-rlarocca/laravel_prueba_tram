@extends('layouts.base')
@section('javascript')
	{{--HTML::script("js/js-comprobante.js", array("type" => "text/javascript"))--}}
	{{--HTML::script("js/js-envioemailinicio.js", array("type" => "text/javascript"))--}}
	{{HTML::script("js/js-click.js", array("type" => "text/javascript"))}}	
@stop	
@section('contenido')

{{Form::hidden('msj',Session::get('mensaje'),  array('id' => 'msj'))}}	
{{Form::hidden('usuario',$usuario,  array('id' => 'id_usuario'))}}	


	<div class="row">
		<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		    </div>
		</div>
		<div class="col">
			<h1>{{$titulo}}</h1>	
		</div>
	</div>	
	</br>
	<div class="row-border">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<h2>Se ha dado inicio al trámite solicitado.</h2>
			</div>
		</div>	
		<div class="row">
			
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<h2>Nº de seguimiento: <strong>{{$nro_seguimiento}}</strong></h2>
			</br>
				<h5>Para completar el trámite, debe cumplimentar con toda la documentación y requisitos, que se especifican en la "Guía de trámites", y completar la carpeta que debe presentar en mesa de entrada de la Caja de Asistencia Social de la Lotería de Santa Fe.
				<br>
				Para conocer el estado actual de su trámite, puede consultarlo desde la opción "Consulta de trámites" de la "Guía de trámites", en el portal, informando el número de seguimiento del mismo.
				<br>
				Recuerde que los trámites, tienen fecha de expiración, y se deben completar en tiempo y forma
				</h5>
				
				<strong><a href="#" onclick='guia();'>Ir a Guía de trámites</a></strong>
				
			</div>
		</div>
		@if (stristr($titulo, "Cancela")===FALSE)
			<div class="row">
				<br/>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					@if(Session::has('mensaje')){{ 'Recibirá el comprobante en su casilla de correo.' }}@endif
				</div>
			</div>
		@endif
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_sin_padding">

			@if (stristr($titulo, "Cancela")!==FALSE)
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
				{{Form::button('Cancelar otro trámite', array('id' => 'btn-cancelar', 'name'=>'btn-cancelar', 'class' => 'btn-primary', 'onclick'=>'cancelar()'))}}
				</div>
			@endif 
			{{Form::open(array('action' => array('TramitesController@detallePorNroSeguimiento'), 'method' => 'post', 'id' => 'formulario-nroTramite'))}} 
			
					{{Form::hidden('nroTramite',$nro_seguimiento,array('id'=>'nroTramite'))}}
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-6">
						{{Form::submit('Detalle Trámite', array('id' => 'btn-iniciar', 'name'=>'btn-iniciar', 'class' => 'btn-primary'))}} 
					</div>
							
			{{Form::close()}}
		</div>
	</div>	
	
@endsection

@section('contenido_modal')
	<div id="sesionModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content" id="modal-content">
					 <div class="modal-body">
						<h3>Su sesión ha finalizado.</h3>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
@endsection
