<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Login extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user');
    }

    public function index_post()
    {
        $email = $this->post('email');
        $password = $this->post('password');

        // echo $email;
        // die;
        $user = $this->db->query("SELECT * FROM `user` WHERE email='$email'")->row_array();

        if ($user) {
            // jika usernya aktif
            if ($user['is_active'] == 1) {
                // cek password
                if (password_verify($password, $user['password'])) {
                    $this->response([
                        'status' => true,
                        'messages' => "Successfull Login!",
                        'data' => $user,
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'messages' => "Password is Wrong",
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $this->response([
                    'status' => false,
                    'messages' => "User Not Activated",
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                'status' => false,
                'messages' => "User Not Found",
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
