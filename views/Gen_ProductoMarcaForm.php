<?php
include_once("../clases/BnGeneral.php");

 ?>

  <!DOCTYPE html>
 <html>
 <head>
  <title>CUSTODIO - Marca</title>
 </head>
<?php include_once 'linker.php'; ?>
<script type="text/javascript">

  $(document).ready(function() {
    var table = $('#tableProductoMarca').DataTable({
      "bProcessing": true,
      "sAjaxSource": "../controllers/server_processing.php",
      "bPaginate":true,
      "sPaginationType":"full_numbers",
      "iDisplayLength": 5,
      "aoColumns": [
      { mData: 'ID' } ,
      { mData: 'ProductoMarca' },
      { mData: 'Anulado' },
      { mData: 'FechaReg' },
      { mData: 'UsuarioReg' },
      { mData: 'FechaMod' },
      { mData: 'UsuarioMod' }
      ]
    });

    $('#tableProductoMarca tbody').on('click', 'tr', function () {
        //var data = table.row( this ).data();
        //var data = $(this).children("td").eq(0).html();
        $("#IdProducto").val($(this).children("td").eq(0).html());
        $("#IdProducto").attr("readonly", true);
        $("#IdProducto").css("width", "50px");
        $("#MarcaProducto").val($(this).children("td").eq(1).html());
        $("#nuevo").modal("show");
    } );

    $("#btn-nuevo").click(function(){
        $("#IdProducto").val("");
        $("#MarcaProducto").val("");
        //$("#IdProducto").hide();
    });

} );

</script>

 <body>
<?php include("header.php"); ?>
<div class="bt-panel">

 <button id="btn-nuevo" class="btn btn-danger fab" data-toggle="modal" data-target="#nuevo"><i class="fa fa-plus"></i></button>
 <div class="sTableProductoMarca" style="overflow-x:auto">
  <table id="tableProductoMarca" class="table table-striped table-bordered">
    <thead>
      <th class="">ID</th>
      <th>Marca</th>
      <th>Estado</th>
      <th>FechaReg</th>
      <th>UsuarioReg</th>
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
              <h4 class="modal-title">Añadir Marca</h4>
      </div>
      <div class="modal-body">
        <form id="modal-form" action="../controllers/Gen_ProductoMarcaGuardar.php" method="get">
               <input type="hidden" class="form-control" id="IdProducto"  name="idproductomarca">
            <div class="form-group">
               <label for="MarcaProducto">Marca</label>
               <input type="text" class="form-control" id="MarcaProducto"  name="productomarca" placeholder="Marca">
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
