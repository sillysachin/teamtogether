<?php
class Gcm_mod extends CI_Model {

     public function __construct()
        {
                $this->load->database();
        }

      public function getAllRegIds($dept="ALL",$usn){
      if($usn=="ALL"){
      if($dept!="ALL"){
      	$this->db->like('email', $dept);
        $query= $this->db->get('gcm_users');
    }
    else
    {
    $query = "SELECT gcm_regid,id FROM gcm_users WHERE gcm_regid IS NOT NULL AND gcm_regid != '' GROUP BY gcm_regid";
    $query = $this->db->query($query);
    }
    }
    else
    {
    $this->db->like('email', $usn);
        $query= $this->db->get('gcm_users');
    }
           	
    if($query->num_rows() > 0){
        $result = $query->result_array();
        $regids = array();

        foreach($result as $row){
            $regids[$row['id']] = $row['gcm_regid'];
        }
        return $regids; 
    }else{
        return false;
    }
}
	public function updateGCMId($userid,$gcmid){
      $query = 'UPDATE gcm_users SET 
                  gcm_regid = "'.$gcmid.'" 
                WHERE id = "'.$userid.'"';
      $this->db->query($query);
}
	public function deleteGCMId($regid){
	 $this->db->where('id', $regid);
   $this->db->delete('gcm_users'); 
	}
	public function allUsers() {
        $result = $this->db->count_all_results('gcm_users');
        return $result;
    }

	public function deleteSame(){
	$ids=array();
	$regids=array();
	$count=0;
	$query="SELECT gcm_regid, MIN(id) as mini FROM gcm_users GROUP BY gcm_regid HAVING COUNT(*) > 1";
	$query = $this->db->query($query);
	foreach ($query->result() as $row)
		{
  			 $ids[]=$row->mini;
  			 $regids[]=$row->gcm_regid;
		}
	$total=count($ids);
	for($i=0;$i<$total;$i++)
	{
		$delquery="DELETE FROM gcm_users WHERE id > {$ids[$i]} AND gcm_regid='{$regids[$i]}'";
		$resquery = $this->db->query($delquery);
		$count++;
		
	}
	return $count;
	}


}