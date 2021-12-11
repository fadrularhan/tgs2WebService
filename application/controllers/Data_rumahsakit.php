<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require "vendor\autoload.php";

use Restserver\Libraries\REST_Controller;
use \Firebase\JWT\JWT;

class Data_rumahsakit extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
    }

    //Menampilkan data RUMAHSAKIT
    public function index_get() {
        $authHeader = $this->input->get_request_header('Authorization');
        $arr = explode(" ", $authHeader);
        $jwt = isset($arr[1])? $arr[1] : "";
        $secretkey = base64_encode("gampang");

        if($jwt){
            try {
                $decode = JWT::decode($jwt, $secretkey, array('HS256'));
                $id = $this->get('id');
        if ($id == '') {
            $data = $this->db->get('rumahsakit')->result();
        } else {
            $this->db->where('id', $id);
            $data = $this->db->get('rumahsakit')->result();
        }
        $result =["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                  "code"=>200,
                  "message"=>"Response successfully",
                  "data"=>$data];
        $this->response($result, 200);
            }catch (Exception $e){

                $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                        "code"=>401,
                        "message"=>"Access denied",
                        "data"=>null];
                $this->response($result, 401);
            }
        }else{
            $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                        "code"=>401,
                        "message"=>"Access denied",
                        "data"=>null];
                $this->response($result, 401);
        }
    }

   // menambah data (post)
    public function index_post() {
        $data = array(
                'tahun'    => $this->post('tahun'),
                'namaRS'   => $this->post('namaRS'),
                'kelas'  => $this->post('kelas'),
                'kabKota'  => $this->post('kabKota'),
                'tglMulai'   => $this->post('tglMulai'),
                'tglMasaBerlaku'  => $this->post('tglMasaBerlaku'),
                'status'    => $this->post('status'),
                'tglUpdate'    => $this->post('tglUpdate'));
        $insert = $this->db->insert('rumahsakit', $data);
        if ($insert) {
            $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                "code"=>201,
                "message"=>"Data has successfully added",
                "data"=>$data];
            $this->response($result, 201);
        }else {
            $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                "code"=>502,
                "message"=>"Failed adding data",
                "data"=>null];
        $this->response($result, 502);
        }
    }
    public function index_put() {
        $id = $this->put('id');
        $data = array(
                'id'    => $this->put('id'),
                'tahun'    => $this->put('tahun'),
                'namaRS'   => $this->put('namaRS'),
                'kelas'  => $this->put('kelas'),
                'kabKota'  => $this->put('kabKota'),
                'tglMulai'   => $this->put('tglMulai'),
                'tglMasaBerlaku'  => $this->put('tglMasaBerlaku'),
                'status'    => $this->put('status'),
                'tglUpdate'    => $this->put('tglUpdate'));
        $this->db->where('id', $id);
        $update = $this->db->update('rumahsakit', $data);
        if ($update) {
            $this->response($data, 200);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }
    
//Menghapus Data
    public function index_delete() {
        $id = $this->delete('id');
        $this->db->where('id', $id);
        $delete = $this->db->delete('rumahsakit');
        if ($delete) {
            $this->response(array('status' => 'success'), 201);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }
}
?>