<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->library('form_validation');
    }

    public function index(){
        $data = $this->data;
        $data['title'] = 'Product';

        $data['categories']             = $this->Product_model->get_category();
        $data['brands']                 = $this->Product_model->get_brand();
        $data['units']                  = $this->Product_model->get_unit();
        $data['product_type']           = $this->Product_model->get_product_type();
        $data['product_status']         = $this->Product_model->get_status();
        $data['product_code_preview']   = $this->Product_model->generate_product_code();

        $this->load->view('templates/header', $data);
        $this->load->view('product/index', $data);
        $this->load->view('templates/footer');
    }

    public function get_data(){
        $filter = [
            'status'       => $this->input->post('status'),
            'category_id'  => $this->input->post('category_id'),
            'brand_id'     => $this->input->post('brand_id'),
            'product_type' => $this->input->post('product_type'),
        ];

        $list = $this->Product_model->get_datatables($filter);

        $data = [];
        $no = $_POST['start'] + 1;

        foreach ($list as $row) {
            $data[] = [
                'no'                => $no++,
                'created_by_username' => $row->created_by_username,
                'product_code'      => $row->product_code,
                'product_name'      => $row->product_name,
                'category_name'     => $row->category_name,
                'brand_name'        => $row->brand_name,
                'unit_name'         => $row->unit_name,
                'product_type_name' => $row->product_type_name,
                'status'            => $row->status,
                'product_id'        => $row->product_id, // dibutuhkan buat tombol Edit/Delete
                'DT_RowIndex'       => $no - 1,
            ];
        }

        $output = [
            "draw"            => intval($_POST['draw']),
            "recordsTotal"    => $this->Product_model->count_all(),
            "recordsFiltered" => $this->Product_model->count_filtered($filter),
            "data"            => $data,
        ];

        echo json_encode($output);
    }

    public function get_detail($id){
        $data = $this->Product_model->get_by_id($id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function create(){
        $data = $this->data;
        $data['categories']           = $this->Product_model->get_category();
        $data['brands']               = $this->Product_model->get_brand();
        $data['units']                = $this->Product_model->get_unit();
        $data['product_type']         = $this->Product_model->get_product_type();
        $data['product_status']       = $this->Product_model->get_status();
        $data['product_code_preview'] = $this->Product_model->generate_product_code();
        $data['mode']                 = 'create';
        $data['product']              = null;
        $data['active_status_id']     = $this->Product_model->get_status_id_by_name('Aktif');
        $data['inactive_status_id']   = $this->Product_model->get_status_id_by_name('Nonaktif');

        $this->load->view('templates/header', $data);
        $this->load->view('product/form', $data);
        $this->load->view('templates/footer');
    }

    public function store(){
        // $this->load->library('form_validation');

        $this->form_validation->set_rules('product_name', 'Nama Product', 'required|max_length[150]|trim');
        $this->form_validation->set_rules('category_id', 'Category', 'required|numeric');
        $this->form_validation->set_rules('brand_id', 'Brand', 'required|numeric');
        $this->form_validation->set_rules('unit_id', 'Unit', 'required|numeric');
        $this->form_validation->set_rules('product_type', 'Product Type', 'required|numeric');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim');
        $this->form_validation->set_rules('status', 'Status', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'failed',
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }

        $this->db->trans_begin();

        $product_code = $this->Product_model->generate_product_code();

        $product_id = $this->Product_model->insert_header([
            'product_code' => $product_code,
            'created_by'   => $this->session->userdata('user_id'),
            'product_name' => $this->input->post('product_name'),
            'category_id'  => $this->input->post('category_id'),
            'brand_id'     => $this->input->post('brand_id'),
            'unit_id'      => $this->input->post('unit_id'),
            'product_type' => $this->input->post('product_type'),
            'description'  => $this->input->post('description'),
            'status'       => $this->input->post('status'),
        ]);

        $product_images = [];
        $upload_errors = [];
        $primary_index = $this->input->post('is_primary_index');
        if (!empty($_FILES['product_images']['name'][0])) {
            $files = $_FILES['product_images'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    // Setup upload configuration for this specific file
                    $config['upload_path']   = './uploads/products/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size']      = 2048; // 2MB
                    $config['encrypt_name']  = TRUE; // Rename with random name

                    // Upload the file
                    $this->load->library('upload', $config);
                    $_FILES['userfile'] = array(
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    );

                    if ($this->upload->do_upload('userfile')) {
                        $upload_data = $this->upload->data();
                        $product_images[] = array(
                            'product_id'  => $product_id,
                            'file_name'   => $upload_data['file_name'],
                            'is_primary'  => ((string) $i === (string) $primary_index) ? 1 : 0,
                            'sort_order'  => $i + 1,
                            'uploaded_at' => date('Y-m-d H:i:s'),
                        );
                    } else {
                        // Handle upload error for this specific file
                        $upload_errors[] = $this->upload->display_errors('', '');
                    }
                }
            }
        }

        if (!empty($upload_errors)) {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => 'failed',
                'message' => implode(' ', $upload_errors)
            ]);
            return;
        }

        $this->Product_model->insert_detail($product_images);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode([
                'status'  => 'failed',
                'message' => 'Gagal menyimpan data.'
            ]);
            return;
        }

        $this->db->trans_commit();
        echo json_encode([
            'status'  => 'success',
            'message' => 'Product ' . $product_code . ' berhasil ditambahkan.'
        ]);

    }

    public function edit($id){
        $product = $this->Product_model->get_by_id($id);

        if (!$product) {
            $this->session->set_flashdata('error', 'Data product tidak ditemukan.');
            redirect('product');
            return;
        }

        $data = $this->data;
        $data['categories']     = $this->Product_model->get_category();
        $data['brands']         = $this->Product_model->get_brand();
        $data['units']          = $this->Product_model->get_unit();
        $data['product_type']   = $this->Product_model->get_product_type();
        $data['product_status'] = $this->Product_model->get_status();
        $data['product']        = $product;
        $data['product_images'] = $this->Product_model->get_images($id);
        $data['mode']           = 'edit';
        $data['active_status_id']     = $this->Product_model->get_status_id_by_name('Aktif');
        $data['inactive_status_id']   = $this->Product_model->get_status_id_by_name('Nonaktif');

        $this->load->view('templates/header', $data);
        $this->load->view('product/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update(){
        $product_id = $this->input->post('product_id');

        if (empty($product_id)) {
            echo json_encode(['status' => 'failed', 'message' => 'ID product tidak valid.']);
            return;
        }

        $this->form_validation->set_rules('product_name', 'Nama Product', 'required|max_length[150]|trim');
        $this->form_validation->set_rules('category_id', 'Category', 'required|numeric');
        $this->form_validation->set_rules('brand_id', 'Brand', 'required|numeric');
        $this->form_validation->set_rules('unit_id', 'Unit', 'required|numeric');
        $this->form_validation->set_rules('product_type', 'Product Type', 'required|numeric');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim');
        $this->form_validation->set_rules('status', 'Status', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'failed',
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }

        $data = array(
            // 'created_by'   => $this->session->userdata('user_id'),
            'product_name' => $this->input->post('product_name'),
            'category_id'  => $this->input->post('category_id'),
            'brand_id'     => $this->input->post('brand_id'),
            'unit_id'      => $this->input->post('unit_id'),
            'product_type' => $this->input->post('product_type'),
            'description'  => $this->input->post('description'),
            'status'       => $this->input->post('status'),
        );
        // catatan: product_code TIDAK diubah saat edit, biarkan tetap seperti data lama

        $this->db->trans_begin();

        $this->Product_model->update($product_id, $data);

        $deleted_image_ids = $this->input->post('deleted_image_id');
        if (!empty($deleted_image_ids)) {
            foreach ((array) $deleted_image_ids as $image_id) {
                $this->Product_model->delete_image($product_id, $image_id);
            }
        }

        $this->Product_model->set_images_not_primary($product_id);

        $primary_type = $this->input->post('is_primary_row_type');
        $primary_value = $this->input->post('is_primary_value');
        $primary_type = is_array($primary_type) ? end($primary_type) : $primary_type;
        $primary_value = is_array($primary_value) ? end($primary_value) : $primary_value;

        if ($primary_type === 'existing' && is_numeric($primary_value)) {
            $this->Product_model->set_image_primary($product_id, $primary_value);
        }

        $product_images = [];
        $new_image_ids = [];
        $upload_errors = [];
        if (!empty($_FILES['product_images']['name'][0])) {
            $files = $_FILES['product_images'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $config = [
                    'upload_path'   => './uploads/products/',
                    'allowed_types' => 'jpg|jpeg|png|gif',
                    'max_size'      => 2048,
                    'encrypt_name'  => TRUE,
                ];
                $this->load->library('upload', $config);
                $_FILES['userfile'] = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];

                if ($this->upload->do_upload('userfile')) {
                    $upload_data = $this->upload->data();
                    $product_images[] = [
                        'product_id'  => $product_id,
                        'file_name'   => $upload_data['file_name'],
                        'is_primary'  => 0,
                        'sort_order'  => $i + 1,
                        'uploaded_at' => date('Y-m-d H:i:s'),
                    ];
                } else {
                    $upload_errors[] = $this->upload->display_errors('', '');
                }
            }
        }

        if (!empty($upload_errors)) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'failed', 'message' => implode(' ', $upload_errors)]);
            return;
        }

        $replace_files = $_FILES['replace_images'] ?? null;
        $replace_image_ids = (array) $this->input->post('replace_image_ids');
        if ($replace_files && !empty($replace_files['name'])) {
            foreach ($replace_files['name'] as $i => $original_name) {
                if (empty($original_name) || ($replace_files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                    continue;
                }

                $config = [
                    'upload_path'   => './uploads/products/',
                    'allowed_types' => 'jpg|jpeg|png|gif',
                    'max_size'      => 2048,
                    'encrypt_name'  => TRUE,
                ];
                $this->load->library('upload', $config);
                $_FILES['userfile'] = [
                    'name'     => $replace_files['name'][$i],
                    'type'     => $replace_files['type'][$i],
                    'tmp_name' => $replace_files['tmp_name'][$i],
                    'error'    => $replace_files['error'][$i],
                    'size'     => $replace_files['size'][$i],
                ];

                if (!$this->upload->do_upload('userfile')) {
                    $upload_errors[] = $this->upload->display_errors('', '');
                    continue;
                }

                $image_id = $replace_image_ids[$i] ?? null;
                $old_image = $this->Product_model->get_image($product_id, $image_id);
                $upload_data = $this->upload->data();
                if (!$old_image || !$this->Product_model->update_image($product_id, $image_id, $upload_data['file_name'])) {
                    @unlink('./uploads/products/' . $upload_data['file_name']);
                    $upload_errors[] = 'Foto pengganti tidak dapat disimpan.';
                    continue;
                }

                if (!empty($old_image->file_name)) {
                    @unlink('./uploads/products/' . $old_image->file_name);
                }
            }
        }

        if (!empty($upload_errors)) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'failed', 'message' => implode(' ', $upload_errors)]);
            return;
        }

        if (!empty($product_images)) {
            $new_image_ids = $this->Product_model->insert_detail($product_images);
        }

        if ($primary_type === 'new' && is_numeric($primary_value) && isset($new_image_ids[(int) $primary_value])) {
            $this->Product_model->set_image_primary($product_id, $new_image_ids[(int) $primary_value]);
        } elseif ($primary_type !== 'existing' && $primary_type !== 'new') {
            $this->Product_model->set_first_image_primary($product_id);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'failed', 'message' => 'Gagal memperbarui data product.']);
            return;
        }

        $this->db->trans_commit();
        $this->session->set_flashdata('success', 'Product berhasil diperbarui.');
        echo json_encode([
            'status'  => 'success',
            'message' => 'Product berhasil diperbarui.'
        ]);
    }

    public function delete(){
        $id = $this->input->post('id');

        if (empty($id)) {
            echo json_encode(['status' => 'failed', 'message' => 'ID product tidak valid.']);
            return;
        }

        // pastikan data memang ada sebelum dihapus
        $product = $this->Product_model->get_by_id($id);
        if (!$product) {
            echo json_encode(['status' => 'failed', 'message' => 'Data tidak ditemukan.']);
            return;
        }

        $this->Product_model->delete($id);
        $this->session->set_flashdata('success', 'Product ' . $product->product_code . ' berhasil dihapus.');
        echo json_encode([
            'status'  => 'success',
            'message' => 'Product ' . $product->product_code . ' berhasil dihapus.'
        ]);
    }

    public function replace_image($image_id){
        $product_id = $this->input->post('product_id');
        $image = $this->Product_model->get_image($product_id, $image_id);

        if (!$image) {
            echo json_encode(['status' => 'failed', 'message' => 'Data foto tidak ditemukan.']);
            return;
        }

        if (empty($_FILES['image']['name'])) {
            echo json_encode(['status' => 'failed', 'message' => 'Silakan pilih foto pengganti.']);
            return;
        }

        $config = [
            'upload_path'   => './uploads/products/',
            'allowed_types' => 'jpg|jpeg|png|gif',
            'max_size'      => 2048,
            'encrypt_name'  => TRUE,
        ];
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('image')) {
            echo json_encode(['status' => 'failed', 'message' => strip_tags($this->upload->display_errors('', ''))]);
            return;
        }

        $upload_data = $this->upload->data();
        if (!$this->Product_model->update_image($product_id, $image_id, $upload_data['file_name'])) {
            @unlink('./uploads/products/' . $upload_data['file_name']);
            echo json_encode(['status' => 'failed', 'message' => 'Foto gagal diperbarui.']);
            return;
        }

        if (!empty($image->file_name)) {
            @unlink('./uploads/products/' . $image->file_name);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Foto berhasil diganti.',
            'image_url' => base_url('uploads/products/' . rawurlencode($upload_data['file_name']))
        ]);
    }

    public function add_image(){
        $product_id = $this->input->post('product_id');
        $product = $this->Product_model->get_by_id($product_id);

        if (!$product) {
            echo json_encode(['status' => 'failed', 'message' => 'Data product tidak ditemukan.']);
            return;
        }

        if (empty($_FILES['image']['name'])) {
            echo json_encode(['status' => 'failed', 'message' => 'Silakan pilih foto.']);
            return;
        }

        $config = [
            'upload_path'   => './uploads/products/',
            'allowed_types' => 'jpg|jpeg|png|gif',
            'max_size'      => 2048,
            'encrypt_name'  => TRUE,
        ];
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('image')) {
            echo json_encode(['status' => 'failed', 'message' => strip_tags($this->upload->display_errors('', ''))]);
            return;
        }

        $upload_data = $this->upload->data();
        $is_primary = $this->input->post('is_primary') == '1' ? 1 : 0;
        if ($is_primary) {
            $this->Product_model->set_images_not_primary($product_id);
        }

        $image_ids = $this->Product_model->insert_detail([[
            'product_id'  => $product_id,
            'file_name'   => $upload_data['file_name'],
            'is_primary'  => $is_primary,
            'sort_order'  => count($this->Product_model->get_images($product_id)) + 1,
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]]);

        if (empty($image_ids)) {
            @unlink('./uploads/products/' . $upload_data['file_name']);
            echo json_encode(['status' => 'failed', 'message' => 'Foto gagal disimpan.']);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Foto berhasil disimpan.',
            'image_id' => $image_ids[0],
            'image_url' => base_url('uploads/products/' . rawurlencode($upload_data['file_name']))
        ]);
    }

    public function set_primary_image($image_id){
        $product_id = $this->input->post('product_id');
        $image = $this->Product_model->get_image($product_id, $image_id);

        if (!$image) {
            echo json_encode(['status' => 'failed', 'message' => 'Data foto tidak ditemukan.']);
            return;
        }

        $this->db->trans_begin();
        $this->Product_model->set_images_not_primary($product_id);
        $this->Product_model->set_image_primary($product_id, $image_id);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'failed', 'message' => 'Gambar utama gagal diubah.']);
            return;
        }

        $this->db->trans_commit();
        echo json_encode(['status' => 'success', 'message' => 'Gambar utama berhasil diubah.']);
    }

    public function delete_image($image_id){
        $product_id = $this->input->post('product_id');
        $image = $this->Product_model->get_image($product_id, $image_id);

        if (!$image) {
            echo json_encode(['status' => 'failed', 'message' => 'Data foto tidak ditemukan.']);
            return;
        }

        if (!$this->Product_model->delete_image($product_id, $image_id)) {
            echo json_encode(['status' => 'failed', 'message' => 'Foto gagal dihapus.']);
            return;
        }

        if (!empty($image->file_name)) {
            @unlink('./uploads/products/' . $image->file_name);
        }

        $this->Product_model->set_first_image_primary($product_id);
        echo json_encode(['status' => 'success', 'message' => 'Foto berhasil dihapus.']);
    }
}