<?php  ?>

<html>
<head>
	<title>Sistema Experto</title>
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
			$("#modalDiagnostico").modal("show");
		});

		$("#btnAddTratamiento").click(function(){
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
				data: {"Sintoma" : $("#SintomaN").val(), "Edad" : $("#SintomaEdadN").val() },
				dataType: "html",
				success: function(respuesta){
					alert("Sintoma agregado");
					$("#modalSintoma").modal("hide");
					ListarSintoma("diagnostico");

				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
        		alert("Status: " + textStatus); alert("Error: " + errorThrown);
    			}
			});
			console.log(xhr);
		});

		$("#btnTratamientoAdd").click(function(){
			var arr = [];
			arr.push(parseInt($("#txtIdTratamiento").val()), $("#txtDiagnostico").val(), $("#txtCompuesto").val(), parseInt($("#txtEdadT").val()),
				$("#txtObs").val(), parseFloat($("#txtTomasDia").val()), parseInt($("#txtNroDia").val()));
			console.log(JSON.stringify(arr));
			var xhr = $.ajax({
				url: "v_expertotratamientoguardar.php",
				type: "post",
				data: {data : JSON.stringify(arr)},
				dataType: "html",
				success: function(respuesta){
					var response = JSON.parse(respuesta);
					$("#tableTratamiento").append("<tr><td>"+response.IdTratamiento+"</td><td>"+response.Tratamiento+"</td></tr>");
					$("#modalAddTratamiento").modal("hide");
					$("#txtCompuesto").val("");
					$("#txtEdadT").val("");
					$("#txtObs").val("");
					$("#txtTomasDia").val("");
					$("#txtNroDia").val("");
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
        		alert("Status: " + textStatus); alert("Error: " + errorThrown);
    			}
			});
			console.log(xhr);
		});

	$("#btnDiagnosticoGuardar").click(function(){
			var arr = [];
			arr.push($("#txtDiagnostico").val(), $("#txtProblema").val(), $("#txtEdadDiagnostico").val(), $("#txtObsDiag").val());
			var arrTableTratamiento = [];
			$("#tableTratamiento tbody").each(function(){
				$("#tableTratamiento tbody tr").each(function(){
					arrTableTratamiento.push([parseInt($(this).children("td").eq(0).html()), $(this).children("td").eq(1).html()]);
				});
			});
			var arrTableSintoma = [];
			$("#tableSintomas tbody").each(function(){
				$("#tableSintomas tbody tr").each(function(){
					arrTableSintoma.push([parseInt($(this).children("td").eq(0).html()), $(this).children("td").eq(1).html(), $(this).children("td").eq(2).html()]);
				});
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
		$("#txtEdadPreO").val($("#txtEdad").val());
		$("#tableTratamientoPreO tbody").empty();
		var xhr = $.ajax({
			url: "../controllers/server_processingDiagnosticoXTratamiento.php",
			type: "get",
			data: {"diagnostico":$("#txtDiagnosticoPreO").val(), "edad":$("#txtEdadPreO").val()},
			dataType: "html",
			success: function(respuesta){
				var response = JSON.parse(respuesta);
				//var arrNroDias = [];
				//console.log(response);
				$.each(response, function(data, value){
					var fila = "<tr><td class='nombreCompuesto'>" + value.ProductoCompuesto + "</td><td>" + value.TomasXDia*value.NroDias + "</td><td></td><td></td><td style='display:none;'>"+value.NroDias+"</td><td style='display:none;'>"+value.TomasXDia+"</td><td style='display:none;'>"+value.NroDias+"</td></tr>";
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
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
        		alert("Status: " + textStatus); alert("Error: " + errorThrown);
    		}
		});

		console.log(xhr);
	});

	$("#tableTratamientoPreO tbody").on("click", "tr", function(e){
		var compuesto = $(this).children("td").eq(0).html();
		$("#tempCompuesto").val(compuesto);
		console.log(compuesto);
		ListarProductos(compuesto);
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
				$(this).children("td").eq(4).html($("#txtNroSelPreO").val());
				$(this).children("td").eq(1).html(parseInt($(this).children("td").eq(4).html()) * parseInt($(this).children("td").eq(5).html()));
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
      			{ mData: 'Edad' }
      			]
    		});
}

function nroDiasOriginal(){
			$("#tableTratamientoPreO tbody").each(function(e){
			$("#tableTratamientoPreO tbody tr").each(function(e){
				$(this).children("td").eq(4).html($(this).children("td").eq(6).html());
				$(this).children("td").eq(1).html(parseInt($(this).children("td").eq(4).html()) * parseInt($(this).children("td").eq(5).html()));
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
	var criterioBusqueda = "";
	var cont = 1;
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
		var criterioFinal = '"'+criterioBusqueda.substr(0,criterioBusqueda.length-1)+'"';
	}
	console.log(criterioFinal);
	var arr = [];
	arr.push(parseInt($("#txtEdad").val()), criterioFinal);
	$("#tableDiagnostico tbody").empty();
  var xhr = $.ajax({
    url: 'v_expertosintomabuscard.php',
    type: 'post',
    data: {data : JSON.stringify(arr)},
    dataType: 'html',
    success: function(respuesta){
      $.each(JSON.parse(respuesta), function(key, value){
            console.log(key + ":" + value + "--" + value.Diagnostico)
            $("#tableDiagnostico tbody").append("<tr><td>"+ value.IdDiagnostico+"</td><td>"+value.Diagnostico+"</td><td>"+value.Problema+"</td><td>"+value.Observacion+"</td><td>"+value.Edad+"</td></tr>");
        })

    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    }
      });
  console.log(xhr);
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

function ListarSintoma(env){

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
      					"edad" : parseInt($("#txtEdad").val())
      				}
      			},
      			"aoColumns": [
      			{ mData: 'IdSintoma' } ,
      			{ mData: 'Sintoma' },
      			{ mData: 'Edad' }
      			]
    		});
			$("#tableSintomaAdd tbody").off("click").on("click", "tr", function(e) {

				$("#tempIdSintoma").val($(this).children("td").eq(0).html());
				$("#tempSintoma").val($(this).children("td").eq(1).html());
				$("#tempEdadSintoma").val($(this).children("td").eq(2).html());

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
		          var fila = "<tr><td>"+ $("#tempIdSintoma").val() +"</td><td class='nombreSintoma'>"+ $("#tempSintoma").val() +"</td><td>"+ $("#tempEdadSintoma").val() +"</td><td class='text-center'><a id='btnEliminarSintoma' class='btn' onclick='EliminarSintoma("+$("#tempIdSintoma").val()+")'><i class='fa fa-trash'></i></a></td></tr>";
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
      			{ mData: 'Edad' }
      			]
    		});
			    $("#tableSintomaAddDiagnostico tbody").on("click", "tr", function(e) {

				$("#tempIdSintoma").val($(this).children("td").eq(0).html());
				$("#tempSintoma").val($(this).children("td").eq(1).html());
				$("#tempEdadSintoma").val($(this).children("td").eq(2).html());

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
		          var fila = "<tr><td>"+ $("#tempIdSintoma").val() +"</td><td class='nombreSintomaAdd'>"+ $("#tempSintoma").val() +"</td><td>"+ $("#tempEdadSintoma").val() +"</td></tr>";
		        $("#tableSintomas tbody").append(fila);

		        }
			});
			});

				$("#btnAddSintoma").click(function(){
					$("#modalSintoma").modal("show");
					//var sintomaN = [];
					//sintomaN.push()
				});
				}

}


