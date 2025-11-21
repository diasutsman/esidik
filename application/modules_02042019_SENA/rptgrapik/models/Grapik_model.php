<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Grapik_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();


    }

    function getLastAbsensi($mydate=null,$limit = 2,$orgId=null)
    {
        //$this->db->select("userinfo.userid,userinfo.badgenumber,userinfo.name,checkinout.checktime,departments.deptname");
        $this->db->from("v_data_fp");
        //$this->db->join('departments', 'departments.deptid=userinfo.deptid');
        //$this->db->join('checkinout', 'userinfo.userid=checkinout.userid');
        $this->db->order_by("checktime", "desc");

        if ($mydate != null)
        {
            $this->db->where("DATE(checktime) = ","'".$mydate."'",false);
        }

        if ($orgId != null)
        {
            $this->db->where("deptid in ","('".implode("','", $orgId)."')", false);
        }
        $this->db->limit($limit);

        $qry = $this->db->get();
        return $qry;
    }

    function getLastDayProcess()
    {
        //$sql ="SELECT DATE(checktime) as tgl FROM checkinout ORDER BY checktime DESC LIMIT 1";
        $rtn= date("Y-m-d");
        $sql ="SELECT DATE(MAX(tgl)) as tgl 
              FROM display_data 
              where date(tgl)<=CURDATE()";
        $qry = $this->db->query($sql);
         if ($qry->num_rows()>0)
         {
            $er = $qry->row_array();
             $rtn=$er["tgl"];
         }

        return $rtn;
    }

    function getLastDayTrans()
    {
        $sql ="SELECT DATE(MAX(checktime)) AS tgl FROM checkinout where date(checktime)<=CURDATE()";
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
        $sql ="SELECT ifnull(sum(jml),0) as jml 
              FROM display_data 
              Where tgl = '$tgl' and telat=0 ";

        if ($orgId != null)
        {
            $sql .= " AND deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getTerlambat($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(sum(jml),0) as jml 
            FROM display_data
            Where tgl = '$tgl' and telat>0";
        if ($orgId != null)
        {
            $sql .= " AND deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getIjin($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(sum(jml),0) as jml 
              FROM display_data
              Where tgl = '$tgl' and ( 
            attendance = 'AB_11' or attendance  like 'AT_AT%' )";
        if ($orgId != null)
        {
            $sql .= " AND deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getTb($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(sum(jml),0) as jml 
              FROM display_data
              Where tgl = '$tgl' and (attendance='AB_15' or 
                attendance='AB_16' or attendance='AT_DK'  )";
        if ($orgId != null)
        {
            $sql .= " AND deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getSakit($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(sum(jml),0) as jml 
            FROM display_data
            Where tgl = '$tgl' and ( 
            attendance = 'AB_1' or attendance  like 'AB_2' 
            or attendance  like 'AB_4' or attendance  like 'AB_5'
            or attendance  like 'AB_6' )";
        if ($orgId != null)
        {
            $sql .= " AND deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getCuti($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(sum(jml),0) as jml 
          FROM display_data
          Where tgl = '$tgl' and ( 
            attendance = 'AB_3' or attendance  like 'AB_7' or attendance  like 'AB_8' or attendance  like 'AB_9'
             or attendance  like 'AB_14' or attendance  like 'AB_17' )";
        if ($orgId != null)
        {
            $sql .= " AND deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getAlpha($tgl,$orgId=null)
    {
        $sql ="SELECT ifnull(sum(jml),0) as jml 
              FROM display_data
              Where tgl = '$tgl' and attendance  like 'ALP' ";
        if ($orgId != null)
        {
            $sql .= " AND deptid in ('".implode("','", $orgId)."')";
        }
        $qry = $this->db->query($sql)->row_array();
        return $qry["jml"];
    }

    function getTptwaktuByYear($tgl,$orgId=null)
    {
        $sql2="";
        if ($orgId != null)
        {
            $sql2 = " AND deptid in ('".implode("','", $orgId)."')";
        }

        $sql ="SELECT Y, m, IFNULL(sum(`display_data`.jml),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                    LEFT JOIN display_data ON ym.y = YEAR(FROM_UNIXTIME(display_data.tgl)) AND  
                    ym.m = MONTH(FROM_UNIXTIME(display_data.tgl)) and display_data.telat=0
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
            $sql2 = " AND deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(sum(`display_data`.jml),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN display_data ON ym.y = YEAR(FROM_UNIXTIME(display_data.tgl)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(display_data.tgl)) and display_data.telat>0
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
            $sql2 = " AND deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(sum(`display_data`.jml),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN display_data ON ym.y = YEAR(FROM_UNIXTIME(display_data.tgl)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(display_data.tgl)) and ( attendance = 'AB_11' or attendance  like 'AT_AT%' )
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
            $sql2 = " AND deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(sum(`display_data`.jml),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN display_data ON ym.y = YEAR(FROM_UNIXTIME(display_data.tgl)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(display_data.tgl)) and (attendance='AB_15' or 
                            attendance='AB_16' or attendance='AT_DK'  )
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
            $sql2 = " AND deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(sum(`display_data`.jml),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN display_data ON ym.y = YEAR(FROM_UNIXTIME(display_data.tgl)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(display_data.tgl)) and ( 
                        attendance = 'AB_1' or attendance  like 'AB_2' or attendance  like 'AB_4' or attendance  like 'AB_5'
                        or attendance  like 'AB_6')
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
            $sql2 = " AND deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(sum(`display_data`.jml),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN display_data ON ym.y = YEAR(FROM_UNIXTIME(display_data.tgl)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(display_data.tgl)) and ( 
                        attendance = 'AB_3' or attendance  like 'AB_7' or attendance  like 'AB_8' or attendance  like 'AB_9'
                         or attendance  like 'AB_14' or attendance  like 'AB_17' )
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
            $sql2 = " AND deptid in ('".implode("','", $orgId)."')";
        }
        $sql ="SELECT Y, m, IFNULL(sum(`display_data`.jml),0) AS jml
                FROM (
                  SELECT Y, m
                  FROM
                    (SELECT YEAR(CURDATE()) Y UNION ALL SELECT YEAR(CURDATE())-1) years,
                    (SELECT 1 m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                      UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                  LEFT JOIN display_data ON ym.y = YEAR(FROM_UNIXTIME(display_data.tgl)) AND 
                    ym.m = MONTH(FROM_UNIXTIME(display_data.tgl)) and attendance  like 'ALP' 
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

    function getDisplayData($tgl,$orgId=null)
    {

        $addwhere="";
        if ($orgId != null)
        {
            $addwhere = " AND deptid in ('".implode("','", $orgId)."')";
        }

        $sql ="
                SELECT 'TEPATWAKTU' AS ket, IFNULL(sum(Jml),0) AS jml 
                FROM v_ontime_by_date 
                WHERE date_shift = '$tgl' $addwhere
                UNION ALL
                SELECT 'TERLAMBAT' AS ket, IFNULL(sum(Jml),0) AS jml 
                FROM v_late_by_date
                WHERE date_shift = '$tgl' $addwhere
                UNION ALL
                SELECT 'IJIN' AS ket, IFNULL(sum(Jml),0) AS jml 
                FROM v_ijin_by_date
                WHERE date_shift = '$tgl' $addwhere
                UNION ALL
                SELECT 'ALPHA' AS ket, IFNULL(sum(Jml),0) AS jml 
                FROM v_alpa_by_date
                WHERE date_shift = '$tgl' $addwhere
                UNION ALL
                SELECT 'SAKIT' AS ket, IFNULL(sum(Jml),0) AS jml 
                FROM v_sakit_by_date
                WHERE date_shift = '$tgl' $addwhere
                UNION  ALL   
                SELECT 'CUTI' AS ket, IFNULL(SUM(jml),0) AS jml 
                FROM v_cuti_by_date
                WHERE date_shift = '$tgl' $addwhere   ";

        return $this->db->query($sql)->result_array();
    }

    function getDisplayGrap($tahun,$orgId=null)
    {
        $addwhere="";
        if ($orgId != null)
        {
            $addwhere = " AND deptid in ('".implode("','", $orgId)."')";
        }

        $sql ="SELECT tahun, bulan,(
                SELECT IFNULL(count(*),0) 
                FROM view_ontime
                WHERE month(date_shift) = bulan AND year(date_shift)=tahun  $addwhere
                ) AS TEPATWAKTU,
                (
                SELECT IFNULL(count(*),0) 
                FROM view_late
                WHERE month(date_shift) = bulan AND year(date_shift)=tahun $addwhere
                ) AS TERLAMBAT,
                (
                SELECT IFNULL(SUM(jml),0) 
                FROM v_thn_bln_display
                WHERE bln = bulan AND thn=tahun AND ( ( attendance = 'AB_11' OR attendance  LIKE 'AT_AT%' ) )
                $addwhere
                ) AS IJIN,
                (
                SELECT IFNULL(count(*),0) 
                FROM view_alpa
                WHERE month(date_shift) = bulan AND year(date_shift)=tahun $addwhere
                ) AS ALPHA,
                (
                SELECT IFNULL(count(*),0) 
                FROM view_sakit
                WHERE month(date_shift) = bulan AND year(date_shift)=tahun  $addwhere
                ) AS SAKIT,
                (
                SELECT IFNULL(SUM(jml),0) 
                FROM v_thn_bln_display
                WHERE bln = bulan AND thn=tahun AND ( 
                            attendance = 'AB_3' OR attendance  LIKE 'AB_7' OR attendance  LIKE 'AB_8' OR attendance  LIKE 'AB_9'
                            OR attendance  LIKE 'AB_14' OR attendance  LIKE 'AB_17' ) $addwhere
                ) AS CUTI
                
                        FROM (
                          SELECT tahun, bulan
                          FROM
                            (SELECT YEAR(CURDATE()) tahun UNION ALL SELECT YEAR(CURDATE())-1) years,
                            (SELECT 1 bulan UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                              UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                              UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) months) ym
                        WHERE tahun = $tahun
                ";
        return $this->db->query($sql)->result();
    }

}

?>         