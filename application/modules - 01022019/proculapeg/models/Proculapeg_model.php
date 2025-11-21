<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 12:55 PM
 * absensi.kemendagri.go.id
 */


class Proculapeg_model extends CI_Model {

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

    public function getListApproval($dipaging=0,$limit=10,$offset=1,$id=null,$cari=null,$count=0,$sOrder=null)
    {
        $sql =  "SELECT ".((isset($count) && $count ==1)?'count(*) as total':'b.*,a.userid,a.name,a.deptname,a.title,a.privilege,c.abname')." 
		 from ulapeg_detail_cuti b
		 INNER JOIN view_employee a ON b.nip=a.userid
		 INNER JOIN absence c ON b.jenis_cuti=c.kd_ula
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

    public function getKeterangan($kdula=0)
    {
        $value="Tidak ada datanya";
        $this->db->where('kd_ula',$kdula);
        $queryr=$this->db->get('absence');
        if($queryr->num_rows()==1)
        {
            $value = $queryr->row()->abname;
        }
        return $value;

    }

    public function gettuserid($userid)
    {
        $this->db->select("ulapeg_detail_cuti.*,absence.abid,absence.abname");
        $this->db->from('ulapeg_detail_cuti');
        $this->db->join('absence','ulapeg_detail_cuti.jenis_cuti=absence.kd_ula');
        $this->db->where_in('ulapeg_detail_cuti.id', $userid);
        return $this->db->get()->result();
    }

    public function statusdata($sts)
    {
        $stsList = array('Y' => '<span class="label-info">Disetujui</span>', 'N' => '<span class="label-danger">Ditolak</span>', '' => '<span class="label-warning">Belum di proses</span>', null => '<span class="label-warning-light">Belum diproses</span>');
        return $stsList[$sts];
    }
}