@extends('layouts.base')
@section('javascript')
	{{--HTML::script("js/js-datepicker.js", array("type" => "text/javascript"))--}}
	{{HTML::script("js/js-motivo.js", array("type" => "text/javascript"))}}
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
			<h1>{{ $tituloTramite }}</h1>	
		</div>
	</div>	
	</br>
	{{Form::open(array('action' => array('TramitesController@'.$accion), 'method' => 'post', 'id' => 'formulario-motivo'))}} 
		
		{{Form::hidden('nopcion', Session::get('nro_tramite'),array('id' => 'id_nopcion'))}}
	<div class="row-border">
		<div class="form-group form-inline">
			<div class="col-xs-2 col-sm-3 col-md-2 col-lg-1">
				{{Form::label('lmotivo', 'Motivo:', array('class'=>'control-label'))}}
			</div>
			<div class="col-xs-10 col-sm-6 col-md-6 col-lg-10">
				{{Form::textarea('motivo', '', array('id' => 'id_motivo', 'class'=>"form-control"))}}
			</div>
		</div>
		<br/>
		<div id="botones" class="form-group form-inline">
			<BR/>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				{{Form::submit('Ingresar', array('id' => 'btn-iniciar', 'name'=>'btn-iniciar', 'class' => 'btn-primary'))}} 
		
				{{Form::button('Volver', array('id'=>'btn-cancelar', 'name'=>'btn-cancelar', 'class'=>'btn-primary', 'onclick'=>'volver2();'))}}			
			
				{{Form::button('Limpiar', array('id'=>'btn_refrescar_motivo', 'method'=>'GET', 'name'=>'btn_refrescar_motivo', 'class'=>'btn-primary', 'onclick'=>'refrescar()'))}}
			</div>
		</div>
	</div>	
	{{Form::close()}}
@endsection






