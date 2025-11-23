<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guest_model extends CI_Model {

    public function get_by_face_id($user_face_id) {
        $sql = "SELECT id, nama, nik, telepon AS hp, alamat, email, user_face_id, kendaraan, nomor_polisi, unit_induk, jabatan, nipp, kelamin
                FROM guests
                WHERE user_face_id = ?";
        $query = $this->db->query($sql, [$user_face_id]);
        return $query->row_array(); // ambil satu row
    }
}
