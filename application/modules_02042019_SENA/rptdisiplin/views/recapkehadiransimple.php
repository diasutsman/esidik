<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php //if($index==0) { 
		$clss=''; ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/resources/css/printrekap.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Rekapitulasi Kehadiran</title>

<style> .phone{ mso-number-format:\@; } </style>
</head>

<body>

<?php
if($cominfo['companyname']!='') {
?>
<center>
    <h1><?php echo $cominfo["companyname"];?></h1>
    <?php echo $cominfo["address1"];?><br>
    Telepon: <?php echo $cominfo["phone"];?>, Faks: <?php echo $cominfo["fax"];?>
</center><br><br><?php } ?>
<center>
<h1>
    <?php echo strtoupper('Laporan Rekapitulasi Kehadiran'); ?>
</h1>

    <?php echo "<div style='font-size: 11px;font-family: arial;'>".$periode."</div>"; ?>
    </center>
<br>
<?php
	$headcol = '<tr align="center">
				<th width="20">'.strtoupper($this->lang->line('no')).'</th>
				<th width="120">'.strtoupper($this->lang->line('userid')).'</th>
				<th width="220">'.strtoupper($this->lang->line('name')).'</th>
				<th width="100">GOLONGAN</th>
				<th width="100">KELAS JABATAN</th>
				<th width="30">Ijin</th>
				<th width="30">Sakit</th>
				<th width="30">Cuti</th>
				<th width="30">Dinas</th>
				<th width="30">TB</th>
				<th width="30">TK</th>
				<th width="30">TD</th>
				<th width="30">PC</th>
				<th width="200">Ket</th>
				</tr><tr>';
	
	$tampilanReport = '<table class="head" width = "1200">';	
				
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
			} else if($que->attendance == 'AB_3' || $que->attendance == 'AB_4' || $que->attendance == 'AB_5' || $que->attendance == 'AB_6' || $que->attendance == 'AB_7' || $que->attendance == 'AB_8' || $que->attendance == 'AB_9' || $que->attendance == 'AB_14' || $que->attendance == 'AB_17') {
				$datarekap[$que->userid][$que->date_shift]='C';
			} else if ($que->attendance == 'AB_13') {
				$datarekap[$que->userid][$que->date_shift]='DL';
			} else if ($que->attendance == 'AB_15' || $que->attendance == 'AT_DK') {
				$datarekap[$que->userid][$que->date_shift]='PDK';
			} else if ($que->attendance == 'AB_16') {
				$datarekap[$que->userid][$que->date_shift]='TB';
			} else if ($que->attendance == 'AB_11') {
				$datarekap[$que->userid][$que->date_shift]='IJ';
			}
		}
	}
	$tampilanReport .= '<tr align="center"><td colspan="10" align="left" style="border:0px"><br><h4> '.$this->lang->line('menu_main_organization').': '.$nama_dept.'</h4></td></tr>';
	$tampilanReport .= $headcol;
	//print_r($datarekap);exit;
	foreach($group_per_date->result() as $dataatt)
	{				
		/* if(!in_array($dataatt->deptname,$_arrDept)){
			$_arrDept[] = $dataatt->deptname;
			$tampilanReport .= '<tr align="center"><td colspan="10" align="left" style="border:0px"><br><h4> '.$this->lang->line('menu_main_organization').': '.$dataatt->deptname.'</h4></td></tr>';
			$tampilanReport .= $headcol;
		} */
		$tampilanReport .= '<tr >
		<td align="center">'.($k+1).'</td>
		<td align="left" class="phone">'.$dataatt->userid.'</td>
		<td align="left">'.$dataatt->name.'</td>
		<td align="left">'.$dataatt->golru.'</td>
		<td align="left">'.$dataatt->kelasjabatan.'</td>';
		$P = 0; $A = 0; $OFF = 0; $L = 0; $S = 0; $C = 0; $DL = 0; $PDK = 0; $TB = 0; $IJ = 0; $TD = 0; $PC = 0;
		
		for($i=0;$i<count($arr_days);$i++){		
			if(isset($datarekap[$dataatt->userid][$arr_days[$i]])) {
				//$tampilanReport .= '<td align="center" class="'.$datarekap[$dataatt->userid][$arr_days[$i]].'">'.$datarekap[$dataatt->userid][$arr_days[$i]].'</td>';
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
			}
		}
		$tampilanReport .= '<td align="center">'.$IJ.'</th>
							<td align="center">'.$S.'</th>
							<td align="center">'.$C.'</th>
							<td align="center">'.$DL.'</th>
							<td align="center">'.$TB.'</th>
							<td align="center">'.$A.'</th>
							<td align="center">'.$TD.'</th>
							<td align="center">'.$PC.'</th>
							<td align="center"></th>
							</tr>';			
		$k++;				
	}		
	echo $tampilanReport."</table>";
?>
<br><br>KETERANGAN : <br><br>
<table width="500" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50" align="center">TB</td>
    <td width="450">TUGAS BELAJAR</td>
  </tr>
  <tr>
    <td width="50" align="center">TK</td>
    <td width="450">ALPA / TANPA KETERANGAN</td>
  </tr>
  <tr>
    <td width="50" align="center">TD</td>
    <td width="450">TERLAMBAT DATANG</td>
  </tr>
  <tr>
    <td width="50" align="center">PC</td>
    <td width="450">PULANG LEBIH AWAL</td>
  </tr>
</table>