/*********************************/
/* Consultar Tramite Pendiente	*/
/*	JG-18/11/2020				*/
/********************************/
jQuery.noConflict();
jQuery(document).ready(function(){
	var grilla='';
	var nroSeguimiento ='';
	var newURL = window.location.protocol + "//" + window.location.host;
	jQuery('a[href="' + newURL + '"]').parent().addClass('active');
	jQuery.ajax({

		type: 'GET',
		url: 'consulta-nrotramite-pendientes',
		dataType:'json',
		data: {},
		success: function (data) {
			for(var i=0; i<data['tramites'].length; i++) {
				nroSeguimiento = data.tramites[i]["nro_seguimiento"];

				grilla+='<td class=" col-lg-2" id="tramite">'+data.tramites[i]["tramite"]+'</td>';
				grilla+='<td class=" col-lg-2" id="estado">'+data.tramites[i]["estado"]+'</td>';
				grilla+='<td class=" col-lg-2" id="nro_seguimiento">'+data.tramites[i]["nro_seguimiento"]+'</td>';
				grilla+=' <!-- <td class=" col-lg-2" id="accion"><a  class="btn-primary"  href="'+newURL+'/CAS_Habilitaciones/public/detalle-tramite-nro-seguimientoP/'+nroSeguimiento+'">IR</a></td> -->';
				grilla+='</tr>';
			}

			jQuery('#cuerpo').html('<tr>'+grilla);
		},
		error: function(data){
			console.log('error');
		}
	});

	jQuery('#accion').click(function(){
		alert('click');
	});
});
