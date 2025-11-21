<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ketidakhadiran extends MX_Controller {

    private $aAkses;
	function Ketidakhadiran(){
		parent::__construct();
		$this->load->helper('utility');
		$this->load->helper('menunavigasi');
		$this->load->library('mypagination' );

        $this->load->model('utils_model','utils');
		$this->load->model('ketidakhadiran_model','ketidakhadiran');
        $this->load->model('pegawai/pegawai_model','pegawai');

        if( !$this->auth->is_logged_in() ){
            redirect('login', 'refresh');
        }
        $this->aAkses = akses("Ketidakhadiran", $this->session->userdata('s_access'));
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');

	}
	
	public function statusdikembalikan()
    {
        $userid = $this->input->post('userid');
        $startattilog = '';
        $endattilog = '';
        if($this->session->userdata('s_access')==1) {
            $startattilog = $this->input->post('startattilog');
            $endattilog = $this->input->post('endattilog');
        }
		$this->db->select('nip,keterangan,status,nomor_sk,tanggal_awal,tanggal_akhir');
		$this->db->from('tb_status_ketidakhadiran');
		$this->db->where('id', $userid);
		$cekkehadiran    = $this->db->get();
		$resultkehadiran = $cekkehadiran->result();
		foreach ($resultkehadiran as $row) {
			$rangekehadiran = (strtotime($row->tanggal_akhir) - strtotime($row->tanggal_awal)) / 86400;
			for($j=0;$j<=$rangekehadiran;$j++) {
				$tgal = strtotime($row->tanggal_awal) + ($j*86400);
				$atdate = date('Y-m-d', $tgal);
				$this->db->select('status');
				$this->db->from('bukatutup');
				$this->db->where('idbln', date('n', $tgal));
				$this->db->where('tahun', date('Y', $tgal));
				$query = $this->db->get();
				if ($query->num_rows()>0) {
					$bukatutup = $query->row()->status;
					if ($bukatutup) {
						$nip = $row->nip;
						$keterangan = $row->keterangan;
						$status2 = $row->status;
						$nomor_sk2 = $row->nomor_sk;
						
						$this->db->where('userid', $nip);
						$this->db->where('rosterdate', $atdate);
						$rsl = $this->db->get("rosterdetailsatt");
						if ($rsl->num_rows() > 0) {
							$result = $rsl->result();
							foreach ($result as $row2) {
								$this->db->where('id', $row2->id);
								$query = $this->db->get("rosterdetailsatt");
								$datas = $query->row_array();
								log_history("Hapus", "rosterdetailsatt", $datas);
								if (isset($datas)) {
									createLog("Menghapus jadwal kerja " . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"], "Sukses");
								}
								$this->db->where('id', $row2->id);
								$this->db->where('userid', $nip);
								$this->db->where('rosterdate', $atdate);
								$this->db->delete('rosterdetailsatt');
							}
							
							$data2['status_verifikasi']    		= 0;
							$data2['status_verifikasi_tanggal'] = date('Y-m-d H:i:s');
							$data2['status_verifikasi_oleh']    = $this->session->userdata('s_username');
							$xss_data2 = $this->security->xss_clean($data2);
							$this->db->where('id',$userid);
							$this->db->update('tb_status_ketidakhadiran',$xss_data2);
							
							$dataupdate2 = array('nip' => $nip,
								'status_verifikasi' => 0,
								'status_verifikasi_tanggal' => date('Y-m-d H:i:s'),
								'create_date' => date('Y-m-d H:i:s'),
								'status_verifikasi_oleh' => $this->session->userdata('s_username'));

							$this->db->insert('tb_status_ketidakhadiran_log', $dataupdate2);
						}else {
							$data2['status_verifikasi']    		= 0;
							$data2['status_verifikasi_tanggal'] = date('Y-m-d H:i:s');
							$data2['status_verifikasi_oleh']    = $this->session->userdata('s_username');
							$xss_data2 = $this->security->xss_clean($data2);
							$this->db->where('id',$userid);
							$this->db->update('tb_status_ketidakhadiran',$xss_data2);
							
							$dataupdate2 = array('nip' => $nip,
								'status_verifikasi' => 0,
								'status_verifikasi_tanggal' => date('Y-m-d H:i:s'),
								'create_date' => date('Y-m-d H:i:s'),
								'status_verifikasi_oleh' => $this->session->userdata('s_username'));

							$this->db->insert('tb_status_ketidakhadiran_log', $dataupdate2);
						}
						
					}
				}
			}	
		}
		

        $data['jmldata'] = count($arr);
        $data['data'] = json_encode($arr);
        $data['msg'] = 'Data berhasil disimpan..';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
    }
	
	public function statusditolak()
    {
        $userid = $this->input->post('userid');
        $startattilog = '';
        $endattilog = '';
        if($this->session->userdata('s_access')==1) {
            $startattilog = $this->input->post('startattilog');
            $endattilog = $this->input->post('endattilog');
        }
		$this->db->select('nip,keterangan,status,nomor_sk,tanggal_awal,tanggal_akhir');
		$this->db->from('tb_status_ketidakhadiran');
		$this->db->where('id', $userid);
		$cekkehadiran    = $this->db->get();
		$resultkehadiran = $cekkehadiran->result();
		$this->nontifikasiFireBase2($userid);
		foreach ($resultkehadiran as $row) {
			$rangekehadiran = (strtotime($row->tanggal_akhir) - strtotime($row->tanggal_awal)) / 86400;
			for($j=0;$j<=$rangekehadiran;$j++) {
				$tgal = strtotime($row->tanggal_awal) + ($j*86400);
				$atdate = date('Y-m-d', $tgal);
				$this->db->select('status');
				$this->db->from('bukatutup');
				$this->db->where('idbln', date('n', $tgal));
				$this->db->where('tahun', date('Y', $tgal));
				$query = $this->db->get();
				if ($query->num_rows()>0) {
					$bukatutup = $query->row()->status;
					if ($bukatutup) {
						$nip = $row->nip;
						$keterangan = $row->keterangan;
						$status2 = $row->status;
						$nomor_sk2 = $row->nomor_sk;
						
						$this->db->where('userid', $nip);
						$this->db->where('rosterdate', $atdate);
						$rsl = $this->db->get("rosterdetailsatt");
						if ($rsl->num_rows() > 0) {
							$result = $rsl->result();
							foreach ($result as $row2) {
								$this->db->where('id', $row2->id);
								$query = $this->db->get("rosterdetailsatt");
								$datas = $query->row_array();
								log_history("Hapus", "rosterdetailsatt", $datas);
								if (isset($datas)) {
									createLog("Menghapus jadwal kerja " . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"], "Sukses");
								}
								$this->db->where('id', $row2->id);
								$this->db->where('userid', $nip);
								$this->db->where('rosterdate', $atdate);
								$this->db->delete('rosterdetailsatt');
							}
							
							$data2['status_verifikasi']    		= 2;
							$data2['status_verifikasi_tanggal'] = date('Y-m-d H:i:s');
							$data2['status_verifikasi_oleh']    = $this->session->userdata('s_username');
							$xss_data2 = $this->security->xss_clean($data2);
							$this->db->where('id',$userid);
							$this->db->update('tb_status_ketidakhadiran',$xss_data2);
							
							$dataupdate2 = array('nip' => $nip,
								'status_verifikasi' => 2,
								'status_verifikasi_tanggal' => date('Y-m-d H:i:s'),
								'create_date' => date('Y-m-d H:i:s'),
								'status_verifikasi_oleh' => $this->session->userdata('s_username'));

							$this->db->insert('tb_status_ketidakhadiran_log', $dataupdate2);
						}else {
							$data2['status_verifikasi']    		= 2;
							$data2['status_verifikasi_tanggal'] = date('Y-m-d H:i:s');
							$data2['status_verifikasi_oleh']    = $this->session->userdata('s_username');
							$xss_data2 = $this->security->xss_clean($data2);
							$this->db->where('id',$userid);
							$this->db->update('tb_status_ketidakhadiran',$xss_data2);
							
							$dataupdate2 = array('nip' => $nip,
								'status_verifikasi' => 2,
								'status_verifikasi_tanggal' => date('Y-m-d H:i:s'),
								'create_date' => date('Y-m-d H:i:s'),
								'status_verifikasi_oleh' => $this->session->userdata('s_username'));

							$this->db->insert('tb_status_ketidakhadiran_log', $dataupdate2);
						}
						
					}
				}
			}	
		}

        $data['jmldata'] = count($arr);
        $data['data'] = json_encode($arr);
        $data['msg'] = 'Data berhasil disimpan..';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
    }
	
	public function statushadir()
    {
        $userid = $this->input->post('userid');
        $startattilog = '';
        $endattilog = '';
        if($this->session->userdata('s_access')==1) {
            $startattilog = $this->input->post('startattilog');
            $endattilog = $this->input->post('endattilog');
        }
		$this->db->select('nip,keterangan,status,nomor_sk,tanggal_awal,tanggal_akhir,keterangan_lokasi');
		$this->db->from('tb_status_ketidakhadiran');
		$this->db->where('id', $userid);
		$cekkehadiran    = $this->db->get();
		$resultkehadiran = $cekkehadiran->result();
		$this->nontifikasiFireBase($userid);
		foreach ($resultkehadiran as $row) {
			$rangekehadiran = (strtotime($row->tanggal_akhir) - strtotime($row->tanggal_awal)) / 86400;
			for($j=0;$j<=$rangekehadiran;$j++) {
				$tgal = strtotime($row->tanggal_awal) + ($j*86400);
				$atdate = date('Y-m-d', $tgal);
				$this->db->select('status');
				$this->db->from('bukatutup');
				$this->db->where('idbln', date('n', $tgal));
				$this->db->where('tahun', date('Y', $tgal));
				$query = $this->db->get();
				if ($query->num_rows()>0) {
					$bukatutup = $query->row()->status;
					if ($bukatutup) {
						$nip = $row->nip;
						$keterangan = $row->keterangan;
						$status2 = $row->status;
						$nomor_sk2 = $row->nomor_sk;
						$keterangan_lokasi2 = $row->keterangan_lokasi;
						
						$this->db->where('userid', $nip);
						$this->db->where('rosterdate', $atdate);
						$rsl = $this->db->get("rosterdetailsatt");
						if ($rsl->num_rows() > 0) {
							$result = $rsl->result();
							foreach ($result as $row2) {
								$this->db->where('id', $row2->id);
								$query = $this->db->get("rosterdetailsatt");
								$datas = $query->row_array();
								log_history("edit", "rosterdetailsatt", $datas);
								if (isset($datas)) {
									createLog("Merubah jadwal kerja " . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["attendance"], "Sukses");
								}
							}
							
							$dataupdater = array(
								'attendance' => $status2,
								'notes' => $keterangan."<br>".$keterangan_lokasi2,
								'nosk' => $nomor_sk2,
								'status' => 1,
								'editby' => $this->session->userdata('s_username')
							);
							$this->db->where('userid', $nip);
							$this->db->where('rosterdate', $atdate);
							$this->db->update('rosterdetailsatt', $dataupdater);
							
							$data2['status_verifikasi']    		= 1;
							$data2['status_verifikasi_tanggal'] = date('Y-m-d H:i:s');
							$data2['status_verifikasi_oleh']    = $this->session->userdata('s_username');
							$xss_data2 = $this->security->xss_clean($data2);
							$this->db->where('id',$userid);
							$this->db->update('tb_status_ketidakhadiran',$xss_data2);
							
							$dataupdate2 = array('nip' => $nip,
								'status_verifikasi' => 1,
								'status_verifikasi_tanggal' => date('Y-m-d H:i:s'),
								'create_date' => date('Y-m-d H:i:s'),
								'status_verifikasi_oleh' => $this->session->userdata('s_username'));

							$this->db->insert('tb_status_ketidakhadiran_log', $dataupdate2);
							
							array_push($arr, array(
								"id" => $nip . "-" . $atdate,
								"sts" => "#AT"
							));
						}else {
							$dataupdate = array('userid' => $nip,
								'rosterdate' => $atdate,
								'attendance' => $status2,
								'notes' => $keterangan."<br>".$keterangan_lokasi2,
								'nosk' => $nomor_sk2,
								'status' => 1,
								'editby' => $this->session->userdata('s_username'));

							$this->db->insert('rosterdetailsatt', $dataupdate);
							
							$dataupdate2 = array('nip' => $nip,
								'status_verifikasi' => 1,
								'status_verifikasi_tanggal' => date('Y-m-d H:i:s'),
								'create_date' => date('Y-m-d H:i:s'),
								'status_verifikasi_oleh' => $this->session->userdata('s_username'));

							$this->db->insert('tb_status_ketidakhadiran_log', $dataupdate2);
							
							createLog("Membuat jadwal kerja " . $nip . " " . $atdate . " " . $attcode, "Sukses");
							
							$data2['status_verifikasi']    		= 1;
							$data2['status_verifikasi_tanggal'] = date('Y-m-d H:i:s');
							$data2['status_verifikasi_oleh']    = $this->session->userdata('s_username');
							$xss_data2 = $this->security->xss_clean($data2);
							$this->db->where('id',$userid);
							$this->db->update('tb_status_ketidakhadiran',$xss_data2);
							
							array_push($arr, array(
								"id" => $nip . "-" . $atdate,
								"sts" => "#AT"
							));
						}
						
						if ($startattilog != '') {
							$this->db->where('checktime', $atdate . ' ' . $startattilog);
							$this->db->where('userid', $nip);
							$rsl = $this->db->get("checkinout");
							if ($rsl->num_rows() > 0) {
								$logupdate = array('userid' => $nip,
									'checktime' => $atdate . ' ' . $startattilog,
									'checktype' => 0,
									'verifycode' => 0,
									'sn' => '1');
								$this->db->insert('checkinout', $logupdate);
								createLog("Membuat cekinout " . $nip . " " . $atdate . " " . $startattilog, "Sukses");
							}
						}
						if ($endattilog != '') {
							$this->db->where('checktime', $atdate . ' ' . $endattilog);
							$this->db->where('userid', $nip);
							$rsl = $this->db->get("checkinout");
							if ($rsl->num_rows() > 0) {
								$logupdate = array('userid' => $nip,
									'checktime' => $atdate . ' ' . $endattilog,
									'checktype' => 0,
									'verifycode' => 0, 'sn' => '1');
								$this->db->insert('checkinout', $logupdate);
								createLog("Membuat cekinout " . $nip . " " . $atdate . " " . $endattilog, "Sukses");
							}
						}
					}
				}
			}	
		}
		
		
        $userid = explode(',', $this->input->post('userid'));
        //$this->rekalkulasi($datestart,$dateend,$userid);

        createLog('Membuat jadwal kerja userid = '.$this->input->post('userid').' start date = '.$this->input->post('start').' end date = '.$this->input->post('end'),"Sukses");

        $data['jmldata'] = count($arr);
        $data['data'] = json_encode($arr);
        $data['msg'] = 'Data berhasil disimpan..';
        $data['status'] = 'succes';
        $this->output->set_output( json_encode($data));
    }
	
	public function nontifikasiFireBase($nip)
    {
		$query=
	        "
	          	SELECT c.name, a.nip as nippegawai,
				DATE_FORMAT(a.tanggal_awal,'%d-%m-%Y') AS tanggal_awal, 
				DATE_FORMAT(a.tanggal_akhir,'%d-%m-%Y') AS tanggal_akhir, a.nomor_sk, a.keterangan, a.keterangan_lokasi,
				b.abname, a.status_verifikasi, 
				DATE_FORMAT(a.create_date,'%d-%m-%Y %H:%i:%s') AS create_date, 
				a.keterangan_lokasi, a.id as idpesan,
				CASE
				    WHEN a.status_verifikasi = 1 THEN 'Disetujui'
				    WHEN a.status_verifikasi = 2 THEN 'Tidak Disetujui'
				    ELSE 'Belum Diproses'
				END AS status_verifikasi
				FROM tb_status_ketidakhadiran AS a 
				LEFT JOIN absence AS b ON a.status = b.abid
				LEFT JOIN userinfo AS c ON a.nip = c.userid
				WHERE a.id = '".$nip."'
	        ";
		$dataabsen = $this->db->query($query);
		$s         = $dataabsen->row();
	

		
		$data3['nip']	 	 	= $s->nippegawai;
		$data3['title']	 	 	= "Verifikasi Absen Status Ketidakhadiran";
		$data3['pesan']			= $pesan;
		$data3['idpesan']		= $s->idpesan;
		$data3['create_date']	= date('Y-m-d H:i:s');
		$xss_data3 = $this->security->xss_clean($data3);
		$this->db->insert('tb_pesan',$xss_data3);
	}
	
	public function nontifikasiFireBase2($nip)
    {
		$query=
	        "
	          	SELECT c.name, a.nip as nippegawai,
				DATE_FORMAT(a.tanggal_awal,'%d-%m-%Y') AS tanggal_awal, 
				DATE_FORMAT(a.tanggal_akhir,'%d-%m-%Y') AS tanggal_akhir, a.nomor_sk, a.keterangan, a.keterangan_lokasi,
				b.abname, a.status_verifikasi, 
				DATE_FORMAT(a.create_date,'%d-%m-%Y %H:%i:%s') AS create_date, 
				a.keterangan_lokasi, a.id as idpesan,
				CASE
				    WHEN a.status_verifikasi = 1 THEN 'Disetujui'
				    WHEN a.status_verifikasi = 2 THEN 'Tidak Disetujui'
				    ELSE 'Belum Diproses'
				END AS status_verifikasi
				FROM tb_status_ketidakhadiran AS a 
				LEFT JOIN absence AS b ON a.status = b.abid
				LEFT JOIN userinfo AS c ON a.nip = c.userid
				WHERE a.id = '".$nip."'
	        ";
		$dataabsen = $this->db->query($query);
		$s         = $dataabsen->row();
		
		
		$data3['nip']	 	 	= $s->nippegawai;
		$data3['title']	 	 	= "Verifikasi Absen Status Ketidakhadiran";
		$data3['pesan']			= $pesan;
		$data3['idpesan']		= $s->idpesan;
		$data3['create_date']	= date('Y-m-d H:i:s');
		$xss_data3 = $this->security->xss_clean($data3);
		$this->db->insert('tb_pesan',$xss_data3);
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
        $this->session->set_userdata('menu','18');
        $data['menu'] = '18';
        $uri_segment=3;
        $offset = 0;
        $orgid = $this->pegawai->deptonall($this->session->userdata('s_dept'));
        $SQLcari="";
        if(!empty($orgid)) {
            if ($orgid!="1") {
                $s = array();
                foreach ($orgid as $ar)
                    $s[] = "'" . $ar . "'";

                $SQLcari .= " and b.deptid in (" . implode(',', $s) . ") ";
            }
        }

        $data['aksesrule']=$this->aAkses;
        $SQLcari .=" and b.jenispegawai in (1,2) and b.jftstatus in ('1','2') ";
        $SQLcari .= " ORDER BY a.create_date DESC ";
        $query = $this->ketidakhadiran->getDaftar(1,10,$offset,null,$SQLcari);
        $jum_data = $this->ketidakhadiran->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url('ketidakhadiran/pagging/');
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),10,$this_url,$uri_segment);
        $data['paging'] = "";
        $data['offset'] = "0";
        $data['jum_data'] = "0";
        $data['result'] = "0";
        $data['lstStsPeg'] = $this->utils->getStatusPegawai();
        $data['lstJnsPeg'] = $this->utils->getJenisPegawai();
		$data['lstVerifikasi'] = $this->utils->getStatusVerifikasi();
		$data['lstKetidakhadiran'] = $this->utils->getStatusKetidakhadiran();
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
		$stausverifikasi = $this->input->post('stausverifikasi');
		$stausketidakhadiran = $this->input->post('stausketidakhadiran');
		
		$limited=((isset($limited) && ($limited!='' || $limited!=null))?$limited:10);
        $offset = ((isset($page) && ($page!='' || $page!=null))?$page:0);
		

        $SQLcari="";
		
		$mulai = dmyToymd($this->input->post('start'));
        $akhir = dmyToymd($this->input->post('end'));
		
		$mulai=((isset($mulai) && ($mulai!='' || $mulai!=null))?$mulai:date("Y-m-d"));
        $akhir=((isset($akhir) && ($akhir!='' || $akhir!=null))?$akhir:date("Y-m-d"));
		$SQLcari .=" and (date(a.create_date) between '".$mulai."' and '".$akhir."' )";
		
        if($cr=='cri'){
            $data['caridata']='';
        }else{
            $data['caridata']= str_replace('%20',' ',$cr);
            $SQLcari .= " AND ( b.name LIKE '%".str_replace('%20',' ',$cr)."%' 
                        or b.userid LIKE '%".str_replace('%20',' ',$cr)."%' ) ";
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

            $SQLcari .= " and b.deptid in (".implode(',', $s).") ";
			
        }
		
        if(!empty($stspeg)) {
            $s = array();
            foreach($stspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and b.jftstatus in (".implode(',', $s).") ";
        }

        if(!empty($jnspeg)) {
            $s = array();
            foreach($jnspeg as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and b.jenispegawai in (".implode(',', $s).") ";
        }
		
		if(!empty($stausverifikasi)) {
            $s = array();
            foreach($stausverifikasi as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and a.status_verifikasi in (".implode(',', $s).") ";
        }
		
		if(!empty($stausketidakhadiran)) {
            $s = array();
            foreach($stausketidakhadiran as $ar)
                $s[] = "'".$ar."'";

            $SQLcari .= " and a.status in (".implode(',', $s).") ";
        }
		
		$aksespegawai=$this->session->userdata('s_access');
		if($aksespegawai == '1'){
			var_dump($SQLcari);
		}
		
        $SQLcari .= " ORDER BY a.create_date DESC ";
        $query = $this->ketidakhadiran->getDaftar(1,$limited,$offset,null,$SQLcari);
        $jum_data = $this->ketidakhadiran->getDaftar(0,null,null,null,$SQLcari);
        $this_url = site_url("ketidakhadiran/pagging");
        $data2 = $this->mypagination->getPagination($jum_data->num_rows(),$limited,$this_url,3);
        $data['paging'] = $data2['link'];
        $data['offset'] = $offset;
        $data['limit_display'] = $limited;
        $data['jum_data'] = $jum_data->num_rows();
        $data['result'] = $query->result();
        $this->load->view('list',$data);
    }

    function form()
    {
        $data['lstShift'] = $this->utils->getShiftAktif()->result();
        $this->load->view('form',$data);
    }

    function save()
    {
        ini_set('MAX_EXECUTION_TIME', '-1');

        $postdatestart = explode('T', $this->input->post('start')."T00:00:00");
        $postdateend = explode('T', $this->input->post('end')."T00:00:00");
        $datestart = strtotime(dmyToymd($postdatestart[0]));
        $dateend = strtotime(dmyToymd($postdateend[0]));
        $userid = explode(',', $this->input->post('userid'));

        if($this->input->post('orgid'))
            $orgid = $this->pegawai->deptonall($this->input->post('orgid'));
        else
            //$orgid = $this->session->userdata('deptid')!=''?$this->pegawai->deptonall(explode(',', $this->session->userdata('deptid'))):array();
            $orgid = $this->session->userdata('deptid')!=''?$this->pegawai->deptonall($this->session->userdata('deptid')):array();

        $range = ($dateend - $datestart) / 86400;
        $arraish = array();
        $arraisha = array();



        if ($this->input->post('userid') == '') {
            $userd = $this->pegawai->getuserid($orgid);
            $usera = array();
            foreach ($userd->result() as $usr)
                $usera[] = $usr->userid;
            $useridi = $usera;
        } else
            $useridi = $userid;

        /*$this->db->where_in('userid', $useridi);
        $this->db->where('rosterdate >=', date('Y-m-d', $datestart));
        $this->db->where('rosterdate <=', date('Y-m-d', $dateend));
        $result = $this->db->get('rosterdetails')->result();
        foreach ($result as $row)
        {
            $this->db->where('id', $row->id);
            $query = $this->db->get("rosterdetails");
            $datas = $query->row_array();
            log_history("delete", "rosterdetails", $datas);

            if (isset($datas)) {
                createLog("Menghapus jadwal " . $datas["userid"] . " " . $datas["rosterdate"] . " " . $datas["absence"] . " " . $datas["attendance"], "Sukses");
            }

            $this->db->where('id', $row->id);
            $this->db->delete('rosterdetails');
        }*/

        /*$this->db->where_in('userid', $useridi);
        $this->db->where('rosterdate >=', date('Y-m-d', $datestart));
        $this->db->where('rosterdate <=', date('Y-m-d', $dateend));
        $this->db->delete('rosterdetails');*/

        foreach ($useridi as $ros) {
            for ($j = 0; $j <= $range; $j++) {
                $day = date('N', $datestart + ($j * 86400));
                $tgal = $datestart + ($j * 86400);

                if ($day == 1) {
                    if($this->input->post('sel1')!="0")
                    {
                        $arraish[$tgal] = $this->input->post('sel1');
                    } else
                    {
                        $arraish[$tgal] = "NWDS";
                    }

                } else if ($day == 2) {
                    if($this->input->post('sel2')!="0") {$arraish[$tgal] = $this->input->post('sel2');} else
                    {
                        $arraish[$tgal] = "NWDS";
                    }

                } else if ($day == 3) {
                    if($this->input->post('sel3')!="0") {$arraish[$tgal] = $this->input->post('sel3'); } else
                    {
                        $arraish[$tgal] = "NWDS";
                    }

                } else if ($day == 4) {
                    if($this->input->post('sel4')!="0") {$arraish[$tgal] = $this->input->post('sel4'); } else
                    {
                        $arraish[$tgal] = "NWDS";
                    }

                } else if ($day == 5) {
                    if($this->input->post('sel5')!="0") {$arraish[$tgal] = $this->input->post('sel5');} else
                    {
                        $arraish[$tgal] = "NWDS";
                    }

                } else if ($day == 6) {
                    if($this->input->post('sel6')!="0") {$arraish[$tgal] = $this->input->post('sel6');} else
                    {
                        $arraish[$tgal] = "NWDS";
                    }

                } else if ($day == 7) {
                    if($this->input->post('sel7') != "0") {$arraish[$tgal] = $this->input->post('sel7');} else
                    {
                        $arraish[$tgal] = "NWDS";
                    }
                }


                if ($arraish[$tgal] !== "0") {
                    $this->db->select('status');
                    $this->db->from('bukatutup');
                    $this->db->where('idbln', date('n', $tgal));
                    $this->db->where('tahun', date('Y', $tgal));
                    $query = $this->db->get();
                    if ($query->num_rows()>0) {
                        $bukatutup = $query->row()->status;
                    } else {
                        $bukatutup = FALSE;
                    }

                    if ($bukatutup) {
                        $this->db->select('rosterdate');
                        $this->db->from('rosterdetails');
                        $this->db->where('rosterdate', date('Y-m-d', $tgal));
                        $this->db->where('userid', $ros);
                        $queryR = $this->db->get();
                        //print_r($this->db->last_query());
                        $jml = $queryR->num_rows();
                        if ($jml==0) {
                            $savedata = array('userid' => $ros, 'rosterdate' => date('Y-m-d', $tgal), 'absence' => $arraish[$tgal]);
                            $this->db->insert('rosterdetails', $savedata);
                            createLog("Membuat jadwal " . $ros . " " . date('Y-m-d', $tgal) . " " . $arraish[$tgal], "Sukses");
                        } else
                        {
                            $savedata = array('absence' => $arraish[$tgal]);
                            $this->db->where('rosterdate', date('Y-m-d', $tgal));
                            $this->db->where('userid', $ros);
                            $this->db->update('rosterdetails', $savedata);
                            createLog("Merubah jadwal " . $ros . " " . date('Y-m-d', $tgal) . " " . $arraish[$tgal], "Sukses");
                        }
                    }
                }
            }
        }

        $data['msg'] = 'Data berhasil disimpan..';
        $data['status'] = 'succes';

        die(json_encode($data));
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */