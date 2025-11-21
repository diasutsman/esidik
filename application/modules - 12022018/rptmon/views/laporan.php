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
<center>
    <h1><?php echo $cominfo["companyname"];?></h1>
    <?php echo $cominfo["address1"];?><br>
    Telepon: <?php echo $cominfo["phone"];?>, Faks: <?php echo $cominfo["fax"];?>
<br><br>
<h1>
    LAPORAN KELUAR MASUK</h1>
</center>

<div style='font-size: 11px;font-family: arial;'><?php echo $periode?>,</div>
<br>
<table class="head">
    <tr align="center">
        <td colspan="4" align="left" style="border:0px"><br><h4> Unit Kerja: </h4></td>
    </tr>
    <tr align="center">
        <th width="40" rowspan="2">NO</th>
        <th width="150" rowspan="2">NIP Pegawai</th>
        <th width="250" rowspan="2">Nama</th>
        <?php foreach ($arr_days as $key => $value) {?>
            <th width="80" colspan="2"><?php echo format_date_singkat($value);?></th>
        <?php }?>

    </tr>
    <tr>
        <?php foreach ($arr_days as $row) {?>
            <th align="center" width="50%">MASUK</th>
            <th align="center">KELUAR</th>
        <?php }?>

    </tr>
    <tr>
        <td align="center" width="40">1</td>
        <td align="left" width="150"></td>
        <td align="left" width="250"></td>
        <?php foreach ($arr_days as $row) {?>
            <td align="center"></td>
            <td align="center"></td>
        <?php }?>
    </tr>
</table>
<?php if ($excelid == 0 ) {?>
<div style="page-break-after:always"></div><br><br>
</body>
</html>
<?php }?>