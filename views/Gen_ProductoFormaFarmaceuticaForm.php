<?php
include("../clases/BnGeneral.php");

 ?>

 <!DOCTYPE html>
 <html>
 <head>
  <title>Rojas Sport - Forma Farmaceutica</title>
 </head>
<?php include_once 'linker.php'; ?>
<script type="text/javascript">

  $(document).ready(function() {
     ListarFormaFarmaceutica();
    $('#tableProductoFormaFarmaceutica tbody').on('click', 'tr', function () {
        $("#IdProducto").val($(this).children("td").eq(0).html());
        $("#IdProducto").prop("readonly", true);
        $("#IdProducto").css("width", "50px");
        $("#FormaFarmaceuticaProducto").val($(this).children("td").eq(1).html());

       // $("#Anulado").val(data[1]);
        if ($(this).children("td").eq(2).html()=="1") {
          $("#Anulado").prop("checked", true);
          }else{
            $("#Anulado").prop("checked", false);
          }

        $("#nuevo").modal("show");
    } );

    $("#btn-nuevo").click(function(){

        $("#IdProducto").val("");
        $("#Anulado").prop("checked", false);
        $("#FormaFarmaceuticaProducto").val("");
        $("#nuevo").modal({backdrop: false});
    });

} );

  function ListarFormaFarmaceutica(){
          $("#tableProductoFormaFarmaceutica").DataTable().destroy();
          var table4 = $("#tableProductoFormaFarmaceutica").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingFormaFarmaceutica.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProductoFormaFarmaceutica' } ,
            { mData: 'ProductoFormaFarmaceutica' },
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

 <button id="btn-nuevo" class="btn btn-danger fab"><i class="fa fa-plus"></i></button>
 <div class="sTableProductoFormaFarmaceutica" style="overflow-x: auto;">
  <table id="tableProductoFormaFarmaceutica" class="table table-striped table-bordered">
    <thead>
      <th class="">#</th>
      <th>Forma Farmaceutica del Producto</th>
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
              <h4 class="modal-title">Añadir Forma Farmaceutica de Producto</h4>
      </div>
      <div class="modal-body">
        <form id="modal-form" action="../controllers/Gen_ProductoFormaFarmaceuticaGuardar.php" method="get">
               <input type="hidden" class="form-control" id="IdProducto"  name="idproductoformafarmaceutica">
            <div class="form-group">
               <label for="FormaFarmaceuticaProducto">Forma Farmaceutica del Producto</label>
               <input type="text" class="form-control" id="FormaFarmaceuticaProducto"  name="productoformafarmaceutica" placeholder="Forma Farmaceutica del Producto">
               <div><input  type="checkbox" id="Anulado" name="anulado"><b>Anulado</b></div>
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
