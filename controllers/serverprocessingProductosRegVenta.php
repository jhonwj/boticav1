<?php 

	include_once '../clases/BnGeneral.php';

	$idDocVenta = $_GET["idDocVenta"];

	$result = devolverProductosRegVenta($idDocVenta);

	$data = array();

	while ($rows = mysqli_fetch_assoc($result)) {
		$data[] = $rows;
	}

	echo json_encode($data);
 ?>