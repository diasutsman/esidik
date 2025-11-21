<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rpttunjangan extends MX_Controller {

    private $aAkses;

	function Rpttunjangan(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );
        $this->load->library("PHPExcel");

        $this->load->model('utils_model','utils');
		$this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('report_model');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Rpttunjangan", $this->session->userdata('s_access'));

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
        $this_url = site_url('rpttunjangan/pagging/');
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
        $jnsLap = $this->input->post('jnslap');
		switch ($jnsLap)
        {
            case 1:
                $this->view1();
                break;
            case 2:
                $this->view2();
                break;
            case 3:
                $this->view3();
                break;
            case 4:
                $this->view4();
                break;
            case 5:
                $this->view5();
                break;
            default:
                $this->view1();
                break;
        }
        /*if ($jnsLap==1) {
            $this->view1();
        } else if ($jnsLap==2) {
            $this->view2();
        } else  {
            $this->view3();
        }*/
    }

    function view1()
    {
        //$postdatestart = $this->input->post('start')."-01";
        //$postdatestop = date("Y-m-t", strtotime($postdatestart)); //$this->input->post('end');

        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));

        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');
        $showpdf= $this->input->post('showpdf');
        $userid = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        $mastertunj = array();

        //trap referensi data
        $this->session->set_userdata('tahundata',date("Y",$datestart));
        
        /* $sqlv = "select * from mastertunjangan"; 
        $validdate = strtotime('01-11-2018');
        if ($datestart < $validdate )
        {
            $sqlv = "select * from mastertunjangan_2018";
        } */
        $nmTble= namaTableTunjangan($postdatestart);
        $sqlv = "select * from ".$nmTble;
        $query = $this->db->query($sqlv);
        foreach ($query->result() as $que) {
            $mastertunj[$que->kelasjabatan] = $que->tunjangan;
        }

        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(convertToArray($this->session->userdata('s_area'))):array();

        $tbar = array();
        $bbar = array();
        $holar = array();
        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $pkrga = array();
        $akrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname
            );
            $pkrga[$at->atid] = $at->value;
            $akrga[$at->atid] = $at->ign_to;
        }
        $pkrg = array();
        $absrecap = $this->report_model->getabs();
        foreach($absrecap->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname
            );
            $pkrg[$bs->abid] = $bs->value;
        }

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $yo = 0;
        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetailsxx($userar);
            $yo=2;
        } else if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg,$jnspeg);
            $yo=1;
        }
		$bbars = array();
		$absrecap = $this->report_model->getabs();
        foreach ($absrecap->result() as $bs) {
            $bbars[$bs->abid] = $bs->abname;
            $absen[] = array('abid' => $bs->abid, 'abname' => $bs->abname);
        }
        //print_r($this->db->last_query());
        //die();
        $aten = array();
        $aben = array();

        $dataallay = array();

        $rslt = $queryemp->result() ;
        //print_r($this->db->last_query());
        //die();
        foreach($rslt as $queq) {
			$unkir= isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0;

            $isplt=0;$isViewIt=1;
            $PltHist = $this->report_model->getkelasplttukinhist($queq->userid,$postdatestart);
            //if (date("Y",$datestart)>2016) {
            //print_r($PltHist);
            //if (isset($queq->tmt_plt) && isset($queq->plt_deptid) && isset($queq->plt_eselon))
            if (isset($PltHist['tmt']) && isset($PltHist['deptid']) && isset($PltHist['eselon']))
            {
                if ($PltHist['tmt'] !='0000-00-00') {
                    //echo $queq->userid." 1<br>";
                    //$time = strtotime($queq->tmt_plt);
                    $time = strtotime($PltHist['tmt']);
                    //$final = strtotime("+1 month", $time);
                    //pindah bulan udah dapet plt/plh
                    if ($datestart >= strtotime(date("Y-m-01",$time))) {
                        if ((intval(date('d', $time)) < 6) && date("Y-m", $datestart) === date('Y-m', $time)) {
                            $isplt = 1;
                            //echo $queq->userid." 2<br>";
                        } else {
                            $time2 = strtotime(date('Y-m-01', $time));
                            $final = strtotime("+1 month", $time2);
                            if ($datestart >= $final) {
                                $isplt = 1;
                            } else {
                                //if (!in_array($queq->deptid, $orgid))
                                if (!in_array($PltHist['deptid'], $orgid))
                                {
                                    continue;
                                }
                            }
                        }
                    } else{
                        $isViewIt=0;
                        //continue;
                        //echo $queq->userid." ".in_array($queq->deptid, $orgid)."<br>";
                        //if (!in_array($queq->deptid, $orgid))
                        if (!in_array($PltHist['deptid'], $orgid))
                        {
                            continue;
                        }
                    }
                }/*else{
                        echo $queq->userid." 4<br>";
                    }*/
            }
            // }

            $pltEselon = isset($PltHist['eselon']) ? $PltHist['eselon']:$queq->plt_eselon;
            $pltDepid= isset($PltHist['deptid']) ? $PltHist['deptid']:$queq->plt_deptid;
            $eselonDef= isset($PltHist['eselondef']) ? $PltHist['eselondef']:$queq->eselon;

            $jbtnDef = strval(konversiEselonBaru($eselonDef));
            $jbtnPlt = strval(konversiEselonBaru($pltEselon));
            $unkirplt=0;
            $kriteriaPlt = 0;


            $pltKelasJabatan = isset($PltHist['kelas']) ? $PltHist['kelas']:$queq->plt_kelasjabatan;

            if ($isplt==1) {
                $unkirplt= isset($mastertunj[$pltKelasJabatan]) ? $mastertunj[$pltKelasJabatan] * refTHP() :0;
                if ( ($jbtnDef  > $jbtnPlt) ) {
                    $kriteriaPlt=1;
                    $unkirplt= isset($mastertunj[$pltKelasJabatan])?$mastertunj[$pltKelasJabatan] :0;
                    $unkirplt= ($unkirplt * refTHP() ) * refPLT();
                }
                if ( ($jbtnDef  == $jbtnPlt ) ) {
                    $kriteriaPlt=2;
                    /*$unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan]:0;
                    $unkirplt= ($unkirplt * refTHP()) * refPLT();*/
                    if ($pltKelasJabatan > $queq->kelasjabatan) {
                        $unkirplt = isset($mastertunj[$pltKelasJabatan]) ? $mastertunj[$pltKelasJabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP());
                        $unkir = ($unkir * refTHP()) * refPLT();
                    } else
                    {
                        $unkirplt = isset($mastertunj[$pltKelasJabatan]) ? $mastertunj[$pltKelasJabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP()) * refPLT();
                    }
                }

                if ( ($jbtnDef  < $jbtnPlt ) ) {
                    $kriteriaPlt=3;
                    //$klsjbatan = max(array($queq->kelasjabatan,$pltKelasJabatan));
					$klsjbatan =$pltKelasJabatan;
                    $unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* refTHP() :0;
                    $unkir = 0;
                }

                //echo $jbtnDef." ".$jbtnPlt." ".$kriteriaPlt." ".$klsjbatan." ".$unkirplt;
            }

            if ($queq->payable == 0){
                $unkir=0;
                $unkirplt=0;
				//echo "nol";
            }

            //echo $queq->userid.' '.$isplt." ".$kriteriaPlt." ".$queq->userid." ".$unkir." ".$unkirplt." ".$jbtnDef." ".$jbtnPlt."<br>";
            //die();

            $dataallay[] = array(
                'userid'   => $queq->userid,
                'empTitle' => $queq->title,
                'empID' => $queq->badgenumber,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
                'kelasjabatan' => $queq->kelasjabatan,
                'tunjanganprofesi' => $queq->tunjanganprofesi,
                'tmtprofesi' => $queq->tmtprofesi,
                'tunjangan' => $unkir,
                'jftstatus' => $queq->jftstatus,
                'jenisjabatan' => $queq->jenisjabatan,
                'jenispegawai' => $queq->jenispegawai,
                'kedudukan' => $queq->kedudukan,
                'tmtkedudukan' => $queq->tmtkedudukan,
                'deptid' => $queq->deptid,
                'tunjanganplt' => $unkirplt,
                'plt_deptid' => $pltDepid,
                'plt_eselon' => $pltEselon,
                'tmt_plt' => $PltHist['tmt'],//$queq->tmt_plt,
                'payable' => $queq->payable,
                'eselon' => $queq->eselon,
                'plt_jbtn' => $queq->plt_jbtn,
                'plt_sk' => $queq->plt_sk,
                'plt_kelasjabatan' => $pltKelasJabatan,//$queq->plt_kelasjabatan,
                'isplt' =>$isplt,
                'kriteriaPlt' =>$kriteriaPlt,
                'plt_deptname' => isset($deptar[$pltDepid])?$deptar[$pltDepid]:'',//isset($deptar[$queq->plt_deptid])?$deptar[$queq->plt_deptid]:'',
                'dipisah'=>0,
                'ishidden'=>0,
                'isViewIt'=>$isViewIt,
                'orgf'=>""
            );
			
			foreach($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
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
        $dataallaye = array();
        $datafoot = array();
        $abc=0;
        $dataview = '';
        $datavw = '';

        foreach($dataallay as $queqe) {
			$querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
            $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);

            $dataarray = array();
            $totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
            $totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
            $totallibur  = 0; $tubel = 0; $totaltubel = 0; $totaltubelplt=0;$tunjtubel = 0; $tunjtubelplt = 0; $tgltubel = 0;
            $totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;
            $tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0; $tunj = 0; $tunj1 = 0; $totplt = 0;$tunjplt = 0;
            $ttlmsk = array(); $tunjangan = array(); $tunjanganplt = array();$tunbulplt = array();
            $tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
            $jft = array(); $jfto = 0; $jftp = array();
            $jpeg = array(); $jpego = 0; $jpegp = array();
            $kedu = array(); $keduo = 0; $kedup = array();
            $totaltunjplt =array();$totaltunj =array();

            foreach($querytempo->result() as $que) {
                $ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunj[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunjplt[date('mY', strtotime($que->date_shift))] = 0;
            }
			
            foreach($querytempo->result() as $que) 
            {
				$kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
                if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
                $tunjang = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);
                if ($tunjang == 0) $tunjang = $mastertunj[$queqe['kelasjabatan']];
                //echo $tunjang;
                if($tunjang!=0) {
                    $queqe['tunjanganasli']=$tunjang;
                    if ($queqe['userid'] != 0 ) {
						//if (date("Y", $datestart) > 2016) {
                        $queqe['tunjangan'] = $tunjang * refTHP();
                        
                    } else {
                        $queqe['tunjangan'] = 0;
                    }
					
                    //baru
                    if ($queqe['kriteriaPlt']==2)
                    {
                        if ( $queqe['plt_kelasjabatan'] > $queqe['kelasjabatan']) {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = ($tjngnga * refTHP()) * refPLT();
                        } else
                        {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = $tjngnga * refTHP();
                        }
                    }
                    //CPNS aktif mulai 1 April 2019 sebesar 80%
                    $curenttime=strtotime($que->date_shift);
                    $bypasstime=strtotime('2019-04-01');
                    if ($curenttime>=$bypasstime){
                        if ($queqe['jftstatus']==1)
                        {
                            $queqe['tunjangan'] = $queqe['tunjangan'] * refTHPCP();
                        }
                    }
                }
				
				if($que->attendance != 'NWK' &&
                    $que->attendance != 'NWDS' &&
                    $que->workinholiday != 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($que->flaghol == 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($tunbuli!=$queqe['tunjangan']) {
                    $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))))? 1 : 1; //0.5 : 1
                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                    $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];
                }

                //echo $que->date_shift." ".$queqe['tunjangan']."<br>";

                $byuser= $userid!='undefined'?1:0;
				if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 3)
                    {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  = isOrgIn($orgtar);

                        //asalnya tidak sama
                        /*if ($cpltdeptid === $corgtar)
                        {
                            $queqe['ishidden'] = 0;
                        }*/
                        $queqe['ishidden'] = ($cpltdeptid != $corgtar)?1:0;
                        //echo $queqe['userid']." ".$cpltdeptid." ".$corgtar." ".$queqe['ishidden']."<br>";
                    }
                }

                if ($byuser==0)
                {
                    if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined')
                        {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            $cdeptid =isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            $corgtar  = isOrgIn($orgtar);
                            if ($dibagi==1)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }

                            if ($tunbuli != $queqe['tunjangan']) {
                                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'] * $kali;
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        $cdeptid = isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  = isOrgIn($orgtar);

                        if ($dibagi==1)
                        {
                            if ($cdeptid == $corgtar) //satu tempat defenitif
                            {
                                $queqe['dipisah']= 1;
                            }
                            if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                            {
                                $queqe['dipisah']= 2;
                            }
                        }
                    }
                } else
                {	
					if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined')
                        {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            $cdeptid =isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            $corgtar  =isOrgIn($orgtar);

                            $queqe['orgf']= $cdeptid." ".$cpltdeptid.' '.$corgtar;
                            if ($dibagi)
                            {
                                if ($cdeptid === $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid === $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }
							//echo $tunbuli;
                            if ($tunbuli != $queqe['tunjangan']) {
                                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'] * $kali;
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        $cdeptid = isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  =  isOrgIn($orgtar);
                        if ($dibagi)
                        {
                            if ($cdeptid == $corgtar) //satu tempat defenitif
                            {
                                $queqe['dipisah']= 1;
                            }
                            if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                            {
                                $queqe['dipisah']= 2;
                            }
                        }
                    }
                }

                //else
                //	$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];
                $jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
				
				if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;

                if($jfto!=$queqe['jftstatus'])
                    $jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];
                /* else
                    $jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */

                $jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
                if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;

                if($jpego!=$queqe['jenispegawai'])
                    $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];
                /* else
                    $jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */

                $kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
                if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;
				//echo $kedupeg." dfdf<br>";
				if($keduo!=$queqe['kedudukan'])
                    $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];
                /* else
                    $kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */

                $tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
                //if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;
                if($tunjangprof!=0) {
                    if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi']))
                    {
                        $queqe['tunjanganprofesi'] = $tunjangprof;
                    } else {
                        $queqe['tunjanganprofesi']=0;
                    }
                }

                //if($tunpro!=$queqe['tunjanganprofesi'])
                //    $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];

                if($tunpro!=$queqe['tunjanganprofesi']) {
                    //if (date("Y", $datestart) > 2016) {
                    if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi'])) {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'] ; //* 0.5
                    } else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = 0;
                    }

                    /*} else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];

                    }*/
                }

                /* else
                    $tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */
                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))))? 1 : 1; //0.5
                $tunbuli = $queqe['tunjangan'] * $kali;
                if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi'])) {
                    $tunpro = $queqe['tunjanganprofesi'];
                } else{
                    $tunpro = 0;
                }
                $jfto = $queqe['jftstatus'];
                $jpego = $queqe['jenispegawai'];
                $keduo = $queqe['kedudukan'];
                //echo $queqe['jftstatus']."<br>";
                //die();

            }

            foreach($querytemp->result() as $que) {
				$late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
                $totalpersenkurang = 0; $totaljadwal++;
                $day = date('D', strtotime($que->date_shift));
                $date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
                if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
                if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));
				//echo $queqe['kedudukan']."<br>";
                if(isset($tunbul[date('mY', strtotime($que->date_shift))])) {
                    if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubel = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        //echo $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubelplt = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                    }
                } else {
                    $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    //echo "1";
                }
				if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12 || $queqe['kedudukan']==13) { $tottun = 0;$totplt=0;}
                if($queqe['jenispegawai']==3 || $queqe['jenispegawai']==4) { $tottun = 0;$totplt=0;}

                if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        //echo "1";
                    }
                }

                if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 ||
                        $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunjanganplt[date('mY', strtotime($que->date_shift))] / 2;
                        } //else {
                        //$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))];
                        // $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunjanganplt[date('mY', strtotime($que->date_shift))];
                        //}
                        //echo "2";
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 ||
                        $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 ||
                        $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tubel = 1;
                            $tgltubel = strtotime($queqe['tmtkedudukan']);
                        }
                    }
                }

                if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 ||
                        $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        //echo "3";
                    }
                }
				
                if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    $tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                    if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
                        //echo $tunjanganpro[date('mY', strtotime($que->date_shift))]." ".$tunjangan[date('mY', strtotime($que->date_shift))]."<br>";
                        if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))]) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                            //echo "4";
                        }
                        else {
                            //khusus profesi
                            //$tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]*2) - $tunjanganpro[date('mY', strtotime($que->date_shift))])*refTHP();
                            $tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]) - $tunjanganpro[date('mY', strtotime($que->date_shift))]);
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                            //echo "5";
                        }
                    }
                }
                //echo $queqe['userid'].' '.$tunjangan[date('mY', strtotime($que->date_shift))]." ".$tunjanganpro[date('mY', strtotime($que->date_shift))].'<br>';
                //die();
                //ambil nilai pulang
                $early=0;
                if($que->early_departure!=0) {
                    $early = $que->early_departure;
                }

                if($que->late!=0) {
                    $late = $que->late;

                    if ($que->late >3660) {
                        $telat = $que->late;
                        $late = $telat <= 0 ? 0 : $telat;
                    } else {
                        if ($que->ot_after != 0) {
                            if ($que->ot_after > 3600)
                                $telat = $que->late - 3600;
                            else
                                $telat = $que->late - $que->ot_after;

                            $late = $telat <= 0 ? 0 : $telat;
                        }
                    }

                    if (date("Y",$datestart)<2017)
                    {
                        if($late < 1860) $krglate = 0; //kurang dari 30 menit
                        else if($late >= 1860 && $late < 3660) $krglate = 0.5; //31- 61 menit
                        else if($late >= 3660 && $late < 5460) $krglate = 1; //61- 91 menit
                        else if($late >= 5460) $krglate = 1.5; //91 menit ke atas
                    } else
                    {
                        if($late < 3660) { // < 61 menit
                            $krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = 0.5;
                        }
                        else if($late >= 3660) { // >=61 menit ke atas
                            $telat = $que->late;
                            $krglate = 1;
                        }

                        //echo $telat." ".$krglate."<br>";
                        //jika terlambat >60 dan mengganti 60 menit
                        if ($que->late > 3660 ) {
                            $krglate = 1;
                            $telat = $que->late;
                        }

                        //berdasarkan referensi potongan untuk terlambat
                        $wry="select persentase FROM ref_potongan WHERE $late BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=1";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krglate = $qryPotongan->row()->persentase;
                            //$krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = $qryPotongan->row()->persentase;
                        }
                    }
                }

                if($que->early_departure!=0) {
                    $early = $que->early_departure;

                    //if (date("Y",$datestart)>2016) {
                    if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;

                    //potongan PSW
                    $wry="select persentase FROM ref_potongan WHERE $early BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=2";
                    $qryPotongan = $this->db->query($wry);
                    if ($qryPotongan->num_rows()>0) {
                        $krgearly = $qryPotongan->row()->persentase;
                    }
                    /*} else {
                        if($early > 1 && $early < 1860) $krgearly = 0.5;
                        else if($early >= 1860 && $early < 3660) $krgearly = 1;
                        else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                        else if($early >= 5460) $krgearly = 1.5;
                    }*/
                }

                //if (date("Y",$datestart)>2016) {
                if ($check_in == null) $krglate = 1;
                if ($check_out == null) $krgearly = 1;
                /*} else {
                    if($check_in==null) $krglate = 1.5;
                    if($check_out==null) $krgearly = 1.5;
                }*/
                if($que->attendance == 'ALP') {
                    $krglate = 0;
                    $krgearly = 0;
                    //if (date("Y",$datestart)>2016) {
                    $krgalpa = 5;
                    /*} else {
                        $krgalpa = 3;
                    }*/
                }

                if($que->attendance == 'BLNK') {
                    $totalblnk++;
                }

                $s = 0;
                if(isset($bbar[$que->attendance])) {
                    $krglate = 0;
                    $krgearly = 0;
                    $krgalpa = 0;
                    $krgstatus = $pkrg[$que->attendance];
                    $s = 1;
                }

                if(isset($atar[$que->attendance])) {
                    if($que->attendance=='AT_SK01' || $que->attendance=='AT_AT1')
                        $krglate = 0;
                    else if($que->attendance=='AT_SK03' || $que->attendance=='AT_AT2')
                        $krgearly = 0;
                    else {
                        $krglate = 0;
                        $krgearly = 0;
                    }
                    $krgalpa = 0;
                    $krgstatus = $pkrga[$que->attendance];

                    switch ($akrga[$que->attendance])
                    {
                        case 1:
                            $krglate = 0;
                            break;
                        case 2:
                            $krgearly = 0;
                            break;
                        default:
                            $krglate = 0;
                            $krgearly = 0;
                            break;
                    }

                }

                if($krglate > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {
					/* $potongan = ($tunjtubel*80)/100;
					//if($status == 1){
                    if ($queqe['jftstatus']==1){
						$alp = round((($krglate /100) * $potongan));
					}else{
						$alp = round(($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					} */
                    $dataarray[] = array(
                        'day'			=> $day,
                        'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                        'status'		=> $que->check_in!=null?'Terlambat':'Tidak absen datang',
                        'nilai'			=> $que->check_in==null?null:$this->report_model->itungan($late),
						'pengurangan'	=> $krglate,
						'total'			=> ($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                        'totalplt'	    => ($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                    );
                    $totalpersenkurang = $totalpersenkurang + $krglate;
					//$tunj1 = $tunj1 + round((($krglate /100) * $potongan));
                    $tunj = $tunj + round((($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                    $totallate = $totallate +(($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {
                   /*  $status = $queqe['jftstatus'];
					$potongan = ($tunjtubel*80)/100;
					if($status == 1){
						$alp = round((($krgearly /100) * $potongan));
					}else{
						$alp = round(($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					} */
                    $dataarray[] = array(
                        'day'			=> $day,
                        'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                        'status'		=> $que->check_out!=null?'Pulang lebih awal':'Tidak absen pulang',
                        'nilai'			=> $que->check_out==null?null:$this->report_model->itungan($que->early_departure),
                        'pengurangan'	=> $krgearly,
						'total'			=> ($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
						'totalplt'		=> ($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                    );
                    $totalpersenkurang = $totalpersenkurang + $krgearly;
					//$tunj1 = $tunj1 + round((($krgearly /100) * $potongan));
                    $tunj = $tunj + round((($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                    $tunjplt = $tunjplt + (($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }
				
				if($krgalpa != 0 && $que->workinholiday!=1) {
					$status = $queqe['jftstatus'];
					$potongan = ($tunjtubel*80)/100;
					if($status == 1){
						$alp = round((($krgalpa /100) * $potongan));
					}else{
						$alp = round(($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}
					$dataarray[] = array(
                        'day'			=> $day,
                        'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                        'status'		=> 'Alpa',
                        'nilai'			=> null,
                        'pengurangan'	=> $krgalpa,
                        'total'			=> ($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                        'totalplt'		=> ($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                    );
                    $totalpersenkurang = $totalpersenkurang + $krgalpa;
                    $totalalpa++;
					//$tunj1 = $tunj1 + round((($krgalpa /100) * $potongan));
                    $tunj = $tunj + round((($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                    $tunjplt = $tunjplt + (($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					//var_dump(round((($krgalpa /100) * $tunjtubel )));
                }
				//var_dump($tunjtubel);
                //echo $totaltubel."<br>TB";
                if($krgstatus != 0  && $queqe['payable']!=0) {
					$status = $queqe['jftstatus'];
					$potongan = ($tunjtubel*80)/100;
					if($status == 1){
						$alp = round((($krgstatus /100) * $potongan));
					}else{
						$alp = round(($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}
                    $dataarray[] = array(
                        'day'			=> $day,
                        'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                        'status'		=> $s==1?$bbar[$que->attendance]:$atar[$que->attendance],
                        'nilai'			=> null,
                        'pengurangan'	=> $krgstatus,
                        'total'			=> ($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
						'totalplt'		=> ($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                    );

                    $totalpersenkurang = $totalpersenkurang + $krgstatus;
                    if($que->attendance=='AB_12') $totalpembatalan++;
					$tunj1 = $tunj1 + round((($krgstatus /100) * $potongan));
                    $tunj = $tunj + round((($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                    $tunjplt = $tunjplt + (($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                /*  khusus upacara status dibayarkan
                    tgl upacara ada ,tgl/waktu check in ada isinya atau kosong dan bukan Tubel
                */ 
                $arrAtt=array("AT_AT1","AT_SK01","AT_AT5","AT_AT6"); //tidak dipotong
                $kdAbsPtg="AB_12";//didipotong

                $ta = $this->report_model->getdetailroster3($que->userid,$que->date_shift);
                $pa = $ta->first_row();
                //echo ("J:".$que->date_shift2." => D:".$que->date_in2.' => T:'.$que->check_in2."<br>");
                if ($que->date_shift2!="" && $queqe['payable'] !=0  && $tubel==0)
                {			
                    //print_r($que->date_shift2);
                    $qa = $this->report_model->gettranslog2($que->date_shift2,$que->date_shift2,$que->userid,"Upacara");
                    $za = $qa->first_row();

                    //if ($que->date_in2!="" && $que->check_in2 !="" ) { //ada tap ke mesin
                    if ($za != '' or $za != null) {
                        //echo("1. ".$que->check_in2."<br>");
                        if($pa != '' or $pa != null) //cek jadwal
                        {
                            $jwdlawal = strtotime($pa->date_shift.' '.$pa->shift_in);
                            $jwdlakhir = strtotime($pa->date_shift.' '.$pa->shift_out);

                            $cekinupcara= strtotime($za->checktime);
                            if ($cekinupcara < $jwdlawal  || $cekinupcara > $jwdlakhir)
                            {
                                $jwdlawal = date('Y-m-d H:i:s',$jwdlawal);
                                $jwdlakhir = date('Y-m-d H:i:s',$jwdlakhir);
                                $cekinupcara = date('Y-m-d H:i:s',$cekinupcara);
                                //echo("11. Diluar $jwdlawal - $jwdlakhir / $cekinupcara <br>");
                                $ispotong=1;

                                if (in_array($pa->attendance,$arrAtt))
                                {
                                    $ispotong=0; 
                                    //echo("13. ATT <br>");
                                } else {
                                    //echo("14. ".substr($pa->attendance,0,2)."<br>");
                                    if (substr($pa->attendance,0,2)=="AB"){
                                        if ($kdAbsPtg==$pa->attendance){
                                            $ispotong=1; 
                                        } 
                                        else {
                                            $ispotong=0; 
                                        }
                                    }
                                    
                                }

                                //echo("15. ".$pa->attendance." - ".$ispotong." <br>");

                                if ($ispotong==1){
                                    $nkurang = refUpacara();
                                    //$potongan = ($tunjtubel*80)/100;
                                    //$alp = round((($nkurang /100) * $potongan));
                                    //$upacara = $this->report_model->getdetailroster2($que->userid,$que->date_shift);

                                    if($queqe['jftstatus'] == 1){
                                        $alp = round((($nkurang /100) * $potongan));
                                    }else{
                                        $alp = round((($nkurang /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                                    }
                                    $dataarray[] = array(
                                        'day'			=> $day,
                                        'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                                        'status'		=> 'Tidak melakukan upacara ',
                                        'nilai'			=> null,
                                        'pengurangan'	=> $nkurang,
                                        'total'			=> ($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                                        'totalplt'		=> ($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                                    );
                                    $totalpersenkurang = $totalpersenkurang + $nkurang;
                                    $tunj1 = $tunj1 + round((($nkurang /100) * $potongan));
                                    $tunj = $tunj + round(($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                                    $tunjplt = $tunjplt + (($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                                }
                            } 
                        }
                    //print_r($this->db->last_query());
                    } else {
                        //echo("0. <br>");
                        $ispotong=1;
                        if (in_array($pa->attendance,$arrAtt))
                        {
                            $ispotong=0; 
                            //echo("01. ATT <br>");
                        } else {
                            //echo("24. ".substr($pa->attendance,0,2)."<br>");
                            if (substr($pa->attendance,0,2)=="AB"){
                                if ($kdAbsPtg==$pa->attendance){
                                    $ispotong=1; 
                                } 
                                else {
                                    $ispotong=0; 
                                }
                            }
                        }

                        if ($ispotong==1){
                            $nkurang = refUpacara();
                            //$potongan = ($tunjangan*80)/100;
                            //$alp = round((($nkurang /100) * $potongan));
                            //$upacara = $this->report_model->getdetailroster2($que->userid,$que->date_shift);
                            //if($queqe['jftstatus'] == 1){
                            //    $alp = round((($nkurang /100) * $potongan));
                            //}else{
                                $alp = round((($nkurang /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                            //}
                            $dataarray[] = array(
                                'day'			=> $day,
                                'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                                'status'		=> 'Tidak melakukan upacara ',
                                'nilai'			=> null,
                                'pengurangan'	=> $nkurang,
                                'total'			=> ($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                                'totalplt'		=> ($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                            );
                            $totalpersenkurang = $totalpersenkurang + $nkurang;
                            $tunj1 = $tunj1 + round((($nkurang /100) * $potongan));
                            $tunj = $tunj + round(($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                            $tunjplt = $tunjplt + (($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        }
                    }
                }

				$totalpersensemua = $totalpersensemua + $totalpersenkurang;
                $totalmasuk = array_sum($ttlmsk);
				
				if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1 ) {
                    if($tubel==1) {
                        //$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        //$totaltubelplt = $totaltubelplt+ $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }
				
				if($que->flaghol == 1) {
                    if($tubel==1) {
                        //$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        //$totaltubelplt = $totaltubelplt + $totaltunjplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }
                //khususTubel
                if ($tubel==1)
                {
                    if ($que->attendance == 'NWK' || $que->flaghol==1 || $que->attendance=="")
                    {

                    } else
                    {
                        $totaltubel = $totaltubel + $tunjtubel*0.5 ;//$tunjangan[date('mY', strtotime($que->date_shift))];
                        //echo $que->date_shift." ".$que->attendance." ".$tunjtubel*0.5."<br>";
                    }
                }
                //echo $que->date_shift." ".$que->attendance." ".$totaltubel." ".$tunjtubel."<br>";
            }
			
            //$totaltubel = $totaltubel/$totalmasuk;
            //echo array_sum($totaltunj);
			if($queqe['jftstatus'] == 1 AND $queqe['empTitle'] != ''){
				$tottun = ($queqe['tunjangan']*80)/100;
			}else{
				$tottun = isset($totaltunj)?array_sum($totaltunj):0;
				$totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;
            }
            
            $tottun = isset($totaltunj)?array_sum($totaltunj):0;
            $totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;
                
            $arrn=array(1,4); //aktif dan tubel
            //echo $queqe['kedudukan'];
            if (!in_array($queqe['kedudukan'], $arrn)) {
                $tottun=0;
                $totplt=0;
            } 
			
			
            if($tubel==1) {
                $dataarray[] = array(
                    'day'			=> '',
                    'date'			=> '',
                    'status'		=> 'Tugas Belajar Per Tgl '.date('d-m-Y', $tgltubel),
                    'nilai'			=> null,
                    'pengurangan'	=> '-',
                    'total'			=> 0,
                    'totalplt'			=> 0
                );
                $tunj = 0;
                $tottun = ($totaltubel/$totalmasuk);
                $totplt= $tunjtubelplt;
            }
            //echo $totaltubel."<br>TB";
            if($totalalpa == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
			}

            if($totalpembatalan == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
			}

            $totalsemua = $totalalpa + $totalpembatalan;
			//echo $totalmasuk;
            
			if($totalsemua == $totalmasuk) {
                $tottun = isset($totaltunj)?array_sum($totaltunj):0;
                $totplt = 0;
			}

            if($totalblnk == $totaljadwal) {
                $tottun = 0;
                $totplt = 0;
				//echo $tottun;
            }
			
            //echo $queqe['userid']." TBL2 ".$tottun." ".$totplt."<br>";
            $dataallaye = array(
                'userid' => $queqe['userid'],
                'empTitle' => $queqe['empTitle'],
                'empID' => $queqe['empID'],
                'empHire' => $queqe['empHire'],
                'empName' =>$queqe['empName'],
                'deptName' => $queqe['deptName'],
                'kelasjabatan' => $queqe['kelasjabatan'],
                'tunjangan' => $tottun,
                'tunjanganplt' => $totplt,
                'plt_sk' => $queqe['plt_sk'],
                'plt_jbtn' => $queqe['plt_jbtn'],
                'plt_kelasjabatan' => $queqe['plt_kelasjabatan'],
                'isplt' => $queqe['isplt'],
                'plt_deptname' => $queqe['plt_deptname'],
                'kriteriaPlt' => $queqe['kriteriaPlt'],
                'orgf' => $queqe['orgf']
            );
			
            //echo $totplt." ".$tunjplt;
            //echo $queqe['userid']." TBL ".$tunj." ".$totplt."<br>";
            //echo $tottun;
			
			if($queqe['jftstatus'] == 1 AND $queqe['empTitle'] != ''){
				$datafoot = array('totalpersen' => $totalpersensemua,
					'total' => $tottun == 0 ? 0 : $tunj1,
					'totalplt' => $totplt == 0 ? 0 : $tunjplt
				);
			}else{
				$datafoot = array('totalpersen' => $totalpersensemua,
					'total' => $tottun == 0 ? 0 : $tunj,
					'totalplt' => $totplt == 0 ? 0 : $tunjplt
				);
            }

            if (!in_array($queqe['kedudukan'], $arrn)) {
                $datafoot = array('totalpersen' => 0,
					'total' => 0,
					'totalplt' => 0
				);
            } 
            

           

            $byuser= $userid!='undefined'?1:0;

            $data = array(
                "dateinfo" => strtoupper(format_date_ind(date('Y-m-d', $datestart) ) ." s/d ".format_date_ind(date('Y-m-d', $datestop))) ,//. " s/d " . date('d-m-Y', $datestop)
                "index" => $abc,
                "cominfo" => $company,
                "empinfo" => $dataallaye,
                "footah" => $datafoot,
                "data" => $dataarray,
                "totaltunj" =>$totaltunj,
                "totaltunjplt" => $totaltunjplt,
                "byuser"=>$byuser,
                "dipisah" =>$queqe['dipisah'],
                "orgf"=>$queqe['orgf'],
                "excelid" => $excelid,
                "pdfid"=>     $ispdf,
                "showpdf"=>$showpdf,
                "ishidden" =>$queqe['ishidden'],
                "isViewIt"=>$queqe['isViewIt']);
			//var_dump($dataallaye);
			$abc++;
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

            if($excelid==1) {
                $dataview = $this->load->view("laporan",$data,true);
                $datavw .= $dataview;
            } else {
                $this->load->view("laporan",$data);
            }
        }

        if ($ispdf==1) {
            $this->load->library('mpdf');
            $this->mpdf =
                new mPDF('',    // mode - default ''
                    '',    // format - A4, for example, default ''
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

            $stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
            $stylesheet = file_get_contents( $stylepdf );
            $this->mpdf->WriteHTML($stylesheet,1);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->WriteHTML($datavw);

            $this->mpdf->Output();
            die();
        }
        if($excelid==1) {
            header("Content-type:application/x-msdownload");
            header("Content-Disposition: attachment; filename=tunjangankinerja.xls");
            echo '<html>';
            echo '<head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Laporan Tunjangan Kinerja</title>
                </head>
                <body>';
            echo $datavw;
            echo '</body></html>';

        }
    }

    function view2()
    {
        //$postdatestart = $this->input->post('start')."-01";
        //$postdatestop = date("Y-m-t", strtotime($postdatestart)); //$this->input->post('end');
        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');
        $showpdf= $this->input->post('showpdf');
        $userid = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);

        $departemen = $this->input->post('org')!='undefined'?$this->input->post('org'):"1";
        $mastertunj = array();

        $this->session->set_userdata('tahundata',date("Y",$datestart));

        /* $sqlv = "select * from mastertunjangan"; 
        $validdate = strtotime('01-11-2018');
        if ($datestart < $validdate )
        {
            $sqlv = "select * from mastertunjangan_2018";
        } */

        $nmTble= namaTableTunjangan($postdatestart);
        $sqlv = "select * from ".$nmTble;
        $query = $this->db->query($sqlv);
        foreach ($query->result() as $que) {
            $mastertunj[$que->kelasjabatan] = $que->tunjangan;
        }

        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();


        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(convertToArray($this->session->userdata('s_area'))):array();

        $tbar = array();
        $bbar = array();
        $holar = array();
        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $akrga = array();
        $pkrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname
            );
            $pkrga[$at->atid] = $at->value;
            $akrga[$at->atid] = $at->ign_to;
        }

        $pkrg = array();
        $absrecap = $this->report_model->getabs();
        foreach($absrecap->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname
            );
            $pkrg[$bs->abid] = $bs->value;
        }

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $yo = 0;
        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetailsxx($userar,$stspeg,$jnspeg);
            $yo=2;
        } else if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg,$jnspeg);
            $yo=1;
        }

        $byuser= $userid!='undefined'?1:0;

        $aten = array();
        $aben = array();

        $dataallay = array();
        foreach($queryemp->result() as $queq) {

            $unkir= isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0;

            $isplt=0;
            $isViewIt=1;
            //if (isset($queq->tmt_plt) && isset($queq->plt_deptid) && isset($queq->plt_eselon))
            $PltHist = $this->report_model->getkelasplttukinhist($queq->userid,$postdatestart);
            if (isset($PltHist['tmt']) && isset($PltHist['deptid']) && isset($PltHist['eselon']))
            {
                if ($PltHist['tmt'] !='0000-00-00') {
                    $time = strtotime($PltHist['tmt']);
                    //$final = strtotime("+1 month", $time);
                    if ($datestart >= strtotime(date("Y-m-01",$time))) {
                        if ((intval(date('d', $time)) < 6) && date("Y-M", $datestart) === date('Y-M', $time)) {
                            $isplt = 1;
                        } else {
                            $time2 = strtotime(date('Y-m-01', $time));
                            $final = strtotime("+1 month", $time2);
                            if ($datestart >= $final) {
                                $isplt = 1;
                            }
                            else {
                                //if (!in_array($queq->deptid, $orgid))
                                if (!in_array($PltHist['deptid'], $orgid))
                                {
                                    continue;
                                }
                            }
                        }
                    } else{
                        $isViewIt=0;
                        //if (!in_array($queq->deptid, $orgid))
                        if (!in_array($PltHist['deptid'], $orgid))
                        {
                            continue;
                        }
                    }
                }
            }

            $pltEselon = isset($PltHist['eselon']) ? $PltHist['eselon']:$queq->plt_eselon;
            $pltDepid= isset($PltHist['deptid']) ? $PltHist['deptid']:$queq->plt_deptid;
            $eselonDef= isset($PltHist['eselondef']) ? $PltHist['eselondef']:$queq->eselon;

            $jbtnDef = strval(konversiEselonBaru($eselonDef));
            $jbtnPlt = strval(konversiEselonBaru($pltEselon));

            $pltKelasJabatan = isset($PltHist['kelas']) ? $PltHist['kelas']:$queq->plt_kelasjabatan;

            $unkirplt = 0;
            if(isset($mastertunj[$queq->kelasjabatan])){
                $ntunj = $mastertunj[$queq->kelasjabatan];
            }
            if(isset($ntunj)){
                $unkirplt = $ntunj * refTHP();
            }

            //$unkirplt= isset($mastertunj[$queq->kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] * 0.5:0;
            $kriteriaPlt = 0;
            if ($isplt==1) {
                if ( ($jbtnDef  > $jbtnPlt) ) {
                    $kriteriaPlt=1;
                    $unkirplt= isset($mastertunj[$pltKelasJabatan])?$mastertunj[$queq->plt_kelasjabatan] :0;
                    $unkirplt= ($unkirplt * refTHP()) * refPLT();
                }
                if ( ($jbtnDef  == $jbtnPlt ) ) {
                    $kriteriaPlt=2;
                    /*$unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan]:0;
                    $unkirplt= ($unkirplt * refTHP()) * refPLT();*/
                    if ($pltKelasJabatan > $queq->kelasjabatan) {
                        $unkirplt = isset($mastertunj[$pltKelasJabatan]) ? $mastertunj[$pltKelasJabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP());
                        $unkir = ($unkir * refTHP()) * refPLT();
                    } else
                    {
                        $unkirplt = isset($mastertunj[$pltKelasJabatan]) ? $mastertunj[$pltKelasJabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP()) * refPLT();
                    }
                }

                if ( ($jbtnDef  < $jbtnPlt ) ) {
                    $kriteriaPlt=3;
                    //$klsjbatan = max(array($queq->kelasjabatan,$pltKelasJabatan));
					$klsjbatan =$pltKelasJabatan;
                    /*if (date("Y",$datestart)>2016)
                    {*/
                        // old
                    //$unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* refTHP():0;
                    $unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* refTHP():0;
                    /*} else
                    {
                        $unkirplt= isset($mastertunj2016[$klsjbatan])?$mastertunj2016[$klsjbatan]* refTHP():0;
                    }*/
                    $unkir = 0;
                }
            }

            if ($queq->payable == 0){
                $unkir=0;
                $unkirplt=0;
            }

            $dataallay[] = array(
                'userid'   => $queq->userid,
                'empTitle' => $queq->title,
                'empID' => $queq->badgenumber,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'golru' => $queq->golru,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
                'npwp'   => $queq->npwp,
                'kelasjabatan' => $queq->kelasjabatan,
                'tunjanganprofesi' => $queq->tunjanganprofesi,
                'tmtprofesi' => $queq->tmtprofesi,
                'tunjangan' => $unkir,
                'jftstatus' => $queq->jftstatus,
                'jenisjabatan' => $queq->jenisjabatan,
                'jenispegawai' => $queq->jenispegawai,
                'kedudukan' => $queq->kedudukan,
                'tmtkedudukan' => $queq->tmtkedudukan,
                'deptid' => $queq->deptid,
                'tunjanganplt' => $unkirplt,
                'plt_deptid' => $pltDepid,
                'plt_eselon' => $pltEselon,
                'tmt_plt' => $PltHist['tmt'],//$queq->tmt_plt,
                'payable' => $queq->payable,
                'eselon' => $queq->eselon,
                'plt_jbtn' => $queq->plt_jbtn,
                'plt_sk' => $queq->plt_sk,
                'plt_kelasjabatan' => $pltKelasJabatan,//$queq->plt_kelasjabatan,
                'isplt' =>$isplt,
                'kriteriaPlt' =>$kriteriaPlt,
                'plt_deptname' => isset($deptar[$pltDepid])?$deptar[$pltDepid]:'',//isset($deptar[$queq->plt_deptid])?$deptar[$queq->plt_deptid]:'',
                'dipisah'=>0,
                'ishidden'=>0,
                'isViewIt'=>$isViewIt,
                'orgf'=>""
            );

            foreach($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
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
        $dataallaye = array();
        $abc=0;
        $dataview = '';
        $datavw = '';

        foreach($dataallay as $queqe) {

            $querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
            $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
            $dataarray = array();
            $totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
            $totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
            $totallibur  = 0; $tubel = 0; $totaltubel = 0; $totaltubelplt=0;$tunjtubel = 0; $tunjtubelplt = 0; $tgltubel = 0; $tunj1 = 0;
            $totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;
            $tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0; $totplt = 0;$tunjplt = 0;
            $ttlmsk = array(); $tunjangan = array(); $tunjanganplt = array();$tunbulplt = array();
            $tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
            $jft = array(); $jfto = 0; $jftp = array();
            $jpeg = array(); $jpego = 0; $jpegp = array();
            $kedu = array(); $keduo = 0; $kedup = array();
            $totaltunjplt =[];$totaltunj =[];
            foreach($querytempo->result() as $que) {
                $ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunj[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunjplt[date('mY', strtotime($que->date_shift))] = 0;
            }
            foreach($querytempo->result() as $que) {

                $kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
                if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
                $tunjang = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);
                if ($tunjang == 0) $tunjang = $mastertunj[$queqe['kelasjabatan']];

                if($tunjang!=0) {
                    if ($queqe['userid'] != 0 ) {
                        //if (date("Y", $datestart) > 2016) {
                        $queqe['tunjangan'] = $tunjang * refTHP();
                        /*} else {
                            $queqe['tunjangan'] = $tunjang;
                        }*/
                    } else {
                        $queqe['tunjangan'] = 0;
                    }
                    //baru
                    if ($queqe['kriteriaPlt']==2)
                    {
                        if ( $queqe['plt_kelasjabatan'] > $queqe['kelasjabatan']) {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = ($tjngnga * refTHP()) * refPLT();


                        } else
                        {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = $tjngnga * refTHP();
                        }
                    }
                    //CPNS aktif mulai 1 April 2019 sebesar 80%
                    $curenttime=strtotime($que->date_shift);
                    $bypasstime=strtotime('2019-04-01');
                    if ($curenttime>=$bypasstime){
                        if ($queqe['jftstatus']==1)
                        {
                            $queqe['tunjangan'] = $queqe['tunjangan'] * refTHPCP();
                        }
                    }
                    
                }
                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($que->flaghol == 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($tunbuli!=$queqe['tunjangan']) {
                    $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1;//0.5 : 1
                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                    $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'] * $kali;
                }

                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        //$lnpltdepid = $this->isEselon2($pltdepid);
                        //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                        $cpltdeptid =isOrgIn($pltdepid);

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        //$lorgtar = $this->isEselon2($orgtar);
                        //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                        $corgtar  = isOrgIn($orgtar);
                        //asalnya tidak sama
                        /* if ($cpltdeptid == $corgtar)
                         {
                             $queqe['ishidden'] = 1;
                         }*/

                        $queqe['ishidden'] = ($cpltdeptid != $corgtar)?1:0;
                    }
                }

                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined') {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            $cdeptid =isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            $corgtar  = isOrgIn($orgtar);

                            if ($dibagi)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }
                            if ($tunbuli != $queqe['tunjangan']) {
                                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali ;
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt']  * $kali ;
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        $cdeptid = isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  = isOrgIn($orgtar);
                        if ($dibagi)
                        {
                            if ($cdeptid == $corgtar) //satu tempat defenitif
                            {
                                $queqe['dipisah']= 1;
                            }
                            if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                            {
                                $queqe['dipisah']= 2;
                            }
                        }
                    }
                }

                //else
                //	$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];
                $jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
                if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;

                if($jfto!=$queqe['jftstatus'])
                    $jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];
                /* else
                    $jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */

                $jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
                if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;

                if($jpego!=$queqe['jenispegawai'])
                    $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];
                /* else
                    $jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */

                $kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
                if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;

                if($keduo!=$queqe['kedudukan'])
                    $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];
                /* else
                    $kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */

                $tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
                //if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;
                if($tunjangprof!=0) {
                    if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi']))
                    {
                        $queqe['tunjanganprofesi'] = $tunjangprof;
                    } else {
                        $queqe['tunjanganprofesi']=0;
                    }
                }

                //if($tunpro!=$queqe['tunjanganprofesi'])
                //    $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];
                if($tunpro!=$queqe['tunjanganprofesi']) {
                    //if (date("Y", $datestart) > 2016)
                    {
                        if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi'])) {
                            $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'] ;//* 0.5;
                        } else {
                            $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = 0;
                        }
                    } /*else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];;
                    }*/
                }
                /* else
                    $tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */

                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                $tunbuli = $queqe['tunjangan'] * $kali;
                $tunpro = $queqe['tunjanganprofesi'];
                $jfto = $queqe['jftstatus'];
                $jpego = $queqe['jenispegawai'];
                $keduo = $queqe['kedudukan'];
            }

            foreach($querytemp->result() as $que) 
            {
                $late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
                $totalpersenkurang = 0; $totaljadwal++;
                $day = date('D', strtotime($que->date_shift));
                $date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
                if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
                if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));


                if(isset($tunbul[date('mY', strtotime($que->date_shift))])) {
                    if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubel = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        //echo $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubelplt = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                    }
                } else {
                    $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                }


                if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12 || $queqe['kedudukan']==13) { $tottun = 0;$totplt=0;}
                if($queqe['jenispegawai']==3 || $queqe['jenispegawai']==4) { $tottun = 0;$totplt=0;}

                if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 ||
                        $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunjanganplt[date('mY', strtotime($que->date_shift))] / 2;
                        }
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tubel = 1;
                            $tgltubel = strtotime($queqe['tmtkedudukan']);
                        }
                    }
                }

                if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 || $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    $tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                    if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
                        if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))]) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        }
                        else {
                            //$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] - $tunjanganpro[date('mY', strtotime($que->date_shift))];
                            //$tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]*2) - $tunjanganpro[date('mY', strtotime($que->date_shift))])*refTHP();
                            $tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]) - $tunjanganpro[date('mY', strtotime($que->date_shift))]);
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        }
                    }
                }

                //ambil nilai pulang
                $early=0;
                if($que->early_departure!=0) {
                    $early = $que->early_departure;
                }
                if($que->late!=0) {
                    $late = $que->late;
                    if ($que->late >3660) {
                        $telat = $que->late;
                        $late = $telat <= 0 ? 0 : $telat;
                    } else {
                        if ($que->ot_after != 0) {
                            if ($que->ot_after > 3600)
                                $telat = $que->late - 3600;
                            else
                                $telat = $que->late - $que->ot_after;
                            $late = $telat <= 0 ? 0 : $telat;
                        }
                    }

                    if (date("Y",$datestart)<2017)
                    {
                        if($late < 1860) $krglate = 0; //kurang dari 30 menit
                        else if($late >= 1860 && $late < 3660) $krglate = 0.5; //31- 61 menit
                        else if($late >= 3660 && $late < 5460) $krglate = 1; //61- 91 menit
                        else if($late >= 5460) $krglate = 1.5; //91 menit ke atas
                    } else
                    {
                        if($late < 3660) { // < 61 menit
                            $krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = 0.5;
                        }
                        else if($late >= 3660) { // >=61 menit ke atas
                            $krglate = 1;
                        }
                        //jika terlambat >60 dan mengganti 60 menit
                        if ($que->late > 3660 )
                            $krglate = 1;

                        //berdasarkan referensi potongan untuk terlambat
                        $wry="select persentase FROM ref_potongan WHERE $late BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=1";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krglate = $qryPotongan->row()->persentase;
                            //$krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = $qryPotongan->row()->persentase;

                        }
                    }
                }

                if($que->early_departure!=0) {
                    $early = $que->early_departure;

                    //if (date("Y",$datestart)>2016)
                    {
                        if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;

                        //potongan PSW
                        $wry="select persentase FROM ref_potongan WHERE $early BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=2";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krgearly = $qryPotongan->row()->persentase;
                        }

                    } /*else {

                        if($early > 1 && $early < 1860) $krgearly = 0.5;
                        else if($early >= 1860 && $early < 3660) $krgearly = 1;
                        else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                        else if($early >= 5460) $krgearly = 1.5;
                    }*/
                }

                //if (date("Y",$datestart)>2016)
                {
                    if ($check_in == null) $krglate = 1;
                    if ($check_out == null) $krgearly = 1;
                } /*else {
                    if($check_in==null) $krglate = 1.5;
                    if($check_out==null) $krgearly = 1.5;
                }*/
                if($que->attendance == 'ALP') {
                    $krglate = 0;
                    $krgearly = 0;
                    //if (date("Y",$datestart)>2016) {
                    $krgalpa = 5;
                    /*} else {
                        $krgalpa = 3;
                    }*/
                }

                if($que->attendance == 'BLNK') {
                    $totalblnk++;
                }

                $s = 0;
                if(isset($bbar[$que->attendance])) {
                    $krglate = 0;
                    $krgearly = 0;
                    $krgalpa = 0;
                    $krgstatus = $pkrg[$que->attendance];
                    $s = 1;
                }

                if(isset($atar[$que->attendance])) {
                    if($que->attendance=='AT_SK01' ||
                        $que->attendance=='AT_AT1')
                        $krglate = 0;
                    else if($que->attendance=='AT_SK03' ||
                        $que->attendance=='AT_AT2')
                        $krgearly = 0;
                    else {
                        $krglate = 0;
                        $krgearly = 0;
                    }
                    $krgalpa = 0;
                    $krgstatus = $pkrga[$que->attendance];

                    switch ($akrga[$que->attendance])
                    {
                        case 1:
                            $krglate = 0;
                            break;
                        case 2:
                            $krgearly = 0;
                            break;
                        default:
                            $krglate = 0;
                            $krgearly = 0;
                            break;
                    }
                }

                if($krglate > 0 && $que->attendance!='NWK' &&
                    $que->attendance!='NWDS' && $que->workinholiday!=1 ) {
					
                    $totalpersenkurang = $totalpersenkurang + $krglate;
					$potongan = ($tunjtubel*80)/100;
					$tunj1 = $tunj1 + round((($krglate /100) * $potongan));
                    $tunj = $tunj + round((($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                    $tunjplt = $tunjplt + round((($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                }

                if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {

                    $totalpersenkurang = $totalpersenkurang + $krgearly;
					$potongan = ($tunjtubel*80)/100;
					$tunj1 = $tunj1 + round((($krgearly /100) * $potongan));
                    $tunj = $tunj + round((($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                    $tunjplt = $tunjplt + round((($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                }
                if($krgalpa != 0 && $que->workinholiday!=1) {

                    $totalpersenkurang = $totalpersenkurang + $krgalpa;
                    $totalalpa++;
					$potongan = ($tunjtubel*80)/100;
					$tunj1 = $tunj1 + round((($krgalpa /100) * $potongan));
                    $tunj = $tunj + round((($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                    $tunjplt = $tunjplt + round((($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                }

                if($krgstatus != 0  && $queqe['payable']!=0) {
                    $totalpersenkurang = $totalpersenkurang + $krgstatus;
                    if($que->attendance=='AB_12') $totalpembatalan++;
					$potongan = ($tunjtubel*80)/100;
					$tunj1 = $tunj1 + round((($krgstatus /100) * $potongan));
                    $tunj = $tunj + round((($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                    $tunjplt = $tunjplt + round((($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                }

                /*  khusus upacara status dibayarkan
                    tgl upacara ada ,tgl/waktu check in ada isinya atau kosong dan bukan Tubel
                */ 
                $arrAtt=array("AT_AT1","AT_SK01","AT_AT5","AT_AT6"); //tidak dipotong
                $kdAbsPtg="AB_12";//didipotong

                $ta = $this->report_model->getdetailroster3($que->userid,$que->date_shift);
                $pa = $ta->first_row();
                //echo ("J:".$que->date_shift2." => D:".$que->date_in2.' => T:'.$que->check_in2."<br>");
                if ($que->date_shift2!="" && $queqe['payable'] !=0  && $tubel==0)
                {			
                    //print_r($que->date_shift2);
                    $qa = $this->report_model->gettranslog2($que->date_shift2,$que->date_shift2,$que->userid,"Upacara");
                    $za = $qa->first_row();

                    //if ($que->date_in2!="" && $que->check_in2 !="" ) { //ada tap ke mesin
                    if ($za != '' or $za != null) {
                        //echo("1. ".$que->check_in2."<br>");
                        if($pa != '' or $pa != null) //cek jadwal
                        {
                            $jwdlawal = strtotime($pa->date_shift.' '.$pa->shift_in);
                            $jwdlakhir = strtotime($pa->date_shift.' '.$pa->shift_out);

                            $cekinupcara= strtotime($za->checktime);
                            if ($cekinupcara < $jwdlawal  || $cekinupcara > $jwdlakhir)
                            {
                                $jwdlawal = date('Y-m-d H:i:s',$jwdlawal);
                                $jwdlakhir = date('Y-m-d H:i:s',$jwdlakhir);
                                $cekinupcara = date('Y-m-d H:i:s',$cekinupcara);
                                //echo("11. Diluar $jwdlawal - $jwdlakhir / $cekinupcara <br>");
                                $ispotong=1;

                                if (in_array($pa->attendance,$arrAtt))
                                {
                                    $ispotong=0; 
                                    //echo("13. ATT <br>");
                                } else {
                                    //echo("14. ".substr($pa->attendance,0,2)."<br>");
                                    if (substr($pa->attendance,0,2)=="AB"){
                                        if ($kdAbsPtg==$pa->attendance){
                                            $ispotong=1; 
                                        } 
                                        else {
                                            $ispotong=0; 
                                        }
                                    }
                                    
                                }

                                //echo("15. ".$pa->attendance." - ".$ispotong." <br>");

                                if ($ispotong==1){
                                    $nkurang = refUpacara();
                                    $potongan = ($tunjtubel*80)/100;
                                    $alp = round((($nkurang /100) * $potongan));
                                    $upacara = $this->report_model->getdetailroster2($que->userid,$que->date_shift);
                                    $dataarray[] = array(
                                        'day'			=> $day,
                                        'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                                        'status'		=> 'Tidak melakukan upacara ',
                                        'nilai'			=> null,
                                        'pengurangan'	=> $nkurang,
                                        'total'			=> ($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                                        'totalplt'		=> ($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                                    );
                                    $totalpersenkurang = $totalpersenkurang + $nkurang;
                                    $tunj1 = $tunj1 + round((($nkurang /100) * $potongan));
                                    $tunj = $tunj + round(($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                                    $tunjplt = $tunjplt + (($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                                }
                            } 
                        }
                    //print_r($this->db->last_query());
                    } else {
                        //echo("0. <br>");
                        $ispotong=1;
                        if (in_array($pa->attendance,$arrAtt))
                        {
                            $ispotong=0; 
                            //echo("01. ATT <br>");
                        } else {
                            //echo("24. ".substr($pa->attendance,0,2)."<br>");
                            if (substr($pa->attendance,0,2)=="AB"){
                                if ($kdAbsPtg==$pa->attendance){
                                    $ispotong=1; 
                                } 
                                else {
                                    $ispotong=0; 
                                }
                            }
                        }

                        if ($ispotong==1){
                            $nkurang = refUpacara();
                            $potongan = ($tunjtubel*80)/100;
                            $alp = round((($nkurang /100) * $potongan));
                            $upacara = $this->report_model->getdetailroster2($que->userid,$que->date_shift);
                            $dataarray[] = array(
                                'day'			=> $day,
                                'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                                'status'		=> 'Tidak melakukan upacara ',
                                'nilai'			=> null,
                                'pengurangan'	=> $nkurang,
                                'total'			=> ($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                                'totalplt'		=> ($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                            );
                            $totalpersenkurang = $totalpersenkurang + $nkurang;
                            $tunj1 = $tunj1 + round((($nkurang /100) * $potongan));
                            $tunj = $tunj + round(($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                            $tunjplt = $tunjplt + (($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        }


                    }
                }

                $totalpersensemua = $totalpersensemua + $totalpersenkurang;
                $totalmasuk = array_sum($ttlmsk);

                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1 ) {
                    if($tubel==1) {
                        //$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltubelplt = $totaltubelplt+ $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }

                if($que->flaghol == 1) {
                    if($tubel==1) {
                        //$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        //$totaltubelplt = $totaltubelplt + $totaltunjplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }

                if ($tubel==1)
                {
                    if ($que->attendance == 'NWK' || $que->flaghol==1 || $que->attendance=="")
                    {

                    } else
                    {
                        $totaltubel = $totaltubel + $tunjtubel*0.5 ;//$tunjangan[date('mY', strtotime($que->date_shift))];
                        //echo $que->date_shift." ".$que->attendance." ".$tunjtubel*0.5."<br>";
                    }
                }
            }
			
			/* if($queqe['jftstatus'] == 1 AND $queqe['empTitle'] != ''){
				$tottun = ($queqe['tunjangan']*80)/100;
			}else{
				$tottun = isset($totaltunj)?array_sum($totaltunj):0;
				$totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;
			} */
			
            $tottun = isset($totaltunj)?array_sum($totaltunj):0;
            $totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;

            if($tubel==1) {

                /*$tunj = $tunj + $totaltubel;
                $tottun = $tunjtubel;*/
                $tunj = 0;
                $tottun = ($totaltubel/$totalmasuk);
                $totplt= $tunjtubelplt;
            }

            if($totalalpa == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
            }

            if($totalpembatalan == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
            }

            $totalsemua = $totalalpa + $totalpembatalan;

            if($totalsemua == $totalmasuk) {
               $tottun = isset($totaltunj)?array_sum($totaltunj):0;
               $totplt = 0;
            }

            if($totalblnk == $totaljadwal) {
                $tottun = 0;
                $totplt = 0;
            }
			if($queqe['jftstatus'] == 1 AND $queqe['empTitle'] != ''){
				$to = $tottun==0?0:$tunj1;
			}else{
				$to = $tottun==0?0:$tunj;
			}
            $dataallaye[] = array(
                'userid'   	=> $queqe['userid'],
                'empTitle' 	=> $queqe['empTitle'],
                'empID' 	=> $queqe['empID'],
                'empHire'	=> $queqe['empHire'],
                'empName' 	=> $queqe['empName'],
                'deptName' 	=> $queqe['deptName'],
                'npwp'   => $queqe['npwp'],
                'kelasjabatan' 			=> $queqe['kelasjabatan'],
                'golongan' 				=> $queqe['golru'],
                'isplt'                 =>$queqe['isplt'],
                'plt_deptname' => $queqe['plt_deptname'],
                'kriteriaPlt' => $queqe['kriteriaPlt'],
                'plt_jbtn' => $queqe['plt_jbtn'],
                'plt_kelasjabatan' => $queqe['plt_kelasjabatan'],
                'byuser'=>  $byuser,
                "dipisah" =>$queqe['dipisah'],
                "ishidden" =>$queqe['ishidden'],
                'isViewIt'=>$queqe['isViewIt'],
                'tunjangan' 			=> $tottun,
                'tunjanganplt' =>       $totplt,
                'totaltunjangan'		=>  $tottun==0?0:$tunj,
                'totaltunjanganplt'		=> $totplt==0?0:$tunjplt,
                "totaltunj" =>$totaltunj,
                "totaltunjplt" => $totaltunjplt
            );
        }

        $data = array(
            //"dateinfo" =>   date('d-m-Y', $datestart)." s/d ".date('d-m-Y', $datestop),
            "dateinfo" => strtoupper(format_date_ind(date('Y-m-d', $datestart))." s/d ".format_date_ind(date('Y-m-d', $datestop))),
            "cominfo" =>    $company,
            "empinfo" =>    $dataallaye,
            "data" =>       $deptar[$departemen],
            "excelid"=>     $excelid,
            "pdfid"=>     $ispdf,
            "showpdf"=>$showpdf,
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

        if($excelid==1) {
            $dataview = $this->load->view("laporan2",$data,true);
            $datavw = $datavw.$dataview;
        } else {
            $this->load->view("laporan2",$data);
        }

        if ($ispdf==1) {
            $this->load->library('mpdf');
            $this->mpdf =
                new mPDF('',    // mode - default ''
                    '',    // format - A4, for example, default ''
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

            $stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
            $stylesheet = file_get_contents( $stylepdf);
            $this->mpdf->WriteHTML($stylesheet,1);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->WriteHTML($datavw);

            $this->mpdf->Output();
            die();
        }

        if($excelid==1) {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=rekapitulasitunjangankinerja.xls");
            //echo "$datavw";

            echo '<html>';
            echo '<head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Laporan Rekapitulasi Tunjangan Kinerja</title>
                </head>
                <body>';
            echo $datavw;
            echo '</body></html>';
        }
    }

    function view3()
    {
        //$myThnBln=$this->input->post('start');
        //$postdatestart = $this->input->post('start')."-01";
        // $postdatestop = date("Y-m-t", strtotime($postdatestart)); //$this->input->post('end');

        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));

        $myThnBln = date("Y-m", strtotime($postdatestart));

        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');

        $showpdf= $this->input->post('showpdf');
        $userid = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        $departemen = $this->input->post('org')!='undefined'?$this->input->post('org'):"1";
        $mastertunj = array();

        $this->session->set_userdata('tahundata',date("Y",$datestart));

        /* $sqlv = "select * from mastertunjangan";
        $validdate = strtotime('01-11-2018');
        if ($datestart < $validdate )
        {
            $sqlv = "select * from mastertunjangan_2018";
        } */
        $nmTble= namaTableTunjangan($postdatestart);
        $sqlv = "select * from ".$nmTble;
        $query = $this->db->query($sqlv);
        foreach ($query->result() as $que) {
            $mastertunj[$que->kelasjabatan] = $que->tunjangan;
        }

        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(convertToArray($this->session->userdata('s_area'))):array();

        $tbar = array();
        $bbar = array();
        $holar = array();
        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $pkrga = array();
        $akrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname,
                'kategori'	=> $at->status_kategori_id
            );
            $pkrga[$at->atid] = $at->value;
            $akrga[$at->atid] = $at->ign_to;
        }

        $pkrgK = array();
        $pkrg = array();
        $absrecap = $this->report_model->getabs();
        foreach($absrecap->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname,
                'kategori'	=> $bs->status_kategori_id
            );
            $pkrg[$bs->abid] = $bs->value;
            if ($bs->status_kategori_id==12) {
                $pkrgK[$bs->abid] = $bs->value;
            }
        }

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $yo = 0;
        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetailsxx($userar,$stspeg,$jnspeg);
            $yo=2;
        } else if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg,$jnspeg);
            $yo=1;
        }

        $byuser= $userid!='undefined'?1:0;

        $aten = array();
        $aben = array();

        $dataallay = array();

        foreach($queryemp->result() as $queq) {

            $unkir= isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0;

            $isplt=0;$isViewIt=1;
            $PltHist = $this->report_model->getkelasplttukinhist($queq->userid,$postdatestart);
            //if (isset($queq->tmt_plt) && isset($queq->plt_deptid) && isset($queq->plt_eselon))
            if (isset($PltHist['tmt']) && isset($PltHist['deptid']) && isset($PltHist['eselon']))
            {
                if ($PltHist['tmt'] !='0000-00-00') {

                    //$time = strtotime($queq->tmt_plt);
                    $time = strtotime($PltHist['tmt']);
                    //$final = strtotime("+1 month", $time);
                    if ($datestart >= strtotime(date("Y-m-01",$time))) {
                        if ((intval(date('d', $time)) < 6) && date("Y-M", $datestart) === date('Y-M', $time)) {
                            $isplt = 1;
                        } else {
                            $time2 = strtotime(date('Y-m-01', $time));
                            $final = strtotime("+1 month", $time2);
                            //echo $queq->tmt_plt . " " . $datestart . " " . $final;
                            if ($datestart >= $final) {
                                $isplt = 1;
                            }
                            else {
                                //if (!in_array($queq->deptid, $orgid))
                                if (!in_array($PltHist['deptid'], $orgid))
                                {
                                    continue;
                                }
                            }
                        }
                    } else {
                        $isViewIt=0;
                        //if (!in_array($queq->deptid, $orgid))
                        if (!in_array($PltHist['deptid'], $orgid))
                        {
                            continue;
                        }
                    }
                }
            }

            $pltEselon = isset($PltHist['eselon']) ? $PltHist['eselon']:$queq->plt_eselon;
            $pltDepid= isset($PltHist['deptid']) ? $PltHist['deptid']:$queq->plt_deptid;
            $eselonDef= isset($PltHist['eselondef']) ? $PltHist['eselondef']:$queq->eselon;

            $jbtnDef = strval(konversiEselonBaru($eselonDef));
            $jbtnPlt = strval(konversiEselonBaru($pltEselon));

            $unkirplt = 0;
            if(isset($mastertunj[$queq->kelasjabatan])){
                $ntunj = $mastertunj[$queq->kelasjabatan];
            }
            if(isset($ntunj)){
                $unkirplt = $ntunj * refTHP();
            }

            //$unkirplt= isset($mastertunj[$queq->kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] * 0.5:0;
            $pltKelasJabatan = isset($PltHist['kelas']) ? $PltHist['kelas']:$queq->plt_kelasjabatan;
            $kriteriaPlt = 0;
            if ($isplt==1) {
                if ( ($jbtnDef  > $jbtnPlt) ) {
                    $kriteriaPlt=1;
                    //$unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan] :0;
                    $unkirplt= isset($mastertunj[$pltKelasJabatan])?$mastertunj[$pltKelasJabatan] :0;
                    $unkirplt= ($unkirplt * refTHP()) * refPLT();
                }
                if ( ($jbtnDef  == $jbtnPlt ) ) {
                    $kriteriaPlt=2;
                    /*$unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan]:0;
                    $unkirplt= ($unkirplt * refTHP()) * refPLT();*/
                    if ($pltKelasJabatan > $queq->kelasjabatan) {
                        //$unkirplt = isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] : 0;
                        $unkirplt = isset($mastertunj[$pltKelasJabatan]) ? $mastertunj[$pltKelasJabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP());
                        $unkir = ($unkir * refTHP()) * refPLT();
                    } else
                    {
                        //$unkirplt = isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] : 0;
                        $unkirplt = isset($mastertunj[$pltKelasJabatan]) ? $mastertunj[$pltKelasJabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP()) * refPLT();
                    }
                }

                if ( ($jbtnDef  < $jbtnPlt ) ) {
                    $kriteriaPlt=3;
                    //$klsjbatan = max(array($queq->kelasjabatan,$queq->plt_kelasjabatan));
                    //$klsjbatan = max(array($queq->kelasjabatan,$pltKelasJabatan));
					$klsjbatan =$pltKelasJabatan;
                    //if (date("Y",$datestart)>2016)
                    //{
                    //$unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* refTHP():0;
                    //old
                    $unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* refTHP():0;
                    //} /*else
                    //{
                    //    $unkirplt= isset($mastertunj2016[$klsjbatan])?$mastertunj2016[$klsjbatan]* refTHP():0;
                    //}*/
                    $unkir = 0;
                }
            }

            if ($queq->payable == 0){
                $unkir=0;
                $unkirplt=0;
            }

            $dataallay[] = array(
                'userid'   => $queq->userid,
                'npwp'   => $queq->npwp,
                'no_rekening'   => $queq->no_rekening,
                'empTitle' => $queq->title,
                'empID' => $queq->badgenumber,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'golru' => $queq->golru,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
                'kelasjabatan' => $queq->kelasjabatan,
                'tunjanganprofesi' => $queq->tunjanganprofesi,
                'tmtprofesi' => $queq->tmtprofesi,
                'tunjangan' => $unkir,
                'jftstatus' => $queq->jftstatus,
                'jenisjabatan' => $queq->jenisjabatan,
                'jenispegawai' => $queq->jenispegawai,
                'kedudukan' => $queq->kedudukan,
                'tmtkedudukan' => $queq->tmtkedudukan,
                'deptid' => $queq->deptid,
                'tunjanganplt' => $unkirplt,
                'plt_deptid' => $pltDepid,//$queq->plt_deptid,
                'plt_eselon' => $pltEselon,//$queq->plt_eselon,
                'tmt_plt' => $PltHist['tmt'],//$queq->tmt_plt,
                'payable' => $queq->payable,
                'eselon' => $queq->eselon,
                'plt_jbtn' => $queq->plt_jbtn,
                'plt_sk' => $queq->plt_sk,
                'plt_kelasjabatan' => $pltKelasJabatan,//$queq->plt_kelasjabatan,
                'isplt' =>$isplt,
                'kriteriaPlt' =>$kriteriaPlt,
                'plt_deptname' => isset($deptar[$pltDepid])?$deptar[$pltDepid]:'',isset($deptar[$queq->plt_deptid])?$deptar[$queq->plt_deptid]:'',
                'dipisah'=>0,
                'ishidden'=>0,
                'isViewIt'=>$isViewIt,
                'orgf'=>"",
                'payable ' =>$queq->payable
            );

            foreach($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
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
        $dataallaye = array();
        $abc=0;
        $dataview = '';
        $datavw = '';

        foreach($dataallay as $queqe) {

            $querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
            $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
            $dataarray = array();
            $totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
            $totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
            $totallibur  = 0; $tubel = 0; $totaltubel = 0; $totaltubelplt=0;$tunjtubel = 0; $tunjtubelplt = 0; $tgltubel = 0; $tunj1 = 0;
            $totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;
            $tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0; $totplt = 0;$tunjplt = 0;
            $ttlmsk = array(); $tunjangan = array(); $tunjanganplt = array();$tunbulplt = array();
            $tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
            $jft = array(); $jfto = 0; $jftp = array();
            $jpeg = array(); $jpego = 0; $jpegp = array();
            $kedu = array(); $keduo = 0; $kedup = array();
            $totaltunjplt =[];$totaltunj =[];
            $jumlalpa=0; $jumlate=0;$jumearly=0;
            $jumijin=0;
            $jumTdkAbsen=0;
            $jumTdkAbsenPlt=0;
            $jumlalpaPlt=0;
            $jumlatePlt=0;$jumearlyPlt=0;
            $jumijinPlt=0;$totallatePlt=0;
            $jumTb=0;
            $jumUpacara=0;
            $jumUpacaraPlt=0;
            foreach($querytempo->result() as $que) {
                $ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunj[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunjplt[date('mY', strtotime($que->date_shift))] = 0;
            }

            foreach($querytempo->result() as $que) {

                $kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
                if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
                $tunjang = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);
                if ($tunjang == 0) $tunjang = $mastertunj[$queqe['kelasjabatan']];

                if($tunjang!=0) {
                    if ($queqe['userid'] != 0 ) {
                        //if (date("Y", $datestart) > 2016) {
                        $queqe['tunjangan'] = $tunjang * refTHP();
                        /*} else {
                            $queqe['tunjangan'] = $tunjang;
                        }*/
                    } else {
                        $queqe['tunjangan'] = 0;
                    }
                    //baru
                    if ($queqe['kriteriaPlt']==2)
                    {
                        if ( $queqe['plt_kelasjabatan'] > $queqe['kelasjabatan']) {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = ($tjngnga * refTHP()) * refPLT();
                        } else
                        {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = $tjngnga * refTHP();
                        }
                    }
                    //CPNS aktif mulai 1 April 2019 sebesar 80%
                    $curenttime=strtotime($que->date_shift);
                    $bypasstime=strtotime('2019-04-01');
                    if ($curenttime>=$bypasstime){
                        if ($queqe['jftstatus']==1)
                        {
                            $queqe['tunjangan'] = $queqe['tunjangan'] * refTHPCP();
                        }
                    }
                    
                }
                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($que->flaghol == 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($tunbuli!=$queqe['tunjangan']) {
                    $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))))? 1 : 1; //0.5 : 1
                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali ;
                    $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'] * $kali ;
                }

                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  = isOrgIn($orgtar);
                        //asalnya tidak sama
                        /*if ($cpltdeptid == $corgtar)
                        {
                           $queqe['ishidden'] = 1;
                        }*/
                        $queqe['ishidden'] = ($cpltdeptid != $corgtar)?1:0;
                    }
                }

                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined') {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            $cdeptid =isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            $corgtar  = isOrgIn($orgtar);

                            if ($dibagi)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }
                            if ($tunbuli != $queqe['tunjangan']) {
                                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))))? 1 : 1; //0.5 : 1
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt']  * $kali;
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        $cdeptid = isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  = isOrgIn($orgtar);
                        if ($dibagi)
                        {
                            if ($cdeptid == $corgtar) //satu tempat defenitif
                            {
                                $queqe['dipisah']= 1;
                            }
                            if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                            {
                                $queqe['dipisah']= 2;
                            }
                        }
                    }
                }

                //else
                //	$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];
                $jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
                if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;

                if($jfto!=$queqe['jftstatus'])
                    $jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];
                /* else
                    $jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */

                $jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
                if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;

                if($jpego!=$queqe['jenispegawai'])
                    $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];
                /* else
                    $jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */

                $kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
                if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;

                if($keduo!=$queqe['kedudukan'])
                    $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];
                /* else
                    $kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */

                $tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
                //if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;
                if($tunjangprof!=0) {
                    if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi']))
                    {
                        $queqe['tunjanganprofesi'] = $tunjangprof;
                    } else {
                        $queqe['tunjanganprofesi']=0;
                    }
                }

                //if($tunpro!=$queqe['tunjanganprofesi'])
                //   $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];
                if($tunpro!=$queqe['tunjanganprofesi']) {
                    //if (date("Y", $datestart) > 2016) {
                    //if (date("Y", $datestart) > 2016) {
                    if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi'])) {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'] ;//* 0.5;
                    } else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = 0;
                    }
                    /*} else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];;
                    }*/

                    /*} else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];
                    }*/
                }
                /* else
                    $tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */

                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1

                $tunbuli = $queqe['tunjangan'] * $kali;
                $tunpro = $queqe['tunjanganprofesi'];
                $jfto = $queqe['jftstatus'];
                $jpego = $queqe['jenispegawai'];
                $keduo = $queqe['kedudukan'];
            }

            foreach($querytemp->result() as $que)
            {
                $late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
                $totalpersenkurang = 0; $totaljadwal++;
                $tottelat=0;
                $day = date('D', strtotime($que->date_shift));
                $date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
                if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
                if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));

                if(isset($tunbul[date('mY', strtotime($que->date_shift))])) {
                    if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubel = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        //echo $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubelplt = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                    }
                } else {
                    $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                }


                if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12 || $queqe['kedudukan']==13) { $tottun = 0;$totplt=0;}
                if($queqe['jenispegawai']==3 || $queqe['jenispegawai']==4) { $tottun = 0;$totplt=0;}

                if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 ||
                        $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunjanganplt[date('mY', strtotime($que->date_shift))] / 2;
                        }
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tubel = 1;
                            $tgltubel = strtotime($queqe['tmtkedudukan']);
                        }
                    }
                }

                if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 || $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    $tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                    if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
                        if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))]) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        }
                        else {
                            //$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] - $tunjanganpro[date('mY', strtotime($que->date_shift))];
                            //$tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]*2) - $tunjanganpro[date('mY', strtotime($que->date_shift))])*refTHP();
                            $tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]) - $tunjanganpro[date('mY', strtotime($que->date_shift))]);
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        }
                    }
                }

                //ambil nilai pulang
                $early=0;
                if($que->early_departure!=0) {
                    $early = $que->early_departure;
                }
                if($que->late!=0) {
                    $late = $que->late;
                    if ($que->late >3660) {
                        $telat = $que->late;
                        $late = $telat <= 0 ? 0 : $telat;
                    } else {
                        if ($que->ot_after != 0) {
                            if ($que->ot_after > 3600)
                                $telat = $que->late - 3600;
                            else
                                $telat = $que->late - $que->ot_after;
                            $late = $telat <= 0 ? 0 : $telat;
                        }
                    }

                    if (date("Y",$datestart)<2017)
                    {
                        if($late < 1860) $krglate = 0; //kurang dari 30 menit
                        else if($late >= 1860 && $late < 3660) $krglate = 0.5; //31- 61 menit
                        else if($late >= 3660 && $late < 5460) $krglate = 1; //61- 91 menit
                        else if($late >= 5460) $krglate = 1.5; //91 menit ke atas
                    } else
                    {
                        if($late < 3660) { // < 61 menit
                            $krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = 0.5;
                        }
                        else if($late >= 3660) { // >=61 menit ke atas
                            $krglate = 1;
                        }
                        //jika terlambat >60 dan mengganti 60 menit
                        if ($que->late > 3660 )
                            $krglate = 1;

                        //berdasarkan referensi potongan untuk terlambat
                        $wry="select persentase FROM ref_potongan WHERE $late BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=1";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krglate = $qryPotongan->row()->persentase;
                            //$krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = $qryPotongan->row()->persentase;
                        }
                    }
                }

                if($que->early_departure!=0) {
                    $early = $que->early_departure;

                    //if (date("Y",$datestart)>2016) {
                    if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;

                    //potongan PSW
                    $wry="select persentase FROM ref_potongan WHERE $early BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=2";
                    $qryPotongan = $this->db->query($wry);
                    if ($qryPotongan->num_rows()>0) {
                        $krgearly = $qryPotongan->row()->persentase;
                    }

                    /*} else {

                        if($early > 1 && $early < 1860) $krgearly = 0.5;
                        else if($early >= 1860 && $early < 3660) $krgearly = 1;
                        else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                        else if($early >= 5460) $krgearly = 1.5;
                    }*/
                }

                //if (date("Y",$datestart)>2016) {
                if ($check_in == null) $krglate = 1;
                if ($check_out == null) $krgearly = 1;
                /*} else {
                    if($check_in==null) $krglate = 1.5;
                    if($check_out==null) $krgearly = 1.5;
                }*/
                if($que->attendance == 'ALP') {
                    $krglate = 0;
                    $krgearly = 0;
                    //if (date("Y",$datestart)>2016) {
                    $krgalpa = 5;
                    /*} else {
                        $krgalpa = 3;
                    }*/
                }

                if($que->attendance == 'BLNK') {
                    $totalblnk++;
                }

                $s = 0;
                if(isset($bbar[$que->attendance])) {
                    $krglate = 0;
                    $krgearly = 0;
                    $krgalpa = 0;
                    $krgstatus = $pkrg[$que->attendance];
                    $s = 1;
                }

                if(isset($atar[$que->attendance])) {
                    if($que->attendance=='AT_SK01' ||
                        $que->attendance=='AT_AT1')
                        $krglate = 0;
                    else if($que->attendance=='AT_SK03' ||
                        $que->attendance=='AT_AT2')
                        $krgearly = 0;
                    else {
                        $krglate = 0;
                        $krgearly = 0;
                    }
                    $krgalpa = 0;
                    $krgstatus = $pkrga[$que->attendance];

                    switch ($akrga[$que->attendance])
                    {
                        case 1:
                            $krglate = 0;
                            break;
                        case 2:
                            $krgearly = 0;
                            break;
                        default:
                            $krglate = 0;
                            $krgearly = 0;
                            break;
                    }
                }
				
                if($krglate > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 )
                {
                    $totalpersenkurang = $totalpersenkurang + $krglate;
					$potongan = ($tunjtubel*80)/100;
					if($queqe['jftstatus'] == 1){
						$alp = round((($krglate /100) * $potongan));
					}else{
						$alp = round((($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
					}
                    $tunj += $alp;
                    $tunjplt += (($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    //terlambat
                    if ($check_in == null) {
						//$t = (($krglate / 100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
						//$y = round($t);
						$jumTdkAbsen += $alp;
                        $jumTdkAbsenPlt += (($krglate / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    } else {
                        $jumlate += $alp;
                        $jumlatePlt += (($krglate / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    }

                    $totallate += round((($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
                    $totallatePlt += (($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

				
                if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {

                    $totalpersenkurang = $totalpersenkurang + $krgearly;
					$potongan = ($tunjtubel*80)/100;
					if($queqe['jftstatus'] == 1){
						$alp = round((($krgearly /100) * $potongan));
					}else{
						$alp = round((($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
					}
                    $tunj +=  $alp;
                    $tunjplt += (($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

                    if ($check_out  == null) {
                        $jumTdkAbsen += $alp;
                        $jumTdkAbsenPlt += (($krgearly / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    } else {
                        $jumearly += $alp;
                        $jumearlyPlt += (($krgearly / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    }
                }

				
                if($krgalpa != 0 && $que->workinholiday!=1) {
					$totalpersenkurang = $totalpersenkurang + $krgalpa;
                    $totalalpa++;
					$potongan = ($tunjtubel*80)/100;
					if($queqe['jftstatus'] == 1){
						$alp = round((($krgalpa /100) * $potongan));
					}else{
						$alp = round((($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
					}
					$tunj += $alp;
                    $tunjplt += (($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    //alpha
                    $jumlalpa += $alp;
                    $jumlalpaPlt += (($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgstatus != 0  && $queqe['payable']!=0) {
                    $totalpersenkurang = $totalpersenkurang + $krgstatus;
					$potongan = ($tunjtubel*80)/100;
					if($queqe['jftstatus'] == 1){
						$alp = round((($krgstatus /100) * $potongan));
					}else{
						$alp = round((($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
					}
                    if($que->attendance=='AB_12')
                    {
                        $totalpembatalan++;

                    }
                    $tunj += $alp;
                    $tunjplt += (($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

                    $jumijin += $alp;
                    $jumijinPlt += (($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }
                
                /*  khusus upacara status dibayarkan
                    tgl upacara,tgl check in kosong dan bukan Tubel
                */
                /* if (isset($que->date_shift2) && !isset($que->date_in2) 
                    && !isset($que->check_in2) && $queqe['payable'] !=0 && $tubel==0)
                {
					$ta = $this->report_model->getdetailroster3($que->userid,$que->date_shift);
					$pa = $ta->first_row();
					$qa = $this->report_model->gettranslog2($que->date_shift2,$que->date_shift2,$que->userid,"Upacara");
					$za = $qa->first_row();
					
					if($za == '' or $za == null){
						if($pa->attendance == 'UPC' OR isset($pa->check_in) OR isset($pa->date_in)){
							$w = strtotime($que->check_in);
							$e = strtotime($pa->shift_in);
							if($e > $w){
								$nkurang = refUpacara();
								$potongan = ($tunjtubel*80)/100;
								if($queqe['jftstatus'] == 1){
									$alp = round((($nkurang /100) * $potongan));
								}else{
									$alp = round((($nkurang /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
								}
								$totalpersenkurang = $totalpersenkurang + $nkurang;
								$tunj += $alp;
								$tunjplt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

								$jumUpacara += $alp;
								$jumUpacaraPlt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
							}
						}
						if($pa->attendance == 'AT_AT1' OR $pa->attendance == 'AT_SK01'){
							$w = strtotime($que->check_in);
							$e = strtotime($pa->shift_in);
							if($e > $w){
								$nkurang = refUpacara();
								$potongan = ($tunjtubel*80)/100;
								if($queqe['jftstatus'] == 1){
									$alp = round((($nkurang /100) * $potongan));
								}else{
									$alp = round((($nkurang /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
								}
								$totalpersenkurang = $totalpersenkurang + $nkurang;
								$tunj += $alp;
								$tunjplt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

								$jumUpacara += $alp;
								$jumUpacaraPlt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
							}
						}
						if($pa->attendance == 'AT_AT2'  OR $pa->attendance == 'AT_SK03'){
							$w = strtotime($que->check_in);
							$e = strtotime($pa->shift_in);
							$b = strtotime($pa->shift_out);
							if($e < $w){
								$nkurang = refUpacara();
								$potongan = ($tunjtubel*80)/100;
								if($queqe['jftstatus'] == 1){
									$alp = round((($nkurang /100) * $potongan));
								}else{
									$alp = round((($nkurang /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
								}
								$totalpersenkurang = $totalpersenkurang + $nkurang;
								$tunj += $alp;
								$tunjplt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

								$jumUpacara += $alp;
								$jumUpacaraPlt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
							}
						}
						
					}
					
                } */
                /*  khusus upacara status dibayarkan
                    tgl upacara ada ,tgl/waktu check in ada isinya atau kosong dan bukan Tubel
                */ 
                $arrAtt=array("AT_AT1","AT_SK01","AT_AT5","AT_AT6"); //tidak dipotong
                $kdAbsPtg="AB_12";//didipotong

                $ta = $this->report_model->getdetailroster3($que->userid,$que->date_shift);
                $pa = $ta->first_row();
                //echo ("J:".$que->date_shift2." => D:".$que->date_in2.' => T:'.$que->check_in2."<br>");
                if ($que->date_shift2!="" && $queqe['payable'] !=0  && $tubel==0)
                {			
                    //print_r($que->date_shift2);
                    $qa = $this->report_model->gettranslog2($que->date_shift2,$que->date_shift2,$que->userid,"Upacara");
                    $za = $qa->first_row();

                    //if ($que->date_in2!="" && $que->check_in2 !="" ) { //ada tap ke mesin
                    if ($za != '' or $za != null) {
                        //echo("1. ".$que->check_in2."<br>");
                        if($pa != '' or $pa != null) //cek jadwal
                        {
                            $jwdlawal = strtotime($pa->date_shift.' '.$pa->shift_in);
                            $jwdlakhir = strtotime($pa->date_shift.' '.$pa->shift_out);

                            $cekinupcara= strtotime($za->checktime);
                            if ($cekinupcara < $jwdlawal  || $cekinupcara > $jwdlakhir)
                            {
                                $jwdlawal = date('Y-m-d H:i:s',$jwdlawal);
                                $jwdlakhir = date('Y-m-d H:i:s',$jwdlakhir);
                                $cekinupcara = date('Y-m-d H:i:s',$cekinupcara);
                                //echo("11. Diluar $jwdlawal - $jwdlakhir / $cekinupcara <br>");
                                $ispotong=1;

                                if (in_array($pa->attendance,$arrAtt))
                                {
                                    $ispotong=0; 
                                    //echo("13. ATT <br>");
                                } else {
                                    //echo("14. ".substr($pa->attendance,0,2)."<br>");
                                    if (substr($pa->attendance,0,2)=="AB"){
                                        if ($kdAbsPtg==$pa->attendance){
                                            $ispotong=1; 
                                        } 
                                        else {
                                            $ispotong=0; 
                                        }
                                    }
                                }

                                if ($ispotong==1){
                                    $nkurang = refUpacara();
                                    $potongan = ($tunjtubel*80)/100;
                                    $alp = round((($nkurang /100) * $potongan));

                                    $totalpersenkurang = $totalpersenkurang + $nkurang;
								    $tunj += (($nkurang / 100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                                    $tunjplt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                                    
                                    $jumUpacara += (($nkurang / 100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
								    $jumUpacaraPlt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                                }
                            } 
                        }
                    //print_r($this->db->last_query());
                    } else {
                        //echo("0. <br>");
                        $ispotong=1;
                        if (in_array($pa->attendance,$arrAtt))
                        {
                            $ispotong=0; 
                            //echo("01. ATT <br>");
                        } else {
                            //echo("24. ".substr($pa->attendance,0,2)."<br>");
                            if (substr($pa->attendance,0,2)=="AB"){
                                if ($kdAbsPtg==$pa->attendance){
                                    $ispotong=1; 
                                } 
                                else {
                                    $ispotong=0; 
                                }
                            }
                        }

                        if ($ispotong==1){
                            $nkurang = refUpacara();
                            $potongan = ($tunjtubel*80)/100;
                            $alp = round((($nkurang /100) * $potongan));
                            $totalpersenkurang = $totalpersenkurang + $nkurang;
                            $tunj += (($nkurang / 100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                            $tunjplt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                            
                            $jumUpacara += (($nkurang / 100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                            $jumUpacaraPlt += (($nkurang / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        }
                    }
                }

                //DPK,Meninggal+Pembatalan

                if(($que->attendance=='AB_18' || $que->attendance=='AB_19' || $que->attendance=='AB_12' ) && $queqe['payable']!=0) {
                    //if (isset($pkrgK[$que->attendance]) && $queqe['payable']!=0 && $que->workinholiday!=1) {
                    if (isset($pkrg[$que->attendance])) {
                        //echo $que->attendance;
                        $krgalpa = $pkrg[$que->attendance];
						$potongan = ($tunjtubel*80)/100;
						if($queqe['jftstatus'] == 1){
							$alp = round((($krgalpa /100) * $potongan));
						}else{
							$alp = round((($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])));
						}
                        $tunj += $alp;
                        $tunjplt += (($krgalpa / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        //alpha
                        $jumlalpa += $alp;
                        $jumlalpaPlt += (($krgalpa / 100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    }
                }

                $totalpersensemua = $totalpersensemua + $totalpersenkurang;
                $totalmasuk = array_sum($ttlmsk);

                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1 ) {
                    if($tubel==1) {
                        //$totaltubel += $tunjangan[date('mY', strtotime($que->date_shift))];
                        //$totaltubelplt += $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }

                if($que->flaghol == 1) {
                    if($tubel==1) {
                        //$totaltubel += $tunjangan[date('mY', strtotime($que->date_shift))];
                        //$totaltubelplt += $totaltunjplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }

                if ($tubel==1)
                {
                    if ($que->attendance == 'NWK' || $que->flaghol==1 || $que->attendance=="")
                    {

                    } else
                    {
                        $totaltubel = $totaltubel + $tunjtubel* (50/100) ;//$tunjangan[date('mY', strtotime($que->date_shift))];
                        //echo $que->date_shift." ".$que->attendance." ".$tunjtubel*0.5."<br>";
                    }
                }
            }

			/* if($queqe['jftstatus'] == 1 AND $queqe['empTitle'] != ''){
				$tottun = ($queqe['tunjangan']*80)/100;
			}else{
				$tottun = isset($totaltunj)?array_sum($totaltunj):0;
				$totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;
            }
             */
            $tottun = isset($totaltunj)?array_sum($totaltunj):0;
			$totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;
           

            if($tubel==1) {

                /*$tunj = $tunj + $totaltubel;
                $tottun = $tunjtubel;*/

                $tottun = ($totaltubel/$totalmasuk);///$totalmasuk);
                $tunj = 0;
                $totplt= $tunjtubelplt;

                $jumTb = $tottun;

                $tottun = (100/50)*$jumTb;
            }

            if($totalalpa == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
            }

            if($totalpembatalan == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
            }

            $totalsemua = $totalalpa + $totalpembatalan;

            if($totalsemua == $totalmasuk) {
                $tottun = isset($totaltunj)?array_sum($totaltunj):0;
                $totplt = 0;
            }

            if($totalblnk == $totaljadwal) {
                $tottun = 0;
                $totplt = 0;
            }

			//trap kedudukan
			$arrn=array(1,4); //aktif dan tubel
            if (!in_array($queqe['kedudukan'], $arrn)) {
                $tottun=0;
                $totplt=0;
				$jumlalpa = 0;
                $jumlate = 0;
                $jumearly = 0;
                $jumijin = 0;
                $jumTdkAbsen = 0;
                $jumTdkAbsenPlt = 0;
                $jumlalpaPlt = 0;
                $jumlatePlt = 0;
                $jumearlyPlt = 0;
                $jumijinPlt = 0;
                $totallatePlt = 0;
                $jumTb = 0;
                $jumUpacara=0;
                $jumUpacaraPlt=0;
            } 
			
			
			
            //date('mY', strtotime($que->date_shift)
            $rslSikerja = $this->report_model->getSikerjaImport($queqe['userid'],$myThnBln);
            $nSiKerja=  0;
            if ($rslSikerja->num_rows()>0) {
                $ret = $rslSikerja->row();
                $nSiKerja =  $ret->jumlah;
            }
            //($this->db->last_query());
            //die();
            if ($queqe['payable']==0) {
                $jumlalpa = 0;
                $jumlate = 0;
                $jumearly = 0;
                $jumijin = 0;
                $jumTdkAbsen = 0;
                $jumTdkAbsenPlt = 0;
                $jumlalpaPlt = 0;
                $jumlatePlt = 0;
                $jumearlyPlt = 0;
                $jumijinPlt = 0;
                $totallatePlt = 0;
                $jumTb = 0;
                $jumUpacara=0;
                $jumUpacaraPlt=0;
            }
			
            $dataallaye[] = array(
                'userid'   	=> $queqe['userid'],
                'no_rekening'   => $queqe['no_rekening'],
                'npwp'   	=> $queqe['npwp'],
                'empTitle' 	=> $queqe['empTitle'],
                'empID' 	=> $queqe['empID'],
                'empHire'	=> $queqe['empHire'],
                'empName' 	=> $queqe['empName'],
                'deptName' 	=> $queqe['deptName'],
                'kelasjabatan' 			=> $queqe['kelasjabatan'],
                'golongan' 				=> $queqe['golru'],
                'isplt'                 =>$queqe['isplt'],
                'plt_deptname' => $queqe['plt_deptname'],
                'kriteriaPlt' => $queqe['kriteriaPlt'],
                'plt_jbtn' => $queqe['plt_jbtn'],
                'plt_kelasjabatan' => $queqe['plt_kelasjabatan'],
                'byuser'=>  $byuser,
                "dipisah" =>$queqe['dipisah'],
                "ishidden" =>$queqe['ishidden'],
                "isViewIt"=>$queqe['isViewIt'],
                'potterlambat' 			=> $jumlate >0 ? $jumlate : 0,
                'potterlambatplt' 			=> $jumlatePlt >0 ? $jumlatePlt : 0,
                'potpsw' 			=> $jumearly >0 ? $jumearly : 0,
                'potpswplt' 			=> $jumearlyPlt >0 ? $jumearlyPlt : 0,
                'potijin' 			=> $jumijin >0 ? $jumijin : 0,
                'potijinplt' 			=> $jumijinPlt >0 ? $jumijinPlt : 0,
                'potalpa' 			=> $jumlalpa >0 ? $jumlalpa : 0,
                'potalpaplt' 			=> $jumlalpaPlt >0 ? $jumlalpaPlt : 0,
                'potcuti' 			=> 0,
                'potcutiplt' 			=> 0,
                'pottb' 			=> $jumTb,
                'pottbplt' 			=> 0,
                'potjam' 			=> 0,
                'potjamplt' 			=> 0,
                'pottdkabsen' 			=> $jumTdkAbsen >0 ? $jumTdkAbsen : 0,
                'pottdkabsenplt' 			=> $jumTdkAbsenPlt >0 ? $jumTdkAbsenPlt : 0,
                'tunjangan' 			=> $tottun,
                'tunjanganplt' =>       $totplt,
                'totaltunjangan'		=> $tottun==0?0:$tunj,
                'totaltunjanganplt'		=> $totplt==0?0:$tunjplt,
                "totaltunj" =>$totaltunj,
                "totaltunjplt" => $totaltunjplt,
                'jmlsikerja' =>       $nSiKerja,
                'potupacara' 			=> $jumUpacara> 0 ? $jumUpacara : 0,
                'potupacaraplt' 			=> $jumUpacaraPlt> 0 ? $jumUpacaraPlt : 0,

            );
        }

        $data = array(
            //"dateinfo" =>   date('d-m-Y', $datestart)." s/d ".date('d-m-Y', $datestop),
            "dateinfo" => strtoupper(format_date_ind(date('Y-m-d', $datestart))." s/d ".format_date_ind(date('Y-m-d', $datestop)) ),
            "cominfo" =>    $company,
            "empinfo" =>    $dataallaye,
            "data" =>       $deptar[$departemen],
            "excelid"=>     $excelid,
            "pdfid"=>     $ispdf,
            "showpdf" =>$showpdf
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

        if($excelid==1) {
            $dataview = $this->load->view("laporan4",$data,true);
            $datavw = $datavw.$dataview;
        } else {
            $this->load->view("laporan4",$data);
        }

        if ($ispdf==1) {
            $this->load->library('mpdf');
            $this->mpdf =
                new mPDF('',    // mode - default ''
                    '',    // format - A4, for example, default ''
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
            $stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
            $stylesheet = file_get_contents( $stylepdf );
            $this->mpdf->WriteHTML($stylesheet,1);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->WriteHTML($datavw);

            $this->mpdf->Output();
            die();
        }

        if($excelid==1) {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=lapkeuangantunjangankinerja.xls");
            //echo "$datavw";

            echo '<html>';
            echo '<head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Laporan Potongan Tunjangan Kinerja</title>
                </head>
                <body>';
            echo $datavw;
            echo '</body></html>';
        }
    }


    function view4_()
    {
        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));

        $myThnBln = date("Y-m", strtotime($postdatestart));

        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');

        $userid = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);
        //echo date("Y-m-d",$datestart)."<br>";
        $departemen = $this->input->post('org')!='undefined'?$this->input->post('org'):"1";

        $sql = "select tmt_berlaku from mastertunjangan order by tmt_berlaku desc limit 1";
        $query = $this->db->query($sql);
        $tgltunjangan = $query->row()->tmt_berlaku;

        $sql = "select * from mastertunjangan";
        $query = $this->db->query($sql);
        foreach($query->result() as $que) {
            $mastertunjbaru[$que->kelasjabatan] = $que->tunjangan;
        }

        $mastertunj = array();
        if($datestart >= strtotime($tgltunjangan)) {
            $sql = "select * from mastertunjangan";
            $query = $this->db->query($sql);
            foreach($query->result() as $que) {
                $mastertunj[$que->kelasjabatan] = $que->tunjangan;
            }
        } else {
            $sql = "select * from tunjanganhistory where tglubah < '$tgltunjangan'";
            //$sql = "select DISTINCT kelasjabatan,tunjangan FROM mastertunjangan_log where tglubah < '$tgltunjangan'";
            $query = $this->db->query($sql);
            foreach($query->result() as $que) {
                $mastertunj[$que->kelasjabatan] = $que->tunjangan;
            }
        }

        //echo $this->db->last_query();

        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(convertToArray($this->session->userdata('s_area'))):array();

        $tbar = array();
        $bbar = array();
        $holar = array();

        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $pkrga = array();
        $akrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname,
                'kategori'	=> $at->status_kategori_id
            );
            $pkrga[$at->atid] = $at->value;
            $akrga[$at->atid] = $at->ign_to;
        }

        $pkrgK = array();
        $pkrg = array();
        $absrecap = $this->report_model->getabs();
        foreach($absrecap->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname,
                'kategori'	=> $bs->status_kategori_id
            );
            $pkrg[$bs->abid] = $bs->value;
            if ($bs->status_kategori_id==12) {
                $pkrgK[$bs->abid] = $bs->value;
            }
        }

        $att = $this->report_model->getatt();
        foreach($att->result() as $tt) {
            $tbar[$tt->atid] = $tt->atname;
        }

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $yo = 0;
        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetailsxx($userar,$stspeg,$jnspeg);
            $yo=2;
        } else if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg,$jnspeg);
            $yo=1;
        }

        $byuser= $userid!='undefined'?1:0;

        $aten = array();
        $aben = array();

        $dataallay = array();

        foreach($queryemp->result() as $queq) {
            $unkir=0;
            $unkirbaru=0;
            $unkirprof=0;

            $kelas = $this->report_model->getkelasjabatan($queq->userid, date("Y-m-d", $datestart));
            //if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
            if ($kelas == null ) {
                $kelas= $queq->kelasjabatan;
            }
            //echo $kelas."<br>";
            $tunjang = $this->report_model->gettunjang(date("Y-m-d", $datestart), $kelas);
            //if ($tunjang == 0) $tunjang = $mastertunj[$queqe['kelasjabatan']];

            if ($queq->payable != 0){
                //$unkir=($mastertunj[$kelas] * refTHP()) ;
                $unkir=($tunjang * refTHP()) ;
                $unkirbaru=($mastertunjbaru[$kelas] * refTHP());
                $unkirprof=$queq->tunjanganprofesi;
            }

            $dataallay[] = array(
                'userid'   => $queq->userid,
                'empTitle' => $queq->title,
                'empID' => $queq->badgenumber,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
                'kelasjabatan' => $kelas,//$queq->kelasjabatan,
                'golru' => $queq->golru,
                'tunjanganprofesi' => $unkirprof,
                'tunjanganbaru' => $unkirbaru,
                'tunjangan' => $unkir,
                'jftstatus' => $queq->jftstatus,
                'jenisjabatan' => $queq->jenisjabatan,
                'jenispegawai' => $queq->jenispegawai,
                'kedudukan' => $queq->kedudukan,
                'tmtkedudukan' => $queq->tmtkedudukan,
                'payable' => $queq->payable,

                'tmtprofesi' => $queq->tmtprofesi,
                'plt_deptid' => $queq->plt_deptid,
                'plt_eselon' => $queq->plt_eselon,
                'tmt_plt' => $queq->tmt_plt,
                'eselon' => $queq->eselon,
                'plt_jbtn' => $queq->plt_jbtn,
                'plt_sk' => $queq->plt_sk,
                'plt_kelasjabatan' => $queq->plt_kelasjabatan,
                'dipisah'=>0,
                'ishidden'=>0,
            );
            foreach($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
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

        $dataallaye = array();
        $datafoot = array();
        $abc=0;
        $dataview = '';
        $datavw = '';

        foreach($dataallay as $queqe) {
            $tubel = 0;
            $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
            $dataarray = array();
            $totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua=0;

            if($queqe['kedudukan']==5 || $queqe['kedudukan']==4) {
                $queqe['tunjanganbaru'] = $queqe['tunjanganbaru'] ;
                $queqe['tunjangan'] = $queqe['tunjangan'] ;
            }

            if($queqe['jftstatus']!=2) {
                $queqe['tunjanganbaru'] = 0;
                $queqe['tunjangan'] = 0;
            }
            if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12 || $queqe['kedudukan']==13) {
                $queqe['tunjanganbaru'] = 0;
                $queqe['tunjangan'] = 0;
            }
            if($queqe['jenispegawai']==3 || $queqe['jenispegawai']==4) {
                $queqe['tunjanganbaru'] = 0;
                $queqe['tunjangan'] = 0;
            }



            if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
                if($queqe['tunjanganprofesi'] >= $queqe['tunjangan'])
                    $queqe['tunjangan'] = 0;
                else
                    $queqe['tunjangan'] = $queqe['tunjangan'] - $queqe['tunjanganprofesi'];

                if($queqe['tunjanganprofesi'] >= $queqe['tunjanganbaru'])
                    $queqe['tunjanganbaru'] = 0;
                else
                    $queqe['tunjanganbaru'] = $queqe['tunjanganbaru'] - $queqe['tunjanganprofesi'];
            }

            foreach($querytemp->result() as $que)
            {
                $late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
                $totalpersenkurang = 0; $totalmasuk++; $totaljadwal++; $tgltubel=null;
                //$day = date('D', strtotime($que->date_shift));
                $date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
                if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
                if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));

                //$hisjnspeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
                //$tgljnspeg = $this->report_model->gettgljenispeghis($queqe['userid'], $que->date_shift, 3);
                //echo $hisjnspeg." ".$tgljnspeg;
                if($queqe['kedudukan'] == 4) {
                    if ($queqe['tmtkedudukan'] != null) {
                        if ($datestart >= strtotime(date("Y-m-01", strtotime($queqe['tmtkedudukan'])))) {
                            $tubel = 1;
                            //echo date("Y-m-01", $datestart) .' '.date("Y-m-01", strtotime($queqe['tmtkedudukan']))."<br>";
                            //$tgltubel = strtotime($tgljnspeg);
                        }
                    }
                }
                //ambil nilai pulang
                $early=0;
                if($que->early_departure!=0) {
                    $early = $que->early_departure;
                }

                if($que->late!=0) {
                    $late = $que->late;

                    if ($que->late >3660) {
                        $telat = $que->late;
                        $late = $telat <= 0 ? 0 : $telat;
                    } else {
                        if ($que->ot_after != 0) {
                            if ($que->ot_after > 3600)
                                $telat = $que->late - 3600;
                            else
                                $telat = $que->late - $que->ot_after;

                            $late = $telat <= 0 ? 0 : $telat;
                        }
                    }

                    if (date("Y",$datestart)<2017)
                    {
                        if($late < 1860) $krglate = 0; //kurang dari 30 menit
                        else if($late >= 1860 && $late < 3660) $krglate = 0.5; //31- 61 menit
                        else if($late >= 3660 && $late < 5460) $krglate = 1; //61- 91 menit
                        else if($late >= 5460) $krglate = 1.5; //91 menit ke atas
                    } else
                    {
                        if($late < 3660) { // < 61 menit
                            $krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = 0.5;
                        }
                        else if($late >= 3660) { // >=61 menit ke atas
                            $telat = $que->late;
                            $krglate = 1;
                        }

                        //echo $telat." ".$krglate."<br>";
                        //jika terlambat >60 dan mengganti 60 menit
                        if ($que->late > 3660 ) {
                            $krglate = 1;
                            $telat = $que->late;
                        }

                        //berdasarkan referensi potongan untuk terlambat
                        $wry="select persentase FROM ref_potongan WHERE $late BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=1";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krglate = $qryPotongan->row()->persentase;
                            //$krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = $qryPotongan->row()->persentase;
                        }
                    }
                }

                if($que->early_departure!=0) {
                    $early = $que->early_departure;

                    //if (date("Y",$datestart)>2016) {
                    if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;

                    //potongan PSW
                    $wry="select persentase FROM ref_potongan WHERE $early BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=2";
                    $qryPotongan = $this->db->query($wry);
                    if ($qryPotongan->num_rows()>0) {
                        $krgearly = $qryPotongan->row()->persentase;
                    }
                    /*} else {
                        if($early > 1 && $early < 1860) $krgearly = 0.5;
                        else if($early >= 1860 && $early < 3660) $krgearly = 1;
                        else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                        else if($early >= 5460) $krgearly = 1.5;
                    }*/
                }

                //if (date("Y",$datestart)>2016) {
                if ($check_in == null) $krglate = 1;
                if ($check_out == null) $krgearly = 1;
                /*} else {
                    if($check_in==null) $krglate = 1.5;
                    if($check_out==null) $krgearly = 1.5;
                }*/
                if($que->attendance == 'ALP') {
                    $krglate = 0;
                    $krgearly = 0;
                    //if (date("Y",$datestart)>2016) {
                    $krgalpa = 5;
                    /*} else {
                        $krgalpa = 3;
                    }*/
                }

                if($que->attendance == 'BLNK') {
                    $totalblnk++;
                }

                $s = 0;
                if(isset($bbar[$que->attendance])) {
                    $krglate = 0;
                    $krgearly = 0;
                    $krgalpa = 0;
                    $krgstatus = $pkrg[$que->attendance];
                    $s = 1;
                }

                if(isset($atar[$que->attendance])) {
                    if($que->attendance=='AT_SK01' || $que->attendance=='AT_AT1')
                        $krglate = 0;
                    else if($que->attendance=='AT_SK03' || $que->attendance=='AT_AT2')
                        $krgearly = 0;
                    else {
                        $krglate = 0;
                        $krgearly = 0;
                    }
                    $krgalpa = 0;
                    $krgstatus = $pkrga[$que->attendance];

                    switch ($akrga[$que->attendance])
                    {
                        case 1:
                            $krglate = 0;
                            break;
                        case 2:
                            $krgearly = 0;
                            break;
                        default:
                            $krglate = 0;
                            $krgearly = 0;
                            break;
                    }
                }

                if($krglate > 0 && $que->attendance!='NWK' &&
                    $que->attendance!='NWDS' &&
                    $que->workinholiday!=1) {
                    $totalpersenkurang = $totalpersenkurang + $krglate;
                }

                if($krgearly > 0 && $que->attendance!='NWK' &&
                    $que->attendance!='NWDS' && $que->workinholiday!=1) {
                    $totalpersenkurang = $totalpersenkurang + $krgearly;
                }
                if($krgalpa != 0 && $que->workinholiday!=1) {
                    $totalalpa++;
                    $totalpersenkurang = $totalpersenkurang + $krgalpa;
                }
                if($krgstatus != 0  && $queqe['payable']!=0) {
                    $totalpersenkurang = $totalpersenkurang + $krgstatus;
                    if($que->attendance=='AB_12') $totalpembatalan++;
                }

                $totalpersensemua = $totalpersensemua + $totalpersenkurang;
            }

            if($totalalpa == $totalmasuk) {
                $queqe['tunjanganbaru'] = 0;
                $queqe['tunjangan'] = 0;
            }

            if($totalpembatalan == $totalmasuk) {
                $queqe['tunjanganbaru'] = 0;
                $queqe['tunjangan'] = 0;
            }

            $totalsemua = $totalalpa + $totalpembatalan;

            if($totalsemua == $totalmasuk) {
                $queqe['tunjanganbaru'] = 0;
                $queqe['tunjangan'] = 0;
            }

            if($totalblnk == $totaljadwal) {
                $queqe['tunjanganbaru'] = 0;
                $queqe['tunjangan'] = 0;
            }
            $tunjanganbaru=$queqe['tunjanganbaru'];
            $tunjangan=$queqe['tunjangan'];

            if($tubel==1) {
                //echo "tbul ".$tgltubel;
                $tunjangan = $tunjangan / 2;
                $tunjanganbaru = $tunjanganbaru / 2;
            }

            $dataallaye[] = array(
                'userid'   	=> $queqe['userid'],
                'empTitle' 	=> $queqe['empTitle'],
                'empID' 	=> $queqe['empID'],
                'empHire'	=> $queqe['empHire'],
                'empName' 	=> $queqe['empName'],
                'deptName' 	=> $queqe['deptName'],
                'kelasjabatan' 		=> $queqe['kelasjabatan'],
                'golongan' 		=> $queqe['golru'],
                'tunjangan' 		=> $tunjangan,
                'tunjanganbaru' 		=> $tunjanganbaru,
                'totaltunjanganbaru'	=> ($totalpersensemua / 100) * $tunjanganbaru,
                'totaltunjangan'	=> ($totalpersensemua / 100) * $tunjangan
            );
        }
        $data = array(
            "dateinfo" => date('d-m-Y', $datestart)." s/d ".date('d-m-Y', $datestop),
            "cominfo" => $company,
            "empinfo" => $dataallaye,
            "data" => $deptar[$departemen],
            "excelid" => $excelid,
            "pdfid"=> $ispdf,

        );

        if($excelid==1) {
            $dataview = $this->load->view("selisihtunjangan",$data,true);
            $datavw = $datavw.$dataview;
        } else {
            $this->load->view("selisihtunjangan",$data);
        }

        if ($ispdf==1) {
            $this->load->library('mpdf');
            $this->mpdf =
                new mPDF('',    // mode - default ''
                    '',    // format - A4, for example, default ''
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
            $stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
            $stylesheet = file_get_contents( $stylepdf );
            $this->mpdf->WriteHTML($stylesheet,1);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->WriteHTML($datavw);

            $this->mpdf->Output();
            die();
        }

        if($excelid==1) {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=lapkeuangantunjangankinerja.xls");
            //echo "$datavw";

            echo '<html>';
            echo '<head>
                    <link rel="stylesheet" type="text/css" href="'.base_url("assets/css/print.css").'"/>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Laporan Selisih Tunjangan Kinerja</title>
                </head>
                <body>';
            echo $datavw;
            echo '</body></html>';
        }

    }

    function view4_2()
    {
        //$postdatestart = $this->input->post('start')."-01";
        //$postdatestop = date("Y-m-t", strtotime($postdatestart)); //$this->input->post('end');
        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');
        $showpdf= $this->input->post('showpdf');
        $userid = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);

        $departemen = $this->input->post('org')!='undefined'?$this->input->post('org'):"1";
        $mastertunj = array();

        $this->session->set_userdata('tahundata',date("Y",$datestart));

        //kelas tunjangan yang baru
        $sqlv = "select * from mastertunjangan";
        $query = $this->db->query($sqlv);
        foreach ($query->result() as $que) {
            $mastertunj[$que->kelasjabatan] = $que->tunjangan;
        }

        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();


        $tbar = array();
        $bbar = array();
        $holar = array();

        $akrga = array();
        $pkrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname
            );
            $pkrga[$at->atid] = $at->value;
            $akrga[$at->atid] = $at->ign_to;
        }

        $pkrg = array();
        $absrecap = $this->report_model->getabs();
        foreach($absrecap->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname
            );
            $pkrg[$bs->abid] = $bs->value;
        }

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $yo = 0;
        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetailsxx($userar,$stspeg,$jnspeg);
            $yo=2;
        } else if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg,$jnspeg);
            $yo=1;
        }

        $byuser= $userid!='undefined'?1:0;

        $aten = array();
        $aben = array();

        $dataallay = array();
        foreach($queryemp->result() as $queq) {

            $kelas = $this->report_model->getkelasjabatan($queq->userid, date("Y-m-d", $datestart));
            //if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
            if ($kelas == null ) {
                $kelas= $queq->kelasjabatan;
            }

            //histori plt dll

            $tmtplt=$queq->tmt_plt;
            $pltdeptid=$queq->plt_deptid;
            $plteselon=$queq->plt_eselon;
            $eselon=$queq->eselon;
            $pltkelas=$queq->plt_kelasjabatan;
            $arow = $this->report_model->gettukinhist($queq->userid, date("Y-m-d", $datestart));
            if ($arow->num_rows()>0)
            {
                $rrow= $arow->row();
                $tmtplt = $rrow->tmt;
                $pltdeptid = $rrow->deptid;
                $plteselon = $rrow->plt_eselon;
                $pltkelas=$rrow->kelasjabatan;
            }


            $unkir= isset($mastertunj[$kelas])?$mastertunj[$kelas]:0;
            $unkirlama = $this->report_model->gettunjang(date("Y-m-d", $datestart), $kelas);
            //echo $unkir." ".$unkirlama;
            $isplt=0;
            $isViewIt=1;
            if (isset($tmtplt) && isset($pltdeptid) && isset($plteselon))
            {
                if ($tmtplt !='0000-00-00') {
                    $time = strtotime($tmtplt);
                    //$final = strtotime("+1 month", $time);
                    if ($datestart >= strtotime(date("Y-m-01",$time))) {
                        if ((intval(date('d', $time)) < 6) && date("Y-M", $datestart) === date('Y-M', $time)) {
                            $isplt = 1;
                        } else {
                            $time2 = strtotime(date('Y-m-01', $time));
                            $final = strtotime("+1 month", $time2);
                            if ($datestart >= $final) {
                                $isplt = 1;
                            }
                            else {
                                if (!in_array($queq->deptid, $orgid))
                                {
                                    continue;
                                }
                            }
                        }
                    } else{
                        $isViewIt=0;
                        if (!in_array($queq->deptid, $orgid))
                        {
                            continue;
                        }
                    }
                }
            }

            $jbtnDef = strval(konversiEselonBaru($eselon));
            $jbtnPlt = strval(konversiEselonBaru($plteselon));

            $unkirplt = 0;$unkirpltlama = 0;
            if(isset($mastertunj[$kelas])){
                $ntunj = $mastertunj[$kelas];
            }
            if(isset($ntunj)){
                $unkirplt = $ntunj * refTHP();
                $unkirpltlama = $unkirlama * refTHP();
            }

            //$unkirplt= isset($mastertunj[$queq->kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] * 0.5:0;
            $kriteriaPlt = 0;
            if ($isplt==1) {
                if ( ($jbtnDef  > $jbtnPlt) ) {
                    $kriteriaPlt=1;
                    $unkirplt= isset($mastertunj[$pltkelas])?$mastertunj[$pltkelas] :0;
                    $unkirplt= ($unkirplt * refTHP()) * refPLT();

                    $unkirpltlama= ($unkirpltlama * refTHP()) * refPLT();
                }
                if ( ($jbtnDef  == $jbtnPlt ) ) {
                    $kriteriaPlt=2;
                    /*$unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan]:0;
                    $unkirplt= ($unkirplt * refTHP()) * refPLT();*/
                    if ($queq->plt_kelasjabatan > $queq->kelasjabatan) {
                        $unkirplt = isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP());
                        $unkir = ($unkir * refTHP()) * refPLT();
                        $unkirpltlama = ($unkirpltlama * refTHP());
                        $unkirlama = ($unkirlama * refTHP()) * refPLT();

                    } else
                    {
                        $unkirplt = isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP()) * refPLT();
                        $unkirpltlama = ($unkirpltlama * refTHP()) * refPLT();
                    }
                }

                if ( ($jbtnDef  < $jbtnPlt ) ) {
                    $kriteriaPlt=3;
                    //$klsjbatan = max(array($queq->kelasjabatan,$queq->plt_kelasjabatan));
					$klsjbatan = $queq->plt_kelasjabatan;
                    /*if (date("Y",$datestart)>2016)
                    {*/
                        //old
                    //$unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* refTHP():0;
                    $unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* refTHP():0;
                    /*} else
                    {
                        $unkirplt= isset($mastertunj2016[$klsjbatan])?$mastertunj2016[$klsjbatan]* refTHP():0;
                    }*/
                    $unkir = 0;
                    $unkirlama = 0;
                    //old
                    //$unkirpltlama = $unkirpltlama* refTHP();
                    $unkirpltlama = $unkirpltlama;
                }
            }

            if ($queq->payable == 0){
                $unkir=0;
                $unkirplt=0;
                $unkirlama=0;
                $unkirpltlama=0;
            }

            $dataallay[] = array(
                'userid'   => $queq->userid,
                'empTitle' => $queq->title,
                'empID' => $queq->badgenumber,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'golru' => $queq->golru,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
                'npwp'   => $queq->npwp,
                'jftstatus' => $queq->jftstatus,
                'jenisjabatan' => $queq->jenisjabatan,
                'jenispegawai' => $queq->jenispegawai,
                'kedudukan' => $queq->kedudukan,
                'tmtkedudukan' => $queq->tmtkedudukan,
                'deptid' => $queq->deptid,
                'plt_deptid' => $queq->plt_deptid,
                'plt_eselon' => $queq->plt_eselon,
                'tmt_plt' => $queq->tmt_plt,
                'payable' => $queq->payable,
                'eselon' => $queq->eselon,
                'plt_jbtn' => $queq->plt_jbtn,
                'plt_sk' => $queq->plt_sk,
                'plt_kelasjabatan' => $queq->plt_kelasjabatan,
                'isplt' =>$isplt,
                'kriteriaPlt' =>$kriteriaPlt,
                'plt_deptname' => isset($deptar[$queq->plt_deptid])?$deptar[$queq->plt_deptid]:'',
                'dipisah'=>0,
                'ishidden'=>0,
                'isViewIt'=>$isViewIt,
                'orgf'=>"",
                'kelasjabatan' => $kelas,//$queq->kelasjabatan,
                'tunjanganprofesi' => $queq->tunjanganprofesi,
                'tmtprofesi' => $queq->tmtprofesi,
                'tunjangan' => $unkir,
                'tunjanganlama' => $unkirlama,
                'tunjanganplt' => $unkirplt,
                'tunjanganpltlama' => $unkirpltlama,
            );

            foreach($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
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
        $dataallaye = array();
        $abc=0;
        $dataview = '';
        $datavw = '';

        foreach($dataallay as $queqe) {

            $querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
            $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
            $dataarray = array();
            $totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
            $totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
            $totallibur  = 0; $tubel = 0; $totaltubel = 0; $totaltubelplt=0;$tunjtubel = 0; $tunjtubelplt = 0; $tgltubel = 0;
            $totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;
            $tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0; $totplt = 0;$tunjplt = 0;
            $ttlmsk = array(); $tunjangan = array(); $tunjanganplt = array();$tunbulplt = array();
            $tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
            $jft = array(); $jfto = 0; $jftp = array();
            $jpeg = array(); $jpego = 0; $jpegp = array();
            $kedu = array(); $keduo = 0; $kedup = array();
            $totaltunjplt =[];$totaltunj =[];
            $totaltunjlama =[];$tunbullama = array();$tunjlama = 0;$tunbulpltlama = array();
            $tunjpltlama = 0;
            foreach($querytempo->result() as $que) {
                $ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunj[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunjplt[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunjlama[date('mY', strtotime($que->date_shift))] = 0;
            }
            foreach($querytempo->result() as $que) {

                $kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
                if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
                $tunjangLama = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);

                $tunjang = $mastertunj[$queqe['kelasjabatan']];

                if($tunjang!=0) {
                    if ($queqe['userid'] != 0 ) {
                        //if (date("Y", $datestart) > 2016) {
                        $queqe['tunjangan'] = $tunjang * refTHP();
                        $queqe['tunjanganlama'] = $tunjangLama * refTHP();
                        /*} else {
                            $queqe['tunjangan'] = $tunjang;
                        }*/
                    } else {
                        $queqe['tunjangan'] = 0;
                        $queqe['tunjanganlama']=0;
                    }
                    //baru
                    if ($queqe['kriteriaPlt']==2)
                    {
                        if ( $queqe['plt_kelasjabatan'] > $queqe['kelasjabatan']) {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = ($tjngnga * refTHP()) * refPLT();

                            $queqe['tunjanganlama']= ($tunjangLama * refTHP()) * refPLT();
                        } else
                        {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = $tjngnga * refTHP();
                            $queqe['tunjanganlama']= ($tunjangLama * refTHP()) ;
                        }
                    }

                    //CPNS aktif mulai 1 April 2019 sebesar 80%
                    $curenttime=strtotime($que->date_shift);
                    $bypasstime=strtotime('2019-04-01');
                    if ($curenttime>=$bypasstime){
                        if ($queqe['jftstatus']==1)
                        {
                            $queqe['tunjangan'] = $queqe['tunjangan'] * refTHPCP();
                            $queqe['tunjanganlama']= $queqe['tunjanganlama'] * refTHPCP() ;
                        }
                    }

                    
                }
                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($que->flaghol == 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($tunbuli!=$queqe['tunjangan']) {
                    $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1;//0.5 : 1
                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                    $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'] * $kali;
                    $tunbullama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganlama'] * $kali;
                    $tunbulpltlama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganpltlama'] * $kali;
                }

                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        //$lnpltdepid = $this->isEselon2($pltdepid);
                        //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                        $cpltdeptid =isOrgIn($pltdepid);

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        //$lorgtar = $this->isEselon2($orgtar);
                        //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                        $corgtar  = isOrgIn($orgtar);
                        //asalnya tidak sama
                        /* if ($cpltdeptid == $corgtar)
                         {
                             $queqe['ishidden'] = 1;
                         }*/

                        $queqe['ishidden'] = ($cpltdeptid != $corgtar)?1:0;
                    }
                }

                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined') {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            $cdeptid =isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            $corgtar  = isOrgIn($orgtar);

                            if ($dibagi)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }
                            if ($tunbuli != $queqe['tunjangan']) {
                                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali ;
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt']  * $kali ;
                                $tunbullama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganlama'] * $kali ;
                                $tunbulpltlama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganpltlama']  * $kali ;
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        $cdeptid = isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  = isOrgIn($orgtar);
                        if ($dibagi)
                        {
                            if ($cdeptid == $corgtar) //satu tempat defenitif
                            {
                                $queqe['dipisah']= 1;
                            }
                            if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                            {
                                $queqe['dipisah']= 2;
                            }
                        }
                    }
                }

                //else
                //	$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];
                $jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
                if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;

                if($jfto!=$queqe['jftstatus'])
                    $jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];
                /* else
                    $jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */

                $jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
                if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;

                if($jpego!=$queqe['jenispegawai'])
                    $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];
                /* else
                    $jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */

                $kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
                if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;

                if($keduo!=$queqe['kedudukan'])
                    $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];
                /* else
                    $kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */

                $tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
                //if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;
                if($tunjangprof!=0) {
                    if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi']))
                    {
                        $queqe['tunjanganprofesi'] = $tunjangprof;
                    } else {
                        $queqe['tunjanganprofesi']=0;
                    }
                }

                //if($tunpro!=$queqe['tunjanganprofesi'])
                //    $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];
                if($tunpro!=$queqe['tunjanganprofesi']) {
                    //if (date("Y", $datestart) > 2016)
                    {
                        if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi'])) {
                            $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'] ;//* 0.5;
                        } else {
                            $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = 0;
                        }
                    } /*else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];;
                    }*/
                }
                /* else
                    $tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */

                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                $tunbuli = $queqe['tunjangan'] * $kali;
                $tunpro = $queqe['tunjanganprofesi'];
                $jfto = $queqe['jftstatus'];
                $jpego = $queqe['jenispegawai'];
                $keduo = $queqe['kedudukan'];
            }

            foreach($querytemp->result() as $que) {
                $late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
                $totalpersenkurang = 0; $totaljadwal++;
                $day = date('D', strtotime($que->date_shift));
                $date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
                if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
                if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));


                if(isset($tunbul[date('mY', strtotime($que->date_shift))])) {
                    if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubel = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        //echo $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubelplt = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];

                        $tunjanganlama[date('mY', strtotime($que->date_shift))] = $tunbullama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = $tunbulpltlama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                    }
                } else {
                    $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;

                    $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                }


                if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12 || $queqe['kedudukan']==13) { $tottun = 0;$totplt=0;}
                if($queqe['jenispegawai']==3 || $queqe['jenispegawai']==4) { $tottun = 0;$totplt=0;}

                if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 ||
                        $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunjanganplt[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganlama[date('mY', strtotime($que->date_shift))] = $tunjanganlama[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = $tunjanganpltlama[date('mY', strtotime($que->date_shift))] / 2;
                        }
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tubel = 1;
                            $tgltubel = strtotime($queqe['tmtkedudukan']);
                        }
                    }
                }

                if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 || $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    $tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                    if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
                        if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))]) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                        }
                        else {
                            //$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] - $tunjanganpro[date('mY', strtotime($que->date_shift))];
                            //$tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]*2) - $tunjanganpro[date('mY', strtotime($que->date_shift))])*refTHP();
                            $tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]) - $tunjanganpro[date('mY', strtotime($que->date_shift))]);
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganlama[date('mY', strtotime($que->date_shift))] = (($tunjanganlama[date('mY', strtotime($que->date_shift))]) - $tunjanganpro[date('mY', strtotime($que->date_shift))]);
                            $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                        }
                    }
                }

                //ambil nilai pulang
                $early=0;
                if($que->early_departure!=0) {
                    $early = $que->early_departure;
                }
                if($que->late!=0) {
                    $late = $que->late;
                    if ($que->late >3660) {
                        $telat = $que->late;
                        $late = $telat <= 0 ? 0 : $telat;
                    } else {
                        if ($que->ot_after != 0) {
                            if ($que->ot_after > 3600)
                                $telat = $que->late - 3600;
                            else
                                $telat = $que->late - $que->ot_after;
                            $late = $telat <= 0 ? 0 : $telat;
                        }
                    }

                    if (date("Y",$datestart)<2017)
                    {
                        if($late < 1860) $krglate = 0; //kurang dari 30 menit
                        else if($late >= 1860 && $late < 3660) $krglate = 0.5; //31- 61 menit
                        else if($late >= 3660 && $late < 5460) $krglate = 1; //61- 91 menit
                        else if($late >= 5460) $krglate = 1.5; //91 menit ke atas
                    } else
                    {
                        if($late < 3660) { // < 61 menit
                            $krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = 0.5;
                        }
                        else if($late >= 3660) { // >=61 menit ke atas
                            $krglate = 1;
                        }
                        //jika terlambat >60 dan mengganti 60 menit
                        if ($que->late > 3660 )
                            $krglate = 1;

                        //berdasarkan referensi potongan untuk terlambat
                        $wry="select persentase FROM ref_potongan WHERE $late BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=1";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krglate = $qryPotongan->row()->persentase;
                            //$krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = $qryPotongan->row()->persentase;

                        }
                    }
                }

                if($que->early_departure!=0) {
                    $early = $que->early_departure;

                    //if (date("Y",$datestart)>2016)
                    {
                        if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;

                        //potongan PSW
                        $wry="select persentase FROM ref_potongan WHERE $early BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=2";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krgearly = $qryPotongan->row()->persentase;
                        }

                    } /*else {

                        if($early > 1 && $early < 1860) $krgearly = 0.5;
                        else if($early >= 1860 && $early < 3660) $krgearly = 1;
                        else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                        else if($early >= 5460) $krgearly = 1.5;
                    }*/
                }

                //if (date("Y",$datestart)>2016)
                {
                    if ($check_in == null) $krglate = 1;
                    if ($check_out == null) $krgearly = 1;
                } /*else {
                    if($check_in==null) $krglate = 1.5;
                    if($check_out==null) $krgearly = 1.5;
                }*/
                if($que->attendance == 'ALP') {
                    $krglate = 0;
                    $krgearly = 0;
                    //if (date("Y",$datestart)>2016) {
                    $krgalpa = 5;
                    /*} else {
                        $krgalpa = 3;
                    }*/
                }

                if($que->attendance == 'BLNK') {
                    $totalblnk++;
                }

                $s = 0;
                if(isset($bbar[$que->attendance])) {
                    $krglate = 0;
                    $krgearly = 0;
                    $krgalpa = 0;
                    $krgstatus = $pkrg[$que->attendance];
                    $s = 1;
                }

                if(isset($atar[$que->attendance])) {
                    if($que->attendance=='AT_SK01' ||
                        $que->attendance=='AT_AT1')
                        $krglate = 0;
                    else if($que->attendance=='AT_SK03' ||
                        $que->attendance=='AT_AT2')
                        $krgearly = 0;
                    else {
                        $krglate = 0;
                        $krgearly = 0;
                    }
                    $krgalpa = 0;
                    $krgstatus = $pkrga[$que->attendance];

                    switch ($akrga[$que->attendance])
                    {
                        case 1:
                            $krglate = 0;
                            break;
                        case 2:
                            $krgearly = 0;
                            break;
                        default:
                            $krglate = 0;
                            $krgearly = 0;
                            break;
                    }
                }

                if($krglate > 0 && $que->attendance!='NWK' &&
                    $que->attendance!='NWDS' && $que->workinholiday!=1 ) {

                    $totalpersenkurang = $totalpersenkurang + $krglate;
                    $tunj = $tunj + (($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

                    $tunjlama = $tunjlama + (($krglate /100) * ($tunjanganlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjpltlama = $tunjpltlama + (($krglate /100) * ($tunjanganpltlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {

                    $totalpersenkurang = $totalpersenkurang + $krgearly;
                    $tunj = $tunj + (($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

                    $tunjlama = $tunjlama + (($krgearly /100) * ($tunjanganlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjpltlama = $tunjplt + (($krgearly /100) * ($tunjanganpltlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }
                if($krgalpa != 0 && $que->workinholiday!=1) {

                    $totalpersenkurang = $totalpersenkurang + $krgalpa;
                    $totalalpa++;
                    $tunj = $tunj + (($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjlama = $tunjlama + (($krgalpa /100) * ($tunjanganlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjpltlama = $tunjplt + (($krgalpa /100) * ($tunjanganpltlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgstatus != 0  && $queqe['payable']!=0) {
                    $totalpersenkurang = $totalpersenkurang + $krgstatus;
                    if($que->attendance=='AB_12') $totalpembatalan++;
                    $tunj = $tunj + (($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjlama = $tunjlama + (($krgstatus /100) * ($tunjanganlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjpltlama = $tunjplt + (($krgstatus /100) * ($tunjanganpltlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                $totalpersensemua = $totalpersensemua + $totalpersenkurang;
                $totalmasuk = array_sum($ttlmsk);

                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1 ) {
                    if($tubel==1) {
                        //$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltubelplt = $totaltubelplt+ $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                        $totaltunjlama[date('mY', strtotime($que->date_shift))] = $totaltunjlama[date('mY', strtotime($que->date_shift))] + $tunjanganlama[date('mY', strtotime($que->date_shift))];
                        $totaltunjpltlama[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganpltlama[date('mY', strtotime($que->date_shift))];
                    }
                }

                if($que->flaghol == 1) {
                    if($tubel==1) {
                        //$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        //$totaltubelplt = $totaltubelplt + $totaltunjplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                        $totaltunjlama[date('mY', strtotime($que->date_shift))] = $totaltunjlama[date('mY', strtotime($que->date_shift))] + $tunjanganlama[date('mY', strtotime($que->date_shift))];
                        $totaltunjpltlama[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganpltlama[date('mY', strtotime($que->date_shift))];
                    }
                }

                if ($tubel==1)
                {
                    if ($que->attendance == 'NWK' || $que->flaghol==1 || $que->attendance=="")
                    {

                    } else
                    {
                        $totaltubel = $totaltubel + $tunjtubel*0.5 ;//$tunjangan[date('mY', strtotime($que->date_shift))];
                        //echo $que->date_shift." ".$que->attendance." ".$tunjtubel*0.5."<br>";
                    }
                }
            }

            $tottun = isset($totaltunj)?array_sum($totaltunj):0;
            $totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;

            $tottunlama = isset($totaltunjlama)?array_sum($totaltunjlama):0;
            $totpltlama = isset($totaltunjplt)?array_sum($totaltunjpltlama):0;

            if($tubel==1) {

                /*$tunj = $tunj + $totaltubel;
                $tottun = $tunjtubel;*/
                $tunj = 0;
                $tottun = ($totaltubel/$totalmasuk);
                $totplt= $tunjtubelplt;

                $tottunlama = ($totaltubel/$totalmasuk);
                $totpltlama= $tunjtubelplt;
            }

            if($totalalpa == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
                $tottunlama=0;
                $totpltlama = 0;
            }

            if($totalpembatalan == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
                $tottunlama=0;
                $totpltlama = 0;
            }

            $totalsemua = $totalalpa + $totalpembatalan;

            if($totalsemua == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
                $tottunlama=0;
                $totpltlama = 0;
            }

            if($totalblnk == $totaljadwal) {
                $tottun = 0;
                $totplt = 0;
                $totpltlama = 0;
                $tottunlama=0;
            }
            echo $queqe['isplt'].'-'.$totpltlama."<br>";
            if ($queqe['isplt'] != 1) {
                $tunjanganplt = 0;
                $totaltunjanganplt = 0;
                $totalplt = 0;
            } else {
                //$tottunlama=$totpltlama;
                if (($queqe['kriteriaPlt'] == 1) || ($queqe['kriteriaPlt'] == 2)) {
                    if ($queqe['dipisah'] == 1) {
                        $tunjanganplt = 0;
                        $totaltunjanganplt = 0;
                        $totalplt = 0;
                        $kelas = $queqe['kelasjabatan'];
                        $tottunlama=0;
                    }
                    if ($queqe['dipisah'] == 2) {
                        $tunjangan = 0;
                        $totaltunjangan = 0;
                        $kelas = $queqe['plt_kelasjabatan'];
                        $tottunlama=0;
                    }
                }

                if (($queqe['kriteriaPlt'] == 3)) {
                    $tunjangan = 0;
                    $totaltunjangan = 0;
                    $tottunlama=0;

                    $kelas = $queqe['plt_kelasjabatan'] == 0 ? '' : $queqe['plt_kelasjabatan'];
                    if ($queqe['dipisah'] == 1) {
                        $tunjangan = 0;
                        $totaltunjangan = 0;
                        $tottunlama=0;
                    }
                    if ($queqe['dipisah'] == 2) {
                        $tunjangan = 0;
                        $totaltunjangan = 0;
                        $tottunlama=0;
                    }
                }
                if (($queqe['ishidden'] == 1)) {
                    $tunjangan = 0;
                    $totaltunjangan = 0;

                    $tunjanganplt = 0;
                    $totaltunjanganplt = 0;
                    $totalplt = 0;
                    $tottunlama=0;
                }
            }


            $dataallaye[] = array(
                'userid'   	=> $queqe['userid'],
                'empTitle' 	=> $queqe['empTitle'],
                'empID' 	=> $queqe['empID'],
                'empHire'	=> $queqe['empHire'],
                'empName' 	=> $queqe['empName'],
                'deptName' 	=> $queqe['deptName'],
                'npwp'   => $queqe['npwp'],
                'kelasjabatan' 			=> $queqe['kelasjabatan'],
                'golongan' 				=> $queqe['golru'],
                'isplt'                 =>$queqe['isplt'],
                'plt_deptname' => $queqe['plt_deptname'],
                'kriteriaPlt' => $queqe['kriteriaPlt'],
                'plt_jbtn' => $queqe['plt_jbtn'],
                'plt_kelasjabatan' => $queqe['plt_kelasjabatan'],
                'byuser'=>  $byuser,
                "dipisah" =>$queqe['dipisah'],
                "ishidden" =>$queqe['ishidden'],
                'isViewIt'=>$queqe['isViewIt'],
                'tunjangan' 			=> $tottunlama,//$tottun,
                'tunjanganplt' =>       $totplt,
                'totaltunjangan'		=> $tunjlama,
                'totaltunjanganplt'		=> $tunjplt,
                "totaltunj" =>$totaltunj,
                "totaltunjplt" => $totaltunjplt,
                'tunjanganbaru'=>$tottun,
                'totaltunjanganbaru'=>$tunj
            );
        }

        $data = array(
            //"dateinfo" =>   date('d-m-Y', $datestart)." s/d ".date('d-m-Y', $datestop),
            "dateinfo" => strtoupper(format_date_ind(date('Y-m-d', $datestart))." s/d ".format_date_ind(date('Y-m-d', $datestop))),
            "cominfo" =>    $company,
            "empinfo" =>    $dataallaye,
            "data" =>       $deptar[$departemen],
            "excelid"=>     $excelid,
            "pdfid"=>     $ispdf,
            "showpdf"=>$showpdf,
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

        if($excelid==1) {
            $dataview = $this->load->view("selisihtunjangan",$data,true);
            $datavw = $datavw.$dataview;
        } else {
            $this->load->view("selisihtunjangan",$data);
        }

        if ($ispdf==1) {
            $this->load->library('mpdf');
            $this->mpdf =
                new mPDF('',    // mode - default ''
                    '',    // format - A4, for example, default ''
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

            $stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
            $stylesheet = file_get_contents( $stylepdf);
            $this->mpdf->WriteHTML($stylesheet,1);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->WriteHTML($datavw);

            $this->mpdf->Output();
            die();
        }

        if($excelid==1) {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=selisihtunjangankinerja.xls");
            //echo "$datavw";

            echo '<html>';
            echo '<head>
                    <link rel="stylesheet" type="text/css" href="'.base_url("assets/resources/css/print.css").'"/>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Laporan Selisih Tunjangan Kinerja</title>
                </head>
                <body>';
            echo $datavw;
            echo '</body></html>';
        }
    }

    function view4()
    {
        //$postdatestart = $this->input->post('start')."-01";
        //$postdatestop = date("Y-m-t", strtotime($postdatestart)); //$this->input->post('end');
        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');
        $showpdf= $this->input->post('showpdf');
        $userid = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);

        $departemen = $this->input->post('org')!='undefined'?$this->input->post('org'):"1";
        $mastertunj = array();

        $this->session->set_userdata('tahundata',date("Y",$datestart));

        //kelas tunjangan yang baru
        $sqlv = "select * from mastertunjangan";
        $query = $this->db->query($sqlv);
        foreach ($query->result() as $que) {
            $mastertunj[$que->kelasjabatan] = $que->tunjangan;
        }

        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();


        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(convertToArray($this->session->userdata('s_area'))):array();

        $tbar = array();
        $bbar = array();
        $holar = array();
        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $akrga = array();
        $pkrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname
            );
            $pkrga[$at->atid] = $at->value;
            $akrga[$at->atid] = $at->ign_to;
        }

        $pkrg = array();
        $absrecap = $this->report_model->getabs();
        foreach($absrecap->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname
            );
            $pkrg[$bs->abid] = $bs->value;
        }

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $yo = 0;
        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetailsxx($userar,$stspeg,$jnspeg);
            $yo=2;
        } else if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg,$jnspeg);
            $yo=1;
        }

        $byuser= $userid!='undefined'?1:0;

        $aten = array();
        $aben = array();

        $dataallay = array();
        foreach($queryemp->result() as $queq) {

            $kelas = $this->report_model->getkelasjabatan($queq->userid, date("Y-m-d", $datestart));
            //if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
            if ($kelas == null ) {
                $kelas= $queq->kelasjabatan;
            }

            $unkir= isset($mastertunj[$kelas])?$mastertunj[$kelas]:0;
            $unkirlama = $this->report_model->gettunjang(date("Y-m-d", $datestart), $kelas);
            //echo $unkir." ".$unkirlama;
            $isplt=0;
            $isViewIt=1;
            if (isset($queq->tmt_plt) && isset($queq->plt_deptid) && isset($queq->plt_eselon))
            {
                if ($queq->tmt_plt !='0000-00-00') {
                    $time = strtotime($queq->tmt_plt);
                    //$final = strtotime("+1 month", $time);
                    if ($datestart >= strtotime(date("Y-m-01",$time))) {
                        if ((intval(date('d', $time)) < 6) && date("Y-M", $datestart) === date('Y-M', $time)) {
                            $isplt = 1;
                        } else {
                            $time2 = strtotime(date('Y-m-01', $time));
                            $final = strtotime("+1 month", $time2);
                            if ($datestart >= $final) {
                                $isplt = 1;
                            }
                            else {
                                if (!in_array($queq->deptid, $orgid))
                                {
                                    continue;
                                }
                            }
                        }
                    } else{
                        $isViewIt=0;
                        if (!in_array($queq->deptid, $orgid))
                        {
                            continue;
                        }
                    }
                }
            }

            $jbtnDef = strval(konversiEselonBaru($queq->eselon));
            $jbtnPlt = strval(konversiEselonBaru($queq->plt_eselon));

            $unkirplt = 0;$unkirpltlama = 0;
            if(isset($mastertunj[$kelas])){
                $ntunj = $mastertunj[$kelas];
            }
            if(isset($ntunj)){
                $unkirplt = $ntunj * refTHP();
                $unkirpltlama = $unkirlama * refTHP();
            }

            //$unkirplt= isset($mastertunj[$queq->kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] * 0.5:0;
            $kriteriaPlt = 0;
            if ($isplt==1) {
                if ( ($jbtnDef  > $jbtnPlt) ) {
                    $kriteriaPlt=1;
                    $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan] :0;
                    $unkirplt= ($unkirplt * refTHP()) * refPLT();

                    $unkirpltlama= ($unkirpltlama * refTHP()) * refPLT();
                }
                if ( ($jbtnDef  == $jbtnPlt ) ) {
                    $kriteriaPlt=2;
                    /*$unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan]:0;
                    $unkirplt= ($unkirplt * refTHP()) * refPLT();*/
                    if ($queq->plt_kelasjabatan > $queq->kelasjabatan) {
                        $unkirplt = isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP());
                        $unkir = ($unkir * refTHP()) * refPLT();
                        $unkirpltlama = ($unkirpltlama * refTHP());
                        $unkirlama = ($unkirlama * refTHP()) * refPLT();

                    } else
                    {
                        $unkirplt = isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] : 0;
                        $unkirplt = ($unkirplt * refTHP()) * refPLT();
                        $unkirpltlama = ($unkirpltlama * refTHP()) * refPLT();
                    }
                }

                if ( ($jbtnDef  < $jbtnPlt ) ) {
                    $kriteriaPlt=3;
                    //$klsjbatan = max(array($queq->kelasjabatan,$queq->plt_kelasjabatan));
					$klsjbatan = $queq->plt_kelasjabatan;
                    /*if (date("Y",$datestart)>2016)
                    {*/
                    $unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* refTHP():0;
                    /*} else
                    {
                        $unkirplt= isset($mastertunj2016[$klsjbatan])?$mastertunj2016[$klsjbatan]* refTHP():0;
                    }*/
                    $unkir = 0;
                    $unkirlama = 0;
                    //$unkirpltlama = $unkirpltlama* refTHP();
                    $unkirpltlama = $unkirpltlama;
                }
            }

            if ($queq->payable == 0){
                $unkir=0;
                $unkirplt=0;
                $unkirlama=0;
                $unkirpltlama=0;
            }

            $dataallay[] = array(
                'userid'   => $queq->userid,
                'empTitle' => $queq->title,
                'empID' => $queq->badgenumber,
                'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                'empName' => $queq->name,
                'golru' => $queq->golru,
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
                'npwp'   => $queq->npwp,
                'jftstatus' => $queq->jftstatus,
                'jenisjabatan' => $queq->jenisjabatan,
                'jenispegawai' => $queq->jenispegawai,
                'kedudukan' => $queq->kedudukan,
                'tmtkedudukan' => $queq->tmtkedudukan,
                'deptid' => $queq->deptid,
                'plt_deptid' => $queq->plt_deptid,
                'plt_eselon' => $queq->plt_eselon,
                'tmt_plt' => $queq->tmt_plt,
                'payable' => $queq->payable,
                'eselon' => $queq->eselon,
                'plt_jbtn' => $queq->plt_jbtn,
                'plt_sk' => $queq->plt_sk,
                'plt_kelasjabatan' => $queq->plt_kelasjabatan,
                'isplt' =>$isplt,
                'kriteriaPlt' =>$kriteriaPlt,
                'plt_deptname' => isset($deptar[$queq->plt_deptid])?$deptar[$queq->plt_deptid]:'',
                'dipisah'=>0,
                'ishidden'=>0,
                'isViewIt'=>$isViewIt,
                'orgf'=>"",
                'kelasjabatan' => $kelas,//$queq->kelasjabatan,
                'tunjanganprofesi' => $queq->tunjanganprofesi,
                'tmtprofesi' => $queq->tmtprofesi,
                'tunjangan' => $unkir,
                'tunjanganlama' => $unkirlama,
                'tunjanganplt' => $unkirplt,
                'tunjanganpltlama' => $unkirpltlama,
            );

            foreach($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
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
        $dataallaye = array();
        $abc=0;
        $dataview = '';
        $datavw = '';

        foreach($dataallay as $queqe) {

            $querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
            $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
            $dataarray = array();
            $totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
            $totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
            $totallibur  = 0; $tubel = 0; $totaltubel = 0; $totaltubelplt=0;$tunjtubel = 0; $tunjtubelplt = 0; $tgltubel = 0;
            $totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;
            $tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0; $totplt = 0;$tunjplt = 0;
            $ttlmsk = array(); $tunjangan = array(); $tunjanganplt = array();$tunbulplt = array();
            $tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
            $jft = array(); $jfto = 0; $jftp = array();
            $jpeg = array(); $jpego = 0; $jpegp = array();
            $kedu = array(); $keduo = 0; $kedup = array();
            $totaltunjplt =[];$totaltunj =[];
            $totaltunjlama =[];$tunbullama = array();$tunjlama = 0;$tunbulpltlama = array();
            $tunjpltlama = 0;
            foreach($querytempo->result() as $que) {
                $ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunj[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunjplt[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunjlama[date('mY', strtotime($que->date_shift))] = 0;
            }
            foreach($querytempo->result() as $que) {

                $kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
                if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
                $tunjangLama = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);

                $tunjang = $mastertunj[$queqe['kelasjabatan']];

                if($tunjang!=0) {
                    if ($queqe['userid'] != 0 ) {
                        //if (date("Y", $datestart) > 2016) {
                        $queqe['tunjangan'] = $tunjang * refTHP();
                        $queqe['tunjanganlama'] = $tunjangLama * refTHP();
                        /*} else {
                            $queqe['tunjangan'] = $tunjang;
                        }*/
                    } else {
                        $queqe['tunjangan'] = 0;
                        $queqe['tunjanganlama']=0;
                    }
                    //baru
                    if ($queqe['kriteriaPlt']==2)
                    {
                        if ( $queqe['plt_kelasjabatan'] > $queqe['kelasjabatan']) {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = ($tjngnga * refTHP()) * refPLT();

                            $queqe['tunjanganlama']= ($tunjangLama * refTHP()) * refPLT();
                        } else
                        {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = $tjngnga * refTHP();
                            $queqe['tunjanganlama']= ($tunjangLama * refTHP()) ;
                        }
                    }
                    //CPNS aktif mulai 1 April 2019 sebesar 80%
                    $curenttime=strtotime($que->date_shift);
                    $bypasstime=strtotime('2019-04-01');
                    if ($curenttime>=$bypasstime){
                        if ($queqe['jftstatus']==1)
                        {
                            $queqe['tunjangan'] = $queqe['tunjangan'] * refTHPCP();
                            $queqe['tunjanganlama']= $queqe['tunjanganlama'] * refTHPCP() ;
                        }
                    }
                }
                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($que->flaghol == 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($tunbuli!=$queqe['tunjangan']) {
                    $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1;//0.5 : 1
                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                    $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'] * $kali;
                    $tunbullama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganlama'] * $kali;
                    $tunbulpltlama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganpltlama'] * $kali;
                }

                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        //$lnpltdepid = $this->isEselon2($pltdepid);
                        //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                        $cpltdeptid =isOrgIn($pltdepid);

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        //$lorgtar = $this->isEselon2($orgtar);
                        //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                        $corgtar  = isOrgIn($orgtar);
                        //asalnya tidak sama
                        /* if ($cpltdeptid == $corgtar)
                         {
                             $queqe['ishidden'] = 1;
                         }*/

                        $queqe['ishidden'] = ($cpltdeptid != $corgtar)?1:0;
                    }
                }

                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined') {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            $cdeptid =isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            $corgtar  = isOrgIn($orgtar);

                            if ($dibagi)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }
                            if ($tunbuli != $queqe['tunjangan']) {
                                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali ;
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt']  * $kali ;
                                $tunbullama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganlama'] * $kali ;
                                $tunbulpltlama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganpltlama']  * $kali ;
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        $cdeptid = isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  = isOrgIn($orgtar);
                        if ($dibagi)
                        {
                            if ($cdeptid == $corgtar) //satu tempat defenitif
                            {
                                $queqe['dipisah']= 1;
                            }
                            if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                            {
                                $queqe['dipisah']= 2;
                            }
                        }
                    }
                }

                //else
                //	$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];
                $jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
                if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;

                if($jfto!=$queqe['jftstatus'])
                    $jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];
                /* else
                    $jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */

                $jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
                if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;

                if($jpego!=$queqe['jenispegawai'])
                    $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];
                /* else
                    $jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */

                $kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
                if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;

                if($keduo!=$queqe['kedudukan'])
                    $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];
                /* else
                    $kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */

                $tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
                //if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;
                if($tunjangprof!=0) {
                    if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi']))
                    {
                        $queqe['tunjanganprofesi'] = $tunjangprof;
                    } else {
                        $queqe['tunjanganprofesi']=0;
                    }
                }

                //if($tunpro!=$queqe['tunjanganprofesi'])
                //    $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];
                if($tunpro!=$queqe['tunjanganprofesi']) {
                    //if (date("Y", $datestart) > 2016)
                    {
                        if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi'])) {
                            $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'] ;//* 0.5;
                        } else {
                            $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = 0;
                        }
                    } /*else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];;
                    }*/
                }
                /* else
                    $tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */

                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                $tunbuli = $queqe['tunjangan'] * $kali;
                $tunpro = $queqe['tunjanganprofesi'];
                $jfto = $queqe['jftstatus'];
                $jpego = $queqe['jenispegawai'];
                $keduo = $queqe['kedudukan'];
            }

            foreach($querytemp->result() as $que) {
                $late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
                $totalpersenkurang = 0; $totaljadwal++;
                $day = date('D', strtotime($que->date_shift));
                $date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
                if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
                if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));


                if(isset($tunbul[date('mY', strtotime($que->date_shift))])) {
                    if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubel = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        //echo $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubelplt = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];

                        $tunjanganlama[date('mY', strtotime($que->date_shift))] = $tunbullama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = $tunbulpltlama[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                    }
                } else {
                    $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;

                    $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                }


                if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12 || $queqe['kedudukan']==13) { $tottun = 0;$totplt=0;}
                if($queqe['jenispegawai']==3 || $queqe['jenispegawai']==4) { $tottun = 0;$totplt=0;}

                if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 ||
                        $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunjanganplt[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganlama[date('mY', strtotime($que->date_shift))] = $tunjanganlama[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = $tunjanganpltlama[date('mY', strtotime($que->date_shift))] / 2;
                        }
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        if ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))) {
                            $tubel = 1;
                            $tgltubel = strtotime($queqe['tmtkedudukan']);
                        }
                    }
                }

                if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 || $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    $tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                    if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
                        if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))]) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganlama[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                        }
                        else {
                            //$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] - $tunjanganpro[date('mY', strtotime($que->date_shift))];
                            //$tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]*2) - $tunjanganpro[date('mY', strtotime($que->date_shift))])*refTHP();
                            $tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]) - $tunjanganpro[date('mY', strtotime($que->date_shift))]);
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganlama[date('mY', strtotime($que->date_shift))] = (($tunjanganlama[date('mY', strtotime($que->date_shift))]) - $tunjanganpro[date('mY', strtotime($que->date_shift))]);
                            $tunjanganpltlama[date('mY', strtotime($que->date_shift))] = 0;
                        }
                    }
                }

                //ambil nilai pulang
                $early=0;
                if($que->early_departure!=0) {
                    $early = $que->early_departure;
                }
                if($que->late!=0) {
                    $late = $que->late;
                    if ($que->late >3660) {
                        $telat = $que->late;
                        $late = $telat <= 0 ? 0 : $telat;
                    } else {
                        if ($que->ot_after != 0) {
                            if ($que->ot_after > 3600)
                                $telat = $que->late - 3600;
                            else
                                $telat = $que->late - $que->ot_after;
                            $late = $telat <= 0 ? 0 : $telat;
                        }
                    }

                    if (date("Y",$datestart)<2017)
                    {
                        if($late < 1860) $krglate = 0; //kurang dari 30 menit
                        else if($late >= 1860 && $late < 3660) $krglate = 0.5; //31- 61 menit
                        else if($late >= 3660 && $late < 5460) $krglate = 1; //61- 91 menit
                        else if($late >= 5460) $krglate = 1.5; //91 menit ke atas
                    } else
                    {
                        if($late < 3660) { // < 61 menit
                            $krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = 0.5;
                        }
                        else if($late >= 3660) { // >=61 menit ke atas
                            $krglate = 1;
                        }
                        //jika terlambat >60 dan mengganti 60 menit
                        if ($que->late > 3660 )
                            $krglate = 1;

                        //berdasarkan referensi potongan untuk terlambat
                        $wry="select persentase FROM ref_potongan WHERE $late BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=1";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krglate = $qryPotongan->row()->persentase;
                            //$krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = $qryPotongan->row()->persentase;

                        }
                    }
                }

                if($que->early_departure!=0) {
                    $early = $que->early_departure;

                    //if (date("Y",$datestart)>2016)
                    {
                        if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;

                        //potongan PSW
                        $wry="select persentase FROM ref_potongan WHERE $early BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=2";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krgearly = $qryPotongan->row()->persentase;
                        }

                    } /*else {

                        if($early > 1 && $early < 1860) $krgearly = 0.5;
                        else if($early >= 1860 && $early < 3660) $krgearly = 1;
                        else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                        else if($early >= 5460) $krgearly = 1.5;
                    }*/
                }

                //if (date("Y",$datestart)>2016)
                {
                    if ($check_in == null) $krglate = 1;
                    if ($check_out == null) $krgearly = 1;
                } /*else {
                    if($check_in==null) $krglate = 1.5;
                    if($check_out==null) $krgearly = 1.5;
                }*/
                if($que->attendance == 'ALP') {
                    $krglate = 0;
                    $krgearly = 0;
                    //if (date("Y",$datestart)>2016) {
                    $krgalpa = 5;
                    /*} else {
                        $krgalpa = 3;
                    }*/
                }

                if($que->attendance == 'BLNK') {
                    $totalblnk++;
                }

                $s = 0;
                if(isset($bbar[$que->attendance])) {
                    $krglate = 0;
                    $krgearly = 0;
                    $krgalpa = 0;
                    $krgstatus = $pkrg[$que->attendance];
                    $s = 1;
                }

                if(isset($atar[$que->attendance])) {
                    if($que->attendance=='AT_SK01' ||
                        $que->attendance=='AT_AT1')
                        $krglate = 0;
                    else if($que->attendance=='AT_SK03' ||
                        $que->attendance=='AT_AT2')
                        $krgearly = 0;
                    else {
                        $krglate = 0;
                        $krgearly = 0;
                    }
                    $krgalpa = 0;
                    $krgstatus = $pkrga[$que->attendance];

                    switch ($akrga[$que->attendance])
                    {
                        case 1:
                            $krglate = 0;
                            break;
                        case 2:
                            $krgearly = 0;
                            break;
                        default:
                            $krglate = 0;
                            $krgearly = 0;
                            break;
                    }
                }

                if($krglate > 0 && $que->attendance!='NWK' &&
                    $que->attendance!='NWDS' && $que->workinholiday!=1 ) {

                    $totalpersenkurang = $totalpersenkurang + $krglate;
                    $tunj = $tunj + (($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

                    $tunjlama = $tunjlama + (($krglate /100) * ($tunjanganlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjpltlama = $tunjpltlama + (($krglate /100) * ($tunjanganpltlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {

                    $totalpersenkurang = $totalpersenkurang + $krgearly;
                    $tunj = $tunj + (($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

                    $tunjlama = $tunjlama + (($krgearly /100) * ($tunjanganlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjpltlama = $tunjplt + (($krgearly /100) * ($tunjanganpltlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }
                if($krgalpa != 0 && $que->workinholiday!=1) {

                    $totalpersenkurang = $totalpersenkurang + $krgalpa;
                    $totalalpa++;
                    $tunj = $tunj + (($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjlama = $tunjlama + (($krgalpa /100) * ($tunjanganlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjpltlama = $tunjplt + (($krgalpa /100) * ($tunjanganpltlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgstatus != 0  && $queqe['payable']!=0) {
                    $totalpersenkurang = $totalpersenkurang + $krgstatus;
                    if($que->attendance=='AB_12') $totalpembatalan++;
                    $tunj = $tunj + (($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjlama = $tunjlama + (($krgstatus /100) * ($tunjanganlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjpltlama = $tunjplt + (($krgstatus /100) * ($tunjanganpltlama[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                /*  khusus upacara status dibayarkan
                    tgl upacara ada ,tgl/waktu check in ada isinya atau kosong dan bukan Tubel
                */ 
                $arrAtt=array("AT_AT1","AT_SK01","AT_AT5","AT_AT6"); //tidak dipotong
                $kdAbsPtg="AB_12";//didipotong

                $ta = $this->report_model->getdetailroster3($que->userid,$que->date_shift);
                $pa = $ta->first_row();
                //echo ("J:".$que->date_shift2." => D:".$que->date_in2.' => T:'.$que->check_in2."<br>");
                if ($que->date_shift2!="" && $queqe['payable'] !=0  && $tubel==0)
                {			
                    //print_r($que->date_shift2);
                    $qa = $this->report_model->gettranslog2($que->date_shift2,$que->date_shift2,$que->userid,"Upacara");
                    $za = $qa->first_row();

                    //if ($que->date_in2!="" && $que->check_in2 !="" ) { //ada tap ke mesin
                    if ($za != '' or $za != null) {
                        //echo("1. ".$que->check_in2."<br>");
                        if($pa != '' or $pa != null) //cek jadwal
                        {
                            $jwdlawal = strtotime($pa->date_shift.' '.$pa->shift_in);
                            $jwdlakhir = strtotime($pa->date_shift.' '.$pa->shift_out);

                            $cekinupcara= strtotime($za->checktime);
                            if ($cekinupcara < $jwdlawal  || $cekinupcara > $jwdlakhir)
                            {
                                $jwdlawal = date('Y-m-d H:i:s',$jwdlawal);
                                $jwdlakhir = date('Y-m-d H:i:s',$jwdlakhir);
                                $cekinupcara = date('Y-m-d H:i:s',$cekinupcara);
                                //echo("11. Diluar $jwdlawal - $jwdlakhir / $cekinupcara <br>");
                                $ispotong=1;

                                if (in_array($pa->attendance,$arrAtt))
                                {
                                    $ispotong=0; 
                                    //echo("13. ATT <br>");
                                } else {
                                    //echo("14. ".substr($pa->attendance,0,2)."<br>");
                                    if (substr($pa->attendance,0,2)=="AB"){
                                        if ($kdAbsPtg==$pa->attendance){
                                            $ispotong=1; 
                                        } 
                                        else {
                                            $ispotong=0; 
                                        }
                                    }
                                    
                                }

                                //echo("15. ".$pa->attendance." - ".$ispotong." <br>");

                                if ($ispotong==1){
                                    $nkurang = refUpacara();
                                    $potongan = ($tunjtubel*80)/100;
                                    $alp = round((($nkurang /100) * $potongan));
                                    $upacara = $this->report_model->getdetailroster2($que->userid,$que->date_shift);
                                    $dataarray[] = array(
                                        'day'			=> $day,
                                        'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                                        'status'		=> 'Tidak melakukan upacara ',
                                        'nilai'			=> null,
                                        'pengurangan'	=> $nkurang,
                                        'total'			=> $alp,
                                        'totalplt'		=> ($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                                    );
                                    $totalpersenkurang = $totalpersenkurang + $nkurang;
                                    $tunj1 = $tunj1 + round((($nkurang /100) * $potongan));
                                    $tunj = $tunj + round(($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                                    $tunjplt = $tunjplt + (($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                                }
                            } 
                        }
                    //print_r($this->db->last_query());
                    } else {
                        //echo("0. <br>");
                        $ispotong=1;
                        if (in_array($pa->attendance,$arrAtt))
                        {
                            $ispotong=0; 
                            //echo("01. ATT <br>");
                        } else {
                            //echo("24. ".substr($pa->attendance,0,2)."<br>");
                            if (substr($pa->attendance,0,2)=="AB"){
                                if ($kdAbsPtg==$pa->attendance){
                                    $ispotong=1; 
                                } 
                                else {
                                    $ispotong=0; 
                                }
                            }
                        }

                        if ($ispotong==1){
                            $nkurang = refUpacara();
                            $potongan = ($tunjtubel*80)/100;
                            $alp = round((($nkurang /100) * $potongan));
                            $upacara = $this->report_model->getdetailroster2($que->userid,$que->date_shift);
                            $dataarray[] = array(
                                'day'			=> $day,
                                'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                                'status'		=> 'Tidak melakukan upacara ',
                                'nilai'			=> null,
                                'pengurangan'	=> $nkurang,
                                'total'			=> $alp,
                                'totalplt'		=> ($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                            );
                            $totalpersenkurang = $totalpersenkurang + $nkurang;
                            $tunj1 = $tunj1 + round((($nkurang /100) * $potongan));
                            $tunj = $tunj + round(($nkurang/100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                            $tunjplt = $tunjplt + (($nkurang/100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        }


                    }
                }

                $totalpersensemua = $totalpersensemua + $totalpersenkurang;
                $totalmasuk = array_sum($ttlmsk);

                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1 ) {
                    if($tubel==1) {
                        //$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltubelplt = $totaltubelplt+ $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                        $totaltunjlama[date('mY', strtotime($que->date_shift))] = $totaltunjlama[date('mY', strtotime($que->date_shift))] + $tunjanganlama[date('mY', strtotime($que->date_shift))];
                        $totaltunjpltlama[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganpltlama[date('mY', strtotime($que->date_shift))];
                    }
                }

                if($que->flaghol == 1) {
                    if($tubel==1) {
                        //$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        //$totaltubelplt = $totaltubelplt + $totaltunjplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                        $totaltunjlama[date('mY', strtotime($que->date_shift))] = $totaltunjlama[date('mY', strtotime($que->date_shift))] + $tunjanganlama[date('mY', strtotime($que->date_shift))];
                        $totaltunjpltlama[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganpltlama[date('mY', strtotime($que->date_shift))];
                    }
                }

                if ($tubel==1)
                {
                    if ($que->attendance == 'NWK' || $que->flaghol==1 || $que->attendance=="")
                    {

                    } else
                    {
                        $totaltubel = $totaltubel + $tunjtubel*0.5 ;//$tunjangan[date('mY', strtotime($que->date_shift))];
                        //echo $que->date_shift." ".$que->attendance." ".$tunjtubel*0.5."<br>";
                    }
                }
            }

            $tottun = isset($totaltunj)?array_sum($totaltunj):0;
            $totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;

            $tottunlama = isset($totaltunjlama)?array_sum($totaltunjlama):0;
            $totpltlama = isset($totaltunjplt)?array_sum($totaltunjpltlama):0;

            if($tubel==1) {

                /*$tunj = $tunj + $totaltubel;
                $tottun = $tunjtubel;*/
                $tunj = 0;
                $tottun = ($totaltubel/$totalmasuk);
                $totplt= $tunjtubelplt;

                $tottunlama = ($totaltubel/$totalmasuk);
                $totpltlama= $tunjtubelplt;
            }

            if($totalalpa == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
                $tottunlama=0;
                $totpltlama = 0;
            }

            if($totalpembatalan == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
                $tottunlama=0;
                $totpltlama = 0;
            }

            $totalsemua = $totalalpa + $totalpembatalan;

            if($totalsemua == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
                $tottunlama=0;
                $totpltlama = 0;
            }

            if($totalblnk == $totaljadwal) {
                $tottun = 0;
                $totplt = 0;
                $totpltlama = 0;
                $tottunlama=0;
            }

            $dataallaye[] = array(
                'userid'   	=> $queqe['userid'],
                'empTitle' 	=> $queqe['empTitle'],
                'empID' 	=> $queqe['empID'],
                'empHire'	=> $queqe['empHire'],
                'empName' 	=> $queqe['empName'],
                'deptName' 	=> $queqe['deptName'],
                'npwp'   => $queqe['npwp'],
                'kelasjabatan' 			=> $queqe['kelasjabatan'],
                'golongan' 				=> $queqe['golru'],
                'isplt'                 =>$queqe['isplt'],
                'plt_deptname' => $queqe['plt_deptname'],
                'kriteriaPlt' => $queqe['kriteriaPlt'],
                'plt_jbtn' => $queqe['plt_jbtn'],
                'plt_kelasjabatan' => $queqe['plt_kelasjabatan'],
                'byuser'=>  $byuser,
                "dipisah" =>$queqe['dipisah'],
                "ishidden" =>$queqe['ishidden'],
                'isViewIt'=>$queqe['isViewIt'],
                'tunjangan' 			=> $tottunlama,//$tottun,
                'tunjanganplt' =>       $totplt,
                'totaltunjangan'		=> $tunjlama,
                'totaltunjanganplt'		=> $tunjplt,
                "totaltunj" =>$totaltunj,
                "totaltunjplt" => $totaltunjplt,
                'tunjanganbaru'=>$tottun,
                'totaltunjanganbaru'=>$tunj
            );
        }

        $data = array(
            //"dateinfo" =>   date('d-m-Y', $datestart)." s/d ".date('d-m-Y', $datestop),
            "dateinfo" => strtoupper(format_date_ind(date('Y-m-d', $datestart))." s/d ".format_date_ind(date('Y-m-d', $datestop))),
            "cominfo" =>    $company,
            "empinfo" =>    $dataallaye,
            "data" =>       $deptar[$departemen],
            "excelid"=>     $excelid,
            "pdfid"=>     $ispdf,
            "showpdf"=>$showpdf,
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

        if($excelid==1) {
            $dataview = $this->load->view("selisihtunjangan",$data,true);
            $datavw = $datavw.$dataview;
        } else {
            $this->load->view("selisihtunjangan",$data);
        }

        if ($ispdf==1) {
            $this->load->library('mpdf');
            $this->mpdf =
                new mPDF('',    // mode - default ''
                    '',    // format - A4, for example, default ''
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

            $stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
            $stylesheet = file_get_contents( $stylepdf);
            $this->mpdf->WriteHTML($stylesheet,1);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->WriteHTML($datavw);

            $this->mpdf->Output();
            die();
        }

        if($excelid==1) {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=selisihtunjangan.xls");
            //echo "$datavw";

            echo '<html>';
            echo '<head>
                    <link rel="stylesheet" type="text/css" href="'.base_url("assets/resources/css/print.css").'"/>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Laporan Selisih Tunjangan Kinerja</title>
                </head>
                <body>';
            echo $datavw;
            echo '</body></html>';
        }
    }

    function view5()
    {
        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));

        $myThnBln = date("Y-m", strtotime($postdatestart));

        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');
        $userid = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        $departemen = $this->input->post('org')!='undefined'?$this->input->post('org'):"1";


        $sql = "select tmt_berlaku from mastertunjangan order by tmt_berlaku desc limit 1";
        $query = $this->db->query($sql);
        $tgltunjangan = $query->row()->tmt_berlaku;

        $sql = "select * from mastertunjangan";
        $query = $this->db->query($sql);
        foreach($query->result() as $que) {
            $mastertunjbaru[$que->kelasjabatan] = $que->tunjangan;
        }

        $mastertunj = array();
        if($datestart >= strtotime($tgltunjangan)) {
            $sql = "select * from mastertunjangan";
            $query = $this->db->query($sql);
            foreach($query->result() as $que) {
                $mastertunj[$que->kelasjabatan] = $que->tunjangan;
            }
        } else {
            //$sql = "select * from tunjanganhistory where tglubah < '$tgltunjangan'";
            $sql = "select DISTINCT kelasjabatan,tunjangan FROM mastertunjangan_log where tglubah < '$tgltunjangan'";
            $query = $this->db->query($sql);
            foreach($query->result() as $que) {
                $mastertunj[$que->kelasjabatan] = $que->tunjangan;
            }
        }

        //echo $this->db->last_query();

        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(convertToArray($this->session->userdata('s_area'))):array();

        $tbar = array();
        $bbar = array();
        $holar = array();

        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $pkrga = array();
        $akrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname,
                'kategori'	=> $at->status_kategori_id
            );
            $pkrga[$at->atid] = $at->value;
            $akrga[$at->atid] = $at->ign_to;
        }

        $pkrgK = array();
        $pkrg = array();
        $absrecap = $this->report_model->getabs();
        foreach($absrecap->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname,
                'kategori'	=> $bs->status_kategori_id
            );
            $pkrg[$bs->abid] = $bs->value;
            if ($bs->status_kategori_id==12) {
                $pkrgK[$bs->abid] = $bs->value;
            }
        }

        $att = $this->report_model->getatt();
        foreach($att->result() as $tt) {
            $tbar[$tt->atid] = $tt->atname;
        }

        $dept = $this->report_model->getdept();
        foreach($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $yo = 0;
        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetailsxx($userar,$stspeg,$jnspeg);
            $yo=2;
        } else if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg,$jnspeg);
            $yo=1;
        }

        $byuser= $userid!='undefined'?1:0;

        $aten = array();
        $aben = array();

        $dataallay = array();

        foreach($queryemp->result() as $queq) {

            $unkir= isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0;

            $isplt=0;$isViewIt=1;
            if (isset($queq->tmt_plt) && isset($queq->plt_deptid) && isset($queq->plt_eselon))
            {
                if ($queq->tmt_plt !='0000-00-00') {
                    //echo $queq->userid." 1<br>";
                    $time = strtotime($queq->tmt_plt);
                    //$final = strtotime("+1 month", $time);
                    //pindah bulan udah dapet plt/plh
                    if ($datestart >= strtotime(date("Y-m-01",$time))) {
                        if ((intval(date('d', $time)) < 6) && date("Y-m", $datestart) === date('Y-m', $time)) {
                            $isplt = 1;
                            //echo $queq->userid." 2<br>";
                        } else {
                            $time2 = strtotime(date('Y-m-01', $time));
                            $final = strtotime("+1 month", $time2);
                            if ($datestart >= $final) {
                                $isplt = 1;
                            } else {
                                if (!in_array($queq->deptid, $orgid))
                                {
                                    continue;
                                }
                            }
                        }
                    } else{
                        $isViewIt=0;
                        if (!in_array($queq->deptid, $orgid))
                        {
                            continue;
                        }
                    }
                }
            }
            // }

            $unkirplt=0;
            $kriteriaPlt = 0;

            if (!empty($queq->plt_eselon))
            {
                $jbtnDef = strval(konversiEselonBaru($queq->eselon));
                $jbtnPlt = strval(konversiEselonBaru($queq->plt_eselon));

                if ($isplt==1) {
                    $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] * refTHP() :0;
                    if ( ($jbtnDef  > $jbtnPlt) ) {
                        $kriteriaPlt=1;
                        $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan] :0;
                        $unkirplt= ($unkirplt * refTHP() ) * refPLT();
                    }
                    if ( ($jbtnDef  == $jbtnPlt ) ) {
                        $kriteriaPlt=2;
                        /*$unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan]:0;
                        $unkirplt= ($unkirplt * refTHP()) * refPLT();*/
                        if ($queq->plt_kelasjabatan > $queq->kelasjabatan) {
                            $unkirplt = isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] : 0;
                            $unkirplt = ($unkirplt * refTHP());
                            $unkir = ($unkir * refTHP()) * refPLT();
                        } else
                        {
                            $unkirplt = isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] : 0;
                            $unkirplt = ($unkirplt * refTHP()) * refPLT();
                        }
                    }

                    if ( ($jbtnDef  < $jbtnPlt ) ) {
                        $kriteriaPlt=3;
                        //$klsjbatan = max(array($queq->kelasjabatan,$queq->plt_kelasjabatan));
						$klsjbatan = $queq->plt_kelasjabatan;
                        //$unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan] * refTHP() :0;
                        $unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* refTHP():0;
                        $unkir = 0;
                    }
                }

                if ($queq->payable == 0){
                    $unkir=0;
                    $unkirplt=0;
                }
            }


            if ($queq->payable == 0){
                $unkir=0;
                $unkirplt=0;
            }

            if ($isplt==1) {
                $dataallay[] = array(
                    'userid' => $queq->userid,
                    'empTitle' => $queq->title,
                    'empID' => $queq->badgenumber,
                    'empHire' => isset($queq->hireddate) ? date('d-m-Y', strtotime($queq->hireddate)) : '',
                    'empName' => $queq->name,
                    'golru' => $queq->golru,
                    'deptName' => isset($deptar[$queq->deptid]) ? $deptar[$queq->deptid] : '',
                    'kelasjabatan' => $queq->kelasjabatan,
                    'tunjanganprofesi' => $queq->tunjanganprofesi,
                    'tmtprofesi' => $queq->tmtprofesi,
                    'tunjangan' => $unkir,
                    'jftstatus' => $queq->jftstatus,
                    'jenisjabatan' => $queq->jenisjabatan,
                    'jenispegawai' => $queq->jenispegawai,
                    'kedudukan' => $queq->kedudukan,
                    'deptid' => $queq->deptid,
                    'tunjanganplt' => $unkirplt,
                    'plt_deptid' => $queq->plt_deptid,
                    'plt_eselon' => $queq->plt_eselon,
                    'tmt_plt' => $queq->tmt_plt,
                    'payable' => $queq->payable,
                    'eselon' => $queq->eselon,
                    'plt_jbtn' => $queq->plt_jbtn,
                    'plt_sk' => $queq->plt_sk,
                    'plt_kelasjabatan' => $queq->plt_kelasjabatan,
                    'isplt' => $isplt,
                    'kriteriaPlt' => $kriteriaPlt,
                    'plt_deptname' => isset($deptar[$queq->plt_deptid]) ? $deptar[$queq->plt_deptid] : '',
                    'dipisah' => 0,
                    'ishidden' => 0,
                    'isViewIt' => $isViewIt,
                    'orgf' => ""
                );
            }

            foreach($attend as $at3) {
                $aten[$queq->userid][$at3['atid']] = 0;
            }
            foreach($absen as $ab3) {
                $aben[$queq->userid][$ab3['abid']] = 0;
            }
        }
        //die();
        $compa = $this->report_model->getcompany();
        $company = array(
            'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
            'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
            'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
            'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
            'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
            'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
        );
        $dataallaye = array();
        $abc=0;
        $dataview = '';
        $datavw = '';

        foreach($dataallay as $queqe) {

            $querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
            $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
            $dataarray = array();
            $totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
            $totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
            $totallibur  = 0; $tubel = 0; $totaltubel = 0; $totaltubelplt=0;$tunjtubel = 0; $tunjtubelplt = 0; $tgltubel = 0;
            $totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;
            $tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0; $totplt = 0;$tunjplt = 0;
            $ttlmsk = array(); $tunjangan = array(); $tunjanganplt = array();$tunbulplt = array();
            $tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
            $jft = array(); $jfto = 0; $jftp = array();
            $jpeg = array(); $jpego = 0; $jpegp = array();
            $kedu = array(); $keduo = 0; $kedup = array();
            $totaltunjplt =[];$totaltunj =[];
            foreach($querytempo->result() as $que) {
                $ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunj[date('mY', strtotime($que->date_shift))] = 0;
                $totaltunjplt[date('mY', strtotime($que->date_shift))] = 0;
            }
            foreach($querytempo->result() as $que) {

                $kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
                if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
                $tunjang = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);
                if ($tunjang == 0) $tunjang = $mastertunj[$queqe['kelasjabatan']];

                if($tunjang!=0) {
                    $queqe['tunjanganasli']=$tunjang;
                    if ($queqe['userid'] != 0 ) {
                        //if (date("Y", $datestart) > 2016) {
                        $queqe['tunjangan'] = $tunjang * refTHP();
                        /*} else {
                            $queqe['tunjangan'] = $tunjang;
                        }*/
                    } else {
                        $queqe['tunjangan'] = 0;
                    }
                    //baru
                    if ($queqe['kriteriaPlt']==2)
                    {
                        if ( $queqe['plt_kelasjabatan'] > $queqe['kelasjabatan']) {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = ($tjngnga * refTHP()) * refPLT();
                        } else
                        {
                            $tjngnga = isset($mastertunj[$queqe['kelasjabatan']]) ? $mastertunj[$queqe['kelasjabatan']] : 0;
                            $queqe['tunjangan'] = $tjngnga * refTHP();
                        }
                    }
                    //CPNS aktif mulai 1 April 2019 sebesar 80%
                    $curenttime=strtotime($que->date_shift);
                    $bypasstime=strtotime('2019-04-01');
                    if ($curenttime>=$bypasstime){
                        if ($queqe['jftstatus']==1)
                        {
                            $queqe['tunjangan'] = $queqe['tunjangan'] * refTHPCP();
                        }
                    }
                }

                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($que->flaghol == 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($tunbuli!=$queqe['tunjangan']) {
                    $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))))? 1 : 1; //0.5 : 1
                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                    $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];
                }

                $byuser= $userid!='undefined'?1:0;


                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 3)
                    {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  = isOrgIn($orgtar);

                        //asalnya tidak sama
                        /*if ($cpltdeptid === $corgtar)
                        {
                            $queqe['ishidden'] = 0;
                        }*/
                        $queqe['ishidden'] = ($cpltdeptid != $corgtar)?1:0;
                        //echo $queqe['userid']." ".$cpltdeptid." ".$corgtar." ".$queqe['ishidden']."<br>";
                    }
                }

                if ($byuser==0)
                {
                    if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined')
                        {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            $cdeptid =isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            $corgtar  = isOrgIn($orgtar);
                            if ($dibagi==1)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }

                            if ($tunbuli != $queqe['tunjangan']) {
                                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'] * $kali;
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        $cdeptid = isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  = isOrgIn($orgtar);

                        if ($dibagi==1)
                        {
                            if ($cdeptid == $corgtar) //satu tempat defenitif
                            {
                                $queqe['dipisah']= 1;
                            }
                            if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                            {
                                $queqe['dipisah']= 2;
                            }
                        }
                    }
                } else
                {
                    if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined')
                        {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            $cdeptid =isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            $corgtar  =isOrgIn($orgtar);

                            $queqe['orgf']= $cdeptid." ".$cpltdeptid.' '.$corgtar;
                            if ($dibagi)
                            {
                                if ($cdeptid === $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid === $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }
                            if ($tunbuli != $queqe['tunjangan']) {
                                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan']))))) ? 1 : 1; //0.5 : 1
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'] * $kali;
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'] * $kali;
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        $cpltdeptid =isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        $cdeptid = isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        $corgtar  =  isOrgIn($orgtar);
                        if ($dibagi)
                        {
                            if ($cdeptid == $corgtar) //satu tempat defenitif
                            {
                                $queqe['dipisah']= 1;
                            }
                            if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                            {
                                $queqe['dipisah']= 2;
                            }
                        }
                    }
                }

                //else
                //	$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];
                $jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
                if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;

                if($jfto!=$queqe['jftstatus'])
                    $jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];
                /* else
                    $jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */

                $jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
                if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;

                if($jpego!=$queqe['jenispegawai'])
                    $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];
                /* else
                    $jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */

                $kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
                if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;

                if($keduo!=$queqe['kedudukan'])
                    $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];
                /* else
                    $kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */

                $tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
                //if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;
                if($tunjangprof!=0) {
                    if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi']))
                    {
                        $queqe['tunjanganprofesi'] = $tunjangprof;
                    } else {
                        $queqe['tunjanganprofesi']=0;
                    }
                }

                //if($tunpro!=$queqe['tunjanganprofesi'])
                //    $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];

                if($tunpro!=$queqe['tunjanganprofesi']) {
                    //if (date("Y", $datestart) > 2016) {
                    if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi'])) {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'] ; //* 0.5
                    } else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = 0;
                    }

                    /*} else {
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];

                    }*/
                }
                /* else
                    $tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */

                $kali = ($queqe['kedudukan']==4 && ($datestart >= strtotime(date("Y-m-01",strtotime($queqe['tmtkedudukan'])))))? 1 : 1; //0.5
                $tunbuli = $queqe['tunjangan'] * $kali;
                if (strtotime($que->date_shift)>=strtotime($queqe['tmtprofesi'])) {
                    $tunpro = $queqe['tunjanganprofesi'];
                } else{
                    $tunpro = 0;
                }
                $jfto = $queqe['jftstatus'];
                $jpego = $queqe['jenispegawai'];
                $keduo = $queqe['kedudukan'];
            }

            foreach($querytemp->result() as $que) {
                $late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
                $totalpersenkurang = 0; $totaljadwal++;
                $day = date('D', strtotime($que->date_shift));
                $date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
                if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
                if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));


                if(isset($tunbul[date('mY', strtotime($que->date_shift))])) {
                    if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubel = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        //echo $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        $tunjtubelplt = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                    }
                } else {
                    $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                }


                if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12 || $queqe['kedudukan']==13) { $tottun = 0;$totplt=0;}
                if($queqe['jenispegawai']==3 || $queqe['jenispegawai']==4) { $tottun = 0;$totplt=0;}

                if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunjanganplt[date('mY', strtotime($que->date_shift))] / 2;
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                    if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        $tubel = 1;
                        $tgltubel = strtotime($queqe['tmtkedudukan']);
                    }
                }

                if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 || $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }
                }

                if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                    $tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                    if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
                        /*if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))]) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        }
                        else {*/
                        //$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] - $tunjanganpro[date('mY', strtotime($que->date_shift))];
                        $tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]*2) - $tunjanganpro[date('mY', strtotime($que->date_shift))])*0.5;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        //}
                    }
                }

                $early=0;
                if($que->early_departure!=0) {
                    $early = $que->early_departure;
                }

                if($que->late!=0) {
                    $late = $que->late;

                    if ($que->late >3660) {
                        $telat = $que->late;
                        $late = $telat <= 0 ? 0 : $telat;
                    } else {
                        if ($que->ot_after != 0) {
                            if ($que->ot_after > 3600)
                                $telat = $que->late - 3600;
                            else
                                $telat = $que->late - $que->ot_after;

                            $late = $telat <= 0 ? 0 : $telat;
                        }
                    }

                    if (date("Y",$datestart)<2017)
                    {
                        if($late < 1860) $krglate = 0; //kurang dari 30 menit
                        else if($late >= 1860 && $late < 3660) $krglate = 0.5; //31- 61 menit
                        else if($late >= 3660 && $late < 5460) $krglate = 1; //61- 91 menit
                        else if($late >= 5460) $krglate = 1.5; //91 menit ke atas
                    } else
                    {
                        if($late < 3660) { // < 61 menit
                            $krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = 0.5;
                        }
                        else if($late >= 3660) { // >=61 menit ke atas
                            $telat = $que->late;
                            $krglate = 1;
                        }

                        //echo $telat." ".$krglate."<br>";
                        //jika terlambat >60 dan mengganti 60 menit
                        if ($que->late > 3660 ) {
                            $krglate = 1;
                            $telat = $que->late;
                        }

                        //berdasarkan referensi potongan untuk terlambat
                        $wry="select persentase FROM ref_potongan WHERE $late BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=1";
                        $qryPotongan = $this->db->query($wry);
                        if ($qryPotongan->num_rows()>0) {
                            $krglate = $qryPotongan->row()->persentase;
                            //$krglate = 0.5;
                            if ($early>=$late) $krglate = 0;
                            if ($check_out == null) $krglate = $qryPotongan->row()->persentase;
                        }
                    }
                }

                if($que->early_departure!=0) {
                    $early = $que->early_departure;

                    //if (date("Y",$datestart)>2016) {
                    if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;

                    //potongan PSW
                    $wry="select persentase FROM ref_potongan WHERE $early BETWEEN rng_menit_1*60 AND rng_menit_2*60 AND jns_potongan=2";
                    $qryPotongan = $this->db->query($wry);
                    if ($qryPotongan->num_rows()>0) {
                        $krgearly = $qryPotongan->row()->persentase;
                    }
                    /*} else {
                        if($early > 1 && $early < 1860) $krgearly = 0.5;
                        else if($early >= 1860 && $early < 3660) $krgearly = 1;
                        else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                        else if($early >= 5460) $krgearly = 1.5;
                    }*/
                }

                //if (date("Y",$datestart)>2016) {
                if ($check_in == null) $krglate = 1;
                if ($check_out == null) $krgearly = 1;
                /*} else {
                    if($check_in==null) $krglate = 1.5;
                    if($check_out==null) $krgearly = 1.5;
                }*/
                if($que->attendance == 'ALP') {
                    $krglate = 0;
                    $krgearly = 0;
                    //if (date("Y",$datestart)>2016) {
                    $krgalpa = 5;
                    /*} else {
                        $krgalpa = 3;
                    }*/
                }

                if($que->attendance == 'BLNK') {
                    $totalblnk++;
                }

                $s = 0;
                if(isset($bbar[$que->attendance])) {
                    $krglate = 0;
                    $krgearly = 0;
                    $krgalpa = 0;
                    $krgstatus = $pkrg[$que->attendance];
                    $s = 1;
                }

                if(isset($atar[$que->attendance])) {
                    if($que->attendance=='AT_SK01' || $que->attendance=='AT_AT1')
                        $krglate = 0;
                    else if($que->attendance=='AT_SK03' || $que->attendance=='AT_AT2')
                        $krgearly = 0;
                    else {
                        $krglate = 0;
                        $krgearly = 0;
                    }
                    $krgalpa = 0;
                    $krgstatus = $pkrga[$que->attendance];

                    switch ($akrga[$que->attendance])
                    {
                        case 1:
                            $krglate = 0;
                            break;
                        case 2:
                            $krgearly = 0;
                            break;
                        default:
                            $krglate = 0;
                            $krgearly = 0;
                            break;
                    }

                }

                if($krglate > 0 && $que->attendance!='NWK' &&
                    $que->attendance!='NWDS' &&
                    $que->workinholiday!=1  ) {

                    $totalpersenkurang = $totalpersenkurang + $krglate;
                    $tunj = $tunj + (($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgearly > 0 && $que->attendance!='NWK' &&
                    $que->attendance!='NWDS' &&
                    $que->workinholiday!=1  ) {

                    $totalpersenkurang = $totalpersenkurang + $krgearly;
                    $tunj = $tunj + (($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }
                if($krgalpa != 0 && $que->workinholiday!=1  ) {

                    $totalpersenkurang = $totalpersenkurang + $krgalpa;
                    $totalalpa++;
                    $tunj = $tunj + (($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgstatus != 0  && $queqe['payable']!=0) {
                    $totalpersenkurang = $totalpersenkurang + $krgstatus;
                    if($que->attendance=='AB_12') $totalpembatalan++;
                    $tunj = $tunj + (($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                /*  khusus upacara status dibayarkan
                    tgl upacara,tgl check in kosong dan bukan Tubel
                */
                if (isset($que->date_shift2) && !isset($que->date_in2) 
                    && !isset($que->check_in2) && $queqe['payable'] !=0 && $tubel==0)
                {
                    if(substr($que->attendance,0,2)!='AB') //jika bukan status ketidak hadiran
                    {
                        $nkurang = refUpacara();
                        $totalpersenkurang = $totalpersenkurang + $nkurang;
                        $tunj = $tunj + (($nkurang /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        $tunjplt = $tunjplt + (($nkurang /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    }
                }

                $totalpersensemua = $totalpersensemua + $totalpersenkurang;
                $totalmasuk = array_sum($ttlmsk);

                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1  && $queqe['payable']!=0) {
                    if($tubel==1) {
                        $totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltubelplt = $totaltubelplt+ $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }

                if($que->flaghol == 1) {
                    if($tubel==1) {
                        //$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        //$totaltubelplt = $totaltubelplt + $totaltunjplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }
                //khususTubel
                if ($tubel==1)
                {
                    if ($que->attendance == 'NWK' || $que->flaghol==1 || $que->attendance=="")
                    {

                    } else
                    {
                        $totaltubel = $totaltubel + $tunjtubel*0.5 ;//$tunjangan[date('mY', strtotime($que->date_shift))];
                        //echo $que->date_shift." ".$que->attendance." ".$tunjtubel*0.5."<br>";
                    }
                }
            }

            $tottun = isset($totaltunj)?array_sum($totaltunj):0;
            $totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;

            if($tubel==1) {
                $tunj = 0;
                $tottun = ($totaltubel/$totalmasuk);
                $totplt= $tunjtubelplt;
            }

            if($totalalpa == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
            }

            if($totalpembatalan == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
            }

            $totalsemua = $totalalpa + $totalpembatalan;

            if($totalsemua == $totalmasuk) {
                $tottun = 0;
                $totplt = 0;
            }

            if($totalblnk == $totaljadwal) {
                $tottun = 0;
                $totplt = 0;
            }

            if ($queqe['payable']==0)
            {
                $tottun = 0;
                $totplt = 0;
            }

            $dataallaye[] = array(
                'userid'   	=> $queqe['userid'],
                'empTitle' 	=> $queqe['empTitle'],
                'empID' 	=> $queqe['empID'],
                'empHire'	=> $queqe['empHire'],
                'empName' 	=> $queqe['empName'],
                'deptName' 	=> $queqe['deptName'],
                'kelasjabatan' 			=> $queqe['kelasjabatan'],
                'golongan' 				=> $queqe['golru'],
                'isplt'                 =>$queqe['isplt'],
                'plt_deptname' => $queqe['plt_deptname'],
                'kriteriaPlt' => $queqe['kriteriaPlt'],
                'plt_jbtn' => $queqe['plt_jbtn'],
                'plt_kelasjabatan' => $queqe['plt_kelasjabatan'],
                'byuser'=>  $byuser,
                "dipisah" =>$queqe['dipisah'],
                "ishidden" =>$queqe['ishidden'],
                'tunjangan' 			=> $tottun,
                'tunjanganplt' =>       $totplt,
                'totaltunjangan'		=> $tottun==0?0:$tunj,
                'totaltunjanganplt'		=> $totplt==0?0:$tunjplt,
                "totaltunj" =>$totaltunj,
                "totaltunjplt" => $totaltunjplt
            );
        }

        $data = array(
            "dateinfo" =>   date('d-m-Y', $datestart)." s/d ".date('d-m-Y', $datestop),
            "cominfo" =>    $company,
            "empinfo" =>    $dataallaye,
            "data" =>       $deptar[$departemen],
            "excelid"=>     $excelid,
            "pdfid"=> $ispdf,
        );


        if($excelid==1) {
            $dataview = $this->load->view("selisihtunjanganplt",$data,true);
            $datavw = $datavw.$dataview;
        } else {
            $this->load->view("selisihtunjanganplt",$data);
        }

        if ($ispdf==1) {
            $this->load->library('mpdf');
            $this->mpdf =
                new mPDF('',    // mode - default ''
                    '',    // format - A4, for example, default ''
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
            $stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
            $stylesheet = file_get_contents( $stylepdf );
            $this->mpdf->WriteHTML($stylesheet,1);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->WriteHTML($datavw);

            $this->mpdf->Output();
            die();
        }

        if($excelid==1) {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=lapkeuangantunjangankinerja.xls");
            //echo "$datavw";

            echo '<html>';
            echo '<head>
                    <link rel="stylesheet" type="text/css" href="'.base_url("assets/css/print.css").'"/>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Laporan Selisih Tunjangan Kinerja</title>
                </head>
                <body>';
            echo $datavw;
            echo '</body></html>';
        }
    }

    public function doimport(){
        $config['upload_path'] = './assets/uploads/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload()){
            $this->session->set_flashdata('msgImport', $this->upload->display_errors());
            redirect('rpttunjangan','refresh');
        }
        else{
            $data = array('upload_data' => $this->upload->data());
            $upload_data = $this->upload->data();
            $filename = $upload_data['file_name'];
            $ststme = $this->utils->upload_data($this->input->post('periode'),$filename);
            unlink('./assets/uploads/'.$filename);
            $this->session->set_flashdata('msgImport', $ststme ? "Data sudah diimport..!!":"Ada Kesalahan mengimport data!!");
            redirect('rpttunjangan','refresh');
        }
    }

    public function expimportxls()
    {
        $periode = $this->input->post('start');

        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);

        if (isset($org) && ($org!='' || $org!=null))
        {
            $orgid = $this->pegawai->deptonall($org);
        } else {
            $orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        }

        $this->db->select('nip,userinfo.name,jumlah');
        $this->db->from('data_sikerja');
        $this->db->join('userinfo','data_sikerja.nip=userinfo.userid');

        if(!empty($orgid)) {
            $s = array();
            foreach($orgid as $ar)
                $s[] = "'".$ar."'";
            $this->db->where_in("deptid",$s);
        }

        if(!empty($stspeg)) {
            $s = array();
            foreach($stspeg as $ar)
                $s[] = $ar;
            $this->db->where_in("jftstatus",$s);
        }

        if(!empty($jnspeg)) {
            $s = array();
            foreach($jnspeg as $ar)
                $s[] = $ar;
            $this->db->where_in("jenispegawai",$s);
        }
        $this->db->where("bulan",$periode);
        $data['listdata']=$this->db->get()->result();

        $dataview = $this->load->view("expsikerja",$data,true);

        header("Content-type:application/x-msdownload");
        header("Content-Disposition: attachment; filename=sikerja-".$periode.".xls");
        echo '<html>';
        echo '<head>
                    <link rel="stylesheet" type="text/css" href="'.base_url("assets/resources/css/print.css").'"/>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Data SiKerja</title>
                </head>
                
                <body>';
        echo $dataview;
        echo '</body></html>';

    }
}
