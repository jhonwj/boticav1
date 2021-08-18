<?php
include_once($_SERVER["DOCUMENT_ROOT"] . '/views/validateUser.php');
include_once("../models/DBManager.php");

function ListarCajaBanco($idCuenta, $fechaDoc, $usuarioreg)
{

    if(isset($_SESSION['User'])) {
    $vendedor = $_SESSION['User'];
    }

    $usuarioReg = isset($usuarioreg) ? $usuarioreg : $vendedor;
    
    $Ssql = " SELECT
    CB.IdCajaBanco,
    CB.Concepto,
    CASE TCB.Tipo
      WHEN 1 THEN 0
          ELSE CB.Importe
    END AS 'Ingresos',
      CASE TCB.Tipo
      WHEN 1 THEN CB.Importe
          ELSE 0
    END AS 'Salida'
      FROM Cb_CajaBanco AS CB
      INNER JOIN Cb_TipoCajaBanco AS TCB
      ON CB.IdTipoCajaBanco = TCB.IdTipoCajaBanco
      WHERE CB.IdCuenta = $idCuenta AND DATE_FORMAT(CB.Fechadoc, '%Y-%m-%d') = '$fechaDoc'
      AND CB.UsuarioReg = '$usuarioReg'
    ORDER BY TCB.Tipo,CB.IdCajaBanco;";
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
