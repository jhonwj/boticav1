<?php
include_once('../views/validateUser.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/clases/BnGeneral.php");

if (isset($_GET['IdProforma'])) {
  $idProforma = $_GET['IdProforma'];

  $result = fn_devolverProforma($idProforma);
  $docVenta = mysqli_fetch_assoc($result);

  $resultDet = fn_devolverProformaDet($idProforma);
  $productos = mysqli_fetch_all($resultDet, MYSQLI_ASSOC);

  include_once('proformaticket.php');

  ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    //window.print()

    <?php
      if(!isset($_GET['preview'])) {
        if (isset($_GET['redirect'])) {
          //echo 'window.print();';
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
<?php 
}
?>