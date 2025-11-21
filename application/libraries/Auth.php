<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth {

	function check_user_authentification()
	{
		$CI =& get_instance();
		$CI->load->library('session');
		$allow = TRUE;
		if ($this->is_logged_in())
		{
			return true;

		}else{
			$data = array(
				'SESS_LOGIN_STATEMENT' => 'Akses Ditolak ;)',
				'error_msg' => 'Anda harus login terlebih dahulu !'
			);
			$CI->session->set_userdata($data);
			redirect('main');
		}
	}

	function is_logged_in()
	{
		$CI =& get_instance();
		$CI->load->library('session');

		return $CI->session->userdata('s_id');
	}

	function hak_permission($id_user,$url){
		$CI =& get_instance();
		if($id_user==1){
			return true;
		}else{
			$sql = "select a.* from trgroupmenu a
					left join tmenu b on (a.menuid = b.menuid)
					where a.groupid = '".$id_user."'
					and b.linkaction2 = '".$url."'";
			$query = $CI->db->query($sql);
			if($query->num_rows() > 0){
				return true;
			}
			else
			{
				redirect('no_akses');
			}
		}
		
	}
	
	function akses_permit($hasil,$abjad)
	{
		if($hasil==$abjad){
			return true;
		}else{
			redirect('no_akses');
		}
	}
}

?>
