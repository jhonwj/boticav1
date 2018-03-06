<html>
<head>
	<title>Seguridad | Perfil</title>
</head>
<?php include 'linker.php'; ?>
<script type="text/javascript">

$(document).ready(function(){

	ListarUsuarioPerfil();

	$("#btnUsuario").click(function(){
		ListarUsuario();
		$("#modalUsuario").modal("show");
	});

	$("#btnNuevoUsuario").click(function(){
		$("#modalNuevoUsuario").modal("show");
	});

	$("#btnVerPass").click(function(e){
		$("#Password").prop("type", "text");
	});

	$("#btnPerfil").click(function(){
		$("#modalPerfil").modal("show");
		ListarPerfil();
	});

	$("#btnNuevoPerfil").click(function(){
		$("#modalNuevoPerfil").modal("show");
	});

	$("#btnNuevoPerfilModulo").click(function(){
		$("#modalPerfil2").modal("show");
	});

	$("#btnNuevoPerfil2").click(function(){
		$("#modalPerfilNuevo").modal("show");
	});

});

function ListarUsuario(){
	$("#tableUsuario").DataTable().destroy();
	$("#tableUsuario").DataTable({
			"bProcessing": true,
            "sAjaxSource": "../controllers/server_processingUsuario.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'Usuario' } ,
						{ mData: 'NombreUsuario' },
						{ mData: 'FechaReg' },
            { mData: 'Anulado' }
            ]
	});
}

function ListarUsuarioPerfil(){
	$("#tableUsuarioPerfil").DataTable().destroy();
	$("#tableUsuarioPerfil").DataTable({
			"bProcessing": true,
            "sAjaxSource": "../controllers/server_processingUsuarioPerfil.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'Usuario' } ,
            { mData: 'UsuarioPerfil' },
						{ mRender : function(data, type, row){
              return "<a onclick='EditarBloque("+ row.UsuarioPerfil +");' class='btn'><i class='fa fa-pencil'></i></a>"
            }}
            ]
	});
}

function ListarPerfil(){
	$("#tablePerfil").DataTable().destroy();
	$("#tablePerfil").DataTable({
			"bProcessing": true,
            "sAjaxSource": "../controllers/server_processingPerfil.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'UsuarioPerfil' } ,
						{ mData: 'FechaReg' },
            { mData: 'Anulado' },
						{ mRender : function(data, type, row){
              return "<a onclick='EditarBloque("+ row.UsuarioPerfil +");' class='btn'><i class='fa fa-pencil'></i></a>"
            }}
            ]
	});
}
</script>
<body>
<?php include 'header.php'; ?>
<div class="container">
 <button id="btn-nuevo" class="btn btn-danger fab" data-toggle="modal" data-target="#nuevo"><i class="fa fa-plus"></i></button>
	<div class="row">
		<div class="col s12">
				<table id="tableUsuarioPerfil" class="table table-bordered table-striped">
					<thead>
						<th>Usuario</th>
						<th>Perfil</th>
						<th>Acciones</th>
					</thead>
				</table>
		</div>
	</div>
</div>
<?php //include "footer.php"; ?>
</body>

<div class="modal fade" id="nuevo" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Agregar Usuario / Perfil</div>
			<div class="modal-body">
				<div class="row">
					<div class="col s12 form-group">
						<label for="txtUsuario"></label>
						<div class="form-inline">
							<input type="text" id="txtUsuario" placeholder="Usuario" class="form-control">
							<button type="button" id="btnUsuario" class="btn btn-success"><i class="fa fa-search-plus"></i></button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col s12 form-group">
						<label for="txtPerfil"></label>
						<div class="form-inline">
							<input type="text" id="txtPerfil" placeholder="Perfil" class="form-control">
							<button type="button" id="btnPerfil" class="btn btn-success"><i class="fa fa-search-plus"></i></button>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalUsuario" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Agregar Usuario</div>
			<div class="modal-body" style="overflow-x:auto;">
				<table id="tableUsuario" class="table table-bordered table-striped">
					<thead>
						<th>Usuario</th>
						<th>NombreUsuario</th>
						<th>FechaReg</th>
						<th>Anulado</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnNuevoUsuario" class="btn btn-success"><i class="fa fa-save"></i> Nuevo</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalNuevoUsuario" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Agregar Usuario</div>
			<div class="modal-body">
				<form class="form">
					<div class="form-group">
						<label for="Usuario">Usuario</label>
						<input type="text" name="usuario" class="form-control">
					</div>
					<div class="form-group">
						<label for="Password">Password</label>
						<div class="form-inline">
							<input type="password" name="usuario" id="Password" class="form-control">
							<a class="btn btn-info" id="btnVerPass"><i class="fa fa-eye"></i></a>
						</div>
					</div>
					<div class="form-group">
						<label for="Usuario">NombreUsuario</label>
						<input type="text" name="usuario" id="Usuario" class="form-control">
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalPerfil" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Agregar Perfil</div>
			<div class="modal-body">
				<table id="tablePerfil" class="table table-bordered table-striped">
					<thead>
						<th>Perfil</th>
						<th>FechaReg</th>
						<th>Anulado</th>
						<th>Acciones</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnNuevoPerfil" class="btn btn-success"><i class="fa fa-save"></i> Nuevo</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalNuevoPerfil" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Agregar Perfil / Modulo</div>
			<div class="modal-body">
				<div class="row">
					<div class="col s12 form-group">
						<label for="NuevoPerfil">Perfil</label>
						<div class="form-inline">
							<input type="text" class="form-control" readonly>
							<button type="button" class="btn btn-success" id="btnNuevoPerfilModulo"><i class="fa fa-search-plus"></i></button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="panel panel-success">
						<div class="panel-heading">Modulo</div>
						<div class="panel-body">
							<table class="table table-bordered">
								<thead>
									<th>Modulo</th>
									<th>Lectura</th>
									<th>Escritura</th>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnGuardarPerfilModulo" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalPerfil2" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Agregar Perfil</div>
			<div class="modal-body">
				<table id="" class="table table-bordered table-striped">
					<thead>
						<th>Perfil</th>
						<th>FechaReg</th>
						<th>UsuarioReg</th>
						<th>FechaMod</th>
						<th>UsuarioMod</th>
						<th>Estado</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnNuevoPerfil2" class="btn btn-success"><i class="fa fa-save"></i> Nuevo</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalPerfilNuevo" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Agregar Perfil</div>
			<div class="modal-body">
				<form class="form">
					<input type="text" name="perfil" class="form-control">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnNuevoPerfilGuardar" class="btn btn-success"><i class="fa fa-save"></i> Nuevo</button>
			</div>
		</div>
	</div>
</div>

</html>
