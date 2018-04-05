<?php

    include_once '../clases/BnGeneral.php';

    $result = ListarProductoFilter($_GET['codigoBarra']);

	$data = array();

	while ($rows = mysqli_fetch_assoc($result)) {
		$data[] = $rows;
	}

	echo json_encode($data);

 ?>
