<?php
/*
 Function for connecting to the database, and
 another one for redirecting to a URL.
*/

function db_connect($db)
{
	$hostname="localhost";
    $username="webuser";
    $password="6fiB2wr6r@RfFNHr";
    //$db="docStorage";
    $dblink=new mysqli($hostname,$username,$password,$db);
    if (mysqli_connect_errno())
    {
        die("Error connecting to database: ".mysqli_connect_error());   
    }
	return $dblink;
}

function redirect ( $uri )
{ ?>
	<script type="text/javascript">
	<!--
	document.location.href="<?php echo $uri; ?>";
	-->
	</script>
<?php die;
}
?>