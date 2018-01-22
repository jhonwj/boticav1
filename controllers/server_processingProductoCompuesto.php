<?php 

	include_once '/../clases/BnGeneral.php';

	$idCompuesto = $_GET["Compuesto"];
	$Compuesto = $_GET["CompuestoNombre"];
	$idProducto = $_GET["Producto"];
	if(empty($idCompuesto) && empty($idProducto)){
		$idCompuesto = "00000";
		$result = fn_devolverProductosXCompuesto($idCompuesto, $Compuesto);
		$data = array();
		while ($rows = mysql_fetch_assoc($result)) {
				$data[] = $rows;
			}
		$results = array(
	 		"sEcho" => 1,
			 "iTotalRecords" => count($data),
	 		"iTotalDisplayRecords" => count($data),
	 		"aaData" => $data);

	echo json_encode($results);
	return true;
	}elseif (empty($Compuesto) && !empty($idCompuesto)) {
		$Compuesto = "00000";
		$result = fn_devolverProductosXCompuesto($idCompuesto, $Compuesto);
	}
	if (!empty($idProducto) && empty($idCompuesto)) {
   	$result = fn_devolverCompuestosXProducto($idProducto);
	}

	$data = array();

	while ($rows = mysql_fetch_assoc($result)) {
		$data[] = $rows;
	}

	echo json_encode($data);


 ?>