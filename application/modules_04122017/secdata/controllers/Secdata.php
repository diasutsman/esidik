<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Secdata extends MX_Controller {

	function Secdata(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('home/home_model','home');
        $this->load->model('pegawai/pegawai_model','pegawai');
        /*if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }*/

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
        $tglskrng = date("Y-m-d");//date("Y-m-d",strtotime("2017-08-08"));//date("Y-m-d");
        $data['menu'] = '0';
        $data['lsttransaksi'] = ymdTodmy($tglskrng);
        $data['unitkerjaUser'] = $this->pegawai->namadeptonall($this->session->userdata('s_dept'));

        //print_r($dptOrg);
        /*$this->load->driver('cache');
        $hasil = $this->cache->file->get('getDisplayData');
        if (!$hasil) {*/
        $hasil = $this->home->getDisplayData($tglskrng, $dptOrg);
        //$hasil = $this->home->getDisplayData(strtotime("2017-07-21"), $dptOrg);
        /* $this->cache->file->save('getDisplayData', $hasil, 400); //per 5 menit
     }*/

        //print_r($this->db->last_query());

        $data['lstprocess'] = ymdTodmy($tglskrng); //date("d-m-Y",$setDate);//

        $data['jmlTepatWaktu'] = $hasil[0]["jml"];//$this->home->getTptwaktu($lstprocess,$dptOrg);
        $data['jmlTerlambat'] = $hasil[1]["jml"];//$this->home->getTerlambat($lstprocess,$dptOrg);
        $data['jmlIjin'] = $hasil[2]["jml"];//$this->home->getIjin($lstprocess,$dptOrg);
        $data['jmlAlpha'] = $hasil[3]["jml"];//$this->home->getAlpha($lstprocess,$dptOrg);
        $data['jmlSakit'] = $hasil[4]["jml"];//$this->home->getSakit($lstprocess,$dptOrg);
        $data['jmlCuti'] = $hasil[5]["jml"];//$this->home->getCuti($lstprocess,$dptOrg);
        $data['jmlTb'] = $hasil[6]["jml"];
        $data['jmlDinas'] = $hasil[7]["jml"];
        $data['jmlPegawai'] = $hasil[8]["jml"];

        $data['thn'] = date("Y",strtotime($tglskrng));

        //$data['lsthariini'] = $this->home->getLastAbsensi($lsttransaksi,5,$dptOrg);
        $data['lsthariini'] = $this->home->getLastAbsensi($tglskrng,5,$dptOrg)->result_array();
        /*$hasilGrp = $this->cache->file->get('hasilGrp');
        if (!$hasilGrp) {
            $hasilGrp = $this->home->getDisplayGrap($data['thn'], $dptOrg);
            $this->cache->file->save('hasilGrp', $hasilGrp, 3600);
        }
        //print_r($this->db->last_query());
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
        $data['lstCuti'] = $strCuti; //$this->home->getCutiByYear($data['thn'],$dptOrg);*/


        //print_r($hasilGrp);


		$this->template->load('templateadmin','home',$data);
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
