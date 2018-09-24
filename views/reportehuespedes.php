<?php
include_once("../clases/helpers/Modal.php");

$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
if($pageWasRefreshed && isset($_GET['idPreOrden'])) {
	header("Location: /views/V_VentaForm.php");
   exit();
}
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
    $src = "/nuevo/index.html#/reportes/huespedes";
?>

<iframe src="<?php echo $src; ?>" width="100%" frameborder="0" onload="resizeIframe(this)"></iframe>


</html>
