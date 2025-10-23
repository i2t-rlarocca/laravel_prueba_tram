<!doctype html>
<html>
    <head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<!--meta http-equiv="cache-control" content="max-age=0" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="cache-control" content="no-store" />
		<meta http-equiv="cache-control" content="must-revalidate" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" /-->
			<title>
				@section('titulo')
					CAS Habilitaciones
				@show
			</title>
			<!-- javasript  -->
					{{HTML::script("js/jquery-1.11.1.min.js", array("type" => "text/javascript"))}} 
					{{--HTML::script("js/jquery-1.11.0.js", array("type" => "text/javascript"))--}} 
					{{HTML::script("bootstrap-3.1.1-dist/js/bootstrap.js", array("type" => "text/javascript"))}}
					{{HTML::script("js/jquery.validate.js", array("type" => "text/javascript"))}}
					{{HTML::script("js/additional-methods.js", array("type" => "text/javascript"))}}
					{{HTML::script("js/js-controller.js", array("type" => "text/javascript"))}}
					{{ HTML::script('js/js-session.js', array('type'=>'text/javascript'))}}
					{{HTML::script('js/alertify.js', array('type'=>'text/javascript'))}}
					
				@yield('javascript')
				<!-- CSS  -->
					{{HTML::style("bootstrap-3.1.1-dist/css/bootstrap.css", array("type" => "text/css"))}}
					{{HTML::style("css/basestyles.css", array("type" => "text/css"))}}
					
					{{HTML::style("css/alertify.css", array("type" => "text/css"))}}
					{{HTML::style("css/bootstrap_alertify.css", array("type" => "text/css"))}}
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
					<a id="header" href="{{URL::to('portal')}}">

					<header class="header">
						<!--?php
						header("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
						header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
						header("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
						header("Pragma: no-cache"); 
						?-->
						
						{{ HTML::image('images/logo_tradicional.png?var='.rand(), 'Imagen no encontrada', array('id'=>'loteria_santa_fe', 'title'=>'Loteria de Santa Fe'))}}
						
						@yield('header')
					</header> 
					</a>
										
					@if( stristr( URL::current() , "carga-tramites") === false)
					@if(stristr(URL::current(),'acceso-no-autorizado')=== false)
					<div class="menu" id="menu_top">
						<nav class="navbar navbar-default" role="navigation">
							
							<div class="navbar-header"> 
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-exl-collapse">
									<span class="sr-only">cambiar navegacion</span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</button>
								@if(stristr(URL::current(),'administracion-permisos')=== false && stristr(URL::previous(),'administracion-permisos')!== false)
									<a href="{{ url('/administracion-permisos-inicio') }}" class="navbar-brand"><span class="glyphicon glyphicon-home"></span> Inicio</a>
								@elseif(stristr(URL::current(),'administracion-permisos')=== false)
									<a href="{{ url('/carga-tramites-inicio') }}" class="navbar-brand"><span class="glyphicon glyphicon-home"></span> Inicio</a>
								@elseif(stristr(URL::current(),'administracion-permisos')!== false)
									<a href="{{URL::to('portal')}}" class="navbar-brand"><span class="glyphicon glyphicon-home"></span> Inicio</a>
								@endif
							</div>
							@if(Session::get('usuarioLogueado.nombreTipoUsuario')=='CAS')
								@if (stristr(URL::current(),'administracion-permisos')=== false && stristr(URL::previous(),'administracion-permisos')=== false)
									<div class="collapse navbar-collapse navbar-exl-collapse" ng-controller="HeaderController" id="acolapsar">
										<ul class="nav navbar-nav">
											<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><strong>Trámites</strong><b class="caret"></b></a>
												<ul class="dropdown-menu">
													<li ng-class="{ active: isActive('/consulta-tramite')}"><a href="{{ url('/consulta-tramite') }}"><strong>Consulta Trámite</strong></a></li>
													@if(in_array('Administracion_Maquinas',Session::get('usuarioLogueado.listaFunciones')))
													<li ng-class="{ active: isActive('/administracion_maquinas')}"><a href="{{ url('/administracion_maquinas') }}" id='admin_maq' class="btn_admin_maq"><strong>Administración Máquinas</strong></a></li>
													@endif
												</ul>
											</li>
										</ul>
								</div>
								@elseif(stristr(URL::current(),'administracion-permisos')=== false && stristr(URL::previous(),'administracion-permisos')!== false)
									<div class="collapse navbar-collapse navbar-exl-collapse" ng-controller="HeaderController" id="acolapsar">
										<ul class="nav navbar-nav">
											<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><strong>Administración</strong><b class="caret"></b></a>
												<ul class="dropdown-menu">
													<li ng-class="{ active: isActive('/administracion-permisos')}"><a href="{{ url('/administracion-permisos') }}" id='cons_perm' class="btn_cons_perm"><strong>Consulta Permisos</strong></a></li>
												</ul>
											</li>
										</ul>
									</div>	
								@else
								<div class="collapse navbar-collapse navbar-exl-collapse" ng-controller="HeaderController" id="acolapsar">
										@if(in_array('Alta_Permisos',Session::get('usuarioLogueado.listaFunciones')) || in_array('Baja_Permisos',Session::get('usuarioLogueado.listaFunciones')))
										<ul class="nav navbar-nav">
											<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><strong>Administración</strong><b class="caret"></b></a>
												<ul class="dropdown-menu">
													@if(in_array('Alta_Permisos',Session::get('usuarioLogueado.listaFunciones')))
													<li ng-class="{ active: isActive('/alta_permiso')}"><a href="#" id='alta_permiso' class="btn_alta"><strong>Nuevo Permiso</strong></a></li>	
													@endif
													@if(in_array('Baja_Permisos',Session::get('usuarioLogueado.listaFunciones')))
													<li ng-class="{ active: isActive('/baja_permiso')}"><a href="#" id='baja_permiso' class="btn_baja"><strong>Baja Permiso</strong></a></li>
													@endif
												</ul>
											</li>
										</ul>
									@endif
								</div>
							@endif
						 @endif
						</nav>
					</div>
					@endif
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