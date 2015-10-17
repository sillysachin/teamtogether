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
function getMeWeeks($week,$sec)
{
	$length=count($week);
	for($j=0;$j<$length;$j++)
	{
		if($sec==substr($week[$j],0,1))
		{
		return substr($week[$j],1,5);
		
		}
	}
	return "none";

}
//Check for all required post params
$isValidPost=$dbobj->validatePost(array("sec"));

if ($isValidPost) {
    $connection=$dbobj->getDB();
    $epost=$dbobj->escapePost(array("sec"),$connection);
    $getTt="SELECT * from timetable";
    $getres=mysqli_query($connection,$getTt);
    while($row=mysqli_fetch_assoc($getres))
    {
       $times[]=$row['time'];
       $monday[]=$row['monday'];
       $tuesday[]=$row['tuesday'];
       $wednesday[]=$row['wednesday'];
       $thursday[]=$row['thursday'];
       $friday[]=$row['friday'];
       $saturday[]=$row['saturday'];
    }
   
   
     echo "{";
    $len=count($times);
    for($i=0;$i<$len;$i++)
    {
    echo "\"{$times[$i]}\":[{";
    	$mon_subs=explode("$",$monday[$i]);
    	$tue_subs=explode("$",$tuesday[$i]);
    	$wed_subs=explode("$",$wednesday[$i]);
    	$thu_subs=explode("$",$thursday[$i]);
    	$fri_subs=explode("$",$friday[$i]);
    	$sat_subs=explode("$",$saturday[$i]);
    	
    	echo "\"monday\":\"",getMeWeeks($mon_subs,$epost["sec"]),"\",";
    	echo "\"tuesday\":\"",getMeWeeks($tue_subs,$epost["sec"]),"\",";
    	echo "\"wednesday\":\"",getMeWeeks($wed_subs,$epost["sec"]),"\",";
    	echo "\"thursday\":\"",getMeWeeks($thu_subs,$epost["sec"]),"\",";
    	echo "\"friday\":\"",getMeWeeks($fri_subs,$epost["sec"]),"\",";
    	if($i!=$len-1)
    	echo "\"saturday\":\"",getMeWeeks($sat_subs,$epost["sec"]),"\"}],";
    	else
    	echo "\"saturday\":\"",getMeWeeks($sat_subs,$epost["sec"]),"\"}]}";
    
    }
} else
    die("{\"status\":\"0\"}");