@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/js-click.js", array("type" => "text/javascript"))}}
@stop
@section('css')
	{{HTML::style("css/ihover.css", array("type" => "text/css"))}}
	{{HTML::style("css/tablero.css", array("type" => "text/css"))}}	
	<link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
@stop
@section('contenido')
<?php $link_tablero = Config::get('habilitacion_config/config.urllink_tablero');?> 

<script>
$(document).ready(function(){
  $(".numero").click(function(event){
    event.preventDefault();
  });
});
</script>
<div class="row">
	<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
	    <div class="alert alert-danger fade in">
	      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
	    </div>
	</div>
	<div class="gestion_tramites">
		<div class="recuadroPermisionarios">
		  <div id='permisionarios' class="">
			<!-- normal -->
			<div class="ih-item square colored effect6 from_top_and_bottom per_iniciados"><a href="{{ url($link_tablero.'1')}}">
				<div class="img">
					<h4>Solicitados por Permisionarios</h4>		
					<p class="numero">{{$cantTramites['iniPer']}}</p>		
				</div>
				<div class="info">
				  <h3>Detalle :</h3>
					<div class="detalle">
					<?php
					foreach ($iniPer as $iniP){
							$descIniciadosPer = "<div class='filaDetalle'><p class='cantDetalle'>" .$iniP->cantidad . "</p><p>" . substr($iniP->nombre_tramite, 10) . "</p></div>";
						echo $descIniciadosPer;
					}
					?>  	
					</div>					
				</div></a>
			</div>
		  </div>

		  <div id='permisionarios' class="">	
			<!-- end normal -->
			 <!-- colored -->
			<div class="ih-item square colored effect6 from_top_and_bottom per_devueltos"><a href="{{ url($link_tablero.'2')}}">
				<div class="img">
					<h4>Devueltos por falta de documentación</h4>		
					<p class="numero">{{$cantTramites['devueltosDoc']}}</p>		
				</div>
				<div class="info">
				  <h3>Detalle :</h3>
					<div class="detalle">

					<?php
					foreach ($devueltosDoc as $devDoc){
							$descDevueltosDoc = "<div class='filaDetalle'><p class='cantDetalle'>" .$devDoc->cantidad . "</p><p>" . substr($devDoc->nombre_tramite, 10) . "</p></div>";
						echo $descDevueltosDoc;
					}
					?>  
					</div>
				</div></a></div>
			<!-- end colored -->
		  </div>	
		</div>  
		
		<!-- Bottom to top-->
		<div class="recuadroRecep">
		  <div id='recepcionRosario' class="">
			<!-- normal -->
			<div class="ih-item square colored effect6 from_top_and_bottom recepcion2"><a href="{{ url($link_tablero.'3')}}">
				<div class="img">
					<h4>Recepción Rosario</h4>	
					<p class="numero">{{$cantTramites['recepcionRos']}}</p>		
				</div>
				<div class="info">
				  <h3>Detalle :</h3>
					<div class="detalle">				  
					<?php
					foreach ($recepcionRos as $recepR){
							$descRecepcionRos = "<div class='filaDetalle'><p class='cantDetalle'>" .$recepR->cantidad . "</p><p>" . substr($recepR->nombre_tramite, 10) . "</p></div>";
						echo $descRecepcionRos;
					}
					?>
					</div>					
				</div></a>
			</div>
			<!-- end normal -->
		  </div> 
		  <div id='recepcionStaFe' class="">
			<!-- normal -->
			<div class="ih-item square colored effect6 from_top_and_bottom recepcion2"><a href="{{ url($link_tablero.'4')}}">
				<div class="img">
					<h4>Recepción Santa Fe</h4>	
					<p class="numero">{{$cantTramites['recepcionStaFe']}}</p>		
				</div>
				<div class="info">
				  <h3>Detalle :</h3>
					<div class="detalle">				  				  
					<?php
					foreach ($recepcionStaFe as $recepSF){
							$descRecepcionStaFe = "<div class='filaDetalle'><p class='cantDetalle'>" .$recepSF->cantidad . "</p><p>" . substr($recepSF->nombre_tramite, 10) . "</p></div>";
						echo $descRecepcionStaFe;
					}
					?>		
					</div>					
				</div></a>
			</div>
			<!-- end normal -->
		  </div> 
		</div>
		<!-- end Bottom to top-->

		<div class="recuadroHab">
			<div id='habilitacion' class=""> 
			<!-- normal -->
				<div class="ih-item square colored effect6 from_top_and_bottom habilit30"><a href="{{ url($link_tablero.'5')}}">
					<div class="img">
						<h4>En Habilitación < 30 ds </h4>	
						<p class="numero">{{$cantTramites['hab30']}}</p>		
					</div>
					<div class="info">
					  <h3>Detalle :</h3>
						<div class="detalle">				  				  					  
					<?php
					foreach ($hab30 as $h30){
							$descHab30 = "<div class='filaDetalle'><p class='cantDetalle'>" .$h30->cantidad . "</p><p>" . substr($h30->nombre_tramite, 10) . "</p></div>";
						echo $descHab30;
					}
					?>
						</div>
					</div></a>
				</div>
			</div>		
		<!-- end normal -->
		<!-- normal -->
			<div id='habilitacion' class=""> 	
				<div class="ih-item square colored effect6 from_top_and_bottom habilit3060"><a href="{{ url($link_tablero.'6')}}">
					<div class="img">
						<h4>En Habilitación 30-60 ds </h4>	
						<p class="numero">{{$cantTramites['hab3060']}}</p>		
					</div>
					<div class="info">
					  <h3>Detalle :</h3>
						<div class="detalle">					  
						<?php
						foreach ($hab3060 as $h3060){
							$descHab3060 = "<div class='filaDetalle'><p class='cantDetalle'>" .$h3060->cantidad . "</p><p>" . substr($h3060->nombre_tramite, 10) . "</p></div>";
							echo $descHab3060;
						}
						?>
						</div>
					</div></a>
				</div>
			</div>	
		<!-- end normal -->
		<!-- normal -->
			<div id='habilitacion' class=""> 	
				<div class="ih-item square colored effect6 from_top_and_bottom habilit60"><a href="{{ url($link_tablero.'7')}}">
					<div class="img">
						<h4>En Habilitación > 60 ds </h4>	
						<p class="numero">{{$cantTramites['hab60']}}</p>		
					</div>
					<div class="info">
					  <h3>Detalle :</h3>
						<div class="detalle">					  					  
						<?php
						foreach ($hab60 as $h60){
							$descHab60 = "<div class='filaDetalle'><p class='cantDetalle'>" .$h60->cantidad . "</p><p>" . substr($h60->nombre_tramite, 10) . "</p></div>";
							echo $descHab60;
						}
						?>
						</div>
					</div></a>
				</div>
			</div>	
		<!-- end normal -->	
		</div>
	  
		<!-- Bottom to top-->
		<div class="recuadroFirma">
		  <div id='firma_vice' class="">
			<!-- colored -->
			<div class="ih-item square colored effect6 from_top_and_bottom firma_vice"><a href="{{ url($link_tablero.'8')}}">
				<div class="img firma_vice">
					<h4>A la firma vicepresidencia </h4>		
					<p class="numero">{{$cantTramites['firmaVice']}}</p>		
				</div>
				<div class="info">
				  <h3>Detalle :</h3>
					<div class="detalle">					  					  				  
					<?php
					foreach ($firmaVice as $firmaV){
						$descFrimaVice = "<div class='filaDetalle'><p class='cantDetalle'>" .$firmaV->cantidad . "</p><p>" . substr($firmaV->nombre_tramite, 10) . "</p></div>";
						echo $descFrimaVice;
					}
					?>		
					</div>					
				</div></a>
			</div>
		  </div>
		</div>
		<!-- fin recuadro firma-->
		<!-- recuadro aprobados  -->
		<div id="aprobados" class="">
			<div id='aprobPend' class=""> 	
				<div class="ih-item square colored effect6 from_top_and_bottom aprobPend"><a href="{{ url($link_tablero.'9')}}">
					<div class="img">
						<h4>Aprobados Pendientes</h4>	
						<p class="numero">{{$cantTramites['aprobPend']}}</p>		
					</div>
					<div class="info">
					  <h3>Detalle :</h3>
						<div class="detalle">					  					  
						<?php
						foreach ($aprobPend as $ap){
							$descAP = "<div class='filaDetalle'><p class='cantDetalle'>" .$ap->cantidad . "</p><p>" . substr($ap->nombre_tramite, 10) . "</p></div>";
							echo $descAP;
						}
						?>
						</div>
					</div></a>
				</div>
			</div>	
		</div>
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
