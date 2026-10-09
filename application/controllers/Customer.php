<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Customer_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->db->order_by('customer_id', 'DESC');
        $this->data['content'] = $this->Customer_model->get();
        $this->data['title'] = 'Customer';
        $this->data['page_js'] = $this->load->view('customer/js', $this->data, TRUE);
        $this->load->view('templates/header', $this->data);
        $this->load->view($this->uri->rsegment(1) . '/index', $this->data);
        $this->load->view('templates/footer', $this->data);
    }

    public function form($id = null)
    {
        if ($id) {
            $customer = $this->Customer_model->get($id, TRUE);
            if (!$customer) {
                redirect('error_page/not_found');
            }

            $this->data['title'] = 'Edit Customer';
            $this->data['is_edit'] = TRUE;
        } else {
            $customer = $this->Customer_model->get_new();
            $this->data['title'] = 'Tambah Customer';
            $this->data['is_edit'] = FALSE;
        }

        $this->data['content'] = $customer;
        $this->data['page_js'] = $this->load->view('customer/js', $this->data, TRUE);
        $this->load->view('templates/header', $this->data);
        $this->load->view('customer/form', $this->data);
        $this->load->view('templates/footer', $this->data);
    }

    public function save($id = null) {
        if ($id) {
            $customer = $this->Customer_model->get($id, TRUE);
            if (!$customer) {
                redirect('error_page/not_found');
            }
        } else {
            $customer = $this->Customer_model->get_new();
        }

        $this->form_validation->set_rules($this->Customer_model->rules);
        if ($this->form_validation->run() === FALSE) {
            $this->data['content'] = $customer;
            $this->data['is_edit'] = (bool) $id;
            $this->data['title'] = $id ? 'Edit Customer' : 'Tambah Customer';
            $this->data['page_js'] = $this->load->view('customer/js', $this->data, TRUE);
            $this->load->view('templates/header', $this->data);
            $this->load->view('customer/form', $this->data);
            $this->load->view('templates/footer', $this->data);
            return;
        }

        $data = [
            'customer_name' => $this->input->post('customer_name', TRUE),
            'contact_person' => $this->input->post('contact_person', TRUE),
            'address' => $this->input->post('address', TRUE),
            'phone' => $this->input->post('phone', TRUE),
            'email' => $this->input->post('email', TRUE),
            'payment_term_days' => $this->input->post('payment_term_days', TRUE),
            'credit_limit' => $this->input->post('credit_limit', TRUE),
        ];

        if (!$id) {
            $data['customer_code'] = $this->Customer_model->generate_customer_code();
            $data['is_active'] = 1;
        } else {
            $data['is_active'] = $this->input->post('is_active', TRUE);
        }

        $this->Customer_model->save($data, $id ? $id : NULL);
        $this->session->set_flashdata(
            'success',
            $id ? 'Data pelanggan berhasil diperbarui' : 'Data pelanggan berhasil disimpan'
        );
        redirect('customer');
    }

    public function delete($id){
        $customer = $this->Customer_model->get($id, TRUE);
        if (!count_valid($customer)) {
            redirect('error404');
        }

        $this->Customer_model->delete($id);
        $this->session->set_flashdata('success', 'Data pelanggan berhasil dihapus');
        redirect($this->uri->rsegment(1));
    }
}