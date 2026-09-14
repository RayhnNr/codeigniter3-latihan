<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brands extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Brands_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Brand';
        $this->load->view('templates/header', $data);
        $this->load->view('brands/index', $data);
        $this->load->view('templates/footer');
        $this->load->view('brands/js', $data);
    }

    public function get_data() {
        $list = $this->Brands_model->get_all_with_status();   // pakai custom method, bukan get()

        $data = [];
        $no = 1;
        foreach ($list as $row) {
            $status_badge = $row->product_status_name === 'Aktif'
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>';

            $data[] = [
                'no'            => $no++,
                'brand_name'    => $row->brand_name,
                'status'        => $status_badge,
                'action' => '
                    <button class="btn btn-warning btn-sm btn-edit" data-id="' . $row->brand_id . '"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="' . $row->brand_id . '"><i class="fas fa-trash"></i></button>
                '
            ];
        }

        echo json_encode(['data' => $data]);
    }

    public function ajax_form() {
        $data['row'] = null;
        $data['status_list'] = $this->Brands_model->get_status_options();
        $data['active_status_id'] = $this->Brands_model->get_status_id_by_name('Aktif');
        $data['inactive_status_id'] = $this->Brands_model->get_status_id_by_name('Nonaktif');
        $this->load->view('brands/form', $data);
    }

    public function ajax_edit() {
        $id = $this->input->post('id');
        $data['row'] = $this->Brands_model->get($id);   // pakai get($id), sudah otomatis row()
        $data['status_list'] = $this->Brands_model->get_status_options();
        $data['active_status_id'] = $this->Brands_model->get_status_id_by_name('Aktif');
        $data['inactive_status_id'] = $this->Brands_model->get_status_id_by_name('Nonaktif');
        $this->load->view('brands/edit', $data);
    }

    public function save() {
        $id = $this->input->post('brand_id');

        $this->form_validation->set_rules('brand_name', 'Nama Brand', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('status', 'Status', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'failed',
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }

        $data = [
            'brand_name' => $this->input->post('brand_name', TRUE),
            'status'          => (int) $this->input->post('status', TRUE),
        ];

        // save() otomatis insert kalau $id NULL, update kalau ada isinya
        $this->Brands_model->save($data, !empty($id) ? $id : NULL);

        $message = !empty($id) ? 'Brand berhasil diperbarui.' : 'Brand berhasil ditambahkan.';

        echo json_encode(['status' => 'success', 'message' => $message]);
    }

    public function delete() {
        $id = $this->input->post('id');

        if (empty($id)) {
            echo json_encode(['status' => 'failed', 'message' => 'ID tidak valid.']);
            return;
        }

        $this->Brands_model->delete($id);
        echo json_encode(['status' => 'success', 'message' => 'Brand berhasil dihapus.']);
    }
}