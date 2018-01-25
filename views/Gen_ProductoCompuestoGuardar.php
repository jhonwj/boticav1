<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
include_once("header.php");
header( 'Content-Type: text/html;charset=utf-8' );
$productoCompuesto=$_GET['productocompuesto'];
$idproductoCompuesto=$_GET['idproductocompuesto'];
$Producto = json_decode($_GET["data"], true);

	if (empty($productoCompuesto)) {
	echo '<script type="text/javascript">alert("Datos incorrectos!");</script>';
	header('Location: '.Compuesto);
	exit;
}
	$existe=fn_devolverProductoCompuestoSiExiste($productoCompuesto);
	if($existe){
	$Ssql = "DELETE FROM Gen_ProductoCompuestoDet WHERE Gen_ProductoCompuestoDet.Gen_ProductoCompuesto_IdProductoCompuesto=$idproductoCompuesto";
	if(!ejecutarSQLCommand($Ssql)){
			foreach ($Producto as $key) {
			fn_guardarProductoCompuestoDet($idproductoCompuesto ,$key[0]);
			}
			}
	}else{
		if (empty($idproductoCompuesto)) {
	$result = fn_guardarProductoCompuesto($productoCompuesto, "Jeam");
	$row = mysqli_fetch_assoc($result);
	$idCompuesto = $row["IdProductoCompuesto"];
	foreach ($Producto as $key) {
		fn_guardarProductoCompuestoDet($idCompuesto ,$key[0]);
		}
	}else{
		$res = fn_modificarProductoCompuesto($idproductoCompuesto,$productoCompuesto,1,"jeam");
		$Ssql = "DELETE FROM Gen_ProductoCompuestoDet WHERE Gen_ProductoCompuestoDet.Gen_ProductoCompuesto_IdProductoCompuesto=$idproductoCompuesto";
		if(!ejecutarSQLCommand($Ssql)){
			foreach ($Producto as $key) {
			fn_guardarProductoCompuestoDet($idproductoCompuesto ,$key[0]);
			}
			}
		}
	}

if($res = "SI"){
	$existe=fn_devolverProductoCompuestoSiExiste($productoCompuesto);

	if($existe){
		echo '<script type="text/javascript">alert("Se guardo!");</script>';
		header('Location: '.Compuesto);
	}else{
		echo '<script type="text/javascript">alert("No se guardo!");</script>';
		header('Location: '.Compuesto);

	}

}

 ?>
