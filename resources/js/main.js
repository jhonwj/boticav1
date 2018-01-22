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

function exportarPDF(obj) {
    var doc = new jsPDF('p', 'pt');
    var table = document.getElementById("tableModalProforma");

    var header = $("#tableModalProformaHeader");
    header = header.clone()
    header.find('input').after(function() {return $(this).val() }).remove()

    table = doc.autoTableHtmlToJson(table);
    header = doc.autoTableHtmlToJson(header[0]);

    getDataUri('../resources/images/delman.jpg', function(dataUri) {
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

        doc.addImage(imgData, 'JPEG', 40, 30, 55, 60);
        doc.setFontSize(14);
        doc.setTextColor(100);
        //var text = doc.splitTextToSize('Boticas Delman', 580);
        doc.text('BOTICAS DELMAN', 237, 45);
        doc.setFontSize(11);
        doc.text('ES GARANTIA DE SALUD', 235, 60);

        doc.setFontSize(10);
        doc.text('Consultorios para la salud', 240, 75);
        doc.text('Jr. Arica N° 150 frente a la plaza menor de Aguaytia', 180, 85);



        doc.save("table.pdf");
    });

}
