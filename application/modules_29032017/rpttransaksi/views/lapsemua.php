<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/print.css")?>" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Transaksi</title>
</head>

<body>

<table class="head">
    <tr>
        <td width="180" rowspan="4" align="center" style="border:0px"></td>
        <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
        <td align="center" style="border:0px"><h1><?php echo $cominfo["companyname"];?></h1></td>
    </tr>
    <tr>
        <td align="center" style="border:0px"><?php echo $cominfo["address1"];?></td>
    </tr>
    <tr>
        <td align="center" style="border:0px">Telepon: <?php echo $cominfo["phone"];?>, Faks: <?php echo $cominfo["fax"];?></td>
        <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
        <td width="180" rowspan="4" align="center" style="border:0px">&nbsp;</td>
    </tr>
</table>
<hr /><h1><center>
        LAPORAN TRANSAKSI</center></h1><br>

<table class="head">
    <tr>
        <td width="120" style="border:0px;" valign="top">NIP Pegawai</td>
        <td style="border:0px" valign="top">: <?php echo $empinfo["empID"];?></td>
        <td width="120" style="border:0px" valign="top">Jabatan</td>
        <td style="border:0px" valign="top">: <?php echo $empinfo["empTitle"];?></td>
        <td style="border:0px" valign="top">&nbsp;</td>
    </tr>
    <tr>
        <td width="120" style="border:0px" valign="top">Nama</td>
        <td style="border:0px" valign="top">: <?php echo $empinfo["empName"];?></td>
        <td width="120" style="border:0px" valign="top">Organisasi</td>
        <td style="border:0px" valign="top">: <?php echo $empinfo["deptName"];?></td>
        <td style="border:0px" valign="top">&nbsp;</td>
    </tr>
</table><br>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <thead><tr>
        <th>Nomor Serial</th>
        <th>Nama Alat</th>
        <th>Tanggal</th>
        <th>Jam</th>
        <th>Function Key</th>
        <th>Deskripsi</th>
        <th>Kode Verifikasi</th>
    </tr></thead><tbody>
    <?php

    $x = 1;
    foreach ($data as $row) {
    ?>
    <tr >
        <td><?php echo $row["SN"];?></td>
        <td><?php echo $row["alias"];?></td>
        <td><?php echo $row["datelog"];?></td>
        <td><?php echo $row["timelog"];?></td>
        <td><?php echo $row["functionkey"];?></td>
        <td><?php echo $row["description"];?></td>
        <td><?php echo $row["verifymode"];?></td>
    </tr>
    <?php }
    ?>
    </tbody>
</table>
<br><br><br>

<div style="page-break-after:always"></div>