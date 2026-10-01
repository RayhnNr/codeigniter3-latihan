<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Budgeting_detail_model extends MY_Model {

    protected $_table_name = 'budgeting_detail';

    protected $_primary_key = 'budgeting_detail_id';

    protected $_primary_filter = 'intval';

    protected $_timestamps = FALSE;

    public $rules = array(
        array(
            'field' => 'item_description[]',
            'label' => 'Deskripsi Item',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'payment_type[]',
            'label' => 'Jenis Pembayaran',
            'rules' => 'required|trim|in_list[Tunai,Transfer,Giro,Kartu Kredit,Kas Kecil]'
        ),
        array(
            'field' => 'qty[]',
            'label' => 'Qty',
            'rules' => 'required|numeric|greater_than[0]'
        ),
        array(
            'field' => 'unit_price[]',
            'label' => 'Harga Satuan',
            'rules' => 'required|numeric|greater_than_equal_to[0]'
        ),
    );

    public function __construct()
    {
        parent::__construct();
    }
}
