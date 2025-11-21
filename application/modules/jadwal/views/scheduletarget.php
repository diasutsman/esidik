<?php 
	ini_set('max_execution_time', 0);
	ini_set('default_socket_timeout', 6000);
		
	// URL
	$url = "https://smsgw.sprintasia.net/api/msg.php";
	//echo $url;
	
	$params = array(
		"u" => "R0pegG4g",
		"p" => "H5BtJd30Lq",
		"d" => "081312379627",
		"m" => "TESTING SMS JAM MASUK"
	);
	
	$field_string = http_build_query($params);
	$curl = curl_init();
	
	curl_setopt( $curl, CURLOPT_URL,            $url          );
	curl_setopt( $curl, CURLOPT_HEADER,         FALSE         );
	curl_setopt( $curl, CURLOPT_POST,           TRUE          );
	curl_setopt( $curl, CURLOPT_POSTFIELDS,     $field_string );
	curl_setopt( $curl, CURLOPT_TIMEOUT,        120           );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE          );
	
	$htm = curl_exec($curl);
	$err = curl_errno($curl);
	$inf = curl_getinfo($curl);
	
	curl_close($curl);

		
?>