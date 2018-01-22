<html>
<head>
	<title>Sistema Experto</title>
	<meta charset="UTF-8">
</head>
<?php include_once 'linker.php'; ?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#btnSintoma").click(function(e){
			e.preventDefault();
			ListarSintoma(1);
		});

		$("#btnAddDiagnostico").click(function(){
			$("#modalAddDiagnostico").modal("show");
				ListarDiagnostico();
		});

		$("#btnDiagnosticoAdd").click(function(){
			//$("#modalAddDiagnostico").modal("show");
			$("#tableTratamiento tbody tr").remove();
			$("#tableSintomas tbody tr").remove();
			$("#txtEdadAnnoMin").val("0");
			$("#txtEdadMesMin").val("0");
			$("#txtEdadAnnoMax").val("18");
			$("#txtEdadMesMax").val("0");
			$("#txtDiagnostico").val("");
			$("#txtProblema").val("");
			$("#txtObsDiag").val("_");
			$("#txtObsDiag").val("_");
			$("#txtTempId").val("");
			$("#modalDiagnostico").modal("show");
		});

		$("#btnAddTratamiento").click(function(){
				$("#txtIdTratamiento").val("0");
				$("#txtCompuesto").val("");
				$("#txtObs").val("");
				$("#txtTomasDia").val("");
				$("#txtNroDia").val("");
				$("#txtDosisXPeso").val("");
				$("#txtCantSol").val("");
				$("#txtUnidadDosisXPeso").val("");
				$("#modalAddTratamiento").modal("show");
		});

		$("#btnAddSintomas").click(function(){
			ListarSintoma("diagnostico");
		});

		$("#btnCompuesto").click(function(){
			$("#modalCompuestoAdd").modal("show");
			ListarCompuesto();
		});

		$("#btnCompuestoAdd").click(function(){
			$("#modalCompuestoSave").modal("show");
		});

		$("#btnCompuestoSave").click(function(){
			//var compuesto = $("#txtCompuestoS").val();
			var xhr = $.ajax({
				url: "gen_productocompuestoguardar.php",
				type: "get",
				data: {"productocompuestoExperto" : $("#txtCompuestoS").val(), "usuario" : "jeam"},
				dataType: "html",
				success: function(respuesta){
					if (respuesta) {
						ListarCompuesto();
					}
					$("#modalCompuestoSave").modal("hide");
					$("#txtCompuestoS").val("");
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
        		alert("Status: " + textStatus); alert("Error: " + errorThrown);
    			}
			});
			console.log(xhr);
		});

		$("#btnSintomaSave").click(function(){
			var xhr = $.ajax({
				url: "V_ExpertoSintomaGuardar.php",
				type: "POST",
				data: {"IdSintoma" : $("#txtIdSintoma").val()  ,"Sintoma" : $("#SintomaN").val()},
				dataType: "html",
				success: function(respuesta){
					alert(respuesta);
					$("#modalSintoma").modal("hide");
					ListarSintoma("diagnostico");

				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
        		alert("Status: " + textStatus); alert("Error: " + errorThrown);
    			}
			});
			console.log(xhr);
		});

		$("#tableTratamiento tbody").on("click", "tr", function(){
			$("#txtIdTratamiento").val($(this).children("td").eq(0).text());
			// $("#txtDiagnostico").val($("#txtTempId").val());
			$("#txtCompuesto").val($(this).children("td").eq(2).text());
			$("#txtNroDia").val($(this).children("td").eq(3).text());
			$("#txtTomasDia").val($(this).children("td").eq(4).text());
			$("#txtObs").val($(this).children("td").eq(5).text());
			$("#txtDosisXPeso").val($(this).children("td").eq(6).text());
			$("#txtCantSol").val($(this).children("td").eq(7).text());
			$("#txtUnidadDosisXPeso").val($(this).children("td").eq(8).text());

			$("#modalAddTratamiento").modal("show");
		});

		$("#btnTratamientoAdd").click(function(){
			var arr = [];
			arr.push(parseInt($("#txtIdTratamiento").val()), $("#txtTempId").val(), $("#txtCompuesto").val(),
				$("#txtObs").val(), parseFloat($("#txtTomasDia").val()), parseInt($("#txtNroDia").val()), parseFloat($("#txtDosisXPeso").val()), parseFloat($("#txtCantSol").val()), $("#txtUnidadDosisXPeso").val());
			console.log(JSON.stringify(arr));
			var xhr = $.ajax({
				url: "v_expertotratamientoguardar.php",
				type: "post",
				data: {data : JSON.stringify(arr)},
				dataType: "json",
				success: function(respuesta){
					$("#modalAddTratamiento").modal("hide");
					console.log(respuesta);
					if (respuesta.length==0) {
						ListarTratamiento($("#txtTempId").val());
					} else {
					//var response = JSON.parse(respuesta);
					var fila="";
					//$.each(response, function(data, value){
					fila = "<tr><td>"+respuesta.IdTratamiento+"</td><td>"+respuesta.Tratamiento+"</td><td style='display:none;'>"+respuesta.ProductoCompuesto+"</td><td style='display:none;'>"+respuesta.NroDias+"</td><td style='display:none;'>"+respuesta.TomasXDia+"</td><td style='display:none;'>"+respuesta.Observacion+"</td><td style='display:none;'>"+respuesta.DosisXPeso+"</td><td style='display:none;'>"+respuesta.Concentracion+"</td><td style='display:none;'>"+respuesta.UnidadDosisXPeso+"</td><td><a class='btn' onclick='EliminarTratamientoXDiagnostico("+respuesta.IdTratamiento+");'><i class='fa fa-trash'></i></a></td></tr>";
					$("#tableTratamiento tbody").append(fila);
				//});
					// $("#tableTratamiento").append("<tr><td>"+response.IdTratamiento+"</td><td>"+response.Tratamiento+"</td><td style='display:none;'>"+response.ProductoCompuesto+"</td><td style='display:none;'>"+response.NroDias+"</td><td style='display:none;'>"+response.TomasXDia+"</td><td style='display:none;'>"+response.Observacion+"</td></tr>");
					$("#modalAddTratamiento").modal("hide");
				}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
        		alert("Status: " + textStatus); alert("Error: " + errorThrown);
    			}
			});
			console.log(xhr);
		});



	$("#btnDiagnosticoGuardar").click(function(){
			var arr = [];
			document.getElementById('txtEdadDiagnostico').value=fnCalcularEdad();

			arr.push($("#txtDiagnostico").val(), $("#txtProblema").val(), $("#txtEdadDiagnostico").val(), $("#txtObsDiag").val(), $("#txtTempId").val());
			var arrTableTratamiento = [];
			$("#tableTratamiento tbody").each(function(){
				$("#tableTratamiento tbody tr").each(function(){
					arrTableTratamiento.push([parseInt($(this).children("td").eq(0).html()), $(this).children("td").eq(1).html()]);
				});
			});
			var arrTableSintoma = [];
				$("#tableSintomas tbody tr").each(function(){
					arrTableSintoma.push([parseInt($(this).children("td").eq(0).html()), $(this).children("td").eq(1).html()]);
				});
			console.log(JSON.stringify(arrTableTratamiento) + " -- "+JSON.stringify(arrTableSintoma));
			var xhr = $.ajax({
				url: "V_ExpertoDiagnosticoGuardar.php",
				type: "post",
				data: {data : JSON.stringify(arr), data2 : JSON.stringify(arrTableTratamiento), data3 : JSON.stringify(arrTableSintoma)},
				dataType: "html",
				success: function(respuesta){
					alert("Diagnostico guardado");
					$("#modalDiagnostico").modal("hide");
					ListarDiagnostico();
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
        		alert("Status: " + textStatus); alert("Error: " + errorThrown);
    			}
			});
			console.log(xhr);
		});

	$("#tableDiagnostico tbody").on("click", "tr", function(){
		$("#modalTratamientoPreO").modal("show");
		$("#txtDiagnosticoPreO").val($(this).children("td").eq(1).html());
		$("#txtObservacionPreO").val($(this).children("td").eq(3).html());
		var xhr = $.ajax({
        	url: "../controllers/server_processingCompuestoXDiagnostico.php",
        	type: "get",
        	data: {"diagnostico": $(this).children("td").eq(0).html()},
        	dataType : "html",
        	success : function(res){
        		var response = JSON.parse(res);
        		var fila = "";
        		$("#tableSintomas tbody tr").remove();
        		$.each(response, function(data, value){
        			fila = "<tr><td>"+ value.IdSintoma + "</td><td>" + value.Sintoma + "</td><td><a class='btn' onclick='EliminarSintomaXDiagnostico("+value.IdSintoma+");'><i class='fa fa-trash'></i></a></td></tr>";
        			$("#tableSintomas tbody").append(fila);
        		});
						$("#tableSintoma tbody tr").each(function(){
							var sintoma = $(this).children("td").eq(0).text();
							$("#tableSintomas tbody tr").each(function(){
								if(sintoma == $(this).children("td").eq(0).text()){
									//$(this).css("background-color","red");
									$(this).children("td").eq(0).css("background-color", "#e57373")
									$(this).children("td").eq(1).css("background-color", "#e57373")
									// console.log("igual "+$(this).children("td").eq(0).text());
								}
							});
						});
					},
        	error : function(XMLHttpRequest, textStatus, errorThrown){
        		alert("Status : "+textStatus);
        	}

        });
		$("#txtEdadPreO").val($(this).children("td").eq(4).text());
		$("#tableTratamientoPreO tbody").empty();
		var xhr = $.ajax({
			url: "../controllers/server_processingDiagnosticoXTratamiento.php",
			type: "get",
			data: {"diagnostico":$("#txtDiagnosticoPreO").val(), "edad":$(this).children("td").eq(5).text()},
			dataType: "html",
			success: function(respuesta){
				var response = JSON.parse(respuesta);
				//var arrNroDias = [];
				//console.log(response);
				$.each(response, function(data, value){
					var fila = "<tr><td class='nombreCompuesto'>" + value.ProductoCompuesto + "</td><td>" + value.TomasXDia*value.NroDias + "</td><td></td><td></td><td>"+ value.DosisXPeso + "</td><td>"+ value.Concentracion+ "</td><td id='txtpeso' contenteditable='true'>"+0+"</td><td style='display:none;'>"+value.NroDias+"</td><td style='display:none;'>"+value.TomasXDia+"</td><td style='display:none;'>"+value.NroDias+"</td></tr>";
					$("#tableTratamientoPreO tbody").append(fila);
					console.log(value.NroDias + " - ");
					//console.log($("#txtMaxPreO").val());
					//arrNroDias.push(value.NroDias);
					if (parseInt($("#txtMaxPreO").val())<parseInt(value.NroDias)) {
						$("#txtMaxPreO").val(value.NroDias);
					}
				});
				//nroDiasXTomas(arrNroDias);
				$("#txtNroSelPreO").val($("#txtMaxPreO").val());
				$("#txtNroSelPreO").attr({max : $("#txtMaxPreO").val()});
				//$("#txtEdadPreO").val($(this).children("td").eq(4).text());
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
        		alert("Status: " + textStatus); alert("Error: " + errorThrown);
    		}
		});

		console.log(xhr);
	});

	$("#tableTratamientoPreO tbody").on("click", "tr td", function(e){
		console.log($(this).attr("id"));
		if ($(this).attr("id")=="txtpeso") {
			console.log("oli");
		} else {
			var compuesto = $(this).parents("tr").children("td").eq(0).text();
			$("#tempCompuesto").val(compuesto);
			console.log(compuesto);
			ListarProductos(compuesto);
		}

	});

	$("#txtEdadA").on("keyup", function(e){
		verificarTablaDiagnostico();
		// e.stopPropagation();
	});
	$("#txtEdadM").keyup(function(e){
		// e.stopPropagation();
		verificarTablaDiagnostico();

	});

	$("#txtNroSelPreO").on({
		//los dias no pueden
		change: function(){
			agregarDiasTratamiento();
		},
		keyup: function(){
			agregarDiasTratamiento();
		}
	});

	});


function agregarDiasTratamiento(){
			if($("#txtNroSelPreO").val()>$("#txtMaxPreO").val() || $("#txtNroSelPreO").val()<1){
		}else{
		if($("#txtNroSelPreO").val() == $("#txtMaxPreO").val()){
			nroDiasOriginal();
		}else{

			$("#tableTratamientoPreO tbody").each(function(e){
			$("#tableTratamientoPreO tbody tr").each(function(e){
				$(this).children("td").eq(7).html($("#txtNroSelPreO").val());
				$(this).children("td").eq(1).html(parseInt($(this).children("td").eq(7).html()) * parseInt($(this).children("td").eq(8).html()));
		});
	});
		}
		}
}

function agregarProducto(){
		var producto ="";
		var precio = "";
		var compuesto = $("#tempCompuesto").val();
	$("#tableProductosAdd tbody").off("click").on("click", "tr", function(e){
		producto = $(this).children("td").eq(1).html();
		precio = $(this).children("td").eq(2).html();
		$("#tableTratamientoPreO tbody").each(function(e){
		$("#tableTratamientoPreO tbody tr").each(function(e){
			if (compuesto == $(this).children("td").eq(0).html()) {
				$(this).children("td").eq(2).html(producto);
				$(this).children("td").eq(3).html(precio);
				console.log($(this).children("td").eq(0).html() + " - " + producto);
			}
		});
	});
		$("#modalProductosAdd").modal("hide");
		$("#tempCompuesto").val("");
		//$("#tableTratamientoPreO tbody tr").off("click");
	});
}

