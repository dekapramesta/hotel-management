<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking extends CI_Controller {

    public function simpan()
    {
        $postData = [
            'nomorKamar'  => $this->input->post('nomorKamar'),
            'lantaiKamar' => $this->input->post('lantaiKamar'),
            'tglCheckin'  => $this->input->post('tglCheckin'),
            'tglCheckout' => $this->input->post('tglCheckout'),
            'jenisTamu'   => $this->input->post('jenisTamu'),
            'user_face_id'=> $this->input->post('user_face_id'),
            'nama'        => $this->input->post('nama'),
            'hp'          => $this->input->post('hp'),
            'nik'         => $this->input->post('nik'),
            'alamat'      => $this->input->post('alamat'),
            'email'       => $this->input->post('email'),
            'kendaraan'   => $this->input->post('kendaraan'),
            'nomor_polisi'=> $this->input->post('nomor_polisi'),
            'unit_induk'  => $this->input->post('unit_induk'),
            'jabatan'     => $this->input->post('jabatan'),
            'nipp'        => $this->input->post('nipp'),
            'kelamin'        => $this->input->post('kelamin'),
            'jenis_pemesanan' => $this->input->post('flag_tamu')
        ];

        $this->load->model('Booking_model');
        $result = $this->Booking_model->simpanBooking($postData);

        echo json_encode($result);
    }

     public function search_bookings() {
        $search = $this->input->get('search', TRUE);
        
        $this->load->model('booking_model');
        $results = $this->booking_model->search_bookings($search);
        
        $data = [
            'status' => 'success',
            'count' => count($results),
            'data' => $results
        ];
        
        echo json_encode($data);
    }

    public function check_active_booking($room_id, $guest_id)
    {
        return $this->db
            ->where("room_id", $room_id)
            ->where("guest_id", $guest_id)
            ->where("NOW() BETWEEN check_in_date AND check_out_date", null, false)
            ->get("bookings")
            ->row();
    }

    public function checkin()
    {
        $room_id  = $this->input->post("room_id");
        $user_id = $this->input->post("user_id");

        $this->load->model("Booking_model");
        $this->load->model("Room_model");

        // cek apakah booking valid
        $booking = $this->Booking_model->check_active_booking($room_id, $user_id);

        if (!$booking) {
            echo json_encode([
                "success" => false,
                "message" => "Booking tidak ditemukan atau tidak valid."
            ]);
            return;
        }

        // update status kamar menjadi occupied
        $this->Room_model->set_occupied($room_id);

        echo json_encode([
            "success" => true,
            "message" => "Check-in berhasil! Kamar sudah menjadi occupied."
        ]);
    }

    public function check_guest_face()
    {
        $room_id = $this->input->get('room_id');

        if (!$room_id) {
            echo json_encode([
                "success" => false,
                "message" => "room_id wajib diisi"
            ]);
            return;
        }

        // 1. Cari booking aktif berdasarkan room_id
        $booking = $this->db->query("
            SELECT guest_id 
            FROM bookings 
            WHERE room_id = ?
            AND NOW() BETWEEN check_in_date AND check_out_date
            LIMIT 1
        ", [$room_id])->row();

        if (!$booking) {
            echo json_encode([
                "success" => false,
                "message" => "Tidak ada tamu yang sedang menginap di kamar ini"
            ]);
            return;
        }

        // 2. Cari user_face_id berdasarkan guest_id
        $guest = $this->db->query("
        SELECT id, user_face_id, nama, kendaraan, nomor_polisi, unit_induk, jabatan, nipp, kelamin, nik, telepon AS hp, alamat, email
        FROM guests
        WHERE id = ?
        LIMIT 1
    ", [$booking->guest_id])->row();

    if (!$guest) {
        echo json_encode([
            "success" => false,
            "message" => "Data tamu tidak ditemukan"
        ]);
        return;
    }

    echo json_encode([
        "success" => true,
        "data" => [
            "guest_id" => $guest->id,
            "user_face_id" => $guest->user_face_id,
            "guest_name" => $guest->nama,
            "nama" => $guest->nama,
            "kendaraan" => $guest->kendaraan,
            "nomor_polisi" => $guest->nomor_polisi,
            "unit_induk" => $guest->unit_induk,
            "jabatan" => $guest->jabatan,
            "nipp" => $guest->nipp,
            "kelamin" => $guest->kelamin,
            "nik" => $guest->nik,
            "hp" => $guest->hp,
            "alamat" => $guest->alamat,
            "email" => $guest->email
        ]
    ]);
    }

 
    public function get_by_user($guest_id) {

        $this->load->model('Booking_model');
        $data = $this->Booking_model->getUserBooking($guest_id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }


}
