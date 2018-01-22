<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
include_once("header.php");
header( 'Content-Type: text/html;charset=utf-8' );

$idproductoMedicion = $_GET["idproductomedicion"];
$productoMedicion = $_GET["productomedicion"];
$usuario = $_GET["usuario"];

/*if (empty($productoMedicion) || empty($usuario)) {
	echo '<script type="text/javascript">alert("Datos incorrectos!");</script>';
	header('Location: '.Medicion);
	exit();
}*/
	$existe=fn_devolverProductoMedicionSiExiste($productoMedicion);
	//echo "VER (".$existe.")";
	if($existe){
		echo '<script type="text/javascript">alert("Ya Existe la Medicion del Producto");</script>';
		header('Location: '.Medicion);
		exit();
	}
if (empty($idproductoMedicion)) {
	$res = fn_guardarProductoMedicion($productoMedicion, "");
}else{
	$res = fn_modificarProductoMedicion($idproductoMedicion,$productoMedicion,1,$usuario);
}

if($res = "SI"){
	$existe=fn_devolverProductoMedicionSiExiste($productoMedicion);

	if($existe){
		echo '<script type="text/javascript">alert("Se guardo!");</script>';
		header('Location: '.Medicion);
	}else{
		echo '<script type="text/javascript">alert("No se guardo!");</script>';
		header('Location: '.Medicion);

	}

}

 ?>
