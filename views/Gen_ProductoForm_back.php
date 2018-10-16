<?php
include_once("../clases/BnGeneral.php");


 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Hotel - Producto</title>
  <meta charset="UTF-8">
 </head>
 <?php include_once 'linker.php'; ?>
<script type="text/javascript">

	$(document).ready(function() {

    ListarProductos();
    TablaCompuesto();
   // ListarProductoDet();

    $('#ProductoCodigoBarra').on('keypress', function(e) {
      if(e.which == 13) {
        $('#ProductoCodigo').focus()
        return false;
      }
    });
    $("#modal-form").submit(function(e){
      e.preventDefault();
      var arrTableCompuestos = [];
      $("#tableCompuestos tbody").each(function(){
        $("#tableCompuestos tbody tr").each(function(){
          arrTableCompuestos.push([parseInt($(this).children("td").eq(0).html()), $(this).children("td").eq(1).html()]);
        });
      });
      var arrTableProductoDet = [];
      $("#tableProductoDet tbody").each(function(){
        $("#tableProductoDet tbody tr").each(function(){
          arrTableProductoDet.push([parseInt($(this).children("td").eq(0).html()), $(this).children("td").eq(1).html(), $(this).children("td").eq(2).html()]);
        });
      });
      console.log(JSON.stringify($(this).serializeArray()) + " - " + JSON.stringify(arrTableCompuestos) + " - " + JSON.stringify(arrTableProductoDet));

      var xhr = $.ajax({
        url: "../controllers/server_processingProductoGuardar.php",
        type: "post",
        data: {data : JSON.stringify(arrTableCompuestos), data2 : JSON.stringify($(this).serializeArray()), data3 : JSON.stringify(arrTableProductoDet)},
        dataType: "html",
        success: function(respuesta){
        $("#nuevo").modal("hide");
        ListarProductos();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
      });
      console.log(xhr);
    });

    $("#btn-nuevo").click(function(){
       $("#modal-form").trigger("reset")
        $("#IdProducto").val("");
        $("#ProductoMarca").val("");
        $("#ProductoFormaFarmaceutica").val("");
        $("#ProductoMedicion").val("");
        $("#ProductoCategoria").val("");
        $("#Producto").val("");
        $("#ProductoDesc").val("");
        $("#ProductoDescCorto").val("");
        $("#ProductoCodigoBarra").val("0");
        $("#ProductoCodigo").val("0");
        $("#ProductoDosis").val("0");
        $("#ProductoPrecioContado").val("0");
        $("#ProductoPrecioXMayor").val("0");
        $("#ProductoStockXMayor").val("0");
        $("#txtStockMinimo").val("0");
        $("#txtStockMinimo").val("0");
        $("#tableCompuestos tbody").empty();
        $("#tableProductoDet tbody").empty();
    });

//marca
    $("#buscarMarca").click(function(event) {
       $("#ModalBuscarMarca").modal("show");
       ListarMarca();
       $('#tableProductoMarca tbody').on('click', 'tr', function () {
        $("#IdProductoMarcaH").val($(this).children("td").eq(0).html());
        $("#ProductoMarca").val($(this).children("td").eq(1).text());
        $("#ModalBuscarMarca").modal("hide");
    } );
    });

    //formafarmecutica
    $("#buscarFormaFarmaceutica").click(function(event) {
       $("#ModalBuscarFormaFarmaceutica").modal("show");
       ListarFormaFarmaceutica();
       $('#tableProductoFormaFarmaceutica tbody').on('click', 'tr', function () {
        $("#IdProductoFormaFarmaceuticaH").val($(this).children("td").eq(0).html());
        $("#ProductoFormaFarmaceutica").val($(this).children("td").eq(1).html());
        $("#ModalBuscarFormaFarmaceutica").modal("hide");
    } );
    });


    //Medicion
    $("#buscarMedicion").click(function(event) {
       $("#ModalBuscarMedicion").modal("show");
       ListarMedicion();
        $('#tableProductoMedicion tbody').on('click', 'tr', function () {
        $("#IdProductoMedicionH").val($(this).children("td").eq(0).html());
        $("#ProductoMedicion").val($(this).children("td").eq(1).html());
        $("#ModalBuscarMedicion").modal("hide");
    } );
    });

    //Categoria
    $("#buscarCategoria").click(function(event) {
       $("#ModalBuscarCategoria").modal("show");
       ListarCategoria();
        $('#tableProductoCategoria tbody').on('click', 'tr', function () {
        $("#IdProductoCategoriaH").val($(this).children("td").eq(0).html());
        $("#ProductoCategoria").val($(this).children("td").eq(1).html());
        $("#ModalBuscarCategoria").modal("hide");
    } );
    });

    //Bloque
    $("#buscarBloque").click(function(event) {
       $("#ModalBuscarBloque").modal("show");
       ListarBloque();
        $('#tableProductoBloque tbody').on('click', 'tr', function () {
        $("#ProductoBloque").val($(this).children("td").eq(1).html());
        $("#ModalBuscarBloque").modal("hide");
    } );
    });

    $("#btnAddCompuestos").click(function(){
        $("#modalCompuestosAdd").modal("show");
        ListarCompuesto();
    });

    $("#btnNuevoCompuesto").click(function(){
      $("#nuevoCompuesto").modal("show");
      $("#CompuestoProducto").val("");
    });

    $("#btnGuardarCompuesto").click(function(){
            var xhr = $.ajax({
        url: "Gen_ProductoCompuestoGuardar.php",
        type: "get",
        //data: {"productocompuesto":$("#CompuestoProducto").val()},
        data: {"idproductocompuesto":"", "productocompuesto":$("#CompuestoProducto").val() ,data : JSON.stringify([])},
        dataType: "html",
        success: function(respuesta){
          ListarCompuesto();
          $("#nuevoCompuesto").modal("hide");
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
          alert("Status" + textStatus); alert("Error" + errorThrown);
        }
      });
            console.log(xhr);
    });

    $("#btnNuevaMarca").click(function(){
      $("#nuevoMarca").modal("show");
      $("#NuevaMarcaProducto").val("");
    });

    $("#btnNuevaMedicion").click(function(){
      $("#nuevoMedicion").modal("show");
      $("#NuevaMedicionProducto").val("");
    });

    $("#btnNuevaForma").click(function(){
      $("#nuevoForma").modal("show");
      $("#NuevaFormaProducto").val("");
    });

    $("#btnNuevaCategoria").click(function(){
      $("#nuevoCategoria").modal("show");
      $("#NuevaCategoriaProducto").val("");
    });

      $("#btnNuevaBloque").click(function(){
      $("#nuevoBloque").modal("show");
      $("#idproductobloque").val("");
      $("#porcMin").val("");
      $("#porcMax").val("");
      $("#NuevaBloqueProducto").val("");
    });

    $("#btnGuardarMarca").click(function(){
      var xhr = $.ajax({
          url: "../controllers/Gen_ProductoMarcaGuardar.php",
          type: "post",
          //data: {"productocompuesto":$("#CompuestoProducto").val()},
          data: {"idproductomarca": "","productomarca": $("#NuevaMarcaProducto").val()},
          dataType: "html",
          success: function(respuesta){
            ListarMarca();
            $("#nuevoMarca").modal("hide");
          },
          error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Status" + textStatus); alert("Error" + errorThrown);
          }
          });
      console.log(xhr);
    });

    $("#btnGuardarForma").click(function(){
      var xhr = $.ajax({
          url: "../controllers/Gen_ProductoFormaFarmaceuticaGuardar.php",
          type: "get",
          //data: {"productocompuesto":$("#CompuestoProducto").val()},
          data: {"idproductoformafarmaceutica": "","productoformafarmaceutica": $("#NuevaFormaProducto").val()},
          dataType: "html",
          success: function(respuesta){
            ListarFormaFarmaceutica();
            $("#nuevoForma").modal("hide");
          },
          error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Status" + textStatus); alert("Error" + errorThrown);
          }
          });
      console.log(xhr);
    });

    $("#btnGuardarMedicion").click(function(){
      var xhr = $.ajax({
          url: "../controllers/Gen_ProductoMedicionGuardar.php",
          type: "get",
          //data: {"productocompuesto":$("#CompuestoProducto").val()},
          data: {"idproductomedicion": "","productomedicion": $("#NuevaMedicionProducto").val()},
          dataType: "html",
          success: function(respuesta){
            //ListarMarca();
            ListarMedicion();
            $("#nuevoMedicion").modal("hide");
          },
          error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Status" + textStatus); alert("Error" + errorThrown);
          }
          });
      console.log(xhr);
    });

    $("#btnGuardarCategoria").click(function(){
      var xhr = $.ajax({
          url: "../controllers/Gen_ProductoCategoriaGuardar.php",
          type: "get",
          //data: {"productocompuesto":$("#CompuestoProducto").val()},
          //data: {"idproductomarca": $("#NuevaMarcaProducto").val()},
          data: {"idproductocategoria": "","productocategoria": $("#NuevaCategoriaProducto").val()},
          dataType: "html",
          success: function(respuesta){
            //ListarMarca();
            ListarCategoria();
            $("#nuevoCategoria").modal("hide");
          },
          error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Status" + textStatus); alert("Error" + errorThrown);
          }
          });
      console.log(xhr);
    });

    $("#btnGuardarBloque").click(function(){
      var xhr = $.ajax({
          url: "../controllers/gen_productobloqueguardar.php",
          type: "get",
          data: {"idproductobloque": $("#idproductobloque").val(),"productobloque": $("#NuevaBloqueProducto").val(), "porcentajeMin": $("#porcMin").val(), "procentajeMax": $("#porcMax").val()},
          dataType: "html",
          success: function(respuesta){
            //ListarMarca();
            if (respuesta=="a") {
              ListarBloque();
              $("#nuevoBloque").modal("hide");
            } else if (respuesta=="m"){
              ListarBloque();
              $("#ModalBuscarBloque").modal("show");
              $("#nuevoBloque").modal("hide");
            }

          },
          error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Status" + textStatus); alert("Error" + errorThrown);
          }
          });
      console.log(xhr);
    });

    $("#btnExcel").click(function(e){
            var xhr = $.ajax({
          url: "reporteExcel.php",
          type: "get",
          data: "",
          //dataType: "html",
          success: function(respuesta){
            window.location = "reporteExcel.php";
          },
          error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Status" + textStatus); alert("Error" + errorThrown);
          }
          });
      console.log(xhr);
    });

    $("#idCheckBox").on("change", function(e){
      if($(this).is(":checked")){
        $("#btnAddDetProducto").prop("disabled", true);
        $("#tableProductoDet tbody").empty();
        $("#txtStockMinimo").prop("readonly", false);
      }else{
        $("#btnAddDetProducto").prop("disabled", false);
        $("#txtStockMinimo").prop("readonly", true);
        $("#tableProductoDet tbody").remove();
        $("#tableProductoDet thead").after("<tbody></tbody>");
        $("#txtStockMinimo").val("0");
      }
    });

    $("#btnAddDetProducto").click(function(e){
      ListarProductosDet();
      $("#txtProductoCantidadDet").val("0.00");
      $("#modalProductoDet").modal("show");
    });

    $("#btnGuardarProductoDet").on({
      click : function(e){
        agregarProductoDet($("#txtProductoCantidadDet").val());
        $("#modalCantidadDet").modal("hide");
        $("#modalProductoDet").modal("hide");
      },
      keydown : function(e){
        console.log(e.which);
        if(e.which == "13"){
          agregarProductoDet($("#txtProductoCantidadDet").val());
          $("#modalCantidadDet").modal("hide");
          $("#modalProductoDet").modal("hide");
        }
      }
    });

    $("#txtProductoCantidadDet").keydown(function(e){
      if (e.which == 13) {
          agregarProductoDet($("#txtProductoCantidadDet").val());
          $("#modalCantidadDet").modal("hide");
          $("#modalProductoDet").modal("hide");
      }
    });

} );

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
            { mData: 'Producto' }
            ]
        });

        $("#tableProductoDetListar tbody").on("click", "tr", function(e) {

        $("#tempIdProductoDet").val($(this).children("td").eq(0).html());
        $("#tempProductoDet").val($(this).children("td").eq(1).html());
        $("#modalCantidadDet").modal("show");
        $("#txtProductoDet").val($("#tempProductoDet").val());
        });
}

function agregarProductoDet(productoCantidadDet){
          var Encontrado = 0;

              $("#tableProductoDet tbody").each(function(index, el) {
                        $("#tableProductoDet tbody tr").each(function(index, el) {
                        var productoDet = $(this).find('.nombreProductoDet').html();
                  if (productoDet == $("#tempProductoDet").val()) {
                 Encontrado = 1;
                  }
              });

            if (Encontrado ==0) {
            var fila = "<tr><td>"+ $("#tempIdProductoDet").val() +"</td><td class='nombreProductoDet'>"+ $("#tempProductoDet").val() + "</td><td>"+productoCantidadDet+"</td><td class='text-center'><a id='btnEliminarProducto' class='btn' onclick='EliminarProductoDet("+$("#tempIdProductoDet").val()+")'><i class='fa fa-trash'></i></a></td></tr>";
            $("#tableProductoDet tbody").append(fila);
            $.notify({
              icon: 'fa fa-plus',
              message: "<strong>"+$("#tempProductoDet").val()+"</strong> Ha sido agregado"
            });
            }
      });
}

function ListarCompuesto(){
  $("#tableCompuestosAdd").DataTable().destroy();
      var table4 = $("#tableCompuestosAdd").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingCompuesto.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProductoCompuesto' } ,
            { mData: 'ProductoCompuesto' }
            ]
        });
      $("#tableCompuestosAdd tbody").on("click", "tr", function(e) {

        $("#tempIdCompuesto").val($(this).children("td").eq(0).html());
        $("#tempCompuesto").val($(this).children("td").eq(1).html());

          var Encontrado = 0;

              $("#tableCompuestos tbody").each(function(index, el) {
                        $("#tableCompuestos tbody tr").each(function(index, el) {
                        var sintoma = $(this).find('.nombreCompuestoAdd').html();
                  if (sintoma == $("#tempCompuesto").val()) {
                 Encontrado = 1;
                  }
              });

            //console.log(Encontrado);

            if (Encontrado ==0) {
            var fila = "<tr><td>"+ $("#tempIdCompuesto").val() +"</td><td class='nombreCompuestoAdd'>"+ $("#tempCompuesto").val() + "</td><td class='text-center'><a id='btnEliminarProducto' class='btn' onclick='EliminarCompuesto("+$("#tempIdCompuesto").val()+")'><i class='fa fa-trash'></i></a></td></tr>";
            $("#tableCompuestos tbody").append(fila);
            $.notify({
              icon: 'fa fa-plus',
              message: "<strong>"+$("#tempCompuesto").val()+"</strong> Ha sido agregado"
            });

           /* $("#success-alert").children("strong").html($("#tempCompuesto").val()+" -- ha sido agregado");
            $("#success-alert").fadeIn("slow");
            $("#success-alert").fadeOut(1000);*/
            //$("#success-alert").children("strong").destroy();
            }
      });
      });
}

/*function ListarProductoDet(){
        $("#tableProducto tbody").on("tr", "click", function(e) {

        $("#IdProducto").val($(this).children("td").eq(0).html());
        $("#ProductoMarca").val($(this).children("td").eq(1).html());
        $("#ProductoFormaFarmaceutica").val($(this).children("td").eq(2).html());
        $("#ProductoMedicion").val($(this).children("td").eq(3).html());
        $("#ProductoCategoria").val($(this).children("td").eq(4).html());
        $("#Producto").val($(this).children("td").eq(5).html());
        $("#ProductoDesc").val($(this).children("td").eq(6).html());
        $("#ProductoDescCorto").val($(this).children("td").eq(7).html());
        $("#ProductoCodigoBarra").val($(this).children("td").eq(8).html());
        $("#ProductoCodigo").val($(this).children("td").eq(9).html());
        $("#ProductoDosis").val($(this).children("td").eq(10).html());
        $("#ProductoPrecioContado").val($(this).children("td").eq(11).html());
        $("#ProductoPrecioXMayor").val($(this).children("td").eq(12).html());
        $("#ProductoStockXMayor").val($(this).children("td").eq(13).html());

        $("#tableProductoDet tbody").empty();
        var xhr = $.ajax({
        url: "../controllers/server_processingProductoDet.php",
        type: "get",
        data: {"Producto": $(this).children("td").eq(0).html()},
        dataType: "html",
        //async: false,
        success: function(respuesta){
          var response = JSON.parse(respuesta);
          $.each(response, function(data, value){
            var fila = "<tr><td>"+ value.IdProducto +"</td><td class='nombreCompuestoAdd'>"+ value.ProductoDet + "</td><td>"+value.Cantidad+"</td><td class='text-center'><a id='btnEliminarProducto' class='btn' onclick='EliminarProductoDet("+value.IdProducto+")'><i class='fa fa-trash'></i></a></td></tr>";
            $("#tableProductoDet tbody").append(fila);
          });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
      });
      console.log(xhr);
        $("#nuevo").modal("show");
        event.stopPropagation();
      });
}*/

function TablaCompuesto(){
        $("#tableProducto tbody").delegate("tr", "click", function(e) {

        $("#IdProducto").val($(this).children("td").eq(0).html());
        $("#ProductoMarca").val($(this).children("td").eq(1).text());
        $("#ProductoFormaFarmaceutica").val($(this).children("td").eq(2).html());
        $("#ProductoMedicion").val($(this).children("td").eq(3).html());
        $("#ProductoCategoria").val($(this).children("td").eq(4).html());
        $("#Producto").val($(this).children("td").eq(5).html());
        $("#ProductoDesc").val($(this).children("td").eq(6).html());
        $("#ProductoDescCorto").val($(this).children("td").eq(7).html());
        $("#ProductoCodigoBarra").val($(this).children("td").eq(8).html());
        $("#ProductoCodigo").val($(this).children("td").eq(9).html());
        $("#ProductoDosis").val($(this).children("td").eq(10).html());
        $("#ProductoPrecioContado").val($(this).children("td").eq(11).html());
        $("#ProductoPrecioXMayor").val($(this).children("td").eq(12).html());
        $("#ProductoStockXMayor").val($(this).children("td").eq(13).html());
        $("#txtStockMinimo").val($(this).children("td").eq(14).html());
        if($(this).children("td").eq(15).html()=="1"){
          $("#idCheckBox").prop("checked", true);
        }else{
          $("#idCheckBox").prop("checked", false);
        }
        $("#ProductoPrecioCosto").val($(this).children("td").eq(18).html());
        $("#PorcentajeUtilidad").val($(this).children("td").eq(20).html());
        if( $("#ProductoPrecioCosto").val()==="" ||  $("#PorcentajeUtilidad").val()===""){
          $("#ProductoPrecioCosto").val("0");
          $("#PorcentajeUtilidad").val("0");
        }
        $("#ProductoBloque").val($(this).children("td").eq(17).html());
        if($(this).children("td").eq(19).html()=="1"){
          $("#idCheckBoxVentaEstrategica").prop("checked", true);
        }else{
          $("#idCheckBoxVentaEstrategica").prop("checked", false);
        }

        $("#tableCompuestos tbody").empty();
        var xhr = $.ajax({
        url: "../controllers/server_processingProductoCompuesto.php",
        type: "get",
        data: {"Producto": $(this).children("td").eq(0).html() ,"Compuesto": "", "CompuestoNombre": ""},
        dataType: "html",
        //async: false,
        success: function(respuesta){
          var response = JSON.parse(respuesta);
          $.each(response, function(data, value){
            var fila = "<tr><td>"+ value.IdProductoCompuesto +"</td><td class='nombreCompuestoAdd'>"+ value.ProductoCompuesto + "</td><td class='text-center'><a id='btnEliminarProducto' class='btn' onclick='EliminarCompuesto("+value.IdProductoCompuesto+")'><i class='fa fa-trash'></i></a></td></tr>";
            $("#tableCompuestos tbody").append(fila);
          });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
      });
      console.log(xhr);
        $("#tableProductoDet tbody").empty();
        var xhr = $.ajax({
        url: "../controllers/server_processingProductoDet.php",
        type: "get",
        data: {"Producto": $(this).children("td").eq(0).html()},
        dataType: "html",
        //async: false,
        success: function(respuesta){
          var response = JSON.parse(respuesta);
          $.each(response, function(data, value){
            var fila = "<tr><td>"+ value.IdProducto +"</td><td class='nombreProductoAdd'>"+ value.Producto + "</td><td>"+value.Cantidad+"</td><td class='text-center'><a id='btnEliminarProducto' class='btn' onclick='EliminarProductoDet("+value.IdProducto+")'><i class='fa fa-trash'></i></a></td></tr>";
            $("#tableProductoDet tbody").append(fila);
          });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
          }
      });
      console.log(xhr);
        $("#nuevo").modal("show");
      });
}

function ListarProductos(){
  $("#tableProducto").DataTable().destroy();
          var table4 = $("#tableProducto").DataTable({
            "serverSide": true,
            "bProcessing": true,
            //"responsive" : true,
            "sAjaxSource": "../controllers/server_processingProducto.php?serverSide=1",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 10,
            //"bAutoWidth": false,
            //"autoWidth" : false,
            //"bFilter": false,
            "aoColumns": [
                { mData: 'IdProducto' } ,
                { mData: 'ProductoMarca' },
                { mData: 'ProductoFormaFarmaceutica' },
                { mData: 'ProductoMedicion' },
                { mData: 'ProductoCategoria' },
                { mData: 'Producto' },
                { mData: 'ProductoDesc' },
                { mData: 'ProductoDescCorto' },
                { mData: 'CodigoBarra' },
                { mData: 'Codigo' },
                { mData: 'Dosis' },
                { mData: 'PrecioContado' },
                { mData: 'PrecioPorMayor'},
                { mData: 'StockPorMayor'},
                { mData: 'StockMinimo'},
                { mData: 'ControlaStock'},
                { mData: 'Anulado'},
                { mData: 'Bloque'},
                { mData: 'PrecioCosto'},
                { mData: 'VentaEstrategica'},
                { mData: 'PorcentajeUtilidad'}
            ],
            "drawCallback": function(settings) {
                var api = this.api();
                $('ul.pagination').append($("<input />", {
                    type: "number",
                    class: "form-control",
                    placeholder: "Página",
                    width: 100,
                    min: '1',
                    autofocus: true
                })
                .on('keypress', function(e) {
                    if(e.which == 13) {
                        var page = $(this).val()
                        if (page > 0) {
                            api.page(page - 1).draw( 'page' );
                        }
                    }
                }))
            }
        });
}

function ListarMarca(){
          $("#tableProductoMarca").DataTable().destroy();
          var table4 = $("#tableProductoMarca").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingMarca.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProductoMarca' } ,
            { mData: 'ProductoMarca' }
            ]
        });
}

function ListarFormaFarmaceutica(){
          $("#tableProductoFormaFarmaceutica").DataTable().destroy();
          var table4 = $("#tableProductoFormaFarmaceutica").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingFormaFarmaceutica.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProductoFormaFarmaceutica' } ,
            { mData: 'ProductoFormaFarmaceutica' }
            ]
        });
}

function ListarCategoria(){
          $("#tableProductoCategoria").DataTable().destroy();
          var table4 = $("#tableProductoCategoria").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingCategoria.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProductoCategoria' } ,
            { mData: 'ProductoCategoria' }
            ]
        });
}

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

function EditarBloque(IdBloque) {
  console.log("se hizo click" + IdBloque);
  $("#idproductobloque").val(IdBloque);
  $("#tableProductoBloque tbody tr").each(function(e){
    if (IdBloque == $(this).children("td").eq(0).text()) {
      //console.log($(this).children("td").eq(0).text());
      $("#NuevaBloqueProducto").val($(this).children("td").eq(1).text());
      $("#porcMin").val($(this).children("td").eq(2).text());
      $("#porcMax").val($(this).children("td").eq(3).text());
    }
  });
  $("#nuevoBloque").modal("show");
}

function ListarMedicion(){
          $("#tableProductoMedicion").DataTable().destroy();
          var table4 = $("#tableProductoMedicion").DataTable({
            "bProcessing": true,
            "sAjaxSource": "../controllers/server_processingMedicion.php",
            "bPaginate":true,
            "sPaginationType":"full_numbers",
            "iDisplayLength": 5,
            "aoColumns": [
            { mData: 'IdProductoMedicion' } ,
            { mData: 'ProductoMedicion' }
            ]
        });
}

function EliminarCompuesto(idcompuesto){
  $("#tableCompuestos tbody").each(function(){
    $("#tableCompuestos tbody tr").each(function(){
      if(idcompuesto == $(this).children("td").eq(0).html()){
        $(this).remove();
        $.notify({
          icon: 'fa fa-plus',
          message: "<strong>"+$(this).children("td").eq(1).html()+"</strong> Ha sido eliminado"
        },{
          type : 'warning'
        });
      }
    });
  });
}

function EliminarProductoDet(idProductoDet){
  $("#tableProductoDet tbody").each(function(){
    $("#tableProductoDet tbody tr").each(function(){
      if(idProductoDet == $(this).children("td").eq(0).html()){
        $(this).remove();
        $.notify({
          icon: 'fa fa-plus',
          message: "<strong>"+$(this).children("td").eq(1).html()+"</strong> Ha sido eliminado"
        },{
          type : 'warning'
        });
      }
    });
  });
}

