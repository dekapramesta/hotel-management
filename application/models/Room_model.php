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
    select r.room_number, r.floor_id, r.status, r.id , 'ROOM' as tipe_room from rooms r
				union ALL
				select rm.room_number, rm.floor_id, rm.status, rm.id , 'MEET' as tipe_room from rooms_meet rm
  ) a WHERE a.floor_id = ? AND a.room_number IS NOT NULL ORDER BY a.room_number ASC
    ", [$floor_id])->result_array();
    }

    public function get_rooms_by_floor_and_date($floor_id, $start_date, $end_date)
    {
        $sql = "
            SELECT 
                rs.room_number, 
                rs.floor_id, 
                rs.id as room_id,
                rs.tipe_room,
                COALESCE(MAX(b.status), rs.physical_status) as status
            FROM (
                SELECT r.room_number, r.floor_id, r.status as physical_status, r.id, 'ROOM' as tipe_room FROM rooms r
                UNION ALL
                SELECT rm.room_number, rm.floor_id, rm.status as physical_status, rm.id, 'MEET' as tipe_room FROM rooms_meet rm
            ) rs
            LEFT JOIN bookings b ON b.room_id = rs.id AND b.room_type = rs.tipe_room 
                AND (b.check_in_date <= ? AND b.check_out_date >= ?)
                AND b.status != 'checked_out'
            WHERE rs.floor_id = ? AND rs.room_number IS NOT NULL
            GROUP BY rs.id, rs.tipe_room, rs.room_number, rs.floor_id, rs.physical_status
            ORDER BY rs.room_number ASC
        ";
        
        return $this->db->query($sql, [$end_date, $start_date, $floor_id])->result_array();
    }


    // Ambil kamar dengan filter
    public function get_rooms($search = '', $floor_number = '', $status = '', $date = '') {
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $sql = "SELECT 
                    rs.room_number, 
                    rs.floor_id, 
                    CASE 
                        WHEN b.status = 'booked' THEN 'booked'
                        WHEN b.status = 'checked_in' THEN 'occupied'
                        WHEN rs.physical_status = 'maintenance' THEN 'maintenance'
                        WHEN rs.physical_status = 'cleaning' THEN 'cleaning'
                        ELSE 'available'
                    END as status,
                    f.description, 
                    rs.id AS room_id,
                    rs.tipe_room
                FROM floors f
                JOIN (
                    SELECT 
                        r.room_number, 
                        r.floor_id, 
                        r.status as physical_status, 
                        r.id, 
                        'ROOM' AS tipe_room 
                    FROM rooms r
                    UNION ALL
                    SELECT 
                        rm.room_number, 
                        rm.floor_id, 
                        rm.status as physical_status, 
                        rm.id, 
                        'MEET' AS tipe_room 
                    FROM rooms_meet rm
                ) rs ON rs.floor_id = f.id
                LEFT JOIN bookings b ON b.room_id = rs.id AND b.room_type = rs.tipe_room 
                      AND (? BETWEEN b.check_in_date AND b.check_out_date)
                      AND b.status != 'checked_out'";
        
        $sql .= " WHERE rs.room_number IS NOT NULL";

        $params = [$date];

        if (!empty($search)) {
            $sql .= " AND rs.room_number LIKE ?";
            $params[] = "%$search%";
        }

        if (!empty($floor_number) && $floor_number != 'Pilih Lantai') {
            $sql .= " AND rs.floor_id = ?";
            $params[] = $floor_number;
        }

        if (!empty($status)) {
            $sql .= " HAVING status = ?";
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

    public function get_room_by_id_and_type($id, $type)
    {
        $table = ($type === 'MEET') ? 'rooms_meet' : 'rooms';
        return $this->db->get_where($table, ['id' => $id])->row_array();
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

     public function count_all_rooms($date = '')
    {
        if (empty($date)) $date = date('Y-m-d');
        
        $sql = "SELECT COUNT(*) as total FROM (
                    SELECT 
                        CASE 
                            WHEN b.status = 'booked' THEN 'booked'
                            WHEN b.status = 'checked_in' THEN 'occupied'
                            WHEN rs.physical_status = 'maintenance' THEN 'maintenance'
                            WHEN rs.physical_status = 'cleaning' THEN 'cleaning'
                            ELSE 'available'
                        END as calculated_status
                    FROM (
                        SELECT id, status as physical_status, 'ROOM' as tipe_room FROM rooms
                        UNION ALL
                        SELECT id, status as physical_status, 'MEET' as tipe_room FROM rooms_meet
                    ) rs
                    LEFT JOIN bookings b ON b.room_id = rs.id AND b.room_type = rs.tipe_room 
                        AND (? BETWEEN b.check_in_date AND b.check_out_date)
                        AND b.status != 'checked_out'
                ) as results WHERE calculated_status = 'available'";
        
        $result = $this->db->query($sql, [$date])->row_array();
        return $result['total'];
    }

    public function count_rooms_by_status($status, $date = '')
    {
        if (empty($date)) $date = date('Y-m-d');
        
        $sql = "SELECT COUNT(*) as total FROM (
                    SELECT 
                        CASE 
                            WHEN b.status = 'booked' THEN 'booked'
                            WHEN b.status = 'checked_in' THEN 'occupied'
                            WHEN rs.physical_status = 'maintenance' THEN 'maintenance'
                            WHEN rs.physical_status = 'cleaning' THEN 'cleaning'
                            ELSE 'available'
                        END as calculated_status
                    FROM (
                        SELECT id, status as physical_status, 'ROOM' as tipe_room FROM rooms
                        UNION ALL
                        SELECT id, status as physical_status, 'MEET' as tipe_room FROM rooms_meet
                    ) rs
                    LEFT JOIN bookings b ON b.room_id = rs.id AND b.room_type = rs.tipe_room 
                        AND (? BETWEEN b.check_in_date AND b.check_out_date)
                        AND b.status != 'checked_out'
                ) as results WHERE calculated_status = ?";
        
        $result = $this->db->query($sql, [$date, strtolower($status)])->row_array();
        return $result['total'];
    }

    /* =======================
       MEETING ROOM
       ======================= */

    public function count_meeting_rooms()
    {
        return $this->db
            ->count_all_results('rooms_meet');
    }

    public function count_meeting_rooms_by_status($status)
    {
        return $this->db
            ->where('LOWER(status)', strtolower($status))
            ->count_all_results('rooms_meet');
    }
}
