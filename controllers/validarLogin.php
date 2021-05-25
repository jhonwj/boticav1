<?php
	session_start();
	include_once($_SERVER["DOCUMENT_ROOT"] . "/models/DBManager.php");
	//include_once '../clases/BnGeneral.php';

	function isLogin($user = '', $pass = '') {
		$Ssql = "SELECT * FROM Seg_Usuario WHERE Usuario='$user' AND Password='$pass'";
		//echo json_encode($Ssql);exit();

		return getSQLResultSet($Ssql);
	}

	function obtenerPermisos($idUsuarioPerfil) {
		$Ssql = "SELECT Seg_UsuarioModulo_has_UsuarioPerfil.IdUsuarioPerfil, Seg_UsuarioModulo.UsuarioModulo, Seg_UsuarioModulo_has_UsuarioPerfil.Lectura, Seg_UsuarioModulo_has_UsuarioPerfil.Escritura
			FROM Seg_UsuarioModulo_has_UsuarioPerfil
			INNER JOIN Seg_UsuarioModulo ON Seg_UsuarioModulo_has_UsuarioPerfil.IdUsuarioModulo = Seg_UsuarioModulo.IdUsuarioModulo
			WHERE Seg_UsuarioModulo_has_UsuarioPerfil.IdUsuarioPerfil = $idUsuarioPerfil";
		return getSQLResultSet($Ssql);
	}

	$Usuario = addslashes($_POST["nameUser"]);
	$Password = addslashes($_POST["namePass"]);

	$isLogin = isLogin($Usuario, $Password);
	if ($isLogin) {
		$isLogin = mysqli_fetch_assoc($isLogin);
		//echo json_encode($isLogin);
		//exit();

		if (empty($isLogin)) {
			$isLogin =  false;
		} else {
			$idUsuarioPerfil = 0;
			$permisos = obtenerPermisos($isLogin['IdUsuarioPerfil']);
			$permisos = mysqli_fetch_all($permisos, MYSQLI_ASSOC);
			$nuevosPermisos = [];
			foreach ($permisos as $key => $permiso) {
				$idUsuarioPerfil = $permiso['IdUsuarioPerfil'];
				$nuevosPermisos[$permiso['UsuarioModulo']] = array(
					'Lectura' => $permiso['Lectura'],
					'Escritura' => $permiso['Escritura']
				);
			}

			$_SESSION['user'] = $Usuario;
			$_SESSION['permisos'] = $nuevosPermisos;
			$_SESSION['start'] = time();
			$_SESSION['idPerfil'] = $idUsuarioPerfil;
      	// $_SESSION['expire'] = $_SESSION['start'] + (5 * 60 * 60);

			$isLogin = true;
		}
	}


	$results = array(
		'isLogin' => $isLogin,
	);
	echo json_encode($results);
 ?>
