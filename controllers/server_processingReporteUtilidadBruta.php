<?php
include_once '../clases/BnGeneral.php';
include_once '../clases/DtGeneral.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':

        $result = fn_listarReporteUtilidadBruta($_GET['fechaInicio'], $_GET['fechaFin']);
        $data = array();
        while ($rows = mysqli_fetch_assoc($result)) {
            $data[] = $rows;
        }


        echo json_encode($data);
        break;

    case 'POST':
        if (isset($_POST['update'])) {
            // Actualizar caja y banco

            break;
        } elseif (isset($_POST['delete'])) {
            // Eliminar caja y Banco

            break;
        } else {
            // Insertar caja y banco

            break;
        }

        break;

    default:
        # code...
        break;
}


 ?>
