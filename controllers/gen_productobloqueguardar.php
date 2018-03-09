<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
//include_once("header.php");
header( 'Content-Type: text/html;charset=utf-8' );
$productoBloque=$_GET['productobloque'];
$porcentajeMin=$_GET['porcentajeMin'];
$porcentajeMax=$_GET['procentajeMax'];
//$usuario=$_GET['usuario'];
$idproductoBloque=$_GET['idproductobloque'];

/*if (empty($productoCategoria) || empty($usuario)) {
	echo '<script type="text/javascript">alert("Datos incorrectos!");</script>';
	header('Location: '.Categoria);
	exit;
}*/
	/*$existe=fn_devolverProductoCategoriaSiExiste($productoCategoria);
	echo "VER (".$existe.")";
	if($existe){
		echo '<script type="text/javascript">alert("Ya Existe la Categoria de este Producto");</script>';
		header('Location: '.Categoria);
	}*/
if (empty($idproductoBloque)) {
	$res = fn_guardarProductoBloque($productoBloque, $porcentajeMin, $porcentajeMax, "Jeam");
	echo "a";
}else{
	$res = fn_modificarProductoBloque($idproductoBloque,$productoBloque, $porcentajeMin, $porcentajeMax,"Jeam");
	echo "m";
}

/*if($res = "SI"){
	$existe=fn_devolverProductoCategoriaSiExiste($productoCategoria);

	if($existe){
		echo '<script type="text/javascript">alert("Se guardo!");</script>';
		header('Location: '.Categoria);
	}else{
		echo '<script type="text/javascript">alert("No se guardo!");</script>';
		header('Location: '.Categoria);

	}

}*/

 ?>
