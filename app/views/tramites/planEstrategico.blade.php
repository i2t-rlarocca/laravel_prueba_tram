@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/jquery-ui.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-planestrategico.js?v=".rand(), array("type" => "text/javascript"))}}
	{{HTML::script("js/js-cargarfotos.js", array("type" => "text/javascript"))}}
	
@stop
@section('css')
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="css/bootstrap-table.css">
@stop
@section('contenido')
	<div class="row">
		<div id="errores" class="bs-example" style="display: none">
		    <div class="alert alert-danger fade in">
		      {{Form::label('mensaje', '', array('id'=>'mensaje'))}}
		    </div>
		</div>
		<div class="col text-center">
			@if(Session::has('tramiteNuevoPermiso'))
				<h1>Plan estratégico y de inversión para nuevo domicilio 
				<br>
				 {{$agencia}}</h1>
			@else
				<h1>Plan estratégico y de inversión para cambio de domicilio 
				<br>
				 {{$agencia}}</h1>
			@endif

		</div>
	</div>		
		
	<div class="row-border">
		@if (strpos($llamada_de,'planEstrategico')!==false)
			{{Form::open(array('action' => array('TramitesController@planEstrategico_add'), 'method' => 'post', 'id' => 'formulario_plan_estrategico', 'class'=>'form-horizontal', 'files'=>true))}}
		@else
			{{Form::open(array('action' => array('TramitesController@planEstrategicoModificado_add'), 'method' => 'post', 'id' => 'formulario_plan_estrategico', 'class'=>'form-horizontal', 'files'=>true))}}
		@endif
		{{-- Domicilio Actual--}}
		<div id="datos_actuales" class="panel panel-warning col-xs-12 col-md-12 col-sm-12 col-lg-12 div_sin_padding">
			<div class="panel-heading">
		      <h4 class="panel-title">
		          Domicilio Actual
		      </h4>
		    </div>
		    <br>
			<div class="form-group col-sm-12 div_sin_padding">
					{{Form::label('ldomicilio_actual', 'Dirección:', array('id'=>'ldomicilio_actual', 'class'=>'control-label col-xs-3 col-lg-3'))}}
					<div class="col-sm-9">
      					{{Form::text('domicilio_actual', '', array('id' => 'domicilio_actual', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
    				</div>
			</div>
			<div class="form-group col-sm-12 div_sin_padding">
					{{Form::label('llocalidad_actual', 'Localidad:', array('id'=>'llocalidad_actual', 'class'=>'control-label col-xs-3 col-lg-3'))}}
					<div class="col-sm-4">
      					{{Form::text('localidad_actual', '', array('id' => 'localidad_actual', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'data-placement'=>'right', 'readonly'=>'true'))}}
    				</div>
			
					{{Form::label('ldpto_actual', 'Departamento:', array('id'=>'ldpto_actual', 'class'=>'control-label col-xs-2'))}}
					<div class="col-sm-3">
      					{{Form::text('departamento', '', array('id' => 'departamento', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'data-placement'=>'right', 'readonly'=>'true'))}}
    				</div>
			</div>
			<div class="form-group col-sm-12 div_sin_padding">
					{{Form::label('lsuperficie_actual', 'Superficie (m2):', array('id'=>'lsuperficie_actual', 'class'=>'control-label col-xs-6 col-sm-3 col-lg-3'))}}
					<div class="col-sm-3">
      					{{Form::text('superficie_actual', '', array('id' => 'superficie_actual', 'class'=>'form-control' ,'data-toggle'=>'tooltip', 'data-placement'=>'right', 'readonly'=>'true'))}}
    				</div>
			
					{{Form::label('lventas_actual', 'Venta (prom. últ. 3 meses):', array('id'=>'lventas_actual', 'class'=>'control-label col-xs-8 col-sm-3 col-lg-3'))}}
					<div class="col-sm-2">
      					{{Form::text('ventas_actual', '', array('id' => 'ventas_actual', 'class'=>'form-control' ,'data-toggle'=>'tooltip', 'data-placement'=>'right', 'readonly'=>'true'))}}
    				</div>
			</div>
			<div class="form-group col-sm-12 div_sin_padding">
					{{Form::label('lpersona_contacto', 'Persona Contacto:', array('id'=>'lpersona_contacto', 'class'=>'control-label col-xs-6 col-sm-3 col-lg-3'))}}
					<div class="col-sm-9">
      					{{Form::text('persona_contacto_a', '', array('id' => 'persona_contacto_a', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'maxlength' => '255','data-placement'=>'right', 'readonly'=>'true'))}}
    				</div>
			</div>
			<div class="form-group col-sm-12 div_sin_padding">
					{{Form::label('lpersona_contacto_datos', 'Contacto:', array('id'=>'lpersona_contacto_datos', 'class'=>'control-label col-xs-3 col-lg-3'))}}
					<div class="col-sm-9">
      					{{Form::textarea('persona_contacto_a_datos', '', array('id' => 'persona_contacto_a_datos', 'class'=>"form-control input-medium", 'maxlength' => '255','data-toggle'=>'tooltip', 'readonly'=>'true', 'rows'=>'2'))}}
    				</div>
			</div>
		</div>
		{{-- Fin Domicilio Actual--}}
		{{-- Módulos--}}
		<div class="panel-group col-xs-12 col-md-12 col-sm-12 div_sin_padding" id="accordion">
			{{-- Domicilio Propuesto--}}
		  <div class="panel panel-info" >
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
		          Domicilio Propuesto
		          <span id="uno" class="glyphicon glyphicon-chevron-up"> </span>
		        </a>
		      </h4>
		    </div>
		    <div id="collapseOne" class="panel-collapse collapse in">
		      <div class="panel-body" >
		      	<h4 class="panel-title" id="titulo_local">
			        <h4><span class="label label-default" id="llocal_c">Local Comercial</span></h4>
			    </h4>
		      	<div class="panel panel-default">
		      		<br>
			        <div class="form-group col-sm-6 ">
						{{Form::label('ldomicilio_nuevo', 'Dirección:', array('id'=>'ldomicilio_nuevo', 'class'=>'control-label col-xs-3'))}}
						<div class="col-sm-9">
							@if (strpos($llamada_de,'planEstrategico')!=0)
	      						{{Form::text('domicilio_nuevo', '', array('id' => 'domicilio_nuevo', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'autofocus'=>'','onfocus'=>'siguienteCampo = this.form.localidad_nuevo.id;nombreForm=this.form.id'))}}
	      					@else
	      						{{Form::text('domicilio_nuevo', '', array('id' => 'domicilio_nuevo', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'autofocus'=>'','onfocus'=>'siguienteCampo = this.form.localidad_nuevo.id;nombreForm=this.form.id', 'readonly'=>'true'))}}
	      					@endif
	    				</div>
					</div>
					<div class="form-group col-sm-6">
							{{Form::label('llocalidad_nuevo', 'Localidad:', array('id'=>'llocalidad_nuevo', 'class'=>'control-label col-xs-3'))}}
							<div class="col-sm-9">
								@if (strpos($llamada_de,'planEstrategico')!=0)
									{{Form::text('localidad_nuevo', '', array('id' => 'localidad_nuevo', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.superficie.id;nombreForm=this.form.id'))}}
								@else
		      					{{Form::text('localidad_nuevo', '', array('id' => 'localidad_nuevo', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.superficie.id;nombreForm=this.form.id', 'readonly'=>'true'))}}
		      					@endif
		    				</div>
					</div>
				</div>

				<div class="panel panel-default col-sm-6 col-lg-6">
					<h4><span class="label label-info">Superficie</span></h4>
					<div class="form-group col-lg-12" id="div_superficie_superior">
						<div class="col-lg-12" id="div_superficie">
							{{ Form::select('superficie',$lista_superficie,Session::get('_old_input.superficie_codigo'),array('id'=>'superficie','class'=>'form-control col-lg-12','onfocus'=>'siguienteCampo = this.form.ubicacion.id;nombreForm=this.form.id')) }}
						</div>
					</div>
				</div>
				<div class="panel panel-default col-sm-6 col-lg-6">
					<h4><span class="label label-info">Ubicación</span></h4>
					
					<div class="form-group col-lg-12" id="div_ubicacion_superior">
						<div class="col-lg-12" id="div_ubicacion">
							{{ Form::select('ubicacion',$lista_ubicacion,Session::get('_old_input.ubicacion_codigo'),array('id'=>'ubicacion','class'=>'form-control col-lg-12','onfocus'=>'siguienteCampo = this.form.vidriera.id;nombreForm=this.form.id')) }}
						</div>
						<div class="col-lg-12" id="div_otra_ubicacion" style="display:none">
							{{Form::label('lotra_ubicacion','Ubicación: ')}}
							{{Form::text('otra_ubicacion', Session::get('_old_input.ubicacion_valor'), array('id' => 'otra_ubicacion', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.vidriera.id;nombreForm=this.form.id'))}}
						</div>
						
					</div>
	    		</div>
	    		<div class="panel panel-default col-sm-12 col-lg-6" id="vidriera">
					<h4><span class="label label-info">Vidriera</span></h4>
					<div class="form-group col-lg-12">
						<div class="col-lg-12">
							{{ Form::select('vidriera',$lista_vidriera,Session::get('_old_input.vidriera_codigo'),array('id'=>'vidriera','class'=>'form-control col-lg-12 vidriera', 'onfocus'=>'siguienteCampo = this.form.rubros_amigos.id;nombreForm=this.form.id')) }}
						</div>
					</div>
					   		
	    		</div>
	    		<div class="panel panel-default col-sm-12 col-lg-6" id="rubros">
					<br>
					<div class="form-group col-sm-12">
	      				{{Form::label('lragencia','Agencia:', array('id'=>'lragencia', 'class'=>'control-label col-xs-6 col-sm-6'))}}
	      				<div class="col-xs-4 col-sm-4 ">
	      					{{Form::text('rubros_agencia','100',array('id'=>'rubros_agencia','class'=>'form-control col-sm-2', 'readonly'=>'true'))}}
	      				</div>
	    				{{Form::label('lporc','%', array('class'=>'control-label col-sm-1 '))}}
	    			</div>
	    			<div class="form-group col-sm-12">
	      				{{Form::label('lramigos','Rubros Afines:', array('id'=>'lramigos', 'class'=>'control-label col-xs-6 col-sm-6 '))}}
	      				<div class="col-xs-4 col-sm-4 ">
	      					{{Form::text('rubros_amigos','0',array('id'=>'rubros_amigos','class'=>'form-control col-sm-2','maxlength' => '2','onfocus'=>'siguienteCampo = this.form.rubros_otros.id;nombreForm=this.form.id'))}}
	      				</div>
	    				{{Form::label('lporc','%', array('class'=>'control-label col-sm-1 '))}}
	    			</div>
	    			<div class="form-group col-sm-12">
	      				{{Form::label('lrotros','Rubros No Afines:', array('id'=>'lrotros', 'class'=>'control-label col-xs-6 col-sm-6 '))}}
	      				<div class="col-xs-4 col-sm-4 ">
	      					{{Form::text('rubros_otros','0',array('id'=>'rubros_otros','class'=>'form-control col-sm-2','maxlength' => '2','onfocus'=>'siguienteCampo = this.form.persona_contacto.id;nombreForm=this.form.id'))}}
	      				</div>
	      				{{Form::label('lporc','%', array('class'=>'control-label col-sm-1 '))}}
	    			</div>
	    			
				</div>
	    		<div class="panel panel-default col-sm-12 col-lg-12">
					<h4>
						<span class="label label-info">Características de Atención</span>
					</h4>
	    			<div class="form-group form-horizontal col-sm-11">
      					{{Form::label('lpersona_contacto_n','Persona Contacto:', array('id'=>'lpersona_contacto_n', 'class'=>'control-label col-sm-5'))}}
      					<div class="col-sm-7">
      					{{Form::text('persona_contacto','',array('id'=>'persona_contacto_n','class'=>'form-control col-sm-8','maxlength' => '255','onfocus'=>'siguienteCampo = this.form.telefono_contacto.id;nombreForm=this.form.id'))}}
      					</div>
	    			</div>
	    			<div class="form-group col-sm-11">
	      				{{Form::label('ltelefono_contacto_n','Teléfono contacto:', array('id'=>'ltelefono_contacto_n', 'class'=>'control-label col-sm-5'))}}
	      				<div class="col-sm-4 col-lg-7">
	      					{{Form::textarea('telefono_contacto','',array('id'=>'telefono_contacto','class'=>'form-control col-sm-8','rows'=>'4','onfocus'=>'siguienteCampo = this.form.cant_empleados.id;nombreForm=this.form.id'))}}
	      				</div>
	    			</div>
	    			<div class="form-group col-sm-11">
	      				{{Form::label('lcant_empleados','Cantidad Empleados en Atención:', array('id'=>'lcant_empleados', 'class'=>'control-label col-sm-5'))}}
	      				<div class="col-sm-2">
	      					{{Form::text('cant_empleados','',array('id'=>'cant_empleados','maxlength' => '3','class'=>'form-control col-sm-2','onfocus'=>'siguienteCampo = this.form.horario.id;nombreForm=this.form.id'))}}
	      				</div>
	    			</div>
	    			<div class="form-group col-sm-11">
	      				{{Form::label('lhorario','Horario:', array('id'=>'lhorario', 'class'=>'control-label col-xs-12 col-sm-5'))}}
	    			
						<div class="col-sm-7 col-lg-4">
							{{ Form::select('horario',$horario,Session::get('_old_input.horario_codigo'),array('id'=>'horario','class'=>'form-control col-lg-12')) }}
						</div>
					
	    			</div>	 
				</div>
				
		      </div>
		    </div>
		  </div>
		  {{-- Fin Domicilio Propuesto--}}
		  {{-- Fotos --}}
		  <div class="panel panel-info">
			<div class="panel-heading">
			  <h4 class="panel-title ">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
				  Fotos del Local Comercial
    			    <span id="dos" class="glyphicon glyphicon-chevron-down"> </span>
				</a>
			  </h4>
			</div>
			<div id="collapseTwo" class="panel-collapse collapse">
			  <div class="panel-body">
			  		<div class="col-xs-6 col-md-6 col-lg-6">
			  			@if(Session::has('_old_input.foto_f'))
  					  		<img  id="vista_previa_ff" alt="Foto Frente Local" src={{Session::get('_old_input.foto_vf')}} style="height: 180px; width: 100%; display: block;" class="img-thumbnail">
  					  	@else
  					  		<img  id="vista_previa_ff" alt="Foto Frente Local" src="images/no-imagen.jpg" style="height: 180px; width: 100%; display: block;" class="img-thumbnail">
  					  	@endif
					  {{ Form::label('lfoto_f','Foto Frente',array('id'=>'lfoto_f','class'=>'')) }}
					  
					  {{ Form::file('foto_f',array('id'=>'foto_f','class'=>'btn','title'=>"Seleccione foto del frente del local")) }}
					  				
					</div>
					<div class="col-xs-6 col-md-6 col-lg-6">

						@if(Session::has('_old_input.foto_i'))
		  					<img  id="vista_previa_fi" alt="Foto Interior Local" src={{Session::get('_old_input.foto_vi')}} style="height: 180px; width: 100%; display: block;" class="img-thumbnail">
		  				@else
		  					<img  id="vista_previa_fi" alt="Foto Interior Local" src="images/no-imagen.jpg" style="height: 180px; width: 100%; display: block;" class="img-thumbnail">
		  				@endif
					  {{Form::label('lfoto_i','Foto Interior',array('id'=>'','class'=>'')) }}
					  {{Form::file('foto_i',array('id'=>'foto_i','class'=>'btn','title'=>"Seleccione foto del interior del local")) }}
					  	<div class=".messages"></div>	
					</div>
			  </div>
			</div>
		  </div>
		  {{-- Fin Fotos --}}
		  {{-- Zona --}}
		  <div class="panel panel-info ">
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
		          Mercado/Zona
		          <span id="tres" class="glyphicon glyphicon-chevron-down"> </span>
		        </a>
		      </h4>
		    </div>
		    <div id="collapseThree" class="panel-collapse collapse">
		      <div class="panel-body">
		        <div class="panel panel-default">
		      		<h5><span>Responder sólo en caso de trasladarse más de 5 cuadras del domicilio actual</span></h5>
		      			<div class="form-group col-sm-12">
				        	{{Form::label('lpreg_1', '¿Por qué cree que la zona propuesta tiene potencial? ¿Es mejor que la actual? ¿Por qué?', array('id'=>'lpreg_1', 'class'=>'control-label col-sm-12'))}}
							<div class="col-sm-12">
								{{Form::textarea('preg_1', '', array('id' => 'preg_1', 'rows'=>'3', 'class'=>"form-control input-medium", 'onfocus'=>'siguienteCampo = this.form.preg_2.id;nombreForm=this.form.id'))}}
							</div>
				        </div>
						<div class="form-group col-sm-12">
							{{Form::label('lpreg_2', '¿Dónde considera que apostarán sus clientes actuales al mudarse? ¿Quedará descubierta la zona actual?', array('id'=>'lpreg_2', 'class'=>'control-label col-sm-12'))}}
							<div class="col-sm-12">
								{{Form::textarea('preg_2', '', array('id' => 'preg_2', 'rows'=>'3','class'=>"form-control input-medium", 'onfocus'=>'siguienteCampo = this.form.caracteristicas.id;nombreForm=this.form.id'))}}
							</div>
						</div>	
				</div>
				<div class="panel panel-default col-lg-12">
					<div class="form-group col-lg-13">
						<h4 class="col-lg-12"><span class="label label-info span4">Características de la zona donde planea mudarse</span>
		      			<small>Seleccione 1 o más opciones</small>
		      			</h4>
		      			 <div class="col-lg-5 col-lg-push-3">
			        		{{Form::select('caracteristicas[]', $lista_caracteristicas,explode(',',Session::get('_old_input.car_zona')), array('id' => 'caracteristicas', 'class'=>'form-control', 'multiple' => true,'onfocus'=>'siguienteCampo = this.form.nivel_circulacion_codigo.id;nombreForm=this.form.id'))}}
			        	</div>	
					</div>					      							
				</div>
				<div class="panel panel-default col-sm-6 col-lg-6" id="div_circulacion">
					<div class="form-group col-xs-8 col-sm-7 col-lg-10">
			      		<h4><span class="label label-info">Nivel de circulación</span>
			      			<small>Seleccione 1 opción</small>
			      		</h4>
				        <div class="col-lg-10">
							{{ Form::select('nivel_circulacion_codigo',$lista_circulacion,'',array('id'=>'nivel_circulacion_codigo','class'=>'form-control col-lg-12','onfocus'=>'siguienteCampo = this.form.nivel_socioeconomico_codigo.id;nombreForm=this.form.id')) }}
						</div>
					</div>
				</div>
				<div class="panel panel-default col-sm-6 col-lg-6">
					<div class="form-group col-xs-8 col-sm-7 col-lg-10">
			      		<h4><span class="label label-info">Nivel socioeconómico</span>
			      			<small>Seleccione 1 opción</small>
			      		</h4>
				        
						<div class="col-lg-10">
							{{ Form::select('nivel_socioeconomico_codigo',$lista_socioeconomico,'',array('id'=>'nivel_socioeconomico_codigo','class'=>'form-control col-lg-12')) }}
						</div>
					</div>
				</div>
		      </div>
		    </div>
		  </div>
		  {{-- Fin Zona --}}
		  {{-- Objetivos --}}
		  <div class="panel panel-info">
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a data-toggle="collapse" id="a_ventas" data-parent="#accordion" href="#collapseFour">
		          Objetivos de Ventas por Trimestre
		          <span id="cuatro" class="glyphicon glyphicon-chevron-down"> </span>
		        </a>
		      </h4>
		    </div>
		    <div id="collapseFour" class="panel-collapse collapse">
		      <div class="panel-body">
		        <h4><span class="label label-info">Recaudación Estimada en $$$</span></h4>
		        <div id="periodo_inicial" class="col-sm-12">
		        	{{Form::label('lperiodo_i','Período Inicial: ', array('class'=>'col-xs-12 col-sm-3'))}}
		        	<div id="div_mes_i" class="form-inline col-xs-2 col-sm-6">
		        	{{Form::text('mes_i', '', array('id' => 'mes_i','class' => 'datepicker form-control','readonly' => 'readonly'))}}
		        	</div>
		        </div>
		        
		        {{-- tabla--}}
     			<div class="col-xs-12" id="cont_ext_tabla">
     				<br>
		        	<div class="fixed-table-container" id="cont_int_tabla">	
		        		{{--Tabla para los encabezados de cada columna--}}

		        		<table class="table" id="tablita">
						  <thead>
						    <tr>
						      <th class="col-md-3 text-center"></th>
						      <th class="col-md-2 text-center">Año Inicio</th>
						      <th class="col-md-2 text-center">Año Fin</th>
						      <th class="col-md-3 text-center">Meses</th>
						      <th class="col-md-3 text-center">Promedio Mensual de Ventas</th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr id="trim_1">
						      <td id="nombre_t1"><strong>Trimestre 1</strong></td>
						      <td class="text-center" id="anioi_t1">{{Session::get('_old_input.anioi_t1')}}</td>
						      <td class="text-center" id="aniof_t1">{{Session::get('_old_input.aniof_t1')}}</td>
						      <td class="text-center" id="meses_t1">{{Session::get('_old_input.meses_t1')}}</td>
						      <td>{{Form::text('venta_1', '', array('id' => 'venta_1', 'class'=>'form-control col-xs-11 text-center' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.venta_2.id;nombreForm=this.form.id'))}}</td>
						    </tr>
						    <tr id="trim_2">
						      <td id="nombre_t2"><strong>Trimestre 2</strong></td>
						      <td class="text-center" id="anioi_t2">{{Session::get('_old_input.anioi_t2')}}</td>
						      <td class="text-center" id="aniof_t2">{{Session::get('_old_input.aniof_t2')}}</td>
						      <td class="text-center" id="meses_t2">{{Session::get('_old_input.meses_t2')}}</td>
						      <td>{{Form::text('venta_2', '', array('id' => 'venta_2', 'class'=>'form-control col-xs-11 text-center' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.venta_3.id;nombreForm=this.form.id'))}}</td>
						    </tr>
						    <tr id="trim_3">
						      <td id="nombre_t3"><strong>Trimestre 3</strong></td>
						      <td class="color_td text-center" id="anioi_t3">{{Session::get('_old_input.anioi_t3')}}</td>
						      <td class="color_td text-center" id="aniof_t3">{{Session::get('_old_input.aniof_t3')}}</td>
						      <td class="color_td text-center" id="meses_t3">{{Session::get('_old_input.meses_t3')}}</td>
						      <td>{{Form::text('venta_3', '', array('id' => 'venta_3', 'class'=>'form-control col-xs-11 text-center' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = this.form.venta_4.id;nombreForm=this.form.id'))}}</td>
						    </tr>
						    <tr id="trim_4">
						      <td class="color_td " id="nombre_t4"><strong>Trimestre 4</strong></td>
						      <td class="color_td text-center" id="anioi_t4">{{Session::get('_old_input.anioi_t4')}}</td>
						      <td class="color_td text-center" id="aniof_t4">{{Session::get('_old_input.aniof_t4')}}</td>
						      <td class="color_td text-center" id="meses_t4">{{Session::get('_old_input.meses_t4')}}</td>
						      <td>
						      	@if (strpos($llamada_de,'planEstrategico')!==false)
						      	{{Form::text('venta_4', '', array('id' => 'venta_4', 'class'=>'form-control col-xs-11 text-center' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = "btn_aceptar";nombreForm=this.form.id'))}}
						      	@else
						      		{{Form::text('venta_4', '', array('id' => 'venta_4', 'class'=>'form-control col-xs-11 text-center' ,'data-toggle'=>'tooltip', 'data-placement'=>'right','onfocus'=>'siguienteCampo = "btn_modificar";nombreForm=this.form.id'))}}
						      	@endif
						      </td>
						    </tr>
						  </tbody>
					</table>
		        		
		        {{-- fin tabla--}}
			  			</div>
					</div>
				</div>
			</div>	
		</div>
		{{-- Fin Objetivos --}}
	</div>
		@if (strpos($llamada_de,'planEstrategico')!==false)
			<div class="col-sm-10">
				{{Form::button('Aceptar', array('id'=>'btn_aceptar', 'name'=>'btn_aceptar', 'class'=>'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}}
				{{Form::button('Volver', array('id'=>'btn_volver', 'name'=>'btn_volver', 'class'=>'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}}
			</div>
		@else
			<div class="col-sm-10">
				{{Form::button('Modificar', array('id'=>'btn_modificar', 'name'=>'btn_modificar', 'class'=>'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}}
				{{Form::button('Volver', array('id'=>'btn_volver', 'name'=>'btn_volver', 'class'=>'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}}
			</div>
		@endif

	{{Form::close()}}
</div>
@endsection

@section('contenido_modal')
	<div id="cargandoModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop:"static" data-keyboard="false">
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





