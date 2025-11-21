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
    foreach ($empinfo as $row) {
        ?>
        <tr class="<?php if ($x % 2 === 0) echo 'even'; else echo 'odd'; ?>">
            <td><?php echo $x ?></td>
            <td class="text"><?php echo $row['empID']; ?></td>
            <td><?php echo $row['empName']; ?></td>
            <td><?php echo $row['golongan']; ?></td>
            <td><?php echo $row['kelasjabatan'] == 0 ? '' : $row['kelasjabatan']; ?></td>
            <td align="right"  class="num"><?php echo number_format(ceil(round($row['tunjangan'], 2)), 0, ',', '.'); ?></td>
            <td align="right"  class="num"><?php echo number_format(ceil(round($row['tunjanganbaru'], 2)), 0, ',', '.'); ?></td>
            <td align="right"  class="num"><?php echo number_format(ceil(round($row['totaltunjangan'], 2)), 0, ',', '.'); ?></td>
            <td align="right"  class="num"><?php echo number_format(ceil(round($row['totaltunjanganbaru'], 2)), 0, ',', '.'); ?></td>
            <td align="right"  class="num"><?php echo number_format(ceil(round($row['tunjangan'], 2)) - ceil(round($row['totaltunjangan'], 2)), 0, ',', '.'); ?></td>
            <td align="right"  class="num"><?php echo number_format(ceil(round($row['tunjanganbaru'], 2)) - ceil(round($row['totaltunjanganbaru'], 2)), 0, ',', '.'); ?></td>
            <td align="right"  class="num"><?php echo number_format((ceil(round($row['tunjanganbaru'], 2)) - ceil(round($row['totaltunjanganbaru'], 2))) - (ceil(round($row['tunjangan'], 2)) - ceil(round($row['totaltunjangan'], 2))), 0, ',', '.'); ?></td>
        </tr>
        <?php
        $totaltun = $totaltun + ceil(round($row['tunjangan'], 2));
        $totaltunbaru = $totaltunbaru + ceil(round($row['tunjanganbaru'], 2));
        $tottotaltun = $tottotaltun + ceil(round($row['totaltunjangan'], 2));
        $tottotaltunbaru = $tottotaltunbaru + ceil(round($row['totaltunjanganbaru'], 2));
        $x++;
    } ?>
    <tr>
        <td colspan=5 align="center"><b>TOTAL</b></td>
        <td align="right"  class="num"><b><?php echo number_format($totaltun, 0, ',', '.'); ?></b></td>
        <td align="right"  class="num"><b><?php echo number_format($totaltunbaru, 0, ',', '.'); ?></b></td>
        <td align="right"  class="num"><b><?php echo number_format($tottotaltun, 0, ',', '.'); ?></b></td>
        <td align="right"  class="num"><b><?php echo number_format($tottotaltunbaru, 0, ',', '.'); ?></b></td>
        <td align="right" class="num"><b><?php echo number_format($totaltun - $tottotaltun, 0, ',', '.'); ?></b></td>
        <td align="right" class="num"><b><?php echo number_format($totaltunbaru - $tottotaltunbaru, 0, ',', '.'); ?></b></td>
        <td align="right" class="num">
            <b><?php echo number_format(($totaltunbaru - $tottotaltunbaru) - ($totaltun - $tottotaltun), 0, ',', '.'); ?></b>
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