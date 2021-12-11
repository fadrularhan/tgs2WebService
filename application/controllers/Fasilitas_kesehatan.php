<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Fasilitas_kesehatan extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->driver('cache', array('adapter' => 'apc','backup' => 'file'));
    }

    //menampilkan data
    public function index_get() {

        $id = $this->get('id');
        $fasilitas_kesehatan=[];
        if ($id == '') {
            $data = $this->db->get('fasilitas_Kesehatan')->result();
            foreach ($data as $row=>$key):
                $fasilitas_kesehatan[]=["id"=>$key->id,
                            "tahun"=>$key->tahun,
                            "wilayah"=>$key->wilayah,
                            "fasilitas"=>$key->fasilitas,
                            "jumlah"=>$key->jumlah,
                            "_links"=>[(object)["href"=>"rumahsakit/{$key->id}",
                                            "rel"=>"rumahsakit",
                                            "type"=>"GET"]]
                            ];
            endforeach;
                $result = [ "took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                            "code"=>200,
					        "message"=>"Response successfully",
					        "data"=>$fasilitas_kesehatan
                    ];
                $this->response($result, 200);
            } else {
			$this->db->where('id', $id);
			$data = $this->db->get('fasilitas_kesehatan')->result();
			$fasilitas_kesehatan=[ "id" => $data[0]->id,
                         "tahun"=>$data[0]->tahun,
						 "wilayah"=>$data[0]->wilayah,
						 "fasilitas"=>$data[0]->fasilitas,
						 "jumlah"=>$data[0]->jumlah,
							        "_links"=>[(object)["href"=>"rumahsakit/{$data[0]->id}",
										"rel"=>"rumahsakit",
										"type"=>"GET"]]
					];   
            $etag = hash('sha256', $data[0]->LastUpdate);
            $this->cache->save($etag, $fasilitas_kesehatan, 300);
            $this->output->set_header('ETag:' .$etag);
            $this->output->set_header('Cache-Control: must-revalidate');
            if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
                $this->output->set_header('HTTP/1.1 304 Not Modified');
            }else{
                $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                        "code"=>200,
                        "message"=>"Response successfully",
                        "data"=>$fasilitas_kesehatan];
                $this->response($result, 200);
            } 
        }
    }
}