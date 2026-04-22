<?php 
session_start();
if($_SERVER['REQUEST_METHOD']=='POST'){
   $title='index.txt';
   $title=str_replace(' ',"",$title);
   $content=$_POST['content'];
   $dir="files";
   if(!is_dir($dir)){
    mkdir($dir,0777,true);
   }
   $file_name=$dir."/".$title;
   $file=fopen($file_name,"w");
   if($file){
    fclose($file);
   }
   if(file_put_contents($file_name,$content));
  //echo "file created success";
  $_SESSION['notify'] = 'File Upload Successfull';
  header('Location: index.php');
  exit;
}



?>