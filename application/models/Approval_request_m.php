<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_request_m extends MY_Model{
    
    protected $_table_name = 'approval_request';

    protected $_primary_key = 'id_approval_request';

    protected $_primary_filter = 'intval';
    protected $_timestamps = FALSE;

    public function __construct() {
        parent::__construct();
    }


    public function get_by_reference($type, $id){
        
        $this->db->where('reference_type', $type);
        $this->db->where('reference_id', $id);
        return $this->db->get($this->_table_name)->result();
    }

    public function update_sequence($id, $new_sequence){
        $this->db->set('sequence', $new_sequence);
        $this->db->where('id_approval_request', $id);
        return $this->db->update($this->_table_name);
    }

    public function update_status($id, $new_status){
        $this->db->set('request_status', $new_status);
        $this->db->where('id_approval_request', $id);
        return $this->db->update($this->_table_name);
    }

    

}