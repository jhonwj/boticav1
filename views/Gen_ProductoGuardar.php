<?php
include_once("../clases/DtGeneral.php");
include_once("../clases/BnGeneral.php");
include_once 'header.php';
header( 'Content-Type: text/html;charset=utf-8' );

/*$idProducto = $_GET["idproducto"];
$productoMarca = $_GET["productomarca"];
$productoFormaFarmaceutica = $_GET["productoformafarmaceutica"];
$productoMedicion = $_GET["productomedicion"];
$productoCategoria = $_GET["productocategoria"];
$producto = $_GET["producto"];
$productoDesc = $_GET["productodesc"];
$productoDescCorto = $_GET["productodescorto"];
$productoCodigoBarra = $_GET["productocodigodebarra"];
$productoCodigo = $_GET["productocodigo"];
$productoDosis = $_GET["productodosis"];
$productoPrecioContado = $_GET["productopreciocontado"];
$productoPrecioXMayor = $_GET["productopreciopormayor"];
$productoStockXMayor = $_GET["productostockpormayor"];*/
$CompuestosJson = json_decode($_POST["data"], true);
$producto = json_decode($_POST["data2"], true);
$productoDet = json_decode($_POST["data3"], true);
//$usuario = $_GET["usuario"];

//echo "adasdasd".$producto[20]["value"];

$controlStock = 0;
$ventaEstrategica = 0;
$porcentajeUtilidad = $producto[17]["value"];
$precioContado = $producto[18]["value"];
$precioMayor = $producto[19]["value"];
$stockMayor = $producto[20]["value"];
$stockMinimo = $producto[21]["value"];
$user = $producto[22]["value"];


if ($producto[17]["value"]=="on" && $producto[22]["value"]=="on") {
	$controlStock = 1;
	$ventaEstrategica = 1;
	$porcentajeUtilidad = $producto[18]["value"];
	$precioContado = $producto[19]["value"];
	$precioMayor = $producto[20]["value"];
	$stockMayor = $producto[21]["value"];
	$stockMinimo = $producto[23]["value"];
	$user = $producto[24]["value"];

}elseif ($producto[17]["value"]=="on") {
	$ventaEstrategica = 1;
	$porcentajeUtilidad = $producto[18]["value"];
	$precioContado = $producto[19]["value"];
	$precioMayor = $producto[20]["value"];
	$stockMayor = $producto[21]["value"];
	$stockMinimo = $producto[22]["value"];
	$user = $producto[23]["value"];
}elseif ($producto[21]["value"]=="on") {
	$controlStock = 1;
	$stockMinimo = $producto[22]["value"];
	$user = $producto[23]["value"];
}

if (empty($producto[0]["value"])) {
	//echo $producto[2]["value"];
	$result = fn_guardarProducto($producto[2]["value"], $producto[4]["value"], $producto[6]["value"], $producto[8]["value"], $producto[9]["value"], $producto[10]["value"], $producto[11]["value"], $producto[12]["value"], $producto[13]["value"], $producto[14]["value"],$producto[15]["value"],$producto[16]["value"], $ventaEstrategica, $porcentajeUtilidad, $precioContado, $precioMayor, $stockMayor, $controlStock, $stockMinimo , $user);
	$row = mysql_fetch_assoc($result);
	$idProducto = $row["IdProducto"];
	foreach ($CompuestosJson as $key) {
		//echo $key[0];
		fn_guardarProductoCompuestoDet($key[0], $idProducto);
		}
	foreach ($productoDet as $key) {
		fn_guardarProductoDet($idProducto, $key[0], $key[2]);
	}
}else{
	$res = fn_modificarProducto($producto[0]["value"], $producto[2]["value"], $producto[4]["value"], $producto[6]["value"], $producto[8]["value"], $producto[9]["value"], $producto[10]["value"], $producto[11]["value"], $producto[12]["value"], $producto[13]["value"], $producto[14]["value"],$producto[15]["value"],$producto[16]["value"], $ventaEstrategica, $porcentajeUtilidad, $precioContado, $precioMayor, $stockMayor, $controlStock, $stockMinimo , $user);
	$Ssql = "DELETE FROM botica.Gen_ProductoCompuestoDet WHERE Gen_ProductoCompuestoDet.Gen_Producto_IdProducto =".$producto[0]["value"];
	$Ssql2 = "DELETE FROM botica.Gen_ProductoDet WHERE Gen_ProductoDet.IdProducto =".$producto[0]["value"];
	if(!ejecutarSQLCommand($Ssql)){
		foreach ($CompuestosJson as $key) {
		fn_guardarProductoCompuestoDet($key[0], $producto[0]["value"]);
		}
		if(!ejecutarSQLCommand($Ssql2)){
		foreach ($productoDet as $key) {
			fn_guardarProductoDet($producto[0]["value"], $key[0], $key[2]);
			}
		}
		//break;
		}
}
/*if ($res = "SI") {
	header('Location: '.Producto);
	exit;
}else{
	echo '<script type="text/javascript">alert("Datos incorrectos! NO");</script>';*/

//}
 ?>
