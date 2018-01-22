<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
header( 'Content-Type: text/html;charset=utf-8' );

$idRegVenta = $_GET["idRegVenta"];

if (!fn_EliminarRegVenta($idRegVenta)) {
  echo "Venta Anulada";
}else {
  echo "Datos erroneos";
}

 ?>
