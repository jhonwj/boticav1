<?php 

include_once '/../models/DBManager.php';

$Ssql="SELECT IdProductoMarca as ID, ProductoMarca, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM botica.Gen_ProductoMarca";
   $result = getSQLResultSet($Ssql);
	//echo "<br/>SE GUARDO ($IdVideo22)!!!!!"

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

 ?>