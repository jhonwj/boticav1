<?php
include_once("../models/DBManager.php");

function ListarCajaBanco($idCuenta, $fechaDoc)
{
    $Ssql = " call SbCb_ListarCajaBanco($idCuenta, '$fechaDoc');";
    return getSQLResultSet($Ssql);
}

function ListarDocAplicados($idCliente)
{
  $Ssql = " call SbCb_ListarDocAplicados($idCliente);";
  return getSQLResultSet($Ssql);
}

?>
