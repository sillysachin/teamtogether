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

    $connection=$dbobj->getDB();
    $get_query="SELECT MAX(id) as id from rnbje_kunena_messages";
    $get_res=mysqli_query($connection,$get_query);
    $id=0;
    while($row=mysqli_fetch_assoc($get_res))
    {
        $id=$row["id"];
    }
echo ($id-1);