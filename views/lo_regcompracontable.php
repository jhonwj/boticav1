<?php  ?>

<html>
<head>
  <title>Registro de Movimiento</title>
</head>
<?php include_once 'linker.php'; ?>

<script type="text/javascript">

$(document).ready(function(e){
  $("#btnGenerar").click(function(e){
    $("#tableRegMov tbody").empty();
    var xhr = $.ajax({
      url: "lo_listarRegCompra.php",
      type: "get",
      data: {periodoT: $("#txtPeriodoT").val(), declarado: $("#declarado").prop("checked")},
      dataType: "html",
      success: function(res){
        var respuesta = JSON.parse(res);
        var tableBody = "";
        $.each(respuesta, function(data, value){
          tableBody = tableBody + "<tr><td>"+value.IdMovimiento+"</td><td>"+value.MovimientoFecha+"</td><td>"+value.IdMovimientoTipo+"</td><td>"+value.TipoMovimiento+"</td><td>"+value.Serie+"</td><td>"+value.Numero+"</td><td>"+value.Proveedor+
          "</td><td>"+value.SUBTOTAL+
          "</td><td>"+value.ISC+
          "</td><td>"+value.IGV+
          "</td><td>"+value.FLETE+
          "</td><td>"+value.Percepcion+
          "</td><td>"+(value.Moneda||'')+
          "</td><td>"+value.TOTAL+
          "</td><td><a class='btn' onclick='EliminarRegMov("+ value.IdMovimiento +");'><i class='fa fa-trash'></i></a></td></tr>" ;
        });
        $("#tableRegMov tbody").append(tableBody);
        var subtotal = 0;
        var igv = 0;
        var total = 0;
        var subtotalUSD = 0;
        var igvUSD = 0;
        var totalUSD = 0;

        $("#tableRegMov tbody").each(function(){
          $("#tableRegMov tbody tr").each(function(){
            if ($(this).children("td").eq(12).text() == 'USD') {
              subtotalUSD +=  parseFloat($(this).children("td").eq(7).text());
              igvUSD +=  parseFloat($(this).children("td").eq(9).text());
              totalUSD +=  parseFloat($(this).children("td").eq(13).text());
            } else {
              subtotal +=  parseFloat($(this).children("td").eq(7).text());
              igv +=  parseFloat($(this).children("td").eq(9).text());
              total +=  parseFloat($(this).children("td").eq(13).text());
            }
          });
        });
        console.log(total);
        $("#txtSubTotal").val(subtotal);
        $("#txtIGV").val(igv);
        $("#txtTotal").val(total.toFixed(2));

        $("#txtSubTotalUSD").val(subtotalUSD);
        $("#txtIGVUSD").val(igvUSD);
        $("#txtTotalUSD").val(totalUSD.toFixed(2));
      },
      error: function(err){
        alert(err);
      }
    });
    console.log(xhr);
  });

  $("#btnExcel").click(function(){
    window.location.href="ReporteExcelRegCompraContable.php?txtPeriodoT="+$("#txtPeriodoT").val()+"&declarado="+$("#declarado").prop("checked");
  });

$("#tableRegMov tbody").on("click", "tr", function(e){
    $("#tableProducto tbody tr").remove();
  var idMov = $(this).children("td").eq(0).html();
   var xhr = $.ajax({
    url: '../controllers/serverprocessingProductosRegMov.php',
    type: 'get',
    data:  {"idMov" : idMov},
    dataType: 'html',
    success: function(respuesta){
        var response = JSON.parse(respuesta);
        var fila = "";
        $.each(response, function(data, value){
          var check = "";
          if (value.TieneIgv == null || value.TieneIgv =="0") {
           check= "<input type='checkbox'>"
          }else{
           check= "<input type='checkbox' checked>"
          }
          fila = "<tr><td>"+value.hashMovimiento+
          "</td><td>"+value.CodigoBarra+
          // "</td><td>"+value.ProductoFormaFarmaceutica+
          "</td><td>"+value.ProductoMarca+
          "</td><td>"+value.Producto+
          // "</td><td>"+value.PrecioContado+
          "</td><td>"+value.ProductoMedicion+
          "</td><td>"+value.Cantidad+
          "</td><td>"+check+
          "</td><td>"+value.Precio+
          "</td><td>"+(value.Precio*value.Cantidad)+
          "</td></tr>";
          $("#tableProducto tbody").append(fila);
        });
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    }
      });
  console.log(xhr);
    $("#modalProductos").modal("show");
  });

});

