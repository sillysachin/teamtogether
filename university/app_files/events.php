<?php
/**
 * Created by PhpStorm.
 * User: SHESHA
 * Date: 14-07-2015
 * Time: 08:17 PM
 */
//Includes
require_once("./dbconnect.php");
$dbobj=new dbconnect();
//Check for all required post params
$isValidPost=($_GET["pass"]=="notifoid")?true:false;
if ($isValidPost) {
    $connection=$dbobj->getDB();
    $get_contacts="SELECT * from events";
    $get_res=mysqli_query($connection,$get_contacts);
    echo "{\"events\":";
    while($row=mysqli_fetch_assoc($get_res))
    {
    $events[]= $row;
    }
    echo json_encode($events);
    echo "}";
} else
    die("{\"status\":\"0\"}");