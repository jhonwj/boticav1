<?php
define("SITE_URL", '/');
define("Producto", SITE_URL . "views/Gen_ProductoForm.php");
define("Forma_Farmaceutica", SITE_URL . "views/Gen_ProductoFormaFarmaceuticaForm.php");
define("Medicion", SITE_URL . "views/Gen_ProductoMedicionForm.php");
define("Marca", SITE_URL . "views/Gen_ProductoMarcaForm.php");
define("Categoria", SITE_URL . "views/Gen_ProductoCategoriaForm.php");
define("Compuesto", SITE_URL . "views/Gen_ProductoCompuestoForm.php");
define("PuntoVenta", SITE_URL . "views/V_VentaForm.php");
define("PreOrden", SITE_URL . "views/V_PreOrden.php");
define("Experto", SITE_URL . "views/V_ExpertoForm.php");
define("Inventario", SITE_URL . "views/Lo_InventarioForm.php");
define("RegVenta", SITE_URL . "views/ve_regventaform.php");
define("Cierre", SITE_URL . "views/ve_cierreform.php");
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

 ?>

 <nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo Login; ?>">Rojas Sport</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
    <ul class="nav navbar-nav" >
      <li class=""><a href="#" class="text-primary-color"><i class="fa fa-home"></i>  Inicio</a></li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><img src="medical-drugs-pills-and-capsules.svg" style="width:1em;"> Gestion Producto
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li ><a href= "<?php echo Producto; ?>" class="text-primary-color"><i class="fa fa-"></i>Producto</a></li>
          <li><a href="<?php echo Marca; ?>" >Marca</a></li>
          <!--<li><a href="<?php echo Forma_Farmaceutica; ?>">Forma Farmaceutica</a></li>-->
          <li><a href="<?php echo Modelo; ?>">Modelo</a></li>
          <li><a href="<?php echo Medicion; ?>">Medicion</a></li>
          <li><a href="<?php echo Categoria; ?>">Categoria</a></li>
          <!--<li><a href="<?php echo Compuesto; ?>">Compuesto</a></li>-->
        </ul>
      </li>

      <li class="dropdown">
        <a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-clipboard"></i> Logistica
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo Inventario; ?>">MOVIMIENTO</a></li>
          <!--<li><a href="<?php echo GenerarCodigoBarra; ?>">CODIGO DE BARRA</a></li>-->
          <li role="separator" class="divider"></li>
          <li class="dropdown-header">KARDEX CONTABLE</li>
            <!--<li><a href="<?php echo ReporteKardex."?Tipo=0"; ?>">1. KARDEX</a></li>-->
            <li><a href="<?php echo ReporteKardexValorizado."?Tipo=0"; ?>">1. KARDEX VALORIZADO</a></li>
          <li class="dropdown-header">KARDEX FECHA STOCK</li>
            <li><a href="<?php echo ReporteKardex."?Tipo=1"; ?>">1. KARDEX</a></li>
            <li><a href="<?php echo ReporteKardexValorizado."?Tipo=1"; ?>">2. KARDEX VALORIZADO</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="<?php echo ReporteStock; ?>"></a></li>
          <li><a href="<?php echo ReporteStock; ?>">STOCK</a></li>
          <li><a href="<?php echo RegMovimiento; ?>">REGISTRO DE MOVIMIENTO</a></li>
          <li><a href="<?php echo RegCompraContable; ?>">REGISTRO DE COMPRA CONTABLE</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="<?php echo BuscarVencimiento; ?>">PRODUCTOS POR VENCER</a></li>
        </ul>
      </li>
      <li><a href="<?php echo Experto; ?>">Sistema Experto</a></li>
      <li class="dropdown">
        <a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-clipboard"></i> Ventas
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo PuntoVenta; ?>">Punto de venta</a></li>
          <li><a href="<?php echo PreOrden; ?>">Pre Orden</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="<?php echo RegVenta; ?>">Registro de Ventas</a></li>
          <li><a href="<?php echo Cierre; ?>">Cierre de Caja</a></li>
          <li><a href="<?php echo CambioPrecioBloque; ?>">Cambio de precio por bloque</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="<?php echo Reimpresion; ?>">Reimpresión de Documentos</a></li>
          <!--<li><a href="<?php echo ReporteUtilidadBruta; ?>">Reporte Utilidad Bruta</a></li>-->

        </ul>
      </li>

      
      <li class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#"> Caja y Banco
      <span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="<?php echo CajaYBanco; ?>"> Caja y Banco</a></li>
        <li><a href="<?php echo CajaYBancoBuscador; ?>">Buscador de Caja y Banco</a></li>
      </ul>
        
      </li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"> Seguridad
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo Usuarios; ?>" >Usuarios</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="<?php echo Logout; ?>"> <i class="fa fa-sign-out" aria-hidden="true"></i></a>
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