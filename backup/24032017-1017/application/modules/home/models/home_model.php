<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();


    }

    function getLastAbsensi($mydate=null,$limit = 10,$orgId=null)
    {
        $this->db->select("userinfo.userid,userinfo.badgenumber,userinfo.name,checkinout.checktime,departments.deptname");
        $this->db->from("userinfo");
        $this->db->join('departments', 'departments.deptid=userinfo.deptid');
        $this->db->join('checkinout', 'userinfo.userid=checkinout.userid');
        $this->db->order_by("checkinout.checktime", "desc");
        $this->db->where("DATE(checktime) = ","'".$mydate."'",false);
        if ($orgId != null)
        {
            $this->db->where("userinfo.deptid in ","('".implode("','", $orgId)."')", false);
        }
        $this->db->limit($limit);

        $qry = $this->db->get();
        return $qry;
    }

    function getLastDayProcess()
    {
        //$sql ="SELECT DATE(checktime) as tgl FROM checkinout ORDER BY checktime DESC LIMIT 1";
        $rtn= date("Y-m-d");
        $sql ="SELECT DATE(date_shift) as tgl 
              FROM process 
              where shift_in is not null ORDER BY date_shift DESC LIMIT 1";
        $qry = $this->db->query($sql);
         if ($qry->num_rows()>0)
         {
            $er = $qry->row_array();
             $rtn=$er["tgl"];
         }

        //echo $this->db->last_query();
        return $rtn;
    }

    function getLastDayTrans()
    {
        $sql ="SELECT DATE(MAX(checktime)) AS tgl FROM checkinout";
        //$sql ="SELECT DATE(date_shift) as tgl FROM process where shift_in is not null ORDER BY date_shift DESC LIMIT 1";
        $qry = $this->db->query($sql)->row_array();
        //echo $this->db->last_query();
        return $qry["tgl"];
    }

    function getLastTransRekap()
    {
        $sql ="SELECT deptname,
                ( (FLOOR( 1 + RAND( ) * 200 )) ) AS jml
                FROM departments WHERE parentid=1";
        $qry = $this->db->query($sql);
        //echo $this->db->last_query();
        return $qry;
    }

    function getTptwaktu($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(count(*),0) as jml 
              FROM process a
              inner join userinfo b on a.userid = b.userid 
              Where date_shift = '$tgl' and late=0 ";

        if ($orgId != null)
        {
            $sql .= " AND b.deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getTerlambat($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(count(*),0) as jml 
            FROM process a
            inner join userinfo b on a.userid = b.userid
            Where date_shift = '$tgl' and late>0";
        if ($orgId != null)
        {
            $sql .= " AND b.deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getIjin($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(count(*),0) as jml 
              FROM process a
            inner join userinfo b on a.userid = b.userid 
              Where date_shift = '$tgl' and ( 
            attendance = 'AB_11' or attendance  like 'AT_AT%' )";
        if ($orgId != null)
        {
            $sql .= " AND b.deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getTb($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(count(*),0) as jml 
              FROM process a
              inner join userinfo b on a.userid = b.userid 
              Where date_shift = '$tgl' and (attendance='AB_15' or 
                attendance='AB_16' or attendance='AT_DK'  )";
        if ($orgId != null)
        {
            $sql .= " AND b.deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getSakit($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(count(*),0) as jml 
            FROM process a
            inner join userinfo b on a.userid = b.userid 
            Where date_shift = '$tgl' and ( 
            attendance = 'AB_1' or attendance  like 'AB_2' or attendance  like 'AB_4' or attendance  like 'AB_5'
            or attendance  like 'AB_6')";
        if ($orgId != null)
        {
            $sql .= " AND b.deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getCuti($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(count(*),0) as jml 
          FROM process a
            inner join userinfo b on a.userid = b.userid 
          Where date_shift = '$tgl' and ( 
            attendance = 'AB_3' or attendance  like 'AB_7' or attendance  like 'AB_8' or attendance  like 'AB_9'
             or attendance  like 'AB_14' or attendance  like 'AB_17' )";
        if ($orgId != null)
        {
            $sql .= " AND b.deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getAlpha($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(count(*),0) as jml 
              FROM process a
            inner join userinfo b on a.userid = b.userid 
              Where date_shift = '$tgl' and attendance  like 'ALP' ";
        if ($orgId != null)
        {
            $sql .= " AND b.deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getTptwaktuByYear($tgl,$orgId=null)
    {
        $sql2="";
        if ($orgId != null)
        {
            $sql2 = " AND userinfo.deptid in ('".implode("','", $orgId)."')";
        }

        $sql ="SELECT Y, m, IFNULL(COUNT(`process`.date_shift),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                    LEFT JOIN process ON ym.y = YEAR(FROM_UNIXTIME(process.date_shift)) AND  
                    ym.m = MONTH(FROM_UNIXTIME(process.date_shift)) and process.late=0
                    left join userinfo  on userinfo.userid = process.userid
                    ".$sql2."
                WHERE
                  Y = $tgl
                GROUP BY Y, m";
        $qry = $this->db->query($sql)->result();
        $arrhasil = array();
        foreach($qry as $row) {
            array_push($arrhasil,$row->jml);
        }

        return implode(",",$arrhasil);
    }

    function getTerlambatByYear($tgl,$orgId=null)
    {
        $sql2="";
        if ($orgId != null)
        {
            $sql2 = " AND userinfo.deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(COUNT(`process`.date_shift),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN process ON ym.y = YEAR(FROM_UNIXTIME(process.date_shift)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(process.date_shift)) and process.late>0
                    left join userinfo  on userinfo.userid = process.userid
                    ".$sql2."
                WHERE
                  Y = $tgl
                GROUP BY Y, m";
        $qry = $this->db->query($sql)->result();
        $arrhasil = array();
        foreach($qry as $row) {
            array_push($arrhasil,$row->jml);
        }

        return implode(",",$arrhasil);
    }

    function getIjinByYear($tgl,$orgId=null)
    {
        $sql2="";
        if ($orgId != null)
        {
            $sql2 = " AND userinfo.deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(COUNT(`process`.date_shift),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN process ON ym.y = YEAR(FROM_UNIXTIME(process.date_shift)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(process.date_shift)) and ( attendance = 'AB_11' or attendance  like 'AT_AT%' )
                  left join userinfo  on userinfo.userid = process.userid
                    ".$sql2."
                WHERE
                  Y = $tgl
                GROUP BY Y, m";
        $qry = $this->db->query($sql)->result();
        $arrhasil = array();
        foreach($qry as $row) {
            array_push($arrhasil,$row->jml);
        }

        return implode(",",$arrhasil);
    }

    function getTbByYear($tgl,$orgId=null)
    {
        $sql2="";
        if ($orgId != null)
        {
            $sql2 = " AND userinfo.deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(COUNT(`process`.date_shift),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN process ON ym.y = YEAR(FROM_UNIXTIME(process.date_shift)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(process.date_shift)) and (attendance='AB_15' or 
                            attendance='AB_16' or attendance='AT_DK'  )
                   left join userinfo  on userinfo.userid = process.userid
                    ".$sql2."
                WHERE
                  Y = $tgl
                GROUP BY Y, m";
        $qry = $this->db->query($sql)->result();
        $arrhasil = array();
        foreach($qry as $row) {
            array_push($arrhasil,$row->jml);
        }

        return implode(",",$arrhasil);
    }

    function getSakitByYear($tgl,$orgId=null)
    {
        $sql2="";
        if ($orgId != null)
        {
            $sql2 = " AND userinfo.deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(COUNT(`process`.date_shift),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN process ON ym.y = YEAR(FROM_UNIXTIME(process.date_shift)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(process.date_shift)) and ( 
                        attendance = 'AB_1' or attendance  like 'AB_2' or attendance  like 'AB_4' or attendance  like 'AB_5'
                        or attendance  like 'AB_6')
                        left join userinfo  on userinfo.userid = process.userid
                    ".$sql2."
                WHERE
                  Y = $tgl
                GROUP BY Y, m";
        $qry = $this->db->query($sql)->result();
        $arrhasil = array();
        foreach($qry as $row) {
            array_push($arrhasil,$row->jml);
        }

        return implode(",",$arrhasil);
    }

    function getCutiByYear($tgl,$orgId=null)
    {
        $sql2="";
        if ($orgId != null)
        {
            $sql2 = " AND userinfo.deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(COUNT(`process`.date_shift),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN process ON ym.y = YEAR(FROM_UNIXTIME(process.date_shift)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(process.date_shift)) and ( 
                        attendance = 'AB_3' or attendance  like 'AB_7' or attendance  like 'AB_8' or attendance  like 'AB_9'
                         or attendance  like 'AB_14' or attendance  like 'AB_17' )
                         left join userinfo  on userinfo.userid = process.userid
                    ".$sql2."
                WHERE
                  Y = $tgl
                GROUP BY Y, m";
        $qry = $this->db->query($sql)->result();
        $arrhasil = array();
        foreach($qry as $row) {
            array_push($arrhasil,$row->jml);
        }

        return implode(",",$arrhasil);
    }

    function getAlphaByYear($tgl,$orgId=null)
    {
        $sql2="";
        if ($orgId != null)
        {
            $sql2 = " AND userinfo.deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(COUNT(`process`.date_shift),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN process ON ym.y = YEAR(FROM_UNIXTIME(process.date_shift)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(process.date_shift)) and attendance  like 'ALP' 
                    left join userinfo  on userinfo.userid = process.userid
                    ".$sql2."
                WHERE
                  Y = $tgl
                GROUP BY Y, m";
        $qry = $this->db->query($sql)->result();
        $arrhasil = array();
        foreach($qry as $row) {
            array_push($arrhasil,$row->jml);
        }

        return implode(",",$arrhasil);
    }

}

?>         