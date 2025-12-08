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
       select a.status, a.tipe_room, a.id, a.room_number, a.floor_id from ( 
    select r.room_number, r.floor_id, r.status, r.id , 'ROOM' as TIPE_ROOM from rooms r
				union ALL
				select rm.room_number, rm.floor_id, rm.status, rm.id , 'MEET' as TIPE_ROOM from rooms_meet rm
  ) a WHERE a.floor_id = ? ORDER BY a.room_number ASC
    ", [$floor_id])->result_array();
    }


    // Ambil kamar dengan filter
    public function get_rooms($search = '', $floor_number = '', $status = '') {
        $sql = "SELECT 
                    rs.room_number, 
                    rs.floor_id, 
                    rs.status, 
                    f.description, 
                    rs.id AS room_id,
                    rs.TIPE_ROOM
                FROM floors f
                LEFT JOIN (
                    SELECT 
                        r.room_number, 
                        r.floor_id, 
                        r.status, 
                        r.id, 
                        'ROOM' AS TIPE_ROOM 
                    FROM rooms r
                    UNION ALL
                    SELECT 
                        rm.room_number, 
                        rm.floor_id, 
                        rm.status, 
                        rm.id, 
                        'MEET' AS TIPE_ROOM 
                    FROM rooms_meet rm
                ) rs ON CAST(rs.floor_id AS CHAR) = CAST(f.id AS CHAR)
                WHERE 1=1";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND rs.room_number LIKE ?";
            $params[] = "%$search%";
        }

        if (!empty($floor_number) && $floor_number != 'Pilih Lantai') {
            $sql .= " AND rs.floor_id = ?";
            $params[] = $floor_number;
        }

        if (!empty($status)) {
            $sql .= " AND rs.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY rs.floor_id ASC, rs.room_number ASC";

        return $this->db->query($sql, $params)->result_array();
    }


    public function set_occupied($room_id, $room_type)
    {
        if ($room_type === 'MEET') {
            return $this->db
                ->where("id", $room_id)
                ->update("rooms_meet", [
                    "status" => "occupied"
                ]);
        }else{
            return $this->db
                ->where("id", $room_id)
                ->update("rooms", [
                    "status" => "occupied"
            ]);
        }
    
    }

    // public function setBooked($room_id, $type)
    // {
    //     if ($type == "kamar") {
    //         $table = "rooms";
    //     } else {
    //         $table = "rooms_meet";
    //     }
    //     $sql = "UPDATE $table SET STATUS = 'booked' where id = '$room_id'";
    //     return $this->db->query($sql);
    // }

    // public function getRoomAvail($check_out_date, $limit = 0)
    // {
    //     $sql = "select * from rooms where id not in (select room_id from bookings where check_out_date >= date_format('$check_out_date', '%d/%m/%y')) and status not in ('booked', 'occupied') order by id asc";
    //     if ($limit > 0) {
    //         $sql .= " limit 1";
    //     }
    //     return $this->db->query($sql)->result_array();
    // }

    // public function getMeetAvail($check_out_date, $limit = 0)
    // {
    //     $sql = "select * from rooms_meet where id not in (select meet_id from bookings where check_out_date >= date_format('$check_out_date', '%d/%m/%y')) and status not in ('booked', 'occupied') order by id asc";
    //     if ($limit > 0) {
    //         $sql .= " limit 1";
    //     }
    //     return $this->db->query($sql)->result_array();
    // }

    // public function checkCapacity($type, $id)
    // {
    //     if ($type == 'kamar') {
    //         $table = " rooms";
    //         $select = " room_number as NAME";
    //     } else {
    //         $select = " room_name as NAME";
    //         $table = " rooms_meet";
    //     }
    //     $sql = "SELECT CAPACITY, $select FROM $table WHERE ID = '$id'";
    //     return $this->db->query($sql)->row_array();
    // }

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
