 <?php
$access=true;
error_reporting(E_ALL);
?>
<html>
<title>A simple media management system</title>
<body>
<h1>Media Management</h1>
<p>Show items by title</p>
<p>
<?php
include_once('helper.php');
showMedia();

?>
</p>
<div>
<a href="s1.php">Search by keyword</a><br/>
<a href="s2.php">Search by profile</a><br/>
<a href="s3.php">Search by example</a><br/>
<a href="s4.php">Full text search</a><br/>

</div>
</body>
</html>
