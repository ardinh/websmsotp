<?php

define('DIRECT_ACCESS', true);

require_once('helper/payload.php');
require_once('helper/reply.php');
require_once('helper/endpoint.php');
require_once('helper/database.php');

$reply = replyErrorCommon();

$input = file_get_contents('php://input');
if (!isMethodPost()) {
    $reply = replyErrorMethod();
    goto finish;
}

$json = json_decode($input, true);
if ($json == null) {
    $reply = replyErrorFormat();
    goto finish;
}

if (
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_SMS_OUT) ||
    !array_key_exists(PAYLOAD_DEVICE_ID, $json)
) {
    $reply = replyErrorFormatJson();
    goto finish;
}


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$no = array('817','818','819','859','877','878','895','896','897','898','899','838','831','832','833','814','815','816','855','856','857','858');
$table = TABLE_SMS_QUEUE;
$status = STATUS_BEGIN;
// $query = "SELECT id, msisdn, content FROM `$table` ORDER BY id desc LIMIT 10";
$query = "SELECT id, msisdn, content FROM `$table` WHERE status=$status AND ISNULL(devid) ORDER BY id ASC LIMIT 10";
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->execute() === FALSE) {
    $reply = replyErrorDatabaseExecute();
    goto finish;
}

$result = $stmt->get_result();
if ($result === false) {
    $reply = replyErrorDatabaseResult();
    goto finish;
}

$ids = array();
$rows = $result->fetch_all(MYSQLI_NUM);
$devid = $json[PAYLOAD_DEVICE_ID];

if (count($rows) == 0) {
    $reply = replyOkSms(array());
    goto finish;
}

for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];

    if ($devid == 5 || $devid == 4 ) {
        if (in_array(substr($row[1], 2,3), $no)) {
            $ids[] = $row[0];
            // print_r($row);
            // die();
        }
    }else{
        if (!in_array(substr($row[1], 2,3), $no)) {
            $ids[] = $row[0];
            // print_r($row);
            // die();
        }
    }
}
// print_r($ids);
// die();

$table = TABLE_SMS_QUEUE;
$status = STATUS_FETCHED;
$ids2 = join(',', $ids);
$query = "UPDATE `$table` SET devid=$devid, status=$status WHERE id IN ($ids2)";
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->execute() === FALSE) {
    $reply = replyErrorDatabaseExecute();
    goto finish;
}

$reply = replyOkSms($rows);

finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}

$response = json_encode($reply);
die($response);