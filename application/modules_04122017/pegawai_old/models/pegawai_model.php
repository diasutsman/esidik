<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:00 PM
 * absensi.kemendagri.go.id
 */
class Pegawai_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'*')." 
		 from view_employee
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        if (isset($dipaging) && $dipaging ==0) {
            //$sql .= " Order by keselon ASC, kgolru DESC ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }

    public function getDaftarHis($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'view_employee.*,(SELECT MAX(create_date) FROM data_proses WHERE data_proses.userid=view_employee.userid) AS lastcreate')." 
		 from view_employee
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        if (isset($dipaging) && $dipaging ==0) {
            //$sql .= " Order by keselon ASC, kgolru DESC ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }

    public function getEmployeeUser($userid)
    {
        $query = "SELECT username FROM users WHERE id =".$this->db->escape($userid);
        $row = $this->db->query($query);
        $data = $row->row_array();
        if($data)
            return $data['username'];
        else
            return '';
    }

    public function getEmployeeUserId($username)
    {
        $query = "SELECT id FROM users WHERE username ='".$this->db->escape_str($username)."'";
        $row = $this->db->query($query);
        $data = $row->row_array();
        if($data)
            return $data['id'];
        else
            return false;
    }

    public function getuserlevel($userid)
    {
        $this->db->select('user_level_id');
        $this->db->from('users');
        $this->db->where('id', $userid);
        $query = $this->db->get();
        if($query->num_rows()>=1)
        {
            return $query->row()->user_level_id;
        }
        return null;
    }

    public function getUserDep()
    {
        $user_level = $this->getuserlevel($this->session->userdata('s_userid'));
        $arr_dept = array();
        $this->db->select('ud_dept_id');
        $this->db->from('user_dept');
        $this->db->where('ud_level_id', $user_level);
        $query_dept = $this->db->get();
        if ($query_dept->num_rows() > 0)
        {
            foreach ($query_dept->result() as $row)
                $arr_dept[] = $row->ud_dept_id ;
        }
        return $arr_dept;
    }

    public function deptonall($orgid)
    {
        $depart=array();
        $deptidi = $orgid; $i=0;
        $depa = $this->getdept($deptidi);
        do {
            $deptid = array();
            foreach($depa->result() as $dep) {
                $deptid[]=$dep->deptid;
                $depart[]=$dep->deptid;
            }
            $this->adachild($deptid)?$i=1:$i=0;
            $depa = $this->getdeptparent($deptid);
        } while ($i==1);
        return $depart;
    }

    public function namadeptonall($orgid)
    {
        $sql="SELECT GROUP_CONCAT(deptname SEPARATOR ', ') as nama_unit
                FROM   departments
                WHERE  FIND_IN_SET(departments.deptid,'$orgid' )";
        //echo $sql;
        $qry = $this->db->query($sql)->row_array();
        return $qry["nama_unit"];
    }


    public function dapetiniduser($areaid)
    {
        $this->db->select('userid');
        $this->db->from('userinfo_attarea');
        $this->db->where_in('areaid', explode(',', $this->session->userdata('s_area')));
        /*$this->db->group_start();
        $ids_chunk = array_chunk(explode(',', $this->session->userdata('s_area')),25);
        foreach($ids_chunk as $sids)
        {
            $this->db->or_where_in('areaid', $sids);
        }
        $this->db->group_end();*/
        $this->db->group_by('userid');
        return $this->db->get();
    }

    public function getallemployee($organid, $start, $limit, $property, $direction, $empfilter)
    {
        $sql = "select * from view_employee ";
        $a = 0;

        if(!empty($organid)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $s = array();
            foreach($organid as $ar)
                $s[] = "'".$ar."'";
            $sql .= $ql."deptid in (".implode(',', $s).") ";
            $a = 1;
        }

        if($empfilter == 1) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $sql .= $ql."(jftstatus != '1' OR jftstatus != '2' OR jftstatus is null) ";
            $a = 1;
        } else if($empfilter == 2) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $sql .= $ql."fp = 0 and (jftstatus = '1' OR jftstatus = '2' OR jftstatus is null) ";
            $a = 1;
        } else {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $sql .= $ql."(jftstatus = '1' OR jftstatus = '2' OR jftstatus is null) ";
            $a = 1;
        }

        $sql .= "order by ".trim($property)." ".trim($direction)." limit ".(int)$limit." offset ".(int)$start;
        //echo $sql;
        return $this->db->query($sql);
    }

    public function getallemployeefind($organid, $nameemp, $property, $direction, $empfilter)
    {
        $sql = "select * from view_employee ";
        $a = 0;

        if(!empty($organid)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $s = array();
            foreach($organid as $ar)
                $s[] = "'".$ar."'";
            $sql .= $ql."deptid in (".implode(',', $s).") ";
            $a = 1;
        }
        /*
        if($empfilter == 1) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $sql .= $ql."(jftstatus != '1' OR jftstatus != '2' OR jftstatus is null) ";
            $a = 1;
        } else if($empfilter == 2) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $sql .= $ql."fp = 0 and (jftstatus = '1' OR jftstatus = '2' OR jftstatus is null) ";
            $a = 1;
        } else {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $sql .= $ql."(jftstatus = '1' OR jftstatus = '2' OR jftstatus is null) ";
            $a = 1;
        }
        */
        if($a==1) $ql = 'AND ';
        else $ql = 'where ';

        $sql .= $ql."(userid ilike '%".$this->db->escape_like_str($nameemp)."%' or name ilike '%".$this->db->escape_like_str($nameemp)."%') ";

        $sql .= "order by ".trim($property)." ".trim($direction);
        return $this->db->query($sql);
    }

    public function getallemployeecount($organid)
    {
        $sql = "select * from view_employee ";
        $a = 0;

        if(!empty($organid)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $s = array();
            foreach($organid as $ar)
                $s[] = "'".$ar."'";
            $sql .= $ql."deptid in (".implode(',', $s).") ";
            $a = 1;
        }
        $query = $this->db->query($sql);
        return $query->num_rows();
    }

    //get shift
    public function getallemploye()
    {
        $this->db->select('userid,id,badgeNumber,deptid,name,Title,placeBirthDate,nickname,hiredDate,resigndate,birthDate,Email');
        $this->db->from('userinfo');
        return $this->db->get();
    }

    public function getallemployeeonlyuserid($stspeg=null)
    {
        $this->db->select('userid');
        $this->db->from('userinfo');
        if ($stspeg != null)
        {
            $this->db->where_in('jenispegawai', $stspeg);
        } else {
            $this->db->where_in('jenispegawai', array(1, 2));
        }

        return $this->db->get();
    }

    public function getallemploye_hired($start_date,$finish_date = 0)
    {

        //$this->db->select('count(userid) as total');
        //$this->db->from('userinfo');
        //$this->db->where("hiredDate BETWEEN '".$start_date."' and '".$finish_date."'");
        $query = "SELECT COUNT(userid) AS total FROM userinfo WHERE hiredDate BETWEEN '".$this->db->escape_str($start_date)."' AND '".$this->db->escape_str($finish_date)."'";

        $row_hr = $this->db->query($query);
        $data_hr = $row_hr->row_array();
        return $data_hr['total'];
    }

    public function getallemploye_hired_1($date_1,$tipe){
        $query = "SELECT count(userid) as total FROM userinfo WHERE ";
        if($tipe == '1'){
            //$this->db->where('hiredDate >',$date_1);
            $query .= "hiredDate > '".$this->db->escape_str($date_1)."'";
        }
        else{
            //$this->db->where('hiredDate <',$data_1);
            $query .= "hiredDate < '".$this->db->escape_str($date_1)."'";
        }

        $row_hiredDate = $this->db->query($query);
        $data_hiredDate = $row_hiredDate->row_array();

        return $data_hiredDate['total'];
    }

    public function getEmployeAge($start_date,$finish_date){
        $query_age = "SELECT count(userid) as total FROM userinfo WHERE birthDate BETWEEN '".$this->db->escape_str($start_date)."' AND '".$this->db->escape_str($finish_date)."'";

        $row_age = $this->db->query($query_age);
        $data_age = $row_age->row_array();

        return $data_age['total'];
    }

    public function getEmployeAge1($tipe,$date){
        $query_age = "SELECT count(userid) as total FROM userinfo WHERE ";
        if($tipe == '1')
            $query_age .= "birthDate > '".$this->db->escape_str($date)."'";
        else
            $query_age .= "birthDate < '".$this->db->escape_str($date)."'";

        $row_age1 = $this->db->query($query_age);
        $data_age1 = $row_age1->row_array();
        return $data_age1['total'];
    }
    public function get_employe_perdate($date){
        $date_now = date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_employe_perdate = "SELECT count(late) as total FROM process WHERE date_shift BETWEEN '".$this->db->escape_str($date)."' AND '".$this->db->escape_str($date_now)."' ";
        $row_employe_perdate = $this->db->query($query_employe_perdate);
        $data_employe_perdate = $row_employe_perdate->row_array();
        return $data_employe_perdate['total'];
    }
    public function get_late_employe($date_late){
        $date_late_now = date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_late = "SELECT count(late) as total FROM process WHERE late != '' AND date_shift BETWEEN '".$this->db->escape_str($date_late)."' AND '".$this->db->escape_str($date_late_now)."' ";
        $row_late = $this->db->query($query_late);
        $data_late = $row_late->row_array();
        return $data_late['total'];
    }

    public function get_ed_employe($date_ed){
        $date_ed_now = date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_ed = "SELECT count(*) as total FROM process WHERE early_departure != '' AND date_shift BETWEEN '".$this->db->escape_str($date_ed)."' AND '".$this->db->escape_str($date_ed_now)."'";
        $row_ed = $this->db->query($query_ed);
        $data_ed = $row_ed->row_array();
        return $data_ed['total'];
    }

    public function employe_month($date_limit){
        $date_employe_now = date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_employe = "SELECT count(userid) as total FROM process WHERE date_shift BETWEEN '".$this->db->escape_str($date_limit)."' AND '".$this->db->escape_str($date_employe_now)."'";

        $row_employe = $this->db->query($query_employe);
        $data_employe = $row_employe->row_array();
        return $data_employe['total'];
    }

    public function overtime_before($date_otBefore){
        $date_employe_now = date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_otBefore = "SELECT count(*) as total FROM process WHERE ot_before != '' AND date_shift BETWEEN '".$this->db->escape_str($date_otBefore)."' AND '".$this->db->escape_str($date_employe_now)."'";
        $row_otBefore = $this->db->query($query_otBefore);
        $data_otBefore = $row_otBefore->row_array();
        return $data_otBefore['total'];
    }

    public function overtime_after($date_otAfter){
        $date_otAfter_now = date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_otAfter = "SELECT count(*) as total FROM process WHERE ot_after != '' AND date_shift BETWEEN '".$this->db->escape_str($date_otAfter)."' AND '".$this->db->escape_str($date_otAfter_now)."'";
        $row_otAfter = $this->db->query($query_otAfter);
        $data_otAfter = $row_otAfter->row_array();
        return $data_otAfter['total'];
    }

    public function getallemploye_page($page,$limit)
    {
        $this->db->select('userid,id,badgeNumber,deptid,name,Password,Card,Privilege,Title,placeBirthDate,nickname,hiredDate,resigndate,birthDate,Email');
        $this->db->from('userinfo');
        $this->db->limit($limit,$page);
        return $this->db->get();
    }

    public function getSearchEmploye($search)
    {
        $this->db->select('userid,id,badgeNumber,deptid,name,Password,Card,Privilege,Title,placeBirthDate,nickname,hiredDate,resigndate,birthDate,Email');
        $this->db->from('userinfo');
        $this->db->ilike('name', $search);
        return $this->db->get();
    }
    public function getEmployeAll()
    {
        return  $this->db->count_all('userinfo');
    }

    public function getEmployeArea($userid){
        $this->db->select('b.areaid');
        $this->db->from('userinfo_attarea a');
        $this->db->join('personnel_area b', 'a.areaid=b.areaid');
        $this->db->where('a.userid',$userid);
        $this->db->order_by('a.areaid', 'ASC');
        return $this->db->get();
    }

    public function getdeptname($deptid)
    {
        $this->db->select('deptname');
        $this->db->from('departments');
        $this->db->where('deptid',$deptid);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->deptname;
        }
        return null;
    }

    public function fpcount($userid)
    {
        $this->db->from('template');
        $this->db->where('userid', $userid);
        return $this->db->count_all_results();
    }

    public function savedata($data)
    {
        if($this->db->insert('userinfo', $data))
        {
            return true;
        }
        return false;
    }

    public function getallarea()
    {
        $this->db->select('areaid,areaname');
        $this->db->from('personnel_area');
        return $this->db->get();
    }

    public function getprivilege()
    {
        $this->db->from('privilege');
        return $this->db->get();
    }

    public function getprivilegeid($pri)
    {
        $this->db->select('id');
        $this->db->from('privilege');
        $this->db->where('privilege', $pri);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->id;
        }
        return false;
    }

    public function get_mf()
    {
        $this->db->select('Gender,count(Gender) as total');
        $this->db->from('userinfo');
        $this->db->where('Gender !=','NULL');
        $this->db->group_by('Gender');
        return $this->db->get();
    }

    public function get_total_mf(){
        //$this->db->select('count(*) as total');
        $this->db->from('userinfo');
        $this->db->where('Gender !=','NULL');
        return $this->db->count_all_results();

    }

    public function getallemploye_rep($page,$limit,$orgId='',$sort_field='',$sort_type='')
    {
        $this->db->select('userid,id,badgeNumber,deptid,name,Password,Card,Privilege,Title,placeBirthDate,nickname,hiredDate,resigndate,birthDate,Email');
        $this->db->from('userinfo');
        if(!empty($orgId) && !empty($orgId))
            $this->db->where('deptid',$orgId);

        if(!empty($sort_field) && !empty($sort_type))
            $this->db->order_by($sort_field,$sort_type);
        $this->db->limit($limit,$page);
        return $this->db->get();
    }

    public function getFID($user)
    {
        $this->db->select_max('fingerid');
        $this->db->from('template');
        $this->db->where('userid',$user);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->fingerid;
        }
        return false;
    }

    public function getsn($area) {
        $this->db->select('sn');
        $this->db->from('iclock');
        $this->db->where_in('areaid', $area);
        /*$this->db->group_start();
        $ids_chunk = array_chunk($area,25);
        foreach($ids_chunk as $sids)
        {
            $this->db->or_where_in('areaid', $sids);
        }
        $this->db->group_end();*/
        return $this->db->get();
    }

    public function getareauserinfo($userid) {
        $this->db->select('areaid');
        $this->db->from('userinfo_attarea');
        $this->db->where('userid', $userid);
        return $this->db->get();
    }

    public function getuserloginid($userid) {
        $this->db->select('user_login_id');
        $this->db->from('userinfo');
        $this->db->where_in('userid', $userid);
        /*$this->db->group_start();
        $ids_chunk = array_chunk($userid,25);
        foreach($ids_chunk as $sids)
        {
            $this->db->or_where_in('userid', $sids);
        }
        $this->db->group_end();*/
        return $this->db->get();
    }

    public function getsnarea($areaid) {
        $this->db->select('sn');
        $this->db->from('iclock');
        $this->db->where('areaid', $areaid);
        return $this->db->get();
    }

    public function getsnareain($areaid) {
        $this->db->select('sn');
        $this->db->from('iclock');
        $this->db->where_in('areaid', $areaid);
        /*$this->db->group_start();
        $ids_chunk = array_chunk($areaid,25);
        foreach($ids_chunk as $sids)
        {
            $this->db->or_where_in('areaid', $sids);
        }
        $this->db->group_end();*/
        return $this->db->get();
    }

    public function get_user($userid) {
        $this->db->select('userid, name, nickname, password, card, accgroup, timezones, privilege');
        $this->db->from('userinfo');
        $this->db->where('userid', $userid);
        return $this->db->get();
    }

    public function get_userid($userid) {
        $this->db->select('userid');
        $this->db->from('userinfo');
        $this->db->where('userid', $userid);
        $query = $this->db->get();
        if($query->num_rows()>=1)
        {
            return true;
        }
        return false;
    }

    public function getactiveemp($userid) {
        $this->db->select('active');
        $this->db->from('userinfo');
        $this->db->where('userid', $userid);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->active;
        }
        return false;
    }

    public function get_fp($userid) {
        $this->db->select('userid, fingerid, valid, template');
        $this->db->from('template');
        $this->db->where('userid', $userid);
        return $this->db->get();
    }

    public function get_traffic_late($lastMonth){
        $date_traffic_now = date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_traffic = "SELECT count(check_in) as total FROM process WHERE check_in != 'NULL' AND date_shift BETWEEN '".$this->db->escape_str($lastMonth)."' AND '".$this->db->escape_str($date_traffic_now)."' ";
        $row_traffic = $this->db->query($query_traffic);
        $data_traffic = $row_traffic->row_array();
        return $data_traffic['total'];
    }

    public function get_traffic_ed($dateLastMonth){
        $date_traffic_now = date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_traffic = "SELECT count(check_out) as total FROM process WHERE check_out != 'NULL' AND date_shift BETWEEN '".$this->db->escape_str($dateLastMonth)."' AND '".$this->db->escape_str($date_traffic_now)."' ";
        $row_traffic = $this->db->query($query_traffic);
        $data_traffic = $row_traffic->row_array();
        return $data_traffic['total'];
    }

    public function get_traffic_overtime($lastMonthOt){
        $date_otAfter_now = date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_otAfter = "SELECT count(check_out) as total FROM process WHERE check_out != 'NULL' OR check_in != 'NULL' AND date_shift BETWEEN '".$this->db->escape_str($lastMonthOt)."' AND '".$this->db->escape_str($date_otAfter_now)."'";
        $row_otAfter = $this->db->query($query_otAfter);
        $data_otAfter = $row_otAfter->row_array();
        return $data_otAfter['total'];
    }

    public function getdept($dept){
        $this->db->from('departments');
        $this->db->where_in('deptid', $dept);
        /*$this->db->group_start();
        $ids_chunk = array_chunk($dept,25);
        foreach($ids_chunk as $sids)
        {
            $this->db->or_where_in('deptid', $sids);
        }
        $this->db->group_end();*/
        return $this->db->get();
    }

    public function getdeptparent($dept){
        $this->db->from('departments');
        $this->db->where_in('parentid', $dept);
        /*$this->db->group_start();
        $ids_chunk = array_chunk($dept,25);
        foreach($ids_chunk as $sids)
        {
            $this->db->or_where_in('deptid', $sids);
        }
        $this->db->group_end();*/
        return $this->db->get();
    }

    public function adachild($dept) {
        if(!empty($dept)) {
            $this->db->from('departments');
            $this->db->where_in('parentid', $dept);
            /*$this->db->group_start();
            $ids_chunk = array_chunk($dept,25);
            foreach($ids_chunk as $sids)
            {
                $this->db->or_where_in('parentid', $sids);
            }
            $this->db->group_end();*/
            $query = $this->db->get();
            if($query->num_rows() > 0) return true;
            else return false;
        } else return false;
    }

    public function getemployeforexcel($areaid, $organid)
    {
        $sql = "select userid, badgenumber, name, nickname, password, card, privilege, deptid, title, gender, birthdate, hireddate, email, area_id, emptype, kelasjabatan from view_employee ";
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
        return $this->db->query($sql);
    }

    public function getresignuserid()
    {
        $this->db->select('userid');
        $this->db->from('userinfo');
        $this->db->where('resigndate', date('Y-m-d'));
        return $this->db->get();
    }

    public function getuserid($orgid)
    {
        $this->db->select('userid');
        $this->db->from('userinfo');
        $this->db->where_in('deptid', $orgid);
        /*$this->db->group_start();
        $ids_chunk = array_chunk($orgid,25);
        foreach($ids_chunk as $sids)
        {
            $this->db->or_where_in('deptid', $sids);
        }
        $this->db->group_end();*/
        return $this->db->get();
    }

    public function areaonall($areaid)
    {
        $depart=array();
        $deptidi = $areaid; $i=0;
        $depa = $this->getarea($deptidi);
        do {
            $deptid = array();
            foreach($depa->result() as $dep) {
                $deptid[]=$dep->areaid;
                $depart[]=$dep->areaid;
            }

            $this->areachild($deptid)?$i=1:$i=0;
            $depa = $this->getareaparent($deptid);
        } while ($i==1);
        return $depart;
    }

    public function getarea($areaid){
        $this->db->from('personnel_area');
        $this->db->where_in('areaid', $areaid);
        /*$this->db->group_start();
        $ids_chunk = array_chunk($areaid,25);
        foreach($ids_chunk as $sids)
        {
            $this->db->or_where_in('areaid', $sids);
        }
        $this->db->group_end();*/
        return $this->db->get();
    }

    public function areachild($areaid) {
        if(!empty($areaid)) {
            $this->db->from('personnel_area');
            $this->db->where_in('parent_id', $areaid);
            /*$this->db->group_start();
            $ids_chunk = array_chunk($areaid,25);
            foreach($ids_chunk as $sids)
            {
                $this->db->or_where_in('parent_id', $sids);
            }
            $this->db->group_end();*/
            $query = $this->db->get();
            if($query->num_rows() > 0) return true;
            else return false;
        } else return false;
    }

    public function getareaparent($areaid){
        $this->db->from('personnel_area');
        $this->db->where_in('parent_id', $areaid);
        /*$this->db->group_start();
        $ids_chunk = array_chunk($areaid,25);
        foreach($ids_chunk as $sids)
        {
            $this->db->or_where_in('parent_id', $sids);
        }
        $this->db->group_end();*/
        return $this->db->get();
    }

    public function getrealabsensi($userid,$startdate,$enddate){
        $sql="SELECT IFNULL(COUNT(DISTINCT DATE(checktime)),0) as jmlHari 
        FROM checkinout 
        WHERE userid='$userid' AND DATE(checktime) BETWEEN '$startdate' and '$enddate'";

        return $this->db->query($sql)->row_array();
    }

    public function gethistabsensi($userid,$startdate,$enddate)
    {
        $sql="SELECT IFNULL(COUNT(DISTINCT DATE(tanggal)),0) as jmlHari 
        FROM data_uang_makan 
        WHERE jum_hadir>0 and userid='$userid' AND DATE(tanggal) BETWEEN '$startdate' and '$enddate'";

        return $this->db->query($sql)->row_array();
    }

    public function getlisthistabsensi($userid,$startdate,$enddate,$depid)
    {
        $sql="SELECT golongan,
              IFNULL(COUNT(jum_hadir),0) AS jum_hadir,
              tarif,
              IFNULL(SUM(jml_kotor),0) AS jml_kotor,
              pajak_persen,
              IFNULL(SUM(jml_pajak),0) AS jml_pajak,
              IFNULL(SUM(bersih),0) AS bersih
        FROM data_uang_makan 
        WHERE jum_hadir>0 AND userid='$userid' AND (DATE(tanggal) BETWEEN '$startdate' and '$enddate')
         and $depid='$depid'
        GROUP BY userid,tarif,pajak_persen";

        return $this->db->query($sql);
    }
}