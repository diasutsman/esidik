<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Setarea extends MX_Controller {
    private $aAkses;

	function Setarea(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('area_model','area');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Setarea", $this->session->userdata('s_access'));
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
		$this->session->set_userdata('menu','2');
		$data['menu'] = '2';
        $data['aksesrule']=$this->aAkses;
        $uri_segment=3;
        $offset = 0;
        $SQLcari = " ORDER BY a.id asc";
        $query = $this->area->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->area->getDaftar(0,null,null,null,null);
        $this_url = site_url('setarea/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['order'] = 'a.id';
        $data['typeorder'] = 'sorting_asc';
		$this->template->load('template','display',$data);
	}

    public function pagging($page=0)
    {
        $data['aksesrule']=$this->aAkses;
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);
        $data['order'] = $this->input->post('order');
        $typeorder = $this->input->post('sorting');
        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( a.areaname LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or a.areaid LIKE '%".str_replace('%20',' ',$cr)."%' or b.areaid LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or b.areaname LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }
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
            $SQLcari .= " ORDER BY a.id asc ";
        }
        $query = $this->area->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->area->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("setarea/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    public function edit($noId=0)
    {
         echo json_encode($this->area->getDaftar(0,null,null,$noId,null,null)->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('txt2');
        $t3 = $this->input->post('area_search');
		$t4 = $this->input->post('latitude');
		$t5 = $this->input->post('longitude');
	    $t6 = $this->input->post('radius');
		$t7 = $this->input->post('statusarea');

        $dataIn['areaid'] = $t1;
        $dataIn['areaname'] =  $t2;
        $dataIn['parent_id'] =  $t3;
		$dataIn['latitude'] =  $t4;
		$dataIn['longitude'] =  $t5;
		$dataIn['radius'] =  $t6;
		$dataIn['statusarea'] =  $t7;
		
        if ($idx==0)
        {
            $dataIn['create_date'] =  date("Y-m-d h:i:s");
            $dataIn['create_by'] =  $this->session->userdata('s_username');
            $insert = $this->db->insert('personnel_area', $dataIn);
            if (!$insert) {

                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {

                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->where('id', $idx);
            $data=$this->db->get("personnel_area")->row_array();
            log_history("edit","personnel_area",$data);

            $dataIn['modif_date'] =  date("Y-m-d h:i:s");
            $dataIn['modif_by'] =  $this->session->userdata('s_username');
            $this->db->where('id', $idx);
            $update = $this->db->update('personnel_area',$dataIn);
            if(!$update){

                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                $data['msg'] = 'Data berhasil dirubah..';
                $data['status'] = 'succes';
            }
        }
        die(json_encode($data));
    }

    public function hapus()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;


            $this->db->where('id', $nId);
            $query=$this->db->get("personnel_area");
            $datas = $query->row_array();
            log_history("delete","personnel_area",$datas);


            $this->db->where('id',$nId);
            $this->db->delete('personnel_area');
        }

        //createLogUser("Delete Data ".$nId,"Pengguna");
        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */