<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Jadwalkrj extends MX_Controller {

	function Jadwalkrj(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
        $this->load->model('report_model','report');
        $this->load->model('shift/shift_model','shift');
		$this->load->model('jadwalkrj_model','jadwalkrj');
        $this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('stathadir/stathadir_model','stathadir');
        $this->load->model('statabsen/statabsen_model','statabsen');

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
        $this->session->set_userdata('menu','18');
        $data['menu'] = '18';
        $uri_segment=3;
        $offset = 0;

        $data2 = $this->mypagination->getPagination(0,10,site_url('jadwalkrj/pagging/'),$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = 0;
        $data['result'] = array();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstshift']= $this->shift->getDaftar(0,null,null,null,null,null)->result();
        $this->template->load('template','display',$data);
    }

    public function pagging($page=0)
    {
        $limited = $this->input->post('lmt');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

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
            $sorting = 'DESC';
            $data['typeorder'] = 'sorting_desc';
        }

        if($data['order']!='' && $data['order']!=null){
            //$SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
        }else{
            //$SQLcari .= " ORDER BY id ASC ";
            $data['order'] ="userid";
        }

        if ( $this->session->userdata("result"))
        {
            $data['result'] = array_slice($this->session->userdata("result"), $offset, $limited);

            $data2 = $this->mypagination->getPagination($this->session->userdata("jum_data"), $limited, site_url("jadwalkrj/pagging"), 3);
            $data['paging'] = $data2['link'];
            $data['offset'] = $offset;
            $data['limit_display'] = $limited;
            $data['jum_data'] = $this->session->userdata("jum_data");

            $data['arrcolor'] = $this->session->userdata("arrcolor");
            $data['start_date'] = $this->session->userdata("start_date");
            $data['end_date'] = $this->session->userdata("end_date");
            $this->session->set_userdata("result",$this->session->userdata("result"));
        }
        else
        {
            $data2 = $this->mypagination->getPagination(0,10,site_url('jadwalkrj/pagging'),3);
            $data['paging'] = $data2['link'];
            $data['offset'] = 0;
            $data['jum_data'] = 0;
            $data['arrcolor'] = array();
            $data['result'] = array();
            $data['start_date'] = null;
            $data['end_date'] = null;
        }
        $this->load->view('list',$data);

    }

    function form()
    {
        $data['lstShift'] = $this->utils->getShift()->result();
        $this->load->view('form',$data);
    }

    function gethistory()
    {
        $userid = $this->input->post('userid');
        $coldate = $this->input->post('coldate');
        //$datarec = $this->input->post('colrec');
        $dataarray = array();
        $a = 1;

        if($coldate!='name' && $coldate!='userid')
        {

            $abs = $this->jadwalkrj->getabsen($userid, $coldate);
            if ($abs->num_rows()>0) {
                $dataarray[0] = array('userid' => $userid, 'name' => $abs->row()->name, 'transdate' => date('d-m-Y', strtotime($coldate)), 'transtime' => '', 'absence' => $abs->row()->notes == '' ? $abs->row()->abname : $abs->row()->notes, 'attendance' => '', 'editby' => $abs->row()->editby);
            }
            $att = $this->jadwalkrj->getattend($userid, $coldate);
            if ($att->num_rows()>0) {
                $dataarray[0] = array('userid' => $userid, 'name' => $att->row()->name, 'transdate' => date('d-m-Y', strtotime($coldate)), 'transtime' => '', 'absence' => '', 'attendance' => $att->row()->notes == '' ? $att->row()->atname : $att->row()->notes, 'editby' => $att->row()->editby);
            }

            $translog = $this->jadwalkrj->gettranslog($userid, $coldate);
            foreach($translog->result() as $tl) {
                $dataarray[$a] = array(
                    'userid'	=> $tl->userid,
                    'name'		=> $tl->name,
                    'transdate'	=> date('Y-m-d', strtotime($tl->checktime)),
                    'transtime'	=> date('H:i:s', strtotime($tl->checktime)),
                    'absence'	=> '',
                    'attendance' => '',
                    'editby'	=> $tl->editby
                );
                $a++;
            }

        }
        $data["lstData"] = $dataarray;
        $this->load->view('list3',$data);
    }

    function buatjadwal()
    {
        $postdatestart = $this->input->post('start');
        $cari = $this->input->post('cari');
        $cari= $cari=="cri"?"":$cari;
        $postdateend = $this->input->post('end');
        $datestart = strtotime(dmyToymd($postdatestart));
        $dateend = strtotime(dmyToymd($postdateend));
        $stspeg = $this->input->post('stspeg');
        $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $datestart));
        $this->db->where('tahun', date('Y', $datestart));
        $query = $this->db->get();
        $bukatutup = $query->row()->status?true:false;

        $this->db->select('code_shift,colour_shift');
        $this->db->from("master_shift");
        $shift =$this->db->get();
        foreach($shift->result() as $at) {
            $arrcolor[$at->code_shift] = $at->colour_shift;
        }

        $att = $this->report->getatt();
        foreach($att->result() as $at) {
            $atar[$at->atid] = $at->atname;
        }

        $abs = $this->report->getabs();
        foreach($abs->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
        }

        $deptshift = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',',$this->session->userdata('s_dept'))):array();


        if($this->input->post('org')!='')	{
            if(in_array($this->input->post('org'), $deptshift)) {
                $orgid = $this->pegawai->deptonall($this->input->post('org'));

                $areaid = array();
                $userlist = $this->report->getempofdept($areaid, $orgid,$stspeg,$cari);

                $countuserlist = $userlist->num_rows();
            } else {
                $orgid = array();
                $countuserlist = 0;
            }
        } else {
            if(!empty($deptshift)) {
                $orgid = $this->pegawai->deptonall($deptshift);

                $areaid = array();
                $userlist = $this->report->getempofdept($areaid, $orgid,$stspeg,$cari);

                $countuserlist = $userlist->num_rows();
            } else {
                $orgid = array();
                $countuserlist = 0;
            }
        }

        $fieldarr = array();
        if($countuserlist!=0) {
            $range = ($dateend - $datestart) / 86400;
            $roster = $this->jadwalkrj->getroster($orgid, $datestart, $dateend);
            $arrayroster = array();
            foreach ($roster->result() as $rosterdetail) {
                $arrayroster[$rosterdetail->userid][strtotime($rosterdetail->rosterdate)] = array('absence' => $rosterdetail->absence, 'attendance' => $rosterdetail->attendance);
            }

            $holiday = $this->jadwalkrj->holiday($orgid);

            $holarray = array();
            foreach ($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if ($selisih == 0) {
                    $holarray[] = $hol->startdate;
                } else {
                    $jarak = $selisih / 86400;
                    for ($k = 0; $k <= $jarak; $k++) {
                        $holarray[] = date('Y-m-d', strtotime($hol->startdate) + ($k * 86400));
                    }
                }
            }
            foreach ($userlist->result() as $datauser) {
                $data_arr = array(
                    'userid' => $datauser->userid,
                    'name' => $datauser->name,
                    'group' => $datauser->deptname);
                for ($j = 0; $j <= $range; $j++) {
                    if (isset($arrayroster[$datauser->userid][$datestart + ($j * 86400)])) {
                        $shiftname = $arrayroster[$datauser->userid][$datestart + ($j * 86400)]['absence'];
                        $absattstat = $arrayroster[$datauser->userid][$datestart + ($j * 86400)]['attendance'];
                        if ($shiftname) {
                            if (isset($absattstat)) {
                                if($absattstat=='NWDS')
                                    $data_arr[($datestart + ($j*86400))]=$shiftname.'#'.$absattstat;
                                else {
                                    if(array_key_exists($absattstat, $atar))
                                        $data_arr[($datestart + ($j*86400))]=$shiftname.'#AT';
                                    else if(array_key_exists($absattstat, $bbar))
                                        $data_arr[($datestart + ($j*86400))]=$shiftname.'#AB';
                                    else
                                        $data_arr[($datestart + ($j*86400))]=$shiftname;
                                }
                            } else {
                                $data_arr[($datestart + ($j * 86400))] = $shiftname;
                            }
                        } else {
                            if (in_array(date('Y-m-d', $datestart + ($j * 86400)), $holarray)) $data_arr[($datestart + ($j * 86400))] = '';
                        }
                    } else {
                        if (in_array(date('Y-m-d', $datestart + ($j * 86400)), $holarray)) $data_arr[($datestart + ($j * 86400))] = '';
                    }
                }
                $fieldarr[] = $data_arr;
            }
        }

        $jum_data = count($fieldarr);

        $data2 = $this->mypagination->getPagination($jum_data,10,site_url("jadwalkrj/pagging"),3);
        $data['paging'] = $data2['link'];
        $data['offset'] = 0;
        $data['limit_display'] = 10;
        $data['jum_data'] = $jum_data;

        $data["start_date"] = dmyToymd($postdatestart);
        $data["end_date"] = dmyToymd($postdateend);
        $data["result"]= array_slice($fieldarr,0,10);
        $data["arrcolor"]= $arrcolor;
        $data['order'] = 'userid';
        $data['typeorder'] = 'sorting_asc';
        $this->session->set_userdata('result',$fieldarr);
        $this->session->set_userdata('arrcolor',$arrcolor);
        $this->session->set_userdata('start_date',$postdatestart);
        $this->session->set_userdata('end_date',$postdateend);
        $this->session->set_userdata('jum_data',$jum_data);

        $this->load->view('list',$data);
    }

    function form1($tgl1=null,$tgl2=null)
    {
        $data["tgl1"] = ($tgl1==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl1)));
        $data["tgl2"] = ($tgl2==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl2)));
        $data["lst"]= $this->db->get("attendance");
        $this->load->view('form',$data);
    }

    function form2($tgl1=null,$tgl2=null)
    {
        $data["tgl1"] = ($tgl1==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl1)));
        $data["tgl2"] = ($tgl2==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl2)));
        $data["lst"]= $this->db->get("absence");
        $this->load->view('form1',$data);
    }
    function form3($tgl1=null,$tgl2=null)
    {
        $data["tgl1"] = ($tgl1==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl1)));
        $data["tgl2"] = ($tgl2==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl2)));
        $this->load->view('form2',$data);
    }

    function statusdata($idpil=0)
    {
        switch ($idpil) {
            case 1:
                $this->statushadir();
                break;
            case 2:
                $this->statustidakhadir();
                break;
            case 3:
                $this->statushapus();
                break;
            default:
                $data['msg'] = 'Tidak berhasil memproses data..';
                $data['status'] = 'succes';
                echo json_encode($data);
                break;
        }
    }

    public function statushadir()
    {
        $userid = $this->input->post('userid');
        $datestart = strtotime(dmyToymd($this->input->post('start')));
        $dateend = strtotime(dmyToymd($this->input->post('end')));
        $nosk = $this->input->post('nosk');
        $attcode = $this->input->post('sel1');
        $startattilog = '';
        $endattilog = '';
        
        if($this->session->userdata('s_access')==1) {
            $startattilog = $this->input->post('startattilog');
            $endattilog = $this->input->post('endattilog');
        }
        $notes = $this->input->post('catatan');

        $range = ($dateend - $datestart) / 86400;
        $arr = array();

        for($j=0;$j<=$range;$j++) {
            $tgal = $datestart + ($j*86400);

            $atdate = date('Y-m-d', $tgal);


            $this->db->where('userid', $userid);
            $this->db->where('rosterdate', $atdate);
            $rsl = $this->db->get("rosterdetailsatt");
            if ($rsl->num_rows() > 0) {

                $result =$rsl->result();
                foreach($result as $row) {
                    $this->db->where('id', $row->id);
                    $query=$this->db->get("rosterdetailsatt");
                    $datas = $query->row_array();
                    log_history("edit","rosterdetailsatt",$datas);

                    if (isset($datas)) {
                        createLog("Merubah jadwal kerja " . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"], "Sukses");
                    }
                }


                $dataupdater = array (
                    'attendance' 	=> $attcode,
                    'notes'			=> $notes,
                    'nosk'			=> $nosk,
                    'editby'		=> $this->session->userdata('s_username')
                );
                $this->db->where('userid', $userid);
                $this->db->where('rosterdate', $atdate);
                $this->db->update('rosterdetailsatt', $dataupdater);


                array_push($arr,array(
                    "id"=>$userid."-".$atdate,
                    "sts"=>"#AT"
                ));
            } else {
                $dataupdate = array('userid' => $userid,
                    'rosterdate' => $atdate,
                    'attendance' => $attcode,
                    'notes' => $notes,
                    'nosk'			=> $nosk,
                    'editby' => $this->session->userdata('s_username'));

                $this->db->insert('rosterdetailsatt', $dataupdate);
                createLog("Membuat jadwal kerja ".$userid." ".$atdate ." ".$attcode,"Sukses");
                array_push($arr,array(
                    "id"=>$userid."-".$atdate,
                    "sts"=>"#AT"
                ));
            }

            if($startattilog!='') {
                $this->db->where('checktime', $atdate . ' ' . $startattilog);
                $this->db->where('userid', $userid);
                $rsl = $this->db->get("checkinout");
                if ($rsl->num_rows() > 0) {
                    $logupdate = array('userid' => $userid,
                        'checktime' => $atdate . ' ' . $startattilog,
                        'checktype' => 0,
                        'verifycode' => 0,
                        'sn' => '1');
                    $this->db->insert('checkinout', $logupdate);
                    createLog("Membuat cekinout ".$userid." ".$atdate ." ".$startattilog,"Sukses");
                }
            }

            if($endattilog!='') {
                $this->db->where('checktime', $atdate . ' ' . $endattilog);
                $this->db->where('userid', $userid);
                $rsl = $this->db->get("checkinout");
                if ($rsl->num_rows() > 0) {
                    $logupdate = array('userid' => $userid,
                        'checktime' => $atdate . ' ' . $endattilog,
                        'checktype' => 0,
                        'verifycode' => 0, 'sn' => '1');
                    $this->db->insert('checkinout', $logupdate);
                    createLog("Membuat cekinout ".$userid." ".$atdate ." ".$endattilog,"Sukses");
                }
            }
        }
        createLog('Membuat jadwal kerja userid = '.$this->input->post('userid').' start date = '.$this->input->post('start').' end date = '.$this->input->post('end'),"Sukses");
        /*$actionlog = array(
            'user'			=> $this->session->userdata('s_username'),
            'ipadd'			=> getRealIpAddr(),
            'logtime'		=> date("Y-m-d H:i:s"),
            'logdetail'		=> 'Assign attendance status userid = '.$this->input->post('userid').' start date = '.$this->input->post('start').' end date = '.$this->input->post('end'),
            'info'			=> "Sukses"
        );
        $this->db->insert('goltca', $actionlog);*/
        $data['data'] = json_encode($arr);
        $data['msg'] = 'Data berhasil disimpan..';
        $data['status'] = 'succes';
        echo json_encode($data);
    }

    public function statustidakhadir()
    {
        $userid = $this->input->post('userid');
        $datestart = strtotime(dmyToymd($this->input->post('start')));
        $dateend = strtotime(dmyToymd($this->input->post('end')));
        $abscode = $this->input->post('sel1');
        $nosk = $this->input->post('nosk');
        $notes = $this->input->post('catatan');
        $arr = array();
        $range = ($dateend - $datestart) / 86400;

        for($j=0;$j<=$range;$j++) {
            $tgal = $datestart + ($j * 86400);

            $atdate = date('Y-m-d', $tgal);
            $this->db->where('userid', $userid);
            $this->db->where('rosterdate', $atdate);
            $rsl = $this->db->get("rosterdetailsatt");
            if ($rsl->num_rows() > 0) {

                $result =$rsl->result();
                foreach($result as $row) {
                    $this->db->where('id', $row->id);
                    $query=$this->db->get("rosterdetailsatt");
                    $datas = $query->row_array();
                    log_history("edit","rosterdetailsatt",$datas);

                    if (isset($datas)) {
                        createLog("Merubah jadwal kerja " . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"]. " " . $datas["attendance"], "Sukses");
                    }
                }

                $dataupdater = array('attendance' => $abscode, 'notes' => $notes,
                    'editby' => $this->session->userdata('s_username'),
                    'nosk'			=> $nosk);
                $this->db->where('userid', $userid);
                $this->db->where('rosterdate', $atdate);
                $this->db->update('rosterdetailsatt', $dataupdater);
            } else{
                $dataupdate = array('userid' => $userid, 'rosterdate' => $atdate,
                    'attendance' => $abscode, 'notes' => $notes,
                    'editby' => $this->session->userdata('s_username'),
                    'nosk'			=> $nosk);
                $this->db->insert('rosterdetailsatt', $dataupdate);

                createLog("Membuat jadwal kerja ".$userid." ".$atdate ." ".$abscode,"Sukses");

                $arr=array(
                    "id"=>$userid."-".$atdate,
                    "sts"=>"#AB"
                );
            }

        }
        /*$actionlog = array(
            'user'			=> $this->session->userdata('s_username'),
            'ipadd'			=> $this->input->ipaddress,
            'logtime'		=> date("Y-m-d H:i:s"),
            'logdetail'		=> 'Assign absence status userid = '.$this->input->post('userid').' start date = '.$this->input->post('start').' end date = '.$this->input->post('end'),
            'info'			=> "Sukses"
        );
        $this->db->insert('goltca', $actionlog);*/

        $data['data'] = "[".json_encode($arr)."]";
        $data['msg'] = 'Data berhasil disimpan..';
        $data['status'] = 'succes';
        echo json_encode($data);
    }

    public function statushapus()
    {
        $userid = explode(',', $this->input->post('userid'));
        $datestart = strtotime(dmyToymd($this->input->post('start')));
        $dateend = strtotime(dmyToymd($this->input->post('end')));

        $range = ($dateend - $datestart) / 86400;
        $arr = array();
        for($j=0;$j<=$range;$j++) {
            $tgal = $datestart + ($j*86400);
            $abdate = date('Y-m-d', $tgal);
            $useraidi = '';
            $this->db->where_in('userid', $userid);
            $this->db->where('rosterdate', $abdate);
            $result =$this->db->get('rosterdetailsatt')->result();
            foreach($result as $row) {
                $this->db->where('id', $row->id);
                $query=$this->db->get("rosterdetailsatt");
                $datas = $query->row_array();
                log_history("delete","rosterdetailsatt",$datas);

                if (isset($datas)) {
                    createLog("Menghapus jadwal kerja " . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"], "Sukses");
                }
            }

            $this->db->where_in('userid', $userid);
            $this->db->where('rosterdate', $abdate);
            $this->db->delete('rosterdetailsatt');
            foreach($userid as $usid) {

                $this->db->where('userid', $userid);
                $this->db->where('date(checktime)', date('Y-m-d', $tgal),false);
                $result =$this->db->get('checkinout')->result();
                foreach($result as $row) {
                    $this->db->where('id', $row->id);
                    $query=$this->db->get("checkinout");
                    $datas = $query->row_array();
                    log_history("delete","checkinout",$datas);

                    if (isset($datas)) {
                        createLog("Menghapus checkinout " . $userid . " " . $datas["checktime"] , "Sukses");
                    }
                }


                $deledittime = array (
                    'userid'			=> $usid,
                    'date(checktime)'	=> date('Y-m-d', $tgal),
                    'sn'			=> '1'
                );
                $this->db->delete('checkinout', $deledittime);
                $useraidi = $useraidi.$usid.',';

                //createLog("Menghapus checkinout ".$usid." ". date('Y-m-d', $tgal),"Sukses");

                $arr=array(
                    "id"=>$usid."-".date('Y-m-d', $tgal),
                );
            }

        }
        /*$actionlog = array(
            'user'			=> $this->session->userdata('s_username'),
            'ipadd'			=> $this->input->ipaddress,
            'logtime'		=> date("Y-m-d H:i:s"),
            'logdetail'		=> 'Hapus status userid = '. $useraidi.' start date = '.$this->input->post('start').' end date = '.$this->input->post('end'),
            'info'			=> "Sukses"
        );
        $this->db->insert('goltca', $actionlog);*/

        $data['data'] = "[".json_encode($arr)."]";
        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';
        echo json_encode($data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */