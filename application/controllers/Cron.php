<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Perizinan_model');
    }

    public function perizinan_expire(){
        if (!is_cli()) {
            show_error('Cron hanya dapat dijalankan melalui CLI.', 403);
        }

        $expired_count = $this->Perizinan_model->expire_pending();
        echo 'Perizinan expired: ' . $expired_count . PHP_EOL;
    }
}