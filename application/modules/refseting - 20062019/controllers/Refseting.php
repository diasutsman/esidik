<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Refseting extends MX_Controller {
    private $aAkses;
	function Refseting(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Refseting", $this->session->userdata('s_access'));
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
	{$data['aksesrule']=$this->aAkses;
		$this->session->set_userdata('menu','9');
		$data['menu'] = '9';
        $data['lsttahun'] = getListTahun();
        $data['lstJdwdl'] = getListJadwal();

        $data['result'] = $this->db->get("company")->row_array();

		$this->template->load('template','display',$data);
	}

    public function save()
    {
        $idkx = $this->db->get("company");

        $idx = $idkx->num_rows();

        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('txt2');
        $t3 = $this->input->post('txt3');
        $t4 = $this->input->post('txt4');
        $t5 = $this->input->post('txt5');
        $t6 = $this->input->post('txt6');
        $t7 = $this->input->post('txt7');
        $t8 = $this->input->post('txt8');
        $t9 = $this->input->post('txt9');
        $t10 = $this->input->post('txt10');
        $t11 = $this->input->post('txt11');
        $t12 = $this->input->post('txt12');
        $txt13 = $this->input->post('txt13');
        $txt14 = $this->input->post('txt14');

        $dataIn['companyname'] = $t1;
        $dataIn['address1'] =  $t2;
        $dataIn['phone'] =  $t3;
        $dataIn['fax'] =  $t4;
        $dataIn['thp'] =  $t5;
        $dataIn['plt'] =  $t6;
        $dataIn['upacara'] =  $t12;
        $dataIn['aktivasi_tahun'] =  $t7;
        $dataIn['sesi_user'] =  intval($t8);
        $dataIn['def_shift'] =  intval($t9);
        $dataIn['def_shift_holy'] =  intval($t10);
        $dataIn['def_shift_fry'] =  intval($t11);
        $dataIn['batas_alpa_sms'] =  intval($t13);
        $dataIn['cpns'] =  intval($t14);

        if ($idx==0)
        {
            $insert = $this->db->insert('company', $dataIn);
            if (!$insert) {
                createLog("Menambah seting","Gagal");
                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {
                createLog("Menambah seting","Sukses");
                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->limit(1);
            $dataawal=$this->db->get("company")->row_array();
            log_history("edit","company",$dataawal);


            $update = $this->db->update('company',$dataIn);
            if(!$update){
                createLog("Merubah seting","Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                createLog("Merubah seting","Sukses");
                $data['msg'] = 'Data berhasil dirubah..';
                $data['status'] = 'succes';
            }
        }
        die(json_encode($data));
    }




}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */