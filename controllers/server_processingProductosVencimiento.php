<?php
include_once '../clases/BnGeneral.php';
include_once '../clases/DtGeneral.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':

        $result = fn_devolverProductosProximosAVencer($_GET['FechaIni'], $_GET['FechaFin']);
        
        $data = array();
        while ($rows = mysqli_fetch_assoc($result)) {
            $data[] = $rows;
        }
        $results = array(
         "sEcho" => 1,
         "iTotalRecords" => count($data),
         "iTotalDisplayRecords" => count($data),
         "aaData" => $data);

        echo json_encode($results);
        break;

    case 'POST':
        if (isset($_POST['update'])) {
            // Actualizar Fecha vencimiento
            $hashMovimiento = $_POST['hashMovimiento'];
            $idProducto = $_POST['idProducto'];
            $fechaVen = $_POST['fechaVen'];

            if (fn_actualizarVencimiento($hashMovimiento, $idProducto, $fechaVen)) {
                $result['success'] = 'Se actualizó la fecha de vencimiento correctamente';
            } else {
                $result['error'] = 'No se puede actualizar la fecha de vencimiento';
            }

            echo json_encode($result); exit();

            break;
        } elseif (isset($_POST['delete'])) {
            // Eliminar Fecha vencimiento


            break;
        } else {

            break;
        }

    default:
        # code...
        break;
}


 ?>