function EliminarRegMov(docVenta){
  $("#tableRegMov tbody").each(function(e){
    $("#tableRegMov tbody tr").each(function(e){
      if(docVenta == $(this).children("td").eq(0).html()){
        $(this).remove();
      }
    });
  });
}

function SumarTotalIgvSub(){
  $("#tableRegMov tbody").each(function(e){

  });
}

</script>

<body>
<?php include("header.php"); ?>
 <button id="btnExcel" class="btn btn-success fab2"><i class="fa fa-file-excel-o"></i></button>

<div class="bt-panel">
  <div class="container center_div" >
    <div class="row">
      <div class="col-md-4 form-group">
        <label>Fecha Final (Periodo Tributario)</label>
        <input type="number" maxlength="6" value="<?php echo date('Ym'); ?>" id="txtPeriodoT" class="form-control">
      </div>
      <div class="col-md-4 form-group">
        <div class="checkbox">
            <label><input id="declarado" type="checkbox">Declarado</label>
        </div>
      </div>
    </div>
  </div>
  <div class="pull-right">
    <button type="button" id="btnGenerar" class="btn btn-success">Generar</button>
  </div>
  <br>
  <hr>
  <div class="panel panel-success">
    <div class="panel panel-heading">
      <div class="form-inline">
      <label class="">REGISTRO DE COMPRA CONTABLE </label>
      <!-- <input type="text" class="form-control"> -->
      </div>
    </div>
    <table id="tableRegMov" class="table table-bordered table-striped">
      <thead>
        <th># Movimiento</th>
        <th>FechaMov</th>
        <th># Tipo</th>
        <th>Tipo</th>
        <th>Serie</th>
        <th>Numero</th>
        <th>Proveedor</th>
        <th>SubTotal</th>
        <th>ISC</th>
        <th>IGV</th>
        <th>FLETE</th>
        <th>PERCEPCION</th>
        <th>MONEDA</th>
        <th>Total</th>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
<div class="">
  <div class="row">
    <div class="col-md-2 pull-right">
      <label class="">SubTotal (PEN)</label>
      <input type="text" value="0" class="form-control" id="txtSubTotal">
    </div>
    <div class="col-md-2 pull-right">
      <label class="">SubTotal (USD)</label>
      <input type="text" value="0" class="form-control" id="txtSubTotalUSD">
    </div>
  </div>
  <div class="row">
    <div class="col-md-2 pull-right">
      <label class="">IGV (PEN)</label>
      <input type="text" value="0" class="form-control" id="txtIGV">
    </div>
    <div class="col-md-2 pull-right">
      <label class="">IGV (USD)</label>
      <input type="text" value="0" class="form-control" id="txtIGVUSD">
    </div>
  </div>
  <div class="row">
    <div class="col-md-2 pull-right">
      <label class="">Total (PEN)</label>
      <input type="text" value="0" class="form-control" id="txtTotal">
    </div>
    <div class="col-md-2 pull-right">
      <label class="">Total (USD)</label>
      <input type="text" value="0" class="form-control" id="txtTotalUSD">
    </div>
  </div>
</div>
<?php include("footer.php"); ?>
</body>

<div class="modal fade" id="modalProductos" role="dialog">
  <div class="modal-dialog" style="width:900px">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Detalle</div>
      </div>
      <div class="modal-body">
        <div class="" style="overflow-x:auto;">


        <table id="tableProducto"  class="table table-bordered table-striped">
          <thead>
            <th>#</th>
            <th>Codigo Barra</th>
            <!--<th>Forma</th>-->
            <th>Marca</th>
            <th>Producto</th>
            <!-- <th>Precio Venta</th> -->
            <th>Medida</th>
            <th>Cantidad</th>
            <th>IGV?</th>
            <th>Precio</th>
            <th>Total</th>
          </thead>
          <tbody></tbody>
        </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" data-dismiss="modal" >Cerrar</button>
      </div>
    </div>
  </div>
</div>
</html>
