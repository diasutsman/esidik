<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rptupacara extends MX_Controller {
    private $aAkses;

	function Rptupacara(){
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

        $this->aAkses = akses("Rptupacara", $this->session->userdata('s_access'));

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
        $this_url = site_url('rptupacara/pagging/');
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
        $this_url = site_url('rptupacara/pagging/');
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
        $userar=array();
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
            $dataallay[] = array('userid' => $queq->userid, 
                                'empTitle' => $queq->title, 
                                'empID' => $queq->badgenumber, 
                                'empHire' => isset($queq->hireddate) ? date('d-m-Y', strtotime($queq->hireddate)) : '', 
                                'empName' => $queq->name, 'deptName' => isset($deptar[$queq->deptid]) ? $deptar[$queq->deptid] : '');
            $dataallu[$queq->userid] = array('empTitle' => $queq->title, 
                                'empID' => $queq->badgenumber, 
                                'empHire' => isset($queq->hireddate) ? date('d-m-Y', strtotime($queq->hireddate)) : '', 
                                'empName' => $queq->name, 'deptName' => isset($deptar[$queq->deptid]) ? $deptar[$queq->deptid] : '');
            foreach ($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach ($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
        }
        $compa = $this->report_model->getcompany();
        $company = array('companyname' => isset($compa->row()->companyname) ? $compa->row()->companyname : '',
            'logo' => isset($compa->row()->logo) ? $compa->row()->logo : '',
            'address1' => isset($compa->row()->address1) ? $compa->row()->address1 : '',
            'address2' => isset($compa->row()->address2) ? $compa->row()->address2 : '',
            'phone' => isset($compa->row()->phone) ? $compa->row()->phone : '',
            'fax' => isset($compa->row()->fax) ? $compa->row()->fax : '');
		
        if ($pilrpt == '1') 
        {
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
                    $querytemp = $this->report_model->getattbydateorgupacara($areaid, $tanggal, $orgid,$stspeg,$jnspeg);
                } else if ($yo == 2) {
                    $querytemp = $this->report_model->getattbydateuserupacara($tanggal, $userar,$stspeg,$jnspeg);
                } else if ($yo == 3) {
                    $querytemp = $this->report_model->getattbydateupacara($areaid, $tanggal,$stspeg,$jnspeg);
                }
                //$querytemp = $this->report_model->getattlogupacara($datestart, $datestop, $queqe['userid']);
                log_message('error', $this->db->last_query()); 
				$dataarray = array();
                foreach ($querytemp->result() as $que) 
                {
					
                    $in = strtotime($que->date_in . ' ' . $que->check_in);

                    $date_in = null;
                    $check_in = null;

                    if (isset($que->date_in) && isset($que->check_in)) {
                        $date_in = date('d-m-Y', strtotime($que->date_in)). ' ' . $que->check_in;
                    } 

                    $upacara='';
					if (isset($que->date_shift)){
                        //$tot++;
                        if($que->attendance=='UPC'){
							if (isset($que->date_in)) {
								$upacara = 'Mengikuti upacara';
							}else{
								$upacara = 'Tidak mengikuti upacara';
                            }
                            
                            
                        }
                        if ($que->attendance=='CPC')
                        {
                            $upacara ='Pembatalan absensi upacara';//$que->notes;
                        }
                        /* else{
							if (isset($tbar[$que->attendance])) {
								$upacara = $tbar[$que->attendance];
							} if (isset($bbar[$que->attendance])) {
								$upacara = $bbar[$que->attendance];
							}
						} */
					}
					
                    $dataarray[] = array(
                        'userid' => $que->userid,
                        'badgeNumber' => $que->badgenumber,
                        'name' => $que->name,
                        'dept' => $deptar[$que->deptid],
                        'workingdate'=>date('d-m-Y', strtotime($que->date_shift)),
                        'workinghour1'=>$que->shift_in,
                        'workinghour2'=>$que->shift_out,
                        'dutyon' => $date_in,
                        'notes' => $upacara);
                }
                $dataallaye = array('day' => $day, 'date' => date('Y-m-d', strtotime($tanggal)));

                $data = array("dateinfo" => $this->input->get('dateinfo'),
                    "index" => $abc,
                    "cominfo" => $company,
                    "empinfo" => $dataallaye,
                    "data" => $dataarray,
                    "excelid" => $excelid,
                    "pdfid" => $ispdf,
                    "lastPage" =>0,
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

                //$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $stylepdf= BASEPATH.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
                $stylesheet = file_get_contents( $stylepdf );
                $this->mpdf->WriteHTML($stylesheet,1);

                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }
            if ($excelid == 1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporankehadiranupacara.xls");
                echo "$datavw";
            }
        } else {
            $dataallaye = array();
            $abc = 0;
            $dataview = '';
            $datavw = '';
			$moderpt = 'Upacara';
            foreach ($dataallay as $queqe) 
            {
                $querytemp = $this->report_model->getattlogupacara($datestart, $datestop, $queqe['userid']);
                //echo $this->db->last_query();
                $dataarray = array();
                $total = 0;
                $absence = 0;
                $tot=0;
                foreach ($querytemp->result() as $que)
                {
					$day = date('D', strtotime($que->date_shift));
                    $in = strtotime($que->date_in . ' ' . $que->check_in);
                    
					$date_in = null;
                    if (isset($que->checktime)) {
                        $date_in = date('d-m-Y H:i:s', strtotime($que->checktime));
                    } 
					
					$upacara='';
					if (isset($que->date_shift)){
                        $tot++;
                        if($que->attendance=='UPC'){
							if (isset($que->date_in)) {
								$upacara = 'Mengikuti upacara';
							}else{
								$upacara = 'Tidak mengikuti upacara';
                            }
                        }
                        if ($que->attendance=='CPC')
                        {
                            $upacara = 'Pembatalan absensi Upacara';//$que->notes;
                        }
                        /* else{
							if (isset($tbar[$que->attendance])) {
								$upacara = $tbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
							} if (isset($bbar[$que->attendance])) {
								$upacara = $bbar[$que->attendance]." ".$this->report_model->getdetailroster($que->userid,$que->date_shift);
							}
						} */
					}

                    $dataarray[] = array(
                        'day' => $day, 
                        'date' => date('d-m-Y', strtotime($que->date_shift)),
                        'workingdate'=>date('d-m-Y', strtotime($que->date_shift)),
                        'workinghour1'=>$que->shift_in,
                        'workinghour2'=>$que->shift_out,
                        'dutyon' => $date_in, 
                        'notes' => $upacara);
                    $total = $total + $tot;

                }

                $dataallaye = array(
                    'userid' => $queqe['userid'],
                    'empTitle' => $queqe['empTitle'],
                    'empID' => $queqe['empID'],
                    'empHire' => $queqe['empHire'],
                    'empName' => $queqe['empName'],
                    'deptName' => $queqe['deptName']);

                $datafoot = array('total' => $this->report_model->itungan3($total));
                $datarecap = array('userid' => $queqe['userid'], );

                $data = array("dateinfo" => $this->input->get('dateinfo'), "index" => $abc,
                    "cominfo" => $company, "empinfo" => $dataallaye,
                    "footah" => $datafoot,
                    "att" => $attend,
                    "abs" => $absen, "rekap" => $datarecap, "data" => $dataarray,
                    "excelid" => $excelid,
                    "pdfid" => $ispdf,
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

                //$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $stylepdf= BASEPATH.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
                $stylesheet = file_get_contents( $stylepdf );
                $this->mpdf->WriteHTML($stylesheet,1);

                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }

            if ($excelid == 1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporankehadiranupacara.xls");
                echo "$datavw";
            }
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */