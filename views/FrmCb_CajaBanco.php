<?php
include_once("../clases/helpers/Modal.php");
?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Caja y Banco</title>
 </head>
 <?php include_once("linker.php"); ?>
 <style media="screen">
 .table td {
 text-align: center;
}
 </style>
 <script type="text/javascript">
   $(document).ready(function(){
     $('#btnAdd').hide()

     $("#btnAdd").click(function(){
       $('#gridDocAplicados').hide();
       $('#txtFechaVen2').val($('#txtFechaVen').val())
       $("#modalFrmCb_CajaBancoNuevo").modal("show");
     });

     // Proveedor
     $("#btnProveedor").click(function(e){
   		listarProveedor();
   		$("#modalProveedor").modal("show");
   	});

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

      // MODAL CLIENTE ESTADO DE CUENTA
      $("#btnEstadoCuentaC").click(function(){
        $("#txtClienteE").val($("#txtCliente").val());
        $("#modalEstadoCuenta").modal("show");
      });
      // MODAL PROVEEDOR ESTADO DE CUENTA
      $("#btnEstadoCuentaP").click(function(){
        $("#txtClienteE").val($("#txtProveedor").val());
        $("#modalEstadoCuenta").modal("show");
      });

      // ESTADO DE CVUENTA Det Cliente
      $("#btnEstadoCuentaC").click(function(){
        var xhr = $.ajax({
          url: '../controllers/server_processingEstadoCuenta.php',
          type: 'get',
          data: {tipoOpe : $("#txtTipoOpe").val(), cliente : $("#txtCliente").val()},
          dataType: 'json',
          success: function(respuesta){
            $("#tableEstadoCuenta tbody").empty();
            var fila = "";
              $.each(respuesta, function(data, value){
                fila = "<tr><td>"+value.idDocVenta+"</td><td>"+value.FechaDoc+"</td><td>"+value.FechaCredito+"</td><td>"+value.Numero+"</td><td>"+value.Total+"</td><td>"+value.Cancelado+"</td><td>"+"</td></tr>";
              });
              $("#tableEstadoCuenta tbody").append(fila);
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
              alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
        });
      });

      // ESTADO DE CVUENTA Det Provvedor
      $("#btnEstadoCuentaP").click(function(){
        var xhr = $.ajax({
          url: '../controllers/server_processingEstadoCuenta.php',
          type: 'get',
          data: {tipoOpe : $("#txtTipoOpe").val(), cliente : $("#txtProveedor").val()},
          dataType: 'json',
          success: function(respuesta){
            $("#tableEstadoCuenta tbody").empty();
            var fila = "";
              $.each(respuesta, function(data, value){
                fila = "<tr><td>"+value.Serie+"</td><td>"+value.FechaDoc+"</td><td>"+value.FechaCredito+"</td><td>"+value.Numero+"</td><td>"+value.Total+"</td><td>"+value.Cancelado+"</td><td>"+"</td></tr>";
              });
              $("#tableEstadoCuenta tbody").append(fila);
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
              alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
        });
      });


     // CLIENTE
     $("#btnCliente").click(function(){
       $("#ModalCliente").modal({backdrop: false});
           $("#tableCliente").DataTable().destroy();
           $("#tableCliente tbody").empty();
           var table4 = $("#tableCliente").DataTable({
           "bProcessing": true,
           "sAjaxSource": "../controllers/server_processingCliente.php",
           "bPaginate":true,
           "sPaginationType":"full_numbers",
           "iDisplayLength": 5,
           "aoColumns": [
           { mData: 'IdCliente' } ,
           { mData: 'Cliente' },
           { mData: 'DniRuc' },
           { mData: 'Direccion', "sClass": "idProd" },
           { mData: 'Telefono', "sClass": "idProd"},
           { mData: 'Email', "sClass": "idProd" },

         ]
         });
           $('#tableCliente tbody').on('click', 'tr', function () {
             //var data = $(this).children("td").eq(1).html();
             //console.log($(this).children("td").eq(1).html());
             var idCliente = $(this).children("td").eq(0).html();
             $('#txtCliente').attr('data-idcliente', idCliente)

             $('#gridDocAplicados').show();


             $("#txtCliente").val($(this).children("td").eq(1).text());
             $("#ModalCliente").modal("hide");

             // Documentos aplicados ListarDocAplicados
             $("#tableDocAplicados").DataTable().destroy();
             $("#tableDocAplicados").DataTable({
                 "bProcessing": true,
                 //"sAjaxSource": "../controllers/server_processingCajaBancoDocAplicados.php",
                 "ajax": {
                   "url": "../controllers/server_processingCajaBancoDocAplicados.php",
                   "data": function (d) {
                     d.idCliente = idCliente;
                   }
                 },
                 "bPaginate":true,
                 "sPaginationType":"full_numbers",
                 "iDisplayLength": 5,
                 "aoColumns": [
                   { mData: 'IdDocVenta' } ,
                   { mData: 'FechaDoc' },
                   { mData: 'FechaCredito' },
                   { mData: 'Correlativo' },
                   { mData: 'Total'},
                   { mData: 'Aplicado' },
                   { mData: 'Saldo' },
                   { mRender: function ( data, type, full ) {
                     return '<button \
                        type="button"  \
                        onclick="verificarImporte(event)" \
                        class="btn btn-success" \
                        data-toggle="modal"  \
                        data-iddocventa="' + full.IdDocVenta + '" \
                        data-fechadoc="' + full.FechaDoc + '" \
                        data-fechacredito="' + full.FechaCredito + '" \
                        data-correlativo="' + full.Correlativo + '" \
                        data-total="' + full.Total + '" \
                        data-aplicado="' + full.Aplicado + '" \
                        data-saldo="' + full.Saldo + '" \
                        >Aplicar</button>';
                   }}
                 ],
                 "rowCallback": function(row, data, index) {
                    /*var sumTotal = parseFloat($('.sumatoriaTotalDAA').text()) || 0
                    var sumAplicado = parseFloat($('.sumatoriaAplicadoDAA').text()) || 0
                    var sumSaldo = parseFloat($('.sumatoriaSaldoDAA').text()) || 0

                    sumTotal += parseFloat(data.Total)
                    sumAplicado += parseFloat(data.Aplicado)
                    sumSaldo += parseFloat(data.Saldo)

                    $('.sumatoriaTotalDAA').text(sumTotal)
                    $('.sumatoriaAplicadoDAA').text(sumAplicado)
                    $('.sumatoriaSaldoDAA').text(sumSaldo)*/
                  },
                  "footerCallback": function(row, data, start, end, display) {
                    if (data.length) {
                      var sumTotal = 0
                      var sumAplicado = 0
                      var sumSaldo = 0
                      $.each(data, function(index, value) {
                        sumTotal +=  parseFloat(value.Total)
                        sumAplicado += parseFloat(value.Aplicado)
                        sumSaldo += parseFloat(value.Saldo)
                      })
                      $('.sumatoriaTotalDAA').text(sumTotal)
                      $('.sumatoriaAplicadoDAA').text(sumAplicado)
                      $('.sumatoriaSaldoDAA').text(sumSaldo)
                    }
                  }
               });

         });
     });
   });
   function listarProveedor(){
     $("#tableProveedor").DataTable().destroy();
       var table4 = $("#tableProveedor").DataTable({
               "bProcessing": true,
               //"responsive" : true,
               "sAjaxSource": "../controllers/server_processingProveedor.php",
               "bPaginate":true,
               "sPaginationType":"full_numbers",
               "iDisplayLength": 5,
               //"bAutoWidth": false,
               //"autoWidth" : false,
               //"bFilter": false,
               "aoColumns": [
               { mData: 'IdProveedor' } ,
               { mData: 'Proveedor' },
               { mData: 'Ruc' },
               { mData: 'Direccion' },
               { mData: 'Observacion' }
               ]
           });
         $("#tableProveedor tbody").on("click", "tr", function(){
           $("#txtProveedor").val($(this).children("td").eq(1).text());
           $("#modalProveedor").modal("hide");
           $('#gridDocAplicados').show()



           // Documentos aplicados ListarDocAplicadosProveedor
           var idProveedor = $(this).children("td").eq(0).html();

          $('#txtProveedor').attr('data-idproveedor', idProveedor);

           $("#tableDocAplicados").DataTable().destroy();
           $("#tableDocAplicados").DataTable({
               "bProcessing": true,
               //"sAjaxSource": "../controllers/server_processingCajaBancoDocAplicados.php",
               "ajax": {
                 "url": "../controllers/server_processingCajaBancoDocAplicadosProveedor.php",
                 "data": function (d) {
                   d.idProveedor = idProveedor;
                 }
               },
               "bPaginate":true,
               "sPaginationType":"full_numbers",
               "iDisplayLength": 5,
               "aoColumns": [
                 { mData: 'Id' } ,
                 { mData: 'FechaDoc' },
                 { mData: 'FechaCredito' },
                 { mData: 'Correlativo' },
                 { mData: 'Total'},
                 { mData: 'Aplicado' },
                 { mData: 'Saldo' },
                 { mRender: function ( data, type, full ) {
                   return '<button \
                      type="button"  \
                      onclick="verificarImporte(event)" \
                      class="btn btn-success" \
                      data-toggle="modal"  \
                      data-hash="' + full.Id + '" \
                      data-fechadoc="' + full.FechaDoc + '" \
                      data-fechacredito="' + full.FechaCredito + '" \
                      data-correlativo="' + full.Correlativo + '" \
                      data-total="' + full.Total + '" \
                      data-aplicado="' + full.Aplicado + '" \
                      data-saldo="' + full.Saldo + '" \
                      >Aplicar</button>';
                 }}
               ],
               "rowCallback": function(row, data, index) {
                  //console.log(index)

                },
                "footerCallback": function(row, data, start, end, display) {
                  if (data.length) {
                    var sumTotal = 0
                    var sumAplicado = 0
                    var sumSaldo = 0
                    $.each(data, function(index, value) {
                      sumTotal +=  parseFloat(value.Total)
                      sumAplicado += parseFloat(value.Aplicado)
                      sumSaldo += parseFloat(value.Saldo)
                    })
                    $('.sumatoriaTotalDAA').text(sumTotal)
                    $('.sumatoriaAplicadoDAA').text(sumAplicado)
                    $('.sumatoriaSaldoDAA').text(sumSaldo)
                  }
                }
             });
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

           listarCajaBanco();
           $('#btnAdd').show()
           });
   }
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
                   { mData: 'TipoCajaBanco' }
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
     var fechaDoc = $('#txtFechaVen').val();

     $("#tableCajaBanco").DataTable({
             "bProcessing": true,
             //"responsive" : true,
             "ajax": {
                 "url": "../controllers/server_processingCajaBanco.php",
                 "data": function(d) {
                     d.IdCuenta = idCuenta || 0,
                     d.FechaDoc = fechaDoc
                 }
             },
             //"sAjaxSource": "../controllers/server_processingCajaBanco.php",
             "bPaginate":false,
             "sPaginationType":"full_numbers",
             "iDisplayLength": 5,
             //"bAutoWidth": false,
             //"autoWidth" : false,
             //"bFilter": false,
             "aaSorting": [],
             "aoColumns": [
               { mData: 'IdCajaBanco' },
               { mData: 'Concepto' },
               { mData: 'Ingresos' },
               { mData: 'Salida' },
               { mData: null },
               { mRender: function ( data, type, full ) {
                 return '<button \
                    type="button"  \
                    class="btn btn-danger" \
                    data-toggle="modal"  \
                    data-title="' + full.Concepto + '" \
                    data-idrow="' + full.IdCajaBanco + '" \
                    data-target="#modalEliminarCajaBanco">Eliminar</button>';
               }}
             ],
             "aoColumnDefs": [ {
                  "aTargets": [ 2, 3, 4 ],
                  "mRender": function (data, type, full) {
                    return parseFloat(data).toFixed(2);
                  }
              }],
             "rowCallback": function( row, data, index ) {
               saldo = parseFloat(saldo) + parseFloat(data.Ingresos) - parseFloat(data.Salida);
               $('td:eq(4)', row).html( saldo.toFixed(2) );
             },
             "drawCallback": function( settings ) {
               saldo = 0
              }
         });
   }
   function asignarAplicadoDocVenta(docAplicado) {
     $('#importeAplicar').val('')
     window.aplicadoDocVenta = window.aplicadoDocVenta || []
     window.aplicadoDocVenta.push(docAplicado)
     var tr = $('<tr class=""></tr>')
     tr.append('<td>' + docAplicado.idDocDet + '</td>')
     tr.append('<td>' + docAplicado.fechaDoc + '</td>')
     tr.append('<td>' + docAplicado.fechaCredito + '</td>')
     tr.append('<td>' + docAplicado.correlativo + '</td>')
     tr.append('<td>' + docAplicado.saldo + '</td>')
     tr.append('<td>' + docAplicado.importe + '</td>')
     tr.append('<td>' + (parseFloat(docAplicado.saldo) - parseFloat(docAplicado.importe)) + '</td>')
     $('#tableDocAplicadosTmp tbody').append(tr)

     var sumSaldoDA = sumatoriaSaldoDA()
     var sumAplicadoDA = sumatoriaSaldoAplicado()
     $('#tableDocAplicadosTmp tfoot .sumatoriaSaldoDA').text(sumSaldoDA)
     $('#tableDocAplicadosTmp tfoot .sumatoriaAplicadoDA').text(sumAplicadoDA)
     $('#tableDocAplicadosTmp tfoot .sumatoriaSaldoFinDA').text(sumSaldoDA - sumAplicadoDA)
   }

   function limpiarTmpCajaBanco() {
     $('#tableDocAplicadosTmp tbody tr').remove();
     $('#tableDocAplicadosTmp .sumatoriaSaldoDA').text('');
     $('#tableDocAplicadosTmp .sumatoriaAplicadoDA').text('');
     $('#tableDocAplicadosTmp .sumatoriaSaldoFinDA').text('');
     window.aplicadoDocVenta = []

     $('#txtProveedor').val('')
     $('#txtCliente').val('')
     $('#txtConcepto').val('')
     $('#txtImporte').val('')
   }

   function guardarCajaBanco() {
      if (!$('#txtConcepto').val()) {
        alert('falta llenar el campo: concepto')
        return;
      }
      if (!$('#txtImporte').val()) {
        alert('falta llenar el campo: Importe')
        return;
      }
      if (sumatoriaSaldoAplicado() > $('#txtImporte').val()) {
        alert('La sumatoria de los importes a aplicar no puede ser mayor al importe general')
        return;
      }

       var asd = $.ajax({
           url: '../controllers/server_processingCajaBanco.php',
           type: 'post',
           data: {
               IdTipoCajaBanco : $('#txtTipoOpeId').val(),
               IdCuenta: $('#txtTipoCuentaId').val(),
               FechaDoc: $('#txtFechaVen2').val(),
               Concepto: $('#txtConcepto').val(),
               Importe: $('#txtImporte').val(),
               IdCliente: $('#txtCliente').is(':disabled') ? 0 : $('#txtCliente').attr('data-idcliente'),
               IdProveedor: $('#txtProveedor').is(':disabled') ? 0 : $('#txtProveedor').attr('data-idproveedor'),
               AplicadoDocVenta: window.aplicadoDocVenta,
               TipoCajaBanco: window.tipoCajaBanco || 0
           },
           dataType: 'json',
           success: function(respuesta){
               if (respuesta.success) {
                   $("#tableCajaBanco").DataTable().ajax.reload();
                   $.notify({
                       icon: 'fa fa-check',
                       message: respuesta.success
                   }, {
                       type: 'success'
                   });
                  // exportarCajaBancoClientePDF(limpiarTmpCajaBanco)
               } else {
                   $.notify({
                       icon: 'fa fa-exclamation',
                       message: respuesta.error
                   }, {
                       type: 'danger'
                   });
               }
               $("#modalFrmCb_CajaBancoNuevo").modal("hide");
           },
           error: function(XMLHttpRequest, textStatus, errorThrown) {
               alert("Status: " + textStatus);
               alert("Error: " + errorThrown);
           }
       });
       console.log(asd)

   }
 </script>
 <body>
   <?php include_once("header.php"); ?>
   <div class="container">
     <div class="row">
       <div class="col-lg-4">

       </div>
       <div class="col-lg-4">
         <div class="input-group" style="margin-bottom:20px;">
           <input type="text" class="form-control" id="txtTipoCuenta" placeholder="Cuenta">
           <input type="hidden" class="form-control" id="txtTipoCuentaId">
           <span class="input-group-btn">
             <button id="btnTipoCuenta" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
           </span>
         </div>
       </div>
     </div>
     <div class="row">
       <div class="col-lg-4">

       </div>
       <div class="col-lg-4">
         <div class="input-group" style="margin-bottom:20px;">
           <input type="date" style="width:318px" class="form-control" id="txtFechaVen" value="<?php echo date("Y-m-d"); ?>" onchange="listarCajaBanco()" >
         </div>
       </div>
     </div>
     <div class="">
       <table class="table table-bordered table-striped table-responsive" id="tableCajaBanco">
         <thead>
           <tr>
             <th>Cuenta</th>
             <th colspan="5">Caja </th>
           </tr>
           <tr>
             <th>ID</th>
             <th>Concepto</th>
             <th>Ingresos </th>
             <th >Salida </th>
             <th >Saldo </th>
             <th >Acciones </th>
           </tr>
         </thead>
       </table>
     </div>
     <div class="pull-right">
       <button id="btnPdf" type="button" class="btn btn-success" onclick="exportarCajaBanco()"><i class="fa fa-file-pdf-o fa-lg"></i></button>
       <button id="btnExcel" type="button" class="btn btn-success" name="button"><i class="fa fa-file-excel-o fa-lg"></i></button>
       <button id="btnAdd" type="button" class="btn btn-danger" name="button"><i class="fa fa-plus fa-lg"></i></button>
     </div>
   </div>
   <?php include_once("footer.php"); ?>
 </body>
 </html>
<!-- Nuevo Agregar -->
 <div id="modalFrmCb_CajaBancoNuevo" class="modal fade" role="dialog">
   <div class="modal-dialog modal-lg ">
     <div class="modal-content">
       <div class="modal-header">Agregar Tipo Caja</div>
       <div class="modal-body">
         <div class="container">
           <div class="row">
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
             <div class="col-lg-3">
               <div class="input-group" style="margin-bottom:20px;">
                 <!-- <label for="txtCliente">Cliente</label> -->
                 <input type="text" class="form-control" id="txtCliente" placeholder="Seleccionar Cliente">
                 <span class="input-group-btn">
                   <button id="btnCliente" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
                 </span>
               </div>
             </div>
             <div class="col-lg-3">
                   <button type="button" id="btnEstadoCuentaC" class="btn btn-info" name="button"><i class="fa fa-id-card-o"></i></button>
             </div>
           </div>
           <div class="row">
             <div class="col-lg-3">
               <div class="input-group" style="margin-bottom:20px;">
                 <!-- <label for="txtProveedor">Proveedor</label> -->
                 <input type="text" class="form-control" id="txtProveedor" placeholder="Seleccionar Proveedor">
                 <span class="input-group-btn">
                   <button id="btnProveedor" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
                 </span>
               </div>
             </div>
             <div class="col-lg-3">
                   <button type="button" id="btnEstadoCuentaP" class="btn btn-info" name="button"><i class="fa fa-id-card-o"></i></button>
             </div>
           </div>
           <div class="row">
             <div class="col-lg-12">
               <div class="form-inline" style="margin-bottom:20px;">
                 <label for="txtConcepto">Fecha de operación</label>
                 <input type="date" class="form-control" id="txtFechaVen2" placeholder="">
               </div>
             </div>
           </div>
           <div class="row">
             <div class="col-lg-12">
               <div class="form-inline" style="margin-bottom:20px;">
                 <label for="txtConcepto">Concepto</label>
                 <input type="text" class="form-control" id="txtConcepto" placeholder="Concepto">
               </div>
             </div>
           </div>
           <div class="row">
             <div class="col-lg-12">
               <div class="form-inline" style="margin-bottom:20px;">
                 <label for="txtImporte">Importe</label>
                 <input type="number" step="0.01" class="form-control" id="txtImporte" placeholder="0.00">
               </div>
             </div>
           </div>
           <div class="row" id="gridDocAplicados">
             <div class="col-lg-9">
               <div class="panel panel-default">
                 <div class="panel-heading">Documentos a aplicar</div>
                 <div class="panel-body">
                   <table class="table table-bordered table-striped table-responsive" id="tableDocAplicados">
                     <thead>
                       <th>ID</th>
                       <th>Fecha</th>
                       <th>FechaVen</th>
                       <th>Correlativo</th>
                       <th>Total</th>
                       <th>Aplicado</th>
                       <th>Saldo</th>
                       <th>Acciones</th>
                     </thead>
                     <tfoot>
                       <tr>
                         <td colspan="4">
                           Sumatoria
                         </td>
                         <td class="sumatoriaTotalDAA" style="font-weight:bold"></td>
                         <td class="sumatoriaAplicadoDAA" style="font-weight:bold"></td>
                         <td class="sumatoriaSaldoDAA" style="font-weight:bold"></td>
                         <td></td>
                       </tr>
                     </tfoot>
                   </table>
                 </div>
               </div>
               <div class="panel panel-default">
                 <div class="panel-heading">Documentos Aplicados</div>
                 <div class="panel-body">
                   <table class="table table-bordered table-striped table-responsive" id="tableDocAplicadosTmp">
                     <thead>
                       <th>ID</th>
                       <th>Fecha</th>
                       <th>FechaVen</th>
                       <th>Correlativo</th>
                       <th>Saldo</th>
                       <th>Aplicado</th>
                       <th>Saldo Fin</th>
                     </thead>
                     <tbody>

                     </tbody>
                     <tfoot>
                       <tr>
                         <td colspan="4">Sumatoria</td>
                         <td class="sumatoriaSaldoDA" style="font-weight: bold"></td>
                         <td class="sumatoriaAplicadoDA" style="font-weight: bold"></td>
                         <td class="sumatoriaSaldoFinDA" style="font-weight: bold"></td>
                       </tr>
                     </tfoot>
                   </table>
                 </div>
               </div>
             </div>
           </div>
         </div>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-success" name="button" onclick="guardarCajaBanco()">Guardar</button>
         <button type="button" class="btn btn-danger" name="button" data-dismiss="modal" onclick="limpiarTmpCajaBanco()">Cancelar</button>
       </div>
     </div>
   </div>
 </div>

 <!-- Estado de cuenta -->
 <div id="modalEstadoCuenta" class="modal fade" role="dialog">
   <div class="modal-dialog">
     <div class="modal-content">
       <div class="modal-header">ESTADO DE CUENTA</div>
       <div class="modal-body">
         <div class="container">
           <div class="row">
             <div class="col-lg-12">
               <div class="form-inline">
                 <input type="text" class="form-control" id="txtClienteE" name="" value="">
               </div>
             </div>
           </div>
           <br>
           <div class="row" style="overflow-x:auto;">
             <div class="col-lg-6">
               <table id="tableEstadoCuenta" class="table table-bordered table-striped table-responsive">
                 <thead>
                   <th>ID</th>
                   <th>Fecha</th>
                   <th>FechaVen</th>
                   <th>Correla</th>
                   <th>Total</th>
                   <th>Cancelado</th>
                   <th>Saldo</th>
                 </thead>
                 <tbody>

                 </tbody>
               </table>
             </div>
           </div>

         </div>
       </div>
       <div class="modal-footer">
         <div class="form-inline ">
           <label for="txtSaldo">SALDO</label>
           <input type="text"  class="form-control" id="txtSaldo" name="" value="">
         </div>
       </div>
     </div>
   </div>
 </div>

 <!-- Estado Cuenta Det -->
<div id="modalEstadoDet" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"></div>
      <div class="modal-body">
        <div class="container">
          <div class="form-inline">
            <label for="txtFechaEstadoDet">Fecha</label>
            <input type="text" id="txtFecha" name="" value="">
          </div>
          <div class="form-inline">
            <label for="txtFechaVenEstadoDet">F.Ven</label>
            <input type="text" id="txtFechaVen" name="" value="">
          </div>
          <div class="form-inline">
            <label for="txtCorr">Corr</label>
            <input type="text" id="txtCorr" name="" value="">
          </div>
          <div class="form-inline">
            <label for="txtSaldoEstadoDet">Saldo</label>
            <input type="text" id="txtFecha" name="" value="">
          </div>
          <div class="form-inline">
            <label for="txtImporteEstadoDet">Importe</label>
            <input type="text" id="txtImporteEstadoDet" name="" value="">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnAddEstadoDet" name="button">ADD</button>
      </div>
    </div>
  </div>
</div>

<!-- CLIENTE -->
<div class="modal fade" id="ModalCliente" role="dialog">
 <div class="modal-dialog">
   <div class="modal-content">
     <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal">&times;</button>
             <h4 class="modal-title">CLIENTE</h4>
     </div>
     <div class="modal-body">
       <div class="sTableCliente">
         <table id="tableCliente" class="table table-striped table-bordered">
           <thead>
             <th>#</th>
             <th>Cliente</th>
             <th>DNI / RUC</th>
             <th style="display:none">Direccion</th>
             <th style="display:none">Telefono</th>
             <th style="display:none">Email</th>
           </thead>
         </table>
       </div>
     </div>
     <div class="modal-footer">
       <button type="button" id="btnModalCliente" class="btn btn-success">Añadir</button>
       <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
     </div>
   </div>
 </div>
</div>

<!-- MODAL PROVEEDOR -->
<div class="modal fade" id="modalProveedor" role="dialog">
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
			Lista de Proveedores
		</div>
		<div class="modal-body" style="overflow-x:auto;">
			<table id="tableProveedor" class="table table-bordered table-striped">
				<thead>
					<th >#</th>
					<th >Proveedor</th>
					<th >RUC</th>
					<th >Direccion</th>
          <th >Observacion</th>
					<th >Editar</th>
				</thead>
			</table>
		</div>
		<div class="modal-footer">
			<button type="button" id="btnAddProveedor" class="btn btn-success">Nuevo</button>
			<button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>

<!-- MODAL CUENTA -->
<div class="modal fade" id="modalCuenta" role="dialog">
  <!-- <div class="modal-dialog" style="width:800px"> -->
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
			Lista de Cuentas
		</div>
		<div class="modal-body" style="overflow-x:auto;">
			<table id="tableCuenta" class="table table-bordered table-striped">
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
			<table id="tableTipoOpe" class="table table-bordered table-striped">
				<thead>
					<th >#</th>
					<th >Tipo de Estado Cuenta</th>
				</thead>
			</table>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>

<!-- MODAL ELIMINAR CAJA BANCO -->
<?php
Modal::render('ModalEliminar', [
    'id' => 'modalEliminarCajaBanco',
    'controller' => 'server_processingCajaBanco',
    'reload' => '#tableCajaBanco'
]);

Modal::render('ModalAplicarCajaBanco', [
    'id' => 'ModalAplicarCajaBanco',
    'controller' => 'server_processingCajaBanco',
    'reload' => '#tableDocAplicados'
]);
?>
