<?php
$messages = file_get_contents('http://notifoid.net/mce/index.php?option=com_jsonexport&table=rnbje_kunena_messages&fields=id,catid,thread,time,modified_time,subject');
echo "{\"TITLE\":";
echo($messages);
echo ",";
$messagestext = file_get_contents('http://notifoid.net/mce/index.php?option=com_jsonexport&table=rnbje_kunena_messages_text');
echo "\"CONTENT\":";
echo($messagestext);
echo ",";
$attachments = file_get_contents('http://notifoid.net/mce/index.php?option=com_jsonexport&table=rnbje_kunena_attachments&fields=id,mesid,size,folder,filename');
echo "\"ATTACH\":";
echo($attachments);
echo "}";
?> 