<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mtunkir extends MX_Controller {

	function Mtunkir(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('mtunkir_model','mtunkir');

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
		$this->session->set_userdata('menu','9');
		$data['menu'] = '9';

        $uri_segment=3;
        $offset = 0;
        $SQLcari = " ORDER BY kelasjabatan desc";
        $query = $this->mtunkir->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->mtunkir->getDaftar(0,null,null,null,null);
        $this_url = site_url('mtunkir/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['order'] = 'kelasjabatan';
        $data['typeorder'] = 'sorting_desc';
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
            $SQLcari .= " AND ( kelasjabatan LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or tunjangan LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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
            $SQLcari .= " ORDER BY kelasjabatan DESC ";
            $data['order'] ="kelasjabatan";
        }

        $query = $this->mtunkir->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->mtunkir->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("mtunkir/pagging");

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
        echo json_encode($this->db->get_where('mastertunjangan', array('kelasjabatan' => $noId))->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('txt2');

        $dataIn['kelasjabatan'] = $t1;
        $dataIn['tunjangan'] =  $t2;

        if ($idx==0)
        {
            $dataIn['create_by'] =  $this->session->userdata('s_username');
            $insert = $this->db->insert('mastertunjangan', $dataIn);
            if (!$insert) {
                createLog("Menambah Master Tunjangan  ".$t1." ".$t2,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {
                createLog("Menambah Master Tunjangan ".$t1." ".$t2,"Sukses");
                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->where('kelasjabatan', $idx);
            $data=$this->db->get("mastertunjangan")->row_array();
            log_history("edit","mastertunjangan",$data);

            $dataIn['modify_by'] =  $this->session->userdata('s_username');
            $dataIn['modif_date'] =  date('Y-m-d H:i:s');
            $this->db->where('kelasjabatan', $idx);
            $update = $this->db->update('mastertunjangan',$dataIn);
            if(!$update){
                createLog("Merubah Master Tunjangan ".$t1." ".$t2,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                createLog("Merubah Master Tunjangan ".$t1." ".$t2,"Sukses");
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

            $this->db->where('kelasjabatan', $nId);
            $query=$this->db->get("mastertunjangan");
            $datas = $query->row_array();
            log_history("delete","mastertunjangan",$datas);

            $this->db->where('kelasjabatan',$nId);
            $this->db->delete('mastertunjangan');

            if (isset($datas)) {
                createLog("Menghapus Master Tunjangan " . $datas["kelasjabatan"] . " " . $datas["tunjangan"], "Sukses");
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