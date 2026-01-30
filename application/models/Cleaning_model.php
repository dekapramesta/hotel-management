<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cleaning_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getFloorCleaner($id_cleaner){
        $sql = "select distinct lantai from cleaning_user where cleaner_id = '$id_cleaner'";
        return $this->db->query($sql)->result_array();
    }

    public function getFloorCleanedByIDCleaner($id_cleaner, $floor)
    {
        $sql = "select distinct lantai from cleaning_user where cleaner_id = '$id_cleaner'";
        return $this->db->query($sql)->result_array();
    }
}
