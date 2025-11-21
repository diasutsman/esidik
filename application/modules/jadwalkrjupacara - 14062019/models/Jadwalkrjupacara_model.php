<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 12:57 PM
 * absensi.kemendagri.go.id
 */

class Jadwalkrjupacara_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'a.*,b.deptname AS nama_parent')." 
		 from departments a
		 LEFT JOIN departments b ON b.deptid=a.parentid
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        //$sql .=" order by id asc ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }

    public function getempofdept($areaid, $organid)
    {
        $sql = "select * from view_employee ";
        $a = 0;
        if(!empty($areaid)) {
            $s = array();
            foreach($areaid as $ar)
                $s[] = "'".$ar."'";
            $sql .= "where string_to_array(area_id, ',') && array[".implode(',',$s)."] ";
            $a = 1;
        }

        if(!empty($organid)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $s = array();
            foreach($organid as $ar)
                $s[] = "'".$ar."'";
            $sql .= $ql."deptid in (".implode(',', $s).") ";
            $a = 1;
        }

        if($a==1) $ql = 'AND ';
        else $ql = 'where ';
        $sql .= $ql."active is null ";

        return $this->db->query($sql);
    }

    public function getuserid($orgid)
    {
        $this->db->select('userid');
        $this->db->from('userinfo');
        if (is_array($orgid))
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
        }
        return $this->db->get();
    }

    public function updateatt($userid, $atdate, $data)
    {
        //$this->db->where_in('userid', $userid);
        if (is_array($userid))
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
        }

        $this->db->where('rosterdate', $atdate);
        if($this->db->update('rosterdetailsatt_upacara', $data)) {
            return true;
        }
        return false;
    }

    public function getshift()
    {
        $this->db->select('code_shift, name_shift');
        $this->db->from('master_shift');
        return $this->db->get();
    }

    public function getroster($orgid, $datestart, $dateend)
    {
        $sql = "select a.userid, a.rosterdate, a.rostertime,a.rostertime_end,a.attendance, a.editby from rosterdetailsatt_upacara a 
                left join userinfo b on a.userid=b.userid
				";
        $a = 0;
        if(!empty($orgid)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $s = array();
            foreach($orgid as $ar)
                $s[] = "'".$ar."'";
            $sql .= $ql."deptid in (".implode(',', $s).") ";
            $a = 1;
        }

        if($a==1) $ql = 'AND ';
        else $ql = 'where ';

        $sql .= $ql."a.rosterdate >= '".$this->db->escape_str(date('Y-m-d', $datestart))."' AND a.rosterdate <= '".$this->db->escape_str(date('Y-m-d', $dateend))."'";

        return $this->db->query($sql);
    }

    public function getrostergroupdetails($orgid, $datestart, $dateend)
    {
        $sql = "select a.userid, a.rosterdate, a.attendance, a.editby from groupshiftdetails a left join userinfo b on a.userid=b.userid ";
        $a = 0;
        if(!empty($orgid)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $s = array();
            foreach($orgid as $ar)
                $s[] = "'".$ar."'";
            $sql .= $ql."deptid in (".implode(',', $s).") ";
            $a = 1;
        }

        if($a==1) $ql = 'AND ';
        else $ql = 'where ';

        $sql .= $ql."rosterdate >= '".$this->db->escape_str(date('Y-m-d', $datestart))."' AND rosterdate <= '".$this->db->escape_str(date('Y-m-d', $dateend))."'";

        return $this->db->query($sql);
    }

    public function getrostergroup()
    {
        $this->db->from('groupshift');
        $query = $this->db->get();
        return $query;
    }

    public function getuseridfromnik()
    {
        $this->db->select('userid, name, deptid');
        $this->db->from('userinfo');
        return $this->db->get();
    }

    public function getdetail($awal, $akhir)
    {
        $this->db->from('rosterdetails');
        $this->db->where('rosterdate >=', date('Y-m-d', $awal));
        $this->db->where('rosterdate <=', date('Y-m-d', $akhir));
        return $this->db->get();
    }

    public function getnamefromnip($nip)
    {
        $this->db->select('name');
        $this->db->from('userinfo');
        $this->db->where('badgeNumber', $nip);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->name;
        }
        return false;

    }

    public function getshiftroster($userid, $date)
    {
        $this->db->select('absence');
        $this->db->from('rosterdetails');
        $this->db->where('userid', $userid);
        $this->db->where('rosterdate', date('Y-m-d', $date));
        $query = $this->db->get();
        if($query->num_rows()>=1)
        {
            return $query->row()->absence;
        }
        return false;

    }

    public function attendance()
    {
        $this->db->from('attendance');
        return $this->db->get();
    }

    public function attendancestatus($atid)
    {
        $this->db->select('atname');
        $this->db->from('attendance');
        $this->db->where('atid', $atid);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->atname;
        }
        return false;
    }

    public function absence()
    {
        $this->db->from('absence');
        return $this->db->get();
    }

    public function savedata($data)
    {
        if($this->db->insert('rosterdetails', $data))
        {
            return true;
        }
        return false;
    }

    public function shift()
    {
        $this->db->from('master_shift');
        return $this->db->get();
    }

    public function getshifttime($userid, $atdate)
    {
        $this->db->select('b.code_shift, b.name_shift, b.check_in, b.check_out');
        $this->db->from('master_shift b');
        $this->db->join('rosterdetails a', 'b.code_shift=a.absence');
        $this->db->where('a.userid', $userid);
        $this->db->where('a.rosterdate', $atdate);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    public function finddetail($userid, $atdate)
    {
        $this->db->from('rosterdetails');
        $this->db->where('userid', $userid);
        $this->db->where('rosterdate', $atdate);
        $query = $this->db->get();
        if($query->num_rows()>=1)
        {
            return true;
        }
        return false;
    }

    public function getusersession($id)
    {
        $this->db->select('username');
        $this->db->from('users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->username;
        }
        return false;
    }

    public function getnonworkingday()
    {
        $this->db->from('tbl_workingday');
        $this->db->where('status_workingday', 0);
        return $this->db->get();
    }

    public function holiday($orgid)
    {
        $this->db->from('holiday');
        if(!empty($orgid)) {
            $s = array();
            foreach($orgid as $ar)
                $s[] = (string)$ar;
            //$this->db->where_in('deptid', $s);

            $this->db->group_start();
            $ids_chunk = array_chunk($s,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('deptid', $s_ids);
            }
            $this->db->group_end();

            $this->db->or_where('deptid', '1');
        }
        return $this->db->get();
    }

    public function getdefaultshift()
    {
        $this->db->select('shift');
        $this->db->from('defaultshift');
        $this->db->where('id', 1);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->shift;
        }
        return false;
    }

    public function getabsen($userid, $tgl)
    {
        $this->db->select('a.userid, a.rosterdate, a.attendance, a.editby, c.name, a.notes');
        $this->db->from('rosterdetailsatt a');
        $this->db->join('userinfo c', 'a.userid=c.userid');
        $this->db->where('a.userid', $userid);
        $this->db->where('a.rosterdate', $tgl);
        return $this->db->get();
    }

    public function getabsengroup($userid, $tgl)
    {
        $this->db->from('groupshiftdetails a');
        $this->db->join('absence b', 'a.attendance=b.abid');
        $this->db->join('userinfo c', 'a.userid=c.userid');
        $this->db->where('a.userid', $userid);
        $this->db->where('a.rosterdate', $tgl);
        return $this->db->get();
    }

    public function getattend($userid, $tgl)
    {
        $this->db->select('a.userid, a.rosterdate, a.absence, d.attendance, d.editby, b.atname, c.name, a.notes');
        $this->db->from('rosterdetails a');
        $this->db->join('userinfo c', 'a.userid=c.userid');
        $this->db->join('rosterdetailsatt d', 'a.userid=d.userid and a.rosterdate=d.rosterdate');
        $this->db->join('attendance b', 'd.attendance=b.atid');
        $this->db->where('a.userid', $userid);
        $this->db->where('a.rosterdate', $tgl);
        return $this->db->get();
    }

    public function getattendgroup($userid, $tgl)
    {
        $this->db->from('groupshiftdetails a');
        $this->db->join('attendance b', 'a.attendance=b.atid');
        $this->db->join('userinfo c', 'a.userid=c.userid');
        $this->db->where('a.userid', $userid);
        $this->db->where('a.rosterdate', $tgl);
        return $this->db->get();
    }

    public function gettranslog($userid, $tgl)
    {
        $this->db->select('a.*,c.*',false);
        $this->db->from('checkinout a');
        $this->db->join('iclock d', 'a.sn=d.sn');
        $this->db->join('userinfo c', 'a.userid=c.userid');
        $this->db->where('a.userid', $userid);
        $this->db->where('d.is_reguler', 0);
        $this->db->where('date(a.checktime)', $tgl);
        return $this->db->get();
    }

    public function getjadwalupacara($userid, $tgl)
    {
        $this->db->select('a.userid, a.rosterdate,a.rostertime,a.rostertime_end, a.attendance, a.editby, c.name, a.notes');
        $this->db->from('rosterdetailsatt_upacara a');
        $this->db->join('userinfo c', 'a.userid=c.userid');
        $this->db->where('a.userid', $userid);
        $this->db->where('a.rosterdate', $tgl);
        return $this->db->get();
    }

    public function multi_array_key_exists( $needle, $haystack ) {

        foreach ( $haystack as $key => $value ) :

            if ( $needle == $key )
                return true;

            if ( is_array( $value ) ) :
                if ( $this->multi_array_key_exists( $needle, $value ) == true )
                    return true;
                else
                    continue;
            endif;

        endforeach;

        return false;
    }
}