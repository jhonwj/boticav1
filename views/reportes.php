<?php
include_once("../clases/helpers/Modal.php");
?>

<html>
<head>
	<title>Inventario</title>
</head>
<?php include_once 'linker.php'; ?>


<body>
<?php include("header.php"); ?>
<div class="container">
<div class="row">
<table class="table table-bordered table-striped">
  <tbody>
    <tr>
        <form action="/api/index.php/reporte/stockmensual" method="GET">
        <td>Reporte Mensual stock</td>
        <td>
            <div>
              <label>Stock Almacen: </label>
              <select name="idAlmacen">
                <option value="1" selected="">Principal</option>
              </select>
            </div>
            <label>Fecha Hasta: </label>
            <input name="fechaHasta" type="date" /><br />
        </td>
        <td>
          <button type="submit" class="btn btn-success">Excel</button>
          <!--<a class="btn btn-success" href="/api/index.php/reporte/ventaestrategicamensual">Excel</a>-->
        </td>
        </form>
    </tr>
  </tbody>
</table>
</div>
</div>
<script>
 
</script>
</body>
</html>

