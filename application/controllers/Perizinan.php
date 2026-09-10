<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perizinan extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Perizinan_model');
        $this->load->model('Jenis_perizinan_model');
        $this->load->library('form_validation');
    }

    public function index(){
        $data['title'] = 'Data Perizinan';
        $data['jenis_perizinan'] = $this->Perizinan_model->get_jenis_perizinan();
        $data['status'] = $this->Perizinan_model->get_status();
        $data['active_status_id'] = $this->Jenis_perizinan_model->get_status_id_by_name('Aktif');
        $data['inactive_status_id'] = $this->Jenis_perizinan_model->get_status_id_by_name('Nonaktif');
        $this->load->view('templates/header', $data);
        $this->load->view('perizinan/index', $data);
        $this->load->view('templates/footer');
        $this->load->view('perizinan/js', $data);
    }

    public function get_data(){
        $user_id = $this->session->userdata('user_id');
        $this->db->select('perizinan.*, jenis_perizinan.jenis_perizinan_name, employees.employee_name, product_status.product_status_name');
        $this->db->from('perizinan');
        $this->db->join('jenis_perizinan', 'jenis_perizinan.jenis_perizinan_id = perizinan.jenis_perizinan_id', 'left');
        $this->db->join('employees', 'employees.employee_id = perizinan.employee_id', 'left');
        $this->db->join('product_status', 'product_status.product_status_id = perizinan.status', 'left');
        $this->db->join('users', 'users.employee_id = perizinan.employee_id', 'left');
        $this->db->where('users.user_id', $user_id);
        $this->db->order_by('perizinan.perizinan_id', 'DESC');
        $rows = $this->db->get()->result();

        $data = [];
        foreach ($rows as $index => $row) {
            $data[] = [
                'no' => $index + 1,
                'perizinan_id' => (int) $row->perizinan_id,
                'perizinan_no' => $row->perizinan_no,
                'jenis_perizinan_name' => $row->jenis_perizinan_name,
                'employee_name' => $row->employee_name,
                'tanggal_pengajuan' => $row->tanggal_pengajuan,
                'tanggal_mulai' => $row->tanggal_mulai,
                'tanggal_selesai' => $row->tanggal_selesai,
                'product_status_name' => $row->product_status_name
            ];
        }

        echo json_encode(['data' => $data]);
    }

    public function create(){
        $user_id = $this->session->userdata('user_id');

        $data['title'] = 'Tambah Perizinan';
        $data['jenis_perizinan'] = $this->Perizinan_model->get_jenis_perizinan();
        $data['status'] = $this->Perizinan_model->get_status();
        $data['pending_status_id'] = $this->Perizinan_model->get_status_id_by_name('Pending');

        $this->db->select('users.employee_id, employees.employee_name');
        $this->db->from('users');
        $this->db->join('employees', 'employees.employee_id = users.employee_id', 'left');
        $this->db->where('users.user_id', $user_id);
        $user = $this->db->get()->row();

        $data['employee_name'] = $user ? $user->employee_name : '-';

        $this->load->view('templates/header', $data);
        $this->load->view('perizinan/form', $data);
        $this->load->view('templates/footer');
        $this->load->view('perizinan/js', $data);
    }

    public function edit($id){
        $data['title'] = 'Edit Perizinan';
        $data['perizinan'] = $this->Perizinan_model->get($id, TRUE);

        if (!$data['perizinan']) {
            $this->session->set_flashdata('error', 'Perizinan tidak ditemukan.');
            redirect('perizinan');
            return;
        }
        
        $data['jenis_perizinan'] = $this->Perizinan_model->get_jenis_perizinan();
        $data['status'] = $this->Perizinan_model->get_status();
        $data['pending_status_id'] = $this->Perizinan_model->get_status_id_by_name('Pending');
        $data['active_status_id'] = $this->Jenis_perizinan_model->get_status_id_by_name('Aktif');
        $data['inactive_status_id'] = $this->Jenis_perizinan_model->get_status_id_by_name('Nonaktif');
        $this->load->view('templates/header', $data);
        $this->load->view('perizinan/edit', $data);
        $this->load->view('templates/footer');
        $this->load->view('perizinan/js', $data);
    }

    public function save(){

        $perizinan_id = $this->input->post('perizinan_id');

        $jenis_perizinan_id = (int) $this->input->post('jenis_perizinan_id');
        $is_single_day = in_array($jenis_perizinan_id, [6, 7, 8], TRUE);
        $is_time_based = in_array($jenis_perizinan_id, [6, 7, 8], TRUE);

        $this->form_validation->set_rules('jenis_perizinan_id', 'Jenis Perizinan', 'required|numeric');
        $this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'required|trim');
        $this->form_validation->set_rules('alasan', 'Alasan', 'trim|required');

        if (!$is_single_day) {
            $this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'required|trim');
        } else {
            $this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'trim');
        }

        $jam_mulai = $this->input->post('jam_mulai', TRUE);
        $jam_selesai = $this->input->post('jam_selesai', TRUE);

        if ($is_time_based) {
            $this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'trim|required');
            $this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'trim|required');
        } else {
            $this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'trim');
            $this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'trim');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('perizinan');
            return;
        }

        $existing = null;
        if (!empty($perizinan_id)) {
            $existing = $this->Perizinan_model->get($perizinan_id);
            if (!$existing) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Data perizinan tidak ditemukan.'
                ]);
                return;
            }
        }

        // Validasi jam untuk Get Pass, Pulang Awal, dan Datang Terlambat
        if ($is_time_based) {
            $errors = [];

            if ($jam_mulai < '08:00' || $jam_mulai > '16:00') {
                $errors['jam_mulai'] = 'Jam mulai harus berada dalam jam kantor (08:00 - 16:00).';
            }

            if ($jam_selesai < '08:00' || $jam_selesai > '16:00') {
                $errors['jam_selesai'] = 'Jam selesai harus berada dalam jam kantor (08:00 - 16:00).';
            }

            if ($jam_mulai && $jam_selesai && $jam_mulai >= $jam_selesai) {
                $errors['jam_selesai'] = 'Jam selesai harus lebih besar dari jam mulai.';
            }

            if (!empty($errors)) {
                echo json_encode(['status' => 'error', 'errors' => $errors]);
                return;
            }
        }

        $attachment_filename = null;

        if (!empty($_FILES['attachment']['name'])) {
            $upload_path = './uploads/perizinan/';

            if (!is_dir($upload_path) && !mkdir($upload_path, 0755, TRUE)) {
                $this->session->set_flashdata('error', 'Folder upload lampiran tidak dapat dibuat.');
                redirect('perizinan');
                return;
            }

            $config['upload_path']      = $upload_path;
            $config['allowed_types']    = 'jpg|jpeg|png|pdf';
            $config['max_size']         = 2048; // 2MB
            $config['encrypt_name']     = TRUE;
            $config['file_ext_tolower'] = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('attachment')) {
                $upload_data         = $this->upload->data();
                $attachment_filename = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('perizinan');
                return;
            }

        } elseif (!empty($perizinan_id)) {
            // mode EDIT, tidak upload file baru -> pertahankan attachment lama
            $attachment_filename = $existing ? $existing->attachment : null;
        }

        // Mengambil employee dari table users
        $user_id = $this->session->userdata('user_id');

        $this->db->select('employee_id');
        $this->db->where('user_id', $user_id);
        $user = $this->db->get('users')->row();

        $employee_id = $user ? $user->employee_id : null;

        if (!$employee_id) {
            $this->session->set_flashdata('error', 'Employee dari user yang login tidak ditemukan.');
            redirect('perizinan');
            return;
        }

        $tanggal_mulai   = $this->input->post('tanggal_mulai', TRUE);
        $tanggal_selesai = $this->input->post('tanggal_selesai', TRUE);

        // Field yang tidak berlaku untuk jenis perizinan disimpan sebagai NULL.
        if ($is_single_day) {
            $tanggal_selesai = null;
        }

        if (!$is_time_based) {
            $jam_mulai = null;
            $jam_selesai = null;
        }

        // Pengecekan tanggal, tanggal tidak boleh sebelum hari ini.
        $tanggal_hari_ini = date('Y-m-d');
        $errors = [];

        if ($tanggal_mulai < $tanggal_hari_ini) {
            $errors['tanggal_mulai'] = 'Tanggal mulai tidak boleh sebelum hari ini.';
        }

        if (!$is_single_day && $tanggal_selesai < $tanggal_hari_ini) {
            $errors['tanggal_selesai'] = 'Tanggal selesai tidak boleh sebelum hari ini.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

        // Pengecekan tanggal mulai harus <= tanggal selesai
        if (!$is_single_day && $tanggal_mulai > $tanggal_selesai) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.'
            ]);
            return;
        }

        // Pengecekan tanggal yang bentrok
        $this->db->where('employee_id', $employee_id);
        $tanggal_bentrok_selesai = $tanggal_selesai ?: $tanggal_mulai;
        $this->db->where('tanggal_mulai <=', $tanggal_bentrok_selesai);
        $this->db->where('COALESCE(tanggal_selesai, tanggal_mulai) >=', $tanggal_mulai);

        if (!empty($perizinan_id)) {
            $this->db->where('perizinan_id !=', $perizinan_id);
        }

        $cek = $this->db->get('perizinan')->row();

        // Bentrok? -> jika status Pending / Approved, pengajuan ditolak, pilih tanggal lain
        // Bentrok? -> jika status Rejected -> pengajuan boleh diajukan
        if ($cek) {
            $pending_status_id  = $this->Perizinan_model->get_status_id_by_name('Pending');
            $approved_status_id = $this->Perizinan_model->get_status_id_by_name('Approved');

            if ($cek->status == $pending_status_id || $cek->status == $approved_status_id) {
                $tanggal_mulai_bentrok   = date('d-m-Y', strtotime($cek->tanggal_mulai));
                $tanggal_selesai_bentrok = date('d-m-Y', strtotime($cek->tanggal_selesai ?: $cek->tanggal_mulai));

                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Tanggal ' . $tanggal_mulai_bentrok . ' s/d ' . $tanggal_selesai_bentrok . ' sudah diajukan. Silakan pilih tanggal lain.'
                ]);
                return;
            }
        }

        // Tambah
        if (empty($perizinan_id)) {

            $pending_status_id = $this->Perizinan_model->get_status_id_by_name('Pending');

            if (!$pending_status_id) {
                $this->session->set_flashdata('error', 'Status Pending untuk perizinan belum tersedia.');
                redirect('perizinan');
                return;
            }

            $code_perizinan = $this->Perizinan_model->generate_perizinan_no();

            $data = [
                'perizinan_no'       => $code_perizinan,
                'jenis_perizinan_id' => $jenis_perizinan_id,
                'employee_id'        => $employee_id,
                'tanggal_pengajuan'  => date('Y-m-d'),
                'tanggal_mulai'      => $tanggal_mulai,
                'tanggal_selesai'    => $tanggal_selesai,
                'jam_mulai'          => $jam_mulai,
                'jam_selesai'        => $jam_selesai,
                'alasan'             => $this->input->post('alasan', TRUE),
                'attachment'         => $attachment_filename,
                'status'             => $pending_status_id,
                'created_by'         => $user_id,
            ];

        // Edit
        } else {

            $data = [
                'jenis_perizinan_id' => $jenis_perizinan_id,
                'employee_id'        => $employee_id,
                'tanggal_mulai'      => $tanggal_mulai,
                'tanggal_selesai'    => $tanggal_selesai,
                'jam_mulai'          => $jam_mulai,
                'jam_selesai'        => $jam_selesai,
                'alasan'             => $this->input->post('alasan', TRUE),
                'attachment'         => $attachment_filename,
                'status'             => $existing->status,
                'updated_by'         => $user_id,
                'updated_at'         => date('Y-m-d H:i:s'),
            ];
        }

        // Jika ada ID maka update
        $saved_id = $this->Perizinan_model->save($data, !empty($perizinan_id) ? $perizinan_id : null);

        if (!$saved_id) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Data perizinan gagal disimpan.'
            ]);
            return;
        }

        $this->session->set_flashdata('success', 'Data berhasil disimpan.');

        echo json_encode([
            'status' => 'success'
        ]);
        return;
    }

    public function delete($id){
        $this->Perizinan_model->delete($id);
        // hapus attachment
        $attachment = $this->Perizinan_model->get($id)->attachment;
        if ($attachment) {
            unlink('./uploads/perizinan/' . $attachment);
        }
        $this->session->set_flashdata('success', 'Data berhasil dihapus.');
        redirect('perizinan');
    }

}