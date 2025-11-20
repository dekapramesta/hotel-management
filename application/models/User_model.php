<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    protected $table = 'users'; // Nama tabel users

    public function get_user($username)
    {
        return $this->db->get_where($this->table, ['username' => $username])->row();
    }
}
