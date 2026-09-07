<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_request_detail_m extends MY_Model{
    
    protected $_table_name = 'approval_request_detail';

    protected $_primary_key = 'id_approval_request_detail';

    protected $_primary_filter = 'intval';
    protected $_timestamps = FALSE;

    public function __construct() {
        parent::__construct();
    }

    public function copy_from_template($approval_request_id, $approval_id, $pending_status_id){
        
        $this->db->select('approval_detail.*');
        $this->db->from('approval_detail');
        $this->db->where('approval_id', $approval_id);
        $query = $this->db->get();
        $details = $query->result();

        foreach($details as $detail){
            $data = array(
                'approval_request_id' => $approval_request_id,
                'approval_detail_name' => $detail->approval_detail_name,
                'sequence' => $detail->sequence,
                'request_status' => $pending_status_id
            );
            $this->insert($data);
        }
    }

    public function get_by_request($approval_request_id){
        $this->db->where('approval_request_id', $approval_request_id);
        return $this->db->get($this->_table_name)->result();
    }

    public function get_current_approver_row($approval_request_id, $sequence){
        $this->db->where('approval_request_id', $approval_request_id);
        $this->db->where('sequence', $sequence);
        return $this->db->get($this->_table_name)->row();
    }

    public function is_last_sequence($approval_request_id, $sequence){
        $this->db->where('approval_request_id', $approval_request_id);
        $this->db->where('sequence >', $sequence);
        return $this->db->get($this->_table_name)->num_rows() == 0;
    }


    public function update_approve($id, $approved_status_id, $note){
        $this->db->set('status', $approved_status_id);
        $this->db->set('notes', $note);
        $this->db->where('id_approval_request_detail', $id);
        return $this->db->update($this->_table_name);
    }


    public function update_reject($id, $rejected_status_id, $note){
        $this->db->set('status', $rejected_status_id);
        $this->db->set('notes', $note);
        $this->db->where('id_approval_request_detail', $id);
        return $this->db->update($this->_table_name);
    }

    // 17=Pending, 18=Approved, 19=Rejected
    public function get_pending_for_user($user_id){
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 17);
        return $this->db->get($this->_table_name)->result();
    }

    

}