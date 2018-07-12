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
$anio = date("Y", $emision);
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
    margin-top: 52mm;
    width: 190mm;
  /*  border: 1px solid red;*/
  }
  td {
    line-height: 3.5mm;
    letter-spacing: 0.5mm;
    font-size: 3.5mm;
    font-weight: bold;
    /*border: 1px solid black;*/
  }
  .emision {
    width: 690px;
  }
  .dia span {
    margin-left: 13mm;
  }
  .dia {
    width: 14mm;
    text-align:center;
  }
  .mes {
    width: 13mm;
    text-align:center;
  }
  .mes span {
  }
  .anio {
    /*width: 50px;*/
    width: 12mm;
    text-align:center;
  }
  .anio span {
  }
  .cliente {
    margin-left: 18mm;
  }
  .ruc {
    /*width: 320px;*/
  }
  .ruc span {
    margin-left: 12mm;
  }
  .totales {
      text-align: right;
    float: right;
    width: 18mm;
  }
  .totales span {
    margin-right: 5mm;
  }
  .totales td {
      line-height: 6mm;
  }

  /* Estilos para productos */
  .productos {
    height: 58mm
  }
  .productos {
    padding-top: 7mm;
    box-sizing: border-box;
    margin-left: -2mm;
  }
  .productos .cantidad {
    width: 16mm;
    text-align: center;
  }
  .productos .detalle span{
    margin-left: 2mm;
  }
  .productos .precio {
    width: 20mm;
  }
  .productos .pTotal {
    width: 18mm;
  }
  .productos .pTotal span {
    margin-left: 10px;
  }

  .footer {
    width: 160mm;
    float: left;
  }
  .footer .son {
    margin-left: 80px;
  }
  .footer .user, .footer .fecha {
    margin-left: 10mm;
  }
  .footer .small {
    display: block;
    font-size: 13px;
  }
  .footer .small:first-child {
    margin-top: 16mm;
  }
  .arriba td {
      line-height: 6mm;
  }
</style>
<div class="container">
    <div class="arriba">
    <div class="emision">
        <table>
            <tr>
                <td class="dia">
                <span ><?php echo $day ?></span>
                </td>
                <td class="mes">
                <span><?php echo  $mes ?></span>
                </td>
                <td class="anio">
                <span ><?php echo $anio; ?></span>
                </td>
                <td class="ruc">
                    <span><?php echo $dniRuc; ?></span>
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
                
            </td>
            </tr>
            <tr>
            <td>
                <span class="cliente"><?php echo $direccion; ?></span>
            </td>
            <td>

            </td>
            </tr>
        </table>
    </div>
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
