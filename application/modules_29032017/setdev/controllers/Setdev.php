<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Setdev extends MX_Controller {

	function Setdev(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

		$this->load->model('device_model','setdev');

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
		$this->session->set_userdata('menu','2');
		$data['menu'] = '2';
        $uri_segment=3;
        $offset = 0;
        $SQLcari = " ORDER BY a.id asc";
        $query = $this->setdev->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->setdev->getDaftar(0,null,null,null,null);
        $this_url = site_url('setdev/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['resulttemp'] = $this->db->get("devtemp")->result();
        $data['order'] = 'terminal_id';
        $data['typeorder'] = 'sorting_asc';

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
            $SQLcari .= " AND ( b.areaname LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or a.alias LIKE '%".str_replace('%20',' ',$cr)."%' or a.ipaddress LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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
            $SQLcari .= " ORDER BY a.id asc ";
        }

        $query = $this->setdev->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->setdev->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("setdev/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();

        $this->load->view('list',$data);
    }

    public function reboot()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'REBOOT',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("Reboot Mesin " . $datas["terminal_id"] . " " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah me-restart mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public  function rubahsn()
    {
        $id = $this->input->post('idold');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();
            log_history("update","iclock",$datas);

            $snbaru = $this->input->post('txt1');

            $dataupdate = array('sn' => $snbaru);
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $dataupdate);

            if (isset($datas)) {
                createLog("Merubah SN " . $datas["sn"].' ke '.$snbaru , "Sukses");
            }
        }

        $data['msg'] = 'SN Mesin sudah dirubah..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public  function rubaharea()
    {
        $id = $this->input->post('idarea');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();
            log_history("update","iclock",$datas);

            $areabaru = $this->input->post('area');

            $dataupdate = array('areaid' => $areabaru);
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $dataupdate);

            $comm = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'st'			=>1,
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $comm);

            if (isset($datas)) {
                createLog("Merubah Area " . $datas["areaid"].' ke '.$areabaru , "Sukses");
            }
        }

        $data['msg'] = 'SN Mesin sudah dirubah..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function synkronisasi()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $comm = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'st'			=>1,
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $comm);

            if (isset($datas)) {
                createLog("Sinkronisasi Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah me-sinkronisasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }


    public function fpinfo()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $comm = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'INFO',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $comm);

            if (isset($datas)) {
                createLog("Informasi Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function transakinfo()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $updat = array (
                'stamp'	  =>0
            );
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $updat);

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("Informasi Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function transakuser()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $updat = array (
                'opstamp'	  =>0
            );
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $updat);

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("User Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }


    public function transakfoto()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $updat = array (
                'photostamp'	  =>0
            );
            $this->db->where('sn', $datas["sn"]);
            $this->db->update('iclock', $updat);

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CHECK',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("Foto Peawai Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }


    public function transakdelalllog()
    {
        $id = $this->input->post('id');
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CLEAR LOG',
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("Hapus log Mesin " . $datas["sn"], "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    public function transakdeldatelog()
    {
        $id = $this->input->post('id');
        $tanggal = strtotime(dmyToymd($this->input->post('deltanggal'))) + 86400;
        for($i=0;$i<count($id);$i++){
            $nId = is_numeric($id[$i])?$id[$i]:0;

            $this->db->where('id', $nId);
            $query=$this->db->get("iclock");
            $datas = $query->row_array();

            $arransemen = array (
                'sn'			=>$datas["sn"],
                'cmd'			=>'CLEAR ATTLOG BY TIME '.date('YmdHis', $tanggal),
                'status'		=>1,
                'submittime'	=>date("Y-m-d H:i:s")
            );
            $this->db->insert('command', $arransemen);

            if (isset($datas)) {
                createLog("Hapus transaksi Mesin " . $datas["sn"].' tgl '.$this->input->post('deltanggal'), "Sukses");
            }
        }

        $data['msg'] = 'Perintah mengambil informasi mesin sudah dilakukan..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */