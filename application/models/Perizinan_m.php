<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perizinan_m extends MY_Model{
    
    protected $_table_name = 'perizinan';

    protected $_primary_key = 'id_perizinan';

    protected $_primary_filter = 'intval';
    protected $_timestamps = FALSE;

    public function __construct() {
        parent::__construct();
    }

    public function generate_code(){
        $this->db->select('RIGHT(perizinan.perizinan_code,4) as kode', FALSE);
        $this->db->order_by('perizinan_code','DESC');
        $this->db->limit(1);
        $query = $this->db->get('perizinan');      //cek dulu apakah ada sudah ada kode di tabel.    
        if($query->num_rows() <> 0){      
         //jika kode ternyata sudah ada.      
         $data = $query->row();      
         $kode = intval($data->kode) + 1;    
        }
        else {      
         //jika kode belum ada      
         $kode = 1;    
        }
        $kodemax = str_pad($kode, 4, "0", STR_PAD_LEFT); // angka 4 menunjukkan jumlah digit angka 0
        $kodejadi = "PZ".$kodemax;    // hasilnya ODJ-9921-0001 dst.
        return $kodejadi;
    }


    public function get_status_id_by_name($name){
        $this->db->select('product_status_id');
        $this->db->from('product_status');
        $this->db->where('module', 'perizinan');
        $this->db->where('LOWER(product_status_name)', strtolower($name));
        $query = $this->db->get();
        return $query->row() ? $query->row()->product_status_id : null;
    }


    public function get_with_join($id){
        $this->db->select('perizinan.*, ap_perizinan_tipe.perizinan_tipe_name');
        $this->db->from('perizinan');
        $this->db->join('ap_perizinan_tipe', 'perizinan.id_perizinan_tipe = ap_perizinan_tipe.id_perizinan_tipe', 'left');
        $this->db->where('perizinan.id_perizinan', $id);
        return $this->db->get()->row();
    }

    public function get_data_by_user($user_id){
        
        $this->db->select('perizinan.*, ap_perizinan_tipe.perizinan_tipe_name');
        $this->db->from('perizinan');
        $this->db->join('ap_perizinan_tipe', 'perizinan.id_perizinan_tipe = ap_perizinan_tipe.id_perizinan_tipe', 'left');
        $this->db->where('perizinan.user_id', $user_id);
        return $this->db->get()->result();
    }



}