<!DOCTYPE html>
<link href="assets/css/bootstrap.css" rel="stylesheet" />
<link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet" />
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.12.4.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/bootstrap-fileupload.js"></script>
<?php
/*
 Displays a page that allows the user to upload a file from their local computer. 
 When the user submits the file, it stores the file in a directory in the server
 and saves its metadata in the database.
*/

include("functions.php");
echo '<div id="page-inner">';
if (isset($_REQUEST['msg']) && ($_REQUEST['msg']=="success"))
{
	echo '<div class="alert alert-success alert-dismissable">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
	echo 'Document successfully uploaded!</div>';
}
echo '<h1 class="page-head-line">Upload a New File to DocStorage</h1>';
echo '<div class="panel-body">';
echo '<form method="post" enctype="multipart/form-data" action="">';
echo '<input type="hidden" name="uploadedby" value="user@test.mail">';
echo '<input type="hidden" name="MAX_FILE_SIZE" value="100000000">';
echo '<div class="form-group">';
echo '<label class="control-label col-lg-4">File Upload</label>';
echo '<div class="">';
echo '<div class="fileupload fileupload-new" data-provides="fileupload">';
echo '<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"></div>';
echo '<div class="row">';//buttons
echo '<div class="col-md-2"><span class="btn btn-file btn-primary"><span class="fileupload-new">Select File</span><span class="fileupload-exists">Change</span>';
echo '<input name="userfile" type="file"></span></div>';
echo '<div class="col-md-2"><a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a></div>';
echo '</div>';//end buttons
echo '</div>';//end fileupload fileupload-new
echo '</div>';//end ""
echo '</div>';//end form-group
echo '<hr>';
echo '<button type="submit" name="submit" value="submit" class="btn btn-lg btn-block btn-success">Upload File</button>';
echo '</form>';
echo '</div>';//end panel-body
echo '</div>';//end page-inner
if (isset($_POST['submit']))
{
	$dblink=db_connect("doc_system");
	$uploadDate=date("Y-m-d_H:i:s");
	$uploadBy="user@test.mail";
	$name=str_replace(" ", "_", $_FILES['userfile']['name']);
	$fileName=$uploadDate.$name;
	$docType="Test Doc";
	$status='active';
	$octalAccessPermissions=7;
	$hierarchyLevel=0;
	$isDownloadable=1;
	$tmpName=$_FILES['userfile']['tmp_name']; //Will use in future
	$fileSize=$_FILES['userfile']['size'];
	$fileType=$_FILES['userfile']['type'];
    $path="/var/www/html/uploads/";
	//console.log(gettype($uploadDate));
	$fp=fopen($tmpName, 'r');
	$content=fread($fp, filesize($tmpName));
	fclose($fp);
	$sql="Insert into `doc_storage` 
	(`name`,`path`, `size`, `fileType`, `docType`, `status`, `octalAccessPermissions`, `hierarchyLevel`, `ownerID`, `lastAccessBy`, `lastModifiedBy`, `creationDate`, `lastModifiedDate`,`lastAccessedDate`, `isDownloadable`) values ('$name','$path','$fileSize', '$fileType', '$docType', '$status', '$octalAccessPermissions', '$hierarchyLevel', '$uploadBy', '$uploadBy', '$uploadBy', '$uploadDate', '$uploadDate', '$uploadDate', '$isDownloadable')";

	$dblink->query($sql) or
		die("Something went wrong with $sql<br>".$dblink->error);
	$fp=fopen($path.$fileName,"wb") or
		die("Could not open $path$fileName for writing");
	fwrite($fp,$content);
	fclose($fp);
	header("Location: https://192.168.56.101/upload.php?msg=success");
}
?>