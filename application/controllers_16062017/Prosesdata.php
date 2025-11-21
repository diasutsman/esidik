<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Prosesdata extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('utility');
        $this->load->model('utils_model', 'utils');
        $this->load->model('pegawai/pegawai_model', 'pegawai');
        $this->load->model('process_model');
		$this->load->model('report_model');
    }

    function deptonprocess($orgid)
    {
        $depart = array();
        $deptid = $orgid;
        $i = 0;
        $depa = $this->pegawai->getdept($deptid);
        do {
            $deptid = array();
            foreach ($depa->result() as $dep) {
                $deptid[] = $dep->deptid;
                $depart[] = $dep->deptid;
            }
            $this->pegawai->adachild($deptid) ? $i = 1 : $i = 0;
            $depa = $this->pegawai->getdeptparent($deptid);
        } while ($i == 1);
        return $depart;
    }

    public function doprosesdata($orgidp = "undefined", $startdate = null, $enddate = null, $stspeg = null)
    {
        $orgidi = $orgidp == 'undefined' ? '1' : $orgidp;

        $tgl1 = $startdate;
        $tgl2 = $enddate;

        if (empty($tgl1)) {
            $tgl = strtotime("-1 day");
            $tgl1 = date('Y-m-d', $tgl);
            $tgl2 = date('Y-m-d');
        }

        $datestart = strtotime($tgl1);
        $dateend = strtotime($tgl2);

        ini_set("pcre.backtrack_limit", PHP_INT_MAX);
        ini_set("pcre.recursion_limit", PHP_INT_MAX);

        $orgid = $this->deptonprocess($orgidi);
        $range = ($dateend - $datestart) / 86400;

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

        if ($bukatutup) {
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');

            $shift = $this->process_model->getshiftorgid($orgid, $datestart, $dateend, $stspeg);
            $arrai = array();
            foreach ($shift as $shifting) {
                $arrai[$shifting->userid][strtotime($shifting->rosterdate)] = array('start_in' => $shifting->start_in, 'check_in' => $shifting->check_in, 'end_check_in' => $shifting->end_check_in, 'start_out' => $shifting->start_out, 'check_out' => $shifting->check_out, 'end_check_out' => $shifting->end_check_out, 'start_break' => $shifting->start_break, 'break_out' => $shifting->break_out, 'break_in' => $shifting->break_in, 'end_break' => $shifting->end_break, 'late_tolerance' => $shifting->late_tolerance, 'early_departure' => $shifting->early_departure, 'shift_in' => $shifting->shift_in, 'shift_out' => $shifting->shift_out, 'ot_tolerance' => $shifting->ot_tolerance, 'in_ot_tolerance' => $shifting->in_ot_tolerance, 'out_ot_tolerance' => $shifting->out_ot_tolerance, 'attendance' => $shifting->attendance, 'absence' => $shifting->absence, 'notes' => $shifting->notes, 'emptype' => $shifting->emptype, 'codeshift' => $shifting->code_shift);
            }

            $holiday = $this->process_model->cekholiday($datestart, $dateend);
            $holarray = array();
            $flaghol = array();
            foreach ($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if ($selisih == 0) {
                    $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                    $flaghol[$hol->deptid][$hol->startdate] = $hol->flag;
                } else {
                    $jarak = $selisih / 86400;
                    for ($k = 0; $k <= $jarak; $k++) {
                        $holarray[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->info;
                        $flaghol[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->flag;
                    }
                }
            }
            $useraidi = array();
            $user = $this->process_model->getuserbyorg($orgid);
            foreach ($user->result() as $userya) {
                $useraidi[] = $userya->userid;
                $deptid[$userya->userid] = $userya->deptid;
            }

            if (count($useraidi) > 0) {
                $this->db->where_in('userid', $useraidi);
                $this->db->where('date_shift >=', date('Y-m-d', $datestart));
                $this->db->where('date_shift <=', date('Y-m-d', $dateend));
                $this->db->delete('process');

                $otsetting = $this->process_model->cekotsetting();
                foreach ($otsetting->result() as $otset) $otseting[$otset->field_id] = $otset->field_value;

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
                                if ($arrai[$userdata][strtotime($tanggal)]['check_in'] < $arrai[$userdata][strtotime($tanggal)]['start_in']) $tanggalsci = date('Y-m-d', strtotime($tanggal) - 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_in'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggaleci = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_break'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_out'] < $arrai[$userdata][strtotime($tanggal)]['start_break']) $tanggalbo = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_in'] < $arrai[$userdata][strtotime($tanggal)]['break_out']) $tanggalbi = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_break'] < $arrai[$userdata][strtotime($tanggal)]['break_in']) $tanggaleb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['check_out'] < $arrai[$userdata][strtotime($tanggal)]['start_out']) $tanggalco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_out'] < $arrai[$userdata][strtotime($tanggal)]['check_out']) $tanggaleco = date('Y-m-d', strtotime($tanggal) + 86400);
                            }

                            $startcheckin = strtotime($tanggalsci . " " . $arrai[$userdata][strtotime($tanggal)]['start_in']);
                            $cekin = strtotime($tanggalci . " " . $arrai[$userdata][strtotime($tanggal)]['check_in']);
                            $endcheckin = strtotime($tanggaleci . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_in']);

                            $startcheckout = strtotime($tanggalsco . " " . $arrai[$userdata][strtotime($tanggal)]['start_out']);
                            $cekout = strtotime($tanggalco . " " . $arrai[$userdata][strtotime($tanggal)]['check_out']);
                            $endcheckout = strtotime($tanggaleco . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_out']);

                            $startbreak = strtotime($tanggalsb . " " . $arrai[$userdata][strtotime($tanggal)]['start_break']);
                            $breakout = strtotime($tanggalbo . " " . $arrai[$userdata][strtotime($tanggal)]['break_out']);
                            $breakin = strtotime($tanggalbi . " " . $arrai[$userdata][strtotime($tanggal)]['break_in']);
                            $endbreak = strtotime($tanggaleb . " " . $arrai[$userdata][strtotime($tanggal)]['end_break']);

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

                            /*if($arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'PMD' || $arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'S1C') {
                                $empin = strtotime($this->process_model->getawalpmd($userdata, date('Y-m-d H:i:s', $startcheckin), date('Y-m-d H:i:s', $endcheckin)));
                                $empout = strtotime($this->process_model->getakhirpmd($userdata, date('Y-m-d H:i:s', $startcheckout), date('Y-m-d H:i:s', $endcheckout)));
                            } else */
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

                            if ($attendances != 'AT_') {
                                if (strpos($attendances, 'AT_') !== false) {
                                    /*if ($attendances == 'AT_AT4' || $attendances == 'AT_AT3' || $attendances == 'AT_AT1') $late = 0; else if ($attendances == 'AT_AT5' || $attendances == 'AT_AT2') $early = 0; else {
                                        $late = 0;
                                        $early = 0;
                                    }*/
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

                            $savetemporary = array('userid' => $userdata, 'date_shift' => date('Y-m-d', strtotime($tanggal)), 'shift_in' => date('H:i:s', $cekin), 'shift_out' => date('H:i:s', $cekout), 'date_in' => $datein, 'check_in' => $timein, 'break_out' => $brkout, 'break_in' => $brkin, 'date_out' => $dateout, 'check_out' => $timeout, 'late' => $late, 'early_departure' => $early, 'ot_before' => $otbefore, 'ot_after' => $otafter, 'workinholiday' => $workinholiday, 'attendance' => $attend, 'edit_come' => $editin, 'edit_home' => $editout, 'notes' => $notes != '' ? $notes : null, 'flaghol' => $flagholiday);
                            $this->db->insert('process', $savetemporary);
                        } else {
                            $savetemporary = array('userid' => $userdata, 'date_shift' => date('Y-m-d', strtotime($tanggal)), 'shift_in' => null, 'shift_out' => null, 'date_in' => null, 'check_in' => null, 'break_out' => null, 'break_in' => null, 'date_out' => null, 'check_out' => null, 'late' => null, 'early_departure' => null, 'ot_before' => null, 'ot_after' => null, 'workinholiday' => $workinholiday, 'attendance' => 'BLNK', 'edit_come' => null, 'edit_home' => null, 'notes' => null, 'flaghol' => $flagholiday);
                            $this->db->insert('process', $savetemporary);
                        }
                    }
                }
                $actionlog = array(
                    'user' => "UserOto",
                    'ipadd' => getRealIpAddr(),
                    'logtime' => date("Y-m-d H:i:s"),
                    'logdetail' => 'Process all data',
                    'info' => "Sukses"
                );
                $this->db->insert('goltca', $actionlog);
            }

            //push buat display data
            //$this->db->query("CALL sp_push_display_by_date('$startdate','$enddate')");

            $data['msg'] = 'Data berhasil diproses..';
            $data['status'] = 'succes';

            echo json_encode($data);
        } else {
            $data['msg'] = 'Periode sudah ditutup..';
            $data['status'] = 'error';

            echo json_encode($data);
        }

    }

    public function doprosesdataws()
    {
        //$orgidi = $this->input->post("orgidp") == 'undefined' ? '1' : $this->input->post("orgidp");

        $tgl1 = $this->input->post("startdate");
        $tgl2 = $this->input->post("enddate");

        if (empty($tgl1)) {
            $tgl = strtotime("-1 day");
            $tgl1 = date('Y-m-d', $tgl);
            $tgl2 = date('Y-m-d');
        }

        $datestart = strtotime($tgl1);
        $dateend = strtotime($tgl2);
        //$stspeg = $this->input->post("stspeg");
        $stspeg = array('1', '2');// explode(",",$stspeg);
        //$orgid = $this->deptonprocess($orgidi);
        $range = ($dateend - $datestart) / 86400;

        $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $datestart));
        $this->db->where('tahun', date('Y', $datestart));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $bukatutup = $query->row()->status;
        } else {
            $bukatutup = FALSE;
        }

        if ($bukatutup) {
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');
            $shift = $this->process_model->getshiftorgidall($datestart, $dateend, $stspeg);
            $arrai = array();
            foreach ($shift as $shifting) {
                $arrai[$shifting->userid][strtotime($shifting->rosterdate)] =
                    array('start_in' => $shifting->start_in, 'check_in' => $shifting->check_in,
                        'end_check_in' => $shifting->end_check_in, 'start_out' => $shifting->start_out,
                        'check_out' => $shifting->check_out, 'end_check_out' => $shifting->end_check_out,
                        'start_break' => $shifting->start_break, 'break_out' => $shifting->break_out,
                        'break_in' => $shifting->break_in, 'end_break' => $shifting->end_break,
                        'late_tolerance' => $shifting->late_tolerance,
                        'early_departure' => $shifting->early_departure,
                        'shift_in' => $shifting->shift_in, 'shift_out' => $shifting->shift_out,
                        'ot_tolerance' => $shifting->ot_tolerance,
                        'in_ot_tolerance' => $shifting->in_ot_tolerance,
                        'out_ot_tolerance' => $shifting->out_ot_tolerance,
                        'attendance' => $shifting->attendance,
                        'absence' => $shifting->absence, 'notes' => $shifting->notes,
                        'emptype' => $shifting->emptype,
                        'codeshift' => $shifting->code_shift);
            }

            $holiday = $this->process_model->cekholiday($datestart, $dateend);
            $holarray = array();
            $flaghol = array();
            foreach ($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if ($selisih == 0) {
                    $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                    $flaghol[$hol->deptid][$hol->startdate] = $hol->flag;
                } else {
                    $jarak = $selisih / 86400;
                    for ($k = 0; $k <= $jarak; $k++) {
                        $holarray[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->info;
                        $flaghol[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->flag;
                    }
                }
            }
            $useraidi = array();
            $user = $this->process_model->getuserall($stspeg);
            foreach ($user->result() as $userya) {
                $useraidi[] = $userya->userid;
                $deptid[$userya->userid] = $userya->deptid;
            }

            if (count($useraidi) > 0) {
                $user_or = array();
                foreach ($useraidi as $xids) {
                    $user_or[] = "userid = $xids";
                }

                $this->db->where('date_shift >=', date('Y-m-d', $datestart));
                $this->db->where('date_shift <=', date('Y-m-d', $dateend));
                $this->db->where("(" . implode(' OR ', $user_or) . ")");
                //$this->db->where_in("userid",$useraidi);
                $query = $this->db->get("process");
                $datas = $query->row_array();
                log_history("edit", "process", $datas);

                /*$this->db->where('date_shift >=', date('Y-m-d', $datestart));
                $this->db->where('date_shift <=', date('Y-m-d', $dateend));
                $this->db->where("(".implode(' OR ',$user_or).")");
                //$this->db->where_in("userid",$useraidi);
                $this->db->delete('process');*/

                $otsetting = $this->process_model->cekotsetting();
                foreach ($otsetting->result() as $otset) $otseting[$otset->field_id] = $otset->field_value;

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
                                if ($arrai[$userdata][strtotime($tanggal)]['check_in'] < $arrai[$userdata][strtotime($tanggal)]['start_in']) $tanggalsci = date('Y-m-d', strtotime($tanggal) - 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_in'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggaleci = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_break'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_out'] < $arrai[$userdata][strtotime($tanggal)]['start_break']) $tanggalbo = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_in'] < $arrai[$userdata][strtotime($tanggal)]['break_out']) $tanggalbi = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_break'] < $arrai[$userdata][strtotime($tanggal)]['break_in']) $tanggaleb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['check_out'] < $arrai[$userdata][strtotime($tanggal)]['start_out']) $tanggalco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_out'] < $arrai[$userdata][strtotime($tanggal)]['check_out']) $tanggaleco = date('Y-m-d', strtotime($tanggal) + 86400);
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

                            /*if($arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'PMD' || $arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'S1C') {
                                $empin = strtotime($this->process_model->getawalpmd($userdata, date('Y-m-d H:i:s', $startcheckin), date('Y-m-d H:i:s', $endcheckin)));
                                $empout = strtotime($this->process_model->getakhirpmd($userdata, date('Y-m-d H:i:s', $startcheckout), date('Y-m-d H:i:s', $endcheckout)));
                            } else */
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

                            if ($attendances != 'AT_') {
                                if (strpos($attendances, 'AT_') !== false) {
                                    if ($attendances == 'AT_AT6' || $attendances == 'AT_AT3' || $attendances == 'AT_AT1' || $attendances == 'AT_SK01' || $attendances == 'AT_SK03') $late = 0;
                                    else if ($attendances == 'AT_AT6' || $attendances == 'AT_AT2' || $attendances == 'AT_SK02') $early = 0;
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
                                $savetemporary = array('userid' => $userdata,
                                    'date_shift' => date('Y-m-d', strtotime($tanggal)),
                                    'shift_in' => date('H:i:s', $cekin),
                                    'shift_out' => date('H:i:s', $cekout),
                                    'date_in' => $datein, 'check_in' => $timein, 'break_out' => $brkout,
                                    'break_in' => $brkin, 'date_out' => $dateout, 'check_out' => $timeout,
                                    'late' => $late, 'early_departure' => $early, 'ot_before' => $otbefore,
                                    'ot_after' => $otafter, 'workinholiday' => $workinholiday,
                                    'attendance' => $attend, 'edit_come' => $editin, 'edit_home' => $editout,
                                    'notes' => $notes != '' ? $notes : null, 'flaghol' => $flagholiday);
                                $this->db->insert('process', $savetemporary);
                            } else {
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
                                $savetemporary = array('userid' => $userdata,
                                    'date_shift' => date('Y-m-d', strtotime($tanggal)),
                                    'shift_in' => null, 'shift_out' => null, 'date_in' => null,
                                    'check_in' => null, 'break_out' => null, 'break_in' => null,
                                    'date_out' => null, 'check_out' => null, 'late' => null,
                                    'early_departure' => null, 'ot_before' => null, 'ot_after' => null,
                                    'workinholiday' => $workinholiday, 'attendance' => 'BLNK',
                                    'edit_come' => null, 'edit_home' => null, 'notes' => null,
                                    'flaghol' => $flagholiday);
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
                $actionlog = array(
                    'user' => "UserOto",
                    'ipadd' => getRealIpAddr(),
                    'logtime' => date("Y-m-d H:i:s"),
                    'logdetail' => 'Process all data',
                    'info' => "Sukses"
                );
                $this->db->insert('goltca', $actionlog);

                //$this->db->query("CALL sp_push_display_by_date('$startdate','$enddate')");

                $this->doprocuangmakan($datestart, $dateend);

                $data['msg'] = 'Data berhasil diproses..';
                $data['status'] = 'succes';
            } else {
                $data['msg'] = 'Tidak ada data pegawai yang diproses..';
                $data['status'] = 'error';
            }

			createLog("Proses prosensi otomatis","procdata");
            echo json_encode($data);
        } else {
            $data['msg'] = 'Periode sudah ditutup..';
            $data['status'] = 'error';
            echo json_encode($data);
        }


    }

    public function doprosesdatauser($iduser = null, $startdate = null, $enddate = null)
    {

        ini_set("pcre.backtrack_limit", PHP_INT_MAX);
        ini_set("pcre.recursion_limit", PHP_INT_MAX);
        if ($iduser == null) {
            $iduser = array();
            $rsl = $this->pegawai->getallemployeeonlyuserid();
            foreach ($rsl->result_array() as $row) {
                $iduser[] = $row['userid']; //add the fetched result to the result array;
            }
        }

        $tgl1 = $startdate;
        $tgl2 = $enddate;

        if (empty($tgl1)) {
            $tgl = strtotime("-1 day");
            $tgl1 = date('Y-m-d', $tgl);
            $tgl2 = date('Y-m-d');
        }

        $datestart = strtotime($tgl1);
        $dateend = strtotime($tgl2);

        //$orgid = $this->deptonprocess($orgidi);
        $range = ($dateend - $datestart) / 86400;

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

        if ($bukatutup) {

            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');

            $shift = $this->process_model->getshiftuserid($iduser, $datestart, $dateend);
            $arrai = array();
            foreach ($shift as $shifting) {
                $arrai[$shifting->userid][strtotime($shifting->rosterdate)] = array('start_in' => $shifting->start_in, 'check_in' => $shifting->check_in, 'end_check_in' => $shifting->end_check_in, 'start_out' => $shifting->start_out, 'check_out' => $shifting->check_out, 'end_check_out' => $shifting->end_check_out, 'start_break' => $shifting->start_break, 'break_out' => $shifting->break_out, 'break_in' => $shifting->break_in, 'end_break' => $shifting->end_break, 'late_tolerance' => $shifting->late_tolerance, 'early_departure' => $shifting->early_departure, 'shift_in' => $shifting->shift_in, 'shift_out' => $shifting->shift_out, 'ot_tolerance' => $shifting->ot_tolerance, 'in_ot_tolerance' => $shifting->in_ot_tolerance, 'out_ot_tolerance' => $shifting->out_ot_tolerance, 'attendance' => $shifting->attendance, 'absence' => $shifting->absence, 'notes' => $shifting->notes, 'emptype' => $shifting->emptype, 'codeshift' => $shifting->code_shift);
            }

            $holiday = $this->process_model->cekholiday($datestart, $dateend);
            $holarray = array();
            $flaghol = array();
            foreach ($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if ($selisih == 0) {
                    $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                    $flaghol[$hol->deptid][$hol->startdate] = $hol->flag;
                } else {
                    $jarak = $selisih / 86400;
                    for ($k = 0; $k <= $jarak; $k++) {
                        $holarray[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->info;
                        $flaghol[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->flag;
                    }
                }
            }
            $useraidi = array();
            $user = $this->process_model->getuserbyuser($iduser);
            foreach ($user->result() as $userya) {
                $useraidi[] = $userya->userid;
                $deptid[$userya->userid] = $userya->deptid;
            }

            if (count($useraidi) > 0) {

                $this->db->where_in('userid', $useraidi);
                $this->db->where('date_shift >=', date('Y-m-d', $datestart));
                $this->db->where('date_shift <=', date('Y-m-d', $dateend));
                $this->db->delete('process');

                $otsetting = $this->process_model->cekotsetting();
                foreach ($otsetting->result() as $otset) $otseting[$otset->field_id] = $otset->field_value;

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
                                if ($arrai[$userdata][strtotime($tanggal)]['check_in'] < $arrai[$userdata][strtotime($tanggal)]['start_in']) $tanggalsci = date('Y-m-d', strtotime($tanggal) - 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_in'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggaleci = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_break'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_out'] < $arrai[$userdata][strtotime($tanggal)]['start_break']) $tanggalbo = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_in'] < $arrai[$userdata][strtotime($tanggal)]['break_out']) $tanggalbi = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_break'] < $arrai[$userdata][strtotime($tanggal)]['break_in']) $tanggaleb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['check_out'] < $arrai[$userdata][strtotime($tanggal)]['start_out']) $tanggalco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_out'] < $arrai[$userdata][strtotime($tanggal)]['check_out']) $tanggaleco = date('Y-m-d', strtotime($tanggal) + 86400);
                            }

                            $startcheckin = strtotime($tanggalsci . " " . $arrai[$userdata][strtotime($tanggal)]['start_in']);
                            $cekin = strtotime($tanggalci . " " . $arrai[$userdata][strtotime($tanggal)]['check_in']);
                            $endcheckin = strtotime($tanggaleci . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_in']);

                            $startcheckout = strtotime($tanggalsco . " " . $arrai[$userdata][strtotime($tanggal)]['start_out']);
                            $cekout = strtotime($tanggalco . " " . $arrai[$userdata][strtotime($tanggal)]['check_out']);
                            $endcheckout = strtotime($tanggaleco . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_out']);

                            $startbreak = strtotime($tanggalsb . " " . $arrai[$userdata][strtotime($tanggal)]['start_break']);
                            $breakout = strtotime($tanggalbo . " " . $arrai[$userdata][strtotime($tanggal)]['break_out']);
                            $breakin = strtotime($tanggalbi . " " . $arrai[$userdata][strtotime($tanggal)]['break_in']);
                            $endbreak = strtotime($tanggaleb . " " . $arrai[$userdata][strtotime($tanggal)]['end_break']);

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

                            /*if($arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'PMD' || $arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'S1C') {
                                $empin = strtotime($this->process_model->getawalpmd($userdata, date('Y-m-d H:i:s', $startcheckin), date('Y-m-d H:i:s', $endcheckin)));
                                $empout = strtotime($this->process_model->getakhirpmd($userdata, date('Y-m-d H:i:s', $startcheckout), date('Y-m-d H:i:s', $endcheckout)));
                            } else */
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

                            if ($attendances != 'AT_') {
                                if (strpos($attendances, 'AT_') !== false) {
                                    /*if ($attendances == 'AT_AT4' || $attendances == 'AT_AT3' || $attendances == 'AT_AT1') $late = 0; else if ($attendances == 'AT_AT5' || $attendances == 'AT_AT2') $early = 0; else {
                                        $late = 0;
                                        $early = 0;
                                    }*/
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

                            $savetemporary = array('userid' => $userdata, 'date_shift' => date('Y-m-d', strtotime($tanggal)), 'shift_in' => date('H:i:s', $cekin), 'shift_out' => date('H:i:s', $cekout), 'date_in' => $datein, 'check_in' => $timein, 'break_out' => $brkout, 'break_in' => $brkin, 'date_out' => $dateout, 'check_out' => $timeout, 'late' => $late, 'early_departure' => $early, 'ot_before' => $otbefore, 'ot_after' => $otafter, 'workinholiday' => $workinholiday, 'attendance' => $attend, 'edit_come' => $editin, 'edit_home' => $editout, 'notes' => $notes != '' ? $notes : null, 'flaghol' => $flagholiday);
                            $this->db->insert('process', $savetemporary);
                        } else {
                            $savetemporary = array('userid' => $userdata, 'date_shift' => date('Y-m-d', strtotime($tanggal)), 'shift_in' => null, 'shift_out' => null, 'date_in' => null, 'check_in' => null, 'break_out' => null, 'break_in' => null, 'date_out' => null, 'check_out' => null, 'late' => null, 'early_departure' => null, 'ot_before' => null, 'ot_after' => null, 'workinholiday' => $workinholiday, 'attendance' => 'BLNK', 'edit_come' => null, 'edit_home' => null, 'notes' => null, 'flaghol' => $flagholiday);
                            $this->db->insert('process', $savetemporary);
                        }
                    }
                }
                $actionlog = array(
                    'user' => "UserOto",
                    'ipadd' => getRealIpAddr(),
                    'logtime' => date("Y-m-d H:i:s"),
                    'logdetail' => 'Process all data',
                    'info' => "Sukses"
                );
                $this->db->insert('goltca', $actionlog);

                //$this->db->query("CALL sp_push_display_by_date('$startdate','$enddate')");
            }
            $data['msg'] = 'Data berhasil diproses..';
            $data['status'] = 'succes';

            echo json_encode($data);
        } else {
            $data['msg'] = 'Periode sudah ditutup..';
            $data['status'] = 'error';

            echo json_encode($data);
        }

    }

    public function doprosesdatauserws()
    {
        ini_set("pcre.backtrack_limit", PHP_INT_MAX);
        ini_set("pcre.recursion_limit", PHP_INT_MAX);
        $iduser = $this->input->post("iduser");
        $startdate = $this->input->post("startdate");
        $enddate = $this->input->post("enddate");

        if ($iduser == null) {
            $iduser = array();
            $rsl = $this->pegawai->getallemployeeonlyuserid();
            foreach ($rsl->result_array() as $row) {
                $iduser[] = $row['userid']; //add the fetched result to the result array;
            }
        }

        $tgl1 = $startdate;
        $tgl2 = $enddate;

        if (empty($tgl1)) {
            $tgl = strtotime("-1 day");
            $tgl1 = date('Y-m-d', $tgl);
            $tgl2 = date('Y-m-d');
        }

        $datestart = strtotime($tgl1);
        $dateend = strtotime($tgl2);

        $range = ($dateend - $datestart) / 86400;

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

        if ($bukatutup) {

            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');

            $shift = $this->process_model->getshiftuserid($iduser, $datestart, $dateend);
            $arrai = array();
            foreach ($shift as $shifting) {
                $arrai[$shifting->userid][strtotime($shifting->rosterdate)] = array('start_in' => $shifting->start_in, 'check_in' => $shifting->check_in, 'end_check_in' => $shifting->end_check_in, 'start_out' => $shifting->start_out, 'check_out' => $shifting->check_out, 'end_check_out' => $shifting->end_check_out, 'start_break' => $shifting->start_break, 'break_out' => $shifting->break_out, 'break_in' => $shifting->break_in, 'end_break' => $shifting->end_break, 'late_tolerance' => $shifting->late_tolerance, 'early_departure' => $shifting->early_departure, 'shift_in' => $shifting->shift_in, 'shift_out' => $shifting->shift_out, 'ot_tolerance' => $shifting->ot_tolerance, 'in_ot_tolerance' => $shifting->in_ot_tolerance, 'out_ot_tolerance' => $shifting->out_ot_tolerance, 'attendance' => $shifting->attendance, 'absence' => $shifting->absence, 'notes' => $shifting->notes, 'emptype' => $shifting->emptype, 'codeshift' => $shifting->code_shift);
            }

            $holiday = $this->process_model->cekholiday($datestart, $dateend);
            $holarray = array();
            $flaghol = array();
            foreach ($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if ($selisih == 0) {
                    $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                    $flaghol[$hol->deptid][$hol->startdate] = $hol->flag;
                } else {
                    $jarak = $selisih / 86400;
                    for ($k = 0; $k <= $jarak; $k++) {
                        $holarray[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->info;
                        $flaghol[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->flag;
                    }
                }
            }
            $useraidi = array();
            $user = $this->process_model->getuserbyuser($iduser);
            foreach ($user->result() as $userya) {
                $useraidi[] = $userya->userid;
                $deptid[$userya->userid] = $userya->deptid;
            }

            if (count($useraidi) > 0) {
                $this->db->where_in('userid', $useraidi);
                $this->db->where('date_shift >=', date('Y-m-d', $datestart));
                $this->db->where('date_shift <=', date('Y-m-d', $dateend));
                $this->db->delete('process');

                $otsetting = $this->process_model->cekotsetting();
                foreach ($otsetting->result() as $otset) $otseting[$otset->field_id] = $otset->field_value;

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
                                if ($arrai[$userdata][strtotime($tanggal)]['check_in'] < $arrai[$userdata][strtotime($tanggal)]['start_in']) $tanggalsci = date('Y-m-d', strtotime($tanggal) - 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_in'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggaleci = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_break'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_out'] < $arrai[$userdata][strtotime($tanggal)]['start_break']) $tanggalbo = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_in'] < $arrai[$userdata][strtotime($tanggal)]['break_out']) $tanggalbi = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_break'] < $arrai[$userdata][strtotime($tanggal)]['break_in']) $tanggaleb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['check_out'] < $arrai[$userdata][strtotime($tanggal)]['start_out']) $tanggalco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_out'] < $arrai[$userdata][strtotime($tanggal)]['check_out']) $tanggaleco = date('Y-m-d', strtotime($tanggal) + 86400);
                            }

                            $startcheckin = strtotime($tanggalsci . " " . $arrai[$userdata][strtotime($tanggal)]['start_in']);
                            $cekin = strtotime($tanggalci . " " . $arrai[$userdata][strtotime($tanggal)]['check_in']);
                            $endcheckin = strtotime($tanggaleci . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_in']);

                            $startcheckout = strtotime($tanggalsco . " " . $arrai[$userdata][strtotime($tanggal)]['start_out']);
                            $cekout = strtotime($tanggalco . " " . $arrai[$userdata][strtotime($tanggal)]['check_out']);
                            $endcheckout = strtotime($tanggaleco . " " . $arrai[$userdata][strtotime($tanggal)]['end_check_out']);

                            $startbreak = strtotime($tanggalsb . " " . $arrai[$userdata][strtotime($tanggal)]['start_break']);
                            $breakout = strtotime($tanggalbo . " " . $arrai[$userdata][strtotime($tanggal)]['break_out']);
                            $breakin = strtotime($tanggalbi . " " . $arrai[$userdata][strtotime($tanggal)]['break_in']);
                            $endbreak = strtotime($tanggaleb . " " . $arrai[$userdata][strtotime($tanggal)]['end_break']);

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

                            /*if($arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'PMD' || $arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'S1C') {
                                $empin = strtotime($this->process_model->getawalpmd($userdata, date('Y-m-d H:i:s', $startcheckin), date('Y-m-d H:i:s', $endcheckin)));
                                $empout = strtotime($this->process_model->getakhirpmd($userdata, date('Y-m-d H:i:s', $startcheckout), date('Y-m-d H:i:s', $endcheckout)));
                            } else */
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

                            if ($attendances != 'AT_') {
                                if (strpos($attendances, 'AT_') !== false) {
                                   /* if ($attendances == 'AT_AT4' || $attendances == 'AT_AT3' || $attendances == 'AT_AT1') $late = 0; else if ($attendances == 'AT_AT5' || $attendances == 'AT_AT2') $early = 0; else {
                                        $late = 0;
                                        $early = 0;
                                    }*/
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

                            $savetemporary = array('userid' => $userdata, 'date_shift' => date('Y-m-d', strtotime($tanggal)), 'shift_in' => date('H:i:s', $cekin), 'shift_out' => date('H:i:s', $cekout), 'date_in' => $datein, 'check_in' => $timein, 'break_out' => $brkout, 'break_in' => $brkin, 'date_out' => $dateout, 'check_out' => $timeout, 'late' => $late, 'early_departure' => $early, 'ot_before' => $otbefore, 'ot_after' => $otafter, 'workinholiday' => $workinholiday, 'attendance' => $attend, 'edit_come' => $editin, 'edit_home' => $editout, 'notes' => $notes != '' ? $notes : null, 'flaghol' => $flagholiday);
                            $this->db->insert('process', $savetemporary);
                        } else {
                            $savetemporary = array('userid' => $userdata, 'date_shift' => date('Y-m-d', strtotime($tanggal)), 'shift_in' => null, 'shift_out' => null, 'date_in' => null, 'check_in' => null, 'break_out' => null, 'break_in' => null, 'date_out' => null, 'check_out' => null, 'late' => null, 'early_departure' => null, 'ot_before' => null, 'ot_after' => null, 'workinholiday' => $workinholiday, 'attendance' => 'BLNK', 'edit_come' => null, 'edit_home' => null, 'notes' => null, 'flaghol' => $flagholiday);
                            $this->db->insert('process', $savetemporary);
                        }
                    }
                }
                $actionlog = array(
                    'user' => "UserOto",
                    'ipadd' => getRealIpAddr(),
                    'logtime' => date("Y-m-d H:i:s"),
                    'logdetail' => 'Process all data',
                    'info' => "Sukses"
                );
                $this->db->insert('goltca', $actionlog);

                $startdate = date('Y-m-d', $datestart);
                $enddate = date('Y-m-d', $dateend);
                //$this->db->query("CALL sp_push_display_by_date('$startdate','$enddate')");
            }
            $data['msg'] = 'Data berhasil diproses..';
            $data['status'] = 'succes';

            echo json_encode($data);
        } else {
            $data['msg'] = 'Periode sudah ditutup..';
            $data['status'] = 'error';

            echo json_encode($data);
        }

    }

    public function doprocuangmakan($startdate = null, $enddate = null, $withmsg = null)
    {
        ini_set("pcre.backtrack_limit", PHP_INT_MAX);
        ini_set("pcre.recursion_limit", PHP_INT_MAX);

        $datestart = strtotime($startdate);
        $dateend = strtotime($enddate);
        //$orgidi = $org=='undefined'?'1':$org;

        $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $datestart));
        $this->db->where('tahun', date('Y', $datestart));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $bukatutup = $query->row()->status;
        } else {
            $bukatutup = FALSE;
        }

        if ($bukatutup) {
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');
            //$orgid = $this->pegawai->deptonall($orgidi);

            $useraidi = array();
            $user = $this->process_model->getuserall();
            foreach ($user->result() as $userya) {
                $useraidi[] = $userya->userid;
                $deptid[$userya->userid] = $userya->deptid;
            }
            if (count($useraidi) > 0) {

               /* $this->db->select("process.userid, date_shift, check_in, check_out, attendance, workinholiday, late, early_departure");
                $this->db->from("process");
                $this->db->join("userinfo", "process.userid=userinfo.userid ");
                $this->db->where("date_shift >=", date('Y-m-d', $datestart));
                $this->db->where("date_shift <=", date('Y-m-d', $dateend));

                $usr_ids_chunk = array_chunk($useraidi, 25);
                foreach ($usr_ids_chunk as $usr_ids) {
                    //$this->db->or_where_in('deptid', $org_ids);
                    $this->db->or_where_in('userinfo.userid', $usr_ids);
                }

                //$this->db->where_in('userinfo.userid', $useraidi);
                $rstl = $this->db->get();
                $usrproc = array();
                foreach ($rstl->result() as $rwr) {
                    $usrproc[$rwr->userid][date('Y-m-d', strtotime($rwr->date_shift))] = $rwr->attendance;
                }*/

                //proses uang makan
                $this->db->where_in('userid', $useraidi);
                $this->db->where('tanggal >=', date('Y-m-d', $datestart));
                $this->db->where('tanggal <=', date('Y-m-d', $dateend));
                $this->db->delete('data_uang_makan');

                foreach ($user->result() as $row) {
                    $kdGol = konversiGolongan($row->golru);
                    $umk = $this->uangmakan->ref_uangmakan($kdGol);

                    $str1 = date('Y-m-d', $datestart);
                    $end1 = date('Y-m-d', $dateend);
                    while (strtotime($str1) <= strtotime($end1)) {

                        $tanggal = date('Y-m-d', strtotime($str1));
                        //$workinholiday = 0;
                        $flagholiday = 0;
                        if (isset($flaghol[$row->deptid][date('Y-m-d', strtotime($tanggal))]) || isset($flaghol['1'][date('Y-m-d', strtotime($tanggal))])) $flagholiday = 1;

                        /*$flagoff = 1;
                        if (isset($usrproc[$row->userid][date('Y-m-d', strtotime($tanggal))])) {
                            $att = $usrproc[$row->userid][date('Y-m-d', strtotime($tanggal))];
                            if ($att == 'NWK' || $att == 'NWDS') {
                                $flagoff = 1;
                            } else {
                                $flagoff = 0;
                            }
                        }*/

                        $this->db->select("userid");
                        $this->db->where('userid', $row->userid);
                        $this->db->where('rosterdate', $tanggal);
                        $this->db->where(" absence NOT IN ('OFF','OFFPD') ", NULL, FALSE);
                        $this->db->limit(1);
                        $rsttt = $this->db->get('rosterdetails');
                        $num_results = $rsttt->num_rows();

                        $this->db->select("userid");
                        $this->db->where('userid', $row->userid);
                        $this->db->where('rosterdate', $tanggal);
                        $this->db->limit(1);
                        $rsttt = $this->db->get('rosterdetailsatt');
                        $num_results2 = $rsttt->num_rows();

                        if ($flagholiday == 0 && $num_results > 1 && $num_results2==0) {
                            $jml = $this->pegawai->getrealabsensi($row->userid, date('Y-m-d', strtotime($str1)), date('Y-m-d', strtotime($str1)));

                            $jmlkotor = $jml["jmlHari"] * $umk["nominal"];
                            $pajak = $jmlkotor * ($umk["pajak"] / 100);
                            $jmlBersih = $jmlkotor - $pajak;

                            $datapeg = array("userid" => $row->userid,
                                "deptid" => $row->deptid,
                                "golongan" => $row->golru,
                                "tanggal" => date('Y-m-d', strtotime($str1)),
                                "tarif" => $umk["nominal"], "jml_pajak" => $pajak,
                                "jum_hadir" => $jml["jmlHari"],
                                "pajak_persen" => $umk["pajak"],
                                "jml_kotor" => $jmlkotor, "bersih" => $jmlBersih
                            );
                            $this->db->insert('data_uang_makan', $datapeg);
                        }

                        $str1 = date("Y-m-d", strtotime("+1 days", strtotime($str1)));
                    }

                }

            }
            $data['msg'] = 'Data sudah diproses..';
        } else {
            $data['msg'] = 'Periode sudah ditutup..';
        }

        $data['status'] = 'succes';
        if ($withmsg != null) {
            echo json_encode($data);
        }
    }
	
	public function prosesdataws()
    {

        $tgl = strtotime("-1 day");
        $tgl1 = date('Y-m-d', $tgl);
        $tgl2 = date('Y-m-d');


        $datestart = strtotime($tgl1);
        $dateend = strtotime($tgl2);
        //$stspeg = $this->input->post("stspeg");
        $stspeg = array('1', '2');// explode(",",$stspeg);
        //$orgid = $this->deptonprocess($orgidi);
        $range = ($dateend - $datestart) / 86400;

        $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $datestart));
        $this->db->where('tahun', date('Y', $datestart));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $bukatutup = $query->row()->status;
        } else {
            $bukatutup = FALSE;
        }

        //createLog("Proses prosensi otomatis","procdata");

        //if ($bukatutup) {
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');
            $shift = $this->process_model->getshiftorgidall($datestart, $dateend, $stspeg);
            $arrai = array();
            foreach ($shift as $shifting) {
                $arrai[$shifting->userid][strtotime($shifting->rosterdate)] =
                    array('start_in' => $shifting->start_in, 'check_in' => $shifting->check_in,
                        'end_check_in' => $shifting->end_check_in, 'start_out' => $shifting->start_out,
                        'check_out' => $shifting->check_out, 'end_check_out' => $shifting->end_check_out,
                        'start_break' => $shifting->start_break, 'break_out' => $shifting->break_out,
                        'break_in' => $shifting->break_in, 'end_break' => $shifting->end_break,
                        'late_tolerance' => $shifting->late_tolerance,
                        'early_departure' => $shifting->early_departure,
                        'shift_in' => $shifting->shift_in, 'shift_out' => $shifting->shift_out,
                        'ot_tolerance' => $shifting->ot_tolerance,
                        'in_ot_tolerance' => $shifting->in_ot_tolerance,
                        'out_ot_tolerance' => $shifting->out_ot_tolerance,
                        'attendance' => $shifting->attendance,
                        'absence' => $shifting->absence, 'notes' => $shifting->notes,
                        'emptype' => $shifting->emptype,
                        'codeshift' => $shifting->code_shift);
            }

            $holiday = $this->process_model->cekholiday($datestart, $dateend);
            $holarray = array();
            $flaghol = array();
            foreach ($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if ($selisih == 0) {
                    $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                    $flaghol[$hol->deptid][$hol->startdate] = $hol->flag;
                } else {
                    $jarak = $selisih / 86400;
                    for ($k = 0; $k <= $jarak; $k++) {
                        $holarray[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->info;
                        $flaghol[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->flag;
                    }
                }
            }
            $useraidi = array();
            $user = $this->process_model->getuserall($stspeg);
            foreach ($user->result() as $userya) {
                $useraidi[] = $userya->userid;
                $deptid[$userya->userid] = $userya->deptid;
            }

            if (count($useraidi) > 0) {
                $user_or = array();
                foreach ($useraidi as $xids) {
                    $user_or[] = "userid = $xids";
                }

                $this->db->where('date_shift >=', date('Y-m-d', $datestart));
                $this->db->where('date_shift <=', date('Y-m-d', $dateend));
                $this->db->where("(" . implode(' OR ', $user_or) . ")");
                //$this->db->where_in("userid",$useraidi);
                $query = $this->db->get("process");
                $datas = $query->row_array();
                log_history("edit", "process", $datas);

                /*$this->db->where('date_shift >=', date('Y-m-d', $datestart));
                $this->db->where('date_shift <=', date('Y-m-d', $dateend));
                $this->db->where("(".implode(' OR ',$user_or).")");
                //$this->db->where_in("userid",$useraidi);
                $this->db->delete('process');*/

                $otsetting = $this->process_model->cekotsetting();
                foreach ($otsetting->result() as $otset) $otseting[$otset->field_id] = $otset->field_value;

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
                                if ($arrai[$userdata][strtotime($tanggal)]['check_in'] < $arrai[$userdata][strtotime($tanggal)]['start_in']) $tanggalsci = date('Y-m-d', strtotime($tanggal) - 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_in'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggaleci = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_break'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_out'] < $arrai[$userdata][strtotime($tanggal)]['start_break']) $tanggalbo = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['break_in'] < $arrai[$userdata][strtotime($tanggal)]['break_out']) $tanggalbi = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_break'] < $arrai[$userdata][strtotime($tanggal)]['break_in']) $tanggaleb = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['start_out'] < $arrai[$userdata][strtotime($tanggal)]['check_in']) $tanggalsco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['check_out'] < $arrai[$userdata][strtotime($tanggal)]['start_out']) $tanggalco = date('Y-m-d', strtotime($tanggal) + 86400);
                                if ($arrai[$userdata][strtotime($tanggal)]['end_check_out'] < $arrai[$userdata][strtotime($tanggal)]['check_out']) $tanggaleco = date('Y-m-d', strtotime($tanggal) + 86400);
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

                            /*if($arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'PMD' || $arrai[$userdata][strtotime($tanggal)]['codeshift'] == 'S1C') {
                                $empin = strtotime($this->process_model->getawalpmd($userdata, date('Y-m-d H:i:s', $startcheckin), date('Y-m-d H:i:s', $endcheckin)));
                                $empout = strtotime($this->process_model->getakhirpmd($userdata, date('Y-m-d H:i:s', $startcheckout), date('Y-m-d H:i:s', $endcheckout)));
                            } else */
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

                            if ($attendances != 'AT_') {
                                if (strpos($attendances, 'AT_') !== false) {
                                    if ($attendances == 'AT_AT6' || $attendances == 'AT_AT3' || $attendances == 'AT_AT1' || $attendances == 'AT_SK01' || $attendances == 'AT_SK03') $late = 0;
                                    else if ($attendances == 'AT_AT6' || $attendances == 'AT_AT2' || $attendances == 'AT_SK02') $early = 0;
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
                                $savetemporary = array('userid' => $userdata,
                                    'date_shift' => date('Y-m-d', strtotime($tanggal)),
                                    'shift_in' => date('H:i:s', $cekin),
                                    'shift_out' => date('H:i:s', $cekout),
                                    'date_in' => $datein, 'check_in' => $timein, 'break_out' => $brkout,
                                    'break_in' => $brkin, 'date_out' => $dateout, 'check_out' => $timeout,
                                    'late' => $late, 'early_departure' => $early, 'ot_before' => $otbefore,
                                    'ot_after' => $otafter, 'workinholiday' => $workinholiday,
                                    'attendance' => $attend, 'edit_come' => $editin, 'edit_home' => $editout,
                                    'notes' => $notes != '' ? $notes : null, 'flaghol' => $flagholiday);
                                $this->db->insert('process', $savetemporary);
                            } else {
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
                                $savetemporary = array('userid' => $userdata,
                                    'date_shift' => date('Y-m-d', strtotime($tanggal)),
                                    'shift_in' => null, 'shift_out' => null, 'date_in' => null,
                                    'check_in' => null, 'break_out' => null, 'break_in' => null,
                                    'date_out' => null, 'check_out' => null, 'late' => null,
                                    'early_departure' => null, 'ot_before' => null, 'ot_after' => null,
                                    'workinholiday' => $workinholiday, 'attendance' => 'BLNK',
                                    'edit_come' => null, 'edit_home' => null, 'notes' => null,
                                    'flaghol' => $flagholiday);
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
                
	             /*  $actionlog = array(
                    'user' => "UserOto",
                    'ipadd' => getRealIpAddr(),
                    'logtime' => date("Y-m-d H:i:s"),
                    'logdetail' => 'Process all data',
                    'info' => "Sukses"
                );
                $this->db->insert('goltca', $actionlog);
                */

                //$this->db->query("CALL sp_push_display_by_date('$startdate','$enddate')");

                $this->doprocuangmakan($datestart, $dateend);

                $data['msg'] = 'Data berhasil diproses..';
                $data['status'] = 'succes';
            } else {
                $data['msg'] = 'Tidak ada data pegawai yang diproses..';
                $data['status'] = 'error';
            }

            echo json_encode($data);
        /*} else {
            $data['msg'] = 'Periode sudah ditutup..';
            $data['status'] = 'error';
            echo json_encode($data);
        }*/
    }
	public function allpegawai()
    {
        ini_set("pcre.backtrack_limit", PHP_INT_MAX);
        ini_set("pcre.recursion_limit", PHP_INT_MAX);

        $tgl = date('Y-m-d');

        $datestart = strtotime($tgl);
        $dateend = strtotime($tgl);
        $orgidi = '1';

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

        if($bukatutup) {
			echo "Proses tanggal:". $tgl;
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

                                $savetemporary = array(
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

                                $this->db->where('userid', $userdata);
                                $this->db->where('date_shift', date('Y-m-d', strtotime($tanggal)));
                                $this->db->update('process', $savetemporary);
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
            }
            $data['status'] = 'true';
            $data['msg'] = 'Data sudah diproses..';
        } else {
            $data['status'] = 'false';
            $data['msg'] = 'Periode sudah ditutup..';
        }

        $data['status'] = 'succes';
        echo json_encode($data);
    }
	
	public function dashboarddata()
    {
        $addwhere=" AND jftstatus in ('1','2') and jenispegawai in ('1','2') ";
        $tgl = date("Y-m-d");
        $sql ="
                SELECT 'TEPATWAKTU' AS ket, IFNULL(count(*),0) AS jml,deptid,date_shift 
                FROM view_ontime 
                WHERE date_shift = '$tgl' $addwhere
                group by deptid,date_shift
                UNION ALL
                SELECT 'TERLAMBAT' AS ket, IFNULL(count(*),0) AS jml,deptid,date_shift  
                FROM view_late
                WHERE date_shift = '$tgl' $addwhere
                group by deptid,date_shift
                UNION ALL
                SELECT 'IJIN' AS ket, IFNULL(count(*),0) AS jml ,deptid,date_shift 
                FROM view_ijin
                WHERE date_shift = '$tgl' $addwhere
                group by deptid,date_shift
                UNION ALL
                SELECT 'ALPHA' AS ket, IFNULL(count(*),0) AS jml ,deptid,date_shift 
                FROM view_alpa
                WHERE date_shift = '$tgl' $addwhere
                group by deptid,date_shift
                UNION ALL
                SELECT 'SAKIT' AS ket, IFNULL(count(*),0) AS jml ,deptid,date_shift 
                FROM view_sakit
                WHERE date_shift = '$tgl' $addwhere
                group by deptid,date_shift
                UNION  ALL   
                SELECT 'CUTI' AS ket, IFNULL(count(*),0) AS jml ,deptid,date_shift 
                FROM view_cuti
                WHERE date_shift = '$tgl' $addwhere
                group by deptid,date_shift
                UNION  ALL   
                SELECT 'TB' AS ket, IFNULL(count(*),0) AS jml ,deptid,date_shift 
                FROM view_tb
                WHERE date_shift = '$tgl' $addwhere
                group by deptid,date_shift
                UNION  ALL   
                SELECT 'DINAS' AS ket, IFNULL(count(*),0) AS jml ,deptid,date_shift 
                FROM view_dinas_luar
                WHERE date_shift = '$tgl' $addwhere
                group by deptid,date_shift
                UNION  ALL   
                SELECT 'KHS' AS ket, IFNULL(count(*),0) AS jml ,deptid,date_shift 
                FROM view_no_jdwl
                WHERE date_shift = '$tgl' $addwhere
                group by deptid,date_shift
                ";

        $data = $this->db->query($sql)->result_array();

        $fp = fopen('assets/dashboard/data_'.date("Y-m-d").'.json', 'w');
        fwrite($fp, json_encode(array('data'=>$data,'lastget'=>date("Y-m-d H:i"))));
        fclose($fp);
    }

	public function postingdata()
    {
        $datestart = strtotime(date("Y-m-d"));
        $datestop = strtotime(date("Y-m-d"));
        $orgidi = '1';
        $stspeg = array('1', '2');
        $jnspeg = array('1', '2');
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

            //tunjangan by date
            $mastertunj = array();

            //trap referensi data
            $this->session->set_userdata('tahundata',date("Y",$datestart));

            $sql = "select * from mastertunjangan";
            $query = $this->db->query($sql);
            foreach ($query->result() as $que) {
                $mastertunj[$que->kelasjabatan] = $que->tunjangan;
            }

            $orgid = $this->pegawai->deptonall($orgidi);

            $tbar = array();
            $bbar = array();
            $holar = array();
            $range = ($datestop - $datestart) / 86400;
            $totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;

            $pkrga = array();
            $attrecap = $this->report_model->getatt();
            foreach($attrecap->result() as $at) {
                $atar[$at->atid] = $at->atname;
                $attend[] = array(
                    'atid'		=> $at->atid,
                    'atname'	=> $at->atname
                );
                $pkrga[$at->atid] = $at->value;
            }
            $pkrg = array();
            $absrecap = $this->report_model->getabs();
            foreach($absrecap->result() as $bs) {
                $bbar[$bs->abid] = $bs->abname;
                $absen[] = array(
                    'abid'		=> $bs->abid,
                    'abname'	=> $bs->abname
                );
                $pkrg[$bs->abid] = $bs->value;
            }

            $dept = $this->report_model->getdept();
            foreach($dept->result() as $depat) {
                $deptar[$depat->deptid] = $depat->deptname;
            }

            $queryemp = $this->report_model->getorgemployeedetailsxx($orgid,$stspeg,$jnspeg);
            //print_r($this->db->last_query());
            //die();
            $aten = array();
            $aben = array();

            $dataallay = array();
            $dataallu = array();
            foreach($queryemp->result() as $queq) {

                $unkir= isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0;

                $isplt=0;
                if (date("Y",$datestart)>2016) {
                    if (isset($queq->tmt_plt) && isset($queq->plt_deptid) && isset($queq->plt_eselon))
                    {
                        $time = strtotime($queq->tmt_plt);
                        $final = strtotime("+1 month", $time);
                        if ($datestart >= $final) {
                            $isplt=1;
                        }
                    }
                }

                $jbtnAwal = strval(konversiEselon($queq->eselon));
                $jbtnAkhir = strval(konversiEselon($queq->plt_eselon));
                $unkirplt=0;
                $kriteriaPlt = 0;
                if ($isplt==1) {
                    $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan]) ? $mastertunj[$queq->plt_kelasjabatan] * refTHP() :0;
                    if ( ($jbtnAwal  < $jbtnAkhir) ) {
                        $kriteriaPlt=1;
                        $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan] :0;
                        $unkirplt= ($unkirplt * refTHP() ) * refPLT();
                    }
                    if ( ($jbtnAwal  == $jbtnAkhir ) ) {
                        $kriteriaPlt=2;
                        $unkirplt= isset($mastertunj[$queq->plt_kelasjabatan])?$mastertunj[$queq->plt_kelasjabatan]:0;
                        $unkirplt= ($unkirplt * refTHP() ) * refPLT();
                    }

                    if ( ($jbtnAwal  > $jbtnAkhir ) ) {
                        $kriteriaPlt=3;
                        $klsjbatan = max(array($queq->kelasjabatan,$queq->plt_kelasjabatan));
                        $unkirplt= isset($mastertunj[$klsjbatan])?$mastertunj[$klsjbatan] * refTHP() :0;
                        $unkir = 0;
                    }
                }

                if ($queq->payable == 0){
                    $unkir=0;
                    $unkirplt=0;
                }
                $dataallay[] = array(
                    'userid'   => $queq->userid,
                    'empTitle' => $queq->title,
                    'empID' => $queq->badgenumber,
                    'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
                    'empName' => $queq->name,
                    'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
                    'kelasjabatan' => $queq->kelasjabatan,
                    'tunjanganprofesi' => $queq->tunjanganprofesi,
                    'tunjangan' => $unkir,
                    'jftstatus' => $queq->jftstatus,
                    'jenisjabatan' => $queq->jenisjabatan,
                    'jenispegawai' => $queq->jenispegawai,
                    'kedudukan' => $queq->kedudukan,
                    'deptid' => $queq->deptid,
                    'tunjanganplt' => $unkirplt,
                    'plt_deptid' => $queq->plt_deptid,
                    'plt_eselon' => $queq->plt_eselon,
                    'tmt_plt' => $queq->tmt_plt,
                    'payable' => $queq->payable,
                    'eselon' => $queq->eselon,
                    'plt_jbtn' => $queq->plt_jbtn,
                    'plt_sk' => $queq->plt_sk,
                    'plt_kelasjabatan' => $queq->plt_kelasjabatan,
                    'isplt' =>$isplt,
                    'golongan' => $queq->golru,
                    'kriteriaPlt' =>$kriteriaPlt,
                    'plt_deptname' => isset($deptar[$queq->plt_deptid])?$deptar[$queq->plt_deptid]:'',
                    'dipisah'=>0,
                    'ishidden'=>0,
                    'orgf'=>""
                );

                foreach($attend as $at3) {
                    $aten[$queq->userid][$at3['atid']] = 0;
                }
                foreach($absen as $ab3) {
                    $aben[$queq->userid][$ab3['abid']] = 0;
                }
            }

            $compa = $this->report_model->getcompany();
            $company = array(
                'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
                'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
                'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
                'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
                'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
                'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
            );
            $dataallaye = array();
            $datafoot = array();
            $abc=0;
            $dataview = '';
            $datavw = '';

            foreach($dataallay as $queqe) {

                $querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
                $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);

                $dataarray = array();
                $totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
                $totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
                $totallibur  = 0; $tubel = 0; $totaltubel = 0; $totaltubelplt=0;$tunjtubel = 0; $tunjtubelplt = 0; $tgltubel = 0;
                $totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;
                $tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0; $totplt = 0;$tunjplt = 0;
                $ttlmsk = array(); $tunjangan = array(); $tunjanganplt = array();$tunbulplt = array();
                $tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
                $jft = array(); $jfto = 0; $jftp = array();
                $jpeg = array(); $jpego = 0; $jpegp = array();
                $kedu = array(); $keduo = 0; $kedup = array();
                $totaltunjplt =array();$totaltunj =array();

                foreach($querytempo->result() as $que) {
                    $ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
                    $totaltunj[date('mY', strtotime($que->date_shift))] = 0;
                    $totaltunjplt[date('mY', strtotime($que->date_shift))] = 0;
                }

                foreach($querytempo->result() as $que) {

                    $kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
                    if ($kelas != 0) $queqe['kelasjabatan'] = $kelas;
                    $tunjang = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);

                    if($tunjang!=0) {
                        $queqe['tunjanganasli']=$tunjang;
                        if ($queqe['userid'] != 0 ) {
                            if (date("Y", $datestart) > 2016) {
                                $queqe['tunjangan'] = $tunjang * refTHP();
                            } else {
                                $queqe['tunjangan'] = $tunjang;
                            }
                        } else {
                            $queqe['tunjangan'] = 0;
                        }
                    }


                    if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1)
                        $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                    if($que->flaghol == 1)
                        $ttlmsk[date('mY', strtotime($que->date_shift))]++;

                    if($tunbuli!=$queqe['tunjangan'])
                        $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];
                    $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];

                    $byuser= 1;
                    if ($byuser==0) {
                        if ($queqe['kriteriaPlt'] == 3)
                        {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            ///$lnpltdepid = $this->isEselon2($pltdepid);
                            //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                            $cpltdeptid =isOrgIn($pltdepid);

                            $orgtar = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            //$lorgtar = $this->isEselon2($orgtar);
                            //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                            $corgtar  = isOrgIn($orgtar);

                            if ($cpltdeptid != $corgtar)
                            {
                                $queqe['ishidden'] = 1;
                            }
                        }
                    }

                    if ($byuser==0)
                    {
                        if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                            if($this->input->post('org')!='undefined')
                            {
                                $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                                //$lnpltdepid = $this->isEselon2($pltdepid);
                                //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                                $cpltdeptid =isOrgIn($pltdepid);

                                $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                                //$lndepid = $this->isEselon2($depid);
                                //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                                $cdeptid =isOrgIn($depid);

                                $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                                $orgtar = strlen($depid)==7 ? "0".$depid: $depid;
                                //$lorgtar = $this->isEselon2($orgtar);
                                //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                                $corgtar  = isOrgIn($orgtar);
                                //echo $dibagi." PLT:".$queqe['plt_deptid']."-".$pltdepid." DEF:".$queqe['deptid']."-".$depid." ".$corgtar." : ".$queqe['userid']."</br>";
                                //$queqe['orgf']= $cdeptid." ".$cpltdeptid.' '.$corgtar." ".$dibagi;
                                if ($dibagi==1)
                                {
                                    if ($cdeptid == $corgtar) //satu tempat defenitif
                                    {
                                        $queqe['dipisah']= 1;
                                        //$queqe['orgf']= $cdeptid.' '.$corgtar;
                                    }
                                    if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                    {
                                        $queqe['dipisah']= 2;
                                        // $queqe['orgf']= $cpltdeptid.' '.$corgtar;
                                    }
                                }
                                //$queqe['orgf']= $cdeptid." ".$cpltdeptid.' '.$corgtar." ".$dibagi." ".$lorgtar." ".$queqe['deptid']." ".$queqe['plt_deptid'];

                                if ($tunbuli != $queqe['tunjangan']) {
                                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];
                                    $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];
                                }
                            }
                        }

                        if ($queqe['kriteriaPlt'] == 3) {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            //$lnpltdepid = $this->isEselon2($pltdepid);
                            //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            //$lndepid = $this->isEselon2($depid);
                            //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                            $cdeptid = isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            //$lorgtar = $this->isEselon2($orgtar);
                            //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                            $corgtar  = isOrgIn($orgtar);

                            if ($dibagi==1)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }
                        }
                    } else
                    {
                        if ($queqe['kriteriaPlt'] == 1 || $queqe['kriteriaPlt'] == 2) {
                            if($orgid!='undefined')
                            {
                                $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                                //$lnpltdepid = $this->isEselon2($pltdepid);
                                //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                                $cpltdeptid =isOrgIn($pltdepid);

                                $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                                //$lndepid = $this->isEselon2($depid);
                                //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                                $cdeptid =isOrgIn($depid);

                                $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                                $orgtar = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                                //$lorgtar = $this->isEselon2($orgtar);
                                //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                                $corgtar  =isOrgIn($orgtar);

                                $queqe['orgf']= $cdeptid." ".$cpltdeptid.' '.$corgtar;
                                if ($dibagi)
                                {
                                    if ($cdeptid === $corgtar) //satu tempat defenitif
                                    {
                                        $queqe['dipisah']= 1;
                                    }
                                    if ($cpltdeptid === $corgtar) //satu tempat plt/plh
                                    {
                                        $queqe['dipisah']= 2;
                                    }
                                }
                                if ($tunbuli != $queqe['tunjangan']) {
                                    $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];
                                    $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganplt'];
                                }
                            }
                        }

                        if ($queqe['kriteriaPlt'] == 3) {
                            $pltdepid = strlen($queqe['plt_deptid'])==7 ? "0".$queqe['plt_deptid']: $queqe['plt_deptid'];
                            //$lnpltdepid = $this->isEselon2($pltdepid);
                            //$cpltdeptid = ($lnpltdepid>0) ? substr($pltdepid,0,4) : substr($pltdepid,0,2);
                            $cpltdeptid =isOrgIn($pltdepid);

                            $depid = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            //$lndepid = $this->isEselon2($depid);
                            //$cdeptid = ($lndepid>0) ? substr($depid,0,4) : substr($depid,0,2);
                            $cdeptid = isOrgIn($depid);

                            $dibagi = ($cpltdeptid == $cdeptid) ? 0 : 1;

                            $orgtar = strlen($queqe['deptid'])==7 ? "0".$queqe['deptid']: $queqe['deptid'];
                            //$lorgtar = $this->isEselon2($orgtar);
                            //$corgtar  = ($lorgtar>0) ? substr($orgtar,0,4) : substr($orgtar,0,2);
                            $corgtar  =  isOrgIn($orgtar);
                            if ($dibagi)
                            {
                                if ($cdeptid == $corgtar) //satu tempat defenitif
                                {
                                    $queqe['dipisah']= 1;
                                }
                                if ($cpltdeptid == $corgtar) //satu tempat plt/plh
                                {
                                    $queqe['dipisah']= 2;
                                }
                            }
                        }
                    }

                    //else
                    //	$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];
                    $jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
                    if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;

                    if($jfto!=$queqe['jftstatus'])
                        $jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];
                    /* else
                        $jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */

                    $jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
                    if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;

                    if($jpego!=$queqe['jenispegawai'])
                        $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];
                    /* else
                        $jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */

                    $kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
                    if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;

                    if($keduo!=$queqe['kedudukan'])
                        $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];
                    /* else
                        $kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */

                    $tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
                    if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;

                    if($tunpro!=$queqe['tunjanganprofesi'])
                        $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];
                    /* else
                        $tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */

                    $tunbuli = $queqe['tunjangan'];
                    $tunpro = $queqe['tunjanganprofesi'];
                    $jfto = $queqe['jftstatus'];
                    $jpego = $queqe['jenispegawai'];
                    $keduo = $queqe['kedudukan'];
                }

                foreach($querytemp->result() as $que) {
                    $late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
                    $totalpersenkurang = 0; $totaljadwal++;
                    $day = date('D', strtotime($que->date_shift));
                    $date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
                    if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
                    if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));


                    if(isset($tunbul[date('mY', strtotime($que->date_shift))])) {
                        if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                            $tunjtubel = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                            //echo $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                            $tunjtubelplt = $tunbulplt[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
                        }
                    } else {
                        $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                        $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                    }


                    if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12) { $tottun = 0;$totplt=0;}
                    if($queqe['jenispegawai']==3 || $queqe['jenispegawai']==4) { $tottun = 0;$totplt=0;}

                    if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        }
                    }

                    if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = $tunjanganplt[date('mY', strtotime($que->date_shift))] / 2;
                        }
                        if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        }
                        if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                            $tubel = 1;
                            $tgltubel = strtotime($que->date_shift);
                        }
                    }

                    if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 || $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
                            $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                            $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                        }
                    }

                    if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
                        $tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];
                        if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
                            if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))]) {
                                $tunjangan[date('mY', strtotime($que->date_shift))] = 0;
                                $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                            }
                            else {
                                //khusus profesi
                                $tunjangan[date('mY', strtotime($que->date_shift))] = (($tunjangan[date('mY', strtotime($que->date_shift))]*2) - $tunjanganpro[date('mY', strtotime($que->date_shift))])*refTHP();
                                $tunjanganplt[date('mY', strtotime($que->date_shift))] = 0;
                            }
                        }
                    }

                    //ambil nilai pulang
                    $early=0;
                    if($que->early_departure!=0) {
                        $early = $que->early_departure;
                    }

                    if($que->late!=0) {
                        $late = $que->late;
                        if($que->ot_after!=0) {
                            if($que->ot_after>3600)
                                $telat = $que->late - 3600;
                            else
                                $telat = $que->late - $que->ot_after;
                            $late = $telat<=0?0:$telat;
                        }

                        if (date("Y",$datestart)<2017)
                        {
                            if($late < 1860) $krglate = 0; //kurang dari 30 menit
                            else if($late >= 1860 && $late < 3660) $krglate = 0.5; //31- 61 menit
                            else if($late >= 3660 && $late < 5460) $krglate = 1; //61- 91 menit
                            else if($late >= 5460) $krglate = 1.5; //91 menit ke atas
                        } else
                        {
                            if($late < 3660) { // < 61 menit
                                $krglate = 0.5;
                                if ($early>=$late) $krglate = 0;
                                if ($check_out == null) $krglate = 0.5;
                            }
                            else if($late >= 3660) { // >=61 menit ke atas
                                $krglate = 1;
                            }
                            //jika terlambat >60 dan mengganti 60 menit
                            if ($que->late > 3660 && $que->ot_after>3660)
                                $krglate = 1;

                        }
                    }

                    if($que->early_departure!=0) {
                        $early = $que->early_departure;

                        if (date("Y",$datestart)>2016) {
                            if ($early < 3660) $krgearly = 0.5; else if ($early >= 3660) $krgearly = 1;
                        } else {
                            if($early > 1 && $early < 1860) $krgearly = 0.5;
                            else if($early >= 1860 && $early < 3660) $krgearly = 1;
                            else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
                            else if($early >= 5460) $krgearly = 1.5;
                        }
                    }

                    if (date("Y",$datestart)>2016) {
                        if ($check_in == null) $krglate = 1;
                        if ($check_out == null) $krgearly = 1;
                    } else {
                        if($check_in==null) $krglate = 1.5;
                        if($check_out==null) $krgearly = 1.5;
                    }
                    if($que->attendance == 'ALP') {
                        $krglate = 0;
                        $krgearly = 0;
                        if (date("Y",$datestart)>2016) {
                            $krgalpa = 5;
                        } else {
                            $krgalpa = 3;
                        }
                    }

                    if($que->attendance == 'BLNK') {
                        $totalblnk++;
                    }

                    $s = 0;
                    if(isset($bbar[$que->attendance])) {
                        $krglate = 0;
                        $krgearly = 0;
                        $krgalpa = 0;
                        $krgstatus = $pkrg[$que->attendance];
                        $s = 1;
                    }

                    if(isset($atar[$que->attendance])) {
                        if($que->attendance=='AT_AT4' || $que->attendance=='AT_AT3' || $que->attendance=='AT_AT1')
                            $krglate = 0;
                        else if($que->attendance=='AT_AT5' || $que->attendance=='AT_AT2')
                            $krgearly = 0;
                        else {
                            $krglate = 0;
                            $krgearly = 0;
                        }
                        $krgalpa = 0;
                        $krgstatus = $pkrga[$que->attendance];
                    }

                    if($krglate > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {
                        $dataarray[] = array(
                            'day'			=> $day,
                            'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                            'status'		=> $que->check_in!=null?'Terlambat':'Tidak absen datang',
                            'nilai'			=> $que->check_in==null?null:$this->report_model->itungan($late),
                            'pengurangan'	=> $krglate,
                            'tunjangan'			=> ($tunjangan[date('mY', strtotime($que->date_shift))]),
                            'tunjanganplt'		=> ($tunjanganplt[date('mY', strtotime($que->date_shift))] ),
                            'total'			=> ($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                            'totalplt'	    => ($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                        );
                        $totalpersenkurang = $totalpersenkurang + $krglate;
                        $tunj = $tunj + (($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        $totallate = $totallate +(($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        $tunjplt = $tunjplt + (($krglate /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    }

                    if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1 ) {
                        $dataarray[] = array(
                            'day'			=> $day,
                            'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                            'status'		=> $que->check_out!=null?'Pulang lebih awal':'Tidak absen pulang',
                            'nilai'			=> $que->check_out==null?null:$this->report_model->itungan($que->early_departure),
                            'pengurangan'	=> $krgearly,
                            'tunjangan'			=> ($tunjangan[date('mY', strtotime($que->date_shift))]),
                            'tunjanganplt'		=> ($tunjanganplt[date('mY', strtotime($que->date_shift))] ),
                            'total'			=> ($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                            'totalplt'			=> ($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                        );
                        $totalpersenkurang = $totalpersenkurang + $krgearly;
                        $tunj = $tunj + (($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        $tunjplt = $tunjplt + (($krgearly /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    }
                    if($krgalpa != 0 && $que->workinholiday!=1) {
                        $dataarray[] = array(
                            'day'			=> $day,
                            'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                            'status'		=> 'Alpa',
                            'nilai'			=> null,
                            'pengurangan'	=> $krgalpa,
                            'tunjangan'			=> ($tunjangan[date('mY', strtotime($que->date_shift))]),
                            'tunjanganplt'		=> ($tunjanganplt[date('mY', strtotime($que->date_shift))] ),
                            'total'			=> ($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                            'totalplt'			=> ($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                        );
                        $totalpersenkurang = $totalpersenkurang + $krgalpa;
                        $totalalpa++;
                        $tunj = $tunj + (($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        $tunjplt = $tunjplt + (($krgalpa /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    }

                    if($krgstatus != 0  && $queqe['payable']!=0) {
                        $dataarray[] = array(
                            'day'			=> $day,
                            'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                            'status'		=> $s==1?$bbar[$que->attendance]:$atar[$que->attendance],
                            'nilai'			=> null,
                            'pengurangan'	=> $krgstatus,
                            'tunjangan'			=> ($tunjangan[date('mY', strtotime($que->date_shift))]),
                            'tunjanganplt'		=> ($tunjanganplt[date('mY', strtotime($que->date_shift))] ),
                            'total'			=> ($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]),
                            'totalplt'		=> ($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
                        );

                        $totalpersenkurang = $totalpersenkurang + $krgstatus;
                        if($que->attendance=='AB_12') $totalpembatalan++;
                        $tunj = $tunj + (($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                        $tunjplt = $tunjplt + (($krgstatus /100) * ($tunjanganplt[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
                    }

                    $totalpersensemua = $totalpersensemua + $totalpersenkurang;
                    $totalmasuk = array_sum($ttlmsk);

                    if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1 ) {
                        if($tubel==1) {
                            $totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                            $totaltubelplt = $totaltubelplt+ $tunjanganplt[date('mY', strtotime($que->date_shift))];
                        } else {
                            $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                            $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                        }

                        $dataarray[] = array(
                            'day'			=> $day,
                            'date'			=> date('d-m-Y', strtotime($que->date_shift)),
                            'status'		=> null,
                            'nilai'			=> null,
                            'pengurangan'	=> 0,
                            'total'=>0,
                            'totalplt'=>0,
                            'tunjangan'			=> ($tunjangan[date('mY', strtotime($que->date_shift))]),
                            'tunjanganplt'		=> ($tunjanganplt[date('mY', strtotime($que->date_shift))] )
                        );
                    }

                    if($que->flaghol == 1) {
                        if($tubel==1) {
                            $totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
                            $totaltubelplt = $totaltubelplt + $totaltunjplt[date('mY', strtotime($que->date_shift))];
                        } else {
                            $totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];
                            $totaltunjplt[date('mY', strtotime($que->date_shift))] = $totaltunjplt[date('mY', strtotime($que->date_shift))] + $tunjanganplt[date('mY', strtotime($que->date_shift))];
                        }
                    }

                }

                $tottun = isset($totaltunj)?array_sum($totaltunj):0;
                $totplt = isset($totaltunjplt)?array_sum($totaltunjplt):0;
                //echo $tottun;
                if($tubel==1) {
                    $dataarray[] = array(
                        'day'			=> '',
                        'date'			=> '',
                        'status'		=> 'Tugas Belajar Per Tgl '.date('d-m-Y', $tgltubel),
                        'nilai'			=> null,
                        'pengurangan'	=> '50',
                        'total'			=> $totaltubel,
                        'totalplt'			=> 0,
                        'tunjangan'			=> 0,
                        'tunjanganplt' =>0
                    );
                    $tunj = $tunj + $totaltubel;
                    $tottun = $tunjtubel;
                    $totplt= $tunjtubelplt;
                }

                if($totalalpa == $totalmasuk) {
                    $tottun = 0;
                    $totplt = 0;
                }

                if($totalpembatalan == $totalmasuk) {
                    $tottun = 0;
                    $totplt = 0;
                }

                $totalsemua = $totalalpa + $totalpembatalan;

                if($totalsemua == $totalmasuk) {
                    $tottun = 0;
                    $totplt = 0;
                }

                if($totalblnk == $totaljadwal) {
                    $tottun = 0;
                    $totplt = 0;
                }

                $dataallaye = array(
                    'userid' => $queqe['userid'],
                    'empTitle' => $queqe['empTitle'],
                    'empID' => $queqe['empID'],
                    'empHire' => $queqe['empHire'],
                    'empName' =>$queqe['empName'],
                    'deptId' => $queqe['deptid'],
                    'deptName' => $queqe['deptName'],
                    'status_pegawai' => $queqe['jftstatus'],
                    'jenis_pegawai' => $queqe['jenispegawai'],
                    'kelasjabatan' => $queqe['kelasjabatan'],
                    'tunjangan' => $tottun,
                    'tunjanganplt' => $totplt,
                    'plt_eselon' => $queqe['plt_eselon'],
                    'plt_sk' => $queqe['plt_sk'],
                    'plt_jbtn' => $queqe['plt_jbtn'],
                    'plt_kelasjabatan' => $queqe['plt_kelasjabatan'],
                    'isplt' => $queqe['isplt'],
                    'plt_deptid' => $queqe['plt_deptid'],
                    'plt_deptname' => $queqe['plt_deptname'],
                    'tmt_plt' => $queqe['tmt_plt'],
                    'kriteriaPlt' => $queqe['kriteriaPlt'],
                    'orgf' => $queqe['orgf'],
                    'golongan'=> $queqe['golongan'],
                    'payable'=> $queqe['payable'],
                );

                $datafoot = array('totalpersen' => $totalpersensemua,
                    'total' => $tottun == 0 ? 0 : $tunj,
                    'totalplt' => $totplt == 0 ? 0 : $tunjplt
                );

                $byuser= 1;

                $datanya = array(
                    "dateinfo" => strtoupper(format_bulan_tahun(date('d-m-Y', $datestart) )),
                    "index" => $abc,
                    "cominfo" => $company,
                    "empinfo" => $dataallaye,
                    "footah" => $datafoot,
                    "data" => $dataarray,
                    "totaltunj" =>$totaltunj,
                    "totaltunjplt" => $totaltunjplt,
                    "byuser"=>$byuser,
                    "dipisah" =>$queqe['dipisah'],
                    "orgf"=>$queqe['orgf'],
                    "ishidden" =>$queqe['ishidden']);
                $abc++;
                //loadFunction
                $this->poston($datanya);
            }

            $data['msg'] = 'Data sudah diproses..';
        } else {
            $data['msg'] = 'Periode sudah ditutup..';
        }

        $data['status'] = 'succes';
        $this->output->set_output(  json_encode($data));

    }

    function poston($datanya)
    {

        $dateinfo=$datanya["dateinfo"];
        $cominfo= $datanya["cominfo"];
        $empinfo= $datanya["empinfo"];
        $footah= $datanya["footah"];
        $datarow= $datanya["data"];
        $totaltunj=$datanya["totaltunj"];
        $totaltunjplt= $datanya["totaltunjplt"];
        $byuser=$datanya["byuser"];
        $dipisah=$datanya['dipisah'];
        $orgf=$datanya['orgf'];
        $ishidden=$datanya['ishidden'];

        //print_r($datanya);exit();

        if (!$ishidden) {

            $datain["nama_jabatan"] = $empinfo['empTitle'];
            $datain["plt_kelasjabatan"] = $empinfo['plt_kelasjabatan'];
            $datain["plt_eselon"] = $empinfo['plt_eselon'];
            $datain["plt_jbtn"] = $empinfo['plt_jbtn'];
            $datain["kelas_jabatan"] = $empinfo['kelasjabatan'];
            //start untuk tunjangan

            if ($byuser == 1) {
                //echo $byuser." Kriteria: ".$empinfo['kriteriaPlt']." dipisah: ".$dipisah." unit: ".$empinfo['orgf'];
                if (($empinfo['isplt'] == 1)) {
                    $datain["kelas_jabatan"] = $empinfo['kelasjabatan'] == 0 ? '0' : $empinfo['kelasjabatan'];
                    $datain["plt_kelasjabatan"] = $empinfo['plt_kelasjabatan'] == 0 ? '0' : $empinfo['plt_kelasjabatan'];
                } else {
                    $datain["kelas_jabatan"] = $empinfo['kelasjabatan'] == 0 ? '0' : $empinfo['kelasjabatan'];
                }

                if ($empinfo['isplt'] == 1) {

                    if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {
                        //dipisah

                        $pengurangan = (ceil(round($footah['total'], 2)) > ceil(round($empinfo['tunjangan'], 2))) ? ceil(round($empinfo['tunjangan'], 2)) : ceil(round($footah['total'], 2));
                        $tunjangan = $empinfo['tunjangan'];
                        $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));

                        $penguranganplt = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                        $tunjanganplt = $empinfo['tunjanganplt'];
                        $totltunkplt = (ceil(round($tunjanganplt, 2)) - ceil(round($penguranganplt, 2)));
                        $grandttl = $totltunk + $totltunkplt;

                        $datain["tunj_plt"] = ceil(round($tunjanganplt, 2));
                        $datain["tunj_presensi"] = ceil(round($tunjangan, 2));

                    } else {
                        $pengurangan = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                        $tunjangan = $empinfo['tunjanganplt'];
                        $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));
                        $datain["tunj_plt"] = ceil(round($tunjangan, 2));
                        $datain["tunj_presensi"] = ceil(round($tunjangan, 2));
                    }
                } else {
                    if ($empinfo['kriteriaPlt'] == 3)
                    {
                        $datain["tunj_plt"] = ceil(round($empinfo['tunjangan'], 2));
                    }

                    $pengurangan = (ceil(round($footah['total'], 2)) > ceil(round($empinfo['tunjangan'], 2))) ? ceil(round($empinfo['tunjangan'], 2)) : ceil(round($footah['total'], 2));
                    $tunjangan = $empinfo['tunjangan'];
                    $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));
                    $datain["tunj_presensi"] = ceil(round($tunjangan, 2));
                }
            } //end byUser
            else {
                if (($empinfo['kriteriaPlt'] == 2) || ($empinfo['kriteriaPlt'] == 1)) {
                    if ($dipisah == 2) {
                        $dpename = $empinfo['plt_deptname'];
                        $dpename2 = $empinfo['plt_jbtn'];
                        $dpename3 = $empinfo['plt_kelasjabatan'] == 0 ? '0' : $empinfo['plt_kelasjabatan'];
                    } else {
                        $dpename = $empinfo['deptName'];
                        $dpename2 = $empinfo['empTitle'];
                        $dpename3 = $empinfo['kelasjabatan'] == 0 ? '0' : $empinfo['kelasjabatan'];
                    }
                    $datain["nama_jabatan"] = $dpename3;

                    if ($dipisah == 0) {

                        $datain["plt_jbtn"] = $empinfo['plt_jbtn'];
                        $datain["kelas_jabatan"] = $empinfo['kelasjabatan'] == 0 ? '0' : $empinfo['kelasjabatan'];

                        $pengurangan = (ceil(round($footah['total'], 2)) > ceil(round($empinfo['tunjangan'], 2))) ? ceil(round($empinfo['tunjangan'], 2)) : ceil(round($footah['total'], 2));
                        $tunjangan = $empinfo['tunjangan'];
                        $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));

                        $penguranganplt = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                        $tunjanganplt = $empinfo['tunjanganplt'];
                        $totltunkplt = (ceil(round($tunjanganplt, 2)) - ceil(round($penguranganplt, 2)));

                        $grandttl = $totltunk + $totltunkplt;

                        if ($dipisah == 2) {
                            $grandttl = $totltunkplt;
                        } else if ($dipisah == 1) {
                            $grandttl = $totltunk;
                        } else {
                            $grandttl = $totltunkplt + $totltunk;
                        }
                        if ($dipisah == 1) {
                            $datain["tunj_presensi"] = ceil(round($empinfo['tunjangan'], 2));
                        }
                        if ($dipisah == 2) {
                            $datain["tunj_plt"] = ceil(round($empinfo['tunjanganplt'], 2));
                        }
                        if ($dipisah == 0) {
                            $datain["tunj_presensi"] = ceil(round($empinfo['tunjangan'], 2));
                            $datain["tunj_plt"] = ceil(round($empinfo['tunjanganplt'], 2));
                        }

                    } else if (($empinfo['kriteriaPlt'] == 3) && ($ishidden)) {

                        $datain["kelas_jabatan"] = $empinfo['kelasjabatan'] == 0 ? '0' : $empinfo['kelasjabatan'];
                        $datain["plt_jbtn"] = $empinfo['plt_jbtn'];
                        $datain["tunj_presensi"] = 0;
                    } else {

                        $datain["kelas_jabatan"] = $empinfo['kelasjabatan'];
                        if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {
                            if ($dipisah == 2) {
                                $datain["kelas_jabatan"] = $empinfo['plt_kelasjabatan'];
                            }
                        }
                        if ($empinfo['kriteriaPlt'] == 3) {
                            $datain["kelas_jabatan"] = $empinfo['plt_kelasjabatan'];
                        }

                        $pengurangan = (ceil(round($footah['total'], 2)) > ceil(round($empinfo['tunjangan'], 2))) ? ceil(round($empinfo['tunjangan'], 2)) : ceil(round($footah['total'], 2));
                        $tunjangan = $empinfo['tunjangan'];
                        $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));

                        if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {

                            if ($dipisah == 2) {
                                $pengurangan = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                                $tunjangan = $empinfo['tunjanganplt'];
                                $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));
                            }
                        }
                        if ($empinfo['kriteriaPlt'] == 3) {
                            $pengurangan = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                            $tunjangan = $empinfo['tunjanganplt'];
                            $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));
                        }
                        $datain["tunj_presensi"] = ceil(round($tunjangan, 2));

                    }

                }
            }

            //end untuk tunjangan

            $datain["userid"] = $empinfo['userid'];
            $datain["deptid"] = $empinfo['deptId'];

            $datain["status_pegawai"] = $empinfo['status_pegawai'];
            $datain["jenis_pegawai"] = $empinfo['jenis_pegawai'];
            $datain["plt_deptid"] = $empinfo['plt_deptid'];

            $datain["plt_sk"] = $empinfo['plt_sk'];
            $datain["tmt_plt"] = $empinfo['tmt_plt'];
            $datain["golongan"] = $empinfo['golongan'];
            $datain["payable"] = $empinfo['payable'];
            $datain["create_by"] = $this->session->userdata('s_username');

            foreach ($datarow as $row) {

                $datain["tgl_proses"] = dmyToymd($row['date']);
                $datain["ket_potongan"] = $row['status'];
                $datain["nilai_potongan"] = $row['nilai'];
                $datain["potongan"] = $row['pengurangan'];
                $datain["tunjangan"] = ceil(round($row['tunjangan'], 2)) ;
                $datain["tunjangan_plt"] = ceil(round($row['tunjanganplt'], 2));

                $nPlt = 0;
                $nUnkir = 0;
                $nTotalRow = 0;
                $showplt = 0;
                $showunkir = 1;
                $nUnkir = ceil(round($row['total'], 2));
                $nTotalRow = ceil(round($row['total'], 2)) + ceil(round($row['totalplt'], 2));
                if ($empinfo['kriteriaPlt'] != 3) {
                    if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {
                        $nPlt = ceil(round($row['totalplt'], 2));
                        if ($dipisah == 2) {
                            $showplt = 1;
                            $showunkir = 0;
                            $nTotalRow = ceil(round($row['totalplt'], 2));
                        }
                        if ($dipisah == 0) {
                            $showplt = 1;
                            $showunkir = 1;
                            $nTotalRow = ceil(round($row['total'], 2)) + ceil(round($row['totalplt'], 2));
                        }
                    }
                } else {
                    $nPlt = ceil(round($row['totalplt'], 2));
                    $nUnkir = 0;
                    $showplt = 1;
                    $showunkir = 0;
                    $nTotalRow = ceil(round($row['totalplt'], 2));
                }
                $datain["row_potongan"] = $nTotalRow;

                $this->db->select('id_proses');
                $this->db->from('data_proses');
                $this->db->where('tgl_proses', $datain['tgl_proses']);
                $this->db->where('userid', $datain['userid']);
                $this->db->where('deptid', $datain['deptid']);
                $query = $this->db->get();
                $isAllowInsert = $query->num_rows()>0 ? FALSE : TRUE;

                if ($isAllowInsert ) {
                    $this->db->insert('data_proses', $datain);
                }

            }
        }
    }
	
	public function sinkronsimpeg()
    {
        ini_set('MAX_EXECUTION_TIME', '-1');
        ini_set('memory_limit', '-1');

        $lstdata = getContentUrl("http://ropeg.setjen.kemendagri.go.id/restserver/index.php/api/for_absen/pegawai_dengan_plt_plh");

        $namafile = 'assets/tmpsimpeg/data_'.date("Y-m-d-H-i-s").'.json';
        $fp = fopen($namafile, 'w');
        if (flock($fp, LOCK_EX)) { // do an exclusive lock
            ftruncate($fp, 0);
            fwrite($fp, $lstdata);
            flock($fp, LOCK_UN); // release the lock
        }
        fclose($fp);

        $namafileJson =FCPATH.$namafile;
        $adaJson = file_exists($namafileJson);
        if ($adaJson)
        {
            $str = file_get_contents($namafileJson);
            $aListPeg = json_decode($str, true);

            foreach ($aListPeg as $itemdata)
            {
                $dataup=null;
                $golru="";
                $this->db->select('ngolru');
                $this->db->from('ref_golruang');
                $this->db->where('kgolru', $itemdata["kgolru"]);
                $queryf = $this->db->get();
                if ($queryf->num_rows()>0) {
                    $golru = $queryf->row()->ngolru;
                }

                $dataup["name"] =  $itemdata["namapeg"];
                $dataup["title"] =  $itemdata["njab_definitif"];
                $dataup["placebirthdate"] =  $itemdata["tempat_lahir"];
                $dataup["nickname"] =  $itemdata["nama_panggilan"];
                $dataup["gender"] =  $itemdata["jenis_kelamain"];

                $arrproc=array("1","2","3","4","5","6","7","8");
                $allowproc=in_array($itemdata["agama"],$arrproc);
                if ($allowproc) {
                    $dataup["religion"] = $this->utils->getKdAgama()[$itemdata["agama"]];
                }
                $dataup["birthdate"] =  $itemdata["tgl_lahir"];
                $dataup["golru"] =  $golru;
                $dataup["kelasjabatan"] =  $itemdata["peringkat_definitif"];
                $dataup["jftstatus"] =  $itemdata["status_pegawai"];
                $dataup["jenispegawai"] =  $itemdata["jenis_pegawai"];
                $dataup["kedudukan"] =  $itemdata["kduduk"];
                $dataup["tmtpangkat"] =  $itemdata["tmtpang"];

                $this->db->select('deptid');
                $this->db->from('departments');
                $this->db->where('deptid', substr($itemdata["kunker_definitif"],1,-1));
                $queryf = $this->db->get();
                if ($queryf->num_rows()>0) {
                    $dataup["deptid"] =  substr($itemdata["kunker_definitif"],1,-1);
                }

                if ($itemdata["status_plt"]=="1") {
                    $dataup["plt_jbtn"] = $itemdata["njab_plt"];
                    $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plt"];
                    $dataup["plt_sk"] = $itemdata["sk_plt"];
                    $dataup["tmt_plt"] = $itemdata["tmtjab_plt"];

                    $this->db->select('deptid');
                    $this->db->from('departments');
                    $this->db->where('deptid', empty($itemdata["kunker_plt"]) ? "" : substr($itemdata["kunker_plt"], 1, -1));
                    $queryf = $this->db->get();
                    if ($queryf->num_rows()>0) {
                        $dataup["plt_deptid"] =  empty($itemdata["kunker_plt"]) ? "" : substr($itemdata["kunker_plt"], 1, -1);
                    }
                }

                if ($itemdata["status_plh"]=="1") {
                    $dataup["plt_jbtn"] = $itemdata["njab_plh"];
                    $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plh"];
                    $dataup["plt_sk"] = $itemdata["sk_plh"];
                    $dataup["tmt_plt"] = $itemdata["tmtjab_plh"];

                    $this->db->select('deptid');
                    $this->db->from('departments');
                    $this->db->where('deptid', empty($itemdata["kunker_plh"]) ? "" : substr($itemdata["kunker_plh"], 1, -1));
                    $queryf = $this->db->get();
                    if ($queryf->num_rows()>0) {
                        $dataup["plt_deptid"] =  empty($itemdata["kunker_plh"]) ? "" : substr($itemdata["kunker_plh"], 1, -1);
                    }
                }

                $this->db->select('neselon');
                $this->db->from('ref_eselon');
                $this->db->where('keselon', $itemdata["keselon"]);
                $queryf = $this->db->get();
                if ($queryf->num_rows()>0) {
                    $dataup["eselon"] =  $queryf->row()->neselon;
                } else {
                    $dataup["eselon"]="";
                }

                $this->db->from('userinfo');
                $this->db->where('userid', $itemdata["nip"]);
                $query = $this->db->get();
                if ($query->num_rows()>0) {
                    $datas = $query->row_array();
                    log_history("update-simpeg","userinfo",$datas);

                    $this->db->where('userid', $itemdata["nip"]);
                    $this->db->update("userinfo",$dataup);

                    $this->db->reset_query();

                } else {
                    $dataup["userid"] =  $itemdata["nip"];
                    $dataup["badgenumber"] =  $itemdata["nip"];
                    $dataup["timezones"] =  "0000000000000000";
                    $dataup["accgroup"] =  "1";

                    $this->db->insert("userinfo",$dataup);
                    $this->db->reset_query();
                }

            }

        }
		
		$data['msg'] = 'Data pegawai sudah disinkronisasikan..';
        $data['status'] = 'succes';
        $this->output->set_output(  json_encode($data));
	}
}
