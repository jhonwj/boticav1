<?php
include_once("../controllers/NumerosEnLetras/NumerosEnLetras.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/views/validateUser.php');

$emision = strtotime($movimiento['MovimientoFecha']);
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
$cliente = strtoupper($movimiento['Proveedor']);
$dniRuc = $movimiento['Ruc'];
$docVentaNro = $movimiento['Serie'] . ' - ' . str_pad( $movimiento['Numero'], 8, "0", STR_PAD_LEFT);
$fechaDoc = $movimiento['MovimientoFecha'];
$tipoDoc = $movimiento['MovimientoTipo'];

$subtotal = 0;
$total = 0;
$totalDescuento = 0;
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

  .center, .precio {
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
  <div class="center"><img src="/resources/images/logo-ticket.png" style="max-width:100%; width: 40mm"/></div><br>
  <div class="center">FERRETERIA BRIANNA</div>
  <div class="center">RUC: 10768688422 </div>
  <br />

  <div class="separar"></div>
  
  <?php if ($tipoDoc == 'TICKET BOLETA' || $tipoDoc == 'TICKET FACTURA'): ?>
    <div class="strike">
      <span><?php echo str_replace('TICKET ', '', $tipoDoc); ?></span>
    </div>
    <br />
  <?php endif; ?>
  
  <div class="">MOVIMIENTO NRO: <?php echo $docVentaNro; ?></div>
  <div class="">FECHA: <?php echo $fechaDoc; ?></div>
  <div class=""><?php echo $movimiento['TipoMovimiento']; ?></div>

  <div class="separar"></div>
  <!-- <div>SR(ES) : <?php echo $cliente ?></div>
  <div>RUC/DNI : <?php echo $dniRuc; ?></div>
  <div>DIR : <?php echo $direccion; ?></div>
  -->
  <br />

  <div class="productos">
    <table width="100%">
      <thead>
        <tr>
          <th class="cantidad">CANT</th>
          <th class="producto">PRODUCTO</th>
          <th class="unitario">P/U</th>
          <th class="precio text-right">TOT</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $filas = 0;
        $tieneIgv = false;

foreach ($movimientoDet as $key => $producto) { 
        $tot = $producto['Cantidad'] * $producto['Precio'];
?>
          <tr>
            <td class="cantidad">
              <span><?php echo $producto['Cantidad']; ?></span>
            </td>
            <td class="producto">
              <span><?php echo $producto['Producto'] ?></span>
            </td>
            <td class="precio">
              <span><?php echo $movimiento['Moneda'] . ' ' . $producto['Precio'] ?></span>
            </td>
            <td class="text-right">
              <span> <?php echo $movimiento['Moneda'] . ' ' . $tot ?></span>
            </td>
          </tr>
          <?php
          $filas += 1;
          $totalDescuento += floatval($producto['Descuento']);
          $total += floatval($tot);
        }

        if ($tieneIgv) {
          $subtotal = $total / 1.18;
          $igv = $total - $subtotal;
        } else {
          $igv = '0.00';
          $subtotal = $total;
        }
        ?>
        <!--<tr>
          <td></td>
          <td></td>
          <td class="text-right">SUBTOTAL</td>
          <td class="text-right">S/.<?php echo number_format($subtotal, 2); ?></td>
        </tr>-->
        <!--<tr>
          <td></td>
          <td class="text-right">IGV</td>
          <td class="text-right">S/.<?php echo number_format($igv, 2); ?></td>
        </tr>-->
        <?php if ($totalDescuento > 0) : ?>
          <tr>
            <td></td>
            <td></td>
            <td colspan="2" class="text-right">DESCUENTO</td>
            <td class="text-right">- <?php echo $movimiento['Moneda'] . ' ' .  number_format($totalDescuento, 2); ?></td>
          </tr>
        <?php endif; ?>
        <tr>
          <td></td>
          <td></td>
          <td class="text-right"><strong>TOTAL</strong></td>
          <td class="text-right"><?php echo $movimiento['Moneda'] . ' ' .  number_format($total, 2); ?></td>
        </tr>
      </tbody>
    </table>
    <br />
    <div style="text-transform: uppercase;">
        Usuario: <?= $movimiento['UsuarioReg'] ?>
    </div>
    <!--<span class="son">SON: <?php echo strtoupper(NumerosEnLetras::convertir(number_format($total - $totalDescuento, 2, '.', ''),'SOLES',true, 'asd')); ?></span>
    <?php if ($docVenta['PagoCon'] > 0) : ?>
      <span>PAGÓ CON: S/. <?php echo number_format($docVenta['PagoCon'], 2); ?></span><br />
      <span>VUELTO: S/.<?php echo number_format($docVenta['PagoCon'] - $total, 2) ?></span>
    <?php endif; ?>-->

  </div>
  <br />
  <!--<div class="center small">BIENES TRANSFERIDOS EN LA AMAZONIA</div>
  <div class="center small">PARA SER CONSUMIDOS EN LA MISMA</div>-->
  <!--<div class="center small"><strong>Salida la mercancía no hay lugar a reclamos<br>
  Todo cambio con su respectivo comprobante dentro de las 48hrs<br>
  Gracias por su compra</strong></div>
  <div class="center small">.</div>-->
  <br />
  <br />

</div>
