<?php
ini_set('memory_limit','-1');
ini_set('MAX_EXECUTION_TIME', '-1');

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('report_model');
        $this->load->model('pegawai/pegawai_model','pegawai');
        $this->load->model('jadwal/jadwal_model','jadwal');
        $this->load->model('jadwalkrj/jadwalkrj_model','jadwalkrj');
        $this->load->model('process_model'); 
    }

    public function index()
    {
        redirect('users');
    }	
    
    public function getemailreport()
    {
       $hasil = $this->report_model->getallemailreport();
	   $data_arr = array();
	   foreach($hasil->result() as $data) 
	   {
			$data_arr[] = array(
                'id' => $data->id,
                'report' => $data->report,
                'date_time' => $data->date_time,
                'sendto' => $data->sendto
            );
	   }
	   echo json_encode(array('data'=>$data_arr));
    }
	
	public function getemployeeemail()
    {
       $res = $this->report_model->getemployemail();
	   $dataemplo = array();
	   foreach($res->result() as $datares) 
	   {
			$dataemplo[] = array(
                'userid' => $datares->userid,
                'name' => $datares->name,
                'Email' => $datares->Email,
				'DeptID' => $datares->DeptID
            );
	   }
	   echo json_encode(array('data'=>$dataemplo));
    }
	
	function deptonreport($orgid)
	{
		$depart=array();
		$deptid = $orgid; $i=0;
		$depa = $this->pegawai->getdept($deptid);
		do {	
			$deptid = array();
			foreach($depa->result() as $dep) {						
				$deptid[]=$dep->deptid;
				$depart[]=$dep->deptid;
			}
			$this->pegawai->adachild($deptid)?$i=1:$i=0;
			$depa = $this->pegawai->getdeptparent($deptid);
		} while ($i==1);			
		return $depart;
	}
	
	public function get_allemployee()
	{
		if ($this->auth->is_logged_in()) {
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
			$block1 = str_replace("[", "", $_REQUEST['sort']);
			$block2 = str_replace("]", "", $block1);
			
			$empfilter = isset($_REQUEST['empfilter']) ? $_REQUEST['empfilter'] : 5;
			$postData = '{"datasort":'.$block2.'}';
			$postDataJson = json_decode($postData);
			
			if(isset($_REQUEST['organid']))		
				$orgid = $this->pegawai->deptonall($_REQUEST['organid']);
			else 
				$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			 
			if(isset($_REQUEST['namaemp'])) {
				if($_REQUEST['namaemp']!='')  {
					$emplo = $this->pegawai->getallemployeefind($orgid, $_REQUEST['namaemp'], $postDataJson->datasort->property, $postDataJson->datasort->direction, $empfilter);
				} else  {
					$emplo = $this->pegawai->getallemployee($orgid, $start, $limit, $postDataJson->datasort->property, $postDataJson->datasort->direction, $empfilter);
				}
			} else {
				$emplo = $this->pegawai->getallemployee($orgid, $start, $limit, $postDataJson->datasort->property, $postDataJson->datasort->direction, $empfilter);
			}
			$results = $this->pegawai->getallemployeecount($orgid);
			
			$arremp = array();
			foreach($emplo->result() as $emp) {
				$arremp[] = array (
								'userid' 		=> $emp->userid,
								'badgenumber'	=> $emp->badgenumber,
								'name'			=> $emp->name,
								'deptname'		=> $emp->deptname,
								'deptid'		=> $emp->deptid
							);
			}
			echo '{success:true,results:'. $results .',data:'.json_encode($arremp).'}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function savetranstemp()
	{
		if ($this->auth->is_logged_in()) {
			$postdatestart = $this->input->get('datestart');
			$postdatestop = $this->input->get('dateend');
			$selall = $this->input->get('selall');		
			$sorting = $this->input->get('sorting');		
			$userid = $this->input->get('userid');
			$excelid = $this->input->get('excelid');
			
			if($this->input->get('orgid')!='undefined')		
				$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
			else 
				$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			
			$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			$esen = $this->report_model->gettermid();
			foreach($esen->result() as $es) {
				$esar[$es->sn] = array( 
							'terminal_id'	=> $es->terminal_id,
							'alias'			=> $es->alias
						);
			}
			
			$state = $this->report_model->getstate();
			foreach($state->result() as $st) {
				$star[$st->id] = $st->state;
			}
			
			$att = $this->report_model->getatt();
			foreach($att->result() as $at) {
				$atar[$at->atid] = $at->atname;
			}
			
			$rd = $this->report_model->getrd($postdatestart, $postdatestop);
			foreach($rd->result() as $rosdet) {
				$rdar[$rosdet->rosterdate] = $rosdet->attendance;
			}
			
			$dept = $this->report_model->getdept();
			foreach($dept->result() as $depat) {
				$deptar[$depat->deptid] = $depat->deptname;
			}
			$yo = 0;			
			if($this->input->get('orgid')!='undefined') {	
				$queryemp = $this->report_model->getorgemployeedetails($orgid);
				$yo=1;
			}
			
			if($userid!='undefined') {
				$userar = explode(',', $userid);
				$queryemp = $this->report_model->getuseremployeedetails($userar);
				$yo=2;
			}
			if($selall=='true') {
				$queryemp = $this->report_model->getallemployeedetails();
				$yo=3;
			} 
			$dataallay = array();
			$dataallu = array();
			foreach($queryemp->result() as $queq) {
				$dataallay[] = array(
								'userid'   => $queq->userid,
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''					
						);
				$dataallu[$queq->userid] = array(
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''					
						);
			}
			$compa = $this->report_model->getcompany();
			$company = array(
					'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
					'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
					'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
					'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
					'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
					'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
			);	
			
			$datestart = strtotime($postdatestart);			
			$datestop = strtotime($postdatestop);			
			
			if($sorting=='true') {
				$dataallaye = array();
				$abc = 0;
				$dataview = '';
				$datavw = '';
				$range = ($datestop - $datestart) / 86400;
				for($x=0;$x<=$range;$x++) {
					$tanggal = date('Y-m-d', $datestart + ($x * 86400));			
					if($yo==1) {
						$querytemp = $this->report_model->gettranslogbydateorg($tanggal, $orgid, $areaid);
					} else if($yo==2) {
						$querytemp = $this->report_model->gettranslogbydateuser($tanggal, $userar);	
					} else if($yo==3) {
						$querytemp = $this->report_model->gettranslogbydate($tanggal, $areaid);			
					}
					$dataarray = array();
					foreach($querytemp->result() as $que) {	
						if($que->editby != '') {
							$desc = isset($rdar[date('Y-m-d', strtotime($que->checktime))])?$atar[$rdar[date('Y-m-d', strtotime($que->checktime))]]:'Attendance Status';
						} else {
							$desc = isset($star[$que->checktype])?$star[$que->checktype]:null;
						}
						$dataarray[] = array (
											'userid'		=> $que->userid,
											'badgeNumber'	=> $dataallu[$que->userid]['empID'],
											'name'			=> $dataallu[$que->userid]['empName'],
											'SN'			=> isset($esar[$que->sn]['terminal_id'])?$esar[$que->sn]['terminal_id']:1,
											'alias'			=> isset($esar[$que->sn]['alias'])?$esar[$que->sn]['alias']:'',
											'datelog'		=> date('d-m-Y', strtotime($que->checktime)),
											'timelog'		=> date('H:i:s', strtotime($que->checktime)),
											'functionkey'	=> $que->checktype,
											'description'	=> $desc,
											'verifymode'	=> $que->verifycode,
											'edited'		=> $que->editdate=='0000-00-00 00:00:00'? null : $que->editdate,
											'editby'		=> $que->editby								
						);
					}
					$dataallaye = array(
							'datee'   	=> date('d-M-Y', strtotime($tanggal))				
					);
					$data = array(
						"index" => $abc,
						"cominfo" => $company,
						"empinfo" => $dataallaye,
						"data" => $dataarray
					);		
					$abc++;					
					if($excelid==1) {
						$dataview = $this->load->view("transrep/translogbydate",$data,true);
						$datavw = $datavw.$dataview;
					} else {
						$this->load->view("transrep/translogbydate",$data);
					}					
				}
				if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=transactionlogreport.xls");
					echo "$datavw";
				}
			} else {
				$dataallaye = array();
				$abc = 0;
				$dataview = '';
				$datavw = '';
				foreach($dataallay as $queqe) {
					$querytemp = $this->report_model->gettranslog($datestart, $datestop, $queqe['userid']);
					$dataarray = array();
					foreach($querytemp->result() as $que) {	
						if($que->editby != '') {
							$desc = isset($rdar[date('Y-m-d', strtotime($que->checktime))])?$atar[$rdar[date('Y-m-d', strtotime($que->checktime))]]:'Attendance Status';
						} else {
							$desc = isset($star[$que->checktype])?$star[$que->checktype]:null;
						}
						$dataarray[] = array (
											'SN'			=> isset($esar[$que->sn]['terminal_id'])?$esar[$que->sn]['terminal_id']:1,
											'alias'			=> isset($esar[$que->sn]['alias'])?$esar[$que->sn]['alias']:'',
											'datelog'		=> date('d-m-Y', strtotime($que->checktime)),
											'timelog'		=> date('H:i:s', strtotime($que->checktime)),
											'functionkey'	=> $que->checktype,
											'description'	=> $desc,
											'verifymode'	=> $que->verifycode,
											'edited'		=> $que->editdate=='0000-00-00 00:00:00'? null : $que->editdate,
											'editby'		=> $que->editby								
						);
					}
					$dataallaye = array(
							'userid'   	=> $queqe['userid'],
							'empTitle' 	=> $queqe['empTitle'],
							'empID' 	=> $queqe['empID'],
							'empHire'	=> $queqe['empHire'],
							'empName' 	=> $queqe['empName'],
							'deptName' 	=> $queqe['deptName']					
					);
					$data = array(
						"index" => $abc,
						"cominfo" => $company,
						"empinfo" => $dataallaye,
						"data" => $dataarray
					);		
					$abc++;
					if($excelid==1) {
						$dataview = $this->load->view("transrep/translogall",$data,true);
						$datavw = $datavw.$dataview;
					} else {
						$this->load->view("transrep/translogall",$data);
					}					
				}
				if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=transactionlogreport.xls");
					echo "$datavw";
				}
			}
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function saveatttemp()
	{
		if ($this->auth->is_logged_in()) {
			$postdatestart = $this->input->get('datestart');
			$postdatestop = $this->input->get('dateend');
			$selall = $this->input->get('selall');	
			$sorting = $this->input->get('sorting');		
			$excelid = $this->input->get('excelid');		
			$userid = $this->input->get('userid');
			$datestart = strtotime($postdatestart);			
			$datestop = strtotime($postdatestop);
			
			if($this->input->get('orgid')!='undefined')		
				$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
			else 
				$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			
			$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			$tbar = array();
			$bbar = array();
			$holar = array();
			
			$range = ($datestop - $datestart) / 86400;
			$totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;
			
			$attrecap = $this->report_model->getatt();
			foreach($attrecap->result() as $at) {
				$atar[$at->atid] = $at->atname;
				$attend[] = array(	
							'atid'		=> $at->atid,
							'atname'	=> $at->atname
						);
			}
			$absrecap = $this->report_model->getabs();
			foreach($absrecap->result() as $bs) {
				$bbar[$bs->abid] = $bs->abname;
				$absen[] = array(	
							'abid'		=> $bs->abid,
							'abname'	=> $bs->abname
						);
			}
			
			$holiday = $this->process_model->cekholiday($datestart, $datestop);
			$holarray = array();
			foreach($holiday->result() as $hol) {
				$tglmulai = strtotime($hol->startdate);
				$tglselesai = strtotime($hol->enddate);
				$selisih = $tglselesai - $tglmulai;
				if($selisih==0) {
					$holar[$hol->startdate] = $hol->info;
				} else {
					$jarak = $selisih / 86400;
					for($k=0;$k<=$jarak;$k++) {
						$holar[date('Y-m-d',strtotime($hol->startdate) + ($k*86400))] = $hol->info;
					}
				}					
			}	
			
			$att = $this->report_model->getatt();
			foreach($att->result() as $tt) {
				$tbar[$tt->atid] = $tt->atname;
			}
			
			$dept = $this->report_model->getdept();
			foreach($dept->result() as $depat) {
				$deptar[$depat->deptid] = $depat->deptname;
			}
			
			$abs = $this->report_model->getabs();
			foreach($abs->result() as $bs) {
				$bbar[$bs->abid] = $bs->abname;
			}
			
			$yo = 0;
			if($this->input->get('orgid')!='undefined') {
				$queryemp = $this->report_model->getorgemployeedetails($orgid);
				$yo=1;
			}
			
			if($userid!='undefined') {
				$userar = explode(',', $userid);
				$queryemp = $this->report_model->getuseremployeedetails($userar);
				$yo=2;
			}
			if($selall=='true') {
				$queryemp = $this->report_model->getallemployeedetails();
				$yo=3;
			} 
			
			$aten = array();
			$aben = array();
			
			$dataallay = array();
			$dataallu = array();
			foreach($queryemp->result() as $queq) {
				$dataallay[] = array(
								'userid'   => $queq->userid,
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''					
						);
				$dataallu[$queq->userid] = array(
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''					
						);
				foreach($attend as $at3) {
					$aten[$queq->userid][$at3['atid']] = 0;
				}
				foreach($absen as $ab3) {
					$aben[$queq->userid][$ab3['abid']] = 0;
				}
			}
			$compa = $this->report_model->getcompany();
			$company = array(
					'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
					'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
					'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
					'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
					'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
					'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
			);				
			
			if($sorting=='true') {
				$dataallaye = array();
				$abc = 0;
				$dataview = '';
				$datavw = '';
				$range = ($datestop - $datestart) / 86400;
				for($x=0;$x<=$range;$x++) {
					$totalholiday  = 0;
					$tanggal = date('Y-m-d', $datestart + ($x * 86400));	
					$day = date('D', strtotime($tanggal));				
					if($yo==1) {
						$querytemp = $this->report_model->getattbydateorg($areaid, $tanggal, $orgid);
					} else if($yo==2) {
						$querytemp = $this->report_model->getattbydateuser($tanggal, $userar);	
					} else if($yo==3) {
						$querytemp = $this->report_model->getattbydate($areaid, $tanggal);			
					}
					$dataarray = array();
					foreach($querytemp->result() as $que) {	
						$in = strtotime($que->date_in.' '.$que->check_in);
						$out = strtotime($que->date_out.' '.$que->check_out);
						$bout = strtotime($que->date_in.' '.$que->break_out);
						$bin = strtotime($que->date_in.' '.$que->break_in);
						
						if(!isset($que->break_out) || !isset($que->break_in)) {
							$btot = 0;
						} else {
							$btot= $bin-$bout;
						}
						
						$tot=($out-$in)-$btot;
						$totalhour = $this->report_model->itungan($tot);					
						
						$date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
						if(isset($que->date_in)) { $date_in = date('d-m-Y', strtotime($que->date_in)); } else { $totalhour = ''; }
						if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
						if(isset($que->break_out)) $break_out = date('H:i:s', strtotime($date_in.' '.$que->break_out));
						if(isset($que->break_in)) $break_in = date('H:i:s', strtotime($date_in.' '.$que->break_in));
						if(isset($que->date_out)) { $date_out = date('d-m-Y', strtotime($que->date_out)); } else { $totalhour = ''; }
						if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));					
						
						if($que->late!=0) {
							$late = $this->report_model->itungan($que->late);
						} else {
							$late = '';
						}
						
						if($que->early_departure!=0) {
							$earlydept = $this->report_model->itungan($que->early_departure);
						} else {
							$earlydept='';
						}
						
						if($que->ot_before!=0) {
							$otbef = $this->report_model->itungan($que->ot_before);
						} else {
							$otbef = '';
						}
						
						if($que->ot_after!=0) {
							$otaf = $this->report_model->itungan($que->ot_after);
						} else {
							$otaf = '';
						}
						
						$workinghour = date('H:i', strtotime($que->shift_in)).' - '.date('H:i', strtotime($que->shift_out));
						
						if($que->workinholiday==1) {
							//$activity = $this->lang->line('work');
							$activity = $this->lang->line('holiday');
							$notes = $holar[$que->date_shift];	
							$totalholiday++;
							$late = '';
							$early = '';
							
							if($que->attendance=='OFF') {
								$activity = $this->lang->line('work');
								$totalhour = '';
								$workinghour = '';
							}
							
							if($que->attendance=='BLNK') {
								$activity = '';
								$totalhour = '';
								$workinghour = '';
							}
							
							if($que->attendance=='ALP') {
								$activity = $this->lang->line('holiday');
								$totalhour = '';
							}
							
							if($que->attendance=='NWK') {
								$activity = $this->lang->line('holiday');
								$totalhour = '';
							}
							
							if($que->attendance=='OT') {
								$activity = $this->lang->line('overtime');
								$otaf = $totalhour;
							}		

							if(isset($tbar[$que->attendance])) {
								$activity = $this->lang->line('work');
								$notes = $que->notes!=''?$que->notes:$tbar[$que->attendance];
							} else if(isset($bbar[$que->attendance])) {
								$activity = '';
								$notes = $que->notes!=''?$que->notes:$bbar[$que->attendance];
							}							
						} else if ($que->workinholiday==2){
							$activity = $this->lang->line('work');
							$notes = $this->lang->line('nwds');				
							$late = '';
							$early = '';
							
							if($que->attendance=='OFF') {
								$totalhour = '';
								$workinghour = '';
							}
							if($que->attendance=='BLNK') {
								$activity = '';
								$totalhour = '';
								$workinghour = '';
							}						
							if($que->attendance=='ALP') {
								$activity = '';
								$totalhour = '';
							}						
							
							if($que->attendance=='NWK') {
								$totalhour = '';
							}
							
							if($que->attendance=='NWDS') {
								$totalhour = '';
								$late = '';
								$earlydept = '';
							}
							
							if($que->attendance=='OT') {
								$activity = $this->lang->line('overtime');	
								$otaf = $totalhour;
							}	
							
							if(isset($tbar[$que->attendance])) {
								$activity = $this->lang->line('work');	
								$notes = $que->notes!=''?$que->notes:$tbar[$que->attendance];
							} else if(isset($bbar[$que->attendance])) {
								$activity = '';
								$notes = $que->notes!=''?$que->notes:$bbar[$que->attendance];
							}
						} else {
							$notes = '';
							if(isset($tbar[$que->attendance])) {
								$activity = $this->lang->line('work');	
								$notes = $que->notes!=''?$que->notes:$tbar[$que->attendance];
							} else if(isset($bbar[$que->attendance])) {
								$activity = '';
								$notes = $que->notes!=''?$que->notes:$bbar[$que->attendance];
								
								$date_in = '';
								$check_in = '';
								$break_out = '';
								$break_in = '';
								$date_out = '';
								$check_out = '';
								$late = '';
								$earlydept = '';
								$otbef = '';
								$otaf = '';
								$totalhour = '';
							} else {
								$activity = $this->lang->line('work');	
							}
							
							if($que->attendance=='OFF') {
								$activity = $this->lang->line('off');	
								$totalhour = '';
								$workinghour = '';
							}
							
							if($que->attendance=='BLNK') {
								$activity = '';
								$totalhour = '';
								$workinghour = '';
							}
							
							if($que->attendance=='NWDS') {
								$activity = $this->lang->line('off');
								$totalhour = '';
							}
							
							if($que->attendance=='NWK') {
								$activity = $this->lang->line('off');
								$totalhour = '';
							}
							
							if($que->attendance=='ALP') {
								$activity = $this->lang->line('absent');
								$totalhour = '';
							}
							
							if($que->attendance=='OT') {
								$activity = $this->lang->line('overtime');
								$otaf = $totalhour;
							}
						}												
						
						$dataarray[] = array (
											'userid'		=> $que->userid,
											'badgeNumber'	=> $que->badgenumber,
											'name'			=> $que->name,
											'dept'			=> $deptar[$que->deptid],
											'workinghour'	=> $workinghour,
											'activity'		=> $activity,
											'datein'		=> $date_in,
											'dutyon'		=> $check_in,
											'breakout'		=> $break_out,
											'breakin'		=> $break_in,
											'dateout'		=> $date_out,
											'dutyoff'		=> $check_out,
											'latein'		=> $late,
											'earlydept'		=> $earlydept,
											'otbef'			=> $otbef,
											'otaf'			=> $otaf,
											'totalhour'		=> $totalhour,
											'notes'			=> $notes
										);								
					}
					$dataallaye = array(
							'day'   	=> $day,
							'date' 		=> date('d-M-Y', strtotime($tanggal))				
					);
					
					$data = array(
						"dateinfo" => $this->input->get('dateinfo'),
						"index" => $abc,
						"cominfo" => $company,
						"empinfo" => $dataallaye,
						"data" => $dataarray
					);		
					$abc++;
					if($excelid==1) {
						$dataview = $this->load->view("attrep/attrepbydate",$data,true);
						$datavw = $datavw.$dataview;
					} else {
						$this->load->view("attrep/attrepbydate",$data);
					}					
				}
				if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=attendancereport.xls");
					echo "$datavw";
				}
			} else {
				$dataallaye = array();
				$datafoot = array();
				$abc=0; 
				$dataview = '';
				$datavw = '';
				foreach($dataallay as $queqe) {
					$querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
					$dataarray = array();
					$totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
					$totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0;
                    $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
					$totalholiday  = 0;
					foreach($querytemp->result() as $que) {
						$totalr++;							
						if($que->late!=0) {
							$totallater++;
						}
					
						if($que->early_departure!=0) {
							$totalearlyr++;
						}
					
						if($que->ot_before!=0 || $que->ot_after!=0) {
							$totalotr++;
						}
					
						if($que->workinholiday==1 || $que->workinholiday==2) {
							$adashift = $this->report_model->checkinout($que->date_shift, $queqe['userid']);
							if($adashift->total>0) 
								$workinholiday++;
						}
					
						if(isset($atar[$que->attendance])) {
							$attendance++;	
							$aten[$queqe['userid']][$que->attendance]++;					
						} 
					
						if(isset($bbar[$que->attendance])) {
							$absence++;
							$aben[$queqe['userid']][$que->attendance]++;							
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
							
						if($que->attendance=='ALP') {
							if($que->workinholiday!=1) $alpha++;                    
						}
					
						if($que->attendance=='OT') {
							$totalotr++;
						}
					
						if(!empty($que->edit_come) ) {
							$editcome++;							
						}
					
						if(!empty($que->edit_home)) {
							$edithome++;
						}
						
						$day = date('D', strtotime($que->date_shift));
						
						$in = strtotime($que->date_in.' '.$que->check_in);
						$out = strtotime($que->date_out.' '.$que->check_out);
						$bout = strtotime($que->date_in.' '.$que->break_out);
						$bin = strtotime($que->date_in.' '.$que->break_in);
						
						if(!isset($que->break_out) || !isset($que->break_in)) {
							$btot = 0;
						} else {
							$btot= $bin-$bout;
						}
						
						$tot=($out-$in)-$btot;
						$totalhour = $this->report_model->itungan($tot);					
						
						$date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
						if(isset($que->date_in)) { $date_in = date('d-m-Y', strtotime($que->date_in)); } else { $totalhour = ''; $tot = 0; }
						if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
						if(isset($que->break_out)) $break_out = date('H:i:s', strtotime($date_in.' '.$que->break_out));
						if(isset($que->break_in)) $break_in = date('H:i:s', strtotime($date_in.' '.$que->break_in));
						if(isset($que->date_out)) { $date_out = date('d-m-Y', strtotime($que->date_out)); } else { $totalhour = ''; $tot = 0; }
						if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));					
						
						if($que->late!=0) {
							$late = $this->report_model->itungan($que->late);
							$totallate = $totallate + $que->late;
						} else {
							$late = '';
						}
						
						if($que->early_departure!=0) {
							$earlydept = $this->report_model->itungan($que->early_departure);
							$totalearly = $totalearly + $que->early_departure;
						} else {
							$earlydept='';
						}
						
						if($que->ot_before!=0) {
							$otbef = $this->report_model->itungan($que->ot_before);
							$totalotbef = $totalotbef + $que->ot_before;
						} else {
							$otbef = '';
						}
						
						if($que->ot_after!=0) {
							$otaf = $this->report_model->itungan($que->ot_after);
							$totalotaf = $totalotaf + $que->ot_after;
						} else {
							$otaf = '';
						}
						
						$workinghour = date('H:i', strtotime($que->shift_in)).' - '.date('H:i', strtotime($que->shift_out));
						
						if($que->workinholiday==1) {
							$activity = $this->lang->line('holiday');
							//$activity = $this->lang->line('work');
							$notes = $holar[$que->date_shift];			
							$totalholiday++;
							if($totallate!=0)
								$totallate = $totallate - $que->late;
							if($totallater!=0)
								$totallater--;
							$late = '';
							$early = '';
							
							if($que->attendance=='OFF') {
								$activity = $this->lang->line('off');
								$totalhour = '';
								$tot = 0;
								$workinghour = '';
							}
							
							if($que->attendance=='BLNK') {
								$activity = '';
								$totalhour = '';
								$tot = 0;
								$workinghour = '';
							}
							
							if($que->attendance=='NWK') {
								$activity = $this->lang->line('holiday');
								$totalhour = '';
							}
							
							if($que->attendance=='ALP') {
								$activity = $this->lang->line('holiday');
								$totalhour = '';
								$tot = 0;
							}
							
							if($que->attendance=='OT') {
								$activity = $this->lang->line('Overtime');
								$otaf = $totalhour;
							}			
							
							if(isset($tbar[$que->attendance])) {
								$activity = $this->lang->line('work');
								$notes = $que->notes!=''?$que->notes:$tbar[$que->attendance];
							} else if(isset($bbar[$que->attendance])) {
								$activity = '';
								$notes = $que->notes!=''?$que->notes:$bbar[$que->attendance];
							}							
						} else if ($que->workinholiday==2) {
							$activity = $this->lang->line('work');
							$notes = $this->lang->line('nwds');
							if($totallate!=0)
								$totallate = $totallate - $que->late;
							if($totallater!=0)
								$totallater--;
							$late = '';
							$early = '';
							
							if($que->attendance=='OFF') {
								//$activity = 'Off';
								$totalhour = '';
								$tot = 0;
								$workinghour = '';
							}
							if($que->attendance=='BLNK') {
								$activity = '';
								$totalhour = '';
								$tot = 0;
								$workinghour = '';
							}
							
							if($que->attendance=='ALP') {
								$activity = '';
								$totalhour = '';
								$tot = 0;
							}
							
							if($que->attendance=='NWK') {
								//$activity = 'Off';
								$totalhour = '';
								$tot = 0;
							}
							
							if($que->attendance=='NWDS') {
								//$activity = 'Off';								
								$late = '';
								$earlydept = '';
								$otaf = $totalhour;
							}
							
							if($que->attendance=='OT') {
								$activity = $this->lang->line('overtime');
								$otaf = $totalhour;
							}	
							
							if(isset($tbar[$que->attendance])) {
								$activity = $this->lang->line('work');
								$notes = $que->notes!=''?$que->notes:$tbar[$que->attendance];
							} else if(isset($bbar[$que->attendance])) {
								$activity = '';
								$notes = $que->notes!=''?$que->notes:$bbar[$que->attendance];
							}
							
						} else {
							$notes = '';
							if(isset($tbar[$que->attendance])) {
								$activity = $this->lang->line('work');
								$notes = $que->notes!=''?$que->notes:$tbar[$que->attendance];
							} else if(isset($bbar[$que->attendance])) {
								$activity = '';					
								$notes = $que->notes!=''?$que->notes:$bbar[$que->attendance];
								
								$date_in = '';
								$check_in = '';
								$break_out = '';
								$break_in = '';
								$date_out = '';
								$check_out = '';
								$late = '';
								$earlydept = '';
								$otbef = '';
								$otaf = '';
								$totalhour = '';
								$tot = 0;
							} else {
								$activity = $this->lang->line('work');
							}
							
							if($que->attendance=='OFF') {
								$activity = $this->lang->line('off');
								$totalhour = '';
								$tot = 0;
								$workinghour = '';
							}
							
							if($que->attendance=='BLNK') {
								$activity = '';
								$totalhour = '';
								$tot = 0;
								$workinghour = '';
							}
							
							if($que->attendance=='NWDS') {
								$activity = $this->lang->line('off');
								$totalhour = '';
								$tot = 0;
							}
							
							if($que->attendance=='NWK') {
								$activity = $this->lang->line('off');
								$totalhour = '';
								$tot = 0;
							}
							
							if($que->attendance=='ALP') {
								$activity = $this->lang->line('absent');
								$totalhour = '';
								//$notes = 'Alpa';
								$tot = 0;
							}
							
							if($que->attendance=='OT') {
								$activity = $this->lang->line('overtime');
								$otaf = $totalhour;
							}
						}												
						
						$dataarray[] = array (
											'day'			=> $day,
											'date'			=> date('d-m-Y', strtotime($que->date_shift)),
											'workinghour'	=> $workinghour,
											'activity'		=> $activity,
											'datein'		=> $date_in,
											'dutyon'		=> $check_in,
											'breakout'		=> $break_out,
											'breakin'		=> $break_in,
											'dateout'		=> $date_out,
											'dutyoff'		=> $check_out,
											'latein'		=> $late,
											'earlydept'		=> $earlydept,
											'otbef'			=> $otbef,
											'otaf'			=> $otaf,
											'totalhour'		=> $totalhour,
											'notes'			=> $notes
										);						
						$total = $total + $tot;
						if(isset($holar[$que->date_shift])  && ($que->attendance=='NWDS' || $que->attendance=='NWK'))
							$holonnwds++;
					}
					
					$totalbgt = $totalr - $off - $alpha - $absence;
					$ttlworkingday = $totalr;
					$workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
					$totalabsent = $alpha + $absence;
					
					$dataallaye = array(
							'userid'   	=> $queqe['userid'],
							'empTitle' 	=> $queqe['empTitle'],
							'empID' 	=> $queqe['empID'],
							'empHire'	=> $queqe['empHire'],
							'empName' 	=> $queqe['empName'],
							'deptName' 	=> $queqe['deptName']					
					);
					
					$datafoot = array(
							'totallate'   	=> $this->report_model->itungan($totallate),
							'totalearly' 	=> $this->report_model->itungan($totalearly),
							'totalotbef' 	=> $this->report_model->itungan($totalotbef),
							'totalotaf'		=> $this->report_model->itungan($totalotaf),
							'total' 		=> $this->report_model->itungan($total)				
					);
					$datarecap = array (
									'userid'		=> $queqe['userid'],
									'holiday'		=> $totalholiday,
									'workingday'	=> $ttlworkingday!=0?($ttlworkingday - $totalholiday + $holonnwds - $off):'0',
									'workday'       => $workday!=0?$workday:'0',
									'off'			=> $off!=0?($off + $totalholiday) - $holonnwds:'-',
									'attendance'	=> $attendance!=0?$attendance:'0',
									'aten'			=> $aten,
									'absence'		=> $absence!=0?$absence:'0',
									'aben'			=> $aben,
									'absent'		=> $alpha!=0?$alpha:'-',
									'totalabsent'   => $totalabsent!=0?$totalabsent:'0',
									'late'			=> $totallater!=0?$totallater:'-',
									'early'			=> $totalearlyr!=0?$totalearlyr:'-',
									'OT'			=> $totalotr!=0?$totalotr:'-',
									'workinholiday'	=> $workinholiday!=0?$workinholiday:'-'
					);
					
					$data = array(
						"dateinfo" => $this->input->get('dateinfo'),
						"index" => $abc,
						"cominfo" => $company,
						"empinfo" => $dataallaye,
						"footah" => $datafoot,
						"att"	=> $attend,
						"abs"	=> $absen,
						"rekap" => $datarecap,
						"data" => $dataarray
					);		
					$abc++;
					
					if($excelid==1) {
						$dataview = $this->load->view("attrep/attrepall",$data,true);
						$datavw = $datavw.$dataview;
					} else {
						$this->load->view("attrep/attrepall",$data);
					}					
				}
				if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=attendancereport.xls");
					echo "$datavw";				
				}
			}
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function savesplit()
	{
		if ($this->auth->is_logged_in()) {
			$postdatestart = $this->input->get('datestart');
			$postdatestop = $this->input->get('dateend');
			$selall = $this->input->get('selall');		
			$orgid = $this->input->get('orgid');		
			$excelid = $this->input->get('excelid');		
			$userid = $this->input->get('userid');
			$areaid = $this->device_model->areaonall($this->device_model->getUserArea());
			$datestart = strtotime($postdatestart);			
			$datestop = strtotime($postdatestop);			
			
			$yo = 0;
			if($orgid!='undefined') {
				$queryemp = $this->report_model->getorgemployeedetails($this->deptonreport($orgid));
				$depart = $this->deptonreport($orgid);
				$yo=1;
			}
			
			if($userid!='undefined') {
				$userar = explode(',', $userid);
				$queryemp = $this->report_model->getuseremployeedetails($userar);
				$yo=2;
			}
			if($selall=='true') {
				$queryemp = $this->report_model->getallemployeedetails();
				$yo=3;
			} 
			
			$dataallay = array();
			$dataallu = array();
			foreach($queryemp->result() as $queq) {
				$dataallay[] = array(
								'userid'   => $queq->userid,
								'empTitle' => $queq->Title,
								'empID' => $queq->badgeNumber,
								'empHire' => isset($queq->hiredDate)?date('d-m-Y', strtotime($queq->hiredDate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->DeptID])?$deptar[$queq->DeptID]:''					
						);
				$dataallu[$queq->userid] = array(
								'empTitle' => $queq->Title,
								'empID' => $queq->badgeNumber,
								'empHire' => isset($queq->hiredDate)?date('d-m-Y', strtotime($queq->hiredDate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->DeptID])?$deptar[$queq->DeptID]:''					
						);
			}
			$compa = $this->report_model->getcompany();
			$company = array(
					'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
					'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
					'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
					'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
					'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
					'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
			);				
			
			$dataallaye = array();
			$abc=0; 
			$dataview = '';
			$datavw = '';
			foreach($dataallay as $queqe) {
				$querytemp = $this->report_model->getsplit($datestart, $datestop, $queqe['userid']);
				$dataarray = array();
				$totall1=0; $totall2=0; $totall=0;
				foreach($querytemp->result() as $que) {
					$total1 = ($que->shift_out1!=0&&$que->shift_in1!=0)?$que->shift_out1 - $que->shift_in1:0;
					$totall1 = $totall1 + $total1;
					$total2 = ($que->shift_out2!=0&&$que->shift_in2!=0)?$que->shift_out2 - $que->shift_in2:0;
					$totall2 = $totall2 + $total2;
					$total = $total1 + $total2;
					$totall = $totall + $total;
					$dataarray[] = array (
						'date'			=> date('d-m-Y', strtotime($que->date_shift)),
						'shift_in1'		=> $que->shift_in1!=0?date('H:i:s', $que->shift_in1):'',
						'shift_out1'	=> $que->shift_out1!=0?date('H:i:s', $que->shift_out1):'',
						'total1'		=> $total1!=0?$this->report_model->itungan($total1):'',
						'shift_in2'		=> $que->shift_in2!=0?date('H:i:s', $que->shift_in2):'',
						'shift_out2'	=> $que->shift_out2!=0?date('H:i:s', $que->shift_out2):'',
						'total2'		=> $total2!=0?$this->report_model->itungan($total2):'',
						'total'			=> $total!=0?$this->report_model->itungan($total):''
					);						
				}
					
				$dataallaye = array(
						'userid'   	=> $queqe['userid'],
						'empTitle' 	=> $queqe['empTitle'],
						'empID' 	=> $queqe['empID'],
						'empHire'	=> $queqe['empHire'],
						'empName' 	=> $queqe['empName'],
						'deptName' 	=> $queqe['deptName']					
				);
				
				$datafoot = array(
							'totall1'   	=> $this->report_model->itungan($totall1),
							'totall2' 		=> $this->report_model->itungan($totall2),
							'totall' 		=> $this->report_model->itungan($totall)		
				);	
					
				$data = array(
					"dateinfo" => $this->input->get('dateinfo'),
					"index" => $abc,
					"cominfo" => $company,
					"empinfo" => $dataallaye,
					"footah" => $datafoot,
					"data" => $dataarray
				);		
				$abc++;
				
				if($excelid==1) {
					$dataview = $this->load->view("attrep/split",$data,true);
					$datavw = $datavw.$dataview;
				} else {
					$this->load->view("attrep/split",$data);
				}					
			}	
			if($excelid==1) {
				header("Content-type: application/x-msdownload");
				header("Content-Disposition: attachment; filename=splitreport.xls");
				echo "$datavw";				
			}
			
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function saverecaptemp()
	{
		if ($this->auth->is_logged_in()) {
			$postdatestart = $this->input->get('datestart');
			$postdatestop = $this->input->get('dateend');
			$selall = $this->input->get('selall');	
			$userid = $this->input->get('userid');
			$excelid = $this->input->get('excelid');
			$datestart = strtotime($postdatestart);			
			$datestop = strtotime($postdatestop);
			
			if($this->input->get('orgid')!='undefined')		
				$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
			else 
				$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			
			$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			$range = ($datestop - $datestart) / 86400;
			$totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;
			
			$att = $this->report_model->getatt();
			foreach($att->result() as $at) {
				$atar[$at->atid] = $at->atname;
				$attend[] = array(	
							'atid'		=> $at->atid,
							'atname'	=> $at->atname
						);
			}
			$abs = $this->report_model->getabs();
			foreach($abs->result() as $bs) {
				$bbar[$bs->abid] = $bs->abname;
				$absen[] = array(	
							'abid'		=> $bs->abid,
							'abname'	=> $bs->abname
						);
			}
			
			$dept = $this->report_model->getdept();
			foreach($dept->result() as $depat) {
				$deptar[$depat->deptid] = $depat->deptname;
			}
			
			$compa = $this->report_model->getcompany();
			$company = array(
					'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
					'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
					'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
					'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
					'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
					'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
			);	
			
			if($selall == 'true'){				
				$deptGroupid = $this->report_model->getdepart();							  
			}
			
			$yo = 0;
			$dpt = 1;
			if($this->input->get('orgid')!='undefined') {
				$queryemp = $this->report_model->getorgemployeedetails($orgid);
				$dpt = $this->input->get('orgid');
				$yo=1;
			}
			
			if($userid!='undefined') {
				$userar = explode(',', $userid);
				$queryemp = $this->report_model->getuseremployeedetails($userar);
				$yo=2;
			}
			if($selall=='true') {
				$queryemp = $this->report_model->getallemployeedetails();
				$yo=3;
			} 
			
			$holiday = $this->process_model->cekholiday($datestart, $datestop);
			$holarray = array();
			foreach($holiday->result() as $hol) {
				$tglmulai = strtotime($hol->startdate);
				$tglselesai = strtotime($hol->enddate);
				$selisih = $tglselesai - $tglmulai;
				if($selisih==0) {
					$holar[$hol->startdate] = $hol->info;
				} else {
					$jarak = $selisih / 86400;
					for($k=0;$k<=$jarak;$k++) {
						$holar[date('Y-m-d',strtotime($hol->startdate) + ($k*86400))] = $hol->info;
					}
				}					
			}	
			
			$dataallay = array();
			$dataallu = array();
			$aten = array();
			$aben = array();
			foreach($queryemp->result() as $queq) {
				$dataallay[] = array(
								'userid'   => $queq->userid,
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'empEselon' => $queq->eselon,
								'empGolru' => $queq->golru,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''					
						);
				$dataallu[$queq->userid] = array(
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'empEselon' => $queq->eselon,
								'empGolru' => $queq->golru,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''					
						);
				foreach($attend as $at3) {
					$aten[$queq->userid][$at3['atid']] = 0;
				}
				foreach($absen as $ab3) {
					$aben[$queq->userid][$ab3['abid']] = 0;
				}
			}	
			
			$dataallaye = array();
			$dataarray = array();
			if($selall != 'true'){
				foreach($dataallay as $queqe) {
					$querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);			
					$totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
					$holonnwds=0;$totalholiday  = 0;
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
							$adashift = $this->report_model->checkinout($que->date_shift, $queqe['userid']);
							if($adashift->total>0) 
								$workinholiday++;
						}
					
						if(isset($atar[$que->attendance])) {
							$attendance++;	
							$aten[$queqe['userid']][$que->attendance]++;					
						} 
					
						if(isset($bbar[$que->attendance])) {
							$absence++;
							$aben[$queqe['userid']][$que->attendance]++;							
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
							
						if($que->attendance=='ALP') {
							if($que->workinholiday!=1) $alpha++;                    
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
					}
				
					$totalbgt = $total - $off - $alpha - $absence;
					$ttlworkingday = $total;
					$workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
					$totalabsent = $alpha + $absence;
					$dataarray[] = array (
									'userid'		=> $queqe['userid'],
									'badgeNumber'	=> $queqe['empID'],
									'eselon'		=> $queqe['empEselon'],
									'golru'			=> $queqe['empGolru'],
									'name'			=> $queqe['empName'],
									'workingday'	=> $ttlworkingday!=0?$ttlworkingday - $totalholiday + $holonnwds - $off:'0',
									'workday'       => $workday!=0?$workday:'0',
									'off'			=> $off!=0?$off + $totalholiday - $holonnwds:'-',
									'attendance'	=> $attendance!=0?$attendance:'0',
									'aten'			=> $aten,
									'absence'		=> $absence!=0?$absence:'0',
									'aben'			=> $aben,
									'absent'		=> $alpha!=0?$alpha:'-',
									'totalabsent'   => $totalabsent!=0?$totalabsent:'0',
									'late'			=> $totallate!=0?$totallate:'-',
									'early'			=> $totalearly!=0?$totalearly:'-',
									'OT'			=> $totalot!=0?$totalot:'-',
									'workinholiday'	=> $workinholiday!=0?$workinholiday:'-',
									'editcome'      => $editcome!=''?$editcome:'-',
									'edithome'      => $edithome!=''?$edithome:'-'
					);
				}
				$dataallaye = array(
					'dept'   		=> isset($deptar[$dpt])?$deptar[$dpt]:'',
					'holidays' 		=> $totalholiday,
					'datestart' 	=> date('d-M-Y', $datestart),
					'datestop' 		=> date('d-M-Y', $datestop)
				);
			} else {        
				foreach($dataallay as $queqe) {
					$querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);			
					$totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
					$holonnwds=0;$totalholiday  = 0;
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
							$adashift = $this->report_model->checkinout($que->date_shift, $queqe['userid']);
							if($adashift->total>0) 
								$workinholiday++;
						}
						
						if(isset($atar[$que->attendance])) {
							$attendance++;	
							$aten[$queqe['userid']][$que->attendance]++;					
						} 
						
						if(isset($bbar[$que->attendance])) {
							$absence++;
							$aben[$queqe['userid']][$que->attendance]++;
							
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
								
						if($que->attendance=='ALP') {
							if($que->workinholiday!=1) $alpha++;                     
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
						if(isset($holar[$que->date_shift])  && ($que->attendance=='NWDS' || $que->attendance=='NWK'))
							$holonnwds++;
					}
					
					$totalbgt = $total - $off - $alpha - $absence;
					$ttlworkingday = $total;
					$workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
					$totalabsent = $alpha + $absence;
					
					$dataarray[] = array (
								'userid'		=> $queqe['userid'],
								'badgeNumber'	=> $queqe['empID'],
								'eselon'		=> $queqe['empEselon'],
								'golru'			=> $queqe['empGolru'],
								'name'			=> $queqe['empName'],
								'DeptID'        => $queqe['deptName'],
								'workingday'	=> $ttlworkingday!=0?$ttlworkingday - $totalholiday + $holonnwds - $off:'0',
								'workday'       => $workday!=0?$workday:'0',
								'off'			=> $off!=0?$off + $totalholiday - $holonnwds:'-',
								'attendance'	=> $attendance!=0?$attendance:'0',
								'aten'			=> $aten,
								'absence'		=> $absence!=0?$absence:'0',
								'aben'			=> $aben,
								'absent'		=> $alpha!=0?$alpha:'-',
								'totalabsent'   => $totalabsent!=0?$totalabsent:'0',
								'late'			=> $totallate!=0?$totallate:'-',
								'early'			=> $totalearly!=0?$totalearly:'-',
								'OT'			=> $totalot!=0?$totalot:'-',
								'workinholiday'	=> $workinholiday!=0?$workinholiday:'-',
								'editcome'      => $editcome!=''?$editcome:'-',
								'edithome'      => $edithome!=''?$edithome:'-'
					);					
				}
				
				$dataallaye = array(
					'dept'   		=> 'All',
					'holidays' 		=> $totalholiday,
					'datestart' 	=> date('d-M-Y', $datestart),
					'datestop' 		=> date('d-M-Y', $datestop)
				);
			
			}
					
			$data = array(
				"cominfo" 	=> $company,
				"empinfo" 	=> $dataallaye,
				"att"		=> $attend,
				"abs"		=> $absen,
				"data" 		=> $dataarray
			);			
			
			if($selall != 'true') {
				if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=recapitulationreport.xls");			
				}				
				$this->load->view("recap/recapview",$data);
			} else {	
				$dataOrganization = array(
					"cominfo"        => $company,
					"empinfo"        => $dataallaye,
					"att"            => $attend,
					"abs"            => $absen,
					"companyLooping" => $deptGroupid,
					"data"           => $dataarray
				);
				if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=recapitulationreport.xls");			
				}
				$this->load->view("recap/recapvieworg",$dataOrganization);
			}
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function saverecaptempdept()
	{
		if ($this->auth->is_logged_in()) {
			$postdatestart = $this->input->get('datestart');
			$postdatestop = $this->input->get('dateend');
			$selall = $this->input->get('selall');	
			$userid = $this->input->get('userid');
			$excelid = $this->input->get('excelid');
			$datestart = strtotime($postdatestart);			
			$datestop = strtotime($postdatestop);
			
			if($this->input->get('orgid')!='')		{
				$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
			} else {
				$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			}
			$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			$range = ($datestop - $datestart) / 86400;
			$totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;
			
			$att = $this->report_model->getatt();
			foreach($att->result() as $at) {
				$atar[$at->atid] = $at->atname;
				$attend[] = array(	
							'atid'		=> $at->atid,
							'atname'	=> $at->atname
						);
			}
			$abs = $this->report_model->getabs();
			foreach($abs->result() as $bs) {
				$bbar[$bs->abid] = $bs->abname;
				$absen[] = array(	
							'abid'		=> $bs->abid,
							'abname'	=> $bs->abname
						);
			}
			
			$dept = $this->report_model->getdept();
			foreach($dept->result() as $depat) {
				$deptar[$depat->deptid] = $depat->deptname;
			}
			
			$compa = $this->report_model->getcompany();
			$company = array(
					'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
					'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
					'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
					'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
					'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
					'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
			);	
			
			if($selall == 'true'){				
				$deptGroupid = $this->report_model->getdepart();							  
			}
			
			$yo = 0;
			$dpt = 1;
			if($this->input->get('orgid')!='undefined') {
				$queryemp = $this->report_model->getorgemployeedetails($orgid);
				$dpt = $this->input->get('orgid');
				$yo=1;
			}
			
			if($userid!='undefined') {
				$userar = explode(',', $userid);
				$queryemp = $this->report_model->getuseremployeedetails($userar);
				$yo=2;
			}
			if($selall=='true') {
				$queryemp = $this->report_model->getallemployeedetails();
				$yo=3;
			} 
			
			$holiday = $this->process_model->cekholiday($datestart, $datestop);
			$holarray = array();
			foreach($holiday->result() as $hol) {
				$tglmulai = strtotime($hol->startdate);
				$tglselesai = strtotime($hol->enddate);
				$selisih = $tglselesai - $tglmulai;
				if($selisih==0) {
					$holar[$hol->startdate] = $hol->info;
				} else {
					$jarak = $selisih / 86400;
					for($k=0;$k<=$jarak;$k++) {
						$holar[date('Y-m-d',strtotime($hol->startdate) + ($k*86400))] = $hol->info;
					}
				}					
			}	
			
			$dataallay = array();
			$dataallu = array();
			$aten = array();
			$aben = array();
			foreach($queryemp->result() as $queq) {
				$dataallay[] = array(
								'userid'   => $queq->userid,
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''					
						);
				$dataallu[$queq->userid] = array(
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:''					
						);
				foreach($attend as $at3) {
					$aten[$queq->userid][$at3['atid']] = 0;
				}
				foreach($absen as $ab3) {
					$aben[$queq->userid][$ab3['abid']] = 0;
				}
			}	
			
			$dataallaye = array();
			$dataarray = array();
			if($selall != 'true'){
				foreach($dataallay as $queqe) {
					$querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);			
					$totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
					$holonnwds=0;$totalholiday  = 0;
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
							$adashift = $this->report_model->checkinout($que->date_shift, $queqe['userid']);
							if($adashift->total>0) 
								$workinholiday++;
						}
					
						if(isset($atar[$que->attendance])) {
							$attendance++;	
							$aten[$queqe['userid']][$que->attendance]++;					
						} 
					
						if(isset($bbar[$que->attendance])) {
							$absence++;
							$aben[$queqe['userid']][$que->attendance]++;							
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
							
						if($que->attendance=='ALP') {
							if($que->workinholiday!=1) $alpha++;                    
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
					}
				
					$totalbgt = $total - $off - $alpha - $absence;
					$ttlworkingday = $total;
					$workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
					$totalabsent = $alpha + $absence;
					$dataarray[] = array (
									'userid'		=> $queqe['userid'],
									'badgeNumber'	=> $queqe['empID'],
									'name'			=> $queqe['empName'],
									'workingday'	=> $ttlworkingday!=0?$ttlworkingday - $totalholiday + $holonnwds - $off:'0',
									'workday'       => $workday!=0?$workday:'0',
									'off'			=> $off!=0?$off + $totalholiday - $holonnwds:'-',
									'attendance'	=> $attendance!=0?$attendance:'0',
									'aten'			=> $aten,
									'absence'		=> $absence!=0?$absence:'0',
									'aben'			=> $aben,
									'absent'		=> $alpha!=0?$alpha:'-',
									'totalabsent'   => $totalabsent!=0?$totalabsent:'0',
									'late'			=> $totallate!=0?$totallate:'-',
									'early'			=> $totalearly!=0?$totalearly:'-',
									'OT'			=> $totalot!=0?$totalot:'-',
									'workinholiday'	=> $workinholiday!=0?$workinholiday:'-',
									'editcome'      => $editcome!=''?$editcome:'-',
									'edithome'      => $edithome!=''?$edithome:'-'
					);
				}
				$dataallaye = array(
					'dept'   		=> isset($deptar[$dpt])?$deptar[$dpt]:'',
					'holidays' 		=> $totalholiday,
					'datestart' 	=> date('d-M-Y', $datestart),
					'datestop' 		=> date('d-M-Y', $datestop)
				);
			} else {        
				foreach($dataallay as $queqe) {
					$querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);			
					$totallate=0; $totalearly=0; $totalot=0; $total=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
					$holonnwds=0;$totalholiday  = 0;
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
							$adashift = $this->report_model->checkinout($que->date_shift, $queqe['userid']);
							if($adashift->total>0) 
								$workinholiday++;
						}
						
						if(isset($atar[$que->attendance])) {
							$attendance++;	
							$aten[$queqe['userid']][$que->attendance]++;					
						} 
						
						if(isset($bbar[$que->attendance])) {
							$absence++;
							$aben[$queqe['userid']][$que->attendance]++;
							
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
								
						if($que->attendance=='ALP') {
							if($que->workinholiday!=1) $alpha++;                     
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
						if(isset($holar[$que->date_shift])  && ($que->attendance=='NWDS' || $que->attendance=='NWK'))
							$holonnwds++;
					}
					
					$totalbgt = $total - $off - $alpha - $absence;
					$ttlworkingday = $total;
					$workday = $ttlworkingday - $absence - $alpha - $totalholiday + $holonnwds - $off;
					$totalabsent = $alpha + $absence;
					
					$dataarray[] = array (
								'userid'		=> $queqe['userid'],
								'badgeNumber'	=> $queqe['empID'],
								'name'			=> $queqe['empName'],
								'DeptID'        => $queqe['deptName'],
								'workingday'	=> $ttlworkingday!=0?$ttlworkingday - $totalholiday + $holonnwds - $off:'0',
								'workday'       => $workday!=0?$workday:'0',
								'off'			=> $off!=0?$off + $totalholiday - $holonnwds:'-',
								'attendance'	=> $attendance!=0?$attendance:'0',
								'aten'			=> $aten,
								'absence'		=> $absence!=0?$absence:'0',
								'aben'			=> $aben,
								'absent'		=> $alpha!=0?$alpha:'-',
								'totalabsent'   => $totalabsent!=0?$totalabsent:'0',
								'late'			=> $totallate!=0?$totallate:'-',
								'early'			=> $totalearly!=0?$totalearly:'-',
								'OT'			=> $totalot!=0?$totalot:'-',
								'workinholiday'	=> $workinholiday!=0?$workinholiday:'-',
								'editcome'      => $editcome!=''?$editcome:'-',
								'edithome'      => $edithome!=''?$edithome:'-'
					);					
				}
				
				$dataallaye = array(
					'dept'   		=> 'All',
					'holidays' 		=> $totalholiday,
					'datestart' 	=> date('d-M-Y', $datestart),
					'datestop' 		=> date('d-M-Y', $datestop)
				);
			
			}
					
			$data = array(
				"cominfo" 	=> $company,
				"empinfo" 	=> $dataallaye,
				"att"		=> $attend,
				"abs"		=> $absen,
				"data" 		=> $dataarray
			);			
			
			if($selall != 'true') {
				if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=recapitulationreport.xls");			
				}				
				$this->load->view("recap/recapview",$data);
			} else {	
				$dataOrganization = array(
					"cominfo"        => $company,
					"empinfo"        => $dataallaye,
					"att"            => $attend,
					"abs"            => $absen,
					"companyLooping" => $deptGroupid,
					"data"           => $dataarray
				);
				if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=recapitulationreport.xls");			
				}
				$this->load->view("recap/recapvieworg",$dataOrganization);
			}
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	
	public function ArByDate()
	{
		if(!empty($_GET['startR'])){
            $start_date1 = $_GET['startR'];
            $exStart = explode(" ",$start_date1);
            $time = $exStart[3]."-".$exStart[1]."-".$exStart[2];
            $start_date = date("Y-m-d",strtotime($time));
		} else {
            $start_date = $startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-7,date('Y')));
		}
				
		if(!empty($_GET['endR'])){
            $end_date1 = $_GET['endR'];
            $exEnd = explode(" ",$end_date1);
            $time = $exEnd[3]."-".$exEnd[1]."-".$exEnd[2];
            $end_date = date("Y-m-d",strtotime($time));
		} else {
            $end_date = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y')));
		}				
		
		$wdt = '';
		if(isset($_GET['wdt']))	$wdt = $_GET['wdt'];
		
		$query_att = "SELECT  date_shift FROM process GROUP BY date_shift";       
		$group_date = $this->db->query($query_att); 
		$tampilanReport = 
			'<table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
					<tr align="left">
						<td align="left" width="118"><input type="button" value="Print" onClick="window.print()"/></td>												
						<td>&nbsp;</td>
					</tr>
					<tr align="left">
						<td colspan="2" align="center"><h3>ATTENDANCE REPORT</h3></td>
					</tr>
					<tr align="left">
					  <td align="center" colspan="2"><b>Periode '.date("d/m/Y",strtotime($start_date)).' - '.date("d/m/Y",strtotime($end_date)).'</b></td>
					</tr>';
		foreach($group_date->result() as $tglatt)
		{

		$tampilanReport .= '<tr align="left">
					<td align="left" style="font-size:11px;"> <br><br>'.date("d F Y",strtotime($tglatt->date_shift)).'&nbsp;</td>
					<td>&nbsp;</td>
					</tr>
					</table>
					<table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
					<tbody>
					<tr align="center">
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Employee ID</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Employe Name </th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Working Hour</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Activity</th>';
							if(!empty($wdt)) $tampilanReport .= '<th style="border:1px solid #C1DAD7; background-color: #dedede;">Date In</th>';
							$tampilanReport .= '<th style="border:1px solid #C1DAD7; background-color: #dedede;">Duty On</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Break Out</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Break In</th>';
							if(!empty($wdt)) $tampilanReport .= '<th style="border:1px solid #C1DAD7; background-color: #dedede;">Date Out</th>';
							$tampilanReport .= '<th style="border:1px solid #C1DAD7; background-color: #dedede;">Duty Off</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Late In</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Early Dept</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">OT Before</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">OT After</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Total Hour</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Notes</th>
						</tr>';
						
				$query_att_perdate = "SELECT * FROM process JOIN userinfo USING(userid)
															WHERE date_shift='".$this->db->escape_str($tglatt->date_shift)."'";       
				$group_per_date = $this->db->query($query_att_perdate); 	
				$total_hour=0;
				foreach($group_per_date->result() as $dataatt)
				{				
					$diffbreak =strtotime($dataatt->break_in) - strtotime($dataatt->break_out);
					
					$diff =strtotime($dataatt->check_out) - strtotime($dataatt->check_in);
					
					$hour = round($diff / 3600);
					$minute = ($diff%3600)/60;
					$total_hour=$hour.'h '.$minute.'m';
					//echo ''.$minute;
					$tampilanReport .= '<tr align="center">
								<td style="border:1px solid #C1DAD7;">'.$dataatt->userid.'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->name.'</td>
								<td style="border:1px solid #C1DAD7;">'.substr($dataatt->shift_in,0,-3).' - '.substr($dataatt->shift_out,0,-3).'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->attendance.'</td>';
								if(!empty($wdt)) 
								$tampilanReport .= '<td style="border:1px solid #C1DAD7;">'.date("d-m-Y",strtotime($dataatt->date_in)).'</td>';
								$tampilanReport .= '<td style="border:1px solid #C1DAD7;">'.substr($dataatt->check_in,0,-3).'</td>
								<td style="border:1px solid #C1DAD7;">'.substr($dataatt->break_out,0,-3).'</td>
								<td style="border:1px solid #C1DAD7;">'.substr($dataatt->break_in,0,-3).'</td>';
								if(!empty($wdt)) 
								$tampilanReport .= '<td style="border:1px solid #C1DAD7;">'.date("d-m-Y",strtotime($dataatt->date_out)).'</td>';
								$tampilanReport .= '<td style="border:1px solid #C1DAD7;">'.substr($dataatt->check_out,0,-3).'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->late.'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->early_departure.'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->ot_before.'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->ot_after.'</td>
								<td style="border:1px solid #C1DAD7;">'.$total_hour.'</td>
								<td style="border:1px solid #C1DAD7;">&nbsp;</td>								
							</tr>';
				}		
		}		
		$tampilanReport .=	'</tbody></table>';						
		echo $tampilanReport;
	}
	
	
	public function ArByOrg()
	{
		if(!empty($_GET['startR'])){
            $start_date1 = $_GET['startR'];
            $exStart = explode(" ",$start_date1);
            $time = $exStart[3]."-".$exStart[1]."-".$exStart[2];
            $start_date = date("Y-m-d",strtotime($time));
    }else{
            $start_date = $startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-7,date('Y')));
    }
				
    if(!empty($_GET['endR'])){
            $end_date1 = $_GET['endR'];
            $exEnd = explode(" ",$end_date1);
            $time = $exEnd[3]."-".$exEnd[1]."-".$exEnd[2];
            $end_date = date("Y-m-d",strtotime($time));
    }else{
            $end_date = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y')));
    }				
		
		if(!empty($_GET['userid'])){
			$ex_user = $_GET['userid'];
			$idsql = " AND userinfo.userid IN (".$ex_user.")";
		}else
			$idsql='';
			
		$wdt = '';
		if(isset($_GET['wdt']))	$wdt = $_GET['wdt'];
		
		$query_att = "SELECT  DeptID,DeptName FROM departments GROUP BY DeptID ORDER BY DeptName";       
    $group_date = $this->db->query($query_att); 
		$tampilanReport = 
			'<table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
					<tr align="left">
						<td align="left" width="118"><input type="button" value="Print" onClick="window.print()"/></td>												
						<td>&nbsp;</td>
					</tr>
					<tr align="left">
						<td colspan="2" align="center"><h3>ATTENDANCE REPORT</h3></td>
					</tr>
					';
		foreach($group_date->result() as $tglatt)
		{

		$tampilanReport .= '<tr align="left">
					<td align="left" style="font-size:11px;"> <br><br>'.$tglatt->DeptName.'&nbsp;</td>
					<td>&nbsp;</td>
					</tr>
					</table>
					<table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
					<tbody>
					<tr align="center">
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Employee ID</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Employe Name </th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Working Hour</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Activity</th>';
							
							if(!empty($wdt)) $tampilanReport .='<th style="border:1px solid #C1DAD7; background-color: #dedede;">Date In</th>';
							$tampilanReport .='<th style="border:1px solid #C1DAD7; background-color: #dedede;">Duty On</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Break Out</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Break In</th>';
							
							if(!empty($wdt)) $tampilanReport .= '<th style="border:1px solid #C1DAD7; background-color: #dedede;">Date Out</th>';
							$tampilanReport .=  '<th style="border:1px solid #C1DAD7; background-color: #dedede;">Duty Off</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Late In</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Early Dept</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">OT Before</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">OT After</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Total Hour</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Notes</th>
						</tr>';
						
				$query_att_perdate = "SELECT * FROM process JOIN userinfo USING(userid)
															WHERE DeptID='".$this->db->escape_str($tglatt->DeptID)."' ".$idsql;       
				$group_per_date = $this->db->query($query_att_perdate); 	
				$total_hour=0;
				foreach($group_per_date->result() as $dataatt)
				{				
					$total_hour=round((strtotime($dataatt->check_out) - strtotime($dataatt->check_in)) / (60*60));
					$tampilanReport .= '<tr align="center">
								<td style="border:1px solid #C1DAD7;">'.$dataatt->userid.'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->name.'</td>
								<td style="border:1px solid #C1DAD7;">'.substr($dataatt->shift_in,0,-3).' - '.substr($dataatt->shift_out,0,-3).'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->attendance.'</td>';
								
								if(!empty($wdt))
								$tampilanReport .= '<td style="border:1px solid #C1DAD7;">'.date("d-m-Y",strtotime($dataatt->date_in)).'</td>';								
								$tampilanReport .= '<td style="border:1px solid #C1DAD7;">'.substr($dataatt->check_in,0,-3).'</td>
								<td style="border:1px solid #C1DAD7;">'.substr($dataatt->break_out,0,-3).'</td>
								<td style="border:1px solid #C1DAD7;">'.substr($dataatt->break_in,0,-3).'</td>';
								
								if(!empty($wdt))
								$tampilanReport .= '<td style="border:1px solid #C1DAD7;">'.date("d-m-Y",strtotime($dataatt->date_out)).'</td>';								
								$tampilanReport .= '<td style="border:1px solid #C1DAD7;">'.substr($dataatt->check_out,0,-3).'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->late.'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->early_departure.'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->ot_before.'</td>
								<td style="border:1px solid #C1DAD7;">'.$dataatt->ot_after.'</td>
								<td style="border:1px solid #C1DAD7;">'.$total_hour.'</td>
								<td style="border:1px solid #C1DAD7;">&nbsp;</td>								
							</tr>';
				}		
		}		
		$tampilanReport .=	'</tbody></table>';						
		echo $tampilanReport;
	}

	public function EmpRep()
	{
		//http://localhost/woowtime/report/EmpRep?orgId=29&col=useridsort=asc	
		$where = '';
		$order = '';
		if(!empty($_GET['orgId']) && $_GET['orgId']!='null')	$where .= " WHERE DeptID=".$this->db->escape($_GET['orgId']);
		if(!empty($_GET['col']))	$order .= " ORDER BY  ".trim($_GET['col'])." ".trim($_GET['sort']);

		
		$tampilanReport = 
			'<table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
					<tr align="left">
						<td align="left" width="118"><input type="button" value="Print" onClick="window.print()"/></td>												
						<td>&nbsp;</td>
					</tr>
					<tr align="left">
						<td colspan="2" align="center"><h3>EMPLOYEE REPORT</h3></td>
					</tr>
					</table>					
					<table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
					<tbody>
					<tr align="center">
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">NIK</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Employe Name </th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Nick Name</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Card No</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Privilege</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Organization</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Title</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Place Of Birth</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Birth of Date</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Hired Date</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Area</th>
							<th style="border:1px solid #C1DAD7; background-color: #dedede;">Email</th>
						</tr>';
						
				$query_att_perdate = "SELECT userid,badgeNumber,DeptName,name, Card, Privilege, Title,placeBirthDate,NickName,hiredDate,birthDate,areaid,Email ,areaname
															FROM departments LEFT JOIN userinfo  USING(DeptID) LEFT JOIN userinfo_attarea USING(userid) LEFT JOIN personnel_area USING(areaid)".$where.$order;       
				$group_per_date = $this->db->query($query_att_perdate); 	
				$total_hour=0;
				foreach($group_per_date->result() as $dataatt)
				{				
					$tampilanReport .= '<tr align="center">
								<td style="border:1px solid #C1DAD7;">'.$dataatt->badgeNumber.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->name.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->NickName.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->Card.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->Privilege.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->DeptName.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->Title.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->placeBirthDate.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->birthDate.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->hiredDate.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->areaname.'&nbsp;</td>		
								<td style="border:1px solid #C1DAD7;">'.$dataatt->Email.'&nbsp;</td>		
							</tr>';
				}		
		
		$tampilanReport .=	'</tbody></table>';						
		echo $tampilanReport;
	}
		
	
	public function RPTattendanceOR(){
        
        $orgid = "";
        $start_date = "";
        $end_date = "";
        
		$holid = $this->report_model->getholiday();
				foreach($holid->result() as $hol) {
					$holar[$hol->date_holiday] = $hol->holiday_information;
				}
				
				$att = $this->report_model->getattname($userid);
				foreach($att->result() as $tt) {
					$tbar[$tt->date_shift] = $tt->atname;
				}
				
				$abs = $this->report_model->getabsname($userid);
				foreach($abs->result() as $bs) {
					$bbar[$bs->date_shift] = $bs->abname;
				}
				
        if(!empty($_GET['orgId'])){
            $orgid = $_GET['orgId'];
        }
        if(!empty($_GET['dateStart'])){
            $start_date = $_GET['dateStart'];
        }
        if(!empty($_GET['dateEnd'])){
            $end_date = $_GET['dateEnd'];
        }
        $tampilanAttendance = '<table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
                                      <tr>
                                        <td colspan="2" align="center"><b>ATTENDANCE REPORT</b><br></td>
                                      </tr>
                                      <tr>
                                        <td colspan="2">
                              ';
        $getDataEmploye = $this->report_model->getDataEmployeOrg($orgid);
        $cntEmploye = count($getDataEmploye);
        for($a=0;$a<$cntEmploye;$a++){
        $tampilanAttendance .='
                                        	<table width="100%" border="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
                                          <tr>
                                            <td width="28%" align="left">User ID</td>
                                            <td width="28%" align="left">:'.$getDataEmploye[$a]['userid'].'</td>
                                            <td width="22%" align="left">Employee Title</td>
                                            <td width="22%" align="left">:'.$getDataEmploye[$a]['title'].'</td>
                                          </tr>
                                          <tr>
                                            <td align="left">Employee ID</td>
                                            <td align="left">:'.$getDataEmploye[$a]['badgeNumber'].'</td>
                                            <td align="left">Employee Hire Date</td>
                                            <td align="left">:'.$getDataEmploye[$a]['hiredDate'].'</td>
                                          </tr>
                                          <tr>
                                            <td align="left">Employee Name</td>
                                            <td align="left">:'.$getDataEmploye[$a]['name'].'</td>
                                            <td align="left">Departement Name</td>
                                            <td align="left">:'.$getDataEmploye[$a]['dept'].'</td>
                                          </tr>
                                          <tr>
                                            <td colspan="4">
                                            <table width="100%" cellpadding="0"  cellspacing="0" style="border:1px solid #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
                                              <tr style="font-weight:bold;border:1px solid #C1DAD7">
                                                <td style="border:1px solid #C1DAD7;font-family">Day</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Date</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Working Hour</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Activity</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Date In</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Duty On</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Break Out</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Break In</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Date Out</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Duty Off</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Late In</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Early Dept</td>
                                                <td style="border:1px solid #C1DAD7;font-family">OT Before</td>
                                                <td style="border:1px solid #C1DAD7;font-family">OT After</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Total Hour</td>
                                                <td style="border:1px solid #C1DAD7;font-family">Notes</td>
                                              </tr>
                                ';
        $getProcessEmploye=$this->report_model->processEmployee($getDataEmploye[$a]['userid'],$start_date,$end_date);
        $cntProcess = count($getProcessEmploye);
        //echo var_dump($getProcessEmploye);
        //exit;
        for($p=0;$p<$cntProcess;$p++){
            ;
            $dateDay = explode("-",$getProcessEmploye[$p]['dateShift']);
            $day = date("D", mktime(0, 0, 0, $dateDay[1], $dateDay[2], $dateDay[0]));
            $dateW = $dateDay[2]."-".$dateDay[1]."-".$dateDay[0];
            if($getProcessEmploye[$p]['workinHoliday'] == '1'){
                $act = 'Work in Holiday';
            }
            else{
                $act = 'Work';
            }
            (!empty($getProcessEmploye[$p]['dateIn']))?$dateIn=$getProcessEmploye[$p]['dateIn']:$dateIn="&nbsp";
            (!empty($getProcessEmploye[$p]['checkOut']))?$checkOut=$getProcessEmploye[$p]['checkOut']:$checkOut="&nbsp";
            (!empty($getProcessEmploye[$p]['breakOut']))?$breakOut=$getProcessEmploye[$p]['breakOut']:$breakOut="&nbsp";
            (!empty($getProcessEmploye[$p]['breakIn']))?$breakIn=$getProcessEmploye[$p]['breakIn']:$breakIn="&nbsp";
            (!empty($getProcessEmploye[$p]['dateOut']))?$dateOut=$getProcessEmploye[$p]['dateOut']:$dateOut="&nbsp";
            (!empty($getProcessEmploye[$p]['checkOut']))?$checkOut=$getProcessEmploye[$p]['checkOut']:$checkOut="&nbsp";
            
        $tampilanAttendance .='
                                              <tr>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$day.'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$dateW.'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$getProcessEmploye[$p]['shiftIn']."-".$getProcessEmploye[$p]['shiftOut'].'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$act.'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$dateIn.'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$checkOut.'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$breakOut.'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$breakIn.'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$dateOut.'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$checkOut.'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$getProcessEmploye[$p]['Late'].'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$getProcessEmploye[$p]['earlyDeparture'].'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$getProcessEmploye[$p]['otBefore'].'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">'.$getProcessEmploye[$p]['otAfter'].'</td>
                                                <td style="border:1px solid #C1DAD7;font-family">0h0m</td>
                                                <td style="border:1px solid #C1DAD7;font-family">nbsp;</td>
                                              </tr>
                                            
                                            ';
        }
        $tampilanAttendance .='
                                </table>
                                            </td>
                                            </tr>
                                        </table><br>
                                ';
        }
        $tampilanAttendance .='
                                        </td>
                                      </tr>
                                      </table>
                                    ';
        echo $tampilanAttendance;
        
    }
	
	public function summaryreport()
	{
		$kueri = $this->report_model->getprocessdata('2012-06-01');
		$late=0;$early=0;$ot=0;$att=0;$abs=0;
		foreach($kueri->result() as $kue) {
			if($kue->late!=0) $late++;
			if($kue->early_departure!=0) $early++;
			if($kue->ot_before!=0) {
				$ot++;
			} else if($kue->ot_after!=0) {
				$ot++;
			}
			if($kue->attendance!=0) $att++;
			if($kue->absence!=0) $abs++;			
		}
		
		echo "attendance\t\t: ".$att.'<br>';
		echo "absence\t\t\t: ".$abs.'<br>';
		echo "late\t\t\t\t: ".$late.'<br>';
		echo "early departure\t: ".$early.'<br>';
		echo "overtime\t\t\t: ".$ot.'<br>';	
	}
	
	public function recap1(){
 
        
        if(!empty($_GET['startR'])){
            //$start_date = $_POST['startReport'];
            $start_date1 = $_GET['startR'];
            $exStart = explode("-",$start_date1);
            $time = $exStart[2]."-".$exStart[1]."-".$exStart[0];
            $start_date = date("Y-m-d",strtotime($time));
        }else{
            $start_date = $startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-7,date('Y')));
        }
        if(!empty($_GET['userid'])){
            $userid = $_GET['userid'];
            $badgeNumber = $this->report_model->getBadgeNumber($userid);
        }
        else{
            $userid = "";
			$badgeNumber ="";
        }
        if(!empty($_GET['endR'])){
            //$end_date = $_POST['endReport'];
            $end_date1 = $_GET['endR'];
            $exEnd = explode("-",$end_date1);
            $time = $exEnd[2]."-".$exEnd[1]."-".$exEnd[0];
            $end_date = date("Y-m-d",strtotime($time));
        }else{
            $end_date = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y')));
         }
         
         if(!empty($_GET['organization'])){
            $organization_id = $_GET['organization'];
            $it_org = explode(",",$organization_id);
            $temp_org = $this->departemen_model->getDepartement($it_org);
            $temp_organization = $_GET['organization'];
         }
         else{
            $temp_organization = "";
            $temp_org = "-";
         }  
        $tampilanReport= '';
        $tmp_str = explode("-",$start_date);
        $tmp_end = explode("-",$end_date);
        $tipeAttendance = $this->report_model->attendance();
        $jmlTipeAttendance = $tipeAttendance->{'num_rows'};
        $tipeAbsence = $this->report_model->absence();
        $jmlTipeAbsence = $tipeAbsence->{'num_rows'};
        $totalHolidays = $this->cntHoliday($start_date,$end_date);
        $temp_daywork = $this->workDay($start_date,$end_date);
        $dayWork = $temp_daywork['workday'] - $totalHolidays;
        $totalHolidays = $temp_daywork['holiday'] + $totalHolidays;
        
        //logo perusahaan
        
        
        
        
        $tampilanReport .= '
           <table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
               <tr align="left">
                    	<td align="left" width="118"><input type="button" value="Print" onClick="window.print()"/></td>
                        <td colspan="3"><a href="http://localhost/HITCORP/woowtiime/report/recap1?startR='.$_GET['startR'].'&endR='.$_GET['endR'].'&userid='.$userid.'&organization='.$_GET['organization'].'&pdf=excel">Export To EXCEL</a></td>                        
               </tr>
           </table>
           <div id="printableArea">
           <table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
                <tr>
                	<td colspan="4" align="center"><h1>Recapitulation Report</h1></td>
                </tr>
                <tr align="left">
                	<td align="left">Departemen Name &nbsp;</td><td width="537" align="left">&nbsp; '.$temp_org.'</td>
                	<td width="138" align="left">Total Holidays</td>
                	<td width="304" align="left">'.$totalHolidays.' Days</td>
                </tr>
                <tr align="left">
                	<td align="left">Period &nbsp;</td><td align="left">&nbsp; '.$tmp_str['2'].'/'.$tmp_str['1'].'/'.$tmp_str['0'].' - '.$tmp_end['2'].'/'.$tmp_end['1'].'/'.$tmp_end['0'].'</td>
                	<td align="left">Total Working Days</td>
                	<td align="left">'.$dayWork.' Days</td>
                </tr>
            </table>
           <table width="100%" cellpadding="0"  cellspacing="0" style="border:2px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
                  <tr align="center" valign="middle" >
                    <td rowspan="2" style="border:1px solid #C1DAD7; background-color: #dedede;">User id</td>
                    <td rowspan="2" style="border:1px solid #C1DAD7; background-color: #dedede;">Nama</td>
                    <td rowspan="2" style="border:1px solid #C1DAD7; background-color: #dedede;">Attendance</td>
                    <td colspan="'.$jmlTipeAttendance.'" style="border:1px solid #C1DAD7; background-color: #dedede;">Attendance Status</td>
                    <td rowspan="2" style="border:1px solid #C1DAD7; background-color: #dedede;">Absence</td>
                    <td colspan="'.$jmlTipeAbsence.'" style="border:1px solid #C1DAD7; background-color: #dedede;">Absence Status</td>
                    <td rowspan="2" style="border:1px solid #C1DAD7; background-color: #dedede;">Late</td>
                    <td rowspan="2" style="border:1px solid #C1DAD7; background-color: #dedede;">Early<br>Departure</td>
                    <td rowspan="2" style="border:1px solid #C1DAD7; background-color: #dedede;">Over<br>Time</td>
                    <td rowspan="2" style="border:1px solid #C1DAD7; background-color: #dedede;">Worked<br>in<br>Holiday</td>
                    <td colspan="2" style="border:1px solid #C1DAD7; background-color: #dedede;">Edited<br>Time</td>
                  </tr>
                  <tr align="center" valign="middle">
                  ';
                  $at = 1;
                  $tempAtt ='';
				  $tempAttarr=array();
                  foreach($tipeAttendance->result() as $rowAtt){
                    $tempAttarr[$at] = $rowAtt->atid;
                    $tampilanReport .= '<td style="border:1px solid #C1DAD7; background-color: #dedede;">'.$rowAtt->atid.'</td>';
                    if($at == $jmlTipeAttendance) {$tempAtt .= "'".$rowAtt->atid."'";}
                    else {$tempAtt .= "'".$rowAtt->atid."',";}
                    $at++;
                  }
                  $ab = 1;
                  $tempAb = '';
				  $tempAbarr=array();
                  foreach($tipeAbsence->result() as $rowAbsence){
                    $tempAbarr[$ab] = $rowAbsence->abid;
                    $tampilanReport .= '<td style="border:1px solid #C1DAD7; background-color: #dedede;">'.$rowAbsence->abid.'</td>';
                    if($ab == $jmlTipeAbsence) {$tempAb .= "'".$rowAbsence->abid."'";}
                    else {$tempAb .= "'".$rowAbsence->abid."',";}
                    $ab++;
                  }
                  
                  $tampilanReport .= '  
                    <td style="border:1px solid #C1DAD7; background-color: #dedede;">Check In</td>
                    <td style="border:1px solid #C1DAD7; background-color: #dedede;">Check Out</td>
                  </tr>                  
                  ';
                                    
                  $dataRoaster = $this->report_model->recap1($temp_organization,$userid);
                  
                  if($dataRoaster->{'num_rows'} > '0'){
                    
                    $dataArrAtt = $this->report_model->getArrAtt($start_date,$end_date,$badgeNumber);
                    $dataArrProcess = $this->report_model->getArrPro($start_date,$end_date,$badgeNumber);
                    //echo count($tempAttarr)."<br>";
                    //echo count($tempAbarr);
                    //exit;
                    $rowcolour = 0;
                    $totalAtt = 0;
                    $totalAbsen = 0;
                    $totalLate = 0;
                    $totalEd = 0;
                    $totalOt = 0;
                    $totalWh = 0;
                    $totalCome = 0;
                    $totalHome = 0;
                    $ttlAtt = array();
                    
                    foreach($dataRoaster->result() as $row_roster){
                        
                        //$jmlAtt = $this->report_model->countAttendace($start_date,$end_date,$tempAtt,$row_roster->userid);
                        //$jmlAb = $this->report_model->countAttendace($start_date,$end_date,$tempAb,$row_roster->userid);                        
                        //$process = $this->report_model->tblProcess($start_date,$end_date,$row_roster->userid);
                        if($rowcolour%2 == '0')
                            $tempColour = 'bgcolor="#CCFFFF"';
                        else
                            $tempColour = 'bgcolor="#FFFFFF"';
                       $tempAbsent[] = 'ABSENT';
                        $jmlAtt = $this->report_model->countAtt($row_roster->userid,$tempAttarr,$dataArrAtt); 
                                                                       
                        $jmlAb = $this->report_model->countAtt($row_roster->userid,$tempAbarr,$dataArrAtt);
                        $jmlAbsent = $this->report_model->countAtt($row_roster->userid,$tempAbsent,$dataArrAtt);
                        $jmlAb1 = $jmlAbsent + $jmlAb;      
                        $jmlAtt1 = $dayWork - $jmlAb1;                                     
                        //$jmlAtt = $this->report_model->search($dataArrAtt, 'userid', $row_roster->userid);
                        if($jmlAtt1 == '0' || $jmlAtt1 == '') 
                            $jmlAtt1 = "-";
                        else
                            $totalAtt = $jmlAtt1 + $totalAtt;
                        
                        if(!empty($row_roster->name)){
                            $user_name = $row_roster->name;
                        }
                        else{
                            $user_name = ' - ';
                        }
                        
                        if(!empty($row_roster->userid)){
                            $userid = $row_roster->userid;
                        }
                        else{
                            $userid = ' - ';
                        }
                        
                          $tampilanReport .='
                          <tr '.$tempColour.'>
                            <td align="center" style="border:1px solid #C1DAD7;">'.$userid.'</td>
                            <td align="center" style="border:1px solid #C1DAD7;">'.$user_name.'</td>
                            <td align="center" style="border:1px solid #C1DAD7;">'.$jmlAtt1.'</td>
                            ';
                            //for attendance report
                            
                            for($cat=1;$cat<$at;$cat++){ 
                                                                //if($tempAttarr[$cat] == $getDataEmp[$cat])
                                //$getDataAtt = $this->report_model->tblAtt($start_date,$end_date,$row_roster->userid,$tempAttarr[$cat]);
                                $getDataAtt = $this->report_model->searchAtt($dataArrAtt, 'userid', $row_roster->userid,$tempAttarr[$cat]);                                
                                if($getDataAtt != ''){
                                    $tampilanReport .= '<td align="center" style="border:1px solid #C1DAD7;">'.$getDataAtt.'</td>';
                                }
                                else{
                                    $tampilanReport .= '<td align="center" style="border:1px solid #C1DAD7;">-</td>';
                                }
                                //echo $ttlAtt[$tempAttarr[$cat]]."<br>";
                            }
                            //exit();
                            if($jmlAb1 == '0' || $jmlAb1 == '') 
                                $jmlAb = "-";
                            else
                                $totalAbsen = $jmlAb1 + $totalAbsen;
                            
                            $tampilanReport .='
                            <td align="center" style="border:1px solid #C1DAD7;">'.$jmlAb1.'</td>
                            ';
                            //for attendance absence
                            for($cab=1;$cab<$ab;$cab++){ 
                                //if($tempAttarr[$cab] == $getDataEmp[$cab])
                                //$getDataAb = $this->report_model->tblAtt($start_date,$end_date,$row_roster->userid,$tempAbarr[$cab]);
                                $getDataAb = $this->report_model->searchAtt($dataArrAtt, 'userid', $row_roster->userid,$tempAbarr[$cab]);
                                //$totalAbb[$tempAbarr[$cab]] = $totalAbb[$tempAbarr[$cab]] + $getDataAb;
                                if($getDataAb != ''){
                                    
                                    $tampilanReport .= '<td align="center" style="border:1px solid #C1DAD7;">'.$getDataAb.'</td>';
                                }
                                else{
                                    
                                    $tampilanReport .= '<td align="center" style="border:1px solid #C1DAD7;">-</td>';
                                }
                            }
                            
                            $process = $this->report_model->searchProcess($dataArrProcess,'userid',$row_roster->userid);
                            
                            if($process['late'] == "0" || $process['late'] == "") $process['late'] = "-";
                            else $totalLate = $totalLate + $process['late'];
                            
                            if($process['Ed'] == "0" || $process['Ed'] == "") $process['Ed'] = "-";
                            else $totalEd = $totalEd + $process['Ed'];
                            
                            if($process['ot'] == "0" || $process['ot'] == "") $process['ot'] = "-";
                            else $totalOt = $totalOt + $process['ot'];
                            
                            if($process['Wh'] == "0" || $process['Wh'] == "") $process['Wh'] = "-";
                            else $totalWh = $totalWh + $process['Wh'];
                            
                            if($process['come'] == "0" || $process['come'] == "") $process['come'] = "-";
                            else $totalCome = $totalCome + $process['come'];
                            
                            if($process['home'] == "0" || $process['home'] == "") $process['home'] = "-";
                            else $totalHome = $totalHome + $process['home'];
                            
                            $tampilanReport.='
                            <td align="center" style="border:1px solid #C1DAD7;">'.$process['late'].'</td>
                            <td align="center" style="border:1px solid #C1DAD7;">'.$process['Ed'].'</td>
                            <td align="center" style="border:1px solid #C1DAD7;">'.$process['ot'].'</td>
                            <td align="center" style="border:1px solid #C1DAD7;">'.$process['Wh'].'</td>
                            <td align="center" style="border:1px solid #C1DAD7;">'.$process['come'].'</td>
                            <td align="center" style="border:1px solid #C1DAD7;">'.$process['home'].'</td>
                          </tr>
                          ';
                          $rowcolour++;
                      }
                      
                  }
                  $attColoumn = "";
                  $abbColoumn = "";
                  

                  foreach($tipeAttendance->result() as $rowAtt){                    
                    //$attColoumn.= '<td style="border:1px solid #C1DAD7; background-color: #dedede;font-weight:bold;">'.$totalAtt[$rowAtt->atid].'</td>'; 
                    $hslatt = $this->report_model->countAtt_($dataArrAtt,$rowAtt->atid);
                    $attColoumn.= '<td align="center" style="border:1px solid #C1DAD7; background-color: #dedede;font-weight:bold;">'.$hslatt.'</td>';                    
                  }
                  
                  foreach($tipeAbsence->result() as $rowAbsence){       
                    $hslab = $this->report_model->countAtt_($dataArrAtt,$rowAbsence->abid);
                    //$abbColoumn .= '<td style="border:1px solid #C1DAD7; background-color: #dedede;font-weight:bold;">'.$totalAbb[$rowAbsence->abid].'</td>';
                    $abbColoumn .= '<td align="center" style="border:1px solid #C1DAD7; background-color: #dedede;font-weight:bold;">'.$hslab.'</td>';                    
                  } 
                  $tampilanReport .= '
                    <tr>
                            <td align="center" style="border:1px solid #C1DAD7;" colspan="2">Total</td>
                            <td align="center" style="border:1px solid #C1DAD7;font-weight:bold;">'.$totalAtt.'</td>
                            '.$attColoumn.'
                            <td align="center" style="border:1px solid #C1DAD7;font-weight:bold;">'.$totalAbsen.'</td>
                            '.$abbColoumn.'
                            <td align="center" style="border:1px solid #C1DAD7;font-weight:bold;">'.$totalLate.'</td>
                            <td align="center" style="border:1px solid #C1DAD7;font-weight:bold;">'.$totalEd.'</td>
                            <td align="center" style="border:1px solid #C1DAD7;font-weight:bold;">'.$totalOt.'</td>
                            <td align="center" style="border:1px solid #C1DAD7;font-weight:bold;">'.$totalWh.'</td>
                            <td align="center" style="border:1px solid #C1DAD7;font-weight:bold;">'.$totalCome.'</td>
                            <td align="center" style="border:1px solid #C1DAD7;font-weight:bold;">'.$totalHome.'</td>
                    </tr>
                  ';
                  $tampilanReport .= '                  
                </table>
                </div>';
        if(!empty($_GET['pdf'])){
            //echo "aaa";
            $download_pdf = $_GET['pdf'];
        }
        else{
            $download_pdf = '';
        }
        
        
        if($download_pdf == 'excel'){
            //generate_pdf($tampilanReport,'REPORT_RECAP.pdf');
            $filename="reportRecapitulation".date('mdy');
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=$filename.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $tampilanReport;
        }else{        
            echo $tampilanReport;
        }
	}
	
	public function cntHoliday($startTime,$endTime){        
        $tempArrHoliday = $this->report_model->getDayHoliday($startTime,$endTime);
        $sumHoliday = count($tempArrHoliday);
        $tempDay = 0;
        if($sumHoliday > '0'){
            foreach($tempArrHoliday as $rowHoliday){
                //$dayHoliday = $this->countDay($rowHoliday->{'date_holiday'},$rowHoliday->{'end_date_holiday'});
                $tempDay = $tempDay+$dayHoliday;
            }  
        }
              
        return $tempDay;
    }
	
	public function workDay($startTime,$endTime){
        $day = 86400;
        //$format = 'Y-m-d';
        $format = 'N';
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);
        $numDays = round(($endTime - $startTime) / $day) + 1;
        $days = array();
        $temp_workDay=0;
        $temp_holiday=0;
        $get_nonworkDay = $this->report_model->getnonWorkDay();
        for ($i = 0; $i < $numDays; $i++) {
            //$days[] = date($format, ($startTime + ($i * $day)));    
            $days = date($format, ($startTime + ($i * $day)));             
            //if(($days!='6')&&($days!='7')){   
            if(!in_array($days,$get_nonworkDay)){
                $temp_workDay++;
            }  
            else{
                $temp_holiday++;
            }          
        }
        $tempWrk['workday'] = $temp_workDay;
        $tempWrk['holiday'] = $temp_holiday;
        return $tempWrk;
    }
	
	public function exportxl(){
        $filename = 'reportrecap';
        $startdate = $_SESSION['startdate'];
        $enddate = $_SESSION['enddate'];
                
        //$startdate = "2012-06-01";
        //$enddate = "2012-06-20";
        $hasil = $this->report_model->getreport($startdate,$enddate);
        $header = "USER ID\tDATE SHIFT\tSHIFT IN\tSHIFT OUT\tDATE IN\tCHECK IN\tDATE OUT\tCHECK OUT\tLATE\tEARLY DEPARTURE\tOVER TIME BEFORE\tOVER TIME AFTER\tWORK IN HOLIDAY\tATTENDANCE\tABSENCE";
        $rowDt ="";
        foreach($hasil->result() as $dataReport){
            $rowDt .= $dataReport->userid."\t".$dataReport->date_shift."\t".$dataReport->shift_in."\t".$dataReport->shift_out."\t".$dataReport->date_in."\t".$dataReport->check_in."\t".$dataReport->date_out."\t".$dataReport->check_out."\t".$dataReport->late."\t".$dataReport->early_departure."\t".$dataReport->ot_before."\t".$dataReport->ot_after."\t".$dataReport->workinholiday."\t".$dataReport->attendance."\t".$dataReport->absence."\n";        
           
        }
    
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$header\n$rowDt";
    }
	
	public function monthlyReport()
	{
		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];
		$excelid = $_GET['excelid'];
		$periode = $this->lang->line('periode').' : '.date("d F Y",strtotime($start_date)).' - '.date("d F Y",strtotime($end_date));
		$arr_days = $this->createDateRangeArray($start_date,$end_date);
		
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
		
		if($this->input->get('orgid')!='undefined')		
			$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
		else 
			$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
		
		if(!empty($orgid)) {
			foreach($orgid as $org) 
				$orga[] = "'".$org."'";
			$orgaidi = implode(',', $orga);
			$with_user_id = "deptid IN (".$orgaidi.") ";
			$tambahan = "deptid IN (".$orgaidi.") ";
		}
			
		if(isset($_GET['userid']) && $_GET['userid']!='undefined') {
			$useraidi = explode(',',substr($_GET['userid'],0,-1));
			foreach($useraidi as $usr)
				$usernya[] = "'".$usr."'";
			$with_user_id = "userid IN (".implode(',',$usernya).") ";
			$tambahan = "a.userid IN (".implode(',',$usernya).") ";
		}
			
		$sqlcok = "select a.userid, date_shift, check_in, check_out, attendance 
						from process a join userinfo b on a.userid=b.userid 
						where ".$tambahan."and date_shift >= '".$this->db->escape_str($start_date)."' and date_shift <= '".$this->db->escape_str($end_date)."'";
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
		$this->load->view("inoutrep/inoutrep",$data);
	}
	
	public function genMonthRep()
	{
		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];
		//	exit; 
		$arr_days = $this->createDateRangeArray($start_date,$end_date);
	//	var_dump($arr_days);
		
		
		$headcol = '<tr align="center">
				<th style="border:1px solid #C1DAD7; background-color: #dedede;" width="40" >'.$this->lang->line('no').'</th>
				<th style="border:1px solid #C1DAD7; background-color: #dedede;" width="100" >'.$this->lang->line('userid').'</th>
				<th style="border:1px solid #C1DAD7; background-color: #dedede;" width="150" >'.$this->lang->line('name').'</th>';
		for($i=0;$i<count($arr_days);$i++) {		
			$headcol .= '<th style="border:1px solid #C1DAD7; background-color: #dedede;" width="80">
									<table>
										<tr>
											<td colspan="2" align="center" style="font-size:12px" width="200">'.date("d F",strtotime($arr_days[$i])).'</td>
										</tr>
										<tr>
											<td style="border-right:1px solid #C1DAD7;font-size:12px;text-align:center" width="50%">'.$this->lang->line('in').'</td>
											<td style="font-size:12px;text-align:center">'.$this->lang->line('out').'</td>
										</tr>
									</table></th>';
		}
		$headcol .= '</tr>';
		$tampilanReport = 
			'
			<table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
					<tr align="left">
						<td align="left" width="118">';
		$tampilanReport .= '</td>												
						<td>&nbsp;</td>
					</tr>
					<tr align="left">
						<td colspan="2" align="center"><h3>'.strtoupper($this->lang->line('menu_main_inoutreport')).'</h3></td>
					</tr>
					<tr>
						<td colspan="2">'.$this->lang->line('periode').' : '.date("d F Y",strtotime($start_date)).' - '.date("d F Y",strtotime($end_date)).'</td>
						<td></td>
					</tr>
					</table>				

								
					<table cellpadding="0" width="100%"  cellspacing="0" style="border:0px #C1DAD7;font-family: verdana,arial,sans-serif;font-size:11px;">
					<tbody>';	

				
			
		return $tampilanReport;
	}
	
	public function monthrepexcel()
	{
		$data = $this->genMonthRep();
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=io_report.xls");
		echo "$data";
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
	
	public function statrep()
	{
		$postdatestart = $this->input->get('datestart');
		$postdatestop = $this->input->get('dateend');
		$selall = $this->input->get('selall');
		$statusid = $this->input->get('status');	
		$userid = $this->input->get('userid');	
		$excelid = $this->input->get('excelid');	
		$compa = $this->report_model->getcompany();	
		$datestart = strtotime($postdatestart);			
		$datestop = strtotime($postdatestop);	
		
		if($this->input->get('orgid')!='undefined')		
			$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
		else 
			$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
		
		$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
		
		$dept = $this->report_model->getdept();
		foreach($dept->result() as $depat) {
			$deptar[$depat->deptid] = $depat->deptname;
		}		
		
		$deptGroupid = $this->report_model->getdepart();
		
		$company = array(
				'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
				'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
				'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
				'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
				'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
				'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
		);		
		
		$data['_height'] = '';
		$data['cominfo'] = $company;
		
		$yo = 0;
		if($this->input->get('orgid')!='undefined') {
			$queryemp = $this->report_model->getorgemployeedetails($orgid);
			$yo=1;
		}
		
		if($userid!='undefined') {
			$userar = explode(',', $userid);
			$queryemp = $this->report_model->getuseremployeedetails($userar);
			$yo=2;
		}
		if($selall=='true') {
			$queryemp = $this->report_model->getallemployeedetails();
			$yo=3;
		} 
		$index=0;
		foreach($queryemp->result() as $queq) {
			$dataallay['userid'] = $queq->userid;
			$dataallay['datestart'] =  date('d-M-Y', $datestart);
			$dataallay['empID'] = $queq->badgenumber;
			$dataallay['datestop'] = date('d-M-Y', $datestop);
			$dataallay['empName'] = $queq->name;
			$dataallay['deptName'] = isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'';
			$data['empinfo']=$dataallay;			
			
			$querytemp = $this->report_model->getallstatus($datestart, $datestop, $queq->userid, $statusid);
		
			$countrow = $querytemp->num_rows();	
			if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=statusreport.xls");			
			}
			if($yo==2) {							
				$data['data'] = $querytemp;		
				$data['index'] = $index;	
				$this->load->view("statrep/statusrep",$data);
				$index++;
			} else {
				if($countrow>0) {
					$data['data'] = $querytemp;			
					$data['index'] = $index;	
					$this->load->view("statrep/statusrep",$data);
					$index++;
				}
			}
		}			
	}	
	
	public function statrepdetail()
	{
		$postdatestart = $this->input->get('datestart');
		$postdatestop = $this->input->get('dateend');
		$selall = $this->input->get('selall');
		$orgid = $this->input->get('orgid');
		$statusid = $this->input->get('status');	
		$userid = $this->input->get('userid');
		$excelid = $this->input->get('excelid');	
		$compa = $this->report_model->getcompany();
		$areaid = $this->device_model->areaonall($this->device_model->getUserArea());
		
		$datestart = strtotime($postdatestart);			
		$datestop = strtotime($postdatestop);
		
		$dept = $this->report_model->getdept();
		foreach($dept->result() as $depat) {
			$deptar[$depat->DeptID] = $depat->DeptName;
		}		
		
		$company = array(
				'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
				'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
				'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
				'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
				'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
				'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
		);		
		$data['_height'] = '';
		$data['cominfo'] = $company;
		$index=0;
		$queryemp = $this->report_model->getstatus();
		foreach($queryemp->result() as $queq) 		{			
			$data['statusatt'] = $queq->name;		
			$statusid = $queq->id;
			$querytemp = $this->report_model->getallstatus($datestart, $datestop,'',$statusid,$orgid);		
			$countrow = $querytemp->num_rows();	
		//	if($countrow>0){							
				$data['data'] = $querytemp;		
				$data['index'] = $index;		
				$data['deptname'] = isset($deptar[$orgid])?$deptar[$orgid]:'';	
				$this->load->view("statrep/statusrepdetail",$data);
				$index++;
		//	}
		}				
	}
	
	public function rosterrep()
	{		
		$compa = $this->report_model->getcompany();
		$dept = $this->report_model->getdept();
		$excelid = $this->input->get('excelid');	
		foreach($dept->result() as $depat) {
			$deptar[$depat->deptid] = $depat->deptname;
		}					
		
		$company = array(
				'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
				'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
				'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
				'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
				'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
				'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
		);
		
		$this->db->select('code_shift, colour_shift');
		$this->db->from('master_shift');		
		$shiftcolor = $this->db->get();
		$shiftcol = array();
		foreach($shiftcolor->result() as $sc) 
			$shiftcol[$sc->code_shift] = $sc->colour_shift;
		
		if($this->input->get('orgid')!='undefined')		
			$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
		else 
			$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
		
		$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
		
		$postdatestart = $this->input->get('datestart');
		$postdateend = $this->input->get('dateend');		
		$datestart = strtotime($postdatestart);
		$dateend = strtotime($postdateend);										
		
		$userlist = $this->roster_model->getempofdept($areaid, $orgid);
		$data['deptname'] = isset($deptar[$this->input->get('orgid')])?$deptar[$this->input->get('orgid')]:$deptar['1'];
			
		$countuserlist = $userlist->num_rows();
		if($countuserlist!=0) {
			$range = ($dateend - $datestart) / 86400;		

			$roster = $this->roster_model->getroster($orgid, $datestart, $dateend);
			$arrayroster = array();
			foreach($roster->result() as $rosterdetail) {
				$arrayroster[$rosterdetail->userid][strtotime($rosterdetail->rosterdate)] = array ('absence' => $rosterdetail->absence, 'attendance' => $rosterdetail->attendance);				
			}
				
			$nonarray = array();
			
			$holiday = $this->process_model->cekholiday($datestart, $dateend);
			$holarray = array();
			foreach($holiday->result() as $hol) {
				$tglmulai = strtotime($hol->startdate);
				$tglselesai = strtotime($hol->enddate);
				$selisih = $tglselesai - $tglmulai;
				if($selisih==0) {
					$holarray[$hol->deptid][$hol->startdate] = $hol->info;
				} else {
					$jarak = $selisih / 86400;
					for($k=0;$k<=$jarak;$k++) {
						$holarray[$hol->deptid][date('Y-m-d',strtotime($hol->startdate) + ($k*86400))] = $hol->info;
					}
				}					
			}	
																			
			$data['nonarray'] = $nonarray;
			$data['holarray'] = $holarray;
			$data['datestart'] = $datestart;
			$data['empdata'] = $userlist;
			$data['rosterdata'] = $arrayroster;
			$data['cominfo'] = $company;
			$data['shiftcolor'] = $shiftcol;
			
			$data['range'] = $range;
			if($excelid==1) {
					header("Content-type: application/x-msdownload");
					header("Content-Disposition: attachment; filename=rosterreport.xls");			
			}			
			$this->load->view("rosrep/rosterrep",$data);
		}	
	}
	
	public function autotransfertext()
	{
		$rem = $_REQUEST['rem'];		
		if($rem) {		
			$waktu = $_REQUEST['timeautotransfertxt'];		
			$format = $_REQUEST['cbtranslogform'];	
			if($format==1) {
				$output = shell_exec('schtasks.exe /delete /tn autotxttransaction /f');
				$output = shell_exec('schtasks.exe /create /sc daily /st '.$waktu.':00 /tn autotxttransaction /tr '.$this->config->item('base_dir').'apps\\sched\\autotxttransaction.bat /ru System /v1');
			} else {
				$output = shell_exec('schtasks.exe /delete /tn autotxtattendance /f');
				$output = shell_exec('schtasks.exe /create /sc daily /st '.$waktu.':00 /tn autotxtattendance /tr '.$this->config->item('base_dir').'apps\\sched\\autotxtattendance.bat /ru System /v1');
			}
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> 'Set auto transfer text',
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('actionlog', $actionlog);
		} else {			
			$output = shell_exec('schtasks.exe /delete /tn autotxttransaction /f');
			$output = shell_exec('schtasks.exe /delete /tn autotxtattendance /f');
				
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> 'Remove auto transfer text',
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('actionlog', $actionlog);
		}
		$hasil = array("success" => true, "responseText" => "success");
		echo json_encode($hasil);
	}
	
	public function autotxttransaction()
	{
		$startdate = strtotime(date('Y-m-d')) - 86400;	
		
		$path = 'assets/resources/data/setting/elifsket.wt';	
		$setting = file_get_contents($path);			
		$line = explode("\n", $setting);
		$field = explode(',', $line[0]);
		$sql = $line[1]." where checktime::date = '".$this->db->escape_str(date('Y-m-d', $startdate))."'";
		$delimiter = $line[2];
		$query = $this->db->query($sql);
		$txt = '';
		foreach($query->result() as $que) {
			for($a=0;$a<count($field);$a++) {
				$txt .= $que->$field[$a];
				if($a!=count($field)-1)
					$txt .= $delimiter;			
			}
			$txt .="\r\n";
		}		
		$fp = fopen($this->config->item('base_dir').'textfiles/transaction-'.date('Y-m-d', strtotime(date('Y-m-d'))-86400).'.txt', 'w');
		fwrite($fp, $txt);
		fclose($fp);				
	}
	
	public function autotxtattendance()
	{
		$startdate = strtotime(date('Y-m-d')) - 86400;	
		
		$path = 'assets/resources/data/setting/oielifsket.wt';	
		$setting = file_get_contents($path);			
		$line = explode("\n", $setting);
		$fieldi = explode(',', $line[0]);
		$fieldo = explode(',', $line[1]);
		$sql = $line[2]." where date_shift::date = '".$this->db->escape_str(date('Y-m-d', $startdate))."'";
		$delimiter = $line[3];
		$query = $this->db->query($sql);
		$txt = '';
		foreach($query->result() as $que) {
			if($que->check_in!='') {
				for($a=0;$a<count($fieldi);$a++)
					$txt .= $que->$fieldi[$a].$delimiter;	
				$txt .="I\r\n";
			}
			if($que->check_out!='') {
				for($a=0;$a<count($fieldo);$a++)
					$txt .= $que->$fieldo[$a].$delimiter;	
				$txt .="O\r\n";
			}
			
		}		
		$fp = fopen($this->config->item('base_dir').'textfiles/attendance-'.date('Y-m-d', strtotime(date('Y-m-d'))-86400).'.txt', 'w');
		fwrite($fp, $txt);
		fclose($fp);
	}
	
	public function reptunjangan()
	{
		if ($this->auth->is_logged_in()) {
			$postdatestart = $this->input->get('datestart');
			$postdatestop = $this->input->get('dateend');	
			$excelid = $this->input->get('excelid');		
			$userid = $this->input->get('userid');
			$datestart = strtotime($postdatestart);			
			$datestop = strtotime($postdatestop);			
			$mastertunj = array();
			
			$sql = "select * from mastertunjangan";
			$query = $this->db->query($sql);
			foreach($query->result() as $que) {
				$mastertunj[$que->kelasjabatan] = $que->tunjangan;
			}
			
			if($this->input->get('orgid')!='undefined')		
				$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
			else 
				$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			
			$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			$tbar = array();
			$bbar = array();
			$holar = array();
			
			$range = ($datestop - $datestart) / 86400;
			$totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;
			
			$pkrga = array();
			$attrecap = $this->report_model->getatt();
			foreach($attrecap->result() as $at) {
				$atar[$at->atid] = $at->atname;
				$attend[] = array(	
							'atid'		=> $at->atid,
							'atname'	=> $at->atname
						);						
				$pkrga[$at->atid] = $at->value;
			}
			
			$pkrg = array();
			$absrecap = $this->report_model->getabs();
			foreach($absrecap->result() as $bs) {
				$bbar[$bs->abid] = $bs->abname;
				$absen[] = array(	
					'abid'		=> $bs->abid,
					'abname'	=> $bs->abname
				);						
				$pkrg[$bs->abid] = $bs->value;
			}
			
			$dept = $this->report_model->getdept();
			foreach($dept->result() as $depat) {
				$deptar[$depat->deptid] = $depat->deptname;
			}
			
			$yo = 0;
			if($this->input->get('orgid')!='undefined') {
				$queryemp = $this->report_model->getorgemployeedetailsxx($orgid);
				$yo=1;
			}
			
			if($userid!='undefined') {
				$userar = explode(',', $userid);
				$queryemp = $this->report_model->getuseremployeedetailsxx($userar);
				$yo=2;
			}
			
			$aten = array();
			$aben = array();
			
			$dataallay = array();
			$dataallu = array();
			foreach($queryemp->result() as $queq) {
				$dataallay[] = array(
								'userid'   => $queq->userid,
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
								'kelasjabatan' => $queq->kelasjabatan,
								'tunjanganprofesi' => $queq->tunjanganprofesi,
								'tunjangan' => isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0,
								'jftstatus' => $queq->jftstatus,
								'jenisjabatan' => $queq->jenisjabatan,
								'jenispegawai' => $queq->jenispegawai,
								'kedudukan' => $queq->kedudukan
						);
				foreach($attend as $at3) {
					$aten[$queq->userid][$at3['atid']] = 0;
				}
				foreach($absen as $ab3) {
					$aben[$queq->userid][$ab3['abid']] = 0;
				}
			}
			$compa = $this->report_model->getcompany();
			$company = array(
					'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
					'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
					'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
					'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
					'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
					'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
			);				
			
			$dataallaye = array();
			$datafoot = array();
			$abc=0; 
			$dataview = '';
			$datavw = '';
			foreach($dataallay as $queqe) {
				$querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
				$querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
				$dataarray = array();
				$totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
				$totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
				$totallibur  = 0; $tubel = 0; $totaltubel = 0; $tunjtubel = 0; $tgltubel = 0;
				$totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;	
				$tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0;
				$ttlmsk = array(); $tunjangan = array();
				$tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
				$jft = array(); $jfto = 0; $jftp = array();
				$jpeg = array(); $jpego = 0; $jpegp = array();
				$kedu = array(); $keduo = 0; $kedup = array();
				
				foreach($querytempo->result() as $que) {	
					$ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
					$totaltunj[date('mY', strtotime($que->date_shift))] = 0;
				}
				
				foreach($querytempo->result() as $que) {					
					$kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
					if($kelas!=0) $queqe['kelasjabatan'] = $kelas;
					
					$tunjang = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);
					if($tunjang!=0) {
                        $queqe['tunjanganawal'] =$tunjang;
                        $queqe['tunjangan'] = $tunjang * 0.5;
                    }
					
					if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1) 
						$ttlmsk[date('mY', strtotime($que->date_shift))]++;
					
					if($que->flaghol == 1) 
						$ttlmsk[date('mY', strtotime($que->date_shift))]++;
					
					if($tunbuli!=$queqe['tunjangan'])
						$tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];					
					//else 
					//	$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];					
					//echo $tunbuli."-".$queqe['tunjangan']."<br>";					
					$jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
					if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;
					
					if($jfto!=$queqe['jftstatus'])
						$jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];					
					/* else 
						$jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */
					
					$jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
					if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;
					
					if($jpego!=$queqe['jenispegawai'])
						$jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];					
					/* else 
						$jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */
					
					$kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
					if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;
					
					if($keduo!=$queqe['kedudukan'])
						$kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];					
					/* else 
						$kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */
					
					$tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
					if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;
					
					if($tunpro!=$queqe['tunjanganprofesi'])
						$tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];					
					/* else 
						$tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */					
					
					$tunbuli = $queqe['tunjangan'];
					$tunpro = $queqe['tunjanganprofesi'];					
					$jfto = $queqe['jftstatus'];					
					$jpego = $queqe['jenispegawai'];					
					$keduo = $queqe['kedudukan'];					
				}
				
				foreach($querytemp->result() as $que) {
					$late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
					$totalpersenkurang = 0; $totaljadwal++;
					$day = date('D', strtotime($que->date_shift));
					$date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
					if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
					if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));					
					
					if(isset($tunbul[date('mY', strtotime($que->date_shift))])) {
						if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
							$tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];						
							$tunjtubel = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
							//echo $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
						}
					} else {
						$tunjangan[date('mY', strtotime($que->date_shift))] = 0;
					}
					
								
					if($queqe['kedudukan']==2 || $queqe['kedudukan']==6 || $queqe['kedudukan']==12) $tottun = 0;
					if($queqe['jenispegawai']==3 || $queqe['jenispegawai']==4) $tottun = 0;
				
					if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = 0;							
					}
					
					if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;							
						if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = 0;	
						if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
							$tubel = 1;
							$tgltubel = strtotime($que->date_shift);
						}
					}
					
					if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 || $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = 0;							
					}					
									
					if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						$tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];						
						if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
							if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))])
								$tunjangan[date('mY', strtotime($que->date_shift))] = 0;
							else 
								$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] - $tunjanganpro[date('mY', strtotime($que->date_shift))];
						}
					}
					
					if($que->late!=0) {
						$late = $que->late;
						if($que->ot_after!=0) {
							if($que->ot_after>3600) 
								$telat = $que->late - 3600;
							else 
								$telat = $que->late - $que->ot_after;
							$late = $telat<=0?0:$telat;
						}

						/*
						 * versi lama

						if($late < 1860) $krglate = 0; //kurang dari 30 menit
						else if($late >= 1860 && $late < 3660) $krglate = 0.5; //31- 61 menit
						else if($late >= 3660 && $late < 5460) $krglate = 1; //61- 91 menit
						else if($late >= 5460) $krglate = 1.5; //91 menit ke atas
                        */

                        if($late < 3660) $krglate = 0.5; // < 61 menit
                        else if($late >= 3660) $krglate = 1; // >=61 menit ke atas


					}
					
					if($que->early_departure!=0) {
						$early = $que->early_departure;
						
						/*versi lama
						 * if($early > 1 && $early < 1860) $krgearly = 0.5;
						else if($early >= 1860 && $early < 3660) $krgearly = 1;
						else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
						else if($early >= 5460) $krgearly = 1.5;*/
                        if($early < 3660) $krgearly = 0.5;
                        else if($early >= 3660) $krgearly = 1;

					}
					
					/*if($check_in==null) $krglate = 1.5;
					if($check_out==null) $krgearly = 1.5;*/

                    if($check_in==null) $krglate = 1;
                    if($check_out==null) $krgearly = 1;
                    if($que->attendance == 'ALP') {
						$krglate = 0;
						$krgearly = 0;
						/*$krgalpa = 3;*/
                        $krgalpa = 5;
					}					
					
					if($que->attendance == 'BLNK') {
						$totalblnk++;
					}
					
					$s = 0;
					if(isset($bbar[$que->attendance])) {
						$krglate = 0;
						$krgearly = 0;
						$krgalpa = 0;
						$krgstatus = $pkrg[$que->attendance];
						$s = 1;
					}
					
					if(isset($atar[$que->attendance])) {
						if($que->attendance=='AT_AT4' || $que->attendance=='AT_AT3' || $que->attendance=='AT_AT1') 
							$krglate = 0;
						else if($que->attendance=='AT_AT5' || $que->attendance=='AT_AT2') 
							$krgearly = 0;
						else {	
							$krglate = 0;
							$krgearly = 0;
						}	
						$krgalpa = 0;
						$krgstatus = $pkrga[$que->attendance];
					}
					
					if($krglate > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1) {						
						$dataarray[] = array(
							'day'			=> $day,
							'date'			=> date('d-m-Y', strtotime($que->date_shift)),
							'status'		=> $que->check_in!=null?'Terlambat':'Tidak absen datang',
							'nilai'			=> $que->check_in==null?null:$this->report_model->itungan($late),
							'pengurangan'	=> $krglate,
							'total'			=> ($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
						);
						$totalpersenkurang = $totalpersenkurang + $krglate;
						$tunj = $tunj + (($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}					
					
					if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1) {
						$dataarray[] = array(
							'day'			=> $day,
							'date'			=> date('d-m-Y', strtotime($que->date_shift)),
							'status'		=> $que->check_out!=null?'Pulang lebih awal':'Tidak absen pulang',
							'nilai'			=> $que->check_out==null?null:$this->report_model->itungan($que->early_departure),
							'pengurangan'	=> $krgearly,
							'total'			=> ($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
						);
						$totalpersenkurang = $totalpersenkurang + $krgearly;
						$tunj = $tunj + (($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}
					if($krgalpa != 0 && $que->workinholiday!=1) {
						$dataarray[] = array(
							'day'			=> $day,
							'date'			=> date('d-m-Y', strtotime($que->date_shift)),
							'status'		=> 'Alpa',
							'nilai'			=> null,
							'pengurangan'	=> $krgalpa,
							'total'			=> ($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
						);
						$totalpersenkurang = $totalpersenkurang + $krgalpa;
						$totalalpa++;
						$tunj = $tunj + (($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}				
					if($krgstatus != 0) {
						$dataarray[] = array(
							'day'			=> $day,
							'date'			=> date('d-m-Y', strtotime($que->date_shift)),
							'status'		=> $s==1?$bbar[$que->attendance]:$atar[$que->attendance],
							'nilai'			=> null,
							'pengurangan'	=> $krgstatus,
							'total'			=> ($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))])
						);
						$totalpersenkurang = $totalpersenkurang + $krgstatus;
						if($que->attendance=='AB_12') $totalpembatalan++;
						$tunj = $tunj + (($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}		
					
					$totalpersensemua = $totalpersensemua + $totalpersenkurang;	
					$totalmasuk = array_sum($ttlmsk);	
					
					if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1) {
						if($tubel==1) {				
							$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
						} else {
							$totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];						
						}
					}
					
					if($que->flaghol == 1) {							
						if($tubel==1) {				
							$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
						} else {
							$totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];						
						}
					}
				}
				
				$tottun = array_sum($totaltunj);
				 
				if($tubel==1) {
					$dataarray[] = array(
						'day'			=> '',
						'date'			=> '',
						'status'		=> 'Tugas Belajar Per Tgl '.date('d-m-Y', $tgltubel),
						'nilai'			=> null,
						'pengurangan'	=> '50',
						'total'			=> $totaltubel
					);
					//$totalpersensemua = 50;	
					$tunj = $tunj + $totaltubel;
					$tottun = $tunjtubel;
				}
				 
				if($totalalpa == $totalmasuk) {
					$tottun = 0;
				}
				
				if($totalpembatalan == $totalmasuk) {
					$tottun = 0;
				}
				
				$totalsemua = $totalalpa + $totalpembatalan;
				
				if($totalsemua == $totalmasuk) {
					$tottun = 0;
				}
				
				if($totalblnk == $totaljadwal) {
					$tottun = 0;
				}				
				$dataallaye = array(
						'userid'   			=> $queqe['userid'],
						'empTitle' 			=> $queqe['empTitle'],
						'empID' 			=> $queqe['empID'],
						'empHire'			=> $queqe['empHire'],
						'empName' 			=> $queqe['empName'],
						'deptName' 			=> $queqe['deptName'],					
						'kelasjabatan' 		=> $queqe['kelasjabatan'],						
						'tunjangan' 		=> $tottun,
                        'tunjanganawal' 	=> $queqe['tunjanganawal']
				);
				
				$datafoot = array(
						'totalpersen'   => $totalpersensemua,
						'total'			=> $tottun==0?0:$tunj
				);
				
				$data = array(
					"dateinfo" 		=> date('d-m-Y', $datestart)." s/d ".date('d-m-Y', $datestop),
					"index" 		=> $abc,
					"cominfo" 		=> $company,
					"empinfo" 		=> $dataallaye,
					"footah" 		=> $datafoot,
					"data" 			=> $dataarray,
					"totaltunj"		=> $totaltunj,
					"excelid"		=> $excelid
				);		
				$abc++;
				
				if($excelid==1) {
					$dataview = $this->load->view("reptunjangan/tunjangan",$data,true);
					$datavw = $datavw.$dataview;
				} else {
					$this->load->view("reptunjangan/tunjangan",$data);
				}
			}
			if($excelid==1) {
				header("Content-type: application/x-msdownload");
				header("Content-Disposition: attachment; filename=tunjangankinerja.xls");
				echo "$datavw";				
			}
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function reprectunjangan()
	{
		if ($this->auth->is_logged_in()) {
			$postdatestart = $this->input->get('datestart');
			$postdatestop = $this->input->get('dateend');	
			$excelid = $this->input->get('excelid');		
			$userid = $this->input->get('userid');
			$datestart = strtotime($postdatestart);			
			$datestop = strtotime($postdatestop);
			$departemen = $this->input->get('orgid')!='undefined'?$this->input->get('orgid'):"1";
			
			$sql = "select * from mastertunjangan";
			$query = $this->db->query($sql);
			foreach($query->result() as $que) {
				$mastertunj[$que->kelasjabatan] = $que->tunjangan;
			}
			
			if($this->input->get('orgid')!='undefined')		
				$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
			else 
				$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			
			$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			$tbar = array();
			$bbar = array();
			$holar = array();
			
			$range = ($datestop - $datestart) / 86400;
			$totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;
			
			$pkrga = array();
			$attrecap = $this->report_model->getatt();
			foreach($attrecap->result() as $at) {
				$atar[$at->atid] = $at->atname;
				$attend[] = array(	
							'atid'		=> $at->atid,
							'atname'	=> $at->atname
						);
				$pkrga[$at->atid] = $at->value;
			}
			
			$pkrg = array();
			$absrecap = $this->report_model->getabs();
			foreach($absrecap->result() as $bs) {
				$bbar[$bs->abid] = $bs->abname;
				$absen[] = array(	
					'abid'		=> $bs->abid,
					'abname'	=> $bs->abname
				);						
				$pkrg[$bs->abid] = $bs->value;
			}
			
			$att = $this->report_model->getatt();
			foreach($att->result() as $tt) {
				$tbar[$tt->atid] = $tt->atname;
			}
			
			$dept = $this->report_model->getdept();
			foreach($dept->result() as $depat) {
				$deptar[$depat->deptid] = $depat->deptname;
			}
			
			$yo = 0;
			if($this->input->get('orgid')!='undefined') {
				$queryemp = $this->report_model->getorgemployeedetailsxx($orgid);
				$yo=1;
			}
			
			if($userid!='undefined') {
				$userar = explode(',', $userid);
				$queryemp = $this->report_model->getuseremployeedetailsxx($userar);
				$yo=2;
			}
			
			$aten = array();
			$aben = array();
			
			$dataallay = array();
			$dataallu = array();
			foreach($queryemp->result() as $queq) {
				$dataallay[] = array(
								'userid'   => $queq->userid,
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
								'kelasjabatan' => $queq->kelasjabatan,
								'golru' => $queq->golru,
								'tunjanganprofesi' => $queq->tunjanganprofesi,
								'tunjangan' => isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0,
								'jftstatus' => $queq->jftstatus,
								'jenisjabatan' => $queq->jenisjabatan,
								'jenispegawai' => $queq->jenispegawai,
								'kedudukan' => $queq->kedudukan
						);
				foreach($attend as $at3) {
					$aten[$queq->userid][$at3['atid']] = 0;
				}
				foreach($absen as $ab3) {
					$aben[$queq->userid][$ab3['abid']] = 0;
				}
			}
			$compa = $this->report_model->getcompany();
			$company = array(
					'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
					'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
					'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
					'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
					'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
					'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
			);				
			
			$dataallaye = array();
			$datafoot = array();
			$abc=0; 
			$dataview = '';
			$datavw = '';
			foreach($dataallay as $queqe) {
				$querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
				$querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
				$dataarray = array();
				$totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
				$totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
				$totallibur  = 0; $tubel = 0; $totaltubel = 0; $tunjtubel = 0; $tgltubel = 0;
				$totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;	
				$tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0;
				$ttlmsk = array(); $tunjangan = array();
				$tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
				$jft = array(); $jfto = 0; $jftp = array();
				$jpeg = array(); $jpego = 0; $jpegp = array();
				$kedu = array(); $keduo = 0; $kedup = array();
				
				foreach($querytempo->result() as $que) {	
					$ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
					$totaltunj[date('mY', strtotime($que->date_shift))] = 0;
				}
				
				foreach($querytempo->result() as $que) {					
					$kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
					if($kelas!=0) $queqe['kelasjabatan'] = $kelas;
					
					$tunjang = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);
					if($tunjang!=0) {
                        $queqe['tunjangan'] = $tunjang*0.5;
                        $queqe['tunjanganawal'] = $tunjang;
                    }
					
					if($que->attendance != 'NWK' && $que->attendance != 'NWDS'  && $que->workinholiday != 1) 
						$ttlmsk[date('mY', strtotime($que->date_shift))]++;
					
					if($que->flaghol == 1) 
						$ttlmsk[date('mY', strtotime($que->date_shift))]++;
					
					if($tunbuli!=$queqe['tunjangan'])
						$tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];					
					/* else 
						$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];	 */				
										
					$jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
					if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;
					
					if($jfto!=$queqe['jftstatus'])
						$jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];					
					/* else 
						$jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */
					
					$jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
					if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;
					
					if($jpego!=$queqe['jenispegawai'])
						$jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];					
					/* else 
						$jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */
					
					$kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
					if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;
					
					if($keduo!=$queqe['kedudukan'])
						$kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];					
					/* else 
						$kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */
					
					$tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
					if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;
					
					if($tunpro!=$queqe['tunjanganprofesi'])
						$tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];					
					/* else 
						$tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */					
					
					$tunbuli = $queqe['tunjangan'];
					$tunpro = $queqe['tunjanganprofesi'];					
					$jfto = $queqe['jftstatus'];					
					$jpego = $queqe['jenispegawai'];					
					$keduo = $queqe['kedudukan'];					
				}
				
				foreach($querytemp->result() as $que) {
					$late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
					$totalpersenkurang = 0; $totaljadwal++;
					$day = date('D', strtotime($que->date_shift));
					$date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
					if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
					if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));					
					
					if(isset($tunbul[date('mY', strtotime($que->date_shift))])) {
						if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
							$tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];						
							$tunjtubel = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
							//echo $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))];
						}
					} else {
						$tunjangan[date('mY', strtotime($que->date_shift))] = 0;
					}
				
					if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = 0;							
					}
					
					if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;							
						if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = 0;	
						
						if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4) {
							$tubel = 1;
							$tgltubel = strtotime($que->date_shift);
						}
					}
					
					if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 || $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = 0;							
					}					
									
					if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						$tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];						
						if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
							if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))])
								$tunjangan[date('mY', strtotime($que->date_shift))] = 0;
							else 
								$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] - $tunjanganpro[date('mY', strtotime($que->date_shift))];
						}
					}						
					
					if($que->late!=0) {
						$late = $que->late;
						if($que->ot_after!=0) {
							if($que->ot_after>3600) 
								$telat = $que->late - 3600;
							else 
								$telat = $que->late - $que->ot_after;
							$late = $telat<=0?0:$telat;
						}
						/*if($late < 1860) $krglate = 0;
						else if($late >= 1860 && $late < 3660) $krglate = 0.5;
						else if($late >= 3660 && $late < 5460) $krglate = 1;
						else if($late >= 5460) $krglate = 1.5;*/
                        if($late < 3660) $krglate = 0.5; // < 61 menit
                        else if($late >= 3660) $krglate = 1; // >=61 menit ke atas
					}
					
					if($que->early_departure!=0) {
						$early = $que->early_departure;
						
						/*if($early > 1 && $early < 1860) $krgearly = 0.5;
						else if($early >= 1860 && $early < 3660) $krgearly = 1;
						else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
						else if($early >= 5460) $krgearly = 1.5;*/
                        if($early < 3660) $krgearly = 0.5;
                        else if($early >= 3660) $krgearly = 1;
					}
					
					/*if($check_in==null) $krglate = 1.5;
					if($check_out==null) $krgearly = 1.5;*/
                    if($check_in==null) $krglate = 1;
                    if($check_out==null) $krgearly = 1;
					if($que->attendance == 'ALP') {
						$krglate = 0;
						$krgearly = 0;
						/*$krgalpa = 3;*/
                        $krgalpa = 5;
					}
					
					if($que->attendance == 'BLNK') {
						$totalblnk++;
					}
					
					if($que->attendance == 'NWK' || $que->attendance == 'NWDS' || $que->workinholiday == 1) {
						$totalmasuk--;
					}
					
					if(isset($bbar[$que->attendance])) {
						$krglate = 0;
						$krgearly = 0;
						$krgalpa = 0;
						$krgstatus = $pkrg[$que->attendance];
					}
					
					if(isset($atar[$que->attendance])) {
						if($que->attendance=='AT_AT4' || $que->attendance=='AT_AT3' || $que->attendance=='AT_AT1') 
							$krglate = 0;
						else if($que->attendance=='AT_AT5' || $que->attendance=='AT_AT2') 
							$krgearly = 0;
						else {	
							$krglate = 0;
							$krgearly = 0;
						}
						$krgalpa = 0;
						$krgstatus = $pkrga[$que->attendance];
					}
					
					if($krglate > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1) {
						$totalpersenkurang = $totalpersenkurang + $krglate;
						$tunj = $tunj + (($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}					
					
					if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1) {
						$totalpersenkurang = $totalpersenkurang + $krgearly;
						$tunj = $tunj + (($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}
					if($krgalpa != 0 && $que->workinholiday!=1) {						
						$totalalpa++;
						$totalpersenkurang = $totalpersenkurang + $krgalpa;
						$tunj = $tunj + (($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}				
					if($krgstatus != 0) {
						$totalpersenkurang = $totalpersenkurang + $krgstatus;
						if($que->attendance=='AB_12') $totalpembatalan++;
						$tunj = $tunj + (($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}					
					$totalpersensemua = $totalpersensemua + $totalpersenkurang;
					$totalmasuk = array_sum($ttlmsk);	
					
					if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday != 1) {
						if($tubel==1) {				
							$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
						} else {
							$totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];						
						}
					}
					
					if($que->flaghol == 1) {							
						if($tubel==1) {				
							$totaltubel = $totaltubel + $tunjangan[date('mY', strtotime($que->date_shift))];
						} else {
							$totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];						
						}
					}
				}
				
				$tottun = array_sum($totaltunj);
				 if($tubel==1) {
						
					$tunj = $tunj + $totaltubel;
					$tottun = $tunjtubel;
				}
				if($totalalpa == $totalmasuk) {
					$tottun = 0;
				}
				
				if($totalpembatalan == $totalmasuk) {
					$tottun = 0;
				}
				
				$totalsemua = $totalalpa + $totalpembatalan;
				
				if($totalsemua == $totalmasuk) {
					$tottun = 0;
				}
				
				if($totalblnk == $totaljadwal) {
					$tottun = 0;
				}	
				
				$dataallaye[] = array(
						'userid'   	=> $queqe['userid'],
						'empTitle' 	=> $queqe['empTitle'],
						'empID' 	=> $queqe['empID'],
						'empHire'	=> $queqe['empHire'],
						'empName' 	=> $queqe['empName'],
						'deptName' 	=> $queqe['deptName'],					
						'kelasjabatan' 			=> $queqe['kelasjabatan'],						
						'golongan' 				=> $queqe['golru'],						
						'tunjangan' 			=> $tottun,
                        'tunjanganawal' 		=> $queqe['tunjanganawal'],
						'totaltunjangan'		=> $tottun==0?0:$tunj
				);								
			}
			$data = array(
				"dateinfo" => date('d-m-Y', $datestart)." s/d ".date('d-m-Y', $datestop),
				"cominfo" => $company,
				"empinfo" => $dataallaye,
				"data" => $deptar[$departemen],
				"excelid"		=> $excelid
			);		
			
			if($excelid==1) {
				$dataview = $this->load->view("reptunjangan/rekaptunjangan",$data,true);
				$datavw = $datavw.$dataview;
			} else {
				$this->load->view("reptunjangan/rekaptunjangan",$data);
			}	
			if($excelid==1) {
				header("Content-type: application/x-msdownload");
				header("Content-Disposition: attachment; filename=rekaptunjangankinerja.xls");
				echo "$datavw";				
			}
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function repdetrekaptunjangan()
	{
		if ($this->auth->is_logged_in()) {
			$postdatestart = $this->input->get('datestart');
			$postdatestop = $this->input->get('dateend');	
			$excelid = $this->input->get('excelid');		
			$userid = $this->input->get('userid');
			$datestart = strtotime($postdatestart);			
			$datestop = strtotime($postdatestop);
			$departemen = $this->input->get('orgid')!='undefined'?$this->input->get('orgid'):"1";
			
			$sql = "select * from mastertunjangan";
			$query = $this->db->query($sql);
			foreach($query->result() as $que) {
				$mastertunj[$que->kelasjabatan] = $que->tunjangan;
			}
			
			if($this->input->get('orgid')!='undefined')		
				$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
			else 
				$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			
			$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			$tbar = array();
			$bbar = array();
			$holar = array();
			
			$range = ($datestop - $datestart) / 86400;
			$totalworkday = 0; $totalholiday = 0; $totalnonworkingday = 0; $holonnwds = 0;
			
			$pkrga = array();
			$attrecap = $this->report_model->getatt();
			foreach($attrecap->result() as $at) {
				$atar[$at->atid] = $at->atname;
				$attend[] = array(	
							'atid'		=> $at->atid,
							'atname'	=> $at->atname
						);
				$pkrga[$at->atid] = $at->value;
			}
			
			$pkrg = array();
			$absrecap = $this->report_model->getabs();
			foreach($absrecap->result() as $bs) {
				$bbar[$bs->abid] = $bs->abname;
				$absen[] = array(	
					'abid'		=> $bs->abid,
					'abname'	=> $bs->abname
				);						
				$pkrg[$bs->abid] = $bs->value;
			}
			
			$att = $this->report_model->getatt();
			foreach($att->result() as $tt) {
				$tbar[$tt->atid] = $tt->atname;
			}
			
			$dept = $this->report_model->getdept();
			foreach($dept->result() as $depat) {
				$deptar[$depat->deptid] = $depat->deptname;
			}
			
			$yo = 0;
			if($this->input->get('orgid')!='undefined') {
				$queryemp = $this->report_model->getorgemployeedetailsxx($orgid);
				$yo=1;
			}
			
			if($userid!='undefined') {
				$userar = explode(',', $userid);
				$queryemp = $this->report_model->getuseremployeedetailsxx($userar);
				$yo=2;
			}
			
			$aten = array();
			$aben = array();
			
			$dataallay = array();
			$dataallu = array();
			foreach($queryemp->result() as $queq) {
				$dataallay[] = array(
								'userid'   => $queq->userid,
								'empTitle' => $queq->title,
								'empID' => $queq->badgenumber,
								'empHire' => isset($queq->hireddate)?date('d-m-Y', strtotime($queq->hireddate)):'',
								'empName' => $queq->name,
								'deptName' => isset($deptar[$queq->deptid])?$deptar[$queq->deptid]:'',
								'kelasjabatan' => $queq->kelasjabatan,
								'golru' => $queq->golru,
								'tunjanganprofesi' => $queq->tunjanganprofesi,
								'tunjangan' => isset($mastertunj[$queq->kelasjabatan])?$mastertunj[$queq->kelasjabatan]:0,
								'jftstatus' => $queq->jftstatus,
								'jenisjabatan' => $queq->jenisjabatan,
								'jenispegawai' => $queq->jenispegawai,
								'kedudukan' => $queq->kedudukan
						);
				foreach($attend as $at3) {
					$aten[$queq->userid][$at3['atid']] = 0;
				}
				foreach($absen as $ab3) {
					$aben[$queq->userid][$ab3['abid']] = 0;
				}
			}
			$compa = $this->report_model->getcompany();
			$company = array(
					'companyname'	=> isset($compa->row()->companyname)?$compa->row()->companyname:'',
					'logo'			=> isset($compa->row()->logo)?$compa->row()->logo:'',
					'address1'		=> isset($compa->row()->address1)?$compa->row()->address1:'',
					'address2'		=> isset($compa->row()->address2)?$compa->row()->address2:'',
					'phone'			=> isset($compa->row()->phone)?$compa->row()->phone:'',
					'fax'			=> isset($compa->row()->fax)?$compa->row()->fax:''
			);				
			
			$dataallaye = array();
			$datafoot = array();
			$abc=0; 
			$dataview = '';
			$datavw = '';
			foreach($dataallay as $queqe) {
				$querytempo = $this->report_model->getattlogo($datestart, $datestop, $queqe['userid']);
				$querytemp = $this->report_model->getattlog($datestart, $datestop, $queqe['userid']);
				$dataarray = array();
				$totallate=0; $totalearly=0; $totalotbef=0; $totalotaf=0; $total=0;
				$totallater=0; $totalearlyr=0; $totalotr=0; $totalr=0; $workinholiday=0; $attendance=0; $absence=0; $off=0; $alpha=0;$editcome=0;$edithome=0;$workday=0;
				$totallibur  = 0;
				$totalpersensemua = 0; $totalalpa = 0; $totalmasuk = 0; $totalpembatalan = 0; $totaljadwal=0; $totalblnk=0; $totalsemua = 0;	
				$tunbul = array(); $tunbuli = 0; $tottun = 0; $tunj = 0;
				$ttlmsk = array(); $tunjangan = array();
				$tunprobul = array(); $tunpro = 0; $tunjanganpro = array();
				$jft = array(); $jfto = 0; $jftp = array();
				$jpeg = array(); $jpego = 0; $jpegp = array();
				$kedu = array(); $keduo = 0; $kedup = array();
				
				foreach($querytempo->result() as $que) {	
					$ttlmsk[date('mY', strtotime($que->date_shift))] = 0;
					$totaltunj[date('mY', strtotime($que->date_shift))] = 0;
				}
				
				foreach($querytempo->result() as $que) {					
					$kelas = $this->report_model->getkelasjabatan($queqe['userid'], $que->date_shift);
					if($kelas!=0) $queqe['kelasjabatan'] = $kelas;
					
					$tunjang = $this->report_model->gettunjang($que->date_shift, $queqe['kelasjabatan']);
					if($tunjang!=0) {
                        $queqe['tunjanganawal'] = $tunjang;
					    $queqe['tunjangan'] = $tunjang*0.5;
                    }
					
					if($que->attendance != 'NWK' && $que->attendance != 'NWDS'  && $que->workinholiday != 1) 
						$ttlmsk[date('mY', strtotime($que->date_shift))]++;
					
					if($que->flaghol == 1) 
						$ttlmsk[date('mY', strtotime($que->date_shift))]++;
					
					if($tunbuli!=$queqe['tunjangan'])
						$tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjangan'];					
					/* else 
						$tunbul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjangan'];	 */				
										
					$jftpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 1);
					if($jftpeg!=0) $queqe['jftstatus'] = $jftpeg;
					
					if($jfto!=$queqe['jftstatus'])
						$jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jftstatus'];					
					/* else 
						$jft[date('mY', strtotime($que->date_shift))][1] = $queqe['jftstatus']; */
					
					$jpegpeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 2);
					if($jpegpeg!=0) $queqe['jenispegawai'] = $jpegpeg;
					
					if($jpego!=$queqe['jenispegawai'])
						$jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['jenispegawai'];					
					/* else 
						$jpeg[date('mY', strtotime($que->date_shift))][1] = $queqe['jenispegawai']; */
					
					$kedupeg = $this->report_model->getjenispeghis($queqe['userid'], $que->date_shift, 3);
					if($kedupeg!=0) $queqe['kedudukan'] = $kedupeg;
					
					if($keduo!=$queqe['kedudukan'])
						$kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['kedudukan'];					
					/* else 
						$kedu[date('mY', strtotime($que->date_shift))][1] = $queqe['kedudukan']; */
					
					$tunjangprof = $this->report_model->gettunjangprof($queqe['userid'], $que->date_shift);
					if($tunjangprof!=0) $queqe['tunjanganprofesi'] = $tunjangprof;
					
					if($tunpro!=$queqe['tunjanganprofesi'])
						$tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] = $queqe['tunjanganprofesi'];					
					/* else 
						$tunprobul[date('mY', strtotime($que->date_shift))][1] = $queqe['tunjanganprofesi']; */					
					
					$tunbuli = $queqe['tunjangan'];
					$tunpro = $queqe['tunjanganprofesi'];					
					$jfto = $queqe['jftstatus'];					
					$jpego = $queqe['jenispegawai'];					
					$keduo = $queqe['kedudukan'];					
				}
				
				foreach($querytemp->result() as $que) {
					$late=0;$early=0;$telat=0;$krglate=0;$krgearly=0;$krgalpa=0;$krgstatus=0;
					$totalpersenkurang = 0; $totaljadwal++;
					$day = date('D', strtotime($que->date_shift));
					$date_in = null; $check_in = null; $break_out = null; $break_in = null; $date_out = null; $check_out = null;
					if(isset($que->check_in)) $check_in = date('H:i:s', strtotime($date_in.' '.$que->check_in));
					if(isset($que->check_out)) $check_out = date('H:i:s', strtotime($date_out.' '.$que->check_out));					
					
					if(isset($tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						$tunjangan[date('mY', strtotime($que->date_shift))] = $tunbul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];						
					}
				
					if(isset($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						if($jft[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] != 2)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = 0;							
					}
					
					if(isset($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 5 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] / 2;							
						if($kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 2 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 6 || $kedu[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 12)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = 0;	
					}
					
					if(isset($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						if($jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 3 || $jpeg[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] == 4)	
							$tunjangan[date('mY', strtotime($que->date_shift))] = 0;							
					}					
									
					if(isset($tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))])) {
						$tunjanganpro[date('mY', strtotime($que->date_shift))] = $tunprobul[date('mY', strtotime($que->date_shift))][date('j', strtotime($que->date_shift))] / $ttlmsk[date('mY', strtotime($que->date_shift))];						
						if($queqe['tunjanganprofesi']!=null || $queqe['tunjanganprofesi']!=0) {
							if($tunjanganpro[date('mY', strtotime($que->date_shift))] >= $tunjangan[date('mY', strtotime($que->date_shift))])
								$tunjangan[date('mY', strtotime($que->date_shift))] = 0;
							else 
								$tunjangan[date('mY', strtotime($que->date_shift))] = $tunjangan[date('mY', strtotime($que->date_shift))] - $tunjanganpro[date('mY', strtotime($que->date_shift))];
						}
					}						
					
					if($que->late!=0) {
						$late = $que->late;
						if($que->ot_after!=0) {
							if($que->ot_after>3600) 
								$telat = $que->late - 3600;
							else 
								$telat = $que->late - $que->ot_after;
							$late = $telat<=0?0:$telat;
						}
						/*if($late < 1860) $krglate = 0;
						else if($late >= 1860 && $late < 3660) $krglate = 0.5;
						else if($late >= 3660 && $late < 5460) $krglate = 1;
						else if($late >= 5460) $krglate = 1.5;*/
                        if($late < 3660) $krglate = 0.5; // < 61 menit
                        else if($late >= 3660) $krglate = 1; // >=61 menit ke atas
					}
					
					if($que->early_departure!=0) {
						$early = $que->early_departure;
						
						/*if($early > 1 && $early < 1860) $krgearly = 0.5;
						else if($early >= 1860 && $early < 3660) $krgearly = 1;
						else if($early >= 3660 && $early < 5460) $krgearly = 1.25;
						else if($early >= 5460) $krgearly = 1.5;*/
                        if($early < 3660) $krgearly = 0.5;
                        else if($early >= 3660) $krgearly = 1;
					}
					
					/*if($check_in==null) $krglate = 1.5;
					if($check_out==null) $krgearly = 1.5;*/
                    if($check_in==null) $krglate = 1;
                    if($check_out==null) $krgearly = 1;
					if($que->attendance == 'ALP') {
						$krglate = 0;
						$krgearly = 0;
						/*$krgalpa = 3;*/
                        $krgalpa = 5;
					}
					
					if($que->attendance == 'BLNK') {
						$totalblnk++;
					}
					
					if($que->attendance == 'NWK' || $que->attendance == 'NWDS' || $que->workinholiday == 1) {
						$totalmasuk--;
					}
					
					if(isset($bbar[$que->attendance])) {
						$krglate = 0;
						$krgearly = 0;
						$krgalpa = 0;
						$krgstatus = $pkrg[$que->attendance];
					}
					
					if(isset($atar[$que->attendance])) {
						if($que->attendance=='AT_AT4' || $que->attendance=='AT_AT3' || $que->attendance=='AT_AT1') 
							$krglate = 0;
						else if($que->attendance=='AT_AT5' || $que->attendance=='AT_AT2') 
							$krgearly = 0;
						else {	
							$krglate = 0;
							$krgearly = 0;
						}
						$krgalpa = 0;
						$krgstatus = $pkrga[$que->attendance];
					}
					
					if($krglate > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1) {
						$totalpersenkurang = $totalpersenkurang + $krglate;
						$tunj = $tunj + (($krglate /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}					
					
					if($krgearly > 0 && $que->attendance!='NWK' && $que->attendance!='NWDS' && $que->workinholiday!=1) {
						$totalpersenkurang = $totalpersenkurang + $krgearly;
						$tunj = $tunj + (($krgearly /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}
					if($krgalpa != 0 && $que->workinholiday!=1) {						
						$totalalpa++;
						$totalpersenkurang = $totalpersenkurang + $krgalpa;
						$tunj = $tunj + (($krgalpa /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}				
					if($krgstatus != 0) {
						$totalpersenkurang = $totalpersenkurang + $krgstatus;
						if($que->attendance=='AB_12') $totalpembatalan++;
						$tunj = $tunj + (($krgstatus /100) * ($tunjangan[date('mY', strtotime($que->date_shift))] * $ttlmsk[date('mY', strtotime($que->date_shift))]));
					}					
					$totalpersensemua = $totalpersensemua + $totalpersenkurang;
					$totalmasuk = array_sum($ttlmsk);	
					
					if($que->attendance != 'NWK' && $que->attendance != 'NWDS' && $que->workinholiday!=1) {
						$totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];						
					}		
					
					if($que->flaghol == 1) {
						$totaltunj[date('mY', strtotime($que->date_shift))] = $totaltunj[date('mY', strtotime($que->date_shift))] + $tunjangan[date('mY', strtotime($que->date_shift))];						
					}
				}
				
				$tottun = array_sum($totaltunj);
				 
				if($totalalpa == $totalmasuk) {
					$tottun = 0;
				}
				
				if($totalpembatalan == $totalmasuk) {
					$tottun = 0;
				}
				
				$totalsemua = $totalalpa + $totalpembatalan;
				
				if($totalsemua == $totalmasuk) {
					$tottun = 0;
				}
				
				if($totalblnk == $totaljadwal) {
					$tottun = 0;
				}	
				
				$dataallaye[] = array(
						'userid'   	=> $queqe['userid'],
						'empTitle' 	=> $queqe['empTitle'],
						'empID' 	=> $queqe['empID'],
						'empHire'	=> $queqe['empHire'],
						'empName' 	=> $queqe['empName'],
						'deptName' 	=> $queqe['deptName'],					
						'kelasjabatan' 			=> $queqe['kelasjabatan'],						
						'golongan' 				=> $queqe['golru'],						
						'tunjangan' 			=> $tottun,
                    'tunjanganawal' 			=> $queqe["tunjanganawal"],
						'totaltunjangan'		=> $tottun==0?0:$tunj
				);								
			}
			$data = array(
				"dateinfo" => date('d-m-Y', $datestart)." s/d ".date('d-m-Y', $datestop),
				"cominfo" => $company,
				"empinfo" => $dataallaye,
				"data" => $deptar[$departemen],
				"excelid"		=> $excelid
			);		
			
			if($excelid==1) {
				$dataview = $this->load->view("reptunjangan/detilrekaptunjangan",$data,true);
				$datavw = $datavw.$dataview;
			} else {
				$this->load->view("reptunjangan/detilrekaptunjangan",$data);
			}	
			if($excelid==1) {
				header("Content-type: application/x-msdownload");
				header("Content-Disposition: attachment; filename=detilrekaptunjangankinerja.xls");
				echo "$datavw";				
			}
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function saverecapkehadirantemp()
	{
		$start_date = $_GET['datestart'];
		$end_date = $_GET['dateend'];
		$excelid = $_GET['excelid'];
		$simple = $_GET['simple'];
		$periode = $this->lang->line('periode').' : '.date("d F Y",strtotime($start_date)).' - '.date("d F Y",strtotime($end_date));
		$arr_days = $this->createDateRangeArray($start_date,$end_date);
		
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
		
		
		
		if($this->input->get('orgid')!='undefined')	{
			$orgid = $this->pegawai->deptonall($this->input->get('orgid'));
			$this->db->select('deptname');
			$this->db->from('departments');
			$this->db->where('deptid', $this->input->get('orgid'));
			$query = $this->db->get();
			$namadept = $query->row()->deptname;
		} else {
			$orgid = $this->session->userdata('user_dept')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			$namadept = 'Semua';
		}
		
		if(!empty($orgid)) {
			foreach($orgid as $org) 
				$orga[] = "'".$org."'";
			$orgaidi = implode(',', $orga);
			$with_user_id = "deptid IN (".$orgaidi.") ";
			$tambahan = "deptid IN (".$orgaidi.") ";
		}
			
		if(isset($_GET['userid']) && $_GET['userid']!='undefined') {
			$useraidi = explode(',',substr($_GET['userid'],0,-1));
			foreach($useraidi as $usr)
				$usernya[] = "'".$usr."'";
			$with_user_id = "userid IN (".implode(',',$usernya).") ";
			$tambahan = "a.userid IN (".implode(',',$usernya).") ";
		}
			
		$sqlcok = "select a.userid, date_shift, check_in, check_out, attendance, workinholiday, late, early_departure
						from process a join userinfo b on a.userid=b.userid 
						where ".$tambahan."and date_shift >= '".$this->db->escape_str($start_date)."' and date_shift <= '".$this->db->escape_str($end_date)."'";
		$querycok = $this->db->query($sqlcok);		
		
		if($simple==1) {
			$query_att_perdate = "SELECT userid, name, deptname, title, golru, kelasjabatan
														FROM userinfo JOIN departments USING(deptid) 
														WHERE ".$with_user_id."
														GROUP BY userid, name, deptname, title, eselon, golru, kelasjabatan, userinfo.id, parentid ORDER BY eselon, golru desc, kelasjabatan desc";  
		} else {
			$query_att_perdate = "SELECT userid,badgenumber,name, deptname, title 
														FROM userinfo JOIN departments USING(deptid) 
														WHERE ".$with_user_id."
														GROUP BY userid,badgenumber,name, deptname, title, eselon, golru, kelasjabatan ORDER BY eselon, golru desc, kelasjabatan desc ";  
		}
		$group_per_date = $this->db->query($query_att_perdate); 
			
		$data = array(
			"cominfo" => $company,
			"periode" => $periode,
			"arr_days" => $arr_days,
			"querycok" => $querycok,
			"group_per_date" => $group_per_date,
			"nama_dept"	=> $namadept
		);	
		if($simple==1) {
			if($excelid==1) {
				header("Content-type: application/x-msdownload");
				header("Content-Disposition: attachment; filename=recapkehadiransimple.xls");			
			}
			$this->load->view("recap/recapkehadiransimple",$data);
		} else {
			if($excelid==1) {
				header("Content-type: application/x-msdownload");
				header("Content-Disposition: attachment; filename=recapkehadiran.xls");			
			}
			$this->load->view("recap/recapkehadiran",$data);

		}
	}/* 
	
	public function getsimpeg() {
		$secdb = $this->load->database('second', true);
		$sql = "insert into tr_kehadiran (nip, tanggal, datang, pulang, islibur) values ('197512151995111001', '2016-02-29', '09:00:00', '15:00:00', 0)";
		$query = $secdb->query($sql);
		echo "OK";
	}  */
}
