<?php
include_once("../models/DBManager.php");

function ListarCajaBanco($idCuenta, $fechaDoc)
{
    $Ssql = " call SbCb_ListarCajaBanco($idCuenta, '$fechaDoc');";
    return getSQLResultSet($Ssql);
}

?>
