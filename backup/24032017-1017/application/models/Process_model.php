<?php 

class Process_model extends CI_Model
{	
    public function getuser($areaid)
    {
        $this->db->select('a.userid, a.name');
		$this->db->from('userinfo a');
		$this->db->join('userinfo_attarea b', 'a.userid=b.userid');
		$this->db->where_in('b.areaid', $areaid);
		$this->db->group_by('a.userid');
		return $this->db->get();
    }
	
	public function getuserbyuser($userid)
    {
        $this->db->select('userid, name, deptid');
		$this->db->from('userinfo');
		$this->db->where_in('userid', $userid);
		return $this->db->get();
    }
	
	public function getuserbyorg($orgid)
    {
        $this->db->select('userid, name, deptid');
		$this->db->from('userinfo');
		$this->db->where_in('deptid', $orgid);
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
		$this->db->where_in('a.deptid', $deptid);
		$this->db->group_by('a.userid');
		return $this->db->get();
    }
	
	public function getshift($orgid, $datestart, $dateend)
    {
		$this->db->from('view_rosterdetails');
		$this->db->where('rosterdate >=', date('Y-m-d', $datestart));
		$this->db->where('rosterdate <=', date('Y-m-d', $dateend));
		$this->db->where_in('deptid', $orgid);
		$query = $this->db->get();
		return $query->result();
    }
	
	public function getshiftuserid($userid, $datestart, $dateend,$stspeg=null)
    {
		$this->db->from('view_rosterdetails');
		$this->db->where('rosterdate >=', date('Y-m-d', $datestart));
		$this->db->where('rosterdate <=', date('Y-m-d', $dateend));
		$this->db->where_in('userid', $userid);
        if ($stspeg != null)
        {
            $this->db->where_in('jenispegawai', $stspeg);
        }
		$query = $this->db->get();
		return $query->result();
    }
	
	public function getshiftorgid($orgid, $datestart, $dateend,$stspeg=null)
    {
		$this->db->from('view_rosterdetails');
		$this->db->where('rosterdate >=', date('Y-m-d', $datestart));
		$this->db->where('rosterdate <=', date('Y-m-d', $dateend));
		$this->db->where_in('deptid', $orgid);
        if ($stspeg != null)
        {
            $this->db->where_in('jenispegawai', $stspeg);
        }
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
	
	public function cekholiday($datestart=null, $dateend=null)
	{
		$this->db->from('holiday');
		if(!empty($orgid)) {
			$s = array();
			foreach($orgid as $ar)
				$s[] = (string)$ar;	
			$this->db->where_in('deptid', implode(',', $s));
		}
		$this->db->or_where('deptid', '1');
		return $this->db->get();
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
}
