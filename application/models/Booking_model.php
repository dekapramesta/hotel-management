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
        $kendaraan   = $data['kendaraan'];
        $nomorPolisi = $data['nomor_polisi'];
        $unitInduk   = $data['unit_induk'];
        $jabatan     = $data['jabatan'];
        $nipp        = $data['nipp'];
        $kelamin     = isset($data['kelamin']) ? $data['kelamin'] : null;
        $jenis_pemesanan     = isset($data['jenis_pemesanan']) ? $data['jenis_pemesanan'] : null;
        $status_kamar = null;

        $this->db->trans_start();

        if($jenis_pemesanan == 'reservation_baru'){
            ///cek apakah nipp sudah ada
            $guestCheck = $this->db->where("nipp", $nipp)->get("guests")->row();
            if($guestCheck){
                return ['status' => 'error', 'message' => 'NIPP sudah terdaftar, silahkan gunakan fitur check-in'];
            }
            $status_kamar = 'booked';
        }

        if($jenis_pemesanan == 'reservation_lama'){
            //ambil guest id dari nipp
            $guestData = $this->db->where("nipp", $nipp)->get("guests")->row();
            if($guestData){
                $guestId = $guestData->id;
            } else {
                return ['status' => 'error', 'message' => 'Data tamu tidak ditemukan, silahkan gunakan fitur reservasi baru'];
            }
            
            $status_kamar = 'booked';
        }
        
        if($jenis_pemesanan == 'checkin_lama'){
            if (!empty($userFaceId)) {
                $selectSql = "SELECT id FROM guests WHERE user_face_id = ? and nipp = ? LIMIT 1";
                $query = $this->db->query($selectSql, [$userFaceId, $nipp]);
                
                if ($query->num_rows() > 0) {
                    $guestId = $query->row()->id;
                }

            }else{
                return ['status' => 'error', 'message' => 'User Face ID tidak ditemukan'];
            }
            $status_kamar = 'occupied';

        }

         if($jenis_pemesanan == 'checkin_baru'){
            $status_kamar = 'occupied';
            $guestId = null; // reset guestId untuk pemesanan baru
        }


        

        // Jika guestId tidak ditemukan (user_face_id null atau tidak ditemukan), lakukan insert baru
        if (empty($guestId)) {
            $guestSql = "INSERT INTO guests 
                (nama, nik, telepon, email, alamat, foto_wajah, user_face_id, kendaraan, nomor_polisi, unit_induk, jabatan, nipp, kelamin) 
                VALUES 
                (?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, ?)";

            $this->db->query($guestSql, [$nama, $nik, $hp, $email, $alamat, $userFaceId, $kendaraan, $nomorPolisi, $unitInduk, $jabatan, $nipp, $kelamin]);
            $guestId = $this->db->insert_id();

            if (!$guestId) {
                $this->db->trans_rollback();
                return ['status' => 'error', 'message' => 'Gagal menyimpan tamu'];
            }
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
        $updateRoomSql = "UPDATE rooms SET status = '".$status_kamar."' WHERE id = ?";
        $this->db->query($updateRoomSql, [$room->id]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['status' => 'error', 'message' => 'Gagal menyimpan booking'];
        } else {
            return ['status' => 'success', 'message' => 'Booking berhasil disimpan'];
        }
    }


     public function check_active_booking($room_id, $guest_id)
    {
        $sql = "
            SELECT *
            FROM bookings b
            WHERE b.room_id = ?
              AND b.guest_id = ?
              AND NOW() BETWEEN b.check_in_date AND b.check_out_date
        ";

        $query = $this->db->query($sql, [$room_id, $guest_id]);
        return $query->num_rows() > 0;
    }

    public function getUserBooking($guest_id)
    {
        $sql = "
            SELECT 
                g.nama,
                g.nipp,
                g.jabatan,
                g.unit_induk,
                r.room_number,
                r.floor_name,
                b.check_in_date,
                b.check_out_date
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            JOIN guests g ON g.id = b.guest_id
            WHERE b.guest_id = ?
              AND NOW() BETWEEN b.check_in_date AND b.check_out_date
        ";

        return $this->db->query($sql, [$guest_id])->result();
    }
}
