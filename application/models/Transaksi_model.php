<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi_model extends CI_Model {


    public function get_floor(){

    	$sql = "SELECT * FROM FLOORS";

    	return $this->db->query($sql)->result_array();
    }

    public function get_rooms($search, $floor_id, $status){
    	
    	$sql = "SELECT * FROM rooms WHERE 1=1";

	    if ($search) {
	        $sql .= " AND room_number LIKE '%$search%'";
	    }

	    if ($floor_id) {
	        $sql .= " AND floor_id = '$floor_id'";
	    }

	    if ($status) {
	        $sql .= " AND status = '$status'";
	    }

	    $sql .= " ORDER BY floor_id ASC, room_number ASC";

	    return $this->db->query($sql)->result_array();

    }
}
