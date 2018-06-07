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
      url: "../controllers/listarRegMov.php",
      type: "get",
      data: {fechaIni : $("#fechaIni").val(), fechaFin: $("#fechaFinal").val(), declarado: $("#declarado").prop("checked")},
      dataType: "html",
      success: function(res){
        var respuesta = JSON.parse(res);
        var tableBody = "";
        $.each(respuesta, function(data, value){
          var btnImprimir = ''
          if (value.Tipo == "1" || value.Tipo == "2") {
            btnImprimir = "<a class='btn' href='/imprimir/index.php?hashMovimiento="+ value.IdMovimiento +"'><i class='fa fa-print'></i></a>"
          }

          console.log(data)
          console.log(value)
          tableBody = tableBody + "<tr data-periodo='"+value.FechaPeriodoTributario+"'><td>"+value.IdMovimiento+"</td><td>"+value.MovimientoFecha+"</td><td>"+value.IdMovimientoTipo+"</td><td>"+value.TipoMovimiento+"</td><td>"+value.Serie+"</td><td>"+value.Numero+"</td><td>"+value.Proveedor+
          "</td><td>"+value.SUBTOTAL+
          "</td><td>"+value.ISC+
          "</td><td>"+value.IGV+
          "</td><td>"+value.FLETE+
          "</td><td>"+value.Percepcion+
          "</td><td>"+(value.Moneda || '')+
          "</td><td>"+(parseFloat(value.TOTAL).toFixed(2) || 0)+
          "</td><td><a class='btn' onclick='EliminarRegMov("+ value.IdMovimiento +");'><i class='fa fa-trash'></i></a>"+ btnImprimir
          "</td></tr>" ;
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
    window.location.href="ReporteExcel6.php?fechaIni="+$("#fechaIni").val()+"&fechaFin="+$("#fechaFinal").val()+"&declarado="+$("#declarado").prop("checked");
  });

$("#tableRegMov tbody").on("click", "tr", function(e){
    $("#tableProducto tbody tr").remove();
  var idMov = $(this).children("td").eq(0).html();
  var periodoTributario = $(this).data('periodo');
  $('#periodoTributario').val(periodoTributario)
  $('#periodoTributario').data('idMov', idMov)

   var xhr = $.ajax({
    url: '../controllers/serverprocessingProductosRegMov.php',
    type: 'get',
    data:  {"idMov" : idMov},
    dataType: 'html',
    success: function(respuesta){
        var response = JSON.parse(respuesta);
        console.log(response)
        
        $('#verCodigoBarra').attr('href', '/views/codigoBarra.php?hash=' + response[0].hashMovimiento);

        var fila = "";
        $.each(response, function(data, value){
          var total = value.Precio*value.Cantidad;
          var igv = "";
          if (value.TieneIgv == null || value.TieneIgv =="0") {
           igv = 0
          }else{
           igv = total * 0.18;
          }
          var totalGeneral = parseFloat(total) + parseFloat(igv) + parseFloat(value.ISC)

          fila = "<tr><td>"+value.hashMovimiento+
          "</td><td>"+value.Codigo+
          "</td><td>"+value.ProductoFormaFarmaceutica+
          "</td><td>"+value.ProductoMarca+
          "</td><td>"+value.Producto+
          // "</td><td>"+value.PrecioContado+
          "</td><td>"+value.ProductoMedicion+
          "</td><td>"+value.Cantidad+
          "</td><td>"+igv+
          "</td><td>"+value.ISC+
          "</td><td>"+value.Precio+
          "</td><td>"+totalGeneral.toFixed(2)+
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

  $("#modalProductos").on('show.bs.modal', function (e) {
      $("#periodoTributario").on("keyup paste input change",function(){
          if ($(this).val().length==6) {
              var sub = $(this).val().substr(4,2);
              if (sub>12) {
                    $(this).val(parseInt(parseInt($(this).val().substr(0,4)) + 1).toString() + "01");
              }else if (sub<1) {
                  $(this).val(parseInt(parseInt($(this).val().substr(0,4)) - 1).toString() + "12");
              }
          }
      });
  })

});




function EliminarRegMov(idMovimiento){

var r = confirm("Estas seguro que desea eliminar el movimiento?");
if (r == true) {
  var xhr =  $.ajax({
    url: '../controllers/EliminarMovimiento.php',
    type: 'get',
    data:  {"idMov" : idMovimiento},
    dataType: 'html',
    success : function(res){
      $("#modalProductos").modal("hide");
      $("#btnGenerar").trigger("click");
      alert(res);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    }
  });
}
}

function SumarTotalIgvSub(){
  $("#tableRegMov tbody").each(function(e){

  });
}

</script>

<body>
<?php include("header.php"); ?>
<div class="fab2">
  <button id="" class="btn btn-success" onclick="exportarTXT($('#tableRegMov'))"><i class="fa fa-file"></i>.txt</button>
  <button id="btnExcel" class="btn btn-success"><i class="fa fa-file-excel-o"></i>.xls</button>
</div>

<div class="bt-panel">
  <div class="container center_div" >
    <div class="row">
      <div class="col-md-4 form-group">
        <label>Fecha Inicio</label>
        <input type="date" id="fechaIni" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 form-group">
        <label>Fecha Final</label>
        <input type="date" id="fechaFinal" class="form-control">
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
      <label class="">REGISTRO DE MOVIMIENTO </label>
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

            <div class="form-group">
                <label for="periodoTributario">Periodo Tributario: </label>
                <span  class="help-block">El valor debe ser AAAAMM</span>
                <div class="form-inline">
                    <input type="number" max="999999" class="form-control txt-tributario" id="periodoTributario">
                    <button class="btn btn-success"
                        onclick="actualizarRegistro({
                            tabla: 'Lo_Movimiento',
                            campos: {
                                'FechaPeriodoTributario': $('#periodoTributario').val()
                            },
                            where: ['Hash', '=', $('#periodoTributario').data('idMov')],
                            mensaje: {
                                'success': 'Se actualizo el periodo tributario correctamente',
                                'Error': 'No se ha podido actualizar el Periodo Tributario'
                            },
                            controller: 'server_processingMovimiento.php'
                        })">
                        Actualizar Periodo
                    </button>
                </div>
            </div>
        <br />
        <table id="tableProducto"  class="table table-bordered table-striped">
          <thead>
            <th>#</th>
            <th>Codigo</th>
            <th>Forma</th>
            <th>Laboratorio</th>
            <th>Producto</th>
            <!-- <th>Precio Venta</th> -->
            <th>Medida</th>
            <th>Cantidad</th>
            <th>IGV</th>
            <th>ISC</th>
            <th>Precio</th>
            <th>Total</th>
          </thead>
          <tbody></tbody>
        </table>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-warning" target="_blank" id="verCodigoBarra">Codigo de barras</a>
        <button class="btn btn-success" data-dismiss="modal" >Cerrar</button>
      </div>
    </div>
  </div>
</div>
</html>
