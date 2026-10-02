<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    protected $data = [];

    // Controller yang tidak perlu dicek hak akses menunya
    protected $_skip_access_check = ['dashboard', 'error_page'];

    public function __construct() {
        parent::__construct();

        $this->load->library('session');

        // 1. Wajib login
        if (!$this->session->userdata('user_id')) {
            $this->_deny(401, 'Sesi berakhir, silakan login kembali.', 'auth');
        }

        // 2. Cek hak akses menu
        $controller = strtolower($this->router->fetch_class());
        $method     = strtolower($this->router->fetch_method());

        if (!in_array($controller, $this->_skip_access_check, true)) {
            $this->load->model('Menu_model');
            if (!$this->Menu_model->has_view_access_for_route(
                $controller,
                $method,
                $this->session->userdata('role_id'),
                $this->session->userdata('role')
            )) {
                $this->_deny(403, 'Anda tidak punya akses ke halaman ini.', 'error_page/forbidden');
            }
        }

        // 3. Selalu inject info user ke semua view
        $this->data['logged_user'] = [
            'user_id'     => $this->session->userdata('user_id'),
            'employee_id' => $this->session->userdata('employee_id'),
            'username'    => $this->session->userdata('username'),
            'email'       => $this->session->userdata('email'),
            'role_id'     => $this->session->userdata('role_id'),
            'role'        => $this->session->userdata('role'),
            'role_name'   => $this->session->userdata('role_name'),
        ];
        $this->data['user_role'] = $this->session->userdata('role');
    }

    protected function render($view, $title = '') {
        $this->data['title'] = $title;
        $this->load->view('templates/header', $this->data);
        $this->load->view($view, $this->data);
        $this->load->view('templates/footer');
    }

    protected function only_admin() {
        $role    = $this->session->userdata('role');
        $role_id = $this->session->userdata('role_id');
        if ($role !== 'admin' && $role_id != 1) {
            $this->_deny(403, 'Anda tidak punya akses ke halaman ini.', 'error_page/forbidden');
        }
    }

    protected function only_staff_or_admin() {
        $role    = $this->session->userdata('role');
        $role_id = $this->session->userdata('role_id');
        if (!in_array($role, ['admin', 'staff']) && $role_id != 1) {
            $this->_deny(403, 'Anda tidak punya akses ke halaman ini.', 'error_page/forbidden');
        }
    }

    // Tolak akses: JSON untuk AJAX, redirect untuk request biasa
    protected function _deny($code, $message, $redirect_to) {
        if ($this->input->is_ajax_request()) {
            $this->output
                ->set_status_header($code)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => $message]))
                ->_display();
            exit;
        }

        redirect($redirect_to);
    }
}