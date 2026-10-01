<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Budgeting extends MY_Controller {

    // Status Modul Approval (product_status)
    const STATUS_PENDING  = 17;
    const STATUS_APPROVED = 18;
    const STATUS_REJECTED = 19;

    public function __construct() {
        parent::__construct();
        $this->load->model('Budgeting_model');
        $this->load->model('Budgeting_detail_model');
        $this->load->library('form_validation');
    }

    public function index() {
        // Deteksi nama kolom status di tabel budgeting
        $status_col = $this->db->field_exists('status_id', 'budgeting') ? 'status_id' : 'product_status_id';

        // Query data list budgeting (fat controller) dengan modul approval
        $this->db->select('budgeting.*, employees.employee_name, departments.department_name, product_status.product_status_name');
        $this->db->join('employees', 'employees.employee_id = budgeting.employee_id', 'left');
        $this->db->join('departments', 'departments.department_id = budgeting.department_id', 'left');
        $this->db->join('product_status', "product_status.product_status_id = budgeting.{$status_col}", 'left');
        $this->db->order_by('budgeting.budgeting_id', 'DESC');
        $rows = $this->Budgeting_model->get();

        // Normalisasi properti nomor budgeting untuk kompatibilitas view
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if (!isset($row->budgeting_no) && isset($row->budget_no)) {
                    $row->budgeting_no = $row->budget_no;
                }
                if (!isset($row->budget_no) && isset($row->budgeting_no)) {
                    $row->budget_no = $row->budgeting_no;
                }
            }
        }
        $data['content'] = $rows;

        $data['title'] = 'Budgeting';
        $this->load->view('templates/header', $data);
        $this->load->view($this->uri->rsegment(1) . '/index', $data);
        $this->load->view('templates/footer');
        $this->load->view($this->uri->rsegment(1) . '/js', $data);
    }

    public function create() {
        // Data master untuk dropdown
        $this->db->order_by('employee_name', 'ASC');
        $data['employees'] = $this->db->get('employees')->result();

        $this->db->order_by('department_name', 'ASC');
        $data['departments'] = $this->db->get('departments')->result();

        $data['title']     = 'Tambah Budgeting';
        $data['budgeting'] = null;
        $data['detail']    = [];

        $this->load->view('templates/header', $data);
        $this->load->view('budgeting/form', $data);
        $this->load->view('templates/footer');
        $this->load->view('budgeting/form_js', $data);
    }

    public function edit($id = NULL) {
        $id = (int) $id;
        $status_col = $this->db->field_exists('status_id', 'budgeting') ? 'status_id' : 'product_status_id';

        $this->db->select('budgeting.*, employees.employee_name, departments.department_name, product_status.product_status_name');
        $this->db->join('employees', 'employees.employee_id = budgeting.employee_id', 'left');
        $this->db->join('departments', 'departments.department_id = budgeting.department_id', 'left');
        $this->db->join('product_status', "product_status.product_status_id = budgeting.{$status_col}", 'left');
        $header = $this->Budgeting_model->get_by(['budgeting.budgeting_id' => $id], TRUE);

        if (!$header) {
            $this->session->set_flashdata('error', 'Data budgeting tidak ditemukan.');
            redirect('budgeting');
            return;
        }

        $currentStatusId = (int) ($header->$status_col ?? $header->product_status_id ?? 0);
        $statusName      = strtolower(trim($header->product_status_name ?? ''));

        // Cek hak edit: hanya boleh jika status Pending
        if ($currentStatusId !== self::STATUS_PENDING && !in_array($statusName, ['pending', 'draft']) && $currentStatusId !== 0) {
            $this->session->set_flashdata('error', 'Budgeting dengan status ' . ($header->product_status_name ?: 'tersebut') . ' tidak dapat diedit. Hanya status Pending yang diperbolehkan.');
            redirect('budgeting');
            return;
        }

        // Normalisasi nomor
        if (!isset($header->budgeting_no) && isset($header->budget_no)) {
            $header->budgeting_no = $header->budget_no;
        }
        if (!isset($header->budget_no) && isset($header->budgeting_no)) {
            $header->budget_no = $header->budgeting_no;
        }

        $data['budgeting'] = $header;
        $data['detail']    = $this->Budgeting_detail_model->get_by(['budgeting_id' => $id]);

        $this->db->order_by('employee_name', 'ASC');
        $data['employees'] = $this->db->get('employees')->result();

        $this->db->order_by('department_name', 'ASC');
        $data['departments'] = $this->db->get('departments')->result();

        $data['title'] = 'Edit Budgeting';
        $this->load->view('templates/header', $data);
        $this->load->view('budgeting/form', $data);
        $this->load->view('templates/footer');
        $this->load->view('budgeting/form_js', $data);
    }

    public function get_data($id = NULL)
    {
        $id = (int) $id;
        $status_col = $this->db->field_exists('status_id', 'budgeting') ? 'status_id' : 'product_status_id';

        $this->db->select('budgeting.*, employees.employee_name, departments.department_name, product_status.product_status_name');
        $this->db->join('employees', 'employees.employee_id = budgeting.employee_id', 'left');
        $this->db->join('departments', 'departments.department_id = budgeting.department_id', 'left');
        $this->db->join('product_status', "product_status.product_status_id = budgeting.{$status_col}", 'left');
        $header = $this->Budgeting_model->get_by(['budgeting.budgeting_id' => $id], TRUE);

        if (!$header) {
            return $this->_json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        // Normalisasi nomor budgeting
        if (!isset($header->budgeting_no) && isset($header->budget_no)) {
            $header->budgeting_no = $header->budget_no;
        }
        if (!isset($header->budget_no) && isset($header->budgeting_no)) {
            $header->budget_no = $header->budgeting_no;
        }

        $detail = $this->Budgeting_detail_model->get_by(['budgeting_id' => $id]);

        return $this->_json(['status' => true, 'header' => $header, 'detail' => $detail]);
    }

    public function save()
    {
        // Sanitasi $_POST unit_price & qty dari titik pemisah ribuan sebelum validasi form
        if (!empty($_POST['unit_price']) && is_array($_POST['unit_price'])) {
            foreach ($_POST['unit_price'] as $k => $v) {
                $clean = str_replace([' ', '.'], '', (string) $v);
                $clean = str_replace(',', '.', $clean);
                $_POST['unit_price'][$k] = $clean;
            }
        }
        if (!empty($_POST['qty']) && is_array($_POST['qty'])) {
            foreach ($_POST['qty'] as $k => $v) {
                $clean = str_replace([' ', ','], ['', '.'], (string) $v);
                $_POST['qty'][$k] = $clean;
            }
        }

        // Validasi form menggabungkan rules model (Konvensi 3)
        $this->form_validation->set_rules(array_merge($this->Budgeting_model->rules, $this->Budgeting_detail_model->rules));

        if ($this->form_validation->run() === FALSE) {
            return $this->_json([
                'status'  => false,
                'message' => 'Validasi gagal, silakan periksa kelengkapan input Anda.',
                'errors'  => $this->form_validation->error_array()
            ]);
        }

        $budgeting_id = (int) $this->input->post('budgeting_id');
        $status_col   = $this->db->field_exists('status_id', 'budgeting') ? 'status_id' : 'product_status_id';
        $no_col       = $this->db->field_exists('budget_no', 'budgeting') ? 'budget_no' : 'budgeting_no';

        // Jika update, periksa status harus Pending (17)
        if ($budgeting_id > 0) {
            $this->db->join('product_status', "product_status.product_status_id = budgeting.{$status_col}", 'left');
            $existing = $this->Budgeting_model->get($budgeting_id, TRUE);

            if (!$existing) {
                return $this->_json(['status' => false, 'message' => 'Data budgeting tidak ditemukan.']);
            }

            $currentStatusId = (int) ($existing->$status_col ?? $existing->product_status_id ?? 0);
            $statusName      = strtolower(trim($existing->product_status_name ?? ''));

            // Hanya izinkan edit jika status 17 (Pending)
            if ($currentStatusId !== self::STATUS_PENDING && !in_array($statusName, ['pending', 'draft']) && $currentStatusId !== 0) {
                return $this->_json(['status' => false, 'message' => 'Hanya data dengan status Pending yang dapat diubah.']);
            }
        }

        // Ambil dan hitung detail di server (Konvensi 5)
        $item_descriptions = (array) $this->input->post('item_description');
        $payment_types     = (array) $this->input->post('payment_type');
        $qtys              = (array) $this->input->post('qty');
        $unit_prices       = (array) $this->input->post('unit_price');

        if (empty($item_descriptions)) {
            return $this->_json(['status' => false, 'message' => 'Detail budgeting minimal harus memiliki 1 baris.']);
        }

        $detail_rows  = [];
        $total_amount = 0;

        foreach ($item_descriptions as $i => $desc) {
            $descTrim = trim($desc);
            if ($descTrim === '') {
                continue;
            }

            // Sanitasi format harga dan qty dari pemisah ribuan titik (misal '1.500.000' -> 1500000)
            $rawQty   = str_replace([' ', ','], ['', '.'], (string) ($qtys[$i] ?? 0));
            $rawPrice = str_replace([' ', '.'], ['', ''], (string) ($unit_prices[$i] ?? 0));
            $rawPrice = str_replace(',', '.', $rawPrice);

            $qty        = (float) $rawQty;
            $unit_price = (float) $rawPrice;
            $amount     = $qty * $unit_price;
            $total_amount += $amount;

            $detail_rows[] = [
                'payment_type'     => $payment_types[$i] ?? 'Tunai',
                'item_description' => $descTrim,
                'qty'              => $qty,
                'unit_price'       => $unit_price,
                'amount'           => $amount,
            ];
        }

        if (empty($detail_rows)) {
            return $this->_json(['status' => false, 'message' => 'Detail budgeting minimal harus memiliki 1 baris terisi.']);
        }

        $header_data = [
            'budget_date'   => $this->input->post('budget_date', TRUE),
            'employee_id'   => (int) $this->input->post('employee_id'),
            'department_id' => (int) $this->input->post('department_id'),
            'description'   => $this->input->post('description', TRUE),
            'total_amount'  => $total_amount,
        ];

        // Simpan header (create / update via save())
        if ($budgeting_id > 0) {
            $this->Budgeting_model->save($header_data, $budgeting_id);
            $id = $budgeting_id;

            // Hapus detail lama untuk digantikan dengan yang baru
            $this->Budgeting_detail_model->delete_by(['budgeting_id' => $id]);
        } else {
            $header_data[$no_col] = $this->Budgeting_model->generate_budgeting_no();

            // Status awal selalu 17 (Pending) dari modul approval
            $header_data[$status_col] = self::STATUS_PENDING;

            $id = $this->Budgeting_model->save($header_data);
        }

        // Simpan baris detail (insert_batch)
        foreach ($detail_rows as &$row) {
            $row['budgeting_id'] = $id;
        }
        unset($row);

        $this->Budgeting_detail_model->insert_batch($detail_rows);

        $this->session->set_flashdata('success', $budgeting_id > 0 ? 'Data budgeting berhasil diperbarui.' : 'Data budgeting berhasil disimpan.');

        return $this->_json([
            'status'   => true,
            'message'  => $budgeting_id > 0 ? 'Data budgeting berhasil diperbarui.' : 'Data budgeting berhasil disimpan.',
            'id'       => $id,
            'redirect' => base_url('budgeting')
        ]);
    }

    public function delete($id = NULL)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return $this->_json(['status' => false, 'message' => 'ID budgeting tidak valid.']);
        }

        $status_col = $this->db->field_exists('status_id', 'budgeting') ? 'status_id' : 'product_status_id';

        // Hapus hanya boleh saat status Pending (17)
        $this->db->join('product_status', "product_status.product_status_id = budgeting.{$status_col}", 'left');
        $existing = $this->Budgeting_model->get($id, TRUE);

        if (!$existing) {
            return $this->_json(['status' => false, 'message' => 'Data budgeting tidak ditemukan.']);
        }

        $currentStatusId = (int) ($existing->$status_col ?? $existing->product_status_id ?? 0);
        $statusName      = strtolower(trim($existing->product_status_name ?? ''));

        // Hanya izinkan hapus jika status 17 (Pending)
        if ($currentStatusId !== self::STATUS_PENDING && !in_array($statusName, ['pending', 'draft']) && $currentStatusId !== 0) {
            return $this->_json(['status' => false, 'message' => 'Hanya data dengan status Pending yang dapat dihapus.']);
        }

        // Hapus detail lalu header
        $this->Budgeting_detail_model->delete_by(['budgeting_id' => $id]);
        $this->Budgeting_model->delete($id);

        return $this->_json(['status' => true, 'message' => 'Data budgeting berhasil dihapus.']);
    }

    protected function _json($data)
    {
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash']  = $this->security->get_csrf_hash();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}