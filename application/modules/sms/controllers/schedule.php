<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @property
 */
class Schedule extends MX_Controller
{
	function Schedule(){
		parent::__construct();
		
		$this->load->model('sms_setting/sms_setting_model','sms_setting');
		
		$this->load->model('pegawai/pegawai_model','pegawai');
		$this->load->model('report_model','report');
		$this->load->model('jadwalkrj/jadwalkrj_model','jadwalkrj');
	}
	
	function index()
    {
		$testing = true;
		$sendsms = true;
		$onedataonly = true;
		
		// CONFIGURATION
		// RUBAH SESUAI YG DIINGINKAN
		// ==================================
		// ==================================
		//$simpeg_host = "http://localhost/simpeg/index.php?";
		//$check_hour = "08";
		//$check_minute = "30";
		//$sms_sender = "ROPEG DAGRI";
		// ==================================
		// ==================================
		
		$setting = $this->sms_setting->getData();
		foreach ($setting->result() as $settingdetail) {
			$simpeg_host = $settingdetail->simpeg_host;
			$sms_sender = $settingdetail->sms_sender;
			$checkinout_hour = $settingdetail->checkinout_hour;
			if ($checkinout_hour < 10)
			{
				$check_hour = "0". $checkinout_hour;
			}
			else
			{
				$check_hour = $checkinout_hour;
			}
			$checkinout_minute = $settingdetail->checkinout_minute;
			if ($checkinout_minute < 10)
			{
				$check_minute = "0". $checkinout_minute;
			}
			else
			{
				$check_minute = $checkinout_minute;
			}
			$sms_msg1 = $settingdetail->sms_msg1;
		}
		if (strlen($sms_msg1) == 0)
		{
			die();
		}
		if ($testing)
		{
			//echo "<br/>SETTING<br />";
			//echo $simpeg_host."<br/>";
			//echo $check_hour."<br/>";
			//echo $check_minute."<br/>";
			//echo $sms_sender."<br/>";
		}
		
		if ($testing)
		{
			$simpeg_host = "http://localhost/simpeg/index.php?";
			$sms_sender = "IDSM";
		}
		
		//if ($testing) echo "START<br />";
		ini_set('MAX_EXECUTION_TIME', '-1');
		ini_set('memory_limit', '-1');
		
		// DATA
		$date = date("Y-m-d",time());
		if ($testing) $date = "2018-12-18";
		$curdate = strtotime($date);
		$datestart = $curdate;
		$dateend = $curdate;
		
		$date_from = $date." 00:00:00";
		$date_to = $date." ".$check_hour.":".$check_minute.":00";
		
		$cari = "";
		$org = "";
        //$stspeg = $this->input->post('stspeg');
		$stspeg = array(1,2);
        //$jnspeg = $this->input->post('jnspeg');
		$jnspeg = array(1,2);
		
		// HOLIDAY
		// =======
		//if ($testing) echo "<br/>HOLIDAY<br />";
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
		foreach($holarray as $hol) {
			//if ($testing) echo $hol."<br />";
		}
		if (in_array(date('Y-m-d', $datestart), $holarray))
		{
			//if ($testing) echo "<br />HARI LIBUR - SKIPPED<br />";
			die();
		}
		
		// BUKATUTUP
		// =========
        $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $curdate));
        $this->db->where('tahun', date('Y', $curdate));
        $query = $this->db->get();
        $rowcount = $query->num_rows();
        if ($rowcount>0)
		{
            $bukatutup = $query->row()->status?true:false;
        } else {
            $bukatutup = true;
        }
		
		// MASTER ATTENDACE
		// ================
		//if ($testing) echo "<br/>ATTENDANCE<br/>";
        $att = $this->report->getatt();
        foreach($att->result() as $at) {
            $atar[$at->atid] = $at->atname;
			//if ($testing) echo $at->atid." - ".$at->atname."<br />";
        }
		
		// MASTER ABSENCE
		// ==============
		//if ($testing) echo "<br/>ABSENCE<br />";
        $abs = $this->report->getabs();
        foreach($abs->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
			//if ($testing) echo $bs->abid." - ".$bs->abname."<br />";
        }
		
		//$deptshift = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall($this->session->userdata('s_dept')):array();
		$deptshift = $this->pegawai->deptonall(array(1)); // dept 1 = KEMENTRIAN DALAM NEGRI (All)
		//if ($testing) print_r($deptshift);

        if($org!='')	{
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
				
				// GET EMPLOYEE
				// Report line 2220
                $userlist = $this->report->getempofdept($areaid, $orgid,$stspeg,$cari,$jnspeg);

                $countuserlist = $userlist->num_rows();
            } else {
                $orgid = array();
                $countuserlist = 0;
            }
        }
		
		// CHECKINOUT
		//if ($testing) echo "<br/>CHECKINOUT<br />";
		$this->db->from('checkinout a');
        $this->db->where('a.checktype', 0);
        $this->db->where("a.checktime BETWEEN '".$date_from."' AND '".$date_to."'");
        $iclock = $this->db->get();
		$checkinoutarr = array();
		foreach ($iclock->result() as $ic)
		{
			$checkinoutarr[] = $ic->userid;
			//if ($testing) echo $ic->userid." - ".$ic->checktime."<br/>";
		}
		
		// EXCLUSION
		//if ($testing) echo "<br/>EXCLUSION<br />";
		$this->db->from('sms_exclusion');
        $exclusion = $this->db->get();
		$exclusionarr = array();
		foreach ($exclusion->result() as $ex)
		{
			$exclusionarr[] = $ex->userid;
			//if ($testing) echo $ex->userid."<br/>";
		}
		
		
		// EXCLUDE
		$exclude = array('OFF','OFFPD');
		
		// PROSES
        $fieldarr = array();
        if($countuserlist!=0) {
			// ROSTER
			//if ($testing) echo "<br/>ROSTER<br />";
            $roster = $this->jadwalkrj->getroster($orgid, $datestart, $dateend);
            $arrayroster = array();
            foreach ($roster->result() as $rosterdetail) {
                $arrayroster[$rosterdetail->userid][strtotime($rosterdetail->rosterdate)] = array('absence' => $rosterdetail->absence, 'attendance' => $rosterdetail->attendance);
				//if ($testing) echo $rosterdetail->userid." - ".$rosterdetail->rosterdate." - ".$rosterdetail->absence." - ".$rosterdetail->attendance."<br />";
            }
			
			// PER EMPLOYEE
            foreach ($userlist->result() as $datauser) {
                $data_arr = array(
                    'userid' => $datauser->userid,
                    'name' => $datauser->name,
                    'group' => $datauser->deptname);
				if (isset($arrayroster[$datauser->userid][$datestart])) {
					$shiftname = $arrayroster[$datauser->userid][$datestart]['absence'];
					$absattstat = $arrayroster[$datauser->userid][$datestart]['attendance'];
					if ($shiftname) {
						if (isset($absattstat)) {
							if($absattstat=='NWDS')
								$data_arr[$datestart]=$shiftname.'#'.$absattstat;
							else {
								if(array_key_exists($absattstat, $atar))
									$data_arr[$datestart]=$shiftname.'#AT';
								else if(array_key_exists($absattstat, $bbar))
									$data_arr[$datestart]=$shiftname.'#AB';
								else
									$data_arr[$datestart]=$shiftname;
							}
						} else {
							$data_arr[$datestart] = $shiftname;
						}
					} else {
						if (in_array(date('Y-m-d', $datestart), $holarray)) $data_arr[$datestart] = '';
					}
				} else {
					if (in_array(date('Y-m-d', $datestart), $holarray)) $data_arr[$datestart] = '';
				}
				
				// EXCLUDE
				$ket = explode("#", $data_arr[$datestart]);
				if (in_array($ket[0],$exclude))
				{
				}
				else
				{
					if ($ket[1] == "AB" || $ket[1] == "AT")
					{
					}
					else
					{
						if (isset($data_arr[$datestart]))
						{
							if (in_array($data_arr["userid"],$checkinoutarr))
							{
							}
							else
							{
								if (in_array($data_arr["userid"],$exclusionarr))
								{
								}
								else
								{
                					$fieldarr[] = $data_arr;
								}
							}
						}
					}
				}
            }
        }
		
		// SAMPLE 1 SAJA
		if ($onedataonly) $fieldarr = array_slice($fieldarr,0,1);

        $jum_data = count($fieldarr);
		
		if ($testing) echo "<br/>SEND SMS<br />";
		$count = 0;
		foreach ($fieldarr as $key =>$value)
		{
			$count++;
			if ($testing)
			{
				echo $count;
				echo " - ";
				echo $value["userid"];
				echo " - ";
				echo $value[$curdate];
				if (!$sendsms)
				{
					echo "<br />";
				}
			}
			
			// Format SMS
			$message = "Yth ".$value["name"].", ".$sms_msg1;
		
			// kirim sms menggunakan api
			// URL
			$url = $simpeg_host."/sms/api_absensi";
			//echo $url;
			
			$params = array(
				"userid" => $value["userid"],
				"message" => $message,
				"sender" => $sms_sender
			);
			
			$field_string = http_build_query($params);
			
			$curl = curl_init();
			
			curl_setopt( $curl, CURLOPT_URL,            $url          );
			curl_setopt( $curl, CURLOPT_HEADER,         FALSE         );
			curl_setopt( $curl, CURLOPT_POST,           TRUE          );
			curl_setopt( $curl, CURLOPT_POSTFIELDS,     $field_string );
			curl_setopt( $curl, CURLOPT_TIMEOUT,        120           );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE          );
			
			if ($sendsms)
			{
				$htm = curl_exec($curl);
			}
			$err = curl_errno($curl);
			$inf = curl_getinfo($curl);
			
			curl_close($curl);
			
			// SHOW WHAT CAME BACK, IF ANYTHING
			if ($htm)
			{
				if ($testing)
				{
					echo " - ";
					echo "API RESULT=";
					echo $htm;
					echo "<br/>";
				}
			}
		}
	}

    function index2()
    {
		$testing = true;
		
		// CONFIGURATION
		// RUBAH SESUAI YG DIINGINKAN
		// ==================================
		// ==================================
		$simpeg_host = "http://localhost/simpeg/index.php?";
		$check_hour = "08";
		$check_minute = "30";
		$sms_sender = "ROPEG DAGRI";
		// ==================================
		// ==================================
		
		if ($testing)
		{
			$simpeg_host = "http://localhost/simpeg/index.php?";
			$sms_sender = "IDSM";
		}
		
		ini_set('MAX_EXECUTION_TIME', '-1');
		ini_set('memory_limit', '-1');
		
		$curdate = date("Y-m-d",time());
		$date_from = $curdate." 00:00:00";
		$date_to = $curdate." ".$check_hour.":".$check_minute.":00";
		
		//$sql = "SELECT userid FROM checkinout WHERE checktype=0 ";
		//$sql .= "AND checktime BETWEEN '".$date_from."' AND '".$date_to."' ";
		//$query = $this->db->query($sql);
		//foreach ($query->result() as $row)
		//{
		//	echo $row->userid;
		//	echo "<br/>";
		//}
		
		$SQLcari = "";
		$SQLcari .= " AND jftstatus in (1,2) AND jenispegawai in (1,2) ";
		$SQLcari .= " AND userid NOT IN (SELECT userid  FROM checkinout WHERE checktype=0 ";
		$SQLcari .= "AND checktime BETWEEN '".$date_from."' AND '".$date_to."') ";
		$SQLcari .= " ORDER BY id asc";
		
		$dipaging = 0;
		$limit = null;
		$offset = null;
		
		// UNTUK TEST
		if ($testing)
		{
			$dipaging = 1;
			$limit = 1;
			$offset = 1;
		}
		
		// DATA
		$query = $this->pegawai->getDaftar($dipaging,$limit,$offset,null,$SQLcari);
		$result = $query->result();
		
		$query2 = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
		$jum_data = $query2->num_rows();
		
		//echo $jum_data;
		//echo "<br/>";
		
		$i = 1;
		foreach ($result as $row)
		{
			$i++;
			
			echo $i." ";
			echo $row->userid." ".$row->name;
			//echo "<br/>";
			
			// Format SMS
			$message = "Yth ".$row->name." Anda Telat";
		
			// kirim sms menggunakan api
			// URL
			$url = $simpeg_host."/sms/api_absensi";
			//echo $url;
			
			$params = array(
				"userid" => $row->userid,
				"message" => $message,
				"sender" => $sms_sender
			);
			
			$field_string = http_build_query($params);
			
			$curl = curl_init();
			
			curl_setopt( $curl, CURLOPT_URL,            $url          );
			curl_setopt( $curl, CURLOPT_HEADER,         FALSE         );
			curl_setopt( $curl, CURLOPT_POST,           TRUE          );
			curl_setopt( $curl, CURLOPT_POSTFIELDS,     $field_string );
			curl_setopt( $curl, CURLOPT_TIMEOUT,        120           );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE          );
			
			$htm = curl_exec($curl);
			$err = curl_errno($curl);
			$inf = curl_getinfo($curl);
			
			curl_close($curl);
			
			// SHOW WHAT CAME BACK, IF ANYTHING
			if ($htm)
			{
				if ($testing)
				{
					echo " - ";
					echo "API RESULT=";
					echo $htm;
					echo "<br/>";
				}
			}
		}
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
