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
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_ADD_OP) ||
    !array_key_exists(PAYLOAD_DEVICE_ID, $json)
) {
    $reply = replyErrorFormatJson();
    goto finish;
}
// echo base64_decode($json[PAYLOAD_SCR]);
// die();
$operator = $json['op'];
$devid = $json[PAYLOAD_DEVICE_ID];

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = "UPDATE sms_dvc_3 SET operator='$operator' WHERE devid = $devid";
// $query = 'INSERT INTO `sms_in` (`msisdn`, `content`, `devid`,`kuota`) VALUES (?, ?, ?, ?)';
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->execute() === FALSE) {
    $reply = replyErrorDatabaseExecute();
    goto finish;
}

$reply = replyOkCommon();

finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}

$response = json_encode($reply);
die($response);