function ListarDiagnostico(){
	$("#tableDiagnosticoAdd").DataTable().destroy();
			    var table4 = $("#tableDiagnosticoAdd").DataTable({
      			"bProcessing": true,
      			"sAjaxSource": "../controllers/server_processingDiagnostico.php",
      			"bPaginate":true,
      			"sPaginationType":"full_numbers",
      			"iDisplayLength": 5,
      			"aoColumns": [
      			{ mData: 'IdDiagnostico' } ,
      			{ mData: 'Diagnostico' },
      			{ mData: 'Problema' },
      			{ mData: 'Observacion' },
      			{ mData: 'Edad' }
      			]
    		});
	    $('#tableDiagnosticoAdd tbody').off("click").on('click', 'tr', function () {
	    $("#tableSintomas tbody tr").remove();
	    $("#txtTempId").val($(this).children("td").eq(0).html());
        $("#txtDiagnostico").val($(this).children("td").eq(1).html());
        $("#txtProblema").val($(this).children("td").eq(2).html());
        $("#txtObsDiag").val($(this).children("td").eq(3).html());
        var str = pad($(this).children("td").eq(4).html().toString(),8);
        $("#txtEdadAnnoMin").val(str.substring(4,6));
        $("#txtEdadMesMin").val(str.substring(6,8));
        $("#txtEdadAnnoMax").val(str.substring(0,2));
        $("#txtEdadMesMax").val(str.substring(2,4));

        ListarTratamiento($(this).children("td").eq(0).text());

        var xhr = $.ajax({
        	url: "../controllers/server_processingCompuestoXDiagnostico.php",
        	type: "get",
        	data: {"diagnostico": $(this).children("td").eq(0).html()},
        	dataType : "html",
        	success : function(res){
        		var response = JSON.parse(res);
        		var fila = "";

        		$.each(response, function(data, value){
        			fila = "<tr><td>"+ value.IdSintoma + "</td><td>" + value.Sintoma + "</td><td><a class='btn' onclick='EliminarSintomaXDiagnostico("+value.IdSintoma+");'><i class='fa fa-trash'></i></a></td></tr>";
        			$("#tableSintomas tbody").append(fila);
        		});
        	},
        	error : function(XMLHttpRequest, textStatus, errorThrown){
        		alert("Status : "+textStatus);
        	}

        });
        console.log(xhr);
        $("#modalDiagnostico").modal("show");

    });
}

function ListarTratamiento(diagnostico){
	var chr = $.ajax({
		url: "../controllers/server_processingTratamientoXDiagnostico.php",
		type: "get",
		data: {"diagnostico": diagnostico},
		dataType : "html",
		success : function(res){
			// $("#modalAddTratamiento").modal("hide");
			$("#tableTratamiento tbody tr").remove();
			var response = JSON.parse(res);
			var fila = "";
			$.each(response, function(data, value){
				//fila = "<tr><td>"+ value.IdTratamiento + "</td><td>" + value.Tratamiento + "</td><td><a class='btn' onclick='EliminarTratamientoXDiagnostico("+value.IdTratamiento+");'><i class='fa fa-trash'></i></a></td></tr>";
		fila = "<tr><td>"+value.IdTratamiento+"</td><td>"+value.Tratamiento+"</td><td style='display:none;'>"+value.ProductoCompuesto+"</td><td style='display:none;'>"+value.NroDias+"</td><td style='display:none;'>"+value.TomasXDia+"</td><td style='display:none;'>"+value.Observacion+"</td><td style='display:none;'>"+value.DosisXPeso+"</td><td style='display:none;'>"+value.Concentracion+"</td><td style='display:none;'>"+value.UnidadDosisXPeso+"</td><td><a class='btn' onclick='EliminarTratamientoXDiagnostico("+value.IdTratamiento+");'><i class='fa fa-trash'></i></a></td></tr>";
		//$("#tableTratamiento").append();
				$("#tableTratamiento tbody").append(fila);
			});
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("Status : "+textStatus);
		}
	});
	console.log(chr);
}

function nroDiasOriginal(){
			$("#tableTratamientoPreO tbody").each(function(e){
			$("#tableTratamientoPreO tbody tr").each(function(e){
				$(this).children("td").eq(7).html($(this).children("td").eq(9).html());
				$(this).children("td").eq(1).html(parseInt($(this).children("td").eq(7).html()) * parseInt($(this).children("td").eq(8).html()));
		});
	});
}

/*function nroDiasXTomas(){
	var arrNroDias = [];
	$("#tableTratamientoPreO tbody").each(function(e){
		$("#tableTratamientoPreO tbody tr").each(function(e){
			arrNroDias.push(parseInt($(this).children("td").eq(4).html()));
			console.log("arrar"+$(this).children("td").eq(4).html());
		});
	});
	return Math.max.apply(null, arrNroDias);
}*/

function ListarProductos(compuesto){
		$("#modalProductosAdd").modal("show");
		$("#tableProductosAdd").DataTable().destroy();
	    var table4 = $("#tableProductosAdd").DataTable({
      	"bProcessing": true,
      	"bPaginate":true,
      	"sPaginationType":"full_numbers",
      	"iDisplayLength": 5,
      	"ajax": {
      		"url": "../controllers/server_processingProductoCompuesto.php",
      		"type": "GET",
      		"cache" : "false",
      		"data": {
      			"Producto": "",
      			"Compuesto": "",
      			"CompuestoNombre": compuesto
      		}
      	},
      	"aoColumns": [
      	{ mData: 'IdProducto' } ,
      	{ mData: 'Producto' },
      	{ mData: 'PrecioContado' },
      	{ mData: 'PrecioPorMayor' }
      	]
    });
		agregarProducto();
}

function verificarTablaDiagnostico(){
	var tamanio = $("#tableSintoma tbody tr").length;
	console.log(tamanio);
	var criterioBusqueda = "";
	var cont = 1;
	var criterioFinal = "";
	if(tamanio>0){
		$("#tableSintoma tbody tr").each(function(){
			var sintoma = $(this).children("td").eq(1).html();
			if (cont == 1) {
				criterioBusqueda = "'"+sintoma.concat("',");
				console.log(criterioBusqueda);
			}else{
				criterioBusqueda = criterioBusqueda.concat("'"+sintoma.concat("',"));
				console.log(criterioBusqueda);
			}
			cont = parseInt(cont)+1;
		});
		criterioFinal = '"'+criterioBusqueda.substr(0,criterioBusqueda.length-1)+'"';
	}
	console.log(criterioFinal + fnCalcularEdad2($("#txtEdadA").val(), $("#txtEdadM").val()));
	var arr = [];
	arr.push(parseInt(fnCalcularEdad2($("#txtEdadA").val(), $("#txtEdadM").val())), criterioFinal, tamanio);
	console.log(JSON.stringify(arr));

   var timer = $.ajax({
    url: 'v_expertosintomabuscard.php',
    type: 'post',
    data: {data : JSON.stringify(arr)},
		// cache: false,
    dataType: 'html',
    success: function(respuesta){
			$("#tableDiagnostico tbody").empty();
      $.each(JSON.parse(respuesta), function(key, value){
            console.log(key + ":" + value + "--" + value.Edad);
						console.log("adad" + fnCalcularEdad2(parseInt($("#txtEdadA").val()), $("#txtEdadM").val()));
            console.log("adassd" + fnCalcularEdad2(parseInt($("#txtEdadA").val())+5, $("#txtEdadM").val()));
            // if (value.Edad >=fnCalcularEdad2(parseInt($("#txtEdadA").val()), $("#txtEdadM").val()) && value.Edad <= fnCalcularEdad2(parseInt($("#txtEdadA").val()), $("#txtEdadM").val())) {
						// if (fnCalcularEdad2(parseInt($("#txtEdadA").val()), $("#txtEdadM").val()) >= value.Edad && fnCalcularEdad2(parseInt($("#txtEdadA").val()), $("#txtEdadM").val())) {
						if (BuscarPorEdades(value.Edad, $("#txtEdadA").val(), $("#txtEdadM").val()) == 1) {
							$("#tableDiagnostico tbody").append("<tr><td>"+ value.IdDiagnostico+"</td><td>"+value.Diagnostico+"</td><td>"+value.Problema+"</td><td>"+value.Observacion+"</td><td>"+fn_rangoEdades(value.Edad)+"</td><td style='display:none;'>"+value.Edad+"</td></tr>");
						}else {
							//alert("")
						}
            // }
        })

    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    },

	});
			/*xhr.always(function(){
				/*var elemento = $("#tableDiagnostico tbody").find("tr").eq(0).children("td").eq(0).text();
				var c = 0;
				/*$("#tableDiagnostico tbody tr").each(function(){
					if(elemento == $(this).children("td").eq(0).text()){
						c++;
					}
				});

			});*/
  console.log(timer);
}

