<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "sistem_parkir";

$con = mysqli_connect($servername, $username, $password, $database);

if (!$con) {
    die("koeksi gagal: " . mysqli_connect_error());
}
