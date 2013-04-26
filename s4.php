<html>
<title>Full text search</title>
<body>
<h1>Full text search</h1>
<form name="FTS" action="s4.php" methond="get">
<input type="text" name="text"/>
<input type="submit" value="Search"/>
</form>
<?php
include_once('helper.php');
if(isset($_GET) and count($_GET)!=0){
   $text=$_GET['text'];
   showFTS($text);
}
?>
</body>
</html>