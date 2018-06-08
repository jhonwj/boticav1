<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
include_once("../views/header.php");
// header( 'Content-Type: text/html;charset=utf-8' );
$productoMarca=$_POST['productomarca'];
$usuario=$_POST['usuario'];
$idproductoMarca=$_POST['idproductomarca'];

/*if (empty($productoMarca) || empty($usuario)) {
	echo '<script type="text/javascript">alert("Datos incorrectos!");</script>';
	header('Location: '.Marca);
	exit;
}*/
	$existe=fn_devolverProductoMarcaSiExiste($productoMarca);
	echo "VER (".$existe.")";
	if($existe){
		echo '<script type="text/javascript">alert("Ya Existe la Marca de Producto");</script>';
		exit();
	}
if (empty($idproductoMarca)) {
	$res = fn_guardarProductoMarca($productoMarca, "Jeam");
}else{
	$res = fn_modificarProductoMarca($idproductoMarca,$productoMarca,1,$usuario);
}

if($res = "SI"){
	$existe=fn_devolverProductoMarcaSiExiste($productoMarca);

	if($existe){
		echo '<script type="text/javascript">alert("Se guardo!");</script>';
		header('Location: '.Marca);
	}else{
		echo '<script type="text/javascript">alert("No se guardo!");</script>';
		header('Location: '.Marca);

	}

}

 ?>
