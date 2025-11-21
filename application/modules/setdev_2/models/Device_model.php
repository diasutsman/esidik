<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:04 PM
 * absensi.kemendagri.go.id
 */
class Device_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'a.*,b.areaname,c.deptname')." 
		 from iclock a
		 left join personnel_area b on b.areaid=a.areaid
		 left join departments c on c.deptid=a.iddept
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

    public function getFinger($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'a.*,b.badgenumber,b.name,d.areaname,e.alias,e.ipaddress,e.sn')." 
		FROM checkinout a
        INNER JOIN userinfo b ON b.userid=a.userid
        INNER JOIN iclock e ON e.sn=a.sn
        INNER JOIN personnel_area d ON d.areaid = e.areaid
		Where (1=1) ";
        if($id!='' && $id !=null) $sql .= " AND a.id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        //$sql .=" order by checktime desc ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //die($sql);
        return $this->db->query($sql);

    }
    
    
}