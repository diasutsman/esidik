<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php //if($index==0) { 
$clss='';
if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <style>
        <?php include FCPATH."/assets/css/printrekap.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Rekapitulasi Disiplin</title>
</head>
<?php } ?>
<body>
<style> .phone{ mso-number-format:\@; } </style>

<table class="head" width="100%">
    <tr>
        <td align="center" style="border:0px"><h1><?php echo $cominfo["companyname"]?></h1></td>
    </tr>
    <tr>
        <td align="center" style="border:0px"><?php echo $cominfo["address1"]?></td>
    </tr>
    <tr>
        <td align="center" style="border:0px">Telepon: <?php echo $cominfo["phone"]?>, Faks: <?php echo $cominfo["fax"]?></td>
    </tr>
</table>
<hr/>
<h1><center>
    <?php echo strtoupper('Laporan Rekapitulasi Disiplin Kepegawaian'); ?>
    <?php echo "<div style='font-size: 11px;font-family: arial;'>".$periode."</div>"; ?>
    </center>
</h1>

<br>
<?php
	$headcol = '<thead><tr align="center">
				<th width="100" rowspan="3">Jumlah Pegawai</th>
				<th width="120" colspan="11">Kehadiran</th>
				</tr>';
$headcol .= '<tr align="center">
				<th width="100" rowspan="2">Hadir</th>
				<th width="100" rowspan="2">TK</th>
				<th width="100" rowspan="2">Izin</th>
				<th width="100" rowspan="2">Sakit</th>
				<th width="100" colspan="5">Cuti</th>
				<th width="100" rowspan="2">Dinas</th>
				<th width="100" rowspan="2">Tugas Belajar</th>
				</tr>';
$headcol .= '<tr align="center">
				<th width="120" >CAP</th>
				<th width="120" >CBR</th>
				<th width="120" >CBS</th>
				<th width="120" >CLTN</th>
				<th width="120" >CT</th>
				</tr></thead>';

	$tampilanReport = '<table width = "1200" border="1">';
				
	$total_hour=0;
	$k=0;
	$_arrDept = array();
	
	$datarekap = array();
	foreach($querycok->result() as $que) {
		if($que->late > 0 && $que->attendance !='AT_AT1' && $que->attendance !='AT_AT3' && $que->attendance !='AT_AT4' && $que->attendance !='AT_AT6' && $que->attendance !='AT_DK' && $que->attendance != 'NWK' && $que->workinholiday != 1 && strpos($que->attendance, 'AB_')===false) 
			$datarekap[$que->userid][$que->date_shift]='TD';
		else if($que->early_departure > 0 && $que->attendance !='AT_AT2' && $que->attendance !='AT_AT5' && $que->attendance !='AT_AT6' && $que->attendance !='AT_DK' && $que->attendance != 'NWK' && $que->workinholiday != 1 && strpos($que->attendance, 'AB_')===false) 
			$datarekap[$que->userid][$que->date_shift]='PC';
		else if ($que->workinholiday==1){
			$datarekap[$que->userid][$que->date_shift]='L';
		} else {
			if($que->attendance == 'NWK') {
				$datarekap[$que->userid][$que->date_shift]='OFF';
			} else if($que->attendance == 'ALP') {
				$datarekap[$que->userid][$que->date_shift]='A';
			} else if($que->attendance == 'AB_1' || $que->attendance == 'AB_2') {
				$datarekap[$que->userid][$que->date_shift]='S';
			} else if($que->attendance == 'AB_14' ) {
                $datarekap[$que->userid][$que->date_shift]='CAP';
            } else if($que->attendance == 'AB_4' || $que->attendance == 'AB_5') {
                $datarekap[$que->userid][$que->date_shift]='CBR';
            } else if($que->attendance == 'AB_7' ) {
                $datarekap[$que->userid][$que->date_shift]='CBS';
            } else if($que->attendance == 'AB_7' ) {
                $datarekap[$que->userid][$que->date_shift]='CLTN';
            } else if($que->attendance == 'AB_3' ) {
                $datarekap[$que->userid][$que->date_shift]='CT';
            }
            else if($que->attendance == 'AB_6' ||
                $que->attendance == 'AB_8' ||
                $que->attendance == 'AB_9' ||
                $que->attendance == 'AB_17') {
				$datarekap[$que->userid][$que->date_shift]='C';
			} else if ($que->attendance == 'AB_13') {
				$datarekap[$que->userid][$que->date_shift]='DL';
			} else if ($que->attendance == 'AB_15' || $que->attendance == 'AT_DK') {
				$datarekap[$que->userid][$que->date_shift]='PDK';
			} else if ($que->attendance == 'AB_16') {
				$datarekap[$que->userid][$que->date_shift]='TB';
			} else if ($que->attendance == 'AB_11') {
				$datarekap[$que->userid][$que->date_shift]='IJ';
			} else {
                $datarekap[$que->userid][$que->date_shift]='H';
            }
		}
	}
	$tampilanReport .= '<tr align="center"><td colspan="12" align="left" ><h4>Unit Kerja: '.$nama_dept.'</h4></td></tr>';
	$tampilanReport .= $headcol;
	//print_r($datarekap);exit;
    $jml=0; $P = 0; $A = 0; $OFF = 0; $L = 0; $S = 0; $C = 0; $DL = 0; $PDK = 0; $TB = 0; $IJ = 0; $TD = 0; $PC = 0;
    $H=0;$CAP=0;$CBR=0;$CBS=0;$CLTN=0;$CT=0;
	foreach($group_per_date->result() as $dataatt)
	{
		for($i=0;$i<count($arr_days);$i++){
			if(isset($datarekap[$dataatt->userid][$arr_days[$i]])) {
				if($datarekap[$dataatt->userid][$arr_days[$i]]=='P') $P++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='A') $A++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='OFF') $OFF++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='L') $L++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='S') $S++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='C') $C++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='DL') $DL++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='PDK') $PDK++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='TB') $TB++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='IJ') $IJ++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='TD') $TD++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='PC') $PC++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='H') $H++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='CAP') $CAP++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='CBR') $CBR++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='CBS') $CBS++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='CLTN') $CLTN++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='CT') $CT++;
			}
		}
        $k++;
	}

