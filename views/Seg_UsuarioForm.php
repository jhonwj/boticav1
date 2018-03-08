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
		$('#NuevoPerfil').val('');
		$('#tableLecturaEscritura tbody tr').remove()
		$("#modalNuevoPerfil").modal("show");
	});

	$("#btnNuevoPerfilModulo").click(function(){
		$("#modalPerfil2").modal("show");
	});

	$("#btnNuevoPerfil2").click(function(){
		$("#modalPerfilNuevo").modal("show");
	});

	$('#btnListarModulo').click(function(){
		$('#modalListarModulo').modal('show');
		ListarModulos();
	})


	$('#guardarNuevoUsuario').click(function() {
		if (!$('#Usuario').val()) {
			alert('Debe ingresar un nombre de usuario')
			return;
		}
		if (!$('#Password').val()) {
			alert('Debe ingresar una contraseña')
			return;
		}
		if (!$('#Perfil').val()) {
			alert('Debe seleccionar un perfil')
			return;
		}
		var xhr = $.ajax({
			url: "../controllers/server_processingUsuario.php",
			type: "post",
			data: {
				Usuario : $('#Usuario').val(),
				IdUsuarioPerfil : $('#IdUsuarioPerfil').val(),
				Password : $('#Password').val(),
				NombreUsuario: $('#NombreUsuario').val()
			},
			dataType: "json",
			success: function(respuesta){
				if (respuesta.success) {
						$.notify({
								icon: 'fa fa-check',
								message: respuesta.success
						}, {
								type: 'success'
						});

				} else {
						$.notify({
								icon: 'fa fa-exclamation',
								message: respuesta.error
						}, {
								type: 'danger'
						});
				}
				$("#nuevo").modal("hide");
				ListarUsuarioPerfil();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("Status: " + textStatus); alert("Error: " + errorThrown);
			}
		});
		console.log(xhr)
	})


	$('#btnGuardarPerfilModulo').click(function() {
		var modulos = [];

		if (!$('#NuevoPerfil').val()) {
			alert('Ingrese un perfil')
			return;
		}
		$('#tableLecturaEscritura tbody tr').each(function(key, value) {
			var chkLectura = $(value).find('input[type="checkbox"].lectura').first();
			var chkEscritura = $(value).find('input[type="checkbox"].escritura').first();

			modulos.push({
				'IdUsuarioModulo': chkLectura.attr('data-idmodulo'),
				'Lectura': chkLectura.is(':checked') ? 1 : 0,
				'Escritura': chkEscritura.is(':checked') ? 1 : 0,
			})
		})

		var xhr = $.ajax({
			url: "../controllers/server_processingUsuarioPerfil.php",
			type: "post",
			data: {
				//IdUsuarioPerfil: $('#IdUsuarioPerfil').val() || 0,
				UsuarioPerfil : $('#NuevoPerfil').val().toLowerCase(),
				Modulos : JSON.stringify(modulos)
			},
			dataType: "json",
			success: function(respuesta){
				if (respuesta.success) {
						$.notify({
								icon: 'fa fa-check',
								message: respuesta.success
						}, {
								type: 'success'
						});

				} else {
						$.notify({
								icon: 'fa fa-exclamation',
								message: respuesta.error
						}, {
								type: 'danger'
						});
				}
				$("#modalNuevoPerfil").modal("hide");

				ListarPerfil();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("Status: " + textStatus); alert("Error: " + errorThrown);
			}
		});
		console.log(xhr)
	})

	$('#btnNuevoModulo').click(function() {
		$('#modalNuevoModulo').modal('show');
	})

	$('#btnNuevoModuloGuardar').click(function() {
		if (!$('#txtNuevoModulo').val()) {
			alert('Debe establecer un nombre de módulo');
			return;
		}

		var xhr = $.ajax({
			url: "../controllers/server_processingUsuarioModulo.php",
			type: "post",
			data: {
				UsuarioModulo : $('#txtNuevoModulo').val()
			},
			dataType: "json",
			success: function(respuesta){
				if (respuesta.success) {
						$.notify({
								icon: 'fa fa-check',
								message: respuesta.success
						}, {
								type: 'success'
						});

				} else {
						$.notify({
								icon: 'fa fa-exclamation',
								message: respuesta.error
						}, {
								type: 'danger'
						});
				}
				$("#modalNuevoModulo").modal("hide");

				ListarModulos();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("Status: " + textStatus); alert("Error: " + errorThrown);
			}
		});
		console.log(xhr)
	})


	$('#nuevo').on('hide.bs.modal', function(e) {
		$('#IdUsuarioPerfil').val('')
		$('#Perfil').val('')
		$('#NombreUsuario').val('')
		$('#Password').val('')
		$('#Usuario').val('')
	})
});

function ListarModulos() {
	$("#tableModulos").DataTable().destroy();
	$("#tableModulos").DataTable({
			"bProcessing": true,
			"sAjaxSource": "../controllers/server_processingUsuarioModulo.php",
			"bPaginate":true,
			"sPaginationType":"full_numbers",
			"iDisplayLength": 5,
			"aoColumns": [
				{ mData: 'IdUsuarioModulo' } ,
				{ mData: 'UsuarioModulo' }
			],
			"rowCallback": function(row, data, index){
					$(row).on('click', function() {
						var existe = false;
						$('#tableLecturaEscritura tbody tr td:first-child').each(function(key, value) {
							if($(value).text() == data.UsuarioModulo) {
								alert('ya selecciono este módulo')
								existe = true;
								return;
							}
						})

						if (existe) {
							return;
						}

						var tr = $('<tr></tr>');
						tr.append('<td>' + data.UsuarioModulo + '</td>')
						tr.append('<td><input class="lectura" type="checkbox" data-idModulo="' + data.IdUsuarioModulo + '" /></td>')
						tr.append('<td><input class="escritura" type="checkbox" /></td>')
						tr.hide()
						$('#tableLecturaEscritura tbody').append(tr)
						tr.show('slow')

						$('#modalListarModulo').modal('hide');
					})
			}
	});
}

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
            	return "<a onclick='EditarUsuario("+ JSON.stringify(row) +");' class='btn'><i class='fa fa-pencil'></i></a>"
            }}
            ]
	});
}

function EditarUsuario(user) {
	$('#nuevo').modal('show');
	$('#Usuario').val(user.Usuario)
	$('#Password').val(user.Password)

	$('#NombreUsuario').val(user.NombreUsuario)
	$('#IdUsuarioPerfil').val(user.IdUsuarioPerfil)
	$('#Perfil').val(user.UsuarioPerfil)
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
          return "<a onclick='EditarPerfil("+ JSON.stringify({idUsuarioPerfil: row.IdUsuarioPerfil, usuarioPerfil: row.UsuarioPerfil })+ ", event);' class='btn'><i class='fa fa-pencil'></i></a>"
        }}
      ],
			"rowCallback": function(row, data, index){
					$(row).on('click', function() {
						console.log(data)
						$('#IdUsuarioPerfil').val(data.IdUsuarioPerfil);
						$('#Perfil').val(data.UsuarioPerfil);
						$('#modalPerfil').modal('hide');
					})
			}
	});
}

function EditarPerfil(perfil,  event) {
	event.stopPropagation()
	$('#modalNuevoPerfil').modal('show');
	$('#NuevoPerfil').val(perfil.usuarioPerfil)

	var xhr = $.ajax({
		url: "../controllers/server_processingUsuarioPerfil.php",
		type: "get",
		data: {
			IdUsuarioPerfil : perfil.idUsuarioPerfil,
		},
		dataType: "json",
		success: function(respuesta){
			$('#tableLecturaEscritura tbody tr').remove()
			$.each(respuesta, function(index, modulo) {
				var tr = $('<tr></tr>');
				tr.append('<td>' + modulo.UsuarioModulo + '</td>');
				console.log(modulo.Lectura)
				tr.append('<td><input type="checkbox" class="lectura" data-idmodulo="' + modulo.IdUsuarioModulo + '" ' + (modulo.Lectura == 1 ? 'checked' : '') + ' /></td>');
				tr.append('<td><input type="checkbox" class="escritura" data-idmodulo="' + modulo.IdUsuarioModulo + '" ' + (modulo.Escritura == 1 ? 'checked' : '') + ' /></td>');
				$('#tableLecturaEscritura tbody').append(tr);
			})
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert("Status: " + textStatus); alert("Error: " + errorThrown);
		}
	});
	console.log(xhr)
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
				<div class=" ">
					<div class="col s12 form-group">

						<div class="form-group">
							<label for="Usuario">Usuario</label>
							<input type="text" name="usuario" id="Usuario" class="form-control">
						</div>
						<div class="form-group">
							<label for="Password">Password</label>
							<div class="form-inline">
								<input type="password" name="password" id="Password" class="form-control">
								<!--<a class="btn btn-info" id="btnVerPass"><i class="fa fa-eye"></i></a>-->
							</div>
						</div>
						<div class="form-group">
							<label for="Usuario">Nombre de Usuario</label>
							<input type="text" name="nombreUsuario" id="NombreUsuario" class="form-control">
						</div>
						<div class="form-group">
							<label for="Password">Perfil</label>
							<div class="form-inline">
								<input type="hidden" id="IdUsuarioPerfil" />
								<input type="text" id="Perfil" placeholder="Seleccione un perfil" class="form-control" readonly="readonly">
								<button type="button" id="btnPerfil" class="btn btn-success"><i class="fa fa-search-plus"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="guardarNuevoUsuario"><i class="fa fa-save"></i> Guardar</button>
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

<!--<div class="modal fade" id="modalNuevoUsuario" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Agregar Usuario</div>
			<div class="modal-body">
				<form class="form">
					<div class="form-group">
						<label for="Usuario">Usuario</label>
						<input type="text" name="usuario" id="Usuario" class="form-control">
					</div>
					<div class="form-group">
						<label for="Password">Password</label>
						<div class="form-inline">
							<input type="password" name="password" id="Password" class="form-control">
							<a class="btn btn-info" id="btnVerPass"><i class="fa fa-eye"></i></a>
						</div>
					</div>
					<div class="form-group">
						<label for="Usuario">NombreUsuario</label>
						<input type="text" name="nombreUsuario" id="NombreUsuario" class="form-control">
					</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" id="guardarNuevoUsuario"><i class="fa fa-save"></i> Guardar</button>
				</form>
			</div>
		</div>
	</div>
</div>
-->

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
				<div class="">
					<div class="col s12 form-group">
						<label for="NuevoPerfil">Perfil</label>
						<div class="form-inline">
							<input type="hidden" class="form-control" id="IdUsuarioPerfil" >
							<input type="text" class="form-control" id="NuevoPerfil">
							<!--<button type="button" class="btn btn-success" id="btnNuevoPerfilModulo"><i class="fa fa-search-plus"></i></button>-->
						</div>
					</div>
					<div class="col s12 form-group">
						<label for="Modulo">Módulo</label>
						<div class="form-inline">
							<input type="text" class="form-control" id="Modulo" readonly>
							<button type="button" class="btn btn-success" id="btnListarModulo"><i class="fa fa-search-plus"></i></button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="panel panel-success">
						<div class="panel-heading">Modulo</div>
						<div class="panel-body">
							<table class="table table-bordered" id="tableLecturaEscritura">
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

<div class="modal fade" id="modalListarModulo" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Seleccione un Módulo</div>
			<div class="modal-body">
				<table class="table table-bordered table-striped" id="tableModulos">
					<thead>
					  <th>
							IdUsuarioModulo
						</th>
						<th>
							Módulo
						</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnNuevoModulo" class="btn btn-success"><i class="fa fa-save"></i> Nuevo Mòdulo</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modalNuevoModulo" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">Nuevo Módulo</div>
			<div class="modal-body">
				<input type="text" id="txtNuevoModulo" />
			</div>
			<div class="modal-footer">
				<button type="button" id="btnNuevoModuloGuardar" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
			</div>
		</div>
	</div>
</div>

</html>
