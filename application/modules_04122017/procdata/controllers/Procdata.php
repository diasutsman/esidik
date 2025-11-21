<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Procdata extends MX_Controller {
    private $aAkses;

	function Procdata(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('Procdata_model');
        $this->load->model('Process_model',"process_model");
        $this->load->model('pegawai/pegawai_model','pegawai');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Procdata", $this->session->userdata('s_access'));
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
    {$data['aksesrule']=$this->aAkses;
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
        $this_url = site_url('procdata/pagging/');
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
        $this_url = site_url("procdata/pagging");
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

            $this->db->where_in('userid', $userid);
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
                            $empin = strtotime($this->process_model->getawal($userdata, date('Y-m-d H:i:s', $startcheckin), date('Y-m-d H:i:s', $endcheckin)));
                            $empout = strtotime($this->process_model->getakhir($userdata, date('Y-m-d H:i:s', $startcheckout), date('Y-m-d H:i:s', $endcheckout)));
                        }
                        $editin = $this->process_model->getedit($userdata, date('Y-m-d H:i:s', $empin));
                        $editout = $this->process_model->getedit($userdata, date('Y-m-d H:i:s', $empout));

                        $brkout = strtotime($this->process_model->getawal($userdata, date('Y-m-d H:i:s', $startbreak), date('Y-m-d H:i:s', $endbreak)));
                        $brkin = strtotime($this->process_model->getakhir($userdata, date('Y-m-d H:i:s', $startbreak), date('Y-m-d H:i:s', $endbreak)));

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
                                if($attendances=='AT_AT6' || $attendances=='AT_AT3' || $attendances=='AT_AT1' || $attendances=='AT_SK01'|| $attendances=='AT_SK03') $late = 0;
                                else if($attendances=='AT_AT6' || $attendances=='AT_AT2' || $attendances=='AT_SK02') $early = 0;
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
                            if (($attendances == '' || $attendances==null) && $workinholiday==0) {
                                $attend = 'ALP';
                            } else if($attendances=='NWDS') {
                                $attend = 'NWK';
                            } else {
                                if($workinholiday==0) {
                                    $attend = $attendances;
                                }
                            }
                        }

                        /*if($emptype==2) {
                            $attend = $attendances;
                            $late = 0;
                            $early = 0;
                            $otbefore = 0;
                            $otafter = 0;
                        }*/

                        $this->db->select('userid');
                        $this->db->from('process');
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
                        } else
                        {
                            $this->db->where('userid', $userdata);
                            $this->db->where('date_shift', strtotime($tanggal));
                            $updatetemporary = array(
                                'shift_in' => date('H:i:s', $cekin),
                                'shift_out' => date('H:i:s', $cekout),
                                'date_in' => $datein, 'check_in' => $timein, 'break_out' => $brkout,
                                'break_in' => $brkin, 'date_out' => $dateout, 'check_out' => $timeout,
                                'late' => $late, 'early_departure' => $early, 'ot_before' => $otbefore,
                                'ot_after' => $otafter, 'workinholiday' => $workinholiday,
                                'attendance' => $attend, 'edit_come' => $editin, 'edit_home' => $editout,
                                'notes' => $notes != '' ? $notes : null, 'flaghol' => $flagholiday);
                            $this->db->update('process', $updatetemporary);
                        }


                    } else {
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
                                'break_out' => null,
                                'break_in' => null,
                                'date_out' => null,
                                'check_out' => null,
                                'late' => null,
                                'early_departure' => null,
                                'ot_before' => null,
                                'ot_after' => null,
                                'workinholiday' => $workinholiday,
                                'attendance' => 'BLNK',
                                'edit_come' => null,
                                'edit_home' => null,
                                'notes' => null,
                                'flaghol' => $flagholiday
                            );
                            $this->db->insert('process', $savetemporary);
                        } else {
                            $this->db->where('userid', $userdata);
                            $this->db->where('date_shift', strtotime($tanggal));
                            $updatetemporary = array(
                                'shift_in' => null, 'shift_out' => null, 'date_in' => null,
                                'check_in' => null, 'break_out' => null, 'break_in' => null,
                                'date_out' => null, 'check_out' => null, 'late' => null,
                                'early_departure' => null, 'ot_before' => null, 'ot_after' => null,
                                'workinholiday' => $workinholiday, 'attendance' => 'BLNK',
                                'edit_come' => null, 'edit_home' => null, 'notes' => null,
                                'flaghol' => $flagholiday);
                            $this->db->update('process', $updatetemporary);
                        }
                    }
                }
            }

           /* $actionlog = array(
                'user'			=> $this->session->userdata('s_username'),
                'ipadd'			=> getRealIpAddr(),
                'logtime'		=> date("Y-m-d H:i:s"),
                'logdetail'		=> 'Process by employee userid = '.substr($this->input->get('userid'),0,-1).'. periode = '.$this->input->get('startdate').' - '.$this->input->get('enddate'),
                'info'			=> "Success"
            );
            $this->db->insert('goltca', $actionlog);*/

            $data['msg'] = 'Data sudah diproses..';
        } else {
            $data['msg'] = 'Periode sudah ditutup..';
        }

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
            $orgid = $this->pegawai->deptonall($orgidi);
            $range = ($dateend - $datestart) / 86400;
            $shift = $this->process_model->getshiftorgid($orgid, $datestart, $dateend);
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
            if (count($useraidi)>1) {
                $this->db->where_in('userid', $useraidi);
                $this->db->where('date_shift >=', date('Y-m-d', $datestart));
                $this->db->where('date_shift <=', date('Y-m-d', $dateend));
                $this->db->delete('process');

                $otsetting = $this->process_model->cekotsetting();
                foreach ($otsetting->result() as $otset)
                    $otseting[$otset->field_id] = $otset->field_value;

                foreach ($useraidi as $userdata) {
                    for ($x = 0; $x <= $range; $x++) {
                        $tanggal = date('Y-m-d', $datestart + ($x * 86400));
                        $workinholiday = 0;
                        $xx = 0;
                        $woh = 0;
                        $flagholiday = 0;

                        if (isset($holarray[$deptid[$userdata]][date('Y-m-d', strtotime($tanggal))]) || isset($holarray['1'][date('Y-m-d', strtotime($tanggal))])) $workinholiday = 1;
                        if (isset($flaghol[$deptid[$userdata]][date('Y-m-d', strtotime($tanggal))]) || isset($flaghol['1'][date('Y-m-d', strtotime($tanggal))])) $flagholiday = 1;

                        if (isset($arrai[$userdata][strtotime($tanggal)])) {

                            $tanggalsci = $tanggal;
                            $tanggalci = $tanggal;
                            $tanggaleci = $tanggal;
                            $tanggalsb = $tanggal;
                            $tanggalbo = $tanggal;
                            $tanggalbi = $tanggal;
                            $tanggaleb = $tanggal;
                            $tanggalsco = $tanggal;
                            $tanggalco = $tanggal;
                            $tanggaleco = $tanggal;

                            if ($arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'PMD' || $arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'S1C') {
                                $tanggalsco = date('Y-m-d', strtotime($tanggal) + 86400);
                                $tanggalco = date('Y-m-d', strtotime($tanggal) + 86400);
                                $tanggaleco = date('Y-m-d', strtotime($tanggal) + 86400);
                            } else {
                                if ($arrai[$userdata][strtotime($tanggal)]['check_in'] < $arrai[$userdata][strtotime($tanggal)]['start_in'])
                                    $tanggalsci = date('Y-m-d', strtotime($tanggal) - 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_in'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                    $tanggaleci = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_break'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                    $tanggalsb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_out'] < $arrai[$userdata][strtotime($tanggal)]['start_break'])
                                    $tanggalbo = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_in'] < $arrai[$userdata][strtotime($tanggal)]['break_out'])
                                    $tanggalbi = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_break'] < $arrai[$userdata][strtotime($tanggal)]['break_in'])
                                    $tanggaleb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in'])
                                    $tanggalsco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['check_out'] < $arrai[$userdata][strtotime($tanggal)]['start_out'])
                                    $tanggalco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_out'] < $arrai[$userdata][strtotime($tanggal)]['check_out'])
                                    $tanggaleco = date('Y-m-d', strtotime($tanggal) + 86400);
                            }

                            /*$startcheckin = strtotime($tanggalsci . " " . $arrai[$userdata][strtotime($tanggal)]['start_in']);
                            $cekin = strtotime($tanggalci . " " . $arrai[$userdata][strtotime($tanggal)]['check_in']);
                            $endcheckin = strtotime($tanggaleci . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_in']);

                            $startcheckout = strtotime($tanggalsco . " " . $arrai[$userdata][strtotime($tanggal)]['start_out']);
                            $cekout = strtotime($tanggalco . " " . $arrai[$userdata][strtotime($tanggal)]['check_out']);
                            $endcheckout = strtotime($tanggaleco . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_out']);

                            $startbreak = strtotime($tanggalsb . " " . $arrai[$userdata][strtotime($tanggal)]['start_break']);
                            $breakout = strtotime($tanggalbo . " " . $arrai[$userdata][strtotime($tanggal)]['break_out']);
                            $breakin = strtotime($tanggalbi . " " . $arrai[$userdata][strtotime($tanggal)]['break_in']);
                            $endbreak = strtotime($tanggaleb . " " . $arrai[$userdata][strtotime($tanggal)]['end_break']);*/

                            $startcheckin = strtotime($tanggalsci . " " . $arrai[$userdata][strtotime($tanggal)]['start_in']);
                            $cekin = strtotime($tanggalci . " " . $arrai[$userdata][strtotime($tanggal)]['check_in']);
                            $endcheckin = strtotime($tanggaleci . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_in']);

                            $startcheckout = strtotime($tanggalsco . " " . $arrai[$userdata][strtotime($tanggal)]['start_out'].":59");
                            $cekout = strtotime($tanggalco . " " . $arrai[$userdata][strtotime($tanggal)]['check_out'].":59");
                            $endcheckout = strtotime($tanggaleco . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_out'].":59");

                            $startbreak = strtotime($tanggalsb . " " . $arrai[$userdata][strtotime($tanggal)]['start_break']);
                            $breakout = strtotime($tanggalbo . " " . $arrai[$userdata][strtotime($tanggal)]['break_out'].":59");
                            $breakin = strtotime($tanggalbi . " " . $arrai[$userdata][strtotime($tanggal)]['break_in']);
                            $endbreak = strtotime($tanggaleb . " " . $arrai[$userdata][strtotime($tanggal)]['end_break'].":59");

                            $latetolerance = $arrai[$userdata][strtotime($tanggal)]['late_tolerance'];
                            $earlydeparture = $arrai[$userdata][strtotime($tanggal)]['early_departure'];
                            $shiftin = $arrai[$userdata][strtotime($tanggal)]['shift_in'];
                            $shiftout = $arrai[$userdata][strtotime($tanggal)]['shift_out'];
                            $ottol = $arrai[$userdata][strtotime($tanggal)]['ot_tolerance'];
                            $ottolin = $arrai[$userdata][strtotime($tanggal)]['in_ot_tolerance'];
                            $ottolout = $arrai[$userdata][strtotime($tanggal)]['out_ot_tolerance'];
                            $attendances = $arrai[$userdata][strtotime($tanggal)]['attendance'];
                            $absences = $arrai[$userdata][strtotime($tanggal)]['absence'];
                            $notes = $arrai[$userdata][strtotime($tanggal)]['notes'];
                            $emptype = $arrai[$userdata][strtotime($tanggal)]['emptype'];

                            if ($absences == 'OFF' || $absences == 'OFFPD') $attendances = 'NWDS';

                            if ($arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'OFFPD') {
                                $empin = strtotime(false);
                                $empout = strtotime(false);
                            } else {
                                $empin = strtotime($this->process_model->getawal($userdata, date('Y-m-d H:i:s', $startcheckin), date('Y-m-d H:i:s', $endcheckin)));
                                $empout = strtotime($this->process_model->getakhir($userdata, date('Y-m-d H:i:s', $startcheckout), date('Y-m-d H:i:s', $endcheckout)));
                            }

                            $editin = $this->process_model->getedit($userdata, date('Y-m-d H:i:s', $empin));
                            $editout = $this->process_model->getedit($userdata, date('Y-m-d H:i:s', $empout));

                            $brkout = strtotime($this->process_model->getawal($userdata, date('Y-m-d H:i:s', $startbreak), date('Y-m-d H:i:s', $endbreak)));
                            $brkin = strtotime($this->process_model->getakhir($userdata, date('Y-m-d H:i:s', $startbreak), date('Y-m-d H:i:s', $endbreak)));

                            $fixcekin = 0;
                            $otcekin = 0;
                            $fixcekout = 0;
                            $otcekout = 0;
                            $late = 0;
                            $early = 0;
                            $otbefore = 0;
                            $otafter = 0;

                            $fixcekin = $cekin + ($latetolerance * 60);
                            if ($empin > $fixcekin) {
                                if ($shiftin == 0) {
                                    $late = $empin - $cekin;
                                } else {
                                    $late = $empin - $fixcekin;
                                }
                            } else {
                                $late = 0;
                            }

                            $fixcekout = $cekout - ($earlydeparture * 60);
                            if ($empout < $fixcekout) {
                                if ($shiftout == 0) {
                                    $early = $cekout - $empout;
                                } else {
                                    $early = $fixcekout - $empout;
                                }
                            } else {
                                $early = 0;
                            }

                            $otcekin = $cekin - ($ottol * 60);
                            if ($empin < $otcekin) {
                                if ($ottolin == 0) {
                                    $otbefore = $cekin - $empin;
                                } else {
                                    $otbefore = $otcekin - $empin;
                                }
                            } else {
                                $otbefore = 0;
                            }

                            $otcekout = $cekout + ($ottol * 60);
                            if ($empout > $otcekout) {
                                if ($ottolout == 0) {
                                    $otafter = $empout - $cekout;
                                } else {
                                    $otafter = $empout - $otcekout;
                                }
                            } else {
                                $otafter = 0;
                            }

                            if ($empin == 0) {
                                $datein = null;
                                $timein = null;
                                $late = 0;
                                $otbefore = 0;
                            } else {
                                $datein = date('Y-m-d', $empin);
                                $timein = date('H:i:s', $empin);
                                $xx = 1;
                            }

                            if ($empout == 0) {
                                $dateout = null;
                                $timeout = null;
                                $early = 0;
                                $otafter = 0;
                            } else {
                                $dateout = date('Y-m-d', $empout);
                                $timeout = date('H:i:s', $empout);
                                $xx = 1;
                            }

                            $brkout == 0 ? $brkout = null : $brkout = date('H:i:s', $brkout);
                            $brkin == 0 ? $brkin = null : $brkin = date('H:i:s', $brkin);

                            if ($otseting[1] == 0) $otbefore = 0;
                            if ($otseting[2] == 0) $otafter = 0;

                            if ($attendances == 'NWDS') {
                                $xx == 1 ? $woh = 2 : $woh = 0;
                                $workinholiday == 1 ? $workinholiday = 1 : $workinholiday = $woh;
                                $late = 0;
                                $early = 0;
                            }

                            if (strpos($attendances, 'AB_') !== false) {
                                $late = 0;
                                $early = 0;
                                $otbefore = 0;
                                $otafter = 0;
                            }

                            /*if ($attendances != 'AT_') {
                                if (strpos($attendances, 'AT_') !== false) {
                                    if ($attendances == 'AT_AT4' || $attendances == 'AT_AT3' || $attendances == 'AT_AT1') $late = 0;
                                    else if ($attendances == 'AT_AT5' || $attendances == 'AT_AT2') $early = 0;
                                    else {
                                        $late = 0;
                                        $early = 0;
                                    }
                                }
                            }*/
                            if($attendances != 'AT_') {
                                if(strpos($attendances,'AT_')!==false) {
                                    if($attendances=='AT_AT6' || $attendances=='AT_AT3' || $attendances=='AT_AT1' || $attendances=='AT_SK01'|| $attendances=='AT_SK03') $late = 0;
                                    else if($attendances=='AT_AT6' || $attendances=='AT_AT2' || $attendances=='AT_SK02') $early = 0;
                                    else {
                                        $late = 0;
                                        $early = 0;
                                    }
                                }
                            }
                            $attend = '';
                            if ($empin != 0 or $empout != 0) {
                                if ($workinholiday == 0) {
                                    $attend = $attendances;
                                }
                            } else {
                                if (($attendances == '' || $attendances==null) && $workinholiday==0) {
                                    $attend = 'ALP';
                                } else if ($attendances == 'NWDS') {
                                    $attend = 'NWK';
                                } else {
                                    if ($workinholiday == 0) {
                                        $attend = $attendances;
                                    }
                                }
                            }

                            /*if ($emptype == 2) {
                                $attend = $attendances;
                                $late = 0;
                                $early = 0;
                                $otbefore = 0;
                                $otafter = 0;
                            }*/

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
                                    'shift_in' => date('H:i:s', $cekin),
                                    'shift_out' => date('H:i:s', $cekout),
                                    'date_in' => $datein,
                                    'check_in' => $timein,
                                    'break_out' => $brkout,
                                    'break_in' => $brkin,
                                    'date_out' => $dateout,
                                    'check_out' => $timeout,
                                    'late' => $late,
                                    'early_departure' => $early,
                                    'ot_before' => $otbefore,
                                    'ot_after' => $otafter,
                                    'workinholiday' => $workinholiday,
                                    'attendance' => $attend,
                                    'edit_come' => $editin,
                                    'edit_home' => $editout,
                                    'notes' => $notes != '' ? $notes : null,
                                    'flaghol' => $flagholiday
                                );
                                $this->db->insert('process', $savetemporary);
                            } else {

                            }

                        } else {
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
                                    'break_out' => null,
                                    'break_in' => null,
                                    'date_out' => null,
                                    'check_out' => null,
                                    'late' => null,
                                    'early_departure' => null,
                                    'ot_before' => null,
                                    'ot_after' => null,
                                    'workinholiday' => $workinholiday,
                                    'attendance' => 'BLNK',
                                    'edit_come' => null,
                                    'edit_home' => null,
                                    'notes' => null,
                                    'flaghol' => $flagholiday
                                );
                                $this->db->insert('process', $savetemporary);
                            }
                            else {
                                $this->db->where('userid', $userdata);
                                $this->db->where('date_shift', strtotime($tanggal));
                                $updatetemporary = array(
                                    'shift_in' => null, 'shift_out' => null, 'date_in' => null,
                                    'check_in' => null, 'break_out' => null, 'break_in' => null,
                                    'date_out' => null, 'check_out' => null, 'late' => null,
                                    'early_departure' => null, 'ot_before' => null, 'ot_after' => null,
                                    'workinholiday' => $workinholiday, 'attendance' => 'BLNK',
                                    'edit_come' => null, 'edit_home' => null, 'notes' => null,
                                    'flaghol' => $flagholiday);
                                $this->db->update('process', $updatetemporary);
                            }
                        }
                    }
                }
                /*$actionlog = array(
                    'user'			=> $this->my_usession->userdata('username'),
                    'ipadd'			=> $this->ipaddress->get_ip(),
                    'logtime'		=> date("Y-m-d H:i:s"),
                    'logdetail'		=> 'Process all organization = '.substr($this->input->get('userid'),0,-1).'. periode = '.$this->input->get('startdate').' - '.$this->input->get('enddate'),
                    'info'			=> $this->lang->line('message_success')
                );
                $this->db->insert('goltca', $actionlog);*/
            }
            $data['msg'] = 'Data sudah diproses..';
        } else {
            $data['msg'] = 'Periode sudah ditutup..';
        }

        $data['status'] = 'succes';
        echo json_encode($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */