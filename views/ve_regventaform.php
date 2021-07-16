
<html>
<head>
	<title>Inventario</title>
</head>
<?php include_once 'linker.php'; ?>

<script type="text/javascript">

$(document).ready(function(e){
	$("#btnGenerar").click(function(e){
		$("#tableRegVenta tbody").empty();
		var xhr = $.ajax({
			url: "../controllers/listarRegVenta.php",
			type: "get",
			data: {
				fechaIni : $("#fechaIni").val() + ' ' + $("#horaIni").val(), 
      			fechaFin: $("#fechaFinal").val() + ' ' + $("#horaFinal").val(),    
				declarado: $("#declarado").prop("checked"),
				almacen: $("#almacen").val(),
			},
			dataType: "html",
			success: function(res){
				var respuesta = JSON.parse(res);
				var tableBody = "";
				var TotalGeneral = 0.00;
				
				$.each(respuesta, function(data, value){
					var style='';
					if(value.Anulado == '1') {
						style='danger';
					}
					if (value.CodSunat == '07') {
							value.SubTotal =  (-1 * value.SubTotal).toFixed(2);
							value.Total = (-1 * value.Total).toFixed(2);
							if (value.Anulado == '0') {
								TotalGeneral = TotalGeneral + parseFloat(value.Total);
							}
						} else {
							if (value.Anulado == '0') {
								TotalGeneral = TotalGeneral + parseFloat(value.Total);
							}
					}
					tableBody = tableBody + "<tr class='" + style + "'><td>"+value.idDocVenta+"</td><td>"+value.UsuarioReg+"</td><td>"+value.FechaDoc+"</td><td>"+value.CodSunat+"</td><td>"+value.TipoDoc+"</td><td>"+value.Anulado+"</td><td>"+value.Serie+"</td><td>"+value.Numero+"</td><td>"+value.SubTotal+"</td><td>"+value.Igv+"</td><td>"+value.Total+"</td><td><a class='btn' onclick='EliminarRegVenta("+ value.idDocVenta +",\""+value.FechaDoc+"\");'><i class='fa fa-ban'></i></a></td></tr>" ;
				});
				$("#tableRegVenta tbody").append(tableBody);
				$("#txtTotal").val(TotalGeneral);
			},
			error: function(err){
				alert(err);
			}
		});
		console.log(xhr);
	});

	$("#tableRegVenta tbody").on("click", "tr", function(e){
		$("#tableProducto tbody tr").remove();
	var iddoceventa = $(this).children("td").eq(0).html();
   var xhr = $.ajax({
    url: '../controllers/serverprocessingProductosRegVenta.php',
    type: 'get',
    data:  {"idDocVenta" : iddoceventa},
    dataType: 'html',
    success: function(respuesta){
        var response = JSON.parse(respuesta);
        var fila = "";
        $.each(response, function(data, value){
        	fila = "<tr><td>"+value.IdDocVenta+
					"</td><td>"+value.CodigoBarra+
					// "</td><td>"+value.ProductoFormaFarmaceutica+
					"</td><td>"+value.ProductoMarca+
					"</td><td>"+value.Producto+
					"</td><td>"+value.ProductoMedicion+
					"</td><td>"+value.Cantidad+
					"</td><td>"+value.Precio+
					"</td><td>"+value.Descuento+
					"</td><td>"+((value.Precio*value.Cantidad) - value.Descuento)+
					"</td></tr>";
        	$("#tableProducto tbody").append(fila);
        });
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    }
      });
  console.log(xhr);
		$("#modalProductos").modal("show");
	});

	$("#btnExcel").click(function(){
		//window.location.href="reporteExcel5.php?fechaIni="+$("#fechaIni").val()+"&fechaFin="+$("#fechaFinal").val()+"&declarado="+$("#declarado").prop("checked");
		// window.open("/api/index.php/reporte/ventas?fechaInicio=" + $("#fechaIni").val() + "&fechaFin=" + $("#fechaFinal").val() + "&declarado=" + ($("#declarado").prop("checked") ? 1 : 0) + "&idAlmacen=" + $("#almacen").val());
		window.open("/api/index.php/reporte/ventas?fechaInicio=" + ($("#fechaIni").val() + ' ' + $("#horaIni").val()) + "&fechaFin=" + ($("#fechaFinal").val() + ' ' + $("#horaFinal").val()) + "&declarado=" + ($("#declarado").prop("checked") ? 1 : 0) + "&idAlmacen=" + $("#almacen").val());
	});


	// cargar almacenes
	$.ajax({
		url: "/api/index.php/almacenes",
		type: "get",
		dataType: "json",
		success: function(res){
			console.log(res);

			$.each(res, function( index, almacen ) {
				$( "#almacen" ).append( "<option value=" + almacen.IdAlmacen + ">" + almacen.Almacen + "</option>" );
			});
		},
		error: function(err){
			alert(err);
		}
		// window.open("/api/index.php/reporte/ventas?fechaInicio=" + $("#fechaIni").val() + "&fechaFin=" + $("#fechaFinal").val() + "&declarado=" + ($("#declarado").prop("checked") ? 1 : 0));
	});

	$("#btnExcel2").click(function(){
		// window.location.href="reporteExcel5.php?fechaIni="+$("#fechaIni").val()+"&fechaFin="+$("#fechaFinal").val()+"&declarado="+$("#declarado").prop("checked");
		window.open("/api/index.php/reporte/ventasproducto?fechaInicio=" + $("#fechaIni").val() + "&fechaFin=" + $("#fechaFinal").val() + "&declarado=" + ($("#declarado").prop("checked") ? 1 : 0));
	});

});

