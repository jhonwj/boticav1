<?php

$productos = json_decode($_GET['productos']);
?>

<style>
  * {
    font-size: 13px;
    font-family: sans-serif;
  }
  body {
    display: block;
    margin: 8px;
  }
  td,
  th,
  tr,
  table {
    border-top: 1px solid black;
    border-collapse: collapse;
  }
  .container {
    width: 100%;
    max-width: 100%;
  }

  .center {
    text-align: center;
  }
  .separar {
    margin: 8px 0;
    border-top: 1px dashed #000;

  }
  td {
    line-height: 1.48em;
    font-size: 1em;
  }
  td, th {
    padding: 0 6px;
  }
  td.cantidad {
    text-align: center;
  }
  .text-right {
    text-align: right;
  }
  .float-right {
      float: right;
  }
  .small {
    font-size: 11px;
  }
  .firma {
      padding: 10px 30px;
      border-top: 1px dashed #ccc;
  }
</style>

<div class="container">
  <!--<div class="center">
    <img width="80px" src="../resources/images/delmancito.jpg"  /><br /><br />
  </div>-->
  <div class="center">ESPECIFICACIONES (No valido como receta médica)</div>
  <br />

  <div class="separar"></div>

  <br />
    <ol>
        <?php foreach($productos as $espec) { 
            $dosisPeso = $espec->dosisXPeso;
            $peso = $espec->peso;
            $concentracion = $espec->concentracion;
            $dosis = $espec->dosis;
            $unidadDosisPeso = $espec->unidadDosisPeso;
            $dosisDia = $espec->dosisDia;
            $nroDias = $espec->nroDias;

            $dosisMasa = $dosisPeso * $peso;
            $dosisVolumen = round(($dosisMasa * $concentracion)/$dosis, 1);
        ?>
        <li>
            <?php echo $espec->producto;?>  <br>
            - DOSIS: <?php echo $dosisVolumen ?> <?php echo $unidadDosisPeso; ?> Cada <?php echo 24/$dosisDia ?> Horas x <?php echo $nroDias ?> días 

        </li>
        <br>
        <?php } ?>
    </ol>
    <br><br><br><br>
    <div class="float-right firma">
        <?php echo date('d - m - Y')?>
    </div>
  </div>

