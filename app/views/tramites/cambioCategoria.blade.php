@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/js-localidades.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-cambiocategoria.js?v=".rand(), array("type" => "text/javascript"))}}
	{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/dropzone.js", array("type" => "text/javascript"))}}	
	{{HTML::script("js/js-adjuntararchivos.js?v=".rand(), array("type" => "text/javascript"))}}	
	{{HTML::script('js/jquery.uploadfile.min.js', array('type'=>'text/javascript'))}}
	{{HTML::script('js/js-session.js', array('type'=>'text/javascript'))}}
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
	      		{{Form::label('mensaje', '', array('id'=>'mensaje'))}}
	    	</div>
		</div>
		<div class="col">
			<h1>{{$titulo}}</h1>	
		</div>
	</div>	
		<br/>
	
	<div class="row-border">
		@if(Session::has('tramiteNuevoPermiso'))
			<div class="col-sm-12 col-lg-12 div_sin_padding alert-danger text-center">
			{{Form::label('nuevo_permiso','TRÁMITE PARA NUEVO PERMISO',array('id'=>'nuevo_permiso','class'=>'alert-danger'))}}
			</div>
			<BR>
		@endif
		{{Form::open(array('action' => array('TramitesController@cambioCategoria_add'), 'method' => 'post', 'id' => 'formulario-cambio-categoria', 'class'=>'form-horizontal'))}} 
	
		{{Form::hidden('es_nuevo_permiso', Session::has('tramiteNuevoPermiso'))}}
		{{Form::hidden('nro_tramite', Session::get('nro_tramite'))}}
				<div class="form-group">	
						{{Form::label('lnro_permiso', 'Permiso:', array('id'=>'lnro_permiso', 'class'=>'control-label col-xs-3'))}}
					<div class="col-sm-2">
						{{Form::text('permiso', '', array('id' => 'id_permiso', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}  
					</div>
					@if($cant==0 || ($meses<0 && $dias<0) || $meses<0)
					@elseif($paso_fecha==0)<!-- no paso fecha límite-->
							@if($meses==0)
								@if($dias==1)
									<div class="col-sm-6 div_sin_padding alert-danger text-center">
										{{Form::label('margen','IMPORTANTE: Último Cambio de Categoria hace '.$dias.' dia.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@elseif ($dias==0) 
									<div class="col-sm-6 div_sin_padding alert-danger text-center">
										{{Form::label('margen','IMPORTANTE: Último Cambio de Categoria hoy.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@else
									<div class="col-sm-6 div_sin_padding alert-danger text-center">
										{{Form::label('margen','IMPORTANTE: Último Cambio de Categoria hace '.$dias.' dias.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@endif
							@elseif($meses==1)
								<div class="col-sm-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último Cambio de Categoria hace '.$meses.' mes.',array('id'=>'margen','class'=>'alert-danger'))}}
								</div>
							@else
								<div class="col-sm-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último Cambio de Categoria hace '.$meses.' meses.',array('id'=>'margen','class'=>'alert-danger'))}}
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
						<div class="col-sm-2">
							{{Form::text('subagente', '', array('id' => 'id_subagente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip',  'readonly'=>'true', 'disabled'=>'disabled'))}}  
						</div>
					</div>
					<div class="form-group">
							{{Form::label('lrazon_social', 'Razón Social:', array('id'=>'lrazon_social', 'class'=>'control-label col-xs-3'))}}	
							<div class="col-sm-5">
								{{Form::text('razon_social', '', array('id' => 'razon_social', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
							</div>
					</div>
					@if(Session::has('usuarioTramite'))
						@if(Session::get('usuarioTramite.subagente')!=0)
							<BR/>
							<div class="form-group form-horizontal">
								<label id="lred" class="control-label col-xs-4 col-lg-4">Red:</label>
								<div class="col-sm-3 col-lg-4">
									{{Form::text('red', '', array('id' => 'red', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
								</div>
							</div>
							<BR/>
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
								{{Form::text('localidad_actual', '', array('id' => 'localidad_actual', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip',  'readonly'=>'true'))}}
							
							{{Form::hidden('localidad_actual_id','',array('id'=>'localidad_actual_id'))}}	

							</div>
					</div>
				@endif	
				<br/>
					
				<div class="alert alert-warning col-sm-12" id="nueva_categoria">
					<h4>{{Form::label('ldatos_nueva_categoria', 'Datos Nueva Categoria', array('id'=>'lnuevo_titular','class'=>'text-center col-xs-1 col-lg-12'))}}</h4>
					<br>
					<br>
					

				@if(Session::has('tramiteNuevoPermiso'))
					@if(empty($datosExtra))
					<div class="form-group form-inline">
							{{Form::label('lnueva_categoria', 'Categoria:', array('class'=>'control-label col-xs-3'))}}
						<div class="col-sm-4">
									{{Form::select('categorias', $categorias, '', array('id' => 'id_categoria', 'class'=>'form-control col-xs-3','onfocus'=>'siguienteCampo="buscarLocalidad"; nombreForm=this.form.id'))}}
						</div>
					</div>
							<div id="div_nueva_localidad" class="form-group">
								{{Form::label('llocalidades_nueva', 'Localidad:', array('id'=>'llocalidad_nueva', 'class'=>'control-label col-xs-3 col-sm-4 col-lg-3'))}}	
								<div class="col-sm-6 col-lg-6">
									<input list="localidades" name="buscarLocalidad" id="buscarLocalidad" class='form-control' placeholder="Ingrese Localidad" autocomplete="off" autofocus="" onfocus="siguienteCampo=this.form.nro_red.id; nombreForm=this.form.id" >
											<datalist id="localidades" >
											</datalist>
									@if( $errors->has('nueva_localidad'))
							              @foreach($errors->get('nueva_localidad') as $error )
							                   {{ "Localidad no válida." }}
							              @endforeach
							        @endif
									{{Form::hidden('nueva_localidad','',array('id'=>'nueva_localidad'))}}
									{{Form::hidden('nombre_localidad','',array('id'=>'nombre_localidad'))}}
									{{Form::hidden('cpscp_localidad','',array('id'=>'cpscp_localidad'))}}
							</div>
							</div>
					@else
						<div class="form-group form-inline">
									{{Form::label('lnueva_categoria', 'Categoria:', array('class'=>'control-label col-xs-3'))}}
								<div class="col-sm-4">
									{{Form::select('categorias', $categorias, '', array('id' => 'id_categoria', 'class'=>'form-control col-xs-3','onfocus'=>'siguienteCampo="nro_red"; nombreForm=this.form.id'))}}
							</div>
						</div>					
							<div id="div_nueva_localidad" class="form-group">
								{{Form::label('llocalidades_nueva', 'Localidad:', array('id'=>'llocalidad_nueva', 'class'=>'control-label col-xs-3 col-sm-3 col-md-3'))}}	
								<div class="col-sm-6">
									{{Form::text('buscarLocalidad', $datosExtra['nombre_cpscp_localidad'], array('id' => 'buscarLocalidad', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '50', 'data-toggle'=>'tooltip', 'readonly'=>'true', 'disabled'=>'disabled'))}}		
									{{Form::hidden('nueva_localidad',$datosExtra['id_localidad'],array('id'=>'nueva_localidad'))}}
									{{Form::hidden('nombre_localidad',$datosExtra['nombre_localidad'],array('id'=>'nombre_localidad'))}}
									{{Form::hidden('cpscp_localidad',$datosExtra['cpscp_localidad'],array('id'=>'cpscp_localidad'))}}
								</div>
							</div>
					@endif
					@else
						<div class="form-group form-inline">
								{{Form::label('lnueva_categoria', 'Categoria:', array('class'=>'control-label col-xs-3'))}}
							<div class="col-sm-4">
								{{Form::select('categorias', $categorias, '', array('id' => 'id_categoria', 'class'=>'form-control col-xs-3','onfocus'=>'siguienteCampo="nro_red"; nombreForm=this.form.id'))}}
						</div>
					</div>
					@endif

					
					@if(array_key_exists(1,$categorias)) <!-- Pasa a ser subagente-->
						<div class="form-group" id="nueva_red" >	
							{{Form::label('Nº Red:', '', array('id'=>'lnro_red','class'=>'control-label col-xs-3'))}}
							<div class="col-sm-2">
								{{Form::text('nro_red', $nro_red, array('id' => 'nro_red', 'class'=>'form-control ' ,'maxlength' => '4', 'data-toggle'=>'tooltip', 'data-placement'=>'right', 'autofocus'=>"", 'onfocus'=>'siguienteCampo="nro_subagente"; nombreForm=this.form.id'))}} 
	 		</div>
							<span id="icono_ok" class="glyphicon glyphicon-ok form-horizontal" style="display: none"></span>
							<span id="icono_error" class="glyphicon glyphicon-remove form-horizontal" style="display: none"></span>
							<div class="col-sm-6" id="div_razon_social" style="display: none">
								{{Form::text('razon_social_nr', '', array('id'=>'razon_social_nr','class'=>'form-control col-xs-3', 'readonly'=>'readonly'))}} 
	</div>		
 </div>
						<div class="form-group">	
							@if($datosExtra['nombreTipoUsuario'] !='SubAgente' && $datosExtra['nombreTipoUsuario'] != 'Agente')						
								{{Form::label('lnro_subagente', 'Subagente:', array('id'=>'lnro_subagente', 'class'=>'control-label col-xs-3 col-lg-3'))}}
							@endif
							<div class="col-sm-2 col-lg-2">
							@if($datosExtra['nombreTipoUsuario'] =='SubAgente' || $datosExtra['nombreTipoUsuario'] == 'Agente')
								{{Form::hidden('nro_subagente', '', array('id' => 'nro_subagente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo="motivo_cc"; nombreForm=this.form.id'))}}  
							@else
								{{Form::text('nro_subagente', '', array('id' => 'nro_subagente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo="motivo_cc"; nombreForm=this.form.id'))}}  
							@endif	
				</div>			
			</div>
					@else <!-- Pasa a ser agente-->
						<div class="form-group" id="nueva_red" >	
							{{Form::label('Nº Red:', '', array('id'=>'lnro_red','class'=>'control-label col-xs-3'))}}
							<div class="col-sm-2">
								{{Form::text('nro_red', $nro_red, array('id' => 'nro_red', 'class'=>'form-control ' ,'maxlength' => '4', 'data-toggle'=>'tooltip', 'data-placement'=>'right', 'autofocus'=>"", 'onfocus'=>'siguienteCampo="cbu"; nombreForm=this.form.id'))}} 
							</div>
							<span id="icono_ok" class="glyphicon glyphicon-ok form-horizontal" style="display: none"></span>
							<span id="icono_error" class="glyphicon glyphicon-remove form-horizontal" style="display: none"></span>
							<div class="col-sm-6" id="div_razon_social" style="display: none">
								{{Form::text('razon_social_nr', '', array('id'=>'razon_social_nr','class'=>'form-control col-xs-3', 'readonly'=>'readonly'))}} 
							</div>
						</div>
						<div class="form-group">	
								@if($datosExtra['nombreTipoUsuario'] !='SubAgente' && $datosExtra['nombreTipoUsuario'] != 'Agente')
									{{Form::label('lnro_subagente', 'Subagente:', array('id'=>'lnro_subagente', 'class'=>'control-label col-xs-3 col-lg-3'))}}
								@endif
							<div class="col-sm-2 col-lg-2">
								@if($datosExtra['nombreTipoUsuario'] =='SubAgente' || $datosExtra['nombreTipoUsuario'] == 'Agente')
									{{Form::hidden('nro_subagente', '0', array('id' => 'nro_subagente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'readonly'=>'true', 'disabled'=>'disabled', 'onfocus'=>'siguienteCampo="cbu"; nombreForm=this.form.id'))}}  
								@else
									{{Form::text('nro_subagente', '0', array('id' => 'nro_subagente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'readonly'=>'true', 'disabled'=>'disabled', 'onfocus'=>'siguienteCampo="cbu"; nombreForm=this.form.id'))}}  
								@endif
							</div>
						</div>
						<div class="form-group form-inline">
							{{Form::label('lcbu', 'CBU:', array('id'=>'lcbu','class'=>'control-label col-xs-3'))}}
							<div class="col-sm-3">
							{{Form::text('cbu',$cbu, array('id' => 'cbu', 'class'=>'form-control input-sm col-xs-3' , 'data-toggle'=>'tooltip','onfocus'=>'siguienteCampo = "motivo_cc";nombreForm=this.form.id','autocomplete'=>'off'))}}  
		</div>
						</div>					
					@endif
					
					{{Form::hidden('nombreTipoUsuario', $datosExtra['nombreTipoUsuario'], array('id' => 'nombreTipoUsuario'))}}  
					
					<div class="form-group">
						{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambioc','class'=>'control-label col-xs-1 col-lg-3'))}}
				
						<div class="col-sm-4 col-lg-8">
							{{Form::textarea('motivo_cc', '', array('id' => 'id_motivo_cambio_c', 'class'=>"form-control input-medium", 'autofocus'=>"",'onfocus'=>'siguienteCampo="btn_adjuntar"; nombreForm=this.form.id'))}}
						</div>
					
					</div>

					<!-- ADJUNTO DE ARCHIVOS -->
		<div class="col-sm-12" id="div_adjuntos">
			{{Form::label('ltipo_documento', 'Tipo Documento:', array('id'=>'ltipo_documento','class'=>'control-label col-xs-3 col-sm-4'))}}
				
			<div class="col-sm-6 col-lg-6 ">
				{{Form::select('tipo_documento', $tipo_documento,'' ,array('id' => 'tipo_documento', 'class'=>'form-control col-sm-3 input-medium','autofocus'=>"",'onfocus'=>'siguienteCampo="btn_adjuntar"; nombreForm=this.form.id;'))}}
			</div>
			{{Form::button('Adjuntar archivos', array('id' => 'btn_adjuntar', 'name'=>'btn_adjuntar', 'class' => 'col-sm-3 glyphicon glyphicon-plus btn btn-success','onfocus'=>'siguienteCampo="btn_ingresar"; nombreForm=this.form.id'))}}
			<div id="adjuntos" style="display:none" class="col-sm-9">
					{{Form::hidden('nombre_tipo_tramite','CC',  array('id' => 'nombre_tipo_tramite'))}}
			       <div class="dropzone" id="imagenes"> </div>
	 		</div>
	 	</div><!-- fin adjunto -->	
	</div>	<!--Fin zona amarilla-->
	<div class="col-sm-12">
		{{Form::button('Ingresar', array('id' => 'btn_ingresar', 'name'=>'btn_ingresar', 'class' => 'btn-primary'))}} 
		{{Form::button('Volver', array('id'=>'btn_volver', 'name'=>'btn_volver', 'class'=>'btn-primary'))}}	
		{{Form::button('Limpiar', array('id'=>'btn-limpiar', 'name'=>'btn-limpiar', 'class'=>'btn-primary', 'onclick'=>'refrescar(this.form.id);'))}}
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




