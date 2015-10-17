$resultcoment = mysql_query("SELECT * FROM notificaciones");
$registrationIDs = array();

while ($row = mysql_fetch_array($resultcoment)){
  $result = '"' . $row['regId'] . '", ';
  //print_r($result);
  $registrationIDs[] = $result;
}

$message = $_POST['message'];

$fields = array(
            'registration_ids'  => $registrationIDs,
            'data'              => array( "message" => $message ),
            );