<?php
$servername = "localhost";
$username = "root";
$password = "";

$database = "kauppatietokanta";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = mysqli_connect($servername, $username, $password, $database);

if($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}