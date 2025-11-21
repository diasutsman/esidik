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
<style> .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
</style>
<center>
    <h1><?php echo $cominfo["companyname"];?></h1>
    <?php echo $cominfo["address1"];?><br>
    Telepon: <?php echo $cominfo["phone"];?>, Faks: <?php echo $cominfo["fax"];?>
<hr/>
<h1>

        <?php echo strtoupper('Laporan Rekapitulasi Kehadiran'); ?>
        <?php echo "<div style='font-size: 11px;font-family: arial;'>" . $periode . "</div>"; ?>
</h1>
</center>
<br>
<?php
$headcol = '<thead><tr align="center" class="head">
				<th width="20" rowspan="2" class="text">No.</th>
				<th width="120"  rowspan="2" class="text">NIP</th>
				<th width="220"  rowspan="2" class="text">Nama</th>
				<th width="380"  rowspan="2" class="text">Pangkat / Go. Ruang</th>
				<th width="380"  rowspan="2" class="text">JABATAN</th>
				<th width="30"  rowspan="2" class="text">Alpa</th>
				<th width="70"  rowspan="2" class="text">Atasan Langsung</th>
				<th width="50"  rowspan="2" class="text">Indikasi Pelanggaran</th>
				<th width="30" colspan="2" class="text">Tindak Lanjut</th>
				<th width="380"  rowspan="2" class="text">Keterangan</th>
				</tr>';

$headcol .= '<tr><th width="30"  class="text">Sudah</th>
				<th width="30"  class="text">Belum</th>
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
foreach ($data as $dataatt) {
    $tampilanReport .= '<tr border="1">
		<td align="center" class="text">' . ($k + 1) . '</td>
		<td align="left" class="text"> ' . $dataatt['userid'] . '</td>
		<td align="left" class="text">' . $dataatt['name'] . '</td>
		<td align="left" class="text">' . $dataatt['pangkat'] . " ( " . $dataatt['golru'] . ' ) </td>
		<td align="left" class="text">' . $dataatt['title'] . '</td>
		';

    /*$A = 0;

    //foreach($group_per_date->result() as $dataatt)
    {
        for ($i = 0; $i < count($arr_days); $i++) {
            if (isset($datarekap[$dataatt->userid][$arr_days[$i]])) {
                if ($datarekap[$dataatt->userid][$arr_days[$i]] == 'A') $A++;
            }
        }
        $k++;
    }*/

    $tampilanReport .= '
							<td align="center" class="text">'.$dataatt["absent"].'</td>
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
