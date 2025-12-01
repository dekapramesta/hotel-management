<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Room_model extends CI_Model
{

    // Ambil semua lantai
    public function get_floors()
    {
        $sql = "SELECT floor_number, description FROM floors ORDER BY floor_number ASC";
        return $this->db->query($sql)->result_array();
    }

    public function get_all_rooms()
    {
        return $this->db->query("SELECT room_number FROM rooms ORDER BY room_number ASC")
            ->result_array();
    }

    public function get_all_floors()
    {
        return $this->db->query("SELECT floor_number, description 
                                 FROM floors ORDER BY floor_number ASC")
            ->result_array();
    }


    public function get_rooms_by_floor($floor_id)
    {
        return $this->db->query("
        SELECT room_number 
        FROM rooms 
        WHERE floor_id = ?
        ORDER BY room_number ASC
    ", [$floor_id])->result_array();
    }


    // Ambil kamar dengan filter
    public function get_rooms($search = '', $floor_number = '', $status = '')
    {
        $sql = "SELECT r.room_number, r.floor_id, r.status, f.description, r.id as room_id
                FROM rooms r
                JOIN floors f ON r.floor_id = f.floor_number
                WHERE 1=1";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND r.room_number LIKE ?";
            $params[] = "%$search%";
        }

        if (!empty($floor_number) && $floor_number != 'Pilih Lantai') {
            $sql .= " AND r.floor_id = ?";
            $params[] = $floor_number;
        }

        if (!empty($status)) {
            $sql .= " AND r.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY r.floor_id ASC, r.room_number ASC";

        return $this->db->query($sql, $params)->result_array();
    }


    public function set_occupied($room_id)
    {
        return $this->db
            ->where("id", $room_id)
            ->update("rooms", [
                "status" => "occupied"
            ]);
    }

    public function setBooked($room_id, $type)
    {
        if ($type == "kamar") {
            $table = "rooms";
        } else {
            $table = "rooms_meet";
        }
        $sql = "UPDATE $table SET STATUS = 'booked' where id = '$room_id'";
        return $this->db->query($sql);
    }

    public function getRoomAvail($check_out_date, $limit = 0)
    {
        $sql = "select * from rooms where id not in (select room_id from bookings where check_out_date >= date_format('$check_out_date', '%d/%m/%y')) and status not in ('booked', 'occupied') order by id asc";
        if ($limit > 0) {
            $sql .= " limit 1";
        }
        return $this->db->query($sql)->result_array();
    }

    public function getMeetAvail($check_out_date, $limit = 0)
    {
        $sql = "select * from rooms_meet where id not in (select meet_id from bookings where check_out_date >= date_format('$check_out_date', '%d/%m/%y')) and status not in ('booked', 'occupied') order by id asc";
        if ($limit > 0) {
            $sql .= " limit 1";
        }
        return $this->db->query($sql)->result_array();
    }

    public function checkCapacity($type, $id)
    {
        if ($type == 'kamar') {
            $table = " rooms";
            $select = " room_number as NAME";
        } else {
            $select = " room_name as NAME";
            $table = " rooms_meet";
        }
        $sql = "SELECT CAPACITY, $select FROM $table WHERE ID = '$id'";
        return $this->db->query($sql)->row_array();
    }
}
