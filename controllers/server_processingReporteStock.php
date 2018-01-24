<?php

	include_once '../clases/BnGeneral.php';

	$almacen = $_GET["almacen"];

   	$result = ListarReporteStock($almacen,"");
	$resultVencimiento = ListarLoteFechaVencimiento();

	$data = array();
	$dataVencimiento = array();

	while ($rowsVencimiento = mysqli_fetch_assoc($resultVencimiento)) {
		$dataVencimiento[$rowsVencimiento['IdProducto']] = $rowsVencimiento;
	}

	while ($rows = mysqli_fetch_assoc($result)) {
		$rows['IdLote'] = isset($dataVencimiento[$rows['numero']]) ? $dataVencimiento[$rows['numero']]['IdLote'] : '';
		$rows['FechaVen'] = isset($dataVencimiento[$rows['numero']]) ? $dataVencimiento[$rows['numero']]['FechaVen'] : '';
		$data[] = $rows;
	}

	$results = array(
	 "sEcho" => 1,
	 "iTotalRecords" => count($data),
	 "iTotalDisplayRecords" => count($data),
	 "aaData" => $data);

	echo json_encode($results);

 ?>
