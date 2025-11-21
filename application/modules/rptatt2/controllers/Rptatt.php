<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rptatt extends MX_Controller {
    private $aAkses;
	function Rptatt(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }

        $this->load->model('utils_model','utils');
        $this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('report_model');

        $this->aAkses = akses("Rptatt", $this->session->userdata('s_access'));

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

        if(!empty($jnspeg)) {
            $s = array();
            foreach($jnspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and jenispegawai in (".implode(',', $s).") ";
        }
        if(!empty($stspeg)) {
            $s = array();
            foreach($stspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and jftstatus in (".implode(',', $s).") ";
        }

        $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('rptatt/pagging/');
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
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $orgid = $this->input->post('org');
        $pilrpt = $this->input->post('jnslap');

        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);


        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $selall = 'false';//$this->input->post('selall');
        $userid = $this->input->post('uid');
        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');


        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        if ($orgid != 'undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept') != '' ? $this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))) : array();

        $areaid = $this->session->userdata('s_area') != '' ? $this->pegawai->areaonall(convertToArray($this->session->userdata('s_area'))) : array();

        $tbar = array();
        $bbar = array();
        $holar = array();

        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0;
        $totalholiday = 0;
        $totalnonworkingday = 0;
        $holonnwds = 0;

        $attrecap = $this->report_model->getatt();
        foreach ($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array('atid' => $at->atid, 'atname' => $at->atname);
        }

        $absrecap = $this->report_model->getabs();
        foreach ($absrecap->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array('abid' => $bs->abid, 'abname' => $bs->abname);
        }

        $holiday = $this->utils->cekholiday($datestart, $datestop);
        $holarray = array();

        foreach ($holiday->result() as $hol) {
            $tglmulai = strtotime($hol->startdate);
            $tglselesai = strtotime($hol->enddate);
            $selisih = $tglselesai - $tglmulai;
            if ($selisih == 0) {
                $holar[$hol->startdate] = $hol->info;
            } else {
                $jarak = $selisih / 86400;
                for ($k = 0; $k <= $jarak; $k++) {
                    $holar[date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->info;
                }
            }
        }

        $this->db->reset_query();
        $att = $this->report_model->getatt();
        foreach ($att->result() as $tt) {
            $tbar[$tt->atid] = $tt->atname;
        }
        $dept = $this->report_model->getdept();
        foreach ($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $abs = $this->report_model->getabs();
        foreach ($abs->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
        }

        $yo = 0;
         if ($userid != 'undefined') {
            $userar = explode(",",$this->input->post('uid'));
            $queryemp = $this->report_model->getuseremployeedetails($userar,$stspeg,$jnspeg);
            $yo = 2;
            }
     else if ($this->input->post('org') != 'undefined') {
            $queryemp = $this->report_model->getorgemployeedetails($orgid,$stspeg,$jnspeg);
            $yo = 1;
        }
        if ($selall == 'true') {
            $queryemp = $this->report_model->getallemployeedetails($stspeg,$jnspeg);
            $yo = 3;
        }
        //print_r($this->db->last_query());
        $aten = array();
        $aben = array();

        $dataallay = array();
        $dataallu = array();

        foreach ($queryemp->result() as $queq) {
            $dataallay[] = array('userid' => $queq->userid, 'empTitle' => $queq->title, 'empID' => $queq->badgenumber, 'empHire' => isset($queq->hireddate) ? date('d-m-Y', strtotime($queq->hireddate)) : '', 'empName' => $queq->name, 'deptName' => isset($deptar[$queq->deptid]) ? $deptar[$queq->deptid] : '');
            $dataallu[$queq->userid] = array('empTitle' => $queq->title, 'empID' => $queq->badgenumber, 'empHire' => isset($queq->hireddate) ? date('d-m-Y', strtotime($queq->hireddate)) : '', 'empName' => $queq->name, 'deptName' => isset($deptar[$queq->deptid]) ? $deptar[$queq->deptid] : '');
            foreach ($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach ($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
        }
        $compa = $this->report_model->getcompany();
        $company = array('companyname' => isset($compa->row()->companyname) ? $compa->row()->companyname : '', 'logo' => isset($compa->row()->logo) ? $compa->row()->logo : '', 'address1' => isset($compa->row()->address1) ? $compa->row()->address1 : '', 'address2' => isset($compa->row()->address2) ? $compa->row()->address2 : '', 'phone' => isset($compa->row()->phone) ? $compa->row()->phone : '', 'fax' => isset($compa->row()->fax) ? $compa->row()->fax : '');
        //echo $yo;
        if ($pilrpt == '1') {
            $dataallaye = array();
            $abc = 0;
            $dataview = '';
            $datavw = '';
            $range = ($datestop - $datestart) / 86400;
            for ($x = 0; $x <= $range; $x++) {
                $totalholiday = 0;
                $tanggal = date('Y-m-d', $datestart + ($x * 86400));
                $day = date('D', strtotime($tanggal));
                if ($yo == 1) {
                    $querytemp = $this->report_model->getattbydateorg($areaid, $tanggal, $orgid,$stspeg,$jnspeg);
                } else if ($yo == 2) {
                    $querytemp = $this->report_model->getattbydateuser($tanggal, $userar,$stspeg,$jnspeg);
                } else if ($yo == 3) {
                    $querytemp = $this->report_model->getattbydate($areaid, $tanggal,$stspeg,$jnspeg);
                }

                $dataarray = array();
                foreach ($querytemp->result() as $que) {
                    $in = strtotime($que->date_in . ' ' . $que->check_in);
                    $out = strtotime($que->date_out . ' ' . $que->check_out);
                    $bout = strtotime($que->date_in . ' ' . $que->break_out);
                    $bin = strtotime($que->date_in . ' ' . $que->break_in);

                    if (!isset($que->break_out) || !isset($que->break_in)) {
                        $btot = 0;
                    } else {
                        $btot = $bin - $bout;
                    }

                    $tot = ($out - $in) - $btot;
                    $totalhour = $this->report_model->itungan($tot);

                    $date_in = null;
                    $check_in = null;
                    $break_out = null;
                    $break_in = null;
                    $date_out = null;
                    $check_out = null;
                    if (isset($que->date_in)) {
                        $date_in = date('d-m-Y', strtotime($que->date_in));
                    } else {
                        $totalhour = '';
                    }
                    if (isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in . ' ' . $que->check_in));
                    if (isset($que->break_out)) $break_out = date('H:i:s', strtotime($date_in . ' ' . $que->break_out));
                    if (isset($que->break_in)) $break_in = date('H:i:s', strtotime($date_in . ' ' . $que->break_in));
                    if (isset($que->date_out)) {
                        $date_out = date('d-m-Y', strtotime($que->date_out));
                    } else {
                        $totalhour = '';
                    }
                    if (isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out . ' ' . $que->check_out));

                    if ($que->late != 0) {
                        $late = $this->report_model->itungan($que->late);
                    } else {
                        $late = '';
                    }

                    if ($que->early_departure != 0) {
                        $earlydept = $this->report_model->itungan($que->early_departure);
                    } else {
                        $earlydept = '';
                    }

                    if ($que->ot_before != 0) {
                        $otbef = $this->report_model->itungan($que->ot_before);
                    } else {
                        $otbef = '';
                    }

                    if ($que->ot_after != 0) {
                        $otaf = $this->report_model->itungan($que->ot_after);
                    } else {
                        $otaf = '';
                    }

                    $workinghour = date('H:i', strtotime($que->shift_in)) . ' - ' . date('H:i', strtotime($que->shift_out));

                    if ($que->workinholiday == 1) {
                        //$activity = "Bekerja";
                        $activity = "Libur";
                        $notes = $holar[$que->date_shift];
                        $totalholiday++;
                        $late = '';
                        $early = '';

                        if ($que->attendance == 'OFF') {
                            $activity = "Libur";
                            $totalhour = '';
                            $workinghour = '';
                        }

                        if ($que->attendance == 'BLNK') {
                            $activity = '';
                            $totalhour = '';
                            $workinghour = '';
                        }

                        if ($que->attendance == 'ALP') {
                            $activity = "Libur";
                            $totalhour = '';
                        }

                        if ($que->attendance == 'NWDS') {
                            $activity = "";
                            $totalhour = '';
                        }

                        if ($que->attendance == 'NWK') {
                            $activity = "Libur";
                            $totalhour = '';
                        }

                        if ($que->attendance == 'OT') {
                            $activity = "Lembur";
                            $otaf = $totalhour;
                        }

                        if (isset($tbar[$que->attendance])) {
                            $activity = "Bekerja";
                            //$notes = $que->notes != '' ? $que->notes : $tbar[$que->attendance];
                            $notes = $tbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        } else if (isset($bbar[$que->attendance])) {
                            $activity = 'Tidak Bekerja';
                            //$notes = $que->notes != '' ? $que->notes : $bbar[$que->attendance];
                            $notes = $bbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        }
                    } else if ($que->workinholiday == 2) {
                        $activity = "Bekerja";
                        $notes = 'Bukan Hari Kerja';
                        $late = '';
                        $early = '';

                        if ($que->attendance == 'OFF') {
                            $activity = 'Libur';
                            $totalhour = '';
                            $workinghour = '';
                        }
                        if ($que->attendance == 'BLNK') {
                            $activity = '';
                            $totalhour = '';
                            $workinghour = '';
                        }
                        if ($que->attendance == 'ALP') {
                            $activity = '';
                            $totalhour = '';
                        }

                        if ($que->attendance == 'NWK') {
                            $activity = '';
                            $totalhour = '';
                        }

                        if ($que->attendance == 'NWDS') {
                            $activity = '';
                            $totalhour = '';
                            $late = '';
                            $earlydept = '';
                        }

                        if ($que->attendance == 'OT') {
                            $activity = "Lembur";
                            $otaf = $totalhour;
                        }

                        if (isset($tbar[$que->attendance])) {
                            $activity = "Bekerja";
                            //$notes = $que->notes != '' ? $que->notes : $tbar[$que->attendance];
                            $notes = $tbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        } else if (isset($bbar[$que->attendance])) {
                            $activity = 'Tidak Bekerja';
                            //$notes = $que->notes != '' ? $que->notes : $bbar[$que->attendance];
                            $notes = $bbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        }
                        if ($que->attendance == '') {
                            $activity = "Libur";
                        }

                    } else {
                        $notes = '';
                        if (isset($tbar[$que->attendance])) {
                            $activity ="Bekerja";
                            //$notes = $que->notes != '' ? $que->notes : $tbar[$que->attendance];
                            $notes = $tbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        } else if (isset($bbar[$que->attendance])) {
                            $activity = 'Tidak Bekerja';
                            //$notes = $que->notes != '' ? $que->notes : $bbar[$que->attendance];
                            $notes = $bbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);

                            $date_in = '';
                            $check_in = '';
                            $break_out = '';
                            $break_in = '';
                            $date_out = '';
                            $check_out = '';
                            $late = '';
                            $earlydept = '';
                            $otbef = '';
                            $otaf = '';
                            $totalhour = '';
                        } else {
                            $activity = "Bekerja";
                        }

                        if ($que->attendance == 'OFF') {
                            $activity = "Libur";
                            $totalhour = '';
                            $workinghour = '';
                        }

                        if ($que->attendance == 'BLNK') {
                            $activity = '';
                            $totalhour = '';
                            $workinghour = '';
                        }

                        if ($que->attendance == 'NWDS') {
                            $activity = "";
                            $totalhour = '';
                        }

                        if ($que->attendance == 'NWK') {
                            $activity = "Libur";
                            $totalhour = '';
                        }

                        if ($que->attendance == 'ALP') {
                            $activity = "Alpa";
                            $totalhour = '';
                        }

                        if ($que->attendance == 'OT') {
                            $activity = "Lembur";
                            $otaf = $totalhour;
                        }
                    }

                    $dataarray[] = array(
                        'userid' => $que->userid,
                        'badgeNumber' => $que->badgenumber,
                        'name' => $que->name,
                        'dept' => $deptar[$que->deptid],
                        'workinghour' => $workinghour,
                        'activity' => $activity,
                        'datein' => $date_in,
                        'dutyon' => $check_in,
                        'breakout' => $break_out,
                        'breakin' => $break_in,
                        'dateout' => $date_out,
                        'dutyoff' => $check_out,
                        'latein' => $late,
                        'earlydept' => $earlydept,
                        'otbef' => $otbef,
                        'otaf' => $otaf,
                        'totalhour' => $totalhour,
                        'notes' => $notes);
                }
                $dataallaye = array('day' => $day, 'date' => date('Y-m-d', strtotime($tanggal)));

                $data = array("dateinfo" => $this->input->get('dateinfo'),
                    "index" => $abc,
                    "cominfo" => $company,
                    "empinfo" => $dataallaye,
                    "data" => $dataarray,
                    "excelid" => $excelid,
                    "pdfid" => $ispdf,
                    "lastPage" =>0
                    );
                $abc++;
                if ($excelid == 1) {
                    $dataview = $this->load->view("laporanbydate", $data, true);
                    $datavw = $datavw . $dataview;
                } else {
                    $this->load->view("laporanbydate", $data);
                }
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

                $stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $this->mpdf->WriteHTML($stylesheet,1);

                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }
            if ($excelid == 1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporankehadiran.xls");
                echo "$datavw";
            }
        } else {
            $dataallaye = array();
            $datafoot = array();
            $abc = 0;
            $dataview = '';
            $datavw = '';
            foreach ($dataallay as $queqe) {
                $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
                $dataarray = array();
                $totallate = 0;
                $totalearly = 0;
                $totalotbef = 0;
                $totalotaf = 0;
                $total = 0;
                $totallater = 0;
                $totalearlyr = 0;
                $totalotr = 0;
                $totalr = 0;
                $workinholiday = 0;
                $attendance = 0;
                $absence = 0;
                $off = 0;
                $alpha = 0;
                $editcome = 0;
                $edithome = 0;
                $workday = 0;
                $totalholiday = 0;
                foreach ($querytemp->result() as $que) {
                    $totalr++;
                    if ($que->late != 0) {
                        $totallater++;
                    }

                    if ($que->early_departure != 0) {
                        $totalearlyr++;
                    }

                    if ($que->ot_before != 0 || $que->ot_after != 0) {
                        $totalotr++;
                    }

                    if ($que->workinholiday == 1 || $que->workinholiday == 2) {
                        $adashift = $this->report_model->checkinout($que->date_shift, $queqe['userid']);
                        if ($adashift->total > 0) $workinholiday++;
                    }

                    if (isset($atar[$que->attendance])) {
                        $attendance++;
                        $aten[$queqe['userid']][$que->attendance]++;
                    }

                    if (isset($bbar[$que->attendance])) {
                        $absence++;
                        $aben[$queqe['userid']][$que->attendance]++;
                    }

                    if ($que->attendance == 'NWDS') {
                        $off++;
                    }

                    if ($que->attendance == 'NWK') {
                        $off++;
                    }

                    if ($que->attendance == 'BLNK') {
                        $off++;
                    }

                    if ($que->attendance == 'ALP') {
                        if ($que->workinholiday != 1) $alpha++;
                    }

                    if ($que->attendance == 'OT') {
                        $totalotr++;
                    }

                    if (!empty($que->edit_come)) {
                        $editcome++;
                    }

                    if (!empty($que->edit_home)) {
                        $edithome++;
                    }

                    $day = date('D', strtotime($que->date_shift));

                    $in = strtotime($que->date_in . ' ' . $que->check_in);
                    $out = strtotime($que->date_out . ' ' . $que->check_out);
                    $bout = strtotime($que->date_in . ' ' . $que->break_out);
                    $bin = strtotime($que->date_in . ' ' . $que->break_in);

                    if (!isset($que->break_out) || !isset($que->break_in)) {
                        $btot = 0;
                    } else {
                        $btot = $bin - $bout;
                    }

                    $tot = ($out - $in) - $btot;
                    $totalhour = $this->report_model->itungan($tot);

                    $date_in = null;
                    $check_in = null;
                    $break_out = null;
                    $break_in = null;
                    $date_out = null;
                    $check_out = null;
                    if (isset($que->date_in)) {
                        $date_in = date('d-m-Y', strtotime($que->date_in));
                    } else {
                        $totalhour = '';
                        $tot = 0;
                    }
                    if (isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in . ' ' . $que->check_in));
                    if (isset($que->break_out)) $break_out = date('H:i:s', strtotime($date_in . ' ' . $que->break_out));
                    if (isset($que->break_in)) $break_in = date('H:i:s', strtotime($date_in . ' ' . $que->break_in));
                    if (isset($que->date_out)) {
                        $date_out = date('d-m-Y', strtotime($que->date_out));
                    } else {
                        $totalhour = '';
                        $tot = 0;
                    }
                    if (isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out . ' ' . $que->check_out));

                    if ($que->late != 0) {
                        $late = $this->report_model->itungan($que->late);
                        $totallate = $totallate + $que->late;


                    } else {
                        $late = '';
                    }

                    if ($que->early_departure != 0) {
                        $earlydept = $this->report_model->itungan($que->early_departure);
                        $totalearly = $totalearly + $que->early_departure;
                    } else {
                        $earlydept = '';
                    }

                    if ($que->ot_before != 0) {
                        $otbef = $this->report_model->itungan($que->ot_before);
                        $totalotbef = $totalotbef + $que->ot_before;
                    } else {
                        $otbef = '';
                    }

                    if ($que->ot_after != 0) {
                        $otaf = $this->report_model->itungan($que->ot_after);
                        $totalotaf = $totalotaf + $que->ot_after;
                    } else {
                        $otaf = '';
                    }

                    $workinghour = date('H:i', strtotime($que->shift_in)) . ' - ' . date('H:i', strtotime($que->shift_out));

                    if ($que->workinholiday == 1) {
                        $activity = "Libur";
                        //$activity = "Bekerja";
                        $notes = $holar[$que->date_shift];
                        $totalholiday++;
                        if ($totallate != 0) $totallate = $totallate - $que->late;
                        if ($totallater != 0) $totallater--;
                        $late = '';
                        $early = '';

                        if ($que->attendance == 'OFF') {
                            $activity = "Libur";
                            $totalhour = '';
                            $tot = 0;
                            $workinghour = '';
                        }

                        if ($que->attendance == 'BLNK') {
                            $activity = '';
                            $totalhour = '';
                            $tot = 0;
                            $workinghour = '';
                        }

                        if ($que->attendance == 'NWK') {
                            $activity = "Libur";
                            $totalhour = '';
                        }

                        if ($que->attendance == 'ALP') {
                            //$activity = "Alpha";
                            $totalhour = '';
                            $tot = 0;
                        }

                        if ($que->attendance == 'OT') {
                            $activity = "Lembur";
                            $otaf = $totalhour;
                        }

                        if (isset($tbar[$que->attendance])) {
                            $activity = "Bekerja";
                            //$notes = $que->notes != '' ? $que->notes : $tbar[$que->attendance];
                            $notes = $tbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        } else if (isset($bbar[$que->attendance])) {
                            $activity = 'Tidak Berkerja';
                            //$notes = $que->notes != '' ? $que->notes : $bbar[$que->attendance];
                            $notes = $bbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        }
                    } else if ($que->workinholiday == 2) {
                        $activity = "Bekerja";
                        $notes = "Bukan Hari Kerja";
                        if ($totallate != 0) $totallate = $totallate - $que->late;
                        if ($totallater != 0) $totallater--;
                        $late = '';
                        $early = '';

                        if ($que->attendance == 'OFF') {
                            //$activity = 'Off';
                            $activity = 'Libur';
                            $totalhour = '';
                            $tot = 0;
                            $workinghour = '';
                        }
                        if ($que->attendance == 'BLNK') {
                            $activity = '';
                            $totalhour = '';
                            $tot = 0;
                            $workinghour = '';
                        }

                        if ($que->attendance == 'ALP') {
                            $activity = '';
                            $totalhour = '';
                            $tot = 0;
                        }

                        if ($que->attendance == 'NWK') {
                            //$activity = 'Off';
                            $totalhour = '';
                            $tot = 0;
                        }

                        if ($que->attendance == 'NWDS') {
                            //$activity = 'Off';
                            $late = '';
                            $earlydept = '';
                            $otaf = $totalhour;
                        }

                        if ($que->attendance == 'OT') {
                            $activity = "Lembur";
                            $otaf = $totalhour;
                        }

                        if (isset($tbar[$que->attendance])) {
                            $activity = "Bekerja";
                            //$notes = $que->notes != '' ? $que->notes : $tbar[$que->attendance];
                            $notes = $tbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        } else if (isset($bbar[$que->attendance])) {
                            $activity = 'Tidak Bekerja';
                            //$notes = $que->notes != '' ? $que->notes : $bbar[$que->attendance];
                            $notes = $bbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        }

                        if ($que->attendance == '') {
                            $activity = "Libur";
                        }

                    } else {
                        $notes = '';
                        if (isset($tbar[$que->attendance])) {
                            $activity = "Bekerja";
                            //$notes = $que->notes != '' ? $que->notes : $tbar[$que->attendance];
                            $notes = $tbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
                        } else if (isset($bbar[$que->attendance])) {
                            $activity = 'Tidak Bekerja';
                            //$notes = $que->notes != '' ? $que->notes : $bbar[$que->attendance];
                            $notes = $bbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);

                            $date_in = '';
                            $check_in = '';
                            $break_out = '';
                            $break_in = '';
                            $date_out = '';
                            $check_out = '';
                            $late = '';
                            $earlydept = '';
                            $otbef = '';
                            $otaf = '';
                            $totalhour = '';
                            $tot = 0;
                        } else {
                            $activity = "Bekerja";
                        }

                        if ($que->attendance == 'OFF') {
                            $activity = "Libur";
                            $totalhour = '';
                            $tot = 0;
                            $workinghour = '';
                        }

                        if ($que->attendance == 'BLNK') {
                            $activity = '';
                            $totalhour = '';
                            $tot = 0;
                            $workinghour = '';
                        }

                        if ($que->attendance == 'NWDS') {
                            $activity = "Libur";
                            $totalhour = '';
                            $tot = 0;
                        }

                        if ($que->attendance == 'NWK') {
                            $activity = "Libur";
                            $totalhour = '';
                            $tot = 0;
                        }

                        if ($que->attendance == 'ALP') {
                            $activity = "Alpha";
                            $totalhour = '';
                            //$notes = 'Alpa';
                            $tot = 0;
                        }

                        if ($que->attendance == 'OT') {
                            $activity = "Lembur";
                            $otaf = $totalhour;
                        }
                    }

                    $dataarray[] = array('day' => $day, 'date' => date('d-m-Y', strtotime($que->date_shift)),
                        'workinghour' => $workinghour, 'activity' => $activity,
                        'datein' => $date_in,
                        'dutyon' => $check_in, 'breakout' => $break_out, 'breakin' => $break_in,
                        'dateout' => $date_out, 'dutyoff' => $check_out,
                        'latein' => $late, 'earlydept' => $earlydept,
                        'otbef' => $otbef, 'otaf' => $otaf,
                        'totalhour' => $totalhour, 'notes' => $notes);
                    $total = $total + $tot;
                    if (isset($holar[$que->date_shift]) && ($que->attendance == 'NWDS' || $que->attendance == 'NWK')) $holonnwds++;
                }

                $totalbgt = $totalr - $off - $alpha - $absence;
                $ttlworkingday = $totalr;
                $workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
                $totalabsent = $alpha + $absence;

                $dataallaye = array(
                    'userid' => $queqe['userid'],
                    'empTitle' => $queqe['empTitle'],
                    'empID' => $queqe['empID'],
                    'empHire' => $queqe['empHire'],
                    'empName' => $queqe['empName'],
                    'deptName' => $queqe['deptName']);

                $datafoot = array('totallate' => $this->report_model->itungan($totallate), 'totalearly' => $this->report_model->itungan($totalearly),
                    'totalotbef' => $this->report_model->itungan($totalotbef), 'totalotaf' => $this->report_model->itungan($totalotaf), 'total' => $this->report_model->itungan3($total));
                $datarecap = array('userid' => $queqe['userid'], 'holiday' => $totalholiday, 'workingday' => $ttlworkingday != 0 ? ($ttlworkingday - $totalholiday + $holonnwds - $off) : '0', 'workday' => $workday != 0 ? $workday : '0', 'off' => $off != 0 ? ($off + $totalholiday) - $holonnwds : '-', 'attendance' => $attendance != 0 ? $attendance : '0', 'aten' => $aten, 'absence' => $absence != 0 ? $absence : '0', 'aben' => $aben, 'absent' => $alpha != 0 ? $alpha : '-', 'totalabsent' => $totalabsent != 0 ? $totalabsent : '0', 'late' => $totallater != 0 ? $totallater : '-', 'early' => $totalearlyr != 0 ? $totalearlyr : '-', 'OT' => $totalotr != 0 ? $totalotr : '-', 'workinholiday' => $workinholiday != 0 ? $workinholiday : '-');

                $data = array("dateinfo" => $this->input->get('dateinfo'), "index" => $abc,
                    "cominfo" => $company, "empinfo" => $dataallaye,
                    "footah" => $datafoot,
                    "att" => $attend,
                    "abs" => $absen, "rekap" => $datarecap, "data" => $dataarray,
                    "excelid" => $excelid,
                    "pdfid" => $ispdf
                );
                $abc++;

                if ($excelid == 1) {
                    $dataview = $this->load->view("laporan", $data, true);
                    $datavw = $datavw . $dataview;
                } else {
                    $this->load->view("laporan", $data);
                }
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

                $stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $this->mpdf->WriteHTML($stylesheet,1);

                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }

            if ($excelid == 1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporankehadiran.xls");
                echo "$datavw";
            }
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */