<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_m extends MY_Model {
    protected $_table_name = 'users';
    protected $_primary_key = 'user_id';
    protected $_primary_filter = 'intval';
    protected $_timestamps = FALSE;

    public function username_exists($username, $exclude_user_id){
        return $this->db->where('username', $username)
            ->where('user_id !=', (int) $exclude_user_id)
            ->count_all_results($this->_table_name) > 0;
    }

    public function email_exists($email, $exclude_user_id){
        return $this->db->where('email', $email)
            ->where('user_id !=', (int) $exclude_user_id)
            ->count_all_results($this->_table_name) > 0;
    }
}