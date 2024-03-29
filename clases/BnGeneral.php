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

	function fn_devolverProforma($idProforma) {
		$Ssql = "SELECT Ve_Proforma.*, Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.Direccion, 
		Ve_DocVentaCliente.Email AS ClienteEmail, Ve_DocVentaCliente.DniRuc
			FROM Ve_Proforma
			INNER JOIN Ve_DocVentaCliente ON Ve_Proforma.IdCliente = Ve_DocVentaCliente.IdCliente
			WHERE Ve_Proforma.IdProforma = '" . $idProforma . "' ";

		return getSQLResultSet($Ssql);

	}
	//Detalle con unidad de medicion
	function fn_devolverProformaDet($idProforma) {
		$Ssql = "SELECT Ve_ProformaDet.IdProforma, Ve_ProformaDet.Cantidad, Ve_ProformaDet.Descripcion,
		Ve_ProformaDet.Precio, Ve_ProformaDet.Descuento, Gen_Producto.*,Gen_ProductoMedicion.ProductoMedicion,
		Round(Ve_ProformaDet.Cantidad*Ve_ProformaDet.Precio,2) as TOTAL
		FROM Ve_ProformaDet
		INNER JOIN Gen_Producto ON Ve_ProformaDet.IdProducto = Gen_Producto.IdProducto
		INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion=Gen_ProductoMedicion.IdProductoMedicion
		WHERE Ve_ProformaDet.IdProforma = '" . $idProforma . "'";

		return getSQLResultSet($Ssql);
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
Ve_DocVentaCliente.Direccion2,
Ve_DocVentaCliente.Direccion3,
Ve_DocVentaCliente.Puntos,
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
Ve_DocVenta.PagoCon,
Ve_DocVenta.CampoDireccion,
Ve_DocVenta.EsCredito,
Ve_DocVenta.FechaCredito,
Ve_DocVenta.CodSunatModifica,
Ve_DocVenta.NroComprobanteModifica,
Ve_DocVenta.NotaIdMotivo,
Ve_DocVenta.NotaDescMotivo,
Ve_DocVenta.NombreOrganizacion,
Ve_DocVenta.EsOrganizacion,
Ve_DocVentaPuntoVenta.SerieImpresora,
Ve_DocVentaPuntoVenta.RutaImpresora,
Ve_DocVentaTipoDoc.CodSunat,
Ve_DocVentaTipoDoc.TieneIgv,
Ve_DocVentaTipoDoc.LimiteItems,
MetodoDetalle.EfectivoDesc,
MetodoDetalle.VisaDesc,
MetodoDetalle.MastercardDesc,
MetodoDetalle.Efectivo,
MetodoDetalle.Visa,
MetodoDetalle.Mastercard,
IFNULL(Viaje.Nombre,'') AS Nombre,
IFNULL(Viaje.FechaViaje,'') AS FechaViaje,
IFNULL(Viaje.CiudadDestino,'') AS CiudadDestino,
IFNULL(Viaje.CiudadOrigen,'') AS CiudadOrigen,
IFNULL(Viaje.HoraViajeFormat,'') AS HoraViajeFormat,
IFNULL(Viaje.IdAsiento,'') AS IdAsiento
FROM
Ve_DocVenta
INNER JOIN Ve_DocVentaPuntoVenta ON Ve_DocVenta.IdDocVentaPuntoVenta = Ve_DocVentaPuntoVenta.IdDocVentaPuntoVenta
INNER JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
INNER JOIN Lo_Almacen ON Ve_DocVenta.IdAlmacen = Lo_Almacen.IdAlmacen
LEFT JOIN (
	SELECT Ve_DocVentaMetodoPagoDet.IdDocVenta, 
	IFNULL(IF(Ve_DocVentaMetodoPagoDet.IdMetodoPago = 1,Ve_DocVentaMetodoPagoDet.NroTarjeta,''),'') AS EfectivoDesc,
	IFNULL(IF(Ve_DocVentaMetodoPagoDet.IdMetodoPago = 2,Ve_DocVentaMetodoPagoDet.NroTarjeta,''),'') AS VisaDesc,
	IFNULL(IF(Ve_DocVentaMetodoPagoDet.IdMetodoPago = 3,Ve_DocVentaMetodoPagoDet.NroTarjeta,''),'') AS MastercardDesc,
	IFNULL(SUM(IF(Ve_DocVentaMetodoPagoDet.IdMetodoPago = 1,Ve_DocVentaMetodoPagoDet.Importe,0)),0) AS Efectivo,
	IFNULL(SUM(IF(Ve_DocVentaMetodoPagoDet.IdMetodoPago = 2,Ve_DocVentaMetodoPagoDet.Importe,0)),0) AS Visa,
	IFNULL(SUM(IF(Ve_DocVentaMetodoPagoDet.IdMetodoPago = 3,Ve_DocVentaMetodoPagoDet.Importe,0)),0) AS Mastercard
	FROM Ve_DocVentaMetodoPagoDet
	GROUP BY Ve_DocVentaMetodoPagoDet.IdDocVenta
) AS MetodoDetalle ON  MetodoDetalle.IdDocVenta = Ve_DocVenta.idDocVenta
LEFT JOIN (
	SELECT va.IdDocVenta, vi.Nombre, va.IdAsiento, vi.FechaViaje, co.Nombre AS CiudadOrigen, cd.Nombre AS CiudadDestino,TIME_FORMAT(vi.HoraViaje, '%h:%i %p') AS HoraViajeFormat FROM Tr_Viaje vi
	INNER JOIN Tr_Ciudad co ON vi.IdOrigen = co.IdCiudad
	INNER JOIN Tr_Ciudad cd ON vi.IdDestino = cd.IdCiudad
	LEFT JOIN Tr_VehiculoAsiento va ON vi.IdViaje = va.IdViaje
	GROUP BY va.IdDocVenta
) AS Viaje ON Viaje.IdDocVenta = Ve_DocVenta.idDocVenta";
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
Ve_DocVentaDet.FechaAlquilerInicio,
Ve_DocVentaDet.FechaAlquilerFin,
Ve_DocVentaDet.Descripcion,
Ve_DocVentaDet.Descuento,
Ve_DocVentaDet.EsManoDeObra,
Gen_Producto.ProductoDesc,
Gen_Producto.ProductoDesc2,
Gen_Producto.ProductoDesc3,
Gen_Producto.Producto,
Gen_Producto.CodigoBarra,
Ve_DocVentaDet.Cantidad,
Ve_DocVentaDet.Precio,
Gen_ProductoMedicion.ProductoMedicion,
Round(Ve_DocVentaDet.Cantidad*
Ve_DocVentaDet.Precio,2) as TOTAL
FROM
Ve_DocVentaDet
INNER JOIN Gen_Producto ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto 
INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion=Gen_ProductoMedicion.IdProductoMedicion";
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
	function fn_devolverDocVentaDetEntrega($criterio){

		$Ssql = "SELECT 
		vDonVenClie.Cliente AS Cliente,
		vDonVenClie.DniRuc AS DniRuc,
		vDocVenDetEnt.Fecha AS Fecha 
		FROM Ve_DocVenta AS vDocVen INNER JOIN Ve_DocVentaCliente AS vDonVenClie 
		ON vDonVenClie.IdCliente = vDocVen.IdCliente INNER JOIN Ve_DocVentaDet AS vDocVenDet 
		ON vDocVen.idDocVenta = vDocVenDet.IdDocVenta INNER JOIN Ve_DocVentaDetEntrega AS vDocVenDetEnt 
		ON vDocVenDetEnt.IdDocVentaDet = vDocVenDet.IdDocVentaDet";

		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
		}
		//echo $Ssql;
		return getSQLResultSet($Ssql);
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";

	}
	function fn_devolverDocVentaDetEntregaDet($criterio){

		$Ssql = "SELECT 
		vDocVenDetEnt.Cantidad AS Cantidad, 
		gProd.Producto AS Producto, 
		vDocVenDet.Precio AS Precio,
		vDocVenDetEnt.Fecha AS Fecha 
		FROM Ve_DocVentaDetEntrega AS vDocVenDetEnt INNER JOIN Ve_DocVentaDet AS vDocVenDet 
		ON vDocVenDetEnt.IdDocVentaDet = vDocVenDet.IdDocVentaDet INNER JOIN Gen_Producto AS gProd 
		ON gProd.IdProducto = vDocVenDet.IdProducto INNER JOIN Ve_DocVenta AS vDocVen 
		ON vDocVenDet.IdDocVenta = vDocVen.idDocVenta INNER JOIN Ve_DocVentaCliente AS vDonVenClie 
		ON vDocVen.IdCliente = vDonVenClie.IdCliente";

		//echo $Ssql;
		if (!empty($criterio)) {
			$Ssql= $Ssql." WHERE ".$criterio;
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
		if(isset($_GET['ultimaCompra'])) {
			$Ssql = "Select Gen_Producto.IdProducto as numero, prodstock.ProductoMarca as marca,ProductoCategoria as categoria,prodstock.FormaFarmaceutica as formafarmaceutica, Gen_Producto.Producto as Producto,Stock as stock ,
			Gen_Producto.PrecioContado,	Gen_Producto.PrecioPorMayor, Gen_Producto.StockPorMayor, Gen_Producto.Codigo, Gen_Producto.VentaEstrategica, Gen_ProductoMedicion.ProductoMedicion, Gen_Producto.CodigoBarra, Gen_Producto.StockMinimo, Gen_Producto.controlaStock,
			ROUND((SELECT Lo_MovimientoDetalle.Precio FROM Lo_MovimientoDetalle WHERE IdProducto = Gen_Producto.IdProducto ORDER BY hashMovimiento DESC LIMIT 1), 2) as MovimientoPrecio,
			(SELECT Lo_MovimientoDetalle.Cantidad FROM Lo_MovimientoDetalle WHERE IdProducto = Gen_Producto.IdProducto ORDER BY hashMovimiento DESC LIMIT 1) as MovimientoCantidad,
			ROUND((ROUND((SELECT Lo_MovimientoDetalle.Precio FROM Lo_MovimientoDetalle WHERE IdProducto = Gen_Producto.IdProducto ORDER BY hashMovimiento DESC LIMIT 1), 2) * (SELECT Lo_MovimientoDetalle.Cantidad FROM Lo_MovimientoDetalle WHERE IdProducto = Gen_Producto.IdProducto ORDER BY hashMovimiento DESC LIMIT 1)), 2) as MovimientoTotal,
			(SELECT Lo_Movimiento.IdProveedor FROM Lo_Movimiento WHERE Lo_Movimiento.Hash = (
				SELECT hashMovimiento FROM Lo_MovimientoDetalle
				WHERE IdProducto = Gen_Producto.IdProducto
				ORDER BY hashMovimiento DESC
				LIMIT 1)) as IdProveedor
			FROM Gen_Producto
			LEFT JOIN prodstock ON Gen_Producto.IdProducto = prodstock.IdProducto
			INNER JOIN Gen_ProductoMedicion ON Gen_ProductoMedicion.IdProductoMedicion = Gen_Producto.IdProductoMedicion ";
		} else {
			$Ssql = "Select Gen_Producto.IdProducto as numero, prodstock.ProductoMarca as marca,ProductoCategoria as categoria,prodstock.FormaFarmaceutica as formafarmaceutica, Gen_Producto.Producto as Producto,Stock as stock ,
			Gen_Producto.PrecioContado,	Gen_Producto.PrecioPorMayor, Gen_Producto.StockPorMayor, Gen_Producto.Codigo, Gen_Producto.VentaEstrategica, Gen_ProductoMedicion.ProductoMedicion, Gen_Producto.CodigoBarra, Gen_Producto.StockMinimo, Gen_Producto.controlaStock
			FROM Gen_Producto
			LEFT JOIN prodstock ON Gen_Producto.IdProducto = prodstock.IdProducto
			INNER JOIN Gen_ProductoMedicion ON Gen_ProductoMedicion.IdProductoMedicion = Gen_Producto.IdProductoMedicion ";
		}
		
			

		if ($proveedor) {
			$Ssql .= "WHERE (SELECT Lo_Movimiento.IdProveedor FROM Lo_Movimiento WHERE Lo_Movimiento.Hash = (
				SELECT hashMovimiento FROM Lo_MovimientoDetalle
				WHERE IdProducto = Gen_Producto.IdProducto
				ORDER BY hashMovimiento DESC
				LIMIT 1))=$proveedor ";
			if($menorStock) {
				$Ssql .= " AND Stock <= Gen_Producto.StockMinimo";
			}
			return getSQLResultSet($Ssql);				
		}
		if ($serverSide) {

			$serverSideQuery = generateDatatableServerSideQuery(
				'Gen_Producto.IdProducto',
				array('Gen_Producto.IdProducto', 'ProductoMarca', 'ProductoCategoria', 'FormaFarmaceutica', 'Gen_Producto.Producto', 'Stock', 
					'Gen_Producto.PrecioContado', 'Gen_Producto.PrecioPorMayor', 'Gen_Producto.StockPorMayor', 'Gen_Producto.Codigo', 'Gen_Producto.VentaEstrategica', 'Gen_ProductoMedicion.ProductoMedicion', 'Gen_Producto.CodigoBarra'),
				'Gen_Producto',	
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

	function ListarRegVenta($fechaIni, $fechaFin, $declarado, $almacen)
	{
		// $Ssql = "call SbVe_RegDocVenta($declarado, '$fechaIni', '$fechaFin');";
		$Ssql = "SELECT Ve_DocVenta.idDocVenta, Ve_DocVenta.UsuarioReg, Ve_DocVenta.FechaDoc,
		Ve_DocVentaTipoDoc.CodSunat, Ve_DocVentaTipoDoc.TipoDoc, Ve_DocVenta.Anulado,
	   Ve_DocVenta.Serie, Ve_DocVenta.Numero, IFNULL((Select Sum(Round((Ve_DocVentaDet.Precio* Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento,2)) FROM Ve_DocVentaDet Where Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.idDocVenta),0) as SubTotal,
	   IF(Ve_DocVentaTipoDoc.TieneIgv = 1,
	   ROUND((IFNULL((SELECT SUM(Round((Ve_DocVentaDet.Precio* Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento,2)) FROM Ve_DocVentaDet WHERE Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.idDocVenta),0)) -
	   (IFNULL((SELECT SUM(Round((Ve_DocVentaDet.Precio* Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento,2)) FROM Ve_DocVentaDet WHERE Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.idDocVenta),0)) / 1.18, 2)
	   , 0) as Igv,
	   IFNULL((Select Sum(Round((Ve_DocVentaDet.Precio* Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento,2)) FROM Ve_DocVentaDet Where Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.idDocVenta),0) as Total
	   FROM Ve_DocVenta INNER JOIN Ve_DocVentaTipoDoc On Ve_DocVenta.IdTipoDoc=Ve_DocVentaTipoDoc.IdTipoDoc
	   WHERE Ve_DocVentaTipoDoc.VaRegVenta=$declarado ";

	   if(!empty($almacen)) {
		   $Ssql .= " AND Ve_DocVenta.IdAlmacen = $almacen ";
	   }

	   $Ssql .= " AND Ve_DocVenta.Fechadoc BETWEEN CAST('$fechaIni' AS DATETIME) and CAST('$fechaFin' AS DATETIME)
		  ORDER BY  Ve_DocVenta.Fechadoc DESC;";
		//echo $Ssql;
		//exit();
		return getSQLResultSet($Ssql);
	}
	function ListarRegNov($fechaIni, $fechaFin, $declarado, $descripcion = "")
	{
		// $Ssql = "call SbLo_RegMovimiento($declarado, '$fechaIni', '$fechaFin', '$descripcion');";
		$Ssql ="SELECT
		Lo_Movimiento.`Hash` as IdMovimiento,
		Lo_Movimiento.MovimientoFecha,
		Lo_Movimiento.IdMovimientoTipo,
		Lo_MovimientoTipo.Tipo,
		Lo_MovimientoTipo.TipoMovimiento,
		Lo_Movimiento.Serie,
		Lo_Movimiento.Numero,
		Lo_Movimiento.FechaPeriodoTributario,
		Lo_Proveedor.Proveedor,
		CASE
			WHEN Lo_Movimiento.IdAlmacenOrigen>0 THEN (
			Select
				Lo_Almacen.Almacen
			From
				Lo_Almacen
			Where
				Lo_Almacen.IdAlmacen = Lo_Movimiento.IdAlmacenOrigen)
			ELSE '-'
		END AS AlmacenOrigen,
		CASE
			WHEN Lo_Movimiento.IdAlmacenDestino>0 THEN (
			Select
				Lo_Almacen.Almacen
			From
				Lo_Almacen
			Where
				Lo_Almacen.IdAlmacen = Lo_Movimiento.IdAlmacenDestino)
			ELSE '-'
		END AS AlmacenDestino,
		Lo_Movimiento.Observacion,
		Lo_Movimiento.Anulado,
		(
		SELECT
			Sum(ROUND(Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio, 2)) as SUBTOTAL
		FROM
			Lo_MovimientoDetalle
		WHERE
			Lo_MovimientoDetalle.hashMovimiento = Lo_Movimiento.`Hash`) as SUBTOTAL,
		(
		SELECT
			Sum(CASE WHEN Lo_MovimientoDetalle.TieneIgv = 1 THEN ROUND((Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio)* (Select Igv From GEN_EMPRESA), 2) ELSE 0 END) as IGV
		FROM
			Lo_MovimientoDetalle
		WHERE
			Lo_MovimientoDetalle.hashMovimiento = Lo_Movimiento.`Hash`) as IGV,
		(
		SELECT
			Sum(ROUND(Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio, 2)) + Sum(ROUND(CASE WHEN ISNULL(Lo_MovimientoDetalle.ISC)= 1 THEN 0 ELSE Lo_MovimientoDetalle.ISC END, 2)) + SUM(ROUND(CASE WHEN ISNULL(Lo_MovimientoDetalle.FLETE)= 1 THEN 0 ELSE Lo_MovimientoDetalle.FLETE END, 2))
			+ CASE
				WHEN ISNULL(Lo_Movimiento.Percepcion)= 1 THEN 0
				ELSE Lo_Movimiento.Percepcion
			END + Sum(CASE WHEN Lo_MovimientoDetalle.TieneIgv = 1 THEN ROUND((Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio)* (Select Igv From GEN_EMPRESA), 2) ELSE 0 END) as TOTAL
		FROM
			Lo_MovimientoDetalle
		WHERE
			Lo_MovimientoDetalle.hashMovimiento = Lo_Movimiento.`Hash`) as TOTAL,
		Lo_Movimiento.FechaReg,
		Lo_Movimiento.UsuarioReg,
		Lo_Movimiento.FechaMod,
		Lo_Movimiento.UsuarioMod,
		(
		SELECT
			SUM(ROUND(CASE WHEN ISNULL(Lo_MovimientoDetalle.ISC)= 1 THEN 0 ELSE Lo_MovimientoDetalle.ISC END, 2))
		FROM
			Lo_MovimientoDetalle
		WHERE
			Lo_MovimientoDetalle.hashMovimiento = Lo_Movimiento.`Hash`) as ISC,
		(
		SELECT
			SUM(ROUND(CASE WHEN ISNULL(Lo_MovimientoDetalle.FLETE)= 1 THEN 0 ELSE Lo_MovimientoDetalle.FLETE END, 2))
		FROM
			Lo_MovimientoDetalle
		WHERE
			Lo_MovimientoDetalle.hashMovimiento = Lo_Movimiento.`Hash`) as FLETE,
		CASE
			WHEN ISNULL(Lo_Movimiento.Percepcion)= 1 THEN 0
			ELSE Lo_Movimiento.Percepcion
		END AS Percepcion,
		Lo_Movimiento.TipoCambio,
		Lo_Movimiento.Moneda
	FROM
		Lo_Movimiento
	INNER JOIN Lo_MovimientoTipo On
		Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
	INNER JOIN Lo_Proveedor On
		Lo_Movimiento.IdProveedor = Lo_Proveedor.IdProveedor
	WHERE
		Lo_MovimientoTipo.VaRegCompra = $declarado
		AND Lo_Movimiento.MovimientoFecha BETWEEN CAST('$fechaIni' AS DATETIME) and  CAST('$fechaFin' AS DATETIME)
		AND Lo_Movimiento.Hash IN 
			(SELECT DISTINCT(Lo_MovimientoDetalle.hashMovimiento) FROM Lo_MovimientoDetalle WHERE Lo_MovimientoDetalle.Descripcion like CONCAT('%', '$descripcion', '%'))
	ORDER BY
		Lo_Movimiento.MovimientoFecha DESC;";
		//echo $Ssql;
		//exit();
		return getSQLResultSet($Ssql);
	}
	// function ListarCajaBanco2($fechaIni, $fechaFin, $declarado, $descripcion = "")
	// {
	// 	$Ssql = "call SbFrmCb_CajaBancoBuscador($declarado, '$fechaIni', '$fechaFin', '$descripcion');";
	// 	//echo $Ssql;
	// 	//exit();
	// 	return getSQLResultSet($Ssql);
	// }
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
		$Ssql = "SELECT Gen_Producto.IdProducto, Gen_Producto.Producto, Gen_Producto.Codigo, Gen_Producto.CodigoBarra, Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica, Gen_ProductoMedicion.ProductoMedicion, Ve_DocVentaDet.IdDocVenta, Ve_DocVentaDet.Cantidad, Ve_DocVentaDet.Precio, Ve_DocVentaDet.Descuento, Gen_ProductoMarca.ProductoMarca
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
		$Ssql = "Select Lo_MovimientoDetalle.hashMovimiento, Lo_MovimientoDetalle.Descripcion, Gen_Producto.Codigo, Gen_Producto.CodigoBarra, 
		Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica, Gen_Producto.Producto, 
		Gen_Producto.PrecioContado, Gen_ProductoMedicion.ProductoMedicion, Lo_MovimientoDetalle.Cantidad,
		Lo_MovimientoDetalle.TieneIgv, Lo_MovimientoDetalle.Precio, Gen_ProductoMarca.ProductoMarca, Lo_MovimientoDetalle.ISC
		   FROM Lo_MovimientoDetalle
		   INNER JOIN Gen_Producto on Gen_Producto.IdProducto = Lo_MovimientoDetalle.IdProducto
		   INNER JOIN Gen_ProductoFormaFarmaceutica ON Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica = Gen_Producto.IdProductoFormaFarmaceutica
		   INNER JOIN Gen_ProductoMedicion ON Gen_ProductoMedicion.IdProductoMedicion = Gen_Producto.IdProductoMedicion
		   INNER JOIN Gen_ProductoMarca ON Gen_ProductoMarca.IdProductoMarca = Gen_Producto.IdProductoMarca
		   WHERE Lo_MovimientoDetalle.hashMovimiento='$idMov'";
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
		$Ssql = " SELECT Seg_Usuario.Usuario, Seg_Usuario.IdTipoDoc, Seg_UsuarioPerfil.UsuarioPerfil, Seg_Usuario.IdUsuarioPerfil, Seg_Usuario.Password, Seg_Usuario.NombreUsuario
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

	function fn_devolverPreOrden($idPreOrden) {
		$sql = "SELECT Ve_PreOrden.*, Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.DniRuc FROM Ve_PreOrden 
			INNER JOIN Ve_DocVentaCliente ON Ve_PreOrden.IdCliente = Ve_DocVentaCliente.IdCliente
			WHERE IdPreOrden=$idPreOrden";
		return getSQLResultSet($sql);
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
		$Ssql = " SELECT DV.idDocVenta, DV.Serie, DV.Numero, DV.FechaDoc, DVC.DniRuc, DVTD.TipoDoc, DVC.Cliente,
				(SELECT ROUND(SUM(DVD.Cantidad * DVD.Precio) - DVD.Descuento, 2) FROM Ve_DocVentaDet as DVD WHERE DVD.IdDocVenta = DV.idDocVenta) as Total
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
		$Ssql = "SELECT Lo_Movimiento.*, Gen_Moneda.Simbolo AS Moneda, Ve_DocVenta.Serie AS DocVentaSerie, Ve_DocVenta.Numero as DocVentaNumero FROM Lo_Movimiento 
		LEFT JOIN Ve_DocVenta ON Lo_Movimiento.IdDocVenta = Ve_DocVenta.idDocVenta
		LEFT JOIN Gen_Moneda ON Lo_Movimiento.Moneda = Gen_Moneda.Moneda
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

	function ListarProductoFilter($codigoBarra) {
		$Ssql = "SELECT *, 
			(SELECT MD.IdLote FROM Lo_MovimientoDetalle as MD WHERE MD.IdProducto=Gen_Producto.IdProducto group by IdProducto ORDER BY FechaVen ASC) AS Lote,
			(SELECT MD.FechaVen FROM Lo_MovimientoDetalle as MD WHERE MD.IdProducto=Gen_Producto.IdProducto group by IdProducto ORDER BY FechaVen ASC) AS FechaVen 
			FROM Gen_Producto WHERE CodigoBarra='$codigoBarra' LIMIT 1";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverProductosProximosAVencer($fechaIni, $fechaFin) {
		$Ssql = "SELECT Lo_Movimiento.Serie, Lo_Movimiento.Numero, Lo_MovimientoDetalle.*, Gen_Producto.Producto, 
		Gen_Producto.Codigo, Gen_Producto.Codigo, 
		(SELECT ProductoFormaFarmaceutica FROM Gen_ProductoFormaFarmaceutica WHERE IdProductoFormaFarmaceutica=Gen_Producto.IdProductoFormaFarmaceutica) AS FormaFarmaceutica, 
		(SELECT ProductoMarca FROM Gen_ProductoMarca WHERE IdProductoMarca=Gen_Producto.IdProductoMarca) AS Marca
		FROM Lo_MovimientoDetalle 
		INNER JOIN Gen_Producto ON Lo_MovimientoDetalle.IdProducto = Gen_Producto.IdProducto
		LEFT JOIN Lo_Movimiento ON Lo_MovimientoDetalle.hashMovimiento = Lo_Movimiento.Hash
		WHERE FechaVen BETWEEN '$fechaIni' AND '$fechaFin'
		group by Lo_MovimientoDetalle.IdProducto ORDER BY FechaVen ASC	";
		return getSQLResultSet($Ssql);		
	}

	function fn_devolverProductosDetalle() {
		$Ssql = "select Gen_Producto.IdProducto, Gen_Producto.Codigo, Gen_Producto.Producto, 
			Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica, Gen_ProductoMarca.ProductoMarca
		FROM Gen_Producto 
		LEFT JOIN Gen_ProductoFormaFarmaceutica ON Gen_Producto.IdProductoFormaFarmaceutica = Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica 
		LEFT JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
		";

		return getSQLResultSet($Ssql);
	}

	function obtenerClienteVenta($idDocVenta) {
		$Ssql = "SELECT Ve_DocVentaCliente.* FROM Ve_DocVentaCliente 
		INNER JOIN Ve_DocVenta ON Ve_DocVentaCliente.IdCliente = Ve_DocVenta.IdCliente
		WHERE Ve_DocVenta.idDocVenta=$idDocVenta";

		return getSQLResultSet($Ssql);
	}

	function fn_devolverUltimaProforma() {
		$Ssql = "SELECT Numero, Anio FROM Ve_Proforma WHERE Anio='" . date("Y") . "' ORDER BY Numero DESC LIMIT 1";
		return getSQLResultSet($Ssql);
	}

	function fn_devolverCajaBanco($idCajaBanco) {
		$Ssql = "SELECT Cb_CajaBanco.*, Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.Direccion FROM Cb_CajaBanco 
			INNER JOIN Ve_DocVentaCliente ON Cb_CajaBanco.IdCliente = Ve_DocVentaCliente.IdCliente
			WHERE IdCajaBanco=$idCajaBanco";
		return getSQLResultSet($Ssql);
	}
 ?>
