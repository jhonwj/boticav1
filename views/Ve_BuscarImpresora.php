<?php
// header('Content-Type: application/pdf');
// header('Content-Disposition: attachment;filename="01simple.pdf"');
// header('Cache-Control: max-age=0');
// readfile("original.pdf");

include("../clases/BnGeneral.php");
$IdDocVenta=$_GET['IdDocVenta'];

 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title></title>
     <?php include_once("linker.php"); ?>
   </head>
   <script type="text/javascript">
     $(document).ready(function(e){
        //  e.preventDefault();
        //  $(".ticket").print();
       $(".ticket").print();
     });
   </script>
   <body>
     <div class="ticket">
     <?php
     $criterio="Ve_DocVenta.IdDocVenta=$IdDocVenta";
     $result = fn_devolverDocVenta($criterio, "");
     while ($row =mysql_fetch_row($result)) {
     $IDocVentaNro = $row[12];
     $FechaHora=$row[13];
     $Cliente=$row[3];
     $DniRuc=$row[4];
     $Direccion=$row[5];
     $SerieMaq=$row[19];
     ?>
     <h3>AMEDRA</h3>
     <h4>BOTICA - DELMAN</h4>
     <p>PUCALLPA - UCAYALI - PERU</p>
     <p>CORONEL PORTILLO - CALLERIA</p>
     <p>Ticket Nro: <?php echo $IDocVentaNro; ?></p>
     <p>FECHA HORA :<?php echo $FechaHora; ?> </p>
     <p>SERIE MAQ REG : <?php echo $SerieMaq; ?> </p>
     <p>Sr(es) : <?php echo $Cliente; ?></p>
     <p>Ruc/Dni : <?php echo $DniRuc; ?></p>
     <p>Dir : <?php echo $Direccion; ?></p>
     <table border="0">
       <thead>
         <th>CANT</th>
         <th>DESC</th>
         <th> P/U </th>
         <th>TOT</th>
       </thead>
       <tbody>
         <?php $criterio="Ve_DocVentaDet.IdDocVenta=$IdDocVenta";
          $resultDet = fn_devolverDocVentaDet($criterio, "Gen_Producto.Producto desc");
          while ($rowDet =mysql_fetch_row($resultDet)) {
            $Cant = $rowDet[5];
            $Precio = $rowDet[6];
            $Tot = $rowDet[7];
            $Desc = $rowDet[3];
            echo "<tr><td>".$Cant."</td><td>".$Desc."</td><td>".$Precio."</td><td>".$Tot."</td></tr>";
          }
          ?>
       </tbody>
     </table>
     <p>SUBTOTAL : </p>
     <p>IGV : </p>
     <p>TOTAL : </p>
     <p>BIENES TRANSFERIDOS PARA SER</p>
     <p>TRANSFERIDOS EN LA AMAZONIA</p>
   <?php }
    ?>
   </div>
   </body>
 </html>
