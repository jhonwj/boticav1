<?php
include_once("../clases/BnGeneral.php");
//include("../clases/DtGeneral.php");
include_once("../clases/helpers/Modal.php");
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title>Botica - Producto</title>

 </head>
<?php include_once 'linker.php'; ?>
<script type="text/javascript">

    $(document).ready(function(){})

</script>

<body>
<?php include("header.php"); ?>
<div class="container">
    <div class="" style="margin-left:10px; margin-right:10px;">
        <div class="row">
            <div class="col-lg-4">
                <div class="input-group" style="margin-bottom:20px;">
                <input type="text" class="form-control" id="txtTipoCuenta" placeholder="Cuenta">
                <input type="hidden" class="form-control" id="txtTipoCuentaId" value="1">
                <span class="input-group-btn">
                    <button id="btnTipoCuenta" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
                </span>
                </div>
            </div>
            <div class="col-lg-3">
               <div class="input-group" style="margin-bottom:20px;">
                 <!-- <label for="txtTipoOpe">Tipo de Operacion</label> -->
                 <input type="text" class="form-control" id="txtTipoOpe" placeholder="Tipo de Operacion">
                 <input type="hidden" class="form-control" id="txtTipoOpeId" placeholder="Tipo de Operacion">
                 <span class="input-group-btn">
                   <button id="btnTipoOpe" class="btn btn-danger" type="button"><i class="fa fa-search-plus"></i></button>
                 </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 form-group">
				<label>Fecha Inicio</label>
				<input type="date" id="fechaIni" class="form-control">
            </div>
            <div class="col-md-4 form-group">
				<label>Fecha Final</label>
				<input type="date" id="fechaFinal" class="form-control">
			</div>
        </div>
        <button type="button" id="btnGenerar" class="btn btn-success">Generar</button>
        <br><br>
        <div class="">
       <table class="table table-bordered table-striped table-responsive" id="tableCajaBanco">
         <thead>
           <tr>
             <th>Cuenta</th>
             <th colspan="5">Caja </th>
           </tr>
           <tr>
             <th>ID</th>
             <th>Concepto</th>
             <th>Ingresos </th>
             <th >Salida </th>
             <th >Saldo </th>
             <th >Acciones </th>
           </tr>
         </thead>
       </table>
     </div>
    </div>
</div>
<?php include("footer.php"); ?>
 </body>
 </html>
