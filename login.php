<?php 
$servername="localhost";
$database="WMS";
$username="root";
$password="";

$conn=new mysqli($servername,$database,$database,$password);
 
require 'filename';

if($conn->connect_error){
die("connection falied:".$conn->connect_error);
}
echo "Connected successfully";




   if( $_SERVER["REQUEST_METHOD"]=="POST");
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM login WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        echo "Login successful!";
        // Redirect to a different page or perform other actions
    } else {
        echo "Invalid email or password.";
    }

?>
