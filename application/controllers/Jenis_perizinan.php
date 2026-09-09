<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jenis_perizinan extends MY_Controller {


    public function __construct() {
        parent::__construct();
        $this->load->model('Jenis_perizinan_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Jenis Perizinan';
        $data['list']  = $this->Jenis_perizinan_model->get();
        $data['active_status_id'] = $this->Jenis_perizinan_model->get_status_id_by_name('Aktif');
        $this->load->view('templates/header', $data);
        $this->load->view('jenis_perizinan/index', $data);
        $this->load->view('templates/footer');
    }


    public function ajax_form(){
        $jenis_perizinan_id = $this->input->post('jenis_perizinan_id');
        $data['row']        = !empty($jenis_perizinan_id) ? $this->Jenis_perizinan_model->get($jenis_perizinan_id) : null;
        $data['status']     = $this->Jenis_perizinan_model->get_status();
        $data['active_status_id'] = $this->Jenis_perizinan_model->get_status_id_by_name('Aktif');
        $data['inactive_status_id'] = $this->Jenis_perizinan_model->get_status_id_by_name('Nonaktif');
        $this->load->view('jenis_perizinan/ajax_form', $data);
    }


    public function save(){
        $jenis_perizinan_id = $this->input->post('jenis_perizinan_id');

        $this->form_validation->set_rules('jenis_perizinan_name', 'Jenis Perizinan', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[50]');
        $this->form_validation->set_rules('status', 'Status', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('jenis_perizinan');
            return;
        }

        $text = $this->input->post('jenis_perizinan_name', TRUE);
        $kode_jenis_perizinan = $this->Jenis_perizinan_model->generate_code($text);

        if ($this->Jenis_perizinan_model->cek_jenis_perizinan(['jenis_perizinan_code' => $kode_jenis_perizinan], $jenis_perizinan_id) > 0) {
            redirect('jenis_perizinan');
            return;
        }

        $data = [
            'jenis_perizinan_code' => $kode_jenis_perizinan,
            'jenis_perizinan_name' => $text,
            'description'          => $this->input->post('description', TRUE),
            'status'               => (int) $this->input->post('status')
        ];

        if (empty($jenis_perizinan_id)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->session->userdata('user_id');
        }

        $this->Jenis_perizinan_model->save($data, !empty($jenis_perizinan_id) ? $jenis_perizinan_id : NULL);
        $this->session->set_flashdata('success', 'Data berhasil disimpan.');
        redirect('jenis_perizinan');
    }

    public function check_code(){
        $name = $this->input->post('jenis_perizinan_name', TRUE);
        $id = $this->input->post('jenis_perizinan_id');
        $code = $this->Jenis_perizinan_model->generate_code($name);

        echo json_encode([
            'code' => $code,
            'exists' => $this->Jenis_perizinan_model->cek_jenis_perizinan([
                'jenis_perizinan_code' => $code
            ], $id) > 0
        ]);
    }

    public function delete($jenis_perizinan_id){
        if(!empty($jenis_perizinan_id)){
            $this->Jenis_perizinan_model->delete($jenis_perizinan_id);
            $this->session->set_flashdata('success', 'Data berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'ID tidak valid.');
        }
        redirect('jenis_perizinan');
    }
}