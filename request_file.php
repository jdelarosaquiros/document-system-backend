<?php
/*
 This function requests the contents of a file from the external server
 given the id of the file.
 It saves the file in a temporary directory and logs to the database that
 the request was successful. 
*/

include_once("functions.php");

function request_file($sid, $uid, $fid){

	$dblink=db_connect("log_system");
		
	$apiURL="https://cs4743.professorvaladez.com/api/";
	$data = "sid=$sid&uid=$uid&fid=$fid";
	$endpoint = "request_file";
	
	$ch = curl_init($apiURL.$endpoint);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data)
	));
	
	$requestDate=date("Y-m-d_H:i:s");
	$time_start = microtime(true);
	$result = curl_exec($ch);
	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start) / 60;
	curl_close($ch);
	$content = $result;
	echo "Request Execution Time: $execution_time\n";
	
	$fp = fopen("/var/www/html/receive/$fid", "wb");
	fwrite($fp, $content);
	fclose($fp);
	
	echo "$fid written to file system\r\n";
	
	// Log Request in Database
	if(empty($content)) {
		$status = 'error';
	} 
	else {
		$status = 'received';
	}
	$path = "/var/www/html/receive/$fid";
	$sql="Insert into `requested_files` 
	(`name`, `status`, `apiURL`, `data`, `endpoint`, `creationDate`,`modifiedDate`, `executionTime`, `path`) values 
	('$fid', '$status', '$apiURL', '$data', '$endpoint', '$requestDate', '$requestDate', '$execution_time', '$path')";
	
	$dblink->query($sql) or
		die("Something went wrong with $sql<br>".$dblink->error);
	
	return $fid;
}
?>