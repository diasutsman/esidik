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
        <td width="120" style="border:0px">Hari</td>
        <td style="border:0px">: <?php echo hariToInd($empinfo["day"])?></td>
    </tr>
    <tr>
        <td width="120" style="border:0px">Tanggal</td>
        <td style="border:0px">: <?php echo format_date_ind($empinfo["date"])?></td>
    </tr>
</table>
<br>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th>NIP</th>
        <th>Nama</th>
        <th>Organisasi</th>
        <th>Jam Kerja</th>
        <th>Aktifitas</th>
        <th>Jam Masuk</th>
        <th>Keluar Istirahat</th>
        <th>Masuk Istirahat</th>
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
        <td><?php echo $row["userid"];?></td>
        <td><?php echo $row["name"];?></td>
        <td><?php echo $row["dept"];?></td>
        <td><?php echo $row["workinghour"];?></td>
        <td><?php echo $row["activity"];?></td>
        <td ><?php echo $row["datein"];?></td>
        <td><?php echo $row["breakout"];?></td>
        <td><?php echo $row["breakin"];?></td>
        <td ><?php echo $row["dateout"];?></td>
        <td><?php echo $row["latein"];?></td>
        <td><?php echo $row["earlydept"];?></td>
        <td><?php echo $row["otbef"];?></td>
        <td><?php echo $row["otaf"];?></td>
        <td><?php echo $row["totalhour"];?></td>
        <td><?php echo $row["notes"];?></td>
    </tr>
    <?php }

    ?>
    <tr>
        <td colspan=9 align="right">Total :</td>
        <td>00:00</td>
        <td>00:00</td>
        <td>00:00</td>
        <td>00:00</td>
        <td>00:00</td>
        <td>&nbsp;</td>
    </tr>
</table>
<br><br>
<div style="page-break-after:always"></div>
</body>
</html>