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
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_SMS_KUOTA_OUT) ||
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

$query = "SELECT msisdn FROM sms_dvc WHERE devid = 10001";
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

$ids1 = array();
$rows = $result->fetch_all(MYSQLI_NUM);

if (count($rows) == 0) {
    $reply = replyOkSms(array());
    goto finish;
}

for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    $ids1[] = $row[0];
}

$table = TABLE_SMS_KUOTA;
$devid = $json[PAYLOAD_DEVICE_ID];
$status = STATUS_BEGIN;
$query = "SELECT id FROM `$table` WHERE status=$status AND devid = $devid  ORDER BY id ASC LIMIT 10";
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

if (count($rows) == 0) {
    $reply = replyOkSms(array());
    array_push($reply, $ids1[0]);
    goto finish;
}

for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];

    $ids[] = $row[0];
}

$table = TABLE_SMS_KUOTA;
$status = STATUS_FETCHED;
$ids2 = join(',', $ids);
$query = "UPDATE `$table` SET status=$status WHERE id IN ($ids2)";
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
array_push($reply, $ids1[0]);

finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}

$response = json_encode($reply);
die($response);