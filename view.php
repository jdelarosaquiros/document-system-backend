<link href="assets/css/bootstrap.css" rel="stylesheet" />
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.12.4.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<?php
/*
 Shows link to view the file. 
 Depending on the browser, the file may be downloaded or opened in the browse, 
 especially if the file is a PDF because most browsers can display them. 
*/

include("functions.php");
$dblink=db_connect("doc_system");
$autoid=$_REQUEST['fid'];
echo '<div id="page-inner">';
echo '<h1 class="page-head-line">View Files on DB</h1>';
echo '<div class="panel-body">';
$sql="Select `name`, `creationDate`,`path` from `doc_storage` where `autoID`='$autoid'";
$result=$dblink->query($sql) or
	die("Something went wrong with $sql<br>".$dblink->error);
$data=$result->fetch_array(MYSQLI_ASSOC);

// TODO: Maybe implement temporary files by coping them into a temp directory
if ($data['path']!=NULL)
	$creationDate = str_replace(" ", "_", $data['creationDate']);
	echo '<p>File: <a href="uploads/'.$creationDate.$data['name'].'" target="_blank">'.$data['creationDate'].$data['name'].'</a></p>';

echo '</div>';//end panel-body
echo '</div>';//end page-inner
?>