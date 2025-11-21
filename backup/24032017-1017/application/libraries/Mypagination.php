<?php  if ( ! defined('BASEPATH' )) exit('No direct script access allowed' );
/**
* Class untuk paging
*/
Class Mypagination
{
	function Mypagination()
	{
		$this->SP =& get_instance();
		$this->SP->load->library('pagination' );
	}
	
	function getPagination($total, $per_page, $url, $uri_segment)
	{
		$config['base_url' ] = $url;
		$config['full_tag_open' ] = '<ul class="pagination">' ;
		$config['full_tag_close' ] = '</ul>' ;
		$config['cur_tag_open' ] = '<li class="active"><a href="javascript:;">' ;
		$config['cur_tag_close' ] = '</a></li>' ;
		$config['num_tag_open' ] = '<li class="">' ;
		$config['num_tag_close' ] = '</li>' ;
		$config['next_tag_open' ] = '<li class="">' ;
		$config['next_tag_close' ] = '</li>' ;
		$config['prev_tag_open' ] = '<li class="">' ;
		$config['prev_tag_close' ] = '</li>' ;
		$config['first_tag_open' ] = '<li class="">' ;
		$config['first_tag_close' ] = '</li>' ;
		$config['last_tag_open' ] = '<li class="">' ;
		$config['last_tag_close' ] = '</li>' ;
		$config['uri_segment' ] = $uri_segment;
		$config['next_link' ] = '&#8250' ;
		$config['prev_link' ] = '&#8249' ;
		$config['first_link'] = '&#171';
		$config['last_link'] = '&#187;';
		$config['num_links' ] = 4;
		$config['total_rows' ] = $total;
		$config['per_page' ] = $per_page;
		$this->SP->pagination->initialize($config);
		$data['link' ]=$this->SP->pagination->create_links();
		return $data;
 }
}

?>