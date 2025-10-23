@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/jquery.validate.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-consultanrotramite.js", array("type" => "text/javascript"))}}	
	{{HTML::script("js/js-click.js", array("type" => "text/javascript"))}}
	{{HTML::script('js/js-session.js', array('type'=>'text/javascript'))}}
	{{HTML::script('js/js-tramitespendientes.js?var='.rand(), array('type'=>'text/javascript'))}}
	

@stop

@section('contenido')
	<div class="row">
		<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		    </div>
		</div>
		<h1>Consultar Número Seguimiento
		</h1>	
	</div>	
		<br/>
	
	<!---Vista Nro de Seguimiento--->
	<div class="row-border">
		{{Form::open(array('action' => array('TramitesController@consultarPorNroTramite_add'), 'method' => 'post', 'id' => 'formulario-nroTramite'))}} 
			<div class="form-group form-inline">
				<div class="col-xs-4 col-sm-3 col-md-3 col-lg-2">
					{{Form::label('lntramite', 'Nro. Seguimiento')}}
				</div>
				<div class="col-xs-8 col-sm-6 col-md-6 col-lg-3">
					{{Form::text('ntramite', '', array('id' => 'id_tramite', 'name'=>'ntramite', 'class'=>'form-control','autofocus'=>'""'))}}
				</div>
			
				<div class="col-xs-12">
					{{Form::button('Consultar', array('id' => 'btn-iniciar', 'name'=>'btn-iniciar', 'class' => 'btn-primary'))}} 
				</div>
			</div>
		{{Form::close()}}
		{{Form::open(array('action' => array('TramitesController@detallePorNroSeguimiento'), 'method' => 'post', 'id' => 'formulario-tramite'))}}
			{{Form::hidden('nroTramite','',  array('id' => 'nroTramite'))}}
		{{Form::close()}}	
		
		
	</div>

	<!---Vista Listado de Tramites--->
	
	
	
	<div class="row-border">
		
		<div id="contenedor_tabla" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="table-responsive" id="tabla-scroll">
					<table class="table table-bordered table-condensed" id="tabla-tramites"> 
						<thead class="encabezados">
							<tr id="fixed-row">
								<th class="col-lg-2">Trámite</th>
								<th class="col-lg-2">Estado</th>
								<th class="col-lg-2">Nº Seguimiento</th>
								<!-- <th class="col-lg-2">Accion</th> -->
						</thead>
						<tbody id="cuerpo" class="cuerpo"></tbody>
					</table>
				</div>
					
		</div>
		
		{{Form::open(array('action' => array('TramitesController@detallePorNroSeguimiento'), 'method' => 'post', 'id' => 'formulario-tramite2'))}}
			{{Form::hidden('nroTramite','',  array('id' => 'nroTramite'))}}
		{{Form::close()}}
	
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





