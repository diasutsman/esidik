<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rptjbd extends MX_Controller {
    private $aAkses;
	function Rptjbd(){
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

        $this->aAkses = akses("Rptjbd", $this->session->userdata('s_access'));

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
        
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();
        $this->template->load('template','display',$data);
    }

    public function simpan_laporan(){
        $ket1   = $this->input->post('ket1');
        $ket2   = $this->input->post('ket2');
        $ket3   = $this->input->post('ket3');
        $ket4   = $this->input->post('ket4');
        $ket5   = $this->input->post('ket5');
        $ket6   = $this->input->post('ket6');
        $ket7   = $this->input->post('ket7');
        $ket8   = $this->input->post('ket8');
        $ket9   = $this->input->post('ket9');
        $ket10  = $this->input->post('ket10');
        $org    = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $jnspeg = $this->input->post('jnspeg');
        $mulai  = dmyToymd($this->input->post('start'));
        $akhir  = dmyToymd($this->input->post('end'));

        if (isset($org) && ($org!='' || $org!=null))
        {
            $orgid = $org;
        } else {
            $orgid = $this->session->userdata('s_dept');
        }

        $mulai=((isset($mulai) && ($mulai!='' || $mulai!=null))?$mulai:date("Y-m-d"));
        $akhir=((isset($akhir) && ($akhir!='' || $akhir!=null))?$akhir:date("Y-m-d"));

        $data['ket1'] = $ket1;
        $data['ket2'] = $ket2;
        $data['ket3'] = $ket3;
        $data['ket4'] = $ket4;
        $data['ket5'] = $ket5;
        $data['ket6'] = $ket6;
        $data['ket7'] = $ket7;
        $data['ket8'] = $ket8;
        $data['ket9'] = $ket9;
        $data['ket10'] = $ket10;
        $data['tanggal_awal']  = $mulai;
        $data['tanggal_akhir'] = $akhir;
        $data['unitkerja']   = $orgid;
        $data['create_by']   = $this->session->userdata('s_username');
        $data['create_date'] = date('Y-m-d H:i:s');
        $xss_data            = $this->security->xss_clean($data);
        $this->db->insert('riw_panrb', $xss_data);
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
        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $jnspeg = $this->input->post('jnspeg');

        $mulai = dmyToymd($this->input->post('start'));
        $akhir = dmyToymd($this->input->post('end'));
        
        $SQLcari  = "";
        $SQLcaripanrb = "";
        $SQLJadwal  = "";
        $SQLcari2 = " AND a.attendance = 'WFH' ";
        $SQLcari3 = " AND a.attendance = 'AB_13' ";
        $SQLcari4 = " AND a.absence IN('CVD1','CVD2') ";
        $SQLcari5 = " AND a.absence = 'CVD1' ";
        $SQLcari6 = " AND a.absence = 'CVD2' ";
        $SQLcari7 = " AND a.absence = 'S-K' ";
        $SQLcari8 = " AND a.attendance IN ('AB_3','AB_4','AB_5','AB_6','AB_7','AB_8','AB_14','AB_20') ";
        
        $mulai=((isset($mulai) && ($mulai!='' || $mulai!=null))?$mulai:date("Y-m-d"));
        $akhir=((isset($akhir) && ($akhir!='' || $akhir!=null))?$akhir:date("Y-m-d"));
		
        $SQLcari2 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
		$SQLcari3 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
		$SQLcari4 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
		$SQLcari5 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
		$SQLcari6 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLcari7 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLcari8 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLJadwal .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLcaripanrb .= " and tanggal_awal = '".$mulai."' and tanggal_akhir = '".$akhir."' ";

        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari  .= " AND ( a.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari2 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari3 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari4 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari5 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari6 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari7 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari8 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLJadwal .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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

            $SQLcari  .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari2 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari3 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari4 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari5 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari6 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari7 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari8 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLJadwal .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcaripanrb .= " and unitkerja in (".implode(',', $s).") ";
        }

        if(!empty($jnspeg)) {
            $s = array();
            foreach($jnspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari  .= " and a.jenispegawai in (".implode(',', $s).") ";
            $SQLcari2 .= " and b.jenispegawai in (".implode(',', $s).") ";
            $SQLcari3 .= " and b.jenispegawai in (".implode(',', $s).") ";
            $SQLcari4 .= " and b.jenispegawai in (".implode(',', $s).") ";
            $SQLcari5 .= " and b.jenispegawai in (".implode(',', $s).") ";
            $SQLcari6 .= " and b.jenispegawai in (".implode(',', $s).") ";
            $SQLcari7 .= " and b.jenispegawai in (".implode(',', $s).") ";
            $SQLcari8 .= " and b.jenispegawai in (".implode(',', $s).") ";
            $SQLJadwal .= " and b.jenispegawai in (".implode(',', $s).") ";
        }

        if(!empty($stspeg)) {
            $s = array();
            foreach($stspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari  .= " and a.jftstatus in (".implode(',', $s).") ";
            $SQLcari2 .= " and b.jftstatus in (".implode(',', $s).") ";
            $SQLcari3 .= " and b.jftstatus in (".implode(',', $s).") ";
            $SQLcari4 .= " and b.jftstatus in (".implode(',', $s).") ";
            $SQLcari5 .= " and b.jftstatus in (".implode(',', $s).") ";
            $SQLcari6 .= " and b.jftstatus in (".implode(',', $s).") ";
            $SQLcari7 .= " and b.jftstatus in (".implode(',', $s).") ";
            $SQLcari8 .= " and b.jftstatus in (".implode(',', $s).") ";
            $SQLJadwal .= " and b.jftstatus in (".implode(',', $s).") ";
        }
        
        $querypanrb = $this->pegawai->get_panrb($SQLcaripanrb);
        $data['panrb'] = $querypanrb->result();
        
        //Get Jumlah Pegawai
        $jum_pegawai         = $this->pegawai->getJumlahPegawai($SQLcari);
        $data['jum_pegawai'] = $jum_pegawai->row()->total;

        //Get Jumlah Pegawai WFH
        $jum_pegawai_wfh         = $this->pegawai->getJumlahJabodetabek($SQLcari2);
        

        //Get Jumlah Pegawai Dinas
        $jum_pegawai_dinas       = $this->pegawai->getJumlahJabodetabek($SQLcari3);

        //Get Jumlah Pegawai Shift
        $jum_pegawai_shift       = $this->pegawai->getJumlahShift($SQLcari4);

        //Get Jumlah Pegawai Shift-1
        $jum_pegawai_shift1      = $this->pegawai->getJumlahShift($SQLcari5);

        //Get Jumlah Pegawai Shift-1
        $jum_pegawai_shift2      = $this->pegawai->getJumlahShift($SQLcari6);

        $query  = $this->pegawai->getJadwalkerja($SQLJadwal);
        $result = $query->result();
        
        $wfh = 0; 
        $shift_pegawai=0;
        $shift_pegawai1=0;
        $shift_pegawai2=0;
        $dinas = 0;
        foreach ($result as $l) {
            
            $cekStatus2 = " AND a.userid = '".$l->userid."' AND a.rosterdate between '".$mulai."' and '".$akhir."'  ";

            $c  = $this->pegawai->cekStatusWFH($cekStatus2);
            $q  = $c->result();

            $c2 = $this->pegawai->cekStatusDINAS($cekStatus2);
            $q2 = $c2->result();

            $c3  = $this->pegawai->cekStatusWFH($cekStatus2);
            $q3  = $c3->result();

            foreach ($q as $e) {
                if($e->attendance == 'WFH'){
                    $wfh += $e; 
                }
                 
            }

            foreach ($q2 as $e2) {
                $cekStatus3 = " AND a.userid = '".$e2->userid."' AND a.rosterdate between '".$mulai."' AND '".$akhir."'  ";
                if($e2->attendance == 'AB_13'){
                    $c3  = $this->pegawai->cekStatusWFH2($cekStatus3);
                    $q3  = $c3->num_rows();
                    if($q3 == 0){
                        $dinas = $dinas;
                        $dinas++;
                    }
                }
            }

            $c4  = $this->pegawai->cekStatusWFH3($cekStatus2);
            $q4  = $c4->num_rows();
            if($q4 == 0){
                $shift_pegawai = $shift_pegawai;
                $shift_pegawai++;
            }
            
            $c5  = $this->pegawai->cekStatusWFH4($cekStatus2);
            $q5  = $c5->result();
            foreach ($q5 as $e5) {
                $cekStatus4 = " AND a.userid = '".$e5->userid."' AND a.rosterdate between '".$mulai."' AND '".$akhir."'  ";
                if($e5->absence == 'CVD1'){
                    $c6  = $this->pegawai->cekStatusWFH5($cekStatus4);
                    $q6  = $c6->num_rows(); 
                    if($q6 == 0){
                        $shift_pegawai1 = $shift_pegawai1;
                        $shift_pegawai1++;
                    }
                }

                if($e5->absence == 'CVD2'){
                    $c7  = $this->pegawai->cekStatusWFH5($cekStatus4);
                    $q7  = $c7->num_rows(); 
                    if($q7 == 0){
                        $shift_pegawai2 = $shift_pegawai2;
                        $shift_pegawai2++;
                    }
                }
                 
            }
            
             
        }
        
        $data['jum_pegawai_wfh']    = $wfh;
        $data['jum_pegawai_dinas']  = $dinas; 
        $data['jum_pegawai_shift']  = $shift_pegawai1+$shift_pegawai2;
        $data['jum_pegawai_shift1'] = $shift_pegawai1;
        $data['jum_pegawai_shift2'] = $shift_pegawai2;
        $totalsel = $jum_pegawai->row()->total;
        $totalkel = $wfh+$dinas+$shift_pegawai1+$shift_pegawai2;

        //Get Jumlah Pegawai Normanl
        $jum_pegawai_normal         = $this->pegawai->getJumlahShift($SQLcari7);
        $data['jum_pegawai_normal'] = $totalsel-$totalkel;

        //Get Jumlah Pegawai Cuti
        $jum_pegawai_cuti         = $this->pegawai->getJumlahJabodetabek($SQLcari8);
        $data['jum_pegawai_cuti'] = $jum_pegawai_cuti->num_rows();

       
    	
        $data['mulai'] = date_format(date_create($mulai),"d-m-Y");
        $data['akhir'] = date_format(date_create($akhir),"d-m-Y");
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

    public function simpan_new(){
        
        $cr = $this->input->post('cari');
        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $ket = $this->input->post('ket');
        $jnspeg = $this->input->post('jnspeg');

        $mulai = dmyToymd($this->input->post('start'));
        $akhir = dmyToymd($this->input->post('end'));

        $mulai=((isset($mulai) && ($mulai!='' || $mulai!=null))?$mulai:date("Y-m-d"));
        $akhir=((isset($akhir) && ($akhir!='' || $akhir!=null))?$akhir:date("Y-m-d"));
        $departemen = $this->input->post('org')!='undefined'?$this->input->post('org'):"1";

        $ket1   = $this->input->post('ket1');
        $ket2   = $this->input->post('ket2');
        $ket3   = $this->input->post('ket3');
        $ket4   = $this->input->post('ket4');
        $ket5   = $this->input->post('ket5');
        $ket6   = $this->input->post('ket6');
        $ket7   = $this->input->post('ket7');
        $ket8   = $this->input->post('ket8');
        $ket9   = $this->input->post('ket9');
        $ket10  = $this->input->post('ket10');

        $cekpanrb = getCount("riw_panrb","unitkerja = '".$departemen."' and tanggal_awal = '".$mulai."' and tanggal_akhir = '".$akhir."' ",1);
        if ($cekpanrb == 0){
            $data2['ket1'] = $ket1;
            $data2['ket2'] = $ket2;
            $data2['ket3'] = $ket3;
            $data2['ket4'] = $ket4;
            $data2['ket5'] = $ket5;
            $data2['ket6'] = $ket6;
            $data2['ket7'] = $ket7;
            $data2['ket8'] = $ket8;
            $data2['ket9'] = $ket9;
            $data2['ket10'] = $ket10;
            $data2['tanggal_awal']  = $mulai;
            $data2['tanggal_akhir'] = $akhir;
            $data2['unitkerja']   = $departemen;
            $data2['create_by']   = $this->session->userdata('s_username');
            $data2['create_date'] = date('Y-m-d H:i:s');
            $xss_data2            = $this->security->xss_clean($data2);
            $this->db->insert('riw_panrb', $xss_data2);
            $this->db->insert('riw_panrb_log', $xss_data2);
            $data['status']         = 'sukses';
            $data['msg']            = 'Data berhasil disimpan!';
        }else{
            $data2['ket1'] = $ket1;
            $data2['ket2'] = $ket2;
            $data2['ket3'] = $ket3;
            $data2['ket4'] = $ket4;
            $data2['ket5'] = $ket5;
            $data2['ket6'] = $ket6;
            $data2['ket7'] = $ket7;
            $data2['ket8'] = $ket8;
            $data2['ket9'] = $ket9;
            $data2['ket10'] = $ket10;
            $data2['tanggal_awal']  = $mulai;
            $data2['tanggal_akhir'] = $akhir;
            $data2['unitkerja']   = $departemen;
            $data2['create_by']   = $this->session->userdata('s_username');
            $data2['create_date'] = date('Y-m-d H:i:s');
            $xss_data2            = $this->security->xss_clean($data2);
            $this->db->where('unitkerja',$departemen);
            $this->db->where('tanggal_awal',$mulai);
            $this->db->where('tanggal_akhir',$akhir);
            $this->db->update('riw_panrb',$xss_data2);
            $this->db->insert('riw_panrb_log', $xss_data2);
            $data['status']         = 'sukses';
            $data['msg']            = 'Data berhasil disimpan!';
        }
        echo json_encode($data);
    }

    public function view()
    {

        $id    = $this->input->post('idcetak');
        $priv  = $this->pegawai->getprivilege();
        $privi = array();
        foreach($priv->result() as $privilege)
        {
            $privi[$privilege->id] = $privilege->privilege;
        }
        $data['priv'] = $privi;

        $cr = $this->input->post('cari');
        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $ket = $this->input->post('ket');
        $jnspeg = $this->input->post('jnspeg');

        $mulai = dmyToymd($this->input->post('start'));
        $akhir = dmyToymd($this->input->post('end'));

        $SQLcari  = "";
        $SQLJadwal  = "";
        $SQLcari2 = " AND a.attendance = 'WFH' ";
        $SQLcari3 = " AND a.attendance = 'AB_13' ";
        $SQLcari4 = " AND a.absence IN('CVD1','CVD2') ";
        $SQLcari5 = " AND a.absence = 'CVD1' ";
        $SQLcari6 = " AND a.absence = 'CVD2' ";
        $SQLcari7 = " AND a.absence = 'S-K' ";
        $SQLcari8 = " AND a.attendance IN ('AB_3','AB_4','AB_5','AB_6','AB_7','AB_8','AB_14','AB_20') ";
        
        $mulai=((isset($mulai) && ($mulai!='' || $mulai!=null))?$mulai:date("Y-m-d"));
        $akhir=((isset($akhir) && ($akhir!='' || $akhir!=null))?$akhir:date("Y-m-d"));
        
        $SQLcari2 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLcari3 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLcari4 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLcari5 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLcari6 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLcari7 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLcari8 .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";
        $SQLJadwal .=" and (date(a.rosterdate) between '".$mulai."' and '".$akhir."' )";

        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari  .= " AND ( a.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari2 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari3 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari4 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari5 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari6 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari7 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLcari8 .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
            $SQLJadwal .= " AND ( b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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

            $SQLcari  .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari2 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari3 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari4 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari5 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari6 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari7 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLcari8 .= " and b.deptid in (".implode(',', $s).") ";
            $SQLJadwal .= " and b.deptid in (".implode(',', $s).") ";
        }
 
        $SQLcari  .= " and a.jenispegawai in (".$jnspeg.") ";
        $SQLcari2 .= " and b.jenispegawai in (".$jnspeg.") ";
        $SQLcari3 .= " and b.jenispegawai in (".$jnspeg.") ";
        $SQLcari4 .= " and b.jenispegawai in (".$jnspeg.") ";
        $SQLcari5 .= " and b.jenispegawai in (".$jnspeg.") ";
        $SQLcari6 .= " and b.jenispegawai in (".$jnspeg.") ";
        $SQLcari7 .= " and b.jenispegawai in (".$jnspeg.") ";
        $SQLcari8 .= " and b.jenispegawai in (".$jnspeg.") ";
        $SQLJadwal .= " and b.jenispegawai in (".$jnspeg.") ";

        $SQLcari  .= " and a.jftstatus in (".$stspeg.") ";
        $SQLcari2 .= " and b.jftstatus in (".$stspeg.") ";
        $SQLcari3 .= " and b.jftstatus in (".$stspeg.") ";
        $SQLcari4 .= " and b.jftstatus in (".$stspeg.") ";
        $SQLcari5 .= " and b.jftstatus in (".$stspeg.") ";
        $SQLcari6 .= " and b.jftstatus in (".$stspeg.") ";
        $SQLcari7 .= " and b.jftstatus in (".$stspeg.") ";
        $SQLcari8 .= " and b.jftstatus in (".$stspeg.") ";
        $SQLJadwal .= " and b.jftstatus in (".$stspeg.") ";
    

        //Get Jumlah Pegawai
        $jum_pegawai         = $this->pegawai->getJumlahPegawai($SQLcari);
        $data['jum_pegawai'] = $jum_pegawai->row()->total;

        //Get Jumlah Pegawai WFH
        $jum_pegawai_wfh         = $this->pegawai->getJumlahJabodetabek($SQLcari2);
        

        //Get Jumlah Pegawai Dinas
        $jum_pegawai_dinas       = $this->pegawai->getJumlahJabodetabek($SQLcari3);

        //Get Jumlah Pegawai Shift
        $jum_pegawai_shift       = $this->pegawai->getJumlahShift($SQLcari4);

        //Get Jumlah Pegawai Shift-1
        $jum_pegawai_shift1      = $this->pegawai->getJumlahShift($SQLcari5);

        //Get Jumlah Pegawai Shift-1
        $jum_pegawai_shift2      = $this->pegawai->getJumlahShift($SQLcari6);

        $query  = $this->pegawai->getJadwalkerja($SQLJadwal);
        $result = $query->result();
        $wfh = 0; 
        $shift_pegawai=0;
        $shift_pegawai1=0;
        $shift_pegawai2=0;
        $dinas = 0;
        foreach ($result as $l) {
            
            $cekStatus2 = " AND a.userid = '".$l->userid."' AND a.rosterdate between '".$mulai."' and '".$akhir."'  ";
            

            $c  = $this->pegawai->cekStatusWFH($cekStatus2);
            $q  = $c->result();

            $c2 = $this->pegawai->cekStatusDINAS($cekStatus2);
            $q2 = $c2->result();

            $c3  = $this->pegawai->cekStatusWFH($cekStatus2);
            $q3  = $c3->result();

            foreach ($q as $e) {
                if($e->attendance == 'WFH'){
                    $wfh += $e; 
                }
                 
            }

            foreach ($q2 as $e2) {
                $cekStatus3 = " AND a.userid = '".$e2->userid."' AND a.rosterdate between '".$mulai."' AND '".$akhir."'  ";
                if($e2->attendance == 'AB_13'){
                    $c3  = $this->pegawai->cekStatusWFH2($cekStatus3);
                    $q3  = $c3->num_rows();
                    if($q3 == 0){
                        $dinas = $dinas;
                        $dinas++;
                    }
                }
            }

            $c4  = $this->pegawai->cekStatusWFH3($cekStatus2);
            $q4  = $c4->num_rows();
            if($q4 == 0){
                $shift_pegawai = $shift_pegawai;
                $shift_pegawai++;
            }
            
            $c5  = $this->pegawai->cekStatusWFH4($cekStatus2);
            $q5  = $c5->result();
            foreach ($q5 as $e5) {
                $cekStatus4 = " AND a.userid = '".$e5->userid."' AND a.rosterdate between '".$mulai."' AND '".$akhir."'  ";
                if($e5->absence == 'CVD1'){
                    $c6  = $this->pegawai->cekStatusWFH5($cekStatus4);
                    $q6  = $c6->num_rows(); 
                    if($q6 == 0){
                        $shift_pegawai1 = $shift_pegawai1;
                        $shift_pegawai1++;
                    }
                }

                if($e5->absence == 'CVD2'){
                    $c7  = $this->pegawai->cekStatusWFH5($cekStatus4);
                    $q7  = $c7->num_rows(); 
                    if($q7 == 0){
                        $shift_pegawai2 = $shift_pegawai2;
                        $shift_pegawai2++;
                    }
                }
                 
            }
            
             
        }
        
        $data['jum_pegawai_wfh']    = $wfh;
        $data['jum_pegawai_dinas']  = $dinas; 
        $data['jum_pegawai_shift']  = $shift_pegawai1+$shift_pegawai2;
        $data['jum_pegawai_shift1'] = $shift_pegawai1;
        $data['jum_pegawai_shift2'] = $shift_pegawai2;

        $totalsel = $jum_pegawai->row()->total;
        $totalkel = $wfh+$dinas+$shift_pegawai1+$shift_pegawai2;

        //Get Jumlah Pegawai Normanl
        $jum_pegawai_normal         = $this->pegawai->getJumlahShift($SQLcari7);
        $data['jum_pegawai_normal'] = $totalsel-$totalkel;

        $data['mulai'] = date_format(date_create($mulai),"d-m-Y");
        $data['akhir'] = date_format(date_create($akhir),"d-m-Y");
        $data['tanggalsekarang'] = date('Y-m-d');
        $compa = $this->report_model->getcompany();
        $company = array('companyname' => isset($compa->row()->companyname) ? $compa->row()->companyname : '',
            'logo' => isset($compa->row()->logo) ? $compa->row()->logo : '',
            'address1' => isset($compa->row()->address1) ? $compa->row()->address1 : '',
            'address2' => isset($compa->row()->address2) ? $compa->row()->address2 : '',
            'phone' => isset($compa->row()->phone) ? $compa->row()->phone : '',
            'fax' => isset($compa->row()->fax) ? $compa->row()->fax : '');
        $data['comp'] = $company;

        $departemen = $this->input->post('org')!='undefined'?$this->input->post('org'):"1";
        $mastertunj = array();

        $namaunit = getValue('deptname','departments','deptid = "'.$departemen.'"');
        if($departemen == '03000000' OR $departemen == '04000000' OR $departemen == '05000000' OR $departemen == '06000000' OR $departemen == '07000000' OR $departemen == '08000000' OR $departemen == '09000000'){
            $dep = substr($departemen,0,2);
            $lep = $dep.'010000';

            $kepala  = getValue('title','userinfo','deptid = "'.$lep.'"  AND jftstatus IN ("1","2") AND jenisjabatan IN ("1") AND eselon IN ("II.A","II.B") ');
            $namattd = getValue('name','userinfo','deptid = "'.$lep.'"  AND jftstatus IN ("1","2") AND jenisjabatan IN ("1") AND eselon IN ("II.A","II.B") ');
            $nipttd  = getValue('userid','userinfo','deptid = "'.$lep.'" AND jftstatus IN ("1","2") AND jenisjabatan IN ("1") AND eselon IN ("II.A","II.B") ');

        }else{
            $kepala  = getValue('title','userinfo','deptid = "'.$departemen.'"  AND jftstatus IN ("1","2") AND jenisjabatan IN ("1") ');
            $namattd = getValue('name','userinfo','deptid = "'.$departemen.'"  AND jftstatus IN ("1","2") AND jenisjabatan IN ("1")');
            $nipttd  = getValue('userid','userinfo','deptid = "'.$departemen.'" AND jftstatus IN ("1","2") AND jenisjabatan IN ("1") ');
        }
        
       
        $namaunitkerja = $namaunit;
        $data['namaunitkerja'] = $namaunitkerja;
        $data['namattd'] = $namattd;
        $data['nipttd'] = $nipttd;
        $data['kepala'] = $kepala;
        $data['keterangan'] = $ket;

        
        if($id == 1){
            $this->load->library('word');
            $document = $this->word->loadTemplate("assets/template/surat_template.docx");
            $document->setValue('namaunitkerja',ucwords(strtolower($namaunitkerja)));
            $document->setValue('namattd',$namattd);
            $document->setValue('nipttd',$nipttd);
            $document->setValue('kepala',substr(ucwords(strtolower($kepala)),0,strpos($kepala,"PADA")));
            $document->setValue('keterangan',$ket);
            $document->setValue('jum_pegawai',$totalsel);
            $document->setValue('jum_pegawai_dinas',$dinas);
            $document->setValue('jum_pegawai_wfh',$wfh);
            $document->setValue('jum_pegawai_shift',$shift_pegawai1+$shift_pegawai2);
            $document->setValue('jum_pegawai_shift1',$shift_pegawai1);
            $document->setValue('jum_pegawai_shift2',$shift_pegawai2);
            $document->setValue('jum_pegawai_normal',$totalsel-$totalkel);
            $document->setValue('mulai',format_date_ind(date_format(date_create($mulai),"Y-m-d")));
            $document->setValue('akhir',format_date_ind(date_format(date_create($akhir),"Y-m-d")));
            $document->setValue('tanggalsekarang',format_date_ind(date('Y-m-d')));
            
            $nama_file = 'Laporan Pengendalian Jam Kerja ASN Pada '.ucwords(strtolower($namaunitkerja));
            $lokasi    = 'assets/cetakan/'.$nama_file.'.docx';
            $document->save($lokasi);
            $url = base_url().$lokasi;
            redirect($url);
                
        }else{
            $this->load->view('laporan',$data);
        }

        

        $savedata = array(
            'unitkerja' => $departemen, 
            'jumlah_pegawai' => $totalsel,
            'jumlah_dinas' => $dinas,
            'jumlah_wfh' => $wfh,
            'jumlah_shift' => $shift_pegawai1+$shift_pegawai2,
            'shift1' => $shift_pegawai1,
            'shift2' => $shift_pegawai2, 
            'jumlah_wfo' => $totalsel-$totalkel, 
            'tanggal_mulai' => $mulai, 
            'tanggal_akhir' => $akhir, 
            'keterangan' => $ket,
            'createdate' => date('Y-m-d H:i:s'), 
            'nipttd' => $nipttd,
            'createuser' => $this->session->userdata('s_username')
        );
        $this->db->insert('log_transaksi_laporan_jabodetabek', $savedata);
        
        
    }

    function form()
    {
        $data['data'] = '';
        $this->load->view('form',$data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */