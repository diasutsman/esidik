<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <style>
        <?php include FCPATH."/assets/css/print.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Tunjangan</title>
</head>
<body>

<style> .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
</style>

<table class="head">
    <tr>
        <td width="180" rowspan="4" align="center" style="border:0px"></td>
        <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
        <td align="center" style="border:0px" class="text"><h1><?php echo $cominfo["companyname"]?></h1></td>
    </tr>
    <tr>
        <td align="center" style="border:0px" class="text"><?php echo $cominfo["address1"]?></td>
    </tr>
    <tr>
        <td align="center" style="border:0px" class="text">Telepon: <?php echo $cominfo["phone"]?>, Faks: <?php echo $cominfo["fax"]?></td>
        <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
        <td width="180" rowspan="4" align="center" style="border:0px">&nbsp;</td>
    </tr>
</table>
<hr /><h1><center>
        LAPORAN REKAPITULASI TUNJANGAN KINERJA<br/>BERDASARKAN DISIPLIN KERJA</center></h1><br>
<center><div >PERIODE : </div></center><br>
<table class="head">
    <tr>
        <td width="150" style="border:0px" class="text">Unit Kerja</td>
        <td style="border:0px" class="text">: </td>
        <td width="150" style="border:0px"></td>
        <td style="border:0px"></td>
        <td style="border:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th class="text">No</th>
        <th class="text">NIP</th>
        <th class="text">Nama</th>
        <th class="text">GOL</th>
        <th class="text">Kelas Jabatan</th>
        <th class="text">Tunjangan Kinerja</th>
        <th class="text">Total Pengurangan</th>
        <th class="text">Total</th>
    </tr>
    </thead>
    <tbody>
    <tr class="odd">
        <td class="text">1</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
    </tr>
    <tr>
        <td colspan=5 align="center" class="text"><b>Total</b></td>
        <td align="right"><b></b></td>
        <td align="right"><b></b></td>
        <td align="right"><b></b></td>
    </tr>
</table>
<br><br>
<div style="page-break-after:always"></div>
</body>
</html>