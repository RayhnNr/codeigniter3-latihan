<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coa_model extends MY_Model
{
    protected $_table_name  = 'coa';
    protected $_primary_key = 'coa_id';

    public $rules = array(
        array('field' => 'coa_name', 'label' => 'Nama Akun', 'rules' => 'trim|required|max_length[150]'),
    );

    public function generate_coa_code($parent_id)
    {
        $parent = $this->get($parent_id, TRUE);
        if (!$parent || !$parent->is_header || !preg_match('/^[A-Za-z0-9]-[0-9]{4}$/D', $parent->coa_code)) {
            return NULL;
        }

        $this->db->where('parent_id', $parent_id);
        $this->db->order_by('coa_code', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get($this->_table_name)->row();

        $last_code = $last ? $last->coa_code : $parent->coa_code;
        if (
            !preg_match('/^[A-Za-z0-9]-[0-9]{4}$/D', $last_code)
            || substr($last_code, 0, 1) !== substr($parent->coa_code, 0, 1)
        ) {
            return NULL;
        }

        $num = (int) substr($last_code, 2) + 100;
        if ($num > 9900) {
            return NULL;
        }

        return substr($parent->coa_code, 0, 1) . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}