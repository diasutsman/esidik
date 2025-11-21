<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Rptgrapik extends MX_Controller {
    private $aAkses;
	function Rptgrapik(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('grapik_model','home');
        $this->load->model('pegawai/pegawai_model','pegawai');
        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }

        $this->aAkses = akses("Rptgrapik", $this->session->userdata('s_access'));
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
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
        $dptOrg=$this->pegawai->deptonall($this->session->userdata('s_dept'));
		$this->session->set_userdata('menu','0');

		$data['menu'] = '0';
        $lsttransaksi = $this->home->getLastDayTrans();

        $data['lsttransaksi'] = ymdTodmy($lsttransaksi);
        $data['unitkerjaUser'] = $this->pegawai->namadeptonall($this->session->userdata('s_dept'));
        $lstprocess = $this->home->getLastDayProcess();

        /*$this->load->driver('cache');
        $hasil = $this->cache->file->get('getDisplayDataGrp');
        if (!$hasil) {*/
            //$hasil = $this->home->getDisplayData($lstprocess, $dptOrg);
            /*$this->cache->file->save('getDisplayDataGrp', $hasil, 3600);
        }*/
        //print_r($this->db->last_query());
        /*$time = strtotime(date("Y-m-d").' -1 day');
        $data['lstprocess'] = date("d-m-Y",$time);//ymdTodmy($lstprocess);

        $data['jmlTepatWaktu'] = $hasil[0]["jml"];//$this->home->getTptwaktu($lstprocess,$dptOrg);
        $data['jmlTerlambat'] = $hasil[1]["jml"];//$this->home->getTerlambat($lstprocess,$dptOrg);
        $data['jmlIjin'] = $hasil[2]["jml"];//$this->home->getIjin($lstprocess,$dptOrg);
        $data['jmlAlpha'] = $hasil[3]["jml"];//$this->home->getAlpha($lstprocess,$dptOrg);
        $data['jmlSakit'] = $hasil[4]["jml"];//$this->home->getSakit($lstprocess,$dptOrg);
        $data['jmlCuti'] = $hasil[5]["jml"];//$this->home->getCuti($lstprocess,$dptOrg);*/

        $data['thn'] = date("Y",strtotime($lstprocess));

        //$data['lsthariini'] = $this->home->getLastAbsensi($lsttransaksi,5,$dptOrg);
        $a = $this->home->getLastAbsensi(date('Y-m-d'),5,$dptOrg)->result_array();
        //uasort($a,'cmp');
        $data['lsthariini'] = $a;

        /*$hasilGrp = $this->cache->file->get('hasilGrp');
        if (!$hasilGrp) {*/
            $hasilGrp = $this->home->getDisplayGrap($data['thn'], $dptOrg);
            /*$this->cache->file->save('hasilGrp', $hasilGrp, 3600);
        }*/

        $arrTepatWaktu = array();$arrTerlambat = array();
        $arrIjin = array();$arrAlpha = array();
        $arrSakit = array();$arrCuti = array();
        foreach($hasilGrp as $row) {
            array_push($arrTepatWaktu,$row->TEPATWAKTU);
            array_push($arrTerlambat,$row->TERLAMBAT);
            array_push($arrIjin,$row->IJIN);
            array_push($arrAlpha,$row->ALPHA);
            array_push($arrSakit,$row->SAKIT);
            array_push($arrCuti,$row->CUTI);
        }

        $strTepatWaktu = implode(",",$arrTepatWaktu);
        $strTerlambat = implode(",",$arrTerlambat);
        $strIjin = implode(",",$arrIjin);
        $strAlpha = implode(",",$arrAlpha);
        $strSakit = implode(",",$arrSakit);
        $strCuti = implode(",",$arrCuti);

        $data['lstTepatWaktu'] = $strTepatWaktu; //$this->home->getTptwaktuByYear($data['thn'],$dptOrg);
        $data['lstTerlambat'] = $strTerlambat; //$this->home->getTerlambatByYear($data['thn'],$dptOrg);
        $data['lstIjin'] = $strIjin; //$this->home->getIjinByYear($data['thn'],$dptOrg);
        $data['lstlAlpha'] = $strAlpha; //$this->home->getAlphaByYear($data['thn'],$dptOrg);
        $data['lstSakit'] = $strSakit; //$this->home->getSakitByYear($data['thn'],$dptOrg);
        $data['lstCuti'] = $strCuti; //$this->home->getCutiByYear($data['thn'],$dptOrg);


		$this->template->load('template','view',$data);
	}





}
