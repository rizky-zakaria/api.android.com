<?php

class User_model extends CI_Model
{

    public function getUser($username = null, $password = null)
    {
        if ($username === null && $password === null) {
            $query = "SELECT * FROM user";
            return $this->db->query($query)->result_array();
        } else {
            return $this->db->get_where('user', ['username' => $username, 'password' => MD5($password)])->result_array();
        }
    }

    public function delUser($id_user)
    {
        $this->db->delete('tb_user', ['id_user' => $id_user]);
        return $this->db->affected_rows();
    }

    public function createUser($data)
    {
        $data = array(
            'name' => 'My title',
            'email'  => 'My Name',
            'image'  => 'My date'
        );

        $sql = $this->db->set($data)->get_compiled_insert('mytable');
        $this->db->insert('user', $data);
        return $this->db->affected_rows();
    }

    public function cekUser($username, $password)
    {
        // return $result = $this->db->get_where('tb_user', ['username' => $username, 'password' => MD5($password)])->result_array();
        $result = $this->db->query("SELECT * FROM tb_user WHERE username='$username' AND password = MD5('$password') ")->result_array();
        return $result;
    }

    public function updateUser($data, $old_nik)
    {
        $this->db->update('tb_user', $data, ['nik' => $old_nik]);
        return $this->db->affected_rows();
    }

    public function cek($id_user)
    {
        // return $result = $this->db->get_where('tb_user', ['username' => $username, 'password' => MD5($password)])->result_array();
        $result = $this->db->query("SELECT * FROM tb_pinjam WHERE id_user = '$id_user' ")->result_array();
        return $result;
    }
}
