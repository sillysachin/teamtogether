<?php
  include_once 'db_functions.php';
        $db = new DB_Functions();
        $users = $db->getAllUsers();
$row = mysql_fetch_array($users); 
$query = "SELECT (*) FROM table WHERE column = value";
$results = mysql_query($query);
$rows = mysql_num_rows($results);
echo $rows ;
?>