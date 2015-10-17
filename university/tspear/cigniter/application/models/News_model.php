<?php
class News_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }
 public function get_news($slug = FALSE)
{
        if ($slug === FALSE)
        {
                $query = $this->db->get('gcm_users');
                return $query->result_array();
        }

        $query = $this->db->get_where('gcm_users', array('email' => $slug));
        return $query->row_array();
}


}