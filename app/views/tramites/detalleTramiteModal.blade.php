<div class="modal-header" id='hmodal'> 
	{{HTML::script("js/jquery-ui.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-datepicker.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-imprimirComprobantes.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-detalletramiteestados.js?var=".rand(), array("type" => "text/javascript"))}} 
	{{HTML::script("js/js-detalletramitehistorial.js?var=".rand(), array("type" => "text/javascript"))}}
	
	{{HTML::script("js/dropzone.js", array("type" => "text/javascript"))}}	
	{{HTML::script("js/js-adjuntararchivosmodal.js?v=".rand(), array("type" => "text/javascript"))}}	
	{{HTML::script('js/jquery.uploadfile.min.js', array('type'=>'text/javascript'))}}
	
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
	
	<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		<div class="alert alert-danger fade in">
			{{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		</div>
	</div>
	<div class="col">
		<h1 class="modal-title">Detalle de Trámite - {{$tramite['titulo']}} </h1>
	</div>
</div>
 <div class="modal-body" id='bmodal'>
	<div class="row-border">

		@if($tramite['nuevo_permiso']==1)
			<div class="col-sm-12 col-lg-12 div_sin_padding alert-danger text-center">
			{{Form::label('lnuevo_permiso','TRÁMITE PARA NUEVO PERMISO',array('id'=>'lnuevo_permiso','class'=>'alert-danger'))}}
			</div>
			<BR>
		@endif
		<!-- Campos comunes a todos los trámites-->
			<div class="form-group form-horizontal">
				@if($tramite['id_tipo_tramite']==2)
					{{Form::hidden('hsuba_a_ag',$tramite['suba_a_ag'], array('id'=>'suba_a_ag'))}}
				@endif
				
				<label class="control-label col-xs-5 col-sm-4" id="nro_seguimiento">Nº Seguimiento:</label> 
				<div class="col-xs-4 col-sm-3">
					<input id="nro_tramite" type="text" class="form-control col-xs-3 text-right" value={{$tramite['nro_tramite']}} maxlenght="15" disabled>
				</div>
				@if((Session::get('usuarioLogueado.nombreTipoUsuario')=='CAS' || Session::get('usuarioLogueado.nombreTipoUsuario')=='CAS_ROS') && $tramite['id_estado_tramite']<7)
					@if($cant==0 || ($meses<0 && $dias<0) || $meses<0)
					@elseif($paso_fecha==0)<!--no paso fecha límite-->
							@if($meses==0)
								@if($dias==1)
									<div class="col-sm-6 col-lg-6 div_sin_padding alert-danger text-center">
										{{Form::label('margen','IMPORTANTE: Último '.str_replace("Comercial", "", $tramite['nombre_tramite']).' hace '.$dias.' dia.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@elseif($dias==0)
									<div class="col-sm-6 col-lg-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último '.str_replace("Comercial", "", $tramite['nombre_tramite']).' hoy.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@else
									<div class="col-sm-6 col-lg-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último '.str_replace("Comercial", "", $tramite['nombre_tramite']).' hace '.$dias.' dias.',array('id'=>'margen','class'=>'alert-danger'))}}
									</div>
								@endif
							@elseif($meses==1)
								<div class="col-sm-6 col-lg-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último '.str_replace("Comercial", "", $tramite['nombre_tramite']).' hace '.$meses.' mes.',array('id'=>'margen','class'=>'alert-danger'))}}
								</div>
							@else
								<div class="col-sm-6 col-lg-6 div_sin_padding alert-danger text-center">
									{{Form::label('margen','IMPORTANTE: Último '.str_replace("Comercial", "", $tramite['nombre_tramite']).' hace '.$meses.' meses.',array('id'=>'margen','class'=>'alert-danger'))}}
								</div>
							@endif
					@endif				
					
				@endif
			</div>
			<BR/>
			<div class="form-group form-horizontal">
				<label id="label_nro_permiso_" class="control-label  col-xs-5 col-sm-4">Permiso:</label>
				<div class="col-xs-4 col-sm-3">
					<input id="nro_permiso_" type="text" class="form-control col-xs-3 text-right" value={{$tramite['nro_permiso']}} disabled>
				</div> 
			</div>
			<BR/>
			@if($tramite['nuevo_permiso']==0)
				<div class="form-group form-horizontal">
					<label id="agente" class="control-label col-xs-5 col-sm-4 text-right">Agente:</label>
					<div class="col-xs-4 col-sm-3 col-lg-3">
						<input type="text" class="form-control col-xs-3 text-right" value={{$tramite['agente']}}  disabled>
					</div>
					
					<label id="subagente_m" class="control-label col-xs-5 col-sm-2 col-lg-3">Sub Agente:</label>
					<div class="col-xs-4 col-sm-3 col-lg-2">
						<input id="subagente" type="text" class="form-control col-xs-3 text-right" value={{$tramite['subagente']}} disabled>
					</div>
				</div>
				<BR>
				<div class="form-group form-horizontal">
					<label id="raz_soc" class="control-label col-xs-3 col-lg-4">Razón Social:</label>
					<div class="col-sm-8 col-lg-8">
						<input type="text" class="form-control col-xs-3 text-right" value="{{$tramite['razon_social_permiso']}}"  disabled>
					</div>
				</div>
				<BR>
				@if($tramite['subagente']!=0)
					<div class="form-group form-horizontal">
						<label id="lred" class="control-label col-xs-4 col-lg-4">Red:</label>
						<div class="col-sm-3 col-lg-8">
							{{Form::text('red', $tramite['red'], array('id' => 'red', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
						</div>
					</div>
					<BR/>
				@endif			
				<div class="form-group form-horizontal">
					@if($tramite['id_tipo_tramite']==3 || $tramite['id_tipo_tramite']==8)
						<label id="ldomicilio" class="control-label col-xs-6 col-sm-4">Domicilio Particular:</label>
					@else
						<label id="ldomicilio" class="control-label col-xs-6 col-sm-4">Domicilio:</label>
					@endif
					<div class="col-sm-8">
						<input type="text" class="form-control col-xs-3 text-right" value=@if($datosPermiso['domicilio']!="")"{{$datosPermiso['domicilio']}}"@else '' @endif disabled>
					</div>
				</div>
				<BR/>
				<div class="form-group form-horizontal">
					<label id="llocalidad_m_" class="control-label col-xs-3 col-sm-4">Localidad:</label>
					<div class="col-sm-5">
						<input id="text_localidad_m_"type="text" class="form-control col-xs-3 text-right" value=@if($datosPermiso['localidad']!="")"{{$datosPermiso['localidad']}}"@else '' @endif disabled>
					</div>

					<label id="lcp_localidad_m_" class="control-label col-xs-1">C.P:</label>
					<div class="col-sm-2">
						<input id="cp_localidad_m_" type="text" class="form-control col-xs-3 text-right" value=@if($datosPermiso['cp_localidad']!=""){{$datosPermiso['cp_localidad']}}@else '' @endif  disabled>
					</div>
					
				</div>								
				<BR/>
			
				<div class="form-group form-horizontal">					
						<label id="fecha_it_m_" class="control-label col-xs-4 col-sm-4 col-lg-4">Fecha Inicio:</label> 
						<div class="col-sm-5">
							<input type="text" class="form-control col-xs-3 text-right" value={{$tramite['fecha']}}  disabled>
						</div>
				</div>
				<BR/>
				<div class="form-group form-horizontal">
						<label id="observaciones" class="control-label col-xs-3 col-sm-4">Motivo:</label> <!-- observaciones-->

						<div class="col-sm-8">
							@if($tramite['observaciones']==" ") 
								<input id="textarea_motivo_m_" class="form-control col-lg-12 " type="text" value=" " disabled>
							@else 
								<textarea id="textarea_motivo_m_" class="form-control" cols="80" disabled>{{$tramite['observaciones']}}</textarea>																		     
							@endif
						</div>
				</div>
			@else
				{{form::hidden('subagente',$tramite['subagente'],array('id'=>'subagente'))}}
			@endif
			@if($tramite['id_tipo_tramite']!=6 && $tramite['id_tipo_tramite']!=7)
				@if($tramite['id_tipo_tramite']!=2 || ($tramite['id_tipo_tramite']==2 && $tramite['suba_a_ag'] && $tramite['nuevo_permiso']==0))
					<div class="alert alert-warning form-group form-horizontal col-lg-12">
						<br>
						@if($tramite['id_tipo_tramite']==1)
							<label id="lnuevo_domicilio" class="control-label col-xs-4 col-lg-3">Nuevo Domicilio:</label>
								<div class="col-sm-3 col-lg-8">
									{{Form::textarea('nuevo_domicilio', $tramite['nuevo_domicilio'], array('id' => 'nuevo_domicilio', 'class'=>'form-control col-xs-3' , 'data-toggle'=>'tooltip', 'readonly'=>'true','rows'=>3))}}
								</div>
						@elseif($tramite['id_tipo_tramite']==2 && $tramite['suba_a_ag'] && $tramite['nuevo_permiso']==0)<!--  && $tramite['id_estado_tramite']>=7 --> 
							<label id="lnueva_categoria" class="control-label col-xs-4 col-lg-4">Nueva Red:</label>
									<div class="col-sm-3 col-lg-8">
							  @if($tramite['id_estado_tramite']<7)
								{{Form::text('nueva_categoria', "Aún no establecida", array('id' => 'nueva_categoria', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
							  @else
								{{Form::text('nueva_categoria', $tramite['nro_red_nueva'], array('id' => 'nueva_categoria', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
							  @endif
							  {{form::hidden('nueva_red','',array('id'=>'nueva_red'))}}
									</div>
						@elseif($tramite['id_tipo_tramite']==3)
							<label id="lnuevo_titular" class="control-label col-xs-4 col-lg-3">Nuevo Titular:</label>
							<div class="col-sm-3 col-lg-8">
								{{Form::text('nuevo_titular', $tramite['persona_nt']['apellido_nombre_razon'], array('id' => 'nuevo_titular', 'class'=>'form-control col-xs-3' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
									</div>
						@elseif($tramite['id_tipo_tramite']==4)
							<label id="lnueva_red" class="control-label col-xs-4 col-lg-4">Nueva Red:</label>
							<div class="col-sm-3 col-lg-6">
								{{Form::text('nueva_red_dependencia', $tramite['nueva_red_dependencia'], array('id' => 'nueva_red_dependencia', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
							</div>
						@elseif($tramite['id_tipo_tramite']==8)
							<label id="lnuevo_titular" class="control-label col-xs-4 col-lg-3">Nuevo CoTitular:</label>
							<div class="col-sm-3 col-lg-8">
								{{Form::text('nuevo_titular', $tramite['persona_nt']['apellido_nombre_razon'], array('id' => 'nuevo_titular', 'class'=>'form-control col-xs-3' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
							</div>
						@elseif($tramite['id_tipo_tramite']==11)
							<label id="lbaja_titular_nombre" class="control-label col-xs-4 col-lg-3">Razón Social:</label>
							<div class="col-sm-3 col-lg-8">
								{{Form::text('cotitular_a_borrar_nombre', $tramite['cotitular']['nombre_titular'], array('id' => 'titular_a_eliminar_nombre', 'class'=>'form-control col-xs-3' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
							</div>
							<label id="lbaja_titular_cuit" class="control-label col-xs-4 col-lg-3">CUIT/CUIL:</label>
							<div class="col-sm-3 col-lg-8">
								{{Form::text('cotitular_a_borrar_cuit', $tramite['cotitular']['dni_cuit_titular_c'], array('id' => 'titular_a_eliminar_cuit', 'class'=>'form-control col-xs-3' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
							</div>
					    @endif
					</div>
				@endif
			@endif
			<BR/>
			{{Form::open(array('action' => array('TramitesController@actualizarEstadoTramite'), 'method' => 'post', 'id' => 'formulario-estado','class'=>'formulario-estado'))}}
				{{Form::hidden('usu_log', $usu, array('id'=>'usu_log'))}}
				{{Form::hidden('tipo_tramite', $tramite['id_tipo_tramite'], array('id'=>'tipo_tramite'))}}
				<div class="form-group form-horizontal">
						<label id="lestados_t_m" class="control-label col-xs-3 col-sm-4">Estado Tr&aacutemite:</label>
						<div class="col-sm-7">
							@if(count($estados)==1)
								{{Form::select('estados_t', $estados, 1, array('id' => 'estados_t','class'=>'form-control input-sm col-lg-12','style' => 'width:250px','disabled'))}}
							@else	
								{{Form::select('estados_t', $estados, $tramite['id_estado_tramite'], array('id' => 'estados_t', 'class'=>'form-control input-sm col-lg-12','style' => 'width:250px','autofocus'=>"", 'onfocus'=>'siguienteCampo="textoComentarios_m"; nombreForm=this.form.id'))}}
							@endif
						</div>
				</div>

				{{Form::hidden('nroTramite',$tramite['nro_tramite'],  array('id' => 'nroTramite'))}}
				{{Form::hidden('estadoI',$tramite['id_estado_tramite'],  array('id' => 'estadoI'))}}
				{{Form::hidden('nuevo_permiso',$tramite['nuevo_permiso'], array('id'=>'nuevo_permiso'))}}
				@if ($tramite['id_tipo_tramite']==4)
					{{Form::hidden('nro_red',$tramite['nro_red'], array('id'=>'nro_red'))}}
					{{Form::hidden('nro_nuevo_sbag',$tramite['nro_nuevo_sbag'], array('id'=>'nro_nuevo_sbag'))}}
				@endif
				<br/>
				<div id="divCambioEstado" style="display: none">
					<BR/>
					<div class="form-group form-horizontal col-lg-12">
						<label id="comentarios" for="lcomentario" class="control-label col-xs-3">Comentarios:</label>
						<div class="col-sm-8">	
							<textarea id="textoComentarios_m" class="form-control input-sm col-lg-push-11" name="textoComentarios_m" rows="4" cols="80" placeholder="Ingrese un comentario..." onfocus='siguienteCampo="btn_aplicar"; nombreForm=this.form.id'> </textarea>
						</div>
					</div>
					<div class="form-group form-horizontal">
						<input type="button" id="btn_aplicar" name="btn_aplicar" value="Aplicar" class="btn-primary col-lg-2 pull-right" onfocus='siguienteCampo = "fin";nombreForm=this.form.id' >
					</div>
					
				</div>
				<BR/>
				<div id="divEstadosResolExp" class="alert alert-warning col-xs-12" style="display: none">
				
					<div id="divSubAAg" class="form-group form-horizontal" style="display: none">
						
						@if ($tramite['id_tipo_tramite']==2)
							@if($tramite['nuevo_permiso']==1)
								{{Form::hidden('cp_localidad_m_',$datosPermiso['cp_localidad'],array('id'=>'cp_localidad_m_'))}}
							@endif
							{{Form::hidden('nro_nueva_red_original',$tramite['nro_red_nueva'],array('id'=>'nro_nueva_red_original'))}}
							<div class="form-group">
								<label id="lnro_nueva_red" for="lnro_nueva_red" class="control-label col-xs-3 col-lg-4">Nº Nueva Red:</label>
								<div class="form-horizontal col-sm-3">
									{{Form::text('nro_nueva_red', $tramite['nro_red_nueva'], array('id' => 'nro_nueva_red', 'class'=>'form-control input-sm col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'maxlength' =>'5', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.resolucion.id;nombreForm=this.form.id','autocomplete'=>'off'))}}  
									
								</div>
								<span id="icono_ok" class="glyphicon glyphicon-ok form-horizontal"></span>
								<span id="icono_error" class="glyphicon glyphicon-remove form-horizontal" style="display: none"></span>
							</div>
							<div class="form-group">
								{{Form::label('lcbu', 'CBU:', array('id'=>'lcbu','class'=>'control-label col-xs-3 col-lg-4'))}}
								<div class="col-sm-3">
								{{Form::text('cbu', $tramite['cbu'], array('id' => 'cbu', 'class'=>'form-control input-sm col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.resolucion.id;nombreForm=this.form.id','autocomplete'=>'off'))}}  
								</div>
							</div>
						@endif
					</div>
					<div id="divAgASub" class="form-group form-horizontal col-xs-12" style="display: none">	
						@if ($tramite['id_tipo_tramite']==2)					
							<div class="form-group">
								{{Form::label('Nº Red:', '', array('id'=>'lnro_red','class'=>'control-label col-xs-3 col-md-4'))}}
								<div class="col-sm-2">
									{{Form::text('nro_red', $tramite['nro_red'], array('id' => 'nro_red', 'class'=>'form-control ' ,'maxlength' => '4', 'data-toggle'=>'tooltip', 'data-placement'=>'right', 'autofocus'=>"", 'onfocus'=>'siguienteCampo="cbu"; nombreForm=this.form.id','readonly'=>'readonly'))}} 
								</div>
								<div class="col-md-1" style="width: 0.5em;">
									{{Form::label('/', '', array('id'=>'lnro_subag','class'=>'control-label'))}}
								</div>							
								<div class="col-md-1">
									{{Form::text('nro_nuevo_sbag', $tramite['nro_nuevo_sbag'], array('id' => 'nro_nuevo_sbag', 'class'=>'form-control ' ,'maxlength' => '3', 'data-toggle'=>'tooltip', 'data-placement'=>'right', 'autofocus'=>"", 'onfocus'=>'siguienteCampo="cbu"; nombreForm=this.form.id', 'style'=>"width: 4em;" ))}} 
								</div>	
								<div class="col-md-1">
									<span id="icono_ok" class="glyphicon glyphicon-ok form-horizontal"></span>
									<span id="icono_error" class="glyphicon glyphicon-remove form-horizontal" style="display: none"></span>
								</div>	
								<div class="col-sm-6" id="div_razon_social" style="display: none">
									{{Form::text('razon_social', '', array('id'=>'razon_social','class'=>'form-control col-xs-3', 'readonly'=>'readonly'))}} 
								</div>							
							</div>	
						@endif									
					</div>

					<div id="divCuit" class="form-group form-horizontal col-xs-12" style="display: none">
						@if ($tramite['id_tipo_tramite']==3 || $tramite['id_tipo_tramite']==8)
							{{Form::hidden('cuit_actual',$tramite['persona_nt']['cuit'],array('id'=>'cuit_actual'))}}
							{{Form::hidden('tienePI',$tramite['tieneTramitePI'],  array('id' => 'tienePI'))}}
							{{Form::hidden('sexo_actual',$tramite['persona_nt']['sexo'],array('id'=>'sexo_actual'))}}
							{{Form::hidden('tipo_soc_actual',$tramite['persona_nt']['tipo_sociedad'],array('id'=>'tipo_soc_actual'))}}
							{{Form::hidden('email_actual',$tramite['persona_nt']['email'],  array('id' => 'email_actual'))}}
							{{Form::hidden('sit_gan_actual',$tramite['persona_nt']['situacion_ganancia'],  array('id' => 'sit_gan_actual'))}}
							{{Form::hidden('fecha_nacimiento',$tramite['persona_nt']['fecha_nacimiento'],  array('id' => 'fecha_nacimiento'))}}
							<div class="form-group form-horizontal">
								{{Form::label('cuit', 'Cuit:', array('id'=>'lcuit','class'=>'control-label col-xs-4'))}}
								<div class="col-xs-6 col-sm-4">
								{{Form::text('cuit', $tramite['persona_nt']['cuit'], array('id' => 'cuit', 'class'=>'form-control input-sm col-xs-3 text-right' ,'maxlength' => '13', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.cbu.id;nombreForm=this.form.id','autocomplete'=>'off'))}}  
								</div>
							</div>
							<div class="form-group" id="div_tipo_persona">
								{{Form::label('ltipo_persona', 'Persona:', array('class'=>'control-label col-xs-4'))}}
								<div class="col-xs-4 col-sm-3">
									{{Form::select('tipo_persona', $tipo_persona, $tramite['persona_nt']['tipo_persona'], array('id' => 'tipo_persona', 'class'=>'form-control col-xs-3 input-sm','autofocus'=>'""'))}}
								</div>
							
								<div id="sexo_persona_fisica" class="form-group form-horizontal">
									<div class="form-group">
										{{Form::label('lsexo', 'Sexo:', array('class'=>'control-label col-xs-1 col-lg-2'))}}
										<div class="col-xs-3 col-sm-3 col-lg-2">
										  <label class="radio-inline">
										    <input type="radio" name="sexo_persona" id="mujer" value="F" @if($tramite['persona_nt']['sexo']=='F') checked @endif onfocus='siguienteCampo = this.form.hombre.id;nombreForm=this.form.id'>F
										  </label>
										
										  <label class="radio-inline">
										    <input type="radio" name="sexo_persona" id="hombre" value="M" @if($tramite['persona_nt']['sexo']=='M') checked @endif onfocus='siguienteCampo = this.form.tipo_situacion.id;nombreForm=this.form.id'>M
										  </label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-xs-12 col-sm-12 col-lg-12">
										{{Form::label('lfecha_nac', 'Fecha nacimiento:', array('id'=>'lfecha_nac','class'=>'control-label col-xs-3 col-lg-3 col-lg-offset-1'))}}
										<div class="col-sm-2">
										{{Form::text('fecha_nac', '', array('id' => 'fecha_nac', 'class'=>'datepicker form-control ' ,'maxlength' => '10', 'pattern'=>"\d{1,2}/\d{1,2}/\d{4}",'placeholder'=>'dd/mm/aaaa','data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo = this.form.tipo_sociedad;nombreForm=this.form.id','autocomplete'=>'off'))}}  
										</div>
									</div>
								</div>
								<div id='tipo_persona_juridica' class="form-group form-horizontal">
									<div class="form-group col-xs-12 col-sm-12 col-lg-12">
										{{Form::label('ltipo_sociedad', 'Tipo Sociedad: ', array('class'=>'control-label col-xs-5 col-sm-2 col-lg-4 div_sin_padding'))}}
										<div class="col-sm-2 col-lg-2 div_sin_padding">

										@if($tramite['persona_nt']['tipo_persona']=='F')
											{{Form::select('tipo_sociedad', $tipo_sociedad, "ni", array('id' => 'tipo_sociedad', 'class'=>'form-control col-xs-3 col-lg-offset-2 input-sm', 'onfocus'=>'siguienteCampo = this.form.cuit.id;nombreForm=this.form.id'))}}
										@else
											{{Form::select('tipo_sociedad', $tipo_sociedad, $tramite['persona_nt']['tipo_sociedad'], array('id' => 'tipo_sociedad', 'class'=>'form-control col-xs-3 col-lg-offset-2 input-sm', 'onfocus'=>'siguienteCampo = this.form.cuit.id;nombreForm=this.form.id'))}}
										@endif
										</div>	
									</div>									
								</div>
							</div><!-- Fin div de tipo de persona-->
							<div class="form-group form-horizontal" id="div_ganancias">
								<div class="form-group">
									{{Form::label('ltipo_situacion_ganancia', 'Sit. Ganancias: ', array('class'=>'control-label col-xs-4 col-sm-4 col-lg-4'))}}
									<div class="col-xs-5 col-sm-4 col-lg-4">
										{{Form::select('tipo_situacion', $tipo_situacion, '', array('id' => 'tipo_situacion', 'class'=>'form-control col-xs-3 input-sm','onfocus'=>'siguienteCampo = this.form.referente.id;nombreForm=this.form.id'))}}
									</div>	
								</div>	

<!-- Anulado ADEN - 2024-04-24											
								<div class="form-group">
									{{Form::label('Nº Ingresos brutos:', '', array('id'=>'lingresos','class'=>'control-label col-xs-4 col-sm-4 col-lg-4'))}}
									<div class="col-xs-3 col-sm-3 col-lg-3" id="div_ingresos">
									{{Form::text('ingresos', $tramite['persona_nt']['nro_ingresos'], array('id' => 'ingresos', 'class'=>'form-control col-xs-12 text-right input-sm' , 'maxlength' => '12', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.cbu.id;nombreForm=this.form.id'))}}  
									</div>
								</div>
-->								
							</div>
<!-- Anulado ADEN - 2024-04-24											
							<div class="form-group form-horizontal">
								{{Form::label('lcbu', 'CBU:', array('id'=>'lcbu','class'=>'control-label col-xs-4'))}}
								<div class="col-xs-4 col-sm-3">
								{{Form::text('cbu', $tramite['persona_nt']['cbu'], array('id' => 'cbu', 'class'=>'form-control input-sm col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.referente.id;nombreForm=this.form.id','autocomplete'=>'off'))}} 
								</div>
							</div>
-->						
							<div class="form-group">	
								{{Form::label('Referente agencia:', '', array('id'=>'lreferente','class'=>'control-label col-xs-4'))}}
								<div class="col-xs-5 col-sm-5">
								{{Form::text('referente', $tramite['persona_nt']['referente'], array('id' => 'referente', 'class'=>'form-control input-sm text-right','data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.datos_contacto.id;nombreForm=this.form.id', 'maxlength'=>'255'))}}				
								</div>
							</div>
							<div class="form-group">	
								{{Form::label('Datos contacto:', '', array('id'=>'ldatos_contacto','class'=>'control-label col-xs-4 col-sm-4 col-lg-4'))}}
								<div class="col-xs-5 col-sm-5">  
									{{Form::textarea('datos_contacto', $tramite['persona_nt']['datos_contacto'], array('id' => 'datos_contacto', 'class'=>"form-control input-sm text-right",'maxlength'=>'255' ,'onfocus'=>'siguienteCampo = this.form.email.id;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'3'))}}
								</div>
							</div>
							<div class="form-group">	
								{{Form::label('Email:', '', array('id'=>'lemail','class'=>'control-label col-xs-4'))}}
								<div class="col-xs-5 col-sm-5">
								{{Form::email('email', $tramite['persona_nt']['email'], array('id' => 'email', 'class'=>'form-control input-sm' , 'placeholder'=>'unemail@mail.com' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.resolucion.id;nombreForm=this.form.id'))}}  
								</div>
							</div>												
						@endif
					</div>	

					<div class="form-group form-horizontal">
						<div class="form-group">
							<label id="lresolucion" for="lresolucion" class="control-label col-xs-4">Nº Resolución:</label>	
							<div class="col-xs-5 col-sm-5">
								<input id="resolucion" name="resolucion" type="text" class="form-control input-sm col-xs-3 text-right" value='' size="" onfocus='siguienteCampo="expediente"; nombreForm=this.form.id'>
							</div>
						</div>
					</div>
					
					<div class="form-group form-horizontal">
						<div class="form-group">
							<label id="lexpediente" for="lexpediente" class="control-label col-xs-4">Nº Expediente:</label>	
							<div class="col-xs-5 col-sm-5">
								<input id="expediente" name="expediente" type="text" class="form-control input-sm col-xs-3 text-right" value='' size="" onfocus='siguienteCampo="btn_aplicar_re"; nombreForm=this.form.id'>
							</div>
						</div>
					</div>
					<div class="form-group">
						<input type="button" id="btn_aplicar_re" name="btn_aplicar_re" value="Aplicar" class="btn-primary col-lg-2 pull-right" onfocus='siguienteCampo = "fin";nombreForm=this.form.id'>
					</div>
					<br>
					<br>
				</div>

			{{Form::close()}}
		<!-- ADJUNTO DE ARCHIVOS -->
			<div class="col-sm-12" id="div_adjuntos">
			
				{{Form::label('ltipo_documento', 'Tipo Documento:', array('id'=>'ltipo_documento','class'=>'control-label col-xs-3 col-sm-4'))}}
				
			<div class="col-sm-6 col-lg-6 ">
				{{Form::select('tipo_documento', $tipo_documento,'' ,array('id' => 'tipo_documento', 'class'=>'form-control col-sm-3 input-medium','autofocus'=>"",'onfocus'=>'siguienteCampo="btn_adjuntar"; nombreForm=this.form.id;'))}}
			</div>
			
			{{Form::button('Adjuntar archivos', array('id' => 'btn_adjuntar', 'name'=>'btn_adjuntar', 'class' => 'col-sm-4 glyphicon glyphicon-plus btn btn-success','disabled'))}}
			<div id="adjuntos" style="display:none" class="col-sm-9">
					{{Form::hidden('nombre_tipo_tramite',$tramite['abreviatura'],  array('id' => 'nombre_tipo_tramite'))}}
					{{Form::hidden('id_permiso',$tramite['nro_permiso'],  array('id' => 'id_permiso'))}}
			       <div class="dropzone" id="imagenes"> </div>
	 		</div>
	 	</div><!-- fin adjunto -->
		<div id="botones" class="col-lg-12">
				<div id="botones2" class="col-xs-6 col-sm-3">
					{{Form::open(array('action' => array('TramitesController@historialEstadoTramite'), 'method' => 'post', 'id' => 'formulario-historial','class'=>'formulario-historial'))}} 											
						{{Form::hidden('nroTramite',$tramite['nro_tramite'],  array('id' => 'nroTramite'))}}
						{{Form::submit('Historial Tramite', array('id' => 'btn-cargar-historial', 'name'=>'btn-cargar-historial', 'class' => 'btn-primary'))}} 
					{{Form::close()}}
				</div>
				{{Form::hidden('estado_t',$tramite['id_estado_tramite'],  array('id' => 'id_est_t'))}}
				{{Form::hidden('id_tipo_tramite',$tramite['id_tipo_tramite'],  array('id' => 'id_tipo_tramite'))}}
				{{Form::hidden('planCompleto',$tramite['planCompleto'],  array('id' => 'planCompleto'))}}
				
				<div id="div_btn_plan_pdf" class="col-xs-3 col-sm-3" style="display:none">
					{{Form::button('Plan PDF', array('id' => 'btn_plan_pdf', 'name'=>'btn_plan_pdf', 'class' => 'btn btn-primary'))}} 
				</div>
				<div id="div_btn_historial" class="col-xs-3 col-sm-3">
						{{Form::button('Imprimir Historial', array('id' => 'btn_imprimir_historial', 'name'=>'btn_imprimir_historial', 'class' => 'btn-primary'))}} 
				</div>
				<div id="div_btn_modificar_tramite" class="col-xs-3 col-sm-3" style="display:none">
					{{Form::open(array('action' => array('TramitesController@modificarTramite'), 'method' => 'post', 'id' => 'form_modif_tra','class'=>'form_modif_tra'))}}
						{{Form::hidden('nroTramite',$tramite['nro_tramite'],  array('id' => 'nroTramite'))}}
						{{Form::submit('Modificar Trámite', array('id' => 'btn_modificar_tramite', 'name'=>'btn_modificar_tramite', 'class' => 'btn-primary'))}} 
					{{Form::close()}}
				</div>
				<div id="btnes_car_sol" style="display:none">
					<div id="botones2" class="col-xs-6 col-sm-5">
						{{Form::button('Imprimir Carátula', array('id' => 'btn-imprimir-caratula', 'name'=>'btn-imprimir-caratula', 'class' => 'btn-primary'))}} 
					</div>
					<div id="botones2" class="col-xs-5 col-sm-5">
						{{Form::button('Imprimir Solicitud!', array('id' => 'btn-imprimir-nota', 'name'=>'btn-imprimir-nota', 'class' => 'btn-primary'))}}
					</div>
					<div>
					     <p>Este es el form</p>
					</div>
				</div>
				<div id="div_btn_eval_dom" class="col-xs-3 col-sm-3" style="display:none">
					{{Form::open(array('action' => array('TramitesController@cargarEvaluacionDomicilio'), 'method' => 'post', 'id' => 'formulario_evaluacion','class'=>'formulario_evaluacion'))}} 											
						{{Form::hidden('nroTramite',$tramite['nro_tramite'],  array('id' => 'nroTramite'))}}
						{{Form::submit('Inspección', array('id' => 'btn_eval_dom', 'name'=>'btn_eval_dom', 'class' => 'btn btn-primary'))}}
					{{Form::close()}} 					
				</div>
		</div><!-- fin div botones -->
		
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
		<div id="historial" style="display: none;">
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

<div class="modal-footer" id="fmodal" >
	<div class="col-xs-12 col-sm-12 col-md-10 col-lg-offset-10 col-lg-1">
		<BR/>
    	<button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
   </div>
</div>

