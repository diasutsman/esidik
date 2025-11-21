<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:01 PM
 * absensi.kemendagri.go.id
 */
class Setpresensi_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'*')." 
		 FROM ref_status_kategori 
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND id_status_kategori='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        //$sql .=" order by jns_potongan asc ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);
    }
}