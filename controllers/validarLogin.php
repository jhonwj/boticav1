<?php
	//session_start();
	include_once '../clases/BnGeneral.php';

	$Usuario = $_POST["nameUser"];
	$Password = $_POST["namePass"];

	$isLogin = isLogin($Usuario, $Password);

	if ($isLogin) {
		$isLogin = mysqli_fetch_assoc($isLogin);
		if (empty($isLogin)) {
			$isLogin =  false;
		} else {
			$permisos = obtenerPermisos($isLogin['IdUsuarioPerfil']);
			$permisos = mysqli_fetch_all($permisos, MYSQLI_ASSOC);

			echo json_encode($permisos);
			exit();
			$_SESSION['user'] = $Usuario;
			$isLogin = true;
		}
	}


	$results = array(
		'isLogin' => $isLogin,
	);
	echo json_encode($results);
 ?>
