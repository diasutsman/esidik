<?php 

class Report_model extends CI_Model
{
    public function attendance() {
        return $this->db->get('attendance');
    }

    public function getdept() {
        $this->db->order_by('deptid','ASC');
        return $this->db->get('departments');
    }

    public function getcompany() {
        return $this->db->get('company');
    }


    public function getuserid($id)
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

    public function getemployemail()
    {
        $this->db->select('userid, name, Email, deptid');
        $this->db->from('userinfo');
        return $this->db->get();
    }

    public function getbadgenumber($userid) {
        $this->db->select('badgenumber');
        $this->db->from('userinfo');
        $this->db->where('userid', $userid);
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->badgenumber;
        }
        return false;
    }

    public function getfunctionkey($fkey,$type) {
        if($type=='name'){
            $this->db->select('State');
            $this->db->from('state');
            $this->db->where('ID', $fkey);
            $query = $this->db->get();
            return $query->row()->State;
        }else
            return $fkey;
    }

    public function getverifycode($vcode,$type) {
        if($type=='name'){
            $this->db->select('Verification');
            $this->db->from('verification');
            $this->db->where('ID', $vcode);
            $query = $this->db->get();
            return $query->row()->Verification;
        }else
            return $vcode;
    }

    public function holidaycount($tanggal) {

        $sql="SELECT id
                FROM holiday
                WHERE '$tanggal' BETWEEN startdate AND enddate";

        /*$this->db->from('holiday');
        $this->db->where('holiday', $tanggal);
        $query = $this->db->get();*/
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
            return true;
        } else
        {
            $th= date("Y",strtotime($tanggal));
            $sql="SELECT STR_TO_DATE(CONCAT(startdate,'-$th'), '%d-%m-%Y') AS startdate,
                STR_TO_DATE(CONCAT(enddate,'-$th'), '%d-%m-%Y') AS enddate
                FROM defa_holiday
                WHERE '$tanggal' BETWEEN STR_TO_DATE(CONCAT(startdate,'-$th'), '%d-%m-%Y') 
                AND STR_TO_DATE(CONCAT(enddate,'-$th'), '%d-%m-%Y')";
            $query = $this->db->query($sql);
            if($query->num_rows()>0)
            {
                return true;
            }
        }
        return false;
    }

    public function absence(){
        return $this->db->get('absence');
    }

    public function getArrAtt($start_date,$end_date,$userid){

        $whereUserid = "";
        if(!empty($userid)){
            $whereUserid = " and userid = '".$this->db->escape_str($userid)."'";
        }

        $queryAtt = "select userid,attendance,count(userid) as total 
                     from rosterdetails 
                     where rosterdate between '".$this->db->escape_str($start_date)."' and '".$this->db->escape_str($end_date)."' ".$whereUserid." group by attendance,userid";
        //echo $queryAtt;
        $temp_queryAtt = $this->db->query($queryAtt);
        return $temp_queryAtt->result();
    }

    public function getArrPro($start_date,$end_date,$userid){

        $whereuseridProcess = "";
        if(!empty($userid)){
            $whereuseridProcess = "and userid = '".$this->db->escape_str($userid)."'";
        }

        $queryProcess = "select userid,late,early_departure,(ot_before+ot_after) as overtime,
                        workinholiday,edit_come,edit_home 
                        from process 
                        where date_shift BETWEEN '".$this->db->escape_str($start_date)."' and '".$this->db->escape_str($end_date)."' ".$whereuseridProcess."";

        $temp_queryProcess = $this->db->query($queryProcess);
        return $temp_queryProcess->result();
    }

    public function searchAtt($array,$key,$val,$att){
        //var_dump($array);

        foreach($array as $row){
            if(($row->{'userid'} == $val) && ($row->{'attendance'} == $att)){
                return $row->{'total'};
            }
        }

        return NULL;
    }

    public function searchProcess($dataArr,$key,$value){
        $dt = array();
        $lt =0;
        $ed =0;
        $ot =0;
        $wh =0;
        $ec =0;
        $eh =0;

        foreach($dataArr as $rowProcess){
            if($rowProcess->{'userid'} == $value){

                if($rowProcess->{'late'} != "0"){$lt++;}
                if($rowProcess->{'early_departure'} != "0"){$ed++;}
                if($rowProcess->{'overtime'} != "0"){$ot++;}
                if($rowProcess->{'workinholiday'} != "0"){$wh++;}
                if($rowProcess->{'edit_come'} != ""){$ec++;}
                if($rowProcess->{'edit_home'} != ""){$eh++;}
            }
        }
        $dt['late'] = $lt;
        $dt['Ed'] = $ed;
        $dt['ot'] = $ot;
        $dt['Wh'] = $wh;
        $dt['come'] = $ec;
        $dt['home'] = $eh;

        return $dt;
    }

    public function countAtt_($arrRoster,$tipeAtt){

        $rtnArray = 0;
        foreach($arrRoster as $rowRoster){
            if($rowRoster->{'attendance'} == $tipeAtt){
                $rtnArray =  $rowRoster->{'total'} + $rtnArray;
            }
        }

        return $rtnArray;

    }

    public function countAtt($userid,$arrAtt,$arrRoster){

        $temp_att = 0;
        foreach($arrRoster as $rowRoster){
            if($rowRoster->{'userid'} == $userid){

                if(in_array($rowRoster->{'attendance'},$arrAtt)){
                    //$temp_att++;
                    $temp_att = $temp_att + $rowRoster->{'total'};
                }
            }
        }

        return $temp_att;
    }

    public function getnonWorkDay(){
        $queryNonWorkDay = "select id_day from tbl_workingday where status_workingday = '0'";
        $tempNonWorkingDay = $this->db->query($queryNonWorkDay);
        $ghWd = $tempNonWorkingDay->result();
        $tempArrNWD = array();
        foreach($ghWd as $rowNonWd){
            $tempArrNWD[] = $rowNonWd->{'id_day'};
        }

        return $tempArrNWD;
    }

    public function getDayHoliday($startTime,$endTime){
        $queryHoliday = "select id_dateHoliday,date_holiday,end_date_holiday,holiday_information 
                         from tbl_holiday 
                         where date_holiday between '".$this->db->escape_str($startTime)."' and '".$this->db->escape_str($endTime)."'";

        $temp_queryHoliday = $this->db->query($queryHoliday);
        return $temp_queryHoliday->result();
    }


   /* public function recap1($org)
    {
        if($org!="")
            $temp_kondisi = "deptid IN (".$org.")";
        else
            $temp_kondisi = "1";

        $queryReportRecapitulation = "SELECT badgenumber as userid,name 
                    FROM userinfo WHERE ".$temp_kondisi." LIMIT 0,400";

        return $this->db->query($queryReportRecapitulation);
    }*/

    public function getallemployee($iduser, $organid, $nameemp, $start, $limit, $property, $direction)
    {
        $this->db->from('userinfo a');
        if(!empty($iduser)) {
            $this->db->where_in('a.userid', $iduser);
            /*if (is_array($iduser))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($iduser,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('a.userid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('a.userid', $iduser);
            }*/
        }
        if($organid!='' && $nameemp!='') {
            $this->db->join('departments b', 'a.deptid = b.deptid');
            $this->db->where_in('a.deptid', $organid);
            /*if (is_array($organid))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($organid,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('a.deptid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('a.deptid', $organid);
            }*/

            $this->db->like('a.name', $nameemp);
            $this->db->or_like('a.userid', $nameemp);
        } else if($organid!='') {
            $this->db->join('departments b', 'a.deptid = b.deptid');
            $this->db->where_in('a.deptid', $organid);
            /*if (is_array($organid))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($organid,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('a.deptid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('a.deptid', $organid);
            }*/
        } else if($nameemp!='') {
            $this->db->like('a.name', $nameemp);
            $this->db->or_like('a.userid', $nameemp);
        }
        $this->db->limit($limit, $start);
        $this->db->order_by('a.'.$property, $direction);
        return $this->db->get();
    }

    public function getallemployeecount($iduser, $organid, $nameemp)
    {
        $this->db->from('userinfo a');
        if(!empty($iduser)) {
            $this->db->where_in('a.userid', $iduser);
            /*if (is_array($iduser))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($iduser,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('a.userid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('a.userid', $iduser);
            }*/
        }
        if($organid!='' && $nameemp!='') {
            $this->db->join('departments b', 'a.deptid = b.deptid');
            $this->db->where_in('a.deptid', $organid);
            /*if (is_array($organid))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($organid,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('a.deptid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('a.deptid', $organid);
            }*/
            $this->db->like('a.name', $nameemp);
            $this->db->or_like('a.userid', $nameemp);
        } else if($organid!='') {
            $this->db->join('departments b', 'a.deptid = b.deptid');
            $this->db->where_in('a.deptid', $organid);
            /*if (is_array($organid))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($organid,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('a.deptid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('a.deptid', $organid);
            }*/
        } else if($nameemp!='') {
            $this->db->like('a.name', $nameemp);
            $this->db->or_like('a.userid', $nameemp);
        }
        return $this->db->count_all_results();
    }
	public function gettranslog3($uid,$tgl)
    {
		$mode="Upacara";
		$this->db->select('a.userid, a.sn, a.checktime, a.checktype, a.verifycode, a.editdate, a.editby');
        $this->db->from('checkinout a');
        
            $this->db->join('iclock c', 'a.sn=c.sn');
            $this->db->where('c.is_reguler', $mode=="Upacara"? 0:1 );
         
        $this->db->where('a.userid', $uid);
        $this->db->where('date(a.checktime) =', date('Y-m-d', $tgl));
        

        $this->db->order_by('a.checktime', 'asc');
        return $this->db->get();
    }
	
    public function gettranslog($datestart, $datestop, $userid,$mode="All")
    {
        $this->db->select('a.userid, a.sn, a.checktime, a.checktype, a.verifycode, a.editdate, a.editby');
        $this->db->from('checkinout a');
        if ($mode=="Reguler" || $mode=="Upacara"){
            $this->db->join('iclock c', 'a.sn=c.sn');
            $this->db->where('c.is_reguler', $mode=="Upacara"? 0:1 );
            //echo $mode=="Upacara";
        }
        $this->db->where('a.userid', $userid);
        $this->db->where('date(a.checktime) >=', date('Y-m-d', $datestart));
        $this->db->where('date(a.checktime) <=', date('Y-m-d', $datestop));

        $this->db->order_by('a.checktime', 'asc');
        return $this->db->get();
    }
	
	public function gettranslog2($datestart, $datestop, $userid,$mode="All")
    {
        $this->db->select('a.userid, a.sn, a.checktime, a.checktype, a.verifycode, a.editdate, a.editby');
        $this->db->from('checkinout a');
        if ($mode=="Reguler" || $mode=="Upacara"){
            $this->db->join('iclock c', 'a.sn=c.sn');
            $this->db->where('c.is_reguler', $mode=="Upacara"? 0:1 );
            //echo $mode=="Upacara";
        } 
        $this->db->where('a.userid', $userid);
        $this->db->where('date(a.checktime) >=', date('Y-m-d',strtotime($datestart)));
        $this->db->where('date(a.checktime) <=', date('Y-m-d', strtotime($datestop)));

        $this->db->order_by('a.checktime', 'asc');
        return $this->db->get();
    }

    public function gettranslogbydate($tanggal, $areaid,$stspeg=null,$jnspeg=null,$mode="All")
    {
        $this->db->select('a.userid, a.sn, a.checktime, a.checktype, a.verifycode, a.editdate, a.editby');
        $this->db->from('checkinout a');
        if ($mode=="Reguler" || $mode=="Upacara"){
            $this->db->join('iclock c', 'a.sn=c.sn');
            $this->db->where('c.is_reguler', $mode=="Upacara"?0:1 );
        }
        $this->db->join('userinfo_attarea b', 'a.userid=b.userid');
        $this->db->join('userinfo c', 'a.userid=c.userid');
        //$this->db->join('iclock', 'checkinout.SN=iclock.SN');
        $this->db->where('date(checktime)', $tanggal);
        if(!empty($areaid)) {
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
        }

        if ($stspeg != null)
        {
            $this->db->where_in('c.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('c.jenispegawai', $jnspeg);
        }
        $this->db->group_by('a.userid, a.sn, a.checktime, a.checktype, a.verifycode, a.editdate, a.editby');
        return $this->db->get();
    }

    public function gettranslogbydateorg($tanggal, $orgid, $areaid,$stspeg=null,$jnspeg=null,$mode='All')
    {
        $this->db->select('a.userid, a.sn, a.checktime, a.checktype, a.verifycode, a.editdate, a.editby');
        $this->db->from('checkinout a');
        if ($mode=="Reguler" || $mode=="Upacara"){
            $this->db->join('iclock c', 'a.sn=c.sn');
            $this->db->where('c.is_reguler', $mode=="Upacara"?0:1 );
        }

        $this->db->join('userinfo b', 'a.userid=b.userid');
        $this->db->join('userinfo_attarea c', 'a.userid=c.userid');
        $this->db->where('date(a.checktime)', $tanggal);
        $this->db->where_in('b.deptid', $orgid);
        /*if (is_array($orgid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($orgid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('b.deptid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('b.deptid', $orgid);
        }*/

        if ($stspeg != null)
        {
            $this->db->where_in('b.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('b.jenispegawai', $jnspeg);
        }
        if(!empty($areaid))
            $this->db->where_in('c.areaid', $areaid);
        $this->db->group_by('a.userid, a.sn, a.checktime, a.checktype, a.verifycode, a.editdate, a.editby');
        return $this->db->get();
    }

    public function gettranslogbydateuser($tanggal, $userid,$stspeg=null,$jnspeg=null,$mode="All")
    {
        $this->db->select('a.userid, a.sn, a.checktime, a.checktype, a.verifycode, a.editdate, a.editby');
        $this->db->from('checkinout a');
        if ($mode=="Reguler" || $mode=="Upacara"){
            $this->db->join('iclock d', 'a.sn=d.sn');
            $this->db->where('d.is_reguler', $mode=="Upacara"?0:1 );
        }
        $this->db->join('userinfo_attarea b', 'a.userid=b.userid');
        $this->db->join('userinfo c', 'a.userid=c.userid');
        $this->db->where('date(checktime)', $tanggal);
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
        if ($stspeg != null)
        {
            $this->db->where_in('c.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('c.jenispegawai', $jnspeg);
        }
        $this->db->group_by('a.userid, a.sn, a.checktime, a.checktype, a.verifycode, a.editdate, a.editby');
        return $this->db->get();
    }

    public function gettranslogorg($orgid, $datestart, $datestop)
    {
        $this->db->select('a.badgenumber, a.name, b.SN, b.checktime, b.checktype, b.verifycode, b.editdate, b.editby');
        $this->db->from('checkinout b');
        $this->db->join('userinfo a', 'a.userid=b.userid');
        $this->db->where('a.deptid', $orgid);
        $this->db->where('date(checktime) >=', date('Y-m-d', $datestart));
        $this->db->where('date(checktime) <=', date('Y-m-d', $datestop));
        return $this->db->get();
    }

    public function getattbydateorg($areaid, $tanggal, $orgid,$stspeg=null,$jnspeg=null)
    {
        $this->db->select('a.userid, a.date_shift, a.shift_in, a.shift_out, a.date_in, a.check_in, a.date_out, a.check_out, 
		a.break_out, a.break_in, a.late, a.early_departure, a.ot_before, a.ot_after, a.workinholiday, a.attendance,
		b.badgenumber, b.name, b.deptid,a.notes,process_upacara.date_shift as date_shift2, process_upacara.shift_in as shift_in2, process_upacara.shift_out as shift_out2, process_upacara.date_in as date_in2, process_upacara.check_in as check_in2,process_upacara.attendance as attendance2');
        $this->db->from('process a');
        $this->db->join('userinfo b', 'a.userid=b.userid');
        $this->db->join('userinfo_attarea c', 'a.userid=c.userid');
        $this->db->join('process_upacara','process_upacara.userid=a.userid and process_upacara.date_shift=a.date_shift','LEFT');

        $this->db->where('a.date_shift', $tanggal);
        $this->db->where_in('b.deptid', $orgid);
        /*if (is_array($orgid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($orgid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('b.deptid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('b.deptid', $orgid);
        }*/
        if ($stspeg != null) $this->db->where_in('b.jftstatus', $stspeg);
        if ($jnspeg != null) $this->db->where_in('b.jenispegawai', $jnspeg);
        if(!empty($areaid)) {
            $this->db->where_in('c.areaid', $areaid);
            /*if (is_array($areaid))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($areaid,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('c.areaid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('c.areaid', $areaid);
            }*/
        }

        $this->db->group_by('a.userid, a.date_shift, a.shift_in, a.shift_out, a.date_in, a.check_in, a.date_out, a.check_out, 
		a.break_out, a.break_in, a.late, a.early_departure, a.ot_before, a.ot_after, a.workinholiday, a.attendance,
		b.badgenumber, b.name, b.deptid');
        //$qry= $this->db->get_compiled_select('process');
        //echo "kuprest".$qry;
        //$this->db->reset_query();
        return $this->db->get();
    }

    public function getattbydate($areaid, $tanggal,$stspeg=null,$jnspeg=null)
    {
        $this->db->select('a.userid, a.date_shift, a.shift_in, a.shift_out,  a.date_in, a.check_in, a.date_out, a.check_out, 
		a.break_out, a.break_in, a.late, a.early_departure, a.ot_before, a.ot_after, a.workinholiday, a.attendance,
		b.badgenumber, b.name, b.deptid,a.notes,process_upacara.date_shift as date_shift2, process_upacara.shift_in as shift_in2, process_upacara.shift_out as shift_out2, process_upacara.date_in as date_in2, process_upacara.check_in as check_in2,process_upacara.attendance as attendance2');
        $this->db->from('process a');
        $this->db->join('userinfo b', 'a.userid=b.userid');
        $this->db->join('userinfo_attarea c', 'a.userid=c.userid');
        $this->db->join('process_upacara','process_upacara.userid=a.userid and process_upacara.date_shift=a.date_shift','LEFT');
        $this->db->where('a.date_shift', $tanggal);
        if(!empty($areaid)) {
            $this->db->where_in('c.areaid', $areaid);
            /*if (is_array($areaid))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($areaid,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('c.areaid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('c.areaid', $areaid);
            }*/
        }

        if ($stspeg != null)
        {
            $this->db->where_in('b.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('b.jenispegawai', $jnspeg);
        }
        $this->db->group_by('a.userid, a.date_shift, a.shift_in, a.shift_out, , a.date_in, a.check_in, a.date_out, a.check_out, 
		a.break_out, a.break_in, a.late, a.early_departure, a.ot_before, a.ot_after, a.workinholiday, a.attendance,
		b.badgenumber, b.name, b.deptid');
        //$qry= $this->db->get_compiled_select('process');
        //echo "kuprest 2 ".$qry
        //    $this->db->reset_query();
        return $this->db->get();
    }

    public function getattbydateuser($tanggal, $userid,$stspeg=null,$jnspeg=null)
    {
        $this->db->select('a.userid, a.date_shift, a.shift_in, a.shift_out, a.date_in, a.check_in, a.date_out, a.check_out, 
		a.break_out, a.break_in, a.late, a.early_departure, a.ot_before, a.ot_after, a.workinholiday, a.attendance,
		b.badgenumber, b.name, b.deptid,a.notes,process_upacara.date_shift as date_shift2, process_upacara.shift_in as shift_in2, process_upacara.shift_out as shift_out2, process_upacara.date_in as date_in2, process_upacara.check_in as check_in2,process_upacara.attendance as attendance2');
        $this->db->from('process a');
        $this->db->join('userinfo b', 'a.userid=b.userid');
        $this->db->join('process_upacara','process_upacara.userid=a.userid and process_upacara.date_shift=a.date_shift','LEFT');
        $this->db->where('a.date_shift', $tanggal);
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
        if ($stspeg != null)
        {
            $this->db->where_in('b.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('b.jenispegawai', $jnspeg);
        }
        $this->db->group_by('a.userid, a.date_shift, a.shift_in, a.shift_out, a.date_in, a.check_in, a.date_out, a.check_out, 
		a.break_out, a.break_in, a.late, a.early_departure, a.ot_before, a.ot_after, a.workinholiday, a.attendance,
		b.badgenumber, b.name, b.deptid');
        //$qry= $this->db->get_compiled_select('process');

        //echo "kuprest 3 ".$mode;
        //$this->db->reset_query();
        return $this->db->get();
    }



    public function gettermid()
    {
        $this->db->select('sn, terminal_id, alias');
        $this->db->from('iclock');
        return $this->db->get();
    }

    public function getstate()
    {
        $this->db->from('state');
        return $this->db->get();
    }

    public function getatt()
    {
        $this->db->from('attendance');
        return $this->db->get();
    }

    public function getabs()
    {

        $this->db->from('absence');
        return $this->db->get();
    }

    public function getattAktif()
    {
        $this->db->where("state",1);
        $this->db->from('attendance');
        return $this->db->get();
    }

    public function getabsAktif()
    {
        $this->db->where("state",1);
        $this->db->from('absence');
        return $this->db->get();
    }

    public function getholiday()
    {
        $this->db->from('tbl_holiday');
        return $this->db->get();
    }

    public function getrd($datestart, $datestop)
    {
        $this->db->select('rosterdate, attendance');
        $this->db->from('rosterdetails');
        $this->db->where('rosterdate >=', $datestart);
        $this->db->where('rosterdate <=', $datestop);
        $this->db->where('attendance !=', '');
        $this->db->where('attendance !=', 'NWDS');
        $this->db->not_like('attendance ', 'AB');
        return $this->db->get();
    }

    public function getchilddepart($orgid)
    {
        $this->db->order_by('deptid','ASC');
        $this->db->from('departments');
        $this->db->where('deptid', $orgid);
        return $this->db->get();
    }

    public function itungan($angka)
    {
        $totah = floor($angka/3600);
        $totaj = floor(($angka % 3600) / 60);
        //$totas = $totaj % 60;
        return str_pad($totah,2,'0',STR_PAD_LEFT).':'.str_pad($totaj,2,'0',STR_PAD_LEFT);
    }

    public function itungan2($angka)
    {
        $totah = floor($angka/3600);
        $totaj = floor(($angka % 3600) / 60);
        //$totas = $totaj % 60;
        return $totah.' hour(s), '.$totaj.' minute(s)';
    }

    public function getattlog($datestart, $datestop, $userid)
    {
        $this->db->select('process.*,process_upacara.date_shift as date_shift2, process_upacara.shift_in as shift_in2, process_upacara.shift_out as shift_out2, process_upacara.date_in as date_in2, process_upacara.check_in as check_in2,process_upacara.attendance as attendance2',false);
        $this->db->from('process');
        $this->db->join('process_upacara','process_upacara.userid=process.userid and process_upacara.date_shift=process.date_shift','LEFT');
        $this->db->where('process.userid', $userid);
        $this->db->where('process.date_shift >=', date('Y-m-d', $datestart));
        $this->db->where('process.date_shift <=', date('Y-m-d', $datestop));
        $this->db->order_by('process.date_shift', 'asc');
        //$qry= $this->db->get_compiled_select();
        //$this->db->reset_query();
        //echo $mode.' '.$qry;
        return $this->db->get();
    }

    public function getattlogo($datestart, $datestop, $userid)
    {
        $this->db->select('process.*');
        $this->db->from('process');
        $this->db->where('userid', $userid);
        $this->db->where('date_shift >=', date('Y-m-01', $datestart));
        $this->db->where('date_shift <=', date('Y-m-t', $datestop));
        $this->db->order_by('date_shift', 'asc');
        return $this->db->get();
    }

    public function getsplit($datestart, $datestop, $userid)
    {
        $this->db->from('processsplit');
        $this->db->where('userid', $userid);
        $this->db->where('date_shift >=', date('Y-m-d', $datestart));
        $this->db->where('date_shift <=', date('Y-m-d', $datestop));
        $this->db->order_by('date_shift', 'asc');
        return $this->db->get();
    }

    public function getallemployeedetails($stspeg=null,$jnspeg=null)
    {
        $this->db->select('a.userid, a.title, a.badgenumber, a.hireddate, a.name, a.deptid, a.eselon, a.golru,a.kelasjabatan');
        $this->db->from('userinfo a');
        $this->db->join('userinfo_attarea c', 'a.userid=c.userid');
        if ($stspeg != null)
        {
            $this->db->where_in('a.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('a.jenispegawai', $jnspeg);
        }
        //$this->db->order_by('a.id', 'ASC');
        $this->db->order_by('a.kelasjabatan', 'DESC');
        $this->db->group_by('a.id, a.userid, a.title, a.badgenumber, a.hireddate, a.name, a.deptid, a.eselon, a.golru,a.kelasjabatan');
        return $this->db->get();
    }

    public function getorgemployeedetails($orgid,$stspeg=null,$jnspeg=null)
    {
        $this->db->select('a.userid, a.title, a.badgenumber, a.hireddate, a.name, a.deptid, a.eselon, a.golru,a.kelasjabatan');
        $this->db->from('userinfo a');
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
        if ($stspeg != null)
        {
            $this->db->where_in('a.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('a.jenispegawai', $jnspeg);
        }
        $this->db->group_by('a.id, a.userid, a.title, a.badgenumber, a.hireddate, a.name, a.deptid, a.eselon, a.golru,a.kelasjabatan');
        //$this->db->order_by('a.id', 'ASC');
       /* $this->db->order_by('a.golru', 'DESC');
        $this->db->order_by('a.eselon', 'ASC');*/
        $this->db->order_by('a.kelasjabatan', 'DESC');


        return $this->db->get();
    }

    public function getorgemployeedetailsxx($orgid,$stspeg=null,$jnspeg=null)
    {
        $this->db->select('a.userid, a.title, a.badgenumber, a.hireddate, a.name, a.deptid, a.kelasjabatan, a.tunjanganprofesi, a.jftstatus, a.jenisjabatan, a.jenispegawai, a.kedudukan, a.golru');
        $this->db->select('a.plt_deptid,a.tmt_plt,a.payable,a.plt_eselon,a.eselon,a.plt_jbtn,a.plt_sk,a.plt_kelasjabatan,a.npwp,a.no_rekening,a.tmtprofesi,a.tmtkedudukan');
        $this->db->from('userinfo a');
        //$this->db->join('mastertunjangan d', 'a.kelasjabatan=d.kelasjabatan', 'left');
        $this->db->group_start();
        $this->db->where_in('deptid', $orgid);
        $this->db->or_group_start();
        $this->db->where_in('plt_deptid', $orgid);
        $this->db->where('tmt_plt is Not NULL', null,FALSE);
        $this->db->where('tmt_plt !=', "'0000-00-00'",FALSE);
        $this->db->group_end();
        $this->db->group_end();
        if ($stspeg != null)
        {
            $this->db->where_in('jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('jenispegawai', $jnspeg);
        }
        $this->db->group_by('a.id, a.userid, a.title, a.badgenumber, a.hireddate, a.name, a.deptid, a.kelasjabatan, a.tunjanganprofesi, a.jftstatus, a.jenisjabatan, a.jenispegawai, a.kedudukan, a.golru,a.plt_deptid,a.tmt_plt,a.payable,a.plt_eselon,a.eselon,a.tmtkedudukan');
        //$this->db->order_by('a.kelasjabatan', 'DESC');
        $this->db->order_by('a.kelasjabatan', 'DESC');
        $this->db->order_by('a.golru', 'DESC');
        $this->db->order_by('a.eselon', 'ASC');

        return $this->db->get();
    }

    public function getuseremployeedetails($userid,$stspeg=null,$jnspeg=null)
    {
        $this->db->select('userid, title, badgenumber, hireddate, name, deptid, eselon, golru');
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
        if ($stspeg != null)
        {
            $this->db->where_in('jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('jenispegawai', $jnspeg);
        }
        /*$this->db->order_by('golru', 'DESC');
        $this->db->order_by('eselon', 'ASC');*/
        $this->db->order_by('kelasjabatan', 'DESC');
        //$this->db->order_by('id', 'DESC');
        return $this->db->get();
    }

    public function getuseremployeedetailsxx($userid,$stspeg=null,$jnspeg=null)
    {
        $this->db->select("a.userid, a.title, a.badgenumber, a.hireddate, a.name, a.deptid, a.kelasjabatan, ");
        $this->db->select("a.tunjanganprofesi, b.tunjangan, a.jftstatus, a.jenisjabatan, a.jenispegawai, a.kedudukan, a.golru");
        $this->db->select("a.plt_deptid,a.tmt_plt,a.payable,a.plt_eselon,a.eselon,a.plt_jbtn,a.plt_sk,a.npwp,a.plt_kelasjabatan,a.no_rekening,a.tmtprofesi,a.tmtkedudukan");
        $this->db->from('userinfo a');
        $this->db->join('mastertunjangan b', 'a.kelasjabatan=b.kelasjabatan', 'left');
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
        if ($stspeg != null)
        {
            $this->db->where_in('a.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('a.jenispegawai', $jnspeg);
        }
        $this->db->order_by('a.kelasjabatan', 'DESC');
        $this->db->order_by('a.golru', 'DESC');
        $this->db->order_by('a.eselon', 'ASC');
        return $this->db->get();
    }

    public function getallemployeedetailsrecap($areaid, $sortby,$stspeg=null,$jnspeg=null)
    {
        $this->db->select('a.userid, a.title, a.badgenumber, a.hireddate, a.name, a.deptid');
        $this->db->from('userinfo a');
        $this->db->join('userinfo_attarea c', 'a.userid=c.userid');
        $this->db->where_in('c.areaid', $areaid);
        /*if (is_array($areaid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($areaid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('c.areaid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('c.areaid', $areaid);
        }*/
        if ($stspeg != null)
        {
            $this->db->where_in('a.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('a.jenispegawai', $jnspeg);
        }
        $this->db->order_by($sortby, 'ASC');
        $this->db->group_by('a.userid');
        return $this->db->get();
    }

    public function getorgemployeedetailsrecap($areaid, $orgid, $sortby)
    {
        $this->db->select('a.userid, a.title, a.badgenumber, a.hireddate, a.name, a.deptid');
        $this->db->from('userinfo a');
        $this->db->join('userinfo_attarea c', 'a.userid=c.userid');
        $this->db->where_in('c.areaid', $areaid);
        /*if (is_array($areaid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($areaid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('c.areaid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('c.areaid', $areaid);
        }*/
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
        $this->db->order_by($sortby, 'ASC');
        $this->db->group_by('a.userid');
        return $this->db->get();
    }

    public function getuseremployeedetailsrecap($userid, $sortby)
    {
        $this->db->select('userid, title, badgenumber, hireddate, name, deptid');
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
        //$this->db->order_by('id', 'ASC');
        $this->db->order_by('kelasjabatan', 'DESC');
        return $this->db->get();
    }

    public function getprocessdata($tanggal)
    {
        $this->db->select('late, early_departure, ot_before, ot_after, attendance, absence');
        $this->db->from('process');
        $this->db->where('date_shift', $tanggal);
        return $this->db->get();
    }

    public function getreport($startdate,$enddate)
    {

        $query_report = "SELECT userid,date_shift,shift_in,shift_out,date_in,check_in,
                  date_out,check_out,late,early_departure,ot_before,ot_after,workinholiday,
                  edit_come,edit_home,attendance 
                  FROM process 
                  WHERE date_in BETWEEN '".$this->db->escape_str($startdate)."' AND '".$this->db->escape_str($enddate)."'";

        $report_query=$this->db->query($query_report);
        return $report_query;
    }

    public function getdataReport($lgDay)
    {
        $start_date = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $date_validate = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-$lgDay,date('Y')));

        $query_late = "select count(date_in) as totalLate from process where date_in between '".$this->db->escape_str($date_validate)."' and '".$this->db->escape_str($start_date)."' and late != ''";
        return $this->db->query($query_late);
    }

    public function getdataReportED($lgDay)
    {
        $start_date = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $date_validate = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-$lgDay,date('Y')));

        $query_ed = "select count(date_in) as totalED from process where date_in between '".$this->db->escape_str($date_validate)."' and '".$this->db->escape_str($start_date)."' and early_departure != ''";
        return $this->db->query($query_ed);

    }

    public function getdataAttendace($lgDay)
    {
        $start_date = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $date_validate = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-$lgDay,date('Y')));
        $query_att = "select count(date_in) as totalAttendace from process where date_in between '".$this->db->escape_str($date_validate)."' and '".$this->db->escape_str($start_date)."' and attendance != ''";
        return $this->db->query($query_att);
    }

    public function getDataYesterday($idNum)
    {
        $yesterday = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $query_yesterday = "select count(date_in) as total from process where date_in = '".$this->db->escape_str($yesterday)."' and ";
        if($idNum == '1'){
            $query_yesterday .= "late != ''";
        }
        else if($idNum == '2'){
            $query_yesterday .= "early_departure != ''";
        }
        else if($idNum == '3'){
            $query_yesterday .= "attendance != ''";
        }
        else if($idNum == '4'){
            $query_yesterday .= "absence != ''";
        }
        return $this->db->query($query_yesterday);
    }

    public function getDataNow($idNum)
    {
        $now = date("Y-m-d",mktime(0,0,0,date('m'),date('d'),date('Y')));
        $query_now = "select count(date_in) as total from process where date_in = '".$this->db->escape_str($now)."' and ";
        if($idNum == '1'){
            $query_now .= "late != ''";
        }
        else if($idNum == '2'){
            $query_now .= "early_departure != ''";
        }
        else if($idNum == '3'){
            $query_now .= "attendance != ''";
        }
        else if($idNum == '4'){
            $query_now .= "absence != ''";
        }
        return $this->db->query($query_now);
    }

    public function getdataAbsence($lgDay)
    {
        $start_date = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $date_validate = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-$lgDay,date('Y')));
        $query_absence = "select count(date_in) as totalAbsence from process where date_in between '".$this->db->escape_str($date_validate)."' and '".$this->db->escape_str($start_date)."' and absence != ''";
        return $this->db->query($query_absence);
    }

    public function getdataOvertimeBefore($lgDay)
    {
        $start_date = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $date_validate = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-$lgDay,date('Y')));
        $query_ob = "select count(date_in) as totalAbsenceBefore from process where date_in between '".$this->db->escape_str($date_validate)."' and '".$this->db->escape_str($start_date)."' and ot_before != ''";
        return $this->db->query($query_ob);
    }

    public function getdataOvertimeAfter($lgDay)
    {
        $start_date = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $date_validate = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-$lgDay,date('Y')));
        $query_oa = "select count(date_in) as totalAbsenceAfter from process where date_in between '".$this->db->escape_str($date_validate)."' and '".$this->db->escape_str($start_date)."' and ot_after != ''";
        return $this->db->query($query_oa);
    }

    public function getDataWorkingHoliday($lgDay)
    {
        $start_date = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $date_validate = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-$lgDay,date('Y')));

        $this->db->select('count(*) as totalED');
        $this->db->form('process');
        $this->db->where('date_in BETWEEN '.$this->db->escape_str($date_validate).' AND "'.$this->db->escape_str($start_date).'"');
        $this->db->where("workinholiday != ''");
        return $this->db->get();
    }

    public function getalllogs($startdate,$enddate)
    {
        $this->db->select('checkinout.id AS logid,SN,badgenumber,userinfo.name, checktime,State,Verification');
        $this->db->from('checkinout ');
        $this->db->join('state', 'state.ID = checktype');
        $this->db->join('verification', 'verification.ID = verifycode');
        $this->db->join('userinfo', 'userinfo.userid = checkinout.userid');
        //WHERE date_in BETWEEN '".$startdate."' AND '".$enddate."'";
        $this->db->where('checktime >=', $startdate);
        $this->db->where('checktime <=', $enddate);
        return $this->db->get();
    }

    public function getdesc($tgl, $ver)
    {
        if($ver==20) {
            $this->db->select('a.atname');
            $this->db->from('attendance a');
            $this->db->join('rosterdetails b', 'a.atid=b.attendance');
            $this->db->where('b.rosterdate', $tgl);
            $query = $this->db->get();
            if($query->num_rows()==1) {
                return $query->row()->atname;
            }
        } else {
            $this->db->select('a.State');
            $this->db->from('state a');
            $this->db->join('checkinout b', 'a.ID=b.checktype');
            $this->db->where('b.checktype', $ver);
            $query = $this->db->get();
            if($query->num_rows()==1) {
                return $query->row()->State;
            }
        }
    }

    public function getDataEmployeOrg($organizationID){
        $tempUserInfo = array();
        $i=0;
        $temp_query = "SELECT a.DeptName as nameDept,b.name as name,b.userid as userid,
        b.title as title,b.hireddate as hireddate,b.badgenumber as badgenumber FROM  departments a 
        JOIN userinfo b ON a.deptid = b.deptid 
        WHERE b.deptid = '".$this->db->escape_str($organizationID)."'";
        $query_userinfo = $this->db->query($temp_query);
        if ($query_userinfo->num_rows() > 0)
        {
            foreach ($query_userinfo->result() as $row)
            {
                $tempUserInfo[$i]['dept'] = $row->nameDept ;
                $tempUserInfo[$i]['userid'] = $row->userid ;
                $tempUserInfo[$i]['title'] = $row->title ;
                $tempUserInfo[$i]['hireddate'] = $row->hireddate ;
                $tempUserInfo[$i]['badgenumber'] = $row->badgenumber;
                $tempUserInfo[$i]['name'] = $row->name;

                $i++;
            }

        }
        return $tempUserInfo;
    }

    public function processEmployee($userid,$start_date,$end_date){
        $j=0;
        $dataProcess = array();
        $temp_queryProcess = "SELECT date_shift,
                                     shift_in,
                                     shift_out,
                                     date_in,
                                     check_in,
                                     date_out,
                                     check_out,
                                     break_out,
                                     break_in,
                                     late,
                                     early_departure,
                                     ot_before,
                                     ot_after,
                                     workinholiday 
                            FROM process 
                            WHERE userid = '".$this->db->escape_str($userid)."' AND date_shift BETWEEN '".$this->db->escape_str($start_date)."' AND '".$this->db->escape_str($end_date)."'";
        $query_process = $this->db->query($temp_queryProcess);
        if ($query_process->num_rows() > 0){
            foreach($query_process->result() as $rowProcess){

                $dataProcess[$j]['dateShift']= $rowProcess->date_shift;
                $dataProcess[$j]['shiftIn']= $rowProcess->shift_in;
                $dataProcess[$j]['shiftOut']= $rowProcess->shift_out;
                $dataProcess[$j]['dateIn']= $rowProcess->date_in;
                $dataProcess[$j]['checkIn']= $rowProcess->check_in;
                $dataProcess[$j]['dateOut']= $rowProcess->date_out;
                $dataProcess[$j]['checkOut']= $rowProcess->check_out;
                $dataProcess[$j]['breakOut']= $rowProcess->break_out;
                $dataProcess[$j]['breakIn']= $rowProcess->break_in;
                $dataProcess[$j]['Late']= $rowProcess->late;
                $dataProcess[$j]['earlyDeparture']= $rowProcess->early_departure;
                $dataProcess[$j]['otBefore']= $rowProcess->ot_before;
                $dataProcess[$j]['otAfter']= $rowProcess->ot_after;
                $dataProcess[$j]['workinHoliday']= $rowProcess->workinholiday;
                $j++;
            }
        }

        return $dataProcess;
    }

    public function rosterdetails($startdate,$enddate,$userid){
        $queryRoster = "SELECT COUNT(userid) as total FROM rosterdetails WHERE userid = '".$this->db->escape_str($userid)."' 
        AND rosterdate BETWEEN '".$this->db->escape_str(date('Y-m-d', $startdate))."' AND '".
            $this->db->escape_str(date('Y-m-d',$enddate))."' AND 
            (attendance is null OR attendance like 'AT%' OR attendance like 'AB%')";
        $execRoster = $this->db->query($queryRoster);
        $sumRoster = $execRoster->row();
        return $sumRoster;
    }

    public function checkinout($startdate,$userid){
        $queryRoster = "SELECT COUNT(userid) as total FROM 
                  checkinout WHERE userid = '".$this->db->escape_str($userid)."' 
                  AND date(checktime) ='".$this->db->escape_str($startdate)."'";
        $execRoster = $this->db->query($queryRoster);
        $sumRoster = $execRoster->row();
        return $sumRoster;
    }

    public function checkEmployeeLevel($level_id)
    {
        $sql = " SELECT user_level_name FROM user_level WHERE user_level_id=$level_id";
        //echo $sql;
        $query = $this->db->query($sql);
        $row = $query->row();
        if($row->user_level_name=='employee')
        {
            return true;
        }
        return false;
    }

    public function getdepart(){
        $queryDepart = "SELECT deptid,deptname FROM departments order by deptid";
        $execDepart = $this->db->query($queryDepart);
        $tempArrDep = array();
        $d=0;
        foreach($execDepart->result() as $hslDepart){
            $tempArrDep[$d]['name'] = $hslDepart->deptname;
            $tempArrDep[$d]['id'] = $hslDepart->deptid;
            $d++;
        }
        return $tempArrDep;
    }

    public function getchilddepart2($orgid){
        $queryDepart = "SELECT deptid,deptname FROM departments 
                        WHERE deptid = '".$this->db->escape_str($orgid)."'
                        order by deptid";
        $execDepart = $this->db->query($queryDepart);
        $row = $execDepart->row();
        $tempArrDep['name'] = $row->deptname;
        $tempArrDep['id'] = $row->deptid;
        return $tempArrDep;

    }

    public function getallstatus($datestart, $datestop, $userid='',$statusid)
    {
        $this->db->select('date_shift, attendance, notes');
        $this->db->from('process');
        $this->db->where('userid', $userid);
        $this->db->where('date_shift >=', date('Y-m-d', $datestart));
        $this->db->where('date_shift <=', date('Y-m-d', $datestop));
        $this->db->where('attendance !=', 'NWK');
        $this->db->where('attendance !=', 'NWDS');
        $this->db->where('attendance !=', 'BLNK');
        if($statusid!='undefined') {
            if($statusid=='ABSENCE'){
                $this->db->like('attendance','AB','after');
            } else if($statusid=='ATTENDN'){
                $this->db->like('attendance','AT','after');
            } else if($statusid=='absent'){
                $this->db->where('attendance','ALP');
            } else if($statusid!='') {
                $this->db->where('attendance', $statusid);
            }
        } else {
            $this->db->like('( attendance','AB','after');
            $this->db->or_like('attendance','AT','after');
            $this->db->bracket('close', 'like');
        }
        return $this->db->get();
    }

    public function getstatus()
    {
        $sql = "SELECT abid as id,abname AS name FROM absence 
                UNION select atid as id, atname AS name FROM attendance";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getstatusname()
    {
        $sql = "SELECT abid as id,abname AS name FROM absence 
              UNION select atid as id, atname AS name FROM attendance";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getontime($datestart, $datestop, $orgid)
    {
        $this->db->from('view_ontime');
        $this->db->where('date_shift >=', $datestart);
        $this->db->where('date_shift <=', $datestop);
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

        return $this->db->count_all_results();
    }

    public function getlate($datestart, $datestop, $orgid)
    {
        $this->db->from('view_late');
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
        $this->db->where('date_shift >=', $datestart);
        $this->db->where('date_shift <=', $datestop);
        return $this->db->count_all_results();
    }

    public function getearly($datestart, $datestop, $orgid)
    {
        $this->db->from('view_early');
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
        $this->db->where('date_shift >=', $datestart);
        $this->db->where('date_shift <=', $datestop);
        return $this->db->count_all_results();
    }

    public function getsakit($datestart, $datestop, $orgid)
    {
        $this->db->from('view_sakit');
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
        $this->db->where('date_shift >=', $datestart);
        $this->db->where('date_shift <=', $datestop);
        return $this->db->count_all_results();
    }

    public function getalpa($datestart, $datestop, $orgid)
    {
        $this->db->from('view_alpa');
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
        $this->db->where('date_shift >=', $datestart);
        $this->db->where('date_shift <=', $datestop);
        return $this->db->count_all_results();
    }

    public function getbarsakit($datestart, $datestop, $orgid)
    {
        $sql = "select f.deptid, COALESCE(jml,0) as jumlah from departments f left Join (
				SELECT b.deptid, count(*) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND a.attendance = 'AB_1' 
				GROUP BY deptid) N on N.deptid=f.deptid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarsakitdept($datestart, $datestop, $orgid)
    {
        $sql = "select f.userid, f.name, COALESCE(jml,0) as jumlah from userinfo f left Join (
				SELECT b.userid, count(*) as jml 
				FROM process a
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND a.attendance = 'AB_1' 
				GROUP BY b.userid) N on N.userid=f.userid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarsakit2($datestart, $datestop, $orgid)
    {
        $orgid = implode(',', $orgid);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT deptid as dpt from departments) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.deptid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.deptid IN (".$orgid.")  
				AND a.attendance = 'AB_1' 
				GROUP BY b.deptid, bulan
				ORDER BY bulan, field(b.deptid,".$orgid.") asc) N on N.deptid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$orgid.") ORDER BY ym.bln, field(ym.dpt,".$orgid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarsakit2dept($datestart, $datestop, $userid)
    {
        foreach($userid as $usr)
            $usar[] = "'".$usr."'";
        $userid = implode(',', $usar);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT userid as dpt from userinfo) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.userid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.userid IN (".$userid.")  
				AND a.attendance = 'AB_1' 
				GROUP BY b.userid, bulan
				ORDER BY bulan, field(b.userid,".$userid.") asc) N on N.userid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$userid.") ORDER BY ym.bln, field(ym.dpt,".$userid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbaralpa($datestart, $datestop, $orgid)
    {
        $sql = "select f.deptid, COALESCE(jml,0) as jumlah from departments f left Join (
				SELECT b.deptid, count(*) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND a.attendance = 'ALP' 
				AND a.workinholiday != 1 
				GROUP BY deptid) N on N.deptid=f.deptid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbaralpadept($datestart, $datestop, $orgid)
    {
        $sql = "select f.userid, f.name, COALESCE(jml,0) as jumlah from userinfo f left Join (
				SELECT b.userid, count(*) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."'
				AND a.attendance = 'ALP' 
				AND a.workinholiday != 1 
				GROUP BY b.userid) N on N.userid=f.userid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbaralpa2($datestart, $datestop, $orgid)
    {
        $orgid = implode(',', $orgid);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT deptid as dpt from departments) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.deptid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.deptid IN (".$orgid.")  
				AND a.attendance = 'ALP' 
				AND a.workinholiday != 1 
				GROUP BY b.deptid, bulan
				ORDER BY bulan, field(b.deptid,".$orgid.") asc) N on N.deptid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$orgid.") ORDER BY ym.bln, field(ym.dpt,".$orgid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbaralpa2dept($datestart, $datestop, $userid)
    {
        foreach($userid as $usr)
            $usar[] = "'".$usr."'";
        $userid = implode(',', $usar);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT userid as dpt from userinfo) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.userid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.userid IN (".$userid.")    
				AND a.attendance = 'ALP' 
				AND a.workinholiday != 1 
				GROUP BY b.userid, bulan
				ORDER BY bulan, field(b.userid,".$userid.") asc) N on N.userid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$userid.") ORDER BY ym.bln, field(ym.dpt,".$userid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbartotalworkdept($datestart, $datestop, $userid)
    {
        foreach($userid as $usr)
            $usar[] = "'".$usr."'";
        $userid = implode(',', $usar);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT userid as dpt from userinfo) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.userid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.userid IN (".$userid.")  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK'				 
				AND a.attendance != 'OFF'
				AND a.workinholiday !=1)) 
				GROUP BY b.userid, bulan
				ORDER BY bulan, field(b.userid,".$userid.") asc) N on N.userid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$userid.") ORDER BY ym.bln, field(ym.dpt,".$userid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarontime($datestart, $datestop, $orgid)
    {
        $sql = "select f.deptid, COALESCE(jml,0) as jumlah from departments f left Join (
				SELECT b.deptid, count(*) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."'  
				AND (a.attendance is null OR a.attendance like 'AT%') AND a.late = 0 
				GROUP BY deptid) N on N.deptid=f.deptid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbartotal($datestart, $datestop, $orgid)
    {
        $orgid = implode(',', $orgid);
        $sql = "select f.deptid, COALESCE(jml,0) as jumlah from departments f left Join (
				SELECT b.deptid, count(*) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."'  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK'				 
				AND a.attendance != 'OFF'
				AND a.workinholiday !=1)) 
				GROUP BY deptid) N on N.deptid=f.deptid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarontimedept($datestart, $datestop, $orgid)
    {
        $sql = "select f.userid, f.name, COALESCE(jml,0) as jumlah from userinfo f left Join (
				SELECT b.userid, count(*) as jml 
				FROM process a
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."'  
				AND (a.attendance is null OR a.attendance like 'AT%') AND a.late = 0
				GROUP BY b.userid) N on N.userid=f.userid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarontime2($datestart, $datestop, $orgid)
    {
        $orgid = implode(',', $orgid);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT deptid as dpt from departments) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.deptid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.deptid IN (".$orgid.")  
				AND (a.attendance is null OR a.attendance like 'AT%') AND a.late = 0 
				GROUP BY b.deptid, bulan
				ORDER BY bulan, field(b.deptid,".$orgid.") asc) N on N.deptid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$orgid.") ORDER BY ym.bln, field(ym.dpt,".$orgid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbartotalwork($datestart, $datestop, $orgid)
    {
        $orgid = implode(',', $orgid);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT deptid as dpt from departments) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.deptid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.deptid IN (".$orgid.")  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK'				 
				AND a.attendance != 'OFF'
				AND a.workinholiday !=1)) 
				GROUP BY b.deptid, bulan
				ORDER BY bulan, field(b.deptid,".$orgid.") asc) N on N.deptid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$orgid.") ORDER BY ym.bln, field(ym.dpt,".$orgid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarontime2dept($datestart, $datestop, $userid)
    {
        foreach($userid as $usr)
            $usar[] = "'".$usr."'";
        $userid = implode(',', $usar);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT userid as dpt from userinfo) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.userid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.userid IN (".$userid.")  
				AND (a.attendance is null OR a.attendance like 'AT%')
				AND a.late = 0 
				GROUP BY b.userid, bulan
				ORDER BY bulan, field(b.userid,".$userid.") asc) N on N.userid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$userid.") ORDER BY ym.bln, field(ym.dpt,".$userid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarlate($datestart, $datestop, $orgid)
    {
        $sql = "select f.deptid, COALESCE(jml,0) as jumlah from departments f left Join (
				SELECT b.deptid, sum(a.late) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."'  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK' 
				AND a.attendance != 'OFF'
				AND a.workinholiday !=1))
				AND a.late != 0  
				GROUP BY deptid) N on N.deptid=f.deptid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarlatedept($datestart, $datestop, $orgid)
    {
        $sql = "select f.userid, f.name, COALESCE(jml,0) as jumlah from userinfo f left Join (
				SELECT b.userid, sum(a.late) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."'  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK' 
				AND a.attendance != 'OFF'
				AND a.workinholiday != 1)) 
				AND a.late != 0
				GROUP BY b.userid) N on N.userid=f.userid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarlate2($datestart, $datestop, $orgid)
    {
        $orgid = implode(',', $orgid);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT deptid as dpt from departments) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.deptid, extract(month from date_shift) as bulan, sum(a.late) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.deptid IN (".$orgid.")  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK' 
				AND a.attendance != 'OFF'
				AND a.workinholiday != 1)) 
				AND a.late != 0 
				GROUP BY b.deptid, bulan
				ORDER BY bulan, field(b.deptid,".$orgid.") asc) N on N.deptid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$orgid.") ORDER BY ym.bln, field(ym.dpt,".$orgid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarlate2dept($datestart, $datestop, $userid)
    {
        foreach($userid as $usr)
            $usar[] = "'".$usr."'";
        $userid = implode(',', $usar);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT userid as dpt from userinfo) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.userid, extract(month from date_shift) as bulan, sum(a.late) as jumlah 
				FROM process a
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.userid IN (".$userid.")  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK' 
				AND a.attendance != 'OFF'
				AND a.workinholiday != 1)) 
				AND a.late != 0
				GROUP BY b.userid, bulan
				ORDER BY bulan, field(b.userid,".$userid.") asc) N on N.userid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$userid.") ORDER BY ym.bln, field(ym.dpt,".$userid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarleave($datestart, $datestop, $orgid)
    {
        $sql = "select f.deptid, COALESCE(jml,0) as jumlah from departments f left Join (
				SELECT b.deptid, count(*) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				and a.attendance != 'NWDS' AND a.attendance != 'NWK' AND a.attendance != 'BLNK' AND a.attendance != 'ALP' AND a.attendance != 'OFF' 
				AND a.workinholiday != 1
				AND a.attendance != 'AB_1' 
				GROUP BY deptid) N on N.deptid=f.deptid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarleavedept($datestart, $datestop, $orgid)
    {
        $sql = "select f.userid, f.name, COALESCE(jml,0) as jumlah from userinfo f left Join (
				SELECT b.userid, count(*) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				and a.attendance != 'NWDS' AND a.attendance != 'NWK' AND a.attendance != 'BLNK' AND a.attendance != 'ALP' AND a.attendance != 'OFF'
				AND a.attendance != 'AB_1' 
				AND a.workinholiday != 1
				GROUP BY b.userid) N on N.userid=f.userid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarleave2($datestart, $datestop, $orgid)
    {
        $orgid = implode(',', $orgid);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT deptid as dpt from departments) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.deptid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.deptid IN (".$orgid.")  
				and a.attendance != 'NWDS' AND a.attendance != 'NWK' AND a.attendance != 'BLNK' AND a.attendance != 'ALP' AND a.attendance != 'OFF'
				AND a.attendance != 'AB_1' 
				AND a.workinholiday != 1
				GROUP BY b.deptid, bulan
				ORDER BY bulan, field(b.deptid,".$orgid.") asc) N on N.deptid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$orgid.") ORDER BY ym.bln, field(ym.dpt,".$orgid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarleave2dept($datestart, $datestop, $userid)
    {
        foreach($userid as $usr)
            $usar[] = "'".$usr."'";
        $userid = implode(',', $usar);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT userid as dpt from userinfo) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.userid, extract(month from date_shift) as bulan, count(*) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.userid IN (".$userid.")  
				and a.attendance != 'NWDS' AND a.attendance != 'NWK' AND a.attendance != 'BLNK' AND a.attendance != 'ALP' AND a.attendance != 'OFF'
				AND a.attendance != 'AB_1' 
				AND a.workinholiday != 1
				GROUP BY b.userid, bulan
				ORDER BY bulan, field(b.userid,".$userid.") asc) N on N.userid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$userid.") ORDER BY ym.bln, field(ym.dpt,".$userid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarot($datestart, $datestop, $orgid)
    {
        $sql = "select f.deptid, COALESCE(jml,0) as jumlah from departments f left Join (
				SELECT b.deptid, sum(a.ot_after) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."'  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK' 
				AND a.attendance != 'OFF'
				AND a.workinholiday != 1))
				AND a.ot_after != 0
				GROUP BY deptid) N on N.deptid=f.deptid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarotdept($datestart, $datestop, $orgid)
    {
        $sql = "select f.userid, f.name, COALESCE(jml,0) as jumlah from userinfo f left Join (
				SELECT b.userid, sum(a.ot_after) as jml 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."'  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK' 
				AND a.attendance != 'OFF'
				AND a.workinholiday != 1)) 
				AND a.ot_after != 0
				GROUP BY b.userid) N on N.userid=f.userid
				where f.deptid in (".$orgid.")
				ORDER BY jumlah desc 
				limit 10";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarot2($datestart, $datestop, $orgid)
    {
        $orgid = implode(',', $orgid);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT deptid as dpt from departments) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.deptid, extract(month from date_shift) as bulan, sum(a.ot_after) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.deptid IN (".$orgid.")  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK' 
				AND a.attendance != 'OFF'
				AND a.workinholiday != 1)) 
				AND a.ot_after != 0 
				GROUP BY b.deptid, bulan
				ORDER BY bulan, field(b.deptid,".$orgid.") asc) N on N.deptid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$orgid.") ORDER BY ym.bln, field(ym.dpt,".$orgid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getbarot2dept($datestart, $datestop, $userid)
    {
        foreach($userid as $usr)
            $usar[] = "'".$usr."'";
        $userid = implode(',', $usar);
        $bulan1 = date('m', strtotime($datestart));
        $bulan2 = date('m', strtotime("+1 months", strtotime($datestart)));
        $bulan3 = date('m', strtotime($datestop));

        $sql = "select dpt, bln, COALESCE(N.jumlah,0) as jml from (
				SELECT dpt, bln FROM   
				(SELECT userid as dpt from userinfo) depts,         
				(SELECT ".$bulan1." bln UNION ALL SELECT ".$bulan2." UNION ALL SELECT ".$bulan3.") months ) ym
				LEFT JOIN (SELECT b.userid, extract(month from date_shift) as bulan, sum(a.ot_after) as jumlah 
				FROM process a 
				JOIN userinfo b ON a.userid=b.userid 
				WHERE a.date_shift >= '".$this->db->escape_str($datestart)."' 
				AND a.date_shift <= '".$this->db->escape_str($datestop)."' 
				AND b.userid IN (".$userid.")  
				AND (a.attendance is null
				OR
				(a.attendance != 'NWDS' 
				AND a.attendance != 'NWK' 
				AND a.attendance != 'BLNK' 
				AND a.attendance != 'OFF'
				AND a.workinholiday != 1)) 
				AND a.ot_after != 0
				GROUP BY b.userid, bulan
				ORDER BY bulan, field(b.userid,".$userid.") asc) N on N.userid=ym.dpt and N.bulan=ym.bln
				WHERE ym.dpt IN (".$userid.") ORDER BY ym.bln, field(ym.dpt,".$userid.")";
        $query = $this->db->query($sql);
        return $query;
    }

    public function getchildofdept($orgid)
    {
        $this->db->from('departments');
        $this->db->where('parentid', $orgid);
        $this->db->order_by('deptid','ASC');
        $query = $this->db->get();
        $data = '';
        foreach($query->result() as $que)
            $data = $data."'".$que->deptid."',";
        return substr($data,0,-1);
    }

    public function getchildofdept2($orgid)
    {
        $this->db->from('departments');
        $this->db->where('parentid', $orgid);
        $this->db->or_where('deptid', $orgid);
        $this->db->order_by('deptid','ASC');
        $query = $this->db->get();
        $data = "";
        foreach($query->result() as $que)
            $data = $data."'".$que->deptid."',";
        return substr($data,0,-1);
    }

    public function getparentdept($dept){
        $this->db->select('parentid');
        $this->db->from('departments');
        $this->db->where('deptid', $dept);
        $this->db->order_by('deptid','ASC');
        $query = $this->db->get();
        if($query->num_rows()==1)
        {
            return $query->row()->parentid;
        }
        return false;
    }

    public function getkelasjabatan($userid, $tgl){
        $sql = "select kelas from userinfohistory where userid = '".$userid."' and tmtjabatan <= '".$tgl."' order by tmtjabatan desc limit 1";
        $query = $this->db->query($sql);
        $sqlt = "select kelas from userinfohistory where userid = '".$userid."' and tmtjabatan >= '".$tgl."' order by tmtjabatan asc limit 1";
        $queryt = $this->db->query($sqlt);
        $vkelas = null;
        if($query->num_rows()==1) {
            $row = $query->row();
            if ( isset($row))
            {
                $vkelas = $row->kelas;
            }
        }
        else if($queryt->num_rows()==1) {
            $row = $queryt->row();
            if ( isset($row))
            {
                $vkelas = $row->kelas;
            }
        }
        return $vkelas;
    }

    public function gettunjang($tgl, $kelas){
        $tunjang = 0;
		if(!empty($kelas)) {
            $sql = "select tunjangan from tunjanganhistory where tglubah <= '".$tgl."' and kelasjabatan = ".$kelas." order by tglubah desc limit 1";
            $query = $this->db->query($sql);
            $sqlt = "select tunjangan from tunjanganhistory where tglubah >= '".$tgl."' and kelasjabatan = ".$kelas." order by tglubah asc limit 1";
            $queryt = $this->db->query($sqlt);
            $sqls = "select tunjangan from mastertunjangan where kelasjabatan = ".$kelas." limit 1";
            $querys = $this->db->query($sqlt);
            if($query->num_rows()==1)
                $tunjang = $query->row()->tunjangan;
            else if($queryt->num_rows()==1)
                $tunjang = $queryt->row()->tunjangan;
            else if($querys->num_rows()==1)
                $tunjang = $querys->row()->tunjangan;
        }
		return $tunjang;
    }

    public function gettunjangprof($userid, $tgl){
        $sql = "select tunjanganprofesi from tunjanganprofhistory where userid = '".$userid."' and tunjprofdate <= '".$tgl."' order by tunjprofdate desc limit 1";
        $query = $this->db->query($sql);
        $sqlt = "select tunjanganprofesi from tunjanganprofhistory where userid = '".$userid."' and tunjprofdate >= '".$tgl."' order by tunjprofdate asc limit 1";
        $queryt = $this->db->query($sql);
        $tunjanganprof = 0;
        if($query->num_rows()==1)
            $tunjanganprof = $query->row()->tunjanganprofesi;
        else if($queryt->num_rows()==1)
            $tunjanganprof = $queryt->row()->tunjanganprofesi;
        return $tunjanganprof;
    }

    public function getjenispeghis($userid, $tgl, $jenis){
		$sql = "select value from jenispegawaihistory where userid = '".$userid."' and tanggal <= '".$tgl."' and jenis = ".$jenis." order by tanggal desc limit 1";
        $query = $this->db->query($sql);

        $sqlt = "select value from jenispegawaihistory where userid = '".$userid."' and tanggal >= '".$tgl."' and jenis = ".$jenis." order by tanggal asc limit 1";
        $queryt = $this->db->query($sqlt);

        $value = 0;
        if($query->num_rows()==1)
            $value = $query->row()->value;
        else if($queryt->num_rows()==1)
            $value = $queryt->row()->value;
        else {
            switch ($jenis)
            {
                case 1:
                    $sqlr = "select jftstatus from userinfo where userid = '".$userid."'";
                    $queryr = $this->db->query($sqlr);
                    if($queryr->num_rows()==1)
                    {
                        $value = $queryr->row()->jftstatus;
                    }
                    break;
                case 2:
                    $sqlr = "select jenispegawai from userinfo where userid = '".$userid."'";
                    $queryr = $this->db->query($sqlr);
                    if($queryr->num_rows()==1)
                    {
                        $value = $queryr->row()->jenispegawai;
                    }
                    break;
                default:
                    $sqlr = "select kedudukan from userinfo where userid = '".$userid."'";
                    $queryr = $this->db->query($sqlr);
                    if($queryr->num_rows()==1)
                    {
                        $value = $queryr->row()->kedudukan;
                    }
                    break;
            }
        }
		//var_dump($value);
        return $value;
    }

    public function gettgljenispeghis($userid, $tgl, $jenis){
        $sql = "select tanggal from jenispegawaihistory where userid = '".$userid."' and tanggal <= '".$tgl."' and jenis = ".$jenis." order by tanggal desc limit 1";
        $query = $this->db->query($sql);

        $sqlt = "select tanggal from jenispegawaihistory where userid = '".$userid."' and tanggal >= '".$tgl."' and jenis = ".$jenis." order by tanggal asc limit 1";
        $queryt = $this->db->query($sqlt);

        $value = null;
        if($query->num_rows()==1)
            $value = $query->row()->tanggal;
        else if($queryt->num_rows()==1)
            $value = $queryt->row()->tanggal;
        else {
            $sqlr = "select tmtkedudukan from userinfo where userid = '".$userid."'";
            $queryr = $this->db->query($sqlr);
            if($queryr->num_rows()==1)
            {
                $value = $queryr->row()->tmtkedudukan;
            }
        }
        return $value;
    }

    public function getempofdept($areaid, $organid, $stspeg,$cari=null,$jnspeg=null)
    {
        $sql = "select * from view_employee ";
        $a = 0;
        if(!empty($areaid)) {
            $s = array();
            foreach($areaid as $ar)
                $s[] = "'".$ar."'";
            $sql .= "where area_id in (".implode(',',$s).") ";
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

        if(!empty($stspeg)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $s = array();
            foreach($stspeg as $ar)
                $s[] = "'".$ar."'";
            $sql .= $ql." jftstatus in (".implode(',', $s).") ";
            $a = 1;
        }

        if(!empty($jnspeg)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $s = array();
            foreach($jnspeg as $ar)
                $s[] = "'".$ar."'";
            $sql .= $ql." jenispegawai  in (".implode(',', $s).") ";
            $a = 1;
        }

        if(!empty($cari)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';

            $sql .= $ql." ( name LIKE '%".str_replace('%20',' ',$cari)."%' 
                        or badgenumber LIKE '%".str_replace('%20',' ',$cari)."%' or userid LIKE '%".str_replace('%20',' ',$cari)."%' ) ".
            "and jftstatus in ('1','2') ";


            $a = 1;
        }



        if($a==1) $ql = 'AND ';
        else $ql = 'where ';
        $sql .= $ql."active is null ";


        return $this->db->query($sql);
    }

    public function getroster($orgid, $datestart, $dateend)
    {
        $sql = "select a.userid, a.rosterdate, a.absence, c.attendance, c.editby 
                from rosterdetails a left join userinfo b on a.userid=b.userid
				left join rosterdetailsatt c on a.userid = c.userid and a.rosterdate = c.rosterdate ";
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

    public function getrosterbyuid($userid, $datestart, $dateend)
    {
        $sql = "select a.userid, a.rosterdate, a.absence, c.attendance, c.editby 
                from rosterdetails a left join userinfo b on a.userid=b.userid
				left join rosterdetailsatt c on a.userid = c.userid and a.rosterdate = c.rosterdate ";
        $a = 0;
        if(!empty($userid)) {
            if($a==1) $ql = 'AND ';
            else $ql = 'where ';
            $s = array();
            foreach($userid as $ar)
                $s[] = "'".$ar."'";
            $sql .= $ql."a.userid in (".implode(',', $s).") ";
            $a = 1;
        }

        if($a==1) $ql = 'AND ';
        else $ql = 'where ';

        $sql .= $ql."a.rosterdate >= '".$this->db->escape_str(date('Y-m-d', $datestart))."' AND a.rosterdate <= '".$this->db->escape_str(date('Y-m-d', $dateend))."'";

        return $this->db->query($sql);
    }

    public function getrostergroupdetails($orgid, $datestart, $dateend)
    {
        $sql = "select a.userid, a.rosterdate, a.attendance, a.editby 
              from groupshiftdetails a left join userinfo b on a.userid=b.userid ";
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

    function itungan3($ss) {
        //$s = $ss%60;
        $m = floor(($ss%3600)/60);
        $h = floor(($ss%86400)/3600);
        $d = floor(($ss%2592000)/86400);
        $M = floor($ss/2592000);

        $m= str_pad($m,2,'0',STR_PAD_LEFT);
        $h= str_pad($h,2,'0',STR_PAD_LEFT);

        $d = ($d>0)?str_pad($d,2,'0',STR_PAD_LEFT).":":"";
        $M = ($M>0)?str_pad($M,2,'0',STR_PAD_LEFT).":":"";

        return "$M$d$h:$m";
    }

    public function getAbsKategoriAktif($katID)
    {
        $this->db->where("state",1);
        $this->db->where("status_kategori_id",$katID);
        $this->db->from('absence');
        return $this->db->get();
    }

    public function getSikerjaImport($nip,$thnbln)
    {
        $this->db->select("jumlah");
        $this->db->where("bulan",$thnbln);
        $this->db->where("nip",$nip);
        $this->db->limit(1);
        return $this->db->get("data_sikerja");
    }

    public function getdetailroster($uid,$tgl)
    {
        $valuret="";
        $this->db->select('notes,nosk');
        $this->db->from('rosterdetailsatt');
        $this->db->where('userid', $uid);
        $this->db->where('rosterdate', $tgl);
        $query = $this->db->get();
        if($query->num_rows()==1) {
            $valuret = "<br>No.SK: ".$query->row()->nosk."<br>Ket:".$query->row()->notes;
        }

        return $valuret;
    }
	
	public function getdetailroster2($uid,$tgl)
    {
        $valuret="";
        
		$this->db->select('process_upacara.*',false);
        $this->db->from('process_upacara');
        $this->db->where('process_upacara.userid', $uid);
        $this->db->where('process_upacara.date_shift =', $tgl);
		$query = $this->db->get();
		if($query->num_rows()==1) {
			$s = $query->result();
			if($s[0]->attendance == "UPC"){
				if (isset($s[0]->date_in)) {
					$valuret = "Ya";
				}else{
					$valuret = "Tidak";
				}
				
			}else{
				$valuret = "Tidak";
			}
        }
        return $valuret;
    }
	
	 public function getdetailroster3($uid,$tgl)
    {
        $this->db->select('process_upacara.*',false);
        $this->db->from('process_upacara');
        $this->db->where('process_upacara.userid', $uid);
        $this->db->where('process_upacara.date_shift =', $tgl);
        return $this->db->get();
    }

    /*public function gettukinhist($userid, $tgl){
        $sql = "select * from v_tukinhistory where userid = '".$userid."' and '".$tgl."' between tmt and tglakhir order by tmt desc limit 1";
        return $this->db->query($sql);
    }*/

    public function getkelasplttukinhist($userid, $tgl){


        $sql = "select tmt,kelasjabatan,deptid,plt_eselon,eselon from tukinhistory where userid = '".$userid."' and tmt <= '".$tgl."'  and jenis='PLT' order by tmt desc,kelasjabatan DESC limit 1";
        $query = $this->db->query($sql);
        $sqlt = "select tmt,kelasjabatan,deptid,plt_eselon,eselon from tukinhistory where userid = '".$userid."' and tmt >= '".$tgl."' and jenis='PLT' order by tmt asc,kelasjabatan DESC limit 1";
        $queryt = $this->db->query($sqlt);
        $sqls = "select tmt_plt as tmt,plt_kelasjabatan as kelasjabatan,plt_deptid as deptid, plt_eselon,eselon from userinfo where userid = ".$userid." limit 1";
        $querys = $this->db->query($sqls);
        //echo $userid." ".$tgl." ".$query->num_rows()." ".$queryt->num_rows()." ".$querys->num_rows();
        if($query->num_rows()==1) {
            $rslt["tmt"] = $query->row()->tmt;
            $rslt["kelas"] = $query->row()->kelasjabatan;
            $rslt["deptid"] = $query->row()->deptid;
            $rslt["eselon"] = $query->row()->plt_eselon;
            $rslt["eselondef"] = $query->row()->eselon;
        }
        else if($queryt->num_rows()==1) {
            $rslt["tmt"] = $query->row()->tmt;
            $rslt["kelas"] = $queryt->row()->kelasjabatan;
            $rslt["deptid"] = $queryt->row()->deptid;
            $rslt["eselon"] = $queryt->row()->plt_eselon;
            $rslt["eselondef"] = $queryt->row()->eselon;
        }
        else if($querys->num_rows()==1) {
            $rslt["tmt"] = $querys->row()->tmt;
            $rslt["kelas"] = $querys->row()->kelasjabatan;
            $rslt["deptid"] = $querys->row()->deptid;
            $rslt["eselon"] = $querys->row()->plt_eselon;
            $rslt["eselondef"] = $querys->row()->eselon;
        }

        //$sql = "select * from v_tukinhistory where userid = '".$userid."' and '".$tgl."' between tmt and tglakhir order by tmt desc limit 1";
        return $rslt ;//$this->db->query($sql);
    }

    public function getstatushadir($datestart, $userid)
    {
        $this->db->from('rosterdetailsatt');
        $this->db->where_in('userid', $userid);
        $this->db->where('rosterdate', $datestart);
        $this->db->where(' EXISTS (SELECT abname FROM absence WHERE absence.abid=rosterdetailsatt.attendance)',null,false);

        return $this->db->count_all_results();
    }

    public function getstatustidakhadir($datestart, $userid)
    {
        $this->db->from('rosterdetailsatt');
        $this->db->where_in('userid', $userid);
        $this->db->where('rosterdate', $datestart);
        $this->db->where(' EXISTS (SELECT atname FROM attendance WHERE attendance.atid=rosterdetailsatt.attendance)',null,false);

        return $this->db->count_all_results();
    }

    //upacara
    public function getattbydateorgupacara($areaid, $tanggal, $orgid,$stspeg=null,$jnspeg=null)
    {
		$this->db->select('a.*,b.badgenumber, b.name, b.deptid');
        $this->db->from('process_upacara a');
        $this->db->join('userinfo b', 'a.userid=b.userid');
        $this->db->join('userinfo_attarea c', 'a.userid=c.userid');
        $this->db->where('a.date_shift', $tanggal);
        $this->db->where_in('b.deptid', $orgid);
        /*if (is_array($orgid))
        {
            $this->db->group_start();
            $ids_chunk = array_chunk($orgid,25);
            foreach($ids_chunk as $s_ids)
            {
                $this->db->or_where_in('b.deptid', $s_ids);
            }
            $this->db->group_end();
        } else {
            $this->db->where_in('b.deptid', $orgid);
        }*/
        if ($stspeg != null) $this->db->where_in('b.jftstatus', $stspeg);
        if ($jnspeg != null) $this->db->where_in('b.jenispegawai', $jnspeg);
        if(!empty($areaid)) {
            $this->db->where_in('c.areaid', $areaid);
            /*if (is_array($areaid))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($areaid,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('c.areaid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('c.areaid', $areaid);
            }*/
        }

        // $this->db->group_by('a.userid, a.date_shift, a.shift_in, a.shift_out, a.date_in, a.check_in, a.date_out, a.check_out, 
		// a.break_out, a.break_in, a.late, a.early_departure, a.ot_before, a.ot_after, a.workinholiday, a.attendance,
		// b.badgenumber, b.name, b.deptid');
        //$qry= $this->db->get_compiled_select('process');
        //echo "kuprest".$qry;
        //$this->db->reset_query();
        return $this->db->get();
    }

    public function getattbydateupacara($areaid, $tanggal,$stspeg=null,$jnspeg=null)
    {
        $this->db->select('a.*,b.badgenumber, b.name, b.deptid');
        $this->db->from('process_upacara a');
        $this->db->join('userinfo b', 'a.userid=b.userid');
        $this->db->join('userinfo_attarea c', 'a.userid=c.userid');
        $this->db->where('a.date_shift', $tanggal);
        if(!empty($areaid)) {
            $this->db->where_in('c.areaid', $areaid);
            /*if (is_array($areaid))
            {
                $this->db->group_start();
                $ids_chunk = array_chunk($areaid,25);
                foreach($ids_chunk as $s_ids)
                {
                    $this->db->or_where_in('c.areaid', $s_ids);
                }
                $this->db->group_end();
            } else {
                $this->db->where_in('c.areaid', $areaid);
            }*/
        }

        if ($stspeg != null)
        {
            $this->db->where_in('b.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('b.jenispegawai', $jnspeg);
        }
        // $this->db->group_by('a.userid, a.date_shift, a.shift_in, a.shift_out, , a.date_in, a.check_in, a.date_out, a.check_out, 
		// a.break_out, a.break_in, a.late, a.early_departure, a.ot_before, a.ot_after, a.workinholiday, a.attendance,
		// b.badgenumber, b.name, b.deptid');
        //$qry= $this->db->get_compiled_select('process');
        //echo "kuprest 2 ".$qry
        //    $this->db->reset_query();
        return $this->db->get();
    }

    public function getattbydateuserupacara($tanggal, $userid,$stspeg=null,$jnspeg=null)
    {
        $this->db->select('a.*,
		b.badgenumber, b.name, b.deptid');
        $this->db->from('process_upacara a');
        $this->db->join('userinfo b', 'a.userid=b.userid');
        $this->db->where('a.date_shift', $tanggal);
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
        if ($stspeg != null)
        {
            $this->db->where_in('b.jftstatus', $stspeg);
        }
        if ($jnspeg != null)
        {
            $this->db->where_in('b.jenispegawai', $jnspeg);
        }
        // $this->db->group_by('a.userid, a.date_shift, a.shift_in, a.shift_out, a.date_in, a.check_in, a.date_out, a.check_out, 
		// a.break_out, a.break_in, a.late, a.early_departure, a.ot_before, a.ot_after, a.workinholiday, a.attendance,
		// b.badgenumber, b.name, b.deptid');
        //$qry= $this->db->get_compiled_select('process');

        //echo "kuprest 3 ".$mode;
        //$this->db->reset_query();
        return $this->db->get();
    }

	
    public function getattlogupacaraold($datestart, $datestop, $userid)
    {
        $this->db->select('process_upacara.*',false);
        $this->db->from('process_upacara');
        $this->db->where('process_upacara.userid', $userid);
        $this->db->where('process_upacara.date_shift >=', date('Y-m-d', $datestart));
        $this->db->where('process_upacara.date_shift <=', date('Y-m-d', $datestop));
        $this->db->order_by('process_upacara.date_shift', 'asc');
        //$qry= $this->db->get_compiled_select();
        //$this->db->reset_query();
        //echo $mode.' '.$qry;
        return $this->db->get();
    }
	
	public function getattlogupacara($datein, $datestop, $userid)
    {
        $this->db->select("process_upacara.*,concat(process_upacara.date_in,' ',process_upacara.check_in) AS checktime",false);
        $this->db->from('process_upacara');
        $this->db->where('process_upacara.userid', $userid);
        $this->db->where('process_upacara.date_shift >=', date('Y-m-d', $datein));
        $this->db->where('process_upacara.date_shift <=', date('Y-m-d', $datestop));
        $this->db->order_by('process_upacara.date_shift', 'asc');
        //$qry= $this->db->get_compiled_select();
        //$this->db->reset_query();
        //echo $mode.' '.$qry;
        return $this->db->get();
    }

}
