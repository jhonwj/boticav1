<?php
include_once("../clases/DtGeneral.php");

$clientejson = json_decode($_POST['data'], true);

//empty($clientejson[5]["value"]) id del cliente

//echo $clientejson[5]["value"];
//exit();

if(empty($clientejson[5]["value"])){
	if(!fn_guardarCliente($clientejson[0]["value"], $clientejson[1]["value"], $clientejson[2]["value"], $clientejson[3]["value"], $clientejson[4]["value"], "jeam")){
		echo "a";
		//break;
	}
	//echo false;
	//break;
}else{
	if(!fn_modificarCliente($clientejson[5]["value"], $clientejson[0]["value"], $clientejson[1]["value"], $clientejson[2]["value"], $clientejson[3]["value"], $clientejson[4]["value"], "jeam")){
		echo "m";
		//break;
	}
	//echo false;
	//break;
}


 ?>
