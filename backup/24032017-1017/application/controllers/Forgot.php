<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forgot extends CI_Controller {

	function Forgot(){
		parent::__construct();
		$this->load->helper('utility');
        $this->load->helper('string');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
	}


	function killSession()
	{
	}
	
	function index()
	{
        if(empty($_POST)){
            $this->index_list();
        }
        else {
            redirect('forgot');
        }
	}
	
	function index_list()
	{
        $data['image'] = random_string("numeric", 6);

        $this->session->set_userdata('mycapture', $data['image']);
        $this->load->view("forgot",$data);
	}

}

