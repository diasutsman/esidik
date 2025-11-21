<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File: ${FILE_NAME}
 * Author: abdiIwan.
 * Date: 2/24/2017
 * Time: 1:03 PM
 * absensi.kemendagri.go.id
 */
class Sms_setting_model extends CI_Model {

	protected $table = "";

	public function __construct()
	{
		parent::__construct();
	}

	public function getData()
	{
		$sql =  "SELECT * FROM sms_setting WHERE 1=1 LIMIT 0,1";
		//echo $sql;
		return $this->db->query($sql);
    }
}