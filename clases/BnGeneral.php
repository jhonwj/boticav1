<?php
	include_once($_SERVER["DOCUMENT_ROOT"] . '/views/validateUser.php');
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
Ve_DocVentaPuntoVenta.RutaImpresora,
Ve_DocVentaTipoDoc.CodSunat,
Ve_DocVentaTipoDoc.TieneIgv,
Ve_DocVentaTipoDoc.LimiteItems
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
    function fn_devolverProducto($criterio, $orden, $serverSide = false){

		$Ssql="SELECT
					SQL_CALC_FOUND_ROWS
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
		if ($serverSide) {
			
			$serverSideQuery = generateDatatableServerSideQuery(
				'Gen_Producto.IdProducto',
				array(
					'Gen_Producto.IdProducto',
					'Gen_Producto.IdProductoMarca',
					'Gen_ProductoMarca.ProductoMarca',
					'Gen_Producto.IdProductoFormaFarmaceutica',
					'Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica',
					'Gen_Producto.IdProductoMedicion',
					'Gen_ProductoMedicion.ProductoMedicion',
					'Gen_Producto.IdProductoCategoria',
					'Gen_ProductoCategoria.ProductoCategoria',
					'Gen_Producto.Producto',
					'Gen_Producto.ProductoDesc',
					'Gen_Producto.ProductoDescCorto',
					'Gen_Producto.CodigoBarra',
					'Gen_Producto.Codigo',
					'Gen_Producto.Dosis',
					'Gen_Producto.PrecioContado',
					'Gen_Producto.PrecioPorMayor',
					'Gen_Producto.StockPorMayor',
					'Gen_Producto.StockMinimo',
					'Gen_Producto.ControlaStock',
					'Gen_Producto.Anulado',
					'Gen_Producto.FechaReg',
					'Gen_Producto.UsuarioReg',
					'Gen_Producto.FechaMod',
					'Gen_Producto.UsuarioMod',
					'Gen_Producto.Dosis',
					'Gen_ProductoBloque.Bloque',
					'Gen_Producto.VentaEstrategica',
					'Gen_Producto.PrecioCosto',
					'Gen_Producto.PorcentajeUtilidad'),
					'Gen_Producto',
					$Ssql
			);
			return $serverSideQuery;
			
		}else {
			if (!empty($criterio)) {
				$Ssql= $Ssql." WHERE ".$criterio;
			}
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
    	$Ssql="SELECT IdTipoDoc, TipoDoc, VaRegVenta, CodSunat, TieneIgv, LimiteItems FROM Ve_DocVentaTipoDoc";
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

	function ListarReporteStock($almacen, $producto, $serverSide = false, $proveedor = false, $menorStock = false)
	{
		$Ssql = "Select prodstock.IdProducto as numero, ProductoMarca as marca,ProductoCategoria as categoria,FormaFarmaceutica as formafarmaceutica, prodstock.Producto as Producto,Stock as stock ,
			Gen_Producto.PrecioContado,	Gen_Producto.PrecioPorMayor, Gen_Producto.StockPorMayor, Gen_Producto.Codigo, Gen_Producto.VentaEstrategica, Gen_ProductoMedicion.ProductoMedicion, Gen_Producto.CodigoBarra, Gen_Producto.StockMinimo, Gen_Producto.controlaStock,
			(SELECT Lo_MovimientoDetalle.Precio FROM Lo_MovimientoDetalle WHERE IdProducto = prodstock.IdProducto ORDER BY hashMovimiento DESC LIMIT 1) as MovimientoPrecio,
			(SELECT Lo_MovimientoDetalle.Cantidad FROM Lo_MovimientoDetalle WHERE IdProducto = prodstock.IdProducto ORDER BY hashMovimiento DESC LIMIT 1) as MovimientoCantidad,
			((SELECT Lo_MovimientoDetalle.Precio FROM Lo_MovimientoDetalle WHERE IdProducto = prodstock.IdProducto ORDER BY hashMovimiento DESC LIMIT 1) * (SELECT Lo_MovimientoDetalle.Cantidad FROM Lo_MovimientoDetalle WHERE IdProducto = prodstock.IdProducto ORDER BY hashMovimiento DESC LIMIT 1)) as MovimientoTotal,
			(SELECT Lo_Movimiento.IdProveedor FROM Lo_Movimiento WHERE Lo_Movimiento.Hash = (
				SELECT hashMovimiento FROM Lo_MovimientoDetalle
				WHERE IdProducto = prodstock.IdProducto
				ORDER BY hashMovimiento DESC
				LIMIT 1)) as IdProveedor
			FROM prodstock
			INNER JOIN Gen_Producto ON Gen_Producto.IdProducto = prodstock.IdProducto
			INNER JOIN Gen_ProductoMedicion ON Gen_ProductoMedicion.IdProductoMedicion = Gen_Producto.IdProductoMedicion ";
			

		if ($proveedor) {
			$Ssql .= "WHERE (SELECT Lo_Movimiento.IdProveedor FROM Lo_Movimiento WHERE Lo_Movimiento.Hash = (
				SELECT hashMovimiento FROM Lo_MovimientoDetalle
				WHERE IdProducto = prodstock.IdProducto
				ORDER BY hashMovimiento DESC
				LIMIT 1))=$proveedor ";
			if($menorStock) {
				$Ssql .= " AND Stock <= Gen_Producto.StockMinimo";
			}
			return getSQLResultSet($Ssql);				
		}
		if ($serverSide) {

			$serverSideQuery = generateDatatableServerSideQuery(
				'prodstock.IdProducto',
				array('prodstock.IdProducto', 'ProductoMarca', 'ProductoCategoria', 'FormaFarmaceutica', 'prodstock.Producto', 'Stock', 
					'Gen_Producto.PrecioContado', 'Gen_Producto.PrecioPorMayor', 'Gen_Producto.StockPorMayor', 'Gen_Producto.Codigo', 'Gen_Producto.VentaEstrategica', 'Gen_ProductoMedicion.ProductoMedicion', 'Gen_Producto.CodigoBarra'),
				'prodstock',	
				$Ssql
			);

			return $serverSideQuery;
		} else {
			$Ssql = "CALL SbLo_Stock('$almacen', '$producto');";
			return getSQLResultSet($Ssql);
		}
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
		//$Ssql = " call Sb_ListarProductosRegVenta($idDocVenta);";
		$Ssql = "SELECT Gen_Producto.IdProducto, Gen_Producto.Producto, Gen_Producto.Codigo, Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica, Gen_ProductoMedicion.ProductoMedicion, Ve_DocVentaDet.IdDocVenta, Ve_DocVentaDet.Cantidad, Ve_DocVentaDet.Precio, Gen_ProductoMarca.ProductoMarca
		FROM Gen_Producto
		INNER JOIN Ve_DocVentaDet ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto
		INNER JOIN Gen_ProductoFormaFarmaceutica ON Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica = Gen_Producto.IdProductoFormaFarmaceutica
		INNER JOIN Gen_ProductoMedicion ON Gen_ProductoMedicion.IdProductoMedicion = Gen_Producto.IdProductoMedicion
		INNER JOIN Gen_ProductoMarca ON Gen_ProductoMarca.IdProductoMarca = Gen_Producto.IdProductoMarca
		WHERE Ve_DocVentaDet.IdDocVenta = $idDocVenta;";

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
		$Ssql = " SELECT Seg_Usuario.Usuario, Seg_UsuarioPerfil.UsuarioPerfil, Seg_Usuario.IdUsuarioPerfil, Seg_Usuario.Password, Seg_Usuario.NombreUsuario
			FROM Seg_Usuario
			INNER JOIN Seg_UsuarioPerfil ON Seg_Usuario.IdUsuarioPerfil = Seg_UsuarioPerfil.IdUsuarioPerfil;";
		return getSQLResultSet($Ssql);
	}

	function ListarUsuarioPerfilModulo($idUsuarioPerfil)
	{
		$Ssql = " SELECT Seg_UsuarioModulo.IdUsuarioModulo, Seg_UsuarioModulo.UsuarioModulo, Seg_UsuarioModulo_has_UsuarioPerfil.IdUsuarioPerfil, Seg_UsuarioModulo_has_UsuarioPerfil.Lectura, Seg_UsuarioModulo_has_UsuarioPerfil.Escritura FROM Seg_UsuarioModulo_has_UsuarioPerfil
			INNER JOIN Seg_UsuarioModulo ON Seg_UsuarioModulo_has_UsuarioPerfil.IdUsuarioModulo = Seg_UsuarioModulo.IdUsuarioModulo
			WHERE Seg_UsuarioModulo_has_UsuarioPerfil.IdUsuarioPerfil = $idUsuarioPerfil";
			return getSQLResultSet($Ssql);

	}
	function ListarUsuario()
	{
		$Ssql = " SELECT * FROM Seg_Usuario";
		return getSQLResultSet($Ssql);
	}

	function ListarUsuarioModulo() {
		$Ssql = " SELECT * FROM Seg_UsuarioModulo";
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



	function fn_devolverListaProductosPorBloque($bloque, $porcentaje) {
		$Ssql = " call Sb_ListaDeProductosXBloque('$bloque', $porcentaje);";
		return getSQLResultSet($Ssql);
	}

/* Pre orden */
	function  fn_listarPreOrden() {
		$Ssql = " call SbVe_ListarPreOrden();";
		return getSQLResultSet($Ssql);
	}

	function fn_listarProductosPreOrden($idPreOrden) {
		$Ssql = "SELECT PRO.IdProducto, PRO.Producto, POD.Cantidad, PRO.PrecioContado AS Precio,
			(SELECT MD.IdLote FROM Lo_MovimientoDetalle as MD WHERE MD.IdProducto=PRO.IdProducto group by IdProducto ORDER BY FechaVen ASC) AS Lote,
			(SELECT MD.FechaVen FROM Lo_MovimientoDetalle as MD WHERE MD.IdProducto=PRO.IdProducto group by IdProducto ORDER BY FechaVen ASC) AS FechaVen
			FROM Ve_PreOrdenDet AS POD INNER JOIN Gen_Producto AS PRO ON POD.IdProducto = PRO.IdProducto
			WHERE POD.IdPreOrden = $idPreOrden";
		return getSQLResultSet($Ssql);
	}

	function fn_listarReporteUtilidadBruta($fechaIni, $fechaFin){
		$Ssql = " call SbVe_ReporteUtilidadBruta('$fechaIni', '$fechaFin');";
		return getSQLResultSet($Ssql);
	}


	function fn_devolverPuntoVentaSerie($idPuntoVenta, $idTipoDoc) {
		$Ssql = " SELECT * FROM Ve_DocVentaPuntoVentaDet WHERE IdDocVentaPuntoVenta=$idPuntoVenta AND IdDocVentaTipoDoc=$idTipoDoc";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverMonedas() {
		$Ssql = " SELECT * FROM Gen_Moneda";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverVentas() {
		$Ssql = " SELECT DV.idDocVenta, DV.FechaDoc, DVC.DniRuc, DVTD.TipoDoc, DVC.Cliente,
				(SELECT SUM(DVD.Cantidad * DVD.Precio) FROM Ve_DocVentaDet as DVD WHERE DVD.IdDocVenta = DV.idDocVenta) as Total
			FROM Ve_DocVenta AS DV
			INNER JOIN Ve_DocVentaCliente AS DVC ON DV.IdCliente = DVC.IdCliente
			INNER JOIN Ve_DocVentaTipoDoc AS DVTD ON DV.IdTipoDoc = DVTD.IdTipoDoc
			ORDER BY DV.FechaDoc DESC";
		return getSQLResultSet($Ssql);
	}

	function ejecutarStockCursor($almacen, $producto) {
		$Ssql = " call SbLo_Stock_Cursor('$almacen', '$producto');";
		return getSQLResultSet($Ssql);
	}

	/* SERVER SIDE DATATABLE */
	function datatableStringLimit() {
		$mysqli = getMysqliLink();
		
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".$mysqli->real_escape_string( $_GET['iDisplayStart'] ).", ".
				$mysqli->real_escape_string( $_GET['iDisplayLength'] );
		}

		return $sLimit;
	}

	function datatableStringOrder($aColumns) {
		$mysqli = getMysqliLink();

		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
						".$mysqli->real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
			return $sOrder;
		}
	}

	function datatableStringSearch($aColumns) {
		$mysqli = getMysqliLink();		
		$sWhere = "";
		if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$sWhere .= $aColumns[$i]." LIKE '%".$mysqli->real_escape_string( $_GET['sSearch'] )."%' OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		return $sWhere;
	}

	function generateDatatableServerSideQuery($sIndexColumn, $aColumns, $sTable, $sql) {
		$sWhere = datatableStringSearch($aColumns);
		$sOrder = datatableStringOrder($aColumns);
		$sLimit = datatableStringLimit();

		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) && $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
			}
		}

		$Ssql =  $sql . "
			$sWhere 
			$sOrder 
			$sLimit";
		
		$result = getSQLResultSet($Ssql);
		$sQuery = " SELECT FOUND_ROWS()";
		$rResultFilterTotal = getSQLResultSet($sQuery);
		$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
		$iFilteredTotal = $aResultFilterTotal[0];
		//var_dump($aResultFilterTotal);exit();

		$sQuery = "
			SELECT COUNT(".$sIndexColumn.")
			FROM   $sTable
		";
		$rResultTotal = getSQLResultSet($sQuery);
		$aResultTotal = mysqli_fetch_array($rResultTotal);
		$iTotal = $aResultTotal[0];
			
		return array(
			'aaData' => $result,
			'iTotalRecords' => $iTotal,
			'iTotalDisplayRecords' => $iFilteredTotal
		);
	}

	function devolverNumeroSiguienteMovimiento($serie) {
		$Ssql = "SELECT Lo_Movimiento.IdMovimientoTipo, Lo_MovimientoTipo.Tipo, Lo_Movimiento.Serie, Lo_Movimiento.Numero, (Lo_Movimiento.Numero+1) as NuevoNumero
		FROM Lo_Movimiento
		INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
		WHERE Lo_Movimiento.Serie = '$serie' AND (Lo_MovimientoTipo.Tipo = 1 OR Lo_MovimientoTipo.Tipo = 2)
		ORDER BY Lo_Movimiento.MovimientoFecha DESC
		LIMIT 1";
		return getSQLResultSet($Ssql);
		
	}


	function fn_devolverMovimiento($hash) {
		$Ssql = "SELECT Lo_Movimiento.*, Ve_DocVenta.Serie AS DocVentaSerie, Ve_DocVenta.Numero as DocVentaNumero FROM Lo_Movimiento 
		LEFT JOIN Ve_DocVenta ON Lo_Movimiento.IdDocVenta = Ve_DocVenta.idDocVenta
		WHERE Lo_Movimiento.Hash='$hash';";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverMovimientoDet($hash) {
		$Ssql = "SELECT Lo_MovimientoDetalle.*, Gen_Producto.Producto, Gen_ProductoMedicion.ProductoMedicion FROM Lo_MovimientoDetalle
			INNER JOIN Gen_Producto ON Lo_MovimientoDetalle.IdProducto = Gen_Producto.IdProducto
			INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
			WHERE hashMovimiento='$hash';";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverCajaBancoPorFecha($cuenta, $tipo, $fechaIni, $fechaFin) {
		$Ssql = "SELECT IdCajaBanco, FechaDoc, Lo_Proveedor.Proveedor, Ve_DocVentaCliente.Cliente, Concepto, Importe 
			FROM Cb_CajaBanco
			LEFT JOIN Lo_Proveedor ON Cb_CajaBanco.IdProveedor = Lo_Proveedor.IdProveedor
			LEFT JOIN Ve_DocVentaCliente ON Cb_CajaBanco.IdCliente = Ve_DocVentaCliente.IdCliente
			WHERE IdCuenta=$cuenta AND IdTipoCajaBanco=$tipo AND FechaDoc BETWEEN '$fechaIni' AND '$fechaFin'";
		return getSQLResultSet($Ssql);
	}

 ?>
