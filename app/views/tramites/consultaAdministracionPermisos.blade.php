@extends('layouts.base')
@section('javascript')	
	{{HTML::script("js/jquery-ui.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-datepicker.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-botonesconsultapermisos.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}	
@stop	
@section('css')

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">

@stop
@section('contenido')

	<h1>Administración de Permisos</h1> 
	
{{---------------------------------Tabla Trámites --------------------------------------}}	
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
						<div class="btn-group btn-group-lg col-xs-12 col-sm-12 col-md-12 col-lg-12">
						    {{Form::open(array('action' => array('TramitesController@adjudicarPermiso'), 'method' => 'post', 'id' => 'formulario-seleccionado','class'=>'formulario-seleccionado'))}} 
						
								{{Form::hidden('id_seleccionado','',array('id' => 'id_seleccionado'))}}
								
								<button id="btn_adjudicar_agencia" title="Adjudicar (Alt+a)" type="button" class="btn btn-default col-xs-3 col-sm-3 col-md-3 col-lg-3" accesskey="a" style="display:none">Adjudicar Agencia</button>							  
								<button id="btn_adjudicar_subagencia" title="Adjudicar (Alt+a)" type="button" class="btn btn-default col-xs-3 col-sm-3 col-md-3 col-lg-3" accesskey="s" style="display:none">Adjudicar Subagencia</button>

							
						    	<button id="btn_imprimir" title="Imprimir consulta en PDF" type="button" class="btn btn-default col-xs-2 col-sm-2 col-md-2 col-lg-2" accesskey="i">Imprimir Consulta</button>
								
								<button id="btn_csv" title="Descargar consulta en formato CSV" type="button" class="btn btn-default col-xs-2 col-sm-2 col-md-2 col-lg-2" accesskey="c">CSV</button>
							{{Form::close()}}
						</div> 
					</div>				
				</div>{{-- nav bar collapse--}}
			</div>
		</nav>
	</div>

	{{Form::open(array('action' => array('TramitesController@grillaPermisosSinAdjudicar'), 'method' => 'post', 'id' => 'formulario_permisos','class'=>'formulario_permisos'))}}
		<div id="contenedor_tabla" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			    <div class="table-responsive" id="tabla-scroll">
			    	<table class="table table-bordered table-condensed center-table" id="tabla-tramites"> 
			    		<caption><strong><h1>Permisos sin adjudicar</h1></strong></caption>
						<thead class="encabezados">
							<tr id="fixed-row">
								<th class="col-lg-2">Nº Permiso</th>
								<th class="col-lg-2">Fecha Inicio</th>
								<th class="col-lg-2">Estado</th>
								<th class="col-lg-2">Usuario Generador</th>
							</tr>
						</thead>
						<tbody id="cuerpo" class="cuerpo"></tbody>
			  			<tfoot class="pie" id="pie_tabla"></tfoot>
			   		</table>
			    </div>				
		</div>
	{{Form::close()}}
	
@endsection
@section('contenido_modal')
<div class="modal fade" id="permisoModal" role="dialog" aria-labelledby="permisoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" id="contenedor_modal">
        	<div class="modal-header" id='hmodal'> 
				<div class="col">
					<h1 class="modal-title">Alta Permisos</h1>
				</div>
			</div>
			 <div class="modal-body" id='bmodal'>
				<div class="row-border">
					<div align="center">
					{{Form::open(array('action' => array('TramitesController@altaPermisos_add'), 'method' => 'post', 'id' => 'formulario_alta_permisos','class'=>'formulario_alta_permisos'))}}
							<div class="form-group form-horizontal col-lg-12">
						 		<label for="l_cant_permiso" id="l_cant_permiso" class="control-label col-lg-12">Cantidad de Permisos:</label>
						 		<div class="col-lg-6" id="div_cant_permisos_modal">
									<input class="form-control col-lg-4 text-center" type="text" id="cant_permisos_modal" pattern="\d*" 
									title='Sólo Nº' maxlength="2" onfocus='siguienteCampo = "resolucion_modal"; nombreForm=this.form.id;'></input>
								</div>
							</div>
							
							<div class="form-group form-horizontal col-lg-12">
								<label for="l_resolucion" id="l_resolucion" class="control-label">Resolución:</label>
								<input class="form-control text-center" type="text" id="resolucion_modal" maxlenght="100" onfocus='siguienteCampo = "fecha_resol"; nombreForm=this.form.id;'></input>
							</div>
							<div class="form-group form-horizontal col-lg-12">
								<label for="l_fecha_resol" id="l_fecha_resol" class="control-label">Fecha Resolución:</label>
								<input type="text" class="datepicker form-control col-lg-6 text-center" id="fecha_resol" maxlength='10' pattern='\d{1,2}/\d{1,2}/\d{4}' placeholder='dd/mm/aaaa' onfocus='siguienteCampo = "cargar"; nombreForm=this.form.id;'></input>
							</div>			
						
					</div>
				</div>
			</div>

			<div class="modal-footer" id="fmodal" >
				<div class="col-xs-12 col-sm-12 col-md-10 col-lg-12">
					<button id="cargar" type="button" class="btn btn-default">Alta</button>
				{{Form::close()}}
			    	<button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
			   </div>
			</div>                  	
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /fin modal permiso .modal -->
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








