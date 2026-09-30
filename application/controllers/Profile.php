<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->model('Users_m');
        $this->load->model('Employee_m');
        $this->load->library('form_validation');
    }

    public function index(){
        $user_id = $this->session->userdata('user_id');
        $user    = $this->Auth_model->get_by_id($user_id);

        if (!$user){
            $this->session->set_flashdata('error', 'Data user tidak ditemukan.');
            redirect('dashboard');
        }

        $this->data['title'] = 'Profile Saya';
        $this->data['user']  = $user;
        $this->data['employee'] = !empty($user->employee_id) ? $this->Employee_m->get($user->employee_id, TRUE) : NULL;
        $this->load->view('templates/header', $this->data);
        $this->load->view('profile/index', $this->data);
        $this->load->view('templates/footer');
    }

    public function change_password(){
        header('Content-Type: application/json');

        $user_id = $this->session->userdata('user_id');
        $user    = $this->Auth_model->get_by_id($user_id);

        if (!$user){
            echo json_encode(['status' => false, 'message' => 'User tidak ditemukan.']);
            return;
        }

        $this->form_validation->set_rules('current_password', 'Password Saat Ini', 'required');
        $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[new_password]', [
            'matches' => 'Konfirmasi password tidak sama dengan password baru.'
        ]);

        if ($this->form_validation->run() == FALSE){
            echo json_encode(['status' => false, 'errors' => $this->form_validation->error_array()]);
            return;
        }

        $current = $this->input->post('current_password');
        if (!password_verify($current, $user->password)){
            echo json_encode(['status' => false, 'errors' => ['current_password' => 'Password saat ini tidak sesuai.']]);
            return;
        }

        $new_hashed = password_hash($this->input->post('new_password'), PASSWORD_DEFAULT);
        $this->Auth_model->update_password($user_id, $new_hashed);

        echo json_encode(['status' => true, 'message' => 'Password berhasil diubah.']);
    }

    public function update(){
        $this->output->set_content_type('application/json');

        $user_id = (int) $this->session->userdata('user_id');
        $user = $this->Users_m->get($user_id, TRUE);
        if (!$user || empty($user->employee_id)) {
            echo json_encode(['status' => false, 'errors' => ['profile' => 'Data user atau employee tidak ditemukan.']]);
            return;
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[4]|max_length[50]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[255]');
        $this->form_validation->set_rules('nomor_hp', 'Nomor HP', 'trim|max_length[20]');
        $this->form_validation->set_rules('employee_name', 'Nama', 'required|trim|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => false, 'errors' => $this->form_validation->error_array()]);
            return;
        }

        $username = trim($this->input->post('username', TRUE));
        $email = trim($this->input->post('email', TRUE));
        $nomor_hp = trim((string) $this->input->post('nomor_hp', TRUE));
        $employee_name = trim($this->input->post('employee_name', TRUE));

        $errors = [];
        if ($this->Users_m->username_exists($username, $user_id)) {
            $errors['username'] = 'Username sudah digunakan.';
        }
        if ($this->Users_m->email_exists($email, $user_id)) {
            $errors['email'] = 'Email sudah digunakan.';
        }
        if ($errors) {
            echo json_encode(['status' => false, 'errors' => $errors]);
            return;
        }

        $photo_path = FCPATH . 'uploads/employees/photo/';
        if (!is_dir($photo_path) && !mkdir($photo_path, 0755, TRUE) && !is_dir($photo_path)) {
            echo json_encode(['status' => false, 'errors' => ['photo' => 'Folder foto tidak dapat dibuat.']]);
            return;
        }

        $old_employee = $this->Employee_m->get($user->employee_id, TRUE);
        if (!$old_employee) {
            echo json_encode(['status' => false, 'errors' => ['profile' => 'Data employee tidak ditemukan.']]);
            return;
        }

        $photo = $old_employee->photo;
        $new_photo = NULL;
        if (!empty($_FILES['photo']['name'])) {
            $config = [
                'upload_path' => $photo_path,
                'allowed_types' => 'jpg|jpeg|png',
                'max_size' => 2048,
                'encrypt_name' => TRUE,
                'file_ext_tolower' => TRUE
            ];
            $this->load->library('upload');
            $this->upload->initialize($config, TRUE);
            if (!$this->upload->do_upload('photo')) {
                echo json_encode(['status' => false, 'errors' => ['photo' => strip_tags($this->upload->display_errors('', ''))]]);
                return;
            }
            $new_photo = $this->upload->data('file_name');
            $photo = $new_photo;
        }

        $this->db->trans_begin();
        $this->Users_m->save([
            'username' => $username,
            'email' => $email,
            'nomor_hp' => $nomor_hp
        ], $user_id);
        $this->Employee_m->save([
            'employee_name' => $employee_name,
            'photo' => $photo
        ], $user->employee_id);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($new_photo && is_file($photo_path . $new_photo)) {
                @unlink($photo_path . $new_photo);
            }
            echo json_encode(['status' => false, 'errors' => ['profile' => 'Profil gagal disimpan. Silakan coba lagi.']]);
            return;
        }
        $this->db->trans_commit();

        if ($new_photo && !empty($old_employee->photo)) {
            $old_photo_path = $photo_path . basename($old_employee->photo);
            if (is_file($old_photo_path)) {
                @unlink($old_photo_path);
            }
        }

        echo json_encode(['status' => true, 'errors' => new stdClass()]);
    }
}
