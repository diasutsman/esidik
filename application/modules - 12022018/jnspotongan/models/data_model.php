<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 12:59 PM
 * absensi.kemendagri.go.id
 */
class Data_model extends CI_Model {

    protected $table = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function getDaftar()
    {
        $rstl = $this->db->get("ref_jnspot");
        return $rstl;
    }
    
    
}