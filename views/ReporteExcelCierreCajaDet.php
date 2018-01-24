<?php

require_once '../controllers/Reporte/Classes/PHPExcel.php';
include_once '../clases/BnGeneral.php';

        $FechaInicio = $_GET["fechaI"];
        $FechaFin = $_GET["fechaF"];
        $Salida = $_GET["salida"];
        $Credito = $_GET["credito"];
        $Contado = $_GET["contado"];
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
$objPHPExcel->getActiveSheet()->setCellValue('D2', 'Fecha Inicio');
$objPHPExcel->getActiveSheet()->setCellValue('E2', $FechaInicio);
$objPHPExcel->getActiveSheet()->setCellValue('F2', 'Fecha Fin');
$objPHPExcel->getActiveSheet()->setCellValue('G2', $FechaFin);
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Salida S/.');
$objPHPExcel->getActiveSheet()->setCellValue('E3', $Salida);
$objPHPExcel->getActiveSheet()->setCellValue('D4', 'Credito');
$objPHPExcel->getActiveSheet()->setCellValue('E4', $Credito);
$objPHPExcel->getActiveSheet()->setCellValue('D5', 'Total');
$objPHPExcel->getActiveSheet()->setCellValue('E5', $Contado);
// Agregar en celda A2 valor numerico
//$objPHPExcel->getActiveSheet()->setCellValue('A2', 12345.6789);

// Agregar en celda A3 valor boleano
//$objPHPExcel->getActiveSheet()->setCellValue('A3', TRUE);

// Agregar a Celda A4 una formula
//$objPHPExcel->getActiveSheet()->setCellValue('A4', '=CONCATENATE(A1, " ", A2)');

    /*    $result = ListarCierre();
   $cont = 4;
	while ($row = mysqli_fetch_assoc($result)) {
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cont, $row["FechaDoc"]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cont, $row["TipoDoc"]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cont, $row["Serie"]);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cont, $row["Numero"]);
    $objPHPExcel->getActiveSheet()->setCellValue('H'.$cont, $row["Total"]);
		$cont++;
	}*/

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
 for ($i=3; $i < 7; $i++) {
 	$objPHPExcel->getActiveSheet()->getStyle(chr(68+($i-3)).'2')->applyFromArray( $style_header );
 	$objPHPExcel->getActiveSheet()->getColumnDimension(chr(68+($i-3)))->setAutoSize(true);
}


 /*$objPHPExcel->getActiveSheet()
 		->getStyle("D4:H".$cont)
 		->getAlignment()
 		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/

header('Content-Disposition: attachment;filename="ReporteRegCompraContable.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>
