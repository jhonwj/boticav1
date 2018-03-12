<?php
	include_once '../clases/BnGeneral.php';

	if(isset($_GET['serverSide'])) {
		$result = fn_devolverProducto("","", true);

		$data = array();

		while ($rows = mysqli_fetch_assoc($result['aaData'])) {
			$data[] = $rows;
		}
//var_dump($result);exit();
		$results = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $result['iTotalRecords'],
			"iTotalDisplayRecords" => $result['iTotalRecords'],
			"aaData" => $data
		);
		echo json_encode($results);
		exit();
	}

   $result = fn_devolverProducto("","");

	$data = array();

	while ($rows = mysqli_fetch_assoc($result)) {
		$data[] = $rows;
	}

	$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($data),
		"iTotalDisplayRecords" => count($data),
		"aaData" => $data
	);

	echo json_encode($results);

 ?>
