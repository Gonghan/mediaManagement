<html>
<title>Search by example</title>
<body>
<h1>Search by example</h1>
<h2>Find the items with similiar tags</h2>
<?php
include_once('helper.php');

if(isset($_GET) and count($_GET)!=0){
   $id=$_GET['id'];
   showItemById($id);
   showRelatedItems($id);
}
else{
   showAllItems();
}

?>
</body>
</html>