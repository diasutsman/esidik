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

<table class="head" width="100%">
    <tr>
        <td align="center" style="border:0px"><h1><?php echo $cominfo["companyname"] ?></h1></td>
    </tr>
    <tr>
        <td align="center" style="border:0px"><?php echo $cominfo["address1"] ?></td>
    </tr>
    <tr>
        <td align="center" style="border:0px">Telepon: <?php echo $cominfo["phone"] ?>, Faks: <?php echo $cominfo["fax"] ?></td>
    </tr>
</table>
<hr/>
<h1>
    <center>
        LAPORAN KEHADIRAN
    </center>
</h1>
<br>
<table class="head">
    <tr>
        <td width="120" style="border:0px">NIP Pegawai</td>
        <td style="border:0px">: <?php echo $empinfo["empID"] ?></td>
        <td width="120" style="border:0px">Jabatan</td>
        <td style="border:0px">: <?php echo $empinfo["empTitle"] ?></td>
    </tr>
    <tr>
        <td width="120" style="border:0px">Nama</td>
        <td style="border:0px">: <?php echo $empinfo["empName"] ?></td>
        <td width="120" style="border:0px">Unit Kerja</td>
        <td style="border:0px">: <?php echo $empinfo["deptName"] ?></td>
    </tr>
</table>
<br>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th>Hari</th>
        <th>Tanggal</th>
        <th>Jam Kerja</th>
        <th>Aktifitas</th>
        <th>Tanggal Masuk</th>
        <th>Jam Masuk</th>
        <th>Keluar Istirahat</th>
        <th>Masuk Istirahat</th>
        <th>Tanggal Keluar</th>
        <th>Jam Keluar</th>
        <th>Terlambat</th>
        <th>Pulang Awal</th>
        <th>Lembur Sebelum</th>
        <th>Lembur Setelah</th>
        <th>Total Jam</th>
        <th>Catatan</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $x = 1;
    foreach ($data as $row) {
        ?>
        <tr>
            <td class="text"><?php echo hariToInd($row["day"]); ?></td>
            <td class="text"><?php echo $row["date"]; ?></td>
            <td class="text"><?php echo $row["workinghour"]; ?></td>
            <td class="text"><?php echo $row["activity"]; ?></td>
            <td class="text"><?php echo $row["datein"]; ?></td>
            <td class="text"><?php echo $row["dutyon"]; ?></td>
            <td class="text"><?php echo $row["breakout"]; ?></td>
            <td class="text"><?php echo $row["breakin"]; ?></td>
            <td class="text"><?php echo $row["dateout"]; ?></td>
            <td class="text"><?php echo $row["dutyoff"]; ?></td>
            <td class="text"><?php echo $row["latein"]; ?></td>
            <td class="text"><?php echo $row["earlydept"]; ?></td>
            <td class="text"><?php echo $row["otbef"]; ?></td>
            <td class="text"><?php echo $row["otaf"]; ?></td>
            <td class="num"><?php echo $row["totalhour"]; ?></td>
            <td class="text"> <?php echo $row["notes"]; ?></td>
        </tr>
    <?php
    }

    ?>
    <tr>
        <td colspan=10 align="right">Total :</td>
        <td><?php echo $footah["totallate"]; ?></td>
        <td><?php echo $footah["totalearly"]; ?></td>
        <td><?php echo $footah["totalotbef"]; ?></td>
        <td><?php echo $footah["totalotaf"]; ?></td>
        <td><?php echo $footah["total"]; ?></td>
        <td>&nbsp;</td>
    </tr>
</table>
<?php if ($pdfid == 1 ) {?>
    <pagebreak />
<?php }?>
<?php if ($excelid == 0 ) {?>
    <div style="page-break-after:always"></div><br><br>
    </body>
    </html>
<?php }?>