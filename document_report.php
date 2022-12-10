<?php
/*
 Script for showing reports about the files saved in the database.
 Reports include: 
	- List of users who have all types of documents 
	  (aka. credit, closing, title, financial, personal, internal, legal, other)
	- List of users who are missing at least one type of document along with
	  which types they are missing.
*/

include("api_helper.php");
include("request_file.php");
include_once("functions.php");
$dblinkLog=db_connect("doc_system");

$sql="Select `autoID`, `name`,`ownerID`, `docType`, `size` from `doc_storage` WHERE `creationDate` >= '2022-11-13 20:08:30'";
	
$result = $dblinkLog->query($sql) or
	die("Something went wrong with $sql<br>".$dblinkLog->error);

$loanNumbers = array();
$documentTypes = array();
$missingLoans = array();
$completeLoans = array();

while($data=$result->fetch_array(MYSQLI_ASSOC)){
	
	if(!array_key_exists($data['docType'], $documentTypes)){
		$documentTypes[$data['docType']] = 1;
	} 
	else {
		$documentTypes[$data['docType']] += 1;
	}
	
	if(!array_key_exists($data['ownerID'], $loanNumbers)){
		$loanNumbers[$data['ownerID']] = array($data['docType'] => 1);
	} 
	else {
		$loanNumbers[$data['ownerID']][$data['docType']] = 1;
	}
}



foreach($loanNumbers as $loanNumber => $docTypeList){
	$isComplete = true;
	foreach($documentTypes as $docType => $value){
		if(!array_key_exists($docType, $docTypeList)){
			$isComplete = false;
			if(!array_key_exists($loanNumber, $missingLoans)){
				$missingLoans[$loanNumber] = array($docType => 1);
			} 
			else {
				$missingLoans[$loanNumber][$docType] = 1;
			}
		} 
	}
	
	if($isComplete){
		array_push($completeLoans, $loanNumber);
	}
}

echo "Loans with Missing Documents:<br><br>";

foreach($missingLoans as $loanNumber => $docTypeList){
	echo "$loanNumber:";
	foreach($docTypeList as $docType => $value){
		echo " - $docType";
	}
	echo "<br>";
}

echo "<br>Complete Loans:<br><br>";

foreach($completeLoans as $loanNumber){
	echo "$loanNumber<br>";
}

echo "<br>Total Documents by Type:<br><br>";

foreach($documentTypes as $docType => $totalDocs){
	echo "$docType: $totalDocs<br>";
}

echo "<br><br>";
?>