
<html>
<head>
	<title>Buscar Productos vencidos</title>
</head>
<?php include_once 'linker.php'; ?>

<script type="text/javascript">

$(document).ready(function(e){
	$("#btnGenerar").click(function(e){
		cargarProductos();
	});

	$('#btnGuardarNuevaFecha').click(function(e) {
		actualizarVencimiento()
	})


});

function cargarProductos() {
	$("#tableProducto").DataTable().destroy();
		var table4 = $("#tableProducto").DataTable({
		"bProcessing": true,
		"bPaginate":true,
		"sPaginationType":"full_numbers",
		"iDisplayLength": 50,
		//"sAjaxSource": "../controllers/server_processingProductosVencimiento.php",
		"ajax":{
			"url": "../controllers/server_processingProductosVencimiento.php",
			"type": "get",
			"data": {
			"FechaIni" : $('#fechaIni').val(),
			"FechaFin" : $('#fechaFinal').val()
			}
		},
		"aoColumns": [
			{ mData: 'hashMovimiento'} ,
			{ mData: 'Serie' },
			{ mData: 'Numero' },
			{ mData: 'Producto' } ,
			{ mData: 'IdLote' },
			{ mData: 'FechaVen' }
		],
		/*rowCallback: function( row, data, index ) {
			var dt = new Date()
			var hoy = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate()

			if(data.FechaVen < hoy) {
			$(row).addClass('danger')
			}
		},*/
		"rowCallback" : function( row, data, index) {
			$(row).attr('data-fechaven', data.FechaVen)

			$(row).on('click', function(){
				$('#nombreProducto').text(data.Producto)
				$('#hashMovimiento').val(data.hashMovimiento)
				$('#idProducto').val(data.IdProducto)
				$('#newFechaVen').val($(row).attr('data-fechaven'))
				$("#modalProductos").modal("show");
			})
		},
		"initComplete": function( settings, json ) {

		}
	});
}


function actualizarVencimiento() {
	var xhr =  $.ajax({
	url: '../controllers/server_processingProductosVencimiento.php',
	type: 'post',
	data:  {
		"update" : true, 
		"hashMovimiento": $("#hashMovimiento").val(), 
		"idProducto": $("#idProducto").val(),
		"fechaVen": $("#newFechaVen").val()
	},
	dataType: 'html',
	success : function(res){
		var res = JSON.parse(res)
		
		if (res.success) {
			alert(res.success)
		}

		$("#modalProductos").modal("hide");
		cargarProductos()
	},
	error: function(XMLHttpRequest, textStatus, errorThrown) {
		alert("Status: " + textStatus); alert("Error: " + errorThrown);
	}
	});
}

</script>

<body>
<?php include("header.php"); ?>


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
			<br>
			<div class="pull-left">
				<button type="button" id="btnGenerar" class="btn btn-success">Buscar</button>
			</div>
		</div>
	</div>
	
	<br>
	<hr>
	<div class="panel panel-success">
		<table id="tableProducto" class="table table-striped table-bordered">
			<thead>
				<th>hashMovimiento</th>
				<th>Serie</th>
				<th>Numero</th>
         		<th>Producto</th>
         		<th>Lote</th>
         		<th>Fecha Vencimiento</th>
			</thead>
			<tbody></tbody>
		</table>
	</div>

</div>

<?php include("footer.php"); ?>
</body>

<div class="modal fade" id="modalProductos" role="dialog">
	<div class="modal-dialog" style="width:700px">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">Cambiar Fecha de Vencimiento</div>
			</div>
			<div class="modal-body" style="overflow-x:auto;">
				<div class="row">
				<input type="hidden" id="hashMovimiento" />
				<input type="hidden" id="idProducto" />
					<div class="col-md-12">
						<p id="nombreProducto"></p>
					</div>
					<div class="col-md-4 form-group">
						<label>Fecha de vencimiento</label>
						<input type="text" id="newFechaVen" class="form-control">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" id="btnGuardarNuevaFecha">Guardar</button>
				<button class="btn btn-default" data-dismiss="modal" >Cerrar</button>
			</div>
		</div>
	</div>
</div>
</html>
