@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/jquery-ui.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-datepicker.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-localidades.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-cambiotitular.js?v=".rand(), array("type" => "text/javascript"))}}
	{{HTML::script("js/js-validacuit.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/dropzone.js", array("type" => "text/javascript"))}}	
	{{HTML::script("js/js-adjuntararchivos.js?v=".rand(), array("type" => "text/javascript"))}}	
	{{HTML::script('js/jquery.uploadfile.min.js', array('type'=>'text/javascript'))}}
@stop
@section('css')
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
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
		
	<div class="row-border">
		@if(Session::has('tramiteNuevoPermiso'))
			<div class="col-sm-12 col-lg-12 div_sin_padding alert-danger text-center">
			{{Form::label('nuevo_permiso','TRÁMITE PARA NUEVO PERMISO',array('id'=>'nuevo_permiso','class'=>'alert-danger'))}}
			</div>
			<BR>
		@endif
		<div class="row">
		{{Form::hidden('tipo_tramite',$tipo,array('id'=>'tipo_tramite'))}}
<!-- el llave-llave-form evalua los datos!!! , tuve que condicionar ADEN - 2024-09-03 -->		
		@if($tipo != 8)
<!--		{{Form::hidden('cuitPermiso',$cuitPermiso,array('id'=>'cuitPermiso'))}} -->
        @endif
		@if($tipo == 3) <!-- 3=> Cambio de Titular, X=> Cambio de CO-Titular -->
			{{Form::open(array('action' => array('TramitesController@cambioTitular_add'), 'method' => 'post', 'id' => 'formulario-cambio-titular', 'class'=>'form-horizontal'))}} 
		@else
			{{Form::open(array('action' => array('TramitesController@incorporarCoTitular_add'), 'method' => 'post', 'id' => 'formulario-cambio-titular', 'class'=>'form-horizontal'))}} 
		@endif
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
										{{Form::label('margen','IMPORTANTE: Último Cambio de Titular hace '.$dias.' dia.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@elseif($dias==0)
									<div class="col-sm-6 div_sin_padding alert-danger text-center">
										{{Form::label('margen','IMPORTANTE: Último Cambio de Titular hoy.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@else
									<div class="col-sm-6 div_sin_padding alert-danger text-center">
										{{Form::label('margen','IMPORTANTE: Último Cambio de Titular hace '.$dias.' dias.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@endif
							@elseif($meses==1)
								<div class="col-sm-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último Cambio de Titular hace '.$meses.' mes.',array('id'=>'margen','class'=>'alert-danger'))}}
								</div>
							@else
								<div class="col-sm-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último Cambio de Titular hace '.$meses.' meses.',array('id'=>'margen','class'=>'alert-danger'))}}
								</div>
							@endif				
					@endif
					
				</div>
				@if(!Session::has('tramiteNuevoPermiso'))
					<div class="form-group form-horizontal">
						{{Form::label('lnro_agente', 'Agente:', array('id'=>'lnro_agente', 'class'=>'control-label col-xs-3'))}}
						<div class="col-sm-2">
	      					{{Form::text('agente', '', array('id' => 'id_agente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip',  'readonly'=>'true'))}}
	    				</div>
							
						{{Form::label('lnro_subagente', 'Subagente:', array('id'=>'lnro_subagente', 'class'=>'control-label col-xs-3 col-lg-1'))}}
						<div class="col-sm-2 col-lg-2">
							{{Form::text('subagente', '', array('id' => 'id_subagente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}  
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
								<div class="col-sm-3 col-lg-6">
									{{Form::text('red', '', array('id' => 'red', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
								</div>
							</div>
							<BR/>
						@endif
					@endif
					<div class="form-group">					
							{{Form::label('ldepartamento', 'Departamento:', array('id'=>'ldepartamento', 'class'=>'control-label col-xs-3'))}}	
							<div class="col-sm-5">
								{{Form::text('departamento', '', array('id' => 'departamento', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip',  'readonly'=>'true'))}}
							
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
				{{-- NUEVOS DATOS--}}
				<div class="alert alert-warning col-sm-12" id="nueva_red">
					@if($tipo == 3) <!-- 3=> Cambio de Titular, X=> Cambio de CO-Titular -->
						<h4>{{Form::label('lnuevo_titular', 'Datos del Nuevo Titular', array('id'=>'lnuevo_titular','class'=>'text-center col-xs-12 col-lg-12'))}}</h4>
					@else
						<h4>{{Form::label('lnuevo_titular', 'Datos del Nuevo CoTitular', array('id'=>'lnuevo_titular','class'=>'text-center col-xs-12 col-lg-12'))}}</h4>
					@endif
					<br>
					<div class="form-group form-inline">
						{{Form::label('ltipo_persona', 'Persona:', array('class'=>'control-label col-xs-3'))}}
						<div class="col-sm-2">
							{{Form::select('tipo_persona', $tipo_persona, '', array('id' => 'tipo_persona', 'class'=>'form-control col-xs-3','autofocus'=>'""'))}}
						</div>

						<div id="sexo_persona_fisica" class="form-group form-inline">
							{{Form::label('lsexo', 'Sexo:', array('class'=>'control-label col-xs-4 col-xs-offset-1 col-lg-3'))}}
							<div class="col-xs-4 col-sm-8 col-lg-8">
							  <label class="radio-inline">
							    <input type="radio" name="sexo_persona" id="mujer" value="F" checked onfocus='siguienteCampo = this.form.hombre.id;nombreForm=this.form.id'>F
							  </label>
							
							  <label class="radio-inline">
							    <input type="radio" name="sexo_persona" id="hombre" value="M" checked onfocus='siguienteCampo = this.form.cuit.id;nombreForm=this.form.id'>M
							  </label>
							</div>
						</div>
						<div id='tipo_persona_juridica' class="form-group form-inline" style='display:none'>
							{{Form::label('ltipo_sociedad', 'Tipo Sociedad: ', array('class'=>'control-label col-xs-5 col-lg-7 col-lg-pull-1'))}}
							<div class="col-xs-4 col-sm-8 col-lg-3 col-lg-pull-2">
							{{Form::select('tipo_sociedad', $tipo_sociedad, '', array('id' => 'tipo_sociedad', 'class'=>'form-control col-xs-3', 'onfocus'=>'siguienteCampo = this.form.cuit.id;nombreForm=this.form.id'))}}
							</div>	
						</div>				
					</div>	
					
					<div class="form-group">	
						{{Form::label('cuit', 'Cuit:', array('id'=>'lcuit','class'=>'control-label col-xs-3'))}}
						<div class="col-sm-3">
						{{Form::text('cuit', '', array('id' => 'cuit', 'class'=>'form-control ' ,'maxlength' => '13', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.tipo_doc.id;nombreForm=this.form.id','autocomplete'=>'off'))}}  
						</div>
					</div>
					<div id="datos_persona_fisica">
						<div class="form-group form-inline" >	
							<div class="col-lg-6">
								{{Form::label('ltipo_doc', 'Tipo documento:', array('id'=>'ltipo_doc','class'=>'control-label col-xs-3 col-lg-6'))}}
								<div class="col-sm-2 col-lg-2">
								{{Form::select('tipo_doc', $tipo_doc, '3', array('id' => 'tipo_doc', 'class'=>'form-control col-xs-2', 'onfocus'=>'siguienteCampo = this.form.nro_doc.id;nombreForm=this.form.id'))}}
								</div>
							</div>
							<div class="col-lg-6">
								{{Form::label('ldoc', 'Nº documento:', array('id'=>'ldoc','class'=>'control-label col-xs-3 col-lg-5'))}}
								<div class="col-sm-3 col-lg-5 div_sin_padding">
								{{Form::text('nro_doc', '', array('id' => 'nro_doc', 'class'=>'form-control ' ,'maxlength' => '10','data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo = this.form.fecha_nac.id;nombreForm=this.form.id','autocomplete'=>'off'))}}{{-- ,'pattern'=>'^[1-9]{1,2}[.]\d{3}[.]\d{3}$' 'pattern'=>"^[1-9]{1,2}[0-9]{6,7}$" --}}  
								</div>
								<div class="col-lg-8" style="color:red">
									@if($errors->any())
										<h4>{{$errors->first('nro_doc')}}</h4>
									@endif
								</div>
							</div>
						</div>
						<div class="form-group">
							{{Form::label('lfecha_nac', 'Fecha nacimiento:', array('id'=>'lfecha_nac','class'=>'control-label col-xs-3 col-lg-3'))}}
							<div class="col-sm-2">
							{{Form::text('fecha_nac', '', array('id' => 'fecha_nac', 'class'=>'datepicker form-control ' ,'maxlength' => '10', 'pattern'=>"\d{1,2}/\d{1,2}/\d{4}",'placeholder'=>'dd/mm/aaaa','data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo = this.form.tipo_ocup.id;nombreForm=this.form.id','autocomplete'=>'off'))}}  
							</div>
						</div>
						<div class="form-group">	
							{{Form::label('Ocupación:', '', array('id'=>'locupación','class'=>'control-label col-xs-3 col-lg-3'))}}
							<div class="col-sm-5">
							 	{{Form::select('tipo_ocup', $tipo_ocup_tit, "ni", array('id' => 'tipo_ocup', 'class'=>'form-control col-xs-2','onfocus'=>'siguienteCampo = this.form.apellido_nombre.id;nombreForm=this.form.id'))}}
							</div>
						</div>
						<div id="div_pers_fisica_n" class="form-group">	
						{{Form::label('Apellido y nombre:', '', array('id'=>'lnombre','class'=>'control-label col-xs-3'))}}
							<div class="col-sm-5">
							{{Form::text('apellido_nombre', '', array('id' => 'apellido_nombre', 'class'=>'form-control ' ,'maxlength' => '50', 'data-toggle'=>'tooltip', 'data-placement'=>'right','placeholder'=>'apellido nombre','onfocus'=>'siguienteCampo = this.form.apellido_mat.id;nombreForm=this.form.id'))}}  
							</div>
						</div>						
						<div id="div_pers_fisica_a" class="form-group">	
							{{Form::label('Apellido materno:', '', array('id'=>'lapellido_mat','class'=>'control-label col-xs-3'))}}
							<div class="col-sm-5">
							{{Form::text('apellido_mat', '', array('id' => 'id_apellido_mat', 'class'=>'form-control ' ,'maxlength' => '50','placeholder'=>'apellido materno', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.tipo_situacion.id;nombreForm=this.form.id'))}}  
							</div>
						</div>						
					</div>
					<div class="form-group col-lg-12">						
							{{Form::label('ltipo_situacion_ganancia', 'Situación de Ganancias: ', array('class'=>'control-label col-xs-12 col-lg-3'))}}
							<div class="col-xs-12 col-sm-4">
								{{Form::select('tipo_situacion', $tipo_situacion, 'ni', array('id' => 'tipo_situacion', 'class'=>'form-control col-xs-3','onfocus'=>'siguienteCampo = this.form.razon_social.id;nombreForm=this.form.id'))}}
							</div>	
<!-- Anulado ADEN - 2024-04-19							
						<div class="form-inline col-lg-4">
							{{Form::label('Nº Ing. brutos:', '', array('id'=>'lingresos','class'=>'control-label col-xs-12 col-lg-9 col-lg-pull-3'))}}
							<div class="col-xs-12 col-sm-2 col-lg-3 col-lg-pull-4">
							{{Form::text('ingresos', '', array('id' => 'ingresos', 'class'=>'form-control col-xs-12' , 'maxlength' => '12', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.domicilio.id;nombreForm=this.form.id'))}}  
							@if ($errors->get('ingresos'))
								<error_dt for="ingresos" class="error">{{ $errors->first('ingresos') }}</error_dt>
							@endif
							</div>
						</div>						
-->
					</div>

<!-- Anulado ADEN - 2024-04-16					
					<div class="form-group">	
						{{Form::label('lcbu', 'CBU:', array('id'=>'lcbu','class'=>'control-label col-xs-3'))}}
						<div class="col-sm-3">
						{{Form::text('cbu', '', array('id' => 'cbu', 'class'=>'form-control ' , 'data-toggle'=>'tooltip', 'data-placement'=>'right','autocomplete'=>'off'))}}  
						</div>
					</div>
-->					
					
					<div id="div_pers_jur" style='display:none' class="form-group">	
						{{Form::label('Razon Social:', '', array('id'=>'lrazonsocial','class'=>'control-label col-xs-3'))}}
						<div class="col-sm-5">
						{{Form::text('razon_social', '', array('id' => 'razon_social', 'class'=>'form-control ' ,'maxlength' => '50', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.domicilio.id;nombreForm=this.form.id'))}}  
						</div>
					</div>
					
					<div class="form-group">	
						{{Form::label('Domicilio Actual Titular:', '', array('id'=>'ldomicilio','class'=>'control-label col-xs-8 col-lg-3'))}}
						<div class="col-sm-5">  
						{{Form::textarea('domicilio', '', array('id' => 'domicilio', 'class'=>"form-control input-medium",'maxlength'=>'255' , 'onfocus'=>'siguienteCampo = this.form.id_email.id;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'3'))}}
						</div>
					</div>		
					<div class="form-group">	
						{{Form::label('Email:', '', array('id'=>'lemail','class'=>'control-label col-xs-3'))}}
						<div class="col-sm-5">
						{{Form::email('email', '', array('id' => 'id_email', 'class'=>'form-control ' , 'placeholder'=>'unemail@mail.com' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.referente.id;nombreForm=this.form.id'))}}  
						</div>
					</div>
					
					<div class="form-group">	
						{{Form::label('Referente agencia:', '', array('id'=>'lreferente','class'=>'control-label col-xs-3'))}}
						<div class="col-sm-5">
						{{Form::text('referente', '', array('id' => 'referente', 'class'=>'form-control ','data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.datos_contacto.id;nombreForm=this.form.id', 'maxlength'=>'255'))}}						
						</div>
					</div>
					<div class="form-group">	
						{{Form::label('Datos contacto:', '', array('id'=>'ldatos_contacto','class'=>'control-label col-xs-8 col-lg-3'))}}
						<div class="col-sm-5">  
							{{Form::textarea('datos_contacto', '', array('id' => 'datos_contacto', 'class'=>"form-control input-medium",'maxlength'=>'255' ,'onfocus'=>'siguienteCampo = this.form.buscarLocalidad.name;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'3'))}}
						</div>
					</div>	
					<div id="div_nueva_localidad" class="form-group">
						{{Form::label('llocalidades_nueva', 'Localidad Actual Agencia:', array('id'=>'llocalidad_nueva', 'class'=>'control-label col-xs-3'))}}	
						<div class="col-sm-5">
							<input list="localidades" name="buscarLocalidad" id="buscarLocalidad" class='form-control' placeholder="Ingrese Localidad" autocomplete="off" onfocus='siguienteCampo=this.form.motivo_ct.id; nombreForm=this.form.id'>
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
					<div class="form-group">					
						{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambiot','class'=>'control-label col-xs-1 col-lg-3'))}}
				
						<div class="col-sm-4 col-lg-8">
							{{Form::textarea('motivo_ct', '', array('id' => 'motivo_ct', 'class'=>"form-control input-medium",'maxlength'=>'255' ,'onfocus'=>'siguienteCampo = this.form.fecha_examen.id;nombreForm=this.form.id'))}}
						</div>
					
					</div>
				
				<!-- INI SE AGREGA 2024-04-23 (ADEN) FECHA DE EXAMEN, NRO DE INTENTOS y CALIFICACION -->
				
					<div id="datos_examen">
						<div class="form-group">
							<h4>{{Form::label('lexamen_evaluacion', 'Resultado del examen virtual del cesionario', array('id'=>'lexamen_evaluacion','class'=>'text-center col-xs-12 col-lg-12'))}}</h4>
						</div>
						<div class="form-group">
							{{Form::label('lfecha_examen', 'Fecha exámen:', array('id'=>'lfecha_examen','class'=>'control-label col-xs-3 col-lg-3'))}}
							<div class="col-sm-2">
							{{Form::text('fecha_examen', '', array('id' => 'fecha_examen', 'class'=>'datepicker form-control bordeverde' ,'maxlength' => '10', 'pattern'=>"\d{1,2}/\d{1,2}/\d{4}",'placeholder'=>'dd/mm/aaaa','data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo = this.form.intentos.id;nombreForm=this.form.id','autocomplete'=>'off'))}}  
							</div>

						</div>

						<div class="form-group" >	
							{{Form::label('lintentos', 'Intentos:', array('id'=>'lintentos','class'=>'control-label col-xs-1 col-lg-3'))}}
							<div class="col-sm-2">
								{{Form::text('intentos', '', array('id' => 'intentos', 'class'=>'form-control bordeverde','data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.calificacion.id;nombreForm=this.form.id', 'maxlength'=>'2'))}}						
							</div>
							{{Form::label('lcalificacion', 'Calificación:', array('id'=>'lintentos','class'=>'control-label col-xs-1 col-lg-3'))}}
							<div class="col-sm-2">
								{{Form::text('calificacion', '', array('id' => 'calificacion', 'class'=>'form-control ','data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = "btn_adjuntar" ;nombreForm=this.form.id', 'maxlength'=>'2'))}}						
							</div>
						</div>
<!--
						</div>
-->						
						@if($errors->any())
							<div class="col-lg-12" style="color:red">
								<h4>{{$errors->first('examen')}}</h4>
							</div>
						@endif
					</div> <!-- fin datos_examen -->
				
				
				<!-- FIN SE AGREGA 2024-04-23 -->
				
				
				<!-- ADJUNTO DE ARCHIVOS -->
<!-- antes - aden - 2024-04-23 				
				<div class="col-sm-12" id="div_adjuntos">
     despues - aden - 2024-04-23 -->				
				<div class="col-sm-12" id="div_adjuntos" style="display: none"> 
<!-- fin de cambio - aden -- 2024-04-23 -->				
					<!-- <div class="form-group"> -->
				{{Form::label('ltipo_documento', 'Tipo Documento:', array('id'=>'ltipo_documento','class'=>'control-label col-xs-3 col-sm-4'))}}
				
				<div class="col-sm-6 col-lg-6 ">
					{{Form::select('tipo_documento', $listaDocumentos,'' ,array('id' => 'tipo_documento', 'class'=>'form-control col-sm-3 input-medium','autofocus'=>"",'onfocus'=>'siguienteCampo="btn_adjuntar"; nombreForm=this.form.id;'))}}
				</div>
					{{Form::button('Adjuntar archivos', array('id' => 'btn_adjuntar', 'name'=>'btn_adjuntar', 'class' => 'col-sm-3 glyphicon glyphicon-plus btn btn-success', 'onfocus'=>'siguienteCampo = "btn_ingresar";nombreForm=this.form.id'))}}
					<div id="adjuntos" style="display:none" class="col-sm-9">
							{{Form::hidden('nombre_tipo_tramite','CT',  array('id' => 'nombre_tipo_tramite'))}}
					       <div class="dropzone" id="imagenes"></div>
					 </div>
			 	</div><!-- fin adjunto -->
				
		</div><!-- fin zona amarilla-->
				
		<div class="col-sm-12">	
			{{Form::hidden('continuar','',array('id'=>'continuar'))}}	
			{{-- Form::button('Ingresar', array('id' => 'btn_ingresar', 'name'=>'btn_ingresar', 'disabled'=>$bloqIngreso, 'class' => 'btn-primary', 'onfocus'=>'siguienteCampo = "nada";')) --}} 
			{{Form::button('Ingresar', array('id' => 'btn_ingresar', 'name'=>'btn_ingresar', 'class' => 'btn-primary', 'onfocus'=>'siguienteCampo = "nada";'))}} 
			{{-- Form::button('Cargar Plan', array('id' => 'btn_cargarPlan', 'name'=>'btn_cargarPlan', 'class' => 'btn-primary','onfocus'=>'siguienteCampo = "nada";')) --}} 
			{{Form::button('Volver', array('id'=>'btn_volver', 'name'=>'btn_volver', 'class'=>'btn-primary', 'onfocus'=>'siguienteCampo = "nada";'))}}		
			{{Form::button('Limpiar', array('id'=>'btn-limpiar', 'name'=>'btn-limpiar', 'class'=>'btn-primary'))}}		
		</div>
		{{Form::close()}}	
	</div>	
	<div id="botonera_2" class="col-sm-12">
						
			</div>	
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





