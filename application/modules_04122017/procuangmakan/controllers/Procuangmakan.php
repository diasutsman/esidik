<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Procuangmakan extends MX_Controller {
    private $aAkses;
	function Procuangmakan(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('Procdata_model');
        $this->load->model('Process_model',"process_model");
        $this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('rptuangmakan/uangmakan_model',"uangmakan");

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Procuangmakan", $this->session->userdata('s_access'));
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
    {$data['aksesrule']=$this->aAkses;
        $this->session->set_userdata('menu','33');
        $data['menu'] = '33';
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


        $SQLcari .=" and jenispegawai in (1,2) and jftstatus in (1,2)";
        $SQLcari .= " ORDER BY id asc";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('procuangmakan/pagging/');
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
        $this_url = site_url("procuangmakan/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    function pegawai()
    {
        $datestart = strtotime(dmyToymd($this->input->post('startdate')));
        $dateend = strtotime(dmyToymd($this->input->post('enddate')));
        $userid = $this->input->post('userid');

        $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $datestart));
        $this->db->where('tahun', date('Y', $datestart));
        $query = $this->db->get();
        if ($query->num_rows()>0) {
            $bukatutup = $query->row()->status;
        } else {
            $bukatutup = FALSE;
        }

        if($bukatutup || $this->session->userdata('s_access')==1) {

            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');

            $user = $this->process_model->getuserbyuser($userid);

            $holiday = $this->process_model->cekholiday();
            $holarray = array();
            $flaghol = array();
            foreach ($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if ($selisih == 0) {
                    $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                    $flaghol[$hol->deptid][$hol->startdate] = $hol->flag;
                } else {
                    $jarak = $selisih / 86400;
                    for ($k = 0; $k <= $jarak; $k++) {
                        $holarray[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->info;
                        $flaghol[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->flag;
                    }
                }
            }

            $this->db->select("process.userid, date_shift, check_in, check_out, attendance, workinholiday, late, early_departure");
            $this->db->from("process");
            $this->db->join("userinfo","process.userid=userinfo.userid ");
            $this->db->where("date_shift >=",date('Y-m-d', $datestart));
            $this->db->where("date_shift <=",date('Y-m-d', $dateend));
            $this->db->where_in('userinfo.userid', $userid);
            $rstl =$this->db->get();
            /*$usrproc = array();
            foreach ($rstl->result() as $rwr) {
                $usrproc[$rwr->userid][date('Y-m-d', strtotime($rwr->date_shift))]=$rwr->attendance;
            }*/

           //proses uang makan
            $this->db->where_in('userid', $userid);
            $this->db->where('tanggal >=', date('Y-m-d', $datestart));
            $this->db->where('tanggal <=', date('Y-m-d', $dateend));
            $this->db->delete('data_uang_makan');

           // $msg = $this->db->last_query();

            foreach($user->result() as $row)
            {
                $kdGol = konversiGolongan($row->golru);
                $umk = $this->uangmakan->ref_uangmakan($kdGol);

                $str1=date('Y-m-d',$datestart);
                $end1=date('Y-m-d',$dateend);
                while (strtotime($str1) <= strtotime($end1)) {

                    $tanggal = date('Y-m-d', strtotime($str1));
                    //$workinholiday = 0;
                    $flagholiday = 0;
                    if (isset($flaghol[$row->deptid][date('Y-m-d', strtotime($tanggal))]) || isset($flaghol['1'][date('Y-m-d', strtotime($tanggal))])) $flagholiday = 1;

                    $flagoff=2;

                    $this->db->select("userid");
                    $this->db->where('userid', $row->userid);
                    $this->db->where('rosterdate', $tanggal);
                    $this->db->where(" absence NOT IN ('OFF','OFFPD') ", NULL, FALSE);
                    $this->db->limit(1);
                    $rsttt = $this->db->get('rosterdetails');
                    $num_results = $rsttt->num_rows();

                    $this->db->select("userid");
                    $this->db->where('userid', $row->userid);
                    $this->db->where('rosterdate', $tanggal);
                    $this->db->limit(1);
                    $rsttt = $this->db->get('rosterdetailsatt');
                    $num_results2 = $rsttt->num_rows();

                    /*if (isset($usrproc[$row->userid][date('Y-m-d', strtotime($tanggal))]))
                    {
                        $att = $usrproc[$row->userid][date('Y-m-d', strtotime($tanggal))];
                        if ($att == 'NWK' || $att == 'NWDS' )
                        {
                            $flagoff = 1;
                        } else{
                            $flagoff = 0;
                        }
                    }*/

                    if ($flagholiday == 0 && $num_results>0 && $num_results2==0) {
                        $jml = $this->pegawai->getrealabsensi($row->userid, date('Y-m-d', strtotime($str1)), date('Y-m-d', strtotime($str1)));

                        if ($jml["jmlHari"] > 0) {
                            $jmlkotor = $jml["jmlHari"] * $umk["nominal"];
                            $pajak = $jmlkotor * ($umk["pajak"] / 100);
                            $jmlBersih = $jmlkotor - $pajak;

                            $datapeg = array(
                                "userid" => $row->userid,
                                "golongan" => $row->golru,
                                "deptid" => $row->deptid,
                                "tanggal" => date('Y-m-d', strtotime($str1)),
                                "tarif" => $umk["nominal"], "jml_pajak" => $pajak,
                                "jum_hadir" => $jml["jmlHari"],
                                "pajak_persen" => $umk["pajak"],
                                "jml_kotor" => $jmlkotor, "bersih" => $jmlBersih
                            );
                            $this->db->insert('data_uang_makan', $datapeg);
                            //$msg .= "insert "+$row->userid."<br>";
                        }
                    }
                    $str1 = date("Y-m-d", strtotime("+1 days", strtotime($str1)));
                }

            }
            $data['msg'] = 'Data sudah diproses..';


        } else {
            $data['msg'] = 'Periode sudah ditutup..';
        }

        $data['status'] = 'succes';
        echo json_encode($data);
    }

    function allpegawai()
    {
        $datestart = strtotime(dmyToymd($this->input->post('startdate')));
        $dateend = strtotime(dmyToymd($this->input->post('enddate')));
        $orgidi = $this->input->post('org')=='undefined'?'1':$this->input->post('org');

        $this->db->select('status');
        $this->db->from('bukatutup');
        $this->db->where('idbln', date('n', $datestart));
        $this->db->where('tahun', date('Y', $datestart));
        $query = $this->db->get();
        if ($query->num_rows()>0) {
            $bukatutup = $query->row()->status;
        } else {
            $bukatutup = FALSE;
        }

        if($bukatutup || $this->session->userdata('s_access')==1) {
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('memory_limit', '-1');
            $orgid = $this->pegawai->deptonall($orgidi);

            $useraidi = array();
            $user = $this->process_model->getuserbyorg($orgid);
            foreach($user->result() as $userya) {
                $useraidi[] = $userya->userid;
                $deptid[$userya->userid] = $userya->deptid;
            }

            if (count($useraidi)>0) {

                $this->db->select("process.userid, date_shift, check_in, check_out, attendance, workinholiday, late, early_departure");
                $this->db->from("process");
                $this->db->join("userinfo", "process.userid=userinfo.userid ");
                $this->db->where("date_shift >=", date('Y-m-d', $datestart));
                $this->db->where("date_shift <=", date('Y-m-d', $dateend));
                $this->db->where_in('userinfo.userid', $useraidi);
                $rstl = $this->db->get();
                /*$usrproc = array();
                foreach ($rstl->result() as $rwr) {
                    $usrproc[$rwr->userid][$rwr->date_shift] = $rwr->attendance;
                }*/

                $holiday = $this->process_model->cekholiday();
                $holarray = array();
                $flaghol = array();
                foreach ($holiday->result() as $hol) {
                    $tglmulai = strtotime($hol->startdate);
                    $tglselesai = strtotime($hol->enddate);
                    $selisih = $tglselesai - $tglmulai;
                    if ($selisih == 0) {
                        $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                        $flaghol[$hol->deptid][$hol->startdate] = $hol->flag;
                    } else {
                        $jarak = $selisih / 86400;
                        for ($k = 0; $k <= $jarak; $k++) {
                            $holarray[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->info;
                            $flaghol[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->flag;
                        }
                    }
                }

                //proses uang makan
                $this->db->where_in('userid', $useraidi);
                $this->db->where('tanggal >=', date('Y-m-d', $datestart));
                $this->db->where('tanggal <=', date('Y-m-d', $dateend));
                $this->db->delete('data_uang_makan');

                foreach ($user->result() as $row) {
                    $kdGol = konversiGolongan($row->golru);
                    $umk = $this->uangmakan->ref_uangmakan($kdGol);

                    $str1 = date('Y-m-d', $datestart);
                    $end1 = date('Y-m-d', $dateend);
                    while (strtotime($str1) <= strtotime($end1)) {

                        $tanggal = date('Y-m-d', strtotime($str1));
                        //$workinholiday = 0;
                        $flagholiday = 0;
                        //if(isset($holarray[$row->deptid][date('Y-m-d', strtotime($tanggal))]) || isset($holarray['1'][date('Y-m-d', strtotime($tanggal))])) $workinholiday = 1;
                        if (isset($flaghol[$row->deptid][date('Y-m-d', strtotime($tanggal))]) || isset($flaghol['1'][date('Y-m-d', strtotime($tanggal))])) $flagholiday = 1;

                        /*$flagoff = 2;
                        if (isset($usrproc[$row->userid][date('Y-m-d', strtotime($tanggal))])) {
                            $att = $usrproc[$row->userid][date('Y-m-d', strtotime($tanggal))];
                            if ($att == 'NWK' || $att == 'NWDS') {
                                $flagoff = 1;
                            } else {
                                $flagoff = 0;
                            }
                        }*/

                        $this->db->select("userid");
                        $this->db->where('userid', $row->userid);
                        $this->db->where('rosterdate', $tanggal);
                        $this->db->where(" absence NOT IN ('OFF','OFFPD') ", NULL, FALSE);
                        $this->db->limit(1);
                        $rsttt = $this->db->get('rosterdetails');
                        $num_results = $rsttt->num_rows();

                        $this->db->select("userid");
                        $this->db->where('userid', $row->userid);
                        $this->db->where('rosterdate', $tanggal);
                        $this->db->limit(1);
                        $rsttt = $this->db->get('rosterdetailsatt');
                        $num_results2 = $rsttt->num_rows();

                        if ($flagholiday == 0 && $num_results>0 &&$num_results2==0) {
                            $jml = $this->pegawai->getrealabsensi($row->userid, date('Y-m-d', strtotime($str1)), date('Y-m-d', strtotime($str1)));

                            $jmlkotor = $jml["jmlHari"] * $umk["nominal"];
                            $pajak = $jmlkotor * ($umk["pajak"] / 100);
                            $jmlBersih = $jmlkotor - $pajak;

                            $datapeg = array("userid" => $row->userid,
                                "golongan" => $row->golru,
                                "deptid" => $row->deptid,
                                "tanggal" => date('Y-m-d', strtotime($str1)),
                                "tarif" => $umk["nominal"], "jml_pajak" => $pajak,
                                "jum_hadir" => $jml["jmlHari"],
                                "pajak_persen" => $umk["pajak"],
                                "jml_kotor" => $jmlkotor, "bersih" => $jmlBersih
                            );
                            $this->db->insert('data_uang_makan', $datapeg);
                        }

                        $str1 = date("Y-m-d", strtotime("+1 days", strtotime($str1)));
                    }

                }
            }
            $data['msg'] = 'Data sudah diproses..';
        } else {
            $data['msg'] = 'Periode sudah ditutup..';
        }

        $data['status'] = 'succes';
        echo json_encode($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */