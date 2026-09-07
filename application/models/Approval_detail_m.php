<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_detail_m extends MY_Model{
    
    protected $_table_name = 'approval_detail';

    protected $_primary_key = 'id_approval_detail';

    protected $_primary_filter = 'intval';
    protected $_timestamps = FALSE;

    public function __construct() {
        parent::__construct();
    }

    

}