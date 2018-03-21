<?php
include_once($_SERVER["DOCUMENT_ROOT"] . '/views/validateUser.php');

//var_dump($docVenta);

$movimientoFecha = $movimiento['MovimientoFecha'];
$fechaStock = $movimiento['FechaStock'];

$partidaDist = $movimiento['PartidaDist'];
$partidaProv = $movimiento['PartidaProv'];
$partidaDpto = $movimiento['PartidaDpto'];
$llegadaDist = $movimiento['LlegadaDist'];
$llegadaProv = $movimiento['LlegadaProv'];
$llegadaDpto = $movimiento['LlegadaDpto'];

$destinoRS = $movimiento['DestinatarioRazonSocial'];
$destinoRUC = $movimiento['DestinatarioRUC'];

$transporteNumPlaca = $movimiento['TransporteNumPlaca'];
$transporteNumContrato = $movimiento['TransporteNumContrato'];
$transporteNumLicencia = $movimiento['TransporteNumLicencia'];
$transporteRazonSocial = $movimiento['TransporteRazonSocial'];
$transporteRUC = $movimiento['TransporteRUC'];

$docVentaSerie = $movimiento['DocVentaSerie'];
$docVentaNumero = $movimiento['DocVentaNumero'];
$limitProducto = 7;


?>
<style>
  body {
    font-size: 22px;
    margin: 0;
    font-family: sans-serif;
    letter-spacing: 4px;

  }
  .container {
    margin-top: 7.7em;
    width: 1200px;
    margin-left: 25px;
  /*  border: 1px solid red;*/
  }
  td {
    line-height: 1.48em;
    font-size: 1em;
    /*border: 1px solid black;*/
  }
  .emision {
    width: 690px;
  }
  .dia {
    margin-left: 220px;
  }
  .mes {
    width: 300px;
  }
  .mes span {
    margin-left: 10px;
  }
  .anio {
    width: 50px;

  }
  .anio span {
    margin-left: 15px;
  }
  .cliente {
    margin-left: 140px;
  }
  .ruc {
    width: 320px;
  }
  .ruc span {
    margin-left: 60px;
  }
  .totales {
    float: right;
    width: 310px;
  }
  .totales span {
    margin-left: 140px;
  }

  /* Estilos para productos */
  .productos {
  /*  height: 8em;*/
  }
  .productos {
    padding-top: 1em;
    box-sizing: border-box;
  }
  .productos .cantidad {
    width: 70px;
    text-align: center;
  }
  .productos .detalle span{
    margin-left: 20px;
  }
  .productos .precio {
    width: 100px;
  }
  .productos .pTotal {
    width: 174px;
  }
  .productos .pTotal span {
    margin-left: 10px;
  }

  .footer {
    width: 890px;
    float: left;
    margin-top: 5px;
  }
  .footer .son {
    margin-left: 80px;
  }
  .footer .user, .footer .fecha {
    margin-left: 230px;
  }
  .footer .small {
    display: block;
    font-size: 13px;
  }
  .footer .small:first-child {
    margin-top: 20px;
  }
</style>
<div class="container">

    <table width="100%">
        <tr>
            <td>
                <span class=""><?php echo $movimientoFecha ?></span>
            </td>
            <td>
                <span class=""><?php echo $fechaStock ?></span>
            </td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td>
                <span class=""><?php echo $partidaDist ?></span>
            </td>
            <td class="es">
                <span><?php echo $partidaProv ?></span>
            </td>
            <td class="">
                <span ><?php echo $partidaDpto; ?></span>
            </td>
            <td>
                <span class=""><?php echo $llegadaDist ?></span>
            </td>
            <td class="">
                <span><?php echo $llegadaProv ?></span>
            </td>
            <td class="">
                <span ><?php echo $llegadaDpto; ?></span>
            </td>
        </tr>
    </table>
  <table width="100%">
    <tr>
      <td>
        <span class=""><?php echo $destinoRS; ?></span>
      </td>
      <td class="">
        <span><?php echo $destinoRUC; ?></span>
      </td>
    </tr>
  </table>
  <table width="100%">
    <tr>
      <td>
        <span class=""><?php echo $transporteNumPlaca; ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span class=""><?php echo $transporteNumContrato; ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span class=""><?php echo $transporteNumLicencia; ?></span>
      </td>
    </tr>
  </table>
  <table width="100%">
    <tr>
      <td>
        <span class=""><?php echo $transporteRazonSocial; ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span class=""><?php echo $transporteRUC; ?></span>
      </td>
    </tr>
  </table>
  <div class="productos">
    <table width="100%">
      <?php
      $filas = 0;
      foreach ($movimientoDet as $key => $producto) { ?>
          <tr>
            <td class="detalle">
              <span><?php echo mb_strimwidth($producto['Producto'], 0, 60, '...') ?></span>
            </td>
            <td class="cantidad">
              <span><?php echo $producto['Cantidad']; ?></span>
            </td>
            <td class="medida">
              <span><?php echo $producto['ProductoMedicion'] ?></span>
            </td>
            <td class="pesoTotal">
              <span><?php echo $producto['PesoTotal'] ?></span>
            </td>
          </tr>
      <?php
      $filas += 1;
      }

      while ($filas < $limitProducto) {
      ?>
      <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
      <?php
        $filas += 1;
      }

     ?>
    </table>
  </div>



  <table class="footer">
    <tr>
      <td>
        <span></span>
      </td>
    </tr>
    <tr>
      <td>
        <span><?php
            if ($movimiento['IdDocVenta']) {
                echo $docVentaSerie . '-' . $docVentaNumero;
            }
        ?></span>
      </td>
    </tr>
    <tr>
      <td>
        <span></span>
      </td>
    </tr>
  </table>
</div>
