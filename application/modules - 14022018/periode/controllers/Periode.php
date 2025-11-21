<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Periode extends MX_Controller {
    private $aAkses;

	function Periode(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('data_model','periode');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Periode", $this->session->userdata('s_access'));
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
        $data['aksesrule']=$this->aAkses;
	    $thn = $this->db->get_where("bukatutup",array("idbln"=>date("m"),"tahun"=>date("Y")));
        if ($thn->num_rows()==0 )
        {
            $arbln = $this->utils->getBulan();
            $dataIn['idbln'] = date("m");
            $dataIn['tahun'] =  date("Y");
            $dataIn['status'] =  1;
            $dataIn['bulan'] =  $arbln[intval(date("m"))];
            $this->db->insert('bukatutup', $dataIn);
        }

		$this->session->set_userdata('menu','33');
        $data['tahun'] = getListTahun();//$this->periode->getTahun()->result_array();
        $data['pilthn'] = getAktifTahun();
        $uri_segment=3;
        $offset = 0;
        $SQLcari =" and tahun=".$data['pilthn'];
        $SQLcari .=" ORDER BY idbln ASC";
        $query = $this->periode->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->periode->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('periode/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['lstBulan'] = $this->utils->getBulan();
        $data['order'] = 'idbln';
        $data['typeorder'] = 'sorting_asc';
		$this->template->load('template','display',$data);
	}

    public function pagging($page=0)
    {
        $data['aksesrule']=$this->aAkses;
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $thn = $this->input->post('thn');
        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( bulan LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or status LIKE '%".str_replace('%20',' ',$cr)."%' or tahun LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }
        $SQLcari .=" and tahun=".$thn;
        $data['tahun'] = getListTahun();
        $data['pilthn'] = $thn;

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
            $SQLcari .= " ORDER BY idbln ASC ";
        }

        $query = $this->periode->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->periode->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("periode/pagging");
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
        echo json_encode($this->db->get_where('bukatutup', array('id' => $noId))->row_array());
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $t1 = $this->input->post('txt1');
        $t2 = $this->input->post('txt2');
        $t3 = $this->input->post('txt3');
        $t4 = $this->input->post('txt5');
        $arbln = $this->utils->getBulan();
        $dataIn['idbln'] = $t1;
        $dataIn['tahun'] =  $t2;
        $dataIn['status'] =  $t3;
        $dataIn['unit_kerja'] =  $t4;
        $dataIn['bulan'] =  $arbln[$t1];
        if ($idx==0)
        {
            $insert = $this->db->insert('bukatutup', $dataIn);
            if (!$insert) {
                createLog("Menambah periode aktivasi ".$t1." ".$t2." ".$t3,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa menyimpan data..';
                $data['status'] = 'error';

            } else {
                createLog("Menambah periode aktivasi ".$t1." ".$t2." ".$t3,"Sukses");
                $data['msg'] = 'Data berhasil disimpan..';
                $data['status'] = 'succes';
            }
        }
        else
        {
            $this->db->where('id', $idx);
            $data=$this->db->get("bukatutup")->row_array();
            log_history("edit","bukatutup",$data);

            $this->db->where('id', $idx);
            $update = $this->db->update('bukatutup',$dataIn);
            if(!$update){
                createLog("Merubah periode aktivasi ".$t1." ".$t2." ".$t3,"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                createLog("Merubah periode aktivasi ".$t1." ".$t2." ".$t3,"Sukses");
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
            $query=$this->db->get("bukatutup");
            $datas = $query->row_array();
            log_history("delete","bukatutup",$datas);

            $this->db->where('id',$nId);
            $this->db->delete('bukatutup');

            if (isset($datas)) {
                createLog("Menghapus periode aktivasi " . $datas["bulan"] . " " . $datas["tahun"] . " " . $datas["status"], "Sukses");
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
        $dataIn["status"] =$sts;

        $this->db->where('id', $id);
        $data=$this->db->get("bukatutup")->row_array();
        log_history("edit","bukatutup",$data);


        $this->db->where('id', $id);
        $this->db->update('bukatutup',$dataIn);

        createLog("Merubah periode aktivasi ".$id." ".$sts,"Sukses");

        $data['msg'] = 'Data berhasil dirubah..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

}
