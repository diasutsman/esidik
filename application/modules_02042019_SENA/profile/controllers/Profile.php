<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Profile extends MX_Controller {

	function Profile(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('profile_model','profile');

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
		$this->session->set_userdata('menu','1');
		$data['menu'] = '1';
        $isValid = $this->profile->getMyProfile()->row_array()==null?False:True;
        $data['myProfile'] = $isValid? $this->profile->getMyProfile()->row_array(): $this->profile->getMyProfile2()->row_array();
        $data['myData'] = $this->profile->getMyActivities();
        $data['isValid'] = $isValid;
		$this->template->load('template','home',$data);
	}

    function rubihpwd()
    {
        $id = $this->session->userdata('s_id');
        $pwd = $this->input->post('txtpassnew');
        $dataIn["password_md5"]=md5($pwd);
        $this->db->where('id', $id);
        $this->db->update('users',$dataIn);

        $data['msg'] = 'Password berhasil dirubah..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */