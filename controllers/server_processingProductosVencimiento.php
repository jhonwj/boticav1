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
            $hashMovimiento = $_GET['hashMovimiento'];
            $idProducto = $_GET['idProducto'];
            $fechaVen = $_GET['fechaVen'];


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
