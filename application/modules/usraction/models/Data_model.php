<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:08 PM
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
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':"grup_action.*,menu_desc ").
                 "FROM grup_action
                    INNER JOIN menu_new b ON b.menu_id=grup_action.id_menu
		        Where (1=1) ";

        if($id!='' && $id !=null) $sql .= " AND id_action='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }

    public function getDaftarAll($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'a.*,
            (
                SELECT GROUP_CONCAT(menu_desc SEPARATOR \', \')
                FROM menu_new, menu_level   
                WHERE menu_level.menu_level_user_level=a.user_level_id AND FIND_IN_SET( menu_level.menu_level_menu,menu_new.menu_id )
            ) AS lst_menu  
            ')." 
		 from user_level a
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND a.user_level_id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";
        //$sql .=" order by user_level_id desc ";
        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);

    }
}