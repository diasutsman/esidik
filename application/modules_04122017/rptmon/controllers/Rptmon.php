<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rptmon extends MX_Controller {

    private $aAkses;

    function Rptmon(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model');
		$this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('report_model');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Rptmon", $this->session->userdata('s_access'));
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
        $this_url = site_url('rpttransaksi/pagging/');
        $data2 = $this->mypagination->getPagination(0,10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = 0;
        $data['result'] = array();
        $data['lstStsPeg'] = $this->utils_model->getStatusPegawai();
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
            $SQLcari .= " AND ( deptname LIKE '%".str_replace('%20',' ',$cr)."%' 
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

            $SQLcari .= " and jenispegawai in (".implode(',', $s).") ";
        }
        $SQLcari .= " and jftstatus in ('1','2')  ";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('rptmon/pagging/');
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
        $start_date = $this->input->post('start');
        $end_date = $this->input->post('end');
        $excelid = 0; //$_GET['excelid'];
        $periode = 'Periode : '.format_date_ind(date('Y-m-d', $start_date))." s/d ".format_date_ind(date('Y-m-d', $end_date));
        $arr_days = $this->utils_model->createDateRangeArray($start_date,$end_date);

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

        if($this->input->post('org')!='undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('s_dept'))):array();

        if(!empty($orgid)) {
            foreach($orgid as $org)
                $orga[] = "'".$org."'";
            $orgaidi = implode(',', $orga);
            $with_user_id = "deptid IN (".$orgaidi.") ";
            $tambahan = "deptid IN (".$orgaidi.") ";
        }

        if ( $this->input->post('uid')!='undefined') {
            $useraidi = explode(',',substr($this->input->post('uid'),0,-1));
            foreach($useraidi as $usr)
                $usernya[] = "'".$usr."'";
            $with_user_id = "userid IN (".implode(',',$usernya).") ";
            $tambahan = "a.userid IN (".implode(',',$usernya).") ";
        }

        $sqlcok = "select a.userid, date_shift, check_in, check_out, attendance 
						from process a join userinfo b on a.userid=b.userid 
						where ".$tambahan." and date_shift >= '".$this->db->escape_str($start_date)."' and date_shift <= '".$this->db->escape_str($end_date)."'";

        $querycok = $this->db->query($sqlcok);

        $query_att_perdate = "SELECT userid,badgenumber,name, deptname
                            FROM userinfo JOIN departments USING(deptid) 
                            WHERE ".$with_user_id."
                            GROUP BY userid,badgenumber,name, deptname ORDER BY deptname,name ";

        $group_per_date = $this->db->query($query_att_perdate);

        $data = array(
            "cominfo" => $company,
            "periode" => $periode,
            "arr_days" => $arr_days,
            "querycok" => $querycok,
            "group_per_date" => $group_per_date
        );
        if($excelid==1) {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=inoutreport.xls");
        }
        $this->load->view("laporan",$data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */