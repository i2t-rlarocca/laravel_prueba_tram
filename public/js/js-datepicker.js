jQuery.noConflict(); 
var j = jQuery.noConflict(); 
j(function() {     
	//Array para dar formato en español
	j.datepicker.regional['es'] =
	{
	closeText: 'Cerrar',
	prevText: 'Próximo',
	nextText: 'Previo',
	monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
	'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
	monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
	'Jul','Ago','Sep','Oct','Nov','Dic'],
	monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año',
	dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
	dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'],
	dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
	dateFormat: 'dd/mm/yy', firstDay: 0,
	initStatus: 'Selecciona la fecha', isRTL: false};
	j.datepicker.setDefaults(j.datepicker.regional['es']);
	j(".datepicker" ).datepicker({ maxDate: "" });
});

