<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perizinan_model extends MY_Model{
    
    protected $_table_name = 'perizinan';

    protected $_primary_key = 'perizinan_id';

    protected $_primary_filter = 'intval';
    protected $_timestamps = FALSE;

    public function __construct() {
        parent::__construct();
    }

    public function generate_perizinan_no(){
        $year = substr(date('Y'), -2);
        $month = date('m');
        $this->db->like('perizinan_no', 'PR'.$year.$month, 'after');
        $this->db->order_by('perizinan_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->_table_name);
        $last = $query->row();


        if($last){
            $last_number = (int) substr($last->perizinan_no, -6);
            $next_number = $last_number + 1;
        }else{
            $next_number = 1;
        }

        $formatted = str_pad($next_number, 6, '0', STR_PAD_LEFT);

        return 'PR'.$year.$month.$formatted;
    }


    public function get_status_id_by_name($name){
        $this->db->select('product_status_id');
        $this->db->from('product_status');
        $this->db->where('module', 'perizinan');
        $this->db->where('LOWER(product_status_name)', strtolower($name));
        $query = $this->db->get();
        return $query->row() ? $query->row()->product_status_id : null;
    }

    public function get_status(){
        $this->db->where('module','perizinan');
        return $this->db->get('product_status')->result();
    }

    public function get_jenis_perizinan(){
        return $this->db->get_where('jenis_perizinan', ['status' => 1])->result();
    }
}