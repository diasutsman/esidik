<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {

	function Login(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->library('auth');
        $this->load->model('utils_model','utils');
		//$this->auth->check_user_authentification();
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		//$this->is_logged_in();
	}

	function is_logged_in(){
		if($this->session->userdata('is_logged_in')){
			redirect('home');
		}
    }
	
	function killSession()
	{
	}
	
	function index()
	{
        if(empty($_POST)){
            $this->is_logged_in();
            $this->index_list();
        }
        else {
            $this->validate();
        }
	}
	
	function index_list()
	{

        $data['image'] = randomString(6);
        $data['acak'] = ENVIRONMENT=="development"?$data['image']:"";
        $this->session->set_userdata('mycapture', $data['image']);
        $this->load->view("login",$data);
	}

    function validate(){
        if (!$this->_user_validation())
        {
            $this->session->set_userdata('error_msg', validation_errors());
            redirect();
        }
        else
        {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $data['capture'] = $this->input->post('capture');
            $query = $this->utils->validation($username, $password);

            if($query->num_rows() > 0){
                $row = $query->row();
                $que = $this->utils->insert_online($username);
                //$rw = $que->row_array();
                //$dtup=array('lastlogin' => date('Y-m-d H:i:s'));
                //$uvisitor = getRealIpAddr().' | '.$this->session->userdata('user_agent');

                if(!$this->session->userdata('mycapture')){
                    redirect();
                }

                if(strtolower($this->session->userdata('mycapture')) !== strtolower($data['capture'])){
                    $datax = array(
                        'error_msg' => "Kode Unik Salah"
                    );
                    createLog("Login to system","Kode Unik Salah !");
                    $this->session->set_userdata($datax);
                    $datax['image'] = randomString(6);

                    $this->session->set_userdata('mycapture', $datax['image']);
                    $this->session->set_userdata($datax);
                    redirect();
                }

                $data = array(
                    's_id' => $row->id,
                    's_userid' => $row->userid,
                    's_username' => $row->username,
                    's_email' =>$row->email,
                    's_dept' => empty($row->dept_id)?"1":$row->dept_id,
                    's_access' => $row->user_level_id,
                    's_area' => $row->area_id,
                    'is_logged_in' => TRUE
                );
                $this->session->set_userdata($data);
				createLog("Login to system","Sukses");
                redirect('home');
            }else{
                $data = array(
                    'error_msg' => "Username dan/atau Password salah !"
                );
                createLog("Login to system","Username dan/atau Password salah !");
                $this->session->set_userdata($data);
                $data['image'] = randomString(6);

                $this->session->set_userdata('mycapture', $data['image']);
                $this->session->set_userdata($data);
                redirect();
            }
        }
    }

    function _user_validation(){
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('capture', 'Kode Unik', 'trim|required');

        return $this->form_validation->run();
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */