<?php
require_once '/Reporte/Classes/PHPExcel.php';

$filename = $_POST['filename'] . '.' . $_POST['type'];
$table    = $_POST['table'];

// save $table inside temporary file that will be deleted later
$tmpfile = tempnam(sys_get_temp_dir(), 'html');
file_put_contents($tmpfile, $table);

// insert $table into $objPHPExcel's Active Sheet through $excelHTMLReader
$objPHPExcel     = new PHPExcel();
$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
// $objPHPExcel->getActiveSheet()->setTitle('Proforma'); // Change sheet's title if you want

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
 for ($i=0; $i < 4; $i++) {
     //var_dump(chr(68+($i-3)).'3');exit();
 	$objPHPExcel->getActiveSheet()->getStyle(chr(68+($i-3)).'5')->applyFromArray( $style_header );
 	$objPHPExcel->getActiveSheet()->getColumnDimension(chr(68+($i-3)))->setAutoSize(true);
 }

 $gdImage = @imagecreatefromjpeg('../resources/images/delman.jpg');
 // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
 $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
 $objDrawing->setName('Sample image');
 $objDrawing->setDescription('Sample image');
 $objDrawing->setImageResource($gdImage);
 $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
 $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
 $objDrawing->setHeight(200);
     $objDrawing->setCoordinates(D10);
     $objDrawing->setOffsetX(200);
 $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

unlink($tmpfile); // delete temporary file because it isn't needed anymore

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
header('Cache-Control: max-age=0');

// Creates a writer to output the $objPHPExcel's content
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_start();
$writer->save('php://output');
$xlsData = ob_get_contents();
ob_end_clean();
$response =  array(
        'success' => 'Se exporto correctamente: ' . $filename,
        'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
    );

die(json_encode($response));
?>
