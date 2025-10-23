@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/js-ingresos.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
@stop

@section('contenido')
	<div class="row">
		<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		    </div>
		</div>
		<div class="col">
			<h1>Ingreso</h1>	
		</div>
	</div>	
		</br>
	<div class="row-border">
	{{Form::open(array('action' => array('TramitesController@cargaPermiso_add'), 'method' => 'post', 'id' => 'formulario-ingreso', 'class'=>'form-horizontal'))}} 
		<div class="form-group col-sm-12">
				<strong>
					@if(Session::has('usuarioLogueado'))
						{{Form::label('lagente', 'Agente: '.Session::get('usuarioLogueado.agente')."/".Session::get('usuarioLogueado.subAgente')." - ".Session::get('usuarioLogueado.apellido'), array('class'=>'control-label '))}}
					@else
						{{Form::label('lagente', "Sin identificación", array('class'=>'control-label col-xs-4'))}}
					@endif
				</strong>
		</div>
		<BR/>
		<BR/>
		<div class="form-group col-sm-12">
			{{Form::label('lemail', 'Mail:', array('class'=>'control-label col-xs-2'))}} 

			<div class="col-sm-8">
				{{Form::text('email',Session::get('usuarioLogueado.email') , array('id' => 'email', 'class'=>'form-control col-xs-8','maxlength' => '150', 'readonly'=>'true', 'autofocus'=>"",'onfocus'=>'siguienteCampo = "btn_ingresar"; nombreForm=this.form.id;', 'autocomplete'=>'off'))}}
			</div>
<!--
			<div class="col-sm-8">
				{{Form::text('email2','adrian.enrico@i2t.com.ar', array('id' => 'email2', 'class'=>'form-control col-xs-8','maxlength' => '150','autofocus'=>"",'onfocus'=>'siguienteCampo = "btn_ingresar"; nombreForm=this.form.id;', 'autocomplete'=>'off'))}}
			</div>
-->			


		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			{{Form::button('Ingresar', array('id' => 'btn_ingresar', 'name'=>'btn_ingresar', 'class' => 'btn-primary'))}} 
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