function ListarCompuesto () {
			$("#tableCompuesto").DataTable().destroy();
			var table4 = $("#tableCompuesto").DataTable({
      			"bProcessing": true,
      			"sAjaxSource": "../controllers/server_processingCompuesto.php",
      			"bPaginate":true,
      			"sPaginationType":"full_numbers",
      			"iDisplayLength": 5,
      			"aoColumns": [
      			{ mData: 'IdProductoCompuesto' } ,
      			{ mData: 'ProductoCompuesto' }
      			]
    		});
			$("#tableCompuesto tbody").on("click", "tr", function(e) {

				$("#txtCompuesto").val($(this).children("td").eq(1).html());
				$("#modalCompuestoAdd").modal("hide");
		  });

}

function EliminarSintoma(idSintoma) {
	$("#tableSintoma tbody tr").each(function(){
		if (idSintoma == $(this).children("td").eq(0).html()) {
				$(this).remove();
				$.notify({
					icon: 'fa fa-trash',
					message: "<strong>"+$(this).children("td").eq(1).html()+"</strong> Ha sido eliminado"
				});
				verificarTablaDiagnostico();
		}
	});
}

function EliminarSintomaXDiagnostico(Compuesto) {
	$("#tableSintomas tbody tr").each(function(){
		if (Compuesto == $(this).children("td").eq(0).text()) {
				$(this).remove();
				$.notify({
					icon: 'fa fa-trash',
					message: "<strong>"+$(this).children("td").eq(1).html()+"</strong> Ha sido eliminado"
				});
				//verificarTablaDiagnostico();
		}
	});
}

function EliminarTratamientoXDiagnostico(Tratamiento) {
	$("#tableTratamiento tbody tr").each(function(){
		if (Tratamiento == $(this).children("td").eq(0).html()) {
				$(this).remove();
				$.notify({
					icon: 'fa fa-trash',
					message: "<strong>"+$(this).children("td").eq(1).html()+"</strong> Ha sido eliminado"
				});
				//verificarTablaDiagnostico();
		}
	});
}

function pad(num, size) {
		var s = num+"";
		while (s.length < size) s = "0" + s;
		return s;
	};
function fnCalcularEdad(){
		var vEdadMax=parseInt($("#txtEdadAnnoMax").val());
		var vEdadMes=parseInt($("#txtEdadMesMax").val());
		//alert ("vEdadMax " + vEdadMax + "  vEdadMes " + vEdadMes);
		if (vEdadMax==0){
			//alert ("vEdadMax IGUAL A CERO");
			if (vEdadMes==0){
				vEdadMax="";
			}else{
				vEdadMax=$("#txtEdadMesMax").val();
			}

		}else{
			vEdadMax=vEdadMax + "" + pad($("#txtEdadMesMax").val(),2);
		}

		var vEdadMin=pad($("#txtEdadAnnoMin").val(),2) + "" + pad($("#txtEdadMesMin").val(),2);

		var vEdad=vEdadMax + "" + vEdadMin;
		//document.getElementById('txtEdadDiagnostico').value=pad($("#txtEdadAnnoMax").val(),2) + "" + pad($("#txtEdadMesMax").val(),2) + "" +
		//	pad($("#txtEdadAnnoMin").val(),2) + "" + pad($("#txtEdadMesMin").val(),2);
		//ert ("MAXIMO " + vEdadMax + "  min " + vEdadMin);


		return vEdad;
		};

function fnCalcularEdad2 (edades, meses) {
	var vEdadMax =edades ;
	var vEdadMes = meses;
	if (vEdadMax==0){
			//alert ("vEdadMax IGUAL A CERO");
			if (vEdadMes==0){
				vEdadMax="";
			}else{
				vEdadMax=vEdadMes;
			}

		}else{
			vEdadMax=vEdadMax + ""+pad(vEdadMes,2)+"0000" ;
		}
		var vEdad=vEdadMax;

		return vEdad;
}

function BuscarPorEdades(edad1, edadI, mesI) {
	var NuevaEdadI = edadI, NuevoMesI = mesI, result;
	if(edadI.length<=1){
		NuevaEdadI = pad(edadI,2);
	}
	if (mesI.length<=1) {
		NuevoMesI = pad(mesI,2);
	}
	var EdadIngresada = parseInt(NuevaEdadI.concat(NuevoMesI));
	var EdadDesde = parseInt(edad1.substr(edad1.length-4));
	var EdadHasta = parseInt(edad1.substr(0,edad1.length-4));
	if (EdadIngresada >= EdadDesde && EdadIngresada <= EdadHasta) {
		result = 1;
	}else {
		result = 0;
	}
	return result;
}

function EditarSintoma(idSintoma) {
	//$("#SintomaN").val();
	/*$("#tableSintomas tbody tr").each(function(e){
		console.log($(this).children("td").eq(0).text());
		if (idSintoma == $(this).children("td").eq(0).text()) {
			$(this).remove();
			console.log("Entro pero no elimino");
		}
		console.log("Entro pero no elimino" + idSintoma);
	});*/

	$("#tableSintomaAddDiagnostico tbody tr").each(function(e){
		if (idSintoma == $(this).children("td").eq(0).text()) {
			//$(this).children("td").eq(2).text("e");
			$("#txtIdSintoma").val(idSintoma);
			$("#SintomaN").val($(this).children("td").eq(1).text());
		}
	});
	$("#modalSintoma").modal("show");
}

