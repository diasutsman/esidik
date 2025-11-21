<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Harilbr extends MX_Controller {

	function Harilbr(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );
        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
		$this->load->model('harilbr_model','harilbr');
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
		$this->session->set_userdata('menu','9');
		$data['menu'] = '9';

        $uri_segment=3;
        $offset = 0;
        $SQLcari = " ORDER BY a.startdate desc";
        $query = $this->harilbr->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->harilbr->getDaftar(0,null,null,null,null);
        $this_url = site_url('harilbr/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['order'] = 'a.startdate';
        $data['typeorder'] = 'sorting_desc';
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
            $SQLcari .= " AND ( info LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or startdate LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }

        $data['order'] = $this->input->post('order');
        $typeorder = $this->input->post('sorting');

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
            $sorting = 'DESC';
            $data['typeorder'] = 'sorting_desc';
        }

        if($data['order']!='' && $data['order']!=null){
            $SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
        }else{
            $SQLcari .= " ORDER BY a.startdate Desc ";
        }
        $query = $this->harilbr->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->harilbr->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("harilbr/pagging");
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
        $this->db->select("a.id,DATE_FORMAT(startdate,'%d-%m-%Y') startdate,DATE_FORMAT(enddate,'%d-%m-%Y') enddate,info,a.deptid,flag,b.deptname",false);
        $this->db->from("holiday a");
        $this->db->join("departments b ","a.deptid=b.deptid","left");
        $this->db->where("a.id",$noId);
        echo json_encode($this->db->get()->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('start');
        $t2 = $this->input->post('end');
        $t3 = $this->input->post('txt3');
        $t4 = $this->input->post('unit_search');

        $dataIn['startdate'] = dmyToymd($t1);
        $dataIn['enddate'] =  dmyToymd($t2);
        $dataIn['info'] =  $t3;
        $dataIn['deptid'] =  $t4;

        if ($idx==0)
        {
            $insert = $this->db->insert('holiday', $dataIn);
            if (!$insert) {

                createLog("Membuat hari libur ".$t1." s/d ".$t2 ." ".$t3." ".$t4,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {
                createLog("Membuat hari libur ".$t1." s/d ".$t2 ." ".$t3." ".$t4,"Sukses");
                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->where('id', $idx);
            $data=$this->db->get("holiday")->row_array();
            log_history("edit","holiday",$data);

            $this->db->where('id', $idx);
            $update = $this->db->update('holiday',$dataIn);
            if(!$update){
                createLog("Merubah hari libur ".$t1." s/d ".$t2 ." ".$t3." ".$t4,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';
            }else{
                createLog("Merubah hari libur ".$t1." s/d ".$t2 ." ".$t3." ".$t4,"Sukses");
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
            $query=$this->db->get("holiday");
            $datas = $query->row_array();
            log_history("delete","holiday",$datas);

            $this->db->where('id',$nId);
            $this->db->delete('holiday');

            if (isset($datas)) {
                createLog("Menghapus hari libur " . $datas["startdate"] . " s/d " . $datas["enddate"] . " " . $datas["info"], "Sukses");
            }
        }

        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */