<?php if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <style>
        <?php include FCPATH."/assets/css/print.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Transaksi Sidik Jari</title>
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
<hr />
<h1>
        LAPORAN TRANSAKSI SIDIK JARI
</h1>
</center>
<br>
<table class="head">
    <tr>
        <td width="120" style="border:0px" class="text">Tanggal</td>
        <td style="border:0px" class="text">: <?php echo format_date_ind($empinfo["datee"]);?></td>
        <td style="border:0px">&nbsp;</td>
    </tr>

</table>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th class="text">NIP</th>
        <th class="text">Nama</th>
        <th class="text">Nomor Serial</th>
        <th class="text">Nama Alat</th>
        <th class="text">Tanggal</th>
        <th class="text">Jam</th>
        <th class="text">Function Key</th>
        <th class="text">Deskripsi</th>
        <th class="text">Kode Verifikasi</th>
    </tr></thead>
    <tbody>
    <?php
    $x = 1;
    foreach ($data as $row) {
    ?>
        <tr>
            <td  class="text"><?php echo "&nbsp;".$row["userid"];?></td>
            <td class="text"><?php echo $row["name"];?></td>
            <td class="text"><?php echo "&nbsp;".$row["SN"];?></td>
            <td class="text"><?php echo $row["alias"];?></td>
            <td class="text"><?php echo $row["datelog"];?></td>
            <td class="text"><?php echo $row["timelog"];?></td>
            <td class="text"><?php echo $row["functionkey"];?></td>
            <td class="text"><?php echo $row["description"];?></td>
            <td class="text"><?php echo $row["verifymode"];?></td>
        </tr>
    <?php }
    ?>
    </tbody>
</table>
<?php if ($pdfid == 1 ) {?>
    <pagebreak />
<?php }?>
<?php if ($excelid == 0 ) {?>
    <div style="page-break-after:always"></div>
    <br><br><br>
    </body>
    </html>
<?php }?>