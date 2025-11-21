<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rptros extends MX_Controller {

	function Rptros(){
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

        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        //echo $this->db->last_query();
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('rptros/pagging/');
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
        $compa = $this->report_model->getcompany();
        $dept = $this->report_model->getdept();
        $excelid = $this->input->post('xls');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);

        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $company = array(
            'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
            'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
            'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
            'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
            'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
            'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
        );

        $this->db->select('code_shift, colour_shift');
        $this->db->from('master_shift');
        $shiftcolor = $this->db->get();
        $shiftcol = array();
        foreach($shiftcolor->result() as $sc)
            $shiftcol[$sc->code_shift] = $sc->colour_shift;

        /*if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(explode(',', $this->session->userdata('s_area'))):array();

        $postdatestart = dmyToymd($this->input->post('start'));
        $postdateend = dmyToymd($this->input->post('end'));
        $datestart = strtotime($postdatestart);
        $dateend = strtotime($postdateend);

        $userlist = $this->report_model->getempofdept($areaid, $orgid,$stspeg);

        $data['deptname'] = isset($deptar[$this->input->post('org')])?$deptar[$this->input->post('org')]:$deptar['1'];

        $countuserlist = $userlist->num_rows();
        if($countuserlist!=0) {
            $range = ($dateend - $datestart) / 86400;

            $roster = $this->report_model->getroster($orgid, $datestart, $dateend);
            $arrayroster = array();
            foreach($roster->result() as $rosterdetail) {
                $arrayroster[$rosterdetail->userid][strtotime($rosterdetail->rosterdate)] = array ('absence' => $rosterdetail->absence, 'attendance' => $rosterdetail->attendance);
            }

            $nonarray = array();

            $holiday = $this->utils->cekholiday($datestart, $dateend);
            $holarray = array();
            foreach($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if($selisih==0) {
                    $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                } else {
                    $jarak = $selisih / 86400;
                    for($k=0;$k<=$jarak;$k++) {
                        $holarray[$hol->deptid][date('Y-m-d',strtotime($hol->startdate) + ($k*86400))] = $hol->info;
                    }
                }
            }

            $data['nonarray'] = $nonarray;
            $data['holarray'] = $holarray;
            $data['datestart'] = $datestart;
            $data['empdata'] = $userlist;
            $data['rosterdata'] = $arrayroster;*/
            $data['cominfo'] = $company;
            $data['shiftcolor'] = $shiftcol;

            //$data['range'] = $range;
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporanjadwalkerja.xls");
            }
            $this->load->view("laporan",$data);
        //}
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */