<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Journal_model extends MY_Model
{
    protected $_table_name  = 'journal';
    protected $_primary_key = 'journal_id';

    // DIUBAH: _primary_filter = intval (sesuai default MY_Model, bukan strval)
    protected $_primary_filter = 'intval';

    protected $_timestamps = FALSE;

    // DIUBAH: rules hanya untuk field yang dikirim via POST oleh user.
    // total_debit & total_credit TIDAK masuk rules — dihitung di server, bukan divalidasi dari form.
    public $rules = [
        [
            'field' => 'journal_date',
            'label' => 'Tanggal Jurnal',
            'rules' => 'trim|required',
        ],
        [
            'field' => 'description',
            'label' => 'Keterangan',
            'rules' => 'trim|required',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate nomor jurnal: JV-YYYY-NNNN.
     * HARUS dipanggil di dalam transaksi (trans_start sudah berjalan).
     * SELECT ... FOR UPDATE mencegah race condition saat request bersamaan.
     *
     * DIUBAH: pakai query builder manual dengan FOR UPDATE lock,
     *         filter LIKE per tahun (bukan global LIKE 'JV-'),
     *         order_by journal_no DESC agar urutan string benar.
     */
    public function generate_journal_no()
    {
        $year = date('Y');
        $prefix = 'JV-' . $year . '-';

        // BARU: lock baris terakhir supaya tidak ada race condition
        $query = $this->db->query(
            "SELECT journal_no FROM `{$this->_table_name}`
             WHERE journal_no LIKE ?
             ORDER BY journal_no DESC
             LIMIT 1
             FOR UPDATE",
            [$prefix . '%']
        );

        $last = $query->row();

        if ($last) {
            // Ambil 4 digit terakhir; substr aman karena format sudah terjamin
            $last_seq  = (int) substr($last->journal_no, -4);
            $next_seq  = $last_seq + 1;
        } else {
            $next_seq = 1;
        }

        // BARU: guard overflow (maks 9999 per tahun)
        if ($next_seq > 9999) {
            show_error('Nomor jurnal untuk tahun ' . $year . ' sudah habis (maks 9999).');
        }

        return $prefix . str_pad($next_seq, 4, '0', STR_PAD_LEFT);
    }
}