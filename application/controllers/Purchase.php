<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // $this->only_admin();
        $this->load->model('Purchase_model');
        $this->load->model('Product_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Purchase';
        $data['status'] = $this->Purchase_model->get_status();
        $data['suppliers'] = $this->Purchase_model->get_supplier();
        $data['pembayaran'] = $this->Purchase_model->get_pembayaran();
        $data['purchase_script_page'] = 'index';
        $this->load->view('templates/header', $data);
        $this->load->view('purchase/index', $data);
        $this->load->view('templates/footer');
        $this->load->view('purchase/js', $data);
    }

    public function get_data() {
        $filter = [
            'status' => $this->input->post('status', TRUE),
            'supplier_id' => $this->input->post('supplier_id', TRUE),
            'start_date' => $this->input->post('start_date', TRUE),
            'end_date' => $this->input->post('end_date', TRUE),
            'payment_type' => $this->input->post('payment_type', TRUE)
        ];

        $data = $this->Purchase_model->get_data($filter);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function ajax_index()
    {
        $csrname = $this->security->get_csrf_token_name();
        $csrhash = $this->security->get_csrf_hash();

        $length = (int) $this->input->post('length');
        $start = (int) $this->input->post('start');

        $status = $this->input->post('status', TRUE);
        $supplier_id = $this->input->post('supplier_id', TRUE);
        $start_date = $this->input->post('start_date', TRUE);
        $end_date = $this->input->post('end_date', TRUE);
        $payment_type = $this->input->post('payment_type', TRUE);

        $this->db->join('supplier', 'supplier.id = purchase.supplier_id', 'left');
        $this->db->join('product_status', 'product_status.product_status_id = purchase.status', 'left');
        $this->db->join('users', 'users.user_id = purchase.created_by', 'left');

        if ($status !== '' && $status !== null) {
            $this->db->where('purchase.status', $status);
        }

        if (!empty($supplier_id)) {
            $this->db->where('purchase.supplier_id', $supplier_id);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $this->db->where('purchase.purchase_date >=', $start_date);
            $this->db->where('purchase.purchase_date <=', $end_date);
        }

        if (!empty($payment_type)) {
            $this->db->where('purchase.payment_type', $payment_type);
        }

        // Search DataTables
        $search = $this->input->post('search');

        if ($search && !empty($search['value'])) {
            $keyword = $search['value'];

            $this->db->group_start();
            $this->db->like('purchase.purchase_code', $keyword);
            $this->db->or_like('supplier.supplier_name', $keyword);
            $this->db->or_like('purchase.purchase_date', $keyword);
            $this->db->or_like('purchase.due_date', $keyword);
            $this->db->or_like('purchase.payment_type', $keyword);
            $this->db->or_like('product_status.product_status_name', $keyword);
            $this->db->or_like('users.username', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('purchase.purchase_id', 'DESC');

        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $content = $this->Purchase_model->get();

        $data = array();
        $no = $start;

        foreach ($content as $val) {
            $no++;

            $row = array();
            $row[] = $no;
            $row[] = '
                <button type="button" class="btn btn-sm btn-block btn-info" onclick="detailData(' . (int) $val->purchase_id . ')">
                    <i class="fas fa-eye"></i> ' . htmlspecialchars($val->purchase_code, ENT_QUOTES, 'UTF-8') . '
                </button>
            ';
            $row[] = $val->nama_supplier;
            $row[] = $val->purchase_date;
            $row[] = $val->due_date;
            $row[] = $val->payment_type;
            $row[] = $val->product_status_name;
            $row[] = $val->username;

            $row[] = '<div align="center" class="text-nowrap">
                <a href="' . base_url('purchase/edit/' . $val->purchase_id) . '" class="btn btn-sm btn-warning mb-2 mr-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="btn btn-sm btn-danger btn-delete mb-2 mr-2"
                    data-id="' . $val->purchase_id . '"
                    data-code="' . $val->purchase_code . '">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>';

            $data[] = $row;
        }

        $output = array(
            'draw' => (int) $this->input->post('draw'),
            'recordsTotal' => $this->total_record(),
            'recordsFiltered' => $this->total_record(TRUE),
            'data' => $data
        );

        $output[$csrname] = $csrhash;

        echo json_encode($output);
        exit;
    }

    public function total_record($filtered = FALSE)
    {
        $this->db->select('COUNT(purchase.purchase_id) as total', FALSE);

        $this->db->join('supplier', 'supplier.id = purchase.supplier_id', 'left');
        $this->db->join('product_status', 'product_status.product_status_id = purchase.status', 'left');
        $this->db->join('users', 'users.user_id = purchase.created_by', 'left');

        if ($filtered) {
            $search = $this->input->post('search');

            if ($search && !empty($search['value'])) {
                $keyword = $search['value'];

                $this->db->group_start();
                $this->db->like('purchase.purchase_code', $keyword);
                $this->db->or_like('supplier.supplier_name', $keyword);
                $this->db->or_like('purchase.purchase_date', $keyword);
                $this->db->or_like('purchase.due_date', $keyword);
                $this->db->or_like('purchase.payment_type', $keyword);
                $this->db->or_like('product_status.product_status_name', $keyword);
                $this->db->or_like('users.username', $keyword);
                $this->db->group_end();
            }
        }

        return (int) $this->db->get('purchase')->row()->total;
    }

    public function search($with_order = TRUE)
    {
        $column_order = array(
            NULL,
            'purchase.purchase_code',
            'supplier.supplier_name',
            'purchase.purchase_date',
            'purchase.due_date',
            'purchase.payment_type',
            'product_status.product_status_name',
            'users.username',
            NULL
        );

        $column_search = array(
            'purchase.purchase_code',
            'supplier.supplier_name',
            'purchase.purchase_date',
            'purchase.due_date',
            'purchase.payment_type',
            'product_status.product_status_name',
            'users.username'
        );

        $search = $this->input->post('search');

        if ($search && !empty($search['value'])) {
            $this->db->group_start();

            foreach ($column_search as $key => $item) {
                if ($key === 0) {
                    $this->db->like($item, $search['value']);
                } else {
                    $this->db->or_like($item, $search['value']);
                }
            }

            $this->db->group_end();
        }

        $order = $this->input->post('order');

        if ($with_order) {
            if ($order) {
                $column = (int) $order[0]['column'];
                $direction = strtoupper($order[0]['dir']) === 'ASC' ? 'ASC' : 'DESC';

                if (isset($column_order[$column]) && $column_order[$column] !== NULL) {
                    $this->db->order_by($column_order[$column], $direction);
                }
            } else {
                $this->db->order_by('purchase.purchase_id', 'DESC');
            }
        }
    }

    public function create(){
        $data['title'] = 'Tambah Purchase';
        $data['status'] = $this->Purchase_model->get_status();
        $data['suppliers'] = $this->Purchase_model->get_supplier();
        $data['product'] = $this->Purchase_model->get_product();
        $data['purchase_code_preview'] = $this->Purchase_model->generate_purchase_code();
        $data['purchase_draft_storage_key'] = 'purchase_draft_items_' . $this->session->userdata('user_id');
        $data['purchase_script_page'] = 'create';

        $this->load->view('templates/header', $data);
        $this->load->view('purchase/form', $data);
        $this->load->view('templates/footer');
        $this->load->view('purchase/js', $data);
    }

    public function store(){
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required|numeric');
        $this->form_validation->set_rules('purchase_date', 'Tanggal Purchase', 'required|trim');
        $this->form_validation->set_rules('due_date', 'Tanggal Jatuh Tempo', 'required|trim');
        $this->form_validation->set_rules('payment_type', 'Pembayaran', 'required|trim|max_length[50]');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'status' => 'failed',
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }

        $product_ids = $this->input->post('product_id', TRUE);
        $quantities = $this->input->post('qty', TRUE);
        $prices = $this->input->post('price', TRUE);

        if (
            empty($product_ids) ||
            empty($quantities) ||
            empty($prices) ||
            count($product_ids) !== count($quantities) ||
            count($product_ids) !== count($prices)
        ) {
            echo json_encode([
                'status' => 'failed',
                'message' => 'Produk, kuantitas, dan harga harus diisi.'
            ]);
            return;
        }

        $items = [];
        $subtotal_all = 0;
        foreach ($product_ids as $i => $pid) {
            $qty = (int) ($quantities[$i] ?? 0);
            $price = (float) ($prices[$i] ?? 0);
            if ($qty < 1 || $price < 0) {
                echo json_encode([
                    'status' => 'failed',
                    'message' => 'Kuantitas dan harga produk tidak valid.'
                ]);
                return;
            }
            $items[] = [
                'product_id' => $pid,
                'qty'        => $qty,
                'price'      => $price,
            ];
            $subtotal_all += ($qty * $price);
        }

        $discount = (float) $this->input->post('discount', TRUE);
        $tax = (float) $this->input->post('tax', TRUE);
        $grand_total = $subtotal_all - $discount + $tax;

        $purchase_code = $this->Purchase_model->generate_purchase_code();

        $header = [
            'purchase_code' => $purchase_code,
            'created_by'    => $this->session->userdata('user_id'),
            'supplier_id'   => (int) $this->input->post('supplier_id', TRUE),
            'purchase_date' => $this->input->post('purchase_date', TRUE),
            'due_date'      => $this->input->post('due_date', TRUE),
            'payment_type'  => $this->input->post('payment_type', TRUE),
            'status'        => 12,
            'notes'         => $this->input->post('notes', TRUE),
            'subtotal'      => $subtotal_all,
            'discount'      => $discount,
            'tax'           => $tax,
            'grand_total'   => $grand_total
        ];

        $purchase_id = $this->Purchase_model->proses_purchase($header, $items);

        if ($purchase_id) {
            $this->session->set_flashdata('success', 'Purchase berhasil disimpan dengan kode: ' . $purchase_code);
            echo json_encode([
                'status' => 'success',
                'message' => 'Purchase berhasil disimpan dengan kode: ' . $purchase_code
            ]);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data purchase.');
            echo json_encode([
                'status' => 'failed',
                'message' => 'Gagal menyimpan data purchase.'
            ]);
        }
    }

    public function detail($id = null){
        $purchase = $this->Purchase_model->get_purchase_detail($id);

        if (!$purchase) {
            $this->session->set_flashdata('error', 'Purchase tidak ditemukan.');
            redirect('purchase');
            return;
        }
        header('Content-Type: application/json');
        echo json_encode($purchase);
    }

    public function edit($id = null){
        $purchase = $this->Purchase_model->get_purchase_detail($id);

        if (!$purchase) {
            $this->session->set_flashdata('error', 'Purchase tidak ditemukan.');
            redirect('purchase');
            return;
        }

        $data['title']      = 'Edit Purchase';
        $data['status']     = $this->Purchase_model->get_status();
        $data['items']      = $this->Purchase_model->get_purchase_items($id);
        $data['suppliers']  = $this->Purchase_model->get_supplier();
        $data['product']    = $this->Purchase_model->get_product();
        $data['purchase']   = $purchase;
        $data['purchase_script_page'] = 'edit';

        $this->load->view('templates/header', $data);
        $this->load->view('purchase/edit', $data);
        $this->load->view('templates/footer');
        $this->load->view('purchase/js', $data);
    }

    public function detail_store($purchase_id = null){
        $this->output->set_content_type('application/json');
        $purchase = $this->Purchase_model->get_purchase_detail($purchase_id);
        $product_id = (int) $this->input->post('product_id', TRUE);
        $qty = (int) $this->input->post('qty', TRUE);
        $price = (float) $this->input->post('price', TRUE);

        if (!$purchase || !$product_id || $qty < 1 || $price < 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Data produk tidak valid.']);
            return;
        }

        $detail_id = $this->Purchase_model->insert_purchase_item($purchase_id, $product_id, $qty, $price);
        if (!$detail_id) {
            echo json_encode(['status' => 'failed', 'message' => 'Produk sudah ada atau gagal disimpan.']);
            return;
        }

        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan.', 'item' => $this->Purchase_model->get_purchase_item($detail_id, $purchase_id)]);
    }

    public function detail_update($purchase_id = null, $detail_id = null){
        $this->output->set_content_type('application/json');
        $product_id = (int) $this->input->post('product_id', TRUE);
        $qty = (int) $this->input->post('qty', TRUE);
        $price = (float) $this->input->post('price', TRUE);

        if (!$this->Purchase_model->get_purchase_detail($purchase_id) || !$detail_id || !$product_id || $qty < 1 || $price < 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Data produk tidak valid.']);
            return;
        }

        if (!$this->Purchase_model->update_purchase_item($detail_id, $purchase_id, $product_id, $qty, $price)) {
            echo json_encode(['status' => 'failed', 'message' => 'Produk sudah ada atau gagal diperbarui.']);
            return;
        }

        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil diperbarui.', 'item' => $this->Purchase_model->get_purchase_item($detail_id, $purchase_id)]);
    }

    public function detail_delete($purchase_id = null, $detail_id = null){
        $this->output->set_content_type('application/json');
        if (!$this->Purchase_model->get_purchase_detail($purchase_id)) {
            echo json_encode(['status' => 'failed', 'message' => 'Purchase tidak ditemukan.']);
            return;
        }

        if (!$this->Purchase_model->delete_purchase_item($detail_id, $purchase_id)) {
            echo json_encode(['status' => 'failed', 'message' => 'Gagal menghapus produk.']);
            return;
        }

        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil dihapus.']);
    }

    public function update($id = null){
        $purchase = $this->Purchase_model->get_purchase_detail($id);

        if (!$purchase) {
            echo json_encode(['status' => 'failed', 'message' => 'Purchase tidak ditemukan.']);
            return;
        }

        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required|numeric');
        $this->form_validation->set_rules('purchase_date', 'Tanggal Purchase', 'required|trim');
        $this->form_validation->set_rules('due_date', 'Tanggal Jatuh Tempo', 'required|trim');
        $this->form_validation->set_rules('payment_type', 'Pembayaran', 'required|trim|max_length[50]');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'status' => 'failed',
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }

        $product_ids = $this->input->post('product_id', TRUE);
        $quantities = $this->input->post('qty', TRUE);
        $prices = $this->input->post('price', TRUE);

        if (
            empty($product_ids) ||
            empty($quantities) ||
            empty($prices) ||
            count($product_ids) !== count($quantities) ||
            count($product_ids) !== count($prices)
        ) {
            echo json_encode([
                'status' => 'failed',
                'message' => 'Produk, kuantitas, dan harga harus diisi.'
            ]);
            return;
        }

        $items = [];
        $subtotal_all = 0;
        foreach ($product_ids as $i => $pid) {
            $qty = (int) ($quantities[$i] ?? 0);
            $price = (float) ($prices[$i] ?? 0);
            if ($qty < 1 || $price < 0) {
                echo json_encode([
                    'status' => 'failed',
                    'message' => 'Kuantitas dan harga produk tidak valid.'
                ]);
                return;
            }
            $items[] = [
                'product_id' => $pid,
                'qty'        => $qty,
                'price'      => $price,
            ];
            $subtotal_all += ($qty * $price);
        }

        $discount = (float) $this->input->post('discount', TRUE);
        $tax = (float) $this->input->post('tax', TRUE);
        $grand_total = $subtotal_all - $discount + $tax;

        $header = [
            'created_by'    => $this->session->userdata('user_id'),
            'supplier_id'   => (int) $this->input->post('supplier_id', TRUE),
            'purchase_date' => $this->input->post('purchase_date', TRUE),
            'due_date'      => $this->input->post('due_date', TRUE),
            'payment_type'  => $this->input->post('payment_type', TRUE),
            'status'        => 12,
            'notes'         => $this->input->post('notes', TRUE),
            'subtotal'      => $subtotal_all,
            'discount'      => $discount,
            'tax'           => $tax,
            'grand_total'   => $grand_total
        ];

        $success = $this->Purchase_model->update_purchase($id, $header, $items);

        if ($success) {
            $this->session->set_flashdata('success', 'Purchase berhasil diperbarui.');
            echo json_encode(['status' => 'success', 'message' => 'Purchase berhasil diperbarui.']);
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data purchase.');
            echo json_encode(['status' => 'failed', 'message' => 'Gagal memperbarui data purchase.']);
        }
    }

    public function delete($id = null){
        $purchase = $this->Purchase_model->get_purchase_detail($id);

        if (!$purchase) {
            echo json_encode(['status' => 'failed', 'message' => 'Purchase tidak ditemukan.']);
            return;
        }

        $success = $this->Purchase_model->delete_purchase($id);

        if ($success) {
            $this->session->set_flashdata('success', 'Purchase berhasil dihapus.');
            echo json_encode(['status' => 'success', 'message' => 'Purchase berhasil dihapus.']);
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data purchase.');
            echo json_encode(['status' => 'failed', 'message' => 'Gagal menghapus data purchase.']);
        }
    }

    public function get_product_code()
    {
        $code = $this->input->post('code');
        $product = $this->Product_model->get_by_code($code);

        if (!$product) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan.'
            ]);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'item' => [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name
            ]
        ]);
    }
        
}