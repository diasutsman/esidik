<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pesan extends MX_Controller {

	function Pesan(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('data_model','pesan_mdl');

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
		$this->session->set_userdata('menu','1');
        $uri_segment=3;
        $offset = 0;
        $SQLcari =" and untuk_id='".$this->session->userdata('s_id')."'";
        $query = $this->pesan_mdl->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pesan_mdl->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('pesan/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['lstusr'] = $this->pesan_mdl->getUsers();
        $data['lstsifat'] =array("Biasa","Penting","Rahasia");
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
            $SQLcari .= " AND isi_pesan LIKE '%".str_replace('%20',' ',$cr)."%' ";
        }
        $SQLcari .=" and untuk_id='".$this->session->userdata('s_userid')."'";
        $query = $this->pesan_mdl->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->pesan_mdl->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("pesan/pagging");
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
        echo json_encode($this->db->get_where('pesan', array('id' => $noId))->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('lstusr');
        $t2 = $this->input->post('txt1');
        $t3 = $this->input->post('txt2');
        switch ( $this->input->post('lstsifat'))
        {
            case "0":
                $t4 ="Biasa";
                break;
            case "1":
                $t4 ="Penting";
                break;
            case "2":
                $t4 ="Rahasia";
                break;
            default:
                $t4 ="Biasa"; break;
        }


        $dataIn['judul'] = $t2;
        $dataIn['isi_pesan'] =  $t3;
        $dataIn['sifat'] =  $t4;
        $dataIn["dari"] = $this->session->userdata('s_id');

        if ($idx==0)
        {
            $insert = $this->db->insert('pesan', $dataIn);
            if (!$insert) {
                createLog("Menambah pesan ".$t2." ".$t3,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {
                $idNew= $this->db->insert_id();
                if(!empty($t1)) {
                    foreach($t1 as $ar) {
                        $dataIn2['pesan_id'] = $idNew;
                        $dataIn2['untuk_id'] =  $ar;
                        $this->db->insert('pesan_detail', $dataIn2);
                    }
                }

                createLog("Menambah pesan ".$t2." ".$t3,"Sukses");
                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->where('id', $idx);
            $data=$this->db->get("pesan")->row_array();
            log_history("edit","pesan",$data);

            $this->db->where('id', $idx);
            $update = $this->db->update('pesan',$dataIn);
            if(!$update){
                createLog("Merubah pesan ".$t1." ".$t2." ".$t3,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                $this->db->where('pesan_id', $idx);
                $this->db->delete('pesan_detail');
                if(!empty($t1)) {
                    foreach($t1 as $ar) {
                        $dataIn2['pesan_id'] = $idx;
                        $dataIn2['untuk_id'] =  $ar;
                        $this->db->insert('pesan_detail', $dataIn2);
                    }
                }

                createLog("Merubah pesan ".$t1." ".$t2." ".$t3,"Sukses");
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
            $query=$this->db->get("pesan");
            $datas = $query->row_array();
            log_history("delete","pesan",$datas);

            $this->db->where('id',$nId);
            $this->db->delete('pesan');

            if (isset($datas)) {
                createLog("Menghapus pesan " . $datas["isi_pesan"], "Sukses");
            }
        }

        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function setstatus()
    {
        $id = $this->input->post('id');
        $sts = $this->input->post('sts');
        $dataIn["isread"] =$sts;
        $dataIn["tgl_read"] =date("Y-m-d H:i:s");

        $this->db->where('id_detail', $id);
        $this->db->update('pesan_detail',$dataIn);

        createLog("Merubah status pesan ".$id." ".$sts,"Sukses");

        $jmlUnread = $this->db
            ->where('untuk_id', $this->session->userdata('s_userid'))
            ->where('isread', 0)
            ->count_all_results('pesan_detail');

        $data['jmlUnread'] = $jmlUnread;
        $data['msg'] = 'Data berhasil dirubah..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

}
