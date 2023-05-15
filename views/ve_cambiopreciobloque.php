<?php
include_once("../clases/BnGeneral.php");
//include("../clases/DtGeneral.php");
include_once("../clases/helpers/Modal.php");
include_once('../info.php');
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title><?php echo NOMBRE_SISTEMA ?> - Producto</title>

 </head>
<?php include_once 'linker.php'; ?>
<script>
  $(document).ready(function() {
    function ListarBloque(){
      $("#tableProductoBloque").DataTable().destroy();
      var table4 = $("#tableProductoBloque").DataTable({
        "bProcessing": true,
        "sAjaxSource": "../controllers/server_processingBloque.php",
        "bPaginate":true,
        "sPaginationType":"full_numbers",
        "iDisplayLength": 5,
        "aoColumns": [
          { mData: 'IdBloque' } ,
          { mData: 'Bloque' },
          { mData: 'PorcentajeMin' },
          { mData: 'PorcentajeMax' },
          { mRender : function(data, type, row){
            return "<a onclick='EditarBloque("+ row.IdBloque +");' class='btn'><i class='fa fa-pencil'></i></a>"
          }}
        ]
      });
    }

    function guardarNuevosProductos() {
      var xhr = $.ajax({
        url: "../controllers/server_processingProductosPorBloque.php",
        type: "post",
        dataType: 'json',
        data: {
          nuevosProductos:  JSON.stringify(window.nuevosProductos),
          mensaje: {
              'success': 'Se actualizaron los productos correctamente',
              'Error': 'No se han podido actualizar los productos'
          }
        },
        success: function(respuesta){
          if (respuesta.success) {
              $.notify({
                  icon: 'fa fa-check',
                  message: respuesta.success
              }, {
                  type: 'success'
              });
          } else {
              $.notify({
                  icon: 'fa fa-exclamation',
                  message: respuesta.error
              }, {
                  type: 'danger'
              });
          }
          $('#guardarNuevosProductos').attr('disabled', false)
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          $('#guardarNuevosProductos').attr('disabled', false)

          alert("Status: " + textStatus); alert("Error: " + errorThrown);
        }
      });
      console.log(xhr);
    }

    function ListarProductosGenerados() {
      $("#tableProducto").DataTable().destroy();
      $("#tableProducto").DataTable({
        "bProcessing": true,
        //"responsive" : true,
        "sAjaxSource": "../controllers/server_processingProductosPorBloque.php?bloque=" + $('#ProductoBloque').val() + "&porcentaje=" + $('#PorcentajeNuevo').val(),
        "bPaginate":true,
        "sPaginationType":"full_numbers",
        "iDisplayLength": 5,
        //"bAutoWidth": false,
        //"autoWidth" : false,
        //"bFilter": false,
        "aoColumns": [
            { mData: 'IdProducto' },
            { mData: 'Codigo' },
            { mData: 'Producto' },
            { mData: 'ProductoFormaFarmaceutica'},
            { mData: 'ProductoMarca'},
            { mData: 'PrecioCosto' },
            { mData: 'Utilidad' },
            { mData: 'PrecioContado' },
            { mData: 'PorcentajeNuevo' },
            { mData: 'PrecioVentaNuevo' }
        ],
        "drawCallback": function( settings ) {
            var api = this.api();
            var data = api.rows().data();
            var newData = [];

            for (var i = 0; i < data.length; i++) {
              newData[i] = data[i]
            }

            window.nuevosProductos = newData
        }
      });
    }

    $('#guardarNuevosProductos').click(function() {
      $.notify({
          icon: 'fa fa-exclamation',
          message: 'Espere un momento por favor...'
      }, {
          type: 'info'
      });
      $('#guardarNuevosProductos').attr('disabled', true)
      guardarNuevosProductos()
    })

    // Bloque
    $("#buscarBloque").click(function(event) {
      $("#ModalBuscarBloque").modal("show");
      ListarBloque();
      $('#tableProductoBloque tbody').on('click', 'tr', function () {
        $("#ProductoBloque").val($(this).children("td").eq(1).html());
        $("#porcentajeMinimo").val($(this).children("td").eq(2).html());
        $("#porcentajeMaximo").val($(this).children("td").eq(3).html());
        $("#PorcentajeNuevo").val($(this).children("td").eq(2).html());

        $("#ModalBuscarBloque").modal("hide");
      });
    });

    // Generar Productos
    $('#generarUtilidad').click(function(event) {
      ListarProductosGenerados()
    })

  })
</script>
<body>

  <?php include("header.php"); ?>
  <div class="container">
  <!-- nuevo -->
  <div class="" style="margin-left:10px; margin-right:10px;">
    <div class="row">
      <div class="col-xs-6 col-md-6">
        <div class="form-group" style="margin-bottom:20px;">
          <label>Seleccione Bloque</label>
          <div class="form-inline">
            <input type="text" class="form-control" id="ProductoBloque" readonly  name="productobloque" placeholder="Bloque del Producto">
          <button type="button" id="buscarBloque" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
          </div>
        </div>
      </div>
      </div>
      <div class="row">
        <div class="col-xs-6 col-md-6">
          <div class="form-group" style="margin-bottom:20px;">
            <label>Rango de Utilidad</label>
            <div class="form-inline">
              <input type="number" class="form-control" id="porcentajeMinimo"   name="productobloque" placeholder="Porcentaje Mínimo">
              <input type="number" class="form-control" id="porcentajeMaximo"   name="productobloque" placeholder="Porcentaje Máximo">
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-md-6">
          <div class="form-group" style="margin-bottom:20px;">
            <label>Establecer Utilidad</label>
            <div class="form-inline">
              <input type="number" class="form-control" id="PorcentajeNuevo"   name="productobloque" placeholder="Utilidad Nueva">
              <button type="button" id="generarUtilidad" class="btn btn-success">Generar</button>
            </div>
          </div>
      </div>
    </div>
  </div>

  <div class="sTableProducto" class="table-responsive" style="overflow-x:auto">
   <table id="tableProducto" class="table table-striped table-bordered" style="">
     <thead>
       <th class="">ID</th>
       <th>Código</th>
       <th>Producto</th>
       <th>Forma <br>Farmaceutica</th>
       <th>Laboratorio</th>
       <th>Precio <br>Costo</th>
       <th>Utilidad</th>
       <th>Precio <br>Venta</th>
       <th>Utilidad <br>Nueva</th>
       <th>Precio Venta<br> Nueva</th>
     </thead>
   </table>
  </div>

  <div class="pull-right">
    <button type="button" class="btn btn-success" id="guardarNuevosProductos">Guardar</button>
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