function ListarSintoma(env){
			document.getElementById('txtEdadDiagnostico').value=fnCalcularEdad();
			if (env == 1) {
				$("#modalAddSintoma").modal("show");
				$("#tableSintomaAdd").DataTable().destroy();
			    var table4 = $("#tableSintomaAdd").DataTable({
      			"bProcessing": true,
      			"bPaginate":true,
      			"sPaginationType":"full_numbers",
      			"iDisplayLength": 5,
      			"ajax": {
      				"url": "../controllers/server_processingSintomas.php",
      				"type": "POST",
      				"data": {
      					"edad" : parseInt($("#txtEdadDiagnostico").val())
      				}
      			},
      			"aoColumns": [
      			{ mData: 'IdSintoma' } ,
      			{ mData: 'Sintoma' }
      			]
    		});
			$("#tableSintomaAdd tbody").off("click").on("click", "tr", function(e) {

				$("#tempIdSintoma").val($(this).children("td").eq(0).html());
				$("#tempSintoma").val($(this).children("td").eq(1).html());


			  	var Encontrado = 0;

		        	$("#tableSintoma tbody").each(function(index, el) {
		                    $("#tableSintoma tbody tr").each(function(index, el) {
		                    var sintoma = $(this).find('.nombreSintoma').html();
		              if (sintoma == $("#tempSintoma").val()) {
		             Encontrado = 1;
		              }
		        	});

		        //console.log(Encontrado);

		        if (Encontrado ==0) {
		          var fila = "<tr><td>"+ $("#tempIdSintoma").val() +"</td><td class='nombreSintoma'>"+ $("#tempSintoma").val() +"</td><td class='text-center'><a id='btnEliminarSintoma' class='btn' onclick='EliminarSintoma("+$("#tempIdSintoma").val()+")'><i class='fa fa-trash'></i></a></td></tr>";
		        $("#tableSintoma tbody").append(fila);

		        }
		        verificarTablaDiagnostico();
			});
			});

			} else if(env == "diagnostico"){
				$("#modalAddSintomaDiagnostico").modal("show");
				$("#tableSintomaAddDiagnostico").DataTable().destroy();
			    var table4 = $("#tableSintomaAddDiagnostico").DataTable({
      			"bProcessing": true,
      			"bPaginate":true,
      			"sPaginationType":"full_numbers",
      			"iDisplayLength": 5,
      			"ajax": {
      				"url": "../controllers/server_processingSintomas.php",
      				"type": "POST",
      				"data": {
      					"edad" : parseInt($("#txtEdadDiagnostico").val())
      				}
      			},
      			"aoColumns": [
      			{ mData: 'IdSintoma' } ,
      			{ mData: 'Sintoma' },
						{ mRender : function(data, type, row){
             return "<a onclick='EditarSintoma("+ row.IdSintoma +");' class='btn'><i class='fa fa-pencil'></i></a>"
            }}
      			]
    		});
			    //SeleccionarSintoma();
						SeleccionarSintoma2();

				$("#btnAddSintoma").click(function(){
					//document.getElementById('SintomaEdadN').value=fnCalcularEdad();
					$("#SintomaN").val("");
					$("#txtIdSintoma").val("");
					$("#modalSintoma").modal("show");
					//var sintomaN = [];
					//sintomaN.push()
				});
				}

}

function fn_rangoEdades(edad){
	var numToString= edad.toString();
  var str = pad(numToString, 8);
  return "D. "+str.substring(4,6)+" años "+str.substring(6,8)+" meses "+" H. "+str.substring(0,2) + " años "+str.substring(2,4) + " meses ";
}

function SeleccionarSintoma2() {
	/*$("#tableSintomaAddDiagnostico tbody tr ").click(function(e){
		console.log($(this).text());
	});*/

	$("#tableSintomaAddDiagnostico tbody").off("click").on("click", "tr td", function(e){
		console.log($(this).text().length);
		$("#tempIdSintoma").val($(this).parent().children("td").eq(0).text());
		$("#tempSintoma").val($(this).parent().children("td").eq(1).text());
		if($(this).text().length > 0){
			console.log($(this).parent().children("td").eq(0).text());
			//console.log($(this).eq(0).text());
			 $("#tempIdSintoma").val($(this).parent().children("td").eq(0).text());
			 $("#tempSintoma").val($(this).parent().children("td").eq(1).text());
			 var Encontrado = 0;

 					$("#tableSintomas tbody").each(function(index, el) {

 										$("#tableSintomas tbody tr").each(function(index, el) {
 										var sintoma = $(this).find('.nombreSintomaAdd').html();
 							if (sintoma == $("#tempSintoma").val()) {
 						 Encontrado = 1;
 							}
 					});

 				//console.log(Encontrado);

 				if (Encontrado ==0) {
 					var fila = "<tr><td>"+ $("#tempIdSintoma").val() +"</td><td class='nombreSintomaAdd'>"+ $("#tempSintoma").val() +"</td><td class='text-center'><a id='btnEliminarSintoma' class='btn' onclick='EliminarSintomaXDiagnostico("+$("#tempIdSintoma").val()+")'><i class='fa fa-trash'></i></a></td></tr>";
 				$("#tableSintomas tbody").append(fila);

 				}
 	});
}else {
	//$("#tempIdSintoma").val("");
}
		e.stopPropagation();
	});
}

function SeleccionarSintoma(){
	$("#tableSintomaAddDiagnostico tbody").on("click", "tr", function(e) {
		$("#tempIdSintoma").val($(this).children("td").eq(0).html());
		$("#tempSintoma").val($(this).children("td").eq(1).html());


			var Encontrado = 0;

					$("#tableSintomas tbody").each(function(index, el) {
										$("#tableSintomas tbody tr").each(function(index, el) {
										var sintoma = $(this).find('.nombreSintomaAdd').html();
							if (sintoma == $("#tempSintoma").val()) {
						 Encontrado = 1;
							}
					});

				//console.log(Encontrado);

				if (Encontrado ==0) {
					var fila = "<tr><td>"+ $("#tempIdSintoma").val() +"</td><td class='nombreSintomaAdd'>"+ $("#tempSintoma").val() +"</td><td class='text-center'><a id='btnEliminarSintoma' class='btn' onclick='EliminarSintomaXDiagnostico("+$("#tempIdSintoma").val()+")'><i class='fa fa-trash'></i></a></td></tr>";
				$("#tableSintomas tbody").append(fila);

				}
	});

});
}

