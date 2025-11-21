<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php //if($index==0) { 
$clss = '';
if ($excelid == 0) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <style>
        <?php include FCPATH."/assets/css/printrekap.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rekapitulasi Kehadiran</title>
</head>
<?php } ?>
<body>
<style> .phone {
        mso-number-format: \@;
    } </style>
<table class="head" width="100%">
    <tr>
        <td align="center" style="border:0px"><h1><?php echo $cominfo["companyname"] ?></h1></td>
    </tr>
    <tr>
        <td align="center" style="border:0px"><?php echo $cominfo["address1"] ?></td>
    </tr>
    <tr>
        <td align="center" style="border:0px">Telepon: <?php echo $cominfo["phone"] ?>,
            Faks: <?php echo $cominfo["fax"] ?></td>
    </tr>
</table>
<hr/>
<h1>
    <center>
        <?php echo strtoupper('Laporan Rekapitulasi Kehadiran'); ?>
        <?php echo "<div style='font-size: 11px;font-family: arial;'>" . $periode . "</div>"; ?>
    </center>
</h1>
<br>
<?php
$headcol = '<thead><tr align="center" class="head">
				<th width="20" rowspan="2">No.</th>
				<th width="120"  rowspan="2">NIP</th>
				<th width="220"  rowspan="2">Nama</th>
				<th width="380"  rowspan="2">Pangkat / Go. Ruang</th>
				<th width="380"  rowspan="2">JABATAN</th>
				<th width="30"  rowspan="2">Alpa</th>
				<th width="70"  rowspan="2">Atasan Langsung</th>
				<th width="50"  rowspan="2">Indikasi Pelanggaran</th>
				<th width="30" colspan="2">Tindak Lanjut</th>
				<th width="380"  rowspan="2">Keterangan</th>
				</tr>';

$headcol .= '<tr><th width="30" >Sudah</th>
				<th width="30" >Belum</th>
				</tr></thead>';

$tampilanReport = '<table width = "100%" border="1">';

$total_hour = 0;
$k = 0;
$_arrDept = array();

$datarekap = array();
foreach ($querycok->result() as $que) {
    if ($que->attendance == 'ALP') {
        $datarekap[$que->userid][$que->date_shift] = 'A';
    }
}
$tampilanReport .= '<tr align="center"><td colspan="11" align="left" ><h4> Unit Kerja: ' . $nama_dept . '</h4></td></tr>';
$tampilanReport .= $headcol;
//print_r($arr_days);
foreach ($group_per_date->result() as $dataatt) {
    $tampilanReport .= '<tr border="1">
		<td align="center">' . ($k + 1) . '</td>
		<td align="left" class="phone"> ' . $dataatt->userid . '</td>
		<td align="left">' . $dataatt->name . '</td>
		<td align="left">' . $dataatt->pangkat . " ( " . $dataatt->golru . ' ) </td>
		<td align="left">' . $dataatt->title . '</td>
		';

    $A = 0;

    //foreach($group_per_date->result() as $dataatt)
    {
        for ($i = 0; $i < count($arr_days); $i++) {
            if (isset($datarekap[$dataatt->userid][$arr_days[$i]])) {
                if ($datarekap[$dataatt->userid][$arr_days[$i]] == 'A') $A++;
            }
        }
        $k++;
    }

    $tampilanReport .= '
							<td align="center">' . $A . '</td>
							<td align="center"></td>
							<td align="center"></td>
							<td align="center"></td>
							<td align="center"></td>
							<td align="center"></td>
							</tr>';
    $k++;
}
echo $tampilanReport . "</table>";

?>
