<?php
include('resconnect.php');
$send=0;
$totalsent=0;
$invalid=0;
 if(isset($_POST['username']) && isset($_POST['password']))
 
 {
 	$username=$_POST['username'];
 	$password=$_POST['password'];
 	if($username=="mce" && $password=="mceres123")
 	 $send=1;
 	 else
 	 $invalid=1;
 
 
 }
if($send==1){
$usnquery="SELECT email from gcm_users WHERE email LIKE '%4MC14%' OR email LIKE '4mc13cs063'";
$usnres=mysqli_query($connection,$usnquery);
while($row=mysqli_fetch_assoc($usnres)){
$usns[]=$row['email'];
}
$count=0;
$total=count($usns);
for($i=0;$i<$total;$i++)
{
$url = 'http://notifoid.net/mceresults/getResult.php?res=notifoid';
$fields = array();
$fields['usn']=$usns[$i];

//url-ify the data for the POST
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string,'&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//execute post
$result = curl_exec($ch);
$result=json_decode($result);
$status=$result->info[0]->status;
$usn=$result->info[0]->usn;
$sgpa=$result->info[0]->sgpa;
$sem=$result->info[0]->sem;
$name=$result->info[0]->name;
$ntfn="";
$ntfn.=" Hey {$name}, ";
if($status==0)
	$ntfn.="Your results are now announced. The USN that you registered with was incorrect.  ";
if($status==1)
	$ntfn.="You've Passed with an SGPA of ";
if($status==2)
	$ntfn.="You've failed. Your SGPA is ";
if($status==3)
	$ntfn.="Your sem results are now announced. Your result is yet to be announced.   ";
if($status==1 || $status==2)
{
	$ntfn.="{$sgpa}.";
	$ntfn.="  For more info, view results inside application.";
}
$url = 'http://notifoid.net/mce/notifoid/cigniter/index.php';
$fields1 = array(
			'username'=>'MCE',
			'password'=>'mce-notifier@notifoid'
			
        );
$fields1['message']=$ntfn;
$fields1['usn']=$usns[$i];
//url-ify the data for the POST
foreach($fields1 as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string,'&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,count($fields1));
curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//execute post
$result = curl_exec($ch);
$count+=intval($result);
}
$totalsent=$count;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Notification Sender</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="shortcut icon" href="http://notifoid.net/templates/ja_nuevo/favicon.ico" type="image/x-icon" />
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                
            });
            function sendPushNotification(id){
                var data = $('form#'+id).serialize();
                $('form#'+id).unbind('submit');                
                $.ajax({
                    url: "send_message.php",
                    type: 'GET',
                    data: data,
                    beforeSend: function() {
                         
                    },
                    success: function(data, textStatus, xhr) {
                          $('.txt_message').val("");
                    },
                    error: function(xhr, textStatus, errorThrown) {
                         
                    }
                });
                return false;
            }
        </script>
    <style type="text/css">

