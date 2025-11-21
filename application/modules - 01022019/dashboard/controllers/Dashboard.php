<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Dashboard extends MX_Controller {

	function Dashboard(){
		parent::__construct();
		//$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		// $this->load->library('mypagination' );


		$this->load->model('home/home_model','home');
        $this->load->model('pegawai/pegawai_model','pegawai');
        // if( !$this->auth->is_logged_in() ){
            // redirect('login', 'refresh');
        // }

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
	    if ($this->session->userdata('unitkerjadash')==null) {
            $data1 = array('unitkerjadash' => 1);
            $this->session->set_userdata($data1);
        }

		if (isset($_POST['unit_search'])) {
			$data1 = array('unitkerjadash' => $_POST['unit_search']);
			$this->session->set_userdata($data1);
		}

	    $unitKerjaUser= $this->session->userdata('unitkerjadash');
        $dptOrg=$this->pegawai->deptonall($unitKerjaUser);
		$this->session->set_userdata('menu','0');

        $tglskrng = date("Y-m-d");
		$data['menu'] = '0';
        $data['lsttransaksi'] = ymdTodmy($tglskrng);

        $data['unitkerjaUser'] = $this->pegawai->namadeptonall($unitKerjaUser);
        $data['idunitkerja'] = $unitKerjaUser;
        $data['nmunitkerja'] = $this->pegawai->getdeptname($unitKerjaUser);

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
            /*if (ENVIRONMENT==="development") {
                $data['lstprocess'] = ymdTodmy($tglskrng);
                $nMaxData=5000;
                $nData = rand(0,$nMaxData);
                $data['jmlTepatWaktu'] = $nData;

                $nKrg=$nMaxData-$nData;
                $nData = rand(0,$nKrg);
                $data['jmlTerlambat'] = $nData;

                $nKrg1=$nMaxData-($nData+$nKrg);
                $nData = rand(0,$nKrg1);
                $data['jmlIjin'] = $nData;

                $nKrg2=$nMaxData-($nData+$nKrg+$nKrg1);
                $nData = rand(0,$nKrg2);
                $data['jmlAlpha'] = $nData;

                $nKrg3=$nMaxData-($nData+$nKrg+$nKrg1+$nKrg2);
                $nData = rand(0,$nKrg3);
                $data['jmlSakit'] = $nData;

                $nKrg4=$nMaxData-($nData+$nKrg+$nKrg1+$nKrg2+$nKrg3);
                $nData = rand(0,$nKrg4);
                $data['jmlCuti'] = $nData;

                $nKrg5=$nMaxData-($nData+$nKrg+$nKrg1+$nKrg2+$nKrg3+$nKrg4);
                $nData = rand(0,$nKrg5);
                $data['jmlTb'] = rand(1,5000);

                $nKrg6=$nMaxData-($nData+$nKrg+$nKrg1+$nKrg2+$nKrg3+$nKrg4+$nKrg5);
                $nData = rand(0,$nKrg6);
                $data['jmlDinas'] = $nData;

                $nKrg7=$nMaxData-($nData+$nKrg+$nKrg1+$nKrg2+$nKrg3+$nKrg4+$nKrg5+$nKrg6);
                $nData = rand(0,$nKrg7);
                $data['jmlLain'] = $nData;


                $data['jmlPegawai'] = $nMaxData;
            } else */{
                $this->load->driver('cache');
                $hasil = $this->cache->file->get('getDisplayData');
                if (!$hasil) {
                    $hasil = $this->home->getDisplayData_New($tglskrng, $dptOrg);
                    //$hasil = $this->home->getDisplayData(strtotime("2017-07-21"), $dptOrg);
                    $this->cache->file->save('getDisplayData', $hasil, 300); //per 5 menit
                }

                $data['lstprocess'] = ymdTodmy($tglskrng); //date("d-m-Y",$setDate);//

                //$hasil = $this->home->getDisplayData_New($tglskrng, $dptOrg);
                $data['jmlTepatWaktu'] = $hasil[0]["jml"];
                $data['jmlTerlambat'] = $hasil[1]["jml"];
                /*$data['jmlIjin'] = $hasil[2]["jml"];*/
                $data['jmlAlpha'] = $hasil[2]["jml"];
                $data['jmlSakit'] = $hasil[3]["jml"];
                $data['jmlCuti'] = $hasil[4]["jml"];
                $data['jmlTb'] = $hasil[5]["jml"];
                $data['jmlDinas'] = $hasil[6]["jml"];

                $jmlhDas = $hasil[0]["jml"] + $hasil[1]["jml"] + $hasil[2]["jml"] + $hasil[3]["jml"] + $hasil[4]["jml"] + $hasil[5]["jml"] + $hasil[6]["jml"] ;
                $data['jmlLain'] = $hasil[8]["jml"];
                $data['jmlPegawai'] = $hasil[7]["jml"];;
            }
		}

		$data['thn'] = date("Y",strtotime($tglskrng));
		$data['lsthariini'] = $this->home->getLastAbsensi(date("Y-m-d"),30,$dptOrg)->result_array();
		$this->template->load('template_dashboard','dashboard',$data);
	}

    function listabsen()
    {
        $dptOrg=$this->pegawai->deptonall($this->session->userdata('unitkerjadash'));

        $js ="<script>
            var multilines = $('#data-presensi').newsTicker({
                row_height: 127,
                speed: 800,
                duration:30000,
                pauseOnHover: 0
            });
        </script>";

        $resoponse = '<ul class="list-group" id="data-presensi">';
        $lsthariini = $this->home->getLastAbsensi( date('Y-m-d'),30,$dptOrg)->result_array();
        foreach ($lsthariini as $row)
        {
            $resoponse .= '<li class="list-group-item">
                <p><span class="text-info">'.$row["name"].'</span> | <small><span class="text-left">
                '.$row["deptname"].'</span></small></p>
                <small class="block text-muted"><i class="fa fa-clock-o"></i> '.ymdToIna($row["checktime"])." ".$row["waktu"].'</small>
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

    function getUnitKerja()
    {
        $this->load->view('unkerja');
    }

    function child_unkerja($param=1,$lvl=1)
    {
        $data['param'] = $param;
        $data['lvl'] = $lvl;
        $this->load->view('next_unkerja',$data);
    }


}
