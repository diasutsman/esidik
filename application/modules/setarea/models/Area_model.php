<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:03 PM
 * absensi.kemendagri.go.id
 */
class Area_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'a.*,b.areaname AS nama_parent')." 
		 from personnel_area a
		 LEFT JOIN personnel_area b ON b.areaid=a.parent_id
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND a.id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        //$sql .=" order by id asc ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }
}