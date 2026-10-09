<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_order_detail_model extends MY_Model{
    protected $_table_name  = 'sales_order_detail';
    protected $_primary_key = 'sales_order_detail_id';

    public $rules = array(
        array(
            'field' => 'product_id[]', 
            'label' => 'Produk', 
            'rules' => 'trim|required|integer'
        ),
        array(
            'field' => 'qty[]',
            'label' => 'Qty',
            'rules' => 'trim|required|numeric|greater_than[0]'
        ),
        array(
            'field' => 'unit_price[]',
            'label' => 'Harga',
            'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
        ),
        array(
            'field' => 'discount_percent[]',
            'label' => 'Diskon',
            'rules' => 'trim|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
        ),

    );

    // public function get_new(){
    //     $so = new stdClass();
    //     $so->sales_order_id = '';
    //     $so_detail->product_id = '';
    //     $so_detail->qty = 0;
    //     $so_detail->unit_price = 0;
    //     $so_detail->discount_percent = 0;

    //     return $so_detail;
    // }
}