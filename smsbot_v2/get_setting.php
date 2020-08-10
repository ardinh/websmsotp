<?php

require_once('helper/database.php');
// require_once('helper/reply.php');
// print_r($input);
// die();
$api_key = $_GET["api_key"];

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = "SELECT nama_setting FROM content_setting where api_key = '$api_key'";
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
$idss = array();
for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    $ids['name'] = $row[0];
    $idss['setting'][] = $ids;
}

// print_r($rows);
echo json_encode($idss);

die();
finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}