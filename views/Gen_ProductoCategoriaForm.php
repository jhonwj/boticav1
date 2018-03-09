<?php
include_once("../clases/BnGeneral.php");

//include_once("masterPage.php");

 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Botica - Categoria</title>
 </head>
<?php include_once 'linker.php'; ?>
<script type="text/javascript">

	$(document).ready(function() {
    //var table = $('#tableProductoCategoria').DataTable();
     ListarCategoria();
    $('#tableProductoCategoria tbody').on('click', 'tr', function () {
        $("#IdProducto").val($(this).children("td").eq(0).html());
        $("#IdProducto").attr("readonly", true);
        $("#IdProducto").css("width", "50px");
        $("#CategoriaProducto").val($(this).children("td").eq(1).html());
        $("#nuevo").modal("show");
    } );

    $("#btn-nuevo").click(function(){
        $("#IdProducto").val("");
        $("#CategoriaProducto").val("");
        //$("#IdProducto").hide();
    });

} );

  function ListarCategoria(){
          $("#tableProductoCategoria").DataTable().destroy();
          var table4 = $("#tableProductoCategoria").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingCategoria.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProductoCategoria' } ,
            { mData: 'ProductoCategoria' },
            { mData: 'Anulado' },
            { mData: 'FechaReg' },
            { mData: 'UsuarioReg' },
            { mData: 'FechaMod' },
            { mData: 'UsuarioMod' }
            //{ mData: 'IdProductoCategoriaSub' }
            ]
        });

}

</script>

 <body>

<?php include("header.php"); ?>
<div class="bt-panel">

 <button id="btn-nuevo" class="btn btn-danger fab" data-toggle="modal" data-target="#nuevo"><i class="fa fa-plus"></i></button>
 <div class="sTableProductoCategoria" style="overflow-x: auto;">
 	<table id="tableProductoCategoria" class="table table-striped table-bordered">
 		<thead>
      <th class="">#</th>
 			<th>Categoria</th>
      <th>Estado</th>
      <th>FechaReg</th>
      <th>Registro</th>
      <th>FechaMod</th>
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
          		<h4 class="modal-title">Añadir Categoria de Producto</h4>
 			</div>
 			<div class="modal-body">
 				<form id="modal-form" action="../controllers/Gen_ProductoCategoriaGuardar.php" method="get">
               <input type="hidden" class="form-control" id="IdProducto"  name="idproductocategoria">
  					<div class="form-group">
   						 <label for="CategoriaProducto">Categoria del Producto</label>
   						 <input type="text" class="form-control" id="CategoriaProducto"  name="productocategoria" placeholder="Categoria del Producto">
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
