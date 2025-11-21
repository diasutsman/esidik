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
    <title>Laporan Rekapitulasi Disiplin Kehadiran</title>
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
        <?php echo strtoupper('Laporan Rekapitulasi DISIPLIN Kehadiran'); ?>
        <?php echo "<div style='font-size: 11px;font-family: arial;'>" . $periode . "</div>"; ?>
    </h1>
</center>
<br>
<?php
$headcol = '<thead><tr align="center" class="head">
				<th width="20" rowspan="3" class="text">No.</th>
				<th width="120"  rowspan="3" class="text">NIP</th>
				<th width="220"  rowspan="3" class="text">Nama</th>
				<th width="380"  rowspan="3" class="text">Pangkat / Go. Ruang</th>
				<th width="380"  rowspan="3" class="text">JABATAN</th>
				<th width="40"  rowspan="3" class="text">Hadir</th>
				<th width="40"  rowspan="3" class="text">Tidak Hadir</th>
				<th colspan="11" class="text">Keterangan Ketidakhadiran</th>
				</tr>';

$headcol .= '<tr>
				<th width="40"  rowspan="2" class="text">TK</th>
				<th width="40" colspan="6" class="text">Cuti</th>
				<th width="40"  rowspan="2" class="text">Dinas</th>
				<th width="80"  rowspan="2" class="text">Tugas Belajar</th>
				<th width="80"  rowspan="2" class="text">Meninggal</th>
				<th width="80"  rowspan="2" class="text">DPK</th>
				</tr>';

$headcol .= '<tr>
                <th width="40" class="text">CT</th>
                <th width="40"  class="text">CBS</th>
                <th width="40"  class="text">CS</th>
                <th width="40"  class="text">CBR</th>
				<th width="40"  class="text">CAP</th>
				<th width="40" class="text">CLTN</th>
				</tr></thead>';

$tampilanReport = '<table width = "100%" border="1">';

$total_hour = 0;
$k = 0;
$_arrDept = array();

$tampilanReport .= '<tr align="center"><td colspan="18" align="left" ><h4> Unit Kerja: ' . $nama_dept . '</h4></td></tr>';
$tampilanReport .= $headcol;

$tWorkDay=0;
$tMasuk=0;
$tAbsen=0;
$persen=0;
$ttlCAP =0;$ttlCBR =0;$ttlCBS =0;$ttlCTLN =0;$ttlCT =0;$ttlDL =0;$ttlTB=0;
$ttlworkinholiday=0;$ttlTK=0;$ttlCS=0;$ttlCB=0;$ttlCB=0;$ttlMeninggal=0;$ttlDPK=0;
foreach ($data as $dataatt) {
    $tampilanReport .= '<tr border="1">
		<td align="center" class="text">' . ($k + 1) . '</td>
		<td align="left" class="text"> ' . $dataatt['userid'] . '</td>
		<td align="left" class="text">' . $dataatt['name'] . '</td>
		<td align="left" class="text">' . $dataatt['pangkat'] . " ( " . $dataatt['golru'] . ' ) </td>
		<td align="left" class="text">' . $dataatt['title'] . '</td>
		';
    $tWorkDay +=$dataatt['workday'];
    $tMasuk +=$dataatt['attendance'];
    $tAbsen +=$dataatt['absent'];
    $ttlCAP +=$dataatt['ttlCAP'];
    $ttlCBR +=$dataatt['ttlCBR'];
    $ttlCBS +=$dataatt['ttlCBS'];
    $ttlCTLN +=$dataatt['ttlCTLN'];
    $ttlCT +=$dataatt['ttlCT'];
    $ttlDL +=$dataatt['ttlDL'];
    $ttlTB +=$dataatt['ttlTB'];
    $ttlCS +=$dataatt['ttlCS'];
    $ttlCB +=$dataatt['ttlCB'];
    $ttlMeninggal +=$dataatt['ttlMeninggal'];
    $ttlDPK +=$dataatt['ttlDPK'];

    $ttlworkinholiday +=$dataatt['workinholiday'];
    $TK=($dataatt["absent"]+$dataatt["ttlCAP"]+$dataatt["ttlCBR"]+$dataatt["ttlCBS"]+$dataatt["ttlCTLN"]+$dataatt["ttlCT"]+$dataatt["ttlDL"]+$dataatt["ttlTB"]);
    $ttlTK += $TK;

    $tampilanReport .= '
                    <td align="center">'.$dataatt['attendance'].'</td>
                    <td align="center">'.$TK.'</td>
                    <td align="center">'.$dataatt["absent"].'</td>
                    <td align="center">'.$dataatt["ttlCT"].'</td>
                    <td align="center">'.$dataatt["ttlCBS"].'</td>
                    <td align="center">'.$dataatt["ttlCS"].'</td>
                    <td align="center">'.$dataatt["ttlCBR"].'</td>
                    <td align="center">'.$dataatt["ttlCAP"].'</td>
                    <td align="center">'.$dataatt["ttlCTLN"].'</td>
                    
                    <td align="center">'.$dataatt["ttlDL"].'</td>
                    <td align="center">'.$dataatt["ttlTB"].'</td>
                    <td align="center">'.$dataatt["ttlMeninggal"].'</td>
                    <td align="center">'.$dataatt["ttlDPK"].'</td>
                    </tr>';
    $k++;
}


