<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error_page extends MY_Controller
{
    public function index()
    {
        $this->not_found();
    }

    public function not_found()
    {
        $this->_show(404, 'Halaman Tidak Ditemukan',
            'Halaman yang kamu cari tidak ada atau sudah dipindahkan.');
    }

    public function forbidden()
    {
        $this->_show(403, 'Akses Ditolak',
            'Kamu tidak memiliki izin untuk membuka halaman ini.');
    }

    public function server_error()
    {
        $this->_show(500, 'Terjadi Kesalahan',
            'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.');
    }

    private function _show($code, $title, $message)
    {
        set_status_header($code);

        $this->data['error_code'] = $code;
        $this->data['message']    = $message;

        $this->render('error_page/index', $title);
    }
}