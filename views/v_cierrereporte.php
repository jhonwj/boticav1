
<html>
<head>
	<title>Reporte Cierre de Caja</title>
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
    $src = "/nuevo/index.html#/reportes/reportecierrecaja";
?>
<iframe src="<?php echo $src; ?>" width="100%" frameborder="0"  onload="resizeIframe(this)"></iframe>


</html>