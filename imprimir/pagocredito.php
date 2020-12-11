<?php
include_once('../views/validateUser.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/clases/BnGeneral.php");

if (isset($_GET['idCajaBanco'])) {
  $idCajaBanco = $_GET['idCajaBanco'];
  $result = fn_devolverCajaBanco($idCajaBanco);
  $cajaBanco = mysqli_fetch_assoc($result);
?>
<style>
  * {
    font-size: 3mm;
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
    padding: 0 4mm;
    box-sizing: border-box;
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
  .small {
    font-size: 11px;
  }
</style>


<div class="container">
    <!--<div class="center">BOTICA</div>-->
    <div class="center">DISMART E.I.R.L.</div>
    <!--<div class="center">RUC: 20393999544 </div>--><br>

    <div class="">COMROBANTE NRO: 001- <?php echo $cajaBanco['IdCajaBanco']; ?></div>
    <div class="">FECHA: <?php echo $cajaBanco['FechaDoc']; ?></div>

    <div class="separar"></div>
    <div>SR(ES) : <?php echo $cajaBanco['Cliente'] ?></div>
    <!--<div>RUC/DNI : <?php echo $cajaBanco['DniRuc']; ?></div>
    <div>DIR : <?php echo $cajaBanco['Direccion']; ?></div>-->
<br>
    <div class="productos">
    <table width="100%">
      <thead>
        <tr>
          <th class="cantidad">CANT</th>
          <th class="producto">PRODUCTO</th>
          <!--<th class="unitario">P/U</th>-->
          <th class="precio text-right">TOT</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td class="center">1</td>
            <td class="center"><?php echo $cajaBanco['Concepto'] ?></td>
            <td class="text-right"><?php echo $cajaBanco['Importe'] ?></td>
        </tr>
				<tr>
          <td></td>
          <td class="text-right">SUBTOTAL</td>
          <td class="text-right">S/.<?php echo number_format($cajaBanco['Importe'], 2); ?></td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right"><strong>TOTAL</strong></td>
          <td class="text-right">S/.<?php echo number_format($cajaBanco['Importe'], 2); ?></td>
        </tr>
      </tbody>
    </table>
    </div>

    <br>

    <!--<div class="center small">BIENES TRANSFERIDOS EN LA AMAZONIA</div>
    <div class="center small">PARA SER CONSUMIDOS EN LA MISMA</div>-->

</div>



<script>
  document.addEventListener('DOMContentLoaded', function() {
    //window.print()

    <?php
      if(!isset($_GET['preview'])) {
        if (isset($_GET['redirect'])) {
          echo 'window.print();';
          //echo 'window.location.href="' . $_GET['redirect'] . '";';
        } else {
          echo 'window.print();';
          //echo 'window.location.href="/views/V_VentaForm.php";';
        }
      }

    ?>
    //window.location.href="/views/V_VentaForm.php";

  })
</script>
<?php } ?>







<style>
.strike {
  display: block;
  text-align: center;
  overflow: hidden;
  white-space: nowrap;
}

.strike > span {
  position: relative;
  display: inline-block;
}

.strike > span:before,
.strike > span:after {
  content: "";
  position: absolute;
  top: 50%;
  width: 9999px;
  height: 1px;
  background: #000;
}

.strike > span:before {
  right: 100%;
  margin-right: 15px;
}

.strike > span:after {
  left: 100%;
  margin-left: 15px;
}
</style>