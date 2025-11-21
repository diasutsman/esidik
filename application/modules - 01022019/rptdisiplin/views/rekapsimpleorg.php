<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <style>
        <?php include FCPATH."/assets/css/print.css"; ?>
    </style>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->lang->line('menu_main_recapreport'); ?></title>
</head>
<body>
<?php 

$page=1;
$headerrep = '<table class="head">
	<tr>
    	<td width="180" rowspan="4" align="center" style="border:0px">';
if($cominfo['logo']!='') {
	$headerrep .= '<img src="'.base_url().$cominfo['logo'].'" width="50" />';
	$clss = 'align="left"'; 
} else { 
	$clss = 'align="center"'; 
} 

$headerrep .= '</td>
    	<td width="10" rowspan="4" style="border:0px">&nbsp;</td>
        <td '.$clss.' style="border:0px"><h1>'.$cominfo['companyname'].'</h1></td>
    </tr>
	<tr>
    	<td '.$clss.' style="border:0px">'.$cominfo['address1'].'</td>
    </tr>';

	if ($cominfo['address2']!='') {
		$headerrep .= '
    <tr>
    	<td <?php '.$clss.' style="border:0px">'.$cominfo['address2'].'</td>
    </tr>';
	}
	$headerrep .= '
    <tr>
      <td '.$clss.' style="border:0px">'.$this->lang->line('phone').': '.$cominfo['phone'].', '.$this->lang->line('fax').': '.$cominfo['fax'].'</td>
	  <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
	  <td width="180" rowspan="4" align="center" style="border:0px">&nbsp;</td>
    </tr>
</table>
<hr />';

$atco = count($att); $abco = count($abs); 
$headcol =' <thead><tr>
     <th rowspan="2" align="center">No</th>
    <th rowspan="2" align="center">'.$this->lang->line('empidrecap').'</th>
    <th rowspan="2" align="center">'.$this->lang->line('empnamerecap').'</th>    
    <th rowspan="2"  align="center">Kehadiran</th> 	
    <th rowspan="2" align="center">Tidak Hadir</th>
    <th colspan="'.$abco.'" align="center">Status Kerja</th>
  </tr>
  <tr>';
    
		foreach($abs as $abse) {
			$headcol .='<th align="center">'.$abse['abid'].'</th>';
		}

$headcol .=' </tr></thead>';
$clss='';
	if($cominfo['companyname']!='') { 
echo $headerrep;
	} ?> 
<tbody>
<h1><center>
    <?php echo strtoupper($this->lang->line('menu_main_recapreport')); ?>
</center></h1>
<?php
    //START LOOPING ORGANIZATION REPORT
    $tempcnt = count($companyLooping);
$height = 0;
for($var_org=0;$var_org<$tempcnt;$var_org++){
    $ar = 0;
    foreach($data as $row1){
        if($row1['DeptID'] == $companyLooping[$var_org]['name']){
			$ar++;
        }
    }
    if($ar > 0){
		$height += 80;
?>
<br>
<table class="head">
	<tr>
    	<td width="120" style="border:0px"><?php echo $this->lang->line('menu_main_organization'); ?></td>
        <td style="border:0px">: <?php /*echo $empinfo['dept'];*/ echo $companyLooping[$var_org]['name']?></td>
        <td width="120" style="border:0px"><?php echo $this->lang->line('totholiday'); ?></td>
        <td style="border:0px">: <?php echo $empinfo['holidays']; ?></td>
        <td width="40%" style="border:0px">&nbsp;</td>
    </tr>
	<tr>
    	<td width="120" style="border:0px"><?php echo $this->lang->line('periode'); ?></td>
        <td style="border:0px">: <?php echo $empinfo['datestart'].' - '.$empinfo['datestop']; ?></td>
        <td width="120" style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
        <td width="40%" style="border:0px">&nbsp;</td>
    </tr>
</table><br>


<table width="100%" border="1" cellspacing="0" cellpadding="0">  
  <?php 
	echo $headcol."<tbody";
	$x=1;
foreach($data as $row) { 
	   if($row['DeptID'] == $companyLooping[$var_org]['name']){ 
		 	$height += 10;
    //echo $height.'=='; 
  ?>
  <tr class="<?php if($x % 2 === 0) echo 'even'; else echo 'odd'; ?>">
    <td><?php echo $x; ?></td>
    <td><?php echo $row['badgeNumber']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td align="center"><?php echo $row['workday']; ?></td>
    <td align="center"><?php echo $row['absent']; ?></td>
    <?php 
		foreach($abs as $abse) {
			if($row['aben'][$row['userid']][$abse['abid']]!=0) {
				echo '<td align="center">'.$row['aben'][$row['userid']][$abse['abid']].'</td>';
			} else {
				echo '<td align="center">-</td>';
			}
		}
	?>
  </tr>
  <?php
	//echo $height;
	//echo $x;
	/*if($page==1){
		if($height%500==0 ){
			echo "</table><br  style='page-break-before: always;'>";
			echo "<table width='100%' border='1' cellspacing='0' cellpadding='0'>";
			echo $headcol;
			$page++;
		} 
	}else{
		if($height%550==0 ){
			echo "</table><br  style='page-break-before: always;'>";		
			echo "<table width='100%' border='1' cellspacing='0' cellpadding='0'>";
			echo $headcol;
			$page++;
		} 		
	}*/
	
	$x++; } 

	} ?>
</tbody></table><br><br>
<?php
    }    
}
    //END LOOPING GROUP REPORT
?>
<table class="head">
	<tr>
    	<td width="50px" style="border:0px">Kepala Subbagian Tatalaksana <br>dan Kepegawaian,</td>        
    </tr>
	<tr><td width="50px" style="border:0px"></td></tr>
	<tr><td width="50px" style="border:0px"></td></tr>
	<tr><td width="50px" style="border:0px"></td></tr>
	<tr><td width="50px" style="border:0px"></td></tr>
	<tr><td width="50px" style="border:0px"></td></tr>
	<tr><td width="50px" style="border:0px"></td></tr>
	<tr>
    	<td width="50px" style="border:0px">Suharyoko, S.H.</td>        
    </tr>
</table>
</tbody>
<tfoot></tfoot>