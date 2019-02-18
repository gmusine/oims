<?php 
$conn = mysqli_connect("localhost", "root", "root@75", "dev");
if(! $conn){
  die("Connection to the database failed: " . mysqli_connect_error()); 
}
?>