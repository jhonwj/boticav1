<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
include_once '../views/header.php';
header( 'Content-Type: text/html;charset=utf-8' );
$productoFormaFarmaceutica=$_GET['productoformafarmaceutica'];
$usuario=$_GET['usuario'];
$anulado=$_GET['anulado'];
$idproductoFormaFarmaceutica=$_GET['idproductoformafarmaceutica'];

/*if (empty($productoFormaFarmaceutica) || empty($usuario)) {
	echo '<script type="text/javascript">alert("Datos incorrectos!");</script>';
	header('Location: '.Forma_Farmaceutica);
	exit;
}*/
		if (empty($anulado)) {$anulado = "0";}else{$anulado = "1";}

	$existe=fn_devolverProductoFormaFarmaceuticaSiExiste($productoFormaFarmaceutica);
	//echo "VER (".$existe.")";
	if($existe){
		fn_modificarProductoFormaFarmaceuticaEstado($idproductoFormaFarmaceutica, $anulado, $usuario);
		//header('Location: '.Forma_Farmaceutica);
		//echo '<script type="text/javascript">alert("Ya Existe la Forma Farmaceutica");</script>';
		//exit();
	}else{
		if (empty($idproductoFormaFarmaceutica)) {
				$res = fn_guardarProductoFormaFarmaceutica($productoFormaFarmaceutica, "Jeam");	
			}else{
				$res = fn_modificarProductoFormaFarmaceutica($idproductoFormaFarmaceutica,$productoFormaFarmaceutica,$anulado,$usuario);

			}
	}

if($res = "SI"){
	$existe=fn_devolverProductoFormaFarmaceuticaSiExiste($productoFormaFarmaceutica);

	if($existe){
		echo '<script type="text/javascript">alert("Se guardo!");</script>';
		header('Location: '.Forma_Farmaceutica);
	}else{
		echo '<script type="text/javascript">alert("No se guardo!");</script>';
		header('Location: '.Forma_Farmaceutica);

	}

}

 ?>
