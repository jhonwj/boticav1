<?php
include_once("../clases/helpers/Modal.php");
?>

<html>
<head>
	<title>Inventario</title>
</head>
<?php include_once 'linker.php'; ?>

<style media="screen">
	#modalAddProveedor{
		/*/width: 700px; !important*/
	}
</style>

<script type="text/javascript">

$(document).ready(function(e){

calcularFlete();
  $("#btnAlmacenOrigen").prop("disabled", true);
  $("#btnAlmacenDestino").prop("disabled", true);
  $("#btnProveedor").prop("disabled", true);

	$("#FechaVen").hide();

	$("#txtPeriodoT").on("keyup paste input change",function(){
		if ($(this).val().length==6) {
			var sub = $(this).val().substr(4,2);
			if (sub>12) {
				  $(this).val(parseInt(parseInt($(this).val().substr(0,4)) + 1).toString() + "01");
			}else if (sub<1) {
				$(this).val(parseInt(parseInt($(this).val().substr(0,4)) - 1).toString() + "12");
			}
		}
		console.log(sub);
		console.log($(this).val().length);
	});
	$("#txtPeriodoT").blur(function(){
		if ($(this).val().length==6) {
			$(this).closest("div").removeClass("has-error");
			$(this).next().addClass("hide");
			$("#btnGuardarMov").prop("disabled", false);
		}else {
			$(this).closest("div").addClass("has-error");
			$(this).next().removeClass("hide");
			$("#btnGuardarMov").prop("disabled", true);
		}
	});

	$("#txtFechaStock").on("keyup paste input change",function(){
		var anio = $(this).val().substr(0,4);
		var mes = $(this).val().substr(5,2);
		console.log($(this).val());
		console.log(anio);
		console.log(mes);
		$("#txtPeriodoT").val(anio + mes);
	});

	$("#txtISC").on("change", function(){
		// $(this).val();
		// console.log("nel perro");
		// console.log("cambio "+ $(this).val());
		$("#txtTotal").val(parseFloat(parseFloat(isNaN($(this).val())?0:$(this).val()) + parseFloat($("#txtSubTotal").val()) + parseFloat($("#txtIGV").val()) + parseFloat($("#txtFlete").val())).toFixed(2));
	});

	$("#txtFlete").on("change", function(){
		$("#txtTotal").val(parseFloat(parseFloat(isNaN($(this).val())?0:$(this).val()) + parseFloat($("#txtSubTotal").val()) + parseFloat($("#txtIGV").val()) + parseFloat($("#txtISC").val())).toFixed(2));
	});

	$("#EsCreditoDiv").click(function(e){
		if($("#txtCredito").is(":checked")){
			$("#FechaVen").show();
		}else{
			$("#FechaVen").hide();
		}
	});

	$("#btnProveedor").click(function(e){
		listarProveedor();
		$("#modalProveedor").modal("show");
	});

	$("#btnAddProveedor").click(function(e){
		// $("#modalAddProveedor").find(".modal-body").css("width", "700");
    $("#form_proveedor").trigger("reset");
    $("#txtIdProveedor").val("");
		$("#modalAddProveedor").modal("show");
	});

	$("#form_proveedor").submit(function(e){
		e.preventDefault();
		console.log($(this).serializeArray());
		var xhr = $.ajax({
			url: "../controllers/Lo_ProveedorGuardar.php",
			type: "post",
			data: {proveedor : JSON.stringify($(this).serializeArray())},
			dataType: "html",
			success : function(respuesta){
				if (respuesta=="Nuevo") {
					$("#modalAddProveedor").modal("hide");
          $("#modalProveedor").modal("show");
          listarProveedor();
				}else if(respuesta=="Modificado"){
          $("#modalAddProveedor").modal("hide");
          $("#modalProveedor").modal("show");
          listarProveedor();
        }
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				alert("Status " + textStatus);
				alert("errorThrown " + errorThrown);
			}
		});
		console.log(xhr);
	});

	$("#btnAddIProducto").click(function(e){
      ListarProductosDet();
      $("#txtProductoCantidadI").val("0");
      $("#txtProductoPrecioI").val("0.00");
      $("#modalProductoDet").modal("show");
    });

    $("#btnGuardarProductoDet").on({
      click : function(e){
		  if ($('#txtProductoPrecioI').val() == '0.00' || $('#txtProductoPrecioI').val() == '' || $('#txtProductoPrecioI').val() == '0') {
		  	alert('Establezca un precio');
			return;
		  }
        agregarProductoDet($("#txtProductoCantidadI").val(), $("#txtProductoPrecioI").val());
        $("#modalCantidadI").modal("hide");
        $("#modalProductoDet").modal("hide");
      },
      keydown : function(e){
        console.log(e.which);
        if(e.which == "13"){
          agregarProductoDet($("#txtProductoCantidadI").val(), $("#txtProductoPrecioI").val());
          $("#modalCantidadI").modal("hide");
          $("#modalProductoDet").modal("hide");
        }
      }
    });

    $("#txtProductoCantidadDet").keydown(function(e){
      if (e.which == 13) {
          agregarProductoDet($("#txtProductoCantidadI").val(), $("#txtProductoPrecioI").val());
          $("#modalCantidadI").modal("hide");
          $("#modalProductoDet").modal("hide");
      }
    });

    $("#btnTipoMovimiento").click(function(e){
    	ListarTipoMovimiento();
    	$("#modalTipoMovimiento").modal("show");
    });

    $("#btnAlmacenDestino").click(function(){
      $("#modalAlmacen").modal("show");
      ListarAlmacen("destino");
      AgregarAlmacen("destino");
    });

    $("#btnAlmacenOrigen").click(function(){
      $("#modalAlmacen").modal("show");
      ListarAlmacen("origen");
      AgregarAlmacen("origen");
    });

    $("#btnNuevoAlmacen").click(function(){
      $("#txtAlmacenNuevo").val("");
      $("#modalNuevoAlmacen").modal("show");
    });

   /* $(".inputsIgv").click(function(){
      var checkbox1 = $("#idCheckIGV", this).val();
      console.log("clicked!" + checkbox1);
    });*/
		$("#txtPercepcion").keyup(function(e){
				$("#txtPercepcionTotal").val(parseFloat(parseFloat($("#txtPercepcion").val()) + parseFloat($("#txtTotal").val())).toFixed(2));
			});

    $("#btnGuardarMov").click(function(e){
			var dateNull = 0;
			$("#tableProductoI tbody tr").each(function(){
					if ($(this).children("td").eq(11+4).find("input[type='date']").val() == "") {
						alert("Por favor ingrese la fecha de vencimiento del producto : " + $(this).children("td").eq(1+4).text() );
						dateNull++;
						return false;
					}
			});
			if (dateNull==0) {
				var Movimiento = [];
	      Movimiento.push([$("#txtTipoMovimiento").val(), $("#txtProveedor").val(), $("#txtSerie").val(), $("#txtNumero").val(), $("#txtFecha").val(), $("#txtAlmacenOrigenTemp").val(), $("#txtAlmacenDestinoTemp").val(), $("#txtObservacion").val(),
													$("#txtFechaStock").val(), $("#txtPercepcion").val(), $("#txtCredito").is(":checked"), $("#txtFechaCredito").val(), $("#txtPeriodoT").val(), parseFloat($('#txtTipoCambio').val()).toFixed(2), $('#txtMoneda').val()]);

	      console.log(JSON.stringify(Movimiento));
	      var arrTableProductos = [];
	      $("#tableProductoI tbody").each(function(){
	        $("#tableProductoI tbody tr").each(function(){
	            arrTableProductos.push([$(this).children("td").eq(0).html(), $(this).children("td").eq(1+4).html(), $(this).children("td").eq(2+4).html(), $(this).children("td").eq(3+4).html(),
							 												$(this).children("td").eq(4+4).find("#idCheckIGV").is(":checked"), $(this).children("td").eq(6+4).text(), $(this).children("td").eq(8+4).text(),$(this).children("td").eq(10+4).text(),
						 													$(this).children("td").eq(11+4).find("input[type='date']").val(), $(this).children("td").eq(12+4).find("input").val()])
	        });
	      });

        var remision = {
          'PartidaDist': $('#txtPartidaDist').val(),
          'PartidaProv': $('#txtPartidaProv').val(),
          'PartidaDpto': $('#txtPartidaDpto').val(),
          'LlegadaDist': $('#txtLlegadaDist').val(),
          'LlegadaProv': $('#txtLlegadaProv').val(),
          'LlegadaDpto': $('#txtLlegadaDpto').val(),
          'DestinatarioRazonSocial': $('#txtDestinatarioRazonSocial').val(),
          'DestinatarioRUC': $('#txtDestinatarioRUC').val(),
          'TransporteNumPlaca': $('#txtTransporteNumPlaca').val(),
          'TransporteNumContrato': $('#txtTransporteNumContrato').val(),
          'TransporteNumLicencia': $('#txtTransporteNumLicencia').val(),
          'TransporteRazonSocial': $('#txtTransporteRazonSocial').val(),
          'TransporteRUC': $('#txtTransporteRUC').val(),
          'IdDocVenta': $('#txtFactura').attr('data-iddocventa') || 0
        }

	      console.log(JSON.stringify(arrTableProductos));
	        var xhr = $.ajax({
	          url: "../controllers/Lo_MovimientoGuardar.php",
	          type: 'post',
	          data : {movimiento:JSON.stringify(Movimiento), producto:JSON.stringify(arrTableProductos), remision:JSON.stringify(remision)},
	          dataType : "html",
	          success: function(res){
	            if (res == "E") {
	            	alert("El movimiento ya existe");
	            } else if (res == "OK") {
	            	alert("Movimiento registrado");
								Limpiar();
	            } else {
	            	alert("Por favor ingresar los datos correctos");
	            }
	          },
	          error : function(XMLHttpRequest, textStatus, errorThrown){
	            alert("Status " + textStatus);
	            alert("errorThrown " + errorThrown);
	          }
	        });
	        console.log(xhr);
			}
			});


// buscar docVenta
$('#btnBuscarFactura').click(function() {
  $("#tableFactura").DataTable().destroy();
  
  $("#tableFactura").DataTable({
			"bProcessing": true,
			"sAjaxSource": "../controllers/listarRegVenta.php?codSunat=01&datatable=1&fechaIni="+$("#fechaIni").val()+"&fechaFin="+$("#fechaFinal").val()+"&declarado="+$("#declarado").prop("checked"),
			"bPaginate":true,
			"sPaginationType":"full_numbers",
			"iDisplayLength": 5,
			"aoColumns": [
				{ mData: 'idDocVenta' } ,
				{ mData: 'Serie' } ,
				{ mData: 'Numero' },
				{ mData: 'TipoDoc' }
			],
			"rowCallback": function(row, data, index){
        $(row).on('click', function() {
          console.log(data)
          var xhr = $.ajax({
            url: '../controllers/serverprocessingProductosRegVenta.php',
            type: 'get',
            data:  {"idDocVenta" : data.idDocVenta},
            dataType: 'html',
            success: function(respuesta){
                var response = JSON.parse(respuesta);
                $('#tableProductoI tbody tr').remove();
                $('#txtFactura').val(data.Serie + '-' + data.Numero)
                $('#txtFactura').attr('data-iddocventa', data.idDocVenta)
                $.each(response, function(data, value){
                  $('#tempIdProductoDet').val(value.IdProducto)
                  $('#tempProductoDetCodigo').val(value.Codigo)
                  $('#tempProductoDetMarca').val(value.ProductoMarca)
                  $('#tempProductoDetForma').val(value.ProductoFormaFarmaceutica)
                  $('#tempProductoDetMedida').val(value.ProductoMedicion)
                  $('#tempProductoDet').val(value.Producto)
                  agregarProductoDet(value.Cantidad, value.Precio, true);
                });
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus); alert("Error: " + errorThrown);
            }
          });

          //agregarProductoDet($("#txtProductoCantidadI").val(), $("#txtProductoPrecioI").val());
          

          $('#modalFactura').modal('hide');
        })
			}
	});
  
})


// cargar numero cuando la serie cambia
$('#txtSerie').keyup(function() {
  if($('#txtTipoMovimiento').attr('data-tipo') == "1" || $('#txtTipoMovimiento').attr('data-tipo') == "2") {
    LLenarNumero($('#txtSerie').val())
  }
})


});

