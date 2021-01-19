<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Imprimir codigo de barra</title>
    <?php include_once 'linker.php'; ?>
    <style>
            @page {
                size: 11cm 4cm portrait;
                margin 0!important;
            }
            body {
                color: #000000!important;
                margin: 0;
                font-size: 1.8mm!important;
                font-family: sans-serif !important;
            }
            /* ESTILOS COMPONENTES */
            #barcode {
                width: 106mm;
                margin-left: 1mm;
            }
            #barcode .negro {
                /* display: none !important; */
                background-color: #000000 !important;
                -webkit-print-color-adjust: exact; 
                color: #fff!important;
                padding: 0 .5mm;
                margin-right: 1mm;
                font-weight: bold !important;
            }
            #barcode .code {
                margin-bottom: 2.5mm;
                height: 2mm;
            }
            #barcode .direccion {
                font-size: 1.5mm;
                z-index: 1;
                position: absolute;
                top: .4mm;
                right: 1mm;
                width: 20mm;
                line-height: 1.7mm;
            }
            .float-left {
                float: left;
            }
            .text-right: {
                text-align: right;
            }
            .barcode-box {
                border-radius: 2mm;
                margin: .9mm 1.5mm .3mm;
                padding: 0.2mm;
                border: .5mm solid #000;
                display: inline-block;
                width: 32mm;
                height: 20mm;
                overflow: hidden;
                position: relative;
            }
            .barcode-left {
                line-height: 1.8mm;
                padding-top: 3mm;
            }
            .barcode-right {
                text-align: right;
                float: right;
            }
            .barcode-right label {
                margin-bottom: 0;
                font-size: 2.9mm !important;
                text-align: center;
                width: 12mm;
                /* padding: .2mm .5mm; */
                border: .1mm solid #000;
            }
            .barcode-right p {
                font-size: 1.4mm !important;
                margin: 0;
            }
            .barcode-image {
                text-align: center;
            }
            .barcode-logo {
                position: absolute;
                top: 0;
                z-index: -1;
                left: 0;
            }
            #barcode .item {
                display: inline-block;
                width: 6mm;
                box-sizing: border-box;
            }
            #barcode .item.derecha {
                width: 15mm;
            }
            @media print { 
                body {
                }
            }
    </style>
    
</head>
<body>

<div id="app">
    <?php if (isset($_GET['idProducto'])) : ?>
        <?php $cantidad = isset($_GET['cantidad']) ? $_GET['cantidad'] : 10 ?>
        <codigo-barra-lista id-producto="<?php echo $_GET['idProducto']?>" cantidad="<?php echo $cantidad ?>"></codigo-barra-lista>            
    <?php else : ?>
        <codigo-barra-lista hash-movimiento="<?php echo $_GET['hash']?>"></codigo-barra-lista>
    <?php endif; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/@xkeshi/vue-barcode@0.2.0/dist/vue-barcode.min.js"></script>
<script type="module" src="../components/CodigoBarraLista.js"></script>
<script type="module">
    import CodigoBarraLista from '../components/CodigoBarraLista.js';
    
    Vue.component('barcode', VueBarcode);

    new Vue({
      el: '#app',
      components: {
        CodigoBarraLista
      }
    });
</script>
</body>
</html>