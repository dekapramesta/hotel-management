<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        $this->load->model('Booking_model', 'book');
        $this->load->library('pagination');
    }

    public function index()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        
        $config['base_url'] = base_url('report/index');
        $config['total_rows'] = $this->book->count_log_booking_grouped($start_date, $end_date);
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['reuse_query_string'] = TRUE;

        // Bootstrap 5 Pagination Styling
        $config['full_tag_open'] = '<ul class="pagination pagination-sm justify-content-end mb-0">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        
        $data['title'] = 'Booking Report';
        $data['logs'] = $this->book->get_log_booking_grouped($config['per_page'], $page, $start_date, $end_date);
        $data['pagination'] = $this->pagination->create_links();
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar');
        $this->load->view('report/log_booking', $data);
        $this->load->view('templates/footer');
    }

    public function export()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $logs = $this->book->get_log_booking_grouped(NULL, NULL, $start_date, $end_date);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Booking_Report_" . date('Y-m-d') . ".xls");

        echo '
        <table border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Guest Name</th>
                    <th>Room No</th>
                    <th>Reserved At</th>
                    <th>Checked-in At</th>
                    <th>Checked-out At</th>
                    <th>Status</th>
                    <th>Last Performed By</th>
                </tr>
            </thead>
            <tbody>';
        
        $no = 1;
        foreach ($logs as $log) {
            echo '
                <tr>
                    <td>' . $no++ . '</td>
                    <td>' . ($log['guest_name'] ?: 'Unknown') . '</td>
                    <td>' . ($log['room_number'] ?: '-') . '</td>
                    <td>' . ($log['reserved_at'] ? date('d/m/Y H:i', strtotime($log['reserved_at'])) : '-') . '</td>
                    <td>' . ($log['checked_in_at'] ? date('d/m/Y H:i', strtotime($log['checked_in_at'])) : '-') . '</td>
                    <td>' . ($log['checked_out_at'] ? date('d/m/Y H:i', strtotime($log['checked_out_at'])) : '-') . '</td>
                    <td>' . ucfirst(str_replace('_', ' ', $log['current_status'])) . '</td>
                    <td>' . $log['last_activity_by'] . '</td>
                </tr>';
        }

        echo '
            </tbody>
        </table>';
    }
}
