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
         if (isset($_POST['update'])) {
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
         } elseif (isset($_POST['delete'])) {
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
            $mensaje = $_POST['mensaje'];
            $nuevosProductos = json_decode($_POST['nuevosProductos'], true);

            $result = [];
            foreach ($nuevosProductos as $key => $value) {
              $idProducto = $value['IdProducto'];
              $precioCosto = $value['PrecioCosto'];
              $porcentajeUtilidad = $value['PorcentajeNuevo'];
              $precioContado = $value['PrecioVentaNuevo'];
              if ($idProducto) {
                if(fn_actualizarRegistro('Gen_Producto', [
                  'PrecioCosto' => "$precioCosto",
                  'PorcentajeUtilidad' => $porcentajeUtilidad,
                  'PrecioContado' => $precioContado
                ], ['IdProducto', '=', $idProducto])) {
                  $result['success'] = 'Se actualizaron los precios';
                } else {
                  $result['error'] = 'Ha ocurrido un error, vuelva a intentarlo';
                }
              }
            }

            echo json_encode($result);

            break;
         }

     default:
         # code...
         break;
 }


  ?>
