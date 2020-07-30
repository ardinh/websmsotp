<?php

require_once('helper/database.php');
// require_once('helper/reply.php');
// print_r($input);
// die();

$input = file_get_contents('php://input');
$arr = explode("&", $input);
$message = explode("=", $arr[0])[1];
$app_name = explode("=", $arr[1])[1];
$sender = explode("=", $arr[2])[1];
$sender = str_replace("-", "", $sender);
$sender = str_replace("+", "", $sender);
$msisdn = get_string_between($sender.';',"B",";");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = "SELECT setting FROM content_setting where api_key = 1 and whatsapp = 1";
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

for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    $ids[] = base64_decode($row[0]);
}

$status = STATUS_BEGIN;
// $query = "SELECT content FROM sms_queue WHERE msisdn = '".$msisdn."' and DATE_ADD(insert_time,INTERVAL 2 MINUTE) > now() ORDER BY id desc";
$query = "SELECT content FROM sms_queue ORDER BY id desc limit 1";
// echo $query;
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

$rows = $result->fetch_all(MYSQLI_NUM);
$data = $rows;

$script = "";
$h = "";
if($data[0][0] == null){
	$reply = array('reply' => "Pastikan nomor telah di input ke app Mydrakor, dan juga nomor Whatsapp sama dengan nomor yang di inputkan");	
}else{
    $h = $data[0][0];
    for ($j=0; $j < count($ids); $j++) { 
        $e = dirname(__FILE__);
        $script = $ids[$j];
        $name_file = "setting_".$j.".php";
        file_put_contents($name_file, $script);
        $h = exec("php ".$e."\/".$name_file." ".$h);
    }
	$reply = array('reply' => $h);
}
echo json_encode($reply);
/*for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    $ids[] = $row[0];
}

// print_r($ids);*/
die();
finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
} 


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}