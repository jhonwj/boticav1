<?php
include_once("../clases/BnGeneral.php");

 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Minimarket - Compuesto</title>
 </head>
<?php include_once 'linker.php'; ?>
<script type="text/javascript">

	$(document).ready(function() {
    $("#success-alert").hide();
     ListarCompuesto();
     TablaCompuesto();
    $("#btn-nuevo").click(function(){
        $("#IdProducto").val("");
        $("#CompuestoProducto").val("");
        $("#tableProductos tbody").empty();
        $("#nuevo").modal("show");
        //$("#IdProducto").hide();
    });

    $("#btnAddProductos").click(function(){
      $("#modalProductosAdd").modal("show");
      ListarProducto();
    });

    $("#btnGuardarCompuesto").click(function(e){
      e.preventDefault();
      var arrTableProductos = [];
      $("#tableProductos tbody").each(function(){
        $("#tableProductos tbody tr").each(function(){
          arrTableProductos.push([parseInt($(this).children("td").eq(0).html()), $(this).children("td").eq(1).html()]);
        });
      });
      var xhr = $.ajax({
        url: "Gen_ProductoCompuestoGuardar.php",
        type: "get",
        data: {"idproductocompuesto":$("#IdProducto").val(), "productocompuesto":$("#CompuestoProducto").val() ,data : JSON.stringify(arrTableProductos)},
        dataType: "html",
        success: function(respuesta){
        $("#nuevo").modal("hide");
        ListarCompuesto();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
      });
      console.log(xhr);
    });

} );

  function ListarCompuesto () {
        //$("#nuevo").modal("show");
      $("#tableProductoCompuesto").DataTable().destroy();
      var table4 = $("#tableProductoCompuesto").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingCompuesto.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProductoCompuesto' } ,
            { mData: 'ProductoCompuesto', sWidth: "10px" },
            { mData: 'Anulado'},
            { mData: 'FechaReg'},
            { mData: 'UsuarioReg'},
            { mData: 'FechaMod'},
            { mData: 'UsuarioMod' }
            ]
        });
}

function TablaCompuesto(){
        $("#tableProductoCompuesto tbody").delegate("tr", "click", function(e) {
        $("#IdProducto").val($(this).children("td").eq(0).html());
        $("#CompuestoProducto").val($(this).children("td").eq(1).html());
        $("#tableProductos tbody").empty();
        var xhr = $.ajax({
        url: "../controllers/server_processingProductoCompuesto.php",
        type: "get",
        data: {"Producto": "" ,"Compuesto": $(this).children("td").eq(0).html(), "CompuestoNombre": ""},
        dataType: "html",
        //async: false,
        success: function(respuesta){
          var response = JSON.parse(respuesta);
          $.each(response, function(data, value){
            var fila = "<tr><td>"+ value.IdProducto +"</td><td class='nombreProductoAdd'>"+ value.Producto + "</td><td class='text-center'><a id='btnEliminarProducto' class='btn' onclick='EliminarProducto("+value.IdProducto+")'><i class='fa fa-trash fa-2x'></i></a></td></tr>";
            $("#tableProductos tbody").append(fila);
          });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
      });
      console.log(xhr);
        $("#nuevo").modal("show");
        event.stopPropagation();
      });
}

function ListarProducto(){
  $("#tableProductosAdd").DataTable().destroy();
      var table4 = $("#tableProductosAdd").DataTable({
            "serverSide": true,
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingProducto.php?serverSide=1",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProducto' } ,
            { mData: 'Producto' }
            ]
        });
      $("#tableProductosAdd tbody").on("click", "tr", function(e) {

        $("#tempIdProducto").val($(this).children("td").eq(0).html());
        $("#tempProducto").val($(this).children("td").eq(1).html());

          var Encontrado = 0;

              $("#tableProductos tbody").each(function(index, el) {
                        $("#tableProductos tbody tr").each(function(index, el) {
                        var sintoma = $(this).find('.nombreProductoAdd').html();
                  if (sintoma == $("#tempProducto").val()) {
                 Encontrado = 1;
                  }
              });

            //console.log(Encontrado);

            if (Encontrado ==0) {
            var fila = "<tr><td>"+ $("#tempIdProducto").val() +"</td><td class='nombreProductoAdd'>"+ $("#tempProducto").val() + "</td><td class='text-center'><a id='btnEliminarProducto' class='btn' onclick='EliminarProducto("+$("#tempIdProducto").val()+")'><i class='fa fa-trash fa-2x'></i></a></td></tr>";
            $("#tableProductos tbody").append(fila);
            $.notify({
              icon: 'fa fa-plus',
              message: "<strong>"+$("#tempProducto").val()+"</strong> Ha sido agregado"
            });
            //$("#success-alert").children("strong").destroy();
            }
      });
      });
}

function EliminarProducto(idproducto){
  $("#tableProductos tbody").each(function(){
    $("#tableProductos tbody tr").each(function(){
      if(idproducto == $(this).children("td").eq(0).html()){
        $(this).remove();
        $.notify({
          icon: 'fa fa-plus',
          message: "<strong>"+$(this).children("td").eq(1).html()+"</strong> Ha sido eliminado"
          },{
          type : 'warning'
        });
      }
    });
  });
}

</script>

 <body>
<?php include("header.php"); ?>
<div class="bt-panel">

 <button id="btn-nuevo" class="btn btn-danger fab"><i class="fa fa-plus"></i></button>
 <div class="sTableProductoCompuesto" style="overflow-x: auto;">
 	<table id="tableProductoCompuesto" class="table table-striped table-bordered">
 		<thead>
      <th class="">#</th>
 			<th>Compuesto del Producto</th>
      <th>Estado</th>
      <th>FechaReg</th>
      <th>UsuarioReg</th>
      <th>FechaMod</th>
      <th>UsuarioMod</th>
 <!--     <th>IdProductoCategoriaSub</th> -->
 		</thead>
 		<tbody>
 		</tbody>
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
          		<h4 class="modal-title">Añadir Compuesto de Producto</h4>
 			</div>
 			<div class="modal-body">
               <input type="hidden" class="form-control" id="IdProducto"  name="idproductocompuesto">
  					<div class="form-group">
   						 <label for="CompuestoProducto">Compuesto del Producto</label>
   						 <input type="text" class="form-control" id="CompuestoProducto"  name="productocompuesto" placeholder="Compuesto del Producto">
  					</div>
  					<input type="hidden" name="usuario" value="Jeam">
            <div class="panel panel-success">
              <div class="panel-heading" style="height:50px;">Productos
                <button type="button" id="btnAddProductos" class="btn btn-success pull-right"><i class="fa fa-plus"></i></button>
              </div>
              <input type="hidden" id="tempIdProducto">
              <input type="hidden" id="tempProducto">
              <table id="tableProductos" class="table table-striped table-bordered">
                <thead>
                  <th>Id</th>
                  <th>Producto</th>
                </thead>
                <tbody></tbody>
              </table>
            </div>
 			</div>
 			<div class="modal-footer">
 				<button type="button" id="btnGuardarCompuesto" class="btn btn-success">Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
 				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
 			</div>
 		</div>
 	</div>
 </div>

<div class="modal fade" id="modalProductosAdd" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Productos </h4>
      </div>
      <div class="modal-body">
        <table id="tableProductosAdd" class="table table-striped table-bordered">
          <thead>
            <th>Id</th>
            <th>Productos</th>
          </thead>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>
 </html>
