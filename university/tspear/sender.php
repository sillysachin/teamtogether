<?php
$url    = 'https://android.googleapis.com/gcm/send';
  include_once 'db_functions.php';
        $db = new DB_Functions();
        $users = $db->getAllUsers();
$row = mysql_fetch_array($users); 
//your api key
$username="MCE";
$password="mce-notifier@ssd";
//$apiKey = 'AIzaSyCQciRJRgc21pFt3f57GZeT_eIYlFzEXIE';
$apiKey = 'AIzaSyCQciRJRgc21pFt3f57GZeT_eIYlFzEXIE';

//Login Validator

if(trim($_POST['username'])!=$username && $_POST['password']!=$password){

$dept = strtoupper($_POST['department']);
                    while ($row = mysql_fetch_array($users)) {
$registrationIDs[] = $row['gcm_regid'];
$counter[]=strtoupper($row['email']);

//Take IDs into array

//$ids[]=$row['id'];
}

//print_r($counter);
echo "<br />";
//sender to hold sending ID's
//$sender[]=array();
$size=count($counter);
$all="ALL";
$sender=array();
//echo "{$dept}<br/>{$ids[0]}<br/>";
if($dept!=$all){
for($x=0;$x<$size;$x++){
if(strpos($counter[$x],$dept)>0){$sender[]=$registrationIDs[$x]; }
}}
else{
$sender=array_merge($sender,$registrationIDs);}
print_r($registrationIDs);
if(isset($_POST["price"]))
    $notifoid = $_POST["price"];
else
    unset($notifoid);
//print_r($sender);
//payload data
$data   = array('price' => "$notifoid");
 
$fields = array('registration_ids' => $sender,
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
 //echo "<script type=\"text/javascript\"> window.location=\"notifier.php?sub=1\"</script>"; 
echo $result;
}
else{
header("Location: http://notifoid.net/mce/notifoid/notifier.php?login=false");  
 }
?>