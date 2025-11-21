<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Setsms extends MX_Controller {
    private $aAkses;
	function Setsms(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("setsms", $this->session->userdata('s_access'));
		
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
		$data['aksesrule']=$this->aAkses;
		$this->session->set_userdata('menu','9');
		$data['menu'] = '9';

        $data['result'] = $this->db->get("sms_setting")->row_array();

		$this->template->load('template','display',$data);
	}

    public function save()
    {
        $idkx = $this->db->get("sms_setting");

        $idx = $idkx->num_rows();

        $t1 = $this->input->post('txt1');
       // $t2 = $this->input->post('txt2');
        $t3 = $this->input->post('txt3');
        $t4 = $this->input->post('txt4');
		$t5 = $this->input->post('txt5');

        $dataIn['simpeg_host'] = $t1;
        //$dataIn['sms_sender'] =  $t2;
        $dataIn['checkinout_hour'] =  intval($t3);
        $dataIn['checkinout_minute'] =  intval($t4);
		$dataIn['sms_msg1'] =  $t5;

        if ($idx==0)
        {
            $insert = $this->db->insert('sms_setting', $dataIn);
            if (!$insert) {

                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {

                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $update = $this->db->update('sms_setting',$dataIn);
            if(!$update){

                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                $data['msg'] = 'Data berhasil dirubah..';
                $data['status'] = 'succes';
            }
        }
        die(json_encode($data));
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */