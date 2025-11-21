<?php if ($excelid == 0 ) { ?>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/print.css"/>
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
    <h1><?php echo $cominfo["companyname"];?></h1>
    <?php echo $cominfo["address1"];?><br>
    Telepon: <?php echo $cominfo["phone"];?>, Faks: <?php echo $cominfo["fax"];?>
<hr/>
<h1>
        LAPORAN REKAPITULASI

</h1>
</center>
<br>
<table class="head">
    <tr>
        <td width="120" style="border:0px" class="text">Organisasi</td>
        <td style="border:0px">: </td>
        <td width="120" style="border:0px" class="text">Total Libur</td>
        <td style="border:0px">: </td>
        <td width="40%" style="border:0px">&nbsp;</td>
    </tr>
    <tr>
        <td width="120" style="border:0px" class="text">Periode</td>
        <td style="border:0px" class="text">: </td>
        <td width="120" style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
        <td width="40%" style="border:0px">&nbsp;</td>
    </tr>
</table>
<br>

<table width="100%" border="1" cellspacing="0" cellpadding="0">

    <thead>
    <tr>
        <th rowspan="2" align="center" class="text">ID<br>Pegawai</th>
        <th rowspan="2" align="center" class="text">Nama<br>Pegawai</th>
        <th rowspan="2" align="center" class="text">Hari<br>Kerja</th>
        <th rowspan="2" align="center" class="text">Bukan<br>Hari<br>Kerja</th>
        <th colspan="2" align="center" class="text">Kehadiran</th>
        <th rowspan="2" align="center" class="text">Ketidakhadiran<br>+<br>Alpa</th>
        <th colspan="7" align="center" class="text">Status Kehadiran</th>
        <th rowspan="2" align="center" class="text">Total<br/>Status<br/>Kehadiran</th>
        <th colspan="18" align="center" class="text">Status Ketidakhadiran</th>
        <th rowspan="2" align="center" class="text">Total<br/>Status<br/>Ketidakhadiran</th>
        <th rowspan="2" align="center" class="text">Alpa</th>
        <th rowspan="2" align="center" class="text">Terlambat</th>
        <th rowspan="2" align="center" class="text">Pulang Awal</th>
        <th rowspan="2" align="center" class="text">Lembur</th>
    </tr>
    <tr>
        <th align="center" class="text">Hari<br/>Kerja</th>
        <th align="center" class="text">Bukan Hari<br/>Kerja</th>
        <th align="center" class="text">AT_AT4</th>
        <th align="center" class="text">AT_AT5</th>
        <th align="center" class="text">AT_AT2</th>
        <th align="center" class="text">AT_DK</th>
        <th align="center" class="text">AT_AT3</th>
        <th align="center" class="text">AT_AT1</th>
        <th align="center" class="text">AT_AT6</th>
        <th align="center" class="text">AB_1</th>
        <th align="center" class="text">AB_2</th>
        <th align="center" class="text">AB_3</th>
        <th align="center"> class="text"AB_4</th>
        <th align="center" class="text">AB_5</th>
        <th align="center" class="text">AB_6</th>
        <th align="center" class="text">AB_7</th>
        <th align="center" class="text">AB_13</th>
        <th align="center" class="text">AB_8</th>
        <th align="center" class="text">AB_9</th>
        <th align="center" class="text">AB_15</th>
        <th align="center" class="text">AB_16</th>
        <th align="center" class="text">AB_17</th>
        <th align="center" class="text">AB_12</th>
        <th align="center" class="text">AB_18</th>
        <th align="center" class="text">AB_19</th>
        <th align="center" class="text">AB_11</th>
        <th align="center" class="text">AB_14</th>
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
        <td class="text"><?php echo $key?></td>
        <td class="text"><?php echo $row?></td>
    <tr>
        <?php } ?>

    <tr>
        <td colspan="2">Status Ketidakhadiran:
        </td>
    <tr>

        <?php foreach ($absence as $key=>$row) {?>
    <tr>
        <td class="text"><?php echo $key?></td>
        <td class="text"><?php echo $row?></td>
    <tr>
        <?php } ?>
</table>
<?php if ($excelid == 0 ) {?>
<div style="page-break-after:always"></div><br><br>
</body>
</html>
<?php }?>