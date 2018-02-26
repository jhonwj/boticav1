<?php
?>
<div class="modal fade" id="<?php echo $args['id'] ?>" role="dialog">
  <!-- <div class="modal-dialog" style="width:800px"> -->
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
            <h4 class="modal-title" id="">
                Seleccionar Moneda
            </h4>
		</div>
		<div class="modal-body" style="overflow-x:auto;">
      <!-- Table -->
      <table id="tableModalMoneda" class="table table-striped table-bordered" style="width: 100%">
        <thead>
					<th>Moneda</th>
          <th>Tipo de cambio</th>
        </thead>
      </table>
		</div>
		<div class="modal-footer">
		</div>
	</div>
	</div>
</div>

<script>
    $(document).ready(function () {
        var idModal = '#<?php echo $args["id"] ?>'

        $(idModal).on('show.bs.modal', function (e) {
					$("#tableModalMoneda").DataTable().destroy();
					var tableMoneda = $("#tableModalMoneda").DataTable({
			      "bProcessing": true,
			      "sAjaxSource": "../controllers/server_processingMoneda.php",
			      "bPaginate":true,
			      "sPaginationType":"full_numbers",
			      "iDisplayLength": 5,
			      "aoColumns": [
				      { mData: 'Moneda' } ,
							{ mData: 'TipoCambio' }
				    ]
			    });



					// Add event listener for opening and closing details
			    $(idModal + ' tbody').on('click', 'tr', function () {
            var data = tableMoneda.row( this ).data();
            
            $('#txtMoneda').val(data.Moneda)
            $('#txtTipoCambio').val(data.TipoCambio)
            $(idModal).modal("hide");
			    });

        })
        $(idModal).on('hide.bs.modal', function (e) {
            //$(this).find('#tableModalProforma tbody tr').remove()
        })


    })
</script>
