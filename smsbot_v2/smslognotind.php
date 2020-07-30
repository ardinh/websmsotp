<?php

/**
 *  Request
 *  -------
 *  Url: http://dev.motion.co.id/smsbot/queue.php
 *  Method: POST
 *  Body: {"c":1,"i":6282228886446,"s":"isi sms"}
 *
 *  Response
 *  --------
 *  In the format of {"c":x,...}
 *  Parameter "c" value:
 *   1       => OK
 *  -1       => Unknown error
 *  -1000001 => Method not POST
 *  -1000002 => Wrong format it should be in the format of {"c":1,"i":xxx,"s":"xxx"}
 *  -1000003 => Cannot decode body into json
 *  -2000001 => Cannot connect to database server
 *  -2000002 => Cannot prepare database query
 *  -2000003 => Cannot bind parameter in the query
 *  -2000004 => Cannot execute database query
 */

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
print_r($json);
if ($json == null) {
    $reply = replyErrorFormat();
    goto finish;
}

if (
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_SMS_LOG) ||
    !array_key_exists(PAYLOAD_MSISDN, $json) ||
    !array_key_exists(PAYLOAD_ID_SERVER, $json) ||
    !array_key_exists(PAYLOAD_ERROR, $json) ||
    !array_key_exists(PAYLOAD_PRICE, $json) ||
    !array_key_exists(PAYLOAD_BALANCE, $json)
) {
    $reply = replyErrorFormatJson();
    goto finish;
}


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = 'INSERT INTO `sms_log_not_ind` (`message_id`, `to`, `status`,`remaining_balance`,`price`) VALUES (?, ?, ?, ?, ?)';
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->bind_param('ssiss', $mid, $to, $stat, $balance, $price) === FALSE) {
    $reply = replyErrorDatabaseBind();
    goto finish;
}

$mid = $json[PAYLOAD_ID_SERVER];
$to = $json[PAYLOAD_MSISDN];
$stat = $json[PAYLOAD_ERROR];
$balance = $json[PAYLOAD_BALANCE];
$price = $json[PAYLOAD_PRICE];
// die($json[PAYLOAD_ERROR]);
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