</script>
<body>
	<?php include("header.php"); ?>
	<div class="panelExperto">
		<div class="form-inline">
			<label>EDAD:</label>
			<input type="text" id="txtEdad" class="form-control" value="18">
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
    				<th>Edad</th>
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
				<h1>Seleccionar Diagnostico</h1>
			</div>
			<div class="form-input">
					<label>EDAD</label>
					<input type="number" id="txtEdadDiagnostico" value="" class="form-control">
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
    					<th>EDAD</th>
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
					<label>Edad</label>
					<input type="number" id="txtEdadT" class="form-control">
				</div>
				<div class="form-inline">
					<label>Tomas por dias</label>
					<input type="number" id="txtTomasDia" class="form-control">
				</div>
				<div class="form-inline">
					<label>Nro de dias</label>
					<input type="number" id="txtNroDia" class="form-control">
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
						<th>Edad</th>
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
						<th>Edad</th>
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
					<label>EDAD</label>
					<input type="text" id="SintomaEdadN" class="form-control">
				</div>
				<div class="form-inline">
					<label>SINTOMA</label>
					<input type="text" id="SintomaN" class="form-control">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
				<button type="button" id="btnSintomaSave" class="btn btn-sucess" >Nuevo</button>
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
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Tratamiento</h1>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>DIAGNOSTICO </label>
					<input type="text" readonly id="txtDiagnosticoPreO" class="form-control">
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
						<input type="number" id="txtEdadPreO" readonly class="form-control">
					</div>
				<div class="panel panel-success">
					<div class="panel-heading">Tratamiento</div>
					<table id="tableTratamientoPreO" class="table table-striped table-bordered">
						<thead>
							<th>Compuesto</th>
							<th>Cantidad</th>
							<th>Producto</th>
							<th>Precio</th>
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
