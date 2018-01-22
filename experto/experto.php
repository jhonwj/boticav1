<?php  ?>

<html>
<head>
	<title>Sistema Experto</title>
</head>
<link rel="stylesheet" type="text/css" href="../resources/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../resources/font-awesome/css/font-awesome.css">
<link rel="stylesheet" type="text/css" href="../resources/css/bootstrap.css">
<script src="../resources/js/jquery-3.2.1.min.js"></script>
<script src="../resources/js/bootstrap.js"></script>
<script src="../resources/js/jquery.dataTables.js"></script>
<style type="text/css">
	body{
		margin: 0;
	}
	.bt-panel{
    /*position: absolute;*/
		margin: 10px;
    max-width: 1000px;
    top: 50%;
    left: 50%;

    /*max-width: 1000px;*/
	}
	.btnAddDiagnostico{

	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$("#btnSintoma").click(function(){
			$("#btnAddSintoma").hide();
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
      					"edad" : parseInt($("#txtEdad").val().concat("00"))
      				}
      			},
      			"aoColumns": [
      			{ mData: 'IdSintoma' } ,
      			{ mData: 'Sintoma' },
      			{ mData: 'Edad' }
      			]
    		});
			$("#tableSintomaAdd tbody").on("click", "tr", function() {
				console.log($(this).html()); 
				$("#tableSintoma").append("<tr><td>"+$(this).children("td").eq(0).html()+"</td></tr>");
				$("#modalAddSintoma").modal("hide");
			});
		});

		$("#btnAddDiagnostico").click(function(){
			$("#modalAddDiagnostico").modal("show");
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
		});

		$("#btnDiagnosticoAdd").click(function(){
			//$("#modalAddDiagnostico").modal("show");
			$("#modalDiagnostico").modal("show");
		});

		$("#btnAddTratamiento").click(function(){
			$("#modalAddTratamiento").modal("show");
		});

		$("#btnAddSintomas").click(function(){
			$("#btnAddSintoma").show();
			$("#modalAddSintoma").modal("show");
			$("#tableSintomaAdd").DataTable().destroy();
			    var table4 = $("#tableSintomaAdd").DataTable({
      			"bProcessing": true,
      			"sAjaxSource": "../controllers/server_processingSintomas.php",
      			"bPaginate":true,
      			"sPaginationType":"full_numbers",
      			"iDisplayLength": 5,
      			"aoColumns": [
      			{ mData: 'IdSintoma' } ,
      			{ mData: 'Sintoma' },
      			{ mData: 'Edad' }
      			]
    		});

			$("#btnAddSintoma").click(function(){
				$("#modalSintoma").modal("show");
			});
			
		});

		$("#btnCompuesto").click(function(){
			$("#modalCompuestoAdd").modal("show");
		});

		$("#btnCompuestoAdd").click(function(){
			$("#modalCompuestoSave").modal("show");
		});

	});
</script>
<body>
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
  			<table id="tableSintoma" class="table">
    			<thead>
    				<th>#</th>
    				<th>Sintoma</th>
    				<th>Edad</th>
    			</thead>
    			<tbody>
    			</tbody>
  			</table>
		</div>
		<div class="">
			<div class="panel panel-info">
  			<div class="panel-heading">DIAGNOSTICOS
  				<button type="button" id="btnAddDiagnostico" class="btn btn-danger pull-right"><i class="fa fa-plus"></i></button>
  			</div>
  			<table id="tablePuntoVentaDet" class="table">
    			<thead>
    				<th>#</th>
    				<th>Diagnostico</th>
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
				<table class"table" id="tableDiagnosticoAdd">
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
			<div class="modal-body">
				<div class="form-inline">
					<label>ID</label>
					<input type="text" class="form-control">
				</div>
				<div class="form-input">
					<label>DIAGNOSTICO</label>
					<input type="text" class="form-control">
				</div>
				<div class="form-input">
					<label>PROBLEMA</label>
					<input type="text" class="form-control">
				</div>
				<div class="form-inline">
					<label>EDAD</label>
					<input type="text" class="form-control">
				</div>
				<hr>
				<div class="panel panel-info">
  						<div class="panel-heading">TRATAMIENTO
  					<button type="button" id="btnAddTratamiento" class="btn btn-danger pull-right"><i class="fa fa-plus"></i></button>
  					</div>
  					<table id="tableTratamiento" class="table">
    				<thead>
    					<th>#</th>
    					<th>TRATAMIENTO</th>
    				</thead>
    				<tbody>
    				</tbody>
  					</table>
				</div>
				<hr>
				<div class="panel panel-info">
  						<div class="panel-heading">SINTOMAS
  					<button type="button" id="btnAddSintomas" class="btn btn-danger pull-right"><i class="fa fa-plus"></i></button>
  					</div>
  					<table id="tableSintomas" class="table">
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
				<button type="button" id="btnSintomasGuardar" data-dismiss="modal" class="btn btn-danger">Cancelar</button>
				<button type="button" id="btnSintomasGuardar" class="btn btn-success">Guardar</button>
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
					<label>ID_TRATAMIENTO</label>
					<input type="text" class="form-control">
				</div>
				<div class="input-group">
      				<input type="text" class="form-control" placeholder="Seleccionar Compuesto">
      				<span class="input-group-btn">
        			<button class="btn btn-primary" id="btnCompuesto" type="button">ADD</button>
      				</span>
    			</div>
				<div class="form-inline">
					<label>Edad</label>
					<input type="text" class="form-control">
				</div>
				<div class="form-inline">
					<label>Tomas por dias</label>
					<input type="int" class="form-control">
				</div>
				<div class="form-inline">
					<label>Nro de dias</label>
					<input type="int" class="form-control">
				</div>
				<div class="form-group">
					<label>Observaciones</label>
					<textarea class="form-control" rows="4" cols="50"></textarea>
				</div>
			 </div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnDiagnosticoAdd" data-dismiss="modal" class="btn btn-danger">Cancelar</button>
				<button type="button" id="btnDiagnosticoAdd" class="btn btn-success">Guardar</button>
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
				<table class"" id="tableSintomaAdd">
					<thead>
						<th>ID</th>
						<th>Sintomas</th>
						<th>Edad</th>
					</thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				<button type="button" id="btnAddSintoma" class="btn btn-sucess" >Nuevo</button>
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
					<input type="text" class="form-control">
				</div>
				<div class="form-inline">
					<label>SINTOMA</label>
					<input type="text" class="form-control">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				<button type="button" id="btnSintoma" class="btn btn-sucess" >Nuevo</button>
			</div>
		</div>
	</div>
</div>

<!-- Anadir Compuesto -->
<div class="modal fade" id="modalCompuestoAdd" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Seleccionar Compsuesto</h1>
			</div>
			<div class="modal-body">
				<table class"" id="tableSintomaAdd">
					<thead>
						<th>ID</th>
						<th>COMPUESTO</th>
					</thead>
				</table>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
					<label>COMPUESTO</label>
					<input type="text" class="form-control">
				</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				<button type="button" id="btnCompuestoSave" class="btn btn-sucess" >Guardar</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>