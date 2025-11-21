<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <style>
        <?php include FCPATH."/assets/css/printrekap.css"; ?>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rekapitulasi Ketidakhadiran</title>
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

    .ttd {
        border-style: none;
    }
</style>

<?php
if($cominfo['companyname']!='') {
?>
<center>
    <h1><?php echo $cominfo["companyname"];?></h1>
    <p align="center"><?php echo $cominfo["address1"];?></p>
    <p align="center">Telepon: <?php echo $cominfo["phone"];?>, Faks: <?php echo $cominfo["fax"];?></p>
    <hr/><br></center><?php } ?><center>
<h1>
    <?php echo strtoupper('Laporan Rekapitulasi Ketidakhadiran'); ?>
</h1>

    <?php echo "<div style='font-size: 11px;font-family: arial;'> Periode: ".$periode."</div>"; ?>
</center>
<br>
<?php
	$headcol = '<tr align="center" border="1">
				<th width="20">No.</th>
				<th width="120">NIP</th>
				<th width="220">Nama</th>
				<th width="100">Golongan</th>
				<!--<th width="30">Ijin</th>
				<th width="30">Sakit</th>-->
				<th width="30">Cuti</th>
				<th width="30">Dinas</th>
				<th width="30">TB</th>
				<th width="30">ALP</th>
				<th width="30">TLT</th>
				<th width="30">PSW</th>
				<th width="30">Meninggal</th>
				<th width="30">DPK</th>
				<th width="200">Keterangan</th>
				</tr>';
	
	$tampilanReport = '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
				
	$total_hour=0;
	$k=0;
	$_arrDept = array();
	
	$datarekap = array();
	foreach($querycok->result() as $que) {
		if($que->late > 0 && $que->attendance !='AT_AT1' && $que->attendance !='AT_AT3' && $que->attendance !='AT_AT4' && $que->attendance !='AT_AT6' && $que->attendance !='AT_DK' && $que->attendance != 'NWK' && $que->workinholiday != 1 && strpos($que->attendance, 'AB_')===false) 
			$datarekap[$que->userid][$que->date_shift]='TD';
		else if($que->early_departure > 0 && $que->attendance !='AT_AT2' && $que->attendance !='AT_AT5' && $que->attendance !='AT_AT6' && $que->attendance !='AT_DK' && $que->attendance != 'NWK' && $que->workinholiday != 1 && strpos($que->attendance, 'AB_')===false) 
			$datarekap[$que->userid][$que->date_shift]='PC';
		else if ($que->workinholiday==1){
			$datarekap[$que->userid][$que->date_shift]='L';
		} else {
			if($que->attendance == 'NWK') {
				$datarekap[$que->userid][$que->date_shift]='OFF';
			} else if($que->attendance == 'ALP' ) {
				$datarekap[$que->userid][$que->date_shift]='A';
			} else if($que->attendance == 'AB_12' ) {
                $datarekap[$que->userid][$que->date_shift]='A';
            } else if($que->attendance == 'AB_18') {
                $datarekap[$que->userid][$que->date_shift]='M';
            } else if($que->attendance == 'AB_19') {
                $datarekap[$que->userid][$que->date_shift]='DPK';
            } else if($que->attendance == 'AB_1' || $que->attendance == 'AB_2') {
				$datarekap[$que->userid][$que->date_shift]='S';
			} else if($que->attendance == 'AB_3' || $que->attendance == 'AB_4' || $que->attendance == 'AB_5' ||
                $que->attendance == 'AB_6' || $que->attendance == 'AB_7' || $que->attendance == 'AB_8' ||
                $que->attendance == 'AB_9' || $que->attendance == 'AB_14' || $que->attendance == 'AB_17' ||
                $que->attendance == 'AB_20') {
				$datarekap[$que->userid][$que->date_shift]='C';
			} else if ($que->attendance == 'AB_13') {
				$datarekap[$que->userid][$que->date_shift]='DL';
			} else if ($que->attendance == 'AB_15' || $que->attendance == 'AT_DK') {
				$datarekap[$que->userid][$que->date_shift]='PDK';
			} else if ($que->attendance == 'AB_16') {
				$datarekap[$que->userid][$que->date_shift]='TB';
			} else if ($que->attendance == 'AB_11') {
				$datarekap[$que->userid][$que->date_shift]='IJ';
			}
		}

		//echo $datarekap[$que->userid][$que->date_shift]."<br>";
	}

    $tampilanReport .= '<tr align="center"><td colspan="13" align="left" ><h4> Unit Kerja: '.$nama_dept.'</h4></td></tr>';
	$tampilanReport .= $headcol;

    $jP = 0; $jA = 0; $OjFF = 0; $jL = 0; $jS = 0; $jC = 0; $jDL = 0; $jPDK = 0; $jTB = 0; $jIJ = 0; $jTD = 0; $jPC = 0;
    $jM=0;$jDPK=0;
	foreach($group_per_date->result() as $dataatt)
	{				
		$tampilanReport .= '<tr >
		<td align="center">'.($k+1).'</td>
		<td align="left" class="text">'.($excelid==1?" ".$dataatt->userid:$dataatt->userid).'</td>
		<td align="left">'.$dataatt->name.'</td>
		<td align="left">'.$dataatt->golru.'</td>';
		$P = 0; $A = 0; $OFF = 0; $L = 0; $S = 0; $C = 0; $DL = 0; $PDK = 0; $TB = 0; $IJ = 0; $TD = 0; $PC = 0;
		$M=0;$DPK=0;
		
		for($i=0;$i<count($arr_days);$i++){		
			if(isset($datarekap[$dataatt->userid][$arr_days[$i]])) {
				if($datarekap[$dataatt->userid][$arr_days[$i]]=='P') $P++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='A') $A++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='OFF') $OFF++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='L') $L++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='S') $S++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='C') $C++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='DL') $DL++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='PDK') $PDK++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='TB') $TB++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='IJ') $IJ++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='TD') $TD++;
				else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='PC') $PC++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='M') $M++;
                else if ($datarekap[$dataatt->userid][$arr_days[$i]]==='DPK') $DPK++;
			}
            //echo $arr_days[$i]." - ".($datarekap[$dataatt->userid][$arr_days[$i]])."<br>";
		}
		/*<td align="center">'.$IJ.'</th>
							<td align="center">'.$S.'</th>
		*/
		$tampilanReport .= '
							<td align="center">'.($C+$S).'</td>
							<td align="center">'.$DL.'</td>
							<td align="center">'.$TB.'</td>
							<td align="center">'.$A.'</td>
							<td align="center">'.$TD.'</td>
							<td align="center">'.$PC.'</td>
							<td align="center">'.$M.'</td>
							<td align="center">'.$DPK.'</td>
							<td align="center"></td>
							</tr>';

        $jIJ +=$IJ;
        $jS +=$S;
        $jC +=$C;
        $jDL +=$DL;
        $jTB +=$TB;
        $jA +=$A;
        $jTD +=$TD;
        $jPC +=$PC;
        $jM +=$M;
        $jDPK +=$DPK;
		$k++;
	}

	/*<td align="center">'.$jIJ.'</th>
							<td align="center">'.$jS.'</th>
	*/
$RowReport = '
							<td align="center">'.($jC+$jS).'</td>
							<td align="center">'.$jDL.'</td>
							<td align="center">'.$jTB.'</td>
							<td align="center">'.$jA.'</td>
							<td align="center">'.$jTD.'</td>
							<td align="center">'.$jPC.'</td>
							<td align="center">'.$jM.'</td>
							<td align="center">'.$jDPK.'</td>
							<td align="center"></td>';

$tampilanReport .= '<tr align="center"><td colspan="4" align="left" >TOTAL</td>'.$RowReport.'</tr>';


echo $tampilanReport."</table>";
?>
<br><br>
<table border="0" cellspacing="0" class="tblnoborder" cellpadding="0" style="width:100%;">
    <tr>
        <td width="50%">
            <table>
                <tr>
                    <td style="font-size: 15px">KETERANGAN:</td>
                </tr>
                <tr>
                    <td width="50" align="center">TB</td>
                    <td width="450">TUGAS BELAJAR</td>
                </tr>
                <tr>
                    <td width="50" align="center">ALP</td>
                    <td width="450">ALPA / TANPA KETERANGAN</td>
                </tr>
                <tr>
                    <td width="50" align="center">TLT</td>
                    <td width="450">TERLAMBAT DATANG</td>
                </tr>
                <tr>
                    <td width="50" align="center">PSW</td>
                    <td width="450">PULANG SEBELUM WAKTUNYA</td>
                </tr>
            </table>
        </td>

        <td width="50">

            <!-- section ttd -->
            <div style="margin-top: 75px">
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
                        <td width="35%" class="text ttd"><b><?php echo  $ttd_nama; ?></b></td>
                    </tr>
                    <tr>

                        <td width="20%" class="ttd"></td>
                        <td width="20%" class="ttd"></td>
                        <td width="20%" class="ttd"></td>
                        <td width="35%" class="text ttd"><b><?php echo  $ttd_gol; ?></b></td>
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

        </td>
    </tr>
</table>