function AgregarAlmacen(almacen1){
      $("#form_almacen").submit(function(e){
          e.preventDefault();
    console.log($(this).serializeArray());
    var xhr = $.ajax({
      url: "../controllers/Lo_AlmacenGuardar.php",
      type: "post",
      data: {almacen : JSON.stringify($(this).serializeArray())},
      dataType: "html",
      success : function(respuesta){
        if (respuesta=="SI") {
          $("#modalNuevoAlmacen").modal("hide");
          ListarAlmacen(almacen1);
        }
      },
      error : function(XMLHttpRequest, textStatus, errorThrown){
        alert("Status " + textStatus);
        alert("errorThrown " + errorThrown);
      }
    });
    console.log(xhr);
    });
}

function ListarProductosDet(){
          $("#tableProductoDetListar").DataTable().destroy();
          var table4 = $("#tableProductoDetListar").DataTable({
            "serverSide": true,
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingProducto.php?serverSide=1",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
							{ mData: 'IdProducto' } ,
							{ mData: 'Codigo' } ,
							{ mData: 'ProductoMarca' } ,
            { mData: 'ProductoFormaFarmaceutica' } ,
						{ mData: 'Producto' },
						{ mData: 'ProductoMedicion' },
						{ mData: 'PrecioContado' },

            ]
        });

        $("#tableProductoDetListar tbody").on("click", "tr", function(e) {

        $("#tempIdProductoDet").val($(this).children("td").eq(0).html());
				$("#tempProductoDetCodigo").val($(this).children("td").eq(1).html());
				$("#tempProductoDetMarca").val($(this).children("td").eq(2).html());
				$("#tempProductoDetForma").val($(this).children("td").eq(3).html());
				$("#tempProductoDetMedida").val($(this).children("td").eq(5).html());
        $("#tempProductoDet").val($(this).children("td").eq(4).html());
        $("#modalCantidadI").modal("show");
        $("#txtProductoI").val($("#tempProductoDet").val());
        });
}

function ListarTipoMovimiento(){
          $("#tableTipoMovimientoListar").DataTable().destroy();
          var table4 = $("#tableTipoMovimientoListar").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingMovimientoTipo.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdMovimientoTipo' } ,
            { mData: 'TipoMovimiento' },
            { mData: 'Tipo' }
            ]
        });
        $("#tableTipoMovimientoListar tbody").on("click", "tr", function(){
          $("#txtTipoMovimiento").val($(this).children("td").eq(1).html());
          var tipo = $(this).children("td").eq(2).html();
          $('#txtTipoMovimiento').attr('data-tipo', tipo);
          if(tipo=="0"){
            $("#btnAlmacenOrigen").prop("disabled", true);
            $("#btnAlmacenDestino").prop("disabled", false);
            $("#btnProveedor").prop("disabled", false);
            $("#txtAlmacenOrigen").val("-");
            $("#txtAlmacenOrigenTemp").val("-1");
            $('#btnFactura').prop("disabled", true)
            $('#txtNumero').prop("disabled", false)
            
          }else
          if(tipo=="1"){
            $("#btnAlmacenOrigen").prop("disabled", false);
            $("#btnAlmacenDestino").prop("disabled", true);
            $("#btnProveedor").prop("disabled", true);
            $("#txtAlmacenDestino").val("-");
            $("#txtAlmacenDestinoTemp").val("-1");
            $("#txtProveedor").val("-");
            $('#btnFactura').prop("disabled", false)
            $('#txtNumero').prop("disabled", true)
            
          }else
          if(tipo=="2"){
            $("#btnAlmacenOrigen").prop("disabled", false);
            $("#btnAlmacenDestino").prop("disabled", false);
            $("#btnProveedor").prop("disabled", true);
            $("#txtProveedor").val("-");
            $('#btnFactura').prop("disabled", false)
            $('#txtNumero').prop("disabled", true)      
            
          }

          if(tipo == "1" || tipo == "2") {
            console.log(typeof $('#txtSerie').val())
            if($('#txtSerie').val().length) {
              LLenarNumero($('#txtSerie').val())
            }
          } else {
            $('#txtNumero').val('')
          }

          $("#modalTipoMovimiento").modal("hide");
        });
}

function LLenarNumero(serie) {
    var xhr = $.ajax({
			url: "../controllers/server_processingNuevoNumero.php",
			type: "get",
			data: {serie : serie},
			dataType: "json",
			success : function(respuesta){
        if(respuesta) {
          $('#txtNumero').val(respuesta.NuevoNumero)
        }else {
          $('#txtNumero').val('00001')
        }
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				alert("Status " + textStatus);
				alert("errorThrown " + errorThrown);
			}
		});
		console.log(xhr);
}



function Limpiar(){
 $("#txtAlmacenOrigen").val("");
 $("#txtAlmacenOrigenTemp").val("");
 $("#txtAlmacenDestino").val("");
 $("#txtAlmacenDestinoTemp").val("");
 $("#txtProveedor").val("");
 $("#txtTipoMovimiento").val("");
 $("#txtSerie").val("0");
 $("#txtObservacion").val("-");
 $("#txtNumero").val("0");
 $("#btnAlmacenOrigen").prop("disabled", false);
 $("#btnAlmacenDestino").prop("disabled", false);
 $("#btnProveedor").prop("disabled", false);
 $("#tableProductoI tbody").empty();
 $("#txtSubTotal").val("0");
 $("#txtTotal").val("0");
 $("#txtISC").val("0");
 $("#txtFlete").val("0");
 $("#txtIGV").val("0");
 $("#txtPercepcion").val("0");
 $("#txtPercepcionTotal").val("0");
 //$("#txtPeriodoT").val("201706");

 $('#txtPartidaDist').val('')
 $('#txtPartidaProv').val('')
 $('#txtPartidaDpto').val('')
 $('#txtLlegadaDist').val('')
 $('#txtLlegadaProv').val('')
 $('#txtLlegadaDpto').val('')
 $('#txtDestinatarioRazonSocial').val('')
 $('#txtDestinatarioRUC').val('')
 $('#txtTrasladoMotivo').val('')
 $('#txtTransporteNumPlaca').val('')
 $('#txtTransporteNumContrato').val('')
 $('#txtTransporteNumLicencia').val('')
 $('#txtTransporteRazonSocial').val('')
 $('#txtTransporteRUC').val('')
 $('#txtFactura').val('')
 $('#txtFactura').attr('data-iddocventa', '')

}

function agregarProductoDet(productoCantidadDet, productoPrecioDet, incluyeIgv = false){
          var Encontrado = 0;

              $("#tableProductoI tbody").each(function(index, el) {
                        $("#tableProductoI tbody tr").each(function(index, el) {
                        var productoDet = $(this).find('.nombreProductoDet').html();
                  if (productoDet == $(this).children("td").eq(1+4).html()) {
                 if(productoPrecioDet != $(this).children("td").eq(3+4).html()){
                    Encontrado = 0;
                    }else{
                    Encontrado = 1;
                    }
                  }else{
                    Encontrado = 0;
                  }
              });

            if (Encontrado ==0) {
            var fila = "<tr><td>"+ $("#tempIdProductoDet").val() +
						"</td><td class=''>"+ $("#tempProductoDetCodigo").val() +
						"</td><td class=''>"+ $("#tempProductoDetMarca").val() +
						"</td><td class=''>"+ $("#tempProductoDetForma").val() +
						"</td><td class=''>"+ $("#tempProductoDetMedida").val() +
						"</td><td class='nombreProductoDet'>"+ $("#tempProductoDet").val() +
						"</td><td>"+productoCantidadDet+
						"</td><td>"+productoPrecioDet+
						"</td><td class='inputsIgv'>"+"<input type='checkbox' "+(incluyeIgv ? '' : 'checked')+" onchange='checkClick($(this), "+$("#tempIdProductoDet").val().concat(productoPrecioDet)+");' class='' id='idCheckIGV' value='0'>"+
						"</td><td>"+parseFloat(parseFloat(productoCantidadDet)*parseFloat(productoPrecioDet)).toFixed(2)+
						"</td><td contenteditable='true' onkeyup='sumarISC();'>"+0+
						"</td><td>"+parseFloat(parseFloat(productoCantidadDet)*parseFloat(productoPrecioDet)*(incluyeIgv ? '0' : 0.18)).toFixed(2)+
						"</td><td contenteditable='true' onkeyup='sumarFlete();'>"+0+
						"</td><td>"+parseFloat(parseFloat(productoCantidadDet)*parseFloat(productoPrecioDet) + parseFloat(productoCantidadDet)*parseFloat(productoPrecioDet)*(incluyeIgv ? '0' : 0.18)).toFixed(2)+
						"</td><td contenteditable='true'>"+0+
						"</td><td contenteditable='true'><input type='date'>"+
						"</td><td contenteditable='false' ><input type='text' class='pesoProductoTotal' style='width:50px;'>"+
						"</td><td class='text-center'><a id='btnEliminarProducto' class='btn' onclick='EliminarProductoDet("+$("#tempIdProductoDet").val().concat(productoPrecioDet)+")'><i class='fa fa-trash'></i></a></td></tr>";
            
            $("#tableProductoI tbody").append(fila);
            console.log($("#tempIdProductoDet").val().concat(productoPrecioDet));
            console.log(String(parseFloat(parseFloat(productoCantidadDet)*parseFloat(productoPrecioDet) + parseFloat($("#idCheckIGV").val()))));
            console.log("asdasdas"+parseFloat(parseFloat($("#idCheckIGV").val())+50));
            $.notify({
              icon: 'fa fa-plus',
              message: "<strong>"+$("#tempProductoDet").val()+"</strong> Ha sido agregado"
            });
            }
      });

AgregarSubIgvTot();
}

/*function listarProveedor(){
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
        $("#txtProveedor").val($(this).children("td").eq(1).html());
        });
}*/

  function checkClick(t, e){
    console.log(t.is(":checked"));
    $("#tableProductoI tbody tr").each(function(){
      var str = $(this).children("td").eq(0).html();
      if (e == str.concat($(this).children("td").eq(3+4).html())) {
        if (t.is(":checked")) {
          $(this).children("td").eq(7+4).html(parseFloat($(this).children("td").eq(5+4).html()*0.18).toFixed(2));
          //$(this).children("td").eq(9).html(parseFloat(parseFloat($(this).children("td").eq(5).html())+parseFloat($(this).children("td").eq(7).html())).toFixed(2));
					$(this).children("td").eq(9+4).html(parseFloat(parseFloat($(this).children("td").eq(5+4).html())+parseFloat($(this).children("td").eq(7+4).html())+
					parseFloat($(this).children("td").eq(6+4).html())+parseFloat($(this).children("td").eq(8+4).html())).toFixed(2));
        }else{
          $(this).children("td").eq(7+4).html("0")
          $(this).children("td").eq(9+4).html(parseFloat(parseFloat($(this).children("td").eq(2+4).html())*parseFloat($(this).children("td").eq(3+4).html())));
        }
      }
    });
    AgregarSubIgvTot();
  }

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
            { mData: 'Observacion' },
            { mRender : function(data, type, row){
             return "<a onclick='EditarProveedor("+ row.IdProveedor +");' class='btn'><i class='fa fa-pencil'></i></a>"
            }}
            ]
        });
      $("#tableProveedor tbody").on("click", "tr", function(){
        $("#txtProveedor").val($(this).children("td").eq(1).text());
        $("#modalProveedor").modal("hide");
        });
}

function ListarAlmacen(almacen){
  $("#tableAlmacen").DataTable().destroy();
    var table4 = $("#tableAlmacen").DataTable({
            "bProcessing": true,
            //"responsive" : true,
            "sAjaxSource": "../controllers/server_processingAlmacen.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            //"bAutoWidth": false,
            //"autoWidth" : false,
            //"bFilter": false,
            "aoColumns": [
            { mData: 'IdAlmacen' } ,
            { mData: 'Almacen' }
            ]
        });
    if(almacen == "origen"){
      $("#tableAlmacen tbody").off('click').on("click", "tr", function(){
      $("#txtAlmacenOrigen").val($(this).children("td").eq(1).html());
      $("#txtAlmacenOrigenTemp").val($(this).children("td").eq(0).html());
      $("#modalAlmacen").modal("hide");
      });
    }
    if(almacen == "destino"){
      $("#tableAlmacen tbody").off('click').on("click", "tr", function(){
      $("#txtAlmacenDestino").val($(this).children("td").eq(1).html());
      $("#txtAlmacenDestinoTemp").val($(this).children("td").eq(0).html());
      $("#modalAlmacen").modal("hide");
      });
    }
}

function EditarProveedor(idProveedor) {
  //console.log("se hizo click" + idProveedor);
  $("#tableProveedor tbody tr").each(function(e){
    if (idProveedor == $(this).children("td").eq(0).text()) {
      //console.log($(this).children("td").eq(0).text());
      $("#txtProveedorAdd").val($(this).children("td").eq(1).text());
      $("#txtRucAdd").val($(this).children("td").eq(2).text());
      $("#txtDireccionAdd").val($(this).children("td").eq(3).text());
      $("#txtObsAdd").val($(this).children("td").eq(4).text());
      $("#txtIdProveedor").val($(this).children("td").eq(0).text());
    }
  });
  $("#modalAddProveedor").modal("show");
}

function sumarISC(){
	var sumaISC = 0;
	$("#tableProductoI tbody").each(function(){
		$("#tableProductoI tbody tr").each(function(){
			sumaISC += parseFloat($(this).children("td").eq(6+4).text()) ;
			$(this).children("td").eq(9+4).html(parseFloat(parseFloat($(this).children("td").eq(5+4).html())+parseFloat($(this).children("td").eq(7+4).html())+parseFloat($(this).children("td").eq(6+4).html())+parseFloat($(this).children("td").eq(8+4).html())).toFixed(2));
		});
	});
	$("#txtISC").val(sumaISC);
	$("#txtISC").trigger("change");
}

function sumarFlete(){
	var sumaFlete = 0;
	$("#tableProductoI tbody").each(function(){
		$("#tableProductoI tbody tr").each(function(){
			sumaFlete += parseFloat($(this).children("td").eq(8+4).text()) ;
			$(this).children("td").eq(9+4).html(parseFloat(parseFloat($(this).children("td").eq(5+4).html())+parseFloat($(this).children("td").eq(7+4).html())+parseFloat($(this).children("td").eq(6+4).html())+parseFloat($(this).children("td").eq(8+4).html())).toFixed(2));
		});
	});
	$("#txtFlete").val(sumaFlete);
	$("#txtFlete").trigger("change");
}

function EliminarProductoDet(idProductoDet){
  $("#tableProductoI tbody").each(function(){
    $("#tableProductoI tbody tr").each(function(){
      var str = $(this).children("td").eq(0).html();
      if(idProductoDet == str.concat($(this).children("td").eq(3+4).html())){
        $(this).remove();
        $.notify({
          icon: 'fa fa-plus',
          message: "<strong>"+$(this).children("td").eq(1+4).html()+"</strong> Ha sido eliminado"
        },{
          type : 'warning'
        });
      }
    });
  });
}