@import url(http://fonts.googleapis.com/css?family=Open+Sans);
.btn { display: inline-block; *display: inline; *zoom: 1; padding: 4px 10px 4px; margin-bottom: 0; font-size: 13px; line-height: 18px; color: #333333; text-align: center;text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75); vertical-align: middle; background-color: #f5f5f5; background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6); background-image: -ms-linear-gradient(top, #ffffff, #e6e6e6); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6)); background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6); background-image: -o-linear-gradient(top, #ffffff, #e6e6e6); background-image: linear-gradient(top, #ffffff, #e6e6e6); background-repeat: repeat-x; filter: progid:dximagetransform.microsoft.gradient(startColorstr=#ffffff, endColorstr=#e6e6e6, GradientType=0); border-color: #e6e6e6 #e6e6e6 #e6e6e6; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); border: 1px solid #e6e6e6; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); cursor: pointer; *margin-left: .3em; }
.btn:hover, .btn:active, .btn.active, .btn.disabled, .btn[disabled] { background-color: #e6e6e6; }
.btn-large { padding: 9px 14px; font-size: 15px; line-height: normal; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; }
.btn:hover { color: #333333; text-decoration: none; background-color: #e6e6e6; background-position: 0 -15px; -webkit-transition: background-position 0.1s linear; -moz-transition: background-position 0.1s linear; -ms-transition: background-position 0.1s linear; -o-transition: background-position 0.1s linear; transition: background-position 0.1s linear; }
.btn-primary, .btn-primary:hover { text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); color: #ffffff; }
.btn-primary.active { color: rgba(255, 255, 255, 0.75); }
.btn-primary { background-color: #4a77d4; background-image: -moz-linear-gradient(top, #6eb6de, #4a77d4); background-image: -ms-linear-gradient(top, #6eb6de, #4a77d4); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#6eb6de), to(#4a77d4)); background-image: -webkit-linear-gradient(top, #6eb6de, #4a77d4); background-image: -o-linear-gradient(top, #6eb6de, #4a77d4); background-image: linear-gradient(top, #6eb6de, #4a77d4); background-repeat: repeat-x; filter: progid:dximagetransform.microsoft.gradient(startColorstr=#6eb6de, endColorstr=#4a77d4, GradientType=0);  border: 1px solid #3762bc; text-shadow: 1px 1px 1px rgba(0,0,0,0.4); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.5); }
.btn-primary:hover, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, .btn-primary[disabled] { filter: none; background-color: #4a77d4; }
.btn-block { width: 100%; display:block; }

* { -webkit-box-sizing:border-box; -moz-box-sizing:border-box; -ms-box-sizing:border-box; -o-box-sizing:border-box; box-sizing:border-box; }

html { width: 100%; height:100%; overflow:hidden; }

body { 
	width: 100%;
	height:100%;
	font-family: 'Open Sans', sans-serif;
	background: #092756;
	background: -moz-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%),-moz-linear-gradient(top,  rgba(57,173,219,.25) 0%, rgba(42,60,87,.4) 100%), -moz-linear-gradient(-45deg,  #670d10 0%, #092756 100%);
	background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -webkit-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -webkit-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
	background: -o-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -o-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -o-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
	background: -ms-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -ms-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -ms-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
	background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), linear-gradient(to bottom,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), linear-gradient(135deg,  #670d10 0%,#092756 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3E1D6D', endColorstr='#092756',GradientType=1 );
}
hr{margin-left:auto;margin-right:auto;width:400px}
.notifier { 
	position: absolute;
	top: 50%;
	left: 50%;
	margin: -190px 0 0 -150px;
	width:300px;
	height:300px;
}
.notifier h1 { color: #fff; text-shadow: 0 0 10px rgba(0,0,0,0.3); letter-spacing:1px; text-align:center;font-size:2em; }

textarea { 
	width: 100%; 
	margin-bottom: 10px; 
	background: rgba(0,0,0,0.3);
	border: none;
	outline: none;
	padding: 10px;
	font-size: 13px;
	color: #fff;
font-family:'Open Sans';
	text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
	border: 1px solid rgba(0,0,0,0.3);
	border-radius: 4px;
	box-shadow: inset 0 -5px 45px rgba(100,100,100,0.2), 0 1px 1px rgba(255,255,255,0.2);
	-webkit-transition: box-shadow .5s ease;
	-moz-transition: box-shadow .5s ease;
	-o-transition: box-shadow .5s ease;
	-ms-transition: box-shadow .5s ease;
	transition: box-shadow .5s ease;
}
textarea:focus { box-shadow: inset 0 -5px 45px rgba(100,100,100,0.4), 0 1px 1px rgba(255,255,255,0.2); }
            h1{
                font-family: 'Open Sans';
                font-size: 12px;
                color: white;text-align:center;
            }
            div.clear{
                clear: both;
            }
input { 
	width: 100%; 
	background: rgba(0,0,0,0.3);
	border: none;
	outline: none;
	font-size: 13px;
	color: #fff;
font-family:'Open Sans';
	text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
	border: 1px solid rgba(0,0,0,0.3);
	border-radius: 4px;
	box-shadow: inset 0 -5px 45px rgba(100,100,100,0.2), 0 1px 1px rgba(255,255,255,0.2);
	-webkit-transition: box-shadow .5s ease;
	-moz-transition: box-shadow .5s ease;
	-o-transition: box-shadow .5s ease;
	-ms-transition: box-shadow .5s ease;
	transition: box-shadow .5s ease;
}
input:focus { box-shadow: inset 0 -5px 45px rgba(100,100,100,0.4), 0 1px 1px rgba(255,255,255,0.2); }
            h1{
                font-family: 'Open Sans';
                font-size: 12px;
                color: white;text-align:center;
            }
            div.clear{
                clear: both;
            }
ul{list-style:none}
          
            ul.devices li textarea{
                resize: none;
            }
            
//Radio Styling


//Radio styling End
        </style>
    </head>
    <body>
        
        <div class="container">
 
 
            
            <h1>RESULT SENDER</h1>
            <hr/><?php if($invalid==1) echo "<h1 style=\"font-size:20px\">Wrong Username or Password!</h1>";
if($totalsent>0) echo "<h1 style=\"font-size:20px\">Successfully sent result to {$totalsent} devices.</h1>";
?>
<div class="notifier">
  <a href="http://notifoid.net"><img src="logo_white.png" style="margin-left:auto;margin-right:auto;display:block"/></a>
                            <form action="resender.php" method="post">

</br>
</br>
<input type="text" autocomplete="off" rows="1" cols="25" name="username" class="txt_message" placeholder="Username" style="padding:10px;margin-bottom:10px;" autocomplete="off"/>
<input type="password" name="password_fake" id="password_fake" value="" style="display:none;" />
<input type="password" rows="1" cols="25" autocomplete="off" name="password" class="txt_message" placeholder="Password" style="padding:10px;margin-bottom:10px;"/>
<input type="submit" class="btn btn-primary btn-block btn-large" value="Send Result!" onclick=""/>
</div></div>
</form></div>

        </div>
    </body>
</html>

       