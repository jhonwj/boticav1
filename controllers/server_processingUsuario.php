<?php

include_once '../clases/BnGeneral.php';
include_once '../clases/DtGeneral.php';

 $method = $_SERVER['REQUEST_METHOD'];

 switch ($method) {
     case 'GET':
			 	$result = ListarUsuario();
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
             // Insertar usuario
						 $usuario = $_POST['Usuario'];
						 $password = $_POST['Password'];
             $nombreUsuario = $_POST['NombreUsuario'];
             $update = $_POST['Update'];

             $usuario = fn_guardarUsuario($_POST);
             if ($usuario) {
                 $result['success'] = 'Se añadió o actualizó un nuevo usuario ';
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
