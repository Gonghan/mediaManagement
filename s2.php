<html>
<title>Search by profile</title>
<body>
<h1>Search by a few tags</h1>
<form name="s2" action="s2.php" method="get">
Title:
<input type="text" name="title"><br/>
<br/>
Create Date:<br/>
From<br/>
Y<input type="text" name="FY" size="4">
M<input type="text" name="FM" size="2">
D<input type="text" name="FD" size="2">
<br/>
To<br/>
Y<input type="text" name="TY" size="4">
M<input type="text" name="TM" size="2">
D<input type="text" name="TD" size="2">
<br/>
<br/>
Format: 
<input type="radio" name="format" value="jpg">jpg
<input type="radio" name="format" value="png">png<br/>

<input type="submit" value="Submit">
</form><br/>
<?php
include_once('helper.php');
if(isset($_GET) and count($_GET)>0){
   showItemByProfile($_GET);
   
}
?>

</body>
</html>