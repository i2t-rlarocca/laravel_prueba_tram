@extends('layouts.base')
@section('javascript')
		{{HTML::script("js/jquery-ui.js", array("type" => "text/javascript"))}}
		{{HTML::script("js/js-datepicker.js", array("type" => "text/javascript"))}}
		{{HTML::script("js/js-click.js", array("type" => "text/javascript"))}}	
		{{HTML::script("js/js-consultapermiso.js?v=".rand(), array("type" => "text/javascript"))}}
		{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
		{{HTML::script("js/js-nuevotramite.js?v=".rand(), array("type" => "text/javascript"))}}
@stop
@section('css')
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
@stop
		
@section('contenido')
	<div class="row">
		<div class="col-lg-4">
			<h1>Nuevo Trámite</h1>	
		</div>
		<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		    </div>
		</div>
	</div>	
		</br>
	<div class="row-border">
		@if(Session::get('usuarioLogueado.nombreTipoUsuario')=='CAS' || Session::get('usuarioLogueado.nombreTipoUsuario')=='CAS_ROS')
			{{Form::open(array('action' => array('TramitesController@consultaPermiso'), 'method' => 'post', 'id' => 'formulario_permiso'))}} 
				<div class="form-group form-inline">
					{{Form::label('lpermiso', 'Permiso:', array('id'=>'id_lpermiso', 'class'=>'control-label col-xs-3'))}}
				</div>
				<div class="form-group form-horizontal">
					<div class="col-sm-6">
						{{Form::text('permiso', '', array('id' => 'id_permiso_nt', 'class'=>'form-control col-xs-3' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'data-placement'=>'right', 'title'=>'Permiso del agente/subagente destinatario del trámite', 'autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.btn_consultar_permiso.name; nombreForm=this.form.id;'))}} 
					</div>
				</div>
				<div class="col-sm-10">
					<input type="button" id="btn_consultar_permiso" name="btn_consultar_permiso" value="Consultar" class="btn-primary" onfocus='siguienteCampo = "nada";'>
					{{Form::button('Volver', array('id'=>'btn_volver', 'name'=>'btn_volver', 'class'=>'btn-primary', 'onclick'=>'volver();','onfocus'=>'siguienteCampo = "nada";'))}}
				</div>
				
			{{Form::close()}}
		@endif 
		{{Form::open(array('action' => array('TramitesController@irTramite'), 'method' => 'post', 'id' => 'formulario_tramite', 'class'=>'form-inline'))}} 
			
				<div id="divagentesubage" style="display: none">
					@if(!Session::has('usuarioTramite'))
						{{Form::hidden('permiso',Session::get('usuarioLogueado.permiso'),  array('id' => 'nro_permiso'))}}
						{{Form::hidden('agente',Session::get('usuarioLogueado.agente') ,  array('id' => 'agente'))}}
						{{Form::hidden('subagente',Session::get('usuarioLogueado.subAgente') ,  array('id' => 'subagente'))}}
						{{Form::hidden('titular',Session::get('usuarioLogueado.titular') ,  array('id' => 'titular'))}}
					@else
						{{Form::hidden('permiso',Session::get('usuarioTramite.permiso'),  array('id' => 'nro_permiso'))}}
						{{Form::hidden('agente',Session::get('usuarioTramite.agente'),  array('id' => 'agente'))}}
						{{Form::hidden('subagente',Session::get('usuarioTramite.subAgente'),  array('id' => 'subagente'))}}
						{{Form::hidden('titular',Session::get('usuarioTramite.titular'),  array('id' => 'titular'))}}
					@endif
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<BR/>	
						<div class="alert alert-warning" id="div_agsubag">
							{{Form::label('lagente', '', array('id'=>'id_agente','class'=>'control-label'))}}
						</div>
					</div>
					
					<div class="form-group">
						{{Form::label('ltramites', 'Trámite:', array('class'=>'control-label col-xs-3'))}}
						<div class="col-sm-7">
							{{Form::select('tramites', $combobox, '', array('id' => 'id_tramites', 'class'=>'form-control col-xs-3 col-lg-push-8', 'autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.btn_nuevo.name; nombreForm=this.form.id;'))}}
						</div>
					</div>
					<BR/>
					<div class="form-group">
						<div class="col-xs-12 col-lg-12">
							
							{{Form::button('Nuevo', array('id' => 'btn_nuevo', 'name'=>'btn_nuevo', 'class' => 'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}} 						
							{{Form::button('Limpiar', array('id'=>'btn_refrescar', 'method'=>'GET', 'name'=>'btn_refrescar', 'class'=>'btn-primary', 'onclick'=>'refrescar_cas()','onfocus'=>'siguienteCampo = "nada";'))}}
							{{Form::button('Volver', array('id'=>'btn_cancelar', 'name'=>'btn_cancelar', 'class'=>'btn-primary', 'onclick'=>'volver();','onfocus'=>'siguienteCampo = "nada";'))}}	
						
						</div>
					</div>	
				
				</div>{{-- fin usuario CAS/CAS_ROS--}}	
			@if(Session::get('usuarioLogueado.nombreTipoUsuario')!='CAS' && Session::get('usuarioLogueado.nombreTipoUsuario')!='CAS_ROS')<!-- cualquier usuario -->
			{{Form::hidden('subagente',Session::get('usuarioLogueado.subAgente'),  array('id' => 'subagente_agente'))}}
				<div class="form-group form-inline">
          {{Form::label('ltramites', 'Trámite:', array('class'=>'control-label col-xs-3'))}}
          <div class="col-sm-8">
            {{Form::select('tramites', $combobox,'' ,array('id' => 'id_tramites_agente', 'class'=>'form-control col-xs-3 col-lg-push-9','autofocus'=>"",'onfocus'=>'siguienteCampo="btn_nuevo_agente"; nombreForm=this.form.id;'))}}
          </div>
				<BR/>
					<div class="col-xs-12 col-lg-12">
						{{Form::button('Nuevo', array('id' => 'btn_nuevo_agente', 'name'=>'btn_nuevo_agente', 'class' => 'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}} 
					</div>
				</div>		
			@endif
		{{Form::close()}}
		{{Form::hidden('tipo_maquina','',array('id'=>'tipo_maquina'))}}
		{{Form::hidden('observaciones_maq','',array('id'=>'observaciones_maq'))}}
		{{Form::hidden('motivo_baja','',array('id'=>'motivo_baja'))}}
		{{Form::hidden('observaciones_baja','',array('id'=>'observaciones_baja'))}}
		{{Form::hidden('listaMotivos',$listaMotivos,array('id'=>'listaMotivos'))}}
	</div>	
@endsection

@section('contenido_modal')
	<div id="terminalModal" class="modal fade" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content" id="modal-content">
				 <div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button id="btn_incorporar" type="button" class="btn btn-default">Incorporar</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
	<div id="terminalRetiroModal" class="modal fade" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content" id="modal-content">
				 <div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button id="btn_retirar" type="button" class="btn btn-default">Retirar</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
	<div id="bajaModal" class="modal fade" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content" id="modal-content">
				<div class="modal-header" id='hmodal'> 
					<div class="col">
						<h1 class="modal-title">Solicitud de Baja de Permiso</h1>
					</div>
				</div>
				 <div class="modal-body" id='bmodal'>
					<div class="row-border">
						<div align="center">
						{{Form::open(array('action' => array('TramitesController@bajaPermiso'), 'method' => 'post', 'id' => 'formulario_baja_permiso','class'=>'formulario_baja_permiso'))}}
								<div class="form-group col-lg-12">
									<div id="label1">
										<label for="lmotivobaja" id="lmotivobaja" class="control-label col-lg-12">Motivo:</label>
									</div>
									<div class="col-sm-7" id="select_motivo">
										{{Form::select('motivo', $combobox, '', array('id' => 'id_motivos', 'class'=>'form-control col-xs-3'))}}
									</div>
								</div>
								<div class="form-group col-lg-12">
									<div id="label2">
										<label for="l_cant_permiso" id="l_cant_permiso" class="control-label col-lg-12">Observaciones:</label>
									</div>
							 		<div class="col-lg-10" id="div_motivo_baja_modal">
							 			{{Form::textarea('observaciones_baja', '', array('id' => 'observaciones_baja', 'class'=>"form-control input-medium",'rows'=>'5','maxlength'=>'255' ,'onfocus'=>'siguienteCampo = "btn_sol_baja";nombreForm=this.form.id'))}}
									</div>
								</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button id="btn_sol_baja" type="button" class="btn btn-default">Aceptar</button>
					{{Form::close()}}
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>

	<div id="renunciaModal" class="modal fade" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content" id="modal-content">
				<div class="modal-header" id='hmodal'> 
					<div class="col">
						<h1 class="modal-title">Solicitud de Renuncia</h1>
					</div>
				</div>
				 <div class="modal-body" id='bmodal'>
					<div class="row-border">
						<div align="center">
						{{Form::open(array('action' => array('TramitesController@renunciaPermiso'), 'method' => 'post', 'id' => 'formulario_renuncia_permiso','class'=>'formulario_renuncia_permiso'))}}
								<div class="form-group col-lg-12">
									<div id="label1">
										<label for="lmotivobaja" id="lmotivobaja" class="control-label col-lg-12">Trámite:</label>
									</div>
									<div class="col-sm-7" id="select_motivo">
										{{Form::text('motivo','Renuncia', array('id' => 'id_motivos_renuncia', 'class'=>'form-control col-xs-3','readonly'))}}
										{{Form::hidden('motivo_ren','6', array('id' => 'motivo_ren'))}}
									</div>
								</div>
								<div class="form-group col-sm-12 col-lg-12">
									<div id="label2">
										<label for="l_cant_permiso" id="l_cant_permiso" class="control-label col-lg-12">Motivo:</label>
									</div>
							 		<div class="col-lg-10" id="div_motivo_baja_modal">
							 			{{Form::textarea('observaciones_renuncia', '', array('id' => 'observaciones_renuncia', 'class'=>"form-control input-medium",'rows'=>'5','maxlength'=>'255' ,'onfocus'=>'siguienteCampo = "btn_sol_baja";nombreForm=this.form.id'))}}
									</div>
								</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button id="btn_sol_renuncia" type="button" class="btn btn-default">Aceptar</button>
					{{Form::close()}}
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
	
	<div id="habilitacionModal" class="modal fade" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content" id="modal-content">
				<div class="modal-header" id='hmodal'> 
					<div class="col">
						<h1 class="modal-title">Solicitud de habilitación de Permiso</h1>
				</div>			
			</div>
				 <div class="modal-body" id='bmodal'>
					<div class="row-border">
						<div align="center">
						{{Form::open(array('action' => array('TramitesController@habilitarPermiso'), 'method' => 'post', 'id' => 'formulario_habilitacion_permiso','class'=>'formulario_habilitacion_permiso'))}}
								<div class="form-group col-xs-12 col-md-12">
									<div id="labelFec">
										<label for="lfdesde" id="lbfechad" class="control-label col-xs-12 col-sm-12">Fecha Desde:</label>
		</div>
									<div class="col-xs-4 col-sm-4 div_sin_padding" style="margin: 0 auto; float: none;">
										{{Form::text('fecha_desde', '', array('id' => 'fecha_desde','class' => 'datepicker form-control', 'maxlength' => '10','pattern'=>"\d{1,2}/\d{1,2}/\d{4}",'placeholder'=>'dd/mm/aaaa','onfocus'=>'siguienteCampo="observaciones_hab"; nombreForm=this.form.id'))}}
					</div>
					</div>
								<div class="form-group col-lg-12">
									<div id="labelobs">
										<label for="labelobs" id="lobs" class="control-label col-lg-12">Observaciones:</label>
				</div>
							 		<div class="col-lg-10" id="div_motivo_baja_modal">
							 			{{Form::textarea('observaciones_hab', '', array('id' => 'observaciones_hab', 'class'=>"form-control input-medium",'rows'=>'5','maxlength'=>'255' ,'onfocus'=>'siguienteCampo = "btn_sol_hab";nombreForm=this.form.id'))}}
			</div>
		</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button id="btn_sol_hab" type="button" class="btn btn-default">Aceptar</button>
					{{Form::close()}}
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>

	<div id="suspensionModal" class="modal fade" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content" id="modal-content">
				<div class="modal-header" id='hmodal'> 
					<div class="col">
						<h1 class="modal-title">Solicitud de suspensión de Permiso</h1>
					</div>
				</div>
				 <div class="modal-body" id='bmodal'>
					<div class="row-border">
						<div align="center">
						{{Form::open(array('action' => array('TramitesController@suspenderPermiso'), 'method' => 'post', 'id' => 'formulario_suspension_permiso','class'=>'formulario_suspension_permiso'))}}
								<div class="form-group col-xs-12 col-md-12">
									<div id="labelFec">
										<label for="lfdesde" id="lbfechad" class="control-label col-xs-12 col-sm-12">Fecha Desde:</label>
									</div>				
									<div class="col-xs-4 col-sm-4 div_sin_padding" style="margin: 0 auto; float: none;">
										{{Form::text('fecha_hasta', '', array('id' => 'fecha_hasta','class' => 'datepicker form-control', 'maxlength' => '10','pattern'=>"\d{1,2}/\d{1,2}/\d{4}",'placeholder'=>'dd/mm/aaaa','onfocus'=>'siguienteCampo="observaciones_susp"; nombreForm=this.form.id'))}}
									</div>
								</div>
								<div class="form-group col-lg-12">
									<div id="labelobs">
										<label for="labelobs" id="lobs" class="control-label col-lg-12">Observaciones:</label>
									</div>
							 		<div class="col-lg-10" id="div_motivo_baja_modal">
							 			{{Form::textarea('observaciones_susp', '', array('id' => 'observaciones_susp', 'class'=>"form-control input-medium",'rows'=>'5','maxlength'=>'255' ,'onfocus'=>'siguienteCampo = "btn_sol_susp";nombreForm=this.form.id'))}}
									</div>
								</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button id="btn_sol_susp" type="button" class="btn btn-default">Aceptar</button>
					{{Form::close()}}
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>

	<div id="cargandoModal" class="modal fade" role="dialog" aria-hidden="true" data-keyboard="false">
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





