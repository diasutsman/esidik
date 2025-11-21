<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rptstat extends MX_Controller {

    private $aAkses;

	function Rptstat(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
        $this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('report_model');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Rptstat", $this->session->userdata('s_access'));
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

        $this->session->set_userdata('menu','23');
        $uri_segment=3;
        $offset = 0;
        //$orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        /*$SQLcari =" and jenispegawai in (1,2)";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);*/
        $this_url = site_url('rpttransaksi/pagging/');
        $data2 = $this->mypagination->getPagination(0,10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = 0;
        $data['result'] = array();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
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
            $SQLcari .= " AND ( deptname LIKE '%".str_replace('%20',' ',$cr)."%' or name LIKE '%".str_replace('%20',' ',$cr)."%'
                        or userid LIKE '%".str_replace('%20',' ',$cr)."%' or badgenumber LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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
        $SQLcari .= " and jftstatus in ('1','2')  ";
        $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('rptstat/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();

        $this->load->view('list',$data);
    }

    public function save()
    {
        $id = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $orgid = $this->input->post('org');
        $cari = $this->input->post('cari');

        //createLogUser("Delete Data ".$nId,"Pengguna");
        $data['msg'] = 'Data berhasil diproses..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function view()
    {
        $postdatestart = $this->input->post('start');
        $postdatestop = $this->input->post('end');
        $selall = "fasel";//$this->input->post('selall');
        $statusid = "";$this->input->post('status');
        $userid = $this->input->post('uid');
        $excelid = 0;//$this->input->post('excelid');
        $compa = $this->report_model->getcompany();
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(explode(',', $this->session->userdata('s_area'))):array();

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $deptGroupid = $this->report_model->getdepart();

        $company = array(
            'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
            'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
            'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
            'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
            'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
            'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
        );

        $data['_height'] = '';
        $data['cominfo'] = $company;

        $yo = 0;
        if($this->input->get('orgid')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetails($orgid);
            $yo=1;
        }

        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetails($userar);
            $yo=2;
        }
        if($selall=='true') {
            $queryemp = $this->report_model->getallemployeedetails();
            $yo=3;
        }
        $index=0;
        foreach($queryemp->result() as $queq) {
            $dataallay['userid'] = $queq->userid;
            $dataallay['datestart'] =  date('d-M-Y', $datestart);
            $dataallay['empID'] = $queq->badgenumber;
            $dataallay['datestop'] = date('d-M-Y', $datestop);
            $dataallay['empName'] = $queq->name;
            $dataallay['deptName'] = isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'';
            $data['empinfo']=$dataallay;

            $querytemp = $this->report_model->getallstatus($datestart, $datestop, $queq->userid, $statusid);

            $countrow = $querytemp->num_rows();
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporanstatus.xls");
            }
            if($yo==2) {
                $data['data'] = $querytemp;
                $data['index'] = $index;
                $this->load->view("laporan",$data);
                $index++;
            } else {
                if($countrow>0) {
                    $data['data'] = $querytemp;
                    $data['index'] = $index;
                    $this->load->view("laporan",$data);
                    $index++;
                }
            }
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */