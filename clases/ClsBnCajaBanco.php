<?php
include_once($_SERVER["DOCUMENT_ROOT"] . '/views/validateUser.php');
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

function ListarDocAplicadosProveedor($idProveedor)
{
  $Ssql = " call SbCb_ListarDocAplicadosProveedor($idProveedor);";
  return getSQLResultSet($Ssql);
}

function ListarDocAplicadoCajaBancoDetProveedor($hash)
{
  $Ssql = "SELECT Cb_CajaBanco.FechaDoc, Cb_CajaBancoDet.Importe From Cb_CajaBancoDet INNER JOIN Cb_CajaBanco ON Cb_CajaBancoDet.IdCajaBanco = Cb_CajaBanco.IdCajaBanco WHERE Cb_CajaBancoDet.Tipo='MO' And Cb_CajaBancoDet.Hash=$hash";
  return getSQLResultSet($Ssql);
}
?>
