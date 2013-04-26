<?php
define ('SITE_ROOT',    realpath(dirname(__FILE__)));
define ('BASE_FOLDER', basename(dirname(__FILE__)));
define ('SITE_URL',    'http://'.$_SERVER['HTTP_HOST'].'/'.BASE_FOLDER);
$link=pg_connect("host =localhost port=5432 user=postgres password=123456 dbname=mediaMetadata");


function showMedia(){
   global $link;
   $sql="select * from metadata";
   $result=pg_query($link,$sql);
   while ($row = pg_fetch_array($result)) {
   $format = '<a href="showItem.php?id=%s">%s</a><br/>';
   echo sprintf($format, $row[0], $row[8]);
 }       
}

function showAllItems(){
   global $link;
   $sql="select * from metadata";
   $result=pg_query($link,$sql);
   while ($row = pg_fetch_array($result)) {
      $format = '<a href="s3.php?id=%s">%s</a><br/>';
      echo sprintf($format, $row[0], $row[8]);
   }
}

function showItemById($id,$details=true){
   showImage($id);
   global $link;
   $sql="select * from metadata where id='".$id."'";
   $result=pg_query($link,$sql);
   if(!pg_num_rows($result)){
      echo '<p>Please provide a valid ID.</p>';
   }
   else{
      $row=pg_fetch_array($result);
      $title=$row[8];
      echo "<p>$title</p>";
      if ($details){
         echo '<div style="float:left"><p>Metadata</P>';
         echo '<table border=1>';
      
         
         foreach($row as $c => $value){
            if(!is_numeric ($c)){
               echo '<tr>';
               echo '<td>'.$c.'</td><td>'.$value.'</td>';
               echo '</tr>';
            }
         }
      
         echo '</table></div>';
         
         
         $sql="select * from mediainfo where m_id='$id'";
         $result=pg_query($link,$sql);
         echo '<div style="float:left"><p>Media Infomation</P>';
         echo '<table border=1>';
         while ($row = pg_fetch_array($result)) {
            foreach($row as $c => $value){
               if(!is_numeric ($c)){
                  echo '<tr>';
                  echo '<td>'.$c.'</td><td>'.$value.'</td>';
                  echo '</tr>';
               }
            }
         }
         echo '</table></div>';
      }
   }
}
function showItemByProfile($v){
   global $link;
   $ifNeedAnd=false;
   $sql='select title,id from metadata where';
   $prompt='<br/>Output:<br/>';
   if(isset($v['title']) and $v['title']!=''){
      $title=$v['title'];
      $sql=$sql." title='$title'";
      $ifNeedAnd=true;
      $prompt=$prompt."Title:$title<br/>";
   }
   if(isValid($v['FY']) and isValid($v['FM']) and isValid($v['FD'])){
      $FY=$v['FY'];
      $FM=$v['FM'];
      $FD=$v['FD'];
      $from=getValidData($FY,$FM,$FD);
      if($from!==false){
         if($ifNeedAnd){
            $sql=$sql.' and ';
         }
         $sql=$sql." creationdate>='$from'";
         $ifNeedAnd=true;
      }
      $prompt=$prompt."From:$from<br/>";
   }
   if(isValid($v['TY']) and isValid($v['TM']) and isValid($v['TD'])){
      $TY=$v['TY'];
      $TM=$v['TM'];
      $TD=$v['TD'];
      $to=getValidData($TY,$TM,$TD);
      if($from!==false){
         if($ifNeedAnd){
            $sql=$sql.' and ';
         }
         $sql=$sql." creationdate<='$to'";
         $ifNeedAnd=true;
      }
      $prompt=$prompt."To:$to<br/>";
   }
   if(isset($v['format'])){
      $format=$v['format'];
      if($ifNeedAnd){
         $sql=$sql.' and ';
      }
      $sql=$sql." formate='$format'";
      $prompt=$prompt."Format:$format<br/>";
   }
   echo $prompt;
   echo $sql;
   $result=pg_query($link,$sql);
   if(!$result){
      echo 'Please check your inputs.';
   }
   else{
      if(pg_num_rows($result)===0){
         echo '<p>No matches.</p>';
      }else
      {
         echo "<p>$prompt</p>";
         while ($row = pg_fetch_row($result)) {
            echo "<p>$row[0]</p>";
            showImage($row[1]);
         }
      }
   }
}
#same creator
#same formater
#close creationdate
function showRelatedItems($id){
   global $link;
   echo '<p>Related Items</P>';
   $sql_format='select m2.id,m2.title '.
         ' from metadata as m1, metadata as m2 '.
         " where m1.id='%s'".
         ' and m2.formate=m1.formate'.
         ' and m1.creator=m2.creator'.
         ' and m1.id!=m2.id'.
         ' and abs(m1.creationdate-m2.creationdate)<7'.
         ' order by m2.title';
         
   $sql=sprintf($sql_format,$id);
   $result=pg_query($link,$sql);
   if(!$result){
      echo '<p>Error occurs. Wrong item id.</p>';
   }else{
      if(pg_num_rows($result)===0){
         echo '<p>This item is too special. No similiar ones.</p>';
      }else{
         echo '<p>Find ones created by the same creator and in the same week. And the same format.</p>';
         while ($row = pg_fetch_row($result)) {
            echo '<p>';
            echo $row[0].'<br/>'.$row[1];
            showImage($row[0]);
            echo '</p>';
         }
      }
   }
   
   #echo $sql;
   
   
}


function getValidData($y,$m,$d){
   if(isValid($y) and isValid($m) and isValid($d)){
      $date=sprintf('%d-%d-%d',$y,$m,$d);
      if(strtotime($date)===false){
         return false;
      }
      else{
         return $date;
      }
   }
}

function showImage($id){
   global $link;
   $sql="select title,mongo_id,formate from metadata where id='$id'";
   $result=pg_query($link,$sql);
   $row = pg_fetch_row($result);
   $title=$row[0];
   $mongo_id=$row[1];
   $formate=$row[2];
   $connection = new Mongo('localhost:27017');
   $images = $connection->media->getGridFS('images');
   $image = $images->findOne($title);
   $imagePath=SITE_ROOT."/resource/$title.$formate";
   $image->write($imagePath);
   $imageSrc=SITE_URL."/resource/$title.$formate";
   echo "<img src='$imageSrc' width='300' height='300'/>";
   
}

function showFTS($text){
   global $link;
   $newText=trim($text);
   $newText=preg_replace('!\s+!', ' ', $newText);
   $pieces=explode(" ",$newText);
   $newText="";
   foreach($pieces as $piece){
      if(strlen($newText)===0){
         $newText=$piece;
      }else{
         $newText=$newText." & ".$piece;
      }
   }
   $sql="SELECT id,title ".
         "FROM metadata ".
         "WHERE to_tsvector(description ||' '||".
         " title||' '||creator||".
         "' '||identifier||' '||formate||' '||type|| ".
         "' '||source||' '||language||' '||creationdate)".
         " @@ to_tsquery('english', '$newText')".
         " order by creationdate DESC";
   $result=pg_query($link,$sql);
   
   if(!$result){
      echo '<p>Error.</p>';
   }
   else{
      if(pg_num_rows($result)===0){
         echo '<p>No matches</p>';
      }else{
         while ($row = pg_fetch_row($result)) {
            echo '<p>';
            echo $row[0].'<br/>'.$row[1];
            showImage($row[0]);
            echo '</p>';
         }
      }
   }
}


function isValid($v){
   if(isset($v) and $v!=0)
      return true;
   return false;
}


