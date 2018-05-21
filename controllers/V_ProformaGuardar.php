<?php
include_once '../clases/BnGeneral.php';
include_once '../clases/DtGeneral.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':

       
        break;

    case 'POST':
        if (isset($_POST['update'])) {
            // Actualizar caja y banco


            break;
        } elseif (isset($_POST['delete'])) {
            
            break;
        } else {
            // Insertar proforma

            $proforma = fn_guardarProforma();
            if ($proforma) {
                $result['success'] = 'Proforma generada correctamente';
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
