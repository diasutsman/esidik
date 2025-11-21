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
}
