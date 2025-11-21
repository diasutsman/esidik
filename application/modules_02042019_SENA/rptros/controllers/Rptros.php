<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rptros extends MX_Controller
{
    private $aAkses;

    function Rptros()
    {
        parent::__construct();
        $this->load->helper('utility');
        $this->load->helper('menunavigasi');
        $this->load->library('mypagination');

        $this->load->model('utils_model', 'utils');
        $this->load->model('pegawai/pegawai_model', 'pegawai');
        $this->load->model('report_model');

        if (!$this->auth->is_logged_in()) {
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Rptros", $this->session->userdata('s_access'));
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

        $this->session->set_userdata('menu', '23');
        $uri_segment = 3;
        $offset = 0;
        //$orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        /*$SQLcari =" and jenispegawai in (1,2)";
        $query = $this->pegawai->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->pegawai->getDaftar(0,null,null,null,$SQLcari);*/
        $this_url = site_url('rpttransaksi/pagging/');
        $data2 = $this->mypagination->getPagination(0, 10, $this_url, $uri_segment);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['jum_data'] = 0;
        $data['result'] = array();
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();
        $this->template->load('template', 'display', $data);
    }

    public function pagging($page = 0)
    {
        $priv = $this->pegawai->getprivilege();
        $privi = array();
        foreach ($priv->result() as $privilege) {
            $privi[$privilege->id] = $privilege->privilege;
        }
        $data['priv'] = $privi;

        $cr = $this->input->post('cari');
        $limited = $this->input->post('lmt');
        $org = $this->input->post('org');
        $stspeg = $this->input->post('stspeg');
        $jnspeg = $this->input->post('jnspeg');

        $limited = ((isset($limited) && ($limited != '' || $limited != null)) ? $limited : 10);
        $offset = ((isset($page) && ($page != '' || $page != null)) ? $page : 0);

        $SQLcari = "";
        if ($cr == 'cri') {
            $data['caridata'] = '';
        } else {
            $data['caridata'] = str_replace('%20', ' ', $cr);
            $SQLcari .= " AND ( deptname LIKE '%" . str_replace('%20', ' ', $cr) . "%' or name LIKE '%" . str_replace('%20', ' ', $cr) . "%' 
                        or userid LIKE '%" . str_replace('%20', ' ', $cr) . "%' or badgenumber LIKE '%" . str_replace('%20', ' ', $cr) . "%' ) ";
        }

        if (isset($org) && ($org != '' || $org != null)) {
            $orgid = $this->pegawai->deptonall($org);
        } else {
            $orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        }

        if (!empty($orgid)) {
            $s = array();
            foreach ($orgid as $ar)
                $s[] = "'" . $ar . "'";

            $SQLcari .= " and deptid in (" . implode(',', $s) . ") ";
        }

        if (!empty($stspeg)) {
            $s = array();
            foreach ($stspeg as $ar)
                $s[] = "'" . $ar . "'";

            $SQLcari .= " and jftstatus in (" . implode(',', $s) . ") ";
        }

        if (!empty($jnspeg)) {
            $s = array();
            foreach ($jnspeg as $ar)
                $s[] = "'" . $ar . "'";

            $SQLcari .= " and jenispegawai in (" . implode(',', $s) . ") ";
        }

        $query = $this->pegawai->getDaftar(1, 10, $offset, null, $SQLcari);
        //echo $this->db->last_query();
        $jum_data = $this->pegawai->getDaftar(0, null, null, null, $SQLcari);
        $this_url = site_url('rptros/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(), $limited, $this_url, 3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();

        $this->load->view('list', $data);
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
        $compa = $this->report_model->getcompany();
        $dept = $this->report_model->getdept();
        $excelid = $this->input->post('xls');
        $ispdf = $this->input->post('pdf');
        $stspeg = $this->input->post('stspeg');
        $stspeg = explode(",", $stspeg);
        $jnspeg = $this->input->post('jnspeg');
        $jnspeg = explode(",", $jnspeg);
        $userid = $this->input->post('uid');

        foreach ($dept->result() as $depat) {
            $deptar[$depat->deptid] = $depat->deptname;
        }

        $company = array(
            'companyname' => isset($compa->row()->companyname) ? $compa->row()->companyname : '',
            'logo' => isset($compa->row()->logo) ? $compa->row()->logo : '',
            'address1' => isset($compa->row()->address1) ? $compa->row()->address1 : '',
            'address2' => isset($compa->row()->address2) ? $compa->row()->address2 : '',
            'phone' => isset($compa->row()->phone) ? $compa->row()->phone : '',
            'fax' => isset($compa->row()->fax) ? $compa->row()->fax : ''
        );

        $this->db->select('code_shift, colour_shift');
        $this->db->from('master_shift');
        $shiftcolor = $this->db->get();
        $shiftcol = array();
        foreach ($shiftcolor->result() as $sc)
            $shiftcol[$sc->code_shift] = $sc->colour_shift;

        if ($this->input->post('org') != 'undefined')
            $orgid = $this->pegawai->deptonall($this->input->post('org'));
        else
            $orgid = $this->session->userdata('s_dept') != '' ? $this->pegawai->deptonall(explode(',', $this->session->userdata('s_dept'))) : array();

        $areaid = $this->session->userdata('s_area') != '' ? $this->pegawai->areaonall(explode(',', $this->session->userdata('s_area'))) : array();

        $postdatestart = dmyToymd($this->input->post('start'));
        $postdateend = dmyToymd($this->input->post('end'));
        $datestart = strtotime($postdatestart);
        $dateend = strtotime($postdateend);

        if($userid!='undefined') {
            $userar = explode(",",$this->input->post('uid'));
            $userlist = $this->report_model->getuseremployeedetails($userar,$stspeg,$jnspeg);
        } else if($this->input->get('orgid')!='undefined') {
            $userlist = $this->report_model->getorgemployeedetails($orgid,$stspeg,$jnspeg);
        }

        //$userlist = $this->report_model->getempofdept("", $orgid, $stspeg);
        $data['deptname'] = isset($deptar[$this->input->post('org')]) ? $deptar[$this->input->post('org')] : $deptar['1'];

        $countuserlist = $userlist->num_rows();
        if ($countuserlist != 0) {
            $range = ($dateend - $datestart) / 86400;

            if($userid!='undefined') {
                $roster = $this->report_model->getrosterbyuid($userar, $datestart, $dateend);
                $arrayroster = array();
                foreach ($roster->result() as $rosterdetail) {
                    $arrayroster[$rosterdetail->userid][strtotime($rosterdetail->rosterdate)] = array('absence' => $rosterdetail->absence, 'attendance' => $rosterdetail->attendance);
                }
            } else if($this->input->get('orgid')!='undefined') {
                $roster = $this->report_model->getroster($orgid, $datestart, $dateend);
                $arrayroster = array();
                foreach ($roster->result() as $rosterdetail) {
                    $arrayroster[$rosterdetail->userid][strtotime($rosterdetail->rosterdate)] = array('absence' => $rosterdetail->absence, 'attendance' => $rosterdetail->attendance);
                }
            }

            $holiday = $this->utils->cekholiday($datestart, $dateend);
            $holarray = array();
            foreach ($holiday->result() as $hol) {
                $tglmulai = strtotime($hol->startdate);
                $tglselesai = strtotime($hol->enddate);
                $selisih = $tglselesai - $tglmulai;
                if ($selisih == 0) {
                    $holarray[$hol->deptid][$hol->startdate] = $hol->info;
                } else {
                    $jarak = $selisih / 86400;
                    for ($k = 0; $k <= $jarak; $k++) {
                        $holarray[$hol->deptid][date('Y-m-d', strtotime($hol->startdate) + ($k * 86400))] = $hol->info;
                    }
                }
            }

            $data['holarray'] = $holarray;
            $data['datestart'] = $datestart;
            $data['dateend'] = $dateend;
            $data['empdata'] = $userlist;
            $data['rosterdata'] = $arrayroster;

            $data['cominfo'] = $company;
            $data['shiftcolor'] = $shiftcol;
            $data['excelid'] = $excelid;
            $data['pdfid'] = $ispdf;

            $data['range'] = $range;
            //print_r($range);

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
				$stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
				$stylesheet = file_get_contents( $stylepdf);
                $this->mpdf->WriteHTML($stylesheet,1);
                $datavw = $this->load->view("laporan",$data,true);
                $this->mpdf->SetDisplayMode('fullpage');
                $this->mpdf->WriteHTML($datavw);

                $this->mpdf->Output();
                die();
            }
            if ($excelid == 1) {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=laporanjadwalkerja.xls");
            }
            $this->load->view("laporan", $data);
        }
    }
}