</script>
<body>
	<?php include("header.php"); ?>
	<div class="panelExperto">
		<div class="form-inline">
			<label>AÑOS: </label>
			<input type="text" id="txtEdadA" class="form-control" value="18">
			<label>MESES :</label>
			<input type="text" id="txtEdadM" class="form-control" value="00">
		</div>
		<div>
			<div class="input-group">
      		<input type="text" class="form-control" placeholder="Seleccionar Sintoma">
      		<span class="input-group-btn">
        		<button class="btn btn-primary" id="btnSintoma" type="button">ADD</button>
      		</span>
    		</div>
		</div>
		<div class="panel panel-default" style="overflow-y:auto;">
  		<div class="panel-heading">SINTOMAS SELECCIONADOS</div>
  			<table id="tableSintoma" class="table table-striped table-bordered">
    			<thead>
    				<th>#</th>
    				<th>Sintoma</th>
    			</thead>
    			<tbody>
    			</tbody>
  			</table>
		</div>
		<input type="hidden" id="tempIdSintoma">
		<input type="hidden" id="tempSintoma">
		<input type="hidden" id="tempEdadSintoma">
		<div class="">
			<div class="panel panel-info">
  			<div class="panel-heading">DIAGNOSTICOS
  				<button type="button" id="btnAddDiagnostico" class="btn btn-danger pull-right"><i class="fa fa-plus"></i></button>
  			</div>
  			<table id="tableDiagnostico" class="table table-striped table-bordered">
    			<thead>
    				<th>#</th>
    				<th>Diagnostico</th>
    				<th>Problema</th>
    				<th>Observaciones</th>
    				<th>Edad</th>
    			</thead>
    			<tbody>
    			</tbody>
  			</table>
		</div>
		</div>
	</div>


<!-- Anadir Diagnostico -->
<div class="modal fade" id="modalAddDiagnostico" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Seleccionar Diagnostico</h1>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-bordered" id="tableDiagnosticoAdd">
					<thead>
						<th>ID</th>
						<th>Diagnostico</th>
						<th>Problema</th>
						<th>Observacion</th>
						<th>Edad</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnDiagnosticoAdd" class="btn btn-danger"><i class="fa fa-plus"></i></button>
			</div>
		</div>
	</div>
</div>

<!-- Anadir Diagnostico Add -->
<div class="modal fade" id="modalDiagnostico" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>REGISTRO DE DIAGNOSTICO</h1>
			</div>
			<div class="modal-body">
					<input type="hidden" id="txtTempId">
					<input type="hidden" id="txtEdadDiagnostico" value="" class="form-control">
					<h3>MINIMO</h3>
					<div class="form-input">
						<label>EDAD</label>
						<input type="number" required id="txtEdadAnnoMin" class="form-control">
					</div>
					<div class="form-input">
						<label>MESES</label>
						<input type="number" required id="txtEdadMesMin" class="form-control">
					</div>
			</div>
			<div class="modal-body">
					<h3>MAXIMO</h3>
					<div class="form-input">
						<label>EDAD</label>
						<input type="number" required id="txtEdadAnnoMax" class="form-control">
					</div>
					<div class="form-input">
						<label>MESES</label>
						<input type="number" required id="txtEdadMesMax" class="form-control">
					</div>
			</div>
			<div class="modal-body">


				<div class="form-input">
					<label>DIAGNOSTICO</label>
					<input type="text" required id="txtDiagnostico" class="form-control">
				</div>
				<div class="form-input">
					<label>PROBLEMA</label>
					<input type="text" id="txtProblema" class="form-control">
				</div>
				<div class="form-input">
					<label>OBSERVACION</label>
					<textarea class="form-control" id="txtObsDiag" rows="4" cols="50"></textarea>
				</div>
				<hr>
				<div class="panel panel-warning">
  						<div class="panel-heading">TRATAMIENTO
  					<button type="button" id="btnAddTratamiento" class="btn btn-danger pull-right"><i class="fa fa-plus"></i></button>
  					</div>
  					<table id="tableTratamiento" class="table table-striped table-bordered">
    				<thead>
    					<th>#</th>
    					<th>TRATAMIENTO</th>
    				</thead>
    				<tbody>
    				</tbody>
  					</table>
				</div>
				<hr>
				<div class="panel panel-success">
  						<div class="panel-heading">SINTOMAS
  					<button type="button" id="btnAddSintomas" class="btn btn-danger pull-right"><i class="fa fa-plus"></i></button>
  					</div>
  					<table id="tableSintomas"class="table table-striped table-bordered">
    				<thead>
    					<th>#</th>
    					<th>SINTOMAS</th>

    				</thead>
    				<tbody>
    				</tbody>
  					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-danger">Cerrar</button>
				<button type="button" id="btnDiagnosticoGuardar" class="btn btn-success">Guardar</button>
			</div>
		</div>
	</div>
</div>

