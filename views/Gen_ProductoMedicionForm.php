<?php 
include_once("../clases/BnGeneral.php");

 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Botica - Medicion</title>
 </head>
<?php include_once 'linker.php'; ?>
<script type="text/javascript">
	
	$(document).ready(function() {
    ListarMedicion();
     
    $('#tableProductoMedicion tbody').on('click', 'tr', function () {
        $("#IdProducto").val($(this).children("td").eq(0).html());
        $("#IdProducto").attr("readonly", true);
        $("#IdProducto").css("width", "50px");
        $("#MedicionProducto").val($(this).children("td").eq(1).html());
        $("#nuevo").modal("show");
    } );

    $("#btn-nuevo").click(function(){
        $("#IdProducto").val("");
        $("#MedicionProducto").val("");
        //$("#IdProducto").hide();
    });

} );

  function ListarMedicion(){
          $("#tableProductoMedicion").DataTable().destroy();
          var table4 = $("#tableProductoMedicion").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingMedicion.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProductoMedicion' } ,
            { mData: 'ProductoMedicion' },
            { mData: 'Anulado' },
            { mData: 'FechaReg' },
            { mData: 'UsuarioReg' },
            { mData: 'FechaMod' },
            { mData: 'UsuarioMod' }
            ]
        });
}

</script>

 <body>
<?php include("header.php"); ?>
<div class="bt-panel">

 <button id="btn-nuevo" class="btn btn-danger fab" data-toggle="modal" data-target="#nuevo"><i class="fa fa-plus"></i></button>
 <div class="sTableProductoMedicion" style="overflow-x: auto;">
 	<table id="tableProductoMedicion" class="table table-striped table-bordered">
 		<thead>
      <th class="">#</th>
 			<th>Medicion del Producto</th>
      <th>Estado</th>
      <th>Fecha de Registro</th>
      <th>Usuario Registro</th>
      <th>Fecha Mod</th>
      <th>UsuarioMod</th>
 		</thead>
 	</table>
 </div>

</div>
<?php include("footer.php"); ?>
 </body>

 <div class="modal fade" id="nuevo" role="dialog">
 	<div class="modal-dialog">
 		<div class="modal-content">
 			<div class="modal-header">
 				<button type="button" class="close" data-dismiss="modal">&times;</button>
          		<h4 class="modal-title">Añadir Medicion de Producto</h4>
 			</div>
 			<div class="modal-body">
 				<form id="modal-form" action="Gen_ProductoMedicionGuardar.php" method="get">
               <input type="hidden" class="form-control" id="IdProducto"  name="idproductomedicion">
  					<div class="form-group">
   						 <label for="MedicionProducto">Medicion del producto</label>
   						 <input type="text" class="form-control" id="MedicionProducto"  name="productomedicion" placeholder="Medicion del producto">
  					</div>
  					<input type="hidden" name="usuario" value="Jeam">
 			</div>
 			<div class="modal-footer">
 				<button type="submit" class="btn btn-success enviar"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
 				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
 			</form>
 			</div>
 		</div>
 	</div>
 </div>
 </html>