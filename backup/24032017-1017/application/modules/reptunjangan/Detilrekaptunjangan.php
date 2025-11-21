<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php //if($index==0) { 
		$clss=''; ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/resources/css/print.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Rekapitulasi Tunjangan Kinerja</title>
</head>

<body>

<style> .phone{ mso-number-format:\@; } </style>
<?php
if($cominfo['companyname']!='') {
?>
<table class="head">
	<tr>
    	<td width="180" rowspan="4" align="center" style="border:0px"><?php if($cominfo['logo']!='') { ?><img src="<?php echo base_url().$cominfo['logo']; ?>" width="50" /><?php $clss = 'align="left"'; } else { $clss = 'align="center"'; } ?></td>
    	<td width="10" rowspan="4" style="border:0px">&nbsp;</td>
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
</table>
<hr /><?php } ?>
<h1><center>
    <?php echo "LAPORAN REKAPITULASI TUNJANGAN KINERJA"; ?>
</center></h1><br>
<center><div style='font-family:arial;font-size:11px'>PERIODE : <?=$dateinfo;?></div></center><br>
<?php //}?>
<table class="head">
	<tr>
    	<td width="150" style="border:0px"><?php echo $this->lang->line('menu_main_organization'); ?></td>
        <td style="border:0px">: <?php echo $data ?></td>
        <td width="150" style="border:0px"></td>
        <td style="border:0px"></td>
        <td style="border:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <thead>
  <tr>
    <th>No</th>
    <th>User ID</th>
    <th>NIP</th>
    <th>Nama</th>
	<th>GOL</th>
    <th>Kelas Jabatan</th>
    <th>Tunjangan Kinerja</th>
    <th>Total Pengurangan</th>
    <th>Total</th>
  </tr>
  </thead>
  <tbody>
  <?php 
	$x=1; $totaltun=0; $tottotaltun=0; $totalan = 0;
	foreach($empinfo as $row) { 
  ?>
  <tr class="<?php if($x % 2 === 0) echo 'even'; else echo 'odd'; ?>">
    <td><?php echo $x ?></td>
    <td class="phone"><?php echo $row['userid']; ?></td>
    <td><?php echo $row['empID']; ?></td>
    <td><?php echo $row['empName']; ?></td>
	<td><?php echo $row['golongan']; ?></td>
    <td><?php echo $row['kelasjabatan']==0?'':$row['kelasjabatan']; ?></td>
    <td align="right"><?php echo number_format(ceil(round($row['tunjangan'],2)),0,'.',','); ?></td>
    <td align="right"><?php echo number_format(ceil(round($row['totaltunjangan'],2)),0,'.',','); ?></td>
    <td align="right"><?php echo number_format(ceil(round($row['tunjangan'],2)) - ceil(round($row['totaltunjangan'],2)),0,'.',','); ?></td>
  </tr>
 <?php 
		$totaltun = $totaltun + ceil(round($row['tunjangan'],2));
		$tottotaltun = $tottotaltun + ceil(round($row['totaltunjangan'],2));
		$x++; } ?>
  <tr>
    <td colspan=6 align="center"><b><?php echo $this->lang->line('total'); ?></b></td>
    <td align="right"><b><?php echo number_format($totaltun,0,'.',','); ?></b></td>
    <td align="right"><b><?php echo number_format($tottotaltun,0,'.',','); ?></b></td>
    <td align="right"><b><?php echo number_format($totaltun - $tottotaltun,0,'.',','); ?></b></td>
  </tr>
</table>
<br><br>
<div style="page-break-after:always"></div>