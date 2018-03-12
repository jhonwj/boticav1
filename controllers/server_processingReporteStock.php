<?php

	include_once '../clases/BnGeneral.php';

	$almacen = $_GET["almacen"];

	if (isset($_GET['cursor'])) {
		$cursor = ejecutarStockCursor($almacen, "");
		if($cursor) {
			echo json_encode(array("success"=> true));
		} else {
			echo json_encode(array("error"=> true));
		}
		exit();
	}

	if(isset($_GET['serverSide'])) {
		$result = ListarReporteStock($almacen,"", true);
		$resultVencimiento = ListarLoteFechaVencimiento();
		$data = array();
		$dataVencimiento = array();
		while ($rowsVencimiento = mysqli_fetch_assoc($resultVencimiento)) {
			$dataVencimiento[$rowsVencimiento['IdProducto']] = $rowsVencimiento;
		}
		while ($rows = mysqli_fetch_assoc($result['aaData'])) {
			$rows['IdLote'] = isset($dataVencimiento[$rows['numero']]) ? $dataVencimiento[$rows['numero']]['IdLote'] : '';
			$rows['FechaVen'] = isset($dataVencimiento[$rows['numero']]) ? $dataVencimiento[$rows['numero']]['FechaVen'] : '';
			$data[] = $rows;
		}
		$results = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $result['iTotalRecords'],
			"iTotalDisplayRecords" => $result['iTotalRecords'],
			"aaData" => $data);
	   
		echo json_encode($results);	   
		exit();
	}

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
