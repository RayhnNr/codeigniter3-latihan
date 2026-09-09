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
        $data['title'] = 'Tambah Perizinan';
        $data['jenis_perizinan'] = $this->Perizinan_model->get_jenis_perizinan();
        $data['status'] = $this->Perizinan_model->get_status();
        $data['pending_status_id'] = $this->Perizinan_model->get_status_id_by_name('Pending');
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

        $this->form_validation->set_rules('jenis_perizinan_id', 'Jenis Perizinan', 'required|numeric');
        $this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'required|trim');
        $this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'required|trim');
        $this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'trim');
        $this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'trim');
        $this->form_validation->set_rules('alasan', 'Alasan', 'trim|required');
        // $this->form_validation->set_rules('attachment', 'Attachment', 'trim');

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
                    'status' => 'error',
                    'message' => 'Data perizinan tidak ditemukan.'
                ]);
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

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf';
            $config['max_size'] = 2048; // 2MB
            $config['encrypt_name'] = TRUE;
            $config['file_ext_tolower'] = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('attachment')) {
                $upload_data = $this->upload->data();
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


        $tanggal_mulai = $this->input->post('tanggal_mulai', TRUE);
        $tanggal_selesai = $this->input->post('tanggal_selesai', TRUE);


        // Pengecakan tanggal, tanggal harus > tanggal sekarang
        if (empty($perizinan_id) && $tanggal_mulai < date('Y-m-d')) {
            echo json_encode([
                'status' => false,
                'message' => 'Tanggal mulai harus merupakan tanggal di masa depan.'
            ]);
            return;
        }

        // Pengecekan tanggal musali harus < dari tanggal sekarang
        if ($tanggal_mulai > $tanggal_selesai) {
            echo json_encode([
                'status' => false,
                'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.'
            ]);
            return;
        }

        // Pengecekan Tanggal yang bentrol
        $this->db->where('employee_id', $employee_id);
        $this->db->where('tanggal_mulai <=', $tanggal_selesai);
        $this->db->where('tanggal_selesai >=', $tanggal_mulai);

        if (!empty($perizinan_id)) {
            $this->db->where('perizinan_id !=', $perizinan_id);
        }

        $cek = $this->db->get('perizinan')->row();
        
        // Pengecekan Tanggal
        // Bentrok? -> jika status Pending / Approve, Pengajuan Ditolak, pilih tanggal lain,
        // Bentrok? -> Jika status Reject -> pengajuan boleh di ajukan
        if ($cek) {
            $pending_status_id = $this->Perizinan_model->get_status_id_by_name('Pending');
            $approved_status_id = $this->Perizinan_model->get_status_id_by_name('Approved');

            if ($cek->status == $pending_status_id || $cek->status == $approved_status_id) {
                $tanggal_mulai_bentrok = date('d-m-Y', strtotime($cek->tanggal_mulai));
                $tanggal_selesai_bentrok = date('d-m-Y', strtotime($cek->tanggal_selesai));

                echo json_encode([
                    'status' => false,
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
                'perizinan_no' => $code_perizinan,
                'jenis_perizinan_id' => $this->input->post('jenis_perizinan_id', TRUE),
                'employee_id' => $employee_id,
                'tanggal_pengajuan' => date('Y-m-d'),
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'jam_mulai' => $this->input->post('jam_mulai', TRUE),
                'jam_selesai' => $this->input->post('jam_selesai', TRUE),
                'alasan' => $this->input->post('alasan', TRUE),
                'attachment' => $attachment_filename,
                'status' => $pending_status_id,
                'created_by' => $user_id,
            ];

        // Edit
        } else {

            $data = [
                'jenis_perizinan_id' => $this->input->post('jenis_perizinan_id', TRUE),
                'employee_id' => $employee_id,
                'tanggal_pengajuan' => date('Y-m-d'),
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'jam_mulai' => $this->input->post('jam_mulai', TRUE),
                'jam_selesai' => $this->input->post('jam_selesai', TRUE),
                'alasan' => $this->input->post('alasan', TRUE),
                'attachment' => $attachment_filename,
                'status' => $existing->status,
                'updated_by' => $user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Jika ada ID maka update
        $saved_id = $this->Perizinan_model->save($data, !empty($perizinan_id) ? $perizinan_id : null);

        if (!$saved_id) {
            echo json_encode([
                'status' => 'error',
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