<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/print.css"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Kehadiran</title>
</head>

<body>

<table class="head">
    <tr>
        <td width="180" rowspan="4" align="center" style="border:0px"></td>
        <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
        <td align="center" style="border:0px"><h1><?php echo $cominfo["companyname"]?></h1></td>
    </tr>
    <tr>
        <td align="center" style="border:0px"><?php echo $cominfo["address1"]?></td>
    </tr>
    <tr>
        <td align="center" style="border:0px">Telepon: <?php echo $cominfo["phone"]?>, Faks: <?php echo $cominfo["fax"]?></td>
        <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
        <td width="180" rowspan="4" align="center" style="border:0px">&nbsp;</td>
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
        <td style="border:0px">:  <?php echo $empinfo["empID"]?></td>
        <td width="120" style="border:0px">Jabatan</td>
        <td style="border:0px">:  <?php echo $empinfo["empTitle"]?></td>
    </tr>
    <tr>
        <td width="120" style="border:0px">Nama</td>
        <td style="border:0px">:  <?php echo $empinfo["empName"]?></td>
        <td width="120" style="border:0px">Organisasi</td>
        <td style="border:0px">:  <?php echo $empinfo["deptName"]?></td>
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
    <tr >
        <td><?php echo hariToInd($row["day"]);?></td>
        <td><?php echo $row["date"];?></td>
        <td><?php echo $row["workinghour"];?></td>
        <td><?php echo $row["activity"];?></td>
        <td><?php echo $row["datein"];?></td>
        <td><?php echo $row["dutyon"];?></td>
        <td><?php echo $row["breakout"];?></td>
        <td><?php echo $row["breakin"];?></td>
        <td><?php echo $row["dateout"];?></td>
        <td><?php echo $row["dutyoff"];?></td>
        <td><?php echo $row["latein"];?></td>
        <td><?php echo $row["earlydept"];?></td>
        <td><?php echo $row["otbef"];?></td>
        <td><?php echo $row["otaf"];?></td>
        <td><?php echo $row["totalhour"];?></td>
        <td><?php echo $row["notes"];?></td>
    </tr>
    <?}

    ?>
    <tr>
        <td colspan=10 align="right">Total :</td>
        <td><?php echo $footah["totallate"];?></td>
        <td><?php echo $footah["totalearly"];?></td>
        <td><?php echo $footah["totalotbef"];?></td>
        <td><?php echo $footah["totalotaf"];?></td>
        <td><?php echo $footah["total"];?></td>
        <td>&nbsp;</td>
    </tr>
</table>

<br><br>
<div style="page-break-after:always"></div>
</body>
</html>