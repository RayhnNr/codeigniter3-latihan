<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perizinan extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Perizinan_m');
        $this->load->model('Perizinan_tipe_m');
        $this->load->model('Approve_model');        // nama class: Approve_model, file: Approve_model.php
        $this->load->model('Approval_detail_m');
        $this->load->model('Approval_request_m');
        $this->load->model('Approval_request_detail_m');
        $this->load->library('form_validation');
    }


    // -------------------------------------------------------------------------
    // Halaman list riwayat pengajuan izin milik user yang sedang login
    // -------------------------------------------------------------------------
    public function index(){
        $user_id = $this->session->userdata('user_id');

        $data['title']     = 'Riwayat Pengajuan Izin';
        $data['perizinan'] = $this->Perizinan_m->get_data_by_user($user_id);

        $this->load->view('templates/header', $data);
        $this->load->view('perizinan/index', $data);
        $this->load->view('templates/footer');
    }


    // -------------------------------------------------------------------------
    // Fragment HTML form pengajuan izin (dipanggil via AJAX, render ke modal)
    // -------------------------------------------------------------------------
    public function ajax_form(){
        $data['jenis_izin'] = $this->Perizinan_tipe_m->get_all();
        $this->load->view('perizinan/ajax_form', $data);
    }


    // -------------------------------------------------------------------------
    // Simpan pengajuan izin + buat approval_request + copy detail dari template
    // -------------------------------------------------------------------------
    public function store(){

        // --- 1. Validasi form ---
        $this->form_validation->set_rules('id_perizinan_tipe', 'Jenis Izin',      'required|integer');
        $this->form_validation->set_rules('tanggal_mulai',     'Tanggal Mulai',   'required');
        $this->form_validation->set_rules('tanggal_selesai',   'Tanggal Selesai', 'required');
        $this->form_validation->set_rules('alasan',            'Alasan',          'required');

        if ($this->form_validation->run() === FALSE) {
            $errors = array_filter([
                'id_perizinan_tipe' => strip_tags(form_error('id_perizinan_tipe', '', '')),
                'tanggal_mulai'     => strip_tags(form_error('tanggal_mulai',     '', '')),
                'tanggal_selesai'   => strip_tags(form_error('tanggal_selesai',   '', '')),
                'alasan'            => strip_tags(form_error('alasan',            '', '')),
            ]);

            echo json_encode(['status' => 'failed', 'errors' => $errors]);
            return;
        }

        $user_id = $this->session->userdata('user_id');

        // --- 2. Mulai transaction ---
        $this->db->trans_begin();

        // --- 3. Generate kode perizinan ---
        $kode = $this->Perizinan_m->generate_code();

        // --- 4. Ambil status ID "Pending" untuk tabel perizinan ---
        $pending_status_perizinan = $this->Perizinan_m->get_status_id_by_name('Pending');

        // --- 5. Susun & insert data perizinan ---
        $data_perizinan = [
            'user_id'           => $user_id,
            'id_perizinan_tipe' => $this->input->post('id_perizinan_tipe'),
            'tanggal_mulai'     => $this->input->post('tanggal_mulai'),
            'tanggal_selesai'   => $this->input->post('tanggal_selesai'),
            'alasan'            => $this->input->post('alasan'),
            'status'            => $pending_status_perizinan,  // nama kolom asli di tabel perizinan
            'perizinan_code'    => $kode,
        ];

        $perizinan_id = $this->Perizinan_m->insert($data_perizinan);

        // --- 6. Tentukan approval template yang dipakai (ambil baris pertama) ---
        $all_approvals = $this->Approve_model->get_data();
        $approval      = !empty($all_approvals) ? $all_approvals[0] : null;
        $approval_id   = $approval ? $approval->approval_id : 1;

        // --- 7. Ambil status ID "Pending" untuk approval ---
        $pending_status_approval = $this->Approve_model->get_status_id_by_name('Pending');

        // --- 8. Susun & insert approval_request ---
        $data_request = [
            'approval_id'      => $approval_id,
            'reference_type'   => 'perizinan',
            'reference_id'     => $perizinan_id,
            'requester_id'     => $user_id,
            'request_date'     => date('Y-m-d H:i:s'),
            'current_sequence' => 1,
            'request_status'   => $pending_status_approval,
        ];

        $approval_request_id = $this->Approval_request_m->insert($data_request);

        // --- 9. Copy detail approver dari template ke approval_request_detail ---
        $this->Approval_request_detail_m->copy_from_template(
            $approval_request_id,
            $approval_id,
            $pending_status_approval
        );

        // --- 10. Cek trans_status & commit / rollback ---
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal menyimpan pengajuan izin. Silakan coba lagi.'
            ]);
            return;
        }

        $this->db->trans_commit();

        echo json_encode([
            'status'  => 'success',
            'message' => 'Pengajuan izin ' . $kode . ' berhasil dikirim.'
        ]);
    }


    // -------------------------------------------------------------------------
    // Fragment HTML detail + progress approval (dipanggil via AJAX)
    // -------------------------------------------------------------------------
    public function ajax_detail(){
        $id = $this->input->post('id');

        // Data header perizinan (join ke tipe izin)
        $data['perizinan'] = $this->Perizinan_m->get_with_join($id);

        // Cari approval_request yang terkait perizinan ini
        $approval_requests = $this->Approval_request_m->get_by_reference('perizinan', $id);
        $approval_request  = !empty($approval_requests) ? $approval_requests[0] : null;

        $data['approval_request']         = $approval_request;
        $data['approval_request_details'] = [];

        if ($approval_request) {
            // Ambil progress per-approver dari approval_request_detail
            $data['approval_request_details'] = $this->Approval_request_detail_m
                ->get_by_request($approval_request->id_approval_request);
        }

        $this->load->view('perizinan/ajax_detail', $data);
    }

}