<?php
include_once("../models/DBManager.php");

function fn_guardarCajaBanco($data) {
    $idTipo = $data['IdTipoCajaBanco'];
    $idCuenta = $data['IdCuenta'];
    $fechaDoc = $data['FechaDoc'];
    $concepto = $data['Concepto'];
    $importe = $data['Importe'];
    $aplicadoDocVenta = $data['AplicadoDocVenta'];

    $Ssql = "INSERT INTO Cb_CajaBanco (IdTipoCajaBanco, IdCuenta, FechaDoc, Concepto, Importe) VALUES ($idTipo, $idCuenta, '$fechaDoc', '$concepto', $importe)";
    $idCajaBanco = getSQLResultSet($Ssql);

    if ($idCajaBanco) {
      foreach ($aplicadoDocVenta as $key => $value) {
        $Ssql = "INSERT INTO Cb_CajaBancoDet (IdCajaBanco, IdDocDet, Importe, Tipo) VALUES($idCajaBanco, " . $value['idDocDet'] . ", " . $value['importe'] . ", 'VE')";
        getSQLResultSet($Ssql);
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
