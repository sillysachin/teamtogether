<?php
$trialer=$_POST["trial"];
$answer="yes";
if($trialer==$answer)
 header('refresh: 0; url=http://samalnad.com'); 
else
header('refresh: 0; url=http://www.trial.com'); 
?>