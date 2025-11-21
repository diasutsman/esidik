<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Usrdata extends MX_Controller {

	function Usrdata(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('data_model','usrdata');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }

        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');

	}


	function killSession()
	{
	}
	
	function index()
	{
		$this->killSession();
		$this->index_list();
	}
	
	function index_list()
	{
		$this->session->set_userdata('menu','33');
		$data['menu'] = '33';

        $uri_segment=3;
        $offset = 0;
        $query = $this->usrdata->getDaftar(1,10,$offset,null,null);
        $jum_data = $this->usrdata->getDaftar(0,null,null,null,null);
        $this_url = site_url('usrdata/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();

		$this->template->load('template','display',$data);
	}

    public function pagging($page=0)
    {
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( username LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or email LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }


        $query = $this->usrdata->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->usrdata->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("usrdata/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }
	

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */