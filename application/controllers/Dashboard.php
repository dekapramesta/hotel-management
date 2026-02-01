<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller{

  public function __construct()
  {
    parent::__construct();



    if (!$this->session->userdata('logged_in')) {
        redirect('login'); // Jika belum login, redirect ke login
    }

   if ($this->session->userdata('role') !== 'admin') {
      // Jika bukan admin, tampilkan error / redirect
      show_error('Anda tidak memiliki akses ke halaman ini.', 403);
      // atau redirect('dashboard/user'); // bisa juga ke halaman user biasa
    }
    $this->load->model('Room_model');

    $this->load->model('Transaksi_model');


  }

  public function index(){

      $data['rooms'] = $this->Transaksi_model->get_rooms("","","");
      $data['title'] = 'Room Monitoring';
      $data['floors'] = $this->Transaksi_model->get_floor();
      $data['rooms']  = $this->Room_model->get_all_rooms();
      $today = date('Y-m-d');
      $data['jumlah_kamar'] = $this->Room_model->count_all_rooms($today);
      $data['kamar_terisi'] = $this->Room_model->count_rooms_by_status('occupied', $today);
      $data['kamar_cleaning'] = $this->Room_model->count_rooms_by_status('cleaning', $today);
      $data['kamar_maintenance'] = $this->Room_model->count_rooms_by_status('maintenance', $today);
      $data['kamar_booked'] = $this->Room_model->count_rooms_by_status('booked', $today);
      
      // $data['floors'] = $this->Room_model->get_all_floors();
      $this->load->view('templates/header', $data);
      $this->load->view('templates/navbar');
      $this->load->view('dashboard/dashboard');
      $this->load->view('templates/footer');
  }

public function get_rooms_by_floor()
{
    $floor_id = $this->input->post('floor_id');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    if ($start_date && $end_date) {
        $rooms = $this->Room_model->get_rooms_by_floor_and_date($floor_id, $start_date, $end_date);
    } else {
        $rooms = $this->Room_model->get_rooms_by_floor($floor_id);
    }

    echo json_encode([
        'success' => true,
        'rooms' => $rooms
    ]);
}

public function verify_booking()
{
    $room_id = $this->input->post('room_id');
    $user_id = $this->input->post('user_id');

    $this->load->model('Booking_model');

    $isValid = $this->Booking_model->check_active_booking($room_id, $user_id);

    echo json_encode([
        "valid" => $isValid,
        "message" => $isValid ? "Booking valid" : "Booking tidak ditemukan"
    ]);
}



    public function filter() {
        $search = $this->input->post('search');
        $floor  = $this->input->post('floor');
        $status = $this->input->post('status');
        $date   = $this->input->post('date');

        $rooms = $this->Room_model->get_rooms($search, $floor, $status, $date);

        echo json_encode([
            'success' => true,
            'rooms' => $rooms,
            'counts' => [
                'available' => $this->Room_model->count_all_rooms($date),
                'occupied' => $this->Room_model->count_rooms_by_status('occupied', $date),
                'cleaning' => $this->Room_model->count_rooms_by_status('cleaning', $date),
                'maintenance' => $this->Room_model->count_rooms_by_status('maintenance', $date),
                'booked' => $this->Room_model->count_rooms_by_status('booked', $date)
            ]
        ]);
    }

  public function identify(){

      $data['title'] = 'Room Monitoring';
      $this->load->view('templates/header', $data);
      $this->load->view('templates/navbar');
      $this->load->view('face/identify');
      $this->load->view('templates/footer');
  }

  public function scan_face(){

    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    $base64 = $data['image'];

    $payload = json_encode([
       'imageBase64' => $base64
    ]);


    $ch = curl_init("http://localhost:5000/identify-camera");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    echo $result;
  }


  public function room_detail($room_id, $room_type = 'ROOM'){
    $this->load->model('Booking_model');
    $date = $this->input->get('date') ?: date('Y-m-d');
    
    $booking = $this->Booking_model->get_active_booking_by_room($room_id, $room_type, $date);

    if ($booking) {
        echo json_encode([
            'status' => 'success',
            'data' => $booking
        ]);
    } else {
        // Jika tidak ada booking active, ambil detail kamar dasar
        $room = $this->Room_model->get_room_by_id_and_type($room_id, $room_type);
        echo json_encode([
            'status' => 'empty',
            'data' => $room,
            'message' => 'Tidak ada booking aktif untuk ruangan ini pada tanggal tersebut.'
        ]);
    }
  }

  public function get_guest($nipp){

    $this->load->model('Guest_model');
    $guest = $this->Guest_model->get_guest_by_nipp($nipp);

    if ($guest) {
        echo json_encode([
            'status' => 'success',
            'data' => $guest
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tamu tidak ditemukan'
        ]);
    }
  }
  public function get_all_guest(){

    $this->load->model('Guest_model');
    $guest = $this->Guest_model->get_guest();

    if ($guest) {
        echo json_encode([
            'status' => 'success',
            'data' => $guest
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tamu tidak ditemukan'
        ]);
    }
  }

}