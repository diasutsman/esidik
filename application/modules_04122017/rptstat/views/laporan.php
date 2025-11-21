<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <style>
        <?php include FCPATH."/assets/css/print.css"; ?>
    </style>
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
        LAPORAN STATUS
    </center>
</h1>
<br>
<table class="head">
    <tr>
        <td width="120" style="border:0px">ID Pegawai</td>
        <td style="border:0px">: </td>
        <td width="120" style="border:0px">Periode</td>
        <td width="200" style="border:0px">: </td>
        <td width="500" style="border:0px">&nbsp;</td>
    </tr>
    <tr>
        <td width="120" style="border:0px">NIP Pegawai</td>
        <td style="border:0px">: </td>
        <td width="120" style="border:0px">Unit Kerja</td>
        <td width="200" style="border:0px">: </td>
        <td width="500" style="border:0px">&nbsp;</td>
    </tr>
    <tr>
        <td width="120" style="border:0px">Nama</td>
        <td style="border:0px">: </td>
        <td width="120" style="border:0px">&nbsp;</td>
        <td width="200" style="border:0px">&nbsp;</td>
        <td width="500" style="border:0px">&nbsp;</td>
    </tr>
</table>
<br>
<table width='100%' border='1' cellspacing='0' cellpadding='0'>
    <tr>
        <th width="200">Hari</th>
        <th width="400">Tanggal</th>
        <th width="300">Status</th>
        <th>Catatan</th>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

</table>
<br style='page-break-before: always;'>
</body>
</html>