<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perizinan_tipe_m extends MY_Model
{

    protected $_table_name = 'ap_perizinan_tipe';

    protected $_primary_key = 'id_perizinan_tipe';

    protected $_primary_filter = 'intval';

    protected $_timestamps = FALSE;

    public $rules = array();

    function __construct()
    {
        parent::__construct();
    }
}