<?php 
echo "{\"TITLE\":[";
//Get Json Data
$messages = file_get_contents('http://notifoid.net/mce/index.php?option=com_jsonexport&table=rnbje_kunena_messages&fields=id,catid,thread,time,modified_time,subject');
//Split to array of characters
$splitter=str_split($messages);
//Initialise printer array
$mprinter=array();
//initialise id holder
$holder=array();
//Index counter of mprinter
$j=0;
//Initialise mprinter to empty
$mprinter[$j]="";
//Initialise validation to true for object trace
$validator=true;
//Put objects into each string of string array
for($i=1;$i<strlen($messages);$i++){
if($splitter[$i]=='}')
	$validator=false;
if($validator)
	$mprinter[$j].="$splitter[$i]";
if(!$validator){
$mprinter[$j].="}";
$i++;$j++;
$mprinter[$j]="";
$validator=true;
}
}
$i_c=0;
for($m=0;$m<$j;$m++){
	if(strpos($mprinter[$m],"\"catid\":\"15\"")>0)
		$i_c++;
}
//index of holder array
$holder_c=0;
//counter for ignoring ending ',';
$h_c=0;
for($i=0;$i<$j;$i++){
	if(strpos($mprinter[$i],"\"catid\":\"15\"")>0){
		$temp=$mprinter[$i];
	echo "{$temp}";
	$holder[$holder_c]=strstr($mprinter[$i],"\"id\"");
	$holder[$holder_c]=substr($holder[$holder_c],1,10);
	$holder_c++;
	$h_c++;
	if($h_c!=$i_c)
		echo ",";
	}
	
}
echo "],";
 
echo "\"CONTENT\":[";
$messages1 = file_get_contents('http://notifoid.net/mce/index.php?option=com_jsonexport&table=rnbje_kunena_messages_text');
$splitter1=str_split($messages1);
$mprinter1=array();
$j1=0;
$mprinter1[$j1]="";
$validator1=true;
for($i1=1;$i1<strlen($messages1);$i1++){
if($splitter1[$i1]=='}')
	$validator1=false;
if($validator1)
	$mprinter1[$j1].="$splitter1[$i1]";
if(!$validator1){
$mprinter1[$j1].="}";
$i1++;$j1++;
$mprinter1[$j1]="";
$validator1=true;
}
}
$i_c1=0;
for($i1=0;$i1<$j1;$i1++){
	for($x=0;$x<$holder_c;$x++){
	if(strpos($mprinter1[$i1],$holder[$x])>0){
	$i_c1++;
	}
	}
	}
	$h_c1=0;
for($i1=0;$i1<$j1;$i1++){
	for($x=0;$x<$holder_c;$x++){
	if(strpos($mprinter1[$i1],$holder[$x])>0){
		$temp=$mprinter1[$i1];
	echo "{$temp}";
	$h_c1++;
	if($i_c1!=$h_c1)
		echo ",";
}
	}
}
echo "],";

//Attachment Content

echo "\"ATTACH\":[";
$messages2 = file_get_contents('http://notifoid.net/mce/index.php?option=com_jsonexport&table=rnbje_kunena_attachments&fields=mesid,size,folder,filename');
$splitter2=str_split($messages2);
$mprinter2=array();
$j2=0;
$mprinter2[$j2]="";
$validator2=true;
for($i2=1;$i2<strlen($messages2);$i2++){
if($splitter2[$i2]=='}')
	$validator2=false;
if($validator2)
	$mprinter2[$j2].="$splitter2[$i2]";
if(!$validator2){
$mprinter2[$j2].="}";
$i2++;$j2++;
$mprinter2[$j2]="";
$validator2=true;
}
}
$i_c2=0;
for($i1=0;$i1<$j2;$i1++){
	for($x=0;$x<$holder_c;$x++){
	if(strpos($mprinter2[$i1],$holder[$x])>0){
		$i_c2++;
	}
	}
}
$h_c2=0;
for($i1=0;$i1<$j2;$i1++){
	for($x=0;$x<$holder_c;$x++){
	if(strpos($mprinter2[$i1],$holder[$x])>0){
		$temp=$mprinter2[$i1];
	echo "{$temp}";
	$h_c2++;
	if($i_c2!=$h_c2)
		echo ",";
}
	}
}
echo"]}";
?> 