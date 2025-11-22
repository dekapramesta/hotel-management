<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {

    public function simpanBooking($data)
    {
        $nomorKamar  = $data['nomorKamar'];
        $lantaiKamar = $data['lantaiKamar'];
        $tglCheckin  = $data['tglCheckin'];
        $tglCheckout = $data['tglCheckout'];
        $jenisTamu   = $data['jenisTamu'];
        $userFaceId  = $data['user_face_id'];
        $nama        = $data['nama'];
        $hp          = $data['hp'];
        $nik         = $data['nik'];
        $alamat      = $data['alamat'];
        $email       = $data['email'];

        $this->db->trans_start();

        // 1. Insert tamu
        $guestSql = "INSERT INTO guests 
            (nama, nik, telepon, email, alamat, foto_wajah, user_face_id) 
            VALUES 
            (?, ?, ?, ?, ?, NULL, ?)";

        $this->db->query($guestSql, [$nama, $nik, $hp, $email, $alamat, $userFaceId]);
        $guestId = $this->db->insert_id();

        if (!$guestId) {
            $this->db->trans_rollback();
            return ['status' => 'error', 'message' => 'Gagal menyimpan tamu'];
        }

        // 2. Cari room by nomor & lantai
        $roomSql = "SELECT id, status FROM rooms WHERE room_number = ? AND floor_id = ? LIMIT 1";
        $room = $this->db->query($roomSql, [$nomorKamar, $lantaiKamar])->row();

        if (!$room) {
            $this->db->trans_rollback();
            return ['status' => 'error', 'message' => 'Kamar tidak ditemukan'];
        }

        if ($room->status != 'available') {
            $this->db->trans_rollback();
            return ['status' => 'error', 'message' => 'Kamar sudah tidak tersedia'];
        }

        // 3. Insert booking
        $bookingSql = "INSERT INTO bookings 
            (guest_id, room_id, check_in_date, check_out_date, status) 
            VALUES (?, ?, ?, ?, 'booked')";

        $this->db->query($bookingSql, [$guestId, $room->id, $tglCheckin, $tglCheckout]);

        // 4. Update status room
        $updateRoomSql = "UPDATE rooms SET status = 'booked' WHERE id = ?";
        $this->db->query($updateRoomSql, [$room->id]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['status' => 'error', 'message' => 'Gagal menyimpan booking'];
        } else {
            return ['status' => 'success', 'message' => 'Booking berhasil disimpan'];
        }
    }
}
