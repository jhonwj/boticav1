<?php 
include_once("../clases/BnGeneral.php");

$sintoma = json_decode($_POST['data'], true);
//$result = fn_devolverDiagnosticoSintoma()


if(isset($sintoma)){
 $result = fn_devolverDiagnosticoSintoma($sintoma[0], $sintoma[1], $sintoma[2]);
 $arr = array();

 while ($rows = mysqli_fetch_assoc($result)) {
 	 $arr[] = $rows;
 }
 echo json_encode($arr);
}else{
	echo null;
}



 ?>
