<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:05 PM
 * absensi.kemendagri.go.id
 */
class Shift_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }
    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'*')." 
		 from master_shift
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND id_shift='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        $sql .=" order by id_shift asc ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }
    
}