<!-- Anadir Tratamiento -->
<div class="modal fade" id="modalAddTratamiento" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Seleccionar Tratamiento</h1>
			</div>
			<div class="modal-body">
			 <div>
			 	<div class="form-inline">
			 		<label>Id Tratamiento</label>
			 		<input type="text" readonly id="txtIdTratamiento" value="0" class="form-control">
			 	</div>
				<div class="input-group">
      				<input type="text" id="txtCompuesto" class="form-control" placeholder="Seleccionar Compuesto">
      				<span class="input-group-btn">
        			<button class="btn btn-primary" id="btnCompuesto" type="button">ADD</button>
      				</span>
    			</div>
				<div class="form-inline">
					<label>Dosis por dia</label>
					<input type="number" id="txtTomasDia" class="form-control">
				</div>
				<div class="form-inline">
					<label>Nro de dias</label>
					<input type="number" id="txtNroDia" class="form-control">
				</div>
				<div class="form-inline">
					<label>Dosis por peso</label>
					<input type="number" id="txtDosisXPeso" class="form-control">
					<label>  Unidad</label>
					<input type="text" id="txtUnidadDosisXPeso" class="form-control">
				</div>
				<div class="form-inline">
					<label>Cantidad de solucion</label>
					<input type="number" id="txtCantSol" class="form-control">
				</div>
				<div class="form-group">
					<label>Observaciones</label>
					<textarea class="form-control" id="txtObs" rows="4" cols="50"></textarea>
				</div>
			 </div>
			</div>
			<div class="modal-footer">
				<button type="button"  data-dismiss="modal" class="btn btn-danger">Cancelar</button>
				<button type="button" id="btnTratamientoAdd" class="btn btn-success">Guardar</button>
			</div>
		</div>
	</div>
</div>


<!-- Anadir Sintoma -->
<div class="modal fade" id="modalAddSintoma" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Seleccionar Sintomas</h1>
			</div>
			<div class="modal-body">
				<table id="tableSintomaAdd" class="table table-striped table-bordered">
					<thead>
						<th>#</th>
						<th>Sintomas</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-danger" >Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Anadir Sintoma Diagnostico -->
<div class="modal fade" id="modalAddSintomaDiagnostico" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Seleccionar Sintomas</h1>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-bordered" id="tableSintomaAddDiagnostico">
					<thead>
						<th>ID</th>
						<th>Sintomas</th>
						<th>Editar</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-danger" >Cerrar</button>
				<button type="button" id="btnAddSintoma" class="btn btn-success" >Nuevo</button>
			</div>
		</div>
	</div>
</div>
<!-- Anadir Sintoma -->
<div class="modal fade" id="modalSintoma" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Añadir Sintomas</h1>
			</div>
			<div class="modal-body">
				<div class="form-inline">
					<input type="hidden" id="txtIdSintoma" name="" value="">
					<label>SINTOMA</label>
					<input type="text" id="SintomaN" class="form-control">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
				<button type="button" id="btnSintomaSave" class="btn btn-success" >Guardar</button>
			</div>
		</div>
	</div>
</div>

<!-- Anadir Compuesto -->
<div class="modal fade" id="modalCompuestoAdd" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Seleccionar Compuesto</h1>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-bordered" id="tableCompuesto">
					<thead>
						<th>ID</th>
						<th>COMPUESTO</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
				<button type="button" id="btnCompuestoAdd" class="btn btn-sucess" >Nuevo</button>
			</div>
		</div>
	</div>
</div>

<!-- Anadir Compuesto -->
<div class="modal fade" id="modalCompuestoSave" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Nuevo Compuesto</h1>
			</div>
			<div class="modal-body">
				<div class="form-inline">
					<label>COMPUESTO : </label>
					<input type="text" id="txtCompuestoS" class="form-control">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				<button type="button" id="btnCompuestoSave" class="btn btn-sucess" >Guardar</button>
			</div>
		</div>
	</div>
</div>

<!-- Anadir Compuesto -->
<div class="modal fade" id="modalTratamientoPreO" role="dialog">
	<div class="modal-dialog" style="width:1200px;">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Tratamiento</h1>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>DIAGNOSTICO </label>
					<input type="text" readonly id="txtDiagnosticoPreO" class="form-control">
				</div>
				<div class="form-group">
					<label>OBSERVACIONES: </label>
					<textarea readonly id="txtObservacionPreO" class="form-control"></textarea>
				</div>
				<div class="panel panel-success">
  						<div class="panel-heading">SINTOMAS
  					<button type="button" id="btnAddSintomas" class="btn btn-danger pull-right"><i class="fa fa-plus"></i></button>
  					</div>
  					<table id="tableSintomas" class="table table-striped table-bordered">
    				<thead>
    					<th>#</th>
    					<th>SINTOMAS</th>
    				</thead>
    				<tbody>
    				</tbody>
  					</table>
				</div>
					<div class="form-inline">
						<label >NroDias :   </label>
						<label>MAX</label>
						<input type="number" id="txtMaxPreO" readonly value="0" class="form-control">
						<label>NroSelec</label>
						<input type="number" min="1.00" id="txtNroSelPreO" class="form-control">
					</div>
					<div class="form-group">
						<label>EDAD</label>
						<input type="text" id="txtEdadPreO" readonly class="form-control">
					</div>
				<div class="panel panel-success" >
					<div class="panel-heading">Tratamiento</div>
					<table id="tableTratamientoPreO"  class="table table-striped table-bordered">
						<thead>
							<th>Comp.</th>
							<th>Cant.</th>
							<th>Prod.</th>
							<th>Precio</th>
							<th>DosisXPeso</th>
							<th>Concent.</th>
							<th>Peso</th>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnCompuestoSave" class="btn btn-primary" >Pre Orden</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalProductosAdd" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Productos </h4>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="tempCompuesto">
        <table id="tableProductosAdd" class="table table-striped table-bordered">
          <thead>
            <th>#</th>
            <th>Productos</th>
            <th>Precio</th>
            <th>Precio por mayor</th>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
