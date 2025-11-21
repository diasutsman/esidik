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
<table class="head">
	<tr>
    	<td <?php echo $clss; ?> style="border:0px"><h1><?php echo $cominfo['companyname']; ?></h1></td>
    </tr>
	<tr>
    	<td <?php echo $clss; ?> style="border:0px"><?php echo $cominfo['address1']; ?></td>
    </tr>
	<?php
		if ($cominfo['address2']!='') {
	?>
    <tr>
    	<td <?php echo $clss; ?> style="border:0px"><?php echo $cominfo['address2']; ?></td>
    </tr>
	<?php } ?>
    <tr>
      <td <?php echo $clss; ?> style="border:0px"><?php echo $this->lang->line('phone'); ?>: <?php echo $cominfo['phone']; ?>, <?php echo $this->lang->line('fax'); ?>: <?php echo $cominfo['fax']; ?></td>
	  <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
	  <td width="180" rowspan="4" align="center" style="border:0px">&nbsp;</td>
    </tr>
</table><br><br><?php } ?>
<h1>
    <?php echo strtoupper('Laporan Rekapitulasi Kehadiran'); ?>
</h1>

    <?php echo "<div style='font-size: 11px;font-family: arial;'>".$periode."</div>"; ?>
<br>
<?php
	$headcol = '<tr align="center">
				<th width="20" rowspan="2">'.strtoupper($this->lang->line('no')).'</th>
				<th width="120" rowspan="2">'.strtoupper($this->lang->line('userid')).'</th>
				<th width="220" rowspan="2">'.strtoupper($this->lang->line('name')).'</th>
				<th width="380" rowspan="2">JABATAN</th>
				<th colspan="'.count($arr_days).'">'.strtoupper(date('F Y', strtotime($arr_days[1]))).'</th>
				<th colspan="9">STATUS KETIDAKHADIRAN</th>
				</tr><tr>';
				
	for($i=0;$i<count($arr_days);$i++) {		
		$headcol .= '<th width="30">'.date("d",strtotime($arr_days[$i])).'</th>';
	}
	$headcol .= '<th width="30" class="P">P</th>
				<th width="30" class="A">A</th>
				<th width="30" class="OFF">OFF</th>
				<th width="30" class="L">L</th>
				<th width="30" class="S">S</th>
				<th width="30" class="C">C</th>
				<th width="30" class="DL">DL</th>
				<th width="30" class="PDK">PDK</th>
				<th width="30" class="TB">TB</th>
				</tr>';
	
	$tampilanReport = '<table class="head" width = "2500">';	
				
	$total_hour=0;
	$k=0;
	$_arrDept = array();
	
	$datarekap = array();
	foreach($querycok->result() as $que) {
		if(($que->check_in!='' || $que->check_out !='') && $que->attendance != 'NWK' && $que->workinholiday != 1 && $que->workinholiday != 2) 
			$datarekap[$que->userid][$que->date_shift]='P';
		else if ($que->workinholiday==1){
			$datarekap[$que->userid][$que->date_shift]='L';
		} else {
			if($que->attendance == 'NWK' || $que->attendance == 'NWDS' || $que->workinholiday == 2) {
				$datarekap[$que->userid][$que->date_shift]='OFF';
			} else if($que->attendance == 'ALP') {
				$datarekap[$que->userid][$que->date_shift]='A';
			} else if($que->attendance == 'AB_1' || $que->attendance == 'AB_2') {
				$datarekap[$que->userid][$que->date_shift]='S';
			} else if($que->attendance == 'AB_3' || $que->attendance == 'AB_4' || $que->attendance == 'AB_11' || $que->attendance == 'AB_5' || $que->attendance == 'AB_6' || $que->attendance == 'AB_7' || $que->attendance == 'AB_8' || $que->attendance == 'AB_9' || $que->attendance == 'AB_14' || $que->attendance == 'AB_17') {
				$datarekap[$que->userid][$que->date_shift]='C';
			} else if ($que->attendance == 'AB_13') {
				$datarekap[$que->userid][$que->date_shift]='DL';
			} else if ($que->attendance == 'AB_15' || $que->attendance == 'AT_DK') {
				$datarekap[$que->userid][$que->date_shift]='PDK';
			} else if ($que->attendance == 'AB_16') {
				$datarekap[$que->userid][$que->date_shift]='TB';
			}
		}
	}
	$tampilanReport .= '<tr align="center"><td colspan="10" align="left" style="border:0px"><br><h4> '.$this->lang->line('menu_main_organization').': '.$nama_dept.'</h4></td></tr>';
	$tampilanReport .= $headcol;
	//print_r($datarekap);exit;
	foreach($group_per_date->result() as $dataatt)
	{				
		/*if(!in_array($dataatt->deptname,$_arrDept)){
			$_arrDept[] = $dataatt->deptname;
			$tampilanReport .= '<tr align="center"><td colspan="10" align="left" style="border:0px"><br><h4> '.$this->lang->line('menu_main_organization').': '.$dataatt->deptname.'</h4></td></tr>';
			
		}*/
		
		$tampilanReport .= '<tr >
		<td align="center">'.($k+1).'</td>
		<td align="left" class="phone">'.$dataatt->userid.'</td>
		<td align="left">'.$dataatt->name.'</td>
		<td align="left">'.$dataatt->title.'</td>';
		$P = 0; $A = 0; $OFF = 0; $L = 0; $S = 0; $C = 0; $DL = 0; $PDK = 0; $TB = 0;
		for($i=0;$i<count($arr_days);$i++){		
			if(isset($datarekap[$dataatt->userid][$arr_days[$i]])) {
				$tampilanReport .= '<td align="center" class="'.$datarekap[$dataatt->userid][$arr_days[$i]].'">'.$datarekap[$dataatt->userid][$arr_days[$i]].'</td>';
				if($datarekap[$dataatt->userid][$arr_days[$i]]=='P') $P++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='A') $A++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='OFF') $OFF++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='L') $L++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='S') $S++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='C') $C++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='DL') $DL++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='PDK') $PDK++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='TB') $TB++;
			} else {
				$tampilanReport .= '<td align="center" class="A">A</td>';
				$A++;
			}
		}
		$tampilanReport .= '<td align="center">'.$P.'</th>
							<td align="center">'.$A.'</th>
							<td align="center">'.$OFF.'</th>
							<td align="center">'.$L.'</th>
							<td align="center">'.$S.'</th>
							<td align="center">'.$C.'</th>
							<td align="center">'.$DL.'</th>
							<td align="center">'.$PDK.'</th>
							<td align="center">'.$TB.'</th>
							</tr>';			
		$k++;				
	}		
	echo $tampilanReport."</table>";
	
?>
<br><br>KETERANGAN : <br><br>
<table width="500" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50" align="center" class="P">P</td>
    <td width="450">HADIR / PRESENT</td>
  </tr>
  <tr>
    <td width="50" align="center" class="A">A</td>
    <td width="450">ALPA / ABSENT</td>
  </tr>
  <tr>
    <td width="50" align="center" class="OFF">OFF</td>
    <td width="450">BUKAN HARI KERJA</td>
  </tr>
  <tr>
    <td width="50" align="center" class="L">L</td>
    <td width="450">LIBUR NASIONAL / LIBUR BERSAMA</td>
  </tr>
  <tr>
    <td width="50" align="center" class="S">S</td>
    <td width="450">SAKIT</td>
  </tr>
  <tr>
    <td width="50" align="center" class="C">C</td>
    <td width="450">CUTI</td>
  </tr>
  <tr>
    <td width="50" align="center" class="DL">DL</td>
    <td width="450">DINAS LUAR</td>
  </tr>
  <tr>
    <td width="50" align="center" class="PDK">PDK</td>
    <td width="450">DIKLAT</td>
  </tr>
  <tr>
    <td width="50" align="center" class="TB">TB</td>
    <td width="450">TUGAS BELAJAR</td>
  </tr>
</table>