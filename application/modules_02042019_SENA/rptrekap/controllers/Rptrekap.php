<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rptrekap extends MX_Controller {

    private $aAkses;
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
        $this->aAkses = akses("Rptrekap", $this->session->userdata('s_access'));
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
        $this_url = site_url('rptrekap/pagging/');
        $data2 = $this->mypagination->getPagination(0,10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = 0;
        $data['result'] = array();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();
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
        $jnspeg = $this->input->post('jnspeg');

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

            $SQLcari .= " and jftstatus in (".implode(',', $s).") ";
        }

        if(!empty($jnspeg)) {
            $s = array();
            foreach($jnspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and jenispegawai in (".implode(',', $s).") ";
        }

        $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
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
        $jnspil = $this->input->post('jnslap');

        switch ($jnspil) {
            case  2:
                $this->view2(1);
                break;
            case 3:
                $this->view2(2);
                break;
            default:
                $this->view1();
                break;
        }
    }

    //Laporan standard
    public function view1()
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
        $ispdf = $this->input->post('pdf');

        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);

        $ttds = $this->input->post('ttd');
        $ttd = explode("|",$ttds);

        if($orgid!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(convertToArray($this->session->userdata('s_area'))):array();

        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $att = $this->report_model->getattAktif();
        foreach($att->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname
            );
        }
        //$atar["AT_"] = "-";

        $abs = $this->report_model->getabsAktif();
        foreach($abs->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname
            );
        }

        //$bbar["AT_"] = "-";

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
        if($userid!='undefined') {
            $userar = explode(",",$this->input->post('uid'));
            $queryemp = $this->report_model->getuseremployeedetails($userar,$stspeg,$jnspeg);
            $dpt = $this->input->post('org');
            $yo=2;
        } else if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetails($orgid,$stspeg,$jnspeg);
            $dpt = $this->input->post('org');
            $yo=1;
        }
        if($selall=='true') {
            $queryemp = $this->report_model->getallemployeedetails($stspeg,$jnspeg);
            $yo=3;
        }

        //print_r($this->db->last_query());
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
                $this->db->reset_query();
                $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
                $totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
                $holonnwds=0;$totalholiday  = 0;$totalHolWork=0;
                $totalUpacara=0;
                $jmlUpacaraYa=0;
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
                        if (isset($aten[$queqe['userid']][$que->attendance]))
                            $aten[$queqe['userid']][$que->attendance]++;
                    } else if ((isset($que->check_in) || isset($que->check_out )) &&
                        $que->workinholiday!=2 && $que->workinholiday!=1 &&!array_key_exists($que->attendance, $atar)  &&
                        !array_key_exists($que->attendance, $bbar)
                    ) {
                        $attendance++;
                    }

                    if(isset($bbar[$que->attendance])) {
                        $absence++;
                        if (isset($aben[$queqe['userid']][$que->attendance]))
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

                    if($que->attendance=='' &&$que->workinholiday==2) {
                        $off++;
                    }

                    if($que->attendance=='ALP') {
                        if($que->workinholiday!=1) $alpha++;
                    }

                    /*if($que->attendance=='AB_12') {
                        $alpha++;
                    }*/

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

                    if(isset($holar[$que->date_shift]) && $que->workinholiday==1 && empty($que->attendance))
                        $totalHolWork++;

                    if (isset($que->date_shift2)){
                        if (isset($que->date_in2)){
                            $jmlUpacaraYa++;
                        }
                        $totalUpacara++;
                    }
                }

                $totalbgt = $total - $off - $alpha - $absence;
                $ttlworkingday = $total;
                $workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
                $totalabsent = $alpha + $absence;
                $ttlworkday = $total - $totalHolWork - $off; //$ttlworkingday!=0? $ttlworkingday- $totalholiday-$off:0;
                $ttlOff= $off!=0?$off + $totalholiday - $holonnwds:0;

                $dataarray[] = array (
                    'userid'		=> $queqe['userid'],
                    'badgeNumber'	=> $queqe['empID'],
                    'eselon'		=> $queqe['empEselon'],
                    'golru'			=> $queqe['empGolru'],
                    'name'			=> $queqe['empName'],
                    'workingday'	=> $ttlworkingday!=0 ? $ttlworkingday - ($totalholiday - $holonnwds) - $off:0,
                    'workday'       => $ttlworkday,//$workday!=0?$workday:0,
                    'off'			=> $ttlOff,
                    'attendance'	=> $attendance!=0?$attendance:0,
                    'aten'			=> $aten,
                    'absence'		=> $absence!=0?$absence:0,
                    'aben'			=> $aben,
                    'absent'		=> $alpha!=0?$alpha:'-',
                    'totalabsent'   => $totalabsent!=0?$totalabsent:0,
                    'late'			=> $totallate!=0?$totallate:'-',
                    'early'			=> $totalearly!=0?$totalearly:'-',
                    'OT'			=> $totalot!=0?$totalot:'-',
                    'workinholiday'	=> $workinholiday!=0?$workinholiday:'-',
                    'editcome'      => $editcome!=''?$editcome:'-',
                    'edithome'      => $edithome!=''?$edithome:'-',
                    'totalUpacara'  =>  $totalUpacara,
                    'jmlUpacaraYa'  =>  $jmlUpacaraYa
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
                $this->db->reset_query();
                $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
                $totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
                $holonnwds=0;$totalholiday  = 0; $totalHolWork=0;
                $totalUpacara=0; $jmlUpacaraYa=0;
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

                    /*if($que->attendance=='AB_12') {
                        $alpha++;
                    }*/

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

                    if(isset($holar[$que->date_shift]) && $que->workinholiday==1 && empty($que->attendance))
                        $totalHolWork++;

                    if (isset($que->date_shift2)){
                        if (isset($que->date_in2)){
                            $jmlUpacaraYa++;
                        }
                        $totalUpacara++;
                    }
                }

                $totalbgt = $total - $off - $alpha - $absence;
                $ttlworkingday = $total;
                $workday = $total - $totalHolWork - $off;//$ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
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
                    'edithome'      => $edithome!=''?$edithome:'-',
                    'totalUpacara'  =>  $totalUpacara,
                    'jmlUpacaraYa'  =>  $jmlUpacaraYa
                );
            }

            $dataallaye = array(
                'dept'   		=> 'All',
                'holidays' 		=> $totalholiday,
                'datestart' 	=> date('d-M-Y', $datestart),
                'datestop' 		=> date('d-M-Y', $datestop)

            );

        }

        unset($atar["AT_"]);
        unset($bbar["AT_"]);

        $data = array(
            "cominfo" 	=> $company,
            "empinfo" 	=> $dataallaye,
            "att"		=> $attend,
            "abs"		=> $absen,
            "data" 		=> $dataarray,
            'attendance' 		=> $atar,
            'absence' 		=> $bbar,
            "excelid" => $excelid,
            "ttd" => $ttd,
            "pdfid" => $ispdf,
        );

        if($this->input->post('ttd')){
            $tdd_input = explode('|', $this->input->post('ttd'));


            $data['ttd_jabatan']    = $tdd_input[0];
            $data['ttd_nama']       = $tdd_input[1];
            $data['ttd_gol']        = $tdd_input[2];
            $data['ttd_nip']        = $tdd_input[3];
        } else {
            $data['ttd_jabatan']    = '';
            $data['ttd_nama']       = '';
            $data['ttd_gol']        = '';
            $data['ttd_nip']        = '';
        }

        if($selall != 'true') {
            if ($ispdf==1) {
                $this->load->library('mpdf');
                $this->mpdf =
                    new mPDF('',    // mode - default ''
                        'Legal',    // format - A4, for example, default ''
                        0,     // font size - default 0
                        'arial',    // default font family
                        5,    // margin left
                        5,    // margin right
                        5,    // margin top
                        5,    // margin bottom
                        9,     // margin header
                        5,     // margin footer
                        'L');  // L - landscape, P - portrait
                $this->mpdf->simpleTables = true;
                $this->mpdf->packTableData = true;

                //$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $stylepdf= BASEPATH.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
                $stylesheet = file_get_contents( $stylepdf );
                $this->mpdf->WriteHTML($stylesheet,1);

                $datavw = $this->load->view("laporan",$data,true);
                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporanrekapitulasi.xls");
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
                'absence' 		=> $bbar,
                "excelid" => $excelid,
                "pdfid" => $ispdf
            );
            if ($ispdf==1) {
                $this->load->library('mpdf');
                $this->mpdf =
                    new mPDF('',    // mode - default ''
                        'Legal',    // format - A4, for example, default ''
                        0,     // font size - default 0
                        'arial',    // default font family
                        5,    // margin left
                        5,    // margin right
                        5,    // margin top
                        5,    // margin bottom
                        9,     // margin header
                        5,     // margin footer
                        'L');  // L - landscape, P - portrait
                $this->mpdf->simpleTables = true;
                $this->mpdf->packTableData = true;

                //$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $stylepdf= BASEPATH.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
                $stylesheet = file_get_contents( $stylepdf );
                $this->mpdf->WriteHTML($stylesheet,1);



                $datavw = $this->load->view("laporanbyorg",$data,true);
                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporanrekapitulasi.xls");
            }
            $this->load->view("laporanbyorg",$dataOrganization);
        }
    }

    public function viewtemp()
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

        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);

        if($orgid!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(convertToArray($this->session->userdata('s_area'))):array();

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
        if($userid!='undefined') {
            $userar = explode(",",$this->input->post('uid'));
            $queryemp = $this->report_model->getuseremployeedetails($userar,$stspeg,$jnspeg);
            $dpt = $this->input->post('org');
            $yo=2;
        } else if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetails($orgid,$stspeg,$jnspeg);
            $dpt = $this->input->post('org');
            $yo=1;
        }
        if($selall=='true') {
            $queryemp = $this->report_model->getallemployeedetails($stspeg,$jnspeg);
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
                $this->db->reset_query();
                $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
                $totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
                $holonnwds=0;$totalholiday  = 0;$totalHolWork=0;
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

                    /*if($que->attendance=='AB_12') {
                        $alpha++;
                    }*/

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
                    if(isset($holar[$que->date_shift]) && $que->workinholiday==1 && empty($que->attendance))
                        $totalHolWork++;
                }

                $totalbgt = $total - $off - $alpha - $absence;
                $ttlworkingday = $total;
                $workday = $total - $totalHolWork - $off;//$ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
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
                $this->db->reset_query();
                $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
                $totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
                $holonnwds=0;$totalholiday  = 0;$totalHolWork=0;
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

                    /*if($que->attendance=='AB_12') {
                        $alpha++;
                    }*/

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
                    if(isset($holar[$que->date_shift]) && $que->workinholiday==1 && empty($que->attendance))
                        $totalHolWork++;
                }

                $totalbgt = $total - $off - $alpha - $absence;
                $ttlworkingday = $total;
                $workday =$total - $totalHolWork - $off; // $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
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
            'absence' 		=> $bbar,
            "excelid" => $excelid
        );

        if($selall != 'true') {
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporanrekapitulasi.xls");
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
                'absence' 		=> $bbar,
                "excelid" => $excelid
            );
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporanrekapitulasi.xls");
            }
            $this->load->view("laporanbyorg",$dataOrganization);
        }
    }

    //laporan kehadiran
    public function view2($issimple=1)
    {

        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $userid = $this->input->post('uid');

        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $orgid = $this->input->post('org');
        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');

        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);

        $start_date = $postdatestart;
        $end_date = $postdatestop;

        $simple = $issimple;
        $periode = format_date_ind($start_date).' - '.format_date_ind($end_date);
        $arr_days = $this->createDateRangeArray($start_date,$end_date);

        $compa = $this->report_model->getcompany();
        $company = array(
            'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
            'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
            'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
            'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
            'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
            'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
        );

        $with_user_id = '';
        $tambahan = '';

        if($orgid!='undefined')	{
            $orgid = $this->pegawai->deptonall($orgid);
            $this->db->select('deptname');
            $this->db->from('departments');
            $this->db->where('deptid', $this->input->post('org'));
            $query = $this->db->get();
            $namadept = $query->row()->deptname;
        } else {
            $orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('user_dept'))):array();
            $namadept = 'Semua';
        }

        if(!empty($orgid)) {
            foreach($orgid as $org)
                $orga[] = "'".$org."'";
            $orgaidi = implode(',', $orga);
            $with_user_id = " deptid IN (".$orgaidi.") ";
            $tambahan = " deptid IN (".$orgaidi.") ";
        }

        if($userid!='undefined') {
            $useraidi = explode(',',$userid);
            foreach($useraidi as $usr)
                $usernya[] = "'".$usr."'";
            $with_user_id = " userid IN (".implode(',',$usernya).") ";
            $tambahan = " a.userid IN (".implode(',',$usernya).") ";
        }
        $with_jns_pegawai="";
        if ($jnspeg != null)
        {
            $with_jns_pegawai= " and jenispegawai IN (".implode(',',$jnspeg).") ";
        }

        $with_sts_pegawai="";
        if ($stspeg != null)
        {
            $with_sts_pegawai= " and jftstatus IN (".implode(',',$stspeg).") ";
        }

        $sqlcok = "select a.userid, a.date_shift, a.check_in, a.check_out, a.attendance, a.workinholiday, a.late, a.early_departure,
                    c.date_shift as date_shift2,c.date_in as date_in2, c.check_in as check_in2
						from process a 
                        join userinfo b on a.userid=b.userid 
                        left join process_upacara c on a.userid=c.userid and a.date_shift=c.date_shift
						where ".$tambahan." and (a.date_shift >= '".$this->db->escape_str($start_date)."' and 
						a.date_shift <= '".$this->db->escape_str($end_date)."') ".$with_jns_pegawai.$with_sts_pegawai;
        $querycok = $this->db->query($sqlcok);

        if($simple==1) {
            $query_att_perdate = "SELECT userid, name, deptname, title, golru, kelasjabatan
                                    FROM userinfo 
                                    JOIN departments USING(deptid) 
                                    WHERE ".$with_user_id.$with_jns_pegawai.$with_sts_pegawai."
                                    GROUP BY userid, name, deptname, title, eselon, golru, kelasjabatan, userinfo.id, parentid 
                                    ORDER BY golru desc,kelasjabatan desc";
        } else {
            $query_att_perdate = "SELECT userid,badgenumber,name, deptname, title 
                                FROM userinfo 
                                JOIN departments USING(deptid) 
                                WHERE ".$with_user_id.$with_jns_pegawai.$with_sts_pegawai."
                                GROUP BY userid,badgenumber,name, deptname, title, eselon, golru, kelasjabatan 
                                ORDER BY golru desc,kelasjabatan desc ";
        }
        $group_per_date = $this->db->query($query_att_perdate);

        $data = array(
            "cominfo" => $company,
            "periode" => $periode,
            "arr_days" => $arr_days,
            "querycok" => $querycok,
            "group_per_date" => $group_per_date,
            "nama_dept"	=> $namadept,
            "excelid"	=> $excelid,
            'pdfid'=> $ispdf
        );
        if($this->input->post('ttd')){
            $tdd_input = explode('|', $this->input->post('ttd'));


            $data['ttd_jabatan']    = $tdd_input[0];
            $data['ttd_nama']       = $tdd_input[1];
            $data['ttd_gol']        = $tdd_input[2];
            $data['ttd_nip']        = $tdd_input[3];
        } else {
            $data['ttd_jabatan']    = '';
            $data['ttd_nama']       = '';
            $data['ttd_gol']        = '';
            $data['ttd_nip']        = '';
        }
        if($simple==1) {
            if($this->input->post('ttd')){
                $tdd_input = explode('|', $this->input->post('ttd'));


                $data['ttd_jabatan']    = $tdd_input[0];
                $data['ttd_nama']       = $tdd_input[1];
                $data['ttd_gol']        = $tdd_input[2];
                $data['ttd_nip']        = $tdd_input[3];
            } else {
                $data['ttd_jabatan']    = '';
                $data['ttd_nama']       = '';
                $data['ttd_gol']        = '';
                $data['ttd_nip']        = '';
            }
            if ($ispdf==1) {
                $this->load->library('mpdf');
                $this->mpdf =
                    new mPDF('',    // mode - default ''
                        'Legal',    // format - A4, for example, default ''
                        0,     // font size - default 0
                        'arial',    // default font family
                        5,    // margin left
                        5,    // margin right
                        5,    // margin top
                        5,    // margin bottom
                        9,     // margin header
                        5,     // margin footer
                        'L');  // L - landscape, P - portrait
                $this->mpdf->simpleTables = true;
                $this->mpdf->packTableData = true;

                //$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $stylepdf= BASEPATH.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
                $stylesheet = file_get_contents( $stylepdf );
                $this->mpdf->WriteHTML($stylesheet,1);

                $datavw = $this->load->view("lapsimple",$data,true);
                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=rekapultasikehadiran.xls");
            }
            $this->load->view("lapsimple",$data);
        } else {

            if ($ispdf==1) {
                $this->load->library('mpdf');
                $this->mpdf =
                    new mPDF('',    // mode - default ''
                        'A0',    // format - A4, for example, default ''
                        0,     // font size - default 0
                        'arial',    // default font family
                        5,    // margin left
                        5,    // margin right
                        5,    // margin top
                        5,    // margin bottom
                        9,     // margin header
                        5,     // margin footer
                        'L');  // L - landscape, P - portrait
                $this->mpdf->simpleTables = true;
                $this->mpdf->packTableData = true;

                //$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $stylepdf= BASEPATH.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
                $stylesheet = file_get_contents( $stylepdf );
                $this->mpdf->WriteHTML($stylesheet,1);

                $datavw = $this->load->view("lapfull",$data,true);
                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=rekapultasikehadiran.xls");
            }
            $this->load->view("lapfull",$data);

        }
    }

    private function createDateRangeArray($strDateFrom,$strDateTo)
    {
        $aryRange=array();
        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom) {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry

            while ($iDateFrom<$iDateTo) {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }
}

