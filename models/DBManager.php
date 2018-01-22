<?php
header( 'Content-Type: text/html;charset=utf-8' );


function ejecutarSQLCommand($commando){

  $mysqli = new mysqli("localhost", "neurofac_botica", "A*=TS$A_pPZS", "neurofac_botica");
  //$mysqli = new mysqli("localhost", "root", "", "botica");
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}
if (!$mysqli->set_charset("utf8")) {
    //printf("Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error);
    exit();
} else {
    //printf("Conjunto de caracteres actual: %s\n", $mysqli->character_set_name());
}

if ( $mysqli->multi_query($commando)) {
     if ($resultset = $mysqli->store_result()) {
    	while ($row = $resultset->fetch_array(MYSQLI_BOTH)) {

    	}
    	$resultset->free();
      return true;
     }else{
      return false;
     }


}else{
  return false;
}

$mysqli->close();

return true;
}

function getSQLResultSet($commando){

    // $link = mysql_connect("localhost", "root", "");
    $link = mysql_connect("localhost", "neurofac_botica", "A*=TS$A_pPZS");
    $acentos = mysql_query("SET NAMES 'utf8'");

    if (!$link) {
        die('Error de coneccion ' . mysql_error());
    }
    $db_selected = mysql_select_db('neurofac_botica', $link);
    if (!$db_selected) {
        die ('Can\'t use botica : ' . mysql_error());
    }

    $query=$commando;
    $result = mysql_query($query);

    if (!$result) {
        $message = 'Invalid query: ' . mysql_error() . " ";
        $message .= 'Whole query: ' . $query;

        die($message);
        return null;
        exit();
    }
    mysql_close();
    return $result;
}


?>
