<!doctype html>
<html>
    <head>
		<meta charset="UTF-8" name="viewport" content="width=device-width" />
		
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
				{{HTML::script("js/js-controller.js", array("type" => "text/javascript"))}}
				
				@yield('javascript')
			<!-- CSS  -->
				<?php 
				if(stristr($_SERVER['HTTP_USER_AGENT'] , "MSIE")!== false){ ?>
				{{HTML::style("bootstrap-3.1.1-dist/css/bootstrap.css", array("type" => "text/css"))}}
				{{HTML::style("css/estilos.css", array("type" => "text/css"))}}
				<?php } else { ?>
				{{HTML::style("bootstrap-3.1.1-dist/css/bootstrap.css", array("type" => "text/css"))}}
				{{HTML::style("css/estilos.css", array("type" => "text/css"))}}
				<?php }	?>
					
				@yield('css')
				<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
				<!--[if lt IE 9]>
					{{HTML::script("http://html5shim.googlecode.com/svn/trunk/html5.js", array("type" => "text/javascript"))}}
				<![endif]-->



				<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
				<!--[if lt IE 9]>
				  {{HTML::script("js/html5shiv.js", array("type" => "text/javascript"))}}
				  {{HTML::script("js/respond.min.js", array("type" => "text/javascript"))}}
				<![endif]-->	
					
	</head>
	<!-- contenido  -->		
	<div class="contenido_modal">
		@yield('contenido_modal')	
	</div>
    <!-- body  -->    
	<body>
		<!-- contenedor general  -->
		<div class="container-fluid container_gral">
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
						{{ HTML::image('images/logo_tradicional.jpg')}}
						@yield('header')
					</header> 
					<!-- menu  -->
					<?php
					//local
					$url = substr($_SERVER["PHP_SELF"],0,27);
					//servidor 71
					$url2 = substr($_SERVER["PHP_SELF"],0,30);
					?>
					
					
					@if( stristr( URL::current() , "ingreso") === false)
					<div class="menu" id="menu_top">
						<nav class="navbar navbar-default" role="navigation">
							<div class="navbar-header"> 
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-exl-collapse">
									<span class="sr-only">cambiar navegacion</span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</button>
								<a href="{{ url('/ingreso') }}" class="navbar-brand"><span class="glyphicon glyphicon-home"></span> Inicio</a>
							</div>
							<div class="collapse navbar-collapse navbar-exl-collapse" ng-controller="HeaderController" id="acolapsar">
								<ul class="nav navbar-nav">
									<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><strong>Trámites</strong><b class="caret"></b></a>
										<ul class="dropdown-menu">
											<li ng-class="{ active: isActive('/tramites')}"><a href="{{ url('/tramites') }}"><strong>Nuevo Trámite</strong></a></li>	
											<li ng-class="{ active: isActive('/cancelar')}"><a href="{{ url('/cancelar') }}"><strong>Cancelar Trámite</strong></a></li>		
											<li ng-class="{ active: isActive('/consulta-tramite')}"><a href="{{ url('/consulta-tramite') }}"><strong>Consulta Trámite</strong></a></li>
											<li ng-class="{ active: isActive('/consulta-nrotramite')}"><a href="{{ url('/consulta-nrotramite') }}"><strong>Consulta Nro. Seguimiento </strong></a></li>
										</ul>
									</li>	
									<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><strong>Informes</strong><b class="caret"></b></a>
										<ul class="dropdown-menu">
											<li ng-class="{ active: isActive('/informe-licencia')}"><a href="{{ url('/informe-licencia') }}"><strong>Informe Licencias</strong></a></li>		
										</ul>
									</li>		
								</ul>
							</div>
						</nav>
					</div>
					@yield('menu')
					@endif
					
					<!-- contenido  -->		
					<div class="contenido">
						@yield('contenido')	
					</div>
					
					<!-- footer  -->	
					<div id="pie">
						@yield('pie')
					</div>
			</div>
		</div>
	</body>
</html>