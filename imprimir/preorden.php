<?php
include_once('../views/validateUser.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/clases/BnGeneral.php");

$id = $_GET['id'];

$result = fn_devolverPreOrden($id);
$preOrden = mysqli_fetch_assoc($result);

$resultDetalle = fn_listarProductosPreOrden($id);
$productos = mysqli_fetch_all($resultDetalle, MYSQLI_ASSOC);

?>

<style>
  * {
    font-size: 13px;
    font-family: sans-serif;
  }
  body {
    display: block;
    margin: 8px;
  }
  td,
  th,
  tr,
  table {
    border-top: 1px solid black;
    border-collapse: collapse;
  }
  .container {
    width: 100%;
    max-width: 100%;
  }

  .center {
    text-align: center;
  }
  .separar {
    margin: 8px 0;
    border-top: 1px dashed #000;

  }
  td {
    line-height: 1.48em;
    font-size: 1em;
  }
  td, th {
    padding: 0 6px;
  }
  td.cantidad {
    text-align: center;
  }
  .text-right {
    text-align: right;
  }
  .float-right {
      float: right;
  }
  .small {
    font-size: 11px;
  }
  .firma {
      padding: 10px 30px;
      border-top: 1px dashed #ccc;
  }
</style>

<div class="container">
  <div class="center">
    <img src="/resources/images/logo-ticket.png" style="max-width:100%; width: 30mm"/><br /><br />
  </div>
  <div class="center">ORDEN DE PEDIDO</div>
  <br />
  
  <div class="separar"></div>
  <div>CLIENTE: <?php echo $preOrden['Cliente']; ?></div>
  <div>DNI/RUC: <?php echo $preOrden['DniRuc']; ?></div>
  <div>FECHA: <?php echo $preOrden['FechaReg']; ?></div>

  <div class="separar"></div>
  <br />

<div>
  <table width="100%">
      <thead>
        <tr>
          <th class="cantidad">CANT</th>
          <th class="producto">PRODUCTO</th>
          <th class="unitario">P/U</th>
          <th class="precio text-right">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $filas = 0;
        foreach ($productos as $key => $producto) { ?>
          <tr>
            <td class="cantidad">
              <span><?php echo $producto['Cantidad']; ?></span>
            </td>
            <td class="producto">
              <span><?php echo $producto['Producto'] ?></span>
            </td>
            <td class="precio center">
              <span>S/.<?php echo $producto['Precio'] ?></span>
            </td>
            <td class="text-right">
              <span>S/.<?php echo $producto['Cantidad'] * $producto['Precio'] ?></span>
            </td>
          </tr>
          <?php
          $total += floatval($producto['Cantidad'] * $producto['Precio']);
        }
        ?>
        <tr>
          <td></td>
          <td></td>
          <td class="text-right"><strong>TOTAL</strong></td>
          <td class="text-right">S/.<?php echo number_format($total, 2); ?></td>
        </tr>
      </tbody>
    </table>
</div>
    <br><br>
  </div>

