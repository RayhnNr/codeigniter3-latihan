<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perizinan_tipe extends MY_Controller {


    public function __construct() {
        parent::__construct();
        $this->load->model('Perizinan_tipe_m');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Jenis Perizinan';
        $data['list']  = $this->Perizinan_tipe_m->get();
        $this->load->view('templates/header', $data);
        $this->load->view('perizinan_tipe/index', $data);
        $this->load->view('templates/footer');
    }


    public function ajax_form(){
        
        $id = $this->input->post('id');
        $data['row'] = !empty($id) ? $this->Perizinan_tipe_m->get($id) : null;
        $this->load->view('perizinan_tipe/ajax_form', $data);
    }


    public function save(){
        $id = $this->input->post('id_perizinan_tipe');
        $this->form_validation->set_rules('nama_tipe', 'Nama Tipe', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[1,2]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Data tidak valid.');
            redirect('perizinan_tipe');
        }

        $data = [
            'nama_tipe' => $this->input->post('nama_tipe'),
            'status' => (int) $this->input->post('status'),
        ];

        $this->Perizinan_tipe_m->save($data, !empty($id) ? $id : NULL);

        $this->session->set_flashdata('success', 'Data berhasil disimpan.');

        redirect('perizinan_tipe');
    }

    public function delete($id){
        if(!empty($id)){
            $this->Perizinan_tipe_m->delete($id);
            $this->session->set_flashdata('success', 'Data berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'ID tidak valid.');
        }
        redirect('perizinan_tipe');
    }
}