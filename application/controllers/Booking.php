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
            'email'       => $this->input->post('email')
        ];

        $result = $this->book->simpanBooking($postData);

        echo json_encode($result);
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

    public function download_template()
    {
        // --- 1. Tentukan Path File Template ---
        // Ganti dengan path template Excel Anda yang sudah ada
        try {
            $template_file_path = FCPATH . 'assets/template.xlsx';

            if (!file_exists($template_file_path)) {
                throw new Exception('Template file not found at: ' . $template_file_path);
            }

            // --- 2. Load Spreadsheet dari File Template ---
            // Tentukan Reader (misalnya, Xlsx)
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
            $spreadsheet = $reader->load($template_file_path);

            $filename = 'Template_Booking_Batch_' . date('Ymd') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Opsional: Untuk IE 9 dan lebih lama
            // header('Cache-Control: max-age=1');

            // Opsional: Jika Anda menggunakan SSL/HTTPS
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            // --- 4. Tulis Spreadsheet ke Output ---
            // Tentukan Writer (misalnya, Xlsx)
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        } catch (\Exception $e) {
            $this->ErrorHandler(500, 'Error loading spreadsheet: ' . $e->getMessage());
        }
    }
}
