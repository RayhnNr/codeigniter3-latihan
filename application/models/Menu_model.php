<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model{
    protected $table = 'menus';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data() {
        $this->db->select('menus.*, product_status.product_status_name');
        $this->db->from($this->table);
        $this->db->join('product_status', 'product_status.product_status_id = menus.status', 'left');
        $this->db->order_by('menus.id', 'ASC');
        return $this->db->get()->result();
    }

    public function get_parent_menus($exclude_id = null) {
        $menus = $this->db
            ->order_by('order_no', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->table)
            ->result();

        $children = [];
        foreach ($menus as $menu) {
            $children[(int) $menu->parent_id][] = $menu;
        }

        $excluded = [];
        if ($exclude_id) {
            $excluded[(int) $exclude_id] = true;
            $pending = [(int) $exclude_id];
            while ($pending) {
                $parent_id = array_pop($pending);
                foreach ($children[$parent_id] ?? [] as $child) {
                    $child_id = (int) $child->id;
                    if (isset($excluded[$child_id])) {
                        continue;
                    }
                    $excluded[$child_id] = true;
                    $pending[] = $child_id;
                }
            }
        }

        $options = [];
        $append_options = function ($parent_id, $depth) use (&$append_options, &$options, $children, $excluded) {
            foreach ($children[$parent_id] ?? [] as $menu) {
                if (isset($excluded[(int) $menu->id])) {
                    continue;
                }
                if (strtolower(trim((string) $menu->url)) === 'javascript:;') {
                    $menu->depth = $depth;
                    $options[] = $menu;
                }
                $append_options((int) $menu->id, $depth + 1);
            }
        };
        $append_options(0, 0);

        return $options;
    }

    public function get_next_order($parent_id, $exclude_id = null) {
        $this->db->select_max('order_no');
        $this->db->where('parent_id', (int) $parent_id);
        if ($exclude_id) {
            $this->db->where('id !=', (int) $exclude_id);
        }
        $row = $this->db->get($this->table)->row();
        return ((int) ($row->order_no ?? 0)) + 1;
    }

    public function get_for_role($role_id, $role_slug = '') {
        $this->db->select('menus.*');
        $this->db->from($this->table);
        $this->db->where('menus.status', 1);

        $menus = $this->db
            ->order_by('menus.order_no', 'ASC')
            ->order_by('menus.id', 'ASC')
            ->get()
            ->result();

        $is_unrestricted = in_array(strtolower((string) $role_slug), ['superadmin', 'admin'], true)
            || in_array((int) $role_id, [1, 2], true);
        if ($is_unrestricted) {
            return $menus;
        }

        $permission_rows = $this->db
            ->select('menu_id')
            ->where('role_id', (int) $role_id)
            ->where('can_view', 1)
            ->get('role_menu_permissions')
            ->result();
        $allowed = [];
        foreach ($permission_rows as $permission) {
            $allowed[(int) $permission->menu_id] = true;
        }

        $parent_by_id = [];
        foreach ($menus as $menu) {
            $parent_by_id[(int) $menu->id] = (int) $menu->parent_id;
        }

        foreach ($permission_rows as $permission) {
            $ancestor_id = $parent_by_id[(int) $permission->menu_id] ?? 0;
            while ($ancestor_id !== 0) {
                $allowed[$ancestor_id] = true;
                $ancestor_id = $parent_by_id[$ancestor_id] ?? 0;
            }
        }

        return array_values(array_filter($menus, function ($menu) use ($allowed) {
            return isset($allowed[(int) $menu->id]);
        }));
    }

    public function get_by_id($id) {
        $this->db->select('menus.*, product_status.product_status_name');
        $this->db->from($this->table);
        $this->db->join('product_status', 'product_status.product_status_id = menus.status', 'left');
        $this->db->where('menus.id', $id);
        return $this->db->get()->row();
    }

    public function insert($data){
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
    }

    public function update($id, $data){
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
    }

    public function delete($id){
        $this->db->where('id', $id);
        $this->db->delete($this->table);
    }

    public function is_used_in_permission($id){
        return $this->db->where('menu_id', $id)->count_all_results('role_menu_permissions');
    }

    public function is_url_exists($url, $exclude_id = null){
        if (strtolower(trim($url)) === 'javascript:;') {
            return false;
        }

        $this->db->where('url', $url);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }

    // Daftar status Aktif/Nonaktif dipakai bareng dari module 'product'
    public function get_status(){
        $this->db->where('module', 'product');
        $this->db->order_by('product_status_id', 'ASC');
        return $this->db->get('product_status')->result();
    }
}