<?php
/*
 This script processes the files requested from the external server and
 saves them in the document system. 
 
 Note: The files being processed are already requested from the external server and
 are in a temporary directory of the internal server.
*/

include("functions.php");
$dblinkLog=db_connect("log_system");

$sql="Select `autoID`, `name`,`path`, `creationDate` from `requested_files` where `status` like 'received'";
	
$result=$dblinkLog->query($sql) or
	die("Something went wrong with $sql<br>".$dblinkLog->error);

$dblinkDoc=db_connect("doc_system");

echo "Job Date: " . date("Y-m-d_H:i:s") . "\n";

while($data=$result->fetch_array(MYSQLI_ASSOC)){
	
	// Process file name
	$autoID = $data['autoID'];
	$name = $data['name'];
	$path = $data['path'];
	$creationDate = $data['creationDate'];
	
	$tmp = explode(".", $name);
	$fileType = $tmp[1];
	echo "$autoID\n";
	echo "$name\n";
	
	$tmp = explode("-", $name);
	$ownerID = $tmp[0];
	$docType = $tmp[1];
	
	$tmp = explode("_", $tmp[2]);
	$year = substr($tmp[0], 0, 4);
	$month = substr($tmp[0], 4, 2);
	$day = substr($tmp[0], 6, 2);
	$hours = $tmp[1];
	$minutes = $tmp[2];
	$seconds = $tmp[3];
	
	echo "$ownerID\n";
	echo "$docType\n";
	echo "$creationDate\n";
	echo "Y: $year M: $month D: $day H: $hours M: $minutes S: $seconds\n";
	
	
	//TODO: Make a function for adding the file to the doc system
	// Prepare variables
	$name = $name;
	$uploadDate = $creationDate;
	$lastModifiedDate = date("Y-m-d_H:i:s");
	$uploadBy = $ownerID;
	$fileName = $uploadDate.$name;
	$docType = $docType;
	$status = 'active';
	$octalAccessPermissions = 7;
	$hierarchyLevel = 0;
	$isDownloadable = 1;
	$fileSize = filesize($path);
	$fileType = $fileType;

	$fp = fopen($path, 'r');
	$content = fread($fp, filesize($path));
	fclose($fp);
	
	echo "Size: $fileSize\nType: $fileType\n";
    $path="/var/www/html/uploads/";
	
	// Add file to document system
	$sql="Insert into `doc_storage` 
	(`name`,`path`, `size`, `fileType`, `docType`, `status`, `octalAccessPermissions`, `hierarchyLevel`, `ownerID`, `lastAccessBy`, `lastModifiedBy`, `creationDate`, `lastModifiedDate`,`lastAccessedDate`, `isDownloadable`) values ('$name','$path','$fileSize', '$fileType', '$docType', '$status', '$octalAccessPermissions', '$hierarchyLevel', '$uploadBy', '$uploadBy', '$uploadBy', '$uploadDate', '$uploadDate', '$uploadDate', '$isDownloadable')";

	$dblinkDoc->query($sql) or
		die("Something went wrong with $sql<br>".$dblinkDoc->error);
	
	$status = 'processed';
	$sql="Update `requested_files` SET `status`='$status' where `autoID` = '$autoID'";
	$dblinkLog->query($sql) or
			die("Something went wrong with $sql<br>".$dblinkLog->error);
	
	$fp=fopen($path.$fileName,"wb") or
		die("Could not open $path$fileName for writing");
	fwrite($fp, $content);
	fclose($fp);
	echo "\n";
}

echo "\n-----------END OF JOB-----------\n\n";
?>