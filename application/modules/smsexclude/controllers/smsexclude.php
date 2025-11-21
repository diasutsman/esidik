<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class smsexclude extends MX_Controller {

    private $aAkses;
	function smsexclude(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('sms_exclusion_model','sms_exclusion');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("smsexclude", $this->session->userdata('s_access'));
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
		$this->session->set_userdata('menu','33');
		$data['menu'] = '33';
        $uri_segment=3;
        $offset = 0;
        $SQLcari = " ORDER BY a.userid asc";
        $query = $this->sms_exclusion->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->sms_exclusion->getDaftar(0,null,null,null,null);
        $this_url = site_url('sms_exclusion/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['order'] = 'a.userid';
        $data['typeorder'] = 'sorting_asc';
        $data['aksesrule']=$this->aAkses;
		$this->template->load('template','display',$data);
	}

    public function pagging($page=0)
    {
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);
        $data['order'] = $this->input->post('order');
        $typeorder = $this->input->post('sorting');
        $data['aksesrule']=$this->aAkses;
        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( a.userid LIKE '%".str_replace('%20',' ',$cr)."%' 
				or b.name LIKE '%".str_replace('%20',' ',$cr)."%'
			) ";
        }

        if($typeorder!='' && $typeorder!=null){
            if($typeorder=='sorting'){
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            }else if($typeorder=='sorting_asc'){
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            }else{
                $sorting = 'DESC';
                $data['typeorder'] = 'sorting_desc';
            }
        }else{
            $sorting = 'ASC';
            $data['typeorder'] = 'sorting_asc';
        }

        if($data['order']!='' && $data['order']!=null){
            $SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
        }else{
            $SQLcari .= " ORDER BY a.userid asc ";
        }
        $query = $this->sms_exclusion->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->sms_exclusion->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("sms_exclusion/pagging");
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
        $this->db->select("a.*,b.name AS name");
        $this->db->from("sms_exclusion as a");
        $this->db->join("userinfo b ","b.userid=a.userid","left");
        $this->db->where('a.id',$noId);
        echo json_encode($this->db->get()->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('txt1');

        $dataIn['userid'] = $t1;

        if ($idx==0)
        {
            $insert = $this->db->insert('sms_exclusion', $dataIn);
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
            $this->db->where('id', $idx);
            $dataOld=$this->db->get("sms_exclusion")->row_array();

            $this->db->where('id', $idx);
            $update = $this->db->update('sms_exclusion',$dataIn);
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

            //$this->db->where('id', $nId);
            //$query=$this->db->get("sms_exclusion");
            //$datas = $query->row_array();

            $this->db->where('id',$nId);
            $this->db->delete('sms_exclusion');
        }

        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */