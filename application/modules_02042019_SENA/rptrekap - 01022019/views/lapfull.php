<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <style>
        <?php include FCPATH."/assets/css/printrekap.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rekapitulasi Kehadiran</title>
</head>

<body><?php } ?>

<style> .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
    .ttd {
        border-style: none;
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
    <hr/><br></center><?php } ?>
<center>
    <h1>
        <?php echo strtoupper('Laporan Rekapitulasi Kehadiran'); ?>
    </h1>

    <?php echo "<div style='font-size: 11px;font-family: arial;'> Periode: ".$periode."</div>"; ?>
</center>
<br>
<?php
	$headcol = '<tr align="center">
				<th width="20" rowspan="2">No.</th>
				<th width="120" rowspan="2">NIP</th>
				<th width="220" rowspan="2">Nama</th>
				<th width="380" rowspan="2">JABATAN</th>
				<th colspan="'.count($arr_days).'">'.$periode.'</th>
				<th colspan="10">S T A T U S</th>
				</tr><tr>';
				
	for($i=0;$i<count($arr_days);$i++) {		
		$headcol .= '<th width="30">'.date("d",strtotime($arr_days[$i])).'</th>';
	}
	/*
	 * <th width="30" class="SKT">SKT</th>
	 */
	$headcol .= '<th width="30" class="HDR">HDR</th>
				<th width="30" class="ALP">ALP</th>
				<th width="30" class="OFF">OFF</th>
				<th width="30" class="LBR">LBR</th>
				<th width="30" class="CTI">CTI</th>
				<th width="30" class="DSL">DL</th>
				<th width="30" class="PDK">PDK</th>
				<th width="30" class="TB">TB</th>
				<th width="30" class="TB">Meninggal</th>
				<th width="30" class="TB">DPK</th>
				</tr>';
	
	$tampilanReport = '<table width="100%" border="1" cellspacing="0" cellpadding="0" >';
				
	$total_hour=0;
	$k=0;
	$_arrDept = array();
	
	$datarekap = array();
	foreach($querycok->result() as $que) {
		if ($que->workinholiday==1){
			$datarekap[$que->userid][$que->date_shift]='LBR';
		} else {
			if($que->attendance == 'NWK' || $que->attendance == 'NWDS' || $que->workinholiday == 2) {
				$datarekap[$que->userid][$que->date_shift]='OFF';
			} else if($que->attendance == 'ALP' || $que->attendance == 'AB_12' || $que->attendance == 'AB_18') {
			    if ($que->attendance == 'AB_12') $datarekap[$que->userid][$que->date_shift]='ALP';
                if ($que->attendance == 'AB_18') $datarekap[$que->userid][$que->date_shift]='M';
                if ($que->attendance == 'ALP') $datarekap[$que->userid][$que->date_shift]='ALP';
			} else if($que->attendance == 'AB_1' || $que->attendance == 'AB_2') {
				$datarekap[$que->userid][$que->date_shift]='SKT';
			} else if($que->attendance == 'AB_3' || $que->attendance == 'AB_4' || $que->attendance == 'AB_11' ||
                $que->attendance == 'AB_5' || $que->attendance == 'AB_6' || $que->attendance == 'AB_7' ||
                $que->attendance == 'AB_8' || $que->attendance == 'AB_9' || $que->attendance == 'AB_14' ||
                $que->attendance == 'AB_17' || $que->attendance == 'AB_20' ) {
				$datarekap[$que->userid][$que->date_shift]='CTI';
			} else if ($que->attendance == 'AB_13') {
				$datarekap[$que->userid][$que->date_shift]='DSL';
			} else if ($que->attendance == 'AB_15' || $que->attendance == 'AT_DK') {
				$datarekap[$que->userid][$que->date_shift]='PDK';
			} else if ($que->attendance == 'AB_16') {
				$datarekap[$que->userid][$que->date_shift]='TB';
			} else if ($que->attendance == 'AB_19') {
                $datarekap[$que->userid][$que->date_shift]='DPK';
            }else if (substr($que->attendance,0,2) == 'AT') {

			    switch ($que->attendance)
                {
                    case "AT_DK":
                        $datarekap[$que->userid][$que->date_shift]='PDK';
                        break;
                    default:
                        $datarekap[$que->userid][$que->date_shift]='HDR';
                        break;
                }

            } else {
                if(($que->check_in!='' || $que->check_out !='') && $que->attendance != 'NWK' &&
                    $que->workinholiday != 1 && $que->workinholiday != 2) {
                    if ($que->attendance == 'AB_12') {
                        $datarekap[$que->userid][$que->date_shift] = 'ALP';
                    } else {
                        $datarekap[$que->userid][$que->date_shift] = 'HDR';
                    }
                }
            }
		}

		//echo substr($que->attendance,0,2) ." ".$que->date_shift." - ".$datarekap[$que->userid][$que->date_shift]."<br>";
	}
	$tampilanReport .= '<tr align="center"><td colspan="'.(count($arr_days)+14).'" align="left" ><h4> Unit Kerja: '.$nama_dept.'</h4></td></tr>';
	$tampilanReport .= $headcol;
	foreach($group_per_date->result() as $dataatt)
	{				
		$tampilanReport .= '<tr border="1">
		<td align="center" valign="top">'.($k+1).'</td>
		<td align="left" valign="top" class="text">'.($excelid==1?" ".$dataatt->userid:$dataatt->userid).'</td>
		<td align="left" valign="top">'.$dataatt->name.'</td>
		<td align="left" valign="top">'.$dataatt->title.'</td>';
		$P = 0; $A = 0; $OFF = 0; $L = 0; $S = 0; $C = 0; $DL = 0; $PDK = 0; $TB = 0;$M = 0;$DPK = 0;
		for($i=0;$i<count($arr_days);$i++){		
			if(isset($datarekap[$dataatt->userid][$arr_days[$i]])) {
				$tampilanReport .= '<td align="center" class="'.$datarekap[$dataatt->userid][$arr_days[$i]].'">'.$datarekap[$dataatt->userid][$arr_days[$i]].'</td>';
				if($datarekap[$dataatt->userid][$arr_days[$i]]=='HDR') $P++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='ALP') $A++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='OFF') $OFF++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='LBR') $L++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='SKT') $S++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='CTI') $C++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='DSL') $DL++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='PDK') $PDK++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='TB') $TB++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='M') $M++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]=='DPK') $DPK++;
			} else {
				$tampilanReport .= '<td align="center" ></td>';
				$A++;
			}
		}
		/*
		 * <td align="center">'.$S.'</th>
		 */
		$tampilanReport .= '<td align="center">'.$P.'</th>
							<td align="center">'.$A.'</th>
							<td align="center">'.$OFF.'</th>
							<td align="center">'.$L.'</th>
							
							<td align="center">'.($C+$S).'</th>
							<td align="center">'.$DL.'</th>
							<td align="center">'.$PDK.'</th>
							<td align="center">'.$TB.'</th>
							<td align="center">'.$M.'</th>
							<td align="center">'.$DPK.'</th>
							</tr>';			
		$k++;				
	}		
	echo $tampilanReport."</table>";
	
?>
<br><br>KETERANGAN : <br><br>
<table border="0" cellspacing="0" cellpadding="0" style="width:100%;" class="tblnoborder">
    <tr>
        <td width="<?php echo $pdfid==1?'75%':'50%' ?>">
            <table>
                <tr>
                    <td width="50" align="center" class="HDR">HDR</td>
                    <td width="450">HADIR / PRESENT</td>
                </tr>
                <tr>
                    <td width="50" align="center" class="ALP">ALP</td>
                    <td width="450">ALPA / ABSENT</td>
                </tr>
                <tr>
                    <td width="50" align="center" class="LBR">LBR</td>
                    <td width="450">LIBUR NASIONAL / LIBUR BERSAMA</td>
                </tr>
                <!--<tr>
                  <td width="50" align="center" class="SKT">SKT</td>
                  <td width="450">SAKIT</td>
                </tr>-->
                <tr>
                    <td width="50" align="center" class="CTI">CTI</td>
                    <td width="450">CUTI</td>
                </tr>
                <tr>
                    <td width="50" align="center" class="DSL">DL</td>
                    <td width="450">DINAS LUAR</td>
                </tr>
                <tr>
                    <td width="50" align="center" class="PDK">PDK</td>
                    <td width="450">DIKLAT</td>
                </tr>
                <tr>
                    <td width="50" align="center" class="TB">TB</td>
                    <td width="450">TUGAS BELAJAR</td>
                </tr>
                <tr>
                    <td width="50" align="center" class="M">M</td>
                    <td width="450">MENINGGAL DUNIA</td>
                </tr>
                <tr>
                    <td width="50" align="center" class="DPK">TB</td>
                    <td width="450">DPK</td>
                </tr>
            </table>
        </td>

        <td width="<?php echo $pdfid==1?'25%':'50%' ?>">

            <!-- section ttd -->
            <div style="margin-top: 0px">
                <table border="0" style="border: none; width: 100%">
                    <!--  <tr></tr>
                     <tr></tr> -->
                    <tr></tr>
                    <tr>

                        <td width="20%" class="ttd"></td>
                        <td width="20%" class="ttd"></td>
                        <td width="20%" class="ttd"></td>
                        <td colspan="3" width="40%" class="text ttd" style="padding-bottom: 100px;"><b><?php echo $ttd_jabatan; ?></b></td>
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
                        <td width="35%" class="text ttd"><b><?php echo $ttd_nama; ?></b></td>
                    </tr>
                    <tr>

                        <td width="20%" class="ttd"></td>
                        <td width="20%" class="ttd"></td>
                        <td width="20%" class="ttd"></td>
                        <td width="35%" class="text ttd"><b><?php echo $ttd_gol; ?></b></td>
                    </tr>
                    <tr>
                        <td width="20%" class="ttd"></td>
                        <td width="20%" class="ttd"></td>
                        <td width="20%" class="ttd"></td>
                        <td width="35%" class="text ttd">NIP. <?php echo $ttd_nip; ?></td>
                    </tr>
                </table>
            </div>
            <!-- end section ttd -->

        </td>
    </tr>
</table>