<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:06 PM
 * absensi.kemendagri.go.id
 */
class Stathadir_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }
    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'attendance.*,ref_status_kategori.ket_status_kategori')." 
		 from attendance 
		 left join ref_status_kategori on id_status_kategori=status_kategori_id
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }
    
}