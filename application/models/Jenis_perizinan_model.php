<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jenis_perizinan_model extends MY_Model
{

    protected $_table_name = 'jenis_perizinan';

    protected $_primary_key = 'jenis_perizinan_id';

    protected $_primary_filter = 'intval';

    protected $_timestamps = FALSE;

    public $rules = array();

    function __construct()
    {
        parent::__construct();
    }

    function generate_code($text){
        $text = strtoupper(trim($text));
        $text = preg_replace('/\s+/', '_', $text);

        return $text;
    }

    public function cek_jenis_perizinan($conditions, $exclude_id = null){
        foreach ($conditions as $field => $value) {
            $this->db->where($field, $value);
        }

        if (!empty($exclude_id)) {
            $this->db->where('jenis_perizinan_id !=', $exclude_id);
        }

        return $this->db->count_all_results('jenis_perizinan');
    }

    public function get_status_id_by_name($name){
        $this->db->where('module', 'product');
        $this->db->where('LOWER(product_status_name)', strtolower($name));
        $row = $this->db->get('product_status')->row();
        return $row ? $row->product_status_id : null;
    }

    public function get_status(){
        $this->db->where('module','product');
        return $this->db->get('product_status')->result();
    }
}