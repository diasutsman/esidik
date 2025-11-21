<?php if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <style>
        <?php include FCPATH."/assets/css/print.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>LAPORAN KEHADIRAN PELAKSANAAN UPACARA</title>
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
        LAPORAN KEHADIRAN PELAKSANAAN UPACARA
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
        <th class="text" rowSpan="2">NIP</th>
        <th class="text"  rowSpan="2">Nama</th>
        <th class="text"  rowSpan="2">Unit Kerja</th>
        <th class="text" colSpan="2">Waktu Upacara</th>
        <th class="text"  rowSpan="2">Absensi</th>
        <th class="text"  rowSpan="2">Catatan</th>
    </tr>
    <tr>
        <th class="text">Tanggal</th>
        <th class="text">Jam</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $x = 1;
    foreach ($data as $row) {
    ?>
    <tr >
        <td class="text" valign="top"><?php echo "&nbsp;".$row["userid"];?></td>
        <td class="text" valign="top"><?php echo $row["name"];?></td>
        <td class="text" valign="top"><?php echo $row["dept"];?></td>
        <td class="text" valign="top"><?php echo $row["workingdate"];?></td>
        <td class="text" valign="top"><?php echo $row["workinghour1"].' s/d '.$row["workinghour2"];?></td>
        <td class="text" valign="top"><?php echo $row["dutyon"];?></td>
        <td class="text" valign="top"><?php echo $row["notes"];?></td>
    </tr>
    <?php }
    ?>
    
</table>
<?php if ($pdfid == 1 ) {?>
    <pagebreak />
<?php }?>
<?php if ($excelid == 0 ) {?>
    <div style="page-break-after:always"></div><br><br>
    </body>
    </html>
<?php }?>