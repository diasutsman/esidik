<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/print.css"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Tunjangan</title>
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
</body>
</html>