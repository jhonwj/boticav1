<?php
include_once("../clases/helpers/Modal.php");
?>

<html>
<head>
	<title>Inventario</title>
</head>
<?php include_once 'linker.php'; ?>

<style media="screen">
	#modalAddProveedor{
		/*/width: 700px; !important*/
	}
</style>


<body>
<?php include("header.php"); ?>
<script>
  function resizeIframe(obj) {
    //obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
  }
</script>

<iframe src="/nuevo/index.html#/productos" width="100%" height="700px" frameborder="0" scrolling="no"  onload="resizeIframe(this)"></iframe>


</html>
