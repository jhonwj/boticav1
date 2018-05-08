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
//$limitProducto = $docVenta['LimiteItems'];
$limitProducto = 12;

$subtotal = 0;
$total = 0;
$igv = 0;
?>
<style>
  body {
    font-size: 16px;
    margin: 0;
    font-family: sans-serif;
    letter-spacing: 6px;

  }
  .container {
    margin-top: 10em;
    width: 1300px;
    margin-left: 25px;
  /*  border: 1px solid red;*/
  }
  td {
    line-height: 1em;
    font-size: 14px;
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
    width: 110px;
    margin-top: 1.5em;
  }
  .totales span {
    /*margin-left: 140px;*/
  }

  /* Estilos para productos */
  .productos {
  /*  height: 8em;*/
  }
  .productos {
    padding-top: 2.5em;
    box-sizing: border-box;
    margin-left: 1em;
  }
  .productos .cantidad {
    width: 70px;
    text-align: center;
  }.productos .pTotal,
  .productos .detalle span{
    margin-left: 5em;
  }
  .productos .precio {
    width: 180px;
  }
  .productos .pTotal {
    width: 110px;
  }
  .productos .pTotal span {
    margin-left: 10px;
  }

  .footer {
    width: 890px;
    float: left;
    margin-top: .5em;
  }
  .footer .son {
    margin-left: 8em;
  }
  .footer .user, .footer .fecha {
    margin-left: 9em;
  }
  .footer .small {
    display: block;
    font-size: 13px;
  }
  .footer .small:first-child {
    margin-top: 20px;
  }

  .fecha {
    margin-left: 2em;
  }
</style>
<div class="container">
  
  <table width="100%">
    <tr>
      <td>
        <span class="cliente"><?php echo $cliente; ?></span>
      </td>
      <td>
        <span class="fecha"><?php echo $docVenta['FechaDoc']; ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span class="cliente"><?php echo $direccion; ?></span>
      </td>
      <td class="ruc">

      </td>
    </tr>
    <tr>
        <td>
            <span class="cliente"><?php echo $dniRuc; ?></span>
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
        <span class="son"><?php echo strtoupper(NumerosEnLetras::convertir(number_format($total, 2),'SOLES',true, 'asd')); ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <!--<span class="small fecha"><?php echo $docVenta['FechaDoc'] ?></span>-->
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
