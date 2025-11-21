<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pegawai extends MX_Controller {

	function Pegawai(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('pegawai_model','pegawai');

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
        $uri_segment=3;
        $offset = 0;
        $orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        $SQLcari="";
        if(!empty($orgid)) {
            $s = array();
            foreach($orgid as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and deptid in (".implode(',', $s).") ";
        }


        $SQLcari .=" and jenispegawai in (1,2)";
        $SQLcari .= " ORDER BY id asc";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('pegawai/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),5,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();

        $priv = $this->pegawai->getprivilege();
        $privi = array();
        foreach($priv->result() as $privilege)
        {
            $privi[$privilege->id] = $privilege->privilege;
        }
        $data['priv'] = $privi;
        $data['order'] = 'id';
        $data['typeorder'] = 'sorting';
        $this->template->load('template','display',$data);
    }

    public function pagging($page=0)
    {
        $priv = $this->pegawai->getprivilege();
        $privi = array();
        foreach($priv->result() as $privilege)
        {
            $privi[$privilege->id] = $privilege->privilege;
        }
        $data['priv'] = $privi;

        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( name LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or badgenumber LIKE '%".str_replace('%20',' ',$cr)."%' or deptname LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }

        if (isset($org) && ($org!='' || $org!=null))
        {
            $orgid = $this->pegawai->deptonall($org);
        } else {
            $orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        }

        if(!empty($orgid)) {
            $s = array();
            foreach($orgid as $ar)
                $s[] = "'".$ar."'";

                $SQLcari .= " and deptid in (".implode(',', $s).") ";
        }

        if(!empty($stspeg)) {
            $s = array();
            foreach($stspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and jenispegawai in (".implode(',', $s).") ";
        }

        $data['order'] = $this->input->post('order');
        $typeorder = $this->input->post('sorting');

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
            $SQLcari .= " ORDER BY Id ASC ";
        }


        $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("pegawai/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    public function form($noId=null)
    {
        $noId = $noId ==null ? 0 : $noId ;
        $data["id"]=$noId;
        $datar = $this->db->get_where('view_employee', array('id' => $noId))->row_array();
        if (count($datar)==0) {
            $row = $this->db->query("select * from view_employee limit 1")->row_array();
            foreach ($row as $key => $val) {
                $datar[$key]=null;
            }
        }

       $data["field"] = $datar;
        $this->db->order_by("keselon","desc");
        $rows= $this->db->get("ref_eselon");

        foreach($rows->result() as $row)
        {
            $dataeselon[$row->keselon] = $row->neselon;
        }
       $data["lstEselon"] = $dataeselon;

        $rows1= $this->db->get("mastertunjangan");

        foreach($rows1->result() as $row)
        {
            $datakelas[$row->kelasjabatan] = $row->kelasjabatan.' ['.number_format($row->tunjangan,0,',','.').']';
        }
        $data["lstKelas"] = $datakelas;

        $rows2= $this->db->get("ref_golruang");

        foreach($rows2->result() as $row)
        {
            $datagol[$row->ngolru] = $row->ngolru.'  ['.$row->pangkat.']';
        }
        $data["lstGol"] = $datagol;

       $this->template->load('template','form',$data);
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;

        $dataIn['tunjanganprofesi'] = $this->input->post('tunjanganprofesi');
        $dataIn['tmtprofesi'] =  dmyToymd($this->input->post('tmtprofesi'));
        $dataIn['plt_deptid'] =  $this->input->post('plt_deptid');
        $dataIn['plt_kelasjabatan'] =  $this->input->post('plt_kelasjabatan');
        $dataIn['no_rekening'] =  $this->input->post('no_rekening');
        $dataIn['payable'] =  $this->input->post('payable');

        if ($idx>0)
        {
            $this->db->where('id', $idx);
            $dataawal=$this->db->get("userinfo")->row_array();
            log_history("edit","userinfo",$dataawal);

            $dataIn['modify_by'] =  $this->session->userdata('s_username');
            $dataIn['modif_date'] =  date('Y-m-d H:i:s');
            $this->db->where('id', $idx);
            $update = $this->db->update('userinfo',$dataIn);
            if(!$update){
                createLog("Merubah Pegawai ".$dataawal["userid"],"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                createLog("Merubah Pegawai ".$dataawal["userid"],"Sukses");
                $data['msg'] = 'Data berhasil dirubah..';
                $data['status'] = 'succes';
            }
        }
        die(json_encode($data));
    }
}
