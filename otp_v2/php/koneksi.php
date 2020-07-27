<?php
$curdir = dirname(__FILE__);
$robodir = dirname($curdir);
require_once($robodir . "/php/const.php");
$koneksi = mysqli_connect("localhost",DB_CRAWLER_USERNAME,DB_CRAWLER_PASS,DB_CRAWLER);

// Check connection
if (mysqli_connect_errno()){
	die("Koneksi database gagal : " . mysqli_connect_error());
}

