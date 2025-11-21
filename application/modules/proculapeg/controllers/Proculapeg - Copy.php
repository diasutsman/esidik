<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Proculapeg extends MX_Controller {
    private $aAkses;

	function Proculapeg(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('Proculapeg_model','mdl_ulapeg');
        $this->load->model('Process_model',"process_model");
        $this->load->model('report_model');
        $this->load->model('pegawai/pegawai_model','pegawai');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Proculapeg", $this->session->userdata('s_access'));
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

    function index_list()  {
        $data['aksesrule']=$this->aAkses;
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
        $SQLcari .= " ORDER BY tgl_input desc,status_persetujuan asc";
        $query = $this->mdl_ulapeg->getListApproval(1,10,$offset,null,$SQLcari);
        $jum_data = $this->mdl_ulapeg->getListApproval(0,null,null,null,$SQLcari);
        //var_dump($jum_data);
        $this_url = site_url('proculapeg/pagging/');
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
        $query = $this->mdl_ulapeg->getListApproval(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->mdl_ulapeg->getListApproval(0,null,null,null,$SQLcari);
        $this_url = site_url("proculapeg/pagging");
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
        ini_set("pcre.backtrack_limit", PHP_INT_MAX);
        ini_set("pcre.recursion_limit", PHP_INT_MAX);

        $userid = $this->input->post('userid');
        $stsapprov = $this->input->post('stsapprov');

        $datas = $this->mdl_ulapeg->gettuserid($userid);
        foreach($datas as $rpeg) {
            $idkey = $rpeg->id;
            $nip = $rpeg->nip;
            $notes = $rpeg->keterangan;
            $nosk = $rpeg->kode_usulan;
            $attcode = $rpeg->abid;

            $dateend = strtotime($rpeg->takhir);
            $datestart = strtotime($rpeg->tmulai);
            $range = ($dateend - $datestart) / 86400;
            for($x=0;$x<=$range;$x++) {
                $atdate = date('Y-m-d', $datestart + ($x * 86400));
                //isi ke rosterdetailsatt
                if ($stsapprov=='Y') {
                    $this->db->where('userid', $nip);
                    $this->db->where('rosterdate', $atdate);
                    $rsl = $this->db->get("rosterdetailsatt");
                    if ($rsl->num_rows() > 0) {
                        //log
                        $result = $rsl->result();
                        foreach ($result as $row) {
                            $this->db->where('id', $row->id);
                            $query = $this->db->get("rosterdetailsatt");
                            $datas = $query->row_array();
                            log_history("edit", "rosterdetailsatt", $datas);

                            if (isset($datas)) {
                                createLog("Merubah jadwal kerja " . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"], "Sukses");
                            }
                        }
                        //update data
                        $dataupdater = array(
                            'attendance' => $attcode,
                            'notes' => $notes,
                            'nosk' => $nosk,
                            'editby' => $this->session->userdata('s_username')
                        );
                        $this->db->where('userid', $nip);
                        $this->db->where('rosterdate', $atdate);
                        $this->db->update('rosterdetailsatt', $dataupdater);
                    } else {
                        $dataupdate = array('userid' => $nip,
                            'rosterdate' => $atdate,
                            'attendance' => $attcode,
                            'notes' => $notes,
                            'nosk' => $nosk,
                            'editby' => $this->session->userdata('s_username'));
                        $this->db->insert('rosterdetailsatt', $dataupdate);
                        createLog("Membuat jadwal kerja " . $nip . " " . $atdate . " " . $attcode, "Sukses");
                    }
                }
            }

            //update row;
            $dataupda = array(
                'status_persetujuan' => $stsapprov,
                'tgl_approve' => date('Y-m-d'),
                'approve_oleh' => $this->session->userdata('s_username')
            );
            $this->db->where('id', $idkey);
            $this->db->update('ulapeg_detail_cuti', $dataupda);
        }


        $this->sendtoula($userid);
        sleep(30);
        $this->sendtosimpeg($userid);
        sleep(30);
        $data['msg'] = 'Data sudah diproses..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }

    private function sendtoula($luserid){
        $datas = $this->mdl_ulapeg->gettuserid($luserid);
        $urlpost='https://ulapeg.setjen.kemendagri.go.id/api2018/approval_cuti';
        foreach($datas as $rpeg) {
            $arrintd= array(
                'kode_usulan'=>$rpeg->kode_usulan,
                'status_persetujuan'=>$rpeg->status_persetujuan,
                'token'=>'fpulapeg2018'
            );
            apipostdata($urlpost,true,$arrintd);
        }
    }

    private function sendtosimpeg($luserid){
        $datas = $this->mdl_ulapeg->gettuserid($luserid);
        $urlpost='http://ropeg.setjen.kemendagri.go.id/api2018/insert_cuti';
        foreach($datas as $rpeg) {
            if ($rpeg->status_persetujuan==='Y') {
                $arrintd = array(
                    'kode_usulan' => $rpeg->kode_usulan,
                    'nip' => $rpeg->nip,
                    'jenis_cuti' => $rpeg->jenis_cuti,
                    'jum_hari' => $rpeg->jum_hari,
                    'tmulai' => $rpeg->tmulai,
                    'takhir' => $rpeg->takhir,
                    'keterangan' => $rpeg->keterangan,
                    'keterangan_detail' => $rpeg->keterangan_detail,
                    'tgl_input' => $rpeg->tgl_input,
                    'tgl_approve' => $rpeg->tgl_input,
                    'status_persetujuan' => $rpeg->status_persetujuan,
                    'approve_oleh' => $rpeg->approve_oleh,
                    'token' => 'fpulapeg2018'
                );
                apipostdata($urlpost, false, $arrintd);
            }
        }
    }




}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */