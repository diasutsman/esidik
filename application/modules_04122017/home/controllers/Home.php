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
        $tglskrng = date("Y-m-d");
		$data['menu'] = '0';
        $data['lsttransaksi'] = ymdTodmy($tglskrng);
		
        $data['unitkerjaUser'] = $this->pegawai->namadeptonall($this->session->userdata('s_dept'));
		$namafileJson =FCPATH."assets/dashboard/data_".date("Y-m-d").".json";
        $adaJson = file_exists($namafileJson);
        if ($adaJson)
        {
            $str = file_get_contents($namafileJson);
            $json = json_decode($str, true);
            $tglskrng=$json['lastget'];

            $jmlTepatWaktu=0;$jmlTerlambat=0;$jmlIjin=0;
            $jmlAlpha=0; $jmlSakit=0; $jmlCuti=0;
            $jmlTb=0; $jmlDinas=0; $jmlPegawai=0; $jmlLain=0;
            foreach ($json['data'] as $datajson)
            {
                if(isset($datajson['ket']) && $datajson['ket'] == 'TEPATWAKTU') {
                    $found_key = array_search($datajson['deptid'], $dptOrg);
                    if ($found_key)
                        $jmlTepatWaktu += $datajson['jml'];
                }
                if(isset($datajson['ket']) && $datajson['ket'] == 'TERLAMBAT') {
                    $found_key = array_search($datajson['deptid'], $dptOrg);
                    if ($found_key)
                    $jmlTerlambat += $datajson['jml'];
                }
                if(isset($datajson['ket']) && $datajson['ket'] == 'IJIN') {
                    $found_key = array_search($datajson['deptid'], $dptOrg);
                    if ($found_key)
                    $jmlIjin += $datajson['jml'];
                }
                if(isset($datajson['ket']) && $datajson['ket'] == 'ALPHA') {
                    $found_key = array_search($datajson['deptid'], $dptOrg);
                    if ($found_key)
                    $jmlAlpha += $datajson['jml'];
                }
                if(isset($datajson['ket']) && $datajson['ket'] == 'SAKIT') {
                    $found_key = array_search($datajson['deptid'], $dptOrg);
                    if ($found_key)
                    $jmlSakit += $datajson['jml'];
                }
                if(isset($datajson['ket']) && $datajson['ket'] == 'CUTI') {
                    $found_key = array_search($datajson['deptid'], $dptOrg);
                    if ($found_key)
                    $jmlCuti += $datajson['jml'];
                }
                if(isset($datajson['ket']) && $datajson['ket'] == 'TB') {
                    $found_key = array_search($datajson['deptid'], $dptOrg);
                    if ($found_key)
                    $jmlTb += $datajson['jml'];
                }
                if(isset($datajson['ket']) && $datajson['ket'] == 'DINAS') {
                    $found_key = array_search($datajson['deptid'], $dptOrg);
                    if ($found_key)
                    $jmlDinas += $datajson['jml'];
                }
                if(isset($datajson['ket']) && $datajson['ket'] == 'KHS') {
                    $found_key = array_search($datajson['deptid'], $dptOrg);
                    if ($found_key)
                    $jmlLain += $datajson['jml'];
                }

            };

            $data['jmlTepatWaktu'] = $jmlTepatWaktu;
            $data['jmlTerlambat'] = $jmlTerlambat;
            $data['jmlIjin'] = $jmlIjin;
            $data['jmlAlpha'] = $jmlAlpha;
            $data['jmlSakit'] = $jmlSakit;
            $data['jmlCuti'] = $jmlCuti;
            $data['jmlTb'] = $jmlTb;
            $data['jmlDinas'] = $jmlDinas;
            $data['jmlPegawai'] = $jmlPegawai;
            $data['jmlLain'] = $jmlLain;
            $data['lstprocess'] = indo_date_no_hari($tglskrng);
        }
        else {
			$this->load->driver('cache');
			$hasil = $this->cache->file->get('getDisplayData');
			if (!$hasil) {
				$hasil = $this->home->getDisplayData($tglskrng, $dptOrg);
				//$hasil = $this->home->getDisplayData(strtotime("2017-07-21"), $dptOrg);
			   $this->cache->file->save('getDisplayData', $hasil, 300); //per 5 menit
			}

			$data['lstprocess'] = ymdTodmy($tglskrng); //date("d-m-Y",$setDate);//

			$data['jmlTepatWaktu'] = $hasil[0]["jml"];
			$data['jmlTerlambat'] = $hasil[1]["jml"];
			$data['jmlIjin'] = $hasil[2]["jml"];
			$data['jmlAlpha'] = $hasil[3]["jml"];
			$data['jmlSakit'] = $hasil[4]["jml"];
			$data['jmlCuti'] = $hasil[5]["jml"];
			$data['jmlTb'] = $hasil[6]["jml"];
			$data['jmlDinas'] = $hasil[7]["jml"];
			$jmlhDas=$hasil[0]["jml"]+$hasil[1]["jml"]+$hasil[2]["jml"]+$hasil[3]["jml"]+$hasil[4]["jml"]+$hasil[5]["jml"]+$hasil[6]["jml"]+$hasil[7]["jml"];
			$data['jmlLain'] = $hasil[9]["jml"];
			$data['jmlPegawai'] = $hasil[8]["jml"];
		}
		$data['thn'] = date("Y",strtotime($tglskrng));
		$data['lsthariini'] = $this->home->getLastAbsensi(date("Y-m-d"),5,$dptOrg)->result_array();
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

    function listabsen()
    {
        $dptOrg=$this->pegawai->deptonall($this->session->userdata('s_dept'));

        $js ="<script>
            var multilines = $('#data-presensi').newsTicker({
                row_height: 85,
                speed: 800,
                duration:30000,
                pauseOnHover: 0
            });
        </script>";

        $resoponse = '<ul class="list-group" id="data-presensi">';
        $lsthariini = $this->home->getLastAbsensi(date('Y-m-d'),5,$dptOrg)->result_array();
        foreach ($lsthariini as $row)
        {
            $resoponse .= '<li class="list-group-item">
                <p><span class="text-info">'.$row["name"].'</span> | <small><span class="text-left">
                '.$row["deptname"].'</span></small></p>
                <small class="block text-muted"><i class="fa fa-clock-o"></i>'.ymdToIna($row["checktime"]).'</small>
            </li>';
        }
        if (count($lsthariini)==0)
        {
            $resoponse = '<li class="list-group-item">
                <p><span class="text-info">No-Data</span> | <small><span class="text-left"></span></small></p>
                <small class="block text-muted"><i class="fa fa-clock-o"></i></small>
            </li>';
        }
        $resoponse .= '</ul>';
        if (count($lsthariini)>0)
        {
            $resoponse .=" ".$js;
        }
        echo $resoponse;
        die();
    }

}
