<?php if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <style>
        <?php include FCPATH."/assets/css/print.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Kehadiran</title>
</head>

<body><?php } ?>
<style> .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
</style>
<center>
<h1><?php echo $cominfo["companyname"] ?></h1>
<?php echo $cominfo["address1"] ?>
    <br>Telepon: <?php echo $cominfo["phone"] ?>, Faks: <?php echo $cominfo["fax"] ?>
    <hr/>
<h1>
        LAPORAN KEHADIRAN
</h1>
    </center>
<br>
<table class="head">
    <tr>
        <td width="120" style="border:0px" class="text">Hari</td>
        <td style="border:0px" class="text">: <?php echo hariToInd($empinfo["day"])?></td>
    </tr>
    <tr>
        <td width="120" style="border:0px" class="text">Tanggal</td>
        <td style="border:0px" class="text">: <?php echo format_date_ind($empinfo["date"])?></td>
    </tr>
</table>
<br>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th class="text">NIP</th>
        <th class="text">Nama</th>
        <th class="text">Unit Kerja</th>
        <th class="text">Jam Kerja</th>
        <th class="text">Aktifitas</th>
        <th class="text">Jam Masuk</th>
        <!--<th class="text">Keluar Istirahat</th>
        <th class="text">Masuk Istirahat</th>-->
        <th class="text">Jam Keluar</th>
        <th class="text">Terlambat</th>
        <th class="text">Pulang Awal</th>
        <!--<th class="text">Lembur Sebelum</th>-->
        <th class="text">Lembur Setelah</th>
        <th class="text">Total Jam</th>
        <th class="text">Catatan</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $x = 1;
    foreach ($data as $row) {
    ?>
    <tr >
        <td class="text"><?php echo "&nbsp;".$row["userid"];?></td>
        <td class="text"><?php echo $row["name"];?></td>
        <td class="text"><?php echo $row["dept"];?></td>
        <td class="text"><?php echo $row["workinghour"];?></td>
        <td class="text"><?php echo $row["activity"];?></td>
        <td class="text"><?php echo $row["dutyon"];?></td>
        <!--<td class="text"><?php /*echo $row["breakout"];*/?></td>
        <td class="text"><?php /*echo $row["breakin"];*/?></td>-->
        <td class="text"><?php echo $row["dutyoff"];?></td>
        <td class="text"><?php echo $row["latein"];?></td>
        <td class="text"><?php echo $row["earlydept"];?></td>
        <!--<td class="text"><?php /*echo $row["otbef"];*/?></td>-->
        <td class="text"><?php echo $row["otaf"];?></td>
        <td class="text"><?php echo $row["totalhour"];?></td>
        <td class="text"><?php echo $row["notes"];?></td>
    </tr>
    <?php }

    ?>
    <!--<tr>
        <td colspan=9 align="right">Total :</td>
        <td class="text">00:00</td>
        <td class="text">00:00</td>
        <td class="text">00:00</td>
        <td class="text">00:00</td>
        <td class="text">00:00</td>
        <td>&nbsp;</td>
    </tr>-->
</table>
<?php if ($pdfid == 1 ) {?>
    <pagebreak />
<?php }?>
<?php if ($excelid == 0 ) {?>
    <div style="page-break-after:always"></div><br><br>
    </body>
    </html>
<?php }?>