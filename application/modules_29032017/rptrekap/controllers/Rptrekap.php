<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rptrekap extends MX_Controller {

	function Rptrekap(){
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
        //echo $this->db->last_query();
        $this_url = site_url('rptrekap/pagging/');
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
        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $selall = "false";//$this->input->get('selall');
        $userid = $this->input->post('uid');
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $orgid = $this->input->post('org');
        $excelid = $this->input->post('xls');

        if($orgid!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(explode(',', $this->session->userdata('s_area'))):array();

        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $att = $this->report_model->getatt();
        foreach($att->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname
            );
        }
        $abs = $this->report_model->getabs();
        foreach($abs->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname
            );
        }

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
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

        if($selall == 'true'){
            $deptGroupid = $this->report_model->getdepart();
        }

        $yo = 0;
        $dpt = 1;
        if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetails($orgid,$stspeg);
            $dpt = $this->input->post('org');
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


        $holiday = $this->utils->cekholiday($datestart, $datestop);
        $holarray = array();
        foreach($holiday->result() as $hol) {
            $tglmulai = strtotime($hol->startdate);
            $tglselesai = strtotime($hol->enddate);
            $selisih = $tglselesai - $tglmulai;
            if($selisih==0) {
                $holar[$hol->startdate] = $hol->info;
            } else {
                $jarak = $selisih / 86400;
                for($k=0;$k<=$jarak;$k++) {
                    $holar[date('Y-m-d',strtotime($hol->startdate) + ($k*86400))] = $hol->info;
                }
            }
        }

        $dataallay = array();
        $dataallu = array();
        $aten = array();
        $aben = array();
        foreach($queryemp->result() as $queq) {
            $dataallay[] = array(
                'userid'   => $queq->userid,
                'empTitle' => $queq->title,
                'empID' => $queq->badgenumber,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'empEselon' => $queq->eselon,
                'empGolru' => $queq->golru,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''
            );
            $dataallu[$queq->userid] = array(
                'empTitle' => $queq->title,
                'empID' => $queq->badgenumber,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'empEselon' => $queq->eselon,
                'empGolru' => $queq->golru,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''
            );
            foreach($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
        }

        $dataallaye = array();
        $dataarray = array();

        if($selall != 'true'){
            foreach($dataallay as $queqe) {
                $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
                $totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
                $holonnwds=0;$totalholiday  = 0;
                foreach($querytemp->result() as $que) {
                    $total++;
                    if($que->late!=0) {
                        if($que->workinholiday!=1 || $que->workinholiday!=2) {
                            $totallate++;
                        }
                    }

                    if($que->early_departure!=0) {
                        $totalearly++;
                    }

                    if($que->ot_before!=0 || $que->ot_after!=0) {
                        $totalot++;
                    }

                    if($que->workinholiday==1)
                        $totalholiday++;

                    if($que->workinholiday==1 || $que->workinholiday==2) {
                        $adashift = $this->report_model->checkinout($que->date_shift, $queqe['userid']);
                        if($adashift->total>0)
                            $workinholiday++;
                    }

                    if(isset($atar[$que->attendance])) {
                        $attendance++;
                        $aten[$queqe['userid']][$que->attendance]++;
                    }

                    if(isset($bbar[$que->attendance])) {
                        $absence++;
                        $aben[$queqe['userid']][$que->attendance]++;
                    }

                    if($que->attendance=='NWDS') {
                        $off++;
                    }

                    if($que->attendance=='NWK') {
                        $off++;
                    }

                    if($que->attendance=='BLNK') {
                        $off++;
                    }

                    if($que->attendance=='ALP') {
                        if($que->workinholiday!=1) $alpha++;
                    }

                    if($que->attendance=='OT') {
                        $totalot++;
                    }

                    if(!empty($que->edit_come) ) {
                        $editcome++;
                    }

                    if(!empty($que->edit_home)) {
                        $edithome++;
                    }
                    if(isset($holar[$que->date_shift]) && ($que->attendance=='NWDS' || $que->attendance=='NWK'))
                        $holonnwds++;
                }

                $totalbgt = $total - $off - $alpha - $absence;
                $ttlworkingday = $total;
                $workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
                $totalabsent = $alpha + $absence;
                $dataarray[] = array (
                    'userid'		=> $queqe['userid'],
                    'badgeNumber'	=> $queqe['empID'],
                    'eselon'		=> $queqe['empEselon'],
                    'golru'			=> $queqe['empGolru'],
                    'name'			=> $queqe['empName'],
                    'workingday'	=> $ttlworkingday!=0?$ttlworkingday - $totalholiday + $holonnwds - $off:'0',
                    'workday'       => $workday!=0?$workday:'0',
                    'off'			=> $off!=0?$off + $totalholiday - $holonnwds:'-',
                    'attendance'	=> $attendance!=0?$attendance:'0',
                    'aten'			=> $aten,
                    'absence'		=> $absence!=0?$absence:'0',
                    'aben'			=> $aben,
                    'absent'		=> $alpha!=0?$alpha:'-',
                    'totalabsent'   => $totalabsent!=0?$totalabsent:'0',
                    'late'			=> $totallate!=0?$totallate:'-',
                    'early'			=> $totalearly!=0?$totalearly:'-',
                    'OT'			=> $totalot!=0?$totalot:'-',
                    'workinholiday'	=> $workinholiday!=0?$workinholiday:'-',
                    'editcome'      => $editcome!=''?$editcome:'-',
                    'edithome'      => $edithome!=''?$edithome:'-'
                );
            }
            $dataallaye = array(
                'dept'   		=> isset($deptar[$dpt])?$deptar[$dpt]:'',
                'holidays' 		=> $totalholiday,
                'datestart' 	=> date('Y-m-d', $datestart),
                'datestop' 		=> date('Y-m-d', $datestop)
            );
        } else
            {
            foreach($dataallay as $queqe) {
                $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
                $totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
                $holonnwds=0;$totalholiday  = 0;
                foreach($querytemp->result() as $que) {
                    $total++;
                    if($que->late!=0) {
                        if($que->workinholiday!=1 || $que->workinholiday!=2) {
                            $totallate++;
                        }
                    }

                    if($que->early_departure!=0) {
                        $totalearly++;
                    }

                    if($que->ot_before!=0 || $que->ot_after!=0) {
                        $totalot++;
                    }
                    if($que->workinholiday==1)
                        $totalholiday++;

                    if($que->workinholiday==1 || $que->workinholiday==2) {
                        $adashift = $this->report_model->checkinout($que->date_shift, $queqe['userid']);
                        if($adashift->total>0)
                            $workinholiday++;
                    }

                    if(isset($atar[$que->attendance])) {
                        $attendance++;
                        $aten[$queqe['userid']][$que->attendance]++;
                    }

                    if(isset($bbar[$que->attendance])) {
                        $absence++;
                        $aben[$queqe['userid']][$que->attendance]++;

                    }

                    if($que->attendance=='NWDS') {
                        $off++;
                    }

                    if($que->attendance=='NWK') {
                        $off++;
                    }

                    if($que->attendance=='BLNK') {
                        $off++;
                    }

                    if($que->attendance=='ALP') {
                        if($que->workinholiday!=1) $alpha++;
                    }

                    if($que->attendance=='OT') {
                        $totalot++;
                    }

                    if(!empty($que->edit_come) ) {
                        $editcome++;

                    }

                    if(!empty($que->edit_home)) {
                        $edithome++;
                    }
                    if(isset($holar[$que->date_shift])  && ($que->attendance=='NWDS' || $que->attendance=='NWK'))
                        $holonnwds++;
                }

                $totalbgt = $total - $off - $alpha - $absence;
                $ttlworkingday = $total;
                $workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
                $totalabsent = $alpha + $absence;

                $dataarray[] = array (
                    'userid'		=> $queqe['userid'],
                    'badgeNumber'	=> $queqe['empID'],
                    'eselon'		=> $queqe['empEselon'],
                    'golru'			=> $queqe['empGolru'],
                    'name'			=> $queqe['empName'],
                    'DeptID'        => $queqe['deptName'],
                    'workingday'	=> $ttlworkingday!=0?$ttlworkingday - $totalholiday + $holonnwds - $off:'0',
                    'workday'       => $workday!=0?$workday:'0',
                    'off'			=> $off!=0?$off + $totalholiday - $holonnwds:'-',
                    'attendance'	=> $attendance!=0?$attendance:'0',
                    'aten'			=> $aten,
                    'absence'		=> $absence!=0?$absence:'0',
                    'aben'			=> $aben,
                    'absent'		=> $alpha!=0?$alpha:'-',
                    'totalabsent'   => $totalabsent!=0?$totalabsent:'0',
                    'late'			=> $totallate!=0?$totallate:'-',
                    'early'			=> $totalearly!=0?$totalearly:'-',
                    'OT'			=> $totalot!=0?$totalot:'-',
                    'workinholiday'	=> $workinholiday!=0?$workinholiday:'-',
                    'editcome'      => $editcome!=''?$editcome:'-',
                    'edithome'      => $edithome!=''?$edithome:'-'
                );
            }

            $dataallaye = array(
                'dept'   		=> 'All',
                'holidays' 		=> $totalholiday,
                'datestart' 	=> date('d-M-Y', $datestart),
                'datestop' 		=> date('d-M-Y', $datestop)

            );

        }

        $data = array(
            "cominfo" 	=> $company,
            "empinfo" 	=> $dataallaye,
            "att"		=> $attend,
            "abs"		=> $absen,
            "data" 		=> $dataarray,
            'attendance' 		=> $atar,
            'absence' 		=> $bbar
        );

        if($selall != 'true') {
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=recapitulationreport.xls");
            }
            $this->load->view("laporan",$data);
        } else {
            $dataOrganization = array(
                "cominfo"        => $company,
                "empinfo"        => $dataallaye,
                "att"            => $attend,
                "abs"            => $absen,
                "companyLooping" => $deptGroupid,
                "data"           => $dataarray,
                'attendance' 		=> $atar,
                'absence' 		=> $bbar
            );
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=recapitulationreport.xls");
            }
            $this->load->view("laporanbyorg",$dataOrganization);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */