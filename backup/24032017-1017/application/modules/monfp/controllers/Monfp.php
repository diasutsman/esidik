<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Monfp extends MX_Controller {

	function Monfp(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );
		$this->load->model('setdev/device_model','mdldevice');

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
        $this->session->set_userdata('menu','23');
        $data['menu'] = '23';
        $uri_segment=3;
        $offset = 0;
        $sql=" and a.id=0 order by checktime desc ";
        $query = $this->mdldevice->getFinger(1,10,$offset,null,$sql);
        $jum_data = $this->mdldevice->getFinger(0,null,null,null,$sql);
        $this_url = site_url('monfp/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['order'] = 'checktime';
        $data['typeorder'] = 'sorting_desc';
        $this->template->load('template','display',$data);
    }

    public function pagging($page=0)
    {
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $mulai = dmyToymd($this->input->post('strt'));
        $akhir = dmyToymd($this->input->post('end'));
        $data['order'] = $this->input->post('order');
        $typeorder = $this->input->post('sorting');
        $stt = $this->input->post('stt');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

        $mulai=((isset($mulai) && ($mulai!='' || $mulai!=null))?$mulai:date("Y-m-d"));
        $akhir=((isset($akhir) && ($akhir!='' || $akhir!=null))?$akhir:date("Y-m-d"));
        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( areaname LIKE '%".str_replace('%20',' ',$cr)."%' or  name LIKE '%".str_replace('%20',' ',$cr)."%'  
                        or ipaddress LIKE '%".str_replace('%20',' ',$cr)."%' or badgenumber LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }

        $SQLcari .=" and (date(a.checktime) between '".$mulai."' and '".$akhir."' )";
        $SQLcari .=" and checktype = '".$stt."' ";

        if($typeorder!='' && $typeorder!=null){
            if($typeorder=='sorting'){
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            }else if($typeorder=='sorting_asc'){
                $sorting = 'ASC';
                $data['typeorder'] = 'sorting_asc';
            }else{
                $sorting = 'DESC';
                $data['typeorder'] = 'sorting_desc';
            }
        }else{
            $sorting = 'ASC';
            $data['typeorder'] = 'sorting_asc';
        }

        if($data['order']!='' && $data['order']!=null){
            $SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
        }else{
            $SQLcari .= " ORDER BY checktime desc ";
        }

        $query = $this->mdldevice->getFinger(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->mdldevice->getFinger(0,null,null,null,$SQLcari);
        $this_url = site_url("monfp/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

}

