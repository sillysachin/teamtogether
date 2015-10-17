<?php
//request url
 header('refresh: 1; url=http://tap-notify.notifoid.net'); 
$url    = 'https://android.googleapis.com/gcm/send';
  include_once 'db_functions.php';
        $db = new DB_Functions();
        $users = $db->getAllUsers();
$row = mysql_fetch_array($users); 
//your api key
$apiKey = 'AIzaSyCQciRJRgc21pFt3f57GZeT_eIYlFzEXIE';

                    while ($row = mysql_fetch_array($users)) {
$registrationIDs[] = $row['gcm_regid'];
$counter[]=$row['email'];
}
for($x=0,$size=count($counter);$x<=$size;$x++){
if(strpos($counter[$x],'cs') !== false){}else{$registrationIDs[$x]="missing";}
}
if(isset($_POST["price"]))
    $notifoid = $_POST["price"];
else
    unset($notifoid);

//payload data
$data   = array('price' => "$notifoid");
 
$fields = array('registration_ids' => $registrationIDs,
                'data' => $data);
 
//http header
$headers = array('Authorization: key=' . $apiKey,
                 'Content-Type: application/json');
 
//curl connection
$ch = curl_init();
 
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
$result = curl_exec($ch);
 
curl_close($ch);
 
echo $result;
    
  
?>
