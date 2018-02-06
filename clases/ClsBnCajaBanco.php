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

function ListarDocAplicadoCajaBancoDet($idDocVenta)
{
  $Ssql = "SELECT Cb_CajaBanco.FechaDoc, Cb_CajaBancoDet.Importe From Cb_CajaBancoDet INNER JOIN Cb_CajaBanco ON Cb_CajaBancoDet.IdCajaBanco = Cb_CajaBanco.IdCajaBanco WHERE Cb_CajaBancoDet.Tipo='VE' And Cb_CajaBancoDet.IdDocDet=$idDocVenta";
  return getSQLResultSet($Ssql);
}

?>
