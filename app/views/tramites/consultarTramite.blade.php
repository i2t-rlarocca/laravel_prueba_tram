@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/jquery-ui.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-datepicker.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-basicos.js" .'?vari='. rand(), array("type" => "text/javascript"))}} 
	{{HTML::script("js/js-consultatramite.js" .'?vari='. rand(), array("type" => "text/javascript"))}}
	{{HTML::script("js/js-filtrosconsultatramite.js" .'?vari='. rand(), array("type" =>"text/javascript"))}}
	{{HTML::script("js/js-cancelar.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-botonesconsultar.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-validacuit.js", array("type" => "text/javascript"))}}
	{{HTML::script('js/js-session.js', array('type'=>'text/javascript'))}}
@stop	
@section('css')

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
{{HTML::style("css/dropzone.css", array("type" => "text/css"))}}		
	{{HTML::style("css/basic.css", array("type" => "text/css"))}}		
	{{HTML::style("css/uploadfile.css", array("type" => "text/css"))}}
@stop
@section('contenido')

	{{---------------------------------------Filtros para consulta----------------------------------}}
	
		<!-- filtros -->
<div class="row">
	<div class="col-xs-4 col-xs-offset-5 col-md-2 col-md-offset-5" id="div_solapa">
		<a id="solapa-toggle-vertical" href="#" class="toggle" accesskey="i" title="Alt+i">
			<i class="glyphicon glyphicon-chevron-down">Filtros</i>
		</a>
	</div>	
</div>

	<div id="sidebar-wrapper-vertical">
	  <ul class="sidebar-nav-vertical">
		{{Form::open(array('action' => array('TramitesController@consultarTramite_add'), 'method' => 'post', 'id' => 'formulario-tramite','class'=>'formulario-tramite'))}} 
			{{Form::hidden('htramitest','',  array('id' => 'id_htramitest'))}}
			{{Form::hidden('htramitesv','',  array('id' => 'id_htramitesv'))}}		
			{{Form::hidden('hestadost','',  array('id' => 'id_hestadost'))}}
			{{Form::hidden('hestadosv','',  array('id' => 'id_hestadosv'))}}
			{{Form::hidden('mesa_entrada','',  array('id' => 'mesa_entrada'))}}
			{{Form::hidden('ddv_nro','',  array('id' => 'ddv_nro'))}}
			@if(Session::has("abrirmodal"))
				{{Form::hidden('abrirmodal',Session::get("abrirmodal"),  array('id' => 'abrirmodal'))}}
			@else
				{{Form::hidden('abrirmodal','',  array('id' => 'abrirmodal'))}}
			@endif
			<div class="container-fluid">
				
					<div class="form-group">
						<div class="col-xs-12 col-md-8 div_sin_padding">
							{{Form::label('ltramite', 'Trámites', array('id'=>'lbtramites', 'class'=>'control-label col-xs-4 col-sm-3'))}}
							<div class="col-xs-8 col-sm-8 div_sin_padding">
								{{Form::select('tramites', $tramitesbox, '12', array('id' => 'id_tramites_c', 'class'=>'form-control','size'=>count($tramitesbox), 'multiple' => true, 'onfocus'=>'siguienteCampo="fecha_desde"; nombreForm=this.form.id'))}}
							</div>
							<div class="col-xs-12 col-md-12 div_sin_padding">
								<label for="lfdesde" id="lbfechad" class="control-label col-xs-4 col-sm-3">Fecha Desde</label>				
								<div class="col-xs-3 col-sm-3 div_sin_padding">
									{{Form::text('fecha_desde', '', array('id' => 'fecha_desde','class' => 'datepicker form-control', 'maxlength' => '10','readonly' => 'readonly','onfocus'=>'siguienteCampo="fecha_hasta"; nombreForm=this.form.id'))}}
								</div>
								<label for="lfhasta" id="lbfechah" class="control-label col-xs-2 col-sm-2">Hasta:</label>				
								<div class="col-xs-3 col-sm-3 div_sin_padding">
									{{Form::text('fecha_hasta', '', array('id' => 'fecha_hasta','class' => 'datepicker form-control', 'maxlength' => '10','readonly' => 'readonly','onfocus'=>'siguienteCampo="id_permiso"; nombreForm=this.form.id'))}}
								</div>
							</div>
							
							@if(Session::get('usuarioLogueado.nombreTipoUsuario')=='CAS' || Session::get('usuarioLogueado.nombreTipoUsuario')=='CAS_ROS')
								<div class="col-xs-12 col-md-12 div_sin_padding">
									<label for="lpermiso" id="lbpermiso" class="control-label col-xs-4 col-sm-3">Permiso</label>						
									<div class="col-xs-8 col-sm-2 div_sin_padding">
										{{Form::text('permiso', '', array('id' => 'id_permiso', 'class'=>'form-control col-xs-3', 'maxlength' => '5','onfocus'=>'siguienteCampo="id_agente"; nombreForm=this.form.id'))}}
									</div>								
									<label for="lagente" id="lbagente" class="control-label col-xs-4 col-sm-2">Ag./SubAg.</label>					
									<div class="col-xs-2 col-sm-2 div_sin_padding">
										<input id="id_agente" class="form-control col-xs-2 col-xs-3 text-right" maxlength="5" name="agente" type="text" value="" onfocus='siguienteCampo="id_subagente"; nombreForm=this.form.id'>									
									</div>
									<label for="barra" id="barra" class="control-label col-xs-1 col-sm-1">/</label>					<div class="col-xs-1 col-sm-1 div_sin_padding">
										<input id="id_subagente" class="form-control col-xs-3 text-right" maxlength="4" name="subagente" type="text" value="" onfocus='siguienteCampo="ordenar_"; nombreForm=this.form.id'>									</div>	
								</div>
							@endif
														
							<div class="col-xs-12 col-md-12 div_sin_padding">
								<label for="lordenar" id="lbordenar" class="control-label col-xs-4 col-sm-4 col-md-3">Ordenar por:</label>
								<div class="col-xs-8 col-sm-6 col-md-3 div_sin_padding">
									{{Form::select('ordenar', $ordenamiento, '0', array('id' => 'ordenar_', 'class'=>'form-control', 'onfocus'=>'siguienteCampo="n_paginar"; nombreForm=this.form.id'))}}
								</div>
								<div class="col-xs-12 col-md-5 div_sin_padding">
									<label for="paginar" id="lpaginar" class="control-label col-xs-4 col-sm-4 col-md-8">Nº por pág.</label>					<div class="col-xs-4 col-sm-4 col-md-4 div_sin_padding">
										{{Form::select('paginar', $paginacion,'5', array('id' => 'n_paginar', 'class'=>'form-control col-xs-3','onfocus'=>'siguienteCampo="combo_mesa_entrada"; nombreForm=this.form.id'))}}
									</div>
								</div>
								
							</div>
							<div class="col-xs-12 col-md-12 div_sin_padding" id="mesa" style="display:none">
								<label for="lingresadoen" id="lingresadoen" class="control-label col-xs-4 col-sm-4 col-md-3">Ingresado en:</label>				
								<div class="col-xs-8 col-sm-5 col-md-3 div_sin_padding">
									{{Form::select('combo_mesa_entrada', $mesa_entrada,'0', array('id' => 'combo_mesa_entrada', 'class'=>'form-control', 'onfocus'=>'siguienteCampo="id_localidad"; nombreForm=this.form.id'))}}
								</div>
							</div>	
							<div class="col-xs-12 col-md-12 div_sin_padding" >
								<label for="llocalidad" id="llocalidad" class="control-label col-xs-4 col-sm-3">Localidad</label>						
								<div class="col-xs-8 col-sm-4 col-md-4 div_sin_padding">
									{{Form::text('localidad', '', array('id' => 'id_localidad', 'class'=>'form-control col-xs-3', 'maxlength' => '26','onfocus'=>'siguienteCampo="id_estados"; nombreForm=this.form.id'))}}
								</div>	
							</div>	
					</div><!-- fin columna 1 -->

					<div class="col-xs-12 col-md-4 div_sin_padding">
						{{Form::label('lestado', 'Estados',array('id'=>'lbestados', 'class'=>'control-label col-xs-4 col-sm-3'))}}
						<div class="col-xs-6 col-sm-6 col-md-4 div_sin_padding" >
							{{Form::select('estados', $combobox,'', array('id' => 'id_estados', 'class'=>'form-control','size'=>count($combobox), 'multiple' => true, 'onfocus'=>'siguienteCampo="btn_consultar"; nombreForm=this.form.id'))}}
						</div>
					</div><!-- fin columna 2 -->
					<div class="col-xs-12 col-md-6 div_botones">
						{{Form::hidden('nro_pagina',$nro_pagina,  array('id' => 'nro_pagina', 'class'=>'form-control'))}}	
						{{Form::hidden('press-btn',0,  array('id' => 'press-btn'))}}
						<input type="button" id="btn_consultar" name="btn_consultar" value="Consultar" class="btn-primary" onfocus='siguienteCampo="fin"; nombreForm=this.form.id'>
						{{Form::button('Limpiar', array('id'=>'btn_refrescar_c', 'method'=>'GET', 'name'=>'btn_refrescar_c', 'class'=>'btn-primary', 'onclick'=>'refrescar();'))}}	
					</div>
					
				</div>	<!-- form-group -->		
			</div><!-- container-fluid -->			


		{{Form::close()}}
	  </ul>
	</div>

	<div id="errores" class="bs-example" style="display: none">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		    </div>
	</div>
	
	<div id="exito" class="bs-example" style="display: none">
		    <div class="alert alert-success">
		    	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
		      	{{Form::label('mensaje_ex', '', array('id'=>'mensaje_ex'))}}
		    </div>
	</div>


	<h1>Consulta de Trámites</h1> 
	
{{---------------------------------Tabla Trámites ------------------------------------------}}	
	<!--div id="botones_consulta"-->
	<div class="col-sm-12">
		<nav class="navbar navbar-default" role="navigation" id="barra_consulta">
			<div class="container-fluid">
				<div class="navbar-header">
					<!-- Brand and toggle get grouped for better mobile display -->
			      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			        <span class="sr-only">cambiar navegacion</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			      </button>
			    </div>
			    <div class="collapse navbar-collapse navbar-exl-collapse" id="bs-example-navbar-collapse-1">
			<div class="btn-toolbar" role="toolbar" id="barra_btnes">
				<div class="btn-group btn-group-lg col-xs-12 col-xs-push-1 col-sm-13 col-md-12 col-lg-12 ">
					{{Form::open(array('action' => array('TramitesController@detalleModal'), 'method' => 'post', 'id' => 'formulario-seleccionado','class'=>'formulario-seleccionado'))}} 
					
						{{Form::hidden('id_seleccionado','',  array('id' => 'id_seleccionado'))}}
			        	<button id="btn-cargar-detalle" type="button" class="btn btn-default col-xs-3 col-sm-3 col-md-1 col-lg-1">Detalle</button>
			        {{Form::close()}}
					{{Form::open(array('action' => array('TramitesController@completarTramite'), 'method' => 'post', 'id' => 'formulario_completar_tramite','class'=>'formulario_completar_tramite'))}}
			        	<button id="btn_completar_tramite" title="Cargar datos" type="button" class="btn btn-default col-xs-3 col-sm-2 col-md-2 col-lg-2" style="display: none;">Completar Trámite</button>
			        {{Form::close()}}
		        	
					
					{{Form::open(array('action' => array('TramitesController@historialTramiteConsulta'), 'method' => 'post', 'id' => 'formulario-seleccionado-h','class'=>'formulario-seleccionado-h'))}} 
							{{Form::hidden('id_seleccionado_h','',  array('id' => 'id_seleccionado_h'))}}							
							<button id="btn-historial-tramite" type="button" title="Historial Trámite" class="btn btn-default col-xs-3 col-sm-3 col-md-2 col-lg-2">Historial</button>
					{{Form::close()}}
			        {{Form::open(array('action' => array('TramitesController@listarTramites'), 'method' => 'post', 'id' => 'formulario-nuevo-tramite','class'=>'formulario-nuevo-tramite'))}}
							<button id="btn-nuevo-tramite" title="Nuevo Trámite" type="button" class="btn btn-default col-xs-2 col-sm-3 col-md-1 col-lg-1">Nuevo</button>
					{{Form::close()}}
			        {{Form::open(array('action' => array('TramitesController@informeTramite'), 'method' => 'post', 'id' => 'formulario-impresion','class'=>'formulario-impresion'))}}
			        	<button id="btn-imprimir" title="Imprimir consulta en PDF" type="button" class="btn btn-default col-xs-6 col-sm-4 col-md-3 col-lg-2" style="visibility:hidden">Imprimir Consulta</button>
			        {{Form::close()}}
			        {{Form::open(array('action' => array('TramitesController@csv'), 'method' => 'post', 'id' => 'formulario-csv','class'=>'formulario-csv'))}}

			        	<button id="btn-csv" title="Descargar consulta en formato CSV" type="button" class="btn btn-default col-xs-2 col-sm-1 col-md-1 col-lg-1" style="visibility:hidden">CSV</button>

			        {{Form::close()}}
			        {{Form::open(array('action' => array('TramitesController@resolucionTramite'), 'method' => 'post', 'id' => 'formulario-resolucion','class'=>'formulario-resolucion'))}}
						
						<button id="btn-imprimir-resolucion" title="Imprimir Resolución" type="button" class="btn btn-default col-xs-4 col-sm-3 col-md-3 col-lg-2" style="visibility:hidden">Imprimir Resolución</button>

					{{Form::close()}}

					{{Form::open(array('action' => array('TramitesController@caratulaTramite'), 'method' => 'post', 'id' => 'formulario-caratula','class'=>'formulario-caratula'))}}

						<button id="btn-imprimir-caratula" title="Imprimir Carátula" type="button" class="btn btn-default col-xs-5 col-sm-3 col-md-3 col-lg-2" style="display: none;">Imprimir Caratula</button>

					{{Form::close()}}
					{{Form::open(array('action' => array('TramitesController@caratulaTramite'), 'method' => 'post', 'id' => 'formulario-caratula','class'=>'formulario-caratula'))}}

						<button id="btn-nota-solicitud" title="Imprimir Nota Solicitud" type="button" class="btn btn-default col-xs-5 col-sm-3 col-md-3 col-lg-2" style="display: none;">Imprimir Solicitud</button>

					{{Form::close()}}
					
					{{Form::open(array('action' => array('TramitesController@cancelarTramiteConsulta'), 'method' => 'post', 'id' => 'formulario-seleccionado-c','class'=>'formulario-seleccionado-c'))}} 	
						{{Form::hidden('id_seleccionado_c','',  array('id' => 'id_seleccionado_c'))}}
						<button id="btn-cancelar-tramite" type="button" class="btn btn-default col-xs-3 col-sm-3 col-md-2 col-lg-2" style="display:none">Cancelar</button>
					{{Form::close()}}
			    </div> 
			</div>
			</div>{{-- nav bar collapse--}}
		</div>
	</nav>
</div>
	{{-- tabla trámites --}}
	<table id="tabla-tramites-pie" class="col-xs-12 col-sm-12 col-md-11 col-lg-12">
		   <tfoot class="pie" id="pie_tabla"></tfoot>
	</table>
	
	<div id="contenedor_tabla" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		    <div class="table-responsive" id="tabla-scroll">
		    	<table class="table table-bordered table-condensed" id="tabla-tramites"> 
					<thead class="encabezados">
						<tr id="fixed-row">
							<th class="col-lg-2">Nº Seguimiento</th>
							<th class="col-lg-2">Tipo Trámite</th>
							<th class="col-lg-2">Fecha Inicio</th>
							<th class="col-lg-2">Fecha Última Modificación</th>
							<th class="col-lg-2">Estado</th>
							<th class="col-lg-2">Ingresó en</th>
							<th class="col-lg-2">Razón Social</th>
							<th class="col-lg-2">Permiso</th>
							<th class="col-lg-2">Agente</th>
							<th class="col-lg-2">Subagente</th>
						</tr>
					</thead>
				<!--/table>
				<table class="table table-bordered table-condensed" id="tabla-tramites"--> 
					<tbody id="cuerpo" class="cuerpo"></tbody>
		   		</table>
		    </div>
				
	</div>
	
@endsection
@section('contenido_modal')
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="contenedor_modal">
            
           <!-- vista de detalle como modal -->
          	
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
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
</div >
@endsection
