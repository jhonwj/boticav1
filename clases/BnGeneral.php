<?php

	include_once($_SERVER["DOCUMENT_ROOT"] . "/models/DBManager.php");
	//include 'http://sistemasjeam.com/prevenvac/models/DBManager.php';
	function fn_devolverImpresionCentrar($Texto){
		$textoFinal=$Texto;
		$long=strlen($textoFinal);
		$long=$long/2;
		$maximoCaracteres=30-$long;
		for ($i=1; $i < $maximoCaracteres ; $i++) {


				$textoFinal=" " . $textoFinal;

		}
		//echo $Ssql;
		return $textoFinal;
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
	function fn_devolverProductoMarca($criterio, $orden){

		$Ssql="SELECT IdProductoMarca, ProductoMarca, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoMarca";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		if (!empty($orden)) {
			$Ssql= $Ssql." ORDER BY ".$orden;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
   function fn_devolverProductoMarcaSiExiste($ProductoMarca){

		$Ssql="SELECT IdProductoMarca, ProductoMarca, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoMarca";
		//echo $Ssql;
		$criterio = "ProductoMarca = '$ProductoMarca'";

		$result = fn_devolverProductoMarca($criterio, "");

		$existe = false;
		while ($row =mysqli_fetch_row($result)) {
		 $existe = true;
		 break;
		}
		return $existe;
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
//////////////////////////////////////////////////////////////////////////////////
    function fn_devolverProductoCategoria($criterio, $orden){

		$Ssql="SELECT IdProductoCategoria, ProductoCategoria, IdProductoCategoriaSub, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoCategoria";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		if (!empty($orden)) {
			$Ssql= $Ssql." ORDER BY ".$orden;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
    function fn_devolverDocVenta($criterio, $orden){

		$Ssql="SELECT
Ve_DocVenta.idDocVenta,
Ve_DocVenta.IdDocVentaPuntoVenta,
Ve_DocVentaPuntoVenta.PuntoVenta,
Ve_DocVentaCliente.Cliente,
Ve_DocVentaCliente.DniRuc,
Ve_DocVentaCliente.Direccion,
Ve_DocVenta.IdTipoDoc,
Ve_DocVentaTipoDoc.TipoDoc,
Ve_DocVenta.IdAlmacen,
Lo_Almacen.Anulado,
Ve_DocVenta.IdCliente,
Ve_DocVenta.Serie,
Ve_DocVenta.Numero,
Ve_DocVenta.FechaDoc,
Ve_DocVenta.Anulado,
Ve_DocVenta.FechaReg,
Ve_DocVenta.UsuarioReg,
Ve_DocVenta.FechaMod,
Ve_DocVenta.UsuarioMod,
Ve_DocVentaPuntoVenta.SerieImpresora,
Ve_DocVentaPuntoVenta.RutaImpresora
FROM
Ve_DocVenta
INNER JOIN Ve_DocVentaPuntoVenta ON Ve_DocVenta.IdDocVentaPuntoVenta = Ve_DocVentaPuntoVenta.IdDocVentaPuntoVenta
INNER JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
INNER JOIN Lo_Almacen ON Ve_DocVenta.IdAlmacen = Lo_Almacen.IdAlmacen";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		if (!empty($orden)) {
			$Ssql= $Ssql." ORDER BY ".$orden;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
    function fn_devolverDocVentaDet($criterio, $orden){

		$Ssql="SELECT
Ve_DocVentaDet.IdDocVentaDet,
Ve_DocVentaDet.IdDocVenta,
Ve_DocVentaDet.IdProducto,
Gen_Producto.ProductoDesc,
Gen_Producto.Producto,
Ve_DocVentaDet.Cantidad,
Ve_DocVentaDet.Precio,
Round(Ve_DocVentaDet.Cantidad*
Ve_DocVentaDet.Precio,2) as TOTAL
FROM
Ve_DocVentaDet
INNER JOIN Gen_Producto ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto ";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		if (!empty($orden)) {
			$Ssql= $Ssql." ORDER BY ".$orden;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
   function fn_devolverProductoCategoriaSiExiste($ProductoCategoria){

		$Ssql="SELECT IdProductoCategoria, ProductoCategoria, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoCategoria";
		//echo $Ssql;
		$criterio = "ProductoCategoria = '$ProductoCategoria'";

		$result = fn_devolverProductoCategoria($criterio, "");

		$existe = false;
		while ($row =mysqli_fetch_row($result)) {
		 $existe = true;
		 break;
		}
		return $existe;
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
   //////////////////////////////////////////////////////////////////////////////////
    function fn_devolverProductoMedicion($criterio, $orden){

		$Ssql="SELECT IdProductoMedicion, ProductoMedicion, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoMedicion";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		if (!empty($orden)) {
			$Ssql= $Ssql." ORDER BY ".$orden;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
   function fn_devolverProductoMedicionSiExiste($ProductoMedicion){

		$Ssql="SELECT IdProductoMedicion, ProductoMedicion, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoMedicion";
		//echo $Ssql;
		$criterio = "ProductoMedicion = '$ProductoMedicion'";

		$result = fn_devolverProductoMedicion($criterio, "");

		$existe = false;
		while ($row =mysqli_fetch_row($result)) {
		 $existe = true;
		 break;
		}
		return $existe;
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
     //////////////////////////////////////////////////////////////////////////////////
    function fn_devolverProductoFormaFarmaceutica($criterio, $orden){

		$Ssql="SELECT IdProductoFormaFarmaceutica, ProductoFormaFarmaceutica, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoFormaFarmaceutica";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		if (!empty($orden)) {
			$Ssql= $Ssql." ORDER BY ".$orden;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
   function fn_devolverProductoFormaFarmaceuticaSiExiste($ProductoFormaFarmaceutica){

		$Ssql="SELECT IdProductoFormaFarmaceutica, ProductoFormaFarmaceutica, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoFormaFarmaceutica";
		//echo $Ssql;
		$criterio = "ProductoFormaFarmaceutica = '$ProductoFormaFarmaceutica'";

		$result = fn_devolverProductoFormaFarmaceutica($criterio, "");

		$existe = false;
		while ($row =mysqli_fetch_row($result)) {
		 $existe = true;
		 break;
		}
		return $existe;
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }

    //////////////////////////////////////////////////////////////////////////////////
    function fn_devolverProductoCompuesto($criterio, $orden){

		$Ssql="SELECT IdProductoCompuesto, ProductoCompuesto, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoCompuesto";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		if (!empty($orden)) {
			$Ssql= $Ssql." ORDER BY ".$orden;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
   function fn_devolverProductoCompuestoSiExiste($ProductoCompuesto){

		$Ssql="SELECT IdProductoCompuesto, ProductoCompuesto, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoCompuesto";
		//echo $Ssql;
		$criterio = "ProductoCompuesto = '$ProductoCompuesto'";

		$result = fn_devolverProductoCompuesto($criterio, "");

		$existe = false;
		while ($row =mysqli_fetch_row($result)) {
		 $existe = true;
		 break;
		}
		return $existe;
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }

       function fn_devolverfechaActual(){

		$Ssql="SELECT now();";
		//echo $Ssql;

		$result = getSQLResultSet($Ssql);

		$existe = "NO";
		while ($row =mysqli_fetch_row($result)) {
		 $existe = $row[0];
		 break;
		}
		return $existe;
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }

       //////////////////////////////////////////////////////////////////////////////////
    function fn_devolverProducto($criterio, $orden){

		$Ssql="SELECT
					Gen_Producto.IdProducto,
					Gen_Producto.IdProductoMarca,
					Gen_ProductoMarca.ProductoMarca,
					Gen_Producto.IdProductoFormaFarmaceutica,
					Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica,
					Gen_Producto.IdProductoMedicion,
					Gen_ProductoMedicion.ProductoMedicion,
					Gen_Producto.IdProductoCategoria,
					Gen_ProductoCategoria.ProductoCategoria,
					Gen_Producto.Producto,
					Gen_Producto.ProductoDesc,
					Gen_Producto.ProductoDescCorto,
					Gen_Producto.CodigoBarra,
					Gen_Producto.Codigo,
					Gen_Producto.Dosis,
					Gen_Producto.PrecioContado,
					Gen_Producto.PrecioPorMayor,
					Gen_Producto.StockPorMayor,
					Gen_Producto.StockMinimo,
					Gen_Producto.ControlaStock,
					Gen_Producto.Anulado,
					Gen_Producto.FechaReg,
					Gen_Producto.UsuarioReg,
					Gen_Producto.FechaMod,
					Gen_Producto.UsuarioMod,
					Gen_Producto.Dosis,
					Gen_ProductoBloque.Bloque,
					Gen_Producto.VentaEstrategica,
					Gen_Producto.PrecioCosto,
					Gen_Producto.PorcentajeUtilidad
					FROM
					Gen_Producto
					INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
					INNER JOIN Gen_ProductoFormaFarmaceutica ON Gen_Producto.IdProductoFormaFarmaceutica = Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica
					INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
					INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
					LEFT JOIN Gen_ProductoBloque ON Gen_Producto.IdBloque = Gen_ProductoBloque.IdBloque";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }
   /*function fn_devolverProductoCompuestoSiExiste($ProductoCompuesto){

		$Ssql="SELECT IdProductoCompuesto, ProductoCompuesto, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Gen_ProductoCompuesto";
		//echo $Ssql;
		$criterio = "ProductoCompuesto = '$ProductoCompuesto'";

		$result = fn_devolverProductoCompuesto($criterio, "");

		$existe = false;
		while ($row =mysqli_fetch_row($result)) {
		 $existe = true;
		 break;
		}
		return $existe;
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

    }*/

    function fn_devolverTipoDocVenta($criterio, $orden){
    	$Ssql="SELECT IdTipoDoc, TipoDoc, VaRegVenta, CodSunat FROM Ve_DocVentaTipoDoc";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		if (!empty($orden)) {
			$Ssql= $Ssql." ORDER BY ".$orden;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
    }

    function fn_devolverAlmacen($criterio, $orden){
    	$Ssql="SELECT IdAlmacen, Almacen, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Lo_Almacen;";
		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		if (!empty($orden)) {
			$Ssql= $Ssql." ORDER BY ".$orden;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
    }

    function fn_devolverFecha(){
    	return getSQLResultSet("SELECT curdate();");
    }
     function fn_devolverHash(){
    	return getSQLResultSet("SELECT unix_timestamp();");
    }

    function fn_devolverPuntodeVenta($criterio, $orden){
    	$Ssql="SELECT IdDocVentaPuntoVenta, PuntoVenta, SerieDocVenta, SerieImpresora, RutaImpresora, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Ve_DocVentaPuntoVenta;";
    	return getSQLResultSet($Ssql);
    }

   /* function fn_devolverPuntodeVenta($criterio, $orden){
    	$Ssql="SELECT IdDocVentaPuntoVenta, PuntoVenta, SerieDocVenta, SerieImpresora, RutaImpresora, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Ve_DocVentaPuntoVenta;";
    	return getSQLResultSet($Ssql);
    }*/
    function fn_devolverCliente($criterio, $orden){
    	$Ssql = "SELECT IdCliente, Cliente, DniRuc, Direccion, Telefono, Email, Anulado, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Ve_DocVentaCliente;";
    	return getSQLResultSet($Ssql);
    }

		function fn_devolverMetPago()
		{
			$Ssql = "SELECT IdMetodoPago, MetodoPago, EsTarjeta FROM Ve_DocVentaMetodoPago;";
    	return getSQLResultSet($Ssql);
		}
	function fn_devolverSintomas($criterio){
		$Ssql = "CALL SbVe_ExpertoSintomaBuscar ('$criterio');";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverDiagnostico(){
		$Ssql = "SELECT IdDiagnostico, Diagnostico, Problema, Edad, Observacion, FechaReg, UsuarioReg, FechaMod, UsuarioMod FROM Ve_ExpertoDiagnostico;";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverDiagnosticoSintoma($edad, $criterio, $tamanio){
		$Ssql="CALL SbVe_ExpertoDiagnosticoXSintomaBuscar ($edad, $criterio, $tamanio);";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverProductosXCompuesto($IdProductoCompuesto, $ProductoCompuesto){
		$Ssql="CALL SbVe_ListarProductoXCompuesto($IdProductoCompuesto, '$ProductoCompuesto');";
		return getSQLResultSet($Ssql);
	}
	function fn_devolverCompuestosXProducto($IdProducto){
		$Ssql="CALL SbVe_ListarCompuestoXProducto($IdProducto);";
		return getSQLResultSet($Ssql);
	}
	function fn_devolverDiagnosticoXTratamiento($Diagnostico, $edad){
		$Ssql="CALL SbVe_BuscarTratamiento('$Diagnostico', $edad);";
		return getSQLResultSet($Ssql);
	}
	function fn_DevolverProductoDet($producto){
		$Ssql="CALL SbGen_ListarProductoDet($producto);";
		return getSQLResultSet($Ssql);
	}
	function fn_DevolverProveedor(){
		$Ssql="CALL SbLo_ListarProveedor();";
		return getSQLResultSet($Ssql);
	}

	function fn_DevolverTipoMovimiento(){
		$Ssql="CALL SbLo_ListarMovimiento();";
		return getSQLResultSet($Ssql);
	}

	function fn_ListarProductoVenta($almacen){
		$Ssql = "CALL SbVe_ProductoSeleccionar('$almacen');";
		return getSQLResultSet($Ssql);
	}

	function ListarReporteStock($almacen, $producto)
	{
		$Ssql = "CALL SbLo_Stock('$almacen', '$producto');";
		return getSQLResultSet($Ssql);
	}

	function ListarProductoInv(){
		$Ssql = "call Sb_ListarProductoInv();";
		return getSQLResultSet($Ssql);

	}

	function ListarReporteKardex($producto, $fechaIni, $fechaFin, $Tipo)
	{
		$Ssql = "call SbLo_Kardex('$producto', '$fechaIni', '$fechaFin', $Tipo);";
		return getSQLResultSet($Ssql);
	}

	function ListarRegVenta($fechaIni, $fechaFin, $declarado)
	{
		$Ssql = "call SbVe_RegDocVenta($declarado, '$fechaIni', '$fechaFin');";
		//echo $Ssql;
		//exit();
		return getSQLResultSet($Ssql);
	}
	function ListarRegNov($fechaIni, $fechaFin, $declarado)
	{
		$Ssql = "call SbLo_RegMovimiento($declarado, '$fechaIni', '$fechaFin');";
		//echo $Ssql;
		//exit();
		return getSQLResultSet($Ssql);
	}
	function VerificarMovimiento($MovimientoTipo, $Proveedor, $Serie, $Numero)
	{
		$Ssql = "call Sb_VerificarMovimiento('$MovimientoTipo', '$Proveedor', '$Serie', $Numero);";
		//echo $Ssql;
		//exit();
		return getSQLResultSet($Ssql);
	}

	function ListarRegCompraContable($periodoT, $declarado)
	{
		$Ssql = "call SbLo_RegCompraContable($declarado, $periodoT);";
		//echo $Ssql;
		//exit();
		return getSQLResultSet($Ssql);
	}
	function devolverKardexValorizado($producto, $anno, $stock, $precio, $Tipo){
		$Ssql = "call SbLo_StockValoriado('$producto', $stock, $precio, $anno, $Tipo);";
		//echo $Ssql;
		//exit();
		return getSQLResultSet($Ssql);
		/*if(ejecutarSQLCommand($Ssql)){
			return getSQLResultSet("select * from tblKardexvalor");
		}else{
			return false;
		}*/
	}

	function ListarBloque(){
		$Ssql = "call SbGen_ListarProductoBloque();";
		//echo $Ssql;
		//exit();
		return getSQLResultSet($Ssql);
	}

	function devolverProductosRegVenta($idDocVenta)
	{
		$Ssql = " call Sb_ListarProductosRegVenta($idDocVenta);";
		return getSQLResultSet($Ssql);
	}

	function devolverProductosRegMov($idMov)
	{
		$Ssql = " call Sb_ListarProductosRegMov('$idMov');";
		return getSQLResultSet($Ssql);
	}

	function devolverTratamientoXDiagnostico($diagnostico)
	{
		$Ssql = " call Sb_ListarTratamientoXDiagnostico($diagnostico);";
		return getSQLResultSet($Ssql);
	}

	function devolverCompuestoXDiagnostico($diagnostico)
	{
		$Ssql = " call SbVe_ListarCompuestoXDiagnostico($diagnostico);";
		return getSQLResultSet($Ssql);
	}

	function ListarUsuarioPerfil()
	{
		$Ssql = " call SbSeg_ListarUsuarioPerfil();";
		return getSQLResultSet($Ssql);
	}
	function ListarUsuario()
	{
		$Ssql = " SELECT * FROM Seg_Usuario";
		return getSQLResultSet($Ssql);
	}

	function ListarPerfil()
	{
		$Ssql = " SELECT * FROM Seg_UsuarioPerfil";
		return getSQLResultSet($Ssql);
	}

	function ListarCierre()
	{
		$Ssql = " call SbVe_ListarCierre();";
		return getSQLResultSet($Ssql);
	}
	function ListarCuenta()
	{
		$Ssql = " call SbCb_ListarCuenta();";
		return getSQLResultSet($Ssql);
	}
	function ListarTipoOpe()
	{
		$Ssql = " call SbCb_ListarTipoOpe();";
		return getSQLResultSet($Ssql);
	}

  // EstadoCuentaDet
	function BuscarEstadoCuentaDet($Cliente, $TipoOpe)
	{
		$Ssql = " call Sb_BuscarDeudor('$Cliente', $TipoOpe);";
		return getSQLResultSet($Ssql);
	}

	// VentaForm obtener Lote y fecha de vencimiento proximos a vencer
	function ListarLoteFechaVencimiento()
	{
		$Ssql = "SELECT * FROM Lo_MovimientoDetalle group by IdProducto ORDER BY FechaVen ASC;";
		return getSQLResultSet($Ssql);
	}

	function isLogin($user = '', $pass = '')
	{
		$Ssql = "SELECT * FROM Seg_Usuario WHERE Usuario='$user' AND Password='$pass'";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverListaProductosPorBloque($bloque, $porcentaje) {
		$Ssql = " call Sb_ListaDeProductosXBloque('$bloque', $porcentaje);";
		return getSQLResultSet($Ssql);
	}

 ?>
