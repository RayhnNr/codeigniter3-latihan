<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Budgeting_model extends MY_Model{

    protected $_table_name = 'budgeting';

    protected $_primary_key = 'budgeting_id';

    protected $_primary_filter = 'intval';

    protected $_timestamps = FALSE;

    public $rules = array(
        array('field' => 'budget_date',   'label' => 'Tanggal',     'rules' => 'required'),
        array('field' => 'employee_id',   'label' => 'Karyawan',    'rules' => 'required|integer'),
        array('field' => 'department_id', 'label' => 'Departemen',  'rules' => 'required|integer'),
        array('field' => 'description',   'label' => 'Keterangan',  'rules' => 'trim'),
    );

    public $payment_types = array('Tunai', 'Transfer', 'Giro', 'Kartu Kredit', 'Kas Kecil');

    function __construct()
    {
        parent::__construct();
    }

    public function generate_budgeting_no(){
        $year = date('Y');
        $col = $this->db->field_exists('budget_no', $this->_table_name) ? 'budget_no' : 'budgeting_no';
        $this->db->like($col, 'BDG-'.$year.'-', 'after');
        $this->db->order_by($this->_primary_key, 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->_table_name);
        $last = $query->row();

        if($last){
            $val = $last->$col;
            $last_number = (int) substr($val, -4);
            $next_number = $last_number + 1;
        }else{
            $next_number = 1;
        }

        $formatted = str_pad($next_number, 4, '0', STR_PAD_LEFT);

        return 'BDG-' . $year . '-' . $formatted;
    }


}