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
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'ID');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'MARCA');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'FORMA FARMACEUTICA');
$objPHPExcel->getActiveSheet()->setCellValue('G3', 'MEDICION');
$objPHPExcel->getActiveSheet()->setCellValue('H3', 'CATEGORIA');
$objPHPExcel->getActiveSheet()->setCellValue('I3', 'PRODUCTO');
$objPHPExcel->getActiveSheet()->setCellValue('J3', 'DESCRIPCION');
$objPHPExcel->getActiveSheet()->setCellValue('K3', 'DESCRIPCION CORTA');
$objPHPExcel->getActiveSheet()->setCellValue('L3', 'CODIGO BARRA');
$objPHPExcel->getActiveSheet()->setCellValue('M3', 'CODIGO');
$objPHPExcel->getActiveSheet()->setCellValue('N3', 'DOSIS');
$objPHPExcel->getActiveSheet()->setCellValue('O3', 'PRECIO CONTADO');
$objPHPExcel->getActiveSheet()->setCellValue('P3', 'PRECIO POR MAYOR');
$objPHPExcel->getActiveSheet()->setCellValue('Q3', 'STOCK POR MAYOR');
	
// Agregar en celda A2 valor numerico
//$objPHPExcel->getActiveSheet()->setCellValue('A2', 12345.6789);
	
// Agregar en celda A3 valor boleano
//$objPHPExcel->getActiveSheet()->setCellValue('A3', TRUE);
	
// Agregar a Celda A4 una formula
//$objPHPExcel->getActiveSheet()->setCellValue('A4', '=CONCATENATE(A1, " ", A2)');

   $result = fn_devolverProducto("","");
   $cont = 4;
	while ($row = mysqli_fetch_assoc($result)) {
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cont, $row["IdProducto"]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cont, $row["ProductoMarca"]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cont, $row["ProductoFormaFarmaceutica"]);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cont, $row["ProductoMedicion"]);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$cont, $row["ProductoCategoria"]);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$cont, $row["Producto"]);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$cont, $row["ProductoDesc"]);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$cont, $row["ProductoDescCorto"]);
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$cont, $row["CodigoBarra"]);
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$cont, $row["Codigo"]);
		$objPHPExcel->getActiveSheet()->setCellValue('N'.$cont, $row["Dosis"]);
		$objPHPExcel->getActiveSheet()->setCellValue('O'.$cont, $row["PrecioContado"]);
		$objPHPExcel->getActiveSheet()->setCellValue('P'.$cont, $row["PrecioPorMayor"]);
		$objPHPExcel->getActiveSheet()->setCellValue('Q'.$cont, $row["StockPorMayor"]);
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
 for ($i=3; $i < 17; $i++) { 
 	$objPHPExcel->getActiveSheet()->getStyle(chr(68+($i-3)).'3')->applyFromArray( $style_header );
 	$objPHPExcel->getActiveSheet()->getColumnDimension(chr(68+($i-3)))->setAutoSize(true);
 }

 $objPHPExcel->getActiveSheet()
 		->getStyle("D4:Q".$cont)
 		->getAlignment()
 		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

header('Content-Disposition: attachment;filename="ReporteProductos.xlsx"');
header('Cache-Control: max-age=0');
 
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>