
<html>
<head>
	<title>Buscar Productos vencidos</title>
    <?php include_once 'linker.php'; ?>
</head>
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
				<th>Codigo</th>
         		<th>Producto</th>
         		<th>Forma Farmaceutica</th>
         		<th>Laboratorio</th>
         		<th>Lote</th>
         		<th>Fecha Vencimiento</th>
				<th>hashMovimiento</th>
				<th>Serie</th>
				<th>Numero</th>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>


</body>
</html>
