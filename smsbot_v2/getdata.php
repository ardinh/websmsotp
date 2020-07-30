<?php

require_once('helper/database.php');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$msisdn = $_GET['msisdn'];

$status = STATUS_BEGIN;
$query = "SELECT content FROM sms_queue WHERE msisdn = '".$msisdn."' and DATE_ADD(insert_time,INTERVAL 2 MINUTE) > now() ORDER BY id desc";

$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    goto finish;
}

if ($stmt->execute() === FALSE) {
    goto finish;
}

$result = $stmt->get_result();
if ($result === false) {
    goto finish;
}

$ids = array();
$rows = $result->fetch_all(MYSQLI_NUM);

print_r(json_encode($rows));
die();
finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
} 