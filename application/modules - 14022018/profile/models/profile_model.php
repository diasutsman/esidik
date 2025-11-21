<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:02 PM
 * absensi.kemendagri.go.id
 */
class Profile_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }
    public function getLastAbsensi($limit = 10)
    {
        $this->db->select("userinfo.userid,userinfo.badgenumber,userinfo.name,checkinout.checktime,departments.deptname");
        $this->db->from("userinfo");
        $this->db->join('departments', 'departments.deptid=userinfo.deptid');
        $this->db->join('checkinout', 'userinfo.userid=checkinout.userid');
        $this->db->where('userinfo.userid', $this->session->userdata('s_userid'));
        $this->db->order_by("checkinout.checktime", "desc");
        $this->db->limit($limit);
        $qry = $this->db->get();

        //echo $this->db->last_query();
        return $qry;
    }

    public function getMyProfile()
    {
        $this->db->select("userinfo.userid,userinfo.badgenumber,userinfo.name,departments.deptname,userinfo.gender,userinfo.title");
        $this->db->join('departments', 'departments.deptid=userinfo.deptid','left');
        $this->db->join('users', 'users.userid=userinfo.userid','left');
        $this->db->where('users.id', $this->session->userdata('s_id'));
        $qry = $this->db->get("userinfo");
        //echo $this->db->last_query();
        return $qry;
    }

    public function getMyProfile2()
    {
        $this->db->select("users.userid,users.username,departments.deptname,1 as gender,user_level.user_level_name");
        $this->db->join('departments', 'departments.deptid=users.dept_id','left');
        $this->db->join('user_level', 'user_level.user_level_id=users.user_level_id','left');
        $this->db->where('users.id', $this->session->userdata('s_id'));
        $qry = $this->db->get("users");
        //echo $this->db->last_query();
        return $qry;
    }

    public function getMyActivities()
    {
        $this->db->from("goltca");
        $this->db->where('user', $this->session->userdata('s_username'));
        $this->db->limit(10);
        $this->db->order_by("logtime desc");
        $qry = $this->db->get();
        //echo $this->db->last_query();
        return $qry;
    }
    
}