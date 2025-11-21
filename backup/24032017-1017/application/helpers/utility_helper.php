<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  	
	function indo_date($date) { //reformat from yyyy-mm-dd to dd mon yyyy
		
		$newdate = new DateTime($date);
		$y = $newdate->format('Y');
		$m = $newdate->format('n');
		$d = $newdate->format('j');
		$wk = $newdate->format('w');
		
		$getbulan = array ();
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

		$gethari = array ();
		$gethari[0] = 'Minggu';
		$gethari[1] = 'Senin';$gethari[2] = 'Selasa';$gethari[3] = 'Rabu';
		$gethari[4] = 'Kamis';$gethari[5] = 'Jumat';$gethari[6] = 'Sabtu';

		return $gethari[$wk]. ", ". $d ." ". $getbulan[$m] ." ". $y;
	}

    function indo_full_date($date) { //reformat from yyyy-mm-dd to dd mon yyyy

    $newdate = new DateTime($date);
    $y = $newdate->format('Y');
    $m = $newdate->format('n');
    $d = $newdate->format('j');
    $wk = $newdate->format('w');
    $jam = $newdate->format('H:n:s');

    $getbulan = array ();
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

    $gethari = array ();
    $gethari[0] = 'Minggu';
    $gethari[1] = 'Senin';$gethari[2] = 'Selasa';$gethari[3] = 'Rabu';
    $gethari[4] = 'Kamis';$gethari[5] = 'Jumat';$gethari[6] = 'Sabtu';

    return $gethari[$wk]. ", ". $d ." ". $getbulan[$m] ." ". $y." ".$jam;
}

	function hariIndo($enDay)
    {
        $lang['Monday']						= 'Senin';
        $lang['Tuesday']					= 'Selasa';
        $lang['Wednesday']					= 'Rabu';
        $lang['Thursday']					= 'Kamis';
        $lang['Friday']						= 'Jumat';
        $lang['Saturday']					= 'Sabtu';
        $lang['Sunday']						= 'Minggu';
        $lang['Mon']						= 'Sen';
        $lang['Tue']						= 'Sel';
        $lang['Wed']						= 'Rab';
        $lang['Thu']						= 'Kam';
        $lang['Fri']						= 'Jum';
        $lang['Sat']						= 'Sab';
        $lang['Sun']						= 'Min';

        return $lang[$enDay];
    }

	function tgl_ind_to_eng($tgl) {
		$xreturn_ = '';
		if (trim($tgl) != '' && $tgl != '00-00-0000') {
			$tgl_eng=substr($tgl,6,4)."-".substr($tgl,3,2)."-".substr($tgl,0,2);
			$xreturn_ = $tgl_eng;
		}
		return $xreturn_;
	}

	function tgl_eng_to_ind($tgl) {
		$xreturn_ = '';
		if (trim($tgl) != '' AND $tgl != '0000-00-00') { 
			$tgl_ind=substr($tgl,8,2)."-".substr($tgl,5,2)."-".substr($tgl,0,4);
			$xreturn_ = $tgl_ind;
		}
		return $xreturn_;
	}

    function dmyToymd($tgl) {
        $xreturn_ = '';
        if (trim($tgl) != '' AND $tgl != '00-00-0000') {
            $pcs = explode("-", $tgl);
            $xreturn_=$pcs[2]."-".$pcs[1]."-".$pcs[0];
        }
        return $xreturn_;
    }

    function ymdTodmy($tgl) {
        $xreturn_ = '';
        if (trim($tgl) != '' AND $tgl != '0000-00-00') {
            $pcs = explode("-", $tgl);
            $xreturn_=$pcs[2]."-".$pcs[1]."-".$pcs[0];
        }
        return $xreturn_;
    }

    function hariToInd($namahari) {
        $dayList = array(
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu'
        );


    return $dayList[$namahari];
}
	
	function format_angka($angka) {
		$hasil =  number_format($angka,0, ",", ".");
		return $hasil;
	}
	
	function format_date_ind($tgl){
		if (trim($tgl) != ''AND $tgl != '0000-00-00') {
			$d = substr($tgl,8,2);
			$m = substr($tgl,5,2);
			$y = substr($tgl,0,4);
			$getbulan = array ();
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
			
			$tanggal = $d." ".$getbulan[(int)$m]." ".$y;
			return $tanggal ;
		}
	}
	
	function format_date_singkat($tgl){
		if (trim($tgl) != ''AND $tgl != '0000-00-00') {
			$d = substr($tgl,8,2);
			$m = substr($tgl,5,2);
			$y = substr($tgl,0,4);
			$getbulan = array ();
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
			
			$tanggal = $d." ".$getbulan[(int)$m]." ".$y;
			return $tanggal ;
		}
	}
	
	function nama_bulan_ind($m){
		if (trim($m) != '' AND $m != '0') {
			$getbulan = array ();
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
	
	function add_date($givendate,$day=0,$mth=0,$yr=0) {
		$cd = strtotime($givendate);
		$newdate = date('Y-m-d h:i:s', mktime(date('h',$cd),
		date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
		date('d',$cd)+$day, date('Y',$cd)+$yr));
		return $newdate;
    }
	
	function date_diff_custom($d1, $d2){
		$d1 = (is_string($d1) ? strtotime($d1) : $d1);
		$d2 = (is_string($d2) ? strtotime($d2) : $d2);
		$diff_secs = abs($d1 - $d2);
		$base_year = min(date("Y", $d1), date("Y", $d2));
		$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
		return array(
			"years" => date("Y", $diff) - $base_year,
			"months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
			"months" => date("n", $diff) - 1,
			"days_total" => floor($diff_secs / (3600 * 24)),
			"days" => date("j", $diff) - 1,
			"hours_total" => floor($diff_secs / 3600),
			"hours" => date("G", $diff),
			"minutes_total" => floor($diff_secs / 60),
			"minutes" => (int) date("i", $diff),
			"seconds_total" => $diff_secs,
			"seconds" => (int) date("s", $diff)
		);
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
		$str=trim($str);
		return  str_replace("%20"," ",$str); 
	}
	
	function compare_date_greater_than($date_1,$date_2) {
		if (! is_null($date_1) && ! is_null($date_2)) {
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
	
	function compare_date_less_than($date_1,$date_2) {
		if (! is_null($date_1) && ! is_null($date_2)) {
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
	
	function compare_dates($date1, $date2) {
		if (! is_null($date1) && ! is_null($date2)) {
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
		if((strlen($strdate)<10) OR (strlen($strdate)>10)){
			array_unshift($err,"Masukkan tanggal 'dd-mm-yyyy' Format<br>");
		}
		else{
			if((substr_count($strdate,"-"))<>2){
				array_unshift($err,"Masukkan tanggal 'dd-mm-yyyy' format<br>");
			} else{
				$pos = strpos($strdate,"-");
				$date = substr($strdate,0,($pos));
				$result = ereg("^[0-9]+$",$date,$trashed);
				if(!($result)){
					array_unshift($err,"Masukkan tanggal valid<br>");
				}
				else {
					if(($date<=0)OR($date>31)){
						array_unshift($err,"Masukkan tanggal valid<br>");
					}
				}
				
				$month=substr($strdate,($pos+1),($pos));
				if(($month<=0) OR ($month>12)){
					array_unshift($err, "Masukkan bulan yang valid<br>");
				}
				else{
					$result=ereg("^[0-9]+$",$month,$trashed);
					if(!($result)){
						array_unshift($err, "Masukkan bulan yang valid<br>");
					}
				}
				
				$year= substr ($strdate,($pos+4),strlen($strdate));
				$result=ereg("^[0-9]+$",$year,$trashed);
				if(!($result)){
					array_unshift($err, "Masukkan tahun yang valid<br>");
				}
				else{
					if(($year < 1900) OR ($year > 2300)){
						array_unshift($err, "tahun dari 1900-2300<br>");
					}
				}
			}
		}
		
		if (sizeof($err) > 0){
			$hasil = array (
				'err' => $err,
				'valid'=> FALSE
			);
		} else {
			$hasil = array (
				'err' => '',
				'valid'=>TRUE
			);
		}
		 
		return $hasil;
	}	
	
	function filename_extension($filename) {
		$pos = strrpos($filename, '.');
		if($pos===false) {
			return false;
		} else {
			return strtolower(substr($filename, $pos+1));
		}
	}
	
	function kekata($x) {
		$x = abs($x);
		$angka = array("", "satu", "dua", "tiga", "empat", "lima",
		"enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($x <12) {
			$temp = " ". $angka[$x];
		} else if ($x <20) {
			$temp = kekata($x - 10). " belas";
		} else if ($x <100) {
			$temp = kekata($x/10)." puluh". kekata($x % 10);
		} else if ($x <200) {
			$temp = " seratus" . kekata($x - 100);
		} else if ($x <1000) {
			$temp = kekata($x/100) . " ratus" . kekata($x % 100);
		} else if ($x <2000) {
			$temp = " seribu" . kekata($x - 1000);
		} else if ($x <1000000) {
			$temp = kekata($x/1000) . " ribu" . kekata($x % 1000);
		} else if ($x <1000000000) {
			$temp = kekata($x/1000000) . " juta" . kekata($x % 1000000);
		} else if ($x <1000000000000) {
			$temp = kekata($x/1000000000) . " milyar" . kekata(fmod($x,1000000000));
		} else if ($x <1000000000000000) {
			$temp = kekata($x/1000000000000) . " trilyun" . kekata(fmod($x,1000000000000));
		}      
        return $temp;
	}
	
	function terbilang($x, $style=4) {
		if($x<0) {
			$hasil = "minus ". trim(kekata($x));
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
	
	function selisihHari($tgl1,$tgl2)
	{
		$pecah1 = explode("-", $tgl1);
		$date1 = $pecah1[2];
		$month1 = $pecah1[1];
		$year1 = $pecah1[0];
		
		$pecah2 = explode("-", $tgl2);
		$date2 = $pecah2[2];
		$month2 = $pecah2[1];
		$year2 =  $pecah2[0];
		
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
	
	function checkTimeLog($u_simpeg, $u_timelog, $u_length = 12, $u_flag = '00') {
		$note = '';
		if (substr($u_simpeg,0,$u_length) == substr($u_simpeg,0,$u_length)) {
			if (substr($u_simpeg,$u_length,strlen($u_flag)) == $u_flag)
				$note = ' *';
		} else {
			$note = ' **';
		}
		return $note;
	}

	function selisih($time1 = '', $time2 = '') {
		$buffer1 = explode(":", $time1);
		$buffer2 = explode(":", $time2);
		$t1 = ((3600 * (int)$buffer1[0]) + (60 * (int)$buffer1[1]) + (int)$buffer1[2]) ;
		$t2 = ((3600 * (int)$buffer2[0]) + (60 * (int)$buffer2[1]) + (int)$buffer2[2]) ;
		$s = $t1 - $t2;
		if($s < 60){
			return '00:00:'.(($s<=9)?'0'.$s:$s);
		}else if ($s >= 60 && $s < 3600){
			$m = $s%60;
			if($m==0){
				$m2 = $s/60;
				return '00:'.(($m2<=9)?'0'.$m2:$m2).':00';
			}else{
				$m2 = ($s - $m)/60;
				return '00:'.(($m2<=9)?'0'.$m2:$m2).':'.(($m<=9)?'0'.$m:$m);
			}
		}else{
			$j = $s%3600;
			if($j==0){
				return (($j<=9)?'0'.$j:$j).':00:00';
			}else{
				if($j >= 60){
					$j2 = ($s-$j) / 3600;
					$m = $j%60;
					if($m==0){
						$m2 = $j/60;
						return (($j2<=9)?'0'.$j2:$j2).':'.(($m2<=9)?'0'.$m2:$m2).':00';
					}else{
						$m2 = ($j - $m)/60;
						return (($j2<=9)?'0'.$j2:$j2).':'.(($m2<=9)?'0'.$m2:$m2).':'.(($m<=9)?'0'.$m:$m);
					}
				}else{
					$j2 = ($s-$j) / 3600;
					return (($j2<=9)?'0'.$j2:$j2).':00:'.(($j<=9)?'0'.$j:$j);
				}
			}
		}
	}
	
	function jam_to_detik($time1 = '') {
		$buffer1 = explode(":", $time1);
		$t1 = ((3600 * (int)$buffer1[0]) + (60 * (int)$buffer1[1]) + (int)$buffer1[2]) ;
		return $t1;
	}
	
	function ambil_detik($time1 = '',$time2 = '') {
		$buffer1 = explode(":", $time1);
		$buffer2 = explode(":", $time2);
		$t1 = ((3600 * (int)$buffer1[0]) + (60 * (int)$buffer1[1]) + (int)$buffer1[2]) ;
		$t2 = ((3600 * (int)$buffer2[0]) + (60 * (int)$buffer2[1]) + (int)$buffer2[2]) ;
		$s = $t1 - $t2;
		return $s;
	}
	
	function ambil_jam($s = 0) {
		if($s < 60){
			return '00:00:'.(($s<=9)?'0'.$s:$s);
		}else if ($s >= 60 && $s < 3600){
			$m = $s%60;
			if($m==0){
				$m2 = $s/60;
				return '00:'.(($m2<=9)?'0'.$m2:$m2).':00';
			}else{
				$m2 = ($s - $m)/60;
				return '00:'.(($m2<=9)?'0'.$m2:$m2).':'.(($m<=9)?'0'.$m:$m);
			}
		}else{
			$j = $s%3600;
			if($j==0){
				return (($j<=9)?'0'.$j:$j).':00:00';
			}else{
				if($j >= 60){
					$j2 = ($s-$j) / 3600;
					$m = $j%60;
					if($m==0){
						$m2 = $j/60;
						return (($j2<=9)?'0'.$j2:$j2).':'.(($m2<=9)?'0'.$m2:$m2).':00';
					}else{
						$m2 = ($j - $m)/60;
						return (($j2<=9)?'0'.$j2:$j2).':'.(($m2<=9)?'0'.$m2:$m2).':'.(($m<=9)?'0'.$m:$m);
					}
				}else{
					$j2 = ($s-$j) / 3600;
					return (($j2<=9)?'0'.$j2:$j2).':00:'.(($j<=9)?'0'.$j:$j);
				}
			}
		}
	}
	
	function hitung_umur($tgl2) { //(tanggal sekarang, tanggal sebelumnya)
		$thn1=date("Y");
		$bln1=date('m');
		$hr1=date('d');
		$thn2=substr($tgl2,0,4);
		$bln2=substr($tgl2,5,2);
		$hr2=substr($tgl2,8,2);
		$tahun=$thn1-$thn2;
		if ($bln1<$bln2){
			$tahun=$tahun-1;
			$bulan=((int)$bln1+12)-(int)$bln2;
			if ($hr1 < $hr2){
				$bulan=$bulan-1;
				$shr = ((int)$hr2 - (int)$hr1);
				if($shr==30){ $shr = 29;}
				$hari = 30 - $shr;
			}else{
				$hari = (int)$hr1 - (int)$hr2; 
			}
		}else if($bln1==$bln2){
			$bulan=(int)$bln1-(int)$bln2;
			if ($hr1 < $hr2){
				$tahun=$tahun-1;
				$bulan=11;
				$shr = ((int)$hr2 - (int)$hr1);
				if($hr2==31)$hari = 31 - $shr;
				else $hari = 30 - $shr;
			}else{
				$hari = (int)$hr1 - (int)$hr2; 
			}
		}else{
			$bulan=$bln1-$bln2;
			if ($hr1 < $hr2){
				$bulan=$bulan-1;
				$shr = ((int)$hr2 - (int)$hr1);
				if($hr2==31)$hari = 31 - $shr;
				else $hari = 30 - $shr;
			}else{
				$hari = (int)$hr1 - (int)$hr2; 
			}
		}
		
		return $tahun.' Thn<br>'.(($bulan==0)?'':$bulan.' Bln<br>').(($hari==0)?'':$hari.' Hri');
	}
	
	function hitung_umur_range($tgl1,$tgl2) { //(tanggal sekarang, tanggal sebelumnya)
		$thn1=substr($tgl1,0,4);
		$bln1=substr($tgl1,5,2);
		$hr1=substr($tgl1,8,2);
		$thn2=substr($tgl2,0,4);
		$bln2=substr($tgl2,5,2);
		$hr2=substr($tgl2,8,2);
		$tahun=$thn1-$thn2;
		if ($bln1<$bln2){
			$tahun=$tahun-1;
			$bulan=((int)$bln1+12)-(int)$bln2;
			if ($hr1 < $hr2){
				$bulan=$bulan-1;
				$shr = ((int)$hr2 - (int)$hr1);
				if($shr==30){ $shr = 29;}
				$hari = 30 - $shr;
			}else{
				$hari = (int)$hr1 - (int)$hr2; 
			}
		}else if($bln1==$bln2){
			$bulan=(int)$bln1-(int)$bln2;
			if ($hr1 < $hr2){
				$tahun=$tahun-1;
				$bulan=11;
				$shr = ((int)$hr2 - (int)$hr1);
				if($hr2==31)$hari = 31 - $shr;
				else $hari = 30 - $shr;
			}else{
				$hari = (int)$hr1 - (int)$hr2; 
			}
		}else{
			$bulan=$bln1-$bln2;
			if ($hr1 < $hr2){
				$bulan=$bulan-1;
				$shr = ((int)$hr2 - (int)$hr1);
				if($hr2==31)$hari = 31 - $shr;
				else $hari = 30 - $shr;
			}else{
				$hari = (int)$hr1 - (int)$hr2; 
			}
		}
		
		return $tahun.' Thn<br>'.(($bulan==0)?'':$bulan.' Bln<br>').(($hari==0)?'':$hari.' Hri');
	}
	
	function tgl_akhir($bulan,$tahun)
	{
		if($bulan=='1' || $bulan=='3' || $bulan=='5' || $bulan=='7' || $bulan=='8' || $bulan=='10' || $bulan=='12')
		{
			$tgl = '31';
		}else if ($bulan=='2'){
			if($tahun%4==0){
				$tgl = '29';
			}else{
				$tgl = '28';
			}
		}else{
			$tgl='30';
		}
		return $tgl;
	}
	
	function aksiPermis($kata,$abjad)
	{
		$pos = strpos($kata,$abjad);
		if($pos === false){
			$hasil = '';
		}else{
			$hasil = $abjad;
		}
		
		return $hasil;
	}
	
	function akses($url,$user,$abjad)
	{
        $url=removeLastString($url,"/");

		$CI =& get_instance();
		$sql = "select a.aksi
				from tb_hak_akses a
				left join tmenu b
				on (a.id_menu = b.menuid)
				where a.id_user = '".$user."'
				and b.linkaction2 = '".$url."'
				";
		$query = $CI->db->query($sql);
        //echo $CI->db->last_query();
		$kata ='';
		if($query->num_rows() > 0){
			$field = $query->row_array();
			$kata = $field['aksi'];
		}
		
		$pos = strpos($kata,$abjad);
		$hasil='';
		if($pos === false){
			$hasil = '';
		}else{
			$hasil = $abjad;
		}
		
		return $hasil;
	}
	
	function getValue($select,$from,$where)
	{
		$CI =& get_instance();
		$sql = "select ".$select." as field from ".$from." where ".$where;
		$query = $CI->db->query($sql);
		$hasil ='';
		if($query->num_rows() > 0){
			$field = $query->row_array();
			$hasil = $field['field'];
		}
		return $hasil;
	}

	function getCount($from,$where)
	{
		$CI =& get_instance();
		$sql = "select count(*) as jml from ".$from." where ".$where;
		//echo $sql;
		$query = $CI->db->query($sql)->row();
		return $query->jml;
	}
	
	function log_history($aksi,$tabel,$datalog)
	{
		$CI =& get_instance();
        $databaru["updateby"]=$CI->session->userdata('s_id');
        $databaru["updatetime"]=date('Y-m-d H:i:s');
        $databaru["action"]=$aksi;

        $arr = array_merge($datalog,$databaru);
		$CI->db->insert($tabel.'_log',$arr);
	}

    function createLog($msg=null,$sts=null)
    {
        $CI =& get_instance();
        $actionlog = array(
            'user'			=> $CI->session->userdata('s_username'),
            'ipadd'			=> getRealIpAddr(),
            'logtime'		=> date("Y-m-d H:i:s"),
            'logdetail'		=> $msg,
            'info'			=> $sts
        );
        $CI->db->insert('goltca', $actionlog);
    }

    function jmlPesanBlmBaca()
    {
        $CI =& get_instance();
        return $CI->db
            ->where('untuk_id', $CI->session->userdata('s_id'))
            ->where('isread', 0)
            ->count_all_results('pesan_detail');
    }

    function PesanBlmBaca()
    {
        $CI =& get_instance();
        return $CI->db
            ->select("pesan_detail.*,pesan.*,userinfo.name")
            ->from('pesan_detail')
            ->join('pesan','pesan_detail.pesan_id=pesan.id')
            ->join('userinfo','userinfo.userid=pesan.dari',"left")
            ->where('untuk_id', $CI->session->userdata('s_id'))
            ->where('isread', 0)
            ->limit(5)
            ->get();
    }
	
	function getRealIpAddr()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	function doAutoBackup()
	{
		ini_set('max_execution_time', 3600000);
		ini_set('memory_limit','-1');
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
				$datetime = explode(' ',$f['tgl_absensi']);
				$date = $datetime[0];
				$time = $datetime[1];
				$kd_mesin[0] = substr($f['kd_mesin'],0,12);
				$kd_mesin[1] = substr($f['kd_mesin'],12,2);
				if (trim($f['nip']) == "") {	
					if (($prevnip <> $f['nip_pns']) || ($prevdate <> $date)) {
						if (!empty($f['nip_pns']) && !empty($f['kunker'])) {
							$sql = "INSERT INTO tabsensi_harian(nip, tanggal, kode_unit, lengkap) VALUES ('".$f['nip_pns']."', '$date', '".$f['kunker']."', '')";
						//echo $sql;
							//$CI->db->query($sql);
							mysql_query($sql);
							$f['kode_unit'] = $f['kunker'];
						}
					}
				}
				
				if ($time < $batas1) {
					$sql = "UPDATE tabsensi_harian SET datang = '$time', km_datang = '".$f['kd_mesin']."'".
						" WHERE nip = '".$f['nip_pns']."' AND tanggal = '$date'".
						" AND (".
						" (datang = '') OR (ISNULL(datang)) OR".
						" (('".$kd_mesin[0]."' = '".$f['kode_unit']."') AND ('$time' < datang))".
						" )";
					//$CI->db->query($sql);
					mysql_query($sql);
					$sql = "UPDATE tabsensi_harian SET lengkap = CONCAT(lengkap,'$time','_','".$f['kd_mesin']." ')".
						" WHERE nip = '".$f['nip_pns']."' AND tanggal = '$date'";
					//$CI->db->query($sql);
					mysql_query($sql);
				} else if ($time > $batas2) {
					$sql = "UPDATE tabsensi_harian SET pulang = '$time', km_pulang = '".$f['kd_mesin']."'".
						" WHERE nip = '".$f['nip_pns']."' AND tanggal = '$date'".
						" AND (".
						" (pulang = '') OR (ISNULL(pulang)) OR".
						" (('".$kd_mesin[0]."' = '".$f['kode_unit']."') AND ('$time' > pulang))".
						" )";
					//$CI->db->query($sql);
					mysql_query($sql);
					$sql = "UPDATE tabsensi_harian SET lengkap = CONCAT(lengkap,'$time','_','".$f['kd_mesin']." ')".
						" WHERE nip = '".$f['nip_pns']."' AND tanggal = '$date'";
					//$CI->db->query($sql);
					mysql_query($sql);
				} else {
					$sql = "UPDATE tabsensi_harian SET tengah = '$time', km_tengah = '".$f['kd_mesin']."'".
						" WHERE nip = '".$f['nip_pns']."' AND tanggal = '$date'".
						" AND (".
						" (tengah = '') OR (ISNULL(tengah)) OR".
						" ('".$kd_mesin[0]."' = '".$f['kode_unit']."')".
						" )";
					//$CI->db->query($sql);
					mysql_query($sql);
					$sql = "UPDATE tabsensi_harian SET lengkap = CONCAT(lengkap,'$time','_','".$f['kd_mesin']." ')".
						" WHERE nip = '".$f['nip_pns']."' AND tanggal = '$date'";
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

    function removeLastString($stringInput,$isStringInput)
    {
        $check = substr($stringInput, -1);
        if ($isStringInput===$check)
        {
            return substr($stringInput , 0, -1);
        }
        return $stringInput;
    }

    function format_bulan_tahun($date) { //reformat from yyyy-mm to dd mon yyyy

    $newdate = new DateTime(date("Y")."-".$date);
    $m = $newdate->format('n');
    $d = $newdate->format('j');

    $getbulan = array ();
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

    return $d ." ". $getbulan[$m];
    }

    function format_jammenit($data)
    {
        $pecah1 = explode(":", $data);
        $jam = $pecah1[0];
        $menit = $pecah1[1];
        //$detik = $pecah1[2];

        return $jam.":".$menit;

    }

    function timeStampDiff($time1, $time2, $precision = 6) {
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
        $intervals = array('year','month','day','hour','minute','second');
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
    function truncate($val, $f="0")
    {
        if (is_float($val))
        {
            $p = strpos($val, '.');
            $val = floatval(substr($val, 0, $p + 1 + $f));
        } else {
            $val=floatval($val);
            $p = strpos($val, '.');
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }
        return $val;
    }

    function getContentType($url)
    {
        $curl = curl_init($url);
        curl_setopt_array( $curl, array(
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL => $url ) );

        curl_exec( $curl ); //execute
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close( $curl );

        return $contentType;
    }
?>