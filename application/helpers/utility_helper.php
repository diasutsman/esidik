<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function indo_date($date)
{ //reformat from yyyy-mm-dd to dd mon yyyy

    $newdate = new DateTime($date);
    $y = $newdate->format('Y');
    $m = $newdate->format('n');
    $d = $newdate->format('j');
    $wk = $newdate->format('w');

    $getbulan = array();
    $getbulan[1] = 'Januari';
    $getbulan[2] = 'Februari';
    $getbulan[3] = 'Maret';
    $getbulan[4] = 'April';
    $getbulan[5] = 'Mei';
    $getbulan[6] = 'Juni';
    $getbulan[7] = 'Juli';
    $getbulan[8] = 'Agustus';
    $getbulan[9] = 'September';
    $getbulan[10] = 'Oktober';
    $getbulan[11] = 'November';
    $getbulan[12] = 'Desember';

    $gethari = array();
    $gethari[0] = 'Minggu';
    $gethari[1] = 'Senin';
    $gethari[2] = 'Selasa';
    $gethari[3] = 'Rabu';
    $gethari[4] = 'Kamis';
    $gethari[5] = 'Jumat';
    $gethari[6] = 'Sabtu';

    return $gethari[$wk] . ", " . $d . " " . $getbulan[$m] . " " . $y;
}

function indo_full_date($date)
{ //reformat from yyyy-mm-dd to dd mon yyyy

    $newdate = new DateTime($date);
    $y = $newdate->format('Y');
    $m = $newdate->format('n');
    $d = $newdate->format('j');
    $wk = $newdate->format('w');
    $jam = $newdate->format('H:i:s');

    $getbulan = array();
    $getbulan[1] = 'Januari';
    $getbulan[2] = 'Februari';
    $getbulan[3] = 'Maret';
    $getbulan[4] = 'April';
    $getbulan[5] = 'Mei';
    $getbulan[6] = 'Juni';
    $getbulan[7] = 'Juli';
    $getbulan[8] = 'Agustus';
    $getbulan[9] = 'September';
    $getbulan[10] = 'Oktober';
    $getbulan[11] = 'November';
    $getbulan[12] = 'Desember';

    $gethari = array();
    $gethari[0] = 'Minggu';
    $gethari[1] = 'Senin';
    $gethari[2] = 'Selasa';
    $gethari[3] = 'Rabu';
    $gethari[4] = 'Kamis';
    $gethari[5] = 'Jumat';
    $gethari[6] = 'Sabtu';

    return $gethari[$wk] . ", " . $d . " " . $getbulan[$m] . " " . $y . " " . $jam;
}

function indo_date_no_hari($date)
{ //reformat from yyyy-mm-dd to dd mon yyyy

    $newdate = new DateTime($date);
    $y = $newdate->format('Y');
    $m = $newdate->format('n');
    $d = $newdate->format('j');
    $wk = $newdate->format('w');
    $jam = $newdate->format('H:i');

    $getbulan = array();
    $getbulan[1] = 'Jan';
    $getbulan[2] = 'Feb';
    $getbulan[3] = 'Mar';
    $getbulan[4] = 'Apr';
    $getbulan[5] = 'Mei';
    $getbulan[6] = 'Jun';
    $getbulan[7] = 'Jul';
    $getbulan[8] = 'Ags';
    $getbulan[9] = 'Sep';
    $getbulan[10] = 'Okt';
    $getbulan[11] = 'Nov';
    $getbulan[12] = 'Des';

    $gethari = array();
    $gethari[0] = 'Minggu';
    $gethari[1] = 'Senin';
    $gethari[2] = 'Selasa';
    $gethari[3] = 'Rabu';
    $gethari[4] = 'Kamis';
    $gethari[5] = 'Jumat';
    $gethari[6] = 'Sabtu';

    return $d . " " . $getbulan[$m] . " " . $y . " " . $jam;
}

function hariIndo($enDay)
{
    $lang['Monday'] = 'Senin';
    $lang['Tuesday'] = 'Selasa';
    $lang['Wednesday'] = 'Rabu';
    $lang['Thursday'] = 'Kamis';
    $lang['Friday'] = 'Jumat';
    $lang['Saturday'] = 'Sabtu';
    $lang['Sunday'] = 'Minggu';
    $lang['Mon'] = 'Senin';
    $lang['Tue'] = 'Selasa';
    $lang['Wed'] = 'Rabu';
    $lang['Thu'] = 'Kamis';
    $lang['Fri'] = 'Jumat';
    $lang['Sat'] = 'Sabtu';
    $lang['Sun'] = 'Minggu';

    return isset($lang[$enDay])?$lang[$enDay]:$enDay;
}

function tgl_ind_to_eng($tgl)
{
    $xreturn_ = '';
    if (trim($tgl) != '' && $tgl != '00-00-0000') {
        $tgl_eng = substr($tgl, 6, 4) . "-" . substr($tgl, 3, 2) . "-" . substr($tgl, 0, 2);
        $xreturn_ = $tgl_eng;
    }
    return $xreturn_;
}

function tgl_eng_to_ind($tgl)
{
    $xreturn_ = '';
    if (trim($tgl) != '' AND $tgl != '0000-00-00') {
        $tgl_ind = substr($tgl, 8, 2) . "-" . substr($tgl, 5, 2) . "-" . substr($tgl, 0, 4);
        $xreturn_ = $tgl_ind;
    }
    return $xreturn_;
}

function dmyToymd($tgl)
{
    $xreturn_ = '';
    if (trim($tgl) != '' && $tgl != '00-00-0000' && !empty($tgl)) {
        $pcs = explode("-", $tgl);
        $xreturn_ = $pcs[2] . "-" . $pcs[1] . "-" . $pcs[0];
    }
    return $xreturn_;
}

function ymdTodmy($tgl)
{
    $xreturn_ = '';
    if (trim($tgl) != '' AND $tgl != '0000-00-00') {
        $pcs = explode("-", $tgl);
        $xreturn_ = $pcs[2] . "-" . $pcs[1] . "-" . $pcs[0];
    }
    return $xreturn_;
}

function ymdToIna($tgl)
{
    $newdate = new DateTime($tgl);
    $jam = $newdate->format('H:i:s');

    return format_date_singkat($tgl) . " " . $jam;
}

function cmp($a, $b)
{
    return strtotime($a['checktime']) > strtotime($b['checktime']) ? 1 : -1;
}

function hariToInd($namahari)
{
    $dayList = array('Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu');


    return $dayList[$namahari];
}

function format_angka($angka,$isexcel=null)
{
    if ($isexcel !=null) {
        $hasil = $angka;
    } else {
        $hasil = number_format($angka, 0, ",", ".");
    }
    return $hasil;
}

function format_persen($angka,$isexcel=null)
{
    if ($isexcel !=null) {
        $hasil = $angka;
    } else {
        $hasil = number_format($angka, 2, ",", ".");
    }
    return $hasil;
}

function format_date_ind($tgl)
{
    if (trim($tgl) != '' AND $tgl != '0000-00-00') {
        $d = substr($tgl, 8, 2);
        $m = substr($tgl, 5, 2);
        $y = substr($tgl, 0, 4);
        $getbulan = array();
        $getbulan[1] = 'Januari';
        $getbulan[2] = 'Februari';
        $getbulan[3] = 'Maret';
        $getbulan[4] = 'April';
        $getbulan[5] = 'Mei';
        $getbulan[6] = 'Juni';
        $getbulan[7] = 'Juli';
        $getbulan[8] = 'Agustus';
        $getbulan[9] = 'September';
        $getbulan[10] = 'Oktober';
        $getbulan[11] = 'November';
        $getbulan[12] = 'Desember';

        $tanggal = $d . " " . $getbulan[(int)$m] . " " . $y;
        return $tanggal;
    }
}

function format_date_singkat($tgl)
{
    if (trim($tgl) != '' AND $tgl != '0000-00-00') {
        $d = substr($tgl, 8, 2);
        $m = substr($tgl, 5, 2);
        $y = substr($tgl, 0, 4);
        $getbulan = array();
        $getbulan[1] = 'Jan';
        $getbulan[2] = 'Feb';
        $getbulan[3] = 'Mar';
        $getbulan[4] = 'Apr';
        $getbulan[5] = 'Mei';
        $getbulan[6] = 'Jun';
        $getbulan[7] = 'Jul';
        $getbulan[8] = 'Agst';
        $getbulan[9] = 'Sept';
        $getbulan[10] = 'Okt';
        $getbulan[11] = 'Nov';
        $getbulan[12] = 'Des';

        $tanggal = $d . " " . $getbulan[(int)$m] . " " . $y;
        return $tanggal;
    }
}

function nama_bulan_ind($m)
{
    if (trim($m) != '' AND $m != '0') {
        $getbulan = array();
        $getbulan[1] = 'Januari';
        $getbulan[2] = 'Februari';
        $getbulan[3] = 'Maret';
        $getbulan[4] = 'April';
        $getbulan[5] = 'Mei';
        $getbulan[6] = 'Juni';
        $getbulan[7] = 'Juli';
        $getbulan[8] = 'Agustus';
        $getbulan[9] = 'September';
        $getbulan[10] = 'Oktober';
        $getbulan[11] = 'November';
        $getbulan[12] = 'Desember';
        return $getbulan[(int)$m];
    }
}

function add_date($givendate, $day = 0, $mth = 0, $yr = 0)
{
    $cd = strtotime($givendate);
    $newdate = date('Y-m-d h:i:s', mktime(date('h', $cd), date('i', $cd), date('s', $cd), date('m', $cd) + $mth, date('d', $cd) + $day, date('Y', $cd) + $yr));
    return $newdate;
}

function date_diff_custom($d1, $d2)
{
    $d1 = (is_string($d1) ? strtotime($d1) : $d1);
    $d2 = (is_string($d2) ? strtotime($d2) : $d2);
    $diff_secs = abs($d1 - $d2);
    $base_year = min(date("Y", $d1), date("Y", $d2));
    $diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
    return array("years" => date("Y", $diff) - $base_year, "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1, "months" => date("n", $diff) - 1, "days_total" => floor($diff_secs / (3600 * 24)), "days" => date("j", $diff) - 1, "hours_total" => floor($diff_secs / 3600), "hours" => date("G", $diff), "minutes_total" => floor($diff_secs / 60), "minutes" => (int)date("i", $diff), "seconds_total" => $diff_secs, "seconds" => (int)date("s", $diff));
}

function quotes_cek($string)
{
    $value = trim($string);

    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }

    if (!is_numeric($value)) {
        $value = mysql_real_escape_string($value);
    }
    return $value;
}

function remove_spasi($str)
{
    $str = trim($str);
    return str_replace("%20", " ", $str);
}

function compare_date_greater_than($date_1, $date_2)
{
    if (!is_null($date_1) && !is_null($date_2)) {
        list($year, $month, $day) = explode('-', $date_1);
        $new_date_1 = sprintf('%04d%02d%02d', $year, $month, $day);
        list($year, $month, $day) = explode('-', $date_2);
        $new_date_2 = sprintf('%04d%02d%02d', $year, $month, $day);
        if ($date_2 > $date_1) {
            return false;
        } else {
            return true;
        }
    }
}

function compare_date_less_than($date_1, $date_2)
{
    if (!is_null($date_1) && !is_null($date_2)) {
        list($year, $month, $day) = explode('-', $date_1);
        $new_date_1 = sprintf('%04d%02d%02d', $year, $month, $day);
        list($year, $month, $day) = explode('-', $date_2);
        $new_date_2 = sprintf('%04d%02d%02d', $year, $month, $day);
        if ($date_2 < $date_1) {
            return false;
        } else {
            return true;
        }
    }
}

function compare_dates($date1, $date2)
{
    if (!is_null($date1) && !is_null($date2)) {
        list($month, $day, $year) = split('-', $date1);
        $new_date1 = sprintf('%04d%02d%02d', $year, $month, $day);

        list($month, $day, $year) = split('-', $date2);
        $new_date2 = sprintf('%04d%02d%02d', $year, $month, $day);

        return ($new_date1 > $new_date2);
    }
}

function valid_date($strdate)
{
    $err = array();
    if ((strlen($strdate) < 10) OR (strlen($strdate) > 10)) {
        array_unshift($err, "Masukkan tanggal 'dd-mm-yyyy' Format<br>");
    } else {
        if ((substr_count($strdate, "-")) <> 2) {
            array_unshift($err, "Masukkan tanggal 'dd-mm-yyyy' format<br>");
        } else {
            $pos = strpos($strdate, "-");
            $date = substr($strdate, 0, ($pos));
            $result = ereg("^[0-9]+$", $date, $trashed);
            if (!($result)) {
                array_unshift($err, "Masukkan tanggal valid<br>");
            } else {
                if (($date <= 0) OR ($date > 31)) {
                    array_unshift($err, "Masukkan tanggal valid<br>");
                }
            }

            $month = substr($strdate, ($pos + 1), ($pos));
            if (($month <= 0) OR ($month > 12)) {
                array_unshift($err, "Masukkan bulan yang valid<br>");
            } else {
                $result = ereg("^[0-9]+$", $month, $trashed);
                if (!($result)) {
                    array_unshift($err, "Masukkan bulan yang valid<br>");
                }
            }

            $year = substr($strdate, ($pos + 4), strlen($strdate));
            $result = ereg("^[0-9]+$", $year, $trashed);
            if (!($result)) {
                array_unshift($err, "Masukkan tahun yang valid<br>");
            } else {
                if (($year < 1900) OR ($year > 2300)) {
                    array_unshift($err, "tahun dari 1900-2300<br>");
                }
            }
        }
    }

    if (sizeof($err) > 0) {
        $hasil = array('err' => $err, 'valid' => FALSE);
    } else {
        $hasil = array('err' => '', 'valid' => TRUE);
    }

    return $hasil;
}

function filename_extension($filename)
{
    $pos = strrpos($filename, '.');
    if ($pos === false) {
        return false;
    } else {
        return strtolower(substr($filename, $pos + 1));
    }
}

function kekata($x)
{
    $x = abs($x);
    $angka = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($x < 12) {
        $temp = " " . $angka[$x];
    } else if ($x < 20) {
        $temp = kekata($x - 10) . " belas";
    } else if ($x < 100) {
        $temp = kekata($x / 10) . " puluh" . kekata($x % 10);
    } else if ($x < 200) {
        $temp = " seratus" . kekata($x - 100);
    } else if ($x < 1000) {
        $temp = kekata($x / 100) . " ratus" . kekata($x % 100);
    } else if ($x < 2000) {
        $temp = " seribu" . kekata($x - 1000);
    } else if ($x < 1000000) {
        $temp = kekata($x / 1000) . " ribu" . kekata($x % 1000);
    } else if ($x < 1000000000) {
        $temp = kekata($x / 1000000) . " juta" . kekata($x % 1000000);
    } else if ($x < 1000000000000) {
        $temp = kekata($x / 1000000000) . " milyar" . kekata(fmod($x, 1000000000));
    } else if ($x < 1000000000000000) {
        $temp = kekata($x / 1000000000000) . " trilyun" . kekata(fmod($x, 1000000000000));
    }
    return $temp;
}

function terbilang($x, $style = 4)
{
    if ($x < 0) {
        $hasil = "minus " . trim(kekata($x));
    } else {
        $hasil = trim(kekata($x));
    }
    switch ($style) {
        case 1:
            $hasil = strtoupper($hasil);
            break;
        case 2:
            $hasil = strtolower($hasil);
            break;
        case 3:
            $hasil = ucwords($hasil);
            break;
        default:
            $hasil = ucfirst($hasil);
            break;
    }
    return $hasil;
}

function selisihHari($tgl1, $tgl2)
{
    $pecah1 = explode("-", $tgl1);
    $date1 = $pecah1[2];
    $month1 = $pecah1[1];
    $year1 = $pecah1[0];

    $pecah2 = explode("-", $tgl2);
    $date2 = $pecah2[2];
    $month2 = $pecah2[1];
    $year2 = $pecah2[0];

    $jd1 = GregorianToJD($month1, $date1, $year1);
    $jd2 = GregorianToJD($month2, $date2, $year2);

    $selisih = $jd2 - $jd1;
    return $selisih;
}

function kode_hari($date)
{
    $newdate = new DateTime($date);
    $wk = $newdate->format('w');

    return $wk;
}

function checkTimeLog($u_simpeg, $u_timelog, $u_length = 12, $u_flag = '00')
{
    $note = '';
    if (substr($u_simpeg, 0, $u_length) == substr($u_simpeg, 0, $u_length)) {
        if (substr($u_simpeg, $u_length, strlen($u_flag)) == $u_flag) $note = ' *';
    } else {
        $note = ' **';
    }
    return $note;
}

function selisih($time1 = '', $time2 = '')
{
    $buffer1 = explode(":", $time1);
    $buffer2 = explode(":", $time2);
    $t1 = ((3600 * (int)$buffer1[0]) + (60 * (int)$buffer1[1]) + (int)$buffer1[2]);
    $t2 = ((3600 * (int)$buffer2[0]) + (60 * (int)$buffer2[1]) + (int)$buffer2[2]);
    $s = $t1 - $t2;
    if ($s < 60) {
        return '00:00:' . (($s <= 9) ? '0' . $s : $s);
    } else if ($s >= 60 && $s < 3600) {
        $m = $s % 60;
        if ($m == 0) {
            $m2 = $s / 60;
            return '00:' . (($m2 <= 9) ? '0' . $m2 : $m2) . ':00';
        } else {
            $m2 = ($s - $m) / 60;
            return '00:' . (($m2 <= 9) ? '0' . $m2 : $m2) . ':' . (($m <= 9) ? '0' . $m : $m);
        }
    } else {
        $j = $s % 3600;
        if ($j == 0) {
            return (($j <= 9) ? '0' . $j : $j) . ':00:00';
        } else {
            if ($j >= 60) {
                $j2 = ($s - $j) / 3600;
                $m = $j % 60;
                if ($m == 0) {
                    $m2 = $j / 60;
                    return (($j2 <= 9) ? '0' . $j2 : $j2) . ':' . (($m2 <= 9) ? '0' . $m2 : $m2) . ':00';
                } else {
                    $m2 = ($j - $m) / 60;
                    return (($j2 <= 9) ? '0' . $j2 : $j2) . ':' . (($m2 <= 9) ? '0' . $m2 : $m2) . ':' . (($m <= 9) ? '0' . $m : $m);
                }
            } else {
                $j2 = ($s - $j) / 3600;
                return (($j2 <= 9) ? '0' . $j2 : $j2) . ':00:' . (($j <= 9) ? '0' . $j : $j);
            }
        }
    }
}

function jam_to_detik($time1 = '')
{
    $buffer1 = explode(":", $time1);
    $t1 = ((3600 * (int)$buffer1[0]) + (60 * (int)$buffer1[1]) + (int)$buffer1[2]);
    return $t1;
}

function ambil_detik($time1 = '', $time2 = '')
{
    $buffer1 = explode(":", $time1);
    $buffer2 = explode(":", $time2);
    $t1 = ((3600 * (int)$buffer1[0]) + (60 * (int)$buffer1[1]) + (int)$buffer1[2]);
    $t2 = ((3600 * (int)$buffer2[0]) + (60 * (int)$buffer2[1]) + (int)$buffer2[2]);
    $s = $t1 - $t2;
    return $s;
}

function ambil_jam($s = 0)
{
    if ($s < 60) {
        return '00:00:' . (($s <= 9) ? '0' . $s : $s);
    } else if ($s >= 60 && $s < 3600) {
        $m = $s % 60;
        if ($m == 0) {
            $m2 = $s / 60;
            return '00:' . (($m2 <= 9) ? '0' . $m2 : $m2) . ':00';
        } else {
            $m2 = ($s - $m) / 60;
            return '00:' . (($m2 <= 9) ? '0' . $m2 : $m2) . ':' . (($m <= 9) ? '0' . $m : $m);
        }
    } else {
        $j = $s % 3600;
        if ($j == 0) {
            return (($j <= 9) ? '0' . $j : $j) . ':00:00';
        } else {
            if ($j >= 60) {
                $j2 = ($s - $j) / 3600;
                $m = $j % 60;
                if ($m == 0) {
                    $m2 = $j / 60;
                    return (($j2 <= 9) ? '0' . $j2 : $j2) . ':' . (($m2 <= 9) ? '0' . $m2 : $m2) . ':00';
                } else {
                    $m2 = ($j - $m) / 60;
                    return (($j2 <= 9) ? '0' . $j2 : $j2) . ':' . (($m2 <= 9) ? '0' . $m2 : $m2) . ':' . (($m <= 9) ? '0' . $m : $m);
                }
            } else {
                $j2 = ($s - $j) / 3600;
                return (($j2 <= 9) ? '0' . $j2 : $j2) . ':00:' . (($j <= 9) ? '0' . $j : $j);
            }
        }
    }
}

function hitung_umur($tgl2)
{ //(tanggal sekarang, tanggal sebelumnya)
    $thn1 = date("Y");
    $bln1 = date('m');
    $hr1 = date('d');
    $thn2 = substr($tgl2, 0, 4);
    $bln2 = substr($tgl2, 5, 2);
    $hr2 = substr($tgl2, 8, 2);
    $tahun = $thn1 - $thn2;
    if ($bln1 < $bln2) {
        $tahun = $tahun - 1;
        $bulan = ((int)$bln1 + 12) - (int)$bln2;
        if ($hr1 < $hr2) {
            $bulan = $bulan - 1;
            $shr = ((int)$hr2 - (int)$hr1);
            if ($shr == 30) {
                $shr = 29;
            }
            $hari = 30 - $shr;
        } else {
            $hari = (int)$hr1 - (int)$hr2;
        }
    } else if ($bln1 == $bln2) {
        $bulan = (int)$bln1 - (int)$bln2;
        if ($hr1 < $hr2) {
            $tahun = $tahun - 1;
            $bulan = 11;
            $shr = ((int)$hr2 - (int)$hr1);
            if ($hr2 == 31) $hari = 31 - $shr; else $hari = 30 - $shr;
        } else {
            $hari = (int)$hr1 - (int)$hr2;
        }
    } else {
        $bulan = $bln1 - $bln2;
        if ($hr1 < $hr2) {
            $bulan = $bulan - 1;
            $shr = ((int)$hr2 - (int)$hr1);
            if ($hr2 == 31) $hari = 31 - $shr; else $hari = 30 - $shr;
        } else {
            $hari = (int)$hr1 - (int)$hr2;
        }
    }

    return $tahun . ' Thn<br>' . (($bulan == 0) ? '' : $bulan . ' Bln<br>') . (($hari == 0) ? '' : $hari . ' Hri');
}

function hitung_umur_range($tgl1, $tgl2)
{ //(tanggal sekarang, tanggal sebelumnya)
    $thn1 = substr($tgl1, 0, 4);
    $bln1 = substr($tgl1, 5, 2);
    $hr1 = substr($tgl1, 8, 2);
    $thn2 = substr($tgl2, 0, 4);
    $bln2 = substr($tgl2, 5, 2);
    $hr2 = substr($tgl2, 8, 2);
    $tahun = $thn1 - $thn2;
    if ($bln1 < $bln2) {
        $tahun = $tahun - 1;
        $bulan = ((int)$bln1 + 12) - (int)$bln2;
        if ($hr1 < $hr2) {
            $bulan = $bulan - 1;
            $shr = ((int)$hr2 - (int)$hr1);
            if ($shr == 30) {
                $shr = 29;
            }
            $hari = 30 - $shr;
        } else {
            $hari = (int)$hr1 - (int)$hr2;
        }
    } else if ($bln1 == $bln2) {
        $bulan = (int)$bln1 - (int)$bln2;
        if ($hr1 < $hr2) {
            $tahun = $tahun - 1;
            $bulan = 11;
            $shr = ((int)$hr2 - (int)$hr1);
            if ($hr2 == 31) $hari = 31 - $shr; else $hari = 30 - $shr;
        } else {
            $hari = (int)$hr1 - (int)$hr2;
        }
    } else {
        $bulan = $bln1 - $bln2;
        if ($hr1 < $hr2) {
            $bulan = $bulan - 1;
            $shr = ((int)$hr2 - (int)$hr1);
            if ($hr2 == 31) $hari = 31 - $shr; else $hari = 30 - $shr;
        } else {
            $hari = (int)$hr1 - (int)$hr2;
        }
    }

    return $tahun . ' Thn<br>' . (($bulan == 0) ? '' : $bulan . ' Bln<br>') . (($hari == 0) ? '' : $hari . ' Hri');
}

function tgl_akhir($bulan, $tahun)
{
    if ($bulan == '1' || $bulan == '3' || $bulan == '5' || $bulan == '7' || $bulan == '8' || $bulan == '10' || $bulan == '12') {
        $tgl = '31';
    } else if ($bulan == '2') {
        if ($tahun % 4 == 0) {
            $tgl = '29';
        } else {
            $tgl = '28';
        }
    } else {
        $tgl = '30';
    }
    return $tgl;
}

function aksiPermis($kata, $abjad)
{
    $pos = strpos($kata, $abjad);
    if ($pos === false) {
        $hasil = '';
    } else {
        $hasil = $abjad;
    }

    return $hasil;
}

function akses($url, $usergrup)
{
    $url = removeLastString($url, "/");
    $hasil = null;
    $CI =& get_instance();
    $sql = "SELECT menu_id,menu_desc,menu_link,flagadd,flagedit,flagdelete,flagprint
            FROM grup_action a
            INNER JOIN menu_new b ON b.menu_id=a.id_menu
            WHERE id_level=$usergrup
            and LOWER(menu_link) = '" . strtolower($url) . "' limit 1
				";
                //var_dump($sql);
    $query = $CI->db->query($sql);
    if ($query->num_rows() > 0) {
        $field = $query->row_array();
        $hasil["flagadd"] = $field['flagadd'];
        $hasil["flagedit"] = $field['flagedit'];
        $hasil["flagdelete"] = $field['flagdelete'];
        $hasil["flagprint"] = $field['flagprint'];
    }

    return $hasil;
}

function getValue($select, $from, $where)
{
    $CI =& get_instance();
    $sql = "select " . $select . " as field from " . $from . " where " . $where;
    $query = $CI->db->query($sql);
    $hasil = '';
    if ($query->num_rows() > 0) {
        $field = $query->row_array();
        $hasil = $field['field'];
    }
    return $hasil;
}

function getCount($from, $where)
{
    $CI =& get_instance();
    $sql = "select count(*) as jml from " . $from . " where " . $where;
    //echo $sql;
    $query = $CI->db->query($sql)->row();
    return $query->jml;
}

function log_history($aksi, $tabel, $datalog)
{
    $CI =& get_instance();
    $datalog["updateby"] = $CI->session->userdata('s_id');
    $datalog["updatetime"] = date('Y-m-d H:i:s');
    $datalog["action"] = $aksi;

    //$arr = array_merge($datalog, $databaru);
    $CI->db->insert($tabel . '_log', $datalog);
}

function createLog($msg = null, $sts = null,$suser=null)
{
    $CI =& get_instance();
    if ($suser==null)
    {
        $suser = $CI->session->userdata('s_username');
    }
    $actionlog = array('user' => $suser, 
                    'ipadd' => getRealIpAddr(), 'logtime' => date("Y-m-d H:i:s"), 
                    'logdetail' => $msg, 'info' => $sts);
    $CI->db->insert('actionlog', $actionlog);
}

function lastLog($sts = null)
{
    $CI =& get_instance();
    /*$actionlog = array('user' => $CI->session->userdata('s_username'), 'ipadd' => getRealIpAddr(), 'logtime' => date("Y-m-d H:i:s"), 'logdetail' => $msg, 'info' => $sts);
    $CI->db->insert('goltca', $actionlog);*/
    $CI->db->select('logtime');
    $CI->db->from('goltca');
    $CI->db->where('info', $sts);
    $CI->db->limit(1);
    $CI->db->order_by("logtime", "DESC");
    $query = $CI->db->get();

    if ($query->num_rows() > 0) {
        return date('Y-m-d H:i:s',strtotime($query->row()->logtime));
    } else {
        return date('Y-m-d H:i:s');
    }

}

function jmlPesanBlmBaca()
{
    $CI =& get_instance();
    return $CI->db->where('untuk_id', $CI->session->userdata('s_id'))->where('isread', 0)->count_all_results('pesan_detail');
}

function PesanBlmBaca()
{
    $CI =& get_instance();
    return $CI->db->select("pesan_detail.*,pesan.*,userinfo.name")->from('pesan_detail')->join('pesan', 'pesan_detail.pesan_id=pesan.id')->join('userinfo', 'userinfo.userid=pesan.dari', "left")->where('untuk_id', $CI->session->userdata('s_id'))->where('isread', 0)->limit(5)->get();
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function doAutoBackup()
{
    ini_set('max_execution_time', 3600000);
    ini_set('memory_limit', '-1');
    $CI =& get_instance();
    $sql = "SELECT tid, tipe, deskripsi, dari, sampai FROM tabsensi_toleransi WHERE stat = 1 AND tipe = 2 LIMIT 1";
    $rtol = $CI->db->query($sql);
    $ftol = $rtol->row_array();
    $batas1 = $ftol['dari'];
    $batas2 = $ftol['sampai'];
    $sql = "SELECT nip_pns, tabsensi_harian.nip, tgl_absensi, kd_mesin, kode_unit, datang, pulang, tengah,
				RPAD(CONCAT(tkerja.kuntp,tkerja.kunkom),12,'0') as kunker 
				FROM tdm_absensi_temp 
				LEFT JOIN tabsensi_harian ON (DATE(tdm_absensi_temp.tgl_absensi) = tabsensi_harian.tanggal AND 
												   tdm_absensi_temp.nip_pns = tabsensi_harian.nip) 
				LEFT JOIN tkerja ON (tdm_absensi_temp.nip_pns = tkerja.nip AND tkerja.flag = '')
				ORDER BY nip_pns ASC, tgl_absensi ASC";
    $rscheck = $CI->db->query($sql);
    if ($rscheck->num_rows() > 0) {
        $prevnip = '';
        $prevdate = '';
        foreach ($rscheck->result_array() as $f) {
            $datetime = explode(' ', $f['tgl_absensi']);
            $date = $datetime[0];
            $time = $datetime[1];
            $kd_mesin[0] = substr($f['kd_mesin'], 0, 12);
            $kd_mesin[1] = substr($f['kd_mesin'], 12, 2);
            if (trim($f['nip']) == "") {
                if (($prevnip <> $f['nip_pns']) || ($prevdate <> $date)) {
                    if (!empty($f['nip_pns']) && !empty($f['kunker'])) {
                        $sql = "INSERT INTO tabsensi_harian(nip, tanggal, kode_unit, lengkap) VALUES ('" . $f['nip_pns'] . "', '$date', '" . $f['kunker'] . "', '')";
                        //echo $sql;
                        //$CI->db->query($sql);
                        mysql_query($sql);
                        $f['kode_unit'] = $f['kunker'];
                    }
                }
            }

            if ($time < $batas1) {
                $sql = "UPDATE tabsensi_harian SET datang = '$time', km_datang = '" . $f['kd_mesin'] . "'" . " WHERE nip = '" . $f['nip_pns'] . "' AND tanggal = '$date'" . " AND (" . " (datang = '') OR (ISNULL(datang)) OR" . " (('" . $kd_mesin[0] . "' = '" . $f['kode_unit'] . "') AND ('$time' < datang))" . " )";
                //$CI->db->query($sql);
                mysql_query($sql);
                $sql = "UPDATE tabsensi_harian SET lengkap = CONCAT(lengkap,'$time','_','" . $f['kd_mesin'] . " ')" . " WHERE nip = '" . $f['nip_pns'] . "' AND tanggal = '$date'";
                //$CI->db->query($sql);
                mysql_query($sql);
            } else if ($time > $batas2) {
                $sql = "UPDATE tabsensi_harian SET pulang = '$time', km_pulang = '" . $f['kd_mesin'] . "'" . " WHERE nip = '" . $f['nip_pns'] . "' AND tanggal = '$date'" . " AND (" . " (pulang = '') OR (ISNULL(pulang)) OR" . " (('" . $kd_mesin[0] . "' = '" . $f['kode_unit'] . "') AND ('$time' > pulang))" . " )";
                //$CI->db->query($sql);
                mysql_query($sql);
                $sql = "UPDATE tabsensi_harian SET lengkap = CONCAT(lengkap,'$time','_','" . $f['kd_mesin'] . " ')" . " WHERE nip = '" . $f['nip_pns'] . "' AND tanggal = '$date'";
                //$CI->db->query($sql);
                mysql_query($sql);
            } else {
                $sql = "UPDATE tabsensi_harian SET tengah = '$time', km_tengah = '" . $f['kd_mesin'] . "'" . " WHERE nip = '" . $f['nip_pns'] . "' AND tanggal = '$date'" . " AND (" . " (tengah = '') OR (ISNULL(tengah)) OR" . " ('" . $kd_mesin[0] . "' = '" . $f['kode_unit'] . "')" . " )";
                //$CI->db->query($sql);
                mysql_query($sql);
                $sql = "UPDATE tabsensi_harian SET lengkap = CONCAT(lengkap,'$time','_','" . $f['kd_mesin'] . " ')" . " WHERE nip = '" . $f['nip_pns'] . "' AND tanggal = '$date'";
                //$CI->db->query($sql);
                mysql_query($sql);
            }

            $prevnip = $f['nip_pns'];
            $prevdate = $date;
        }

        $sql = "TRUNCATE tdm_absensi_temp";
        //$CI->db->query($sql);
        mysql_query($sql);
    }
}

function removeLastString($stringInput, $isStringInput)
{
    $check = substr($stringInput, -1);
    if ($isStringInput === $check) {
        return substr($stringInput, 0, -1);
    }
    return $stringInput;
}

function format_bulan_tahun($date)
{ //reformat from yyyy-mm to dd mon yyyy

    $newdate = new DateTime($date);
    $y = $newdate->format('Y');
    $m = $newdate->format('n');
    $d = $newdate->format('j');

    $getbulan = array();
    $getbulan[1] = 'Januari';
    $getbulan[2] = 'Februari';
    $getbulan[3] = 'Maret';
    $getbulan[4] = 'April';
    $getbulan[5] = 'Mei';
    $getbulan[6] = 'Juni';
    $getbulan[7] = 'Juli';
    $getbulan[8] = 'Agustus';
    $getbulan[9] = 'September';
    $getbulan[10] = 'Oktober';
    $getbulan[11] = 'November';
    $getbulan[12] = 'Desember';

    return $getbulan[$m]." ".$y;
}

function format_tanggal_bulan($date)
{ //reformat from yyyy-mm to dd mon yyyy

    $newdate = new DateTime($date);
    $y = $newdate->format('Y');
    $m = $newdate->format('n');
    $d = $newdate->format('j');

    $getbulan = array();
    $getbulan[1] = 'Januari';
    $getbulan[2] = 'Februari';
    $getbulan[3] = 'Maret';
    $getbulan[4] = 'April';
    $getbulan[5] = 'Mei';
    $getbulan[6] = 'Juni';
    $getbulan[7] = 'Juli';
    $getbulan[8] = 'Agustus';
    $getbulan[9] = 'September';
    $getbulan[10] = 'Oktober';
    $getbulan[11] = 'November';
    $getbulan[12] = 'Desember';

    return $d." ".$getbulan[$m];
}

function format_jammenit($data)
{
    $pecah1 = explode(":", $data);
    $jam = $pecah1[0];
    $menit = $pecah1[1];
    //$detik = $pecah1[2];

    return $jam . ":" . $menit;

}

function timeStampDiff($time1, $time2, $precision = 6)
{
    date_default_timezone_set("Asia/Jakarta");
    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
        $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
        $time2 = strtotime($time2);
    }

    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
        $ttime = $time1;
        $time1 = $time2;
        $time2 = $ttime;
    }

    // Set up intervals and diffs arrays
    $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
    $diffs = array();

    foreach ($intervals as $interval) {
        $ttime = strtotime('+1 ' . $interval, $time1);
        $add = 1;
        $looped = 0;
        while ($time2 >= $ttime) {
            $add++;
            $ttime = strtotime("+" . $add . " " . $interval, $time1);
            $looped++;
        }

        $time1 = strtotime("+" . $looped . " " . $interval, $time1);
        $diffs[$interval] = $looped;
    }

    $count = 0;
    $times = array();
    foreach ($diffs as $interval => $value) {
        if ($count >= $precision) {
            break;
        }
        if ($value > 0) {
            if ($value != 1) {
                $interval .= "s";
            }
            $times[] = $value . " " . $interval;
            $count++;
        }
    }

    return implode(", ", $times);
}

/* extract dari data field yang pake pemisah koma
 * SELECT datadept.*,departments.deptname FROM (
    SELECT id, SUBSTRING_INDEX(SUBSTRING_INDEX(dept_id, ',', n), ',', -1) AS dept_id
FROM users
JOIN (
    SELECT @row := @row + 1 AS n FROM
(SELECT 0 UNION ALL
	SELECT 1 UNION ALL
	SELECT 3 UNION ALL
	SELECT 4 UNION ALL
	SELECT 5 UNION ALL
	SELECT 6 UNION ALL
	SELECT 6 UNION ALL
	SELECT 7 UNION ALL
	SELECT 8 UNION ALL
	SELECT 9) t,
(SELECT @row:=0) r ) AS numbers ON CHAR_LENGTH(dept_id) - CHAR_LENGTH(REPLACE(dept_id, ',', ''))  >= n - 1
) AS datadept
LEFT JOIN departments ON departments.deptid=datadept.dept_id
WHERE datadept.id= 166   */

/**
 * @example truncate(-1.49999, 2); // returns -1.49
 * @example truncate(.49999, 3); // returns 0.499
 * @param float $val
 * @param int f
 * @return float
 */
function truncate($val, $f = "0")
{
    if (is_float($val)) {
        $p = strpos($val, '.');
        $val = floatval(substr($val, 0, $p + 1 + $f));
    } else {
        $val = floatval($val);
        $p = strpos($val, '.');
        $val = floatval(substr($val, 0, $p + 1 + $f));
    }
    return $val;
}

function getContentType($url)
{
    $curl = curl_init($url);
    curl_setopt_array($curl, array(CURLOPT_HEADER => true, CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_URL => $url));

    curl_exec($curl); //execute
    $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    curl_close($curl);

    return $contentType;
}

function getContentUrl($url)
{
    $curl = curl_init($url);
    curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_URL => $url));
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $content = curl_exec($curl); //execute

    if(curl_errno($curl))
    {
        $content = 'ERROR: ' . curl_error($curl);
    }

    curl_close($curl);

    return $content;
}

function getListTahun()
{
    for ($kk = 2014; $kk <= 2050; $kk++) {
        $aTahun[$kk] = $kk;
    }
    return $aTahun;
}

function getAktifTahun()
{
    $aTahun = date("Y");
    $CI =& get_instance();
    $CI->db->select("aktivasi_tahun");
    $query = $CI->db->get("company");
    //echo $CI->db->last_query();
    if ($query->num_rows() > 0) {
        $field = $query->row_array();
        $aTahun = $field['aktivasi_tahun'];
    }
    return $aTahun;
}

function createUnitKerjaCheckbox($namediv = 'ukertree')
{
    $CI =& get_instance();
    $menunav = '';
    //$kode_unker = (($CI->session->userdata('s_dept') != '') ? $CI->session->userdata('s_dept') : '');
    //$query = $CI->db->query("SELECT * FROM departments WHERE deptid=$kode_unker ORDER BY deptid ASC");
    $query = $CI->db->query("SELECT * FROM departments where parentid is null ORDER BY deptid ASC");
    foreach ($query->result_array() as $row) {
        if (toogletree($row['deptid']) > 0) {
            $menunav .= "<li data-id='" . $row['deptid'] . "' id='" . $row['deptid'] . "'>";
            $menunav .= $row['deptname'];
            $menunav .= formatTreeU($row['deptid']);
            $menunav .= "</li>";
        } else {
            $menunav .= "<li data-id='" . $row['deptid'] . "' id='" . $row['deptid'] . "'>";
            $menunav .= $row['deptname'];
            $menunav .= "</li>";
        }
    }

    echo '<div class="treescroll">
            <div id="' . $namediv . '">
                <ul>' . $menunav . '</ul>
            </div>
          </div>
        ';
    echo '<script type=\'text/javascript\'>
        $(function() {
   
    $("#' . $namediv . '").jstree({
    \'plugins\': ["checkbox","ui","changed"],
    \'checkbox\': { "three_state" : false, "tie_selection": false, "whole_node": false },
        \'core\': {
            \'themes\': {
                \'name\': \'proton\',
                \'responsive\': true,
                \'icons\': false
            }
        }
    });
        });
    </script>
';
}

function formatTreeU($id_parent)
{
    $CI =& get_instance();
    $sql = "SELECT * from departments
                WHERE parentid = '" . $id_parent . "' and state=1";

    $query = $CI->db->query($sql);
    $menunav = "<ul> ";
    foreach ($query->result_array() as $item) {
        if (toogletree($item['deptid']) > 0) {
            $menunav .= "<li data-id='" . $item['deptid'] . "' id='" . $item['deptid'] . "'>";
            $menunav .= $item['deptname'];
            $menunav .= formatTreeU($item['deptid']);
            $menunav .= "</li>";
        } else {
            $menunav .= "<li data-id='" . $item['deptid'] . "' id='" . $item['deptid'] . "'>";
            $menunav .= $item['deptname'];
            $menunav .= "</li>";
        }
    }

    $menunav .= "</ul>";
    return $menunav;
}

function toogletree($id_parent)
{
    $CI =& get_instance();
    $sql = "SELECT * from departments
                WHERE parentid = '" . $id_parent . "' and state=1";
    $query = $CI->db->query($sql);
    return $query->num_rows();
}


function createAreaCheckbox($namediv = 'areatree')
{
    $CI =& get_instance();
    $menunav = '';
    $kode_area = (($CI->session->userdata('s_area') != '') ? $CI->session->userdata('s_area') : '');
    //if ($kode_area=='') {
    $query = $CI->db->query("SELECT * FROM personnel_area where parent_id is null ORDER BY areaid ASC");
    //} else {
    //     $query = $CI->db->query("SELECT * FROM personnel_area WHERE areaid in ($kode_area) ORDER BY areaid ASC");
    //}
    //echo $CI->db->last_query();
    foreach ($query->result_array() as $row) {
        if (toogleareatree($row['areaid']) > 0) {
            $menunav .= "<li data-id='" . $row['areaid'] . "' id='" . $row['areaid'] . "'>";
            $menunav .= $row['areaname'];
            $menunav .= formatAreaTree($row['areaid']);
            $menunav .= "</li>";
        } else {
            $menunav .= "<li data-id='" . $row['areaid'] . "' id='" . $row['areaid'] . "'>";
            $menunav .= $row['areaname'];
            $menunav .= "</li>";
        }
    }

    echo '<div class="treescroll">
            <div id="' . $namediv . '">
                <ul>' . $menunav . '</ul>
                </div>
         </div>
        ';
    echo '<script type=\'text/javascript\'>
        $(function() {
   
    $("#' . $namediv . '").jstree({
        \'plugins\': ["checkbox","ui","changed"],
        \'checkbox\': { "three_state" : false },
        \'core\': {
            \'themes\': {
                \'name\': \'proton\',
                \'responsive\': true,
                \'icons\': false
            }
        
        }
    });
        });
    </script>
';
}

function createArea($namediv = 'areatree')
{
    $CI =& get_instance();
    $menunav = '';
    $kode_area = (($CI->session->userdata('s_area') != '') ? $CI->session->userdata('s_area') : '');
    //if ($kode_area=='') {
    $query = $CI->db->query("SELECT * FROM personnel_area where parent_id is null ORDER BY areaid ASC");
    //} else {
    //     $query = $CI->db->query("SELECT * FROM personnel_area WHERE areaid in ($kode_area) ORDER BY areaid ASC");
    //}
    //echo $CI->db->last_query();
    foreach ($query->result_array() as $row) {
        if (toogleareatree($row['areaid']) > 0) {
            $menunav .= "<li data-id='" . $row['areaid'] . "' id='" . $row['areaid'] . "'>";
            $menunav .= $row['areaname'];
            $menunav .= formatAreaTree($row['areaid']);
            $menunav .= "</li>";
        } else {
            $menunav .= "<li data-id='" . $row['areaid'] . "' id='" . $row['areaid'] . "'>";
            $menunav .= $row['areaname'];
            $menunav .= "</li>";
        }
    }

    echo '<div class="treescroll">
            <div id="' . $namediv . '">
                <ul>' . $menunav . '</ul>
                </div>
         </div>
        ';
    echo '<script type=\'text/javascript\'>
        $(function() {
   
    $("#' . $namediv . '").jstree({
    \'plugins\': ["ui","changed"],
    \'checkbox\': { "two_state" : true },
        \'core\': {
            \'themes\': {
                \'name\': \'proton\',
                \'responsive\': true,
                \'icons\': false
            }
        
        }
    });
        });
    </script>
';
}

function formatAreaTree($id_parent)
{
    $CI =& get_instance();
    $sql = "SELECT * from personnel_area
                WHERE parent_id = '" . $id_parent . "' ";

    $query = $CI->db->query($sql);
    $menunav = "<ul> ";
    foreach ($query->result_array() as $item) {
        if (toogleareatree($item['areaid']) > 0) {
            $menunav .= "<li data-id='" . $item['areaid'] . "' id='" . $item['areaid'] . "'>";
            $menunav .= $item['areaname'];
            $menunav .= formatAreaTree($item['areaid']);
            $menunav .= "</li>";
        } else {
            $menunav .= "<li data-id='" . $item['areaid'] . "' id='" . $item['areaid'] . "'>";
            $menunav .= $item['areaname'];
            $menunav .= "</li>";
        }
    }

    $menunav .= "</ul>";
    return $menunav;
}

