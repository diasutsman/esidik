<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Statabsen extends MX_Controller {

    private $aAkses;
	function Statabsen(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );
        $this->load->model('utils_model','utils');
		$this->load->model('statabsen_model','statabsen');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Statabsen", $this->session->userdata('s_access'));
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
        $data['aksesrule']=$this->aAkses;
        $uri_segment=3;
        $offset = 0;
        $SQLcari = " ORDER BY id asc";
        $query = $this->statabsen->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->statabsen->getDaftar(0,null,null,null,null);
        $this_url = site_url('statabsen/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['order'] = 'id';
        $data['typeorder'] = 'sorting';
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
        $data['aksesrule']=$this->aAkses;
        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( abid LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or abname LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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
            $sorting = 'ASC';
            $data['typeorder'] = 'sorting_asc';
        }

        if($data['order']!='' && $data['order']!=null){
            $SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
        }else{
            $SQLcari .= " ORDER BY id ASC ";
        }
        $query = $this->statabsen->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->statabsen->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("statabsen/pagging");
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
        echo json_encode($this->db->get_where('absence', array('id' => $noId))->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('txt2');
        $t3 = $this->input->post('txt3');
        $t4 = $this->input->post('status');
        $t5 = $this->input->post('idkat');

        $dataIn['abid'] = $t1;
        $dataIn['abname'] =  $t2;
        $dataIn['value'] =  $t3;
        $dataIn['state'] =  $t4;
        $dataIn['status_kategori_id'] =  $t5;
        if ($idx==0)
        {
            $dataIn['create_date'] =  date("Y-m-d h:i:s");
            $dataIn['create_by'] =  $this->session->userdata('s_username');
            $insert = $this->db->insert('absence', $dataIn);
            if (!$insert) {
                createLog("Membuat status ketidakhadiran ".$t1." ".$t2 ." ".$t3,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {
                createLog("Membuat status ketidakhadiran ".$t1." ".$t2 ." ".$t3,"Sukses");
                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->where('id', $idx);
            $data=$this->db->get("absence")->row_array();
            log_history("edit","absence",$data);

            $dataIn['modif_date'] =  date("Y-m-d h:i:s");
            $dataIn['modif_by'] =  $this->session->userdata('s_username');
            $this->db->where('id', $idx);
            $update = $this->db->update('absence',$dataIn);
            if(!$update){
                createLog("Merubah status ketidakhadiran ".$t1." ".$t2 ." ".$t3,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                createLog("Membuat status ketidakhadiran ".$t1." ".$t2 ." ".$t3,"Sukses");
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
            $query=$this->db->get("absence");
            $datas = $query->row_array();
            log_history("delete","absence",$datas);

            $this->db->where('id',$nId);
            $this->db->delete('absence');

            if (isset($datas)) {
                createLog("Menghapus status ketidakhadiran " . $datas["abid"] . " " . $datas["abname"], "Sukses");
            }
        }

        //createLogUser("Delete Data ".$nId,"Pengguna");
        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */