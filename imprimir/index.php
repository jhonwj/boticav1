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
    include_once('boletarojas.php');
  } elseif ($docVenta['CodSunat'] == 01) {
    include_once('facturarojas.php');
  }
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
          //echo 'window.print();';
          //echo 'window.location.href="/views/V_VentaForm.php";';
        }
      }
      
    ?>
    //window.location.href="/views/V_VentaForm.php";

  })
</script>
<?php } ?>








<?php 
  if(isset($_GET['hashMovimiento'])) {
    $hash = $_GET['hashMovimiento'];
    $result = fn_devolverMovimiento($hash);
    $movimiento = mysqli_fetch_assoc($result);
    
    $resultDet = fn_devolverMovimientoDet($hash);
    $movimientoDet = mysqli_fetch_all($resultDet, MYSQLI_ASSOC);
    //var_dump($movimiento);
    //var_dump($movimientoDet);
    include_once('remision.php');

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
          //echo 'window.print();';
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





<?php 
  if(isset($_GET['especificacion'])) {
   
    include_once('especificacion.php');

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
          //echo 'window.print();';
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