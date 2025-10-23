@extends('layouts.base')
@section('javascript')
		{{HTML::script("js/js-click.js", array("type" => "text/javascript"))}}	
@stop
		
@section('contenido')
	<div class="row">
		<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		    </div>
		</div>
		<div class="col">
			<h1>Rechazar Trámite</h1>	
		</div>
	</div>	
		</br>
	<div class="row-border">
		@if(Session::get('usuarioLogueado.nombreTipoUsuario')=='CAS' || Session::get('usuarioLogueado.nombreTipoUsuario')=='CAS_ROS')
			{{Form::open(array('action' => array('TramitesController@consultaPermiso'), 'method' => 'post', 'id' => 'formulario-permiso', 'class'=>'form-horizontal'))}} 
				<div class="form-group">
					
					{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_rechazo','class'=>'control-label col-xs-1'))}}
				
						<div class="col-sm-4 col-lg-10">
							{{Form::textarea('motivo', '', array('id' => 'id_motivo', 'class'=>"form-control input-medium"))}}
						</div>
					
				</div>
				
				<div class="col-sm-10">
						<input type="submit" id="btn-consultar-permiso" name="btn-consultar-permiso" value="Rechazar" class="btn-primary">

						<input type="button" id="btn-consultar-permiso" name="btn-consultar-permiso" value="Volver" class="btn-primary" onclick="consultar();">
				</div>
			{{Form::close()}}
		@endif	
		
			
		
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





