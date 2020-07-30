<?php

require_once('helper/database.php');
// print_r($input);
// die();

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, "dev_bot_autoreply");
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}
// die();

$query = "SELECT * FROM telegram";
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