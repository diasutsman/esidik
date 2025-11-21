<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  	
if(!function_exists('trans'))
{
	function trans_label($kata)
	{
		 $ci = & get_instance();
		 $ci->lang->load('sitka');
		 $jawaban = $ci->lang->line($kata);
		 return $jawaban;
	}
}

	 
 