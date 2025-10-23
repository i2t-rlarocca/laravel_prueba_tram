<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="utf-8">
   </head>
   <body>
      Se ha generado un trámite de: <strong>{{$tipoTramite}}</strong> cuyo Nº de seguimiento es:<strong>{{$nroTramite}}</strong> 
      <br/>
      <strong>Nº de permiso:</strong> {{$nroPermiso}}
      <strong>Agente:</strong> {{$agente."/".$subagente}}
	  <strong>Titular:</strong> {{$titular}}
      <br/>
	  <strong>Observaciones:</strong> {{$observaciones}}
	   <br/>
      Trámite realizado por: {{$usuarioGenerador}}
      <br/>
   </body>
</html>