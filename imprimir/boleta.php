<?php
include_once($_SERVER["DOCUMENT_ROOT"] . '/views/validateUser.php');

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
$cliente = mb_strimwidth(strtoupper($docVenta['Cliente']), 0, 40, '...');
$direccion = mb_strimwidth(strtoupper($docVenta['Direccion']), 0, 40,'...');
$dniRuc = $docVenta['DniRuc'];
$tieneIgv = $docVenta['TieneIgv'];
$limitProducto = $docVenta['LimiteItems'];

$subtotal = 0;
$total = 0;
$igv = 0;
?>
<style>
  body {
    font-size: 18px;
    margin: 0;
    font-family: sans-serif;
    letter-spacing: 4px;
  }
  .container {
    margin-top: 7.2em;
    width: 900px;
    margin-left: 14px;
    /*border: 1px solid red;*/
  }
  td {
    line-height: 1em;
    font-size: 1em;
    /*border: 1px solid black;*/
  }
  .emision {
    width: 690px;
  }
  .dia {
    margin-left: 220px;
  }
  .mes {
    width: 300px;
  }
  .mes span {
    margin-left: 10px;
  }
  .anio {
    width: 50px;

  }
  .anio span {
    margin-left: 10px;
  }
  .cliente {
    margin-left: 100px;
  }
  .ruc {
    width: 230px;
    position: relative;
  }
  .ruc span {
    margin-left: 80px;
    position: absolute;
    top: 0;
    width: 260px;
  }
  .fecha span {
    margin-left: 40px;
  }
  .totales {
    float: right;
    width: 150px;
  }
  .totales span {
    margin-left: 10px;

  }

  /* Estilos para productos */
  .productos {
  /*  height: 8em;*/
  }
  .productos {
    padding-top: 1.2em;
    box-sizing: border-box;
  }
  .productos .cantidad {
    width: 70px;
    text-align: center;
  }
  .productos .detalle span{
    margin-left: 20px;
  }
  .productos .precio {
    width: 100px;
  }
  .productos .pTotal {
    width: 150px;
  }
  .productos .pTotal span {
    margin-left: 10px;
  }
  .footer {
    width: 750px;
    float: left;
  }
  .footer .small {
    display: block;
    font-size: 13px;
  }
  .footer .user {
    margin-top: 10px;
    margin-left: 20px;
  }
</style>
<div class="container">
  <table width="100%">
    <tr>
      <td>
        <span class="cliente"><?php echo $cliente; ?></span>
      </td>
      <td class="ruc">
        <span><?php echo $dniRuc; ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span class="cliente"><?php echo $direccion; ?></span>
      </td>
      <td class="ruc fecha">
        <span><?php echo $docVenta['FechaDoc']; ?></span>
      </td>
    </tr>
  </table>
  <div class="productos">
    <table width="100%">
      <?php
      $filas = 0;
      foreach ($productos as $key => $producto) { ?>
          <tr>
            <td class="cantidad">
              <span><?php echo $producto['Cantidad']; ?></span>
            </td>
            <td class="detalle">
              <span><?php echo mb_strimwidth($producto['Producto'], 0, 38, '...') ?></span>
            </td>
            <td class="precio">
              <span><?php echo $producto['Precio'] ?></span>
            </td>
            <td class="pTotal">
              <span><?php echo $producto['TOTAL'] ?></span>
            </td>
          </tr>
      <?php
      $filas += 1;
        $total += floatval($producto['TOTAL']);
      }

      while ($filas < $limitProducto) {
      ?>
      <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
      <?php
        $filas += 1;
      }

      if ($tieneIgv) {
        $subtotal = $total / 1.18;
        $igv = $total - $subtotal;
      } else {
        $igv = '0.00';
        $subtotal = $total;
      }
     ?>
    </table>
  </div>

  <table class="footer">
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td class="small user"><?php echo strtoupper($_SESSION['user']); ?></td>
      </tr>
  </table>

  <table class="totales">
    <!--<tr>
      <td>
        <span><?php echo number_format($subtotal, 2); ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span><?php echo number_format($igv, 2); ?></span>
      </td>
    </tr>-->
    <tr>
      <td>
        <span><?php echo number_format($total, 2); ?></span>
      </td>
    </tr>
  </table>
</div>
