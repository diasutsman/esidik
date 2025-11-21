
<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sessdata{

	function sesReset($arr_sess,$module){
		$this->CI =& get_instance();
		foreach($arr_sess as $fld){
			$this->CI->session->unset_userdata('filter_'.$module.'_'.$fld,'');
		}
	}

	function sesSet($arr_sess,$module){
		$this->CI =& get_instance();
		foreach($arr_sess as $fld){
			$this->CI->session->set_userdata('filter_'.$module.'_'.$fld,$this->CI->input->post($fld));
		}
	}
	
	function sesGet($arr_sess,$module){
		$this->CI =& get_instance();
		foreach($arr_sess as $fld){
			$this->{$fld} = $this->CI->session->userdata('filter_'.$module.'_'.$fld);
		}
	}

}
