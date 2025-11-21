<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php //if($index==0) { 
		$clss=''; ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/resources/css/print.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Tunjangan Kinerja</title>
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
    <?php echo "LAPORAN TUNJANGAN KINERJA"; ?>
</center></h1><br>
<center><div style='font-family:arial;font-size:11px'>PERIODE : <?=$dateinfo;?></div></center><br>
<?php //}?>
<table class="head">
	<tr>
    	<td width="150" style="border:0px"><?php echo $this->lang->line('userid'); ?></td>
        <td style="border:0px">: <?php echo $empinfo['userid']; ?></td>
        <td width="150" style="border:0px"><?php echo $this->lang->line('menu_main_organization'); ?></td>
        <td style="border:0px">: <?php echo $empinfo['deptName']; ?></td>
        <td style="border:0px">&nbsp;</td>
    </tr>
	<tr>
    	<td width="150" style="border:0px"><?php echo $this->lang->line('empid'); ?></td>
        <td style="border:0px">: <?php echo $empinfo['empID']; ?></td>
        <td width="150" style="border:0px"><?php echo $this->lang->line('emptitle'); ?></td>
        <td style="border:0px">: <?php echo $empinfo['empTitle']; ?></td>
		<td style="border:0px">&nbsp;</td>
    </tr>
    <tr>
    	<td width="150" style="border:0px"><?php echo $this->lang->line('name'); ?></td>
        <td style="border:0px">: <?php echo $empinfo['empName']; ?></td>
        <td width="150" style="border:0px">Kelas Jabatan</td>
        <td style="border:0px">: <?php echo $empinfo['kelasjabatan']==0?'':$empinfo['kelasjabatan']; ?></td>
        <td style="border:0px">&nbsp;</td>
    </tr>
	 <tr>
    	<td width="150" style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
        <td width="150" style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
    </tr>
	<tr>
    	<td width="150" style="border:0px"><b>Tunjangan Kinerja</b></td>
        <td style="border:0px"><b>: Rp. </b></td>
        <td width="150" style="border:0px" align="right"><b><?php echo $excelid==1?ceil(round($empinfo['tunjangan'],2)):number_format(ceil(round($empinfo['tunjangan'],2)),0,'.',','); ?></b></td>
        <td style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
    </tr>
	<tr style="border-bottom:2px solid">
    	<td width="150" style="border:0px"><b>Total Pengurangan</b></td>
        <td style="border:0px"><b>: Rp. </b></td>
        <td width="150" style="border:0px" align="right"><b><?php echo $excelid==1?ceil(round($footah['total'],2)):number_format(ceil(round($footah['total'],2)),0,'.',','); ?></b></td>
        <td style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
    </tr>
	<tr>
    	<td width="150" style="border:0px"><b>Total Tunjangan Kinerja</b></td>
        <td style="border:0px"><b>: Rp. </b></td>
        <td width="150" style="border:0px" align="right"><b><?php echo $excelid==1?(ceil(round($empinfo['tunjangan'],2)) - ceil(round($footah['total'],2))):number_format(ceil(round($empinfo['tunjangan'],2)) - ceil(round($footah['total'],2)),0,'.',','); ?></b></td>
        <td style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
    </tr>
	<tr>
    	<td width="150" style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
        <td width="150" style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
    </tr>
	<tr>
    	<td width="150" style="border:0px">Detail</td>
        <td style="border:0px">: &nbsp;</td>
        <td width="150" style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
    </tr>
</table>
<?php 
	if($empinfo['tunjangan']==0) {
		echo '<table class="head">
				<tr>
					<td style="border:0px"><span style="color:red">Tidak Mendapatkan Tunjangan Kinerja</span></td>
				</tr>
			</table>';
	} else {
?>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <thead>
  <tr>
    <th>Hari</th>
    <th>Tanggal</th>
    <th>Status</th>
    <th>Nilai</th>
    <th>Pengurangan</th>
    <th>Total</th>
  </tr>
  </thead>
  <tbody>
  <?php 
	$x=1;
	foreach($data as $row) { 
  ?>
  <tr class="<?php if($x % 2 === 0) echo 'even'; else echo 'odd'; ?>">
    <td><?php echo $this->lang->line($row['day']); ?></td>
    <td><?php echo $row['date']; ?></td>
    <td><?php echo $row['status']; ?></td>
    <td align="right"><?php echo $row['nilai']; ?></td>
    <td align="right"><?php echo $row['pengurangan']; ?></td>
    <td align="right">
		<?php 
			echo $excelid==1?ceil(round($row['total'],2)):number_format(ceil(round($row['total'],2)),0,'.',','); 
		?>
	</td>
  </tr>
  <?php $x++; } ?>
  <tr>
    <td colspan=4 align="center"><b><?php echo $this->lang->line('total'); ?></b></td>
    <td align="right"><b><?php echo $footah['totalpersen'] ?></b></td>
    <td align="right"><b><?php echo $excelid==1?ceil(round($footah['total'],2)):number_format(ceil(round($footah['total'],2)),0,'.',','); ?></b></td>
  </tr>
</table>
<?php } ?>
<br><br>
<div style="page-break-after:always"></div>