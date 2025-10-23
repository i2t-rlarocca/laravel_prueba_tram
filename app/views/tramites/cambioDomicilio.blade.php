@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/js-localidades.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-cambiodomicilio.js?v=".rand(), array("type" => "text/javascript"))}}	
	{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/dropzone.js", array("type" => "text/javascript"))}}	
	{{HTML::script("js/js-adjuntararchivos.js?v=".rand(), array("type" => "text/javascript"))}}	
	{{HTML::script('js/jquery.uploadfile.min.js', array('type'=>'text/javascript'))}}
@stop
@section('css')
	{{HTML::style("css/dropzone.css", array("type" => "text/css"))}}		
	{{HTML::style("css/basic.css", array("type" => "text/css"))}}		
	{{HTML::style("css/uploadfile.css", array("type" => "text/css"))}}	
@stop
@section('contenido')
	<div class="row">
		<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje','' , array('id'=>'mensaje'))}}
		    </div>
		</div>
		<div class="col">
			<h1>{{$titulo}}</h1>	
		</div>
	</div>	
		</br>
	
	<div class="row-border">
		@if(Session::has('tramiteNuevoPermiso'))
			<div class="col-sm-12 col-lg-12 div_sin_padding alert-danger text-center">
			{{Form::label('nuevo_permiso','TRÁMITE PARA NUEVO PERMISO',array('id'=>'nuevo_permiso','class'=>'alert-danger'))}}
			</div>
			<BR>
		@endif
		{{Form::open(array('action' => array('TramitesController@cambioDomicilio_add'), 'method' => 'post', 'id' => 'formulario_domicilio', 'class'=>'form-horizontal'))}}
		
				<div class="form-group">	
						{{Form::label('lnro_permiso', 'Permiso:', array('id'=>'lnro_permiso', 'class'=>'control-label col-xs-3'))}}
					<div class="col-sm-2">
						{{Form::text('permiso', '', array('id' => 'id_permiso', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}  
					</div>
					@if($cant==0 || ($meses<0 && $dias<0) || $meses<0)
					@elseif($paso_fecha==0)<!--no paso fecha límite-->
							@if($meses==0)
								@if($dias==1)
									<div class="col-sm-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último Cambio de Domicilio hace '.$dias.' dia.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@elseif($dias==0)
									<div class="col-sm-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último Cambio de Domicilio hoy.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@else
									<div class="col-sm-6 div_sin_padding alert-danger text-center">
										{{Form::label('margen','IMPORTANTE: Último Cambio de Domicilio hace '.$dias.' dias.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@endif
							@elseif($meses==1)
								<div class="col-sm-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último Cambio de Domicilio hace '.$meses.' mes.',array('id'=>'margen','class'=>'alert-danger'))}}
								</div>
							@else
								<div class="col-sm-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último Cambio de Domicilio hace '.$meses.' meses.',array('id'=>'margen','class'=>'alert-danger'))}}
								</div>
							@endif				
					@endif
					
				</div>
				@if(!Session::has('tramiteNuevoPermiso'))
				<div class="form-group form-horizontal">
					{{Form::label('lnro_agente', 'Agente:', array('id'=>'lnro_agente', 'class'=>'control-label col-xs-3'))}}
					<div class="col-sm-2">
      					{{Form::text('agente', '', array('id' => 'id_agente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
    				</div>
					
				
					{{Form::label('lnro_subagente', 'Subagente:', array('id'=>'lnro_subagente', 'class'=>'control-label col-xs-3 col-lg-1'))}}
					<div class="col-sm-2 col-lg-2">
						{{Form::text('subagente', '', array('id' => 'id_subagente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}  
					</div>
				</div>
				
				<div class="form-group form-horizontal">
					<label id="raz_soc" class="control-label col-xs-3 ">Razón Social:</label>
					<div class="col-sm-8 col-lg-5">
						{{Form::text('razon_social', '', array('id' => 'razon_social', 'class'=>'form-control col-xs-3 text-right', 'readonly'=>'true'))}} 
					</div>
				</div>
				
				@if(Session::has('usuarioTramite'))
					@if(Session::get('usuarioTramite.subAgente')!=0)
						<div class="form-group form-horizontal">
							<label id="lred" class="control-label col-xs-4 col-lg-3">Red:</label>
							<div class="col-sm-3 col-lg-5">
								{{Form::text('red', '', array('id' => 'red', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
							</div>
						</div>
					@endif
				@endif
				
				<div class="form-group">
					
						{{Form::label('ldepartamento', 'Departamento:', array('id'=>'ldepartamento', 'class'=>'control-label col-xs-3'))}}	
						<div class="col-sm-5">
							{{Form::text('departamento', '', array('id' => 'departamento', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
						
						{{Form::hidden('departamento_id','',array('id'=>'departamento_id'))}}	

						</div>
				</div>
				<div class="form-group">
					
						{{Form::label('llocalidades', 'Localidad:', array('id'=>'llocalidad', 'class'=>'control-label col-xs-3'))}}	
						<div class="col-sm-5">
							{{Form::text('localidad_actual', '', array('id' => 'localidad_actual', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
						
						{{Form::hidden('localidad_actual_id','',array('id'=>'localidad_actual_id'))}}	

						</div>
					
					
				</div>
				@else
					{{Form::hidden('razon_social', '', array('id' => 'razon_social'))}}
					{{Form::hidden('agente', '', array('id' => 'id_agente'))}}
					{{Form::hidden('subagente', '', array('id' => 'id_subagente'))}}
					{{Form::hidden('departamento_id','',array('id'=>'departamento_id'))}}	
					{{Form::hidden('localidad_actual_id','',array('id'=>'localidad_actual_id'))}}
				@endif
				
				<br>
				<div class="alert alert-warning col-sm-12" id="nuevo_domicilio">
					<h4>{{Form::label('lnuevo_dom', 'Datos nuevo domicilio', array('id'=>'lnueva_red','class'=>'text-center col-xs-12 col-lg-12'))}}</h4>
					<br>
					<br>
					<div class="form-group">	
						{{Form::label('Domicilio comercial:', '', array('id'=>'ldomicilio_comercial','class'=>'control-label col-xs-6 col-sm-4'))}}
						<div class="col-sm-6">
						{{Form::text('domicilio_comercial', '', array('id' => 'id_domicilio_comercial', 'class'=>'form-control ' , 'data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.referente.id;nombreForm=this.form.id', 'maxlength' => '255'))}}  
						
						</div>
					</div>
					<div class="form-group">	
						{{Form::label('Referente agencia:', '', array('id'=>'lreferente','class'=>'control-label col-xs-6 col-sm-4'))}}
						<div class="col-sm-6">
						{{Form::text('referente', '', array('id' => 'referente', 'class'=>'form-control ','data-toggle'=>'tooltip', 'autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.datos_contacto.id;nombreForm=this.form.id', 'maxlength'=>'255'))}}						
						</div>
					</div>
					<div class="form-group">	
						{{Form::label('Datos contacto:', '', array('id'=>'ldatos_contacto','class'=>'control-label col-xs-8 col-sm-4 col-lg-4'))}}
						<div class="col-sm-6">  
							{{Form::textarea('datos_contacto', '', array('id' => 'datos_contacto', 'class'=>"form-control input-medium",'maxlength'=>'255' ,'onfocus'=>'siguienteCampo = this.form.buscarLocalidad.name;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'3'))}}
						</div>
					</div>
					@if(empty($datosExtra))
					<div id="div_nueva_localidad" class="form-group x">
						{{Form::label('llocalidades_nueva', 'Localidad:', array('id'=>'llocalidad_nueva', 'class'=>'control-label col-xs-3 col-sm-4'))}}	
						<div class="col-sm-6">
							<input list="localidades" name="buscarLocalidad" id="buscarLocalidad" class='form-control' placeholder="Ingrese Localidad" autocomplete="off" onfocus='siguienteCampo=this.form.motivo_cd.id; nombreForm=this.form.id' >
									<datalist id="localidades" >
									</datalist>
							@if( $errors->has('nueva_localidad'))
					              @foreach($errors->get('nueva_localidad') as $error )
					                   {{ "Localidad no válida." }}
					              @endforeach
					         @endif
							{{Form::hidden('nueva_localidad','',array('id'=>'nueva_localidad'))}}
							{{Form::hidden('nombre_localidad','',array('id'=>'nombre_localidad'))}}
						</div>
					</div>
					@else
						<div id="div_nueva_localidad" class="form-group y">
							{{Form::label('llocalidades_nueva', 'Localidad:', array('id'=>'llocalidad_nueva', 'class'=>'control-label col-xs-3 col-sm-4'))}}	
							<div class="col-sm-6">
								{{Form::text('buscarLocalidad', $datosExtra['nombre_cpscp_localidad'], array('id' => 'buscarLocalidad', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '50', 'data-toggle'=>'tooltip', 'readonly'=>'true', 'disabled'=>'disabled'))}}		
								{{Form::hidden('nueva_localidad',$datosExtra['id_localidad'],array('id'=>'nueva_localidad'))}}
								{{Form::hidden('nombre_localidad',$datosExtra['nombre_localidad'],array('id'=>'nombre_localidad'))}}
							</div>
						</div>
					@endif
					<div class="form-group">
						{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambio','class'=>'control-label col-xs-1 col-lg-4 col-sm-4'))}}
				
						<div class="col-sm-6 col-lg-6">
							{{Form::textarea('motivo_cd', '', array('id' => 'motivo_cd', 'class'=>"form-control input-medium", 'onfocus'=>'siguienteCampo = "btn_adjuntar";nombreForm=this.form.id'))}}
						</div>
					</div>
		
		
		<!-- ADJUNTO DE ARCHIVOS -->

					<!-- esto es lo que hay que corregir!!! -->

		<div class="form-group col-sm-12" id="div_adjuntos" style="display: none"> 
		
				{{Form::label('ltipo_documento', 'Tipo de Documento:', array('id'=>'ltipo_documento','class'=>'control-label col-xs-3 col-sm-4'))}}
				
				<div class="col-sm-6 col-lg-6 ">
					{{Form::select('tipo_documento', $tipo_documento,'' ,array('id' => 'tipo_documento', 'class'=>'form-control col-sm-3 input-medium','autofocus'=>"",'onfocus'=>'siguienteCampo="btn_adjuntar"; nombreForm=this.form.id;'))}}
				</div>

				{{Form::button('Adjuntar archivos', array('id' => 'btn_adjuntar', 'name'=>'btn_adjuntar', 'class' => 'col-sm-3 glyphicon glyphicon-plus btn btn-success','onfocus'=>'siguienteCampo = "btn_ingresar";nombreForm=this.form.id', 'disabled'))}}
				<div id="adjuntos" style="display:none" class="col-sm-9">
					{{Form::hidden('nombre_tipo_tramite','CD',  array('id' => 'nombre_tipo_tramite'))}}
					<div class="dropzone" id="imagenes">	</div>
				</div>
						
	 	</div><!-- fin div_adjuntos -->
	 	
</div> <!-- fin zona amarilla-->
	
		<div class="col-sm-12">	
			{{Form::hidden('continuar','',array('id'=>'continuar'))}}	
			{{Form::button('Ingresar', array('id' => 'btn_ingresar', 'name'=>'btn_ingresar', 'disabled'=>$bloqIngreso, 'class' => 'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}} 
			{{Form::button('Cargar Plan', array('id' => 'btn_cargarPlan', 'name'=>'btn_cargarPlan', 'class' => 'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}} 
			{{Form::button('Volver', array('id'=>'btn_volver', 'name'=>'btn_volver', 'class'=>'btn-primary', 'onfocus'=>'siguienteCampo = "nada";'))}}	
			{{Form::button('Limpiar', array('id'=>'btn-limpiar', 'name'=>'btn-limpiar', 'class'=>'btn-primary', 'onclick'=>'refrescar(this.form.id);','onfocus'=>'siguienteCampo = "nada";'))}}		
		</div>
		
{{Form::close()}}
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





