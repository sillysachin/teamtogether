<?php
/**
 * Created by PhpStorm.
 * User: SHESHA
 * Date: 13-07-2015
 * Time: 03:12 PM
 */
class dbconnect
{
    private $username="unitalentspear";
    private $dbname="talentspear";
    private $hostname="localhost";
    private $password="talentspear";
    public function getDB()
    {
        $connection=mysqli_connect($this->hostname,$this->username,$this->password,$this->dbname) or die("Unable to connect to MySQL");
        mysqli_set_charset ( $connection , 'utf8' );
        return $connection;
    }
    public function validateUser($connection,$pid,$hash)
    {
        $validate_query="SELECT pass from profile WHERE id='{$pid}'";
        $validate_res=mysqli_query($connection,$validate_query);
        if($validate_res->num_rows>0) {
            if (mysqli_fetch_assoc($validate_res)['pass'] == $hash)
                return true;
            else
                return false;
        }
        else
            return false;
    }

    public function validatePost($parray)
    {
        $len=count($parray);
        $flag=0;
        for($i=0;$i<$len;$i++)
        {
            if(!(isset($_POST[$parray[$i]]) && $_POST[$parray[$i]] != ""))
            {
                $flag=1;
                break;
            }
        }
        return ($flag==1)?false:true;
    }

    public function escapePost($parray,$connection)
    {
        $epost=array();
        $len=count($parray);
        for($i=0;$i<$len;$i++)
        {
            $epost[$parray[$i]]=mysqli_real_escape_string($connection,$_POST[$parray[$i]]);
        }

        return $epost;

    }
}