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
//$serieMaq = $docVenta['SerieImpresora'];
$tipoDoc = $docVenta['TipoDoc'];
$docModifica = $docVenta['NroComprobanteModifica'];
$notaDescMotivo = $docVenta['NotaDescMotivo'];
$codSunatModifica = $docVenta['CodSunatModifica'];
$notaIdMotivo = $docVenta['NotaIdMotivo'];
$subtotal = 0;
$total = 0;
$totalDescuento = 0;
$igv = 0;
$tipoDocModifica = $codSunatModifica=='01'? 'FACTURA ELECTRÓNICA':'BOLETA ELECTRÓNICA';

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
    font-size: 3.5mm;
    font-family: monospace;
    font-weight:  bold;
  }
  body {
    display: block;
    margin: 12px;
  }
  td.tabla1,
  th.tabla1,
  tr,
  .tabla1 {
    width: 72mm;
    border-top: black 1px dashed;
    border-collapse: collapse;
  }
  .container {
    width: 75mm;
    max-width: 100%;
    padding:  3mm 0.5mm 3mm .5m;
    box-sizing: border-box;
  }

  .center {
    text-align: center;
  }
  .separar {
    margin: 8px 0;
    border-top: 1px dashed #000;

  }
  td.tabla1 {
    line-height: 1.48em;
    font-size: 1em;
  }
  td, th {
    padding: 2px;
  }
  td.cantidad {
    text-align: center;
  }
  .text-right {
    text-align: right;
  }
  .small {
    font-size: 12px;
  }
</style>
<div class="container">
  <!--<div class="center">
    <img width="80px" src="../resources/images/delmancito.jpg"  /><br /><br />
  </div>-->
  <div class="center"><img src="/resources/images/logo-ticket.png" style="max-width:100%; width: 40mm"/></div>
  <div class="center"><b><?php echo RAZON_SOCIAL_E ?></b></div>
  <div class="center"><b>RUC: <?php echo DOCUMENTO_EMPRESA_E ?></b></div><br>
  <div class="center small"></div>
  
  <center>
  <div class="">NOTA DE CREDITO </br> <span style="font-size:15px"><?php echo $docVentaNro; ?></span></div>
  <div class="">FECHA: <?php echo $fechaDoc; ?></div>
  <!-- <div class="">SERIE MAQ REG : <?php echo $serieMaq; ?></div> -->
  </center>
  <div class="separar"></div>
  <div>DOCUMENTO QUE MOFIDICA:</div>
  <div>TIPO DOCUMENTO:  <?php echo $tipoDocModifica ?></div>
  <div>COMPROBANTE: <?php echo $docModifica; ?></div>
  <div>MOTIVO: <?php echo $notaIdMotivo." - ".$notaDescMotivo ?></div>
  
  <div class="separar"></div>
  <div>SR(ES) : <?php echo $cliente ?></div>
  <div>RUC/DNI : <?php echo $dniRuc; ?></div>
  <div>DIR : <?php echo $direccion; ?></div>

  <br />

  <div class="productos">
    <table width="100%"  class="tabla1">
      <thead>
        <tr>
        <th class="numero" width="5%">#</th>
          <th class="producto" width="50%">PRODUCTO</th>
          <th class="cantidad">CANT</th>
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
            <td class="Numero">
              <span style="font-size:11px" ><?php echo $filas+1; ?></span>
            </td>
             
              <td class="producto">
              <span style="font-size:11px" ><?php echo $producto['Producto'] . ($producto['Descripcion']!=''?(' (' . $producto['Descripcion'] . ')'):'') ?></span>
              </td>
              <td class="cantidad">
                <span style="font-size:11px" ><?php echo $producto['Cantidad']; ?></span>
              </td>
              <td class="precio" style="text-align: center;">
                <span style="font-size:11px" ><?php echo number_format($producto['Precio'],2, '.', '') ?></span>
              </td>
              <td class="text-right">
                <?php 
                  if ($producto['CodigoBarra'] === "MANODEOBRA") {
                    $producto['TOTAL'] = $producto['TOTAL'] + $sumManoDeObra;
                  }
                ?>
                <span style="font-size:11px"><?php echo $producto['TOTAL'] ?></span>
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
      </tbody>
    </table>
    <table class="tabla1" width="100%">
        <tr >
          <td class="text-right" ><span style="font-size:13px">SUBTOTAL</span></td>
          <td class="text-right" width="20%" ><span style="font-size:13px">S/<?php echo number_format($subtotal, 2, '.', ''); ?></span></td>
        </tr>
        <?php if ($igv > 0) : ?>
          <tr>
            <td class="text-right"><span style="font-size:13px">IGV</span></td>
            <td class="text-right" width="20%"><span style="font-size:13px">S/<?php echo number_format($igv, 2, '.', ''); ?></span></td>
          </tr>
          <?php endif; ?>
        <?php if ($totalDescuento > 0) : ?>
          <tr>
            <td class="text-right"><span style="font-size:13px">DESCUENTO</span></td>
            <td class="text-right" width="20%"><span style="font-size:13px"> S/<?php echo number_format($totalDescuento, 2, '.', ''); ?></span></td>
          </tr>
        <?php endif; ?>
        <tr>
          <td class="text-right"><span style="font-size:15px">TOTAL</span></td>
          <td class="text-right" width="20%"><span style="font-size:15px">S/<?php echo number_format($total - $totalDescuento, 2, '.', ''); ?></span></td>
        </tr>
     </table >

    <?php if ($docVenta['Puntos']): ?>
      <span style="text-transform: uppercase">Usted tiene <b><?php echo $docVenta['Puntos']?></b> Puntos</span><br>
    <?php endif; ?>
    <span class="son">SON: <?php echo strtoupper(NumerosEnLetras::convertir(number_format($total - $totalDescuento, 2, '.', ''),'SOLES',true, 'asd')); ?></span><br/>
    <span style="text-transform: uppercase">VENDEDOR: <?php echo $docVenta['UsuarioReg']; ?></span><br>

    <?php if ($docVenta['PagoCon'] > 0) : ?>
      <span>PAGÓ CON: S/ <?php echo number_format($docVenta['PagoCon'], 2); ?></span><br />
      <span>VUELTO: S/<?php echo number_format($docVenta['PagoCon'] - $total, 2) ?></span>
    <?php endif; ?>
  </div>
</br>
  <div class="center">
  <span style="font-size:10px">  <div class="center small">BIENES TRANSFERIDOS EN LA AMAZONÍA REGIÓN</div>
      <div class="center small"> SELVA PARA SER CONSUMIDOS EN LA MISMA</div>
      <span style="font-size:11px">CONSULTA WEB: </br>http://emision.factura.vip</span>
  </div>

 <center>  
  <center>  
 <center>  
  <center>  
 <center>  
  <table class="tabla2">
  <tr >
  <td width="100" 
	  heigth="100">
        <?php
        define('NRO_DOCUMENTO_EMPRESA', DOCUMENTO_EMPRESA_E);
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
  </td>
  <td>
  <center>
  <br /><span style="font-size:10px">Autorizado mediante Resolución de Oficina Zonal</span>
  <br /><span style="font-size:10px">N° 192-005-0000020/SUNAT</span>
  <br /><span style="font-size:10px">Representación impresa de la Factura Electrónica</span>
   <center>
   </td>
  </tr>
  </table >
  </center>
  <div class="center small"><?php echo DIRECCION_E_COMPROBANTE ?></div>
  <div class="center small"><?php echo DEPARTAMENTO_E .' - ' . PROVINCIA_E . ' - ' .DISTRITO_E ?></div>
  <div class="center small"></div>
  <div class="center small">GRACIAS POR SU COMPRA</div>
  <center><br /><span style="font-size:11px">DESARROLLADO POR: </br>https://neurosoft.pe/ - CEL: 954370221</span>  </center>

</div>