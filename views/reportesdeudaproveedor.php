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
            var w = window,
                d = document,
                e = d.documentElement,
                g = d.getElementsByTagName('body')[0],
                x = w.innerWidth || e.clientWidth || g.clientWidth,
                y = w.innerHeight|| e.clientHeight|| g.clientHeight;

        obj.style.height = (y -  70) + 'px';
  }
</script>

<iframe id="iframeMovimiento" src="/nuevo/index.html#/reportes/deudaproveedores" width="100%" frameborder="0" onload="resizeIframe(this)"></iframe>


</html>
