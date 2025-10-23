@extends('layouts.base')
@section('javascript')
		{{HTML::script("js/jquery-ui.js", array("type" => "text/javascript"))}}
		{{HTML::script("js/js-datepicker.js", array("type" => "text/javascript"))}}
		{{HTML::script("js/js-localidades.js", array("type" => "text/javascript"))}}
		{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
		{{HTML::script("js/js-validacuit.js", array("type" => "text/javascript"))}}
		{{HTML::script("js/js-modificartramite.js?var=".rand(), array("type" => "text/javascript"))}}

@stop

@section('css')
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
@stop

@section('contenido')
	<div class="row">
		<div class="col-lg-12">
			<h1>Modificar Trámite - {{$tramite['titulo']}}</h1>
		</div>
		<div id="errores" class="bs-example col-lg-6 col-lg-offset-1">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		    </div>
		</div>
	</div>
		</br>
	<div class="row-border">
		{{Form::open(array('action' => array('TramitesController@modificarTramite_add'), 'method' => 'post', 'id' => 'formulario_modificar_tramite', 'class'=>'form-inline'))}}
			{{Form::hidden('nro', $tramite['nro_tramite'], array('id' => 'nro'))}}
			{{Form::hidden('tipo_tramite', $tramite['id_tipo_tramite'])}}
			{{Form::hidden('estado_tramite', $tramite['id_estado_tramite'])}}
			{{Form::hidden('agente_tramite', $tramite['agente'])}}
			{{Form::hidden('subagente_tramite', $tramite['subagente'])}}
			{{Form::hidden('tipo_usuario', $tipo_usuario)}}
			{{Form::hidden('urlRetorno', $urlRetorno, array('id'=>'urlRetorno'))}}

			<!-- Campos comunes a todos los trámites-->
			<div id="camposComunes" class="col-lg-12">
				<div class="form-group col-md-4">
					<label class="control-label col-xs-12 col-lg-12 text-right" id="nro_seguimiento">Nº Seguimiento:</label>
				</div>
				<div class="form-group col-md-4">
						<input id="nro_tramite" type="text" class="form-control col-xs-3 text-right" value={{$tramite['nro_tramite']}} maxlenght="15" disabled>
				</div>

				</br>
				</br>

				<div class="form-group col-md-4">
					<label id="label_nro_permiso_" class="control-label col-xs-12 col-lg-12 text-right">Permiso:</label>
				</div>
				<div class="form-group col-md-4">
						<input id="nro_permiso_" type="text" class="form-control col-xs-3 text-right" value={{$tramite['nro_permiso']}} disabled>
				</div>

				</br>
				</br>
				
				@if($tramite['nuevo_permiso']==0)

					<div class="form-group col-md-4">
						<label id="agente" class="control-label col-xs-12 col-lg-12 text-right">Agente:</label>
					</div>
					<div class="form-group col-md-2 col-xs-12">
						<input type="text" class="form-control col-xs-3 text-right" value={{$tramite['agente']}}  disabled>
					</div>
					<div class="form-group col-md-3 col-xs-12">
						{{Form::label('subagente_m', 'Sub Agente:', array('id'=>'subagente_m','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
					</div>
					<div class="form-group col-md-1 col-xs-12">
						{{ Form::text('subagente', $tramite['subagente'], ['class' => 'form-control col-xs-4 text-right', 'readonly']) }}
					</div>
					</br>
					</br>
					<div class="form-group col-md-4">
						<label id="raz_soc" class="control-label col-xs-12 col-lg-12 text-right">Razón Social:</label>
					</div>
					<div class="form-group col-md-8">
    					{{Form::textarea('razon_social', $tramite['razon_social'], ['class' => 'form-control col-lg-12', 'rows' => 2, 'cols' => 50, 'readonly'])}}

					</div>
					</br>
				@endif
			</div>
			<!-- fin Campos comunes a todos los trámites-->

			</br>

			<!-- CAMBIO DE DOMICILIO -->
			@if($tramite['id_tipo_tramite']==1)
				<div id="modificarDomicilio">
					<div class="alert alert-warning col-sm-12">
						{{Form::label('titulo', 'Datos a Modificar:', array('id'=>'ldatos','class'=>'control-label col-lg-4 col-xs-12'))}}
						<div class="col-md-12">
							<div class="col-lg-4 col-md-4">
								{{Form::label('Domicilio comercial:', '', array('id'=>'ldomicilio_comercial','class'=>'control-label col-xs-12 col-sm-4 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-6 col-md-4">
								{{Form::text('domicilio_comercial', $tramite['nueva_direccion'], array('id' => 'id_domicilio_comercial', 'class'=>'form-control ' , 'data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.referente.id;nombreForm=this.form.id', 'maxlength' => '255'))}}
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-lg-4 col-md-4">
								{{Form::label('Referente agencia:', '', array('id'=>'lreferente','class'=>'control-label col-xs-12 col-sm-4 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-6 col-md-4">
								{{Form::textarea('referente', $tramite['referente'], array('id' => 'referente', 'class'=>'form-control ','data-toggle'=>'tooltip', 'autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.datos_contacto.id;nombreForm=this.form.id', 'maxlength'=>'255', 'rows' => 2, 'cols' => 50))}}
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-lg-4 col-md-4">
								{{Form::label('Datos contacto:', '', array('id'=>'ldatos_contacto','class'=>'control-label col-xs-12 col-sm-4 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-6 col-md-4">
								{{Form::textarea('datos_contacto', $tramite['datos_contacto'], array('id' => 'datos_contacto', 'class'=>"form-control input-medium",'maxlength'=>'255' ,'onfocus'=>'siguienteCampo = this.form.buscarLocalidad.name;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'3'))}}
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-lg-4 col-md-4">
								{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambio','class'=>'control-label col-xs-12 col-lg-12 col-sm-4 text-right'))}}
							</div>
							<div class="col-sm-6 col-lg-6 col-md-4">
								{{Form::textarea('motivo_cd', $tramite['observaciones'], array('id' => 'motivo_cd', 'class'=>"form-control input-medium", 'maxlength'=>'255' ,'onfocus'=>'siguienteCampo = "btn_plan";nombreForm=this.form.id'))}}
							</div>
						</div>
						</br>
						</br>

					</div>


				</div>
			@endif

			<!-- CAMBIO DE CATEGORÍA -->
			@if($tramite['id_tipo_tramite']==2)
				{{Form::hidden('categoria_nueva',$tramite['categoria_nueva'],array('id'=>'categoria_nueva'))}}
				
				{{Form::hidden('es_nuevo_permiso', 0)}}
				<!-- para tipo de usuario agencia/subagencia -->
				@if($tipo_usuario == 'AGENTE' || $tipo_usuario == 'SUBAGENTE')
					{{Form::hidden('nro_red',$tramite['nro_red_nueva'],array('id'=>'nro_red'))}}
					{{Form::hidden('nro_subagente',$tramite['nro_nuevo_sbag'],array('id'=>'nro_subagente'))}}
					<div id="modificarCategoria" >
						<div class="alert alert-warning col-sm-12">
							@if($tramite['nro_pto_vta_anterior']<>0)
								{{Form::label('titulo', 'Datos a Modificar:', array('id'=>'ldatos','class'=>'control-label col-lg-4 col-xs-12'))}}
								<div class="col-md-12">
									<div class="col-lg-4 col-md-4">
										{{Form::label('lcbu', 'CBUx:', array('id'=>'lcbu','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
									</div>
									<div class="col-md-4">
										{{Form::text('cbu',$tramite['cbu'], array('id' => 'cbu', 'class'=>'form-control input-sm col-xs-3' , 'data-toggle'=>'tooltip','onfocus'=>'siguienteCampo = "motivo_cc";nombreForm=this.form.id','autocomplete'=>'off'))}}
									</div>
								</div>
								</br>
								</br>
								</br>
							@endif
							<div class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambioc','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-sm-4 col-lg-8 col-md-4">
									{{Form::textarea('motivo_cc', $tramite['observaciones'], array('id' => 'id_motivo_cambio_c', 'class'=>"form-control input-medium", 'maxlength'=>'255' ,'autofocus'=>"",'onfocus'=>'siguienteCampo="btn_guardar"; nombreForm=this.form.id'))}}
								</div>

							</div>
						</div>
					</div>
				
				<!-- para tipo de usuario CAS -->
				@else	
					
					<div id="modificarCategoria" >
					<div class="alert alert-warning col-sm-12">
							<div class="col-md-12">
								<div class="form-group col-md-4">
									{{Form::label('Nº Red:', '', array('id'=>'lnro_red','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-md-4">
									{{Form::text('nro_red', $tramite['nro_red_nueva'], array('id' => 'nro_red', 'class'=>'form-control text-right' ,'maxlength' => '4', 'data-toggle'=>'tooltip', 'data-placement'=>'right', 'autofocus'=>"", 'onfocus'=>'siguienteCampo="nro_subagente"; nombreForm=this.form.id'))}} 
								</div>
							</div>
							<div class="col-md-12">
								<div class="col-md-12" id="div_razon_social" style="display: none">
									{{Form::text('razon_social_nr', '', array('id'=>'razon_social_nr','class'=>'col-md-12', 'readonly'=>'readonly'))}} 
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group col-md-4">	
									{{Form::label('lnro_subagente', 'Subagente:', array('id'=>'lnro_subagente', 'class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								@if($tramite['categoria_nueva'] == 1)
								<div class="col-md-4">
									{{Form::text('nro_subagente',$tramite['nro_nuevo_sbag'], array('id' => 'nro_subagente', 'class'=>'form-control text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo="motivo_cc"; nombreForm=this.form.id'))}}  
								</div>
								@else
								<div class="col-md-4">
									{{Form::text('nro_subagente',$tramite['nro_nuevo_sbag'], array('id' => 'nro_subagente', 'class'=>'form-control text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo="motivo_cc"; nombreForm=this.form.id'))}}  
								</div>
								@endif
							</div>
					</div>
						
						@if($tramite['nro_pto_vta_anterior']<>0)
							{{Form::label('titulo', 'Datos a Modificar:', array('id'=>'ldatos','class'=>'control-label col-lg-4 col-xs-12'))}}
							<div class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('lcbu', 'CBUy:', array('id'=>'lcbu','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-md-4">
									{{Form::text('cbu',$tramite['cbu'], array('id' => 'cbu', 'class'=>'form-control input-sm col-xs-3' , 'data-toggle'=>'tooltip','onfocus'=>'siguienteCampo = "motivo_cc";nombreForm=this.form.id','autocomplete'=>'off'))}}
								</div>
							</div>
							</br>
							</br>
							</br>
						@endif
						<div class="col-md-12">
							<div class="col-lg-4 col-md-4">
								{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambioc','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-4 col-lg-8 col-md-4">
								{{Form::textarea('motivo_cc', $tramite['observaciones'], array('id' => 'id_motivo_cambio_c', 'class'=>"form-control input-medium", 'maxlength'=>'255' ,'autofocus'=>"",'onfocus'=>'siguienteCampo="btn_guardar"; nombreForm=this.form.id'))}}
							</div>

						</div>
					</div>
				</div>
					
				@endif
				
			@endif

			<!-- CAMBIO DE TITULAR / INCORPORACIÓN COTITULAR / FALLECIMIENTO -->
			@if($tramite['id_tipo_tramite']==3 || $tramite['id_tipo_tramite']==8 ||  $tramite['id_tipo_tramite']==12)
				{{Form::hidden('tipo_persona',$tramite['persona_nt']['tipo_persona'],array('id'=>'tipo_persona'))}}

			    <input type="radio" name="sexo_persona" style="visibility: hidden;" id="mujer" value="F" @if($tramite['persona_nt']['sexo']=='F') checked @endif onfocus='siguienteCampo = this.form.hombre.id;nombreForm=this.form.id'>

			    <input type="radio" name="sexo_persona" style="visibility: hidden;" id="hombre" value="M" @if($tramite['persona_nt']['sexo']=='M') checked @endif onfocus='siguienteCampo = this.form.tipo_situacion.id;nombreForm=this.form.id'>


				<div id="modificarTitular" >
					<div class="alert alert-warning col-sm-12">
						{{Form::label('titulo', 'Datos a Modificar:', array('id'=>'ldatos','class'=>'control-label col-lg-4 col-xs-12'))}}
						
<!-- ini - Agregado ADEN - 2024-10-18 -->
						<br>
						<div class="col-md-12">
							<div class="col-lg-4 col-md-4">
								{{Form::label('lsexo', 'Sexo:', array('class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-lg-8">
							  <label class="radio-inline">
								<input type="radio" name="sexo_persona" id="mujer" value="F" 
									@if($tramite['persona_nt']['sexo'] == 'F') checked @endif
									onfocus='siguienteCampo = this.form.hombre.id;nombreForm=this.form.id'>F
							  </label>
							
							  <label class="radio-inline">
								<input type="radio" name="sexo_persona" id="hombre" value="M" 
									@if($tramite['persona_nt']['sexo'] == 'M') checked @endif
								onfocus='siguienteCampo = this.form.cuit.id;nombreForm=this.form.id'>M
							  </label>
							</div>
						</div>	
						<br>
<!-- fin - Agregado ADEN - 2024-10-18 -->
						
						<div class="col-md-12">
							<div class="col-lg-4 col-md-4">
								{{Form::label('cuit', 'Cuit:', array('id'=>'lcuit','class'=>'control-label col-lg-12 col-xs-12 text-right'))}}
							</div>
							<div class="col-lg-8">
								{{Form::text('cuit', $tramite['persona_nt']['cuit'], array('id' => 'cuit', 'class'=>'form-control ' ,'maxlength' => '13', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.ltipo_doc.id;nombreForm=this.form.id','autocomplete'=>'off'))}}
							</div>
						</div>

						@if($tramite['persona_nt']['tipo_persona'] == 'F')

<!-- ini - Agregado ADEN - 2024-10-18 -->
							<div class="col-md-12" >	
								<div class="col-lg-4 col-md-4">
									{{Form::label('ltipo_doc', 'Tipo documento:', array('id'=>'ltipo_doc','class'=>'control-label col-md-12 col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2">
									{{Form::select('tipo_doc', $tipo_doc, $tramite['persona_nt']['tipo_documento'], array('id' => 'tipo_doc', 'class'=>'form-control col-xs-2', 'onfocus'=>'siguienteCampo = this.form.nro_doc.id;nombreForm=this.form.id'))}}
								</div>
								<div class="col-lg-3 col-md-3">
									{{Form::label('ldoc', 'Nº documento:', array('id'=>'ldoc','class'=>'control-label col-xs-12 col-lg-12 col-md-12 text-right'))}}
								</div>
								<div class="col-sm-3 col-lg-3 div_sin_padding">
									{{Form::text('nro_doc', $tramite['persona_nt']['nro_documento'], array('id' => 'nro_doc', 'class'=>'form-control ' ,'maxlength' => '10','data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo = this.form.fecha_nac.id;nombreForm=this.form.id','autocomplete'=>'off'))}}{{-- ,'pattern'=>'^[1-9]{1,2}[.]\d{3}[.]\d{3}$' 'pattern'=>"^[1-9]{1,2}[0-9]{6,7}$" --}}  
									<div class="col-lg-8" style="color:red">
									@if($errors->any())
										<h4>{{$errors->first('nro_doc')}}</h4>
									@endif
									</div>
								</div>
							</div>
<!-- fin - Agregado ADEN - 2024-10-18 -->

							<div class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('lfecha_nac', 'Fecha nacimiento:', array('id'=>'lfecha_nac','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-lg-8">
									{{Form::text('fecha_nac', $tramite['persona_nt']['fecha_nacimiento'], array('id' => 'fecha_nac', 'class'=>'datepicker form-control ' ,'maxlength' => '10', 'pattern'=>"\d{1,2}/\d{1,2}/\d{4}",'placeholder'=>'dd/mm/aaaa','data-toggle'=>'tooltip', 'onfocus'=>'siguienteCampo = this.form.tipo_ocup.id;nombreForm=this.form.id','autocomplete'=>'off'))}}
								</div>
							</div>
							<div class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('Ocupación:', '', array('id'=>'locupación','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-lg-8">
								 	{{Form::select('tipo_ocup', $tipo_ocup_tit, $tramite['persona_nt']['ocupacion'], array('id' => 'tipo_ocup', 'class'=>'form-control col-xs-2','onfocus'=>'siguienteCampo = this.form.apellido_nombre.id;nombreForm=this.form.id'))}}
								</div>
							</div>
						@endif
						@if($tramite['persona_nt']['tipo_persona'] == 'J')
							<div id='tipo_persona_juridica' class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('ltipo_sociedad', 'Tipo Sociedad: ', array('class'=>'control-label col-xs-12 col-lg-4 text-right'))}}
								</div>
								<div class="col-xs-4 col-sm-8 col-lg-3 col-md-4">
								{{Form::select('tipo_sociedad', $tipo_sociedad, $tramite['persona_nt']['tipo_sociedad'], array('id' => 'tipo_sociedad', 'class'=>'form-control col-xs-3', 'onfocus'=>'siguienteCampo = this.form.cuit.id;nombreForm=this.form.id'))}}
								</div>
							</div>
							<div id="div_pers_jur" class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('Razon Social:', '', array('id'=>'lrazonsocial','class'=>'control-label col-xs-12 col-lg-4 text-rigth'))}}
								</div>
								<div class="col-sm-5 col-md-4">
									{{Form::text('razon_social', $tramite['persona_nt']['apellido_nombre_razon'], array('id' => 'razon_social', 'class'=>'form-control ' ,'maxlength' => '50', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.domicilio.id;nombreForm=this.form.id'))}}
								</div>
							</div>
						@endif
						@if($tramite['persona_nt']['tipo_persona'] == 'F')
							<div id="div_pers_fisica_n" class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('Apellido y nombre:', '', array('id'=>'lnombre','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-lg-8 col-sm-5 col-md-4">
									{{Form::textarea('apellido_nombre', $tramite['persona_nt']['apellido_nombre_razon'], array('id' => 'apellido_nombre', 'class'=>'form-control ' ,'maxlength' => '50',  'data-toggle'=>'tooltip', 'data-placement'=>'right','placeholder'=>'apellido nombre','onfocus'=>'siguienteCampo = this.form.apellido_mat.id;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'2'))}}
								</div>
							</div>

							<div id="div_pers_fisica_a" class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('Apellido materno:', '', array('id'=>'lapellido_mat','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-lg-8 col-sm-5 col-md-4">
									{{Form::textarea('apellido_mat', $tramite['persona_nt']['apellido_materno'], array('id' => 'id_apellido_mat', 'class'=>'form-control ' ,'maxlength' => '50', 'placeholder'=>'apellido materno', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.tipo_situacion.id;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'2'))}}
								</div>
							</div>
						@endif
						
						@if($tramite['id_tipo_tramite'] == 12)
						<div class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('ltipo_rel', 'Tipo Relación:', array('id'=>'ltipo_rel','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-lg-8">
									{{Form::select('tipo_rel',$lista_tipo_relacion, $tipo_relacion, array('id' => 'tipo_rel', 'class'=>'form-control', 'onfocus'=>'siguienteCampo = this.form.tipo_situacion.id;nombreForm=this.form.id'))}}
								</div>
						</div>
							
							<div class="col-md-12">
								<div class="col-lg-4 col-md-4">
									{{Form::label('ltipo_vin', 'Tipo Vínculo:', array('id'=>'ltipo_vin','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
								</div>
								<div class="col-lg-8">
									{{Form::select('tipo_vin',$lista_tipo_vinculo,$tipo_vinculo, array('id' => 'tipo_vin', 'class'=>'form-control', 'onfocus'=>'siguienteCampo = this.form.tipo_situacion.id;nombreForm=this.form.id'))}}
									{{-- Form::hidden('tipo_vinh', $tipo_vinculo , array('id'=>'tipo_vinh')) --}}
								</div>
							</div>
							
							{{Form::hidden('tipo_vinh', $tipo_vinculo , array('id'=>'tipo_vinh'))}}
						@endif
						
						
						
						
						<div class="col-md-12">
							<div class="col-md-4">
								{{Form::label('ltipo_situacion_ganancia', 'Situación de Ganancias:', array('class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-md-2">
								@if($tramite['id_tipo_tramite'] == 12) <!-- fallecimiento, salta a email, el resto a domicilio -->
									{{Form::select('tipo_situacion', $tipo_situacion, $tramite['persona_nt']['situacion_ganancia'], array('id' => 'tipo_situacion', 'class'=>'form-control col-lg-12 col-xs-3','onfocus'=>'siguienteCampo = this.form.id_email.id;nombreForm=this.form.id'))}}
								@else
									{{Form::select('tipo_situacion', $tipo_situacion, $tramite['persona_nt']['situacion_ganancia'], array('id' => 'tipo_situacion', 'class'=>'form-control col-lg-12 col-xs-3','onfocus'=>'siguienteCampo = this.form.domicilio.id;nombreForm=this.form.id'))}}
								@endif

							</div>
						</div>
<!-- ADEN - 2024-08-06						
						<div class="col-md-12">
							<div class="col-md-4">
								{{Form::label('Nº Ing. brutos:', '', array('id'=>'lingresos','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-xs-12 col-sm-2 col-md-2">
								{{Form::text('ingresos', $tramite['persona_nt']['nro_ingresos'], array('id' => 'ingresos', 'class'=>'form-control col-xs-12' , 'maxlength' => '12', 'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.cbu.id;nombreForm=this.form.id'))}}
								@if ($errors->get('ingresos'))
									<error_dt for="ingresos" class="error">{{ $errors->first('ingresos') }}</error_dt>
								@endif
							</div>
						</div>
						
						<div class="col-md-12">
							<div class="col-md-4">
								{{Form::label('lcbu', 'CBUz:', array('id'=>'lcbu','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-3 col-md-4">
							{{Form::text('cbu', $tramite['persona_nt']['cbu'], array('id' => 'cbu', 'class'=>'form-control ' , 'data-toggle'=>'tooltip', 'data-placement'=>'right','autocomplete'=>'off','onfocus'=>'siguienteCampo = this.form.domicilio.id;nombreForm=this.form.id'))}}
							</div>
						</div>
-->						
						<div class="col-md-12">
							<div class="col-md-4">
								{{Form::label('Domicilio Particular:', '', array('id'=>'ldomicilio','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-5 col-md-4">
								@if($tramite['id_tipo_tramite'] == 12)
									{{Form::textarea('domicilio', $tramite['persona_nt']['domicilio_particular'], array('id' => 'domicilio', 'class'=>"form-control input-medium",'maxlength'=>'255' , 'onfocus'=>'siguienteCampo = this.form.id_email.id;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'3','readonly'))}}
								@else
									{{Form::textarea('domicilio', $tramite['persona_nt']['domicilio_particular'], array('id' => 'domicilio', 'class'=>"form-control input-medium",'maxlength'=>'255' , 'onfocus'=>'siguienteCampo = this.form.id_email.id;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'3'))}}
								@endif
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-4">
								{{Form::label('Email:', '', array('id'=>'lemail','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-5 col-md-4">
								{{Form::email('email', $tramite['persona_nt']['email'], array('id' => 'email', 'class'=>'form-control ' , 'placeholder'=>'unemail@mail.com' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.referente.id;nombreForm=this.form.id'))}}
							</div>
						</div>

						<div class="col-md-12">
							<div class="col-md-4">
								{{Form::label('Referente agencia:', '', array('id'=>'lreferente','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-5 col-md-4">
								{{Form::text('referente', $tramite['persona_nt']['referente'], array('id' => 'referente', 'class'=>'form-control ','data-toggle'=>'tooltip', 'data-placement'=>'right','autofocus'=>"",'onfocus'=>'siguienteCampo = this.form.datos_contacto.id;nombreForm=this.form.id', 'maxlength'=>'255'))}}
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-4">
								{{Form::label('Datos contacto:', '', array('id'=>'ldatos_contacto','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-5 col-md-4">
								{{Form::textarea('datos_contacto', $tramite['persona_nt']['datos_contacto'], array('id' => 'datos_contacto', 'class'=>"form-control input-medium",'maxlength'=>'255' ,'onfocus'=>'siguienteCampo = this.form.buscarLocalidad.name;nombreForm=this.form.id', 'col'=>'50', 'rows'=>'3'))}}
							</div>
						</div>
						<div id="div_nueva_localidad" class="col-md-12">
							<div class="col-md-4">
								{{Form::label('llocalidades_nueva', 'Localidad:', array('id'=>'llocalidad_nueva', 'class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-5 col-md-4">
							@if($tramite['id_tipo_tramite'] != 12)
								<input list="localidades" name="buscarLocalidad" id="buscarLocalidad" class='form-control' placeholder="Ingrese Localidad" autocomplete="off" onfocus='siguienteCampo=this.form.motivo_ct.id; nombreForm=this.form.id'>
								<datalist id="localidades" >
								</datalist>
							@else
								<input list="localidades" name="buscarLocalidad" id="buscarLocalidad" class='form-control' placeholder="Ingrese Localidad" autocomplete="off" onfocus='siguienteCampo=this.form.motivo_ct.id; nombreForm=this.form.id'  readonly='readonly'>
								<datalist id="localidades" >
								</datalist>
							@endif
							
								@if($errors->has('nueva_localidad'))
						              @foreach($errors->get('nueva_localidad') as $error )
						                   {{ "Localidad no válida." }}
						              @endforeach
						         @endif

								@if($tramite['id_tipo_tramite'] != 12)
									{{Form::hidden('nueva_localidad',$tramite['persona_nt']['id_localidad']['id'],array('id'=>'nueva_localidad'))}}
									{{Form::hidden('nombre_localidad',$tramite['persona_nt']['id_localidad']['nombre'],array('id'=>'nombre_localidad'))}}
								@else
									{{Form::hidden('nueva_localidad',$tramite['persona_nt']['id_localidad']['id'],array('id'=>'nueva_localidad'))}}
									{{Form::hidden('nombre_localidad',$tramite['localidad'],array('id'=>'nombre_localidad'))}}
								@endif

							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-4">
								{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambiot','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-4 col-md-4">
								{{Form::textarea('motivo_ct', $tramite['observaciones'], array('id' => 'motivo_ct', 'class'=>"form-control input-medium",'maxlength'=>'255' ,'onfocus'=>'siguienteCampo = "btn_guardar";nombreForm=this.form.id'))}}
							</div>
						</div>
					</div>
				</div>
			@endif

			<!-- BAJA DE COTITULAR -->
			@if($tramite['id_tipo_tramite']==11)
				<div class="row">
					<br>&nbsp;<br>
				</div>
				<div id="bajaCotitular"> <!-- inicio baja cotitular -->
					<!-- <div class="row-border"> -->
						<div class="row">
						    @if(false)
								{{Form::open(array(
									'action' => array('TramitesController@bajaCotitular_add'),
									'method' => 'post',
									'id' => 'formulario-baja-cotitular',
									'class'=>'form-horizontal'))}}
								{{Form::hidden('nombre_tipo_tramite','BC',  array('id' => 'nombre_tipo_tramite'))}}
							@endif
							
							<div class="alert alert-warning col-sm-12" id="baja_cotitular">
								<h4>{{Form::label('lbaja_cotitular', 'Seleccione Cotitular a dar de baja', array('id'=>'lbaja_cotitular','class'=>'text-center col-xs-12 col-lg-12'))}}</h4>
								<table class="table">
									<thead>
											<tr>
												<th></th>
												<th> Apellido y Nombres </th>
												<th> DNI/CUIT </TH>
												<th> Sexo </th>
												<th> Fecha de nacimiento</th>
											</tr>
									</thead>
									<tbody>
											 @foreach($cotitulares as $person)
												<tr>
													<td> {{ Form::radio('cotitular_to_kill', $person->id, ($person->id == $baja_cotitular_id) ) }} </td>
													<td> {{$person->name}} </td>
													<td> {{$person->dni_cuit_titular_c}} </td>
													<td> {{$person->sexo_c}} </td>
													<td> {{$person->fecha_nacimiento_c}} </td>
												</tr>
												@endforeach
									</tbody>
								</table>
								<div class="form-group">
									{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambiot','class'=>'control-label col-xs-1 col-lg-3'))}}
									<div class="col-sm-11 col-lg-9">
										{{Form::textarea('motivo_ct', $tramite['observaciones'], array(
											'id' => 'motivo_ct',
											'class'=>"form-control input-medium",
											'maxlength'=>'255'))}}
									</div>
								</div>
							</div>
						</div>
					<!-- </div> -->
				</div> <!-- fin baja cotitular -->
			@endif

			<!-- CAMBIO DE DEPENDENCIA -->
			@if($tramite['id_tipo_tramite']==4)
				<div id="modificarDependencia">
					<div class="alert alert-warning col-sm-12">
						{{Form::label('titulo', 'Datos a Modificar:', array('id'=>'ldatos','class'=>'control-label col-lg-4 col-xs-12'))}}
						<div class="col-md-12">
							<div class="col-lg-4 col-md-4">
								{{Form::label('lmotivo', 'Motivo:', array('id'=>'lmotivo_cambior','class'=>'control-label col-xs-12 col-lg-12 text-right'))}}
							</div>
							<div class="col-sm-4 col-lg-8 col-md-4">
								{{Form::textarea('motivo_cr', $tramite['observaciones'], array('id' => 'motivo_cr', 'maxlength' => '255', 'class'=>"form-control input-medium", 'onfocus'=>'siguienteCampo = "btn_adjuntar";nombreForm=this.form.id'))}}
							</div>
						</div>
					</div>
				</div>
			@endif

			{{Form::button('Guardar', array('id' => 'btn_guardar', 'name'=>'btn_guardar', 'class' => 'btn-primary'))}}
			{{Form::button('Volver', array('id'=>'btn_volver', 'name'=>'btn_volver', 'class'=>'btn-primary'))}}
			<!-- {{Form::button('Limpiar', array('id'=>'btn_limpiar', 'name'=>'btn_limpiar', 'class'=>'btn-primary', 'onclick'=>'refrescar(this.form.id);'))}} -->

			{{Form::close()}}

			<div id="botonera_2" class="col-sm-12">
				@if($tramite['id_tipo_tramite']==1 || $tramite['id_tipo_tramite']==3 || $tramite['id_tipo_tramite']==4)
					{{Form::open(array('action' => array('TramitesController@llamaPlanEstrategico'), 'method' => 'post', 'id' => 'formulario_plan','class'=>'formulario_plan'))}}
						{{Form::hidden('nroTramite',$tramite['nro_tramite'],  array('id' => 'nroTramite'))}}
						{{Form::hidden('permiso_pe',$tramite['nro_permiso'],  array('id' => 'permiso_pe'))}}
						{{Form::submit('Modificar Plan', array('id' => 'btn_plan', 'name'=>'btn_plan', 'class' => 'btn-primary'))}}
					{{Form::close()}}
				@endif
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





