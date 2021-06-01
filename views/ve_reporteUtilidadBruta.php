
<html>
<head>
	<title>Reporte Utilidad Bruta</title>
</head>
<?php include_once 'linker.php'; ?>

<script type="text/javascript">

$(document).ready(function(e){
	$("#btnGenerar").click(function(e){
		$("#tableUtilidadBruta tbody").empty();
		var xhr = $.ajax({
			url: "/api/index.php/reporte/utlidadbruta",
      data: {fechaInicio: $('#fechaIni').val(), fechaFin: $('#fechaFinal').val() },
			type: "GET",
			dataType: "json",
			success: function(res){
				var sumTotalVenta = 0;
        var sumTotalCosto = 0;
        var sumUtilidadBruta = 0;

        $(res).each(function(index, value) {
          var tr = '<tr>'
          tr += '<td>'+value.IdProducto+'</td>'
          tr += '<td>'+value.Producto+'</td>'
          tr += '<td>'+value.Cantidad+'</td>'
          tr += '<td>'+value.PrecioCosto+'</td>'
          tr += '<td>'+value.PrecioVenta+'</td>'
          tr += '<td>'+value.TotalVenta+'</td>'
          tr += '<td>'+value.TotalCosto+'</td>'
          tr += '<td>'+value.UtilidadBruta+'</td>'
          tr += '</tr>'
          $('#tableUtilidadBruta tbody').append(tr)

          sumTotalVenta += parseInt(value.TotalVenta)
          sumTotalCosto += parseInt(value.TotalCosto)
          sumUtilidadBruta += parseInt(value.UtilidadBruta)
        })

        $('#tableUtilidadBruta tfoot .sumTotalVenta').text(sumTotalVenta)
        $('#tableUtilidadBruta tfoot .sumTotalCosto').text(sumTotalCosto)
        $('#tableUtilidadBruta tfoot .sumUtilidadBruta').text(sumUtilidadBruta)
			},
			error: function(err){
				alert(err);
			}
		});
		console.log(xhr);
	});



	$("#btnExcel").click(function(){
		window.location.href="reporteExcel5.php?fechaIni="+$("#fechaIni").val()+"&fechaFin="+$("#fechaFinal").val()+"&declarado="+$("#declarado").prop("checked");
	});

});


</script>

<body>
<?php include("header.php"); ?>
<div class="fab2">
	<button id="btnExcel" class="btn btn-success"><i class="fa fa-file-pdf-o"></i> Exportar PDF</button>
</div>

<div class="bt-panel">
	<div class="container center_div" >
		<div class="row">
			<div class="col-md-4 form-group">
				<label>Fecha Inicio</label>
				<input type="date" id="fechaIni" class="form-control">
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 form-group">
				<label>Fecha Final</label>
				<input type="date" id="fechaFinal" class="form-control">
			</div>
		</div>
    <div class="pull-left">
      <button type="button" id="btnGenerar" class="btn btn-success">Generar Reporte</button>
    </div>
	</div>
	<br>
	<hr>
	<div class="panel panel-success">
		<div class="panel panel-heading">
			<div class="form-inline">
			<label class="">UTILIDAD BRUTA VENTAS</label>
			<!-- <input type="text" class="form-control"> -->
			</div>
		</div>
		<table id="tableUtilidadBruta" class="table table-bordered table-striped">
			<thead>
				<th>Id Producto</th>
     		<th>Producto</th>
     		<th>Cantidad</th>
     		<th>Precio Costo</th>
				<th>Precio Venta</th>
				<th>Total Venta</th>
				<th>Total Costo</th>
				<th>Utilidad Bruta</th>
			</thead>
      <tbody>

      </tbody>
      <tfoot>
        <tr>
          <td colspan="5"> Sumatoria
          </td>
          <td><strong class="sumTotalVenta"></strong></td>
          <td><strong class="sumTotalCosto"></strong></td>
          <td><strong class="sumUtilidadBruta"></strong></td>
        </tr>
      </tfoot>
		</table>
	</div>
	<div class="container">

	<!-- <div class="row">
		<div class="col-md-5 form-inline pull-right">
			<label class="">Sub Total</label>
			<input type="text" class="form-control" id="txtSubTotal">
		</div>
	</div> -->

</div>
</div>

<?php include("footer.php"); ?>
</body>

</html>
