<?php
	session_start();

	include_once '../clases/BnGeneral.php';

	$Usuario = $_POST["nameUser"];
	$Password = $_POST["namePass"];

	$isLogin = isLogin($Usuario, $Password);

	if ($isLogin) {
		$isLogin = mysqli_fetch_all($isLogin);
		if (empty($isLogin)) {
			$isLogin =  false;
		} else {
			$isLogin = true;
			$_SESSION['user'] = $Usuario;
		}
	}


	$results = array(
		'isLogin' => $isLogin,
	);
	echo json_encode($results);
 ?>
