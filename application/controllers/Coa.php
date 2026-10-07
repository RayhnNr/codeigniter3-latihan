<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coa extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Coa_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['parents'] = $this->Coa_model->get_by([
            'is_header' => 1,
            'is_active' => 1,
        ]);

        // Load the list only after the parent query, since MY_Model reuses the
        // active query builder.
        $this->db->select('coa.coa_id, coa.coa_code, coa.coa_name, coa.coa_type,
                        coa.is_header, coa.is_active,
                        parent.coa_code AS parent_code,
                        parent.coa_name AS parent_name');
        $this->db->join('coa AS parent', 'parent.coa_id = coa.parent_id', 'left');
        $this->db->order_by('coa.coa_code', 'ASC');

        $data['content'] = $this->Coa_model->get();
        $data['title']   = 'Chart of Account';
        $data['extra_script'] = 'assets/adminlte/plugins/bootstrap-switch/js/bootstrap-switch.min.js';

        $this->load->view('templates/header', $data);
        $this->load->view($this->uri->rsegment(1) . '/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view($this->uri->rsegment(1) . '/js', $data);
    }

    public function save()
    {
        $id = (int) $this->input->post('coa_id');

        $this->form_validation->set_rules($this->Coa_model->rules);
        if ($this->form_validation->run() === FALSE) {
            return $this->_json(['status' => false, 'message' => strip_tags(validation_errors(' ', ' '))]);
        }

        $data = [
            'coa_name'  => $this->input->post('coa_name', TRUE),
            'is_active' => $this->input->post('is_active') === '1' ? 1 : 0,
        ];

        if ($id > 0) {
            $coa = $this->Coa_model->get($id, TRUE);
            if (!$coa) {
                return $this->_json(['status' => false, 'message' => 'Akun tidak ditemukan']);
            }

            if ($coa->is_header && !$data['is_active']) {
                $active_children = $this->Coa_model->get_by(['parent_id' => $id, 'is_active' => 1]);
                if (!empty($active_children)) {
                    return $this->_json(['status' => false, 'message' => 'Nonaktifkan dulu semua akun di bawah header ini']);
                }
            }
        } else {
            $parent = $this->Coa_model->get((int) $this->input->post('parent_id'), TRUE);
            if (!$parent || !$parent->is_header || !$parent->is_active) {
                return $this->_json(['status' => false, 'message' => 'Pilih parent yang valid (akun header aktif)']);
            }

            $code = $this->Coa_model->generate_coa_code($parent->coa_id);
            if (!$code) {
                return $this->_json(['status' => false, 'message' => 'Kode akun di bawah parent ini sudah penuh']);
            }

            if ($this->Coa_model->get_by(['coa_code' => $code], TRUE)) {
                return $this->_json(['status' => false, 'message' => 'Kode ' . $code . ' baru saja dipakai, silakan simpan ulang']);
            }

            $data['coa_code']  = $code;
            $data['coa_type']  = $parent->coa_type; // diwarisi dari parent
            $data['parent_id'] = $parent->coa_id;
            $data['is_header'] = 0;
        }

        $saved_id = $this->Coa_model->save($data, $id > 0 ? $id : NULL);

        if (!$saved_id) {
            return $this->_json(['status' => false, 'message' => 'Gagal menyimpan']);
        }

        $doc = $this->Coa_model->get($saved_id, TRUE);

        return $this->_json([
            'status'   => true,
            'message'  => 'Akun tersimpan',
            'coa_id'   => $saved_id,
            'coa_code' => $doc->coa_code,
        ]);
    }
    public function generate_code()
    {
        $parent = $this->Coa_model->get((int) $this->input->get('parent_id'), TRUE);
        $code = ($parent && $parent->is_header && $parent->is_active)
            ? $this->Coa_model->generate_coa_code($parent->coa_id)
            : NULL;

        return $this->_json([
            'status'   => (bool) $code,
            'message'  => $code ? 'Kode akun berhasil dibuat' : 'Parent tidak valid atau kode akun sudah penuh',
            'coa_code' => $code,
            'coa_type' => $parent ? $parent->coa_type : '',
        ]);
    }

    public function get_data($id = NULL)
    {
        $coa = $this->Coa_model->get((int) $id, TRUE);

        return $this->_json($coa
            ? ['status' => true, 'message' => 'Data akun ditemukan', 'data' => $coa]
            : ['status' => false, 'message' => 'Akun tidak ditemukan']);
    }

    public function delete($id = NULL)
    {
        $id = (int) $id;
        if ($id < 1) {
            return $this->_json(['status' => false, 'message' => 'ID akun tidak valid']);
        }

        $coa = $this->Coa_model->get($id, TRUE);
        if (!$coa) {
            return $this->_json(['status' => false, 'message' => 'Akun tidak ditemukan']);
        }

        if ($coa->is_header || $this->Coa_model->get_by(['parent_id' => $id], TRUE)) {
            return $this->_json(['status' => false, 'message' => 'Akun header atau akun yang masih memiliki anak tidak dapat dihapus']);
        }

        if (!$this->Coa_model->delete($id)) {
            return $this->_json(['status' => false, 'message' => 'Gagal menghapus akun']);
        }

        return $this->_json(['status' => true, 'message' => 'Akun berhasil dihapus']);
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
