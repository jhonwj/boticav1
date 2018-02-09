<?php
include_once '../clases/ClsBnCajaBanco.php';

  $hash = $_GET['hash'];

  $result = ListarDocAplicadoCajaBancoDetProveedor($hash);

  $data = array();

  while ($rows = mysqli_fetch_assoc($result)) {
   $data[] = $rows;
  }

$results = array(
"sEcho" => 1,
"iTotalRecords" => count($data),
"iTotalDisplayRecords" => count($data),
"aaData" => $data);

echo json_encode($results);