</script>

 <body>
<?php include("header.php"); ?>

<div class="bt-panel">

 <button id="btn-nuevo" class="btn btn-danger fab" data-toggle="modal" data-target="#nuevo"><i class="fa fa-plus"></i></button>
 <button id="btnExcel" class="btn btn-success fab2"><i class="fa fa-file-excel-o"></i></button>
 <div class="sTableProducto" class="table-responsive" style="overflow-x:auto">
 	<table id="tableProducto" class="table table-striped table-bordered" style="">
 		<thead>
      <th class="">#</th>
 			<th>Laboratorio</th>
      <th>Forma Farmaceutica</th>
      <th>Medicion</th>
      <th>Categoria</th>
      <th>Producto</th>
      <th>Descripcion del Producto</th>
      <th>Descripcion corta</th>
      <th>Codigo de Barra</th>
      <th>Codigo</th>
      <th>Dosis</th>
      <th>Precio Contado</th>
      <th>Precio Por Mayor</th>
      <th>Stock Por Mayor</th>
      <th>Stock Minimo</th>
      <th>Control Stock</th>
      <th>Estado</th>
      <th>Bloque</th>
      <th>Precio Costo</th>
      <th>Venta Estrategica</th>
      <th>Porcentaje Utilidad</th>
 		</thead>

 	</table>
 </div>

</div>
<?php include("footer.php"); ?>
 </body>

 <div class="modal fade" id="nuevo" role="dialog">
 	<div class="modal-dialog">
 		<div class="modal-content">
 			<div class="modal-header">
 				<button type="button" class="close" data-dismiss="modal">&times;</button>
          		<h4 class="modal-title">Añadir Compuesto de Producto</h4>
 			</div>
 			<div class="modal-body">
 				<form id="modal-form">
               <input type="hidden" class="form-control" id="IdProducto"  name="idproducto">
  					<div class="form-group">
   						 <label for="Marca" >Laboratorio</label>
               <div class="form-inline">
                 <input type="hidden" id="IdProductoMarcaH" name="idproductomarca">
                 <input type="text" class="form-control" id="ProductoMarca" readonly  name="productomarca" placeholder="Laboratorio">
                 <button type="button" id="buscarMarca" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
               </div>
  					</div>
            <div class="form-group">
               <label for="ProductoFormaFarmaceutica">Forma  Farmaceutica</label>
               <div class="form-inline">
                <input type="hidden" id="IdProductoFormaFarmaceuticaH" name="idproductoformafarmaceutica">
               <input type="text" class="form-control" id="ProductoFormaFarmaceutica" readonly  name="productoformafarmaceutica" placeholder="Forma Farmaceutica del Producto">
              <button type="button" id="buscarFormaFarmaceutica" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
              </div>
            </div>
            <div class="form-group">
               <label for="ProductoMedicion">Medicion</label>
               <div class="form-inline">
                <input type="hidden" id="IdProductoMedicionH" name="idproductomedicion">
               <input type="text" class="form-control" id="ProductoMedicion" readonly  name="productomedicion" placeholder="Medicion del Producto">
              <button type="button" id="buscarMedicion" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
              </div>
            </div>
            <div class="form-group">
               <label for="ProductoCategoria">Categoria</label>
               <div class="form-inline">
                <input type="hidden" id="IdProductoCategoriaH" name="idproductocategoria">
               <input type="text" class="form-control" id="ProductoCategoria" readonly  name="productocategoria" placeholder="Categoria del Producto">
              <button type="button" id="buscarCategoria" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
              </div>
            </div>
            <div class="form-group">
               <label for="ProductoBloque">Bloque</label>
               <div class="form-inline">
               <input type="text" class="form-control" id="ProductoBloque" readonly  name="productobloque" placeholder="Bloque del Producto">
              <button type="button" id="buscarBloque" class="btn btn-danger"><i class="fa fa-search-plus"></i></button>
              </div>
            </div>
            <div class="form-group">
               <label for="Producto">Producto</label>
               <input type="text" class="form-control" id="Producto"  name="producto" placeholder="Producto">
            </div>
            <div class="form-group">
               <label for="ProductoDesc">Descripcion del Producto</label>
               <!-- <input type="text" class="form-control" id="ProductoDesc"  name="productodesc" placeholder="Compuesto del Producto"> -->
               <textarea class="form-control" id="ProductoDesc" name="productodesc" rows="4" cols="40"></textarea>
            </div>
            <div class="form-group">
               <label for="ProductoDescCorto">Descripcion Corta</label>
               <input type="text" class="form-control" id="ProductoDescCorto" maxlength="30"  name="productodescorto" placeholder="Compuesto del Producto">
            </div>
            <div class="form-group">
               <label for="ProductoCodigoBarra">Codigo de Barra</label>
               <input type="text" class="form-control" id="ProductoCodigoBarra" name="productocodigodebarra" placeholder="Compuesto del Producto">
            </div>
            <div class="form-group">
               <label for="ProductoCodigo">Codigo</label>
               <input type="text" class="form-control" id="ProductoCodigo"  name="productocodigo" placeholder="Compuesto del Producto">
            </div>
            <div class="form-group">
               <label for="ProductoDosis">Dosis</label>
               <input type="text" class="form-control" id="ProductoDosis"  name="productodosis"  placeholder="Compuesto del Producto">
            </div>
            <div class="form-group">
               <label for="ProductoPrecioCosto">Precio Costo</label>
               <input type="text" class="form-control" id="ProductoPrecioCosto" value="0"  name="productoPrecioCosto"  placeholder="Compuesto del Producto">
            </div>
            <div class="checkbox">
              <label><input type="checkbox" name="productoVentaEstrategica"  id="idCheckBoxVentaEstrategica">Venta Estrategica</label>
            </div>
            <div class="form-group">
               <label for="PorcentajeUtilidad">Porcentaje Utilidad</label>
               <input type="text" class="form-control" id="PorcentajeUtilidad" value="0"  name="productoPorcentajeUtilidad"  placeholder="Compuesto del Producto">
            </div>
             <div class="form-group">
               <label for="ProductoPrecioContado">Precio Venta</label>
               <input type="number" step="0.01" class="form-control" id="ProductoPrecioContado"   name="productopreciocontado" placeholder="Compuesto del Producto">
            </div>
            <div class="form-group">
               <label for="ProductoPrecioPorMayor">Precio X Mayor</label>
               <input type="number" step="0.01" class="form-control" id="ProductoPrecioXMayor"  name="productopreciopormayor" placeholder="Compuesto del Producto">
            </div>
            <div class="form-group">
               <label for="ProductoStockPorMayor">Stock X Mayor</label>
               <input type="number" step="0.01" class="form-control" id="ProductoStockXMayor"  name="productostockpormayor" placeholder="Compuesto del Producto">
            </div>

            <div class="panel panel-success">
              <div class="panel-heading" style="height:50px;">Compuestos
                <button type="button" id="btnAddCompuestos" class="btn btn-success pull-right"><i class="fa fa-plus"></i></button>
              </div>
              <input type="hidden" id="tempIdCompuesto">
              <input type="hidden" id="tempCompuesto">
              <table id="tableCompuestos" class="table table-striped table-bordered">
                <thead>
                  <th>#</th>
                  <th>Compuestos</th>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div class="checkbox">
              <label><input type="checkbox" name="productoControlaStock"  id="idCheckBox">Controla Stock</label>
            </div>
            <div class="form-group">
              <label>Stock Minimo</label>
              <input type="number" name="productoStockMinimo" class="form-control" id="txtStockMinimo" placeholder="Stock Minimo">
            </div>
            <div class="panel panel-success">
              <div class="panel-heading" style="height:50px;">Detalle Producto
                <button type="button" id="btnAddDetProducto" class="btn btn-success pull-right"><i class="fa fa-search-plus"></i></button>
              </div>
              <input type="hidden" id="tempIdProductoDet">
              <input type="hidden" id="tempProductoDet">
              <table id="tableProductoDet" class="table table-striped table-bordered">
                <thead>
                  <th>#</th>
                  <th>Producto</th>
                  <th>Cantidad</th>
                </thead>
                <tbody></tbody>
              </table>
            </div>
 			</div>
      <input type="hidden" name="usuario" value="Jeam">
 			<div class="modal-footer">
 				<button type="submit" id="btnGuardarProducto" class="btn btn-success">Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
 				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
 			</form>
 			</div>
 		</div>
 	</div>
 </div>
