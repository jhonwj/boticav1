<?php
include_once("../models/DBManager.php");

function fn_guardarCajaBanco($data) {
    $idTipo = $data['IdTipoCajaBanco'];
    $idCuenta = $data['IdCuenta'];
    $fechaDoc = $data['FechaDoc'];
    $concepto = $data['Concepto'];
    $importe = $data['Importe'];

    $Ssql = "INSERT INTO Cb_CajaBanco (IdTipoCajaBanco, IdCuenta, FechaDoc, Concepto, Importe) VALUES ($idTipo, $idCuenta, '$fechaDoc', '$concepto', $importe)";
    return getSQLResultSet($Ssql);
}

function fn_eliminarCajaBanco($idCajaBanco) {
    $Ssql = "DELETE FROM Cb_CajaBancoDet WHERE IdCajaBanco = $idCajaBanco";
    getSQLResultSet($Ssql);

    $Ssql = "DELETE FROM Cb_CajaBanco WHERE IdCajaBanco = $idCajaBanco";
    return getSQLResultSet($Ssql);
}
?>
