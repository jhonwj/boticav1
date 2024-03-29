<?php
include_once("../controllers/NumerosEnLetras/NumerosEnLetras.php");
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
$cliente = mb_strimwidth(strtoupper($docVenta['Cliente']), 0, 50, '...');
$direccion = mb_strimwidth(strtoupper($docVenta['Direccion']), 0, 50, '...');
$dniRuc = $docVenta['DniRuc'];
$tieneIgv = $docVenta['TieneIgv'];
$limitProducto = $docVenta['LimiteItems'];

$subtotal = 0;
$total = 0;
$igv = 0;
?>
<style>
  body {
    font-size: 22px;
    margin: 0;
    font-family: sans-serif;
    letter-spacing: 4px;

  }
  .container {
    margin-top: 7.7em;
    width: 1200px;
    margin-left: 25px;
  /*  border: 1px solid red;*/
  }
  td {
    line-height: 1.48em;
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
    margin-left: 15px;
  }
  .cliente {
    margin-left: 140px;
  }
  .ruc {
    width: 320px;
  }
  .ruc span {
    margin-left: 60px;
  }
  .totales {
    float: right;
    width: 310px;
  }
  .totales span {
    margin-left: 140px;
  }

  /* Estilos para productos */
  .productos {
  /*  height: 8em;*/
  }
  .productos {
    padding-top: 1em;
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
    width: 174px;
  }
  .productos .pTotal span {
    margin-left: 10px;
  }

  .footer {
    width: 890px;
    float: left;
    margin-top: 5px;
  }
  .footer .son {
    margin-left: 80px;
  }
  .footer .user, .footer .fecha {
    margin-left: 230px;
  }
  .footer .small {
    display: block;
    font-size: 13px;
  }
  .footer .small:first-child {
    margin-top: 20px;
  }
</style>
<div class="container">
  <div class="emision">
    <table width="100%">
      <tr>
        <td>
          <span class="dia"><?php echo $day ?></span>
        </td>
        <td class="mes">
          <span><?php echo  strtoupper($meses[$mes]) ?></span>
        </td>
        <td class="anio">
          <span ><?php echo $lastAnio; ?></span>
        </td>
      </tr>
    </table>
  </div>
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
      <td class="ruc">

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
              <span><?php echo mb_strimwidth($producto['Producto'], 0, 60, '...') ?></span>
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
      <td>
        <span class="son"><?php echo strtoupper(NumerosEnLetras::convertir(number_format($total, 2, '.', ''),'SOLES',true, 'asd')); ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span class="small fecha"><?php echo $docVenta['FechaDoc'] ?></span>
        <span class="small user"><?php echo strtoupper($_SESSION['user']); ?></span>
      </td>
    </tr>
  </table>

  <table class="totales">
    <tr>
      <td>
        <span><?php echo number_format($subtotal, 2); ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span><?php echo number_format($igv, 2); ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span><?php echo number_format($total, 2); ?></span>
      </td>
    </tr>
  </table>
</div>
