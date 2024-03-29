<?php
define("SITE_URL", "http://".$_SERVER['SERVER_NAME'].'/');
define("", SITE_URL . "views/Gen_ProductoForm.php");
define("Forma_Farmaceutica", SITE_URL . "views/Gen_ProductoFormaFarmaceuticaForm.php");
define("Medicion", SITE_URL . "views/Gen_ProductoMedicionForm.php");
define("Marca", SITE_URL . "views/Gen_ProductoMarcaForm.php");
define("Categoria", SITE_URL . "views/Gen_ProductoCategoriaForm.php");
define("Compuesto", SITE_URL . "views/Gen_ProductoCompuestoForm.php");
define("Clientes", SITE_URL . "views/V_VentaCliente.php");
define("PuntoVenta", SITE_URL . "views/V_VentaForm.php");
define("PreOrden", SITE_URL . "views/V_PreOrden.php");
define("Experto", SITE_URL . "views/V_ExpertoForm.php");
define("Inventario", SITE_URL . "views/Lo_InventarioForm.php");
define("RegVenta", SITE_URL . "views/ve_regventaform.php");
define("Cierre", SITE_URL . "views/ve_cierreform.php?noDetalle=1");
define("CierreReporte", SITE_URL . "/views/v_cierrereporte.php");
define("CambioPrecioBloque", SITE_URL . "views/ve_cambiopreciobloque.php");
define("Reimpresion", SITE_URL . "views/ve_reimpresionDocumentos.php");
define("ReporteUtilidadBruta", SITE_URL . "views/ve_reporteUtilidadBruta.php");
define("RegMovimiento", SITE_URL . "views/lo_regmovimiento.php");
define("RegCompraContable", SITE_URL . "views/lo_regcompracontable.php");
define("ReporteStock", SITE_URL . "views/Lo_ReporteStockForm.php");
define("ReporteKardex", SITE_URL . "views/lo_kardexform.php");
define("ReporteKardexValorizado", SITE_URL . "views/lo_kardexvalorizadoform.php");
define("Login", SITE_URL . "index.php");
define("CajaYBanco", SITE_URL . "views/FrmCb_CajaBanco.php");
define("Logout", SITE_URL . "controllers/logout.php");
define("Usuarios", SITE_URL . "views/Seg_UsuarioForm.php");
define("Roles", SITE_URL . "views/Seg_UsuarioPerfil.php");
define("CajaYBancoBuscador", SITE_URL . "views/FrmCb_CajaBancoBuscador.php");
define("BuscarVencimiento", SITE_URL . "views/Lo_BuscarVencimiento.php");
define("GenerarCodigoBarra", SITE_URL . "views/Lo_GenerarCodigoBarra.php");
define("Modelo", SITE_URL . "views/Gen_ProductoModelo.php");

date_default_timezone_set('America/Lima');
include_once('../info.php');
 ?>
<script> 
function datos(){
          fetch('https://deperu.com/api/rest/cotizaciondolar.json')
          .then(res =>res.json())
          .then(data=>{
            var cotizacion = data.Cotizacion[0];
            var compra = cotizacion.Compra; 
            var venta = cotizacion.Venta; 

            if ($.isNumeric(compra) && $.isNumeric(venta)) {
              $('#compra').html(compra)
              $('#venta').html(venta)
            }
          })
        }
        datos();
</script>
 <nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo Login; ?>"><?php echo NOMBRE_SISTEMA ?></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
    <ul class="nav navbar-nav" >
      <!--<li class=""><a href="#" class="text-primary-color"><i class="fa fa-home"></i>  Inicio</a></li>-->
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><img src="medical-drugs-pills-and-capsules.svg" style="width:1em;"> Gestion
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li ><a href= "/views/Gen_ProductoForm.php" class="text-primary-color"><i class="fa fa-"></i>Modificar producto</a></li>
          <li role="separator" class="divider"></li>
          <li ><a href= "/views/Tr_Viajes.php" class="text-primary-color"><i class="fa fa-"></i>Registro de viajes</a></li>
          <li ><a href= "/views/Tr_Conductores.php" class="text-primary-color"><i class="fa fa-"></i>Registro de conductores</a></li>
          <li ><a href= "/views/Tr_Vehiculos.php" class="text-primary-color"><i class="fa fa-"></i>Registro de vehiculos</a></li>
          <!--<li ><a href= "/views/Tr_Asientos.php" class="text-primary-color"><i class="fa fa-"></i>Configuración de asientos</a></li>-->
          <!--<li><a href="<?php echo Marca; ?>" >Marca</a></li>-->
          <!--<li><a href="<?php echo Forma_Farmaceutica; ?>">Forma Farmaceutica</a></li>-->
          <!--<li><a href="<?php echo Modelo; ?>">Modelo</a></li>
          <li><a href="<?php echo Medicion; ?>">Medicion</a></li>
          <li><a href="<?php echo Categoria; ?>">Categoria</a></li>-->
          <!--<li><a href="<?php echo Compuesto; ?>">Compuesto</a></li>-->
        </ul>
      </li>

      <li class="dropdown">
        <!--<a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-clipboard"></i> Logistica
        <span class="caret"></span></a>-->
        <ul class="dropdown-menu">
          <li><a href="<?php echo Inventario; ?>">REGISTRO DE COMPRAS</a></li>
          <!--<li><a href="<?php echo GenerarCodigoBarra; ?>">CODIGO DE BARRA</a></li>-->
          <li role="separator" class="divider"></li>
          <li class="dropdown-header">KARDEX CONTABLE</li>
            <!--<li><a href="<?php echo ReporteKardex."?Tipo=0"; ?>">1. KARDEX</a></li>-->
            <li><a href="<?php echo ReporteKardexValorizado."?Tipo=0"; ?>">1. KARDEX VALORIZADO</a></li>
          <li class="dropdown-header">KARDEX FECHA STOCK</li>
            <li><a href="<?php echo ReporteKardex."?Tipo=1"; ?>">1. KARDEX</a></li>
            <!-- <li><a href="<?php /*echo ReporteKardexValorizado."?Tipo=1";*/ ?>">2. KARDEX VALORIZADO</a></li> -->
            <li><a href="/views/lo_reportemasmovimiento.php">ARTICULOS CON MAS MOVIMIENTOS</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="/views/lo_kardex.php">NUEVO KARDEX</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="<?php echo ReporteStock; ?>"></a></li>
          <li><a href="<?php echo ReporteStock; ?>">STOCK</a></li>
          <li><a href="<?php echo RegMovimiento; ?>">REGISTRO DE MOVIMIENTO</a></li>
          <!--<li><a href="<?php echo RegCompraContable; ?>">REGISTRO DE COMPRA CONTABLE</a></li>-->
          <li role="separator" class="divider"></li>
          <!--<li><a href="<?php echo BuscarVencimiento; ?>">PRODUCTOS POR VENCER</a></li>-->
        </ul>
      </li>
      <!--<li><a href="<?php echo Experto; ?>">Sistema Experto</a></li>-->
      <li class="dropdown">
        <a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-clipboard"></i> Ventas
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo PuntoVenta; ?>">Punto de venta</a></li>
          <li><a href="<?php echo Clientes; ?>">Clientes </a></li>
          <!--<li><a href="<?php echo PreOrden; ?>">Pre Orden</a></li>-->
          <!--<li><a href="/views/habitaciones.php">Proyectos</a></li>-->
          <li role="separator" class="divider"></li>
          <li><a href="<?php echo RegVenta; ?>">Registro de Ventas</a></li>
          <!--<li><a href="<?php echo SITE_URL . "views/ve_regventaformvendedor.php" ?>">Registro de Ventas VENDEDOR</a></li>-->
          <li><a href="<?php echo Cierre; ?>">Cierre de Caja</a></li>
           <!--<li><a href="<?php echo CierreReporte; ?>">Reporte Cierre de Caja</a></li>-->
          <!--<li><a href="<?php echo CambioPrecioBloque; ?>">Cambio de precio por bloque</a></li>-->
          <li role="separator" class="divider"></li>
          <li><a href="<?php echo Reimpresion; ?>">Reimpresión de Documentos</a></li>
          <!--<li><a href="<?php echo ReporteUtilidadBruta; ?>">Reporte Utilidad Bruta</a></li>-->

        </ul>
      </li>


      <li class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#"> Facturación
      <span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="/views/facturacion.php"> Facturación Electrónica </a></li>
        <li><a href="/api/sunat/pag_cliente/" target="_blank"> Página Cliente </a></li>
        <li><a href="http://emision.factura.vip/" target="_blank"> Consulta CPE </a></li>
      </ul>

      </li>


      <li class="dropdown">
      <!--<a class="dropdown-toggle" data-toggle="dropdown" href="#"> Reportes
      <span class="caret"></span></a>-->
      <ul class="dropdown-menu">
        <li><a href="/views/reportehuespedes.php"> Registro de Proyectos </a></li>
        <li><a href="/views/reportestockminimo.php"> Reporte Stock Mínimo </a></li>
        <li><a href="/views/reportesdeudacliente.php"> Reporte Deuda Clientes </a></li>
        <li><a href="/views/reportesdeudaproveedor.php"> Reporte Deuda Proveedores </a></li>
        <li><a href="/views/reportesrankingclientes.php"> Reporte Ranking Clientes </a></li>
        <li><a href="/views/reportesventasporentregar.php"> Reporte Ventas por entregar </a></li>
        <li><a href="/views/reportes.php">REPORTES</a></li>
      </ul>

      </li>


      <li class="dropdown">
      <!--<a class="dropdown-toggle" data-toggle="dropdown" href="#"> Caja y Banco
      <span class="caret"></span></a>-->
      <ul class="dropdown-menu">
        <li><a href="<?php echo CajaYBanco; ?>"> Caja y Banco</a></li>
        <li><a href="<?php echo CajaYBancoBuscador; ?>">Buscador de Caja y Banco</a></li>
      </ul>

      </li>
      <li class="dropdown">
        <!--<a class="dropdown-toggle" data-toggle="dropdown" href="#"> Seguridad
        <span class="caret"></span></a>-->
        <ul class="dropdown-menu">
          <li><a href="<?php echo Usuarios; ?>" >Usuarios</a></li>
        </ul>
      </li>
      <?php if ($_SESSION['idPerfil'] == 1): ?>
        <!--<li class="dropdown">
          <a href="#" onclick="window.open('http://app.factura.vip/client/login/10423952437','winname','directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no,width=600,height=700');"><b>APP</b></a>
        </li>-->
      <?php endif; ?>
      <li class="dropdown">
        <a href="<?php echo Logout; ?>"> <i class="fa fa-sign-out" aria-hidden="true"></i></a>
      </li>
      <li><div style="color: #EBF5FB; font-weight: bold; font-size: 13px; padding-top: 8px; display: block;">
      Compra: <span id="compra"></span></br> 
      Venta : <span id="venta"></span>
      </div></li>
      <li>
        <img src="/resources/images/neurologo.png" width="150px" style="margin-left: 10px; margin-top: 8px">
      </li>
      <li style="color: white; font-weight: bold; margin-left: 5px; font-size: 1.5rem;">
          <label>Contacto: Telf: (062) 511550 - Cel: 954370221 </label>
          <br>
          <label>Soporte: Cel: 997578199 </label>
      </li>
    </ul>
    <!-- <ul class="nav navbar-nav navbar-right"> -->
      <!-- <li><a href=""><i class="fa fa-user-circle"></i> Usuario</a></li> -->
      <!-- <li><a href=""><i class="fa fa-sign-out"></i> Cerrar Sesion</a></li> -->
    <!-- </ul> -->
  </div>
  </div>
</nav>


<div id="loading"></div>

<script>
    sessionStorage.clear();
    sessionStorage.setItem('User', "<?php echo $_SESSION['user'] ?>");
    sessionStorage.setItem('IdPerfil', "<?php echo $_SESSION['idPerfil'] ?>")
  </script>
