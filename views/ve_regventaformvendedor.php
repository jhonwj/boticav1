<?php
include_once("../clases/helpers/Modal.php");
?>

<html>
<head>
	<title>Inventario</title>
</head>
<?php include_once 'linker.php'; ?>

<style media="screen">
</style>


<body>
<?php include("header.php"); ?>
<script>
  function resizeIframe(obj) {
		//obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
		obj.style.height = ($( window ).height() -  70) + 'px';
  }
</script>

<iframe id="iframeMovimiento" src="/nuevo/index.html#/reportes/ventas" width="100%" frameborder="0" onload="resizeIframe(this)"></iframe>


</html>
