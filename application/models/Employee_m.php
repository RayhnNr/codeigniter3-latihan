<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_m extends MY_Model {
    protected $_table_name = 'employees';
    protected $_primary_key = 'employee_id';
    protected $_primary_filter = 'intval';
    protected $_timestamps = FALSE;
}