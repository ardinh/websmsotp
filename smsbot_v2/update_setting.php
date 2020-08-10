<?php

define('DIRECT_ACCESS', true);

require_once('helper/payload.php');
require_once('helper/reply.php');
require_once('helper/endpoint.php');
require_once('helper/database.php');

$reply = replyErrorCommon();

$api_key = $_GET['api_key'];
$nama = $_GET['nama'];
$script = $_GET['script'];
$wa = $_GET['wa'];
$sms = $_GET['sms'];
$var = "";
if($script !== ""){
	$var = "setting='$script',";
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = "UPDATE content_setting SET $var whatsapp=$wa, sms=$sms WHERE api_key = '$api_key' and nama_setting = '$nama'";
echo $query;
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->execute() === FALSE) {
    $reply = replyErrorDatabaseExecute();
    goto finish;
}

$reply = replyOkSms($stmt);

finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}

$response = json_encode($reply);
die($response);