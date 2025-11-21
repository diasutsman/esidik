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
        <title>Laporan Keuangan Tunjangan Kinerja</title>
    </head>

    <body>
<?php } ?>
    <style> .phone{ mso-number-format:\@; } </style>
    <table class="head" width="100%">
        <tr>
            <td align="center"  style="border:0px"><h1><?php echo $cominfo['companyname']; ?></h1></td>
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
            <?php echo "LAPORAN KEUANGAN TUNJANGAN KINERJA"; ?>
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
            <th rowspan="2">No</th>
            <th rowspan="2">NIP</th>
            <th rowspan="2">Nama</th>
            <th rowspan="2">NPWP</th>
            <th rowspan="2">Gol</th>
            <th rowspan="2">Kelas Jabatan</th>
            <th rowspan="2">Nilai</th>
            <th rowspan="2">Nomor Rekening</th>
            <th colspan="10" align="center" >Potongan</th>

        </tr>
        <tr>
            <th>Terlambat / Tidak Absen Datang</th>
            <th>Pulang Cepat</th>
            <th>Izin</th>
            <th>Cuti > 5</th>
            <th>Alpha</th>
            <th>TB</th>
            <th>Jam Kerja < 7.5 Jam</th>
            <th>Jumlah Potongan</th>
            <th>Jumlah Dibayarkan</th>
            <th>Pajak</th>
        </tr>
        <tr>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
            <th>6</th>
            <th>7</th>
            <th>8</th>
            <th>9</th>
            <th>10</th>
            <th>11</th>
            <th>12</th>
            <th>13</th>
            <th>14</th>
            <th>15</th>
            <th>16</th>
            <th>17</th>
            <th>18</th>
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

            $telat = ceil(round($row['potterlambat'],2));
            $psw  = ceil(round($row['potpsw'],2));
            $ijin  = ceil(round($row['potijin'],2));
            $alpa  = ceil(round($row['potalpa'],2));
            $cuti  = ceil(round($row['potcuti'],2));
            $tb  = ceil(round($row['pottb'],2));
            $jam  = ceil(round($row['potjam'],2));

            $pengurang= $totaltunjangan+$totaltunjanganplt;

            $total = ($tunjangan+$tunjanganplt)-$pengurang;

            ?>
            <tr class="<?php if($x % 2 === 0) echo 'even'; else echo 'odd'; ?>">
                <td><?php echo $x ?></td>
                <td class="text"><?php echo "&nbsp;".$row['userid']; ?></td>
                <td><?php echo $row['empName']; ?></td>
                <td><?php echo $row['npwp']; ?></td>
                <td><?php echo $row['golongan']; ?></td>
                <td><?php echo $kelas; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$tunjangan:number_format($tunjangan,2,'.',',');; ?></td>
                <td><?php echo $row['no_rekening']; ?></td>
                <!--<td align="center" class="num"><?php /*echo $kelas*/?></td>
                <td align="right" class="num"><?php /*echo $excelid==1?$tunjangan:number_format($tunjangan,2,'.',','); */?></td>
                <td align="right" class="num"><?php /*echo $excelid==1?$tunjanganplt:number_format($tunjanganplt,2,'.',','); */?></td>
                <td align="right" class="num"><?php /*echo $excelid==1?$pengurang:number_format($pengurang,2,'.',','); */?></td>
                <td align="right" class="num"><?php /*echo $excelid==1?$total:number_format($total,2,'.',','); */?></td>-->
                <td align="right" class="num"><?php echo $excelid==1?$telat:number_format($telat,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$psw:number_format($psw,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$ijin:number_format($ijin,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$cuti:number_format($cuti,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$alpa:number_format($alpa,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$tb:number_format($tb,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$jam:number_format($jam,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$pengurang:number_format($pengurang,2,'.',','); ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$total:number_format($total,2,'.',','); ?></td>
                <td align="right" class="num"></td>
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
            <!--<td align="right" class="num"><b><?php /*echo $excelid==1?$totaltunplt:number_format($totaltunplt,2,'.',','); */?></b></td>
            <td align="right" class="num"><b><?php /*echo $excelid==1?$totalpengurang:number_format($totalpengurang,2,'.',','); */?></b></td>
            <td align="right" class="num"><b><?php /*echo $excelid==1?$grandtotal:number_format($grandtotal,2,'.',','); */?></b></td>-->
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$pengurang:number_format($totalpengurang,2,'.',','); ?></b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$total:number_format($grandtotal,2,'.',','); ?></b></td>
            <td align="right" class="num"></td>
        </tr>
    </table>
<?php if ($excelid == 0 ) {?>
    <br><br>
    <div style="page-break-after:always"></div>
<?php }?>