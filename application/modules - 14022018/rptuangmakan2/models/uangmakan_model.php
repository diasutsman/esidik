<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:00 PM
 * absensi.kemendagri.go.id
 */
class Uangmakan_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }


    public function ref_uangmakan($kdid){
        $this->db->from('ref_uangmakan');
        $this->db->where('golongan', $kdid);
        return $this->db->get()->row_array();
    }




}