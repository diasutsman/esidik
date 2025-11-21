<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/print.css"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Kehadiran</title>
</head>
<body>
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
<br><br>
<h1>
    LAPORAN KELUAR MASUK</h1>

<div style='font-size: 11px;font-family: arial;'><?php echo $periode?>,</div>
<br>
<table class="head">
    <tr align="center">
        <td colspan="4" align="left" style="border:0px"><br><h4> Organisasi: </h4></td>
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
</body>
</html>