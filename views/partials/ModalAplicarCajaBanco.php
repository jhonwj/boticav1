<?php
?>
<div class="modal fade" id="<?php echo $args['id'] ?>" role="dialog">
  <!-- <div class="modal-dialog" style="width:800px"> -->
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
            <h4 class="modal-title" id="">
                Aplicar Importe al ID: <strong></strong>
            </h4>
		</div>
		<div class="modal-body" style="overflow-x:auto;">
      <table class="table table-bordered table-striped table-responsive" id="tableModalAplicarCajaBanco">
        <thead>
          <th>ID</th>
          <th>Fecha</th>
          <th>FechaVen</th>
          <th>Correlativo</th>
          <th>Total</th>
          <th>Aplicado</th>
          <th>Saldo</th>
        </thead>
        <tbody>
          <tr>
            <td class="iddocventa"></td>
            <td class="fechadoc"></td>
            <td class="fechacredito"></td>
            <td class="correlativo"></td>
            <td class="total"></td>
            <td class="aplicado"></td>
            <td class="saldo"></td>
          </tr>
        </tbody>
      </table>



      <div class="row">
       <div class="col-lg-12">
         <div class="form-inline" style="margin-bottom:20px;">
           <label for="txtConcepto">Importe a Aplicar: </label>
           <input type="text" class="form-control" id="importeAplicar" placeholder="">
          </div>
        </div>
      </div>
		</div>
		<div class="modal-footer">
            <button type="button" class="btn btn-success" onclick="asignarAplicadoDocVenta($('.iddocventa').text(), $('#importeAplicar').val())" data-dismiss="modal">Guardar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
		</div>
	</div>
	</div>
</div>

<script>
    $(document).ready(function () {
        var idModal = '#<?php echo $args["id"] ?>'

        $(idModal).on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var row = button.parents('tr').first()
            row = row.clone()
            row.find('td').last().remove()

            $('.row-cajabanco').remove()

            var iddocventa = button.data('iddocventa')
            var fechadoc = button.data('fechadoc')
            var fechacredito = button.data('fechacredito')
            var correlativo = button.data('correlativo')
            var total = button.data('total')
            var aplicado = button.data('aplicado')
            var saldo = button.data('saldo')
            var modal = $(this)

            //modal.find('.modal-body table:first-child tbody').empty()
            //modal.find('.modal-body table tbody').append(row)

      			//modal.find('#btnEliminar').data('idrow', idrow)
      			modal.find('#btnEliminar').data('title', iddocventa)

            modal.find('.modal-title > strong').text(iddocventa)
            modal.find('.iddocventa').text(iddocventa)
            modal.find('.fechadoc').text(fechadoc)
            modal.find('.fechacredito').text(fechacredito)
            modal.find('.correlativo').text(correlativo)
            modal.find('.total').text(total)
            //modal.find('.aplicado').text(aplicado)
            modal.find('.aplicado').text('00.00')
            modal.find('.saldo').text(saldo)

            $.ajax({
    					url: '../controllers/server_processingCajaBancoDetDocAplicado.php',
    					type: 'get',
    					data: { idDocVenta : iddocventa },
    					dataType: 'json',
    					success: function(respuesta){
                var data = respuesta.aaData;

    						if (data) {
                  // hacer foreach
                  $.each(data, function(index, value) {

                    var tr = $('<tr class="row-cajabanco"></tr>')
                    tr.append('<td></td>')
                    tr.append('<td>' + value.FechaDoc + '</td>')
                    tr.append('<td colspan="2"></td>')
                    tr.append('<td>' + total + '</td>')
                    tr.append('<td>' + value.Importe + '</td>')
                    tr.append('<td>' + (parseFloat(total) - parseFloat(value.Importe)) + '</td>')

                    $('#tableModalAplicarCajaBanco').append(tr)
                    total -= parseFloat(value.Importe)
                  })
    						} else {

    						}
    					},
    					error: function(XMLHttpRequest, textStatus, errorThrown) {
    						alert("Status: " + textStatus);
    						alert("Error: " + errorThrown);
    					}
    				});

        })


    })
</script>
