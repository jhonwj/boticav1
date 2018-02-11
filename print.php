<?php
include($_SERVER["DOCUMENT_ROOT"] . "clases/BnGeneral.php");
$IdDocVenta=$_GET['IdDocVenta'];
 ?>
 <html>
 <head>
     <script language="javascript">
         function WriteToFile()
         {
             var txt = new ActiveXObject("Scripting.FileSystemObject");
             var s = txt.CreateTextFile("\\\\USER01\\Tiketera", true);
             s.WriteLine(FnCentrar('BOTICA'));
             s.WriteLine(FnCentrar('PREVENVAC S.A.C'));
             <?php
             $criterio="Ve_DocVenta.IdDocVenta=$IdDocVenta";
             $result = fn_devolverDocVenta($criterio, "");
             $Linea1="--------------------------------";
             $Linea2="================================";
             while ($row = mysqli_fetch_row($result)) {
               $IDocVentaNro = $row[12];
               $FechaHora=$row[13];
               $Cliente=$row[3];
               $DniRuc=$row[4];
               $Direccion=$row[5];
               $SerieMaq=$row[19];
               $FontGrande=20;
               $FontNormal=10;
               $IDocVentaNro=str_pad($IDocVentaNro, 8, "0", STR_PAD_LEFT);
              ?>
              s.WriteLine('TICKET Nro: <?php echo $IDocVentaNro; ?>');
              s.WriteLine('FECHA HORA: <?php echo $FechaHora; ?>');
              s.WriteLine('SERIE MAQ REG : <?php echo $SerieMaq; ?>');
              s.WriteLine("================================");
              s.WriteLine('Sr(es) : <?php echo $Cliente; ?>');
              s.WriteLine('Ruc/Dni : <?php echo $DniRuc; ?>');
              s.WriteLine('Dir : <?php echo $Direccion; ?>');
              s.WriteLine("================================");
              s.WriteLine('CANT | DESC  |  P/U|  TOT');
              s.WriteLine("================================");
              <?php
              $criterio="Ve_DocVentaDet.IdDocVenta=$IdDocVenta";
              $resultDet = fn_devolverDocVentaDet($criterio, "Gen_Producto.Producto desc");
              $Total=0;
              while ($rowDet =mysqli_fetch_row($resultDet)) {
                $Cant = $rowDet[5];
                $Precio = $rowDet[6];
                $Tot = $rowDet[7];
                $Desc = $rowDet[3];
                $Total = $Total + $Tot;
               ?>
               s.WriteLine('<?php echo $Desc; ?>');
               s.WriteLine('( <?php echo $Cant; ?>)         S/.(<?php echo $Precio;?>)   S/.<?php echo $Tot;?>');
               s.WriteLine(' ');
               <?php } ?>
               s.WriteLine("================================");
               s.WriteLine(FnCentrar('SUBTOTAL S/. <?php echo  number_format($Total, 2, '.', ' '); ?>'));
               s.WriteLine(FnCentrar('IGV S/. 0.00'));
               s.WriteLine(FnCentrar('TOTAL S/. <?php echo  number_format($Total, 2, '.', ' '); ?>'));
               s.WriteLine(FnCentrar('BIENES TRANSFERIDOS EN LA AMAZONIA'));
               s.WriteLine(FnCentrar('PARA SER CONSUMIDOS EN LA MISMA'));
              <?php } ?>
             s.WriteLine	(String.fromCharCode(29) + "V" + String.fromCharCode(66) + String.fromCharCode(0));
             s.Close();
             window.location.href="/v_ventaform.php";
          }

          function FnCentrar(VbText){
            var text2=VbText;
            for (i = VbText.length; i < 38; i++) {
        if (i % 2 == 0 ){
          text2 = text2 + " ";
        }else{
          text2 = " " + text2;
        }
    }
        return text2;
    }
      </script>
 </head>
<body onload="WriteToFile()">

</body>
</html>
