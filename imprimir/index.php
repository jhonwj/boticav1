<?php
include_once('../views/validateUser.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/clases/BnGeneral.php");

if (isset($_GET['IdDocVenta'])) {
  $idDocVenta = $_GET['IdDocVenta'];

  $criterio = "Ve_DocVenta.IdDocVenta=$idDocVenta";
  $result = fn_devolverDocVenta($criterio, "");
  $docVenta = mysqli_fetch_assoc($result);

  $criterio="Ve_DocVentaDet.IdDocVenta=$idDocVenta";
  $resultDet = fn_devolverDocVentaDet($criterio, "Gen_Producto.Producto desc");
  $productos = mysqli_fetch_all($resultDet, MYSQLI_ASSOC);

  if ($docVenta['CodSunat'] == 12) {
    include_once('ticket.php');
  } elseif ($docVenta['CodSunat'] == 03) {
    include_once('boleta.php');
  } elseif ($docVenta['CodSunat'] == 01) {
    include_once('factura.php');
  }
?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    //window.print()

    <?php
      if (isset($_GET['redirect'])) {
        //echo 'window.print();';
        //echo 'window.location.href="' . $_GET['redirect'] . '";';
      } else {
        //echo 'window.print();';
        //echo 'window.location.href="/views/V_VentaForm.php";';
      }
    ?>
    //window.location.href="/views/V_VentaForm.php";

  })
</script>

<?php } ?>