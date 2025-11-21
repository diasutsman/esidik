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
            <?php echo "LAPORAN REKAPITULASI TUNJANGAN KINERJA"; ?>
            <br><div >PERIODE : <?=$dateinfo;?></div>
        </h1></center><br>
    <br>
<?php //}?>
    <table class="head">
        <tr>
            <td width="150" style="border:0px" class="text">Unit Kerja</td>
            <td style="border:0px" class="text">: <?php echo $data ?></td>
            <td width="150" style="border:0px"></td>
            <td style="border:0px"></td>
            <td style="border:0px">&nbsp;</td>
        </tr>
    </table>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="text">No</th>
            <th class="text">Nama</th>
            <th class="text">NIP</th>
            <th class="text">NPWP</th>
            <th class="text">GOL</th>
            <th class="text">Kelas Jabatan</th>
            <th class="text">Tunjangan Kinerja</th>
            <th class="text">Tunjangan PLT/PLH</th>
            <th class="text">Total Pengurangan</th>
            <th class="text">Total</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $x=1; $totaltun=0; $tottotaltun=0; $totalan = 0;
        $totaltunplt=0; $tottotaltunplt=0; $totalanplt = 0;
        $totalpengurang=0;$grandtotal=0;
        foreach($empinfo as $row) {

            $kelas = $row['kelasjabatan'] == 0 ? '' : $row['kelasjabatan'];
            $tunjangan = ceil(round($row['tunjangan'], 2));
            $totaltunjangan = ceil(round($row['totaltunjangan'], 2));
            $totaltunjangan = $totaltunjangan > $tunjangan ? $tunjangan : $totaltunjangan;


            $tunjanganplt = ceil(round($row['tunjanganplt'], 2));
            $totaltunjanganplt = ceil(round($row['totaltunjanganplt'], 2));
            $totaltunjanganplt = $totaltunjanganplt > $tunjanganplt ? $tunjanganplt : $totaltunjanganplt;
            $totalplt = $tunjanganplt - $totaltunjanganplt;

            if ($isViewIt) {
                if ($row['isplt'] != 1) {
                    $tunjanganplt = 0;
                    $totaltunjanganplt = 0;
                    $totalplt = 0;
                } else {
                    if (($row['kriteriaPlt'] == 1) || ($row['kriteriaPlt'] == 2)) {
                        if ($row['dipisah'] == 1) {
                            $tunjanganplt = 0;
                            $totaltunjanganplt = 0;
                            $totalplt = 0;
                        }
                        if ($row['dipisah'] == 2) {
                            $tunjangan = 0;
                            $totaltunjangan = 0;
                        }

                        if (($row['kriteriaPlt'] == 2)) {
                            $kelas = $row['plt_kelasjabatan'] > $row['kelasjabatan'] ? $row['plt_kelasjabatan'] : $row['kelasjabatan'];
                        }

                    }
                    if (($row['kriteriaPlt'] == 3)) {
                        $tunjangan = 0;
                        $totaltunjangan = 0;

                        $kelas = $row['plt_kelasjabatan'] == 0 ? '' : $row['plt_kelasjabatan'];
                        if ($row['dipisah'] == 1) {
                            $tunjangan = 0;
                            $totaltunjangan = 0;
                        }
                        if ($row['dipisah'] == 2) {
                            $tunjangan = 0;
                            $totaltunjangan = 0;
                        }

                    }
                    if (($row['ishidden'] == 1)) {
                        $tunjangan = 0;
                        $totaltunjangan = 0;

                        $tunjanganplt = 0;
                        $totaltunjanganplt = 0;
                        $totalplt = 0;
                    }
                }

                $pengurang = $totaltunjangan + $totaltunjanganplt;

                $total = ($tunjangan + $tunjanganplt) - $pengurang;

                ?>
                <tr>
                    <td class="text"><?php echo $x ?></td>

                    <td class="text"><?php echo $row['empName']; ?></td>
                    <td class="text"><?php echo "&nbsp;" . $row['userid']; ?></td>
                    <td class="text"><?php echo $row['npwp']; ?></td>
                    <td class="text"><?php echo $row['golongan']; ?></td>
                    <td align="center" class="num"><?php echo $kelas ?></td>
                    <td align="right"
                        class="num"><?php echo $excelid == 1 ? $tunjangan : number_format($tunjangan, 2, '.', ','); ?></td>
                    <td align="right"
                        class="num"><?php echo $excelid == 1 ? $tunjanganplt : number_format($tunjanganplt, 2, '.', ','); ?></td>
                    <td align="right"
                        class="num"><?php echo $excelid == 1 ? $pengurang : number_format($pengurang, 2, '.', ','); ?></td>
                    <td align="right"
                        class="num"><?php echo $excelid == 1 ? $total : number_format($total, 2, '.', ','); ?></td>
                </tr>
                <?php
                $totaltun = $totaltun + $tunjangan;
                $tottotaltun = $tottotaltun + $totaltunjangan;

                $totaltunplt = $totaltunplt + $tunjanganplt;
                $tottotaltunplt = $tottotaltunplt + $totaltunjanganplt;

                $totalpengurang = $totalpengurang + $pengurang;

                $grandtotal = $grandtotal + $total;
                $x++;
            }
        }?>
        <tr>
            <td colspan=6 align="center" class="text"><b>Total</b></td>
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