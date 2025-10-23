<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="utf-8">
   </head>
   <body>
      Su trámite de: {{$tipoTramite}} se ha cancelado exitosamente. 
      <br/>
      Trámite cancelado por: {{$usuarioGenerador}}
      <br/>
      Agente: {{ $nroAgente}}/{{$nroSubAgente}} 
      <br/>
      Nº Permiso {{$nroPermiso}}
      <br/>
      Para imprimir la carátula del trámite haga click aquí:
      <a href="{{$linkPDF}}" target="_blank">PDF</a>
   </body>
</html>

		
		