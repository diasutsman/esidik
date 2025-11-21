<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
//echo $isViewIt."-".$empinfo['userid']."<br>";
//if ($isViewIt) {

    $clss = '';
    ?>
    <?php if ($excelid == 0) { ?>
        <html xmlns="http://www.w3.org/1999/xhtml">

        <head>
            <style>
                <?php include FCPATH."/assets/css/print.css"; ?>
            </style>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <title>Laporan Tunjangan Kinerja</title>
        </head>

        <body><?php } ?>
<div style="page-break-after:always">
    <style> .num {
            mso-number-format: General;
        }

        .text {
            mso-number-format: "\@"; /*force text*/
        }
        .ttd {
            border-style: none;
        }
    </style>

    <center>
        <h1><?php echo $cominfo["companyname"]; ?></h1>
        <p align="center"><?php echo $cominfo["address1"]; ?><br></p>
        <p align="center">Telepon: <?php echo $cominfo["phone"]; ?>, Faks: <?php echo $cominfo["fax"]; ?></p>
        <hr/>
        <h1>
            <?php echo "LAPORAN TUNJANGAN KINERJA<br/>BERDASARKAN DISIPLIN KERJA"; ?>
            <div>PERIODE : <?php echo $dateinfo; ?></div>
        </h1>
    </center>
    <br>
    <br>
    <table class="head">
        <?php
        //echo $empinfo['userid'].' '.$empinfo['kriteriaPlt'].' '.$dipisah.' '.$ishidden;

        if ($byuser == 1) {
            //echo $byuser." Kriteria: ".$empinfo['kriteriaPlt']." dipisah: ".$dipisah." unit: ".$empinfo['orgf'];
            if (($empinfo['isplt'] == 1)) {
                ?>
                <tr>
                    <td width="150" style="border:0px" class="text">ID</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['userid']; ?></td>
                    <td width="150" style="border:0px" class="text">Unit Kerja</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['deptName']; ?></td>
                    <td style="border:0px" class="text">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">NIP</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empID']; ?></td>
                    <td width="150" style="border:0px" class="text">Jabatan</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empTitle']; ?></td>
                    <td style="border:0px" class="text">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">Nama</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empName']; ?></td>
                    <td width="150" style="border:0px" class="text">Kelas Jabatan</td>
                    <td style="border:0px" class="text">
                        : <?php echo $empinfo['kelasjabatan'] == 0 ? '' : $empinfo['kelasjabatan']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"></td>
                    <td style="border:0px"></td>
                    <td width="150" style="border:0px" class="text">Unit Kerja PLT/PLH&nbsp;</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['plt_deptname']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"></td>
                    <td style="border:0px"></td>
                    <td width="150" style="border:0px" class="text">Jabatan PLT/PLH&nbsp</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['plt_jbtn']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"></td>
                    <td style="border:0px"></td>
                    <td width="150" style="border:0px" class="text">Kelas Jabatan</td>
                    <td style="border:0px" class="text">
                        : <?php echo $empinfo['plt_kelasjabatan'] == 0 ? '' : $empinfo['plt_kelasjabatan']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <?php

            } else {
                ?>
                <tr>
                    <td width="150" style="border:0px" class="text">ID</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['userid']; ?></td>
                    <td width="150" style="border:0px" class="text">Unit Kerja</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['deptName']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">NIP</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empID']; ?></td>
                    <td width="150" style="border:0px" class="text">Jabatan</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empTitle']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">Nama</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empName']; ?></td>
                    <td width="150" style="border:0px" class="text">Kelas Jabatan</td>
                    <td style="border:0px" class="text">
                        : <?php echo $empinfo['kelasjabatan'] == 0 ? '' : $empinfo['kelasjabatan']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
            <?php }
            if ($empinfo['isplt'] == 1) {

                if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {
                    //dipisah

                    $pengurangan = (ceil(round($footah['total'], 2)) > ceil(round($empinfo['tunjangan'], 2))) ? ceil(round($empinfo['tunjangan'], 2)) : ceil(round($footah['total'], 2));
                    $tunjangan = $empinfo['tunjangan'];
                    $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));

                    $penguranganplt = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                    $tunjanganplt = $empinfo['tunjanganplt'];
                    $totltunkplt = (ceil(round($tunjanganplt, 2)) - ceil(round($penguranganplt, 2)));
                    //$pengurangan=
                    $grandttl = $totltunk + $totltunkplt;
                    ?>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Tunjangan Kinerja </b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') : $excelid == 1 ? ceil(round($empinfo['tunjangan'])) : number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>

                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Pengurangan Tunj.Kinerja</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($pengurangan, 0, ',', '.') : $excelid == 1 ? ceil(round($pengurangan, 2)) : number_format($pengurangan, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="150" style="border:0px"><b>Total Tunjangan Kinerja</b></td>
                        <td style="border:0px"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right">
                            <b><?php echo $showpdf == 1 ? number_format($totltunk, 0, ',', '.') : $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Tunjangan PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format(ceil(round($empinfo['tunjanganplt'], 2)), 0, ',', '.') : $excelid == 1 ? (ceil(round($empinfo['tunjanganplt'], 2))) : number_format(ceil(round($empinfo['tunjanganplt'], 2)), 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Pengurangan Tunj.PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($penguranganplt, 0, ',', '.') : $excelid == 1 ? ceil(round($penguranganplt, 2)) : number_format($penguranganplt, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Total Tunj.PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($totltunkplt, 0, ',', '.') : $excelid == 1 ? $totltunkplt : number_format($totltunkplt, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Grand Total Tunjangan Kinerja</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format(ceil(round($grandttl, 2)), 0, ',', '.') : $excelid == 1 ? (ceil(round($grandttl, 2))) : number_format(ceil(round($grandttl, 2)), 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <?php
                } else {
                    $pengurangan = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                    $tunjangan = $empinfo['tunjanganplt'];
                    $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));
                    ?>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Tunjangan Kinerja PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format(ceil(round($tunjangan, 2)), 0, ',', '.') : $excelid == 1 ? ceil(round($tunjangan)) : number_format(ceil(round($tunjangan, 2)), 0, ',', '.') ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>

                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Total Pengurangan</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($pengurangan, 0, ',', '.') : $excelid == 1 ? ceil(round($pengurangan, 2)) : number_format($pengurangan, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Total Tunjangan Kinerja</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($totltunk, 0, ',', '.') : $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td width="150" style="border:0px" class="text"><b>Tunjangan
                            Kinerja <?php echo ($empinfo['kriteriaPlt'] == 3) ? "PLT/PLH" : "" ?></b></td>
                    <td style="border:0px" class="text"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right" class="num">
                        <b><?php echo $showpdf == 1 ? number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') : $excelid == 1 ? ceil(round($empinfo['tunjangan'])) : number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') ?></b>
                    </td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <?php

                $pengurangan = (ceil(round($footah['total'], 2)) > ceil(round($empinfo['tunjangan'], 2))) ? ceil(round($empinfo['tunjangan'], 2)) : ceil(round($footah['total'], 2));
                $tunjangan = $empinfo['tunjangan'];
                $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));
                ?>
                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px" class="text"><b>Total Pengurangan</b></td>
                    <td style="border:0px" class="text"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right" class="num">
                        <b><?php echo $showpdf == 1 ? number_format($pengurangan, 0, ',', '.') : $excelid == 1 ? $pengurangan : number_format($pengurangan, 0, ',', '.'); ?></b>
                    </td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text"><b>Total Tunjangan Kinerja</b></td>
                    <td style="border:0px" class="text"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right" class="num">
                        <b><?php echo $showpdf == 1 ? number_format($totltunk, 0, ',', '.') : $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b>
                    </td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <?php
            }
        } //end byUser
        else {
            if (($empinfo['kriteriaPlt'] == 2) || ($empinfo['kriteriaPlt'] == 1)) {
                if ($dipisah == 2) {
                    $dpename = $empinfo['plt_deptname'];
                    $dpename2 = $empinfo['plt_jbtn'];
                    $dpename3 = $empinfo['plt_kelasjabatan'] == 0 ? '' : $empinfo['plt_kelasjabatan'];
                } else {
                    $dpename = $empinfo['deptName'];
                    $dpename2 = $empinfo['empTitle'];
                    $dpename3 = $empinfo['kelasjabatan'] == 0 ? '' : $empinfo['kelasjabatan'];
                }
                ?>
                <tr>
                    <td width="150" style="border:0px" class="text">ID</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['userid']; ?></td>
                    <td width="150" style="border:0px">Unit Kerja</td>
                    <td style="border:0px" class="text">: <?php echo $dpename; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">NIP</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empID']; ?></td>
                    <td width="150" style="border:0px" class="text">Jabatan</td>
                    <td style="border:0px" class="text">: <?php echo $dpename2 ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">Nama</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empName']; ?></td>
                    <td width="150" style="border:0px" class="text">Kelas Jabatan</td>
                    <td style="border:0px" class="text">: <?php echo $dpename3 ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <?php if ($dipisah == 0) { ?>
                    <tr>
                        <td width="150" style="border:0px"></td>
                        <td style="border:0px"></td>
                        <td width="150" style="border:0px" class="text">Unit Kerja PLT/PLH&nbsp;</td>
                        <td style="border:0px" class="text">: <?php echo $empinfo['plt_deptname']; ?></td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="150" style="border:0px"></td>
                        <td style="border:0px"></td>
                        <td width="150" style="border:0px" class="text">Jabatan PLT/PLH&nbsp</td>
                        <td style="border:0px" class="text">: <?php echo $empinfo['plt_jbtn']; ?></td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="150" style="border:0px"></td>
                        <td style="border:0px"></td>
                        <td width="150" style="border:0px" class="text">Kelas Jabatan</td>
                        <td style="border:0px" class="text">
                            : <?php echo $empinfo['plt_kelasjabatan'] == 0 ? '' : $empinfo['plt_kelasjabatan']; ?></td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                <?php } ?>
                <?php
                $pengurangan = (ceil(round($footah['total'], 2)) > ceil(round($empinfo['tunjangan'], 2))) ? ceil(round($empinfo['tunjangan'], 2)) : ceil(round($footah['total'], 2));
                $tunjangan = $empinfo['tunjangan'];
                $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));

                $penguranganplt = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                $tunjanganplt = $empinfo['tunjanganplt'];
                $totltunkplt = (ceil(round($tunjanganplt, 2)) - ceil(round($penguranganplt, 2)));

                $grandttl = $totltunk + $totltunkplt;

                if ($dipisah == 2) {
                    $grandttl = $totltunkplt;
                } else if ($dipisah == 1) {
                    $grandttl = $totltunk;
                } else {
                    $grandttl = $totltunkplt + $totltunk;
                }
                if ($dipisah == 1) {
                    ?>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Tunjangan Kinerja </b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') : $excelid == 1 ? ceil(round($empinfo['tunjangan'])) : number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>

                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Pengurangan Tunj.Kinerja</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($pengurangan, 0, ',', '.') : $excelid == 1 ? ceil(round($pengurangan, 2)) : number_format($pengurangan, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Total Tunjangan Kinerja</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($totltunk, 0, ',', '.') : $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                <?php }
                if ($dipisah == 2) {
                    ?>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Tunjangan PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format(ceil(round($empinfo['tunjanganplt'], 2)), 0, ',', '.') : $excelid == 1 ? (ceil(round($empinfo['tunjanganplt'], 2))) : number_format(ceil(round($empinfo['tunjanganplt'], 2)), 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Pengurangan Tunj.PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($penguranganplt, 0, ',', '.') : $excelid == 1 ? ceil(round($penguranganplt, 2)) : number_format($penguranganplt, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Total Tunj.PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($totltunkplt, 0, ',', '.') : $excelid == 1 ? $totltunkplt : number_format($totltunkplt, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                <?php }
                if ($dipisah == 0) {
                    ?>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Tunjangan Kinerja </b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') : $excelid == 1 ? ceil(round($empinfo['tunjangan'])) : number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>

                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Pengurangan Tunj.Kinerja</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($pengurangan, 0, ',', '.') : $excelid == 1 ? ceil(round($pengurangan, 2)) : number_format($pengurangan, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Total Tunjangan Kinerja</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($totltunk, 0, ',', '.') : $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Tunjangan PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format(ceil(round($empinfo['tunjanganplt'], 2)), 0, ',', '.') : $excelid == 1 ? (ceil(round($empinfo['tunjanganplt'], 2))) : number_format(ceil(round($empinfo['tunjanganplt'], 2)), 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Pengurangan Tunj.PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($penguranganplt, 0, ',', '.') : $excelid == 1 ? ceil(round($penguranganplt, 2)) : number_format($penguranganplt, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <tr style="border-bottom:2px solid">
                        <td width="150" style="border:0px" class="text"><b>Total Tunj.PLT/PLH</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format($totltunkplt, 0, ',', '.') : $excelid == 1 ? $totltunkplt : number_format($totltunkplt, 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                <?php } ?>
                <?php
                if ($dipisah == 0) {
                    ?>
                    <tr>
                        <td width="150" style="border:0px" class="text"><b>Grand Total Tunjangan Kinerja</b></td>
                        <td style="border:0px" class="text"><b>: Rp. </b></td>
                        <td width="150" style="border:0px" align="right" class="num">
                            <b><?php echo $showpdf == 1 ? number_format(ceil(round($grandttl, 2)), 0, ',', '.') : $excelid == 1 ? (ceil(round($grandttl, 2))) : number_format(ceil(round($grandttl, 2)), 0, ',', '.'); ?></b>
                        </td>
                        <td style="border:0px">&nbsp;</td>
                        <td style="border:0px">&nbsp;</td>
                    </tr>
                    <?php
                }
            } else if (($empinfo['kriteriaPlt'] == 3) && ($ishidden)) {
                ?>
                <tr>
                    <td width="150" style="border:0px" class="text">ID</td>
                    <td style="border:0px" class="text" class="text">: <?php echo $empinfo['userid']; ?></td>
                    <td width="150" style="border:0px" class="text">Unit Kerja</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['deptName']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">NIP</td>
                    <td style="border:0px" class="text" class="text">: <?php echo $empinfo['empID']; ?></td>
                    <td width="150" style="border:0px" class="text">Jabatan</td>
                    <td style="border:0px" class="text">:
                    <?php echo $empinfo['empTitle']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">Nama</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empName']; ?></td>
                    <td width="150" style="border:0px" class="text">Kelas Jabatan</td>
                    <td style="border:0px" class="text">
                        : <?php echo $empinfo['kelasjabatan'] == 0 ? '' : $empinfo['kelasjabatan']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"></td>
                    <td style="border:0px"></td>
                    <td width="150" style="border:0px" class="text">Unit Kerja PLT/PLH&nbsp;</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['plt_deptname']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"></td>
                    <td style="border:0px"></td>
                    <td width="150" style="border:0px" class="text">Jabatan PLT/PLH&nbsp</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['plt_jbtn']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"></td>
                    <td style="border:0px"></td>
                    <td width="150" style="border:0px" class="text">Kelas Jabatan</td>
                    <td style="border:0px" class="text">
                        : <?php echo $empinfo['plt_kelasjabatan'] == 0 ? '' : $empinfo['plt_kelasjabatan']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text"><b>Tunjangan Kinerja</b></td>
                    <td style="border:0px" class="text"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right" class="text"><b>-</b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px" class="text"><b>Total Pengurangan</b></td>
                    <td style="border:0px" class="text"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right" class="text"><b>-</b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text"><b>Total Tunjangan Kinerja</b></td>
                    <td style="border:0px" class="text"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right" class="text"><b>-</b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>

            <?php } else {
                ?>
                <tr>
                    <td width="150" style="border:0px" class="text">ID</td>
                    <td style="border:0px" class="text" class="text">: <?php echo $empinfo['userid']; ?></td>
                    <td width="150" style="border:0px"
                        class="text"><?php echo "Unit Kerja " . ($empinfo['kriteriaPlt'] == 3 ? "PLT/PLH" : ""); ?></td>
                    <td style="border:0px">: <?php
                        $namaorg = $empinfo['deptName'];
                        if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {

                            if ($dipisah == 2) {
                                $namaorg = $empinfo['plt_deptname'];
                            }
                        }
                        if ($empinfo['kriteriaPlt'] == 3) {
                            $namaorg = $empinfo['plt_deptname'];
                        }
                        echo $namaorg;
                        ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">NIP</td>
                    <td style="border:0px" class="text" class="text">: <?php echo $empinfo['empID']; ?></td>
                    <td width="150" style="border:0px"
                        class="text"><?php echo "Jabatan " . ($empinfo['kriteriaPlt'] == 3 ? "PLT/PLH" : "") ?></td>
                    <td style="border:0px">: <?php
                        $namajbtn = $empinfo['empTitle'];
                        if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {

                            if ($dipisah == 2) {
                                $namajbtn = $empinfo['plt_jbtn'];
                            }
                        }
                        if ($empinfo['kriteriaPlt'] == 3) {
                            $namajbtn = $empinfo['plt_jbtn'];
                        }
                        echo $namajbtn;
                        ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text">Nama</td>
                    <td style="border:0px" class="text">: <?php echo $empinfo['empName']; ?></td>
                    <td width="150" style="border:0px" class="text">Kelas
                        Jabatan <?php echo($empinfo['kriteriaPlt'] == 3 ? "PLT/PLH" : "") ?></td>
                    <td style="border:0px">:
                        <?php
                        $namakelas = $empinfo['kelasjabatan'];
                        if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {

                            if ($dipisah == 2) {
                                $namakelas = $empinfo['plt_kelasjabatan'];
                            }
                        }
                        if ($empinfo['kriteriaPlt'] == 3) {
                            $namakelas = $empinfo['plt_kelasjabatan'];
                        }
                        echo $namakelas;
                        ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <?php
                $pengurangan = (ceil(round($footah['total'], 2)) > ceil(round($empinfo['tunjangan'], 2))) ? ceil(round($empinfo['tunjangan'], 2)) : ceil(round($footah['total'], 2));
                $tunjangan = $empinfo['tunjangan'];
                $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));

                if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {

                    if ($dipisah == 2) {
                        $pengurangan = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                        $tunjangan = $empinfo['tunjanganplt'];
                        $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));
                    }
                }
                if ($empinfo['kriteriaPlt'] == 3) {
                    $pengurangan = (ceil(round($footah['totalplt'], 2)) > ceil(round($empinfo['tunjanganplt'], 2))) ? ceil(round($empinfo['tunjanganplt'], 2)) : ceil(round($footah['totalplt'], 2));
                    $tunjangan = $empinfo['tunjanganplt'];
                    $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));
                    //echo "no 3";
                }
                ?>
                <tr>
                    <td width="150" style="border:0px" class="text"><b>Tunjangan
                            Kinerja <?php echo ($empinfo['kriteriaPlt'] == 3) ? "PLT/PLH" : "" ?></b></td>
                    <td style="border:0px" class="text"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right" class="num">
                        <b><?php echo $showpdf == 1 ? number_format(ceil(round($tunjangan, 2)), 2, ',', '.') : $excelid == 1 ? ceil(round($tunjangan, 2)) : number_format(ceil(round($tunjangan, 2)), 2, ',', '.') ?></b>
                    </td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>

                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px" class="text"><b>Total Pengurangan</b></td>
                    <td style="border:0px" class="text"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right" class="num">
                        <b><?php echo $showpdf == 1 ? number_format($pengurangan, 2, ',', '.') : $excelid == 1 ? $pengurangan : number_format($pengurangan, 2, ',', '.'); ?></b>
                    </td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px" class="text"><b>Total Tunjangan Kinerja</b></td>
                    <td style="border:0px" class="text"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right" class="num">
                        <b><?php echo $showpdf == 1 ? number_format($totltunk, 2, ',', '.') : $excelid == 1 ? $totltunk : number_format($totltunk, 2, ',', '.'); ?></b>
                    </td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
            <?php } ?>

        <?php } ?>
        <tr>
            <td width="150" style="border:0px">&nbsp;</td>
            <td style="border:0px">&nbsp;</td>
            <td width="150" style="border:0px">&nbsp;</td>
            <td style="border:0px">&nbsp;</td>
            <td style="border:0px">&nbsp;</td>
        </tr>
        <?php
        if (!$ishidden) { ?>
            <tr>
                <td width="150" style="border:0px">Detail</td>
                <td style="border:0px">: &nbsp;</td>
                <td width="150" style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <?php
        } ?>
    </table>

        <?php
    if (!$ishidden) {
        if (($empinfo['tunjangan'] + $empinfo['tunjanganplt']) == 0) {
            echo '<table class="head">
				<tr>
					<td style="border:0px"><span style="color:red"  class="text">Tidak ada data Tunjangan Kinerja</span></td>
				</tr>
			</table>';
        } else {


            if (($empinfo['isplt'] == 1)) {

                $showhunkir = 0;
                $showhplt = 0;
                if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {
                    if ($dipisah == 2) {
                        $showhplt = 1;
                        $showhunkir = 0;
                    }
                    if ($dipisah == 1) {
                        $showhplt = 0;
                        $showhunkir = 1;
                    }
                    if ($dipisah == 0) {
                        $showhplt = 1;
                        $showhunkir = 1;
                    }
                }

                if ($empinfo['kriteriaPlt'] == 3) {
                    $showhplt = 1;
                    $showhunkir = 0;
                }
                ?>
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th width="100" class="text">Hari</th>
                        <th width="100" class="text">Tanggal</th>
                        <th class="text">Status</th>
                        <th width="130" class="text">Nilai</th>
                        <th width="50" class="text">Pengurangan</th>
                        <?php if ($showhplt == 1 || $byuser == 1) { ?>
                            <th width="130" class="text">PLT/PLH</th>
                        <?php } ?>
                        <?php if ($showhunkir == 1 || $byuser == 1) { ?>
                            <th width="130" class="text">Disiplin</th>
                        <?php } ?>
                        <th width="130" class="text">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
                    foreach ($data as $row) {
                        ?>
                        <tr>
                            <td class="text"><?php echo hariIndo($row['day']); ?></td>
                            <td class="text"><?php echo $row['date']; ?></td>
                            <td class="text"><?php echo $row['status']; ?></td>
                            <td align="right" class="text"><?php echo $row['nilai']; ?></td>
                            <td align="center"
                                class="text"><?php echo $showpdf == 1 ? number_format($row['pengurangan'], 2, ',', '.') : $excelid == 1 ? $row['pengurangan'] : number_format($row['pengurangan'], 2, ',', '.'); ?></td>
                            <?php
                            $nPlt = 0;
                            $nUnkir = 0;
                            $nTotalRow = 0;
                            $showplt = 0;
                            $showunkir = 1;
                            $nUnkir = $showpdf == 1 ? number_format(ceil(round($row['total'], 2)), 2, ',', '.') : $excelid == 1 ? ceil(round($row['total'], 2)) : number_format(ceil(round($row['total'], 2)), 2, ',', '.');
                            $nTotalRow = ceil(round($row['total'], 2)) + ceil(round($row['totalplt'], 2));
                            if ($empinfo['kriteriaPlt'] != 3) {
                                if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {
                                    $nPlt = $showpdf == 1 ? number_format(ceil(round($row['totalplt'], 2)), 2, ',', '.') : $excelid == 1 ? ceil(round($row['totalplt'], 2)) : number_format(ceil(round($row['totalplt'], 2)), 2, ',', '.');
                                    if ($dipisah == 2) {
                                        $showplt = 1;
                                        $showunkir = 0;
                                        $nTotalRow = ceil(round($row['totalplt'], 2));
                                    }
                                    if ($dipisah == 0) {
                                        $showplt = 1;
                                        $showunkir = 1;
                                        $nTotalRow = ceil(round($row['total'], 2)) + ceil(round($row['totalplt'], 2));
                                    }
                                    if ($dipisah == 1) {
                                        $showplt = 0;
                                        $showunkir = 1;
                                        $nTotalRow = ceil(round($row['total'], 2));
                                    }
                                }
                            } else {
                                $nPlt = $showpdf == 1 ? number_format(ceil(round($row['totalplt'], 2)), 2, ',', '.') : $excelid == 1 ? ceil(round($row['totalplt'], 2)) : number_format(ceil(round($row['totalplt'], 2)), 2, ',', '.');
                                $nUnkir = 0;
                                $showplt = 1;
                                $showunkir = 0;
                                $nTotalRow = ceil(round($row['totalplt'], 2));
                            }

                            $nTotalRow = $showpdf == 1 ? number_format($nTotalRow, 2, ',', '.') : $excelid == 1 ? $nTotalRow : number_format($nTotalRow, 2, ',', '.');
                            ?>
                            <?php if ($showplt == 1 || $byuser == 1) { ?>
                                <td align="right" class="num">
                                    <?php echo $nPlt ?>
                                </td>
                            <?php } ?>
                            <?php if ($showunkir == 1 || $byuser == 1) { ?>
                                <td align="right" class="num">
                                    <?php echo $nUnkir ?>
                                </td>
                            <?php } ?>
                            <td align="right" class="num">
                                <?php echo $nTotalRow ?>
                            </td>

                        </tr>
                        <?php $x++;
                    } ?>
                    </tbody>
                    <?php

                    $nfootplt = ceil(round($footah['totalplt'], 2));
                    $nfootunkir = ceil(round($footah['total'], 2));
                    $nfoottotal = $nfootplt + $nfootunkir;

                    //echo $empinfo['userid'].' '.$byuser.' '.$dipisah.' '.$empinfo['kriteriaPlt'].' '.$nfootplt.' '.$nfootunkir;

                    $footplt = $showpdf == 1 ? number_format($nfootplt, 2, ',', '.') : $excelid == 1 ? $nfootplt : number_format($nfootplt, 2, ',', '.');
                    $footunkir = $showpdf == 1 ? number_format($nfootunkir, 2, ',', '.') : $excelid == 1 ? $nfootunkir : number_format($nfootunkir, 2, ',', '.');
                    $foottotal = $showpdf == 1 ? number_format($nfoottotal, 2, ',', '.') : $excelid == 1 ? $nfoottotal : number_format($nfoottotal, 2, ',', '.');

                    if ($empinfo['kriteriaPlt'] != 3) {
                        if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {
                            if ($dipisah == 2) {
                                $showhplt = 1;
                                $showhunkir = 0;
                                $foottotal = $showpdf == 1 ? number_format($nfootplt, 2, ',', '.') : $excelid == 1 ? $nfootplt : number_format($nfootplt, 2, ',', '.');
                            }
                            if ($dipisah == 1) {
                                $showhplt = 0;
                                $showhunkir = 1;
                                $foottotal = $showpdf == 1 ? number_format($nfootunkir, 2, ',', '.') : $excelid == 1 ? $nfootunkir : number_format($nfootunkir, 2, ',', '.');
                            }
                        }
                    } else {
                        $nUnkir = 0;
                        $footunkir = 0;
                        $foottotal = $showpdf == 1 ? number_format($nfootplt, 2, ',', '.') : $excelid == 1 ? $nfootplt : number_format($nfootplt, 2, ',', '.');
                    }
                    //echo $foottotal;
                    ?>
                    <tr>
                        <td colspan=4 align="center" class="text"><b>Total</b></td>
                        <td align="center" class="num"><b><?php echo $footah['totalpersen'] ?></b></td>
                        <?php if ($showhplt == 1 || $byuser == 1) { ?>
                            <td align="right" class="text"><b><?php echo $footplt ?></b></td>
                        <?php } ?>
                        <?php if ($showhunkir == 1 || $byuser == 1) { ?>
                            <td align="right" class="text"><b><?php echo $footunkir ?></b></td>
                        <?php } ?>
                        <td align="right" class="text"><b><?php echo $foottotal ?></b></td>

                    </tr>
                </table>
            <?php } else { ?>

                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th width="100" class="text">Hari</th>
                        <th width="100" class="text">Tanggal</th>
                        <th class="text">Status</th>
                        <th width="130" class="text">Nilai</th>
                        <th width="50" class="text">Pengurangan</th>
                        <th width="130" class="text">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
                    foreach ($data as $row) {
                        ?>
                        <tr>
                            <td class="text"><?php echo hariIndo($row['day']); ?></td>
                            <td class="text"><?php echo $row['date']; ?></td>
                            <td class="text"><?php echo $row['status']; ?></td>
                            <td align="right" class="text"><?php echo $row['nilai']; ?></td>
                            <td align="center" class="num"><?php echo $row['pengurangan']; ?></td>
                            <td align="right"
                                class="num"><?php echo $showpdf == 1 ? number_format(ceil(round($row['total'], 2)), 2, ',', '.') : $excelid == 1 ? ceil(round($row['total'], 2)) : number_format(ceil(round($row['total'], 2)), 2, ',', '.'); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan=4 align="center" class="text"><b>Total</b></td>
                        <td align="center" class="num"><b><?php echo $footah['totalpersen'] ?></b></td>
                        <td align="right" class="num">
                            <b><?php echo $excelid == 1 ? ceil(round($footah['total'], 2)) : number_format(ceil(round($footah['total'], 2)), 2, ',', '.'); ?></b>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php
            }
            ?><?php
        }
    } ?>
    <!-- section ttd -->
    <div style="margin-top: 75px">
        <table border="0" style="border: none; width: 100%">
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr>

                <td width="20%" class="ttd"></td>
                <td width="20%" class="ttd"></td>
                <td width="20%" class="ttd"></td>
                <td colspan="3" width="35%" class="text ttd" style="padding-bottom: 100px;"><b><?php echo $ttd_jabatan; ?></b></td>
            </tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr>

                <td width="20%" class="ttd"></td>
                <td width="20%" class="ttd"></td>
                <td width="20%" class="ttd"></td>
                <td width="35%" class="text ttd"><b><?php echo  $ttd_nama; ?></b></td>
            </tr>
            <tr>
                <td width="20%" class="ttd"></td>
                <td width="20%" class="ttd"></td>
                <td width="20%" class="ttd"></td>
                <td width="35%" class="text ttd"><?php echo  $ttd_gol; ?></td>
            </tr>
            <tr>
                <td width="20%" class="ttd"></td>
                <td width="20%" class="ttd"></td>
                <td width="20%" class="ttd"></td>
                <td width="35%" class="text ttd">NIP. <?php echo  $ttd_nip; ?></td>
            </tr>
        </table>
    </div>
    <!-- end section ttd -->
</div>
    <?php if ($pdfid == 1) { ?>
        <pagebreak/>
    <?php } ?>
    <?php if ($excelid == 0) { ?>
        <br><br>
        <div style="page-break-after:always"></div>
        </body>
        </html>
    <?php }
?>

