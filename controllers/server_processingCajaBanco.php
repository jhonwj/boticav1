<?php
include_once '/../clases/ClsBnCajaBanco.php';
include_once '/../clases/ClsDtCajaBanco.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $result = ListarCajaBanco($_GET['IdCuenta'], $_GET['FechaDoc']);
        $data = array();
        while ($rows = mysql_fetch_assoc($result)) {
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
        if ($_POST['update']) {
            // Actualizar caja y banco


            break;
        } elseif ($_POST['delete']) {
            // Eliminar caja y Banco

            $id = $_POST['id'];
            $title = $_POST['title'];
            $result = [];

            if (fn_eliminarCajaBanco($id)) {
                $result['success'] = 'Se elimino ' . $title . ' correctamente';
            } else {
                $result['error'] = 'No se pudo eliminar ' . $title;
            }
            echo json_encode($result);

            break;
        } else {
            // Insertar caja y banco

            $concepto = $_POST['Concepto'];

            $cajaBanco = fn_guardarCajaBanco($_POST);
            if ($cajaBanco) {
                $result['success'] = 'Registro ' . $concepto . ' Insertado correctamente';
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
