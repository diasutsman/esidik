<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shiftkhusus extends MX_Controller {
    private $aAkses;

	function Shiftkhusus(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('shift/shift_model');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Shift", $this->session->userdata('s_access'));
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
        $data['aksesrule']=$this->aAkses;
        $uri_segment=3;
        $offset = 0;
        $SQLcari =" AND khusus=1";
        $query = $this->shift_model->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->shift_model->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('shiftkhusus/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
		$this->template->load('template','display',$data);
	}

    public function pagging($page=0)
    {
        $data['aksesrule']=$this->aAkses;
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( code_shift LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or name_shift LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }

        $SQLcari .=" AND khusus=1";

        $query = $this->shift_model->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->shift_model->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("shiftkhusus/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    public function edit($noId=0)
    {
        $this->output->set_output( json_encode($this->db->get_where('master_shift', array('id_shift' => $noId))->row_array()));
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('txt2');

        $dataIn['deptid'] = $t1;
        $dataIn['deptname'] =  $t2;


        if ($idx==0)
        {
            $dataIn['khusus'] =  1;
            $dataIn['create_date'] =  date("Y-m-d h:i:s");
            $dataIn['create_by'] =  $this->session->userdata('s_username');
            $insert = $this->db->insert('master_shift', $dataIn);
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
            $dataIn['modif_date'] =  date("Y-m-d h:i:s");
            $dataIn['modif_by'] =  $this->session->userdata('s_username');
            $this->db->where('id', $idx);
            $update = $this->db->update('master_shift',$dataIn);
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

    public function hapus()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id_shift', $nId);
            $query=$this->db->get("master_shift");
            $datas = $query->row_array();
            log_history("delete","master_shift",$datas);

            $this->db->where('id_shift',$nId);
            $this->db->delete('master_shift');

            if (isset($datas)) {
                createLog("Menghapus shift " . $datas["code_shift"] . " " . $datas["name_shift"] , "Sukses");
            }
        }

        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        $this->output->set_output( json_encode($data));
    }

    public function saveedit($id)
    {
        $this->db->where('id_shift', $id);
        $data=$this->db->get("master_shift")->row_array();
        log_history("edit","master_shift",$data);

        $dataIn= $this->input->post();
        $dataIn["start_in"]=preg_replace('/\s+/', '', $dataIn["start_in"]);
        $dataIn["check_in"]=preg_replace('/\s+/', '', $dataIn["check_in"]);
        $dataIn["end_check_in"]=preg_replace('/\s+/', '', $dataIn["end_check_in"]);
        $dataIn["start_break"]=preg_replace('/\s+/', '', $dataIn["start_break"]);
        $dataIn["break_out"]=preg_replace('/\s+/', '', $dataIn["break_out"]);
        $dataIn["break_in"]=preg_replace('/\s+/', '', $dataIn["break_in"]);
        $dataIn["end_break"]=preg_replace('/\s+/', '', $dataIn["end_break"]);
        $dataIn["start_out"]=preg_replace('/\s+/', '', $dataIn["start_out"]);
        $dataIn["check_out"]=preg_replace('/\s+/', '', $dataIn["check_out"]);
        $dataIn["end_check_out"]=preg_replace('/\s+/', '', $dataIn["end_check_out"]);
        unset( $dataIn[0] );
        $this->db->where('id_shift',$id);
        $this->db->update('master_shift',$dataIn);

        createLog("Merubah shift ".$id." ".$dataIn["code_shift"],"Sukses");

        $data['msg'] = 'Data berhasil dirubah..';
        $data['status'] = 'succes';

        $this->output->set_output( json_encode($data));
    }

    public function simpanbaru()
    {
        $dataIn= $this->input->post();
        $dataIn["start_in"]=preg_replace('/\s+/', '', $dataIn["start_in"]);
        $dataIn["check_in"]=preg_replace('/\s+/', '', $dataIn["check_in"]);
        $dataIn["end_check_in"]=preg_replace('/\s+/', '', $dataIn["end_check_in"]);
        $dataIn["start_break"]=preg_replace('/\s+/', '', $dataIn["start_break"]);
        $dataIn["break_out"]=preg_replace('/\s+/', '', $dataIn["break_out"]);
        $dataIn["break_in"]=preg_replace('/\s+/', '', $dataIn["break_in"]);
        $dataIn["end_break"]=preg_replace('/\s+/', '', $dataIn["end_break"]);
        $dataIn["start_out"]=preg_replace('/\s+/', '', $dataIn["start_out"]);
        $dataIn["check_out"]=preg_replace('/\s+/', '', $dataIn["check_out"]);
        $dataIn["end_check_out"]=preg_replace('/\s+/', '', $dataIn["end_check_out"]);
        $dataIn["khusus"]=1;

        if ($this->db->insert('master_shift',$dataIn)) {
            createLog("Menambah shift  ".$dataIn["code_shift"],"Sukses");
            $data['msg'] = 'Data berhasil disimpan..';
            $data['status'] = 'succes';
        } else
        {
            createLog("Menambah shift ".$dataIn["code_shift"],"Gagal");
            $data['msg'] = "Tidak berhasil menyimpan data..<br>Cek kembali isian..!!";
            $data['status'] = 'error';
        }

        $this->output->set_output( json_encode($data));
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */