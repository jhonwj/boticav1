<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
header( 'Content-Type: text/html;charset=utf-8' );

$IdMovimiento = $_GET["idMov"];

if (!fn_EliminarMovimiento($IdMovimiento)) {
  echo "Movimiento Eliminado";
}else {
  echo "Datos erroneos";
}

 ?>
