<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Rptmaps extends MX_Controller {

    private $aAkses;
	function Rptmaps(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('maps_model','maps');
        $this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('setdev/device_model','setdev');
        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Rptmaps", $this->session->userdata('s_access'));
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
		$this->session->set_userdata('menu','2');
		$data['menu'] = '2';
	    $dataarea= $this->pegawai->areaonall('010000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        $jmlAlloff=0;$jmlAllon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;

            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"010000\\\")' >Daftar Mesin</a>";
	    $data["pusat"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;
        $dataarea= $this->pegawai->areaonall('020000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"020000\\\")' >Daftar Mesin</a>";
        $data["bandung"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul ;

        $dataarea= $this->pegawai->areaonall('030000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"030000\\\")'>Daftar Mesin</a>";
        $data["jatinangor"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('040000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"040000\\\")'>Daftar Mesin</a>";
        $data["Yogyakarta"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('050000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"050000\\\")'>Daftar Mesin</a>";
        $data["Malang"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('060000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"060000\\\")'>Daftar Mesin</a>";
        $data["sumbar"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('070000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"070000\\\")'>Daftar Mesin</a>";
        $data["riau"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('080000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"080000\\\")'>Daftar Mesin</a>";
        $data["lampung"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('090000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"090000\\\")'>Daftar Mesin</a>";
        $data["kalbar"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('110000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"110000\\\")'>Daftar Mesin</a>";
        $data["sulsel"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('100000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"100000\\\")'>Daftar Mesin</a>";
        $data["sulut"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('120000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"120000\\\")'>Daftar Mesin</a>";
        $data["ntb"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $dataarea= $this->pegawai->areaonall('130000');
        $dataMesin = $this->maps->getDisplayData($dataarea);
        $jml=0;$jmloff=0;$jmlon=0;
        foreach ($dataMesin as $row)
        {
            if ($row['status']==1) {
                $jmlon++;
            } else
            {
                $jmloff++;
            }
            $jml++;
            $jmlAlloff +=$jmloff;
            $jmlAllon +=$jmlon;
        }
        $arul = "<br><a href='#' onclick='bukaDaftar(\\\"130000\\\")'>Daftar Mesin</a>";
        $data["papua"]="<strong>Jumlah Mesin : ".$jml."<br><span style='color:white;background:green'>ONLINE</span> : ".$jmlon."<br><span style='color:white;background:red'>OFFLINE</span> : ".$jmloff."</strong>".$arul;

        $data["jmlAlloff"] = $jmlAlloff;
        $data["jmlAllon"] = $jmlAllon;
		$this->template->load('template','view',$data);
	}



    function showmesin($idarea=0)
    {
        $dataarea= $this->pegawai->areaonall($idarea);

        $SQLcari = " AND b.areaid in ('".implode("','", $dataarea)."')";
        $SQLcari .=" Order by status";
        $data["result"]=$this->setdev->getDaftar(0,null,null,null,$SQLcari)->result();
        $this->load->view('list',$data);
    }

}
