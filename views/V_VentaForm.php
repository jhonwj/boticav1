<?php
include_once("../clases/BnGeneral.php");
//include("../clases/DtGeneral.php");
include_once("../clases/helpers/Modal.php");
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title>Botica - Producto</title>

 </head>
<?php include_once 'linker.php'; ?>
<script type="text/javascript">

	$(document).ready(function(){

    var almacenDefault = 'PRINCIPAL';

    //$(".sTableProducto").doubleScroll();

    var table2 = $("#tableTipoDocVenta").DataTable();
    var table3 = $("#tableAlmacen").DataTable();



	 table2
    .column( 1 )
    .data()
    .each( function ( value, index ) {
        if(value == "TICKET"){
          var id = table2.column(0).data()[index];
          var igv = table2.column(2).data()[index];
          var limite = table2.column(3).data()[index];
          $('#txtTipoVenta').attr('data-id', id)
          $('#txtTipoVenta').attr('data-igv', igv)
          $("#txtTipoVenta").attr('data-limite', limite);
          $("#txtTipoVenta").val(value);

          obtenerSerie(1, id)
        }
    } );

    table3
    .column( 1 )
    .data()
    .each( function ( value, index ) {
        if(index == 0) {
          almacenDefault = value
          $("#txtAlmacen").val(value);
          console.log(almacenDefault)
        }
    } );

    /*table4
    .column( 1 )
    .data()
    .each( function ( value, index ) {
        if(value == ""){
        	$("#txtCliente").val(value);
        }
    } );*/


      // ejecutar cursor- cargar stock
      $.ajax({
        url: '../controllers/server_processingReporteStock.php?cursor=1&almacen=' + almacenDefault,
        type: 'get',
        dataType: 'json',
        success: function(respuesta){
            if(respuesta.success){
              window.isLoadStock = true
            }
            else{
              window.isLoadStock = false
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            window.isLoadStock = false
            //alert("Status: " + textStatus); alert("Error: " + errorThrown);
        }
      });


$("#FechaVen").hide();

//producto
    $("#btnProducto").off("click").click(function(event) {
      if ($('#txtCliente').val() == '-') {
        alert('Seleccione un cliente');
        return;
      }

      if (!window.isLoadStock) {
        ListarProducto($("#txtAlmacen").val());
      } else {
        ListarProducto($("#txtAlmacen").val(), true);
      }
      $("#ModalBuscarProducto").modal("show");
        
    });
    $('#tableTipoDocVenta tbody').on('click', 'tr', function () {
        var data = table2.row( this ).data();
        $("#txtTipoVenta").val(data[1]);
        $('#txtTipoVenta').attr('data-igv', data[2])
        $('#txtTipoVenta').attr('data-limite', data[3])
        $("#ModalBuscarTipoVenta").modal("hide");

        // data[2] = IGV
        var total = parseFloat($('#txtTotalGen').val()).toFixed(2)
        if (data[2] == 1) {
          var subtotal = (total / 1.18).toFixed(2)
          $('#txtSubTot').val(subtotal)
          $('#txtIGV').val((total - subtotal).toFixed(2))
        } else {
          $('#txtSubTot').val(total)
          $('#txtIGV').val('0.00')
        }

        // cargar serie correcta
        obtenerSerie(1, data[0])

    });
    
    $('#tableAlmacen tbody').on('click', 'tr', function () {
        var data = table3.row( this ).data();
        $("#txtAlmacen").val(data[1]);
        $("#ModalAlmacen").modal("hide");

        window.isLoadStock = false;
        // Ejecutar cursor - carga stock
        $.ajax({
          url: '../controllers/server_processingReporteStock.php?cursor=1&almacen=' + data[1],
          type: 'get',
          dataType: 'json',
          success: function(respuesta){
              if(respuesta.success){
                window.isLoadStock = true
              }
              else{
                window.isLoadStock = false
              }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
              window.isLoadStock = false
              //alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
        });
    });
    
    $('.spinner .btn:first-of-type').on('click', function() {
    $('.spinner input').val( parseInt($('.spinner input').val(), 10) + 1);
    if ($('.spinner input').val()<="1") {$("#btnCaretDown").prop("disabled", true); $("#txtTotal").val(($("#txtPrecio").val()*$('.spinner input').val()).toFixed(2));} else{$("#btnCaretDown").prop("disabled", false); $("#txtTotal").val((($("#txtPrecio").val())*$('.spinner input').val()).toFixed(2));};
  	});
 	$('.spinner .btn:last-of-type').on('click', function() {

    $('.spinner input').val( parseInt($('.spinner input').val(), 10) - 1);
    if ($('.spinner input').val()<="1") {$("#btnCaretDown").prop("disabled", true); $("#txtTotal").val(($("#txtPrecio").val()*$('.spinner input').val()).toFixed(2));} else{$("#btnCaretDown").prop("disabled", false); $("#txtTotal").val(($("#txtPrecio").val()*$('.spinner input').val()).toFixed(2));};

  });
  $("#txtCantidad").on("keyup", function(e){
    if($('#txtCantidad').val()<="1"){
      $("#btnCaretDown").prop("disabled", true);
      $('#txtCantidad').val("1");
    }
    $("#txtTotal").val(($("#txtPrecio").val()*$('#txtCantidad').val()).toFixed(2));
  });

 	//tipodocventa
 	$("#btnTipoVenta").click(function(event) {
 		$("#ModalBuscarTipoVenta").modal("show");
 	});
 	//almacen
 	$("#btnAlmacen").click(function(event) {
 		$("#ModalAlmacen").modal("show");
 	});

  // Limpiar
  $("#btnClean").click(function(){
    $("#tablePuntoVentaDet tbody tr").remove();
    fn_SumarProd();
  });

  $("#btnTotal").click(function(event) {
                    var Encontrado = 0;
        var limiteItems = $('#txtTipoVenta').attr('data-limite');
        if ($("#tablePuntoVentaDet tbody tr").length >= limiteItems && limiteItems != 0 ) {
          alert('Ya se ha seleccionado el máximo de productos permitidos para el tipo de documento')
          return;
        }
        $("#tablePuntoVentaDet tbody").each(function(index, el) {
                  /*  $("#tablePuntoVentaDet tbody tr").each(function(index, el) {
                    var producto = $(this).find('.nombreProducto').text();
                   // console.log(producto);
                   // console.log($("#tempProducto").val());
              if (producto == $("#tempProducto").val()) {
                var fila = "<tr><td class='idProd'>"+ $("#tempId").val() +"</td><td class='nombreProducto'>"+ $("#tempProducto").val() +"</td><td>"+ $('.spinner input').val() + "</td><td>" + $("#txtPrecio").val() + "</td><td>"+ $("#txtTotal").val() +"</td><td><a id='EliminarVenta' class='btn' onclick='fn_EliminarVenta("+$("#tempId").val()+")'><i class='fa fa-trash'></i></a></td></tr>";
                $("#tablePuntoVentaDet tbody").append(fila);

            // $(this).find('td').eq(2).html( parseInt($(this).find('td').eq(2).html()) + parseInt($('.spinner input').val()));
            // $(this).find('td').eq(4).html( ( parseFloat($(this).find('td').eq(2).html()) * parseFloat($(this).find('td').eq(3).html())).toFixed(2));

             Encontrado = 1;
            // console.log("entro al if");

              }
        });*/

        //console.log(Encontrado);

        if (Encontrado ==0) {
          var fila = "<tr><td class='idProd'>"+ $("#tempId").val() +"</td><td class='nombreProducto'>"+ $("#tempProducto").val() +"</td><td>"+ $('.spinner input').val() + "</td><td>" + $("#txtPrecio").val() + "</td><td>"+ $("#txtTotal").val() +"</td><td class='nombreProducto'>"+ $("#tempLote").val() + "</td><td class='nombreProducto'>"+ $("#tempFechaVen").val() +"</td><td><a id='EliminarVenta' class='btn' onclick='fn_EliminarVenta(this);' ><i class='fa fa-trash'></i></a></td></tr>";
        $("#tablePuntoVentaDet tbody").append(fila);

        }
        var Total = 0;
        $("#tablePuntoVentaDet tbody tr").each(function(index, el) {
          //console.log($(this).find('td').eq(4).html());
          var Tot = $(this).find('td').eq(4).html();
          Total = parseFloat(Total) + parseFloat(Tot);
          //console.log(Total;
        });
        //console.log(Total);
       // console.log(Total.toFixed(2));
       if ($('#txtTipoVenta').attr('data-igv') == 1) {
        var subtotal = (Total.toFixed(2)/1.18).toFixed(2);

        $("#txtSubTot").val(subtotal);
        $('#txtIGV').val((Total.toFixed(2) - subtotal).toFixed(2))
       } else {
        $("#txtSubTot").val(Total.toFixed(2));
       }

       $("#txtTotalGen").val(Total.toFixed(2));



    $("#ModalBuscarProductoDet").modal("hide");
    $("#ModalBuscarProducto").modal("hide");
  });

  });

//Guardar Venta

$("#btnSave").click(function(event) {
  if($("#tablePuntoVentaDet tbody tr").length>0){
      $("#txtTotalPagar").val($("#txtTotalGen").val());
      $("#ModalMetPago").modal({backdrop: false});
  }else{
    alert("Registra al menos un producto");
  }
}); //click

$("#ModalMetPago").on("hidden.bs.modal", function(){
  $("#tableMetodoPago tbody").html("");
  $("#txtTotalPagar").val("");
  $("#txtCambio").val("");
});

  $("#txtPrecio").keyup(function(){
    $("#txtTotal").val(parseFloat($("#txtCantidad").val()*$(this).val()).toFixed(2));
  });

  $("#EsCreditoDiv").click(function(e){
		if($("#txtCredito").is(":checked")){
			$("#FechaVen").show();
		}else{
			$("#FechaVen").hide();
		}
	});

//met
$("#mPEfectivo").click(function(){
  //tableMetodoPago
  var numFilas = $("#tableMetodoPago tbody tr").length;
  console.log(numFilas);
  if (numFilas==0) {
        var fila = "<tr class='"+numFilas+"'><td>"+ $(this).html() +"</td><td contenteditable='true'>" +"</td><td id='txtImporteE' onkeyup='check();' contenteditable='true'>"+ "</t><td><a class='btn' onclick='EliminarMetPago("+ numFilas +");'><i class='fa fa-times'></i></a>" + "</td></tr>";
        $("#tableMetodoPago tbody").append(fila);
        $("#txtImporteE").text($("#txtTotalPagar").val());
        $("#txtTotalPago").val($("#txtImporteE").text());
        $("#txtCambio").val("0.00");
      }else{
        var valores = 0;
        $("#tableMetodoPago tbody tr").each(function(){
        var num = parseFloat($(this).find("td").eq(2).text());
        valores += parseFloat(isNaN(num) ? 0.00 : num);
      });
        var diferencia = $("#txtTotalPagar").val() - valores;
        if(diferencia<0){
          diferencia = 0.00;
        }
        var fila = "<tr class='"+numFilas+"'><td>"+ $(this).html() +"</td><td contenteditable='true'>" +"</td><td id='txtImporteE' onkeyup='check();' contenteditable='true'>"+ diferencia +  "</t><td><a class='btn' onclick='EliminarMetPago("+ numFilas +");'><i class='fa fa-times'></i></a>"+"</td></tr>";
        $("#tableMetodoPago tbody").append(fila);
    }


});
$("#mPVisa").click(function(){
    var numFilas = $("#tableMetodoPago tbody tr").length;
  console.log(numFilas);
  if (numFilas==0) {
        var fila = "<tr class='"+numFilas+"'><td>"+ $(this).html() +"</td><td contenteditable='true'>" +"</td><td id='txtImporteV' onkeyup='check();' contenteditable='true'>"+ "</t><td><a class='btn' onclick='EliminarMetPago("+ numFilas +");'><i class='fa fa-times'></i></a>"+ "</td></tr>";
        $("#tableMetodoPago tbody").append(fila);
        $("#txtImporteV").text($("#txtTotalPagar").val());
        $("#txtTotalPago").val($("#txtImporteV").text());
        $("#txtCambio").val("0.00");
      }else{
        var valores = 0;
        $("#tableMetodoPago tbody tr").each(function(){
        var num = parseFloat($(this).find("td").eq(2).text());
        valores += parseFloat(isNaN(num) ? 0.00 : num);
      });
        var diferencia = $("#txtTotalPagar").val() - valores;
        if(diferencia<0){
          diferencia = 0.00;
        }
        var fila = "<tr class='"+numFilas+"'><td>"+ $(this).html() +"</td><td contenteditable='true'>" +"</td><td id='txtImporteV' onkeyup='check();' contenteditable='true'>"+ diferencia + "</t><td><a class='btn' onclick='EliminarMetPago("+ numFilas +");'><i class='fa fa-times'></i></a>"+ "</td></tr>";
        $("#tableMetodoPago tbody").append(fila);
    }

});

$("#mPMastercard").click(function(){
    var numFilas = $("#tableMetodoPago tbody tr").length;
  console.log(numFilas);
  if (numFilas==0) {
        var fila = "<tr class='"+numFilas+"'><td>"+ $(this).html() +"</td><td contenteditable='true'>" +"</td><td id='txtImporteM' onkeyup='check();' contenteditable='true'>"+ "</t><td><a class='btn' onclick='EliminarMetPago("+ numFilas +");'><i class='fa fa-times'></i></a>"+"</td></tr>";
        $("#tableMetodoPago tbody").append(fila);
        $("#txtImporteM").text($("#txtTotalPagar").val());
        $("#txtTotalPago").val($("#txtImporteM").text());
        $("#txtCambio").val("0.00");
      }else{
        var valores = 0;
        $("#tableMetodoPago tbody tr").each(function(){
        var num = parseFloat($(this).find("td").eq(2).text());
        valores += parseFloat(isNaN(num) ? 0.00 : num);
      });
        var diferencia = $("#txtTotalPagar").val() - valores;
        if(diferencia<0){
          diferencia = 0.00;
        }
        var fila = "<tr class='"+numFilas+"'><td>"+ $(this).html() +"</td><td contenteditable='true'>" +"</td><td id='txtImporteM' onkeyup='check();' contenteditable='true'>"+ diferencia + "</t><td><a class='btn' onclick='EliminarMetPago("+ numFilas +");'><i class='fa fa-times'></i></a>"+"</td></tr>";
        $("#tableMetodoPago tbody").append(fila);
    }

});

$("#btnGuardarMetPago").click(function(){

  var cliente = $("#txtCliente").val();
  var tipoDoc = $("#txtTipoVenta").val();
  var almacen = $("#txtAlmacen").val();
  var serie = $("#txtSerie").val();
  var EsCredito = $("#txtCredito").is(":checked");
  var FechaCredito = $("#txtFechaCredito").val();

  var cabecera = [];

  cabecera.push(cliente, tipoDoc, almacen, serie, EsCredito, FechaCredito);

  var myJson2 = JSON.stringify(cabecera);

  var tablaVenta = [];
  var fila = 0;

    $("#tablePuntoVentaDet tbody tr").each(function(index, el) {
    tablaVenta.push([$(this).find('td').eq(0).html(),$(this).find('td').eq(1).html(),$(this).find('td').eq(2).html(), $(this).find('td').eq(3).html(),
    $(this).find('td').eq(4).html()]);

    fila = parseInt(fila) + 1;
  });

  var myJson = JSON.stringify(tablaVenta);

  var tablaMetodoPago = [];

  $("#tableMetodoPago tbody tr").each(function(){
    tablaMetodoPago.push([$(this).find('td').eq(0).html(), $(this).find('td').eq(1).html(), $(this).find('td').eq(2).html()]);
  });

  var myJson3 = JSON.stringify(tablaMetodoPago);

  var xhr = $.ajax({
    url: '../controllers/v_VentaGuardar.php',
    type: 'post',
    data: {data : myJson2, data2 : myJson, data3 : myJson3},
    dataType: 'html',
    success: function(respuesta){
        if(respuesta){
          //alert("Se envio Satisfactoriamente"+respuesta);
          $("#tablePuntoVentaDet tbody").empty();
          $("#txtSubTot").val("");
          $("#txtTotalGen").val("");
          $("#ModalMetPago").modal("hide");
          //window.location.href = "/views/ve_buscarimpresora.php?IdDocVenta="+respuesta;
          window.location.href = "/imprimir/index.php?IdDocVenta="+respuesta;
          //window.print();
        }
        else{
          alert("¡Error en el envio!");
        }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    }
      });
  console.log(xhr);
});

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
      { mRender : function(data, type, row){
        return "<a onclick='EditarCliente("+ row.IdCliente +");' class='btn'><i class='fa fa-pencil'></i></a>"
      }},
      { mData: 'Direccion', "sClass": "idProd" },
      { mData: 'Telefono', "sClass": "idProd"},
      { mData: 'Email', "sClass": "idProd" },

    ]
    });
      $('#tableCliente tbody').on('click', 'tr', function () {
        //var data = $(this).children("td").eq(1).html();
        //console.log($(this).children("td").eq(1).html());
        $("#txtCliente").val($(this).children("td").eq(1).html());
        $("#ModalCliente").modal("hide");

    });
});

$("#btnModalCliente").click(function(){
  $("#formAddCliente")[0].reset();
  $("#ModalClienteAñadir").modal("show");
});

$("#formAddCliente").submit(function(e){
  var clienteToJson = JSON.stringify($(this).serializeArray());
  console.log(JSON.stringify($(this).serializeArray()));
  e.preventDefault();

  var xhr = $.ajax({
    url : "../controllers/V_ClienteGuardar.php",
    type: "post",
    data: {data : clienteToJson},
    dataType : "html",
    success : function(respuesta){
      if(respuesta == "a"){
          $("#ModalClienteAñadir").modal("hide");
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
          { mRender : function(data, type, row){
            return "<a onclick='EditarCliente("+ row.IdCliente +");' class='btn'><i class='fa fa-pencil'></i></a>"
          }},
          { mData: 'Direccion', "sClass": "idProd" },
          { mData: 'Telefono', "sClass": "idProd"},
          { mData: 'Email', "sClass": "idProd" },
          ]
        });
        //$("#ModalCliente").modal("show");
      }else if (respuesta == "m") {
          $("#txtCliente").val("");
          $("#ModalClienteAñadir").modal("hide");
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
            { mRender : function(data, type, row){
              return "<a onclick='EditarCliente("+ row.IdCliente +");' class='btn'><i class='fa fa-pencil'></i></a>"
            }},
            { mData: 'Direccion', "sClass": "idProd" },
            { mData: 'Telefono', "sClass": "idProd"},
            { mData: 'Email', "sClass": "idProd" },
            ]
          });
        $("#ModalCliente").modal("show");
      }

      $('#txtDniAddId').val('')
      $('#txtClienteAddId').val('')
      $('#txtDireccionAddId').val('')
      $('#txtTelefonoAddId').val('')
      $('#txtEmailAddId').val()
    },
  });

  console.log(xhr);
});


} );

function obtenerSerie(idPuntoVenta, idTipoDoc) {
  $.ajax({
    url: '../controllers/server_processingPuntoVentaDet.php?idPuntoVenta=' + idPuntoVenta + '&idTipoDoc=' + idTipoDoc,
    dataType: 'json',
    success: function(respuesta){
       $('#txtSerie').val(respuesta['Serie'])
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    }
  });
}

function EliminarMetPago(id) {
  $("#tableMetodoPago tbody").find("."+id).remove();
  check();
}

function EditarCliente(idCliente) {
  //console.log("se hizo click" + idCliente);
  $("#tableCliente tbody tr").each(function(e){
    if (idCliente == $(this).children("td").eq(0).text()) {
      //console.log($(this).children("td").eq(0).text());
      $("#txtClienteAddId").val($(this).children("td").eq(1).text());
      $("#txtDniAddId").val($(this).children("td").eq(2).text());
      $("#txtDireccionAddId").val($(this).children("td").eq(4).text());
      $("#txtTelefonoAddId").val($(this).children("td").eq(5).text());
      $("#txtEmailAddId").val($(this).children("td").eq(6).text());
      $("#txtIdClienteAdd").val($(this).children("td").eq(0).text());
    }
  });
  $("#ModalClienteAñadir").modal("show");
}

function ListarProducto(almacen, serverSide = false){
  if (serverSide) {
    serverSide = true;
    ajaxSource = "../controllers/server_processingReporteStock.php?serverSide=1&almacen=" + almacen;
  } else {
    serverSide = false;
    ajaxSource = "../controllers/server_processingReporteStock.php?almacen=" + almacen;
  }
      $("#tableProducto").DataTable().destroy();
          var table4 = $("#tableProducto").DataTable({
            "serverSide": serverSide,
            "bProcessing": true,
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "sAjaxSource": ajaxSource,
            /*"ajax":{
              "url": "../controllers/server_processingReporteStock.php?serverSide=1",
              "type": "get",
              "data": {
                "almacen" : almacen
              }
            },*/
            "aoColumns": [
              { mData: 'numero', sClass: "idProd" } ,
              { mData: 'Producto' } ,
              { mData: 'PrecioContado' },
              { mData: 'PrecioPorMayor' },
              { mData: 'StockPorMayor' },
              { mData: 'formafarmaceutica' },
              { mData: 'marca' },
              { mData: 'Codigo' },
              { mData: 'ProductoMedicion' },
              { mData: 'stock' },
              /*{ mRender: function ( data, type, row ) {
                return '';
              } }*/
              { mData: 'VentaEstrategica' },
              { mData: 'IdLote' },
              { mData: 'FechaVen' }
            ],
            "initComplete": function( settings, json ) {
              window.isLoadStock = true;
            }
        });
        
      $('#tableProducto tbody').off("click").on('click', 'tr', function () {
        var id = $(this).children("td").eq(0).text();
        $("#txtPrecio").prop("readonly", true);
        $("#tablePuntoVentaDet tbody tr").each(function(){
          if (id === $(this).children("td").eq(0).text()) {
            $("#txtPrecio").prop("readonly", false);
          }else {
            $("#txtPrecio").prop("readonly", true);
          }
        });

        $("#tempId").val($(this).children("td").eq(0).text());
        $("#tempProducto").val($(this).children("td").eq(1).text());
        $("#tempLote").val($(this).children("td").eq(11).text());
        $("#tempFechaVen").val($(this).children("td").eq(12).text());

        $("#txtPrecio").val($(this).children("td").eq(2).text());
        $('.spinner input').val("1");
        if ($('.spinner input').val()<="1") {
          $("#btnCaretDown").prop("disabled", true); $("#txtTotal").val(($("#txtPrecio").val()*$('.spinner input').val()).toFixed(2));
        } else{
          $("#btnCaretDown").prop("disabled", false); $("#txtTotal").val(($("#txtPrecio").val()*$('.spinner input').val()).toFixed(2));
        }
        $("#ModalBuscarProductoDet").modal("show");

    });
}


