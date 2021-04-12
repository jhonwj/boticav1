$(".modal").on("shown.bs.modal", function(e){
    $(this).find('[autofocus]').focus();
});

function getDataUri(url, callback) {
    var image = new Image();

    image.onload = function () {
        var canvas = document.createElement('canvas');
        canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
        canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

        canvas.getContext('2d').drawImage(this, 0, 0);

        // Get raw image data
        // callback(canvas.toDataURL('image/png').replace(/^data:image\/(png|jpg);base64,/, ''));

        // ... or get as Data URI
        callback(canvas.toDataURL('image/png'));
    };

    image.src = url;
}


function actualizarRegistro(obj) {
    var controller = obj.controller || 'server_processing.php'
    $.ajax({
        url: '../controllers/' + controller,
        type: 'post',
        data: { update : true, tabla : obj.tabla, campos: obj.campos, where: obj.where, mensaje: obj.mensaje },
        dataType: 'json',
        success: function(respuesta){
            if (respuesta.success) {
                //$("<?php echo $args['reload'] ?>").DataTable().ajax.reload();
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
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus);
            alert("Error: " + errorThrown);
        }
    });
}

function exportarProforma(obj) {
    type = obj.type || 'xlsx'
    table = obj.table.clone()
    table.find('input').after(function() {return $(this).val() }).remove()
    table = table.prop('outerHTML')

    /*$('<form/>', {
        action: "../controllers/server_exportReporte.php",
        method: "post",
        html: "<input name='type' value='"+type+"'>"
            +"<input name='table' value='"+table+"'>"
            +"<input name='filename' value='"+obj.filename+"'>",
        class: "s"
    })
    .appendTo('body')
    .submit()*/

    $.ajax({
        url: '../controllers/server_exportReporte.php',
        type: 'post',
        data: { type : type, table : table, filename: obj.filename },
        dataType: 'json',
        success: function(respuesta){
            if (respuesta.success) {
                var $a = $("<a>");
                $a.attr("href",respuesta.file);
                $("body").append($a);
                $a.attr("download", obj.filename + '.' + type);
                $a[0].click();
                $a.remove();

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
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus);
            alert("Error: " + errorThrown);
        }
    });
}

function renderHeaderPDF(doc, imgData) {
  var width = doc.internal.pageSize.width;    

  doc.addImage(imgData, 'JPEG', 0, 30, width, 60);
  /*doc.setFontSize(14);
  doc.setTextColor(100);
  //var text = doc.splitTextToSize('Boticas Delman', 580);
  doc.text('BOTICAS DELMAN', 237, 45);
  doc.setFontSize(11);
  doc.text('ES GARANTIA DE SALUD', 235, 60);

  doc.setFontSize(10);
  doc.text('Consultorios para la salud', 240, 75);
  doc.text('Jr. Arica N° 150 frente a la plaza menor de Aguaytia', 180, 85);*/

  return doc
}

function exportarCajaBanco() {
    var doc = new jsPDF('p', 'pt');
    var table = $("#tableCajaBanco");
    table = table.clone()

    table.find('thead tr:first-child').remove()
    table.find('thead tr th:first-child').remove()
    table.find('thead tr th:last-child').remove()

    table.find('tbody tr td:first-child').remove()

    table = doc.autoTableHtmlToJson(table[0]);
    console.log(table)
    getDataUri('../resources/images/header.jpg', function(dataUri) {
        var imgData = dataUri
        doc.setFontSize(13);
        doc.text(40, 140, 'Reporte Caja y Banco') 
        doc.text(40, 160, 'Fecha: ' + $('#txtFechaVen').val()) 
  
        doc.autoTable(table.columns, table.data, {
            margin: {top: 60},
            startY: 200,
            tableLineColor: 200,
        });
  
        doc = renderHeaderPDF(doc, imgData)

        doc.save("ReporteCajaBanco.pdf");
    });
}

function exportarCajaBancoClientePDF(fn) {
  var doc = new jsPDF('p', 'pt');
  var table = $("#tableDocAplicadosTmp");
  table = table.clone()
  table.find('td[colspan=4]').parent().prepend('<td></td><td></td><td></td>')

  var header = $("<table></table>");
  header.append('<tbody></tbody>')
  header.find('tbody').append('<tr><td></td><td></td><td></td><td></td></tr>')
  header.find('tbody').append('<tr><td>Cliente: </td><td>' + $('#txtCliente').val() + '</td><td>Fecha de operación: </td><td>' + $('#txtFechaVen2').val() + '</td></tr>')
  header.find('tbody').append('<tr><td>Concepto: </td><td>' + $('#txtConcepto').val() + '</td><td>Importe: </td><td>' + $('#txtImporte').val() + '</td></tr>')

  table = doc.autoTableHtmlToJson(table[0]);
  header = doc.autoTableHtmlToJson(header[0]);

  getDataUri('../resources/images/botica-header.jpg', function(dataUri) {
      var imgData = dataUri

      doc.autoTable(header.columns, header.data, {
          theme: 'plain',
          margin: {top: 100},
          headerStyles: {fillColor: false},
          bodyStyles: {fillColor: false}
      });

      doc.autoTable(table.columns, table.data, {
          margin: {top: 60},
          startY: 200,
          tableLineColor: 200,
      });

      doc = renderHeaderPDF(doc, imgData)

      doc.save("PagoCliente.pdf");
      fn()
  });
}

function exportarPDF(obj) {
    var doc = new jsPDF('p', 'pt');
    var table = document.getElementById("tableModalProforma");

    var header = $("#tableModalProformaHeader");
    header = header.clone()
    header.find('input').after(function() {return $(this).val() }).remove()

    table = doc.autoTableHtmlToJson(table);
    header = doc.autoTableHtmlToJson(header[0]);

    getDataUri('../resources/images/botica-header.jpg', function(dataUri) {
        var imgData = dataUri

        doc.autoTable(header.columns, header.data, {
            theme: 'plain',
            margin: {top: 100},
            headerStyles: {fillColor: false},
            bodyStyles: {fillColor: false}
        });

        doc.autoTable(table.columns, table.data, {
            margin: {top: 60},
            startY: 200,
            tableLineColor: 200,
        });

        doc = renderHeaderPDF(doc, imgData)

        doc.save("proforma.pdf");
    });

}

function exportarProductosVencidos() {
    
}

function exportarOrdenCompra(proveedor, total, productos, ordenCompra) {
    var doc = new jsPDF('p', 'pt');

    var table = $('<table><thead></thead><tbody></tbody></table>')
    table.find('thead').append('<tr><th>ID</th><th>Producto</th><th>Forma</th><th>Laboratorio</th><th>CANT</th><th>Precio</th><th>Tot.</th></tr>')
    $(productos).each(function(index, value) {
        table.find('tbody').append('<tr><td>'+(index+1)+'</td><td>'+value.Producto+'</td><td>'+value.Forma+'</td><td>'+value.Laboratorio+'</td><td>'+value.Cantidad+'</td><td>'+value.Precio+'</td><td>'+(value.Cantidad*value.Precio)+'</td></tr>')        
    })
    table.find('tbody').append('<tr><td></td><td></td><td></td><td></td><td></td><td>Sumatoria</td><td>'+total+'</td></tr>')
    table = doc.autoTableHtmlToJson(table[0]);
    
    getDataUri('../resources/images/botica-header.jpg', function(dataUri) {
        var imgData = dataUri

        /*doc.autoTable(header.columns, header.data, {
            theme: 'plain',
            margin: {top: 100},
            headerStyles: {fillColor: false},
            bodyStyles: {fillColor: false}
        });*/

        doc.setFontSize(14);
        doc.text(40, 160, 'Proveedor: ' + proveedor) 
        doc.text(40, 180, 'Orden de pedido: N° ' + ordenCompra['Numero'] + '-' + ordenCompra['Anio'] ) 

        doc.autoTable(table.columns, table.data, {
            margin: {top: 60},
            startY: 200,
            tableLineColor: 200,
        });

        doc = renderHeaderPDF(doc, imgData)

        doc.save("ordenCompra.pdf");
    });

}

