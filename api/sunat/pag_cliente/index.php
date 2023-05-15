<?php
include_once('../../../info.php');
$ruc = DOCUMENTO_EMPRESA_E;
$ruta = "../api_cpe/PRODUCCION/".DOCUMENTO_EMPRESA_E."/";
$archivo = (isset($_GET["archivo"])) ? $_GET["archivo"] : "";
if (file_exists($ruta . $archivo . ".pdf")) {
    $validacion = "1";
} else {
    $validacion = "0";
}
?>

<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="favicon.ico">
        <title>CONSULTA DOCUMENTO ELECTRÓNICO</title>
        <link href="./css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="./css/theme.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <section id="login">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-md-offset-2">
                        <div class="col-md-6" style="display: flex; align-items: center; justify-content: center;">	    
                            <img src="./img/tulogo.png" alt=""  width="160px"  />
                        </div>
                        <div class="col-md-6">	    
                            <img src="./img/cuadro01.jpg" alt="" class="img-responsive" />
                        </div>
                        <div class="col-md-12">  	    
                            <div class="col-md-12 form">
                                <form action="#" class="form-horizontal" id="login-form">
                                    <div class="form-group">
                                        <label class="col-lg-5 control-label">Tipo documento</label>
                                        <div class="col-lg-7">
                                            <select id="txtTipoDte" name="txtTipoDte" class="form-control">
                                                <option value="01" selected="selected">Factura</option>
                                                <option value="07">Nota Credito</option>
                                                <option value="08">Nota Debito</option>
                                                <option value="03">Boleta</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-5 control-label">Folio del Documento<br>(Serie-Correlativo)</label>
                                        <div class="col-lg-7"><input id="txtNroComprobante" name="txtNroComprobante"  type="text" class="form-control" ></div>
                                    </div>
                                    <!-- 
                                    <div class="form-group">
                                        <label class="col-lg-5 control-label">Fecha Emisi�n<br>(Ej: DD-MM-AAAA)</label>
                                        <div class="col-lg-7"><input type="text" class="form-control" ></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-5 control-label">Monto Total S/.</label>
                                        <div class="col-lg-7"><input type="text" class="form-control" ></div>
                                    </div>
                                    -->
                                    <div class="form-group">
                                        <div class="col-lg-5"></div>
                                        <div class="col-lg-7">
                                            <button type="button" onclick="BuscarComprobante()" class="btn btn-danger">Ver documento</button>
                                        </div>
                                    </div>                                    
                                    <div id="xmlComprobantes">
                                        ---------------------------------------------------------------------------------------
                                        <fieldset>
                                            <?php if (file_exists($ruta . $archivo . ".XML")): ?>
                                                <a href="#" onclick="DescargaArchivo('CPE')"><button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-cloud-download"></span>CPE</button></a>
                                            <?php endif; ?>
                                            <a href="#" onclick="DescargaArchivo('PDF')"><button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-cloud-download"></span>PDF</button></a>
                                            
                                            <?php if (file_exists($ruta . $archivo . ".XML")): ?>                                            
                                                <a href="#" onclick="DescargaArchivo('CDR')"><button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-cloud-download"></span>CDR</button></a>                                                               
                                            <?php endif; ?>
                                        </fieldset>
                                    </div>
                                </form>
                            </div></div>
                    </div> 
                </div> <!-- /.col-xs-12 -->
            </div> <!-- /.row -->
        </section>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="./js/jquery.min.js" type="text/javascript"></script>
        <script src="./js/jquery-3.2.1.slim.min.js" type="text/javascript"></script>
        <script src="./js/popper.min.js" type="text/javascript"></script>
    </body>
    <script type='text/javascript'>

                                                function BuscarComprobante() {
                                                    var fichero = "<?php echo $ruc; ?>" + "-" + $('#txtTipoDte').val() + "-" + $('#txtNroComprobante').val();
                                                    document.location.href = "index.php?archivo=" + fichero;
                                                }

                                                function DescargaArchivo(tipo) {
                                                    var archivo = '<?php echo $archivo; ?>';
                                                    var ruta = '<?php echo $ruta; ?>';
                                                    if (tipo == "CPE") {
                                                        ruta = ruta + archivo + ".XML";
                                                        archivo = archivo + ".XML";
                                                    }
                                                    if (tipo == "CDR") {
                                                        ruta = ruta + "R-" + archivo + ".XML";
                                                        archivo = "R-" + archivo + ".XML";
                                                    }
                                                    if (tipo == "PDF") {
                                                        ruta = ruta + archivo + ".pdf";
                                                        archivo = archivo + ".pdf";
                                                    }
                                                    document.location = "../funcionesGlobales/descargaArchivo.php?archivo=" + archivo + "&ruta=" + ruta;
                                                }

                                                $(document).ready(function () {/////////////SE EJECUTARA AL CARGAR LA PAGINA
                                                    $('#xmlComprobantes').hide();
                                                    var val = '<?php echo $validacion; ?>';
                                                    var archivo = '<?php echo $archivo; ?>';
                                                    if (val == 1) {
                                                        $('#xmlComprobantes').show();
                                                    } else {
                                                        if (archivo !== "") {
                                                            alert("El Comprobante no Existe");
                                                        }
                                                        $('#xmlComprobantes').hide();
                                                    }
                                                });

    </script>
</html>