function toogleareatree($id_parent)
{
    $CI =& get_instance();
    $sql = "SELECT * from personnel_area
                WHERE parent_id = '" . $id_parent . "'";
    $query = $CI->db->query($sql);
    return $query->num_rows();
}

//Menu Tree
function createMenutree($namediv = 'menutree')
{
    $CI =& get_instance();
    $menunav = '';

    $query = $CI->db->query("SELECT * FROM menu_new where menu_parent = 0 and menu_status=1 ORDER BY menu_sort ASC");

    //echo $CI->db->last_query();
    foreach ($query->result_array() as $row) {
        if (toogleMenutree($row['menu_id']) > 0) {
            $menunav .= "<li data-id='" . $row['menu_id'] . "' id='" . $row['menu_id'] . "'>";
            $menunav .= $row['menu_desc'];
            $menunav .= formatAreaMenu($row['menu_id']);
            $menunav .= "</li>";
        } else {
            $menunav .= "<li data-id='" . $row['menu_id'] . "' id='" . $row['menu_id'] . "'>";
            $menunav .= $row['menu_desc'];
            $menunav .= "</li>";
        }
    }

    echo '<div class="treescroll">
            <div id="' . $namediv . '">
                <ul>' . $menunav . '</ul>
                </div>
         </div>
        ';
    echo '<script type=\'text/javascript\'>
        $(function() {
   
    $("#' . $namediv . '").jstree({
    \'plugins\': ["checkbox","ui","changed"],
    \'checkbox\': { "three_state" : false },
        \'core\': {
            \'themes\': {
                \'name\': \'proton\',
                \'responsive\': true,
                \'icons\': false
            }
        
        }
    });
        });
    </script>
';
}

function formatAreaMenu($id_parent)
{
    $CI =& get_instance();
    $sql = "SELECT * from menu_new
                WHERE menu_parent = '" . $id_parent . "' and menu_status=1 ";

    $query = $CI->db->query($sql);
    $menunav = "<ul> ";
    foreach ($query->result_array() as $item) {
        if (toogleMenutree($item['menu_id']) > 0) {
            $menunav .= "<li data-id='" . $item['menu_id'] . "' id='" . $item['menu_id'] . "'>";
            $menunav .= $item['menu_desc'];
            $menunav .= formatAreaMenu($item['menu_id']);
            $menunav .= "</li>";
        } else {
            $menunav .= "<li data-id='" . $item['menu_id'] . "' id='" . $item['menu_id'] . "'>";
            $menunav .= $item['menu_desc'];
            $menunav .= "</li>";
        }
    }

    $menunav .= "</ul>";
    return $menunav;
}

function toogleMenutree($id_parent)
{
    $CI =& get_instance();
    $sql = "SELECT * from menu_new
                WHERE menu_parent = '" . $id_parent . "' and menu_status=1 ";
    $query = $CI->db->query($sql);
    return $query->num_rows();
}

function rand_date($min_date, $max_date)
{
    $min_epoch = strtotime($min_date);
    $max_epoch = strtotime($max_date);

    $rand_epoch = rand($min_epoch, $max_epoch);

    return date('Y-m-d H:i:s', $rand_epoch);
}

function konversiGolongan($kode=null)
{
    $kodes = strtoupper($kode);
    $kodef=explode("/", preg_replace('/\s+/', '', $kodes));
    $arrk = array("I"=>1,"II"=>2,"III"=>3,"IV"=>4);
    return isset($arrk[$kodef[0]])?$arrk[$kodef[0]]:"";
}

function konversiEselon($kode=null)
{
    $kodes = strtoupper($kode);
    $kodef=preg_replace('/\s+/', '', $kodes);
    $arrk = array("I.A"=>11,"I.B"=>12,"I.C"=>12,"I.D"=>12,
        "II.A"=>21,"II.B"=>22,"II.C"=>23,"II.D"=>24,
        "III.A"=>31,"III.B"=>32,"III.C"=>33,"III.D"=>34,
        "IV.A"=>41,"IV.B"=>42,"IV.C"=>43,"IV.D"=>44,
        "V.A"=>51,"V.B"=>52,"V.C"=>53,"V.D"=>54,""=>99);
    if (array_key_exists($kodef, $arrk) )
    {
        return $arrk[$kodef];
    } else{
        return 99;
    }

}

function konversiEselonBaru($kode=null)
{
    $kodes = strtoupper($kode);
    $kodef=preg_replace('/\s+/', '', $kodes);
    $arrk = array("I.A"=>5,"I.B"=>5,"I.C"=>5,"I.D"=>5,
        "II.A"=>4,"II.B"=>4,"II.C"=>4,"II.D"=>4,
        "III.A"=>3,"III.B"=>3,"III.C"=>3,"III.D"=>3,
        "IV.A"=>2,"IV.B"=>2,"IV.C"=>2,"IV.D"=>2,
        "V.A"=>1,"V.B"=>1,"V.C"=>1,"V.D"=>1,""=>0);
    if (array_key_exists($kodef, $arrk) )
    {
        return $arrk[$kodef];
    } else{
        return 0;
    }

}

function isEselon2($kode=null)
{
    $arrk = array("01","07","11","12");
    return in_array(substr($kode,0,2),$arrk);
}

function isOrgIn($kode=null)
{
    $arrk = array(
        "0706","0707","0708", //PMD
        "1107","1108","1109","1110", //BPSDM
        "1217","1218","1219","1220","1228","1229","1230" //IPDN
    );

    $arrk2 = array(
        "011"
    );

    if (substr($kode,0,2)=="01") //khusus setjen
    {
        if (in_array(substr($kode,0,3),$arrk2)>0)
        {
            return substr($kode,0,3);
        } else {
            return substr($kode,0,4);
        }

    } else{
        return in_array(substr($kode,0,4),$arrk)>0 ? substr($kode,0,4): substr($kode,0,2);
    }


}