$tampilanReport .= '<tr><td align="center" colspan="5">TOTAL</td>
							<td align="center">'.format_angka($tMasuk).'</td>
							<td align="center">'.format_angka($ttlTK).'</td>
							<td align="center">'.format_angka($tAbsen).'</td>
							<td align="center">'.format_angka($ttlCT).'</td>
							<td align="center">'.format_angka($ttlCBS).'</td>
							<td align="center">'.format_angka($ttlCS).'</td>
							<td align="center">'.format_angka($ttlCBR).'</td>
							<td align="center">'.format_angka($ttlCAP).'</td>
							<td align="center">'.format_angka($ttlCTLN).'</td>
							
							<td align="center">'.format_angka($ttlDL).'</td>
							<td align="center">'.format_angka($ttlTB).'</td>
							<td align="center">'.format_angka($ttlMeninggal).'</td>
							<td align="center">'.format_angka($ttlDPK).'</td>
							</tr>';

                    $sh1 = $tWorkDay ;
                    $sh2= $tMasuk;

                    $pMasuk = $sh1==0?0: $sh2 / $sh1;
                    $pAbsen= $sh1==0?0: $tAbsen/ $sh1;
                    $pCAP= $sh1==0?0: $ttlCAP/ $sh1;
                    $pCBR= $sh1==0?0: $ttlCBR/ $sh1;
                    $pCBS= $sh1==0?0: $ttlCBS/ $sh1;
                    $pCTLN= $sh1==0?0: $ttlCTLN/ $sh1;
                    $pCT= $sh1==0?0: $ttlCT/ $sh1;
                    $pCDL= $sh1==0?0: $ttlDL/ $sh1;
                    $pTB= $sh1==0?0: $ttlTB/ $sh1;
                    $pCS= $sh1==0?0: $ttlCS/ $sh1;
                    $pCB= $sh1==0?0: $ttlCB/ $sh1;
                    $pMeninggal= $sh1==0?0: $ttlMeninggal/ $sh1;
                    $pDPK= $sh1==0?0: $ttlDPK/ $sh1;

                    $pTK= $pAbsen+$pCAP+$pCBR+$pCBS+$pCTLN+$pCT+$pCDL+$pTB+$pCS;

$tampilanReport .= '<tr><td align="center" colspan="5">PERSENTASE</td>
							<td align="center">'.format_persen(round($pMasuk*100,2)).'</td>
							<td align="center">'.format_persen(round($pTK*100,2)).'</td>
							<td align="center">'.format_persen(round($pAbsen*100,2)).'</td>
							<td align="center">'.format_persen(round($pCT*100,2)).'</td>
							<td align="center">'.format_persen(round($pCBS*100,2)).'</td>
							<td align="center">'.format_persen(round($pCS*100,2)).'</td>
							<td align="center">'.format_persen(round($pCBR*100,2)).'</td>
							<td align="center">'.format_persen(round($pCAP*100,2)).'</td>
							<td align="center">'.format_persen(round($pCTLN*100,2)).'</td>
							
							<td align="center">'.format_persen(round($pCDL*100,2)).'</td>
							<td align="center">'.format_persen(round($pTB*100,2)).'</td>
							<td align="center">'.format_persen(round($pMeninggal*100,2)).'</td>
							<td align="center">'.format_persen(round($pDPK*100,2)).'</td>
							</tr>';
echo $tampilanReport . "</table>";

?>
<br/>
<table cellspacing="0" cellpadding="0" width="200">
    <thead>
    <tr>
        <td colspan="2">KETERANGAN</td>
    </tr>
    </thead>
    <tr>
        <td colspan="2">Status Ketidakhadiran:
        </td>
    <tr>
    <tr>
        <td width="30" class="text">CT</td>
        <td>Cuti Tahunan</td>
    <tr>
    <tr>
        <td width="30" class="text">CBS</td>
        <td>Cuti Besar</td>
    <tr>
    <tr>
        <td width="30" class="text">CS</td>
        <td>Cuti Sakit</td>
    <tr>
    <tr>
        <td width="30" class="text">CBR</td>
        <td>Cuti Bersalin</td>
    <tr>
    <tr>
        <td width="30" class="text">CAP</td>
        <td>Cuti Alasan Penting</td>
    <tr>
    <tr>
        <td width="30" class="text">CLTN</td>
        <td>Cuti Diluar Tanggungan Negara</td>
    <tr>
</table>

