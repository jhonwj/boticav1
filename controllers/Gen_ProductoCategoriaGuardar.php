<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
include_once("header.php");
header( 'Content-Type: text/html;charset=utf-8' );
$productoCategoria=$_GET['productocategoria'];
$usuario=$_GET['usuario'];
$idproductoCategoria=$_GET['idproductocategoria'];

/*if (empty($productoCategoria) || empty($usuario)) {
	echo '<script type="text/javascript">alert("Datos incorrectos!");</script>';
	header('Location: '.Categoria);
	exit;
}*/
	$existe=fn_devolverProductoCategoriaSiExiste($productoCategoria);
	echo "VER (".$existe.")";
	if($existe){
		echo '<script type="text/javascript">alert("Ya Existe la Categoria de este Producto");</script>';
		header('Location: '.Categoria);
	}
if (empty($idproductoCategoria)) {
	$res = fn_guardarProductoCategoria($productoCategoria, "Jeam");
}else{
	$res = fn_modificarProductoCategoria($idproductoCategoria,$productoCategoria,1,$usuario);
}

if($res = "SI"){
	$existe=fn_devolverProductoCategoriaSiExiste($productoCategoria);

	if($existe){
		echo '<script type="text/javascript">alert("Se guardo!");</script>';
		header('Location: '.Categoria);
	}else{
		echo '<script type="text/javascript">alert("No se guardo!");</script>';
		header('Location: '.Categoria);

	}

}

 ?>
