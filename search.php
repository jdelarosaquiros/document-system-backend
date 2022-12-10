<link href="assets/css/bootstrap.css" rel="stylesheet" />
<link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet" />
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.12.4.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/bootstrap-fileupload.js"></script>
<?php
/*
 Page to search files by name, owner, creation date, and document type.
 It also displays links to view the contents of the file.
*/

include("functions.php");
$dblink=db_connect("doc_system");
echo '<div id="page-inner">';
echo '<h1 class="page-head-line">Search Files on DB</h1>';
echo '<div class="panel-body">';
if (!isset($_POST['submit']))
{
	echo '<form action="" method="post">';
	echo '<div class="form-group">';
	echo '<label>Search String:</label>';
	echo '<input type="text" class="form-control" name="searchString">';
	echo '</div>';
	echo '<select name="searchType">';
	echo '<option value="name">Name</option>';
	echo '<option value="uploadBy">Created By</option>';
	echo '<option value="creationDate">Date</option>';
	echo '<option value="docType">Category</option>';
	echo '<option value="all">All</option>';
	echo '</select>';
	echo '<hr>';
	echo '<button type="submit" name="submit" value="submit">Search</button>';
	echo '</form>';
}
if (isset($_POST['submit']))
{
	$searchType=$_POST['searchType'];
	$searchString=addslashes($_POST['searchString']);
	switch($searchType)
	{
		case "name":
			$sql="Select `name`,`creationDate`,`autoID`,`docType` from `doc_storage` where `name` like '%$searchString%'";
			break;
		case "uploadBy":
			$sql="Select `name`,`creationDate`,`autoID`,`docType` from `doc_storage` where `ownerID` like '%$searchString%'";
			break;
		case "creationDate":
			$sql="Select `name`,`creationDate`,`autoID`,`docType` from `doc_storage` where `creationDate` like '%$searchString%'";
			break;
		case "docType":
			$sql="Select `name`,`creationDate`,`autoID`,`docType` from `doc_storage` where `docType` like '%$searchString%'";
			break;
		case "all":
			$sql="Select `name`,`creationDate`,`autoID`,`docType` from `doc_storage`";
			break;
		default:
			redirect("search.php?msg=searchTypeError");
			break;
	}
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql<br>".$dblink->error);
	echo '<table>';
	while ($data=$result->fetch_array(MYSQLI_ASSOC))
	{
		echo '<tr>';
		echo '<td style="padding-right: 10px">'.$data['name'].'</td>';
		echo '<td style="padding-right: 10px">'.$data['creationDate'].'</td>';
		echo '<td style="padding-right: 10px">'.$data['docType'].'</td>';
		echo '<td style="padding-right: 10px"><a href="view.php?fid='.$data['autoID'].'">View</a></td>';
		echo '</tr>';
	}
	echo '</table>';
}
echo '</div>';//end panel-body
echo '</div>';//end page-inner
?>