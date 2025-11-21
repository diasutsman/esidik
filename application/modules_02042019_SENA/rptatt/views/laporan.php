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
        <td width="120" style="border:0px" class="text " >NIP Pegawai</td>
        <td style="border:0px" class="text">: <?php echo $empinfo["empID"] ?></td>
        <td width="120" style="border:0px;" class="text" >Jabatan</td>
        <td style="border:0px" class="text">: <?php echo $empinfo["empTitle"] ?></td>
    </tr>
    <tr>
        <td width="120" style="border:0px" class="text txtLeftCenter" >Nama</td>
        <td style="border:0px" class="text txtLeftCenter">: <?php echo $empinfo["empName"] ?></td>
        <td width="120" style="border:0px" class="text txtLeftCenter" >Unit Kerja</td>
        <td style="border:0px" class="text txtLeftCenter">: <?php echo $empinfo["deptName"] ?></td>
    </tr>
</table>
<br>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th class="text">Hari</th>
        <th class="text">Tanggal</th>
        <th class="text">Jam Kerja</th>
        <th class="text">Aktifitas</th>
        <th class="text">Pelaksanaan Upacara</th>
        <th class="text">Tanggal Masuk</th>
        <th class="text">Jam Masuk</th>
        <!--<th class="text">Keluar Istirahat</th>
        <th class="text">Masuk Istirahat</th>-->
        <th class="text">Tanggal Keluar</th>
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
        <tr>
            <td class="text" valign="top"><?php echo hariToInd($row["day"]); ?></td>
            <td class="text" valign="top"><?php echo $row["date"]; ?></td>
            <td class="text" valign="top"><?php echo $row["workinghour"]; ?></td>
            <td class="text" valign="top"><?php echo $row["activity"]; ?></td>
            <td class="text" valign="top" align="center"><?php echo $row["activity2"]; ?></td>
            <td class="text" valign="top"><?php echo $row["datein"]; ?></td>
            <td class="text" valign="top"><?php echo $row["dutyon"]; ?></td>
            <!--<td class="text"><?php /*echo $row["breakout"]; */?></td>
            <td class="text"><?php /*echo $row["breakin"]; */?></td>-->
            <td class="text" valign="top"><?php echo $row["dateout"]; ?></td>
            <td class="text" valign="top"><?php echo $row["dutyoff"]; ?></td>
            <td class="text" valign="top"><?php echo $row["latein"]; ?></td>
            <td class="text" valign="top"><?php echo $row["earlydept"]; ?></td>
            <!--<td class="text"><?php /*echo $row["otbef"]; */?></td>-->
            <td class="text" valign="top"><?php echo $row["otaf"]; ?></td>
            <td class="text" valign="top"><?php echo $row["totalhour"]; ?></td>
            <td class="text" valign="top"> <?php echo $row["notes"]; ?></td>
        </tr>
    <?php
    }

    ?>
    <tr>
        <td colspan=9 align="right">Total :</td>
        <td class="text"><?php echo $footah["totallate"]; ?></td>
        <td class="text"><?php echo $footah["totalearly"]; ?></td>
        <!--<td class="text"><?php /*echo $footah["totalotbef"]; */?></td>-->
        <td class="text"><?php echo $footah["totalotaf"]; ?></td>
        <td class="text"><?php echo $footah["total"]; ?></td>
        <td>&nbsp;</td>
    </tr>
</table>
<?php if ($pdfid == 1 && $lastPage == 0) {?>
    <pagebreak />
<?php }?>
<?php if ($excelid == 0 ) {?>
    <div style="page-break-after:always"></div><br><br>
    </body>
    </html>
<?php }?>