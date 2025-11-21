<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 12:55 PM
 * absensi.kemendagri.go.id
 */


class Procdata_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'a.*,b.deptname AS nama_parent')." 
		 from departments a
		 LEFT JOIN departments b ON b.deptid=a.parentid
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }

    public function getJmlHari($userid,$datestart,$dateend)
    {
        $this->db->where('userid', $userid);
        $this->db->where('tanggal >=', $datestart);
        $this->db->where('tanggal <=', $dateend);
        $rslt = $this->db->get('data_uang_makan');
        return $rslt->num_rows();

    }
	
	public function getJmlHariWFH($userid,$datestart,$dateend)
    {
		$sql =  "SELECT COUNT(*) AS jumlah FROM rosterdetailsatt WHERE 
		userid = '".$userid."' AND rosterdate BETWEEN '".$datestart."' AND '".$dateend."' 
		AND attendance = 'WFH'  ";
        $query = $this->db->query($sql);
		return $query;
    }
    
    
}