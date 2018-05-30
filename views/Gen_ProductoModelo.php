
<html>
<head>
	<title>Buscar Productos vencidos</title>
    <?php include_once 'linker.php'; ?>
    <?php include("header.php"); ?>
</head>
<body>

<div id="app">
</div>


<script type="module" src="../components/ProductForm.js"></script>
<script type="module" src="../components/ProveedorForm.js"></script>
<script type="module">
    import ProductoForm from '../components/ProductForm.js';
    import ProveedorForm from '../components/ProveedorForm.js';

    new Vue({
      el: '#app',
      components: {
        ProductoForm,
        ProveedorForm
      }
    });
</script>

</body>
</html>
