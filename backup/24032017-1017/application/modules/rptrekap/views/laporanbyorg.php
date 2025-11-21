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
        <th rowspan="2" align="center">ID<br>Pegawai</th>
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
<table class="head">
    <tr>
        <td width="500px" style="border:0px">Status :</td>
    </tr>
    <tr>
        <td width="500px" style="border:0px">
            Kehadiran :
        </td>
    </tr>
    <tr>
        <td width="500px" style="border:0px">
            <div style="float: left; padding: 15px;">AT_AT4&nbsp;&nbsp;:Ijin Tidak Absen Datang / Datang Terlambat dengan alasan sah</div>
            <div style="float: left; padding: 15px;">AT_AT5&nbsp;&nbsp;:Ijin Tidak Absen Pulang / Pulang cepat dengan alasan sah</div>
            <div style="float: left; padding: 15px;">AT_AT2&nbsp;&nbsp;:Tidak Absen Pulang Karena Rapat di Luar Kantor</div>
            <div style="float: left; padding: 15px;">AT_DK&nbsp;&nbsp;:Pendidikan dan Pelatihan</div>
            <div style="float: left; padding: 15px;">AT_AT3&nbsp;&nbsp;:Terlambat Absen Datang Karena Tugas Luar</div>
            <div style="float: left; padding: 15px;">AT_AT1&nbsp;&nbsp;:Tidak Absen Datang Karena Rapat di Luar Kantor</div>
            <div style="float: left; padding: 15px;">AT_AT6&nbsp;&nbsp;:Tidak Absen Datang & Pulang Karena Rapat di Luar Kantor</div>
        </td>
    </tr>
    <tr>
        <td width="500px" style="border:0px">
            Ketidakhadiran :
        </td>
    </tr>
    <tr>
        <td width="500px" style="border:0px">
            <div style="float: left; padding: 15px;">AB_1&nbsp;&nbsp;:Sakit</div>
            <div style="float: left; padding: 15px;">AB_2&nbsp;&nbsp;:Sakit Akibat Kecelakaan Dalam Tugas</div>
            <div style="float: left; padding: 15px;">AB_3&nbsp;&nbsp;:Cuti Tahunan</div>
            <div style="float: left; padding: 15px;">AB_4&nbsp;&nbsp;:Cuti Bersalin Anak Ke-1 Dan Ke-2</div>
            <div style="float: left; padding: 15px;">AB_5&nbsp;&nbsp;:Cuti Bersalin Anak Ke-3 Dst</div>
            <div style="float: left; padding: 15px;">AB_6&nbsp;&nbsp;:Cuti Sakit</div>
            <div style="float: left; padding: 15px;">AB_7&nbsp;&nbsp;:Cuti Besar</div>
            <div style="float: left; padding: 15px;">AB_13&nbsp;&nbsp;:Penugasan Dari Pimpinan (Dalam / Luar Negeri)</div>
            <div style="float: left; padding: 15px;">AB_8&nbsp;&nbsp;:Cuti Alasan Penting (Kerabat Meninggal >5 hari)</div>
            <div style="float: left; padding: 15px;">AB_9&nbsp;&nbsp;:Cuti Alasan Penting (Kerabat Meninggal <=5 hari)</div>
            <div style="float: left; padding: 15px;">AB_15&nbsp;&nbsp;:Pendidikan dan Pelatihan</div>
            <div style="float: left; padding: 15px;">AB_16&nbsp;&nbsp;:Tugas Belajar</div>
            <div style="float: left; padding: 15px;">AB_17&nbsp;&nbsp;:Cuti Luar Tanggungan Negara</div>
            <div style="float: left; padding: 15px;">AB_12&nbsp;&nbsp;:Pembatalan Absensi</div>
            <div style="float: left; padding: 15px;">AB_18&nbsp;&nbsp;:Meninggal</div>
            <div style="float: left; padding: 15px;">AB_19&nbsp;&nbsp;:DPK</div>
            <div style="float: left; padding: 15px;">AB_11&nbsp;&nbsp;:Ijin Tidak Masuk Kerja Dengan Alasan Sah</div>
            <div style="float: left; padding: 15px;">AB_14&nbsp;&nbsp;:Cuti Alasan Penting</div>
        </td>
    </tr>
    <tr>
        <td width="500px" style="border:0px">
        </td>
    </tr>
</table>
</body>
</html>