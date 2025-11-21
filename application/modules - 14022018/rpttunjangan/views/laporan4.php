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
            <?php echo "LAPORAN POTONGAN TUNJANGAN KINERJA"; ?>
            <br><div >PERIODE : <?=$dateinfo;?></div>
        </h1>
</center><br>
    <center></center><br>
<?php //}?>
    <table class="head">
        <tr>
            <td width="150" style="border:0px" class="text">Unit Kerja</td>
            <td style="border:0px" class="text">: <?php echo $data ?></td>
            <td width="150" style="border:0px" class="text"></td>
            <td style="border:0px"></td>
            <td style="border:0px">&nbsp;</td>
        </tr>
    </table>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>

        <tr>
            <th rowspan="2" class="text">No</th>
            <th rowspan="2" class="text">NIP</th>
            <th rowspan="2" class="text">Nama</th>
            <th rowspan="2" class="text">NPWP</th>
            <th rowspan="2" class="text">Gol</th>
            <th rowspan="2" class="text">KELAS JABATAN</th>
            <th rowspan="2" class="text">JUMLAH</th>
            <th rowspan="2" class="text">REKENING</th>
            <th colspan="5" align="center"  class="text">POTONGAN</th>
            <th class="text" rowspan="2">JUMLAH POTONGAN </th>
            <th class="text" rowspan="2">JUMLAH TUNKIR FINGER PRINT</th>
            <th class="text" rowspan="2">CAPAIAN KINERJA</th>
            <th class="text" rowspan="2">JUMLAH DIBAYARKAN</th>
            <th class="text" >PPn IV</th>
            <th class="text" >15%</th>
        </tr>
        <tr>
            <th class="text">TERLAMBAT </th>
            <th class="text">CEPAT PULANG </th>
            <!--<th class="text">IZIN / SAKIT</th>-->
            <!--<th class="text">CLTN</th>-->
            <th class="text">ALPA</th>
            <th class="text">TB</th>
            <th class="text">TIDAK ABSEN DATANG/PULANG</th>
            <th class="text" >PPn III</th>
            <th class="text" >5%</th>
        </tr>

        </thead>
        <tbody>
        <?php
        $x=1; $totaltun=0; $tottotaltun=0; $totalan = 0;
        $totaltunplt=0; $tottotaltunplt=0; $totalanplt = 0;
        $totalpengurang=0;$grandtotal=0; $totalsikerja=0;$totalThp=0;$totalPpn=0;

        foreach($empinfo as $row) {

            $kelas = $row['kelasjabatan']==0?'':$row['kelasjabatan'];
            $tunjangan = ceil(round($row['tunjangan'],2));
            $totaltunjangan = ceil(round($row['totaltunjangan'],2));
            $totaltunjangan= $totaltunjangan> $tunjangan?$tunjangan :$totaltunjangan;


            $tunjanganplt = ceil(round($row['tunjanganplt'],2));
            $totaltunjanganplt = ceil(round($row['totaltunjanganplt'],2));
            $totaltunjanganplt = $totaltunjanganplt>$tunjanganplt?$tunjanganplt:$totaltunjanganplt;
            $totalplt = $tunjanganplt-$totaltunjanganplt;
            $nsikerja=$row['jmlsikerja'];

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
            $tdkabsen  = ceil(round($row['pottdkabsen'],2));

            $pengurang= $totaltunjangan+$totaltunjanganplt;

            $total = ($tunjangan+$tunjanganplt)-$pengurang;


            $nPot=0;
            $gl=explode('/',$row['golongan']);
            switch ($gl[0])
            {
                case "IV":
                    $nPot=0.015;
                    break;
                case "III":
                    $nPot=0.005;
                    break;
            }

            $thp=$total+$nsikerja;
            $nPPn= ($total+$nsikerja)*$nPot;
            $totalsikerja +=$nsikerja;
            $totalThp +=$thp;
            $totalPpn +=$nPPn;
            ?>
            <tr >
                <td class="text"><?php echo $x ?></td>
                <td class="text"><?php echo "&nbsp;".$row['userid']; ?></td>
                <td class="text"><?php echo $row['empName']; ?></td>
                <td class="text"><?php echo $row['npwp']; ?></td>
                <td class="text"><?php echo $row['golongan']; ?></td>
                <td class="text" align="center"><?php echo $kelas; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$tunjangan:number_format($tunjangan,2,'.',',');; ?></td>
                <td><?php echo $row['no_rekening']; ?></td>
                <!--<td align="center" class="num"><?php /*echo $kelas*/?></td>
                <td align="right" class="num"><?php /*echo $excelid==1?$tunjangan:number_format($tunjangan,2,'.',','); */?></td>
                <td align="right" class="num"><?php /*echo $excelid==1?$tunjanganplt:number_format($tunjanganplt,2,'.',','); */?></td>
                <td align="right" class="num"><?php /*echo $excelid==1?$pengurang:number_format($pengurang,2,'.',','); */?></td>
                <td align="right" class="num"><?php /*echo $excelid==1?$total:number_format($total,2,'.',','); */?></td>-->
                <td align="right" class="num"><?php echo $excelid==1?$telat:number_format($telat,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$psw:number_format($psw,2,'.',',');; ?></td>
                <!--<td align="right" class="num"><?php /*echo $excelid==1?$ijin:number_format($ijin,2,'.',',');; */?></td>-->
                <!--<td align="right" class="num"><?php /*echo $excelid==1?$cuti:number_format($cuti,2,'.',',');; */?></td>-->
                <td align="right" class="num"><?php echo $excelid==1?$alpa:number_format($alpa,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$tb:number_format($tb,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$tdkabsen:number_format($tdkabsen,2,'.',',');; ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$pengurang:number_format($pengurang,2,'.',','); ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$total:number_format($total,2,'.',','); ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$nsikerja:number_format($nsikerja,2,'.',','); ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$total:number_format($thp,2,'.',','); ?></td>
                <td align="right" class="num"><?php echo $excelid==1?$total:number_format($nPPn,2,'.',','); ?></td>
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
            <td colspan=6 align="center" class="text"><b>Total</b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$totaltun:number_format($totaltun,2,'.',','); ?></b></td>
            <!--<td align="right" class="num"><b><?php /*echo $excelid==1?$totaltunplt:number_format($totaltunplt,2,'.',','); */?></b></td>
            <td align="right" class="num"><b><?php /*echo $excelid==1?$totalpengurang:number_format($totalpengurang,2,'.',','); */?></b></td>
            <td align="right" class="num"><b><?php /*echo $excelid==1?$grandtotal:number_format($grandtotal,2,'.',','); */?></b></td>-->
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <td align="right" class="num"></td>
            <!--<td align="right" class="num"></td>-->
            <!--<td align="right" class="num"></td>-->
            <td align="right" class="num"></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$pengurang:number_format($totalpengurang,2,'.',','); ?></b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$total:number_format($grandtotal,2,'.',','); ?></b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$totalsikerja:number_format($totalsikerja,2,'.',','); ?></b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$totalThp:number_format($totalThp,2,'.',','); ?></b></td>
            <td align="right" class="num"><b><?php echo $excelid==1?$totalPpn:number_format($totalPpn,2,'.',','); ?></b></td>
            <td align="right" class="num"></td>
        </tr>
    </table>
<?php if ($excelid == 0 ) {?>
    <br><br>
    <div style="page-break-after:always"></div>
<?php }?>