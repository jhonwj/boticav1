<?php

//include("../models/DBManager.php");
//include 'http://sistemasjeam.com/prevenvac/models/DBManager.php';
include_once("/../clases/BnGeneral.php");

function fn_guardarProductoMarca($productoMarca,$usuario){

		$Ssql="INSERT INTO botica.Gen_ProductoMarca (ProductoMarca,Anulado, FechaReg,UsuarioReg) VALUES('$productoMarca',0, now(),'$usuario')";
		//echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
function fn_modificarProductoMarca($idproductoMarca,$productoMarca,$anulado,$usuario){

		$Ssql="UPDATE botica.Gen_ProductoMarca SET ProductoMarca='$productoMarca',Anulado=$anulado, FechaMod=now(),UsuarioMod='$usuario' WHERE IdProductoMarca=$idproductoMarca";
		//echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }

    function fn_eliminarProductoMarca($idProductoMarca){

		$Ssql="DELETE FROM botica.Gen_ProductoMarca WHERE IdProductoMarca = $idProductoMarca";
		//echo $Ssql;
		if(eliminar($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
///////////////////////////////////////////////////CATEGORIA
function fn_guardarProductoCategoria($productoCategoria,$usuario){

		$Ssql="INSERT INTO botica.Gen_ProductoCategoria (ProductoCategoria,Anulado, FechaReg,UsuarioReg) VALUES('$productoCategoria',0, now(),'$usuario')";
		//echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
function fn_modificarProductoCategoria($idProductoCategoria,$productoCategoria,$anulado,$usuario){

		$Ssql="UPDATE botica.Gen_ProductoCategoria SET ProductoCategoria='$productoCategoria',Anulado=$anulado, FechaMod=now(),UsuarioMod='$usuario' WHERE IdProductoCategoria=$idProductoCategoria";
		//echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }

    function fn_eliminarProductoCategoria($IdProductoCategoria){

		$Ssql="DELETE FROM botica.Gen_ProductoCategoria WHERE IdProductoCategoria = $IdProductoCategoria";
		//echo $Ssql;
		if(eliminar($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }

//////////////////////////////////////////////////BLOQUE
function fn_guardarProductoBloque($productoBloque, $procentajeMin, $procentajeMax,$usuario){

		$Ssql="INSERT INTO botica.Gen_ProductoBloque (Bloque, PorcentajeMin, PorcentajeMax, FechaReg,UsuarioReg) VALUES('$productoBloque',$procentajeMin, $procentajeMax, now(),'$usuario')";
		//echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
function fn_modificarProductoBloque($idproductoBloque,$productoBloque, $procentajeMin, $procentajeMax,$usuario){

	$Ssql="UPDATE botica.Gen_ProductoBloque SET Bloque='$productoBloque', PorcentajeMin=$procentajeMin,PorcentajeMax=$procentajeMax, FechaMod=now(),UsuarioMod='$usuario' WHERE IdBloque=$idproductoBloque";
	// $Ssql="INSERT INTO botica.Gen_ProductoBloque (Bloque, PorcentajeMin, PorcentajeMax, FechaReg,UsuarioReg) VALUES('$productoBloque',$procentajeMin, $procentajeMax, now(),'$usuario')";
		//echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }

    function fn_eliminarProductoBloque($IdProductoCategoria){

		$Ssql="DELETE FROM botica.Gen_ProductoCategoria WHERE IdProductoCategoria = $IdProductoCategoria";
		//echo $Ssql;
		if(eliminar($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
 ///////////////////////////////////////////////////Medicion
function fn_guardarProductoMedicion($productoMedicion,$usuario){

		$Ssql="INSERT INTO botica.Gen_ProductoMedicion (ProductoMedicion,Anulado, FechaReg,UsuarioReg) VALUES('$productoMedicion',0, now(),'$usuario')";
		echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
function fn_modificarProductoMedicion($idProductoMedicion,$productoMedicion,$anulado,$usuario){

		$Ssql="UPDATE botica.Gen_ProductoMedicion SET ProductoMedicion='$productoMedicion',Anulado=$anulado, FechaMod=now(),UsuarioMod='$usuario' WHERE IdProductoMedicion=$idProductoMedicion";
		echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }

    function fn_eliminarProductoMedicion($IdProductoMedicion){

		$Ssql="DELETE FROM botica.Gen_ProductoMedicion WHERE IdProductoMedicion = $IdProductoMedicion";
		//echo $Ssql;
		if(eliminar($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
     ///////////////////////////////////////////////////FormaFarmaceutica
function fn_guardarProductoFormaFarmaceutica($productoFormaFarmaceutica,$usuario){

		$Ssql="INSERT INTO botica.Gen_ProductoFormaFarmaceutica (ProductoFormaFarmaceutica,Anulado, FechaReg,UsuarioReg) VALUES('$productoFormaFarmaceutica',0, now(),'$usuario')";
		//echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
function fn_modificarProductoFormaFarmaceutica($idProductoFarmaceutica,$productoFarmaceutica,$anulado,$usuario){

		$Ssql="UPDATE botica.Gen_ProductoFormaFarmaceutica SET ProductoFormaFarmaceutica='$productoFarmaceutica',Anulado=$anulado, FechaMod=now(),UsuarioMod='$usuario' WHERE IdProductoFormaFarmaceutica=$idProductoFarmaceutica";
		//echo $Ssql;
		//exit();
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }

    function fn_modificarProductoFormaFarmaceuticaEstado($IdProductoFarmaceutica, $anulado, $usuario){

		$Ssql="UPDATE botica.Gen_ProductoFormaFarmaceutica SET Anulado=$anulado,FechaMod=now(),UsuarioMod='$usuario' WHERE IdProductoFormaFarmaceutica = $IdProductoFarmaceutica";
		//echo $Ssql;
		//exit();
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
         ///////////////////////////////////////////////////ProductoCompuesto
function fn_guardarProductoCompuesto($productoCompuesto,$usuario){

		$Ssql="CALL SbGen_CompuestoGuardar('$productoCompuesto', '$usuario')";
		return getSQLResultSet($Ssql);

    }
function fn_modificarProductoCompuesto($idProductoCompuesto,$productoCompuesto,$anulado,$usuario){

		$Ssql="UPDATE botica.Gen_ProductoCompuesto SET ProductoCompuesto='$productoCompuesto',Anulado=$anulado, FechaMod=now(),UsuarioMod='$usuario' WHERE IdProductoCompuesto=$idProductoCompuesto";
		//echo $Ssql;
		if(ejecutarSQLCommand($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }

    function fn_eliminarProductoCompuesto($idProductoCompuesto){

		$Ssql="DELETE FROM botica.Gen_ProductoCompuesto WHERE IdProductoCompuesto = $idProductoCompuesto";
		//echo $Ssql;
		if(eliminar($Ssql)){
			return "SI";
		}
		//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
		return "NO";

    }
		///////////////////////////////////////////////////PRODUCTO
		function fn_guardarProducto($idProductoMarca, $idPoductoFormaFarmaceutica, $idProductoMedicion, $idProductoCategoria, $bloque,  $producto, $productoDesc, $productoDescCorto, $codigoBarra, $codigo, $dosis,  $precioCosto,$ventaEstrategica, $precioUtilidad, $precioContado,$precioXMayor,$stockXMayor, $controlaStock, $stockMin,$usuario){

				$Ssql="CALL SbGen_ProductoGuardar('$idProductoMarca', '$idPoductoFormaFarmaceutica', '$idProductoMedicion', '$idProductoCategoria', '$producto', '$productoDesc', '$productoDescCorto', '$codigo', '$codigoBarra', '$dosis',$precioContado,$precioXMayor,$stockXMayor, $controlaStock, $stockMin, '$usuario', $precioCosto, $ventaEstrategica, $precioUtilidad, '$bloque')";
				//echo $Ssql;
				//exit();
				return getSQLResultSet($Ssql);

		    }
			function fn_modificarProducto($idProducto , $idProductoMarca, $idPoductoFormaFarmaceutica, $idProductoMedicion, $idProductoCategoria, $bloque,  $producto, $productoDesc, $productoDescCorto, $codigoBarra, $codigo, $dosis,  $precioCosto,$ventaEstrategica, $precioUtilidad, $precioContado,$precioXMayor,$stockXMayor, $controlaStock, $stockMin,$usuario){

				$Ssql="CALL SbProductoModificar ($idProducto, '$idProductoMarca', '$idPoductoFormaFarmaceutica', '$idProductoMedicion', '$idProductoCategoria', '$producto', '$productoDesc', '$productoDescCorto', '$codigoBarra', '$codigo', '$dosis',$precioContado,$precioXMayor,$stockXMayor, $controlaStock, $stockMin, '$usuario', $precioCosto, $ventaEstrategica, $precioUtilidad, '$bloque')";
				echo $Ssql;
				if(ejecutarSQLCommand($Ssql)){
					return "SI";
				}
				//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
				return "NO";

		    }

		    function fn_guardarDocVenta($puntoVenta, $tipoDoc, $cliente, $almacen, $usuario, $EsCredito, $fechaCredito){
				//	echo $fechaCredito;
					//exit();
				$fechaDoc=fn_devolverfechaActual();
				$Ssql="CALL botica.SbVe_GuardarDocVenta ('$puntoVenta', '$tipoDoc', '$cliente', '$almacen', '$fechaDoc', '$usuario', $EsCredito, '$fechaCredito')";
				$res=getSQLResultSet($Ssql);
				$idDocVenta = "";
				while ($row =mysql_fetch_row($res)) {
		 		$idDocVenta = $row[0];
		 		//break;
				}
				//echo $Ssql;

				//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
				return $idDocVenta;

		    }

		    function fn_guardarDocVentaDet($idDocVenta, $idProducto, $cantidad, $precio){

				$Ssql="CALL SbVe_GuardarDocVentaDet ('$idDocVenta', '$idProducto', '$cantidad', '$precio')";
				$res=ejecutarSQLCommand($Ssql);
				if($res){
					return $res;

				//echo $Ssql;

				}
				//echo $Ssql;

				//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
				return false;

		    }

				function fn_EliminarRegVenta($docVenta)
				{
					$Ssql="CALL SbVe_DocVentaAnular($docVenta)";
					ejecutarSQLCommand($Ssql);
				}

		    function fn_guardarDocVentaMetodoPagoDet($idDocVenta, $metodoPago, $importe, $numTarjeta){

				$Ssql="CALL SbVe_GuardarMetodoPagoDet ('$idDocVenta', '$metodoPago', '$importe', '$numTarjeta')";
				$res=ejecutarSQLCommand($Ssql);
				if($res){
					return $res;

				//echo $Ssql;

				}
				//echo $Ssql;

				//echo "<br/>SE GUARDO ($IdVideo22)!!!!!";
				return false;

		    }

		    function fn_guardarCliente($cliente, $dni, $direccion, $telefono, $email, $usuario){

				$Ssql="INSERT INTO botica.Ve_DocVentaCliente(Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.Direccion, Ve_DocVentaCliente.Telefono, Ve_DocVentaCliente.Email, Ve_DocVentaCliente.Anulado, Ve_DocVentaCliente.FechaReg,Ve_DocVentaCliente.UsuarioReg)
						VALUES(
							'$cliente',
							'$dni',
							'$direccion',
							'$telefono',
							'$email',
							 0,
							 now(),
							'$usuario'
							)";
				$res=ejecutarSQLCommand($Ssql);
				if(!$res){
					return $res;
				}else{
					return false;
				}



		    }

				function fn_modificarCliente($idCliente, $cliente, $dni, $direccion, $telefono, $email, $usuario)
				{
					$Ssql = "UPDATE botica.Ve_DocVentaCliente SET
					Cliente = '$cliente',
					DniRuc =  '$dni',
					Direccion = '$direccion',
					Telefono = '$telefono',
					Email = '$email',
					FechaMod = now(),
					UsuarioMod = 'Jeam'
					WHERE IdCliente = $idCliente;
					";
					//echo $Ssql;
					//exit();
					$res=ejecutarSQLCommand($Ssql);
				if(!$res){
					return $res;
				}else{
					return false;

				}

				}

		    function fn_guardarTratamiento($IdTratamiento, $Diagnostico, $Compuesto, $Observacion, $TomaDia, $NroDia, $DosisXPeso, $CantSol, $UnidadDosisXPeso){

				$Ssql="CALL SbVe_GuardarTratamiento($IdTratamiento, '$Diagnostico', '$Compuesto', '$Observacion', $TomaDia, $NroDia, $DosisXPeso, $CantSol, 'Jeam', '$UnidadDosisXPeso')";
				return getSQLResultSet($Ssql);


		    }

		    function fn_guardarSintoma($Sintoma){

				$Ssql="INSERT INTO botica.Ve_ExpertoSintoma(Ve_ExpertoSintoma.Sintoma) VALUES ('$Sintoma')";
				$res=ejecutarSQLCommand($Ssql);
				if($res){
					return $res;
				}

				return false;
		    }

				function fn_modificarSintoma($IdSintoma, $Sintoma){

				$Ssql="UPDATE botica.Ve_ExpertoSintoma SET Ve_ExpertoSintoma.Sintoma='$Sintoma' WHERE Ve_ExpertoSintoma.IdSintoma = $IdSintoma";
				$res=ejecutarSQLCommand($Ssql);
				if($res){
					return $res;
				}

				return false;
		    }

		    function fn_guardarDiagnostico($Diagnostico, $Problema, $Edad, $Obs, $usuario){

				$Ssql="CALL SbVe_ExpertoDiagnosticoGuardar('$Diagnostico', '$Problema', $Edad, '$Obs', '$usuario')";
				return getSQLResultSet($Ssql);
		    }

		    function fn_modificarDiagnostico($IdDiagnostico ,$Diagnostico, $Problema, $Edad, $Obs, $usuario){

				$Ssql="CALL SbVe_ExpertoDiagnosticoModificar($IdDiagnostico ,'$Diagnostico', '$Problema', $Edad, '$Obs', '$usuario')";
				$res = ejecutarSQLCommand($Ssql);
		    }

		    function fn_actualizarTratamientoD($IdTratamiento, $IdDiagnostico)
		    {
		    	$Ssql="CALL SbVe_ExpertoTratamientoActualizarD($IdTratamiento, $IdDiagnostico);";
				$res=ejecutarSQLCommand($Ssql);
		    }

		    function fn_actualizarSintomaD($IdDiagnostico, $IdSintoma, $usuario)
		    {
		    	$Ssql="CALL SbVe_ExpertoDiagnosticoSintomaDet($IdDiagnostico, $IdSintoma, '$usuario');";
				$res=ejecutarSQLCommand($Ssql);
		    }

		    function fn_guardarProductoCompuestoDet($IdProductoCompuesto, $IdProducto)
		    {
		    	$Ssql="CALL SbGen_ProductoCompuestoGuardar($IdProductoCompuesto, $IdProducto);";
				ejecutarSQLCommand($Ssql);
		    }

		    function fn_guardarProductoDet($idProducto, $idProductoDet, $cantidad)
		    {
		    	$Ssql="CALL SbGen_ProductoDetGuardar($idProducto, $idProductoDet, $cantidad);";
				ejecutarSQLCommand($Ssql);
		    }

		    function fn_guardarProveedor($proveedor, $ruc, $direccion, $observacion)
		    {
		    	$Ssql = "CALL SbLo_ProveedorGuardar('$proveedor', '$ruc', '$direccion', '$observacion' , 'Jeam')";
		    	/*echo $Ssql;
		    	exit();*/
		    	ejecutarSQLCommand($Ssql);

		    }

		    function fn_modificarProveedor($idProveedor, $proveedor, $ruc, $direccion, $observacion)
		    {
		    	$Ssql = "UPDATE botica.Lo_Proveedor SET
		    			Proveedor = '$proveedor',
		    			Ruc = '$ruc',
		    			Direccion = '$direccion',
		    			Observacion = '$observacion'
		    			WHERE IdProveedor = $idProveedor;
		    			";
		    	ejecutarSQLCommand($Ssql);
		    }

		    function fn_guardarAlmacen($almacen){
		    	$Ssql = "INSERT INTO botica.Lo_Almacen(Almacen, Anulado, FechaReg, UsuarioReg) VALUES ('$almacen', 0, now(), 'Jeam')";
		    	ejecutarSQLCommand($Ssql);
		    }

		    function fn_guardarMovimiento($tipoMovimiento, $proveedor, $serie, $numero, $fecha, $almacenOrigen, $almacenDestino, $obs, $fechaStock, $percepcion, $EsCredito, $fechaCredito, $periodoTributario){
		    	$Ssql = "CALL SbLo_MovimientoGuardar('$tipoMovimiento', '$proveedor', '$serie', $numero, '$fecha', $almacenOrigen, $almacenDestino, '$obs', 'jeam',  '$fechaStock', $percepcion, $EsCredito,'$fechaCredito', $periodoTributario)";
					//echo $Ssql;
					// exit();
					$result = getSQLResultSet($Ssql);
		    	$hash = "";
		    	while ($row = mysql_fetch_assoc($result)) {
		    		$hash = $row["Hash"];
		    	}
		    	return $hash;
		    }
				function fn_EliminarMovimiento($idMov)
				{
					$Ssql = "CALL SbLo_MovimientoEliminar('$idMov')";
					//echo $Ssql;
					//exit();
					ejecutarSQLCommand($Ssql);
				}

			function fn_guardarMovimientoDet($hash, $producto, $cantidad, $precio, $tieneIgv, $ISC, $Flete, $Lote, $fechaVen){
		    	$Ssql = "CALL SbLo_MovimientoDetGuardar('$hash', '$producto', $cantidad, $tieneIgv, $precio,$ISC, $Flete, $Lote, '$fechaVen')";
					//echo $Ssql;
					//exit();
					ejecutarSQLCommand($Ssql);
		    }

				function fn_GuardarCierre()
				{
					# code...
					$Ssql = " UPDATE botica.Ve_DocVenta SET IdCierre=1 WHERE IdCierre IS NULL";
					ejecutarSQLCommand($Ssql);
				}


// Funciones Generales
function fn_actualizarRegistro($tabla, $campos, $where)
{
	if (!is_array($campos) || !isset($campos) || !isset($tabla) || !is_array($where)) {
		return false;
	}

	$Ssql = " UPDATE botica.$tabla SET";
	foreach ($campos as $key => $value) {
		$Ssql .= " " . $key . "=" . $value;

		if ($value !== end($campos)) {
	        $Ssql .= ",";
	    }
	}

	$Ssql .= " WHERE $where[0] $where[1] $where[2]";

	return getSQLResultSet($Ssql);
}

 ?>