function check () {
        var valoresT = 0;
        var valoresE = 0;
        $("#tableMetodoPago tbody tr").each(function(){
          if($(this).find("td").eq(0).text() == "Visa" || $(this).find("td").eq(0).text() == "Mastercard"){
              var num = parseFloat($(this).find("td").eq(2).text());
              valoresT += parseFloat(isNaN(num) ? 0.00 : num);
          }else{
              var num = parseFloat($(this).find("td").eq(2).text());
              valoresE += parseFloat(isNaN(num) ? 0.00 : num);
          }
  });
  $("#txtTotalPago").val((valoresT+valoresE).toFixed(2));
  $("#txtCambio").val((($("#txtTotalPagar").val() - $("#txtTotalPago").val())*(-1)).toFixed(2));
}

function fn_EliminarVenta(idprod){
      console.log(idprod);
     $(idprod).closest('tr').remove();
  fn_SumarProd();

}


function fn_SumarProd(){
    var Total = 0;
    $("#tablePuntoVentaDet tbody tr").each(function(index, el) {
    //console.log($(this).find('td').eq(4).html());
    var Tot = $(this).find('td').eq(4).html();
    Total = parseFloat(Total) + parseFloat(Tot);
    //console.log(Total;
    });
    //console.log(Total);
    // console.log(Total.toFixed(2));
    $("#txtSubTot").val(Total.toFixed(2));
    $("#txtTotalGen").val(Total.toFixed(2));
}

function cargarPreOrden(row) {

  var xhr = $.ajax({
    url: '../controllers/server_processingPreOrden.php?idPreOrden=' + row.IdPreOrden,
    dataType: 'json',
    success: function(respuesta){
       row.Productos = respuesta

       $.each(row.Productos, function(index, value) {
           var fila = "<tr><td class='idProd'>"+value.IdProducto+"</td><td class='nombreProducto'>"+value.Producto+"</td><td>"+value.Cantidad+ "</td><td>" +value.Precio+ "</td><td>"+parseFloat(value.Precio)*parseFloat(value.Cantidad)+"</td><td class='nombreProducto'>"+ ((value.Lote === null)?'':value.Lote) + "</td><td class='nombreProducto'>"+ ((value.FechaVen === null)?'':value.FechaVen) +"</td><td><a id='EliminarVenta' class='btn' onclick='fn_EliminarVenta(this);' ><i class='fa fa-trash'></i></a></td></tr>";
           $("#tablePuntoVentaDet tbody").append(fila);

       })
       var Total = 0;
       $("#tablePuntoVentaDet tbody tr").each(function(index, el) {
         //console.log($(this).find('td').eq(4).html());
         var Tot = $(this).find('td').eq(4).html();
         Total = parseFloat(Total) + parseFloat(Tot);
         //console.log(Total;
       });
       $("#txtSubTot").val(Total.toFixed(2));
       $("#txtTotalGen").val(Total.toFixed(2));

       $("#modalPreOrden").modal("hide");

    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    }
  });

  $("#txtCliente").val(row.Cliente);
  $("#tablePuntoVentaDet tbody tr").remove();




}

</script>

 <body>
<?php include("header.php"); ?>
<div class="container">
<!-- nuevo -->
<div class="" style="margin-left:10px; margin-right:10px;">
  <div class="row">
    <div class="col-xs-6 col-md-3">
      <div class="input-group" style="margin-bottom:20px;">
        <input type="text" class="form-control" id="txtTipoVenta" placeholder="" >
        <span class="input-group-btn">
          <button id="btnTipoVenta" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
        </span>
      </div>
    </div>
    <div class="col-xs-6 col-md-3">
      <div class="input-group" style="">
        <input id="txtAlmacen" type="text" class="form-control" placeholder="Venta">
        <span class="input-group-btn">
          <button id="btnAlmacen" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
        </span>
      </div>
    </div>
    <div class="col-xs-6 col-md-6">
      <div class="pull-right">
        <?php
        	$result = fn_devolverFecha();
        	while ($row = mysqli_fetch_array($result)) {
        	echo '<input type="text" id="fecha" class="form-control fechaActual" disabled placeholder="fecha" value="'.$row[0].'">';
        	}
         ?>
      </div>

      <div class="pull-right">
        <?php
            /*$result = fn_devolverPuntodeVenta("", "");
            while ($row = mysqli_fetch_array($result)) {
            echo '<input type="text" id="txtSerie" class="form-control puntoVentaActual" readonly placeholder="fecha" value="'.$row[2].'">';
            }*/
          ?>
          <input type="text" id="txtSerie" class="form-control puntoVentaActual" readonly value="">
      </div>
    </div>
  </div>
</div>
<!-- /nuevo -->

  <div class="" style="margin-left:10px; margin-right:10px;">
    <div class="row">
      <div class="col-xs-6">
        <div class="input-group" style="margin-bottom:20px;">
  				<input type="text" id="txtCliente" class="form-control" value="-">
  				<span class="input-group-btn">
  					<button id="btnCliente" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
  				</span>
  			</div>
      </div>
    </div>

  </div>
      <div class="" style="margin-left:10px; margin-right:10px;">
        <div class="row">
          <div class="col-xs-6">
            <div class="input-group" style="margin-bottom:20px;">
                <input type="text" class="form-control" placeholder="Producto">
                <span class="input-group-btn">
                <button id="btnProducto" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
               </span>
            </div>
          </div>
        </div>
      </div>

<div class="panel panel-default" style="overflow-y:auto;">
  <div class="panel-heading">Productos Seleccionados</div>

  <!-- Table -->
  <table id="tablePuntoVentaDet" class="table table-striped table-bordered">
    <thead>
    	<th class="idProd">#</th>
    	<th>Producto</th>
    	<th>Cantidad</th>
    	<th>Precio</th>
        <th>Tot.</th>
        <th>Lote.</th>
    	<th>FechaVencimiento.</th>
    </thead>
    <tbody>

    </tbody>
  </table>
</div>


<div class="row">
  <div class="col-xs-12">
    <div class="checkbox" id="EsCreditoDiv">
      <label for=""><input type="checkbox" id="txtCredito" name="" value=""> Credito</label>
      <div class="" id="FechaVen">
          <input type="date" name="" id="txtFechaCredito" value="<?php echo date('Y-m-d'); ?>" >
      </div>
    </div>
  </div>
</div>

 <div class="row">
  <div class="col-xs-4 col-md-2 col-md-offset-6">
  	<div class="form-group">
    <div class="input-group" style="margin-bottom:20px;">
      <span class="input-group-addon">Sub.</span>
      <input id="txtSubTot" readonly type="text" class="form-control" value="0.00">
    </div>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
  	<div class="form-group">
 		<div class="input-group" style="margin-bottom:20px;">
      <span class="input-group-addon">IGV.</span>
 			<input id="txtIGV" type="text" readonly class="form-control" value="0.00">
 		</div>
    </div>
  </div>
<div class="col-xs-4 col-md-2">
  <div class="form-group">
 		<div class="input-group" style="margin-bottom:20px;">
      <span class="input-group-addon">Total.</span>
 			<input id="txtTotalGen" readonly type="text" class="form-control" value="0.00">
 		</div>
    </div>
  </div>


</div>

<div class="pull-right">
  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalPreOrden"><i class="fa fa-upload fa-lg"></i>Cargar Pre orden</button>
  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalProformaVenta"><i class="fa fa-file-pdf-o fa-lg"></i>Crear Proforma</button>
  <button id="btnSave" type="button" class="btn btn-primary" name="button"><i class="fa fa-money fa-lg"></i>   Efectuar pago</button>
  <button id="btnClean" type="button" class="btn btn-warning" name="button"><i class="fa fa-eraser fa-lg"></i>Limpiar</button>
</div>

</div>
<?php include("footer.php"); ?>
 </body>

<!-- PRODUCTO -->
  <div class="modal fade" id="ModalBuscarProducto" role="dialog">
  <div class="modal-dialog" style="width:1000px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Producto</h4>
      </div>
      <div class="modal-body">
        <div class="sTableProducto" style="overflow-x:auto;">
          <table id="tableProducto" class="table table-striped table-bordered">
            <thead>
             <th class="idProd">#</th>
             <th>Producto</th>
             <th>Precio Contado</th>
             <th>Precio Por Mayor</th>
             <th>Stock Por Mayor</th>
             <th>Forma</th>
             <th>Laboratorio</th>
             <th>Codigo</th>
             <th>Medicion</th>
             <th>Stock</th>
             <th>Venta Estrategica</th>
             <th>Lote</th>
             <th>FechaVencimiento</th>
            </thead>
        </table>
        </div>
      </div>
    </div>
  </div>
 </div>

 <!-- PRODUCTO DETALLE -->
  <div class="modal fade" id="ModalBuscarProductoDet" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <div class="form-inline pull-right">
              	<label for="">PRECIO. </label>
              	<input type="text" id="txtPrecio" class="form-control">
              </div>
      </div>
      <div class="modal-body">
        <div class="ProductoDet">
  				<div class="input-group spinner">
    			<input id="txtCantidad" type="text" value="1">
    				<div class="input-group-btn-vertical">
      					<button class="btn btn-default" type="button"><i class="fa fa-caret-up fa-4x"></i></button>
      					<button id="btnCaretDown" class="btn btn-default" type="button"><i class="fa fa-caret-down fa-4x"></i></button>
    				</div>
      			</div>
      			<input id="tempId" type="hidden">
                <input id="tempProducto" type="hidden">
                <input id="tempLote" type="hidden">
    			<input id="tempFechaVen" type="hidden">
      			<hr/>
      			<div class="input-group">
      				<span class="input-group-addon">S/.</span>
        			<input id="txtTotal" type="text" readonly="readonly" class="form-control currency" id="c2" />
    			</div>
    	</div>
  </div>
  		<div class="modal-footer">
 			<button id="btnTotal" class="btn btn-danger" type="button">OK</button>
 		</div>
 	</div>
 	</div>
 </div>

<!-- TIPO VENTA -->
<div class="modal fade" id="ModalBuscarTipoVenta" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Tipo Venta</h4>
      </div>
      <div class="modal-body">
        <div class="sTableTipoDocVenta">
          <table id="tableTipoDocVenta" class="table table-striped table-bordered">
            <thead>
             <th class="idProd" style="display: table-cell;">ID</th>
             <th>Producto</th>
             <th>Tiene IGV</th>
             <th>Limite Productos</th>
            </thead>
              <tbody>
                <?php
                   $result = fn_devolverTipoDocVenta("", "");
                    while ($row = mysqli_fetch_array($result)) {
                    	if ($row["TipoDoc"]) {
                    		echo '<tr>';
                     		echo '<td class="idTipoDoc">'.$row["IdTipoDoc"]."</td>";
                        echo "<td>".$row["TipoDoc"]."</td>";
                        echo "<td>".$row["TieneIgv"]."</td>";
                     		echo "<td>".$row["LimiteItems"]."</td>";
                     		echo "</tr>";
                    	}
                     }
                    ?>
              </tbody>
        </table>
        </div>
      </div>
    </div>
  </div>
 </div>

 <!-- ALMACEN -->
<div class="modal fade" id="ModalAlmacen" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">ALMACEN</h4>
      </div>
      <div class="modal-body">
        <div class="sTableAlmacen">
          <table id="tableAlmacen" class="table table-striped table-bordered">
            <thead>
             <th class="">#</th>
             <th>Almacen</th>
            </thead>
              <tbody>
                <?php
                   $result = fn_devolverAlmacen("", "");
                    while ($row = mysqli_fetch_array($result)) {
                    		echo '<tr>';
                     		echo '<td class="idTipoDoc">'.$row["IdAlmacen"]."</td>";
                     		echo "<td>".$row["Almacen"]."</td>";
                     		echo "</tr>";
                     }
                    ?>
              </tbody>
        </table>
        </div>
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
              <th>Editar</th>
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

  <!-- AÑADIR CLIENTE -->
