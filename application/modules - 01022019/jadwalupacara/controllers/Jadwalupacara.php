<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jadwalupacara extends MX_Controller {

    private $aAkses;
	function Jadwalupacara(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('jadwalupacara_model','jadwal');
        $this->load->model('pegawai/pegawai_model','pegawai');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Jadwalupacara", $this->session->userdata('s_access'));
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
        $orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        $SQLcari="";
        if(!empty($orgid)) {
            if ($orgid!="1") {
                $s = array();
                foreach ($orgid as $ar)
                    $s[] = "'" . $ar . "'";

                $SQLcari .= " and deptid in (" . implode(',', $s) . ") ";
            }
        }

        $data['aksesrule']=$this->aAkses;
        $SQLcari .=" and jenispegawai in (1,2) and jftstatus in ('1','2') ";
        $SQLcari .= " ORDER BY id asc";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('jadwalupacara/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();
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
        $data['aksesrule']=$this->aAkses;
        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $jnspeg = $this->input->post('jnspeg');

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

            $SQLcari .= " and jftstatus in (".implode(',', $s).") ";
        }

        if(!empty($jnspeg)) {
            $s = array();
            foreach($jnspeg as $ar)
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
        $this_url = site_url("jadwalupacara/pagging");
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
        $this->load->view('form');
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
            $orgid = $this->session->userdata('deptid')!=''?$this->pegawai->deptonall($this->session->userdata('deptid')):array();

        $range = ($dateend - $datestart) / 86400;


        if ($this->input->post('userid') == '') {
            $userd = $this->pegawai->getuserid($orgid);
            $usera = array();
            foreach ($userd->result() as $usr)
                $usera[] = $usr->userid;
            $useridi = $usera;
        } else
            $useridi = $userid;


        foreach ($useridi as $ros) {
            for ($j = 0; $j <= $range; $j++) {
                $tgal = $datestart + ($j * 86400);
                $jam = $this->input->post('jam');
                $jam2 = $this->input->post('jam2');

                $this->db->select('status');
                $this->db->from('bukatutup');
                $this->db->where('idbln', date('n', $tgal));
                $this->db->where('tahun', date('Y', $tgal));
                $query = $this->db->get();
                if ($query->num_rows()>0) {
                    $bukatutup = $query->row()->status;
                } else {
                    $bukatutup = FALSE;
                }

                if ($bukatutup) {
                    $this->db->select('rosterdate');
                    $this->db->from('rosterdetailsatt_upacara');
                    $this->db->where('rosterdate', date('Y-m-d', $tgal));
                    $this->db->where('userid', $ros);
                    $queryR = $this->db->get();
                    $jml = $queryR->num_rows();
                    if ($jml==0) {
                        $savedata = array('userid' => $ros,
                            'rosterdate' => date('Y-m-d', $tgal),
                            'attendance' =>'UPC',
                            'rostertime' => $jam,
                            'rostertime_end' => $jam2
                        );
                        $this->db->insert('rosterdetailsatt_upacara', $savedata);
                        createLog("Membuat jadwal upacara " . $ros . " " . date('Y-m-d', $tgal) . " " . $jam, "Sukses");
                    } else
                    {
                        $savedata = array('attendance' => 'UPC','rostertime'=>$jam,'rostertime_end' => $jam2);
                        $this->db->where('rosterdate', date('Y-m-d', $tgal));
                        $this->db->where('userid', $ros);
                        $this->db->update('rosterdetailsatt_upacara', $savedata);
                        createLog("Merubah jadwal upacara " . $ros . " " . date('Y-m-d', $tgal) . " " . $jam, "Sukses");
                    }
                }

            }
        }

        $data['msg'] = 'Data berhasil disimpan..';
        $data['status'] = 'succes';

        die(json_encode($data));
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */