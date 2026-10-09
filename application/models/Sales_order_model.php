<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_order_model extends MY_Model{
    protected $_table_name  = 'sales_order';
    protected $_primary_key = 'sales_order_id';

    public $rules = array(
        array(
            'field' => 'so_date', 
            'label' => 'Tanggal SO', 
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'customer_id', 
            'label' => 'Customer',
            'rules' => 'trim|required|integer'
        ),
        array(
            'field' => 'delivery_date', 
            'label' => 'Tanggal Pengiriman', 
            'rules' => 'trim'
        ),
        array(
            'field' => 'payment_term_days', 
            'label' => 'Terms Pembayaran', 
            'rules' => 'trim|required|integer|greater_than_equal_to[0]'
        ),
        array(
            'field' => 'tax_percent', 
            'label' => 'Pajak', 
            'rules' => 'trim|required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
        ),
        array(
            'field' => 'notes',
            'label' => 'Catatan',
            'rules' => 'trim|max_length[255]'
        )
    );

    public function generate_so_no(){
        $year = date('Y');
        $this->db->like('so_no', 'SO-'.$year.'-', 'after');
        $this->db->order_by('so_no', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->_table_name);
        $last = $query->row();

        if($last){
            $last_number = (int) substr($last->so_no, -4);
            $next_number = $last_number + 1;
        }else{
            $next_number = 1;
        }

        $formatted = str_pad($next_number, 4, '0', STR_PAD_LEFT);

        return 'SO-'.$year.'-'.$formatted;
    }

    public function get_new(){
        $so = new stdClass();
        $so->sales_order_id = '';
        $so->so_no = '';
        $so->so_date = date('Y-m-d');
        $so->delivery_date = '';
        $so->payment_term_days = 0;
        $so->tax_percent = 11;
        $so->notes = '';
        $so->customer_id = '';
        $so->product_status_id = 26;
        $so->created_by = '';
        return $so;
    }
}