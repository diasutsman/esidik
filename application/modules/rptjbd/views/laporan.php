<?php
ini_set('max_execution_time', 3600000);
ini_set('memory_limit','-1');

$html = '
<html>
<head>
<title></title>
<style> .str{ mso-number-format:\@; } </style>
</head>
<body>
<center>
<h1>'.$comp["companyname"].'</h1>
<p style="text-align:center;">'.$comp["address1"].'</p>
<p style="text-align:center;">Telepon: '.$comp["phone"].', Faks: '.$comp["fax"].'</p>
<hr/>
<h1>
        LAPORAN PENGENDALIAN JAM KERJA
</h1>
</center>
<br>
<br>
<div style="col-md-12">
    <table width="100%" style="font-size:14px;">
        <tbody>
            <tr>
                <td style="width:440px;"></td>
                <td></td>
                <td></td>

                <td>Jakarta, '.format_date_ind($tanggalsekarang).'</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
<div style="margin-left:35px;">
    <table width="100%" style="font-size:14px;">
        <tbody>
            <tr>
                <td style="width:30px;">Nomor</td>
                <td style="width:10px;">:</td>
                <td></td>

                <td>Kepada</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:30px;">Sifat</td>
                <td style="width:10px;">:</td>
                <td>Segera</td>

                <td>Bapak  Menteri Dalam Negeri</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="width:30px;">Hal</td>
                <td style="width:10px;">:</td>
                <td style="width:420px;">Laporan Pengendalian <br>Jam kerja  ASN Pada <br>'.ucwords(strtolower($namaunitkerja)).'</td>

                <td>Cq.<br> Biro Kepegawaian <br> Di</td>
                <td></td>
                <td></td>
            </tr>
            
            
            <tr>
                <td></td>
                <td></td>
                <td></td>

                <td style="text-align:center;">Tempat</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
<br>
<br>
<div style="margin-left: 105px;margin-right: 75px;">
<p style="text-align: justify;font-size:14px;text-indent: 40px;"> Menindaklanjuti Surat Edaran Menteri Dalam Negeri tentang Pengendalian Jam Kerja Aparatur Sipil Negara Kementerian Dalam Negeri Di Wilayah JABODETABEK berikut kami sampaikan laporan pengendalian jam kerja pada '.ucwords(strtolower($namaunitkerja)).' dengan rincian sebagai berikut :</p>
<div style="padding-left: 0px;" >
<table width="100%" style="padding-left: 0px;">
    <tbody>
        <tr>
            <td style="width:120px;font-size:14px;padding-left: 0px;">Unit Kerja</td>
            <td style="width:10px;font-size:14px;">:</td>
            <td style="font-size:14px;">'.ucwords(strtolower($namaunitkerja)).'</td>
        </tr>
        <tr>
            <td style="width:120px;font-size:14px;padding-left: 0px;">Periode laporan</td>
            <td style="width:10px;font-size:14px;">:</td>
            <td style="font-size:14px;">'.$mulai.' s/d '.$akhir.'</td>
        </tr>
    </tbody>
</table>
</div>
<table width="100%" border="1" style="font-size:14px;">
    <thead>
        <tr>
            <th  style="vertical-align: middle;text-align:center;">No</th>
            <th  style="vertical-align: middle;text-align:center;">Keterangan</th>
            <th  style="vertical-align: middle;text-align:center;">Jumlah Pegawai</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center;">1.</td>
            <td>Jumlah Keseluruhan Pegawai</td>
            <td style="text-align: center;">'.$jum_pegawai.'</td>
        </tr>
        <tr>
            <td style="text-align: center;">2.</td>
            <td>Rata-rata jumlah pegawai yang melaksanakan tugas kedinasan di luar kantor (perjalanan dinas atau tugas kedinasan lainnya)</td>
            <td style="text-align: center;">'.$jum_pegawai_dinas.'</td>
        </tr>
        <tr>
            <td style="text-align: center;">3.</td>
            <td>Rata-rata jumlah pegawai yang melaksanakan tugas kedinasan di rumah (WFH)</td>
            <td style="text-align: center;">'.$jum_pegawai_wfh.'</td>
        </tr>
        <tr>
            <td></td>
            <td>Rata-rata jumlah pegawai yang melaksanakan tugas kedinasan di kantor (WFO)</td>
            <td style="text-align: center;">'. $jum_pegawai_shift.'</td>
        </tr>
        <tr>
            <td style="text-align: center;">4.</td>
            <td>WFO Shift 1</td>
            <td style="text-align: center;">'. $jum_pegawai_shift1.'</td>
        </tr>
        <tr>
            <td></td>
            <td>WFO Shift 2</td>
            <td style="text-align: center;">'. $jum_pegawai_shift2.'</td>
        </tr>
    </tbody>
</table>
<p style="font-size:14px;"><b>Catatan Pelaksanaan</b></p>
<p style="font-size:14px;">WFO Normal ('.$keterangan.') jumlah pegawai = '.$jum_pegawai_normal.'
<br>
<p style="text-align: justify;font-size:14px;text-indent: 40px;"> Demikian disampaikan, atas perhatian disampaikan terimakasih.</p>
<table width="100%" style="margin-bottom:120px;">
    <tbody>
        <tr>
            <td style="width:350px;font-size:14px;"></td>
            <td style="font-size:14px;"></td>
            <td style="font-size:14px;text-align:center;">
                '.substr(ucwords(strtolower($kepala)),0,strpos($kepala,"PADA")).'
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                '.$namattd.'
                <br>
                '.$nipttd.'
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>

    </tbody>
</table>
</div>
</body>
</html>
';
include_once APPPATH.'/third_party/mpdf60/mpdf.php';
$mpdf = new mPDF('','Legal',0,'arial',5,5,5,5,9,5,'L');
$stylesheet = file_get_contents( base_url().'assets/css/mpdfstyletables.css' );
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html);
$mpdf->Output('Laporan Pengendalian Jam Kerja ASN Pada '.substr(ucwords(strtolower($kepala)),0,strpos($kepala,"PADA")).'.pdf','I');
exit;
?>
