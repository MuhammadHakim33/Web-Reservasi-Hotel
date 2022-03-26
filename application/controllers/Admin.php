<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Model_main');
		$this->load->library('session');

		// Cek Status Login
		if(empty($this->session->userdata('status'))){
			redirect(base_url("login"));
			die;
		}

		// Cek Status Level
		if($this->session->userdata('level') != "administrator") {
			redirect(base_url("resepsionis"));
			die;
		}
	}

	public function index()
	{
		$data["title"] = "Dashboard";
		$data["nama"] = $this->session->userdata('nama');
		$data["kamar"] = $this->Model_main->getData('tbl_kamar');
		$data["fasilitas_kamar"] = $this->Model_main->get_join_data('tbl_fasilitas_kamar', 'tbl_kamar', 'id_kamar' , 'id');
		$data["fasilitas_hotel"] = $this->Model_main->getData('tbl_fasilitas_hotel');

        $this->load->view('staff/view_header', $data);
		$this->load->view('staff/view_dashboard_admin', $data);
        $this->load->view('staff/view_set_modal', $data);
        $this->load->view('staff/view_footer');
	}

	// Kumpulan Function Tambah Data
	public function tambah_kamar() {
		$tipe_kamar =  $this->input->post('tipe_kamar', true);
		$jumlah_kamar =  $this->input->post('jumlah_kamar', true);
		$gambar = $_FILES['gambar']['name'];

		$gambar = $this->upload_img($gambar);

		$table = 'tbl_kamar';
		$input = [
			'nama_tipe_kamar' => $tipe_kamar,
			'jumlah_kamar' => $jumlah_kamar,
			'gambar_kamar' => $gambar
		];

		$this->Model_main->insert_data($table, $input);
		redirect(base_url('admin'));
		die;
	}

	public function tambah_fasilitas_kamar() {
		$tipe_kamar =  $this->input->post('tipe_kamar', true);
		$fasilitas_kamar =  $this->input->post('fasilitas_kamar', true);

		$table = 'tbl_fasilitas_kamar';
		$input = [
			'id_kamar' => $tipe_kamar,
			'fasilitas_kamar' => $fasilitas_kamar
		];

		$this->Model_main->insert_data($table, $input);
		redirect(base_url('admin'));
		die;
	}

	public function tambah_fasilitas_hotel() {
		$fasilitas_hotel =  $this->input->post('fasilitas_hotel', true);
		$keterangan_fasilitas =  $this->input->post('keterangan_fasilitas', true);
		$gambar = $_FILES['gambar']['name'];

		$gambar = $this->upload_img($gambar);

		$table = 'tbl_fasilitas_hotel';
		$input = [
			'fasilitas_hotel' => $fasilitas_hotel,
			'keterangan_fasilitas' => $keterangan_fasilitas,
			'gambar_fasilitas_hotel' => $gambar
		];

		$this->Model_main->insert_data($table, $input);
		redirect(base_url('admin'));
		die;
	}


	// Kumpulan Function Edit Data
	public function edit_kamar() {
		$id =  $this->input->post('id', true);
		$tipe_kamar =  $this->input->post('tipe_kamar', true);
		$jumlah_kamar =  $this->input->post('jumlah_kamar', true);
		$gambar = $_FILES['gambar']['name'];

		$this->upload_img($gambar, true, $id, 'tbl_kamar', 'gambar_kamar');

		$table = 'tbl_kamar';
		$where = ['id' => $id];
		$input = [
			'nama_tipe_kamar' => $tipe_kamar,
			'jumlah_kamar' => $jumlah_kamar
		];

		$this->Model_main->update_data($table, $input, $where);
		redirect(base_url('admin'));
		die;
	}

	public function edit_fasilitas_kamar() {
		$id =  $this->input->post('id', true);
		$tipe_kamar =  $this->input->post('tipe_kamar', true);
		$fasilitas_kamar =  $this->input->post('fasilitas_kamar', true);

		$table = 'tbl_fasilitas_kamar';
		$where = ['id' => $id];
		$input = [
			'id_kamar' => $tipe_kamar,
			'fasilitas_kamar' => $fasilitas_kamar
		];

		$this->Model_main->update_data($table, $input, $where);
		redirect(base_url('admin'));
		die;
	}

	public function edit_fasilitas_hotel() {
		$id =  $this->input->post('id', true);
		$fasilitas_hotel =  $this->input->post('fasilitas_hotel', true);
		$keterangan_fasilitas =  $this->input->post('keterangan_fasilitas', true);
		$gambar = $_FILES['gambar']['name'];

		$this->upload_img($gambar, true, $id, 'tbl_fasilitas_hotel', 'gambar_fasilitas_hotel');

		$table = 'tbl_fasilitas_hotel';
		$where = ['id' => $id];
		$input = [
			'fasilitas_hotel' => $fasilitas_hotel,
			'keterangan_fasilitas' => $keterangan_fasilitas
		];

		$this->Model_main->update_data($table, $input, $where);
		redirect(base_url('admin'));
		die;
	}


	// Kumpulan Function Hapus Data
	public function hapus_kamar($id) {
		$table = 'tbl_kamar';
		$where = ['id' => $id];

		$this->Model_main->delete_data($table, $where);
		redirect(base_url('admin'));
		die;
	}

	public function hapus_fasilitas_kamar($id) {
		$table = 'tbl_fasilitas_kamar';
		$where = ['id' => $id];

		$this->Model_main->delete_data($table, $where);
		redirect(base_url('admin'));
		die;
	}

	public function hapus_fasilitas_hotel($id) {
		$table = 'tbl_fasilitas_hotel';
		$where = ['id' => $id];

		$this->Model_main->delete_data($table, $where);
		redirect(base_url('admin'));
		die;
	}



	public function upload_img($gambar, $edit = false, $id = null, $table = null, $field_gambar = null) {
		if($gambar) {
			$file_name = rand(1, 999);
			$config['file_name'] = $file_name;
			$config['upload_path'] = FCPATH.'/assets/img/';
			$config['allowed_types'] = 'jpg|png';
			$config['overwrite'] = true;

			$this->load->library('upload', $config);

			if(!$this->upload->do_upload('gambar')){
				$data['error'] = $this->upload->display_errors();
				redirect(base_url('admin'));
				die;
			} else { 

				$gambar = $this->upload->data('file_name'); 

				if($edit == false) { return $gambar; } 
				
				else {
					$where = ['id' => $id];
					$input = [$field_gambar => $gambar];
					$this->Model_main->update_data($table, $input, $where);
				}
			}
		}

	}
    
}
