<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "projeto";

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
  die("Erro ao conectar ao banco: " . mysqli_connect_error());
}
?>