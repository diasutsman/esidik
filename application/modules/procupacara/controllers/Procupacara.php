<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Procupacara extends MX_Controller {
    private $aAkses;

	function Procupacara(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('Procupacara_model','Procdata_model');
        $this->load->model('Process_model',"process_model");
        $this->load->model('report_model');
        $this->load->model('pegawai/pegawai_model','pegawai');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Procupacara", $this->session->userdata('s_access'));
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
        $this->session->set_userdata('menu','33');
        $data['menu'] = '33';
        $uri_segment=3;
        $offset = 0;
        //$orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        $SQLcari="";
        /*if(!empty($orgid)) {
            $s = array();
            foreach($orgid as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and deptid in (".implode(',', $s).") ";
        }*/


        $SQLcari .=" and jenispegawai in (1,2) and jftstatus in (1,2)";
        $SQLcari .= " ORDER BY id asc";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('procupacara/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();
        $data['order'] = 'id';
        $data['typeorder'] = 'sorting';
        $this->template->load('template','display',$data);
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
        $limited = $this->input->post('lmt');
        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $jnspeg = $this->input->post('jnspeg');

        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( name LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or badgenumber LIKE '%".str_replace('%20',' ',$cr)."%' or userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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

            $SQLcari .= " and deptid in (".implode(',', $s).") ";
        }

        if(!empty($stspeg)) {
            $s = array();
            foreach($stspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and jftstatus in (".implode(',', $s).") ";
        }

        if(!empty($jnspeg)) {
            $s = array();
            foreach($jnspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and jenispegawai in (".implode(',', $s).") ";
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
            $sorting = 'DESC';
            $data['typeorder'] = 'sorting_desc';
        }

        if($data['order']!='' && $data['order']!=null){
            $SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
        }else{
            $SQLcari .= " ORDER BY id ASC ";
            $data['order'] ="id";
        }
        $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("procupacara/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    function pegawai()
    {
        ini_set("pcre.backtrack_limit", PHP_INT_MAX);
        ini_set("pcre.recursion_limit", PHP_INT_MAX);

        $datestart = strtotime(dmyToymd($this->input->post('startdate')));
        $dateend = strtotime(dmyToymd($this->input->post('enddate')));
        $userid = $this->input->post('userid');

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

        $akrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $akrga[$at->atid] = $at->ign_to;
        }

        //if($this->session->userdata('s_access')==1) 
        {
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');

            $range = ($dateend - $datestart) / 86400;

            $shift = $this->process_model->getupacarauserid($userid, $datestart, $dateend);
            
            $arrai = array();
            foreach($shift as $shifting) {
                $this->db->select('status');
                $this->db->from('bukatutup');
                $this->db->where('idbln', date('n', strtotime($shifting->rosterdate)));
                $this->db->where('tahun', date('Y', strtotime($shifting->rosterdate)));
                $query = $this->db->get();
                if ($query->num_rows()>0) {
                    $bukatutup = ($query->row()->status==0) ? TRUE:FALSE;
                } else {
                    $bukatutup = FALSE;
                }

                if (!$bukatutup){
                    $arrai[$shifting->userid][strtotime($shifting->rosterdate)] = array (
                        'date'			    => $shifting->rosterdate,
                        'check_in'			=> $shifting->rostertime,
                        'check_out'			=> $shifting->rostertime_end,
                        'attendance'		=> $shifting->attendance,
                        'rosterdate2'		=> $shifting->rosterdate2,
                        'attendance2'		=> $shifting->attendance2,
                        'attendancebatal'		=> $shifting->attendance_lainnya,
                        'notes'			    => $shifting->notes,
                        'notes2'			=> $shifting->notes2,
                    );
                    $this->db->where('userid', $shifting->userid);
                    $this->db->where('date_shift', date('Y-m-d', strtotime($shifting->rosterdate)));
                    $this->db->delete('process_upacara');
                }
            }
            //log_message('error', json_encode($arrai)); 

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

            /* $this->db->where_in('userid', $userid);
            $this->db->where('date_shift >=', date('Y-m-d', $datestart));
            $this->db->where('date_shift <=', date('Y-m-d', $dateend));
            $this->db->delete('process_upacara'); */

            $user = $this->process_model->getuserbyuser($userid);
            foreach($user->result() as $userya)
                $deptid[$userya->userid] = $userya->deptid;

            foreach($userid as $userdata)
            {
                for($x=0;$x<=$range;$x++) {
                    $tanggal = date('Y-m-d', $datestart + ($x * 86400));
                    $workinholiday = 0;
                    $flagholiday = 0;
                    if(isset($holarray[$deptid[$userdata]][date('Y-m-d', strtotime($tanggal))]) || isset($holarray['1'][date('Y-m-d', strtotime($tanggal))])) $workinholiday = 1;
                    if(isset($flaghol[$deptid[$userdata]][date('Y-m-d', strtotime($tanggal))]) || isset($flaghol['1'][date('Y-m-d', strtotime($tanggal))])) $flagholiday = 1;

                    $this->db->select('status');
                        $this->db->from('bukatutup');
                        $this->db->where('idbln', date('n', strtotime($tanggal)));
                        $this->db->where('tahun', date('Y', strtotime($tanggal)));
                        $query = $this->db->get();
                        if ($query->num_rows()>0) {
                            $bukatutup = ($query->row()->status==0) ? TRUE:FALSE;
                        } else {
                            $bukatutup = FALSE;
                        }
                        //log_message('error', 'Tanggal '.date('Y-m-d', strtotime($tanggal))); 

                        if (!$bukatutup)
                        {
                            if(isset($arrai[$userdata][strtotime($tanggal)])) {

                                $empin = $this->process_model->getawal($userdata, 
                                                                date('Y-m-d H:i:s', strtotime($tanggal." ".$arrai[$userdata][strtotime($tanggal)]['check_in'])),
                                                                date('Y-m-d H:i:s', strtotime($tanggal." ".$arrai[$userdata][strtotime($tanggal)]['check_out'])),
                                                                0);

                                //log_message('error', 'empi '.$empin);                                 
                                $cekin 			= strtotime($tanggal." ".$arrai[$userdata][strtotime($tanggal)]['check_in']);
                                $cekout 		= strtotime($tanggal." ".$arrai[$userdata][strtotime($tanggal)]['check_out']);

                                $attendances	= $arrai[$userdata][strtotime($tanggal)]['attendance'];
                                $attendances2	= $arrai[$userdata][strtotime($tanggal)]['attendance2'];
                                $attendancesbatal	= $arrai[$userdata][strtotime($tanggal)]['attendancebatal'];
                                //log_message('error', 'att'.$attendances);
                                //log_message('error', 'att2'.$attendances2);
                                $datein=NULL;
                                $timein=NULL;
                                if (!empty($empin) || ($empin!=null) || ($empin!=false))  {
                                    
                                    $datein = date('Y-m-d', strtotime($empin));
                                    $timein = date('H:i:s', strtotime($empin));
                                } 

                                if ($attendances2!='')
                                {
                                    $attendances = $attendances2;
                                } else {
                                    if ($attendancesbatal !='')
                                    {
                                        $attendances = $attendancesbatal;
                                    } 
                                }

                                $notes = $arrai[$userdata][strtotime($tanggal)]['notes'];
                                $notes2 = $arrai[$userdata][strtotime($tanggal)]['notes2'];

                                $this->db->select('userid');
                                $this->db->from('process_upacara');
                                $this->db->where('userid', $userdata);
                                $this->db->where('date_shift', date('Y-m-d', strtotime($tanggal)));
                                $queryf = $this->db->get();
                                $allowInsert = ($queryf->num_rows() > 0) ? FALSE : TRUE;

                                if ($allowInsert) {
                                    $savetemporary = array (
                                        'userid' 			=> $userdata,
                                        'date_shift'		=> date('Y-m-d', strtotime($tanggal)),
                                        'shift_in'			=> date('H:i:s', $cekin),
                                        'shift_out'			=> date('H:i:s', $cekout),
                                        'date_in' 			=> isset($empin)? $datein : NULL,
                                        'check_in' 			=> isset($timein)? $timein: NULL,
                                        'workinholiday'		=> $workinholiday,
                                        //'attendance'		=> $attendances2!=''?$attendances2:$attendances,
                                        'attendance'		=> $attendances,
                                        'notes'				=> $notes2!=''?$notes2:$notes,
                                        'flaghol'			=> $flagholiday
                                    );
                                    $this->db->insert('process_upacara', $savetemporary);
                                } else
                                {
                                    $this->db->where('userid', $userdata);
                                    $this->db->where('date_shift', strtotime($tanggal));
                                    $updatetemporary = array(
                                        'shift_in' => date('H:i:s', $cekin),
                                        'shift_out' => date('H:i:s', $cekout),
                                        'date_in' => isset($empin)? $datein : NULL,
                                        'check_in' => isset($timein)? $timein: NULL,
                                        'workinholiday' => $workinholiday,
                                        //'attendance' => $attendances2!=''?$attendances2:$attendances,
                                        'attendance' => $attendances,
                                        'notes' => $notes != '' ? $notes : null, 
                                        'flaghol' => $flagholiday);
                                    $this->db->update('process_upacara', $updatetemporary);
                                }
                            } 
                        } else {
                            log_message('error', 'Buka tutup '); 
                        }
                }
            }


            $data['msg'] = 'Data sudah diproses..';
        } /* else {
            $data['msg'] = 'Maaf anda tidak punya akses..';
        } */

        $data['status'] = 'succes';
        echo json_encode($data);
    }

    function allpegawai()
    {
        ini_set("pcre.backtrack_limit", PHP_INT_MAX);
        ini_set("pcre.recursion_limit", PHP_INT_MAX);

        $datestart = strtotime(dmyToymd($this->input->post('startdate')));
        $dateend = strtotime(dmyToymd($this->input->post('enddate')));
        $orgidi = $this->input->post('org')=='undefined'?'1':$this->input->post('org');

        /* $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $datestart));
        $this->db->where('tahun', date('Y', $datestart));
        $query = $this->db->get();
        if ($query->num_rows()>0) {
            $bukatutup = $query->row()->status;
        } else {
            $bukatutup = FALSE;
        } */

        $akrga = array();
        $attrecap = $this->report_model->getatt();
        foreach($attrecap->result() as $at) {
            $akrga[$at->atid] = $at->ign_to;
        }

       // if($this->session->userdata('s_access')==1) 
        {
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');
            $orgid = $this->pegawai->deptonall($orgidi);
            $range = ($dateend - $datestart) / 86400;

            $shift = $this->process_model->getupacaraorgid($orgid, $datestart, $dateend);
            $arrai = array();
            foreach($shift as $shifting) {
                $this->db->select('status');
                $this->db->from('bukatutup');
                $this->db->where('idbln', date('n', strtotime($shifting->rosterdate)));
                $this->db->where('tahun', date('Y', strtotime($shifting->rosterdate)));
                $query = $this->db->get();
                if ($query->num_rows()>0) {
                    $bukatutup = ($query->row()->status==0) ? TRUE:FALSE;
                } else {
                    $bukatutup = FALSE;
                }
                if (!$bukatutup){
                    $arrai[$shifting->userid][strtotime($shifting->rosterdate)] = array (
                        'date'			=> $shifting->rosterdate,
                        'check_in'			=> $shifting->rostertime,
                        'check_out'			=> $shifting->rostertime_end,
                        'attendance'			=> $shifting->attendance,
                        'rosterdate2'			=> $shifting->rosterdate2,
                        'attendance2'			=> $shifting->attendance2,
                        'attendancebatal'			=> $shifting->attendance_lainnya,
                        'notes'			=> $shifting->notes,
                        'notes2'			=> $shifting->notes2,
                    );
                    $this->db->where('userid', $shifting->userid);
                    $this->db->where('date_shift', date('Y-m-d', strtotime($shifting->rosterdate)));
                    $this->db->delete('process_upacara');
                }
            }

            $holiday = $this->process_model->cekholiday($datestart, $dateend);
            $holarray = array();
            $flaghol = array();
            $useraidi = array();

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

            $user = $this->process_model->getuserbyorg($orgid);

            foreach($user->result() as $userya) {
                $useraidi[] = $userya->userid;
                $deptid[$userya->userid] = $userya->deptid;
            }

            if (count($useraidi)>0) {
                /* $this->db->where_in('userid', $useraidi);
                $this->db->where('date_shift >=', date('Y-m-d', $datestart));
                $this->db->where('date_shift <=', date('Y-m-d', $dateend));
                $this->db->delete('process_upacara'); */

                $abg = 1;
                foreach($useraidi as $userdata) {
                    for($x=0;$x<=$range;$x++) {
                        $tanggal = date('Y-m-d', $datestart + ($x * 86400));
                        $workinholiday = 0; $xx = 0; $woh = 0;
                        $flagholiday = 0;
                        if(isset($holarray[$deptid[$userdata]][date('Y-m-d', strtotime($tanggal))]) || isset($holarray['1'][date('Y-m-d', strtotime($tanggal))])) $workinholiday = 1;
                        if(isset($flaghol[$deptid[$userdata]][date('Y-m-d', strtotime($tanggal))]) || isset($flaghol['1'][date('Y-m-d', strtotime($tanggal))])) $flagholiday = 1;

                        $this->db->select('status');
                        $this->db->from('bukatutup');
                        $this->db->where('idbln', date('n', strtotime($tanggal)));
                        $this->db->where('tahun', date('Y', strtotime($tanggal)));
                        $query = $this->db->get();
                        if ($query->num_rows()>0) {
                            $bukatutup = ($query->row()->status==0) ? TRUE:FALSE;
                        } else {
                            $bukatutup = FALSE;
                        }
                        
                        if (!$bukatutup)
                        {
                            if(isset($arrai[$userdata][strtotime($tanggal)])) {

                                $empin = $this->process_model->getawal($userdata, 
                                    date('Y-m-d H:i:s', strtotime($tanggal." ".$arrai[$userdata][strtotime($tanggal)]['check_in'])),
                                    date('Y-m-d H:i:s', strtotime($tanggal." ".$arrai[$userdata][strtotime($tanggal)]['check_out'])),
                                    0);
                                $cekin 			= strtotime($tanggal." ".$arrai[$userdata][strtotime($tanggal)]['check_in']);
                                $cekout 		= strtotime($tanggal." ".$arrai[$userdata][strtotime($tanggal)]['check_out']);

                                $attendances	= $arrai[$userdata][strtotime($tanggal)]['attendance'];
                                $attendances2	= $arrai[$userdata][strtotime($tanggal)]['attendance2'];
                                $attendancesbatal	= $arrai[$userdata][strtotime($tanggal)]['attendancebatal'];
                                
                                $datein = NULL;
                                $timein = NULL;

                                $notes = $arrai[$userdata][strtotime($tanggal)]['notes'];
                                $notes2 = $arrai[$userdata][strtotime($tanggal)]['notes2'];


                                if (!empty($empin) || ($empin!=null) || ($empin!=false))  {
                                    //log_message('error', 'tidak kosong'.$empin);
                                    $datein = date('Y-m-d', strtotime($empin));
                                    $timein = date('H:i:s', strtotime($empin));
                                } 

                                if ($attendances2!='')
                                {
                                    $attendances = $attendances2;
                                } else {
                                    if ($attendancesbatal !='')
                                    {
                                        $attendances = $attendancesbatal;
                                    } 
                                }
                                

                                $this->db->select('userid');
                                $this->db->from('process_upacara');
                                $this->db->where('userid', $userdata);
                                $this->db->where('date_shift', date('Y-m-d', strtotime($tanggal)));
                                $queryf = $this->db->get();
                                $allowInsert = ($queryf->num_rows() > 0) ? FALSE : TRUE;

                                if ($allowInsert) {
                                    $savetemporary = array (
                                        'userid' 			=> $userdata,
                                        'date_shift'		=> date('Y-m-d', strtotime($tanggal)),
                                        'shift_in'			=> date('H:i:s', $cekin),
                                        'shift_out'			=> date('H:i:s', $cekout),
                                        'date_in' 			=> isset($empin)? $datein : NULL,
                                        'check_in' 			=> isset($empin)? $timein: NULL,
                                        'workinholiday'		=> $workinholiday,
                                        //'attendance'		=> $attendances2!=''?$attendances2:$attendances,
                                        'attendance'		=> $attendances,
                                        'notes'				=> $notes2!=''?$notes2:$notes,
                                        'flaghol'			=> $flagholiday
                                    );
                                    $this->db->insert('process_upacara', $savetemporary);
                                } else
                                {
                                    $this->db->where('userid', $userdata);
                                    $this->db->where('date_shift', strtotime($tanggal));
                                    $updatetemporary = array(
                                        'shift_in' => date('H:i:s', $cekin),
                                        'shift_out' => date('H:i:s', $cekout),
                                        'date_in' => isset($empin)? $datein : NULL,
                                        'check_in' => isset($empin)? $timein: NULL,
                                        'workinholiday' => $workinholiday,
                                        //'attendance' => $attendances2!=''?$attendances2:$attendances,
                                        'attendance' => $attendances,
                                        'notes' => $notes != '' ? $notes : null, 'flaghol' => $flagholiday);
                                    $this->db->update('process_upacara', $updatetemporary);
                                }
                            } /*else {
                                $this->db->select('userid');
                                $this->db->from('process');
                                $this->db->where('userid', $userdata);
                                $this->db->where('date_shift', date('Y-m-d', strtotime($tanggal)));
                                $queryf = $this->db->get();
                                $allowInsert = ($queryf->num_rows() > 0) ? FALSE : TRUE;

                                if ($allowInsert) {
                                    $savetemporary = array(
                                        'userid' => $userdata,
                                        'date_shift' => date('Y-m-d', strtotime($tanggal)),
                                        'shift_in' => null,
                                        'shift_out' => null,
                                        'date_in' => null,
                                        'check_in' => null,
                                        'workinholiday' => $workinholiday,
                                        'attendance' => 'BLNK',
                                        'notes' => null,
                                        'flaghol' => $flagholiday
                                    );
                                    $this->db->insert('process_upacara', $savetemporary);
                                } else {
                                    $this->db->where('userid', $userdata);
                                    $this->db->where('date_shift', strtotime($tanggal));
                                    $updatetemporary = array(
                                        'shift_in' => null, 'shift_out' => null, 'date_in' => null,
                                        'check_in' => null,
                                        'workinholiday' => $workinholiday, 'attendance' => 'BLNK',
                                        'notes' => null,
                                        'flaghol' => $flagholiday);
                                    $this->db->update('process_upacara', $updatetemporary);
                                }
                            }*/
                        }
                    }
                }
            }
            $data['msg'] = 'Data sudah diproses..';
        } /* else {
            $data['msg'] = 'Maaf anda tidak punya akses..';
        } */

        $data['status'] = 'succes';
        echo json_encode($data);
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */