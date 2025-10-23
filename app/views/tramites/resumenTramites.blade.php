@extends('layouts.base')
@section('javascript')
{{HTML::script("js/js-click.js", array("type" => "text/javascript"))}}

<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" language="JavaScript">// <![CDATA[
$(document).ready( function () {
	$('#frame-tablero-9').load( function () {
		$(this).contents().find(".jrPage").css({'background-color':'#77808A'});
	});
});
// ]]></script>

@stop

@section('contenido')
<?php $link_tablero = Config::get('habilitacion_config/config.urllink_tablero');?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>

	<div class="row">
		<div class="gestion_tramites" >

			<div class="recuadroFinal" >		
				<a href="{{ url($link_tablero.'9')}}">			
					<div id='cuadroResumen' class="">		
						<div id='inner' class="cuadroResumen">	
							<h4>Resumen por tipo de tramite (Ingresados en CAS)</h4>									
							<iframe id="frame-tablero-9" name="frame" src="{{$url_repositorio}}" frameborder="0" hspace="0" vspace="0"> aqui</iframe>			
						</div>
					</div>
				</a>				
			</div>				
		</div>	
	</div>	

@endsection

