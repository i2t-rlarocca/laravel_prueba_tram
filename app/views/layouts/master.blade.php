<!doctype html>
<html>
    <head>
		<meta charset="UTF-8" />
			<title>
			@section('titulo')
				CAS
			@show
			</title>
			{{HTML::script("js/jquery-1.11.0.js", array("type" => "text/javascript"))}} 
			{{HTML::script("js/jquery.validate.js", array("type" => "text/javascript"))}}
			{{HTML::script("js/additional-methods.js", array("type" => "text/javascript"))}}
			{{HTML::script("bootstrap-3.1.1-dist/js/bootstrap.js", array("type" => "text/javascript"))}}
			
			@yield('javascript')
			
		<script language="JavaScript">
		var navegador = navigator.appName;
		if (navegador == "Microsoft Internet Explorer") {
		document.write('<link rel="stylesheet" href="css/estilosie.css" type="text/css" />'); 
		document.write('<link rel="stylesheet" href="bootstrap-3.1.1-dist/css/bootstrap.css" type="text/css" />'); 
		}
		else {
		document.write('<link rel="stylesheet" href="css/estilos.css" type="text/css" />'); 
		document.write('<link rel="stylesheet" href="bootstrap-3.1.1-dist/css/bootstrap.css" type="text/css" />');
		}
		</script>
		@yield('css')
					
	</head>
         
	<body>
    
    <header class="header">
	<?php
		header ("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
		header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
		header ("Pragma: no-cache"); 
		?>
			<div class="spam">
			{{ HTML::image('images/logo_tradicional.jpg')}}
			</div>
		@yield('header')
	</header> 
	@section('sidebar') 
	
	@show
	
	<div class="container">
	@yield('container')
    <div class="formula"> 
	@yield('formula')
    <div class="table">
	@yield('table')	
    </div>
    </div>
	</div>
	<div id="footer">
        @yield('footer')
      </div>
	</body>
</html>