function exportarTXT(table) {
  var txt = ''
  table.find('tbody tr').each(function(index, tr) {
    $(tr).find('td').each(function(index, td) {
      if ($(td).text()) {
        txt += $(td).text() + '|'
      }
    })
    txt += '\r\n'
  })
  var blob = new Blob([txt], {type: "text/plain;charset=utf-8"});
  saveAs(blob, table.attr('id') + '.txt');
}

function asignarMaximoAplicado(el){
  var max = parseFloat($(el).attr('max'))
  if ($(el).val() > max ) {
    alert('El importe no puede ser mayor al saldo pendiente')
    $(el).val(max)
  }

  if ((parseFloat($(el).val()) + sumatoriaSaldoAplicado()) > (parseFloat($('#txtImporte').val()) || 0)) {
    alert('La sumatoria de los importes a aplicar no puede ser mayor al importe general')
    $(el).val(parseFloat($('#txtImporte').val()) - sumatoriaSaldoAplicado())
  }
}

function sumatoriaSaldoAplicado () {
  var sumatoria = 0
  if (window.aplicadoDocVenta && window.aplicadoDocVenta.length) {
    window.aplicadoDocVenta.map(function(el){
      sumatoria += parseFloat(el.importe)
    })
  }
  return sumatoria
}

function sumatoriaSaldoDA () {
  var sumatoria = 0
  if (window.aplicadoDocVenta && window.aplicadoDocVenta.length) {
    window.aplicadoDocVenta.map(function(el){
      sumatoria += parseFloat(el.saldo)
    })
  }
  return sumatoria
}

function verificarImporte(e) {
  if (!$('#txtImporte').val()) {
    alert('Debe establecer el importe antes de Aplicar')
  } else {
    window.buttonAplicar = e.target
    $('#ModalAplicarCajaBanco').modal('show');
  }
}

function consultarDNIRUC(numero, type, callback) {
  var asd = $.ajax({
    url: "../controllers/server_consultarDNIRUC.php?type=" + type + "&numero=" + numero,
    type: 'GET',
    success: function(respuesta){
        if (respuesta.success) {
            $.notify({
                icon: 'fa fa-check',
                message: 'Se llenarán los datos....'
            }, {
                type: 'success'
            });
            if (type == 'DNI') {
              console.log(callback)
              callback({
                nombres: respuesta.result.Paterno + " " + respuesta.result.Materno + " " + respuesta.result.Nombre
              })
            }else if (type == 'RUC') {
              callback(respuesta.result)
            } else {
              return false
            }
        } else {
            $.notify({
                icon: 'fa fa-exclamation',
                message: respuesta.error
            }, {
                type: 'danger'
            });
        }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Status: " + textStatus);
        alert("Error: " + errorThrown);
    }
  })
}

/* PREFILTER AJAX JQUERY */
$.ajaxPrefilter(function(options, _, jqXHR) {
  console.log(options);
  if (String(options.type).toLowerCase() == 'post') {
    if (sessionStorage.getItem('Escritura') == '0' && sessionStorage.getItem('User') != 'admin') {
      $.notify({
          icon: 'fa fa-exclamation',
          message: 'Usted no tiene permisos de Escritura'
      }, {
          type: 'danger'
      });
      jqXHR.abort();
    }
  }
  console.log(options.type)
});



function exportTableToExcel(tableID, filename = ''){
  var downloadLink;
  var dataType = 'application/vnd.ms-excel';
  var tableSelect = document.getElementById(tableID);
  var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
  
  // Specify file name
  filename = filename?filename+'.xls':'excel_data.xls';
  
  // Create download link element
  downloadLink = document.createElement("a");
  
  document.body.appendChild(downloadLink);
  
  if(navigator.msSaveOrOpenBlob){
      var blob = new Blob(['\ufeff', tableHTML], {
          type: dataType
      });
      navigator.msSaveOrOpenBlob( blob, filename);
  }else{
      // Create a link to the file
      downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
  
      // Setting the file name
      downloadLink.download = filename;
      
      //triggering the function
      downloadLink.click();
  }
}