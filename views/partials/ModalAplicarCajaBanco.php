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
           <input min="0" type="number" class="form-control" id="importeAplicar" placeholder="" onkeyup="asignarMaximoAplicado(this)" onmouseup="asignarMaximoAplicado(this)" >
          </div>
        </div>
      </div>
		</div>
		<div class="modal-footer">
      <button type="button" class="btn btn-success" onclick="asignarAplicadoDocVenta({
				idDocDet: $('.iddocventa').text(),
				importe: $('#importeAplicar').val(),
				fechaDoc: $('.fechadoc').text(),
				fechaCredito: $('.fechacredito').text(),
				correlativo: $('.correlativo').text(),
				saldo: $('.row-cajabanco td:last-child').last().text() || $('.saldo').text()
			})" data-dismiss="modal" id="btnAplicar">Aplicar</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
		</div>
	</div>
	</div>
</div>

<script>
    $(document).ready(function () {
        var idModal = '#<?php echo $args["id"] ?>'

				$(idModal).on('shown.bs.modal', function() {
					$('#importeAplicar').mouseup()
				})

        $(idModal).on('show.bs.modal', function (event) {
						//var button = $(event.relatedTarget)
            var button = $(window.buttonAplicar)
            var row = button.parents('tr').first()

						$('#btnAplicar').on('click', function (){
							button.attr('disabled', true),
							row.addClass('danger')
						})

						//rowClone = row.clone()
            //*rowClone.find('td').last().remove()
            $('.row-cajabanco').remove()

						var hash = button.data('hash')
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
						var url = '../controllers/server_processingCajaBancoDetDocAplicado.php'
						var data = { idDocVenta : iddocventa }

						if (hash) {
							url = '../controllers/server_processingCajaBancoDetDocAplicadoProveedor.php'
							iddocventa = hash
							data = { hash: hash }
						}

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
            modal.find('.saldo').text(total)
						modal.find('#importeAplicar').val(saldo)
						modal.find('#importeAplicar').attr('max', saldo)

            $.ajax({
    					url: url,
    					type: 'get',
    					data: data,
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

				$(idModal).on('hidden.bs.modal', function (e) {
				  $('#btnAplicar').off('click')
				})
    })
</script>

<style>
</style>
