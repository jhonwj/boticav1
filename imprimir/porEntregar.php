<?php
include_once("../controllers/NumerosEnLetras/NumerosEnLetras.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/views/validateUser.php');

$cliente = $docVentaDetEntre['Cliente'];
$dniRuc = $docVentaDetEntre['DniRuc'];
$fecha = $docVentaDetEntre['Fecha'];

$totalCant = 0;

?>
<style>
  * {
    font-size: 3mm;
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
    padding: 0 4mm;
    box-sizing: border-box;
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
  .small {
    font-size: 11px;
  }
</style>
<div class="container">

  <div class="">FECHA: <?php echo $fecha; ?></div>

  <div class="separar"></div>
  <div>SR(ES) : <?php echo $cliente ?></div>
  <div>RUC/DNI : <?php echo $dniRuc; ?></div>

  <br />

  <div class="productos">
    <table width="100%">
      <thead>
        <tr>
          <th class="cantidad">CANT</th>
          <th class="producto">PRODUCTO</th>
          <th class="unitario text-right">PRECIO U.</th>
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
            <td class="precio text-right">
              <span>S/.<?php echo $producto['Precio'] ?></span>
            </td>
          </tr>
          <?php
          $filas += 1;
          // $totalDescuento += floatval($producto['Descuento']);
          // $total += floatval($producto['TOTAL']);
          $totalCant += $producto['Cantidad'];
        }
        ?>
        
      </tbody>
    </table>
    <br>
    <table>
      <tr>
        <td><b>TOTAL</b></td>
        <td><?php echo $totalCant; ?> Unidades</td>
      </tr>
    </table>

  </div>

</div>