<!-- MARCA -->
  <div class="modal fade" id="ModalBuscarMarca" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Laboratorio de Producto</h4>
      </div>
      <div class="modal-body">
        <div class="sTableProductoMarca">
          <table id="tableProductoMarca" class="table table-striped table-bordered">
            <thead>
             <th class="">ID</th>
             <th>Laboratorio del Producto</th>
            </thead>
        </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btnNuevaMarca" name="button">Nuevo <i class="fa fa-plus"></i></button>
      </div>
    </div>
  </div>
 </div>

 <!-- forma farmaceutica -->
  <div class="modal fade" id="ModalBuscarFormaFarmaceutica" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Forma Farmaceutica de Producto</h4>
      </div>
      <div class="modal-body">
        <div class="sTableProductoFormaFarmaceutica">
          <table id="tableProductoFormaFarmaceutica" class="table table-striped table-bordered">
            <thead>
             <th class="">ID</th>
             <th>Forma Farmaceutica del Producto</th>
            </thead>
        </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btnNuevaForma" name="button">Nuevo <i class="fa fa-plus"></i></button>
      </div>
    </div>
  </div>
 </div>

 <!-- medicion -->
  <div class="modal fade" id="ModalBuscarMedicion" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Medicion de Producto</h4>
      </div>
      <div class="modal-body">
        <div class="sTableProductoMedicion">
          <table id="tableProductoMedicion" class="table table-striped table-bordered">
            <thead>
             <th class="">ID</th>
             <th>Medicion del Producto</th>
            </thead>
        </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btnNuevaMedicion" name="button">Nuevo <i class="fa fa-plus"></i></button>
      </div>
    </div>
  </div>
 </div>

 <!-- categoria -->
  <div class="modal fade" id="ModalBuscarCategoria" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Categoria de Producto</h4>
      </div>
      <div class="modal-body">
        <div class="sTableProductoCategoria">
          <table id="tableProductoCategoria" class="table table-striped table-bordered">
            <thead>
             <th class="">#</th>
             <th>Categoria del Producto</th>
            </thead>
        </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btnNuevaCategoria" name="button">Nuevo <i class="fa fa-plus"></i></button>
      </div>
    </div>
  </div>
 </div>

  <!-- categoria -->
  <div class="modal fade" id="ModalBuscarBloque" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Bloque de Producto</h4>
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

 <div class="modal fade" id="modalCompuestosAdd" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Compuestos </h4>
      </div>
      <div class="modal-body">
        <table id="tableCompuestosAdd" class="table table-striped table-bordered">
          <thead>
            <th>#</th>
            <th>Compuestos</th>
          </thead>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnNuevoCompuesto" class="btn btn-success">Nuevo <i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar <i class="fa fa-close"></i></button>
      </div>
    </div>
  </div>
