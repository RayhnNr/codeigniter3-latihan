<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_order extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Sales_order_model');
        $this->load->model('Sales_order_detail_model');
        $this->load->model('Customer_model');
        $this->load->model('Product_model');
        $this->load->model('Product_status_model');
        $this->load->library('form_validation');
    }
    public function index() {
        $this->db->join('customers', 'customers.customer_id = sales_order.customer_id');
        $this->db->join('product_status', 'product_status.product_status_id = sales_order.product_status_id');
        $this->db->order_by('sales_order.sales_order_id', 'DESC');
        
        $this->data['content'] = $this->Sales_order_model->get();
        $this->data['title'] = 'Sales Order';

        $this->data['page_js'] = $this->load->view($this->uri->rsegment(1) . '/js', $this->data, TRUE);
        $this->load->view('templates/header', $this->data);
        $this->load->view($this->uri->rsegment(1) . '/index', $this->data);
        $this->load->view('templates/footer', $this->data);
    }

    public function form($id = null)
    {
        $id = $id !== null ? (int) $id : 0;

        if ($id > 0) {
            $sales_order = $this->Sales_order_model->get($id, TRUE);
            if (!$sales_order) {
                redirect('error_page/not_found');
            }

            $draft = $this->Product_status_model->get_by(
                ['product_status_name' => 'Draft', 'module' => 'sales_order'], TRUE
            );
            if (!$draft || (int) $sales_order->product_status_id !== (int) $draft->product_status_id) {
                $this->session->set_flashdata('error', 'Hanya Sales Order Draft yang dapat diedit.');
                redirect('sales_order');
            }

            $this->data['title'] = 'Edit Sales Order';
            $this->data['detail'] = $this->Sales_order_detail_model->get_by(['sales_order_id' => $id]);
        } else {
            $sales_order = $this->Sales_order_model->get_new();
            $this->data['title'] = 'Tambah Sales Order';
            $this->data['detail'] = [];
        }

        $this->db->where('is_active', 1);
        $this->db->order_by('customer_name', 'ASC');
        $this->data['customers'] = $this->Customer_model->get();

        $this->db->where('status', 1);
        $this->db->order_by('product_name', 'ASC');
        $this->data['products'] = $this->db->get('products')->result();

        $this->data['sales_order'] = $sales_order;
        $this->data['page_js'] = $this->load->view($this->uri->rsegment(1) . '/js', $this->data, TRUE);
        $this->load->view('templates/header', $this->data);
        $this->load->view($this->uri->rsegment(1) . '/form', $this->data);
        $this->load->view('templates/footer', $this->data);
    }

    public function save()
    {
        $id = (int) $this->input->post('sales_order_id'); // 0 = baru, >0 = edit
        if ($id < 0) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => 'ID Sales Order tidak valid.',
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]));
        }

        foreach (['payment_term_days', 'tax_percent'] as $field) {
            $_POST[$field] = $this->_normalize_decimal_input($this->input->post($field));
        }
        foreach (['qty', 'unit_price', 'discount_percent'] as $field) {
            $values = (array) $this->input->post($field);
            $_POST[$field] = [];
            foreach ($values as $key => $value) {
                $_POST[$field][$key] = $this->_normalize_decimal_input($value);
            }
        }

        // Validate normalized values so Indonesian thousands/decimal separators are accepted.
        $this->form_validation->set_rules(
            array_merge($this->Sales_order_model->rules, $this->Sales_order_detail_model->rules)
        );
        if ($this->form_validation->run() === FALSE) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => false,
                'message'   => strip_tags(validation_errors(' ', ' ')),
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]));
        }

        // 2. Cek header tambahan
        $customer = $this->Customer_model->get_by(
            ['customer_id' => (int) $this->input->post('customer_id'), 'is_active' => 1], TRUE
        );
        if (!$customer) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false, 'message' => 'Customer tidak valid atau nonaktif',
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]));
        }

        $so_date       = $this->input->post('so_date');
        $delivery_date = $this->input->post('delivery_date');
        if ($delivery_date && $delivery_date < $so_date) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false, 'message' => 'Tanggal kirim tidak boleh sebelum tanggal SO',
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]));
        }

        // 3. Susun detail dan hitung total di server
        $pids  = (array) $this->input->post('product_id');
        $qtys  = (array) $this->input->post('qty');
        $price = (array) $this->input->post('unit_price');
        $disc  = (array) $this->input->post('discount_percent');

        $valid = [];
        $this->db->where('status', 1);
        foreach ($this->db->get('products')->result() as $p) {
            $valid[(int) $p->product_id] = $p;
        }

        $details = [];
        $gross = 0;
        $net   = 0;
        $error = NULL;

        foreach ($pids as $i => $pid) {
            $pid = (int) $pid;
            $raw_qty = $qtys[$i] ?? '';
            $raw_price = $price[$i] ?? '';
            $raw_discount = $disc[$i] ?? '';
            $n   = $i + 1;

            if ($pid === 0 && $raw_qty === '' && $raw_price === '' && $raw_discount === '') continue;

            if (!isset($valid[$pid])) { $error = "Baris $n: produk tidak valid atau nonaktif"; break; }
            if (!is_numeric($raw_qty) || !is_numeric($raw_price) ||
                ($raw_discount !== '' && !is_numeric($raw_discount))) {
                $error = "Baris $n: qty, harga, atau diskon tidak valid";
                break;
            }

            $q = round((float) $raw_qty, 4);
            $p = round((float) $raw_price, 2);
            $d = $raw_discount !== '' ? round((float) $raw_discount, 2) : 0;

            if ($q <= 0)              { $error = "Baris $n: qty harus lebih dari 0"; break; }
            if ($p < 0)               { $error = "Baris $n: harga tidak boleh negatif"; break; }
            if ($d < 0 || $d > 100)   { $error = "Baris $n: diskon harus 0 sampai 100"; break; }

            $line_net = round($q * $p * (1 - $d / 100), 2);
            $gross   += round($q * $p, 2);
            $net     += $line_net;

            $details[] = [
                'product_id'       => $pid,
                'unit_id'          => $valid[$pid]->unit_id,   // satuan disalin dari master
                'qty'              => $q,
                'unit_price'       => $p,
                'discount_percent' => $d,
                'subtotal'         => $line_net,
            ];
        }

        if (!$error && count($details) < 1) {
            $error = 'Minimal 1 baris produk';
        }
        if ($error) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false, 'message' => $error,
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]));
        }

        $tax_percent = (float) $this->input->post('tax_percent');
        $tax_amount  = round($net * $tax_percent / 100, 2);

        $data = [
            'so_date'           => $so_date,
            'delivery_date'     => $delivery_date ?: NULL,
            'customer_id'       => $customer->customer_id,
            'payment_term_days' => (int) $this->input->post('payment_term_days'),
            'tax_percent'       => $tax_percent,
            'notes'             => $this->input->post('notes'),
            'subtotal'          => round($gross, 2),
            'discount_total'    => round($gross - $net, 2),
            'tax_amount'        => $tax_amount,
            'grand_total'       => round($net + $tax_amount, 2),
        ];

        // 4. Status: edit hanya saat Draft, baru butuh status Draft
        $draft = $this->Product_status_model->get_by(
            ['product_status_name' => 'Draft', 'module' => 'sales_order'], TRUE
        );
        if (!$draft) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false, 'message' => 'Status Draft (module sales_order) belum ada',
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]));
        }

        if ($id > 0) {
            $doc = $this->Sales_order_model->get_by(['sales_order_id' => $id], TRUE);
            if (!$doc || (int) $doc->product_status_id !== (int) $draft->product_status_id) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false, 'message' => 'Sales order tidak ditemukan atau bukan Draft',
                    'csrf_hash' => $this->security->get_csrf_hash(),
                ]));
            }
        } else {
            $data['product_status_id'] = $draft->product_status_id;
            $data['created_by']        = $this->session->userdata('employee_id');
        }

        // 5. Simpan dalam satu transaksi
        $this->db->trans_start();

        if ($id === 0) {
            // nomor SO dibuat langsung di sini, di dalam transaksi (FOR UPDATE)
            $prefix = 'SO-' . date('Y') . '-';
            $last   = $this->db->query(
                "SELECT so_no FROM sales_order WHERE so_no LIKE ? ORDER BY so_no DESC LIMIT 1 FOR UPDATE",
                [$prefix . '%']
            )->row();
            $next = $last ? ((int) substr($last->so_no, -4)) + 1 : 1;

            $data['so_no'] = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
            $id = $this->Sales_order_model->save($data);
        } else {
            $this->Sales_order_model->save($data, $id);
            $this->Sales_order_detail_model->delete_by(['sales_order_id' => $id]);
        }

        foreach ($details as &$d) {
            $d['sales_order_id'] = $id;
        }
        unset($d);
        $this->Sales_order_detail_model->insert_batch($details);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false, 'message' => 'Gagal menyimpan',
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]));
        }

        $saved = $this->Sales_order_model->get_by(['sales_order_id' => $id], TRUE);

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'         => true,
            'message'        => 'Sales order tersimpan',
            'sales_order_id' => $id,
            'so_no'          => $saved->so_no,
            'csrf_hash'      => $this->security->get_csrf_hash(),
        ]));
    }

    public function delete()
    {
        $id = (int) $this->input->post('id');
        $csrf_hash = $this->security->get_csrf_hash();

        if ($id < 1) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => 'ID Sales Order tidak valid.',
                'csrf_hash' => $csrf_hash,
            ]));
        }

        $draft = $this->Product_status_model->get_by(
            ['product_status_name' => 'Draft', 'module' => 'sales_order'], TRUE
        );
        $sales_order = $this->Sales_order_model->get($id, TRUE);
        if (!$draft || !$sales_order || (int) $sales_order->product_status_id !== (int) $draft->product_status_id) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => 'Sales Order tidak ditemukan atau bukan Draft.',
                'csrf_hash' => $csrf_hash,
            ]));
        }

        $this->db->trans_begin();
        $details_deleted = $this->Sales_order_detail_model->delete_by(['sales_order_id' => $id]);
        $order_deleted = $this->Sales_order_model->delete($id);

        if (!$details_deleted || !$order_deleted || $this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => false,
                'message' => 'Sales Order gagal dihapus.',
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]));
        }

        $this->db->trans_commit();
        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Sales Order berhasil dihapus.',
            'csrf_hash' => $this->security->get_csrf_hash(),
        ]));
    }

    private function _normalize_decimal_input($value)
    {
        $value = trim(str_ireplace(['Rp', "\xc2\xa0", ' '], '', (string) $value));
        if (strpos($value, ',') !== FALSE) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (substr_count($value, '.') > 1) {
            $value = str_replace('.', '', $value);
        }

        return $value;
    }
}