<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR...nsitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    	
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<!--meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" /-->
			<title>
				@section('titulo')
					CAS Habilitaciones
				@show
			</title>
			<!-- javasript  -->
				{{HTML::script("js/jquery-1.11.0.js", array("type" => "text/javascript"))}} 
				{{HTML::script("bootstrap-3.1.1-dist/js/bootstrap.js", array("type" => "text/javascript"))}}
				{{HTML::script("js/jquery.validate.js", array("type" => "text/javascript"))}}
				{{HTML::script("js/additional-methods.js", array("type" => "text/javascript"))}}
				
				
				@yield('javascript')
				<!-- CSS  -->
				<?php 
				if(stristr($_SERVER['HTTP_USER_AGENT'] , "MSIE")!== false){ ?>
				{{HTML::style("bootstrap-3.1.1-dist/css/bootstrap.css", array("type" => "text/css"))}}
				{{HTML::style("css/basestyles.css", array("type" => "text/css"))}}
				<?php } else { ?>
				{{HTML::style("bootstrap-3.1.1-dist/css/bootstrap.css", array("type" => "text/css"))}}
				{{HTML::style("css/basestyles.css", array("type" => "text/css"))}}
				<?php }	?>
					
				@yield('css')
					
	</head>

    <!-- body  -->    
	<body>
		<!-- contenedor general  -->
		<div class="container_gral_reporte">
			<!-- contenedor principal  -->
			<div class="container_ppal">
					<!-- header  -->
					<header class="header">
						<?php
						header("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
						header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
						header("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
						header("Pragma: no-cache"); 

						?>
						{{ HTML::image('images/logo_tradicional.png')}}
						@yield('header')
					</header> 
					<!-- contenido  -->		
					<div class="contenido_iframe">
						@yield('contenido_iframe')	
					</div>
					
					<!-- footer  -->	
					<div id="pie">
						@yield('pie')
					</div>
			</div>
		</div>
	</body>
</html>