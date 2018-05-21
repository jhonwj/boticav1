<?php
?>
<div class="modal fade" id="<?php echo $args['id'] ?>" role="dialog">
  <!-- <div class="modal-dialog" style="width:800px"> -->
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
            <h4 class="modal-title" id="">
                Buscar Pre orden
            </h4>
		</div>
		<div class="modal-body" style="overflow-x:auto;">
      <!-- Table -->
      <table id="tableModalPreOrden" class="table table-striped table-bordered" style="width: 100%">
        <thead>
					<th></th>
          <th>DNI/RUC</th>
          <th>Cliente</th>
					<th>Tot.</th>
          <th>Fecha</th>
					<th>
						Acciones
					</th>
        </thead>
      </table>
		</div>
		<div class="modal-footer">
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
					$("#tableModalPreOrden").DataTable().destroy();
					var tablePreOrden = $("#tableModalPreOrden").DataTable({
			      "bProcessing": true,
			      "sAjaxSource": "../controllers/server_processingPreOrden.php",
			      "bPaginate":true,
			      "sPaginationType":"full_numbers",
			      "iDisplayLength": 5,
			      "aoColumns": [
							{
	                "className":      'details-control',
	                "orderable":      false,
	                "data":           null,
	                "defaultContent": '<button type="button" class="btn"><i class="fa fa-plus"></i></button>'
	            },
				      { mData: 'DniRuc' } ,
							{ mData: 'Cliente' },
							{ mData: 'Total' },
				      { mData: 'FechaReg' },
				      { mRender : function(data, type, row){
								var buttons = "<button class='btn btn-success' onclick='cargarPreOrden(" + JSON.stringify(row) +")'>Cargar</button>"
								buttons += "<button class='btn btn-danger' onclick='eliminarPreOrden(" + JSON.stringify(row) + ")'>Eliminar</button>"

				        return buttons;
				      }}
				    ]
			    });

					function format ( d ) {
			    // `d` is the original data object for the row

						var table = '<table class="table">';
						table += '<thead><tr><th></th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Total</th></tr></thead>'
						var productos = d.Productos
						productos.forEach(function (el, index, array) {
							table += '<tr>' +
								'<td></td>' +
								'<td>'+el.Producto+'</td>' +
								'<td>'+el.Cantidad+'</td>' +
								'<td>'+el.Precio+'</td>' +
								'<td>'+(parseFloat(el.Precio) * parseFloat(el.Cantidad))+'</td>'
							+ '</tr>'
						})
						table += '</table>'
				    return table;
					}

					// Add event listener for opening and closing details
			    $(idModal + ' tbody').on('click', 'td.details-control button', function () {
			        var tr = $(this).closest('tr');
			        var row = tablePreOrden.row( tr );
							var data = row.data();

							var xhr = $.ajax({
						    url: '../controllers/server_processingPreOrden.php?idPreOrden=' + data.IdPreOrden,
						    dataType: 'json',
						    success: function(respuesta){
									 data.Productos = respuesta
									 if ( row.child.isShown() ) {
											 // This row is already open - close it
											 row.child.hide();
											 tr.removeClass('shown');
									 }
									 else {
											 // Open this row
											 row.child( format(data) ).show();
											 tr.addClass('shown');
									 }

						    },
						    error: function(XMLHttpRequest, textStatus, errorThrown) {
						        alert("Status: " + textStatus); alert("Error: " + errorThrown);
						    }
						  });


			    });

        })
        $(idModal).on('hide.bs.modal', function (e) {
            //$(this).find('#tableModalProforma tbody tr').remove()
        })


    })
</script>
