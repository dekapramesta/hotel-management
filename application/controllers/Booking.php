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
            'email'       => $this->input->post('email')
        ];

        $this->load->model('Booking_model');
        $result = $this->Booking_model->simpanBooking($postData);

        echo json_encode($result);
    }
}
