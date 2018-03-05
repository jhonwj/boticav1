<?php
//var_dump($docVenta);
$emision = strtotime($docVenta['FechaDoc']);
$day = date("d", $emision);
$mes = date("m", $emision);
$meses = array(
  '01' => 'Enero',
  '02' => 'Febrero',
  '03' => 'Marzo',
  '04' => 'Abril',
  '05' => 'Mayo',
  '06' => 'Junio',
  '07' => 'Julio',
  '08' => 'Agosto',
  '09' => 'Septiembre',
  '10' => 'Octubre',
  '11' => 'Noviembre',
  '12' =>'Diciembre'
);
$lastAnio = substr(date("Y", $emision), -1);
$cliente = strtoupper($docVenta['Cliente']);
$direccion = strtoupper($docVenta['Direccion']);
$dniRuc = $docVenta['DniRuc'];
$tieneIgv = $docVenta['TieneIgv'];
$limitProducto = $docVenta['LimiteItems'];
$docVentaNro = str_pad( $docVenta['Numero'], 8, "0", STR_PAD_LEFT);
$fechaDoc = $docVenta['FechaDoc'];
$serieMaq = $docVenta['SerieImpresora'];

$subtotal = 0;
$total = 0;
$igv = 0;
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
  .small {
    font-size: 11px;
  }
</style>
<div class="container">
  <!--<div class="center">
    <img width="80px" src="../resources/images/delmancito.jpg"  /><br /><br />
  </div>-->
  <div class="center">BOTICA</div>
  <div class="center">BOTICA DELMAN S.A.C</div>
  <div class="center">RUC: 20393999544 </div>
  <br />
  <div class="">TICKET NRO: <?php echo $docVentaNro; ?></div>
  <div class="">FECHA: <?php echo $fechaDoc; ?></div>
  <div class="">SERIE MAQ REG : <?php echo $serieMaq; ?></div>

  <div class="separar"></div>
  <div>SR(ES) : <?php echo $cliente ?></div>
  <div>RUC/DNI : <?php echo $dniRuc; ?></div>
  <div>DIR : <?php echo $direccion; ?></div>

  <br />

  <div class="productos">
    <table width="100%">
      <thead>
        <tr>
          <th class="cantidad">CANT</th>
          <th class="producto">PRODUCTO</th>
          <!--<th class="unitario">P/U</th>-->
          <th class="precio text-right">TOT</th>
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
            <!--<td class="precio">
              <span>S/.<?php echo $producto['Precio'] ?></span>
            </td>-->
            <td class="text-right">
              <span>S/.<?php echo $producto['TOTAL'] ?></span>
            </td>
          </tr>
          <?php
          $filas += 1;
          $total += floatval($producto['TOTAL']);
        }

        if ($tieneIgv) {
          $subtotal = $total / 1.18;
          $igv = $total - $subtotal;
        } else {
          $igv = '0.00';
          $subtotal = $total;
        }
        ?>
        <tr>
          <td></td>
          <td class="text-right">SUBTOTAL</td>
          <td class="text-right">S/.<?php echo number_format($subtotal, 2); ?></td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right">IGV</td>
          <td class="text-right">S/.<?php echo number_format($igv, 2); ?></td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right"><strong>TOTAL</strong></td>
          <td class="text-right">S/.<?php echo number_format($total, 2); ?></td>
        </tr>
      </tbody>
    </table>
  </div>
  <br />
  <div class="center small">BIENES TRANSFERIDOS EN LA AMAZONIA</div>
  <div class="center small">PARA SER CONSUMIDOS EN LA MISMA</div>

</div>
