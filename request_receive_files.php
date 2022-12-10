<?php
/*
 This script requests all of the files that have not been requested.
 It starts by creating a session in the external server.
 Then, it requests the ID of the files that have not been requested.
 Afterwards, it gets the contents of those files and
 saves them in a temporary directory.
 In the end, it closes its current session in the external server.
*/

include("api_helper.php");
include("request_file.php");
include("credentials.php");

$data = "username=$username&password=$password";

// Create Session
echo "Job Date: " . date("Y-m-d_H:i:s");
$result = api_request($data, "create_session");
$cinfo = json_decode($result, true);

if($cinfo[0] == "Status: OK" && $cinfo[1] == "MSG: Session Created"){
	
	$sid = $cinfo[2];
	echo "SID: $sid\n";
	
	// Request Files
	$data = "sid=$sid&uid=$username";
	$result = api_request($data, "query_files");
	$cinfo = json_decode($result, true);
	
	if($cinfo[0] == "Status: OK"){
		
		if($cinfo[2] == "Action: None"){
			echo "\r\n No new file to import found\r\n";
		}
		else {
			$tmp = explode(":", $cinfo[1]);
			$files = explode(",", $tmp[1]);
			echo "\r\nFiles Found: " . count($files) . "\r\n";
			echo "Files:\r\n";
			foreach($files as $key => $value){
				$value = trim($value);
				echo "\r\nFile: $value\r\n";
				$tmp = explode("/", $value);
				$file = $tmp[4];
				request_file($sid, $username, $file);
			}
		}
	}
	// Close Session
	$data = "sid=$sid&uid=$username";
	$result = api_request($data, "close_session");
	$cinfo = json_decode($result, true);
	if($cinfo[0] == "Status: OK"){
		echo "Session successfully Closed\n";
	}
	else {
		echo $cinfo[0];
		echo "\r\n";
		echo $cinfo[1];
		echo "\r\n";
		echo $cinfo[2];
		echo "\r\n";
	}
}
echo "\n-----------END OF JOB-----------\n\n";

?>