$tampilanReport .= '<tr><td align="center">'.$k.'</td>
                        <td align="center">'.($TD+$PC+$H).'</td>
                        <td align="center">'.$A.'</td>
                        <td align="center">'.$IJ.'</td>
                        <td align="center">'.$S.'</td>
                        <td align="center">'.$CAP.'</td>
                        <td align="center">'.$CBR.'</td>
                        <td align="center">'.$CBS.'</td>
                        <td align="center">'.$CLTN.'</td>
                        <td align="center">'.$CT.'</td>
                        <td align="center">'.$DL.'</td>
                        <td align="center">'.$TB.'</td>
                        
                        </tr>';
	echo $tampilanReport."</table>";
?>
<br><br><span style="font-size: 10px">KETERANGAN : </span><br><br>
<table width="500" border="1" cellspacing="0" cellpadding="0" style="font-size: 10px">
 <tr>
    <td width="50" align="center">TK</td>
    <td width="450">ALPA / TANPA KETERANGAN</td>
  </tr>
  <tr>
    <td width="50" align="center">CAP</td>
    <td width="450">Cuti Alasan Penting</td>
  </tr>
  <tr>
    <td width="50" align="center">CBR</td>
    <td width="450">Cuti Bersalin</td>
  </tr>
    <tr>
        <td width="50" align="center">CBS</td>
        <td width="450">Cuti Besar</td>
    </tr>
    <tr>
        <td width="50" align="center">CLTN</td>
        <td width="450">Cuti Diluar Tanggungan Negara</td>
    </tr>
    <tr>
        <td width="50" align="center">CS</td>
        <td width="450">Cuti Sakit</td>
    </tr>
    <tr>
        <td width="50" align="center">CT</td>
        <td width="450">Cuti Tahunan</td>
    </tr>
</table>