<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Jadwal extends MX_Controller {

	function Jadwal(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('jadwal_model','jadwal');
        $this->load->model('pegawai/pegawai_model','pegawai');

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
        $this->session->set_userdata('menu','18');
        $data['menu'] = '18';
        $uri_segment=3;
        $offset = 0;
        //$orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        $SQLcari="";
        /*if(!empty($orgid)) {
            $s = array();
            foreach($orgid as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and deptid in (".implode(',', $s).") ";
        }*/


        $SQLcari .=" and jenispegawai in (1,2)";
        $SQLcari .= " ORDER BY id asc";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('jadwal/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['order'] = 'id';
        $data['typeorder'] = 'sorting';
        $this->template->load('template','display',$data);
    }

    public function pagging($page=0)
    {
        $priv = $this->pegawai->getprivilege();
        $privi = array();
        foreach($priv->result() as $privilege)
        {
            $privi[$privilege->id] = $privilege->privilege;
        }
        $data['priv'] = $privi;

        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');

        $limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);

        $SQLcari="";
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( name LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or badgenumber LIKE '%".str_replace('%20',' ',$cr)."%' or userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
        }

        if (isset($org) && ($org!='' || $org!=null))
        {
            $orgid = $this->pegawai->deptonall($org);
        } else {
            $orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        }

        if(!empty($orgid)) {
            $s = array();
            foreach($orgid as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and deptid in (".implode(',', $s).") ";
        }

        if(!empty($stspeg)) {
            $s = array();
            foreach($stspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and jenispegawai in (".implode(',', $s).") ";
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
            $SQLcari .= " ORDER BY id ASC ";
            $data['order'] ="id";
        }
        $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("jadwal/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    function form()
    {
        $data['lstShift'] = $this->utils->getShiftAktif()->result();
        $this->load->view('form',$data);
    }

    function save()
    {
        ini_set('MAX_EXECUTION_TIME', '-1');

        $postdatestart = explode('T', $this->input->post('start')."T00:00:00");
        $postdateend = explode('T', $this->input->post('end')."T00:00:00");
        $datestart = strtotime(dmyToymd($postdatestart[0]));
        $dateend = strtotime(dmyToymd($postdateend[0]));
        $userid = explode(',', $this->input->post('userid'));

        if($this->input->post('orgid'))
            $orgid = $this->pegawai->deptonall($this->input->post('orgid'));
        else
            $orgid = $this->session->userdata('deptid')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('deptid'))):array();

        $range = ($dateend - $datestart) / 86400;
        $arraish = array();
        $arraisha = array();

        if($this->input->post('userid')=='') {
            $userd = $this->pegawai->getuserid($orgid);
            $usera = array();
            foreach($userd->result() as $usr)
                $usera[] = $usr->userid;
            $useridi = $usera;
        } else
            $useridi = $userid;

        $this->db->where_in('userid', $useridi);
        $this->db->where('rosterdate >=', date('Y-m-d', $datestart));
        $this->db->where('rosterdate <=', date('Y-m-d', $dateend));
        $result =$this->db->get('rosterdetails')->result();
        foreach($result as $row) {
            $this->db->where('id', $row->id);
            $query=$this->db->get("rosterdetails");
            $datas = $query->row_array();
            log_history("delete","rosterdetails",$datas);

            if (isset($datas)) {
                createLog("Menghapus jadwal " . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["absence"]. " " . $datas["attendance"], "Sukses");
            }

            $this->db->where('id', $row->id);
            $this->db->delete('rosterdetails');
        }

        /*$this->db->where_in('userid', $useridi);
        $this->db->where('rosterdate >=', date('Y-m-d', $datestart));
        $this->db->where('rosterdate <=', date('Y-m-d', $dateend));
        $this->db->delete('rosterdetails');*/

        foreach($useridi as $ros) {
            for($j=0;$j<=$range;$j++) {
                $day = date('N', $datestart + ($j*86400));
                $tgal = $datestart + ($j*86400);

                if($day==1) {

                    $arraish[$tgal] = $this->input->post('sel1');
                    /*if($this->input->post('cek1')=='on') {
                        $arraisha[$tgal] = null;
                    } else {
                        $arraisha[$tgal] = 'NWDS';
                    }*/
                } else if ($day==2) {
                    $arraish[$tgal] = $this->input->post('sel2');
                    /*if($this->input->post('cek2')=='on') {
                        $arraisha[$tgal] = null;
                    } else {
                        $arraisha[$tgal] = 'NWDS';
                    }*/
                } else if ($day==3) {
                    $arraish[$tgal] = $this->input->post('sel3');
                    /*if($this->input->post('cek3')=='on') {
                        $arraisha[$tgal] = null;
                    } else {
                        $arraisha[$tgal] = 'NWDS';
                    }*/
                } else if ($day==4) {
                    $arraish[$tgal] = $this->input->post('sel4');
                    /*if($this->input->post('cek4')=='on') {
                        $arraisha[$tgal] = null;
                    } else {
                        $arraisha[$tgal] = 'NWDS';
                    }*/
                } else if ($day==5) {
                    $arraish[$tgal] = $this->input->post('sel5');
                    /*if($this->input->post('cek4')=='on') {
                        $arraisha[$tgal] = null;
                    } else {
                        $arraisha[$tgal] = 'NWDS';
                    }*/
                } else if ($day==6) {
                    $arraish[$tgal] = $this->input->post('sel6');
                    /*if($this->input->post('cek6')=='on') {
                        $arraisha[$tgal] = null;
                    } else {
                        $arraisha[$tgal] = 'NWDS';
                    }*/
                } else if ($day==7) {
                    $arraish[$tgal] = $this->input->post('sel7');
                    /*if($this->input->post('cek7')=='on') {
                        $arraisha[$tgal] = null;
                    } else {
                        $arraisha[$tgal] = 'NWDS';
                    }*/
                }

                /*if($arraisha[$tgal]==null) {

                    $savedata = array (
                        'userid'		=> $ros,
                        'rosterdate' 	=> date('Y-m-d', $tgal),
                        'absence'		=> $arraish[$tgal]
                    );
                    $this->db->insert('rosterdetails', $savedata);

                    createLog("Membuat jadwal ".$ros." ".date('Y-m-d', $tgal) ." ".$arraish[$tgal],"Sukses");
                } else {*/
                    if ($arraish[$tgal]!=0) {
                        $savedata = array('userid' => $ros, 'rosterdate' => date('Y-m-d', $tgal), 'absence' => $arraish[$tgal]);
                        $this->db->insert('rosterdetails', $savedata);
                        createLog("Membuat jadwal " . $ros . " " . date('Y-m-d', $tgal) . " " . $arraish[$tgal], "Sukses");
                    }
                /*}*/
            }
        }
        /*$actionlog = array(
            'user'			=> $this->session->userdata('s_username'),
            'ipadd'			=> $this->input->ip_address(),
            'logtime'		=> date("Y-m-d H:i:s"),
            'logdetail'		=> 'Assign work schedulle',
            'info'			=> "Sukses Pembuatan jadwal"
        );
        $this->db->insert('goltca', $actionlog);*/

        $data['msg'] = 'Data berhasil disimpan..';
        $data['status'] = 'succes';
        die(json_encode($data));
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */