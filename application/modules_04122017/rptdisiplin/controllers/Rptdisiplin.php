<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rptdisiplin extends MX_Controller {

    private $aAkses;
	function Rptdisiplin(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
        $this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('report_model');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Rptdisiplin", $this->session->userdata('s_access'));

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
        $this->session->set_userdata('menu','23');
        $uri_segment=3;
        $offset = 0;
        //$orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        /*$SQLcari =" and jenispegawai in (1,2)";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);*/
        $this_url = site_url('rptdisiplin/pagging/');
        $data2 = $this->mypagination->getPagination(0,10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = 0;
        $data['result'] = array();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();
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
            $SQLcari .= " AND ( deptname LIKE '%".str_replace('%20',' ',$cr)."%' or name LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or userid LIKE '%".str_replace('%20',' ',$cr)."%' or badgenumber LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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

        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        //echo $this->db->last_query();
        $this_url = site_url('rptdisiplin/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();

        $this->load->view('list',$data);
    }

    public function save()
    {
        $id = $this->input->post('uid');
        $stspeg = $this->input->post('stspeg');
        $orgid = $this->input->post('org');
        $cari = $this->input->post('cari');

        //createLogUser("Delete Data ".$nId,"Pengguna");
        $data['msg'] = 'Data berhasil diproses..';
        $data['status'] = 'succes';

        echo json_encode($data);
    }


    public function view()
    {
        $jns = $this->input->post('jns');
        $this->recapkehadiran($jns);

    }
    function recapkehadiran($njns)
    {

        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $userid = $this->input->post('uid');
        $datestart = strtotime($postdatestart);
        $datestop = strtotime($postdatestop);

        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);

        $orgid = $this->input->post('org');
        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');
        $simple =$njns;

        $periode = 'Periode : '.format_date_singkat(date("Y-m-d",strtotime($postdatestart))) .' - '.format_date_singkat(date("Y-m-d",strtotime($postdatestop)));
        $arr_days = $this->createDateRangeArray($postdatestart,$postdatestop);

        $compa = $this->report_model->getcompany();
        $company = array(
            'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
            'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
            'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
            'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
            'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
            'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
        );

        $with_user_id = '';
        $tambahan = '';

        if($orgid!='undefined')	{
            $this->db->select('deptname');
            $this->db->from('departments');
            $this->db->where('deptid', $orgid);
            $query = $this->db->get();
            $namadept = $query->row()->deptname;

            $orgid = $this->pegawai->deptonall($orgid);

        } else {
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('s_dept'))):array();
            $namadept = 'Semua Unit Kerja';
        }



        if($userid!='undefined') {
            $useraidi = explode(',',$userid);
            foreach($useraidi as $usr)
                $usernya[] = "'".$usr."'";
            $with_user_id = "userid IN (".implode(',',$usernya).") ";
            $tambahan = "a.userid IN (".implode(',',$usernya).") ";
        } else {
            if(!empty($orgid)) {
                foreach($orgid as $org)
                    $orga[] = "'".$org."'";
                $orgaidi = implode(',', $orga);
                $with_user_id = "b.deptid IN (".$orgaidi.") ";
                $tambahan = "b.deptid IN (".$orgaidi.") ";
            }
        }

        $SQLcari ="";
        if(!empty($stspeg)) {
            $s = array();
            foreach($stspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari = " and jftstatus in (".implode(',', $s).") ";
        }

        if(!empty($jnspeg)) {
            $s = array();
            foreach($jnspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari = " and jenispegawai in (".implode(',', $s).") ";
        }
        //$SQLcari .= " and  in ('1','2')  ";

        $sqlcok = "select a.userid, date_shift, check_in, check_out, attendance, workinholiday, late, early_departure
						from process a join userinfo b on a.userid=b.userid 
						where ".$tambahan." and (date_shift between '".$postdatestart."' and '".$postdatestop."' ) ".$SQLcari;

        $querycok = $this->db->query($sqlcok);

        if($simple==1) {
            $query_att_perdate = "SELECT userid, name, deptname, title, golru, kelasjabatan,pangkat
                                    FROM userinfo b
                                    JOIN departments a on a.deptid = b.deptid 
                                    left join ref_golruang on lower(trim(b.golru))=lower(trim(ref_golruang.ngolru))
                                    WHERE ".$with_user_id.$SQLcari."
                                    GROUP BY userid, name, deptname, title, eselon, golru, kelasjabatan, b.id, parentid ORDER BY eselon, golru desc, kelasjabatan desc";
        } else {
            $query_att_perdate = "SELECT userid,badgenumber,name, deptname, title,golru, kelasjabatan,pangkat 
                                FROM userinfo b
                                JOIN departments a on a.deptid = b.deptid
                                left join ref_golruang on lower(trim(b.golru))=lower(trim(ref_golruang.ngolru))
                                WHERE ".$with_user_id.$SQLcari."
                                GROUP BY badgenumber,name, deptname, title, eselon, golru, kelasjabatan ORDER BY eselon, golru desc, kelasjabatan desc ";
        }
        $group_per_date = $this->db->query($query_att_perdate);
        $data = array(
            "cominfo" => $company,
            "periode" => $periode,
            "arr_days" => $arr_days,
            "querycok" => $querycok,
            "group_per_date" => $group_per_date,
            "nama_dept"	=> $namadept,
            "excelid"	=> $excelid
        );
        if($simple==1) {
            if ($ispdf==1) {
                $this->load->library('mpdf');
                $this->mpdf =
                    new mPDF('',    // mode - default ''
                        'Legal',    // format - A4, for example, default ''
                        0,     // font size - default 0
                        'arial',    // default font family
                        5,    // margin left
                        5,    // margin right
                        5,    // margin top
                        5,    // margin bottom
                        9,     // margin header
                        5,     // margin footer
                        'L');  // L - landscape, P - portrait
                $this->mpdf->simpleTables = true;
                $this->mpdf->packTableData = true;

                $stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $this->mpdf->WriteHTML($stylesheet,1);
                $datavw = $this->load->view("rekapkehadiransimple",$data,true);
                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=disiplinpegawai.xls");
            }
            $this->load->view("rekapkehadiransimple",$data);
        } else {
            if ($ispdf==1) {
                $this->load->library('mpdf');
                $this->mpdf =
                    new mPDF('',    // mode - default ''
                        'Legal',    // format - A4, for example, default ''
                        0,     // font size - default 0
                        'arial',    // default font family
                        5,    // margin left
                        5,    // margin right
                        5,    // margin top
                        5,    // margin bottom
                        9,     // margin header
                        5,     // margin footer
                        'L');  // L - landscape, P - portrait
                $this->mpdf->simpleTables = true;
                $this->mpdf->packTableData = true;

                $stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
                $this->mpdf->WriteHTML($stylesheet,1);
                $datavw = $this->load->view("rekapkehadiran",$data,true);
                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }
            if($excelid==1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=rekapkehadiran.xls");
            }
            $this->load->view("rekapkehadiran",$data);

        }
    }

    private function createDateRangeArray($strDateFrom,$strDateTo)
    {
        $aryRange=array();
        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom) {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry

            while ($iDateFrom<$iDateTo) {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */