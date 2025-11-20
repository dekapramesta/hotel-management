<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller{

  public function __construct()
  {
    parent::__construct();

  }

  public function index(){

     if ($this->session->userdata('logged_in')) {
          redirect('dashboard');
      }

      $this->load->view('login/index');
  }

  public function login_process()
  {
        header('Content-Type: application/json');

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // Load model user
        $this->load->model('User_model');
        $user = $this->User_model->get_user($username);

        if($user && (md5($password) == $user->password)) {
            // Set session
            $this->session->set_userdata('user_id', $user->id);
            $this->session->set_userdata('user_name', $user->nama_lengkap);
            $this->session->set_userdata('role', $user->role);
            $this->session->set_userdata('logged_in', true);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Username atau password salah']);
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }

}