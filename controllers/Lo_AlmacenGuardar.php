<?php 

include_once '../clases/DtGeneral.php';

$almacen = json_decode($_POST["almacen"], true);
if (empty($almacen)) {
	echo false;
}elseif (!fn_guardarAlmacen($almacen[0]["value"])) {
	echo "SI";
}else{
	echo "false";
}
