<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Usrlevel extends MX_Controller {

    private $aAkses;

	function Usrlevel(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('data_model','usrlevel');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Usrlevel", $this->session->userdata('s_access'));
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
        $SQLcari = " ORDER BY user_level_id desc";
        $query = $this->usrlevel->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->usrlevel->getDaftar(0,null,null,null,null);
        $this_url = site_url('usrlevel/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['order'] = 'user_level_id';
        $data['typeorder'] = 'sorting';
        $data['aksesrule']=$this->aAkses;
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
            $SQLcari .= " AND ( user_level_name LIKE '%".str_replace('%20',' ',$cr)."%'  ) ";
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
            $SQLcari .= " ORDER BY user_level_id ASC ";
        }

        $query = $this->usrlevel->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->usrlevel->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("usrlevel/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['aksesrule']=$this->aAkses;
        $this->load->view('list',$data);
    }

    public function edit($noId=0)
    {
        $sql="select *,(
          SELECT GROUP_CONCAT(menu_level_menu) AS id_grup FROM menu_level WHERE user_level.user_level_id=menu_level.menu_level_user_level
        ) as id_menu
          from user_level where user_level_id=$noId";

        $this->output->set_output( json_encode($this->db->query($sql)->row_array()));
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('status');
        $t3 = explode(",",$this->input->post('txt6'));

        $dataIn['user_level_name'] = $t1;
        $dataIn['state'] = $t2;


        if ($idx==0)
        {
            $insert = $this->db->insert('user_level', $dataIn);
            if (!$insert) {
                createLog("Membuat level user ".$t1,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {
                createLog("Membuat level user ".$t1,"Sukses");

                $this->db->select("user_level_id");
                $this->db->where("user_level_name",$t1);
                $rwd = $this->db->get("user_level")->row_array();

                foreach ( $t3 as $value)
                {
                    $datalevl["menu_level_user_level"]=$rwd["user_level_id"];
                    $datalevl["menu_level_menu"]=$value;
                    $this->db->insert("menu_level",$datalevl);
                }


                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->where('user_level_id', $idx);
            $data=$this->db->get("user_level")->row_array();
            log_history("edit","user_level",$data);

            $this->db->where('user_level_id', $idx);
            $update = $this->db->update('user_level',$dataIn);
            if(!$update){
                createLog("Merubah level user ".$t1,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                createLog("Merubah level user ".$t1,"Sukses");

                $this->db->where('menu_level_user_level', $idx);
                $this->db->delete("menu_level");
                foreach ( $t3 as $value)
                {
                    $datalevl["menu_level_user_level"]=$idx;
                    $datalevl["menu_level_menu"]=$value;
                    $this->db->insert("menu_level",$datalevl);
                }





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

            $this->db->where('menu_level_user_level',$nId);
            $this->db->delete('menu_level');

            $this->db->where('user_level_id', $nId);
            $query=$this->db->get("user_level");
            $datas = $query->row_array();
            log_history("delete","user_level",$datas);

            $this->db->where('user_level_id',$nId);
            $this->db->delete('user_level');

            if (isset($datas)) {
                createLog("Menghapus level user  " . $datas["user_level_name"] , "Sukses");
            }
        }

        $data['msg'] = 'Data berhasil dihapus..';
        $data['status'] = 'succes';

        $this->output->set_output( json_encode($data));
    }
	

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */