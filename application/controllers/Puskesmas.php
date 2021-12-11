<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Puskesmas extends REST_Controller{

	function __construct($config = 'rest'){
		parent::__construct($config);
		$this->load->driver('cache', array('adapter' => 'apc','backup' => 'file'));
	}

	//Menampilkan data
	public function index_get(){

		$id = $this->get('id');
		$puskesmas=[];
		if ($id == '') {
			$data = $this->db->get('puskesmas')->result();
			foreach ($data as $row => $key): 
				$puskesmas[]=[
                        "PuskesmasID"=>$key->PuskesmasID,
						"wilayah"=>$key->wilayah,
						"nama_puskesmas"=>$key->nama_puskesmas,
						"alamat_puskesmas"=>$key->alamat_puskesmas,
					];
			endforeach;

			$etag = hash('sha256', time());
			$this->cache->save($etag, $puskesmas, 300);
			$this->output->set_header('ETag:'.$etag);
			$this->output->set_header('Cache-Control: must-revalidate');
			if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
				$this->output->set_header('HTTP/1.1 304 Not Modified');
			}else{
				$result = [
					"took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					"code"=>200,
					"message"=>"Response successfully",
					"data"=>$puskesmas
				];
				$this->response($result, 200);
			}

		}else{
			$this->db->where('PuskesmasID', $id);
			$data = $this->db->get('puskesmas')->result();
			$puskesmas[]=[
				"PuskesmasID"=>$key->PuskesmasID,
						"wilayah"=>$key->wilayah,
						"nama_puskesmas"=>$key->nama_puskesmas,
						"alamat_puskesmas"=>$key->alamat_puskesmas,
			];
		$etag = hash('sha256', $data[0]->PuskesmasID);
		$this->cache->save($etag, $puskesmas, 300);
		$this->output->set_header('ETag:'.$etag);
		$this->output->set_header('Cache-Control: must-revalidate');
		if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
			$this->output->set_header('HTTP/1.1 304 Not Modified');
		}else{
			$result = [
				"took"=>$_SERVER["REQUEST_TIME_FLOAT"],
				"code"=>200,
				"message"=>"Response successfully",
				"data"=>$puskesmas];
			$this->response($result, 200);
		}
	}

}

//Menambah data
	public function index_post(){
		$data = array(
					'PuskesmasID' => $this->post('PuskesmasID'),
					'wilayah' => $this->post('wilayah'), 
					'nama_puskesmas'=> $this->post('nama_puskesmas'),
					'alamat_puskesmas'=> $this->post('alamat_puskesmas')
				);
		$this->db->where("PuskesmasID", $this->post('PuskesmasID'));
		$this->db->where("alamat_puskesmas", $this->post('alamat_puskesmas'));
		$check = $this->db->get('puskesmas')->num_rows();
		if ($check==0):
            $insert = $this->db->insert('puskesmas', $data);
			if ($insert) {
				$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					"code"=>201,
					"message"=>"Response successfully",
					"data"=>$data];
				$this->response($result, 201);
			}else{
				$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					"code"=>502,
					"message"=>"Failed adding data",
					"data"=>null];
				$this->response($result, 502);
			}
		else:
			$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					"code"=>304,
					"message"=>"Data already added",
					"data"=>$data];
				$this->response($result, 304);
		endif;
	}
	//Memperbarui data
		public function index_put(){
			$id = $this->put('PuskesmasID');
			$data = array(
						'wilayah' => $this->put('wilayah'), 
						'nama_puskesmas'=> $this->put('nama_puskesmas'),
						'alamat_puskesmas'=> $this->put('alamat_puskesmas')
					);
			$this->db->where('productCode', $id);
			$update = $this->db->update('puskesmas', $data);
			if ($update) {
				$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					"code"=>200,
					"message"=>"Data Updated",
					"data"=>$data];
				$this->response($result, 200);
			}else{
				$this->response(array('status' => 'fail', 502));
			}
		}

		//Mnghapus data
		public function index_delete(){
			$id = $this->delete('productCode');
			//check data
			$this->db->where('productCode', $id);
			$check = $this->db->get('orderdetails')->num_rows();
			if($check==0):
				$this->output->set_header('HTTP/1.1 304 Not Modified');
			else:
				$this->db->where('productCode', $id);
				$delete = $this->db->delete('orderdetails');
				$this->db->where('productCode', $id);
				if ($delete) {
					$this->response(array('status' => 'success'), 201);
				}else{
					$this->response(array('status' => 'fail', 502));
				}
			endif;
		}
}
?>