function ArraySortBy($field, &$array, $direction = 'asc')
{
    usort($array, create_function('$a, $b', '
		$a = $a["' . $field . '"];
		$b = $b["' . $field . '"];

		if ($a == $b)
		{
			return 0;
		}

		return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
	'));

    return true;
}

function log_que($sql) {
    $filepath = APPPATH . 'logs/crud-log-' . date('Y-m-d') . '.php';
    $handle = fopen($filepath, "a+");
    //fwrite($handle, $sql." \n Execution Time: ".date("Y-m-d H:i:s")."\n\n");
    //if(preg_match('/INSERT/',$sql)) {
    if (preg_match('/^\s*"?(INSERT|UPDATE|DELETE)\s/i', $sql)) {
        fwrite($handle, $sql." \n Execution Time: ".date("Y-m-d H:i:s")."\n\n");
    }
    fclose($handle);
}

function createListMesin($namediv = 'listmesin')
{
    $CI =& get_instance();
    $menunav = '';
    $kode_area = (($CI->session->userdata('s_area') != '') ? $CI->session->userdata('s_area') : '');

    $sql =  "SELECT a.*,b.areaname,c.deptname
		 from iclock a
		 left join personnel_area b on b.areaid=a.areaid
		 left join departments c on c.deptid=a.iddept
		  order by a.alias";

    //if ($kode_area=='') {
    $query = $CI->db->query($sql);
    //} else {
    //     $query = $CI->db->query("SELECT * FROM personnel_area WHERE areaid in ($kode_area) ORDER BY areaid ASC");
    //}
    //echo $CI->db->last_query();
    foreach ($query->result_array() as $row) {
        $menunav .= "<li data-id='" . $row['sn'] . "' id='" . $row['sn'] . "'>";
        $menunav .= '<label>
                            <input type="checkbox" name="chek_mesin" id="chek_mesin" class="selectedmsin" value="'.$row['sn'].'"  data-id="'.$row['sn'].'"/>
                ';
        $menunav .= $row['alias']." ".$row['areaname'];
        $menunav .= "</label>";
        $menunav .= "</li>";
    }
    echo '<style>
            .columns{  
              -webkit-column-count: 3;
              -webkit-column-gap: 26px; 
              -moz-column-count: 3;
              -moz-column-gap: 26px;  
              column-count: 3;
              column-gap: 26px;
              list-style: none;
              margin: 0;
              padding: 0;
              font-size:11px;
              
              /* Small screen */
              @media all and (max-width: 768px){
                -webkit-column-count: 2;
                -moz-column-count: 3;
                column-count: 3;
              }
              /* Small screen */
              @media all and (max-width: 600px){
                -webkit-column-count: 1;
                -moz-column-count: 1;
                column-count: 1;
              }
            }
            
            .columns ul{
            list-style-type: none;
            }
            .columns li{
              position: relative;
              padding: 7px 7px 6px 0px;
  
          .no-csscolumns &{
            display: block;
            float: left;    
            @media all and (min-width: 769px){
              margin-right: 3.05882%;
              width: 31.29412%;
              &:nth-child(3n){
                margin-right: 0;
              }
              &:nth-child(3n+1){
                clear: left;
              }
            }    
            @media all and (min-width: 600px) and (max-width: 768px){
              margin-right: 2.27671%;
              width: 48.86165%;
              &:nth-child(even){
                margin-right: 0;
              }
              &:nth-child(odd){
                clear: left;
              }
            }
            @media all and (max-width: 600px){
              width: 100%;
            }    
          }
        }
        
        input[type=checkbox]{
          margin-right: 5px;
        }
            </style>';
    echo '<div class="columns">
            <div id="' . $namediv . '">
                <ul>' . $menunav . '</ul>
                </div>
         </div>
        ';
}

function tz_list() {
    $zones_array = array("-12","-11","-10","-9","-9.5","-8","-7","-6","-5","-4.5","-4","-3.5","-3","-2","-1","0",
        "1","2","3","3.5","4","4.5","5","5.5","5.75","6","6.5","7","8","8.5","9","9.5","10","10.5","1","11.5","12","12.75","13","14");
    /*$timestamp = time();
    foreach(timezone_identifiers_list() as $key => $zone) {
        date_default_timezone_set($zone);
        $zones_array[$key]['zone'] = $zone;
        $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
    }*/
    return $zones_array;

    /*$zones_array = array();
    $timestamp = time();
    foreach(timezone_identifiers_list() as $key => $zone) {
        date_default_timezone_set($zone);
        $zones_array[$key]['zone'] = $zone;
        $zones_array[$key]['offset'] = (int) ((int) date('O', $timestamp))/100;
        $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
    }
    return $zones_array;*/
}

function randomString($length = 6) {
    $str = "";
    $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
}

function getSesi()
{
    $CI =& get_instance();
    $CI =& get_instance();
    $CI->db->select("sesi_user");
    $rslt = $CI->db->get("company");
    $sesi = $rslt->row_array();
    $nses = intval($sesi["sesi_user"]);

    return ($nses* 60*1000);

}

function refTHP()
{
    $hasil=0.5;
    $CI =& get_instance();
    //$sql = "SELECT thp FROM company limit 1 ";
    //$query = $CI->db->query($sql);
    $CI->db->select('thp');
    $CI->db->limit(1);
    $query = $CI->db->get('company');
    if ($query->num_rows() > 0) {
        $field = $query->row_array();
        $hasil = ($field['thp']/100);

    }

    return $hasil;
}

function refPLT()
{
    $hasil=0.2;
    $CI =& get_instance();
    $CI->db->select('plt');
    $CI->db->limit(1);
    $query = $CI->db->get('company');
    //$sql = "SELECT plt FROM company limit 1 ";
    //$query = $CI->db->query($sql);
    if ($query->num_rows() > 0) {
        $field = $query->row_array();
        $hasil = ($field['plt']/100);
    }

    return $hasil;
}

function refTHPCP()
{
    $hasil=0.8;
    $CI =& get_instance();
    //$sql = "SELECT thp FROM company limit 1 ";
    //$query = $CI->db->query($sql);
    $CI->db->select('cpns');
    $CI->db->limit(1);
    $query = $CI->db->get('company');
    if ($query->num_rows() > 0) {
        $field = $query->row_array();
        $hasil = ($field['cpns']/100);

    }

    return $hasil;
}

function refTHPJFT()
{
    $hasil=1;
    $CI =& get_instance();
    //$sql = "SELECT thp FROM company limit 1 ";
    //$query = $CI->db->query($sql);
    $CI->db->select('jft_non_aktif');
    $CI->db->limit(1);
    $query = $CI->db->get('company');
    if ($query->num_rows() > 0) {
        $field = $query->row_array();
        $hasil = ($field['jft_non_aktif']/100);

    }

    return $hasil;
}

function convertToArray($stringDel)
{
    if (is_array($stringDel)) return $stringDel;
    if (strlen($stringDel)==0) return array();

    $findme   = ',';
    $pos = strpos($stringDel, $findme);

    if ($pos === false) {
        return array($stringDel);
    } else {
        return explode(',',$stringDel);
    }
}
function listPager()
{
    return array(
        '10' => '10',
        '25' => '25',
        '50' => '50',
        '100' => '100',
        '500' => '500',
        '1000' => '1.000',
        '2000' => '2.000',
        '3000' => '3.000',
        '4000' => '4.000',
        '5000' => '5.000'
    );
}

function getListJadwal()
{

    $CI =& get_instance();
    $CI->db->where('state',1);
    $query = $CI->db->get('master_shift');

    foreach ($query->result() as $row) {
        $aTahun[$row->id_shift] = $row->name_shift;
    }

    return $aTahun;
}

function apipostdata($url,$withttps=false,$array=null){
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handle, CURLOPT_POST, 1);

    if ($withttps){
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
    }
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $array);
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);
    return $buffer;
}

function refUpacara()
{
    $hasil=2;
    $CI =& get_instance();
    $CI->db->select('upacara');
    $CI->db->limit(1);
    $query = $CI->db->get('company');
    if ($query->num_rows() > 0) {
        $field = $query->row_array();
        $hasil = $field['upacara'];
    }

    return $hasil;
}

function namaTableTunjangan($datestart)
{
    $hasil='mastertunjangan';
    $CI =& get_instance();
    $CI->db->select('name_table');
    $CI->db->limit(1);
    $CI->db->where("'".$datestart."' BETWEEN awal AND akhir",null,False);
    $query = $CI->db->get('m_tbl_tunjangan');
    if ($query->num_rows() > 0) {
        $field = $query->row_array();
        $hasil = $field['name_table'];
    }
    //echo $CI->db->last_query();
    return $hasil;
}

function sendsmsviaapi($nohp,$pesan)
{
    $url = "http://103.16.199.187/masking/send_post.php";

    $rtn=true;
    $rows = array (
    'username' => 'data2017',
    'password' => 'Subbagdata-2010',
    'hp' => $nohp,
    'message' => $pesan
    );

    $curl = curl_init();

    curl_setopt( $curl, CURLOPT_URL,  $url );
    curl_setopt( $curl, CURLOPT_POST, TRUE  );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $curl, CURLOPT_POSTFIELDS,     http_build_query($rows) );
    curl_setopt( $curl, CURLOPT_HEADER,  FALSE );
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);

    $htm = curl_exec($curl);

    if(curl_errno($curl) !== 0) {
        //error_log('cURL error when connecting to ' . $url . ': ' . curl_error($curl)); 
        log_message('error', 'SMS Masking: '.curl_error($curl));
        $rtn=false;
    }
    curl_close($curl);
    //print_r($htm);
    return $rtn;
}
?>