function EliminarRegVenta(docVenta,FechaDoc){
		let fecha1 = new Date(FechaDoc);
	    let fecha2 = new Date()
	    let resta = Math.round((fecha2.getTime() - fecha1.getTime())/ (1000*60*60*24))
		if (resta>5){
			alert('No es posible eliminar la venta pasado los 5 dias')
			return
		}
	var r = confirm("Estas seguro que desea anular la venta?");
	if (r == true) {
	  var xhr =  $.ajax({
	    url: '../controllers/EliminarRegVenta.php',
	    type: 'get',
	    data:  {"idRegVenta" : docVenta},
	    dataType: 'html',
	    success : function(res){
	      $("#modalProductos").modal("hide");
	      $("#btnGenerar").trigger("click");
	      alert(res);
	    },
	    error: function(XMLHttpRequest, textStatus, errorThrown) {
	        alert("Status: " + textStatus); alert("Error: " + errorThrown);
	    }
	  });
	}
}

</script>

<body>
<?php include("header.php"); ?>
<div class="fab2">
	<button id="" class="btn btn-success" onclick="exportarTXT($('#tableRegVenta'))"><i class="fa fa-file"></i>.txt</button>
	<button id="btnExcel" class="btn btn-success"><i class="fa fa-file-excel-o"></i>.xls</button>
	<!-- <button id="btnExcel2" class="btn btn-warning"><i class="fa fa-file-excel-o"></i>.xls Detalle</button> -->
</div>

<div class="bt-panel">
	<div class="container center_div" >
	<div class="row">
		<div class="col-md-6 form-group">
				<label>Fecha Inicio</label>
				</br>
				<div class="col-md-4 form-group">
				<input type="date" id="fechaIni" class="form-control">
				</div>
				<div class="col-md-3 form-group">
				<input type="time" id="horaIni" class="form-control" value="00:00">
				</div>
			</div>
		</div>
		<div class="row">
		<div class="col-md-6 form-group">
				<label>Fecha Final</label>
				</br>
				<div class="col-md-4 form-group">
				<input type="date" id="fechaFinal" class="form-control">
				</div>
				<div class="col-md-3 form-group">
				<input type="time" id="horaFinal" class="form-control" value="23:59">
				</div>

		</div>
				<div class="col-md-6 form-group">
				<div class="checkbox">
  					<label><input id="declarado" type="checkbox">Declarado</label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 form-group">
				<label>Almacen</label>
				<select id="almacen"  class="form-control">
					<option value="0" selected>TODOS</option>
				</select>
			</div>
		</div>
	</div>
	<div class="pull-right">
		<button type="button" id="btnGenerar" class="btn btn-success">Generar</button>
	</div>
	<br>
	<hr>
	<div class="panel panel-success">
		<div class="panel panel-heading">
			<div class="form-inline">
			<label class="">REGISTRO DE VENTA </label>
			<!-- <input type="text" class="form-control"> -->
			</div>
		</div>
		<table id="tableRegVenta" class="table table-bordered table-striped">
			<thead>
				<th># DocVenta</th>
				<th>Vendedor</th>
         		<th>FechaDoc</th>
         		<th>Codigo Sunat</th>
         		<th>TipoDoc</th>
						<th>Anulado</th>
				<th>Serie</th>
				<th>Numero</th>
				<th>Sub Total</th>
				<th>Igv</th>
				<th>Total</th>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<div class="container">

	<!-- <div class="row">
		<div class="col-md-5 form-inline pull-right">
			<label class="">Sub Total</label>
			<input type="text" class="form-control" id="txtSubTotal">
		</div>
	</div> -->
	<div class="row">
		<div class="col-md-5 form-inline pull-right">
			<label class="">Total</label>
			<input type="text" class="form-control" id="txtTotal">
		</div>
	</div>

</div>
</div>

<?php include("footer.php"); ?>
</body>

<div class="modal fade" id="modalProductos" role="dialog">
	<div class="modal-dialog" style="width:700px">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">Detalle</div>
			</div>
			<div class="modal-body" style="overflow-x:auto;">
				<table id="tableProducto" style="overflow-x:auto;" class="table table-bordered table-striped">
					<thead>
						<th>IdDocVenta</th>
						<th>Codigo Barra</th>
						<!--<th>Forma</th>-->
						<th>Marca</th>
						<th>Producto</th>
						<th>Medicion</th>
						<th>Cantidad</th>
						<th>Precio</th>
						<th>Descuento</th>
						<th>Total</th>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" data-dismiss="modal" >Cerrar</button>
			</div>
		</div>
	</div>
</div>
</html>
