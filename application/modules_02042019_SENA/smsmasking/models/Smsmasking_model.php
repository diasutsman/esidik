<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 12:55 PM
 * absensi.kemendagri.go.id
 */


class Smsmasking_model extends CI_Model {

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

    public function getListSend($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0,$sOrder=null)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'b.*,a.userid,a.name,a.deptname,a.title,a.privilege')." 
		 from data_send_sms b
		 INNER JOIN view_employee a ON b.nip=a.userid
		Where (1=1)";
        if($id!='' && $id !=null) $sql .= " AND id='".$id."' ";
        if($cari!='' && $cari !=null) $sql .= " $cari ";

        //echo $sql;
        if($sOrder!='' && $sOrder !=null)  {
            $sql .= " Order by ".$sOrder;
        }

        if (isset($dipaging) && $dipaging ==1) {
            $sql .= " limit $offset, $limit ";
        }
        if (isset($dipaging) && $dipaging ==0) {
            //$sql .= " Order by tgl_input desc";
        }

        return $this->db->query($sql);
    }

    public function getAtasan($nip='')
    {
        $value=array();
        $this->db->where(" deptid = (SELECT deptid FROM userinfo WHERE userid='$nip') ",null,false);
        $this->db->order_by('eselon','DESC');
        $this->db->limit(1);
        $queryr=$this->db->get('userinfo');
        if($queryr->num_rows()==1)
        {
            $value['nip'] = $queryr->row()->userid;
            $value['nama'] = $queryr->row()->name;
            $value['deptid'] = $queryr->row()->deptid;
        }
        return $value;

    }

    public function getAtasanByDeptId($deptid='')
    {
        $value=array();
        $this->db->where(" deptid = (SELECT parentid FROM departments WHERE deptid='$deptid') ",null,false);
        $this->db->order_by('eselon','DESC');
        $this->db->limit(1);
        $queryr=$this->db->get('userinfo');
        if($queryr->num_rows()==1)
        {
            $value['nip'] = $queryr->row()->userid;
            $value['nama'] = $queryr->row()->name;
            $value['deptid'] = $queryr->row()->deptid;
        }
        return $value;

    }

}