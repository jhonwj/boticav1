<?php
include_once '../clases/BnGeneral.php';
include_once '../clases/ClsBnCajaBanco.php';
include_once '../clases/ClsDtCajaBanco.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':

        if (isset($_GET['rango'])) {
            $result = fn_devolverCajaBancoPorFecha($_GET['IdCuenta'], $_GET['IdTipoOperacion'], $_GET['FechaIni'], $_GET['FechaFin']);
        } else {
            $result = ListarCajaBanco($_GET['IdCuenta'], $_GET['FechaDoc'],$_GET['UsuarioReg']);
        }
        
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
