<?php
?>
<div class="modal fade" id="<?php echo $args['id'] ?>" role="dialog">
  <!-- <div class="modal-dialog" style="width:800px"> -->
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
            <h4 class="modal-title" id="">
                Exportar Proforma
            </h4>
		</div>
		<div class="modal-body" style="overflow-x:auto;">
            <!-- Table -->
			<table class="table table-striped table-bordered"  id="tableModalProformaHeader">
				<tbody>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
                        <td>Fecha: </td>
                        <td class="fecha"></td>
                        <td>RUC:</td>
                        <td class="dniruc"><input class="form-control" type="text" /></td>
						<td style="text-align:center">
							PROFORMA
						</td>
                    </tr>
                    <tr>
                        <td>Cliente:</td>
                        <td class='cliente'><input class="form-control" type="text" /></td>
                        <td>Email:</td>
                        <td class="email"><input class="form-control" type="text" /></td>
						<td style="text-align:center">
							NRO: <input class="form-control" type="text" />
						</td>
                    </tr>
                    <tr>
                        <td>Direccion:</td>
                        <td class="direccion"><input class="form-control" type="text" /></td>
  					  	<td>Teléfono:</td>
                        <td class="telefono"><input class="form-control" type="text" /></td>
						<td></td>
                    </tr>
				</tbody>
			</table>

            <table id="tableModalProforma" class="table table-striped table-bordered">
              <thead>
                  <tr>
				  	  <th>#</th>
                      <th>Producto</th>
                      <th>Forma</th>
                      <th>Laboratorio</th>
                      <th>Vencimiento</th>
                      <th>Cantidad</th>
                      <th>Precio</th>
                      <th>Tot.</th>
                  </tr>
              </thead>
			  <tbody>

			  </tbody>
			  <tfoot>
				<tr>
				    <td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td><strong>SUBTOTAL</strong></td>
					<td class='subtotal'></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td><strong>IGV</strong></td>
					<td class='igv'></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td><strong>TOTAL</strong></td>
					<td class='total'></td>
				</tr>
			</tfoot>
            </table>
            <div class="modal-clone"></div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" onclick="exportarPDF()">
				<i class="fa fa-file-pdf-o fa-lg"></i> PDF
			</button>
            <!--<button
				type="button"
				class="btn btn-success"
				id="btnEliminar"
				onclick="exportarProforma({
					type: 'xlsx',
					filename: 'Proforma',
					table: $('#tableModalProforma')
				})">
				<i class="fa fa-file-excel-o fa-lg"></i>Exportar
			</button>-->
		</div>
	</div>
	</div>
</div>

<script>
    $(document).ready(function () {
        var idModal = '#<?php echo $args["id"] ?>'

        $(idModal).on('show.bs.modal', function (e) {
			$('.fecha').html($('#fecha').val())
			$('.cliente input').val($('#txtCliente').val())
			$('.direccion input').val($('#txtCliente').attr('data-direccion'))
			$('.dniruc input').val($('#txtCliente').attr('data-dniruc'))
			$('.email input').val($('#txtCliente').attr('data-email'))
			$('.telefono input').val($('#txtCliente').attr('data-telefono'))

			$('.subtotal').html($('#txtSubTot').val())
			$('.igv').html($('#txtIGV').val())
			$('.total').html($('#txtTotalGen').val())

			$(this).find('#tableModalProforma tbody').prepend($('<?php echo $args["clone"] ?> tbody tr').clone(false))
			$(this).find('#tableModalProforma tbody tr td:first-child').remove()
			
			$(this).find('#tableModalProforma tbody tr').each(function(index, value) {
				$(this).prepend('<td>' + (index+1) + '</td>')
				
				$(this).find('td:eq(1)').after('<td>' + $(this).attr('data-forma') + '</td>')
				$(this).find('td:eq(2)').after('<td>' + $(this).attr('data-laboratorio') + '</td>')
				$(this).find('td:eq(3)').after('<td>' + $(this).attr('data-vencimiento') + '</td>')
			})
			
			// insert forma y laboratorio

			$(this).find('#tableModalProforma tbody tr td:last-child').remove()
			$(this).find('#tableModalProforma tbody tr td:last-child').remove()
			$(this).find('#tableModalProforma tbody tr td:last-child').remove()
        })
        $(idModal).on('hide.bs.modal', function (e) {
            $(this).find('#tableModalProforma tbody tr').remove()
        })
    })
</script>
