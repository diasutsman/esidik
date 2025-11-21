<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MX_Controller {

	function Home(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('home_model','home');
        $this->load->model('pegawai/pegawai_model','pegawai');
        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }

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
        $dptOrg=$this->pegawai->deptonall($this->session->userdata('s_dept'));
		$this->session->set_userdata('menu','0');
		$data['menu'] = '0';
        $lsttransaksi = $this->home->getLastDayTrans();
        $data['lsttransaksi'] = ymdTodmy($lsttransaksi);

        $lstprocess = $this->home->getLastDayProcess();
        $data['lstprocess'] = ymdTodmy($lstprocess);

        $data['jmlTepatWaktu'] = $this->home->getTptwaktu($lstprocess,$dptOrg);
        $data['jmlTerlambat'] = $this->home->getTerlambat($lstprocess,$dptOrg);
        $data['jmlIjin'] = $this->home->getIjin($lstprocess,$dptOrg);
        $data['jmlAlpha'] = $this->home->getAlpha($lstprocess,$dptOrg);
        $data['jmlSakit'] = $this->home->getSakit($lstprocess,$dptOrg);
        $data['jmlCuti'] = $this->home->getCuti($lstprocess,$dptOrg);
        $data['thn'] = date("Y",strtotime($lstprocess));

        $data['lsthariini'] = $this->home->getLastAbsensi($lsttransaksi,5,$dptOrg);
        $data['lstTepatWaktu'] = $this->home->getTptwaktuByYear($data['thn'],$dptOrg);
        $data['lstTerlambat'] = $this->home->getTerlambatByYear($data['thn'],$dptOrg);
        $data['lstIjin'] = $this->home->getIjinByYear($data['thn'],$dptOrg);
        $data['lstlAlpha'] = $this->home->getAlphaByYear($data['thn'],$dptOrg);
        $data['lstSakit'] = $this->home->getSakitByYear($data['thn'],$dptOrg);
        $data['lstCuti'] = $this->home->getCutiByYear($data['thn'],$dptOrg);

		$this->template->load('template','home',$data);
	}

	function kuncipage()
    {

        $this->load->view('kuncipage');
    }

    function tryunlock()
    {
        $this->load->view('kuncipage');
    }
	

}
