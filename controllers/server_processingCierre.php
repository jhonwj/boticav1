<?php

include_once("../clases/BnGeneral.php");

$result = ListarCierre();

$data = array();

while($row = mysql_fetch_array($result)){
  $data[] = $row;
}

echo json_encode($data);

?>
