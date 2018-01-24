<?php

include_once("../clases/DtGeneral.php");

$result = fn_GuardarCierre();

header("Location: /views/ve_cierreform.php");

 ?>
