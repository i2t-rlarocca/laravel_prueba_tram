@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/js-cancelarPorNroTramite.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
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
			<h1>Cancelar Trámite</h1>	
		</div>
	</div>	
		<br/>
	
		<div class="row-border">
			{{Form::open(array('action' => array('TramitesController@cancelarTramite_add'), 'method' => 'post', 'id' => 'formulario_cancelarTramite', 'class'=>'form-horizontal'))}} 
				<div class="form-group">
					{{Form::label('lntramite', 'Nº Seguimiento ', array('class'=>'control-label col-xs-3'))}}
					<div class="col-sm-5">
					{{Form::text('ntramite', '', array('id' => 'id_tramite','class'=>'form-control col-xs-3', 'autofocus'=>'','onfocus'=>'siguienteCampo="btn_aceptar_cancelar"; nombreForm=this.form.id;'))}}
					</div>	
				</div>
				<br/>
				<div class="form-group col-xs-12">
					{{Form::button('Aceptar', array('id' => 'btn_aceptar_cancelar', 'name'=>'btn_aceptar_cancelar', 'class' => 'btn-primary','onfocus'=>'siguienteCampo="nada"; nombreForm=this.form.id;'))}} 
				
					{{Form::button('Volver', array('id' => 'btn-volver', 'name'=>'btn-volver', 'class' => 'btn-primary', 'onclick'=>'consultar();'))}} 
				</div>
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





