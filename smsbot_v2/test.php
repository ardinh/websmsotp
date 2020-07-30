<?php

define('DIRECT_ACCESS', true);

require_once('helper/payload.php');
require_once('helper/reply.php');
require_once('helper/endpoint.php');
require_once('helper/database.php');

$reply = replyErrorCommon();

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = "SELECT session FROM `session_zenziva`";


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
    goto finish;
}


for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
}
print_r($rows);
die();
finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}

$response = json_encode($reply);
die($response);
