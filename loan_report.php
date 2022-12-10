<?php
/*
 Script for showing reports about the files saved in the database.
 Reports include: 
 	- Total users with documents in the system
	- Total and average size of all documents
	- Total documents owned by each user and whether their number of
	  documents is below or above average
*/

include("api_helper.php");
include("request_file.php");
include_once("functions.php");
$dblinkLog=db_connect("doc_system");

$sql="Select `autoID`, `name`,`ownerID`, `docType`, `size` from `doc_storage` WHERE `creationDate` >= '2022-11-13 20:08:30'";
	
$result = $dblinkLog->query($sql) or
	die("Something went wrong with $sql<br>".$dblinkLog->error);

$documents = array();
$loanNumbers = array();
$totalSize = 0;


while($data=$result->fetch_array(MYSQLI_ASSOC)){
	$documents[$data['name']] = $data['ownerID'];
	
	if(!array_key_exists($data['ownerID'], $loanNumbers)){
		$loanNumbers[$data['ownerID']] = 1;
	} 
	else {
		$loanNumbers[$data['ownerID']] += 1;
	}
	
	$totalSize += $data['size'];
}

$totalDocuments = count($documents);
$totalLoanNumbers = count($loanNumbers);
$averageSize = $totalSize / $totalDocuments;
$averageDocumentNumber = $totalDocuments / $totalLoanNumbers;

echo "Total Files: " . count($documents) . "<br>";
echo "Total Unique Loans: $totalLoanNumbers<br>";
echo "Total Size of All Documents: $totalSize<br>";
echo "Average Document Size: $averageSize<br>";

echo "<br>Loan Numbers:<br><br>";

foreach($loanNumbers as $loanNumber => $numDocuments){
	echo "$loanNumber:<br>";
	echo "  Average Document Number: $averageDocumentNumber<br>";
	echo "  Total Documents: $numDocuments - ";
	if($numDocuments > $averageDocumentNumber){
		echo "Above Average<br><br>";
	} 
	else{
		echo "Bellow Average<br><br>";
	}
}
echo "<br><br>";
?>