<?php  ?>

<html>
<head>
	<title>Reporte Stock</title>
</head>
<?php include_once 'linker.php'; ?>

<script type="text/javascript">

$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = parseInt( $('#min').val(), 10 );
        var max = parseInt( $('#max').val(), 10 );
        var stock = parseFloat( data[4] ) || 0; // use data for the stock column

        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && stock <= max ) ||
             ( min <= stock   && isNaN( max ) ) ||
             ( min <= stock   && stock <= max ) )
        {
            return true;
        }
        return false;
    }
);

$(document).ready(function(e){

    $("#btnAlmacen").off("click").click(function(e){
      listarAlmacen();
      $("#modalAlmacen").modal("show");
    });
      $('#min, #max').keyup( function() {
        $("#tableProducto").DataTable().draw();
        } );
    $("#btnReportar").click(function(e){
    window.location="../controllers/reporteExcel2.php?almacen="+$("#txtAlmacen").val();
    });



    $('#generarStock').click(function() {
      if (!window.isLoadStock) {
        listarProveedor($('#txtAlmacen').val())
      }else {
        listarProveedor($('#txtAlmacen').val(), true);
      }
    })



});
function listarProveedor(almacen, serverSide = false){
  if (serverSide) {
    serverSide = true;
    ajaxSource = "../controllers/server_processingReporteStock.php?serverSide=1&almacen=" + almacen;
  } else {
    serverSide = false;
    ajaxSource = "../controllers/server_processingReporteStock.php?almacen=" + almacen;
  }

  $("#tableProducto").DataTable().destroy();
    return $("#tableProducto").DataTable({
            "serverSide": serverSide,
            "bProcessing": true,
            "retrieve" : true,
            "order": [[ 4, "desc" ]],
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "sAjaxSource": ajaxSource,
            /*"ajax":{
              "url" : "../controllers/server_processingReporteStock.php",
              "type" : "get",
              "data" : {
                almacen : almacen
              }
            },*/
            "aoColumns": [
            { mData: 'marca' } ,
            { mData: 'categoria' },
						{ mData: 'formafarmaceutica' },
            { mData: 'Codigo' },
            { mData: 'Producto' },
            { mData: 'StockMinimo' },
            { mData: 'controlaStock' },
            { mData: 'stock' },
            { mData: 'MovimientoPrecio' },
            { mData: 'MovimientoCantidad' },
            { mData: 'MovimientoTotal' },
            ],
            "initComplete": function( settings, json ) {
              window.isLoadStock = true;
            }
        });
     /* $("#tableProducto tbody").on("click", "tr", function(){
        $("#txtProveedor").val($(this).children("td").eq(1).html());
        $("#modalProveedor").modal("hide");
        });*/
}

function listarAlmacen(){
    $("#tableAlmacen").DataTable().destroy();
    $("#tableAlmacen").DataTable({
            "bProcessing": true,
            //"responsive" : true,
            "sAjaxSource": "../controllers/server_processingAlmacen.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            //"bAutoWidth": false,
            //"autoWidth" : false,
            //"bFilter": false,
            "aoColumns": [
            { mData: 'IdAlmacen' } ,
            { mData: 'Almacen' }

            ]
        });

     $("#tableAlmacen tbody").off("click").on("click", "tr", function(){

        window.isLoadStock = false;
        // Ejecutar cursor - carga stock
        $.ajax({
          url: '../controllers/server_processingReporteStock.php?cursor=1&almacen=' + $(this).children("td").eq(1).html(),
          type: 'get',
          dataType: 'json',
          success: function(respuesta){
              if(respuesta.success){
                window.isLoadStock = true
              }
              else{
                window.isLoadStock = false
              }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
              window.isLoadStock = false
              //alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
        });
       
        $("#txtAlmacen").val($(this).children("td").eq(1).html());
        //listarProveedor($(this).children("td").eq(1).html(), true);
        $("#modalAlmacen").modal("hide");
      });
}
</script>

<body>
<?php include("header.php"); ?>

<div class="bt-panel">
 <button id="btnReportar" class="btn btn-success fab" data-toggle="modal" data-target="#nuevo"><i class="fa fa-file-excel-o"></i></button>
	<div class="container center_div" >
    <div class="row">
      <div class="col-md-6 form-group">
        <label>Almacen</label>
        <div class="form-inline">
        <input type="text" readonly id="txtAlmacen" class="form-control">
        <button type="button" class="btn btn-success" id="btnAlmacen"><i class="fa fa-search"></i></button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 form-group">
        <label>Min</label>
        <div class="form-inline">
        <input type="text" value="0" id="min" class="form-control">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 form-group">
        <label>Max</label>
        <div class="form-inline">
        <input type="text"  id="max" value="5000" class="form-control">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 form-group">
        <button class="btn btn-success" id="generarStock">Generar</button>
      </div>
    </div>
		<div class="center_div_form">
      <table id="tableProducto" class="table table-bordered table-striped">
        <thead>
          <th>MARCA</th>
          <th>CATEGORIA</th>
					<th>FORMA FARMACEUTICA</th>
          <th>CODIGO</th>
          <th>PRODUCTO</th>
          <th>STOCK MINIMO</th>
          <th>CONTROLA STOCK</th>
          <th>STOCK</th>
          <th>P/U compra</th>
          <th>CANT.U Compra</th>
          <th>TOTAL</th>
        </thead>
      </table>
		</div>
  </div>
</div>

<?php include("footer.php"); ?>
</body>

<div class="modal fade" id="modalAlmacen" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4>Seleccionar Almacen</h4>
      </div>
      <div class="row modal-body">
        <div class="col-md-12">
          <table id="tableAlmacen" class="table table-bordered table-striped">
            <thead>
              <th>#</th>
              <th>ALMACEN</th>
            </thead>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i>Cerrar</i></button>
      </div>
    </div>
  </div>
</div>
</html>
