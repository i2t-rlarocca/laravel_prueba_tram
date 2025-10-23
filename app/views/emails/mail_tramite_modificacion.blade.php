<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="utf-8">
   </head>
   <body>
      @if($esNuevoTramite)
         El estado de sus trámites para nuevo permiso: <strong>{{$nro_permiso}}</strong></strong> han cambiado del estado:<strong>{{$estadoI}}</strong> al estado <strong>{{$estadoF}}</strong>.
			@if(isset($observaciones))
				<strong>Observaciones:</strong> {{$observaciones}}
				<br/>
			@endif
         Estado cambiado por: {{$usuarioGenerador}}
      <br/>
			
      @else
         El estado de su trámite de: <strong>{{$tipoTramite}}</strong> cuyo Nº de seguimiento es:<strong>{{$nroTramite}}</strong> ha cambiado del estado:<strong>{{$estadoI}}</strong> al estado <strong>{{$estadoF}}</strong>.
    
	   <br/>@if(isset($agente))
		  <strong>Agente/Subagente:</strong> {{$agente.'/'.$subagente}}
				<br/>
			@endif
		  
      <br/>
         <strong>Observaciones:</strong> {{$observaciones}}
         <br/>
         Estado cambiado por: {{$usuarioGenerador}}
         <br/>
      @if(isset($linkPDF))
         Para imprimir la carátula del trámite haga click aquí:
         <a href="{{$linkPDF}}" target="_blank">CARÁTULA</a>
      @endif
      @if(isset($linkPDF2))
         <br/>
         Para imprimir la nota de solicitud del trámite haga click aquí:
         <a href="{{$linkPDF2}}" target="_blank">NOTA</a>
      @endif
	    @if(isset($linkEncuesta))
         <br/> <br/>
         <a href="{{$linkEncuesta}}" target="_blank">Señor agenciero complete la encuesta de satisfacción</a>
      @endif
      <br/>
      <br/>
      <a href="{{$linkguia}}" target="_blank">Guía de trámites</a> 

      @endif
   </body>
</html>