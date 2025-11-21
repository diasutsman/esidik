<?php if ($excelid == 0 ) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <style>
        <?php include FCPATH."/assets/css/print.css"; ?>
    </style>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Jadwal Kerja</title>
</head>

<body><?php } ?>
<style> .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
</style>

<table class="head" width="100%">
    <tr>
        <td align="center" style="border:0px" class="text"><h1><?php echo $cominfo["companyname"]?></h1></td>
    </tr>
    <tr>
        <td align="center" style="border:0px" class="text"><?php echo $cominfo["address1"]?></td>
    </tr>
    <tr>
        <td align="center" style="border:0px" class="text">Telepon: <?php echo $cominfo["phone"]?>, Faks: <?php echo $cominfo["fax"]?></td>
    </tr>
</table>
<hr/>
<h1>
    <center>
        LAPORAN JADWAL KERJA<br>
        Periode : <?php echo format_date_ind(date('Y-m-d',$datestart))?> s.d <?php echo format_date_ind(date('Y-m-d',$dateend))?>
    </center>
</h1>
<br>
<div class="head">Unit Kerja: <?php echo $deptname?></div>
<br>
<table border="1">
    <tr>
        <th bgcolor="#CCCCCC" class="text">No.</th>
        <th bgcolor="#CCCCCC" class="text">NIP Pegawai</th>
        <th bgcolor="#CCCCCC" class="text">Nama</th>
        <?php
        $str1=date('Y-m-d',$datestart);
        $end1=date('Y-m-d',$dateend);
        while (strtotime($str1) <= strtotime($end1)) {
            echo "<th bgcolor='#CCCCCC' class='text'>".date("j",strtotime($str1))."</th>";
            $str1 = date("Y-m-d", strtotime("+1 days", strtotime($str1)));
        }
        ?>
    </tr>
        <?php
        $nmr=1;
        foreach($empdata->result() as $row) {
            echo '<tr>';
            echo '<td style=\'text-align: center\'>'.$nmr.'</td>';
            echo '<td  class="text">'."&nbsp;".$row->userid.'</td>';
            echo '<td class="text">'.$row->name.'</td>';

            $str1=date('Y-m-d',$datestart);
            $end1=date('Y-m-d',$dateend);
            while (strtotime($str1) <= strtotime($end1)) {
                $back="";$absence="";$attendance="";$libur="";$ketat="";
                $ck = array_key_exists($row->userid,$rosterdata);
               // if (count($holarray)>1) {

                $hola=array_key_exists(date('Y-m-d',strtotime($str1)),$holarray[1]);
                if ($hola)
                {
                    $libur=$holarray[1][date('Y-m-d',strtotime($str1))];
                }
                //}
                if ($ck)
                {
                    $arr=$rosterdata[$row->userid];
                    $ck1 = array_key_exists(strtotime($str1),$arr);
                    if ($ck1)
                    {
                        $ck2 = $arr[strtotime($str1)];
                        $absence = $ck2['absence'];
                        $attendance = $ck2['attendance'];
                        $ketat = $ck2['absence']." ".$ck2['attendance'];

                    }
                }
                if (!empty($absence)){

                    $back=$shiftcolor[$absence];

                    if (!empty($attendance))
                    {
                        $absence="<img src='".base_url("assets/img/att.png")."'>";
                    }
                }

                if (!empty($libur))
                {
                    $absence="<img src='".base_url("assets/img/holi.png")."'>";
                }

                echo "<td bgcolor='$back' style='text-align: center' class='text'>".$absence."</td>";
                $str1 = date("Y-m-d", strtotime("+1 days", strtotime($str1)));
            }

            echo '</tr>';
            $nmr++;
        }

        ?>
</table>
<?php if ($excelid == 0 ) {?>
    <div style="page-break-after:always"></div><br>
    </body>
    </html>
<?php }?>