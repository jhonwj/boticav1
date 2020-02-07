<?php
header( 'Content-Type: text/html;charset=utf-8' );
//include_once($_SERVER["DOCUMENT_ROOT"] . '/views/validateUser.php');

function ejecutarSQLCommand($commando){

  $mysqli = new mysqli("localhost", "root", '', "neurosys_hotel");
  // $mysqli = new mysqli("localhost", "neurosys_NEUROSOFT", 'IX!!q!t(&Fc^', "neurosys_NEUROSOFT");
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
    $mysqli = new mysqli("localhost", "root", '', 'neurosys_hotel');
    // $mysqli = new mysqli("localhost", "neurosys_NEUROSOFT", 'IX!!q!t(&Fc^', "neurosys_NEUROSOFT");

    $mysqli->set_charset("utf8");
    // $acentos = mysql_query("SET NAMES 'utf8'");

    if (!$mysqli) {
        die('Error de conexion ' . $mysqli->error );
    }
    //$db_selected = mysql_select_db('botica', $link);
    //if (!$db_selected) {
        //die ('Can\'t use botica : ' . mysql_error());
    //}

    $query = $commando;
    //$result = mysql_query($query);
    $result = $mysqli->query( $query );

    if (!$result) {
        $message = 'Invalid query: ' . $mysqli->error . " ";
        $message .= 'Whole query: ' . $query;

        die($message);
        return null;
        exit();
    }

    if ($mysqli->insert_id) {
      return $mysqli->insert_id;
    }

    mysqli_close($mysqli);
    return $result;
}

function getMysqliLink() {
    $mysqli = new mysqli("localhost", "root", '', 'neurosys_hotel');
    //$mysqli = new mysqli("localhost", "neurosys_NEUROSOFT", 'IX!!q!t(&Fc^', 'neurosys_NEUROSOFT');
    $mysqli->set_charset("utf8");
    if (!$mysqli) {
        die('Error de conexion ' . $mysqli->error );
    }
    return $mysqli;
}

?>
