<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php //if($index==0) { 
$clss = ''; ?>
<?php if ($excelid == 0) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <style>
        <?php include FCPATH."/assets/css/print.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Selisih Tunjangan Kinerja</title>
</head>

<body>
<?php } ?>
    <style> .num {
        mso-number-format: General;
    }

    .text {
        mso-number-format: "\@"; /*force text*/
    }

    .ttd {
        border-style: none;
    }
</style>
<center>
    <h1><?php echo $cominfo["companyname"]; ?></h1>
    <p align="center"><?php echo $cominfo["address1"]; ?><br></p>
    <p align="center">Telepon: <?php echo $cominfo["phone"]; ?>, Faks: <?php echo $cominfo["fax"]; ?></p>
    <hr/>
    <h1>
        <?php echo "LAPORAN SELISIH TUNJANGAN KINERJA<br/>BERDASARKAN DISIPLIN KERJA"; ?>
        <div>PERIODE : <?php echo $dateinfo; ?></div>
    </h1>
</center>
<br>
<br>
<table class="head">
    <tr>
        <td width="150" style="border:0px" class="text">Unit Kerja</td>
        <td style="border:0px" class="text">: <?php echo $data ?></td>
        <td width="150" style="border:0px"></td>
        <td style="border:0px"></td>
        <td style="border:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>GOL</th>
        <th>Kelas Jabatan</th>
        <th>Tunjangan Kinerja Lama</th>
        <th>Tunjangan Kinerja Baru</th>
        <th>Total Pengurangan Lama</th>
        <th>Total Pengurangan Baru</th>
        <th>Total Lama</th>
        <th>Total Baru</th>
        <th>Selisih</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $x = 1;
    $totaltun = 0;
    $totaltunbaru = 0;
    $tottotaltun = 0;
    $tottotaltunbaru = 0;
    $totalan = 0;
    $totalan1 = 0;
    $totalan2 = 0;
    $totalan3 = 0;
    foreach ($empinfo as $row) {

        $ntunjangan =ceil(round($row['tunjangan'], 2));
        $tunjangan = $showpdf == 1 ? number_format($ntunjangan, 2, ',', '.') : $excelid == 1 ? $ntunjangan : number_format($ntunjangan, 2, ',', '.');
        $ntunjanganbaru=ceil(round($row['tunjanganbaru'], 2));
        $tunjanganbaru = $showpdf == 1 ? number_format($ntunjanganbaru, 2, ',', '.') : $excelid == 1 ? $ntunjanganbaru : number_format($ntunjanganbaru, 2, ',', '.');
        $ntotaltunjangan = ceil(round($row['totaltunjangan'], 2));
        $totaltunjangan = $showpdf == 1 ? number_format($ntotaltunjangan, 2, ',', '.') : $excelid == 1 ? $ntotaltunjangan : number_format($ntotaltunjangan, 2, ',', '.');
        $ntotaltunjanganbaru = ceil(round($row['totaltunjanganbaru'], 2));
        $totaltunjanganbaru = $showpdf == 1 ? number_format($ntotaltunjanganbaru, 2, ',', '.') : $excelid == 1 ? $ntotaltunjanganbaru : number_format($ntotaltunjanganbaru, 2, ',', '.');

        $ntunjangan1 = ($ntunjangan - $ntotaltunjangan)<=0 ? 0 :($ntunjangan - $ntotaltunjangan);
        $tunjangan1 = $showpdf == 1 ? number_format($ntunjangan1, 2, ',', '.') : $excelid == 1 ? $ntunjangan1 : number_format($ntunjangan1, 2, ',', '.');
        $ntunjangan2 = ($ntunjanganbaru -$ntotaltunjanganbaru)<=0 ? 0 : ($ntunjanganbaru -$ntotaltunjanganbaru);
        $tunjangan2 = $showpdf == 1 ? number_format($ntunjangan2, 2, ',', '.') : $excelid == 1 ? $ntunjangan2 : number_format($ntunjangan2, 2, ',', '.');
        $ntunjangan3 = ($ntunjangan2 - $ntunjangan1) <= 0 ? 0 : ($ntunjangan2 - $ntunjangan1);
        $tunjangan3 = $showpdf == 1 ? number_format($ntunjangan3, 2, ',', '.') : $excelid == 1 ? $ntunjangan3 : number_format($ntunjangan3, 2, ',', '.');

        ?>
        <tr class="<?php if ($x % 2 === 0) echo 'even'; else echo 'odd'; ?>">
            <td valign="top"><?php echo $x ?></td>
            <td valign="top" class="text"><?php echo $row['empID']; ?></td>
            <td valign="top"><?php echo $row['empName']; ?></td>
            <td valign="top" align="center"  ><?php echo $row['golongan']; ?></td>
            <td valign="top" align="center"  ><?php echo $row['kelasjabatan'] == 0 ? '' : $row['kelasjabatan']; ?></td>
            <td valign="top" align="right"  class="num"><?php echo $tunjangan ?></td>
            <td valign="top" align="right"  class="num"><?php echo $tunjanganbaru; ?></td>
            <td valign="top" align="right"  class="num"><?php echo $totaltunjangan; ?></td>
            <td valign="top" align="right"  class="num"><?php echo $totaltunjanganbaru; ?></td>
            <td valign="top" align="right"  class="num"><?php echo $tunjangan1 ?></td>
            <td valign="top" align="right"  class="num"><?php echo $tunjangan2; ?></td>
            <td valign="top" align="right"  class="num"><?php echo $tunjangan3; ?></td>
        </tr>
        <?php
        $totaltun = $totaltun + $ntunjangan;
        $totaltunbaru = $totaltunbaru + $ntunjanganbaru;
        $tottotaltun = $tottotaltun + $ntotaltunjangan;
        $tottotaltunbaru = $tottotaltunbaru + $ntotaltunjanganbaru;

        $totalan1 = $totalan1 + $ntunjangan1;
        $totalan2 = $totalan2 + $ntunjangan2;
        $totalan3 = $totalan3 + $ntunjangan3;

        $x++;
    }

    $ctotaltun = $showpdf == 1 ? number_format($totaltun, 2, ',', '.') : $excelid == 1 ? $totaltun : number_format($totaltun, 2, ',', '.');
    $ctotaltunbaru = $showpdf == 1 ? number_format($totaltunbaru, 2, ',', '.') : $excelid == 1 ? $totaltunbaru : number_format($totaltunbaru, 2, ',', '.');
    $ctottotaltun = $showpdf == 1 ? number_format($tottotaltun, 2, ',', '.') : $excelid == 1 ? $tottotaltun : number_format($tottotaltun, 2, ',', '.');
    $ctottotaltunbaru = $showpdf == 1 ? number_format($tottotaltunbaru, 2, ',', '.') : $excelid == 1 ? $tottotaltunbaru : number_format($tottotaltunbaru, 2, ',', '.');
    $ntotaltun = ($totaltun - $tottotaltun)<=0 ? 0 : ($totaltun - $tottotaltun);
    $ctotal1 = $showpdf == 1 ? number_format($ntotaltun, 2, ',', '.') : $excelid == 1 ? $ntotaltun : number_format($ntotaltun, 2, ',', '.');
    $ntotaltun = ($totaltunbaru - $tottotaltunbaru) <=0 ? 0 : ($totaltunbaru - $tottotaltunbaru);
    $ctotal2 = $showpdf == 1 ? number_format($ntotaltun, 2, ',', '.') : $excelid == 1 ? $ntotaltun : number_format($ntotaltun, 2, ',', '.');
    $ntotaltun = ($totaltunbaru - $tottotaltunbaru) - ($totaltun - $tottotaltun);
	$ntotaltun = $ntotaltun<=0? 0 : $ntotaltun;
    $ctotal3 = $showpdf == 1 ? number_format($ntotaltun, 2, ',', '.') : $excelid == 1 ? $ntotaltun : number_format($ntotaltun, 2, ',', '.');

    $ctotal1 = $showpdf == 1 ? number_format($totalan1, 2, ',', '.') : $excelid == 1 ? $totalan1 : number_format($totalan1, 2, ',', '.');
    $ctotal1 = $showpdf == 1 ? number_format($totalan2, 2, ',', '.') : $excelid == 1 ? $totalan2 : number_format($totalan2, 2, ',', '.');
    $ctotal1 = $showpdf == 1 ? number_format($totalan3, 2, ',', '.') : $excelid == 1 ? $totalan3 : number_format($totalan3, 2, ',', '.');
    ?>
    <tr>
        <td colspan=5 align="center"><b>TOTAL</b></td>
        <td align="right"  class="num"><b><?php echo $ctotaltun?></b></td>
        <td align="right"  class="num"><b><?php echo $ctotaltunbaru ?></b></td>
        <td align="right"  class="num"><b><?php echo $ctottotaltun?></b></td>
        <td align="right"  class="num"><b><?php echo $ctottotaltunbaru?></b></td>
        <td align="right" class="num"><b><?php echo $ctotal1; ?></b></td>
        <td align="right" class="num"><b><?php echo $ctotal2; ?></b></td>
        <td align="right" class="num"><b><?php echo $ctotal3; ?></b>
        </td>
    </tr>
</table>
<?php if ($pdfid == 1) { ?>
    <pagebreak/>
<?php } ?>
<?php if ($excelid == 0) { ?>
    <br><br>
    <div style="page-break-after:always"></div>
    </body>
    </html>
<?php }
?>