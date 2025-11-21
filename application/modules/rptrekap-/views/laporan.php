<?php

if ($excelid == 0 ) { ?>
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
        .tblnoborder{
            float:right;
        }

        .tblnoborder td {
            padding: 4px 3px 4px 5px;
            border-style: none;
            border-width: 0px;

    </style>

    <center>
        <h1><?php echo $cominfo["companyname"];?></h1>
        <?php echo $cominfo["address1"];?><br>
        Telepon: <?php echo $cominfo["phone"];?>, Faks: <?php echo $cominfo["fax"];?>
        <hr/>
        <h1>
            <?php echo strtoupper('Laporan Rekapitulasi Kehadiran'); ?>

        </h1>
    </center>
    <br>
    <table class="head"  width="100%">
        <tr>
            <td width="120" style="border:0px">Unit Kerja</td>
            <td style="border:0px">: <?php echo $empinfo["dept"]?></td>
        </tr>
        <tr>
            <td width="120" style="border:0px">Periode</td>
            <td style="border:0px">: <?php echo format_date_ind($empinfo["datestart"])." s/d ".format_date_ind($empinfo["datestop"])?></td>
        </tr>
        <tr>
            <td width="120" style="border:0px">Hari Libur Nasional</td>
            <td style="border:0px">: <?php echo $empinfo["holidays"]?></td>
        </tr>
    </table>
    <br>
<?php
$jmlFieldAtt=count($attendance);
$jmlFieldAbs=count($absence);
?>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
            <th rowspan="2" align="center">NIP<br>Pegawai</th>
            <th rowspan="2" align="center">Nama<br>Pegawai</th>
            <th rowspan="2" align="center">Hari<br>Kerja</th>
            <th rowspan="2" align="center">WFH</th>
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
        <?php
        $x = 1;
        foreach ($data as $row) {
            ?>
            <tr border="1">
                <td class="text"><?php echo ($excelid==1?" ".$row["userid"]:$row["userid"]) ?></td>
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

        //print_r($attendance);
        ?>

        <tr>
            <td style="border: none" colspan="<?php echo ($jmlFieldAbs+$jmlFieldAtt+13)?>">
                <table cellspacing="0" width="100%" cellpadding="0" border="0" style="border: none">
                    <tr>
                        <td valign="top" style="border: none">
                            <table cellspacing="0" cellpadding="0">
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
                        </td>
                        <?php
                        for ($i=0; $i < 35; $i++) { ?>

                            <td valign="top" style="border: none">
                                <table cellspacing="0" cellpadding="0" border="0" class="tblnoborder">
                                    <tr>
                                        <td >&nbsp;
                                        </td>
                                    <tr>
                                    <tr>
                                        <td >&nbsp;
                                        </td>
                                    <tr>
                                    <tr>
                                        <td >&nbsp;
                                        </td>
                                    <tr>
                                    <tr>
                                        <td >&nbsp;
                                        </td>
                                    <tr>
                                    <tr>
                                        <td >&nbsp;
                                        </td>
                                    <tr>
                                    <tr>
                                        <td >&nbsp;
                                        </td>
                                    <tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    <tr>
                                    <tr>
                                        <td >&nbsp;
                                        </td>
                                    <tr>
                                    <tr>
                                        <td >&nbsp;
                                        </td>
                                    <tr>
                                </table>
                            </td>


                        <?php     }     ?>
                        <td valign="top" style="border: none">
                            <table cellspacing="0" cellpadding="0" border="0" class="tblnoborder">
                                <tr>
                                    <td style="width: 70">Tempat,
                                    </td>
                                <tr>
                                <tr>
                                    <td ><?php echo $ttd[0];?>
                                    </td>
                                <tr>
                                <tr>
                                    <td >&nbsp;
                                    </td>
                                <tr>
                                <tr>
                                    <td >&nbsp;
                                    </td>
                                <tr>
                                <tr>
                                    <td >&nbsp;
                                    </td>
                                <tr>
                                <tr>
                                    <td >&nbsp;
                                    </td>
                                <tr>
                                <tr>
                                    <td ><?php echo $ttd[1];?>
                                    </td>
                                <tr>
                                <tr>
                                    <td ><?php echo $ttd[2];?>
                                    </td>
                                <tr>
                                <tr>
                                    <td >NIP:  <?php echo $ttd[3];?>
                                    </td>
                                <tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <!-- End table td keterangan -->

        </tr>

    </table>
    <br/>

    <br>

<?php if ($excelid == 0 ) {?>
    <div style="page-break-after:always"></div><br><br>
    </body>
    </html>
<?php }?>