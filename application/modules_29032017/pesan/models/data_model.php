<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Data_model extends CI_Model
{
    public function getDaftar($dipaging = 0, $limit = 10, $offset = 1, $id = null, $cari = null, $count = 0)
    {
        $sql = "SELECT " . ((isset($count) && $count == 1) ? 'count(*) as total' : 'pesan_detail.*,pesan.*,userinfo.name') . " 
		 from pesan_detail
		 inner join pesan on pesan_detail.pesan_id=pesan.id
		 left join userinfo on userinfo.userid=pesan.dari
		Where (1=1)";
        if ($id != '' && $id != null) $sql .= " AND id_detail='" . $id . "' ";
        if ($cari != '' && $cari != null) $sql .= " $cari ";
        $sql .= " order by tgl_pesan desc ";
        if (isset($dipaging) && $dipaging == 1) {
            $sql .= " limit $offset, $limit ";
        }
        //echo $sql;
        return $this->db->query($sql);
    }

    public function getUsers()
    {
        $this->db->select('id,username');
        $this->db->from('users');
        $this->db->where('id !=', $this->session->userdata('s_id'));
        $query = $this->db->get();
        $srr = array();
        foreach($query->result() as $row)
        {
            $srr[$row->id]=$row->username;
        }
        return $srr;

        //return $this->db->query($sql);
    }
}
