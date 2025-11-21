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
        <td align="center" style="border:0px"><h1><?php echo $cominfo["companyname"]?></h1></td>
    </tr>
    <tr>
        <td align="center" style="border:0px"><?php echo $cominfo["address1"]?></td>
    </tr>
    <tr>
        <td align="center" style="border:0px">Telepon: <?php echo $cominfo["phone"]?>, Faks: <?php echo $cominfo["fax"]?></td>
    </tr>
</table>
<hr/>
<h1>
    <center>
        LAPORAN REKAPITULASI
    </center>
</h1>
<br>
<table class="head">
    <tr>
        <td width="120" style="border:0px">Unit Kerja</td>
        <td style="border:0px">: </td>
        <td width="120" style="border:0px">Total Libur</td>
        <td style="border:0px">: </td>
        <td width="40%" style="border:0px">&nbsp;</td>
    </tr>
    <tr>
        <td width="120" style="border:0px">Periode</td>
        <td style="border:0px">: </td>
        <td width="120" style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
        <td width="40%" style="border:0px">&nbsp;</td>
    </tr>
</table>
<br>

<table width="100%" border="1" cellspacing="0" cellpadding="0">

    <thead>
    <tr>
        <th rowspan="2" align="center">NIP<br>Pegawai</th>
        <th rowspan="2" align="center">Nama<br>Pegawai</th>
        <th rowspan="2" align="center">Hari<br>Kerja</th>
        <th rowspan="2" align="center">Bukan<br>Hari<br>Kerja</th>
        <th colspan="2" align="center">Kehadiran</th>
        <th rowspan="2" align="center">Ketidakhadiran<br>+<br>Alpa</th>
        <th colspan="7" align="center">Status Kehadiran</th>
        <th rowspan="2" align="center">Total<br/>Status<br/>Kehadiran</th>
        <th colspan="18" align="center">Status Ketidakhadiran</th>
        <th rowspan="2" align="center">Total<br/>Status<br/>Ketidakhadiran</th>
        <th rowspan="2" align="center">Alpa</th>
        <th rowspan="2" align="center">Terlambat</th>
        <th rowspan="2" align="center">Pulang Awal</th>
        <th rowspan="2" align="center">Lembur</th>
    </tr>
    <tr>
        <th align="center">Hari<br/>Kerja</th>
        <th align="center">Bukan Hari<br/>Kerja</th>
        <th align="center">AT_AT4</th>
        <th align="center">AT_AT5</th>
        <th align="center">AT_AT2</th>
        <th align="center">AT_DK</th>
        <th align="center">AT_AT3</th>
        <th align="center">AT_AT1</th>
        <th align="center">AT_AT6</th>
        <th align="center">AB_1</th>
        <th align="center">AB_2</th>
        <th align="center">AB_3</th>
        <th align="center">AB_4</th>
        <th align="center">AB_5</th>
        <th align="center">AB_6</th>
        <th align="center">AB_7</th>
        <th align="center">AB_13</th>
        <th align="center">AB_8</th>
        <th align="center">AB_9</th>
        <th align="center">AB_15</th>
        <th align="center">AB_16</th>
        <th align="center">AB_17</th>
        <th align="center">AB_12</th>
        <th align="center">AB_18</th>
        <th align="center">AB_19</th>
        <th align="center">AB_11</th>
        <th align="center">AB_14</th>
    </tr>
    </thead>
    <tbody>
    <tr >
        <td></td>
        <td></td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center">-</td>
        <td align="center"></td>

        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>

        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
    </tr>
    </tbody>
</table>
<br>
<br>
<table width="100%" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <td colspan="2">KETERANGAN</td>
    </tr>
    </thead>
    <tr>
        <td colspan="2">Status Kehadiran:
        </td>
    <tr>

        <?php foreach ($attendance as $key=>$row) {?>
    <tr>
        <td><?php echo $key?></td>
        <td><?php echo $row?></td>
    <tr>
        <?php } ?>

    <tr>
        <td colspan="2">Status Ketidakhadiran:
        </td>
    <tr>

        <?php foreach ($absence as $key=>$row) {?>
    <tr>
        <td><?php echo $key?></td>
        <td><?php echo $row?></td>
    <tr>
        <?php } ?>
</table>
<?php if ($excelid == 0 ) {?>
<div style="page-break-after:always"></div><br><br>
</body>
</html>
<?php }?>