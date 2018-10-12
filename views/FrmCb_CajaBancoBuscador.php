<?php
include_once("../clases/BnGeneral.php");
//include("../clases/DtGeneral.php");
include_once("../clases/helpers/Modal.php");
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title>Hotel - Producto</title>

 </head>
<?php include_once 'linker.php'; ?>
<script type="text/javascript">

    $(document).ready(function(){

      // CUENTA
      $("#btnTipoCuenta").click(function(){
        listarCuenta();
        $("#modalCuenta").modal("show");
      });
      // TIPO OPE
      $("#btnTipoOpe").click(function(){
        listarTipoOpe();
        $("#modalTipoOpe").modal("show");
      });

      $('#btnGenerar').click(function() {
        if (!$('#txtTipoCuentaId').val() || !$('#txtTipoOpe').val()) {

        }
        listarCajaBanco();
      })

    })

    function listarTipoOpe() {
     $("#tableTipoOpe").DataTable().destroy();
       var table4 = $("#tableTipoOpe").DataTable({
               "bProcessing": true,
               //"responsive" : true,
               "sAjaxSource": "../controllers/server_processingTipoOpe.php",
               "bPaginate":true,
               "sPaginationType":"full_numbers",
               "iDisplayLength": 5,
               //"bAutoWidth": false,
               //"autoWidth" : false,
               //"bFilter": false,
               "aoColumns": [
                   { mData: 'IdTipoCajaBanco' } ,
                   { mData: 'TipoCajaBanco' },
                   { mData: 'Tipo' }
               ],
               "rowCallback": function(row, data, index) {
                   $(row).click(function() {
                       $("#txtTipoOpe").val(data.TipoCajaBanco);
                       $("#txtTipoOpeId").val(data.IdTipoCajaBanco);
                       if (data.Tipo == "1") {
                           window.tipoCajaBanco = 1
                           $("#txtCliente").prop("disabled", true);
                           $("#txtCliente").val("");
                           $("#btnCliente").prop("disabled", true);
                           $("#btnEstadoCuentaC").prop("disabled", true);
                           // PROVEEDOR
                           $("#txtProveedor").prop("disabled", false);
                           $("#btnProveedor").prop("disabled", false);
                           $("#btnEstadoCuentaP").prop("disabled", false);
                       }else {
                           window.tipoCajaBanco = 0
                           $("#txtCliente").prop("disabled", false);
                           $("#btnCliente").prop("disabled", false);
                           $("#btnEstadoCuentaC").prop("disabled", false);
                           // proveedor
                           $("#txtProveedor").prop("disabled", true);
                           $("#txtProveedor").val("");
                           $("#btnProveedor").prop("disabled", true);
                           $("#btnEstadoCuentaP").prop("disabled", true);
                       }
                       $("#modalTipoOpe").modal("hide");
                   })
               }
           });
   }


function listarCajaBanco(){
     $("#tableCajaBanco").DataTable().destroy();
     var saldo = 0;
     var idCuenta = $("#txtTipoCuentaId").val();
     var idTipo = $("#txtTipoOpeId").val();
     var fechaInicio = $('#fechaIni').val();
     var fechaFinal = $('#fechaFinal').val();

     $("#tableCajaBanco").DataTable({
             "bProcessing": true,
             //"responsive" : true,
             "ajax": {
                 "url": "../controllers/server_processingCajaBanco.php?rango=1",
                 "data": function(d) {
                     d.IdCuenta = idCuenta || 0,
                     d.IdTipoOperacion = idTipo
                     d.FechaIni = fechaInicio,
                     d.FechaFin = fechaFinal
                 }
             },
             //"sAjaxSource": "../controllers/server_processingCajaBanco.php",
             "bPaginate":true,
             "sPaginationType":"full_numbers",
             "iDisplayLength": 5,
             //"bAutoWidth": false,
             //"autoWidth" : false,
             //"bFilter": false,
             "aaSorting": [],
             "aoColumns": [
               { mData: 'IdCajaBanco' },
               { mData: 'FechaDoc' },
               { mData: 'Proveedor', 
                 visible: (window.tipoCajaBanco == "1")?true:false
               },
               { mData: 'Cliente',
                 visible: (window.tipoCajaBanco == "0")?true:false
               },
               { mData: 'Concepto' },
               { mData: 'Importe' }
             ],
             "rowCallback": function( row, data, index ) {
               
             },
             "drawCallback": function( settings ) {
               
              }
         });
   }


    function listarCuenta(){
     $("#tableCuenta").DataTable().destroy();
       var table4 = $("#tableCuenta").DataTable({
               "bProcessing": true,
               //"responsive" : true,
               "sAjaxSource": "../controllers/server_processingCuenta.php",
               "bPaginate":true,
               "sPaginationType":"full_numbers",
               "iDisplayLength": 5,
               //"bAutoWidth": false,
               //"autoWidth" : false,
               //"bFilter": false,
               "aoColumns": [
               { mData: 'IdCuenta' } ,
               { mData: 'Cuenta' },
               {mRender: function ( data, type, full ) {
                if (data==0) {
                  return "Anulado"
                }else{
                  return "Activo"
                }
              }}
               ]
           });
         $("#tableCuenta tbody").on("click", "tr", function(){
           $("#txtTipoCuenta").val($(this).children("td").eq(1).text());
           $("#txtTipoCuentaId").val($(this).children("td").eq(0).text());
           $("#modalCuenta").modal("hide");
           });
   }

   

