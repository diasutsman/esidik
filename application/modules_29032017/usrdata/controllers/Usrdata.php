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

        $rows1= $this->db->where("state",1)->get("user_level");
        foreach($rows1->result() as $row)
        {
            $dataLevel[$row->user_level_id] = $row->user_level_name;
        }
        $data["lstLevel"] = $dataLevel;

        $uri_segment=3;
        $offset = 0;
        $SQLcari = " ORDER BY a.id desc";
        $query = $this->usrdata->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->usrdata->getDaftar(0,null,null,null,null);
        $this_url = site_url('usrdata/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['order'] = 'a.id';
        $data['typeorder'] = 'sorting';
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
                        or a.email LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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
            $SQLcari .= " ORDER BY a.id ASC ";
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

    public function edit($noId=0)
    {
        $this->db->select("id,username,email,user_level_id,dept_id,area_id,userid,state");
        echo json_encode($this->db->get_where('users', array('id' => $noId))->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('txt2');
        $t3 = $this->input->post('txt3');
        $t4 = $this->input->post('txt4');
        $t5 = $this->input->post('level');
        $status = $this->input->post('status');

        $dataIn['username'] = $t1;
        $dataIn['email'] = $t2;
        $dataIn['userid'] = $t3;
        $dataIn['dept_id'] = $t4;
        $dataIn['area_id'] = $t5;
        $dataIn['state'] = $status;
        $dataIn['user_level_id'] = $t5;


        if ($idx==0)
        {
            $insert = $this->db->insert('users', $dataIn);
            if (!$insert) {
                createLog("Membuat user ".$t1,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {
                createLog("Membuat user ".$t1,"Sukses");
                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->where('id', $idx);
            $data=$this->db->get("users")->row_array();
            log_history("edit","users",$data);

            $this->db->where('id', $idx);
            $update = $this->db->update('users',$dataIn);
            if(!$update){
                createLog("Merubah user ".$t1,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                createLog("Merubah user ".$t1,"Sukses");
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
            $query=$this->db->get("users");
            $datas = $query->row_array();
            log_history("delete","users",$datas);

            $this->db->where('id',$nId);
            $this->db->delete('users');

            if (isset($datas)) {
                createLog("Menghapus user  " . $datas["username"] , "Sukses");
            }
        }

        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function rubihpwd()
    {
        $id = $this->input->post('idold');
        $pwd = $this->input->post('txtpassnew');
        $dataIn["password_md5"]=md5($pwd);
        $this->db->where('id', $id);
        $this->db->update('users',$dataIn);

        $data['msg'] = 'Password berhasil dirubah..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }




}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */