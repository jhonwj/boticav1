<?php
var_dump($docVenta);
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
$docVentaNro = $docVenta['Numero'];
$fechaDoc = $docVenta['FechaDoc'];
$serieMaq = $docVenta['SerieImpresora'];

$subtotal = 0;
$total = 0;
$igv = 0;
?>
<style>
  body {
    font-size: 22px;
    margin: 0;
    font-family: sans-serif;

  }
  .container {
    margin-top: 7.5em;
    width: 400px;
    margin-left: 20px;
    border: 1px solid red;
  }
  .center {
    text-align: center;
  }
  .separar {
    padding: .2em;
    border-top: 1px dashed #000;
    border-bottom: 1px dashed #000;
  }
  td {
    line-height: 1.48em;
    font-size: 1em;
    border: 1px solid black;
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
    margin-left: 200px;
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
</style>
<div class="container">
  <div class="center">BOTICA</div>
  <div class="center">BOTICA DELMAN S.A.C</div>
  <div class="center">RUC: 20393999544 </div>
  <div class="center">TICKET Nro: <?php echo $docVentaNro; ?></div>
  <div class="center">FECHA HORA: <?php echo $fechaDoc; ?></div>
  <div class="center">SERIE MAQ REG : <?php echo $serieMaq; ?></div>
  <div class="separar"></div>

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
              <span><?php echo $producto['Producto'] ?></span>
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
