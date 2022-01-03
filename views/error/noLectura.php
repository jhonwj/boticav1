<?php

 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>DEMO FERRETERIA - Sin permisos de lectura</title>
  <meta charset="UTF-8">
 </head>
 <?php include_once $_SERVER["DOCUMENT_ROOT"] . '/views/linker.php'; ?>
<body>
  <?php include($_SERVER["DOCUMENT_ROOT"] . '/views/header.php'); ?>

  <div class="bt-panel">
    <div class="alert alert-danger" role="alert">
  Usted no puede leer esta sección
</div>
  </div>
</body>
