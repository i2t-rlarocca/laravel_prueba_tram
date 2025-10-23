@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/jquery-ui.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-click.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-bajacotitular.js?v=" . rand(), array("type" => "text/javascript"))}}
@stop
@section('css')
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
@stop
@section('contenido')
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
	<br>

	<div class="row-border">
		<div class="row">
			{{Form::open(array(
				'action' => array('TramitesController@bajaCotitular_add'),
				'method' => 'post',
				'id' => 'formulario-baja-cotitular',
				'class'=>'form-horizontal'))}}
			{{Form::hidden('nombre_tipo_tramite','BC',  array('id' => 'nombre_tipo_tramite'))}}
			@include('tramites/datosPermiso', ['datos' => $datos])

			@if(empty($cotitulares))
				<div id="failure" class="bs-example col-lg-6 col-lg-offset-1">
					<div class="alert alert-danger fade in">
						{{Form::label('mensaje', 'El permiso no cuenta con cotitulares activos.')}}
					</div>
					<div class="col-sm-12">
					{{Form::button(
						'Volver',
						array(
							'id'=>'btn_volver',
							'name'=>'btn_volver',
							'class'=>'btn-primary',
							'onclick'=>'volver2();',
							'onfocus'=>'siguienteCampo = "nada";'))}}
					</div>
				</div>
			@else
				<div class="alert alert-warning col-sm-12" id="baja_cotitular">
					<h4>{{Form::label('lbaja_cotitular', 'Seleccione Cotitular a dar de baja', array('id'=>'lbaja_cotitular','class'=>'text-center col-xs-12 col-lg-12'))}}</h4>
					<table class="table">
						<thead>
								<tr>
									<th></th>
									<th> Apellido y Nombres </th>
									<th> DNI/CUIT </TH>
									<th> Sexo </th>
									<th> Fecha de nacimiento</th>
								</tr>
						</thead>
						<tbody>
								 @foreach($cotitulares as $person)
									<tr>
										<td> {{ Form::radio('cotitular_to_kill', $person->id, false) }} </td>
										<td> {{$person->name}} </td>
										<td> {{$person->dni_cuit_titular_c}} </td>
										<td> {{$person->sexo_c}} </td>
										<td> {{$person->fecha_nacimiento_c}} </td>
									</tr>
									@endforeach
						</tbody>
					</table>

						<div class="form-group">
							{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambiot','class'=>'control-label col-xs-1 col-lg-3'))}}
							<div class="col-sm-11 col-lg-9">
								{{Form::textarea('motivo_ct', '', array(
									'id' => 'motivo_ct',
									'class'=>"form-control input-medium",
									'maxlength'=>'255'))}}
							</div>
						</div>

				</div>
				<div class="col-sm-12">
					{{Form::button(
						'Ingresar',
						array(
							'id' => 'btn-baja-cotitular',
							'name'=>'btn-baja-cotitular',
							'class' => 'btn-primary',
							'onclick'=>'dar_de_baja_cotitular();',
							'onfocus'=>'siguienteCampo = "nada";'))}}
					{{Form::button(
						'Volver',
						array(
							'id'=>'btn_volver',
							'name'=>'btn_volver',
							'class'=>'btn-primary',
							'onclick'=>'volver2();',
							'onfocus'=>'siguienteCampo = "nada";'))}}
					{{Form::button(
						'Limpiar',
						array(
							'id'=>'btn-limpiar',
							'name'=>'btn-limpiar',
							'class'=>'btn-primary',
							'onclick'=>'refrescar(this.form.id);',
							'onfocus'=>'siguienteCampo = "nada";'))}}
				</div>
				{{ Form::close() }}
			@endif
		</div>
	</div>
@endsection

@section('contenido_modal')
	<div id="cargandoModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-sm">
			<div class="modal-content" id="modal-content">
				 <div class="modal-body" align="center">
				 	<h3>Cargando...</h3>
				    <img src="../public/images/cargando.gif" id="loading-indicator" />
				</div>
			</div>
		</div>
	</div >
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
