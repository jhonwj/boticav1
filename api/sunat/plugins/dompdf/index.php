<?php

//require_once("dompdf/dompdf_config.inc.php");

$rutat=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$rutat= str_replace("plugins/dompdf/index.php", "", $rutat);

require_once 'lib/html5lib/Parser.php';
require_once 'lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'lib/php-svg-lib/src/autoload.php';
require_once 'src/Autoloader.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;
use Dompdf\Options;
include "../phpqrcode/qrlib.php";

// require "../../modelos/resumen.php";
// require "../../modelos/numeros-letras.php";

// $resumen=new Resumen();

// $id=$_GET['id'];

// $sql="SELECT *FROM venta WHERE idventa='$id' ";
// $mostrar= ejecutarConsultaSimpleFila($sql);

// $sql2="SELECT *FROM persona WHERE idpersona='$mostrar[txtID_CLIENTE]' ";
// $mcliente= ejecutarConsultaSimpleFila($sql2);

// $sql3="SELECT *FROM config WHERE estado='1' ";
// $mempresa= ejecutarConsultaSimpleFila($sql3);
$bodyRequest = file_get_contents("php://input");
$cab = json_decode($bodyRequest, true);


if($cab['txtTIPO_PROCESO']=='03'){ $tipop='BETA'; }else{ $tipop='PRODUCCION'; }

if($cab['txtCOD_MONEDA']=='PEN'){ $valmoneda='SOLES'; }
if($cab['txtCOD_MONEDA']=='USD'){ $valmoneda='DOLARES'; }
if($cab['txtCOD_MONEDA']=='EUR'){ $valmoneda='EUROS'; }

$ruta="../../api_cpe/".$tipop."/".$cab['txtNRO_DOCUMENTO_EMPRESA']."/";
$fichero=$cab['txtNRO_DOCUMENTO_EMPRESA'].'-'.$cab['txtCOD_TIPO_DOCUMENTO'].'-'.$cab['txtNRO_COMPROBANTE'];

if($cab['txtCOD_TIPO_DOCUMENTO']=='03'){ $tdocumento='BOLETA ELECTRÓNICA'; }
if($cab['txtCOD_TIPO_DOCUMENTO']=='01'){ $tdocumento='FACTURA ELECTRÓNICA'; }

$comprobante = explode('-', $cab['txtNRO_COMPROBANTE']);
$serie = $comprobante[0];
$numero = $comprobante[1];
//QRcode::png("".$text);
//DATOS OBLIGATORIOS DE LA SUNAT EN EL QR
//RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE //EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |

$text=$cab['txtNRO_DOCUMENTO_EMPRESA'].' | '.$tdocumento.' | '.$serie.' | '.$numero.' | '.$cab['txtTOTAL_IGV'].' | '.$cab['txtTOTAL'].' | '.$cab['txtFECHA_DOCUMENTO'].' | '.$cab['txtTIPO_DOCUMENTO_CLIENTE'].' | '.$cab['txtNRO_DOCUMENTO_CLIENTE'].' |';
QRcode::png($text, $cab['txtNRO_DOCUMENTO_EMPRESA'].".png", 'Q',15, 0);

 $html =
   '
<html> 
   <head> 
   <style> 
body{
font:10px Arial, Tahoma, Verdana, Helvetica, sans-serif;
color:#000;
}
.cabecera table {
	width: 100%;
    color:black;
    margin-top: 0em;
    text-align: left; font-size: 10px;
}
.cabecera h1 {
    font-size:17px; padding-bottom: 0px; margin-bottom: 0px; te
}
.cabecera2 table { border-collapse: collapse; border: solid 1px #000000;}
.cabecera2 th, .cabecera2 td { text-align: center; border-collapse: separate; border: solid 1px #000000; font-size:12px; padding: 8px; } 
.cabeza{ text-align: left; }
.nfactura{ background-color: #D8D8D8; }
.cuerpo table { border-collapse: collapse; margin-top:1px; border: solid 1px #000000; }
.cuerpo thead { border: solid 1px #000000; } 
.cuerpo2 thead { border: solid 1px #000000; } 
table { width: 100%; color:black; }
  
tbody { background-color: #ffffff; }
th,td { padding: 3pt; }           
.celda_right{  border-right: 1px solid black;  }
.celda_left{  border-left: 1px solid black; }         
.footer th, .footer td { padding: 1pt; border: solid 1px #000000; }
.footer { position: fixed; bottom: 180px; font-size:10px;  width: 100%; border: solid 0px #000000; }
.fg { font-size: 11px;} 
.fg2 { text-align: center; }
.fg3 { border: solid 0px; } 
.total td { border: solid 0px; padding: 0px; } 
.total2 { text-align: right; } 
   </style>
    
   </head> 
    
   <body>        
   
<table width="100%" border="0" class="cabecera" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
	
<td width="6%"><img src="../../images/logo.png" width="123" height="60" /></td>
	
<td class="cabeza"><h1>'.$cab['txtNOMBRE_COMERCIAL_EMPRESA'].'</h1>
<strong>DIRECCIÓN: </strong> '.$cab['txtDIRECCION_EMPRESA'].'<br>
  <!--<strong>TELF.:</strong>  '.$cab['txtTELEFONOS_EMPRESA'].'<br>-->
</td>
		
      <td width="30%">
        
        
        <table width="100%" class="cabecera2" cellspacing="0" >
          <tbody>
            <tr>
              <td >RUC N° '.$cab['txtNRO_DOCUMENTO_EMPRESA'].'</td>
            </tr>
            <tr>
              <td class="nfactura">'.$tdocumento.'</td>
            </tr>
            <tr>
              <td >'.$cab['txtNRO_COMPROBANTE'].'</td>
            </tr>
          </tbody>
        </table>
        
        
        
        
      </td>
    </tr>
  </tbody>
</table>
<br>
<table width="100%" class="cuerpo" cellspacing="0">
<thead>
    <tr>
      <td width="10%">NRO.DOCU.:</td>
      <td width="60%">'.$cab['txtNRO_DOCUMENTO_CLIENTE'].'</td>
      <td width="10%">FECHA:</td>
      <td width="20%">'.date("Y-m-d", strtotime($cab['txtFECHA_DOCUMENTO'])).'</td>
    </tr>
    <tr>
      <td>CLIENTE:</td>
      <td>'.$cab['txtRAZON_SOCIAL_CLIENTE'].'</td>
      <td>NRO.GUIA:</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>DIRECCIÓN:</td>
      <td>'.$cab['txtDIRECCION_CLIENTE'].'</td>
      <td>MONEDA:</td>
      <td>'.$valmoneda.'</td>
    </tr>
  </thead>
</table>
<table width="100%" class="cuerpo2" border="0" cellspacing="0">
<thead> 
    <tr>
      <td width="10%">CODIGO</td>
      <td width="50%">DESCRIPCION</td>
      <td width="10%">U. M.</td>
      <td width="10%">PRECIO</td>
      <td width="10%">CANTIDAD</td>
      <td width="10%">IMPORTE</td>
    </tr>
</thead>
<tbody>
';
// $rspta = $resumen->detfactura($mostrar['idventa']);
// while ($reg = $rspta->fetch_object()){	

foreach ($cab['detalle'] as $producto) {
$html.='
<tr>
      <td>'.$producto['txtCODIGO_DET'].'</td>
      <td>'.$producto['txtDESCRIPCION_DET'].'</td>
      <td>'.$producto['txtUNIDAD_MEDIDA_NOMBRE_DET'].'</td>
      <td>'.number_format($producto['txtPRECIO_DET'], 2, '.', '').'</td>
      <td>'.$producto['txtCANTIDAD_DET'].'</td>
      <td>'.number_format($producto['txtIMPORTE_DET'], 2, '.', '').'</td>
    </tr>';
}
$html.='
  </tbody>
</table>
</div> 
<table width="100%"  class="footer" border="0" cellspacing="0">
  <tbody>
    <tr>
<td colspan="3" class="fg"><strong>SON: '.$cab['txtTOTAL_LETRAS'].'</strong></td>
    </tr>
    <tr>
<td width="64%">
<br>
Representación impresa de la '.$tdocumento.'<br>
</td>
<td width="16%" rowspan="5"  class="fg fg2" >
<img src="'.$cab['txtNRO_DOCUMENTO_EMPRESA'].'.png" width="120" height="120" />
</td>
<td rowspan="5" class="fg fg2" width="20%" >
<table width="100%" border="0" cellspacing="0"  class="total"  >
        <tbody>
<tr><td class="total2" width="50%"><strong>SUB.TOTAL:</strong></td><td><strong>'.number_format($cab['txtSUB_TOTAL'], '2', '.', '').'</strong></td></tr>
<tr><td class="total2"><strong>GRAVADAS:</strong></td><td><strong>0.00</strong></td></tr>
<tr><td class="total2"><strong>INAFECTA:</strong></td><td><strong>0.00</strong></td></tr>
<tr><td class="total2"><strong>EXONERADA:</strong></td><td><strong>'.number_format($cab['txtSUB_TOTAL'], '2', '.', '').'</strong></td></tr>
<tr><td class="total2"><strong>GRATUITA:</strong></td><td><strong>0.00</strong></td></tr>
<tr><td class="total2"><strong>DESCUENTO:</strong></td><td><strong>'.number_format($cab['txtTOTAL_DESCUENTO'], '2', '.', '').'</strong></td></tr>
<tr><td class="total2"><strong>IGV(18%):</strong></td><td><strong>'.number_format($cab['txtTOTAL_IGV'], '2', '.', '').'</strong></td></tr>
<tr><td class="total2"><strong>ISC:</strong></td><td><strong>0.00</strong></td></tr>
<tr><td class="total2"><strong>TOTAL:</strong></td><td><strong>'.number_format($cab['txtTOTAL'], '2', '.', '').'</strong></td></tr>
        </tbody>
      </table>
</td>
    </tr>
    <tr>
  <td >
    <strong>HASH: '.$cab['hash_cpe'].'</strong>
  </td>
  </tr>
<tr><td>'.$cab['txtRAZON_SOCIAL_CLIENTE'].'</td></tr>
<tr><td style="text-transform: uppercase"><strong>VENDEDOR:</strong>'.$cab['txtVENDEDOR'].'</td></tr>
<tr>  
<td>
<!-- Operación  sujeta al sistma de pago de obligaciones tributarios con el gobierno central SPOT, sujeta a detracción del 10% si es mayor a S/.700.00 -->
  </td>
</tr>
   
<tr>
  <td colspan="3" style="padding: 5px; text-align:center" >
  BIENES TRANSFERIDOS EN LA AMAZONÍA REGIÓN
  <br>
  SELVA PARA SER CONSUMIDOS EN LA MISMA
  </td>
</tr>
  </tbody>
</table>
</body> </html>
   
   
 ';

 $dompdf = new DOMPDF();
 $dompdf->set_paper('letter','portrait');
 //$dompdf->set_paper('legal','landscape');
 $dompdf->load_html($html);
 $dompdf->render();
 //$dompdf->stream("pdf".Date('Y-m-d').".pdf");
 //$dompdf->stream("ejemplo-basico.pdf", array('Attachment' => 0));
 $pdf = $dompdf->output();
 //file_put_contents('../'.$ruta, $pdf);
 file_put_contents($ruta.$fichero.'.pdf', $pdf);
 
// var_dump($ruta.$fichero.'.pdf');exit();

?>