<div class="modal fade" id="ModalClienteAñadir" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Cliente</h4>
      </div>
      <div class="modal-body">
        <div>
          <form id="formAddCliente">

            <div>
              <label>DNI/RUC</label>
              <div class="input-group">
                <input type="text" id="txtDniAddId" name="txtDniAdd" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button">
                    Consulta DNI/RUC <span class="caret"></span>
                  </button>
                     <ul class="dropdown-menu">
                      <li><a href="#" onclick="consultarDNIRUC(
                        $('#txtDniAddId').val(), 'DNI',
                        function(cliente) {
                          $('#txtClienteAddId').val(cliente.nombres)
                        }
                      )">Consulta por DNI</a></li>
                      <li><a href="#" onclick="consultarDNIRUC(
                        $('#txtDniAddId').val(), 'RUC',
                        function(cliente) {
                          $('#txtClienteAddId').val(cliente.RazonSocial)
                          $('#txtTelefonoAddId').val(cliente.Telefono)
                          $('#txtDireccionAddId').val(cliente.Direccion)
                        }
                      )">Consulta por RUC </a></li>
                    </ul>
                </span>
              </div>
            </div>

            <div class="input-group">
              <label>Nombre o Razón Social</label>
              <input type="text" id="txtClienteAddId" name="txtClienteAdd" class="form-control">
            </div>

            <div class="input-group">
              <label>Direccion</label>
              <!-- <input type="text" id="txtDireccionAddId" name="txtDireccionAdd" class="form-control"> -->
              <textarea name="txtDireccionAdd" class="form-control" id="txtDireccionAddId" rows="3" cols="40"></textarea>
            </div>
            <div class="input-group">
              <label>Telefono</label>
              <input type="text" id="txtTelefonoAddId" name="txtTelefonoAdd" class="form-control">
            </div>
            <div class="input-group">
              <label>Email</label>
              <input type="text" id="txtEmailAddId" name="txtEmailAdd" class="form-control">
            </div>
            <input type="hidden" id="txtIdClienteAdd" name="txtIdClienteAddName" value="">
        </div>
      </div>
      <div class="modal-footer">
        <input type="submit" id="btnAddCliente" class="btn btn-success" value="Añadir" >
      </div>
      </form>
    </div>
  </div>
 </div>

<!-- MetPago -->
 <div class="modal fade" id="ModalMetPago" role="dialog">
   <div class="modal-dialog">
     <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title">Elegir Metodo de pago</h4>
       </div>
       <div class="modal-body">
         <div class="">
           <div class="row">
             <?php
                $result = fn_devolverMetPago();
                while ($row = mysqli_fetch_array($result)) {
                  echo '<div class="col-md-4">';
                  echo '<button id="mP'.$row["MetodoPago"].'" class="btn btn-info btn-lg">'.$row["MetodoPago"]."</button>";
                  echo "</div>";
                }
              ?>
           </div>
           <hr>
           <div class="input-group" style="margin-bottom:20px;">
              <span class="input-group-addon">S/.</span>
              <input id="txtTotalPagar" readonly type="text" class="form-control" value="0.00">
            </div>
           <hr>
           <br>
           <div class="panel panel-default" style="overflow-y:auto;">
           <div class="panel-heading">Productos Seleccionados</div>
          <!-- Table -->
          <table id="tableMetodoPago" class="table table-striped table-bordered">
           <thead>
              <th>Metodo de pago</th>
              <th>Descripcion</th>
              <th>Soles S/.</th>
           </thead>
            <tbody>

            </tbody>
           </table>
          </div>
          <hr>
            <div class="input-group" style="margin-bottom:20px;">
              <span class="input-group-addon">Total S/.</span>
              <input id="txtTotalPago" readonly type="text" class="form-control" value="0.00">
            </div>
            <div class="input-group" style="margin-bottom:20px;">
              <span class="input-group-addon">Cambio S/.</span>
              <input id="txtCambio" readonly type="text" class="form-control" value="0.00">
            </div>
         </div>
       </div>
       <div class="modal-footer">
         <button type="button" id="btnGuardarMetPago" class="btn btn-success" name="button"><i class="fa fa-print"></i>  Imprimir pago</button>
         <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>  Cancelar</button>
       </div>
     </div>
   </div>
  </div>
  <!-- MODAL CREAR PROFORMA -->
  <?php
  Modal::render('ModalPreOrden', [
      'id' => 'modalPreOrden',
      'controller' => 'server_processingPreOrden'
  ]);

  Modal::render('ModalProforma', [
      'id' => 'modalProformaVenta',
      'controller' => 'server_processingCajaBanco',
      'clone' => '#tablePuntoVentaDet'
  ]);
  ?>
 </html>
