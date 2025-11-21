<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/resources/css/print.css" />
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
    <th rowspan="2" align="center">'.$this->lang->line('empidrecap').'</th>
    <th rowspan="2" align="center">'.$this->lang->line('empnamerecap').'</th>
    <th rowspan="2" align="center">'.$this->lang->line('workdayrecap').'</th>
	<th rowspan="2" align="center">'.$this->lang->line('nonworkdayrecap').'</th>	    
    <th colspan="2"  align="center">'.$this->lang->line('att').'</th> 
    <th rowspan="2" align="center">'.$this->lang->line('tabsstatusrecap').'</th>
    <th colspan="'.$atco.'" align="center">'.$this->lang->line('menu_main_attstatus').'</th>
    <th rowspan="2" align="center">'.$this->lang->line('totalattrecap').'</th>
    <th colspan="'.$abco.'" align="center">'.$this->lang->line('menu_main_absstatus').'</th>
    <th rowspan="2" align="center">'.$this->lang->line('totalabsrecap').'</th>
    <th rowspan="2" align="center">'.$this->lang->line('absent').'</th>
    <th rowspan="2" align="center">'.$this->lang->line('all_late').'</th>
    <th rowspan="2" align="center">'.$this->lang->line('all_early').'</th>
    <th rowspan="2" align="center">'.$this->lang->line('all_legendot').'</th>
  </tr>
  <tr>
    <th align="center">'.$this->lang->line('workdrecap').'</th> 
    <th align="center">'.$this->lang->line('nonworkdrecap').'</th>';
    
		foreach($att as $atte) {
			$headcol .='<th align="center">'.$atte['atid'].'</th>';
		}
		
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
    <td><?php echo $row['badgeNumber']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td align="center"><?php echo $row['workingday']; ?></td>
    <td align="center"><?php echo $row['off']; ?></td>
    <td align="center"><?php echo $row['workday']; ?></td>
    <td align="center"><?php echo $row['workinholiday']; ?></td>
    <td align="center"><?php echo $row['totalabsent']; ?></td>
    
    <?php 
		foreach($att as $atte) {			
			if($row['aten'][$row['userid']][$atte['atid']]!=0) {
				echo '<td align="center">'.$row['aten'][$row['userid']][$atte['atid']].'</td>';
			} else {
				echo '<td align="center">-</td>';
			}
		}
	?>    
    <td align="center"><?php echo $row['attendance']; ?></td>
    <?php 
		foreach($abs as $abse) {
			if($row['aben'][$row['userid']][$abse['abid']]!=0) {
				echo '<td align="center">'.$row['aben'][$row['userid']][$abse['abid']].'</td>';
			} else {
				echo '<td align="center">-</td>';
			}
		}
	?>
    <td align="center"><?php echo $row['absence']; ?></td>
    <td align="center"><?php echo $row['absent']; ?></td>
    <td align="center"><?php echo $row['late']; ?></td>
    <td align="center"><?php echo $row['early']; ?></td>
    <td align="center"><?php echo $row['OT']; ?></td>
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
</tbody></table><br>
<?php
    }    
}
    //END LOOPING GROUP REPORT
?>
<table class="head">
	<tr>
    	<td width="500px" style="border:0px"><?php echo $this->lang->line('statusrecap'); ?> :</td>        
    </tr>
    <tr>
    	<td width="500px" style="border:0px">
            <?php echo $this->lang->line('att'); ?> :
        </td>        
    </tr>
    <tr>
    	<td width="500px" style="border:0px">
            <?php 
		      foreach($att as $atte) {
	        ?>
            <div style="float: left; padding: 15px;"><?php echo $atte['atid']; ?>&nbsp;&nbsp;:<?php echo $atte['atname']; ?></div>
            <?php
                }
            ?>
        </td>        
    </tr>
    <tr>
    	<td width="500px" style="border:0px">
            <?php echo $this->lang->line('abs'); ?> :
        </td>        
    </tr>
    <tr>
    	<td width="500px" style="border:0px">
            <?php 
		      foreach($abs as $abe) {
	        ?>
            <div style="float: left; padding: 15px;"><?php echo $abe['abid']; ?>&nbsp;&nbsp;:<?php echo $abe['abname']; ?></div>
            <?php
                }
            ?>
        </td>        
    </tr>
    <tr>
        <td width="500px" style="border:0px">
            <?php echo $this->lang->line('footnoterecap'); ?>
        </td>
    </tr>
</table>
</tbody>
<tfoot></tfoot>