@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/js-click.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-exitoplan.js", array("type" => "text/javascript"))}}
	@stop	
@section('contenido')
	<div class="row">
		<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		    </div>
		</div>
		<div class="col">
			<h1>Plan exitosamente modificado</h1>	
		</div>
		{{Form::open(array('action' => array($accion), 'method' => $metodo, 'id' => 'formulario_nroTramite'))}} 
			{{Form::hidden('nroTramite',$nroTramite,array('id'=>'nroTramite'))}}
		{{Form::close()}}
	</div>	
	</br>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<input type="button" id="btn_exit" name="btn_exit" value="Aceptar" class="btn-primary pull-right">				
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





