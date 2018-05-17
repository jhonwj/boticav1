<?php 

require_once '../controllers/Reporte/Classes/PHPExcel.php';
include_once '../clases/BnGeneral.php';

$objPHPExcel = new PHPExcel();

//propiedades del documento excel
$objPHPExcel->getProperties()
		->setCreator("Códigos de Programación")
        ->setLastModifiedBy("Códigos de Programación")
        ->setTitle("Excel en PHP")
        ->setSubject("Documento de prueba")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("excel phpexcel php")
        ->setCategory("Ejemplos");

//propiedades de la hoja
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Hoja 1');


// Agregar en celda A1 valor string
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'CODIGO');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PRODUCTO');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'FORMA FARMACEUTICA');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'LABORATORIO');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'FECHA VENCIMIENTO');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'LOTE');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'SERIE');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'NUMERO');
	
// Agregar en celda A2 valor numerico
//$objPHPExcel->getActiveSheet()->setCellValue('A2', 12345.6789);
	
// Agregar en celda A3 valor boleano
//$objPHPExcel->getActiveSheet()->setCellValue('A3', TRUE);
	
// Agregar a Celda A4 una formula
//$objPHPExcel->getActiveSheet()->setCellValue('A4', '=CONCATENATE(A1, " ", A2)');

    $result = fn_devolverProductosProximosAVencer($_GET['FechaIni'], $_GET['FechaFin']);
    $cont = 2;

	while ($row = mysqli_fetch_assoc($result)) {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$cont, $row["IdProducto"]);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$cont, $row["Codigo"]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$cont, $row["Producto"]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cont, $row["FormaFarmaceutica"]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cont, $row["Marca"]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cont, $row["FechaVen"]);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cont, $row["IdLote"]);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$cont, $row["Serie"]);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$cont, $row["Numero"]);
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
 for ($i=1; $i < 10; $i++) { 
 	$objPHPExcel->getActiveSheet()->getStyle(chr(68+($i-4)).'1')->applyFromArray( $style_header );
 	$objPHPExcel->getActiveSheet()->getColumnDimension(chr(68+($i-4)))->setAutoSize(true);
 }  

/* $objPHPExcel->getActiveSheet()
 		->getStyle("D4:Q".$cont)
 		->getAlignment()
 		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/

header('Content-Disposition: attachment;filename="ReporteProductosPorVencer.xlsx"');
header('Cache-Control: max-age=0');
 
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>