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
            $dataeselon[$row->keselon] = $row->neselon;
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
        $dataIn['plt_deptid'] =  $this->input->post('plt_deptid');
        $dataIn['plt_sk'] =  $this->input->post('plt_sk');
        $dataIn['plt_kelasjabatan'] =  $this->input->post('plt_kelasjabatan');
        $dataIn['no_rekening'] =  $this->input->post('no_rekening');
        $dataIn['payable'] =  $this->input->post('payable');
        $dataIn['plt_jbtn'] =  $this->input->post('plt_jbtn');


        if ($idx>0)
        {
            $this->db->where('id', $idx);
            $dataawal=$this->db->get("userinfo")->row_array();
            log_history("edit","userinfo",$dataawal);

            $dataIn['modify_by'] =  $this->session->userdata('s_username');
            $dataIn['modif_date'] =  date('Y-m-d H:i:s');
            $this->db->where('id', $idx);
            $update = $this->db->update('userinfo',$dataIn);
            if(!$update){
                createLog("Merubah Pegawai ".$dataawal["userid"],"Gagal");
                $data['msg'] = 'Maaf, Tidak bisa merubah data..';
                $data['status'] = 'error';

            }else{
                createLog("Merubah Pegawai ".$dataawal["userid"],"Sukses");
                $data['msg'] = 'Data berhasil dirubah..';
                $data['status'] = 'succes';
            }
        }
        die(json_encode($data));
    }

    public  function delfp()
    {
        $woowdb = $this->load->database('woow', TRUE);
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
                $serialno = $this->pegawai->getsnarea($usea->areaid);
                foreach ($serialno->result() as $serno) {
                    $comdev = array(
                        'sn' => $serno->sn,
                        'cmd' => 'DATA DEL_FP PIN=' . $datas["userid"],
                        'status' => 1,
                        'submittime' => date("Y-m-d H:i:s")
                    );
                    $this->db->insert('command', $comdev);
                    $woowdb->insert('command', $comdev);
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

        $data['msg'] = 'FP Pegawai di Mesin sudah dihapus..';
        $data['status'] = 'succes';

        $this->output->set_output( json_encode($data));
    }

    public  function rubaharea()
    {
        $woowdb = $this->load->database('woow', TRUE);
        $id = $this->input->post('idarea');
        $areaid = explode(',', $this->input->post('area'));

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
                if(isset($serialno)) {
                    foreach($serialno->result() as $serno) {
                        if($this->pegawai->getactiveemp($datas["userid"])!=1) {
                            $comdev= array (
                                'sn'			=>$serno->sn,
                                'cmd'			=>'DATA USER PIN='.$datas["userid"].'	Name='.$usertable->row()->nickname.'	Passwd='.$usertable->row()->password.'	Card='.$usertable->row()->card.'	Grp='.$usertable->row()->accgroup.'	TZ='.$usertable->row()->timezones.'	Pri='.$usertable->row()->privilege,
                                'status'		=>1,
                                'submittime'	=>date("Y-m-d H:i:s")
                            );
                            $this->db->insert('command', $comdev);
                            $woowdb->insert('command', $comdev);
                        }
                    }
                }
            }

            foreach($del as $dele) {
                $serialno=$this->pegawai->getsnarea($dele);
                if(isset($serialno)) {
                    foreach($serialno->result() as $serno) {
                        $comdev= array (
                            'sn'			=>$serno->sn,
                            'cmd'			=>'DATA DEL_USER PIN='.$datas["userid"],
                            'status'		=>1,
                            'submittime'	=>date("Y-m-d H:i:s")
                        );
                        $this->db->insert('command', $comdev);
                        $woowdb->insert('command', $comdev);
                    }
                }
                $this->db->delete('userinfo_attarea', array('userid'=>$datas["userid"], 'areaid'=>$dele));
                $woowdb->delete('userinfo_attarea', array('userid'=>$datas["userid"], 'areaid'=>$dele));
            }

            foreach($add as $adda) {
                $usertablearea = $this->pegawai->get_user($datas["userid"]);
                $fingertab = $this->pegawai->get_fp($datas["userid"]);
                $serialno = $this->pegawai->getsnarea($adda);
                if (isset($serialno)) {
                    foreach ($serialno->result() as $serno) {
                        if ($this->pegawai->getactiveemp($datas["userid"]) != 1) {
                            $comdev = array(
                                'sn' => $serno->sn,
                                'cmd' => 'DATA USER PIN=' . $datas["userid"] . '	Name=' . $usertablearea->row()->nickname . '	Passwd=' . $usertablearea->row()->password . '	Card=' . $usertablearea->row()->card . '	Grp=' . $usertablearea->row()->accgroup . '	TZ=' . $usertablearea->row()->timezones . '	Pri=' . $usertablearea->row()->privilege,
                                'status' => 1,
                                'submittime' => date("Y-m-d H:i:s",time() + 30)
                            );
                            $woowdb->insert('command', $comdev);
                            $this->db->insert('command', $comdev);

                            foreach ($fingertab->result() as $quedfp) {
                                $comdev = array(
                                    'sn' => $serno->sn,
                                    'cmd' => 'DATA FP PIN=' . $datas["userid"] . '	FID=' . $quedfp->fingerid . '	Valid=' . $quedfp->valid . '	TMP=' . $quedfp->template,
                                    'status' => 1,
                                    'submittime' => date("Y-m-d H:i:s",time() + 30)
                                );

                                $woowdb->insert('command', $comdev);
                                $this->db->insert('command', $comdev);
                            }
                        }
                    }
                }
                $insertuser = array(
                    'userid' => $datas["userid"],
                    'areaid' => $adda
                );
                $woowdb->insert('userinfo_attarea', $insertuser);
                $this->db->insert('userinfo_attarea', $insertuser);

            }
        }

        $data['msg'] = 'Area Pegawai sudah dirubah..';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
    }

    public function synchronizing()
    {
            $userid = $_POST['id'];
            $jmldata = count($userid);
            $woowdb = $this->load->database('woow', TRUE);
            for($i=0;$i<$jmldata;$i++) {
                $this->db->where('id', $userid[$i]);
                $query = $this->db->get("userinfo");
                $datas = $query->row_array();

                $userarea=$this->pegawai->getareauserinfo($datas["userid"]);
                foreach($userarea->result() as $usea) {
                    $serialno=$this->pegawai->getsnarea($usea->areaid);
                    $usertable=$this->pegawai->get_user($datas["userid"]);
                    $fingertab=$this->pegawai->get_fp($datas["userid"]);
                    foreach($serialno->result() as $serno) {
                            $comdev= array (
                                'sn'			=>$serno->sn,
                                'cmd'			=>'DATA USER PIN='.$datas["userid"].'	Name='.$usertable->row()->nickname.'	Passwd='.$usertable->row()->password.'	Card='.$usertable->row()->card.'	Grp='.$usertable->row()->accgroup.'	TZ='.$usertable->row()->timezones.'	Pri='.$usertable->row()->privilege,
                                'status'		=>1,
                                'submittime'	=>date("Y-m-d H:i:s")
                            );
                            $this->db->insert('command', $comdev);
                            createLog("Memasukan data ".$datas["userid"]." ke SN " . $serno->sn , "Sukses");

                            $woowdb->insert('command', $comdev);

                            foreach($fingertab->result() as $quedfp) {
                                $comdev = array (
                                    'sn' 			=>$serno->sn,
                                    'cmd'			=>'DATA FP PIN='.$datas["userid"].'	FID='.$quedfp->fingerid.'	Valid='.$quedfp->valid.'	TMP='.$quedfp->template,
                                    'status'		=>1,
                                    'submittime'	=>date("Y-m-d H:i:s")
                                );
                                $this->db->insert('command', $comdev);
                                createLog("Memasukan FP ".$datas["userid"]." ke SN " . $serno->sn , "Sukses");

                                $woowdb->insert('command', $comdev);

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

        $woowdb = $this->load->database('woow', TRUE);
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
                    $woowdb->insert('command', $comdevs);
                    $this->db->insert('command', $comdevs);*/

                    //sleep(30);

                    $comdev = array(
                        'sn' => $nId,
                        'cmd' => 'DATA USER PIN=' . $uId . '	Name=' . $usertable->name . '	Passwd=' . $usertable->password . '	Card=' . $usertable->card . '	Grp=' . $usertable->accgroup . '	TZ=' . $usertable->timezones . '	Pri=' . $usertable->privilege,
                        'status' => 1,
                        'submittime' => date("Y-m-d H:i:s",time() + 20)
                    );

                    $woowdb->insert('command', $comdev);
                    $this->db->insert('command', $comdev);

                    $fingertab = $this->pegawai->get_fp($uId);
                    foreach ($fingertab->result() as $quedfp) {
                        $comdevt = array(
                            'sn' => $nId,
                            'cmd' => 'DATA FP PIN=' . $uId . '	FID=' . $quedfp->fingerid . '	Valid=' . $quedfp->valid . '	TMP=' . $quedfp->template,
                            'status' => 1,
                            'submittime' => date("Y-m-d H:i:s",time() + 20)
                        );
                        $woowdb->insert('command', $comdevt);
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

        $lstdata = getContentUrl("http://ropeg.setjen.kemendagri.go.id/restserver/index.php/api/for_absen/pegawai_dengan_plt_plh");

        $namafile = 'assets/tmpsimpeg/data_'.date("Y-m-d-H-i-s").'.json';
        $fp = fopen($namafile, 'w');
        if (flock($fp, LOCK_EX)) { // do an exclusive lock
            ftruncate($fp, 0);
            fwrite($fp, $lstdata);
            flock($fp, LOCK_UN); // release the lock
        }
        fclose($fp);

        $namafileJson =FCPATH.$namafile;
        $adaJson = file_exists($namafileJson);
        if ($adaJson)
        {
            $str = file_get_contents($namafileJson);
            $aListPeg = json_decode($str, true);

            foreach ($aListPeg as $itemdata)
            {
                $dataup=null;
                $golru="";
                $this->db->select('ngolru');
                $this->db->from('ref_golruang');
                $this->db->where('kgolru', $itemdata["kgolru"]);
                $queryf = $this->db->get();
                if ($queryf->num_rows()>0) {
                    $golru = $queryf->row()->ngolru;
                }

                $dataup["name"] =  $itemdata["namapeg"];
                $dataup["title"] =  $itemdata["njab_definitif"];
                $dataup["placebirthdate"] =  $itemdata["tempat_lahir"];
                $dataup["nickname"] =  $itemdata["nama_panggilan"];
                $dataup["gender"] =  $itemdata["jenis_kelamain"];

                $arrproc=array("1","2","3","4","5","6","7","8");
                $allowproc=in_array($itemdata["agama"],$arrproc);
                if ($allowproc) {
                    $dataup["religion"] = $this->utils->getKdAgama()[$itemdata["agama"]];
                }
                $dataup["birthdate"] =  $itemdata["tgl_lahir"];
                $dataup["golru"] =  $golru;
                $dataup["kelasjabatan"] =  $itemdata["peringkat_definitif"];
                $dataup["jftstatus"] =  $itemdata["status_pegawai"];
                $dataup["jenispegawai"] =  $itemdata["jenis_pegawai"];
                $dataup["kedudukan"] =  $itemdata["kduduk"];
                $dataup["tmtpangkat"] =  $itemdata["tmtpang"];

                $this->db->select('deptid');
                $this->db->from('departments');
                $this->db->where('deptid', substr($itemdata["kunker_definitif"],1,-1));
                $queryf = $this->db->get();
                if ($queryf->num_rows()>0) {
                    $dataup["deptid"] =  substr($itemdata["kunker_definitif"],1,-1);
                }

                if ($itemdata["status_plt"]=="1") {
                    $dataup["plt_jbtn"] = $itemdata["njab_plt"];
                    $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plt"];
                    $dataup["plt_sk"] = $itemdata["sk_plt"];
                    $dataup["tmt_plt"] = $itemdata["tmtjab_plt"];

                    $this->db->select('deptid');
                    $this->db->from('departments');
                    $this->db->where('deptid', empty($itemdata["kunker_plt"]) ? "" : substr($itemdata["kunker_plt"], 1, -1));
                    $queryf = $this->db->get();
                    if ($queryf->num_rows()>0) {
                        $dataup["plt_deptid"] =  empty($itemdata["kunker_plt"]) ? "" : substr($itemdata["kunker_plt"], 1, -1);
                    }
                }

                if ($itemdata["status_plh"]=="1") {
                    $dataup["plt_jbtn"] = $itemdata["njab_plh"];
                    $dataup["plt_kelasjabatan"] = $itemdata["peringkat_plh"];
                    $dataup["plt_sk"] = $itemdata["sk_plh"];
                    $dataup["tmt_plt"] = $itemdata["tmtjab_plh"];

                    $this->db->select('deptid');
                    $this->db->from('departments');
                    $this->db->where('deptid', empty($itemdata["kunker_plh"]) ? "" : substr($itemdata["kunker_plh"], 1, -1));
                    $queryf = $this->db->get();
                    if ($queryf->num_rows()>0) {
                        $dataup["plt_deptid"] =  empty($itemdata["kunker_plh"]) ? "" : substr($itemdata["kunker_plh"], 1, -1);
                    }
                }

                $this->db->select('neselon');
                $this->db->from('ref_eselon');
                $this->db->where('keselon', $itemdata["keselon"]);
                $queryf = $this->db->get();
                if ($queryf->num_rows()>0) {
                    $dataup["eselon"] =  $queryf->row()->neselon;
                } else {
                    $dataup["eselon"]="";
                }

                $this->db->from('userinfo');
                $this->db->where('userid', $itemdata["nip"]);
                $query = $this->db->get();
                if ($query->num_rows()>0) {
                    $datas = $query->row_array();
                    log_history("update-simpeg","userinfo",$datas);

                    $this->db->where('userid', $itemdata["nip"]);
                    $this->db->update("userinfo",$dataup);

                    $this->db->reset_query();

                } else {
                    $dataup["userid"] =  $itemdata["nip"];
                    $dataup["badgenumber"] =  $itemdata["nip"];
                    $dataup["timezones"] =  "0000000000000000";
                    $dataup["accgroup"] =  "1";

                    $this->db->insert("userinfo",$dataup);
                    $this->db->reset_query();
                }

            }

        }
		
        $data['msg'] = 'Data pegawai sudah disinkronisasikan..';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
    }
}
