<?php 

	include_once '../clases/BnGeneral.php';

	$idProducto = $_GET["Producto"];
	if(!empty($idProducto)){
		//$idCompuesto = "00000";
		$result = fn_DevolverProductoDet($idProducto);
		$data = array();
		while ($rows = mysqli_fetch_assoc($result)) {
				$data[] = $rows;
			}

	echo json_encode($data);
	return true;
	}
	/*elseif (empty($Compuesto) && !empty($idCompuesto)) {
		$Compuesto = "00000";
		$result = fn_devolverProductosXCompuesto($idCompuesto, $Compuesto);
	}*/
	/*if (!empty($idProducto) && empty($idCompuesto)) {
   	$result = fn_devolverCompuestosXProducto($idProducto);
	}*/

	/*$data = array();

	while ($rows = mysqli_fetch_assoc($result)) {
		$data[] = $rows;
	}

	echo json_encode($data);*/


 ?>