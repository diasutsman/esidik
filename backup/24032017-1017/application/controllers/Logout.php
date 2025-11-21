<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Logout extends CI_Controller {

	function Logout(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->library('auth');
        $this->load->model('utils_model','utils');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
	}

	function killSession()
	{
        $username = $this->session->userdata('s_username');
        $uvisitor = getRealIpAddr().' | '.$this->session->userdata('user_agent');
        $que = $this->utils->insert_online($username);
        $this->session->sess_destroy();
	}
	
	function index()
	{
		$this->killSession();
		$this->index_list();
	}
	
	function index_list()
	{
        redirect('login');
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */