<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rpttransaksi extends MX_Controller {

	function Rpttransaksi(){
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
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('rpttransaksi/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();

        $this->load->view('list',$data);
    }

    public function view()
    {

        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $orgid = $this->input->post('org');
        $pilrpt = $this->input->post('jnslap');

        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $selall = 'false';//$this->input->post('selall');
        $sorting = 'true';//$this->input->get('sorting');
        $userid = $this->input->post('uid');
        $excelid = $this->input->post('xls');;

        if($orgid!='undefined')
            $orgid = $this->pegawai->deptonall($orgid);
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(explode(',', $this->session->userdata('s_area'))):array();

        $esen = $this->report_model->gettermid();
        foreach($esen->result() as $es) {
            $esar[$es->sn] = array(
                'terminal_id'	=> $es->terminal_id,
                'alias'			=> $es->alias,
                'sn'			=> $es->sn
            );
        }

        $state = $this->report_model->getstate();
        foreach($state->result() as $st) {
            $star[$st->id] = $st->state;
        }

        $att = $this->report_model->getatt();
        foreach($att->result() as $at) {
            $atar[$at->atid] = $at->atname;
        }

        $rd = $this->report_model->getrd($postdatestart, $postdatestop);
        foreach($rd->result() as $rosdet) {
            $rdar[$rosdet->rosterdate] = $rosdet->attendance;
        }

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }
        $yo = 0;
        if($this->input->get('orgid')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetails($orgid,$stspeg);
            $yo=1;
        } else if($userid!='undefined') {
            $userar = explode(",",$this->input->post('uid'));
            $queryemp = $this->report_model->getuseremployeedetails($userar,$stspeg);
            $yo=2;
        }
        if($selall=='true') {
            $queryemp = $this->report_model->getallemployeedetails($stspeg);
            $yo=3;
        }
        $dataallay = array();
        $dataallu = array();

        foreach($queryemp->result() as $queq) {
            $dataallay[] = array(
                'userid'   => $queq->userid,
                'empTitle' => $queq->title,
                'empID' => $queq->userid,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''
            );
            $dataallu[$queq->userid] = array(
                'empTitle' => $queq->title,
                'empID' => $queq->userid,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''
            );
        }
        $compa = $this->report_model->getcompany();
        $company = array(
            'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
            'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
            'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
            'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
            'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
            'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
        );

        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        if($pilrpt=='1') {
            $dataallaye = array();
            $abc = 0;
            $dataview = '';
            $datavw = '';
            $range = ($datestop - $datestart) / 86400;
            for($x=0;$x<=$range;$x++) {
                $tanggal = date('Y-m-d', $datestart + ($x * 86400));
                if($yo==1) {
                    $querytemp = $this->report_model->gettranslogbydateorg($tanggal, $orgid, $areaid,$stspeg);
                } else if($yo==2) {
                    $querytemp = $this->report_model->gettranslogbydateuser($tanggal, $userar,$stspeg);
                } else if($yo==3) {
                    $querytemp = $this->report_model->gettranslogbydate($tanggal, $areaid,$stspeg);
                }

                $dataarray = array();
                foreach($querytemp->result() as $que) {
                    if($que->editby != '') {
                        $desc = isset($rdar[date('Y-m-d', strtotime($que->checktime))])?$atar[$rdar[date('Y-m-d', strtotime($que->checktime))]]:'Attendance Status';
                    } else {
                        $desc = isset($star[$que->checktype])?$star[$que->checktype]:null;
                    }
                    $dataarray[] = array (
                        'userid'		=> $que->userid,
                        'badgeNumber'	=> $dataallu[$que->userid]['empID'],
                        'name'			=> $dataallu[$que->userid]['empName'],
                        //'SN'			=> isset($esar[$que->sn]['terminal_id'])?$esar[$que->sn]['terminal_id']:1,
                        'SN'			=> isset($esar[$que->sn]['sn'])?$esar[$que->sn]['sn']:1,
                        'alias'			=> isset($esar[$que->sn]['alias'])?$esar[$que->sn]['alias']:'',
                        'datelog'		=> date('d-m-Y', strtotime($que->checktime)),
                        'timelog'		=> date('H:i:s', strtotime($que->checktime)),
                        'functionkey'	=> $que->checktype,
                        'description'	=> $desc,
                        'verifymode'	=> $que->verifycode,
                        'edited'		=> $que->editdate=='0000-00-00 00:00:00'? null : $que->editdate,
                        'editby'		=> $que->editby
                    );
                }
                $dataallaye = array(
                    'datee'   	=> date('Y-m-d', strtotime($tanggal))
                );
                $data = array(
                    "index" => $abc,
                    "cominfo" => $company,
                    "empinfo" => $dataallaye,
                    "data" => $dataarray
                );
                $abc++;
                if($excelid==1) {
                    $dataview = $this->load->view("lapbydate",$data,true);
                    $datavw = $datavw.$dataview;
                } else {
                    $this->load->view("lapbydate",$data);
                }
            }
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporantransaksi.xls");
                echo "$datavw";
            }
        } else {
            $dataallaye = array();
            $abc = 0;
            $dataview = '';
            $datavw = '';
            foreach($dataallay as $queqe) {
                $querytemp = $this->report_model->gettranslog($datestart, $datestop, $queqe['userid']);
                $dataarray = array();
                foreach($querytemp->result() as $que) {
                    if($que->editby != '') {
                        $desc = isset($rdar[date('Y-m-d', strtotime($que->checktime))])?$atar[$rdar[date('Y-m-d', strtotime($que->checktime))]]:'Attendance Status';
                    } else {
                        $desc = isset($star[$que->checktype])?$star[$que->checktype]:null;
                    }
                    $dataarray[] = array (
                        'SN'			=> isset($esar[$que->sn]['sn'])?$esar[$que->sn]['sn']:1,
                        'alias'			=> isset($esar[$que->sn]['alias'])?$esar[$que->sn]['alias']:'',
                        'datelog'		=> date('d-m-Y', strtotime($que->checktime)),
                        'timelog'		=> date('H:i:s', strtotime($que->checktime)),
                        'functionkey'	=> $que->checktype,
                        'description'	=> $desc,
                        'verifymode'	=> $que->verifycode,
                        'edited'		=> $que->editdate=='0000-00-00 00:00:00'? null : $que->editdate,
                        'editby'		=> $que->editby
                    );
                }
                $dataallaye = array(
                    'userid'   	=> $queqe['userid'],
                    'empTitle' 	=> $queqe['empTitle'],
                    'empID' 	=> $queqe['empID'],
                    'empHire'	=> $queqe['empHire'],
                    'empName' 	=> $queqe['empName'],
                    'deptName' 	=> $queqe['deptName']
                );
                $data = array(
                    "index" => $abc,
                    "cominfo" => $company,
                    "empinfo" => $dataallaye,
                    "data" => $dataarray
                );
                $abc++;

                if($excelid==1) {
                    $dataview = $this->load->view("lapsemua",$data,true);
                    $datavw = $datavw.$dataview;
                } else {
                    $this->load->view("lapsemua",$data);
                }
            }
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporantransaksi.xls");
                echo "$datavw";
            }
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */