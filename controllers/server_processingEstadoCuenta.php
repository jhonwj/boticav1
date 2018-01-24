<?php

include_once '../clases/BnGeneral.php';

$TipoOpe = $_GET['tipoOpe'];
$Cliente = $_GET['cliente'];
$TipoOpeNumero = 0;

if ($TipoOpe=="PAGO A PROVEEDOR") {
  $TipoOpeNumero=2;
}elseif ($TipoOpe=="PAGO DE CLIENTE") {
  $TipoOpeNumero=1;
}
$res="";
$res = BuscarEstadoCuentaDet($Cliente, $TipoOpeNumero);
$data = array();

while ($rows = mysqli_fetch_assoc($res)) {
  $data[] = $rows;
}
 echo json_encode($data);

 ?>
