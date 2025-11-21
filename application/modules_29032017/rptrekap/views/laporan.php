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
        LAPORAN REKAPITULASI
    </center>
</h1>
<br>
<table class="head">
    <tr>
        <td width="120" style="border:0px">Organisasi</td>
        <td style="border:0px">: <?php echo $empinfo["dept"]?></td>
        <td width="120" style="border:0px">Total Libur</td>
        <td style="border:0px">: <?php echo $empinfo["holidays"]?></td>
        <td width="40%" style="border:0px">&nbsp;</td>
    </tr>
    <tr>
        <td width="120" style="border:0px">Periode</td>
        <td style="border:0px">: <?php echo format_date_ind($empinfo["datestart"])." s/d ".format_date_ind($empinfo["datestop"])?></td>
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
        <th rowspan="2" align="center">ID<br>Pegawai</th>
        <th rowspan="2" align="center">Nama<br>Pegawai</th>
        <th rowspan="2" align="center">Hari<br>Kerja</th>
        <th rowspan="2" align="center">Bukan<br>Hari<br>Kerja</th>
        <th colspan="2" align="center">Kehadiran</th>
        <th rowspan="2" align="center">Ketidakhadiran<br>+<br>Alpa</th>
        <th colspan="<?php echo $jmlFieldAtt?>" align="center">Status Kehadiran</th>
        <th rowspan="2" align="center">Total<br/>Status<br/>Kehadiran</th>
        <th colspan="<?php echo $jmlFieldAbs?>" align="center">Status Ketidakhadiran</th>
        <th rowspan="2" align="center">Total<br/>Status<br/>Ketidakhadiran</th>
        <th rowspan="2" align="center">Alpa</th>
        <th rowspan="2" align="center">Terlambat</th>
        <th rowspan="2" align="center">Pulang Awal</th>
        <th rowspan="2" align="center">Lembur</th>
    </tr>
    <tr>
        <th align="center">Hari<br/>Kerja</th>
        <th align="center">Bukan Hari<br/>Kerja</th>
        <?php foreach ($attendance as $key=>$row) {?>
        <th align="center"><?php echo $key?></th>
        <?php } ?>
        <?php foreach ($absence as $key=>$row) {?>
            <th align="center"><?php echo $key?></th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $x = 1;
    foreach ($data as $row) {
    ?>
    <tr >
        <td><?php echo $row["userid"]?></td>
        <td><?php echo $row["name"]?></td>
        <td align="center"><?php echo $row["workday"]?></td>
        <td align="center"><?php echo $row["off"]?></td>
        <td align="center"><?php echo $row["attendance"]?></td>
        <td align="center"><?php echo $row["workinholiday"]?></td>
        <td align="center"><?php echo $row["totalabsent"]?></td>
        <?php $araten = $row["aten"]; $jmlr=0;?>
        <?php foreach ($attendance as $key=>$rowk) {?>
            <td align="center"><?php echo $araten[$row["userid"]][$key]!=0?$araten[$row["userid"]][$key]:'-' ; $jmlr +=$araten[$row["userid"]][$key];?></td>
        <?php } ?>
                <td align="center"><?php echo $row["attendance"]?></td>
        <?php $araten = $row["aben"]; $jmlr=0; ?>
        <?php foreach ($absence as $key=>$rowk) {?>
            <td align="center"><?php echo $araten[$row["userid"]][$key]!=0?$araten[$row["userid"]][$key]:'-'; $jmlr +=$araten[$row["userid"]][$key];?></td>
        <?php } ?>

        <td align="center"><?php echo $row["absence"]?></td>
        <td align="center"><?php echo $row["absent"]?></td>
        <td align="center"><?php echo $row["late"]?></td>
        <td align="center"><?php echo $row["early"]?></td>
        <td align="center"><?php echo $row["OT"]?></td>
    </tr>
    <?php }

    ?>
    </tbody>
</table>
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
</body>
</html>