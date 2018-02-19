<?php

include_once '../clases/BnGeneral.php';



 ?>
 <?php
 include_once '../clases/BnGeneral.php';
 include_once '../clases/DtGeneral.php';

 $method = $_SERVER['REQUEST_METHOD'];

 switch ($method) {
     case 'GET':
        $result = fn_devolverListaProductosPorBloque($_GET['bloque'], $_GET['porcentaje']);

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
         if ($_POST['update']) {
             // Actualizar caja y banco
             
             $mensaje = $_POST['mensaje'];

             $result = [];
             var_dump($_POST);
             exit();
             if (fn_actualizarRegistro($tabla, $campos, $where)) {
                 $result['success'] = $mensaje['success'];
             } else {
                 $result['error'] = $mensaje['error'];
             }
             echo json_encode($result);

             break;
         } elseif ($_POST['delete']) {
             // Eliminar caja y Banco

             /*$id = $_POST['id'];
             $title = $_POST['title'];
             $result = [];

             if (fn_eliminarCajaBanco($id)) {
                 $result['success'] = 'Se elimino ' . $title . ' correctamente';
             } else {
                 $result['error'] = 'No se pudo eliminar ' . $title;
             }
             echo json_encode($result);*/

             break;
         } else {
             // Insertar caja y banco

             /*$concepto = $_POST['Concepto'];

             $cajaBanco = fn_guardarCajaBanco($_POST);
             if ($cajaBanco) {
                 $result['success'] = 'Registro ' . $concepto . ' Insertado correctamente';
             } else {
                 $result['error'] = 'Ha ocurrido un error, vuelva a intentarlo más tarde';
             }
             echo json_encode($result);9*/

             break;
         }

     default:
         # code...
         break;
 }


  ?>
