<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brands_model extends MY_Model{

    protected $_table_name = 'brands';

    protected $_primary_key = 'brand_id';

    protected $_primary_filter = 'intval';

    protected $_timestamps = FALSE;

    public $rules = array();

    function __construct()
    {
        parent::__construct();
    }

    // fungsi tambahan untuk ambil daftar status (dropdown)
    public function get_status_options(){
        $this->db->where('module', 'product');
        $this->db->order_by('product_status_id', 'ASC');
        return $this->db->get('product_status')->result();
    }

    // fungsi tambahan untuk ambil ID status by name
    public function get_status_id_by_name($name){
        $this->db->where('module', 'product');
        $this->db->where('LOWER(product_status_name)', strtolower($name));
        $row = $this->db->get('product_status')->row();
        return $row ? $row->product_status_id : null;
    }

    // override get_all() supaya ikut JOIN nama status (opsional, kalau perlu tampilkan nama status di tabel)
    public function get_all_with_status(){
        $this->db->select('brands.*, product_status.product_status_name');
        $this->db->from($this->_table_name);
        $this->db->join('product_status', 'product_status.product_status_id = brands.status', 'left');
        return $this->db->get()->result();
    }
}