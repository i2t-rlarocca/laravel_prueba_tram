//varias funciones javascript que sirven para varias pantallas.

function refrescar() {
  var table = document.getElementById("cuerpo");
  for (var i = table.rows.length - 1; i > 0; i--) {
    table.deleteRow(i);
  }
  //document.getElementById("tabla-tramites").deleteTHead();
  //reset de todos los elementos del formulario
  document.getElementById("formulario-tramite").reset();

  var fd = document.getElementById("fecha_desde");
  fd.value = "";
  var fh = document.getElementById("fecha_hasta");
  fh.value = "";
  var seleccionado = document.getElementById('id_seleccionado').value;
  if (seleccionado != "") {
    document.getElementById('id_seleccionado').value = "";
  }

  document.getElementById('n_paginar').value = "5";
  var mytable = document.getElementById("pie_tabla")
  while (mytable.rows.length > 0) //deletes all rows of a table
    mytable.deleteRow(0)
  if (typeof document.getElementById('btn-csv') != 'undefined') {
    var valorBtnCSV = document.getElementById("btn-csv").style.visibility;
    if (valorBtnCSV == "visible") {
      document.getElementById("btn-csv").style.visibility = "hidden";
    }
  }
  var valorBtnImprimir = document.getElementById("btn-imprimir").style.visibility;
  if (valorBtnImprimir == "visible") {
    document.getElementById("btn-imprimir").style.visibility = "hidden";
  }
  document.getElementById("errores").style.display = "none";

  //seteamos los estados por defecto
  var estados = ["0", "1", "2", "3", "4", "5", "6"];
  var el = document.getElementById("id_estados");
  for (var j = 0; j < estados.length; j++) {
    for (var i = 0; i < el.length; i++) {
      if (el[i].value == estados[j]) {
        el[i].selected = true;
      }
    }
  }

  //seteamos los trámites por defecto
  var tramites = ["12"]; //Todos por defecto
  var tt = document.getElementById("id_tramites_c");
  for (var j = 0; j < tramites.length; j++) {
    for (var i = 0; i < tt.length; i++) {
      if (tt[i].value == tramites[j]) {
        tt[i].selected = true;
      }
    }
  }
}
