<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaderboard extends CI_Controller{

  public function __construct()
  {
    parent::__construct();

  }

  public function index(){

      $data['rooms'] = [
          ['number' => '201', 'status' => 'occupied'],
          ['number' => '202', 'status' => 'vacant'],
          ['number' => '203', 'status' => 'occupied'],
          ['number' => '204', 'status' => 'vacant'],
          ['number' => '205', 'status' => 'occupied'],
          ['number' => '206', 'status' => 'vacant'],
      ];

      $data['title'] = 'Room Monitoring';
      $this->load->view('templates/header', $data);
      $this->load->view('templates/navbar');
      $this->load->view('leaderboard/leaderboard');
      $this->load->view('templates/footer');
  }

  public function customerService(){

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

}