<?php
include_once("../clases/DtGeneral.php");

$clientejson = json_decode($_POST['data'], true);

//empty($clientejson[5]["value"]) id del cliente

//echo $clientejson[5]["value"];
//exit();

echo fn_guardarCliente($clientejson[0]["value"], $clientejson[1]["value"], $clientejson[2]["value"], $clientejson[3]["value"], $clientejson[4]["value"], "jeam");

 ?>
