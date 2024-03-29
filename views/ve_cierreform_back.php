<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Cierre de caja</title>
    <?php include_once("linker.php"); ?>
    <style>
      #tableCierreDetallePagos td{
      }
    </style>
  </head>
  <script type="text/javascript">
    $(document).ready(function(e){
        ListarTablaCierre();
        CerrarCaja();

        $("#btnReporteExcelCierre").click(function(){
          window.location.href="ReporteExcelCierreCaja.php?fechaI="+$("#txtFechaI").val()+"&fechaF="+$("#txtFechaF").val();
        });
        $("#btnReporteExcelCierreDetalle").click(function(){
          window.location.href="ReporteExcelCierreCajaDet.php?fechaI="+$("#txtFechaI").val()+"&fechaF="+$("#txtFechaF").val()+
          "&salida="+$("#tableCierreDetalle").find("tr").eq(0).children("td").eq(1).text()+
          "&credito="+$("#tableCierreDetalle").find("tr").eq(1).children("td").eq(1).text()+
          "&contado="+$("#tableCierreDetalle").find("tr").eq(2).children("td").eq(1).text();
        });
    });

    function CerrarCaja(){
      $("#btnCierre").click(function(e){
        window.location.href="ve_cierreguardar.php";
      });
    }

    function ListarTablaCierre() {
      var xhr = $.ajax({
        url : "../controllers/server_processingCierre.php",
        type : "get",
        dataType : "html",
        success : function(res){
          console.log(JSON.parse(res));
          var fila = "";
          var sumTotales = 0;
          var sumEfectivo = 0;
          var sumVisa = 0;
          var sumMastercard = 0;
          var sumTotalCredito = 0;
          $.each(JSON.parse(res), function(data, value){
            fila = fila + "<tr><td>"+value.FechaDoc+"</td><td>"+value.TipoDoc+"</td><td>"+value.Serie+"</td><td>"+value.Numero+"</td><td>"+value.Total+"</td></tr>";
            sumTotales += parseFloat( value.Total);
            if (value.EsCredito == 1) {
              sumTotalCredito = sumTotalCredito + parseFloat(value.Total);
            }
            sumEfectivo += parseFloat(value.Efectivo);
            sumVisa += parseFloat(value.Visa);
            sumMastercard += parseFloat(value.Mastercard);

          });
          $("#tableCierre tbody").append(fila);
          $("#txtFechaI").val($("#tableCierre tbody").find("tr").eq(0).children("td").eq(0).text());
          $("#txtFechaF").val($("#tableCierre tbody").find("tr:last").eq(0).children("td").eq(0).text());
          $("#tableCierreDetalle").find("tr").eq(0).children("td").eq(1).text(sumTotales.toFixed(2));
          $("#tableCierreDetalle").find("tr").eq(1).children("td").eq(1).text(sumTotalCredito.toFixed(2));
          $("#tableCierreDetalle").find("tr").eq(2).children("td").eq(1).text((sumTotales - sumTotalCredito).toFixed(2));

          $("#tableCierreDetallePagos").find("tr").eq(0).children("td").eq(1).text(sumEfectivo.toFixed(2));
          $("#tableCierreDetallePagos").find("tr").eq(1).children("td").eq(1).text(sumVisa.toFixed(2));
          $("#tableCierreDetallePagos").find("tr").eq(2).children("td").eq(1).text(sumMastercard.toFixed(2));

        },
        error : function(errorThrown ,XMLHttpRequest, textStatus,){
          alert(errorThrown);
        }
      });
      console.log(xhr);
    }
  </script>
  <body>
    <?php include_once("header.php"); ?>

    <button type="button" class="btn btn-success" id="btnReporteExcelCierre" name="button">ReporteExcel</button>
    <div class="panel panel-success" style="margin-bottom:0">
      <div class="panel-heading">
        <div class="panel-body">
          <table id="tableCierreDetalle" class="table table-striped table-bordered" style="width: 50%">
            <tr>
              <td>Salida S/.</td>
              <td></td>
            </tr>
            <tr>
              <td>Credito</td>
              <td></td>
            </tr>
            <tr>
              <td>Contado</td>
              <td></td>
            </tr>
          </table>
          <table id="tableCierreDetallePagos" class="table table-striped table-bordered" style="width: 50%">
            <tr>
              <td>Efectivo </td>
              <td></td>
            </tr>
            <tr>
              <td>Visa</td>
              <td></td>
            </tr>
            <tr>
              <td>Mastercard</td>
              <td></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <button type="button" class="btn btn-success" id="btnReporteExcelCierreDetalle" name="button">ReporteExcelDet</button><br /><br />


      <button type="button" id="btnCierre" class="btn btn-success" name="button">Cierre de caja</button>
      <br>
      <div class="panel panel-success">
        <div class="panel-heading">Fecha Inicio :
          <input type="text" readonly  id="txtFechaI" name="" value="">
          &nbsp;
          - Fecha Fin :
          <input type="text" readonly  id="txtFechaF" name="" value="">
        </div>
        <div class="panel-body">
          <table id="tableCierre" class="table table-striped table-bordered">
            <thead>
              <th>Fecha</th>
              <th>TipoDoc</th>
              <th>Serie</th>
              <th>Numero</th>
              <th>Total</th>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>


    <?php include_once("footer.php"); ?>
  </body>
</html>
