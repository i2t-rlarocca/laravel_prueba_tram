@extends('layouts.base')
@section('javascript')
	{{HTML::script("js/jquery-ui.js", array("type" => "text/javascript"))}}
	{{HTML::script("js/js-teclapulsada.js", array("type" => "text/javascript"))}}
	{{HTML::script('js/js-evaluaciondomicilio.js', array('type'=>'text/javascript'))}}
	{{HTML::script('js/js-session.js', array('type'=>'text/javascript'))}}
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
			
				<h1>Inspección Cambio de domicilio - {{$nroTramite}}
				<br>
				 {{$agencia}}</h1>				
			
		</div>
	</div>		
		
	<div class="row-border">
		{{Form::open(array('action' => array('TramitesController@evaluacionDomicilio'), 'method' => 'post', 'id' => 'formulario_evaluacion_domicilio', 'class'=>'form-horizontal', 'files'=>true))}}

			{{Form::hidden('urlRetorno', $urlRetorno, array('id'=>'urlRetorno'))}}
			{{Form::hidden('permiso', $permiso, array('id'=>'permiso'))}}
			{{Form::hidden('nroTramite', $nroTramite, array('id'=>'nroTramite'))}}
			
			
			
			{{-- Domicilio Actual--}}
			<div id="datos_actuales" class="panel panel-warning col-xs-12 col-md-6 col-sm-6 col-lg-6 div_sin_padding">
				<div class="panel-heading">
			      <h4 class="panel-title">
			          Domicilio Actual
			      </h4>
			    </div>
			    <br>
				<div class="form-group col-sm-12 div_sin_padding">
						{{Form::label('ldomicilio_actual', 'Dirección:', array('id'=>'ldomicilio_actual', 'class'=>'control-label col-xs-3 col-lg-3'))}}
						<div class="col-sm-9">
	      					{{Form::text('domicilio_actual', $datos['direccion_vieja'], array('id' => 'domicilio_actual', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
	    				</div>
				</div>
				<div class="form-group col-sm-12 div_sin_padding">
						{{Form::label('llocalidad_actual', 'Localidad:', array('id'=>'llocalidad_actual', 'class'=>'control-label col-xs-3 col-lg-3'))}}
						<div class="col-sm-9">
	      					{{Form::text('localidad_actual', $datos['localidad_vieja'], array('id' => 'localidad_actual', 'class'=>'form-control col-xs-12' ,'data-toggle'=>'tooltip', 'data-placement'=>'right', 'readonly'=>'true'))}}
	    				</div>
				</div>
				<div class="form-group col-sm-12 div_sin_padding">
						{{Form::label('ldpto_actual', 'Departamento:', array('id'=>'ldpto_actual', 'class'=>'control-label col-xs-3'))}}
						<div class="col-sm-9">
	      					{{Form::text('departamento', $datos['departamento_viejo'], array('id' => 'departamento', 'class'=>'form-control col-xs-12' ,'data-toggle'=>'tooltip', 'data-placement'=>'right', 'readonly'=>'true'))}}
	    				</div>
				</div>	
			</div>			
			{{-- Fin Domicilio Actual--}}

			{{-- Domicilio Propuesto--}}
			<div id="datos_actuales" class="panel panel-warning col-xs-12 col-md-6 col-sm-6 col-lg-6 div_sin_padding">
				<div class="panel-heading">
			      <h4 class="panel-title">
			          Domicilio Propuesto
			      </h4>
			    </div>
			    <br>
				<div class="form-group col-sm-12 div_sin_padding">
						{{Form::label('ldomicilio_nuevo', 'Dirección:', array('id'=>'ldomicilio_nuevo', 'class'=>'control-label col-xs-3 col-lg-3'))}}
						<div class="col-sm-9">
	      					{{Form::text('direccion_nueva', $datos['direccion_nueva'], array('id' => 'direccion_nueva', 'class'=>'form-control col-xs-3' ,'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
	    				</div>
				</div>
				<div class="form-group col-sm-12 div_sin_padding">
						{{Form::label('llocalidad_nueva', 'Localidad:', array('id'=>'llocalidad_nueva', 'class'=>'control-label col-xs-3 col-lg-3'))}}
						<div class="col-sm-9">
	      					{{Form::text('localidad_nueva', $datos['localidad_nueva'], array('id' => 'localidad_nueva', 'class'=>'form-control col-xs-12' ,'data-toggle'=>'tooltip', 'data-placement'=>'right', 'readonly'=>'true'))}}
	    				</div>
	    		</div>
				<div class="form-group col-sm-12 div_sin_padding">
						{{Form::label('ldpto_nuevo', 'Departamento:', array('id'=>'ldpto_nuevo', 'class'=>'control-label col-xs-3'))}}
						<div class="col-sm-9">
	      					{{Form::text('departamento_nuevo', $datos['departamento_nuevo'], array('id' => 'departamento_nuevo', 'class'=>'form-control col-xs-12' ,'data-toggle'=>'tooltip', 'data-placement'=>'right', 'readonly'=>'true'))}}
	    				</div>
				</div>	
			</div>			
			{{-- Fin Domicilio Propuesto--}}

			{{-- Módulos--}}
			<div class="panel-group col-xs-12 col-md-12 col-sm-12 div_sin_padding" id="accordion">
				{{-- Local --}}
			  <div class="panel panel-info" >
				    <div class="panel-heading">
				      <h4 class="panel-title">
				        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
				          Local
				          <span id="uno" class="glyphicon glyphicon-chevron-down"> </span>
				        </a>
				      </h4>
				    </div>
				    <div id="collapseOne" class="panel-collapse collapse in">
					     <div class="panel-body">
					     	<div class="col-md-6">
					     		<div class="row">
					     			<div class="form-group">
					     				{{Form::label('lubicacion', 'Ubicación:', array('id'=>'lubicacion', 'class'=>'control-label col-xs-4 col-lg-4'))}}
					     				<div class="col-md-8">
					     					@if (Session::get('_old_input.ubicacion_codigo') == "ni")
					     						{{ Form::select('ubicacion',$lista_ubicacion,$plan["ubicacion_codigo"],array('id'=>'ubicacion','class'=>'form-control col-lg-12','onfocus'=>'siguienteCampo = this.form.superficie.id;nombreForm=this.form.id')) }}
					     					
											@else
					      						{{ Form::select('ubicacion',$lista_ubicacion,Session::get('_old_input.ubicacion_codigo'),array('id'=>'ubicacion','class'=>'form-control col-lg-12','onfocus'=>'siguienteCampo = this.form.superficie.id;nombreForm=this.form.id')) }}
					      					@endif
					    				</div>
					     			</div>
					     		</div>
					     		<div class="row">
					     			<div class="form-group">
					     				{{Form::label('lsuperficie', 'Superficie:', array('id'=>'lsuperficie', 'class'=>'control-label col-xs-4 col-lg-4'))}}
					     				<div class="col-md-8">
					     					@if (Session::get('_old_input.superficie_codigo') == "ni")
					      						{{ Form::select('superficie',$lista_superficie,$plan['superficie_codigo'],array('id'=>'superficie','class'=>'form-control col-lg-12','onfocus'=>'siguienteCampo = this.form.vidriera.id;nombreForm=this.form.id')) }}
					      					@else
					      						{{ Form::select('superficie',$lista_superficie,Session::get('_old_input.superficie_codigo'),array('id'=>'superficie','class'=>'form-control col-lg-12','onfocus'=>'siguienteCampo = this.form.vidriera.id;nombreForm=this.form.id')) }}
					      					@endif
					    				</div>
					     			</div>
					     		</div>
					     		<div class="row">
					     			<div class="form-group">
					     				{{Form::label('lvidriera', 'Vidriera:', array('id'=>'lvidriera', 'class'=>'control-label col-xs-4 col-lg-4'))}}
					     				<div class="col-md-8">
					     					@if (Session::get('_old_input.vidriera_codigo') == "ni")
					      						{{ Form::select('vidriera',$lista_vidriera,$plan['vidriera_codigo'],array('id'=>'vidriera','class'=>'form-control col-lg-12 vidriera', 'onfocus'=>'siguienteCampo = this.form.rubros_amigos.id;nombreForm=this.form.id')) }}
					      					@else
					      						{{ Form::select('vidriera',$lista_vidriera,Session::get('_old_input.vidriera_codigo'),array('id'=>'vidriera','class'=>'form-control col-lg-12 vidriera', 'onfocus'=>'siguienteCampo = this.form.rubros_amigos.id;nombreForm=this.form.id')) }}
					      					@endif
					    				</div>
					     			</div>
					     		</div>
					    	</div>

					     	<div class="col-md-6">
					     		<div class="row">
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
					      					@if (Session::get('_old_input.rubros_amigos') == 0)
					      						{{Form::text('rubros_amigos',$plan['rubros_amigos'],array('id'=>'rubros_amigos','class'=>'form-control col-sm-2','maxlength' => '2','onfocus'=>'siguienteCampo = this.form.rubros_otros.id;nombreForm=this.form.id'))}}
					      					@else
					      						{{Form::text('rubros_amigos',Session::get('_old_input.rubros_amigos'),array('id'=>'rubros_amigos','class'=>'form-control col-sm-2','maxlength' => '2','onfocus'=>'siguienteCampo = this.form.rubros_otros.id;nombreForm=this.form.id'))}}
					      					@endif
					      				</div>
					    				{{Form::label('lporc','%', array('class'=>'control-label col-sm-1 '))}}
					    			</div>
					    			<div class="form-group col-sm-12">
					      				{{Form::label('lrotros','Rubros No Afines:', array('id'=>'lrotros', 'class'=>'control-label col-xs-6 col-sm-6 '))}}
					      				<div class="col-xs-4 col-sm-4 ">
					      					@if (Session::get('_old_input.rubros_otros') == 0)
					      						{{Form::text('rubros_otros',$plan['rubros_otros'],array('id'=>'rubros_otros','class'=>'form-control col-sm-2','maxlength' => '2','onfocus'=>'siguienteCampo = "nada";nombreForm=this.form.id'))}}
					      					@else
					      						{{Form::text('rubros_otros',Session::get('_old_input.rubros_otros'),array('id'=>'rubros_otros','class'=>'form-control col-sm-2','maxlength' => '2','onfocus'=>'siguienteCampo = "nada";nombreForm=this.form.id'))}}
					      					@endif
					      				</div>
					      				{{Form::label('lporc','%', array('class'=>'control-label col-sm-1 '))}}
					    			</div>
					     		</div>
					     	</div>

					     	<!--div class="col-md-12">
					     		<div class="row">
						     		<div class="form-group">
					     				{{Form::label('lvisibilidad', 'Visiblidad:', array('id'=>'lvisibilidad', 'class'=>'control-label col-xs-2 col-lg-2'))}}
					     				<div class="col-md-4">
					      					{{ Form::select('visibilidad',$lista_visibilidad,Session::get('_old_input.visibilidad_codigo'),array('id'=>'visibilidad','class'=>'form-control col-lg-12', 'onfocus'=>'siguienteCampo = this.form.tipolocal.id;nombreForm=this.form.id')) }}
					    				</div>
					     			</div>
					     		</div>
					     	</div>

					     	<div class="col-md-12">
					     		<div class="row">
						     		<div class="form-group">
					     				{{Form::label('ltipolocal', 'Tipo de local:', array('id'=>'ltipolocal', 'class'=>'control-label col-xs-2 col-lg-2'))}}
					     				<div class="col-md-4">
					      					{{ Form::select('tipolocal',$lista_tipolocal,Session::get('_old_input.tipolocal_codigo'),array('id'=>'tipolocal','class'=>'form-control col-lg-12', 'onfocus'=>'siguienteCampo = this.form.observaciones_local.id;nombreForm=this.form.id')) }}
					    				</div>
					     			</div>
					     		</div>
					     	</div-->


					        <div class="col-md-12">
						        <div class="row">
						        	<div class="form-group">
										{{Form::label('lobservacioneslocal', 'Observaciones:', array('id'=>'lobservacioneslocal', 'class'=>'control-label col-xs-2 col-lg-2'))}}
										<div class="col-sm-9">
					      					{{Form::textarea('observaciones_local', Session::get('_old_input.observacion_local'), array('id' => 'observaciones_local', 'class'=>"form-control input-medium", 'maxlength' => '1000','data-toggle'=>'tooltip',  'rows'=>'2', 'onfocus'=>'siguienteCampo = this.form.estado_local.id;nombreForm=this.form.id'))}}
					    				</div>
									</div>
								</div>
							</div>

<!-- ADEN - 2024-04-25 
							<div class="col-md-12">
						        <div class="row">
									<div class="form-group">
					                    {{Form::label('lestadolocal', 'Estado:', array('id'=>'lestadolocal', 'class'=>'control-label col-xs-2 col-lg-2'))}}
					                    <div class="col-md-3">
					                    	{{Form::select('estado_local', $lista_estados, Session::get('_old_input.estado_local'), array('id' => 'estado_local','class'=>'form-control input-sm col-lg-8 col-md-8'))}}		                        
					                    </div>
					                </div>
					            </div>
					        </div>
-->
						</div>			    
				  </div>
			 </div>
			  {{-- Fin Local --}}
			  {{-- Entorno --}}
			  <div class="panel panel-info">
				<div class="panel-heading">
				  <h4 class="panel-title ">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
					  Entorno
	    			    <span id="dos" class="glyphicon glyphicon-chevron-down"> </span>
					</a>
				  </h4>
				</div>
				<div id="collapseTwo" class="panel-collapse collapse">
				  <div class="panel-body">
				  		<div class="row">
							<div class="form-group col-md-6">
								{{Form::label('lcaracteristicas', 'Localización:', array('id'=>'lcaracteristicas', 'class'=>'control-label col-xs-4 col-lg-4 col-md-4'))}}
				      			 <div class="col-lg-8 col-md-8">
				      			 	@if (Session::get('_old_input.caracteristicas') == "")
					        			{{Form::select('localizacion[]', $lista_caracteristicas,explode(',',$plan['car_zona']), array('id' => 'localizacion', 'class'=>'form-control', 'multiple' => true,'onfocus'=>'siguienteCampo = this.form.soc_eco_codigo.id;nombreForm=this.form.id'))}}
					        		@else

					        			{{Form::select('localizacion[]', $lista_caracteristicas,explode(',',Session::get('_old_input.caracteristicas')), array('id' => 'localizacion', 'class'=>'form-control', 'multiple' => true,'onfocus'=>'siguienteCampo = this.form.soc_eco_codigo.id;nombreForm=this.form.id'))}}
					        		@endif
					        	</div>	
							</div>					      							
						
							<div class="form-group col-md-6">
								{{Form::label('lnivelsoc', 'Nivel Socioeconómico:', array('id'=>'lnivelsoc', 'class'=>'control-label col-xs-6 col-lg-6 col-md-6'))}}
				      			<div class="col-lg-6 col-md-6">
				      				@if (Session::get('_old_input.socioeconomico_codigo') == "ni")
										{{ Form::select('soc_eco_codigo',$lista_socioeconomico,$plan['nivel_socioeconomico_codigo'],array('id'=>'soc_eco_codigo','class'=>'form-control col-lg-12','onfocus'=>'siguienteCampo = this.form.btn_todos_i.id;nombreForm=this.form.id')) }}
				      				@else
										{{ Form::select('soc_eco_codigo',$lista_socioeconomico,Session::get('_old_input.socioeconomico_codigo'),array('id'=>'soc_eco_codigo','class'=>'form-control col-lg-12','onfocus'=>'siguienteCampo = this.form.btn_todos_i.id;nombreForm=this.form.id')) }}
									@endif
								</div>	
							</div>					      							
						</div>

			  			<div class="row" id="divCentrosAfinidad">
			  				<div class="row"> 
				  				{{Form::label('lcentro', 'Centros de afinidad', array('id'=>'lcentro', 'class'=>'center-text col-xs-12 col-lg-12 col-md-12'))}}
			  				</div>
			  				<div class="row"> 
								Seleccione los centros de afinidad de la lista izquierda cercanos al nuevo local y pulse <b>“>”</b> para agregarlos.
								Para eliminar uno, seleccione el centro de afinidad de la lista derecha y pulse <b>“>”</b>.
			  				</div>
			  				<div class="row"> <!-- una fila! -->
					        <div class="dual-list list-left col-md-5">
					            <div class="well text-right">
					                <div class="row">
						  				<div class="col-md-2">
					                        <div class="btn-group">
					                            <a id="btn_todos_i" class="btn btn-default selector" title="Seleccionar todos"><i class="glyphicon glyphicon-unchecked"></i>Todos</a>
					                        </div>
					                    </div>
					                </div>
				                     <ul class="list-group">
				                     	@if (Session::get('_old_input.centroafinidad')!="")
											@foreach ($lista_centro_afinidad as $key => $lca)
												@if(!array_key_exists($key, Session::get('_old_input.centroafinidad')))
													<li class="list-group-item" value="{{$key}}">{{$lca}}</li>
												@endif
											@endforeach
										@else
											@foreach ($lista_centro_afinidad as $key => $lca)
												<li class="list-group-item" value="{{$key}}">{{$lca}}</li>
											@endforeach
										@endif
				                     </ul>
					               
					            </div>
					        </div>

					        <div class="list-arrows col-md-1 text-center">
					            <button type="button" id="flechaIzq" class="btn btn-default btn-sm move-left">
					                <span class="glyphicon glyphicon-chevron-left"></span>
					            </button>

					            <button type="button" id="flechaDer" class="btn btn-default btn-sm move-right">
					                <span class="glyphicon glyphicon-chevron-right"></span>
					            </button>
					        </div>

	        				<div class="dual-list list-right col-md-5">
					            <div class="well">
					                <div class="row">
					                    <div class="col-md-2">
					                        <div class="btn-group">
					                            <a class="btn btn-default selector" title="Seleccionar todos"><i class="glyphicon glyphicon-unchecked"></i>Todos</a>
					                        </div>
					                    </div>				                    
					                </div>
					                <ul class="list-group" id="centros_afinidad">
					                	@if (Session::get('_old_input.centroafinidad')!="")
						                    @foreach (Session::get('_old_input.centroafinidad') as $lca)
											     <li class="list-group-item" value="{{$lca['clave']}}">{{$lca['valor']}}</li>
											@endforeach
										@endif
					                </ul>
					            </div>
					        </div>
			  				</div> <!-- div row una fila! -->
					    </div> <!-- divCentrosAfinidad -->

					    <div class="row">
					    	<div class="form-group ">
								{{Form::label('linfocart', 'Información cartográfica:', array('id'=>'linfocart', 'class'=>'control-label col-xs-2 col-lg-2 col-md-2'))}}
								<div class="row">
				                    <div class="col-md-9">
							  			@if(Session::get('_old_input.plano')!="")						  				
				  					  		<img  id="vista_previa_plano" alt="Información cartográfica" src={{Session::get('_old_input.plano')}} style="height: 300px; width: 100%; display: block;" class="img-thumbnail">
				  					  	@else
				  					  		<img  id="vista_previa_plano" alt="Información cartográfica" src="images/no-imagen.jpg" style="height: 300px; width: 100%; display: block;" class="img-thumbnail">
				  					  	@endif
									  									  
									  	{{ Form::file('plano',array('id'=>'plano','class'=>'btn','title'=>"Seleccione el plano con información cartográfica",'onfocus'=>'siguienteCampo = this.form.observaciones_entorno.id;nombreForm=this.form.id')) }}
									</div>
								</div>
							</div>
						  				
						</div>

				        <div class="row">
				        	<div class="form-group ">
								{{Form::label('lobservacionesentorno', 'Observaciones:', array('id'=>'lobservacionesentorno', 'class'=>'control-label col-xs-2 col-lg-2 col-md-2'))}}
								<div class="col-sm-9 col-md-8">
			      					{{Form::textarea('observaciones_entorno', Session::get('_old_input.observacion_entorno'), array('id' => 'observaciones_entorno', 'class'=>"form-control input-medium", 'maxlength' => '1000','data-toggle'=>'tooltip', 'rows'=>'2' ,'onfocus'=>'siguienteCampo = this.form.estado_entorno.id;nombreForm=this.form.id'))}}
			    				</div>
							</div>
						</div>

<!-- ADEN - 2024-04-25
						<div class="form-group">
		                    {{Form::label('lestadoentorno', 'Estado:', array('id'=>'lestadoentorno', 'class'=>'control-label col-xs-2 col-lg-2'))}}
		                    <div class="col-md-3">
		                    	{{Form::select('estado_entorno', $lista_estados, Session::get('_old_input.estado_entorno'), array('id' => 'estado_entorno','class'=>'form-control input-sm col-lg-8 col-md-8'))}}		                        
		                    </div>
		                </div>
-->					   
				  </div>
				</div>
			  </div>
			  {{-- Fin Entorno --}}
			  {{-- Cuantitativo --}}
			  <!--
			  <div class="panel panel-info ">
			    <div class="panel-heading">
			      <h4 class="panel-title">
			        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
			          Cuantitativo
			          <span id="tres" class="glyphicon glyphicon-chevron-down"> </span>
			        </a>
			      </h4>
			    </div>
			    <div id="collapseThree" class="panel-collapse collapse">
			      <div class="panel-body">
			      	 <div class="row">

			      	 	<table align="center" class="table table-inbox table-hover table-bordered">
	                        <caption><h4><span class="label label-primary"><ins>Antecedentes</ins></span></h4></caption>      
	                        <tbody>
	                        	<!-- encabezados -->
	                          <!-- <tr class="unread"> -->
	                          <!-- 	<td class="view-message text-center">Punto de venta</td> -->
	                         	<!--td class="view-message">Fracción Actual</td-->
	                          <!-- 	<td class="view-message text-center">Localidad Actual</td> -->
	                          <!-- 	<td class="view-message text-center">Indicador</td> -->
	                            <!-- td class="view-message">Fracción Solicitada</td-->
	                          <!--  <td class="view-message text-center">Localidad Solicitada</td> -->
	                          <!--  <td class="view-message text-center">Departamento Solicitado</td> -->
	                          <!--  </tr>  -->

	                          <!-- Cuerpo  -->
	                           @foreach($tablaAntecedentes as $rta)
		                        <!--   <tr class="">
		                              <td class="view-message  dont-show">{{$rta->punto}}</td>
		                              <td class="view-message">{{$rta->locaAct}}</td>
		                              <td class="text-center"><span class="label label-success">{{$rta->indicador}}</span></td>
		                              <td class="view-message  text-left">{{$rta->locaSol}}</td>
		                              <td class="view-message  text-left">{{$rta->dptoSol}}</td>
		                          </tr> -->
	                          @endforeach
	                    <!--  </tbody>
	                    </table>

	                </div>          
	                <br>
			        <div class="row">
			        	<div class="form-group"> 
							{{-- Form::label('lobservacionescuantitativo', 'Observaciones:', array('id'=>'lobservacionescuantitativo', 'class'=>'control-label col-xs-2 col-lg-2')) --}}
							<div class="col-sm-9">
		      				{{-- Form::textarea('observaciones_cuantitativo', Session::get('_old_input.observacion_cuantitativo'), array('id' => 'observaciones_cuantitativo', 'class'=>"form-control input-medium", 'maxlength' => '1000','data-toggle'=>'tooltip', 'rows'=>'2')) --}}
		    				</div>
						</div>
					</div>

					
		                
	                <div class="form-group">
	                    {{--  Form::label('lestadocuantitativo', 'Estado:', array('id'=>'lestadocuantitativo', 'class'=>'control-label col-xs-2 col-lg-2')) --}}
	                    <div class="col-md-3">
	                    	{{-- Form::select('estado_cuantitativo', $lista_estados, Session::get('_old_input.estado_cuantitativo'), array('id' => 'estado_cuantitativo','class'=>'form-control input-sm col-lg-8 col-md-8')) --}}		                        
	                    </div>
	                </div>
		                
		            

			      </div>
			    </div>
			  </div> -->
			  {{-- Fin Cuantitativo --}}
			  {{-- Competencia --}}
			  <div class="panel panel-info">
			    <div class="panel-heading">
			      <h4 class="panel-title">
			        <a data-toggle="collapse" id="a_ventas" data-parent="#accordion" href="#collapseFour">
			          Competencia
			          <span id="cuatro" class="glyphicon glyphicon-chevron-down"> </span>
			        </a>
			      </h4>
			    </div>
			    <div id="collapseFour" class="panel-collapse collapse">
			     	<div class="panel-heading">
			     		<div class="row">
			     			<div class="funkyradio col-md-4">
			     				<div class="funkyradio-info">
			     					@if(Session::get('_old_input.existen_competidores'))
						            	<input type="checkbox" name="checkbox" id="chbxCompetidores" checked/>
						           	@else
						            	<input type="checkbox" name="checkbox" id="chbxCompetidores"/>
						            @endif
						            <label for="chbxCompetidores">Existen Competidores</label>
						        </div>
			     			</div>
			     		</div>		     		
			     		<div id="divDistanciaCompetidores" class="alert alert-success">
			     			<div class="row">
				        		<div class="form-group">
				     				{{Form::label('lencuadra','En la cuadra: ', array('class'=>'col-md-4'))}}
				     				<div class=" col-md-3">
					        			<div class="input-group number-spinner">
					                        <span class="input-group-btn">
					                        	@if(Session::get('_old_input.existen_competidores'))
					                            	<a class="btn btn-danger" data-dir="dwn"><span class="glyphicon glyphicon-minus"></span></a>
					                            @else
					                            	<a class="btn btn-danger" data-dir="dwn" disabled><span class="glyphicon glyphicon-minus"></span></a>
					                            @endif
					                        </span>
					                           	<input type="text" disabled name="cuadra" id="cuadra" class="form-control text-center" value="{{Session::get('_old_input.competidor_cuadra')}}" max=9 min=0> 
					                        
					                        <span class="input-group-btn">
					                            @if(Session::get('_old_input.existen_competidores'))
					                            	<a class="btn btn-info" data-dir="up"><span class="glyphicon glyphicon-plus"></span></a>
					                            @else
					                            	<a class="btn btn-info" data-dir="up" disabled><span class="glyphicon glyphicon-plus"></span></a>
					                            @endif
					                        </span>
					                    </div>
					                </div>
				        		</div>
				     		</div>

				     		<div class="row">
				        		<div class="form-group">
				     				{{Form::label('lantsig','Cuadra anterior/Siguiente: ', array('class'=>'col-md-4'))}}
				     				<div class=" col-md-3">
					        			<div class="input-group number-spinner">
					                        <span class="input-group-btn">
					                        	@if(Session::get('_old_input.existen_competidores'))
					                            	<a class="btn btn-danger" data-dir="dwn"><span class="glyphicon glyphicon-minus"></span></a>
					                            @else
					                            	<a class="btn btn-danger" data-dir="dwn" disabled><span class="glyphicon glyphicon-minus"></span></a>
					                            @endif
					                        </span>
					                        <input type="text" disabled name="antesig" id="antesig" class="form-control text-center" value="{{Session::get('_old_input.competidor_antsig')}}" max=9 min=0>
					                        <span class="input-group-btn">
					                            @if(Session::get('_old_input.existen_competidores'))
					                            	<a class="btn btn-info" data-dir="up"><span class="glyphicon glyphicon-plus"></span></a>
					                            @else
					                            	<a class="btn btn-info" data-dir="up" disabled><span class="glyphicon glyphicon-plus"></span></a>
					                            @endif
					                        </span>
					                    </div>
					                </div>
				        		</div>
				     		</div>

				     		<div class="row">
				        		<div class="form-group">
				     				{{Form::label('ltransv','Hasta 200 mts calles transversales / paralela: ', array('class'=>'col-md-4'))}}
				     				<div class=" col-md-3">
					        			<div class="input-group number-spinner">
					                        <span class="input-group-btn">
					                        	@if(Session::get('_old_input.existen_competidores'))
					                            	<a class="btn btn-danger" data-dir="dwn"><span class="glyphicon glyphicon-minus"></span></a>
					                            @else
					                            	<a class="btn btn-danger" data-dir="dwn" disabled><span class="glyphicon glyphicon-minus"></span></a>
					                            @endif
					                        </span>
					                        <input type="text" disabled name="transver" id="transver" class="form-control text-center" value="{{Session::get('_old_input.competidor_transv')}}" max=9 min=0>
					                        <span class="input-group-btn">
					                        	@if(Session::get('_old_input.existen_competidores'))
					                            	<a class="btn btn-info" data-dir="up" ><span class="glyphicon glyphicon-plus"></span></a>
					                            @else
					                            	<a class="btn btn-info" data-dir="up" disabled><span class="glyphicon glyphicon-plus"></span></a>
					                            @endif
					                        </span>
					                    </div>
					                </div>
				        		</div>
				     		</div>
						</div>
			     	</div>	
			     	<div class="panel-body">
						De existir competencia, detallar N° de agencia y distancia a la que se encuentra.
			        	<div class="form-group">
							{{Form::label('lobservacionescompentencia', 'Agencias:', array('id'=>'lobservacionescompentencia', 'class'=>'control-label col-xs-2 col-lg-2'))}}
							<div class="col-sm-9">
		      					{{Form::textarea('observaciones_competencia', Session::get('_old_input.observacion_competencia'), array('id' => 'observaciones_competencia', 'class'=>"form-control input-medium", 'maxlength' => '1000','data-toggle'=>'tooltip', 'rows'=>'2'))}}
		    				</div>
						</div>					
<!-- ADEN - 2024-04-25						
	                    <div class="form-group">
	                    	{{Form::label('lestadocompetencia', 'Estado:', array('id'=>'lestadocompetencia', 'class'=>'control-label col-xs-2 col-lg-2'))}}
	                        <div class="col-md-3">
	                        	{{Form::select('estado_competencia', $lista_estados, Session::get('_old_input.estado_competencia'), array('id' => 'estado_competencia','class'=>'form-control input-sm col-lg-8 col-md-8'))}}		                        
		                    </div>
		                </div>
-->			                
			            
					</div>
				</div>
			  </div> 
			{{-- Fin Competencia --}}
			
		<div class="col-sm-10">

			@if($habilitarGuardar)
				{{Form::button('Guardar', array('id'=>'btn_aceptar', 'name'=>'btn_aceptar', 'class'=>'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}}
			@endif

			{{Form::button('Documento PDF', array('id'=>'btn_eval_pdf', 'name'=>'btn_eval_pdf', 'class'=>'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}}

			{{Form::button('Volver', array('id'=>'btn_volver', 'name'=>'btn_volver', 'class'=>'btn-primary','onfocus'=>'siguienteCampo = "nada";'))}}
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<label for="category" class="col-md-12">Resultado final:</label>
			</div>
		</div>
		<div class="col-md-3">
			{{Form::select('estado_eval', $lista_estados_general, Session::get('_old_input.estado'), array('id' => 'estado_eval','class'=>'form-control input-sm col-lg-8 col-md-8', 'onfocus'=>'siguienteCampo = this.form.ubicacion.id;nombreForm=this.form.id'))}}	
		</div>
	</div>


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





