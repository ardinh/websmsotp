<?php

/**
 *  Request
 *  -------
 *  Url: http://dev.motion.co.id/smsbot/newdev.php
 *  Method: POST
 *  Body: {"c":2}
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
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_QUEUE_SMS)
) {
    $reply = replyErrorFormatJson();
    goto finish;
}

$mysqli = new mysqli('localhost', 'dev', 'devbemobile04', 'dev_smsbot');
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = 'INSERT INTO `queue_sms` (`msisdn`, `content`, `status`) VALUES (?, ?, ?)';
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->bind_param('isi', $msisdn, $smsContent, $status) === FALSE) {
    $reply = replyErrorDatabaseBind();
    goto finish;
}

$msisdn = $json[PAYLOAD_MSISDN];
$smsContent = $json[PAYLOAD_SMS_CONTENT];
$status = STATUS_BEGIN;
if ($stmt->execute() === FALSE) {
    $reply = replyErrorDatabaseExecute();
    goto finish;
}

$reply = replyOkCommon();

finish:
if ($stmt !== false) {
    $stmt->close();
}

if($mysqli !== false) {
    $mysqli->close();
}

$response = json_encode($reply);
die($response);