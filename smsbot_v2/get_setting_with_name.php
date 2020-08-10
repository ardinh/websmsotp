<?php

require_once('helper/database.php');
// require_once('helper/reply.php');
// print_r($input);
// die();
$api_key = $_GET["api_key"];
$name = $_GET["name_sett"];
$name = base64_decode($name);
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = "SELECT setting, whatsapp, sms FROM content_setting where api_key = '$api_key' and nama_setting = '$name'";
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

$ids['script'] = base64_decode($rows[0][0]);
$ids['wa'] = $rows[0][1];
$ids['sms'] = $rows[0][2];

echo json_encode($ids);

die();
finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}