<?php

include_once '../clases/BnGeneral.php';

    $serie = $_GET['serie'];

    $result = devolverNumeroSiguienteMovimiento($serie);
	$row = mysqli_fetch_assoc($result);


	echo json_encode($row);

 ?>
