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
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_ADD_SET) ||
    !array_key_exists(PAYLOAD_USER, $json) ||
    !array_key_exists(PAYLOAD_NS, $json) ||
    !array_key_exists(PAYLOAD_SCR, $json) ||
    !array_key_exists(PAYLOAD_WA, $json) ||
    !array_key_exists(PAYLOAD_SMS, $json)
) {
    $reply = replyErrorFormatJson();
    goto finish;
}
if (substr(base64_decode($json[PAYLOAD_SCR]), 0,5) != "<?php") {
    $json[PAYLOAD_SCR] = base64_encode('<?php unset($argv[0]); $content = implode(" ", $argv); '.base64_decode($json[PAYLOAD_SCR]).' ?>');
}
// echo base64_decode($json[PAYLOAD_SCR]);
// die();

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = 'INSERT INTO `content_setting` (`api_key`, `nama_setting`, `setting`, `whatsapp`,`sms`) VALUES (?, ?, ?, ?, ?)';
// $query = 'INSERT INTO `sms_in` (`msisdn`, `content`, `devid`,`kuota`) VALUES (?, ?, ?, ?)';
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->bind_param('sssss', $id, $name, $sett, $wa, $sms) === FALSE) {
    $reply = replyErrorDatabaseBind();
    goto finish;
}

$name = $json[PAYLOAD_NS];
$sett = $json[PAYLOAD_SCR];
$wa = $json[PAYLOAD_WA];
$sms = $json[PAYLOAD_SMS];
$id = $json[PAYLOAD_USER];
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