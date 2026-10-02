<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions
{
    public function show_404($page = '', $log_error = TRUE)
    {
        if ($log_error) {
            log_message('error', '404 Page Not Found: ' . $page);
        }

        if ($this->_use_template()) {
            $this->_go('error_page/not_found');
        }

        return parent::show_404($page, FALSE); // fallback polos
    }

    public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        if ($template === 'error_general' && $this->_use_template()) {
            log_message('error', $heading . ' | ' . (is_array($message) ? implode(' ', $message) : $message));

            if ($status_code == 403) {
                $this->_go('error_page/forbidden');
            } elseif ($status_code == 404) {
                $this->_go('error_page/not_found');
            }
            $this->_go('error_page/server_error');
        }

        // error_db, error_php, AJAX, CLI: tetap view polos di views/errors/
        return parent::show_error($heading, $message, $template, $status_code);
    }

    private function _use_template()
    {
        if (is_cli() || headers_sent()) return FALSE;

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return FALSE;
        }

        // cegah loop kalau error terjadi di halaman error itu sendiri
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'error_page') !== FALSE) {
            return FALSE;
        }

        return TRUE;
    }

    private function _go($path)
    {
        $index = config_item('index_page');
        $url   = rtrim(config_item('base_url'), '/') . '/' . ($index ? $index . '/' : '') . $path;

        header('Location: ' . $url, TRUE, 302);
        exit;
    }
}