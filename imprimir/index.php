<?php
include_once('../views/validateUser.php');
include_once('../info.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/clases/BnGeneral.php");
require '../api/sunat/plugins/phpqrcode/qrlib.php';

if (isset($_GET['IdDocVenta'])) {
  $idDocVenta = $_GET['IdDocVenta'];

  $criterio = "Ve_DocVenta.IdDocVenta=$idDocVenta";
  $result = fn_devolverDocVenta($criterio, "");
  $docVenta = mysqli_fetch_assoc($result);

  $criterio="Ve_DocVentaDet.IdDocVenta=$idDocVenta";
  $resultDet = fn_devolverDocVentaDet($criterio, "Ve_DocVentaDet.IdDocVentaDet ASC");
  $productos = mysqli_fetch_all($resultDet, MYSQLI_ASSOC);

  if ($docVenta['CodSunat'] == 12) {
    include_once('ticket.php');
  } elseif ($docVenta['CodSunat'] == 03) {
    if ($docVenta['IdTipoDoc'] == 7) {
      include_once('boletaelectronica.php');
    } else {
      include_once('boletarojas.php');
    }
  } elseif ($docVenta['CodSunat'] == 01) {
    if ($docVenta['IdTipoDoc'] == 6 || $docVenta['IdTipoDoc'] == 8) { // FActura Electronica
      include_once('facturaelectronica.php');
    } else {
      include_once('facturarojas.php');
    }
  }elseif($docVenta['CodSunat'] == 07){
      include_once('notacredito.php');
  }
?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    //window.print()

    <?php
      if(!isset($_GET['preview'])) {
        if (isset($_GET['redirect'])) {
          echo 'window.print();';
          // echo 'window.location.href="' . $_GET['redirect'] . '";';
        } else {
          echo 'window.print();';
          // echo 'window.location.href="/views/V_VentaForm.php";';
        }
      }
      
    ?>
    //window.location.href="/views/V_VentaForm.php";

  })
</script>
<?php } ?>

<?php
if (isset($_GET['IdDocVent'])) {
  $idDocVenta = $_GET['IdDocVent'];
  $fecha = $_GET['fecha'];
  // $criterio = "vDocVen.idDocVenta=$idDocVenta";
  // $result = fn_devolverDocVentaDetEntrega($criterio);
  // $docVentaDetEntre = mysqli_fetch_assoc($result);



  $criterio = "vDocVen.idDocVenta=$idDocVenta AND vDocVenDetEnt.Fecha=$fecha";
  $result = fn_devolverDocVentaDetEntrega($criterio);
  $docVentaDetEntre = mysqli_fetch_assoc($result);
  
  $criterio2="vDocVen.idDocVenta=$idDocVenta AND vDocVenDetEnt.Fecha=$fecha";
  $resultDet2 = fn_devolverDocVentaDetEntregaDet($criterio2);
  $productos = mysqli_fetch_all($resultDet2, MYSQLI_ASSOC);

  include_once('porEntregar.php');
}
?>






<?php 
  if(isset($_GET['hashMovimiento'])) {
    $hash = $_GET['hashMovimiento'];
    $result = fn_devolverMovimiento($hash);
    $movimiento = mysqli_fetch_assoc($result);
    
    $resultDet = fn_devolverMovimientoDet($hash);
    $movimientoDet = mysqli_fetch_all($resultDet, MYSQLI_ASSOC);
    //var_dump($movimiento);
    //var_dump($movimientoDet);
    include_once('remisionticket.php');

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


<!-- OPEN PRINTER -->
<div id="printers">
  <a class="print no-print" href='/api/index.php/imprimirpdf/<?php echo $_GET['IdDocVenta']; ?>'>¡IMPRIMIR PDF!</a>
</div>
<br />
<div id="printer">
<a class="print no-print" href='neuroprinter://factura.vip' style="font-size: 2.5rem; margin-left:2px"> ¡IMPRIMIR POR BLUETOOTH!</a>
</div>
<script src="../resources/js/jquery-3.2.1.min.js"></script>

<script>
  $(document).ready(function() {

    fetch('/api/index.php/empresa/id/1')
      .then(function(response) {
        return response.json();
      })
      .then(function(empresa) {
        var ruta = 'neuroprinter://factura.vip?iddocventa=<?php echo $idDocVenta ?>&token=' + empresa.APPTOKEN;

        $('#printer .print').attr('href', ruta)
      });
  })
</script>
<div class="print no-print">
  <br>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <button style="cursor: pointer; margin-left:calc(4vw); background-color:#25d366; border: 1px #25d366 solid; border-radius: 5px; padding: 3px; color:#FFF; font-size:30px; display: flex;
    justify-content: center; align-items: center;" onclick="enviarNumero()"><i class="fa fa-whatsapp whatsapp-icon"></i><b style="margin-left: 5px;"> Enviar a Whatsapp</b></button>
</div>
<script>
  async function enviarNumero() {
    let numero = prompt("Ingrese un número de 9 digitos");
    if((''+numero).trim().length!=9){

      return alert("Numero no valido")
    }
    try {
      const url = '/api/index.php/enviar/whatsapp/comprobante/<?php echo $_GET['IdDocVenta']; ?>?numero=51'+(''+numero).trim();
      const response = await fetch(url);
      const data = await response.json();
      if(data.success){
        alert("Comprobante enviado")
      }else{
        alert("Ha ocurrido un error")
      }

    } catch (error) {
      alert("Ha ocurrido un error")
    }  
  }
</script>
<style>
  #printer {
    width: 20%;
    text-align: center; 
  }
  #printer .print {
    font-size: 4mm;
  }
  @media print {
    .no-print {
      display: none;
    }
  }
</style>



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