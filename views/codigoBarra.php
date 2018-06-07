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
                font-size: 2.2mm!important;
                font-family: sans-serif !important;
            }
            /* ESTILOS COMPONENTES */
            #barcode {
                width: 109mm;
                margin-left: 1mm;
            }
            #barcode .negro {
                background-color: #000000 !important;
                -webkit-print-color-adjust: exact; 
                color: #fff!important;
                padding: 0 .5mm;
                margin-right: 1mm;
                font-weight: bold !important;
            }
            #barcode .direccion {
                font-size: .2cm;
            }
            .text-right: {
                text-align: right;
            }
            .barcode-box {
                border-radius: 2mm;
                margin: .4mm 2.5mm;
                padding: 1mm;
                border: .5mm solid #000;
                display: inline-block;
                width: 49mm;
                height: 36mm;
                overflow: hidden;
            }
            .barcode-right {
                text-align: right;
                float: right;
            }
            .barcode-right label {
                margin-bottom: 0;
                font-size: 2.5mm !important;
                text-align: center;
                width: 8mm;
                padding: .2mm .5mm;
                border: .1mm solid #000;
            }
            .barcode-right p {
                font-size: 1.8mm !important;
                margin: 0;
            }
            .barcode-image {
                text-align: center;
            }
            #barcode .item {
                display: inline-block;
                width: 13mm;
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
    <codigo-barra-lista hash-movimiento="<?php echo $_GET['hash']?>"></codigo-barra-lista>
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