</div>


 <div class="modal fade" id="nuevoCompuesto" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Compuesto de Producto</h4>
      </div>
      <div class="modal-body">
               <input type="hidden" class="form-control" id="IdProducto"  name="idproductocompuesto">
            <div class="form-group">
               <label for="CompuestoProducto">Compuesto del Producto</label>
               <input type="text" class="form-control" id="CompuestoProducto"  name="productocompuesto" placeholder="Compuesto del Producto">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnGuardarCompuesto" class="btn btn-success">Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
 </div>

 <!-- nueva marca -->

 <div class="modal fade" id="nuevoMarca" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Marca de Producto</h4>
      </div>
      <div class="modal-body">
               <input type="hidden" class="form-control" id="idproductomarca"  name="idproductomarca">
            <div class="form-group">
               <label for="NuevaMarcaProducto">Compuesto del Producto</label>
               <input type="text" class="form-control" id="NuevaMarcaProducto"  name="productomarca" placeholder="Marca del Producto">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnGuardarMarca" class="btn btn-success">Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
 </div>

 <!-- nueva forma -->

 <div class="modal fade" id="nuevoForma" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Forma Farmaceutica de Producto</h4>
      </div>
      <div class="modal-body">
               <input type="hidden" class="form-control" id="idproductoforma"  name="idproductoforma">
            <div class="form-group">
               <label for="NuevaFormaProducto">Compuesto del Producto</label>
               <input type="text" class="form-control" id="NuevaFormaProducto"  name="productoforma" placeholder="Forma Farmaceutica del Producto">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnGuardarForma" class="btn btn-success">Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
 </div>

 <!-- nueva categoria -->

 <div class="modal fade" id="nuevoCategoria" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Categoria de Producto</h4>
      </div>
      <div class="modal-body">
               <input type="hidden" class="form-control" id="idproductocategoria"  name="idproductocategoria">
            <div class="form-group">
               <label for="NuevaMarcaProducto">Compuesto del Producto</label>
               <input type="text" class="form-control" id="NuevaCategoriaProducto"  name="productocategoria" placeholder="Categoria del Producto">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnGuardarCategoria" class="btn btn-success">Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
 </div>

 <!-- nueva Bloque -->

 <div class="modal fade" id="nuevoBloque" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Nuevo Bloque de Producto</h4>
      </div>
      <div class="modal-body">
               <input type="hidden" class="form-control" id="idproductobloque"  name="idproductobloque">
            <div class="form-group">
               <label for="NuevaMarcaProducto">Bloque del Producto</label>
               <input type="text" class="form-control" id="NuevaBloqueProducto"  name="productocategoria" placeholder="Bloque del Producto">
            </div>
            <div class="form-group">
               <label for="porcMin">Porcentaje Minimo</label>
               <input type="number" class="form-control" id="porcMin"  name="productocategoria" placeholder="Bloque del Producto">
            </div>
            <div class="form-group">
               <label for="NuevaMarcaProducto">Porcentaje Maximo</label>
               <input type="number" class="form-control" id="porcMax"  name="productocategoria" placeholder="Bloque del Producto">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnGuardarBloque" class="btn btn-success">Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
 </div>

 <!-- nueva medicion -->

 <div class="modal fade" id="nuevoMedicion" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Añadir Medicion de Producto</h4>
      </div>
      <div class="modal-body">
               <input type="hidden" class="form-control" id="idproductomedicion"  name="idproductomedicion">
            <div class="form-group">
               <label for="NuevaMarcaProducto">Compuesto del Producto</label>
               <input type="text" class="form-control" id="NuevaMedicionProducto"  name="productomedicion" placeholder="Medicion del Producto">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnGuardarMedicion" class="btn btn-success">Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
 </div>

 <!-- nueva Producto Det -->

 <div class="modal fade" id="modalProductoDet" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Productos </h4>
      </div>
      <div class="modal-body">
        <table id="tableProductoDetListar" class="table table-striped table-bordered">
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

 <div class="modal fade" id="modalCantidadDet" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> Agregar cantidad </h4>
      </div>
      <div class="modal-body">
        <div class="container">
          <div class="input-group">
            <input type="text" id="txtProductoDet" readonly class="form-control">
            <div class="separator"></div>
            <label class="">Cantidad</label>
            <input type="number" required id="txtProductoCantidadDet" autofocus  class="form-control" value="0.00" placeholder="00.00">
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

 </html>
