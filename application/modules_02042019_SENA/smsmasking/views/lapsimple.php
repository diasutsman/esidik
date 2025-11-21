<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <style>
        <?php include FCPATH."/assets/css/printrekap.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daftar Pegawai</title>
</head>

<body><?php } ?>
<style> .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
    .tblnoborder td {
        padding: 4px 3px 4px 5px;
        border-style: none;
        border-width: 0px;
    }

    
</style>

<?php
if($cominfo['companyname']!='') {
?>
    <center>
    <h1><?php echo $cominfo["companyname"];?></h1>
    <p align="center"><?php echo $cominfo["address1"];?></p>
    <p align="center">Telepon: <?php echo $cominfo["phone"];?>, Faks: <?php echo $cominfo["fax"];?></p>
    <hr/><br>
    </center>
<?php } ?>
<center>
<h1><?php echo strtoupper('Daftar Pegawai'); ?></h1>
<?php echo "<div style='font-size: 11px;font-family: arial;'> Periode: ".$periode."</div>"; ?>
</center>
<br>
<table width="100%" border="1" class="tblnoborder" cellspacing="0" cellpadding="0">
    <tr align="center">
    <th width="20">No.</th>
    <th width="120">NIP</th>
    <th width="120">No.Telp</th>
    <th width="120">Email</th>
    <th width="220">Nama</th>
    <th width="100">Atasan</th>
    <th width="200">Jumlah Ketidakhadiran</th>
    </tr>
    <?php 
    $no=1;
    foreach($querycok->result() as $que) {

        /* $sqlcok = "select ifnull(count(*),0) as jml
						from process a 
						where a.userid='".$que->userid."' and (a.date_shift >= '".$this->db->escape_str($start_date)."' and 
						a.date_shift <= '".$this->db->escape_str($end_date)."') and a.attendance in ('ALP','AB_12')";
        $querycok = $this->db->query($sqlcok); */
        $value = $que->jml;
        $valued = $value<5 ?'label-info':'label-danger';

        $atasan =$this->mdl_sms->getAtasan($que->userid);
        $nip="";$nama="";
        if (count($atasan)>0)
        {
            $nip=$atasan['nip'];
            $nama=$atasan['nama'];
            if ($atasan['nip']==$que->userid)     
            {
            $atasan2 =$this->mdl_sms->getAtasanByDeptId($atasan['deptid']);
            if (count($atasan2)>0)
            {
                $nip=$atasan2['nip'];
                $nama=$atasan2['nama'];
            }
            }
        }

        //if ($value>=5)
        $compa = $this->report_model->getcompany();
        if ($value >= intval($compa->row()->batas_alpa_sms))
        {

    ?>
    <tr>
		<td align="center" class="text" valign="top"><?php echo $no?></td>
        <td align="left" class="text" valign="top"><?php echo $que->userid?></td>
        <td align="left" class="text" valign="top"><?php echo $que->no_telepon?></td>
        <td align="left" class="text" valign="top"><?php echo $que->email?></td>
        <td align="left" class="text" valign="top"><?php echo $que->name?></td>
        <td align="left" class="text" valign="top"><?php echo $nip."<br>".$nama;?></td>
        <td align="center" class="num" valign="top"><?php echo $value?></td>
	</tr>
    <?php 
        $no++;
        }
    }
    if ($querycok->num_rows()==0)    
    {
        echo '<tr>
		<td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
	    </tr>';
    }
    ?>
</table>