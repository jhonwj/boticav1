<?php
include_once("../clases/BnGeneral.php");
//include("../clases/DtGeneral.php");
include_once("../clases/helpers/Modal.php");
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title>Botica - Reimpresión de documentos</title>

 </head>
<?php include_once 'linker.php'; ?>
<script>
  $(document).ready(function() {
      $("#tableReimpresion").DataTable().destroy();
      var table4 = $("#tableReimpresion").DataTable({
        "bSort": false,
        "bProcessing": true,
        "sAjaxSource": "../controllers/server_processingReimpresion.php",
        "bPaginate":true,
        "sPaginationType":"full_numbers",
        "iDisplayLength": 5,
        "aoColumns": [
          { mData: 'idDocVenta' } ,
          { mData: 'FechaDoc' },
          { mData: 'DniRuc' },
          { mData: 'TipoDoc' },
          { mData: 'Cliente' },
          { mData: 'Total' },
          { mRender : function(data, type, row){
            return "<a href='/imprimir/index.php?IdDocVenta=" + row.idDocVenta + "&redirect=/views/ve_reimpresionDocumentos.php' class='btn btn-success'>" + row.TipoDoc + "</a>"
          }}
        ]
      });

  })
</script>
<body>

  <?php include("header.php"); ?>
  <div class="container">
  <!-- nuevo -->


  <div class="sTableProducto" class="table-responsive" style="overflow-x:auto">
   <table id="tableReimpresion" class="table table-striped table-bordered" style="">
     <thead>
       <th class="">IdDocVenta</th>
       <th>Fecha</th>
       <th>DNI / RUC</th>
       <th>Tipo Doc</th>
       <th>Cliente</th>
       <th>Total</th>
       <th>Reimprimir</th>
     </thead>
   </table>
  </div>


<!-- MODALES -->
  <div class="modal fade" id="ModalBuscarBloque" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Seleccionar Bloque de Producto</h4>
        </div>
        <div class="modal-body">
          <div class="sTableProductoCategoria">
            <table id="tableProductoBloque" class="table table-striped table-bordered">
              <thead>
               <th class="">#</th>
               <th>Bloque del Producto</th>
               <th>Porcentaje Minimo</th>
               <th>PorcentajeMax</th>
               <th>Editar</th>
              </thead>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="btnNuevaBloque" name="button">Nuevo <i class="fa fa-plus"></i></button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>