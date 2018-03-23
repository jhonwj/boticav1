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
        listarStock($('#txtAlmacen').val())
      }else {
        listarStock($('#txtAlmacen').val(), true);
      }
    })

    $('#btnProducto').click(function() {
      listarStock($('#txtAlmacen').val(), false, 'tableProductosProveedor', $('#txtProveedor').attr('data-id'))
      $('#modalProductosProveedor').modal("show")

      var tableProveedor = $('#tableProductosProveedor').DataTable();
      var tableOrdenCompra = $('#tableOrdenCompra').DataTable();
      $('#tableProductosProveedor tbody').off("click").on('click', 'tr', function() {
        var d = tableProveedor.row( this ).data();
        
        if(tableOrdenCompra.column(4).data().indexOf(d.Producto) == -1) {
          tableOrdenCompra.row.add(d).draw(false);            
        }

        actualizarTotalOrdenCompra()
        $('#modalProductosProveedor').modal('hide')
        
       // console.log(tableOrdenCompra.rows('[Producto='+'TESTPRODUCTO'+']').any())
      })
    })

    

    $("#btnProveedor").click(function(e){
      listarProveedor();
      $("#modalProveedor").modal("show");
    });

    $("#generarOrdenCompra").click(function(e){
      listarStock($('#txtAlmacen').val(), false, 'tableOrdenCompra', $('#txtProveedor').attr('data-id'), true);
      $("#modalOrdenCompra").modal("show");
    });



    $('#btnGuardarOrdenCompra').click(function() {
      var idProveedor = $('#txtProveedor').attr('data-id');
      var total = parseFloat($('#totalOrdenCompra').text());

      var productos = [];

      $('#tableOrdenCompra tbody tr').each(function(index, value) {
        var idProducto = $(value).attr('data-idproducto')
        var cantidad = $(value).find('.cantidad').text()
        var precio = $(value).find('.precio').text()
        var producto = $(value).find('td').eq(4).text()
        productos.push({
          IdProducto: idProducto,
          Producto: producto,
          Cantidad: cantidad,
          Precio: precio
        })
      })
      
      if($("#tableOrdenCompra tbody tr").length>0){
        var xhr = $.ajax({
          url: '/controllers/server_processingOrdenCompra.php',
          type: 'post',
          data: {idProveedor : idProveedor, total: total, productos : JSON.stringify(productos)},
          dataType: 'json',
          success: function(respuesta){
            if (respuesta.success) {
                $.notify({
                    icon: 'fa fa-check',
                    message: respuesta.success
                }, {
                    type: 'success'
                });
                exportarOrdenCompra($('#txtProveedor').val(), total, productos);

            } else {
                $.notify({
                    icon: 'fa fa-exclamation',
                    message: respuesta.error
                }, {
                    type: 'danger'
                });
            }

          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
              alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
        });
        console.log(xhr);
      }else {
        alert("Registra al menos un producto");
      }
    })
});



function actualizarTotalOrdenCompra() {
  var total = 0
  $('#tableOrdenCompra tbody tr').each(function(index, value) {
    total += parseFloat($(value).find('.total').text())
  })
  $('#totalOrdenCompra').text(total)
}


function listarProveedor(){
  $("#tableProveedor").DataTable().destroy();
    var table4 = $("#tableProveedor").DataTable({
            "bProcessing": true,
            //"responsive" : true,
            "sAjaxSource": "../controllers/server_processingProveedor.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            //"bAutoWidth": false,
            //"autoWidth" : false,
            //"bFilter": false,
            "aoColumns": [
            { mData: 'IdProveedor' } ,
            { mData: 'Proveedor' },
            { mData: 'Ruc' },
            { mData: 'Direccion' },
            ]
        });
      $("#tableProveedor tbody").on("click", "tr", function(){
        $("#txtProveedor").val($(this).children("td").eq(1).html());
        $('#txtProveedor').attr('data-id', $(this).children("td").eq(0).html())
        $("#modalProveedor").modal("hide");
        $('#generarOrdenCompra').prop('disabled', false)
      });
}

function listarStock(almacen, serverSide = false, table = 'tableProducto', proveedor = false, menorStock = false){
  if (serverSide) {
    serverSide = true;
    ajaxSource = "../controllers/server_processingReporteStock.php?serverSide=1&almacen=" + almacen;
  } else if(proveedor) {
    serverSide = false;
    if(menorStock) {
      ajaxSource = "../controllers/server_processingReporteStock.php?menorStock=1&proveedor=" + proveedor + "&almacen=" + almacen;
    }else {
      ajaxSource = "../controllers/server_processingReporteStock.php?proveedor=" + proveedor + "&almacen=" + almacen;
    }
  } else {
    serverSide = false;
    ajaxSource = "../controllers/server_processingReporteStock.php?almacen=" + almacen;
  }

  $("#"+table).DataTable().destroy();
    var tableProducto = $("#"+table).DataTable({
            "serverSide": serverSide,
            "bProcessing": true,
            "retrieve" : true,
            "order": [[ 4, "desc" ]],
            "bPaginate": table == 'tableOrdenCompra' ? false : true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 10,
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
            { mData: 'IdProveedor' }
            ],
            "rowCallback": function( row, data, index ) {
              $(row).attr('data-idproducto', data.numero)
            },
            'columnDefs': [
              {
                  'targets': [8, 9, 10],
                  'createdCell':  function (td, cellData, rowData, row, col) {
                      if (col == 8) {
                        $(td).attr('class', 'precio')                        
                      } else if(col == 9) {
                        $(td).attr('class', 'cantidad')                                                
                      } else {
                        $(td).attr('class', 'total')
                      }

                      if(table == 'tableOrdenCompra') {
                        if(col == 8 || col == 9) {
                          $(td).addClass('bg-info')
                          $(td).attr('contenteditable', true)
                        }else {
                          $(td).addClass('bg-success')                          
                        }

                        
                      }
                  }
              }
            ],
            "initComplete": function( settings, json ) {
              window.isLoadStock = true;
              
              actualizarTotalOrdenCompra()
            }
        });
        $('#tableOrdenCompra').on('keyup',['.precio', '.compra'], function(e) {
          var precio = parseFloat($(e.target).parent().find('.precio').text())
          var cantidad = parseFloat($(e.target).parent().find('.cantidad').text())
          $(e.target).parent().find('.total').text((precio * cantidad).toFixed(2))
          actualizarTotalOrdenCompra()
        })
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
       $('#loading').addClass('active');
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
          },
          complete: function() {
            $('#loading').removeClass('active');
            
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
          <th>IDPROVEEDOR</th>
        </thead>
      </table>
		</div>
    <br><br>
    <div class="row">
      <div class="col-md-6 form-group">
        <label>Seleccione un proveedor</label>
        <div class="form-inline">
          <input type="text" readonly="" id="txtProveedor" class="form-control">
          <button type="button" class="btn btn-success" id="btnProveedor"><i class="fa fa-search"></i></button>
        </div><br>
        <button class="btn btn-success" id="generarOrdenCompra" disabled>Generar orden de compra</button>
      </div>
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


<div class="modal fade" id="modalProveedor" role="dialog">
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
			Lista de Proveedores
		</div>
		<div class="modal-body" style="overflow-x:auto;">
			<table id="tableProveedor" class="table table-bordered table-striped">
				<thead>
					<th >#</th>
					<th >Proveedor</th>
					<th >RUC</th>
					<th >Direccion</th>
				</thead>
			</table>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>


<div class="modal fade" id="modalOrdenCompra" role="dialog">
	<div class="modal-dialog" style="width:1100px">
	<div class="modal-content">
		<div class="modal-header">
			Orden de compra
		</div>
		<div class="modal-body" style="overflow-x:auto;">
      <div class="row">
          <div class="col-xs-6">
            <div class="input-group" style="margin-bottom:20px;">
                <input type="text" class="form-control" placeholder="Producto">
                <span class="input-group-btn">
                <button id="btnProducto" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
               </span>
            </div>
          </div>
      </div>
			<table id="tableOrdenCompra" class="table table-bordered table-striped">
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
          <th>IDPROVEEDOR</th>
				</thead>
			</table>
      <br><br>
		</div>
		<div class="modal-footer">
    <p>TOTAL ORDEN DE COMPRA: <strong id="totalOrdenCompra">0</strong></p>
			<button type="button" id="btnGuardarOrdenCompra" class="btn btn-success">Guardar orden de compra</button>
			<button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>




<div class="modal fade" id="modalProductosProveedor" role="dialog">
	<div class="modal-dialog" style="width:1000px">
	<div class="modal-content">
		<div class="modal-header">
			Añadir Productos en la orden de compra
		</div>
		<div class="modal-body" style="overflow-x:auto;">
			<table id="tableProductosProveedor" class="table table-bordered table-striped">
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
          <th>IDPROVEEDOR</th>
				</thead>
			</table>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>

