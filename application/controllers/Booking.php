<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Booking extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model', 'book');
        $this->load->model('Room_model', 'room');
        $this->load->model('Guest_model', 'guest');
        $this->load->helper('global');
    }

    private function ErrorHandler($code, $msg, $status = "error")
    {
        http_res_code($code);
        exit(json_encode(array("status" => $status, "msg" => $msg)));
    }

    public function simpan()
    {
        $postData = [
            'nomorKamar'  => $this->input->post('nomorKamar'),
            'lantaiKamar' => $this->input->post('lantaiKamar'),
            'tglCheckin'  => $this->input->post('tglCheckin'),
            'tglCheckout' => $this->input->post('tglCheckout'),
            'jenisTamu'   => $this->input->post('jenisTamu'),
            'user_face_id' => $this->input->post('user_face_id'),
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
        ];

        $result = $this->book->simpanBooking($postData);

        echo json_encode($result);
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


    public function Batch()
    {
        $data = '';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar');
        $this->load->view('book/index');
        $this->load->view('templates/footer');
    }

    public function impBatch()
    {
        try {
            $this->db->trans_begin();
            $file = $_FILES['excel_file'];
            $filename = $file['name'];

            $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx;
            $spreadsheet = $reader->load($file['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $sheet = [];
            foreach ($sheetData as $key => $row) {
                if ($key > 1) {
                    $sheet[] = [
                        "no" => $key - 1,
                        "kode_pembelajaran" => $row["A"],
                        "judul_pembelajaran" => $row["B"],
                        "start_date" => $row["C"],
                        "end_date" => $row["D"],
                        "durasi" => $row["E"],
                        "nip" => $row["F"],
                        "nama" => $row["G"],
                        "jabatan" => $row["H"],
                        "unit_induk" => $row["I"],
                        "jenis_kelamin" => $row["J"]
                    ];
                }
            }
            $this->db->trans_commit();
            $data = [
                "status" => "Berhasil Mengupload Excel",
                "data" => $sheet
            ];
            $this->ErrorHandler(200, $data, 'success');
        } catch (\Throwable $th) {
            $data = [
                "status" => "Terjadi Kesalahan " . $th->getMessage(),
                "data" => []
            ];
            $this->db->trans_rollback();
            $this->ErrorHandler(500, $data);
        }
    }

    public function addBatchBook()
    {
        $array_insert = $this->input->post('data');
        try {
            $this->db->trans_begin();
            $counter = 0;
            $rooms = $this->room->getOneRoomAvail($array_insert[0]['Start_Date']);
            if (count($rooms) == 0) {
                throw new Exception("Kamar Tidak Tersedia / Kamar Melebihi batas Peserta");
            }

            $last_id_rooms = $rooms[0]['id'];
            foreach ($array_insert as $key => $value) {
                if ($counter >= $rooms[0]['capacity']) {
                    $this->room->setBooked($rooms[0]['id']);
                    $counter = 0;
                    $rooms = $this->room->getOneRoomAvail($value['Start_Date']);
                    if (count($rooms) == 0) {
                        throw new Exception("Kamar Tidak Tersedia / Kamar Melebihi batas Peserta");
                    }
                    $last_id_rooms = $rooms[0]['id'];
                } else {
                    $counter++;
                    $last_id_rooms = $rooms[0]['id'];
                }
                $rooms_id = $rooms[0]['id'];

                $guest = $this->guest->getGuestByNipp($value['NIP']);
                if (count($guest) == 0) {
                    throw new Exception("Peserta dengan nama " . $value['Nama'] . " Belum Terdaftar");
                }


                $booked = [
                    "room_id" => $rooms_id,
                    "guest_id" => $guest[0]['id'],
                    "check_in_date" => $value['Start_Date'],
                    "check_out_date" => $value['End_Date'],
                    "status" => "booked",
                    "judul" => $value['Judul_Pembelajaran']
                ];
                $this->book->insert_batch($booked);
                if ($this->db->trans_status() == FALSE) {
                    throw new Exception("[ADD BATCH]");
                }
            }
            $this->room->setBooked($last_id_rooms);
            $this->db->trans_commit();
            $this->ErrorHandler(200, 'Berhasil Melakukan Batch Bookings', 'success');
        } catch (\Throwable $th) {
            $this->db->trans_rollback();
            $this->ErrorHandler(500, 'Terjadi Kesalahan ' . $th->getMessage());
        }
    }
}
