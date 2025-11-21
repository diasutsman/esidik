<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:07 PM
 * absensi.kemendagri.go.id
 */
class Data_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getDaftar($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'a.*,b.user_level_name,c.name')." 
		 from users a
		 left join user_level b on a.user_level_id=b.user_level_id
		 left join userinfo c on a.userid=c.userid
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND a.id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        $sql .=" order by a.id desc ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }
}