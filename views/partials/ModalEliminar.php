<?php
?>
<div class="modal fade" id="<?php echo $args['id'] ?>" role="dialog">
  <!-- <div class="modal-dialog" style="width:800px"> -->
	<div class="modal-dialog" style="width:800px">
	<div class="modal-content">
		<div class="modal-header">
            <h4 class="modal-title" id="">
                ¿Está seguro que desea eliminar <strong><?php echo $args['title'] ?></strong>?
            </h4>
		</div>
		<!--<div class="modal-body" style="overflow-x:auto;">
		</div>-->
		<div class="modal-footer">
            <button type="button" class="btn btn-danger" id="btnEliminar" data-dismiss="modal">Eliminar</button>
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
            var title = button.data('title')
			var idrow = button.data('idrow')
            var modal = $(this)

			modal.find('#btnEliminar').data('idrow', idrow)
			modal.find('#btnEliminar').data('title', title)
            if (title) {
                modal.find('.modal-title > strong').text(title)
            }
        })

        $(idModal + ' #btnEliminar').click(function() {
			idrow = $(this).data('idrow')
			title = $(this).data('title')

			if(idrow) {
				$.ajax({
					url: '../controllers/<?php echo $args['controller'] ?>.php',
					type: 'post',
					data: { delete : true, id : idrow, title: title },
					dataType: 'json',
					success: function(respuesta){
						if (respuesta.success) {
							$("<?php echo $args['reload'] ?>").DataTable().ajax.reload();
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
        })

    })
</script>
