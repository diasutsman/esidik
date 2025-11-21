<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kehadiran_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'a.*,a.id as idpegawai, a.create_date as tanggalinput, b.*')." 
		from tb_status_kehadiran a
		INNER JOIN userinfo b ON b.userid=a.nip
		Where (1=1) ";
        if($id!='' && $id !=null) $sql .= " AND id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }
    
    
}