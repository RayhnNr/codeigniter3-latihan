<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Journal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model([
            'Journal_model',
            'Journal_detail_model',
            'Coa_model',
            'Product_status_model',
        ]);
    }

    // ===== LIST =====
    public function index()
    {
        // 1. Query tanpa join dijalankan lebih dulu (konvensi)
        $this->db->order_by('coa_code', 'ASC');
        $this->data['accounts'] = $this->Coa_model->get_by([
            'is_header' => 0,
            'is_active' => 1,
        ]);

        // 2. Query list (memakai join)
        $this->db->select('journal.journal_id, journal.journal_no, journal.journal_date,
                           journal.description, journal.total_debit, journal.total_credit,
                           product_status.product_status_name');
        $this->db->join('product_status', 'product_status.product_status_id = journal.product_status_id', 'left');
        $this->db->order_by('journal.journal_date', 'DESC');
        $this->db->order_by('journal.journal_id',   'DESC');

        $this->data['content'] = $this->Journal_model->get();
        $this->data['title']   = 'Jurnal';

        $this->load->view('templates/header', $this->data);
        $this->load->view('journal/index', $this->data);
        $this->load->view('templates/footer', $this->data);
        $this->load->view('journal/js', $this->data);
    }

    // ===== HALAMAN TAMBAH JURNAL =====
    // BARU: metode create() untuk halaman penuh (bukan modal)
    public function create()
    {
        // Akun aktif non-header untuk dropdown
        $this->db->order_by('coa_code', 'ASC');
        $this->data['accounts'] = $this->Coa_model->get_by([
            'is_header' => 0,
            'is_active' => 1,
        ]);

        $this->data['journal'] = NULL;
        $this->data['detail']  = [];
        $this->data['title']   = 'Tambah Jurnal';

        $this->load->view('templates/header', $this->data);
        $this->load->view('journal/form', $this->data);
        $this->load->view('templates/footer', $this->data);
        $this->load->view('journal/form_js', $this->data);
    }

    // ===== HALAMAN EDIT JURNAL =====
    // BARU: metode edit() untuk halaman penuh (bukan modal), redirect jika sudah Posted
    public function edit($id = NULL)
    {
        $id = (int) $id;

        $header = $this->_find($id);
        if (!$header) {
            $this->session->set_flashdata('error', 'Jurnal tidak ditemukan.');
            redirect('journal');
            return;
        }

        if (!$this->_is_draft($id)) {
            $this->session->set_flashdata('error', 'Jurnal sudah Posted, tidak dapat diedit.');
            redirect('journal');
            return;
        }

        // Detail dengan kode & nama akun untuk pre-populate form
        $this->db->select('journal_detail.*, coa.coa_code, coa.coa_name');
        $this->db->join('coa', 'coa.coa_id = journal_detail.coa_id', 'left');
        $this->db->order_by('journal_detail.journal_detail_id', 'ASC');
        $detail = $this->Journal_detail_model->get_by(['journal_detail.journal_id' => $id]);

        // Akun aktif non-header
        $this->db->order_by('coa_code', 'ASC');
        $this->data['accounts'] = $this->Coa_model->get_by([
            'is_header' => 0,
            'is_active' => 1,
        ]);

        $this->data['journal'] = $header;
        $this->data['detail']  = $detail ?: [];
        $this->data['title']   = 'Edit Jurnal';

        $this->load->view('templates/header', $this->data);
        $this->load->view('journal/form', $this->data);
        $this->load->view('templates/footer', $this->data);
        $this->load->view('journal/form_js', $this->data);
    }

    public function get_data($id = NULL)
    {
        $id = (int) $id;

        // 1. Header + nama status dalam satu query
        $this->db->select('journal.*, product_status.product_status_name AS status_name');
        $this->db->join('product_status', 'product_status.product_status_id = journal.product_status_id', 'left');
        $header = $id > 0 ? $this->Journal_model->get_by(['journal.journal_id' => $id], TRUE) : NULL;

        if (!$header) {
            return $this->_json(['status' => false, 'message' => 'Jurnal tidak ditemukan']);
        }

        // 2. Detail + nama akun
        $this->db->select('journal_detail.*, coa.coa_code, coa.coa_name');
        $this->db->join('coa', 'coa.coa_id = journal_detail.coa_id', 'left');
        $this->db->order_by('journal_detail.journal_detail_id', 'ASC');
        $detail = $this->Journal_detail_model->get_by(['journal_detail.journal_id' => $header->journal_id]);

        return $this->_json([
            'status'   => true,
            'header'   => $header,        // sudah memuat status_name
            'detail'   => $detail,
            'is_draft' => $this->_is_draft($header->journal_id),
        ]);
    }

    // ===== SIMPAN (VALIDASI + BUILD + CREATE/UPDATE) =====
    public function save()
    {
        $id = (int) $this->input->post('journal_id'); // 0 = baru, >0 = edit

        // 1. Validasi header
        $this->form_validation->set_rules($this->Journal_model->rules);
        if ($this->form_validation->run() === FALSE) {
            return $this->_json(['status' => false, 'message' => strip_tags(validation_errors(' ', ' | '))]);
        }

        // 2. Susun detail, validasi tiap baris, hitung total di server
        $build = $this->_build_details();
        if ($build['error']) {
            return $this->_json(['status' => false, 'message' => $build['error']]);
        }

        $data = [
            'journal_date' => $this->input->post('journal_date'),
            'description'  => $this->input->post('description'),
            'total_debit'  => $build['total_debit'],
            'total_credit' => $build['total_credit'],
        ];

        // 3. Cek status
        if ($id > 0) {
            if (!$this->_is_draft($id)) {
                return $this->_json(['status' => false, 'message' => 'Jurnal tidak ada atau sudah tidak berstatus Draft']);
            }
        } else {
            $draft = $this->_status('Draft');
            if (!$draft) {
                return $this->_json(['status' => false, 'message' => 'Status Draft (module journal) belum ada di product_status']);
            }
            $data['product_status_id'] = $draft->product_status_id;
        }

        // 4. Simpan dalam satu transaksi
        $this->db->trans_start();

        if ($id === 0) {
            $data['journal_no'] = $this->Journal_model->generate_journal_no();
            $id = $this->Journal_model->save($data);
        } else {
            $this->Journal_model->save($data, $id);
            $this->Journal_detail_model->delete_by(['journal_id' => $id]);
        }

        foreach ($build['details'] as &$d) {
            $d['journal_id'] = $id;
        }
        unset($d);
        $this->Journal_detail_model->insert_batch($build['details']);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return $this->_json(['status' => false, 'message' => 'Gagal menyimpan, transaksi dibatalkan']);
        }

        $doc = $this->_find($id);

        // DIUBAH: set flashdata untuk ditampilkan di halaman list
        $this->session->set_flashdata('success', ($this->input->post('journal_id') > 0 ? 'Jurnal berhasil diperbarui.' : 'Jurnal berhasil disimpan.'));

        return $this->_json([
            'status'     => true,
            'message'    => 'Jurnal tersimpan',
            'journal_id' => $id,
            'journal_no' => $doc ? $doc->journal_no : '',
            'redirect'   => base_url('journal'),
        ]);
    }

    // ===== POSTING: Draft -> Posted =====
    public function post($id = NULL)
    {
        $id = (int) $id;

        if (!$this->_is_draft($id)) {
            return $this->_json(['status' => false, 'message' => 'Jurnal tidak ada atau bukan Draft']);
        }

        $rows = $this->Journal_detail_model->get_by(['journal_id' => $id]);

        if (!$rows || count($rows) < 2) {
            return $this->_json(['status' => false, 'message' => 'Jurnal minimal 2 baris detail']);
        }

        $td = 0; $tc = 0;
        foreach ($rows as $r) {
            $td += (float) $r->debit;
            $tc += (float) $r->credit;
        }

        if (round($td, 2) !== round($tc, 2)) {
            return $this->_json(['status' => false, 'message' => 'Debit dan kredit tidak seimbang']);
        }

        if (round($td, 2) <= 0) {
            return $this->_json(['status' => false, 'message' => 'Total jurnal tidak boleh nol']);
        }

        $posted = $this->_status('Posted');
        if (!$posted) {
            return $this->_json(['status' => false, 'message' => 'Status Posted (module journal) belum ada di product_status']);
        }

        $this->Journal_model->save([
            'total_debit'       => round($td, 2),
            'total_credit'      => round($tc, 2),
            'product_status_id' => $posted->product_status_id,
        ], $id);

        return $this->_json(['status' => true, 'message' => 'Jurnal berhasil diposting']);
    }

    // ===== HAPUS (hanya Draft) =====
    public function delete($id = NULL)
    {
        $id = (int) $id;

        if (!$this->_is_draft($id)) {
            return $this->_json(['status' => false, 'message' => 'Jurnal tidak ada atau sudah Posted']);
        }

        $ok = $this->Journal_model->delete($id);

        return $this->_json([
            'status'  => (bool) $ok,
            'message' => $ok ? 'Jurnal dihapus' : 'Gagal menghapus',
        ]);
    }

    // ===== HELPER =====

    private function _build_details()
    {
        $coa_ids = (array) $this->input->post('coa_id');
        $debits  = (array) $this->input->post('debit');
        $credits = (array) $this->input->post('credit');
        $descs   = (array) $this->input->post('detail_description');

        $valid = [];
        foreach ($this->Coa_model->get_by(['is_header' => 0, 'is_active' => 1]) as $a) {
            $valid[(int) $a->coa_id] = TRUE;
        }

        $details = [];
        $td = 0.0; $tc = 0.0;

        foreach ($coa_ids as $i => $raw_coa_id) {
            $coa_id = (int) $raw_coa_id;

            $raw_d = isset($debits[$i])  ? trim((string) $debits[$i])  : '';
            $raw_c = isset($credits[$i]) ? trim((string) $credits[$i]) : '';

            // Normalisasi format ribuan titik + koma desimal
            $raw_d = str_replace(',', '.', str_replace('.', '', $raw_d));
            $raw_c = str_replace(',', '.', str_replace('.', '', $raw_c));

            $d = is_numeric($raw_d) ? round((float) $raw_d, 2) : 0.0;
            $c = is_numeric($raw_c) ? round((float) $raw_c, 2) : 0.0;

            $n = $i + 1;

            if ($coa_id === 0 && $d == 0 && $c == 0) continue;

            if (!isset($valid[$coa_id])) {
                return $this->_fail("Baris $n: akun tidak valid, nonaktif, atau akun header");
            }
            if ($d < 0 || $c < 0) {
                return $this->_fail("Baris $n: nilai tidak boleh negatif");
            }
            if ($d > 0 && $c > 0) {
                return $this->_fail("Baris $n: isi salah satu saja (debit atau kredit), tidak boleh keduanya");
            }
            if ($d == 0 && $c == 0) {
                return $this->_fail("Baris $n: akun dipilih tapi nilai debit dan kredit keduanya nol");
            }

            $td += $d;
            $tc += $c;

            $details[] = [
                'coa_id'      => $coa_id,
                'debit'       => $d,
                'credit'      => $c,
                'description' => isset($descs[$i]) ? trim((string) $descs[$i]) : NULL,
            ];
        }

        if (count($details) < 2) {
            return $this->_fail('Jurnal minimal 2 baris detail yang terisi');
        }

        if (round($td, 2) !== round($tc, 2)) {
            return $this->_fail(
                'Debit (' . number_format($td, 2, ',', '.') . ') dan kredit ('
                . number_format($tc, 2, ',', '.') . ') tidak seimbang'
            );
        }

        if (round($td, 2) <= 0) {
            return $this->_fail('Total jurnal tidak boleh nol');
        }

        return [
            'error'        => NULL,
            'details'      => $details,
            'total_debit'  => round($td, 2),
            'total_credit' => round($tc, 2),
        ];
    }

    private function _fail($msg)
    {
        return ['error' => $msg, 'details' => [], 'total_debit' => 0, 'total_credit' => 0];
    }

    private function _status($name)
    {
        return $this->Product_status_model->get_by(
            ['product_status_name' => $name, 'module' => 'journal'], TRUE
        );
    }

    private function _find($id)
    {
        $id = (int) $id;
        return $id > 0 ? $this->Journal_model->get_by(['journal_id' => $id], TRUE) : NULL;
    }

    private function _is_draft($id)
    {
        $doc   = $this->_find($id);
        $draft = $this->_status('Draft');
        return $doc && $draft && (int) $doc->product_status_id === (int) $draft->product_status_id;
    }

    private function _json($arr)
    {
        $arr['csrf_hash'] = $this->security->get_csrf_hash();
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($arr));
    }
}