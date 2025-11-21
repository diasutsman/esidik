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
        <td style="border:0px" class="text">: <?php echo $empinfo["dept"]?></td>
        <td width="120" style="border:0px" class="text">Total Libur</td>
        <td style="border:0px" class="text">: <?php echo $empinfo["holidays"]?></td>
        <td width="40%" style="border:0px">&nbsp;</td>
    </tr>
    <tr>
        <td width="120" style="border:0px" class="text">Periode</td>
        <td style="border:0px" class="text">: <?php echo format_date_ind($empinfo["datestart"])." s/d ".format_date_ind($empinfo["datestop"])?></td>
        <td width="120" style="border:0px">&nbsp;</td>
        <td style="border:0px">&nbsp;</td>
        <td width="40%" style="border:0px">&nbsp;</td>
    </tr>
</table>
<br>
<?php
$jmlFieldAtt=count($attendance);
$jmlFieldAbs=count($absence);
?>
<table width="100%" border="1" cellspacing="0" cellpadding="0">

    <thead>
    <tr>
        <th rowspan="2" align="center" class="text">ID<br>Pegawai</th>
        <th rowspan="2" align="center" class="text">Nama<br>Pegawai</th>
        <th rowspan="2" align="center" class="text">Hari<br>Kerja</th>
        <th rowspan="2" align="center" class="text">Bukan<br>Hari<br>Kerja</th>
        <th colspan="2" align="center" class="text">Kehadiran</th>
        <th rowspan="2" align="center" class="text">Ketidakhadiran<br>+<br>Alpa</th>
        <th colspan="<?php echo $jmlFieldAtt?>" align="center" class="text">Status Kehadiran</th>
        <th rowspan="2" align="center" class="text">Total<br/>Status<br/>Kehadiran</th>
        <th colspan="<?php echo $jmlFieldAbs?>" align="center" class="text">Status Ketidakhadiran</th>
        <th rowspan="2" align="center" class="text">Total<br/>Status<br/>Ketidakhadiran</th>
        <th rowspan="2" align="center" class="text">Alpa</th>
        <th rowspan="2" align="center" class="text">Terlambat</th>
        <th rowspan="2" align="center" class="text">Pulang Awal</th>
        <th rowspan="2" align="center" class="text">Lembur</th>
    </tr>
    <tr>
        <th align="center" class="text">Hari<br/>Kerja</th>
        <th align="center" class="text">Bukan Hari<br/>Kerja</th>
        <?php foreach ($attendance as $key=>$row) {?>
        <th align="center" class="text"><?php echo $key?></th>
        <?php } ?>
        <?php foreach ($absence as $key=>$row) {?>
            <th align="center" class="text"><?php echo $key?></th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $x = 1;
    foreach ($data as $row) {
    ?>
    <tr >
        <td class="text"><?php echo "&nbsp;".$row["userid"]?></td>
        <td class="text"><?php echo $row["name"]?></td>
        <td align="center" class="text"><?php echo $row["workday"]?></td>
        <td align="center" class="text"><?php echo $row["off"]?></td>
        <td align="center" class="text"><?php echo $row["attendance"]?></td>
        <td align="center" class="text"><?php echo $row["workinholiday"]?></td>
        <td align="center" class="text"><?php echo $row["totalabsent"]?></td>
        <?php $araten = $row["aten"]; $jmlr=0;?>
        <?php foreach ($attendance as $key=>$rowk) {?>
            <td align="center" class="text"><?php echo $araten[$row["userid"]][$key]!=0?$araten[$row["userid"]][$key]:'-' ; $jmlr +=$araten[$row["userid"]][$key];?></td>
        <?php } ?>
                <td align="center" class="text"><?php echo $row["attendance"]?></td>
        <?php $araten = $row["aben"]; $jmlr=0; ?>
        <?php foreach ($absence as $key=>$rowk) {?>
            <td align="center" class="text"><?php echo $araten[$row["userid"]][$key]!=0?$araten[$row["userid"]][$key]:'-'; $jmlr +=$araten[$row["userid"]][$key];?></td>
        <?php } ?>

        <td align="center" class="text"><?php echo $row["absence"]?></td>
        <td align="center" class="text"><?php echo $row["absent"]?></td>
        <td align="center" class="text"><?php echo $row["late"]?></td>
        <td align="center" class="text"><?php echo $row["early"]?></td>
        <td align="center" class="text"><?php echo $row["OT"]?></td>
    </tr>
    <?php }

    ?>
    </tbody>
</table>
<br>
<table width="100%" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <td colspan="2" class="text">KETERANGAN</td>
        </tr>
    </thead>
    <tr>
        <td colspan="2" class="text">Status Kehadiran:
        </td>
    <tr>

        <?php foreach ($attendance as $key=>$row) {?>
    <tr>
        <td><?php echo $key?></td>
            <td><?php echo $row?></td>
    <tr>
        <?php } ?>

    <tr>
        <td colspan="2" class="text">Status Ketidakhadiran:
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