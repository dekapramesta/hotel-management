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
            $sort_kamar = [];
            $sort_ruangan = [];
            foreach ($array_insert as $key => $value) {
                $guest = $this->guest->getGuestByNipp($value['NIP']);
                if (count($guest) == 0) {
                    throw new Exception("Peserta dengan nama " . $value['Nama'] . " Belum Terdaftar");
                }

                $booked = [
                    "room_id" => $value['Kamar'],
                    "meet_id" => $value['Ruangan'],
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

                $this->room->setBooked($value['Kamar'], 'kamar');
                if ($this->db->trans_status() == FALSE) {
                    throw new Exception("[SET ROOM BOOKED]");
                }
                $this->room->setBooked($value['Ruangan'], 'ruangan');
                if ($this->db->trans_status() == FALSE) {
                    throw new Exception("[SET ROOM MEET BOOKED]");
                }

                $sort_kamar[] = $value['Kamar'];
                $sort_ruangan[] = $value['Ruangan'];
            }
            // sorting jumlah capacity
            $rle_kamar = $this->runLengthEncode($sort_kamar);
            $rle_ruangan = $this->runLengthEncode($sort_ruangan);

            // validasi sorting
            foreach ($rle_kamar as $key => $value) {
                $cek_capacity = $this->room->checkCapacity('kamar', $value[0]);
                if ($value[1] > $cek_capacity['CAPACITY']) {
                    throw new Exception("Kamar " . $cek_capacity["NAME"] . " Melebihi kapasitas");
                }
            }

            foreach ($rle_ruangan as $key => $value) {
                $cek_capacity = $this->room->checkCapacity('ruangan', $value[0]);
                if ($value[1] > $cek_capacity['CAPACITY']) {
                    throw new Exception("Ruangan " . $cek_capacity["NAME"] . " Melebihi kapasitas");
                }
            }
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

    public function getDataBatch()
    {
        $this->load->library('sheetdb');
        $data = $this->sheetdb->get_data();
        $key_mapping = [
            "Kode Pembelajaran"     => "kode_pembelajaran",
            "Judul Pembelajaran"    => "judul_pembelajaran",
            "Start Date"            => "start_date",
            "End Date"              => "end_date",
            "Durasi (hari)"         => "durasi",
            "NIP"                   => "nip",
            "Nama"                  => "nama",
            "Jabatan"               => "jabatan",
            "Unit Induk"            => "unit_induk",
            "Jenis Kelamin (L/P)"   => "jenis_kelamin",
        ];
        $newData = [];
        foreach ($data as $key => $value) {
            $row = [
                "no" => $key + 1,
            ];
            foreach ($key_mapping as $old_key => $new_key) {
                if (isset($value[$old_key])) {
                    $row[$new_key] = $value[$old_key];
                } else {
                    $row[$new_key] = null;
                }
            }
            $newData[] = $row;
        }
        echo json_encode($newData);
    }

    public function getFloor()
    {
        $data_room = $this->room->getRoomAvail("01/12/25", 0);
        $data_meet = $this->room->getMeetAvail("01/12/25", 0);
        $return = [
            "room" => $data_room,
            "meet" => $data_meet
        ];
        $this->ErrorHandler(200, $return, 'success');
    }

    function runLengthEncode(array $data)
    {
        if (empty($data)) {
            return [];
        }

        $encoded_data = [];
        $count = 0;
        $current_value = null;
        foreach ($data as $value) {
            if ($value === $current_value) {
                $count++;
            } else {
                if ($current_value !== null) {
                    $encoded_data[] = [$current_value, $count];
                }
                $current_value = $value;
                $count = 1;
            }
        }

        if ($current_value !== null) {
            $encoded_data[] = [$current_value, $count];
        }

        return $encoded_data;
    }
}
