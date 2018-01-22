<?php
include("boticav1/clases/BnGeneral.php");
$IdDocVenta=$_GET['IdDocVenta'];

try {
									$criterio="botica.Ve_DocVenta.IdDocVenta=$IdDocVenta";
									$result = fn_devolverDocVenta($criterio, "");
									$Linea1="--------------------------------";
									$Linea2="================================";

									$Salto=20;
									
									while ($row =mysql_fetch_row($result)) {
											//$RutaImpresora=$row[20];
											//echo "$RutaImpresora";
										$RutaImpresora="Microsoft XPS Document Writer";
											$handle = printer_open("$RutaImpresora");
											echo $handle;
											
											printer_set_option($handle, PRINTER_MODE, "text");
											//printer_set_option($handle, PRINTER_TEXT_ALIGN, PRINTER_TA_CENTER);
											printer_start_doc($handle, " AMEDRA"); // Name Document 
											printer_start_page($handle); 
											
											 $IDocVentaNro = $row[12];
											 $FechaHora=$row[13];
											 $Cliente=$row[3];
											 $DniRuc=$row[4];
											 $Direccion=$row[5];
											 $SerieMaq=$row[19];
											 $FontGrande=20;
											 $FontNormal=10;


											$Top=1;
											$font=printer_create_font("Arial", $FontGrande,8, 400, false,false,false,0);
											printer_select_font($handle, $font);
											$text = fn_devolverImpresionCentrar("AMEDRA");
											$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		   	 								printer_draw_text($handle, $text,0,$Top);

											$Top=$Top+$Salto;
											//$font=printer_create_font("Arial", $FontNormal,8, 400, false,false,false,0);
											//printer_select_font($handle, $font);
		   	 								$text = "         BOTICA - VERGAMITROLA";
											$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);

											$Top=$Top+$Salto;
		   	 								$text = "         PUCALLPA - UCAYALI - PERU";
											$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);

		                                    $Top=$Top+$Salto;
		   	 								$text = "         CORONEL PORTILLO - CALLERIA";
											$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
		                                    $Top=$Top+$Salto;
		                                    //printer_set_option($handle, PRINTER_TEXT_ALIGN, PRINTER_TA_LEFT);
		                                    $IDocVentaNro=str_pad($IDocVentaNro, 8, "0", STR_PAD_LEFT);
		   	 								$text = "TICKET Nro : " . $IDocVentaNro ;
											$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
											$Top=$Top+$Salto;		                                   
		   	 								$text = "FECHA HORA : " . $FechaHora ;
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
		                                    $Top=$Top+$Salto;		                                   
		   	 								$text = "SERIE MAQ REG : " . $SerieMaq ;
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
											$Top=$Top+$Salto;		                                   
		   	 								$text = $Linea1;
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
		                                    $Top=$Top+$Salto;		                                   
		   	 								$text = "Sr(es) : " . $Cliente;
											$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
		                                    $Top=$Top+$Salto;
		                                    $text = "Ruc/Dni : " . $DniRuc;
											$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
		                                    $Top=$Top+$Salto;
		                                    $text = "DIR : " . $Direccion;
											$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
		                                    $Top=$Top+$Salto;		                                   
		   	 								$text =  $Linea1;
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);

											$Top=$Top+$Salto;		                                   
		   	 								$text = "CANT|  DESC   .|  P/U |      TOT";
											$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
											$Top=$Top+$Salto;		                                   
		   	 								$text =  $Linea2;
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
		                                    $criterio="botica.Ve_DocVentaDet.IdDocVenta=$IdDocVenta";
											$resultDet = fn_devolverDocVentaDet($criterio, "Gen_Producto.Producto desc");
											$Total=0;
											while ($rowDet =mysql_fetch_row($resultDet)) {
												$Cant = $rowDet[5];
												$Precio = $rowDet[6];
												$Tot = $rowDet[7];												
												$Desc = $rowDet[3];	
												
												$Total=$Total+$rowDet[7];
												$Precio2=$Precio;
												$Tot2=$Tot;
												$lenfor=6-strlen($cant) ;
												for ($i=1; $i <$lenfor ; $i++) { 
													$Cant= " " . $Cant;
												}

												$lenfor=7-strlen($Precio2) ;
												for ($i=1; $i <$lenfor ; $i++) { 
													$Precio2= " " . $Precio2;
												}
												$lenfor=10-strlen($Tot2) ;
												for ($i=1; $i < $lenfor ; $i++) { 
													$Tot2=" " . $Tot2;
												}
												
												$Top=$Top+$Salto;
		                                    	$text = "$Desc ";
												//$text = str_replace( "-"," ", $text);
												$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array								
		                                    	printer_draw_text($handle, $text,1,$Top);
		                                    	$Top=$Top+$Salto;
		                                    	$text = "($Cant)              S/.($Precio2)         S/. $Tot2";
												//$text = str_replace( "-"," ", $text);
												$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array								
		                                    	printer_draw_text($handle, $text,1,$Top);
												 //break;
											}
											$Top=$Top+$Salto;		                                   
		   	 								$text =  $Linea2;
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array									
		                                    printer_draw_text($handle, $text,1,$Top);
											$Top=$Top+$Salto;
											$lenfor=10-strlen($Total) ;
												for ($i=1; $i <$lenfor ; $i++) { 
													$Total= " " . $Total;
											}
	                                    	$text = "                    SUBTOTAL S/. $Total";

											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array								
	                                    	printer_draw_text($handle, $text,1,$Top);
	                                    	$Top=$Top+$Salto;
	                                    	$text = "                                IGV  S/.       0.00";
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array								
	                                    	printer_draw_text($handle, $text,1,$Top);
	                                    	$Top=$Top+$Salto;
	                                    	$text = "                          TOTAL  S/. $Total";
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array								
	                                    	printer_draw_text($handle, $text,1,$Top);

	                                    	$Top=$Top+$Salto;
	                                    	$text = "        -  ";
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array								
	                                    	printer_draw_text($handle, $text,1,$Top);
	                                    	$Top=$Top+$Salto;
	                                    	$text = "BIENES TRANSFERIDOS PARA SER";
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array								
	                                    	printer_draw_text($handle, $text,1,$Top);
	                                    	$Top=$Top+$Salto;
	                                    	$text = "TRANSFERIDOS EN LA AMAZONIA";
											//$text = str_replace( "-"," ", $text);
											$text = str_replace( "/r/n"," \r\n , ", $text); // replace and make string Array								
	                                    	printer_draw_text($handle, $text,1,$Top);
									 	break;
									}
                                   

printer_end_page($handle);  // End Text

//printer_start_page($handle); // Start Logo QR
//printer_draw_bmp($handle, "c:\\QR.bmp", 60, 0);  // Logo Dir, lenght H , With V
//printer_end_page($handle);  // End Logo QR

printer_end_doc($handle);   // Close document 
printer_close($handle);     // Close Pritner

header("Location: http://localhost/boticav1/views/v_ventaform.php");

} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}

?>