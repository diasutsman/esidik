<?php if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <style>
        <?php include FCPATH."/assets/css/print.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Uang Makan</title>
</head>

<body><?php } ?>
<style> .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
    .ttd {
        border-style: none;
    }
</style>

<center>
        <h1><?php echo $cominfo["companyname"];?></h1>
    <p align="center"><?php echo $cominfo["address1"];?></p>
    <p align="center">Telepon: <?php echo $cominfo["phone"];?>, Faks: <?php echo $cominfo["fax"];?></p>
<hr/>
<h1>
        LAPORAN UANG MAKAN<br>
        BERDASARKAN REKAPITULASI FINGER PRINT BULAN  <?php echo strtoupper(format_bulan_tahun($tgl))?>

</h1>
</center>
<br>
<div class="head">Unit Kerja: <?php echo ($dptname)?></div>
<table border="1">
    <tr>
        <th bgcolor="#CCCCCC" class="text">No.</th>
        <th bgcolor="#CCCCCC" class="text">NIP Pegawai</th>
        <th bgcolor="#CCCCCC" class="text">Nama</th>
        <th bgcolor="#CCCCCC" class="text">Golongan</th>
        <th bgcolor="#CCCCCC" class="text">Kehadiran Hari Kerja</th>
        <th bgcolor="#CCCCCC" class="text">Tarif Uang Makan</th>
        <th bgcolor="#CCCCCC" class="text">Jumlah Kotor</th>
        <th bgcolor="#CCCCCC" class="text">PPh</th>
        <th bgcolor="#CCCCCC" class="text">Jumlah Bersih</th>

    </tr>
    <?php
        $co=1;
        $jm1=0;$jm12=0;$jm13=0;$jm14=0;
        foreach($result as $row)
        {
            $jm1 +=$row["tarif"];
            $jm12 +=$row["kotor"];
            $jm13 +=$row["pajak"];
            $jm14 +=$row["bersih"];
        ?>
        <tr>
            <td  class="text"><?php echo $co;?></td>
            <td class="text" ><?php echo "&nbsp;".$row["userid"]?></td>
            <td class="text" ><?php echo $row["name"];?></td>
            <td class="text" ><?php echo $row["golru"]?></td>
            <td class="num" style="text-align: center"><?php echo $excelid == 0 ? format_angka($row["hadir"]) : $row["hadir"]?></td>
            <td class="num" style="text-align: right"><?php echo $excelid == 0 ? format_angka($row["tarif"]) : $row["tarif"] ?></td>
            <td class="num" style="text-align: right"><?php echo $excelid == 0 ? format_angka($row["kotor"]) : $row["kotor"]?></td>
            <td class="num" style="text-align: right"><?php echo $excelid == 0 ? format_angka($row["pajak"]) : $row["pajak"]?></td>
            <td class="num" style="text-align: right"><?php echo $excelid == 0 ? format_angka($row["bersih"]) : $row["bersih"]?></td>
        </tr>
    <?php

        $co++;
    }?>
    <tr style="font-weight: bold">
        <td colspan="5" style="text-align: center" class="text">JUMLAH</td>
        <td class="num" style="text-align: right"><?php echo $excelid == 0 ? format_angka($jm1) : $jm1; ?></td>
        <td class="num" style="text-align: right"><?php echo $excelid == 0 ? format_angka($jm12) : $jm12;?></td>
        <td class="num" style="text-align: right"><?php echo $excelid == 0 ? format_angka($jm13) : $jm13;?></td>
        <td class="num"  style="text-align: right"><?php echo $excelid == 0 ? format_angka($jm14) : $jm14;?></td>
    </tr>
</table>
<!-- section ttd -->
<div style="margin-top: 75px">
    <table border="0" style="border: none; width: 100%">
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>

            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td colspan="3" width="35%" class="text ttd" style="padding-bottom: 100px;"><b><?php echo  $ttd_jabatan; ?></b></td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>

            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="35%" class="text ttd"><b><?php echo  $ttd_nama; ?></b></td>
        </tr>
        <tr>

            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="35%" class="text ttd"><b><?php echo  $ttd_gol; ?></b></td>
        </tr>
        <tr>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="10%" class="ttd"></td>
            <td width="35%" class="text ttd">NIP. <?php echo  $ttd_nip; ?></td>
        </tr>
    </table>
</div>
<!-- end section ttd -->
<?php if ($excelid == 0 ) {?>
<br><br>
<div style="page-break-after:always"></div>
</body>
</html>
<?php }?>