function AgregarSubIgvTot(){
  var SubTotal = 0;
  var Total = 0;
  var Igv = 0;
   $("#tableProductoI tbody").each(function(){
    $("#tableProductoI tbody tr").each(function(){
      SubTotal = SubTotal + parseFloat($(this).children("td").eq(5+4).html());
      Igv = Igv + parseFloat($(this).children("td").eq(7+4).html());
      Total = Total + parseFloat($(this).children("td").eq(9+4).html());
      //console.log($(this).children("td").eq(4).find("idCheckIGV").is(":checked"));
    });
   });
      $("#txtSubTotal").val(SubTotal.toFixed(2));
      $("#txtIGV").val(Igv.toFixed(2))
      $("#txtTotal").val(Total.toFixed(2));
			$("#txtPercepcionTotal").val(parseFloat(parseFloat($("#txtPercepcion").val()) + parseFloat($("#txtTotal").val())).toFixed(2));

}

function calcularFlete() {
	$("#txtFlete").keyup(function(){
		var filas = $("#tableProductoI tbody tr").length;
		if (filas > 0) {
			var fleteDistribuido = parseFloat(($("#txtFlete").val()=="")?0:$("#txtFlete").val())/filas;
			$("#tableProductoI tbody tr").each(function(e){
				$(this).children("td").eq(8+4).text(fleteDistribuido);
			});
		}
		$("#txtFlete").trigger("change");
	});

}

</script>

<body>
<?php include("header.php"); ?>

<div class="bt-panel">
	<div class="center_div" >
		<div class="center_div_form">
		<div class="row">
			<div class="col-md-4 form-group">
				<label class=""> Serie :  </label>
				<input type="text" maxlength="4" id="txtSerie" class="form-control" style="width:195px;">
				<div class="">
					<label class="control-label " for=""> Periodo Tributario :  </label>
					<input type="number" max="999999" value="<?php echo date('Ym'); ?>"  id="txtPeriodoT" class="form-control" style="width:195px;">
					<span class="help-block hide">Por favor el valor debe ser AAAAMM</span>
				</div>
			</div>
      <div class="col-md-4 form-group">
        <div>
					<label class=""> TIPO MOVIMIENTO  </label>
					<div class="form-inline">
					<input type="text" id="txtTipoMovimiento" readonly class="form-control" style="width:195px;">
					<button type="button" id="btnTipoMovimiento" class="btn btn-success" ><i class="fa fa-search-plus"></i></button>
					</div>
				</div>
        
        <label class=""> Numero :  </label>
        <input type="text" id="txtNumero"  class="form-control" style="width:195px;">
				
      </div>
      <div class="col-md-4 form-group">
        <label class=""> Fecha Contable :  </label>
        <input type="date" id="txtFecha"  class="form-control" value="<?php echo date('Y-m-d'); ?>" style="width:195px;">
				<label class=""> Fecha Stock :  </label>
        <input type="date" id="txtFechaStock"  class="form-control" value="<?php echo date('Y-m-d'); ?>" style="width:195px;">
			</div>
		</div>
		<!-- <div class="row">
			<div class="col-md-4 form-group">
				<label for="">Fecha Periodo Tributario</label>
				<input type="date" name="" id="txtPeriodoT" value="">
			</div>
		</div> -->
		<div class="row">
			<div class="col-md-6 form-group">

			</div>
		</div>
		<div class="row">
			<div class="col-md-4 form-group">
				<label class=""> Proveedor </label>
				<div class="form-inline">
				<input type="text" id="txtProveedor" readonly class="form-control">
				<button type="button" id="btnProveedor" class="btn btn-success" ><i class="fa fa-search-plus"></i></button>
				</div>
			</div>
			<div class="col-md-4 form-group">
				<label>Moneda</label>
				<div class="form-inline">
					<input type="text" id="txtMoneda" class="form-control" value="PEN">
					<button type="button" id="btnMoneda" class="btn btn-success"  data-toggle="modal" data-target="#modalMoneda"><i class="fa fa-search-plus"></i></button>
				</div>
			</div>
			<div class="col-md-4 form-group">
				<label>Tipo de Cambio</label>
				<div class="form-inline">
					<input type="text" id="txtTipoCambio" class="form-control" value="1">
					</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 iform-group">
				<label class=""> Almacen Destino </label>
				<div class="form-inline">
          <input type="hidden" id="txtAlmacenDestinoTemp">
				<input type="text" id="txtAlmacenDestino" readonly class="form-control">
				<button type="button" id="btnAlmacenDestino" class="btn btn-success" ><i class="fa fa-search-plus"></i></button>
				</div>
			</div>
			<div class="col-md-4 form-group">
				<label class=""> Almacen Origen </label>
				<div class="form-inline">
          <input type="hidden" id="txtAlmacenOrigenTemp">
				<input type="text" id="txtAlmacenOrigen" readonly class="form-control">
				<button type="button" id="btnAlmacenOrigen" class="btn btn-success" ><i class="fa fa-search-plus"></i></button>
				</div>
			</div>
      <div class="col-md-4 form-group">
        <label class=""> Observacion </label>
        <textarea class="form-control" style="width:250px;" rows="2" id="txtObservacion"></textarea>
      </div>
		</div>
    
		<div class="row">
		  <div class="col-md-4 form-group">
		    <div class="checkbox" id="EsCreditoDiv">
		      <label for=""><input type="checkbox" id="txtCredito" name="" value=""> Credito</label>
		    </div>
				<div id="FechaVen">
					<input type="date" name="" id="txtFechaCredito" value="<?php echo date('Y-m-d'); ?>">
				</div>
		  </div>
      <div class="col-md-4 form-group">
				<label>Buscar Factura</label>
				<div class="form-inline">
					<input type="text" id="txtFactura" class="form-control" readonly>
					<button type="button" id="btnFactura" disabled class="btn btn-success"  data-toggle="modal" data-target="#modalFactura"><i class="fa fa-search-plus"></i></button>
				</div>
			</div>
		</div>
    <hr/>
    <div class="row">
      <h4 class="text-center">Datos para la guia de remisión</h2>
      <br>
      <div class="col-md-3 form-group">
        <div id="PartidaDist">
          <label class=""> Punto de partida (Dist)</label>
          <input type="text" name="" id="txtPartidaDist" class="form-control" value="" style="width:195px;">
        </div>
        <div id="PartidaProv">
          <label class=""> Punto de partida (Prov) </label>
          <input type="text" name="" id="txtPartidaProv" class="form-control" value="" style="width:195px;">
        </div>
        <div id="PartidaDpto">
          <label class=""> Punto de partida (Dpto) </label>
          <input type="text" name="" id="txtPartidaDpto" class="form-control" value="" style="width:195px;">
        </div>
      </div>
      <div class="col-md-3 form-group">
        <div id="LlegadaDist">
          <label class=""> Punto de llegada (Dist)</label>
          <input type="text" name="" id="txtLlegadaDist" class="form-control" value="" style="width:195px;">
        </div>
        <div id="LlegadaDist">
          <label class=""> Punto de llegada (Prov) </label>
          <input type="text" name="" id="txtLlegadaProv" class="form-control" value="" style="width:195px;">
        </div>
        <div id="LlegadaDist">
          <label class=""> Punto de llegada (Dpto) </label>
          <input type="text" name="" id="txtLlegadaDpto" class="form-control" value="" style="width:195px;">
        </div>
      </div>
      <div class="col-md-3 form-group">
        <div id="DestinatarioRazonSocial">
          <label class=""> Destinatario - Razón Social</label>
          <input type="text" name="" id="txtDestinatarioRazonSocial" class="form-control" value="" style="width:195px;">
        </div>
        <div id="DestinatarioRUC">
          <label class=""> Destinatario RUC </label>
          <input type="text" name="" id="txtDestinatarioRUC" class="form-control" value="" style="width:195px;">
        </div>
        <div id="TrasladoMotivo">
          <label class=""> Motivo del traslado </label>
          <textarea name="" id="txtTrasladoMotivo" class="form-control" value="" rows="2" style="width:195px;"> </textarea>
        </div>
      </div>
      <div class="col-md-3 form-group">
        <div id="TransporteNumPlaca">
          <label>N° de placa</label>
          <input type="text" name="" id="txtTransporteNumPlaca" class="form-control" value="" style="width:195px;">
        </div>
        <div id="TransporteNumContrato">
          <label>N° de contrato de Inscripción</label>
          <input type="text" name="" id="txtTransporteNumContrato" class="form-control" value="" style="width:195px;">
        </div>
        <div id="TransporteNumLicencia">
          <label>N° de licencia del conductor</label>
          <input type="text" name="" id="txtTransporteNumLicencia" class="form-control" value="" style="width:195px;">
        </div>
        <div id="TransporteRazonSocial">
          <label>Razón social empresa de transporte</label>
          <input type="text" name="" id="txtTransporteRazonSocial" class="form-control" value="" style="width:195px;">
        </div>
        <div id="TransporteRUC">
          <label>RUC empresa de transporte</label>
          <input type="text" name="" id="txtTransporteRUC" class="form-control" value="" style="width:195px;">
        </div>
      </div>


    </div>
		</div>
		<br>
		<hr>
		<div class="panel panel-success">
			<div class="panel panel-heading">
				<div class="form-inline">
				<label class=""> Buscar producto </label>
				<!-- <input type="text" class="form-control"> -->
				<button type="button" id="btnAddIProducto" class="btn btn-success" ><i class="fa fa-search-plus"></i></button>
			</div>
			</div>
			<table id="tableProductoI" class="table table-bordered table-striped">
				<thead>
					<th>#</th>
					<th>Codigo</th>
					<th>Marca</th>
					<th>Forma</th>
					<th>Medida</th>
					<th>Producto</th>
					<th>Cantidad</th>
          <th>Precio</th>
          <th>Tiene IGV?</th>
          <th>SubTotal</th>
					<th>ISC</th>
					<th>IGV</th>
          <th>FLETE</th>
					<th>Total</th>
					<th>Lote</th>
					<th>FechaVencimiento</th>
					<th>Peso Total</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
  <br>
  <hr>
  <div class="row">
    <div class="col-md-2 col-md-offset-2">
      <label class="">SubTotal.</label>
      <input type="text" readonly value="0" class="form-control" id="txtSubTotal">
    </div>
    <div class="col-md-2">
      <label class="">ISC.</label>
      <input type="text" readonly value="0" class="form-control" id="txtISC">
    </div>
    <div class="col-md-2">
      <label class="">IGV.</label>
      <input type="text" readonly value="0" class="form-control" id="txtIGV">
    </div>
    <div class="col-md-2">
      <label class="">FLETE.</label>
      <input type="text" value="0" class="form-control" id="txtFlete">
    </div>
    <div class="col-md-2">
      <label class="">Total.</label>
      <input type="text" readonly  value="0" class="form-control" id="txtTotal">
			<label class="">Percepcion.</label>
      <input type="number" value="0" class="form-control" id="txtPercepcion">
			<label class="">Total c/ Percepcion</label>
      <input type="number" value="0" class="form-control" id="txtPercepcionTotal">
    </div>
  </div>
	<div class="row">
    <div class="col-md-2 pull-right">

    </div>
  </div>
	<div class="row">
    <div class="col-md-2 pull-right">

    </div>
  </div>
	<br>
	<br>
  <div class="pull-right">
    <button type="button" id="btnGuardarMov"  class="btn btn-success" id="">Guardar</button>
  </div>
</div>

<?php include("footer.php"); ?>
</body>
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

<!-- Agregar Proveedor -->
<div class="modal fade" id="modalAddProveedor" role="dialog">
	<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			Agregar Proveedor
		</div>
		<div class="modal-body">
			<div class="container">
				<form id="form_proveedor">
					<div class="form-group">
						<label for="">Nombre del Proveedor</label>
						<input type="text" id="txtProveedorAdd" required name="proveedor" placeholder="Proveedor">
					</div>
					<div class="form-group">
						<label for="">RUC</label>
						<input type="text" id="txtRucAdd" required name="ruc" placeholder="RUC">
					</div>
					<div class="form-group">
						<label for="">Direccion</label>
						<input type="text" id="txtDireccionAdd" name="direccion" placeholder="Direccion">
					</div>
					<div class="form-group">
						<label for="">Observacion</label>
						<input type="text" id="txtObsAdd" name="observacion" placeholder="Observacion">
					</div>
          <input type="hidden" id="txtIdProveedor" name="idproveedor">
			</div>
		</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-success">Agregar</button>
			<button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
			</form>
		</div>
	</div>
	</div>
</div>

 <div class="modal fade" id="modalProductoDet" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Productos </h4>
      </div>
      <div class="modal-body">
				<div class="" style="overflow-x:auto">


        <table id="tableProductoDetListar" class="table table-striped table-bordered">
          <thead>
						<th>#</th>
						<th>Codigo</th>
						<th>Marca</th>
            <th>Forma</th>
						<th>Producto</th>
            <th>Medicion</th>
						<th>Precio</th>
          </thead>
        </table>
				</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>

 <div class="modal fade" id="modalTipoMovimiento" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Productos </h4>
      </div>
      <div class="modal-body">
        <table id="tableTipoMovimientoListar" class="table table-striped table-bordered">
          <thead>
            <th>#</th>
            <th>Tipo de movimiento</th>
            <th>Tipo</th>
          </thead>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>

 <div class="modal fade" id="modalAlmacen" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Productos </h4>
      </div>
      <div class="modal-body">
        <table id="tableAlmacen" class="table table-striped table-bordered">
          <thead>
            <th>#</th>
            <th>Almacen</th>
          </thead>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btnNuevoAlmacen">Nuevo <i class="fa fa-save"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalNuevoAlmacen" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Agregar almacen</h4>
      </div>
      <div class="modal-body">
        <form id="form_almacen">
          <div class="form-group">
            <label>Almacen</label>
            <input type="text" id="txtAlmacenNuevo" name="almacen" required class="form-control" placeholder="Nombre de almacen">
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Agregar <i class="fa fa-save"></i></button>
        </form>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>

 <div class="modal fade" id="modalCantidadI" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Agregar cantidad </h4>
      </div>
      <div class="modal-body">
        <div class="container">
          <div class="input-group">
          	<input type="hidden" id="tempIdProductoDet">
						<input type="hidden" id="tempProductoDetCodigo">
						<input type="hidden" id="tempProductoDetMarca">
						<input type="hidden" id="tempProductoDetForma">
						<input type="hidden" id="tempProductoDetMedida">
          	<input type="hidden" id="tempProductoDet">
            <input type="text" id="txtProductoI" readonly class="form-control">
            <div class="separator"></div>
            <label class="">Cantidad</label>
            <input type="number" required id="txtProductoCantidadI" autofocus  class="form-control" placeholder="0.00">
          </div>
          <div class="input-group">
            <label class="">P/U</label>
            <input type="number" step="any" required id="txtProductoPrecioI"  class="form-control" placeholder="0.00" min="0">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnGuardarProductoDet" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalFactura" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Seleccionar Factura </h4>
      </div>
      <div class="modal-body">
        <div class="">
          <div class="row">
            <div class="col-md-4 form-group">
              <label>Fecha Inicio</label>
              <input type="date" id="fechaIni" class="form-control">
            </div>
            <div class="col-md-4 form-group">
              <label>Fecha Final</label>
              <input type="date" id="fechaFinal" class="form-control">
              <div class="checkbox">
                  <label><input id="declarado" type="checkbox">Declarado</label>
              </div>
            </div>
            <div class="col-md-4 form-group">
              <button class="btn btn-success" id="btnBuscarFactura">Buscar Factura</button>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <table id="tableFactura" class="table table-striped table-bordered">
                <thead>
                  <th>IdDocVenta</th>
                  <th>Serie</th>
                  <th>Numero</th>
                  <th>Tipo Doc.</th>
                </thead>
              </table>
            </div>
            
          </div>
        </div>
      </div>
      <div class="modal-footer">
       <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>




<?php
Modal::render('ModalMoneda', [
		'id' => 'modalMoneda'
]);
?>

</html>
