<?php
// BARU — dipanggil oleh Journal controller dan controller lain yang butuh status
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_status_model extends MY_Model
{
    protected $_table_name  = 'product_status';
    protected $_primary_key = 'product_status_id';

    // Tidak ada rules khusus; tabel ini hanya dibaca oleh modul lain
    public $rules = [];

    public function __construct()
    {
        parent::__construct();
    }
}
