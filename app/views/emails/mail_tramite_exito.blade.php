<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="utf-8">
   </head>
   <body>
      Su trámite de: <b>{{$tipoTramite}}</b> se ha generado exitosamente. 

      @if(($paso_fecha==0 && $meses==0 && $dias==0) || ($meses<0 && $dias<0) || $meses<0)
      @elseif($paso_fecha==0)
         <br/>
         @if($meses==0)
            @if($dias==1)
               <div style="color:#ff0000"> Importante: Último {{$tipoTramite}} hace: {{$dias}} dia.</div>
            @elseif($dias==0)
               <div style="color:#ff0000"> Importante: Último {{$tipoTramite}} hoy.</div>
            @else
               <div style="color:#ff0000"> Importante: Último {{$tipoTramite}} hace: {{$dias}} dias.</div>
            @endif
         @elseif($meses==1)
            <div style="color:#ff0000"> Importante: Último {{$tipoTramite}} hace: {{$meses}} mes.</div>
         @else
            <div style="color:#ff0000"> Importante: Último {{$tipoTramite}} hace: {{$meses}} meses.</div>
         @endif
      @endif
      
      <br/>
      <h3>Nº de seguimiento: <strong>{{$nroTramite}}</strong></h3> 
      <br/>
      Trámite generado por: {{$usuarioGenerador}}
      <br/>
      Agente: {{$nroAgente}}/{{$nroSubAgente}} 
      <br/>
      Nº Permiso: {{$nroPermiso}}
      <br/>
<!-- no anda en esta version de laravel!	  
	  @isset($tipoTramite)
	     Tipo de Tramite no definido<br/>
	  @endisset
	  @empty($tipoTramite)
	     Tipo de Tramite vacío<br/>
	  @endempty
-->	  
	  @if($tipoTramite == 'Cambio de Domicilio')
		  Nuevo Domicilio: {{$domicilio_nuevo}}
		  <br/>
		  Localidad: {{$localidad_nuevo}}
		  <br/>
	  @endif
	  Motivo: {{$motivo}}
	  <br/>
      
      <h4>Para completar el trámite, debe cumplimentar con toda la documentación y requisitos, que se especifican en la "Guía de trámites", y mandar por sobre cerrado con la carátula impresa que emite el sistema, a Mesa de Entrada de Santa Fe o Rosario, según le corresponda por ubicación geográfica.
      <br>
      Para conocer el estado actual de su trámite, puede consultarlo desde la opción "Consulta de trámites" de la "Guía de trámites", en el portal, informando el número de seguimiento del mismo.
      <br>
      Recuerde que los trámites, tienen fecha de expiración, y se deben completar en tiempo y forma
      </h4>
      <a href="{{$linkguia}}" target="_blank">Guía de trámites</a>      
   </body>
</html>

		
		