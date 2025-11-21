<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller  {

	function __construct(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->library('auth');
        $this->load->model('Utils_model','utils');
		// $this->auth->check_user_authentification()U
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
        $this->config->load('cust_settings', TRUE);
		// $this->is_logged_in();
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
        $webropeg_url = $this->config->item('webropeg_url', 'cust_settings');
        $param=$this->uri->segment('3');
        $enc = str_replace("%20","=",$param);
        $sso_username = base64_decode($enc);

        if ($sso_username!="" || $sso_username!=NULL) {
            $username=$sso_username;
            // if(empty($_POST)){
            //     $this->is_logged_in();
            //     $this->index_list();
            // }
            // else {
              $this->validate($username);
            // }
        }else if($this->session->userdata('is_logged_in')!="" || $this->session->userdata('is_logged_in')!=NULL){
              $url=$webropeg_url."homepage/masuk";
                echo "<script>window.location='".$url."';</script>"; 
        }else {
            
            $url=$webropeg_url."loginsso";
            echo "<script>window.location='".$url."';</script>"; 
            
        }
	}
	
	function index_list()
	{

        $data['image'] = randomString(6);
        $data['acak'] = ENVIRONMENT=="development"?$data['image']:"";
        $this->session->set_userdata('mycapture', $data['image']);
        $this->load->view("login",$data);
	}

    function validate($nip){
        $webropeg_url = $this->config->item('webropeg_url', 'cust_settings');
        // if (!$this->_user_validation())
        // {
        //     $this->session->set_userdata('error_msg', validation_errors());
        //     redirect();
        // }
        // else
        // {
            $username = $nip;
            if($username == '3273232510910001'){
                $userid = 'super_root';
            }else if($username == '3273161902980004'){
                $userid = 'anggims';
            }else if($username == 'krisnu' OR $username == '197303142008011008'){
                $userid = 'super_root';
            }else if($username == '198911112014021002'){
                $userid = 'ycghs';
            }else if($username == 'bpkkdn2023'){
                $userid = 'bpkkdn2023';
            }else{
                $userid = $username;
            }
            // $password = $this->input->post('password');
            // $data['capture'] = $this->input->post('capture');
            //$query = $this->utils->validation($username, $password);
            $query = $this->utils->validation2($userid);
            //var_dump( $query);die;
            if($query->num_rows() > 0){
                $row = $query->row();
                $que = $this->utils->insert_online($username);
                //$rw = $que->row_array();
                //$dtup=array('lastlogin' => date('Y-m-d H:i:s'));
                //$uvisitor = getRealIpAddr().' | '.$this->session->userdata('user_agent');

                // if(!$this->session->userdata('mycapture')){
                //     redirect();
                // }

                // if(strtolower($this->session->userdata('mycapture')) !== strtolower($data['capture'])){
                //     $datax = array(
                //         'error_msg' => "Kode Unik Salah"
                //     );
                //     createLog("Login to system","Kode Unik Salah !",$username);
                //     $this->session->set_userdata($datax);
                //     $datax['image'] = randomString(6);

                //     $this->session->set_userdata('mycapture', $datax['image']);
                //     $this->session->set_userdata($datax);
                //     redirect();
                // }

                if (!empty($row->dept_id))
                {
                    if (strpos($row->dept_id,","))
                    {
                        $dept = explode(',',$row->dept_id);
                    } else
                    {
                        $dept=array($row->dept_id);
                    }
                } else {
                    $dept=array("01000000");
                }

                $data = array(
                    's_id' => $row->id,
                    's_userid' => $row->userid,
                    's_user_level_id' => $row->user_level_id,
                    's_username' => $row->username,
                    's_email' =>$row->email,
                    's_dept' => $dept,//empty($row->dept_id)?"1":$row->dept_id,
                    's_access' => $row->user_level_id,
                    's_area' => $row->area_id,
                    'is_logged_in' => TRUE
                );
                $this->session->set_userdata($data);
				createLog("Login to system","Sukses");
                redirect('home');
            }
            else{
                // $data = array(
                //     'error_msg' => "Username dan/atau Password salah !"
                // );
                // createLog("Login to system","Username dan/atau Password salah !");
                // $this->session->set_userdata($data);
                // $data['image'] = randomString(6);

                // $this->session->set_userdata('mycapture', $data['image']);
                // $this->session->set_userdata($data);
                // redirect();
                 $url=$webropeg_url."homepage/masuk";
                echo "<script>window.location='".$url."';</script>"; 
                //redirect($webropeg_url.'homepage');
            }
        // }
    }

    function _user_validation(){
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        //$this->form_validation->set_rules('password', 'Password', 'trim|required');
        //$this->form_validation->set_rules('capture', 'Kode Unik', 'trim|required');

        return $this->form_validation->run();
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */