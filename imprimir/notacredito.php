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
$cliente = strtoupper($docVenta['Cliente']);
// $direccion = strtoupper($docVenta['Direccion']);
$dniRuc = $docVenta['DniRuc'];
$tieneIgv = $docVenta['TieneIgv'];
$limitProducto = $docVenta['LimiteItems'];
$docVentaNro = $docVenta['Serie'] . ' - ' . str_pad( $docVenta['Numero'], 8, "0", STR_PAD_LEFT);
$fechaDoc = $docVenta['FechaDoc'];
$fecha = date("Y-m-d", strtotime($docVenta['FechaDoc']));
$serieMaq = $docVenta['SerieImpresora'];
$tipoDoc = $docVenta['TipoDoc'];
$docModifica = $docVenta['NroComprobanteModifica'];
$notaDescMotivo = $docVenta['NotaDescMotivo'];
$codSunatModifica = $docVenta['CodSunatModifica'];
$notaIdMotivo = $docVenta['NotaIdMotivo'];
$subtotal = 0;
$total = 0;
$totalDescuento = 0;
$igv = 0;
$tipoDocModifica = $codSunatModifica=='01'? '01 - FACTURA ELECTRÓNICA':'03 - BOLETA ELECTRÓNICA';

$comprobantemod = explode('-', $docModifica);
$seriemod = $comprobantemod[0];
$numeromod = $comprobantemod[1];



if ($docVenta['CampoDireccion']) {
  $direccion = strtoupper($docVenta[$docVenta['CampoDireccion']]);
} else {
  $direccion = strtoupper($docVenta['Direccion']);
}

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
  <!--<div class="center">
    <img width="80px" src="../resources/images/delmancito.jpg"  /><br /><br />
  </div>-->
 <div class="center"><img src="/resources/images/logo-ticket.png" style="max-width:100%; width: 40mm"/></div><br>
  <div class="center"><b>INVERSIONES Y AFINES CUSTODIO E.I.R.L.</b></div>
  <div class="center"><b>RUC:20394084221</b> </div><br>
  <!--<div class="center small">VENTA DE ....</div>-->

  <div class="">NOTA DE CREDITO NRO: <?php echo $docVentaNro; ?></div>
  <div class="">FECHA: <?php echo $fechaDoc; ?></div>
  <!-- <div class="">SERIE MAQ REG : <?php echo $serieMaq; ?></div> -->
  <div class="separar"></div>
  <div>DOCUMENTO QUE MOFIDICA:</div>
  <div>TIPO DOCUMENTO:  <?php echo $tipoDocModifica ?></div>
  <div>NUMERO: <?php echo $numeromod; ?></div>
  <div>MOTIVO: <?php echo $notaDescMotivo." - ".$notaIdMotivo ?></div>
  
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
          <th class="unitario">P/U</th>
          <th class="precio text-right">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sumManoDeObra = 0;
        foreach ($productos as $key => $producto) {
          if ($producto['EsManoDeObra']) {
            $sumManoDeObra += $producto['TOTAL'];
          }
        }

        $filas = 0;
        foreach ($productos as $key => $producto) { ?>
          <?php if (empty($producto['EsManoDeObra'])) : ?>
            <tr>
              <td class="cantidad">
                <span><?php echo $producto['Cantidad']; ?></span>
              </td>
              <?php
                  $producto['Descripcion'] ='';
                  if (!empty($producto['ProductoDesc'])) $producto['Descripcion'] .= $producto['ProductoDesc'];
                  if (!empty($producto['ProductoDesc2'])) $producto['Descripcion'] .= $producto['ProductoDesc2'];
                  if (!empty($producto['ProductoDesc3'])) $producto['Descripcion'] .= $producto['ProductoDesc3'];
                ?>
              <td class="producto">
                <?php if($producto['Descripcion']) : ?>
                <span>
                <?php echo $producto['Producto'] . ' (' . $producto['Descripcion'] . ') ' ?>
                </span>
                <?php else : ?>
                  <?php echo $producto['Producto'] . ' (' . $producto['ProductoMedicion'] . ') ' ?>
                <?php endif; ?>
              </td>
              <td class="precio" style="text-align: center;">
                <span>S/.<?php echo $producto['Precio'] ?></span>
              </td>
              <td class="text-right">
                <?php 
                  if ($producto['CodigoBarra'] === "MANODEOBRA") {
                    $producto['TOTAL'] = $producto['TOTAL'] + $sumManoDeObra;
                  }
                ?>
                <span>S/.<?php echo $producto['TOTAL']; ?></span>
              </td>
            </tr>
            <?php
            $filas += 1;
            $totalDescuento += floatval($producto['Descuento']);
            $total += floatval($producto['TOTAL']);
          
          endif;
        }

        if ($tieneIgv) {
          // $subtotal = $total - ($total * 0.18);
          $subtotal = $total / 1.18;
          $igv = $total - $subtotal;
        } else {
          $igv = '0.00';
          $subtotal = $total;
        }
        ?>
        <tr>
          <td></td><td></td>
          <td class="text-right">SUBTOTAL</td>
          <td class="text-right">S/.<?php echo number_format($subtotal, 2); ?></td>
        </tr>
        <?php if ($igv > 0) : ?>
          <tr>
            <td></td>
            <td class="text-right">IGV</td>
            <td class="text-right">S/.<?php echo number_format($igv, 2); ?></td>
          </tr>
        <?php endif; ?>
        <?php if ($totalDescuento > 0) : ?>
          <tr>
            <td></td>
            <td class="text-right">DESCUENTO</td>
            <td class="text-right">- S/.<?php echo number_format($totalDescuento, 2); ?></td>
          </tr>
        <?php endif; ?>
        <tr>
          <td></td><td></td>
          <td class="text-right"><strong>TOTAL</strong></td>
          <td class="text-right">S/.<?php echo number_format($total - $totalDescuento, 2); ?></td>
        </tr>
      </tbody>
    </table>
    <?php if ($docVenta['Puntos']): ?>
      <span style="text-transform: uppercase">Usted tiene <b><?php echo $docVenta['Puntos']?></b> Puntos</span><br>
    <?php endif; ?>
    <span class="son">SON: <?php echo strtoupper(NumerosEnLetras::convertir(number_format($total - $totalDescuento, 2, '.', ''),'SOLES',true, 'asd')); ?></span><br/>
    <span style="text-transform: uppercase">VENDEDOR: <?php echo $docVenta['UsuarioReg']; ?></span><br>
    
    <?php if ($docVenta['PagoCon'] > 0) : ?>
      <span>PAGÓ CON: S/. <?php echo number_format($docVenta['PagoCon'], 2); ?></span><br />
      <span>VUELTO: S/.<?php echo number_format($docVenta['PagoCon'] - $total, 2) ?></span>
    <?php endif; ?>

  </div>
  <br />
  <div class="center">
        <div class="center small">BIENES TRANSFERIDOS EN LA AMAZONÍA REGIÓN</div>
        <div class="center small"> SELVA PARA SER CONSUMIDOS EN LA MISMA</div>
        <br />
        <?php
        define('NRO_DOCUMENTO_EMPRESA', '20393999463');
        $tipoDocCliente = strlen($docVenta['DniRuc']) > 9 ? "6" : "1";
        if($docVenta['CodSunat']=='03'){ $tdocumento='BOLETA ELECTRÓNICA'; }
        if($docVenta['CodSunat']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }

        $text = NRO_DOCUMENTO_EMPRESA . ' | ' . $tdocumento. ' | ' . $docVenta['Serie'] . ' | ' . $docVenta['Numero'].
          ' | ' . number_format($igv, 2) . ' | ' . number_format($total - $totalDescuento, 2) . ' | ' . $fecha .
          ' | ' . $tipoDocCliente . ' | ' . $docVenta['DniRuc'] . ' |';

        QRcode::png($text, 'qr.png', 'Q',15, 0);
        $imagedata = file_get_contents("qr.png");
        $base64 = base64_encode($imagedata);
        ?>
        <img style="max-width: 100%; width: 100px;" src="data:image/png;base64,<?php echo $base64 ?>" />
  </div>
  <br />
  <div class="center small">Autorizado mediante Resolución de Oficina Zonal</div>
  <div class="center small">N° 192-005-0000020/SUNAT</div>
  <div class="center small">Representación impresa de la Boleta Electrónica</div><br>
  <div class="center small">Consulte su comprobante en:</div>
  <div class="center small"><b>http://<?php echo $_SERVER['SERVER_NAME'] ?>/api/sunat/pag_cliente/</b></div>
  <div class="center small"></div><br />

  <div class="center small">AV. TUPAC AMARU MZA. 19 LOTE. 18 A.H. SIEMPRE UNIDOS II </div>
  <div class="center small">CORONEL PORTILLO - MANANTAY - UCAYALI</div>
  <!--<div class="center small">TELF. xxx - CEL. xxx</div>-->
  <div class="center small">GRACIAS POR SU COMPRA</div>
  <br />
  <br />

</div>
