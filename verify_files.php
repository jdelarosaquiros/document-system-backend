<?php
/*
 This script is for checking that there is no missing file.
 It first gets a history of the requested files from the external server.
 Then, it checks if those files are in the datbase of the document system.
*/

include("api_helper.php");
include("request_file.php");
include_once("functions.php");
$dblinkLog=db_connect("doc_system");

$sql="Select `autoID`, `name`,`ownerID`, `docType` from `doc_storage`";
	
$result = $dblinkLog->query($sql) or
	die("Something went wrong with $sql<br>".$dblinkLog->error);

$files = array();

while($data=$result->fetch_array(MYSQLI_ASSOC)){
	$files[$data['name']] = $data['ownerID'];
}

echo "Total Files: " . count($files) . "\r\n";

$username = "mjy509";
$password = "X@cqYJkr2dtjvGh";

$data = "username=$username&password=$password";

// Create Session
echo "Job Date: " . date("Y-m-d_H:i:s");
$result = api_request($data, "create_session");
$cinfo = json_decode($result, true);

$filesMissing = array();

if($cinfo[0] == "Status: OK" && $cinfo[1] == "MSG: Session Created"){
	
	$sid = $cinfo[2];
	echo "SID: $sid\n";
	
	// Request Files
	$data = "sid=$sid&uid=$username";
	$result = api_request($data, "request_loans");
	$cinfo = json_decode($result, true);
	
	if($cinfo[0] == "Status: OK"){
		
		$tmp = explode(":", $cinfo[1]);
		$loanNumbers = explode(",", $tmp[1]);
		echo "\r\nTotal Loans: " . count($loanNumbers) . "\r\n";
		echo "Loans:\r\n";
		foreach($loanNumbers as $key => $loanNumber){
			
			$loanNumber = trim($loanNumber);
			$loanNumber = preg_replace('/"|\[|\]/', '', $loanNumber);
			echo "\r\nLoan: $loanNumber\r\n";
			$data = "sid=$sid&uid=$username&lid=$loanNumber";
			$result = api_request($data, "request_file_by_loan");
			$cinfo = json_decode($result, true);

			if($cinfo[0] == "Status: OK"){


				$tmp = explode(":", $cinfo[1]);
				$loanFiles = explode(",", $tmp[1]);
				echo "\r\nFiles Found: " . count($loanFiles) . "\r\n";
				echo "Files:\r\n";
				foreach($loanFiles as $key => $file){
					$file = trim($file);
					$file = preg_replace('/"|\[|\]/', '', $file);
					echo "\r\nFile: $$file\r\n";

					if(!array_key_exists($file, $files)){
						echo "Does not exist!!";
						array_push($filesMissing, $file);
					}
				}
			}
		}
	}
	echo "\r\nTotal Missing Files: " . count($filesMissing) . "\r\n";
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