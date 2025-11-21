<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Smsmasking extends MX_Controller {
    private $aAkses;

	function Smsmasking(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
        $this->load->library('mypagination' );
        $this->load->library('sessdata');

        $this->load->model('utils_model','utils');
		$this->load->model('smsmasking_model','mdl_sms');
        $this->load->model('process_model',"process_model");
        $this->load->model('report_model');
        $this->load->model('pegawai/pegawai_model','pegawai');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Smsmasking", $this->session->userdata('s_access'));
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->arr_filter	= array('stspeg','jnspeg','tgl1','tgl2','org','cari','order','sorting','lmt');

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
        $SQLcari="";
        $jum_data = 0;
        $this_url = site_url('smsmasking/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data,10,$this_url,$uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = $jum_data;
        $data['result'] = array();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();
        $data['order'] = 'id';
        $data['typeorder'] = 'sorting';

        $sesGet		= new sessdata;
		$sesGet->sesGet($this->arr_filter,$this->uri->segment(1));
		foreach($this->arr_filter as $fld){
			$data['filter_'.$fld] = $sesGet->{$fld};
        }
        $intsemptyall=0;
        foreach($this->arr_filter as $fld){
            $intsemptyall .= !empty($sesGet->{$fld})? 1:0;
        }

        if ($intsemptyall>0){
            $cr = $sesGet->cari;
            $limited = !empty($sesGet->lmt)?$sesGet->lmt:10;
            $org = $sesGet->org;
            $stspeg = $sesGet->stspeg;
            $jnspeg = $sesGet->jnspeg;
            $tgl1 = $sesGet->tgl1;
            $tgl2 = $sesGet->tgl2;

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
            } else {
                $SQLcari .= " and jftstatus in (1,2) " ;
            }

            if(!empty($jnspeg)) {
                $s = array();
                foreach($jnspeg as $ar)
                    $s[] = "'".$ar."'";

                $SQLcari .= " and jenispegawai in (".implode(',', $s).") ";
            }else {
                $SQLcari .= " and jenispegawai in (1,2) " ;
            }

            $data['order'] = $sesGet->order;
            $typeorder = $sesGet->sorting;
            if($typeorder!='' && $typeorder!=null){
                if($typeorder=='sorting'){
                    $sorting = 'ASC';
                }else if($typeorder=='sorting_asc'){
                    $sorting = 'ASC';
                }else{
                    $sorting = 'DESC';
                }
            }else{
                $sorting = 'DESC';
            }

            if($data['order']!='' && $data['order']!=null){
                $SQLcari .= " ORDER BY ".$data['order']." ".$sorting;
            }else{
                $SQLcari .= " ORDER BY id ASC ";
                $data['order'] ="id";
            }

            $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
            $jum_data = $this->pegawai->getDaftar(0,$limited,$offset,null,$SQLcari);
            $this_url = site_url("smsmasking/pagging");
            $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
            $data['paging'] = $data2['link'];
            $data['offset'] = $offset;
            $data['limit_display'] = $limited;
            $data['jum_data'] = $jum_data->num_rows();
            $data['result'] = $query->result();
            $data['start_date'] = dmyToymd($tgl1);
            $data['end_date'] = dmyToymd($tgl2);
        }
        //print_r($data);
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

        $sesSet = new sessdata;
		$sesSet->sesSet($this->arr_filter,$this->uri->segment(1));

        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $jnspeg = $this->input->post('jnspeg');
        $tgl1 = $this->input->post('tgl1');
        $tgl2 = $this->input->post('tgl2');

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

        $data['typeorder'] = $typeorder;

        $data['start_date'] = dmyToymd($tgl1);
        $data['end_date'] = dmyToymd($tgl2);
        
        $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("smsmasking/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    public function view()
    {
        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $userid = $this->input->post('uid');

        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",",$stspeg);
        $orgid = $this->input->post('org');
        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');

        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",",$jnspeg);

        $start_date = $postdatestart;
        $end_date = $postdatestop;
        

        if($orgid!='undefined')	{
            $orgid = $this->pegawai->deptonall($orgid);
            $this->db->select('deptname');
            $this->db->from('departments');
            $this->db->where('deptid', $this->input->post('org'));
            $query = $this->db->get();
            $namadept = $query->row()->deptname;
        } else {
            $orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('user_dept'))):array();
            $namadept = 'Semua';
        }

        if(!empty($orgid)) {
            foreach($orgid as $org)
                $orga[] = "'".$org."'";
            $orgaidi = implode(',', $orga);
            $tambahan = " deptid IN (".$orgaidi.") ";
        }

        if($userid!='undefined') {
            $useraidi = explode(',',$userid);
            foreach($useraidi as $usr)
                $usernya[] = "'".$usr."'";
            
            $tambahan = " and id IN (".implode(',',$usernya).")";
        }
        $with_jns_pegawai="";
        if ($jnspeg != null)
        {
            $with_jns_pegawai= " and jenispegawai IN (".implode(',',$jnspeg).") ";
        }

        $with_sts_pegawai="";
        if ($stspeg != null)
        {
            $with_sts_pegawai= " and jftstatus IN (".implode(',',$stspeg).") ";
        }

        $compa = $this->report_model->getcompany();
        $tmbhanFilter=' HAVING jml >= '.intval($compa->row()->batas_alpa_sms);
        $sqlcok = "select *,
        (
            select ifnull(count(*),0) as jml
						from process a 
						where a.userid=userinfo.userid and (a.date_shift >= '".$start_date."' and 
						a.date_shift <= '".$end_date."') and a.attendance in ('ALP','AB_12')
        ) as jml
                        from userinfo
						where userid is not null ".$tambahan.$with_jns_pegawai.$with_sts_pegawai.$tmbhanFilter;
        $querycok = $this->db->query($sqlcok);

        $company = array(
            'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
            'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
            'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
            'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
            'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
            'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
        );

        $periode = format_date_ind($start_date).' - '.format_date_ind($end_date);

        $data = array(
            "cominfo" => $company,
            "periode" => $periode,
            "querycok" => $querycok,
            "nama_dept"	=> $namadept,
            "excelid"	=> $excelid,
            'pdfid'=> $ispdf,
            'start_date' => dmyToymd($start_date),
            'end_date' => dmyToymd($end_date)
        );
        //$ispdf=0;    
        //$excelid=0;
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

            //$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
            $stylepdf= BASEPATH.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
            $stylesheet = file_get_contents( $stylepdf );
            $this->mpdf->WriteHTML($stylesheet,1);

            $datavw = $this->load->view("lapsimple",$data,true);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->WriteHTML($datavw);

            $this->mpdf->Output();
            die();
        }
        if($excelid==1) {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=daftarpegawaisms-".date('ymdhns').".xls");
        }
        $this->load->view("lapsimple",$data);

    }

    public function process()
    {
        $postdatestart = dmyToymd($this->input->post('start'));
        $postdatestop = dmyToymd($this->input->post('end'));
        $userid = $this->input->post('userid');

        $stspeg = $this->input->post('stspeg');
        //$stspeg = explode(",",$stspeg);
        $orgid = $this->input->post('org');
        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');

        $jnspeg = $this->input->post('jnspeg');
        //$jnspeg = explode(",",$jnspeg);

        $start_date = $postdatestart;
        $end_date = $postdatestop;
        
        if($orgid!='undefined')	{
            $orgid = $this->pegawai->deptonall($orgid);
            $this->db->select('deptname');
            $this->db->from('departments');
            $this->db->where('deptid', $this->input->post('org'));
            $query = $this->db->get();
            $namadept = $query->row()->deptname;
        } else {
            $orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('user_dept'))):array();
            $namadept = 'Semua';
        }

        if(!empty($orgid)) {
            foreach($orgid as $org)
                $orga[] = "'".$org."'";
            $orgaidi = implode(',', $orga);

            $tambahan = " and deptid IN (".$orgaidi.") ";
        }

        if($userid!='undefined') {
            $tambahan = " and id IN (".implode(',',$userid).") ";
        }
        $with_jns_pegawai="";
        if ($jnspeg != null)
        {
            $with_jns_pegawai= " and jenispegawai IN (".implode(',',$jnspeg).") ";
        }

        $with_sts_pegawai="";
        if ($stspeg != null)
        {
            $with_sts_pegawai= " and jftstatus IN (".implode(',',$stspeg).") ";
        }

        $compa = $this->report_model->getcompany();

        $tmbhanFilter=' HAVING jml >= '.intval($compa->row()->batas_alpa_sms);
        $sqlcok = "select *,
        (
            select ifnull(count(*),0) as jml
						from process a 
						where a.userid=userinfo.userid and (a.date_shift >= '".$start_date."' and 
						a.date_shift <= '".$end_date."') and a.attendance in ('ALP','AB_12')
        ) as jml
                        from userinfo
						where userid is not null ".$tambahan.$with_jns_pegawai.$with_sts_pegawai.$tmbhanFilter;
        $querycok = $this->db->query($sqlcok);
        foreach($querycok->result() as $que) 
        {

            $atasan =$this->mdl_sms->getAtasan($que->userid);
            $nip="";$nama="";
            if (count($atasan)>0)
            {
                $nip=$atasan['nip'];
                $nama=$atasan['nama'];
                if ($atasan['nip']==$que->userid)
                {
                    $atasan2 =$this->mdl_sms->getAtasanByDeptId($atasan['deptid']);
                    if (count($atasan2)>0)
                    {
                        $nip=$atasan2['nip'];
                        $nama=$atasan2['nama'];
                    }
                }
            }

            $statussms='';
            if (strlen($que->no_telepon)>7)
            {
                //try send sms blast
            }

            $statusemail='';
            $pattern = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
            if (preg_match($pattern, $que->email) === 1) {
                ///send email blast
            }

            $insrtD = array(
                'userid' => $que->userid,
                'no_telp' => $que->no_telepon,
                'email' => $que->email,
                'userid_atasan' => $nip,
                'nama_atasan' => $nama,
                'pesan' => 'Yth. '.$que->name,
                'state_sms' => $statussms,
                'state_email' =>$statusemail,
                'tgl_1' => $start_date,
                'tgl_2' => $end_date,
                'value' => $que->jml,
                'create_by' => $this->session->userdata('s_username')
            );
            $this->db->insert('sms_masking', $insrtD);

        }
        $data['msg'] = 'Data sudah diproses..';
        $data['status'] = 'succes';
        echo json_encode($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */