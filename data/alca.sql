-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 26-02-2018 a las 06:13:19
-- Versión del servidor: 10.1.30-MariaDB
-- Versión de PHP: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `neurofac_botica2`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `SbCb_ListarCajaBanco` (IN `var_IdCuenta` INT, IN `var_FechaDoc` DATETIME)  BEGIN

	SELECT

	CB.IdCajaBanco,

	CB.Concepto,

	CASE TCB.Tipo

		WHEN 1 THEN 0

        ELSE CB.Importe

	END as 'Ingresos',

    CASE TCB.Tipo

		WHEN 1 THEN CB.Importe

        ELSE 0

	END as 'Salida'

    FROM Cb_CajaBanco as CB

    INNER JOIN Cb_TipoCajaBanco as TCB

    ON CB.IdTipoCajaBanco = TCB.IdTipoCajaBanco
    WHERE CB.IdCuenta = var_IdCuenta AND CB.FechaDoc = var_FechaDoc

	ORDER BY TCB.Tipo,CB.IdCajaBanco;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbCb_ListarCuenta` ()  BEGIN

	SELECT IdCuenta, Cuenta, Anulado FROM Cb_Cuenta;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbCb_ListarDocAplicados` (IN `var_IdCliente` INT)  BEGIN
Select
Ve_DocVenta.IdDocVenta,
Ve_DocVenta.FechaDoc,
Ve_DocVenta.FechaCredito,
CONCAT(CASE WHEN Ve_DocVentaTipoDoc.CodSunat ='01' THEN
	'F'
ELSE
	CASE WHEN Ve_DocVentaTipoDoc.CodSunat='03' THEN
		'B'
	ELSE
		CASE WHEN Ve_DocVentaTipoDoc.CodSunat='12' THEN
			'T'
		ELSE
			'OTRO'
		END
	END
END,Ve_DocVenta.Serie,'-' , convert(Ve_DocVenta.Numero, char) )as Correlativo,
Round(Sum(Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad),2) as Total,
Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='VE' And Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) as Aplicado,
Round(Sum(Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad),2) -
Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='VE' And Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) as Saldo
From Ve_DocVenta
Inner Join Ve_DocVentaDet On Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta
Inner Join Ve_DocVentaTipoDoc On Ve_DocVenta.IdTipoDoc=Ve_DocVentaTipoDoc.IdTipoDoc
Where EsCredito=1 and Ve_DocVenta.IdCliente=var_IdCliente

Group by
Ve_DocVenta.IdDocVenta,
Ve_DocVenta.FechaDoc,
Ve_DocVenta.FechaCredito,
Ve_DocVentaTipoDoc.CodSunat,
Ve_DocVenta.Serie,
Ve_DocVenta.Numero
HAVING Round(Sum(Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad),2) -
Round((SELECT IfNull((SElect Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='VE' And Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2)>0
Order by FechaDoc;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbCb_ListarDocAplicadosProveedor` (IN `var_IdProveedor` INT)  BEGIN
SELECT
Lo_Movimiento.Hash as Id,
Lo_Movimiento.MovimientoFecha as FechaDoc,
Lo_Movimiento.FechaVenCredito as FechaCredito,
CONCAT(CASE WHEN Lo_MovimientoTipo.CodSunat ='01' THEN
	'F'
ELSE
	CASE WHEN Lo_MovimientoTipo.CodSunat='03' THEN
		'B'
	ELSE
		CASE WHEN Lo_MovimientoTipo.CodSunat='12' THEN
			'T'
		ELSE
			'OTRO'
		END
	END
END,Lo_Movimiento.Serie,'-' , convert(Lo_Movimiento.Numero, char) )as Correlativo,
Round(Sum(Lo_MovimientoDetalle.Precio*Lo_MovimientoDetalle.Cantidad),2) as Total,
Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='MO' And Cb_CajaBancoDet.Hash=Lo_Movimiento.Hash),0)),2) as Aplicado,
Round(SUM(Lo_MovimientoDetalle.Precio*Lo_MovimientoDetalle.Cantidad),2) -
Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='MO' And Cb_CajaBancoDet.Hash=Lo_Movimiento.Hash),0)),2) as Saldo
From Lo_Movimiento
Inner Join Lo_MovimientoDetalle On Lo_Movimiento.Hash=Lo_MovimientoDetalle.hashMovimiento
Inner Join Lo_MovimientoTipo On Lo_Movimiento.IdMovimientoTipo=Lo_MovimientoTipo.IdMovimientoTipo
Where EsCredito=1 and Lo_Movimiento.IdProveedor=var_IdProveedor

Group by
Lo_Movimiento.Hash,
Lo_Movimiento.MovimientoFecha,
Lo_Movimiento.FechaVenCredito,
Lo_MovimientoTipo.CodSunat,
Lo_Movimiento.Serie,
Lo_Movimiento.Numero
HAVING Round(Sum(Lo_MovimientoDetalle.Precio*Lo_MovimientoDetalle.Cantidad),2) -
Round((SELECT IfNull((Select Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='MO' And Cb_CajaBancoDet.Hash=Lo_Movimiento.Hash),0)),2)>0
Order by MovimientoFecha;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbCb_ListarTipoOpe` ()  BEGIN

	SELECT IdTipoCajaBanco, TipoCajaBanco, Tipo FROM Cb_TipoCajaBanco;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbGen_CambiarPrecioXBloque` (IN `var_Bloque` VARCHAR(255), IN `var_Porcentaje` VARCHAR(255), IN `var_PrecioXMayorIgual` BIT)  BEGIN



		DECLARE v_IdProducto Int;

		DECLARE v_PrecioCosto FLOAT;

		DECLARE v_PorcentajeUtilidad FLOAT;

		DECLARE v_PrecioVenta FLOAT;

		DECLARE v_PrecioVentaFinal FLOAT;



		DECLARE done INT DEFAULT FALSE;







		declare cur1 cursor for

						Select IdProducto,PrecioCosto,PorcentajeUtilidad,Gen_Producto.PrecioContado

						from Gen_Producto

						Inner JOIN Gen_ProductoCategoria On Gen_Producto.IdProductoCategoria=Gen_ProductoCategoria.IdProductoCategoria

						Inner Join Gen_ProductoMarca On Gen_ProductoMarca.IdProductoMarca=Gen_Producto.IdProductoMarca

						Inner Join Gen_ProductoFormaFarmaceutica On Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica=Gen_Producto.IdProductoFormaFarmaceutica

						Inner Join Gen_ProductoBloque On Gen_Producto.IdBloque=Gen_ProductoBloque.IdBloque

						where Gen_ProductoBloque.Bloque=var_Bloque and Gen_Producto.Anulado=0;

		declare continue handler for not found set done=1;

    set done = 0;

    open cur1;
igmLoop: loop

        fetch cur1 into v_IdProducto,v_PrecioCosto,v_PorcentajeUtilidad,v_PrecioVenta;

        if done = 1 then leave igmLoop; end if;

							SET v_PrecioVentaFinal=v_PrecioCosto*(var_Porcentaje/100);

							IF var_PrecioXMayorIgual=1 THEN

									UPDATE Gen_Producto

									SET PorcentajeUtilidad=var_Porcentaje,

											PrecioVenta=v_PrecioVentaFinal,

											PrecioPorMayor=v_PrecioVentaFinal,

											StockMinimo=1

									WHERE Gen_Producto.IdProducto=v_IdProducto;

							ELSE

									UPDATE Gen_Producto

									SET

											PorcentajeUtilidad=var_Porcentaje,

											PrecioVenta=v_PrecioVentaFinal

									WHERE Gen_Producto.IdProducto=v_IdProducto;

							END IF;









    end loop igmLoop;

    close cur1;









END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbGen_CompuestoGuardar` (IN `var_Compuesto` VARCHAR(255), IN `var_UsuarioReg` VARCHAR(255))  BEGIN
SET @Hash2=(Select UNIX_TIMESTAMP());
INSERT INTO Gen_ProductoCompuesto(Gen_ProductoCompuesto.ProductoCompuesto, Gen_ProductoCompuesto.Anulado, Gen_ProductoCompuesto.FechaReg, Gen_ProductoCompuesto.UsuarioReg, Gen_ProductoCompuesto.`Hash`)
	VALUES (
		var_Compuesto,
		0,
		NOW(),
		var_UsuarioReg,
		@Hash2
);
SELECT Gen_ProductoCompuesto.IdProductoCompuesto FROM Gen_ProductoCompuesto WHERE Gen_ProductoCompuesto.`Hash` = @Hash2;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbGen_ListarProductoBloque` ()  BEGIN

select IdBloque, Bloque, PorcentajeMin, PorcentajeMax from Gen_ProductoBloque;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbGen_ListarProductoDet` (IN `var_Producto` INT)  BEGIN
SELECT Gen_Producto.IdProducto, Gen_Producto.Producto, Gen_ProductoDet.Cantidad FROM Gen_ProductoDet
INNER JOIN Gen_Producto ON Gen_Producto.IdProducto = Gen_ProductoDet.IdProductoDet
 WHERE Gen_ProductoDet.IdProducto = var_Producto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbGen_ProductoCompuestoGuardar` (IN `var_Compuesto` INT, IN `var_Producto` INT)  BEGIN
INSERT INTO Gen_ProductoCompuestoDet(Gen_ProductoCompuestoDet.Gen_ProductoCompuesto_IdProductoCompuesto, Gen_ProductoCompuestoDet.Gen_Producto_IdProducto)
	VALUES (
		var_Compuesto,
		var_Producto
);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbGen_ProductoDetGuardar` (IN `var_idProducto` INT, IN `var_idProductoDet` INT, IN `var_cantidad` FLOAT)  BEGIN

INSERT INTO Gen_ProductoDet(Gen_ProductoDet.IdProducto, Gen_ProductoDet.IdProductoDet, Gen_ProductoDet.Cantidad)

VALUES (var_idProducto, var_idProductoDet, var_cantidad);

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbGen_ProductoGuardar` (IN `var_ProductoMarca` VARCHAR(255), IN `var_FormaFarmaceutica` VARCHAR(255), IN `var_Medicion` VARCHAR(255), IN `var_Categoria` VARCHAR(255), IN `var_Producto` VARCHAR(255), IN `var_ProductoDesc` TEXT, IN `var_ProductoDescCorto` VARCHAR(255), IN `var_Codigo` VARCHAR(255), IN `var_CodigoBarra` VARCHAR(255), IN `var_Dosis` VARCHAR(255), IN `var_PrecioContado` FLOAT, IN `var_PrecioPorMayor` FLOAT, IN `var_StockPorMayor` FLOAT, IN `var_ControlaStock` BIT, IN `var_StockPorMin` FLOAT, IN `var_Usuario` VARCHAR(255), IN `var_PrecioCosto` FLOAT, IN `var_VentaEstrategica` BIT, IN `var_PrecioUtilidad` FLOAT, IN `var_Bloque` VARCHAR(255))  BEGIN



SET @Hash2=(Select UNIX_TIMESTAMP());



INSERT INTO Gen_Producto(Gen_Producto.IdProductoMarca, Gen_Producto.IdProductoFormaFarmaceutica, Gen_Producto.IdProductoMedicion, Gen_Producto.IdProductoCategoria,



													Gen_Producto.Producto, Gen_Producto.ProductoDesc, Gen_Producto.ProductoDescCorto, Gen_Producto.CodigoBarra, Gen_Producto.Codigo, Gen_Producto.Dosis,



													Gen_Producto.precioContado,Gen_Producto.PrecioPorMayor,Gen_Producto.StockPorMayor, Gen_Producto.ControlaStock, Gen_Producto.StockMinimo, Gen_Producto.PrecioCosto, Gen_Producto.VentaEstrategica, Gen_Producto.PorcentajeUtilidad, Gen_Producto.IdBloque,Gen_Producto.Anulado, Gen_Producto.FechaReg, Gen_Producto.UsuarioReg,



													Gen_Producto.Hash)



						VALUES(



							(SELECT PM.IdProductoMarca FROM Gen_ProductoMarca AS PM WHERE PM.ProductoMarca  = var_ProductoMarca),



							(SELECT PFF.IdProductoFormaFarmaceutica FROM Gen_ProductoFormaFarmaceutica AS PFF WHERE PFF.ProductoFormaFarmaceutica = var_FormaFarmaceutica),



							(SELECT PME.IdProductoMedicion FROM Gen_ProductoMedicion AS PME WHERE PME.ProductoMedicion = var_Medicion),



							(SELECT PC.IdProductoCategoria FROM Gen_ProductoCategoria AS PC WHERE PC.ProductoCategoria = var_Categoria),



							var_Producto,



							var_ProductoDesc,



							var_ProductoDescCorto,



							var_CodigoBarra,



							var_Codigo,



							var_Dosis,



							var_PrecioContado,



							var_PrecioPorMayor,



							var_StockPorMayor,



							var_ControlaStock ,



							var_StockPorMin ,



							var_PrecioCosto,

							var_VentaEstrategica,

							var_PrecioUtilidad,

							 (SELECT PB.IdBloque FROM Gen_ProductoBloque AS PB WHERE PB.Bloque = var_Bloque),



							 0,



							 now(),



							var_Usuario,



							@Hash2



							);



SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Hash=@Hash2;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_Kardex` (IN `var_Producto` VARCHAR(255), IN `var_FechaIni` DATE, IN `var_FechaFin` DATE, IN `var_Tipo` INT)  BEGIN







	CREATE TABLE IF NOT EXISTS `tblKardex` (



  `d1` varchar(255) DEFAULT ' ',



  `d2` varchar(255) DEFAULT ' ',



  `d3` varchar(255) DEFAULT ' ',



	`d4` varchar(255) DEFAULT ' ',



	`d5` varchar(255) DEFAULT ' ');



	Delete from tblKardex;











	INSERT INTO tblKardex (d1) VALUES ('KARDEX');



	INSERT INTO tblKardex (d1) VALUES (' ');



	INSERT INTO tblKardex (d1,d2) VALUES ('PRODUCTO',var_Producto);



	INSERT INTO tblKardex (d1,d2) VALUES ('DESDE',var_FechaIni);



	INSERT INTO tblKardex (d1,d2) VALUES ('HASTA',var_FechaFin);



	INSERT INTO tblKardex (d1) VALUES (' ');



	INSERT INTO tblKardex (d1,d2,d3,d4,d5) VALUES ('FECHA','DETALLE','ENTRADA','SALIDA','SALDO');











	call SbLo_StockAnt (var_Producto,var_FechaIni);



	SET @SALDO=@StockAnt;



	SET @INGRESOS=0;



	SET @SALIDA=@StockAnt;



	SET @FechaSel=var_FechaIni;







	WHILE (DATEDIFF(var_FechaFin,var_FechaIni)>=0) DO



		 CALL SbLo_Kardex_IngresosUND (var_Producto,var_FechaIni,var_Tipo);



		 CALL SbLo_Kardex_IngresosCAJA (var_Producto,var_FechaIni,var_Tipo);



		 CALL SbLo_Kardex_SalidaUND (var_Producto,var_FechaIni,var_Tipo);



		  CALL SbLo_Kardex_SalidaCAJA (var_Producto,var_FechaIni,var_Tipo);



		 CALL SbLo_Kardex_SalidaVentaUND (var_Producto,var_FechaIni);



		 CALL SbLo_Kardex_SalidaVentaCAJA (var_Producto,var_FechaIni);















		SET var_FechaIni =DATE_ADD(var_FechaIni, INTERVAL 1 DAY);







	END WHILE;



 Select * From tblKardex;







END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_KardexValorizadoINGRESOS` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_StockInical` FLOAT, IN `var_PrecioInicial` FLOAT, OUT `var_StockFinal` FLOAT, OUT `var_PrecioFinal` FLOAT, IN `var_Tipo` INT)  BEGIN



		DECLARE V1_Saldo Float;



		DECLARE V1_Precio FLOAT;



		DECLARE V1_Total FLOAT;



		DECLARE v_cantidad  FLOAT;



		DECLARE v_Precio FLOAT;



		DECLARE v_Total FLOAT;



		declare v_doc varchar(100);



		DECLARE done INT DEFAULT FALSE;



		declare cur1 cursor for



						SELECT CONCAT(Lo_MovimientoTipo.TipoMovimiento,' ' , Lo_Movimiento.Serie ,'-',Lo_Movimiento.Numero) AS DocMovimiento,



						Lo_MovimientoDetalle.Cantidad,Lo_MovimientoDetalle.Precio, Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio as Total



						FROM Lo_Movimiento



						Inner Join Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento



						INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo



						WHERE CASE WHEN var_Tipo=0 THEN Lo_Movimiento.MovimientoFecha=var_FechaSel ELSE Lo_Movimiento.FechaStock=var_FechaSel END and Lo_MovimientoDetalle.IdProducto in



						(SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto )



						AND Lo_Movimiento.IdAlmacenDestino>0;



		declare continue handler for not found set done=1;







    set done = 0;



    open cur1;



		SET V1_Saldo=var_StockInical;



		SET V1_Precio=var_PrecioInicial;



		SET V1_Total=V1_Saldo*V1_Precio;







    igmLoop: loop



        fetch cur1 into v_doc,v_cantidad,v_Precio,v_Total;



        if done = 1 then leave igmLoop; end if;



							SET V1_Saldo=V1_Saldo+v_cantidad;



							SET V1_Precio=ROUND((v_Total+V1_Total)/V1_Saldo,2);



							SET V1_Total=Round(V1_Saldo*V1_Precio,2);















							INSERT INTO tblKardexvalor (d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,d11) VALUES (var_FechaSel,



																																											v_doc,v_cantidad,



																																											v_Precio,



																																											v_Total,



																																											'',



																																											'',



																																											'',



																																											V1_Saldo,



																																											Round(V1_Precio,2),



																																											Round(V1_Total,2));











    end loop igmLoop;



    close cur1;



		SET var_StockFinal=V1_Saldo;



		SET var_PrecioFinal=V1_Precio;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_KardexValorizadoINGRESOS_Caja` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_StockInical` FLOAT, IN `var_PrecioInicial` FLOAT, OUT `var_StockFinal` FLOAT, OUT `var_PrecioFinal` FLOAT, IN `var_Tipo` INT)  BEGIN



		DECLARE V1_Saldo Float;



		DECLARE V1_Precio FLOAT;



		DECLARE V1_Total FLOAT;



		DECLARE v_cantidad  FLOAT;



		DECLARE v_Precio FLOAT;



		DECLARE v_Total FLOAT;



		declare v_doc varchar(100);



		DECLARE done INT DEFAULT FALSE;



		declare cur1 cursor for



						SELECT CONCAT(Lo_MovimientoTipo.TipoMovimiento,' ' , Lo_Movimiento.Serie ,'-',Lo_Movimiento.Numero) AS DocMovimiento,



								Lo_MovimientoDetalle.Cantidad*



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 ),



								Round((Lo_MovimientoDetalle.Precio/



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 )),2),



								Round(Lo_MovimientoDetalle.Cantidad*



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 )



								*Round((Lo_MovimientoDetalle.Precio/



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 )),2),2)



						FROM Lo_Movimiento



						Inner Join Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento



						INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo



						WHERE CASE WHEN var_Tipo=0 THEN Lo_Movimiento.MovimientoFecha=var_FechaSel ELSE Lo_Movimiento.FechaStock=var_FechaSel END and Lo_MovimientoDetalle.IdProducto in



						(SELECT Gen_ProductoDet.IdProducto FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet=(



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ))



						AND Lo_Movimiento.IdAlmacenDestino>0;







		declare continue handler for not found set done=1;







    set done = 0;



    open cur1;



		SET V1_Saldo=Round(var_StockInical,2);



		SET V1_Precio=Round(var_PrecioInicial,2);



		SET V1_Total=Round(V1_Saldo*V1_Precio,2);







    igmLoop: loop



        fetch cur1 into v_doc,v_cantidad,v_Precio,v_Total;



        if done = 1 then leave igmLoop; end if;



							SET V1_Saldo=V1_Saldo+v_cantidad;



							SET V1_Precio=ROUND((v_Total+V1_Total)/V1_Saldo,2);



							SET V1_Total=Round(V1_Saldo*V1_Precio,2);















							INSERT INTO tblKardexvalor (d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,d11) VALUES (var_FechaSel,



																																											v_doc,



																																											Round(v_cantidad,2),



																																											Round(v_Precio,2),



																																											Round(v_Total,2),



																																											'',



																																											'',



																																											'',



																																											V1_Saldo,



																																											Round(V1_Precio,2),



																																											Round(V1_Total,2));











    end loop igmLoop;



    close cur1;



		SET var_StockFinal=V1_Saldo;



		SET var_PrecioFinal=V1_Precio;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_KardexValorizadoIniciar` (IN `Var_Producto` VARCHAR(255), IN `var_anno` INT, IN `Var_StockInical` FLOAT, IN `Var_PrecioInicial` FLOAT)  BEGIN

	DECLARE COSTO Float;

	DECLARE var_FechaFin varchar(255);

	DECLARE var_FechaIni varchar(255);

	SET COSTO=Var_PrecioInicial*Var_StockInical;

	CREATE TABLE IF NOT EXISTS `tblKardexvalor` (

  `d1` varchar(255) DEFAULT ' ',

  `d2` varchar(255) DEFAULT ' ',

  `d3` varchar(255) DEFAULT ' ',

	`d4` varchar(255) DEFAULT ' ',

	`d5` varchar(255) DEFAULT ' ',

	`d6` varchar(255) DEFAULT ' ',

	`d7` varchar(255) DEFAULT ' ',

  `d8` varchar(255) DEFAULT ' ',

	`d9` varchar(255) DEFAULT ' ',

	`d10` varchar(255) DEFAULT ' ',

	`d11` varchar(255) DEFAULT ' ');

	Delete from tblKardexvalor;









	SET var_FechaFin=CONCAT(var_anno,'-12-31');

	SET var_FechaIni=CONCAT(var_anno,'-01-01');



	INSERT INTO tblKardexvalor (d1) VALUES ('KARDEX VALORIZADO');

	INSERT INTO tblKardexvalor (d1) VALUES (' ');

	INSERT INTO tblKardexvalor (d1,d2) VALUES ('PRODUCTO',var_Producto);

	INSERT INTO tblKardexvalor (d1,d2) VALUES ('AÑO',var_anno);

	INSERT INTO tblKardexvalor (d1) VALUES (' ');

	INSERT INTO tblKardexvalor (d1,d2) VALUES ('DESDE ',var_FechaIni);

	INSERT INTO tblKardexvalor (d1,d2) VALUES ('HASTA ',var_FechaFin);

	INSERT INTO tblKardexvalor (d1) VALUES (' ');

	INSERT INTO tblKardexvalor (d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,d11) VALUES ('FECHA','DETALLE','ENTRADA','P/C','TOTAL','SALIDA','P/C','TOTAL','SALDO','P/C','TOTAL');

	INSERT INTO tblKardexvalor (d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,d11) VALUES ('2017-01-01','SALDO INICIAL',Var_StockInical,Var_PrecioInicial,COSTO,'','','',Var_StockInical,Var_PrecioInicial,COSTO);





END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_KardexValorizadoSalidaVenta_CAJA` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_StockInical` FLOAT, IN `var_PrecioInicial` FLOAT, OUT `var_StockFinal` FLOAT, OUT `var_PrecioFinal` FLOAT)  BEGIN

		DECLARE V1_Saldo Float;

		DECLARE V1_Precio FLOAT;

		DECLARE V1_Total FLOAT;

		DECLARE v_cantidad  FLOAT;

		DECLARE v_Precio FLOAT;

		DECLARE v_Total FLOAT;

		declare v_doc varchar(100);

		DECLARE done INT DEFAULT FALSE;

		declare cur1 cursor for

						SELECT CONCAT(Ve_DocVentaTipoDoc.TipoDoc,' ' , Ve_DocVenta.Serie ,'-',Ve_DocVenta.Numero) AS DocMovimiento,

								Ve_DocVentaDet.Cantidad*

										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in  (

										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ) and Gen_ProductoDet.IdProducto=Ve_DocVentaDet.IdProducto LIMIT 1 ),

								Round(Ve_DocVentaDet.Precio/

								(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in  (

										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ) and Gen_ProductoDet.IdProducto=Ve_DocVentaDet.IdProducto LIMIT 1 ),2),

							'0' as Total

						FROM Ve_DocVenta

						INNER JOIN Ve_DocVentaDet On Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta

						INNER JOIN Ve_DocVentaTipoDoc On Ve_DocVentaTipoDoc.IdTipoDoc=Ve_DocVenta.IdTipoDoc

						WHERE convert(Ve_DocVenta.FechaDoc,date)=var_FechaSel and Ve_DocVentaDet.IdProducto in

						(SELECT Gen_ProductoDet.IdProducto FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (

										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ));



		declare continue handler for not found set done=1;



    set done = 0;

    open cur1;

		SET V1_Saldo=Round(var_StockInical,2);

		SET V1_Precio=Round(var_PrecioInicial,2);

		SET V1_Total=Round(V1_Saldo*V1_Precio,2);



    igmLoop: loop

        fetch cur1 into v_doc,v_cantidad,v_Precio,v_Total;

        if done = 1 then leave igmLoop; end if;

							SET V1_Saldo=V1_Saldo-v_cantidad;



							SET V1_Total=Round(V1_Saldo*V1_Precio,2);

							SET v_Total=Round(V1_Precio *v_cantidad,2);







							INSERT INTO tblKardexvalor (d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,d11) VALUES (var_FechaSel,

																																											v_doc,

																																											'',

																																											'',

																																											'',

																																											Round(v_cantidad,2),

																																											Round(V1_Precio,2),

																																											Round(v_Total,2),

																																											V1_Saldo,

																																											Round(V1_Precio,2),

																																											Round(V1_Total,2));





    end loop igmLoop;

    close cur1;

		SET var_StockFinal=V1_Saldo;

		SET var_PrecioFinal=V1_Precio;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_KardexValorizadoSalidaVenta_UND` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_StockInical` FLOAT, IN `var_PrecioInicial` FLOAT, OUT `var_StockFinal` FLOAT, OUT `var_PrecioFinal` FLOAT)  BEGIN

		DECLARE V1_Saldo Float;

		DECLARE V1_Precio FLOAT;

		DECLARE V1_Total FLOAT;

		DECLARE v_cantidad  FLOAT;

		DECLARE v_Precio FLOAT;

		DECLARE v_Total FLOAT;

		declare v_doc varchar(100);

		DECLARE done INT DEFAULT FALSE;

		declare cur1 cursor for

						SELECT CONCAT(Ve_DocVentaTipoDoc.TipoDoc,' ' , Ve_DocVenta.Serie ,'-',Ve_DocVenta.Numero) AS DocMovimiento,Ve_DocVentaDet.Cantidad ,

						Ve_DocVentaDet.Precio,Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad

						FROM Ve_DocVenta

						Inner Join Ve_DocVentaDet On Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta

						INNER JOIN Ve_DocVentaTipoDoc On Ve_DocVentaTipoDoc.IdTipoDoc=Ve_DocVenta.IdTipoDoc

						WHERE convert(Ve_DocVenta.FechaDoc,date)=var_FechaSel and Ve_DocVentaDet.IdProducto in

						(SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto);



		declare continue handler for not found set done=1;



    set done = 0;

    open cur1;

		SET V1_Saldo=Round(var_StockInical,2);

		SET V1_Precio=Round(var_PrecioInicial,2);

		SET V1_Total=Round(V1_Saldo*V1_Precio,2);



    igmLoop: loop

        fetch cur1 into v_doc,v_cantidad,v_Precio,v_Total;

        if done = 1 then leave igmLoop; end if;

							SET V1_Saldo=V1_Saldo-v_cantidad;



							SET V1_Total=Round(V1_Saldo*V1_Precio,2);

							SET v_Total=Round(V1_Precio *v_cantidad,2);







							INSERT INTO tblKardexvalor (d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,d11) VALUES (var_FechaSel,

																																											v_doc,

																																											'',

																																											'',

																																											'',

																																											Round(v_cantidad,2),

																																											Round(V1_Precio,2),

																																											Round(v_Total,2),

																																											V1_Saldo,

																																											Round(V1_Precio,2),

																																											Round(V1_Total,2));





    end loop igmLoop;

    close cur1;

		SET var_StockFinal=V1_Saldo;

		SET var_PrecioFinal=V1_Precio;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_KardexValorizadoSalida_CAJA` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_StockInical` FLOAT, IN `var_PrecioInicial` FLOAT, OUT `var_StockFinal` FLOAT, OUT `var_PrecioFinal` FLOAT, IN `var_Tipo` INT)  BEGIN



		DECLARE V1_Saldo Float;



		DECLARE V1_Precio FLOAT;



		DECLARE V1_Total FLOAT;



		DECLARE v_cantidad  FLOAT;



		DECLARE v_Precio FLOAT;



		DECLARE v_Total FLOAT;



		declare v_doc varchar(100);



		DECLARE done INT DEFAULT FALSE;



		declare cur1 cursor for



						SELECT CONCAT(Lo_MovimientoTipo.TipoMovimiento,' ' , Lo_Movimiento.Serie ,'-',Lo_Movimiento.Numero) AS DocMovimiento,



								Lo_MovimientoDetalle.Cantidad*



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in  (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 ),



								Round((Lo_MovimientoDetalle.Precio/



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 )),2),



								Round(Lo_MovimientoDetalle.Cantidad*



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 )



								*Round((Lo_MovimientoDetalle.Precio/



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 )),2),2)



						FROM Lo_Movimiento



						Inner Join Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento



						INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo



						WHERE CASE WHEN var_Tipo=0 THEN Lo_Movimiento.MovimientoFecha=var_FechaSel ELSE Lo_Movimiento.FechaStock=var_FechaSel END and Lo_MovimientoDetalle.IdProducto in



						(SELECT Gen_ProductoDet.IdProducto FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet=(



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ))



						AND Lo_Movimiento.IdAlmacenOrigen>0;







		declare continue handler for not found set done=1;







    set done = 0;



    open cur1;



		SET V1_Saldo=Round(var_StockInical,2);



		SET V1_Precio=Round(var_PrecioInicial,2);



		SET V1_Total=Round(V1_Saldo*V1_Precio,2);







    igmLoop: loop



        fetch cur1 into v_doc,v_cantidad,v_Precio,v_Total;



        if done = 1 then leave igmLoop; end if;



							SET V1_Saldo=V1_Saldo-v_cantidad;







							SET V1_Total=Round(V1_Saldo*V1_Precio,2);



							SET v_Total=Round(V1_Precio *v_cantidad,2);















							INSERT INTO tblKardexvalor (d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,d11) VALUES (var_FechaSel,



																																											v_doc,



																																											'',



																																											'',



																																											'',



																																											Round(v_cantidad,2),



																																											Round(V1_Precio,2),



																																											Round(v_Total,2),



																																											V1_Saldo,



																																											Round(V1_Precio,2),



																																											Round(V1_Total,2));











    end loop igmLoop;



    close cur1;



		SET var_StockFinal=V1_Saldo;



		SET var_PrecioFinal=V1_Precio;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_KardexValorizadoSalida_UND` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_StockInical` FLOAT, IN `var_PrecioInicial` FLOAT, OUT `var_StockFinal` FLOAT, OUT `var_PrecioFinal` FLOAT, IN `var_Tipo` INT)  BEGIN



		DECLARE V1_Saldo Float;



		DECLARE V1_Precio FLOAT;



		DECLARE V1_Total FLOAT;



		DECLARE v_cantidad  FLOAT;



		DECLARE v_Precio FLOAT;



		DECLARE v_Total FLOAT;



		declare v_doc varchar(100);



		DECLARE done INT DEFAULT FALSE;



		declare cur1 cursor for



						SELECT CONCAT(Lo_MovimientoTipo.TipoMovimiento,' ' , Lo_Movimiento.Serie ,'-',Lo_Movimiento.Numero) AS DocMovimiento,Lo_MovimientoDetalle.Cantidad,



						Lo_MovimientoDetalle.Precio, Round(Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio,2)



						FROM Lo_Movimiento



						Inner Join Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento



						INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo



						WHERE CASE WHEN var_Tipo=0 THEN Lo_Movimiento.MovimientoFecha=var_FechaSel ELSE Lo_Movimiento.FechaStock=var_FechaSel END and Lo_MovimientoDetalle.IdProducto in



						(SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto)



						AND Lo_Movimiento.IdAlmacenOrigen>0;







		declare continue handler for not found set done=1;







    set done = 0;



    open cur1;



		SET V1_Saldo=Round(var_StockInical,2);



		SET V1_Precio=Round(var_PrecioInicial,2);



		SET V1_Total=Round(V1_Saldo*V1_Precio,2);







    igmLoop: loop



        fetch cur1 into v_doc,v_cantidad,v_Precio,v_Total;



        if done = 1 then leave igmLoop; end if;



							SET V1_Saldo=V1_Saldo-v_cantidad;







							SET V1_Total=Round(V1_Saldo*V1_Precio,2);



							SET v_Total=Round(V1_Precio *v_cantidad,2);















							INSERT INTO tblKardexvalor (d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,d11) VALUES (var_FechaSel,



																																											v_doc,



																																											'',



																																											'',



																																											'',



																																											Round(v_cantidad,2),



																																											Round(V1_Precio,2),



																																											Round(v_Total,2),



																																											V1_Saldo,



																																											Round(V1_Precio,2),



																																											Round(V1_Total,2));











    end loop igmLoop;



    close cur1;



		SET var_StockFinal=V1_Saldo;



		SET var_PrecioFinal=V1_Precio;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_Kardex_IngresosCAJA` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_Tipo` INT)  BEGIN



		DECLARE v_cantidad  FLOAT;



		declare v_doc varchar(100);



		DECLARE done INT DEFAULT FALSE;



		declare cur1 cursor for



						SELECT CONCAT(Lo_MovimientoTipo.TipoMovimiento,' ' , Lo_Movimiento.Serie ,'-',Lo_Movimiento.Numero) AS DocMovimiento,



								Lo_MovimientoDetalle.Cantidad*



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 )



						FROM Lo_Movimiento



						Inner Join Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento



						INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo



						WHERE CASE WHEN var_Tipo=0 THEN Lo_Movimiento.MovimientoFecha=var_FechaSel ELSE Lo_Movimiento.FechaStock=var_FechaSel END and Lo_MovimientoDetalle.IdProducto in



						(SELECT Gen_ProductoDet.IdProducto FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet=(



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ))



						AND Lo_Movimiento.IdAlmacenDestino>0;



		declare continue handler for not found set done=1;







    set done = 0;



    open cur1;
igmLoop: loop



        fetch cur1 into v_doc,v_cantidad;



        if done = 1 then leave igmLoop; end if;







							SET @SALDO=@SALDO+v_cantidad;



              INSERT INTO tblKardex (d1,d2,d3,d4,d5) VALUES (var_FechaSel,v_doc,v_cantidad,'',@SALDO);











    end loop igmLoop;



    close cur1;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_Kardex_IngresosUND` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_Tipo` INT)  BEGIN



		DECLARE v_cantidad  FLOAT;



		declare v_doc varchar(100);



		DECLARE done INT DEFAULT FALSE;



		declare cur1 cursor for



						SELECT CONCAT(Lo_MovimientoTipo.TipoMovimiento,' ' , Lo_Movimiento.Serie ,'-',Lo_Movimiento.Numero) AS DocMovimiento,Lo_MovimientoDetalle.Cantidad



						FROM Lo_Movimiento



						Inner Join Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento



						INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo



						WHERE CASE WHEN var_Tipo=0 THEN Lo_Movimiento.MovimientoFecha=var_FechaSel ELSE Lo_Movimiento.FechaStock=var_FechaSel END  and Lo_MovimientoDetalle.IdProducto in



						(SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto )



						AND Lo_Movimiento.IdAlmacenDestino>0;



		declare continue handler for not found set done=1;







    set done = 0;



    open cur1;
igmLoop: loop



        fetch cur1 into v_doc,v_cantidad;



        if done = 1 then leave igmLoop; end if;







							SET @SALDO=@SALDO+v_cantidad;



              INSERT INTO tblKardex (d1,d2,d3,d4,d5) VALUES (var_FechaSel,v_doc,v_cantidad,'',@SALDO);











    end loop igmLoop;



    close cur1;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_Kardex_SalidaCAJA` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_Tipo` INT)  BEGIN



		DECLARE v_cantidad  FLOAT;



		declare v_doc varchar(100);



		DECLARE done INT DEFAULT FALSE;



		declare cur1 cursor for



						SELECT CONCAT(Lo_MovimientoTipo.TipoMovimiento,' ' , Lo_Movimiento.Serie ,'-',Lo_Movimiento.Numero) AS DocMovimiento,



								Lo_MovimientoDetalle.Cantidad*



										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in  (



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ) and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto LIMIT 1 )



						FROM Lo_Movimiento



						Inner Join Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento



						INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo



						WHERE CASE WHEN var_Tipo=0 THEN Lo_Movimiento.MovimientoFecha=var_FechaSel ELSE Lo_Movimiento.FechaStock=var_FechaSel END and Lo_MovimientoDetalle.IdProducto in



						(SELECT Gen_ProductoDet.IdProducto FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet=(



										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ))



						AND Lo_Movimiento.IdAlmacenOrigen>0;



		declare continue handler for not found set done=1;







    set done = 0;



    open cur1;
igmLoop: loop



        fetch cur1 into v_doc,v_cantidad;



        if done = 1 then leave igmLoop; end if;







							SET @SALDO=@SALDO-v_cantidad;



              INSERT INTO tblKardex (d1,d2,d3,d4,d5) VALUES (var_FechaSel,v_doc,'',v_cantidad,@SALDO);











    end loop igmLoop;



    close cur1;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_Kardex_SalidaUND` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE, IN `var_Tipo` INT)  BEGIN



		DECLARE v_cantidad  FLOAT;



		declare v_doc varchar(100);



		DECLARE done INT DEFAULT FALSE;



		declare cur1 cursor for



						SELECT CONCAT(Lo_MovimientoTipo.TipoMovimiento,' ' , Lo_Movimiento.Serie ,'-',Lo_Movimiento.Numero) AS DocMovimiento,Lo_MovimientoDetalle.Cantidad



						FROM Lo_Movimiento



						Inner Join Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento



						INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo



						WHERE CASE WHEN var_Tipo=0 THEN Lo_Movimiento.MovimientoFecha=var_FechaSel ELSE Lo_Movimiento.FechaStock=var_FechaSel END and Lo_MovimientoDetalle.IdProducto in



						(SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto)



						AND Lo_Movimiento.IdAlmacenOrigen>0;



		declare continue handler for not found set done=1;







    set done = 0;



    open cur1;
igmLoop: loop



        fetch cur1 into v_doc,v_cantidad;



        if done = 1 then leave igmLoop; end if;







							SET @SALDO=@SALDO-v_cantidad;



              INSERT INTO tblKardex (d1,d2,d3,d4,d5) VALUES (var_FechaSel,v_doc,'',v_cantidad,@SALDO);











    end loop igmLoop;



    close cur1;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_Kardex_SalidaVentaCAJA` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE)  BEGIN

		DECLARE v_cantidad  FLOAT;

		declare v_doc varchar(100);

		DECLARE done INT DEFAULT FALSE;

		declare cur1 cursor for

						SELECT CONCAT(Ve_DocVentaTipoDoc.TipoDoc,' ' , Ve_DocVenta.Serie ,'-',Ve_DocVenta.Numero) AS DocMovimiento,

								Ve_DocVentaDet.Cantidad*

										(SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in  (

										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ) and Gen_ProductoDet.IdProducto=Ve_DocVentaDet.IdProducto LIMIT 1 )

						FROM Ve_DocVenta

						INNER JOIN Ve_DocVentaDet On Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta

						INNER JOIN Ve_DocVentaTipoDoc On Ve_DocVentaTipoDoc.IdTipoDoc=Ve_DocVenta.IdTipoDoc

						WHERE convert(Ve_DocVenta.FechaDoc,date)=var_FechaSel and Ve_DocVentaDet.IdProducto in

						(SELECT Gen_ProductoDet.IdProducto FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet in (

										SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto ));

		declare continue handler for not found set done=1;

    set done = 0;

    open cur1;
igmLoop: loop

        fetch cur1 into v_doc,v_cantidad;

        if done = 1 then leave igmLoop; end if;



							SET @SALDO=@SALDO-v_cantidad;

              INSERT INTO tblKardex (d1,d2,d3,d4,d5) VALUES (var_FechaSel,v_doc,'',v_cantidad,@SALDO);





    end loop igmLoop;

    close cur1;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_Kardex_SalidaVentaUND` (IN `var_Producto` VARCHAR(255), IN `var_FechaSel` DATE)  BEGIN



		DECLARE v_cantidad  FLOAT;



		declare v_doc varchar(100);



		DECLARE done INT DEFAULT FALSE;



		declare cur1 cursor for



						SELECT CONCAT(Ve_DocVentaTipoDoc.TipoDoc,' ' , Ve_DocVenta.Serie ,'-',Ve_DocVenta.Numero) AS DocMovimiento,Ve_DocVentaDet.Cantidad



						FROM Ve_DocVenta



						Inner Join Ve_DocVentaDet On Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta



						INNER JOIN Ve_DocVentaTipoDoc On Ve_DocVentaTipoDoc.IdTipoDoc=Ve_DocVenta.IdTipoDoc



						WHERE convert(Ve_DocVenta.FechaDoc,date)=var_FechaSel and Ve_DocVentaDet.IdProducto in



						(SELECT Gen_Producto.IdProducto FROM Gen_Producto WHERE Gen_Producto.Producto=var_Producto);



		declare continue handler for not found set done=1;







    set done = 0;



    open cur1;
igmLoop: loop



        fetch cur1 into v_doc,v_cantidad;



        if done = 1 then leave igmLoop; end if;







							SET @SALDO=@SALDO-v_cantidad;



              INSERT INTO tblKardex (d1,d2,d3,d4,d5) VALUES (var_FechaSel,v_doc,'',v_cantidad,@SALDO);











    end loop igmLoop;



    close cur1;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_ListarAlmacen` ()  BEGIN
SELECT * FROM Lo_Almacen;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_ListarMovimiento` ()  BEGIN
SELECT IdMovimientoTipo, TipoMovimiento, Tipo, VaRegCompra, CodSunat FROM Lo_MovimientoTipo;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_ListarProveedor` ()  BEGIN
SELECT * FROM Lo_Proveedor;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_MovimientoAnular` (IN `var_Hash` VARCHAR(255), IN `var_Anular` BIT)  BEGIN

		UPDATE Lo_Movimiento Set Lo_Movimiento.Anulado=var_Anular Where Lo_Movimiento.`Hash`=var_Hash;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_MovimientoDetGuardar` (IN `var_Hash` VARCHAR(255), IN `var_Producto` VARCHAR(255), IN `var_Cantidad` FLOAT, IN `var_TieneIgv` BIT, IN `var_Precio` FLOAT, IN `var_ISC` FLOAT, IN `var_FLETE` FLOAT, IN `var_IdLote` INT, IN `var_FechaVen` DATE)  BEGIN







	INSERT INTO Lo_MovimientoDetalle(hashMovimiento, IdProducto, Cantidad, TieneIgv, Precio, ISC, FLETE, IdLote, FechaVen)







	VALUES(







	var_Hash,







	(SELECT IdProducto FROM Gen_Producto WHERE Producto = var_Producto),







	var_Cantidad,







	var_TieneIgv,







	var_Precio,



var_ISC ,

var_FLETE ,

var_IdLote ,

var_FechaVen



	);







END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_MovimientoEliminar` (IN `var_Hash` VARCHAR(255))  BEGIN

		DELETE FROM Lo_MovimientoDetalle WHERE Lo_MovimientoDetalle.hashMovimiento=var_Hash;

		DELETE FROM Lo_Movimiento WHERE Lo_Movimiento.`Hash`=var_Hash;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_MovimientoGuardar` (IN `var_MovimientoTipo` VARCHAR(255), IN `var_Proveedor` VARCHAR(255), IN `var_Serie` VARCHAR(255), IN `var_Numero` INT, IN `var_Fecha` DATETIME, IN `var_AlmacenOrigen` INT, IN `var_AlmacenDestino` INT, IN `var_Observacion` TEXT, IN `var_Usuario` VARCHAR(255), IN `var_FechaStock` DATE, IN `var_Percepcion` FLOAT, IN `var_EsCredito` BIT, IN `fechaCredito` DATE, IN `var_FechaPeriodoT` INT)  BEGIN







	SET @Hash2=(SELECT UNIX_TIMESTAMP());







	INSERT INTO Lo_Movimiento(Lo_Movimiento.IdMovimientoTipo, Lo_Movimiento.IdProveedor, Lo_Movimiento.Serie, Lo_Movimiento.Numero, Lo_Movimiento.MovimientoFecha, Lo_Movimiento.IdAlmacenOrigen, Lo_Movimiento.IdAlmacenDestino, Lo_Movimiento.Observacion, Lo_Movimiento.Anulado, Lo_Movimiento.FechaReg, Lo_Movimiento.UsuarioReg, Lo_Movimiento.`Hash`, Lo_Movimiento.FechaStock, Lo_Movimiento.Percepcion, Lo_Movimiento.EsCredito, Lo_Movimiento.FechaVenCredito, Lo_Movimiento.FechaPeriodoTributario)



	VALUES(



		(SELECT IdMovimientoTipo FROM Lo_MovimientoTipo WHERE TipoMovimiento = var_MovimientoTipo),



		(SELECT IdProveedor FROM Lo_Proveedor WHERE Proveedor = var_Proveedor),



		var_Serie,



		var_Numero,



		var_Fecha,



		var_AlmacenOrigen,



		var_AlmacenDestino,



		var_Observacion,



		0,



		now(),



		var_Usuario,



		@Hash2,

		var_FechaStock,

		var_Percepcion,

	var_EsCredito ,

fechaCredito ,

var_FechaPeriodoT



	);







	SELECT Lo_Movimiento.`Hash` FROM Lo_Movimiento WHERE Lo_Movimiento.`Hash` = @Hash2;







END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_ProveedorGuardar` (IN `var_Proveedor` VARCHAR(255), IN `var_Ruc` VARCHAR(255), IN `var_Direccion` TEXT, IN `var_Observacion` TEXT, IN `var_Usuario` VARCHAR(255))  BEGIN
INSERT INTO Lo_Proveedor(Proveedor, Ruc, Direccion, Observacion, Anulado, FechaReg, UsuarioReg)
VALUES(
var_Proveedor,
var_Ruc,
var_Direccion,
var_Observacion,
0,
NOW(),
var_Usuario
);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_RegCompraContable` (IN `var_RegVenta` BIT, IN `var_PeriodoT` INT)  BEGIN



		SELECT



	Lo_Movimiento.`Hash` as IdMovimiento,



	Lo_Movimiento.MovimientoFecha,



	Lo_Movimiento.IdMovimientoTipo,



	Lo_MovimientoTipo.TipoMovimiento,



	Lo_Movimiento.Serie,



	Lo_Movimiento.Numero,



	Lo_Proveedor.Proveedor,



	CASE WHEN Lo_Movimiento.IdAlmacenOrigen>0 THEN



		(Select Lo_Almacen.Almacen From Lo_Almacen Where Lo_Almacen.IdAlmacen=Lo_Movimiento.IdAlmacenOrigen)



	ELSE



		'-'



	END AS AlmacenOrigen,



	CASE WHEN Lo_Movimiento.IdAlmacenDestino>0 THEN



		(Select Lo_Almacen.Almacen From Lo_Almacen Where Lo_Almacen.IdAlmacen=Lo_Movimiento.IdAlmacenDestino)



	ELSE



		'-'



	END AS AlmacenDestino,



	Lo_Movimiento.Observacion,



	Lo_Movimiento.Anulado,



	(SELECT



Sum(ROUND(Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio,2)) as SUBTOTAL



FROM



Lo_MovimientoDetalle



WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as SUBTOTAL,



(SELECT



Sum(CASE WHEN Lo_MovimientoDetalle.TieneIgv=1 THEN



	ROUND((Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio)*



	(Select Igv From GEN_EMPRESA),2)



ELSE



	0



END) as IGV



FROM



Lo_MovimientoDetalle



WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as IGV,



(SELECT



Sum(ROUND(Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio,2)) +



Sum(CASE WHEN Lo_MovimientoDetalle.TieneIgv=1 THEN



	ROUND((Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio)*



	(Select Igv From GEN_EMPRESA),2)



ELSE



	0



END) as TOTAL



FROM



Lo_MovimientoDetalle



WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as TOTAL,



	Lo_Movimiento.FechaReg,



	Lo_Movimiento.UsuarioReg,



	Lo_Movimiento.FechaMod,



	Lo_Movimiento.UsuarioMod,



(SELECT SUM(CASE WHEN ISNULL(Lo_MovimientoDetalle.ISC)=1 THEN  0 ELSE Lo_MovimientoDetalle.ISC END) FROM Lo_MovimientoDetalle WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as ISC,

(SELECT SUM(CASE WHEN ISNULL(Lo_MovimientoDetalle.FLETE)=1 THEN  0 ELSE Lo_MovimientoDetalle.FLETE END) FROM Lo_MovimientoDetalle WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as FLETE,

CASE WHEN ISNULL(Lo_Movimiento.Percepcion)=1 THEN 0 ELSE Lo_Movimiento.Percepcion END AS Percepcion









FROM



	Lo_Movimiento



	INNER JOIN Lo_MovimientoTipo On Lo_Movimiento.IdMovimientoTipo=Lo_MovimientoTipo.IdMovimientoTipo



	INNER JOIN Lo_Proveedor On Lo_Movimiento.IdProveedor=Lo_Proveedor.IdProveedor



WHERE



	Lo_MovimientoTipo.VaRegCompra=var_RegVenta and Lo_Movimiento.FechaPeriodoTributario = var_PeriodoT



ORDER BY Lo_MovimientoTipo.Tipo,Lo_Movimiento.IdProveedor ,Lo_Movimiento.Serie,Lo_Movimiento.Numero;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_RegMovimiento` (IN `var_RegVenta` BIT, IN `var_FechaIni` DATE, IN `var_FechaFin` DATE)  BEGIN



		SELECT



	Lo_Movimiento.`Hash` as IdMovimiento,



	Lo_Movimiento.MovimientoFecha,



	Lo_Movimiento.IdMovimientoTipo,



	Lo_MovimientoTipo.TipoMovimiento,



	Lo_Movimiento.Serie,



	Lo_Movimiento.Numero,

	Lo_Movimiento.FechaPeriodoTributario,



	Lo_Proveedor.Proveedor,



	CASE WHEN Lo_Movimiento.IdAlmacenOrigen>0 THEN



		(Select Lo_Almacen.Almacen From Lo_Almacen Where Lo_Almacen.IdAlmacen=Lo_Movimiento.IdAlmacenOrigen)



	ELSE



		'-'



	END AS AlmacenOrigen,



	CASE WHEN Lo_Movimiento.IdAlmacenDestino>0 THEN



		(Select Lo_Almacen.Almacen From Lo_Almacen Where Lo_Almacen.IdAlmacen=Lo_Movimiento.IdAlmacenDestino)



	ELSE



		'-'



	END AS AlmacenDestino,



	Lo_Movimiento.Observacion,



	Lo_Movimiento.Anulado,



	(SELECT



Sum(ROUND(Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio,2)) as SUBTOTAL



FROM



Lo_MovimientoDetalle



WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as SUBTOTAL,



(SELECT



Sum(CASE WHEN Lo_MovimientoDetalle.TieneIgv=1 THEN



	ROUND((Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio)*



	(Select Igv From GEN_EMPRESA),2)



ELSE



	0



END) as IGV



FROM



Lo_MovimientoDetalle



WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as IGV,



(SELECT



Sum(ROUND(Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio,2)) +



 Sum(ROUND(CASE WHEN ISNULL(Lo_MovimientoDetalle.ISC)=1 THEN  0 ELSE Lo_MovimientoDetalle.ISC END,2))



+ SUM(ROUND(CASE WHEN ISNULL(Lo_MovimientoDetalle.FLETE)=1 THEN  0 ELSE Lo_MovimientoDetalle.FLETE END,2))



+CASE WHEN ISNULL(Lo_Movimiento.Percepcion)=1 THEN 0 ELSE Lo_Movimiento.Percepcion END



+Sum(CASE WHEN Lo_MovimientoDetalle.TieneIgv=1 THEN



	ROUND((Lo_MovimientoDetalle.Cantidad*Lo_MovimientoDetalle.Precio)*



	(Select Igv From GEN_EMPRESA),2)



ELSE



	0



END) as TOTAL



FROM



Lo_MovimientoDetalle



WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as TOTAL,



	Lo_Movimiento.FechaReg,



	Lo_Movimiento.UsuarioReg,



	Lo_Movimiento.FechaMod,



	Lo_Movimiento.UsuarioMod,



(SELECT SUM(ROUND(CASE WHEN ISNULL(Lo_MovimientoDetalle.ISC)=1 THEN  0 ELSE Lo_MovimientoDetalle.ISC END,2)) FROM Lo_MovimientoDetalle WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as ISC,

(SELECT SUM(ROUND(CASE WHEN ISNULL(Lo_MovimientoDetalle.FLETE)=1 THEN  0 ELSE Lo_MovimientoDetalle.FLETE END,2)) FROM Lo_MovimientoDetalle WHERE Lo_MovimientoDetalle.hashMovimiento=Lo_Movimiento.`Hash`) as FLETE,

CASE WHEN ISNULL(Lo_Movimiento.Percepcion)=1 THEN 0 ELSE Lo_Movimiento.Percepcion END AS Percepcion









FROM



	Lo_Movimiento



	INNER JOIN Lo_MovimientoTipo On Lo_Movimiento.IdMovimientoTipo=Lo_MovimientoTipo.IdMovimientoTipo



	INNER JOIN Lo_Proveedor On Lo_Movimiento.IdProveedor=Lo_Proveedor.IdProveedor



WHERE



	Lo_MovimientoTipo.VaRegCompra=var_RegVenta and Lo_Movimiento.MovimientoFecha BETWEEN var_FechaIni and var_FechaFin





ORDER BY Lo_Movimiento.MovimientoFecha DESC;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_Stock` (IN `var_Almacen` VARCHAR(255), IN `Var_Producto` VARCHAR(255))  BEGIN



		DECLARE var_Fecha DATE;



		DECLARE v_IdProducto Int;



		DECLARE v_Marca varchar(255);



		DECLARE v_Categoria varchar(255);



		DECLARE v_FormaFarmaceutica varchar(255);



		DECLARE v_cantidad  FLOAT;



		declare v_Producto varchar(100);



		DECLARE done INT DEFAULT FALSE;















		declare cur1 cursor for



						Select IdProducto,Gen_ProductoMarca.ProductoMarca,Gen_ProductoCategoria.ProductoCategoria,Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica, Producto,0 as Stock



						from Gen_Producto



						Inner JOIN Gen_ProductoCategoria On Gen_Producto.IdProductoCategoria=Gen_ProductoCategoria.IdProductoCategoria



						Inner Join Gen_ProductoMarca On Gen_ProductoMarca.IdProductoMarca=Gen_Producto.IdProductoMarca



						Inner Join Gen_ProductoFormaFarmaceutica On Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica=Gen_Producto.IdProductoFormaFarmaceutica



						where Producto LIKE CONCAT('%',Var_Producto,'%') and Gen_Producto.ControlaStock=1;



		declare continue handler for not found set done=1;



		SET var_Fecha=(Select CURDATE());



		SET @Fecha=DATE_ADD(var_Fecha, INTERVAL 1 DAY);



    set done = 0;



		delete from prodstock;



    open cur1;
igmLoop: loop



        fetch cur1 into v_IdProducto,v_Marca,v_Categoria,v_FormaFarmaceutica,v_Producto,v_cantidad;



        if done = 1 then leave igmLoop; end if;



							CALL SbLo_StockIngresoUnd (var_Almacen,v_Producto,@Fecha,@IngresoUnd);



							CALL SbLo_StockSalidaUnd (var_Almacen,v_Producto,@Fecha,@SalidaUnd);







							CALL SbLo_StockIngresoCaja (var_Almacen,v_Producto,@Fecha,@IngresoCaja);



							CALL SbLo_StockSalidaCaja (var_Almacen,v_Producto,@Fecha,@SalidaCaja);







							CALL SbLo_StockDocVentaUnd (var_Almacen,v_Producto,@Fecha,@DocVentaUnd);



							CALL SbLo_StockDocVentaCaja (var_Almacen,v_Producto,@Fecha,@DocVentaCaja);



							Set @Ingresos=@IngresoUnd+@IngresoCaja;



							Set @Salidas=@SalidaCaja+@SalidaUnd+@DocVentaUnd+@DocVentaCaja;



							Set @Stock=@Ingresos-@Salidas;



							Insert into prodstock (IdProducto,ProductoMarca,ProductoCategoria,FormaFarmaceutica,Producto,Stock) values (v_IdProducto,v_Marca,v_Categoria,v_FormaFarmaceutica, v_Producto,@Stock);















    end loop igmLoop;



    close cur1;







		Select prodstock.IdProducto as numero, ProductoMarca as marca,ProductoCategoria as categoria,FormaFarmaceutica as formafarmaceutica, prodstock.Producto as Producto,Stock as stock ,

		Gen_Producto.PrecioContado,

		Gen_Producto.PrecioPorMayor,

		Gen_Producto.StockPorMayor,

		Gen_Producto.Codigo,

		Gen_Producto.VentaEstrategica,

		Gen_ProductoMedicion.ProductoMedicion

		from prodstock

		INNER JOIN Gen_Producto ON Gen_Producto.IdProducto = prodstock.IdProducto

		INNER JOIN Gen_ProductoMedicion ON Gen_ProductoMedicion.IdProductoMedicion = Gen_Producto.IdProductoMedicion

		where prodstock.Producto LIKE CONCAT('%',Var_Producto,'%') order by stock desc;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_StockAnt` (IN `var_Producto` VARCHAR(255), IN `var_FechaIni` DATE)  BEGIN
	Set @StockAnt=(SELECT


		CASE WHEN Gen_Producto.ControlaStock=TRUE THEN

		((SELECT IFNULL((SELECT

				SUM(Lo_MovimientoDetalle.Cantidad)

			FROM

				Lo_Movimiento

				INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento

				INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo


			WHERE

				Lo_MovimientoDetalle.IdProducto=Gen_Producto.IdProducto

				AND Lo_Movimiento.IdAlmacenDestino>0
				AND Lo_Movimiento.MovimientoFecha<var_FechaIni
				AND Lo_Movimiento.Anulado=false),0)) +



			(SELECT IFNULL((Select Sum(Lo_MovimientoDetalle.Cantidad)*

				(SELECT Gen_ProductoDet.Cantidad

					FROM Gen_ProductoDet

					WHERE Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto and Gen_ProductoDet.IdProductoDet=Gen_Producto.IdProducto)

			From Lo_Movimiento

			INNER JOIN Lo_MovimientoDetalle ON Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento

			where Lo_MovimientoDetalle.IdProducto In

					(Select Gen_ProductoDet.IdProducto FROM Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet=Gen_Producto.IdProducto)

			and Lo_Movimiento.Anulado=0
			AND Lo_Movimiento.IdAlmacenDestino>0
			AND Lo_Movimiento.MovimientoFecha<var_FechaIni
			),0)) ) -





		((SELECT IFNULL((SELECT

				SUM(Lo_MovimientoDetalle.Cantidad)

			FROM

				Lo_Movimiento

				INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento

				INNER JOIN Lo_MovimientoTipo On Lo_MovimientoTipo.IdMovimientoTipo=Lo_Movimiento.IdMovimientoTipo

			WHERE

				Lo_MovimientoDetalle.IdProducto=Gen_Producto.IdProducto

				AND Lo_Movimiento.IdAlmacenOrigen>0
				AND Lo_Movimiento.MovimientoFecha<var_FechaIni

				AND Lo_Movimiento.Anulado=false),0)) +



			(SELECT IFNULL((Select Sum(Lo_MovimientoDetalle.Cantidad)*

				(SELECT Gen_ProductoDet.Cantidad

					FROM Gen_ProductoDet

					WHERE Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto and Gen_ProductoDet.IdProductoDet=Gen_Producto.IdProducto)

			From Lo_Movimiento

			INNER JOIN Lo_MovimientoDetalle ON Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento

			where Lo_MovimientoDetalle.IdProducto In

					(Select Gen_ProductoDet.IdProducto FROM Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet=Gen_Producto.IdProducto)

			and Lo_Movimiento.Anulado=0
			AND Lo_Movimiento.IdAlmacenOrigen>0
			AND Lo_Movimiento.MovimientoFecha<var_FechaIni),0)) +



			(SELECT IFNULL((SELECT

			Sum(Ve_DocVentaDet.Cantidad)

			FROM Ve_DocVentaDet

			INNER JOIN Ve_DocVenta On Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta

			WHERE
			Ve_DocVenta.FechaDoc<var_FechaIni

			AND Ve_DocVentaDet.IdProducto=Gen_Producto.IdProducto),0)) +

					(SELECT IFNULL((Select

				Sum(Ve_DocVentaDet.Cantidad*

				(Select Cantidad From Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet=Gen_Producto.IdProducto and Gen_ProductoDet.IdProducto=Ve_DocVentaDet.IdProducto))

			From

				Ve_DocVentaDet

			INNER JOIN Ve_DocVenta On Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta

			WHERE Ve_DocVenta.FechaDoc<var_FechaIni and

				Ve_DocVentaDet.IdProducto in

				(Select IdProducto From Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet=Gen_Producto.IdProducto)),0)))

			ELSE '-' END as STOCK


	FROM
		Gen_Producto
		INNER JOIN Gen_ProductoCategoria On Gen_ProductoCategoria.IdProductoCategoria=Gen_Producto.IdProductoCategoria
	WHERE
		Gen_Producto.Anulado=false
		AND Gen_Producto.ControlaStock=TRUE
		AND Gen_Producto.Producto=var_Producto
	ORDER BY
		Gen_ProductoCategoria.ProductoCategoria,Gen_Producto.Producto);


	INSERT INTO tblKardex (d2,d5) VALUES ('SALDO ANTERIOR',@StockAnt);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_StockDocVentaCaja` (IN `var_Almacen` VARCHAR(255), IN `Var_Producto` VARCHAR(255), IN `var_Fecha` DATE, OUT `var_Cantidad` FLOAT)  BEGIN

		Set @IdProducto=(SELECT IdProducto From Gen_Producto Where Gen_Producto.Producto=Var_Producto);



		Set @Cantidad=(Select IFNULL((Select

											Sum(Ve_DocVentaDet.Cantidad*IFNULL((SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet= @IdProducto and Gen_ProductoDet.IdProducto=Ve_DocVentaDet.IdProducto ),0))

									FROM Ve_DocVenta

									INNER JOIN Ve_DocVentaDet On Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta

									WHERE

										Ve_DocVenta.IdAlmacen in (SELECT IdAlmacen From Lo_Almacen Where Almacen LIKE CONCAT('%',

                           var_Almacen,

                           '%'))

										and Ve_DocVentaDet.IdProducto in

														(SELECT Gen_ProductoDet.IdProducto FROM Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet= @IdProducto )

										and Ve_DocVenta.Anulado=0

										and Ve_DocVenta.FechaDoc<var_Fecha),0));

		SET var_Cantidad=@Cantidad;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_StockDocVentaUnd` (IN `var_Almacen` VARCHAR(255), IN `Var_Producto` VARCHAR(255), IN `var_Fecha` DATE, OUT `var_Cantidad` FLOAT)  BEGIN

		Set @IdProducto=(SELECT IdProducto From Gen_Producto Where Gen_Producto.Producto=Var_Producto);



		Set @Cantidad=(Select IFNULL((Select

											Sum(Ve_DocVentaDet.Cantidad)

									FROM Ve_DocVenta

									INNER JOIN Ve_DocVentaDet On Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta

									WHERE

										Ve_DocVenta.IdAlmacen in (SELECT IdAlmacen From Lo_Almacen Where Almacen LIKE CONCAT('%',

                           var_Almacen,

                           '%'))

										and Ve_DocVentaDet.IdProducto =@IdProducto

										and Ve_DocVenta.Anulado=0

										and Ve_DocVenta.FechaDoc<var_Fecha),0));

		SET var_Cantidad=@Cantidad;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_StockIngresoCaja` (IN `var_Almacen` VARCHAR(255), IN `Var_Producto` VARCHAR(255), IN `var_Fecha` DATE, OUT `var_Cantidad` FLOAT)  BEGIN

		Set @IdProducto=(SELECT IdProducto From Gen_Producto Where Gen_Producto.Producto=Var_Producto);



		Set @Cantidad=(Select IFNULL((Select

											Sum(Lo_MovimientoDetalle.Cantidad*IFNULL((SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet= @IdProducto and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto ),0))

									FROM Lo_Movimiento

									INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento

									WHERE

										Lo_Movimiento.IdAlmacenDestino in (SELECT IdAlmacen From Lo_Almacen Where Almacen LIKE CONCAT('%',

                           var_Almacen,

                           '%'))

										and Lo_MovimientoDetalle.IdProducto in

														(SELECT Gen_ProductoDet.IdProducto FROM Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet= @IdProducto )

										and Lo_Movimiento.Anulado=0

										and Lo_Movimiento.MovimientoFecha<var_Fecha),0));

		SET var_Cantidad=@Cantidad;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_StockIngresosUnd` (IN `var_Almacen` VARCHAR(255), IN `Var_Producto` VARCHAR(255), IN `var_Fecha` DATE)  BEGIN

		set @Fecha=DATE_ADD(var_Fecha, INTERVAL 1 DAY);

		Set @IdProducto=(SELECT IdProducto From Gen_Producto Where Gen_Producto.Producto=Var_Producto);



		Set @Cantidad=(Select

											Sum(Lo_MovimientoDetalle.Cantidad)

									FROM Lo_Movimiento

									INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento

									WHERE

										Lo_Movimiento.IdAlmacenDestino in (SELECT IdAlmacen From Lo_Almacen Where Almacen LIKE CONCAT('%',

                           var_Almacen,

                           '%'))

										and Lo_MovimientoDetalle.IdProducto=@IdProducto

										and Lo_Movimiento.Anulado=0

										and Lo_Movimiento.MovimientoFecha<@Fecha);



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_StockIngresoUnd` (IN `var_Almacen` VARCHAR(255), IN `Var_Producto` VARCHAR(255), IN `var_Fecha` DATE, OUT `var_Cantidad` FLOAT)  BEGIN

		Set @IdProducto=(SELECT IdProducto From Gen_Producto Where Gen_Producto.Producto=Var_Producto);



		Set @Cantidad=(Select IFNULL((Select

											Sum(Lo_MovimientoDetalle.Cantidad)

									FROM Lo_Movimiento

									INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento

									WHERE

										Lo_Movimiento.IdAlmacenDestino in (SELECT IdAlmacen From Lo_Almacen Where Almacen LIKE CONCAT('%',

                           var_Almacen,

                           '%'))

										and Lo_MovimientoDetalle.IdProducto=@IdProducto

										and Lo_Movimiento.Anulado=0

										and Lo_Movimiento.MovimientoFecha<var_Fecha),0));

		SET var_Cantidad=@Cantidad;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_StockSalidaCaja` (IN `var_Almacen` VARCHAR(255), IN `Var_Producto` VARCHAR(255), IN `var_Fecha` DATE, OUT `var_Cantidad` FLOAT)  BEGIN

		Set @IdProducto=(SELECT IdProducto From Gen_Producto Where Gen_Producto.Producto=Var_Producto);



		Set @Cantidad=(Select IFNULL((Select

											Sum(Lo_MovimientoDetalle.Cantidad*IFNULL((SELECT Gen_ProductoDet.Cantidad FROM Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet= @IdProducto and Gen_ProductoDet.IdProducto=Lo_MovimientoDetalle.IdProducto ),0))

									FROM Lo_Movimiento

									INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento

									WHERE

										Lo_Movimiento.IdAlmacenOrigen in (SELECT IdAlmacen From Lo_Almacen Where Almacen LIKE CONCAT('%',

                           var_Almacen,

                           '%'))

										and Lo_MovimientoDetalle.IdProducto in

														(SELECT Gen_ProductoDet.IdProducto FROM Gen_ProductoDet Where Gen_ProductoDet.IdProductoDet= @IdProducto )

										and Lo_Movimiento.Anulado=0

										and Lo_Movimiento.MovimientoFecha<var_Fecha),0));

		SET var_Cantidad=@Cantidad;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_StockSalidaUnd` (IN `var_Almacen` VARCHAR(255), IN `Var_Producto` VARCHAR(255), IN `var_Fecha` DATE, OUT `var_Cantidad` FLOAT)  BEGIN

		Set @IdProducto=(SELECT IdProducto From Gen_Producto Where Gen_Producto.Producto=Var_Producto);



		Set @Cantidad=(Select IFNULL((Select

											Sum(Lo_MovimientoDetalle.Cantidad)

									FROM Lo_Movimiento

									INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento

									WHERE

										Lo_Movimiento.IdAlmacenOrigen in (SELECT IdAlmacen From Lo_Almacen Where Almacen LIKE CONCAT('%',

                           var_Almacen,

                           '%'))

										and Lo_MovimientoDetalle.IdProducto=@IdProducto

										and Lo_Movimiento.Anulado=0

										and Lo_Movimiento.MovimientoFecha<var_Fecha),0));

		SET var_Cantidad=@Cantidad;





END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbLo_StockValoriado` (IN `Var_Producto` VARCHAR(255), IN `Var_StockInical` FLOAT, IN `Var_PrecioInicial` FLOAT, IN `var_anno` INT, IN `var_Tipo` INT)  BEGIN







	DECLARE v_Fecha DATE;



	DECLARE V1_Precio Float;



	DECLARE V1_Saldo FLOAT;



	DECLARE V1_Total Float;



		DECLARE done INT DEFAULT FALSE;







		declare cur1 cursor for



						SELECT t.FechaDoc



						FROM



						(SELECT case when var_Tipo=0 THEN date(Lo_Movimiento.MovimientoFecha) ELSE date(Lo_Movimiento.FechaStock) END as FechaDoc



						FROM Lo_Movimiento



						GROUP BY Lo_Movimiento.MovimientoFecha,Lo_Movimiento.FechaStock



						UNION



						SELECT DAte(Ve_DocVenta.FechaDoc) as FechaDoc



						FROM Ve_DocVenta



						GROUP BY Ve_DocVenta.FechaDoc) t ;



		declare continue handler for not found set done=1;



		open cur1;



		CALL SbLo_KardexValorizadoIniciar(Var_Producto,var_anno,Var_StockInical,Var_PrecioInicial);



		SET V1_Saldo=Round(Var_StockInical,2);



		SET V1_Precio=Round(Var_PrecioInicial,2);



		SET V1_Total=Round(V1_Saldo*V1_Precio,2);



    igmLoop: loop



        fetch cur1 into v_Fecha;



        if done = 1 then leave igmLoop; end if;







				CALL SbLo_KardexValorizadoINGRESOS(Var_Producto,v_Fecha,V1_Saldo,V1_Precio,V1_Saldo,V1_Precio,var_Tipo);



				CALL SbLo_KardexValorizadoINGRESOS_Caja(Var_Producto,v_Fecha,V1_Saldo,V1_Precio,V1_Saldo,V1_Precio,var_Tipo);



				CALL SbLo_KardexValorizadoSalida_UND(Var_Producto,v_Fecha,V1_Saldo,V1_Precio,V1_Saldo,V1_Precio,var_Tipo);



				CALL SbLo_KardexValorizadoSalida_CAJA(Var_Producto,v_Fecha,V1_Saldo,V1_Precio,V1_Saldo,V1_Precio,var_Tipo);



				CALL SbLo_KardexValorizadoSalidaVenta_UND(Var_Producto,v_Fecha,V1_Saldo,V1_Precio,V1_Saldo,V1_Precio);



				CALL SbLo_KardexValorizadoSalidaVenta_CAJA(Var_Producto,v_Fecha,V1_Saldo,V1_Precio,V1_Saldo,V1_Precio);























    end loop igmLoop;



    close cur1;























	Select * From tblKardexvalor;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbProductoModificar` (IN `var_IdProducto` INT, IN `var_ProductoMarca` VARCHAR(255), IN `var_ProductoFormaFarmaceutica` VARCHAR(255), IN `var_ProductoMedicion` VARCHAR(255), IN `var_ProductoCategoria` VARCHAR(255), IN `var_Producto` VARCHAR(255), IN `var_ProductoDesc` TEXT, IN `var_ProductoDescCorto` VARCHAR(255), IN `var_CodigoBarra` VARCHAR(255), IN `var_Codigo` VARCHAR(255), IN `var_Dosis` VARCHAR(255), IN `var_PrecioContado` FLOAT, IN `var_PrecioXMayor` FLOAT, IN `var_StockXMayor` FLOAT, IN `var_ControlStock` BIT(1), IN `var_StockMinimo` FLOAT, IN `var_Usuario` VARCHAR(255), IN `var_PrecioCosto` FLOAT, IN `var_VentaEstrategica` BIT, IN `var_PrecioUtilidad` FLOAT, IN `var_Bloque` VARCHAR(255))  BEGIN







UPDATE Gen_Producto



SET



		IdProductoMarca=(Select IdProductoMarca From Gen_ProductoMarca Where ProductoMarca=var_ProductoMarca LIMIT 1),



		IdProductoFormaFarmaceutica=(Select Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica From Gen_ProductoFormaFarmaceutica Where ProductoFormaFarmaceutica=var_ProductoFormaFarmaceutica LIMIT 1),



		IdProductoMedicion=(Select IdProductoMedicion From Gen_ProductoMedicion Where ProductoMedicion=var_ProductoMedicion LIMIT 1),



		IdProductoCategoria=(Select Gen_ProductoCategoria.IdProductoCategoria From Gen_ProductoCategoria Where Gen_ProductoCategoria.ProductoCategoria=var_ProductoCategoria LIMIT 1),



		Producto=var_Producto,



		ProductoDesc=var_ProductoDesc,



		ProductoDescCorto=var_ProductoDescCorto,



		Dosis=var_Dosis,



		Codigo=var_Codigo,



		CodigoBarra=var_CodigoBarra,



    PrecioContado=var_PrecioContado,



		PrecioPorMayor=var_PrecioXMayor,



		StockPorMayor=var_StockXMayor,



		ControlaStock = var_ControlStock,



		StockMinimo = var_StockMinimo,



		PrecioCosto = var_PrecioCosto,



		VentaEstrategica = var_VentaEstrategica,



		PorcentajeUtilidad = var_PrecioUtilidad,



		IdBloque = (Select Gen_ProductoBloque.IdBloque from Gen_ProductoBloque where Gen_ProductoBloque.Bloque = var_Bloque),



		Anulado=0,



		FechaMod=Now(),



		UsuarioMod=var_Usuario



WHERE



		Gen_Producto.IdProducto=var_IdProducto;







END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbSeg_ListarUsuarioPerfil` ()  BEGIN



SELECT

Seg_Usuario.Usuario,

Seg_UsuarioPerfil.UsuarioPerfil

FROM

Seg_Usuario

INNER JOIN Seg_UsuarioPerfil ON Seg_Usuario.IdUsuarioPerfil = Seg_UsuarioPerfil.IdUsuarioPerfil;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_BuscarProductoXCompuesto` (IN `var_CriterioBuscar` VARCHAR(255))  BEGIN
SELECT
Gen_Producto.IdProducto,
Gen_ProductoMarca.ProductoMarca,
Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica,
Gen_ProductoMedicion.ProductoMedicion,
Gen_ProductoCategoria.ProductoCategoria,
Gen_Producto.Producto,
Gen_Producto.ProductoDesc,
Gen_Producto.ProductoDescCorto,
Gen_Producto.CodigoBarra,
Gen_Producto.Codigo,
Gen_Producto.Dosis,
Gen_Producto.PrecioContado,
Gen_Producto.PrecioPorMayor,
Gen_Producto.StockPorMayor
FROM
Gen_ProductoCompuesto
INNER JOIN Gen_ProductoCompuestoDet ON Gen_ProductoCompuestoDet.Gen_ProductoCompuesto_IdProductoCompuesto = Gen_ProductoCompuesto.IdProductoCompuesto
INNER JOIN Gen_Producto ON Gen_ProductoCompuestoDet.Gen_Producto_IdProducto = Gen_Producto.IdProducto
INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
INNER JOIN Gen_ProductoFormaFarmaceutica ON Gen_Producto.IdProductoFormaFarmaceutica = Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica
INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
WHERE Gen_ProductoCompuesto.ProductoCompuesto like CONCAT('%', var_CriterioBuscar, '%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_BuscarTratamiento` (IN `var_Diagnostico` VARCHAR(255), IN `var_Edad` INT)  BEGIN



SELECT DISTINCT







Ve_ExpertoTratamiento.NroDias,



Ve_ExpertoTratamiento.TomasXDia,

Ve_ExpertoTratamiento.DosisXPeso,

Ve_ExpertoTratamiento.Concentracion,



Gen_ProductoCompuesto.ProductoCompuesto



FROM



Ve_ExpertoTratamiento



INNER JOIN Gen_ProductoCompuesto ON Gen_ProductoCompuesto.IdProductoCompuesto = Ve_ExpertoTratamiento.IdCompuesto



INNER JOIN Gen_ProductoCompuestoDet ON Gen_ProductoCompuestoDet.Gen_ProductoCompuesto_IdProductoCompuesto = Gen_ProductoCompuesto.IdProductoCompuesto



INNER JOIN Ve_ExpertoDiagnostico ON Ve_ExpertoDiagnostico.IdDiagnostico = Ve_ExpertoTratamiento.IdDiagnostico



WHERE



Ve_ExpertoDiagnostico.Diagnostico = var_Diagnostico AND Ve_ExpertoDiagnostico.Edad >= var_Edad;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_DocVentaAnular` (IN `var_IdDocVenta` INT)  BEGIN



		UPDATE Ve_DocVenta Set Ve_DocVenta.Anulado=1 Where Ve_DocVenta.idDocVenta=var_IdDocVenta;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_DocVentaEliminar` (IN `var_IdDocVenta` INT)  BEGIN

		DELETE FROM Ve_DocVentaDet WHERE Ve_DocVentaDet.IdDocVenta=var_IdDocVenta;

		DELETE FROM Ve_DocVenta WHERE Ve_DocVenta.idDocVenta=var_IdDocVenta;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ExpertoDiagnosticoBuscar` (IN `var_CriterioEdadBuscar` INT, IN `var_CriterioBuscar` VARCHAR(255))  BEGIN
SELECT
Ve_ExpertoDiagnostico.IdDiagnostico,
Ve_ExpertoDiagnostico.Diagnostico,
Ve_ExpertoDiagnostico.Problema,
Ve_ExpertoDiagnostico.Edad,
Ve_ExpertoDiagnostico.FechaReg,
Ve_ExpertoDiagnostico.UsuarioReg,
Ve_ExpertoDiagnostico.FechaMod,
Ve_ExpertoDiagnostico.UsuarioMod
FROM
Ve_ExpertoDiagnostico
WHERE Ve_ExpertoDiagnostico.Diagnostico like CONCAT('%', var_CriterioBuscar, '%') and Ve_ExpertoDiagnostico.Edad<var_CriterioEdadBuscar
Order by Ve_ExpertoDiagnostico.Edad LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ExpertoDiagnosticoGuardar` (IN `var_Diagnostico` VARCHAR(255), IN `var_Problema` TEXT, IN `var_Edad` INT, IN `var_Obs` TEXT, IN `var_UsuarioReg` VARCHAR(255))  BEGIN
SET @Hash2=(Select UNIX_TIMESTAMP());
INSERT INTO Ve_ExpertoDiagnostico(Ve_ExpertoDiagnostico.Diagnostico, Ve_ExpertoDiagnostico.Problema, Ve_ExpertoDiagnostico.Edad, Ve_ExpertoDiagnostico.Observacion, Ve_ExpertoDiagnostico.FechaReg, Ve_ExpertoDiagnostico.UsuarioReg, Ve_ExpertoDiagnostico.Hash)
	VALUES (
		var_Diagnostico,
		var_Problema,
		var_Edad,
        var_Obs,
		NOW(),
		var_UsuarioReg,
		@Hash2
);
SELECT Ve_ExpertoDiagnostico.IdDiagnostico FROM Ve_ExpertoDiagnostico WHERE Ve_ExpertoDiagnostico.Hash = @Hash2;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ExpertoDiagnosticoModificar` (IN `var_IdDiagnostico` INT, IN `var_Diagnostico` VARCHAR(255), IN `var_Problema` TEXT, IN `var_Edad` INT, IN `var_Obs` TEXT, IN `var_Usuario` VARCHAR(255))  BEGIN



UPDATE Ve_ExpertoDiagnostico SET

Ve_ExpertoDiagnostico.Diagnostico = var_Diagnostico,

Ve_ExpertoDiagnostico.Problema = var_Problema,

Ve_ExpertoDiagnostico.Edad = var_Edad,

Ve_ExpertoDiagnostico.Observacion = var_Obs,

Ve_ExpertoDiagnostico.UsuarioMod = var_Usuario,

Ve_ExpertoDiagnostico.FechaMod = NOW()

WHERE Ve_ExpertoDiagnostico.IdDiagnostico = var_IdDiagnostico;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ExpertoDiagnosticoSintomaDet` (IN `var_Diagnostico` INT, IN `var_Sintoma` INT, IN `var_UsuarioReg` VARCHAR(255))  BEGIN
INSERT INTO Ve_ExpertoDiagnosticoSintomaDet(Ve_ExpertoDiagnosticoSintomaDet.IdDiagnostico, Ve_ExpertoDiagnosticoSintomaDet.IdSintoma, Ve_ExpertoDiagnosticoSintomaDet.Fechareg, Ve_ExpertoDiagnosticoSintomaDet.UsuarioReg)
	VALUES (
	var_Diagnostico,
	var_Sintoma,
	NOW(),
	var_UsuarioReg);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ExpertoDiagnosticoXSintomaBuscar` (IN `var_EdadBuscar` INT, IN `var_CriterioSintomaBuscar` TEXT, IN `var_Numero` INT)  BEGIN











set @Ssql:=CONCAT('SELECT



Ve_ExpertoDiagnostico.IdDiagnostico,



Ve_ExpertoDiagnostico.Diagnostico,



Ve_ExpertoDiagnostico.Problema,



Ve_ExpertoDiagnostico.Edad,



Ve_ExpertoDiagnostico.Observacion



FROM



Ve_ExpertoDiagnostico



INNER JOIN Ve_ExpertoDiagnosticoSintomaDet ON Ve_ExpertoDiagnostico.IdDiagnostico = Ve_ExpertoDiagnosticoSintomaDet.IdDiagnostico



INNER JOIN Ve_ExpertoSintoma ON Ve_ExpertoDiagnosticoSintomaDet.IdSintoma=Ve_ExpertoSintoma.Idsintoma



 WHERE Ve_ExpertoDiagnostico.Edad >=',var_EdadBuscar,' and Ve_ExpertoSintoma.Sintoma in (',var_CriterioSintomaBuscar,')

GROUP BY

Ve_ExpertoDiagnostico.IdDiagnostico,



Ve_ExpertoDiagnostico.Diagnostico,



Ve_ExpertoDiagnostico.Problema,



Ve_ExpertoDiagnostico.Edad,



Ve_ExpertoDiagnostico.Observacion

HAVING Count(Ve_ExpertoDiagnostico.IdDiagnostico)=',var_Numero ,'

ORder by Ve_ExpertoDiagnostico.Edad;');



PREPARE myquery FROM @Ssql;



EXECUTE myquery;







END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ExpertoSintomaBuscar` (IN `var_CriterioBuscar` VARCHAR(255))  BEGIN

set @criterio:=CONCAT("'%", var_CriterioBuscar, "%'");



set @Ssql:=CONCAT('SELECT

Ve_ExpertoSintoma.IdSintoma,

Ve_ExpertoSintoma.Sintoma

FROM

Ve_ExpertoSintoma

WHERE (Ve_ExpertoSintoma.Sintoma like', @criterio,')  ;');

PREPARE myquery FROM @Ssql;

EXECUTE myquery;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ExpertoTratamientoActualizarD` (IN `var_Tratamiento` INT, IN `var_Diagnostico` INT)  BEGIN
UPDATE Ve_ExpertoTratamiento SET Ve_ExpertoTratamiento.IdDiagnostico = var_Diagnostico
	WHERE Ve_ExpertoTratamiento.IdTratamiento = var_Tratamiento;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_GuardarDocVenta` (IN `var_PuntoVenta` VARCHAR(255), IN `var_TipoDocVenta` VARCHAR(255), IN `var_Cliente` VARCHAR(255), IN `var_Almacen` VARCHAR(255), IN `var_FechaDoc` DATETIME, IN `var_Usuario` VARCHAR(255), IN `var_Credito` BIT, IN `var_FechaCredito` DATETIME, IN `var_Serie` VARCHAR(45))  BEGIN



SET @Hash2=(Select UNIX_TIMESTAMP());







INSERT INTO Ve_DocVenta (IdDocVentaPuntoVenta,IdCliente,IdTipoDoc,IdAlmacen,Serie,Numero,FechaDoc,Anulado,FechaReg,UsuarioReg,Hash, EsCredito, FechaCredito)



VALUES (



				(Select IdDocVentaPuntoVenta From Ve_DocVentaPuntoVenta Where Ve_DocVentaPuntoVenta.PuntoVenta=var_PuntoVenta),



				(Select IdCliente From Ve_DocVentaCliente Where Ve_DocVentaCliente.Cliente=var_Cliente),



				(Select IdTipoDoc From Ve_DocVentaTipoDoc Where Ve_DocVentaTipoDoc.TipoDoc=var_TipoDocVenta),



				(Select IdAlmacen From Lo_Almacen Where Lo_Almacen.Almacen=var_Almacen),



				var_Serie,



				(Select Ifnull((Select VE.Numero From Ve_DocVenta VE WHERE VE.Serie=var_Serie and VE.IdTipoDoc=var_TipoDocVenta ORDER BY VE.Numero DESC limit 1),0))+1,



				var_FechaDoc,0,now(),var_Usuario,@Hash2, var_Credito, var_FechaCredito );



Select IdDocVenta From Ve_DocVenta Where Hash=@Hash2;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_GuardarDocVentaDet` (IN `var_IdDocVenta` INT, IN `var_IdProducto` INT, IN `var_Cant` FLOAT, IN `var_Precio` FLOAT)  BEGIN

INSERT INTO Ve_DocVentaDet (IdDocVenta,IdProducto,Cantidad,Precio)
VALUES
				(var_IdDocVenta,
				var_IdProducto,
				var_Cant,
				var_Precio);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_GuardarMetodoPagoDet` (IN `var_IdDocVenta` INTEGER, IN `var_MetodoPago` VARCHAR(255), IN `var_Importe` FLOAT, IN `var_NroTarjeta` VARCHAR(255))  BEGIN

INSERT INTO Ve_DocVentaMetodoPagoDet (IdDocVenta,IdMetodoPago,Importe,NroTarjeta)
VALUES (
				var_IdDocVenta,
				(Select IdMetodoPago From Ve_DocVentaMetodoPago Where MetodoPago=var_MetodoPago),
				var_Importe,
				var_NroTarjeta);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_GuardarTratamiento` (IN `var_IdTratamiento` INT, IN `var_Diagnostico` VARCHAR(255), IN `var_Compuesto` VARCHAR(255), IN `var_Observacion` VARCHAR(255), IN `var_TomaXDia` INT, IN `var_NroDia` INT, IN `var_DiasXPeso` FLOAT, IN `var_CantSol` FLOAT, IN `var_Usuario` VARCHAR(255), IN `var_UnidadXPeso` VARCHAR(255))  BEGIN



SET @Hash2=(Select UNIX_TIMESTAMP());



IF var_IdTratamiento=0 THEN



	INSERT INTO Ve_ExpertoTratamiento (IdDiagnostico,IdCompuesto,Observacion,TomasXDia,NroDias,FechaReg, DosisXPeso, UnidadDosisXPeso, Concentracion,UsuarioReg,Hash)



	VALUES (



				0,



				(Select IdProductoCompuesto From Gen_ProductoCompuesto Where Gen_ProductoCompuesto.ProductoCompuesto=var_Compuesto),



				var_Observacion,



				var_TomaXDia,var_NroDia,now(), var_DiasXPeso,var_UnidadXPeso , var_CantSol ,var_Usuario,@Hash2);



ELSE



	UPDATE Ve_ExpertoTratamiento



	Set





			IdCompuesto=(Select Gen_ProductoCompuesto.IdProductoCompuesto From Gen_ProductoCompuesto Where Gen_ProductoCompuesto.ProductoCompuesto=var_Compuesto),



		Observacion=var_Observacion,TomasXDia=var_TomaXDia,NroDias=var_NroDia,FechaMod=NOW(), DosisXPeso=var_DiasXPeso, UnidadDosisXPeso = var_UnidadXPeso, Concentracion= var_CantSol, UsuarioMod=var_Usuario



	WHERE Ve_ExpertoTratamiento.IdTratamiento=var_IdTratamiento;



END IF;



Select Ve_ExpertoTratamiento.IdTratamiento,



CONCAT(Gen_ProductoCompuesto.ProductoCompuesto," NroDias (",CONVERT(Ve_ExpertoTratamiento.NroDias,CHAR),") NroTomas (",CONVERT(Ve_ExpertoTratamiento.TomasXDia,CHAR) , ")") as Tratamiento,

Gen_ProductoCompuesto.ProductoCompuesto,

Ve_ExpertoTratamiento.NroDias,

Ve_ExpertoTratamiento.TomasXDia,

Ve_ExpertoTratamiento.Observacion,

Ve_ExpertoTratamiento.DosisXPeso,

Ve_ExpertoTratamiento.Concentracion,

Ve_ExpertoTratamiento.UnidadDosisXPeso



From Ve_ExpertoTratamiento



Inner Join Gen_ProductoCompuesto On Ve_ExpertoTratamiento.IdCompuesto=Gen_ProductoCompuesto.IdProductoCompuesto



Where Ve_ExpertoTratamiento.Hash=@Hash2;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ListarCierre` ()  BEGIN

SELECT

Ve_DocVenta.FechaDoc,

Ve_DocVentaTipoDoc.TipoDoc,

Ve_DocVenta.Serie,

Ve_DocVenta.Numero,

Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio as Total,

Ve_DocVenta.EsCredito

FROM Ve_DocVenta

INNER JOIN Ve_DocVentaDet ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta

INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVentaTipoDoc.IdTipoDoc = Ve_DocVenta.IdTipoDoc

WHERE Ve_DocVenta.IdCierre IS NULL;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ListarCompuestoXDiagnostico` (IN `var_Diagnostico` INT)  BEGIN

SELECT

Ve_ExpertoSintoma.IdSintoma,

Ve_ExpertoSintoma.Sintoma

FROM

Ve_ExpertoDiagnosticoSintomaDet

INNER JOIN Ve_ExpertoSintoma ON Ve_ExpertoDiagnosticoSintomaDet.IdSintoma = Ve_ExpertoSintoma.IdSintoma

WHERE

Ve_ExpertoDiagnosticoSintomaDet.IdDiagnostico = `var_Diagnostico` ;





END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ListarCompuestoXProducto` (IN `var_IdProducto` INT)  BEGIN
SELECT Gen_ProductoCompuesto.IdProductoCompuesto, Gen_ProductoCompuesto.ProductoCompuesto
FROM Gen_ProductoCompuesto
INNER JOIN Gen_ProductoCompuestoDet ON  Gen_ProductoCompuestoDet.Gen_ProductoCompuesto_IdProductoCompuesto = Gen_ProductoCompuesto.IdProductoCompuesto
INNER JOIN Gen_Producto ON Gen_Producto.IdProducto = Gen_ProductoCompuestoDet.Gen_Producto_IdProducto
WHERE Gen_Producto.IdProducto = var_IdProducto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ListarPreOrden` ()  BEGIN
SELECT po.IdPreOrden, cl.DniRuc, cl.Cliente,
    SUM(pod.Cantidad) as Cantidad,
    SUM(PRO.PrecioContado * pod.Cantidad) AS Total,
    po.FechaReg 
FROM Ve_PreOrden AS po 
INNER JOIN Ve_DocVentaCliente AS cl ON po.IdCliente = cl.IdCliente
INNER JOIN Ve_PreOrdenDet AS pod ON po.IdPreOrden = pod.IdPreOrden
INNER JOIN Gen_Producto AS PRO ON pod.IdProducto = PRO.IdProducto
GROUP BY cl.DniRuc
ORDER BY po.FechaReg DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ListarProductoXCompuesto` (IN `var_IdProductoCompuesto` INT, IN `var_ProductoCompuesto` VARCHAR(255))  BEGIN

IF var_ProductoCompuesto='00000' THEN



SELECT Gen_Producto.IdProducto, Gen_Producto.Producto

FROM Gen_Producto

INNER JOIN Gen_ProductoCompuestoDet ON  Gen_ProductoCompuestoDet.Gen_Producto_IdProducto = Gen_Producto.IdProducto

INNER JOIN Gen_ProductoCompuesto ON Gen_ProductoCompuesto.IdProductoCompuesto = Gen_ProductoCompuestoDet.Gen_ProductoCompuesto_IdProductoCompuesto

WHERE Gen_ProductoCompuesto.IdProductoCompuesto = var_IdProductoCompuesto;



ELSEIF var_IdProductoCompuesto=00000 THEN



SELECT Gen_Producto.IdProducto, Gen_Producto.Producto, Gen_Producto.PrecioContado, Gen_Producto.PrecioPorMayor

FROM Gen_Producto

INNER JOIN Gen_ProductoCompuestoDet ON  Gen_ProductoCompuestoDet.Gen_Producto_IdProducto = Gen_Producto.IdProducto

INNER JOIN Gen_ProductoCompuesto ON Gen_ProductoCompuesto.IdProductoCompuesto = Gen_ProductoCompuestoDet.Gen_ProductoCompuesto_IdProductoCompuesto

WHERE Gen_ProductoCompuesto.ProductoCompuesto = var_ProductoCompuesto;

END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ProductoSeleccionar` (IN `var_Almacen` VARCHAR(255))  BEGIN







	SELECT







		Gen_Producto.IdProducto as numero,







		UPPER(Gen_Producto.Producto) as PRODUCTO,







		Gen_Producto.PrecioContado,







		Gen_Producto.PrecioPorMayor,







		Gen_Producto.StockPorMayor,





		Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica,



		Gen_ProductoMarca.ProductoMarca,

Gen_Producto.Codigo,

Gen_ProductoMedicion.ProductoMedicion,

Gen_Producto.StockMinimo as STOCK,

Gen_Producto.VentaEstrategica

































	FROM







		Gen_Producto







		INNER JOIN Gen_ProductoCategoria On Gen_ProductoCategoria.IdProductoCategoria=Gen_Producto.IdProductoCategoria







		INNER JOIN Gen_ProductoMarca On Gen_ProductoMarca.IdProductoMarca=Gen_Producto.IdProductoMarca



		INNER JOIN Gen_ProductoMedicion On Gen_ProductoMedicion.IdProductoMedicion=Gen_Producto.IdProductoMedicion



		INNER JOIN Gen_ProductoFormaFarmaceutica On Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica=Gen_Producto.IdProductoFormaFarmaceutica







	WHERE







		Gen_Producto.Anulado=false







	ORDER BY







		Gen_Producto.Producto;







END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_RegDocVenta` (IN `var_RegVenta` BIT, IN `var_FechaIni` DATE, IN `var_FechaFin` DATE)  BEGIN



		SELECT



		Ve_DocVenta.idDocVenta,



		Ve_DocVenta.FechaDoc,



		Ve_DocVentaTipoDoc.CodSunat,



		Ve_DocVentaTipoDoc.TipoDoc,



		Ve_DocVenta.Anulado,



		Ve_DocVenta.Serie,



		Ve_DocVenta.Numero,



		IFNULL((Select Sum(Round(Ve_DocVentaDet.Precio* Ve_DocVentaDet.Cantidad,2)) FROM Ve_DocVentaDet Where Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.idDocVenta),0) as SubTotal,



		0 as Igv,



		IFNULL((Select Sum(Round(Ve_DocVentaDet.Precio* Ve_DocVentaDet.Cantidad,2)) FROM Ve_DocVentaDet Where Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.idDocVenta),0) as Total,

Ve_DocVenta.Anulado



FROM



	Ve_DocVenta



	INNER JOIN Ve_DocVentaTipoDoc On Ve_DocVenta.IdTipoDoc=Ve_DocVentaTipoDoc.IdTipoDoc



WHERE



	Ve_DocVentaTipoDoc.VaRegVenta=var_RegVenta and Ve_DocVenta.Fechadoc BETWEEN CAST(var_FechaIni AS DATETIME) and CONCAT(var_FechaFin,' 23:59:59')





ORDER BY  Ve_DocVenta.Fechadoc DESC;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_ReporteUtilidadBruta` (IN `var_FechaIni` DATE, IN `var_FechaFin` DATE)  BEGIN
  SELECT
	Gen_Producto.IdProducto,
	Gen_Producto.Producto,
	Sum(round(Ve_DocVentaDet.Cantidad,2)) AS Cantidad,
	Gen_Producto.PrecioCosto,
	Ve_DocVentaDet.Precio as PrecioVenta,
	Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio as TotalVenta,
	Gen_Producto.PrecioCosto * Ve_DocVentaDet.Cantidad as TotalCosto,
	Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio - Gen_Producto.PrecioCosto * Ve_DocVentaDet.Cantidad as UtilidadBruta

	FROM
	Ve_DocVenta
	INNER JOIN Ve_DocVentaDet ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta
	INNER JOIN Gen_Producto ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto
	WHERE Ve_DocVenta.FechaDoc BETWEEN var_FechaIni and var_FechaFin
	Group by Gen_Producto.IdProducto,
	Gen_Producto.Producto,Ve_DocVentaDet.Precio;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SbVe_VentasXFechasXUsuario` (IN `var_FechaDocIni` DATETIME, IN `var_FechaDocFin` DATETIME, IN `var_Usuario` VARCHAR(255))  BEGIN
SELECT
	Ve_DocVenta.FechaDoc,
	Ve_DocVentaPuntoVenta.PuntoVenta,
	Ve_DocVentaTipoDoc.TipoDoc,
	Ve_DocVentaTipoDoc.CodSunat,
	Ve_DocVenta.Serie,
	Ve_DocVenta.Numero,
	Ve_DocVentaCliente.DniRuc,
	CASE WHEN Ve_DocVenta.Anulado=1 THEN 'ANULADO' ELSE Ve_DocVentaCliente.Cliente END as Cliente,
	Ve_DocVenta.Anulado,
	TRUNCATE((Select Sum(Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) From Ve_DocVentaDet Where Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.idDocVenta),2) as SubTotal,
	0.00 as Igv,
	TRUNCATE((Select Sum(Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) From Ve_DocVentaDet Where Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.idDocVenta),2) as Total
	FROM
	`Ve_DocVenta`
	INNER JOIN `Ve_DocVentaCliente` ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
	INNER JOIN `Ve_DocVentaPuntoVenta` ON Ve_DocVenta.IdDocVentaPuntoVenta = Ve_DocVentaPuntoVenta.IdDocVentaPuntoVenta
	INNER JOIN `Ve_DocVentaTipoDoc` ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
	WHERE  (Ve_DocVenta.FechaDoc Between var_FechaDocIni + ' 00:00:00' and var_FechaDocFin + ' 23:59:59') and Ve_DocVenta.UsuarioReg=var_Usuario and Ve_DocVenta.IdCierre is NULL
	ORDER BY Ve_DocVenta.FechaDoc,
	Ve_DocVentaPuntoVenta.PuntoVenta,
	Ve_DocVentaTipoDoc.TipoDoc,
	Ve_DocVentaTipoDoc.CodSunat,
	Ve_DocVenta.Serie,
	Ve_DocVenta.Numero;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Sb_BuscarDeudor` (IN `Deudor` VARCHAR(255), IN `TipoDeudor` INT)  BEGIN



	IF TipoDeudor=1 THEN



		SELECT DISTINCT Ve_DocVenta.idDocVenta as Serie, FechaDoc, FechaCredito, Numero,

		(SELECT SUM(Ve_DocVentaDet.Cantidad*Ve_DocVentaDet.Precio)FROM Ve_DocVentaDet WHERE Ve_DocVentaDet.IdDocVenta= Ve_DocVenta.idDocVenta) as Total,

	(Select IFNULL((Select  sum(Cb_CajaBancoDet.Importe) from Cb_CajaBancoDet WHERE Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)) as Cancelado



			FROM Ve_DocVenta

		INNER JOIN Ve_DocVentaCliente ON Ve_DocVentaCliente.IdCliente = Ve_DocVenta.IdCliente

		INNER JOIN Ve_DocVentaDet ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta

		INNER JOIN Cb_CajaBancoDet ON Cb_CajaBancoDet.IdDocDet = Ve_DocVenta.idDocVenta

		WHERE Ve_DocVentaCliente.Cliente=Deudor AND Ve_DocVenta.FechaCredito IS NOT NULL ORDER BY FechaCredito ASC;



	ELSEIF TipoDeudor=2 THEN



		SELECT Serie, MovimientoFecha as FechaDoc, FechaVenCredito as FechaCredito, Numero FROM Lo_Movimiento

		INNER JOIN Lo_Proveedor ON Lo_Proveedor.IdProveedor = Lo_Movimiento.IdProveedor

		INNER JOIN Cb_CajaBancoDet ON Cb_CajaBancoDet.`Hash`=Lo_Movimiento.`Hash`

		WHERE Lo_Proveedor.Proveedor=Deudor ORDER BY FechaCredito ASC;



	END IF;



END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Sb_ListarProductoInv` ()  begin

select * from Gen_Producto where ControlaStock = 1 and Anulado = 0;

end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Sb_ListarProductosRegMov` (IN `var_idMov` VARCHAR(255))  BEGIN







	Select  Lo_MovimientoDetalle.hashMovimiento, Gen_Producto.Codigo, Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica, Gen_Producto.Producto, Gen_Producto.PrecioContado, Gen_ProductoMedicion.ProductoMedicion, Lo_MovimientoDetalle.Cantidad, Lo_MovimientoDetalle.TieneIgv, Lo_MovimientoDetalle.Precio,

 Gen_ProductoMarca.ProductoMarca

	from Lo_MovimientoDetalle



	INNER JOIN Gen_Producto on Gen_Producto.IdProducto = Lo_MovimientoDetalle.IdProducto

	INNER JOIN Gen_ProductoFormaFarmaceutica ON Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica = Gen_Producto.IdProductoFormaFarmaceutica

	INNER JOIN Gen_ProductoMedicion ON Gen_ProductoMedicion.IdProductoMedicion = Gen_Producto.IdProductoMedicion

	INNER JOIN Gen_ProductoMarca ON Gen_ProductoMarca.IdProductoMarca = Gen_Producto.IdProductoMarca

	WHERE Lo_MovimientoDetalle.hashMovimiento = var_idMov;







END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Sb_ListarProductosRegVenta` (IN `var_DocVenta` INT)  BEGIN







Select Gen_Producto.Producto, Gen_Producto.Codigo, Gen_ProductoFormaFarmaceutica.ProductoFormaFarmaceutica, Gen_ProductoMedicion.ProductoMedicion, Ve_DocVentaDet.IdDocVenta, Ve_DocVentaDet.Cantidad, Ve_DocVentaDet.Precio,

Gen_ProductoMarca.ProductoMarca

 from Gen_Producto



	INNER JOIN Ve_DocVentaDet on Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto

	INNER JOIN Gen_ProductoFormaFarmaceutica ON Gen_ProductoFormaFarmaceutica.IdProductoFormaFarmaceutica = Gen_Producto.IdProductoFormaFarmaceutica

	INNER JOIN Gen_ProductoMedicion ON Gen_ProductoMedicion.IdProductoMedicion = Gen_Producto.IdProductoMedicion

INNER JOIN Gen_ProductoMarca ON Gen_ProductoMarca.IdProductoMarca = Gen_Producto.IdProductoMarca



	WHERE Ve_DocVentaDet.IdDocVenta = var_DocVenta;







END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Sb_ListarTratamientoXDiagnostico` (IN `var_diagnostico` INT)  BEGIN

Select Ve_ExpertoTratamiento.IdTratamiento,



CONCAT(Gen_ProductoCompuesto.ProductoCompuesto," NroDias (",CONVERT(Ve_ExpertoTratamiento.NroDias,CHAR),") NroTomas (",CONVERT(Ve_ExpertoTratamiento.TomasXDia,CHAR) , ")") as Tratamiento,



Gen_ProductoCompuesto.ProductoCompuesto,

Ve_ExpertoTratamiento.NroDias,

Ve_ExpertoTratamiento.TomasXDia,

Ve_ExpertoTratamiento.Observacion,

Ve_ExpertoTratamiento.DosisXPeso,

Ve_ExpertoTratamiento.Concentracion,

Ve_ExpertoTratamiento.UnidadDosisXPeso



From Ve_ExpertoTratamiento



Inner Join Gen_ProductoCompuesto On Ve_ExpertoTratamiento.IdCompuesto=Gen_ProductoCompuesto.IdProductoCompuesto



where Ve_ExpertoTratamiento.IdDiagnostico = var_diagnostico;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Sb_VerificarMovimiento` (IN `var_MovimientoTipo` VARCHAR(255), IN `var_Proveedor` VARCHAR(255), IN `var_Serie` VARCHAR(255), IN `var_Numero` INT)  BEGIN

	SELECT IFNULL((SELECT COUNT(*) FROM Lo_Movimiento

	INNER JOIN Lo_MovimientoTipo ON Lo_MovimientoTipo.IdMovimientoTipo = Lo_Movimiento.IdMovimientoTipo

	INNER JOIN Lo_Proveedor ON Lo_Proveedor.IdProveedor = Lo_Movimiento.IdProveedor

	WHERE Lo_MovimientoTipo.TipoMovimiento = var_MovimientoTipo

	AND Lo_Proveedor.Proveedor = var_Proveedor

	AND Lo_Movimiento.Serie = var_Serie

	AND Lo_Movimiento.Numero = var_Numero),0) as EXISTE;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cb_CajaBanco`
--

CREATE TABLE `Cb_CajaBanco` (
  `IdCajaBanco` int(11) NOT NULL,
  `IdTipoCajaBanco` int(11) DEFAULT NULL,
  `IdCuenta` int(11) DEFAULT NULL,
  `FechaDoc` datetime DEFAULT NULL,
  `Concepto` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `Importe` double DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `UsuarioReg` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `FechaMod` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cb_CajaBancoDet`
--

CREATE TABLE `Cb_CajaBancoDet` (
  `IdCajaBanco` int(11) NOT NULL,
  `IdDocDet` int(11) NOT NULL,
  `Importe` float DEFAULT NULL,
  `Hash` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `Tipo` varchar(45) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cb_Cuenta`
--

CREATE TABLE `Cb_Cuenta` (
  `IdCuenta` int(11) NOT NULL,
  `Cuenta` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `Cb_Cuenta`
--

INSERT INTO `Cb_Cuenta` (`IdCuenta`, `Cuenta`, `Anulado`) VALUES
(1, 'Caja Principal', b'0'),
(2, 'Banco BCP', b'0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cb_TipoCajaBanco`
--

CREATE TABLE `Cb_TipoCajaBanco` (
  `IdTipoCajaBanco` int(11) NOT NULL,
  `TipoCajaBanco` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `Tipo` int(11) DEFAULT NULL,
  `AplicaDocAPagar` bit(1) DEFAULT NULL,
  `AplicaDocACobrar` bit(1) DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `Cb_TipoCajaBanco`
--

INSERT INTO `Cb_TipoCajaBanco` (`IdTipoCajaBanco`, `TipoCajaBanco`, `Tipo`, `AplicaDocAPagar`, `AplicaDocACobrar`, `Anulado`) VALUES
(1, 'PAGO A PROVEEDOR', 1, b'1', b'0', b'0'),
(2, 'PAGO DE CLIENTE', 0, b'0', b'1', b'0'),
(3, 'VALE DE EGRESO', 1, b'0', b'0', b'0'),
(4, 'VALE DE INGRESO', 0, b'0', b'0', b'0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `GEN_EMPRESA`
--

CREATE TABLE `GEN_EMPRESA` (
  `RAZONSOCIAL` varchar(255) DEFAULT NULL,
  `RUC` varchar(255) DEFAULT NULL,
  `DIRECCION` text,
  `IGV` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `GEN_EMPRESA`
--

INSERT INTO `GEN_EMPRESA` (`RAZONSOCIAL`, `RUC`, `DIRECCION`, `IGV`) VALUES
('asdasd', '123123', '123123', 0.18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_Moneda`
--

CREATE TABLE `Gen_Moneda` (
  `Moneda` varchar(255) DEFAULT NULL,
  `TipoCambio` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `Gen_Moneda`
--

INSERT INTO `Gen_Moneda` (`Moneda`, `TipoCambio`) VALUES
('PEN', 1),
('USD', 3.3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_Producto`
--

CREATE TABLE `Gen_Producto` (
  `IdProducto` int(11) NOT NULL,
  `IdProductoMarca` int(11) NOT NULL,
  `IdProductoFormaFarmaceutica` int(11) NOT NULL,
  `IdProductoMedicion` int(11) NOT NULL,
  `IdProductoCategoria` int(11) NOT NULL,
  `Producto` varchar(255) DEFAULT NULL,
  `ProductoDesc` text,
  `ProductoDescCorto` varchar(255) DEFAULT NULL,
  `CodigoBarra` varchar(255) DEFAULT NULL,
  `Codigo` varchar(255) DEFAULT NULL,
  `Dosis` varchar(255) DEFAULT NULL,
  `PrecioContado` float DEFAULT NULL,
  `PrecioPorMayor` float DEFAULT NULL,
  `StockPorMayor` float DEFAULT NULL,
  `StockMinimo` float DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL,
  `Hash` varchar(255) DEFAULT NULL,
  `ControlaStock` bit(1) DEFAULT NULL,
  `PrecioCosto` float DEFAULT NULL,
  `VentaEstrategica` bit(1) DEFAULT NULL,
  `PorcentajeUtilidad` float DEFAULT NULL,
  `IdBloque` int(11) DEFAULT NULL,
  `Modelo` varchar(255) CHARACTER SET big5 DEFAULT NULL,
  `Moneda` varchar(45) DEFAULT 'S/.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Gen_Producto`
--

INSERT INTO `Gen_Producto` (`IdProducto`, `IdProductoMarca`, `IdProductoFormaFarmaceutica`, `IdProductoMedicion`, `IdProductoCategoria`, `Producto`, `ProductoDesc`, `ProductoDescCorto`, `CodigoBarra`, `Codigo`, `Dosis`, `PrecioContado`, `PrecioPorMayor`, `StockPorMayor`, `StockMinimo`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `ControlaStock`, `PrecioCosto`, `VentaEstrategica`, `PorcentajeUtilidad`, `IdBloque`, `Modelo`, `Moneda`) VALUES
(3156, 299, 1, 6, 52, 'ARO DRAGON WHEELS D1217 20X10.0 10X114.3+127 ET78 B-MX', '', '', 'ARO633', '', '', 156, 520, 0, 0, b'0', NULL, NULL, '2018-02-22 11:34:51', 'Jeam', NULL, b'1', 0, b'0', 0, NULL, 'D1217', 'S/.'),
(3157, 335, 1, 6, 55, 'LLANTA CONTINENTAL 265/65R17 AT TERRAINCONTAC', '', '', 'LHO272', '', '', 146.4, 0, 0, 0, b'0', NULL, NULL, '2018-02-20 14:46:19', 'Jeam', NULL, b'1', 0, b'0', 0, NULL, '265/65R17', 'S/.'),
(3158, 281, 1, 6, 55, 'LLANTA DUNLOP 255/60R15 SPGT SPORT', '', '', 'LHO271', '', '', 72.72, 70, 10, 1, b'0', NULL, NULL, '2018-02-14 11:27:08', 'Jeam', NULL, b'1', 0, b'0', 0, NULL, '255/60R15', 'S/.'),
(3159, 460, 1, 6, 55, 'LLANTA NEXEN 215/65R17 HT 98H', '', '', 'LHO270', '', '', 109.08, 0, 0, 0, b'0', NULL, NULL, '2018-02-20 11:14:07', 'Jeam', NULL, b'1', 0, b'0', 0, NULL, '215/65R16', 'S/.'),
(3163, 330, 1, 6, 55, 'LLANTA KUMHO 235/65R15 KL51 92H', NULL, NULL, 'LHO269', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R15', 'S/.'),
(3164, 275, 1, 6, 55, 'LLANTA TOYO TYRES 265/70R15 AT OPAT', NULL, NULL, 'LHO268', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R15', 'S/.'),
(3165, 292, 1, 6, 55, 'LLANTA ORNET 11.00-20 DEL R503 18PR', NULL, NULL, 'LHO267', NULL, NULL, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11.00-20', 'S/.'),
(3166, 292, 1, 6, 55, 'LLANTA ORNET 11.00-20 POS L602 18PR', NULL, NULL, 'LHO266', NULL, NULL, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11.00-20', 'S/.'),
(3167, 459, 1, 6, 53, 'BATERIA ZERO YTX7L-BS AGM XPLUS', NULL, NULL, 'BAT215', NULL, NULL, 83.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX7L-BS', 'S/.'),
(3168, 459, 1, 6, 53, 'BATERIA ZERO YTX7A-BS AGM XPLUS', NULL, NULL, 'BAT214', NULL, NULL, 83.86, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX7A-BS', 'S/.'),
(3169, 459, 1, 6, 53, 'BATERIA ZERO YTX5L-BS AGM XPLUS', NULL, NULL, 'BAT213', NULL, NULL, 67.38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX5L-BS', 'S/.'),
(3170, 459, 1, 6, 53, 'BATERIA ZERO YTX4L-BS AGM XPLUS', NULL, NULL, 'BAT212', NULL, NULL, 53.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX4L-BS', 'S/.'),
(3171, 459, 1, 6, 53, 'BATERIA ZERO 12N9-3B AGM XPLUS', NULL, NULL, 'BAT211', NULL, NULL, 92.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N9-3B', 'S/.'),
(3172, 459, 1, 6, 53, 'BATERIA ZERO 12N7A-3A AGM XPLUS', NULL, NULL, 'BAT210', NULL, NULL, 85.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N7A-3A', 'S/.'),
(3173, 459, 1, 6, 53, 'BATERIA ZERO 12N7-3B AGM XPLUS', NULL, NULL, 'BAT209', NULL, NULL, 88.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N7-3B', 'S/.'),
(3174, 459, 1, 6, 53, 'BATERIA ZERO 12N6.5-3B AGM XPLUS', NULL, NULL, 'BAT208', NULL, NULL, 77.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N6.5-3B', 'S/.'),
(3175, 459, 1, 6, 53, 'BATERIA ZERO 12N5-3B AGM XPLUS', NULL, NULL, 'BAT207', NULL, NULL, 62.75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N5-3B', 'S/.'),
(3176, 459, 1, 6, 53, 'BATERIA ZERO 6N6-3B AGM XPLUS', NULL, NULL, 'BAT206', NULL, NULL, 41.58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6N6-3B', 'S/.'),
(3177, 458, 1, 6, 55, 'LLANTA ANTYRE 12R22.5 MIX TB877 18PR', NULL, NULL, 'LHO265', NULL, NULL, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'S/.'),
(3178, 291, 1, 6, 55, 'LLANTA HIFLY 245/55R19 HF801 103V', NULL, NULL, 'LHO264', NULL, NULL, 70.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/55R19', 'S/.'),
(3179, 451, 1, 6, 55, 'LLANTA MARUTI 7.50-16 POS INDI LUG 16PR', NULL, NULL, 'LHO263', NULL, NULL, 126, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'S/.'),
(3180, 457, 1, 6, 55, 'LLANTA SUNOTE 185/70R14 SN666 88T', NULL, NULL, 'LHO262', NULL, NULL, 33.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(3181, 335, 1, 6, 55, 'LLANTA CONTINENTAL 225/40R18 CSC5 88Y', NULL, NULL, 'LHO261', NULL, NULL, 228, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/40R18', 'S/.'),
(3182, 326, 1, 6, 55, 'LLANTA DURUN 295/40R21 M626 111W XL', NULL, NULL, 'LHO260', NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '295/40R21', 'S/.'),
(3183, 281, 1, 6, 55, 'LLANTA DUNLOP 185/70R14 ENASAVE EC300', NULL, NULL, 'LHO259', NULL, NULL, 154.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(3184, 366, 1, 6, 55, 'LLANTA HABILEAD 265/70R17 AT PRACTICALMAX', NULL, NULL, 'LHO258', NULL, NULL, 105.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'S/.'),
(3185, 312, 1, 6, 55, 'LLANTA MIRAGE 11R22.5 MIX MG702 16PR', NULL, NULL, 'LHO257', NULL, NULL, 197.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'S/.'),
(3186, 312, 1, 6, 55, 'LLANTA MIRAGE 205/70R15 MR200 8PR', NULL, NULL, 'LHO256', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'S/.'),
(3187, 299, 1, 6, 52, 'ARO DRAGON WHEELS 5003 14X6.0 ET25 8X100+114.3 BP-F', NULL, NULL, 'ARO632', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5003', 'S/.'),
(3188, 447, 1, 6, 55, 'LLANTA MAXXIS 215/75R15 MT BIGHORN', NULL, NULL, 'LHO255', NULL, NULL, 99.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(3189, 447, 1, 6, 55, 'LLANTA MAXXIS 285/70R17 MT BIGHORN', NULL, NULL, 'LHO254', NULL, NULL, 184.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '285/70R17', 'S/.'),
(3190, 291, 1, 6, 55, 'LLANTA HIFLY 215/70R15 SUPER2000 109/107R 8PR', NULL, NULL, 'LHO253', NULL, NULL, 56.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R15', 'S/.'),
(3191, 357, 1, 6, 55, 'LLANTA SUNFULL 225/70R15 SF05 112/110R 8PR', NULL, NULL, 'LHO252', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R15', 'S/.'),
(3192, 291, 1, 6, 55, 'LLANTA HIFLY 165/65R13 HF201 77T', NULL, NULL, 'LHO251', NULL, NULL, 24.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'S/.'),
(3193, 299, 1, 6, 52, 'ARO DRAGON WHEELS L430 17X9.0 ET25 5X114.3 MB-L', NULL, NULL, 'ARO631', NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'L430', 'S/.'),
(3194, 299, 1, 6, 52, 'ARO DRAGON WHEELS 8104 16X7.0 ET20 8X100+114.3 B-P', NULL, NULL, 'ARO630', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8104', 'S/.'),
(3195, 299, 1, 6, 52, 'ARO DRAGON WHEELS L582 15X7.0 ETO 6X139.7 BM-LP', NULL, NULL, 'ARO629', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'L582', 'S/.'),
(3196, 299, 1, 6, 52, 'ARO DRAGON WHEELS L200 14X6.0 ET0 4X100+114.3 M-FB', NULL, NULL, 'ARO628', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'L200', 'S/.'),
(3197, 291, 1, 6, 55, 'LLANTA HIFLY 185/65R15 HF201 88H', NULL, NULL, 'LHO250', NULL, NULL, 36.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'S/.'),
(3198, 326, 1, 6, 55, 'LLANTA DURUN 185/65R14 A2000 86H', NULL, NULL, 'LHO249', NULL, NULL, 35.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'S/.'),
(3199, 291, 1, 6, 55, 'LLANTA HIFLY 185/65R14 HF201 86H', NULL, NULL, 'LHO248', NULL, NULL, 31.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'S/.'),
(3200, 363, 1, 6, 53, 'BATERIA RECORD RC 60 PLUS', NULL, NULL, 'BAT205', NULL, NULL, 213.26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RC60', 'S/.'),
(3201, 280, 1, 6, 53, 'BATERIA HANKOOK MF55457 CHATA 480CCA', NULL, NULL, 'BAT204', NULL, NULL, 227.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55457', 'S/.'),
(3202, 280, 1, 6, 53, 'BATERIA HANKOOK 56821 570CCA CHATA', NULL, NULL, 'BAT203', NULL, NULL, 311.89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '56821', 'S/.'),
(3203, 271, 1, 6, 55, 'LLANTA LING LONG 205/50R16 CROSSWIND 87V', NULL, NULL, 'LHO247', NULL, NULL, 45.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(3204, 357, 1, 6, 55, 'LLANTA SUNFULL 265/70R16 AT AT782', NULL, NULL, 'LHO246', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(3205, 413, 1, 6, 55, 'LLANTA POWERTRAC 265/75R16 MT POWER ROVER', NULL, NULL, 'LHO245', NULL, NULL, 105.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'S/.'),
(3206, 357, 1, 6, 55, 'LLANTA SUNFULL 245/75R16 MT MT781', NULL, NULL, 'LHO244', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3207, 291, 1, 6, 55, 'LLANTA HIFLY 245/75R16 MT VIGOROUS', NULL, NULL, 'LHO243', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3208, 299, 1, 6, 52, 'ARO DRAGON WHEELS 5325 18X8.0 ET25 10X100+114.3 B-P', NULL, NULL, 'ARO627', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5325', 'S/.'),
(3209, 299, 1, 6, 52, 'ARO DRAGON WHEELS 519 18X8.0 ET35 5X114.3 B-LP', NULL, NULL, 'ARO626', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '519', 'S/.'),
(3210, 299, 1, 6, 52, 'ARO DRAGON WHEELS 337 16X7.0 ET35 8X100+114.3 R-BP', NULL, NULL, 'ARO625', NULL, NULL, 75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '337', 'S/.'),
(3211, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3215 16X7.0 ET35 8X100+114.3 B-P', NULL, NULL, 'ARO624', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3215', 'S/.'),
(3212, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-742 14X6.0 ET38 8X100+114.3 BK-IRD', NULL, NULL, 'ARO623', NULL, NULL, 85.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '742', 'S/.'),
(3213, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-480 14X6.0 ET38 8X100+114.3 BK-IRD', NULL, NULL, 'ARO622', NULL, NULL, 83.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '480', 'S/.'),
(3214, 341, 1, 6, 55, 'LLANTA ROADSHINE 9.5R17.5 POS RS604 132/130R 16PR', NULL, NULL, 'LHO241', NULL, NULL, 138, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.5R17.5', 'S/.'),
(3215, 291, 1, 6, 55, 'LLANTA HIFLY 265/75R16 MT VIGOROUS', NULL, NULL, 'LHO240', NULL, NULL, 103.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'S/.'),
(3216, 413, 1, 6, 55, 'LLANTA POWERTRAC 155/70R13 CITYTOUR 75T', '', '', 'LHO239', '', '', 21.03, 0, 0, 0, b'0', NULL, NULL, '2018-02-24 11:15:15', 'Jeam', NULL, b'1', 19.5, b'1', 20, NULL, '155/70R13', 'S/.'),
(3217, 413, 1, 6, 55, 'LLANTA POWERTRAC 165/70R12 CITYPOWER 77T', NULL, NULL, 'LHO238', NULL, NULL, 21.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/70R12', 'S/.'),
(3218, 456, 1, 6, 55, 'LLANTA TORNEL 7.50-16 POS TXL PLUS 16PR', NULL, NULL, 'LHO237', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'S/.'),
(3219, 446, 1, 6, 55, 'LLANTA HILO 265/75R16 AT X TERRA', NULL, NULL, 'LHO236', NULL, NULL, 92.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'S/.'),
(3220, 414, 1, 6, 55, 'LLANTA FIREMAX 265/75R16 MT FM523', NULL, NULL, 'LHO235', NULL, NULL, 115.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'S/.'),
(3221, 280, 1, 6, 55, 'LLANTA HANKOOK 185/70R14 K715 88T', NULL, NULL, 'LHO234', NULL, NULL, 48.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(3222, 281, 1, 6, 55, 'LLANTA DUNLOP 265/65R17 HT GRANTREK2', NULL, NULL, 'LHO233', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'US$'),
(3223, 280, 1, 6, 55, 'LLANTA HANKOOK 265/75R16 AT DYNAPRO', NULL, NULL, 'LHO232', NULL, NULL, 180.65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(3224, 280, 1, 6, 55, 'LLANTA HANKOOK 235/75R15 MT DYNAPRO', NULL, NULL, 'LHO231', NULL, NULL, 140.82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'US$'),
(3225, 455, 1, 6, 55, 'LLANTA ROADTEC 185/70R14 LCG01 88T', NULL, NULL, 'LHO230', NULL, NULL, 33.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(3226, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-9011 16X8.0 ET10 6X139.7 B4', NULL, NULL, 'ARO621', NULL, NULL, 91.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9011', 'S/.'),
(3227, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-115 15X7.0 ET35 8X100+114.3 W-OJKB', NULL, NULL, 'ARO620', NULL, NULL, 90.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '115', 'S/.'),
(3228, 454, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1402 18X8.0 ET40 5X114.3 B4', NULL, NULL, 'ARO619', NULL, NULL, 109.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1402', 'S/.'),
(3229, 296, 1, 6, 52, 'ARO PDW 4526707 14X5.5 ET38 4X100.0 MB', NULL, NULL, 'ARO618', NULL, NULL, 51.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4526707', 'S/.'),
(3230, 296, 1, 6, 52, 'ARO PDW 4515512 14X6.5 ET30 4X100.0 MB', NULL, NULL, 'ARO617', NULL, NULL, 52.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4515512', 'S/.'),
(3231, 296, 1, 6, 52, 'ARO PDW 424352 14X6.5 ET35 4X100.0 MB', NULL, NULL, 'ARO616', NULL, NULL, 51.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '424352', 'S/.'),
(3232, 296, 1, 6, 52, 'ARO PDW 20181460 14X6.0 ET35 4X100.0 MI/B', NULL, NULL, 'ARO615', NULL, NULL, 51.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '20181460', 'S/.'),
(3233, 296, 1, 6, 52, 'ARO PDW 10061460 14X6.0 ET30 4X100.0 M2/B', NULL, NULL, 'ARO614', NULL, NULL, 51.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1006146', 'S/.'),
(3234, 296, 1, 6, 52, 'ARO PDW 30381370 13X7.0 ET70 4X100.0 MI/GR', NULL, NULL, 'ARO612', NULL, NULL, 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '30381370', 'S/.'),
(3235, 296, 1, 6, 52, 'ARO PDW 30381370 13X7.0 ET70 4X114.3 MI/GR', NULL, NULL, 'ARO611', NULL, NULL, 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '303813', 'S/.'),
(3236, 291, 1, 6, 55, 'LLANTA HIFLY 195/70R14 HF201 91H', NULL, NULL, 'LHO229', NULL, NULL, 36.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R14', 'S/.'),
(3237, 291, 1, 6, 55, 'LLANTA HIFLY 235/75R17.5 POS HF628 16PR', NULL, NULL, 'LHO228', NULL, NULL, 123.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R17.5', 'S/.'),
(3238, 291, 1, 6, 55, 'LLANTA HIFLY 8.25R16 POS HH313 16PR', NULL, NULL, 'LHO227', NULL, NULL, 144.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'S/.'),
(3239, 453, 1, 6, 55, 'LLANTA ARTUM 235/60R18 A2000 107H', NULL, NULL, 'LHO226', NULL, NULL, 73.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R18', 'S/.'),
(3240, 385, 1, 6, 55, 'LLANTA NANKANG 205/50R15 NS-2 86V', NULL, NULL, 'LHO225', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'S/.'),
(3241, 445, 1, 6, 55, 'LLANTA DOUPRO 12.00R20 TRA ST968 156/153K 20PR', NULL, NULL, 'LHO224', NULL, NULL, 297.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'S/.'),
(3242, 296, 1, 6, 52, 'ARO PDW 4602615 14X7.0 ET10 6X139.7 MB', NULL, NULL, 'ARO610', NULL, NULL, 61.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4602615', 'S/.'),
(3243, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG2606 18X8.5 ET35 5X114.3 MB FAC', NULL, NULL, 'ARO609', NULL, NULL, 126.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG2606', 'S/.'),
(3244, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-3680 17X7.0 ET40 4X100.0 MB', NULL, NULL, 'ARO608', NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3680', 'S/.'),
(3245, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-1913 15X7.0 ET30 4X100.0 MB', NULL, NULL, 'ARO607', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1913', 'S/.'),
(3246, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-2890 15X7.0 ET35 4X100.0 MB', NULL, NULL, 'ARO606', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2890', 'S/.'),
(3247, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-1911 14X5.5 ET38 4X100+114.3 MB', NULL, NULL, 'ARO605', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1911', 'S/.'),
(3248, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-5071 14X6.5 ET30 4X100.0 B-MFC', NULL, NULL, 'ARO604', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5071', 'S/.'),
(3249, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6600 14X6.0 ET35 4X100.0 MB', NULL, NULL, 'ARO603', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '66003', 'S/.'),
(3250, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-5008 17X9.0 ET0 16X114.3 BM', NULL, NULL, 'ARO602', NULL, NULL, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5008', 'S/.'),
(3251, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-1009 16X8.0 ET15 6X114.3 R LP', NULL, NULL, 'ARO601', NULL, NULL, 85.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1009', 'S/.'),
(3252, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6642 16X7.5 ET15 6X139.7 MB', NULL, NULL, 'ARO600', NULL, NULL, 80.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6642', 'S/.'),
(3253, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-672 16X7.0 ET0 6X139.7 MB', NULL, NULL, 'ARO599', NULL, NULL, 82.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '672', 'S/.'),
(3254, 382, 1, 6, 55, 'LLANTA TRAILCUTTER 265/65R17 AT RADIAL', NULL, NULL, 'LHO223', NULL, NULL, 150, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'S/.'),
(3255, 452, 1, 6, 52, 'ARO VARELOX ZF-495 13X6.0 ET30 8X100+114.3 BP', NULL, NULL, 'ARO598', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '495', 'S/.'),
(3256, 452, 1, 6, 52, 'ARO VARELOX ZY-3311 13X5.5 ET30 8X100+114.3 BP', NULL, NULL, 'ARO597', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3311', 'S/.'),
(3257, 452, 1, 6, 52, 'ARO VARELOX ZY-6704 13X5.5 ET20 4X100 RL-B6', NULL, NULL, 'ARO596', NULL, NULL, 148.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6704', 'S/.'),
(3258, 452, 1, 6, 52, 'ARO VARELOX ZF-448 13X5.5 ET35 4X100+114.3 BP', NULL, NULL, 'ARO595', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '448', 'S/.'),
(3259, 452, 1, 6, 52, 'ARO VARELOX ZF-1032 13X6.0 ET30 8X100+114.3 RL B6', NULL, NULL, 'ARO594', NULL, NULL, 148.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1032', 'S/.'),
(3260, 452, 1, 6, 52, 'ARO VARELOX ZF-3206 13X5.5 ET30 8X100+114.3 ORP', NULL, NULL, 'ARO593', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3206', 'S/.'),
(3261, 452, 1, 6, 52, 'ARO VARELOX ZF-089 13X5.5 ET35 8X100+114.3 SP', NULL, NULL, 'ARO592', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '089', 'S/.'),
(3262, 452, 1, 6, 52, 'ARO VARELOX ZF-089 13X5.5 ET30 8X100+114.3 BP', NULL, NULL, 'ARO591', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '089', 'S/.'),
(3263, 452, 1, 6, 52, 'ARO VARELOX ZY-3209 13X5.5 ET30 8X100+114.3 BP', NULL, NULL, 'ARO590', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3209', 'S/.'),
(3264, 452, 1, 6, 52, 'ARO VARELOX ZF-T5158 13X5.5 ET35 8X100+114.3 BP', NULL, NULL, 'ARO589', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'T5158', 'S/.'),
(3265, 452, 1, 6, 52, 'ARO VARELOX ZF-519 13X5.5 ET35 8X100+114.3 BP', NULL, NULL, 'ARO588', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '519', 'S/.'),
(3266, 452, 1, 6, 52, 'ARO VARELOX ZY-8117 13X5.5 ET30 8X100+114.3 BP', NULL, NULL, 'ARO587', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8117', 'S/.'),
(3267, 452, 1, 6, 52, 'ARO VARELOX ZF-988 13X5.5 ET28 8X100+114.3 BP', NULL, NULL, 'ARO586', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '988', 'S/.'),
(3268, 452, 1, 6, 52, 'ARO VARELOX ZF-801 13X5.5 ET35 8X100+114.3 BP', NULL, NULL, 'ARO585', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '801', 'S/.'),
(3269, 452, 1, 6, 52, 'ARO VARELOX ZF-1001 13X5.5 ET30 8X100+114.3 SP', NULL, NULL, 'ARO584', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1001', 'S/.'),
(3270, 452, 1, 6, 52, 'ARO VARELOX ZF-1001 13X5.5 ET30 8X100+114.3 BP', NULL, NULL, 'ARO583', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1001', 'S/.'),
(3271, 452, 1, 6, 52, 'ARO VARELOX ZY-2794 13X5.5 ET35 8X100+114.3 BP', NULL, NULL, 'ARO582', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2794', 'S/.'),
(3272, 452, 1, 6, 52, 'ARO VARELOX ZF-10086 13X5.5 ET28 8X100+114.3 BP-B', NULL, NULL, 'ARO581', NULL, NULL, 139.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10086', 'S/.'),
(3273, 366, 1, 6, 55, 'LLANTA HABILEAD 31X10.5R15 AT DURABLEMAX', NULL, NULL, 'LHO221', NULL, NULL, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.5R15', 'S/.'),
(3274, 388, 1, 6, 53, 'BATERIA BOSCH S466D 15 PLACAS CHATA', NULL, NULL, 'BAT202', NULL, NULL, 174.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'S466D', 'S/.'),
(3275, 318, 1, 6, 55, 'LLANTA FIRESTONE 205/55R16 FR710 89T', NULL, NULL, 'LHO220', NULL, NULL, 61.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'S/.'),
(3276, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 225/55R18 HT DUELER', NULL, NULL, 'LHO219', NULL, NULL, 193.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/55R18', 'S/.'),
(3277, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/70R15 AT DUELER', NULL, NULL, 'LHO218', NULL, NULL, 155.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R15', 'S/.'),
(3278, 327, 1, 6, 55, 'LLANTA MOTO BRIDGESTONE 140/70ZR17 BATLAX 20RAZ 66H', NULL, NULL, 'LHO217', NULL, NULL, 106.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '140/70R17', 'S/.'),
(3279, 318, 1, 6, 55, 'LLANTA FIRESTONE 225/70R16 AT DESTINATION', NULL, NULL, 'LHO216', NULL, NULL, 134.17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'S/.'),
(3280, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 215/70R16 AT DUELER', NULL, NULL, 'LHO215', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'S/.'),
(3281, 363, 1, 6, 53, 'BATERIA RECORD RP130 21 PLACAS', NULL, NULL, 'BAT201', NULL, NULL, 519.47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RP130', 'S/.'),
(3282, 363, 1, 6, 53, 'BATERIA RECROD RWS 50 PLUS', NULL, NULL, 'BAT200', NULL, NULL, 201.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RWS50', 'S/.'),
(3283, 366, 1, 6, 55, 'LLANTA HABILEAD 215/70R16 AT DURABLEMAX', NULL, NULL, 'LHO214', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'S/.'),
(3284, 451, 1, 6, 55, 'LLANTA MARUTI 4.00-8 DEL GOLD 8PR', NULL, NULL, 'LHO213', NULL, NULL, 22.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4.00-8', 'S/.'),
(3285, 377, 1, 6, 55, 'LLANTA MOTO MICHELIN 80/90-17 CYTI PRO 50S', NULL, NULL, 'LHO212', NULL, NULL, 93.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '80/90-17', 'S/.'),
(3286, 377, 1, 6, 55, 'LLANTA MOTO MICHELIN 70/90-17 CYTI PRO 43S', NULL, NULL, 'LHO211', NULL, NULL, 77.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '70/90-17', 'S/.'),
(3287, 377, 1, 6, 55, 'LLANTA MOTO MICHELIN 120/90-17 SIRAC STREED 64T', NULL, NULL, 'LHO210', NULL, NULL, 288.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '120/90-17', 'S/.'),
(3288, 377, 1, 6, 55, 'LLANTA MOTO MICHELIN 150/60R17 PILOT STREET 66H', NULL, NULL, 'LHO209', NULL, NULL, 320.53, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '150/60R17', 'S/.'),
(3289, 376, 1, 6, 55, 'LLNTA MOTO DURO 130/60-13 HF903 55J', NULL, NULL, 'LHO208', NULL, NULL, 80.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '130/60-13', 'S/.'),
(3290, 447, 1, 6, 55, 'LLANTA MAXXIS 215/45R17 MAZ1 91W', NULL, NULL, 'LHO207', NULL, NULL, 68.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'S/.'),
(3291, 447, 1, 6, 55, 'LLANTA MAXXIS 215/50R17 MAZ3 91W', NULL, NULL, 'LHO206', NULL, NULL, 79.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/50R17', 'S/.'),
(3292, 447, 1, 6, 55, 'LLANTA MAXXIS 275/45R20 MAS2 111V', NULL, NULL, 'LHO205', NULL, NULL, 142.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/45R20', 'S/.'),
(3293, 415, 1, 6, 55, 'LLANTA LUCKYLAND 195/65R15 HG01 95H', NULL, NULL, 'LHO204', NULL, NULL, 40.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(3294, 338, 1, 6, 55, 'LLANTA PIRELLI 205/60R15 P7CINT 91H', NULL, NULL, 'LHO203', NULL, NULL, 84.05, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'S/.'),
(3295, 338, 1, 6, 55, 'LLANTA PIRELLI 195/65R15 P400 91H', NULL, NULL, 'LHO202', NULL, NULL, 76.69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(3296, 338, 1, 6, 55, 'LLANTA PIRELLI 175/70R14 P4CINT 84T', NULL, NULL, 'LHO201', NULL, NULL, 39.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'S/.'),
(3297, 338, 1, 6, 55, 'LLANTA PIRELLI 265/60R18 AT SCORPION', NULL, NULL, 'LHO200', NULL, NULL, 206.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/60R18', 'S/.'),
(3298, 338, 1, 6, 55, 'LLANTA PIRELLI 245/65R17 AT SCORPION', NULL, NULL, 'LHO199', NULL, NULL, 163.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/65R17', 'S/.'),
(3299, 338, 1, 6, 55, 'LLANTA PIRELLI 265/70R16 AT SCORPION', NULL, NULL, 'LHO198', NULL, NULL, 149.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(3300, 361, 1, 6, 55, 'LLANTA SOLIDEAL 26.5-25 L3 G3 E3 TUBELES LOADER 28PR', NULL, NULL, 'LHO197', NULL, NULL, 2100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '26.5-25', 'S/.'),
(3301, 389, 1, 6, 53, 'BATERIA MOTO YUASA 6N6-3B 6V', NULL, NULL, 'BAT199', NULL, NULL, 48.58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6N6-3B', 'S/.'),
(3302, 389, 1, 6, 53, 'BATERIA MOTO YUASA YTX7A-BS 12V', NULL, NULL, 'BAT198', NULL, NULL, 177.65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX7A-BS', 'S/.'),
(3303, 326, 1, 6, 55, 'LLANTA DURUN 215/70R15 A2000 97S', NULL, NULL, 'LHO196', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R15', 'S/.'),
(3304, 291, 1, 6, 55, 'LLANTA HIFLY 5.00R12 SUPER9000 88/86P 10PR', NULL, NULL, 'LHO195', NULL, NULL, 30.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5.00R12', 'S/.'),
(3305, 450, 1, 6, 55, 'LLANTA GOLDPARTNER 265/70R19.5 POS GP704 16PR', NULL, NULL, 'LHO194', NULL, NULL, 152.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R19.5', 'S/.'),
(3306, 449, 1, 6, 55, 'LLANTA YUEHENG 12.00R20 TRA YH-288 156/153K 20PR', NULL, NULL, 'LHO193', NULL, NULL, 294, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'S/.'),
(3307, 343, 1, 6, 55, 'LLANTA WINDA 165/65R14 WP15 79H', NULL, NULL, 'LHO192', NULL, NULL, 31.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R14', 'S/.'),
(3308, 343, 1, 6, 55, 'LLANTA WINDA 195/75R16 WR01 107/105R', NULL, NULL, 'LHO191', NULL, NULL, 62.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/75R16', 'S/.'),
(3309, 343, 1, 6, 55, 'LLANTA WINDA 195/65R15 WP16 95H', NULL, NULL, 'LHO190', NULL, NULL, 39.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(3310, 343, 1, 6, 55, 'LLANTA WINDA 205/60R15 WP16 91H', NULL, NULL, 'LHO189', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'S/.'),
(3311, 343, 1, 6, 55, 'LLANTA WINDA 185/70R14 WP15 88T', NULL, NULL, 'LHO188', NULL, NULL, 33.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(3312, 343, 1, 6, 55, 'LLANTA WINDA 185/70R13 WP15 86T', NULL, NULL, 'LHO187', NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'S/.'),
(3313, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 6.50-14 POS TD442 8PR', NULL, NULL, 'LHO186', NULL, NULL, 92.83, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'S/.'),
(3314, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 6.50-14 DEL TH200 8PR', NULL, NULL, 'LHO185', NULL, NULL, 85.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'S/.'),
(3315, 321, 1, 6, 55, 'LLANTA XCEED 23.5-25 L3 E3 24PR OTRR', NULL, NULL, 'LHO184', NULL, NULL, 1003.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '23.5-25', 'S/.'),
(3316, 321, 1, 6, 55, 'LLANTA XCEED 9.5R17.5 POS XD759 18PR', NULL, NULL, 'LHO183', NULL, NULL, 140.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.5R17.5', 'S/.'),
(3317, 366, 1, 6, 55, 'LLANTA HABILEAD 225/75R16 AT PRACTICALMAX', NULL, NULL, 'LHO182', NULL, NULL, 92.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'S/.'),
(3318, 366, 1, 6, 55, 'LLANTA HABILEAD 225/70R16 AT PRACTICALMAX', NULL, NULL, 'LHO181', NULL, NULL, 79.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'S/.'),
(3319, 426, 1, 6, 55, 'LLANTA CACHLAND 195/60R15 CH268 88V', NULL, NULL, 'LHO180', NULL, NULL, 34.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'S/.'),
(3320, 438, 1, 6, 55, 'LLANTA WOSEN 12R22.5 MIX WS118 152/149M 18PR', NULL, NULL, 'LHO179', NULL, NULL, 216, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'S/.'),
(3321, 438, 1, 6, 55, 'LLANTA WOSEN 11R22.5 MIX WS118 146/143M 16PR', NULL, NULL, 'LHO178', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'S/.'),
(3322, 426, 1, 6, 55, 'LLANTA CACHLAND 195/50R15 CH-861 86V', NULL, NULL, 'LHO177', NULL, NULL, 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/50R15', 'S/.'),
(3323, 349, 1, 6, 55, 'LLANTA VIKRANT 7.00-15 DEL TRACK KING 12PR', NULL, NULL, 'LHO176', NULL, NULL, 126, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-15', 'S/.'),
(3324, 443, 1, 6, 55, 'LLANTA CAMSO 12.5/80-18 SL R4 12PR IMP SUPER', NULL, NULL, 'LHO175', NULL, NULL, 223.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.5/80-18', 'S/.'),
(3325, 443, 1, 6, 55, 'LLANTA CAMSO 12-16.5 XTRA WALL 12PR SKS', NULL, NULL, 'LHO174', NULL, NULL, 206.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12-16.5', 'S/.'),
(3326, 299, 1, 6, 52, 'ARO DRAGON WHEELS D941 20X9.5 ET20 5X120 G3-MF', NULL, NULL, 'ARO580', NULL, NULL, 144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '941', 'S/.'),
(3327, 299, 1, 6, 52, 'ARO DRAGON WHEELS D972 20X8.0 ET30 5X120 G1-MF', NULL, NULL, 'ARO579', NULL, NULL, 150, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'D972', 'S/.'),
(3328, 299, 1, 6, 52, 'ARO DRAGON WHEELS D983 20X9.5 ET20 5X120 G5-MF', NULL, NULL, 'ARO578', NULL, NULL, 118.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'D983', 'S/.'),
(3329, 299, 1, 6, 52, 'ARO DRAGON WHEELS 395 18X8.5 ET25 10X100+114.3 B-P', NULL, NULL, 'ARO577', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '395', 'S/.'),
(3330, 299, 1, 6, 52, 'ARO DRAGON WHEELS 511 18X8.0 ET42 10X100+114.3 B-P', NULL, NULL, 'ARO576', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '511', 'S/.'),
(3331, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3169 14X6.0 ET25 8X100+114.3 B-P', NULL, NULL, 'ARO575', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3169', 'S/.'),
(3332, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3153 14X6.0 ET25 8X100+114.3 B-P', NULL, NULL, 'ARO574', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3153', 'S/.'),
(3333, 299, 1, 6, 52, 'ARO DRAGON WHEELS L199 14X6.0 6X139.7 ET0 BMF/LP', NULL, NULL, 'ARO573', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'L199', 'S/.'),
(3334, 317, 1, 6, 55, 'LLANTA GOOD YEAR 225/75R16 AT WRANGLER', NULL, NULL, 'LHO173', NULL, NULL, 597.89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'S/.'),
(3335, 443, 1, 6, 55, 'LLANTA CAMSO 19.5L-24 SLK 12PR R4', NULL, NULL, 'LHO172', NULL, NULL, 540, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '19.5-24', 'S/.'),
(3336, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/75R16 MT DUELER D674', NULL, NULL, 'LHO171', NULL, NULL, 174, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'S/.'),
(3337, 327, 1, 6, 55, 'LLANTA MOTO BRIDGESTONE 180/55ZR17 S20RSZ 73W', NULL, NULL, 'LHO170', NULL, NULL, 118.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '180/55R17', 'S/.'),
(3338, 327, 1, 6, 55, 'LLANTA MOTO BRIDGESTONE 120/70ZR17 BT0003 62W', NULL, NULL, 'LHO169', NULL, NULL, 91.13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '120/70R17', 'S/.'),
(3339, 327, 1, 6, 55, 'LLANTA MOTO BRIDGESTONE 110/70ZR17 003FZ 54W', NULL, NULL, 'LHO168', NULL, NULL, 88.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '110/70R17', 'S/.'),
(3340, 371, 1, 6, 53, 'BATERIA ALFA AT-19 MAXIMA DURACION', NULL, NULL, 'BAT197', NULL, NULL, 406.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AT-19', 'S/.'),
(3341, 415, 1, 6, 55, 'LLANTA LUCKYLAND 195/55R15 HG01 85V', NULL, NULL, 'LHO167', NULL, NULL, 39.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/55R15', 'S/.'),
(3342, 422, 1, 6, 55, 'LLANTA TEKPRO 185/70R13 TEK01 86T', NULL, NULL, 'LHO166', NULL, NULL, 29.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'S/.'),
(3343, 408, 1, 6, 55, 'LLANTA BKT 17.5-24 TR459 R4 12PR XTRAIL', NULL, NULL, 'LHO165', NULL, NULL, 456, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17.5-24', 'S/.'),
(3344, 280, 1, 6, 55, 'LLANTA HANKOOK 265/70R17 MT DYNAPRO', NULL, NULL, 'LHO164', NULL, NULL, 258.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'S/.'),
(3345, 371, 1, 6, 53, 'BATERIA ALFA AFF-09 MAXIMA DURACION', NULL, NULL, 'BAT196', NULL, NULL, 171.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AFF-09', 'S/.'),
(3346, 299, 1, 6, 52, 'ARO DRAGON WHEELS 746 14X6.0 ET35 8X100+114.3 B-MF', NULL, NULL, 'ARO572', NULL, NULL, 52.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '746', 'S/.'),
(3347, 299, 1, 6, 52, 'ARO DRAGON WHEELS L078 13X6.0 ET36 4X100+114.3 BMF-RED', NULL, NULL, 'ARO571', NULL, NULL, 46.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'L078', 'S/.'),
(3348, 299, 1, 6, 52, 'ARO DRAGON WHEELS 722 13X5.5 ET30 8X100+114.3 B-MF', NULL, NULL, 'ARO570', NULL, NULL, 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '722', 'S/.'),
(3349, 299, 1, 6, 52, 'ARO DRAGON WHEELS 508 13X5.5 ET25 8X100+114.3 B-HS', NULL, NULL, 'ARO569', NULL, NULL, 45.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '508', 'S/.'),
(3350, 336, 1, 6, 55, 'LLANTA MAXTREK 35X12.5R17 MT MUD TRAC', NULL, NULL, 'LHO163', NULL, NULL, 162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '35X12.5R17', 'S/.'),
(3351, 317, 1, 6, 55, 'LLANTA GOOD YEAR 6.50-16 DEL HI MILLER 8PR', NULL, NULL, 'LHO162', NULL, NULL, 59.29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-16', 'S/.'),
(3352, 317, 1, 6, 55, 'LLANTA GOOD YEAR 215/75R17.5 DEL REGIONAL 16PR', NULL, NULL, 'LHO161', NULL, NULL, 110.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R17.5', 'S/.'),
(3353, 317, 1, 6, 55, 'LLANTA GOOD YEAR 6.50-14 DEL CAMINERA 8PR', NULL, NULL, 'LHO160', NULL, NULL, 65.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'S/.'),
(3354, 317, 1, 6, 55, 'LLANTA GOOD YEAR 8.25-16 DEL HI MILER CT176 16PR', NULL, NULL, 'LHO159', NULL, NULL, 108.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(3355, 326, 1, 6, 55, 'LLANTA DURUN 195R14 D108 105/103N 8PR', NULL, NULL, 'LHO158', NULL, NULL, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'S/.'),
(3356, 326, 1, 6, 55, 'LLANTA DURUN 185/65R15 A2000 88H', NULL, NULL, 'LHO157', NULL, NULL, 38.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'S/.'),
(3357, 326, 1, 6, 55, 'LLANTA DURUN 175R13 C212 97/95Q 8PR', NULL, NULL, 'LHO156', NULL, NULL, 40.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175R13', 'S/.'),
(3358, 326, 1, 6, 55, 'LLANTA DURUN 185R14 D108 103/101Q 8PR', NULL, NULL, 'LHO155', NULL, NULL, 43.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185R14', 'S/.'),
(3359, 383, 1, 6, 55, 'LLANTA KAIZEN 12.00-20 POS L002 20PR 156/154K', NULL, NULL, 'LHO154', NULL, NULL, 288, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00-20', 'S/.'),
(3360, 447, 1, 6, 55, 'LLANTA MAXXIS 305/70R17 MT BIGHORN', NULL, NULL, 'LHO153', NULL, NULL, 199.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '305/70R17', 'S/.'),
(3361, 392, 1, 6, 52, 'ARO MAYHEM WHEELS 8010-2937 20X9 ET18 6X135+139.7 M-B', NULL, NULL, 'ARO568', NULL, NULL, 186, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8010-2937', 'S/.'),
(3362, 363, 1, 6, 53, 'BATERIA RECORD RF 95 PLUS', NULL, NULL, 'BAT195', NULL, NULL, 338.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RF95', 'S/.'),
(3363, 448, 1, 6, 55, 'LLANTA LANVIGATOR 225/75R16 AT CATCHFORS', NULL, NULL, 'LHO152', NULL, NULL, 81.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'S/.'),
(3364, 448, 1, 6, 55, 'LLANTA LANVIGATOR 195/50R15 CATCHPOWER 82V', NULL, NULL, 'LHO151', NULL, NULL, 31.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/50R15', 'S/.'),
(3365, 448, 1, 6, 55, 'LLANTA LANVIGATOR 225/65R16 MILEMAX 112/110T', NULL, NULL, 'LHO150', NULL, NULL, 61.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R16', 'S/.'),
(3366, 448, 1, 6, 55, 'LLANTA LANVIGATOR 185R14 MILAMAX 102/100R', NULL, NULL, 'LHO148', NULL, NULL, 45.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185R14', 'S/.'),
(3367, 448, 1, 6, 55, 'LLANTA LANVIGATOR 155R12 MILEMAX 88/88Q 8PR', NULL, NULL, 'LHO147', NULL, NULL, 27.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'S/.'),
(3368, 285, 1, 6, 55, 'LLANTA GOODRIDE 8.25R16 MIX CR926 128/126L 14PR', NULL, NULL, 'LHO146', NULL, NULL, 144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'S/.'),
(3369, 448, 1, 6, 55, 'LLANTA LANVIGATOR 215/75R17.5 DEL S201 135/133J 18PR', NULL, NULL, 'LHO145', NULL, NULL, 110.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R17.5', 'S/.'),
(3370, 427, 1, 6, 55, 'LLANTA SAILUN 185R14 SL12 102/100Q 8PR', NULL, NULL, 'LHO144', NULL, NULL, 47.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185R14', 'S/.'),
(3371, 427, 1, 6, 55, 'LLANTA SAILUN 195/65R14 ATREZZO 89H', NULL, NULL, 'LHO143', NULL, NULL, 37.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R14', 'S/.'),
(3372, 331, 1, 6, 55, 'LLANTA DEESTONE 245/75R16 AT PAYAK', NULL, NULL, 'LHO142', NULL, NULL, 116.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3373, 331, 1, 6, 55, 'LLANTA DEESTONE 5.00R12 R406 88/86P 10PR', NULL, NULL, 'LHO141', NULL, NULL, 43.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5.00R12', 'S/.'),
(3374, 331, 1, 6, 55, 'LLANTA DEESTONE 265/65R17 AT PAYAK', NULL, NULL, 'LHO140', NULL, NULL, 121.19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'S/.'),
(3375, 447, 1, 6, 55, 'LLANTA MAXXIS 245/75R16 MT BIGHORN', NULL, NULL, 'LHO139', NULL, NULL, 148.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3376, 447, 1, 6, 55, 'LLANTA MAXXIS 245/75R16 AT BRAVO', NULL, NULL, 'LHO138', NULL, NULL, 133.78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3377, 447, 1, 6, 55, 'LLANTA MAXXIS 195/60R15 MAT1 88T', NULL, NULL, 'LHO137', NULL, NULL, 59.57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'S/.'),
(3378, 447, 1, 6, 55, 'LLANTA MAXXIS 205/50R15 MAZ1 89V', NULL, NULL, 'LHO136', NULL, NULL, 61.31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'S/.'),
(3379, 447, 1, 6, 55, 'LLANTA MAXXIS 235/45R17 MAZ1 97W', NULL, NULL, 'LHO135', NULL, NULL, 84.07, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/45R17', 'S/.'),
(3380, 447, 1, 6, 55, 'LLANTA MAXXIS 265/65R17 MT BIGHORN', NULL, NULL, 'LHO134', NULL, NULL, 152.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'S/.'),
(3381, 447, 1, 6, 55, 'LLANTA MAXXIS 265/70R16 AT BRAVO', NULL, NULL, 'LHO133', NULL, NULL, 127.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(3382, 447, 1, 6, 55, 'LLANTA MAXXIS 265/70R16 MT BIGHORN', NULL, NULL, 'LHO132', NULL, NULL, 151.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(3383, 291, 1, 6, 55, 'LLANTA HIFLY 205/50R15 HF805 86V', NULL, NULL, 'LHO131', NULL, NULL, 39.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'S/.'),
(3384, 291, 1, 6, 55, 'LLANTA HIFLY 155R12 SUPER2000 88/86Q 8PR', NULL, NULL, 'LHO130', NULL, NULL, 28.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'S/.'),
(3385, 291, 1, 6, 55, 'LLANTA HIFLY 155/70R12 HF201 72T', NULL, NULL, 'LHO129', NULL, NULL, 23.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R12', 'S/.'),
(3386, 318, 1, 6, 55, 'LLANTA FIRESTONE 195/75R14 ST FSR3', NULL, NULL, 'LHO128', NULL, NULL, 62.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/75R14', 'S/.'),
(3387, 318, 1, 6, 55, 'LLANTA FIRESTONE 255/60R18 HT DESTINATION', NULL, NULL, 'LHO127', NULL, NULL, 144.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/60R18', 'S/.'),
(3388, 321, 1, 6, 55, 'LLANTA XCEED 7.00-15 POS XD-107 12PR', NULL, NULL, 'LHO126', NULL, NULL, 113.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-15', 'S/.'),
(3389, 322, 1, 6, 55, 'LLANTA WESTLAKE 265/60R18 AT SL369', NULL, NULL, 'LHO125', NULL, NULL, 112.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/60R18', 'S/.'),
(3390, 290, 1, 6, 55, 'LLANTA TRIANGLE 215/75R17.5 POS TR689 16PR', NULL, NULL, 'LHO124', NULL, NULL, 123.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R17.5', 'S/.'),
(3391, 414, 1, 6, 55, 'LLANTA FIREMAX 245/75R16 AT FM501', NULL, NULL, 'LHO123', NULL, NULL, 89.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3392, 416, 1, 6, 55, 'LLANTA GREMAX 245/75R16 MT CAPTURAR', NULL, NULL, 'LHO122', NULL, NULL, 109.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3393, 415, 1, 6, 55, 'LLANTA LUCKYLAND 195/60R14 LCG01 86H', NULL, NULL, 'LHO121', NULL, NULL, 36.72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'S/.'),
(3394, 312, 1, 6, 55, 'LLANTA MIRAGE 215/75R16 MR200 106/ 108Q', NULL, NULL, 'LHO120', NULL, NULL, 70.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R16', 'S/.'),
(3395, 357, 1, 6, 55, 'LLANTA SUNFULL 12R22.5 MIX HF702 18PR 152/149M', NULL, NULL, 'LHO119', NULL, NULL, 210, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'S/.'),
(3396, 291, 1, 6, 55, 'LLANTA HIFLY 275/40R20 HP801 106W', NULL, NULL, 'LHO118', NULL, NULL, 75.73, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/40R20', 'S/.');
INSERT INTO `Gen_Producto` (`IdProducto`, `IdProductoMarca`, `IdProductoFormaFarmaceutica`, `IdProductoMedicion`, `IdProductoCategoria`, `Producto`, `ProductoDesc`, `ProductoDescCorto`, `CodigoBarra`, `Codigo`, `Dosis`, `PrecioContado`, `PrecioPorMayor`, `StockPorMayor`, `StockMinimo`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `ControlaStock`, `PrecioCosto`, `VentaEstrategica`, `PorcentajeUtilidad`, `IdBloque`, `Modelo`, `Moneda`) VALUES
(3397, 293, 1, 6, 55, 'LLANTA BARUM 235/75R15 AT BRAVURIS', NULL, NULL, 'LHO117', NULL, NULL, 89.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'S/.'),
(3398, 357, 1, 6, 55, 'LLANTA SUNFULL 225/50R16 SF888 92V', NULL, NULL, 'LHO116', NULL, NULL, 47.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/50R16', 'S/.'),
(3399, 291, 1, 6, 55, 'LLANTA HIFLY 175/70R13 HF201 82T', NULL, NULL, 'LHO115', NULL, NULL, 27.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'S/.'),
(3400, 291, 1, 6, 55, 'LLANTA HIFLY 215/75R17.5 DEL HH111 16PR', NULL, NULL, 'LHO114', NULL, NULL, 103.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R17.5', 'S/.'),
(3401, 291, 1, 6, 55, 'LLANTA HIFLY 205/60R14 HF201 88H', NULL, NULL, 'LHO113', NULL, NULL, 36.47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'S/.'),
(3402, 357, 1, 6, 55, 'LLANTA SUNFULL 215/65R16 ST SF668', NULL, NULL, 'LHO112', NULL, NULL, 51.89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/65R16', 'S/.'),
(3403, 357, 1, 6, 55, 'LLANTA SUNFULL 12.00R20 TRA HF321 18PR', NULL, NULL, 'LHO111', NULL, NULL, 283.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(3404, 413, 1, 6, 55, 'LLANTA POWERTRAC 11R22.5 POS PERFORMANC. 146/143K', NULL, NULL, 'LHO110', NULL, NULL, 201.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'S/.'),
(3405, 284, 1, 6, 55, 'LLANTA ARMOUR 21-L24 SOLAR MR 14PR R4A', NULL, NULL, 'LHO109', NULL, NULL, 568.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '21-L24', 'S/.'),
(3406, 291, 1, 6, 55, 'LLANTA HIFLY 265/70R16 AT VIGOROUS', NULL, NULL, 'LHO108', NULL, NULL, 81.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(3407, 408, 1, 6, 55, 'LLANTA BKT 16.9-28 TR459 12PR TRA', NULL, NULL, 'LHO107', NULL, NULL, 464.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '16.9-28', 'S/.'),
(3408, 290, 1, 6, 55, 'LLANTA TRIANGLE 195/55R15 TR968 88H', NULL, NULL, 'LHO106', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/55R15', 'S/.'),
(3409, 373, 1, 6, 55, 'LLANTA COOPER 265/65R17 AT DISCOVERER', NULL, NULL, 'LHO105', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'S/.'),
(3410, 312, 1, 6, 55, 'LLANTA MIRAGE 195R15 MR100 106/104Q 8PR', NULL, NULL, 'LHO104', NULL, NULL, 52.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'S/.'),
(3411, 312, 1, 6, 55, 'LLANTA MIRAGE 195R14 MR100 8PR', NULL, NULL, 'LHO103', NULL, NULL, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'S/.'),
(3412, 312, 1, 6, 55, 'LLANTA MIRAGE 155R12 MR100 8PR 88/86Q', NULL, NULL, 'LHO102', NULL, NULL, 32.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'S/.'),
(3413, 447, 1, 6, 55, 'LLANTA MAXXIS 275/40R20 MAZ2 106V', NULL, NULL, 'LHO101', NULL, NULL, 145.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/40R20', 'S/.'),
(3414, 447, 1, 6, 55, 'LLANTA MAXXIS 265/65R18 AT BRAVO', NULL, NULL, 'LHO100', NULL, NULL, 150.25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R18', 'S/.'),
(3415, 447, 1, 6, 55, 'LLANTA MAXXIS 255/60R18 AT BRAVO', NULL, NULL, 'LHO099', NULL, NULL, 126.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/60R18', 'S/.'),
(3416, 447, 1, 6, 55, 'LLANTA MAXXIS 215/70R16 AT AT771', NULL, NULL, 'LHO098', NULL, NULL, 89.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'S/.'),
(3417, 447, 1, 6, 55, 'LLANTA MAXXIS 265/70R15 AT PRESA', NULL, NULL, 'LHO097', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R15', 'S/.'),
(3418, 447, 1, 6, 55, 'LLANTA MAXXIS 235/75R15 AT BRAVO', NULL, NULL, 'LHO096', NULL, NULL, 105.41, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'S/.'),
(3419, 447, 1, 6, 55, 'LLANTA MAXXIS 215/75R15 AT BRAVO', NULL, NULL, 'LHO095', NULL, NULL, 92.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(3420, 447, 1, 6, 55, 'LLANTA MAXXIS 205/75R15 MT BRAVO', NULL, NULL, 'LHO094', NULL, NULL, 87.91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/75R15', 'S/.'),
(3421, 447, 1, 6, 55, 'LLANTA MAXXIS 27X8.50R14 MT BRAVO', NULL, NULL, 'LHO093', NULL, NULL, 91.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '27X8.50R14', 'S/.'),
(3422, 447, 1, 6, 55, 'LLANTA MAXXIS 27X8.50R14 AT BRAVO', NULL, NULL, 'LHO092', NULL, NULL, 91.19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '27X8.50R14', 'S/.'),
(3423, 447, 1, 6, 55, 'LLANTA MAXXIS 205/70R15 AT BRAVO', NULL, NULL, 'LHO091', NULL, NULL, 76.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'S/.'),
(3424, 447, 1, 6, 55, 'LLANTA MAXXIS 225/45R16 MAZ3 93W', NULL, NULL, 'LHO090', NULL, NULL, 73.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/45R16', 'S/.'),
(3425, 447, 1, 6, 55, 'LLANTA MAXXIS 205/55R16 MAZ1 94W', NULL, NULL, 'LHO089', NULL, NULL, 63.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'S/.'),
(3426, 447, 1, 6, 55, 'LLANTA MAXXIS 205/50R16 MAZ1 91W', NULL, NULL, 'LHO088', NULL, NULL, 66.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(3427, 447, 1, 6, 55, 'LLANTA MAXXIS 205/55R15 MAZ1 88V', NULL, NULL, 'LHO087', NULL, NULL, 59.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R15', 'S/.'),
(3428, 447, 1, 6, 55, 'LLANTA MAXXIS 165/65R13 MAP1 77H', NULL, NULL, 'LHO086', NULL, NULL, 39.53, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'S/.'),
(3429, 447, 1, 6, 55, 'LLANTA MAXXIS 155/70R12 MA701 73H TL', NULL, NULL, 'LHO085', NULL, NULL, 33.83, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R12', 'S/.'),
(3430, 447, 1, 6, 55, 'LLANTA MAXXIS 155R12 SAKURA 8PR 88/86N', NULL, NULL, 'LHO084', NULL, NULL, 33.97, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'S/.'),
(3431, 433, 1, 6, 55, 'LLANTA TAITONG 7.50R16 POS HS918 14PR', NULL, NULL, 'LHO083', NULL, NULL, 114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'S/.'),
(3432, 433, 1, 6, 55, 'LLANTA TAITONG 7.50R16 MIX HS268 14PR', NULL, NULL, 'LHO082', NULL, NULL, 111.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'S/.'),
(3433, 416, 1, 6, 55, 'LLANTA GREMAX 265/65R17 AT MAX', NULL, NULL, 'LHO081', NULL, NULL, 88.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'S/.'),
(3434, 366, 1, 6, 55, 'LLANTA HABILEAD 215/75R15 AT PRACTICALMAX', NULL, NULL, 'LHO080', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(3435, 416, 1, 6, 55, 'LLANTA GREMAX 185R14 CF12 8PR 104/1046R', NULL, NULL, 'LHO079', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185R14', 'S/.'),
(3436, 366, 1, 6, 55, 'LLANTA HABILEAD 205/55R16 H202 88V', NULL, NULL, 'LHO078', NULL, NULL, 37.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'S/.'),
(3437, 446, 1, 6, 55, 'LLANTA HILO 165/65R14 GENESYS 72H', NULL, NULL, 'LHO077', NULL, NULL, 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R14', 'S/.'),
(3438, 446, 1, 6, 55, 'LLANTA HILO 205/60R14 GENESYS 84H', NULL, NULL, 'LHO076', NULL, NULL, 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'S/.'),
(3439, 366, 1, 6, 55, 'LLANTA HABILEAD 165R13 H202 6PR', NULL, NULL, 'LHO075', NULL, NULL, 34.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165R13', 'S/.'),
(3440, 412, 1, 6, 55, 'LLANTA EL DORADO 195R15 VAN TOUR 106/104R 8PR', NULL, NULL, 'LHO074', NULL, NULL, 52.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'S/.'),
(3441, 412, 1, 6, 55, 'LLANTA EL DORADO 205/55R16 ULTRA TOUR 94V', NULL, NULL, 'LHO073', NULL, NULL, 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'S/.'),
(3442, 311, 1, 6, 55, 'LLANTA APLUS 215/75R17.5 POS D801 18PR', NULL, NULL, 'LHO072', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R17.5', 'S/.'),
(3443, 440, 1, 6, 55, 'LLANTA SIERRA 11R22.5 POS SR301 16PR', NULL, NULL, 'LHO071', NULL, NULL, 216, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'S/.'),
(3444, 412, 1, 6, 55, 'LLANTA EL DORADO 31X10.50R15 AT ALL TERRAIN', NULL, NULL, 'LHO070', NULL, NULL, 86.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.50R15', 'US$'),
(3445, 387, 1, 6, 55, 'LLANTA GOALSTAR 245/75R16 MT CATCHFORS', NULL, NULL, 'LHO069', NULL, NULL, 114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3446, 438, 1, 6, 55, 'LLANTA WOSEN 11R22.5 TRA WS826 16PR 146/143M', NULL, NULL, 'LHO068', NULL, NULL, 198, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'S/.'),
(3447, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 195/65R15 T65 88T', NULL, NULL, 'LHO067', NULL, NULL, 62.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(3448, 357, 1, 6, 55, 'LLANTA SUNFULL 12.00R20 TRA HF707 18PR', NULL, NULL, 'LHO066', NULL, NULL, 259.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(3449, 357, 1, 6, 55, 'LLANTA SUNFULL 12.00R20 MIX HF702 18PR', NULL, NULL, 'LHO065', NULL, NULL, 252, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(3450, 438, 1, 6, 55, 'LLANTA WOSEN 12.00R20 MIX WS118 156/153K 20PR', NULL, NULL, 'LHO064', NULL, NULL, 254.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'S/.'),
(3451, 438, 1, 6, 55, 'LLANTA WOSEN 12.00R20 TRA WS678 156/154K 20PR', NULL, NULL, 'LHO063', NULL, NULL, 261.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(3452, 414, 1, 6, 55, 'LLANTA FIREMAX 165/65R13 FM316 77T', NULL, NULL, 'LHO062', NULL, NULL, 25.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'S/.'),
(3453, 438, 1, 6, 55, 'LLANTA WOSEN 12RR2.5 TRA WS678 142/149M 18PR', NULL, NULL, 'LHO061', NULL, NULL, 228, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'S/.'),
(3454, 445, 1, 6, 55, 'LLANTA DOUPRO 12.00R20 MIX YB258 18PR', NULL, NULL, 'LHO060', NULL, NULL, 268.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(3455, 358, 1, 6, 55, 'LLANTA ANNAITE 275/70R22.5 POS A785 18PR', NULL, NULL, 'LHO059', NULL, NULL, 253.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/70R22.5', 'S/.'),
(3456, 299, 1, 6, 52, 'ARO DRAGON WHEELS 508 14X6.0 ET30 4X100.0 HS/BI', NULL, NULL, 'ARO567', NULL, NULL, 62.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '508', 'S/.'),
(3457, 317, 1, 6, 55, 'LLANTA GOOD YEAR 215/60R17 HT EFICIENTGRIP 96H', NULL, NULL, 'LHO058', NULL, NULL, 150, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/60R17', 'S/.'),
(3458, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-20901 17X7.0 ET38 4X100+114.3 M-B', NULL, NULL, 'ARO566', NULL, NULL, 91.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '20901', 'S/.'),
(3459, 296, 1, 6, 52, 'ARO PDW A5162F22 20X8.5 ET33 5X114.3 EJ/1B', NULL, NULL, 'ARO565', NULL, NULL, 166.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'A5162F22', 'S/.'),
(3460, 296, 1, 6, 52, 'ARO PDW 76011F50 17X8.0 ET32 10X105+114.3 MXL-B', NULL, NULL, 'ARO564', NULL, NULL, 94.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '76011F50', 'S/.'),
(3461, 296, 1, 6, 52, 'ARO PDW 723541 17X7.0 ET38 8X100+114.3 MI/MW', NULL, NULL, 'ARO563', NULL, NULL, 93.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '723541', 'S/.'),
(3462, 296, 1, 6, 52, 'ARO PDW 30131670 16X7.0 ET35 10X100+114.3 M-B', NULL, NULL, 'ARO562', NULL, NULL, 77.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '30131670', 'S/.'),
(3463, 296, 1, 6, 52, 'ARO PDW 621356 16X6.5 ET40 8X100+114.3 M-B', NULL, NULL, 'ARO561', NULL, NULL, 75.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '621356', 'S/.'),
(3464, 296, 1, 6, 52, 'ARO PDW 681515 16X7.0 ET35 8X100+114.3 MXL-B', NULL, NULL, 'ARO560', NULL, NULL, 75.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '681515', 'S/.'),
(3465, 296, 1, 6, 52, 'ARO PDW 77021C23 17X8.0 ET20 12X135X139.7 M/UB', NULL, NULL, 'ARO559', NULL, NULL, 107.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '77021C23', 'S/.'),
(3466, 296, 1, 6, 52, 'ARO PDW 66048A03 16X7.0 ET25 6X139.7 M-B', NULL, NULL, 'ARO558', NULL, NULL, 87.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '66048A03', 'S/.'),
(3467, 296, 1, 6, 52, 'ARO PDW 6666A30 16X7.5 ET0 6X139.7 M-B', NULL, NULL, 'ARO557', NULL, NULL, 83.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6666A30', 'S/.'),
(3468, 296, 1, 6, 52, 'ARO PDW 551904 16X7.0 ETO 6X139.7 M-B', NULL, NULL, 'ARO556', NULL, NULL, 83.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '551904', 'S/.'),
(3469, 415, 1, 6, 55, 'LLANTA LUCKYLAND 205/55R16 HG01 91W', NULL, NULL, 'LHO057', NULL, NULL, 42.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'S/.'),
(3470, 422, 1, 6, 55, 'LLANTA TEKPRO 205/50R16 TEK01 87W', NULL, NULL, 'LHO056', NULL, NULL, 49.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(3471, 415, 1, 6, 55, 'LLANTA LUCKYLAND 185/70R14 LCG01 88T', NULL, NULL, 'LHO055', NULL, NULL, 33.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(3472, 280, 1, 6, 53, 'BATERIA HANKOOK MF56828 68AH 570CCA', NULL, NULL, 'BAT194', NULL, NULL, 307.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '56828', 'S/.'),
(3473, 357, 1, 6, 55, 'LLANTA SUNFULL 205/60R13 SF688 86T', NULL, NULL, 'LHO054', NULL, NULL, 33.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R13', 'S/.'),
(3474, 413, 1, 6, 55, 'LLANTA POWERTRAC 255/55R18 HT CITYRACING', NULL, NULL, 'LHO053', NULL, NULL, 72.43, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/55R18', 'S/.'),
(3475, 413, 1, 6, 55, 'LLANTA POWERTRAC 265/60R18 HT CITYROVER', NULL, NULL, 'LHO052', NULL, NULL, 81.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/60R18', 'S/.'),
(3476, 313, 1, 6, 55, 'LLANTA GENERAL 205/60R13 GTMAX 86H', NULL, NULL, 'LHO050', NULL, NULL, 52.31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R13', 'S/.'),
(3477, 357, 1, 6, 55, 'LLANTA SUNFULL 12R22.5 TRA HF768 18PR', NULL, NULL, 'LHO049', NULL, NULL, 252.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'S/.'),
(3478, 413, 1, 6, 55, 'LLANTA POWERTRAC 225/70R16 AT LANDER', NULL, NULL, 'LHO048', NULL, NULL, 64.38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'US$'),
(3479, 413, 1, 6, 55, 'LLANTA POWERTRAC 215/70R16 AT LANDER', NULL, NULL, 'LHO047', NULL, NULL, 55.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'S/.'),
(3480, 413, 1, 6, 55, 'LLANTA POWERTRAC 225/75R15 AT LANDER', NULL, NULL, 'LHO046', NULL, NULL, 63.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R15', 'S/.'),
(3481, 299, 1, 6, 52, 'ARO DRAGON WHEELS L905 17X9.0 ET30 6X114.3 MF-L', NULL, NULL, 'ARO554', NULL, NULL, 91.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'L905', 'S/.'),
(3482, 299, 1, 6, 52, 'ARO DRAGON WHEELS L849 16X8.0 ET30 6X139.7 ML-P', NULL, NULL, 'ARO553', NULL, NULL, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'L849', 'S/.'),
(3483, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3730 15X7.0 ET30 8X100+114.3 B-LP', NULL, NULL, 'ARO552', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3730', 'S/.'),
(3484, 299, 1, 6, 52, 'ARO DRAGON WHEELS L405 15X8.0 ET20 4X100.0 M-B', NULL, NULL, 'ARO551', NULL, NULL, 64.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'L405', 'S/.'),
(3485, 299, 1, 6, 52, 'ARO DRAGON WHEELS L200 15X6.5 ET36 5X114.3 MF-B', NULL, NULL, 'ARO550', NULL, NULL, 67.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'L200', 'S/.'),
(3486, 365, 1, 6, 53, 'BATERIA CAPSA 481MK 15 PLACAS MAXIMA', NULL, NULL, 'BAT193', NULL, NULL, 323.05, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '481MK', 'S/.'),
(3487, 336, 1, 6, 55, 'LLANTA MAXTREK 205/60R15 MAXIMUS 91H', NULL, NULL, 'LHO045', NULL, NULL, 43.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'S/.'),
(3488, 341, 1, 6, 55, 'LLANTA ROADSHINE 165/65R14 RS907 79H', NULL, NULL, 'LHO044', NULL, NULL, 33.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R14', 'S/.'),
(3489, 313, 1, 6, 55, 'LLANTA GENERAL 165/65R14 ALTIMAX 79T', NULL, NULL, 'LHO043', NULL, NULL, 41.58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R14', 'S/.'),
(3490, 444, 1, 6, 55, 'LLANTA MASTERCRAFT 265/70R17 AT WILDCAT', NULL, NULL, 'LHO042', NULL, NULL, 126.38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'S/.'),
(3491, 444, 1, 6, 55, 'LLANTA MASTERCRAFT 265/70R16 AT WILDCAT', NULL, NULL, 'LHO041', NULL, NULL, 122.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(3492, 444, 1, 6, 55, 'LLANTA MASTERCRAFT 185/70R14 STRATEGY 88T', NULL, NULL, 'LHO040', NULL, NULL, 37.07, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(3493, 444, 1, 6, 55, 'LLANTA MASTERCRAFT 185/70R13 STRATEGY 85S', NULL, NULL, 'LHO039', NULL, NULL, 36.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'S/.'),
(3494, 444, 1, 6, 55, 'LLANTA MASTERCRAFT 175/70R14 STRATEGY 84S', NULL, NULL, 'LHO038', NULL, NULL, 36.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'S/.'),
(3495, 444, 1, 6, 55, 'LLANTA MASTERCRAFT 175/70R13 STRATEGY 82T', NULL, NULL, 'LHO037', NULL, NULL, 31.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(3496, 443, 1, 6, 55, 'LLANTA CAMSO 14.00-24 L3 G3 SLIK 16PR', NULL, NULL, 'LHO036', NULL, NULL, 564, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '14.00-24', 'S/.'),
(3497, 311, 1, 6, 55, 'LLANTA APLUS 215/70R16 AT A929', NULL, NULL, 'LHO035', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'S/.'),
(3498, 311, 1, 6, 55, 'LLANTA APLUS 265/75R16 MT A929', NULL, NULL, 'LHO034', NULL, NULL, 117.72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'S/.'),
(3499, 330, 1, 6, 55, 'LLANTA KUMHO 235/75R15 AT ROADVENTURE', NULL, NULL, 'LHO033', NULL, NULL, 114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'S/.'),
(3500, 330, 1, 6, 55, 'LLANTA KUMHO 205/60R14 KU33 88H', NULL, NULL, 'LHO032', NULL, NULL, 63.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'S/.'),
(3501, 311, 1, 6, 55, 'LLANTA APLUS 205/50R16 A607 91W', NULL, NULL, 'LHO031', NULL, NULL, 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(3502, 361, 1, 6, 55, 'LLANTA SOLIDEAL 20.5-25 LOAD MASTER L3 G3 20PR', NULL, NULL, 'LHO030', NULL, NULL, 1032, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '20.5-25', 'S/.'),
(3503, 289, 1, 6, 55, 'LLANTA OTANI 19.5-24 12PR G-45 R4 RETAIL', NULL, NULL, 'LHO028', NULL, NULL, 480, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '19.5-24', 'S/.'),
(3504, 442, 1, 6, 55, 'LLANTA TRANSTONE 12.5/80-18 12PR R4 START', NULL, NULL, 'LHO027', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.5/80-18', 'S/.'),
(3505, 441, 1, 6, 52, 'ARO YOYO Y-666 8X3.5 ET30 4X106.00 BL-RS', NULL, NULL, 'ARO549', NULL, NULL, 36.37, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '666', 'S/.'),
(3506, 440, 1, 6, 55, 'LLANTA SIERRA 11R22.5 TRA SR317 16PR', NULL, NULL, 'LHO026', NULL, NULL, 216, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'S/.'),
(3507, 392, 1, 6, 52, 'ARO MAYHEM 8102-8937 18X9.0 ET18 6X135+139.7 BM-S', NULL, NULL, 'ARO548', NULL, NULL, 172.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8102-8937', 'S/.'),
(3508, 392, 1, 6, 52, 'ARO MAYHEM 8090-7937 17X9.0 ET18 6X135+139.7 M-B', NULL, NULL, 'ARO547', NULL, NULL, 124.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8090-7937', 'S/.'),
(3509, 392, 1, 6, 52, 'ARO MAYHEM 8010-7837 17X8.0 ET10 6X135+139.7 M-T', NULL, NULL, 'ARO546', NULL, NULL, 117.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8010-7837', 'S/.'),
(3510, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-38309 16X7.0 ET31 4X100+114.3 FS-B3', NULL, NULL, 'ARO545', NULL, NULL, 72.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '38309', 'S/.'),
(3511, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-37904 16X7.5 ET30 4X100.0 FS-B3', NULL, NULL, 'ARO544', NULL, NULL, 73.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '37904', 'S/.'),
(3512, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-19112 15X7.0 ET30 5X100+114.3 M-B', NULL, NULL, 'ARO543', NULL, NULL, 62.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '19112', 'S/.'),
(3513, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-37902 15X7.0 ET33 4X100+114.3 BM-FS', NULL, NULL, 'ARO542', NULL, NULL, 60.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '37902', 'S/.'),
(3514, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-63002 16X7.0 ET35 6X139.7 M-B', NULL, NULL, 'ARO541', NULL, NULL, 80.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '63002', 'S/.'),
(3515, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-66508 16X8.0 ET0 6X139.7 FBS-3CI', NULL, NULL, 'ARO540', NULL, NULL, 97.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '66508', 'S/.'),
(3516, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-66508 16X8.0 ET0 6X139.7 FB-BRD', NULL, NULL, 'ARO539', NULL, NULL, 96.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '66508', 'S/.'),
(3517, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6349 16X8.0 ET10 6X139.7 M-B', NULL, NULL, 'ARO538', NULL, NULL, 79.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6349', 'S/.'),
(3518, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG2001 15X10.0 ET44 6X139.7 BM-L', NULL, NULL, 'ARO537', NULL, NULL, 78.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG2001', 'S/.'),
(3519, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6349 15X8.0 ET13 6X139.7 M-B', NULL, NULL, 'ARO536', NULL, NULL, 68.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6349', 'S/.'),
(3520, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-63911 15X8.0 ET10 6X139.7 M-B', NULL, NULL, 'ARO535', NULL, NULL, 68.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '63911', 'S/.'),
(3521, 439, 1, 6, 55, 'LLANTA FESITE 12.00R24 MIX HF702 20PR', NULL, NULL, 'LHO025', NULL, NULL, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R24', 'S/.'),
(3522, 280, 1, 6, 55, 'LLANTA HANKOOK 235/55R19 DYNAPRO 104H', NULL, NULL, 'LHO024', NULL, NULL, 146.02, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/55R19', 'S/.'),
(3523, 280, 1, 6, 55, 'LLANTA HANKOOK 185/70R13 K715 88H', NULL, NULL, 'LHO023', NULL, NULL, 41.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'S/.'),
(3524, 338, 1, 6, 55, 'LLANTA PIRELLI 245/70R16 AT SCORPION', NULL, NULL, 'LHO021', NULL, NULL, 118.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R16', 'S/.'),
(3525, 338, 1, 6, 55, 'LLANTA PIRELLI 185/70R14 P400 88H', NULL, NULL, 'LHO020', NULL, NULL, 41.27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(3526, 413, 1, 6, 55, 'LLANTA POWERTRAC 205/50R16 CITY RACING 91W', NULL, NULL, 'LHO019', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(3527, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LGS1113 16X8.0 ET0 5X114.3 B-M', NULL, NULL, 'ARO534', NULL, NULL, 82.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LGS1113', 'S/.'),
(3528, 317, 1, 6, 55, 'LLANTA GOOD YEAR 9.00-20 DEL CAMINERA 14PR', NULL, NULL, 'LHO018', NULL, NULL, 1148.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.00-20', 'S/.'),
(3529, 317, 1, 6, 55, 'LLANTA GOOD YEAR 9.00-20 POS CHASQUI 14PR', NULL, NULL, 'LHO017', NULL, NULL, 1207.66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.00-20', 'S/.'),
(3530, 317, 1, 6, 55, 'LLANTA GOOD YEAR 235/60R16 HT WRANGLER SUV 100H', NULL, NULL, 'LHO016', NULL, NULL, 626.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R16', 'S/.'),
(3531, 433, 1, 6, 55, 'LLANTA TAITONG 12.00R24 MIX HS268 20PR', NULL, NULL, 'LHO015', NULL, NULL, 276, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R24', 'S/.'),
(3532, 415, 1, 6, 55, 'LLANTA LUCKYLAND 165/65R13 LCG01 77Q', NULL, NULL, 'LHO014', NULL, NULL, 30.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'S/.'),
(3533, 333, 1, 6, 55, 'LLANTA WANDA 195R15 WR092 106/104Q 8PR', NULL, NULL, 'LHO013', NULL, NULL, 49.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'S/.'),
(3534, 339, 1, 6, 55, 'LLANTA ACCELERA 255/55R18 ALPHA 92W', NULL, NULL, 'LHO012', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/55R18', 'S/.'),
(3535, 377, 1, 6, 55, 'LLANTA MOTO MICHELIN 120/80-18 T63 62S TT', NULL, NULL, 'LHO011', NULL, NULL, 219.65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '120/80-18', 'S/.'),
(3536, 377, 1, 6, 55, 'LLANTA MOTO MICHELIN 90/90-18 SIRAC STREED', NULL, NULL, 'LHO010', NULL, NULL, 110.29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90/90-18', 'S/.'),
(3537, 376, 1, 6, 55, 'LLANTA MOTO DURO 90/90-18 HF329 PISTERA', NULL, NULL, 'LHO009', NULL, NULL, 101.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90/90-18', 'S/.'),
(3538, 376, 1, 6, 55, 'LLANTA MOTO DURO 120/80-18 MEDIAN 904 HF', NULL, NULL, 'LHO008', NULL, NULL, 166.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '120/80-18', 'S/.'),
(3539, 376, 1, 6, 55, 'LLANTA MOTO DURO 90/90-17 HF918 49P', NULL, NULL, 'LHO007', NULL, NULL, 77.15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90/90-17', 'S/.'),
(3540, 363, 1, 6, 53, 'BATERIA RECORD 105D31L 15 PLACAS', NULL, NULL, 'BAT192', NULL, NULL, 361.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '105D31L', 'S/.'),
(3541, 363, 1, 6, 53, 'BATERIA RECORD 90D26L 15 PLACAS', NULL, NULL, 'BAT191', NULL, NULL, 286.03, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90D26L', 'S/.'),
(3542, 363, 1, 6, 53, 'BATERIA RECORD 56800 13 PLACAS', NULL, NULL, 'BAT190', NULL, NULL, 283.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '56800', 'S/.'),
(3543, 363, 1, 6, 53, 'BATERIA RECORD 55B24L 11 PLACAS', NULL, NULL, 'BAT189', NULL, NULL, 223.73, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55B24L', 'S/.'),
(3544, 363, 1, 6, 53, 'BATERIA RECORD 44B19L 11 PLACAS', NULL, NULL, 'BAT188', NULL, NULL, 194.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '44B19L', 'S/.'),
(3545, 353, 1, 6, 55, 'LLANTA AEOLUS 12R22.5 POS HN10 18PR', NULL, NULL, 'LHO006', NULL, NULL, 349.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'S/.'),
(3546, 353, 1, 6, 55, 'LLANTA AEOLUS 12R22.5 MIX HN08 18PR', NULL, NULL, 'LHO005', NULL, NULL, 302.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'S/.'),
(3547, 353, 1, 6, 55, 'LLANTA AEOLUS 9.5R17.5 POS ADR35 18PR', NULL, NULL, 'LHO004', NULL, NULL, 174.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.5R17.5', 'S/.'),
(3548, 353, 1, 6, 55, 'LLANTA AEOLUS 9.5R17.5 DEL ATR55 14PR', NULL, NULL, 'LHO003', NULL, NULL, 174.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.5R17.5', 'S/.'),
(3549, 353, 1, 6, 55, 'LLANTA AEOLUS 12.00R20 MIX HN10 18PR', NULL, NULL, 'LHO002', NULL, NULL, 402, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(3550, 353, 1, 6, 55, 'LLANTA AEOLUS 12.00R20 TRA HN08 18PR', NULL, NULL, 'LHO001', NULL, NULL, 402, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(3551, 318, 1, 6, 55, 'LLANTA FIRESTONE 215/75R14 AT DESTINATION', NULL, NULL, 'LHO000', NULL, NULL, 86.69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R14', 'S/.'),
(3552, 318, 1, 6, 55, 'LLANTA FIRESTONE 205/65R15 AT DESTINATION', NULL, NULL, 'LHO999', NULL, NULL, 81.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/65R15', 'S/.'),
(3553, 438, 1, 6, 55, 'LLANTA WOSEN 12.00R20 TRA WS658 156/153K 20PR', NULL, NULL, 'LHO998', NULL, NULL, 282, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(3554, 280, 1, 6, 53, 'BATERIA HANKOOK MF50D20L 11 PLACAS CUADRADA', NULL, NULL, 'BAT187', NULL, NULL, 212.69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '50D20L', 'S/.'),
(3555, 321, 1, 6, 55, 'LLANTA XCEED 9.5R17.5 DEL XD414 132/130K 18PR', NULL, NULL, 'LHO997', NULL, NULL, 132.25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.5R17.5', 'S/.'),
(3556, 359, 1, 6, 55, 'LLANTA SPORTRAK 8.25R16 POS BY35 16PR', NULL, NULL, 'LHO996', NULL, NULL, 136.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'S/.'),
(3557, 313, 1, 6, 55, 'LLANTA GENERAL 165/65R13 ALTIMAX 77T', NULL, NULL, 'LHO995', NULL, NULL, 38.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'US$'),
(3558, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1118 16X8.0 ET0 6X139.7 B-MF', NULL, NULL, 'ARO533', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1118', 'S/.'),
(3559, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1075A 16X7.5 ET32 4X100.0 G-MF', NULL, NULL, 'ARO532', NULL, NULL, 69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1075A', 'S/.'),
(3560, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3215 15X6.5 ET38 8X100+114.3 B-P', NULL, NULL, 'ARO531', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3215', 'S/.'),
(3561, 299, 1, 6, 52, 'ARO DRAGON WHEELS 8104 15X6.0 ET20 8X100+114.3 B-P', NULL, NULL, 'ARO530', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8104', 'S/.'),
(3562, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3215 14X6.0 ET35 8X114+114.3 B-P', NULL, NULL, 'ARO529', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3215', 'S/.'),
(3563, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1143 17X9.0 ET0 12X135+139.7 LA5-B', NULL, NULL, 'ARO528', NULL, NULL, 94.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1143', 'S/.'),
(3564, 299, 1, 6, 52, 'ARO DRAGON WHEELS 528 15X7.0 ET30 4X100.0 B-HS', NULL, NULL, 'ARO526', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '528', 'S/.'),
(3565, 299, 1, 6, 52, 'ARO DRAGON WHEELS 126 15X7.5 ET0 5X114.3 B-MF', NULL, NULL, 'ARO525', NULL, NULL, 67.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '126', 'S/.'),
(3566, 299, 1, 6, 52, 'ARO DRAGON WHEELS 126 15X7.5 ET0 5X139.7 B-MF', NULL, NULL, 'ARO524', NULL, NULL, 75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '126', 'S/.'),
(3567, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1075A 16X7.5 ET32 4X100.0 B-MF', NULL, NULL, 'ARO523', NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1075A', 'S/.'),
(3568, 299, 1, 6, 52, 'ARO DRAGON WHEELS 307 16X7.0 ET0 6X139.7 B-MF', NULL, NULL, 'ARO522', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '307', 'S/.'),
(3569, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3206 15X6.5 ET25 8X100+114.3 B-P', NULL, NULL, 'ARO521', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3206', 'S/.'),
(3570, 299, 1, 6, 52, 'ARO DRAGON WHEELS 395 15X7.0 ET25 8X100+114.3 B-P', NULL, NULL, 'ARO520', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '395', 'S/.'),
(3571, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3718Z 15X6.5 ET35 8X100+114.3 B-PR', NULL, NULL, 'ARO519', NULL, NULL, 62.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3718Z', 'S/.'),
(3572, 299, 1, 6, 52, 'ARO DRAGON WHEELS 337 15X6.5 ET18 8X100+114.3 R-BP', NULL, NULL, 'ARO518', NULL, NULL, 62.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '337', 'S/.'),
(3573, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3111Z 14X6.0 ET35 8X100+114.3 BP', NULL, NULL, 'ARO517', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3111Z', 'S/.'),
(3574, 299, 1, 6, 52, 'ARO DRAGON WHEELS D2769 15X6.5 ET20 8X100+114.3 B-P', NULL, NULL, 'ARO516', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'D2769', 'S/.'),
(3575, 299, 1, 6, 52, 'ARO DRAGON WHEELS 712 13X5.5 ET25 8X100+114.3 B-MF', NULL, NULL, 'ARO515', NULL, NULL, 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '712', 'S/.'),
(3576, 299, 1, 6, 52, 'ARO DRAGON WHEELS 528 13X5.5 ET25 4X100.0 B-HS', NULL, NULL, 'ARO514', NULL, NULL, 45.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '528', 'S/.'),
(3577, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3268 13X5.5 ET25 8X100+114.3 BP', NULL, NULL, 'ARO513', NULL, NULL, 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3268', 'S/.'),
(3578, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3215 13X5.5 ET35 8X100+114.3 BP', NULL, NULL, 'ARO512', NULL, NULL, 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3215', 'S/.'),
(3579, 357, 1, 6, 55, 'LLANTA SUNFULL 205/70R15 SF05 8PR', NULL, NULL, 'LHO994', NULL, NULL, 49.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'S/.'),
(3580, 357, 1, 6, 55, 'LLANTA SUNFULL 215/75R15 AT AT782', NULL, NULL, 'LHO993', NULL, NULL, 63.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(3581, 357, 1, 6, 55, 'LLANTA SUNFULL 155R12 SF01 8PR 88/86Q', NULL, NULL, 'LHO992', NULL, NULL, 29.66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'S/.'),
(3582, 434, 1, 6, 55, 'LLANTA SONAR 235/40R17 SX1-EVO 90V', NULL, NULL, 'LHO991', NULL, NULL, 77.66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/40R17', 'S/.'),
(3583, 434, 1, 6, 55, 'LLANTA SONAR 225/45R17 SX1-EVO 94V', NULL, NULL, 'LHO990', NULL, NULL, 74.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/45R17', 'S/.'),
(3584, 434, 1, 6, 55, 'LLANTA SONAR 215/50R17 SX1-EVO 91V', NULL, NULL, 'LHO989', NULL, NULL, 72.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/50R17', 'S/.'),
(3585, 434, 1, 6, 55, 'LLANTA SONAR 215/45R17 SX1-EVO 91V', NULL, NULL, 'LHO988', NULL, NULL, 68.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'S/.'),
(3586, 388, 1, 6, 53, 'BATERIA BOSCH S3154D N150 25 PLACAS', NULL, NULL, 'BAT186', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'S3154D', 'S/.'),
(3587, 386, 1, 6, 55, 'LLANTA SUNWIDE 205/75R15 ST TRAVOMATE', NULL, NULL, 'LHO987', NULL, NULL, 58.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/75R15', 'S/.'),
(3588, 386, 1, 6, 55, 'LLANTA SUNWIDE 225/75R16 AT DUREVOLE', NULL, NULL, 'LHO986', NULL, NULL, 84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'S/.'),
(3589, 386, 1, 6, 55, 'LLANTA SUNWIDE 175/70R14 ROLIT6 84T', NULL, NULL, 'LHO985', NULL, NULL, 30.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'S/.'),
(3590, 386, 1, 6, 55, 'LLANTA SUNWIDE 175/65R14 ROLIT6 86T', NULL, NULL, 'LHO984', NULL, NULL, 29.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'S/.'),
(3591, 438, 1, 6, 55, 'LLANTA WOSEN 8.25R20 POS WS684 139/137K 16PR', NULL, NULL, 'LHO983', NULL, NULL, 178.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R20', 'S/.'),
(3592, 438, 1, 6, 55, 'LLANTA WOSEN 7.50R16 POS WS648 122/118L 14PR', NULL, NULL, 'LHO982', NULL, NULL, 128.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'S/.'),
(3593, 438, 1, 6, 55, 'LLANTA WOSEN 7.50R16 MIX WS118 122/118L 14PR', NULL, NULL, 'LHO981', NULL, NULL, 118.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'S/.'),
(3594, 438, 1, 6, 55, 'LLANTA WOSEN 8.25R16 POS WS684 128/124L 16PR', NULL, NULL, 'LHO980', NULL, NULL, 142.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'S/.'),
(3595, 290, 1, 6, 55, 'LLANTA TRIANGLE 215/45R17 TR968 91V', NULL, NULL, 'LHO979', NULL, NULL, 53.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'S/.'),
(3596, 290, 1, 6, 55, 'LLANTA TRIANGLE 195/50R15 TR928 82H', NULL, NULL, 'LHO978', NULL, NULL, 58.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/50R15', 'S/.'),
(3597, 290, 1, 6, 55, 'LLANTA TRIANGLE 195/60R15 TR928 88H', NULL, NULL, 'LHO977', NULL, NULL, 44.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'S/.'),
(3598, 290, 1, 6, 55, 'LLANTA TRIANGLE 185/60R15 TR928 84H', NULL, NULL, 'LHO976', NULL, NULL, 37.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R15', 'S/.'),
(3599, 388, 1, 6, 53, 'BATERIA BOSCH S455D 11 PLACAS CHATA', NULL, NULL, 'BAT185', NULL, NULL, 100.77, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'S455DH', 'S/.'),
(3600, 366, 1, 6, 55, 'LLANTA HABILEAD 265/75R16 AT PRACTICALMAX', NULL, NULL, 'LHO975', NULL, NULL, 106.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'S/.'),
(3601, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 225/75R16 AT DUELER', NULL, NULL, 'LHO974', NULL, NULL, 162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'S/.'),
(3602, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 275/70R16 AT DUELER', NULL, NULL, 'LHO973', NULL, NULL, 212.53, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/70R16', 'S/.'),
(3603, 412, 1, 6, 55, 'LLANTA EL DORADO 195R14 106/104Q VAN TOUR 8PR', NULL, NULL, 'LHO972', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'S/.'),
(3604, 412, 1, 6, 55, 'LLANTA EL DORADO 245/75R16 MT MUD TERRAIN', NULL, NULL, 'LHO971', NULL, NULL, 105.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3605, 412, 1, 6, 55, 'LLANTA EL DORADO 265/50R20 AT ALL TERRAIN', NULL, NULL, 'LHO970', NULL, NULL, 114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/50R20', 'S/.'),
(3606, 412, 1, 6, 55, 'LLANTA EL DORADO 245/65R17 AT ALL TERRAIN', NULL, NULL, 'LHO969', NULL, NULL, 81.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/65R17', 'S/.'),
(3607, 412, 1, 6, 55, 'LLANTA EL DORADO 245/75R16 AT ALL TERRAIN', NULL, NULL, 'LHO968', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3608, 361, 1, 6, 55, 'LLANTA SOLIDEAL 12.5/80-18 12PR COMSO L2', NULL, NULL, 'LHO967', NULL, NULL, 219.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.5/80-18', 'S/.'),
(3609, 437, 1, 6, 55, 'LLANTA FORERUNER 12.5/80-18 12PR', NULL, NULL, 'LHO966', NULL, NULL, 259.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.5/80-18', 'S/.'),
(3610, 296, 1, 6, 52, 'ARO PDW 56027F28 15X7.0 ET0 6X139.7 M-B', NULL, NULL, 'ARO511', NULL, NULL, 75.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '56027F28', 'S/.'),
(3611, 296, 1, 6, 52, 'ARO PDW 5518623 15X7.0 ET0 5X114.3 M-B', NULL, NULL, 'ARO510', NULL, NULL, 71.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5518623', 'S/.'),
(3612, 296, 1, 6, 52, 'ARO PDW 5504650 15X8.0 ET15 6X139.7 M-B', NULL, NULL, 'ARO509', NULL, NULL, 75.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5504650', 'S/.'),
(3613, 296, 1, 6, 52, 'ARO PDW 8516220 18X8.0 ET35 5X114.3 B-', NULL, NULL, 'ARO508', NULL, NULL, 123.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8516220', 'S/.'),
(3614, 296, 1, 6, 52, 'ARO PDW 781326 17X7.0 ET35 8X100+114.3 MI-B', NULL, NULL, 'ARO507', NULL, NULL, 94.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '781326', 'S/.'),
(3615, 296, 1, 6, 52, 'ARO PDW 653774 16X7.0 ET35 10X100+114.3 R-M', NULL, NULL, 'ARO506', NULL, NULL, 75.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '653774', 'S/.'),
(3616, 296, 1, 6, 52, 'ARO PDW 6901344 16X7.0 ET35 10X100+114.3 M-B', NULL, NULL, 'ARO505', NULL, NULL, 77.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6901344', 'S/.'),
(3617, 296, 1, 6, 52, 'ARO PDW 5521707 15X7.0 ET40 5X100.0 M-B', NULL, NULL, 'ARO504', NULL, NULL, 68.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5521707', 'S/.'),
(3618, 296, 1, 6, 52, 'ARO PDW 526917 15X7.0 ET35 4X100.0 M-B', NULL, NULL, 'ARO503', NULL, NULL, 64.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '526917', 'S/.'),
(3619, 391, 1, 6, 52, 'ARO ION 184-7937 17X9.0 ET18 6X135+139.7 BM-S', NULL, NULL, 'ARO502', NULL, NULL, 152.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '184-7937', 'S/.'),
(3620, 296, 1, 6, 52, 'ARO PDW 7603243 17X8.0 ET30 6X114.3 M-B', NULL, NULL, 'ARO501', NULL, NULL, 104.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7603243', 'S/.'),
(3621, 296, 1, 6, 52, 'ARO PDW 6602807 16X7.0 ET10 6X139.7 M-B', NULL, NULL, 'ARO500', NULL, NULL, 75.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6602807', 'S/.'),
(3622, 296, 1, 6, 52, 'ARO PDW 6511207 16X7.0 ET0 6X139.7 MI-B', NULL, NULL, 'ARO499', NULL, NULL, 81.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6511207', 'S/.'),
(3623, 296, 1, 6, 52, 'ARO PDW 4703713 14X6.5 ET20 4X100.0 M-B', NULL, NULL, 'ARO498', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4703713', 'S/.'),
(3624, 296, 1, 6, 52, 'ARO PDW 102814550 14X5.5 ET35 4X100.0 M-B', NULL, NULL, 'ARO497', NULL, NULL, 51.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '102814550', 'S/.'),
(3625, 296, 1, 6, 52, 'ARO PDW 385038 13X5.5 ET35 4X100.0 B-', NULL, NULL, 'ARO496', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '385038', 'S/.'),
(3626, 296, 1, 6, 52, 'ARO PDW 390343 13X5.5 ET35 4X100.0 M-B', NULL, NULL, 'ARO495', NULL, NULL, 45.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '390343', 'S/.'),
(3627, 296, 1, 6, 52, 'ARO PDW 362742 13X5.5 ET35 4X100.0 M-B', NULL, NULL, 'ARO494', NULL, NULL, 45.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '362742', 'S/.'),
(3628, 296, 1, 6, 52, 'ARO PDW 356276 13X5.5 ET35 4X100.0 M-B', NULL, NULL, 'ARO493', NULL, NULL, 45.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '356276', 'S/.'),
(3629, 296, 1, 6, 52, 'ARO PDW 356276 13X5.5 ET35 4X100.0 B-M', NULL, NULL, 'ARO492', NULL, NULL, 45.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '356276', 'S/.'),
(3630, 296, 1, 6, 52, 'ARO PDW 3587M33 13X6.0 ET70 4X114.3 B-M', NULL, NULL, 'ARO491', NULL, NULL, 47.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3587M33', 'S/.'),
(3631, 296, 1, 6, 52, 'ARO PDW 3587M32 13X6.0 ET70 4X100.0 M-B', NULL, NULL, 'ARO490', NULL, NULL, 46.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3587M32', 'S/.'),
(3632, 296, 1, 6, 52, 'ARO PDW 3522B15 13X5.5 ET35 4X100.0 M-B', NULL, NULL, 'ARO489', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3522B15', 'S/.'),
(3633, 281, 1, 6, 55, 'LLANTA DUNLOP 265/50R20 HT SPORT MAX 111Y', NULL, NULL, 'LHO965', NULL, NULL, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/50R20', 'S/.'),
(3634, 436, 1, 6, 55, 'LLANTA RINTAL 12-16.5 SKS4 L2 12PR NON DIRECCINAL', NULL, NULL, 'LHO964', NULL, NULL, 129.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12-16.5', 'S/.');
INSERT INTO `Gen_Producto` (`IdProducto`, `IdProductoMarca`, `IdProductoFormaFarmaceutica`, `IdProductoMedicion`, `IdProductoCategoria`, `Producto`, `ProductoDesc`, `ProductoDescCorto`, `CodigoBarra`, `Codigo`, `Dosis`, `PrecioContado`, `PrecioPorMayor`, `StockPorMayor`, `StockMinimo`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `ControlaStock`, `PrecioCosto`, `VentaEstrategica`, `PorcentajeUtilidad`, `IdBloque`, `Modelo`, `Moneda`) VALUES
(3635, 379, 1, 6, 53, 'BATERIA DAEWOO MF75D31L 13 PLACAS', NULL, NULL, 'BAT184', NULL, NULL, 92.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '75D31L', 'S/.'),
(3636, 379, 1, 6, 53, 'BATERIA DAEWOO MF50D20L 11 PLACAS', NULL, NULL, 'BAT183', NULL, NULL, 65.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '50D20L', 'S/.'),
(3637, 392, 1, 6, 52, 'ARO MAYHEM 8090 20X10.0 ET25 6X139.7/6X135.6 BM-S', NULL, NULL, 'ARO488', NULL, NULL, 186.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8090', 'S/.'),
(3638, 392, 1, 6, 52, 'ARO MAYHEM 9101 20X10 ET25 6X135.7/6X135.6 B-M', NULL, NULL, 'ARO487', NULL, NULL, 221.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9101', 'S/.'),
(3639, 434, 1, 6, 55, 'LLANTA SONAR 245/40R18 SX-1EVO 97W', NULL, NULL, 'LHO962', NULL, NULL, 93.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/40R18', 'S/.'),
(3640, 280, 1, 6, 55, 'LLANTA HANKOOK 265/70R16 MT DYNAPRO', NULL, NULL, 'LHO961', NULL, NULL, 205.67, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(3641, 280, 1, 6, 55, 'LLANTA HANKOOK 225/65R17 HT DYNAPRO', NULL, NULL, 'LHO960', NULL, NULL, 120.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'S/.'),
(3642, 365, 1, 6, 53, 'BATERIA CAPSA 1765 PREMIUM', NULL, NULL, 'BAT182', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1765', 'S/.'),
(3643, 288, 1, 6, 55, 'LLANTA ADVANCE 11R24.5 L3 GL909A 16PR', NULL, NULL, 'LHO959', NULL, NULL, 316.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R24.5', 'S/.'),
(3644, 357, 1, 6, 55, 'LLANTA SUNFULL 235/70R16 AT AT782', NULL, NULL, 'LHO958', NULL, NULL, 69.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/70R16', 'S/.'),
(3645, 435, 1, 6, 55, 'LLANTA BLACKLION 275/70R22.5 DEL BA126 148/145M 18PR', NULL, NULL, 'LHO957', NULL, NULL, 210, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/70R22.5', 'S/.'),
(3646, 358, 1, 6, 55, 'LLANTA ANNAITE 265/70R19.5 DEL 366 148/145M 16PR', NULL, NULL, 'LHO956', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R19.5', 'S/.'),
(3647, 291, 1, 6, 55, 'LLANTA HIFLY 9.5R17.5 DEL HH121 16PR', NULL, NULL, 'LHO955', NULL, NULL, 126, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.5R17.5', 'S/.'),
(3648, 291, 1, 6, 55, 'LLANTA HIFLY 7.50R16 POS HH305 14PR', NULL, NULL, 'LHO953', NULL, NULL, 119.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'S/.'),
(3649, 291, 1, 6, 55, 'LLANTA HIFLY 215/75R16 ST SUPER2000 116/114R', NULL, NULL, 'LHO952', NULL, NULL, 62.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R16', 'S/.'),
(3650, 434, 1, 6, 55, 'LLANTA SONAR 285/65R17 AT CONQUEROR', NULL, NULL, 'LHO951', NULL, NULL, 134.66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '285/65R17', 'S/.'),
(3651, 434, 1, 6, 55, 'LLANTA SONAR 275/65R17 AT CONQUEROR', NULL, NULL, 'LHO950', NULL, NULL, 136.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/65R17', 'S/.'),
(3652, 434, 1, 6, 55, 'LLANTA SONAR 205/50R16 SX2-L 87V', NULL, NULL, 'LHO949', NULL, NULL, 66.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(3653, 434, 1, 6, 55, 'LLANTA SONAR 205/55R15 SX1-608 88V', NULL, NULL, 'LHO948', NULL, NULL, 61.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R15', 'S/.'),
(3654, 434, 1, 6, 55, 'LLANTA SONAR 215/55R16 SX1-EVO 93V', NULL, NULL, 'LHO947', NULL, NULL, 73.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/55R16', 'S/.'),
(3655, 434, 1, 6, 55, 'LLANTA SONAR 195/55R15 SX1-EVO 85V', NULL, NULL, 'LHO946', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/55R15', 'S/.'),
(3656, 434, 1, 6, 55, 'LLANTA SONAR 195/50R15 SX1-EVO 86V', NULL, NULL, 'LHO945', NULL, NULL, 51.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/50R15', 'S/.'),
(3657, 434, 1, 6, 55, 'LLANTA SONAR 185/60R13 SX1-EVO 80H', NULL, NULL, 'LHO944', NULL, NULL, 51.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R13', 'S/.'),
(3658, 434, 1, 6, 55, 'LLANTA SONAR 205/60R13 SX1-EVO 86H', NULL, NULL, 'LHO943', NULL, NULL, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R13', 'S/.'),
(3659, 349, 1, 6, 55, 'LLANTA VIKRANT 7.00-15 POS STAR LUG 12PR', NULL, NULL, 'LHO942', NULL, NULL, 129, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-15', 'S/.'),
(3660, 331, 1, 6, 55, 'LLANTA DEESTONE 235/75R15 MT PAYAK', NULL, NULL, 'LHO941', NULL, NULL, 92.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'S/.'),
(3661, 434, 1, 6, 55, 'LLANTA SONAR 225/40R18 SX1-EVO 92H', NULL, NULL, 'LHO940', NULL, NULL, 98.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/40R18', 'S/.'),
(3662, 434, 1, 6, 55, 'LLANTA SONAR 205/60R14 SX1-EVO 92H', NULL, NULL, 'LHO939', NULL, NULL, 61.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'S/.'),
(3663, 408, 1, 6, 55, 'LLANTA BKT 18-19.5 MP567 L3 18PR', NULL, NULL, 'LHO938', NULL, NULL, 574.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '18-19.5', 'S/.'),
(3664, 372, 1, 6, 55, 'LLANTA BEARWAY 205/50R17 YS618 91W', NULL, NULL, 'LHO937', NULL, NULL, 58.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R17', 'S/.'),
(3665, 338, 1, 6, 55, 'LLANTA PIRELLI 225/75R16 AT SCORPION', NULL, NULL, 'LHO936', NULL, NULL, 113.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'S/.'),
(3666, 336, 1, 6, 55, 'LLANTA MAXTREK 165/65R13 MAXIMUS 77T', NULL, NULL, 'LHO935', NULL, NULL, 28.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'S/.'),
(3667, 336, 1, 6, 55, 'LLANTA MAXTREK 175/70R13 SU830 82T', NULL, NULL, 'LHO934', NULL, NULL, 31.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(3668, 354, 1, 6, 55, 'LLANTA KAPSEN 12.00R20 TRA HS801Q 156/153K 20PR', NULL, NULL, 'LHO933', NULL, NULL, 273.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(3669, 387, 1, 6, 55, 'LLANTA GOALSTAR 215/65R16 CATCHGRE 98H', NULL, NULL, 'LHO932', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/65R16', 'S/.'),
(3670, 433, 1, 6, 55, 'LLANTA TAITONG 8.25R16 MIX HS268 125/124K 16PR', NULL, NULL, 'LHO930', NULL, NULL, 128.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'S/.'),
(3671, 433, 1, 6, 55, 'LLANTA TAITONG 9.5R17.5 POS HS928 143/141M 18PR TL', NULL, NULL, 'LHO929', NULL, NULL, 130.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.5R17.5', 'S/.'),
(3672, 433, 1, 6, 55, 'LLANTA TAITONG 9.5R17.5 DEL HS206 18PR 143/141M TL', NULL, NULL, 'LHO928', NULL, NULL, 124.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.5R17.5', 'S/.'),
(3673, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 205R16 HT DUELER', NULL, NULL, 'LHO927', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205R16', 'S/.'),
(3674, 365, 1, 6, 53, 'BATERIA CAPSA 9API PREMIUM', NULL, NULL, 'BAT181', NULL, NULL, 225.31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9API', 'S/.'),
(3675, 296, 1, 6, 52, 'ARO PDW 351957 13X6.0 ET70 4X100.0 MB', NULL, NULL, 'ARO486', NULL, NULL, 47.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '351957', 'S/.'),
(3676, 296, 1, 6, 52, 'ARO PDW 5527011 15X6.0 ET32 4X100.0 MB', NULL, NULL, 'ARO485', NULL, NULL, 65.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5527011', 'S/.'),
(3677, 296, 1, 6, 52, 'ARO PDW 5501954 15X6.5 ET35 5X114.3 MB', NULL, NULL, 'ARO484', NULL, NULL, 64.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5501954', 'S/.'),
(3678, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-1890 14X5.5 ET40 4X100+114.3 BM', NULL, NULL, 'ARO483', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1890', 'S/.'),
(3679, 296, 1, 6, 52, 'ARO PDW 451042 14X6.0 ET35 8X100+114.3 MB', NULL, NULL, 'ARO482', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '451042', 'S/.'),
(3680, 296, 1, 6, 52, 'ARO PDW 1022L1 14X6.0 ET30 4X100.0 MB', NULL, NULL, 'ARO481', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1022L1', 'S/.'),
(3681, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-5102 13X5.5 ET35 4X100+114.3 C-VW', NULL, NULL, 'ARO480', NULL, NULL, 68.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5102', 'S/.'),
(3682, 296, 1, 6, 52, 'ARO PDW 351033 13X5.5 ET35 4X100.0 MB', NULL, NULL, 'ARO479', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '351033', 'S/.'),
(3683, 296, 1, 6, 52, 'ARO PDW 351022 13X5.5 ET35 8X100+114.3 MB', NULL, NULL, 'ARO478', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '351022', 'S/.'),
(3684, 296, 1, 6, 52, 'ARO PDW 358826 13X5.5 ET0 4X114.3 MB', NULL, NULL, 'ARO477', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '358826', 'S/.'),
(3685, 296, 1, 6, 52, 'ARO PDW 388955 13X5.5 ET25 8X100+114.3 MB', NULL, NULL, 'ARO476', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '388955', 'S/.'),
(3686, 296, 1, 6, 52, 'ARO PDW 363342 13X5.5 ET18 4X100.0 MB', NULL, NULL, 'ARO475', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '363342', 'S/.'),
(3687, 296, 1, 6, 52, 'ARO PDW 352952 13X5.5 ET18 4X100.0 MB', NULL, NULL, 'ARO474', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '352952', 'S/.'),
(3688, 296, 1, 6, 52, 'ARO PDW 358451 13X5.5 ET15 4X100.0 MB', NULL, NULL, 'ARO472', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '358451', 'S/.'),
(3689, 296, 1, 6, 52, 'ARO PDW 372027 13X5.5 ET25 4X100.0 MB', NULL, NULL, 'ARO471', NULL, NULL, 45.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '372027', 'S/.'),
(3690, 296, 1, 6, 52, 'ARO PDW 359943 13X5.5 ET25 4X100.0 MB', NULL, NULL, 'ARO470', NULL, NULL, 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '359943', 'S/.'),
(3691, 397, 1, 6, 52, 'ARO XTREME 9145 17X9.0 ET0 6X139.7 B-M', NULL, NULL, 'ARO469', NULL, NULL, 114.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9145', 'S/.'),
(3692, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-7561 17X9.0 ET0 6X139.7 EB-MB', NULL, NULL, 'ARO468', NULL, NULL, 96.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7561', 'S/.'),
(3693, 296, 1, 6, 52, 'ARO PDW 6608903 16X7.5 ET0 6X139.7 MB', NULL, NULL, 'ARO467', NULL, NULL, 87.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6608903', 'S/.'),
(3694, 296, 1, 6, 52, 'ARO PDW 6602031 16X7.5 ET10 6X139.7 MI-UB', NULL, NULL, 'ARO466', NULL, NULL, 88.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6602031', 'S/.'),
(3695, 296, 1, 6, 52, 'ARO PDW 10111580 15X8.0 ET0 6X139.7 MB', NULL, NULL, 'ARO465', NULL, NULL, 75.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10111580', 'S/.'),
(3696, 296, 1, 6, 52, 'ARO PDW 566636 15X7.0 ET0 6X139.7 MB', NULL, NULL, 'ARO464', NULL, NULL, 71.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '566636', 'S/.'),
(3697, 296, 1, 6, 52, 'ARO PDW 5600970 15X8.0 ET0 5X139.7 UB', NULL, NULL, 'ARO463', NULL, NULL, 74.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5600970', 'S/.'),
(3698, 296, 1, 6, 52, 'ARO PDW 5590AM 15X6.5 ET35 8X100+114.3 MO-MIU', NULL, NULL, 'ARO462', NULL, NULL, 64.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5590AM', 'S/.'),
(3699, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-5068 15X7.0 ET30 4X100.0 BM', NULL, NULL, 'ARO461', NULL, NULL, 62.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5068', 'S/.'),
(3700, 411, 1, 6, 55, 'LLANTA BFGOODRICH 165/65R14 GGRIP', NULL, NULL, 'LHO926', NULL, NULL, 53.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R14', 'S/.'),
(3701, 415, 1, 6, 55, 'LLANTA LUCKYLAND 175/70R13 LCG01', NULL, NULL, 'LHO925', NULL, NULL, 28.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(3702, 416, 1, 6, 55, 'LLANTA GREMAX 205/60R15 CAPTURAR', NULL, NULL, 'LHO924', NULL, NULL, 38.38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'S/.'),
(3703, 416, 1, 6, 55, 'LLANTA GREMAX 175/70R14 CAPTURAR', NULL, NULL, 'LHO923', NULL, NULL, 31.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'S/.'),
(3704, 415, 1, 6, 55, 'LLANTA LUCKYLAND 185/70R13 LCG01', NULL, NULL, 'LHO922', NULL, NULL, 31.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(3705, 312, 1, 6, 55, 'LLANTA MIRAGE 9.00R20 MIX MG702 18PR', NULL, NULL, 'LHO921', NULL, NULL, 178.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.00R20', 'S/.'),
(3706, 326, 1, 6, 55, 'LLANTA DURUN 215/45R17 SPORT ONE 91W', NULL, NULL, 'LHO919', NULL, NULL, 50.11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'S/.'),
(3707, 405, 1, 6, 55, 'LLANTA GINELL195/55R15 CF500', NULL, NULL, 'LHO918', NULL, NULL, 40.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/55R15', 'S/.'),
(3708, 432, 1, 6, 55, 'LLANTA BORISTAR 215/75R15 MT BSMTX7', NULL, NULL, 'LHO917', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(3709, 431, 1, 6, 55, 'LLANTA MULTIRAC 225/75R16 MT MULTERRAIN', NULL, NULL, 'LHO916', NULL, NULL, 93.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'S/.'),
(3710, 431, 1, 6, 55, 'LLANTA MULTIRAC 245/75R16 MT MULTIRRAIN', NULL, NULL, 'LHO915', NULL, NULL, 98.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(3711, 430, 1, 6, 55, 'LLANTA KETER 195R15 KT656 8PR', NULL, NULL, 'LHO914', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'US$'),
(3712, 274, 1, 6, 55, 'LLANTA CATCHFORSE 245/75R16 MT WINDFORSE', NULL, NULL, 'LHO913', NULL, NULL, 110.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(3713, 280, 1, 6, 53, 'BATERIA HANKOOK MF55B24LS 45 AH', NULL, NULL, 'BAT180', NULL, NULL, 202.49, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55B24LS', 'S/.'),
(3714, 280, 1, 6, 53, 'BATERIA HANKOOK MF40B19L 11 PLACAS', NULL, NULL, 'BAT179', NULL, NULL, 164.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '40B19L', 'S/.'),
(3715, 409, 1, 6, 52, 'ARO ALMARO WHEELS VK-321 14X7.0 5X114.3 FMC', NULL, NULL, 'ARO460', NULL, NULL, 67.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '321', 'S/.'),
(3716, 409, 1, 6, 52, 'ARO ALMARO WHEELS VK-558 17X7.5 5X114.3 FC-CR', NULL, NULL, 'ARO459', NULL, NULL, 94.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '558', 'S/.'),
(3717, 429, 1, 6, 53, 'BATERIA NORT STAR 33 PLACAS GEL', NULL, NULL, 'BAT178', NULL, NULL, 649.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '33 PLACAS', 'S/.'),
(3718, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1005 8X3.5 ET0 4X106.0 R2-B', NULL, NULL, 'ARO458', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1005', 'S/.'),
(3719, 338, 1, 6, 55, 'LLANTA PIRELLI 265/65R17 HT SCORPION', NULL, NULL, 'LHO912', NULL, NULL, 203.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'S/.'),
(3720, 338, 1, 6, 55, 'LLANTA PIRELLI 195/60R15 88H P1CINT 88H', NULL, NULL, 'LHO911', NULL, NULL, 75.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'S/.'),
(3721, 338, 1, 6, 55, 'LLANTA PIRELLI 225/40R18 PZERO 82Y', NULL, NULL, 'LHO910', NULL, NULL, 252.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/40R18', 'S/.'),
(3722, 338, 1, 6, 55, 'LLANTA PIRELLI 195/55R15 P1CNT 85V', NULL, NULL, 'LHO909', NULL, NULL, 78.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/55R15', 'S/.'),
(3723, 338, 1, 6, 55, 'LLANTA PIRELLI 165/65R13 P1CINT 77T', NULL, NULL, 'LHO908', NULL, NULL, 49.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'S/.'),
(3724, 313, 1, 6, 55, 'LLANTA GENERAL 255/60R18 HT ALTIMAX', NULL, NULL, 'LHO907', NULL, NULL, 142.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/60R18', 'US$'),
(3725, 335, 1, 6, 55, 'LLANTA CONTINENTAL 185/65R14 86H', NULL, NULL, 'LHO906', NULL, NULL, 47.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'S/.'),
(3726, 291, 1, 6, 55, 'LLANTA HIFLY 225/65R16 ST SUPER2000', NULL, NULL, 'LHO904', NULL, NULL, 62.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R16', 'S/.'),
(3727, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/70R16 AT DUELER D693', NULL, NULL, 'LHO903', NULL, NULL, 174, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(3728, 296, 1, 6, 52, 'ARO PDW 626750 16X7 4H114.3', NULL, NULL, 'ARO457', NULL, NULL, 77.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6267', 'US$'),
(3729, 397, 1, 6, 52, 'ARO XTREME 3753 15X7.0 8H100/114.3 BLP', NULL, NULL, 'ARO456', NULL, NULL, 62.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3753', 'US$'),
(3730, 296, 1, 6, 52, 'ARO PDW 7001L 14X6.5 4X100 MB', NULL, NULL, 'ARO455', NULL, NULL, 54.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7001', 'US$'),
(3731, 296, 1, 6, 52, 'ARO PDW 4903015 14X6 4H100 MB', NULL, NULL, 'ARO454', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '49030', 'US$'),
(3732, 296, 1, 6, 52, 'ARO PDW 4511648 14X6 4X100 B', NULL, NULL, 'ARO453', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4511', 'US$'),
(3733, 397, 1, 6, 52, 'ARO XTREME 9077 17X9.0 6H139.7 B-LP', NULL, NULL, 'ARO452', NULL, NULL, 109.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9077', 'US$'),
(3734, 296, 1, 6, 52, 'ARO PDW 6607612 16X7.5 6H139.7', NULL, NULL, 'ARO451', NULL, NULL, 87.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6607', 'US$'),
(3735, 296, 1, 6, 52, 'ARO PDW 6602031 16X7.5 6H139.7 MI/B', NULL, NULL, 'ARO450', NULL, NULL, 88.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '66020', 'US$'),
(3736, 296, 1, 6, 52, 'ARO PDW 6602228 16X7.5 6H139.7 M2/B', NULL, NULL, 'ARO449', NULL, NULL, 88.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '66022', 'US$'),
(3737, 291, 1, 6, 55, 'LLANTA HIFLY 265/65 R17 AT VIGOROUS', NULL, NULL, 'LHO902', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'S/.'),
(3738, 378, 1, 6, 53, 'BATERIA MOTO MGM YB2.5L 6V', NULL, NULL, 'BAT177', NULL, NULL, 26.83, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YB2.5L', 'S/.'),
(3739, 378, 1, 6, 53, 'BATERIA MOTO MGM 12N9-4B 12V', NULL, NULL, 'BAT176', NULL, NULL, 65.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N9-4B', 'S/.'),
(3740, 376, 1, 6, 55, 'LLANTA DURO 10-400 203 HF CHALLY', NULL, NULL, 'LHO901', NULL, NULL, 57.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10-400 203', 'S/.'),
(3741, 377, 1, 6, 55, 'LLANTA MICHELIN 110/70R17 54H PILOT STREET RADIAL TL/TT', NULL, NULL, 'LHO900', NULL, NULL, 228, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '110/70R17', 'S/.'),
(3742, 322, 1, 6, 55, 'LLANTA WESTLAKE185/70R13 H550-A', NULL, NULL, 'LHO899', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(3743, 280, 1, 6, 55, 'LLANTA HANKOOK 6.50R14 AU01', NULL, NULL, 'LH0958', NULL, NULL, 92.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50R14', 'US$'),
(3744, 317, 1, 6, 55, 'LLANTA GOOD YEAR 245/75R16 AT WRANGLER', NULL, NULL, 'LH0957', NULL, NULL, 144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(3745, 276, 1, 6, 55, 'LLANTA LINGLONG 7.00R16 POS D955', NULL, NULL, 'LH0956', NULL, NULL, 132, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00R16 POS', 'US$'),
(3746, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 185/65R17', NULL, NULL, 'LH0955', NULL, NULL, 132, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R17', 'US$'),
(3747, 273, 1, 6, 55, 'LLANTA FALKEN 195/65R15 ZIEX ZZ912', NULL, NULL, 'LH0954', NULL, NULL, 54.38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'US$'),
(3748, 330, 1, 6, 55, 'LLANTA KUMHO 205/60R14 SOLUS KH17', NULL, NULL, 'LH0953', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'US$'),
(3749, 273, 1, 6, 55, 'LLANTA FALKEN 195R14C FALKEN LINAM', NULL, NULL, 'LH0952', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'US$'),
(3750, 400, 1, 6, 55, 'LLANTA MARSHAL 215/45R17 MATRAC-FX', NULL, NULL, 'LH0951', NULL, NULL, 75.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'US$'),
(3751, 330, 1, 6, 55, 'LLANTA KUMHO 205/70R15 KO10 8PR', NULL, NULL, 'LH0950', NULL, NULL, 93.97, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'US$'),
(3752, 317, 1, 6, 55, 'LLANTA GOOD YEAR 215/80R16 WRANGLER AT', NULL, NULL, 'LH0949', NULL, NULL, 205.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/80R16', 'US$'),
(3753, 276, 1, 6, 55, 'LLANTA LINGLONG 6.50-15 POS LR625 8PR', NULL, NULL, 'LH0948', NULL, NULL, 104.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-15', 'US$'),
(3754, 428, 1, 6, 55, 'LLANTA LANVIGATOR 195R15 MILEMAX 106/104R 8PR', NULL, NULL, 'LH0947', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'US$'),
(3755, 427, 1, 6, 55, 'LLANTA SAILUN 205/55R16 ATREZZO 92H', NULL, NULL, 'LH0946', NULL, NULL, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'US$'),
(3756, 427, 1, 6, 55, 'LLANTA SAILUN 205/50R15 SH402 88H', NULL, NULL, 'LH0945', NULL, NULL, 38.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'US$'),
(3757, 387, 1, 6, 55, 'LLANTA GOALSTAR 155/70R12 GP100 77T TL', NULL, NULL, 'LH0944', NULL, NULL, 25.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R12', 'US$'),
(3758, 387, 1, 6, 55, 'LLANTA GOALSTAR 275/35ZR20 BLAZER', NULL, NULL, 'LH0943', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/35R20', 'US$'),
(3759, 386, 1, 6, 55, 'LLANTA SUNWIDE 165/60R14 RS-ZERO', NULL, NULL, 'LH0942', NULL, NULL, 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/60R14', 'US$'),
(3760, 386, 1, 6, 55, 'LLANTA SUNWIDE 195/70R14 ROLIT6 91H', NULL, NULL, 'LH0941', NULL, NULL, 35.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R14', 'US$'),
(3761, 386, 1, 6, 55, 'LLANTA SUNWIDE 265/70R17 AT DURELOVE', NULL, NULL, 'LH0939', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'US$'),
(3762, 386, 1, 6, 55, 'LLANTA SUNWIDE 215/75R15 HT DURELOVE', NULL, NULL, 'LH0938', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'US$'),
(3763, 386, 1, 6, 55, 'LLANTA SUNWIDE 235/65R17 AT DURELOVE', NULL, NULL, 'LH0937', NULL, NULL, 84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(3764, 419, 1, 6, 55, 'LLANTA APOLLO 7.50-16 DEL AMARDELEXU 16PR', NULL, NULL, 'LH0936', NULL, NULL, 152.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(3765, 419, 1, 6, 55, 'LLANTA APOLLO 7.50-16 POS MILESTAR 16PR', NULL, NULL, 'LH0935', NULL, NULL, 154.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(3766, 280, 1, 6, 55, 'LLANTA HANKOOK 245/70R16 AT DYNAPRO', NULL, NULL, 'LH0934', NULL, NULL, 133.63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R16', 'US$'),
(3767, 280, 1, 6, 55, 'LLANTA HANKOOK 215/70R16 ST RA08', NULL, NULL, 'LH0931', NULL, NULL, 120.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'US$'),
(3768, 280, 1, 6, 55, 'LLANTA HANKOOK 165/65R14T K715 77T', '', '', 'LH0930', '', '', 47.77, 0, 0, 0, b'0', NULL, NULL, '2018-02-20 11:13:22', 'Jeam', NULL, b'1', 0, b'0', 0, NULL, '165/65R14', 'US$'),
(3769, 365, 1, 6, 53, 'BATERIA CAPSA 10LBI PREMIUM', NULL, NULL, 'BAT175', NULL, NULL, 175.61, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10LBI', 'S/.'),
(3770, 357, 1, 6, 55, 'LLANTA SUNFULL 195R15 SF05 106/104R 8PR', NULL, NULL, 'LH0926', NULL, NULL, 52.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'US$'),
(3771, 426, 1, 6, 55, 'LLANTA CACHLAND 155/65R13 CH-268 73T', NULL, NULL, 'LH0924', NULL, NULL, 23.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/65R13', 'US$'),
(3772, 366, 1, 6, 55, 'LLANTA HABILEAD 155/70R13 H202 75T TL', NULL, NULL, 'LH0923', NULL, NULL, 30.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R13', 'US$'),
(3773, 366, 1, 6, 55, 'LLANTA HABILEAD 195/60R14 86H H202 COMFORTMAX AS', NULL, NULL, 'LH0922', NULL, NULL, 34.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'US$'),
(3774, 366, 1, 6, 55, 'LLANTA HABILEAD 195/70R15 104/102R R501 DURABLEMAX', NULL, NULL, 'LH0921', NULL, NULL, 49.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R15', 'US$'),
(3775, 425, 1, 6, 55, 'LLANTA CEAT 14.00-24 16PR G2 GRADER XL', NULL, NULL, 'LH0920', NULL, NULL, 443.29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '14.00-24', 'US$'),
(3776, 326, 1, 6, 55, 'LLANTA DURUN 275/60R20 MT WHITE K334', NULL, NULL, 'LH0919', NULL, NULL, 191.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/60R20', 'US$'),
(3777, 326, 1, 6, 55, 'LLANTA DURUN 285/50R20 AT WHITE K325', NULL, NULL, 'LH0918', NULL, NULL, 150.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '285/50R20', 'US$'),
(3778, 357, 1, 6, 55, 'LLANTA 225/65 R17 102H HT782 SUNFULL', NULL, NULL, 'LH0917', NULL, NULL, 61.43, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'US$'),
(3779, 357, 1, 6, 55, 'LLANTA 235/65 R16C 8PR 115/113T SF05 SUNFULL', NULL, NULL, 'LH0916', NULL, NULL, 67.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R16', 'US$'),
(3780, 357, 1, 6, 55, 'LLANTA SUNFULL 225/65R16 ST SF05', NULL, NULL, 'LH0915', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R16', 'US$'),
(3781, 291, 1, 6, 55, 'LLANTA 185 R14C 8PR 102/100R SUPER2000 HIFLY', NULL, NULL, 'LH0914', NULL, NULL, 41.69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185R14', 'US$'),
(3782, 357, 1, 6, 55, 'LLANTA SUNFULL 165/65R13 SF688 77T', NULL, NULL, 'LH0913', NULL, NULL, 27.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'US$'),
(3783, 292, 1, 6, 55, 'LLANTA 16.9X28 14PR RT805M (INDIA) ORNET', NULL, NULL, 'LH912', NULL, NULL, 501.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '16.9-28', 'US$'),
(3784, 425, 1, 6, 55, 'LLANTA CEAT 16.9-28 12PR FARMAX C/CAMARA', NULL, NULL, 'LH0911', NULL, NULL, 451.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '16.9-28', 'US$'),
(3785, 284, 1, 6, 55, 'LLANTA ARMOUR 16.9-28 10PR R1 SET GORIR', NULL, NULL, 'LH0910', NULL, NULL, 440, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '16.9-28', 'US$'),
(3786, 424, 1, 6, 55, 'LLANTA MITAS 110/70-17 MRACER 54S', NULL, NULL, 'LH0909', NULL, NULL, 171.85, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '110/70-170', 'S/.'),
(3787, 376, 1, 6, 55, 'LLANTA DURO 120/90-17 TRAIL', NULL, NULL, 'LH0905', NULL, NULL, 76.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '120/90-17', 'S/.'),
(3788, 424, 1, 6, 55, 'LLANTA ENDURO RCAR 120/90-17 MITAS', NULL, NULL, 'LH0904', NULL, NULL, 220.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '120/90-17', 'S/.'),
(3789, 424, 1, 6, 55, 'LLANTA ENDURO RCAR 150/70-17 MITAS', NULL, NULL, 'LH0903', NULL, NULL, 456.19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '150/70-17', 'S/.'),
(3790, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 24X9-11 AT', NULL, NULL, 'LH0902', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '24X9-11', 'S/.'),
(3791, 423, 1, 6, 55, 'LLANTA H-TRAK 23X8-11 AT', NULL, NULL, 'LH0901', NULL, NULL, 156, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '23X8-11', 'S/.'),
(3792, 388, 1, 6, 53, 'BATERIA BOSCH N100 19 PLACAS SPECIAL', NULL, NULL, 'BAT174', NULL, NULL, 147.03, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'N100', 'S/.'),
(3793, 388, 1, 6, 53, 'BATERIA BOSCH 80D23L 15 PLACAS CUADRADA', NULL, NULL, 'BAT173', NULL, NULL, 145.38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '80D23L', 'S/.'),
(3794, 422, 1, 6, 55, 'LLANTA TEKPRO 185/70R14 TEK01 88T', NULL, NULL, 'LHO898', NULL, NULL, 33.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(3795, 312, 1, 6, 55, 'LLANTA MIRAGE 225/40R18 MR182 91V', NULL, NULL, 'LHO897', NULL, NULL, 47.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/40R18', 'S/.'),
(3796, 312, 1, 6, 55, 'LLANTA MIRAGE 175/65R14 MR162 88T', NULL, NULL, 'LHO896', NULL, NULL, 30.82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'S/.'),
(3797, 312, 1, 6, 55, 'LLANTA MIRAGE 235/65R16 MR200 8PR', NULL, NULL, 'LHO895', NULL, NULL, 75.85, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R16', 'S/.'),
(3798, 312, 1, 6, 55, 'LLANTA MIRAGE 155R13 MR200 8PR', NULL, NULL, 'LHO894', NULL, NULL, 34.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R13', 'S/.'),
(3799, 416, 1, 6, 55, 'GREMAX 165/60R14 CAPTURAR CF18', NULL, NULL, 'LHO893', NULL, NULL, 28.81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/60R14', 'S/.'),
(3800, 312, 1, 6, 55, 'LLANTA MIRAGE 11R22.5 POS MG312 18PR', NULL, NULL, 'LHO892', NULL, NULL, 240.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'S/.'),
(3801, 389, 1, 6, 53, 'BATERIA MOTO YUASA YB2.5L-C 12V', NULL, NULL, 'BAT172', NULL, NULL, 51.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YB2.5L-C', 'S/.'),
(3802, 389, 1, 6, 53, 'BATERIA MOTO YUASA YTX7L-BS 12V', NULL, NULL, 'BAT171', NULL, NULL, 157.11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX7L-BS', 'S/.'),
(3803, 389, 1, 6, 53, 'BATERIA MOTO YUASA YTX4L-BS 12V', NULL, NULL, 'BAT170', NULL, NULL, 87.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX4L-BS', 'S/.'),
(3804, 390, 1, 6, 53, 'BATERIA MOTO KOYO YTX9-BS 12V', NULL, NULL, 'BAT169', NULL, NULL, 156.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX9-BS', 'S/.'),
(3805, 390, 1, 6, 53, 'BATERIA MOTO KOYO YB6.5L-B 12V', NULL, NULL, 'BAT168', NULL, NULL, 99.07, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YB6.5L-B', 'S/.'),
(3806, 390, 1, 6, 53, 'BATERIA MOTO KOYO YB6L-B 12V', NULL, NULL, 'BAT167', NULL, NULL, 93.07, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YB6L-B', 'S/.'),
(3807, 389, 1, 6, 53, 'BATERIA MOTO YUASA YB6L-B 12V', NULL, NULL, 'BAT166', NULL, NULL, 103.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YB6L-B', 'S/.'),
(3808, 389, 1, 6, 53, 'BATERIA MOTO YUASA 12N7B-3A 12V', NULL, NULL, 'BAT165', NULL, NULL, 116.02, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N7B-3A', 'S/.'),
(3809, 317, 1, 6, 55, 'LLANTA GOOD YEAR 10.00-20 DEL CAMINERA 16PR', NULL, NULL, 'LH0891', NULL, NULL, 376.93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10.00-20', 'US$'),
(3810, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-191507 14X6.5 25 73.1 4X100 M-B', NULL, NULL, 'ARO275', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1915', 'S/.'),
(3811, 353, 1, 6, 55, 'LLANTA AEOLUS 235/65R17 HT CROSS ACE 108L', NULL, NULL, 'LH0890', NULL, NULL, 81.19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(3812, 421, 1, 6, 55, 'LLANTA AEOLUS 225/65R17 HT CROSS ACE 102H', NULL, NULL, 'LH0889', NULL, NULL, 93.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'US$'),
(3813, 353, 1, 6, 55, 'LLANTA AEOLUS 225/70R16 HT CROSS ACE 103S', NULL, NULL, 'LH0888', NULL, NULL, 79.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'US$'),
(3814, 353, 1, 6, 55, 'LLANTA AEOLUS 215/70R16 HT CROSS ACE 100S', NULL, NULL, 'LH0887', NULL, NULL, 67.25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'US$'),
(3815, 353, 1, 6, 55, 'LLANTA AEOLUS 175/65R14 82H PRECISION ACE', NULL, NULL, 'LH0886', NULL, NULL, 41.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'US$'),
(3816, 353, 1, 6, 55, 'LLANTA AEOLUS 195R15 TRANS ACE ALO1 106/104', NULL, NULL, 'LH0885', NULL, NULL, 65.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'US$'),
(3817, 353, 1, 6, 55, 'LLANTA AEOLUS 195R14 TRANS ACE ALO1 106/104', NULL, NULL, 'LH0884', NULL, NULL, 64.66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'US$'),
(3818, 353, 1, 6, 55, 'LLANTA AEOLUS 31X10.50R15 AT CROSS ACE', NULL, NULL, 'LH0883', NULL, NULL, 114.07, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.50R15', 'US$'),
(3819, 420, 1, 6, 55, 'LLANTA LUHE 7.50-15 DEL. 14PR RIB', NULL, NULL, 'LH0882', NULL, NULL, 98.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-15', 'US$'),
(3820, 420, 1, 6, 55, 'LLANTA LUHE 7.50-15 POST. 14PR LUG', NULL, NULL, 'LH0881', NULL, NULL, 94.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-15', 'US$'),
(3821, 419, 1, 6, 55, 'LLANTA APOLLO 6.50-14 POS 10PR HERCULES', NULL, NULL, 'LH0880', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(3822, 419, 1, 6, 55, 'LLANTA APOLLO 6.50-14 DEL 8PR CARGO RIB', NULL, NULL, 'LH0879', NULL, NULL, 84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(3823, 296, 1, 6, 52, 'ARO PDW 5849147 15X6.5 ET35 8H100/114.3', NULL, NULL, 'ARO448', NULL, NULL, 69.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5849', 'US$'),
(3824, 296, 1, 6, 52, 'ARO PDW 4800158 14X6.5 ET20 4H/100MM', NULL, NULL, 'ARO447', NULL, NULL, 61.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4800', 'US$'),
(3825, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-506602 15X7 ET30 4X100 BC-H', NULL, NULL, 'ARO445', NULL, NULL, 70.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5066', 'US$'),
(3826, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-1630 15X6.5 ET35 4X100/114.3', NULL, NULL, 'ARO444', NULL, NULL, 68.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1630', 'US$'),
(3827, 296, 1, 6, 52, 'ARO PDW 563869 15X6.5 ET20 8H100/114.3', NULL, NULL, 'ARO443', NULL, NULL, 70.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5638', 'US$'),
(3828, 296, 1, 6, 52, 'ARO PDW 7002R146505 14X6.5 ET28 4X100', NULL, NULL, 'ARO442', NULL, NULL, 60.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7002', 'US$'),
(3829, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-2731 14X6.0 ET40 4X100 BM', NULL, NULL, 'ARO441', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2731', 'US$'),
(3830, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-2209 14X6.0 ET35 4X100/114.3', NULL, NULL, 'ARO440', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2209', 'US$'),
(3831, 296, 1, 6, 52, 'ARO PDW W-4539191 14X6 ET35 4X100+114.3 BM', NULL, NULL, 'ARO439', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4539', 'US$'),
(3832, 397, 1, 6, 52, 'ARO XTREME 3235BP 14X6.0 ET354H100', NULL, NULL, 'ARO438', NULL, NULL, 61.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3235', 'US$'),
(3833, 313, 1, 6, 55, 'LLANTA GENERAL 165/60R14 ALTIMAX HP 75H', NULL, NULL, 'LH0878', NULL, NULL, 36.71, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/60R14', 'US$'),
(3834, 335, 1, 6, 55, 'LLANTA CONTINENTAL 205/55R16 POWERCONTAC 91H', NULL, NULL, 'LH0877', NULL, NULL, 58.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'US$'),
(3835, 418, 1, 6, 53, 'BATERIA SUPER15M99 N2 OM9', NULL, NULL, 'BAT163', NULL, NULL, 222, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '15M99', 'S/.'),
(3836, 418, 1, 6, 53, 'BATERIA SUPER 11M73 N2 OM9', NULL, NULL, 'BAT162', NULL, NULL, 186, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11M73', 'S/.'),
(3837, 336, 1, 6, 55, 'LLANTA MAXTREK 175/65R14 SU830 82H', NULL, NULL, 'LH0876', NULL, NULL, 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'US$'),
(3838, 336, 1, 6, 55, 'LLANTA MAXTREK 195/60R15 MAXIMUS M1 88H', NULL, NULL, 'LH0875', NULL, NULL, 39.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'US$'),
(3839, 350, 1, 6, 55, 'LLANTA ANTARES 245/70R16 AT SMTA7', NULL, NULL, 'LH0874', NULL, NULL, 86.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R16', 'US$'),
(3840, 336, 1, 6, 55, 'LLANTA MAXTREK 265/60R18 AT SU800 110H', NULL, NULL, 'LH0872', NULL, NULL, 92.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/60R18', 'US$'),
(3841, 335, 1, 6, 55, 'LLANTA CONTINENTAL 265/70R16 AT CROSSCONTACT', NULL, NULL, 'LH0871', NULL, NULL, 140.21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(3842, 335, 1, 6, 55, 'LLANTA CONTINENTAL 265/65R17 HT CROSSCONTACT', NULL, NULL, 'LH0870', NULL, NULL, 163.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'US$'),
(3843, 335, 1, 6, 55, 'LLANTA CONTINENTAL 195/65R15 91H POWERCONTACT', NULL, NULL, 'LH0869', NULL, NULL, 54.38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'US$'),
(3844, 321, 1, 6, 55, 'LLANTA XCEED 11R22.5 DEL XD106 146/148R 16PR', NULL, NULL, 'LH0868', NULL, NULL, 188.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'US$'),
(3845, 341, 1, 6, 55, 'LLANTA ROADSHINE 11R22.5 POS. RS604 16PR', NULL, NULL, 'LHO866', NULL, NULL, 197.54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'US$'),
(3846, 359, 1, 6, 55, 'LLANTA SPORTRAK 12R22.5 DEL SP398 18PR', NULL, NULL, 'LHO865', NULL, NULL, 220.93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'US$'),
(3847, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 185/70R13 RE740 86T', NULL, NULL, 'LHO864', NULL, NULL, 60.78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(3848, 417, 1, 6, 52, 'ARO ORIGINAL MAZDA 17X8', NULL, NULL, 'ARO437', NULL, NULL, 499.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17X8', 'S/.'),
(3849, 393, 1, 6, 53, 'BATERIA ETNA FH-1215 DE 15 PLACAS', NULL, NULL, 'BAT160', NULL, NULL, 336, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'FH-1215', 'US$'),
(3850, 393, 1, 6, 53, 'BATERIA ETNA S-1223 DE 23 PLACAS', NULL, NULL, 'BAT159', NULL, NULL, 516, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'S-1223', 'S/.'),
(3851, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 215/50R17 TURANZA 91V', NULL, NULL, 'LHO863', NULL, NULL, 118.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/50R17', 'US$'),
(3852, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 205/70R15 AT DUELER D693', NULL, NULL, 'LHO862', NULL, NULL, 105.37, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'US$'),
(3853, 318, 1, 6, 55, 'LLANTA FIRESTONE 275/45R20 HT DESTINATION', NULL, NULL, 'LHO861', NULL, NULL, 162.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/45R20', 'US$'),
(3854, 372, 1, 6, 55, 'LLANTA BAERWAY 155R12 BW168 8PR 88/86Q', NULL, NULL, 'LHO858', NULL, NULL, 27.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'US$'),
(3855, 364, 1, 6, 53, 'BATERIA ENERJET 17T114P N2', NULL, NULL, 'BAT158', NULL, NULL, 422.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17T114P', 'S/.'),
(3856, 364, 1, 6, 53, 'BATERIA ENERJET 21P144 N2', NULL, NULL, 'BAT157', NULL, NULL, 503.82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '21P144', 'S/.'),
(3857, 284, 1, 6, 55, 'LLANTA ARMOUR 23.5-25 E-3 20PR', NULL, NULL, 'LH0857', NULL, NULL, 1020, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '23.5-25', 'US$'),
(3858, 408, 1, 6, 55, 'LLANTA BKT 14-17.5 NHS 14PR SKID POWER', NULL, NULL, 'LH0856', NULL, NULL, 336, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '14-17.5', 'US$'),
(3859, 280, 1, 6, 53, 'BATERIA HANKOOK MF160G51L', NULL, NULL, 'BAT156', NULL, NULL, 573.77, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '160G51L', 'S/.'),
(3860, 280, 1, 6, 53, 'BATERIA HANKOOK MF105D31L', NULL, NULL, 'BAT155', NULL, NULL, 338.87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '105D31L', 'S/.'),
(3861, 280, 1, 6, 53, 'BATERIA HANKOOK MF75D31R 75AH 660CCA', NULL, NULL, 'BAT154', NULL, NULL, 283.07, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '75D31R', 'S/.'),
(3862, 280, 1, 6, 53, 'BATERIA HANKOOK MF80D26R 70AH 600CCA', NULL, NULL, 'BAT152', NULL, NULL, 270.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '80D26R', 'S/.'),
(3863, 280, 1, 6, 53, 'BATERIA HANKOOK MF80D26L 70AH', NULL, NULL, 'BAT151', NULL, NULL, 270.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '80D26L', 'S/.'),
(3864, 280, 1, 6, 53, 'BATERIA HANKOOK MF75D23L 65AH 580CCA', NULL, NULL, 'BAT150', NULL, NULL, 259, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '75D23L', 'S/.'),
(3865, 280, 1, 6, 53, 'BATERIA HANKOOK MF55459 54AH 480CCA', NULL, NULL, 'BAT149', NULL, NULL, 227.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55459', 'US$'),
(3866, 280, 1, 6, 53, 'BATERIA HANKOOK MF56077 60AH 510CCA', NULL, NULL, 'BAT148', NULL, NULL, 254.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '56077', 'S/.'),
(3867, 299, 1, 6, 52, 'ARO DRAGON WHEELS 479 16X7.5 8X100/114.3 20 74.1 J4/ML', NULL, NULL, 'ARO436', NULL, NULL, 75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '479F', 'US$'),
(3868, 299, 1, 6, 52, 'ARO DRAGON WHEELS 479 16X7.5 8X100/114.3 20 74.1 B/ML', NULL, NULL, 'ARO435', NULL, NULL, 75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '479F', 'US$'),
(3869, 299, 1, 6, 52, 'ARO DRAGON WHEELS 256 17X7.0 8X100/108 15 73.1 BP/M5', NULL, NULL, 'ARO434', NULL, NULL, 84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '256', 'US$'),
(3870, 299, 1, 6, 52, 'ARO DRAGON WHEELS W009 17X7.0 8X100/114.3 35 LA5-B/MF', NULL, NULL, 'ARO433', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '009', 'US$'),
(3871, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1075 17X8.0 8X100/114.3 25 73.1 BMF', NULL, NULL, 'ARO432', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1075', 'US$'),
(3872, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3275 17X7.5 8X100/114.3 25 73.1 B-P', NULL, NULL, 'ARO431', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3275', 'US$'),
(3873, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3113 17X7.5 10X100/114.3 39 73.1 BP/M', NULL, NULL, 'ARO430', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3113', 'US$'),
(3874, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1101 17X7.5 8X100/114.3 30 73.1 BMF', NULL, NULL, 'ARO429', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1101', 'US$'),
(3875, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1034 17X7.5 8X100/114.3 25 LA5-B/MN', NULL, NULL, 'ARO428', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1034', 'US$');
INSERT INTO `Gen_Producto` (`IdProducto`, `IdProductoMarca`, `IdProductoFormaFarmaceutica`, `IdProductoMedicion`, `IdProductoCategoria`, `Producto`, `ProductoDesc`, `ProductoDescCorto`, `CodigoBarra`, `Codigo`, `Dosis`, `PrecioContado`, `PrecioPorMayor`, `StockPorMayor`, `StockMinimo`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `ControlaStock`, `PrecioCosto`, `VentaEstrategica`, `PorcentajeUtilidad`, `IdBloque`, `Modelo`, `Moneda`) VALUES
(3876, 299, 1, 6, 52, 'ARO DRAGON WHEELS 951 16X7.0 5X100 40 67.1 BHS', NULL, NULL, 'ARO427', NULL, NULL, 75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '951', 'US$'),
(3877, 299, 1, 6, 52, 'ARO DRAGON WHEELS 348 16X7.0 8X100/114.3 35 73.1 BHS', NULL, NULL, 'ARO426', NULL, NULL, 74.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '348', 'US$'),
(3878, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3275 16X7 8X100/114.3 25 73.1 B-P', NULL, NULL, 'ARO425', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3275', 'US$'),
(3879, 299, 1, 6, 52, 'ARO DRAGON WHEELS 126 16X7.5 5X139.7 110.5BMF', NULL, NULL, 'ARO424', NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '126', 'US$'),
(3880, 299, 1, 6, 52, 'ARO DRAGON WHEELS 600 15X6.5 8X100/114.3 (B-P)B-LP/M5', NULL, NULL, 'ARO423', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '600', 'US$'),
(3881, 299, 1, 6, 52, 'ARO DRAGON WHEELS 626 18X9.5 5X114.3 28 73.1 LA5-B', NULL, NULL, 'ARO422', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '626', 'US$'),
(3882, 299, 1, 6, 52, 'ARO DRAGON WHEELS 347 18X8.0 5X112 35 73.1 LA5-B/ML', NULL, NULL, 'ARO421', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '347', 'US$'),
(3883, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1155 18X7.5 6X139.7 25 108.5 B/MF', NULL, NULL, 'ARO420', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155', 'US$'),
(3884, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1139 20X9.5 5X114.3 LA5-B', NULL, NULL, 'ARO419', NULL, NULL, 147, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1139', 'US$'),
(3885, 331, 1, 6, 55, 'LLANTA DEESTONE 245/75R16 MT PAYAK', NULL, NULL, 'LH0855', NULL, NULL, 128.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(3886, 280, 1, 6, 55, 'LLANTA HANKOOK 235/65R17 HT 104H', NULL, NULL, 'LH0854', NULL, NULL, 111.49, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(3887, 280, 1, 6, 55, 'LLANTA HANKOOK 225/70R16 AT DYNAPRO', NULL, NULL, 'LH0853', NULL, NULL, 128.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'US$'),
(3888, 280, 1, 6, 55, 'LLANTA HANKOOK 235/55R19 HP XL RA33 105V', NULL, NULL, 'LH0852', NULL, NULL, 149.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/55R19', 'US$'),
(3889, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.50-16 POS. PANTANERA 10PR', NULL, NULL, 'LH0851', NULL, NULL, 150.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(3890, 371, 1, 6, 53, 'BATERIA ALFA AT-23 MAXIMA DURACION', NULL, NULL, 'BAT147', NULL, NULL, 468.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AT-23B', 'S/.'),
(3891, 371, 1, 6, 53, 'BATERIA ALFA AF-15 MAXIMA DURACION', NULL, NULL, 'BAT146', NULL, NULL, 289.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AF-15', 'S/.'),
(3892, 371, 1, 6, 53, 'BATERIA ALFA AF-13 MAXIMA DURACION', NULL, NULL, 'BAT145', NULL, NULL, 271.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AF-13', 'S/.'),
(3893, 371, 1, 6, 53, 'BATERIA ALFA AC-13 MAXIMA DURACION', NULL, NULL, 'BAT144', NULL, NULL, 224.03, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AC-13', 'S/.'),
(3894, 371, 1, 6, 53, 'BATERIA ALFA AC-11 MAXIMA DURACION', NULL, NULL, 'BAT143', NULL, NULL, 197.17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AC-11', 'S/.'),
(3895, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 6.50-14 POS 8PR TD-442', NULL, NULL, 'LH0850', NULL, NULL, 99.35, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(3896, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 6.50-14 DEL 8PR TH-200', NULL, NULL, 'LH0849', NULL, NULL, 92.87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(3897, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3206 14X6.0 ET33 100+114.3X8H', NULL, NULL, 'ARO418', NULL, NULL, 48.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3206', 'US$'),
(3898, 312, 1, 6, 55, 'LLANTA MIRAGE 8.25R20 MIX MG-702 139/137L 16PR', NULL, NULL, 'LH0845', NULL, NULL, 178.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R20', 'US$'),
(3899, 312, 1, 6, 55, 'LLANTA MIRAGE 195/70R15 MR200 8PR', NULL, NULL, 'LH0844', NULL, NULL, 48.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R15', 'US$'),
(3900, 416, 1, 6, 55, 'LLANTA GREMAX 215/75R16 CAPTURAR 113/111R 8PR', NULL, NULL, 'LH0843', NULL, NULL, 64.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R16', 'US$'),
(3901, 415, 1, 6, 55, 'LLANTA LUCKYLAND 205/65R15 94H HG01', NULL, NULL, 'LH0842', NULL, NULL, 43.98, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/65R15', 'US$'),
(3902, 415, 1, 6, 55, 'LLANTA LUCKYLAND 195/60R15 LCG01 88V', NULL, NULL, 'LH0841', NULL, NULL, 38.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'US$'),
(3903, 414, 1, 6, 55, 'LLANTA FIREMAX 185/70R14 CITY TOUR 88H', NULL, NULL, 'LH0840', NULL, NULL, 29.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(3904, 413, 1, 6, 55, 'LLANTA POWERTRAC 185/70R14 CITY TOUR 88H', NULL, NULL, 'LH0838', NULL, NULL, 31.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(3905, 291, 1, 6, 55, 'LLANTA HIFLY 255/60R18 HT VIGOROUS', NULL, NULL, 'LH0837', NULL, NULL, 70.58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/60R18', 'US$'),
(3906, 291, 1, 6, 55, 'LLANTA HIFLY 245/60R18 HT VIGOROUS 105T', NULL, NULL, 'LH0836', NULL, NULL, 67.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/60R18', 'US$'),
(3907, 413, 1, 6, 55, 'LLANTA POWERTRAC 215/70R15 AT POWERLANDER', NULL, NULL, 'LH0835', NULL, NULL, 54.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R15', 'US$'),
(3908, 413, 1, 6, 55, 'LLANTA POWERTRAC 215/75R16 ST VANTOUR 8PR', NULL, NULL, 'LH0834', NULL, NULL, 61.15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R16', 'US$'),
(3909, 357, 1, 6, 55, 'LLANTA SUNFULL 225/40R18 SF888 92W', NULL, NULL, 'LH0833', NULL, NULL, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/40R18', 'US$'),
(3910, 335, 1, 6, 55, 'LLANTA CONTINENTAL 235/60R17 HT CROSSCONTACT', NULL, NULL, 'LH0832', NULL, NULL, 128.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R17', 'US$'),
(3911, 357, 1, 6, 55, 'LLANTA SUNFULL 225/60R17 HT HT782 99H', NULL, NULL, 'LH0831', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/60R17', 'US$'),
(3912, 284, 1, 6, 55, 'LLANTA ARMOUR 12.5/80-18 14PR L2D', NULL, NULL, 'LH0830', NULL, NULL, 205.72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.5/80-18', 'US$'),
(3913, 291, 1, 6, 55, 'LLANTA HIFLY 295/80R22.5 POS. 18PR', NULL, NULL, 'LH0829', NULL, NULL, 218.57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '295/80R22.5', 'US$'),
(3914, 369, 1, 6, 55, 'LLANTA COMPASAL 245/70R19.5 DEL CPS21 16PR', NULL, NULL, 'LH0828', NULL, NULL, 125.33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R19.5', 'US$'),
(3915, 369, 1, 6, 55, 'LLANTA COMPASAL 245/70R19.5 POS CPD81 14PR', NULL, NULL, 'LH0827', NULL, NULL, 128, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R19.5', 'US$'),
(3916, 313, 1, 6, 55, 'LLANTA GENERAL 215/70R16 AT GRABBER', NULL, NULL, 'LH0826', NULL, NULL, 105.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'US$'),
(3917, 412, 1, 6, 55, 'LLANTA EL DORADO 245/70R16 AT ALL TERRAIN', NULL, NULL, 'LH0824', NULL, NULL, 74.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R16', 'US$'),
(3918, 412, 1, 6, 55, 'LLANTA EL DORADO 235/75R15 AT ALL TERRAIN', NULL, NULL, 'LH0823', NULL, NULL, 70.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'US$'),
(3919, 412, 1, 6, 55, 'LLANTA EL DORADO 235/75R15 MT ALL TERRAIN', NULL, NULL, 'LH0822', NULL, NULL, 86.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'US$'),
(3920, 412, 1, 6, 55, 'LLANTA EL DORADO 195/65R15 91H ULTRA TOUR I', NULL, NULL, 'LH0821', NULL, NULL, 37.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'US$'),
(3921, 412, 1, 6, 55, 'LLANTA EL DORADO 195/60R15 88H ULTRA TOUR I', NULL, NULL, 'LH0820', NULL, NULL, 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'US$'),
(3922, 412, 1, 6, 55, 'LLANTA EL DORADO 225/45R17 94W ULTRA SPORT I', NULL, NULL, 'LH0819', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/45R17', 'US$'),
(3923, 412, 1, 6, 55, 'LLANTA EL DORADO 215/65R16 98H ULTRA TOUR I', NULL, NULL, 'LH0818', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/65R16', 'US$'),
(3924, 412, 1, 6, 55, 'LLANTA EL DORADO 205/70R15 HT ULTRA TOUR', NULL, NULL, 'LH0817', NULL, NULL, 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'US$'),
(3925, 412, 1, 6, 55, 'LLANTA EL DORADO 205/60R16 ULTRA TOUR 92H', NULL, NULL, 'LH0816', NULL, NULL, 43.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R16', 'US$'),
(3926, 412, 1, 6, 55, 'LLANTA EL DORADO 265/70R16 AT ALL TERRAIN', NULL, NULL, 'LH0815', NULL, NULL, 76.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(3927, 412, 1, 6, 55, 'LLANTA EL DORADO 235/60R16 HT ULTRA TOUR 100H', NULL, NULL, 'LH0814', NULL, NULL, 58.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R16', 'US$'),
(3928, 412, 1, 6, 55, 'LLANTA EL DORADO 225/60R17 HT ULTRA TOUR 99H', NULL, NULL, 'LH0813', NULL, NULL, 58.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/60R17', 'US$'),
(3929, 412, 1, 6, 55, 'LLANTA EL DORADO 195/55R15 85V ULTRA TOUR I', NULL, NULL, 'LH0812', NULL, NULL, 38.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/55R15', 'US$'),
(3930, 412, 1, 6, 55, 'LLANTA EL DORADO 195/50R15 82H ULTRA TOUR I', NULL, NULL, 'LH0811', NULL, NULL, 39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/50R15', 'US$'),
(3931, 412, 1, 6, 55, 'LLANTA EL DORADO 185/65R15 88H ULTRA TOUR I', NULL, NULL, 'LH0810', NULL, NULL, 33.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'US$'),
(3932, 412, 1, 6, 55, 'LLANTA EL DORADO 185/65R14 86H ULTRA TOUR I', NULL, NULL, 'LH0809', NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'US$'),
(3933, 412, 1, 6, 55, 'LLANTA EL DORADO 185/60R15 88H ULTRA TOUR I', NULL, NULL, 'LH0808', NULL, NULL, 36.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R15', 'US$'),
(3934, 412, 1, 6, 55, 'LLANTA EL DORADO 185/60R14 82H ULTRA TOUR II', NULL, NULL, 'LH0807', NULL, NULL, 31.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R14', 'US$'),
(3935, 412, 1, 6, 55, 'LLANTA EL DORADO 175/70R14 84T ULTRA TOUR I', NULL, NULL, 'LH0806', NULL, NULL, 29.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'US$'),
(3936, 412, 1, 6, 55, 'LLANTA EL DORADO 175/70R13 ULTRA TOUR 82T', NULL, NULL, 'LH0805', NULL, NULL, 26.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(3937, 412, 1, 6, 55, 'LLANTA EL DORADO 175/65R14 82T ULTRA TOUR I', NULL, NULL, 'LH0804', NULL, NULL, 28.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'US$'),
(3938, 412, 1, 6, 55, 'LLANTA EL DORADO 165/65R13 ULTRA TOUR 77T', NULL, NULL, 'LH0925', NULL, NULL, 25.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'US$'),
(3939, 411, 1, 6, 55, 'LLANTA BFGOODRICH 265/75R16 AT ALL TERRAIN', NULL, NULL, 'LH0802', NULL, NULL, 600, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'S/.'),
(3940, 398, 1, 6, 55, 'LLANTA BFGOODRICH 235/75R15 AT ALL TERRAIN', NULL, NULL, 'LH0801', NULL, NULL, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'US$'),
(3941, 338, 1, 6, 55, 'LLANTA PIRELLI 255/60R18 AT SCORPION', NULL, NULL, 'LH0800', NULL, NULL, 206.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/60R18', 'US$'),
(3942, 290, 1, 6, 55, 'LLANTA TRIANGLE 205/50R15 TR918 89H', NULL, NULL, 'LH0797', NULL, NULL, 48.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'US$'),
(3943, 299, 1, 6, 52, 'ARO DRAGON WHEELS 756 13X6.0 8X100/114.3 MF', NULL, NULL, 'ARO416', NULL, NULL, 46.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '756', 'US$'),
(3944, 299, 1, 6, 52, 'ARO DRAGON WHEELS 620 13X7.0 4X100-7 BML', NULL, NULL, 'ARO415', NULL, NULL, 46.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '620', 'US$'),
(3945, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1060 13X5.5 8X100/114.3 BMF', NULL, NULL, 'ARO414', NULL, NULL, 46.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1060', 'US$'),
(3946, 296, 1, 6, 52, 'ARO PDW 525854 15X6.5 4H100 73.10 M/B', NULL, NULL, 'ARO413', NULL, NULL, 69.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5258', 'US$'),
(3947, 397, 1, 6, 52, 'ARO XTREME 3247 18X8.0 5H114.3 B', NULL, NULL, 'ARO412', NULL, NULL, 112.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3247', 'US$'),
(3948, 391, 1, 6, 52, 'ARO ION 196-29337 20X9 6H/135/139.7 M/BCU', NULL, NULL, 'ARO411', NULL, NULL, 180.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '196', 'US$'),
(3949, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-5008 17X9.0 ET30 6x139.7 BMF', NULL, NULL, 'ARO410', NULL, NULL, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5008', 'US$'),
(3950, 296, 1, 6, 52, 'ARO PDW W-6701118 16X8 6H/139.7', NULL, NULL, 'ARO409', NULL, NULL, 96.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6701', 'US$'),
(3951, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-8522 16X8 ET35 6X1143 MB', NULL, NULL, 'ARO408', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8522', 'US$'),
(3952, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-66522 16X8.0 ET35 5X114.3 B/RI', NULL, NULL, 'ARO407', NULL, NULL, 96.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '66522', 'US$'),
(3953, 410, 1, 6, 52, 'ARO AMERICAN WHEELS LGS0311 15X8.0 5X114.3 BMF', NULL, NULL, 'ARO406', NULL, NULL, 73.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LGS03', 'US$'),
(3954, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-37503 15X8.0 ET25 5X139.7 B-M', NULL, NULL, 'ARO405', NULL, NULL, 74.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3750', 'US$'),
(3955, 296, 1, 6, 52, 'ARO PDW 526749 15X6.5 4H10073.10 M/UB', NULL, NULL, 'ARO404', NULL, NULL, 69.89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5267', 'US$'),
(3956, 296, 1, 6, 52, 'ARO PDW 5400344 15X7 4H10073.1 MI/B', NULL, NULL, 'ARO417', NULL, NULL, 70.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '540', 'US$'),
(3957, 296, 1, 6, 52, 'ARO PDW W-426924 14X6.5 ET35 4X100.0 MB', NULL, NULL, 'ARO403', NULL, NULL, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '426924', 'US$'),
(3958, 397, 1, 6, 52, 'ARO XTREME 359 14X6.0 100X114.3 BP', NULL, NULL, 'ARO402', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '359', 'US$'),
(3959, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-73601 18X8.5 ET0 5X120 MB', NULL, NULL, 'ARO401', NULL, NULL, 116.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7360', 'US$'),
(3960, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-507114 16X7.5 ET30 5X114.3 ELBR', NULL, NULL, 'ARO400', NULL, NULL, 84.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5071', 'US$'),
(3961, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG1703 16X7.5 4X100/114.3 MB', NULL, NULL, 'ARO399', NULL, NULL, 69.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG17', 'US$'),
(3962, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG0308 15X7.0 4X100/114.3 MB', NULL, NULL, 'ARO398', NULL, NULL, 69.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG03', 'US$'),
(3963, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG501 15X8 4X100/114.3MB', NULL, NULL, 'ARO397', NULL, NULL, 70.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG50', 'US$'),
(3964, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG4301 14X6 4X100/114.3 MB', NULL, NULL, 'ARO396', NULL, NULL, 60.31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG43', 'US$'),
(3965, 295, 1, 6, 52, 'ARO AMERICAN WHEELS LG4601 13X5.5 4X100/114.3 MB', NULL, NULL, 'ARO395', NULL, NULL, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG46', 'US$'),
(3966, 405, 1, 6, 55, 'LLANTA GINELL 305/70R16 LT8PR 118/115Q GN3000MT', NULL, NULL, 'LH0795', NULL, NULL, 142.86, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '305/70R16', 'US$'),
(3967, 311, 1, 6, 55, 'LLANTA APLUS 31X10.50R15 MT MUD TERRAIN', NULL, NULL, 'LH0793', NULL, NULL, 87.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.50R15', 'US$'),
(3968, 365, 1, 6, 53, 'BATERIA CAPSA 288D PREMIUM', NULL, NULL, 'BAT142', NULL, NULL, 621.43, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '288D', 'US$'),
(3969, 365, 1, 6, 53, 'BATERIA CAPSA 234D PREMIUM', NULL, NULL, 'BAT141', NULL, NULL, 575.15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '234D', 'US$'),
(3970, 409, 1, 6, 52, 'ARO ALMARO VK5033 15X6.5 4X100', NULL, NULL, 'ARO394', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5033', 'US$'),
(3971, 409, 1, 6, 52, 'ARO ALMARO VK5199 14X6.0 8X100/114.3', NULL, NULL, 'ARO393', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5199', 'US$'),
(3972, 409, 1, 6, 52, 'ARO ALMARO VK313 13X5.5 8X100/114.3', NULL, NULL, 'ARO392', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '313', 'US$'),
(3973, 409, 1, 6, 52, 'ARO ALMARO VK321 16X8.0 6X139.7', NULL, NULL, 'ARO391', NULL, NULL, 91.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '321', 'US$'),
(3974, 409, 1, 6, 52, 'ARO ALMARO VK513 15X7.0 6X139.7', NULL, NULL, 'ARO390', NULL, NULL, 73.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '513', 'US$'),
(3975, 409, 1, 6, 52, 'ARO ALMARO VK859 15X8.0 6X139.7', NULL, NULL, 'ARO389', NULL, NULL, 73.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '859', 'US$'),
(3976, 409, 1, 6, 52, 'ARO ALMARO VK413 14X7.0 5X114.3', NULL, NULL, 'ARO388', NULL, NULL, 67.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '413', 'US$'),
(3977, 409, 1, 6, 52, 'ARO ALMARO VK470 15X6.5 8X100/114.3', NULL, NULL, 'ARO387', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '470', 'US$'),
(3978, 409, 1, 6, 52, 'ARO ALMARO VK188 15X6.5 10X100/114.3', NULL, NULL, 'ARO386', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '188', 'US$'),
(3979, 409, 1, 6, 52, 'ARO ALMARO VK196 14X6.0 8X100/114.3', NULL, NULL, 'ARO384', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '196', 'US$'),
(3980, 409, 1, 6, 52, 'ARO ALMARO VK233 14X6.0 8X100/114.3', NULL, NULL, 'ARO383', NULL, NULL, 59.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '233', 'US$'),
(3981, 409, 1, 6, 52, 'ARO ALMARO VK346 14X6.0 8X100/114.3', NULL, NULL, 'ARO382', NULL, NULL, 58.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '346', 'US$'),
(3982, 409, 1, 6, 52, 'ARO ALMARO VK479 14X6.0 8X100/114.3', NULL, NULL, 'ARO381', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '479', 'US$'),
(3983, 409, 1, 6, 52, 'ARO ALMARO VK224 13X6.0 8X100/114.3', NULL, NULL, 'ARO379', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '224', 'US$'),
(3984, 409, 1, 6, 52, 'ARO ALMARO VK312 13X5.5 8X100/114.3', NULL, NULL, 'ARO378', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '312', 'US$'),
(3985, 409, 1, 6, 52, 'ARO ALMARO VK446 13X6.0 4X100', NULL, NULL, 'ARO376', NULL, NULL, 49.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '446', 'US$'),
(3986, 409, 1, 6, 52, 'ARO ALMARO VK470 13X5.5 8X100/114.3', NULL, NULL, 'ARO375', NULL, NULL, 49.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '470', 'US$'),
(3987, 317, 1, 6, 55, 'LLANTA GOOD YEAR 295/80R22.5 DEL G665', NULL, NULL, 'LH0791', NULL, NULL, 564, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '295/80R22.5', 'US$'),
(3988, 331, 1, 6, 55, 'LLANTA DEESTONE 235/40ZR18XL 95W XL R302', NULL, NULL, 'LH0789', NULL, NULL, 87.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/40R18', 'US$'),
(3989, 331, 1, 6, 55, 'LLANTA DEESTONE 235/55R18 104V XL007 R601', NULL, NULL, 'LH0788', NULL, NULL, 99.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/55R18', 'US$'),
(3990, 331, 1, 6, 55, 'LLANTA DEESTONE 205/60R14 88H R203', NULL, NULL, 'LH0786', NULL, NULL, 58.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'US$'),
(3991, 331, 1, 6, 55, 'LLANTA DEESTONE 205R16C R101 8PR', NULL, NULL, 'LH0785', NULL, NULL, 88.21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205R16', 'US$'),
(3992, 331, 1, 6, 55, 'LLANTA DEESTONE 205/60R16 R302 91H', NULL, NULL, 'LH0784', NULL, NULL, 57.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R16', 'US$'),
(3993, 331, 1, 6, 55, 'LLANTA DEESTONE 205/50R16 R702 87W', NULL, NULL, 'LH0782', NULL, NULL, 58.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'US$'),
(3994, 331, 1, 6, 55, 'LLANTA DEESTONE 185/60R15 R203', NULL, NULL, 'LH0781', NULL, NULL, 37.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R15', 'US$'),
(3995, 408, 1, 6, 55, 'LLANTA BKT 7.50-16 POS 16PR COLT', NULL, NULL, 'LH0780', NULL, NULL, 136.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(3996, 366, 1, 6, 55, 'LLANTA HABILEAD 245/75R16 MT PRACTICALMAX', NULL, NULL, 'LH0779', NULL, NULL, 100.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(3997, 408, 1, 6, 55, 'LLANTA BKT 7.50-16 DEL ARROW 16PR', NULL, NULL, 'LH0778', NULL, NULL, 136.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(3998, 366, 1, 6, 55, 'LLANTA HABILEAD 215/45R17 SPORTMAX 92V', NULL, NULL, 'LH0777', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'US$'),
(3999, 366, 1, 6, 55, 'LLANTA HABILEAD 265/70R16 AT DURABLEMAX', NULL, NULL, 'LH0775', NULL, NULL, 103.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(4000, 354, 1, 6, 55, 'LLANTA KAPSEN 225/70R15 RS01 112/110R', NULL, NULL, 'LH0774', NULL, NULL, 55.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R15', 'S/.'),
(4001, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.50-16 POS CHASQUI 12PR', NULL, NULL, 'LH0773', NULL, NULL, 588.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'S/.'),
(4002, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.00-16 POS. CHASQUI 12PR', NULL, NULL, 'LH0772', NULL, NULL, 129.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-16', 'US$'),
(4003, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.00-16 DEL CAMINERA 10PR', NULL, NULL, 'LH0771', NULL, NULL, 119.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-16', 'S/.'),
(4004, 317, 1, 6, 55, 'LLANTA GOOD YEAR 6.50-14 POS CHASQUI 8PR', NULL, NULL, 'LH0770', NULL, NULL, 67.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(4005, 363, 1, 6, 53, 'BATERIA RECORD RT 165 PLUS', NULL, NULL, 'BAT140', NULL, NULL, 621.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RT165', 'S/.'),
(4006, 393, 1, 6, 53, 'BATERIA ETNA W-11 DE 11 PLACAS', NULL, NULL, 'BAT139', NULL, NULL, 216, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'W-11', 'S/.'),
(4007, 393, 1, 6, 53, 'BATERIA ETNA W-13 DE 13 PLACAS', NULL, NULL, 'BAT138', NULL, NULL, 237.58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'W-13', 'S/.'),
(4008, 393, 1, 6, 53, 'BATERIA ETNA V-13 DE 13 PLACAS', NULL, NULL, 'BAT137', NULL, NULL, 254.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'V-13', 'S/.'),
(4009, 393, 1, 6, 53, 'BATERIA ETNA V-11 DE 11 PLACAS', NULL, NULL, 'BAT136', NULL, NULL, 224.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'V-11', 'S/.'),
(4010, 278, 1, 6, 55, 'LLANTA HAIDA 215/75R14 AT HD818', NULL, NULL, 'LH0769', NULL, NULL, 62.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R14', 'US$'),
(4011, 348, 1, 6, 55, 'LLANTA DRC 6.50 -14 12PR 52D', NULL, NULL, 'LH0768', NULL, NULL, 93.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'S/.'),
(4012, 406, 1, 6, 55, 'LLANTA HENGDA 4.00-8 MIX 8PR HDM067', NULL, NULL, 'LH0767', NULL, NULL, 12.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4.00-8', 'US$'),
(4013, 291, 1, 6, 55, 'LLANTA HIFLY 215/75R15 AT VIGOROUS', NULL, NULL, 'LH0766', NULL, NULL, 60.71, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'US$'),
(4014, 405, 1, 6, 55, 'LLANTA GINELL 31X10.50R15 MT GN3000', NULL, NULL, 'LH0765', NULL, NULL, 98.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.50R15', 'US$'),
(4015, 326, 1, 6, 55, 'LLANTA DURUN 185/60R15 99V A2000', NULL, NULL, 'LH0764', NULL, NULL, 35, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R15', 'S/.'),
(4016, 404, 1, 6, 55, 'LLANTA SPORTIVA 215/75R14 AT RADIAL', NULL, NULL, 'LH0763', NULL, NULL, 82.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R14', 'US$'),
(4017, 344, 1, 6, 55, 'LLANTA AUTOGRIP 165/60R14 T5H 808', NULL, NULL, 'LH0762', NULL, NULL, 27.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/60R14', 'US$'),
(4018, 284, 1, 6, 55, 'LLANTA ARMOUR 6.50-14 DEL SET 8PR T2', NULL, NULL, 'LH0761', NULL, NULL, 70.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(4019, 311, 1, 6, 55, 'LLANTA APLUS 195/65R15 A606 4PR', NULL, NULL, 'LH0760', NULL, NULL, 33.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'US$'),
(4020, 403, 1, 6, 55, 'LLANTA DOUBLE STAR 5.00R12 DS809 83/81R 8PR', NULL, NULL, 'LH0758', NULL, NULL, 28.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5.00R12', 'US$'),
(4021, 335, 1, 6, 55, 'LLANTA CONTINENTAL 315/80R22.5 DEL HSR2 20PR', NULL, NULL, 'LH0757', NULL, NULL, 482.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '315/80R22.5', 'US$'),
(4022, 335, 1, 6, 55, 'LLANTA CONTINENTAL 295/80R22.5 DEL.CONTICITYPLUS', NULL, NULL, 'LH0756', NULL, NULL, 407.21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '295/80R22.5', 'US$'),
(4023, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-9012 15X7.5 ET25 6X139.7 B4', NULL, NULL, 'ARO374', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9012', 'US$'),
(4024, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-2042 15X8.0 ET30 6X139.7 B4', NULL, NULL, 'ARO373', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2042', 'US$'),
(4025, 303, 1, 6, 52, 'ARO MAZZARO MZ-1256 13X5.5 100/114.3X8H B4', NULL, NULL, 'ARO372', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1256', 'US$'),
(4026, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-519 18X8.0 ET30 114.3X5H B-LP/M', NULL, NULL, 'ARO371', NULL, NULL, 109.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '519', 'US$'),
(4027, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-331 17X7.5 ET0 100/114.3X8H B-P', NULL, NULL, 'ARO370', NULL, NULL, 95.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '331', 'US$'),
(4028, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-391 17X7.5 100/114.3X8H B-P/M', NULL, NULL, 'ARO369', NULL, NULL, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '391', 'US$'),
(4029, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3113 17X7.5 100/114.3X8H B-P', NULL, NULL, 'ARO368', NULL, NULL, 95.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3113', 'US$'),
(4030, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3905 16X7.0 ET0 100+114.3X8H B-P', NULL, NULL, 'ARO367', NULL, NULL, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3905', 'US$'),
(4031, 301, 1, 6, 52, 'ARO DH-2003 DEMONIUM 16X7.0 108X4H B4TRZX', NULL, NULL, 'ARO366', NULL, NULL, 84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2003', 'US$'),
(4032, 301, 1, 6, 52, 'ARO DH-3250 DEMONIUM 14X6.0 100X4H B-P', NULL, NULL, 'ARO365', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3250', 'US$'),
(4033, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-6704 13X5.5 ET30 100X4H B6', NULL, NULL, 'ARO364', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6704', 'US$'),
(4034, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3206 13X5.5 ET0 100/114.3X8H B-P', NULL, NULL, 'ARO363', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3206', 'US$'),
(4035, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-720 13X6.0 ET35 100X4H B-P', NULL, NULL, 'ARO362', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '720', 'US$'),
(4036, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-348 13X5.5 ET35 100+114.3X8H BKL-M', NULL, NULL, 'ARO361', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '348', 'US$'),
(4037, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-140 13X5.5 ET35 100/114.3X8H B-P', NULL, NULL, 'ARO360', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '140', 'US$'),
(4038, 390, 1, 6, 53, 'BATERIA MOTO KOYO 12N7A -3A 12V', NULL, NULL, 'BAT135', NULL, NULL, 91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N7A-3A', 'S/.'),
(4039, 390, 1, 6, 53, 'BATERIA MOTO KOYO YTX7A-BS 12V', NULL, NULL, 'BAT133', NULL, NULL, 109.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX7A-BS', 'S/.'),
(4040, 390, 1, 6, 53, 'BATERIA MOTO KOYO YTX7L-BS', NULL, NULL, 'BAT132', NULL, NULL, 99.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX7L-BS', 'S/.'),
(4041, 402, 1, 6, 55, 'LLANTA KINGRUN 225/45R17 K3000 PHANTOM', NULL, NULL, 'LH0754', NULL, NULL, 37.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/45R17', 'US$'),
(4042, 361, 1, 6, 55, 'LLANTA SOLIDEAL 19.5-24 12PR R-4 SLK', NULL, NULL, 'LH0753', NULL, NULL, 520.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '19.5-24', 'US$'),
(4043, 401, 1, 6, 55, 'LLANTA INTERSTATE 175/65R14 TOURING 84H', NULL, NULL, 'LH0752', NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'US$'),
(4044, 373, 1, 6, 55, 'LLANTA COOPER 215/65R16 98T CS1 LTR', NULL, NULL, 'LH0751', NULL, NULL, 70.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/65R16', 'US$'),
(4045, 373, 1, 6, 55, 'LLANTA COOPER 245/65R17 AT DISCOVERER', NULL, NULL, 'LH0749', NULL, NULL, 161.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/65R17', 'US$'),
(4046, 373, 1, 6, 55, 'LLANTA COOPER 235/65R17 AT DISCOVER', NULL, NULL, 'LH0748', NULL, NULL, 161.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(4047, 400, 1, 6, 55, 'LLANTA MARSHAL 225/65R17 SOLUS 102H', NULL, NULL, 'LH0747', NULL, NULL, 99.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'US$'),
(4048, 275, 1, 6, 55, 'LLANTA TOYO TYRES 215/60R16 PXC10 95V TL', NULL, NULL, 'LH0746', NULL, NULL, 71.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/60R16', 'S/.'),
(4049, 373, 1, 6, 55, 'LLANTA COOPER 185/65R15 88T CS1', NULL, NULL, 'LH0745', NULL, NULL, 46.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'US$'),
(4050, 399, 1, 6, 55, 'LLANTA DURAMAS 185/60R14 82H DT100 PCR', NULL, NULL, 'LH0744', NULL, NULL, 29.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R14', 'S/.'),
(4051, 388, 1, 6, 53, 'BATERIA BOSCH 56637 CHATA', NULL, NULL, 'BAT131', NULL, NULL, 163.55, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '56637', 'US$'),
(4052, 388, 1, 6, 53, 'BATERIA BOSCH 125D31L 19 PLACAS GRANDE', NULL, NULL, 'BAT130', NULL, NULL, 155.29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '125D31L', 'US$'),
(4053, 388, 1, 6, 53, 'BATERIA BOSCH 55B24L 13 PLACAS TOYOTA', NULL, NULL, 'BAT129', NULL, NULL, 71.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55B24L', 'US$'),
(4054, 388, 1, 6, 53, 'BATERIA BOSCH 90D26L 15 PLACAS GRANDE', NULL, NULL, 'BAT128', NULL, NULL, 113.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90D26L', 'US$'),
(4055, 389, 1, 6, 53, 'BATERIA MOTO YUASA YTX9-BS 12V', NULL, NULL, 'BAT127', NULL, NULL, 157.85, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX9-BS', 'S/.'),
(4056, 389, 1, 6, 53, 'BATERIA MOTO YUASA YB2-5L 12V', NULL, NULL, 'BAT126', NULL, NULL, 51.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YB2-5L', 'S/.'),
(4057, 389, 1, 6, 53, 'BATERIA MOTO YUASA TTZ10S 12V', NULL, NULL, 'BAT125', NULL, NULL, 243.38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'TTZ10', 'S/.'),
(4058, 389, 1, 6, 53, 'BATERIA MOTO YUASA 12N7-3B 12V', NULL, NULL, 'BAT124', NULL, NULL, 116.02, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N7-3B', 'S/.'),
(4059, 389, 1, 6, 53, 'BATERIA MOTO YUASA YTX14L BS 12V', NULL, NULL, 'BAT123', NULL, NULL, 228.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX14L', 'S/.'),
(4060, 390, 1, 6, 53, 'BATERIA MOTO KOYO YTX4L-BS 12V', NULL, NULL, 'BAT121', NULL, NULL, 77.89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX4L BS', 'S/.'),
(4061, 390, 1, 6, 53, 'BATERIA MOTO KOYO 12N14-3B', NULL, NULL, 'BAT120', NULL, NULL, 157.25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N14-3B', 'S/.'),
(4062, 390, 1, 6, 53, 'BATERIA MOTO KOYO 12N9-3B 12V', NULL, NULL, 'BAT119', NULL, NULL, 101.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N9-3B', 'S/.'),
(4063, 390, 1, 6, 53, 'BATERIA MOTO KOYO 12N7-3B 12V', NULL, NULL, 'BAT118', NULL, NULL, 91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N7-3B', 'S/.'),
(4064, 390, 1, 6, 53, 'BATERIA MOTO KOYO YTX20L-BS 12V', NULL, NULL, 'BAT117', NULL, NULL, 292.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX20L-BS', 'S/.'),
(4065, 390, 1, 6, 53, 'BATERIA MOTO KOYO YTX7L-BS 12V', NULL, NULL, 'BAT115', NULL, NULL, 109.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX7L-BS', 'S/.'),
(4066, 389, 1, 6, 53, 'BATERIA MOTO YUASA 12N7A-3A 12V', NULL, NULL, 'BAT114', NULL, NULL, 98.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N7A-3A', 'S/.'),
(4067, 389, 1, 6, 53, 'BATERIA MOTO YUASA NP10-6 6V', NULL, NULL, 'BAT113', NULL, NULL, 76.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'NP10-6', 'S/.'),
(4068, 389, 1, 6, 53, 'BATERIA MOTO YUASA NP7-12 12V', NULL, NULL, 'BAT112', NULL, NULL, 96.21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'NP7-12', 'S/.'),
(4069, 389, 1, 6, 53, 'BATERIA MOTO YUASA NP7-6 6V', NULL, NULL, 'BAT111', NULL, NULL, 57.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'NP7-6', 'S/.'),
(4070, 389, 1, 6, 53, 'BATERIA MOTO YUASA NP4-6 6V', NULL, NULL, 'BAT110', NULL, NULL, 39.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'NP4-6', 'S/.'),
(4071, 398, 1, 6, 55, 'LLANTA BF GOODRICH 275/65R17 AT ALL-TERRAIN', NULL, NULL, 'LH0743', NULL, NULL, 414.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/65R17', 'US$'),
(4072, 377, 1, 6, 55, 'LLANTA MICHELIN 265/65R17 AT FORCE', NULL, NULL, 'LH0742', NULL, NULL, 301.43, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'US$'),
(4073, 397, 1, 6, 52, 'ARO XTREME 9093 17X8.0 5H114.3 BP', NULL, NULL, 'ARO359', NULL, NULL, 92.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9093', 'US$'),
(4074, 397, 1, 6, 52, 'ARO XTREME 3259 18X8.0 5H114.3 BP', NULL, NULL, 'ARO358', NULL, NULL, 108.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3259', 'US$'),
(4075, 296, 1, 6, 52, 'ARO PDW W-663824 16X7 10H 100/114.3 MCRB', NULL, NULL, 'ARO357', NULL, NULL, 76.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '663824', 'US$'),
(4076, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-21506 13X5.5 ET40 4X100 MB', NULL, NULL, 'ARO356', NULL, NULL, 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '21506', 'US$'),
(4077, 397, 1, 6, 52, 'ARO XTREME 6708 13X5.5 ET12 4HX100', NULL, NULL, 'ARO355', NULL, NULL, 46.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6708', 'US$'),
(4078, 397, 1, 6, 52, 'ARO XTREME 668 13X6.0 ET35 8H/100/114.3', NULL, NULL, 'ARO354', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '668', 'US$'),
(4079, 397, 1, 6, 52, 'ARO XTREME 639 13X5.5 ET124H100(58.60)B-LP', NULL, NULL, 'ARO353', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '639', 'US$'),
(4080, 397, 1, 6, 52, 'ARO XTREME 606 13 X6.0 ET35 4H114.3 BP', NULL, NULL, 'ARO352', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '606', 'US$'),
(4081, 397, 1, 6, 52, 'ARO XTREME 600 13X5.5 ET35 8H/100X114.3', NULL, NULL, 'ARO351', NULL, NULL, 46.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '600', 'US$'),
(4082, 397, 1, 6, 52, 'ARO XTREME 598 13X5.5ET35 8H BP', NULL, NULL, 'ARO350', NULL, NULL, 46.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '598', 'US$'),
(4083, 397, 1, 6, 52, 'ARO XTREME 5976P 13X5.5 ET35 8H100X114.3 BP', NULL, NULL, 'ARO349', NULL, NULL, 46.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5976', 'US$'),
(4084, 397, 1, 6, 52, 'ARO XTREME 2790 13X5.5 ET 35 BP', NULL, NULL, 'ARO348', NULL, NULL, 41.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2790', 'US$'),
(4085, 312, 1, 6, 55, 'LLANTA MIRAGE 245/75R16 MT MR172', NULL, NULL, 'LH0741', NULL, NULL, 118.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4086, 312, 1, 6, 55, 'LLANTA MIRAGE 235/75R15 AT MR172', NULL, NULL, 'LH0740', NULL, NULL, 76.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'US$'),
(4087, 344, 1, 6, 55, 'LLANTA AUTOGRIP 185/65R14 GRIP1000 86H', NULL, NULL, 'LH0738', NULL, NULL, 27.66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'US$'),
(4088, 349, 1, 6, 55, 'LLANTA VIKRANT 8.25-16 POS LUG SET 18PR', NULL, NULL, 'LH0737', NULL, NULL, 181.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'US$'),
(4089, 349, 1, 6, 55, 'LLANTA VIKRANT 8.25-16 DEL TRACK KING SET 18PR', NULL, NULL, 'LH0736', NULL, NULL, 159.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'US$'),
(4090, 349, 1, 6, 55, 'LLANTA VIKRANT 7.50-16 DEL 16PR TRACK KING', NULL, NULL, 'LH0735', NULL, NULL, 148.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4091, 359, 1, 6, 55, 'LLANTA SPORTRAK 195R15 8PR TL', NULL, NULL, 'LH0734', NULL, NULL, 51.98, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'US$'),
(4092, 299, 1, 6, 52, 'ARO DRAGON WHEELS 509 13X6.0 4X100 35 73 .1 B/F', NULL, NULL, 'ARO346', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '509', 'US$'),
(4093, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1155 17X7.5 6X139. T26 108. 5 B/MF', NULL, NULL, 'ARO345', NULL, NULL, 102.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1155', 'US$'),
(4094, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1075A 16X7.5 ET32 4X100.0 R7/MN', NULL, NULL, 'ARO344', NULL, NULL, 75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1075A', 'US$'),
(4095, 299, 1, 6, 52, 'ARO DRAGON WHEELS 955 13X5.5 4X100 30 73.1 8HS', NULL, NULL, 'ARO343', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '955', 'US$'),
(4096, 299, 1, 6, 52, 'ARO DRAGON WHEELS 307 17X7.5 6X139.7 0 110.50 BMF', NULL, NULL, 'ARO342', NULL, NULL, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '307', 'US$'),
(4097, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1058 17X8.0 10X100/114.3 35 73.1 B/MF', NULL, NULL, 'ARO341', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1058', 'US$'),
(4098, 299, 1, 6, 52, 'ARO DRAGON WHEELS 804 15X6.5 8X100/114.3 35 73.1 B/MF', NULL, NULL, 'ARO340', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '804', 'US$'),
(4099, 299, 1, 6, 52, 'ARO AMERICAN WHEELS 508 15X6.5 4X100 35 73.1 BHS', NULL, NULL, 'ARO339', NULL, NULL, 64.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '508', 'US$'),
(4100, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1088 14X6 .0 4X100 35 73.1 B/MF', NULL, NULL, 'ARO338', NULL, NULL, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1088', 'US$'),
(4101, 299, 1, 6, 52, 'ARO DRAGON WHEELS 307 16X7 6X139.7 0 110.5 G1/MF', NULL, NULL, 'ARO337', NULL, NULL, 86.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '307', 'US$'),
(4102, 299, 1, 6, 52, 'ARO DRAGON WHEELS 2004 16X9. 08X139.7 0108 5. LA 5-B', NULL, NULL, 'ARO336', NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2004', 'US$'),
(4103, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1003 14X5.5 4X100 3073. 1 B/MF', NULL, NULL, 'ARO334', NULL, NULL, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1003', 'US$'),
(4104, 299, 1, 6, 52, 'ARO DRAGON WHEELS 802 14X6.0 8X100/114.3 35 73.1BHS', NULL, NULL, 'ARO333', NULL, NULL, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '802', 'US$'),
(4105, 299, 1, 6, 52, 'ARO DRAGON WHEELS 528 16X7.0 4X100 25 73.1 BHS', NULL, NULL, 'ARO331', NULL, NULL, 76.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '528', 'US$'),
(4106, 299, 1, 6, 52, 'ARO DRAGON WHEELS 528 14X6.0 4X100 35 73.1 BHS', NULL, NULL, 'ARO330', NULL, NULL, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '528', 'US$'),
(4107, 299, 1, 6, 52, 'ARO DRAGON WHEELS 509 14X6 .0 4X100 35 73.1B/MF', NULL, NULL, 'ARO329', NULL, NULL, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '509', 'US$'),
(4108, 299, 1, 6, 52, 'ARO DRAGON WHEELS 508 14X6.0 4X100. 35 73.1 HB1', NULL, NULL, 'ARO328', NULL, NULL, 54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '508', 'US$'),
(4109, 299, 1, 6, 52, 'ARO DRAGON WHEELS 508 16X7.0 4X100 30 73.1 BHB', NULL, NULL, 'ARO327', NULL, NULL, 81.91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '508', 'US$'),
(4110, 299, 1, 6, 52, 'ARO DRAGON WHEELS 158 18X8.0 5X114.3 45 73.1 G1/MF', NULL, NULL, 'ARO326', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '158', 'US$'),
(4111, 350, 1, 6, 55, 'LLANTA ANTARES 205/60R15 INGENSA 91H', NULL, NULL, 'LH0733', NULL, NULL, 46.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'US$'),
(4112, 350, 1, 6, 55, 'LLANTA ANTARES 195R15 LT 8PR NT 3000', NULL, NULL, 'LH0732', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'US$'),
(4113, 364, 1, 6, 53, 'BATERIA ENERJET NS40-38 N2', NULL, NULL, 'BAT108', NULL, NULL, 186, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'NS40', 'S/.'),
(4114, 290, 1, 6, 55, 'LLANTA TRIANGLE 17.5-25 16PR E-3 L-3 TL612', NULL, NULL, 'LH0726', NULL, NULL, 907.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17.5-25', 'US$'),
(4115, 338, 1, 6, 55, 'LLANTA PIRELLI 10.00-20 DEL CENTAURO 18PR', NULL, NULL, 'LH0725', NULL, NULL, 900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10.00-20', 'S/.');
INSERT INTO `Gen_Producto` (`IdProducto`, `IdProductoMarca`, `IdProductoFormaFarmaceutica`, `IdProductoMedicion`, `IdProductoCategoria`, `Producto`, `ProductoDesc`, `ProductoDescCorto`, `CodigoBarra`, `Codigo`, `Dosis`, `PrecioContado`, `PrecioPorMayor`, `StockPorMayor`, `StockMinimo`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `ControlaStock`, `PrecioCosto`, `VentaEstrategica`, `PorcentajeUtilidad`, `IdBloque`, `Modelo`, `Moneda`) VALUES
(4116, 366, 1, 6, 55, 'LLANTA HABILEAD 235/65R16 RS01 DURABLEMAX', NULL, NULL, 'LH0724', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R16', 'S/.'),
(4117, 272, 1, 6, 55, 'LLANTA INSA TURBO 175/70R14 GREEN LING 84T', NULL, NULL, 'LH0723', NULL, NULL, 33.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'US$'),
(4118, 385, 1, 6, 55, 'LLANTA NANKANG 225/45ZR18 95W XL NS-2', NULL, NULL, 'LH0722', NULL, NULL, 84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/45R18', 'S/.'),
(4119, 275, 1, 6, 55, 'LLANTA TOYO TYRES 175/65R14', NULL, NULL, 'LH0721', NULL, NULL, 77.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'S/.'),
(4120, 275, 1, 6, 55, 'LLANTA TOYO TYRES 205/60R16 PXC10 92V', NULL, NULL, 'LH0720', NULL, NULL, 68.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R16', 'US$'),
(4121, 373, 1, 6, 55, 'LLANTA COOPER 195/65R15 CS10 91T', NULL, NULL, 'LH0719', NULL, NULL, 52.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'US$'),
(4122, 373, 1, 6, 55, 'LLANTA COOPER 225/50R17 TOURING 94V CS3 PCF', NULL, NULL, 'LH0716', NULL, NULL, 122.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/50R17', 'US$'),
(4123, 373, 1, 6, 55, 'LLANTA COOPER 225/75R16 AT DISCOVERER 104T', NULL, NULL, 'LH0715', NULL, NULL, 132, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'US$'),
(4124, 373, 1, 6, 55, 'LLANTA COOPER 185/70R13 CS10 85T', NULL, NULL, 'LH0714', NULL, NULL, 37.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(4125, 280, 1, 6, 55, 'LLANTA HANKOOK 235/70R16 AT DYNAPRO', NULL, NULL, 'LH0713', NULL, NULL, 120.07, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/70R16', 'US$'),
(4126, 280, 1, 6, 55, 'LLANTA HANKOOK 265/65R17 AT DYNAPRO', NULL, NULL, 'LH0712', NULL, NULL, 165.71, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'US$'),
(4127, 280, 1, 6, 55, 'LLANTA HANKOOK 235/60R18 AT DYNAPRO', NULL, NULL, 'LH0711', NULL, NULL, 159.91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R18', 'US$'),
(4128, 280, 1, 6, 55, 'LLANTA HANKOOK 175/70R13 OPTIMO ME02 82H', NULL, NULL, 'LH0710', NULL, NULL, 38.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(4129, 280, 1, 6, 55, 'LLANTA HANKOOK165/65R13 K715 77T', NULL, NULL, 'LH0709', NULL, NULL, 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'US$'),
(4130, 341, 1, 6, 55, 'LLANTA ROADSHINE 205/60R15 RS909 91V', NULL, NULL, 'LH0708', NULL, NULL, 44.23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'US$'),
(4131, 321, 1, 6, 55, 'LLANTA XCEED 185/70R14 XD304 88H', NULL, NULL, 'LH0707', NULL, NULL, 35.57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4132, 377, 1, 6, 55, 'LLANTA MICHELIN 2.75-18 CITY PRO TT 48S', NULL, NULL, 'LH0706', NULL, NULL, 117.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2.75-18', 'S/.'),
(4133, 376, 1, 6, 55, 'LLANTA DURO 18-460 POS 335 HF XL250 CG', NULL, NULL, 'LH0705', NULL, NULL, 149.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '18-460', 'S/.'),
(4134, 396, 1, 6, 55, 'LLANTA ITP 25X10.00R12 NHS 80D BAJACROSS 8PR X/D USA', NULL, NULL, 'LH0704', NULL, NULL, 519.77, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '25X10R12', 'S/.'),
(4135, 396, 1, 6, 55, 'LLANTA MUD LITE 26X11.00R12 NHS 80F XTR 6PR USA', NULL, NULL, 'LH0703', NULL, NULL, 538.38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '26X11R12', 'S/.'),
(4136, 396, 1, 6, 55, 'LLANTA ITP 25X8-12 MUD LITE USA', NULL, NULL, 'LH0702', NULL, NULL, 290, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '25X8-12', 'S/.'),
(4137, 396, 1, 6, 55, 'LLANTA ITP BAJACROSS 26X9.00R12 NHS 79D X/D 8PR USA', NULL, NULL, 'LH0701', NULL, NULL, 474.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '26X9R12', 'S/.'),
(4138, 376, 1, 6, 55, 'LLANTA DURO EXCELERATOR 18-100/100 906 HF 62M', NULL, NULL, 'LH0700', NULL, NULL, 180.11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '18-100/100', 'S/.'),
(4139, 376, 1, 6, 55, 'LLANTA DURO17-460 335 HF XL250 CG', NULL, NULL, 'LH0699', NULL, NULL, 131.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17-460', 'S/.'),
(4140, 377, 1, 6, 55, 'LLANTA MICHELIN 120/80-18 625 T63 TT', NULL, NULL, 'LH0698', NULL, NULL, 233.86, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '120/80-18', 'S/.'),
(4141, 395, 1, 6, 55, 'LLANTA MICHELIN 110/80-18 58S T63 TT M/C', NULL, NULL, 'LH0697', NULL, NULL, 191.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '110/80-18', 'S/.'),
(4142, 377, 1, 6, 55, 'LLANTA MICHELIN 160/60ZR17 PILOT STREET 69W', NULL, NULL, 'LH0696', NULL, NULL, 332.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '160/60R17', 'S/.'),
(4143, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 12.00-20 POS EXTRA 18PR TD-440', NULL, NULL, 'LH0695', NULL, NULL, 359.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00-20', 'US$'),
(4144, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 165R13 GT200 RADIAL', NULL, NULL, 'LH0694', NULL, NULL, 45.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165R13', 'US$'),
(4145, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-32809 14X6.0 ET0 4X100+108.2 BM', NULL, NULL, 'ARO325', NULL, NULL, 51.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '32809', 'US$'),
(4146, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-21606 13X5.5 ET30 4X100 B-M', NULL, NULL, 'ARO324', NULL, NULL, 44.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '216', 'US$'),
(4147, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG1709 17X7.5 ET40 5X100.0 MB', NULL, NULL, 'ARO323', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG1709', 'US$'),
(4148, 296, 1, 6, 52, 'ARO PDW W-7553717X7 10H/100/114.3mm B', NULL, NULL, 'ARO322', NULL, NULL, 116.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7553', 'US$'),
(4149, 296, 1, 6, 52, 'ARO PDW W-7576 17X8 5X114.3 MM M/B', NULL, NULL, 'ARO321', NULL, NULL, 99.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7576', 'US$'),
(4150, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG0817 18X8.0 5X114 .3 MB- M FACE', NULL, NULL, 'ARO320', NULL, NULL, 120.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG0817', 'US$'),
(4151, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG1706 17X7.5 ET3 5X114.3 MB', NULL, NULL, 'ARO319', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG1706', 'US$'),
(4152, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-28929 17X7.5 ET0 5X114.3 MB', NULL, NULL, 'ARO318', NULL, NULL, 100.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '28929', 'US$'),
(4153, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-1730 17X7 5 30 5X114.3 B-M', NULL, NULL, 'ARO317', NULL, NULL, 105.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1730', 'US$'),
(4154, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-58705 14X6.0 ET0 4X100 B-M', NULL, NULL, 'ARO316', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '58705', 'US$'),
(4155, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-58426 14X6 ET30 4X100+114.3 B-M', NULL, NULL, 'ARO315', NULL, NULL, 51.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '58426', 'US$'),
(4156, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG0304 14X6.0 ET 4X100+1114.3 B-M', NULL, NULL, 'ARO314', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '32803', 'US$'),
(4157, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG0304 14X6.0 ET30 4X100+114.3', NULL, NULL, 'ARO313', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG0304', 'US$'),
(4158, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG0603 14X6.0 ET0 4X100+114.3 B-M', NULL, NULL, 'ARO312', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG0603', 'US$'),
(4159, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.50-16 DEL CAMINERA 12PR', NULL, NULL, 'LH0693', NULL, NULL, 553.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4160, 272, 1, 6, 55, 'LLANTA INSA TURBO 195/70R15 ECOLINE 8PR', NULL, NULL, 'LH0692', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R15', 'US$'),
(4161, 272, 1, 6, 55, 'LLANTA INSA TURBO195/60R14 SPORT 88H', NULL, NULL, 'LH0691', NULL, NULL, 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'US$'),
(4162, 290, 1, 6, 55, 'LLANTA TRIANGLE 185/70R13 TE301 86T', NULL, NULL, 'LH0689', NULL, NULL, 36.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(4163, 331, 1, 6, 55, 'LLANTA DEESTONE 265/70R17 AT PAYAK', NULL, NULL, 'LH0688', NULL, NULL, 132, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'US$'),
(4164, 331, 1, 6, 55, 'LLANTA DEESTONE 265/70R16 AT PAYAK', NULL, NULL, 'LH0687', NULL, NULL, 127.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(4165, 331, 1, 6, 55, 'LLANTA DEESTONE 215/70R16 HT PAYAK', NULL, NULL, 'LH0686', NULL, NULL, 73.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'US$'),
(4166, 331, 1, 6, 55, 'LLANTA DEESTONE 205/45R17 88W XL R702', NULL, NULL, 'LH0685', NULL, NULL, 59.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/45R17', 'US$'),
(4167, 331, 1, 6, 55, 'LLANTA DEESTONE 205/50ZR16 R202 75T', NULL, NULL, 'LH0684', NULL, NULL, 48.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'US$'),
(4168, 331, 1, 6, 55, 'LLANTA DEESTONE 155/70R13 R202 75T TL', NULL, NULL, 'LH0683', NULL, NULL, 36.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R13', 'US$'),
(4169, 394, 1, 6, 55, 'LLANTA GALAXY BEEFY BABY 10-16.5 10PR R4 OTR UND', NULL, NULL, 'LH0682', NULL, NULL, 139.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10-16.5 10PR', 'US$'),
(4170, 281, 1, 6, 55, 'LLANTA DUNLOP 175/65R14 SP10 82T', NULL, NULL, 'LH0680', NULL, NULL, 150, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'S/.'),
(4171, 393, 1, 6, 53, 'BATERIA ETNA HL-11 DE 11 PLACAS', NULL, NULL, 'BAT107', NULL, NULL, 201.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'HL-11', 'S/.'),
(4172, 393, 1, 6, 53, 'BATERIA ETNA HL-09 DE 9 PLACAS', NULL, NULL, 'BAT106', NULL, NULL, 171.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'HL-09', 'S/.'),
(4173, 385, 1, 6, 55, 'LLANTA NANKANG 205/50R16 NS-2 87V', NULL, NULL, 'LH0677', NULL, NULL, 64.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'US$'),
(4174, 385, 1, 6, 55, 'LLANTA NANKANG 225/45ZR18 95W NS-2 XLL', NULL, NULL, 'LH0676', NULL, NULL, 84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/45R17', 'US$'),
(4175, 385, 1, 6, 55, 'LLANTA NANKANG 215/40R17 NS-2 87V', NULL, NULL, 'LH0675', NULL, NULL, 66.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/40R17', 'US$'),
(4176, 385, 1, 6, 55, 'LLANTA NANKANG 205/60R14 NS-2 92H', NULL, NULL, 'LH0673', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'US$'),
(4177, 292, 1, 6, 55, 'LLANTA ORNET 12.00R20 TRAC OR517 20PR MINERA', NULL, NULL, 'LH0672', NULL, NULL, 278.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(4178, 291, 1, 6, 55, 'LLANTA HIFLY 195R15 SUPER2000 106/104R 8PR TL', NULL, NULL, 'LH0671', NULL, NULL, 51.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'US$'),
(4179, 357, 1, 6, 55, 'LLANTA SUNFULL 195/70R15 SF05 8PR', NULL, NULL, 'LH0670', NULL, NULL, 49.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R15', 'US$'),
(4180, 291, 1, 6, 55, 'LLANTA HIFLY 205/70R15 HT HF201', NULL, NULL, 'LH0669', NULL, NULL, 45.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'US$'),
(4181, 291, 1, 6, 55, 'LLANTA HIFLY 205/60R15 HF201 91V', NULL, NULL, 'LH0668', NULL, NULL, 40.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'US$'),
(4182, 293, 1, 6, 55, 'LLANTA BARUM 225/70R16 HT BRAVURIS 103H', NULL, NULL, 'LH0667', NULL, NULL, 92.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'US$'),
(4183, 291, 1, 6, 55, 'LLANTA HIFLY 185/70R13 HF201 86H', NULL, NULL, 'LH0666', NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(4184, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-85215 15X8.0 ET0 5X114.3 BM', NULL, NULL, 'ARO311', NULL, NULL, 74.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '852', 'US$'),
(4185, 296, 1, 6, 52, 'ARO PDW R 4509124M/B 14X6 ET35 4H100 73.1 M/B', NULL, NULL, 'ARO310', NULL, NULL, 56.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4509', 'US$'),
(4186, 296, 1, 6, 52, 'ARO PDW W-465749MB 14X6 ET35 4H100 73.1 M/B', NULL, NULL, 'ARO309', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4657', 'US$'),
(4187, 296, 1, 6, 52, 'ARO PDW W-425814MB 14X5.5 ET35 4H100 73.1 M/B', NULL, NULL, 'ARO307', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4258', 'US$'),
(4188, 296, 1, 6, 52, 'ARO PDW W-4905814UB 14X6 ET32 4H100 73.1 UB', NULL, NULL, 'ARO306', NULL, NULL, 55.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4905', 'US$'),
(4189, 296, 1, 6, 52, 'ARO PDW W-4520715 14X6.5 ET20 4H100.0 M/B', NULL, NULL, 'ARO305', NULL, NULL, 52.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4520715', 'US$'),
(4190, 296, 1, 6, 52, 'ARO PDW W-320625 13X6 ET70 4X114.3 M/B', NULL, NULL, 'ARO304', NULL, NULL, 47.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3206', 'US$'),
(4191, 296, 1, 6, 52, 'ARO PDW W-365744MB 13X5.5 ET35 4H100 73.1 M/B', NULL, NULL, 'ARO303', NULL, NULL, 49.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3657', 'US$'),
(4192, 296, 1, 6, 52, 'ARO PDW W-325812MB13X5.5 ET35 4H100 73.1 M/B', NULL, NULL, 'ARO302', NULL, NULL, 47.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3258', 'US$'),
(4193, 392, 1, 6, 52, 'ARO MAYHEM 8010-7837 17X8.0 6X135+139.7 MB-B', NULL, NULL, 'ARO301', NULL, NULL, 124.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8010', 'US$'),
(4194, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-37517 16X8.0 ET10 6X139.7 BM-R', NULL, NULL, 'ARO300', NULL, NULL, 84.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3751', 'US$'),
(4195, 296, 1, 6, 52, 'ARO PDW W-6701045 16X8 ET10 8X100+114.3 U-B', NULL, NULL, 'ARO299', NULL, NULL, 80.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6701', 'US$'),
(4196, 296, 1, 6, 52, 'ARO PDW W-6600943 16X8 ET-0 6H/139.7 mmUB', NULL, NULL, 'ARO298', NULL, NULL, 95.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6600', 'US$'),
(4197, 391, 1, 6, 52, 'ARO BLACK/MACHINED BLIP ION174-6883 R 16X8 ET-5 6H 139.7 108mm LIP (', NULL, NULL, 'ARO297', NULL, NULL, 99.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '174', 'US$'),
(4198, 391, 1, 6, 52, 'ARO ION WHEELS 194-5895 15X8 BLACK/MACHINEDET-27 5-6H139.7 LIP', NULL, NULL, 'ARO296', NULL, NULL, 96.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '194', 'US$'),
(4199, 296, 1, 6, 52, 'ARO PDW W-5603225 15X8 ET0 5H139.7 108 M/U4B', NULL, NULL, 'ARO295', NULL, NULL, 93.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5603', 'US$'),
(4200, 359, 1, 6, 55, 'LLANTA SPORTRAK 12.00R20 POS. SP909 P.OSO', NULL, NULL, 'LH0665', NULL, NULL, 264, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(4201, 317, 1, 6, 55, 'LLANTA GOOD YEAR 6.50-13 SUPERCUCHO 8 TAXI', NULL, NULL, 'LH0664', NULL, NULL, 164.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-13', 'S/.'),
(4202, 389, 1, 6, 53, 'BATERIA MOTO YUASA 12N5-3B 12V', NULL, NULL, 'BAT105', NULL, NULL, 83.69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N5-3B', 'S/.'),
(4203, 390, 1, 6, 53, 'BATERIA MOTO KOYO 12N5-3B 12V', NULL, NULL, 'BAT104', NULL, NULL, 72.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N5-3B', 'S/.'),
(4204, 313, 1, 6, 55, 'LLANTA GENERAL 225/70R16 AT GRABBER', NULL, NULL, 'LH0662', NULL, NULL, 110.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'US$'),
(4205, 313, 1, 6, 55, 'LLANTA GENERAL 7.00-15 DEL POWER JET 10PR', NULL, NULL, 'LH0661', NULL, NULL, 109.69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-15', 'US$'),
(4206, 310, 1, 6, 55, 'LLANTA MARSHALL 205/50R15 MUI1 86V', NULL, NULL, 'LH0656', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'US$'),
(4207, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/65R17 AT DUELER D693', NULL, NULL, 'LH0655', NULL, NULL, 210, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'US$'),
(4208, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1156 14X6.0 ET40 114.3X5H B-P', NULL, NULL, 'ARO294', NULL, NULL, 67.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1156', 'S/.'),
(4209, 365, 1, 6, 53, 'BATERIA CAPSA 13TOI PREMIUM', NULL, NULL, 'BAT103', NULL, NULL, 250.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '13TOI', 'S/.'),
(4210, 359, 1, 6, 55, 'LLANTA SPORTRAK 12.00R24 MIX BS28 20PR', NULL, NULL, 'LH0654', NULL, NULL, 249.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R24', 'US$'),
(4211, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 285/65R17 AT DUELER', NULL, NULL, 'LH0653', NULL, NULL, 232.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '285/65R17', 'US$'),
(4212, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-299 18X8.5 ET25 114.3X5H B-MF', NULL, NULL, 'ARO293', NULL, NULL, 117, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '299', 'US$'),
(4213, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-149 18X8.0 ET30 114.3X5H (73.1) B/MF', NULL, NULL, 'ARO292', NULL, NULL, 117, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '149', 'US$'),
(4214, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-393 16X7.5 ET38 100X4 (73.1/66) H/S', NULL, NULL, 'ARO291', NULL, NULL, 93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '393', 'US$'),
(4215, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-253 16X7 ET40 4X100.0 HS', NULL, NULL, 'ARO290', NULL, NULL, 95.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '253', 'US$'),
(4216, 305, 1, 6, 52, 'ARO ZEHLENDOR WHEELS ZH-115 16X7 ET38 8X100+114.3', NULL, NULL, 'ARO289', NULL, NULL, 97.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '115', 'US$'),
(4217, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-114 17X9.0 ET0 139.7X6H B/MF', NULL, NULL, 'ARO288', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1143', 'US$'),
(4218, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-115 15X7 ET35 8X100+114.3 BK/OJW', NULL, NULL, 'ARO287', NULL, NULL, 90.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '115', 'US$'),
(4219, 390, 1, 6, 53, 'BATERIA MOTO KOYO YTX5L-BS 12V', NULL, NULL, 'BAT102', NULL, NULL, 90.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX5L-BS', 'S/.'),
(4220, 389, 1, 6, 53, 'BATERIA MOTO YUASA YTX5L-BS 12V', NULL, NULL, 'BAT101', NULL, NULL, 123.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX5L-BS', 'S/.'),
(4221, 366, 1, 6, 55, 'LLANTA HABILEAD 215/65R16 ST DURABLEMAX', NULL, NULL, 'LH0652', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/65R16', 'US$'),
(4222, 366, 1, 6, 55, 'LLANTA HABILEAD 195/70R15 R301 8PR', NULL, NULL, 'LH0651', NULL, NULL, 49.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R15', 'US$'),
(4223, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.00-15 10PR PANTANERA', NULL, NULL, 'LH0650', NULL, NULL, 120.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-15', 'US$'),
(4224, 388, 1, 6, 53, 'BATERIA BOSCH 95D31L 17 PLACAS GRANDE', NULL, NULL, 'BAT100', NULL, NULL, 163.55, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '95D31L', 'US$'),
(4225, 388, 1, 6, 53, 'BATERIA BOSCH S545D 11 PLACAS CHATITA', NULL, NULL, 'BAT099', NULL, NULL, 138.77, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'S545D', 'US$'),
(4226, 388, 1, 6, 53, 'BATERIA BOSCH 80D26L 13 PLACAS STANDAR', NULL, NULL, 'BAT098', NULL, NULL, 128.86, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '80D26L', 'US$'),
(4227, 388, 1, 6, 53, 'BATERIA BOSCH 46B24L 11 PLACAS TOYOTA', NULL, NULL, 'BAT097', NULL, NULL, 100.77, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '46B24L', 'US$'),
(4228, 388, 1, 6, 53, 'BATERIA BOSCH 59218 92AH', NULL, NULL, 'BAT096', NULL, NULL, 147.03, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '59218', 'US$'),
(4229, 388, 1, 6, 53, 'BATERIA BOSCH 40B19L 11 PLACAS TICO', NULL, NULL, 'BAT095', NULL, NULL, 70.98, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '40B19L', 'US$'),
(4230, 369, 1, 6, 55, 'LLANTA COMPASAL 285/70R17 MT VERSANT', NULL, NULL, 'LH671', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '285/70R17', 'US$'),
(4231, 291, 1, 6, 55, 'LLANTA HIFLY 205/55R15 HF805 88V', NULL, NULL, 'LH669', NULL, NULL, 43.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R15', 'US$'),
(4232, 284, 1, 6, 55, 'LLANTA ARMOUR 14.00-24 16PR L2 S/C', NULL, NULL, 'LH668', NULL, NULL, 408, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '14.00-24', 'US$'),
(4233, 281, 1, 6, 55, 'LLANTA DUNLOP 185/60R15 SP2030 84H', NULL, NULL, 'LH667', NULL, NULL, 52.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R15', 'US$'),
(4234, 288, 1, 6, 55, 'LLANTA ADVANCE14-17.514PR L-4A TL', NULL, NULL, 'LH666', NULL, NULL, 383.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '14-17.5', 'US$'),
(4235, 379, 1, 6, 53, 'BATERIA DAEWOO MF55B24L 11 PLACAS TOYOTA', NULL, NULL, 'BAT094', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55B24L', 'S/.'),
(4236, 387, 1, 6, 55, 'LLANTA GOALSTAR 235/55R19 BLAZER 105V', NULL, NULL, 'LH665', NULL, NULL, 91.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/55R19', 'US$'),
(4237, 386, 1, 6, 55, 'LLANTA SUNWIDE 265/70R16 AT DURELOVE', NULL, NULL, 'LH663', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(4238, 386, 1, 6, 55, 'LLANTA SUNWIDE 155/65R13 ROLIT6 73T', NULL, NULL, 'LH661', NULL, NULL, 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/65R13', 'US$'),
(4239, 386, 1, 6, 55, 'LLANTA SUNWIDE 175/70R13 ROLIT6 85T', NULL, NULL, 'LH660', NULL, NULL, 24.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(4240, 310, 1, 6, 55, 'LLANTA MARSHALL 215/65R16 HT GRUGEN', NULL, NULL, 'LH659', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/65R16', 'US$'),
(4241, 275, 1, 6, 55, 'LLANTA TOYO TYRES 245/70R16 AT OPEN COUNTRY', NULL, NULL, 'LH658', NULL, NULL, 162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R16', 'US$'),
(4242, 310, 1, 6, 55, 'LLANTA MARSHALL 245/65R17 HT KL21 107S', NULL, NULL, 'LH657', NULL, NULL, 141.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/65R17', 'US$'),
(4243, 373, 1, 6, 55, 'LLANTA COOPER 235/70R16 AT DISCOVERER', NULL, NULL, 'LH655', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/70R16', 'US$'),
(4244, 275, 1, 6, 55, 'LLANTA TOYO TYRES 265/70R17 AT OPEN COUNTRY', NULL, NULL, 'LH654', NULL, NULL, 182.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'US$'),
(4245, 373, 1, 6, 55, 'LLANTA COOPER 265/70R16 AT DISCOVERER', NULL, NULL, 'LH653', NULL, NULL, 170.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(4246, 291, 1, 6, 55, 'LLANTA HIFLY 235/75R15 AT VIGOROUS', NULL, NULL, 'LH652', NULL, NULL, 63.11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'US$'),
(4247, 357, 1, 6, 55, 'LLANTA SUNFULL 235/75R15 AT AT782', NULL, NULL, 'LH651', NULL, NULL, 68.57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'US$'),
(4248, 357, 1, 6, 55, 'LLANTA SUNFULL 215/45R17 SF886 91Y', NULL, NULL, 'LH650', NULL, NULL, 47.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'US$'),
(4249, 291, 1, 6, 55, 'LLANTA HIFLY 155/65R13 HF201 73T', NULL, NULL, 'LH649', NULL, NULL, 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/65R13', 'US$'),
(4250, 285, 1, 6, 55, 'LLANTA GOODRIDE 215/45R17 SV308 91W', NULL, NULL, 'LH647', NULL, NULL, 55.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'US$'),
(4251, 285, 1, 6, 55, 'LLANTA GOODRIDE 245/75R16 AT RADIAL', NULL, NULL, 'LH646', NULL, NULL, 104.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4252, 377, 1, 6, 55, 'LLANTA MICHELIN 215/65R16 AT FORCE', NULL, NULL, 'LH594', NULL, NULL, 150.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/65R16', 'US$'),
(4253, 359, 1, 6, 55, 'LLANTA SPORTRAK 12.00R20 POS. BYD865 MINERA', NULL, NULL, 'LH592', NULL, NULL, 300, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(4254, 313, 1, 6, 55, 'LLANTA GENERAL 7.50-16 POS AMERI DCL 14PR', NULL, NULL, 'LH591', NULL, NULL, 182.02, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4255, 313, 1, 6, 55, 'LLANTA GENERAL 255/55R18 AT GRABBER', NULL, NULL, 'LH590', NULL, NULL, 153.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/55R18', 'US$'),
(4256, 313, 1, 6, 55, 'LLANTA GENERAL 225/60R17 HT GRABBER 99H', NULL, NULL, 'LH589', NULL, NULL, 105.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/60R17', 'US$'),
(4257, 313, 1, 6, 55, 'LLANTA GENERAL 235/65R17 AT GRABBER', NULL, NULL, 'LH587', NULL, NULL, 114.35, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(4258, 336, 1, 6, 55, 'LLANTA MAXTREK 265/70R16 AT SU 800', NULL, NULL, 'LH586', NULL, NULL, 88.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(4259, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 7.50R16 DEL', NULL, NULL, 'LH585', NULL, NULL, 699.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'US$'),
(4260, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 215/75R14 AT SPORT', NULL, NULL, 'LH584', NULL, NULL, 102.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R14', 'US$'),
(4261, 317, 1, 6, 55, 'LLANTA GOOD YEAR 225/75R16 MT WRANGLER', NULL, NULL, 'LH583', NULL, NULL, 194.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'US$'),
(4262, 375, 1, 6, 53, 'BATERIA SOLITE CMF55B24L 11 PLACAS TOYOTA', NULL, NULL, 'BAT093', NULL, NULL, 259.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55B24L', 'S/.'),
(4263, 313, 1, 6, 55, 'LLANTA GENERAL 255/65R16 AT GRABBER AT2', NULL, NULL, 'LH0659', NULL, NULL, 144.33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/65R16', 'US$'),
(4264, 338, 1, 6, 55, 'LLANTA PIRELLI 285/70R17 MT SCORPION', NULL, NULL, 'LH04358', NULL, NULL, 216.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '285/70R17', 'US$'),
(4265, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-40108 13X5.5 ET35 73.1 4X100 B-M', NULL, NULL, 'ARO283', NULL, NULL, 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4010', 'US$'),
(4266, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-501704 18X8.0 ET30 73.1 5X114.3 MS-B3', NULL, NULL, 'ARO282', NULL, NULL, 112.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5017', 'US$'),
(4267, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-53114 20X8.5 ET40 5HX114.3 C-H', NULL, NULL, 'ARO281', NULL, NULL, 183.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '531', 'US$'),
(4268, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG0306 16X7.0 5X100+114.3', NULL, NULL, 'ARO280', NULL, NULL, 84.61, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG03', 'US$'),
(4269, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-26901 16X7.5 30 73.1 5X114.3 ML-TB', NULL, NULL, 'ARO279', NULL, NULL, 83.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '26901', 'US$'),
(4270, 296, 1, 6, 52, 'ARO PDW W-59033832 15X6.5 ET33 8HX100+108 MI-B', NULL, NULL, 'ARO278', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '590338', 'US$'),
(4271, 296, 1, 6, 52, 'ARO PDW W-4905812 14X6.0 ET32 4H100.0 W-KL', NULL, NULL, 'ARO277', NULL, NULL, 54.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '49058', 'US$'),
(4272, 296, 1, 6, 52, 'ARO PDW W-480016 14X6.5 ET20 4H100.0 M-B', NULL, NULL, 'ARO276', NULL, NULL, 61.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '48001', 'US$'),
(4273, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-1992 14X5.5 ET35 4X100+114.3 M-B', NULL, NULL, 'ARO275', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1992', 'US$'),
(4274, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-86016 14X6.0 ET40 4X100 B-M', NULL, NULL, 'ARO273', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '86016', 'US$'),
(4275, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-71509 14X5.5 40 73.1 4X100+114.3 B-M', NULL, NULL, 'ARO272', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '715', 'US$'),
(4276, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-63628 14X5.5 35 73.1 4X100 LI-P', NULL, NULL, 'ARO271', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '636', 'US$'),
(4277, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6020 14X6.0 ET35 4X100+114.3 M-B', NULL, NULL, 'ARO270', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '60209', 'US$'),
(4278, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-27909 14X6.0 40 73.1 4X100 M-B', NULL, NULL, 'ARO269', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '279', 'US$'),
(4279, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-21610 14X6.0 ET40 73.1 4X100 B-M', NULL, NULL, 'ARO268', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '216', 'US$'),
(4280, 281, 1, 6, 55, 'LLANTA DUNLOP 205/55R15 DZ102 88V', NULL, NULL, 'LH04109', NULL, NULL, 56.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R15', 'US$'),
(4281, 385, 1, 6, 55, 'LLANTA NANKANG 235/45R17 NS2 94V TL', NULL, NULL, 'LH04108', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/45R17', 'US$'),
(4282, 288, 1, 6, 55, 'LLANTA ADVANCE 12-16.5 12PR NHS L-2B TL', NULL, NULL, 'LH04107', NULL, NULL, 142.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12-16.5', 'US$'),
(4283, 289, 1, 6, 55, 'LLANTA OTANI 6.50-14 POS S78 10PR', NULL, NULL, 'LH04106', NULL, NULL, 81.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(4284, 384, 1, 6, 55, 'LLANTA BOXER 23.5-25 20PR E-3/L-3 TCF', NULL, NULL, 'LH04105', NULL, NULL, 820.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '23.5-25', 'US$'),
(4285, 290, 1, 6, 55, 'LLANTA TRIANGLE 20.5-25 20PR TL612 E-3/L-3 TCF', NULL, NULL, 'LH04104', NULL, NULL, 925.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '20.5-25', 'US$'),
(4286, 366, 1, 6, 55, 'LLANTA HABILEAD 245/75R16 AT PRACTICALMAX', NULL, NULL, 'LH04102', NULL, NULL, 87.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4287, 366, 1, 6, 55, 'LLANTA HABILEAD 195/60R15 RS23 88H', NULL, NULL, 'LH04101', NULL, NULL, 57.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'US$'),
(4288, 366, 1, 6, 55, 'LLANTA HABILEAD 185/70R14 H202 88H', NULL, NULL, 'LH04100', NULL, NULL, 34.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4289, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-1073 13X5.5 ET30 100+114.3X8H B-MF', NULL, NULL, 'ARO267', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1073', 'US$'),
(4290, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-1085 13X5 ET30 100+114 .3X8H B-MF', NULL, NULL, 'ARO266', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1085', 'US$'),
(4291, 371, 1, 6, 53, 'BATERIA ALFA AW-11 MAXIMA DURACION', NULL, NULL, 'BAT092', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AW-11', 'S/.'),
(4292, 371, 1, 6, 53, 'BATERIA ALFA AW-09 MAXIMA DURACION', NULL, NULL, 'BAT091', NULL, NULL, 171.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AW-09', 'S/.'),
(4293, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-562 14X6.0 ET30 100X4H B-MF', NULL, NULL, 'ARO265', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '562', 'US$'),
(4294, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1253 14X5.5 ET40 100+114.3X8H B4', NULL, NULL, 'ARO264', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1253', 'US$'),
(4295, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-466 14X6.0 ET35 100+108X8H B-MF', NULL, NULL, 'ARO263', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '466', 'US$'),
(4296, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-524 14X7.0 ET-9 114.3X5H LA5-B', NULL, NULL, 'ARO262', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '524', 'US$'),
(4297, 347, 1, 6, 55, 'LLANTA TECHKING 11R22.5 DEL TKST II 16PR', NULL, NULL, 'LH0642', NULL, NULL, 258, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11R22.5', 'S/.'),
(4298, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 7.50-16 PAN CORDILLERA 10PR', NULL, NULL, 'LH0641', NULL, NULL, 468, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'S/.'),
(4299, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 7.50-16 POS TD-440 14PR', NULL, NULL, 'LH0639', NULL, NULL, 462, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'S/.'),
(4300, 276, 1, 6, 55, 'LLANTA LINGLONG 7.50R16 DEL LLF26 14PR', NULL, NULL, 'LH0638', NULL, NULL, 171.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'US$'),
(4301, 346, 1, 6, 55, 'LLANTA DURATREAD 7.50-16 POS POWER LUG 16PR', NULL, NULL, 'LH0634', NULL, NULL, 162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4302, 320, 1, 6, 55, 'LLANTA TEXXAN 7.00-16 POS LV-712 XL 14PR', NULL, NULL, 'LH0633', NULL, NULL, 138, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-16', 'US$'),
(4303, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 7.50-15 POS PIONERA 14PR', NULL, NULL, 'LH0632', NULL, NULL, 570, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-15', 'S/.'),
(4304, 347, 1, 6, 55, 'LLANTA TECHKING 7.50R16 POS TKAM 14PR', NULL, NULL, 'LH0630', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'S/.'),
(4305, 345, 1, 6, 55, 'LLANTA STAMINA ALTURA POS 8.25-16 16PR', NULL, NULL, 'LH0629', NULL, NULL, 191.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'US$'),
(4306, 383, 1, 6, 55, 'LLANTA KAIZEN 7.50-16 DEL KZ-R001 16PR', NULL, NULL, 'LH0628', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4307, 287, 1, 6, 55, 'LLANTA GOODTYRE 7.50R16 POS YB228 14PR', NULL, NULL, 'LH0627', NULL, NULL, 171.79, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'S/.'),
(4308, 322, 1, 6, 55, 'LLANTA WESTLAKE 8.25R16 POS CB 981 16PR', NULL, NULL, 'LH0626', NULL, NULL, 219.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'US$'),
(4309, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.50-15 DEL HI MILER CT176', NULL, NULL, 'LH0625', NULL, NULL, 522.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-15', 'US$'),
(4310, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.00-16 POS CHASQUI 115/110J', NULL, NULL, 'LH0624', NULL, NULL, 463.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-16', 'US$'),
(4311, 383, 1, 6, 55, 'LLANTA KAIZEN 7.50-16 POS KZ-L002 16PR', NULL, NULL, 'LH0619', NULL, NULL, 132, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4312, 318, 1, 6, 55, 'LLANTA FIRESTONE 215/80R16 AT DESTINATION', NULL, NULL, 'LH0618', NULL, NULL, 109.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/80R16', 'US$'),
(4313, 318, 1, 6, 55, 'LLANTA FIRESTONE 205R16 AT DESTINATION', NULL, NULL, 'LH0617', NULL, NULL, 134.61, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205R16', 'US$'),
(4314, 318, 1, 6, 55, 'LLANTA FIRESTONE 205/50R16 FIREHAWK 87V', NULL, NULL, 'LH0615', NULL, NULL, 158.65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'US$'),
(4315, 313, 1, 6, 55, 'LLANTA GENERAL 195/55R15 85H ALTIMAX HP', NULL, NULL, 'LH0614', NULL, NULL, 53.71, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/55R15', 'US$'),
(4316, 350, 1, 6, 55, 'LLANTA ANTARES 225/75R16 AT 118/111S SMT A7', NULL, NULL, 'LH0613', NULL, NULL, 119.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'US$'),
(4317, 382, 1, 6, 55, 'LLANTA TRAILCUTER 245/75R16 MT RTX', NULL, NULL, 'LH0612', NULL, NULL, 185.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4318, 332, 1, 6, 55, 'LLANTA CATCHGRE 175/65R14 WINDFORSE 82H', NULL, NULL, 'LH0611', NULL, NULL, 41.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'US$'),
(4319, 310, 1, 6, 55, 'LLANTA MARSHALL 255/70R16 AT ROAD VENTURE', NULL, NULL, 'LH0609', NULL, NULL, 147.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/70R16', 'US$'),
(4320, 322, 1, 6, 55, 'LLANTA WESTLAKE 145R12 H200 LOAD RANGE', NULL, NULL, 'LH0608', NULL, NULL, 31.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '145R12', 'US$'),
(4321, 381, 1, 6, 55, 'LLANTA LAUFEN 185/65R14 GFITAS 86H', NULL, NULL, 'LH0606', NULL, NULL, 43.54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'US$'),
(4322, 275, 1, 6, 55, 'LLANTA TOYO TYRES 175/70R14 OP350 84T', NULL, NULL, 'LH0605', NULL, NULL, 83.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'US$'),
(4323, 275, 1, 6, 55, 'LLANTA TOYO TYRES 175/65R13 OP350 82T', NULL, NULL, 'LH0604', NULL, NULL, 56.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R13', 'US$'),
(4324, 336, 1, 6, 55, 'LLANTA MAXTREK 215/65R16 ST MK700', NULL, NULL, 'LH0603', NULL, NULL, 70.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/65R16', 'US$'),
(4325, 336, 1, 6, 55, 'LLANTA MAXTREK 215/45R17 INGENS A1 91W', NULL, NULL, 'LH0602', NULL, NULL, 61.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'US$'),
(4326, 280, 1, 6, 55, 'LLANTA HANKOOK 165/60R14 75T OPTIMO K715', NULL, NULL, 'LH0599', NULL, NULL, 48.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/60R14', 'US$'),
(4327, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 195/60R16 89H B250', NULL, NULL, 'LH0597', NULL, NULL, 152.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R16', 'US$'),
(4328, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 205/55R16 POTENZA 90H', NULL, NULL, 'LH0596', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'US$'),
(4329, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 235/70R16 AT DUELER', NULL, NULL, 'LH0595', NULL, NULL, 158.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/70R16', 'US$'),
(4330, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 205R16 AT DUELER D694', NULL, NULL, 'LH0594', NULL, NULL, 192, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205R16', 'US$'),
(4331, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 235/65R17 HT DUELER 108V', NULL, NULL, 'LH0592', NULL, NULL, 168, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(4332, 272, 1, 6, 55, 'LLANTA INSA TURBO 265/70R16 AT MOUNTAIN', NULL, NULL, 'LH0591', NULL, NULL, 158.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(4333, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 225/65R17 AT DUELER', NULL, NULL, 'LH0390', NULL, NULL, 141.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'US$'),
(4334, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/75R16 MT DUELER D673', NULL, NULL, 'LH0389', NULL, NULL, 204, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(4335, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 215/75R15 AT DUELER D694', NULL, NULL, 'LH0589', NULL, NULL, 140.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'US$'),
(4336, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 7.50R16 DEL. RIB 230', NULL, NULL, 'LH0588', NULL, NULL, 164.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'US$'),
(4337, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 185/65R14 GIII', NULL, NULL, 'LH0586', NULL, NULL, 73.19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'US$'),
(4338, 380, 1, 6, 55, 'LLANTA MASSTEK 185/65R14 TEXTIL 86S', NULL, NULL, 'LH0585', NULL, NULL, 59.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'US$'),
(4339, 379, 1, 6, 53, 'BATERIA DAEWOO MF60D31L 11 PLACAS GRANDE', NULL, NULL, 'BAT089', NULL, NULL, 83.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '60D31L', 'US$'),
(4340, 379, 1, 6, 53, 'BATERIA DAEWOO MF75D26L 13 PLACAS CUADRADA', NULL, NULL, 'BAT088', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '75D26L', 'US$'),
(4341, 379, 1, 6, 53, 'BATERIA DAEWOO MF56077 13 PLACAS CHATA', NULL, NULL, 'BAT087', NULL, NULL, 79.15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '56077', 'US$'),
(4342, 379, 1, 6, 53, 'BATERIA DAEWOO MF55046 11 PLACAS CHATA', NULL, NULL, 'BAT086', NULL, NULL, 64.93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55046', 'US$'),
(4343, 375, 1, 6, 53, 'BATERIA SOLITE CMF55016 11 PLACAS CHATITA', NULL, NULL, 'BAT085', NULL, NULL, 241.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'CMF55016', 'S/.'),
(4344, 375, 1, 6, 53, 'BATERIA SOLITE CMF75D23L 13 PLACAS CUADRADA', NULL, NULL, 'BAT084', NULL, NULL, 343, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '75D23L', 'S/.'),
(4345, 375, 1, 6, 53, 'BATERIA SOLITE CMF55D26L 11 PLACAS STANDAR', NULL, NULL, 'BAT083', NULL, NULL, 276.89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55D26L', 'S/.'),
(4346, 375, 1, 6, 53, 'BATERIA SOLITE CMF55040 11 PLACAS CHATA', NULL, NULL, 'BAT082', NULL, NULL, 242.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '55040', 'S/.'),
(4347, 300, 1, 6, 52, 'ARO DARWIN RACING B-963HS 15X6.5 ET35 4X100.0', NULL, NULL, 'ARO261', NULL, NULL, 70.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '963', 'US$'),
(4348, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1291 14X5.5 ET38 100+114.3X8H B-P', NULL, NULL, 'ARO260', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1291', 'US$'),
(4349, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1144 15X6.5 ET40 100+114.3X8H H-S', NULL, NULL, 'ARO259', NULL, NULL, 73.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1144', 'US$'),
(4350, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1144 14X6.0 ET40 100+114 .3X8H H-S', NULL, NULL, 'ARO258', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1144', 'US$'),
(4351, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1519 14X5.5 ET38 100+114.3X8H B-P', NULL, NULL, 'ARO257', NULL, NULL, 58.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1519', 'US$'),
(4352, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-2047 15X6.5 ET40 100+114.3X8H B-P', NULL, NULL, 'ARO256', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2047', 'US$');
INSERT INTO `Gen_Producto` (`IdProducto`, `IdProductoMarca`, `IdProductoFormaFarmaceutica`, `IdProductoMedicion`, `IdProductoCategoria`, `Producto`, `ProductoDesc`, `ProductoDescCorto`, `CodigoBarra`, `Codigo`, `Dosis`, `PrecioContado`, `PrecioPorMayor`, `StockPorMayor`, `StockMinimo`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `ControlaStock`, `PrecioCosto`, `VentaEstrategica`, `PorcentajeUtilidad`, `IdBloque`, `Modelo`, `Moneda`) VALUES
(4353, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1253 13X5.5 ET38 100+114.3X8H HS', NULL, NULL, 'ARO255', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1253', 'US$'),
(4354, 379, 1, 6, 53, 'BATERIA DAEWOO MF44B19L 11 PLACAS TICO', NULL, NULL, 'BAT080', NULL, NULL, 52.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '44B19L', 'US$'),
(4355, 376, 1, 6, 55, 'LLANTA DURO 100/90-19 57M', NULL, NULL, 'LH0582', NULL, NULL, 76.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '100/90-19', 'S/.'),
(4356, 326, 1, 6, 55, 'LLANTA DURUN 205/50R16 A-ONE 88H', NULL, NULL, 'LH0581', NULL, NULL, 40.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'US$'),
(4357, 291, 1, 6, 55, 'LLANTA HIFLY 165/65R14 HF201 79T', NULL, NULL, 'LH0579', NULL, NULL, 28.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R14', 'US$'),
(4358, 357, 1, 6, 55, 'LLANTA SUNFULL 165/65R14 SF688 78T', NULL, NULL, 'LH0578', NULL, NULL, 28.57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R14', 'US$'),
(4359, 326, 1, 6, 55, 'LLANTA DURUN 175/70R13 A2000 82T', NULL, NULL, 'LH0576', NULL, NULL, 29.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(4360, 326, 1, 6, 55, 'LLANTA DURUN 185/70R13 D104 86T', NULL, NULL, 'LH0575', NULL, NULL, 35.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(4361, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-356 15X6.5 ET40 100+114.3X8H B-P', NULL, NULL, 'ARO254', NULL, NULL, 71.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '356', 'US$'),
(4362, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-395 15X7.0 ET40 100+114.3X8H BP-M', NULL, NULL, 'ARO253', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '395', 'US$'),
(4363, 375, 1, 6, 53, 'BATERIA SOLITE CMF105D31L 15 PLACAS GRANDE', NULL, NULL, 'BAT079', NULL, NULL, 423.79, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '105D31L', 'S/.'),
(4364, 378, 1, 6, 53, 'BATERIA MOTO MGM YTX5L-BS 12V', NULL, NULL, 'BAT078', NULL, NULL, 54.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX5L-BS', 'S/.'),
(4365, 378, 1, 6, 53, 'BATERIA MOTO MGM YB7B-B12V', NULL, NULL, 'BAT077', NULL, NULL, 57.33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YB7B-B', 'S/.'),
(4366, 378, 1, 6, 53, 'BATERIA MOTO MGM 6N6-3B 12V', NULL, NULL, 'BAT076', NULL, NULL, 48.58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6N6-3B', 'S/.'),
(4367, 378, 1, 6, 53, 'BATERIA MOTO MGM 12N9-3B 12V', NULL, NULL, 'BAT075', NULL, NULL, 74.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N9-3B', 'US$'),
(4368, 378, 1, 6, 53, 'BATERIA MOTO MGM 12N7-3B 12V', NULL, NULL, 'BAT074', NULL, NULL, 62.65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N7-3B', 'US$'),
(4369, 378, 1, 6, 53, 'BATERIA MOTO MGM YTX7L-BS 12V', NULL, NULL, 'BAT073', NULL, NULL, 73.65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX7L-BS', 'US$'),
(4370, 378, 1, 6, 53, 'BATERIA MOTO MGM YTX7A -BS 12V', NULL, NULL, 'BAT072', NULL, NULL, 67.33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX7A-BS', 'US$'),
(4371, 378, 1, 6, 53, 'BATERIA MOTO MGM YTX4L-BS 12V', NULL, NULL, 'BAT071', NULL, NULL, 44.83, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'YTX4L-BS', 'US$'),
(4372, 378, 1, 6, 53, 'BATERIA MOTO MGM 12N7A-3A 12V', NULL, NULL, 'BAT070', NULL, NULL, 62.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N7A-3A', 'US$'),
(4373, 378, 1, 6, 53, 'BATERIA MOTO MGM 12N6-3B 12V', NULL, NULL, 'BAT069', NULL, NULL, 57.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N6-3B', 'US$'),
(4374, 378, 1, 6, 53, 'BATERIA MOTO MGM 12N5-3B 12V', NULL, NULL, 'BAT068', NULL, NULL, 46.87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12N5-3B', 'US$'),
(4375, 377, 1, 6, 55, 'LLANTA MICHELIN 140/70-17 66S PILOT STREET TL/TT', NULL, NULL, 'LH0574', NULL, NULL, 200.93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '140/70-17', 'S/.'),
(4376, 377, 1, 6, 57, 'LLANTA MICHELIN 130/70-17 62S PILOT STREET TL/TT', NULL, NULL, 'LH0573', NULL, NULL, 194.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '130/70-17', 'S/.'),
(4377, 377, 1, 6, 57, 'LLANTA MICHELIN 100/90-19 57M S/I STARCROSS MH3', NULL, NULL, 'LH0572', NULL, NULL, 234.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '100/90-19', 'S/.'),
(4378, 377, 1, 6, 57, 'LLANTA MICHELIN MX 110/90-19 M/C 62M STARCROSS MH3 TT', NULL, NULL, 'LH0571', NULL, NULL, 264.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '110/90-19', 'S/.'),
(4379, 377, 1, 6, 57, 'LLANTA MICHELIN 90/90-21 54S SIRAC TT', NULL, NULL, 'LH0570', NULL, NULL, 109.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90/90-21', 'S/.'),
(4380, 377, 1, 6, 57, 'LLANTA MICHELIN 90/90-18 57P SIRAC STREET TT', NULL, NULL, 'LH0569', NULL, NULL, 107.78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90/90-18', 'S/.'),
(4381, 377, 1, 6, 57, 'LLANTA MICHELIN 90/90-18 57P CITY PRO TT', NULL, NULL, 'LH0568', NULL, NULL, 100.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90/90-18', 'S/.'),
(4382, 377, 1, 6, 57, 'LLANTA MICHELIN 80/100-21 51M S/I STARCROSS MS3', NULL, NULL, 'LH0567', NULL, NULL, 217.57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '80/100-21', 'S/.'),
(4383, 377, 1, 6, 57, 'LLANTA MICHELIN 3.00-18 52S CITY PRO TT', NULL, NULL, 'LH0566', NULL, NULL, 102.61, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3.00-18', 'S/.'),
(4384, 377, 1, 6, 57, 'LLANTA MICHELIN 2.75-18 42P SIRAC STREET TL/TT', NULL, NULL, 'LH0565', NULL, NULL, 95.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2.75-18', 'S/.'),
(4385, 377, 1, 6, 57, 'LLANTA MICHELIN 110/70-17 54S PILOT STREET TL/TT', NULL, NULL, 'LH0564', NULL, NULL, 133.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '110/70-17', 'S/.'),
(4386, 377, 1, 6, 57, 'LLANTA MICHELIN 100/80-17 52S PILOT STREET TL/TT', NULL, NULL, 'LH0563', NULL, NULL, 128.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '100/80-17', 'S/.'),
(4387, 376, 1, 6, 57, 'LLANTA DURO 21-275 333 HF DEL XL185', NULL, NULL, 'LH0562', NULL, NULL, 76.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '21-275', 'S/.'),
(4388, 376, 1, 6, 57, 'LLANTA DURO 18-4.10 335 HF POS. XL185 (CG)', NULL, NULL, 'LH0561', NULL, NULL, 101.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '18-410', 'S/.'),
(4389, 376, 1, 6, 57, 'LLANTA DURO 18-300 336 HF POS. CG125', NULL, NULL, 'LH0560', NULL, NULL, 60.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '18-300', 'S/.'),
(4390, 376, 1, 6, 57, 'LLANTA DURO 18-300 311 HF', NULL, NULL, 'LH0559', NULL, NULL, 65.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '18-300', 'S/.'),
(4391, 376, 1, 6, 57, 'LLANTA DURO 18-300 307 HF TRAIL', NULL, NULL, 'LH0558', NULL, NULL, 58.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '18-300', 'S/.'),
(4392, 376, 1, 6, 57, 'LLANTA MOTO DURO 17-300 336 HF', NULL, NULL, 'LH0556', NULL, NULL, 58.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17-300', 'S/.'),
(4393, 376, 1, 6, 57, 'LLANTA DURO 17-300 307 HF TRAIL', NULL, NULL, 'LH0555', NULL, NULL, 58.79, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17-300', 'S/.'),
(4394, 376, 1, 6, 57, 'LLANTA DURO 17-275 303 HF', NULL, NULL, 'LH0554', NULL, NULL, 60.75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17-275', 'S/.'),
(4395, 376, 1, 6, 57, 'LLANTA DURO 17-250 303 HF C70 POS', NULL, NULL, 'LH0553', NULL, NULL, 50.23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17-250', 'S/.'),
(4396, 376, 1, 6, 57, 'LLANTA DURO 17-250 301E HF C90', NULL, NULL, 'LH0552', NULL, NULL, 49.63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17-250', 'S/.'),
(4397, 376, 1, 6, 57, 'LLANTA DURO 14-275 315 HF', NULL, NULL, 'LH0551', NULL, NULL, 48.11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '14-275', 'S/.'),
(4398, 376, 1, 6, 56, 'LLANTA DURO 90/90-19 HF-903 52P TT', NULL, NULL, 'LH0550', NULL, NULL, 75.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90/90-19', 'S/.'),
(4399, 376, 1, 6, 57, 'LLANTA DURO 90/90-19 DM-1226 4PR 52P TT', NULL, NULL, 'LH0549', NULL, NULL, 80.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90/90-19', 'S/.'),
(4400, 376, 1, 6, 57, 'LLANTA DURO 90/90-12 HF-908F 54J TL', NULL, NULL, 'LH0548', NULL, NULL, 62.95, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '90/90-12', 'S/.'),
(4401, 376, 1, 6, 57, 'LLANTA DURO 300-18 4PR HF-333', NULL, NULL, 'LH0547', NULL, NULL, 76.82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '300-18', 'S/.'),
(4402, 376, 1, 6, 57, 'LLANTA DURO 3.50-10 HF-263 4PR 51J TL', NULL, NULL, 'LH0546', NULL, NULL, 56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3.50-10', 'S/.'),
(4403, 376, 1, 6, 57, 'LLANTA DURO 18-90/90 329 HF PISTERA TL', NULL, NULL, 'LH0545', NULL, NULL, 105.97, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '18-90/90', 'S/.'),
(4404, 376, 1, 6, 57, 'LLANTA DURO 130/90-15 69P HF296C TL', NULL, NULL, 'LH0544', NULL, NULL, 211.13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '130/90-15', 'S/.'),
(4405, 376, 1, 6, 57, 'LLANTA DURO-DURO 120/90-17 64S HF904', NULL, NULL, 'LH0543', NULL, NULL, 161.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '120/90-17', 'S/.'),
(4406, 376, 1, 6, 57, 'LLANTA DURO 110/90-17 HF-904 60P TT', NULL, NULL, 'LH0542', NULL, NULL, 100.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '110/90-17', 'S/.'),
(4407, 376, 1, 6, 57, 'LLANTA DURO 110/90-17 DM-1226 4PR 60P TT', NULL, NULL, 'LH0541', NULL, NULL, 106.87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '110/90-17', 'S/.'),
(4408, 376, 1, 6, 57, 'LLANTA DURO 100/90-10 HF-290R 4PR TL', NULL, NULL, 'LH0540', NULL, NULL, 66.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '100/90-10', 'S/.'),
(4409, 376, 1, 6, 56, 'LLANTA DURO 100/100-17 58M PANTANERA DM1112', NULL, NULL, 'LH0539', NULL, NULL, 123.19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '100/100-17', 'S/.'),
(4410, 375, 1, 6, 53, 'BATERIA SOLITE CMF42B19L 11 PLACAS TICO', NULL, NULL, 'BAT067', NULL, NULL, 226.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '42B19L', 'S/.'),
(4411, 374, 1, 6, 53, 'BATERIA EXIDE MFS40 AT', NULL, NULL, 'BAT066', NULL, NULL, 219.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'MFS40', 'S/.'),
(4412, 373, 1, 6, 55, 'LLANTA COOPER 235/75R15 AT DISCOVERER', NULL, NULL, 'LH0538', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'US$'),
(4413, 373, 1, 6, 55, 'LLANTA COOPER 195/60R15 CS10 88T', NULL, NULL, 'LH0536', NULL, NULL, 56.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'US$'),
(4414, 310, 1, 6, 55, 'LLANTA MARSHALL 205/70R14 KR21 95T', NULL, NULL, 'LH0535', NULL, NULL, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R14', 'S/.'),
(4415, 373, 1, 6, 55, 'LLANTA COOPER 195/70R14 CS10 91T', NULL, NULL, 'LH0534', NULL, NULL, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R14', 'US$'),
(4416, 275, 1, 6, 55, 'LLANTA TOYO TYRES 195R14 106S HO8LTR', NULL, NULL, 'LH0533', NULL, NULL, 80.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'US$'),
(4417, 373, 1, 6, 55, 'LLANTA COOPER 175/70R14 CS1 84T', NULL, NULL, 'LH0532', NULL, NULL, 39.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'US$'),
(4418, 310, 1, 6, 55, 'LLANTA MARSHALL185/65R14 MH11 86H', NULL, NULL, 'LH0531', NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'S/.'),
(4419, 373, 1, 6, 55, 'LLANTA COOPER 185/70R14 CS10 88T', NULL, NULL, 'LH0530', NULL, NULL, 39.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4420, 373, 1, 6, 55, 'LLANTA COOPER 175/70R13 CS10 82T', NULL, NULL, 'LH0529', NULL, NULL, 32.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(4421, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 185/70R14 RE740 88T', NULL, NULL, 'LH0527', NULL, NULL, 67.54, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4422, 366, 1, 6, 55, 'LLANTA HABILEAD 185R14 RS01 102/100R 8PR DURABLEMAX', NULL, NULL, 'LH0525', NULL, NULL, 43.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185R14', 'US$'),
(4423, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-112 13X5.5 ET48 4X100.0 BP-KJ', NULL, NULL, 'ARO252', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '112', 'S/.'),
(4424, 320, 1, 6, 55, 'LLANTA TEXXAN 4.00-8 MIX 8PR AUTO STAR', NULL, NULL, 'LH0524', NULL, NULL, 21.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4.00-8', 'US$'),
(4425, 320, 1, 6, 55, 'LLANTA TEXXAN 4.00-8 POS 8PR AUTO LUG', NULL, NULL, 'LH0523', NULL, NULL, 21.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4.00-8', 'US$'),
(4426, 372, 1, 6, 55, 'LLANTA BEARWAY LT 6.00R13 M636 8PR', NULL, NULL, 'LH0522', NULL, NULL, 44.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.00R13', 'US$'),
(4427, 365, 1, 6, 53, 'BATERIA CAPSA 1247R PREMIUM', NULL, NULL, 'BAT065', NULL, NULL, 230.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1247R', 'S/.'),
(4428, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3206 14X6.0 ET33 100+114.3X8H BLK-M', NULL, NULL, 'ARO251', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3206', 'S/.'),
(4429, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-337 14X6.0 ET35 100+114.3X8H B-P', NULL, NULL, 'ARO250', NULL, NULL, 58.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '337', 'S/.'),
(4430, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-149 14X6.0 ET35 100+114.3X8H B-P', NULL, NULL, 'ARO249', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '149', 'S/.'),
(4431, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-149 13X5.5 ET35 100+114.3X8H B-P', NULL, NULL, 'ARO248', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '149', 'S/.'),
(4432, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-247 14X6.0 ET35 100+114.3X8H B-P', NULL, NULL, 'ARO247', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '247', 'S/.'),
(4433, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-348 14X6.0 ET35 100+114.3X8H B-P', NULL, NULL, 'ARO246', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '348', 'S/.'),
(4434, 346, 1, 6, 55, 'LLANTA DURATREAD 8.25-16 POS 53-D 18PR', NULL, NULL, 'LH0521', NULL, NULL, 174, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4435, 346, 1, 6, 55, 'LLANTA DURATREAD 8.25-16 DEL 54-B 18PR', NULL, NULL, 'LH0520', NULL, NULL, 174, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4436, 335, 1, 6, 55, 'LLANTA CONTINENTAL 185/65R15 POWER CONTAC 88H', NULL, NULL, 'LH0519', NULL, NULL, 55.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'S/.'),
(4437, 293, 1, 6, 55, 'LLANTA BARUM 185/70R14 BRILLANTIS 88T', NULL, NULL, 'LH0518', NULL, NULL, 41.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4438, 338, 1, 6, 55, 'LLANTA PIRELLI 185/70R13 P400 85T', NULL, NULL, 'LH0517', NULL, NULL, 39.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(4439, 371, 1, 6, 53, 'BATERIA ALFA AC- 09 MAXIMA DURACION', NULL, NULL, 'BAT064', NULL, NULL, 171.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'AC-09', 'S/.'),
(4440, 371, 1, 6, 53, 'BATERIA ALFA ANS -11 MAXIMA DURACION', NULL, NULL, 'BAT063', NULL, NULL, 183.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'ANS-11', 'S/.'),
(4441, 371, 1, 6, 53, 'BATERIA ALFA ANS- 09 MAXIMA DURACION', NULL, NULL, 'BAT062', NULL, NULL, 160.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'ANS-09', 'S/.'),
(4442, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3113 13X5.5 ET32 100+114.3X8H B-P', NULL, NULL, 'ARO245', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3113', 'US$'),
(4443, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-331 14X6.0 ET35 100+114.3X8H EM-M', NULL, NULL, 'ARO244', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '331', 'S/.'),
(4444, 302, 1, 6, 52, 'ARO INDIANA WHEELS IN-2033 14X6.0 ET40 100+114.3X8H B-4', NULL, NULL, 'ARO241', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2033', 'S/.'),
(4445, 280, 1, 6, 55, 'LLANTA HANKOOK 245/75R16 MT DYNAPRO', NULL, NULL, 'LH0515', NULL, NULL, 155.75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4446, 280, 1, 6, 55, 'LLANTA HANKOOK 265/70R16 AT DYNAPRO', NULL, NULL, 'LH0514', NULL, NULL, 144.78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(4447, 280, 1, 6, 55, 'LLANTA HANKOOK 235/60R16 HT DYNAPRO 100H', NULL, NULL, 'LH0513', NULL, NULL, 112.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R16', 'S/.'),
(4448, 280, 1, 6, 55, 'LLANTA HANKOOK 225/75R16 HT OPTIMO 102/104H', NULL, NULL, 'LH0512', NULL, NULL, 133.91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'S/.'),
(4449, 280, 1, 6, 55, 'LLANTA HANKOOK 225/60R17 HT DYNAPRO 99H', NULL, NULL, 'LH0511', NULL, NULL, 94.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/60R17', 'US$'),
(4450, 280, 1, 6, 55, 'LLANTA HANKOOK 205/55R16 KINERGY 91H', NULL, NULL, 'LH0510', NULL, NULL, 69.82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'US$'),
(4451, 280, 1, 6, 55, 'LLANTA HANKOOK 205/50R15 VENTUS 86W', NULL, NULL, 'LH0509', NULL, NULL, 76.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'US$'),
(4452, 290, 1, 6, 55, 'LLANTA TRIANGLE 175/70R13 TR928 82H', NULL, NULL, 'LH0508', NULL, NULL, 37.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(4453, 280, 1, 6, 55, 'LLANTA HANKOOK 195/65R15 K425 88H', NULL, NULL, 'LH0507', NULL, NULL, 60.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(4454, 280, 1, 6, 55, 'LLANTA HANKOOK 155/70R12 H429 73T TL', NULL, NULL, 'LH0506', NULL, NULL, 31.87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R12', 'US$'),
(4455, 317, 1, 6, 55, 'LLANTA GOOD YEAR 265/70R16 AT ADVENTURE', NULL, NULL, 'LH0505', NULL, NULL, 456, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(4456, 317, 1, 6, 55, 'LLANTA GOOD YEAR 245/75R16 AT ARMORTRAC', NULL, NULL, 'LH0504', NULL, NULL, 458.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4457, 366, 1, 6, 55, 'LLANTA HABILEAD 225/65R16 HT DURABLEMAX', NULL, NULL, 'LH0503', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R16', 'S/.'),
(4458, 312, 1, 6, 55, 'LLANTA MIRAGE 155/70R12 MR162 73T TL', NULL, NULL, 'LH0502', NULL, NULL, 26.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R12', 'S/.'),
(4459, 312, 1, 6, 55, 'LLANTA MIRAGE 205/50R16 MR162 87V', NULL, NULL, 'LH0501', NULL, NULL, 44.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'US$'),
(4460, 359, 1, 6, 55, 'LLANTA SPORTRAK 245/70R19.5 POS SP305 14PR TL', NULL, NULL, 'LH0500', NULL, NULL, 145.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R19.5', 'US$'),
(4461, 359, 1, 6, 55, 'LLANTA SPORTRAK 245/70R19.5 DEL SP301 16PR TL', NULL, NULL, 'LH0499', NULL, NULL, 144.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R19.5', 'S/.'),
(4462, 275, 1, 6, 55, 'LLANTA TOYO TYRES 265/70R16 AT OPEN COUNTRY', NULL, NULL, 'LH0497', NULL, NULL, 150, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(4463, 299, 1, 6, 52, 'ARO DRAGON WHEELS 495 16X7.0 5X114.3 35 60 S1/MF', NULL, NULL, 'ARO240', NULL, NULL, 51.85, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '495', 'S/.'),
(4464, 299, 1, 6, 52, 'ARO DRAGON WHEELS 722 14X6.0 5X114.3 00 83 BM/F', NULL, NULL, 'ARO239', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '722', 'S/.'),
(4465, 299, 1, 6, 52, 'ARO DRAGON WHEELS 922 15X6.0 5X114.3 45 60 G1/MF', NULL, NULL, 'ARO238', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '922', 'S/.'),
(4466, 366, 1, 6, 55, 'LLANTA HABILEAD 195R15 RS01 106/104R 8PR TL', NULL, NULL, 'LH0496', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'S/.'),
(4467, 326, 1, 6, 55, 'LLANTA DURUN 195/60R15 A2000 88V', NULL, NULL, 'LH0495', NULL, NULL, 41.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'S/.'),
(4468, 370, 1, 6, 55, 'LLANTA MINNELL 195/65R15 P07 91H', NULL, NULL, 'LH0494', NULL, NULL, 36.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(4469, 369, 1, 6, 55, 'LLANTA COMPASAL 215/75R14 ST TRAILER', NULL, NULL, 'LH0493', NULL, NULL, 43.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R14', 'US$'),
(4470, 281, 1, 6, 55, 'LLANTA DUNLOP 155/70R12 TOURING 73T TL', NULL, NULL, 'LH0492', NULL, NULL, 32.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R12', 'S/.'),
(4471, 290, 1, 6, 55, 'LLANTA TRIANGLE 8.25R16 POS TR690 128/124K 16PR', NULL, NULL, 'LH0491', NULL, NULL, 157.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'S/.'),
(4472, 290, 1, 6, 55, 'LLANTA TRIANGLE 8.25R16 MIX TR668 128/124M 16PR', NULL, NULL, 'LH0490', NULL, NULL, 136.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'S/.'),
(4473, 333, 1, 6, 55, 'LLANTA WANDA 5.00R12 WR081 83/81Q 10PR', NULL, NULL, 'LH0489', NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5.00R12', 'US$'),
(4474, 290, 1, 6, 55, 'LLANTA TRIANGLE 195/65R15 TR928 91H', NULL, NULL, 'LH0488', NULL, NULL, 45.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(4475, 290, 1, 6, 55, 'LLANTA TRIANGLE 205/60R16 TR918 96H', NULL, NULL, 'LH0487', NULL, NULL, 59.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R16', 'S/.'),
(4476, 290, 1, 6, 55, 'LLANTA TRIANGLE 20.5-25 L3 E3 20PR LOAD TL612', NULL, NULL, 'LH0486', NULL, NULL, 995.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '20.5-25', 'S/.'),
(4477, 368, 1, 6, 54, 'GUARDACAMARA TYRESOL R20', NULL, NULL, 'GCA01', NULL, NULL, 14.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'R20', 'S/.'),
(4478, 313, 1, 6, 55, 'LLANTA GENERAL 245/75R16 AT GRABBER', NULL, NULL, 'LH0485', NULL, NULL, 153.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4479, 367, 1, 6, 55, 'LLANTA FARROAD 265/70R16 AT FRD86', NULL, NULL, 'LH0484', NULL, NULL, 91.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(4480, 341, 1, 6, 55, 'LLANTA ROADSHINE 215/70R15 RS925 8PR', NULL, NULL, 'LH0483', NULL, NULL, 67.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R15', 'US$'),
(4481, 341, 1, 6, 55, 'LLANTA ROADSHINE 185/65R14 RS928 86H', NULL, NULL, 'LH0482', NULL, NULL, 34.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'S/.'),
(4482, 341, 1, 6, 55, 'LLANTA ROADSHINE 215/45R17 RS909 91Y', NULL, NULL, 'LH0481', NULL, NULL, 58.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'S/.'),
(4483, 336, 1, 6, 55, 'LLANTA MAXTREK 185/65R15 MAXIMUS 86H', NULL, NULL, 'LH0479', NULL, NULL, 39.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'S/.'),
(4484, 343, 1, 6, 55, 'LLANTA WINDA 155/65R13 WP15 73T', NULL, NULL, 'LH0477', NULL, NULL, 25.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/65R13', 'US$'),
(4485, 317, 1, 6, 55, 'LLANTA GOOD YEAR 12.00-20 DEL CAMINERA 18PR GT150', NULL, NULL, 'LH0476', NULL, NULL, 484.37, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00-20', 'US$'),
(4486, 336, 1, 6, 55, 'LLANTA MAXTREK 185/70R14 SU830 88T', NULL, NULL, 'LH0474', NULL, NULL, 38.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4487, 336, 1, 6, 55, 'LLANTA MAXTREK 155R13 SU810 90/88S', NULL, NULL, 'LH0473', NULL, NULL, 32.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R13', 'US$'),
(4488, 336, 1, 6, 55, 'LLANTA MAXTREK 195/70R15 SU810 8PR', NULL, NULL, 'LH0471', NULL, NULL, 55.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R15', 'US$'),
(4489, 312, 1, 6, 55, 'LLANTA MIRAGE 235/75R17.5 DEL MG-022 143/141J 16PR', NULL, NULL, 'LH0470', NULL, NULL, 133.98, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R17.5', 'US$'),
(4490, 312, 1, 6, 55, 'LLANTA MIRAGE 31X10.50R15 AT MR172', NULL, NULL, 'LH0469', NULL, NULL, 92.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.50R15', 'US$'),
(4491, 312, 1, 6, 55, 'LLANTA MIRAGE 195/70R14 MR162 91H', NULL, NULL, 'LH0468', NULL, NULL, 44.95, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R14', 'US$'),
(4492, 366, 1, 6, 55, 'LLANTA HABILEAD 185/70R13 H202 86T', NULL, NULL, 'LH0465', NULL, NULL, 32.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(4493, 366, 1, 6, 55, 'LLANTA HABILEAD 195R14 RS01 106/104Q TL', NULL, NULL, 'LH0464', NULL, NULL, 47.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'US$'),
(4494, 366, 1, 6, 55, 'LLANTA HABILEAD 185/65R14 H202 86H CONFORT', NULL, NULL, 'LH0463', NULL, NULL, 31.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'S/.'),
(4495, 366, 1, 6, 55, 'LLANTA HABILEAD 165/65R13 H202 77T', NULL, NULL, 'LH0461', NULL, NULL, 25.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'US$'),
(4496, 366, 1, 6, 55, 'LLANTA HABILEAD 205/60R16 H202 92V CONFORT', NULL, NULL, 'LH0460', NULL, NULL, 44.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R16', 'US$'),
(4497, 366, 1, 6, 55, 'LLANTA HABILEAD 175/70R14 H202 84H', NULL, NULL, 'LH0459', NULL, NULL, 31.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'US$'),
(4498, 365, 1, 6, 53, 'BATERIA CAPSA 214D PREMIUM', NULL, NULL, 'BAT058', NULL, NULL, 517.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '214D', 'S/.'),
(4499, 365, 1, 6, 53, 'BATERIA CAPSA 19D44 PREMIUM', NULL, NULL, 'BAT057', NULL, NULL, 448.91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '19D44', 'S/.'),
(4500, 365, 1, 6, 53, 'BATERIA CAPSA 1731T PREMIUM', NULL, NULL, 'BAT056', NULL, NULL, 398.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1731T', 'S/.'),
(4501, 365, 1, 6, 53, 'BATERIA CAPSA 15MBI PREMIUM', NULL, NULL, 'BAT055', NULL, NULL, 296.15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '15MBI', 'S/.'),
(4502, 365, 1, 6, 53, 'BATERIA CAPSA 15APCG PREMIUM', NULL, NULL, 'BAT054', NULL, NULL, 350.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '15APCG', 'S/.'),
(4503, 365, 1, 6, 53, 'BATERIA CAPSA 13APCG PREMIUM', '', '', 'BAT053', '', '', 319.02, 0, 0, 0, b'0', NULL, NULL, '2018-02-22 10:55:45', 'Jeam', NULL, b'1', 0, b'0', 0, NULL, '13APCG', 'S/.'),
(4504, 365, 1, 6, 53, 'BATERIA CAPSA 11APCG PREMIUM', NULL, NULL, 'BAT052', NULL, NULL, 270.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11APCG', 'US$'),
(4505, 365, 1, 6, 53, 'BATERIA CAPSA 13WI PREMIUM', NULL, NULL, 'BAT051', NULL, NULL, 252.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '13WI', 'S/.'),
(4506, 365, 1, 6, 53, 'BATERIA CAPSA 11WI PREMIUM', NULL, NULL, 'BAT050', NULL, NULL, 222.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11WI', 'US$'),
(4507, 365, 1, 6, 53, 'BATERIA CAPSA 9WI PREMIUM', NULL, NULL, 'BAT049', NULL, NULL, 224.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9WI', 'S/.'),
(4508, 365, 1, 6, 53, 'BATERIA CAPSA 13API PREMIUM', NULL, NULL, 'BAT048', NULL, NULL, 286.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '13API', 'S/.'),
(4509, 365, 1, 6, 53, 'BATERIA CAPSA 11API PREMIUM', NULL, NULL, 'BAT047', NULL, NULL, 246.29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11API', 'S/.'),
(4510, 365, 1, 6, 53, 'BATERIA CAPSA 11TOI PREMIUM', NULL, NULL, 'BAT045', NULL, NULL, 218.11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11TOI', 'S/.'),
(4511, 365, 1, 6, 53, 'BATERIA CAPSA 10FDI PREMIUM', NULL, NULL, 'BAT044', NULL, NULL, 206.81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10FDI', 'US$'),
(4512, 365, 1, 6, 53, 'BATERIA CAPSA 9FDI PREMIUM', NULL, NULL, 'BAT043', NULL, NULL, 192.25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9FDI', 'S/.'),
(4513, 365, 1, 6, 53, 'BATERIA CAPSA 7U1R PREMIUM', NULL, NULL, 'BAT042', NULL, NULL, 134.83, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7U1R', 'S/.'),
(4514, 365, 1, 6, 53, 'BATERIA CAPSA 5U1R PREMIUM', NULL, NULL, 'BAT041', NULL, NULL, 109.09, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5U1R', 'S/.'),
(4515, 364, 1, 6, 53, 'BATERIA ENERJET 27P190 N2', NULL, NULL, 'BAT040', NULL, NULL, 599.58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '27P190', 'S/.'),
(4516, 364, 1, 6, 53, 'BATERIA ENERJET 23P159 N2', NULL, NULL, 'BAT039', NULL, NULL, 556.33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '23P159', 'S/.'),
(4517, 364, 1, 6, 53, 'BATERIA ENERJET 19P130 N2', NULL, NULL, 'BAT037', NULL, NULL, 478.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '19P130', 'S/.'),
(4518, 364, 1, 6, 53, 'BATERIA ENERJET 17T114 N2', NULL, NULL, 'BAT036', NULL, NULL, 409.98, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17T114', 'S/.'),
(4519, 364, 1, 6, 53, 'BATERIA ENERJET 15MB90 N2', NULL, NULL, 'BAT035', NULL, NULL, 300.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '15MB90', 'S/.'),
(4520, 364, 1, 6, 53, 'BATERIA ENERJET 15M99 N2', NULL, NULL, 'BAT034', NULL, NULL, 350.69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '15M99', 'S/.'),
(4521, 364, 1, 6, 53, 'BATERIA ENERJET 13M87 N2', NULL, NULL, 'BAT033', NULL, NULL, 323.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '13M87', 'S/.'),
(4522, 364, 1, 6, 53, 'BATERIA ENERJET 11M73 N2', NULL, NULL, 'BAT032', NULL, NULL, 297.29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11M73', 'S/.'),
(4523, 364, 1, 6, 53, 'BATERIA ENERJET 13W75 N2', NULL, NULL, 'BAT031', NULL, NULL, 264.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '13W75', 'S/.'),
(4524, 364, 1, 6, 53, 'BATERIA ENERJET 11W63 N2', NULL, NULL, 'BAT030', NULL, NULL, 236.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11W63', 'S/.'),
(4525, 364, 1, 6, 53, 'BATERIA ENERJET 9W50 N2', NULL, NULL, 'BAT029', NULL, NULL, 210.78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9W50', 'S/.'),
(4526, 364, 1, 6, 53, 'BATERIA ENERJET 13S85 N2', NULL, NULL, 'BAT028', NULL, NULL, 271.78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '13S85', 'S/.'),
(4527, 364, 1, 6, 53, 'BATERIA ENERJET 11S71 N2', NULL, NULL, 'BAT027', NULL, NULL, 240.97, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11S71', 'S/.'),
(4528, 364, 1, 6, 53, 'BATERIA ENERJET 9S58 N2', NULL, NULL, 'BAT026', NULL, NULL, 213.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9S58', 'S/.'),
(4529, 364, 1, 6, 53, 'BATERIA ENERJET 11T56 N2', NULL, NULL, 'BAT025', NULL, NULL, 234.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11T56', 'S/.'),
(4530, 364, 1, 6, 53, 'BATERIA ENERJET 11D56 N2', NULL, NULL, 'BAT024', NULL, NULL, 223.26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '11D56', 'S/.'),
(4531, 364, 1, 6, 53, 'BATERIA ENERJET 9D45 N2', NULL, NULL, 'BAT023', NULL, NULL, 194.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9D45', 'S/.'),
(4532, 364, 1, 6, 53, 'BATERIA ENERJET MT38 N2', NULL, NULL, 'BAT022', NULL, NULL, 141.67, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'MT38', 'S/.'),
(4533, 364, 1, 6, 53, 'BATERIA ENERJET MT30 N2', NULL, NULL, 'BAT021', NULL, NULL, 115.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'MT30', 'S/.'),
(4534, 363, 1, 6, 53, 'BATERIA RECORD RFF 65 PLUS', NULL, NULL, 'BAT020', NULL, NULL, 226.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RFF65', 'S/.'),
(4535, 363, 1, 6, 53, 'BATERIA RECORD RT 230 PLUS', NULL, NULL, 'BAT019', NULL, NULL, 729.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RT230', 'S/.'),
(4536, 363, 1, 6, 53, 'BATERIA RECORD RT 202 PLUS', NULL, NULL, 'BAT018', NULL, NULL, 617.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RT202', 'S/.'),
(4537, 363, 1, 6, 53, 'BATERIA RECORD RT 158 PLUS', NULL, NULL, 'BAT017', NULL, NULL, 544.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RT158', 'S/.'),
(4538, 363, 1, 6, 53, 'BATERIA RECORD RT 130 PLUS', NULL, NULL, 'BAT016', NULL, NULL, 486.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RT130', 'S/.'),
(4539, 363, 1, 6, 53, 'BATERIA RECORD RT 115 PLUS', NULL, NULL, 'BAT015', NULL, NULL, 419.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RT115', 'S/.'),
(4540, 363, 1, 6, 53, 'BATERIA RECORD N 100 PLUS', NULL, NULL, 'BAT014', NULL, NULL, 425.65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'N100', 'S/.'),
(4541, 363, 1, 6, 53, 'BATERIA RECORD RMB 100 PLUS', NULL, NULL, 'BAT013', NULL, NULL, 367.73, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RMB100', 'S/.'),
(4542, 363, 1, 6, 53, 'BATERIA RECORD RMB 85 PLUS', NULL, NULL, 'BAT012', NULL, NULL, 289.93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RMB85', 'S/.'),
(4543, 363, 1, 6, 53, 'BATERIA RECORD RF 90 PLUS', NULL, NULL, 'BAT011', NULL, NULL, 332.26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RF90', 'S/.'),
(4544, 363, 1, 6, 53, 'BATERIA RECORD RF 75 PLUS', NULL, NULL, 'BAT010', NULL, NULL, 319.31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RF75', 'S/.'),
(4545, 363, 1, 6, 53, 'BATERIA RECORD RF 65 PLUS', NULL, NULL, 'BAT009', NULL, NULL, 267.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RF65', 'S/.'),
(4546, 363, 1, 6, 53, 'BATERIA RECORD RW 70 PLUS', NULL, NULL, 'BAT008', NULL, NULL, 254.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RW70', 'S/.'),
(4547, 363, 1, 6, 53, 'BATERIA RECORD RW 65 PLUS', NULL, NULL, 'BAT007', NULL, NULL, 250.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RW65', 'S/.'),
(4548, 363, 1, 6, 53, 'BATERIA RECORD RW 52 PLUS', NULL, NULL, 'BAT006', NULL, NULL, 224.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RW52', 'S/.'),
(4549, 363, 1, 6, 53, 'BATERIA RECORD RC 70 PLUS', NULL, NULL, 'BAT005', NULL, NULL, 261.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RC70', 'S/.'),
(4550, 363, 1, 6, 53, 'BATERIA RECORD RC 65 PLUS', NULL, NULL, 'BAT004', NULL, NULL, 231.07, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RC65', 'S/.'),
(4551, 363, 1, 6, 53, 'BATERIA RECORD RC 52 PLUS', NULL, NULL, 'BAT003', NULL, NULL, 190.14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RC52', 'S/.'),
(4552, 363, 1, 6, 53, 'BATERIA RECORD RNS 45 PLUS', NULL, NULL, 'BAT002', NULL, NULL, 217.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RNS45', 'S/.'),
(4553, 363, 1, 6, 53, 'BATERIA RECORD RNS 40 PLUS', NULL, NULL, 'BAT001', NULL, NULL, 207.89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'RNS40', 'S/.'),
(4554, 362, 1, 6, 55, 'LLANTA PRAXIS 4.00-8 MIX 8PR NYLON GRIP', NULL, NULL, 'LH0458', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4.00-8', 'S/.'),
(4555, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-242 13X5.5 ET35 100+114.3X8H H-S', NULL, NULL, 'ARO236', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '242', 'S/.'),
(4556, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3255 14X6.0 ET35 100X4H CA-S4B', NULL, NULL, 'ARO235', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3255', 'S/.'),
(4557, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3255 13X5.5 ET35 100X4H CA-S4B', NULL, NULL, 'ARO234', NULL, NULL, 51.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3255', 'S/.'),
(4558, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-247 13X5.5 ET35 100+114.3X8H B-P', NULL, NULL, 'ARO233', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '247', 'S/.'),
(4559, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-348 13X5.5 ET32 100+114.3X8H B-P', NULL, NULL, 'ARO232', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '348', 'S/.'),
(4560, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-331 13X5.5 ET35 100+114.3X8H B-P', NULL, NULL, 'ARO231', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '331', 'S/.'),
(4561, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-6708 13X5.5 ET12 100X4H B-P', NULL, NULL, 'ARO230', NULL, NULL, 51.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6708', 'S/.'),
(4562, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 235/60R18 HT SPORT 107V', NULL, NULL, 'LH0457', NULL, NULL, 156, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R18', 'US$'),
(4563, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 195/65R15 GIII 91H', NULL, NULL, 'LH0456', NULL, NULL, 79.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(4564, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 185/60R15 ER300 88H', NULL, NULL, 'LH0455', NULL, NULL, 75.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R15', 'S/.'),
(4565, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/65R17 MT DUELER', NULL, NULL, 'LH0454', NULL, NULL, 210, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'S/.'),
(4566, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 205/60R15 GIII 91V', NULL, NULL, 'LH0453', NULL, NULL, 91.86, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'S/.'),
(4567, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 235/55R19 DUELER HL 105H', NULL, NULL, 'LH0452', NULL, NULL, 160.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/55R19', 'US$'),
(4568, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 235/60R16 HT D687 100H', NULL, NULL, 'LH0451', NULL, NULL, 123.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R16', 'US$'),
(4569, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 225/60R17 HT DUELER', NULL, NULL, 'LH0450', NULL, NULL, 146.95, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/60R17', 'S/.'),
(4570, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 275/40R20 SPORTAS 105W', NULL, NULL, 'LH0449', NULL, NULL, 202.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/40R20', 'US$'),
(4571, 322, 1, 6, 55, 'LLANTA WESTLAKE 5.00R12 83/82P 8PR', NULL, NULL, 'LH0448', NULL, NULL, 34.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5.00R12', 'S/.'),
(4572, 361, 1, 6, 55, 'LLANTA SOLIDEAL 20.5-25 L3 E3 .20PR LOAD MASTER', NULL, NULL, 'LH0447', NULL, NULL, 1356, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '20.5-25', 'S/.'),
(4573, 341, 1, 6, 55, 'LLANTA ROADSHINE 235/75R17.5 DEL RS615 132/129M 16PR', NULL, NULL, 'LH0446', NULL, NULL, 160.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R17.5', 'S/.'),
(4574, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 195/60R16 TURANZA 89H', NULL, NULL, 'LH0445', NULL, NULL, 124.72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R16', 'S/.'),
(4575, 324, 1, 6, 55, 'LLANTA BCT 225/40R18 RT655 102H', NULL, NULL, 'LH0444', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/40R18', 'S/.'),
(4576, 336, 1, 6, 55, 'LLANTA MAXTREK 215/70R16 AT SU830', NULL, NULL, 'LH0443', NULL, NULL, 76.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R16', 'S/.'),
(4577, 331, 1, 6, 55, 'LLANTA DEESTONE 185/65R15 NAKARA 88H R201', NULL, NULL, 'LH0442', NULL, NULL, 48.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'US$'),
(4578, 292, 1, 6, 55, 'LLANTA ORNET 8.25-16 POS L-602 18PR', NULL, NULL, 'LH0441', NULL, NULL, 160.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4579, 293, 1, 6, 55, 'LLANTA BARUM 165/65R13 BRILLANTIS 77T', NULL, NULL, 'LH0440', NULL, NULL, 35.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'US$'),
(4580, 341, 1, 6, 55, 'LLANTA ROADSHINE 12R22.5 TRA RS617 152/149M 18PR', NULL, NULL, 'LH0437', NULL, NULL, 273.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'S/.'),
(4581, 275, 1, 6, 55, 'LLANTA TOYO TYRES 205/55R16 PROXES 91W', NULL, NULL, 'LH0255', NULL, NULL, 123.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'S/.'),
(4582, 336, 1, 6, 55, 'LLANTA MAXTREK 225/75R15 SU800 102h', NULL, NULL, 'LH0436', NULL, NULL, 79.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R15', 'US$'),
(4583, 336, 1, 6, 55, 'LLANTA MAXTREK 215/70R15 MK700 8PR', NULL, NULL, 'LH0435', NULL, NULL, 63.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R15', 'US$'),
(4584, 336, 1, 6, 55, 'LLANTA MAXTREK 185/65R15 SU830 88H', NULL, NULL, 'LH0434', NULL, NULL, 44.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'S/.'),
(4585, 336, 1, 6, 55, 'LLANTA MAXTREK 265/75R16 MT MUD TRAC', NULL, NULL, 'LH0433', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(4586, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/70R16 HT DUELER D694', NULL, NULL, 'LH0432', NULL, NULL, 164.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(4587, 360, 1, 6, 55, 'LLANTA ALLIANCE 19.5-24 12PR L3 NYLON', NULL, NULL, 'LH0430', NULL, NULL, 738.02, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '19.5-24', 'S/.'),
(4588, 287, 1, 6, 55, 'LLANTA GOODTYRE 23.5-25 24PR L3 PERS', NULL, NULL, 'LH0429', NULL, NULL, 1128, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '23.5-25', 'US$'),
(4589, 283, 1, 6, 55, 'LLANTA SAMSON 12.5/80-18 14PR L2D SKID', NULL, NULL, 'LH0428', NULL, NULL, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.5/80-18', 'US$'),
(4590, 312, 1, 6, 55, 'LLANTA MIRAGE 165/65R13 MR162 77T', NULL, NULL, 'LH0427', NULL, NULL, 32.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'US$'),
(4591, 330, 1, 6, 55, 'LLANTA KUMHO 205/50R15 ECSTA KU36 86W', NULL, NULL, 'LH0426', NULL, NULL, 74.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'US$'),
(4592, 272, 1, 6, 55, 'LLANTA INSA TURBO 195/65R15 ECOSABER 91H', NULL, NULL, 'LH0425', NULL, NULL, 45.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'US$'),
(4593, 278, 1, 6, 55, 'LLANTA HAIDA 195/70R14 HD616 91H', NULL, NULL, 'LH0424', NULL, NULL, 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R14', 'US$'),
(4594, 278, 1, 6, 55, 'LLANTA HAIDA 185/70R14 HD667 88T', NULL, NULL, 'LH0423', NULL, NULL, 37.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4595, 317, 1, 6, 55, 'LLANTA GOOD YEAR 175/70R13 ASSURANCE 82T', NULL, NULL, 'LH0422', NULL, NULL, 130.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'S/.');
INSERT INTO `Gen_Producto` (`IdProducto`, `IdProductoMarca`, `IdProductoFormaFarmaceutica`, `IdProductoMedicion`, `IdProductoCategoria`, `Producto`, `ProductoDesc`, `ProductoDescCorto`, `CodigoBarra`, `Codigo`, `Dosis`, `PrecioContado`, `PrecioPorMayor`, `StockPorMayor`, `StockMinimo`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `ControlaStock`, `PrecioCosto`, `VentaEstrategica`, `PorcentajeUtilidad`, `IdBloque`, `Modelo`, `Moneda`) VALUES
(4596, 313, 1, 6, 55, 'LLANTA GENERAL 265/75R16 MT GRABBER', NULL, NULL, 'LH0421', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(4597, 278, 1, 6, 55, 'LLANTA HAIDA 275/45R20 HD921 110W', NULL, NULL, 'LH0419', NULL, NULL, 83.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/45R20', 'US$'),
(4598, 278, 1, 6, 55, 'LLANTA HAIDA 225/65R17 HT HD668 102H', NULL, NULL, 'LH0418', NULL, NULL, 65.19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'US$'),
(4599, 311, 1, 6, 55, 'LLANTA APLUS 245/75R16 MT MUD TERRAIN', NULL, NULL, 'LH0417', NULL, NULL, 101.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4600, 273, 1, 6, 55, 'LLANTA FALKEN 185R14 LINAMAR 102/100P 8PR', NULL, NULL, 'LH0415', NULL, NULL, 84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185R14', 'S/.'),
(4601, 312, 1, 6, 55, 'LLANTA MIRAGE 235/60R16 HT MR172 100H', NULL, NULL, 'LH0414', NULL, NULL, 70.86, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R16', 'US$'),
(4602, 350, 1, 6, 55, 'LLANTA ANTARES 205/60R16 SU830 6PR', NULL, NULL, 'LH0411', NULL, NULL, 50.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R16', 'US$'),
(4603, 290, 1, 6, 55, 'LLANTA TRIANGLE 205/65R15 TR928 94H', NULL, NULL, 'LH0409', NULL, NULL, 53.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/65R15', 'US$'),
(4604, 331, 1, 6, 55, 'LLANTA DEESTONE 205/55R15 VINCENTE 88V', NULL, NULL, 'LH0408', NULL, NULL, 51.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R15', 'US$'),
(4605, 331, 1, 6, 55, 'LLANTA DEESTONE 205/60R15 NAKARA 91V', NULL, NULL, 'LH0407', NULL, NULL, 57.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'US$'),
(4606, 331, 1, 6, 55, 'LLANTA DEESTONE 205R14 KACHOR 101 109/107P', NULL, NULL, 'LH0406', NULL, NULL, 87.19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205R14', 'US$'),
(4607, 278, 1, 6, 55, 'LLANTA HAIDA 205/50R17 HD921 93W', NULL, NULL, 'LH0405', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R17', 'US$'),
(4608, 314, 1, 6, 55, 'LLANTA COMFORSER 205/50R15 CF5000 86V', NULL, NULL, 'LH0403', NULL, NULL, 37.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'US$'),
(4609, 313, 1, 6, 55, 'LLANTA GENERAL 165/60R14 ALTIMAX 75H', NULL, NULL, 'LH0402', NULL, NULL, 38.51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/60R14', 'US$'),
(4610, 311, 1, 6, 55, 'LLANTA APLUS 185/65R14 A606 88H', NULL, NULL, 'LH0400', NULL, NULL, 30.72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'US$'),
(4611, 311, 1, 6, 55, 'LLANTA APLUS 185/60R14 A606 82H', NULL, NULL, 'LH0399', NULL, NULL, 28.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R14', 'US$'),
(4612, 311, 1, 6, 55, 'LLANTA APLUS 175/70R14 A606 84H', NULL, NULL, 'LH0398', NULL, NULL, 28.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'US$'),
(4613, 311, 1, 6, 55, 'LLANTA APLUS 175/65R14 A606 82H', NULL, NULL, 'LH0397', NULL, NULL, 28.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'US$'),
(4614, 359, 1, 6, 55, 'LLANTA SPORTRAK 12.00R24 TRA SP981 20PR', NULL, NULL, 'LH0396', NULL, NULL, 343.19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R24', 'US$'),
(4615, 358, 1, 6, 55, 'LLANTA ANNAITE 12.00R20 TRA A309 18PR', NULL, NULL, 'LH0395', NULL, NULL, 312, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(4616, 291, 1, 6, 55, 'LLANTA HIFLY 12.00R20 TRA HH317 18PR', NULL, NULL, 'LH0394', NULL, NULL, 256.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'S/.'),
(4617, 321, 1, 6, 55, 'LLANTA XCEED 12.00R20 TRA XD-968 18PR', NULL, NULL, 'LH0388', NULL, NULL, 301.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'US$'),
(4618, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 245/75R16 AT DUELER D694', NULL, NULL, 'LH0386', NULL, NULL, 171.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4619, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/75R16 AT DUELER D694', NULL, NULL, 'LH0385', NULL, NULL, 213.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(4620, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 235/85R16 AT DUELER D696', NULL, NULL, 'LH0384', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/85R16', 'S/.'),
(4621, 280, 1, 6, 55, 'LLANTA HANKOOK 265/75R16 MT DYNAPRO', NULL, NULL, 'LH0383', NULL, NULL, 176.87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(4622, 312, 1, 6, 55, 'LLANTA MIRAGE 265/75R16 AT MR172', NULL, NULL, 'LH0382', NULL, NULL, 133, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(4623, 331, 1, 6, 55, 'LLANTA DEESTONE 265/75R16 AT PAYAK', NULL, NULL, 'LH0381', NULL, NULL, 127.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(4624, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/70R16 AT DUELER D694', NULL, NULL, 'LH0380', NULL, NULL, 202.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(4625, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 245/70R16 AT DUELER', NULL, NULL, 'LH0379', NULL, NULL, 228, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R16', 'S/.'),
(4626, 336, 1, 6, 55, 'LLANTA MAXTREK 225/75R16 AT SU800 118/116S', NULL, NULL, 'LH0378', NULL, NULL, 83.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/75R16', 'S/.'),
(4627, 336, 1, 6, 55, 'LLANTA MAXTREK 225/70R16 AT SU830 107S', NULL, NULL, 'LH0377', NULL, NULL, 97.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'US$'),
(4628, 281, 1, 6, 55, 'LLANTA DUNLOP 235/60R16 AT GRANDTREK', NULL, NULL, 'LH0375', NULL, NULL, 154.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R16', 'S/.'),
(4629, 321, 1, 6, 55, 'LLANTA XCEED 7.00-15 DEL XD302 114/112K', NULL, NULL, 'LH0373', NULL, NULL, 110.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-15', 'S/.'),
(4630, 313, 1, 6, 55, 'LLANTA GENERAL 235/60R16 AT GRABBER', NULL, NULL, 'LH0371', NULL, NULL, 100.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R16', 'S/.'),
(4631, 291, 1, 6, 55, 'LLANTA HIFLY 265/70R17 AT VIGOROUS', NULL, NULL, 'LH0370', NULL, NULL, 80.47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'US$'),
(4632, 312, 1, 6, 55, 'LLANTA MIRAGE 265/70R17 AT MR172', NULL, NULL, 'LH0369', NULL, NULL, 105.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'US$'),
(4633, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 265/65R17 AT DUELER D694', NULL, NULL, 'LH0368', NULL, NULL, 223.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'US$'),
(4634, 280, 1, 6, 55, 'LLANTA HANKOOK 245/70R17 AT DYNAPRO', NULL, NULL, 'LH0367', NULL, NULL, 162.58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R17', 'S/.'),
(4635, 280, 1, 6, 55, 'LLANTA HANKOOK 265/70R17 AT DYNAPRO', NULL, NULL, 'LH0365', NULL, NULL, 169.55, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'S/.'),
(4636, 330, 1, 6, 55, 'LLANTA KUMHO 235/85R16 AT MOHAVE', NULL, NULL, 'LH0364', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/85R16', 'S/.'),
(4637, 281, 1, 6, 55, 'LLANTA DUNLOP 265/65R17 AT GRANDTREK', NULL, NULL, 'LH0363', NULL, NULL, 174, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'S/.'),
(4638, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 245/65R17 AT DUELER 107T', NULL, NULL, 'LH0362', NULL, NULL, 195.43, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/65R17', 'S/.'),
(4639, 280, 1, 6, 55, 'LLANTA HANKOOK 235/65R17 AT DYNAPRO', NULL, NULL, 'LH0361', NULL, NULL, 144.47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(4640, 313, 1, 6, 55, 'LLANTA GENERAL 225/60R17 HT GRABBER 95H', NULL, NULL, 'LH0360', NULL, NULL, 111.74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/60R17', 'US$'),
(4641, 336, 1, 6, 55, 'LLANTA MAXTREK 245/70R16 AT SU800', NULL, NULL, 'LH0359', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R16', 'S/.'),
(4642, 322, 1, 6, 55, 'LLANTA WESTLAKE 215/50R17 SU308 95W', NULL, NULL, 'LH0358', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/50R17', 'S/.'),
(4643, 279, 1, 6, 55, 'LLANTA GOLDWAY 215/45R17 G2002 91W', NULL, NULL, 'LH0357', NULL, NULL, 54.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'S/.'),
(4644, 350, 1, 6, 55, 'LLANTA ANTARES 215/45R17 INGENS A1 91W', NULL, NULL, 'LH0356', NULL, NULL, 55.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'S/.'),
(4645, 275, 1, 6, 55, 'LLANTA TOYO TYRES 215/45R17 PROXES 91W', NULL, NULL, 'LH0355', NULL, NULL, 123.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/45R17', 'S/.'),
(4646, 291, 1, 6, 55, 'LLANTA HIFLY 225/45R17 HF805 94W (DEPORTIVO)', NULL, NULL, 'LH0354', NULL, NULL, 49.41, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/45R17', 'US$'),
(4647, 271, 1, 6, 55, 'LLANTA LING LONG 205/45R17 GREEN MAX 88W', NULL, NULL, 'LH0353', NULL, NULL, 53.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/45R17', 'S/.'),
(4648, 280, 1, 6, 55, 'LLANTA HANKOOK 205/50R16 VENTUS V2 87H', NULL, NULL, 'LH0352', NULL, NULL, 122.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(4649, 357, 1, 6, 55, 'LLANTA SUNFULL 205/50R16 SF688 87V', NULL, NULL, 'LH0351', NULL, NULL, 44.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(4650, 278, 1, 6, 55, 'LLANTA HAIDA 205/50R16 HD921 91V', NULL, NULL, 'LH0350', NULL, NULL, 47.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(4651, 290, 1, 6, 55, 'LLANTA TRIANGLE 205/55R16 TR928 91H', NULL, NULL, 'LH0349', NULL, NULL, 52.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'S/.'),
(4652, 339, 1, 6, 55, 'LLANTA ACCELERA 215/55R16 ALPHA 97W', NULL, NULL, 'LH0348', NULL, NULL, 81.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/55R16', 'S/.'),
(4653, 325, 1, 6, 55, 'LLANTA FEDERAL 225/60R16 FORMOZA 98V', NULL, NULL, 'LH0347', NULL, NULL, 96.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/60R16', 'S/.'),
(4654, 273, 1, 6, 55, 'LLANTA FALKEN 205/60R13 ZIEX Z912 86H', NULL, NULL, 'LH0346', NULL, NULL, 82.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R13', 'S/.'),
(4655, 318, 1, 6, 55, 'LLANTA FIRESTONE 195/50R15 FIRE HAWK 82V', NULL, NULL, 'LH0345', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/50R15', 'S/.'),
(4656, 310, 1, 6, 55, 'LLANTA MARSHALL 205/60R13 SOLUS KR21 86T', NULL, NULL, 'LH0344', NULL, NULL, 67.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R13', 'S/.'),
(4657, 328, 1, 6, 55, 'LLANTA FUZION 165/70R13 TOURING 79T', NULL, NULL, 'LH0343', NULL, NULL, 44.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/70R13', 'US$'),
(4658, 275, 1, 6, 55, 'LLANTA TOYO TYRES 175/70R13 OP350 82T', NULL, NULL, 'LH0341', NULL, NULL, 56.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(4659, 330, 1, 6, 55, 'LLANTA KUMHO 165/70R13 SOLUS KR21 78T', NULL, NULL, 'LH0339', NULL, NULL, 43.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/70R13', 'S/.'),
(4660, 356, 1, 6, 55, 'LLANTA JOYROAD 175/70R13 TOUR RX1 82T', NULL, NULL, 'LH0338', NULL, NULL, 37.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(4661, 273, 1, 6, 55, 'LLANTA FALKEN 175/70R13 SN826 82T', NULL, NULL, 'LH0337', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(4662, 312, 1, 6, 55, 'LLANTA MIRAGE 165/70R13 MR162 79T', NULL, NULL, 'LH0334', NULL, NULL, 39.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/70R13', 'S/.'),
(4663, 293, 1, 6, 55, 'LLANTA BARUM 175/70R13 BRILLANTIS 82T', NULL, NULL, 'LH0333', NULL, NULL, 35.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(4664, 330, 1, 6, 55, 'LLANTA KUMHO 155/65R13 SOLUS KM15 73T', NULL, NULL, 'LH0332', NULL, NULL, 40.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/65R13', 'S/.'),
(4665, 343, 1, 6, 55, 'LLANTA WINDA 165/65R13 WP15 77T', NULL, NULL, 'LH0331', NULL, NULL, 28.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'US$'),
(4666, 280, 1, 6, 55, 'LLANTA HANKOOK 155/65R13 OPTIMO K715 73T', NULL, NULL, 'LH0330', NULL, NULL, 31.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/65R13', 'S/.'),
(4667, 312, 1, 6, 55, 'LLANTA MIRAGE 155/65R13 MR162 73T', NULL, NULL, 'LH0328', NULL, NULL, 27.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/65R13', 'S/.'),
(4668, 273, 1, 6, 55, 'LLANTA FALKEN 215/75R15 AT WILDPEAK', NULL, NULL, 'LH0327', NULL, NULL, 141.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(4669, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 195R15 RD613 106/104R 6PR TL', NULL, NULL, 'LH0326', NULL, NULL, 114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'S/.'),
(4670, 341, 1, 6, 55, 'LLANTA ROADSHINE 195/65R15 RS906 91H', NULL, NULL, 'LH0325', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(4671, 336, 1, 6, 55, 'LLANTA MAXTREK 205/70R15 ST MK700', NULL, NULL, 'LH0324', NULL, NULL, 62.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'S/.'),
(4672, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 205/70R15 HT DURAVIS D684', NULL, NULL, 'LH0322', NULL, NULL, 113.41, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'S/.'),
(4673, 291, 1, 6, 55, 'LLANTA HIFLY 205/65R15 HF201 94V', NULL, NULL, 'LH0320', NULL, NULL, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/65R15', 'S/.'),
(4674, 336, 1, 6, 55, 'LLANTA MAXTREK 195/65R15 SU830 91H', NULL, NULL, 'LH0319', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(4675, 280, 1, 6, 55, 'LLANTA HANKOOK 185/65R15 OPTIMO ME02 88H', NULL, NULL, 'LH0318', NULL, NULL, 62.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'US$'),
(4676, 339, 1, 6, 55, 'LLANTA ACCELERA 205/55R15 ALPHA 88V', NULL, NULL, 'LH0317', NULL, NULL, 61.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R15', 'S/.'),
(4677, 336, 1, 6, 55, 'LLANTA MAXTREK 195/60R15 SU 830 88H', NULL, NULL, 'LH0316', NULL, NULL, 46.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'S/.'),
(4678, 325, 1, 6, 55, 'LLANTA FEDERAL 195/55R15 SUPER STEEL 85W', NULL, NULL, 'LH0315', NULL, NULL, 88.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/55R15', 'S/.'),
(4679, 336, 1, 6, 55, 'LLANTA MAXTREK 315/75R16 MT MUD TRAC', NULL, NULL, 'LH0314', NULL, NULL, 166.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '315/75R16', 'US$'),
(4680, 291, 1, 6, 55, 'LLANTA HIFLY 265/70R17 MT VIGOROUS', NULL, NULL, 'LH0313', NULL, NULL, 111.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R17', 'S/.'),
(4681, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 245/75R16 MT DUELER D694', NULL, NULL, 'LH0312', NULL, NULL, 184.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4682, 273, 1, 6, 55, 'LLANTA FALKEN 31X10.50R15 MT LANDAIR', NULL, NULL, 'LH0311', NULL, NULL, 129.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.50R15', 'US$'),
(4683, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 235/75R15 MT DUELER', NULL, NULL, 'LH0310', NULL, NULL, 156, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'S/.'),
(4684, 325, 1, 6, 55, 'LLANTA FEDERAL 235/75R15 MT COURAGIA', NULL, NULL, 'LH0309', NULL, NULL, 125.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'S/.'),
(4685, 314, 1, 6, 55, 'LLANTA COMFORSER 215/75R15 MT CF3000', NULL, NULL, 'LH0308', NULL, NULL, 95.87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(4686, 280, 1, 6, 55, 'LLANTA HANKOOK 235/65R17 HT DYNAPRO 104T', NULL, NULL, 'LH0307', NULL, NULL, 201.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(4687, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 225/65R17 HT DUELER 102T', NULL, NULL, 'LH0306', NULL, NULL, 146.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'S/.'),
(4688, 330, 1, 6, 55, 'LLANTA KUMHO 225/65R17 HT SOLUS KL21 102H', NULL, NULL, 'LH0305', NULL, NULL, 114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'S/.'),
(4689, 336, 1, 6, 55, 'LLANTA MAXTREK 225/60R17 HT MAXIMUS 99V', NULL, NULL, 'LH0304', NULL, NULL, 75.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/60R17', 'S/.'),
(4690, 280, 1, 6, 55, 'LLANTA HANKOOK 235/60R17 HT DYNAPRO 102H', NULL, NULL, 'LH0303', NULL, NULL, 121.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R17', 'S/.'),
(4691, 355, 1, 6, 55, 'LLANTA ODOKING 12.00R20 TRA ST-869 20PR', NULL, NULL, 'LH0302', NULL, NULL, 273.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'S/.'),
(4692, 354, 1, 6, 55, 'LLANTA KAPSEN 12.00R20 MIX HS-801 20PR', NULL, NULL, 'LH0301', NULL, NULL, 249.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'S/.'),
(4693, 292, 1, 6, 55, 'LLANTA ORNET 12.00R20 MIX OR-106 20PR', NULL, NULL, 'LH0300', NULL, NULL, 337.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00R20', 'S/.'),
(4694, 321, 1, 6, 55, 'LLANTA XCEED 12.00-20 DEL XD-301 18PR', NULL, NULL, 'LH0297', NULL, NULL, 305.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12.00-20', 'US$'),
(4695, 280, 1, 6, 55, 'LLANTA HANKOOK 12R22.5 POS AM06 152/148K 16PR', NULL, NULL, 'LH0296', NULL, NULL, 615.61, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12R22.5', 'S/.'),
(4696, 353, 1, 6, 55, 'LLANTA AEOLUS 295/80R22.5 MIX HN-218 152/149L', NULL, NULL, 'LH0294', NULL, NULL, 372, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '295/80R22.5', 'S/.'),
(4697, 352, 1, 6, 55, 'LLANTA DOUBLE COIN 9.5R17.5 DEL RT500 143/141J 18PR', NULL, NULL, 'LH0292', NULL, NULL, 228, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.5R17.5', 'S/.'),
(4698, 322, 1, 6, 55, 'LLANTA WESTLAKE 8.25-16 POS GL-839 POS', NULL, NULL, 'LH0290', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4699, 320, 1, 6, 55, 'LLANTA TEXXAN 8.25-16 POS LX-912 18PR', NULL, NULL, 'LH0289', NULL, NULL, 219.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4700, 321, 1, 6, 55, 'LLANTA XCEED 8.25-16 POS XD-102 16PR', NULL, NULL, 'LH0288', NULL, NULL, 166.79, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4701, 291, 1, 6, 55, 'LLANTA HIFLY 8.25R16 MIX HH301 128/124M 16PR', NULL, NULL, 'LH0287', NULL, NULL, 132.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'S/.'),
(4702, 271, 1, 6, 55, 'LLANTA LING LONG 8.25R16 DEL LLF26 128/126K 14PR', NULL, NULL, 'LH0286', NULL, NULL, 162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R16', 'S/.'),
(4703, 322, 1, 6, 55, 'LLANTA WESTLAKE 8.25-16 DEL CR-892 14PR', NULL, NULL, 'LH0285', NULL, NULL, 177.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4704, 351, 1, 6, 55, 'LLANTA DOUBLE CAMEL 8.25-16 DEL DC-501 16PR', NULL, NULL, 'LH0284', NULL, NULL, 184.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4705, 292, 1, 6, 55, 'LLANTA ORNET 8.25-16 DEL R-501 16PR', NULL, NULL, 'LH0283', NULL, NULL, 204, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4706, 292, 1, 6, 55, 'LLANTA ORNET 8.25-16 DEL R-707 16PR', NULL, NULL, 'LH0282', NULL, NULL, 174, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-16', 'S/.'),
(4707, 341, 1, 6, 55, 'LLANTA ROADSHINE 235/75R17.5 POS RS604 132/129M 16PR', NULL, NULL, 'LH0281', NULL, NULL, 147.29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R17.5', 'S/.'),
(4708, 341, 1, 6, 55, 'LLANTA ROADSHINE 215/75R17.5 POS RS604 127/154M 16PR', NULL, NULL, 'LH0280', NULL, NULL, 120.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R17.5', 'S/.'),
(4709, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 31X10.50R15 AT DUELER', NULL, NULL, 'LH0279', NULL, NULL, 247.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.50R15', 'US$'),
(4710, 281, 1, 6, 55, 'LLANTA DUNLOP 31X10.50R15 AT GRANDTREK', NULL, NULL, 'LH0278', NULL, NULL, 127.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.50R15', 'S/.'),
(4711, 291, 1, 6, 55, 'LLANTA HIFLY 31X10.50R15 AT VIGOROUS', NULL, NULL, 'LH0277', NULL, NULL, 76.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '31X10.50R15', 'US$'),
(4712, 324, 1, 6, 55, 'LLANTA BCT 245/75R16 AT RADIAL', NULL, NULL, 'LH0276', NULL, NULL, 132, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(4713, 280, 1, 6, 55, 'LLANTA HANKOOK 245/75R16 AT DYNAPRO', NULL, NULL, 'LH0275', NULL, NULL, 151.61, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4714, 274, 1, 6, 55, 'LLANTA CATCHFORSE 245/75R16 AT WINDFORSE', NULL, NULL, 'LH0274', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(4715, 280, 1, 6, 55, 'LLANTA HANKOOK 245/70R16 HT DYNAPRO', NULL, NULL, 'LH0273', NULL, NULL, 133.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/70R16', 'US$'),
(4716, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 205/75R16 HT DURAVIS', NULL, NULL, 'LH0271', NULL, NULL, 117.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/75R16', 'US$'),
(4717, 350, 1, 6, 55, 'LLANTA ANTARES 225/65R16 HT NT3000', NULL, NULL, 'LH0270', NULL, NULL, 69.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R16', 'US$'),
(4718, 336, 1, 6, 55, 'LLANTA MAXTREK 225/65R16 ST MK700', NULL, NULL, 'LH0269', NULL, NULL, 67.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R16', 'S/.'),
(4719, 281, 1, 6, 55, 'LLANTA DUNLOP 195/75R16 HT SPLT5', NULL, NULL, 'LH0268', NULL, NULL, 109.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/75R16', 'S/.'),
(4720, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 195/60R15 TURANZA 89H', NULL, NULL, 'LH0264', NULL, NULL, 84.66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'US$'),
(4721, 291, 1, 6, 55, 'LLANTA HIFLY 205/50R16 HF201 87W', NULL, NULL, 'LH0261', NULL, NULL, 50.82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(4722, 339, 1, 6, 55, 'LLANTA ACCELERA 205/50R16 ALPHA 91W', NULL, NULL, 'LH0260', NULL, NULL, 62.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(4723, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 205/55R16 TURANZA 91V', NULL, NULL, 'LH0259', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'S/.'),
(4724, 318, 1, 6, 55, 'LLANTA FIRESTONE 205/50R16 FIRELLA 87V', NULL, NULL, 'LH0258', NULL, NULL, 132.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(4725, 310, 1, 6, 55, 'LLANTA MARSHALL 205/50R16 MATRAC 87W', NULL, NULL, 'LH0257', NULL, NULL, 103, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R16', 'S/.'),
(4726, 291, 1, 6, 55, 'LLANTA HIFLY 205/55R16 HF201 91W', NULL, NULL, 'LH0256', NULL, NULL, 44.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'S/.'),
(4727, 280, 1, 6, 55, 'LLANTA HANKOOK 205/45R16 VENTUS 83H', NULL, NULL, 'LH0254', NULL, NULL, 84.33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/45R16', 'S/.'),
(4728, 336, 1, 6, 55, 'LLANTA MAXTREK 185/70R13 SU830 86T', NULL, NULL, 'LH0253', NULL, NULL, 33.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(4729, 317, 1, 6, 55, 'LLANTA GOOD YEAR 185/70R13 ASSURANCE 86T', NULL, NULL, 'LH0252', NULL, NULL, 115.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'S/.'),
(4730, 317, 1, 6, 55, 'LLANTA GOOD YEAR 185/70R13 VENTURA 86H', NULL, NULL, 'LH0251', NULL, NULL, 158.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(4731, 313, 1, 6, 55, 'LLANTA GENERAL 185/70R13 ALTIMAX 86T', NULL, NULL, 'LH0250', NULL, NULL, 38.98, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(4732, 325, 1, 6, 55, 'LLANTA FEDERAL 205/60R13 SUPER STEEL 87H', NULL, NULL, 'LH0248', NULL, NULL, 47.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R13', 'S/.'),
(4733, 313, 1, 6, 55, 'LLANTA GENERAL 205/60R13 ALTIMAX 86H', NULL, NULL, 'LH0247', NULL, NULL, 50.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R13', 'US$'),
(4734, 336, 1, 6, 55, 'LLANTA MAXTREK 155R12 SU830 88/86S', NULL, NULL, 'LH0245', NULL, NULL, 34.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'S/.'),
(4735, 281, 1, 6, 55, 'LLANTA DUNLOP 155R12 SPP5 88/86N', NULL, NULL, 'LH0244', NULL, NULL, 51.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'S/.'),
(4736, 341, 1, 6, 55, 'LLANTA ROADSHINE 155R12 RS900 83/81Q', NULL, NULL, 'LH0243', NULL, NULL, 34.92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'S/.'),
(4737, 331, 1, 6, 55, 'LLANTA DEESTONE 155R12 R200 88/86R', NULL, NULL, 'LH0242', NULL, NULL, 39.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155R12', 'S/.'),
(4738, 317, 1, 6, 55, 'LLANTA GOOD YEAR 155/70R12 GT70 73T TL', NULL, NULL, 'LH0241', NULL, NULL, 120.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R12', 'S/.'),
(4739, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 7.50-16 POS CORDILLERA 118/116K 10PR', NULL, NULL, 'LH0239', NULL, NULL, 129, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'S/.'),
(4740, 313, 1, 6, 55, 'LLANTA GENERAL 7.50-16 PAN SUPER ALL GRIP 116/112J 10PR', NULL, NULL, 'LH0238', NULL, NULL, 162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4741, 321, 1, 6, 55, 'LLANTA XCEED 7.50-16 POS XD102 1108/115J 16PR', NULL, NULL, 'LH0237', NULL, NULL, 139.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4742, 349, 1, 6, 55, 'LLANTA VIKRANT 7.50-16 POS STAR LUG 16PR', NULL, NULL, 'LH0236', NULL, NULL, 160.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4743, 271, 1, 6, 55, 'LLANTA LING LONG 7.50R16 POS LLD09 122/118M 14PR', NULL, NULL, 'LH0234', NULL, NULL, 143.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'S/.'),
(4744, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 7.50-16 POS EXTRA TD 122/121G 10PR', NULL, NULL, 'LH0232', NULL, NULL, 138.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'S/.'),
(4745, 348, 1, 6, 55, 'LLANTA DRC 7.50-16 POS POWER LUG 127/124L 16PR', NULL, NULL, 'LH0231', NULL, NULL, 138, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4746, 347, 1, 6, 55, 'LLANTA TECHKING 7.50R16 MIX TKAM 122/118M 14PR', NULL, NULL, 'LH0230', NULL, NULL, 144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50R16', 'S/.'),
(4747, 321, 1, 6, 55, 'LLANTA XCEED 7.50-16 DEL XD301 124/120K 16PR', NULL, NULL, 'LH0229', NULL, NULL, 130.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'US$'),
(4748, 346, 1, 6, 55, 'LLANTA DURATREAD 7.50-16 DEL SUPER 127/124K 16PR', NULL, NULL, 'LH0228', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-16', 'S/.'),
(4749, 320, 1, 6, 55, 'LLANTA TEXXAN 7.00-16 POS LV-912 118/116J', NULL, NULL, 'LH0227', NULL, NULL, 138, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-16', 'S/.'),
(4750, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 7.00R16 DEL RB230 116/114L', NULL, NULL, 'LH0226', NULL, NULL, 187.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00R16', 'S/.'),
(4751, 345, 1, 6, 55, 'LLANTA ALTURA 7.00-16 DEL 14PR 116/110I', NULL, NULL, 'LH0225', NULL, NULL, 178.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-16', 'S/.'),
(4752, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.00-16 DEL CAMINERA 111/106J', NULL, NULL, 'LH0224', NULL, NULL, 429.29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-16', 'S/.'),
(4753, 280, 1, 6, 55, 'LLANTA HANKOOK 265/70R15 HT DYNAPRO', NULL, NULL, 'LH0223', NULL, NULL, 148.66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R15', 'S/.'),
(4754, 331, 1, 6, 55, 'LLANTA DEESTONE 235/75R15 AT PAYAK', NULL, NULL, 'LH0222', NULL, NULL, 123.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'S/.'),
(4755, 336, 1, 6, 55, 'LLANTA MAXTREK 235/75R15 AT SU830', NULL, NULL, 'LH0221', NULL, NULL, 81.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'S/.'),
(4756, 341, 1, 6, 55, 'LLANTA ROADSHINE 235/75R15 AT RS915', NULL, NULL, 'LH0219', NULL, NULL, 86.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'S/.'),
(4757, 344, 1, 6, 55, 'LLANTA AUTOGRIP 235/65R16 ST ECOVAN', NULL, NULL, 'LH0218', NULL, NULL, 91.61, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R16', 'S/.'),
(4758, 318, 1, 6, 55, 'LLANTA FIRESTONE 215/75R15 AT DESTINATION', NULL, NULL, 'LH0217', NULL, NULL, 110.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(4759, 281, 1, 6, 55, 'LLANTA DUNLOP 225/70R15 AT GRANTREK', NULL, NULL, 'LH0216', NULL, NULL, 111.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R15', 'S/.'),
(4760, 336, 1, 6, 55, 'LLANTA MAXTREK 215/75R15 AT SU800', NULL, NULL, 'LH0214', NULL, NULL, 68.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(4761, 341, 1, 6, 55, 'LLANTA ROADSHINE 225/70R15 HT RS926', NULL, NULL, 'LH0213', NULL, NULL, 78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R15', 'S/.'),
(4762, 280, 1, 6, 55, 'LLANTA HANKOOK 225/70R15 AT DYNAPRO', NULL, NULL, 'LH0212', NULL, NULL, 112.83, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R15', 'S/.'),
(4763, 280, 1, 6, 55, 'LLANTA HANKOOK 205/70R15 AT DYNAPRO', NULL, NULL, 'LH0211', NULL, NULL, 91.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R15', 'US$'),
(4764, 278, 1, 6, 55, 'LLANTA HAIDA 215/75R15 AT PUMA', NULL, NULL, 'LH0210', NULL, NULL, 71.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R15', 'S/.'),
(4765, 336, 1, 6, 55, 'LLANTA MAXTREK 215/70R15 SU830 98M', NULL, NULL, 'LH0209', NULL, NULL, 53.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/70R15', 'US$'),
(4766, 343, 1, 6, 55, 'LLANTA WINDA 195/70R15 WR01 8PR', NULL, NULL, 'LH0208', NULL, NULL, 56.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R15', 'US$'),
(4767, 330, 1, 6, 55, 'LLANTA KUMHO 205/65R15 ECSTA KM11 94V', NULL, NULL, 'LH0207', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/65R15', 'S/.'),
(4768, 339, 1, 6, 55, 'LLANTA ACCELERA 195/65R15 651 91V', NULL, NULL, 'LH0206', NULL, NULL, 75.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(4769, 342, 1, 6, 55, 'LLANTA WINRUN 195/65R15 R380 91V', NULL, NULL, 'LH0205', NULL, NULL, 40.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'S/.'),
(4770, 337, 1, 6, 55, 'LLANTA LAUFENN 195/65R15 LH41', '', '', 'LH0204', '', '', 56.7, 0, 0, 0, b'0', NULL, NULL, '2018-02-20 11:12:31', 'Jeam', NULL, b'1', 0, b'0', 0, NULL, '195/65R15', 'S/.'),
(4771, 332, 1, 6, 55, 'LLANTA CATCHGRE 195/65R15 WINDFORSE 91V', NULL, NULL, 'LH0203', NULL, NULL, 45.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'US$'),
(4772, 341, 1, 6, 55, 'LLANTA ROADSHINE 205/60R15 RS906 91H', NULL, NULL, 'LH0202', NULL, NULL, 47.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'S/.'),
(4773, 340, 1, 6, 55, 'LLANTA BOTO 195/60R15 GENESYS 88V', NULL, NULL, 'LH0201', NULL, NULL, 37.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'S/.'),
(4774, 279, 1, 6, 55, 'LLANTA GOLDWAY 185/65R15 G2001 88H', NULL, NULL, 'LH0200', NULL, NULL, 43.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'S/.'),
(4775, 340, 1, 6, 55, 'LLANTA BOTO 185/65R15 GENESYS 88H', NULL, NULL, 'LH0199', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'S/.'),
(4776, 311, 1, 6, 55, 'LLANTA APLUS 185/65R15 A606 88H', NULL, NULL, 'LH0198', NULL, NULL, 32.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R15', 'US$'),
(4777, 332, 1, 6, 55, 'LLANTA CATCHGRE 195/60R15 WINDFORSE 88H', NULL, NULL, 'LH0197', NULL, NULL, 44.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R15', 'S/.'),
(4778, 290, 1, 6, 55, 'LLANTA TRIANGLE 205/60R15 TR928 91H', NULL, NULL, 'LH0196', NULL, NULL, 52.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'US$'),
(4779, 336, 1, 6, 55, 'LLANTA MAXTREK 195R14 SU810 106/104S 8PR', NULL, NULL, 'LH0194', NULL, NULL, 61.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'S/.'),
(4780, 291, 1, 6, 55, 'LLANTA HIFLY 205/70R14 HF201 95H', NULL, NULL, 'LH0193', NULL, NULL, 50.82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R14', 'S/.'),
(4781, 312, 1, 6, 55, 'LLANTA MIRAGE 185/70R14 MR162 88H', NULL, NULL, 'LH0190', NULL, NULL, 36.28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4782, 332, 1, 6, 55, 'LLANTA CATCHGRE 195/60R14 WINDFORSE 86H', NULL, NULL, 'LH0189', NULL, NULL, 44.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'S/.'),
(4783, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 175/65R14 ECOPIA 82T', NULL, NULL, 'LH0188', NULL, NULL, 77.98, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'S/.'),
(4784, 341, 1, 6, 55, 'LLANTA ROADSHINE 195/60R14 RS906 86H', NULL, NULL, 'LH0187', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'S/.'),
(4785, 330, 1, 6, 55, 'LLANTA KUMHO 175/70R14 SOLUS KH17 84T', NULL, NULL, 'LH0186', NULL, NULL, 45.47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'S/.'),
(4786, 340, 1, 6, 55, 'LLANTA BOTO 195/60R14 GENESYS 86H', NULL, NULL, 'LH0185', NULL, NULL, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'S/.'),
(4787, 313, 1, 6, 55, 'LLANTA GENERAL 185/70R14 ALTIMAX 88T', NULL, NULL, 'LH0184', NULL, NULL, 41.58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4788, 271, 1, 6, 55, 'LLANTA LING LONG 165/70R14 L666 87R', NULL, NULL, 'LH0183', NULL, NULL, 52.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/70R14', 'S/.'),
(4789, 336, 1, 6, 55, 'LLANTA MAXTREK 175/70R14 SU830 84T', NULL, NULL, 'LH0182', NULL, NULL, 45.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R14', 'S/.'),
(4790, 313, 1, 6, 55, 'LLANTA GENERAL 265/75R16 MT GRABBER ROJAS', NULL, NULL, 'LH0181', NULL, NULL, 234, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(4791, 313, 1, 6, 55, 'LLANTA GENERAL 265/70R16 AT GRABBER', NULL, NULL, 'LH0180', NULL, NULL, 124.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(4792, 281, 1, 6, 55, 'LLANTA DUNLOP 245/75R16 AT GRANDTREK', NULL, NULL, 'LH0179', NULL, NULL, 126.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4793, 281, 1, 6, 55, 'LLANTA DUNLOP 265/70R16 MT GRANDTREK', NULL, NULL, 'LH0178', NULL, NULL, 162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(4794, 291, 1, 6, 55, 'LLANTA HIFLY 245/75R16 AT VIGOROUS', NULL, NULL, 'LH0177', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(4795, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 225/70R16 HT DUELER', NULL, NULL, 'LH0172', NULL, NULL, 160.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'US$'),
(4796, 339, 1, 6, 55, 'LLANTA ACCELERA 225/55R16 ALPHA 99W', NULL, NULL, 'LH0171', NULL, NULL, 85.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/55R16', 'S/.'),
(4797, 312, 1, 6, 55, 'LLANTA MIRAGE 205/60R16 MR162 92V', NULL, NULL, 'LH0170', NULL, NULL, 47.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R16', 'US$'),
(4798, 336, 1, 6, 55, 'LLANTA MAXTREK 285/75R16 MT MUD TRAC', NULL, NULL, 'LH0169', NULL, NULL, 142.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '285/75R16', 'S/.'),
(4799, 336, 1, 6, 55, 'LLANTA MAXTREK 305/70R16 MT 118/115Q MUD TRAC', NULL, NULL, 'LH0168', NULL, NULL, 127.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '305/70R16', 'US$'),
(4800, 272, 1, 6, 55, 'LLANTA INSA TURBO 265/75R16 AT RANGER', NULL, NULL, 'LH0167', NULL, NULL, 164.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(4801, 272, 1, 6, 55, 'LLANTA INSA TURBO 265/70R16 MT DAKAR', NULL, NULL, 'LH0166', NULL, NULL, 138, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'S/.'),
(4802, 318, 1, 6, 55, 'LLANTA FIRESTONE 245/75R16 AT DESTINATION', NULL, NULL, 'LH0165', NULL, NULL, 186, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'S/.'),
(4803, 338, 1, 6, 55, 'LLANTA PIRELLI 255/70R16 MT SCORPION', NULL, NULL, 'LH0163', NULL, NULL, 192, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/70R16', 'S/.'),
(4804, 271, 1, 6, 55, 'LLANTA LING LONG 255/70R16 MT CROSSWIND', NULL, NULL, 'LH0162', NULL, NULL, 123.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/70R16', 'S/.'),
(4805, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 205/70R16 HT DUELER 112S', NULL, NULL, 'LH0161', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R16', 'S/.'),
(4806, 331, 1, 6, 55, 'LLANTA DEESTONE 225/70R16 HT PAYAK 103H', NULL, NULL, 'LH0159', NULL, NULL, 79.27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R16', 'US$'),
(4807, 280, 1, 6, 55, 'LLANTA HANKOOK 175/80R14 OPTIMO K715 88T', NULL, NULL, 'LH0158', NULL, NULL, 64.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/80R14', 'S/.'),
(4808, 336, 1, 6, 55, 'LLANTA MAXTREK 205/70R14 SU830 95H', NULL, NULL, 'LH0157', NULL, NULL, 49.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R14', 'US$'),
(4809, 337, 1, 6, 55, 'LLANTA LAUFENN 185/70R14 FITAS 88T', NULL, NULL, 'LH0156', NULL, NULL, 39.78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4810, 291, 1, 6, 55, 'LLANTA HIFLY 185/70R14 HF201 88H', NULL, NULL, 'LH0155', NULL, NULL, 32.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(4811, 330, 1, 6, 55, 'LLANTA KUMHO 185/70R14 SENSSE 88T', NULL, NULL, 'LH0154', NULL, NULL, 50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(4812, 325, 1, 6, 55, 'LLANTA FEDERAL 205/60R14 SUPER STEEL 89H', NULL, NULL, 'LH0153', NULL, NULL, 58.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'US$'),
(4813, 291, 1, 6, 55, 'LLANTA HIFLY 175/65R14 HF501 82T', NULL, NULL, 'LH0151', NULL, NULL, 28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'S/.'),
(4814, 312, 1, 6, 55, 'LLANTA MIRAGE 185/60R14 MR162 82H', NULL, NULL, 'LH0150', NULL, NULL, 43.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R14', 'S/.'),
(4815, 332, 1, 6, 55, 'LLANTA CATCHGRE 185/60R14 WINDFORSE 82H', NULL, NULL, 'LH0149', NULL, NULL, 40.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R14', 'S/.'),
(4816, 336, 1, 6, 55, 'LLANTA MAXTREK 185/60R14 SU830 82H', NULL, NULL, 'LH0148', NULL, NULL, 45.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R14', 'S/.'),
(4817, 336, 1, 6, 55, 'LLANTA MAXTREK 175/65R14 MAXIMUS 82H', NULL, NULL, 'LH0147', NULL, NULL, 37.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'S/.'),
(4818, 280, 1, 6, 55, 'LLANTA HANKOOK 175/65R14 OPTIMO K715 82T', NULL, NULL, 'LH0146', NULL, NULL, 52.27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'S/.'),
(4819, 336, 1, 6, 55, 'LLANTA MAXTREK 185/65R14 SU830 86H', NULL, NULL, 'LH0145', NULL, NULL, 40.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'S/.'),
(4820, 280, 1, 6, 55, 'LLANTA HANKOOK 165/60R14 KINERGY K425 75H', NULL, NULL, 'LH0142', NULL, NULL, 58.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/60R14', 'S/.'),
(4821, 317, 1, 6, 55, 'LLANTA GOOD YEAR 185/70R14 ASSURANCE 88T', NULL, NULL, 'LH0141', NULL, NULL, 135.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'S/.'),
(4822, 322, 1, 6, 55, 'LLANTA WESTLAKE 195/60R14 H550 86H', NULL, NULL, 'LH0140', NULL, NULL, 52.68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'S/.'),
(4823, 290, 1, 6, 55, 'LLANTA TRIANGLE 195/60R14 TR928 86H', NULL, NULL, 'LH0139', NULL, NULL, 43.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'S/.'),
(4824, 335, 1, 6, 55, 'LLANTA CONTINENTAL 175/65R14 POWER CONTAC 82H', NULL, NULL, 'LH0138', NULL, NULL, 44.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'US$'),
(4825, 334, 1, 6, 55, 'LLANTA GOOD FRIEND 175/65R14 ASTR 82H', NULL, NULL, 'LH0137', NULL, NULL, 31.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'S/.'),
(4826, 280, 1, 6, 55, 'LLANTA HANKOOK 185/65R14 OPTIMO02 86H', NULL, NULL, 'LH0136', NULL, NULL, 47.21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'US$'),
(4827, 333, 1, 6, 55, 'LLANTA WANDA 185/70R14 WR080 88T', NULL, NULL, 'LH0135', NULL, NULL, 30.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4828, 290, 1, 6, 55, 'LLANTA TRIANGLE 185/70R14 TR928 92H', NULL, NULL, 'LH0134', NULL, NULL, 41.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4829, 290, 1, 6, 55, 'LLANTA TRIANGLE 185/65R14 TR928 86H', NULL, NULL, 'LH0133', NULL, NULL, 39.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'US$'),
(4830, 326, 1, 6, 55, 'LLANTA DURUN 165/60R14 CLIMAX T90A 75H', NULL, NULL, 'LH0132', NULL, NULL, 40, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/60R14', 'US$'),
(4831, 330, 1, 6, 55, 'LLANTA KUMHO 165/60R14 SOLUS K117 75H', NULL, NULL, 'LH0131', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/60R14', 'US$'),
(4832, 312, 1, 6, 55, 'LLANTA MIRAGE 195/70R15 MR2000 8PR', NULL, NULL, 'LH0130', NULL, NULL, 75.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/70R15', 'US$'),
(4833, 312, 1, 6, 55, 'LLANTA MIRAGE 195/65R15 MR162 91W', NULL, NULL, 'LH0129', NULL, NULL, 40.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'US$'),
(4834, 313, 1, 6, 55, 'LLANTA GENERAL 195/65R15 ALTIMAX 91H', NULL, NULL, 'LH0128', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/65R15', 'US$');
INSERT INTO `Gen_Producto` (`IdProducto`, `IdProductoMarca`, `IdProductoFormaFarmaceutica`, `IdProductoMedicion`, `IdProductoCategoria`, `Producto`, `ProductoDesc`, `ProductoDescCorto`, `CodigoBarra`, `Codigo`, `Dosis`, `PrecioContado`, `PrecioPorMayor`, `StockPorMayor`, `StockMinimo`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `ControlaStock`, `PrecioCosto`, `VentaEstrategica`, `PorcentajeUtilidad`, `IdBloque`, `Modelo`, `Moneda`) VALUES
(4835, 332, 1, 6, 55, 'LLANTA CATCHGRE 205/60R15 WINDFORSE 91V', NULL, NULL, 'LH0127', NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'US$'),
(4836, 313, 1, 6, 55, 'LLANTA GENERAL 205/60R15 ALTIMAX 91H', NULL, NULL, 'LH0126', NULL, NULL, 47.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R15', 'US$'),
(4837, 322, 1, 6, 55, 'LLANTA WESTLAKE 205/55R15 H660 88V', NULL, NULL, 'LH0125', NULL, NULL, 46.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R15', 'US$'),
(4838, 280, 1, 6, 55, 'LLANTA HANKOOK 185/55R15 VENTUS 82V', NULL, NULL, 'LH0124', NULL, NULL, 74.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/55R15', 'US$'),
(4839, 273, 1, 6, 55, 'LLANTA FALKEN 205/50R15 ZIEX 86V', NULL, NULL, 'LH0123', NULL, NULL, 81.26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'US$'),
(4840, 331, 1, 6, 55, 'LLANTA DEESTONE 195/55R15 VINCENTE 85V', NULL, NULL, 'LH0122', NULL, NULL, 50.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/55R15', 'US$'),
(4841, 330, 1, 6, 55, 'LLANTA KUMHO 205/50R15 ECSTA KU31 86V', NULL, NULL, 'LH0121', NULL, NULL, 74.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/50R15', 'US$'),
(4842, 329, 1, 6, 55, 'LLANTA THUNDERER 185/55R15 MANCHE II 82V', NULL, NULL, 'LH0120', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/55R15', 'US$'),
(4843, 312, 1, 6, 55, 'LLANTA MIRAGE 195/50R15 MR182 86V', NULL, NULL, 'LH0119', NULL, NULL, 59.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/50R15', 'US$'),
(4844, 275, 1, 6, 55, 'LLANTA TOYO TYRES 195/50R15 PROXES 82V', NULL, NULL, 'LH0118', NULL, NULL, 102.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/50R15', 'US$'),
(4845, 280, 1, 6, 55, 'LLANTA HANKOOK 6.50R14 AVOI RADIAL', NULL, NULL, 'LH0117', NULL, NULL, 118.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50R14', 'US$'),
(4846, 313, 1, 6, 55, 'LLANTA GENERAL 215/75R14 AT GRABBER', NULL, NULL, 'LH0116', NULL, NULL, 86.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/75R14', 'US$'),
(4847, 280, 1, 6, 55, 'LLANTA HANKOOK 195R14 RAO08 106/104R 8PR', NULL, NULL, 'LH0114', NULL, NULL, 112.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'US$'),
(4848, 291, 1, 6, 55, 'LLANTA HIFLY 195R14 SUPER2000 106/104R 8PR', NULL, NULL, 'LH0113', NULL, NULL, 49.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R14', 'US$'),
(4849, 273, 1, 6, 55, 'LLANTA FALKEN 205/60R14 ZIEXX 88H', NULL, NULL, 'LH0111', NULL, NULL, 81.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'US$'),
(4850, 275, 1, 6, 55, 'LLANTA TOYO TYRES 205/60R14 PROXES 85H', NULL, NULL, 'LH0110', NULL, NULL, 79.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'US$'),
(4851, 275, 1, 6, 55, 'LLANTA TOYO TYRES 195/60R14 PROXES 85H', NULL, NULL, 'LH0109', NULL, NULL, 74.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'US$'),
(4852, 328, 1, 6, 55, 'LLANTA FUZION 185/65R14 TURING 86T', NULL, NULL, 'LH0108', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/65R14', 'US$'),
(4853, 290, 1, 6, 55, 'LLANTA TRIANGLE 165/70R14 TR928 85T', NULL, NULL, 'LH0107', NULL, NULL, 41.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/70R14', 'US$'),
(4854, 290, 1, 6, 55, 'LLANTA TRIANGLE 175/65R14 TR928 82H', NULL, NULL, 'LH0106', NULL, NULL, 38.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/65R14', 'US$'),
(4855, 317, 1, 6, 55, 'LLANTA GOOD YEAR 195/60R14 VENTURA 86H', NULL, NULL, 'LH0105', NULL, NULL, 63.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/60R14', 'US$'),
(4856, 270, 1, 6, 55, 'LLANTA NEXEN 205/60R14 CP641 88H', NULL, NULL, 'LH0104', NULL, NULL, 68.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/60R14', 'US$'),
(4857, 327, 1, 6, 55, 'LLANTA BRIDGESTONE 235/65R18 HT DUELER 104T', NULL, NULL, 'LH0103', NULL, NULL, 186.01, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R18', 'US$'),
(4858, 280, 1, 6, 55, 'LLANTA HANKOOK 245/60R18 HT DYNAPRO 105H', NULL, NULL, 'LH0102', NULL, NULL, 141.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/60R18', 'US$'),
(4859, 291, 1, 6, 55, 'LLANTA HIFLY 235/60R18 HT HP801 107V', NULL, NULL, 'LH0101', NULL, NULL, 61.69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R18', 'US$'),
(4860, 326, 1, 6, 55, 'LLANTA DURUN 235/60R18 HT K313 103H', NULL, NULL, 'LH0100', NULL, NULL, 69.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R18', 'US$'),
(4861, 280, 1, 6, 55, 'LLANTA HANKOOK 255/55R18 VENTUS ESAT 109Y', NULL, NULL, 'LH0099', NULL, NULL, 131.72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/55R18', 'US$'),
(4862, 280, 1, 6, 55, 'LLANTA HANKOOK 235/55R18 OPTIMO H426 100H', NULL, NULL, 'LH0098', NULL, NULL, 130.37, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/55R18', 'US$'),
(4863, 271, 1, 6, 55, 'LLANTA LING LONG 255/45R18 RADIAL TUBELES 103W (DEPORTIVA)', NULL, NULL, 'LH0097', NULL, NULL, 88.79, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '255/45R18', 'US$'),
(4864, 325, 1, 6, 55, 'LLANTA FEDERAL 225/45R18 SUPER STEEL 91W (DEPORTIVA)', NULL, NULL, 'LH0096', NULL, NULL, 99.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/45R18', 'US$'),
(4865, 290, 1, 6, 55, 'LLANTA TRIANGLE 225/40R18 RADIAL TUBELES 92V (DEPORTIVA)', NULL, NULL, 'LH0095', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/40R18', 'US$'),
(4866, 325, 1, 6, 55, 'LLANTA FEDERAL 215/40R18 SUPER STEEL 85W (DEPORTIVA)', NULL, NULL, 'LH0094', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '215/40R18', 'US$'),
(4867, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.50-15 POS CHASQUI HI MILER CT162', NULL, NULL, 'LH0093', NULL, NULL, 524.69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-15', 'S/.'),
(4868, 289, 1, 6, 55, 'LLANTA OTANI 7.50-15 POS 120/123K 14PR', NULL, NULL, 'LH0092', NULL, NULL, 172.87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-15', 'US$'),
(4869, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 7.50-15 MIX 121/129C 14PR', NULL, NULL, 'LH0091', NULL, NULL, 142, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-15', 'US$'),
(4870, 324, 1, 6, 55, 'LLANTA BCT 7.00R16 DEL 117/116N JINGLUN 12PR', NULL, NULL, 'LH0090', NULL, NULL, 126, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00R16', 'US$'),
(4871, 323, 1, 6, 55, 'LLANTA MALHOTRA 7.50-15 POS 121/119J 14PR', NULL, NULL, 'LH0089', NULL, NULL, 136.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.50-15', 'US$'),
(4872, 283, 1, 6, 55, 'LLANTA SAMSON 7.00-14 PAN TRAKER POWER OB105', NULL, NULL, 'LH0088', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-14', 'US$'),
(4873, 322, 1, 6, 55, 'LLANTA WESTLAKE 6.50-16 DEL CR892 CLIDA', NULL, NULL, 'LH0086', NULL, NULL, 111.72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-16', 'US$'),
(4874, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.00-15 POS CHASQUI CT162 10PR', NULL, NULL, 'LH0085', NULL, NULL, 410.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-15', 'S/.'),
(4875, 322, 1, 6, 55, 'LLANTA WESTLAKE 6.50R15 DEL ST313 CLIMAX 10PR', NULL, NULL, 'LH0084', NULL, NULL, 129.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50R15', 'US$'),
(4876, 281, 1, 6, 55, 'LLANTA DUNLOP 265/70R16 AT GRANDTREK', NULL, NULL, 'LH0083', NULL, NULL, 123.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R16', 'US$'),
(4877, 321, 1, 6, 55, 'LLANTA XCEED 6.50-14 DEL XD-711 8PR', NULL, NULL, 'LH0082', NULL, NULL, 90.79, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(4878, 283, 1, 6, 55, 'LLANTA SAMSON 7.00-15 PAN TAKER PLUS 12PR', NULL, NULL, 'LH0081', NULL, NULL, 138, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-15', 'US$'),
(4879, 289, 1, 6, 55, 'LLANTA OTANI 6.00-14 POS U77 8PR', NULL, NULL, 'LH0080', NULL, NULL, 85.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.00-14', 'US$'),
(4880, 320, 1, 6, 55, 'LLANTA TEXXAN 6.50-14 POS LV912 102/100J 8PR', NULL, NULL, 'LH0079', NULL, NULL, 80.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(4881, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 5.60-15 AUTOPISTA 78L', NULL, NULL, 'LH0078', NULL, NULL, 49.04, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5.60-15', 'US$'),
(4882, 319, 1, 6, 55, 'LLANTA CHAO YANG 6.00-13 POS CL885 6PR', NULL, NULL, 'LH0077', NULL, NULL, 63.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.00-13', 'US$'),
(4883, 318, 1, 6, 55, 'LLANTA FIRESTONE 205/70R14 F570 93T', NULL, NULL, 'LH0076', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R14', 'US$'),
(4884, 317, 1, 6, 55, 'LLANTA GOOD YEAR 7.00-15 DEL CAMINERA CT176 10PR', NULL, NULL, 'LH0075', NULL, NULL, 379.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00-15', 'S/.'),
(4885, 316, 1, 6, 55, 'LLANTA UNITED 6.50-14 DEL UT702 10PR', NULL, NULL, 'LH0074', NULL, NULL, 77.52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-14', 'US$'),
(4886, 311, 1, 6, 55, 'LLANTA APLUS 295/80R22.5 POS D801 152/149L', NULL, NULL, 'LH0073', NULL, NULL, 205.13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '295/80R22.5', 'US$'),
(4887, 315, 1, 6, 55, 'LLANTA WILD COUNTRY 245/75R16 MT RADIAL', NULL, NULL, 'LH0072', NULL, NULL, 144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/75R16', 'US$'),
(4888, 314, 1, 6, 55, 'LLANTA COMFORSER 235/75R15 AT CF3000', NULL, NULL, 'LH0071', NULL, NULL, 99.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R15', 'US$'),
(4889, 313, 1, 6, 55, 'LLANTA GENERAL 235/65R17 HT GRABBER 108H', NULL, NULL, 'LH0070', NULL, NULL, 112.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(4890, 281, 1, 6, 55, 'LLANTA DUNLOP 235/55R19 GRANDTREK 105T', NULL, NULL, 'LH0069', NULL, NULL, 266.88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/55R19', 'US$'),
(4891, 312, 1, 6, 55, 'LLANTA MIRAGE 7.00R16 MIX MG702 14PR 108/114M', NULL, NULL, 'LH0068', NULL, NULL, 109.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7.00R16', 'US$'),
(4892, 311, 1, 6, 55, 'LLANTA APLUS 295/80R22.5 DEL S201 152/149M', NULL, NULL, 'LH0067', NULL, NULL, 183.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '295/80R22.5', 'US$'),
(4893, 278, 1, 6, 55, 'LLANTA HAIDA 235/65R17 HT HD815 104H', NULL, NULL, 'LH0066', NULL, NULL, 79.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(4894, 312, 1, 6, 55, 'LLANTA MIRAGE 225/65R17 HT HIGHWAY 102H', NULL, NULL, 'LH0065', NULL, NULL, 68.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'US$'),
(4895, 272, 1, 6, 55, 'LLANTA INSA TURBO 225/70R15 HT RAPID', NULL, NULL, 'LH0064', NULL, NULL, 75.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R15', 'US$'),
(4896, 311, 1, 6, 55, 'LLANTA APLUS 195R15 A867 106/104L 8PR', NULL, NULL, 'LH0063', NULL, NULL, 55.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'US$'),
(4897, 310, 1, 6, 55, 'LLANTA MARSHALL 195R15 RADIAL 106/104R 6PR LT', NULL, NULL, 'LH0062', NULL, NULL, 86.49, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195R15', 'US$'),
(4898, 285, 1, 6, 55, 'LLANTA GOODRIDE 225/45R17 SV308 94W (DEPORTIVO)', NULL, NULL, 'LH0061', NULL, NULL, 62.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/45R17', 'US$'),
(4899, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 5.60-12 AUTOPISTA 63L', NULL, NULL, 'LH0060', NULL, NULL, 34.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5.60-12', 'US$'),
(4900, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 6.50-13 AUTOPISTA 88L', NULL, NULL, 'LH0059', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6.50-13', 'US$'),
(4901, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 185/70R14 T70 RADIAL 88S', NULL, NULL, 'LH0058', NULL, NULL, 54.96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R14', 'US$'),
(4902, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 165/65R13 T65 PLUS 77S', NULL, NULL, 'LH0057', NULL, NULL, 44.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '165/65R13', 'US$'),
(4903, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 155/70R12 T70 RADIAL 73S TL', NULL, NULL, 'LH0056', NULL, NULL, 36.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '155/70R12', 'US$'),
(4904, 309, 1, 6, 52, 'ARO ORIGINAL MITSUBISHI 16X6H 139.7', NULL, NULL, 'ARO229', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'ORIGINAL', 'S/.'),
(4905, 307, 1, 6, 52, 'ARO ORIGINAL HYUNDAI 16X5H 114.3', NULL, NULL, 'ARO228', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'ORIGINAL', 'S/.'),
(4906, 308, 1, 6, 52, 'ARO ORIGINAL KIA 18X5H 114.3', NULL, NULL, 'ARO227', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'ORIGINAL', 'S/.'),
(4907, 308, 1, 6, 52, 'ARO ORIGINAL KIA 15X4H 100.0', NULL, NULL, 'ARO226', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'ORIGINAL', 'S/.'),
(4908, 308, 1, 6, 52, 'ARO ORIGINAL KIA 14X4H 100.0', NULL, NULL, 'ARO225', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'ORIGINAL', 'S/.'),
(4909, 307, 1, 6, 52, 'ARO ORIGINAL HYUNDAI 15X5H 114.3', NULL, NULL, 'ARO224', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'ORIGINAL', 'S/.'),
(4910, 306, 1, 6, 52, 'ARO ORIGINAL TOYOTA 17X5H 114.3', NULL, NULL, 'ARO223', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'ORIGINAL', 'S/.'),
(4911, 298, 1, 6, 52, 'ARO BELEN WHEELS 7100 15X8.0 4X100+114.3', NULL, NULL, 'ARO222', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7100', 'S/.'),
(4912, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-146 14X7.5 ET0 139.7X6H BL-K', NULL, NULL, 'ARO221', NULL, NULL, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '146', 'S/.'),
(4913, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-5605 20X10.0 ET0 127.0X5H MJ-S', NULL, NULL, 'ARO220', NULL, NULL, 422.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5605', 'US$'),
(4914, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-9145 17X7.5 ETO 100+114.3X8H B-P', NULL, NULL, 'ARO219', NULL, NULL, 425, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9145', 'US$'),
(4915, 305, 1, 6, 52, 'ARO ZEHELNDORF WHEELS ZH-9128 20X9.0 ET0 139.7X6H B-P', NULL, NULL, 'ARO217', NULL, NULL, 141, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9128', 'S/.'),
(4916, 305, 1, 6, 52, 'ARO ZEHELNDORF WHEELS ZH-409 16X8.0 ET0 139.7X6H VB-P', NULL, NULL, 'ARO216', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '409', 'S/.'),
(4917, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-9147 20X9.0 ET0 6X139.7 B-P', NULL, NULL, 'ARO215', NULL, NULL, 1018.18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9147', 'US$'),
(4918, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-9148 17X9.0 ETO 139.7X6H BL-K', NULL, NULL, 'ARO214', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9148', 'S/.'),
(4919, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-9147 17X9.0 ET0 139.7X6H B-P', NULL, NULL, 'ARO213', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9147', 'S/.'),
(4920, 305, 1, 6, 52, 'ARO ZEHLENDORF WHEELS ZH-9147 17X9.0 ET0 139.7X6H BL-K', NULL, NULL, 'ARO212', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9147', 'S/.'),
(4921, 304, 1, 6, 52, 'ARO PEPE RACING 6602 16X7.0 ET40 10H100+108.3 MI-B', NULL, NULL, 'ARO211', NULL, NULL, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6602', 'S/.'),
(4922, 304, 1, 6, 52, 'ARO PEPE RACING 5450 16X7.5 ET35 5H114.3 M-B', NULL, NULL, 'ARO210', NULL, NULL, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5450', 'S/.'),
(4923, 304, 1, 6, 52, 'ARO PEPE RACING 154 14X5.5 ET38 8H100+114.3 G-L', NULL, NULL, 'ARO209', NULL, NULL, 55.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '154', 'S/.'),
(4924, 304, 1, 6, 52, 'ARO PEPE RACING 806 13X6.0 ET35 8H100+114.3 B-F', NULL, NULL, 'ARO208', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '806', 'S/.'),
(4925, 304, 1, 6, 52, 'ARO PEPE RACING 593 13X6.0 ET35 4H100.0 B-L', NULL, NULL, 'ARO207', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '593', 'S/.'),
(4926, 304, 1, 6, 52, 'ARO PEPE RACING 228 13X6.0 ET35 4H114.3 B-F', NULL, NULL, 'ARO206', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '228', 'S/.'),
(4927, 296, 1, 6, 52, 'ARO PDW W-1009158 15X8 6H ET0 6H139.7 EM-D', NULL, NULL, 'ARO205', NULL, NULL, 85.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1009158', 'S/.'),
(4928, 296, 1, 6, 52, 'ARO PDW W-621355 16X6.5 ET40 8H100+114.3 MC-F', NULL, NULL, 'ARO204', NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '621355', 'S/.'),
(4929, 296, 1, 6, 52, 'ARO PDW W-5849142 15X6.5 ET35 8H100+108.0 C-B', NULL, NULL, 'ARO203', NULL, NULL, 114.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5849142', 'S/.'),
(4930, 296, 1, 6, 52, 'ARO PDW W-351956 13X6 ET70 4H114.3 M-B', NULL, NULL, 'ARO202', NULL, NULL, 47.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '351956', 'S/.'),
(4931, 296, 1, 6, 52, 'ARO PDW W-6902 16X7.5 ET40 10H120.5+114.3 C-K', NULL, NULL, 'ARO201', NULL, NULL, 125.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6902', 'S/.'),
(4932, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1180 20X9.5 ET40 114.3X5H HS-1', NULL, NULL, 'ARO200', NULL, NULL, 168.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1180', 'S/.'),
(4933, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1225 17X7.5 ET38 114.3X5H B-4', NULL, NULL, 'ARO199', NULL, NULL, 95.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1225', 'S/.'),
(4934, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1502 17X7.0 ET38 114.3X5H B4-X', NULL, NULL, 'ARO198', NULL, NULL, 97.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1502', 'S/.'),
(4935, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1523 17X7.5 ET10 139.7X6H B3-X', NULL, NULL, 'ARO197', NULL, NULL, 104.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1523', 'S/.'),
(4936, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1578 17X7.5 ET38 114.3X5H B-4', NULL, NULL, 'ARO196', NULL, NULL, 95.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1578', 'S/.'),
(4937, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1538 16X6.5 ET35 100+114.3X8H B-4', NULL, NULL, 'ARO195', NULL, NULL, 81.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1538', 'S/.'),
(4938, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1489 16X7.0 ET40 100X4H B-4', NULL, NULL, 'ARO194', NULL, NULL, 81.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1489', 'S/.'),
(4939, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1288 16X7.0 ET40 100+114.3X8H B-1', NULL, NULL, 'ARO193', NULL, NULL, 83.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1288', 'S/.'),
(4940, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1122 16X7.0 ET38 100+114.3X8H A-G', NULL, NULL, 'ARO192', NULL, NULL, 81.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1122', 'S/.'),
(4941, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-2804 15X6.5 ET38 100+114.3X8H HS-1', NULL, NULL, 'ARO191', NULL, NULL, 71.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2804', 'S/.'),
(4942, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-2023 15X6.5 ET38 100+114.3X8H GR-X', NULL, NULL, 'ARO190', NULL, NULL, 71.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2023', 'S/.'),
(4943, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1411 15X6.0 ET38 100.0X4H B-4', NULL, NULL, 'ARO189', NULL, NULL, 71.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1411', 'S/.'),
(4944, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1169 15X6.5 ET40 100+114.3X8H B4-D', NULL, NULL, 'ARO188', NULL, NULL, 72.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1169', 'S/.'),
(4945, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1101 15X6.5 ET40 100+114.3X8H HS-1', NULL, NULL, 'ARO187', NULL, NULL, 71.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1101', 'S/.'),
(4946, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-2023 14X6.0 ET38 100+114.3X8H HS-1', NULL, NULL, 'ARO186', NULL, NULL, 58.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2023', 'S/.'),
(4947, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-1411 14X5.5 ET40 100X4H B-4', NULL, NULL, 'ARO185', NULL, NULL, 58.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1411', 'S/.'),
(4948, 303, 1, 6, 52, 'ARO MAZZARO WHEELS MZ-2804 13X5.5 ET38 100+108.0X8H HS-1', NULL, NULL, 'ARO184', NULL, NULL, 49.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2804', 'S/.'),
(4949, 302, 1, 6, 52, 'ARO INDIANA WHEELS IN-2028 15X6.5 ET38 100+114.3X8H B4-X', NULL, NULL, 'ARO183', NULL, NULL, 73.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2028', 'S/.'),
(4950, 302, 1, 6, 52, 'ARO INDIANA WHEELS IN-1545 15X6.0 ET38 100.0X4H B-4', NULL, NULL, 'ARO182', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1545', 'S/.'),
(4951, 302, 1, 6, 52, 'ARO INDIANA WHEELS IN-1156 14X7.0 ET10 139.7X6H B-4', NULL, NULL, 'ARO181', NULL, NULL, 67.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1156', 'S/.'),
(4952, 302, 1, 6, 52, 'ARO INDIANA WHEELS IN-2803 16X7.0 ET10 114.3X5H B-4', NULL, NULL, 'ARO180', NULL, NULL, 82.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2803', 'S/.'),
(4953, 302, 1, 6, 52, 'ARO INDIANA WHEELS IN-1307 16X8.0 ET10 139.7X6H B-4', NULL, NULL, 'ARO179', NULL, NULL, 91.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1307', 'S/.'),
(4954, 302, 1, 6, 52, 'ARO INDIANA WHEELS IN-2013 13X5.5 ET35 100+114.3X8H B-4', NULL, NULL, 'ARO178', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2013', 'US$'),
(4955, 302, 1, 6, 52, 'ARO INDIANA WHEELS IN-469 15X6.5 ET35 100+114.3X8H MG-M', NULL, NULL, 'ARO177', NULL, NULL, 69.67, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '469', 'S/.'),
(4956, 302, 1, 6, 52, 'ARO INDIANA WHEELS IN-372 16X8.0 ET40 100+114.3 4X8H B-P', NULL, NULL, 'ARO176', NULL, NULL, 84.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '372', 'S/.'),
(4957, 299, 1, 6, 52, 'ARO DRAGON WHEELS 391 15X6.5 5X100.0 33 66 JRT-4', NULL, NULL, 'ARO175', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '391', 'US$'),
(4958, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3195 20X8.5X114.3 55 22 BF-K', NULL, NULL, 'ARO174', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3195', 'US$'),
(4959, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3717 15X6.5 5X100.0 33 99 GH-L', NULL, NULL, 'ARO173', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3717', 'US$'),
(4960, 299, 1, 6, 52, 'ARO DRAGON WHEELS 535 16X8.0 6X139.7 64 88 B-P', NULL, NULL, 'ARO172', NULL, NULL, 98.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '535', 'US$'),
(4961, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1170 18X8.0 6X139.7 33 55 B-P', NULL, NULL, 'ARO171', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1170', 'S/.'),
(4962, 299, 1, 6, 52, 'ARO DRAGON WHEELS 609 13X6.0 4X100.0 33 55 HS-P', NULL, NULL, 'ARO170', NULL, NULL, 49.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '609', 'US$'),
(4963, 299, 1, 6, 52, 'ARO DRAGON WHEELS 621 14X6.0 6X139.7 33 85 B-P', NULL, NULL, 'ARO169', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '621', 'US$'),
(4964, 299, 1, 6, 52, 'ARO DRAGON WHEELS 722 14X6.0 6X139.7 83 35 BH-B', NULL, NULL, 'ARO168', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '722', 'S/.'),
(4965, 299, 1, 6, 52, 'ARO DRAGON WHEELS 1034 20X8.5 5X114.3 40 76 B-MN', NULL, NULL, 'ARO167', NULL, NULL, 144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1034', 'S/.'),
(4966, 299, 1, 6, 52, 'ARO DRAGON WHEELS 323 15X6.5 4X100+108.0 33 55 B-P', NULL, NULL, 'ARO166', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '323', 'S/.'),
(4967, 299, 1, 6, 52, 'ARO DRAGON WHEELS 965 15X8.0 6X139.7 33 45 BG-H', NULL, NULL, 'ARO164', NULL, NULL, 74.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '965', 'S/.'),
(4968, 299, 1, 6, 52, 'ARO DRAGON WHEELS 5310 16X10.5 6X139.7 33 75 GK-P', NULL, NULL, 'ARO163', NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5310', 'S/.'),
(4969, 299, 1, 6, 52, 'ARO DRAGON WHEELS 965 16X8.0 6X139.7 33 76 B-P', NULL, NULL, 'ARO162', NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '965', 'S/.'),
(4970, 299, 1, 6, 52, 'ARO DRAGON WHEELS 575 20X9.5 5X130.3 33 46 B-P', NULL, NULL, 'ARO161', NULL, NULL, 163.21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '575', 'S/.'),
(4971, 299, 1, 6, 52, 'ARO DRAGON WHEELS 426 16X7.0 4X100+108.0 33 55 HS-P', NULL, NULL, 'ARO160', NULL, NULL, 71.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '426', 'S/.'),
(4972, 299, 1, 6, 52, 'ARO DRAGON WHEELS 210 17X7.5 5X108.0 33 47 B-P', NULL, NULL, 'ARO159', NULL, NULL, 72.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '210', 'S/.'),
(4973, 299, 1, 6, 52, 'ARO DRAGON WHEELS 722 14X6.0 6X139.7 32 45 M-B', NULL, NULL, 'ARO158', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '722', 'US$'),
(4974, 299, 1, 6, 52, 'ARO DRAGON WHEELS 820 13X5.5 4X100+108.0 35 78 VN-H', NULL, NULL, 'ARO157', NULL, NULL, 50.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '820', 'US$'),
(4975, 299, 1, 6, 52, 'ARO DRAGON WHEELS 886 16X7.0 5X100.0 55 32 B-P', NULL, NULL, 'ARO156', NULL, NULL, 98.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '886', 'US$'),
(4976, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3905 15X7.0 8X100+114.3 32 73 OR-P', NULL, NULL, 'ARO155', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3905', 'S/.'),
(4977, 299, 1, 6, 52, 'ARO DRAGON WHEELS 395 15X7.0 8X100+114.3 25 73 OR-P', NULL, NULL, 'ARO154', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '395', 'S/.'),
(4978, 299, 1, 6, 52, 'ARO DRAGON WHEELS 181 16X6.5 8X100+114.3 35 73 MK-M', NULL, NULL, 'ARO153', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '181', 'S/.'),
(4979, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3111 15X6.5 8X100+114.3 25 73 W-P', NULL, NULL, 'ARO152', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3111', 'S/.'),
(4980, 299, 1, 6, 52, 'ARO DRAGON WHEELS 5310 16X8.0 6X139.7 0 110 BL-P', NULL, NULL, 'ARO151', NULL, NULL, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5130', 'S/.'),
(4981, 299, 1, 6, 52, 'ARO DRAGON WHEELS 9134 16X8.0 6X139.7 0 110 B-P', NULL, NULL, 'ARO150', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9134', 'S/.'),
(4982, 299, 1, 6, 52, 'ARO DRAGON WHEELS 3170 15X6.5 8X100+114.3 33 73 OR-P', NULL, NULL, 'ARO149', NULL, NULL, 64.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3170', 'S/.'),
(4983, 299, 1, 6, 52, 'ARO DRAGON WHEELS 357 15X6.5 8X100+114.3 40 73 B-4', NULL, NULL, 'ARO148', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '357', 'S/.'),
(4984, 299, 1, 6, 52, 'ARO DRAGON WHEELS 599 15X6.5 8X100+114.3 38 73 B-P', NULL, NULL, 'ARO147', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '599', 'S/.'),
(4985, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1382 14X6.0 ET35 100+114.3X8H BX-4T', NULL, NULL, 'ARO146', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1382', 'S/.'),
(4986, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-2002 14X6.0 ET40 100+114.3X8H B-4', NULL, NULL, 'ARO145', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2002', 'S/.'),
(4987, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-2003 14X6.0 ET38 100+114.3X8H B-4', NULL, NULL, 'ARO144', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2003', 'S/.'),
(4988, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-2034 14X6.0 ET35 100.0X4H B-4', NULL, NULL, 'ARO143', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2034', 'S/.'),
(4989, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-9015 14X6.0 ET35 100+114.3X8H B3-TR', NULL, NULL, 'ARO142', NULL, NULL, 58.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9015', 'S/.'),
(4990, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1187 13X5.5 ET38 100+114.3X8H HS-1', NULL, NULL, 'ARO141', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1187', 'S/.'),
(4991, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1591 13X5.0 ET36 100.0X4H B-4', NULL, NULL, 'ARO140', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1591', 'S/.'),
(4992, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-8200 13X5.5 ET35 100+114.3X8H B-P', NULL, NULL, 'ARO139', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8200', 'S/.'),
(4993, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1147 16X7.0 ET40 100+114.3X8H B-P', NULL, NULL, 'ARO138', NULL, NULL, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1147', 'S/.'),
(4994, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1156 17X8.0 ET10 139.7X6H B-P', NULL, NULL, 'ARO137', NULL, NULL, 95.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1156', 'S/.'),
(4995, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-1156 14X7.0 ET10 139.7X6H B-P', NULL, NULL, 'ARO136', NULL, NULL, 67.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1156', 'S/.'),
(4996, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-578 18X7.5 ET40 114.3X5H B-P', NULL, NULL, 'ARO135', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '578', 'S/.'),
(4997, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-169 16X6.5 ET20 139.7X6H B-P', NULL, NULL, 'ARO134', NULL, NULL, 91.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '169', 'S/.'),
(4998, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-517 20X8.0 ET35 114.3 X5H B-P', NULL, NULL, 'ARO133', NULL, NULL, 145.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '517', 'S/.'),
(4999, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-2734 16X8.0 ET10 139.7X6H BL-K', NULL, NULL, 'ARO132', NULL, NULL, 91.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2734', 'S/.'),
(5000, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-241 14X6.0 ET35 100+114.3X8H H-S', NULL, NULL, 'ARO131', NULL, NULL, 57.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '241', 'S/.'),
(5001, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-610 13X5.5 ET35 100+114.3X8H B-P', NULL, NULL, 'ARO130', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '610', 'S/.'),
(5002, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-348 16X7.0 ET38 100+114.3X8H B-P', NULL, NULL, 'ARO129', NULL, NULL, 69.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '348', 'S/.'),
(5003, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-355 16X7.0 ET40 100+114.3X8H B6-Z', NULL, NULL, 'ARO128', NULL, NULL, 65.62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '355', 'S/.'),
(5004, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-5913 16X8.0 ET28 139.7X6H B-P', NULL, NULL, 'ARO126', NULL, NULL, 93.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5913', 'S/.'),
(5005, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3719 15X6.5 ET30 100+114.3X8H L-S', NULL, NULL, 'ARO125', NULL, NULL, 210, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3719', 'S/.'),
(5006, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3170 17X7.5 ET30 114.3X5H BL-K', NULL, NULL, 'ARO123', NULL, NULL, 272.34, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3170', 'S/.'),
(5007, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-815 16X7.5 ET35 100+114.3X4H B-P', NULL, NULL, 'ARO122', NULL, NULL, 276.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '815', 'S/.'),
(5008, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-3105 14X6.0 ET38 100+114.3X8H H-S', NULL, NULL, 'ARO121', NULL, NULL, 155.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3105', 'S/.'),
(5009, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-723 15X6.5 ET35 114.3X4H G-S', NULL, NULL, 'ARO120', NULL, NULL, 255.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '723', 'S/.'),
(5010, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-693 16X7.0 ET38 139.7X6H B-L', NULL, NULL, 'ARO119', NULL, NULL, 294, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '693', 'S/.'),
(5011, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-551 20X7.5 ET36 139.7X6H B-K', NULL, NULL, 'ARO117', NULL, NULL, 420, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '551', 'S/.'),
(5012, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-824 18X8.0 ET40 100+114.3X8H B-P', NULL, NULL, 'ARO115', NULL, NULL, 404.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '824', 'S/.'),
(5013, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-355 15X6.5 ET38 100+114.3X8H B-P', NULL, NULL, 'ARO114', NULL, NULL, 232.85, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '355', 'S/.'),
(5014, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-968 14X6.0 ET40 100.0X4H H-S', NULL, NULL, 'ARO113', NULL, NULL, 199.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '968', 'S/.'),
(5015, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-356 16X7.0 ET38 100+114.3X8H G-L', NULL, NULL, 'ARO112', NULL, NULL, 82.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '356', 'US$'),
(5016, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-519 20X8.0 ET30 114.3X5H H-S', NULL, NULL, 'ARO111', NULL, NULL, 630, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '519', 'S/.'),
(5017, 301, 1, 6, 52, 'ARO DEMONIUM WHEELS DH-482 20X8.5 ET42 114.3X5H B-P', NULL, NULL, 'ARO110', NULL, NULL, 528, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '482', 'S/.'),
(5018, 300, 1, 6, 52, 'ARO DARWIN RACING GS-9016BFP 15X6.5 4X100+114.3 ET10', NULL, NULL, 'ARO109', NULL, NULL, 75.72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9016', 'S/.'),
(5019, 300, 1, 6, 52, 'ARO DARWIN RACING F-0522GB 17X7.0 6X139.7 ET25', NULL, NULL, 'ARO108', NULL, NULL, 105.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '0522', 'S/.'),
(5020, 300, 1, 6, 52, 'ARO DARWIN RACING GA-1211GMB 15X6.5 4X100+114.3 ET35', NULL, NULL, 'ARO106', NULL, NULL, 74.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1211', 'S/.'),
(5021, 300, 1, 6, 52, 'ARO DARWIN RACING GS-1308HS 15X8.0 4X114.3 ET30', NULL, NULL, 'ARO105', NULL, NULL, 78.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1308', 'S/.'),
(5022, 300, 1, 6, 52, 'ARO DARWIN RACING F-7026BFP 15X6.5 5X100 ET0', NULL, NULL, 'ARO104', NULL, NULL, 78.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7026', 'S/.'),
(5023, 300, 1, 6, 52, 'ARO DARWIN RACING W-894HB 15X6.5 5X100 ET0', NULL, NULL, 'ARO103', NULL, NULL, 77.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '894', 'S/.'),
(5024, 300, 1, 6, 52, 'ARO DARWIN RACING B-441BFP 15X6.5 4X100+114.3 ET38', NULL, NULL, 'ARO102', NULL, NULL, 78.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '441', 'S/.'),
(5025, 300, 1, 6, 52, 'ARO DARWIN RACING B-808GB 15X6.5 4X100+114.3 ET35', NULL, NULL, 'ARO101', NULL, NULL, 78.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '808', 'S/.'),
(5026, 300, 1, 6, 52, 'ARO DARWIN RACING GS-138BP 15X6.5 4X100+114.3 ET38', NULL, NULL, 'ARO100', NULL, NULL, 78.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '138', 'S/.'),
(5027, 300, 1, 6, 52, 'ARO DARWIN RACING GS-2038BFP 15X6.5 4X100 ET38', NULL, NULL, 'ARO099', NULL, NULL, 78.36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2038', 'S/.'),
(5028, 300, 1, 6, 52, 'ARO DARWIN RACING GS-1322GBFP 13X5.5 4X100+114.3 ET10', NULL, NULL, 'ARO098', NULL, NULL, 51.48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1322', 'S/.'),
(5029, 300, 1, 6, 52, 'ARO DARWIN RACING GS-2014HS 13X5.5 4X100 ET30', NULL, NULL, 'ARO097', NULL, NULL, 52.67, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2014', 'S/.'),
(5030, 300, 1, 6, 52, 'ARO DARWIN RACING GA-1178MBFP 13X5.5 4X100 ET38', NULL, NULL, 'ARO096', NULL, NULL, 52.67, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1178', 'S/.'),
(5031, 300, 1, 6, 52, 'ARO DARWIN RACING B-528BFP 17X8.0 5X114.3 ET30', NULL, NULL, 'ARO095', NULL, NULL, 110.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '528', 'US$'),
(5032, 300, 1, 6, 52, 'ARO DARWIN RACING TRIANGULAR 8-SPOKE 16X7.0 6X139.7', NULL, NULL, 'ARO094', NULL, NULL, 46.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'SPOKE', 'US$'),
(5033, 300, 1, 6, 52, 'ARO DARWIN RACING GA-1109MBP 14X6.0 4X100+114.3 ET40', NULL, NULL, 'ARO093', NULL, NULL, 216, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1109', 'S/.'),
(5034, 300, 1, 6, 52, 'ARO DARWIN RACING GA-1207HS 16X7.0 6X139.7 ET38', NULL, NULL, 'ARO092', NULL, NULL, 96.12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1207', 'US$'),
(5035, 300, 1, 6, 52, 'ARO DARWIN RACING GA-068GRT 16X7.0 4X100+114.3 ET0', NULL, NULL, 'ARO091', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '068', 'S/.'),
(5036, 300, 1, 6, 52, 'ARO DARWIN RACING GA-1203BP 14X6.0 4X100.0 ET38', NULL, NULL, 'ARO090', NULL, NULL, 69.64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1203', 'US$'),
(5037, 300, 1, 6, 52, 'ARO DARWIN RACING GS-2018GPS 13X5.5 4X100 ET30', NULL, NULL, 'ARO089', NULL, NULL, 56.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2018', 'S/.'),
(5038, 300, 1, 6, 52, 'ARO DARWIN RACING GA-303GPS 14X6.0 4X100 ET30', NULL, NULL, 'ARO088', NULL, NULL, 64.91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '303', 'US$'),
(5039, 300, 1, 6, 52, 'ARO DARWIN RACING GS-1282BLK 16X7.0 4X100.0 ET40', NULL, NULL, 'ARO087', NULL, NULL, 86.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1282', 'US$'),
(5040, 300, 1, 6, 52, 'ARO DARWIN RACING GS-9016 BLK 14X6.0 4X100+114.3 ET38', NULL, NULL, 'ARO086', NULL, NULL, 64.91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9016', 'US$'),
(5041, 300, 1, 6, 52, 'ARO DARWIN RACING GQ-846GTR 20X9.0 6X139.7 ET30', NULL, NULL, 'ARO085', NULL, NULL, 74.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '846', 'US$'),
(5042, 300, 1, 6, 52, 'ARO DARWIN RACING H-190HSS 16X7.0 4X100+114.3 ET0', NULL, NULL, 'ARO084', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '190', 'S/.'),
(5043, 300, 1, 6, 52, 'ARO DARWIN RACING B-616HS 18X8.0 5X114.3 ET0', NULL, NULL, 'ARO083', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '616', 'S/.'),
(5044, 300, 1, 6, 52, 'ARO DARWIN RACING GQ-723BPS 17X8.0 5X120.3 ET40', NULL, NULL, 'ARO082', NULL, NULL, 110.44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '723', 'US$'),
(5045, 300, 1, 6, 52, 'ARO DARWIN RACING GA-068BLK 16X7.0 5X100+114.3 ET38', NULL, NULL, 'ARO081', NULL, NULL, 74.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '068', 'US$'),
(5046, 300, 1, 6, 52, 'ARO DARWIN RACING F-001HS 17X7.0 5X114.3 ET39', NULL, NULL, 'ARO080', NULL, NULL, 112.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '001', 'S/.'),
(5047, 300, 1, 6, 52, 'ARO DARWIN RACING S-557HS 16X6.5 6X139.7 ET30', NULL, NULL, 'ARO079', NULL, NULL, 82.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '557', 'US$'),
(5048, 300, 1, 6, 52, 'ARO DARWIN RACING GA-519BFP 15X7.0 6X139.7 ET36', NULL, NULL, 'ARO078', NULL, NULL, 89.82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '519', 'US$'),
(5049, 300, 1, 6, 52, 'ARO DARWIN RACING GA-068HS 14X6.0 5X100+114.3 ET30', NULL, NULL, 'ARO077', NULL, NULL, 64.91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '068', 'US$'),
(5050, 300, 1, 6, 52, 'ARO DARWIN RACING GQ-722HS 15X7.0 6X139.7 ET38', NULL, NULL, 'ARO076', NULL, NULL, 92.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '722', 'US$'),
(5051, 300, 1, 6, 52, 'ARO DARWIN RACING GS-1282GSK 16X7.0 4X100 ET40', NULL, NULL, 'ARO075', NULL, NULL, 86.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1282', 'US$'),
(5052, 300, 1, 6, 52, 'ARO DARWIN RACING GQ-723HS 16X8.0 6X139.7 ET40', NULL, NULL, 'ARO074', NULL, NULL, 103.81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '723', 'US$'),
(5053, 300, 1, 6, 52, 'ARO DARWIN RACING GQ-723XS 15X7.0 6X139.7 ET38', NULL, NULL, 'ARO073', NULL, NULL, 92.22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '723', 'US$'),
(5054, 300, 1, 6, 52, 'ARO DARWIN RACING B-348BLK 16X7.0 6X110+114.3 ET40', NULL, NULL, 'ARO072', NULL, NULL, 86.76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '348', 'US$'),
(5055, 300, 1, 6, 52, 'ARO DARWIN RACING GS-1218BFP 18X8.0 5X114.3 ET35', NULL, NULL, 'ARO071', NULL, NULL, 142.84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1218', 'US$'),
(5056, 300, 1, 6, 52, 'ARO DARWIN RACING B-528HS 16X7.0 4X100 ET38', NULL, NULL, 'ARO070', NULL, NULL, 82.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '528', 'S/.'),
(5057, 300, 1, 6, 52, 'ARO DARWIN RACING B-746HSLP 15X7.0 4X100 ET30', NULL, NULL, 'ARO069', NULL, NULL, 75.72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '746', 'S/.'),
(5058, 300, 1, 6, 52, 'ARO DARWIN RACING GA-234BP 15X6.5 4X100+114.3 ET38', NULL, NULL, 'ARO068', NULL, NULL, 74.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '234', 'S/.'),
(5059, 300, 1, 6, 52, 'ARO DARWIN RACING GA-068HS 15X6.5 4X100+114.3 ET35', NULL, NULL, 'ARO067', NULL, NULL, 74.39, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '068', 'S/.'),
(5060, 300, 1, 6, 52, 'ARO DARWIN RACING B-768HS 13X5.5 4X100+114.3 ET30', NULL, NULL, 'ARO066', NULL, NULL, 56.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '768', 'S/.'),
(5061, 298, 1, 6, 52, 'ARO BELEN WHEELS 3277 13X8.0 100+114.3', NULL, NULL, 'ARO065', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3277', 'US$'),
(5062, 298, 1, 6, 52, 'ARO BELEN WHEELS 346 13X5.5 4X100+114.3', NULL, NULL, 'ARO064', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '346', 'US$'),
(5063, 298, 1, 6, 52, 'ARO BELEN WHEELS 8504 13X8.0 4X114.3', NULL, NULL, 'ARO063', NULL, NULL, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8504', 'US$'),
(5064, 298, 1, 6, 52, 'ARO BELEN WHEELS 804 17X8.0 5X100+120.0', NULL, NULL, 'ARO062', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '804', 'US$'),
(5065, 298, 1, 6, 52, 'ARO BELEN WHEELS 9515 17X7.5 5X114.3', NULL, NULL, 'ARO061', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9515', 'US$'),
(5066, 298, 1, 6, 52, 'ARO BELEN WHEELS 3138 15X6.5 4X100+114.3', NULL, NULL, 'ARO060', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3138', 'US$'),
(5067, 299, 1, 6, 52, 'ARO DRAGON WHEELS 9148 20X9.0 ET0 6X139.7 B-P', NULL, NULL, 'ARO059', NULL, NULL, 157.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9148', 'US$'),
(5068, 298, 1, 6, 52, 'ARO BELEN WHEELS 3126 16X8.0 6X139.7', NULL, NULL, 'ARO058', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3126', 'US$');
INSERT INTO `Gen_Producto` (`IdProducto`, `IdProductoMarca`, `IdProductoFormaFarmaceutica`, `IdProductoMedicion`, `IdProductoCategoria`, `Producto`, `ProductoDesc`, `ProductoDescCorto`, `CodigoBarra`, `Codigo`, `Dosis`, `PrecioContado`, `PrecioPorMayor`, `StockPorMayor`, `StockMinimo`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `ControlaStock`, `PrecioCosto`, `VentaEstrategica`, `PorcentajeUtilidad`, `IdBloque`, `Modelo`, `Moneda`) VALUES
(5069, 298, 1, 6, 52, 'ARO BELEN WHEELS 296 17X7.5 4X100+114.3', NULL, NULL, 'ARO057', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '296', 'US$'),
(5070, 298, 1, 6, 52, 'ARO BELEN WHEELS 296 15X6.5 4X100+114.3', NULL, NULL, 'ARO056', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '296', 'US$'),
(5071, 298, 1, 6, 52, 'ARO BELEN WHEELS 903 15X6.5 4X100+114.3', NULL, NULL, 'ARO055', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '903', 'US$'),
(5072, 298, 1, 6, 52, 'ARO BELEN WHEELS 9104 15X8.0 4X100+114.3', NULL, NULL, 'ARO054', NULL, NULL, 80.46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9104', 'US$'),
(5073, 298, 1, 6, 52, 'ARO BELEN WHEELS 243 14X6.0 5X100.0', NULL, NULL, 'ARO053', NULL, NULL, 63.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '243', 'US$'),
(5074, 298, 1, 6, 52, 'ARO BELEN WHEELS 3234 17X7.0 5X100.0', NULL, NULL, 'ARO052', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3234', 'US$'),
(5075, 298, 1, 6, 52, 'ARO BELEN WHEELS 3138 14X6.0 4X100+114.3', NULL, NULL, 'ARO051', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3138', 'US$'),
(5076, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-562 13X5.5 ET30 100X4H BM-F', NULL, NULL, 'ARO050', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '562', 'S/.'),
(5077, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-378 16X6.0 ET30 100X4H B-ML', NULL, NULL, 'ARO049', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '378', 'S/.'),
(5078, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-1085 13X5.5 ET30 100+108X8H A-MF', NULL, NULL, 'ARO048', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1085', 'S/.'),
(5079, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-1073 13X5.5 ET30 100+114X8H AM-F', NULL, NULL, 'ARO047', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1073', 'S/.'),
(5080, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-1073 13X5.5 ET30 100+108X8H B-MF', NULL, NULL, 'ARO046', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '1073', 'S/.'),
(5081, 297, 1, 6, 52, 'ARO ARAZZO WHEELS AH-740 16X7.0 ET30 100X4H B-P', NULL, NULL, 'ARO045', NULL, NULL, 76.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '740', 'S/.'),
(5082, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-4012 14X6.0 ET35 4X100+114.3 B-M', NULL, NULL, 'ARO044', NULL, NULL, 57, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '4012', 'S/.'),
(5083, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-856 17X9.0 ET0 6X139.7 BM-F', NULL, NULL, 'ARO042', NULL, NULL, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '856', 'S/.'),
(5084, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-3831 17X8.0 ET33 4X100.0 B-M', NULL, NULL, 'ARO041', NULL, NULL, 93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3831', 'S/.'),
(5085, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-3831 17X8.0 ET33 5X114.3 B-M', NULL, NULL, 'ARO040', NULL, NULL, 93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '3831', 'S/.'),
(5086, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG1703 16X7.5 ET30 4X100 M-B', NULL, NULL, 'ARO039', NULL, NULL, 83.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG1703', 'S/.'),
(5087, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG1703 15X7.0 ET0 4X100/114.3 B-M', '', '', 'ARO038', '', '', 63, 0, 0, 0, b'0', NULL, NULL, '2018-02-20 11:17:28', 'Jeam', NULL, b'1', 0, b'0', 0, NULL, 'LG1703', 'US$'),
(5088, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-60217 13X5.5 ET35 4X100+114.3 B-M', NULL, NULL, 'ARO037', NULL, NULL, 44.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6021', 'US$'),
(5089, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LGS1605 16X8.0 ET10 6X139.7 BM-F', NULL, NULL, 'ARO036', NULL, NULL, 93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LGS1605', 'S/.'),
(5090, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LGS0804 16X8.0 ET10 6X139.7 BM-F', NULL, NULL, 'ARO035', NULL, NULL, 93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LGS0804', 'S/.'),
(5091, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LGS0504 16X8.0 ET10 6X139.7 BM-F', NULL, NULL, 'ARO034', NULL, NULL, 91.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LGS0504', 'S/.'),
(5092, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LGS0403 16X8.0 ET10 6X139.7 BM-F', NULL, NULL, 'ARO033', NULL, NULL, 91.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LGS0403', 'S/.'),
(5093, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LGS0303 16X8.0 ET10 6X139.7 BM-F', NULL, NULL, 'ARO032', NULL, NULL, 91.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LGS0303', 'S/.'),
(5094, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-8561 16X8.0 ET10 6X139.7 B-M', NULL, NULL, 'ARO031', NULL, NULL, 91.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8561', 'S/.'),
(5095, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-8551 16X8.0 ET10 6X139.7 E-B', NULL, NULL, 'ARO030', NULL, NULL, 89.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8551', 'S/.'),
(5096, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-7180 16X8.0 ET10 6X139.7 B-M', NULL, NULL, 'ARO029', NULL, NULL, 91.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7180', 'S/.'),
(5097, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6652 16X8.0 ET10 5X114.3 B-R', NULL, NULL, 'ARO028', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6652', 'S/.'),
(5098, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6390 16X8.0 ET10 6X139.7 CH-R', NULL, NULL, 'ARO027', NULL, NULL, 124.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6390', 'S/.'),
(5099, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6340 16X8.0 ET10 6X139.7 B-M', NULL, NULL, 'ARO026', NULL, NULL, 79.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6340', 'S/.'),
(5100, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-5021 16X7.0 ET40 4X100 B-M', NULL, NULL, 'ARO025', NULL, NULL, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5021', 'S/.'),
(5101, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-2050 16X7.0 ET35 4X100+114.3 B-M', NULL, NULL, 'ARO024', NULL, NULL, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '2050', 'S/.'),
(5102, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-7192 15X7.5 ET35 4X100+114.3 W-R', NULL, NULL, 'ARO023', NULL, NULL, 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '7192', 'S/.'),
(5103, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6570 14X6.0 ET25 4X100 LW-B', NULL, NULL, 'ARO022', NULL, NULL, 60.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6570', 'S/.'),
(5104, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-32803 14X6.0 4X100 V-W', NULL, NULL, 'ARO021', NULL, NULL, 80.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '32803', 'US$'),
(5105, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-510 13X5.5 ET35 4X100/114.3 B/M', NULL, NULL, 'ARO020', NULL, NULL, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '510', 'US$'),
(5106, 296, 1, 6, 52, 'ARO PDW W-A67526 20X8.0 ET38 5X114.3 M-B', NULL, NULL, 'ARO019', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '675', 'US$'),
(5107, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-5640 15X8H ET40 MC-B', NULL, NULL, 'ARO018', NULL, NULL, 70.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '5640', 'S/.'),
(5108, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6003 16.6X139 ET15 LS-B', NULL, NULL, 'ARO017', NULL, NULL, 91.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6003', 'S/.'),
(5109, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-6326 16X8.0 ET10 6X139.7 M-B', NULL, NULL, 'ARO016', NULL, NULL, 80.7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '6326', 'S/.'),
(5110, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-376 15.6X139 ET10 FS-B', NULL, NULL, 'ARO015', NULL, NULL, 78.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '376', 'S/.'),
(5111, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-152 17X8H ET35 M-B', NULL, NULL, 'ARO014', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '152', 'S/.'),
(5112, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-146 17X10H ET38 L-B', NULL, NULL, 'ARO013', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '146', 'S/.'),
(5113, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-LG19 17X5H ET35 F-B', NULL, NULL, 'ARO012', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, 'LG19', 'S/.'),
(5114, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-808 16X5H RT38 M-B', NULL, NULL, 'ARO011', NULL, NULL, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '808', 'S/.'),
(5115, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-379 15X8H ET33 M-B', NULL, NULL, 'ARO010', NULL, NULL, 69.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '379', 'S/.'),
(5116, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-183 15X8H ET35 B-M', NULL, NULL, 'ARO009', NULL, NULL, 69.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '183', 'S/.'),
(5117, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-168 15X8H ET35 L-W', NULL, NULL, 'ARO008', NULL, NULL, 69.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '168', 'S/.'),
(5118, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-158 15X8H ET35 M-B', NULL, NULL, 'ARO007', NULL, NULL, 69.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '158', 'S/.'),
(5119, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-131 15X8H ET38 LG-S', NULL, NULL, 'ARO006', NULL, NULL, 69.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '131', 'S/.'),
(5120, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-120 15X8H ET25 L-B', NULL, NULL, 'ARO005', NULL, NULL, 69.3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '120', 'S/.'),
(5121, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-110 15X8H ET15 M-R', NULL, NULL, 'ARO004', NULL, NULL, 69.9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '110', 'S/.'),
(5122, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-122 13X8H ET35 L-B', NULL, NULL, 'ARO003', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '122', 'S/.'),
(5123, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-232 15X8.0 ET01 B-R', NULL, NULL, 'ARO002', NULL, NULL, 70.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '232', 'S/.'),
(5124, 295, 1, 6, 52, 'ARO AMERICAN WHEELS AW-401 13X5.5 ET35 B-P', NULL, NULL, 'ARO001', NULL, NULL, 48.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '401', 'S/.'),
(5125, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 205/70R14 T70 RADIAL 95S', NULL, NULL, 'LH0055', NULL, NULL, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/70R14', 'S/.'),
(5126, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 175/70R13 T70 82S', NULL, NULL, 'LH0054', NULL, NULL, 47.16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175/70R13', 'US$'),
(5127, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 175R13 GT100 RADIAL 66S', NULL, NULL, 'LH0053', NULL, NULL, 46.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '175R13', 'US$'),
(5128, 294, 1, 6, 55, 'LLANTA LIMA CAUCHO 185/70R13 T70 RADIAL 86S', NULL, NULL, 'LH0052', NULL, NULL, 50.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(5129, 293, 1, 6, 55, 'LLANTA BARUM 185/70R13 BRILLANTIS 86T', NULL, NULL, 'LH0051', NULL, NULL, 37.94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/70R13', 'US$'),
(5130, 292, 1, 6, 55, 'LLANTA ORNET 10.00-20 POS L-602 18PR', NULL, NULL, 'LH0049', NULL, NULL, 228, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10.00-20', 'US$'),
(5131, 292, 1, 6, 55, 'LLANTA ORNET 10.00-20 DEL R-503 16PR', NULL, NULL, 'LH0048', NULL, NULL, 263.99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '10.00-20', 'US$'),
(5132, 292, 1, 6, 55, 'LLANTA ORNET 9.00-20 POS L-602 18PR', NULL, NULL, 'LH0047', NULL, NULL, 204, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.00-20', 'US$'),
(5133, 292, 1, 6, 55, 'LLANTA ORNET 9.00-20 DEL R-501 16PR', NULL, NULL, 'LH0046', NULL, NULL, 240, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '9.00-20', 'US$'),
(5134, 291, 1, 6, 55, 'LLANTA HIFLY 8.25R20 MIX HH301 139/137L 16PR', NULL, NULL, 'LH0045', NULL, NULL, 192.59, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R20', 'US$'),
(5135, 290, 1, 6, 55, 'LLANTA TRIANGLE 8.25R20 POS TR690 139/137K', NULL, NULL, 'LH0044', NULL, NULL, 241.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R20', 'US$'),
(5136, 287, 1, 6, 55, 'LLANTA GOODTYRE 8.25R20 MIX 16PR 139/137K', NULL, NULL, 'LH0043', NULL, NULL, 201.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25R20', 'US$'),
(5137, 289, 1, 6, 55, 'LLANTA OTANI 8.25-20 DEL U-77 16PR', NULL, NULL, 'LH0042', NULL, NULL, 220.93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-20', 'US$'),
(5138, 289, 1, 6, 55, 'LLANTA OTANI 8.25-20 POS L-88 16PR', NULL, NULL, 'LH0041', NULL, NULL, 232.56, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '8.25-20', 'US$'),
(5139, 288, 1, 6, 55, 'LLANTA ADVANCE 17.5-24 14PR R4 ESTREME STAR', NULL, NULL, 'LH0039', NULL, NULL, 585.24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17.5-24', 'US$'),
(5140, 287, 1, 6, 55, 'LLANTA GOODTYRE 17.5-25 L-3 20PR R6', NULL, NULL, 'LH0038', NULL, NULL, 662.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '17.5-25', 'US$'),
(5141, 282, 1, 6, 55, 'LLANTA YOKOHAMA 185/60R15 E70 84H', NULL, NULL, 'LH0037', NULL, NULL, 66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185/60R15', 'US$'),
(5142, 280, 1, 6, 55, 'LLANTA HANKOOK 195/50R15 VENTUS 82H', NULL, NULL, 'LH0036', NULL, NULL, 79.42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/50R15', 'US$'),
(5143, 275, 1, 6, 55, 'LLANTA TOYO TYRES 235/60R14 PROXES 96H', NULL, NULL, 'LH0035', NULL, NULL, 104.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/60R14', 'US$'),
(5144, 285, 1, 6, 55, 'LLANTA GOODRIDE 185R14 SC328 102/100Q 8PR', NULL, NULL, 'LH0034', NULL, NULL, 56.1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '185R14', 'US$'),
(5145, 286, 1, 6, 55, 'LLANTA NECSTEL 195/75R14 A/T 93/900N TEEL RADIAL', NULL, NULL, 'LH0033', NULL, NULL, 100.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '195/75R14', 'US$'),
(5146, 285, 1, 6, 55, 'LLANTA GOODRIDE 205/55R16 SV308 94W', NULL, NULL, 'LH0032', NULL, NULL, 52.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '205/55R16', 'US$'),
(5147, 284, 1, 6, 55, 'LLANTA ARMOUR 14.00R24 L2 RX', NULL, NULL, 'LH0031', NULL, NULL, 408, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '14.00-24', 'US$'),
(5148, 283, 1, 6, 55, 'LLANTA SAMSON 12-16.5 12PR L2D SKID STEER', NULL, NULL, 'LH0030', NULL, NULL, 141.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '12-16.5', 'US$'),
(5149, 282, 1, 6, 55, 'LLANTA YOKOHAMA 225/65R17 HT GEOLANDAR 102H', NULL, NULL, 'LH0029', NULL, NULL, 180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/65R17', 'US$'),
(5150, 281, 1, 6, 55, 'LLANTA DUNLOP 225/70R17 AT2 GRANDTREK', NULL, NULL, 'LH0028', NULL, NULL, 144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '225/70R17', 'US$'),
(5151, 276, 1, 6, 55, 'LLANTA LING LONG 275/55R20 L689 117V', NULL, NULL, 'LH0026', NULL, NULL, 168, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/55R20', 'US$'),
(5152, 280, 1, 6, 55, 'LLANTA HANKOOK 245/50R20 VENTUS 102V', NULL, NULL, 'LH0025', NULL, NULL, 186.5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '245/50R20', 'US$'),
(5153, 279, 1, 6, 55, 'LLANTA GOLDWAY 275/45R20 G2003 110V', NULL, NULL, 'LH0024', NULL, NULL, 100.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/45R20', 'US$'),
(5154, 278, 1, 6, 55, 'LLANTA HAIDA 275/40R20 RACING HD609 109W', NULL, NULL, 'LH0023', NULL, NULL, 81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/40R20', 'US$'),
(5155, 277, 1, 6, 55, 'LLANTA ROADSTONE 275/35R20 N7000 102W', NULL, NULL, 'LH0022', NULL, NULL, 192, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/35R20', 'US$'),
(5156, 276, 1, 6, 55, 'LLANTA LING LONG 275/40R20 LL680 106V', NULL, NULL, 'LH0021', NULL, NULL, 139.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/40R20', 'US$'),
(5157, 272, 1, 6, 55, 'LLANTA INSA TURBO 235/65R17 AT DAKAR', NULL, NULL, 'LH0016', NULL, NULL, 114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(5158, 275, 1, 6, 55, 'LLANTA TOYO TYRES 265/65R17 AT OPEN COUNTRY', NULL, NULL, 'LH0015', NULL, NULL, 182.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/65R17', 'US$'),
(5159, 275, 1, 6, 55, 'LLANTA TOYO TYRES 265/75R16 AT OPEN COUNTRY', NULL, NULL, 'LH0014', NULL, NULL, 208.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(5160, 274, 1, 6, 55, 'LLANTA CATCHFORSE 265/75R16 AT WINDFORSE', NULL, NULL, 'LH0013', NULL, NULL, 113.4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/75R16', 'US$'),
(5161, 273, 1, 6, 55, 'LLANTA FALKEN 275/70R16 AT LANDAIR', NULL, NULL, 'LH0012', NULL, NULL, 150, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/70R16', 'US$'),
(5162, 272, 1, 6, 55, 'LLANTA INSA TURBO 235/65R17 HT ECODRIVE', NULL, NULL, 'LH0011', NULL, NULL, 156, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/65R17', 'US$'),
(5163, 271, 1, 6, 55, 'LLANTA LING LONG 235/75R17.5 DEL LLA78 134/139F 16PR', NULL, NULL, 'LH0010', NULL, NULL, 160.8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '235/75R17.5', 'US$'),
(5164, 270, 1, 6, 55, 'LLANTA NEXEN 265/70R18 HT ROADIAN', NULL, NULL, 'LH0006', NULL, NULL, 126, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '265/70R18', 'US$'),
(5165, 269, 1, 6, 55, 'LLANTA HERCULES 275/65R18 AT ALL TRAC', NULL, NULL, 'LH0005', NULL, NULL, 165.6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, b'1', NULL, NULL, NULL, NULL, '275/65R18', 'US$'),
(5211, 269, 1, 6, 52, 'Ejemplo', 'sdfsdf', 'algoss', '646', '461', '0', 30, 25, 0, 0, b'0', '2018-02-13 11:00:00', 'Jeam', NULL, NULL, '1518537600', b'1', 20, b'0', 0, 5, NULL, 'S/.'),
(5212, 280, 1, 6, 55, 'LLANTA HANKOOK 225/70R15 RA08', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-20 10:40:12', 'Jeam', '2018-02-20 11:03:02', 'Jeam', '1519148412', b'1', 0, b'0', 0, NULL, NULL, 'S/.'),
(5213, 280, 1, 6, 55, 'LLANTA HANKOOK 235/60R17V RA33', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-20 10:41:59', 'Jeam', '2018-02-20 11:11:40', 'Jeam', '1519148519', b'1', 0, b'0', 0, NULL, NULL, 'S/.'),
(5214, 280, 1, 6, 55, 'LLANTA HANKOOK 235/65R17H RA33', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-20 10:43:45', 'Jeam', '2018-02-20 11:10:32', 'Jeam', '1519148625', b'1', 0, b'0', 0, NULL, NULL, 'S/.'),
(5215, 280, 1, 6, 55, 'LLANTA HANKOOK 275/55R20T RF10 AT', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-20 10:45:13', 'Jeam', '2018-02-20 11:09:55', 'Jeam', '1519148713', b'1', 0, b'0', 0, NULL, NULL, 'S/.'),
(5216, 280, 1, 6, 55, 'LLANTA HANKOOK 175/70R13T K715', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-20 11:07:59', 'Jeam', NULL, NULL, '1519150079', b'1', 0, b'0', 0, NULL, NULL, 'S/.'),
(5217, 412, 1, 6, 55, 'LLANTA DORADO 185/70R13 ', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-20 11:40:18', 'Jeam', NULL, NULL, '1519152018', b'1', 0, b'0', 0, NULL, NULL, 'S/.'),
(5218, 457, 1, 6, 55, 'LLANTA SUNOTE 185/70R13', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-20 11:48:14', 'Jeam', NULL, NULL, '1519152494', b'1', 0, b'0', 0, NULL, NULL, 'S/.'),
(5219, 365, 1, 6, 53, 'BATERIA CAPSA 9WI/9PLC.12V', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-22 08:01:51', 'Jeam', NULL, NULL, '1519311711', b'0', 0, b'0', 0, NULL, NULL, 'S/.'),
(5220, 413, 1, 6, 55, 'LLANTA 155/70 R12 73T CITY TOUR POWER TRACC', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-24 10:25:52', 'Jeam', '2018-02-24 11:25:54', 'Jeam', '1519493152', b'1', 19.5, b'1', 0, 9, NULL, 'S/.'),
(5221, 413, 1, 6, 55, 'LLANTA 165/70 R12 77T CITYTOUR POWERTRAC', '', '', '0', '0', '0', 0, 0, 0, 0, b'0', '2018-02-24 10:27:43', 'Jeam', NULL, NULL, '1519493263', b'1', 19, b'0', 0, NULL, NULL, 'S/.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_ProductoBloque`
--

CREATE TABLE `Gen_ProductoBloque` (
  `IdBloque` int(11) NOT NULL,
  `Bloque` varchar(255) DEFAULT NULL,
  `PorcentajeMin` float DEFAULT NULL,
  `PorcentajeMax` float DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `Gen_ProductoBloque`
--

INSERT INTO `Gen_ProductoBloque` (`IdBloque`, `Bloque`, `PorcentajeMin`, `PorcentajeMax`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(0, '-', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'PVL', 20, 50, '2017-09-16 13:30:34', 'Jeam', '2017-10-31 18:29:40', 'Jeam'),
(6, 'PEB', 50, 99.9, '2017-10-02 06:24:24', 'Jeam', NULL, NULL),
(7, 'PE', 100, 199.9, '2017-10-02 06:25:02', 'Jeam', NULL, NULL),
(8, 'PSE', 200, 1000, '2017-10-02 06:27:17', 'Jeam', NULL, NULL),
(9, 'PI', 80, 500, '2017-10-02 06:28:19', 'Jeam', NULL, NULL),
(10, 'PBR', 1, 19.9, '2017-10-02 06:29:22', 'Jeam', NULL, NULL),
(11, 'PP', 30, 100, '2017-10-02 06:30:25', 'Jeam', NULL, NULL),
(12, 'PEB3', 50, 99.9, '2017-10-24 09:12:25', 'Jeam', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_ProductoCategoria`
--

CREATE TABLE `Gen_ProductoCategoria` (
  `IdProductoCategoria` int(11) NOT NULL,
  `ProductoCategoria` varchar(255) DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL,
  `IdProductoCategoriaSub` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Gen_ProductoCategoria`
--

INSERT INTO `Gen_ProductoCategoria` (`IdProductoCategoria`, `ProductoCategoria`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `IdProductoCategoriaSub`) VALUES
(52, 'ARO', b'1', '2018-02-12 14:54:32', 'Jeam', '2018-02-14 10:07:28', 'Jeam', NULL),
(53, 'BATERIA', b'1', '2018-02-12 14:54:43', 'Jeam', '2018-02-14 10:07:39', 'Jeam', NULL),
(54, 'GUARDACAMARA', b'0', '2018-02-12 14:55:08', 'Jeam', NULL, NULL, NULL),
(55, 'LLANTA', b'0', '2018-02-12 14:55:22', 'Jeam', NULL, NULL, NULL),
(56, 'LLANTA MOTO LI', b'0', '2018-02-12 14:55:37', 'Jeam', NULL, NULL, NULL),
(57, 'LLANTA MOTO LIN', b'0', '2018-02-12 14:56:15', 'Jeam', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_ProductoCompuesto`
--

CREATE TABLE `Gen_ProductoCompuesto` (
  `IdProductoCompuesto` int(11) NOT NULL,
  `ProductoCompuesto` varchar(255) DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL,
  `Hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_ProductoCompuestoDet`
--

CREATE TABLE `Gen_ProductoCompuestoDet` (
  `Gen_Producto_IdProducto` int(11) NOT NULL,
  `Gen_ProductoCompuesto_IdProductoCompuesto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_ProductoDet`
--

CREATE TABLE `Gen_ProductoDet` (
  `IdProducto` int(11) NOT NULL,
  `IdProductoDet` int(11) NOT NULL,
  `Cantidad` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_ProductoFormaFarmaceutica`
--

CREATE TABLE `Gen_ProductoFormaFarmaceutica` (
  `IdProductoFormaFarmaceutica` int(11) NOT NULL,
  `ProductoFormaFarmaceutica` varchar(255) DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Gen_ProductoFormaFarmaceutica`
--

INSERT INTO `Gen_ProductoFormaFarmaceutica` (`IdProductoFormaFarmaceutica`, `ProductoFormaFarmaceutica`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(1, 'INYECCION', b'0', '2017-07-05 14:32:45', 'Jeam', '2017-08-07 08:30:33', 'Jeam'),
(2, 'LATA', b'0', '2017-08-01 13:34:42', 'Jeam', '2017-08-05 13:03:15', 'Jeam'),
(3, 'AGUA MINERAL', b'0', '2017-08-01 14:17:32', 'Jeam', '2017-08-05 22:23:37', 'Jeam'),
(4, 'GRAGEA', b'1', '2017-08-01 16:55:35', 'Jeam', '2017-08-05 22:21:29', 'Jeam'),
(5, 'OVULO', b'0', '2017-08-02 22:28:53', 'Jeam', '2017-08-05 13:01:13', 'Jeam'),
(6, 'EXTRACTO', b'0', '2017-08-03 14:35:32', 'Jeam', '2017-08-06 05:54:26', 'Jeam'),
(8, 'JARABE', b'0', '2017-08-03 16:40:04', 'Jeam', '2017-08-05 22:24:39', 'Jeam'),
(9, 'COLIRIO', b'0', '2017-08-04 06:46:17', 'Jeam', '2017-08-05 22:25:38', 'Jeam'),
(10, 'CREMA', b'0', '2017-08-04 07:44:37', 'Jeam', '2017-08-05 22:26:04', 'Jeam'),
(11, 'PASTA', b'0', '2017-08-04 12:51:45', 'Jeam', '2017-08-05 22:26:40', 'Jeam'),
(12, 'TABLETA', b'0', '2017-08-04 13:24:13', 'Jeam', NULL, NULL),
(13, 'POMADA / UNGUENTO', b'0', '2017-08-04 14:56:33', 'Jeam', '2017-08-05 22:27:15', 'Jeam'),
(14, 'UNIDAD', b'0', '2017-08-05 06:53:27', 'Jeam', '2017-08-09 08:32:49', 'Jeam'),
(15, 'GEL', b'0', '2017-08-05 07:22:49', 'Jeam', '2017-08-05 22:28:16', 'Jeam'),
(16, 'SOLUCION', b'0', '2017-08-05 07:35:24', 'Jeam', '2017-08-05 22:29:12', 'Jeam'),
(17, 'ROLLO', b'0', '2017-08-05 09:06:32', 'Jeam', '2017-08-06 05:35:30', 'Jeam'),
(18, 'AGUJA', b'0', '2017-08-05 10:13:45', 'Jeam', NULL, NULL),
(19, 'POLVO', b'0', '2017-08-05 11:06:33', 'Jeam', '2017-08-05 22:31:12', 'Jeam'),
(20, 'CAPSULA', b'0', '2017-08-05 14:24:00', 'Jeam', NULL, NULL),
(21, 'PALETA DE MADERA ', b'0', '2017-08-05 15:24:21', 'Jeam', NULL, NULL),
(22, 'SUSPENCION', b'0', '2017-08-06 07:24:04', 'Jeam', NULL, NULL),
(23, 'LOCION', b'0', '2017-08-06 07:28:41', 'Jeam', NULL, NULL),
(24, 'ACEITE', b'0', '2017-08-06 07:30:14', 'Jeam', NULL, NULL),
(25, 'SHAMPOO / ACONDICIONADOR', b'0', '2017-08-06 07:44:13', 'Jeam', NULL, NULL),
(26, 'BIBERON', b'0', '2017-08-06 07:47:59', 'Jeam', '2017-09-20 05:46:53', 'Jeam'),
(27, 'CONDON', b'0', '2017-08-06 08:54:19', 'Jeam', NULL, NULL),
(28, 'AGUA DE COLONIA', b'0', '2017-08-06 09:28:04', 'Jeam', NULL, NULL),
(29, 'TROCISCO', b'0', '2017-08-06 09:41:52', 'Jeam', NULL, NULL),
(30, 'TOALLA SANITARIA', b'0', '2017-08-06 10:02:58', 'Jeam', NULL, NULL),
(31, 'JALEA', b'0', '2017-08-06 10:11:04', 'Jeam', NULL, NULL),
(32, 'HILO', b'0', '2017-08-07 06:13:10', 'Jeam', '2017-08-07 07:58:32', 'Jeam'),
(33, 'BARRA', b'0', '2017-08-07 10:14:15', 'Jeam', NULL, NULL),
(34, 'COLADO', b'0', '2017-08-08 08:13:10', 'Jeam', '2017-08-09 05:49:28', 'Jeam'),
(35, 'GOTAS', b'0', '2017-08-08 09:41:20', 'Jeam', NULL, NULL),
(36, 'COLONIA ', b'0', '2017-08-08 14:55:15', 'Jeam', NULL, NULL),
(37, 'JABON', b'0', '2017-08-09 06:13:38', 'Jeam', NULL, NULL),
(38, 'AGUA DE PERFUME', b'0', '2017-08-09 06:36:01', 'Jeam', NULL, NULL),
(39, 'AEROSOL', b'0', '2017-08-09 07:04:20', 'Jeam', NULL, NULL),
(40, 'SACHET', b'0', '2017-08-09 08:57:31', 'Jeam', '2017-09-05 08:27:49', 'Jeam'),
(41, 'SPRAY', b'0', '2017-08-09 09:43:44', 'Jeam', NULL, NULL),
(42, 'POTE', b'0', '2017-08-09 13:13:10', 'Jeam', '2017-08-09 13:16:01', 'Jeam'),
(43, 'FRASCO', b'0', '2017-08-09 14:57:02', 'Jeam', NULL, NULL),
(44, 'AMPOLLAS', b'0', '2017-08-09 15:26:03', 'Jeam', NULL, NULL),
(45, 'CHUPON', b'0', '2017-08-09 20:42:15', 'Jeam', NULL, NULL),
(46, 'CAJA', b'0', '2017-08-10 05:48:34', 'Jeam', NULL, NULL),
(47, 'GOTERO', b'0', '2017-08-10 06:19:32', 'Jeam', NULL, NULL),
(48, 'BLOQUEADOR.', b'0', '2017-08-10 08:15:12', 'Jeam', '2017-09-20 08:43:50', 'Jeam'),
(49, 'PROTECTORES / PROFILACTICOS', b'0', '2017-08-10 14:02:17', 'Jeam', NULL, NULL),
(50, 'SAVAL', b'0', '2017-08-10 19:13:29', 'Jeam', NULL, NULL),
(51, 'GASA', b'0', '2017-08-12 10:00:55', 'Jeam', NULL, NULL),
(52, 'SOBRE', b'0', '2017-08-12 13:07:57', 'Jeam', NULL, NULL),
(53, 'GALON', b'0', '2017-08-13 07:57:40', 'Jeam', NULL, NULL),
(54, 'HILO SOTURA', b'0', '2017-08-13 11:46:53', 'Jeam', NULL, NULL),
(55, 'GUANTES', b'0', '2017-08-14 06:15:09', 'Jeam', NULL, NULL),
(56, 'GUANTES QUIRURGICOS', b'0', '2017-08-14 07:03:05', 'Jeam', NULL, NULL),
(57, 'GOMITAS', b'0', '2017-08-14 08:00:17', 'Jeam', NULL, NULL),
(58, 'JERINGA', b'0', '2017-08-14 08:04:05', 'Jeam', '2017-08-14 08:17:04', 'Jeam'),
(59, 'TUBO', b'0', '2017-08-14 09:40:21', 'Jeam', NULL, NULL),
(60, 'PARCHES', b'0', '2017-08-14 10:08:44', 'Jeam', NULL, NULL),
(61, 'ENEMA', b'0', '2017-08-14 13:36:32', 'Jeam', NULL, NULL),
(62, 'HOJA BISTURI', b'0', '2017-08-15 05:51:54', 'Jeam', NULL, NULL),
(63, 'PAMPERS', b'0', '2017-08-15 06:02:31', 'Jeam', NULL, NULL),
(64, 'LAXANTE', b'0', '2017-08-15 07:42:38', 'Jeam', NULL, NULL),
(65, 'ENJUAGUE BUCAL', b'0', '2017-08-15 09:41:20', 'Jeam', NULL, NULL),
(66, 'COMPRIMIDOS', b'0', '2017-08-16 09:06:52', 'Jeam', NULL, NULL),
(67, 'JUGUETE', b'0', '2017-08-16 20:46:45', 'Jeam', NULL, NULL),
(68, 'TARRO', b'0', '2017-08-17 12:07:53', 'Jeam', NULL, NULL),
(69, 'TOALLITAS HUMEDAS', b'0', '2017-08-20 08:37:24', 'Jeam', NULL, NULL),
(70, 'VIAL', b'0', '2017-08-23 12:30:53', 'Jeam', NULL, NULL),
(71, 'EQUIPO DE VENOCLISIS', b'0', '2017-08-24 07:06:05', 'Jeam', NULL, NULL),
(72, 'PASTILLA', b'0', '2017-08-24 07:30:40', 'Jeam', NULL, NULL),
(73, 'MARCOS', b'0', '2017-08-30 13:07:31', 'Jeam', NULL, NULL),
(74, 'BOEHRINGER INGELHEIM', b'0', '2017-09-01 14:06:48', 'Jeam', NULL, NULL),
(75, 'REPELENTE', b'0', '2017-09-02 07:59:02', 'Jeam', NULL, NULL),
(76, 'CEPILLO', b'0', '2017-09-28 12:12:48', 'Jeam', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_ProductoMarca`
--

CREATE TABLE `Gen_ProductoMarca` (
  `IdProductoMarca` int(11) NOT NULL,
  `ProductoMarca` varchar(255) DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Gen_ProductoMarca`
--

INSERT INTO `Gen_ProductoMarca` (`IdProductoMarca`, `ProductoMarca`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(269, 'HERCULES', b'1', '2018-02-12 16:03:44', 'Jeam', '2018-02-14 09:06:19', 'Jeam'),
(270, 'NEXEN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(271, 'LING LONG', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(272, 'INSA TURBO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(273, 'FALKEN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(274, 'CATCHFORSE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(275, 'TOYO TYRES', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(276, 'LINGLONG', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(277, 'ROADSTONE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(278, 'HAIDA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(279, 'GOLDWAY', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(280, 'HANKOOK', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(281, 'DUNLOP', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(282, 'YOKOHAMA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(283, 'SAMSON', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(284, 'ARMOUR', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(285, 'GOODRIDE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(286, 'NESCSTEL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(287, 'GOODTYRE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(288, 'ADVANCE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(289, 'OTANI', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(290, 'TRIANGLE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(291, 'HIFLY', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(292, 'ORNET', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(293, 'BARUM', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(294, 'LIMA CAUCHO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(295, 'AMERICAN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(296, 'PDW', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(297, 'ARAZZO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(298, 'BELEN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(299, 'DRAGON', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(300, 'DARWIN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(301, 'DEMONIUM', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(302, 'INDIANA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(303, 'MAZZARO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(304, 'PEPE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(305, 'ZEHLENDORF', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(306, 'TOYOTA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(307, 'HYUNDAI', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(308, 'KIA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(309, 'MITSUBISHI', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(310, 'MARSHALL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(311, 'APLUS', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(312, 'MIRAGE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(313, 'GENERAL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(314, 'COMFORSER', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(315, 'WILD COUNTRY', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(316, 'UNITED', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(317, 'GOOD YEAR', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(318, 'FIRESTONE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(319, 'CHAO YANG', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(320, 'TEXXAN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(321, 'XCEED', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(322, 'WESTLAKE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(323, 'MALHOTRA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(324, 'BCT', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(325, 'FEDERAL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(326, 'DURUN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(327, 'BRIDGESTONE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(328, 'FUZION', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(329, 'THUNDERER', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(330, 'KUMHO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(331, 'DEESTONE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(332, 'CATCHGRE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(333, 'WANDA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(334, 'GOOD FRIEND', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(335, 'CONTINENTAL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(336, 'MAXTREK', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(337, 'LAUFENN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(338, 'PIRELLI', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(339, 'ACCELERA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(340, 'BOTO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(341, 'ROADSHINE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(342, 'WINRUN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(343, 'WINDA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(344, 'AUTOGRIP', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(345, 'ALTURA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(346, 'DURATREAD', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(347, 'TECHKING', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(348, 'DRC', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(349, 'VIKRANT', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(350, 'ANTARES', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(351, 'DOUBLE CAMEL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(352, 'DOUBLE COIN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(353, 'AEOLUS', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(354, 'KAPSEN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(355, 'ODOKING', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(356, 'JOYROAD', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(357, 'SUNFULL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(358, 'ANNAITE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(359, 'SPORTRAK', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(360, 'ALLIANCE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(361, 'SOLIDEAL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(362, 'PRAXXIS', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(363, 'RECORD', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(364, 'ENERJET', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(365, 'CAPSA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(366, 'HABILEAD', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(367, 'FARROAD', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(368, 'TYRESOL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(369, 'COMPASAL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(370, 'MINNELL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(371, 'ALFA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(372, 'BEARWAY', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(373, 'COOPER', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(374, 'EXIDE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(375, 'SOLITE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(376, 'DURO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(377, 'MICHELIN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(378, 'MGM', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(379, 'DAEWOO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(380, 'MASSTEK', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(381, 'LAUFEN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(382, 'TRAILCUTER', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(383, 'KAIZEN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(384, 'BOXER', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(385, 'NANKANG', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(386, 'SUNWIDE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(387, 'GOALSTAR', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(388, 'BOSCH', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(389, 'YUASA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(390, 'KOYO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(391, 'ION', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(392, 'MAYHEM', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(393, 'ETNA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(394, 'GALAXY', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(395, 'MICHEIIN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(396, 'ITP', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(397, 'XTREME', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(398, 'BF GOODRICH', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(399, 'DURAMAS', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(400, 'MARSHAL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(401, 'INTERSTATE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(402, 'KINGRUN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(403, 'DOUBLE STAR', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(404, 'SPORTIVA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(405, 'GINELL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(406, 'HENGDA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(407, 'GOOD  YEAR', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(408, 'BKT', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(409, 'ALMARO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(410, ' AMERICAN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(411, 'BFGOODRICH', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(412, 'EL DORADO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(413, 'POWERTRAC', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(414, 'FIREMAX', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(415, 'LUCKYLAND', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(416, 'GREMAX', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(417, 'MAZDA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(418, 'SUPER', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(419, 'APOLLO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(420, 'LUHE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(421, 'AELOUS', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(422, 'TEKPRO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(423, 'H-TRAK', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(424, 'MITAS', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(425, 'CEAT', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(426, 'CACHLAND', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(427, 'SAILUN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(428, 'MILEMAX', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(429, 'NORT STAR', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(430, 'KETER', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(431, 'MULTIRAC', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(432, 'BORISTAR', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(433, 'TAITONG', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(434, 'SONAR', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(435, 'BLACKLION', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(436, 'RINTAL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(437, 'FORERUNER', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(438, 'WOSEN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(439, 'FESITE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(440, 'SIERRA', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(441, 'YOYO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(442, 'TRANSTONE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(443, 'CAMSO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(444, 'MASTERCRAFT', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(445, 'DOUPRO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(446, 'HILO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(447, 'MAXXIS', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(448, 'LANVIGATOR', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(449, 'YUEHENG', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(450, 'GOLDPARTNER', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(451, 'MARUTI', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(452, 'VARELOX', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(453, 'ARTUM', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(454, 'DEMONIO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(455, 'ROADTEC', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(456, 'TORNEL', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(457, 'SUNOTE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(458, 'ANTYRE', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(459, 'ZERO', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL),
(460, 'NEXXEN', b'0', '2018-02-12 16:03:44', 'Jeam', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gen_ProductoMedicion`
--

CREATE TABLE `Gen_ProductoMedicion` (
  `IdProductoMedicion` int(11) NOT NULL,
  `ProductoMedicion` varchar(255) DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Gen_ProductoMedicion`
--

INSERT INTO `Gen_ProductoMedicion` (`IdProductoMedicion`, `ProductoMedicion`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(6, 'UNI.', b'0', '2017-08-03 13:51:13', 'Jeam', NULL, NULL),
(8, 'PAQ.', b'0', '2017-08-05 07:37:45', '', NULL, NULL),
(9, 'CAJA', b'0', '2017-08-05 08:21:39', '', NULL, NULL),
(10, 'BOLSA', b'1', '2017-08-05 14:23:23', '', '2017-08-05 15:29:11', 'Jeam'),
(11, 'TARRO', b'0', '2017-08-10 12:37:33', '', NULL, NULL),
(12, 'SOBRE', b'0', '2017-08-12 12:38:18', '', NULL, NULL),
(13, 'BLISTER', b'0', '2017-08-14 06:22:28', '', NULL, NULL),
(14, 'POTE', b'0', '2017-08-20 11:09:14', '', NULL, NULL),
(15, 'TUBO', b'0', '2017-08-26 06:48:02', '', NULL, NULL),
(16, 'ROLLO', b'0', '2017-08-26 09:30:36', '', NULL, NULL),
(17, 'SACHET', b'0', '2017-08-31 12:13:22', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lo_Almacen`
--

CREATE TABLE `Lo_Almacen` (
  `IdAlmacen` int(11) NOT NULL,
  `Almacen` varchar(255) DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Lo_Almacen`
--

INSERT INTO `Lo_Almacen` (`IdAlmacen`, `Almacen`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(1, 'VENTA', b'0', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lo_Impuesto`
--

CREATE TABLE `Lo_Impuesto` (
  `IdImpuesto` int(11) NOT NULL,
  `Impuesto` varchar(255) DEFAULT NULL,
  `VaEnDetalle` bit(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `Lo_Impuesto`
--

INSERT INTO `Lo_Impuesto` (`IdImpuesto`, `Impuesto`, `VaEnDetalle`) VALUES
(1, 'PERCEPCION', NULL),
(2, 'ISC', NULL),
(3, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lo_Movimiento`
--

CREATE TABLE `Lo_Movimiento` (
  `IdMovimientoTipo` int(11) NOT NULL,
  `IdProveedor` int(11) NOT NULL,
  `Serie` varchar(255) NOT NULL,
  `Numero` int(11) NOT NULL,
  `MovimientoFecha` datetime DEFAULT NULL,
  `IdAlmacenOrigen` int(11) DEFAULT NULL,
  `IdAlmacenDestino` int(11) DEFAULT NULL,
  `Observacion` text,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL,
  `Hash` varchar(255) DEFAULT NULL,
  `Percepcion` float DEFAULT NULL,
  `FechaStock` datetime DEFAULT NULL,
  `EsCredito` bit(1) DEFAULT NULL,
  `FechaVenCredito` datetime DEFAULT NULL,
  `FechaPeriodoTributario` int(11) NOT NULL,
  `Moneda` varchar(255) DEFAULT NULL,
  `TipoCambio` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lo_MovimientoDetalle`
--

CREATE TABLE `Lo_MovimientoDetalle` (
  `hashMovimiento` varchar(255) NOT NULL,
  `IdProducto` int(11) NOT NULL,
  `Cantidad` float NOT NULL,
  `TieneIgv` bit(1) DEFAULT NULL,
  `Precio` float NOT NULL,
  `ISC` float DEFAULT NULL,
  `FLETE` float DEFAULT NULL,
  `IdLote` int(11) DEFAULT NULL,
  `FechaVen` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lo_MovimientoDetalleImpuesto`
--

CREATE TABLE `Lo_MovimientoDetalleImpuesto` (
  `HashMovimientoDetalle` varchar(255) DEFAULT NULL,
  `IdImpuesto` int(11) DEFAULT NULL,
  `Importe` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lo_MovimientoTipo`
--

CREATE TABLE `Lo_MovimientoTipo` (
  `IdMovimientoTipo` int(11) NOT NULL,
  `TipoMovimiento` varchar(255) DEFAULT NULL,
  `Tipo` int(11) DEFAULT NULL,
  `VaRegCompra` bit(1) DEFAULT NULL,
  `CodSunat` varchar(255) DEFAULT NULL,
  `TipoMovSunat` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Lo_MovimientoTipo`
--

INSERT INTO `Lo_MovimientoTipo` (`IdMovimientoTipo`, `TipoMovimiento`, `Tipo`, `VaRegCompra`, `CodSunat`, `TipoMovSunat`) VALUES
(1, 'COMPRA FACTURA', 0, b'1', '01', '02'),
(2, 'COMPRA BOLETA', 0, b'1', '03', '02'),
(3, 'COMPRA TICKET', 0, b'1', '12', '02'),
(4, 'INGRESO', 0, b'1', '0', '99'),
(5, 'SALIDA X DETERIORO', 1, b'1', '0', '13'),
(6, 'TRASLADOS', 2, b'0', '0', '0'),
(9, 'SALIDA OTROS', 1, b'1', '99', '12'),
(10, 'DONACION', 1, b'0', '00', '00'),
(11, 'SOBRANTES', 0, b'0', '00', '00'),
(12, 'FALTANTES', 1, b'0', '00', '00'),
(13, 'DEVOLUCION', 0, b'0', '00', '00'),
(15, 'SALIDA X VENCIMIENTO', 1, b'0', '00', '00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lo_Proveedor`
--

CREATE TABLE `Lo_Proveedor` (
  `IdProveedor` int(11) NOT NULL,
  `Proveedor` varchar(255) DEFAULT NULL,
  `Ruc` varchar(255) DEFAULT NULL,
  `Direccion` text,
  `Observacion` text,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Lo_Proveedor`
--

INSERT INTO `Lo_Proveedor` (`IdProveedor`, `Proveedor`, `Ruc`, `Direccion`, `Observacion`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(0, '-', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'STEELS &ASOCIADOS SRL', '20297970489', 'AV. DE LOS HEROES Nº 459 SAN JUAN DE MIRAFLORES LIMA', 'LLANTAS', b'0', '2018-02-24 10:19:42', 'Jeam', NULL, NULL),
(17, 'PEVISA AUTOPARTS S.A.', '20100084768', 'AV. ELMER FAUCETT Nº 717 CALLAO', 'BATERIAS Y LLANTAS', b'0', '2018-02-20 10:15:45', 'Jeam', NULL, NULL),
(18, 'MAC JOHNSON CONTROLS COLOMBIA S.A.S SUCURSAL PERU', '20392895800', 'AV. MARISCAL LUIS JOSE DE ORBEGOSO Nº 177 UB. EL PINO SAN LUIS LIMA', 'BATERIAS', b'0', '2018-02-22 08:07:41', 'Jeam', NULL, NULL),
(19, 'DISTRIBUCIONES ADAN ', '20601479886', 'JR:LEON DE HUANUCO 175-183', 'LLANTA', b'0', '2018-02-22 10:40:14', 'Jeam', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prodstock`
--

CREATE TABLE `prodstock` (
  `IdProducto` int(11) NOT NULL,
  `ProductoMarca` varchar(255) DEFAULT NULL,
  `ProductoCategoria` varchar(255) DEFAULT NULL,
  `FormaFarmaceutica` varchar(255) DEFAULT NULL,
  `Producto` varchar(255) DEFAULT NULL,
  `Stock` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `prodstock`
--

INSERT INTO `prodstock` (`IdProducto`, `ProductoMarca`, `ProductoCategoria`, `FormaFarmaceutica`, `Producto`, `Stock`) VALUES
(3156, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS D1217 20X10.0 10X114.3+127 ET78 B-MX', -5),
(3187, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 5003 14X6.0 ET25 8X100+114.3 BP-F', 0),
(3193, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS L430 17X9.0 ET25 5X114.3 MB-L', 0),
(3194, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 8104 16X7.0 ET20 8X100+114.3 B-P', 0),
(3195, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS L582 15X7.0 ETO 6X139.7 BM-LP', 0),
(3196, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS L200 14X6.0 ET0 4X100+114.3 M-FB', 0),
(3208, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 5325 18X8.0 ET25 10X100+114.3 B-P', 0),
(3209, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 519 18X8.0 ET35 5X114.3 B-LP', 0),
(3210, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 337 16X7.0 ET35 8X100+114.3 R-BP', 0),
(3211, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3215 16X7.0 ET35 8X100+114.3 B-P', 0),
(3212, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-742 14X6.0 ET38 8X100+114.3 BK-IRD', 0),
(3213, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-480 14X6.0 ET38 8X100+114.3 BK-IRD', 0),
(3226, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-9011 16X8.0 ET10 6X139.7 B4', 0),
(3227, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-115 15X7.0 ET35 8X100+114.3 W-OJKB', 0),
(3228, 'DEMONIO', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1402 18X8.0 ET40 5X114.3 B4', 0),
(3229, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 4526707 14X5.5 ET38 4X100.0 MB', 0),
(3230, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 4515512 14X6.5 ET30 4X100.0 MB', 0),
(3231, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 424352 14X6.5 ET35 4X100.0 MB', 0),
(3232, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 20181460 14X6.0 ET35 4X100.0 MI/B', 0),
(3233, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 10061460 14X6.0 ET30 4X100.0 M2/B', 0),
(3234, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 30381370 13X7.0 ET70 4X100.0 MI/GR', 0),
(3235, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 30381370 13X7.0 ET70 4X114.3 MI/GR', 0),
(3242, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 4602615 14X7.0 ET10 6X139.7 MB', 0),
(3243, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG2606 18X8.5 ET35 5X114.3 MB FAC', 0),
(3244, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-3680 17X7.0 ET40 4X100.0 MB', 0),
(3245, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-1913 15X7.0 ET30 4X100.0 MB', 0),
(3246, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-2890 15X7.0 ET35 4X100.0 MB', 0),
(3247, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-1911 14X5.5 ET38 4X100+114.3 MB', 0),
(3248, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-5071 14X6.5 ET30 4X100.0 B-MFC', 0),
(3249, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6600 14X6.0 ET35 4X100.0 MB', 0),
(3250, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-5008 17X9.0 ET0 16X114.3 BM', 0),
(3251, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-1009 16X8.0 ET15 6X114.3 R LP', 0),
(3252, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6642 16X7.5 ET15 6X139.7 MB', 0),
(3253, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-672 16X7.0 ET0 6X139.7 MB', 0),
(3255, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-495 13X6.0 ET30 8X100+114.3 BP', 0),
(3256, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZY-3311 13X5.5 ET30 8X100+114.3 BP', 0),
(3257, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZY-6704 13X5.5 ET20 4X100 RL-B6', 0),
(3258, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-448 13X5.5 ET35 4X100+114.3 BP', 0),
(3259, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-1032 13X6.0 ET30 8X100+114.3 RL B6', 0),
(3260, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-3206 13X5.5 ET30 8X100+114.3 ORP', 0),
(3261, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-089 13X5.5 ET35 8X100+114.3 SP', 0),
(3262, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-089 13X5.5 ET30 8X100+114.3 BP', 0),
(3263, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZY-3209 13X5.5 ET30 8X100+114.3 BP', 0),
(3264, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-T5158 13X5.5 ET35 8X100+114.3 BP', 0),
(3265, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-519 13X5.5 ET35 8X100+114.3 BP', 0),
(3266, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZY-8117 13X5.5 ET30 8X100+114.3 BP', 0),
(3267, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-988 13X5.5 ET28 8X100+114.3 BP', 0),
(3268, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-801 13X5.5 ET35 8X100+114.3 BP', 0),
(3269, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-1001 13X5.5 ET30 8X100+114.3 SP', 0),
(3270, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-1001 13X5.5 ET30 8X100+114.3 BP', 0),
(3271, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZY-2794 13X5.5 ET35 8X100+114.3 BP', 0),
(3272, 'VARELOX', 'ARO', 'INYECCION', 'ARO VARELOX ZF-10086 13X5.5 ET28 8X100+114.3 BP-B', 0),
(3326, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS D941 20X9.5 ET20 5X120 G3-MF', 0),
(3327, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS D972 20X8.0 ET30 5X120 G1-MF', 0),
(3328, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS D983 20X9.5 ET20 5X120 G5-MF', 0),
(3329, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 395 18X8.5 ET25 10X100+114.3 B-P', 0),
(3330, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 511 18X8.0 ET42 10X100+114.3 B-P', 0),
(3331, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3169 14X6.0 ET25 8X100+114.3 B-P', 0),
(3332, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3153 14X6.0 ET25 8X100+114.3 B-P', 0),
(3333, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS L199 14X6.0 6X139.7 ET0 BMF/LP', 0),
(3346, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 746 14X6.0 ET35 8X100+114.3 B-MF', 0),
(3347, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS L078 13X6.0 ET36 4X100+114.3 BMF-RED', 0),
(3348, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 722 13X5.5 ET30 8X100+114.3 B-MF', 0),
(3349, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 508 13X5.5 ET25 8X100+114.3 B-HS', 0),
(3361, 'MAYHEM', 'ARO', 'INYECCION', 'ARO MAYHEM WHEELS 8010-2937 20X9 ET18 6X135+139.7 M-B', 0),
(3456, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 508 14X6.0 ET30 4X100.0 HS/BI', 0),
(3458, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-20901 17X7.0 ET38 4X100+114.3 M-B', 0),
(3459, 'PDW', 'ARO', 'INYECCION', 'ARO PDW A5162F22 20X8.5 ET33 5X114.3 EJ/1B', 0),
(3460, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 76011F50 17X8.0 ET32 10X105+114.3 MXL-B', 0),
(3461, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 723541 17X7.0 ET38 8X100+114.3 MI/MW', 0),
(3462, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 30131670 16X7.0 ET35 10X100+114.3 M-B', 0),
(3463, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 621356 16X6.5 ET40 8X100+114.3 M-B', 0),
(3464, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 681515 16X7.0 ET35 8X100+114.3 MXL-B', 0),
(3465, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 77021C23 17X8.0 ET20 12X135X139.7 M/UB', 0),
(3466, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 66048A03 16X7.0 ET25 6X139.7 M-B', 0),
(3467, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 6666A30 16X7.5 ET0 6X139.7 M-B', 0),
(3468, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 551904 16X7.0 ETO 6X139.7 M-B', 0),
(3481, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS L905 17X9.0 ET30 6X114.3 MF-L', 0),
(3482, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS L849 16X8.0 ET30 6X139.7 ML-P', 0),
(3483, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3730 15X7.0 ET30 8X100+114.3 B-LP', 0),
(3484, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS L405 15X8.0 ET20 4X100.0 M-B', 0),
(3485, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS L200 15X6.5 ET36 5X114.3 MF-B', 0),
(3505, 'YOYO', 'ARO', 'INYECCION', 'ARO YOYO Y-666 8X3.5 ET30 4X106.00 BL-RS', 0),
(3507, 'MAYHEM', 'ARO', 'INYECCION', 'ARO MAYHEM 8102-8937 18X9.0 ET18 6X135+139.7 BM-S', 0),
(3508, 'MAYHEM', 'ARO', 'INYECCION', 'ARO MAYHEM 8090-7937 17X9.0 ET18 6X135+139.7 M-B', 0),
(3509, 'MAYHEM', 'ARO', 'INYECCION', 'ARO MAYHEM 8010-7837 17X8.0 ET10 6X135+139.7 M-T', 0),
(3510, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-38309 16X7.0 ET31 4X100+114.3 FS-B3', 0),
(3511, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-37904 16X7.5 ET30 4X100.0 FS-B3', 0),
(3512, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-19112 15X7.0 ET30 5X100+114.3 M-B', 0),
(3513, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-37902 15X7.0 ET33 4X100+114.3 BM-FS', 0),
(3514, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-63002 16X7.0 ET35 6X139.7 M-B', 0),
(3515, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-66508 16X8.0 ET0 6X139.7 FBS-3CI', 0),
(3516, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-66508 16X8.0 ET0 6X139.7 FB-BRD', 0),
(3517, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6349 16X8.0 ET10 6X139.7 M-B', 0),
(3518, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG2001 15X10.0 ET44 6X139.7 BM-L', 0),
(3519, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6349 15X8.0 ET13 6X139.7 M-B', 0),
(3520, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-63911 15X8.0 ET10 6X139.7 M-B', 0),
(3527, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LGS1113 16X8.0 ET0 5X114.3 B-M', 0),
(3558, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1118 16X8.0 ET0 6X139.7 B-MF', 0),
(3559, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1075A 16X7.5 ET32 4X100.0 G-MF', 0),
(3560, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3215 15X6.5 ET38 8X100+114.3 B-P', 0),
(3561, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 8104 15X6.0 ET20 8X100+114.3 B-P', 0),
(3562, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3215 14X6.0 ET35 8X114+114.3 B-P', 0),
(3563, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1143 17X9.0 ET0 12X135+139.7 LA5-B', 0),
(3564, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 528 15X7.0 ET30 4X100.0 B-HS', 0),
(3565, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 126 15X7.5 ET0 5X114.3 B-MF', 0),
(3566, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 126 15X7.5 ET0 5X139.7 B-MF', 0),
(3567, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1075A 16X7.5 ET32 4X100.0 B-MF', 0),
(3568, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 307 16X7.0 ET0 6X139.7 B-MF', 0),
(3569, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3206 15X6.5 ET25 8X100+114.3 B-P', 0),
(3570, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 395 15X7.0 ET25 8X100+114.3 B-P', 0),
(3571, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3718Z 15X6.5 ET35 8X100+114.3 B-PR', 0),
(3572, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 337 15X6.5 ET18 8X100+114.3 R-BP', 0),
(3573, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3111Z 14X6.0 ET35 8X100+114.3 BP', 0),
(3574, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS D2769 15X6.5 ET20 8X100+114.3 B-P', 0),
(3575, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 712 13X5.5 ET25 8X100+114.3 B-MF', 0),
(3576, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 528 13X5.5 ET25 4X100.0 B-HS', 0),
(3577, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3268 13X5.5 ET25 8X100+114.3 BP', 0),
(3578, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3215 13X5.5 ET35 8X100+114.3 BP', 0),
(3610, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 56027F28 15X7.0 ET0 6X139.7 M-B', 0),
(3611, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 5518623 15X7.0 ET0 5X114.3 M-B', 0),
(3612, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 5504650 15X8.0 ET15 6X139.7 M-B', 0),
(3613, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 8516220 18X8.0 ET35 5X114.3 B-', 0),
(3614, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 781326 17X7.0 ET35 8X100+114.3 MI-B', 0),
(3615, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 653774 16X7.0 ET35 10X100+114.3 R-M', 0),
(3616, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 6901344 16X7.0 ET35 10X100+114.3 M-B', 0),
(3617, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 5521707 15X7.0 ET40 5X100.0 M-B', 0),
(3618, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 526917 15X7.0 ET35 4X100.0 M-B', 0),
(3619, 'ION', 'ARO', 'INYECCION', 'ARO ION 184-7937 17X9.0 ET18 6X135+139.7 BM-S', 0),
(3620, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 7603243 17X8.0 ET30 6X114.3 M-B', 0),
(3621, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 6602807 16X7.0 ET10 6X139.7 M-B', 0),
(3622, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 6511207 16X7.0 ET0 6X139.7 MI-B', 0),
(3623, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 4703713 14X6.5 ET20 4X100.0 M-B', 0),
(3624, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 102814550 14X5.5 ET35 4X100.0 M-B', 0),
(3625, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 385038 13X5.5 ET35 4X100.0 B-', 0),
(3626, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 390343 13X5.5 ET35 4X100.0 M-B', 0),
(3627, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 362742 13X5.5 ET35 4X100.0 M-B', 0),
(3628, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 356276 13X5.5 ET35 4X100.0 M-B', 0),
(3629, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 356276 13X5.5 ET35 4X100.0 B-M', 0),
(3630, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 3587M33 13X6.0 ET70 4X114.3 B-M', 0),
(3631, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 3587M32 13X6.0 ET70 4X100.0 M-B', 0),
(3632, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 3522B15 13X5.5 ET35 4X100.0 M-B', 0),
(3637, 'MAYHEM', 'ARO', 'INYECCION', 'ARO MAYHEM 8090 20X10.0 ET25 6X139.7/6X135.6 BM-S', 0),
(3638, 'MAYHEM', 'ARO', 'INYECCION', 'ARO MAYHEM 9101 20X10 ET25 6X135.7/6X135.6 B-M', 0),
(3675, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 351957 13X6.0 ET70 4X100.0 MB', 0),
(3676, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 5527011 15X6.0 ET32 4X100.0 MB', 0),
(3677, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 5501954 15X6.5 ET35 5X114.3 MB', 0),
(3678, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-1890 14X5.5 ET40 4X100+114.3 BM', 0),
(3679, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 451042 14X6.0 ET35 8X100+114.3 MB', 0),
(3680, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 1022L1 14X6.0 ET30 4X100.0 MB', 0),
(3681, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-5102 13X5.5 ET35 4X100+114.3 C-VW', 0),
(3682, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 351033 13X5.5 ET35 4X100.0 MB', 0),
(3683, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 351022 13X5.5 ET35 8X100+114.3 MB', 0),
(3684, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 358826 13X5.5 ET0 4X114.3 MB', 0),
(3685, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 388955 13X5.5 ET25 8X100+114.3 MB', 0),
(3686, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 363342 13X5.5 ET18 4X100.0 MB', 0),
(3687, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 352952 13X5.5 ET18 4X100.0 MB', 0),
(3688, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 358451 13X5.5 ET15 4X100.0 MB', 0),
(3689, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 372027 13X5.5 ET25 4X100.0 MB', 0),
(3690, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 359943 13X5.5 ET25 4X100.0 MB', 0),
(3691, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 9145 17X9.0 ET0 6X139.7 B-M', 0),
(3692, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-7561 17X9.0 ET0 6X139.7 EB-MB', 0),
(3693, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 6608903 16X7.5 ET0 6X139.7 MB', 0),
(3694, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 6602031 16X7.5 ET10 6X139.7 MI-UB', 0),
(3695, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 10111580 15X8.0 ET0 6X139.7 MB', 0),
(3696, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 566636 15X7.0 ET0 6X139.7 MB', 0),
(3697, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 5600970 15X8.0 ET0 5X139.7 UB', 0),
(3698, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 5590AM 15X6.5 ET35 8X100+114.3 MO-MIU', 0),
(3699, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-5068 15X7.0 ET30 4X100.0 BM', 0),
(3715, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO WHEELS VK-321 14X7.0 5X114.3 FMC', 0),
(3716, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO WHEELS VK-558 17X7.5 5X114.3 FC-CR', 0),
(3718, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1005 8X3.5 ET0 4X106.0 R2-B', 0),
(3728, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 626750 16X7 4H114.3', 0),
(3729, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 3753 15X7.0 8H100/114.3 BLP', 0),
(3730, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 7001L 14X6.5 4X100 MB', 0),
(3731, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 4903015 14X6 4H100 MB', 0),
(3732, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 4511648 14X6 4X100 B', 0),
(3733, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 9077 17X9.0 6H139.7 B-LP', 0),
(3734, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 6607612 16X7.5 6H139.7', 0),
(3735, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 6602031 16X7.5 6H139.7 MI/B', 0),
(3736, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 6602228 16X7.5 6H139.7 M2/B', 0),
(3810, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-191507 14X6.5 25 73.1 4X100 M-B', 0),
(3823, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 5849147 15X6.5 ET35 8H100/114.3', 0),
(3824, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 4800158 14X6.5 ET20 4H/100MM', 0),
(3825, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-506602 15X7 ET30 4X100 BC-H', 0),
(3826, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-1630 15X6.5 ET35 4X100/114.3', 0),
(3827, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 563869 15X6.5 ET20 8H100/114.3', 0),
(3828, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 7002R146505 14X6.5 ET28 4X100', 0),
(3829, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-2731 14X6.0 ET40 4X100 BM', 0),
(3830, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-2209 14X6.0 ET35 4X100/114.3', 0),
(3831, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-4539191 14X6 ET35 4X100+114.3 BM', 0),
(3832, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 3235BP 14X6.0 ET354H100', 0),
(3848, 'MAZDA', 'ARO', 'INYECCION', 'ARO ORIGINAL MAZDA 17X8', 0),
(3867, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 479 16X7.5 8X100/114.3 20 74.1 J4/ML', 0),
(3868, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 479 16X7.5 8X100/114.3 20 74.1 B/ML', 0),
(3869, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 256 17X7.0 8X100/108 15 73.1 BP/M5', 0),
(3870, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS W009 17X7.0 8X100/114.3 35 LA5-B/MF', 0),
(3871, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1075 17X8.0 8X100/114.3 25 73.1 BMF', 0),
(3872, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3275 17X7.5 8X100/114.3 25 73.1 B-P', 0),
(3873, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3113 17X7.5 10X100/114.3 39 73.1 BP/M', 0),
(3874, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1101 17X7.5 8X100/114.3 30 73.1 BMF', 0),
(3875, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1034 17X7.5 8X100/114.3 25 LA5-B/MN', 0),
(3876, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 951 16X7.0 5X100 40 67.1 BHS', 0),
(3877, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 348 16X7.0 8X100/114.3 35 73.1 BHS', 0),
(3878, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3275 16X7 8X100/114.3 25 73.1 B-P', 0),
(3879, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 126 16X7.5 5X139.7 110.5BMF', 0),
(3880, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 600 15X6.5 8X100/114.3 (B-P)B-LP/M5', 0),
(3881, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 626 18X9.5 5X114.3 28 73.1 LA5-B', 0),
(3882, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 347 18X8.0 5X112 35 73.1 LA5-B/ML', 0),
(3883, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1155 18X7.5 6X139.7 25 108.5 B/MF', 0),
(3884, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1139 20X9.5 5X114.3 LA5-B', 0),
(3897, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3206 14X6.0 ET33 100+114.3X8H', 0),
(3943, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 756 13X6.0 8X100/114.3 MF', 0),
(3944, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 620 13X7.0 4X100-7 BML', 0),
(3945, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1060 13X5.5 8X100/114.3 BMF', 0),
(3946, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 525854 15X6.5 4H100 73.10 M/B', 0),
(3947, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 3247 18X8.0 5H114.3 B', 0),
(3948, 'ION', 'ARO', 'INYECCION', 'ARO ION 196-29337 20X9 6H/135/139.7 M/BCU', 0),
(3949, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-5008 17X9.0 ET30 6x139.7 BMF', 0),
(3950, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-6701118 16X8 6H/139.7', 0),
(3951, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-8522 16X8 ET35 6X1143 MB', 0),
(3952, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-66522 16X8.0 ET35 5X114.3 B/RI', 0),
(3953, ' AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS LGS0311 15X8.0 5X114.3 BMF', 0),
(3954, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-37503 15X8.0 ET25 5X139.7 B-M', 0),
(3955, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 526749 15X6.5 4H10073.10 M/UB', 0),
(3956, 'PDW', 'ARO', 'INYECCION', 'ARO PDW 5400344 15X7 4H10073.1 MI/B', 0),
(3957, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-426924 14X6.5 ET35 4X100.0 MB', 0),
(3958, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 359 14X6.0 100X114.3 BP', 0),
(3959, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-73601 18X8.5 ET0 5X120 MB', 0),
(3960, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-507114 16X7.5 ET30 5X114.3 ELBR', 0),
(3961, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG1703 16X7.5 4X100/114.3 MB', 0),
(3962, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG0308 15X7.0 4X100/114.3 MB', 0),
(3963, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG501 15X8 4X100/114.3MB', 0),
(3964, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG4301 14X6 4X100/114.3 MB', 0),
(3965, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS LG4601 13X5.5 4X100/114.3 MB', 0),
(3970, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK5033 15X6.5 4X100', 0),
(3971, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK5199 14X6.0 8X100/114.3', 0),
(3972, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK313 13X5.5 8X100/114.3', 0),
(3973, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK321 16X8.0 6X139.7', 0),
(3974, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK513 15X7.0 6X139.7', 0),
(3975, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK859 15X8.0 6X139.7', 0),
(3976, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK413 14X7.0 5X114.3', 0),
(3977, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK470 15X6.5 8X100/114.3', 0),
(3978, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK188 15X6.5 10X100/114.3', 0),
(3979, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK196 14X6.0 8X100/114.3', 0),
(3980, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK233 14X6.0 8X100/114.3', 0),
(3981, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK346 14X6.0 8X100/114.3', 0),
(3982, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK479 14X6.0 8X100/114.3', 0),
(3983, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK224 13X6.0 8X100/114.3', 0),
(3984, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK312 13X5.5 8X100/114.3', 0),
(3985, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK446 13X6.0 4X100', 0),
(3986, 'ALMARO', 'ARO', 'INYECCION', 'ARO ALMARO VK470 13X5.5 8X100/114.3', 0),
(4023, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-9012 15X7.5 ET25 6X139.7 B4', 0),
(4024, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-2042 15X8.0 ET30 6X139.7 B4', 0),
(4025, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO MZ-1256 13X5.5 100/114.3X8H B4', 0),
(4026, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-519 18X8.0 ET30 114.3X5H B-LP/M', 0),
(4027, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-331 17X7.5 ET0 100/114.3X8H B-P', 0),
(4028, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-391 17X7.5 100/114.3X8H B-P/M', 0),
(4029, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3113 17X7.5 100/114.3X8H B-P', 0),
(4030, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3905 16X7.0 ET0 100+114.3X8H B-P', 0),
(4031, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DH-2003 DEMONIUM 16X7.0 108X4H B4TRZX', 0),
(4032, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DH-3250 DEMONIUM 14X6.0 100X4H B-P', 0),
(4033, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-6704 13X5.5 ET30 100X4H B6', 0),
(4034, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3206 13X5.5 ET0 100/114.3X8H B-P', 0),
(4035, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-720 13X6.0 ET35 100X4H B-P', 0),
(4036, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-348 13X5.5 ET35 100+114.3X8H BKL-M', 0),
(4037, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-140 13X5.5 ET35 100/114.3X8H B-P', 0),
(4073, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 9093 17X8.0 5H114.3 BP', 0),
(4074, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 3259 18X8.0 5H114.3 BP', 0),
(4075, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-663824 16X7 10H 100/114.3 MCRB', 0),
(4076, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-21506 13X5.5 ET40 4X100 MB', 0),
(4077, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 6708 13X5.5 ET12 4HX100', 0),
(4078, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 668 13X6.0 ET35 8H/100/114.3', 0),
(4079, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 639 13X5.5 ET124H100(58.60)B-LP', 0),
(4080, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 606 13 X6.0 ET35 4H114.3 BP', 0),
(4081, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 600 13X5.5 ET35 8H/100X114.3', 0),
(4082, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 598 13X5.5ET35 8H BP', 0),
(4083, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 5976P 13X5.5 ET35 8H100X114.3 BP', 0),
(4084, 'XTREME', 'ARO', 'INYECCION', 'ARO XTREME 2790 13X5.5 ET 35 BP', 0),
(4092, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 509 13X6.0 4X100 35 73 .1 B/F', 0),
(4093, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1155 17X7.5 6X139. T26 108. 5 B/MF', 0),
(4094, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1075A 16X7.5 ET32 4X100.0 R7/MN', 0),
(4095, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 955 13X5.5 4X100 30 73.1 8HS', 0),
(4096, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 307 17X7.5 6X139.7 0 110.50 BMF', 0),
(4097, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1058 17X8.0 10X100/114.3 35 73.1 B/MF', 0),
(4098, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 804 15X6.5 8X100/114.3 35 73.1 B/MF', 0),
(4099, 'DRAGON', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS 508 15X6.5 4X100 35 73.1 BHS', 0),
(4100, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1088 14X6 .0 4X100 35 73.1 B/MF', 0),
(4101, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 307 16X7 6X139.7 0 110.5 G1/MF', 0),
(4102, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 2004 16X9. 08X139.7 0108 5. LA 5-B', 0),
(4103, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1003 14X5.5 4X100 3073. 1 B/MF', 0),
(4104, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 802 14X6.0 8X100/114.3 35 73.1BHS', 0),
(4105, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 528 16X7.0 4X100 25 73.1 BHS', 0),
(4106, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 528 14X6.0 4X100 35 73.1 BHS', 0),
(4107, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 509 14X6 .0 4X100 35 73.1B/MF', 0),
(4108, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 508 14X6.0 4X100. 35 73.1 HB1', 0),
(4109, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 508 16X7.0 4X100 30 73.1 BHB', 0),
(4110, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 158 18X8.0 5X114.3 45 73.1 G1/MF', 0),
(4145, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-32809 14X6.0 ET0 4X100+108.2 BM', 0),
(4146, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-21606 13X5.5 ET30 4X100 B-M', 0),
(4147, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG1709 17X7.5 ET40 5X100.0 MB', 0),
(4148, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-7553717X7 10H/100/114.3mm B', 0),
(4149, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-7576 17X8 5X114.3 MM M/B', 0),
(4150, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG0817 18X8.0 5X114 .3 MB- M FACE', 0),
(4151, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG1706 17X7.5 ET3 5X114.3 MB', 0),
(4152, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-28929 17X7.5 ET0 5X114.3 MB', 0),
(4153, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-1730 17X7 5 30 5X114.3 B-M', 0),
(4154, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-58705 14X6.0 ET0 4X100 B-M', 0),
(4155, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-58426 14X6 ET30 4X100+114.3 B-M', 0),
(4156, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG0304 14X6.0 ET 4X100+1114.3 B-M', 0),
(4157, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG0304 14X6.0 ET30 4X100+114.3', 0),
(4158, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG0603 14X6.0 ET0 4X100+114.3 B-M', 0),
(4184, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-85215 15X8.0 ET0 5X114.3 BM', 0),
(4185, 'PDW', 'ARO', 'INYECCION', 'ARO PDW R 4509124M/B 14X6 ET35 4H100 73.1 M/B', 0),
(4186, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-465749MB 14X6 ET35 4H100 73.1 M/B', 0),
(4187, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-425814MB 14X5.5 ET35 4H100 73.1 M/B', 0),
(4188, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-4905814UB 14X6 ET32 4H100 73.1 UB', 0),
(4189, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-4520715 14X6.5 ET20 4H100.0 M/B', 0),
(4190, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-320625 13X6 ET70 4X114.3 M/B', 0),
(4191, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-365744MB 13X5.5 ET35 4H100 73.1 M/B', 0),
(4192, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-325812MB13X5.5 ET35 4H100 73.1 M/B', 0),
(4193, 'MAYHEM', 'ARO', 'INYECCION', 'ARO MAYHEM 8010-7837 17X8.0 6X135+139.7 MB-B', 0),
(4194, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-37517 16X8.0 ET10 6X139.7 BM-R', 0),
(4195, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-6701045 16X8 ET10 8X100+114.3 U-B', 0),
(4196, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-6600943 16X8 ET-0 6H/139.7 mmUB', 0),
(4197, 'ION', 'ARO', 'INYECCION', 'ARO BLACK/MACHINED BLIP ION174-6883 R 16X8 ET-5 6H 139.7 108mm LIP (', 0),
(4198, 'ION', 'ARO', 'INYECCION', 'ARO ION WHEELS 194-5895 15X8 BLACK/MACHINEDET-27 5-6H139.7 LIP', 0),
(4199, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-5603225 15X8 ET0 5H139.7 108 M/U4B', 0),
(4208, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1156 14X6.0 ET40 114.3X5H B-P', 0),
(4212, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-299 18X8.5 ET25 114.3X5H B-MF', 0),
(4213, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-149 18X8.0 ET30 114.3X5H (73.1) B/MF', 0),
(4214, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-393 16X7.5 ET38 100X4 (73.1/66) H/S', 0),
(4215, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-253 16X7 ET40 4X100.0 HS', 0),
(4216, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDOR WHEELS ZH-115 16X7 ET38 8X100+114.3', 0),
(4217, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-114 17X9.0 ET0 139.7X6H B/MF', 0),
(4218, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-115 15X7 ET35 8X100+114.3 BK/OJW', 0),
(4265, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-40108 13X5.5 ET35 73.1 4X100 B-M', 0),
(4266, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-501704 18X8.0 ET30 73.1 5X114.3 MS-B3', 0),
(4267, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-53114 20X8.5 ET40 5HX114.3 C-H', 0),
(4268, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG0306 16X7.0 5X100+114.3', 0),
(4269, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-26901 16X7.5 30 73.1 5X114.3 ML-TB', 0),
(4270, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-59033832 15X6.5 ET33 8HX100+108 MI-B', 0),
(4271, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-4905812 14X6.0 ET32 4H100.0 W-KL', 0),
(4272, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-480016 14X6.5 ET20 4H100.0 M-B', 0),
(4273, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-1992 14X5.5 ET35 4X100+114.3 M-B', 0),
(4274, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-86016 14X6.0 ET40 4X100 B-M', 0),
(4275, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-71509 14X5.5 40 73.1 4X100+114.3 B-M', 0),
(4276, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-63628 14X5.5 35 73.1 4X100 LI-P', 0),
(4277, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6020 14X6.0 ET35 4X100+114.3 M-B', 0),
(4278, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-27909 14X6.0 40 73.1 4X100 M-B', 0),
(4279, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-21610 14X6.0 ET40 73.1 4X100 B-M', 0),
(4289, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-1073 13X5.5 ET30 100+114.3X8H B-MF', 0),
(4290, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-1085 13X5 ET30 100+114 .3X8H B-MF', 0),
(4293, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-562 14X6.0 ET30 100X4H B-MF', 0),
(4294, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1253 14X5.5 ET40 100+114.3X8H B4', 0),
(4295, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-466 14X6.0 ET35 100+108X8H B-MF', 0),
(4296, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-524 14X7.0 ET-9 114.3X5H LA5-B', 0),
(4347, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING B-963HS 15X6.5 ET35 4X100.0', 0),
(4348, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1291 14X5.5 ET38 100+114.3X8H B-P', 0),
(4349, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1144 15X6.5 ET40 100+114.3X8H H-S', 0),
(4350, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1144 14X6.0 ET40 100+114 .3X8H H-S', 0),
(4351, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1519 14X5.5 ET38 100+114.3X8H B-P', 0),
(4352, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-2047 15X6.5 ET40 100+114.3X8H B-P', 0),
(4353, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1253 13X5.5 ET38 100+114.3X8H HS', 0),
(4361, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-356 15X6.5 ET40 100+114.3X8H B-P', 0),
(4362, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-395 15X7.0 ET40 100+114.3X8H BP-M', 0),
(4423, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-112 13X5.5 ET48 4X100.0 BP-KJ', 0),
(4428, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3206 14X6.0 ET33 100+114.3X8H BLK-M', 0),
(4429, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-337 14X6.0 ET35 100+114.3X8H B-P', 0),
(4430, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-149 14X6.0 ET35 100+114.3X8H B-P', 0),
(4431, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-149 13X5.5 ET35 100+114.3X8H B-P', 0),
(4432, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-247 14X6.0 ET35 100+114.3X8H B-P', 0),
(4433, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-348 14X6.0 ET35 100+114.3X8H B-P', 0),
(4442, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3113 13X5.5 ET32 100+114.3X8H B-P', 0),
(4443, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-331 14X6.0 ET35 100+114.3X8H EM-M', 0),
(4444, 'INDIANA', 'ARO', 'INYECCION', 'ARO INDIANA WHEELS IN-2033 14X6.0 ET40 100+114.3X8H B-4', 0),
(4463, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 495 16X7.0 5X114.3 35 60 S1/MF', 0),
(4464, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 722 14X6.0 5X114.3 00 83 BM/F', 0),
(4465, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 922 15X6.0 5X114.3 45 60 G1/MF', 0),
(4555, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-242 13X5.5 ET35 100+114.3X8H H-S', 0),
(4556, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3255 14X6.0 ET35 100X4H CA-S4B', 0),
(4557, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3255 13X5.5 ET35 100X4H CA-S4B', 0),
(4558, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-247 13X5.5 ET35 100+114.3X8H B-P', 0),
(4559, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-348 13X5.5 ET32 100+114.3X8H B-P', 0),
(4560, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-331 13X5.5 ET35 100+114.3X8H B-P', 0),
(4561, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-6708 13X5.5 ET12 100X4H B-P', 0),
(4904, 'MITSUBISHI', 'ARO', 'INYECCION', 'ARO ORIGINAL MITSUBISHI 16X6H 139.7', 0),
(4905, 'HYUNDAI', 'ARO', 'INYECCION', 'ARO ORIGINAL HYUNDAI 16X5H 114.3', 0),
(4906, 'KIA', 'ARO', 'INYECCION', 'ARO ORIGINAL KIA 18X5H 114.3', 0),
(4907, 'KIA', 'ARO', 'INYECCION', 'ARO ORIGINAL KIA 15X4H 100.0', 0),
(4908, 'KIA', 'ARO', 'INYECCION', 'ARO ORIGINAL KIA 14X4H 100.0', 0),
(4909, 'HYUNDAI', 'ARO', 'INYECCION', 'ARO ORIGINAL HYUNDAI 15X5H 114.3', 0),
(4910, 'TOYOTA', 'ARO', 'INYECCION', 'ARO ORIGINAL TOYOTA 17X5H 114.3', 0),
(4911, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 7100 15X8.0 4X100+114.3', 0),
(4912, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-146 14X7.5 ET0 139.7X6H BL-K', 0),
(4913, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-5605 20X10.0 ET0 127.0X5H MJ-S', 0),
(4914, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-9145 17X7.5 ETO 100+114.3X8H B-P', 0),
(4915, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHELNDORF WHEELS ZH-9128 20X9.0 ET0 139.7X6H B-P', 0),
(4916, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHELNDORF WHEELS ZH-409 16X8.0 ET0 139.7X6H VB-P', 0),
(4917, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-9147 20X9.0 ET0 6X139.7 B-P', 0),
(4918, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-9148 17X9.0 ETO 139.7X6H BL-K', 0),
(4919, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-9147 17X9.0 ET0 139.7X6H B-P', 0),
(4920, 'ZEHLENDORF', 'ARO', 'INYECCION', 'ARO ZEHLENDORF WHEELS ZH-9147 17X9.0 ET0 139.7X6H BL-K', 0),
(4921, 'PEPE', 'ARO', 'INYECCION', 'ARO PEPE RACING 6602 16X7.0 ET40 10H100+108.3 MI-B', 0),
(4922, 'PEPE', 'ARO', 'INYECCION', 'ARO PEPE RACING 5450 16X7.5 ET35 5H114.3 M-B', 0),
(4923, 'PEPE', 'ARO', 'INYECCION', 'ARO PEPE RACING 154 14X5.5 ET38 8H100+114.3 G-L', 0),
(4924, 'PEPE', 'ARO', 'INYECCION', 'ARO PEPE RACING 806 13X6.0 ET35 8H100+114.3 B-F', 0),
(4925, 'PEPE', 'ARO', 'INYECCION', 'ARO PEPE RACING 593 13X6.0 ET35 4H100.0 B-L', 0),
(4926, 'PEPE', 'ARO', 'INYECCION', 'ARO PEPE RACING 228 13X6.0 ET35 4H114.3 B-F', 0),
(4927, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-1009158 15X8 6H ET0 6H139.7 EM-D', 0),
(4928, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-621355 16X6.5 ET40 8H100+114.3 MC-F', 0),
(4929, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-5849142 15X6.5 ET35 8H100+108.0 C-B', 0),
(4930, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-351956 13X6 ET70 4H114.3 M-B', 0),
(4931, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-6902 16X7.5 ET40 10H120.5+114.3 C-K', 0),
(4932, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1180 20X9.5 ET40 114.3X5H HS-1', 0),
(4933, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1225 17X7.5 ET38 114.3X5H B-4', 0),
(4934, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1502 17X7.0 ET38 114.3X5H B4-X', 0),
(4935, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1523 17X7.5 ET10 139.7X6H B3-X', 0),
(4936, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1578 17X7.5 ET38 114.3X5H B-4', 0),
(4937, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1538 16X6.5 ET35 100+114.3X8H B-4', 0),
(4938, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1489 16X7.0 ET40 100X4H B-4', 0),
(4939, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1288 16X7.0 ET40 100+114.3X8H B-1', 0),
(4940, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1122 16X7.0 ET38 100+114.3X8H A-G', 0),
(4941, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-2804 15X6.5 ET38 100+114.3X8H HS-1', 0),
(4942, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-2023 15X6.5 ET38 100+114.3X8H GR-X', 0),
(4943, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1411 15X6.0 ET38 100.0X4H B-4', 0),
(4944, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1169 15X6.5 ET40 100+114.3X8H B4-D', 0),
(4945, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1101 15X6.5 ET40 100+114.3X8H HS-1', 0),
(4946, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-2023 14X6.0 ET38 100+114.3X8H HS-1', 0),
(4947, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-1411 14X5.5 ET40 100X4H B-4', 0),
(4948, 'MAZZARO', 'ARO', 'INYECCION', 'ARO MAZZARO WHEELS MZ-2804 13X5.5 ET38 100+108.0X8H HS-1', 0),
(4949, 'INDIANA', 'ARO', 'INYECCION', 'ARO INDIANA WHEELS IN-2028 15X6.5 ET38 100+114.3X8H B4-X', 0),
(4950, 'INDIANA', 'ARO', 'INYECCION', 'ARO INDIANA WHEELS IN-1545 15X6.0 ET38 100.0X4H B-4', 0),
(4951, 'INDIANA', 'ARO', 'INYECCION', 'ARO INDIANA WHEELS IN-1156 14X7.0 ET10 139.7X6H B-4', 0),
(4952, 'INDIANA', 'ARO', 'INYECCION', 'ARO INDIANA WHEELS IN-2803 16X7.0 ET10 114.3X5H B-4', 0),
(4953, 'INDIANA', 'ARO', 'INYECCION', 'ARO INDIANA WHEELS IN-1307 16X8.0 ET10 139.7X6H B-4', 0),
(4954, 'INDIANA', 'ARO', 'INYECCION', 'ARO INDIANA WHEELS IN-2013 13X5.5 ET35 100+114.3X8H B-4', 0),
(4955, 'INDIANA', 'ARO', 'INYECCION', 'ARO INDIANA WHEELS IN-469 15X6.5 ET35 100+114.3X8H MG-M', 0),
(4956, 'INDIANA', 'ARO', 'INYECCION', 'ARO INDIANA WHEELS IN-372 16X8.0 ET40 100+114.3 4X8H B-P', 0),
(4957, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 391 15X6.5 5X100.0 33 66 JRT-4', 0),
(4958, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3195 20X8.5X114.3 55 22 BF-K', 0),
(4959, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3717 15X6.5 5X100.0 33 99 GH-L', 0),
(4960, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 535 16X8.0 6X139.7 64 88 B-P', 0),
(4961, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1170 18X8.0 6X139.7 33 55 B-P', 0),
(4962, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 609 13X6.0 4X100.0 33 55 HS-P', 0),
(4963, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 621 14X6.0 6X139.7 33 85 B-P', 0),
(4964, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 722 14X6.0 6X139.7 83 35 BH-B', 0),
(4965, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 1034 20X8.5 5X114.3 40 76 B-MN', 0),
(4966, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 323 15X6.5 4X100+108.0 33 55 B-P', 0),
(4967, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 965 15X8.0 6X139.7 33 45 BG-H', 0),
(4968, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 5310 16X10.5 6X139.7 33 75 GK-P', 0),
(4969, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 965 16X8.0 6X139.7 33 76 B-P', 0),
(4970, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 575 20X9.5 5X130.3 33 46 B-P', 0),
(4971, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 426 16X7.0 4X100+108.0 33 55 HS-P', 0),
(4972, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 210 17X7.5 5X108.0 33 47 B-P', 0),
(4973, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 722 14X6.0 6X139.7 32 45 M-B', 0),
(4974, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 820 13X5.5 4X100+108.0 35 78 VN-H', 0),
(4975, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 886 16X7.0 5X100.0 55 32 B-P', 0),
(4976, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3905 15X7.0 8X100+114.3 32 73 OR-P', 0),
(4977, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 395 15X7.0 8X100+114.3 25 73 OR-P', 0),
(4978, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 181 16X6.5 8X100+114.3 35 73 MK-M', 0),
(4979, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3111 15X6.5 8X100+114.3 25 73 W-P', 0),
(4980, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 5310 16X8.0 6X139.7 0 110 BL-P', 0),
(4981, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 9134 16X8.0 6X139.7 0 110 B-P', 0),
(4982, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 3170 15X6.5 8X100+114.3 33 73 OR-P', 0),
(4983, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 357 15X6.5 8X100+114.3 40 73 B-4', 0),
(4984, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 599 15X6.5 8X100+114.3 38 73 B-P', 0),
(4985, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1382 14X6.0 ET35 100+114.3X8H BX-4T', 0),
(4986, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-2002 14X6.0 ET40 100+114.3X8H B-4', 0),
(4987, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-2003 14X6.0 ET38 100+114.3X8H B-4', 0),
(4988, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-2034 14X6.0 ET35 100.0X4H B-4', 0),
(4989, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-9015 14X6.0 ET35 100+114.3X8H B3-TR', 0),
(4990, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1187 13X5.5 ET38 100+114.3X8H HS-1', 0),
(4991, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1591 13X5.0 ET36 100.0X4H B-4', 0),
(4992, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-8200 13X5.5 ET35 100+114.3X8H B-P', 0),
(4993, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1147 16X7.0 ET40 100+114.3X8H B-P', 0),
(4994, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1156 17X8.0 ET10 139.7X6H B-P', 0),
(4995, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-1156 14X7.0 ET10 139.7X6H B-P', 0),
(4996, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-578 18X7.5 ET40 114.3X5H B-P', 0),
(4997, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-169 16X6.5 ET20 139.7X6H B-P', 0),
(4998, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-517 20X8.0 ET35 114.3 X5H B-P', 0),
(4999, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-2734 16X8.0 ET10 139.7X6H BL-K', 0),
(5000, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-241 14X6.0 ET35 100+114.3X8H H-S', 0),
(5001, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-610 13X5.5 ET35 100+114.3X8H B-P', 0),
(5002, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-348 16X7.0 ET38 100+114.3X8H B-P', 0),
(5003, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-355 16X7.0 ET40 100+114.3X8H B6-Z', 0),
(5004, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-5913 16X8.0 ET28 139.7X6H B-P', 0),
(5005, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3719 15X6.5 ET30 100+114.3X8H L-S', 0),
(5006, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3170 17X7.5 ET30 114.3X5H BL-K', 0),
(5007, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-815 16X7.5 ET35 100+114.3X4H B-P', 0),
(5008, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-3105 14X6.0 ET38 100+114.3X8H H-S', 0),
(5009, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-723 15X6.5 ET35 114.3X4H G-S', 0),
(5010, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-693 16X7.0 ET38 139.7X6H B-L', 0),
(5011, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-551 20X7.5 ET36 139.7X6H B-K', 0),
(5012, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-824 18X8.0 ET40 100+114.3X8H B-P', 0),
(5013, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-355 15X6.5 ET38 100+114.3X8H B-P', 0),
(5014, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-968 14X6.0 ET40 100.0X4H H-S', 0),
(5015, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-356 16X7.0 ET38 100+114.3X8H G-L', 0),
(5016, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-519 20X8.0 ET30 114.3X5H H-S', 0),
(5017, 'DEMONIUM', 'ARO', 'INYECCION', 'ARO DEMONIUM WHEELS DH-482 20X8.5 ET42 114.3X5H B-P', 0),
(5018, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-9016BFP 15X6.5 4X100+114.3 ET10', 0),
(5019, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING F-0522GB 17X7.0 6X139.7 ET25', 0),
(5020, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-1211GMB 15X6.5 4X100+114.3 ET35', 0),
(5021, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-1308HS 15X8.0 4X114.3 ET30', 0),
(5022, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING F-7026BFP 15X6.5 5X100 ET0', 0),
(5023, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING W-894HB 15X6.5 5X100 ET0', 0),
(5024, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING B-441BFP 15X6.5 4X100+114.3 ET38', 0),
(5025, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING B-808GB 15X6.5 4X100+114.3 ET35', 0),
(5026, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-138BP 15X6.5 4X100+114.3 ET38', 0),
(5027, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-2038BFP 15X6.5 4X100 ET38', 0),
(5028, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-1322GBFP 13X5.5 4X100+114.3 ET10', 0),
(5029, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-2014HS 13X5.5 4X100 ET30', 0),
(5030, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-1178MBFP 13X5.5 4X100 ET38', 0),
(5031, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING B-528BFP 17X8.0 5X114.3 ET30', 0),
(5032, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING TRIANGULAR 8-SPOKE 16X7.0 6X139.7', 0),
(5033, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-1109MBP 14X6.0 4X100+114.3 ET40', 0),
(5034, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-1207HS 16X7.0 6X139.7 ET38', 0),
(5035, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-068GRT 16X7.0 4X100+114.3 ET0', 0),
(5036, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-1203BP 14X6.0 4X100.0 ET38', 0),
(5037, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-2018GPS 13X5.5 4X100 ET30', 0),
(5038, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-303GPS 14X6.0 4X100 ET30', 0),
(5039, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-1282BLK 16X7.0 4X100.0 ET40', 0),
(5040, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-9016 BLK 14X6.0 4X100+114.3 ET38', 0),
(5041, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GQ-846GTR 20X9.0 6X139.7 ET30', 0),
(5042, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING H-190HSS 16X7.0 4X100+114.3 ET0', 0),
(5043, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING B-616HS 18X8.0 5X114.3 ET0', 0),
(5044, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GQ-723BPS 17X8.0 5X120.3 ET40', 0),
(5045, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-068BLK 16X7.0 5X100+114.3 ET38', 0),
(5046, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING F-001HS 17X7.0 5X114.3 ET39', 0),
(5047, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING S-557HS 16X6.5 6X139.7 ET30', 0),
(5048, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-519BFP 15X7.0 6X139.7 ET36', 0),
(5049, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-068HS 14X6.0 5X100+114.3 ET30', 0),
(5050, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GQ-722HS 15X7.0 6X139.7 ET38', 0),
(5051, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-1282GSK 16X7.0 4X100 ET40', 0),
(5052, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GQ-723HS 16X8.0 6X139.7 ET40', 0),
(5053, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GQ-723XS 15X7.0 6X139.7 ET38', 0),
(5054, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING B-348BLK 16X7.0 6X110+114.3 ET40', 0),
(5055, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GS-1218BFP 18X8.0 5X114.3 ET35', 0),
(5056, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING B-528HS 16X7.0 4X100 ET38', 0),
(5057, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING B-746HSLP 15X7.0 4X100 ET30', 0),
(5058, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-234BP 15X6.5 4X100+114.3 ET38', 0),
(5059, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING GA-068HS 15X6.5 4X100+114.3 ET35', 0),
(5060, 'DARWIN', 'ARO', 'INYECCION', 'ARO DARWIN RACING B-768HS 13X5.5 4X100+114.3 ET30', 0),
(5061, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 3277 13X8.0 100+114.3', 0),
(5062, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 346 13X5.5 4X100+114.3', 0),
(5063, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 8504 13X8.0 4X114.3', 0),
(5064, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 804 17X8.0 5X100+120.0', 0),
(5065, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 9515 17X7.5 5X114.3', 0),
(5066, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 3138 15X6.5 4X100+114.3', 0),
(5067, 'DRAGON', 'ARO', 'INYECCION', 'ARO DRAGON WHEELS 9148 20X9.0 ET0 6X139.7 B-P', 0),
(5068, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 3126 16X8.0 6X139.7', 0),
(5069, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 296 17X7.5 4X100+114.3', 0);
INSERT INTO `prodstock` (`IdProducto`, `ProductoMarca`, `ProductoCategoria`, `FormaFarmaceutica`, `Producto`, `Stock`) VALUES
(5070, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 296 15X6.5 4X100+114.3', 0),
(5071, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 903 15X6.5 4X100+114.3', 0),
(5072, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 9104 15X8.0 4X100+114.3', 0),
(5073, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 243 14X6.0 5X100.0', 0),
(5074, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 3234 17X7.0 5X100.0', 0),
(5075, 'BELEN', 'ARO', 'INYECCION', 'ARO BELEN WHEELS 3138 14X6.0 4X100+114.3', 0),
(5076, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-562 13X5.5 ET30 100X4H BM-F', 0),
(5077, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-378 16X6.0 ET30 100X4H B-ML', 0),
(5078, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-1085 13X5.5 ET30 100+108X8H A-MF', 0),
(5079, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-1073 13X5.5 ET30 100+114X8H AM-F', 0),
(5080, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-1073 13X5.5 ET30 100+108X8H B-MF', 0),
(5081, 'ARAZZO', 'ARO', 'INYECCION', 'ARO ARAZZO WHEELS AH-740 16X7.0 ET30 100X4H B-P', 0),
(5082, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-4012 14X6.0 ET35 4X100+114.3 B-M', 0),
(5083, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-856 17X9.0 ET0 6X139.7 BM-F', 0),
(5084, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-3831 17X8.0 ET33 4X100.0 B-M', 0),
(5085, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-3831 17X8.0 ET33 5X114.3 B-M', 0),
(5086, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG1703 16X7.5 ET30 4X100 M-B', 0),
(5087, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG1703 15X7.0 ET0 4X100/114.3 B-M', 0),
(5088, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-60217 13X5.5 ET35 4X100+114.3 B-M', 0),
(5089, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LGS1605 16X8.0 ET10 6X139.7 BM-F', 0),
(5090, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LGS0804 16X8.0 ET10 6X139.7 BM-F', 0),
(5091, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LGS0504 16X8.0 ET10 6X139.7 BM-F', 0),
(5092, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LGS0403 16X8.0 ET10 6X139.7 BM-F', 0),
(5093, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LGS0303 16X8.0 ET10 6X139.7 BM-F', 0),
(5094, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-8561 16X8.0 ET10 6X139.7 B-M', 0),
(5095, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-8551 16X8.0 ET10 6X139.7 E-B', 0),
(5096, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-7180 16X8.0 ET10 6X139.7 B-M', 0),
(5097, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6652 16X8.0 ET10 5X114.3 B-R', 0),
(5098, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6390 16X8.0 ET10 6X139.7 CH-R', 0),
(5099, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6340 16X8.0 ET10 6X139.7 B-M', 0),
(5100, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-5021 16X7.0 ET40 4X100 B-M', 0),
(5101, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-2050 16X7.0 ET35 4X100+114.3 B-M', 0),
(5102, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-7192 15X7.5 ET35 4X100+114.3 W-R', 0),
(5103, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6570 14X6.0 ET25 4X100 LW-B', 0),
(5104, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-32803 14X6.0 4X100 V-W', 0),
(5105, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-510 13X5.5 ET35 4X100/114.3 B/M', 0),
(5106, 'PDW', 'ARO', 'INYECCION', 'ARO PDW W-A67526 20X8.0 ET38 5X114.3 M-B', 0),
(5107, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-5640 15X8H ET40 MC-B', 0),
(5108, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6003 16.6X139 ET15 LS-B', 0),
(5109, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-6326 16X8.0 ET10 6X139.7 M-B', 0),
(5110, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-376 15.6X139 ET10 FS-B', 0),
(5111, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-152 17X8H ET35 M-B', 0),
(5112, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-146 17X10H ET38 L-B', 0),
(5113, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-LG19 17X5H ET35 F-B', 0),
(5114, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-808 16X5H RT38 M-B', 0),
(5115, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-379 15X8H ET33 M-B', 0),
(5116, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-183 15X8H ET35 B-M', 0),
(5117, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-168 15X8H ET35 L-W', 0),
(5118, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-158 15X8H ET35 M-B', 0),
(5119, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-131 15X8H ET38 LG-S', 0),
(5120, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-120 15X8H ET25 L-B', 0),
(5121, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-110 15X8H ET15 M-R', 0),
(5122, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-122 13X8H ET35 L-B', 0),
(5123, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-232 15X8.0 ET01 B-R', 0),
(5124, 'AMERICAN', 'ARO', 'INYECCION', 'ARO AMERICAN WHEELS AW-401 13X5.5 ET35 B-P', 0),
(5211, 'HERCULES', 'ARO', 'INYECCION', 'Ejemplo', 0),
(3167, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO YTX7L-BS AGM XPLUS', 0),
(3168, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO YTX7A-BS AGM XPLUS', 0),
(3169, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO YTX5L-BS AGM XPLUS', 0),
(3170, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO YTX4L-BS AGM XPLUS', 0),
(3171, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO 12N9-3B AGM XPLUS', 0),
(3172, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO 12N7A-3A AGM XPLUS', 0),
(3173, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO 12N7-3B AGM XPLUS', 0),
(3174, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO 12N6.5-3B AGM XPLUS', 0),
(3175, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO 12N5-3B AGM XPLUS', 0),
(3176, 'ZERO', 'BATERIA', 'INYECCION', 'BATERIA ZERO 6N6-3B AGM XPLUS', 0),
(3200, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RC 60 PLUS', 0),
(3201, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF55457 CHATA 480CCA', 0),
(3202, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK 56821 570CCA CHATA', 0),
(3274, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH S466D 15 PLACAS CHATA', 0),
(3281, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RP130 21 PLACAS', 0),
(3282, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECROD RWS 50 PLUS', 0),
(3301, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA 6N6-3B 6V', 0),
(3302, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA YTX7A-BS 12V', 0),
(3340, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AT-19 MAXIMA DURACION', 0),
(3345, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AFF-09 MAXIMA DURACION', 0),
(3362, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RF 95 PLUS', 0),
(3472, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF56828 68AH 570CCA', 0),
(3486, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 481MK 15 PLACAS MAXIMA', 0),
(3540, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD 105D31L 15 PLACAS', 0),
(3541, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD 90D26L 15 PLACAS', 0),
(3542, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD 56800 13 PLACAS', 0),
(3543, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD 55B24L 11 PLACAS', 0),
(3544, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD 44B19L 11 PLACAS', 0),
(3554, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF50D20L 11 PLACAS CUADRADA', 0),
(3586, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH S3154D N150 25 PLACAS', 0),
(3599, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH S455D 11 PLACAS CHATA', 0),
(3635, 'DAEWOO', 'BATERIA', 'INYECCION', 'BATERIA DAEWOO MF75D31L 13 PLACAS', 0),
(3636, 'DAEWOO', 'BATERIA', 'INYECCION', 'BATERIA DAEWOO MF50D20L 11 PLACAS', 0),
(3642, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 1765 PREMIUM', 0),
(3674, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 9API PREMIUM', 0),
(3713, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF55B24LS 45 AH', 0),
(3714, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF40B19L 11 PLACAS', 0),
(3717, 'NORT STAR', 'BATERIA', 'INYECCION', 'BATERIA NORT STAR 33 PLACAS GEL', 0),
(3738, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM YB2.5L 6V', 0),
(3739, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM 12N9-4B 12V', 0),
(3769, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 10LBI PREMIUM', 0),
(3792, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH N100 19 PLACAS SPECIAL', 0),
(3793, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 80D23L 15 PLACAS CUADRADA', 0),
(3801, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA YB2.5L-C 12V', 0),
(3802, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA YTX7L-BS 12V', 0),
(3803, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA YTX4L-BS 12V', 0),
(3804, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO YTX9-BS 12V', 0),
(3805, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO YB6.5L-B 12V', 0),
(3806, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO YB6L-B 12V', 0),
(3807, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA YB6L-B 12V', 0),
(3808, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA 12N7B-3A 12V', 0),
(3835, 'SUPER', 'BATERIA', 'INYECCION', 'BATERIA SUPER15M99 N2 OM9', 0),
(3836, 'SUPER', 'BATERIA', 'INYECCION', 'BATERIA SUPER 11M73 N2 OM9', 0),
(3849, 'ETNA', 'BATERIA', 'INYECCION', 'BATERIA ETNA FH-1215 DE 15 PLACAS', 0),
(3850, 'ETNA', 'BATERIA', 'INYECCION', 'BATERIA ETNA S-1223 DE 23 PLACAS', 0),
(3855, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 17T114P N2', 0),
(3856, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 21P144 N2', 0),
(3859, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF160G51L', 0),
(3860, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF105D31L', 0),
(3861, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF75D31R 75AH 660CCA', 0),
(3862, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF80D26R 70AH 600CCA', 0),
(3863, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF80D26L 70AH', 0),
(3864, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF75D23L 65AH 580CCA', 0),
(3865, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF55459 54AH 480CCA', 0),
(3866, 'HANKOOK', 'BATERIA', 'INYECCION', 'BATERIA HANKOOK MF56077 60AH 510CCA', 0),
(3890, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AT-23 MAXIMA DURACION', 0),
(3891, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AF-15 MAXIMA DURACION', 0),
(3892, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AF-13 MAXIMA DURACION', 0),
(3893, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AC-13 MAXIMA DURACION', 0),
(3894, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AC-11 MAXIMA DURACION', 0),
(3968, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 288D PREMIUM', 0),
(3969, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 234D PREMIUM', 0),
(4005, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RT 165 PLUS', 0),
(4006, 'ETNA', 'BATERIA', 'INYECCION', 'BATERIA ETNA W-11 DE 11 PLACAS', 0),
(4007, 'ETNA', 'BATERIA', 'INYECCION', 'BATERIA ETNA W-13 DE 13 PLACAS', 0),
(4008, 'ETNA', 'BATERIA', 'INYECCION', 'BATERIA ETNA V-13 DE 13 PLACAS', 0),
(4009, 'ETNA', 'BATERIA', 'INYECCION', 'BATERIA ETNA V-11 DE 11 PLACAS', 0),
(4038, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO 12N7A -3A 12V', 0),
(4039, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO YTX7A-BS 12V', 0),
(4040, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO YTX7L-BS', 0),
(4051, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 56637 CHATA', 0),
(4052, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 125D31L 19 PLACAS GRANDE', 0),
(4053, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 55B24L 13 PLACAS TOYOTA', 0),
(4054, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 90D26L 15 PLACAS GRANDE', 0),
(4055, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA YTX9-BS 12V', 0),
(4056, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA YB2-5L 12V', 0),
(4057, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA TTZ10S 12V', 0),
(4058, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA 12N7-3B 12V', 0),
(4059, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA YTX14L BS 12V', 0),
(4060, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO YTX4L-BS 12V', 0),
(4061, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO 12N14-3B', 0),
(4062, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO 12N9-3B 12V', 0),
(4063, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO 12N7-3B 12V', 0),
(4064, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO YTX20L-BS 12V', 0),
(4065, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO YTX7L-BS 12V', 0),
(4066, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA 12N7A-3A 12V', 0),
(4067, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA NP10-6 6V', 0),
(4068, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA NP7-12 12V', 0),
(4069, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA NP7-6 6V', 0),
(4070, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA NP4-6 6V', 0),
(4113, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET NS40-38 N2', 0),
(4171, 'ETNA', 'BATERIA', 'INYECCION', 'BATERIA ETNA HL-11 DE 11 PLACAS', 0),
(4172, 'ETNA', 'BATERIA', 'INYECCION', 'BATERIA ETNA HL-09 DE 9 PLACAS', 0),
(4202, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA 12N5-3B 12V', 0),
(4203, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO 12N5-3B 12V', 0),
(4209, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 13TOI PREMIUM', 0),
(4219, 'KOYO', 'BATERIA', 'INYECCION', 'BATERIA MOTO KOYO YTX5L-BS 12V', 0),
(4220, 'YUASA', 'BATERIA', 'INYECCION', 'BATERIA MOTO YUASA YTX5L-BS 12V', 0),
(4224, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 95D31L 17 PLACAS GRANDE', 0),
(4225, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH S545D 11 PLACAS CHATITA', 0),
(4226, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 80D26L 13 PLACAS STANDAR', 0),
(4227, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 46B24L 11 PLACAS TOYOTA', 0),
(4228, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 59218 92AH', 0),
(4229, 'BOSCH', 'BATERIA', 'INYECCION', 'BATERIA BOSCH 40B19L 11 PLACAS TICO', 0),
(4235, 'DAEWOO', 'BATERIA', 'INYECCION', 'BATERIA DAEWOO MF55B24L 11 PLACAS TOYOTA', 0),
(4262, 'SOLITE', 'BATERIA', 'INYECCION', 'BATERIA SOLITE CMF55B24L 11 PLACAS TOYOTA', 0),
(4291, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AW-11 MAXIMA DURACION', 0),
(4292, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AW-09 MAXIMA DURACION', 0),
(4339, 'DAEWOO', 'BATERIA', 'INYECCION', 'BATERIA DAEWOO MF60D31L 11 PLACAS GRANDE', 0),
(4340, 'DAEWOO', 'BATERIA', 'INYECCION', 'BATERIA DAEWOO MF75D26L 13 PLACAS CUADRADA', 0),
(4341, 'DAEWOO', 'BATERIA', 'INYECCION', 'BATERIA DAEWOO MF56077 13 PLACAS CHATA', 0),
(4342, 'DAEWOO', 'BATERIA', 'INYECCION', 'BATERIA DAEWOO MF55046 11 PLACAS CHATA', 0),
(4343, 'SOLITE', 'BATERIA', 'INYECCION', 'BATERIA SOLITE CMF55016 11 PLACAS CHATITA', 0),
(4344, 'SOLITE', 'BATERIA', 'INYECCION', 'BATERIA SOLITE CMF75D23L 13 PLACAS CUADRADA', 0),
(4345, 'SOLITE', 'BATERIA', 'INYECCION', 'BATERIA SOLITE CMF55D26L 11 PLACAS STANDAR', 0),
(4346, 'SOLITE', 'BATERIA', 'INYECCION', 'BATERIA SOLITE CMF55040 11 PLACAS CHATA', 0),
(4354, 'DAEWOO', 'BATERIA', 'INYECCION', 'BATERIA DAEWOO MF44B19L 11 PLACAS TICO', 0),
(4363, 'SOLITE', 'BATERIA', 'INYECCION', 'BATERIA SOLITE CMF105D31L 15 PLACAS GRANDE', 0),
(4364, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM YTX5L-BS 12V', 0),
(4365, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM YB7B-B12V', 0),
(4366, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM 6N6-3B 12V', 0),
(4367, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM 12N9-3B 12V', 0),
(4368, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM 12N7-3B 12V', 0),
(4369, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM YTX7L-BS 12V', 0),
(4370, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM YTX7A -BS 12V', 0),
(4371, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM YTX4L-BS 12V', 0),
(4372, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM 12N7A-3A 12V', 0),
(4373, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM 12N6-3B 12V', 0),
(4374, 'MGM', 'BATERIA', 'INYECCION', 'BATERIA MOTO MGM 12N5-3B 12V', 0),
(4410, 'SOLITE', 'BATERIA', 'INYECCION', 'BATERIA SOLITE CMF42B19L 11 PLACAS TICO', 0),
(4411, 'EXIDE', 'BATERIA', 'INYECCION', 'BATERIA EXIDE MFS40 AT', 0),
(4427, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 1247R PREMIUM', 0),
(4439, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA AC- 09 MAXIMA DURACION', 0),
(4440, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA ANS -11 MAXIMA DURACION', 0),
(4441, 'ALFA', 'BATERIA', 'INYECCION', 'BATERIA ALFA ANS- 09 MAXIMA DURACION', 0),
(4498, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 214D PREMIUM', 0),
(4499, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 19D44 PREMIUM', 0),
(4500, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 1731T PREMIUM', 0),
(4501, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 15MBI PREMIUM', 0),
(4502, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 15APCG PREMIUM', 0),
(4503, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 13APCG PREMIUM', 0),
(4504, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 11APCG PREMIUM', 0),
(4505, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 13WI PREMIUM', 0),
(4506, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 11WI PREMIUM', 0),
(4507, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 9WI PREMIUM', 0),
(4508, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 13API PREMIUM', 0),
(4509, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 11API PREMIUM', 0),
(4510, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 11TOI PREMIUM', 0),
(4511, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 10FDI PREMIUM', 0),
(4512, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 9FDI PREMIUM', 0),
(4513, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 7U1R PREMIUM', 0),
(4514, 'CAPSA', 'BATERIA', 'INYECCION', 'BATERIA CAPSA 5U1R PREMIUM', 0),
(4515, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 27P190 N2', 0),
(4516, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 23P159 N2', 0),
(4517, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 19P130 N2', 0),
(4518, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 17T114 N2', 0),
(4519, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 15MB90 N2', 0),
(4520, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 15M99 N2', 0),
(4521, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 13M87 N2', 0),
(4522, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 11M73 N2', 0),
(4523, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 13W75 N2', 0),
(4524, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 11W63 N2', 0),
(4525, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 9W50 N2', 0),
(4526, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 13S85 N2', 0),
(4527, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 11S71 N2', 0),
(4528, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 9S58 N2', 0),
(4529, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 11T56 N2', 0),
(4530, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 11D56 N2', 0),
(4531, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET 9D45 N2', 0),
(4532, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET MT38 N2', 0),
(4533, 'ENERJET', 'BATERIA', 'INYECCION', 'BATERIA ENERJET MT30 N2', 0),
(4534, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RFF 65 PLUS', 0),
(4535, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RT 230 PLUS', 0),
(4536, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RT 202 PLUS', 0),
(4537, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RT 158 PLUS', 0),
(4538, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RT 130 PLUS', 0),
(4539, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RT 115 PLUS', 0),
(4540, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD N 100 PLUS', 0),
(4541, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RMB 100 PLUS', 0),
(4542, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RMB 85 PLUS', 0),
(4543, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RF 90 PLUS', 0),
(4544, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RF 75 PLUS', 0),
(4545, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RF 65 PLUS', 0),
(4546, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RW 70 PLUS', 0),
(4547, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RW 65 PLUS', 0),
(4548, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RW 52 PLUS', 0),
(4549, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RC 70 PLUS', 0),
(4550, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RC 65 PLUS', 0),
(4551, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RC 52 PLUS', 0),
(4552, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RNS 45 PLUS', 0),
(4553, 'RECORD', 'BATERIA', 'INYECCION', 'BATERIA RECORD RNS 40 PLUS', 0),
(4477, 'TYRESOL', 'GUARDACAMARA', 'INYECCION', 'GUARDACAMARA TYRESOL R20', 0),
(3157, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 265/65R17 AT TERRAINCONTAC', 0),
(3158, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 255/60R15 SPGT SPORT', 0),
(3159, 'NEXXEN', 'LLANTA', 'INYECCION', 'LLANTA NEXEN 215/65R17 HT 98H', 0),
(3163, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 235/65R15 KL51 92H', 0),
(3164, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 265/70R15 AT OPAT', 0),
(3165, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 11.00-20 DEL R503 18PR', 0),
(3166, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 11.00-20 POS L602 18PR', 0),
(3177, 'ANTYRE', 'LLANTA', 'INYECCION', 'LLANTA ANTYRE 12R22.5 MIX TB877 18PR', 0),
(3178, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 245/55R19 HF801 103V', 0),
(3179, 'MARUTI', 'LLANTA', 'INYECCION', 'LLANTA MARUTI 7.50-16 POS INDI LUG 16PR', 0),
(3180, 'SUNOTE', 'LLANTA', 'INYECCION', 'LLANTA SUNOTE 185/70R14 SN666 88T', 0),
(3181, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 225/40R18 CSC5 88Y', 0),
(3182, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 295/40R21 M626 111W XL', 0),
(3183, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 185/70R14 ENASAVE EC300', 0),
(3184, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 265/70R17 AT PRACTICALMAX', 0),
(3185, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 11R22.5 MIX MG702 16PR', 0),
(3186, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 205/70R15 MR200 8PR', 0),
(3188, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 215/75R15 MT BIGHORN', 0),
(3189, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 285/70R17 MT BIGHORN', 0),
(3190, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 215/70R15 SUPER2000 109/107R 8PR', 0),
(3191, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 225/70R15 SF05 112/110R 8PR', 0),
(3192, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 165/65R13 HF201 77T', 0),
(3197, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 185/65R15 HF201 88H', 0),
(3198, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 185/65R14 A2000 86H', 0),
(3199, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 185/65R14 HF201 86H', 0),
(3203, 'LING LONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 205/50R16 CROSSWIND 87V', 0),
(3204, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 265/70R16 AT AT782', 0),
(3205, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 265/75R16 MT POWER ROVER', 0),
(3206, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 245/75R16 MT MT781', 0),
(3207, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 245/75R16 MT VIGOROUS', 0),
(3214, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 9.5R17.5 POS RS604 132/130R 16PR', 0),
(3215, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 265/75R16 MT VIGOROUS', 0),
(3216, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 155/70R13 CITYTOUR 75T', 0),
(3217, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 165/70R12 CITYPOWER 77T', 0),
(3218, 'TORNEL', 'LLANTA', 'INYECCION', 'LLANTA TORNEL 7.50-16 POS TXL PLUS 16PR', 0),
(3219, 'HILO', 'LLANTA', 'INYECCION', 'LLANTA HILO 265/75R16 AT X TERRA', 0),
(3220, 'FIREMAX', 'LLANTA', 'INYECCION', 'LLANTA FIREMAX 265/75R16 MT FM523', 0),
(3221, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 185/70R14 K715 88T', 0),
(3222, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 265/65R17 HT GRANTREK2', 0),
(3223, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 265/75R16 AT DYNAPRO', 0),
(3224, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/75R15 MT DYNAPRO', 0),
(3225, 'ROADTEC', 'LLANTA', 'INYECCION', 'LLANTA ROADTEC 185/70R14 LCG01 88T', 0),
(3236, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 195/70R14 HF201 91H', 0),
(3237, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 235/75R17.5 POS HF628 16PR', 0),
(3238, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 8.25R16 POS HH313 16PR', 0),
(3239, 'ARTUM', 'LLANTA', 'INYECCION', 'LLANTA ARTUM 235/60R18 A2000 107H', 0),
(3240, 'NANKANG', 'LLANTA', 'INYECCION', 'LLANTA NANKANG 205/50R15 NS-2 86V', 0),
(3241, 'DOUPRO', 'LLANTA', 'INYECCION', 'LLANTA DOUPRO 12.00R20 TRA ST968 156/153K 20PR', 0),
(3254, 'TRAILCUTER', 'LLANTA', 'INYECCION', 'LLANTA TRAILCUTTER 265/65R17 AT RADIAL', 0),
(3273, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 31X10.5R15 AT DURABLEMAX', 0),
(3275, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 205/55R16 FR710 89T', 0),
(3276, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 225/55R18 HT DUELER', 0),
(3277, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/70R15 AT DUELER', 0),
(3278, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA MOTO BRIDGESTONE 140/70ZR17 BATLAX 20RAZ 66H', 0),
(3279, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 225/70R16 AT DESTINATION', 0),
(3280, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 215/70R16 AT DUELER', 0),
(3283, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 215/70R16 AT DURABLEMAX', 0),
(3284, 'MARUTI', 'LLANTA', 'INYECCION', 'LLANTA MARUTI 4.00-8 DEL GOLD 8PR', 0),
(3285, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MOTO MICHELIN 80/90-17 CYTI PRO 50S', 0),
(3286, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MOTO MICHELIN 70/90-17 CYTI PRO 43S', 0),
(3287, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MOTO MICHELIN 120/90-17 SIRAC STREED 64T', 0),
(3288, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MOTO MICHELIN 150/60R17 PILOT STREET 66H', 0),
(3289, 'DURO', 'LLANTA', 'INYECCION', 'LLNTA MOTO DURO 130/60-13 HF903 55J', 0),
(3290, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 215/45R17 MAZ1 91W', 0),
(3291, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 215/50R17 MAZ3 91W', 0),
(3292, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 275/45R20 MAS2 111V', 0),
(3293, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 195/65R15 HG01 95H', 0),
(3294, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 205/60R15 P7CINT 91H', 0),
(3295, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 195/65R15 P400 91H', 0),
(3296, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 175/70R14 P4CINT 84T', 0),
(3297, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 265/60R18 AT SCORPION', 0),
(3298, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 245/65R17 AT SCORPION', 0),
(3299, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 265/70R16 AT SCORPION', 0),
(3300, 'SOLIDEAL', 'LLANTA', 'INYECCION', 'LLANTA SOLIDEAL 26.5-25 L3 G3 E3 TUBELES LOADER 28PR', 0),
(3303, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 215/70R15 A2000 97S', 0),
(3304, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 5.00R12 SUPER9000 88/86P 10PR', 0),
(3305, 'GOLDPARTNER', 'LLANTA', 'INYECCION', 'LLANTA GOLDPARTNER 265/70R19.5 POS GP704 16PR', 0),
(3306, 'YUEHENG', 'LLANTA', 'INYECCION', 'LLANTA YUEHENG 12.00R20 TRA YH-288 156/153K 20PR', 0),
(3307, 'WINDA', 'LLANTA', 'INYECCION', 'LLANTA WINDA 165/65R14 WP15 79H', 0),
(3308, 'WINDA', 'LLANTA', 'INYECCION', 'LLANTA WINDA 195/75R16 WR01 107/105R', 0),
(3309, 'WINDA', 'LLANTA', 'INYECCION', 'LLANTA WINDA 195/65R15 WP16 95H', 0),
(3310, 'WINDA', 'LLANTA', 'INYECCION', 'LLANTA WINDA 205/60R15 WP16 91H', 0),
(3311, 'WINDA', 'LLANTA', 'INYECCION', 'LLANTA WINDA 185/70R14 WP15 88T', 0),
(3312, 'WINDA', 'LLANTA', 'INYECCION', 'LLANTA WINDA 185/70R13 WP15 86T', 0),
(3313, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 6.50-14 POS TD442 8PR', 0),
(3314, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 6.50-14 DEL TH200 8PR', 0),
(3315, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 23.5-25 L3 E3 24PR OTRR', 0),
(3316, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 9.5R17.5 POS XD759 18PR', 0),
(3317, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 225/75R16 AT PRACTICALMAX', 0),
(3318, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 225/70R16 AT PRACTICALMAX', 0),
(3319, 'CACHLAND', 'LLANTA', 'INYECCION', 'LLANTA CACHLAND 195/60R15 CH268 88V', 0),
(3320, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 12R22.5 MIX WS118 152/149M 18PR', 0),
(3321, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 11R22.5 MIX WS118 146/143M 16PR', 0),
(3322, 'CACHLAND', 'LLANTA', 'INYECCION', 'LLANTA CACHLAND 195/50R15 CH-861 86V', 0),
(3323, 'VIKRANT', 'LLANTA', 'INYECCION', 'LLANTA VIKRANT 7.00-15 DEL TRACK KING 12PR', 0),
(3324, 'CAMSO', 'LLANTA', 'INYECCION', 'LLANTA CAMSO 12.5/80-18 SL R4 12PR IMP SUPER', 0),
(3325, 'CAMSO', 'LLANTA', 'INYECCION', 'LLANTA CAMSO 12-16.5 XTRA WALL 12PR SKS', 0),
(3334, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 225/75R16 AT WRANGLER', 0),
(3335, 'CAMSO', 'LLANTA', 'INYECCION', 'LLANTA CAMSO 19.5L-24 SLK 12PR R4', 0),
(3336, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/75R16 MT DUELER D674', 0),
(3337, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA MOTO BRIDGESTONE 180/55ZR17 S20RSZ 73W', 0),
(3338, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA MOTO BRIDGESTONE 120/70ZR17 BT0003 62W', 0),
(3339, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA MOTO BRIDGESTONE 110/70ZR17 003FZ 54W', 0),
(3341, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 195/55R15 HG01 85V', 0),
(3342, 'TEKPRO', 'LLANTA', 'INYECCION', 'LLANTA TEKPRO 185/70R13 TEK01 86T', 0),
(3343, 'BKT', 'LLANTA', 'INYECCION', 'LLANTA BKT 17.5-24 TR459 R4 12PR XTRAIL', 0),
(3344, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 265/70R17 MT DYNAPRO', 0),
(3350, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 35X12.5R17 MT MUD TRAC', 0),
(3351, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 6.50-16 DEL HI MILLER 8PR', 0),
(3352, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 215/75R17.5 DEL REGIONAL 16PR', 0),
(3353, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 6.50-14 DEL CAMINERA 8PR', 0),
(3354, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 8.25-16 DEL HI MILER CT176 16PR', 0),
(3355, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 195R14 D108 105/103N 8PR', 0),
(3356, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 185/65R15 A2000 88H', 0),
(3357, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 175R13 C212 97/95Q 8PR', 0),
(3358, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 185R14 D108 103/101Q 8PR', 0),
(3359, 'KAIZEN', 'LLANTA', 'INYECCION', 'LLANTA KAIZEN 12.00-20 POS L002 20PR 156/154K', 0),
(3360, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 305/70R17 MT BIGHORN', 0),
(3363, 'LANVIGATOR', 'LLANTA', 'INYECCION', 'LLANTA LANVIGATOR 225/75R16 AT CATCHFORS', 0),
(3364, 'LANVIGATOR', 'LLANTA', 'INYECCION', 'LLANTA LANVIGATOR 195/50R15 CATCHPOWER 82V', 0),
(3365, 'LANVIGATOR', 'LLANTA', 'INYECCION', 'LLANTA LANVIGATOR 225/65R16 MILEMAX 112/110T', 0),
(3366, 'LANVIGATOR', 'LLANTA', 'INYECCION', 'LLANTA LANVIGATOR 185R14 MILAMAX 102/100R', 0),
(3367, 'LANVIGATOR', 'LLANTA', 'INYECCION', 'LLANTA LANVIGATOR 155R12 MILEMAX 88/88Q 8PR', 0),
(3368, 'GOODRIDE', 'LLANTA', 'INYECCION', 'LLANTA GOODRIDE 8.25R16 MIX CR926 128/126L 14PR', 0),
(3369, 'LANVIGATOR', 'LLANTA', 'INYECCION', 'LLANTA LANVIGATOR 215/75R17.5 DEL S201 135/133J 18PR', 0),
(3370, 'SAILUN', 'LLANTA', 'INYECCION', 'LLANTA SAILUN 185R14 SL12 102/100Q 8PR', 0),
(3371, 'SAILUN', 'LLANTA', 'INYECCION', 'LLANTA SAILUN 195/65R14 ATREZZO 89H', 0),
(3372, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 245/75R16 AT PAYAK', 0),
(3373, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 5.00R12 R406 88/86P 10PR', 0),
(3374, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 265/65R17 AT PAYAK', 0),
(3375, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 245/75R16 MT BIGHORN', 0),
(3376, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 245/75R16 AT BRAVO', 0),
(3377, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 195/60R15 MAT1 88T', 0),
(3378, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 205/50R15 MAZ1 89V', 0),
(3379, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 235/45R17 MAZ1 97W', 0),
(3380, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 265/65R17 MT BIGHORN', 0),
(3381, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 265/70R16 AT BRAVO', 0),
(3382, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 265/70R16 MT BIGHORN', 0),
(3383, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 205/50R15 HF805 86V', 0),
(3384, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 155R12 SUPER2000 88/86Q 8PR', 0),
(3385, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 155/70R12 HF201 72T', 0),
(3386, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 195/75R14 ST FSR3', 0),
(3387, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 255/60R18 HT DESTINATION', 0),
(3388, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 7.00-15 POS XD-107 12PR', 0),
(3389, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 265/60R18 AT SL369', 0),
(3390, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 215/75R17.5 POS TR689 16PR', 0),
(3391, 'FIREMAX', 'LLANTA', 'INYECCION', 'LLANTA FIREMAX 245/75R16 AT FM501', 0),
(3392, 'GREMAX', 'LLANTA', 'INYECCION', 'LLANTA GREMAX 245/75R16 MT CAPTURAR', 0),
(3393, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 195/60R14 LCG01 86H', 0),
(3394, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 215/75R16 MR200 106/ 108Q', 0),
(3395, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 12R22.5 MIX HF702 18PR 152/149M', 0),
(3396, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 275/40R20 HP801 106W', 0),
(3397, 'BARUM', 'LLANTA', 'INYECCION', 'LLANTA BARUM 235/75R15 AT BRAVURIS', 0),
(3398, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 225/50R16 SF888 92V', 0),
(3399, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 175/70R13 HF201 82T', 0),
(3400, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 215/75R17.5 DEL HH111 16PR', 0),
(3401, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 205/60R14 HF201 88H', 0),
(3402, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 215/65R16 ST SF668', 0),
(3403, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 12.00R20 TRA HF321 18PR', 0),
(3404, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 11R22.5 POS PERFORMANC. 146/143K', 0),
(3405, 'ARMOUR', 'LLANTA', 'INYECCION', 'LLANTA ARMOUR 21-L24 SOLAR MR 14PR R4A', 0),
(3406, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 265/70R16 AT VIGOROUS', 0),
(3407, 'BKT', 'LLANTA', 'INYECCION', 'LLANTA BKT 16.9-28 TR459 12PR TRA', 0),
(3408, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 195/55R15 TR968 88H', 0),
(3409, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 265/65R17 AT DISCOVERER', 0),
(3410, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 195R15 MR100 106/104Q 8PR', 0),
(3411, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 195R14 MR100 8PR', 0),
(3412, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 155R12 MR100 8PR 88/86Q', 0),
(3413, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 275/40R20 MAZ2 106V', 0),
(3414, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 265/65R18 AT BRAVO', 0),
(3415, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 255/60R18 AT BRAVO', 0),
(3416, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 215/70R16 AT AT771', 0),
(3417, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 265/70R15 AT PRESA', 0),
(3418, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 235/75R15 AT BRAVO', 0),
(3419, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 215/75R15 AT BRAVO', 0),
(3420, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 205/75R15 MT BRAVO', 0),
(3421, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 27X8.50R14 MT BRAVO', 0),
(3422, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 27X8.50R14 AT BRAVO', 0),
(3423, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 205/70R15 AT BRAVO', 0),
(3424, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 225/45R16 MAZ3 93W', 0),
(3425, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 205/55R16 MAZ1 94W', 0),
(3426, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 205/50R16 MAZ1 91W', 0),
(3427, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 205/55R15 MAZ1 88V', 0),
(3428, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 165/65R13 MAP1 77H', 0),
(3429, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 155/70R12 MA701 73H TL', 0),
(3430, 'MAXXIS', 'LLANTA', 'INYECCION', 'LLANTA MAXXIS 155R12 SAKURA 8PR 88/86N', 0),
(3431, 'TAITONG', 'LLANTA', 'INYECCION', 'LLANTA TAITONG 7.50R16 POS HS918 14PR', 0),
(3432, 'TAITONG', 'LLANTA', 'INYECCION', 'LLANTA TAITONG 7.50R16 MIX HS268 14PR', 0),
(3433, 'GREMAX', 'LLANTA', 'INYECCION', 'LLANTA GREMAX 265/65R17 AT MAX', 0),
(3434, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 215/75R15 AT PRACTICALMAX', 0),
(3435, 'GREMAX', 'LLANTA', 'INYECCION', 'LLANTA GREMAX 185R14 CF12 8PR 104/1046R', 0),
(3436, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 205/55R16 H202 88V', 0),
(3437, 'HILO', 'LLANTA', 'INYECCION', 'LLANTA HILO 165/65R14 GENESYS 72H', 0),
(3438, 'HILO', 'LLANTA', 'INYECCION', 'LLANTA HILO 205/60R14 GENESYS 84H', 0),
(3439, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 165R13 H202 6PR', 0),
(3440, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 195R15 VAN TOUR 106/104R 8PR', 0),
(3441, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 205/55R16 ULTRA TOUR 94V', 0),
(3442, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 215/75R17.5 POS D801 18PR', 0),
(3443, 'SIERRA', 'LLANTA', 'INYECCION', 'LLANTA SIERRA 11R22.5 POS SR301 16PR', 0),
(3444, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 31X10.50R15 AT ALL TERRAIN', 0),
(3445, 'GOALSTAR', 'LLANTA', 'INYECCION', 'LLANTA GOALSTAR 245/75R16 MT CATCHFORS', 0),
(3446, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 11R22.5 TRA WS826 16PR 146/143M', 0),
(3447, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 195/65R15 T65 88T', 0),
(3448, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 12.00R20 TRA HF707 18PR', 0),
(3449, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 12.00R20 MIX HF702 18PR', 0),
(3450, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 12.00R20 MIX WS118 156/153K 20PR', 0),
(3451, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 12.00R20 TRA WS678 156/154K 20PR', 0),
(3452, 'FIREMAX', 'LLANTA', 'INYECCION', 'LLANTA FIREMAX 165/65R13 FM316 77T', 0),
(3453, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 12RR2.5 TRA WS678 142/149M 18PR', 0),
(3454, 'DOUPRO', 'LLANTA', 'INYECCION', 'LLANTA DOUPRO 12.00R20 MIX YB258 18PR', 0),
(3455, 'ANNAITE', 'LLANTA', 'INYECCION', 'LLANTA ANNAITE 275/70R22.5 POS A785 18PR', 0),
(3457, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 215/60R17 HT EFICIENTGRIP 96H', 0),
(3469, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 205/55R16 HG01 91W', 0),
(3470, 'TEKPRO', 'LLANTA', 'INYECCION', 'LLANTA TEKPRO 205/50R16 TEK01 87W', 0),
(3471, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 185/70R14 LCG01 88T', 0),
(3473, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 205/60R13 SF688 86T', 0),
(3474, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 255/55R18 HT CITYRACING', 0),
(3475, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 265/60R18 HT CITYROVER', 0),
(3476, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 205/60R13 GTMAX 86H', 0),
(3477, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 12R22.5 TRA HF768 18PR', 0),
(3478, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 225/70R16 AT LANDER', 0),
(3479, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 215/70R16 AT LANDER', 0),
(3480, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 225/75R15 AT LANDER', 0),
(3487, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 205/60R15 MAXIMUS 91H', 0),
(3488, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 165/65R14 RS907 79H', 0),
(3489, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 165/65R14 ALTIMAX 79T', 0),
(3490, 'MASTERCRAFT', 'LLANTA', 'INYECCION', 'LLANTA MASTERCRAFT 265/70R17 AT WILDCAT', 0),
(3491, 'MASTERCRAFT', 'LLANTA', 'INYECCION', 'LLANTA MASTERCRAFT 265/70R16 AT WILDCAT', 0),
(3492, 'MASTERCRAFT', 'LLANTA', 'INYECCION', 'LLANTA MASTERCRAFT 185/70R14 STRATEGY 88T', 0),
(3493, 'MASTERCRAFT', 'LLANTA', 'INYECCION', 'LLANTA MASTERCRAFT 185/70R13 STRATEGY 85S', 0),
(3494, 'MASTERCRAFT', 'LLANTA', 'INYECCION', 'LLANTA MASTERCRAFT 175/70R14 STRATEGY 84S', 0),
(3495, 'MASTERCRAFT', 'LLANTA', 'INYECCION', 'LLANTA MASTERCRAFT 175/70R13 STRATEGY 82T', 0),
(3496, 'CAMSO', 'LLANTA', 'INYECCION', 'LLANTA CAMSO 14.00-24 L3 G3 SLIK 16PR', 0),
(3497, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 215/70R16 AT A929', 0),
(3498, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 265/75R16 MT A929', 0),
(3499, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 235/75R15 AT ROADVENTURE', 0),
(3500, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 205/60R14 KU33 88H', 0),
(3501, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 205/50R16 A607 91W', 0),
(3502, 'SOLIDEAL', 'LLANTA', 'INYECCION', 'LLANTA SOLIDEAL 20.5-25 LOAD MASTER L3 G3 20PR', 0),
(3503, 'OTANI', 'LLANTA', 'INYECCION', 'LLANTA OTANI 19.5-24 12PR G-45 R4 RETAIL', 0),
(3504, 'TRANSTONE', 'LLANTA', 'INYECCION', 'LLANTA TRANSTONE 12.5/80-18 12PR R4 START', 0),
(3506, 'SIERRA', 'LLANTA', 'INYECCION', 'LLANTA SIERRA 11R22.5 TRA SR317 16PR', 0),
(3521, 'FESITE', 'LLANTA', 'INYECCION', 'LLANTA FESITE 12.00R24 MIX HF702 20PR', 0),
(3522, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/55R19 DYNAPRO 104H', 0),
(3523, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 185/70R13 K715 88H', 0),
(3524, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 245/70R16 AT SCORPION', 0),
(3525, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 185/70R14 P400 88H', 0),
(3526, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 205/50R16 CITY RACING 91W', 0),
(3528, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 9.00-20 DEL CAMINERA 14PR', 0),
(3529, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 9.00-20 POS CHASQUI 14PR', 0),
(3530, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 235/60R16 HT WRANGLER SUV 100H', 0),
(3531, 'TAITONG', 'LLANTA', 'INYECCION', 'LLANTA TAITONG 12.00R24 MIX HS268 20PR', 0),
(3532, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 165/65R13 LCG01 77Q', 0),
(3533, 'WANDA', 'LLANTA', 'INYECCION', 'LLANTA WANDA 195R15 WR092 106/104Q 8PR', 0),
(3534, 'ACCELERA', 'LLANTA', 'INYECCION', 'LLANTA ACCELERA 255/55R18 ALPHA 92W', 0),
(3535, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MOTO MICHELIN 120/80-18 T63 62S TT', 0),
(3536, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MOTO MICHELIN 90/90-18 SIRAC STREED', 0),
(3537, 'DURO', 'LLANTA', 'INYECCION', 'LLANTA MOTO DURO 90/90-18 HF329 PISTERA', 0),
(3538, 'DURO', 'LLANTA', 'INYECCION', 'LLANTA MOTO DURO 120/80-18 MEDIAN 904 HF', 0),
(3539, 'DURO', 'LLANTA', 'INYECCION', 'LLANTA MOTO DURO 90/90-17 HF918 49P', 0),
(3545, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 12R22.5 POS HN10 18PR', 0),
(3546, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 12R22.5 MIX HN08 18PR', 0),
(3547, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 9.5R17.5 POS ADR35 18PR', 0),
(3548, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 9.5R17.5 DEL ATR55 14PR', 0),
(3549, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 12.00R20 MIX HN10 18PR', 0),
(3550, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 12.00R20 TRA HN08 18PR', 0),
(3551, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 215/75R14 AT DESTINATION', 0),
(3552, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 205/65R15 AT DESTINATION', 0),
(3553, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 12.00R20 TRA WS658 156/153K 20PR', 0),
(3555, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 9.5R17.5 DEL XD414 132/130K 18PR', 0),
(3556, 'SPORTRAK', 'LLANTA', 'INYECCION', 'LLANTA SPORTRAK 8.25R16 POS BY35 16PR', 0),
(3557, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 165/65R13 ALTIMAX 77T', 0),
(3579, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 205/70R15 SF05 8PR', 0),
(3580, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 215/75R15 AT AT782', 0),
(3581, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 155R12 SF01 8PR 88/86Q', 0),
(3582, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 235/40R17 SX1-EVO 90V', 0),
(3583, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 225/45R17 SX1-EVO 94V', 0),
(3584, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 215/50R17 SX1-EVO 91V', 0),
(3585, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 215/45R17 SX1-EVO 91V', 0),
(3587, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 205/75R15 ST TRAVOMATE', 0),
(3588, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 225/75R16 AT DUREVOLE', 0),
(3589, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 175/70R14 ROLIT6 84T', 0),
(3590, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 175/65R14 ROLIT6 86T', 0),
(3591, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 8.25R20 POS WS684 139/137K 16PR', 0),
(3592, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 7.50R16 POS WS648 122/118L 14PR', 0),
(3593, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 7.50R16 MIX WS118 122/118L 14PR', 0),
(3594, 'WOSEN', 'LLANTA', 'INYECCION', 'LLANTA WOSEN 8.25R16 POS WS684 128/124L 16PR', 0),
(3595, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 215/45R17 TR968 91V', 0),
(3596, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 195/50R15 TR928 82H', 0),
(3597, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 195/60R15 TR928 88H', 0),
(3598, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 185/60R15 TR928 84H', 0),
(3600, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 265/75R16 AT PRACTICALMAX', 0),
(3601, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 225/75R16 AT DUELER', 0),
(3602, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 275/70R16 AT DUELER', 0),
(3603, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 195R14 106/104Q VAN TOUR 8PR', 0),
(3604, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 245/75R16 MT MUD TERRAIN', 0),
(3605, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 265/50R20 AT ALL TERRAIN', 0),
(3606, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 245/65R17 AT ALL TERRAIN', 0),
(3607, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 245/75R16 AT ALL TERRAIN', 0),
(3608, 'SOLIDEAL', 'LLANTA', 'INYECCION', 'LLANTA SOLIDEAL 12.5/80-18 12PR COMSO L2', 0),
(3609, 'FORERUNER', 'LLANTA', 'INYECCION', 'LLANTA FORERUNER 12.5/80-18 12PR', 0),
(3633, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 265/50R20 HT SPORT MAX 111Y', 0),
(3634, 'RINTAL', 'LLANTA', 'INYECCION', 'LLANTA RINTAL 12-16.5 SKS4 L2 12PR NON DIRECCINAL', 0),
(3639, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 245/40R18 SX-1EVO 97W', 0),
(3640, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 265/70R16 MT DYNAPRO', 0),
(3641, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 225/65R17 HT DYNAPRO', 0),
(3643, 'ADVANCE', 'LLANTA', 'INYECCION', 'LLANTA ADVANCE 11R24.5 L3 GL909A 16PR', 0),
(3644, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 235/70R16 AT AT782', 0),
(3645, 'BLACKLION', 'LLANTA', 'INYECCION', 'LLANTA BLACKLION 275/70R22.5 DEL BA126 148/145M 18PR', 0),
(3646, 'ANNAITE', 'LLANTA', 'INYECCION', 'LLANTA ANNAITE 265/70R19.5 DEL 366 148/145M 16PR', 0),
(3647, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 9.5R17.5 DEL HH121 16PR', 0),
(3648, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 7.50R16 POS HH305 14PR', 0),
(3649, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 215/75R16 ST SUPER2000 116/114R', 0),
(3650, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 285/65R17 AT CONQUEROR', 0),
(3651, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 275/65R17 AT CONQUEROR', 0),
(3652, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 205/50R16 SX2-L 87V', 0),
(3653, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 205/55R15 SX1-608 88V', 0),
(3654, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 215/55R16 SX1-EVO 93V', 0),
(3655, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 195/55R15 SX1-EVO 85V', 0),
(3656, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 195/50R15 SX1-EVO 86V', 0),
(3657, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 185/60R13 SX1-EVO 80H', 0),
(3658, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 205/60R13 SX1-EVO 86H', 0),
(3659, 'VIKRANT', 'LLANTA', 'INYECCION', 'LLANTA VIKRANT 7.00-15 POS STAR LUG 12PR', 0),
(3660, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 235/75R15 MT PAYAK', 0),
(3661, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 225/40R18 SX1-EVO 92H', 0),
(3662, 'SONAR', 'LLANTA', 'INYECCION', 'LLANTA SONAR 205/60R14 SX1-EVO 92H', 0),
(3663, 'BKT', 'LLANTA', 'INYECCION', 'LLANTA BKT 18-19.5 MP567 L3 18PR', 0),
(3664, 'BEARWAY', 'LLANTA', 'INYECCION', 'LLANTA BEARWAY 205/50R17 YS618 91W', 0),
(3665, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 225/75R16 AT SCORPION', 0),
(3666, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 165/65R13 MAXIMUS 77T', 0),
(3667, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 175/70R13 SU830 82T', 0),
(3668, 'KAPSEN', 'LLANTA', 'INYECCION', 'LLANTA KAPSEN 12.00R20 TRA HS801Q 156/153K 20PR', 0),
(3669, 'GOALSTAR', 'LLANTA', 'INYECCION', 'LLANTA GOALSTAR 215/65R16 CATCHGRE 98H', 0),
(3670, 'TAITONG', 'LLANTA', 'INYECCION', 'LLANTA TAITONG 8.25R16 MIX HS268 125/124K 16PR', 0),
(3671, 'TAITONG', 'LLANTA', 'INYECCION', 'LLANTA TAITONG 9.5R17.5 POS HS928 143/141M 18PR TL', 0),
(3672, 'TAITONG', 'LLANTA', 'INYECCION', 'LLANTA TAITONG 9.5R17.5 DEL HS206 18PR 143/141M TL', 0),
(3673, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 205R16 HT DUELER', 0),
(3700, 'BFGOODRICH', 'LLANTA', 'INYECCION', 'LLANTA BFGOODRICH 165/65R14 GGRIP', 0),
(3701, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 175/70R13 LCG01', 0),
(3702, 'GREMAX', 'LLANTA', 'INYECCION', 'LLANTA GREMAX 205/60R15 CAPTURAR', 0),
(3703, 'GREMAX', 'LLANTA', 'INYECCION', 'LLANTA GREMAX 175/70R14 CAPTURAR', 0),
(3704, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 185/70R13 LCG01', 0),
(3705, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 9.00R20 MIX MG702 18PR', 0),
(3706, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 215/45R17 SPORT ONE 91W', 0),
(3707, 'GINELL', 'LLANTA', 'INYECCION', 'LLANTA GINELL195/55R15 CF500', 0),
(3708, 'BORISTAR', 'LLANTA', 'INYECCION', 'LLANTA BORISTAR 215/75R15 MT BSMTX7', 0),
(3709, 'MULTIRAC', 'LLANTA', 'INYECCION', 'LLANTA MULTIRAC 225/75R16 MT MULTERRAIN', 0),
(3710, 'MULTIRAC', 'LLANTA', 'INYECCION', 'LLANTA MULTIRAC 245/75R16 MT MULTIRRAIN', 0),
(3711, 'KETER', 'LLANTA', 'INYECCION', 'LLANTA KETER 195R15 KT656 8PR', 0);
INSERT INTO `prodstock` (`IdProducto`, `ProductoMarca`, `ProductoCategoria`, `FormaFarmaceutica`, `Producto`, `Stock`) VALUES
(3712, 'CATCHFORSE', 'LLANTA', 'INYECCION', 'LLANTA CATCHFORSE 245/75R16 MT WINDFORSE', 0),
(3719, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 265/65R17 HT SCORPION', 0),
(3720, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 195/60R15 88H P1CINT 88H', 0),
(3721, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 225/40R18 PZERO 82Y', 0),
(3722, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 195/55R15 P1CNT 85V', 0),
(3723, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 165/65R13 P1CINT 77T', 0),
(3724, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 255/60R18 HT ALTIMAX', 0),
(3725, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 185/65R14 86H', 0),
(3726, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 225/65R16 ST SUPER2000', 0),
(3727, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/70R16 AT DUELER D693', 0),
(3737, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 265/65 R17 AT VIGOROUS', 0),
(3740, 'DURO', 'LLANTA', 'INYECCION', 'LLANTA DURO 10-400 203 HF CHALLY', 0),
(3741, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MICHELIN 110/70R17 54H PILOT STREET RADIAL TL/TT', 0),
(3742, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE185/70R13 H550-A', 0),
(3743, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 6.50R14 AU01', 0),
(3744, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 245/75R16 AT WRANGLER', 0),
(3745, 'LINGLONG', 'LLANTA', 'INYECCION', 'LLANTA LINGLONG 7.00R16 POS D955', 0),
(3746, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 185/65R17', 0),
(3747, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 195/65R15 ZIEX ZZ912', 0),
(3748, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 205/60R14 SOLUS KH17', 0),
(3749, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 195R14C FALKEN LINAM', 0),
(3750, 'MARSHAL', 'LLANTA', 'INYECCION', 'LLANTA MARSHAL 215/45R17 MATRAC-FX', 0),
(3751, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 205/70R15 KO10 8PR', 0),
(3752, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 215/80R16 WRANGLER AT', 0),
(3753, 'LINGLONG', 'LLANTA', 'INYECCION', 'LLANTA LINGLONG 6.50-15 POS LR625 8PR', 0),
(3754, 'MILEMAX', 'LLANTA', 'INYECCION', 'LLANTA LANVIGATOR 195R15 MILEMAX 106/104R 8PR', 0),
(3755, 'SAILUN', 'LLANTA', 'INYECCION', 'LLANTA SAILUN 205/55R16 ATREZZO 92H', 0),
(3756, 'SAILUN', 'LLANTA', 'INYECCION', 'LLANTA SAILUN 205/50R15 SH402 88H', 0),
(3757, 'GOALSTAR', 'LLANTA', 'INYECCION', 'LLANTA GOALSTAR 155/70R12 GP100 77T TL', 0),
(3758, 'GOALSTAR', 'LLANTA', 'INYECCION', 'LLANTA GOALSTAR 275/35ZR20 BLAZER', 0),
(3759, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 165/60R14 RS-ZERO', 0),
(3760, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 195/70R14 ROLIT6 91H', 0),
(3761, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 265/70R17 AT DURELOVE', 0),
(3762, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 215/75R15 HT DURELOVE', 0),
(3763, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 235/65R17 AT DURELOVE', 0),
(3764, 'APOLLO', 'LLANTA', 'INYECCION', 'LLANTA APOLLO 7.50-16 DEL AMARDELEXU 16PR', 0),
(3765, 'APOLLO', 'LLANTA', 'INYECCION', 'LLANTA APOLLO 7.50-16 POS MILESTAR 16PR', 0),
(3766, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 245/70R16 AT DYNAPRO', 0),
(3767, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 215/70R16 ST RA08', 0),
(3768, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 165/65R14T K715 77T', 0),
(3770, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 195R15 SF05 106/104R 8PR', 0),
(3771, 'CACHLAND', 'LLANTA', 'INYECCION', 'LLANTA CACHLAND 155/65R13 CH-268 73T', 0),
(3772, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 155/70R13 H202 75T TL', 0),
(3773, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 195/60R14 86H H202 COMFORTMAX AS', 0),
(3774, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 195/70R15 104/102R R501 DURABLEMAX', 0),
(3775, 'CEAT', 'LLANTA', 'INYECCION', 'LLANTA CEAT 14.00-24 16PR G2 GRADER XL', 0),
(3776, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 275/60R20 MT WHITE K334', 0),
(3777, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 285/50R20 AT WHITE K325', 0),
(3778, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA 225/65 R17 102H HT782 SUNFULL', 0),
(3779, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA 235/65 R16C 8PR 115/113T SF05 SUNFULL', 0),
(3780, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 225/65R16 ST SF05', 0),
(3781, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA 185 R14C 8PR 102/100R SUPER2000 HIFLY', 0),
(3782, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 165/65R13 SF688 77T', 0),
(3783, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA 16.9X28 14PR RT805M (INDIA) ORNET', 0),
(3784, 'CEAT', 'LLANTA', 'INYECCION', 'LLANTA CEAT 16.9-28 12PR FARMAX C/CAMARA', 0),
(3785, 'ARMOUR', 'LLANTA', 'INYECCION', 'LLANTA ARMOUR 16.9-28 10PR R1 SET GORIR', 0),
(3786, 'MITAS', 'LLANTA', 'INYECCION', 'LLANTA MITAS 110/70-17 MRACER 54S', 0),
(3787, 'DURO', 'LLANTA', 'INYECCION', 'LLANTA DURO 120/90-17 TRAIL', 0),
(3788, 'MITAS', 'LLANTA', 'INYECCION', 'LLANTA ENDURO RCAR 120/90-17 MITAS', 0),
(3789, 'MITAS', 'LLANTA', 'INYECCION', 'LLANTA ENDURO RCAR 150/70-17 MITAS', 0),
(3790, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 24X9-11 AT', 0),
(3791, 'H-TRAK', 'LLANTA', 'INYECCION', 'LLANTA H-TRAK 23X8-11 AT', 0),
(3794, 'TEKPRO', 'LLANTA', 'INYECCION', 'LLANTA TEKPRO 185/70R14 TEK01 88T', 0),
(3795, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 225/40R18 MR182 91V', 0),
(3796, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 175/65R14 MR162 88T', 0),
(3797, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 235/65R16 MR200 8PR', 0),
(3798, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 155R13 MR200 8PR', 0),
(3799, 'GREMAX', 'LLANTA', 'INYECCION', 'GREMAX 165/60R14 CAPTURAR CF18', 0),
(3800, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 11R22.5 POS MG312 18PR', 0),
(3809, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 10.00-20 DEL CAMINERA 16PR', 0),
(3811, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 235/65R17 HT CROSS ACE 108L', 0),
(3812, 'AELOUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 225/65R17 HT CROSS ACE 102H', 0),
(3813, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 225/70R16 HT CROSS ACE 103S', 0),
(3814, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 215/70R16 HT CROSS ACE 100S', 0),
(3815, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 175/65R14 82H PRECISION ACE', 0),
(3816, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 195R15 TRANS ACE ALO1 106/104', 0),
(3817, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 195R14 TRANS ACE ALO1 106/104', 0),
(3818, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 31X10.50R15 AT CROSS ACE', 0),
(3819, 'LUHE', 'LLANTA', 'INYECCION', 'LLANTA LUHE 7.50-15 DEL. 14PR RIB', 0),
(3820, 'LUHE', 'LLANTA', 'INYECCION', 'LLANTA LUHE 7.50-15 POST. 14PR LUG', 0),
(3821, 'APOLLO', 'LLANTA', 'INYECCION', 'LLANTA APOLLO 6.50-14 POS 10PR HERCULES', 0),
(3822, 'APOLLO', 'LLANTA', 'INYECCION', 'LLANTA APOLLO 6.50-14 DEL 8PR CARGO RIB', 0),
(3833, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 165/60R14 ALTIMAX HP 75H', 0),
(3834, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 205/55R16 POWERCONTAC 91H', 0),
(3837, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 175/65R14 SU830 82H', 0),
(3838, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 195/60R15 MAXIMUS M1 88H', 0),
(3839, 'ANTARES', 'LLANTA', 'INYECCION', 'LLANTA ANTARES 245/70R16 AT SMTA7', 0),
(3840, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 265/60R18 AT SU800 110H', 0),
(3841, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 265/70R16 AT CROSSCONTACT', 0),
(3842, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 265/65R17 HT CROSSCONTACT', 0),
(3843, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 195/65R15 91H POWERCONTACT', 0),
(3844, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 11R22.5 DEL XD106 146/148R 16PR', 0),
(3845, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 11R22.5 POS. RS604 16PR', 0),
(3846, 'SPORTRAK', 'LLANTA', 'INYECCION', 'LLANTA SPORTRAK 12R22.5 DEL SP398 18PR', 0),
(3847, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 185/70R13 RE740 86T', 0),
(3851, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 215/50R17 TURANZA 91V', 0),
(3852, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 205/70R15 AT DUELER D693', 0),
(3853, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 275/45R20 HT DESTINATION', 0),
(3854, 'BEARWAY', 'LLANTA', 'INYECCION', 'LLANTA BAERWAY 155R12 BW168 8PR 88/86Q', 0),
(3857, 'ARMOUR', 'LLANTA', 'INYECCION', 'LLANTA ARMOUR 23.5-25 E-3 20PR', 0),
(3858, 'BKT', 'LLANTA', 'INYECCION', 'LLANTA BKT 14-17.5 NHS 14PR SKID POWER', 0),
(3885, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 245/75R16 MT PAYAK', 0),
(3886, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/65R17 HT 104H', 0),
(3887, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 225/70R16 AT DYNAPRO', 0),
(3888, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/55R19 HP XL RA33 105V', 0),
(3889, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.50-16 POS. PANTANERA 10PR', 0),
(3895, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 6.50-14 POS 8PR TD-442', 0),
(3896, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 6.50-14 DEL 8PR TH-200', 0),
(3898, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 8.25R20 MIX MG-702 139/137L 16PR', 0),
(3899, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 195/70R15 MR200 8PR', 0),
(3900, 'GREMAX', 'LLANTA', 'INYECCION', 'LLANTA GREMAX 215/75R16 CAPTURAR 113/111R 8PR', 0),
(3901, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 205/65R15 94H HG01', 0),
(3902, 'LUCKYLAND', 'LLANTA', 'INYECCION', 'LLANTA LUCKYLAND 195/60R15 LCG01 88V', 0),
(3903, 'FIREMAX', 'LLANTA', 'INYECCION', 'LLANTA FIREMAX 185/70R14 CITY TOUR 88H', 0),
(3904, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 185/70R14 CITY TOUR 88H', 0),
(3905, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 255/60R18 HT VIGOROUS', 0),
(3906, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 245/60R18 HT VIGOROUS 105T', 0),
(3907, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 215/70R15 AT POWERLANDER', 0),
(3908, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA POWERTRAC 215/75R16 ST VANTOUR 8PR', 0),
(3909, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 225/40R18 SF888 92W', 0),
(3910, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 235/60R17 HT CROSSCONTACT', 0),
(3911, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 225/60R17 HT HT782 99H', 0),
(3912, 'ARMOUR', 'LLANTA', 'INYECCION', 'LLANTA ARMOUR 12.5/80-18 14PR L2D', 0),
(3913, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 295/80R22.5 POS. 18PR', 0),
(3914, 'COMPASAL', 'LLANTA', 'INYECCION', 'LLANTA COMPASAL 245/70R19.5 DEL CPS21 16PR', 0),
(3915, 'COMPASAL', 'LLANTA', 'INYECCION', 'LLANTA COMPASAL 245/70R19.5 POS CPD81 14PR', 0),
(3916, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 215/70R16 AT GRABBER', 0),
(3917, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 245/70R16 AT ALL TERRAIN', 0),
(3918, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 235/75R15 AT ALL TERRAIN', 0),
(3919, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 235/75R15 MT ALL TERRAIN', 0),
(3920, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 195/65R15 91H ULTRA TOUR I', 0),
(3921, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 195/60R15 88H ULTRA TOUR I', 0),
(3922, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 225/45R17 94W ULTRA SPORT I', 0),
(3923, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 215/65R16 98H ULTRA TOUR I', 0),
(3924, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 205/70R15 HT ULTRA TOUR', 0),
(3925, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 205/60R16 ULTRA TOUR 92H', 0),
(3926, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 265/70R16 AT ALL TERRAIN', 0),
(3927, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 235/60R16 HT ULTRA TOUR 100H', 0),
(3928, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 225/60R17 HT ULTRA TOUR 99H', 0),
(3929, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 195/55R15 85V ULTRA TOUR I', 0),
(3930, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 195/50R15 82H ULTRA TOUR I', 0),
(3931, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 185/65R15 88H ULTRA TOUR I', 0),
(3932, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 185/65R14 86H ULTRA TOUR I', 0),
(3933, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 185/60R15 88H ULTRA TOUR I', 0),
(3934, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 185/60R14 82H ULTRA TOUR II', 0),
(3935, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 175/70R14 84T ULTRA TOUR I', 0),
(3936, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 175/70R13 ULTRA TOUR 82T', 0),
(3937, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 175/65R14 82T ULTRA TOUR I', 0),
(3938, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA EL DORADO 165/65R13 ULTRA TOUR 77T', 0),
(3939, 'BFGOODRICH', 'LLANTA', 'INYECCION', 'LLANTA BFGOODRICH 265/75R16 AT ALL TERRAIN', 0),
(3940, 'BF GOODRICH', 'LLANTA', 'INYECCION', 'LLANTA BFGOODRICH 235/75R15 AT ALL TERRAIN', 0),
(3941, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 255/60R18 AT SCORPION', 0),
(3942, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 205/50R15 TR918 89H', 0),
(3966, 'GINELL', 'LLANTA', 'INYECCION', 'LLANTA GINELL 305/70R16 LT8PR 118/115Q GN3000MT', 0),
(3967, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 31X10.50R15 MT MUD TERRAIN', 0),
(3987, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 295/80R22.5 DEL G665', 0),
(3988, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 235/40ZR18XL 95W XL R302', 0),
(3989, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 235/55R18 104V XL007 R601', 0),
(3990, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 205/60R14 88H R203', 0),
(3991, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 205R16C R101 8PR', 0),
(3992, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 205/60R16 R302 91H', 0),
(3993, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 205/50R16 R702 87W', 0),
(3994, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 185/60R15 R203', 0),
(3995, 'BKT', 'LLANTA', 'INYECCION', 'LLANTA BKT 7.50-16 POS 16PR COLT', 0),
(3996, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 245/75R16 MT PRACTICALMAX', 0),
(3997, 'BKT', 'LLANTA', 'INYECCION', 'LLANTA BKT 7.50-16 DEL ARROW 16PR', 0),
(3998, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 215/45R17 SPORTMAX 92V', 0),
(3999, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 265/70R16 AT DURABLEMAX', 0),
(4000, 'KAPSEN', 'LLANTA', 'INYECCION', 'LLANTA KAPSEN 225/70R15 RS01 112/110R', 0),
(4001, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.50-16 POS CHASQUI 12PR', 0),
(4002, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.00-16 POS. CHASQUI 12PR', 0),
(4003, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.00-16 DEL CAMINERA 10PR', 0),
(4004, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 6.50-14 POS CHASQUI 8PR', 0),
(4010, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 215/75R14 AT HD818', 0),
(4011, 'DRC', 'LLANTA', 'INYECCION', 'LLANTA DRC 6.50 -14 12PR 52D', 0),
(4012, 'HENGDA', 'LLANTA', 'INYECCION', 'LLANTA HENGDA 4.00-8 MIX 8PR HDM067', 0),
(4013, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 215/75R15 AT VIGOROUS', 0),
(4014, 'GINELL', 'LLANTA', 'INYECCION', 'LLANTA GINELL 31X10.50R15 MT GN3000', 0),
(4015, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 185/60R15 99V A2000', 0),
(4016, 'SPORTIVA', 'LLANTA', 'INYECCION', 'LLANTA SPORTIVA 215/75R14 AT RADIAL', 0),
(4017, 'AUTOGRIP', 'LLANTA', 'INYECCION', 'LLANTA AUTOGRIP 165/60R14 T5H 808', 0),
(4018, 'ARMOUR', 'LLANTA', 'INYECCION', 'LLANTA ARMOUR 6.50-14 DEL SET 8PR T2', 0),
(4019, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 195/65R15 A606 4PR', 0),
(4020, 'DOUBLE STAR', 'LLANTA', 'INYECCION', 'LLANTA DOUBLE STAR 5.00R12 DS809 83/81R 8PR', 0),
(4021, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 315/80R22.5 DEL HSR2 20PR', 0),
(4022, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 295/80R22.5 DEL.CONTICITYPLUS', 0),
(4041, 'KINGRUN', 'LLANTA', 'INYECCION', 'LLANTA KINGRUN 225/45R17 K3000 PHANTOM', 0),
(4042, 'SOLIDEAL', 'LLANTA', 'INYECCION', 'LLANTA SOLIDEAL 19.5-24 12PR R-4 SLK', 0),
(4043, 'INTERSTATE', 'LLANTA', 'INYECCION', 'LLANTA INTERSTATE 175/65R14 TOURING 84H', 0),
(4044, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 215/65R16 98T CS1 LTR', 0),
(4045, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 245/65R17 AT DISCOVERER', 0),
(4046, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 235/65R17 AT DISCOVER', 0),
(4047, 'MARSHAL', 'LLANTA', 'INYECCION', 'LLANTA MARSHAL 225/65R17 SOLUS 102H', 0),
(4048, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 215/60R16 PXC10 95V TL', 0),
(4049, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 185/65R15 88T CS1', 0),
(4050, 'DURAMAS', 'LLANTA', 'INYECCION', 'LLANTA DURAMAS 185/60R14 82H DT100 PCR', 0),
(4071, 'BF GOODRICH', 'LLANTA', 'INYECCION', 'LLANTA BF GOODRICH 275/65R17 AT ALL-TERRAIN', 0),
(4072, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MICHELIN 265/65R17 AT FORCE', 0),
(4085, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 245/75R16 MT MR172', 0),
(4086, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 235/75R15 AT MR172', 0),
(4087, 'AUTOGRIP', 'LLANTA', 'INYECCION', 'LLANTA AUTOGRIP 185/65R14 GRIP1000 86H', 0),
(4088, 'VIKRANT', 'LLANTA', 'INYECCION', 'LLANTA VIKRANT 8.25-16 POS LUG SET 18PR', 0),
(4089, 'VIKRANT', 'LLANTA', 'INYECCION', 'LLANTA VIKRANT 8.25-16 DEL TRACK KING SET 18PR', 0),
(4090, 'VIKRANT', 'LLANTA', 'INYECCION', 'LLANTA VIKRANT 7.50-16 DEL 16PR TRACK KING', 0),
(4091, 'SPORTRAK', 'LLANTA', 'INYECCION', 'LLANTA SPORTRAK 195R15 8PR TL', 0),
(4111, 'ANTARES', 'LLANTA', 'INYECCION', 'LLANTA ANTARES 205/60R15 INGENSA 91H', 0),
(4112, 'ANTARES', 'LLANTA', 'INYECCION', 'LLANTA ANTARES 195R15 LT 8PR NT 3000', 0),
(4114, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 17.5-25 16PR E-3 L-3 TL612', 0),
(4115, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 10.00-20 DEL CENTAURO 18PR', 0),
(4116, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 235/65R16 RS01 DURABLEMAX', 0),
(4117, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO 175/70R14 GREEN LING 84T', 0),
(4118, 'NANKANG', 'LLANTA', 'INYECCION', 'LLANTA NANKANG 225/45ZR18 95W XL NS-2', 0),
(4119, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 175/65R14', 0),
(4120, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 205/60R16 PXC10 92V', 0),
(4121, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 195/65R15 CS10 91T', 0),
(4122, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 225/50R17 TOURING 94V CS3 PCF', 0),
(4123, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 225/75R16 AT DISCOVERER 104T', 0),
(4124, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 185/70R13 CS10 85T', 0),
(4125, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/70R16 AT DYNAPRO', 0),
(4126, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 265/65R17 AT DYNAPRO', 0),
(4127, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/60R18 AT DYNAPRO', 0),
(4128, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 175/70R13 OPTIMO ME02 82H', 0),
(4129, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK165/65R13 K715 77T', 0),
(4130, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 205/60R15 RS909 91V', 0),
(4131, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 185/70R14 XD304 88H', 0),
(4132, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MICHELIN 2.75-18 CITY PRO TT 48S', 0),
(4133, 'DURO', 'LLANTA', 'INYECCION', 'LLANTA DURO 18-460 POS 335 HF XL250 CG', 0),
(4134, 'ITP', 'LLANTA', 'INYECCION', 'LLANTA ITP 25X10.00R12 NHS 80D BAJACROSS 8PR X/D USA', 0),
(4135, 'ITP', 'LLANTA', 'INYECCION', 'LLANTA MUD LITE 26X11.00R12 NHS 80F XTR 6PR USA', 0),
(4136, 'ITP', 'LLANTA', 'INYECCION', 'LLANTA ITP 25X8-12 MUD LITE USA', 0),
(4137, 'ITP', 'LLANTA', 'INYECCION', 'LLANTA ITP BAJACROSS 26X9.00R12 NHS 79D X/D 8PR USA', 0),
(4138, 'DURO', 'LLANTA', 'INYECCION', 'LLANTA DURO EXCELERATOR 18-100/100 906 HF 62M', 0),
(4139, 'DURO', 'LLANTA', 'INYECCION', 'LLANTA DURO17-460 335 HF XL250 CG', 0),
(4140, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MICHELIN 120/80-18 625 T63 TT', 0),
(4141, 'MICHEIIN', 'LLANTA', 'INYECCION', 'LLANTA MICHELIN 110/80-18 58S T63 TT M/C', 0),
(4142, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MICHELIN 160/60ZR17 PILOT STREET 69W', 0),
(4143, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 12.00-20 POS EXTRA 18PR TD-440', 0),
(4144, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 165R13 GT200 RADIAL', 0),
(4159, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.50-16 DEL CAMINERA 12PR', 0),
(4160, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO 195/70R15 ECOLINE 8PR', 0),
(4161, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO195/60R14 SPORT 88H', 0),
(4162, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 185/70R13 TE301 86T', 0),
(4163, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 265/70R17 AT PAYAK', 0),
(4164, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 265/70R16 AT PAYAK', 0),
(4165, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 215/70R16 HT PAYAK', 0),
(4166, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 205/45R17 88W XL R702', 0),
(4167, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 205/50ZR16 R202 75T', 0),
(4168, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 155/70R13 R202 75T TL', 0),
(4169, 'GALAXY', 'LLANTA', 'INYECCION', 'LLANTA GALAXY BEEFY BABY 10-16.5 10PR R4 OTR UND', 0),
(4170, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 175/65R14 SP10 82T', 0),
(4173, 'NANKANG', 'LLANTA', 'INYECCION', 'LLANTA NANKANG 205/50R16 NS-2 87V', 0),
(4174, 'NANKANG', 'LLANTA', 'INYECCION', 'LLANTA NANKANG 225/45ZR18 95W NS-2 XLL', 0),
(4175, 'NANKANG', 'LLANTA', 'INYECCION', 'LLANTA NANKANG 215/40R17 NS-2 87V', 0),
(4176, 'NANKANG', 'LLANTA', 'INYECCION', 'LLANTA NANKANG 205/60R14 NS-2 92H', 0),
(4177, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 12.00R20 TRAC OR517 20PR MINERA', 0),
(4178, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 195R15 SUPER2000 106/104R 8PR TL', 0),
(4179, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 195/70R15 SF05 8PR', 0),
(4180, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 205/70R15 HT HF201', 0),
(4181, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 205/60R15 HF201 91V', 0),
(4182, 'BARUM', 'LLANTA', 'INYECCION', 'LLANTA BARUM 225/70R16 HT BRAVURIS 103H', 0),
(4183, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 185/70R13 HF201 86H', 0),
(4200, 'SPORTRAK', 'LLANTA', 'INYECCION', 'LLANTA SPORTRAK 12.00R20 POS. SP909 P.OSO', 0),
(4201, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 6.50-13 SUPERCUCHO 8 TAXI', 0),
(4204, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 225/70R16 AT GRABBER', 0),
(4205, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 7.00-15 DEL POWER JET 10PR', 0),
(4206, 'MARSHALL', 'LLANTA', 'INYECCION', 'LLANTA MARSHALL 205/50R15 MUI1 86V', 0),
(4207, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/65R17 AT DUELER D693', 0),
(4210, 'SPORTRAK', 'LLANTA', 'INYECCION', 'LLANTA SPORTRAK 12.00R24 MIX BS28 20PR', 0),
(4211, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 285/65R17 AT DUELER', 0),
(4221, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 215/65R16 ST DURABLEMAX', 0),
(4222, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 195/70R15 R301 8PR', 0),
(4223, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.00-15 10PR PANTANERA', 0),
(4230, 'COMPASAL', 'LLANTA', 'INYECCION', 'LLANTA COMPASAL 285/70R17 MT VERSANT', 0),
(4231, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 205/55R15 HF805 88V', 0),
(4232, 'ARMOUR', 'LLANTA', 'INYECCION', 'LLANTA ARMOUR 14.00-24 16PR L2 S/C', 0),
(4233, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 185/60R15 SP2030 84H', 0),
(4234, 'ADVANCE', 'LLANTA', 'INYECCION', 'LLANTA ADVANCE14-17.514PR L-4A TL', 0),
(4236, 'GOALSTAR', 'LLANTA', 'INYECCION', 'LLANTA GOALSTAR 235/55R19 BLAZER 105V', 0),
(4237, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 265/70R16 AT DURELOVE', 0),
(4238, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 155/65R13 ROLIT6 73T', 0),
(4239, 'SUNWIDE', 'LLANTA', 'INYECCION', 'LLANTA SUNWIDE 175/70R13 ROLIT6 85T', 0),
(4240, 'MARSHALL', 'LLANTA', 'INYECCION', 'LLANTA MARSHALL 215/65R16 HT GRUGEN', 0),
(4241, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 245/70R16 AT OPEN COUNTRY', 0),
(4242, 'MARSHALL', 'LLANTA', 'INYECCION', 'LLANTA MARSHALL 245/65R17 HT KL21 107S', 0),
(4243, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 235/70R16 AT DISCOVERER', 0),
(4244, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 265/70R17 AT OPEN COUNTRY', 0),
(4245, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 265/70R16 AT DISCOVERER', 0),
(4246, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 235/75R15 AT VIGOROUS', 0),
(4247, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 235/75R15 AT AT782', 0),
(4248, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 215/45R17 SF886 91Y', 0),
(4249, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 155/65R13 HF201 73T', 0),
(4250, 'GOODRIDE', 'LLANTA', 'INYECCION', 'LLANTA GOODRIDE 215/45R17 SV308 91W', 0),
(4251, 'GOODRIDE', 'LLANTA', 'INYECCION', 'LLANTA GOODRIDE 245/75R16 AT RADIAL', 0),
(4252, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MICHELIN 215/65R16 AT FORCE', 0),
(4253, 'SPORTRAK', 'LLANTA', 'INYECCION', 'LLANTA SPORTRAK 12.00R20 POS. BYD865 MINERA', 0),
(4254, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 7.50-16 POS AMERI DCL 14PR', 0),
(4255, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 255/55R18 AT GRABBER', 0),
(4256, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 225/60R17 HT GRABBER 99H', 0),
(4257, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 235/65R17 AT GRABBER', 0),
(4258, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 265/70R16 AT SU 800', 0),
(4259, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 7.50R16 DEL', 0),
(4260, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 215/75R14 AT SPORT', 0),
(4261, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 225/75R16 MT WRANGLER', 0),
(4263, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 255/65R16 AT GRABBER AT2', 0),
(4264, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 285/70R17 MT SCORPION', 0),
(4280, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 205/55R15 DZ102 88V', 0),
(4281, 'NANKANG', 'LLANTA', 'INYECCION', 'LLANTA NANKANG 235/45R17 NS2 94V TL', 0),
(4282, 'ADVANCE', 'LLANTA', 'INYECCION', 'LLANTA ADVANCE 12-16.5 12PR NHS L-2B TL', 0),
(4283, 'OTANI', 'LLANTA', 'INYECCION', 'LLANTA OTANI 6.50-14 POS S78 10PR', 0),
(4284, 'BOXER', 'LLANTA', 'INYECCION', 'LLANTA BOXER 23.5-25 20PR E-3/L-3 TCF', 0),
(4285, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 20.5-25 20PR TL612 E-3/L-3 TCF', 0),
(4286, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 245/75R16 AT PRACTICALMAX', 0),
(4287, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 195/60R15 RS23 88H', 0),
(4288, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 185/70R14 H202 88H', 0),
(4297, 'TECHKING', 'LLANTA', 'INYECCION', 'LLANTA TECHKING 11R22.5 DEL TKST II 16PR', 0),
(4298, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 7.50-16 PAN CORDILLERA 10PR', 0),
(4299, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 7.50-16 POS TD-440 14PR', 0),
(4300, 'LINGLONG', 'LLANTA', 'INYECCION', 'LLANTA LINGLONG 7.50R16 DEL LLF26 14PR', 0),
(4301, 'DURATREAD', 'LLANTA', 'INYECCION', 'LLANTA DURATREAD 7.50-16 POS POWER LUG 16PR', 0),
(4302, 'TEXXAN', 'LLANTA', 'INYECCION', 'LLANTA TEXXAN 7.00-16 POS LV-712 XL 14PR', 0),
(4303, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 7.50-15 POS PIONERA 14PR', 0),
(4304, 'TECHKING', 'LLANTA', 'INYECCION', 'LLANTA TECHKING 7.50R16 POS TKAM 14PR', 0),
(4305, 'ALTURA', 'LLANTA', 'INYECCION', 'LLANTA STAMINA ALTURA POS 8.25-16 16PR', 0),
(4306, 'KAIZEN', 'LLANTA', 'INYECCION', 'LLANTA KAIZEN 7.50-16 DEL KZ-R001 16PR', 0),
(4307, 'GOODTYRE', 'LLANTA', 'INYECCION', 'LLANTA GOODTYRE 7.50R16 POS YB228 14PR', 0),
(4308, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 8.25R16 POS CB 981 16PR', 0),
(4309, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.50-15 DEL HI MILER CT176', 0),
(4310, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.00-16 POS CHASQUI 115/110J', 0),
(4311, 'KAIZEN', 'LLANTA', 'INYECCION', 'LLANTA KAIZEN 7.50-16 POS KZ-L002 16PR', 0),
(4312, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 215/80R16 AT DESTINATION', 0),
(4313, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 205R16 AT DESTINATION', 0),
(4314, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 205/50R16 FIREHAWK 87V', 0),
(4315, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 195/55R15 85H ALTIMAX HP', 0),
(4316, 'ANTARES', 'LLANTA', 'INYECCION', 'LLANTA ANTARES 225/75R16 AT 118/111S SMT A7', 0),
(4317, 'TRAILCUTER', 'LLANTA', 'INYECCION', 'LLANTA TRAILCUTER 245/75R16 MT RTX', 0),
(4318, 'CATCHGRE', 'LLANTA', 'INYECCION', 'LLANTA CATCHGRE 175/65R14 WINDFORSE 82H', 0),
(4319, 'MARSHALL', 'LLANTA', 'INYECCION', 'LLANTA MARSHALL 255/70R16 AT ROAD VENTURE', 0),
(4320, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 145R12 H200 LOAD RANGE', 0),
(4321, 'LAUFEN', 'LLANTA', 'INYECCION', 'LLANTA LAUFEN 185/65R14 GFITAS 86H', 0),
(4322, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 175/70R14 OP350 84T', 0),
(4323, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 175/65R13 OP350 82T', 0),
(4324, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 215/65R16 ST MK700', 0),
(4325, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 215/45R17 INGENS A1 91W', 0),
(4326, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 165/60R14 75T OPTIMO K715', 0),
(4327, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 195/60R16 89H B250', 0),
(4328, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 205/55R16 POTENZA 90H', 0),
(4329, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 235/70R16 AT DUELER', 0),
(4330, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 205R16 AT DUELER D694', 0),
(4331, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 235/65R17 HT DUELER 108V', 0),
(4332, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO 265/70R16 AT MOUNTAIN', 0),
(4333, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 225/65R17 AT DUELER', 0),
(4334, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/75R16 MT DUELER D673', 0),
(4335, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 215/75R15 AT DUELER D694', 0),
(4336, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 7.50R16 DEL. RIB 230', 0),
(4337, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 185/65R14 GIII', 0),
(4338, 'MASSTEK', 'LLANTA', 'INYECCION', 'LLANTA MASSTEK 185/65R14 TEXTIL 86S', 0),
(4355, 'DURO', 'LLANTA', 'INYECCION', 'LLANTA DURO 100/90-19 57M', 0),
(4356, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 205/50R16 A-ONE 88H', 0),
(4357, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 165/65R14 HF201 79T', 0),
(4358, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 165/65R14 SF688 78T', 0),
(4359, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 175/70R13 A2000 82T', 0),
(4360, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 185/70R13 D104 86T', 0),
(4375, 'MICHELIN', 'LLANTA', 'INYECCION', 'LLANTA MICHELIN 140/70-17 66S PILOT STREET TL/TT', 0),
(4412, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 235/75R15 AT DISCOVERER', 0),
(4413, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 195/60R15 CS10 88T', 0),
(4414, 'MARSHALL', 'LLANTA', 'INYECCION', 'LLANTA MARSHALL 205/70R14 KR21 95T', 0),
(4415, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 195/70R14 CS10 91T', 0),
(4416, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 195R14 106S HO8LTR', 0),
(4417, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 175/70R14 CS1 84T', 0),
(4418, 'MARSHALL', 'LLANTA', 'INYECCION', 'LLANTA MARSHALL185/65R14 MH11 86H', 0),
(4419, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 185/70R14 CS10 88T', 0),
(4420, 'COOPER', 'LLANTA', 'INYECCION', 'LLANTA COOPER 175/70R13 CS10 82T', 0),
(4421, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 185/70R14 RE740 88T', 0),
(4422, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 185R14 RS01 102/100R 8PR DURABLEMAX', 0),
(4424, 'TEXXAN', 'LLANTA', 'INYECCION', 'LLANTA TEXXAN 4.00-8 MIX 8PR AUTO STAR', 0),
(4425, 'TEXXAN', 'LLANTA', 'INYECCION', 'LLANTA TEXXAN 4.00-8 POS 8PR AUTO LUG', 0),
(4426, 'BEARWAY', 'LLANTA', 'INYECCION', 'LLANTA BEARWAY LT 6.00R13 M636 8PR', 0),
(4434, 'DURATREAD', 'LLANTA', 'INYECCION', 'LLANTA DURATREAD 8.25-16 POS 53-D 18PR', 0),
(4435, 'DURATREAD', 'LLANTA', 'INYECCION', 'LLANTA DURATREAD 8.25-16 DEL 54-B 18PR', 0),
(4436, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 185/65R15 POWER CONTAC 88H', 0),
(4437, 'BARUM', 'LLANTA', 'INYECCION', 'LLANTA BARUM 185/70R14 BRILLANTIS 88T', 0),
(4438, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 185/70R13 P400 85T', 0),
(4445, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 245/75R16 MT DYNAPRO', 0),
(4446, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 265/70R16 AT DYNAPRO', 0),
(4447, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/60R16 HT DYNAPRO 100H', 0),
(4448, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 225/75R16 HT OPTIMO 102/104H', 0),
(4449, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 225/60R17 HT DYNAPRO 99H', 0),
(4450, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 205/55R16 KINERGY 91H', 0),
(4451, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 205/50R15 VENTUS 86W', 0),
(4452, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 175/70R13 TR928 82H', 0),
(4453, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 195/65R15 K425 88H', 0),
(4454, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 155/70R12 H429 73T TL', 0),
(4455, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 265/70R16 AT ADVENTURE', 0),
(4456, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 245/75R16 AT ARMORTRAC', 0),
(4457, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 225/65R16 HT DURABLEMAX', 0),
(4458, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 155/70R12 MR162 73T TL', 0),
(4459, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 205/50R16 MR162 87V', 0),
(4460, 'SPORTRAK', 'LLANTA', 'INYECCION', 'LLANTA SPORTRAK 245/70R19.5 POS SP305 14PR TL', 0),
(4461, 'SPORTRAK', 'LLANTA', 'INYECCION', 'LLANTA SPORTRAK 245/70R19.5 DEL SP301 16PR TL', 0),
(4462, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 265/70R16 AT OPEN COUNTRY', 0),
(4466, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 195R15 RS01 106/104R 8PR TL', 0),
(4467, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 195/60R15 A2000 88V', 0),
(4468, 'MINNELL', 'LLANTA', 'INYECCION', 'LLANTA MINNELL 195/65R15 P07 91H', 0),
(4469, 'COMPASAL', 'LLANTA', 'INYECCION', 'LLANTA COMPASAL 215/75R14 ST TRAILER', 0),
(4470, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 155/70R12 TOURING 73T TL', 0),
(4471, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 8.25R16 POS TR690 128/124K 16PR', 0),
(4472, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 8.25R16 MIX TR668 128/124M 16PR', 0),
(4473, 'WANDA', 'LLANTA', 'INYECCION', 'LLANTA WANDA 5.00R12 WR081 83/81Q 10PR', 0),
(4474, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 195/65R15 TR928 91H', 0),
(4475, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 205/60R16 TR918 96H', 0),
(4476, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 20.5-25 L3 E3 20PR LOAD TL612', 0),
(4478, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 245/75R16 AT GRABBER', 0),
(4479, 'FARROAD', 'LLANTA', 'INYECCION', 'LLANTA FARROAD 265/70R16 AT FRD86', 0),
(4480, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 215/70R15 RS925 8PR', 0),
(4481, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 185/65R14 RS928 86H', 0),
(4482, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 215/45R17 RS909 91Y', 0),
(4483, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 185/65R15 MAXIMUS 86H', 0),
(4484, 'WINDA', 'LLANTA', 'INYECCION', 'LLANTA WINDA 155/65R13 WP15 73T', 0),
(4485, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 12.00-20 DEL CAMINERA 18PR GT150', 0),
(4486, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 185/70R14 SU830 88T', 0),
(4487, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 155R13 SU810 90/88S', 0),
(4488, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 195/70R15 SU810 8PR', 0),
(4489, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 235/75R17.5 DEL MG-022 143/141J 16PR', 0),
(4490, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 31X10.50R15 AT MR172', 0),
(4491, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 195/70R14 MR162 91H', 0),
(4492, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 185/70R13 H202 86T', 0),
(4493, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 195R14 RS01 106/104Q TL', 0),
(4494, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 185/65R14 H202 86H CONFORT', 0),
(4495, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 165/65R13 H202 77T', 0),
(4496, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 205/60R16 H202 92V CONFORT', 0),
(4497, 'HABILEAD', 'LLANTA', 'INYECCION', 'LLANTA HABILEAD 175/70R14 H202 84H', 0),
(4554, 'PRAXXIS', 'LLANTA', 'INYECCION', 'LLANTA PRAXIS 4.00-8 MIX 8PR NYLON GRIP', 0),
(4562, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 235/60R18 HT SPORT 107V', 0),
(4563, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 195/65R15 GIII 91H', 0),
(4564, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 185/60R15 ER300 88H', 0),
(4565, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/65R17 MT DUELER', 0),
(4566, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 205/60R15 GIII 91V', 0),
(4567, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 235/55R19 DUELER HL 105H', 0),
(4568, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 235/60R16 HT D687 100H', 0),
(4569, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 225/60R17 HT DUELER', 0),
(4570, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 275/40R20 SPORTAS 105W', 0),
(4571, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 5.00R12 83/82P 8PR', 0),
(4572, 'SOLIDEAL', 'LLANTA', 'INYECCION', 'LLANTA SOLIDEAL 20.5-25 L3 E3 .20PR LOAD MASTER', 0),
(4573, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 235/75R17.5 DEL RS615 132/129M 16PR', 0),
(4574, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 195/60R16 TURANZA 89H', 0),
(4575, 'BCT', 'LLANTA', 'INYECCION', 'LLANTA BCT 225/40R18 RT655 102H', 0),
(4576, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 215/70R16 AT SU830', 0),
(4577, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 185/65R15 NAKARA 88H R201', 0),
(4578, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 8.25-16 POS L-602 18PR', 0),
(4579, 'BARUM', 'LLANTA', 'INYECCION', 'LLANTA BARUM 165/65R13 BRILLANTIS 77T', 0),
(4580, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 12R22.5 TRA RS617 152/149M 18PR', 0),
(4581, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 205/55R16 PROXES 91W', 0),
(4582, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 225/75R15 SU800 102h', 0),
(4583, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 215/70R15 MK700 8PR', 0),
(4584, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 185/65R15 SU830 88H', 0),
(4585, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 265/75R16 MT MUD TRAC', 0),
(4586, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/70R16 HT DUELER D694', 0),
(4587, 'ALLIANCE', 'LLANTA', 'INYECCION', 'LLANTA ALLIANCE 19.5-24 12PR L3 NYLON', 0),
(4588, 'GOODTYRE', 'LLANTA', 'INYECCION', 'LLANTA GOODTYRE 23.5-25 24PR L3 PERS', 0),
(4589, 'SAMSON', 'LLANTA', 'INYECCION', 'LLANTA SAMSON 12.5/80-18 14PR L2D SKID', 0),
(4590, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 165/65R13 MR162 77T', 0),
(4591, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 205/50R15 ECSTA KU36 86W', 0),
(4592, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO 195/65R15 ECOSABER 91H', 0),
(4593, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 195/70R14 HD616 91H', 0),
(4594, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 185/70R14 HD667 88T', 0),
(4595, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 175/70R13 ASSURANCE 82T', 0),
(4596, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 265/75R16 MT GRABBER', 0),
(4597, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 275/45R20 HD921 110W', 0),
(4598, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 225/65R17 HT HD668 102H', 0),
(4599, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 245/75R16 MT MUD TERRAIN', 0),
(4600, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 185R14 LINAMAR 102/100P 8PR', 0),
(4601, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 235/60R16 HT MR172 100H', 0),
(4602, 'ANTARES', 'LLANTA', 'INYECCION', 'LLANTA ANTARES 205/60R16 SU830 6PR', 0),
(4603, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 205/65R15 TR928 94H', 0),
(4604, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 205/55R15 VINCENTE 88V', 0),
(4605, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 205/60R15 NAKARA 91V', 0),
(4606, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 205R14 KACHOR 101 109/107P', 0),
(4607, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 205/50R17 HD921 93W', 0),
(4608, 'COMFORSER', 'LLANTA', 'INYECCION', 'LLANTA COMFORSER 205/50R15 CF5000 86V', 0),
(4609, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 165/60R14 ALTIMAX 75H', 0),
(4610, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 185/65R14 A606 88H', 0),
(4611, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 185/60R14 A606 82H', 0),
(4612, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 175/70R14 A606 84H', 0),
(4613, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 175/65R14 A606 82H', 0),
(4614, 'SPORTRAK', 'LLANTA', 'INYECCION', 'LLANTA SPORTRAK 12.00R24 TRA SP981 20PR', 0),
(4615, 'ANNAITE', 'LLANTA', 'INYECCION', 'LLANTA ANNAITE 12.00R20 TRA A309 18PR', 0),
(4616, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 12.00R20 TRA HH317 18PR', 0),
(4617, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 12.00R20 TRA XD-968 18PR', 0),
(4618, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 245/75R16 AT DUELER D694', 0),
(4619, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/75R16 AT DUELER D694', 0),
(4620, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 235/85R16 AT DUELER D696', 0),
(4621, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 265/75R16 MT DYNAPRO', 0),
(4622, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 265/75R16 AT MR172', 0),
(4623, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 265/75R16 AT PAYAK', 0),
(4624, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/70R16 AT DUELER D694', 0),
(4625, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 245/70R16 AT DUELER', 0),
(4626, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 225/75R16 AT SU800 118/116S', 0),
(4627, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 225/70R16 AT SU830 107S', 0),
(4628, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 235/60R16 AT GRANDTREK', 0),
(4629, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 7.00-15 DEL XD302 114/112K', 0),
(4630, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 235/60R16 AT GRABBER', 0),
(4631, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 265/70R17 AT VIGOROUS', 0),
(4632, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 265/70R17 AT MR172', 0),
(4633, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 265/65R17 AT DUELER D694', 0),
(4634, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 245/70R17 AT DYNAPRO', 0),
(4635, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 265/70R17 AT DYNAPRO', 0),
(4636, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 235/85R16 AT MOHAVE', 0),
(4637, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 265/65R17 AT GRANDTREK', 0),
(4638, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 245/65R17 AT DUELER 107T', 0),
(4639, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/65R17 AT DYNAPRO', 0),
(4640, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 225/60R17 HT GRABBER 95H', 0),
(4641, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 245/70R16 AT SU800', 0),
(4642, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 215/50R17 SU308 95W', 0),
(4643, 'GOLDWAY', 'LLANTA', 'INYECCION', 'LLANTA GOLDWAY 215/45R17 G2002 91W', 0),
(4644, 'ANTARES', 'LLANTA', 'INYECCION', 'LLANTA ANTARES 215/45R17 INGENS A1 91W', 0),
(4645, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 215/45R17 PROXES 91W', 0),
(4646, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 225/45R17 HF805 94W (DEPORTIVO)', 0),
(4647, 'LING LONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 205/45R17 GREEN MAX 88W', 0),
(4648, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 205/50R16 VENTUS V2 87H', 0),
(4649, 'SUNFULL', 'LLANTA', 'INYECCION', 'LLANTA SUNFULL 205/50R16 SF688 87V', 0),
(4650, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 205/50R16 HD921 91V', 0),
(4651, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 205/55R16 TR928 91H', 0),
(4652, 'ACCELERA', 'LLANTA', 'INYECCION', 'LLANTA ACCELERA 215/55R16 ALPHA 97W', 0),
(4653, 'FEDERAL', 'LLANTA', 'INYECCION', 'LLANTA FEDERAL 225/60R16 FORMOZA 98V', 0),
(4654, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 205/60R13 ZIEX Z912 86H', 0),
(4655, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 195/50R15 FIRE HAWK 82V', 0),
(4656, 'MARSHALL', 'LLANTA', 'INYECCION', 'LLANTA MARSHALL 205/60R13 SOLUS KR21 86T', 0),
(4657, 'FUZION', 'LLANTA', 'INYECCION', 'LLANTA FUZION 165/70R13 TOURING 79T', 0),
(4658, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 175/70R13 OP350 82T', 0),
(4659, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 165/70R13 SOLUS KR21 78T', 0),
(4660, 'JOYROAD', 'LLANTA', 'INYECCION', 'LLANTA JOYROAD 175/70R13 TOUR RX1 82T', 0),
(4661, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 175/70R13 SN826 82T', 0),
(4662, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 165/70R13 MR162 79T', 0),
(4663, 'BARUM', 'LLANTA', 'INYECCION', 'LLANTA BARUM 175/70R13 BRILLANTIS 82T', 0),
(4664, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 155/65R13 SOLUS KM15 73T', 0),
(4665, 'WINDA', 'LLANTA', 'INYECCION', 'LLANTA WINDA 165/65R13 WP15 77T', 0),
(4666, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 155/65R13 OPTIMO K715 73T', 0),
(4667, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 155/65R13 MR162 73T', 0),
(4668, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 215/75R15 AT WILDPEAK', 0),
(4669, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 195R15 RD613 106/104R 6PR TL', 0),
(4670, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 195/65R15 RS906 91H', 0),
(4671, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 205/70R15 ST MK700', 0),
(4672, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 205/70R15 HT DURAVIS D684', 0),
(4673, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 205/65R15 HF201 94V', 0),
(4674, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 195/65R15 SU830 91H', 0),
(4675, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 185/65R15 OPTIMO ME02 88H', 0),
(4676, 'ACCELERA', 'LLANTA', 'INYECCION', 'LLANTA ACCELERA 205/55R15 ALPHA 88V', 0),
(4677, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 195/60R15 SU 830 88H', 0),
(4678, 'FEDERAL', 'LLANTA', 'INYECCION', 'LLANTA FEDERAL 195/55R15 SUPER STEEL 85W', 0),
(4679, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 315/75R16 MT MUD TRAC', 0),
(4680, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 265/70R17 MT VIGOROUS', 0),
(4681, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 245/75R16 MT DUELER D694', 0),
(4682, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 31X10.50R15 MT LANDAIR', 0),
(4683, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 235/75R15 MT DUELER', 0),
(4684, 'FEDERAL', 'LLANTA', 'INYECCION', 'LLANTA FEDERAL 235/75R15 MT COURAGIA', 0),
(4685, 'COMFORSER', 'LLANTA', 'INYECCION', 'LLANTA COMFORSER 215/75R15 MT CF3000', 0),
(4686, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/65R17 HT DYNAPRO 104T', 0),
(4687, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 225/65R17 HT DUELER 102T', 0),
(4688, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 225/65R17 HT SOLUS KL21 102H', 0),
(4689, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 225/60R17 HT MAXIMUS 99V', 0),
(4690, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/60R17 HT DYNAPRO 102H', 0),
(4691, 'ODOKING', 'LLANTA', 'INYECCION', 'LLANTA ODOKING 12.00R20 TRA ST-869 20PR', 0),
(4692, 'KAPSEN', 'LLANTA', 'INYECCION', 'LLANTA KAPSEN 12.00R20 MIX HS-801 20PR', 0),
(4693, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 12.00R20 MIX OR-106 20PR', 0),
(4694, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 12.00-20 DEL XD-301 18PR', 0),
(4695, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 12R22.5 POS AM06 152/148K 16PR', 0),
(4696, 'AEOLUS', 'LLANTA', 'INYECCION', 'LLANTA AEOLUS 295/80R22.5 MIX HN-218 152/149L', 0),
(4697, 'DOUBLE COIN', 'LLANTA', 'INYECCION', 'LLANTA DOUBLE COIN 9.5R17.5 DEL RT500 143/141J 18PR', 0),
(4698, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 8.25-16 POS GL-839 POS', 0),
(4699, 'TEXXAN', 'LLANTA', 'INYECCION', 'LLANTA TEXXAN 8.25-16 POS LX-912 18PR', 0),
(4700, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 8.25-16 POS XD-102 16PR', 0),
(4701, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 8.25R16 MIX HH301 128/124M 16PR', 0),
(4702, 'LING LONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 8.25R16 DEL LLF26 128/126K 14PR', 0),
(4703, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 8.25-16 DEL CR-892 14PR', 0),
(4704, 'DOUBLE CAMEL', 'LLANTA', 'INYECCION', 'LLANTA DOUBLE CAMEL 8.25-16 DEL DC-501 16PR', 0),
(4705, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 8.25-16 DEL R-501 16PR', 0),
(4706, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 8.25-16 DEL R-707 16PR', 0),
(4707, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 235/75R17.5 POS RS604 132/129M 16PR', 0),
(4708, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 215/75R17.5 POS RS604 127/154M 16PR', 0),
(4709, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 31X10.50R15 AT DUELER', 0),
(4710, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 31X10.50R15 AT GRANDTREK', 0),
(4711, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 31X10.50R15 AT VIGOROUS', 0),
(4712, 'BCT', 'LLANTA', 'INYECCION', 'LLANTA BCT 245/75R16 AT RADIAL', 0),
(4713, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 245/75R16 AT DYNAPRO', 0),
(4714, 'CATCHFORSE', 'LLANTA', 'INYECCION', 'LLANTA CATCHFORSE 245/75R16 AT WINDFORSE', 0),
(4715, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 245/70R16 HT DYNAPRO', 0);
INSERT INTO `prodstock` (`IdProducto`, `ProductoMarca`, `ProductoCategoria`, `FormaFarmaceutica`, `Producto`, `Stock`) VALUES
(4716, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 205/75R16 HT DURAVIS', 0),
(4717, 'ANTARES', 'LLANTA', 'INYECCION', 'LLANTA ANTARES 225/65R16 HT NT3000', 0),
(4718, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 225/65R16 ST MK700', 0),
(4719, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 195/75R16 HT SPLT5', 0),
(4720, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 195/60R15 TURANZA 89H', 0),
(4721, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 205/50R16 HF201 87W', 0),
(4722, 'ACCELERA', 'LLANTA', 'INYECCION', 'LLANTA ACCELERA 205/50R16 ALPHA 91W', 0),
(4723, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 205/55R16 TURANZA 91V', 0),
(4724, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 205/50R16 FIRELLA 87V', 0),
(4725, 'MARSHALL', 'LLANTA', 'INYECCION', 'LLANTA MARSHALL 205/50R16 MATRAC 87W', 0),
(4726, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 205/55R16 HF201 91W', 0),
(4727, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 205/45R16 VENTUS 83H', 0),
(4728, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 185/70R13 SU830 86T', 0),
(4729, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 185/70R13 ASSURANCE 86T', 0),
(4730, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 185/70R13 VENTURA 86H', 0),
(4731, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 185/70R13 ALTIMAX 86T', 0),
(4732, 'FEDERAL', 'LLANTA', 'INYECCION', 'LLANTA FEDERAL 205/60R13 SUPER STEEL 87H', 0),
(4733, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 205/60R13 ALTIMAX 86H', 0),
(4734, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 155R12 SU830 88/86S', 0),
(4735, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 155R12 SPP5 88/86N', 0),
(4736, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 155R12 RS900 83/81Q', 0),
(4737, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 155R12 R200 88/86R', 0),
(4738, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 155/70R12 GT70 73T TL', 0),
(4739, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 7.50-16 POS CORDILLERA 118/116K 10PR', 0),
(4740, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 7.50-16 PAN SUPER ALL GRIP 116/112J 10PR', 0),
(4741, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 7.50-16 POS XD102 1108/115J 16PR', 0),
(4742, 'VIKRANT', 'LLANTA', 'INYECCION', 'LLANTA VIKRANT 7.50-16 POS STAR LUG 16PR', 0),
(4743, 'LING LONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 7.50R16 POS LLD09 122/118M 14PR', 0),
(4744, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 7.50-16 POS EXTRA TD 122/121G 10PR', 0),
(4745, 'DRC', 'LLANTA', 'INYECCION', 'LLANTA DRC 7.50-16 POS POWER LUG 127/124L 16PR', 0),
(4746, 'TECHKING', 'LLANTA', 'INYECCION', 'LLANTA TECHKING 7.50R16 MIX TKAM 122/118M 14PR', 0),
(4747, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 7.50-16 DEL XD301 124/120K 16PR', 0),
(4748, 'DURATREAD', 'LLANTA', 'INYECCION', 'LLANTA DURATREAD 7.50-16 DEL SUPER 127/124K 16PR', 0),
(4749, 'TEXXAN', 'LLANTA', 'INYECCION', 'LLANTA TEXXAN 7.00-16 POS LV-912 118/116J', 0),
(4750, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 7.00R16 DEL RB230 116/114L', 0),
(4751, 'ALTURA', 'LLANTA', 'INYECCION', 'LLANTA ALTURA 7.00-16 DEL 14PR 116/110I', 0),
(4752, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.00-16 DEL CAMINERA 111/106J', 0),
(4753, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 265/70R15 HT DYNAPRO', 0),
(4754, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 235/75R15 AT PAYAK', 0),
(4755, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 235/75R15 AT SU830', 0),
(4756, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 235/75R15 AT RS915', 0),
(4757, 'AUTOGRIP', 'LLANTA', 'INYECCION', 'LLANTA AUTOGRIP 235/65R16 ST ECOVAN', 0),
(4758, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 215/75R15 AT DESTINATION', 0),
(4759, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 225/70R15 AT GRANTREK', 0),
(4760, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 215/75R15 AT SU800', 0),
(4761, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 225/70R15 HT RS926', 0),
(4762, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 225/70R15 AT DYNAPRO', 0),
(4763, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 205/70R15 AT DYNAPRO', 0),
(4764, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 215/75R15 AT PUMA', 0),
(4765, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 215/70R15 SU830 98M', 0),
(4766, 'WINDA', 'LLANTA', 'INYECCION', 'LLANTA WINDA 195/70R15 WR01 8PR', 0),
(4767, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 205/65R15 ECSTA KM11 94V', 0),
(4768, 'ACCELERA', 'LLANTA', 'INYECCION', 'LLANTA ACCELERA 195/65R15 651 91V', 0),
(4769, 'WINRUN', 'LLANTA', 'INYECCION', 'LLANTA WINRUN 195/65R15 R380 91V', 0),
(4770, 'LAUFENN', 'LLANTA', 'INYECCION', 'LLANTA LAUFENN 195/65R15 LH41', 0),
(4771, 'CATCHGRE', 'LLANTA', 'INYECCION', 'LLANTA CATCHGRE 195/65R15 WINDFORSE 91V', 0),
(4772, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 205/60R15 RS906 91H', 0),
(4773, 'BOTO', 'LLANTA', 'INYECCION', 'LLANTA BOTO 195/60R15 GENESYS 88V', 0),
(4774, 'GOLDWAY', 'LLANTA', 'INYECCION', 'LLANTA GOLDWAY 185/65R15 G2001 88H', 0),
(4775, 'BOTO', 'LLANTA', 'INYECCION', 'LLANTA BOTO 185/65R15 GENESYS 88H', 0),
(4776, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 185/65R15 A606 88H', 0),
(4777, 'CATCHGRE', 'LLANTA', 'INYECCION', 'LLANTA CATCHGRE 195/60R15 WINDFORSE 88H', 0),
(4778, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 205/60R15 TR928 91H', 0),
(4779, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 195R14 SU810 106/104S 8PR', 0),
(4780, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 205/70R14 HF201 95H', 0),
(4781, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 185/70R14 MR162 88H', 0),
(4782, 'CATCHGRE', 'LLANTA', 'INYECCION', 'LLANTA CATCHGRE 195/60R14 WINDFORSE 86H', 0),
(4783, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 175/65R14 ECOPIA 82T', 0),
(4784, 'ROADSHINE', 'LLANTA', 'INYECCION', 'LLANTA ROADSHINE 195/60R14 RS906 86H', 0),
(4785, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 175/70R14 SOLUS KH17 84T', 0),
(4786, 'BOTO', 'LLANTA', 'INYECCION', 'LLANTA BOTO 195/60R14 GENESYS 86H', 0),
(4787, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 185/70R14 ALTIMAX 88T', 0),
(4788, 'LING LONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 165/70R14 L666 87R', 0),
(4789, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 175/70R14 SU830 84T', 0),
(4790, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 265/75R16 MT GRABBER ROJAS', 0),
(4791, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 265/70R16 AT GRABBER', 0),
(4792, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 245/75R16 AT GRANDTREK', 0),
(4793, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 265/70R16 MT GRANDTREK', 0),
(4794, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 245/75R16 AT VIGOROUS', 0),
(4795, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 225/70R16 HT DUELER', 0),
(4796, 'ACCELERA', 'LLANTA', 'INYECCION', 'LLANTA ACCELERA 225/55R16 ALPHA 99W', 0),
(4797, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 205/60R16 MR162 92V', 0),
(4798, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 285/75R16 MT MUD TRAC', 0),
(4799, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 305/70R16 MT 118/115Q MUD TRAC', 0),
(4800, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO 265/75R16 AT RANGER', 0),
(4801, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO 265/70R16 MT DAKAR', 0),
(4802, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 245/75R16 AT DESTINATION', 0),
(4803, 'PIRELLI', 'LLANTA', 'INYECCION', 'LLANTA PIRELLI 255/70R16 MT SCORPION', 0),
(4804, 'LING LONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 255/70R16 MT CROSSWIND', 0),
(4805, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 205/70R16 HT DUELER 112S', 0),
(4806, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 225/70R16 HT PAYAK 103H', 0),
(4807, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 175/80R14 OPTIMO K715 88T', 0),
(4808, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 205/70R14 SU830 95H', 0),
(4809, 'LAUFENN', 'LLANTA', 'INYECCION', 'LLANTA LAUFENN 185/70R14 FITAS 88T', 0),
(4810, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 185/70R14 HF201 88H', 0),
(4811, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 185/70R14 SENSSE 88T', 0),
(4812, 'FEDERAL', 'LLANTA', 'INYECCION', 'LLANTA FEDERAL 205/60R14 SUPER STEEL 89H', 0),
(4813, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 175/65R14 HF501 82T', 0),
(4814, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 185/60R14 MR162 82H', 0),
(4815, 'CATCHGRE', 'LLANTA', 'INYECCION', 'LLANTA CATCHGRE 185/60R14 WINDFORSE 82H', 0),
(4816, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 185/60R14 SU830 82H', 0),
(4817, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 175/65R14 MAXIMUS 82H', 0),
(4818, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 175/65R14 OPTIMO K715 82T', 0),
(4819, 'MAXTREK', 'LLANTA', 'INYECCION', 'LLANTA MAXTREK 185/65R14 SU830 86H', 0),
(4820, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 165/60R14 KINERGY K425 75H', 0),
(4821, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 185/70R14 ASSURANCE 88T', 0),
(4822, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 195/60R14 H550 86H', 0),
(4823, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 195/60R14 TR928 86H', 0),
(4824, 'CONTINENTAL', 'LLANTA', 'INYECCION', 'LLANTA CONTINENTAL 175/65R14 POWER CONTAC 82H', 0),
(4825, 'GOOD FRIEND', 'LLANTA', 'INYECCION', 'LLANTA GOOD FRIEND 175/65R14 ASTR 82H', 0),
(4826, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 185/65R14 OPTIMO02 86H', 0),
(4827, 'WANDA', 'LLANTA', 'INYECCION', 'LLANTA WANDA 185/70R14 WR080 88T', 0),
(4828, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 185/70R14 TR928 92H', 0),
(4829, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 185/65R14 TR928 86H', 0),
(4830, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 165/60R14 CLIMAX T90A 75H', 0),
(4831, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 165/60R14 SOLUS K117 75H', 0),
(4832, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 195/70R15 MR2000 8PR', 0),
(4833, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 195/65R15 MR162 91W', 0),
(4834, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 195/65R15 ALTIMAX 91H', 0),
(4835, 'CATCHGRE', 'LLANTA', 'INYECCION', 'LLANTA CATCHGRE 205/60R15 WINDFORSE 91V', 0),
(4836, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 205/60R15 ALTIMAX 91H', 0),
(4837, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 205/55R15 H660 88V', 0),
(4838, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 185/55R15 VENTUS 82V', 0),
(4839, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 205/50R15 ZIEX 86V', 0),
(4840, 'DEESTONE', 'LLANTA', 'INYECCION', 'LLANTA DEESTONE 195/55R15 VINCENTE 85V', 0),
(4841, 'KUMHO', 'LLANTA', 'INYECCION', 'LLANTA KUMHO 205/50R15 ECSTA KU31 86V', 0),
(4842, 'THUNDERER', 'LLANTA', 'INYECCION', 'LLANTA THUNDERER 185/55R15 MANCHE II 82V', 0),
(4843, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 195/50R15 MR182 86V', 0),
(4844, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 195/50R15 PROXES 82V', 0),
(4845, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 6.50R14 AVOI RADIAL', 0),
(4846, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 215/75R14 AT GRABBER', 0),
(4847, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 195R14 RAO08 106/104R 8PR', 0),
(4848, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 195R14 SUPER2000 106/104R 8PR', 0),
(4849, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 205/60R14 ZIEXX 88H', 0),
(4850, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 205/60R14 PROXES 85H', 0),
(4851, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 195/60R14 PROXES 85H', 0),
(4852, 'FUZION', 'LLANTA', 'INYECCION', 'LLANTA FUZION 185/65R14 TURING 86T', 0),
(4853, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 165/70R14 TR928 85T', 0),
(4854, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 175/65R14 TR928 82H', 0),
(4855, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 195/60R14 VENTURA 86H', 0),
(4856, 'NEXEN', 'LLANTA', 'INYECCION', 'LLANTA NEXEN 205/60R14 CP641 88H', 0),
(4857, 'BRIDGESTONE', 'LLANTA', 'INYECCION', 'LLANTA BRIDGESTONE 235/65R18 HT DUELER 104T', 0),
(4858, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 245/60R18 HT DYNAPRO 105H', 0),
(4859, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 235/60R18 HT HP801 107V', 0),
(4860, 'DURUN', 'LLANTA', 'INYECCION', 'LLANTA DURUN 235/60R18 HT K313 103H', 0),
(4861, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 255/55R18 VENTUS ESAT 109Y', 0),
(4862, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/55R18 OPTIMO H426 100H', 0),
(4863, 'LING LONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 255/45R18 RADIAL TUBELES 103W (DEPORTIVA)', 0),
(4864, 'FEDERAL', 'LLANTA', 'INYECCION', 'LLANTA FEDERAL 225/45R18 SUPER STEEL 91W (DEPORTIVA)', 0),
(4865, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 225/40R18 RADIAL TUBELES 92V (DEPORTIVA)', 0),
(4866, 'FEDERAL', 'LLANTA', 'INYECCION', 'LLANTA FEDERAL 215/40R18 SUPER STEEL 85W (DEPORTIVA)', 0),
(4867, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.50-15 POS CHASQUI HI MILER CT162', 0),
(4868, 'OTANI', 'LLANTA', 'INYECCION', 'LLANTA OTANI 7.50-15 POS 120/123K 14PR', 0),
(4869, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 7.50-15 MIX 121/129C 14PR', 0),
(4870, 'BCT', 'LLANTA', 'INYECCION', 'LLANTA BCT 7.00R16 DEL 117/116N JINGLUN 12PR', 0),
(4871, 'MALHOTRA', 'LLANTA', 'INYECCION', 'LLANTA MALHOTRA 7.50-15 POS 121/119J 14PR', 0),
(4872, 'SAMSON', 'LLANTA', 'INYECCION', 'LLANTA SAMSON 7.00-14 PAN TRAKER POWER OB105', 0),
(4873, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 6.50-16 DEL CR892 CLIDA', 0),
(4874, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.00-15 POS CHASQUI CT162 10PR', 0),
(4875, 'WESTLAKE', 'LLANTA', 'INYECCION', 'LLANTA WESTLAKE 6.50R15 DEL ST313 CLIMAX 10PR', 0),
(4876, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 265/70R16 AT GRANDTREK', 0),
(4877, 'XCEED', 'LLANTA', 'INYECCION', 'LLANTA XCEED 6.50-14 DEL XD-711 8PR', 0),
(4878, 'SAMSON', 'LLANTA', 'INYECCION', 'LLANTA SAMSON 7.00-15 PAN TAKER PLUS 12PR', 0),
(4879, 'OTANI', 'LLANTA', 'INYECCION', 'LLANTA OTANI 6.00-14 POS U77 8PR', 0),
(4880, 'TEXXAN', 'LLANTA', 'INYECCION', 'LLANTA TEXXAN 6.50-14 POS LV912 102/100J 8PR', 0),
(4881, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 5.60-15 AUTOPISTA 78L', 0),
(4882, 'CHAO YANG', 'LLANTA', 'INYECCION', 'LLANTA CHAO YANG 6.00-13 POS CL885 6PR', 0),
(4883, 'FIRESTONE', 'LLANTA', 'INYECCION', 'LLANTA FIRESTONE 205/70R14 F570 93T', 0),
(4884, 'GOOD YEAR', 'LLANTA', 'INYECCION', 'LLANTA GOOD YEAR 7.00-15 DEL CAMINERA CT176 10PR', 0),
(4885, 'UNITED', 'LLANTA', 'INYECCION', 'LLANTA UNITED 6.50-14 DEL UT702 10PR', 0),
(4886, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 295/80R22.5 POS D801 152/149L', 0),
(4887, 'WILD COUNTRY', 'LLANTA', 'INYECCION', 'LLANTA WILD COUNTRY 245/75R16 MT RADIAL', 0),
(4888, 'COMFORSER', 'LLANTA', 'INYECCION', 'LLANTA COMFORSER 235/75R15 AT CF3000', 0),
(4889, 'GENERAL', 'LLANTA', 'INYECCION', 'LLANTA GENERAL 235/65R17 HT GRABBER 108H', 0),
(4890, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 235/55R19 GRANDTREK 105T', 0),
(4891, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 7.00R16 MIX MG702 14PR 108/114M', 0),
(4892, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 295/80R22.5 DEL S201 152/149M', 0),
(4893, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 235/65R17 HT HD815 104H', 0),
(4894, 'MIRAGE', 'LLANTA', 'INYECCION', 'LLANTA MIRAGE 225/65R17 HT HIGHWAY 102H', 0),
(4895, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO 225/70R15 HT RAPID', 0),
(4896, 'APLUS', 'LLANTA', 'INYECCION', 'LLANTA APLUS 195R15 A867 106/104L 8PR', 0),
(4897, 'MARSHALL', 'LLANTA', 'INYECCION', 'LLANTA MARSHALL 195R15 RADIAL 106/104R 6PR LT', 0),
(4898, 'GOODRIDE', 'LLANTA', 'INYECCION', 'LLANTA GOODRIDE 225/45R17 SV308 94W (DEPORTIVO)', 0),
(4899, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 5.60-12 AUTOPISTA 63L', 0),
(4900, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 6.50-13 AUTOPISTA 88L', 0),
(4901, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 185/70R14 T70 RADIAL 88S', 0),
(4902, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 165/65R13 T65 PLUS 77S', 0),
(4903, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 155/70R12 T70 RADIAL 73S TL', 0),
(5125, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 205/70R14 T70 RADIAL 95S', 0),
(5126, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 175/70R13 T70 82S', 0),
(5127, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 175R13 GT100 RADIAL 66S', 0),
(5128, 'LIMA CAUCHO', 'LLANTA', 'INYECCION', 'LLANTA LIMA CAUCHO 185/70R13 T70 RADIAL 86S', 0),
(5129, 'BARUM', 'LLANTA', 'INYECCION', 'LLANTA BARUM 185/70R13 BRILLANTIS 86T', 0),
(5130, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 10.00-20 POS L-602 18PR', 0),
(5131, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 10.00-20 DEL R-503 16PR', 0),
(5132, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 9.00-20 POS L-602 18PR', 0),
(5133, 'ORNET', 'LLANTA', 'INYECCION', 'LLANTA ORNET 9.00-20 DEL R-501 16PR', 0),
(5134, 'HIFLY', 'LLANTA', 'INYECCION', 'LLANTA HIFLY 8.25R20 MIX HH301 139/137L 16PR', 0),
(5135, 'TRIANGLE', 'LLANTA', 'INYECCION', 'LLANTA TRIANGLE 8.25R20 POS TR690 139/137K', 0),
(5136, 'GOODTYRE', 'LLANTA', 'INYECCION', 'LLANTA GOODTYRE 8.25R20 MIX 16PR 139/137K', 0),
(5137, 'OTANI', 'LLANTA', 'INYECCION', 'LLANTA OTANI 8.25-20 DEL U-77 16PR', 0),
(5138, 'OTANI', 'LLANTA', 'INYECCION', 'LLANTA OTANI 8.25-20 POS L-88 16PR', 0),
(5139, 'ADVANCE', 'LLANTA', 'INYECCION', 'LLANTA ADVANCE 17.5-24 14PR R4 ESTREME STAR', 0),
(5140, 'GOODTYRE', 'LLANTA', 'INYECCION', 'LLANTA GOODTYRE 17.5-25 L-3 20PR R6', 0),
(5141, 'YOKOHAMA', 'LLANTA', 'INYECCION', 'LLANTA YOKOHAMA 185/60R15 E70 84H', 0),
(5142, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 195/50R15 VENTUS 82H', 0),
(5143, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 235/60R14 PROXES 96H', 0),
(5144, 'GOODRIDE', 'LLANTA', 'INYECCION', 'LLANTA GOODRIDE 185R14 SC328 102/100Q 8PR', 0),
(5145, 'NESCSTEL', 'LLANTA', 'INYECCION', 'LLANTA NECSTEL 195/75R14 A/T 93/900N TEEL RADIAL', 0),
(5146, 'GOODRIDE', 'LLANTA', 'INYECCION', 'LLANTA GOODRIDE 205/55R16 SV308 94W', 0),
(5147, 'ARMOUR', 'LLANTA', 'INYECCION', 'LLANTA ARMOUR 14.00R24 L2 RX', 0),
(5148, 'SAMSON', 'LLANTA', 'INYECCION', 'LLANTA SAMSON 12-16.5 12PR L2D SKID STEER', 0),
(5149, 'YOKOHAMA', 'LLANTA', 'INYECCION', 'LLANTA YOKOHAMA 225/65R17 HT GEOLANDAR 102H', 0),
(5150, 'DUNLOP', 'LLANTA', 'INYECCION', 'LLANTA DUNLOP 225/70R17 AT2 GRANDTREK', 0),
(5151, 'LINGLONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 275/55R20 L689 117V', 0),
(5152, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 245/50R20 VENTUS 102V', 0),
(5153, 'GOLDWAY', 'LLANTA', 'INYECCION', 'LLANTA GOLDWAY 275/45R20 G2003 110V', 0),
(5154, 'HAIDA', 'LLANTA', 'INYECCION', 'LLANTA HAIDA 275/40R20 RACING HD609 109W', 0),
(5155, 'ROADSTONE', 'LLANTA', 'INYECCION', 'LLANTA ROADSTONE 275/35R20 N7000 102W', 0),
(5156, 'LINGLONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 275/40R20 LL680 106V', 0),
(5157, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO 235/65R17 AT DAKAR', 0),
(5158, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 265/65R17 AT OPEN COUNTRY', 0),
(5159, 'TOYO TYRES', 'LLANTA', 'INYECCION', 'LLANTA TOYO TYRES 265/75R16 AT OPEN COUNTRY', 0),
(5160, 'CATCHFORSE', 'LLANTA', 'INYECCION', 'LLANTA CATCHFORSE 265/75R16 AT WINDFORSE', 0),
(5161, 'FALKEN', 'LLANTA', 'INYECCION', 'LLANTA FALKEN 275/70R16 AT LANDAIR', 0),
(5162, 'INSA TURBO', 'LLANTA', 'INYECCION', 'LLANTA INSA TURBO 235/65R17 HT ECODRIVE', 0),
(5163, 'LING LONG', 'LLANTA', 'INYECCION', 'LLANTA LING LONG 235/75R17.5 DEL LLA78 134/139F 16PR', 0),
(5164, 'NEXEN', 'LLANTA', 'INYECCION', 'LLANTA NEXEN 265/70R18 HT ROADIAN', 0),
(5165, 'HERCULES', 'LLANTA', 'INYECCION', 'LLANTA HERCULES 275/65R18 AT ALL TRAC', 0),
(5212, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 225/70R15 RA08', 0),
(5213, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/60R17V RA33', 0),
(5214, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 235/65R17H RA33', 0),
(5215, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 275/55R20T RF10 AT', 0),
(5216, 'HANKOOK', 'LLANTA', 'INYECCION', 'LLANTA HANKOOK 175/70R13T K715', 0),
(5217, 'EL DORADO', 'LLANTA', 'INYECCION', 'LLANTA DORADO 185/70R13 ', 0),
(5218, 'SUNOTE', 'LLANTA', 'INYECCION', 'LLANTA SUNOTE 185/70R13', 0),
(5220, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA 155/70 R12 73T CITY TOUR POWER TRACC', 0),
(5221, 'POWERTRAC', 'LLANTA', 'INYECCION', 'LLANTA 165/70 R12 77T CITYTOUR POWERTRAC', 0),
(4398, 'DURO', 'LLANTA MOTO LI', 'INYECCION', 'LLANTA DURO 90/90-19 HF-903 52P TT', 0),
(4409, 'DURO', 'LLANTA MOTO LI', 'INYECCION', 'LLANTA DURO 100/100-17 58M PANTANERA DM1112', 0),
(4376, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 130/70-17 62S PILOT STREET TL/TT', 0),
(4377, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 100/90-19 57M S/I STARCROSS MH3', 0),
(4378, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN MX 110/90-19 M/C 62M STARCROSS MH3 TT', 0),
(4379, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 90/90-21 54S SIRAC TT', 0),
(4380, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 90/90-18 57P SIRAC STREET TT', 0),
(4381, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 90/90-18 57P CITY PRO TT', 0),
(4382, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 80/100-21 51M S/I STARCROSS MS3', 0),
(4383, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 3.00-18 52S CITY PRO TT', 0),
(4384, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 2.75-18 42P SIRAC STREET TL/TT', 0),
(4385, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 110/70-17 54S PILOT STREET TL/TT', 0),
(4386, 'MICHELIN', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MICHELIN 100/80-17 52S PILOT STREET TL/TT', 0),
(4387, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 21-275 333 HF DEL XL185', 0),
(4388, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 18-4.10 335 HF POS. XL185 (CG)', 0),
(4389, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 18-300 336 HF POS. CG125', 0),
(4390, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 18-300 311 HF', 0),
(4391, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 18-300 307 HF TRAIL', 0),
(4392, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA MOTO DURO 17-300 336 HF', 0),
(4393, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 17-300 307 HF TRAIL', 0),
(4394, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 17-275 303 HF', 0),
(4395, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 17-250 303 HF C70 POS', 0),
(4396, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 17-250 301E HF C90', 0),
(4397, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 14-275 315 HF', 0),
(4399, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 90/90-19 DM-1226 4PR 52P TT', 0),
(4400, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 90/90-12 HF-908F 54J TL', 0),
(4401, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 300-18 4PR HF-333', 0),
(4402, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 3.50-10 HF-263 4PR 51J TL', 0),
(4403, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 18-90/90 329 HF PISTERA TL', 0),
(4404, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 130/90-15 69P HF296C TL', 0),
(4405, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO-DURO 120/90-17 64S HF904', 0),
(4406, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 110/90-17 HF-904 60P TT', 0),
(4407, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 110/90-17 DM-1226 4PR 60P TT', 0),
(4408, 'DURO', 'LLANTA MOTO LIN', 'INYECCION', 'LLANTA DURO 100/90-10 HF-290R 4PR TL', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Seg_Usuario`
--

CREATE TABLE `Seg_Usuario` (
  `Usuario` varchar(255) NOT NULL,
  `IdUsuarioPerfil` int(11) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `NombreUsuario` varchar(255) DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Seg_Usuario`
--

INSERT INTO `Seg_Usuario` (`Usuario`, `IdUsuarioPerfil`, `Password`, `NombreUsuario`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
('admin', 1, '123', 'jeam', b'0', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '-'),
('dig', 2, '123', 'digitador', b'0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Seg_UsuarioModulo`
--

CREATE TABLE `Seg_UsuarioModulo` (
  `IdUsuarioModulo` int(11) NOT NULL,
  `UsuarioModulo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Seg_UsuarioModulo`
--

INSERT INTO `Seg_UsuarioModulo` (`IdUsuarioModulo`, `UsuarioModulo`) VALUES
(1, 'V_VentaForm.php');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Seg_UsuarioModulo_has_UsuarioPerfil`
--

CREATE TABLE `Seg_UsuarioModulo_has_UsuarioPerfil` (
  `IdUsuarioModulo` int(11) NOT NULL,
  `IdUsuarioPerfil` int(11) NOT NULL,
  `Lectura` bit(1) DEFAULT NULL,
  `Escritura` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Seg_UsuarioModulo_has_UsuarioPerfil`
--

INSERT INTO `Seg_UsuarioModulo_has_UsuarioPerfil` (`IdUsuarioModulo`, `IdUsuarioPerfil`, `Lectura`, `Escritura`) VALUES
(1, 1, b'1', b'1'),
(1, 2, b'1', b'0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Seg_UsuarioPerfil`
--

CREATE TABLE `Seg_UsuarioPerfil` (
  `IdUsuarioPerfil` int(11) NOT NULL,
  `UsuarioPerfil` varchar(255) DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Seg_UsuarioPerfil`
--

INSERT INTO `Seg_UsuarioPerfil` (`IdUsuarioPerfil`, `UsuarioPerfil`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(1, 'Administrador', b'0', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '-'),
(2, 'Digitador', b'0', '0000-00-00 00:00:00', '-', '0000-00-00 00:00:00', '-');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblKardex`
--

CREATE TABLE `tblKardex` (
  `d1` varchar(255) DEFAULT ' ',
  `d2` varchar(255) DEFAULT ' ',
  `d3` varchar(255) DEFAULT ' ',
  `d4` varchar(255) DEFAULT ' ',
  `d5` varchar(255) DEFAULT ' '
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tblKardex`
--

INSERT INTO `tblKardex` (`d1`, `d2`, `d3`, `d4`, `d5`) VALUES
('KARDEX', ' ', ' ', ' ', ' '),
(' ', ' ', ' ', ' ', ' '),
('PRODUCTO', 'LLANTA HANKOOK 165/65R14T K715 77T', ' ', ' ', ' '),
('DESDE', '2018-02-01', ' ', ' ', ' '),
('HASTA', '2018-02-22', ' ', ' ', ' '),
(' ', ' ', ' ', ' ', ' '),
('FECHA', 'DETALLE', 'ENTRADA', 'SALIDA', 'SALDO'),
(' ', 'SALDO ANTERIOR', ' ', ' ', '20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblKardexvalor`
--

CREATE TABLE `tblKardexvalor` (
  `d1` varchar(255) DEFAULT ' ',
  `d2` varchar(255) DEFAULT ' ',
  `d3` varchar(255) DEFAULT ' ',
  `d4` varchar(255) DEFAULT ' ',
  `d5` varchar(255) DEFAULT ' ',
  `d6` varchar(255) DEFAULT ' ',
  `d7` varchar(255) DEFAULT ' ',
  `d8` varchar(255) DEFAULT ' ',
  `d9` varchar(255) DEFAULT ' ',
  `d10` varchar(255) DEFAULT ' ',
  `d11` varchar(255) DEFAULT ' '
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tblKardexvalor`
--

INSERT INTO `tblKardexvalor` (`d1`, `d2`, `d3`, `d4`, `d5`, `d6`, `d7`, `d8`, `d9`, `d10`, `d11`) VALUES
('KARDEX VALORIZADO', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '),
(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '),
('PRODUCTO', 'LLANTA CONTINENTAL 265/65R17 AT TERRAINCONTAC', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '),
('AÑO', '2018', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '),
(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '),
('DESDE ', '2018-01-01', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '),
('HASTA ', '2018-12-31', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '),
(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '),
('FECHA', 'DETALLE', 'ENTRADA', 'P/C', 'TOTAL', 'SALIDA', 'P/C', 'TOTAL', 'SALDO', 'P/C', 'TOTAL'),
('2017-01-01', 'SALDO INICIAL', '0', '0', '0', '', '', '', '0', '0', '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_DocVenta`
--

CREATE TABLE `Ve_DocVenta` (
  `idDocVenta` int(11) NOT NULL,
  `IdDocVentaPuntoVenta` int(11) NOT NULL,
  `IdCliente` int(11) NOT NULL,
  `IdTipoDoc` int(11) NOT NULL,
  `IdAlmacen` int(11) NOT NULL,
  `Serie` varchar(255) DEFAULT NULL,
  `Numero` int(11) DEFAULT NULL,
  `FechaDoc` datetime DEFAULT NULL,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL,
  `Hash` varchar(255) DEFAULT NULL,
  `IdCierre` int(11) DEFAULT NULL,
  `EsCredito` bit(1) DEFAULT NULL,
  `FechaCredito` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_DocVenta`
--

INSERT INTO `Ve_DocVenta` (`idDocVenta`, `IdDocVentaPuntoVenta`, `IdCliente`, `IdTipoDoc`, `IdAlmacen`, `Serie`, `Numero`, `FechaDoc`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`, `IdCierre`, `EsCredito`, `FechaCredito`) VALUES
(153, 1, 39, 1, 1, '001', 1, '2018-02-24 09:21:48', b'0', '2018-02-24 09:21:48', 'jeam', NULL, NULL, '1519489308', NULL, b'0', NULL),
(154, 1, 39, 3, 1, '001', 2, '2018-02-24 09:25:53', b'0', '2018-02-24 09:25:53', 'jeam', NULL, NULL, '1519489553', NULL, b'0', NULL),
(155, 1, 0, 3, 1, '001', 3, '2018-02-24 09:44:07', b'0', '2018-02-24 09:44:07', 'jeam', NULL, NULL, '1519490647', NULL, b'0', NULL),
(156, 1, 39, 3, 1, '001', 4, '2018-02-24 10:05:45', b'0', '2018-02-24 10:05:45', 'jeam', NULL, NULL, '1519491945', NULL, b'0', NULL),
(157, 1, 0, 3, 1, '001', 5, '2018-02-24 10:53:48', b'0', '2018-02-24 10:53:48', 'jeam', NULL, NULL, '1519494828', NULL, b'0', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_DocVentaCliente`
--

CREATE TABLE `Ve_DocVentaCliente` (
  `IdCliente` int(11) NOT NULL,
  `Cliente` varchar(255) DEFAULT NULL,
  `DniRuc` varchar(255) DEFAULT NULL,
  `Direccion` longtext,
  `Telefono` longtext,
  `Email` varchar(255) DEFAULT NULL,
  `Anulado` int(11) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_DocVentaCliente`
--

INSERT INTO `Ve_DocVentaCliente` (`IdCliente`, `Cliente`, `DniRuc`, `Direccion`, `Telefono`, `Email`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(0, '-', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'KINGNER GROUP SOCIEDAD ANONIMA CERRADA', '20601330955', 'JR. LOS NOGALES MZA. N LOTE. 37 URB. LOS PORTALES (FRENTE AL SEGUNDO PARQUE DE LOS PORTALES) HUANUCO - HUANUCO - AMARILIS', '/ 964724625', '', 0, '2018-02-23 09:39:09', 'jeam', NULL, NULL),
(40, 'IMPORTACIONES Y CERAMICAS NICOL VILLANUEVA EMPRESA INDIVIDUAL DE RESPONSABILIDAD LIMITADA', '20489569800', '----VIA INTER REGIONAL HUANUCO - TINGO MARIA KM. 2 LLICUA BAJA HUANUCO - HUANUCO - AMARILIS', '516429 / 979800749', '', 0, '2018-02-23 09:46:26', 'jeam', NULL, NULL),
(41, 'DISTRIBUCIONES ADAN EMPRESA INDIVIDUAL DE RESPONSABILIDAD LIMITADA', '20601479886', 'JR. LEÓN DE HUÁNUCO NRO. 175 (JR LEÓN DE HUÁNUCO 183) HUANUCO - HUANUCO - HUANUCO', '/ 990004715 / 999024319', '', 0, '2018-02-23 09:46:40', 'jeam', NULL, NULL),
(42, 'PONCE ADAN ADLER', '44030535', '', '', '', 0, '2018-02-23 09:47:00', 'jeam', NULL, NULL),
(43, 'CHINO LURITA PIERR DANIEL', '72560482', '', '', '', 0, '2018-02-23 09:47:17', 'jeam', NULL, NULL),
(44, 'CONSULTORA INFORMATICA NEUROSYSTEM PERU S.A.C', '20573027125', 'JR. 28 DE JULIO NRO. 313 CENT C.U HUANUCO (FRENTE AL GRIFO TORRES) HUANUCO - HUANUCO - HUANUCO', '/ 962991328', '', 0, '2018-02-23 15:38:59', 'jeam', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_DocVentaDet`
--

CREATE TABLE `Ve_DocVentaDet` (
  `IdDocVentaDet` int(11) NOT NULL,
  `IdDocVenta` int(11) NOT NULL,
  `IdProducto` int(11) NOT NULL,
  `Cantidad` float DEFAULT NULL,
  `Precio` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_DocVentaDet`
--

INSERT INTO `Ve_DocVentaDet` (`IdDocVentaDet`, `IdDocVenta`, `IdProducto`, `Cantidad`, `Precio`) VALUES
(5, 153, 3156, 1, 156),
(6, 154, 3156, 1, 156),
(7, 155, 3156, 1, 156),
(8, 156, 3156, 1, 156),
(9, 157, 3156, 1, 156);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_DocVentaMetodoPago`
--

CREATE TABLE `Ve_DocVentaMetodoPago` (
  `IdMetodoPago` int(11) NOT NULL,
  `MetodoPago` varchar(255) DEFAULT NULL,
  `EsTarjeta` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_DocVentaMetodoPago`
--

INSERT INTO `Ve_DocVentaMetodoPago` (`IdMetodoPago`, `MetodoPago`, `EsTarjeta`) VALUES
(1, 'Efectivo', b'0'),
(2, 'Visa', b'1'),
(3, 'Mastercard', b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_DocVentaMetodoPagoDet`
--

CREATE TABLE `Ve_DocVentaMetodoPagoDet` (
  `IdDocVenta` int(11) NOT NULL,
  `IdMetodoPago` int(11) NOT NULL,
  `Importe` float(15,2) NOT NULL,
  `NroTarjeta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_DocVentaMetodoPagoDet`
--

INSERT INTO `Ve_DocVentaMetodoPagoDet` (`IdDocVenta`, `IdMetodoPago`, `Importe`, `NroTarjeta`) VALUES
(2, 1, 0.00, '57.80'),
(2, 2, 1000000.00, '0'),
(3, 1, 20.00, 'paga tu cuenta sinverguenza'),
(3, 2, 32.00, '6518514517'),
(4, 1, 10.00, 'asdasd'),
(4, 2, 30.46, '72560482'),
(5, 1, 500.00, 'GFGH'),
(5, 2, 261.48, '72560482'),
(6, 1, 363.40, 'asd'),
(7, 1, 17.34, ''),
(8, 1, 23.12, ''),
(9, 2, 30.00, ''),
(9, 1, 100.00, ''),
(10, 1, 477.37, '123'),
(12, 1, 5.78, ''),
(13, 1, 50.00, ''),
(13, 2, 46.63, ''),
(14, 1, 0.00, ''),
(14, 1, 0.00, ''),
(15, 1, 562.44, ''),
(16, 1, 30.00, ''),
(16, 2, 356.52, ''),
(17, 1, 100.00, ''),
(17, 2, 30.00, ''),
(18, 2, 100.00, '1233'),
(18, 3, 100.00, '4444'),
(18, 1, 300.00, ''),
(20, 1, 5.78, ''),
(21, 1, 50.00, ''),
(21, 2, 117.52, ''),
(22, 1, 300.00, ''),
(22, 2, 275.90, ''),
(23, 1, 96.63, ''),
(24, 1, 140.90, ''),
(25, 1, 169.44, ''),
(26, 1, 10.00, ''),
(27, 1, 100.00, ''),
(27, 2, 50.00, '6768'),
(27, 2, 30.00, '9009'),
(28, 1, 350.00, ''),
(29, 1, 34.68, 'gjjhgh'),
(30, 1, 6.00, ''),
(31, 1, 6.00, ''),
(32, 1, 3.00, ''),
(33, 1, 4.50, ''),
(35, 1, 15.00, ''),
(35, 1, 15.00, ''),
(36, 1, 4.50, ''),
(37, 1, 18.00, ''),
(38, 2, 10.00, '1231'),
(38, 3, 5.00, '2453'),
(38, 2, 5.00, '233'),
(38, 1, 100.00, ''),
(39, 1, 9.00, ''),
(40, 1, 7.50, ''),
(41, 1, 1.50, ''),
(42, 1, 7.50, ''),
(43, 1, 7.50, ''),
(44, 1, 1.50, ''),
(45, 1, 2.50, ''),
(47, 1, 1.50, ''),
(48, 1, 7.50, ''),
(49, 1, 124.00, ''),
(50, 1, 16.50, ''),
(52, 1, 12.00, ''),
(53, 1, 90.00, ''),
(54, 1, 45.00, ''),
(55, 1, 45.00, ''),
(56, 1, 7.50, ''),
(57, 1, 12.00, ''),
(58, 1, 1.50, '10.00'),
(59, 1, 100.00, ''),
(60, 1, 200.00, 'COMPRA'),
(61, 1, 200.00, ''),
(62, 1, 45.00, ''),
(63, 1, 18.50, ''),
(64, 1, 25.00, ''),
(65, 1, 18.50, ''),
(66, 1, 200.00, ''),
(67, 1, 15.00, ''),
(68, 1, 30.00, ''),
(69, 2, 400.00, ''),
(70, 3, 5.00, ''),
(71, 1, 50.00, 'MEDICAMENTOS'),
(72, 1, 200.00, ''),
(73, 1, 1500.00, ''),
(74, 1, 50.00, ''),
(75, 1, 50.00, ''),
(76, 1, 5.00, ''),
(77, 1, 100.00, ''),
(78, 1, 100.00, ''),
(79, 1, 100.00, ''),
(80, 1, 100.00, ''),
(80, 2, 50.00, '1231'),
(80, 3, 20.00, '123'),
(81, 1, 100.00, ''),
(82, 1, 200.00, ''),
(83, 1, 18.50, ''),
(84, 1, 20.00, ''),
(85, 1, 100.00, ''),
(86, 1, 200.00, ''),
(87, 1, 100.00, ''),
(87, 1, 0.00, ''),
(88, 1, 31.50, ''),
(89, 1, 18.50, ''),
(90, 1, 18.50, ''),
(91, 1, 40.00, ''),
(92, 1, 18.50, ''),
(93, 1, 18.50, ''),
(94, 1, 108.00, ''),
(95, 1, 18.50, ''),
(96, 1, 18.50, ''),
(97, 2, 20.00, '2312'),
(97, 1, 100.00, ''),
(98, 1, 18.50, ''),
(99, 1, 37.00, ''),
(102, 1, 100.00, ''),
(103, 1, 126.00, ''),
(104, 1, 10.00, ''),
(105, 1, 42.00, ''),
(106, 1, 42.00, ''),
(107, 1, 42.00, ''),
(108, 1, 10.00, ''),
(109, 1, 100.00, ''),
(110, 1, 10.50, ''),
(111, 1, 1.00, ''),
(112, 1, 50.00, ''),
(113, 1, 50.00, ''),
(114, 1, 145.00, ''),
(115, 1, 18.50, ''),
(116, 1, 18.50, ''),
(118, 1, 50.00, ''),
(119, 1, 18.50, ''),
(120, 1, 20.00, ''),
(121, 1, 18.50, ''),
(122, 1, 16.50, ''),
(123, 1, 18.50, ''),
(124, 1, 18.50, ''),
(125, 1, 18.50, ''),
(126, 1, 16.50, ''),
(127, 1, 40.00, ''),
(128, 1, 19.50, ''),
(129, 1, 42.50, ''),
(130, 1, 36.50, ''),
(131, 1, 37.00, ''),
(132, 1, 150.00, ''),
(133, 1, 55.50, ''),
(134, 1, 100.00, ''),
(135, 1, 18.50, ''),
(136, 1, 18.50, ''),
(137, 1, 39.00, ''),
(139, 2, 103.00, ''),
(140, 1, 400.00, ''),
(141, 1, 150.00, ''),
(142, 1, 70.00, ''),
(143, 1, 19.50, ''),
(144, 1, 19.50, ''),
(145, 2, 3.00, ''),
(146, 1, 37.00, ''),
(147, 1, 55.50, ''),
(148, 2, 75.00, ''),
(151, 1, 468.00, ''),
(152, 1, 628.00, ''),
(153, 1, 156.00, ''),
(154, 1, 156.00, ''),
(155, 1, 156.00, ''),
(156, 1, 156.00, ''),
(157, 1, 156.00, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_DocVentaPuntoVenta`
--

CREATE TABLE `Ve_DocVentaPuntoVenta` (
  `IdDocVentaPuntoVenta` int(11) NOT NULL,
  `PuntoVenta` varchar(255) DEFAULT NULL,
  `SerieDocVenta` varchar(255) DEFAULT NULL,
  `SerieImpresora` varchar(255) DEFAULT NULL,
  `RutaImpresora` longtext,
  `Anulado` bit(1) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_DocVentaPuntoVenta`
--

INSERT INTO `Ve_DocVentaPuntoVenta` (`IdDocVentaPuntoVenta`, `PuntoVenta`, `SerieDocVenta`, `SerieImpresora`, `RutaImpresora`, `Anulado`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(1, 'CAJA1', '001', 'FF698761', '\\\\PCHINOL\\ticket', b'0', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_DocVentaPuntoVentaDet`
--

CREATE TABLE `Ve_DocVentaPuntoVentaDet` (
  `IdDocVentaPuntoVenta` int(11) NOT NULL,
  `IdDocVentaTipoDoc` int(11) NOT NULL,
  `Serie` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `Ve_DocVentaPuntoVentaDet`
--

INSERT INTO `Ve_DocVentaPuntoVentaDet` (`IdDocVentaPuntoVenta`, `IdDocVentaTipoDoc`, `Serie`) VALUES
(1, 1, '001'),
(1, 2, '001'),
(1, 3, '100'),
(1, 4, '0'),
(1, 5, '002');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_DocVentaTipoDoc`
--

CREATE TABLE `Ve_DocVentaTipoDoc` (
  `IdTipoDoc` int(11) NOT NULL,
  `TipoDoc` varchar(255) DEFAULT NULL,
  `VaRegVenta` bit(1) DEFAULT NULL,
  `CodSunat` varchar(255) DEFAULT NULL,
  `TipoDocSunat` varchar(255) DEFAULT NULL,
  `TieneIgv` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_DocVentaTipoDoc`
--

INSERT INTO `Ve_DocVentaTipoDoc` (`IdTipoDoc`, `TipoDoc`, `VaRegVenta`, `CodSunat`, `TipoDocSunat`, `TieneIgv`) VALUES
(1, 'FACTURA', b'1', '01', '01', b'0'),
(2, 'BOLETA', b'1', '03', '01', b'0'),
(3, 'TICKET', b'1', '12', '01', b'0'),
(4, 'NO VALIDO', b'0', '00', '99', b'0'),
(5, 'FACTURA/IGV', b'1', '01', '01', b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_ExpertoDiagnostico`
--

CREATE TABLE `Ve_ExpertoDiagnostico` (
  `IdDiagnostico` int(11) NOT NULL,
  `Diagnostico` varchar(255) NOT NULL,
  `Problema` text NOT NULL,
  `Edad` int(11) DEFAULT NULL,
  `Observacion` text,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL,
  `Hash` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_ExpertoDiagnostico`
--

INSERT INTO `Ve_ExpertoDiagnostico` (`IdDiagnostico`, `Diagnostico`, `Problema`, `Edad`, `Observacion`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`) VALUES
(41, 'OTITIS MEDIA AGUDA', 'DEL OIDO', 4110002, 'El Dx. se determina con supuración de oído o tímpano rojo. El tratamiento es ambulatorio', '2017-10-04 07:48:19', 'jeam', '2018-01-09 06:47:15', 'jeam', '1507128499'),
(42, 'ORZUELO.', 'DEL OJO.', 11110500, 'El Dx. se determina con pequeño nodulo blanco rojizo en el parpado.', '2017-10-26 06:58:01', 'jeam', '2017-11-04 06:30:36', 'jeam', '1509026281'),
(47, 'CANDIDIASIS ORAL.', 'ORAL.', 11110500, 'El Dx. se determina con placas blanca en la cavidad oral.', '2017-11-04 07:27:05', 'jeam', '2017-11-04 07:48:59', 'jeam', '1509805625'),
(44, 'MASTOIDITIS', 'DE OIDO', 4110002, 'Tiene un solo signo', '2017-10-31 15:24:52', 'jeam', '2017-11-03 15:47:02', 'jeam', '1509488692'),
(48, 'AFTAS BUCALES', 'ORAL', 11110500, 'El Dx. Se determina por erupción bucal de color amarillo y rojo alrededor.', '2017-11-04 07:48:05', 'jeam', '2017-11-04 07:49:29', 'jeam', '1509806885'),
(49, 'ESTREÑIMIENTO ', 'DIGESTIVO.', 11110500, 'El Dx. Se determina deposiciones duras y secas, menos de 3 deposiciones por semana.', '2017-11-04 08:07:50', 'jeam', NULL, NULL, '1509808070'),
(50, 'TIÑA CORPORIS.', 'DERMICO.', 11110500, 'El Dx. Se determina erupcion cutanea en forma de medallones circulares de borde escamoso y vesiculoso.', '2017-11-04 08:39:37', 'jeam', NULL, NULL, '1509809977'),
(51, 'DIABETES.', '', 64111200, 'El Dx. Se determina con sed excesiva.', '2017-11-04 09:08:21', 'jeam', '2017-11-04 10:21:21', 'jeam', '1509811701'),
(52, 'CONJUNTIVITIS', 'DEL OJO.', 64111200, 'Secreción purulenta amarillo - verdosa, con inflamación y enrojecimientos de los ojos.', '2017-11-04 09:32:55', 'jeam', '2017-11-10 07:17:35', 'jeam', '1509813175'),
(53, 'INFECCION DEL TRACTO URINARIO.', 'DEL TRACTO URINARIO.', 64111200, 'Dolor ardor al orinar.', '2017-11-04 09:45:37', 'jeam', '2017-11-04 10:21:50', 'jeam', '1509813937'),
(54, 'HIPERTENSION ARTERIAL.', '', 64111200, 'El Dx. Se determina zumbido de oídos, visión borrosa.', '2017-11-04 10:05:31', 'jeam', '2017-11-04 10:22:03', 'jeam', '1509815131'),
(55, 'ARTRITIS.', '', 64111200, 'El Dx. Se determina rigidez de las articulaciones, inflamación e hinchazón.', '2017-11-04 10:51:13', 'jeam', NULL, NULL, '1509817873'),
(56, 'LINFOGRANULOMA VENEREO.', 'ITS', 64111300, 'El Dx. Se determina tumefacción y dolor en los ganglios linfáticos. ', '2017-11-06 06:00:18', 'jeam', NULL, NULL, '1509973218'),
(57, 'GONORREA.', 'ITS', 64111300, 'El Dx. Se determina secreción blanco, amarillo o verden en el pene.', '2017-11-06 06:11:46', 'jeam', NULL, NULL, '1509973906'),
(58, 'HERPES.', 'ITS.', 64111300, 'El Dx. se determina con con ulceras o llagas genitales.', '2017-11-06 06:28:13', 'jeam', NULL, NULL, '1509974893'),
(59, 'BALANITIS.', 'ITS.', 64111300, 'El Dx. Se determina con inflamación del glande y el prepucio.', '2017-11-06 06:36:39', 'jeam', NULL, NULL, '1509975399'),
(60, 'ENFERMEDAD PELVICA INFLAMATORIA.', 'ITS.', 64111300, 'El Dx. Se determina con dolor abdominal inferior.', '2017-11-06 06:49:45', 'jeam', '2017-11-10 11:31:00', 'jeam', '1509976185'),
(61, 'DISENTERIA.', 'INTESTINAL.', 4110002, 'El Dx. Se determina con sangre en heces.', '2017-11-06 06:57:19', 'jeam', '2017-11-11 05:38:26', 'jeam', '1509976639'),
(62, 'PARASITOSIS', 'INTESTINAL.', 4110002, 'El Dx. Se determina cuando no se desparasito en los últimos 4 meses.', '2017-11-06 09:57:52', 'jeam', NULL, NULL, '1509987472'),
(63, 'RESFRIADO COMÚN.', 'RESPIRATORIO.', 4110002, 'El tratamiento es ambulatorio.', '2017-11-06 10:10:19', 'jeam', NULL, NULL, '1509988219'),
(64, 'LITIASIS BILIAR.', '', 64111200, 'El Dx. Se determina con dolor en cuadrante superior derecho del abdomen que se irradia al omóplato derecho o la espalda.', '2017-11-10 06:25:16', 'jeam', NULL, NULL, '1510320316'),
(65, 'APENDICITIS.', '', 64111200, 'El Dx. Se determina dolor abdominal agudo tipo cólico en la fosa iliaca derecha.', '2017-11-10 06:43:37', 'jeam', NULL, NULL, '1510321417'),
(66, 'CELULITIS ', 'DERMICO.', 64111200, 'El tratamiento es ambulatorio.', '2017-11-10 07:21:55', 'jeam', NULL, NULL, '1510323715'),
(67, 'SINUSITIS.', 'RESPIRATORIO.', 64111200, 'El tratamiento es ambulatorio.', '2017-11-10 07:32:46', 'jeam', NULL, NULL, '1510324366'),
(68, 'HEPATITIS A', '', 64111200, 'El tratamiento es ambulatorio.', '2017-11-10 08:01:39', 'jeam', NULL, NULL, '1510326099'),
(69, 'FARINGOAMIGDALITIS ESTREPTOCOCICA.', 'RESPIRATORIO.', 11110500, 'El tratamiento es ambulatorio.', '2017-11-10 08:20:07', 'jeam', '2017-11-10 09:11:09', 'jeam', '1510327207'),
(70, 'PAROTIDITIS.', '', 11110500, 'El tratamiento es ambulatorio.', '2017-11-10 08:28:36', 'jeam', NULL, NULL, '1510327716'),
(71, 'ESCABIOSIS.', 'DERMICO', 11110500, 'El tratamiento es ambulatorio.', '2017-11-10 08:53:49', 'jeam', NULL, NULL, '1510329229'),
(72, 'PIE DE ATLETA.', 'DERMICO.', 11110500, 'El tratamiento es ambulatorio.', '2017-11-10 09:08:00', 'jeam', NULL, NULL, '1510330080'),
(73, 'FARINGOAMIGDALITIS VIRAL.', 'RESPIRATORIO.', 11110500, 'El tratamiento es ambulatorio.', '2017-11-10 09:13:57', 'jeam', NULL, NULL, '1510330437'),
(74, 'VAGINOSIS.', 'ITS.', 64111300, 'El tratamiento es ambulatorio.', '2017-11-10 09:33:28', 'jeam', '2017-11-10 09:47:52', 'jeam', '1510331608'),
(75, 'CANDIDIASIS.', 'ITS', 64111300, 'El tratamiento es ambulatorio.', '2017-11-10 09:47:04', 'jeam', '2017-11-10 11:27:24', 'jeam', '1510332424'),
(76, 'GRANULOMA INGUINAL.', 'ITS.', 64111300, 'El tratamiento es ambulatorio.', '2017-11-10 11:39:28', 'jeam', NULL, NULL, '1510339168'),
(77, 'PEDICULOSIS PUBIS.', 'ITS.', 64111300, 'El tratamiento es ambulatorio.', '2017-11-10 11:55:47', 'jeam', NULL, NULL, '1510340147'),
(78, 'NEUMONIA', 'RESPIRATORIO.', 4110002, 'El Dx. Se determina con respiracion rapida.', '2017-11-10 12:11:55', 'jeam', NULL, NULL, '1510341115'),
(79, 'SOBA.', 'RESPIRATORIO.', 4110002, 'El tratamiento es ambulatorio.', '2017-11-10 13:08:50', 'jeam', NULL, NULL, '1510344530'),
(80, 'CRUP MODERADO.', 'RESPIRATORIO.', 4110002, 'El tratamiento es ambulatorio.', '2017-11-10 13:35:53', 'jeam', NULL, NULL, '1510346153'),
(81, 'DESIDRATACION.', 'INTESTINAL.', 4110002, 'El tratamiento es ambulatorio.', '2017-11-10 13:51:50', 'jeam', NULL, NULL, '1510347110'),
(82, 'CISTITIS.', '', 4110002, 'El tratamiento es ambulatorio.', '2017-11-11 06:38:31', 'jeam', NULL, NULL, '1510407511'),
(83, 'MENOPAUSIA.', 'CLIMATERIO.', 55110040, 'El tratamiento es ambulatorio.', '2017-11-11 11:13:44', 'jeam', '2017-11-11 11:15:23', 'jeam', '1510424024'),
(84, 'MUJER EN EDAD FERTIL', 'ANTICONCEPTIVO ORAL DE RUTINA.', 39111500, 'El anticonceptivo es de rutina via oral.', '2017-11-11 11:53:45', 'jeam', NULL, NULL, '1510426425'),
(85, 'MUJER EN EDAD FERTIL.', 'ANTICONCEPTIVO ORAL DE EMERGENCIA.', 39111500, 'La administracion es via oral dentro de las 72 horas despues de las relaciones sexuales sin proteccion.', '2017-11-11 12:15:57', 'jeam', '2017-11-11 12:18:46', 'jeam', '1510427757');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_ExpertoDiagnosticoSintomaDet`
--

CREATE TABLE `Ve_ExpertoDiagnosticoSintomaDet` (
  `IdDiagnostico` int(11) NOT NULL,
  `IdSintoma` int(11) NOT NULL,
  `Fechareg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_ExpertoDiagnosticoSintomaDet`
--

INSERT INTO `Ve_ExpertoDiagnosticoSintomaDet` (`IdDiagnostico`, `IdSintoma`, `Fechareg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`) VALUES
(41, 37, '2018-01-09 06:47:15', 'jeam', NULL, NULL),
(41, 38, '2018-01-09 06:47:15', 'jeam', NULL, NULL),
(41, 39, '2018-01-09 06:47:15', 'jeam', NULL, NULL),
(41, 40, '2018-01-09 06:47:15', 'jeam', NULL, NULL),
(43, 37, '2017-11-02 08:25:38', 'jeam', NULL, NULL),
(42, 43, '2017-11-04 06:30:36', 'jeam', NULL, NULL),
(42, 45, '2017-11-04 06:30:36', 'jeam', NULL, NULL),
(42, 48, '2017-11-04 06:30:36', 'jeam', NULL, NULL),
(43, 38, '2017-11-02 08:25:38', 'jeam', NULL, NULL),
(44, 40, '2017-11-03 15:47:02', 'jeam', NULL, NULL),
(45, 38, '2017-11-02 11:57:47', 'jeam', NULL, NULL),
(46, 40, '2017-11-01 18:14:30', 'jeam', NULL, NULL),
(45, 40, '2017-11-02 11:57:47', 'jeam', NULL, NULL),
(44, 46, '2017-11-03 15:47:02', 'jeam', NULL, NULL),
(47, 49, '2017-11-04 07:48:59', 'jeam', NULL, NULL),
(47, 40, '2017-11-04 07:48:59', 'jeam', NULL, NULL),
(47, 44, '2017-11-04 07:48:59', 'jeam', NULL, NULL),
(48, 44, '2017-11-04 07:49:29', 'jeam', NULL, NULL),
(47, 50, '2017-11-04 07:48:59', 'jeam', NULL, NULL),
(48, 51, '2017-11-04 07:49:29', 'jeam', NULL, NULL),
(49, 52, '2017-11-04 08:07:50', 'jeam', NULL, NULL),
(49, 53, '2017-11-04 08:07:50', 'jeam', NULL, NULL),
(50, 55, '2017-11-04 08:39:37', 'jeam', NULL, NULL),
(50, 54, '2017-11-04 08:39:37', 'jeam', NULL, NULL),
(51, 60, '2017-11-04 10:21:21', 'jeam', NULL, NULL),
(51, 59, '2017-11-04 10:21:21', 'jeam', NULL, NULL),
(51, 58, '2017-11-04 10:21:21', 'jeam', NULL, NULL),
(51, 57, '2017-11-04 10:21:21', 'jeam', NULL, NULL),
(51, 56, '2017-11-04 10:21:21', 'jeam', NULL, NULL),
(52, 61, '2017-11-10 07:17:35', 'jeam', NULL, NULL),
(52, 62, '2017-11-10 07:17:35', 'jeam', NULL, NULL),
(53, 65, '2017-11-04 10:21:50', 'jeam', NULL, NULL),
(53, 66, '2017-11-04 10:21:50', 'jeam', NULL, NULL),
(53, 64, '2017-11-04 10:21:50', 'jeam', NULL, NULL),
(54, 68, '2017-11-04 10:22:03', 'jeam', NULL, NULL),
(54, 69, '2017-11-04 10:22:03', 'jeam', NULL, NULL),
(54, 70, '2017-11-04 10:22:03', 'jeam', NULL, NULL),
(54, 59, '2017-11-04 10:22:03', 'jeam', NULL, NULL),
(52, 44, '2017-11-10 07:17:35', 'jeam', NULL, NULL),
(53, 67, '2017-11-04 10:21:50', 'jeam', NULL, NULL),
(54, 71, '2017-11-04 10:22:03', 'jeam', NULL, NULL),
(55, 72, '2017-11-04 10:51:13', 'jeam', NULL, NULL),
(55, 44, '2017-11-04 10:51:13', 'jeam', NULL, NULL),
(55, 73, '2017-11-04 10:51:13', 'jeam', NULL, NULL),
(56, 74, '2017-11-06 06:00:18', 'jeam', NULL, NULL),
(57, 75, '2017-11-06 06:11:46', 'jeam', NULL, NULL),
(57, 76, '2017-11-06 06:11:46', 'jeam', NULL, NULL),
(57, 64, '2017-11-06 06:11:46', 'jeam', NULL, NULL),
(58, 77, '2017-11-06 06:28:13', 'jeam', NULL, NULL),
(58, 74, '2017-11-06 06:28:13', 'jeam', NULL, NULL),
(59, 78, '2017-11-06 06:36:39', 'jeam', NULL, NULL),
(59, 79, '2017-11-06 06:36:39', 'jeam', NULL, NULL),
(59, 80, '2017-11-06 06:36:39', 'jeam', NULL, NULL),
(60, 82, '2017-11-10 11:31:00', 'jeam', NULL, NULL),
(60, 81, '2017-11-10 11:31:00', 'jeam', NULL, NULL),
(61, 83, '2017-11-11 05:38:26', 'jeam', NULL, NULL),
(62, 84, '2017-11-06 09:57:52', 'jeam', NULL, NULL),
(62, 85, '2017-11-06 09:57:52', 'jeam', NULL, NULL),
(62, 86, '2017-11-06 09:57:52', 'jeam', NULL, NULL),
(62, 87, '2017-11-06 09:57:52', 'jeam', NULL, NULL),
(63, 88, '2017-11-06 10:10:19', 'jeam', NULL, NULL),
(63, 89, '2017-11-06 10:10:19', 'jeam', NULL, NULL),
(63, 90, '2017-11-06 10:10:19', 'jeam', NULL, NULL),
(63, 91, '2017-11-06 10:10:19', 'jeam', NULL, NULL),
(64, 92, '2017-11-10 06:25:16', 'jeam', NULL, NULL),
(64, 93, '2017-11-10 06:25:16', 'jeam', NULL, NULL),
(64, 94, '2017-11-10 06:25:16', 'jeam', NULL, NULL),
(64, 40, '2017-11-10 06:25:16', 'jeam', NULL, NULL),
(64, 95, '2017-11-10 06:25:16', 'jeam', NULL, NULL),
(65, 96, '2017-11-10 06:43:37', 'jeam', NULL, NULL),
(65, 97, '2017-11-10 06:43:37', 'jeam', NULL, NULL),
(65, 40, '2017-11-10 06:43:37', 'jeam', NULL, NULL),
(65, 98, '2017-11-10 06:43:37', 'jeam', NULL, NULL),
(52, 63, '2017-11-10 07:17:35', 'jeam', NULL, NULL),
(52, 91, '2017-11-10 07:17:35', 'jeam', NULL, NULL),
(66, 99, '2017-11-10 07:21:55', 'jeam', NULL, NULL),
(66, 100, '2017-11-10 07:21:55', 'jeam', NULL, NULL),
(66, 101, '2017-11-10 07:21:55', 'jeam', NULL, NULL),
(66, 102, '2017-11-10 07:21:55', 'jeam', NULL, NULL),
(67, 103, '2017-11-10 07:32:46', 'jeam', NULL, NULL),
(67, 104, '2017-11-10 07:32:46', 'jeam', NULL, NULL),
(67, 105, '2017-11-10 07:32:46', 'jeam', NULL, NULL),
(68, 106, '2017-11-10 08:01:39', 'jeam', NULL, NULL),
(68, 107, '2017-11-10 08:01:39', 'jeam', NULL, NULL),
(68, 108, '2017-11-10 08:01:39', 'jeam', NULL, NULL),
(68, 40, '2017-11-10 08:01:39', 'jeam', NULL, NULL),
(68, 95, '2017-11-10 08:01:39', 'jeam', NULL, NULL),
(69, 109, '2017-11-10 09:11:09', 'jeam', NULL, NULL),
(70, 111, '2017-11-10 08:28:36', 'jeam', NULL, NULL),
(70, 112, '2017-11-10 08:28:36', 'jeam', NULL, NULL),
(70, 113, '2017-11-10 08:28:36', 'jeam', NULL, NULL),
(70, 40, '2017-11-10 08:28:36', 'jeam', NULL, NULL),
(71, 114, '2017-11-10 08:53:49', 'jeam', NULL, NULL),
(71, 115, '2017-11-10 08:53:49', 'jeam', NULL, NULL),
(71, 116, '2017-11-10 08:53:49', 'jeam', NULL, NULL),
(72, 118, '2017-11-10 09:08:00', 'jeam', NULL, NULL),
(72, 119, '2017-11-10 09:08:00', 'jeam', NULL, NULL),
(72, 117, '2017-11-10 09:08:00', 'jeam', NULL, NULL),
(72, 79, '2017-11-10 09:08:00', 'jeam', NULL, NULL),
(69, 110, '2017-11-10 09:11:09', 'jeam', NULL, NULL),
(73, 120, '2017-11-10 09:13:57', 'jeam', NULL, NULL),
(73, 121, '2017-11-10 09:13:57', 'jeam', NULL, NULL),
(74, 119, '2017-11-10 09:47:52', 'jeam', NULL, NULL),
(74, 64, '2017-11-10 09:47:52', 'jeam', NULL, NULL),
(74, 79, '2017-11-10 09:47:52', 'jeam', NULL, NULL),
(75, 127, '2017-11-10 11:27:24', 'jeam', NULL, NULL),
(75, 125, '2017-11-10 11:27:24', 'jeam', NULL, NULL),
(75, 82, '2017-11-10 11:27:24', 'jeam', NULL, NULL),
(74, 122, '2017-11-10 09:47:52', 'jeam', NULL, NULL),
(74, 82, '2017-11-10 09:47:52', 'jeam', NULL, NULL),
(75, 122, '2017-11-10 11:27:24', 'jeam', NULL, NULL),
(76, 128, '2017-11-10 11:39:28', 'jeam', NULL, NULL),
(76, 129, '2017-11-10 11:39:28', 'jeam', NULL, NULL),
(76, 130, '2017-11-10 11:39:28', 'jeam', NULL, NULL),
(77, 131, '2017-11-10 11:55:47', 'jeam', NULL, NULL),
(77, 132, '2017-11-10 11:55:47', 'jeam', NULL, NULL),
(78, 133, '2017-11-10 12:11:55', 'jeam', NULL, NULL),
(78, 134, '2017-11-10 12:11:55', 'jeam', NULL, NULL),
(78, 40, '2017-11-10 12:11:55', 'jeam', NULL, NULL),
(79, 135, '2017-11-10 13:08:50', 'jeam', NULL, NULL),
(79, 136, '2017-11-10 13:08:50', 'jeam', NULL, NULL),
(79, 137, '2017-11-10 13:08:50', 'jeam', NULL, NULL),
(79, 138, '2017-11-10 13:08:50', 'jeam', NULL, NULL),
(79, 139, '2017-11-10 13:08:50', 'jeam', NULL, NULL),
(79, 140, '2017-11-10 13:08:50', 'jeam', NULL, NULL),
(80, 141, '2017-11-10 13:35:53', 'jeam', NULL, NULL),
(80, 142, '2017-11-10 13:35:53', 'jeam', NULL, NULL),
(80, 40, '2017-11-10 13:35:53', 'jeam', NULL, NULL),
(80, 143, '2017-11-10 13:35:53', 'jeam', NULL, NULL),
(81, 144, '2017-11-10 13:51:50', 'jeam', NULL, NULL),
(81, 145, '2017-11-10 13:51:50', 'jeam', NULL, NULL),
(81, 146, '2017-11-10 13:51:50', 'jeam', NULL, NULL),
(81, 147, '2017-11-10 13:51:50', 'jeam', NULL, NULL),
(81, 148, '2017-11-10 13:51:50', 'jeam', NULL, NULL),
(81, 149, '2017-11-10 13:51:50', 'jeam', NULL, NULL),
(82, 64, '2017-11-11 06:38:31', 'jeam', NULL, NULL),
(82, 150, '2017-11-11 06:38:31', 'jeam', NULL, NULL),
(82, 151, '2017-11-11 06:38:31', 'jeam', NULL, NULL),
(83, 152, '2017-11-11 11:15:23', 'jeam', NULL, NULL),
(83, 123, '2017-11-11 11:15:23', 'jeam', NULL, NULL),
(83, 153, '2017-11-11 11:15:23', 'jeam', NULL, NULL),
(83, 154, '2017-11-11 11:15:23', 'jeam', NULL, NULL),
(83, 155, '2017-11-11 11:15:23', 'jeam', NULL, NULL),
(83, 82, '2017-11-11 11:15:23', 'jeam', NULL, NULL),
(83, 156, '2017-11-11 11:15:23', 'jeam', NULL, NULL),
(83, 157, '2017-11-11 11:15:23', 'jeam', NULL, NULL),
(84, 158, '2017-11-11 11:53:46', 'jeam', NULL, NULL),
(85, 158, '2017-11-11 12:18:46', 'jeam', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_ExpertoSintoma`
--

CREATE TABLE `Ve_ExpertoSintoma` (
  `IdSintoma` int(11) NOT NULL,
  `Sintoma` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `Ve_ExpertoSintoma`
--

INSERT INTO `Ve_ExpertoSintoma` (`IdSintoma`, `Sintoma`) VALUES
(43, 'inflamacion de los parpados.'),
(37, 'Supuración visible del oído que comenzó menos de 14 días  '),
(38, 'Tímpano rojo'),
(39, 'Dolor de oido'),
(40, 'Fiebre'),
(44, 'Dolor.'),
(45, 'pequeño nodulo blanco rojizo en el parpado.'),
(46, 'Tumefacción dolorosa al tacto de tras de la oreja'),
(47, 'Dificultad para respirar'),
(48, 'Dolor en zona de abceso.'),
(49, 'Placas blancas en mucosa de la lengua.'),
(50, 'Placas blancas en labios, encias y boca.'),
(51, 'Erupcion bucal de color amarillo y rojo alrededor de forma ovalada.'),
(52, 'Menos de tres deposiciones por semana.'),
(53, 'Deposiciones intestinales duras y secas.'),
(54, 'Forma de medallones circulares de borde escamoso y vesciluso.'),
(55, 'erupcion cutanea de borde pruriginoso y centro escamoso.'),
(56, 'Sed excesiva.'),
(57, 'Miccion frecuente.'),
(58, 'Cansancio'),
(59, 'Vision borrosa.'),
(60, 'Perder la sensibilidad de los pies o sentir hormigueo.'),
(61, 'Secrecion purulenta amarillo o verdosa.'),
(62, 'Inflamacion de los ojos.'),
(63, 'Costras que se forman durante la noche.'),
(64, 'Dolor ardor al orinar.'),
(65, 'Aumento del numero de micciones durante la noche.'),
(66, 'Dolor o presion en la parte baja del abdomen y espalda.'),
(67, 'Presencia de sangre en la orina.'),
(68, 'Dolor de cabeza.'),
(69, 'Zumbido de oidos'),
(70, 'Mareos'),
(71, 'Valor de la presion arterial mayor a lo normal.'),
(72, 'Rigidez en las articulaciones.'),
(73, 'Inflamación de las articulaciones.'),
(74, 'Tumefacion y dolor en los ganglios linfáticos.'),
(75, 'Hinchazón del escroto.'),
(76, 'Secreción blanco amarillo, verdoso.'),
(77, 'Ulcera o llagas genitales.'),
(78, 'Dolor y enrojecimiento  e  inflamacion de glande y el prepucio.'),
(79, 'Mal olor.'),
(80, 'Picor intenso.'),
(81, 'Dolor abdominal inferior.'),
(82, 'Dolor durante las relaciones sexuales.'),
(83, 'Sangre en heces.'),
(84, 'No se desparasito en los ultimos 4 meses.'),
(85, 'abdomen voluminoso.'),
(86, 'Diarrea persistente.'),
(87, 'Picazon en el ano.'),
(88, 'Secreción nasal.'),
(89, 'Estornudos.'),
(90, 'Bloque de la cavidad nariz.'),
(91, 'Enrojecimiento de los ojos.'),
(92, 'Dolor en el cuadrante superior derecho del abdomen que se irradia al omoplato derecho o la espalda.'),
(93, 'Dolor en 30 a 90 minutos despues de haber ingerido comidas rápidas.'),
(94, 'Dolor que dura de 30 minutos a 3 horas e incluso días.'),
(95, 'Nauseas, vómitos.'),
(96, 'Dolor abdominal agudo tipo cólico en la fosa iliaca derecha.'),
(97, 'Incremento rapido de la intensidad del dolor.'),
(98, 'Dolor que se incrementa al caminar o toser.'),
(99, 'Inflamación de piel.'),
(100, 'Enrojecimiento de la piel.'),
(101, 'Hinchazon cutanea brillante y estirada.'),
(102, 'Calor en el area enrojecida sin limites definidos.'),
(103, 'Dolor y pesades en la zona  ojos , frente y cara.'),
(104, 'Secreción nasal espesa.'),
(105, 'Obstrucción  nasal.'),
(106, 'Orina de color oscuro.'),
(107, 'Heces de color claro.'),
(108, 'Ictericia cutanea, ojos.'),
(109, 'Ganglios linfaticos del cuello crecidos y dolorosos.'),
(110, 'Garganta eritematosa con exudados blancos.'),
(111, 'Hinchazon dolorosa entre la oreja  y el angulo de la mandibula.'),
(112, 'Dolor en la masticacion, deglucion.'),
(113, 'Sordera.'),
(114, 'Prurito intenso de predominio nocturno.'),
(115, 'Surcos acarinos en los pliegues cutaneos.'),
(116, 'Nodulos rojisos marrones en zonas de pliegues cubiertas.'),
(117, 'Descamasion y grietas en los pliegues interdigitales.'),
(118, 'Vesiculas de contenido claro.'),
(119, 'Picazon.'),
(120, 'Garganta eritematosa.'),
(121, 'Con o sin exudados en las amigdalas.'),
(122, 'Flujo vaginal.'),
(123, 'Alteraciones mestruales.'),
(124, 'Prurito.'),
(125, 'Enrojecimiento e inflamacion de la vulva.'),
(126, 'Ardor.'),
(127, 'Ardor y prurito en los labios y en la vagina.'),
(128, 'Protuberancias carnosas rojas y pequeñas en los genitales o area peri anal.'),
(129, 'Genitales y la piel circundante pierden color.'),
(130, 'Destruye el tejido genital.'),
(131, 'Mancha roja que causa picor.'),
(132, 'Liendres o ladillas adheridas a la base del pelo pubico.'),
(133, 'Respiracion rapida.'),
(134, 'Sibilancias.'),
(135, 'Tos con predominio en la noche.'),
(136, 'Tos aumenta progresivamente de intensidad y frecuencia.'),
(137, 'Enrojecimiento de la cara.'),
(138, 'c/s ahogo.'),
(139, 'c/s agitacion.'),
(140, 'c/s silbido del pecho.'),
(141, 'Tos con voz ronca.'),
(142, 'C/s estridor al esfuerzo.'),
(143, 'Afonia.'),
(144, 'Intranquilo.'),
(145, 'Irritable.'),
(146, 'Ojos undidos.'),
(147, 'Boca y lengua seca.'),
(148, 'Bebe avidamente con sed.'),
(149, 'Signo de pliegue cutaneo.'),
(150, 'Aumento en numero de micciones en escasa cantidad.'),
(151, 'Sensacion de vacio incompleto en la evacuacion de la orina.'),
(152, 'Tener entre 40 a 55 años.'),
(153, 'Bochorno y sudoracion.'),
(154, 'Hemorragia anormal.'),
(155, 'Sequedad vaginal.'),
(156, 'Infecciones urinarias frecuentes.'),
(157, 'Ausencia de mestruacion igual o mayor de 12 meses.'),
(158, 'Control natal.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_ExpertoTratamiento`
--

CREATE TABLE `Ve_ExpertoTratamiento` (
  `IdTratamiento` int(11) NOT NULL,
  `IdDiagnostico` int(11) NOT NULL,
  `IdCompuesto` int(11) NOT NULL,
  `Observacion` text,
  `TomasXDia` float DEFAULT NULL,
  `DosisXPeso` float DEFAULT NULL,
  `UnidadDosisXPeso` varchar(255) DEFAULT NULL,
  `Concentracion` float DEFAULT NULL,
  `NroDias` int(11) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL,
  `Hash` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_ExpertoTratamiento`
--

INSERT INTO `Ve_ExpertoTratamiento` (`IdTratamiento`, `IdDiagnostico`, `IdCompuesto`, `Observacion`, `TomasXDia`, `DosisXPeso`, `UnidadDosisXPeso`, `Concentracion`, `NroDias`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Hash`) VALUES
(101, 41, 103, 'El tratamiento es ví oral', 2, 5, NULL, 5, 7, '2017-10-04 07:34:21', 'Jeam', NULL, NULL, '1507127661'),
(102, 41, 104, 'El tratamiento es vía oral y se dosifica con la trimetropina', 2, 5, NULL, 5, 7, '2017-10-04 07:38:54', 'Jeam', NULL, NULL, '1507127934'),
(103, 41, 70, 'El tratamiento es vía oral y es para aliviar los síntomas de dolor y la fiebre.', 3, 12, NULL, 5, 3, '2017-10-04 07:41:09', 'Jeam', NULL, NULL, '1507128069'),
(104, 0, 50, '', 3, 15, NULL, 5, 7, '2017-10-24 14:15:46', 'Jeam', NULL, NULL, '1508879746'),
(105, 0, 50, '', 3, 15, NULL, 5, 7, '2017-10-24 14:15:47', 'Jeam', NULL, NULL, '1508879747'),
(106, 0, 50, 'Antibiótico conjugado, la dosificación se hace con la amoxicilina', 3, 15, NULL, 5, 7, '2017-10-24 15:01:06', 'Jeam', NULL, NULL, '1508882466'),
(107, 0, 50, 'via poral expresado como amoxicilina ', 3, 10, NULL, 5, 7, '2017-10-25 09:21:32', 'Jeam', NULL, NULL, '1508948492'),
(108, 42, 514, '', 3, 30, 'mg', 5, 5, '2017-10-26 06:55:49', 'Jeam', '2017-11-04 05:42:37', 'Jeam', '1509026149'),
(109, 0, 116, '', 1, 10, NULL, 5, 3, '2017-10-30 13:14:02', 'Jeam', NULL, NULL, '1509394442'),
(110, 0, 44, '', 2, 2, NULL, 2, 2, '2017-10-31 13:51:24', 'Jeam', NULL, NULL, '1509483084'),
(111, 43, 44, '', 3, 3, NULL, 3, 3, '2017-10-31 13:52:07', 'Jeam', NULL, NULL, '1509483127'),
(112, 0, 50, 'Antibiótico compuesto, se dosifica con la amoxicilina y es vía oral', 3, 15, NULL, 5, 7, '2017-10-31 15:17:08', 'Jeam', NULL, NULL, '1509488228'),
(113, 0, 50, '', 3, 15, NULL, 5, 7, '2017-10-31 15:18:25', 'Jeam', NULL, NULL, '1509488305'),
(114, 0, 206, '', 1, 50000, NULL, 5, 3, '2017-10-31 15:19:40', 'Jeam', NULL, NULL, '1509488380'),
(115, 0, 206, 'Antibiotico de vía I. M.', 1, 50000, NULL, 5, 3, '2017-10-31 15:28:37', 'Jeam', NULL, NULL, '1509488917'),
(116, 0, 206, 'Antibiótico de vía I.M. ', 1, 50000, NULL, 5, 3, '2017-10-31 16:09:53', 'Jeam', NULL, NULL, '1509491393'),
(117, 0, 206, '', 1, 50000, NULL, 5, 3, '2017-10-31 16:11:49', 'Jeam', NULL, NULL, '1509491509'),
(127, 0, 48, '2', 2, 2, NULL, 2, 2, '2017-11-02 10:45:10', 'Jeam', NULL, NULL, '1509644710'),
(119, 46, 44, 'das', 3, 45, NULL, 2, 5, '2017-11-01 18:14:19', 'Jeam', NULL, NULL, '1509585259'),
(120, 0, 46, '', 3, 1, NULL, 2, 2, '2017-11-02 08:24:29', 'Jeam', NULL, NULL, '1509636269'),
(121, 0, 47, '', 5, 3, NULL, 4, 3, '2017-11-02 08:25:33', 'Jeam', NULL, NULL, '1509636333'),
(122, 0, 206, '-', 1, 50000, NULL, 5, 3, '2017-11-02 08:28:27', 'Jeam', NULL, NULL, '1509636507'),
(123, 0, 103, '-', 5, 5, NULL, 5, 7, '2017-11-02 08:55:17', 'Jeam', NULL, NULL, '1509638117'),
(126, 0, 45, '1', 1, 1, NULL, 1, 1, '2017-11-02 10:39:03', 'Jeam', NULL, NULL, '1509644343'),
(125, 45, 45, 'aaaaa', 2, 3, NULL, 4, 1, '2017-11-02 09:28:47', 'Jeam', '2017-11-02 10:37:29', 'Jeam', '1509640127'),
(128, 0, 44, '23', 32, 23, NULL, 23, 23, '2017-11-02 10:53:04', 'Jeam', NULL, NULL, '1509645184'),
(129, 0, 44, '12', 12, 12, NULL, 12, 12, '2017-11-02 10:54:49', 'Jeam', NULL, NULL, '1509645289'),
(130, 0, 44, '2', 2, 2, NULL, 2, 2, '2017-11-02 10:56:23', 'Jeam', NULL, NULL, '1509645383'),
(131, 0, 47, '2', 2, 2, NULL, 2, 2, '2017-11-02 10:57:10', 'Jeam', NULL, NULL, '1509645430'),
(132, 0, 47, '2', 2, 2, NULL, 2, 2, '2017-11-02 10:58:46', 'Jeam', NULL, NULL, '1509645526'),
(133, 0, 48, '2', 2, 2, NULL, 2, 2, '2017-11-02 10:59:16', 'Jeam', NULL, NULL, '1509645556'),
(134, 0, 46, '2', 2, 2, NULL, 2, 2, '2017-11-02 11:00:04', 'Jeam', NULL, NULL, '1509645604'),
(135, 0, 45, '2', 2, 2, NULL, 2, 2, '2017-11-02 11:01:35', 'Jeam', NULL, NULL, '1509645695'),
(136, 0, 48, '3', 2, 2, 'ml', 3, 2, '2017-11-02 11:22:39', 'Jeam', NULL, NULL, '1509646959'),
(137, 0, 924, '334', 23, 23, 'gr', 34, 23, '2017-11-02 11:25:38', 'Jeam', NULL, NULL, '1509647138'),
(138, 0, 921, '2', 2, 2, '2', 2, 2, '2017-11-02 11:30:53', 'Jeam', NULL, NULL, '1509647453'),
(139, 45, 44, '2', 2, 2, 'fedf', 2, 2, '2017-11-02 11:40:07', 'Jeam', '2017-11-02 11:56:22', 'Jeam', '1509648007'),
(140, 0, 924, '234', 12, 23, 'sdf', 24, 23, '2017-11-02 11:56:41', 'Jeam', '2017-11-02 11:56:51', 'Jeam', '1509649001'),
(141, 44, 206, '-', 1, 50000, 'U.I.', 5, 3, '2017-11-02 14:01:14', 'Jeam', NULL, NULL, '1509656474'),
(142, 0, 179, '-', 1, 17, 'MG.', 5, 1, '2017-11-02 14:02:58', 'Jeam', NULL, NULL, '1509656578'),
(143, 44, 179, '-', 1, 17, 'MG.', 2, 3, '2017-11-02 15:33:26', 'Jeam', NULL, NULL, '1509662006'),
(144, 0, 260, '-', 1, 20, 'MG.', 2, 3, '2017-11-03 15:45:31', 'Jeam', NULL, NULL, '1509749131'),
(145, 44, 260, '-', 1, 20, 'MG.', 2, 3, '2017-11-03 15:46:52', 'Jeam', NULL, NULL, '1509749212'),
(146, 47, 417, 'La administración es tópica pequeños toques en la cavidad oral.', 3, 2, 'pinceladas', 30, 5, '2017-11-04 06:46:53', 'Jeam', NULL, NULL, '1509803213'),
(147, 47, 70, '', 3, 10, 'mg', 5, 3, '2017-11-04 06:48:11', 'Jeam', NULL, NULL, '1509803291'),
(148, 47, 183, '', 1, 50, 'MG', 3.5, 1, '2017-11-04 07:26:48', 'Jeam', NULL, NULL, '1509805608'),
(149, 48, 417, 'Se determina con placas blanca en la cavidad oral.', 3, 2, 'pinceladas', 30, 5, '2017-11-04 07:37:42', 'Jeam', NULL, NULL, '1509806262'),
(150, 48, 581, '', 2, 10, 'mg', 5, 30, '2017-11-04 07:42:34', 'Jeam', NULL, NULL, '1509806554'),
(151, 48, 874, '', 2, 22, 'mg', 1, 30, '2017-11-04 07:43:48', 'Jeam', NULL, NULL, '1509806628'),
(152, 48, 70, '', 3, 10, 'mg', 5, 3, '2017-11-04 07:44:30', 'Jeam', NULL, NULL, '1509806670'),
(153, 49, 507, '', 1, 10, 'ML', 5, 2, '2017-11-04 08:05:41', 'Jeam', NULL, NULL, '1509807941'),
(154, 49, 861, '', 2, 1, 'UNIDAD', 1, 10, '2017-11-04 08:06:28', 'Jeam', NULL, NULL, '1509807988'),
(155, 50, 902, 'Administracion topica dermica.', 2, 1, '', 1, 15, '2017-11-04 08:37:42', 'Jeam', NULL, NULL, '1509809862'),
(156, 51, 422, '', 1, 10, 'mg', 1, 30, '2017-11-04 08:57:56', 'Jeam', NULL, NULL, '1509811076'),
(157, 51, 420, '', 1, 1, 'mg', 1, 30, '2017-11-04 08:59:50', 'Jeam', NULL, NULL, '1509811190'),
(158, 52, 679, '', 3, 1, 'PORCION', 1, 7, '2017-11-04 09:23:55', 'Jeam', NULL, NULL, '1509812635'),
(159, 52, 61, '', 3, 10, 'MG', 1, 5, '2017-11-04 09:25:11', 'Jeam', NULL, NULL, '1509812711'),
(160, 52, 410, '', 1, 3, 'MG', 2, 3, '2017-11-04 09:28:08', 'Jeam', NULL, NULL, '1509812888'),
(161, 54, 133, '', 1, 0.5, 'mg', 1, 30, '2017-11-04 10:18:01', 'Jeam', NULL, NULL, '1509815881'),
(162, 55, 251, '', 1, 0.33, 'mg', 1, 7, '2017-11-04 10:50:11', 'Jeam', NULL, NULL, '1509817811'),
(163, 55, 923, '', 1, 0.44, 'mg', 1, 7, '2017-11-04 10:50:56', 'Jeam', NULL, NULL, '1509817856'),
(164, 56, 141, 'La administracion es por via oral.', 2, 11, 'MG', 1, 3, '2017-11-06 05:52:13', 'Jeam', NULL, NULL, '1509972733'),
(165, 56, 124, 'La administracion es dosis unica solo de 250 mg.', 1, 20, 'MG', 3.5, 1, '2017-11-06 05:56:29', 'Jeam', NULL, NULL, '1509972989'),
(166, 56, 319, 'La administración es por vía oral.', 4, 11, 'MG', 1, 15, '2017-11-06 05:59:28', 'Jeam', NULL, NULL, '1509973168'),
(167, 57, 124, 'L a administracion es solo la dosis unica de 250 mg.', 1, 20, 'MG.', 3.5, 1, '2017-11-06 06:06:15', 'Jeam', NULL, NULL, '1509973575'),
(168, 57, 141, 'La administración es por vía oral en una dosis única de 500 mg.', 1, 11, 'MG', 1, 1, '2017-11-06 06:08:02', 'Jeam', NULL, NULL, '1509973682'),
(169, 57, 319, 'La administracion es por via oral de 500 mg dosis unica.', 1, 11, 'MG', 1, 1, '2017-11-06 06:09:33', 'Jeam', NULL, NULL, '1509973773'),
(170, 58, 824, 'La administración es por vía oral.', 3, 8.8, 'MG', 1, 10, '2017-11-06 06:27:17', 'Jeam', NULL, NULL, '1509974837'),
(171, 59, 132, 'La administración es tópica.', 14, 1, '', 1, 2, '2017-11-06 06:33:43', 'Jeam', NULL, NULL, '1509975223'),
(172, 0, 240, 'La administración es por vía oral.', 3, 10, 'MG', 1, 3, '2017-11-06 06:40:39', 'Jeam', NULL, NULL, '1509975639'),
(173, 0, 141, 'La administración es por vía oral como dosis única.', 1, 11, 'MG', 1, 1, '2017-11-06 06:42:20', 'Jeam', '2017-11-06 06:43:49', 'Jeam', '1509975740'),
(174, 60, 240, 'La administración es por vía oral.', 3, 10, 'MG', 1, 3, '2017-11-06 06:46:46', 'Jeam', NULL, NULL, '1509976006'),
(175, 60, 141, 'La administración es por va oral dosis única de 500 mg.', 1, 10, 'mg', 1, 1, '2017-11-06 06:47:35', 'Jeam', NULL, NULL, '1509976055'),
(176, 60, 124, 'La administración es por I.M con una dosis única de 250 mg.', 1, 20, 'MG', 3.5, 1, '2017-11-06 06:48:42', 'Jeam', NULL, NULL, '1509976122'),
(177, 61, 103, 'La administración es por vía oral.', 2, 5, 'mg', 5, 7, '2017-11-06 06:54:15', 'Jeam', NULL, NULL, '1509976455'),
(178, 61, 195, 'La administración es por vía oral.', 2, 5, ' mg', 5, 7, '2017-11-06 06:55:22', 'Jeam', NULL, NULL, '1509976522'),
(179, 0, 340, 'La administración es por vía oral.', 4, 1.5, 'mg', 5, 6, '2017-11-06 06:56:40', 'Jeam', NULL, NULL, '1509976600'),
(180, 62, 169, 'La administración es por vía oral, como dosis unica.', 2, 2.2, 'MG', 5, 1, '2017-11-06 09:54:45', 'Jeam', NULL, NULL, '1509987285'),
(181, 63, 266, 'La administración es vía oral.', 1, 0.22, 'mg', 5, 5, '2017-11-06 10:05:45', 'Jeam', NULL, NULL, '1509987945'),
(182, 63, 116, 'La administración es vía oral.', 3, 9, 'mg', 5, 3, '2017-11-06 10:07:11', 'Jeam', NULL, NULL, '1509988031'),
(183, 64, 476, 'La administración es vía endovenosa.', 2, 0.8, 'MG', 1, 3, '2017-11-10 06:18:44', 'Jeam', NULL, NULL, '1510319924'),
(184, 64, 179, 'La administración es vía endovenosa.', 2, 20, 'MG', 2, 3, '2017-11-10 06:20:46', 'Jeam', NULL, NULL, '1510320046'),
(185, 64, 840, 'La administración es vía endovenosa.', 2, 0.4, 'MG', 2, 3, '2017-11-10 06:22:31', 'Jeam', NULL, NULL, '1510320151'),
(186, 64, 880, 'La administración es vía oral.', 2, 1, 'TAB.', 1, 30, '2017-11-10 06:24:02', 'Jeam', NULL, NULL, '1510320242'),
(187, 64, 252, 'La administración es por vía oral.', 3, 1, 'TAB.', 1, 3, '2017-11-10 06:25:07', 'Jeam', NULL, NULL, '1510320307'),
(188, 0, 296, 'La administración es por vía oral dosis única de 1 gramo.', 1, 15, 'MG', 1, 1, '2017-11-10 06:34:12', 'Jeam', '2017-11-10 06:34:55', 'Jeam', '1510320852'),
(189, 65, 296, 'La administración es por vía oral dosis única de 1 gramo.', 1, 15, 'MG', 1, 1, '2017-11-10 06:35:48', 'Jeam', NULL, NULL, '1510320948'),
(190, 65, 410, 'La administracion es por IM.', 2, 3.5, 'MG.', 2, 3, '2017-11-10 06:37:21', 'Jeam', NULL, NULL, '1510321041'),
(191, 65, 141, 'La administración es por vía oral.', 2, 20, 'MG', 1, 3, '2017-11-10 06:38:24', 'Jeam', NULL, NULL, '1510321104'),
(192, 65, 124, 'La administración es por vía I.M.', 3, 20, 'MG', 3.5, 1, '2017-11-10 06:39:47', 'Jeam', NULL, NULL, '1510321187'),
(193, 66, 190, 'La administración es por vía endovenosa.', 3, 10, 'MG', 4, 3, '2017-11-10 07:09:24', 'Jeam', NULL, NULL, '1510322964'),
(194, 66, 61, 'La administración es por vía oral.', 3, 10, 'MG', 1, 5, '2017-11-10 07:10:15', 'Jeam', NULL, NULL, '1510323015'),
(195, 66, 166, 'La administración es por vía endovenosa.', 3, 30, 'MG', 3.5, 3, '2017-11-10 07:13:08', 'Jeam', NULL, NULL, '1510323188'),
(196, 67, 61, 'La administración es por vía oral.', 3, 10, 'MG', 1, 5, '2017-11-10 07:29:21', 'Jeam', NULL, NULL, '1510324161'),
(197, 67, 87, 'La administración es por vía oral.', 1, 10, 'MG', 1, 3, '2017-11-10 07:31:36', 'Jeam', NULL, NULL, '1510324296'),
(198, 67, 107, 'L a administración es por vía oral.', 3, 15, 'MG', 1, 5, '2017-11-10 07:32:44', 'Jeam', NULL, NULL, '1510324364'),
(199, 68, 696, 'La administración es por vía oral.', 3, 5, 'ML', 100, 30, '2017-11-10 07:59:49', 'Jeam', NULL, NULL, '1510325989'),
(200, 69, 183, 'La administración es vía I.M. como dosis única.', 1, 50000, 'MG', 5, 1, '2017-11-10 08:15:27', 'Jeam', NULL, NULL, '1510326927'),
(201, 69, 514, 'La administracion es por via oral.', 4, 7.5, 'MG', 5, 3, '2017-11-10 08:16:42', 'Jeam', NULL, NULL, '1510327002'),
(202, 69, 50, 'La administración es por vía oral.', 3, 10, 'MG', 5, 5, '2017-11-10 08:18:09', 'Jeam', NULL, NULL, '1510327089'),
(203, 70, 81, 'La administración es por vía oral.', 2, 10, 'MG', 5, 3, '2017-11-10 08:24:03', 'Jeam', NULL, NULL, '1510327443'),
(204, 70, 634, 'La administracion e spor via oral.', 3, 10, 'MG', 5, 3, '2017-11-10 08:25:26', 'Jeam', NULL, NULL, '1510327526'),
(205, 71, 54, 'La administración es tópica.', 2, 1, '', 42, 15, '2017-11-10 08:41:31', 'Jeam', NULL, NULL, '1510328491'),
(206, 0, 54, 'La administración es tópica.', 2, 1, '', 42, 15, '2017-11-10 08:41:31', 'Jeam', NULL, NULL, '1510328491'),
(207, 71, 835, 'La administración es vía oral.', 1, 2.5, 'MG', 5, 3, '2017-11-10 08:49:35', 'Jeam', NULL, NULL, '1510328975'),
(208, 0, 604, 'La administración es tópica.', 2, 1, 'mg', 20, 15, '2017-11-10 09:00:32', 'Jeam', NULL, NULL, '1510329632'),
(209, 72, 132, 'La administración es tópica.', 2, 1, 'mg', 20, 15, '2017-11-10 09:02:09', 'Jeam', NULL, NULL, '1510329729'),
(210, 0, 162, 'La administración es vía oral.', 2, 10, 'mg', 5, 5, '2017-11-10 09:03:37', 'Jeam', NULL, NULL, '1510329817'),
(211, 72, 691, 'La administración es vía oral.', 2, 10, 'mg', 5, 5, '2017-11-10 09:04:27', 'Jeam', NULL, NULL, '1510329867'),
(212, 73, 70, 'La administración es vía oral.', 3, 10, 'mg', 5, 3, '2017-11-10 09:12:50', 'Jeam', NULL, NULL, '1510330370'),
(213, 74, 296, 'La administracion via oral con una dosis unica de 1 g.', 1, 20, 'mg', 1, 1, '2017-11-10 09:28:10', 'Jeam', NULL, NULL, '1510331290'),
(214, 74, 820, 'La administración es intra vaginal.', 1, 100, 'mg', 1, 7, '2017-11-10 09:31:24', 'Jeam', NULL, NULL, '1510331484'),
(215, 75, 577, 'La administración es intra vaginal.', 1, 1, 'ovulo', 1, 7, '2017-11-10 09:42:00', 'Jeam', NULL, NULL, '1510332120'),
(216, 75, 375, 'La administracion es oral 1 capsula semanal por tres meses.', 1, 1, 'cap.', 1, 4, '2017-11-10 09:44:29', 'Jeam', NULL, NULL, '1510332269'),
(217, 76, 319, 'La administración es por vía oral.', 4, 10, 'mg', 1, 21, '2017-11-10 11:33:56', 'Jeam', NULL, NULL, '1510338836'),
(218, 76, 141, 'La administracion es por via oral.', 2, 15, 'mg', 1, 21, '2017-11-10 11:34:38', 'Jeam', NULL, NULL, '1510338878'),
(219, 76, 101, 'La administracion es por via oral.', 2, 16, 'mg', 1, 21, '2017-11-10 11:36:28', 'Jeam', NULL, NULL, '1510338988'),
(220, 77, 760, 'La administracion es topica.', 1, 1, 'aplicacion.', 60, 3, '2017-11-10 11:44:40', 'Jeam', NULL, NULL, '1510339480'),
(221, 78, 108, 'La administracion es por via oral previa preparacion.', 3, 15, 'mg', 5, 7, '2017-11-10 12:04:42', 'Jeam', NULL, NULL, '1510340682'),
(222, 78, 117, 'La administracion es por via oral.', 3, 15, 'mg', 5, 7, '2017-11-10 12:05:34', 'Jeam', NULL, NULL, '1510340734'),
(223, 78, 103, 'La administracion es por via oral.', 2, 5, 'mg', 5, 7, '2017-11-10 12:06:15', 'Jeam', NULL, NULL, '1510340775'),
(224, 78, 50, 'La administracion es via oral.', 3, 15, 'mg', 5, 7, '2017-11-10 12:07:25', 'Jeam', NULL, NULL, '1510340845'),
(225, 78, 206, 'La administracion es via intramuscular,', 1, 50000, 'mg', 5, 3, '2017-11-10 12:08:23', 'Jeam', NULL, NULL, '1510340903'),
(226, 78, 179, 'La administracion por via intramuscular.', 1, 20, 'mg', 2, 1, '2017-11-10 12:09:36', 'Jeam', NULL, NULL, '1510340976'),
(227, 78, 634, 'La administracion es por via oral.', 3, 10, 'mg', 4, 3, '2017-11-10 12:11:00', 'Jeam', NULL, NULL, '1510341060'),
(228, 79, 888, 'La administracion es inhalatoria.', 3, 2, 'inhalaciones por dosis.', 200, 3, '2017-11-10 13:00:57', 'Jeam', NULL, NULL, '1510344057'),
(229, 79, 904, 'La administracion es por via oral.', 3, 0.15, 'mg', 5, 3, '2017-11-10 13:04:23', 'Jeam', NULL, NULL, '1510344263'),
(230, 79, 302, 'La administracion es por via oral.', 2, 0.6, 'MG', 5, 3, '2017-11-10 13:05:26', 'Jeam', NULL, NULL, '1510344326'),
(231, 80, 302, 'La administracion por via oral.', 2, 0.6, 'mg', 5, 3, '2017-11-10 13:34:38', 'Jeam', NULL, NULL, '1510346078'),
(232, 81, 454, 'La administracion es por via oral con frecuencia como rehidratante.', 12, 75, 'ml', 60, 5, '2017-11-10 13:48:58', 'Jeam', NULL, NULL, '1510346938'),
(233, 61, 291, 'La administracion es via oral.', 4, 1.5, 'mg', 5, 6, '2017-11-11 05:38:23', 'Jeam', NULL, NULL, '1510403903'),
(234, 82, 50, 'La administracion es por via oral.', 3, 10, 'mg', 5, 7, '2017-11-11 06:28:39', 'Jeam', NULL, NULL, '1510406919'),
(235, 82, 159, 'La administracion es via oral.', 3, 10, 'mg', 5, 5, '2017-11-11 06:33:36', 'Jeam', NULL, NULL, '1510407216'),
(236, 82, 412, 'La administracion es via intra muscular.', 1, 5, 'MG', 2, 3, '2017-11-11 06:34:22', 'Jeam', NULL, NULL, '1510407262'),
(237, 82, 92, 'La administracion es via intramuscular.', 1, 1.5, 'mg', 2, 3, '2017-11-11 06:35:33', 'Jeam', NULL, NULL, '1510407333'),
(238, 83, 64, 'La administaracion es via oral 1 semanal por 1 mes.', 1, 10, 'mg', 1, 4, '2017-11-11 11:07:34', 'Jeam', NULL, NULL, '1510423654'),
(239, 83, 765, 'LA administracion es via oral y intravaginal.', 1, 1, 'ovulo', 1, 7, '2017-11-11 11:08:41', 'Jeam', NULL, NULL, '1510423721'),
(240, 83, 127, 'La administracion es por via oral.', 1, 1, 'capsula', 1, 30, '2017-11-11 11:09:26', 'Jeam', NULL, NULL, '1510423766'),
(241, 83, 540, 'La administracion es via oral.', 1, 1, 'capsula', 1, 15, '2017-11-11 11:10:13', 'Jeam', NULL, NULL, '1510423813'),
(242, 83, 907, 'La administracion es por via oral.', 1, 1, 'tableta.', 1, 12, '2017-11-11 11:11:07', 'Jeam', NULL, NULL, '1510423867'),
(243, 84, 293, 'La administracion es via oral de rutina de 21 dias mas 7 dias.', 1, 1, 'gragea', 1, 21, '2017-11-11 11:50:18', 'Jeam', NULL, NULL, '1510426218'),
(244, 85, 330, 'La administracion es via oral.', 1, 1, 'gragea', 1, 1, '2017-11-11 12:02:33', 'Jeam', NULL, NULL, '1510426953'),
(245, 85, 894, 'La administracion es via oral tomar las dos grageas juntas.', 1, 2, 'gragea.', 1, 2, '2017-11-11 12:07:09', 'Jeam', NULL, NULL, '1510427229'),
(246, 0, 191, 'La administracion es via intramuslar.', 1, 1, 'ML', 1, 1, '2017-11-11 12:26:24', 'Jeam', NULL, NULL, '1510428384'),
(247, 0, 339, 'La administracion es via intramuscular profunda intervalod e 28 dias.', 1, 1, 'ML', 1, 1, '2017-11-11 12:32:11', 'Jeam', NULL, NULL, '1510428731'),
(248, 0, 584, 'La administracion es via intramuscular profunda.', 1, 1, 'ML', 1, 1, '2017-11-11 12:33:45', 'Jeam', NULL, NULL, '1510428825'),
(249, 0, 612, 'La administarcion es via intramuscular profunda.', 1, 2, 'ml', 1, 1, '2017-11-11 12:34:28', 'Jeam', NULL, NULL, '1510428868'),
(250, 0, 613, 'La administracion es via intramuscular profunda cada 3 meses.', 1, 2, 'ML', 2, 1, '2017-11-11 12:43:27', 'Jeam', NULL, NULL, '1510429407'),
(251, 0, 613, 'La administracion es via intramuscular profunda con el inicio de los primeros 5 dias de la mestruacion y intervalos de 84 dias.', 1, 2, 'ml', 2, 1, '2017-11-11 13:12:29', 'Jeam', NULL, NULL, '1510431149');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_PreOrden`
--

CREATE TABLE `Ve_PreOrden` (
  `IdPreOrden` int(11) NOT NULL,
  `IdCliente` int(11) DEFAULT NULL,
  `FechaReg` datetime DEFAULT NULL,
  `UsuarioReg` varchar(255) DEFAULT NULL,
  `FechaMod` datetime DEFAULT NULL,
  `UsuarioMod` varchar(255) DEFAULT NULL,
  `Estado` varchar(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_PreOrden`
--

INSERT INTO `Ve_PreOrden` (`IdPreOrden`, `IdCliente`, `FechaReg`, `UsuarioReg`, `FechaMod`, `UsuarioMod`, `Estado`) VALUES
(1, 39, '2018-02-23 09:49:01', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ve_PreOrdenDet`
--

CREATE TABLE `Ve_PreOrdenDet` (
  `IdPreOrden` int(11) DEFAULT NULL,
  `IdProducto` int(11) DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Ve_PreOrdenDet`
--

INSERT INTO `Ve_PreOrdenDet` (`IdPreOrden`, `IdProducto`, `Cantidad`) VALUES
(1, 3156, 2),
(1, 3159, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Cb_CajaBanco`
--
ALTER TABLE `Cb_CajaBanco`
  ADD PRIMARY KEY (`IdCajaBanco`),
  ADD KEY `fk_2` (`IdCuenta`),
  ADD KEY `fk_TipoBanco` (`IdTipoCajaBanco`);

--
-- Indices de la tabla `Cb_CajaBancoDet`
--
ALTER TABLE `Cb_CajaBancoDet`
  ADD PRIMARY KEY (`IdDocDet`,`IdCajaBanco`),
  ADD KEY `fk1` (`IdCajaBanco`);

--
-- Indices de la tabla `Cb_Cuenta`
--
ALTER TABLE `Cb_Cuenta`
  ADD PRIMARY KEY (`IdCuenta`);

--
-- Indices de la tabla `Cb_TipoCajaBanco`
--
ALTER TABLE `Cb_TipoCajaBanco`
  ADD PRIMARY KEY (`IdTipoCajaBanco`);

--
-- Indices de la tabla `Gen_Producto`
--
ALTER TABLE `Gen_Producto`
  ADD PRIMARY KEY (`IdProducto`,`IdProductoMarca`,`IdProductoFormaFarmaceutica`,`IdProductoMedicion`,`IdProductoCategoria`),
  ADD UNIQUE KEY `Producto_UNIQUE` (`Producto`),
  ADD KEY `fk_Producto_ProductoMarca1_idx` (`IdProductoMarca`),
  ADD KEY `fk_Producto_ProductoFormaFarmaceutica1_idx` (`IdProductoFormaFarmaceutica`),
  ADD KEY `fk_Producto_ProductoMedicion1_idx` (`IdProductoMedicion`),
  ADD KEY `fk_Producto_ProductoCategoria1_idx` (`IdProductoCategoria`);

--
-- Indices de la tabla `Gen_ProductoBloque`
--
ALTER TABLE `Gen_ProductoBloque`
  ADD PRIMARY KEY (`IdBloque`),
  ADD UNIQUE KEY `BLOQUE_UNIQUE` (`Bloque`) USING BTREE;

--
-- Indices de la tabla `Gen_ProductoCategoria`
--
ALTER TABLE `Gen_ProductoCategoria`
  ADD PRIMARY KEY (`IdProductoCategoria`),
  ADD UNIQUE KEY `ProductoCategoria_UNIQUE` (`ProductoCategoria`);

--
-- Indices de la tabla `Gen_ProductoCompuesto`
--
ALTER TABLE `Gen_ProductoCompuesto`
  ADD PRIMARY KEY (`IdProductoCompuesto`),
  ADD UNIQUE KEY `ProductoCompuesto_UNIQUE` (`ProductoCompuesto`);

--
-- Indices de la tabla `Gen_ProductoCompuestoDet`
--
ALTER TABLE `Gen_ProductoCompuestoDet`
  ADD PRIMARY KEY (`Gen_Producto_IdProducto`,`Gen_ProductoCompuesto_IdProductoCompuesto`),
  ADD KEY `fk_Gen_Producto_has_Gen_ProductoCompuesto_Gen_ProductoCompu_idx` (`Gen_ProductoCompuesto_IdProductoCompuesto`),
  ADD KEY `fk_Gen_Producto_has_Gen_ProductoCompuesto_Gen_Producto1_idx` (`Gen_Producto_IdProducto`);

--
-- Indices de la tabla `Gen_ProductoDet`
--
ALTER TABLE `Gen_ProductoDet`
  ADD PRIMARY KEY (`IdProducto`,`IdProductoDet`);

--
-- Indices de la tabla `Gen_ProductoFormaFarmaceutica`
--
ALTER TABLE `Gen_ProductoFormaFarmaceutica`
  ADD PRIMARY KEY (`IdProductoFormaFarmaceutica`);

--
-- Indices de la tabla `Gen_ProductoMarca`
--
ALTER TABLE `Gen_ProductoMarca`
  ADD PRIMARY KEY (`IdProductoMarca`),
  ADD UNIQUE KEY `ProductoMarca_UNIQUE` (`ProductoMarca`);

--
-- Indices de la tabla `Gen_ProductoMedicion`
--
ALTER TABLE `Gen_ProductoMedicion`
  ADD PRIMARY KEY (`IdProductoMedicion`);

--
-- Indices de la tabla `Lo_Almacen`
--
ALTER TABLE `Lo_Almacen`
  ADD PRIMARY KEY (`IdAlmacen`),
  ADD UNIQUE KEY `IdAlmacen_UNIQUE` (`IdAlmacen`),
  ADD UNIQUE KEY `Almacen_UNIQUE` (`Almacen`);

--
-- Indices de la tabla `Lo_Impuesto`
--
ALTER TABLE `Lo_Impuesto`
  ADD PRIMARY KEY (`IdImpuesto`),
  ADD UNIQUE KEY `UNIQUE` (`Impuesto`) USING BTREE;

--
-- Indices de la tabla `Lo_Movimiento`
--
ALTER TABLE `Lo_Movimiento`
  ADD PRIMARY KEY (`IdMovimientoTipo`,`IdProveedor`,`Serie`,`Numero`),
  ADD UNIQUE KEY `unique` (`Hash`) USING BTREE;

--
-- Indices de la tabla `Lo_MovimientoDetalle`
--
ALTER TABLE `Lo_MovimientoDetalle`
  ADD PRIMARY KEY (`hashMovimiento`,`IdProducto`,`Precio`);

--
-- Indices de la tabla `Lo_MovimientoTipo`
--
ALTER TABLE `Lo_MovimientoTipo`
  ADD PRIMARY KEY (`IdMovimientoTipo`),
  ADD UNIQUE KEY `TipoMovimiento2` (`TipoMovimiento`) USING BTREE;

--
-- Indices de la tabla `Lo_Proveedor`
--
ALTER TABLE `Lo_Proveedor`
  ADD PRIMARY KEY (`IdProveedor`),
  ADD UNIQUE KEY `UNIQUE` (`Proveedor`) USING BTREE,
  ADD UNIQUE KEY `UNIQUE2` (`Ruc`) USING BTREE;

--
-- Indices de la tabla `prodstock`
--
ALTER TABLE `prodstock`
  ADD PRIMARY KEY (`IdProducto`);

--
-- Indices de la tabla `Seg_Usuario`
--
ALTER TABLE `Seg_Usuario`
  ADD PRIMARY KEY (`Usuario`,`IdUsuarioPerfil`),
  ADD KEY `fk_Usuario_UsuarioPerfil1_idx` (`IdUsuarioPerfil`);

--
-- Indices de la tabla `Seg_UsuarioModulo`
--
ALTER TABLE `Seg_UsuarioModulo`
  ADD PRIMARY KEY (`IdUsuarioModulo`),
  ADD UNIQUE KEY `UsuarioModulo_UNIQUE` (`UsuarioModulo`);

--
-- Indices de la tabla `Seg_UsuarioModulo_has_UsuarioPerfil`
--
ALTER TABLE `Seg_UsuarioModulo_has_UsuarioPerfil`
  ADD PRIMARY KEY (`IdUsuarioModulo`,`IdUsuarioPerfil`),
  ADD KEY `fk_UsuarioModulo_has_UsuarioPerfil_UsuarioPerfil1_idx` (`IdUsuarioPerfil`),
  ADD KEY `fk_UsuarioModulo_has_UsuarioPerfil_UsuarioModulo1_idx` (`IdUsuarioModulo`);

--
-- Indices de la tabla `Seg_UsuarioPerfil`
--
ALTER TABLE `Seg_UsuarioPerfil`
  ADD PRIMARY KEY (`IdUsuarioPerfil`),
  ADD UNIQUE KEY `UsuarioPerfil_UNIQUE` (`UsuarioPerfil`);

--
-- Indices de la tabla `Ve_DocVenta`
--
ALTER TABLE `Ve_DocVenta`
  ADD PRIMARY KEY (`idDocVenta`),
  ADD UNIQUE KEY `idDocVenta_UNIQUE` (`idDocVenta`),
  ADD KEY `fk_Ve_DocVenta_Ve_DocVentaPuntoVenta_idx` (`IdDocVentaPuntoVenta`),
  ADD KEY `fk_Ve_DocVenta_Ve_DocVentaCliente1_idx` (`IdCliente`),
  ADD KEY `fk_Ve_DocVenta_Ve_DocVentaTipoDoc1_idx` (`IdTipoDoc`),
  ADD KEY `fk_Ve_DocVenta_Lo_Almacen1_idx` (`IdAlmacen`);

--
-- Indices de la tabla `Ve_DocVentaCliente`
--
ALTER TABLE `Ve_DocVentaCliente`
  ADD PRIMARY KEY (`IdCliente`),
  ADD UNIQUE KEY `IdCliente_UNIQUE` (`IdCliente`),
  ADD UNIQUE KEY `CLIENTE_UNIQUE` (`Cliente`),
  ADD UNIQUE KEY `DNIRUC_UNIQUE` (`DniRuc`);

--
-- Indices de la tabla `Ve_DocVentaDet`
--
ALTER TABLE `Ve_DocVentaDet`
  ADD PRIMARY KEY (`IdDocVentaDet`),
  ADD KEY `fk_Ve_DocVentaDet_Ve_DocVenta1_idx` (`IdDocVenta`),
  ADD KEY `fk_Ve_DocVentaDet_Gen_Producto_idx` (`IdProducto`);

--
-- Indices de la tabla `Ve_DocVentaMetodoPago`
--
ALTER TABLE `Ve_DocVentaMetodoPago`
  ADD PRIMARY KEY (`IdMetodoPago`),
  ADD UNIQUE KEY `MetodoPago_UNIQUE` (`MetodoPago`);

--
-- Indices de la tabla `Ve_DocVentaPuntoVenta`
--
ALTER TABLE `Ve_DocVentaPuntoVenta`
  ADD PRIMARY KEY (`IdDocVentaPuntoVenta`),
  ADD UNIQUE KEY `IdDocVentaPuntoVenta_UNIQUE` (`IdDocVentaPuntoVenta`);

--
-- Indices de la tabla `Ve_DocVentaPuntoVentaDet`
--
ALTER TABLE `Ve_DocVentaPuntoVentaDet`
  ADD PRIMARY KEY (`IdDocVentaPuntoVenta`,`IdDocVentaTipoDoc`);

--
-- Indices de la tabla `Ve_DocVentaTipoDoc`
--
ALTER TABLE `Ve_DocVentaTipoDoc`
  ADD PRIMARY KEY (`IdTipoDoc`),
  ADD UNIQUE KEY `TipoDoc_UNIQUE` (`TipoDoc`);

--
-- Indices de la tabla `Ve_ExpertoDiagnostico`
--
ALTER TABLE `Ve_ExpertoDiagnostico`
  ADD PRIMARY KEY (`IdDiagnostico`),
  ADD UNIQUE KEY `ENFERMEDAD_UNIQUE` (`Diagnostico`) USING BTREE;

--
-- Indices de la tabla `Ve_ExpertoDiagnosticoSintomaDet`
--
ALTER TABLE `Ve_ExpertoDiagnosticoSintomaDet`
  ADD PRIMARY KEY (`IdDiagnostico`,`IdSintoma`),
  ADD KEY `Sintomas` (`IdSintoma`);

--
-- Indices de la tabla `Ve_ExpertoSintoma`
--
ALTER TABLE `Ve_ExpertoSintoma`
  ADD PRIMARY KEY (`IdSintoma`),
  ADD UNIQUE KEY `SINTOMA_UNIQUE` (`Sintoma`) USING BTREE;

--
-- Indices de la tabla `Ve_ExpertoTratamiento`
--
ALTER TABLE `Ve_ExpertoTratamiento`
  ADD PRIMARY KEY (`IdTratamiento`);

--
-- Indices de la tabla `Ve_PreOrden`
--
ALTER TABLE `Ve_PreOrden`
  ADD PRIMARY KEY (`IdPreOrden`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Cb_CajaBanco`
--
ALTER TABLE `Cb_CajaBanco`
  MODIFY `IdCajaBanco` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Cb_CajaBancoDet`
--
ALTER TABLE `Cb_CajaBancoDet`
  MODIFY `IdCajaBanco` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Cb_Cuenta`
--
ALTER TABLE `Cb_Cuenta`
  MODIFY `IdCuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `Cb_TipoCajaBanco`
--
ALTER TABLE `Cb_TipoCajaBanco`
  MODIFY `IdTipoCajaBanco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `Gen_Producto`
--
ALTER TABLE `Gen_Producto`
  MODIFY `IdProducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5222;

--
-- AUTO_INCREMENT de la tabla `Gen_ProductoBloque`
--
ALTER TABLE `Gen_ProductoBloque`
  MODIFY `IdBloque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `Gen_ProductoCategoria`
--
ALTER TABLE `Gen_ProductoCategoria`
  MODIFY `IdProductoCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `Gen_ProductoCompuesto`
--
ALTER TABLE `Gen_ProductoCompuesto`
  MODIFY `IdProductoCompuesto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Gen_ProductoFormaFarmaceutica`
--
ALTER TABLE `Gen_ProductoFormaFarmaceutica`
  MODIFY `IdProductoFormaFarmaceutica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de la tabla `Gen_ProductoMarca`
--
ALTER TABLE `Gen_ProductoMarca`
  MODIFY `IdProductoMarca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=461;

--
-- AUTO_INCREMENT de la tabla `Gen_ProductoMedicion`
--
ALTER TABLE `Gen_ProductoMedicion`
  MODIFY `IdProductoMedicion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `Lo_Almacen`
--
ALTER TABLE `Lo_Almacen`
  MODIFY `IdAlmacen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `Lo_Impuesto`
--
ALTER TABLE `Lo_Impuesto`
  MODIFY `IdImpuesto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `Lo_MovimientoTipo`
--
ALTER TABLE `Lo_MovimientoTipo`
  MODIFY `IdMovimientoTipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `Lo_Proveedor`
--
ALTER TABLE `Lo_Proveedor`
  MODIFY `IdProveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `Seg_UsuarioModulo`
--
ALTER TABLE `Seg_UsuarioModulo`
  MODIFY `IdUsuarioModulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `Seg_UsuarioPerfil`
--
ALTER TABLE `Seg_UsuarioPerfil`
  MODIFY `IdUsuarioPerfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `Ve_DocVenta`
--
ALTER TABLE `Ve_DocVenta`
  MODIFY `idDocVenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT de la tabla `Ve_DocVentaCliente`
--
ALTER TABLE `Ve_DocVentaCliente`
  MODIFY `IdCliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `Ve_DocVentaDet`
--
ALTER TABLE `Ve_DocVentaDet`
  MODIFY `IdDocVentaDet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `Ve_DocVentaMetodoPago`
--
ALTER TABLE `Ve_DocVentaMetodoPago`
  MODIFY `IdMetodoPago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `Ve_DocVentaPuntoVenta`
--
ALTER TABLE `Ve_DocVentaPuntoVenta`
  MODIFY `IdDocVentaPuntoVenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `Ve_DocVentaTipoDoc`
--
ALTER TABLE `Ve_DocVentaTipoDoc`
  MODIFY `IdTipoDoc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `Ve_ExpertoDiagnostico`
--
ALTER TABLE `Ve_ExpertoDiagnostico`
  MODIFY `IdDiagnostico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT de la tabla `Ve_ExpertoSintoma`
--
ALTER TABLE `Ve_ExpertoSintoma`
  MODIFY `IdSintoma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT de la tabla `Ve_ExpertoTratamiento`
--
ALTER TABLE `Ve_ExpertoTratamiento`
  MODIFY `IdTratamiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;

--
-- AUTO_INCREMENT de la tabla `Ve_PreOrden`
--
ALTER TABLE `Ve_PreOrden`
  MODIFY `IdPreOrden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Cb_CajaBanco`
--
ALTER TABLE `Cb_CajaBanco`
  ADD CONSTRAINT `fk_2` FOREIGN KEY (`IdCuenta`) REFERENCES `Cb_Cuenta` (`IdCuenta`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_TipoBanco` FOREIGN KEY (`IdTipoCajaBanco`) REFERENCES `Cb_TipoCajaBanco` (`IdTipoCajaBanco`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Cb_CajaBancoDet`
--
ALTER TABLE `Cb_CajaBancoDet`
  ADD CONSTRAINT `fk1` FOREIGN KEY (`IdCajaBanco`) REFERENCES `Cb_CajaBanco` (`IdCajaBanco`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Gen_Producto`
--
ALTER TABLE `Gen_Producto`
  ADD CONSTRAINT `fk_Producto_ProductoCategoria1` FOREIGN KEY (`IdProductoCategoria`) REFERENCES `Gen_ProductoCategoria` (`IdProductoCategoria`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Producto_ProductoFormaFarmaceutica1` FOREIGN KEY (`IdProductoFormaFarmaceutica`) REFERENCES `Gen_ProductoFormaFarmaceutica` (`IdProductoFormaFarmaceutica`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Producto_ProductoMarca1` FOREIGN KEY (`IdProductoMarca`) REFERENCES `Gen_ProductoMarca` (`IdProductoMarca`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Producto_ProductoMedicion1` FOREIGN KEY (`IdProductoMedicion`) REFERENCES `Gen_ProductoMedicion` (`IdProductoMedicion`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Gen_ProductoCompuestoDet`
--
ALTER TABLE `Gen_ProductoCompuestoDet`
  ADD CONSTRAINT `fk_Gen_Producto_has_Gen_ProductoCompuesto_Gen_Producto1` FOREIGN KEY (`Gen_Producto_IdProducto`) REFERENCES `Gen_Producto` (`IdProducto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Gen_Producto_has_Gen_ProductoCompuesto_Gen_ProductoCompues1` FOREIGN KEY (`Gen_ProductoCompuesto_IdProductoCompuesto`) REFERENCES `Gen_ProductoCompuesto` (`IdProductoCompuesto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Seg_Usuario`
--
ALTER TABLE `Seg_Usuario`
  ADD CONSTRAINT `fk_Usuario_UsuarioPerfil1` FOREIGN KEY (`IdUsuarioPerfil`) REFERENCES `Seg_UsuarioPerfil` (`IdUsuarioPerfil`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Seg_UsuarioModulo_has_UsuarioPerfil`
--
ALTER TABLE `Seg_UsuarioModulo_has_UsuarioPerfil`
  ADD CONSTRAINT `fk_UsuarioModulo_has_UsuarioPerfil_UsuarioModulo1` FOREIGN KEY (`IdUsuarioModulo`) REFERENCES `Seg_UsuarioModulo` (`IdUsuarioModulo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_UsuarioModulo_has_UsuarioPerfil_UsuarioPerfil1` FOREIGN KEY (`IdUsuarioPerfil`) REFERENCES `Seg_UsuarioPerfil` (`IdUsuarioPerfil`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Ve_DocVenta`
--
ALTER TABLE `Ve_DocVenta`
  ADD CONSTRAINT `fk_Ve_DocVenta_Lo_Almacen1` FOREIGN KEY (`IdAlmacen`) REFERENCES `Lo_Almacen` (`IdAlmacen`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Ve_DocVenta_Ve_DocVentaCliente1` FOREIGN KEY (`IdCliente`) REFERENCES `Ve_DocVentaCliente` (`IdCliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Ve_DocVenta_Ve_DocVentaPuntoVenta` FOREIGN KEY (`IdDocVentaPuntoVenta`) REFERENCES `Ve_DocVentaPuntoVenta` (`IdDocVentaPuntoVenta`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Ve_DocVenta_Ve_DocVentaTipoDoc1` FOREIGN KEY (`IdTipoDoc`) REFERENCES `Ve_DocVentaTipoDoc` (`IdTipoDoc`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Ve_DocVentaDet`
--
ALTER TABLE `Ve_DocVentaDet`
  ADD CONSTRAINT `fk_Ve_DocVentaDet_Gen_Producto` FOREIGN KEY (`IdProducto`) REFERENCES `Gen_Producto` (`IdProducto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Ve_DocVentaDet_Ve_DocVenta1` FOREIGN KEY (`IdDocVenta`) REFERENCES `Ve_DocVenta` (`idDocVenta`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
