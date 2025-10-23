<div class="form-group">
	{{Form::label('lnro_permiso', 'Permiso:', array('id'=>'lnro_permiso', 'class'=>'control-label col-xs-3'))}}
	<div class="col-sm-2">
		{{Form::text('permiso', $datos['permiso'], array('id' => 'id_permiso', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
	</div>
</div>

@if(!Session::has('tramiteNuevoPermiso'))
	<div class="form-group form-horizontal">
		{{Form::label('lnro_agente', 'Agente:', array('id'=>'lnro_agente', 'class'=>'control-label col-xs-3'))}}
		<div class="col-sm-2">
			{{Form::text('agente', $datos['agente'], array('id' => 'id_agente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip',  'readonly'=>'true'))}}
		</div>

		{{Form::label('lnro_subagente', 'Subagente:', array('id'=>'lnro_subagente', 'class'=>'control-label col-xs-3 col-lg-1'))}}
		<div class="col-sm-2 col-lg-2">
			{{Form::text('subagente', $datos['subagente'], array('id' => 'id_subagente', 'class'=>'form-control col-xs-3 text-right' ,'maxlength' => '10', 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
		</div>
	</div>

	<div class="form-group">
		{{Form::label('lrazon_social', 'Razón Social:', array('id'=>'lrazon_social', 'class'=>'control-label col-xs-3'))}}
		<div class="col-sm-5">
			{{Form::text('razon_social', $datos['razon_social'], array('id' => 'razon_social', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
		</div>
	</div>

	@if(Session::has('usuarioTramite'))
		@if(Session::get('usuarioTramite.subagente')!=0)
			<BR/>
			<div class="form-group form-horizontal">
				<label id="lred" class="control-label col-xs-4 col-lg-4">Red:</label>
				<div class="col-sm-3 col-lg-6">
					{{Form::text('red', $datos['red'], array('id' => 'red', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
				</div>
			</div>
			<BR/>
		@endif
	@endif

	<div class="form-group">
		{{Form::label('ldepartamento', 'Departamento:', array('id'=>'ldepartamento', 'class'=>'control-label col-xs-3'))}}
		<div class="col-sm-5">
			{{Form::text('departamento', $datos['departamento'], array('id' => 'departamento', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip',  'readonly'=>'true'))}}
			{{Form::hidden('departamento_id', $datos['departamento_id'], array('id'=>'departamento_id'))}}
		</div>
	</div>

	<div class="form-group">
		{{Form::label('llocalidades', 'Localidad:', array('id'=>'llocalidad', 'class'=>'control-label col-xs-3'))}}
		<div class="col-sm-5">
			{{Form::text('localidad_actual', $datos['localidad_actual'], array('id' => 'localidad_actual', 'class'=>'form-control col-xs-3 text-right' , 'data-toggle'=>'tooltip', 'readonly'=>'true'))}}
			{{Form::hidden('localidad_actual_id', $datos['localidad_actual_id'],array('id'=>'localidad_actual_id'))}}
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
