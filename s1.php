<html>
<title>Search by keyword</title>
<body>
<h1>Search by keyword</h1>
<form name="s1" action="s1.php" method="get">
ID: <input type="text" name="ID"><br/>
<input type="submit" value="Submit">
</form>
<?php
include_once('helper.php');
if (isset($_GET['ID'])){
   $ID=$_GET['ID'];
   if($ID)
      showItemById($ID);
}
?>
</body>
</html>