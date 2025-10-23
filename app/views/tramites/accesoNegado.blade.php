@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/js-click.js", array("type" => "text/javascript"))}}	
@stop	
@section('contenido')
	<div class="row-border">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<h1 id="titulo_exit">Acceso No Permitido</h1>
			</div>
		</div>	
		<div class="row">
			<h2>{{$mensaje}}</h2>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<input type="button" id="btn_exit" name="btn_exit" value="Aceptar" class="btn-primary pull-right" onclick="portal();">				
			</div>
		</div>
		
	</div>	
	
@endsection






