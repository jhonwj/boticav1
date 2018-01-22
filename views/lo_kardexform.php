<?php  ?>

<html>
<head>
	<title>Inventario</title>
</head>
<?php include_once 'linker.php'; ?>

<script type="text/javascript">

$(document).ready(function(e){
	$("#btnProducto").click(function(e){
		ListarProductos();
		$("#modalProducto").modal("show");
	});

	$("#btnReportar").click(function(e){
		window.location = "reporteExcel3.php?producto="+$("#txtProducto").val()+"&fechaIni="+$("#txtFechaIni").val()+"&fechaFin="+$("#txtFechaFin").val()+"&Tipo="+ $("#Tipo").val();
	});

  $("#btnGenerar").click(function(){
    $("#Spiner_preload").removeClass("hide");
    $("#tableProductoI tbody tr").remove();
    var xhr = $.ajax({
      url: "lo_listarstock.php",
      type: "get",
      data: {producto: $("#txtProducto").val(), fechaIni: $("#txtFechaIni").val(), fechaFin: $("#txtFechaFin").val(), Tipo: $("#Tipo").val()},
      dataType: "html",
      success: function(res){
        $("#Spiner_preload").addClass("hide");
        //console.log(res);
        var respuesta = JSON.parse(res);
        var tablagenerado = "";
        $.each(respuesta, function(data, value){
          tablagenerado =tablagenerado  + "<tr><td>"+value.d1+"</td><td>"+value.d2+"</td><td>"+value.d3+"</td><td>"+value.d4+"</td><td>"+value.d5+"</td></tr>";
        });
        //console.log(tablagenerado);
        $("#tableProductoI tbody").append(tablagenerado);
      },
      error: function(error){
        $("#Spiner_preload").addClass("hide");
        alert("ingrese los campos correctos");
      }
    });
  });

});


function ListarProductos(){
          $("#tableProducto").DataTable().destroy();
          var table4 = $("#tableProducto").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingProductoInv.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProducto' } ,
            { mData: 'Producto' }
            ]
        });
          console
        $("#tableProducto tbody").on("click", "tr", function(e) {
        $("#txtProducto").val($(this).children("td").eq(1).html());
        $("#tempIdProductoDet").val($(this).children("td").eq(0).html());
        $("#tempProductoDet").val($(this).children("td").eq(1).html());
        $("#modalProducto").modal("hide");
        //agregarProductoDet();
        });
}



function agregarProductoDet(){
          var Encontrado = 0;

              $("#tableProductoI tbody").each(function(index, el) {
                        $("#tableProductoI tbody tr").each(function(index, el) {
                        var productoDet = $(this).find('.nombreProductoDet').html();
                  if (productoDet == $("#tempProductoDet").val()) {
                 Encontrado = 1;
                  }
              });

            if (Encontrado ==0) {
            var fila = "<tr><td>"+ $("#tempIdProductoDet").val() +"</td><td class='nombreProductoDet'>"+ $("#tempProductoDet").val() + "<td class='text-center'><a id='btnEliminarProducto' class='btn' onclick='EliminarProductoDet("+$("#tempIdProductoDet").val()+")'><i class='fa fa-trash'></i></a></td></tr>";
            $("#tableProductoI tbody").append(fila);
            $.notify({
              icon: 'fa fa-plus',
              message: "<strong>"+$("#tempProductoDet").val()+"</strong> Ha sido agregado"
            });
            }
      });
}



</script>

<body>
<?php include("header.php"); ?>

<div class="bt-panel">
	<div class="container center_div" >
 	<button id="btnReportar" class="btn btn-success fab" data-toggle="modal" data-target="#nuevo"><i class="fa fa-file-excel-o"></i></button>
		<div class="center_div_form">
		<div class="row">
			<div class="col-md-4 form-group">
				<label class=""> Producto  </label>
				<div class="form-inline">
				<input type="text" readonly id="txtProducto" class="form-control" style="width:195px;">
				<button type="button" class="btn btn-success" id="btnProducto"><i class="fa fa-search"></i></button>
				<input type="hidden" id="tempIdProductoDet">
				<input type="hidden" id="tempProductoDet">
				<input type="hidden" name="" id="Tipo" value="<?php echo $_GET['Tipo']; ?>">
				</div>
			</div>
			<div class="col-md-4 form-group">
        		<label class=""> Fecha Inicio :  </label>
        		<input type="date" id="txtFechaIni"  class="form-control" value="<?php
        		echo date('Y-m')."-".str_pad(((int)(date('d')-(date('d')-1))), 2, 0, STR_PAD_LEFT); ?>" style="width:195px;">
      		</div>
      		<div class="col-md-4 form-group">
        		<label class=""> Fecha Fin :  </label>
        		<input type="date" id="txtFechaFin"  class="form-control" value="<?php echo date('Y-m-d'); ?>" style="width:195px;">
      		</div>
		</div>
    <div class="row">
      <div class="col-md-4">
        <button class="btn btn-success" id="btnGenerar">Generar</button>
      </div>
    </div>
		</div>
		<br>
		<hr>
		<div class="panel panel-success">
			<div class="panel panel-heading">
				<div class="form-inline">
				<label class="">producto </label>
				<!-- <input type="text" class="form-control"> -->
				</div>
			</div>
			<table id="tableProductoI" class="table table-bordered table-striped">
				<thead>
					<th></th>
          <th></th>
          <th></th>
          <th></th>
					<th></th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
    <div id="Spiner_preload" class="hide">
      <p>Generando...</p>
      <i class="fa fa-spinner fa-spin" style="font-size:24px"></i>
    </div>
	</div>
  <br>
  <hr>
</div>

<?php include("footer.php"); ?>
</body>
 <div class="modal fade" id="modalProducto" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Productos </h4>
      </div>
      <div class="modal-body">
        <table id="tableProducto" class="table table-striped table-bordered">
          <thead>
            <th>#</th>
            <th>Producto</th>
          </thead>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>

</html>
