<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Device extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('device_model');
        $this->load->model('setting_model');
        $this->load->model('employe_model');

    }

    public function index()
    {
        redirect('home');
    }
	
	function hasHtml($str){
		//we compare the length of the string with html tags and without html tags
		if(strlen($str) != strlen(strip_tags($str)))
			return true;  
		return false;
	}
	
	public function getip() 
	{
		echo $this->ipaddress->get_ip();
	}
	
	public function load_area()
    {          
		$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
		$query = $this->device_model->getwhereinarea($areaid);
		$refs = array();
		$list = array();
		$listing = array();
		$a=0;
		
		foreach($query->result() as $data) {
			$thisref = &$refs[ $data->areaid ];	
			if ($data->parent_id == "") {
				if($this->device_model->getwhichareaparent($data->areaid)) {
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaid.' - '.$data->areaname,
						'expanded'	=>'true'
					);
				} else {						
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaid.' - '.$data->areaname,
						'leaf'		=>'true'
					);
				}
				$a++;
				$list[ ] = &$thisref;					
			} else {
				if($this->device_model->getwhichareaparent($data->areaid)) {
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaid.' - '.$data->areaname,
						'expanded'	=>'true'
					);
				} else {						
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaid.' - '.$data->areaname,
						'leaf'		=>'true'
					);
				}
				$refs[ $data->parent_id ]['children'] [ ]= &$thisref;
			}			
			$listing[ ] = &$thisref;
		}  
		if($a>0) 
			echo json_encode($list);
		else 				
			echo json_encode($listing);
    }
	
	public function load_areatree()
    {          
		$userid = explode(',',substr($this->input->post('userid'),0,-1));
		$areauser = array();
		if(!empty($userid)) {
			$areauserid = $this->device_model->getareauserid($userid);			
			foreach($areauserid->result() as $aui) {
				$areauser[] = $aui->areaid;
			}
		}
		$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
		$query = $this->device_model->getwhereinarea($areaid);
		
		$refs = array();
		$list = array();
		$listing = array();
		$a=0;
		
		foreach($query->result() as $data) {
			$thisref = &$refs[ $data->areaid ];	
			if ($data->parent_id == "") {
				if($this->device_model->getwhichareaparent($data->areaid)) {
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaid.' - '.$data->areaname,
						'expanded'	=>true,
						'checked'	=>in_array($data->areaid, $areauser)?true:false
					);
				} else {						
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaid.' - '.$data->areaname,
						'leaf'		=>true,
						'checked'	=>in_array($data->areaid, $areauser)?true:false
					);
				}
				$a++;
				$list[ ] = &$thisref;					
			} else {
				if($this->device_model->getwhichareaparent($data->areaid)) {
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaid.' - '.$data->areaname,
						'expanded'	=>true,
						'checked'	=>in_array($data->areaid, $areauser)?true:false
					);
				} else {						
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaid.' - '.$data->areaname,
						'leaf'		=>true,
						'checked'	=>in_array($data->areaid, $areauser)?true:false
					);
				}
				$refs[ $data->parent_id ]['children'] [ ]= &$thisref;
			}
			$listing[ ] = &$thisref;
		}  
		if($a>0) 
			echo json_encode($list);
		else 				
			echo json_encode($listing);
    }
	
	public function load_areaforuser()
    {          
		$areauser = array();
		if($this->input->post('id')) {
			$this->db->select('area_id');
			$this->db->from('users');
			$this->db->where('id', $this->input->post('id'));
			$query = $this->db->get();		
			$areauser = explode(',',$query->row()->area_id);
		};
		$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
		$query = $this->device_model->getwhereinarea($areaid);
		
		$refs = array();
		$list = array();
		$listing = array();
		$a=0;
		
		foreach($query->result() as $data) {
			$thisref = &$refs[ $data->areaid ];	
			if ($data->parent_id == "") {
				if($this->device_model->getwhichareaparent($data->areaid)) {
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaname,
						'expanded'	=>true,
						'checked'	=>in_array($data->areaid, $areauser)?true:false
					);
				} else {						
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaname,
						'leaf'		=>true,
						'checked'	=>in_array($data->areaid, $areauser)?true:false
					);
				}
				$a++;
				$list[ ] = &$thisref;					
			} else {
				if($this->device_model->getwhichareaparent($data->areaid)) {
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaname,
						'expanded'	=>true,
						'checked'	=>in_array($data->areaid, $areauser)?true:false
					);
				} else {						
					$thisref = array(
						'id' 		=>$data->areaid,
						'text'		=>$data->areaname,
						'leaf'		=>true,
						'checked'	=>in_array($data->areaid, $areauser)?true:false
					);
				}
				$refs[ $data->parent_id ]['children'] [ ]= &$thisref;
			}
			$listing[ ] = &$thisref;
		}  
		if($a>0) 
			echo json_encode($list);
		else 				
			echo json_encode($listing);
    }
	
    public function get_alldevice()
    {
		if ($this->auth->is_logged_in()) {
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
			if(isset($_REQUEST['status']) && $_REQUEST['status']!='')
				$status = $_REQUEST['status'];
			else 
				$status = 3;
			$areacombo = $this->input->post('areaid');
			
			if(!empty($areacombo))
				$areaid = $this->device_model->areaonall($areacombo);
			else 
				$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();

			$block1 = str_replace("[", "", $_REQUEST['sort']);
			$block2 = str_replace("]", "", $block1);
			
			$postData = '{"datasort":'.$block2.'}';
			$postDataJson = json_decode($postData);
			
			if(isset($_REQUEST['temukan'])) {
				if($_REQUEST['temukan']!='') 
					$hasil = $this->device_model->getalldevicefind($areaid, $_REQUEST['temukan'], $postDataJson->datasort->property, $postDataJson->datasort->direction, $status);
				else 
					$hasil = $this->device_model->getalldevice($areaid, $start, $limit, $postDataJson->datasort->property, $postDataJson->datasort->direction, $status);			
			} else {
				$hasil = $this->device_model->getalldevice($areaid, $start, $limit, $postDataJson->datasort->property, $postDataJson->datasort->direction, $status);
			}	
			
			$results = $this->device_model->getdevcount($areaid);
			$data_arr = array();
			foreach($hasil->result() as $data) 
			{
				$last = strtotime($data->lastactivity);
				$cur = strtotime(date("Y-m-d H:i:s"));
				$avg = $cur - $last;	
				$stat = array('status' => 0);						
				if($this->device_model->getstatus($data->sn)!=2) {
					if($avg>($data->delay+120)) {
						$this->db->update('iclock', $stat, array('sn'=>$data->sn));
					}
				}
					
				$data_arr[] = array(
					'sn' 				=> $data->sn,
					'alias' 			=> $data->alias,
					'status' 			=> $data->status,
					'lastactivity' 		=> $data->lastactivity,
					'ipaddress' 		=> $data->ipaddress,
					'areaid' 			=> $this->device_model->getarea($data->areaid),
					'user_count' 		=> $data->user_count,
					'fp_count' 			=> $data->fp_count.' / '.$data->max_finger_count*100,
					'transaction_count' => $data->transaction_count.' / '.$data->max_attlog_count*10000,
					'errdelay' 			=> $data->errdelay,
					'delay' 			=> $data->delay,
					'timezone' 			=> $data->timezone,
					'terminal_id' 		=> $data->terminal_id
				);
			}
			
			$fp = fopen('assets/js/template/temporary.json', 'w');
			fwrite($fp, json_encode(array('data'=>'copy')));
			fclose($fp);	
			
			echo '{success:true,results:'. $results .',data:'.json_encode($data_arr).'}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
	
	public function delete_device()
    {
		$hasil = array("success" => false, "responseText" => "success");
		echo json_encode($hasil);
    }	
	
	public function deldevice()
    {		
		if ($this->auth->is_logged_in()) {
			$snlama = $this->input->post('SN');		
			$snbaru = $this->input->post('snbaru');	
			$dataupdate = array('sn' => $snbaru);
			$this->db->where('sn', $snlama);		
			if ($this->db->update('iclock', $dataupdate)) {
				$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> 'ganti sn mesin '.$snlama.' ke '.$snbaru,
						'info'			=> $this->lang->line('message_success')
				);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "success");		
			} else {
				$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> 'ganti sn mesin '.$snlama.' ke '.$snbaru,
						'info'			=> $this->lang->line('message_errdelete')
				);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "success");
			}		
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function change_area()
    {
		if ($this->auth->is_logged_in()) {
			$SN = explode(',', $this->input->post('SN'));
			$areaid = $this->input->post('areaid');
			$jmlsn = count($SN) - 1;
			for($i=0;$i<$jmlsn;$i++) {
				if($this->device_model->getsnarea($SN[$i])!=$areaid) {
					/*$com = array (
										'sn'			=>$SN[$i],
										'cmd'			=>'CLEAR DATA',
										'st'			=>1,
										'status'		=>1,
										'submittime'	=>date("Y-m-d H:i:s")
									);							
					$this->device_model->save('command', $com);	*/
					
					$comm = array (
										'sn'			=>$SN[$i],
										'cmd'			=>'CHECK',
										'st'			=>1,
										'status'		=>1,
										'submittime'	=>date("Y-m-d H:i:s")
									);							
					$this->device_model->save('command', $comm);
				}
				
				$savedata = array (
							'areaid'			=> $areaid
						);
				$st = $this->device_model->update('sn', $SN[$i], 'iclock', $savedata);
			}			
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_changearea').' '.implode(',', $SN),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
	
	public function edit_device()
    {
		if ($this->auth->is_logged_in()) {
			$postData = '{"data":'.$_POST['data'].'}';
			$postDataJson = json_decode($postData);
			
			$terminid = $this->device_model->getterminalid($postDataJson->data->sn);
			$savedata = array (
				'alias'				=> $postDataJson->data->alias,
				'errdelay'			=> $postDataJson->data->errdelay,
				'delay'				=> $postDataJson->data->delay,
				'timezone'			=> $postDataJson->data->timezone
			);
			$st = $this->device_model->update('sn', $postDataJson->data->sn, 'iclock', $savedata);
				
			$com = array (
				'sn'			=>$postDataJson->data->sn,
				'cmd'			=>'CHECK',
				'status'		=>1,
				'submittime'	=>date("Y-m-d H:i:s")
			);							
			$this->device_model->save('command', $com);
			
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> $this->lang->line('device_log_editdevice').' '.$postDataJson->data->sn,
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => false, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
    
    public function deletetrans()
    {
		if ($this->auth->is_logged_in()) {
			$tanggal = strtotime($_POST['deltanggal']) + 86400;
			$sn = explode(',', $_POST['SN']);
			/*$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$arransemen = array (
					'sn'			=>$sn[$i],
					'cmd'			=>'CLEAR ATTLOG BY TIME '.date('YmdHis', $tanggal),
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);	
				$this->db->insert('command', $arransemen);
			}
			*/
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_deletetrans').' '.implode(',', $sn),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function delalllog()
    {
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['SN']);
			/*$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$arransemen = array (
					'sn'			=>$sn[$i],
					'cmd'			=>'CLEAR LOG',
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);	
				$this->db->insert('command', $arransemen);
			}
			*/
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_deletealltrans').' '.implode(',', $sn),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function delphotolog()
    {
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['SN']);
			/*$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$arransemen = array (
					'sn'			=>$sn[$i],
					'cmd'			=>'CLEAR PHOTO',
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);	
				$this->db->insert('command', $arransemen);
			}			
			*/
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_delphototrans').' '.implode(',', $sn),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function synchronizing()
    {
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['SN']);
			$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$comm = array (
							'sn'			=>$sn[$i],
							'cmd'			=>'CHECK',
							'st'			=>1,
							'status'		=>1,
							'submittime'	=>date("Y-m-d H:i:s")
						);		
				$this->db->insert('command', $comm);
			}			
			
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_synchronize').' '.implode(',', $sn),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }	
	}
	
	public function cleardata()
    {		
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['SN']);
			/*$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$arransemen = array (
					'sn'			=>$sn[$i],
					'cmd'			=>'CLEAR DATA',
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);	
				$this->db->insert('command', $arransemen);
			}
			*/
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_cleardata').' '.implode(',', $sn),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}	
	
	public function downloaddata()
    {		
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['SN']);
			$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$updat = array (
							'stamp'	  =>0
						);
				$this->db->where('sn', $sn[$i]);
				$this->db->update('iclock', $updat);
				
				$arransemen = array (
							'sn'			=>$sn[$i],
							'cmd'			=>'CHECK',
							'status'		=>1,
							'submittime'	=>date("Y-m-d H:i:s")
						);	
				$this->db->insert('command', $arransemen);
			}
						
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> $this->lang->line('device_log_downlog').' '.implode(',', $sn),
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function downloaddatauser()
    {		
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['SN']);
			$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$updat = array (
							'opstamp'	  =>0
						);
				$this->db->where('sn', $sn[$i]);
				$this->db->update('iclock', $updat);
				
				$arransemen = array (
					'sn'			=>$sn[$i],
					'cmd'			=>'CHECK',
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);	
				$this->db->insert('command', $arransemen);
			}		
			
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> $this->lang->line('device_log_downuser').' '.implode(',', $sn),
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function downloaddataphoto()
    {		
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['SN']);
			$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$updat = array (
							'photostamp'	  =>0
						);
				$this->db->where('sn', $sn[$i]);
				$this->db->update('iclock', $updat);
				
				$arransemen = array (
							'sn'			=>$sn[$i],
							'cmd'			=>'CHECK',
							'status'		=>1,
							'submittime'	=>date("Y-m-d H:i:s")
						);	
				$this->db->insert('command', $arransemen);
			}
			
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_downphoto').' '.implode(',', $sn),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function devinfo()
    {		
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['SN']);
			$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$arransemen = array (
					'sn'			=>$sn[$i],
					'cmd'			=>'INFO',
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);	
				$this->db->insert('command', $arransemen);
			}
			
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_info').' '.implode(',', $sn),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function reboot()
    {		
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['SN']);
			$jmldata = count($sn) - 1;
			
			for($i=0;$i<$jmldata;$i++) {
				$arransemen = array (
					'sn'			=>$sn[$i],
					'cmd'			=>'REBOOT',
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);	
				$this->db->insert('command', $arransemen);
			}
			
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_reboot').' '.implode(',', $sn),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function get_allarea()
    {
		if ($this->auth->is_logged_in()) {
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
			
			$block1 = str_replace("[", "", $_REQUEST['sort']);
			$block2 = str_replace("]", "", $block1);
			
			$postData = '{"datasort":'.$block2.'}';
			$postDataJson = json_decode($postData);
			
			$areacombo = $this->input->post('areaid');

			if(!empty($areacombo))
				$areaid = $this->device_model->areaonall($areacombo);
			else 
				$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			if(isset($_REQUEST['temukan'])) {
				if($_REQUEST['temukan']!='')  {
					$arearesult = $this->device_model->getallareafind($areaid, $_REQUEST['temukan'], $postDataJson->datasort->property, $postDataJson->datasort->direction);		
				} else  {
					$arearesult = $this->device_model->getallarea($areaid, $start, $limit, $postDataJson->datasort->property, $postDataJson->datasort->direction);
				}
			} else {
				$arearesult = $this->device_model->getallarea($areaid, $start, $limit, $postDataJson->datasort->property, $postDataJson->datasort->direction);
			}
			
			$results = $this->device_model->getareacount($areaid);
			
			$data_area = array();
			foreach($arearesult->result() as $data) 
			{
				$parentname = '';
				if($data->parent_id!='') {
					$parentname = $this->device_model->getparentname($data->parent_id);
				}	
				
				$data_area[] = array(
					'areaid' => $data->areaid,
					'areaname' => $data->areaname,
					'parentid' => $data->parent_id,
					'parentname' => $parentname,
					'dev_count' => $this->device_model->count_all_devices($data->areaid),
					'user_count' => $this->device_model->count_all_users($data->areaid)
				);
			}
			echo '{success:true,results:'. $results .',data:'.json_encode($data_area).'}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
	
	public function geteditarea()
	{
		$areaid = $this->input->post('areaid');
		$this->db->from('personnel_area');
		$this->db->where('areaid', $areaid);
		$query = $this->db->get();
		
		$data = array(
			'areaid'			=> $query->row()->areaid,
			'areaname'			=> $query->row()->areaname,
			'parentid'			=> $query->row()->parent_id
		);
		$hasil = array("data" => $data, "success" => true);
		echo json_encode($hasil);	
	}
	
	public function geteditdept()
	{
		$deptid = $this->input->post('deptid');
		$this->db->from('departments');
		$this->db->where('deptid', $deptid);
		$query = $this->db->get();
		
		$data = array(
			'deptid'			=> $query->row()->deptid,
			'deptname'			=> $query->row()->deptname,
			'parentid'			=> $query->row()->parentid
		);
		$hasil = array("data" => $data, "success" => true);
		echo json_encode($hasil);	
	}
	
	public function get_allareaforcombo()
    {		
		$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
		$arearesult = $this->device_model->getwhereinarea($areaid);
		$data_area = array();
		foreach($arearesult->result() as $data) 
		{			
			$data_area[] = array(
                'areaid' => $data->areaid,
                'areaname' => $data->areaid.' - '.$data->areaname
            );
		}		
		echo '{success:true,data:'.json_encode($data_area).'}';
    }
	
	public function create_area()
	{
		if ($this->auth->is_logged_in()) {
			$areaid = $this->input->post('areaid');
			$areaname = $this->input->post('areaname');
			$parentid = $this->input->post('parentid');
			$edit = $this->input->post('edit');
			
			if($parentid === $areaid) {
				$hasil = array("success" => false, "responseText" => "sameparent");
			} else {
				$savedata = array (
					'areaid'	=> $areaid,
					'areaname'	=> $areaname,
					'parent_id'	=> $parentid!=''?$parentid:null
				);
				if($this->db->insert('personnel_area', $savedata)) {
					if($this->session->userdata('s_access')!=1) {
						$areaid = array($areaid);
						
						$this->db->select('id, area_id');
						$this->db->from('users');					
						$this->db->where('user_level_id', $this->session->userdata('s_access'));
						$query = $this->db->get();
					
						$areases = '';
						foreach($query->result() as $que) {
							$area = explode(',', $que->area_id);
							$areares = array_merge($area, $areaid);
							$areaimplode = array('area_id'=>implode(',', $areares));
							if($que->id == $this->session->userdata('user_id'))
								$areases = implode(',', $areares);
							$this->db->where('user_level_id', $this->session->userdata('s_access'));
							$this->db->update('users', $areaimplode);				
						}	
						$this->session->unset_userdata('s_area');
						$this->session->set_userdata('s_area', $areases);
					} 
					$actionlog = array(
							'user'			=> $this->session->userdata('s_username'),
							'ipadd'			=> $this->ipaddress->get_ip(),
							'logtime'		=> date("Y-m-d H:i:s"),
							'logdetail'		=> $this->lang->line('area_log_createarea').' '.$areaid,
							'info'			=> $this->lang->line('message_success')
						);
					$this->db->insert('goltca', $actionlog);
					
					$hasil = array("success" => true, "responseText" => "success");
				} else {
					if($this->input->post('edit')==1) {
						$savedata = array (
							'areaname'	=> $areaname,
							'parent_id'	=> $parentid!=''?$parentid:null
						);
						$st = $this->device_model->update('areaid', $areaid, 'personnel_area', $savedata);	
						$actionlog = array(
								'user'			=> $this->session->userdata('s_username'),
								'ipadd'			=> $this->ipaddress->get_ip(),
								'logtime'		=> date("Y-m-d H:i:s"),
								'logdetail'		=> $this->lang->line('area_log_editarea').' '.$areaid,
								'info'			=> $this->lang->line('message_success')
							);
						$this->db->insert('goltca', $actionlog);
						$hasil = array("success" => true, "responseText" => "success");						
					} else 
						$hasil = array("success" => false, "responseText" => "notsuccess");
				}
			}		
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function edit_area()
    {
		if ($this->auth->is_logged_in()) {
			$postData = '{"data":'.$_POST['data'].'}';
			$postDataJson = json_decode($postData);
			
			if($postDataJson->data->parentid === $postDataJson->data->areaid) {
				$hasil = array("success" => false, "responseText" => "sameparent");
			} else {
				if(strval(intval($postDataJson->data->parentid)) == strval($postDataJson->data->parentid)) {
					$parentid = $postDataJson->data->parentid;
				} else {
					$parentid = $this->device_model->getparentidfromareaid($postDataJson->data->areaid);	
				}
				
				$hasil = array("success" => false, "responseText" => "success");
			}
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
	
	public function delete_area()
    {
		$hasil = array("success" => false, "responseText" => "success");
		echo json_encode($hasil);
    }
	
	public function delarea()
    {		
		if ($this->auth->is_logged_in()) {
			$areaidlist = explode(',', substr($_POST['areaid'],0,-1));	
			$areaid = $this->device_model->areaonall($areaidlist);
			if($this->device_model->getdevempexist($areaid)) {
				$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('area_log_delarea').' '.implode(',', $areaid),
						'info'			=> $this->lang->line('message_devexist')
					);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "notsuccess");
			} else if(in_array("1", $areaidlist, true)){
				$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('area_log_delarea').' '.implode(',', $areaid),
						'info'			=> $this->lang->line('message_defaultarea')
					);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "notdel");
			} else {
				$this->db->where_in('areaid', $areaid);	
				if ($this->db->delete('personnel_area')) {
					$this->db->where_in('areaid', $areaid);	
					$this->db->delete('userinfo_attarea');
					
					if($this->session->userdata('s_access')!=1) {
						$this->db->select('id, area_id');
						$this->db->from('users');
						$this->db->where('user_level_id', $this->session->userdata('s_access'));
						$query = $this->db->get();
						$areases = '';
						foreach($query->result() as $que) {
							$area = explode(',', $que->area_id);
							$areares = array_diff($area, $areaid);
							$areaimplode = array('area_id'=>implode(',', $areares));
							if($que->id == $this->session->userdata('user_id'))
								$areases = implode(',', $areares);
							$this->db->where('user_level_id', $this->session->userdata('s_access'));
							$this->db->update('users', $areaimplode);				
						}	
						$this->session->unset_userdata('s_area');
						$this->session->set_userdata('s_area', $areases);
					}
					$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('area_log_delarea').' '.implode(',', $areaid),
						'info'			=> $this->lang->line('message_success')
					);
					$this->db->insert('goltca', $actionlog);
					$hasil = array("success" => true, "responseText" => "success");		
				} else { 
					$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('area_log_delarea').' '.implode(',', $areaid),
						'info'			=> $this->lang->line('message_notsuccess')
					);
					$this->db->insert('goltca', $actionlog);
					$hasil = array("success" => true, "responseText" => "success");
				}		
			}
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	// Department ------------------------------------------------------------------------------------------------
	public function get_alldept()
    {
		if ($this->auth->is_logged_in()) {
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
			
			if(isset($_REQUEST['organid']))		
				$orgid = $this->employe_model->deptonall($_REQUEST['organid']);
			else 
				$orgid = $this->session->userdata('user_dept')!=''?$this->employe_model->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			
			$block1 = str_replace("[", "", $_REQUEST['sort']);
			$block2 = str_replace("]", "", $block1);
			
			$postData = '{"datasort":'.$block2.'}';
			$postDataJson = json_decode($postData);
			
			if(isset($_REQUEST['namaemp'])) {
				if($_REQUEST['namaemp']!='') {
					$deptresult = $this->device_model->getalldeptfind($orgid, $_REQUEST['namaemp'], $postDataJson->datasort->property, $postDataJson->datasort->direction);
				} else {
					$deptresult = $this->device_model->getalldept($orgid, $start, $limit, $postDataJson->datasort->property, $postDataJson->datasort->direction);					
				}
			} else {
				$deptresult = $this->device_model->getalldept($orgid, $start, $limit, $postDataJson->datasort->property, $postDataJson->datasort->direction);
			}
			
			$results = $this->device_model->getdeptcont($orgid);
			$data_dept = array();
			foreach($deptresult->result() as $data) 
			{
				$parentname = '';
				if($data->parentid!='') {
					$parentname = $this->device_model->getdeptparentname($data->parentid);
				}	
				
				$data_dept[] = array(
					'deptid' => $data->deptid,
					'deptname' => $data->deptname,
					'parentid' => $data->parentid,
					'parentname' => $parentname
				);
			}		
			echo '{success:true,results:'. $results .',data:'.json_encode($data_dept).'}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
	
	public function get_alldeptforcombo()
    {	
		$orgid = $this->session->userdata('user_dept')!=''?$this->employe_model->deptonall(explode(',', $this->session->userdata('user_dept'))):array();
			
		$deptresult = $this->device_model->getalldeptcombo($orgid);
		$data_dept = array();
		foreach($deptresult->result() as $data) 
		{
			$data_dept[] = array(
                'deptid' => $data->deptid,
                'deptname' => $data->deptname
            );
		}		
		echo '{success:true,data:'.json_encode($data_dept).'}';
    }
	
	public function create_dept()
	{
		if ($this->auth->is_logged_in()) {
			$deptid = $this->input->post('deptid');
			$deptname = $this->input->post('deptname');
			$parentid = $this->input->post('parentid')!=''?$this->input->post('parentid'):'1';
			$edit = $this->input->post('edit');
			
			if($parentid == $deptid) {
				$hasil = array("success" => false, "responseText" => "sameparent");
			} else {
				$savedata = array (
					'deptid'	=> $deptid,
					'deptname'	=> $deptname,
					'parentid'	=> $parentid
				);
				if($this->db->insert('departments', $savedata)) {
					if($this->session->userdata('s_access')!=1) {
						$deptid = array($deptid);
					
						$this->db->select('id, dept_id');
						$this->db->from('users');
						$this->db->where('user_level_id', $this->session->userdata('s_access'));
						$query = $this->db->get();
						
						$deptses = '';
						foreach($query->result() as $que) {
							$dept = explode(',', $que->dept_id);
							$deptres = array_merge($dept, $deptid);
							$deptimplode = array('dept_id'=>implode(',', $deptres));
							if($que->id == $this->session->userdata('user_id'))
								$deptses = implode(',', $deptres);
							$this->db->where('user_level_id', $this->session->userdata('s_access'));
							$this->db->update('users', $deptimplode);				
						}	
						$this->session->unset_userdata('user_dept');
						$this->session->set_userdata('user_dept', $deptses);
					}
					$actionlog = array(
							'user'			=> $this->session->userdata('s_username'),
							'ipadd'			=> $this->ipaddress->get_ip(),
							'logtime'		=> date("Y-m-d H:i:s"),
							'logdetail'		=> $this->lang->line('dept_log_createdept').' '.$deptid,
							'info'			=> $this->lang->line('message_success')
						);
					$this->db->insert('goltca', $actionlog);
					$hasil = array("success" => true, "responseText" => "success");
				} else {
					if($this->input->post('edit')==1) {
						$savedata = array (
							'deptname'	=> $deptname,
							'parentid'	=> $parentid
						);
						$st = $this->device_model->update('deptid', $deptid, 'departments', $savedata);	
						$actionlog = array(
							'user'			=> $this->session->userdata('s_username'),
							'ipadd'			=> $this->ipaddress->get_ip(),
							'logtime'		=> date("Y-m-d H:i:s"),
							'logdetail'		=> $this->lang->line('dept_log_editdept').' '.$deptid,
							'info'			=> $this->lang->line('message_success')
						);
						$this->db->insert('goltca', $actionlog);
						$hasil = array("success" => true, "responseText" => "success");						
					} else
						$hasil = array("success" => false, "responseText" => "notsuccess");
				}
			}
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function delete_dept()
    {
		$hasil = array("success" => false, "responseText" => "success");
		echo json_encode($hasil);
    }
	
	public function delorgan() 
	{
		if ($this->auth->is_logged_in()) {
			$deptidlist = explode(',', substr($_POST['DeptID'],0,-1));		
			$deptid = $this->employe_model->deptonall($deptidlist);
			if($this->device_model->getempexist($deptid)) {
				$actionlog = array(
					'user'			=> $this->session->userdata('s_username'),
					'ipadd'			=> $this->ipaddress->get_ip(),
					'logtime'		=> date("Y-m-d H:i:s"),
					'logdetail'		=> $this->lang->line('dept_log_deldept').' '.implode(',', $deptid),
					'info'			=> $this->lang->line('message_employeeexist')
				);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "notsuccess");
			} else if(in_array('1', $deptidlist, true)){
				$actionlog = array(
					'user'			=> $this->session->userdata('s_username'),
					'ipadd'			=> $this->ipaddress->get_ip(),
					'logtime'		=> date("Y-m-d H:i:s"),
					'logdetail'		=> $this->lang->line('dept_log_deldept').' '.implode(',', $deptid),
					'info'			=> $this->lang->line('message_defaultdept')
				);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "notdel");
			} else {			
				$this->db->where_in('deptid', $deptid);		
				if ($this->db->delete('departments')) {
					if($this->session->userdata('s_access')!=1) {
						$this->db->select('id, dept_id');
						$this->db->from('users');
						$this->db->where('user_level_id', $this->session->userdata('s_access'));
						$query = $this->db->get();
						$deptses = '';
						foreach($query->result() as $que) {
							$dept = explode(',', $que->dept_id);
							$deptres = array_diff($dept, $deptid);
							$deptimplode = array('dept_id'=>implode(',', $deptres));
							if($que->id == $this->session->userdata('user_id'))
								$deptses = implode(',', $deptres);
							$this->db->where('user_level_id', $this->session->userdata('s_access'));
							$this->db->update('users', $deptimplode);				
						}	
						$this->session->unset_userdata('user_dept');
						$this->session->set_userdata('user_dept', $deptses);
					}
					$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('dept_log_deldept').' '.implode(',', $deptid),
						'info'			=> $this->lang->line('message_success')
					);
					$this->db->insert('goltca', $actionlog);
					$hasil = array("success" => true, "responseText" => "success");		
				} else {
					$hasil = array("success" => true, "responseText" => "error");
				}
			}
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	//--------------------------------------------------------------------------
	
	public function get_comsent()
    {
		if ($this->auth->is_logged_in()) {
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
			$areacombo = $this->input->post('areaid');
			
			if(!empty($areacombo))
				$areaid = $this->device_model->areaonall($areacombo);
			else 
				$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			if(isset($_REQUEST['temukan'])) {
				if($_REQUEST['temukan']!='')
					$comsent = $this->device_model->comsentfind($areaid, $_REQUEST['temukan'], $start, $limit);
				else
					$comsent = $this->device_model->comsent($areaid, $start, $limit);
			} else {
				$comsent = $this->device_model->comsent($areaid, $start, $limit);
			}
			$result = $this->device_model->comsentcount($areaid);
			$data_com = array();
			foreach($comsent->result() as $data) 
			{		
				$data_com[] = array(
					'id' => $data->id,
					'sn' => $data->sn,
					'command' => $data->cmd,
					'submit_time' => $data->submittime,
					'return_time' => $data->returntime,
					'status' => $data->status
				);
			}
			echo '{success:true,results:'. $result .',data:'.json_encode($data_com).'}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
    
    public function delcomsent()
    {		
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', substr($_POST['ID'],0,-1));
			$this->db->where_in('id', $sn);
			$this->db->delete('command');		
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> 'Delete command sent '.implode(',', $sn),
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function clearcomsent()
    {		
		if ($this->auth->is_logged_in()) {
			$this->db->where('status !=',5);
			$this->db->delete('command');		
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> 'Clear command sent',
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function uploadpendingcom()
    {		
		if ($this->auth->is_logged_in()) {
			$this->db->where('status', 2);
			$this->db->update('command', array('status'=>1));		
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> 'Upload pending command',
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function get_comfail()
    {
		if ($this->auth->is_logged_in()) {
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
			
			$areacombo = $this->input->post('areaid');
			
			if(!empty($areacombo))
				$areaid = $this->device_model->areaonall($areacombo);
			else 
				$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			if(isset($_REQUEST['temukan'])) {
				if($_REQUEST['temukan']!='')
					$comfail = $this->device_model->comfailfind($areaid, $_REQUEST['temukan'], $start, $limit);
				else
					$comfail = $this->device_model->comfail($areaid, $start, $limit);
			} else {
				$comfail = $this->device_model->comfail($areaid, $start, $limit);
			}		
			
			$countcomfail = $this->device_model->countcomfail($areaid);
			$data_comf = array();
			foreach($comfail->result() as $data) 
			{
				$data_comf[] = array(
					'id'			=> $data->id,
					'sn' 			=> $data->sn,
					'command' 		=> $data->cmd,
					'submit_time'	=> $data->submittime,
					'error_value' 	=> $data->parameter
				);
			}
			echo '{success:true,results:'. $countcomfail .',data:'.json_encode($data_comf).'}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
    
    public function delcomfail()
    {		
		if ($this->auth->is_logged_in()) {
			$sn = explode(',', $_POST['ID']);
			$this->db->where_in('id', $sn);
			$this->db->delete('command');			
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> 'Delete failed command',
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => false, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function clearfc()
    {		
		if ($this->auth->is_logged_in()) {
			$this->db->where('status',5);
			$this->db->delete('command');		
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> 'Clear failed command',
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function get_transmon()
    {
		if ($this->auth->is_logged_in()) {
			/*$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
			$state = $this->input->post('state');
			$areacombo = $this->input->post('areaid');
			if(!empty($areacombo))
				$areaid = $this->device_model->areaonall($areacombo);
			else 
				$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			if(isset($_REQUEST['temukan'])) {
				if($_REQUEST['temukan']!='')
					$trans = $this->device_model->transfind($areaid, $_REQUEST['temukan'], $start, $limit, $state);
				else
					$trans = $this->device_model->trans($areaid, $start, $limit, $state);
			} else {
				$trans = $this->device_model->trans($areaid, $start, $limit, $state);
			}			
			
			$transcount = $this->device_model->transcount($areaid);
			$data_trans = array();
			foreach($trans->result() as $data) 
			{
				$data_trans[] = array(
					'id'			=> $data->id,
					'sn' 			=> $data->sn,
					'alias'			=> $data->alias,
					'userid'		=> $data->userid,
					'badgenumber'	=> $data->badgenumber,
					'name' 			=> $data->name,
					'date_time' 	=> $data->checktime,
					'status' 		=> $this->device_model->funckey($data->checktype),
					'verify' 		=> $this->device_model->verify($data->verifycode)
				);
			}
			echo '{success:true,results:'. $transcount .',data:'.json_encode($data_trans).'}';*/
			echo '{success: true}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
    
	public function get_areabyuserid()
	{
		$user_id = $_POST['level_id'];
		$arearesult = $this->device_model->getarea_by_userid($user_id);	
		foreach($arearesult->result() as $data) 
		{
			$_arr_id[] = $data->ua_area_id;
		}
		$_ids = implode(",",$_arr_id);
		$hasil = array("success" => false, "responseText" => $_ids);
		echo json_encode($hasil);		
	}
	
	public function get_deptbyuserid()
	{
		$user_id = $_POST['level_id'];
		$deptresult = $this->device_model->getdept_by_userid($user_id);	
		foreach($deptresult->result() as $data) 
		{
			$_arr_id[] = $data->ud_dept_id;
		}
		$_ids = implode(",",$_arr_id);
		$hasil = array("success" => false, "responseText" => $_ids);
		echo json_encode($hasil);		
	}	
	
	public function saveuserarea()
	{	
		$area_id = explode(',', $_POST['area_id']);
		$jmldata = count($area_id) - 1;
		$ua_user_level = $_POST['user_level'];
		for($i=0;$i<$jmldata;$i++) {
			$areas[] = array (
						'ua_area_id '			=>$area_id[$i],
						'ua_user_level'			=>$ua_user_level
					);								
		}
		$sql = "DELETE FROM user_area WHERE  ua_user_level =".$this->db->escape($ua_user_level);
		$this->db->query($sql);	
		$this->db->insert_batch('s_area', $areas);
		
		$hasil = array("success" => false, "responseText" => "success");
		echo json_encode($hasil);		
	}	
	
	public function saveuserdept()
	{	
		$dept_id = explode(',', $_POST['dept_id']);
		$jmldata = count($dept_id) - 1;
		$ud_user_level = $_POST['user_level'];
		for($i=0;$i<$jmldata;$i++) {
			$depts[] = array (
						'ud_dept_id '			=>$dept_id[$i],
						'ud_level_id'			=>$ud_user_level
					);								
		}
		$sql = "DELETE FROM user_dept WHERE  ud_level_id =".$this->db->escape($ud_user_level);
		$this->db->query($sql);	
		$this->db->insert_batch('user_dept', $depts);
		
		$hasil = array("success" => false, "responseText" => "success");
		echo json_encode($hasil);		
	}		
	
	public function get_photomon()
    {
		if ($this->auth->is_logged_in()) {
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
			$tipe = $this->input->post('tipe');
			$areacombo = $this->input->post('areaid');
			
			if(!empty($areacombo))
				$areaid = $this->device_model->areaonall($areacombo);
			else 
				$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			if(isset($_REQUEST['temukan'])) {
				if($_REQUEST['temukan']!='')
					$photo = $this->device_model->getphotofind($areaid, $_REQUEST['temukan'], $start, $limit, $tipe);
				else
					$photo = $this->device_model->getphoto($areaid, $start, $limit, $tipe);
			} else {
				$photo = $this->device_model->getphoto($areaid, $start, $limit, $tipe);
			}		
			
			$photocount = $this->device_model->photocount($areaid);
			$data_trans = array();
			foreach($photo->result() as $data) 
			{
				$data_trans[] = array(
					'id'		=> $data->id,
					'sn' 		=> $data->sn,
					'alias'		=> $data->alias,
					'userid'	=> $data->userid,
					'badgenumber'	=> $data->badgenumber,
					'name' 		=> $data->name,
					'date_time' => $data->photodate,
					'photoimage' 	=> $data->sn."/".$data->filename
				);
			}
			echo '{success:true,results:'. $photocount .',data:'.json_encode($data_trans).'}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
		
	public function networksetting()
    {
		if ($this->auth->is_logged_in()) {
			$SN = explode(',', $this->input->post('SN'));
			/*$ipaddress = $this->input->post('ipadd');
			$netmask = $this->input->post('netmask');
			$gateway = $this->input->post('gateway');
			
			if($ipaddress!='') {
				$com = array (
					'sn'			=>$SN[0],
					'cmd'			=>'SET OPTION IPAddress='.$ipaddress,
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);							
				$this->device_model->save('command', $com);	
			}
			
			if($gateway!='') {
				$com = array (
					'sn'			=>$SN[0],
					'cmd'			=>'SET OPTION GATEIPAddress='.$gateway,
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);							
				$this->device_model->save('command', $com);	
			}
			
			if($netmask!='') {
				$com = array (
					'sn'			=>$SN[0],
					'cmd'			=>'SET OPTION NetMask='.$netmask,
					'status'		=>1,
					'submittime'	=>date("Y-m-d H:i:s")
				);							
				$this->device_model->save('command', $com);	
			}*/
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_network').' '.implode(',', $SN),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
	
	public function webserversetting()
    {
		if ($this->auth->is_logged_in()) {
			$SN = explode(',', $this->input->post('SN'));
			/*$webserver = $this->input->post('ipwebserver');
			$port = $this->input->post('port');
			$urlmode = $this->input->post('urlmode');
			foreach($SN as $esen) {
				if(isset($esen)) {
					if($urlmode==1) {
						$com = array (
							'sn'			=>$esen,
							'cmd'			=>'SET OPTION WebServerURLModel=1',
							'status'		=>1,
							'submittime'	=>date("Y-m-d H:i:s")
						);							
						$this->device_model->save('command', $com);	
					} else if($urlmode==2) {
						$com = array (
							'sn'			=>$esen,
							'cmd'			=>'SET OPTION WebServerURLModel=0',
							'status'		=>1,
							'submittime'	=>date("Y-m-d H:i:s")
						);							
						$this->device_model->save('command', $com);
					}

					if($webserver!='') {
						$com = array (
							'sn'			=>$esen,
							'cmd'			=>'SET OPTION WebServerIP='.$webserver,
							'status'		=>1,
							'submittime'	=>date("Y-m-d H:i:s")
						);							
						$this->device_model->save('command', $com);	
					}
					
					if($port!='') {
						$com = array (
							'sn'			=>$esen,
							'cmd'			=>'SET OPTION WebServerPort='.$port,
							'status'		=>1,
							'submittime'	=>date("Y-m-d H:i:s")
						);							
						$this->device_model->save('command', $com);	
					}
				}
			}*/
			$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_webserver').' '.implode(',', $SN),
						'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
			$hasil = array("success" => true, "responseText" => "success");
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
	
	public function getusblog()
	{
		if ($this->auth->is_logged_in()) {
			$SN = str_replace(',','',$this->input->post('SN'));
			$fileupload = $_FILES['fileusb']['tmp_name'];
			$namafile = $_FILES['fileusb']['name'];
			$path = 'assets/resources/data/usb/';
			$pathfile = $path.$namafile;
				
			if(move_uploaded_file($fileupload,$pathfile)) {
				$roster = file_get_contents($pathfile);
				$line = explode("\n", $roster);
				$countline = count($line)-1;
				$ada = 0;
				for($i=0;$i<$countline;$i++) {
					$data = explode("\t",$line[$i]);
					$userid = trim($data[0]);
					$dataarray = array(
						'sn'			=>$SN,
						'userid'		=>$userid,
						'checktime'		=>$data[1],
						'checktype'		=>$data[3],
						'verifycode'	=>$data[4]	
					);
					if($this->db->insert('checkinout', $dataarray)) $ada = 1;
				}
				
				$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_downusb'),
						'info'			=> $this->lang->line('message_success')
				);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "success");
				echo json_encode($hasil);
			} else {
				$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_downusb'),
						'info'			=> $this->lang->line('message_error')
				);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("responseText" => $this->lang->line('message_usbfail'), "success" => false);
				
				$hasil = array("success" => true, "responseText" => "success");
				echo json_encode($hasil);
			}
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }		
	}	
	
	public function get_alltempdev()
	{
		if ($this->auth->is_logged_in()) {
			$block1 = str_replace("[", "", $_REQUEST['sort']);
			$block2 = str_replace("]", "", $block1);
			
			$postData = '{"datasort":'.$block2.'}';
			$postDataJson = json_decode($postData);
			
			$hasil = $this->device_model->getalltempdev($postDataJson->datasort->property, $postDataJson->datasort->direction);
			
			$results = $this->db->count_all_results('devtemp');
			$data_arr = array();
			foreach($hasil->result() as $data) 
			{
				$data_arr[] = array(
					'sn' 				=> $data->sn,
					'condate' 			=> $data->condate,
					'ipaddress' 		=> $data->ipaddress
				);
			}
		
			echo '{success:true,results:'. $results .',data:'.json_encode($data_arr).'}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }		
	}	
	
	public function deltempdevice()
    {		
		if ($this->auth->is_logged_in()) {
			$SN = explode(',', $_POST['SN']);			
			$this->db->where_in('sn', $SN);		
			if ($this->db->delete('devtemp')) {
				$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_deltemp').' '.implode(',', $SN),
						'info'			=> $this->lang->line('message_success')
				);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "success");		
			} else {
				$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_deltemp').' '.implode(',', $SN),
						'info'			=> $this->lang->line('message_errdelete')
				);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "error");
			}		
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
	}
	
	public function savedevicelist() 
	{
		if ($this->auth->is_logged_in()) {
			$SN = explode(',', $this->input->post('SN'));
			
			$devname = $this->input->post('device_name');
			$areaid = $this->input->post('areaid')?$this->input->post('areaid'):1;
			$delay = $this->input->post('delay')?$this->input->post('delay'):$this->input->post('delay2');
			$errdelay = $this->input->post('err_delay')?$this->input->post('err_delay'):$this->input->post('err_delay2');
			$tz = $this->input->post('tzone')?$this->input->post('tzone'):$this->input->post('tzone2');
			$jmlsn = count($SN) - 1;
			
			$dev=0;
			$stepone = $this->setting_model->custom_hash($this->setting_model->sidik(),16);
			if($this->setting_model->tegdis()) {
				$sid = $this->setting_model->tegasdis();
				$expl = explode('.', $this->setting_model->dpoison($sid, 'gandaria8'));
				if($this->setting_model->dpoison($expl[0])==$stepone) {
					$dal = explode('#', $this->setting_model->dpoison($expl[1]));
					$dev = $dal[1];
				} else {
					$dev = 5;
				}
			} else {
				$dev = 5;
			}
			
			if($this->device_model->jmlhsn()>=$dev) {
				$actionlog = array(
						'user'			=> $this->session->userdata('s_username'),
						'ipadd'			=> $this->ipaddress->get_ip(),
						'logtime'		=> date("Y-m-d H:i:s"),
						'logdetail'		=> $this->lang->line('device_log_createdevice').' '.substr($this->input->post('SN'),0,-1),
						'info'			=> $this->lang->line('message_limit')
				);
				$this->db->insert('goltca', $actionlog);
				$hasil = array("success" => true, "responseText" => "notsnsuccess");
			} else {
				if($jmlsn==1) {				
					$savedata = array (
							'sn'				=> substr($this->input->post('SN'),0,-1),
							'alias'				=> $devname!=''?$devname:substr($this->input->post('SN'),0,-1),
							'areaid'			=> $areaid,
							'errdelay'			=> $errdelay,
							'delay'				=> $delay,
							'timezone'			=> $tz,
							'stamp'				=> '0',
							'opstamp'			=> '0',
							'photostamp'		=> '0',
							'transtimes'		=> '00:00;14:05',
							'transinterval' 	=> 1,
							'transflag'			=> '1111101000',												
							'realtime'			=> 1,
							'encrypt'			=> 0
					);
					$st = $this->device_model->save('iclock', $savedata);	
					$actionlog = array(
							'user'			=> $this->session->userdata('s_username'),
							'ipadd'			=> $this->ipaddress->get_ip(),
							'logtime'		=> date("Y-m-d H:i:s"),
							'logdetail'		=> $this->lang->line('device_log_createdevice').' '.substr($this->input->post('SN'),0,-1),
							'info'			=> $this->lang->line('message_success')
					);
					$this->db->insert('goltca', $actionlog);		
					$this->db->where_in('sn', $SN);
					$this->db->delete('devtemp');					
					$hasil = array("success" => true, "responseText" => "success");
				} else {
					for($i=0;$i<$jmlsn;$i++) {
						$savedata = array (
								'sn'				=> $SN[$i],
								'alias'				=> $SN[$i],
								'areaid'			=> $areaid,
								'errdelay'			=> $errdelay,
								'delay'				=> $delay,
								'timezone'			=> $tz,
								'stamp'				=> '0',
								'opstamp'			=> '0',
								'photostamp'		=> '0',
								'transtimes'		=> '00:00;14:05',
								'transinterval' 	=> 1,
								'transflag'			=> '1111101000',												
								'realtime'			=> 1,
								'encrypt'			=> 0
						);
						$st = $this->device_model->save('iclock', $savedata);						
					}
					$actionlog = array(
							'user'			=> $this->session->userdata('s_username'),
							'ipadd'			=> $this->ipaddress->get_ip(),
							'logtime'		=> date("Y-m-d H:i:s"),
							'logdetail'		=> $this->lang->line('device_log_createdevice').' '.substr($this->input->post('SN'),0,-1),
							'info'			=> $this->lang->line('message_success')
					);
					$this->db->insert('goltca', $actionlog);
					
					$this->db->where_in('sn', $SN);
					$this->db->delete('devtemp');							
					$hasil = array("success" => true, "responseText" => "success");
				}
			}			
			echo json_encode($hasil);
		} else {
            $hasil = array("success" => true, "responseText" => "notlogin");
			echo json_encode($hasil);
        }	
	}	
	
	public function getfilterstate() 
	{
		$this->db->from('state');
		$query = $this->db->get();
		$serial = array();
		foreach($query->result() as $SN) {
			$serial[] = array(
				'id' => $SN->id,
				'state' => $SN->state
			);
		}
		
		echo '{success:true,data:'.json_encode($serial).'}';
	}
	
	public function get_oplogmon()
    {
		if ($this->auth->is_logged_in()) {
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
			$areacombo = $this->input->post('areaid');
			
			//get opcode name
			$opcode = $this->device_model->getopname();
			$dataoparray = array();
			foreach($opcode->result() as $opname) {
				$dataoparray[$opname->opcode] = $opname->opcontent;
			}	
			$alarm = array();
			$alarm[50] = $this->lang->line('alarm_50');
			$alarm[51] = $this->lang->line('alarm_51');
			$alarm[55] = $this->lang->line('alarm_55');
			$alarm[53] = $this->lang->line('alarm_53');
			$alarm[54] = $this->lang->line('alarm_54');
			$alarm[58] = $this->lang->line('alarm_58');
			$alarm[65535] = $this->lang->line('alarm_65535');			
						
			if(!empty($areacombo))
				$areaid = $this->device_model->areaonall($areacombo);
			else 
				$areaid = $this->session->userdata('s_area')!=''?$this->device_model->areaonall(explode(',', $this->session->userdata('s_area'))):array();
			
			if(isset($_REQUEST['temukan'])) {
				if($_REQUEST['temukan']!='')
					$oplog = $this->device_model->getoplogfind($areaid, $_REQUEST['temukan'], $start, $limit);
				else
					$oplog = $this->device_model->getoplog($areaid, $start, $limit);
			} else {
				$oplog = $this->device_model->getoplog($areaid, $start, $limit);
			}		
			
			$oplogcount = $this->device_model->oplogcount($areaid);
			$data_trans = array();
			foreach($oplog->result() as $data) 
			{
				if($data->opcode==3) 
					$objectx = $alarm[$data->object1];
				else
					$objectx = $data->object1;
					
				$data_trans[] = array(
					'id'			=> $data->id,
					'sn' 			=> $data->sn,
					'alias'			=> $data->alias,
					'date_time' 	=> $data->opdatetime,
					'oplog'			=> isset($dataoparray[$data->opcode])?$dataoparray[$data->opcode]:'',
					'object' 		=> $objectx
				);
			}
			echo '{success:true,results:'. $oplogcount .',data:'.json_encode($data_trans).'}';
		} else {
            $hasil = array("success" => false, "responseText" => "notlogin");
			echo json_encode($hasil);
        }
    }
	
	public function autoreboot()
	{
		/*$rem = $_REQUEST['rem'];		
		if($rem) {		
			$waktu = $_REQUEST['timeautoreboot'];
			$output = shell_exec('schtasks.exe /delete /tn autorebootdevice /f');
			$output = shell_exec('schtasks.exe /create /sc daily /st '.$waktu.':00 /tn autorebootdevice /tr '.$this->config->item('base_dir').'apps\\sched\\autorebootdev.bat /ru System /v1');
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> 'Set autoreboot device',
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
		} else {			
			$output = shell_exec('schtasks.exe /delete /tn autorebootdevice /f');
			$actionlog = array(
				'user'			=> $this->session->userdata('s_username'),
				'ipadd'			=> $this->ipaddress->get_ip(),
				'logtime'		=> date("Y-m-d H:i:s"),
				'logdetail'		=> 'Remove autoreboot device',
				'info'			=> $this->lang->line('message_success')
			);
			$this->db->insert('goltca', $actionlog);
		}*/
		$hasil = array("success" => true, "responseText" => "success");
		echo json_encode($hasil);
	}
	
	public function autorebootaction()
	{
		/*$sn = $this->device_model->getallsn();
		foreach($sn->result() as $esen) {
			$com = array (
				'sn'			=>$esen->sn,
				'cmd'			=>'REBOOT',
				'status'		=>1,
				'submittime'	=>date("Y-m-d H:i:s")
			);							
			$this->device_model->save('command', $com);	
		}*/
		$hasil = array("success" => true, "responseText" => "success");
		echo json_encode($hasil); 
	}
	
	public function autoemailoffline()
	{
		/*$skrg = strtotime(date('Y-m-d H:i:s'));
		$awal = strtotime(date('Y-m-d 08:00:00'));
		$akhir = strtotime(date('Y-m-d 20:00:00'));
		
		if($skrg>=$awal && $skrg<=$akhir) {		
			$offline = $this->device_model->getoffline();
			$a = 0;
			$b = array();
			foreach($offline->result() as $off) {
				$waktu = strtotime(date('Y-m-d H:i:s')) - strtotime($off->lastactivity);
				if($waktu > 900) {
					$a++;
					$b[] = array(
						'sn' 			=> $off->sn,
						'alias' 		=> $off->alias,
						'lastactivity' 	=> $off->lastactivity
					);
				}
			}
		
			$content = 	'<table width="600" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td colspan="3"><img src="cid:about-image.jpg" width="164" height="66" /></td>
							</tr>
							<tr height="10">
								<td colspan="3"><b></b></td>
							</tr>
							<tr height="30">
								<td colspan="3"><b>OFFLINE DEVICES ('.$a.' total)</b></td>
							</tr>
							<tr>
								<td width="200"><u />Serial No</td>
								<td width="200"><u />Device Name</td>
								<td width="200"><u />Last Activity</td>
							</tr>';
			foreach($b as $c) {
				$content .= '<tr>
								<td width="200">'.$c['sn'].'</td>
								<td width="200">'.$c['alias'].'</td>
								<td width="200">'.$c['lastactivity'].'</td>
							</tr>';
			}
			$content .= '</table>';
			
			$sql = "select a.email from userinfo a
						right join autoemail b
						on string_to_array(a.userid, ',') <@ string_to_array(b.ae_users, ',')
						where b.ae_report_type = 1";
			$query = $this->db->query($sql);
			foreach($query->result() as $que) 
				$emailnya[] = $que->email;
			
			$path = 'assets/resources/data/setting/email.wt';	
			$setting = file_get_contents($path);			
			$line = explode("\n", $setting);
			$emailfrom = $line[2];
			$emailto = implode(',', $emailnya);
			$this->load->library('email');
			
			$this->email->from($emailfrom, 'Woowtime');
			$this->email->to($emailto); 
			$this->email->attach($this->config->item('base_dir').'frameworks\\codeigniter\\htdocs\\woowtime\\assets\\js\\template\\images\\about-image.jpg', 'inline');
			$this->email->subject('Woowtime Offline Devices Report');
			$this->email->message($content);	

			$this->email->send();
			echo $this->email->print_debugger();
		}*/
	}
}
