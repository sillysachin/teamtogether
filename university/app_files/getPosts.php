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
$isValidPost=$dbobj->validatePost(array("cid","tid","jid"));

if ($isValidPost) {
    $connection=$dbobj->getDB();
    $epost=$dbobj->escapePost(array("cid","tid","jid"),$connection);
    $get_query="SELECT id,time,modified_time,subject from rnbje_kunena_messages WHERE catid='{$epost["cid"]}' AND thread='{$epost["tid"]}' ORDER BY id DESC LIMIT {$epost["jid"]},3";
    $get_res=mysqli_query($connection,$get_query);
    $ids=array();
    while($row=mysqli_fetch_assoc($get_res))
    {
        $ids[]=$row["id"];
        $time[]=$row["time"];
        $mtime[]=$row["modified_time"];
        $sub[]=json_encode($row["subject"]);
    }
    $len=count($ids);
    $attachments=array();
    for($i=0;$i<$len;$i++)
    {
        $attach_query="SELECT id from rnbje_kunena_attachments WHERE mesid='{$ids[$i]}'";
        $attachres=mysqli_query($connection,$attach_query);
        $attachments[$i]=($attachres->num_rows>0 )? true:false;
        $attachres->close();
    }
    echo "{\"posts\":[";
    for($i=0;$i<$len;$i++)
    {
        $get_query="SELECT message FROM rnbje_kunena_messages_text WHERE mesid='{$ids[$i]}'";
        $get_res=mysqli_query($connection,$get_query);
        $message=mysqli_fetch_assoc($get_res)['message'];
        echo "{ \"id\":\"{$ids[$i]}\",";
        echo "\"message\":",json_encode($message),",";
        if($mtime[$i]==null)
            echo "\"time\":\"{$time[$i]}\",";
        else
            echo "\"mtime\":\"{$mtime[$i]}\",";
        //Check if any attachments and echo them
        if($attachments[$i])
        {
            $attach_query="SELECT size,folder,filename FROM rnbje_kunena_attachments WHERE mesid='{$ids[$i]}'";
            $attachres=mysqli_query($connection,$attach_query);
            if($attachres->num_rows>0) {
                while ($row = mysqli_fetch_assoc($attachres)) {
                    $size = $row['size'];
                    $folder = $row['folder'];
                    $filename = $row['filename'];
                }
                echo "\"link\":" . json_encode("http://ts.icias2015.org/university/" . $folder ."/". $filename), ",";
                echo "\"size\":\"{$size}\",";

            }
        }
        echo "\"sub\":{$sub[$i]}";
        if($i!=$len-1)
            echo "},";
        else
            echo "}";

    }
    echo "]}";
} else
    die("{\"status\":\"0\"}");