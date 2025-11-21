<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php //if($index==0) {
$clss=''; ?>
<?php if ($excelid == 0 ) { ?>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <style>
            <?php include FCPATH."/assets/css/print.css"; ?>
        </style>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Laporan Rekapitulasi Tunjangan Kinerja</title>
    </head>

    <body>
<?php } ?>
    <style> .phone{ mso-number-format:\@; } </style>
    <table class="head" width="100%">
        <tr>
            <td align="center" style="border:0px"><h1><?php echo $cominfo['companyname']; ?></h1></td>
        </tr>
        <tr>
            <td align="center"  style="border:0px"><?php echo $cominfo['address1']; ?></td>
        </tr>
        <tr>
            <td align="center"  style="border:0px">Telepon: <?php echo $cominfo['phone']; ?>, Faks: <?php echo $cominfo['fax']; ?></td>
        </tr>
    </table>
    <hr />
    <h1><center>
            <?php echo "LAPORAN REKAPITULASI TUNJANGAN KINERJA"; ?>
            <br><div >PERIODE : <?=$dateinfo;?></div>
        </center></h1><br>
    <center></center><br>
<?php //}?>
    <table class="head">
        <tr>
            <td width="150" style="border:0px">Unit Kerja</td>
            <td style="border:0px">: <?php echo $data ?></td>
            <td width="150" style="border:0px"></td>
            <td style="border:0px"></td>
            <td style="border:0px">&nbsp;</td>
        </tr>
    </table>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIP</th>
            <th>NPWP</th>
            <th>GOL</th>
            <th>Kelas Jabatan</th>
            <th>Tunjangan Kinerja</th>
            <th>Tunjangan PLT/PLH</th>
            <th>Total Pengurangan</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $x=1; $totaltun=0; $tottotaltun=0; $totalan = 0;
        $totaltunplt=0; $tottotaltunplt=0; $totalanplt = 0;
        $totalpengurang=0;$grandtotal=0;
        foreach($empinfo as $row) {

            $kelas = $row['kelasjabatan']==0?'':$row['kelasjabatan'];
            $tunjangan = ceil(round($row['tunjangan'],2));
            $totaltunjangan = ceil(round($row['totaltunjangan'],2));
            $totaltunjangan= $totaltunjangan> $tunjangan?$tunjangan :$totaltunjangan;


            $tunjanganplt = ceil(round($row['tunjanganplt'],2));
            $totaltunjanganplt = ceil(round($row['totaltunjanganplt'],2));
            $totaltunjanganplt = $totaltunjanganplt>$tunjanganplt?$tunjanganplt:$totaltunjanganplt;
            $totalplt = $tunjanganplt-$totaltunjanganplt;

            if ($row['isplt']!=1)
            {
                $tunjanganplt = 0;
                $totaltunjanganplt = 0;
                $totalplt = 0;
            } else {
                if ( ($row['kriteriaPlt']==1) ||($row['kriteriaPlt']==2)){
                    if ($row['dipisah']==1)
                    {
                        $tunjanganplt = 0;
                        $totaltunjanganplt = 0;
                        $totalplt = 0;
                    }
                    if ($row['dipisah']==2)
                    {
                        $tunjangan = 0;
                        $totaltunjangan = 0;
                    }

                }
                if ( ($row['kriteriaPlt']==3))
                {
                    $tunjangan = 0;
                    $totaltunjangan = 0;

                    $kelas = $row['plt_kelasjabatan']==0?'':$row['plt_kelasjabatan'];
                    if ($row['dipisah']==1)
                    {
                        $tunjangan = 0;
                        $totaltunjangan = 0;
                    }
                    if ($row['dipisah']==2)
                    {
                        $tunjangan = 0;
                        $totaltunjangan = 0;
                    }
                }
                if ( ($row['ishidden']==1)) {
                    $tunjangan = 0;
                    $totaltunjangan = 0;

                    $tunjanganplt = 0;
                    $totaltunjanganplt = 0;
                    $totalplt = 0;
                }
            }

            $pengurang= $totaltunjangan+$totaltunjanganplt;

            $total = ($tunjangan+$tunjanganplt)-$pengurang;

            ?>
            <tr class="<?php if($x % 2 === 0) echo 'even'; else echo 'odd'; ?>">
                <td><?php echo $x ?></td>

                <td><?php echo $row['empName']; ?></td>
                <td class="text"><?php echo "&nbsp;".$row['userid']; ?></td>
                <td><?php echo $row['npwp']; ?></td>
                <td><?php echo $row['golongan']; ?></td>
                <td align="center" class="num"><?php echo $kelas?></td>
                <td align="right" class="num"><?php echo $excelid==1?$tunjangan:number_format($tunjangan,2,'.',','); ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$tunjanganplt:number_format($tunjanganplt,2,'.',','); ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$pengurang:number_format($pengurang,2,'.',','); ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$total:number_format($total,2,'.',','); ?></td>
            </tr>
            <?php
            $totaltun = $totaltun + $tunjangan;
            $tottotaltun = $tottotaltun + $totaltunjangan;

            $totaltunplt = $totaltunplt + $tunjanganplt;
            $tottotaltunplt = $tottotaltunplt + $totaltunjanganplt;

            $totalpengurang = $totalpengurang+$pengurang;

            $grandtotal = $grandtotal +$total;
            $x++; } ?>
        <tr>
            <td colspan=6 align="center"><b>Total</b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$totaltun:number_format($totaltun,2,'.',','); ?></b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$totaltunplt:number_format($totaltunplt,2,'.',','); ?></b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$totalpengurang:number_format($totalpengurang,2,'.',','); ?></b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$grandtotal:number_format($grandtotal,2,'.',','); ?></b></td>
        </tr>
    </table>
<?php if ($excelid == 0 ) {?>
    <br><br>
    <div style="page-break-after:always"></div>
<?php }?>