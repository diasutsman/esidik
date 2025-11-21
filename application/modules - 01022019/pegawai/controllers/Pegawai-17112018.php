<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pegawai extends MX_Controller {

    private $aAkses;
	function Pegawai(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('pegawai_model','pegawai');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Pegawai", $this->session->userdata('s_access'));
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
        $SQLcari .=" and jftstatus in (1,2) and jenispegawai in (1,2) ";
        $SQLcari .= " ORDER BY id asc";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('pegawai/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),5,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();

        $priv = $this->pegawai->getprivilege();
        $privi = array();
        foreach($priv->result() as $privilege)
        {
            $privi[$privilege->id] = $privilege->privilege;
        }
        $data['priv'] = $privi;
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
                        or badgenumber LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or userid LIKE '%".str_replace('%20',' ',$cr)."%'
                        or deptname LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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
            $sorting = 'ASC';
            $data['typeorder'] = 'sorting_asc';
        }

        if($data['order']!='' && $data['order']!=null){
            $SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
        }else{
            $SQLcari .= " ORDER BY Id ASC ";
        }

        $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
        //echo $this->db->last_query();
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("pegawai/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    public function form($noId=null)
    {
        $noId = $noId ==null ? 0 : $noId ;
        $data["id"]=$noId;
        $datar = $this->db->get_where('view_employee', array('id' => $noId))->row_array();
        if (count($datar)==0) {
            $row = $this->db->query("select * from view_employee limit 1")->row_array();
            foreach ($row as $key => $val) {
                $datar[$key]=null;
            }
        }

       $data["field"] = $datar;
        $this->db->order_by("keselon","desc");
        $rows= $this->db->get("ref_eselon");

        foreach($rows->result() as $row)
        {
            $dataeselon[$row->neselon] = $row->neselon;
        }
       $data["lstEselon"] = $dataeselon;

        $rows1= $this->db->get("mastertunjangan");

        foreach($rows1->result() as $row)
        {
            $datakelas[$row->kelasjabatan] = $row->kelasjabatan.' ['.number_format($row->tunjangan,0,',','.').']';
        }
        $data["lstKelas"] = $datakelas;

        $rows2= $this->db->get("ref_golruang");

        foreach($rows2->result() as $row)
        {
            $datagol[$row->ngolru] = $row->ngolru.'  ['.$row->pangkat.']';
        }
        $data["lstGol"] = $datagol;

       $this->template->load('template','form',$data);
    }

    public function save()
    {
        $idx = is_numeric($this->input->post('iddata'))? intval($this->input->post('iddata')):0;
        $dataIn['tunjanganprofesi'] = $this->input->post('tunjanganprofesi');
        $dataIn['tmtprofesi'] =  dmyToymd($this->input->post('tmtprofesi'));
        /*$dataIn['plt_deptid'] =  $this->input->post('plt_deptid');*/
        /*$dataIn['plt_sk'] =  $this->input->post('plt_sk');
        $dataIn['plt_kelasjabatan'] =  $this->input->post('plt_kelasjabatan');*/
        $dataIn['no_rekening'] =  $this->input->post('no_rekening');
        $dataIn['payable'] =  $this->input->post('payable');
        $dataIn['jftdate'] =  dmyToymd($this->input->post('jftdate'));
        /*$dataIn['plt_jbtn'] =  $this->input->post('plt_jbtn');*/
        /*$dataIn['npwp'] =  $this->input->post('npwp');*/
        /*$dataIn['tmt_plt'] =  dmyToymd($this->input->post('tmt_plt'));*/
        /*$dataIn['eselon'] =  $this->input->post('eselon');*/
        /*$dataIn['kelasjabatan'] =  $this->input->post('kelasjabatan');*/
        /*$dataIn['plt_eselon'] =  $this->input->post('plt_eselon');*/
        /*$dataIn['kedudukan'] =  $this->input->post('kedudukan');*/
        $dataIn['tmtkedudukan'] =  dmyToymd($this->input->post('tmtkedudukan'));

        $tunjprof = $this->input->post('tunjanganprofesi');
        $tunjprofdate = $this->input->post('tmtprofesi');

        $kedudukandate =  dmyToymd($this->input->post('tmtkedudukan'));
        $jenispegawaidate = dmyToymd($this->input->post('jftdate'));

        if ($idx>0)
        {
            $this->db->where('id', $idx);
            $dataawal=$this->db->get("userinfo")->row_array();
            log_history("edit","userinfo",$dataawal);

            $userid = $dataawal["userid"];

            $dataIn['modify_by'] =  $this->session->userdata('s_username');
            $dataIn['modif_date'] =  date('Y-m-d H:i:s');
            $this->db->where('id', $idx);
            $update = $this->db->update('userinfo',$dataIn);
            //echo $this->db->last_query();
            if(!$update){

                createLog("Merubah Pegawai ".$dataawal["userid"],"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                //tunjangan profesi
                if ($tunjprof != null && $tunjprofdate != null) {
                    $q = $this->db->select('userid')
                        ->from('tunjanganprofhistory')
                        ->where(array('userid' => $userid, 'tunjprofdate' => $tunjprofdate))->get();
                    if ($q->num_rows() == 0) {


                        $history = array(
                            'userid' => $userid,
                            'tunjprofdate' => $tunjprofdate,
                            'tunjanganprofesi' => $tunjprof != '' ? $tunjprof : null
                        );
                        $this->db->insert('tunjanganprofhistory', $history);
                    } else {
                        $this->db->delete("tunjanganprofhistory",
                                array('userid' => $userid,
                                    'tunjprofdate' => $tunjprofdate));

                        $history = array(
                            'userid' => $userid,
                            'tunjprofdate' => $tunjprofdate,
                            'tunjanganprofesi' => $tunjprof != '' ? $tunjprof : null
                        );
                        $this->db->insert('tunjanganprofhistory', $history);
                    }
                }

                if ($tunjprof == null && $tunjprofdate != null) {
                    $q = $this->db->select('userid')
                        ->from('tunjanganprofhistory')
                        ->where(array('userid' => $userid,
                            'tunjprofdate' => $tunjprofdate))->get();
                    if ($q->num_rows() == 0) {
                        $history = array(
                            'userid' => $userid,
                            'tunjprofdate' => $tunjprofdate,
                            'tunjanganprofesi' => 0
                        );
                        $this->db->insert('tunjanganprofhistory', $history);
                    } else {
                        $this->db->delete("tunjanganprofhistory",
                            array('userid' => $userid, 'tunjprofdate' => $tunjprofdate));

                        $history = array(
                            'userid' => $userid,
                            'tunjprofdate' => $tunjprofdate,
                            'tunjanganprofesi' => 0
                        );
                        $this->db->insert('tunjanganprofhistory', $history);
                    }
                }

                /*if ($jenispegawaidate != null) {
                    $history = array(
                        'userid' => $userid,
                        'jenis' => 2,
                        'tanggal' => $jenispegawaidate,
                        'value' => $jenispegawai != '' ? $jenispegawai : null
                    );
                    $historyup = array(
                        'tanggal' => $jenispegawaidate,
                        'value' => $jenispegawai != '' ? $jenispegawai : null
                    );

                    $this->db->where('userid', $userid);
                    $this->db->where('jenis', 2);
                    $this->db->where('tanggal', $jenispegawaidate);
                    $this->db->where('value', $jenispegawai);
                    $query = $this->db->get("jenispegawaihistory");
                    if ($query->num_rows() == 0) {
                        $this->db->insert('jenispegawaihistory', $history);
                    }
                }

                if ($kedudukandate != null) {
                    $history = array(
                        'userid' => $userid,
                        'jenis' => 3,
                        'tanggal' => $kedudukandate,
                        'value' => $kedudukan != '' ? $kedudukan : null
                    );
                    $historyup = array(
                        'tanggal' => $jenispegawaidate,
                        'value' => $kedudukan != '' ? $kedudukan : null
                    );


                    $this->db->where('userid', $userid);
                    $this->db->where('jenis', 3);
                    $this->db->where('tanggal', $kedudukandate);
                    $this->db->where('value', $kedudukan);
                    $query = $this->db->get("jenispegawaihistory");
                    if ($query->num_rows() == 0) {
                        $this->db->insert('jenispegawaihistory', $history);
                    }
                }*/



                createLog("Merubah Pegawai ".$dataawal["userid"],"Sukses");
                $data['msg'] = 'Data berhasil dirubah..';
                $data['status'] = 'succes';
            }
        }
        die(json_encode($data));
    }

    public  function delfp()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');

        $uid="";
        for ($i = 0; $i < count($id); $i++) {
            $nId = is_numeric($id[$i]) ? $id[$i] : 0;

            $this->db->where('id', $nId);
            $query = $this->db->get("userinfo");
            $datas = $query->row_array();
            $uid .= $datas["userid"];
            /*
            $this->db->where_in('userid', $datas["userid"]);
            if ($this->db->delete('template'))
            {*/

            $userarea = $this->pegawai->getareauserinfo($datas["userid"]);
            foreach ($userarea->result() as $usea) {
                //$serialno = $this->pegawai->getsnarea($usea->areaid);
                $serialno = $this->pegawai->getsnareaaktif($usea->areaid);
                foreach ($serialno->result() as $serno) {
                    $comdev = array(
                        'sn' => $serno->sn,
                        'cmd' => 'DATA DEL_FP PIN='.$datas["userid"],
                        'status' => 1,
                        'submittime' => date("Y-m-d H:i:s")
                    );
                    $this->db->insert('command', $comdev);
                    //$woowdb->insert('command', $comdev);
                }
            }

            if (isset($datas)) {
                createLog("Menghapus fingerprint " . $datas["userid"], "Sukses");
            }

            /*} else {
                if (isset($datas)) {
                    createLog("Menghapus fingerprint " . $datas["userid"], "Error");
                }
            }*/
        }

        $data['msg'] = 'Perintah penghapusan FP Pegawai sudah dilakukan..';
        $data['status'] = 'succes';

        $this->output->set_output( json_encode($data));
    }

    public  function rubaharea()
    {
        //$woowdb = $this->load->database('woow', TRUE);

        //$id = explode(',', $this->input->post('idarea'));
        if (strpos($this->input->post('idarea'), ',') !== false) {
            $id = explode(',', $this->input->post('idarea'));
        } else
        {
            $id = array($this->input->post('idarea'));
        }

        //$areaid = explode(',', $this->input->post('area'));
        if (strpos($this->input->post('area'), ',') !== false) {
            $areaid = explode(',', $this->input->post('area'));
        } else
        {
            $areaid = array($this->input->post('area'));
        }


        for ($i = 0; $i < count($id); $i++) {
            $nId = is_numeric($id[$i]) ? $id[$i] : 0;

            $this->db->where('id', $nId);
            $query = $this->db->get("userinfo");
            $datas = $query->row_array();

            $usertable=$this->pegawai->get_user($datas["userid"]);

            $userareaactive = array();
            $userarea=$this->pegawai->getareauserinfo($datas["userid"]);
            foreach($userarea->result() as $usea) {
                $userareaactive[]=$usea->areaid;
            }

            $thr=array_intersect($userareaactive, $areaid);
            $del=array_diff($userareaactive, $areaid);
            $add=array_diff($areaid, $userareaactive);

            foreach($thr as $thre) {
                $serialno=$this->pegawai->getsnarea($thre);
                //$serialno=$this->pegawai->getsnareaaktif($thre);
                if(isset($serialno)) {
                    foreach($serialno->result() as $serno) {
                            $comdev= array (
                                'sn'			=>$serno->sn,
                                //'cmd'			=>'DATA USER PIN='.$datas["userid"].'	Name='.$usertable->row()->nickname.'	Passwd='.$usertable->row()->password.'	Card='.$usertable->row()->card.'	Grp='.$usertable->row()->accgroup.'	TZ='.$usertable->row()->timezones.'	Pri='.$usertable->row()->privilege,
                                'cmd'			=>'DATA USER PIN='.$datas["userid"].' Name='.$usertable->row()->nickname.'	Passwd='.$usertable->row()->password.'	Card=	Grp=	TZ=	Pri='.$usertable->row()->privilege,
                                'status'		=>1,
                                'submittime'	=>date("Y-m-d H:i:s")
                            );
                            $this->db->insert('command', $comdev);
                            //$woowdb->insert('command', $comdev);
                        //}
                    }
                }
            }

            sleep(40);

            foreach($del as $dele) {
                $serialno=$this->pegawai->getsnarea($dele);
                //$serialno=$this->pegawai->getsnareaaktif($dele);
                if(isset($serialno)) {
                    foreach($serialno->result() as $serno) {
                        $comdev= array (
                            'sn'			=>$serno->sn,
                            'cmd'			=>'DATA DEL_USER PIN='.$datas["userid"],
                            //'cmd' => 'DATA DEL_FP PIN='.$datas["userid"],
                            'status'		=>1,
                            'submittime'	=>date("Y-m-d H:i:s")
                        );
                        $this->db->insert('command', $comdev);
                        //$woowdb->insert('command', $comdev);
                    }
                }

                //log_history("change-area", "userinfo_attarea", $datas);

                $this->db->delete('userinfo_attarea', array('userid'=>$datas["userid"], 'areaid'=>$dele));
                //$woowdb->delete('userinfo_attarea', array('userid'=>$datas["userid"], 'areaid'=>$dele));
            }

            sleep(40);

            //print_r($add);
            foreach($add as $adda) {
               // $usertablearea = $this->pegawai->get_user($datas["userid"]);
                $fingertab = $this->pegawai->get_fp($datas["userid"]);
                $serialno = $this->pegawai->getsnarea($adda);
                //$serialno = $this->pegawai->getsnareaaktif($adda);
                if (isset($serialno)) {
                    foreach ($serialno->result() as $serno) {
                            $comdev = array(
                                'sn' => $serno->sn,
                                //'cmd' => 'DATA USER PIN=' . $datas["userid"] . '    Name=' . $usertablearea->row()->nickname . '    Passwd=' . $usertablearea->row()->password . '  Card=' . $usertablearea->row()->card . '    Grp=' . $usertablearea->row()->accgroup . ' TZ=' . $usertablearea->row()->timezones . ' Pri=' . $usertablearea->row()->privilege,
                                'cmd'			=>'DATA USER PIN='.$datas["userid"].' Name='.$usertable->row()->nickname.'	Passwd='.$usertable->row()->password.'	Card=	Grp=	TZ=	Pri='.$usertable->row()->privilege,
                                'status' => 1,
                                'submittime' => date("Y-m-d H:i:s",time() + 30)
                            );
                            //$woowdb->insert('command', $comdev);
                            $this->db->insert('command', $comdev);

                            foreach ($fingertab->result() as $quedfp) {
                                $comdev = array(
                                    'sn' => $serno->sn,
                                    //'cmd' => 'DATA FP PIN=' . $datas["userid"] . '  FID=' . $quedfp->fingerid . '   Valid=' . $quedfp->valid . '    TMP=' . $quedfp->template,
                                    'cmd'			=>'DATA FP PIN='.$datas["userid"].'	FID='.$quedfp->fingerid.'	Valid='.$quedfp->valid.'	TMP='.$quedfp->template,
                                    'status' => 1,
                                    'submittime' => date("Y-m-d H:i:s",time() + 30)
                                );

                                //$woowdb->insert('command', $comdev);
                                $this->db->insert('command', $comdev);
                            }
                    }
                }
                $insertuser = array(
                    'userid' => $datas["userid"],
                    'areaid' => $adda
                );
                //$woowdb->insert('userinfo_attarea', $insertuser);
                $this->db->insert('userinfo_attarea', $insertuser);

            }
        }

        $data['msg'] = 'Area Mesin untuk Pegawai sudah dirubah..';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
    }

    public function synchronizing()
    {
            $userid = $_POST['id'];
            $jmldata = count($userid);
            //$woowdb = $this->load->database('woow', TRUE);
            for($i=0;$i<$jmldata;$i++) {
                $this->db->where('id', $userid[$i]);
                $query = $this->db->get("userinfo");
                $datas = $query->row_array();

                $userarea=$this->pegawai->getareauserinfo($datas["userid"]);
                foreach($userarea->result() as $usea) {
                    $serialno=$this->pegawai->getsnarea($usea->areaid);
                    //$serialno=$this->pegawai->getsnareaaktif($usea->areaid);
                    $usertable=$this->pegawai->get_user($datas["userid"]);
                    $fingertab=$this->pegawai->get_fp($datas["userid"]);
                    foreach($serialno->result() as $serno) {
                            $comdev= array (
                                'sn'			=>$serno->sn,
                                //'cmd'			=>'DATA USER PIN='.$datas["userid"].'	Name='.$usertable->row()->nickname.'	Passwd='.$usertable->row()->password.'	Card='.$usertable->row()->card.'	Grp='.$usertable->row()->accgroup.'	TZ='.$usertable->row()->timezones.'	Pri='.$usertable->row()->privilege,
                                'cmd'			=>'DATA USER PIN='.$datas["userid"].'	Name='.$usertable->row()->nickname.'	Passwd='.$usertable->password.'	Card=	Grp=	TZ=	Pri='.$usertable->row()->privilege,
                                'status'		=>1,
                                'submittime'	=>date("Y-m-d H:i:s")
                            );

                            $this->db->insert('command', $comdev);
                            createLog("Memasukan data ".$datas["userid"]." ke SN " . $serno->sn , "Sukses");

                            //$woowdb->insert('command', $comdev);

                            foreach($fingertab->result() as $quedfp) {
                                $comdev = array (
                                    'sn' 			=>$serno->sn,
                                    //'cmd'			=>'DATA FP PIN='.$datas["userid"].' FID='.$quedfp->fingerid.'   Valid='.$quedfp->valid.'	TMP='.$quedfp->template,
                                    'cmd'			=>'DATA FP PIN='.$datas["userid"].'	FID='.$quedfp->fingerid.'	Valid='.$quedfp->valid.'	TMP='.$quedfp->template,
                                    'status'		=>1,
                                    'submittime'	=>date("Y-m-d H:i:s")
                                );
                                $this->db->insert('command', $comdev);
                                createLog("Memasukan FP ".$datas["userid"]." ke SN " . $serno->sn , "Sukses");

                                //$woowdb->insert('command', $comdev);
                            }
                    }
                }
            }

        $data['msg'] = 'Data pegawai sudah dimasukan..';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));

    }

    public function gethistori()
    {
        $data['aksesrule']=$this->aAkses;
        $idg = $this->input->post('id');
        $data['offset'] = 0;
        $data['limit_display'] = 20;
        $query = $this->db->where("userid",$idg)->get("userinfohistory");
        $data['result'] = $query->result();
        $data['jum_data'] = $query->num_rows();

        $query = $this->db->where("userid",$idg)->get("tunjanganprofhistory");
        $data['result2'] = $query->result();
        $data['jum_data2'] = $query->num_rows();

        $this->db->where("userid",$idg);
        $this->db->where('jenis', 3);
        $query = $this->db->get("jenispegawaihistory");
        $data['result3'] = $query->result();
        $data['jum_data3'] = $query->num_rows();

        $this->db->where("userid",$idg);
        $this->db->where('jenis', 2);
        $query = $this->db->get("jenispegawaihistory");
        $data['result4'] = $query->result();
        $data['jum_data4'] = $query->num_rows();

        $this->db->where("userid",$idg);
        $this->db->where('jenis', 1);
        $query = $this->db->get("jenispegawaihistory");
        $data['result5'] = $query->result();
        $data['jum_data5'] = $query->num_rows();

        $this->load->view('listhistori',$data);
    }

    public function copyfpkemesin()
    {
        if (strpos($this->input->post('mesinid'), ',') !== false) {
            $mesinid = explode(',', $this->input->post('mesinid'));
        } else
        {
            $mesinid = array($this->input->post('mesinid'));
        }

        if (strpos($this->input->post('useid'), ',') !== false) {
            $userid = explode(',', $this->input->post('useid'));
        } else
        {
            $userid = array($this->input->post('useid'));
        }

        //$woowdb = $this->load->database('woow', TRUE);
        for ($i = 0; $i < count($mesinid); $i++) {
            $nId = $mesinid[$i];

            for ($ii = 0; $ii < count($userid); $ii++) {
                $this->db->where('id', $userid[$ii]);
                $query = $this->db->get("userinfo")->row_array();
                if ($query) {
                    $uId = $query["userid"];

                    $usertable = $this->pegawai->get_user($uId)->row();

                    /*$comdevs= array (
                        'sn'			=>$nId,
                        'cmd'			=>'DATA DEL_USER PIN='.$uId,
                        'status'		=>1,
                        'submittime'	=>date("Y-m-d H:i:s")
                    );
                    //$woowdb->insert('command', $comdevs);
                    $this->db->insert('command', $comdevs);

                    sleep(30);*/

                    $comdev = array(
                        'sn' => $nId,
                        'cmd'			=>'DATA USER PIN='.$uId.'	Name='.$usertable->name.'	Passwd='.$usertable->password.'	Card=	Grp=	TZ=	Pri='.$usertable->privilege,
                        'status' => 1,
                        'submittime' => date("Y-m-d H:i:s")
                    );

                    //$woowdb->insert('command', $comdev);
                    $this->db->insert('command', $comdev);

                    sleep(40);

                    $fingertab = $this->pegawai->get_fp($uId);
                    foreach ($fingertab->result() as $quedfp) {
                        $comdevt = array(
                            'sn' => $nId,
                            'cmd'			=>'DATA FP PIN='.$uId.'	FID='.$quedfp->fingerid.'	Valid='.$quedfp->valid.'	TMP='.$quedfp->template,
                            'status' => 1,
                            'submittime' => date("Y-m-d H:i:s")
                        );
                        //$woowdb->insert('command', $comdevt);
                        $this->db->insert('command', $comdevt);
                    }
                }
            }
        }

       $data['msg'] = 'FP Pegawai sudah diduplikasi..';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));

    }

    public  function hapuskelas()
    {
        $id = $this->input->post('id');

        $this->db->where('id',$id);
        $this->db->delete('userinfohistory');

        $data['msg'] = 'Kelas jabatan pegawai sudah dihapus..';
        $data['status'] = 'succes';

        $this->output->set_output( json_encode($data));
    }

    public  function rowprof()
    {
        $id = $this->input->post('id');

        $this->db->where('id',$id);
        $this->db->delete('tunjanganprofhistory');

        $data['msg'] = 'Tunjangan profesi pegawai sudah dihapus..';
        $data['status'] = 'succes';

        $this->output->set_output( json_encode($data));
    }

    public function sinkrondata()
    {
        ini_set('MAX_EXECUTION_TIME', '-1');
        ini_set('memory_limit', '-1');

        $npil = $this->input->post('pil');
        $cr = $this->input->post('id');

        $lstdata = getContentUrl($this->config->item('serversinkronsimpeg'));

        if ($lstdata) {
            if (substr($lstdata,0,5)==='ERROR') {
                $data['msg'] = $lstdata.'<br>Ada kesalahan pengambilan data dari SIMPEG';
                $data['status'] = 'succes';
            } else {
                $namafile = 'assets/tmpsimpeg/data_' . date("Ymd-His") . '.json';
                $fp = fopen($namafile, 'w');
                if (flock($fp, LOCK_EX)) { // do an exclusive lock
                    ftruncate($fp, 0);
                    fwrite($fp, $lstdata);
                    flock($fp, LOCK_UN); // release the lock
                }
                fclose($fp);


                $aPeg = array();

                if ($npil==0) {
                    if ($cr !== 'All') {
                        $this->db->select('userid');
                        $this->db->where_in('id', $cr);
                        $aPeg = $this->db->get('userinfo')->result();
                        foreach ($aPeg as $rPeg) {
                            array_push($aPeg, $rPeg->userid);
                        }
                    }
                } else {
                    $this->db->select('userid');
                    $aPeg = $this->db->get('userinfo')->result();
                    foreach ($aPeg as $rPeg) {
                        array_push($aPeg, $rPeg->userid);
                    }
                }

                $namafileJson = FCPATH . $namafile;
                $adaJson = file_exists($namafileJson);

                if ($adaJson) {
                    $str = file_get_contents($namafileJson);
                    $aListPeg = json_decode($str, true);

                    if ($npil==0) {
                        if ($cr != 'All') {
                            $nIdx = 0;
                            foreach ($aListPeg as $itemdata) {
                                if (!in_array($itemdata["nip"], $aPeg)) {
                                    unset($aListPeg[$nIdx]);
                                }
                                $nIdx++;
                            }
                        }

                        foreach ($aListPeg as $itemdata) {
                            $dataup = null;
                            $golru = "";
                            $this->db->select('ngolru');
                            $this->db->from('ref_golruang');
                            $this->db->where('kgolru', $itemdata["kgolru"]);
                            $queryf = $this->db->get();
                            if ($queryf->num_rows() > 0) {
                                $golru = $queryf->row()->ngolru;
                            }

                            $dataup["name"] = $itemdata["namapeg"];
                            $dataup["title"] = $itemdata["njab_definitif"];
                            $dataup["placebirthdate"] = $itemdata["tempat_lahir"];
                            $dataup["nickname"] = $itemdata["nama_panggilan"];
                            $dataup["gender"] = $itemdata["jenis_kelamain"];

                            $arrproc = array("1", "2", "3", "4", "5", "6", "7", "8");
                            $allowproc = in_array($itemdata["agama"], $arrproc);
                            if ($allowproc) {
                                $dataup["religion"] = $this->utils->getKdAgama()[$itemdata["agama"]];
                            }
                            $dataup["birthdate"] = $itemdata["tgl_lahir"];
                            $dataup["golru"] = $golru;
                            $dataup["kelasjabatan"] = $itemdata["peringkat_definitif"];

                            $dataup["jftstatus"] = $itemdata["status_pegawai"];
                            $dataup["jenispegawai"] = $itemdata["jenis_pegawai"];
                            $dataup["kedudukan"] = $itemdata["kduduk"];
                            $dataup["tmtpangkat"] = $itemdata["tmtpang"];
                            $dataup["tmtjabatan"] = $itemdata["tmtjab"];
                            $dataup["npwp"] = $itemdata["npwp"];
                            //history
                            $tmtkelas = $itemdata["tmtjab"];
                            $tmtjabatan = $itemdata["tmtjab"];
                            $kelas = $itemdata["peringkat_definitif"];
                            $jftstatus = $itemdata["status_pegawai"];
                            $jftstatusdate = $itemdata["tmtjab"];
                            $jenispegawaidate = $itemdata["tmtpang"];
                            $jenispegawai = $itemdata["jenis_pegawai"];
                            $kedudukandate = $itemdata["tmtpang"];
                            $kedudukan = $itemdata["kduduk"];

                            //end history

                            $kdUnker = substr($itemdata["kunker_definitif"], 2);
                            $this->db->select('deptid');
                            $this->db->from('departments');
                            $this->db->where('deptid', $kdUnker);
                            $queryf = $this->db->get();
                            if ($queryf->num_rows() > 0) {
                                $dataup["deptid"] = $kdUnker;
                            }

                            $this->db->select('neselon');
                            $this->db->from('ref_eselon');
                            $this->db->where('keselon', $itemdata["keselon"]);
                            $queryf = $this->db->get();
                            if ($queryf->num_rows() > 0) {
                                $dataup["eselon"] = $queryf->row()->neselon;
                            } else {
                                $dataup["eselon"] = "";
                            }


                            if ($itemdata["status_plt"] == "1") { //jika status PLT
                                $dataup["plt_jbtn"] = $itemdata["njab_plt"];
                                $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plt"];
                                $dataup["plt_sk"] = $itemdata["sk_plt"];
                                $dataup["tmt_plt"] = $itemdata["tmtjab_plt"];

                                $kdUnker = substr($itemdata["kunker_plt"], 2);

                                $this->db->select('deptid');
                                $this->db->from('departments');
                                $this->db->where('deptid', empty($itemdata["kunker_plt"]) ? "XX" : $kdUnker);
                                $queryf = $this->db->get();
                                if ($queryf->num_rows() > 0) {
                                    $dataup["plt_deptid"] = empty($itemdata["kunker_plt"]) ? "" : $kdUnker;
                                }

                                $this->db->select('neselon');
                                $this->db->from('ref_eselon');
                                $this->db->where('keselon', $itemdata["keselon_plt"]);
                                $queryf = $this->db->get();
                                if ($queryf->num_rows() > 0) {
                                    $dataup["plt_eselon"] = $queryf->row()->neselon;
                                } else {
                                    $dataup["plt_eselon"] = "";
                                }
                            } else {

                                if ($itemdata["status_plh"] == "1") { //Jika status PLH
                                    $dataup["plt_jbtn"] = $itemdata["njab_plh"];
                                    $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plh"];
                                    $dataup["plt_sk"] = $itemdata["sk_plh"];
                                    $dataup["tmt_plt"] = $itemdata["tmtjab_plh"];

                                    $kdUnker = substr($itemdata["kunker_plh"], 2);
                                    $this->db->select('deptid');
                                    $this->db->from('departments');
                                    $this->db->where('deptid', empty($itemdata["kunker_plh"]) ? "XX" : $kdUnker);
                                    $queryf = $this->db->get();
                                    if ($queryf->num_rows() > 0) {
                                        $dataup["plt_deptid"] = empty($itemdata["kunker_plh"]) ? "" : $kdUnker;
                                    }
                                    $this->db->select('neselon');
                                    $this->db->from('ref_eselon');
                                    $this->db->where('keselon', $itemdata["keselon_plh"]);
                                    $queryf = $this->db->get();
                                    if ($queryf->num_rows() > 0) {
                                        $dataup["plt_eselon"] = $queryf->row()->neselon;
                                    } else {
                                        $dataup["plt_eselon"] = "";
                                    }
                                } else {
                                    //jika status PLT & PLH tidak aktif
                                    $dataup["plt_eselon"] = "";
                                    $dataup["plt_deptid"] = "";
                                    $dataup["plt_jbtn"] = "";
                                    $dataup["plt_kelasjabatan"] = null;
                                    $dataup["plt_sk"] = null;
                                    $dataup["tmt_plt"] = null;
                                }

                            }

                            $this->db->from('userinfo');
                            $this->db->where('userid', trim($itemdata["nip"]));
                            $query = $this->db->get();
                            if ($query->num_rows() > 0) { //update
                                $datas = $query->row_array();
                                log_history("update-simpeg", "userinfo", $datas);

								//log unit kerja per pegawai
                                $this->db->from('departments');
                                $this->db->where('deptid', $datas['deptid']);
                                $queryd = $this->db->get();
                                if ($queryd->num_rows() > 0) {
                                    $datasx = $queryd->row_array();
                                    $dataunker = array(
										'userid'=>trim($itemdata["nip"]),
                                        'unitkerjadate' => date('Y-m-d H:i:s'),
                                        'unitkerjakode' => $datas['deptid'],
                                        'unitkerjanama' => $datasx['deptname'],
                                        'unitkerjaparent' => $datasx['parentid'],
                                        'create_by'=>$this->session->userdata('s_username')
                                    );
                                    $this->db->insert("unitkerjafhistory", $dataunker);
                                }
								
                                $this->db->where('userid', $itemdata["nip"]);
                                $this->db->update("userinfo", $dataup);

                                $this->db->reset_query();

                            } else { //insert
                                $dataup["userid"] = trim($itemdata["nip"]);
                                $dataup["badgenumber"] = $itemdata["nip"];
                                $dataup["timezones"] = "0000000000000000";
                                $dataup["accgroup"] = "1";

                                $this->db->insert("userinfo", $dataup);
                                $this->db->reset_query();

                            }

                            //start history
                            if ($tmtkelas != null) {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'tmtjabatan' => $tmtkelas,
                                    'kelas' => $kelas != '' ? $kelas : 0
                                );
                            } else {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'tmtjabatan' => $tmtjabatan,
                                    'kelas' => $kelas != '' ? $kelas : 0
                                );
                            }
                            $historyup = array(
                                'tmtjabatan' => $tmtkelas,
                                'kelas' => $kelas != '' ? $kelas : 0
                            );


                            if (!$this->db->insert('userinfohistory', $history)) {
                                $this->db->where('userid', trim($itemdata["nip"]));
                                $this->db->update('userinfohistory', $historyup);
                            }
                            $this->db->reset_query();

                            if ($jftstatusdate != null) {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'jenis' => 1,
                                    'tanggal' => $jftstatusdate,
                                    'value' => $jftstatus != '' ? $jftstatus : null
                                );

                                $this->db->where('userid', trim($itemdata["nip"]));
                                $this->db->where('jenis', 1);
                                $this->db->where('tanggal', $jftstatusdate);
                                $this->db->where('value', $jftstatus);
                                $query = $this->db->get("jenispegawaihistory");
                                if ($query->num_rows() == 0) {
                                    $this->db->insert('jenispegawaihistory', $history);
                                    $this->db->reset_query();
                                }
                            }

                            if ($jenispegawaidate != null) {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'jenis' => 2,
                                    'tanggal' => $jenispegawaidate,
                                    'value' => $jenispegawai != '' ? $jenispegawai : null
                                );


                                $this->db->where('userid', trim($itemdata["nip"]));
                                $this->db->where('jenis', 2);
                                $this->db->where('tanggal', $jenispegawaidate);
                                $this->db->where('value', $jenispegawai);
                                $query = $this->db->get("jenispegawaihistory");
                                if ($query->num_rows() == 0) {
                                    $this->db->insert('jenispegawaihistory', $history);
                                    $this->db->reset_query();
                                }
                            }

                            if ($kedudukandate != null) {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'jenis' => 3,
                                    'tanggal' => $kedudukandate,
                                    'value' => $kedudukan != '' ? $kedudukan : null
                                );


                                $this->db->where('userid', trim($itemdata["nip"]));
                                $this->db->where('jenis', 3);
                                $this->db->where('tanggal', $kedudukandate);
                                $this->db->where('value', $kedudukan);
                                $query = $this->db->get("jenispegawaihistory");
                                if ($query->num_rows() == 0) {
                                    $this->db->insert('jenispegawaihistory', $history);
                                    $this->db->reset_query();
                                }
                            }
                            //end history

                        }
                    } else {
                        $nIdx = 0;
                        foreach ($aListPeg as $itemdata) {
                            if (in_array($itemdata["nip"], $aPeg)) {
                                unset($aListPeg[$nIdx]);
                            }
                            $nIdx++;
                        }

                        foreach ($aListPeg as $itemdata) {
                            $dataup = null;
                            $golru = "";
                            $this->db->select('ngolru');
                            $this->db->from('ref_golruang');
                            $this->db->where('kgolru', $itemdata["kgolru"]);
                            $queryf = $this->db->get();
                            if ($queryf->num_rows() > 0) {
                                $golru = $queryf->row()->ngolru;
                            }

                            $dataup["name"] = $itemdata["namapeg"];
                            $dataup["title"] = $itemdata["njab_definitif"];
                            $dataup["placebirthdate"] = $itemdata["tempat_lahir"];
                            $dataup["nickname"] = $itemdata["nama_panggilan"];
                            $dataup["gender"] = $itemdata["jenis_kelamain"];

                            $arrproc = array("1", "2", "3", "4", "5", "6", "7", "8");
                            $allowproc = in_array($itemdata["agama"], $arrproc);
                            if ($allowproc) {
                                $dataup["religion"] = $this->utils->getKdAgama()[$itemdata["agama"]];
                            }
                            $dataup["birthdate"] = $itemdata["tgl_lahir"];
                            $dataup["golru"] = $golru;
                            $dataup["kelasjabatan"] = $itemdata["peringkat_definitif"];

                            $dataup["jftstatus"] = $itemdata["status_pegawai"];
                            $dataup["jenispegawai"] = $itemdata["jenis_pegawai"];
                            $dataup["kedudukan"] = $itemdata["kduduk"];
                            $dataup["tmtpangkat"] = $itemdata["tmtpang"];
                            $dataup["tmtjabatan"] = $itemdata["tmtjab"];
                            $dataup["npwp"] = $itemdata["npwp"];
                            //history
                            $tmtkelas = $itemdata["tmtjab"];
                            $tmtjabatan = $itemdata["tmtjab"];
                            $kelas = $itemdata["peringkat_definitif"];
                            $jftstatus = $itemdata["status_pegawai"];
                            $jftstatusdate = $itemdata["tmtjab"];
                            $jenispegawaidate = $itemdata["tmtpang"];
                            $jenispegawai = $itemdata["jenis_pegawai"];
                            $kedudukandate = $itemdata["tmtpang"];
                            $kedudukan = $itemdata["kduduk"];

                            //end history

                            $kdUnker = substr($itemdata["kunker_definitif"], 2);
                            $this->db->select('deptid');
                            $this->db->from('departments');
                            $this->db->where('deptid', $kdUnker);
                            $queryf = $this->db->get();
                            if ($queryf->num_rows() > 0) {
                                $dataup["deptid"] = $kdUnker;
                            }

                            $this->db->select('neselon');
                            $this->db->from('ref_eselon');
                            $this->db->where('keselon', $itemdata["keselon"]);
                            $queryf = $this->db->get();
                            if ($queryf->num_rows() > 0) {
                                $dataup["eselon"] = $queryf->row()->neselon;
                            } else {
                                $dataup["eselon"] = "";
                            }


                            if ($itemdata["status_plt"] == "1") { //jika status PLT
                                $dataup["plt_jbtn"] = $itemdata["njab_plt"];
                                $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plt"];
                                $dataup["plt_sk"] = $itemdata["sk_plt"];
                                $dataup["tmt_plt"] = $itemdata["tmtjab_plt"];

                                $kdUnker = substr($itemdata["kunker_plt"], 2);

                                $this->db->select('deptid');
                                $this->db->from('departments');
                                $this->db->where('deptid', empty($itemdata["kunker_plt"]) ? "XX" : $kdUnker);
                                $queryf = $this->db->get();
                                if ($queryf->num_rows() > 0) {
                                    $dataup["plt_deptid"] = empty($itemdata["kunker_plt"]) ? "" : $kdUnker;
                                }

                                $this->db->select('neselon');
                                $this->db->from('ref_eselon');
                                $this->db->where('keselon', $itemdata["keselon_plt"]);
                                $queryf = $this->db->get();
                                if ($queryf->num_rows() > 0) {
                                    $dataup["plt_eselon"] = $queryf->row()->neselon;
                                } else {
                                    $dataup["plt_eselon"] = "";
                                }
                            } else {

                                if ($itemdata["status_plh"] == "1") { //Jika status PLH
                                    $dataup["plt_jbtn"] = $itemdata["njab_plh"];
                                    $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plh"];
                                    $dataup["plt_sk"] = $itemdata["sk_plh"];
                                    $dataup["tmt_plt"] = $itemdata["tmtjab_plh"];

                                    $kdUnker = substr($itemdata["kunker_plh"], 2);
                                    $this->db->select('deptid');
                                    $this->db->from('departments');
                                    $this->db->where('deptid', empty($itemdata["kunker_plh"]) ? "XX" : $kdUnker);
                                    $queryf = $this->db->get();
                                    if ($queryf->num_rows() > 0) {
                                        $dataup["plt_deptid"] = empty($itemdata["kunker_plh"]) ? "" : $kdUnker;
                                    }
                                    $this->db->select('neselon');
                                    $this->db->from('ref_eselon');
                                    $this->db->where('keselon', $itemdata["keselon_plh"]);
                                    $queryf = $this->db->get();
                                    if ($queryf->num_rows() > 0) {
                                        $dataup["plt_eselon"] = $queryf->row()->neselon;
                                    } else {
                                        $dataup["plt_eselon"] = "";
                                    }
                                } else {
                                    //jika status PLT & PLH tidak aktif
                                    $dataup["plt_eselon"] = "";
                                    $dataup["plt_deptid"] = "";
                                    $dataup["plt_jbtn"] = "";
                                    $dataup["plt_kelasjabatan"] = null;
                                    $dataup["plt_sk"] = null;
                                    $dataup["tmt_plt"] = null;
                                }

                            }

                            $this->db->from('userinfo');
                            $this->db->where('userid', trim($itemdata["nip"]));
                            $query = $this->db->get();
                            if ($query->num_rows() == 0) { //insert
                                $dataup["userid"] = trim($itemdata["nip"]);
                                $dataup["badgenumber"] = $itemdata["nip"];
                                $dataup["timezones"] = "0000000000000000";
                                $dataup["accgroup"] = "1";
                                $this->db->insert("userinfo", $dataup);
                                $this->db->reset_query();
                            } else {
								$itemdatadd = $query->row_array();
                                //log unit kerja per pegawai
                                $this->db->from('departments');
                                $this->db->where('deptid', $itemdatadd['deptid']);
                                $queryd = $this->db->get();
                                if ($queryd->num_rows() > 0) {
                                    $datasx = $queryd->row_array();
                                    $dataunker = array(
										'userid'=>trim($itemdata["nip"]),
                                        'unitkerjadate' => date('Y-m-d H:i:s'),
                                        'unitkerjakode' => $itemdatadd['deptid'],
                                        'unitkerjanama' => $datasx['deptname'],
                                        'unitkerjaparent' => $datasx['parentid'],
                                        'create_by'=>$this->session->userdata('s_username')
                                    );
                                    $this->db->insert("unitkerjafhistory", $dataunker);
                                }
							}

                            //start history
                            if ($tmtkelas != null) {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'tmtjabatan' => $tmtkelas,
                                    'kelas' => $kelas != '' ? $kelas : 0
                                );
                            } else {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'tmtjabatan' => $tmtjabatan,
                                    'kelas' => $kelas != '' ? $kelas : 0
                                );
                            }
                            $historyup = array(
                                'tmtjabatan' => $tmtkelas,
                                'kelas' => $kelas != '' ? $kelas : 0
                            );


                            if (!$this->db->insert('userinfohistory', $history)) {
                                $this->db->where('userid', trim($itemdata["nip"]));
                                $this->db->update('userinfohistory', $historyup);
                            }
                            $this->db->reset_query();

                            if ($jftstatusdate != null) {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'jenis' => 1,
                                    'tanggal' => $jftstatusdate,
                                    'value' => $jftstatus != '' ? $jftstatus : null
                                );

                                $this->db->where('userid', trim($itemdata["nip"]));
                                $this->db->where('jenis', 1);
                                $this->db->where('tanggal', $jftstatusdate);
                                $this->db->where('value', $jftstatus);
                                $query = $this->db->get("jenispegawaihistory");
                                if ($query->num_rows() == 0) {
                                    $this->db->insert('jenispegawaihistory', $history);
                                    $this->db->reset_query();
                                }
                            }

                            if ($jenispegawaidate != null) {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'jenis' => 2,
                                    'tanggal' => $jenispegawaidate,
                                    'value' => $jenispegawai != '' ? $jenispegawai : null
                                );


                                $this->db->where('userid', trim($itemdata["nip"]));
                                $this->db->where('jenis', 2);
                                $this->db->where('tanggal', $jenispegawaidate);
                                $this->db->where('value', $jenispegawai);
                                $query = $this->db->get("jenispegawaihistory");
                                if ($query->num_rows() == 0) {
                                    $this->db->insert('jenispegawaihistory', $history);
                                    $this->db->reset_query();
                                }
                            }

                            if ($kedudukandate != null) {
                                $history = array(
                                    'userid' => trim($itemdata["nip"]),
                                    'jenis' => 3,
                                    'tanggal' => $kedudukandate,
                                    'value' => $kedudukan != '' ? $kedudukan : null
                                );


                                $this->db->where('userid', trim($itemdata["nip"]));
                                $this->db->where('jenis', 3);
                                $this->db->where('tanggal', $kedudukandate);
                                $this->db->where('value', $kedudukan);
                                $query = $this->db->get("jenispegawaihistory");
                                if ($query->num_rows() == 0) {
                                    $this->db->insert('jenispegawaihistory', $history);
                                    $this->db->reset_query();
                                }
                            }
                            //end history

                        }
                    }
                    unlink($namafileJson);
                    $data['msg'] = 'Data pegawai sudah disinkronisasikan..!!!<br>Silakan Refresh datanya atau klik tombol cari';
                    $data['status'] = 'succes';
                } else {
                    $data['msg'] = 'Data pegawai tidak bisa disinkronisasikan..!!!<br>Ada kesalahan penyimpanan data dari SIMPEG';
                    $data['status'] = 'succes';
                }
            }
        } else {
            $data['msg'] = 'Data pegawai tidak bisa disinkronisasikan..!!!<br>Ada kesalahan pengambilan data dari SIMPEG';
            $data['status'] = 'succes';
        }
        $this->output->set_output( json_encode($data));
    }

    public function Area($noId=null)
    {
        $data["id"]=$noId;
        $datar = $this->db->get_where('view_employee', array('id' => $noId))->row_array();
        if (count($datar)==0) {
            $data["id"]="000";

        } else{
            $data["id"]=$datar['userid'];
        }

        $ref = $this->utils->getareauser($data["id"])->result_array();
        $nmArea="";
        $listAreaUser=array();
        foreach ($ref as $item)
        {
            $nmArea .=$item["areaname"].",";
            array_push($listAreaUser,$item["areaid"]);
        }

        $data["areaOld"] = count($ref)>1? substr($nmArea,0, -1):$nmArea;

        $data["field"] = $datar;
        $data["listAreaUser"] = implode(',',$listAreaUser);
        $this->template->load('template','area',$data);
    }

    public function simpanarea()
    {
        $idUser=$this->input->post('iddata');
        $aArea=convertToArray($this->input->post('area'));

        $this->db->where('userid', $idUser);
        $query = $this->db->get("userinfo_attarea");
        if ($query->num_rows()>0) {
            $datas = $query->row_array();
            log_history("update-area", "userinfo_attarea", $datas);
        }

        $this->db->where('userid', $idUser);
        $this->db->delete("userinfo_attarea");
        $this->db->reset_query();

        foreach ($aArea as $item)
        {
            $comdev = array(
                'userid' =>$idUser,
                'areaid' => $item
            );
            $this->db->insert("userinfo_attarea",$comdev);
            $this->db->reset_query();
        }

        $data['msg'] = 'Area Mesin untuk pegawai sudah disimpan..';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
    }

    public function updateuid()
    {
        $newuserid=$this->input->post('newUID');
        $olduserid = $this->input->post('olduseid');

        $userarea=$this->pegawai->getareauserinfo($olduserid);
        $comdev = array();
        foreach($userarea->result() as $usea) {
            $serialno=$this->pegawai->getsnarea($usea->areaid);
            //$serialno=$this->pegawai->getsnareaaktif($usea->areaid);
            foreach($serialno->result() as $serno) {
                $comdev= array (
                    'sn'			=>$serno->sn,
                    'cmd'			=>'DATA DEL_USER PIN='.$olduserid,
                    'status'		=>1,
                    'submittime'	=>date("Y-m-d H:i:s")
                );
                $this->db->insert('command', $comdev);
            }
        }

        $this->db->where('id', $olduserid);
        $query = $this->db->get("userinfo");
        if ($query->num_rows()>0) {
            $datas = $query->row_array();
            log_history("update", "userinfo", $datas);
            $olduserid=$datas["userid"];
        } else {
            $olduserid=-1;
        }

        $databaru = array('userid'=>$newuserid);
        $this->db->update('userinfo', $databaru, array('userid'=>$olduserid));

        $this->db->where('userid', $olduserid);
        $query = $this->db->get("userinfo_attarea");
        if ($query->num_rows()>0) {
            $datas = $query->row_array();
            log_history("update", "userinfo_attarea", $datas);
        }

        $this->db->update('userinfo_attarea', $databaru, array('userid'=>$olduserid));

        $this->db->where('userid', $olduserid);
        $query = $this->db->get("checkinout");
        if ($query->num_rows()>0) {
            $datas = $query->row_array();
            log_history("update", "checkinout", $datas);
        }

        $this->db->update('checkinout', $databaru, array('userid'=>$olduserid));

        $this->db->where('userid', $olduserid);
        $query = $this->db->get("photostamp");
        if ($query->num_rows()>0) {
            $datas = $query->row_array();
            log_history("update", "photostamp", $datas);
        }

        $this->db->update('photostamp', $databaru, array('userid'=>$olduserid));

        $this->db->where('userid', $olduserid);
        $query = $this->db->get("template");
        if ($query->num_rows()>0) {
            $datas = $query->row_array();
            log_history("update", "template", $datas);
        }

        $this->db->update('template', $databaru, array('userid'=>$olduserid));

        $userarea=$this->pegawai->getareauserinfo($newuserid);
        $comdef = array();
        foreach($userarea->result() as $usea) {
            $serialno=$this->pegawai->getsnarea($usea->areaid);
            //$serialno=$this->pegawai->getsnareaaktif($usea->areaid);
            $usertable=$this->pegawai->get_user($newuserid);
            $fingertab=$this->pegawai->get_fp($newuserid);
            foreach($serialno->result() as $serno) {
                $comdef= array (
                    'sn'			=>$serno->sn,
                    'cmd'			=>'DATA USER PIN='.$newuserid.' Name='.$usertable->row()->name.'    Passwd='.$usertable->row()->password.'	Card='.$usertable->row()->card.'    Grp='.$usertable->row()->accgroup.' TZ='.$usertable->row()->timezones.' Pri='.$usertable->row()->privilege,
                    'status'		=>1,
                    'submittime'	=>date("Y-m-d H:i:s")
                );
                $this->db->insert('command', $comdef);

                foreach($fingertab->result() as $quedfp) {
                    $comdef = array (
                        'sn' 			=>$serno->sn,
                        'cmd'			=>'DATA FP PIN='.$newuserid.'   FID='.$quedfp->fingerid.'	Valid='.$quedfp->valid.'	TMP='.$quedfp->template,
                        'status'		=>1,
                        'submittime'	=>date("Y-m-d H:i:s")
                    );
                    $this->db->insert('command', $comdef);
                }
            }
        }

        createLog('Change userid '.$olduserid.' to '.$newuserid,'Sukses');

        $hasil = array("status" => 'Sukses', "msg" => "Perubahan User ID/NIP sudah dilakukan..!!");
        echo json_encode($hasil);
    }

    private function getdataSimpeg()
    {
        ini_set('MAX_EXECUTION_TIME', '-1');
        ini_set('memory_limit', '-1');

        $lstdata = getContentUrl($this->config->item('serversinkronsimpeg'));

        if ($lstdata) {
            if (substr($lstdata,0,5)==='ERROR') {
                $data['status'] = 'error';
            } else {
                $namafile = 'assets/tmpsimpeg/data_' . date("Y-m-d-H-i-s") . '.json';
                $fp = fopen($namafile, 'w');
                if (flock($fp, LOCK_EX)) { // do an exclusive lock
                    ftruncate($fp, 0);
                    fwrite($fp, $lstdata);
                    flock($fp, LOCK_UN); // release the lock
                }
                fclose($fp);

                $namafileJson = FCPATH . $namafile;
                $adaJson = file_exists($namafileJson);

                if ($adaJson) {
                    $str = file_get_contents($namafileJson);
                    $data['datapegawai']=json_decode($str, true);
                    unlink($namafileJson);
                    $data['status'] = 'succes';
                } else {
                    $data['status'] = 'error';
                }
            }
        } else {
            $data['status'] = 'error';
        }
        return $data;
    }

    function listsimpeg()
    {
        $this->session->set_userdata('menu','9');
        $data['menu'] = '9';
        $uri_segment=3;
        $offset = 0;
        $jum_data = 0;
        $this_url = site_url('pegawai/listpagging/');

        $dataSimpeg=$this->getdataSimpeg();
        $nIdx=0;
        foreach ($dataSimpeg['datapegawai'] as $itemdata) {

            $this->db->where('userid', $itemdata["nip"]);
            $query = $this->db->get("userinfo");
            if ($query->num_rows()>0) {
                unset($dataSimpeg['datapegawai'][$nIdx]);
            }

            $nIdx++;
        }

        $aSts = $this->utils->getStatusPegawai();

        $this->db->select('deptid,deptname');
        $this->db->from('departments');
        $queryf = $this->db->get();
        $rslUnor=$queryf->result_array();
        foreach ($rslUnor as $dataR)
        {
            $rsltUnor[$dataR['deptid']]['deptname']=$dataR['deptname'];
        }

        if (isset($dataSimpeg['datapegawai'])) {
            $aListPeg = $dataSimpeg['datapegawai'];
            $i 		= 0;
            $this->session->set_userdata(array('datasimpeg' => $aListPeg));
            foreach ($aListPeg as $itemdata) {

                $nama_unor="";$nama_status="";
                $kdUnker = substr($itemdata["kunker_definitif"], 2);

                if (isset($aSts[$itemdata['status_pegawai']])) {
                    $nama_status = $aSts[$itemdata['status_pegawai']];
                }

                if (isset($rsltUnor[$kdUnker])) {
                    $nama_unor= $rsltUnor[$kdUnker]['deptname'];
                }
                /*if (!empty($itemdata['peringkat_definitif']) &&
                    ($itemdata['status_pegawai']=='1' || $itemdata['status_pegawai']=='2'))*/ {
                    $data['ListArray'][$i]['nip'] = $itemdata['nip'];
                    $data['ListArray'][$i]['nama'] = $itemdata['nama_panggilan'];
                    $data['ListArray'][$i]['kelasjabatan'] = $itemdata['peringkat_definitif'];
                    $data['ListArray'][$i]['nama_unor'] = $nama_unor;
                    $data['ListArray'][$i]['jabatan'] = $itemdata['njab_definitif'];
                    $data['ListArray'][$i]['status'] = $nama_status;
                    $i++;
                }
            }
            $jum_data = count($aListPeg);
        }

        $data['jum_data'] = $jum_data;
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();

        $data['order'] = 'nip';
        $data['typeorder'] = 'sorting';
        $this->template->load('template','display-simpeg',$data);
    }

    function adddata($nip=null)
    {
        $aListPeg = $this->session->userdata('datasimpeg');

        $this->db->order_by("keselon","desc");
        $rows= $this->db->get("ref_eselon");

        foreach($rows->result() as $row)
        {
            $dataeselon[$row->neselon] = $row->neselon;
        }
        $data["lstEselon"] = $dataeselon;

        $rows1= $this->db->get("mastertunjangan");

        foreach($rows1->result() as $row)
        {
            $datakelas[$row->kelasjabatan] = $row->kelasjabatan.' ['.number_format($row->tunjangan,0,',','.').']';
        }
        $data["lstKelas"] = $datakelas;

        $rows2= $this->db->get("ref_golruang");

        foreach($rows2->result() as $row)
        {
            $datagol[$row->ngolru] = $row->ngolru.'  ['.$row->pangkat.']';
        }
        $data["lstGol"] = $datagol;

        if (empty($aListPeg))
        {
            redirect("pegawai/listsimpeg");
        }
        else {
            if ($nip == null)  redirect("pegawai/listsimpeg");
            //print_r($aListPeg);

            //$key = array_search($nip, $aListPeg);

            $aSts = $this->utils->getStatusPegawai();

            $this->db->select('deptid,deptname');
            $this->db->from('departments');
            $queryf = $this->db->get();
            $rslUnor=$queryf->result_array();
            foreach ($rslUnor as $dataR)
            {
                $rsltUnor[$dataR['deptid']]['deptname']=$dataR['deptname'];
            }

            foreach ($aListPeg as $itemdata) {
                if ($itemdata['nip']===$nip)
                {
                    $kdUnker = substr($itemdata["kunker_definitif"], 2);
                    $nama_unor="";$nama_status="";
                    if (isset($aSts[$itemdata['status_pegawai']])) {
                        $nama_status = $aSts[$itemdata['status_pegawai']];
                    }

                    if (isset($rsltUnor[$kdUnker])) {
                        $nama_unor= $rsltUnor[$kdUnker]['deptname'];
                    } else {
                        $kdUnker="";
                    }

                    if ($itemdata["status_plt"] == "1") { //jika status PLT
                        $dataup["plt_jbtn"] = $itemdata["njab_plt"];
                        $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plt"];
                        $dataup["plt_sk"] = $itemdata["sk_plt"];
                        $dataup["tmt_plt"] = $itemdata["tmtjab_plt"];

                        $kdUnker = substr($itemdata["kunker_plt"], 2);

                        $this->db->select('deptid');
                        $this->db->from('departments');
                        $this->db->where('deptid', empty($itemdata["kunker_plt"]) ? "XX" : $kdUnker);
                        $queryf = $this->db->get();
                        if ($queryf->num_rows() > 0) {
                            $dataup["plt_deptid"] = empty($itemdata["kunker_plt"]) ? "" : $kdUnker;
                        }

                        $this->db->select('neselon');
                        $this->db->from('ref_eselon');
                        $this->db->where('keselon', $itemdata["keselon_plt"]);
                        $queryf = $this->db->get();
                        if ($queryf->num_rows() > 0) {
                            $dataup["plt_eselon"] = $queryf->row()->neselon;
                        } else {
                            $dataup["plt_eselon"] = "";
                        }
                    } else {

                        if ($itemdata["status_plh"] == "1") { //Jika status PLH
                            $dataup["plt_jbtn"] = $itemdata["njab_plh"];
                            $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plh"];
                            $dataup["plt_sk"] = $itemdata["sk_plh"];
                            $dataup["tmt_plt"] = $itemdata["tmtjab_plh"];

                            $kdUnker = substr($itemdata["kunker_plh"], 2);
                            $this->db->select('deptid');
                            $this->db->from('departments');
                            $this->db->where('deptid', empty($itemdata["kunker_plh"]) ? "XX" : $kdUnker);
                            $queryf = $this->db->get();
                            if ($queryf->num_rows() > 0) {
                                $dataup["plt_deptid"] = empty($itemdata["kunker_plh"]) ? "" : $kdUnker;
                            }
                            $this->db->select('neselon');
                            $this->db->from('ref_eselon');
                            $this->db->where('keselon', $itemdata["keselon_plh"]);
                            $queryf = $this->db->get();
                            if ($queryf->num_rows() > 0) {
                                $dataup["plt_eselon"] = $queryf->row()->neselon;
                            } else {
                                $dataup["plt_eselon"] = "";
                            }
                        } else {
                            //jika status PLT & PLH tidak aktif
                            $dataup["plt_eselon"] = "";
                            $dataup["plt_deptid"] = "";
                            $dataup["plt_jbtn"] = "";
                            $dataup["plt_kelasjabatan"] = null;
                            $dataup["plt_sk"] = null;
                            $dataup["tmt_plt"] = null;
                        }

                    }
                    $golru="";
                    $this->db->select('ngolru');
                    $this->db->from('ref_golruang');
                    $this->db->where('kgolru', $itemdata["kgolru"]);
                    $queryf = $this->db->get();
                    if ($queryf->num_rows() > 0) {
                        $golru = $queryf->row()->ngolru;
                    }

                    $data['field']=array(
                        'nip'=>$itemdata['nip'],
                        'name'=>$itemdata['namapeg'],
                        'nickname'=>$itemdata['nama_panggilan'],
                        'deptname'=>$nama_unor,
                        'deptid'=>$kdUnker,
                        'title' => $itemdata['njab_definitif'],
                        'tmtjabatan'=>$itemdata['tmtjab'],
                        "gender" => $itemdata["jenis_kelamain"],
                        "tmtpangkat" => $itemdata["tmtpang"],
                        "kedudukan" => $itemdata["kduduk"],
                        "jenispegawai" => $itemdata["jenis_pegawai"],
                        "npwp" => $itemdata["npwp"],
                        "religion" => $itemdata["agama"],
                        "birthdate" => $itemdata["tgl_lahir"],
                        "placebirthdate"=> $itemdata["tempat_lahir"],
                        "jftstatus"=>$itemdata["status_pegawai"],
                        'kelasjabatan' => $itemdata["peringkat_definitif"],
                        'golru' => $golru,
                        'tmtkedudukan' => $itemdata["tmtpang"],
                        'jenisjabatan'=>'',
                        'eselon'=>'',
                        'tunjanganprofesi'=>0,
                        'tmtprofesi'=>null,
                        'plt_deptname'=>null,
                        'plt_deptid'=>$dataup["plt_deptid"],
                        'tmt_plt'=>$dataup["tmt_plt"],
                        'plt_eselon'=>$dataup["plt_eselon"],
                        'plt_kelasjabatan'=>null,
                        'plt_sk'=>$dataup["plt_sk"],
                        'plt_jbtn'=>$dataup["plt_jbtn"],
                        'payable'=>1,
                        'no_rekening'=>null,

                    );
                    break;
                }
            }
        }

        $this->template->load('template','form-simpeg',$data);
    }

    function savenew()
    {
        $data['msg'] = 'Data pegawai berhasil disimpan..';
        $data['status'] = 'succes';
        die(json_encode($data));
    }

    public  function rowjnspeg()
    {
        $id = $this->input->post('id');

        $this->db->where('id',$id);
        $this->db->delete('jenispegawaihistory');

        $data['msg'] = 'History pegawai sudah dihapus..';
        $data['status'] = 'succes';

        $this->output->set_output( json_encode($data));
    }

    public  function deldata()
    {
        //$woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('id');

        $uid="";
        for ($i = 0; $i < count($id); $i++) {
            $nId = is_numeric($id[$i]) ? $id[$i] : 0;

            $this->db->where('id', $nId);
            $query = $this->db->get("userinfo");
            $datas = $query->row_array();
            $uid .= $datas["userid"];

            $userarea = $this->pegawai->getareauserinfo($datas["userid"]);
            foreach ($userarea->result() as $usea) {
                //$serialno = $this->pegawai->getsnarea($usea->areaid);
                $serialno = $this->pegawai->getsnareaaktif($usea->areaid);
                foreach ($serialno->result() as $serno) {
                    /*$comdev = array(
                        'sn' => $serno->sn,
                        'cmd' => 'DATA DEL_FP PIN=' . $datas["userid"],
                        'status' => 1,
                        'submittime' => date("Y-m-d H:i:s")
                    );
                    $this->db->insert('command', $comdev);*/

                    $comdev = array(
                        'sn' => $serno->sn,
                        'cmd' => 'DATA DEL_USER PIN='.$datas["userid"],
                        'status' => 1,
                        'submittime' => date("Y-m-d H:i:s")
                    );
                    $this->db->insert('command', $comdev);
                    //$woowdb->insert('command', $comdev);
                }
            }
            //create copy data first
            $this->db->where('userid', $datas["userid"]);
            $query = $this->db->get("userinfo");
            if ($query->num_rows()>0) {
                $datasd = $query->row_array();
                log_history("delete", "userinfo", $datasd);
            }
            //delete data
            $this->db->where('userid', $datas["userid"]);
            $this->db->delete('userinfo');

            //create copy data first
            $this->db->where('userid', $datas["userid"]);
            $query = $this->db->get("template");
            if ($query->num_rows()>0) {
                $datasd = $query->row_array();
                log_history("delete", "template", $datasd);
            }
            //delete data
            $this->db->where('userid', $datas["userid"]);
            $this->db->delete('template');

            //create copy data first
            $this->db->where('userid', $datas["userid"]);
            $query = $this->db->get("userinfo_attarea");
            if ($query->num_rows()>0) {
                $datasd = $query->row_array();
                log_history("delete", "userinfo_attarea", $datasd);
            }
            //delete data
            $this->db->where('userid', $datas["userid"]);
            $this->db->delete('userinfo_attarea');

            if (isset($datas)) {
                createLog("Menghapus data pegawai " . $datas["userid"], "Sukses");
            }

            /*} else {
                if (isset($datas)) {
                    createLog("Menghapus fingerprint " . $datas["userid"], "Error");
                }
            }*/
        }

        $data['msg'] = 'Data Pegawai sudah dihapus..';
        $data['status'] = 'succes';

        $this->output->set_output( json_encode($data));
    }

    public  function cekdata()
    {
        $newuserid=$this->input->post('newUID');
        $olduserid = $this->input->post('olduseid');

        $this->db->select("userid,name");
        $this->db->where("id",$olduserid);
        $row=$this->db->get("userinfo");
        $arowA = $row->row_array();

        $this->db->select("userid,name");
        $this->db->where("userid",$newuserid);
        $row=$this->db->get("userinfo");
        if ($row->num_rows()>0)
        {
            $arow = $row->row_array();
            $data['msg'] = '<br>Dari Pegawai : <strong>'.$arowA['name'].'</strong><br>Ke Pegawai : <strong>'.$arow['name'].'</strong>';
            $data['status'] = 'succes';
        } else {
            $data['msg'] = 'Data Pegawai tidak ditemukan..';
            $data['status'] = 'error';
        }
        $this->output->set_output( json_encode($data));
    }

    public  function movedata()
    {
        $newuserid=$this->input->post('newUID');
        $olduserid = $this->input->post('olduseid');

        $this->db->select("userid,name");
        $this->db->where("id",$olduserid);
        $row=$this->db->get("userinfo");
        $arowA = $row->row_array();

        $this->db->select("userid,name");
        $this->db->where("userid",$newuserid);
        $row=$this->db->get("userinfo");
        if ($row->num_rows()>0)
        {
            $arow = $row->row_array();

            $this->db->set('userid', $arow['userid']);
            $this->db->where('userid', $arowA['userid']);
            $this->db->update('checkinout');

            $this->db->set('userid', $arow['userid']);
            $this->db->where('userid', $arowA['userid']);
            $this->db->update('process');

            $this->db->set('userid', $arow['userid']);
            $this->db->where('userid', $arowA['userid']);
            $this->db->update('rosterdetails');

            $this->db->set('userid', $arow['userid']);
            $this->db->where('userid', $arowA['userid']);
            $this->db->update('rosterdetailsatt');

            $this->db->set('userid', $arow['userid']);
            $this->db->where('userid', $arowA['userid']);
            $this->db->update('template');

            $this->db->set('userid', $arow['userid']);
            $this->db->where('userid', $arowA['userid']);
            $this->db->update('data_uang_makan');

            $this->db->set('userid', $arow['userid']);
            $this->db->where('userid', $arowA['userid']);
            $this->db->update('data_proses');

            $data['msg'] = 'Data presensi Pegawai sudah <strong>dipindah</strong>!!';
            $data['status'] = 'succes';
        }
        else {
            $data['msg'] = 'Data presensi Pegawai tidak bisa dipindahkan<br>Data Pegawai tidak ditemukan..';
            $data['status'] = 'error';
        }

        $this->output->set_output( json_encode($data));
    }
}
