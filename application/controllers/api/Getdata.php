<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Getdata extends CI_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }

    public function dataabsen()
    {
        $this->db->where('DATE(checktime)', "'".date('Y-m-d')."'", FALSE);
        //$this->db->limit(10);
        $dataabsen = $this->db->get("checkinout")->result_array();
        if (!empty($dataabsen))
        {
            header('Content-Type: application/json');
            $arr = [
                'status' => TRUE,
                'message' => 'Data absensi',
                'data' => $dataabsen
            ];
            echo json_encode($arr);

        }
        else
        {
            $arr = [
                'status' => FALSE,
                'message' => 'Tidak ada data absensi',
                'data' => array()
            ];
            echo json_encode($arr);
        }
    }

	public function dataharilibur($thun=null)
    {
        if (empty($thun))
            $thun= date("Y");

        $sql="SELECT startdate,enddate,info FROM holiday
            WHERE YEAR(startdate)=$thun AND YEAR(enddate)=$thun
            UNION ALL
            SELECT CONVERT(STR_TO_DATE(CONCAT(tgl_libur,'-','$thun'),'%m-%d-%Y'), DATE) startdate,  
            CONVERT(STR_TO_DATE(CONCAT(tgl_libur2,'-','$thun'),'%m-%d-%Y'), DATE) enddate,
            ket_libur info
            FROM ref_def_libur";
        $qry = $this->db->query($sql);
        //$this->db->limit(10);
        $dataabsen = $qry->result_array();
        header('Content-Type: application/json');
        if (!empty($dataabsen))
        {

            $arr = [
                'status' => TRUE,
                'message' => 'Data hari libur',
                'data' => $dataabsen
            ];
            $this->output->set_output(json_encode($arr));
        }
        else
        {
            $arr = [
                'status' => FALSE,
                'message' => 'Tidak ada data',
                'data' => array()
            ];
            $this->output->set_output(json_encode($arr));
        }
    }
	
	public function checkinout()
    {
        header('Content-Type: application/json');
        $vtgl = $this->input->post('tanggal');
        $reqTgl = date('Y-m-d',strtotime($vtgl));
        if ($vtgl==null)
        {
            $arr = array(
                'status' => TRUE,
                'message' => 'Cek kembali parameter',
            );
        } else {
            $this->db->select('userid as nip,checktime');
            $this->db->where('DATE(checktime)', "'".$reqTgl."'", FALSE);
            $dataabsen = $this->db->get("checkinout");
            if ($dataabsen->num_rows() > 0) {
                $arr = array(
                    'status' => FALSE,
                    'message' => 'List data presensi',
                    'data' => $dataabsen->result_array()
                );
            } else {
                $arr = array(
                    'status' => TRUE,
                    'message' => 'Tidak ada data cekin',
                );
            }
        }

        $this->output->set_output(json_encode($arr));
    }
	
	
    public function cutitahunan($token=null,$nip='',$tahun='')
    {
        header('Content-Type: application/json');

        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
        if ($rwo->num_rows()>0) {

            $this->db->select('ifnull(count(*),0) as jumlah');
            $this->db->where('attendance', 'AB_3');
            $this->db->where('userid', $nip);
            $this->db->where('Year(rosterdate)', "'" . $tahun . "'", FALSE);
            $dataabsen = $this->db->get("rosterdetailsatt");
            //log_message('error', $this->db->last_query());
            $arr = array(
                'status' => TRUE,
                'data' => $dataabsen->row()->jumlah,
            );

            $arrint= array(
                'token_api'=>$token,
                'api_method'=>'cutitahunan',
                'api_ip'=>getRealIpAddr()
            );

            $this->db->insert('api_log',$arrint)    ;
            $this->output->set_output(json_encode($arr));
        } else
        {
            $arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            createLog("API cutitahunan",$arr['msg']);
            $this->output->set_output(json_encode($arr));
        }
    }
	
	public function statushadir($token=null,$nip='',$tahun='',$sts='')
    {
        header('Content-Type: application/json');

        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
        if ($rwo->num_rows()>0) {
            $this->db->select('ifnull(count(*),0) as jumlah');
            $this->db->where_in('attendance', $sts);
            $this->db->where('userid', $nip);
            $this->db->where('Year(rosterdate)', "'" . $tahun . "'", FALSE);
            $dataabsen = $this->db->get("rosterdetailsatt");
            //log_message('error', $this->db->last_query());
            $arr = array(
                'status' => TRUE,
                'data' => $dataabsen->row->jumlah,
            );

            $arrint= array(
                'token_api'=>$token,
                'api_method'=>'statushadir',
                'api_ip'=>getRealIpAddr()
            );

            $this->db->insert('api_log',$arrint)    ;
            $this->output->set_output(json_encode($arr));
        } else
        {
            $arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            createLog("API statushadir ",$arr['msg']);
            $this->output->set_output(json_encode($arr));
        }
    }

	public function kehadiran($token=null, $nip='', $tglstart='', $tglend=''){
		header('Content-Type: application/json');
        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
		if ($rwo->num_rows()>0) {
			$query="select process.*,process_upacara.date_shift as date_shift2, process_upacara.shift_in as shift_in2, 
					process_upacara.shift_out as shift_out2, process_upacara.date_in as date_in2, process_upacara.check_in as check_in2,
					process_upacara.attendance as attendance2 from process
					left join process_upacara on process_upacara.userid=process.userid and process_upacara.date_shift=process.date_shift
					where process.userid = '".$nip."' and process.date_shift between  '".$tglstart."' and '".$tglend."'
			";
			$dataabsen = $this->db->query($query);
			$arr = array(
                'status' => TRUE,
                'data' => $dataabsen->result_array(),
            );
			$this->output->set_output(json_encode($arr));
		}else{
			$arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            $this->output->set_output(json_encode($arr));
		}
	}
	
	public function tunjangan($token=null, $tgl='', $kelas=''){
		header('Content-Type: application/json');
        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
		if ($rwo->num_rows()>0) {
			 $tunjang = 0;
			if(!empty($kelas)) {
				$sql = "select tunjangan from tunjanganhistory where tglubah <= '".$tgl."' and kelasjabatan = ".$kelas." order by tglubah desc limit 1";
				$query = $this->db->query($sql);
				$sqlt = "select tunjangan from tunjanganhistory where tglubah >= '".$tgl."' and kelasjabatan = ".$kelas." order by tglubah asc limit 1";
				$queryt = $this->db->query($sqlt);
				$sqls = "select tunjangan from mastertunjangan where kelasjabatan = ".$kelas." limit 1";
				$querys = $this->db->query($sqlt);
				if($query->num_rows()==1)
					$tunjang = $query->row()->tunjangan / 2;
				else if($queryt->num_rows()==1)
					$tunjang = $queryt->row()->tunjangan / 2;
				else if($querys->num_rows()==1)
					$tunjang = $querys->row()->tunjangan / 2;
			}
			
			$arr = array(
                'status' => TRUE,
                'data' => $tunjang,
            );
			$this->output->set_output(json_encode($arr));
		}else{
			$arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            $this->output->set_output(json_encode($arr));
		}
	}
	
	public function kelasjabatan($token=null, $userid= '', $tgl=''){
		header('Content-Type: application/json');
        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
		if ($rwo->num_rows()>0) {
			$sql = "select kelas from userinfohistory where userid = '".$userid."' and tmtjabatan <= '".$tgl."' order by tmtjabatan desc limit 1";
			$query = $this->db->query($sql);
			$sqlt = "select kelas from userinfohistory where userid = '".$userid."' and tmtjabatan >= '".$tgl."' order by tmtjabatan asc limit 1";
			$queryt = $this->db->query($sqlt);
			$vkelas = null;
			if($query->num_rows()==1) {
				$row = $query->row();
				if ( isset($row))
				{
					$vkelas = $row->kelas;
				}
			}
			else if($queryt->num_rows()==1) {
				$row = $queryt->row();
				if ( isset($row))
				{
					$vkelas = $row->kelas;
				}
			}
			
			$arr = array(
                'status' => TRUE,
                'data' => $vkelas,
            );
			$this->output->set_output(json_encode($arr));
		}else{
			$arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            $this->output->set_output(json_encode($arr));
		}
	}
	
	public function datastatus($token=null,$nip='',$tgl1='', $tgl2='')
    {
        header('Content-Type: application/json');

        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
        if ($rwo->num_rows()>0) {

            $query="
                    select * from (
                        SELECT MIN(checktime) AS keterangan
                                        FROM checkinout 
                                        WHERE checkinout.userid='$nip' AND DATE(checktime) between '$tgl1' and '$tgl2'
                                        UNION 
                                        SELECT MAX(checktime) AS keterangan
                                        FROM checkinout
                                        WHERE checkinout.userid='$nip' AND DATE(checktime) between '$tgl1' and '$tgl2'
                                        UNION
                                        SELECT attendance AS keterangan
                                        FROM rosterdetailsatt
                                        WHERE userid='$nip' AND rosterdate BETWEEN  '$tgl1' and '$tgl2'
                        ) as dataskrg
                        where keterangan is not NULL";
            $dataabsen = $this->db->query($query);
            //log_message('error', $this->db->last_query());
            $arr = array(
                'status' => TRUE,
                'data' => $dataabsen->result_array(),
            );

            $arrint= array(
                'token_api'=>$token,
                'api_method'=>'datastatus',
                'api_ip'=>getRealIpAddr()
            );

            $this->db->insert('api_log',$arrint)    ;
            $this->output->set_output(json_encode($arr));
        } else
        {
            $arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            createLog("API Satastatus",$arr['msg']);
            $this->output->set_output(json_encode($arr));
        }
    }
	
    public function datastatus_old($token=null,$nip='',$tgl='')
    {
        header('Content-Type: application/json');

        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
        if ($rwo->num_rows()>0) {

            $query="SELECT MIN(checktime) AS keterangan
                    FROM checkinout 
                    WHERE checkinout.userid='$nip' AND DATE(checktime)='$tgl'
                    UNION 
                    SELECT MAX(checktime) AS keterangan
                    FROM checkinout
                    WHERE checkinout.userid='$nip' AND DATE(checktime)='$tgl'
                    UNION
                    SELECT attendance AS keterangan
                    FROM rosterdetailsatt
                    WHERE userid='$nip' AND rosterdate='$tgl'
                    ";
            $dataabsen = $this->db->query($query);
            //log_message('error', $this->db->last_query());
            $arr = array(
                'status' => TRUE,
                'data' => $dataabsen->row_array(),
            );

            $arrint= array(
                'token_api'=>$token,
                'api_method'=>'datastatus',
                'api_ip'=>getRealIpAddr()
            );

            $this->db->insert('api_log',$arrint)    ;
            $this->output->set_output(json_encode($arr));
        } else
        {
            $arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            createLog("API datastatus",$arr['msg']);
            $this->output->set_output(json_encode($arr));
        }
    }

    public function statustidakhadir($token=null,$nip='',$tahun='')
    {
        header('Content-Type: application/json');

        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
        if ($rwo->num_rows()>0) {

          $qry="SELECT ket_status_kategori AS keterangan,CASE
                WHEN IFNULL(COUNT(*),0)>1 THEN 'Mengambil' ELSE 'Belum' END AS status_ket,
            IFNULL(COUNT(*),0) AS jumlahdata FROM ref_status_kategori 
            LEFT JOIN attendance ON attendance.status_kategori_id=ref_status_kategori.id_status_kategori
            LEFT JOIN absence ON absence.status_kategori_id=ref_status_kategori.id_status_kategori
            LEFT JOIN rosterdetailsatt ON (rosterdetailsatt.attendance=absence.abid OR rosterdetailsatt.attendance=attendance.atid)
            WHERE id_status_kategori IN (2,7,8,9,10,11,17) AND rosterdetailsatt.userid='$nip' AND YEAR(rosterdate)=$tahun
            GROUP BY id_status_kategori";
            $dataabsen = $this->db->query($qry);
            //log_message('error', $this->db->last_query());
            $arr = array(
                'status' => TRUE,
                'data' => $dataabsen->row()->jumlah,
            );

            $arrint= array(
                'token_api'=>$token,
                'api_method'=>'statustidakhadir',
                'api_ip'=>getRealIpAddr()
            );

            $this->db->insert('api_log',$arrint)    ;
            $this->output->set_output(json_encode($arr));
        } else
        {
            $arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            createLog("API statustidakhadir",$arr['msg']);
            $this->output->set_output(json_encode($arr));
        }
    }

    public function daftartidakhadir($token=null,$nip='',$tahun='')
    {
        header('Content-Type: application/json');

        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
        if ($rwo->num_rows()>0) {

            $qry="SELECT ket_status_kategori AS keterangan,rosterdate AS tanggal FROM ref_status_kategori 
                    LEFT JOIN attendance ON attendance.status_kategori_id=ref_status_kategori.id_status_kategori
                    LEFT JOIN absence ON absence.status_kategori_id=ref_status_kategori.id_status_kategori
                    LEFT JOIN rosterdetailsatt ON (rosterdetailsatt.attendance=absence.abid OR rosterdetailsatt.attendance=attendance.atid)
                    WHERE id_status_kategori IN (2,7,8,9,10,11,17) AND rosterdetailsatt.userid='$nip' AND YEAR(rosterdate)=$tahun";
            $dataabsen = $this->db->query($qry);
            //log_message('error', $this->db->last_query());
            $arr = array(
                'status' => TRUE,
                'data' => $dataabsen->row()->jumlah,
            );

            $arrint= array(
                'token_api'=>$token,
                'api_method'=>'daftartidakhadir',
                'api_ip'=>getRealIpAddr()
            );

            $this->db->insert('api_log',$arrint)    ;
            $this->output->set_output(json_encode($arr));
        } else
        {
            $arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            createLog("API daftartidakhadir",$arr['msg']);
            $this->output->set_output(json_encode($arr));
        }
    }
	
	private function saveapilog($tokenid,$methodname='')
    {
        $arrint= array(
            'token_api'=>$tokenid,
            'api_method'=>$methodname,
            'api_ip'=>getRealIpAddr()
        );

        $this->db->insert('api_log',$arrint) ;
    }
	
	public function statuspresensi($token=null,$idsatker='',$sts='',$tgl=null,$limit=10)
    {
        header('Content-Type: application/json');

        if ($tgl==null)
        {
            $vtgl =date('Y-m-d');
        } else {
            $vtgl =date('Y-m-d',strtotime($tgl));
        }
        
        $this->db->where('api_token', $token);
        $rwo = $this->db->get("api_client");
        if ($rwo->num_rows()>0) {

            switch ($sts)
            {
                case 'cpt': //datang cepat
                    $qry="SELECT userid AS nip,date_in,check_in,nama,b.deptname AS unit_kerja
                        FROM view_ontime a
                        INNER JOIN departments b ON a.deptid=b.deptid
                        WHERE SUBSTR(a.deptid,1,2)='$idsatker' and a.date_shift='$vtgl'
						AND check_in IS NOT NULL
                        order BY check_in asc
                        limit $limit
                        ";

                    $dataabsen = $this->db->query($qry);
                    //log_message('error', $this->db->last_query());
                    $arr = array(
                        'status' => TRUE,
                        'data' => $dataabsen->result_array(),
                    );

                    $this->saveapilog($token,'statuspresensi-'.$sts);
                    break;
                case 'tlt': //datang terlambat
                    $qry="SELECT userid AS nip,date_in,check_in,nama,b.deptname AS unit_kerja
                        FROM view_late a
                        INNER JOIN departments b ON a.deptid=b.deptid
                        WHERE SUBSTR(a.deptid,1,2)='$idsatker' and a.date_shift='$vtgl'
						AND check_in IS NOT NULL
                        order BY check_in desc
                        limit $limit";
                    $dataabsen = $this->db->query($qry);
                    //log_message('error', $this->db->last_query());
                    $arr = array(
                        'status' => TRUE,
                        'data' => $dataabsen->result_array(),
                    );

                    $this->saveapilog($token,'statuspresensi-'.$sts);
                    break;
                default:
                    $arr = array(
                        'status' => FALSE,
                        'msg' => 'Not Valid Parameter!!',
                    );
                    createLog("API statuspresensi ",$arr['msg']);
                    break;

            }
            $this->output->set_output(json_encode($arr));
        } else
        {
            $arr = array(
                'status' => FALSE,
                'msg' => 'Not Authorize!!',
            );
            createLog("API statuspresensi ",$arr['msg']);
            $this->output->set_output(json_encode($arr));
        }
    }
}
