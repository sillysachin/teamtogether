<?php
class Gcm extends CI_Controller 
{

	private $onlyusn=0;
	//Check for forum notifications
	private $formnotify=0;
	private $isEdited=0;
	private $isDeleted=0;
	private $mid=0;
	private $newpost=1;
	public function sendtoall(){
	 
    $this->load->model('gcm_mod');
    $usn= $this->input->post('usn');
    $fromforum=$this->input->post('fromforum');
	$isEdited=$this->input->post('edited');
	$isDeleted=$this->input->post('deleted');
	$mid=$this->input->post('mid');
	if($isEdited==1) $this->newpost=2;
	if($isDeleted==1) $this->newpost=3;
    if($fromforum==1) $this->formnotify=1;
    if($usn==FALSE) $usn="ALL";
    $dept=strtoupper($this->input->post('department'));
    $result = $this->gcm_mod->getAllRegIds($dept,$usn); 
    if($usn!="ALL") $this->onlyusn=1;
    $message = $this->input->post('message');
    $chunks = array_chunk($result,1000); //broken the array into chunks of 1000 ids because gcm can only send message to 1000 ids at a time

    $status = $this->sendNotification($chunks,$message,$this->newpost,$mid);
    

    if(!empty($status) && is_array($status)){
        foreach($status as $key=>$row){

            if(array_key_exists('canonical_ids',$row) && $row['canonical_ids'] > 0){
                $canonical_ids = $row['canonical_ids'];

                if(array_key_exists('results',$row) && !empty($row['results']) && is_array($row['results'])){
                    foreach($row['results'] as $k=>$v){
                        if(array_key_exists('registration_id',$v)){

                            $userid = array_search($chunks[$key][$k], $result);
                            $newgcmid  = $v['registration_id'];

                            $this->gcm_mod->updateGCMId($userid,$newgcmid);
                        }
                        if(array_key_exists('error',$v)){

                            $userid = array_search($chunks[$key][$k], $result);
                            $error= $v['error'];
                            if($error=="NotRegistered"){
                            $this->gcm_mod->deleteGCMId($userid);
                            }
                        }
                    }
                }
            }
        }
    }
    
    
	return json_encode($status);
}
	private function sendNotification($regids,$message,$newpost=7,$mid){
    $status = array();
    $result = $regids;
    if($result){
        foreach($result as $thousandids){
            $registatoin_ids=$thousandids;

            $msg=array("price"=>$message,"PostType"=>$newpost,"PostId"=>$mid);



            $url='https://android.googleapis.com/gcm/send';
            $fields=array
             (
              'registration_ids'=>$registatoin_ids,
              'data'=>$msg
             );
            $headers=array
             (
              'Authorization: key=AIzaSyCQciRJRgc21pFt3f57GZeT_eIYlFzEXIE',
              'Content-Type: application/json'
             );
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
            $result=curl_exec($ch);
            curl_close($ch);


            $result = json_decode($result,1);

            $status[] = $result;
		
        }
    }

    return $status;
}

	public function index()
	{
	$this->load->model('gcm_mod');
        $this->load->helper('form');
        $data['wrong']=0;
        $data['success']=0;
        $num = $this->gcm_mod->allUsers();
        $this->load->library('form_validation');
     	$username=$this->input->post('username');
     	$password=$this->input->post('password');
     	$message =$this->input->post('message');
     	$delete=$this->input->post('delete');
     	$data['num']=$num;
     	$data['deleted']=FALSE;
     	$data['delenum']=0;
     	$data['result']=0;
     	$data['fail']=0;
     	if($delete=="doit")
     	{
     		$count=$this->gcm_mod->deleteSame();
     		$data['deleted']=TRUE;
     		$data['delenum']=$count;
     		$this->load->view('notifier/sender', $data);
     	
     	}
     	else
     	{
     	if(($username=="talentspear"  && $password=="talentspear_university") && $message!=FALSE)
     	{
     		$jso=json_decode($this->sendtoall());
     		$data['result']=$jso[0]->success;
     		$data['fail']=$jso[0]->failure;
     		$data['success']=1;
     		if($this->onlyusn==1)
     		$this->load->view('notifier/result', $data);
     		else if($this->formnotify==1)
     		$this->load->view('notifier/result', $data);
     		else
     		$this->load->view('notifier/sender', $data);
     	}
     	else
     	{
     		
     		
     		
     		if($message!=FALSE)
     		$data['wrong']=1;
     		$this->load->view('notifier/sender', $data);
     	}
     	}
    	}
    	

}