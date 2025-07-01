<?php 
$servername="localhost";
$database="";
$username="root";
$password="";

$conn=new mysqli($servername,$database,$password);

if($conn->connect_error){
die("connection falied:")

