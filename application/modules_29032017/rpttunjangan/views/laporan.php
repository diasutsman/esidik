<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php //if($index==0) {
$clss = '';
?>
<?php if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/print.css"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Tunjangan Kinerja</title>
</head>

<body><?php } ?>
<style> .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
</style>
<?php


if ($cominfo['companyname'] != '') {

    ?>
    <table class="head">
        <tr>
            <td width="180" rowspan="4" align="center" style="border:0px"><?php if ($cominfo['logo'] != '') { ?><img src="<?php echo base_url() . $cominfo['logo']; ?>" width="50" /><?php $clss = 'align="left"';
                } else {
                    $clss = 'align="center"';
                } ?></td>
            <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
            <td <?php echo $clss; ?> style="border:0px"><h1><?php echo $cominfo['companyname']; ?></h1></td>
        </tr>
        <tr>
            <td <?php echo $clss; ?> style="border:0px"><?php echo $cominfo['address1']; ?></td>
        </tr>
        <?php
        if ($cominfo['address2'] != '') {
            ?>
            <tr>
                <td <?php echo $clss; ?> style="border:0px"><?php echo $cominfo['address2']; ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td <?php echo $clss; ?> style="border:0px">Telepon: <?php echo $cominfo['phone']; ?>, Fax: <?php echo $cominfo['fax']; ?></td>
            <td width="10" rowspan="4" style="border:0px">&nbsp;</td>
            <td width="180" rowspan="4" align="center" style="border:0px">&nbsp;</td>
        </tr>
    </table>
    <hr/><?php } ?>
<h1>
    <center>
        <?php echo "LAPORAN TUNJANGAN KINERJA<br/>BERDASARKAN DISIPLIN KERJA"; ?>
    </center>
</h1>
<br>
<center>
    <div style='font-family:arial;font-size:11px'>PERIODE : <?= $dateinfo; ?></div>
</center>
<br>
<table class="head">
    <?php

    if ($byuser == 1) {
        //echo $byuser." Kriteria: ".$empinfo['kriteriaPlt']." dipisah: ".$dipisah." unit: ".$empinfo['orgf'];
        if (($empinfo['isplt'] == 1)) {
            ?>
            <tr>
                <td width="150" style="border:0px">ID</td>
                <td style="border:0px">: <?php echo $empinfo['userid']; ?></td>
                <td width="150" style="border:0px">Organisasi</td>
                <td style="border:0px">: <?php echo $empinfo['deptName']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px">NIP</td>
                <td style="border:0px">: <?php echo $empinfo['empID']; ?></td>
                <td width="150" style="border:0px">Jabatan</td>
                <td style="border:0px">: <?php echo $empinfo['empTitle']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px">Nama</td>
                <td style="border:0px">: <?php echo $empinfo['empName']; ?></td>
                <td width="150" style="border:0px">Kelas Jabatan</td>
                <td style="border:0px">: <?php echo $empinfo['kelasjabatan'] == 0 ? '' : $empinfo['kelasjabatan']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"></td>
                <td style="border:0px"></td>
                <td width="150" style="border:0px">Organisasi PLT/PLH&nbsp;</td>
                <td style="border:0px">: <?php echo $empinfo['plt_deptname']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"></td>
                <td style="border:0px"></td>
                <td width="150" style="border:0px">Jabatan PLT/PLH&nbsp</td>
                <td style="border:0px">: <?php echo $empinfo['plt_jbtn']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"></td>
                <td style="border:0px"></td>
                <td width="150" style="border:0px">Kelas Jabatan</td>
                <td style="border:0px">: <?php echo $empinfo['plt_kelasjabatan'] == 0 ? '' : $empinfo['plt_kelasjabatan']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <?php

        } else {
            ?>
            <tr>
                <td width="150" style="border:0px">ID</td>
                <td style="border:0px">: <?php echo $empinfo['userid']; ?></td>
                <td width="150" style="border:0px">Organisasi</td>
                <td style="border:0px">: <?php echo $empinfo['deptName']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px">NIP</td>
                <td style="border:0px">: <?php echo $empinfo['empID']; ?></td>
                <td width="150" style="border:0px">Jabatan</td>
                <td style="border:0px">: <?php echo $empinfo['empTitle']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px">Nama</td>
                <td style="border:0px">: <?php echo $empinfo['empName']; ?></td>
                <td width="150" style="border:0px">Kelas Jabatan</td>
                <td style="border:0px">: <?php echo $empinfo['kelasjabatan'] == 0 ? '' : $empinfo['kelasjabatan']; ?></td>
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
                    <td width="150" style="border:0px"><b>Tunjangan Kinerja </b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($empinfo['tunjangan'])) : number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>

                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Pengurangan Tunj.Kinerja</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($pengurangan, 2)) : number_format($pengurangan, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"><b>Total Tunjangan Kinerja</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"><b>Tunjangan PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? (ceil(round($empinfo['tunjanganplt'], 2))) : number_format(ceil(round($empinfo['tunjanganplt'], 2)), 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Pengurangan Tunj.PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($penguranganplt, 2)) : number_format($penguranganplt, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Total Tunj.PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $totltunkplt : number_format($totltunkplt, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"><b>Grand Total Tunjangan Kinerja</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? (ceil(round($grandttl, 2))) : number_format(ceil(round($grandttl, 2)), 0, ',', '.'); ?></b></td>
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
                    <td width="150" style="border:0px"><b>Tunjangan Kinerja PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($tunjangan)) : number_format(ceil(round($tunjangan, 2)), 0, ',', '.') ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>

                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Total Pengurangan</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($pengurangan, 2)) : number_format($pengurangan, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"><b>Total Tunjangan Kinerja</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td width="150" style="border:0px"><b>Tunjangan Kinerja <?php echo ($empinfo['kriteriaPlt'] == 3) ? "PLT/PLH" : "" ?></b></td>
                <td style="border:0px"><b>: Rp. </b></td>
                <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($empinfo['tunjangan'])) : number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') ?></b></td>
                <td style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <?php

            $pengurangan = (ceil(round($footah['total'], 2)) > ceil(round($empinfo['tunjangan'], 2))) ? ceil(round($empinfo['tunjangan'], 2)) : ceil(round($footah['total'], 2));
            $tunjangan = $empinfo['tunjangan'];
            $totltunk = (ceil(round($tunjangan, 2)) - ceil(round($pengurangan, 2)));
            ?>
            <tr style="border-bottom:2px solid">
                <td width="150" style="border:0px"><b>Total Pengurangan</b></td>
                <td style="border:0px"><b>: Rp. </b></td>
                <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $pengurangan : number_format($pengurangan, 0, ',', '.'); ?></b></td>
                <td style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"><b>Total Tunjangan Kinerja</b></td>
                <td style="border:0px"><b>: Rp. </b></td>
                <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b></td>
                <td style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <?php
        }
    } //end byUser
    else {
        //echo $byuser." Kriteria: ".$empinfo['kriteriaPlt']." dipisah: ".$dipisah." unit: ".$empinfo['orgf'];

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
                <td width="150" style="border:0px">ID</td>
                <td style="border:0px">: <?php echo $empinfo['userid']; ?></td>
                <td width="150" style="border:0px">Organisasi</td>
                <td style="border:0px">: <?php echo $dpename; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px">NIP</td>
                <td style="border:0px">: <?php echo $empinfo['empID']; ?></td>
                <td width="150" style="border:0px">Jabatan</td>
                <td style="border:0px">: <?php echo $dpename2 ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px">Nama</td>
                <td style="border:0px">: <?php echo $empinfo['empName']; ?></td>
                <td width="150" style="border:0px">Kelas Jabatan</td>
                <td style="border:0px">: <?php echo $dpename3 ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <?php if ($dipisah == 0) { ?>
                <tr>
                    <td width="150" style="border:0px"></td>
                    <td style="border:0px"></td>
                    <td width="150" style="border:0px">Organisasi PLT/PLH&nbsp;</td>
                    <td style="border:0px">: <?php echo $empinfo['plt_deptname']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"></td>
                    <td style="border:0px"></td>
                    <td width="150" style="border:0px">Jabatan PLT/PLH&nbsp</td>
                    <td style="border:0px">: <?php echo $empinfo['plt_jbtn']; ?></td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"></td>
                    <td style="border:0px"></td>
                    <td width="150" style="border:0px">Kelas Jabatan</td>
                    <td style="border:0px">: <?php echo $empinfo['plt_kelasjabatan'] == 0 ? '' : $empinfo['plt_kelasjabatan']; ?></td>
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
                    <td width="150" style="border:0px"><b>Tunjangan Kinerja </b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($empinfo['tunjangan'])) : number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>

                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Pengurangan Tunj.Kinerja</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($pengurangan, 2)) : number_format($pengurangan, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"><b>Total Tunjangan Kinerja</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
            <?php }
            if ($dipisah == 2) {
                ?>
                <tr>
                    <td width="150" style="border:0px"><b>Tunjangan PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? (ceil(round($empinfo['tunjanganplt'], 2))) : number_format(ceil(round($empinfo['tunjanganplt'], 2)), 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Pengurangan Tunj.PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($penguranganplt, 2)) : number_format($penguranganplt, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Total Tunj.PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $totltunkplt : number_format($totltunkplt, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
            <?php }
            if ($dipisah == 0) {
                ?>
                <tr>
                    <td width="150" style="border:0px"><b>Tunjangan Kinerja </b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($empinfo['tunjangan'])) : number_format(ceil(round($empinfo['tunjangan'], 2)), 0, ',', '.') ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>

                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Pengurangan Tunj.Kinerja</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($pengurangan, 2)) : number_format($pengurangan, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"><b>Total Tunjangan Kinerja</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $totltunk : number_format($totltunk, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="150" style="border:0px"><b>Tunjangan PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? (ceil(round($empinfo['tunjanganplt'], 2))) : number_format(ceil(round($empinfo['tunjanganplt'], 2)), 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Pengurangan Tunj.PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($penguranganplt, 2)) : number_format($penguranganplt, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <tr style="border-bottom:2px solid">
                    <td width="150" style="border:0px"><b>Total Tunj.PLT/PLH</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $totltunkplt : number_format($totltunkplt, 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
            <?php } ?>
            <?php
            if ($dipisah == 0) {
                ?>
                <tr>
                    <td width="150" style="border:0px"><b>Grand Total Tunjangan Kinerja</b></td>
                    <td style="border:0px"><b>: Rp. </b></td>
                    <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? (ceil(round($grandttl, 2))) : number_format(ceil(round($grandttl, 2)), 0, ',', '.'); ?></b></td>
                    <td style="border:0px">&nbsp;</td>
                    <td style="border:0px">&nbsp;</td>
                </tr>
                <?php
            }
        } else if (($empinfo['kriteriaPlt'] == 3) && ($ishidden)) {
            ?>
            <tr>
                <td width="150" style="border:0px">ID</td>
                <td style="border:0px">: <?php echo $empinfo['userid']; ?></td>
                <td width="150" style="border:0px">Organisasi</td>
                <td style="border:0px">: <?php echo $empinfo['deptName']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px">NIP</td>
                <td style="border:0px">: <?php echo $empinfo['empID']; ?></td>
                <td width="150" style="border:0px">Jabatan</td>
                <td style="border:0px">: <?php echo $empinfo['empTitle']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px">Nama</td>
                <td style="border:0px">: <?php echo $empinfo['empName']; ?></td>
                <td width="150" style="border:0px">Kelas Jabatan</td>
                <td style="border:0px">: <?php echo $empinfo['kelasjabatan'] == 0 ? '' : $empinfo['kelasjabatan']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"></td>
                <td style="border:0px"></td>
                <td width="150" style="border:0px">Organisasi PLT/PLH&nbsp;</td>
                <td style="border:0px">: <?php echo $empinfo['plt_deptname']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"></td>
                <td style="border:0px"></td>
                <td width="150" style="border:0px">Jabatan PLT/PLH&nbsp</td>
                <td style="border:0px">: <?php echo $empinfo['plt_jbtn']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"></td>
                <td style="border:0px"></td>
                <td width="150" style="border:0px">Kelas Jabatan</td>
                <td style="border:0px">: <?php echo $empinfo['plt_kelasjabatan'] == 0 ? '' : $empinfo['plt_kelasjabatan']; ?></td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"><b>Tunjangan Kinerja</b></td>
                <td style="border:0px"><b>: Rp. </b></td>
                <td width="150" style="border:0px" align="right"><b>-</b></td>
                <td style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr style="border-bottom:2px solid">
                <td width="150" style="border:0px"><b>Total Pengurangan</b></td>
                <td style="border:0px"><b>: Rp. </b></td>
                <td width="150" style="border:0px" align="right"><b>-</b></td>
                <td style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"><b>Total Tunjangan Kinerja</b></td>
                <td style="border:0px"><b>: Rp. </b></td>
                <td width="150" style="border:0px" align="right"><b>-</b></td>
                <td style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
            </tr>

        <?php } else {
            ?>
            <tr>
                <td width="150" style="border:0px">ID</td>
                <td style="border:0px">: <?php echo $empinfo['userid']; ?></td>
                <td width="150" style="border:0px"><?php echo "Organisasi ". ($empinfo['kriteriaPlt'] == 3 ? "PLT/PLH" : ""); ?></td>
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
                <td width="150" style="border:0px">NIP</td>
                <td style="border:0px">: <?php echo $empinfo['empID']; ?></td>
                <td width="150" style="border:0px"><?php echo "Jabatan " . ($empinfo['kriteriaPlt'] == 3 ? "PLT/PLH" : "") ?></td>
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
                <td width="150" style="border:0px">Nama</td>
                <td style="border:0px">: <?php echo $empinfo['empName']; ?></td>
                <td width="150" style="border:0px">Kelas Jabatan <?php echo($empinfo['kriteriaPlt'] == 3 ? "PLT/PLH" : "") ?></td>
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
            }
            ?>
            <tr>
                <td width="150" style="border:0px"><b>Tunjangan Kinerja <?php echo ($empinfo['kriteriaPlt'] == 3) ? "PLT/PLH" : "" ?></b></td>
                <td style="border:0px"><b>: Rp. </b></td>
                <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? ceil(round($tunjangan,2)) : number_format(ceil(round($tunjangan, 2)), 2, ',', '.') ?></b></td>
                <td style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
            </tr>

            <tr style="border-bottom:2px solid">
                <td width="150" style="border:0px"><b>Total Pengurangan</b></td>
                <td style="border:0px"><b>: Rp. </b></td>
                <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $pengurangan : number_format($pengurangan, 2, ',', '.'); ?></b></td>
                <td style="border:0px">&nbsp;</td>
                <td style="border:0px">&nbsp;</td>
            </tr>
            <tr>
                <td width="150" style="border:0px"><b>Total Tunjangan Kinerja</b></td>
                <td style="border:0px"><b>: Rp. </b></td>
                <td width="150" style="border:0px" align="right"><b><?php echo $excelid == 1 ? $totltunk : number_format($totltunk, 2, ',', '.'); ?></b></td>
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
					<td style="border:0px"><span style="color:red">Tidak ada data Tunjangan Kinerja</span></td>
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
                    <th width="100">Hari</th>
                    <th width="100">Tanggal</th>
                    <th>Status</th>
                    <th width="130">Nilai</th>
                    <th width="50">Pengurangan</th>
                    <?php if ($showhplt == 1 || $byuser == 1) { ?>
                        <th width="130">PLT/PLH</th>
                    <?php } ?>
                    <?php if ($showhunkir == 1 || $byuser == 1) { ?>
                        <th width="130">Disiplin</th>
                    <?php } ?>
                    <th width="130">Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $x = 1;
                foreach ($data as $row) {
                    ?>
                    <tr class="<?php if ($x % 2 === 0) echo 'even'; else echo 'odd'; ?>">
                        <td><?php echo hariIndo($row['day']); ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td align="right"><?php echo $row['nilai']; ?></td>
                        <td align="center"><?php echo $excelid == 1 ? $row['pengurangan'] : number_format($row['pengurangan'], 2, ',', '.'); ?></td>
                        <?php
                        $nPlt = 0;
                        $nUnkir = 0;
                        $nTotalRow = 0;
                        $showplt = 0;
                        $showunkir = 1;
                        $nUnkir = $excelid == 1 ? ceil(round($row['total'], 2)) : number_format(ceil(round($row['total'], 2)), 2, ',', '.');
                        $nTotalRow = ceil(round($row['total'], 2)) + ceil(round($row['totalplt'], 2));
                        if ($empinfo['kriteriaPlt'] != 3) {
                            if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {
                                $nPlt = $excelid == 1 ? ceil(round($row['totalplt'], 2)) : number_format(ceil(round($row['totalplt'], 2)), 2, ',', '.');
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
                            }
                        } else {
                            $nPlt = $excelid == 1 ? ceil(round($row['totalplt'], 2)) : number_format(ceil(round($row['totalplt'], 2)), 2, ',', '.');
                            $nUnkir = 0;
                            $showplt = 1;
                            $showunkir = 0;
                            $nTotalRow = ceil(round($row['totalplt'], 2));
                        }

                        $nTotalRow = $excelid == 1 ? $nTotalRow : number_format($nTotalRow, 2, ',', '.');
                        ?>
                        <?php if ($showplt == 1 || $byuser == 1) { ?>
                            <td align="right">
                                <?php echo $nPlt ?>
                            </td>
                        <?php } ?>
                        <?php if ($showunkir == 1 || $byuser == 1) { ?>
                            <td align="right">
                                <?php echo $nUnkir ?>
                            </td>
                        <?php } ?>
                        <td align="right">
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

                $footplt = $excelid == 1 ? $nfootplt : number_format($nfootplt, 2, ',', '.');
                $footunkir = $excelid == 1 ? $nfootunkir : number_format($nfootunkir, 2, ',', '.');
                $foottotal = $excelid == 1 ? $nfoottotal : number_format($nfoottotal, 2, ',', '.');

                if ($empinfo['kriteriaPlt'] != 3) {
                    if ($empinfo['kriteriaPlt'] == 1 || $empinfo['kriteriaPlt'] == 2) {
                        if ($dipisah == 2) {
                            $showhplt = 1;
                            $showhunkir = 0;
                            $foottotal = $excelid == 1 ? $nfootplt : number_format($nfootplt, 2, ',', '.');
                        }
                    }
                } else {
                    $nUnkir = 0;
                    $footunkir = 0;
                    $foottotal = $excelid == 1 ? $nfootplt : number_format($nfootplt, 2, ',', '.');
                }
                ?>
                <tr>
                    <td colspan=4 align="center"><b><?php echo $this->lang->line('total'); ?></b></td>
                    <td align="center"><b><?php echo $footah['totalpersen'] ?></b></td>
                    <?php if ($showhplt == 1 || $byuser == 1) { ?>
                        <td align="right"><b><?php echo $footplt ?></b></td>
                    <?php } ?>
                    <?php if ($showhunkir == 1 || $byuser == 1) { ?>
                        <td align="right"><b><?php echo $footunkir ?></b></td>
                    <?php } ?>
                    <td align="right"><b><?php echo $foottotal ?></b></td>

                </tr>
            </table>
        <?php } else { ?>

            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <thead>
                <tr>
                    <th width="100">Hari</th>
                    <th width="100">Tanggal</th>
                    <th>Status</th>
                    <th width="130">Nilai</th>
                    <th width="50">Pengurangan</th>
                    <th width="130">Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $x = 1;
                foreach ($data as $row) {
                    ?>
                    <tr class="<?php if ($x % 2 === 0) echo 'even'; else echo 'odd'; ?>">
                        <td><?php echo $this->lang->line($row['day']); ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td align="right"><?php echo $row['nilai']; ?></td>
                        <td align="center" class="num"><?php echo $row['pengurangan']; ?></td>
                        <td align="right" class="num"><?php echo $excelid == 1 ? ceil(round($row['total'], 2)) : number_format(ceil(round($row['total'], 2)), 2, ',', '.'); ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan=4 align="center"><b><?php echo $this->lang->line('total'); ?></b></td>
                    <td align="center" class="num"><b><?php echo $footah['totalpersen'] ?></b></td>
                    <td align="right" class="num"><b><?php echo $excelid == 1 ? ceil(round($footah['total'], 2)) : number_format(ceil(round($footah['total'], 2)), 2, ',', '.'); ?></b></td>
                </tr>
                </tbody>
            </table>
            <?php
        }
        ?><?php
    }
} ?>
<?php if ($excelid == 0 ) {?>
<br><br>
<div style="page-break-after:always"></div>
</body>
</html>
<?php }?>

