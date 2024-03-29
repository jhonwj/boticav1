<?php
include_once($_SERVER["DOCUMENT_ROOT"] . '/views/validateUser.php');
include_once("../models/DBManager.php");

function fn_guardarCajaBanco($data) {
    $idTipo = $data['IdTipoCajaBanco'];
    $idCuenta = $data['IdCuenta'];
    $fechaDoc = $data['FechaDoc'];
    $concepto = $data['Concepto'];
    $importe = $data['Importe'];
    $tipo = $data['TipoCajaBanco'];
    $aplicadoDocVenta = isset($data['AplicadoDocVenta']) ? $data['AplicadoDocVenta'] : false;
    $idCliente = isset($data['IdCliente']) ? $data['IdCliente'] : 0;
    $idProveedor = isset($data['IdProveedor']) ? $data['IdProveedor'] : 0;
    $usuarioReg = 'xx';
    if(isset($_SESSION['user'])) {
        $usuarioReg = $_SESSION['user'];
    }

    $Ssql = "INSERT INTO Cb_CajaBanco (IdTipoCajaBanco, IdCuenta, FechaDoc, Concepto, Importe, IdProveedor, IdCliente, UsuarioReg) VALUES ($idTipo, $idCuenta, '$fechaDoc', '$concepto', $importe, $idProveedor, $idCliente, '$usuarioReg')";
    $idCajaBanco = getSQLResultSet($Ssql);

    if ($idCajaBanco && $aplicadoDocVenta) {
      foreach ($aplicadoDocVenta as $key => $value) {
        if ($tipo == 1) {
            $Ssql = "INSERT INTO Cb_CajaBancoDet (IdCajaBanco, IdDocDet, Importe, Hash, Tipo) VALUES($idCajaBanco, " . $value['idDocDet'] . ", " . $value['importe'] . ", " . $value['idDocDet'] . ", 'MO')";
            getSQLResultSet($Ssql);
        } else {
            $Ssql = "INSERT INTO Cb_CajaBancoDet (IdCajaBanco, IdDocDet, Importe, Tipo) VALUES($idCajaBanco, " . $value['idDocDet'] . ", " . $value['importe'] . ", 'VE')";
            getSQLResultSet($Ssql);
        }
      }
    }
    return $idCajaBanco;
}

function fn_eliminarCajaBanco($idCajaBanco) {
    $Ssql = "DELETE FROM Cb_CajaBancoDet WHERE IdCajaBanco = $idCajaBanco";
    getSQLResultSet($Ssql);

    $Ssql = "DELETE FROM Cb_CajaBanco WHERE IdCajaBanco = $idCajaBanco";
    return getSQLResultSet($Ssql);
}
?>
