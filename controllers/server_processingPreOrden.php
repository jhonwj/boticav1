<?php
include_once '../clases/BnGeneral.php';
include_once '../clases/DtGeneral.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['idPreOrden'])) {
          $result = fn_listarProductosPreOrden($_GET['idPreOrden']);
          $data = array();
          while ($rows = mysqli_fetch_assoc($result)) {
              $data[] = $rows;
          }

          echo json_encode($data);
          break;
        }

        $result = fn_listarPreOrden();
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
            // Actualizar caja y banco


            break;
        } elseif (isset($_POST['delete'])) {
            // Eliminar PreOrden
            $idPreOrden = $_POST['idPreOrden'];
            $eliminar = fn_eliminarPreOrden($idPreOrden);

            if ($eliminar) {
                $result['success'] = 'Pre orden eliminado correctamente';
            } else {
                $result['error'] = 'Ha ocurrido un error, vuelva a intentarlo más tarde';
            }
            echo json_encode($result);
            break;
        } else {
            // Insertar caja y banco
            $idCliente = $_POST['idCliente'];
            $productos = json_decode($_POST['productos'], true);

            $preOrden = fn_guardarPreOrden($idCliente, $productos);


            if ($preOrden) {
                $result['idPreOrden'] = $preOrden;
                $result['success'] = 'Pre orden almacenado correctamente';
            } else {
                $result['error'] = 'Ha ocurrido un error, vuelva a intentarlo más tarde';
            }
            echo json_encode($result);

            break;
        }

    default:
        # code...
        break;
}


 ?>
