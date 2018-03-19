<?php

include_once '../clases/BnGeneral.php';

    $serie = $_GET['serie'];

    $result = devolverNumeroSiguienteMovimiento($serie);
	$row = mysqli_fetch_allfg($result);
    var_dump($row);exit();


	echo json_encode($row);

 ?>
