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

        $query = $this->pegawai->getDaftar(1,$limited,$offset,null,$SQLcari);
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
            $orgid = $this->session->userdata('s_dept')!=''?$this->pegawai->deptonall(convertToArray($this->session->userdata('s_dept'))):array();
            $namadept = 'Semua Unit Kerja';
        }



        if($userid!='undefined') {
            $pos = strpos($userid, ',');

            if ($pos === false) {
                $with_user_id = "userid ='".$userid."' ";
                $tambahan = "a.userid = '".$userid."' ";
            } else {
                $aUserID = explode ( ',' , $userid);
                $result = "'" . implode ( "', '", $aUserID ) . "'";
                $with_user_id = "userid IN (".$result.") ";
                $tambahan = "a.userid IN (".$result.") ";
            }


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

            $SQLcari .= " and jftstatus in (".implode(',', $s).") ";
        }

        if(!empty($jnspeg)) {
            $s = array();
            foreach($jnspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and jenispegawai in (".implode(',', $s).") ";
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
                                    GROUP BY userid, name, deptname, title, eselon, golru, kelasjabatan, b.id, parentid ORDER BY golru desc, kelasjabatan desc";
        } else {
            $query_att_perdate = "SELECT userid,badgenumber,name, deptname, title,golru, kelasjabatan,pangkat 
                                FROM userinfo b
                                JOIN departments a on a.deptid = b.deptid
                                left join ref_golruang on lower(trim(b.golru))=lower(trim(ref_golruang.ngolru))
                                WHERE ".$with_user_id.$SQLcari."
                                GROUP BY badgenumber,name, deptname, title, eselon, golru, kelasjabatan ORDER BY golru desc, kelasjabatan desc ";
        }
        $group_per_date = $this->db->query($query_att_perdate);
        //echo $this->db->last_query();


        $att = $this->report_model->getattAktif();
        foreach($att->result() as $at) {
            $atar[$at->atid] = $at->atname;
            $attend[] = array(
                'atid'		=> $at->atid,
                'atname'	=> $at->atname
            );
        }
        //$atar["AT_"] = "-";

        $abs = $this->report_model->getabsAktif();
        foreach($abs->result() as $bs) {
            $bbar[$bs->abid] = $bs->abname;
            $absen[] = array(
                'abid'		=> $bs->abid,
                'abname'	=> $bs->abname
            );
        }

        $rlst = $this->report_model->getabsAktif();
        foreach($rlst->result() as $bs) {
            $absCuti[$bs->abid] = $bs->status_kategori_id;
        }

        foreach($group_per_date->result() as $queqe) {
            $this->db->reset_query();
            $querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe->userid);
            $totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
            $holonnwds=0;$totalholiday  = 0;
            $ttlCAP =0;$ttlCBR =0;$ttlCBS =0;$ttlCTLN =0;$ttlCT =0;$ttlDL =0;$ttlTB=0;
            $ttlCS=0;$ttlCB=0;$ttlMeninggal=0;$ttlDPK=0;

            foreach($querytemp->result() as $que) {
                $total++;
                if($que->late!=0) {
                    if($que->workinholiday!=1 || $que->workinholiday!=2) {
                        $totallate++;
                    }
                }

                if($que->early_departure!=0) {
                    $totalearly++;
                }

                if($que->ot_before!=0 || $que->ot_after!=0) {
                    $totalot++;
                }

                if($que->workinholiday==1)
                    $totalholiday++;

                if($que->workinholiday==1 || $que->workinholiday==2) {
                    $adashift = $this->report_model->checkinout($que->date_shift, $queqe->userid);
                    if($adashift->total>0)
                        $workinholiday++;
                }

                if(isset($atar[$que->attendance])) {
                    $attendance++;
                    if (isset($aten[$queqe->userid][$que->attendance]))
                        $aten[$queqe->userid][$que->attendance]++;
                } else if ((isset($que->check_in) || isset($que->check_out )) &&
                    $que->workinholiday!=2 && !array_key_exists($que->attendance, $atar)  &&
                    !array_key_exists($que->attendance, $bbar)
                ) {
                    $attendance++;
                }

                if(isset($bbar[$que->attendance])) {
                    $absence++;
                    if (isset($aben[$queqe->userid][$que->attendance]))
                        $aben[$queqe->userid][$que->attendance]++;

                    if(isset($absCuti[$que->attendance])) {

                        switch ($absCuti[$que->attendance])
                        {
                            //CAP = 2
                                case 2:
                                    $ttlCAP++;
                                    break;
                            //CBR = 7
                            case 7:
                                $ttlCBR++;
                                    break;
                            //CBS = 8
                            case 8:
                                $ttlCBS++;
                                    break;
                            //CTLN = 9
                            case 9:
                                $ttlCTLN++;
                                    break;
                            //CT = 11
                            case 11:
                                $ttlCT++;
                                    break;
                            //DL = 11
                            case 6:
                                $ttlDL++;
                                break;
                            //TB = 11
                            case 4:
                                $ttlTB++;
                                break;
                            //CS
                            case 10:
                                $ttlCS++;
                                break;
                            case 16:
                                $ttlCB++;
                                break;
                            //Melahirkan = CBR
                            case 17:
                                $ttlCBR++;
                                break;

                        }
                    }
                }



                if($que->attendance=='NWDS') {
                    $off++;
                }

                if($que->attendance=='NWK') {
                    $off++;
                }

                if($que->attendance=='BLNK') {
                    $off++;
                }

                if($que->attendance=='' &&$que->workinholiday==2) {
                    $off++;
                }

                if($que->attendance=='ALP') {
                    if($que->workinholiday!=1) $alpha++;
                }

                if($que->attendance=='AB_12') {
                    $alpha++;
                }

                if($que->attendance=='AB_18') {
                    $ttlMeninggal++;
                }
                if($que->attendance=='AB_19') {
                    $ttlDPK++;
                }

                if($que->attendance=='OT') {
                    $totalot++;
                }

                if(!empty($que->edit_come) ) {
                    $editcome++;
                }

                if(!empty($que->edit_home)) {
                    $edithome++;
                }
                if(isset($holar[$que->date_shift]) && ($que->attendance=='NWDS' || $que->attendance=='NWK'))
                    $holonnwds++;

                if(isset($holar[$que->date_shift]) )
                    $ttlCB++;

            }

            $totalbgt = $total - $off - $alpha - $absence;
            $ttlworkingday = $total;
            $workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
            $totalabsent = $alpha + $absence;
            $ttlworkday = $ttlworkingday!=0? $ttlworkingday- $totalholiday-$off:0;
            $ttlOff= $off!=0?$off + $totalholiday - $holonnwds:0;

            $dataarray[] = array (
                'userid'		=> $queqe->userid,
                'golru'			=> $queqe->golru,
                'name'			=> $queqe->name,
                'pangkat'       => $queqe->pangkat,
                'title'         => $queqe->title,
                'workingday'	=> $ttlworkingday!=0 ? $ttlworkingday - $totalholiday + $holonnwds - $off:0,
                'workday'       => $ttlworkday,//$workday!=0?$workday:0,
                'off'			=> $ttlOff,
                'attendance'	=> $attendance!=0?$attendance:0,
                'absence'		=> $absence!=0?$absence:0,
                'absent'		=> $alpha!=0?$alpha:'-',
                'totalabsent'   => $totalabsent!=0?$totalabsent:0,
                'late'			=> $totallate!=0?$totallate:'-',
                'early'			=> $totalearly!=0?$totalearly:'-',
                'OT'			=> $totalot!=0?$totalot:'-',
                'workinholiday'	=> $workinholiday!=0?$workinholiday:'-',
                'editcome'      => $editcome!=''?$editcome:'-',
                'edithome'      => $edithome!=''?$edithome:'-',
                'ttlCAP'      => $ttlCAP,
                'ttlCBR'      => $ttlCBR,
                'ttlCBS'      => $ttlCBS,
                'ttlCTLN'      => $ttlCTLN,
                'ttlCT'      => $ttlCT,
                'ttlDL'     =>$ttlDL,
                'ttlTB'     =>$ttlTB,
                'ttlCS'     =>$ttlCS,
                'ttlCB'     =>$ttlCB,
                'ttlMeninggal'     =>$ttlMeninggal,
                'ttlDPK'     =>$ttlDPK,
            );
        }

        $data = array(
            "cominfo" => $company,
            "periode" => $periode,
            "arr_days" => $arr_days,
            "querycok" => $querycok,
            "group_per_date" => $group_per_date,
            "data" => $dataarray,
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

                //$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
				$stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
				$stylesheet = file_get_contents( $stylepdf);
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

                //$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
				$stylepdf= APPPATH.DIRECTORY_SEPARATOR.'mpdfstyletables.css';
				$stylesheet = file_get_contents( $stylepdf);
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