<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Journal_detail_model extends MY_Model
{
    protected $_table_name  = 'journal_detail';
    protected $_primary_key = 'journal_detail_id';

    // DIUBAH: _primary_filter = intval (konsisten dengan MY_Model default)
    protected $_primary_filter = 'intval';

    protected $_timestamps = FALSE;

    /**
     * BARU: Rules validasi untuk array detail jurnal.
     *
     * CATATAN PENTING:
     * CI3 form_validation tidak bisa memvalidasi array multidimensi seperti
     * coa_id[0], coa_id[1], dst. secara native. Rules ini dipasang di
     * set_rules() controller HANYA untuk memastikan field array hadir di POST.
     * Validasi per-baris (debit XOR kredit, nilai numerik, akun valid)
     * dilakukan manual di _build_details() controller — jauh lebih andal.
     *
     * Oleh karena itu rules di sini SENGAJA minimal: hanya memastikan
     * field coa_id[], debit[], credit[], dan detail_description[] terkirim.
     * Validasi isi tiap baris ada di controller.
     */
    public $rules = [
        // BARU: validasi bahwa array coa_id hadir (wajib ada minimal 1 elemen)
        [
            'field' => 'coa_id[]',
            'label' => 'Akun',
            'rules' => 'required',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
    }
}