</script>

<body>
<?php include("header.php"); ?>
<div class="container">
    <div class="" style="margin-left:10px; margin-right:10px;">
        <div class="row">
            <div class="col-lg-4">
                <div class="input-group" style="margin-bottom:20px;">
                <input type="text" class="form-control" id="txtTipoCuenta" placeholder="Cuenta">
                <input type="hidden" class="form-control" id="txtTipoCuentaId" value="1">
                <span class="input-group-btn">
                    <button id="btnTipoCuenta" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
                </span>
                </div>
            </div>
            <div class="col-lg-3">
               <div class="input-group" style="margin-bottom:20px;">
                 <!-- <label for="txtTipoOpe">Tipo de Operacion</label> -->
                 <input type="text" class="form-control" id="txtTipoOpe" placeholder="Tipo de Operacion">
                 <input type="hidden" class="form-control" id="txtTipoOpeId" placeholder="Tipo de Operacion">
                 <span class="input-group-btn">
                   <button id="btnTipoOpe" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
                 </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 form-group">
				<label>Fecha Inicio</label>
				<input type="date" id="fechaIni" class="form-control">
            </div>
            <div class="col-md-4 form-group">
				<label>Fecha Final</label>
				<input type="date" id="fechaFinal" class="form-control">
			</div>
        </div>
        <button type="button" id="btnGenerar" class="btn btn-success">Generar</button>
        <br><br>
        <div class="">
       <table class="table table-bordered table-striped table-responsive" id="tableCajaBanco" width="100%">
         <thead>
           <tr>
             <th>Cuenta</th>
             <th colspan="5">Caja </th>
           </tr>
           <tr>
             <th>ID</th>
             <th>Fecha Operación</th>
             <th>Proveedor</th>
             <th>Cliente</th>
             <th>Concepto</th>
             <th>Importe</th>
           </tr>
         </thead>
       </table>
     </div>
    </div>
</div>
<?php include("footer.php"); ?>



<!-- MODAL CUENTA -->
<div class="modal fade" id="modalCuenta" role="dialog">
  <!-- <div class="modal-dialog" style="width:800px"> -->
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
			Lista de Cuentas
		</div>
		<div class="modal-body" style="overflow-x:auto;">
			<table id="tableCuenta" class="table table-bordered table-striped" width="100%">
				<thead>
					<th >#</th>
					<th >Cuenta</th>
					<th >Estado</th>
				</thead>
			</table>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>

<!-- MODAL TIPO_OPERACION -->
<div class="modal fade" id="modalTipoOpe" role="dialog">
  <!-- <div class="modal-dialog" style="width:800px"> -->
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
			Lista de Cuentas
		</div>
		<div class="modal-body" style="overflow-x:auto;">
			<table id="tableTipoOpe" class="table table-bordered table-striped" width="100%">
				<thead>
					<th >#</th>
					<th >Tipo de Estado Cuenta</th>
					<th >Tipo</th>
				</thead>
			</table>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>


 </body>
 </html>

