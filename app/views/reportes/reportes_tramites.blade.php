@extends('layouts.reportes')
@section('javascript')
	{{HTML::script("js/js-reportes.js", array("type" => "text/javascript"))}}
@stop

@section('contenido_iframe')
	<!--div class="row">
		<div id='outer'>
    		<div id='inner'-->
			<iframe id="frame" name="frame" src="{{$url_repositorio}}" frameborder="0" hspace="0" vspace="0" scrolling=auto width="100%" height="100%" > aqui</iframe>			
			<!--/div>
		</div>
	</div-->	
@endsection
