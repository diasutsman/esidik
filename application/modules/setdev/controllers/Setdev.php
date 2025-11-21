<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Setdev extends MX_Controller {
    private $aAkses;
	function Setdev(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('device_model','setdev');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Setdev", $this->session->userdata('s_access'));
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
	    //re update status mesin
        /*$stat = array('status' => 0);
        $this->db->where("TIMESTAMPDIFF(SECOND,lastactivity,NOW()) > (delay+120) ",NULL,false);
        $this->db->where("status <>",2,false);
        $this->db->update('iclock', $stat);*/


        $data['aksesrule']=$this->aAkses;
		$this->session->set_userdata('menu','2');
		$data['menu'] = '2';
        $uri_segment=3;
        $offset = 0;
        $SQLcari = " ORDER BY a.id asc";
        $query = $this->setdev->getDaftar(1,10,$offset,null,$SQLcari);
        foreach($query->result() as $datad) {
            $last = strtotime($datad->lastactivity);
            $cur = strtotime(date("Y-m-d H:i:s"));
            $avg = $cur - $last;
            $stat = array('status' => 0);
            //if ($this->setdev->getstatus($datad->sn) != 2) {
                //if ($avg > ($datad->delay + 120)) { //2 menit
                if ($avg > ($datad->delay + 900)) { //15 menit
                    $this->db->update('iclock', $stat, array('sn' => $datad->sn));
                }
            //}
        }

        $query = $this->setdev->getDaftar(1,10,$offset,null,$SQLcari);

        $jum_data = $this->setdev->getDaftar(0,null,null,null,null);
        $this_url = site_url('setdev/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['resulttemp'] = $this->db->get("devtemp")->result();
        $data['order'] = 'terminal_id';
        $data['typeorder'] = 'sorting_asc';

		$this->template->load('template','display',$data);
	}

    public function pagging($page=0)
    {
        $data['aksesrule']=$this->aAkses;
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( a.sn LIKE '%".str_replace('%20',' ',$cr)."%' or 
                        b.areaname LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or a.alias LIKE '%".str_replace('%20',' ',$cr)."%' or a.ipaddress LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }
        $data['order'] = $this->input->post('order');
        $typeorder = $this->input->post('sorting');

        if($typeorder!='' && $typeorder!=null){
            if($typeorder=='sorting'){
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            }else if($typeorder=='sorting_asc'){
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            }else{
                $sorting = 'DESC';
                $data['typeorder'] = 'sorting_desc';
            }
        }else{
            $sorting = 'ASC';
            $data['typeorder'] = 'sorting_asc';
        }

        if($data['order']!='' && $data['order']!=null){
            $SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
        }else{
            $SQLcari .= " ORDER BY a.id asc ";
        }

        $query = $this->setdev->getDaftar(1,$limited,$offset,null,$SQLcari);

        foreach($query->result() as $datad) {
            $last = strtotime($datad->lastactivity);
            $cur = strtotime(date("Y-m-d H:i:s"));
            $avg = $cur - $last;
            $stat = array('status' => 0);
            if ($this->setdev->getstatus($datad->sn) != 2) {
                if ($avg > ($datad->delay + 120)) {
                    $this->db->update('iclock', $stat, array('sn' => $datad->sn));
                }
            }
        }

        $query = $this->setdev->getDaftar(1,$limited,$offset,null,$SQLcari);

        $jum_data = $this->setdev->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("setdev/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();

        $this->load->view('list',$data);
    }

    public function reboot()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'REBOOT',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            //$woowdb->insert('command', $arransemen);


            if (isset($datas)) {
                createLog("Reboot Mesin " . $datas["terminal_id"] . " " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah me-restart mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public  function rubahsn()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('idold');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();
            log_history("update","iclock",$datas);

            $snbaru = $this->input->post('txt1');

            $dataupdate = array('sn' => $snbaru);
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $dataupdate);

            //$woowdb->where('sn', $datas["sn"]);
            //$woowdb->update('iclock', $dataupdate);

            if (isset($datas)) {
                createLog("Merubah SN " . $datas["sn"].' ke '.$snbaru , "Sukses");
            }
        }

        $data['msg'] = 'SN Mesin sudah dirubah..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public  function rubaharea()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('idarea');
        $id = explode(',',$id);
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();
            log_history("update","iclock",$datas);

            $areabaru = $this->input->post('area');

            $dataupdate = array('areaid' => $areabaru);
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $dataupdate);

            //$woowdb->where('sn', $datas["sn"]);
            //$woowdb->update('iclock', $dataupdate);

            $comm = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'st'			=>1,
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $comm);

            //$woowdb->insert('command', $comm);

            if (isset($datas)) {
                createLog("Merubah Area " . $datas["areaid"].' ke '.$areabaru , "Sukses");
            }
        }

        $data['msg'] = 'Area Mesin sudah dirubah..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function synkronisasi()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');
        //$id = explode(',',$id);
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $comm = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'st'			=>1,
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $comm);

            //$woowdb->insert('command', $comm);

            if (isset($datas)) {
                createLog("Sinkronisasi Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah me-sinkronisasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }


    public function fpinfo()
    {
        //$woowdb = $this->load->database('woow', TRUE);

        $id = $this->input->post('id');
        //$id = explode(',',$id);
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $comm = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'INFO',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $comm);

            //$woowdb->insert('command', $comm);

            if (isset($datas)) {
                createLog("Informasi Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function transakinfo()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');
        //$id = explode(',',$id);
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $updat = array (
                'stamp'	  =>0
            );
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $updat);

            //$woowdb->where('sn', $datas["sn"]);
            //$woowdb->update('iclock', $updat);

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);
            //$woowdb->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("Informasi Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function transakuser()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');
        //$id = explode(',',$id);
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $updat = array (
                'opstamp'	  =>0
            );
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $updat);

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("User Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }


    public function transakfoto()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');
        //$id = explode(',',$id);
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $updat = array (
                'photostamp'	  =>0
            );
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $updat);

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("Foto Peawai Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }


    public function transakdelalllog()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CLEAR LOG',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("Hapus log Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah menghapus log data di mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function transakdeldatelog()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');
        //$id = explode(',',$id);
        $tanggal = strtotime(dmyToymd($this->input->post('deltanggal'))) + 86400;
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CLEAR ATTLOG BY TIME '.date('YmdHis', $tanggal),
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("Hapus transaksi Mesin " . $datas["sn"].' tgl '.$this->input->post('deltanggal'), "Sukses");
            }
        }

        $data['msg'] = 'Perintah menghapus log data di mesin  sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }


    public  function rubahunit()
    {
        $id = $this->input->post('idUnitKerja');
        $unitbaru = $this->input->post('unit_search');
        //$id = explode(',',$id);
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();
            log_history("edit","iclock",$datas);
            //print_r($datas);
            $dataupdate = array('iddept' => $unitbaru);
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $dataupdate);
            //echo $this->db->last_query();
            if (isset($datas)) {
                createLog("Merubah unit kerja SN " . $datas["iddept"].' ke '.$unitbaru , "Sukses");
            }
        }

        $data['msg'] = 'Unit kerja Mesin sudah dirubah..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function deltmp()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');
        //$id = explode(',',$id);
        if( strpos($id,',') !== false ) {
            $id = explode(",",$id);
        } else {
            $id = array($id);
        }

        for($i=0;$i<count($id);$i++){
            $nId = $id[$i];

            $this->db->where('sn', $nId);
            $query=$this->db->get("devtemp");
            $datas = $query->row_array();

            $this->db->where_in('sn', $nId);
            $this->db->delete('devtemp');

            //$woowdb->where_in('sn', $nId);
            //$woowdb->delete('devtemp');

            if (isset($datas)) {
                createLog("Delete Mesin Temp " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Mesin berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function simpantmp()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('idtmp');
        $delayerror = $this->input->post('delayerror');
        $delay = $this->input->post('delay');
        $timezone = $this->input->post('timezone');
        $reguler = $this->input->post('reguler');

        if( strpos($id, ',') !== false ) {
            $id = explode(",",$id);
        } else {
            $id = array($id);
        }

        $arTmp = $this->input->post('idareatmp');

        for($i=0;$i<count($id);$i++) {
            $nId = $id[$i];

            $this->db->where('sn', $nId);
            $reck = $this->db->get('devtemp');
            $rowme = $reck->row_array();

            $savedata = array (
                'sn'				=> $nId,
                'alias'				=> $nId,
                'areaid'			=> $arTmp,
                'errdelay'			=> $delayerror,
                'delay'				=> $delay,
                'timezone'			=> $timezone,
                'stamp'				=> '0',
                'opstamp'			=> '0',
                'photostamp'		=> '0',
                'transtimes'		=> '00:00;14:05',
                'transinterval' 	=> 1,
                'transflag'			=> '1111101000',
                'realtime'			=> 1,
                'encrypt'			=> 0,
                'lastactivity'			=> date('Y-m-d H:i:s'),
                'ipaddress'            => $rowme['ipaddress'],
                'status'			=> 1,
                'is_reguler'			=> $reguler,
            );
            $st = $this->db->insert('iclock', $savedata);

            createLog("Memasukan Mesin sementara ke ICLOCK SN " . $nId , "Sukses");

            $this->db->where_in('sn', $nId);
            $this->db->delete('devtemp');

        }

        $data['msg'] = 'Mesin berhasil didaftarkan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function uploadfile()
    {
        //$woowdb = $this->load->database('woow', TRUE);

        $idSN = $this->input->post('usrSN');
        $fileupload = $_FILES['file-0']['tmp_name'];
        $namafile = date('dmY_H_i_s') . '_'.$_FILES['file-0']['name'];
        $path = 'assets/usb/';
        $pathfile = $path.$namafile;

        $pos = strpos($idSN, ',');

        if ($pos === false) {
            $this->db->where('id', $idSN);
        } else {
            $aUserID = explode ( ',' , $idSN);
            $this->db->where_in('id', $aUserID);
        }
        $query=$this->db->get("iclock");
        $result = $query->result();

        if(move_uploaded_file($fileupload,$pathfile)) {

            $file = $pathfile;

            if (is_file($file) === true)
            {

                $file = fopen($file, 'r');
                $jmlIn=0;$jmlErr=0;$jmlTtl=0;
                while (feof($file) === false)
                {
                    $line = fgets($file);
                    $pos = strpos($line, "\t");

                    if ($pos === false) {

                    } else {
                        $data = explode("\t",$line);
                        $userid = trim($data[0]);

                        $this->db->select('id');
                        $this->db->from('checkinout');
                        $this->db->where('userid', $userid);
                        $this->db->where('checktime', $data[1]);
                        $query = $this->db->get();
                        if ($query->num_rows()>0) {
                            $jmlErr++;
                            createLog("Upload attlog pegawai UserID: " . $userid . ' ' . $data[1] . ' ' . $data[3] . ' ' . $data[4], "Error");
                        }
                        else {
                            foreach ($result as $row) {
                                $dataarray = array(
                                    'sn' => $row->sn,
                                    'userid' => $userid,
                                    'checktime' => $data[1],
                                    'checktype' => $data[3],
                                    'verifycode' => $data[4]
                                );
                                $this->db->insert('checkinout', $dataarray);

                                createLog("Upload attlog pegawai UserID: " . $userid . ' ' . $row->sn . ' ' . $data[1] . ' ' . $data[3] . ' ' . $data[4], "Sukses");
                            }
                            $jmlIn++;
                        }
                        $jmlTtl++;
                    }

                }
                fclose($file);
                createLog("Upload attlog ke ID Mesin " . $idSN, "Sukses");
            }
            $datax['msg'] = 'Data berhasil diunggah..<br>Jumlah Data yang Valid : '.$jmlIn.
                            '<br>Jumlah Data yang Tidak Valid : '.$jmlErr.
                            '<br>Total Jumlah Data : '.$jmlTtl;

        } else {

            $datax['msg'] = 'Data tidak berhasil diunggah.';
        }

        $datax['status'] = 'succes';

        echo json_encode($datax);
    }

    public function edit($noId=0)
    {
        $this->db->from("iclock");
        $this->db->where('id',$noId);
        echo json_encode($this->db->get()->row_array());
    }

    public function simpan()
    {
        //$woowdb = $this->load->database('woow', TRUE);

        $id = $this->input->post('iddata');
        $delayerror = $this->input->post('delayerror');
        $delay = $this->input->post('delay');
        $timezone = $this->input->post('timezone');
        $alias = $this->input->post('alias');
        $reguler = $this->input->post('reguler');

        $this->db->where('id', $id);
        $query=$this->db->get("iclock");
        $datas = $query->row_array();
        log_history("update","iclock",$datas);

        $dataupdate = array(
            'alias' => $alias,
            'errdelay' => $delayerror,
            'delay'=>$delay,
            'timezone'=>$timezone,
            'is_reguler'=>$reguler);
        $this->db->where('sn', $datas["sn"]);
        $this->db->update('iclock', $dataupdate);

        $this->db->where('sn', $datas["sn"]);
        //$woowdb->update('iclock', $dataupdate);

        $data['msg'] = 'Data mesin berhasil diperbaharui..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function hapus()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();
            log_history("delete","iclock",$datas);

            $this->db->where('id',$nId);
            $this->db->delete('iclock');

            if (isset($datas)) {
                createLog("Menghapus mesin  " . $datas["sn"] , "Sukses");
            }
        }

        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */