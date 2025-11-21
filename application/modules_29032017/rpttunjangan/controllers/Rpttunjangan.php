<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rpttunjangan extends MX_Controller {

	function Rpttunjangan(){
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
        $this_url = site_url('rpttunjangan/pagging/');
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
        $start = $this->input->post('start');

        //createLogUser("Delete Data ".$nId,"Pengguna");
        $data['msg'] = 'Data berhasil diproses..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function view()
    {
        $jnsLap = $this->input->post('jnslap');
        if ($jnsLap==1) {
            $this->view1();
        } else if ($jnsLap==2) {
            $this->view2();
        } else  {
            $this->view3();
        }
    }


    function view1()
    {
        $postdatestart = $this->input->post('start')."-01";
        $postdatestop = date("Y-m-t", strtotime($postdatestart)); //$this->input->post('end');
        $excelid = $this->input->post('xls');
        $userid = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        $mastertunj = array();

        //trap referensi data
        $this->session->set_userdata('tahundata',date("Y",$datestart));

        $sql = "select * from mastertunjangan";
        $query = $this->db->query($sql);
        foreach ($query->result() as $que) {
            $mastertunj[$que->kelasjabatan] = $que->tunjangan;
        }


        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(explode(',', $this->session->userdata('s_area'))):array();

        $tbar = array();
        $bbar = array();
        $holar = array();
        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $pkrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname
            );
            $pkrga[$at->atid] = $at->value;
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
        if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg);
            $yo=1;
        }

        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetailsxx($userar);
            $yo=2;
        }

        $aten = array();
        $aben = array();

        $dataallay = array();
        $dataallu = array();
        foreach($queryemp->result() as $queq) {

            $unkir= isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0;

            $isplt=0;
            if (date("Y",$datestart)>2016) {
                if (isset($queq->tmt_plt) && isset($queq->plt_deptid) && isset($queq->plt_eselon))
                {
                    $time = strtotime($queq->tmt_plt);
                    $final = strtotime("+1 month", $time);
                    if ($datestart >= $final) {
                        $isplt=1;
                    }
                }
            }

            $jbtnAwal = strval($this->konversiEselon($queq->eselon));
            $jbtnAkhir = strval($this->konversiEselon($queq->plt_eselon));
            $unkirplt=0;
            $kriteriaPlt = 0;
            if ($isplt==1) {
                $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] * 0.5:0;
                if ( ($jbtnAwal  < $jbtnAkhir) ) {
                    $kriteriaPlt=1;
                    $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan] :0;
                    $unkirplt= ($unkirplt * 0.5) * 0.2;
                }
                if ( ($jbtnAwal  == $jbtnAkhir ) ) {
                    $kriteriaPlt=2;
                    $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan]:0;
                    $unkirplt= ($unkirplt * 0.5) * 0.2;
                }

                if ( ($jbtnAwal  > $jbtnAkhir ) ) {
                    $kriteriaPlt=3;
                    $klsjbatan = max(array($queq->kelasjabatan,$queq->plt_kelasjabatan));
                    $unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* 0.5:0;
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
                'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
                'kelasjabatan' => $queq->kelasjabatan,
                'tunjanganprofesi' => $queq->tunjanganprofesi,
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
                'isplt' =>$isplt,
                'kriteriaPlt' =>$kriteriaPlt,
                'plt_deptname' => isset($deptar[$queq->plt_deptid])?$deptar[$queq->plt_deptid]:'',
                'dipisah'=>0,
                'ishidden'=>0,
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
            $tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0; $totplt = 0;$tunjplt = 0;
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

            foreach($querytempo->result() as $que) {

                $kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
                if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
                $tunjang = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);

                if($tunjang!=0) {
                    $queqe['tunjanganasli']=$tunjang;
                    if ($queqe['userid'] != 0 ) {
                        if (date("Y", $datestart) > 2016) {
                            $queqe['tunjangan'] = $tunjang * 0.5;
                        } else {
                            $queqe['tunjangan'] = $tunjang;
                        }
                    } else {
                        $queqe['tunjangan'] = 0;
                    }
                }


                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($que->flaghol == 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($tunbuli!=$queqe['tunjangan'])
                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];
                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];

                $byuser= $userid!='undefined'?1:0;
                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 3)
                    {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        ///$lnpltdepid = $this->isEselon2($pltdepid);
                        //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                        $cpltdeptid =$this->isOrgIn($pltdepid);

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        //$lorgtar = $this->isEselon2($orgtar);
                        //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                        $corgtar  = $this->isOrgIn($orgtar);

                        if ($cpltdeptid != $corgtar)
                        {
                            $queqe['ishidden'] = 1;
                        }
                    }
                }

                if ($byuser==0)
                {
                    if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined')
                        {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            //$lnpltdepid = $this->isEselon2($pltdepid);
                            //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                            $cpltdeptid =$this->isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            //$lndepid = $this->isEselon2($depid);
                            //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                            $cdeptid =$this->isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            //$lorgtar = $this->isEselon2($orgtar);
                            //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                            $corgtar  = $this->isOrgIn($orgtar);
                            //echo $dibagi." PLT:".$queqe['plt_deptid']."-".$pltdepid." DEF:".$queqe['deptid']."-".$depid." ".$corgtar." : ".$queqe['userid']."</br>";
                            //$queqe['orgf']= $cdeptid." ".$cpltdeptid.' '.$corgtar." ".$dibagi;
                            if ($dibagi==1)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                    //$queqe['orgf']= $cdeptid.' '.$corgtar;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                    // $queqe['orgf']= $cpltdeptid.' '.$corgtar;
                                }
                            }
                            //$queqe['orgf']= $cdeptid." ".$cpltdeptid.' '.$corgtar." ".$dibagi." ".$lorgtar." ".$queqe['deptid']." ".$queqe['plt_deptid'];

                            if ($tunbuli != $queqe['tunjangan']) {
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        //$lnpltdepid = $this->isEselon2($pltdepid);
                        //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                        $cpltdeptid =$this->isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        //$lndepid = $this->isEselon2($depid);
                        //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                        $cdeptid = $this->isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        //$lorgtar = $this->isEselon2($orgtar);
                        //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                        $corgtar  = $this->isOrgIn($orgtar);

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
                            //$lnpltdepid = $this->isEselon2($pltdepid);
                            //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                            $cpltdeptid =$this->isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            //$lndepid = $this->isEselon2($depid);
                            //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                            $cdeptid =$this->isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            //$lorgtar = $this->isEselon2($orgtar);
                            //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                            $corgtar  =$this->isOrgIn($orgtar);

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
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        //$lnpltdepid = $this->isEselon2($pltdepid);
                        //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                        $cpltdeptid =$this->isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        //$lndepid = $this->isEselon2($depid);
                        //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                        $cdeptid = $this->isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        //$lorgtar = $this->isEselon2($orgtar);
                        //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                        $corgtar  =  $this->isOrgIn($orgtar);
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
                if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;

                if($tunpro!=$queqe['tunjanganprofesi'])
                    $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];
                /* else
                    $tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */

                $tunbuli = $queqe['tunjangan'];
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
                    }
                } else {
                    $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                }


                if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12) { $tottun = 0;$totplt=0;}
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
                        $tgltubel = strtotime($que->date_shift);
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
                            //khusus profesi
                            $tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]*2) - $tunjanganpro[date('mY', strtotime($que->date_shift))])*0.5;
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
                    if($que->ot_after!=0) {
                        if($que->ot_after>3600)
                            $telat = $que->late - 3600;
                        else
                            $telat = $que->late - $que->ot_after;
                        $late = $telat<=0?0:$telat;
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
                        if ($que->late > 3660 && $que->ot_after>3660)
                            $krglate = 1;

                    }
                }

                if($que->early_departure!=0) {
                    $early = $que->early_departure;

                    if (date("Y",$datestart)>2016) {
                        if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;
                    } else {
                        if($early > 1 && $early < 1860) $krgearly = 0.5;
                        else if($early >= 1860 && $early < 3660) $krgearly = 1;
                        else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                        else if($early >= 5460) $krgearly = 1.5;
                    }
                }

                if (date("Y",$datestart)>2016) {
                    if ($check_in == null) $krglate = 1;
                    if ($check_out == null) $krgearly = 1;
                } else {
                    if($check_in==null) $krglate = 1.5;
                    if($check_out==null) $krgearly = 1.5;
                }
                if($que->attendance == 'ALP') {
                    $krglate = 0;
                    $krgearly = 0;
                    if (date("Y",$datestart)>2016) {
                        $krgalpa = 5;
                    } else {
                        $krgalpa = 3;
                    }
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
                    if($que->attendance=='AT_AT4' || $que->attendance=='AT_AT3' || $que->attendance=='AT_AT1')
                        $krglate = 0;
                    else if($que->attendance=='AT_AT5' || $que->attendance=='AT_AT2')
                        $krgearly = 0;
                    else {
                        $krglate = 0;
                        $krgearly = 0;
                    }
                    $krgalpa = 0;
                    $krgstatus = $pkrga[$que->attendance];
                }

                if($krglate > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {
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
                    $tunj = $tunj + (($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {
                    $dataarray[] = array(
                        'day'			=> $day,
                        'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                        'status'		=> $que->check_out!=null?'Pulang lebih awal':'Tidak absen pulang',
                        'nilai'			=> $que->check_out==null?null:$this->report_model->itungan($que->early_departure),
                        'pengurangan'	=> $krgearly,
                        'total'			=> ($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                        'totalplt'			=> ($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                    );
                    $totalpersenkurang = $totalpersenkurang + $krgearly;
                    $tunj = $tunj + (($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }
                if($krgalpa != 0 && $que->workinholiday!=1) {
                    $dataarray[] = array(
                        'day'			=> $day,
                        'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                        'status'		=> 'Alpa',
                        'nilai'			=> null,
                        'pengurangan'	=> $krgalpa,
                        'total'			=> ($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                        'totalplt'			=> ($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                    );
                    $totalpersenkurang = $totalpersenkurang + $krgalpa;
                    $totalalpa++;
                    $tunj = $tunj + (($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgstatus != 0  && $queqe['payable']!=0) {
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
                    $tunj = $tunj + (($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));

                }

                $totalpersensemua = $totalpersensemua + $totalpersenkurang;
                $totalmasuk = array_sum($ttlmsk);

                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1 ) {
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
                        $totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltubelplt = $totaltubelplt + $totaltunjplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }
            }

            $tottun = isset($totaltunj)?array_sum($totaltunj):0;
            $totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;
            //echo $tottun;
            if($tubel==1) {
                $dataarray[] = array(
                    'day'			=> '',
                    'date'			=> '',
                    'status'		=> 'Tugas Belajar Per Tgl '.date('d-m-Y', $tgltubel),
                    'nilai'			=> null,
                    'pengurangan'	=> '50',
                    'total'			=> $totaltubel,
                    'totalplt'			=> 0
                );
                $tunj = $tunj + $totaltubel;
                $tottun = $tunjtubel;
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
            $datafoot = array('totalpersen' => $totalpersensemua,
                'total' => $tottun == 0 ? 0 : $tunj,
                'totalplt' => $totplt == 0 ? 0 : $tunjplt
            );

            $byuser= $userid!='undefined'?1:0;
            $data = array(
                "dateinfo" => date('d-m-Y', $datestart) . " s/d " . date('d-m-Y', $datestop), "index" => $abc,
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
                "ishidden" =>$queqe['ishidden']);
            $abc++;
            if($excelid==1) {
                $dataview = $this->load->view("laporan",$data,true);
                $datavw .= $dataview;
            } else {
                $this->load->view("laporan",$data);
            }
        }

        if($excelid==1) {
            header("Content-type:application/x-msdownload");
            header("Content-Disposition: attachment; filename=tunjangankinerja.xls");
            echo '<html>';
            echo '<head>
                    <link rel="stylesheet" type="text/css" href="'.base_url("assets/resources/css/print.css").'"/>
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
        $postdatestart = $this->input->post('start')."-01";
        $postdatestop = date("Y-m-t", strtotime($postdatestart)); //$this->input->post('end');
        $excelid = $this->input->post('xls');
        $userid = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        $departemen = $this->input->post('org')!='undefined'?$this->input->post('org'):"1";
        $mastertunj = array();

        $this->session->set_userdata('tahundata',date("Y",$datestart));

        $sql = "select * from mastertunjangan";
        $query = $this->db->query($sql);
        foreach ($query->result() as $que) {
            $mastertunj[$que->kelasjabatan] = $que->tunjangan;
        }



        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('s_dept'))):array();

        $areaid = $this->session->userdata('s_area')!=''?$this->pegawai->areaonall(explode(',', $this->session->userdata('s_area'))):array();

        $tbar = array();
        $bbar = array();
        $holar = array();
        $range = ($datestop - $datestart) / 86400;
        $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

        $pkrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname
            );
            $pkrga[$at->atid] = $at->value;
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
        if($this->input->post('org')!='undefined') {
            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg);
            $yo=1;
        }

        if($userid!='undefined') {
            $userar = explode(',', $userid);
            $queryemp = $this->report_model->getuseremployeedetailsxx($userar,$stspeg);
            $yo=2;
        }

        $byuser= $userid!='undefined'?1:0;

        $aten = array();
        $aben = array();

        $dataallay = array();
        $dataallu = array();
        foreach($queryemp->result() as $queq) {

            $unkir= isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0;

            $isplt=0;
            if (isset($queq->tmt_plt) && isset($queq->plt_deptid) && isset($queq->plt_eselon))
            {
                $time = strtotime($queq->tmt_plt);
                $final = strtotime("+1 month", $time);
                if ($datestart >= $final) {
                    $isplt=1;
                }
            }

            $jbtnAwal = strval($this->konversiEselon($queq->eselon));
            $jbtnAkhir = strval($this->konversiEselon($queq->plt_eselon));
            $unkirplt= isset($mastertunj[$queq->kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] * 0.5:0;
            $kriteriaPlt = 0;
            if ($isplt==1) {
                if ( ($jbtnAwal  < $jbtnAkhir) ) {
                    $kriteriaPlt=1;
                    $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan] :0;
                    $unkirplt= ($unkirplt * 0.5) * 0.2;
                }
                if ( ($jbtnAwal  == $jbtnAkhir ) ) {
                    $kriteriaPlt=2;
                    $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan]:0;
                    $unkirplt= ($unkirplt * 0.5) * 0.2;
                }

                if ( ($jbtnAwal  > $jbtnAkhir ) ) {
                    $kriteriaPlt=3;
                    $klsjbatan = max(array($queq->kelasjabatan,$queq->plt_kelasjabatan));
                    if (date("Y",$datestart)>2016)
                    {
                        $unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan]* 0.5:0;
                    } else
                    {
                        $unkirplt= isset($mastertunj2016[$klsjbatan])?$mastertunj2016[$klsjbatan]* 0.5:0;
                    }
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
                'kelasjabatan' => $queq->kelasjabatan,
                'tunjanganprofesi' => $queq->tunjanganprofesi,
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
                'isplt' =>$isplt,
                'kriteriaPlt' =>$kriteriaPlt,
                'plt_deptname' => isset($deptar[$queq->plt_deptid])?$deptar[$queq->plt_deptid]:'',
                'dipisah'=>0,
                'ishidden'=>0,
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

                if($tunjang!=0) {
                    if ($queqe['userid'] != 0 ) {
                        if (date("Y", $datestart) > 2016) {
                            $queqe['tunjangan'] = $tunjang * 0.5;
                        } else {
                            $queqe['tunjangan'] = $tunjang;
                        }
                    } else {
                        $queqe['tunjangan'] = 0;
                    }
                }
                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($que->flaghol == 1)
                    $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                if($tunbuli!=$queqe['tunjangan'])
                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];
                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];



                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        //$lnpltdepid = $this->isEselon2($pltdepid);
                        //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                        $cpltdeptid =$this->isOrgIn($pltdepid);

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        //$lorgtar = $this->isEselon2($orgtar);
                        //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                        $corgtar  = $this->isOrgIn($orgtar);
                        if ($cpltdeptid != $corgtar)
                        {
                            $queqe['ishidden'] = 1;
                        }
                    }
                }

                if ($byuser==0) {
                    if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                        if($this->input->post('org')!='undefined') {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            //$lnpltdepid = $this->isEselon2($pltdepid);
                            //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                            $cpltdeptid =$this->isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            //$lndepid = $this->isEselon2($depid);
                            //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                            $cdeptid =$this->isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                            //$lorgtar = $this->isEselon2($orgtar);
                            //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                            $corgtar  = $this->isOrgIn($orgtar);

                            //echo $queqe['userid']." : ".$cpltdeptid."-".$cdeptid." ".$dibagi."</br>";
                            //$queqe['orgf']= $cpltdeptid.' '.$corgtar;
                            if ($dibagi)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                    //$queqe['orgf']= $cdeptid.' '.$corgtar;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                    // $queqe['orgf']= $cpltdeptid.' '.$corgtar;
                                }
                            }
                            if ($tunbuli != $queqe['tunjangan']) {
                                $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];
                                $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];
                            }
                        }
                    }

                    if ($queqe['kriteriaPlt'] == 3) {
                        $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                        //$lnpltdepid = $this->isEselon2($pltdepid);
                        //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                        $cpltdeptid =$this->isOrgIn($pltdepid);

                        $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                        //$lndepid = $this->isEselon2($depid);
                        //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                        $cdeptid = $this->isOrgIn($depid);

                        $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                        $orgtar = strlen($this->input->post('org'))==7 ? "0".$this->input->post('org'): $this->input->post('org');
                        //$lorgtar = $this->isEselon2($orgtar);
                        //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                        $corgtar  = $this->isOrgIn($orgtar);
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
                if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;

                if($tunpro!=$queqe['tunjanganprofesi'])
                    $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];
                /* else
                    $tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */

                $tunbuli = $queqe['tunjangan'];
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
                    }
                } else {
                    $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                    $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                }


                if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12) { $tottun = 0;$totplt=0;}
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
                        $tgltubel = strtotime($que->date_shift);
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
                            $tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]*2) - $tunjanganpro[date('mY', strtotime($que->date_shift))])*0.5;
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
                    if($que->ot_after!=0) {
                        if($que->ot_after>3600)
                            $telat = $que->late - 3600;
                        else
                            $telat = $que->late - $que->ot_after;
                        $late = $telat<=0?0:$telat;
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
                        if ($que->late > 3660 && $que->ot_after>3660)
                            $krglate = 1;
                    }
                }

                if($que->early_departure!=0) {
                    $early = $que->early_departure;

                    if (date("Y",$datestart)>2016) {
                        if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;
                    } else {

                        if($early > 1 && $early < 1860) $krgearly = 0.5;
                        else if($early >= 1860 && $early < 3660) $krgearly = 1;
                        else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                        else if($early >= 5460) $krgearly = 1.5;
                    }
                }

                if (date("Y",$datestart)>2016) {
                    if ($check_in == null) $krglate = 1;
                    if ($check_out == null) $krgearly = 1;
                } else {
                    if($check_in==null) $krglate = 1.5;
                    if($check_out==null) $krgearly = 1.5;
                }
                if($que->attendance == 'ALP') {
                    $krglate = 0;
                    $krgearly = 0;
                    if (date("Y",$datestart)>2016) {
                        $krgalpa = 5;
                    } else {
                        $krgalpa = 3;
                    }
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
                    if($que->attendance=='AT_AT4' || $que->attendance=='AT_AT3' || $que->attendance=='AT_AT1')
                        $krglate = 0;
                    else if($que->attendance=='AT_AT5' || $que->attendance=='AT_AT2')
                        $krgearly = 0;
                    else {
                        $krglate = 0;
                        $krgearly = 0;
                    }
                    $krgalpa = 0;
                    $krgstatus = $pkrga[$que->attendance];
                }

                if($krglate > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {

                    $totalpersenkurang = $totalpersenkurang + $krglate;
                    $tunj = $tunj + (($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }

                if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {

                    $totalpersenkurang = $totalpersenkurang + $krgearly;
                    $tunj = $tunj + (($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    $tunjplt = $tunjplt + (($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                }
                if($krgalpa != 0 && $que->workinholiday!=1) {

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

                $totalpersensemua = $totalpersensemua + $totalpersenkurang;
                $totalmasuk = array_sum($ttlmsk);

                if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1 ) {
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
                        $totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltubelplt = $totaltubelplt + $totaltunjplt[date('mY', strtotime($que->date_shift))];
                    } else {
                        $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                        $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                    }
                }
            }

            $tottun = isset($totaltunj)?array_sum($totaltunj):0;
            $totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;

            if($tubel==1) {

                $tunj = $tunj + $totaltubel;
                $tottun = $tunjtubel;
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
            "excelid"=>     $excelid
        );
        if($excelid==1) {
            $dataview = $this->load->view("laporan2",$data,true);
            $datavw = $datavw.$dataview;
        } else {
            $this->load->view("laporan2",$data);
        }
        if($excelid==1) {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=rekapitulasitunjangankinerja.xls");
            //echo "$datavw";

            echo '<html>';
            echo '<head>
                    <link rel="stylesheet" type="text/css" href="'.base_url("assets/resources/css/print.css").'"/>
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

    }

    function konversiEselon($kode=null)
    {
        $kodes = strtoupper($kode);
        $kodef=preg_replace('/\s+/', '', $kodes);
        $arrk = array("I.A"=>11,"I.B"=>12,"I.C"=>12,"I.D"=>12,
            "II.A"=>21,"II.B"=>22,"II.C"=>23,"II.D"=>24,
            "III.A"=>31,"III.B"=>32,"III.C"=>33,"III.D"=>34,
            "IV.A"=>41,"IV.B"=>42,"IV.C"=>43,"IV.D"=>44,
            "V.A"=>51,"V.B"=>52,"V.C"=>53,"V.D"=>54,""=>99);
        return $arrk[$kodef];
    }

    function isEselon2($kode=null)
    {
        $arrk = array("01","07","11","12");
        return in_array(substr($kode,0,2),$arrk);
    }

    function isOrgIn($kode=null)
    {
        $arrk = array(
            "0706","0707","0708", //PMD
            "1107","1108","1109","1110", //BPSDM
            "1217","1218","1219","1220","1228","1229","1230" //IPDN
        );

        if (substr($kode,0,2)=="01") //khusus setjen
        {
            return substr($kode,0,4);
        } else{
            return in_array(substr($kode,0,4),$arrk)>0 ? substr($kode,0,4): substr($kode,0,2);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */