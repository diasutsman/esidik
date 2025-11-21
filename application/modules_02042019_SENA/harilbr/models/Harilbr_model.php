<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Harilbr_model extends CI_Model
{
    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'a.*,b.deptname')." 
		 from holiday a
		 left join departments b on a.deptid=b.deptid
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND a.id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";

        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);
    }
}

?>
