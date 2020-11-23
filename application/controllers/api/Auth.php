<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Auth extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user');
    }

    public function index_get()
    {
        $username = $this->get('username');
        $password = $this->get('password');

        if ($username === null && $password === null) {
            $user = $this->user->getUser();
        } else {
            $user = $this->user->getUser($username, $password);
        }

        if ($user) {
            $this->response([
                'status' => true,
                'data' => $user,
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'messages' => "Data Tidak Di Temukan",
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_delete()
    {
        $nik = $this->delete('nik');
        if ($nik === null) {
            $this->response([
                'status' => false,
                'messages' => "Pilih data terlebih dahulu",
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($this->user->delUser($nik) > 0) {
                $this->response([
                    'status' => true,
                    'messages' => "Data Berhasil Di Hapus",
                ], REST_Controller::HTTP_ACCEPTED);
            } else {
                $this->response([
                    'status' => false,
                    'messages' => "Data Gagal di Hapus",
                ], REST_Controller::HTTP_CONFLICT);
            }
        }
    }

    public function index_put()
    {
        $old_nik = $this->put('old_nik');
        $data = [
            'nik' => $this->post('nik'),
            'email' => $this->post('email'),
            'username' => $this->post('username'),
            'password' => $this->post('password'),
        ];

        if ($this->user->updateUser($data, $old_nik) > 0) {
            $this->response([
                'status' => true,
                'messages' => "User Has Been Updated",
            ], REST_Controller::HTTP_CREATED);
        } else {
            $this->response([
                'status' => false,
                'messages' => "Failed create user",
            ], REST_Controller::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function index_post()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'This email has already registered!'
        ]);
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password dont match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'WPU User Registration';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email', true);
            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($email),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 0,
                'date_created' => time()
            ];

            // siapkan token
            $token = base64_encode(random_bytes(32));
            $user_token = [
                'email' => $email,
                'token' => $token,
                'date_created' => time()
            ];

            $this->db->insert('user', $data);
            $this->db->insert('user_token', $user_token);

            $send = $this->_sendEmail($token, 'verify');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Congratulation! your account has been created. Please activate your account</div>');
            if ($send) {
                $this->response([
                    'status' => false,
                    'messages' => "verify your email account",
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'messages' => "can't not send verify code",
                ], REST_Controller::HTTP_NO_CONTENT);
            }
        }
    }

    private function _sendEmail($token, $type)
    {
        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'emailtesting364@gmail.com',
            'smtp_pass' => 'emailtesting20',
            'smtp_port' => 465,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->email->initialize($config);

        $this->email->from('emailtesting20@gmail.com', 'Aktifkan Akun Anda Sekarang');
        $this->email->to($this->input->post('email'));

        if ($type == 'verify') {
            $this->email->subject('Account Verification');
            $this->email->message('Click this link to verify you account : <a href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Activate</a>');
        } else if ($type == 'forgot') {
            $this->email->subject('Reset Password');
            $this->email->message('Click this link to reset your password : <a href="' . base_url() . 'auth/resetpassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Reset Password</a>');
        }

        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }
}
