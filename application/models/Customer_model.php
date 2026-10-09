<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends MY_Model
{
    protected $_table_name  = 'customers';
    protected $_primary_key = 'customer_id';

    public $rules = array(
        array(
            'field' => 'customer_name', 
            'label' => 'Nama Pelanggan', 
            'rules' => 'trim|required|max_length[150]'),
        array(
            'field' => 'contact_person', 
            'label' => 'Contact Person',
            'rules' => 'trim|required|max_length[100]'
        ),
        array(
            'field' => 'address', 
            'label' => 'Alamat', 
            'rules' => 'trim|max_length[255]'
        ),
        array(
            'field' => 'phone', 
            'label' => 'No. Telepon', 
            'rules' => 'trim|max_length[20]'
        ),
        array(
            'field' => 'email', 
            'label' => 'Email', 
            'rules' => 'trim|valid_email|max_length[100]'
        ),
        array(
            'field' => 'payment_term_days',
            'label' => 'Terms Pembayaran',
            'rules' => 'trim|required|integer'
        ),
        array(
            'field' => 'credit_limit',
            'label' => 'Limit Kredit',
            'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
        )
    );

    public function generate_customer_code(){
        $this->db->like('customer_code', 'CUST-', 'after');
        $this->db->order_by('customer_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->_table_name);
        $last = $query->row();

        if($last){
            $last_number = (int) substr($last->customer_code, -4);
            $next_number = $last_number + 1;
        }else{
            $next_number = 1;
        }

        $formatted = str_pad($next_number, 4, '0', STR_PAD_LEFT);

        return 'CUST-'.$formatted;
    }

    public function get_new(){
        $customer = new stdClass();
        $customer->customer_name = '';
        $customer->contact_person = '';
        $customer->address = '';
        $customer->phone = '';
        $customer->email = '';
        $customer->payment_term_days = 0;
        $customer->credit_limit = 0;
        $customer->is_active = 1;
        return $customer;
    }




}