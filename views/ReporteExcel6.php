<?php

require_once '../controllers/Reporte/Classes/PHPExcel.php';
include_once '../clases/BnGeneral.php';

        $fechaIni = $_GET["fechaIni"];
        $fechaFin = $_GET["fechaFin"];
        $declarado = $_GET["declarado"];
$objPHPExcel = new PHPExcel();

//echo $almacen;

//propiedades del documento excel
$objPHPExcel->getProperties()
		->setCreator("Botica")
        ->setLastModifiedBy("Botica")
        ->setTitle("Excel en PHP")
        ->setSubject("Documento de prueba")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("excel phpexcel php")
        ->setCategory("Ejemplos");

//propiedades de la hoja
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Hoja 1');


// Agregar en celda A1 valor string
$objPHPExcel->getActiveSheet()->setCellValue('D3', '#');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'Fecha Doc');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'Cod Sunat');
$objPHPExcel->getActiveSheet()->setCellValue('G3', 'Tipo');
$objPHPExcel->getActiveSheet()->setCellValue('H3', 'Serie');
$objPHPExcel->getActiveSheet()->setCellValue('I3', 'Numero');
$objPHPExcel->getActiveSheet()->setCellValue('J3', 'Proveedor');
$objPHPExcel->getActiveSheet()->setCellValue('K3', 'SUBTOTAL');
$objPHPExcel->getActiveSheet()->setCellValue('L3', 'ISC');
$objPHPExcel->getActiveSheet()->setCellValue('M3', 'IGV');
$objPHPExcel->getActiveSheet()->setCellValue('N3', 'FLETE');
$objPHPExcel->getActiveSheet()->setCellValue('O3', 'Percepcion');
$objPHPExcel->getActiveSheet()->setCellValue('P3', 'TOTAL');
// Agregar en celda A2 valor numerico
//$objPHPExcel->getActiveSheet()->setCellValue('A2', 12345.6789);

// Agregar en celda A3 valor boleano
//$objPHPExcel->getActiveSheet()->setCellValue('A3', TRUE);

// Agregar a Celda A4 una formula
//$objPHPExcel->getActiveSheet()->setCellValue('A4', '=CONCATENATE(A1, " ", A2)');

        if($declarado){
            $declarado = 1;
        }else{
            $declarado = 0;
        }

        $result = ListarRegNov($fechaIni, $fechaFin, $declarado);
   $cont = 4;
	while ($row = mysqli_fetch_assoc($result)) {
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cont, $row["IdMovimiento"]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cont, $row["MovimientoFecha"]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cont, $row["IdMovimientoTipo"]);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cont, $row["TipoMovimiento"]);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$cont, $row["Serie"]);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$cont, $row["Numero"]);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$cont, $row["Proveedor"]);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$cont, $row["SUBTOTAL"]);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$cont, $row["ISC"]);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$cont, $row["IGV"]);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$cont, $row["FLETE"]);
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$cont, $row["Percepcion"]);
        $objPHPExcel->getActiveSheet()->setCellValue('P'.$cont, $row["TOTAL"]);
		$cont++;
	}

$default_border = array(
    'style' => PHPExcel_Style_Border::BORDER_THIN,
    'color' => array('rgb'=>'000000')
);
$style_header = array(
    	'borders' => array(
        'bottom' => $default_border,
        'left' => $default_border,
        'top' => $default_border,
        'right' => $default_border,
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'E1E0F7'),
    ),
    'font' => array(
        'bold' => true,
    ),
    'alignment' => array(
         'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
);
 for ($i=3; $i < 16; $i++) { 
 	$objPHPExcel->getActiveSheet()->getStyle(chr(68+($i-3)).'3')->applyFromArray( $style_header );
 	$objPHPExcel->getActiveSheet()->getColumnDimension(chr(68+($i-3)))->setAutoSize(true);
 }

 $objPHPExcel->getActiveSheet()
 		->getStyle("D4:H".$cont)
 		->getAlignment()
 		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

header('Content-Disposition: attachment;filename="ReporteRegMovimiento.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>
