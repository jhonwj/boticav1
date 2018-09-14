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
		obj.style.height = ($( window ).height() -  70) + 'px';
  }
</script>

<?php
if (isset($_GET['idPreOrden']) && isset($_GET['idCliente'])) {
    $src = "/nuevo/index.html#/ventas?idPreOrden=" . $_GET['idPreOrden'] . "&idCliente=" . $_GET['idCliente'];
}else {
    $src = "/nuevo/index.html#/ventas";
}
?>

<iframe id="iframeMovimiento" src="<?php echo $src; ?>" width="100%" frameborder="0" onload="resizeIframe(this)"></iframe>


</html>
