<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/print.css"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Tunjangan</title>
</head>
<body>

<style> .phone{ mso-number-format:\@; } </style>

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
<hr /><h1><center>
        LAPORAN REKAPITULASI TUNJANGAN KINERJA<br/>BERDASARKAN DISIPLIN KERJA</center></h1><br>
<center><div style='font-family:arial;font-size:11px'>PERIODE : </div></center><br>
<table class="head">
    <tr>
        <td width="150" style="border:0px">Organisasi</td>
        <td style="border:0px">: </td>
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
        <th>Tunjangan Kinerja</th>
        <th>Total Pengurangan</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <tr class="odd">
        <td>1</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
    </tr>
    <tr>
        <td colspan=5 align="center"><b>Total</b></td>
        <td align="right"><b></b></td>
        <td align="right"><b></b></td>
        <td align="right"><b></b></td>
    </tr>
</table>
<br><br>
<div style="page-break-after:always"></div>
</body>
</html>