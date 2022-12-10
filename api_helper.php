<?php
/*
	Function for making requests to the external server and 
	logging the results in the database.
*/

include_once("functions.php");

function api_request($data, $endpoint){
	
	if($endpoint == "request_file"){
		return null;
	}
	
	$dblink=db_connect("log_system");
	
	$apiURL="https://cs4743.professorvaladez.com/api/";
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
	echo "\r\nResult: $result\r\n";
	echo "Execution Time: $execution_time\r\n";

	// Log Request in Database

	$sql="Insert into `request_history` 
	(`apiURL`, `data`, `endpoint`, `requestDate`, `executionTime`, `result`) values 
	('$apiURL', '$data', '$endpoint', '$requestDate', '$execution_time', '$result')";
	
	$dblink->query($sql) or
		die("Something went wrong with $sql<br>".$dblink->error);
	
	return $result;
}
?>