<div class="modal-header" id='hmodal'> 
	{{HTML::script("js/js-historialEstadosTramite.js?var=".rand() , array("type" => "text/javascript"))}}
	<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		<div class="alert alert-danger fade in">
			{{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		</div>
	</div>
	<div class="col">
		<h1 class="modal-title">Historial de estados del trámite - {{$tramite['nombre_tramite']}} </h1>
	</div>
</div>
 <div class="modal-body" id='bmodal'>
	<div class="row-border">
		<!-- Campos comunes a todos los trámites-->
			<div class="form-group form-inline">
				<label class="control-label col-xs-3 col-lg-3" id="nro_seguimiento">Nº Seguimiento:</label> 
				<input id="nro_tramite" type="text" class="form-control input-sm col-lg-push-6" value={{$tramite['nro_tramite']}} maxlenght="15" disabled>
			</div>
			
			<div class="form-group form-inline">
				<label id="label_nro_permiso" class="col-xs-3 col-lg-3">Permiso:</label>
				<input id="nro_permiso" type="text" class="form-control input-sm col-lg-push-3" value={{$tramite['nro_permiso']}} disabled> 
			</div>
			<div class="form-group form-inline">
				<label id="agente" class="col-xs-3 col-lg-3">Agente:</label>
				<input type="text" class="form-control input-sm col-lg-3" value={{$tramite['agente']}}  disabled>
		
				<label id="subagente_m" class="col-lg-2">Sub Agente:</label>
				<input type="text" class="form-control input-sm col-md-offset-1 col-lg-push-8" value={{$tramite['subagente']}} disabled>
			</div>
			
		{{Form::open(array('action' => array('TramitesController@historialEstadoTramite'), 'method' => 'post', 'id' => 'formulario-historial','class'=>'formulario-historial'))}} 			{{Form::hidden('usu_log', $usu, array('id'=>'usu_log'))}}								
			{{Form::hidden('nroTramite',$tramite['nro_tramite'],  array('id' => 'nroTramite'))}}
			
		{{Form::close()}}	
		
	
		<div id="historial">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<BR/>
				    <div class="table-responsive">
						<table class="table table-bordered table-condensed" id="tabla-tramites"> 
							<thead class="encabezados">
								<tr id="fixed-row">
									<th class="col-lg-2">Fecha</th>
									<th class="col-lg-2">Estado Anterior</th>
									<th class="col-lg-2">Estado Final</th>
									<th class="col-lg-1">Usuario</th>
									<th class="col-lg-3">Motivo</th>
								</tr>
							</thead>
							<tfoot class="pie" id="pie"></tfoot>
							<tbody id="cuerpo" class="cuerpo">				
							</tbody>
				   		</table>
				    </div>
			</div>
		</div>
		
		
		
	</div>
</div>

<div class="modal-footer" id="fmodal">
	<div class="col-xs-12 col-sm-12 col-md-10 col-lg-offset-10 col-lg-1">
		<BR/>
    	<button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
   </div>
</div>
<div id="sesionModal_m" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
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
