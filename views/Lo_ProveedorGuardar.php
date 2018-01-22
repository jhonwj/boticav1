<?php 

include_once '../clases/DtGeneral.php';

$Proveedores = json_decode($_POST["proveedor"], true);
if (empty($Proveedores[4]["value"])) {
	fn_guardarProveedor($Proveedores[0]["value"], $Proveedores[1]["value"], $Proveedores[2]["value"], $Proveedores[3]["value"]);
	echo "Nuevo";
}else {
	fn_modificarProveedor($Proveedores[4]["value"], $Proveedores[0]["value"], $Proveedores[1]["value"], $Proveedores[2]["value"], $Proveedores[3]["value"]);
	echo "Modificado";
}
 ?>