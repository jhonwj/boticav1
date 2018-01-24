<?php
define("SITE_URL", '/');
define("Producto", SITE_URL . "views/gen_productoform.php");
define("Forma_Farmaceutica", SITE_URL . "views/gen_productoformafarmaceuticaform.php");
define("Medicion", SITE_URL . "views/gen_productomedicionform.php");
define("Marca", SITE_URL . "views/gen_productomarcaform.php");
define("Categoria", SITE_URL . "views/gen_productocategoriaform.php");
define("Compuesto", SITE_URL . "views/gen_productocompuestoform.php");
define("PuntoVenta", SITE_URL . "views/v_ventaform.php");
define("Experto", SITE_URL . "views/v_expertoform.php");
define("Inventario", SITE_URL . "views/lo_inventarioform.php");
define("RegVenta", SITE_URL . "views/ve_regventaform.php");
define("Cierre", SITE_URL . "views/ve_cierreform.php");
define("RegMovimiento", SITE_URL . "views/lo_regmovimiento.php");
define("RegCompraContable", SITE_URL . "views/lo_regcompracontable.php");
define("ReporteStock", SITE_URL . "views/lo_reportestockform.php");
define("ReporteKardex", SITE_URL . "views/lo_kardexform.php");
define("ReporteKardexValorizado", SITE_URL . "views/lo_kardexvalorizadoform.php");
define("Login", SITE_URL . "index.php");
define("CajaYBanco", SITE_URL . "views/frmcb_cajabanco.php");
define("Logout", SITE_URL . "controllers/logout.php");

 ?>

 <nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo Login; ?>">Botica</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
    <ul class="nav navbar-nav" >
      <li class=""><a href="#" class="text-primary-color"><i class="fa fa-home"></i>  Inicio</a></li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><img src="medical-drugs-pills-and-capsules.svg" style="width:1em;"> Gestion Producto
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li ><a href= "<?php echo Producto; ?>" class="text-primary-color"><i class="fa fa-"></i>Producto</a></li>
          <li><a href="<?php echo Marca; ?>" >Laboratorio</a></li>
          <li><a href="<?php echo Forma_Farmaceutica; ?>">Forma Farmaceutica</a></li>
          <li><a href="<?php echo Medicion; ?>">Medicion</a></li>
          <li><a href="<?php echo Categoria; ?>">Categoria</a></li>
          <li><a href="<?php echo Compuesto; ?>">Compuesto</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-clipboard"></i> Ventas
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo PuntoVenta; ?>">Punto de venta</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="<?php echo RegVenta; ?>">Registro de Ventas</a></li>
          <li><a href="<?php echo Cierre; ?>">Cierre de Caja</a></li>
        </ul>
      </li>
      <li><a href="<?php echo Experto; ?>">Sistema Experto</a></li>
      <li class="dropdown">
        <a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-clipboard"></i> Logistica
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo Inventario; ?>">MOVIMIENTO</a></li>
          <li role="separator" class="divider"></li>
          <li class="dropdown-header">KARDEX CONTABLE</li>
            <li><a href="<?php echo ReporteKardex."?Tipo=0"; ?>">1. KARDEX</a></li>
            <li><a href="<?php echo ReporteKardexValorizado."?Tipo=0"; ?>">2. KARDEX VALORIZADO</a></li>
          <li class="dropdown-header">KARDEX FECHA STOCK</li>
            <li><a href="<?php echo ReporteKardex."?Tipo=1"; ?>">1. KARDEX</a></li>
            <li><a href="<?php echo ReporteKardexValorizado."?Tipo=1"; ?>">2. KARDEX VALORIZADO</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="<?php echo ReporteStock; ?>"></a></li>
          <li><a href="<?php echo ReporteStock; ?>">STOCK</a></li>
          <li><a href="<?php echo RegMovimiento; ?>">REGISTRO DE MOVIMIENTO</a></li>
          <li><a href="<?php echo RegCompraContable; ?>">REGISTRO DE COMPRA CONTABLE</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="<?php echo CajaYBanco; ?>"> Caja y Banco</a>
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
