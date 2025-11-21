<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Jnspotongan extends MX_Controller {

	function Jnspotongan(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('Data_model','data');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }

        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');

	}


	function killSession()
	{
	}
	
	function index()
	{
		$this->killSession();
		$this->index_list();
	}
	
	function index_list()
	{
		$this->session->set_userdata('menu','9');
		$data['menu'] = '9';
        $data['lstdata'] = $this->data->getDaftar();
		$this->template->load('template','display',$data);
	}
	

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */