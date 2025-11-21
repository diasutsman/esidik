<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mtunkir_model extends CI_Model
{
    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'*')." 
		 from mastertunjangan
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND kelasjabatan='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);
    }
}

