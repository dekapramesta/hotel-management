<?
defined('BASEPATH') or exit('No direct script access allowed');

class Cleaning extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model', 'book');
        $this->load->model('Room_model', 'room');
        $this->load->model('Guest_model', 'guest');
        $this->load->model('Cleaning_model', 'clean');
        $this->load->helper('global');
    }

    private function ErrorHandler($code, $msg, $status = "error")
    {
        http_res_code($code);
        exit(json_encode(array("status" => $status, "msg" => $msg)));
    }

    public function index()
    {
        $data['rooms'] = [
            ['number' => '201', 'status' => 'occupied'],
            ['number' => '202', 'status' => 'vacant'],
            ['number' => '203', 'status' => 'occupied'],
            ['number' => '204', 'status' => 'vacant'],
            ['number' => '205', 'status' => 'occupied'],
            ['number' => '206', 'status' => 'vacant'],
        ];

        $data['title'] = 'Room Monitoring';
        $this->load->view('leaderboard/customerService');
    }

    public function getFloor()
    {
        $id = $this->input->post('id');
        $data = $this->clean->getFloorCleanedByIDCleaner($id);
        echo json_encode($data);
    }

    public function getRoomsByFloor()
    {
    }
}
