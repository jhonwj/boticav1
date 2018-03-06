<?php
session_start();

if (!isset($_SESSION['user'])) {
  ?>
  <script>
    window.location.href = "/"
  </script>
  <?php
    //header('Location: ' . 'http://' . $_SERVER['HTTP_HOST']);
    exit();
}

$modulo = basename($_SERVER['PHP_SELF']);
$permisos = $_SESSION['permisos'];
$permiso = isset($permisos[$modulo]) ? $permisos[$modulo] : false ;

if ($permiso) {
  if ($permiso['Lectura'] == 0) {
    include_once('error/noLectura.php');
    exit();
  }

  ?>
  <script>
    sessionStorage.clear();
    sessionStorage.setItem('Escritura', <?php echo json_encode($permiso['Escritura']); ?>)
  </script>
  <?php


}else {
  $parent = explode("/", $_SERVER['PHP_SELF']);
  $parent = $parent[1];

  if ($parent != 'controllers') {
    include_once('error/noAcceso.php');
    exit();
  }
}
?>
