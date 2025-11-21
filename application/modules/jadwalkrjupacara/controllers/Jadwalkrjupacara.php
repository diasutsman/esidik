<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jadwalkrjupacara extends MX_Controller {

    private $aAkses;

	function Jadwalkrjupacara(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
        $this->load->model('report_model','report');
        $this->load->model('Process_model',"process_model");
        $this->load->model('shift/shift_model','shift');
		$this->load->model('Jadwalkrjupacara_model','jadwalkrj');
        $this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('stathadir/stathadir_model','stathadir');
        $this->load->model('statabsen/statabsen_model','statabsen');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Jadwalkrjupacara", $this->session->userdata('s_access'));
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

        $this->session->set_userdata('menu','18');
        $data['menu'] = '18';
        $uri_segment=3;
        $offset = 0;

        $data2 = $this->mypagination->getPagination(0,10,site_url('jadwalkrjupacara/pagging/'),$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = 0;
        $data['result'] = array();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();
        $data['lstshift']= $this->shift->getDaftar(0,null,null,null,null,null)->result();
        $this->template->load('template','display',$data);
    }

    public function pagging($page=0)
    {
        $data['aksesrule']=$this->aAkses;

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
            //$SQLcari .= " Order by keselon ASC, kgolru DESC  ";
            $data['order'] ="userid";
        }

        if ( $this->session->userdata("result"))
        {
            $arr = $this->session->userdata("result");
            //print_r($arr);
            ArraySortBy("userid",$arr,strtolower($sorting));
            $data['result'] = array_slice($arr, $offset, $limited);

            $data2 = $this->mypagination->getPagination($this->session->userdata("jum_data"), $limited, site_url("jadwalkrjupacara/pagging"), 3);
            $data['paging'] = $data2['link'];
            $data['offset'] = $offset;
            $data['limit_display'] = $limited;
            $data['jum_data'] = $this->session->userdata("jum_data");

            $data['arrcolor'] = $this->session->userdata("arrcolor");
            $data['start_date'] = $this->session->userdata("start_date");
            $data['end_date'] = $this->session->userdata("end_date");
            $this->session->set_userdata("result",$arr);
        }
        else
        {
            $data2 = $this->mypagination->getPagination(0,10,site_url('jadwalkrjupacara/pagging'),3);
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
            $jdwl = $this->jadwalkrj->getjadwalupacara($userid, $coldate);
            //print_r($this->db->last_query());
            $recjdwl = $jdwl->first_row();
            //die('test');
            $jmlSum=0;
            
            if ($recjdwl!=null){

                if ($recjdwl->attendance_lainnya=='CPC')
                {
                    $jdwl2 = '[ '.date('H:i', strtotime($recjdwl->rostertime))." s/d ".date('H:i', strtotime($recjdwl->rostertime_end)).' ]';
                    $dataarray[0] = array('userid' => $userid, 'name' => 'Pembatalan Upacara',
                        'scdhdate' => date('d-m-Y', strtotime($coldate))." ".$jdwl2,
                        'transdate' => date('d-m-Y', strtotime($coldate)),
                        'transtime' => date('d-m-Y', strtotime($recjdwl->rosterdate)),
                        'attendance' => date('d-m-Y H:i:s', strtotime($recjdwl->update_date)),
                        'absence' => '',
                        'attendance' => '', 'editby' => $recjdwl->editby);    
                }
                $translog = $this->jadwalkrj->gettranslog($userid, $coldate);
                foreach ($translog->result() as $tl) {
                    $jdwl1 = date('d-m-Y', strtotime($coldate));
                    $jdwl2 = '[ '.date('H:i', strtotime($recjdwl->rostertime))." s/d ".date('H:i', strtotime($recjdwl->rostertime_end)).' ]';

                    $dataarray[$a] = array(
                        'userid' => $tl->userid,
                        'name' => $tl->name,
                        'scdhdate' => date('d-m-Y', strtotime($tl->checktime))." ".$jdwl2,
                        'transdate' => date('d-m-Y', strtotime($tl->checktime)),
                        'transtime' => $recjdwl->rostertime." ".$recjdwl->rostertime_end,
                        'absence' => '',
                        'attendance' => date('d-m-Y H:i:s', strtotime($tl->checktime)),
                        'editby' => $tl->editby
                    );
                    $a++;
                }

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
        $jnspeg = $this->input->post('jnspeg');
        $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $datestart));
        $this->db->where('tahun', date('Y', $datestart));
        $query = $this->db->get();
        $rowcount = $query->num_rows();
        if ($rowcount>0) {
            $bukatutup = $query->row()->status?true:false;
        } else {
            $bukatutup = true;
        }

        $arrcolor["CPC"] = "#dd1637";
        $arrcolor["UPC"] = "#07ed29";
        $arrcolor["NWDS"] = "#E3081D";

        $att = $this->report->getatt();
        foreach($att->result() as $at) {
            $atar[$at->atid] = $at->atname;
        }

        $abs = $this->report->getabs();
        foreach($abs->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
        }

        //$deptshift = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',',$this->session->userdata('s_dept'))):array();
        $deptshift = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall($this->session->userdata('s_dept')):array();


        if($this->input->post('org')!='')	{
            if(in_array($this->input->post('org'), $deptshift)) {
                $orgid = $this->pegawai->deptonall($this->input->post('org'));

                $areaid = array();
                $userlist = $this->report->getempofdept($areaid, $orgid,$stspeg,$cari,$jnspeg);

                $countuserlist = $userlist->num_rows();
            } else {
                $orgid = array();
                $countuserlist = 0;
            }
        } else {
            if(!empty($deptshift)) {
                $orgid = $this->pegawai->deptonall($deptshift);

                $areaid = array();
                $userlist = $this->report->getempofdept($areaid, $orgid,$stspeg,$cari,$jnspeg);

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
                $arrayroster[$rosterdetail->userid][strtotime($rosterdetail->rosterdate)] =
                 array('attendance' => $rosterdetail->attendance,'attendance_lainnya' => $rosterdetail->attendance_lainnya);
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
                        $shiftname = $arrayroster[$datauser->userid][$datestart + ($j * 86400)]['attendance'];
                        //$absattstat = $arrayroster[$datauser->userid][$datestart + ($j * 86400)]['attendance'];
                        $absattstat2 = $arrayroster[$datauser->userid][$datestart + ($j * 86400)]['attendance_lainnya'];
                        if ($shiftname) {
                            if (isset($absattstat2)) {
                                if($absattstat2=='CPC')
                                    $data_arr[($datestart + ($j*86400))]=$shiftname.'#'.$absattstat2;
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

        $data2 = $this->mypagination->getPagination($jum_data,10,site_url("jadwalkrjupacara/pagging"),3);
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
        $data["lst"]= $this->db->where("state",1)->get("attendance");
        $this->load->view('form',$data);
    }

    function form2($tgl1=null,$tgl2=null)
    {
        $data["tgl1"] = ($tgl1==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl1)));
        $data["tgl2"] = ($tgl2==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl2)));
        //$data["lst"]= $this->db->where("state",1)->get("absence");
        $this->load->view('form1',$data);
    }
    function form3($tgl1=null,$tgl2=null)
    {
        $data["tgl1"] = ($tgl1==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl1)));
        $data["tgl2"] = ($tgl2==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl2)));
        $this->load->view('form2',$data);
    }

    function form4($tgl1=null,$tgl2=null)
    {
        $data["tgl1"] = ($tgl1==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl1)));
        $data["tgl2"] = ($tgl2==null? date("d-m-Y") : date("d-m-Y",strtotime($tgl2)));
        $this->load->view('form2',$data);
    }

    function statusdata($idpil=0)
    {
        switch ($idpil) {
            /* case 1:
                $this->statushadir();
                break; */
            case 2:
                $this->statusbatalhadir();
                break;
            case 3:
                $this->statushapus();
                break;
            case 4:
                $this->statusbatalhapus();
                break;
            default:
                $data['msg'] = 'Tidak berhasil memproses data..';
                $data['status'] = 'succes';
                $this->output->set_output( json_encode($data));
                break;
        }
    }

    public function statusbatalhadir()
    {
        $userid = $this->input->post('userid');
        $datestart = strtotime(dmyToymd($this->input->post('start')));
        $dateend = strtotime(dmyToymd($this->input->post('end')));
        $abscode = 'CPC';//$this->input->post('sel1');
        //$nosk = $this->input->post('nosk');
        $notes = $this->input->post('catatan');
        $arr = array();
        $range = ($dateend - $datestart) / 86400;

        for($j=0;$j<=$range;$j++) {
            $tgal = $datestart + ($j * 86400);

            $atdate = date('Y-m-d', $tgal);

            $this->db->select('status');
            $this->db->from('bukatutup');
            $this->db->where('idbln', date('n', $tgal));
            $this->db->where('tahun', date('Y', $tgal));
            $query = $this->db->get();
            if ($query->num_rows()>0) {
                $bukatutup = $query->row()->status;
                if ($bukatutup) {
                    $this->db->where('userid', $userid);
                    $this->db->where('rosterdate', $atdate);
                    $this->db->where('attendance', 'UPC');
                    $rsl = $this->db->get("rosterdetailsatt_upacara");
                    if ($rsl->num_rows() > 0) {

                        $result = $rsl->result();
                        foreach ($result as $row) {
                            $this->db->where('id', $row->id);
                            $query = $this->db->get("rosterdetailsatt_upacara");
                            $datas = $query->row_array();
                            log_history("edit", "rosterdetailsatt_upacara", $datas);

                            if (isset($datas)) {
                                createLog("Pembatalan upacara" . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"] . " " . $datas["attendance"], "Sukses");
                            }
                        }

                        $dataupdater = array('attendance_lainnya' => $abscode, 'notes' => $notes,
                            'editby' => $this->session->userdata('s_username'),
                            'update_date' => date('Y-m-d'),
                            );
                        $this->db->where('userid', $userid);
                        $this->db->where('rosterdate', $atdate);
                        $this->db->update('rosterdetailsatt_upacara', $dataupdater);
                        $arr[] = ["id" => $userid . "-" . $atdate,"sts"=>"CPC"];
                    } /* else {
                        $dataupdate = array('userid' => $userid, 'rosterdate' => $atdate,
                            'attendance_lainnya' => $abscode, 'notes' => $notes,
                            'editby' => $this->session->userdata('s_username'),
                            'update_date' => date('Y-m-d'),
                            );
                        $this->db->insert('rosterdetailsatt_upacara', $dataupdate);

                        createLog("Membuat pembatalan upacara" . $userid . " " . $atdate . " " . $abscode, "Sukses");
                    } */
                    
                    
                    
                }
            }

        }


        $userid = explode(',', $this->input->post('userid'));
        //$this->rekalkulasi($datestart,$dateend,$userid);

        $data['data'] = json_encode($arr);
        $data['jmldata'] = count($arr);
        $data['msg'] = 'Data berhasil disimpan..<br><br>Dimohon memproses ulang absensi upacara!!';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
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

            $this->db->select('status');
            $this->db->from('bukatutup');
            $this->db->where('idbln', date('n', $tgal));
            $this->db->where('tahun', date('Y', $tgal));
            $query = $this->db->get();
            if ($query->num_rows()>0) {
                $bukatutup = $query->row()->status;

                if ($bukatutup) {
                    $useraidi = '';
                    //$this->db->where_in('userid', $userid);
                    $this->db->group_start();
                    $ids_chunk = array_chunk($userid,25);
                    foreach($ids_chunk as $sids)
                    {
                        $this->db->or_where_in('userid', $sids);
                    }
                    $this->db->group_end();
                    $this->db->where('rosterdate', $abdate);
                    $this->db->where('rosterdate', $abdate);
                    $this->db->where('attendance', 'UPC');
                    $this->db->where('attendance_lainnya is null', null,false);
                    $result = $this->db->get('rosterdetailsatt_upacara')->result();
                    foreach ($result as $row) {
                        $this->db->where('id', $row->id);
                        $query = $this->db->get("rosterdetailsatt_upacara");
                        $datas = $query->row_array();
                        log_history("delete", "rosterdetailsatt_upacara", $datas);

                        if (isset($datas)) {
                            $arr[] = ["id" => $usid . "-" . date('Y-m-d', $tgal),"sts"=>""];
                            createLog("Menghapus jadwal kerja upacara" . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"], "Sukses");
                        }
                    }
                    

                    foreach ($userid as $usid) {
                        $this->db->where('userid', $usid);
                        $this->db->where('rosterdate', $abdate);
                        $this->db->where('attendance', 'UPC');
                        $this->db->where('attendance_lainnya is null', null,false);
                        $query = $this->db->get("rosterdetailsatt_upacara");
                        $datas = $query->row_array();
                        log_history("delete", "rosterdetailsatt_upacara", $datas);

                        //hapus jadwal upacara
                        $this->db->where('userid', $usid);
                        $this->db->where('rosterdate', $abdate);
                        $this->db->where('attendance', 'UPC');
                        $this->db->where('attendance_lainnya is null', null,false);
                        $this->db->delete('rosterdetailsatt_upacara');

                        
                    }
                }
            }
        }

        //$this->rekalkulasi($datestart,$dateend,$userid);

        $data['data'] = count($arr)>0 ? json_encode($arr): "[]";
        $data['jmldata'] = count($arr);
        $data['msg'] = 'Data berhasil dihapus..<br><br>Dimohon memproses ulang absensi upacara!!';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
    }

    public function statusbatalhapus()
    {
        $userid = explode(',', $this->input->post('userid'));
        $datestart = strtotime(dmyToymd($this->input->post('start')));
        $dateend = strtotime(dmyToymd($this->input->post('end')));

        $range = ($dateend - $datestart) / 86400;
        $arr = array();
        for($j=0;$j<=$range;$j++) {
            $tgal = $datestart + ($j*86400);
            $abdate = date('Y-m-d', $tgal);

            $this->db->select('status');
            $this->db->from('bukatutup');
            $this->db->where('idbln', date('n', $tgal));
            $this->db->where('tahun', date('Y', $tgal));
            $query = $this->db->get();
            if ($query->num_rows()>0) {
                $bukatutup = $query->row()->status;

                if ($bukatutup) {
                    $useraidi = '';
                    //$this->db->where_in('userid', $userid);
                    $this->db->group_start();
                    $ids_chunk = array_chunk($userid,25);
                    foreach($ids_chunk as $sids)
                    {
                        $this->db->or_where_in('userid', $sids);
                    }
                    $this->db->group_end();
                    $this->db->where('rosterdate', $abdate);
                    $this->db->where('attendance_lainnya', 'CPC');
                    $result = $this->db->get('rosterdetailsatt_upacara')->result();
                    foreach ($result as $row) {
                        $this->db->where('id', $row->id);
                        $query = $this->db->get("rosterdetailsatt_upacara");
                        $datas = $query->row_array();
                        log_history("update", "rosterdetailsatt_upacara", $datas);

                        if (isset($datas)) {
                            $arr[] = ["id" => $datas["userid"] . "-" . date('Y-m-d', $tgal),"sts"=>"UPC"];
                            createLog("Menghapus pembatalan absensi upacara" . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"], "Sukses");
                        }

                        $dataupdater = array(
                            'attendance_lainnya' => NULL, 
                            'notes' => NULL,
                            'editby' => NULL,
                            );
                        $this->db->where('userid', $datas["userid"]);
                        $this->db->where('rosterdate', date('Y-m-d', $tgal));
                        $this->db->update('rosterdetailsatt_upacara', $dataupdater);

                        $dataupdater = array(
                            'attendance_lainnya' => NULL, 
                            'notes' => NULL,
                            'editby' => NULL,
                            );
                        $this->db->where('userid', $datas["userid"]);
                        $this->db->where('date_shift', date('Y-m-d', $tgal));
                        $this->db->update('process_upacara', $dataupdater);

                        
                    }
                    
                    
                }
            }
        }

        //$this->rekalkulasi($datestart,$dateend,$userid);

        $data['data'] = count($arr)>0 ? json_encode($arr): "[]";
        $data['jmldata'] = count($arr);
        $data['msg'] = 'Hapus pembatalan absensi upacara berhasil..<br><br>Dimohon memproses ulang absensi upacara!!';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
    }

    function rekalkulasi($startdate,$enddate,$userid)
    {
        ini_set("pcre.backtrack_limit", PHP_INT_MAX);
        ini_set("pcre.recursion_limit", PHP_INT_MAX);

        $datestart = strtotime($startdate);
        $dateend = strtotime($enddate);

        $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $datestart));
        $this->db->where('tahun', date('Y', $datestart));
        $query = $this->db->get();
        if ($query->num_rows()>0) {
            $bukatutup = $query->row()->status;
        } else {
            $bukatutup = FALSE;
        }

        if($bukatutup || $this->session->userdata('s_access')==1) {

            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');
            $range = ($dateend - $datestart) / 86400;

            $shift = $this->process_model->getshiftuserid($userid, $datestart, $dateend);
            $arrai = array();
            foreach($shift as $shifting) {
                $arrai[$shifting->userid][strtotime($shifting->rosterdate)] = array (
                    'start_in'			=> $shifting->start_in,
                    'check_in'			=> $shifting->check_in,
                    'end_check_in'		=> $shifting->end_check_in,
                    'start_out'			=> $shifting->start_out,
                    'check_out'			=> $shifting->check_out,
                    'end_check_out'		=> $shifting->end_check_out,
                    'start_break'		=> $shifting->start_break,
                    'break_out'			=> $shifting->break_out,
                    'break_in'			=> $shifting->break_in,
                    'end_break'			=> $shifting->end_break,
                    'late_tolerance'	=> $shifting->late_tolerance,
                    'early_departure'	=> $shifting->early_departure,
                    'shift_in'			=> $shifting->shift_in,
                    'shift_out'			=> $shifting->shift_out,
                    'ot_tolerance'		=> $shifting->ot_tolerance,
                    'in_ot_tolerance'	=> $shifting->in_ot_tolerance,
                    'out_ot_tolerance'	=> $shifting->out_ot_tolerance,
                    'attendance'		=> $shifting->attendance,
                    'absence'			=> $shifting->absence,
                    'notes'				=> $shifting->notes,
                    'emptype'			=> $shifting->emptype,
                    'codeshift'			=> $shifting->code_shift
                );
            }

            $holiday = $this->process_model->cekholiday($datestart, $dateend);
            $holarray = array();
            $flaghol = array();
            foreach($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if($selisih==0) {
                    $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                    $flaghol[$hol->deptid][$hol->startdate] = $hol->flag;
                } else {
                    $jarak = $selisih / 86400;
                    for($k=0;$k<=$jarak;$k++) {
                        $holarray[$hol->deptid][date('Y-m-d',strtotime($hol->startdate) + ($k*86400))] = $hol->info;
                        $flaghol[$hol->deptid][date('Y-m-d',strtotime($hol->startdate) + ($k*86400))] = $hol->flag;
                    }
                }
            }

            //$this->db->where_in('userid', $userid);
            $this->db->group_start();
            $ids_chunk = array_chunk($userid,25);
            foreach($ids_chunk as $sids)
            {
                $this->db->or_where_in('userid', $sids);
            }
            $this->db->group_end();

            $this->db->where('date_shift >=', date('Y-m-d', $datestart));
            $this->db->where('date_shift <=', date('Y-m-d', $dateend));
            $this->db->delete('process');

            $user = $this->process_model->getuserbyuser($userid);
            foreach($user->result() as $userya)
                $deptid[$userya->userid] = $userya->deptid;

            $otsetting = $this->process_model->cekotsetting();
            foreach($otsetting->result() as $otset)
                $otseting[$otset->field_id] = $otset->field_value;

            $abg = 1;
            foreach($userid as $userdata) {
                for($x=0;$x<=$range;$x++) {
                    $tanggal = date('Y-m-d', $datestart + ($x * 86400));
                    $workinholiday = 0; $xx = 0; $woh = 0;
                    $flagholiday = 0;
                    if(isset($holarray[$deptid[$userdata]][date('Y-m-d', strtotime($tanggal))]) || isset($holarray['1'][date('Y-m-d', strtotime($tanggal))])) $workinholiday = 1;
                    if(isset($flaghol[$deptid[$userdata]][date('Y-m-d', strtotime($tanggal))]) || isset($flaghol['1'][date('Y-m-d', strtotime($tanggal))])) $flagholiday = 1;

                    if(isset($arrai[$userdata][strtotime($tanggal)])) {

                        $tanggalsci = $tanggal;$tanggalci = $tanggal;$tanggaleci = $tanggal;$tanggalsb = $tanggal;$tanggalbo = $tanggal;
                        $tanggalbi = $tanggal;$tanggaleb = $tanggal;$tanggalsco = $tanggal;$tanggalco = $tanggal;$tanggaleco = $tanggal;

                        if($arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'PMD'  || $arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'S1C') {
                            $tanggalsco = date('Y-m-d', strtotime($tanggal) + 86400);
                            $tanggalco = date('Y-m-d', strtotime($tanggal) + 86400);
                            $tanggaleco = date('Y-m-d', strtotime($tanggal) + 86400);
                            $tanggaleb = date('Y-m-d', strtotime($tanggal) + 86400);
                        } else {
                            if($arrai[$userdata][strtotime($tanggal)]['check_in'] < $arrai[$userdata][strtotime($tanggal)]['start_in'])
                                $tanggalsci = date('Y-m-d', strtotime($tanggal) - 86400);
                            if($arrai[$userdata][strtotime($tanggal)]['end_check_in'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                $tanggaleci = date('Y-m-d', strtotime($tanggal) + 86400);
                            if($arrai[$userdata][strtotime($tanggal)]['start_break'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                $tanggalsb = date('Y-m-d', strtotime($tanggal) + 86400);
                            if($arrai[$userdata][strtotime($tanggal)]['break_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                $tanggalbo = date('Y-m-d', strtotime($tanggal) + 86400);
                            if($arrai[$userdata][strtotime($tanggal)]['break_in'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                $tanggalbi = date('Y-m-d', strtotime($tanggal) + 86400);
                            if($arrai[$userdata][strtotime($tanggal)]['end_break'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                $tanggaleb = date('Y-m-d', strtotime($tanggal) + 86400);
                            if($arrai[$userdata][strtotime($tanggal)]['start_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                $tanggalsco = date('Y-m-d', strtotime($tanggal) + 86400);
                            if($arrai[$userdata][strtotime($tanggal)]['check_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                $tanggalco = date('Y-m-d', strtotime($tanggal) + 86400);
                            if($arrai[$userdata][strtotime($tanggal)]['end_check_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                $tanggaleco = date('Y-m-d', strtotime($tanggal) + 86400);
                        }

                        $startcheckin 	= strtotime($tanggalsci." ".$arrai[$userdata][strtotime($tanggal)]['start_in']);
                        $cekin 			= strtotime($tanggalci." ".$arrai[$userdata][strtotime($tanggal)]['check_in']);
                        $endcheckin 	= strtotime($tanggaleci." ".$arrai[$userdata][strtotime($tanggal)]['end_check_in']);

                        $startcheckout 	= strtotime($tanggalsco." ".$arrai[$userdata][strtotime($tanggal)]['start_out']);
                        $cekout 		= strtotime($tanggalco." ".$arrai[$userdata][strtotime($tanggal)]['check_out']);
                        $endcheckout 	= strtotime($tanggaleco." ".$arrai[$userdata][strtotime($tanggal)]['end_check_out']);

                        $startbreak 	= strtotime($tanggalsb." ".$arrai[$userdata][strtotime($tanggal)]['start_break']);
                        $breakout		= strtotime($tanggalbo." ".$arrai[$userdata][strtotime($tanggal)]['break_out']);
                        $breakin		= strtotime($tanggalbi." ".$arrai[$userdata][strtotime($tanggal)]['break_in']);
                        $endbreak 		= strtotime($tanggaleb." ".$arrai[$userdata][strtotime($tanggal)]['end_break']);

                        $latetolerance 	= $arrai[$userdata][strtotime($tanggal)]['late_tolerance'];
                        $earlydeparture	= $arrai[$userdata][strtotime($tanggal)]['early_departure'];
                        $shiftin		= $arrai[$userdata][strtotime($tanggal)]['shift_in'];
                        $shiftout		= $arrai[$userdata][strtotime($tanggal)]['shift_out'];
                        $ottol			= $arrai[$userdata][strtotime($tanggal)]['ot_tolerance'];
                        $ottolin		= $arrai[$userdata][strtotime($tanggal)]['in_ot_tolerance'];
                        $ottolout		= $arrai[$userdata][strtotime($tanggal)]['out_ot_tolerance'];
                        $attendances	= $arrai[$userdata][strtotime($tanggal)]['attendance'];
                        $absences		= $arrai[$userdata][strtotime($tanggal)]['absence'];
                        $notes			= $arrai[$userdata][strtotime($tanggal)]['notes'];
                        $emptype		= $arrai[$userdata][strtotime($tanggal)]['emptype'];

                        if($absences=='OFF' || $absences=='OFFPD') $attendances = 'NWDS';

                        if($arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'OFFPD') {
                            $empin = strtotime(false);
                            $empout = strtotime(false);
                        } else {
                            $empin = strtotime($this->process_model->getawal($userdata, date('Y-m-d H:i:s', $startcheckin), date('Y-m-d H:i:s', $endcheckin),1));
                            $empout = strtotime($this->process_model->getakhir($userdata, date('Y-m-d H:i:s', $startcheckout), date('Y-m-d H:i:s', $endcheckout),1));
                        }
                        $editin = $this->process_model->getedit($userdata, date('Y-m-d H:i:s', $empin));
                        $editout = $this->process_model->getedit($userdata, date('Y-m-d H:i:s', $empout));

                        $brkout = strtotime($this->process_model->getawal($userdata, date('Y-m-d H:i:s', $startbreak), date('Y-m-d H:i:s', $endbreak),1));
                        $brkin = strtotime($this->process_model->getakhir($userdata, date('Y-m-d H:i:s', $startbreak), date('Y-m-d H:i:s', $endbreak),1));

                        $fixcekin=0; $otcekin=0; $fixcekout=0; $otcekout=0; $late=0; $early=0; $otbefore=0; $otafter=0;

                        $fixcekin = $cekin + ($latetolerance * 60);
                        if($empin > $fixcekin) {
                            if($shiftin==0) {
                                $late = $empin - $cekin;
                            } else {
                                $late = $empin - $fixcekin;
                            }
                        } else {
                            $late = 0;
                        }

                        $fixcekout = $cekout - ($earlydeparture * 60);
                        if($empout < $fixcekout) {
                            if($shiftout==0) {
                                $early = $cekout - $empout;
                            } else {
                                $early = $fixcekout - $empout;
                            }
                        } else {
                            $early = 0;
                        }

                        $otcekin = $cekin - ($ottol * 60);
                        if($empin < $otcekin) {
                            if($ottolin==0) {
                                $otbefore = $cekin - $empin;
                            } else {
                                $otbefore = $otcekin - $empin;
                            }
                        } else {
                            $otbefore = 0;
                        }

                        $otcekout = $cekout + ($ottol * 60);
                        if($empout > $otcekout) {
                            if($ottolout==0) {
                                $otafter = $empout - $cekout;
                            } else {
                                $otafter = $empout - $otcekout;
                            }
                        } else {
                            $otafter = 0;
                        }


                        if($abg == $empin && $attendances == 'NWDS') {
                            $datein = null;
                            $timein = null;
                            $late = 0;
                            $otbefore = 0;
                            $dateout = null;
                            $timeout = null;
                            $early = 0;
                            $otafter = 0;
                            $abg = 1;
                        } else {
                            if($empin==0) {
                                $datein = null;
                                $timein = null;
                                $late = 0;
                                $otbefore = 0;
                            } else {
                                $datein = date('Y-m-d', $empin);
                                $timein = date('H:i:s', $empin);
                                $xx = 1;
                            }

                            if($empout==0) {
                                $dateout = null;
                                $timeout = null;
                                $early = 0;
                                $otafter = 0;
                            } else {
                                $dateout = date('Y-m-d', $empout);
                                $timeout = date('H:i:s', $empout);
                                $xx = 1;
                            }

                            $abg = $empout;

                        }

                        $brkout==0 ? $brkout = null : $brkout = date('H:i:s', $brkout);
                        $brkin==0 ? $brkin = null : $brkin = date('H:i:s', $brkin);

                        if($otseting[1]==0) $otbefore = 0;
                        if($otseting[2]==0) $otafter = 0;

                        if($attendances=='NWDS') {
                            $xx==1?$woh=2:$woh=0;
                            $workinholiday==1?$workinholiday=1:$workinholiday=$woh;
                            $late = 0;
                            $early = 0;
                        }

                        if(strpos($attendances,'AB_')!==false) {
                            $late = 0;
                            $early = 0;
                            $otbefore = 0;
                            $otafter = 0;
                        }

                        if($attendances != 'AT_') {
                            if(strpos($attendances,'AT_')!==false) {
                                /*if($attendances=='AT_AT6' || $attendances=='AT_AT3' || $attendances=='AT_AT1' || $attendances=='AT_SK01'|| $attendances=='AT_SK03') $late = 0;
                                else if($attendances=='AT_AT6' || $attendances=='AT_AT2' || $attendances=='AT_SK02') $early = 0;*/
                                if($attendances=='AT_AT1' || $attendances=='AT_SK01') $late = 0;
                                else if($attendances=='AT_AT2' || $attendances=='AT_SK03') $early = 0;
                                else {
                                    $late = 0;
                                    $early = 0;
                                }
                            }
                        }

                        $attend = '';
                        if($empin!=0 or $empout!=0) {
                            if($workinholiday==0) {
                                $attend = $attendances;
                            }
                        } else {
                            if($attendances=='') {
                                $attend = 'ALP';
                            } else if($attendances=='NWDS') {
                                $attend = 'NWK';
                            } else {
                                if($workinholiday==0) {
                                    $attend = $attendances;
                                }
                            }
                        }

                        if($emptype==2) {
                            $attend = $attendances;
                            $late = 0;
                            $early = 0;
                            $otbefore = 0;
                            $otafter = 0;
                        }

                        $savetemporary = array (
                            'userid' 			=> $userdata,
                            'date_shift'		=> date('Y-m-d', strtotime($tanggal)),
                            'shift_in'			=> date('H:i:s', $cekin),
                            'shift_out'			=> date('H:i:s', $cekout),
                            'date_in' 			=> $datein,
                            'check_in' 			=> $timein,
                            'break_out'			=> $brkout,
                            'break_in'			=> $brkin,
                            'date_out' 			=> $dateout,
                            'check_out' 		=> $timeout,
                            'late' 				=> $late,
                            'early_departure' 	=> $early,
                            'ot_before'			=> $otbefore,
                            'ot_after'			=> $otafter,
                            'workinholiday'		=> $workinholiday,
                            'attendance'		=> $attend,
                            'edit_come'			=> $editin,
                            'edit_home'			=> $editout,
                            'notes'				=> $notes!=''?$notes:null,
                            'flaghol'			=> $flagholiday
                        );
                        $this->db->insert('process', $savetemporary);

                    } else {
                        $savetemporary = array (
                            'userid' 			=> $userdata,
                            'date_shift'		=> date('Y-m-d', strtotime($tanggal)),
                            'shift_in'			=> null,
                            'shift_out'			=> null,
                            'date_in' 			=> null,
                            'check_in' 			=> null,
                            'break_out'			=> null,
                            'break_in'			=> null,
                            'date_out' 			=> null,
                            'check_out' 		=> null,
                            'late' 				=> null,
                            'early_departure' 	=> null,
                            'ot_before'			=> null,
                            'ot_after'			=> null,
                            'workinholiday'		=> $workinholiday,
                            'attendance'		=> 'BLNK',
                            'edit_come'			=> null,
                            'edit_home'			=> null,
                            'notes'				=> null,
                            'flaghol'			=> $flagholiday
                        );
                        $this->db->insert('process', $savetemporary);
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */